<?php

namespace EcomDev\Magento2TestEssentials;

use LogicException;
use Magento\Framework\ObjectManagerInterface;
use ReflectionClass;
use ReflectionNamedType;

/**
 * Simplistic implementation of Object Manager for testing
 */
final class ObjectManager implements ObjectManagerInterface
{
    /**
     * List of objects instantiated by ObjectManager via get
     *
     * @var object[]
     */
    private array $instances = [];

    /**
     * Factories for specific types
     *
     * @var callable[]
     */
    private array $factories = [];

    /**
     * List of default arguments for an object creation
     *
     * @var array[]
     */
    private array $defaultArguments = [];

    /**
     * Creates a new instance of ObjectManager
     */
    public static function new(): self
    {
        return new self();
    }

    /**
     * Creates a new version of requested object type with passed arguments
     */
    public function create($type, array $arguments = [])
    {
        return $this->createObject($type, $arguments);
    }

    /**
     * Returns shared default version of the object
     */
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

    public function configure(array $configuration)
    {
        throw new LogicException('Fake ObjectManager does not support native configure, use with other method for configuring it');
    }

    /**
     * Adds an object to internal registries
     */
    public function withObject(string $type, object $instance): self
    {
        $objectManager = clone $this;
        $objectManager->instances[$type] = $instance;
        return $objectManager;
    }

    /**
     * Adds a factory for a specific type an object manager
     */
    public function withFactory(string $type, callable $factory): self
    {
        $objectManager = clone $this;
        $objectManager->factories[$type] = $factory;
        return $objectManager;
    }

    /**
     * Adds a factory for a specific type an object manager
     */
    public function withDefaultArguments(string $type, array $arguments): self
    {
        $objectManager = clone $this;
        $objectManager->defaultArguments[$type] = $arguments;
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

        $arguments += $this->defaultArguments[$type] ?? [];
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
