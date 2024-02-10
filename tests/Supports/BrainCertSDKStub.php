<?php

namespace Tests\Supports;

use App\Interfaces\BrainCertInterface;

class BrainCertSDKStub implements BrainCertInterface
{
    use BrainCertSupport;

    public function schedule()
    {
        return $this->scheduleResponse();
    }

    public function lunchClassroom()
    {
        return $this->meetingUrl();
    }
}
