<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\GameSession;
use App\Models\Node;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    public function search(Request $request, string $campaignSlug): JsonResponse
    {
        $campaign = Campaign::where('user_id', Auth::id())
            ->where('slug', $campaignSlug)
            ->firstOrFail();

        $query = $request->input('q', '');
        $type = $request->input('type'); // Optional filter by type

        if (strlen($query) < 2) {
            return response()->json([
                'results' => [],
                'total' => 0,
            ]);
        }

        $results = [];

        // Search Nodes (characters, places, items, factions, plots)
        $nodeQuery = $campaign->nodes()
            ->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                    ->orWhere('summary', 'LIKE', "%{$query}%");
            });

        if ($type && in_array($type, ['character', 'place', 'item', 'faction', 'plot'])) {
            $nodeQuery->where('type', $type);
        }

        $nodes = $nodeQuery
            ->orderBy('name')
            ->limit(20)
            ->get(['id', 'type', 'subtype', 'name', 'slug', 'summary', 'is_secret']);

        foreach ($nodes as $node) {
            $results[] = [
                'id' => $node->id,
                'type' => $node->type,
                'subtype' => $node->subtype,
                'name' => $node->name,
                'slug' => $node->slug,
                'summary' => $node->summary,
                'is_secret' => $node->is_secret,
                'url' => $this->getNodeUrl($campaignSlug, $node),
            ];
        }

        // Search Sessions (if no type filter or type is 'session')
        if (!$type || $type === 'session') {
            $sessions = $campaign->sessions()
                ->where(function ($q) use ($query) {
                    $q->where('title', 'LIKE', "%{$query}%")
                        ->orWhere('notes', 'LIKE', "%{$query}%")
                        ->orWhere('recap', 'LIKE', "%{$query}%");
                })
                ->orderBy('number', 'desc')
                ->limit(10)
                ->get(['id', 'number', 'title', 'status']);

            foreach ($sessions as $session) {
                $results[] = [
                    'id' => $session->id,
                    'type' => 'session',
                    'subtype' => $session->status,
                    'name' => $session->title ?? "Session {$session->number}",
                    'slug' => (string) $session->number,
                    'summary' => null,
                    'is_secret' => false,
                    'url' => route('campaigns.sessions.show', [$campaignSlug, $session->number]),
                ];
            }
        }

        // Sort results: exact matches first, then by name
        usort($results, function ($a, $b) use ($query) {
            $aExact = stripos($a['name'], $query) === 0 ? 0 : 1;
            $bExact = stripos($b['name'], $query) === 0 ? 0 : 1;

            if ($aExact !== $bExact) {
                return $aExact - $bExact;
            }

            return strcasecmp($a['name'], $b['name']);
        });

        // Group results by type for better display
        $grouped = [
            'characters' => [],
            'places' => [],
            'items' => [],
            'factions' => [],
            'plots' => [],
            'sessions' => [],
        ];

        foreach ($results as $result) {
            $key = $result['type'] . 's'; // character -> characters
            if (isset($grouped[$key])) {
                $grouped[$key][] = $result;
            }
        }

        // Remove empty groups
        $grouped = array_filter($grouped, fn($items) => count($items) > 0);

        return response()->json([
            'results' => $grouped,
            'total' => count($results),
            'query' => $query,
        ]);
    }

    protected function getNodeUrl(string $campaignSlug, Node $node): string
    {
        $routeName = match ($node->type) {
            'character' => 'campaigns.characters.show',
            'place' => 'campaigns.places.show',
            'item' => 'campaigns.items.show',
            'faction' => 'campaigns.factions.show',
            'plot' => 'campaigns.plots.show',
            default => 'campaigns.show',
        };

        if ($routeName === 'campaigns.show') {
            return route($routeName, $campaignSlug);
        }

        return route($routeName, [$campaignSlug, $node->slug]);
    }
}
