<?php

namespace DTL\DecoratorGenerator;

/**
 * Factory for decorators
 */
class DecoratorFactory
{
    private $cacheDir;
    private $generator;
    private $debug;

    public function __construct($cacheDir, DecoratorGenerator $generator, $debug = false)
    {
        $this->cacheDir = $cacheDir;
        $this->generator = $generator;
        $this->debug = $debug;
    }

    /**
     * Decorate the given class with the given class name
     *
     * @param object $object Object to decorate
     * @param string $targetClassName Name of the class to generate and decorate with
     *
     * @return object
     */
    public function decorate($object, $targetClassName)
    {
        if (class_exists($targetClassName)) {
            return new $targetClassName($object);
        }

        $this->generate(get_class($object), $targetClassName);

        return new $targetClassName($object);
    }

    /**
     * Generate a decorator for the given class with the given target class name
     *
     * @param string $className
     * @param string $targetClassName
     *
     * @return string Cache file name
     */
    public function generate($className, $targetClassName)
    {
        if (class_exists($targetClassName)) {
            return null;
        }

        $cacheFile = $this->getCacheFile($targetClassName);

        if (false === $this->debug && file_exists($cacheFile)) {
            require_once($cacheFile);
            return;
        }

        $code = $this->generator->generate($className, $targetClassName);
        file_put_contents($cacheFile, '<?php ' . $code);
        require_once($cacheFile);
    }

    private function getCacheFile($className)
    {
        return $this->cacheDir . '/' . str_replace('\\', '.', $className);
    }
}
