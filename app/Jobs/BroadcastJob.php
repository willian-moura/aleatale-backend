<?php

namespace App\Jobs;

use App\Events\GenericEvent;
use App\Events\RoomClockUpdate;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class BroadcastJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private GenericEvent $event,
        private int $dispatchTimes = 1,
        private int $dispatchDelayMs = 1
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        broadcast($this->event)->toOthers();

        if ($this->dispatchTimes > 1) {
            BroadcastJob::dispatch($this->event, $this->dispatchTimes - 1, $this->dispatchDelayMs)->delay($this->dispatchDelayMs);
        }
    }
}
