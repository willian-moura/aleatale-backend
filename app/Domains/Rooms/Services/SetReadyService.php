<?php

namespace App\Domains\Rooms\Services;

use App\Models\Room;
use App\Models\RoomUser;
use App\Models\User;

class SetReadyService
{
    /**
     * Set user ready status in a room.
     *
     * @throws \Exception
     */
    public function execute(Room $room, User $user, bool $ready): RoomUser
    {
        $entry = RoomUser::where('room_id', $room->id)
            ->where('user_id', $user->id)
            ->whereNull('deleted_at')
            ->first();

        if (!$entry) {
            throw new \Exception('User is not in this room.');
        }

        $entry->update(['ready' => $ready]);

        return $entry;
    }
}

