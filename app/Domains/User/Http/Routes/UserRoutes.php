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
        ];
    }

    protected function routes()
    {
        $this->router->middleware([])->group(function () {
            $this->router->get('/', [UserController::class, 'index']);
        });
    }
}
