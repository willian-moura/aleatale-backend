<?php

namespace App\Providers;

use App\Domains\Rooms\Http\Routes\RoomRoutes;
use App\Domains\Tests\Http\Routes\TestRoutes;
use App\Domains\User\Http\Routes\AuthRoutes;
use App\Domains\User\Http\Routes\UserRoutes;
use App\Http\Middlewares\LogRequest;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends \Illuminate\Foundation\Support\Providers\RouteServiceProvider
{
    protected $apiRoutes = [
        AuthRoutes::class,
        UserRoutes::class,
        RoomRoutes::class,
        TestRoutes::class,
    ];

    public function boot()
    {
        parent::boot();

        foreach ($this->apiRoutes as $route) {
            $middlewares = [];

            if (env('DEBUG_QUERY')) {
                $middlewares[] = LogRequest::class;
            }

            Route::middleware($middlewares)
                ->prefix('api')
                ->group(function () use ($route) {
                    (new $route())->register();
                });
        }
    }
}
