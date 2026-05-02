<?php

namespace App\Domains\Rooms\Events;

use App\Domains\Rooms\Contracts\ATurnEvent;
use App\Domains\Rooms\Enums\RoomEventTypeEnum;
use App\Domains\Rooms\Enums\RoomPhaseEnum;
use App\Models\Turn;

class PhaseChangeEvent extends ATurnEvent
{
    public function __construct(
        Turn $turn,
        private RoomPhaseEnum $phase,
        private int $countdown,
    ) {
        parent::__construct($turn);
    }

    public function getEventType(): RoomEventTypeEnum
    {
        return RoomEventTypeEnum::PHASE_CHANGE;
    }

    public function getPayload(): array
    {
        return [
            ...parent::getPayload(),
            'phase' => $this->phase->value,
            'countdown' => $this->countdown,
            'tale_content' => $this->getRoom()->tale_content,
        ];
    }
}
