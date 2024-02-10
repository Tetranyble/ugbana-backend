<?php

namespace App\Models;

use App\Jobs\SynchronizeEvents;
use App\Jobs\WatchEvents;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Calendar extends Model
{
    use HasFactory;

    protected $fillable = [
        'google_id',
        'name',
        'color',
        'timezone',
        'is_primary',
        'web_service_id',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    /**
     * Get the google account that owns the calendar.
     */
    public function webService(): BelongsTo
    {
        return $this->belongsTo(
            WebService::class,
            'web_service_id'
        );
    }

    /**
     * Get the events.
     */
    public function events(): HasMany
    {
        return $this->hasMany(Event::class, 'calendar_id');
    }

    /**
     * Synchronize events.
     */
    public function synchronize()
    {
        if (! $this->is_primary) {
            return;
        }

        SynchronizeEvents::dispatch($this);
    }

    public function watch()
    {
        if (! $this->is_primary) {
            return;
        }

        WatchEvents::dispatch($this);
    }
}
