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
use Level3\Messages\Request;

use Mockery as m;

class ControllerTest extends TestCase {

    public function setUp()
    {
        parent::setUp();
        $this->appMock = m::mock('Silex\Application');

        $this->processorMock = m::mock('Level3\Processor');

        $this->level3Mock =  m::mock('Level3\Level3');
        $this->level3Mock->shouldReceive('getProcessor')
            ->withNoArgs()->once()
            ->andReturn($this->processorMock);
        
        $this->request = new Request();
        $this->controller = new Controller($this->level3Mock);
    }

    protected function createParametersMock()
    {
        return m::mock('Symfony\Component\HttpFoundation\ParameterBag');
    }

    protected function configureProcessorMock($method, $key = 'foo')
    {
        $this->processorMock->shouldReceive($method)
            ->with($key, m::type('Level3\Messages\Request'))
            ->once();
    }

    public function testFind()
    {
        $this->configureProcessorMock('find');

        $this->request->attributes->set('_route', 'foo:bar');
        $this->controller->find($this->request);
    }

    public function testGet()
    {
        $this->configureProcessorMock('get');

        $this->request->attributes->set('_route', 'foo:bar');
        $this->controller->get($this->request, 1);
    }

    public function testPost()
    {
        $this->configureProcessorMock('post');

        $this->request->attributes->set('_route', 'foo:bar');
        $this->request->request->set('foo', 'bar');

        $this->controller->post($this->request, 2);
    }

    public function testPut()
    {
        $this->configureProcessorMock('put');

        $this->request->attributes->set('_route', 'foo:bar');
        $this->request->request->set('foo', 'bar');

        $this->controller->put($this->request);
    }

    public function testDelete()
    {
        $this->configureProcessorMock('delete');

        $this->request->attributes->set('_route', 'foo:bar');

        $this->controller->delete($this->request, 3);
    }
}

