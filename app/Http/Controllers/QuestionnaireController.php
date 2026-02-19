<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\QuestionnaireResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class QuestionnaireController extends Controller
{
    protected function getCampaign(string $slug): Campaign
    {
        return Campaign::where('user_id', Auth::id())
            ->where('slug', $slug)
            ->firstOrFail();
    }

    public function show(string $campaignSlug)
    {
        $campaign = $this->getCampaign($campaignSlug);

        // Get existing responses
        $responses = $campaign->questionnaireResponses()
            ->where('type', 'campaign_setup')
            ->pluck('response', 'question_key')
            ->toArray();

        return Inertia::render('Campaigns/Setup/Questionnaire', [
            'campaign' => $campaign,
            'questions' => $this->getQuestions(),
            'responses' => $responses,
        ]);
    }

    public function store(Request $request, string $campaignSlug)
    {
        $campaign = $this->getCampaign($campaignSlug);

        $validated = $request->validate([
            'question_key' => 'required|string',
            'response' => 'required',
        ]);

        // Upsert the response
        QuestionnaireResponse::updateOrCreate(
            [
                'campaign_id' => $campaign->id,
                'type' => 'campaign_setup',
                'question_key' => $validated['question_key'],
            ],
            [
                'response' => $validated['response'],
            ]
        );

        return back();
    }

    public function complete(Request $request, string $campaignSlug)
    {
        $campaign = $this->getCampaign($campaignSlug);

        // Update campaign with collected data
        $responses = $campaign->questionnaireResponses()
            ->where('type', 'campaign_setup')
            ->pluck('response', 'question_key')
            ->toArray();

        $updateData = [];

        if (isset($responses['genre'])) {
            $updateData['genre'] = $responses['genre'];
        }

        if (isset($responses['player_count'])) {
            $updateData['player_count'] = (int) $responses['player_count'];
        }

        if (isset($responses['tone_settings'])) {
            $updateData['tone_settings'] = $responses['tone_settings'];
        }

        // Mark campaign as active (setup complete)
        $updateData['status'] = 'active';

        $campaign->update($updateData);

        return redirect()->route('campaigns.show', $campaign->slug)
            ->with('success', 'Campaign setup complete! You can now start building your world.');
    }

    protected function getQuestions(): array
    {
        return [
            [
                'key' => 'genre',
                'title' => 'Campaign Genre',
                'description' => 'What tone and genre best describes your campaign?',
                'type' => 'single_select',
                'options' => [
                    ['value' => 'high_fantasy', 'label' => 'High Fantasy', 'description' => 'Epic quests, powerful magic, world-changing events'],
                    ['value' => 'dark_fantasy', 'label' => 'Dark Fantasy', 'description' => 'Gritty, morally grey, dangerous world'],
                    ['value' => 'heroic_fantasy', 'label' => 'Heroic Fantasy', 'description' => 'Classic heroes vs villains, good triumphs'],
                    ['value' => 'sword_sorcery', 'label' => 'Sword & Sorcery', 'description' => 'Personal stakes, low magic, mortal concerns'],
                    ['value' => 'comedic', 'label' => 'Comedic', 'description' => 'Light-hearted, jokes, absurdist situations'],
                    ['value' => 'mystery', 'label' => 'Mystery', 'description' => 'Investigation, clues, unraveling secrets'],
                    ['value' => 'horror', 'label' => 'Horror', 'description' => 'Fear, dread, survival against the unknown'],
                    ['value' => 'political', 'label' => 'Political Intrigue', 'description' => 'Court drama, factions, scheming'],
                ],
            ],
            [
                'key' => 'player_count',
                'title' => 'Number of Players',
                'description' => 'How many players are in your group?',
                'type' => 'number',
                'min' => 1,
                'max' => 10,
                'default' => 4,
            ],
            [
                'key' => 'player_experience',
                'title' => 'Player Experience',
                'description' => 'How experienced is your group with TTRPGs?',
                'type' => 'single_select',
                'options' => [
                    ['value' => 'beginner', 'label' => 'New Players', 'description' => 'First campaign or still learning the rules'],
                    ['value' => 'intermediate', 'label' => 'Some Experience', 'description' => 'Comfortable with basics, still growing'],
                    ['value' => 'experienced', 'label' => 'Experienced', 'description' => 'Multiple campaigns, know the system well'],
                    ['value' => 'veteran', 'label' => 'Veterans', 'description' => 'Years of play, deep system mastery'],
                    ['value' => 'mixed', 'label' => 'Mixed Group', 'description' => 'Combination of experience levels'],
                ],
            ],
            [
                'key' => 'campaign_length',
                'title' => 'Campaign Length',
                'description' => 'How long do you expect this campaign to run?',
                'type' => 'single_select',
                'options' => [
                    ['value' => 'oneshot', 'label' => 'One-Shot', 'description' => 'Single session adventure'],
                    ['value' => 'short', 'label' => 'Short (1-3 months)', 'description' => '4-12 sessions'],
                    ['value' => 'medium', 'label' => 'Medium (3-6 months)', 'description' => '12-24 sessions'],
                    ['value' => 'long', 'label' => 'Long (6+ months)', 'description' => '24+ sessions, open-ended'],
                ],
            ],
            [
                'key' => 'world_type',
                'title' => 'Campaign World',
                'description' => 'What world will your campaign take place in?',
                'type' => 'single_select',
                'options' => [
                    ['value' => 'homebrew', 'label' => 'Homebrew World', 'description' => 'Your own original creation'],
                    ['value' => 'forgotten_realms', 'label' => 'Forgotten Realms', 'description' => 'Official D&D setting (Faerûn)'],
                    ['value' => 'eberron', 'label' => 'Eberron', 'description' => 'Magitech noir setting'],
                    ['value' => 'ravenloft', 'label' => 'Ravenloft', 'description' => 'Gothic horror domains'],
                    ['value' => 'greyhawk', 'label' => 'Greyhawk', 'description' => 'Classic high fantasy'],
                    ['value' => 'other_official', 'label' => 'Other Official Setting', 'description' => 'Different published setting'],
                    ['value' => 'hybrid', 'label' => 'Hybrid', 'description' => 'Mix of homebrew and official content'],
                ],
            ],
            [
                'key' => 'session_length',
                'title' => 'Typical Session Length',
                'description' => 'How long are your typical play sessions?',
                'type' => 'single_select',
                'options' => [
                    ['value' => '2h', 'label' => '2 hours', 'description' => 'Short sessions'],
                    ['value' => '3h', 'label' => '3 hours', 'description' => 'Standard online sessions'],
                    ['value' => '4h', 'label' => '4 hours', 'description' => 'Standard in-person sessions'],
                    ['value' => '5h+', 'label' => '5+ hours', 'description' => 'Extended play sessions'],
                ],
            ],
            [
                'key' => 'play_style',
                'title' => 'Play Style Balance',
                'description' => 'What balance of play styles does your group prefer?',
                'type' => 'multi_select',
                'max_selections' => 3,
                'options' => [
                    ['value' => 'combat', 'label' => 'Combat', 'description' => 'Tactical battles and encounters'],
                    ['value' => 'roleplay', 'label' => 'Roleplay', 'description' => 'Character interactions and drama'],
                    ['value' => 'exploration', 'label' => 'Exploration', 'description' => 'Discovering the world'],
                    ['value' => 'puzzles', 'label' => 'Puzzles', 'description' => 'Problem-solving challenges'],
                    ['value' => 'social', 'label' => 'Social', 'description' => 'NPC relationships and politics'],
                    ['value' => 'sandbox', 'label' => 'Sandbox', 'description' => 'Player-driven narrative'],
                ],
            ],
            [
                'key' => 'safety_tools',
                'title' => 'Safety Tools',
                'description' => 'Which safety tools will you use in your game?',
                'type' => 'multi_select',
                'options' => [
                    ['value' => 'lines_veils', 'label' => 'Lines & Veils', 'description' => 'Hard limits and fade-to-black topics'],
                    ['value' => 'x_card', 'label' => 'X-Card', 'description' => 'Stop and skip uncomfortable content'],
                    ['value' => 'open_door', 'label' => 'Open Door', 'description' => 'Anyone can leave, no questions asked'],
                    ['value' => 'checkins', 'label' => 'Regular Check-ins', 'description' => 'Periodic consent verification'],
                    ['value' => 'stars_wishes', 'label' => 'Stars & Wishes', 'description' => 'End-of-session feedback'],
                    ['value' => 'none', 'label' => 'None specified', 'description' => 'Will discuss later'],
                ],
            ],
            [
                'key' => 'inspirations',
                'title' => 'Inspirations',
                'description' => 'What books, movies, games, or other media inspire your campaign? (Optional)',
                'type' => 'text',
                'placeholder' => 'e.g., Lord of the Rings, Game of Thrones, The Witcher...',
            ],
            [
                'key' => 'campaign_summary',
                'title' => 'Campaign Pitch',
                'description' => 'Write a brief pitch for your campaign - the elevator speech you would give to players. (Optional)',
                'type' => 'textarea',
                'placeholder' => 'In a world where... your heroes must...',
            ],
        ];
    }
}
