<?php

namespace App\Domains\Rooms\Http\Routes;

use App\Domains\Rooms\Http\Controllers\RoomController;
use App\Http\Routes\RouteFile;

class RoomRoutes extends RouteFile
{

    protected function config()
    {
        return [
            'prefix' => 'room/rooms',
        ];
    }

    protected function routes()
    {
        $this->router->middleware([])->group(function () {
            $this->router->post('/', [RoomController::class, 'store']);
        });
    }
}
