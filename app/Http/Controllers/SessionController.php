<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\GameSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class SessionController extends Controller
{
    protected function getCampaign(string $campaignSlug): Campaign
    {
        return Campaign::where('user_id', Auth::id())
            ->where('slug', $campaignSlug)
            ->firstOrFail();
    }

    protected function getStatusOptions(): array
    {
        return [
            'planned' => 'Planned',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
        ];
    }

    public function index(string $campaignSlug)
    {
        $campaign = $this->getCampaign($campaignSlug);

        $sessions = $campaign->gameSessions()
            ->orderBy('number')
            ->get();

        return Inertia::render('Sessions/Index', [
            'campaign' => $campaign,
            'sessions' => $sessions,
            'statusOptions' => $this->getStatusOptions(),
        ]);
    }

    public function create(string $campaignSlug)
    {
        $campaign = $this->getCampaign($campaignSlug);

        // Calculate next session number
        $nextNumber = $campaign->gameSessions()->max('number') + 1;
        // If no sessions exist and Session 0 hasn't been created, default to 1
        if ($nextNumber === 1 && !$campaign->gameSessions()->where('number', 0)->exists()) {
            $nextNumber = 1;
        }

        return Inertia::render('Sessions/Create', [
            'campaign' => $campaign,
            'nextNumber' => $nextNumber,
            'statusOptions' => $this->getStatusOptions(),
        ]);
    }

    public function store(Request $request, string $campaignSlug)
    {
        $campaign = $this->getCampaign($campaignSlug);

        $validated = $request->validate([
            'number' => 'required|integer|min:0',
            'title' => 'nullable|string|max:255',
            'status' => 'required|string|in:' . implode(',', array_keys($this->getStatusOptions())),
            'planned_date' => 'nullable|date',
            'actual_date' => 'nullable|date',
            'plan' => 'nullable|array',
            'plan.objectives' => 'nullable|string',
            'plan.encounters' => 'nullable|string',
            'plan.npcs' => 'nullable|string',
            'plan.locations' => 'nullable|string',
            'notes' => 'nullable|string',
            'recap' => 'nullable|string',
            'outcomes' => 'nullable|array',
            'outcomes.summary' => 'nullable|string',
            'outcomes.decisions' => 'nullable|string',
            'outcomes.consequences' => 'nullable|string',
        ]);

        // Check for duplicate session number
        if ($campaign->gameSessions()->where('number', $validated['number'])->exists()) {
            return back()->withErrors(['number' => 'A session with this number already exists.']);
        }

        $session = $campaign->gameSessions()->create([
            'number' => $validated['number'],
            'title' => $validated['title'] ?? null,
            'status' => $validated['status'],
            'planned_date' => $validated['planned_date'] ?? null,
            'actual_date' => $validated['actual_date'] ?? null,
            'plan' => $validated['plan'] ?? [],
            'notes' => $validated['notes'] ?? null,
            'recap' => $validated['recap'] ?? null,
            'outcomes' => $validated['outcomes'] ?? [],
        ]);

        return redirect()->route('campaigns.sessions.show', [
            'campaignSlug' => $campaign->slug,
            'number' => $session->number,
        ])->with('success', 'Session created successfully.');
    }

    public function show(string $campaignSlug, int $number)
    {
        $campaign = $this->getCampaign($campaignSlug);

        $session = $campaign->gameSessions()
            ->where('number', $number)
            ->firstOrFail();

        // Get previous and next sessions for navigation
        $previousSession = $campaign->gameSessions()
            ->where('number', '<', $number)
            ->orderBy('number', 'desc')
            ->first();

        $nextSession = $campaign->gameSessions()
            ->where('number', '>', $number)
            ->orderBy('number')
            ->first();

        return Inertia::render('Sessions/Show', [
            'campaign' => $campaign,
            'session' => $session,
            'previousSession' => $previousSession,
            'nextSession' => $nextSession,
            'statusOptions' => $this->getStatusOptions(),
        ]);
    }

    public function edit(string $campaignSlug, int $number)
    {
        $campaign = $this->getCampaign($campaignSlug);

        $session = $campaign->gameSessions()
            ->where('number', $number)
            ->firstOrFail();

        return Inertia::render('Sessions/Edit', [
            'campaign' => $campaign,
            'session' => $session,
            'statusOptions' => $this->getStatusOptions(),
        ]);
    }

    public function update(Request $request, string $campaignSlug, int $number)
    {
        $campaign = $this->getCampaign($campaignSlug);

        $session = $campaign->gameSessions()
            ->where('number', $number)
            ->firstOrFail();

        $validated = $request->validate([
            'number' => 'required|integer|min:0',
            'title' => 'nullable|string|max:255',
            'status' => 'required|string|in:' . implode(',', array_keys($this->getStatusOptions())),
            'planned_date' => 'nullable|date',
            'actual_date' => 'nullable|date',
            'plan' => 'nullable|array',
            'plan.objectives' => 'nullable|string',
            'plan.encounters' => 'nullable|string',
            'plan.npcs' => 'nullable|string',
            'plan.locations' => 'nullable|string',
            'notes' => 'nullable|string',
            'recap' => 'nullable|string',
            'outcomes' => 'nullable|array',
            'outcomes.summary' => 'nullable|string',
            'outcomes.decisions' => 'nullable|string',
            'outcomes.consequences' => 'nullable|string',
        ]);

        // Check for duplicate session number (excluding current session)
        if ($validated['number'] !== $number) {
            if ($campaign->gameSessions()->where('number', $validated['number'])->exists()) {
                return back()->withErrors(['number' => 'A session with this number already exists.']);
            }
        }

        $session->update([
            'number' => $validated['number'],
            'title' => $validated['title'] ?? null,
            'status' => $validated['status'],
            'planned_date' => $validated['planned_date'] ?? null,
            'actual_date' => $validated['actual_date'] ?? null,
            'plan' => $validated['plan'] ?? [],
            'notes' => $validated['notes'] ?? null,
            'recap' => $validated['recap'] ?? null,
            'outcomes' => $validated['outcomes'] ?? [],
        ]);

        return redirect()->route('campaigns.sessions.show', [
            'campaignSlug' => $campaign->slug,
            'number' => $session->number,
        ])->with('success', 'Session updated successfully.');
    }

    public function destroy(string $campaignSlug, int $number)
    {
        $campaign = $this->getCampaign($campaignSlug);

        $session = $campaign->gameSessions()
            ->where('number', $number)
            ->firstOrFail();

        $session->delete();

        return redirect()->route('campaigns.sessions.index', $campaign->slug)
            ->with('success', 'Session deleted successfully.');
    }
}
