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
use App\Domains\Rooms\Events\GameStartedEvent;
use App\Domains\Rooms\Events\TurnChangeEvent;
use App\Domains\Rooms\Events\TurnClockTickEvent;
use App\Models\Room;
use App\Models\Turn;
use App\Support\EventChainBuilder\EventChainBuilder;


class StartGameService
{
    private TurnService $turnService;

    public function __construct(
        private Room $room
    ) {
        $this->turnService = new TurnService();
    }

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
            ->chainEvent(new ClockTickEvent($this->room), 6)
            ->chainEvent(new GameStartedEvent($this->room, $this->room->gameTurns), 1)
            ->chainEvent(new ClockTickEvent($this->room), 5);

        for ($i = 0; $i < $this->room->gameTurns; $i++) {
            $turn = $this->turnService->create($this->room, $i + 1);
            $this->buildTurnEvents($builder, $turn);
        }

        $builder->chainEvent(new GameEndEvent($this->room), 1, 0);

        return $builder;
    }

    private function buildTurnEvents(EventChainBuilder $builder, Turn $turn)
    {
        $builder
            ->chainEvent(new TurnChangeEvent($turn), 1, 0)
            ->chainEvent(new PhaseChangeEvent($turn, RoomPhaseEnum::SUBMISSION, $this->room->submissionTime), 1, 0)
            ->chainEvent(new TurnClockTickEvent($turn), $this->room->submissionTime)
            ->chainEvent(new PhaseChangeEvent($turn, RoomPhaseEnum::INTERVAL, $this->room->intervalTime), 1, 0)
            ->chainEvent(new TurnClockTickEvent($turn), $this->room->intervalTime)
            ->chainEvent(new WordsListEvent($turn), 1, 0)
            ->chainEvent(new PhaseChangeEvent($turn, RoomPhaseEnum::VOTE, $this->room->voteTime), 1, 0)
            ->chainEvent(new TurnClockTickEvent($turn), $this->room->voteTime)
            ->chainEvent(new VotingResultEvent($turn), 1, 0)
            ->chainEvent(new PhaseChangeEvent($turn, RoomPhaseEnum::RESULTS, $this->room->resultsTime), 1, 0)
            ->chainEvent(new TurnClockTickEvent($turn), $this->room->resultsTime);
    }
}
