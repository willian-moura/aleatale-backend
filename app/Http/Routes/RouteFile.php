<?php

namespace App\Http\Routes;

use Illuminate\Routing\Router;

abstract class RouteFile
{
    protected Router $router;

    public function __construct()
    {
        $this->router = app('router');
    }

    public function register()
    {
        $this->router->group($this->config(), function () {
            $this->routes();
        });
    }

    abstract protected function config();

    abstract protected function routes();
}
