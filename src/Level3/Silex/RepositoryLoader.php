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
use ReflectionClass;

class RepositoryLoader
{
    const LOOKUP_PATTERN = '*.php';
    const PHP_EXTENSION = '.php';
    const NAMESPACE_SEPARATOR = '\\';

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

    private function getFilesFromClassPath() {
        $files = $this->searchFilesRecursive($this->classesPath . self::LOOKUP_PATTERN);

        return array_filter($files, function($file) {
            return $this->isBaseOrBuilderFile($file);
        });
    }

    private function isBaseOrBuilderFile($file)
    {
        $tmp = explode(DIRECTORY_SEPARATOR, $file);
        $folder = $tmp[count($tmp) - 2];
        if ($folder == 'Base' || $folder == 'Builder') {
            return false;
        }

        return true;
    }

    private function searchFilesRecursive($pattern, $flags = 0)
    {
        $files = glob($pattern);

        foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR) as $dir) {
            $files = array_merge($files, $this->searchFilesRecursive($dir.'/'.basename($pattern), $flags));
        }

        return $files;
    }

    private function loadRepositoryDefinition($filename)
    {
        $classname = $this->getClassName($filename);
        $fullClassName = $this->getFullClassName($classname);

        if ($this->isAbstract($fullClassName)) return;

        $repositoryKey = $this->getRepositoryKey($classname);
        $repositoryDefinition = $this->getRepositoryDefinition($fullClassName);

        $this->hub->registerDefinition($repositoryKey, $repositoryDefinition); 
    }

    private function isAbstract($fullClassName) {
        $reflectionClass = new ReflectionClass($fullClassName);

        return $reflectionClass->isAbstract();
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
        return str_replace(self::NAMESPACE_SEPARATOR, DIRECTORY_SEPARATOR, strtolower($classname));
    }

    private function getClassName($filename)
    {
        $fileWithOutClassesPath = str_replace($this->classesPath, '', $filename);
        $className = str_replace(self::PHP_EXTENSION, '', $fileWithOutClassesPath);

        return str_replace(DIRECTORY_SEPARATOR, self::NAMESPACE_SEPARATOR, $className);
    }

    private function getFullClassName($className)
    {
        return $this->classesNamespace . self::NAMESPACE_SEPARATOR . $className;
    }
}
