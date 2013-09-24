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
use Level3\Messages\Processors\RequestProcessor;
use Level3\Messages\RequestFactory;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Level3\Response as Level3Response;

class Controller
{
    private $processor;
    private $requestFactory;
    private $allowedOrigin = '';
    private $allowedMethods = '';

    public function __construct(RequestProcessor $processor, RequestFactory $requestFactory)
    {
        $this->processor = $processor;
        $this->requestFactory = $requestFactory;
    }

    public function setAllowedOrigin($allowedOrigin)
    {
        $this->allowedOrigin = $allowedOrigin;
    }

    public function setAllowedMethods($allowedMethods)
    {
        $this->allowedMethods = $allowedMethods;
    }

    protected function allowOrigin(Response $response, $method){
        $response->setHeader('Access-Control-Allow-Origin',$this->allowedOrigin);
        $response->setHeader('Access-Control-Allow-Methods',$this->allowedMethods);
        return $response;
    }

    public function options(Request $request)
    {
        $headers = $request->headers->all();
        $headers['Access-Control-Allow-Origin'] = $this->allowedOrigin;
        $headers['Access-Control-Allow-Methods'] = $this->allowedMethods;
        if (isset($headers['access-control-request-headers'])) {
            $headers['Access-Control-Allow-Headers'] = $headers['access-control-request-headers'];
        }

        unset($headers['access-control-request-headers']);
        unset($headers['access-control-request-method']);
        unset($headers['user-agent']);
        unset($headers['origin']);
        unset($headers['accept']);
        unset($headers['accept-language']);
        unset($headers['accept-encoding']);

        return new Response('', 200, $headers);
    }

    public function find(Request $request)
    {
        $level3Request = $this->createLevel3Request($request);
        $response = $this->processor->find($level3Request);
        $this->allowOrigin($response,'find');
        return $response;
    }

    public function get(Request $request)
    {
        $level3Request = $this->createLevel3Request($request);
        $response = $this->processor->get($level3Request);
        $this->allowOrigin($response,'get');
        return $response;
    }

    public function post(Request $request)
    {
        $level3Request = $this->createLevel3Request($request);
        $response = $this->processor->post($level3Request);
        $this->allowOrigin($response,'post');
        return $response;
    }

    public function put(Request $request)
    {
        $level3Request = $this->createLevel3Request($request);
        $response = $this->processor->put($level3Request);
        $this->allowOrigin($response,'put');
        return $response;
    }

    public function delete(Request $request)
    {
        $level3Request = $this->createLevel3Request($request);
        $response = $this->processor->delete($level3Request);
        $this->allowOrigin($response,'delete');
        return $response;
    }


    protected function createLevel3Request(Request $request)
    {
        return $this->requestFactory->clear()
            ->withKey($this->getResourceKey($request))
            ->withSymfonyRequest($request)
            ->create();
    }

    protected function getResourceKey(Request $request)
    {
        $params = $request->attributes->all();

        $route = explode(':', $params['_route']);
        return $route[0];
    }
}
