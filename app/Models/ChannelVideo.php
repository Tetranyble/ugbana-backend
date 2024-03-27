<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChannelVideo extends Model
{
    use HasFactory;

    protected $fillable = [
        'etag',
        'kind',
        'playlist_id',
        'uuid',
        'description',
        'published_at',
        'title',
        'thumbnail',
        'live_broadcast',
        'url',
        'channel_id',
        'status',
        'tag',
        'repost_count',
        'resolution',
        'playlist',
        'playlist_index',
        'view_count',
        'duration',
        'filename',
        'artist',
        'user_id',
        'category',
    ];

    protected $casts = [
        'thumbnail' => 'array',
        'tag' => 'array',
        'published_at' => 'datetime',
    ];

    /**
     * Get owner
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get channel
     */
    public function channel(): BelongsTo
    {
        return $this->belongsTo(Channel::class, 'channel_id');
    }
}
