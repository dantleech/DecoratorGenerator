<?php

namespace DTL\DecoratorGenerator\Tests;

use Symfony\Component\Filesystem\Filesystem;
use DTL\DecoratorGenerator\DecoratorGenerator;
use DTL\DecoratorGenerator\DecoratorFactory;

class DecoratorFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $filesystem = new Filesystem();
        $cacheDir = __DIR__ . '/cache';

        if (file_exists($cacheDir)) {
            $filesystem->remove($cacheDir);
        }

        mkdir($cacheDir);

        $this->factory = new DecoratorFactory($cacheDir, new DecoratorGenerator());
    }

    /**
     * It should decorate an object
     *
     * @dataProvider provideDecorate
     */
    public function testDecorate($targetClassName)
    {
        $object = new FactoryTestObject();
        $decorated = $this->factory->decorate($object, $targetClassName);
        $something = $decorated->getSomething();

        $this->assertInstanceOf('\\' . $targetClassName, $decorated);
        $this->assertInstanceOf(FactoryTestObject::class, $decorated);
        $this->assertEquals('something', $something);
    }

    public function provideDecorate()
    {
        return array(
            array('FooNamespace\Fors\This\AsClass'),
            array('SomeNewClassName'),
            array(TestObject::class . 'Decorated'),
        );
    }
}

class FactoryTestObject
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
