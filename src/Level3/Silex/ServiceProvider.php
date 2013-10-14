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
use Level3\Processor\Wrapper\ExceptionHandler;
use Level3\Processor\Wrapper\CrossOriginResourceSharing;
use Level3\Processor\Wrapper\Logger;
use Level3\Processor\Wrapper\RateLimiter;
use Level3\Processor\Wrapper\Authenticator;
use Level3\Exceptions\HTTPException;

use Symfony\Component\HttpFoundation\Request;



use Exception;

class ServiceProvider implements ServiceProviderInterface {

    public function register(Application $app) {
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


        $app['level3.wrapper.exception_handler'] = $app->share(function(Application $app) {
            return new ExceptionHandler();
        });

        $app['level3.wrapper.authenticator'] = $app->share(function(Application $app) {
            if ($app['level3.wrapper.authenticator.method']) {
                return new Authenticator(
                    $app['level3.wrapper.authenticator.method']
                );
            }
        });

        $app['level3.wrapper.cors'] = $app->share(function(Application $app) {
            $cors = new CrossOriginResourceSharing();
            $cors->setAllowOrigin($app['level3.cors.allowed_origins']);

            return $cors;
        });

        $app['level3.wrapper.limiter'] = $app->share(function(Application $app) {
            if ($app['level3.redis']) {
                return new RateLimiter($app['level3.redis']);
            }
        });

        $app['level3.wrapper.logger'] = $app->share(function(Application $app) {
            if ($app['level3.logger']) {
                return new Logger($app['level3.logger']);
            }
        });

        $app['level3']  = $app->share(function(Application $app) {
            $level3 = new Level3(
                $app['level3.mapper'],
                $app['level3.hub'],
                $app['level3.processor']
            );


            if ($app['level3.enable.wrapper.authenticator'] && $app['level3.wrapper.authenticator']) {
                $level3->addProcessorWrapper($app['level3.wrapper.authenticator']);
            }

            if ($app['level3.enable.wrapper.limiter'] && $app['level3.wrapper.limiter']) {
                $level3->addProcessorWrapper($app['level3.wrapper.limiter']);
            }

            if ($app['level3.enable.wrapper.cors'] && $app['level3.wrapper.cors']) {
                $level3->addProcessorWrapper($app['level3.wrapper.cors']);
            }

            if ($app['level3.enable.wrapper.logger'] && $app['level3.wrapper.logger']) {
                $level3->addProcessorWrapper($app['level3.wrapper.logger']);
            }

            $level3->addProcessorWrapper($app['level3.wrapper.exception_handler']);

            return $level3;
        });

        $app['level3.enable.wrapper.limiter'] = false;
        $app['level3.enable.wrapper.cors'] = false;
        $app['level3.enable.wrapper.logger'] = false;
        $app['level3.enable.wrapper.authenticator'] = false;

        $app['level3.base_uri'] = '';
        $app['level3.logger'] = null;
        $app['level3.redis'] = null;

        $app['level3.cors.allowed_origins'] = CrossOriginResourceSharing::ALLOW_ORIGIN_WILDCARD;
    }

    public function boot(Application $app) {
        $app['level3']->boot();

        $app->error(function (Exception $exception, $code) use ($app) {
            if ($code != 0) {
                $exception = new HTTPException(
                    $exception->getMessage(), $code, $exception
                );
            }

            return $app['level3.controller']->error($app['request'], $exception);
        });
    }
}