<?php

namespace App\Domains\Rooms\Http\Controllers;

use App\Domains\Rooms\Services\CreateRoomService;
use App\Domains\Rooms\Services\RoomService;
use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function __construct(
        private RoomService $roomService,
        private CreateRoomService $createRoomService
    ) {}

    /**
     * List rooms with optional name filter.
     */
    public function index(Request $request): JsonResponse
    {
        $rooms = $this->roomService->list(
            name: $request->query('name'),
            perPage: $request->query('per_page', 10)
        );

        return $this->success($rooms);
    }

    /**
     * Create a new room.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $room = $this->createRoomService->execute($data);

        return $this->success($room, 201);
    }

    /**
     * Show a specific room.
     */
    public function show(int $id): JsonResponse
    {
        $room = $this->roomService->getById($id);

        return $this->success($room);
    }

    /**
     * Update a room.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'status' => 'sometimes|string',
        ]);

        $room = Room::findOrFail($id);

        $room = $this->roomService->update($room, $data);

        return $this->success($room);
    }

    /**
     * Delete a room (soft delete).
     */
    public function destroy(int $id): JsonResponse
    {
        $room = Room::findOrFail($id);

        $this->roomService->delete($room);

        return $this->success('Room deleted successfully', 200);
    }
}
