<?php

namespace App\Domains\Rooms\Events;

use App\Domains\Rooms\Contracts\ARoomEvent;
use App\Domains\Rooms\Enums\RoomEventTypeEnum;

class VotingResultEvent extends ARoomEvent
{
    public function getEventType(): RoomEventTypeEnum
    {
        return RoomEventTypeEnum::VOTING_RESULT;
    }

    public function getPayload(): array
    {
        return [];
    }
}

