<?php

namespace App\Domains\Rooms\Enums;

enum RoomPhaseEnum: string
{
    case SUBMISSION = 'submission';
    case INTERVAL = 'interval';
    case VOTE = 'vote';
    case RESULTS = 'results';
}

