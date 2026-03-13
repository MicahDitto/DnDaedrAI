<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    use HasUuids;

    protected $table = 'media';

    protected $fillable = [
        'node_id',
        'collection',
        'filename',
        'path',
        'mime_type',
        'size',
        'metadata',
        'order',
    ];

    protected $casts = [
        'metadata' => 'array',
        'size' => 'integer',
        'order' => 'integer',
    ];

    /**
     * Get the node that owns the media.
     */
    public function node(): BelongsTo
    {
        return $this->belongsTo(Node::class);
    }

    /**
     * Get the full URL for the media file.
     */
    public function getUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->path);
    }

    /**
     * Scope a query to only include featured images.
     */
    public function scopeFeatured($query)
    {
        return $query->where('collection', 'featured');
    }

    /**
     * Scope a query to only include gallery images.
     */
    public function scopeGallery($query)
    {
        return $query->where('collection', 'gallery');
    }
}
