<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class SynchronizeEvents implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $synchronizable;

    protected $synchronization;

    public function __construct($synchronizable)
    {
        $this->synchronizable = $synchronizable;

        $this->synchronization = $synchronizable->synchronization;
    }

    public function handle()
    {
        $pageToken = null;

        $syncToken = $this->synchronization->token;

        $service = $this->synchronizable->getGoogleService('Calendar');

        do {
            $tokens = compact('pageToken', 'syncToken');

            try {
                $list = $this->getGoogleRequest($service, $tokens);
            } catch (\Google_Service_Exception $e) {
                if ($e->getCode() === 410) {
                    $this->synchronization->update(['token' => null]);
                    $this->dropAllSyncedItems();

                    return $this->handle();
                }

                throw $e;
            }

            foreach ($list->getItems() as $item) {
                $this->syncItem($item);
            }

            $pageToken = $list->getNextPageToken();
        } while ($pageToken);

        $this->synchronization->update([
            'token' => $list->getNextSyncToken(),
            'last_synchronized_at' => now(),
        ]);
    }

    public function getGoogleRequest($service, $options)
    {
        return $service->events->listEvents(
            $this->synchronizable->google_id, $options
        );
    }

    public function syncItem($googleEvent)
    {
        if ($googleEvent->status === 'cancelled') {
            return $this->synchronizable->events()
                ->where('google_id', $googleEvent->id)
                ->delete();
        }

        if (Carbon::now() > $this->parseDatetime($googleEvent->start)) {
            return;
        }

        $event = $this->synchronizable->events()->updateOrCreate([
            'google_id' => $googleEvent->id,
        ]);

        $appointment = $event->appointment()->updateOrCreate(
            [
                'id' => $event->appointment_id,
            ], [
                'title' => $googleEvent->summary,
                'comment' => $googleEvent->description,
                'schedule_from' => $this->parseDatetime($googleEvent->start),
                'schedule_to' => $this->parseDatetime($googleEvent->end),
                'user_id' => $this->synchronizable->webService->user_id,
                'type' => 'meeting',
            ]
        );

        $event->update(['appointment_id' => $appointment->id]);
    }

    public function dropAllSyncedItems()
    {
        $this->synchronizable->events()->delete();
    }

    protected function isAllDayEvent($googleEvent)
    {
        return ! $googleEvent->start->dateTime && ! $googleEvent->end->dateTime;
    }

    protected function parseDatetime($googleDatetime)
    {
        $rawDatetime = $googleDatetime->dateTime ?: $googleDatetime->date;

        return Carbon::parse($rawDatetime);
    }
}
