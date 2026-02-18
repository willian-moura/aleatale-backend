<?php

namespace App\Domains\Rooms\Services;

use App\Domains\Rooms\Events\PlayerJoinedEvent;
use App\Models\Room;
use App\Models\RoomUser;
use App\Models\User;

class JoinRoomService
{
    /**
     * Join a user to a room.
     *
     * @throws \Exception
     */
    public function execute(Room $room, User $user): RoomUser
    {
        $existingEntry = RoomUser::where('room_id', $room->id)
            ->where('user_id', $user->id)
            ->whereNull('deleted_at')
            ->first();

        if ($existingEntry) {
            throw new \Exception('User is already in this room.');
        }

        $roomUser = RoomUser::create([
            'room_id' => $room->id,
            'user_id' => $user->id,
            'ready' => false,
        ]);

        broadcast(new PlayerJoinedEvent($user, $room))->toOthers();

        return $roomUser;
    }
}

