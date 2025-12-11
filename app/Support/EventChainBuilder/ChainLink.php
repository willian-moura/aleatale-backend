<?php

namespace App\Support\EventChainBuilder;

use App\Events\GenericEvent;

class ChainLink
{
    public function __construct(
        public GenericEvent $event,
        public int $times = 1,
        public int $delay = 1
    ) {}

    public function getEvent(): GenericEvent
    {
        return $this->event;
    }

    public function getTimes(): int
    {
        return $this->times;
    }

    public function getDelay(): int
    {
        return $this->delay;
    }
}
