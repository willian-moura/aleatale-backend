<?php

namespace App\Domains\Rooms\Factories;

use App\Models\Room;

class RoomStatePayloadFactory
{
    public function getRoomStatePayload(Room $room): array
    {
        return [
            'room_id' => $room->id,
            'room_name' => $room->name,
            'room_uuid' => $room->uuid,
            'room_users' => $room->users->toArray(),
        ];
    }
}
