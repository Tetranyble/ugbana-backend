<?php

namespace App\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class WatchEvents implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $synchronizable;

    public function __construct($synchronizable)
    {
        $this->synchronizable = $synchronizable;
    }

    public function handle()
    {
        $synchronization = $this->synchronizable->synchronization;

        try {
            $response = $this->getGoogleRequest(
                $this->synchronizable->getGoogleService('Calendar'),
                $synchronization->asGoogleChannel()
            );

            $synchronization->update([
                'resource_id' => $response->getResourceId(),
                'expired_at' => Carbon::createFromTimestampMs($response->getExpiration()),
            ]);
        } catch (\Google_Service_Exception $e) {
            // If we reach an error at this point, it is likely that
            // push notifications are not allowed for this resource.
            // Instead we will sync it manually at regular interval.
        }
    }

    public function getGoogleRequest($service, $channel)
    {
        return $service->events->watch(
            $this->synchronizable->google_id, $channel
        );
    }
}
