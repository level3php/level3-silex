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
use Level3\RepositoryHub;

class RepositoryLoader
{
    const LOOKUP_PATTERN = '*.php';
    const PHP_EXTENSION = '.php';

    private $app;
    private $hub;
    private $classesPath;
    private $classesNamespace;

    public function __construct(Application $app, RepositoryHub $hub,  $classesPath, $classesNamespace)
    {
        $this->app = $app;
        $this->hub = $hub;

        $this->classesPath = $classesPath;
        $this->classesNamespace = $classesNamespace;
    }

    public function registerRepositories()
    {
        $files = $this->getFilesFromClassPath();
        foreach($files as $filename) {
            $this->loadRepositoryDefinition($filename); 
        }
    }

    private function getFilesFromClassPath()
    {
        return glob($this->classesPath . self::LOOKUP_PATTERN);
    }

    private function loadRepositoryDefinition($filename)
    {
        $classname = $this->getClassName($filename);
        $namespace = $this->getNamespace($classname);

        $repositoryKey = $this->getRepositoryKey($classname);
        $repositoryDefinition = $this->getRepositoryDefinition($namespace);

        $this->hub->registerDefinition($repositoryKey, $repositoryDefinition); 
    }

    private function getRepositoryDefinition($namespace)
    {
        $app = $this->app;
        return function() use ($app, $namespace) {
            return new $namespace($app);
        };
    }

    private function getRepositoryKey($classname)
    {
        return strtolower($classname);
    }

    private function getClassName($filename)
    {
        return basename($filename, self::PHP_EXTENSION);
    }

    private function getNamespace($classname)
    {
        return $this->classesNamespace . '\\' . $classname;
    }
}