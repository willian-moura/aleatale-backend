<?php

namespace App\Domains\Rooms\Events;

use App\Domains\Rooms\Contracts\ATurnEvent;
use App\Domains\Rooms\Enums\RoomEventTypeEnum;

class TurnClockTickEvent extends ATurnEvent
{
    public function getEventType(): RoomEventTypeEnum
    {
        return RoomEventTypeEnum::CLOCK_TICK;
    }

    public function getPayload(): array
    {
        return [
            ...parent::getPayload(),
            'tic' => 'tac',
        ];
    }
}
