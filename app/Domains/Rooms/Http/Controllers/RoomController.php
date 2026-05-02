<?php

namespace App\Domains\Rooms\Http\Controllers;

use App\Domains\Rooms\Services\CreateRoomService;
use App\Domains\Rooms\Services\GetRoomStateService;
use App\Domains\Rooms\Services\JoinRoomService;
use App\Domains\Rooms\Services\LeaveRoomService;
use App\Domains\Rooms\Services\RoomService;
use App\Domains\Rooms\Services\SentenceService;
use App\Domains\Rooms\Services\SetReadyService;
use App\Domains\Rooms\Services\VoteService;
use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Sentence;
use App\Models\Turn;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function __construct(
        private RoomService $roomService,
        private CreateRoomService $createRoomService,
        private JoinRoomService $joinRoomService,
        private LeaveRoomService $leaveRoomService,
        private SetReadyService $setReadyService,
        private GetRoomStateService $getRoomStateService,
        private SentenceService $sentenceService,
        private VoteService $voteService
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
     * Get the current state of a room.
     * Only users present in the room can access the state.
     */
    public function state(Request $request, int $id): JsonResponse
    {
        $room = Room::findOrFail($id);
        $user = $request->user();

        $isInRoom = $room->users()->where('user_id', $user->id)->exists();

        if (!$isInRoom) {
            return $this->error('You are not a member of this room.', [], 403);
        }

        $state = $this->getRoomStateService->execute($room);

        return $this->success($state);
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
            $roomUser = $this->joinRoomService->execute($room, $user);
            return $this->success($roomUser->room, 201);
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
            $this->leaveRoomService->execute($room, $user);
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
            $roomUser = $this->setReadyService->execute($room, $user, true);
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
            $roomUser = $this->setReadyService->execute($room, $user, false);
            return $this->success($roomUser);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), [], 400);
        }
    }

    /**
     * Submit a sentence for a turn.
     */
    public function submit(Request $request, int $id): JsonResponse
    {
        $data = $request->validate([
            'turn_id' => 'required|integer|exists:turns,id',
            'content' => 'required|string',
        ]);

        $room = Room::findOrFail($id);
        $user = $request->user();
        $turn = Turn::findOrFail($data['turn_id']);

        if ($turn->room_id !== $room->id) {
            return $this->error('Turn does not belong to this room.', [], 400);
        }

        if (!$room->users()->where('user_id', $user->id)->exists()) {
            return $this->error('You are not a member of this room.', [], 403);
        }

        $sentence = $this->sentenceService->create($turn, $user, $data['content']);

        return $this->success($sentence, 201);
    }

    /**
     * Vote for a sentence.
     */
    public function vote(Request $request, int $id): JsonResponse
    {
        $data = $request->validate([
            'turn_id' => 'required|integer|exists:turns,id',
            'sentence_id' => 'required|integer|exists:sentences,id',
        ]);

        $room = Room::findOrFail($id);
        $user = $request->user();
        $turn = Turn::findOrFail($data['turn_id']);
        $sentence = Sentence::findOrFail($data['sentence_id']);

        if ($turn->room_id !== $room->id) {
            return $this->error('Turn does not belong to this room.', [], 400);
        }

        if ($sentence->turn_id !== $turn->id) {
            return $this->error('Sentence does not belong to this turn.', [], 400);
        }

        if (!$room->users()->where('user_id', $user->id)->exists()) {
            return $this->error('You are not a member of this room.', [], 403);
        }

        $vote = $this->voteService->create($turn, $user, $sentence);

        return $this->success($vote, 201);
    }
}
