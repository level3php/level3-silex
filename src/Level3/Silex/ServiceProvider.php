<?php
/*
 * This file is part of the Level3 package.
 *
 * (c) Máximo Cuadros <maximo@yunait.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Level3\Silex;

use Silex\ServiceProviderInterface;
use Silex\Application;

use Level3\Level3;
use Level3\Hub;
use Level3\Processor;

class ServiceProvider implements ServiceProviderInterface {

    public function register(Application $app) {
        $app['level3']  = $app->share(function(Application $app) {
            return new Level3(
                $app['level3.mapper'], 
                $app['level3.hub'],
                $app['level3.processor']
            );
        });

        $app['level3.mapper']  = $app->share(function(Application $app) {
            $mapper = new Mapper($app);
            $mapper->setBaseURI($app['level3.base_uri']);
            
            return $mapper;
        });

        $app['level3.hub']  = $app->share(function(Application $app) {
            return new Hub();
        });

        $app['level3.processor']  = $app->share(function(Application $app) {
            return new Processor();
        });

        $app['level3.controller'] = $app->share(function(Application $app) {
            return new Controller(
                $app['level3']
            );
        });

        $app['level3.base_uri'] = '/';
    }

    public function boot(Application $app) {
        $app['level3']->boot();
    }
}