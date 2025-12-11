<?php

namespace App\Domains\Tests\Services;

use App\Domains\Rooms\Enums\RoomStatusEnum;
use App\Domains\Rooms\Events\ClockTickEvent;
use App\Domains\Rooms\Events\GameStartingEvent;
use App\Domains\Tests\Events\HelloWorldEvent;
use App\Models\Room;
use App\Support\EventChainBuilder\EventChainBuilder;
use Illuminate\Support\Str;

class TestEventBuilderService
{
    public function execute(): array {
//        $room = Room::query()->firstOrCreate(['name' => 'TestRoom', 'uuid' => Str::uuid(), 'status' => RoomStatusEnum::CREATED->value]);
        $room = Room::query()->first();

        $builder = new EventChainBuilder();
        $builder
            ->chainEvent(new GameStartingEvent($room), 1, 0)
            ->chainEvent(new ClockTickEvent($room), 5)
            ->dispatch();

        return [
            'room' => $room,
        ];
    }
}
