<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Crypt;

class UserSetting extends Model
{
    protected $fillable = [
        'user_id',
        'ai_provider',
        'api_keys',
        'ai_preferences',
    ];

    protected $casts = [
        'ai_preferences' => 'array',
    ];

    protected $hidden = [
        'api_keys',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get decrypted API keys
     */
    public function getApiKeysDecrypted(): array
    {
        if (empty($this->api_keys)) {
            return [];
        }

        try {
            return json_decode(Crypt::decryptString($this->api_keys), true) ?? [];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Set encrypted API keys
     */
    public function setApiKeysEncrypted(array $keys): void
    {
        // Filter out empty keys
        $keys = array_filter($keys, fn($value) => !empty($value));

        if (empty($keys)) {
            $this->api_keys = null;
        } else {
            $this->api_keys = Crypt::encryptString(json_encode($keys));
        }
    }

    /**
     * Get the API key for the currently selected provider
     */
    public function getCurrentApiKey(): ?string
    {
        $keys = $this->getApiKeysDecrypted();
        return $keys[$this->ai_provider] ?? null;
    }

    /**
     * Get API key for a specific provider
     */
    public function getApiKeyFor(string $provider): ?string
    {
        $keys = $this->getApiKeysDecrypted();
        return $keys[$provider] ?? null;
    }

    /**
     * Check if a provider has an API key configured
     */
    public function hasApiKeyFor(string $provider): bool
    {
        return !empty($this->getApiKeyFor($provider));
    }

    /**
     * Get masked API keys for display (show first/last few chars)
     */
    public function getMaskedApiKeys(): array
    {
        $keys = $this->getApiKeysDecrypted();
        $masked = [];

        foreach ($keys as $provider => $key) {
            if (strlen($key) > 12) {
                $masked[$provider] = substr($key, 0, 7) . '...' . substr($key, -4);
            } else {
                $masked[$provider] = '***';
            }
        }

        return $masked;
    }

    /**
     * Available AI providers
     */
    public static function getAvailableProviders(): array
    {
        return [
            'openai' => [
                'name' => 'OpenAI',
                'models' => ['gpt-4o', 'gpt-4o-mini', 'gpt-4-turbo'],
                'key_prefix' => 'sk-',
                'key_help' => 'Get your API key from platform.openai.com',
            ],
            'anthropic' => [
                'name' => 'Anthropic',
                'models' => ['claude-sonnet-4-20250514', 'claude-3-5-haiku-20241022'],
                'key_prefix' => 'sk-ant-',
                'key_help' => 'Get your API key from console.anthropic.com',
            ],
        ];
    }

    /**
     * Get default AI preferences
     */
    public static function getDefaultPreferences(): array
    {
        return [
            'temperature' => 0.7,
            'max_tokens' => 2000,
            'include_campaign_context' => true,
            'include_related_nodes' => true,
        ];
    }
}
