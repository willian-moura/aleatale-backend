<?php

namespace App\Domains\Rooms\Events;

use App\Domains\Rooms\Contracts\ARoomEvent;
use App\Domains\Rooms\Enums\RoomEventTypeEnum;

class ClockTickEvent extends ARoomEvent
{
    public function getEventType(): RoomEventTypeEnum
    {
        return RoomEventTypeEnum::CLOCK_TICK;
    }

    public function getPayload(): array
    {
        return [
            'tic' => 'tac',
        ];
    }
}
