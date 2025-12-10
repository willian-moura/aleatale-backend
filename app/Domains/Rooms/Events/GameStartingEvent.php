<?php

namespace App\Domains\Rooms\Events;

use App\Domains\Rooms\Contracts\ARoomEvent;
use App\Domains\Rooms\Enums\RoomEventTypeEnum;

class GameStartingEvent extends ARoomEvent
{
    public function getEventType(): RoomEventTypeEnum
    {
        return RoomEventTypeEnum::GAME_STARTING;
    }

    public function getPayload(): array
    {
        return [
            'remainingSeconds' => 5,
        ];
    }
}
