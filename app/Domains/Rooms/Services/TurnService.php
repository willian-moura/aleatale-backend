<?php

namespace App\Domains\Rooms\Services;

use App\Models\Room;
use App\Models\Turn;
use Illuminate\Database\Eloquent\Collection;

class TurnService
{
    /**
     * Create a new turn for a room.
     */
    public function create(Room $room, int $number): Turn
    {
        return Turn::create([
            'room_id' => $room->id,
            'number' => $number,
        ]);
    }

    /**
     * Get a turn by id.
     */
    public function getById(int $id): Turn
    {
        return Turn::findOrFail($id);
    }

    /**
     * Get all turns for a room.
     */
    public function getByRoom(Room $room): Collection
    {
        return $room->turns()->orderBy('number')->get();
    }

    /**
     * Update a turn.
     */
    public function update(Turn $turn, array $data): Turn
    {
        $turn->update($data);

        return $turn;
    }

    /**
     * Delete a turn (soft delete).
     */
    public function delete(Turn $turn): void
    {
        $turn->delete();
    }
}
