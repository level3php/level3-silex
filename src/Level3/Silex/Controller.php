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

use Symfony\Component\HttpFoundation\Request;
use Silex\Application;
use Level3\Level3;
use Level3\Messages;

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

    protected function callMethod(Request $request, $method)
    {
        $level3Request = $this->createLevel3Request($request);
        $response = $this->getProcessor()->$method($level3Request);

        return $response; 
    }

    protected function getProcessor()
    {
        return $this->level3->getProcessor();
    }

    protected function getResourceKey(Request $request)
    {
        $params = $request->attributes->all();

        $route = explode(':', $params['_route']);
        return $route[0];
    }
    
    protected function createLevel3Request(Request $request)
    {
        return new Messages\Request($this->getResourceKey($request), $request);
    }
}