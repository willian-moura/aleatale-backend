<?php

namespace App\Domains\Rooms\Events;

use App\Domains\Rooms\Contracts\ARoomEvent;
use App\Domains\Rooms\Enums\RoomEventTypeEnum;
use App\Domains\Rooms\Enums\RoomPhaseEnum;
use App\Models\Room;

class PhaseChangeEvent extends ARoomEvent
{
    public function __construct(
        Room $room,
        private RoomPhaseEnum $phase,
        private array $payload = [],
    ) {
        parent::__construct($room);
    }

    public function getEventType(): RoomEventTypeEnum
    {
        return RoomEventTypeEnum::PHASE_CHANGE;
    }

    public function getPayload(): array
    {
        return [
            'phase' => $this->phase->value,
            ...$this->payload,
        ];
    }
}
