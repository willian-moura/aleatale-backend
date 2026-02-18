<?php

namespace App\Domains\Rooms\Services;

use App\Domains\Rooms\Events\PlayerLeftEvent;
use App\Models\Room;
use App\Models\RoomUser;
use App\Models\User;

class LeaveRoomService
{
    /**
     * Leave a room (soft delete the entry).
     *
     * @throws \Exception
     */
    public function execute(Room $room, User $user): void
    {
        $entry = RoomUser::where('room_id', $room->id)
            ->where('user_id', $user->id)
            ->whereNull('deleted_at')
            ->first();

        if (!$entry) {
            throw new \Exception('User is not in this room.');
        }

        $entry->delete();

        broadcast(new PlayerLeftEvent($user, $room))->toOthers();
    }
}

