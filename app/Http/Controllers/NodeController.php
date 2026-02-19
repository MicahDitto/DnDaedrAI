<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Node;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Inertia;

class NodeController extends Controller
{
    protected function getCampaign(string $campaignSlug): Campaign
    {
        return Campaign::where('user_id', Auth::id())
            ->where('slug', $campaignSlug)
            ->firstOrFail();
    }

    protected function getSubtypes(string $type): array
    {
        return match ($type) {
            'character' => [
                'pc' => 'Player Character',
                'npc' => 'NPC',
                'villain' => 'Villain',
                'ally' => 'Ally',
                'neutral' => 'Neutral',
            ],
            'place' => [
                'world' => 'World',
                'continent' => 'Continent',
                'region' => 'Region',
                'city' => 'City',
                'town' => 'Town',
                'village' => 'Village',
                'dungeon' => 'Dungeon',
                'building' => 'Building',
                'landmark' => 'Landmark',
            ],
            'item' => [
                'weapon' => 'Weapon',
                'armor' => 'Armor',
                'artifact' => 'Artifact',
                'consumable' => 'Consumable',
                'treasure' => 'Treasure',
                'mundane' => 'Mundane',
            ],
            'faction' => [
                'guild' => 'Guild',
                'government' => 'Government',
                'religious' => 'Religious Order',
                'criminal' => 'Criminal Organization',
                'military' => 'Military',
                'arcane' => 'Arcane Order',
            ],
            'plot' => [
                'main_quest' => 'Main Quest',
                'side_quest' => 'Side Quest',
                'character_arc' => 'Character Arc',
                'mystery' => 'Mystery',
                'conflict' => 'Conflict',
            ],
            default => [],
        };
    }

    protected function getConfidenceLevels(): array
    {
        return [
            'canon' => 'Canon (Established Fact)',
            'likely' => 'Likely (Probable)',
            'rumor' => 'Rumor (Unconfirmed)',
            'unknown' => 'Unknown (Placeholder)',
        ];
    }

    // Characters
    public function charactersIndex(string $campaignSlug)
    {
        $campaign = $this->getCampaign($campaignSlug);

        $characters = $campaign->nodes()
            ->characters()
            ->orderBy('name')
            ->get();

        return Inertia::render('Characters/Index', [
            'campaign' => $campaign,
            'characters' => $characters,
            'subtypes' => $this->getSubtypes('character'),
        ]);
    }

    public function charactersCreate(string $campaignSlug)
    {
        $campaign = $this->getCampaign($campaignSlug);

        return Inertia::render('Characters/Create', [
            'campaign' => $campaign,
            'subtypes' => $this->getSubtypes('character'),
            'confidenceLevels' => $this->getConfidenceLevels(),
        ]);
    }

    public function charactersStore(Request $request, string $campaignSlug)
    {
        $campaign = $this->getCampaign($campaignSlug);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subtype' => 'required|string|in:' . implode(',', array_keys($this->getSubtypes('character'))),
            'summary' => 'nullable|string|max:500',
            'content' => 'nullable|array',
            'content.appearance' => 'nullable|string',
            'content.personality' => 'nullable|string',
            'content.motivation' => 'nullable|string',
            'content.secrets' => 'nullable|string',
            'content.voice_notes' => 'nullable|string',
            'content.stats' => 'nullable|array',
            'confidence' => 'required|string|in:' . implode(',', array_keys($this->getConfidenceLevels())),
            'is_secret' => 'boolean',
        ]);

        $node = $campaign->nodes()->create([
            'type' => 'character',
            'subtype' => $validated['subtype'],
            'name' => $validated['name'],
            'summary' => $validated['summary'] ?? null,
            'content' => $validated['content'] ?? [],
            'confidence' => $validated['confidence'],
            'is_secret' => $validated['is_secret'] ?? false,
        ]);

        return redirect()->route('campaigns.characters.show', [
            'campaignSlug' => $campaign->slug,
            'nodeSlug' => $node->slug,
        ])->with('success', 'Character created successfully.');
    }

    public function charactersShow(string $campaignSlug, string $nodeSlug)
    {
        $campaign = $this->getCampaign($campaignSlug);

        $character = $campaign->nodes()
            ->characters()
            ->where('slug', $nodeSlug)
            ->with(['tags', 'outgoingEdges.targetNode', 'incomingEdges.sourceNode'])
            ->firstOrFail();

        return Inertia::render('Characters/Show', [
            'campaign' => $campaign,
            'character' => $character,
        ]);
    }

    public function charactersEdit(string $campaignSlug, string $nodeSlug)
    {
        $campaign = $this->getCampaign($campaignSlug);

        $character = $campaign->nodes()
            ->characters()
            ->where('slug', $nodeSlug)
            ->firstOrFail();

        return Inertia::render('Characters/Edit', [
            'campaign' => $campaign,
            'character' => $character,
            'subtypes' => $this->getSubtypes('character'),
            'confidenceLevels' => $this->getConfidenceLevels(),
        ]);
    }

    public function charactersUpdate(Request $request, string $campaignSlug, string $nodeSlug)
    {
        $campaign = $this->getCampaign($campaignSlug);

        $character = $campaign->nodes()
            ->characters()
            ->where('slug', $nodeSlug)
            ->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subtype' => 'required|string|in:' . implode(',', array_keys($this->getSubtypes('character'))),
            'summary' => 'nullable|string|max:500',
            'content' => 'nullable|array',
            'content.appearance' => 'nullable|string',
            'content.personality' => 'nullable|string',
            'content.motivation' => 'nullable|string',
            'content.secrets' => 'nullable|string',
            'content.voice_notes' => 'nullable|string',
            'content.stats' => 'nullable|array',
            'confidence' => 'required|string|in:' . implode(',', array_keys($this->getConfidenceLevels())),
            'is_secret' => 'boolean',
        ]);

        $character->update([
            'subtype' => $validated['subtype'],
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'summary' => $validated['summary'] ?? null,
            'content' => $validated['content'] ?? [],
            'confidence' => $validated['confidence'],
            'is_secret' => $validated['is_secret'] ?? false,
        ]);

        return redirect()->route('campaigns.characters.show', [
            'campaignSlug' => $campaign->slug,
            'nodeSlug' => $character->slug,
        ])->with('success', 'Character updated successfully.');
    }

    public function charactersDestroy(string $campaignSlug, string $nodeSlug)
    {
        $campaign = $this->getCampaign($campaignSlug);

        $character = $campaign->nodes()
            ->characters()
            ->where('slug', $nodeSlug)
            ->firstOrFail();

        $character->delete();

        return redirect()->route('campaigns.characters.index', $campaign->slug)
            ->with('success', 'Character deleted successfully.');
    }

    // Places
    public function placesIndex(string $campaignSlug)
    {
        $campaign = $this->getCampaign($campaignSlug);

        $places = $campaign->nodes()
            ->places()
            ->orderBy('name')
            ->get();

        // Get parent places for hierarchy display
        $parentPlaces = $campaign->nodes()
            ->places()
            ->whereIn('subtype', ['world', 'continent', 'region', 'city'])
            ->orderBy('name')
            ->get(['id', 'name', 'subtype']);

        return Inertia::render('Places/Index', [
            'campaign' => $campaign,
            'places' => $places,
            'subtypes' => $this->getSubtypes('place'),
        ]);
    }

    public function placesCreate(string $campaignSlug)
    {
        $campaign = $this->getCampaign($campaignSlug);

        $parentPlaces = $campaign->nodes()
            ->places()
            ->whereIn('subtype', ['world', 'continent', 'region', 'city', 'town'])
            ->orderBy('name')
            ->get(['id', 'name', 'subtype']);

        return Inertia::render('Places/Create', [
            'campaign' => $campaign,
            'subtypes' => $this->getSubtypes('place'),
            'confidenceLevels' => $this->getConfidenceLevels(),
            'parentPlaces' => $parentPlaces,
        ]);
    }

    public function placesStore(Request $request, string $campaignSlug)
    {
        $campaign = $this->getCampaign($campaignSlug);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subtype' => 'required|string|in:' . implode(',', array_keys($this->getSubtypes('place'))),
            'summary' => 'nullable|string|max:500',
            'content' => 'nullable|array',
            'content.description' => 'nullable|string',
            'content.population' => 'nullable|string',
            'content.culture' => 'nullable|string',
            'content.history' => 'nullable|string',
            'content.points_of_interest' => 'nullable|string',
            'content.secrets' => 'nullable|string',
            'parent_id' => 'nullable|uuid|exists:nodes,id',
            'confidence' => 'required|string|in:' . implode(',', array_keys($this->getConfidenceLevels())),
            'is_secret' => 'boolean',
        ]);

        $metadata = [];
        if (!empty($validated['parent_id'])) {
            $metadata['parent_id'] = $validated['parent_id'];
        }

        $node = $campaign->nodes()->create([
            'type' => 'place',
            'subtype' => $validated['subtype'],
            'name' => $validated['name'],
            'summary' => $validated['summary'] ?? null,
            'content' => $validated['content'] ?? [],
            'metadata' => $metadata,
            'confidence' => $validated['confidence'],
            'is_secret' => $validated['is_secret'] ?? false,
        ]);

        // Create edge to parent if specified
        if (!empty($validated['parent_id'])) {
            $campaign->edges()->create([
                'source_node_id' => $node->id,
                'target_node_id' => $validated['parent_id'],
                'type' => 'located_in',
                'label' => 'Located in',
            ]);
        }

        return redirect()->route('campaigns.places.show', [
            'campaignSlug' => $campaign->slug,
            'nodeSlug' => $node->slug,
        ])->with('success', 'Place created successfully.');
    }

    public function placesShow(string $campaignSlug, string $nodeSlug)
    {
        $campaign = $this->getCampaign($campaignSlug);

        $place = $campaign->nodes()
            ->places()
            ->where('slug', $nodeSlug)
            ->with(['tags', 'outgoingEdges.targetNode', 'incomingEdges.sourceNode'])
            ->firstOrFail();

        // Get child places (places that have an edge to this place)
        $childPlaces = $campaign->nodes()
            ->places()
            ->whereHas('outgoingEdges', function ($query) use ($place) {
                $query->where('target_node_id', $place->id)
                    ->where('type', 'located_in');
            })
            ->get();

        // Get characters located here
        $characters = $campaign->nodes()
            ->characters()
            ->whereHas('outgoingEdges', function ($query) use ($place) {
                $query->where('target_node_id', $place->id)
                    ->where('type', 'located_in');
            })
            ->get();

        return Inertia::render('Places/Show', [
            'campaign' => $campaign,
            'place' => $place,
            'childPlaces' => $childPlaces,
            'characters' => $characters,
        ]);
    }

    public function placesEdit(string $campaignSlug, string $nodeSlug)
    {
        $campaign = $this->getCampaign($campaignSlug);

        $place = $campaign->nodes()
            ->places()
            ->where('slug', $nodeSlug)
            ->firstOrFail();

        $parentPlaces = $campaign->nodes()
            ->places()
            ->whereIn('subtype', ['world', 'continent', 'region', 'city', 'town'])
            ->where('id', '!=', $place->id)
            ->orderBy('name')
            ->get(['id', 'name', 'subtype']);

        // Get current parent from edges
        $currentParent = $place->outgoingEdges()
            ->where('type', 'located_in')
            ->first();

        return Inertia::render('Places/Edit', [
            'campaign' => $campaign,
            'place' => $place,
            'subtypes' => $this->getSubtypes('place'),
            'confidenceLevels' => $this->getConfidenceLevels(),
            'parentPlaces' => $parentPlaces,
            'currentParentId' => $currentParent?->target_node_id,
        ]);
    }

    public function placesUpdate(Request $request, string $campaignSlug, string $nodeSlug)
    {
        $campaign = $this->getCampaign($campaignSlug);

        $place = $campaign->nodes()
            ->places()
            ->where('slug', $nodeSlug)
            ->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subtype' => 'required|string|in:' . implode(',', array_keys($this->getSubtypes('place'))),
            'summary' => 'nullable|string|max:500',
            'content' => 'nullable|array',
            'content.description' => 'nullable|string',
            'content.population' => 'nullable|string',
            'content.culture' => 'nullable|string',
            'content.history' => 'nullable|string',
            'content.points_of_interest' => 'nullable|string',
            'content.secrets' => 'nullable|string',
            'parent_id' => 'nullable|uuid|exists:nodes,id',
            'confidence' => 'required|string|in:' . implode(',', array_keys($this->getConfidenceLevels())),
            'is_secret' => 'boolean',
        ]);

        $metadata = $place->metadata ?? [];
        if (!empty($validated['parent_id'])) {
            $metadata['parent_id'] = $validated['parent_id'];
        } else {
            unset($metadata['parent_id']);
        }

        $place->update([
            'subtype' => $validated['subtype'],
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'summary' => $validated['summary'] ?? null,
            'content' => $validated['content'] ?? [],
            'metadata' => $metadata,
            'confidence' => $validated['confidence'],
            'is_secret' => $validated['is_secret'] ?? false,
        ]);

        // Update parent edge
        $place->outgoingEdges()->where('type', 'located_in')->delete();
        if (!empty($validated['parent_id'])) {
            $campaign->edges()->create([
                'source_node_id' => $place->id,
                'target_node_id' => $validated['parent_id'],
                'type' => 'located_in',
                'label' => 'Located in',
            ]);
        }

        return redirect()->route('campaigns.places.show', [
            'campaignSlug' => $campaign->slug,
            'nodeSlug' => $place->slug,
        ])->with('success', 'Place updated successfully.');
    }

    public function placesDestroy(string $campaignSlug, string $nodeSlug)
    {
        $campaign = $this->getCampaign($campaignSlug);

        $place = $campaign->nodes()
            ->places()
            ->where('slug', $nodeSlug)
            ->firstOrFail();

        $place->delete();

        return redirect()->route('campaigns.places.index', $campaign->slug)
            ->with('success', 'Place deleted successfully.');
    }
}
