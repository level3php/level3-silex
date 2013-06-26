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
use Level3\RepositoryMapper as BaseRepositoryMapper;
use Level3\RepositoryHub;

class RepositoryMapper extends BaseRepositoryMapper
{
    private $app;
    private $repositoryHub;

    public function __construct(Application $app, RepositoryHub $repositoryHub)
    {
        parent::__construct($repositoryHub);
        $this->app = $app;
    }

    public function mapFind($resourceKey, $uri)
    {
        $this->app->
            get($uri, 'level3.controller:find')->
            bind(sprintf('%s:find', $resourceKey));
    }

    public function mapGet($resourceKey, $uri)
    {
        $this->app->
            get($uri, 'level3.controller:get')->
            bind(sprintf('%s:get', $resourceKey));
    }
    
    public function mapPost($resourceKey, $uri)
    {
        $this->app->
            post($uri, 'level3.controller:post')->
            bind(sprintf('%s:post', $resourceKey));
    }
    
    public function mapPut($resourceKey, $uri)
    {
        $this->app->
            put($uri, 'level3.controller:put')->
            bind(sprintf('%s:put', $resourceKey));
    }
    
    public function mapDelete($resourceKey, $uri)
    {
        $this->app->
            delete($uri, 'level3.controller:delete')->
            bind(sprintf('%s:delete', $resourceKey));
    }

    public function getURI($resourceKey, $method, array $parameters = null)
    {
        $alias = sprintf('%s:%s', $resourceKey, $method);
        return $this->app['url_generator']->generate($alias, $parameters);
    }
}