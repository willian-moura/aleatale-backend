<?php

namespace App\Domains\Rooms\Services;

use App\Jobs\BroadcastRoomClock;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Domains\Rooms\Enums\RoomStatusEnum;

class CreateRoomService
{
    public function execute(array $data)
    {
        $room = Room::create([
            'uuid' => Str::uuid(),
            'name' => $data['name'],
            'status' => RoomStatusEnum::CREATED->value,
        ]);

        return $room;
    }

    protected function startClockEvent()
    {
        $roomUuid = 'uuid-test-uuid-test-uuid';
        $roomCreatedAt = Carbon::now();

        BroadcastRoomClock::dispatch($roomUuid, $roomCreatedAt)->delay(now()->addSecond());
    }
}
