<?php

namespace App\Jobs;

use App\Events\RoomClockUpdate;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class BroadcastRoomClock implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public $roomUuid,
        public $roomCreatedAt,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $secondsSinceCreation = Carbon::create($this->roomCreatedAt)->diffInSeconds(Carbon::now());

        // Broadcast the clock event
        broadcast(new RoomClockUpdate($secondsSinceCreation, $this->roomUuid))->toOthers();

        // Re-dispatch the job to run again in 1 second
        BroadcastRoomClock::dispatch($this->roomUuid, $this->roomCreatedAt)->delay(now()->addSecond());
    }
}
