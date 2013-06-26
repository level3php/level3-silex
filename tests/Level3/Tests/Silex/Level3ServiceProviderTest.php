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
use Level3\Silex\Level3ServiceProvider;

use Silex\Application;

class Level3ServiceProviderTest extends TestCase {
    private function getAppWithLevel3ServiceProvider()
    {
        $app = new Application();
        $app->register(new Level3ServiceProvider());

        return $app;
    }

    public function testRegisterRepositoryHub()
    {
        $app = $this->getAppWithLevel3ServiceProvider();
        $this->assertInstanceOf('Level3\RepositoryHub', $app['level3.repository_hub']);
    }

    public function testRegisterRepositoryMapper()
    {
        $app = $this->getAppWithLevel3ServiceProvider();
        $this->assertInstanceOf('Level3\Silex\RepositoryMapper', $app['level3.repository_mapper']);

    }

    public function testSetBaseURI()
    {
        $app = $this->getAppWithLevel3ServiceProvider();
        $app['level3.base.uri'] = 'foo';

        $this->assertSame('foo/', $app['level3.repository_mapper']->getBaseURI());
    }

    public function testRegisterFactories()
    {
        $app = $this->getAppWithLevel3ServiceProvider();
        $this->assertInstanceOf('Level3\Messages\ResponseFactory', $app['level3.response_factory']);
        $this->assertInstanceOf('Level3\Messages\RequestFactory', $app['level3.resquest_factory']);
        $this->assertInstanceOf('Level3\Messages\Parser\ParserFactory', $app['level3.parser_factory']);
    }

    public function testRegisterAccessor()
    {
        $app = $this->getAppWithLevel3ServiceProvider();
        $this->assertInstanceOf('Level3\Accessor', $app['level3.accessor']);
    }

    public function testRegisterAccessorWrapper()
    {
        $app = $this->getAppWithLevel3ServiceProvider();
        $this->assertInstanceOf('Level3\Messages\Processors\AccessorWrapper', $app['level3.accessor_wrapper']);
    }

    public function testRegisterController()
    {
        $app = $this->getAppWithLevel3ServiceProvider();
        $this->assertInstanceOf('Level3\Silex\Controller', $app['level3.controller']);
    }

    public function testRegisterRepositoryLoader()
    {
        $app = $this->getAppWithLevel3ServiceProvider();
        $this->assertInstanceOf('Level3\Silex\RepositoryLoader', $app['level3.repository_loader']);
    }
}

