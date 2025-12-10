<?php

namespace App\Domains\Rooms\Contracts;

use App\Models\Room;
use App\Domains\Rooms\Enums\RoomEventTypeEnum;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use App\Events\GenericEvent;

abstract class ARoomEvent extends GenericEvent implements IRoomEvent
{
    public function __construct(
        public Room $room,
    ) {}

    abstract public function getEventType(): RoomEventTypeEnum;
    abstract public function getPayload(): array;

    public function getRoom(): Room
    {
        return $this->room;
    }

    public function broadcastOn(): Channel
    {
        return new PrivateChannel('room-' . $this->getRoom()->uuid);
    }

    public function broadcastWith(): array
    {
        return [
            'eventType' => $this->getEventType(),
            'payload' => $this->getPayload(),
            'timestamp' => now()->timestamp,
        ];
    }
}
