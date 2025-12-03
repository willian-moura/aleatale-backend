<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('room-{uuid}', function ($user, $uuid) {
    return true;
});
