<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Channel extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'uuid',
        'etag',
        'kind',
        'country',
        'custom_url',
        'language',
        'description',
        'published_at',
        'thumbnail',
        'user_id',
        'is_owner',
        'url',
        'is_viable',
        'subscriber_count',
        'channel_user_id',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'thumbnail' => 'array',
        'is_owner' => 'boolean',
        'is_viable' => 'boolean',
    ];

    /**
     * Channel owned
     */
    public function isOwned(): bool
    {
        return $this->is_owner;
    }

    /**
     * Channel viable
     */
    public function isViable(): bool
    {
        return $this->is_viable;
    }

    /**
     * Get channel's owner
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get channel's videos
     */
    public function videos(): HasMany
    {
        return $this->hasMany(ChannelVideo::class, 'channel_id');
    }

    /**
     * Get Channel client profile
     */
    public function client(): HasOneThrough
    {
        return $this->hasOneThrough(
            WebService::class,
            User::class,
            'id'
        );

    }
}
