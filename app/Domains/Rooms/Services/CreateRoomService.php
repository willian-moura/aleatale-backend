<?php

namespace App\Domains\Rooms\Services;

use App\Jobs\BroadcastRoomClock;
use Carbon\Carbon;

class CreateRoomService
{
    public function execute()
    {
        // create room

        // start clock event
        $this->startClockEvent();
    }

    protected function startClockEvent()
    {
        $roomUuid = 'uuid-test-uuid-test-uuid';
        $roomCreatedAt = Carbon::now();

        BroadcastRoomClock::dispatch($roomUuid, $roomCreatedAt)->delay(now()->addSecond());
    }
}
