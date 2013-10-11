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

        $this->processorMock = m::mock('Level3\Processor');

        $this->level3Mock =  m::mock('Level3\Level3');
        $this->level3Mock->shouldReceive('getProcessor')
            ->withNoArgs()->once()
            ->andReturn($this->processorMock);
        
        $this->symfonyRequest = new Request();
        $this->controller = new Controller($this->level3Mock);
    }

    protected function createParametersMock()
    {
        return m::mock('Level3\Messages\Parameters');
    }

    protected function configureProcessorMock($method)
    {
        $this->processorMock->shouldReceive($method)
            ->with(m::type('Level3\Messages\Request'))
            ->once();
    }

    public function testFind()
    {
        $this->configureProcessorMock('find');

        $this->symfonyRequest->attributes->set('_route', 'foo:bar');
        $this->controller->find($this->symfonyRequest);
    }

    public function testGet()
    {
        $this->configureProcessorMock('get');

        $this->symfonyRequest->attributes->set('_route', 'foo:bar');
        $this->controller->get($this->symfonyRequest, 1);
    }

    public function testPost()
    {
        $this->configureProcessorMock('post');

        $this->symfonyRequest->attributes->set('_route', 'foo:bar');
        $this->symfonyRequest->request->set('foo', 'bar');

        $this->controller->post($this->symfonyRequest, 2);
    }

    public function testPut()
    {
        $this->configureProcessorMock('put');

        $this->symfonyRequest->attributes->set('_route', 'foo:bar');
        $this->symfonyRequest->request->set('foo', 'bar');

        $this->controller->put($this->symfonyRequest);
    }

    public function testDelete()
    {
        $this->configureProcessorMock('delete');

        $this->symfonyRequest->attributes->set('_route', 'foo:bar');

        $this->controller->delete($this->symfonyRequest, 3);
    }
}

