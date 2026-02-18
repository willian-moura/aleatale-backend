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

    /**
     * @var int
     * Indica o atraso atual em segundos
     */
    private int $currentDelay = 0;

    public function chainEvent(GenericEvent $event, int $times = 1, int $delay = 1): self
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

        BroadcastJob::dispatch($event, $times, $delay)->delay($this->currentDelay);

        $duration = $times > 1 ? ($times - 1) * $delay : 0;
        $this->increaseCurrentDelay($duration);
    }

    private function increaseCurrentDelay(int $delay): self
    {
        $this->currentDelay += $delay;
        return $this;
    }
}
