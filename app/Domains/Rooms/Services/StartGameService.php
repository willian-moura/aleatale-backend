<?php

namespace App\Domains\Rooms\Services;

use App\Domains\Rooms\Events\ClockTickEvent;
use App\Domains\Rooms\Events\GameStartingEvent;
use App\Domains\Rooms\Events\PhaseChangeEvent;
use App\Domains\Rooms\Events\WordsListEvent;
use App\Domains\Rooms\Events\VotingResultEvent;
use App\Domains\Rooms\Events\GameEndEvent;
use App\Domains\Rooms\Enums\RoomPhaseEnum;
use App\Domains\Rooms\Enums\RoomStatusEnum;
use App\Models\Room;
use App\Support\EventChainBuilder\EventChainBuilder;


class StartGameService
{

    public function __construct(
        private Room $room,
    ) {}

    public function execute(): void
    {
        $builder = $this->buildGameEvents();
        $builder->dispatch();

        $this->room->update([
            'status' => RoomStatusEnum::RUNNING,
            'started_at' => now(),
        ]);
    }

    private function buildGameEvents()
    {
        $builder = new EventChainBuilder();
        $builder
            ->chainEvent(new GameStartingEvent($this->room), 1, 0)
            ->chainEvent(new ClockTickEvent($this->room), 5);

        for ($i = 0; $i < $this->room->gameTurns; $i++) {
            $this->buildTurnEvents($builder);
        }

        $builder->chainEvent(new GameEndEvent($this->room), 1, 0);

        return $builder;
    }
    private function buildTurnEvents(EventChainBuilder $builder)
    {
        $builder
            ->chainEvent(new PhaseChangeEvent($this->room, RoomPhaseEnum::SUBMISSION), 1, 0)
            ->chainEvent(new ClockTickEvent($this->room), $this->room->submissionTime)
            ->chainEvent(new PhaseChangeEvent($this->room, RoomPhaseEnum::INTERVAL), 1, 0)
            ->chainEvent(new WordsListEvent($this->room), 1, 0)
            ->chainEvent(new ClockTickEvent($this->room), $this->room->intervalTime)
            ->chainEvent(new PhaseChangeEvent($this->room, RoomPhaseEnum::VOTE), 1, 0)
            ->chainEvent(new ClockTickEvent($this->room), $this->room->voteTime)
            ->chainEvent(new PhaseChangeEvent($this->room, RoomPhaseEnum::RESULTS), 1, 0)
            ->chainEvent(new VotingResultEvent($this->room), 1, 0)
            ->chainEvent(new ClockTickEvent($this->room), $this->room->resultsTime);
    }
}
