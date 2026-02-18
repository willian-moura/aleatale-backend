<?php

namespace App\Domains\Rooms\Services;

use App\Domains\Rooms\Events\PlayerReadyEvent;
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

        broadcast(new PlayerReadyEvent($user, $room))->toOthers();

        if ($this->checkIfAllPlayersReady($room)) {
            (new StartGameService($room))->execute();
        }

        return $entry;
    }

    private function checkIfAllPlayersReady(Room $room): bool
    {
        return $room->users()->where('ready', false)->count() === 0;
    }
}
