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

use Level3\RepositoryHub;
use Level3\Accessor;
use Level3\Resources;

use Level3\Messages\Processors\AccessorWrapper;
use Level3\Messages\Parser\ParserFactory;
use Level3\Messages\ResponseFactory;
use Level3\Messages\RequestFactory;

class Level3ServiceProvider implements ServiceProviderInterface {

    public function register(Application $app) {
        $app['level3.repository_hub']  = $app->share(function(Application $app) {
            return new RepositoryHub();
        });

        $app['level3.response_factory']  = $app->share(function(Application $app) {
            return new ResponseFactory();
        });

        $app['level3.resquest_factory']  = $app->share(function(Application $app) {
            return new RequestFactory();
        });

        $app['level3.parser_factory']  = $app->share(function(Application $app) {
            return new ParserFactory();
        });

        $app['level3.accessor'] = $app->share(function(Application $app) {
            return new Accessor(
                $app['level3.repository_hub'],
                $app['level3.response_factory']
            );
        });

        $app['level3.accessor_wrapper'] = $app->share(function(Application $app) {
            return new AccessorWrapper(
                $app['level3.accessor'],
                $app['level3.response_factory'],
                $app['level3.parser_factory']
            );
        });

        $app['level3.controller'] = $app->share(function(Application $app) {
            return new Controller(
                $app,
                $app['level3.accessor_wrapper'],
                $app['level3.resquest_factory']
            );
        });

        $app['level3.repository_mapper'] = $app->share(function(Application $app) {
            $mapper = new RepositoryMapper(
                $app,
                $app['level3.repository_hub']
            );

            $mapper->setBaseURI($app['level3.base.uri']);
            return $mapper;
        });
        
        $app['level3.repository_loader'] = $app->share(function(Application $app) {
            return new RepositoryLoader(
                $app,
                $app['level3.repository_hub'],
                $app['level3.loader.path'],
                $app['level3.loader.namespace']
            );
        });
        
        $app['level3.base.uri'] = '/';
        $app['level3.loader.path'] = null;
        $app['level3.loader.namespace'] = null;
    }

    public function boot(Application $app) {
        $app['level3.repository_mapper']->boot();
    }
}


