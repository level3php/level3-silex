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
use Level3\Processor\Wrapper\BasicIpFirewall;
use Level3\Resource\Format\Writer\Siren;
use Level3\Resource\Format\Writer\HAL;
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
            return new Authenticator();
        });

        $app['level3.wrapper.basic_ip_firewall'] = $app->share(function(Application $app) {
            $firewall = new BasicIpFirewall();

            if (strlen($app['level3.firewall.blacklist']) != 0) {
                foreach(explode(',', $app['level3.firewall.blacklist']) as $ip) {
                    $firewall->addIpToBlacklist($ip);
                }
            }

            if (strlen($app['level3.firewall.whitelist']) != 0) {
                foreach(explode(',', $app['level3.firewall.whitelist']) as $ip) {
                    $firewall->addIpToWhitelist($ip);
                }
            }

            return $firewall;
        });

        $app['level3.wrapper.cors'] = $app->share(function(Application $app) {
            $cors = new CrossOriginResourceSharing();
            
            if ($origins = explode(',', $app['level3.cors.allowed_origins'])) {
                if (count($origins) == 1) {
                    $cors->setAllowOrigin($origins[0]);
                } else {
                    $cors->setMultipleAllowOrigin($origins);
                }
            }
                        
            if ($app['level3.cors.expose_headers']) {
                $cors->setExposeHeaders(explode(',', $app['level3.cors.expose_headers']));
            }
                        
            if ($app['level3.cors.max_age'] !== null) {
                $cors->setExposeHeaders((int) $app['level3.cors.max_age']);
            }

            if ($app['level3.cors.allow_credentials'] !== null) {
                $cors->setExposeHeaders((boolean) $app['level3.cors.allow_credentials']);
            }

            if ($app['level3.cors.allow_methods']) {
                $cors->setAllowHeaders(explode(',', $app['level3.cors.allow_methods']));
            }

            if ($app['level3.cors.allow_headers']) {
                $cors->setAllowHeaders(explode(',', $app['level3.cors.allow_headers']));
            }


            return $cors;
        });

        $app['level3.wrapper.limiter'] = $app->share(function(Application $app) {
            if (!$app['level3.redis']) {
                return null;
            }

            $limiter = new RateLimiter($app['level3.redis']);

            if ($app['level3.limiter.max_request'] !== null) {
                $limiter->setLimit($app['level3.limiter.max_request']);
            }

            if ($app['level3.limiter.time_period'] !== null) {
                $limiter->setResetAfterSecs($app['level3.limiter.time_period']);
            }

            return $limiter;
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

            if ($app['level3.enable.firewall'] && $app['level3.wrapper.basic_ip_firewall']) {
                $level3->addProcessorWrapper($app['level3.wrapper.basic_ip_firewall']);
            }

            if ($app['level3.enable.authenticator'] && $app['level3.wrapper.authenticator']) {
                $level3->addProcessorWrapper($app['level3.wrapper.authenticator']);
            }

            if ($app['level3.enable.limiter'] && $app['level3.wrapper.limiter']) {
                $level3->addProcessorWrapper($app['level3.wrapper.limiter']);
            }

            if ($app['level3.enable.cors'] && $app['level3.wrapper.cors']) {
                $level3->addProcessorWrapper($app['level3.wrapper.cors']);
            }

            if ($app['level3.enable.logger'] && $app['level3.wrapper.logger']) {
                $level3->addProcessorWrapper($app['level3.wrapper.logger']);
            }

            $level3->addProcessorWrapper($app['level3.wrapper.exception_handler']);

            $level3->addFormatWriter(new HAL\JsonWriter());
            $level3->addFormatWriter(new HAL\XMLWriter());
            $level3->addFormatWriter(new Siren\JsonWriter());
            
            return $level3;
        });

        $app['level3.enable.limiter'] = false;
        $app['level3.enable.cors'] = false;
        $app['level3.enable.logger'] = false;
        $app['level3.enable.authenticator'] = false;
        $app['level3.enable.firewall'] = false;

        $app['level3.base_uri'] = '';
        $app['level3.logger'] = null;
        $app['level3.redis'] = null;

        $app['level3.limiter.max_request'] = null;
        $app['level3.limiter.time_period'] = null;

        $app['level3.firewall.blacklist'] = null;
        $app['level3.firewall.whitelist'] = null;

        $app['level3.cors.allowed_origins'] = CrossOriginResourceSharing::ALLOW_ORIGIN_WILDCARD;
        $app['level3.cors.expose_headers'] = null;
        $app['level3.cors.max_age'] = null;
        $app['level3.cors.allow_credentials'] = null;
        $app['level3.cors.allow_methods'] = null;
        $app['level3.cors.allow_headers'] = null;
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
