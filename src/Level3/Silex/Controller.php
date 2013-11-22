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
use Level3\Level3;
use Level3\Messages\Request;
use Exception;

class Controller
{
    private $level3;

    public function __construct(Level3 $level3)
    {
        $this->level3 = $level3;
    }

    public function find(Request $request)
    {
        return $this->callMethod($request, __FUNCTION__);
    }

    public function get(Request $request)
    {
        return $this->callMethod($request, __FUNCTION__);
    }

    public function post(Request $request)
    {
        return $this->callMethod($request, __FUNCTION__);
    }

    public function patch(Request $request)
    {
        return $this->callMethod($request, __FUNCTION__);
    }

    public function put(Request $request)
    {
        return $this->callMethod($request, __FUNCTION__);
    }

    public function delete(Request $request)
    {
        return $this->callMethod($request, __FUNCTION__);
    }

    public function options(Request $request)
    {
        return $this->callMethod($request, __FUNCTION__);
    }

    public function error(Request $request, Exception $exception)
    {
        $repositoryKey = $this->getResourceKey($request);
        $response = $this->getProcessor()->error($repositoryKey, $request, $exception);

        return $response;
    }

    protected function callMethod(Request $request, $method)
    {
        $repositoryKey = $this->getResourceKey($request);
        $response = $this->getProcessor()->$method($repositoryKey, $request);

        return $response; 
    }

    protected function getProcessor()
    {
        return $this->level3->getProcessor();
    }

    protected function getResourceKey(Request $request)
    {
        $route = $request->attributes->get('_route');
        if (!$route) {
            return false;
        }

        $route = explode(':', $route);
        return $route[0];
    }
}
