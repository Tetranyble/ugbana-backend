<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ApiRegistered
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The authenticated user.
     *
     * @var \Illuminate\Contracts\Auth\Authenticatable
     */
    public Authenticatable|User $user;

    /**
     * Create a new event instance.
     */
    public function __construct(Authenticatable|User $user)
    {
        $this->user = $user;
    }
}
