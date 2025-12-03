<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RoomClockUpdate implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $secondsSinceCreation;
    public $roomUuid;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($secondsSinceCreation, $roomUuid)
    {
        $this->secondsSinceCreation = $secondsSinceCreation;
        $this->roomUuid = $roomUuid;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel
     */
    public function broadcastOn()
    {
        return new Channel('room-' . $this->roomUuid);
    }

    public function broadcastWith()
    {
        return [
            'seconds' => $this->secondsSinceCreation,
        ];
    }
}
