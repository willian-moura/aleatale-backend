<?php

namespace App\Domains\Rooms\Services;

use App\Domains\Rooms\Factories\RoomStatePayloadFactory;
use App\Models\Room;

class GetRoomStateService
{
    public function __construct(
        private RoomStatePayloadFactory $roomStatePayloadFactory
    ) {}

    public function execute(Room $room): array
    {
        return $this->roomStatePayloadFactory->getRoomStatePayload($room);
    }
}
