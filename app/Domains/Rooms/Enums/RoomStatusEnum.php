<?php

namespace App\Domains\Rooms\Enums;

enum RoomStatusEnum: string
{
    case CREATED = 'created';
    case RUNNING = 'running';
    case FINISHED = 'finished';
}
