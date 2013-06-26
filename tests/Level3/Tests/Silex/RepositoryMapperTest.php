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
use Level3\Silex\RepositoryMapper;
use Symfony\Component\HttpFoundation\Request;
use Mockery as m;

class RepositoryMapperTest extends TestCase {

    public function setUp()
    {
        parent::setUp();
        $this->appMock = m::mock('Silex\Application');
        $this->repositoryHubMock = m::mock('Level3\RepositoryHub');

        $this->repositoryMapper = new RepositoryMapper($this->appMock, $this->repositoryHubMock);
    }

    /**
     * @dataProvider validURLGeneration
     */
    public function testMethods($method, $verb, $uri, $alias)
    {
        $this->appMock
            ->shouldReceive($verb)
                ->once()
                ->with('bar', $uri)
                ->andReturn($this->appMock)
            ->shouldReceive('bind')
                ->once()
                ->with($alias)
                ->andReturn($this->appMock);

        $this->repositoryMapper->$method('foo', 'bar');
    }

    public function validURLGeneration()
    {
        return array(
            array('mapFind', 'get', 'level3.controller:find', 'foo:find'),
            array('mapGet', 'get', 'level3.controller:get', 'foo:get'),
            array('mapPost', 'post', 'level3.controller:post', 'foo:post'),
            array('mapPut', 'put', 'level3.controller:put', 'foo:put'),
            array('mapDelete', 'delete', 'level3.controller:delete', 'foo:delete')
        );
    }
}

