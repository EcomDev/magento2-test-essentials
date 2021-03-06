<?php

namespace EcomDev\Magento2TestEssentials;

use LogicException;
use Magento\Framework\ObjectManagerInterface;
use ReflectionClass;
use ReflectionNamedType;

/**
 * Simplistic implementation of Object Manager for testing
 */
class ObjectManager implements ObjectManagerInterface
{
    /**
     * List of objects instantiated by ObjectManager via get
     *
     * @var object[]
     */
    private $instances = [];

    /**
     * Factories for specific types
     *
     * @var callable[]
     */
    private $factories = [];

    /**
     * Creates a new instance of ObjectManager
     */
    public static function new(): self
    {
        return new self();
    }

    /* @inerhitDoc */
    public function create($type, array $arguments = [])
    {
        return $this->createObject($type, $arguments);
    }

    /* @inerhitDoc */
    public function get($type)
    {
        if ($type === ObjectManagerInterface::class) {
            return $this;
        }

        if (!isset($this->instances[$type])) {
            $this->instances[$type] = $this->createObject($type);
        }

        return $this->instances[$type];
    }

    /* @inerhitDoc */
    public function configure(array $configuration)
    {
        throw new LogicException('FakeObjectManager does not support native configure, use with other method for configuring it');
    }

    public function withObject(string $type, object $instance): self
    {
        $objectManager = clone $this;
        $objectManager->instances[$type] = $instance;
        return $objectManager;
    }

    /**
     * Adds a factory for a specific type an object manager
     *
     * @param  string $type
     * @param  callable $factory
     * @return $this
     */
    public function withFactory(string $type, callable $factory): self
    {
        $objectManager = clone $this;
        $objectManager->factories[$type] = $factory;
        return $objectManager;
    }

    /**
     * Creates an instance of an object
     */
    private function createObject(string $type, array $arguments = null): object
    {
        if (isset($this->factories[$type])) {
            return $this->factories[$type]($this, $arguments ?? []);
        }

        $class = new ReflectionClass($type);

        $constructor = $class->getConstructor();

        if ($arguments === null || $constructor === null) {
            return $class->newInstanceWithoutConstructor();
        }

        $constructorArgs = [];

        foreach ($constructor->getParameters() as $parameter) {
            if (isset($arguments[$parameter->getName()])) {
                $constructorArgs[] = $arguments[$parameter->getName()];
                continue;
            }

            if ($parameter->isDefaultValueAvailable()) {
                $constructorArgs[] = $parameter->getDefaultValue();
                continue;
            }

            if ($type = $parameter->getType()) {
                if ($type->isBuiltin() || !$type instanceof ReflectionNamedType) {
                    throw new \InvalidArgumentException(
                        sprintf(
                            'Argument $%s is required for instantiating %s object but it is impossible to resolve it',
                            $parameter->getName(),
                            $class->getName()
                        )
                    );
                }

                $constructorArgs[] = $this->get($type->getName());
            }
        }

        return $class->newInstanceArgs($constructorArgs);
    }
}
