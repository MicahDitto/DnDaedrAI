<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuestionnaireResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'game_session_id',
        'type',
        'question_key',
        'response',
    ];

    protected $casts = [
        'response' => 'array',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function gameSession(): BelongsTo
    {
        return $this->belongsTo(GameSession::class);
    }

    public function scopeCampaignSetup($query)
    {
        return $query->where('type', 'campaign_setup');
    }

    public function scopeSessionPlanning($query)
    {
        return $query->where('type', 'session_planning');
    }
}
