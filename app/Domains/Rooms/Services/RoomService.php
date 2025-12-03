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
}

