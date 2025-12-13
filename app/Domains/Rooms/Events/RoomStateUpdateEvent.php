<?php

namespace App\Domains\Rooms\Events;

use App\Domains\Rooms\Contracts\ARoomEvent;
use App\Domains\Rooms\Enums\RoomEventTypeEnum;
use App\Domains\Rooms\Factories\RoomStatePayloadFactory;

class RoomStateUpdateEvent extends ARoomEvent
{
    public function getEventType(): RoomEventTypeEnum
    {
        return RoomEventTypeEnum::ROOM_STATE_UPDATE;
    }

    public function getPayload(): array
    {
        return app(RoomStatePayloadFactory::class)->getRoomStatePayload($this->room);
    }
}
