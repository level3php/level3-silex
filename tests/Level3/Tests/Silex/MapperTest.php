<?php
/*
 * This file is part of the Level3\Silex package.
 *
 * (c) Máximo Cuadros <maximo@yunait.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Level3\Tests\Silex;
use Level3\Silex\Mapper;
use Symfony\Component\HttpFoundation\Request;
use Mockery as m;

class RepositoryMapperTest extends TestCase {

    public function setUp()
    {
        parent::setUp();
        $this->appMock = m::mock('Silex\Application');
        $this->repositoryHubMock = m::mock('Level3\RepositoryHub');

        $this->mapper = new Mapper($this->appMock);
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

        if ($verb == 'match') {
            $this->appMock
                ->shouldReceive('method')
                ->once()->with('OPTIONS')
                ->andReturn($this->appMock);
        }

        $this->mapper->$method('foo', 'bar');
    }

    public function validURLGeneration()
    {
        return array(
            array('mapFinder', 'get', 'level3.controller:find', 'foo:find'),
            array('mapGetter', 'get', 'level3.controller:get', 'foo:get'),
            array('mapPoster', 'post', 'level3.controller:post', 'foo:post'),
            array('mapPutter', 'put', 'level3.controller:put', 'foo:put'),
            array('mapDeleter', 'delete', 'level3.controller:delete', 'foo:delete'),
            //array('mapOptions', 'match', 'level3.controller:options', 'foo:options')
        );
    }
}

