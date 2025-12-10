<?php

namespace App\Domains\Rooms\Contracts;

use App\Domains\Rooms\Enums\RoomEventTypeEnum;

interface IRoomEvent
{
    public function getEventType(): RoomEventTypeEnum;
    public function getPayload(): array;
}
