<?php

namespace App\Services\Google;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use DateTime;
use Google\Client;
use Google_Service_Calendar;

class GoogleCalendar
{
    protected Google_Service_Calendar $calendar;

    protected $calendarId;

    public function __construct(
        Client $client,
        string $calendarId = 'primary'
    ) {
        $this->calendar = new Google_Service_Calendar($client);
        $this->calendarId = $calendarId;
    }

    public function events(?CarbonInterface $startDateTime = null, ?CarbonInterface $endDateTime = null, array $queryParameters = []): \Google\Service\Calendar\Events
    {
        $parameters = [
            'singleEvents' => true,
            'orderBy' => 'startTime',
        ];

        if (is_null($startDateTime)) {
            $startDateTime = Carbon::now()->startOfDay();
        }

        $parameters['timeMin'] = $startDateTime->format(DateTime::RFC3339);

        if (is_null($endDateTime)) {
            $endDateTime = Carbon::now()->addYear()->endOfDay();
        }
        $parameters['timeMax'] = $endDateTime->format(DateTime::RFC3339);

        $parameters = array_merge($parameters, $queryParameters);

        return $this
            ->calendar
            ->events
            ->listEvents($this->calendarId, $parameters);
    }

    public function getEvent(string $eventId): \Google\Service\Calendar\Event
    {
        return $this->calendar->events->get($this->calendarId, $eventId);
    }

    /*
   * @link https://developers.google.com/google-apps/calendar/v3/reference/events/insert
   */
    public function insertEvent($event, $optParams = []): \Google\Service\Calendar\Event
    {
        if ($event instanceof Event) {
            $event = $event->googleEvent;
        }

        return $this->calendar->events->insert($this->calendarId, $event, $optParams);
    }

    public function updateEvent($event, $optParams = []): \Google\Service\Calendar\Event
    {
        if ($event instanceof Event) {
            $event = $event->googleEvent;
        }

        return $this->calendar->events->update($this->calendarId, $event->id, $event, $optParams);
    }

    public function deleteEvent($eventId, $optParams = []): void
    {
        if ($eventId instanceof Event) {
            $eventId = $eventId->id;
        }

        $this->calendar->events->delete($this->calendarId, $eventId, $optParams);
    }

    public function getService(): Google_Service_Calendar
    {
        return $this->calendar;
    }

    /**
     * @return $this
     */
    public function setCalendarId(string $calendarId): static
    {

        $this->calendarId = $calendarId;

        return $this;

    }
}
