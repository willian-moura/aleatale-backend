<?php

namespace App\Domains\User\Http\Routes;

use App\Domains\User\Http\Controllers\UserController;
use App\Http\Routes\RouteFile;

class UserRoutes extends RouteFile
{
    protected function config()
    {
        return [
            'prefix' => 'users',
            'middleware' => 'auth:sanctum',
        ];
    }

    protected function routes()
    {
        $this->router->get('/', [UserController::class, 'index']);
        $this->router->post('/', [UserController::class, 'store']);
        $this->router->get('/{user}', [UserController::class, 'show']);
        $this->router->put('/{user}', [UserController::class, 'update']);
        $this->router->delete('/{user}', [UserController::class, 'destroy']);
    }
}
