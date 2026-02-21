<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Edge;
use App\Models\Node;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EdgeController extends Controller
{
    protected function getCampaign(string $campaignSlug): Campaign
    {
        return Campaign::where('user_id', Auth::id())
            ->where('slug', $campaignSlug)
            ->firstOrFail();
    }

    /**
     * Get all available relationship types
     */
    public function types(): JsonResponse
    {
        return response()->json([
            'types' => $this->getRelationshipTypes(),
        ]);
    }

    /**
     * Get relationships for a specific node
     */
    public function index(Request $request, string $campaignSlug, string $nodeId): JsonResponse
    {
        $campaign = $this->getCampaign($campaignSlug);

        $node = $campaign->nodes()->where('id', $nodeId)->firstOrFail();

        $outgoing = $node->outgoingEdges()
            ->with('targetNode:id,type,subtype,name,slug')
            ->get();

        $incoming = $node->incomingEdges()
            ->with('sourceNode:id,type,subtype,name,slug')
            ->get();

        return response()->json([
            'outgoing' => $outgoing,
            'incoming' => $incoming,
            'node' => [
                'id' => $node->id,
                'name' => $node->name,
                'type' => $node->type,
            ],
        ]);
    }

    /**
     * Get available target nodes for relationships
     */
    public function availableTargets(Request $request, string $campaignSlug, string $nodeId): JsonResponse
    {
        $campaign = $this->getCampaign($campaignSlug);

        // Exclude the source node itself
        $nodes = $campaign->nodes()
            ->where('id', '!=', $nodeId)
            ->orderBy('type')
            ->orderBy('name')
            ->get(['id', 'type', 'subtype', 'name', 'slug']);

        // Group by type for easier selection
        $grouped = $nodes->groupBy('type')->map(function ($items, $type) {
            return [
                'type' => $type,
                'label' => ucfirst($type) . 's',
                'nodes' => $items->values(),
            ];
        })->values();

        return response()->json([
            'groups' => $grouped,
            'all' => $nodes,
        ]);
    }

    /**
     * Create a new relationship
     */
    public function store(Request $request, string $campaignSlug): JsonResponse
    {
        $campaign = $this->getCampaign($campaignSlug);

        $validated = $request->validate([
            'source_node_id' => 'required|uuid|exists:nodes,id',
            'target_node_id' => 'required|uuid|exists:nodes,id|different:source_node_id',
            'type' => 'required|string|max:50',
            'label' => 'nullable|string|max:100',
            'strength' => 'nullable|integer|min:1|max:10',
            'is_secret' => 'boolean',
            'bidirectional' => 'boolean',
        ]);

        // Verify both nodes belong to this campaign
        $sourceNode = $campaign->nodes()->where('id', $validated['source_node_id'])->firstOrFail();
        $targetNode = $campaign->nodes()->where('id', $validated['target_node_id'])->firstOrFail();

        // Check for duplicate edge
        $existing = Edge::where('campaign_id', $campaign->id)
            ->where('source_node_id', $validated['source_node_id'])
            ->where('target_node_id', $validated['target_node_id'])
            ->where('type', $validated['type'])
            ->first();

        if ($existing) {
            return response()->json([
                'error' => 'This relationship already exists.',
            ], 422);
        }

        // Create the edge
        $edge = $campaign->edges()->create([
            'source_node_id' => $validated['source_node_id'],
            'target_node_id' => $validated['target_node_id'],
            'type' => $validated['type'],
            'label' => $validated['label'] ?? $this->getLabelForType($validated['type']),
            'strength' => $validated['strength'] ?? null,
            'is_secret' => $validated['is_secret'] ?? false,
        ]);

        // Load the relationships for response
        $edge->load(['sourceNode:id,type,name,slug', 'targetNode:id,type,name,slug']);

        // Create bidirectional edge if requested
        $reverseEdge = null;
        if ($request->boolean('bidirectional')) {
            $reverseType = $this->getReverseType($validated['type']);

            // Check if reverse doesn't already exist
            $existingReverse = Edge::where('campaign_id', $campaign->id)
                ->where('source_node_id', $validated['target_node_id'])
                ->where('target_node_id', $validated['source_node_id'])
                ->where('type', $reverseType)
                ->first();

            if (!$existingReverse) {
                $reverseEdge = $campaign->edges()->create([
                    'source_node_id' => $validated['target_node_id'],
                    'target_node_id' => $validated['source_node_id'],
                    'type' => $reverseType,
                    'label' => $this->getLabelForType($reverseType),
                    'strength' => $validated['strength'] ?? null,
                    'is_secret' => $validated['is_secret'] ?? false,
                ]);
                $reverseEdge->load(['sourceNode:id,type,name,slug', 'targetNode:id,type,name,slug']);
            }
        }

        return response()->json([
            'edge' => $edge,
            'reverse_edge' => $reverseEdge,
            'message' => 'Relationship created successfully.',
        ], 201);
    }

    /**
     * Update an existing relationship
     */
    public function update(Request $request, string $campaignSlug, int $edgeId): JsonResponse
    {
        $campaign = $this->getCampaign($campaignSlug);

        $edge = $campaign->edges()->where('id', $edgeId)->firstOrFail();

        $validated = $request->validate([
            'type' => 'sometimes|required|string|max:50',
            'label' => 'nullable|string|max:100',
            'strength' => 'nullable|integer|min:1|max:10',
            'is_secret' => 'boolean',
        ]);

        $edge->update($validated);
        $edge->load(['sourceNode:id,type,name,slug', 'targetNode:id,type,name,slug']);

        return response()->json([
            'edge' => $edge,
            'message' => 'Relationship updated successfully.',
        ]);
    }

    /**
     * Delete a relationship
     */
    public function destroy(Request $request, string $campaignSlug, int $edgeId): JsonResponse
    {
        $campaign = $this->getCampaign($campaignSlug);

        $edge = $campaign->edges()->where('id', $edgeId)->firstOrFail();

        $edge->delete();

        return response()->json([
            'message' => 'Relationship deleted successfully.',
        ]);
    }

    /**
     * Get all relationship types organized by category
     */
    protected function getRelationshipTypes(): array
    {
        return [
            'social' => [
                ['value' => 'knows', 'label' => 'Knows', 'bidirectional' => true],
                ['value' => 'friends_with', 'label' => 'Friends with', 'bidirectional' => true],
                ['value' => 'allied_with', 'label' => 'Allied with', 'bidirectional' => true],
                ['value' => 'enemies_with', 'label' => 'Enemies with', 'bidirectional' => true],
                ['value' => 'rivals_with', 'label' => 'Rivals with', 'bidirectional' => true],
                ['value' => 'related_to', 'label' => 'Related to', 'bidirectional' => true],
                ['value' => 'married_to', 'label' => 'Married to', 'bidirectional' => true],
                ['value' => 'parent_of', 'label' => 'Parent of', 'reverse' => 'child_of'],
                ['value' => 'child_of', 'label' => 'Child of', 'reverse' => 'parent_of'],
                ['value' => 'sibling_of', 'label' => 'Sibling of', 'bidirectional' => true],
            ],
            'hierarchy' => [
                ['value' => 'serves', 'label' => 'Serves', 'reverse' => 'commands'],
                ['value' => 'commands', 'label' => 'Commands', 'reverse' => 'serves'],
                ['value' => 'employs', 'label' => 'Employs', 'reverse' => 'employed_by'],
                ['value' => 'employed_by', 'label' => 'Employed by', 'reverse' => 'employs'],
                ['value' => 'mentors', 'label' => 'Mentors', 'reverse' => 'mentored_by'],
                ['value' => 'mentored_by', 'label' => 'Mentored by', 'reverse' => 'mentors'],
            ],
            'organization' => [
                ['value' => 'member_of', 'label' => 'Member of'],
                ['value' => 'leads', 'label' => 'Leads'],
                ['value' => 'founded', 'label' => 'Founded'],
                ['value' => 'headquartered_in', 'label' => 'Headquartered in'],
            ],
            'location' => [
                ['value' => 'located_in', 'label' => 'Located in'],
                ['value' => 'lives_in', 'label' => 'Lives in'],
                ['value' => 'rules_over', 'label' => 'Rules over'],
                ['value' => 'visited', 'label' => 'Visited'],
                ['value' => 'born_in', 'label' => 'Born in'],
            ],
            'possession' => [
                ['value' => 'owns', 'label' => 'Owns', 'reverse' => 'owned_by'],
                ['value' => 'owned_by', 'label' => 'Owned by', 'reverse' => 'owns'],
                ['value' => 'created', 'label' => 'Created', 'reverse' => 'created_by'],
                ['value' => 'created_by', 'label' => 'Created by', 'reverse' => 'created'],
                ['value' => 'seeks', 'label' => 'Seeks'],
                ['value' => 'guards', 'label' => 'Guards'],
            ],
            'plot' => [
                ['value' => 'involves', 'label' => 'Involves'],
                ['value' => 'involved_in', 'label' => 'Involved in'],
                ['value' => 'takes_place_in', 'label' => 'Takes place in'],
                ['value' => 'caused', 'label' => 'Caused'],
                ['value' => 'affected_by', 'label' => 'Affected by'],
            ],
            'custom' => [
                ['value' => 'custom', 'label' => 'Custom (specify label)'],
            ],
        ];
    }

    /**
     * Get the default label for a relationship type
     */
    protected function getLabelForType(string $type): string
    {
        $types = collect($this->getRelationshipTypes())
            ->flatten(1)
            ->where('value', $type)
            ->first();

        return $types['label'] ?? ucfirst(str_replace('_', ' ', $type));
    }

    /**
     * Get the reverse relationship type
     */
    protected function getReverseType(string $type): string
    {
        $types = collect($this->getRelationshipTypes())->flatten(1);

        $found = $types->where('value', $type)->first();

        if ($found) {
            if (isset($found['bidirectional']) && $found['bidirectional']) {
                return $type;
            }
            if (isset($found['reverse'])) {
                return $found['reverse'];
            }
        }

        return $type;
    }
}
