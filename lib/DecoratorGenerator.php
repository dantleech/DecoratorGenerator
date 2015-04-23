<?php

namespace DTL\DecoratorGenerator;

use \ReflectionClass;
use \ReflectionMethod;
use Zend\Code\Reflection\ClassReflection;
use Zend\Code\Generator\ClassGenerator;

/**
 * Generates decorator classes
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
class DecoratorGenerator
{
    public function generate($class, $targetClassName)
    {
        $reflection = new ClassReflection($class);
        $generator = ClassGenerator::fromReflection($reflection);

        $code = $this->buildClass($generator, $targetClassName);

        return $code;
    }

    private function buildClass(ClassGenerator $reflection, $targetClass)
    {
        $targetSegments = explode('\\', $targetClass);
        $targetName = array_pop($targetSegments);
        $targetNamespace = implode('\\', $targetSegments);
        $lines = array();
        if ($targetNamespace) {
            $lines[] = sprintf('namespace %s;', $targetNamespace);
        }
        $lines[] = sprintf('class %s extends \\%s\\%s {', $targetName, $reflection->getNamespaceName(), $reflection->getName());
        $lines[] = '    private $wrapped;';
        $lines[] = sprintf('    public function __construct(\\%s\\%s $wrapped) { $this->wrapped = $wrapped; }', $reflection->getNamespaceName(), $reflection->getName());

        foreach ($reflection->getMethods() as $method) {
            $args = array();
            foreach ($method->getParameters() as $param) {
                $args[] = '$' . $param->getName();
            }

            $method->setBody(sprintf('return $this->wrapped->%s(%s);', $method->getName(), implode(', ', $args)));
            $lines[] = $method->generate();
        }

        $lines[] = '}';

        return implode("\n", $lines);
    }
}

class TestObject
{
    private function privateMethod()
    {
    }

    public function doSomething(\stdClass $class, $foo = 0, $null = null, array $string)
    {
    }


    public function getSomething()
    {
        return 'something';
    }

}
