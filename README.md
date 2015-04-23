Decorator Generator
===================

This is a small library for generating decorators for objects. Its only
purpose is to provide the object with a new class name whilst maintaining its
class type.

````php
$object = new \stdClass;

$factory = new DecoratorFactory('/path/to/cache', new DecoratorGenerator());
$decorator = $factory->generate($object, 'NewClass\Fqn\ClassName');
$decorated = new \NewClass\Fqn\ClassName();
````
