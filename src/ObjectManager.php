<?php

namespace EcomDev\Magento2TestEssentials;

use LogicException;
use Magento\Framework\ObjectManagerInterface;
use ReflectionClass;
use ReflectionIntersectionType;
use ReflectionNamedType;
use ReflectionUnionType;

/**
 * Simplistic implementation of Object Manager for testing
 */
final class ObjectManager implements ObjectManagerInterface
{
    /**
     * List of objects instantiated by ObjectManager via get
     *
     * @var array<class-string, object>
     */
    private array $instances = [];

    /**
     * Factories for specific types
     *
     * @var array<class-string, callable(ObjectManager, array<string, mixed>):object>
     */
    private array $factories = [];

    /**
     * List of default arguments for an object creation
     *
     * @var array<class-string, array<string, mixed>>
     */
    private array $defaultArguments = [];

    /**
     * Marks classes that should be autowired instead of created with no default constructor
     *
     * @var array<class-string, bool>
     */
    private array $autowiredClasses = [];

    /**
     * Marks object argument to be auto-resolved
     * from another object reference in container
     *
     * @var array<class-string, array<string,class-string>>
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
     *
     * @param class-string<T> $type
     * @param array<string,mixed> $arguments
     *
     * @template T of object
     * @return   T
     */
    public function create($type, array $arguments = [])
    {
        return $this->createObject($type, $arguments);
    }

    /**
     * Returns shared default version of the object
     *
     * @param class-string<T> $type
     *
     * @return   T
     * @template T of object
     */
    public function get($type): object
    {
        if ($type === ObjectManagerInterface::class) {
            // @phpstan-ignore-next-line
            return $this;
        }

        if (!isset($this->instances[$type])) {
            $this->instances[$type] = $this->createObject($type);
        }

        // @phpstan-ignore-next-line
        return $this->instances[$type];
    }

    /**
     * Stub for configure
     *
     * @param array<string, mixed> $configuration
     */
    public function configure(array $configuration)
    {
        throw new LogicException('Fake ObjectManager does not support native configure, use with other method for configuring it');
    }

    /**
     * Adds an object to internal registries
     *
     * @param class-string<T> $type
     * @param T $instance
     *
     * @template T of object
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
     * @param callable(ObjectManager, array<string,mixed>): T $factory
     * @param class-string<T> $type
     *
     * @template T of object
     */
    public function withFactory(string $type, callable $factory): self
    {
        $objectManager = clone $this;
        $objectManager->factories[$type] = $factory;
        return $objectManager;
    }

    /**
     * Adds a factory for a specific type an object manager
     *
     * @param class-string<T> $type
     * @param array<string, mixed> $arguments
     *
     * @template T of object
     */
    public function withDefaultArguments(string $type, array $arguments): self
    {
        $objectManager = clone $this;
        $objectManager->defaultArguments[$type] = $arguments;
        return $objectManager;
    }

    /**
     * Adds a auto-resolver for specific argument
     *
     * @param class-string<T> $type
     * @param class-string<R> $reference
     *
     * @template T of object
     * @template R of object
     */
    public function withAutoResolvedArgument(string $type, string $name, string $reference): self
    {
        $objectManager = clone $this;
        $objectManager->autoResolvedArguments[$type][$name] = $reference;
        return $objectManager;
    }

    /**
     * Creates an instance of an object
     *
     * @param class-string<T> $type
     * @param null|array<string,mixed> $arguments
     *
     * @return   T
     * @template T of object
     */
    private function createObject(string $type, ?array $arguments = null): object
    {
        if (isset($this->factories[$type])) {
            // @phpstan-ignore-next-line
            return $this->factories[$type]($this, ($arguments ?? []) + ($this->defaultArguments[$type] ?? []));
        }

        return $this->createWithReflection($type, $arguments);
    }

    /**
     * Marks object as autowired
     *
     * @param class-string<T> $type
     *
     * @template T of object
     */
    public function withAutowire(string $type): self
    {
        $object = clone $this;
        $object->autowiredClasses[$type] = true;
        return $object;
    }

    /**
     * Used to create objects with autowired dependencies
     *
     * @param class-string<T> $type
     * @param null|array<string,mixed> $arguments
     *
     * @return     T
     * @template   T of object
     * @deprecated 2.1.0 will be removed in 3.0.0
     *
     * @see self::withAutowire for make sure object is autowired on get calls
     * @see self::create for creating objects manually
     */
    public function createAutowiredObject(string $type, ?array $arguments = null): object
    {
        return $this->createWithReflection($type, $arguments);
    }

    /**
     * Used to create objects with autowired dependencies
     *
     * @param class-string<T> $type
     * @param null|array<string,mixed> $arguments
     *
     * @return   T
     * @template T of object
     */
    private function createWithReflection(string $type, ?array $arguments = null): object
    {
        $class = new ReflectionClass($type);

        $constructor = $class->getConstructor();

        if ($arguments === null && !empty($this->autowiredClasses[$type])) {
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
                if ($parameterType instanceof ReflectionIntersectionType || $parameterType instanceof ReflectionUnionType) {
                    throw new \InvalidArgumentException(
                        sprintf(
                            'Argument $%s of intersection or union cannot be resolved for %s',
                            $parameter->getName(),
                            $class->getName()
                        )
                    );
                }

                if (!$parameterType instanceof ReflectionNamedType || $parameterType->isBuiltin()) {
                    throw new \InvalidArgumentException(
                        sprintf(
                            'Argument $%s is required for instantiating %s object but it is impossible to resolve it',
                            $parameter->getName(),
                            $class->getName()
                        )
                    );
                }

                // @phpstan-ignore-next-line
                $constructorArgs[] = $this->get($parameterType->getName());
            }
        }

        return $class->newInstanceArgs($constructorArgs);
    }
}
