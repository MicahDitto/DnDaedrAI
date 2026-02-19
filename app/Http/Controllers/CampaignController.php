<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class CampaignController extends Controller
{
    public function index(): Response
    {
        $campaigns = Auth::user()->campaigns()->latest()->get();

        return Inertia::render('Campaigns/Index', [
            'campaigns' => $campaigns,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Campaigns/Create', [
            'genres' => $this->getGenreOptions(),
            'ruleSystems' => $this->getRuleSystemOptions(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'genre' => 'nullable|string|max:50',
            'rule_system' => 'nullable|string|max:50',
            'player_count' => 'nullable|integer|min:1|max:20',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['slug'] = Str::slug($validated['name']);

        // Ensure unique slug for user
        $baseSlug = $validated['slug'];
        $counter = 1;
        while (Campaign::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = $baseSlug . '-' . $counter++;
        }

        $campaign = Campaign::create($validated);

        return redirect()->route('campaigns.show', $campaign->slug)
            ->with('success', 'Campaign created successfully!');
    }

    public function show(string $slug): Response
    {
        $campaign = Auth::user()->campaigns()
            ->where('slug', $slug)
            ->firstOrFail();

        $campaigns = Auth::user()->campaigns()->get(['id', 'name', 'slug']);

        // Get counts for dashboard
        $stats = [
            'characters' => $campaign->nodes()->where('type', 'character')->count(),
            'places' => $campaign->nodes()->where('type', 'place')->count(),
            'plots' => $campaign->nodes()->where('type', 'plot')->count(),
            'sessions' => $campaign->gameSessions()->count(),
        ];

        // Get recent nodes
        $recentNodes = $campaign->nodes()
            ->latest()
            ->take(5)
            ->get(['id', 'type', 'name', 'summary', 'created_at']);

        // Get upcoming/recent sessions
        $sessions = $campaign->gameSessions()
            ->orderBy('number', 'desc')
            ->take(5)
            ->get();

        return Inertia::render('Campaigns/Show', [
            'campaign' => $campaign,
            'campaigns' => $campaigns,
            'stats' => $stats,
            'recentNodes' => $recentNodes,
            'sessions' => $sessions,
        ]);
    }

    public function edit(string $slug): Response
    {
        $campaign = Auth::user()->campaigns()
            ->where('slug', $slug)
            ->firstOrFail();

        return Inertia::render('Campaigns/Edit', [
            'campaign' => $campaign,
            'genres' => $this->getGenreOptions(),
            'ruleSystems' => $this->getRuleSystemOptions(),
        ]);
    }

    public function update(Request $request, string $slug)
    {
        $campaign = Auth::user()->campaigns()
            ->where('slug', $slug)
            ->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'genre' => 'nullable|string|max:50',
            'rule_system' => 'nullable|string|max:50',
            'player_count' => 'nullable|integer|min:1|max:20',
            'status' => 'nullable|string|in:setup,active,paused,completed',
            'tone_settings' => 'nullable|array',
            'settings' => 'nullable|array',
        ]);

        $campaign->update($validated);

        return redirect()->route('campaigns.show', $campaign->slug)
            ->with('success', 'Campaign updated successfully!');
    }

    public function destroy(string $slug)
    {
        $campaign = Auth::user()->campaigns()
            ->where('slug', $slug)
            ->firstOrFail();

        $campaign->delete();

        return redirect()->route('campaigns.index')
            ->with('success', 'Campaign deleted successfully!');
    }

    private function getGenreOptions(): array
    {
        return [
            'high_fantasy' => 'High Fantasy',
            'dark_fantasy' => 'Dark Fantasy',
            'low_fantasy' => 'Low Fantasy',
            'sword_and_sorcery' => 'Sword & Sorcery',
            'grimdark' => 'Grimdark',
            'comedic' => 'Comedic',
            'political_intrigue' => 'Political Intrigue',
            'horror' => 'Horror',
            'mystery' => 'Mystery',
            'exploration' => 'Exploration',
            'other' => 'Other',
        ];
    }

    private function getRuleSystemOptions(): array
    {
        return [
            '5e' => 'D&D 5th Edition',
            '5e_2024' => 'D&D 5e (2024)',
            'pathfinder_2e' => 'Pathfinder 2e',
            'pathfinder_1e' => 'Pathfinder 1e',
            '3.5e' => 'D&D 3.5 Edition',
            'osr' => 'OSR / Old School',
            'homebrew' => 'Homebrew System',
            'other' => 'Other',
        ];
    }
}
