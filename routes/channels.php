<?php
use App\Models\Room;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('room-{uuid}', function ($user, $uuid) {
    $room = Room::query()->where('uuid', $uuid)->first();

    if (!$room) {
        throw new Exception('Room not found');
    }

    return $room->users()->where('user_id', $user->id)->exists();
});
