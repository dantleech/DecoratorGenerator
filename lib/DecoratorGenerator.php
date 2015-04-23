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

        $code = $this->buildClass($reflection, $targetClassName);

        return $code;
    }

    private function buildClass(ClassReflection $reflection, $targetClass)
    {
        $generator = ClassGenerator::fromReflection($reflection);

        $targetSegments = explode('\\', $targetClass);
        $targetName = array_pop($targetSegments);
        $targetNamespace = implode('\\', $targetSegments);
        $lines = array();
        if ($targetNamespace) {
            $lines[] = sprintf('namespace %s;', $targetNamespace);
        }
        $lines[] = sprintf('class %s extends \\%s\\%s {', $targetName, $generator->getNamespaceName(), $generator->getName());
        $lines[] = '    private $wrapped;';
        $lines[] = sprintf('    public function __construct(\\%s\\%s $wrapped) { $this->wrapped = $wrapped; }', $generator->getNamespaceName(), $generator->getName());

        $hierarchy = array($reflection);
        $currentReflection = $reflection;

        while (false !== $currentReflection = $currentReflection->getParentClass()) {
            $hierarchy[] = $currentReflection;
        }

        $seenMethods = array();
        foreach ($hierarchy as $hierarchyClass) {
            $hierarchyGenerator = ClassGenerator::fromReflection($hierarchyClass);
            foreach ($hierarchyGenerator->getMethods() as $method) {
                if ($method->getName() === '__construct') {
                    continue;
                }

                if (in_array(strtolower($method->getName()), $seenMethods)) {
                    continue;
                }

                $args = array();
                foreach ($method->getParameters() as $param) {
                    $args[] = '$' . $param->getName();
                }

                $method->setBody(sprintf('return $this->wrapped->%s(%s);', $method->getName(), implode(', ', $args)));
                $lines[] = $method->generate();
                $seenMethods[] = strtolower($method->getName());
            }
        }

        $lines[] = '}';

        return implode("\n", $lines);
    }
}
