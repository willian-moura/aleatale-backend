<?php

namespace App\Domains\Rooms\Events;

use App\Domains\Rooms\Contracts\ARoomEvent;
use App\Domains\Rooms\Enums\RoomEventTypeEnum;
use App\Models\Room;
use Override;

class GameStartedEvent extends ARoomEvent
{
    public function __construct(
        Room $room,
        private int $turns
    ) {
        parent::__construct($room);
    }

    public function getEventType(): RoomEventTypeEnum
    {
        return RoomEventTypeEnum::GAME_STARTED;
    }

    public function getPayload(): array
    {
        return [
            'turns' => $this->turns,
        ];
    }
}
