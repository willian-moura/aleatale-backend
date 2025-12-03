<?php

namespace App\Domains\Rooms\Http\Controllers;

use App\Domains\Rooms\Services\CreateRoomService;
use App\Domains\Rooms\Services\RoomService;
use App\Http\Controllers\Controller;
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

        return response()->json($rooms);
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

        return response()->json($room, 201);
    }
}
