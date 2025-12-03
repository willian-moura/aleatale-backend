<?php

namespace App\Domains\Rooms\Services;

use App\Models\Room;
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
}
