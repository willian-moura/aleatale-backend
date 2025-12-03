<?php

namespace App\Domains\Rooms\Http\Routes;

use App\Domains\Rooms\Http\Controllers\RoomController;
use App\Http\Routes\RouteFile;

class RoomRoutes extends RouteFile
{
    protected function config()
    {
        return [
            'prefix' => 'rooms',
            'middleware' => 'auth:sanctum',
        ];
    }

    protected function routes()
    {
        $this->router->get('/', [RoomController::class, 'index']);
        $this->router->post('/', [RoomController::class, 'store']);
        $this->router->get('/{id}', [RoomController::class, 'show']);
        $this->router->put('/{id}', [RoomController::class, 'update']);
        $this->router->delete('/{id}', [RoomController::class, 'destroy']);
    }
}
