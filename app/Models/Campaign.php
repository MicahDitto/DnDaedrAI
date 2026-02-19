<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'description',
        'genre',
        'rule_system',
        'tone_settings',
        'player_count',
        'status',
        'settings',
    ];

    protected $casts = [
        'tone_settings' => 'array',
        'settings' => 'array',
        'player_count' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (Campaign $campaign) {
            if (empty($campaign->slug)) {
                $campaign->slug = Str::slug($campaign->name);
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function nodes(): HasMany
    {
        return $this->hasMany(Node::class);
    }

    public function edges(): HasMany
    {
        return $this->hasMany(Edge::class);
    }

    public function tags(): HasMany
    {
        return $this->hasMany(Tag::class);
    }

    public function gameSessions(): HasMany
    {
        return $this->hasMany(GameSession::class);
    }

    public function questionnaireResponses(): HasMany
    {
        return $this->hasMany(QuestionnaireResponse::class);
    }

    public function characters(): HasMany
    {
        return $this->nodes()->where('type', 'character');
    }

    public function places(): HasMany
    {
        return $this->nodes()->where('type', 'place');
    }

    public function items(): HasMany
    {
        return $this->nodes()->where('type', 'item');
    }

    public function factions(): HasMany
    {
        return $this->nodes()->where('type', 'faction');
    }

    public function plots(): HasMany
    {
        return $this->nodes()->where('type', 'plot');
    }
}
