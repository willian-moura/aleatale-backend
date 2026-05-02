<?php

namespace App\Domains\Rooms\Events;

use App\Domains\Rooms\Contracts\ARoomEvent;
use App\Domains\Rooms\Enums\RoomEventTypeEnum;
use App\Models\Room;

class TurnChangeEvent extends ARoomEvent
{
    public function __construct(
        Room $room,
        private int $turn,
    ) {
        parent::__construct($room);
    }

    public function getEventType(): RoomEventTypeEnum
    {
        return RoomEventTypeEnum::TURN_CHANGE;
    }

    public function getPayload(): array
    {
        return [
            'turn' => $this->turn,
        ];
    }
}
