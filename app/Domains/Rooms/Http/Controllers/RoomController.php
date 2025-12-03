<?php

namespace App\Domains\Rooms\Http\Controllers;

use App\Domains\Rooms\Services\CreateRoomService;
use App\Http\Controllers\Controller;

class RoomController extends Controller
{
    public function store()
    {
        $service = new CreateRoomService();
        $service->execute();
    }
}
