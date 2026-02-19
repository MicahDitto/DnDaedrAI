<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Edge extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'source_node_id',
        'target_node_id',
        'type',
        'label',
        'strength',
        'metadata',
        'is_secret',
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_secret' => 'boolean',
        'strength' => 'integer',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function sourceNode(): BelongsTo
    {
        return $this->belongsTo(Node::class, 'source_node_id');
    }

    public function targetNode(): BelongsTo
    {
        return $this->belongsTo(Node::class, 'target_node_id');
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeVisible($query)
    {
        return $query->where('is_secret', false);
    }
}
