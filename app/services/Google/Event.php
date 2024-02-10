<?php

namespace App\Services\Google;

use Google_Service_Calendar_Event;

class Event
{
    /** @var \Google_Service_Calendar_Event */
    public $googleEvent;

    /** @var string */
    protected $calendarId;

    /** @var array */
    protected $attendees;

    /** @var bool */
    protected $hasMeetLink = false;

    public function __construct()
    {
        $this->attendees = [];
        $this->googleEvent = new Google_Service_Calendar_Event;
    }
}
