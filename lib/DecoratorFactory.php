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

    public function decorate($object, $targetClassName)
    {
        if (class_exists($targetClassName)) {
            return new $targetClassName($object);
        }

        $cacheFile = $this->getCacheFile($targetClassName);

        if (false === $this->debug && file_exists($cacheFile)) {
            require_once($cacheFile);
            return new $targetClassName($object);
        }

        $code = $this->generator->generate(get_class($object), $targetClassName);
        file_put_contents($cacheFile, '<?php ' . $code);

        require_once($cacheFile);

        return new $targetClassName($object);
    }

    private function getCacheFile($className)
    {
        return $this->cacheDir . '/' . str_replace('\\', '.', $className);
    }
}
