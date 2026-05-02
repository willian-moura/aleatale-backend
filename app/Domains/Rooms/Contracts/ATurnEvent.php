<?php

namespace App\Domains\Rooms\Contracts;

use App\Models\Turn;

abstract class ATurnEvent extends ARoomEvent
{
    public function __construct(
        public Turn $turn,
    ) {
        parent::__construct($turn->room);
    }

    public function getTurn(): Turn
    {
        return $this->turn;
    }

    public function getPayload(): array
    {
        return [
            'turn_id' => $this->turn->id,
        ];
    }
}
