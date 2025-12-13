<?php

namespace App\Domains\Rooms\Events;

use App\Domains\Rooms\Contracts\ARoomEvent;
use App\Domains\Rooms\Enums\RoomEventTypeEnum;
use App\Models\Room;
use App\Models\User;

class PlayerReadyEvent extends ARoomEvent
{
    private User $player;

    public function __construct(User $player, Room $room)
    {
        $this->player = $player;
        parent::__construct($room);
    }

    public function getEventType(): RoomEventTypeEnum
    {
        return RoomEventTypeEnum::PLAYER_READY;
    }

    public function getPayload(): array
    {
        return [
            'player_id' => $this->player->id,
            'ready' => $this->room->users()->where('user_id', $this->player->id)->first()?->pivot?->ready,
        ];
    }
}
