<?php

namespace App\Domains\Rooms\Services;

use App\Models\Room;
use App\Models\RoomUser;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class RoomService
{
    /**
     * List rooms with optional name filter.
     */
    public function list(?string $name = null, int $perPage = 10): LengthAwarePaginator
    {
        $query = Room::query();

        if ($name) {
            $query->where('name', 'ilike', "%{$name}%");
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Get a room by id.
     */
    public function getById(int $id): Room
    {
        return Room::findOrFail($id);
    }

    /**
     * Update a room.
     */
    public function update(Room $room, array $data): Room
    {
        $room->update($data);

        return $room;
    }

    /**
     * Delete a room (soft delete).
     */
    public function delete(Room $room): void
    {
        $room->delete();
    }

    /**
     * Join a user to a room.
     *
     * @throws \Exception
     */
    public function join(Room $room, User $user): RoomUser
    {
        // Check if user is already in the room (has active entry)
        $existingEntry = RoomUser::where('room_id', $room->id)
            ->where('user_id', $user->id)
            ->whereNull('deleted_at')
            ->first();

        if ($existingEntry) {
            throw new \Exception('User is already in this room.');
        }

        // Always create a new entry
        return RoomUser::create([
            'room_id' => $room->id,
            'user_id' => $user->id,
            'ready' => false,
        ]);
    }

    /**
     * Leave a room (soft delete the entry).
     *
     * @throws \Exception
     */
    public function leave(Room $room, User $user): void
    {
        $entry = RoomUser::where('room_id', $room->id)
            ->where('user_id', $user->id)
            ->whereNull('deleted_at')
            ->first();

        if (!$entry) {
            throw new \Exception('User is not in this room.');
        }

        $entry->delete();
    }

    /**
     * Set user ready status in a room.
     *
     * @throws \Exception
     */
    public function setReady(Room $room, User $user, bool $ready): RoomUser
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
