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

    /**
     * Join a room.
     */
    public function join(Request $request, int $id): JsonResponse
    {
        $room = Room::findOrFail($id);
        $user = $request->user();

        try {
            $roomUser = $this->roomService->join($room, $user);
            return $this->success($roomUser, 201);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), [], 400);
        }
    }

    /**
     * Leave a room.
     */
    public function leave(Request $request, int $id): JsonResponse
    {
        $room = Room::findOrFail($id);
        $user = $request->user();

        try {
            $this->roomService->leave($room, $user);
            return $this->success(['message' => 'Left room successfully.']);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), [], 400);
        }
    }

    /**
     * Mark user as ready in the room.
     */
    public function ready(Request $request, int $id): JsonResponse
    {
        $room = Room::findOrFail($id);
        $user = $request->user();

        try {
            $roomUser = $this->roomService->setReady($room, $user, true);
            return $this->success($roomUser);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), [], 400);
        }
    }

    /**
     * Mark user as not ready in the room.
     */
    public function notReady(Request $request, int $id): JsonResponse
    {
        $room = Room::findOrFail($id);
        $user = $request->user();

        try {
            $roomUser = $this->roomService->setReady($room, $user, false);
            return $this->success($roomUser);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), [], 400);
        }
    }
}
