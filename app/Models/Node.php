<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Node extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'campaign_id',
        'type',
        'subtype',
        'name',
        'slug',
        'summary',
        'content',
        'metadata',
        'confidence',
        'is_secret',
    ];

    protected $casts = [
        'content' => 'array',
        'metadata' => 'array',
        'is_secret' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Node $node) {
            if (empty($node->slug)) {
                $node->slug = Str::slug($node->name);
            }
        });
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'node_tag');
    }

    public function outgoingEdges(): HasMany
    {
        return $this->hasMany(Edge::class, 'source_node_id');
    }

    public function incomingEdges(): HasMany
    {
        return $this->hasMany(Edge::class, 'target_node_id');
    }

    public function relatedNodes(): BelongsToMany
    {
        return $this->belongsToMany(
            Node::class,
            'edges',
            'source_node_id',
            'target_node_id'
        )->withPivot(['type', 'label', 'strength', 'metadata', 'is_secret']);
    }

    public function media(): HasMany
    {
        return $this->hasMany(Media::class)->orderBy('order');
    }

    public function featuredImage()
    {
        return $this->hasOne(Media::class)->where('collection', 'featured');
    }

    public function galleryImages(): HasMany
    {
        return $this->hasMany(Media::class)->where('collection', 'gallery')->orderBy('order');
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeCharacters($query)
    {
        return $query->ofType('character');
    }

    public function scopePlaces($query)
    {
        return $query->ofType('place');
    }

    public function scopeItems($query)
    {
        return $query->ofType('item');
    }

    public function scopeFactions($query)
    {
        return $query->ofType('faction');
    }

    public function scopePlots($query)
    {
        return $query->ofType('plot');
    }

    public function scopeLore($query)
    {
        return $query->ofType('lore');
    }

    public function scopeReligions($query)
    {
        return $query->ofType('religion');
    }

    public function scopeMagicSystems($query)
    {
        return $query->ofType('magic_system');
    }

    public function scopeVisible($query)
    {
        return $query->where('is_secret', false);
    }
}
