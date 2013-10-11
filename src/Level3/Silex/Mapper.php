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

use Silex\Application;
use Level3\Mapper as BaseMapper;
use Level3\Resource\Parameters;

class Mapper extends BaseMapper
{
    private $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function mapFinder($resourceKey, $uri)
    {
        $this->app->
            get($uri, 'level3.controller:find')->
            bind(sprintf('%s:find', $resourceKey));
    }

    public function mapGetter($resourceKey, $uri)
    {
        $this->app->
            get($uri, 'level3.controller:get')->
            bind(sprintf('%s:get', $resourceKey));
    }
    
    public function mapPoster($resourceKey, $uri)
    {
        $this->app->
            post($uri, 'level3.controller:post')->
            bind(sprintf('%s:post', $resourceKey));
    }
    
    public function mapPutter($resourceKey, $uri)
    {
        $this->app->
            put($uri, 'level3.controller:put')->
            bind(sprintf('%s:put', $resourceKey));
    }

    public function mapPatcher($resourceKey, $uri)
    {
        $this->app->
            match($uri, 'level3.controller:patch')->
            method('PATCH')->
            bind(sprintf('%s:patch', $resourceKey));
    }
    
    public function mapDeleter($resourceKey, $uri)
    {
        $this->app->
            delete($uri, 'level3.controller:delete')->
            bind(sprintf('%s:delete', $resourceKey));
    }
    
    public function mapOptions($resourceKey, $uri)
    {
        $this->app->
            match($uri, 'level3.controller:options')->
            method('OPTIONS')->
            bind(sprintf('%s:options', $resourceKey));
    }
}