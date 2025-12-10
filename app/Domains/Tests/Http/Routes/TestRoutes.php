<?php

namespace App\Domains\Tests\Http\Routes;

use App\Domains\Tests\Http\Controllers\TestController;
use App\Http\Routes\RouteFile;

class TestRoutes extends RouteFile
{
    protected function config()
    {
        return [
            'prefix' => 'tests',
        ];
    }

    protected function routes()
    {
        $this->router->post('/test-event-builder', [TestController::class, 'testEventBuilder']);
    }
}
