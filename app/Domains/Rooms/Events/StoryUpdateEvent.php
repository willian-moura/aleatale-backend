<?php

namespace App\Domains\Rooms\Events;

use App\Domains\Rooms\Contracts\ARoomEvent;
use App\Domains\Rooms\Enums\RoomEventTypeEnum;

class StoryUpdateEvent extends ARoomEvent
{
    public function getEventType(): RoomEventTypeEnum
    {
        return RoomEventTypeEnum::STORY_UPDATE;
    }

    public function getPayload(): array
    {
        return [];
    }
}

