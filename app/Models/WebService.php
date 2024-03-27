<?php

namespace App\Models;

use App\Enums\StorageProvider;
use App\Jobs\SynchronizeCalendars;
use App\Jobs\WatchCalendars;
use App\Traits\Synchronizable;
use App\Traits\TokenService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WebService extends Model
{
    use HasFactory, Synchronizable,
        TokenService;

    protected $fillable = [
        'name',
        'token',
        'refresh_token',
        'user_id',
        'provider',
        'client_id', // google_id
        'scopes',
        'email',
    ];

    protected $casts = [
        'provider' => StorageProvider::class,
        'token' => 'json',
        'scopes' => 'json',
    ];

    /**
     * Get user's Web Service
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the webservice calendars
     */
    public function calendars(): HasMany
    {
        return $this->hasMany(Calendar::class, 'web_service_id');
    }

    /**
     * Synchronize calendars
     *
     * @return void
     */
    public function synchronize()
    {
        SynchronizeCalendars::dispatch($this);
    }

    /**
     * Start watching Webservice
     *
     * @return void
     */
    public function watch()
    {
        WatchCalendars::dispatch($this);
    }
}
