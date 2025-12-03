<?php

namespace App\Domains\User\Http\Routes;

use App\Domains\User\Http\Controllers\AuthController;
use App\Http\Routes\RouteFile;

class AuthRoutes extends RouteFile
{
    protected function config()
    {
        return [];
    }

    protected function routes()
    {
        // Public routes
        $this->router->post('/login', [AuthController::class, 'login']);

        // Protected routes
        $this->router->middleware('auth:sanctum')->group(function () {
            $this->router->post('/logout', [AuthController::class, 'logout']);
        });
    }
}

