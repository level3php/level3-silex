<?php

namespace Level3\Silex;

use Silex\Application as BaseApplication;

class Level3Application extends BaseApplication
{
    public function __construct()
    {
        parent::__construct();

        $app = $this;

        $this['controllers_factory'] = function () use ($app) {
            return new Level3ControllerCollection($app['route_factory']);
        };
    }

    public function options($pattern, $to)
    {
        return $this['controllers']->options($pattern, $to);
    }
}