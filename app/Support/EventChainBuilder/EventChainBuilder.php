<?php

namespace App\Support\EventChainBuilder;

use App\Events\GenericEvent;
use App\Jobs\BroadcastJob;

class EventChainBuilder
{

    /**
     * @var ChainLink[]
     */
    private array $chainLinks = [];
    private int $currentDelayMs = 0;

    public function chainEvent(GenericEvent $event, int $times = 1, int $delay = 1000): self
    {
        $this->chainLinks[] = new ChainLink($event, $times, $delay);
        return $this;
    }

    public function dispatch()
    {
        foreach ($this->chainLinks as $link) {
            $this->scheduleChainDispatch($link);
        }
    }

    private function scheduleChainDispatch(ChainLink $link)
    {
        $event = $link->getEvent();
        $times = $link->getTimes();
        $delay = $link->getDelay();

        BroadcastJob::dispatch($event, $times, $delay)->delay($this->currentDelayMs);

        $this->increaseCurrentDelay($times * $delay);
    }

    private function increaseCurrentDelay(int $delay): self
    {
        $this->currentDelayMs += $delay;
        return $this;
    }
}
