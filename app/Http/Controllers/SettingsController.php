<?php

namespace App\Http\Controllers;

use App\Models\UserSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class SettingsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $settings = $user->getOrCreateSettings();

        return Inertia::render('Settings/Index', [
            'settings' => [
                'ai_provider' => $settings->ai_provider,
                'ai_preferences' => $settings->ai_preferences ?? UserSetting::getDefaultPreferences(),
                'masked_api_keys' => $settings->getMaskedApiKeys(),
                'has_openai_key' => $settings->hasApiKeyFor('openai'),
                'has_anthropic_key' => $settings->hasApiKeyFor('anthropic'),
            ],
            'providers' => UserSetting::getAvailableProviders(),
            'defaultPreferences' => UserSetting::getDefaultPreferences(),
        ]);
    }

    public function updateProvider(Request $request)
    {
        $validated = $request->validate([
            'ai_provider' => 'required|string|in:openai,anthropic',
        ]);

        $user = Auth::user();
        $settings = $user->getOrCreateSettings();

        $settings->update([
            'ai_provider' => $validated['ai_provider'],
        ]);

        return back()->with('success', 'AI provider updated successfully.');
    }

    public function updateApiKey(Request $request)
    {
        $validated = $request->validate([
            'provider' => 'required|string|in:openai,anthropic',
            'api_key' => 'required|string|min:10',
        ]);

        $user = Auth::user();
        $settings = $user->getOrCreateSettings();

        // Validate API key format
        $providers = UserSetting::getAvailableProviders();
        $prefix = $providers[$validated['provider']]['key_prefix'] ?? '';

        if ($prefix && !str_starts_with($validated['api_key'], $prefix)) {
            return back()->withErrors([
                'api_key' => "API key should start with '{$prefix}'",
            ]);
        }

        // Get existing keys and update the specified one
        $keys = $settings->getApiKeysDecrypted();
        $keys[$validated['provider']] = $validated['api_key'];
        $settings->setApiKeysEncrypted($keys);
        $settings->save();

        return back()->with('success', ucfirst($validated['provider']) . ' API key saved successfully.');
    }

    public function removeApiKey(Request $request)
    {
        $validated = $request->validate([
            'provider' => 'required|string|in:openai,anthropic',
        ]);

        $user = Auth::user();
        $settings = $user->getOrCreateSettings();

        // Get existing keys and remove the specified one
        $keys = $settings->getApiKeysDecrypted();
        unset($keys[$validated['provider']]);
        $settings->setApiKeysEncrypted($keys);
        $settings->save();

        return back()->with('success', ucfirst($validated['provider']) . ' API key removed.');
    }

    public function updatePreferences(Request $request)
    {
        $validated = $request->validate([
            'temperature' => 'nullable|numeric|min:0|max:2',
            'max_tokens' => 'nullable|integer|min:100|max:8000',
            'include_campaign_context' => 'boolean',
            'include_related_nodes' => 'boolean',
        ]);

        $user = Auth::user();
        $settings = $user->getOrCreateSettings();

        $preferences = array_merge(
            $settings->ai_preferences ?? UserSetting::getDefaultPreferences(),
            $validated
        );

        $settings->update([
            'ai_preferences' => $preferences,
        ]);

        return back()->with('success', 'AI preferences updated successfully.');
    }

    public function testApiKey(Request $request)
    {
        $validated = $request->validate([
            'provider' => 'required|string|in:openai,anthropic',
        ]);

        $user = Auth::user();
        $settings = $user->getOrCreateSettings();
        $apiKey = $settings->getApiKeyFor($validated['provider']);

        if (!$apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'No API key configured for ' . $validated['provider'],
            ]);
        }

        try {
            if ($validated['provider'] === 'openai') {
                $response = $this->testOpenAIKey($apiKey);
            } else {
                $response = $this->testAnthropicKey($apiKey);
            }

            return response()->json([
                'success' => true,
                'message' => 'API key is valid!',
                'details' => $response,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'API key test failed: ' . $e->getMessage(),
            ]);
        }
    }

    private function testOpenAIKey(string $apiKey): array
    {
        $client = new \GuzzleHttp\Client();
        $response = $client->get('https://api.openai.com/v1/models', [
            'headers' => [
                'Authorization' => 'Bearer ' . $apiKey,
            ],
            'timeout' => 10,
        ]);

        $data = json_decode($response->getBody()->getContents(), true);
        return [
            'models_available' => count($data['data'] ?? []),
        ];
    }

    private function testAnthropicKey(string $apiKey): array
    {
        $client = new \GuzzleHttp\Client();
        $response = $client->post('https://api.anthropic.com/v1/messages', [
            'headers' => [
                'x-api-key' => $apiKey,
                'anthropic-version' => '2023-06-01',
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'model' => 'claude-3-5-haiku-20241022',
                'max_tokens' => 10,
                'messages' => [
                    ['role' => 'user', 'content' => 'Say "OK" and nothing else.'],
                ],
            ],
            'timeout' => 10,
        ]);

        $data = json_decode($response->getBody()->getContents(), true);
        return [
            'model' => $data['model'] ?? 'unknown',
        ];
    }
}
