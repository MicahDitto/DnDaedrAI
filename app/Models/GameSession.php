<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GameSession extends Model
{
    use HasFactory;

    protected $table = 'game_sessions';

    protected $fillable = [
        'campaign_id',
        'number',
        'title',
        'status',
        'planned_date',
        'actual_date',
        'plan',
        'notes',
        'recap',
        'outcomes',
    ];

    protected $casts = [
        'plan' => 'array',
        'outcomes' => 'array',
        'planned_date' => 'date',
        'actual_date' => 'date',
        'number' => 'integer',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function questionnaireResponses(): HasMany
    {
        return $this->hasMany(QuestionnaireResponse::class);
    }

    public function scopePlanned($query)
    {
        return $query->where('status', 'planned');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function isSessionZero(): bool
    {
        return $this->number === 0;
    }
}
