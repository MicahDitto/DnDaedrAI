<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Media;
use App\Models\Node;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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
            'lore' => [
                'myth' => 'Myth',
                'legend' => 'Legend',
                'prophecy' => 'Prophecy',
                'historical_event' => 'Historical Event',
                'folktale' => 'Folktale',
                'creation_story' => 'Creation Story',
                'cautionary_tale' => 'Cautionary Tale',
                'epic' => 'Epic',
            ],
            'religion' => [
                'pantheon' => 'Pantheon',
                'monotheistic' => 'Monotheistic',
                'dualistic' => 'Dualistic',
                'animist' => 'Animist',
                'ancestor_worship' => 'Ancestor Worship',
                'cult' => 'Cult',
                'philosophy' => 'Philosophy',
                'dead_religion' => 'Dead Religion',
            ],
            'magic_system' => [
                'school' => 'School',
                'source' => 'Source',
                'tradition' => 'Tradition',
                'discipline' => 'Discipline',
                'artifact_magic' => 'Artifact Magic',
                'divine_magic' => 'Divine Magic',
                'primal_magic' => 'Primal Magic',
                'forbidden' => 'Forbidden',
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
            ->with('featuredImage')
            ->orderBy('name')
            ->get();

        // Transform characters to include featured image URL
        $charactersData = $characters->map(function ($character) {
            $data = $character->toArray();
            $data['featured_image_url'] = $character->featuredImage
                ? Storage::url($character->featuredImage->path)
                : null;
            return $data;
        });

        return Inertia::render('Characters/Index', [
            'campaign' => $campaign,
            'characters' => $charactersData,
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
            'featured_image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp,svg|max:5120',
            'gallery_images.*' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp,svg|max:5120',
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

        // Handle featured image
        if ($request->hasFile('featured_image')) {
            $this->storeFeaturedImage($node, $request->file('featured_image'));
        }

        // Handle gallery images
        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $index => $file) {
                $this->storeGalleryImage($node, $file, $index);
            }
        }

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
            ->with(['tags', 'outgoingEdges.targetNode', 'incomingEdges.sourceNode', 'featuredImage', 'galleryImages'])
            ->firstOrFail();

        // Transform character for frontend
        $characterData = $character->toArray();
        $characterData['featured_image'] = $character->featuredImage ? [
            'id' => $character->featuredImage->id,
            'url' => Storage::url($character->featuredImage->path),
            'filename' => $character->featuredImage->filename,
        ] : null;
        $characterData['gallery_images'] = $character->galleryImages->map(fn($img) => [
            'id' => $img->id,
            'url' => Storage::url($img->path),
            'filename' => $img->filename,
            'order' => $img->order,
        ])->toArray();

        return Inertia::render('Characters/Show', [
            'campaign' => $campaign,
            'character' => $characterData,
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
            'featured_image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp,svg|max:5120',
            'gallery_images.*' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp,svg|max:5120',
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

        // Handle featured image replacement
        if ($request->hasFile('featured_image')) {
            // Delete old featured image
            $oldFeatured = $character->featuredImage;
            if ($oldFeatured) {
                Storage::delete($oldFeatured->path);
                $oldFeatured->delete();
            }
            // Store new one
            $this->storeFeaturedImage($character, $request->file('featured_image'));
        }

        // Handle new gallery images
        if ($request->hasFile('gallery_images')) {
            $currentCount = $character->galleryImages()->count();
            foreach ($request->file('gallery_images') as $index => $file) {
                $this->storeGalleryImage($character, $file, $currentCount + $index);
            }
        }

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

    // Factions
    public function factionsIndex(string $campaignSlug)
    {
        $campaign = $this->getCampaign($campaignSlug);

        $factions = $campaign->nodes()
            ->factions()
            ->orderBy('name')
            ->get();

        return Inertia::render('Factions/Index', [
            'campaign' => $campaign,
            'factions' => $factions,
            'subtypes' => $this->getSubtypes('faction'),
        ]);
    }

    public function factionsCreate(string $campaignSlug)
    {
        $campaign = $this->getCampaign($campaignSlug);

        // Get places for headquarters selection
        $places = $campaign->nodes()
            ->places()
            ->orderBy('name')
            ->get(['id', 'name', 'subtype']);

        return Inertia::render('Factions/Create', [
            'campaign' => $campaign,
            'subtypes' => $this->getSubtypes('faction'),
            'confidenceLevels' => $this->getConfidenceLevels(),
            'places' => $places,
        ]);
    }

    public function factionsStore(Request $request, string $campaignSlug)
    {
        $campaign = $this->getCampaign($campaignSlug);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subtype' => 'required|string|in:' . implode(',', array_keys($this->getSubtypes('faction'))),
            'summary' => 'nullable|string|max:500',
            'content' => 'nullable|array',
            'content.description' => 'nullable|string',
            'content.goals' => 'nullable|string',
            'content.methods' => 'nullable|string',
            'content.resources' => 'nullable|string',
            'content.history' => 'nullable|string',
            'content.secrets' => 'nullable|string',
            'headquarters_id' => 'nullable|uuid|exists:nodes,id',
            'confidence' => 'required|string|in:' . implode(',', array_keys($this->getConfidenceLevels())),
            'is_secret' => 'boolean',
        ]);

        $metadata = [];
        if (!empty($validated['headquarters_id'])) {
            $metadata['headquarters_id'] = $validated['headquarters_id'];
        }

        $node = $campaign->nodes()->create([
            'type' => 'faction',
            'subtype' => $validated['subtype'],
            'name' => $validated['name'],
            'summary' => $validated['summary'] ?? null,
            'content' => $validated['content'] ?? [],
            'metadata' => $metadata,
            'confidence' => $validated['confidence'],
            'is_secret' => $validated['is_secret'] ?? false,
        ]);

        // Create edge to headquarters if specified
        if (!empty($validated['headquarters_id'])) {
            $campaign->edges()->create([
                'source_node_id' => $node->id,
                'target_node_id' => $validated['headquarters_id'],
                'type' => 'headquartered_in',
                'label' => 'Headquartered in',
            ]);
        }

        return redirect()->route('campaigns.factions.show', [
            'campaignSlug' => $campaign->slug,
            'nodeSlug' => $node->slug,
        ])->with('success', 'Faction created successfully.');
    }

    public function factionsShow(string $campaignSlug, string $nodeSlug)
    {
        $campaign = $this->getCampaign($campaignSlug);

        $faction = $campaign->nodes()
            ->factions()
            ->where('slug', $nodeSlug)
            ->with(['tags', 'outgoingEdges.targetNode', 'incomingEdges.sourceNode'])
            ->firstOrFail();

        // Get members (characters with member_of edge to this faction)
        $members = $campaign->nodes()
            ->characters()
            ->whereHas('outgoingEdges', function ($query) use ($faction) {
                $query->where('target_node_id', $faction->id)
                    ->where('type', 'member_of');
            })
            ->get();

        // Get headquarters
        $headquartersEdge = $faction->outgoingEdges()
            ->where('type', 'headquartered_in')
            ->with('targetNode')
            ->first();
        $headquarters = $headquartersEdge?->targetNode;

        // Get allied factions
        $alliedFactions = $campaign->nodes()
            ->factions()
            ->where('id', '!=', $faction->id)
            ->where(function ($query) use ($faction) {
                $query->whereHas('outgoingEdges', function ($q) use ($faction) {
                    $q->where('target_node_id', $faction->id)
                        ->where('type', 'allied_with');
                })->orWhereHas('incomingEdges', function ($q) use ($faction) {
                    $q->where('source_node_id', $faction->id)
                        ->where('type', 'allied_with');
                });
            })
            ->get();

        // Get rival factions
        $rivalFactions = $campaign->nodes()
            ->factions()
            ->where('id', '!=', $faction->id)
            ->where(function ($query) use ($faction) {
                $query->whereHas('outgoingEdges', function ($q) use ($faction) {
                    $q->where('target_node_id', $faction->id)
                        ->where('type', 'rivals_with');
                })->orWhereHas('incomingEdges', function ($q) use ($faction) {
                    $q->where('source_node_id', $faction->id)
                        ->where('type', 'rivals_with');
                });
            })
            ->get();

        return Inertia::render('Factions/Show', [
            'campaign' => $campaign,
            'faction' => $faction,
            'members' => $members,
            'headquarters' => $headquarters,
            'alliedFactions' => $alliedFactions,
            'rivalFactions' => $rivalFactions,
        ]);
    }

    public function factionsEdit(string $campaignSlug, string $nodeSlug)
    {
        $campaign = $this->getCampaign($campaignSlug);

        $faction = $campaign->nodes()
            ->factions()
            ->where('slug', $nodeSlug)
            ->firstOrFail();

        $places = $campaign->nodes()
            ->places()
            ->orderBy('name')
            ->get(['id', 'name', 'subtype']);

        // Get current headquarters from edges
        $currentHeadquarters = $faction->outgoingEdges()
            ->where('type', 'headquartered_in')
            ->first();

        return Inertia::render('Factions/Edit', [
            'campaign' => $campaign,
            'faction' => $faction,
            'subtypes' => $this->getSubtypes('faction'),
            'confidenceLevels' => $this->getConfidenceLevels(),
            'places' => $places,
            'currentHeadquartersId' => $currentHeadquarters?->target_node_id,
        ]);
    }

    public function factionsUpdate(Request $request, string $campaignSlug, string $nodeSlug)
    {
        $campaign = $this->getCampaign($campaignSlug);

        $faction = $campaign->nodes()
            ->factions()
            ->where('slug', $nodeSlug)
            ->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subtype' => 'required|string|in:' . implode(',', array_keys($this->getSubtypes('faction'))),
            'summary' => 'nullable|string|max:500',
            'content' => 'nullable|array',
            'content.description' => 'nullable|string',
            'content.goals' => 'nullable|string',
            'content.methods' => 'nullable|string',
            'content.resources' => 'nullable|string',
            'content.history' => 'nullable|string',
            'content.secrets' => 'nullable|string',
            'headquarters_id' => 'nullable|uuid|exists:nodes,id',
            'confidence' => 'required|string|in:' . implode(',', array_keys($this->getConfidenceLevels())),
            'is_secret' => 'boolean',
        ]);

        $metadata = $faction->metadata ?? [];
        if (!empty($validated['headquarters_id'])) {
            $metadata['headquarters_id'] = $validated['headquarters_id'];
        } else {
            unset($metadata['headquarters_id']);
        }

        $faction->update([
            'subtype' => $validated['subtype'],
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'summary' => $validated['summary'] ?? null,
            'content' => $validated['content'] ?? [],
            'metadata' => $metadata,
            'confidence' => $validated['confidence'],
            'is_secret' => $validated['is_secret'] ?? false,
        ]);

        // Update headquarters edge
        $faction->outgoingEdges()->where('type', 'headquartered_in')->delete();
        if (!empty($validated['headquarters_id'])) {
            $campaign->edges()->create([
                'source_node_id' => $faction->id,
                'target_node_id' => $validated['headquarters_id'],
                'type' => 'headquartered_in',
                'label' => 'Headquartered in',
            ]);
        }

        return redirect()->route('campaigns.factions.show', [
            'campaignSlug' => $campaign->slug,
            'nodeSlug' => $faction->slug,
        ])->with('success', 'Faction updated successfully.');
    }

    public function factionsDestroy(string $campaignSlug, string $nodeSlug)
    {
        $campaign = $this->getCampaign($campaignSlug);

        $faction = $campaign->nodes()
            ->factions()
            ->where('slug', $nodeSlug)
            ->firstOrFail();

        $faction->delete();

        return redirect()->route('campaigns.factions.index', $campaign->slug)
            ->with('success', 'Faction deleted successfully.');
    }

    // Items
    public function itemsIndex(string $campaignSlug)
    {
        $campaign = $this->getCampaign($campaignSlug);

        $items = $campaign->nodes()
            ->items()
            ->orderBy('name')
            ->get();

        return Inertia::render('Items/Index', [
            'campaign' => $campaign,
            'items' => $items,
            'subtypes' => $this->getSubtypes('item'),
        ]);
    }

    public function itemsCreate(string $campaignSlug)
    {
        $campaign = $this->getCampaign($campaignSlug);

        // Get characters for owner selection
        $characters = $campaign->nodes()
            ->characters()
            ->orderBy('name')
            ->get(['id', 'name', 'subtype']);

        // Get places for location selection
        $places = $campaign->nodes()
            ->places()
            ->orderBy('name')
            ->get(['id', 'name', 'subtype']);

        return Inertia::render('Items/Create', [
            'campaign' => $campaign,
            'subtypes' => $this->getSubtypes('item'),
            'confidenceLevels' => $this->getConfidenceLevels(),
            'characters' => $characters,
            'places' => $places,
        ]);
    }

    public function itemsStore(Request $request, string $campaignSlug)
    {
        $campaign = $this->getCampaign($campaignSlug);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subtype' => 'required|string|in:' . implode(',', array_keys($this->getSubtypes('item'))),
            'summary' => 'nullable|string|max:500',
            'content' => 'nullable|array',
            'content.description' => 'nullable|string',
            'content.properties' => 'nullable|string',
            'content.history' => 'nullable|string',
            'content.secrets' => 'nullable|string',
            'owner_id' => 'nullable|uuid|exists:nodes,id',
            'location_id' => 'nullable|uuid|exists:nodes,id',
            'confidence' => 'required|string|in:' . implode(',', array_keys($this->getConfidenceLevels())),
            'is_secret' => 'boolean',
        ]);

        $metadata = [];
        if (!empty($validated['owner_id'])) {
            $metadata['owner_id'] = $validated['owner_id'];
        }
        if (!empty($validated['location_id'])) {
            $metadata['location_id'] = $validated['location_id'];
        }

        $node = $campaign->nodes()->create([
            'type' => 'item',
            'subtype' => $validated['subtype'],
            'name' => $validated['name'],
            'summary' => $validated['summary'] ?? null,
            'content' => $validated['content'] ?? [],
            'metadata' => $metadata,
            'confidence' => $validated['confidence'],
            'is_secret' => $validated['is_secret'] ?? false,
        ]);

        // Create edge to owner if specified
        if (!empty($validated['owner_id'])) {
            $campaign->edges()->create([
                'source_node_id' => $node->id,
                'target_node_id' => $validated['owner_id'],
                'type' => 'owned_by',
                'label' => 'Owned by',
            ]);
        }

        // Create edge to location if specified
        if (!empty($validated['location_id'])) {
            $campaign->edges()->create([
                'source_node_id' => $node->id,
                'target_node_id' => $validated['location_id'],
                'type' => 'located_in',
                'label' => 'Located in',
            ]);
        }

        return redirect()->route('campaigns.items.show', [
            'campaignSlug' => $campaign->slug,
            'nodeSlug' => $node->slug,
        ])->with('success', 'Item created successfully.');
    }

    public function itemsShow(string $campaignSlug, string $nodeSlug)
    {
        $campaign = $this->getCampaign($campaignSlug);

        $item = $campaign->nodes()
            ->items()
            ->where('slug', $nodeSlug)
            ->with(['tags', 'outgoingEdges.targetNode', 'incomingEdges.sourceNode'])
            ->firstOrFail();

        // Get owner
        $ownerEdge = $item->outgoingEdges()
            ->where('type', 'owned_by')
            ->with('targetNode')
            ->first();
        $owner = $ownerEdge?->targetNode;

        // Get location
        $locationEdge = $item->outgoingEdges()
            ->where('type', 'located_in')
            ->with('targetNode')
            ->first();
        $location = $locationEdge?->targetNode;

        return Inertia::render('Items/Show', [
            'campaign' => $campaign,
            'item' => $item,
            'owner' => $owner,
            'location' => $location,
        ]);
    }

    public function itemsEdit(string $campaignSlug, string $nodeSlug)
    {
        $campaign = $this->getCampaign($campaignSlug);

        $item = $campaign->nodes()
            ->items()
            ->where('slug', $nodeSlug)
            ->firstOrFail();

        $characters = $campaign->nodes()
            ->characters()
            ->orderBy('name')
            ->get(['id', 'name', 'subtype']);

        $places = $campaign->nodes()
            ->places()
            ->orderBy('name')
            ->get(['id', 'name', 'subtype']);

        // Get current owner from edges
        $currentOwner = $item->outgoingEdges()
            ->where('type', 'owned_by')
            ->first();

        // Get current location from edges
        $currentLocation = $item->outgoingEdges()
            ->where('type', 'located_in')
            ->first();

        return Inertia::render('Items/Edit', [
            'campaign' => $campaign,
            'item' => $item,
            'subtypes' => $this->getSubtypes('item'),
            'confidenceLevels' => $this->getConfidenceLevels(),
            'characters' => $characters,
            'places' => $places,
            'currentOwnerId' => $currentOwner?->target_node_id,
            'currentLocationId' => $currentLocation?->target_node_id,
        ]);
    }

    public function itemsUpdate(Request $request, string $campaignSlug, string $nodeSlug)
    {
        $campaign = $this->getCampaign($campaignSlug);

        $item = $campaign->nodes()
            ->items()
            ->where('slug', $nodeSlug)
            ->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subtype' => 'required|string|in:' . implode(',', array_keys($this->getSubtypes('item'))),
            'summary' => 'nullable|string|max:500',
            'content' => 'nullable|array',
            'content.description' => 'nullable|string',
            'content.properties' => 'nullable|string',
            'content.history' => 'nullable|string',
            'content.secrets' => 'nullable|string',
            'owner_id' => 'nullable|uuid|exists:nodes,id',
            'location_id' => 'nullable|uuid|exists:nodes,id',
            'confidence' => 'required|string|in:' . implode(',', array_keys($this->getConfidenceLevels())),
            'is_secret' => 'boolean',
        ]);

        $metadata = $item->metadata ?? [];
        if (!empty($validated['owner_id'])) {
            $metadata['owner_id'] = $validated['owner_id'];
        } else {
            unset($metadata['owner_id']);
        }
        if (!empty($validated['location_id'])) {
            $metadata['location_id'] = $validated['location_id'];
        } else {
            unset($metadata['location_id']);
        }

        $item->update([
            'subtype' => $validated['subtype'],
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'summary' => $validated['summary'] ?? null,
            'content' => $validated['content'] ?? [],
            'metadata' => $metadata,
            'confidence' => $validated['confidence'],
            'is_secret' => $validated['is_secret'] ?? false,
        ]);

        // Update owner edge
        $item->outgoingEdges()->where('type', 'owned_by')->delete();
        if (!empty($validated['owner_id'])) {
            $campaign->edges()->create([
                'source_node_id' => $item->id,
                'target_node_id' => $validated['owner_id'],
                'type' => 'owned_by',
                'label' => 'Owned by',
            ]);
        }

        // Update location edge
        $item->outgoingEdges()->where('type', 'located_in')->delete();
        if (!empty($validated['location_id'])) {
            $campaign->edges()->create([
                'source_node_id' => $item->id,
                'target_node_id' => $validated['location_id'],
                'type' => 'located_in',
                'label' => 'Located in',
            ]);
        }

        return redirect()->route('campaigns.items.show', [
            'campaignSlug' => $campaign->slug,
            'nodeSlug' => $item->slug,
        ])->with('success', 'Item updated successfully.');
    }

    public function itemsDestroy(string $campaignSlug, string $nodeSlug)
    {
        $campaign = $this->getCampaign($campaignSlug);

        $item = $campaign->nodes()
            ->items()
            ->where('slug', $nodeSlug)
            ->firstOrFail();

        $item->delete();

        return redirect()->route('campaigns.items.index', $campaign->slug)
            ->with('success', 'Item deleted successfully.');
    }

    // Plots
    public function plotsIndex(string $campaignSlug)
    {
        $campaign = $this->getCampaign($campaignSlug);

        $plots = $campaign->nodes()
            ->plots()
            ->orderBy('name')
            ->get();

        return Inertia::render('Plots/Index', [
            'campaign' => $campaign,
            'plots' => $plots,
            'subtypes' => $this->getSubtypes('plot'),
        ]);
    }

    public function plotsCreate(string $campaignSlug)
    {
        $campaign = $this->getCampaign($campaignSlug);

        // Get characters for involvement selection
        $characters = $campaign->nodes()
            ->characters()
            ->orderBy('name')
            ->get(['id', 'name', 'subtype']);

        // Get places for location selection
        $places = $campaign->nodes()
            ->places()
            ->orderBy('name')
            ->get(['id', 'name', 'subtype']);

        // Get factions for involvement selection
        $factions = $campaign->nodes()
            ->factions()
            ->orderBy('name')
            ->get(['id', 'name', 'subtype']);

        return Inertia::render('Plots/Create', [
            'campaign' => $campaign,
            'subtypes' => $this->getSubtypes('plot'),
            'confidenceLevels' => $this->getConfidenceLevels(),
            'characters' => $characters,
            'places' => $places,
            'factions' => $factions,
        ]);
    }

    public function plotsStore(Request $request, string $campaignSlug)
    {
        $campaign = $this->getCampaign($campaignSlug);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subtype' => 'required|string|in:' . implode(',', array_keys($this->getSubtypes('plot'))),
            'summary' => 'nullable|string|max:500',
            'content' => 'nullable|array',
            'content.description' => 'nullable|string',
            'content.objectives' => 'nullable|string',
            'content.stakes' => 'nullable|string',
            'content.progress' => 'nullable|string',
            'content.secrets' => 'nullable|string',
            'involved_character_ids' => 'nullable|array',
            'involved_character_ids.*' => 'uuid|exists:nodes,id',
            'involved_place_ids' => 'nullable|array',
            'involved_place_ids.*' => 'uuid|exists:nodes,id',
            'involved_faction_ids' => 'nullable|array',
            'involved_faction_ids.*' => 'uuid|exists:nodes,id',
            'confidence' => 'required|string|in:' . implode(',', array_keys($this->getConfidenceLevels())),
            'is_secret' => 'boolean',
        ]);

        $metadata = [];
        if (!empty($validated['involved_character_ids'])) {
            $metadata['involved_character_ids'] = $validated['involved_character_ids'];
        }
        if (!empty($validated['involved_place_ids'])) {
            $metadata['involved_place_ids'] = $validated['involved_place_ids'];
        }
        if (!empty($validated['involved_faction_ids'])) {
            $metadata['involved_faction_ids'] = $validated['involved_faction_ids'];
        }

        $node = $campaign->nodes()->create([
            'type' => 'plot',
            'subtype' => $validated['subtype'],
            'name' => $validated['name'],
            'summary' => $validated['summary'] ?? null,
            'content' => $validated['content'] ?? [],
            'metadata' => $metadata,
            'confidence' => $validated['confidence'],
            'is_secret' => $validated['is_secret'] ?? false,
        ]);

        // Create edges to involved characters
        if (!empty($validated['involved_character_ids'])) {
            foreach ($validated['involved_character_ids'] as $characterId) {
                $campaign->edges()->create([
                    'source_node_id' => $node->id,
                    'target_node_id' => $characterId,
                    'type' => 'involves',
                    'label' => 'Involves',
                ]);
            }
        }

        // Create edges to involved places
        if (!empty($validated['involved_place_ids'])) {
            foreach ($validated['involved_place_ids'] as $placeId) {
                $campaign->edges()->create([
                    'source_node_id' => $node->id,
                    'target_node_id' => $placeId,
                    'type' => 'takes_place_in',
                    'label' => 'Takes place in',
                ]);
            }
        }

        // Create edges to involved factions
        if (!empty($validated['involved_faction_ids'])) {
            foreach ($validated['involved_faction_ids'] as $factionId) {
                $campaign->edges()->create([
                    'source_node_id' => $node->id,
                    'target_node_id' => $factionId,
                    'type' => 'involves',
                    'label' => 'Involves',
                ]);
            }
        }

        return redirect()->route('campaigns.plots.show', [
            'campaignSlug' => $campaign->slug,
            'nodeSlug' => $node->slug,
        ])->with('success', 'Plot created successfully.');
    }

    public function plotsShow(string $campaignSlug, string $nodeSlug)
    {
        $campaign = $this->getCampaign($campaignSlug);

        $plot = $campaign->nodes()
            ->plots()
            ->where('slug', $nodeSlug)
            ->with(['tags', 'outgoingEdges.targetNode', 'incomingEdges.sourceNode'])
            ->firstOrFail();

        // Get involved characters
        $involvedCharacters = $campaign->nodes()
            ->characters()
            ->whereHas('incomingEdges', function ($query) use ($plot) {
                $query->where('source_node_id', $plot->id)
                    ->where('type', 'involves');
            })
            ->get();

        // Get involved places
        $involvedPlaces = $campaign->nodes()
            ->places()
            ->whereHas('incomingEdges', function ($query) use ($plot) {
                $query->where('source_node_id', $plot->id)
                    ->where('type', 'takes_place_in');
            })
            ->get();

        // Get involved factions
        $involvedFactions = $campaign->nodes()
            ->factions()
            ->whereHas('incomingEdges', function ($query) use ($plot) {
                $query->where('source_node_id', $plot->id)
                    ->where('type', 'involves');
            })
            ->get();

        return Inertia::render('Plots/Show', [
            'campaign' => $campaign,
            'plot' => $plot,
            'involvedCharacters' => $involvedCharacters,
            'involvedPlaces' => $involvedPlaces,
            'involvedFactions' => $involvedFactions,
        ]);
    }

    public function plotsEdit(string $campaignSlug, string $nodeSlug)
    {
        $campaign = $this->getCampaign($campaignSlug);

        $plot = $campaign->nodes()
            ->plots()
            ->where('slug', $nodeSlug)
            ->firstOrFail();

        $characters = $campaign->nodes()
            ->characters()
            ->orderBy('name')
            ->get(['id', 'name', 'subtype']);

        $places = $campaign->nodes()
            ->places()
            ->orderBy('name')
            ->get(['id', 'name', 'subtype']);

        $factions = $campaign->nodes()
            ->factions()
            ->orderBy('name')
            ->get(['id', 'name', 'subtype']);

        // Get current involved characters from edges
        $currentInvolvedCharacterIds = $plot->outgoingEdges()
            ->where('type', 'involves')
            ->whereHas('targetNode', function ($query) {
                $query->where('type', 'character');
            })
            ->pluck('target_node_id')
            ->toArray();

        // Get current involved places from edges
        $currentInvolvedPlaceIds = $plot->outgoingEdges()
            ->where('type', 'takes_place_in')
            ->pluck('target_node_id')
            ->toArray();

        // Get current involved factions from edges
        $currentInvolvedFactionIds = $plot->outgoingEdges()
            ->where('type', 'involves')
            ->whereHas('targetNode', function ($query) {
                $query->where('type', 'faction');
            })
            ->pluck('target_node_id')
            ->toArray();

        return Inertia::render('Plots/Edit', [
            'campaign' => $campaign,
            'plot' => $plot,
            'subtypes' => $this->getSubtypes('plot'),
            'confidenceLevels' => $this->getConfidenceLevels(),
            'characters' => $characters,
            'places' => $places,
            'factions' => $factions,
            'currentInvolvedCharacterIds' => $currentInvolvedCharacterIds,
            'currentInvolvedPlaceIds' => $currentInvolvedPlaceIds,
            'currentInvolvedFactionIds' => $currentInvolvedFactionIds,
        ]);
    }

    public function plotsUpdate(Request $request, string $campaignSlug, string $nodeSlug)
    {
        $campaign = $this->getCampaign($campaignSlug);

        $plot = $campaign->nodes()
            ->plots()
            ->where('slug', $nodeSlug)
            ->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subtype' => 'required|string|in:' . implode(',', array_keys($this->getSubtypes('plot'))),
            'summary' => 'nullable|string|max:500',
            'content' => 'nullable|array',
            'content.description' => 'nullable|string',
            'content.objectives' => 'nullable|string',
            'content.stakes' => 'nullable|string',
            'content.progress' => 'nullable|string',
            'content.secrets' => 'nullable|string',
            'involved_character_ids' => 'nullable|array',
            'involved_character_ids.*' => 'uuid|exists:nodes,id',
            'involved_place_ids' => 'nullable|array',
            'involved_place_ids.*' => 'uuid|exists:nodes,id',
            'involved_faction_ids' => 'nullable|array',
            'involved_faction_ids.*' => 'uuid|exists:nodes,id',
            'confidence' => 'required|string|in:' . implode(',', array_keys($this->getConfidenceLevels())),
            'is_secret' => 'boolean',
        ]);

        $metadata = $plot->metadata ?? [];
        if (!empty($validated['involved_character_ids'])) {
            $metadata['involved_character_ids'] = $validated['involved_character_ids'];
        } else {
            unset($metadata['involved_character_ids']);
        }
        if (!empty($validated['involved_place_ids'])) {
            $metadata['involved_place_ids'] = $validated['involved_place_ids'];
        } else {
            unset($metadata['involved_place_ids']);
        }
        if (!empty($validated['involved_faction_ids'])) {
            $metadata['involved_faction_ids'] = $validated['involved_faction_ids'];
        } else {
            unset($metadata['involved_faction_ids']);
        }

        $plot->update([
            'subtype' => $validated['subtype'],
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'summary' => $validated['summary'] ?? null,
            'content' => $validated['content'] ?? [],
            'metadata' => $metadata,
            'confidence' => $validated['confidence'],
            'is_secret' => $validated['is_secret'] ?? false,
        ]);

        // Update character involvement edges
        $plot->outgoingEdges()
            ->where('type', 'involves')
            ->whereHas('targetNode', function ($query) {
                $query->where('type', 'character');
            })
            ->delete();
        if (!empty($validated['involved_character_ids'])) {
            foreach ($validated['involved_character_ids'] as $characterId) {
                $campaign->edges()->create([
                    'source_node_id' => $plot->id,
                    'target_node_id' => $characterId,
                    'type' => 'involves',
                    'label' => 'Involves',
                ]);
            }
        }

        // Update place edges
        $plot->outgoingEdges()->where('type', 'takes_place_in')->delete();
        if (!empty($validated['involved_place_ids'])) {
            foreach ($validated['involved_place_ids'] as $placeId) {
                $campaign->edges()->create([
                    'source_node_id' => $plot->id,
                    'target_node_id' => $placeId,
                    'type' => 'takes_place_in',
                    'label' => 'Takes place in',
                ]);
            }
        }

        // Update faction involvement edges
        $plot->outgoingEdges()
            ->where('type', 'involves')
            ->whereHas('targetNode', function ($query) {
                $query->where('type', 'faction');
            })
            ->delete();
        if (!empty($validated['involved_faction_ids'])) {
            foreach ($validated['involved_faction_ids'] as $factionId) {
                $campaign->edges()->create([
                    'source_node_id' => $plot->id,
                    'target_node_id' => $factionId,
                    'type' => 'involves',
                    'label' => 'Involves',
                ]);
            }
        }

        return redirect()->route('campaigns.plots.show', [
            'campaignSlug' => $campaign->slug,
            'nodeSlug' => $plot->slug,
        ])->with('success', 'Plot updated successfully.');
    }

    public function plotsDestroy(string $campaignSlug, string $nodeSlug)
    {
        $campaign = $this->getCampaign($campaignSlug);

        $plot = $campaign->nodes()
            ->plots()
            ->where('slug', $nodeSlug)
            ->firstOrFail();

        $plot->delete();

        return redirect()->route('campaigns.plots.index', $campaign->slug)
            ->with('success', 'Plot deleted successfully.');
    }

    // Lore & Legends
    public function loreIndex(string $campaignSlug)
    {
        $campaign = $this->getCampaign($campaignSlug);

        $lore = $campaign->nodes()
            ->lore()
            ->orderBy('name')
            ->get();

        return Inertia::render('Lore/Index', [
            'campaign' => $campaign,
            'lore' => $lore,
            'subtypes' => $this->getSubtypes('lore'),
        ]);
    }

    public function loreCreate(string $campaignSlug)
    {
        $campaign = $this->getCampaign($campaignSlug);

        // Get places for origin selection
        $places = $campaign->nodes()
            ->places()
            ->orderBy('name')
            ->get(['id', 'name', 'subtype']);

        // Get religions for related religion selection
        $religions = $campaign->nodes()
            ->religions()
            ->orderBy('name')
            ->get(['id', 'name', 'subtype']);

        return Inertia::render('Lore/Create', [
            'campaign' => $campaign,
            'subtypes' => $this->getSubtypes('lore'),
            'confidenceLevels' => $this->getConfidenceLevels(),
            'places' => $places,
            'religions' => $religions,
        ]);
    }

    public function loreStore(Request $request, string $campaignSlug)
    {
        $campaign = $this->getCampaign($campaignSlug);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subtype' => 'required|string|in:' . implode(',', array_keys($this->getSubtypes('lore'))),
            'summary' => 'nullable|string|max:500',
            'content' => 'nullable|array',
            'content.narrative' => 'nullable|string',
            'content.origin' => 'nullable|string',
            'content.variations' => 'nullable|string',
            'content.truth_level' => 'nullable|string',
            'content.cultural_significance' => 'nullable|string',
            'content.known_by' => 'nullable|string',
            'content.secrets' => 'nullable|string',
            'origin_place_id' => 'nullable|uuid|exists:nodes,id',
            'related_religion_id' => 'nullable|uuid|exists:nodes,id',
            'confidence' => 'required|string|in:' . implode(',', array_keys($this->getConfidenceLevels())),
            'is_secret' => 'boolean',
        ]);

        $metadata = [];
        if (!empty($validated['origin_place_id'])) {
            $metadata['origin_place_id'] = $validated['origin_place_id'];
        }
        if (!empty($validated['related_religion_id'])) {
            $metadata['related_religion_id'] = $validated['related_religion_id'];
        }

        $node = $campaign->nodes()->create([
            'type' => 'lore',
            'subtype' => $validated['subtype'],
            'name' => $validated['name'],
            'summary' => $validated['summary'] ?? null,
            'content' => $validated['content'] ?? [],
            'metadata' => $metadata,
            'confidence' => $validated['confidence'],
            'is_secret' => $validated['is_secret'] ?? false,
        ]);

        // Create edge to origin place if specified
        if (!empty($validated['origin_place_id'])) {
            $campaign->edges()->create([
                'source_node_id' => $node->id,
                'target_node_id' => $validated['origin_place_id'],
                'type' => 'originates_from',
                'label' => 'Originates from',
            ]);
        }

        // Create edge to related religion if specified
        if (!empty($validated['related_religion_id'])) {
            $campaign->edges()->create([
                'source_node_id' => $node->id,
                'target_node_id' => $validated['related_religion_id'],
                'type' => 'related_to',
                'label' => 'Related to',
            ]);
        }

        return redirect()->route('campaigns.lore.show', [
            'campaignSlug' => $campaign->slug,
            'nodeSlug' => $node->slug,
        ])->with('success', 'Lore created successfully.');
    }

    public function loreShow(string $campaignSlug, string $nodeSlug)
    {
        $campaign = $this->getCampaign($campaignSlug);

        $lore = $campaign->nodes()
            ->lore()
            ->where('slug', $nodeSlug)
            ->with(['tags', 'outgoingEdges.targetNode', 'incomingEdges.sourceNode'])
            ->firstOrFail();

        // Get origin place
        $originEdge = $lore->outgoingEdges()
            ->where('type', 'originates_from')
            ->with('targetNode')
            ->first();
        $originPlace = $originEdge?->targetNode;

        // Get related religion
        $religionEdge = $lore->outgoingEdges()
            ->where('type', 'related_to')
            ->with('targetNode')
            ->first();
        $relatedReligion = $religionEdge?->targetNode;

        return Inertia::render('Lore/Show', [
            'campaign' => $campaign,
            'lore' => $lore,
            'originPlace' => $originPlace,
            'relatedReligion' => $relatedReligion,
        ]);
    }

    public function loreEdit(string $campaignSlug, string $nodeSlug)
    {
        $campaign = $this->getCampaign($campaignSlug);

        $lore = $campaign->nodes()
            ->lore()
            ->where('slug', $nodeSlug)
            ->firstOrFail();

        $places = $campaign->nodes()
            ->places()
            ->orderBy('name')
            ->get(['id', 'name', 'subtype']);

        $religions = $campaign->nodes()
            ->religions()
            ->orderBy('name')
            ->get(['id', 'name', 'subtype']);

        // Get current origin place from edges
        $currentOriginPlace = $lore->outgoingEdges()
            ->where('type', 'originates_from')
            ->first();

        // Get current related religion from edges
        $currentRelatedReligion = $lore->outgoingEdges()
            ->where('type', 'related_to')
            ->first();

        return Inertia::render('Lore/Edit', [
            'campaign' => $campaign,
            'lore' => $lore,
            'subtypes' => $this->getSubtypes('lore'),
            'confidenceLevels' => $this->getConfidenceLevels(),
            'places' => $places,
            'religions' => $religions,
            'currentOriginPlaceId' => $currentOriginPlace?->target_node_id,
            'currentRelatedReligionId' => $currentRelatedReligion?->target_node_id,
        ]);
    }

    public function loreUpdate(Request $request, string $campaignSlug, string $nodeSlug)
    {
        $campaign = $this->getCampaign($campaignSlug);

        $lore = $campaign->nodes()
            ->lore()
            ->where('slug', $nodeSlug)
            ->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subtype' => 'required|string|in:' . implode(',', array_keys($this->getSubtypes('lore'))),
            'summary' => 'nullable|string|max:500',
            'content' => 'nullable|array',
            'content.narrative' => 'nullable|string',
            'content.origin' => 'nullable|string',
            'content.variations' => 'nullable|string',
            'content.truth_level' => 'nullable|string',
            'content.cultural_significance' => 'nullable|string',
            'content.known_by' => 'nullable|string',
            'content.secrets' => 'nullable|string',
            'origin_place_id' => 'nullable|uuid|exists:nodes,id',
            'related_religion_id' => 'nullable|uuid|exists:nodes,id',
            'confidence' => 'required|string|in:' . implode(',', array_keys($this->getConfidenceLevels())),
            'is_secret' => 'boolean',
        ]);

        $metadata = $lore->metadata ?? [];
        if (!empty($validated['origin_place_id'])) {
            $metadata['origin_place_id'] = $validated['origin_place_id'];
        } else {
            unset($metadata['origin_place_id']);
        }
        if (!empty($validated['related_religion_id'])) {
            $metadata['related_religion_id'] = $validated['related_religion_id'];
        } else {
            unset($metadata['related_religion_id']);
        }

        $lore->update([
            'subtype' => $validated['subtype'],
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'summary' => $validated['summary'] ?? null,
            'content' => $validated['content'] ?? [],
            'metadata' => $metadata,
            'confidence' => $validated['confidence'],
            'is_secret' => $validated['is_secret'] ?? false,
        ]);

        // Update origin place edge
        $lore->outgoingEdges()->where('type', 'originates_from')->delete();
        if (!empty($validated['origin_place_id'])) {
            $campaign->edges()->create([
                'source_node_id' => $lore->id,
                'target_node_id' => $validated['origin_place_id'],
                'type' => 'originates_from',
                'label' => 'Originates from',
            ]);
        }

        // Update related religion edge
        $lore->outgoingEdges()->where('type', 'related_to')->delete();
        if (!empty($validated['related_religion_id'])) {
            $campaign->edges()->create([
                'source_node_id' => $lore->id,
                'target_node_id' => $validated['related_religion_id'],
                'type' => 'related_to',
                'label' => 'Related to',
            ]);
        }

        return redirect()->route('campaigns.lore.show', [
            'campaignSlug' => $campaign->slug,
            'nodeSlug' => $lore->slug,
        ])->with('success', 'Lore updated successfully.');
    }

    public function loreDestroy(string $campaignSlug, string $nodeSlug)
    {
        $campaign = $this->getCampaign($campaignSlug);

        $lore = $campaign->nodes()
            ->lore()
            ->where('slug', $nodeSlug)
            ->firstOrFail();

        $lore->delete();

        return redirect()->route('campaigns.lore.index', $campaign->slug)
            ->with('success', 'Lore deleted successfully.');
    }

    // Religions
    public function religionsIndex(string $campaignSlug)
    {
        $campaign = $this->getCampaign($campaignSlug);

        $religions = $campaign->nodes()
            ->religions()
            ->orderBy('name')
            ->get();

        return Inertia::render('Religions/Index', [
            'campaign' => $campaign,
            'religions' => $religions,
            'subtypes' => $this->getSubtypes('religion'),
        ]);
    }

    public function religionsCreate(string $campaignSlug)
    {
        $campaign = $this->getCampaign($campaignSlug);

        // Get places for headquarters/holy site selection
        $places = $campaign->nodes()
            ->places()
            ->orderBy('name')
            ->get(['id', 'name', 'subtype']);

        // Get characters for founder selection
        $characters = $campaign->nodes()
            ->characters()
            ->orderBy('name')
            ->get(['id', 'name', 'subtype']);

        return Inertia::render('Religions/Create', [
            'campaign' => $campaign,
            'subtypes' => $this->getSubtypes('religion'),
            'confidenceLevels' => $this->getConfidenceLevels(),
            'places' => $places,
            'characters' => $characters,
        ]);
    }

    public function religionsStore(Request $request, string $campaignSlug)
    {
        $campaign = $this->getCampaign($campaignSlug);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subtype' => 'required|string|in:' . implode(',', array_keys($this->getSubtypes('religion'))),
            'summary' => 'nullable|string|max:500',
            'content' => 'nullable|array',
            'content.description' => 'nullable|string',
            'content.beliefs' => 'nullable|string',
            'content.practices' => 'nullable|string',
            'content.hierarchy' => 'nullable|string',
            'content.symbols' => 'nullable|string',
            'content.holy_sites' => 'nullable|string',
            'content.history' => 'nullable|string',
            'content.relationships' => 'nullable|string',
            'content.taboos' => 'nullable|string',
            'content.afterlife' => 'nullable|string',
            'content.secrets' => 'nullable|string',
            'headquarters_id' => 'nullable|uuid|exists:nodes,id',
            'founded_by_id' => 'nullable|uuid|exists:nodes,id',
            'confidence' => 'required|string|in:' . implode(',', array_keys($this->getConfidenceLevels())),
            'is_secret' => 'boolean',
        ]);

        $metadata = [];
        if (!empty($validated['headquarters_id'])) {
            $metadata['headquarters_id'] = $validated['headquarters_id'];
        }
        if (!empty($validated['founded_by_id'])) {
            $metadata['founded_by_id'] = $validated['founded_by_id'];
        }

        $node = $campaign->nodes()->create([
            'type' => 'religion',
            'subtype' => $validated['subtype'],
            'name' => $validated['name'],
            'summary' => $validated['summary'] ?? null,
            'content' => $validated['content'] ?? [],
            'metadata' => $metadata,
            'confidence' => $validated['confidence'],
            'is_secret' => $validated['is_secret'] ?? false,
        ]);

        // Create edge to headquarters if specified
        if (!empty($validated['headquarters_id'])) {
            $campaign->edges()->create([
                'source_node_id' => $node->id,
                'target_node_id' => $validated['headquarters_id'],
                'type' => 'headquartered_in',
                'label' => 'Holy site at',
            ]);
        }

        // Create edge to founder if specified
        if (!empty($validated['founded_by_id'])) {
            $campaign->edges()->create([
                'source_node_id' => $node->id,
                'target_node_id' => $validated['founded_by_id'],
                'type' => 'founded_by',
                'label' => 'Founded by',
            ]);
        }

        return redirect()->route('campaigns.religions.show', [
            'campaignSlug' => $campaign->slug,
            'nodeSlug' => $node->slug,
        ])->with('success', 'Religion created successfully.');
    }

    public function religionsShow(string $campaignSlug, string $nodeSlug)
    {
        $campaign = $this->getCampaign($campaignSlug);

        $religion = $campaign->nodes()
            ->religions()
            ->where('slug', $nodeSlug)
            ->with(['tags', 'outgoingEdges.targetNode', 'incomingEdges.sourceNode'])
            ->firstOrFail();

        // Get headquarters/holy site
        $headquartersEdge = $religion->outgoingEdges()
            ->where('type', 'headquartered_in')
            ->with('targetNode')
            ->first();
        $headquarters = $headquartersEdge?->targetNode;

        // Get founder
        $founderEdge = $religion->outgoingEdges()
            ->where('type', 'founded_by')
            ->with('targetNode')
            ->first();
        $founder = $founderEdge?->targetNode;

        // Get related lore
        $relatedLore = $campaign->nodes()
            ->lore()
            ->whereHas('outgoingEdges', function ($query) use ($religion) {
                $query->where('target_node_id', $religion->id)
                    ->where('type', 'related_to');
            })
            ->get();

        return Inertia::render('Religions/Show', [
            'campaign' => $campaign,
            'religion' => $religion,
            'headquarters' => $headquarters,
            'founder' => $founder,
            'relatedLore' => $relatedLore,
        ]);
    }

    public function religionsEdit(string $campaignSlug, string $nodeSlug)
    {
        $campaign = $this->getCampaign($campaignSlug);

        $religion = $campaign->nodes()
            ->religions()
            ->where('slug', $nodeSlug)
            ->firstOrFail();

        $places = $campaign->nodes()
            ->places()
            ->orderBy('name')
            ->get(['id', 'name', 'subtype']);

        $characters = $campaign->nodes()
            ->characters()
            ->orderBy('name')
            ->get(['id', 'name', 'subtype']);

        // Get current headquarters from edges
        $currentHeadquarters = $religion->outgoingEdges()
            ->where('type', 'headquartered_in')
            ->first();

        // Get current founder from edges
        $currentFounder = $religion->outgoingEdges()
            ->where('type', 'founded_by')
            ->first();

        return Inertia::render('Religions/Edit', [
            'campaign' => $campaign,
            'religion' => $religion,
            'subtypes' => $this->getSubtypes('religion'),
            'confidenceLevels' => $this->getConfidenceLevels(),
            'places' => $places,
            'characters' => $characters,
            'currentHeadquartersId' => $currentHeadquarters?->target_node_id,
            'currentFounderId' => $currentFounder?->target_node_id,
        ]);
    }

    public function religionsUpdate(Request $request, string $campaignSlug, string $nodeSlug)
    {
        $campaign = $this->getCampaign($campaignSlug);

        $religion = $campaign->nodes()
            ->religions()
            ->where('slug', $nodeSlug)
            ->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subtype' => 'required|string|in:' . implode(',', array_keys($this->getSubtypes('religion'))),
            'summary' => 'nullable|string|max:500',
            'content' => 'nullable|array',
            'content.description' => 'nullable|string',
            'content.beliefs' => 'nullable|string',
            'content.practices' => 'nullable|string',
            'content.hierarchy' => 'nullable|string',
            'content.symbols' => 'nullable|string',
            'content.holy_sites' => 'nullable|string',
            'content.history' => 'nullable|string',
            'content.relationships' => 'nullable|string',
            'content.taboos' => 'nullable|string',
            'content.afterlife' => 'nullable|string',
            'content.secrets' => 'nullable|string',
            'headquarters_id' => 'nullable|uuid|exists:nodes,id',
            'founded_by_id' => 'nullable|uuid|exists:nodes,id',
            'confidence' => 'required|string|in:' . implode(',', array_keys($this->getConfidenceLevels())),
            'is_secret' => 'boolean',
        ]);

        $metadata = $religion->metadata ?? [];
        if (!empty($validated['headquarters_id'])) {
            $metadata['headquarters_id'] = $validated['headquarters_id'];
        } else {
            unset($metadata['headquarters_id']);
        }
        if (!empty($validated['founded_by_id'])) {
            $metadata['founded_by_id'] = $validated['founded_by_id'];
        } else {
            unset($metadata['founded_by_id']);
        }

        $religion->update([
            'subtype' => $validated['subtype'],
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'summary' => $validated['summary'] ?? null,
            'content' => $validated['content'] ?? [],
            'metadata' => $metadata,
            'confidence' => $validated['confidence'],
            'is_secret' => $validated['is_secret'] ?? false,
        ]);

        // Update headquarters edge
        $religion->outgoingEdges()->where('type', 'headquartered_in')->delete();
        if (!empty($validated['headquarters_id'])) {
            $campaign->edges()->create([
                'source_node_id' => $religion->id,
                'target_node_id' => $validated['headquarters_id'],
                'type' => 'headquartered_in',
                'label' => 'Holy site at',
            ]);
        }

        // Update founder edge
        $religion->outgoingEdges()->where('type', 'founded_by')->delete();
        if (!empty($validated['founded_by_id'])) {
            $campaign->edges()->create([
                'source_node_id' => $religion->id,
                'target_node_id' => $validated['founded_by_id'],
                'type' => 'founded_by',
                'label' => 'Founded by',
            ]);
        }

        return redirect()->route('campaigns.religions.show', [
            'campaignSlug' => $campaign->slug,
            'nodeSlug' => $religion->slug,
        ])->with('success', 'Religion updated successfully.');
    }

    public function religionsDestroy(string $campaignSlug, string $nodeSlug)
    {
        $campaign = $this->getCampaign($campaignSlug);

        $religion = $campaign->nodes()
            ->religions()
            ->where('slug', $nodeSlug)
            ->firstOrFail();

        $religion->delete();

        return redirect()->route('campaigns.religions.index', $campaign->slug)
            ->with('success', 'Religion deleted successfully.');
    }

    // Magic Systems
    public function magicIndex(string $campaignSlug)
    {
        $campaign = $this->getCampaign($campaignSlug);

        $magicSystems = $campaign->nodes()
            ->magicSystems()
            ->orderBy('name')
            ->get();

        return Inertia::render('Magic/Index', [
            'campaign' => $campaign,
            'magicSystems' => $magicSystems,
            'subtypes' => $this->getSubtypes('magic_system'),
        ]);
    }

    public function magicCreate(string $campaignSlug)
    {
        $campaign = $this->getCampaign($campaignSlug);

        // Get places for taught_at selection
        $places = $campaign->nodes()
            ->places()
            ->orderBy('name')
            ->get(['id', 'name', 'subtype']);

        // Get factions for regulated_by selection
        $factions = $campaign->nodes()
            ->factions()
            ->orderBy('name')
            ->get(['id', 'name', 'subtype']);

        return Inertia::render('Magic/Create', [
            'campaign' => $campaign,
            'subtypes' => $this->getSubtypes('magic_system'),
            'confidenceLevels' => $this->getConfidenceLevels(),
            'places' => $places,
            'factions' => $factions,
        ]);
    }

    public function magicStore(Request $request, string $campaignSlug)
    {
        $campaign = $this->getCampaign($campaignSlug);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subtype' => 'required|string|in:' . implode(',', array_keys($this->getSubtypes('magic_system'))),
            'summary' => 'nullable|string|max:500',
            'content' => 'nullable|array',
            'content.description' => 'nullable|string',
            'content.source' => 'nullable|string',
            'content.practitioners' => 'nullable|string',
            'content.abilities' => 'nullable|string',
            'content.limitations' => 'nullable|string',
            'content.components' => 'nullable|string',
            'content.learning' => 'nullable|string',
            'content.history' => 'nullable|string',
            'content.social_perception' => 'nullable|string',
            'content.secrets' => 'nullable|string',
            'taught_at_id' => 'nullable|uuid|exists:nodes,id',
            'regulated_by_id' => 'nullable|uuid|exists:nodes,id',
            'confidence' => 'required|string|in:' . implode(',', array_keys($this->getConfidenceLevels())),
            'is_secret' => 'boolean',
        ]);

        $metadata = [];
        if (!empty($validated['taught_at_id'])) {
            $metadata['taught_at_id'] = $validated['taught_at_id'];
        }
        if (!empty($validated['regulated_by_id'])) {
            $metadata['regulated_by_id'] = $validated['regulated_by_id'];
        }

        $node = $campaign->nodes()->create([
            'type' => 'magic_system',
            'subtype' => $validated['subtype'],
            'name' => $validated['name'],
            'summary' => $validated['summary'] ?? null,
            'content' => $validated['content'] ?? [],
            'metadata' => $metadata,
            'confidence' => $validated['confidence'],
            'is_secret' => $validated['is_secret'] ?? false,
        ]);

        // Create edge to taught_at if specified
        if (!empty($validated['taught_at_id'])) {
            $campaign->edges()->create([
                'source_node_id' => $node->id,
                'target_node_id' => $validated['taught_at_id'],
                'type' => 'taught_at',
                'label' => 'Taught at',
            ]);
        }

        // Create edge to regulated_by if specified
        if (!empty($validated['regulated_by_id'])) {
            $campaign->edges()->create([
                'source_node_id' => $node->id,
                'target_node_id' => $validated['regulated_by_id'],
                'type' => 'regulated_by',
                'label' => 'Regulated by',
            ]);
        }

        return redirect()->route('campaigns.magic.show', [
            'campaignSlug' => $campaign->slug,
            'nodeSlug' => $node->slug,
        ])->with('success', 'Magic system created successfully.');
    }

    public function magicShow(string $campaignSlug, string $nodeSlug)
    {
        $campaign = $this->getCampaign($campaignSlug);

        $magicSystem = $campaign->nodes()
            ->magicSystems()
            ->where('slug', $nodeSlug)
            ->with(['tags', 'outgoingEdges.targetNode', 'incomingEdges.sourceNode'])
            ->firstOrFail();

        // Get taught_at place
        $taughtAtEdge = $magicSystem->outgoingEdges()
            ->where('type', 'taught_at')
            ->with('targetNode')
            ->first();
        $taughtAt = $taughtAtEdge?->targetNode;

        // Get regulated_by faction
        $regulatedByEdge = $magicSystem->outgoingEdges()
            ->where('type', 'regulated_by')
            ->with('targetNode')
            ->first();
        $regulatedBy = $regulatedByEdge?->targetNode;

        return Inertia::render('Magic/Show', [
            'campaign' => $campaign,
            'magicSystem' => $magicSystem,
            'taughtAt' => $taughtAt,
            'regulatedBy' => $regulatedBy,
        ]);
    }

    public function magicEdit(string $campaignSlug, string $nodeSlug)
    {
        $campaign = $this->getCampaign($campaignSlug);

        $magicSystem = $campaign->nodes()
            ->magicSystems()
            ->where('slug', $nodeSlug)
            ->firstOrFail();

        $places = $campaign->nodes()
            ->places()
            ->orderBy('name')
            ->get(['id', 'name', 'subtype']);

        $factions = $campaign->nodes()
            ->factions()
            ->orderBy('name')
            ->get(['id', 'name', 'subtype']);

        // Get current taught_at from edges
        $currentTaughtAt = $magicSystem->outgoingEdges()
            ->where('type', 'taught_at')
            ->first();

        // Get current regulated_by from edges
        $currentRegulatedBy = $magicSystem->outgoingEdges()
            ->where('type', 'regulated_by')
            ->first();

        return Inertia::render('Magic/Edit', [
            'campaign' => $campaign,
            'magicSystem' => $magicSystem,
            'subtypes' => $this->getSubtypes('magic_system'),
            'confidenceLevels' => $this->getConfidenceLevels(),
            'places' => $places,
            'factions' => $factions,
            'currentTaughtAtId' => $currentTaughtAt?->target_node_id,
            'currentRegulatedById' => $currentRegulatedBy?->target_node_id,
        ]);
    }

    public function magicUpdate(Request $request, string $campaignSlug, string $nodeSlug)
    {
        $campaign = $this->getCampaign($campaignSlug);

        $magicSystem = $campaign->nodes()
            ->magicSystems()
            ->where('slug', $nodeSlug)
            ->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subtype' => 'required|string|in:' . implode(',', array_keys($this->getSubtypes('magic_system'))),
            'summary' => 'nullable|string|max:500',
            'content' => 'nullable|array',
            'content.description' => 'nullable|string',
            'content.source' => 'nullable|string',
            'content.practitioners' => 'nullable|string',
            'content.abilities' => 'nullable|string',
            'content.limitations' => 'nullable|string',
            'content.components' => 'nullable|string',
            'content.learning' => 'nullable|string',
            'content.history' => 'nullable|string',
            'content.social_perception' => 'nullable|string',
            'content.secrets' => 'nullable|string',
            'taught_at_id' => 'nullable|uuid|exists:nodes,id',
            'regulated_by_id' => 'nullable|uuid|exists:nodes,id',
            'confidence' => 'required|string|in:' . implode(',', array_keys($this->getConfidenceLevels())),
            'is_secret' => 'boolean',
        ]);

        $metadata = $magicSystem->metadata ?? [];
        if (!empty($validated['taught_at_id'])) {
            $metadata['taught_at_id'] = $validated['taught_at_id'];
        } else {
            unset($metadata['taught_at_id']);
        }
        if (!empty($validated['regulated_by_id'])) {
            $metadata['regulated_by_id'] = $validated['regulated_by_id'];
        } else {
            unset($metadata['regulated_by_id']);
        }

        $magicSystem->update([
            'subtype' => $validated['subtype'],
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'summary' => $validated['summary'] ?? null,
            'content' => $validated['content'] ?? [],
            'metadata' => $metadata,
            'confidence' => $validated['confidence'],
            'is_secret' => $validated['is_secret'] ?? false,
        ]);

        // Update taught_at edge
        $magicSystem->outgoingEdges()->where('type', 'taught_at')->delete();
        if (!empty($validated['taught_at_id'])) {
            $campaign->edges()->create([
                'source_node_id' => $magicSystem->id,
                'target_node_id' => $validated['taught_at_id'],
                'type' => 'taught_at',
                'label' => 'Taught at',
            ]);
        }

        // Update regulated_by edge
        $magicSystem->outgoingEdges()->where('type', 'regulated_by')->delete();
        if (!empty($validated['regulated_by_id'])) {
            $campaign->edges()->create([
                'source_node_id' => $magicSystem->id,
                'target_node_id' => $validated['regulated_by_id'],
                'type' => 'regulated_by',
                'label' => 'Regulated by',
            ]);
        }

        return redirect()->route('campaigns.magic.show', [
            'campaignSlug' => $campaign->slug,
            'nodeSlug' => $magicSystem->slug,
        ])->with('success', 'Magic system updated successfully.');
    }

    public function magicDestroy(string $campaignSlug, string $nodeSlug)
    {
        $campaign = $this->getCampaign($campaignSlug);

        $magicSystem = $campaign->nodes()
            ->magicSystems()
            ->where('slug', $nodeSlug)
            ->firstOrFail();

        $magicSystem->delete();

        return redirect()->route('campaigns.magic.index', $campaign->slug)
            ->with('success', 'Magic system deleted successfully.');
    }

    /**
     * Store a featured image for a node.
     */
    private function storeFeaturedImage(Node $node, $file): Media
    {
        $path = $file->store("campaigns/{$node->campaign_id}/nodes/{$node->id}", config('filesystems.default'));

        return $node->media()->create([
            'collection' => 'featured',
            'filename' => $file->getClientOriginalName(),
            'path' => $path,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'metadata' => [
                'original_name' => $file->getClientOriginalName(),
            ],
        ]);
    }

    /**
     * Store a gallery image for a node.
     */
    private function storeGalleryImage(Node $node, $file, int $order): Media
    {
        $path = $file->store("campaigns/{$node->campaign_id}/nodes/{$node->id}/gallery", config('filesystems.default'));

        return $node->media()->create([
            'collection' => 'gallery',
            'filename' => $file->getClientOriginalName(),
            'path' => $path,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'order' => $order,
            'metadata' => [
                'original_name' => $file->getClientOriginalName(),
            ],
        ]);
    }

    /**
     * Delete an image from a node.
     */
    public function destroyImage(Request $request, string $campaignSlug, string $nodeSlug)
    {
        $campaign = $this->getCampaign($campaignSlug);
        $node = $campaign->nodes()->where('slug', $nodeSlug)->firstOrFail();

        $mediaId = $request->input('media_id');
        $media = $node->media()->findOrFail($mediaId);

        // Delete file from storage
        Storage::delete($media->path);

        // Delete database record
        $media->delete();

        return back()->with('success', 'Image deleted successfully');
    }
}
