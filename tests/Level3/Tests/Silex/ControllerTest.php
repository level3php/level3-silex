<?php
/*
 * This file is part of the Skeetr package.
 *
 * (c) Máximo Cuadros <maximo@yunait.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Level3\Tests\Silex;
use Level3\Silex\Controller;
use Symfony\Component\HttpFoundation\Request;
use Mockery as m;

class ControllerTest extends TestCase {

    public function setUp()
    {
        parent::setUp();
        $this->appMock = m::mock('Silex\Application');

        $this->processorMock =  m::mock('Level3\Messages\Processors\RequestProcessor');
        $this->requestFactoryMock = m::mock('Level3\Messages\RequestFactory');
        $this->requestMock = m::mock('Level3\Messages\Request');

        $this->controller = new Controller(
            $this->appMock, 
            $this->processorMock, 
            $this->requestFactoryMock
        );
    }

    private function configureProcessorMock($method)
    {
        $this->processorMock
            ->shouldReceive($method)
            ->once()
            ->with($this->requestMock)
            ->andReturn(new \Level3\Messages\Response);
    }

    private function configureRequestFactoryMock($id, $attributes)
    {
        $this->requestFactoryMock
            ->shouldReceive('clear')
                ->once()
                ->withNoArgs()
                ->andReturn($this->requestFactoryMock)
            ->shouldReceive('withKey')
                ->once()
                ->with('foo')
                ->andReturn($this->requestFactoryMock)
            ->shouldReceive('withId')
                ->once()
                ->with($id)
                ->andReturn($this->requestFactoryMock)
            ->shouldReceive('withAttributes')
                ->once()
                ->with($attributes)
                ->andReturn($this->requestFactoryMock)
            ->shouldReceive('withHeaders')
                ->once()
                ->with(null)
                ->andReturn($this->requestFactoryMock)
            ->shouldReceive('withContent')
                ->once()
                ->with(null)
                ->andReturn($this->requestFactoryMock)
            ->shouldReceive('create')
                ->once()
                ->withNoArgs()
                ->andReturn($this->requestMock);
    }

    public function testFind()
    {
        $this->configureProcessorMock('find');
        $this->configureRequestFactoryMock(null, null);


        $request = new Request();
        $request->attributes->set('_route', 'foo:bar');
        $this->controller->find($request);
    }

    public function testGet()
    {
        $this->configureProcessorMock('get');
        $this->configureRequestFactoryMock(1, null);

        $request = new Request();
        $request->attributes->set('_route', 'foo:bar');
        $this->controller->get($request, 1);
    }

    public function testPost()
    {
        $this->configureProcessorMock('post');
        $this->configureRequestFactoryMock(2, array('foo' => 'bar'));

        $request = new Request();
        $request->attributes->set('_route', 'foo:bar');
        $request->request->set('foo', 'bar');

        $this->controller->post($request, 2);
    }

    public function testPut()
    {
        $this->configureProcessorMock('put');
        $this->configureRequestFactoryMock(null, array('foo' => 'bar'));

        $request = new Request();
        $request->attributes->set('_route', 'foo:bar');
        $request->request->set('foo', 'bar');

        $this->controller->put($request);
    }

    public function testDelete()
    {
        $this->configureProcessorMock('delete');
        $this->configureRequestFactoryMock(3, null);

        $request = new Request();
        $request->attributes->set('_route', 'foo:bar');

        $this->controller->delete($request, 3);
    }
}

