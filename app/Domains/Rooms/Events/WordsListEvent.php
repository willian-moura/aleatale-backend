<?php

namespace App\Domains\Rooms\Events;

use App\Domains\Rooms\Contracts\ARoomEvent;
use App\Domains\Rooms\Enums\RoomEventTypeEnum;

class WordsListEvent extends ARoomEvent
{
    public function getEventType(): RoomEventTypeEnum
    {
        return RoomEventTypeEnum::WORDS_LIST;
    }

    public function getPayload(): array
    {
        return [];
    }
}

