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
use Level3\Hal\ResourceBuilderFactory;
use Silex\Application;
use Level3\RepositoryHub;

class RepositoryLoader
{
    const LOOKUP_PATTERN = '*.php';
    const PHP_EXTENSION = '.php';

    private $resourceBuilderFactory;
    private $documentRepositoryContainer;
    private $hub;
    private $classesPath;
    private $classesNamespace;

    public function __construct(
        ResourceBuilderFactory $resourceBuilderFactory,
        RepositoryHub $hub,
        DocumentRepositoryContainer $documentRepositoryContainer,
        $classesPath,
        $classesNamespace)
    {

        $this->resourceBuilderFactory = $resourceBuilderFactory;
        $this->documentRepositoryContainer = $documentRepositoryContainer;
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
        $fullClassName = $this->getFullClassName($classname);

        $reflectionClass = new \ReflectionClass($fullClassName);
        if ($reflectionClass->isAbstract()) return;

        $repositoryKey = $this->getRepositoryKey($classname);
        $repositoryDefinition = $this->getRepositoryDefinition($fullClassName);

        $this->hub->registerDefinition($repositoryKey, $repositoryDefinition); 
    }

    private function getRepositoryDefinition($fullClassName)
    {
        return function() use ($fullClassName) {
            $repositoryDefinition = new $fullClassName(
                $this->resourceBuilderFactory
            );
            $documentRepository = $this->documentRepositoryContainer->getRepositoryForResource($fullClassName);
            $repositoryDefinition->setDocumentRepository($documentRepository);
            return $repositoryDefinition;
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

    private function getFullClassName($classname)
    {
        return $this->classesNamespace . '\\' . $classname;
    }
}
