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
     * Marks classes that should be autowired instead of created with no default constructor
     *
     * @var array<string, bool>
     */
    private array $autowiredClasses = [];

    /**
     * Marks object argument to be auto-resolved
     * from another object reference in container
     *
     * @var array<string,array<string,string>>
     */
    private array $autoResolvedArguments = [];

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
     *
     * @param callable(ObjectManager, array<string,mixed>): object  $factory
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
     * Adds a auto-resolver for specific argument
     */
    public function withAutoResolvedArgument(string $type, string $name, string $reference): self
    {
        $objectManager = clone $this;
        $objectManager->autoResolvedArguments[$type][$name] = $reference;
        return $objectManager;
    }

    /**
     * Creates an instance of an object
     */
    private function createObject(string $type, ?array $arguments = null): object
    {
        if (isset($this->factories[$type])) {
            return $this->factories[$type]($this, ($arguments ?? []) + ($this->defaultArguments[$type] ?? []));
        }

        return $this->createWithReflection($type, $arguments);
    }

    public function withAutowire(string $class): self
    {
        $object = clone $this;
        $object->autowiredClasses[$class] = true;
        return $object;
    }

    /**
     * Used to create objects with autowired dependencies
     *
     * @deprecated since 2.1.0, will be removed in 3.0.0
     *
     * @use self::withAutowire for make sure object is autowired on get calls
     * @use self::create for creating objects manually
     */
    public function createAutowiredObject(string $type, ?array $arguments = null): object
    {
        return $this->createWithReflection($type, $arguments);
    }

    private function createWithReflection(string $type, ?array $arguments = null): object
    {
        $class = new ReflectionClass($type);

        $constructor = $class->getConstructor();

        if ($arguments === null && isset($this->autowiredClasses[$type])) {
            $arguments = []; // Autowire arguments for object
        }

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

            if (isset($this->autoResolvedArguments[$type][$parameter->getName()])) {
                $constructorArgs[] = $this->get(
                    $this->autoResolvedArguments[$type][$parameter->getName()]
                );
                continue;
            }

            if ($parameter->isDefaultValueAvailable()) {
                $constructorArgs[] = $parameter->getDefaultValue();
                continue;
            }

            if ($parameterType = $parameter->getType()) {
                if ($parameterType->isBuiltin() || !$parameterType instanceof ReflectionNamedType) {
                    throw new \InvalidArgumentException(
                        sprintf(
                            'Argument $%s is required for instantiating %s object but it is impossible to resolve it',
                            $parameter->getName(),
                            $class->getName()
                        )
                    );
                }

                $constructorArgs[] = $this->get($parameterType->getName());
            }
        }

        return $class->newInstanceArgs($constructorArgs);
    }
}
