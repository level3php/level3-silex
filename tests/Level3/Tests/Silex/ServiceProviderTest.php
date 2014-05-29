<?php
/*
 * This file is part of the Level3/Silex package.
 *
 * (c) Máximo Cuadros <maximo@yunait.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Level3\Tests\Silex;
use Level3\Silex\ServiceProvider;

use Silex\Application;

class ServiceProviderTest extends TestCase {
    private function getAppWithLevel3ServiceProvider()
    {
        $app = new Application();
        $app->register(new ServiceProvider());

        return $app;
    }
    public function testRegisterLevel3()
    {
        $app = $this->getAppWithLevel3ServiceProvider();
        $this->assertInstanceOf('Level3\Level3', $app['level3']);
    }

    public function testRegisterHub()
    {
        $app = $this->getAppWithLevel3ServiceProvider();
        $this->assertInstanceOf('Level3\Hub', $app['level3.hub']);
    }

    public function testRegisterRepositoryMapper()
    {
        $app = $this->getAppWithLevel3ServiceProvider();
        $this->assertInstanceOf('Level3\Silex\Mapper', $app['level3.mapper']);

    }

    public function testSetBaseURI()
    {
        $app = $this->getAppWithLevel3ServiceProvider();
        $app['level3.base_uri'] = 'foo';

        $this->assertSame('foo', $app['level3.mapper']->getBaseURI());
    }

    public function testRegisterController()
    {
        $app = $this->getAppWithLevel3ServiceProvider();
        $this->assertInstanceOf('Level3\Silex\Controller', $app['level3.controller']);
    }

    public function testRegisterProcessor()
    {
        $app = $this->getAppWithLevel3ServiceProvider();
        $this->assertInstanceOf('Level3\Processor', $app['level3.processor']);
    }
}
