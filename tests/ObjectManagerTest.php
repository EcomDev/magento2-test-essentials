<?php

/**
 * Copyright © EcomDev B.V. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace EcomDev\Magento2TestEssentials;

use InvalidArgumentException;
use LogicException;
use Magento\Catalog\Model\Product;
use Magento\Framework\DataObject;
use Magento\Framework\ObjectManagerInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ObjectManagerTest extends TestCase
{
    #[Test]
    public function returnsSelfWhenObjectManagerInterfaceRequested()
    {
        $objectManager = ObjectManager::new();

        $this->assertSame($objectManager, $objectManager->get(ObjectManagerInterface::class));
    }

    #[Test]
    public function instantiatesComplexObjectFromCoreWithoutInvokingConstructor()
    {
        $objectManager = ObjectManager::new();

        $this->assertInstanceOf(Product::class, $objectManager->get(Product::class));
    }

    #[Test]
    public function createsDependenciesFromArguments()
    {
        $objectManager = ObjectManager::new();

        $this->assertEquals(
            new DataObject(['key1' => 'value1']),
            $objectManager->create(DataObject::class, ['data' => ['key1' => 'value1']])
        );
    }

    #[Test]
    public function createsDependencyWithComplexArgumentsAutomaticallyResolved()
    {
        $objectManager = ObjectManager::new();

        $this->assertEquals(
            new ObjectWithDependencyOneAndTwo(new DependencyOne(), new DependencyTwo()),
            $objectManager->create(ObjectWithDependencyOneAndTwo::class)
        );
    }

    #[Test]
    public function createsObjectWithoutConstructor()
    {
        $objectManger = ObjectManager::new();
        $this->assertEquals(new DependencyOne(), $objectManger->create(DependencyOne::class));
    }

    #[Test]
    public function createsInstanceOnlyOnceOnGet()
    {
        $objectManager = ObjectManager::new();

        $this->assertSame(
            $objectManager->get(DependencyOne::class),
            $objectManager->get(DependencyOne::class)
        );
    }

    #[Test]
    public function createsNewInstanceEachTimeOnCreate()
    {
        $objectManager = ObjectManager::new();

        $this->assertNotSame(
            $objectManager->create(DependencyOne::class),
            $objectManager->create(DependencyOne::class)
        );
    }

    #[Test]
    public function errorsOnConfigurationArrayChange()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Fake ObjectManager does not support native configure');
        ObjectManager::new()->configure([]);
    }

    #[Test]
    public function usesProvidedInstanceWhenGettingType()
    {
        $instance = new DependencyOne();

        $objectManager = ObjectManager::new()
            ->withObject(DependencyOne::class, $instance);

        $this->assertEquals($instance, $objectManager->get(DependencyOne::class));
    }

    #[Test]
    public function usesProvidedInstanceWhenCreatingTypeWithItAsDependency()
    {
        $instanceOne = new DependencyOne();
        $instanceTwo = new DependencyTwo();

        $objectManager = ObjectManager::new()
            ->withObject(DependencyOne::class, $instanceOne)
            ->withObject(DependencyTwo::class, $instanceTwo);

        $objectWithDependency = new ObjectWithDependencyOneAndTwo($instanceOne, $instanceTwo);
        $objectWithDependency->assertSame(
            $objectManager->create(ObjectWithDependencyOneAndTwo::class)
        );
    }

    #[Test]
    public function usesCustomFactoryForObjectCreation()
    {
        $objectManager = ObjectManager::new()
            ->withFactory(
                ObjectWithDependencyOneAndTwo::class,
                function (ObjectManagerInterface $objectManager, array $arguments) {
                    return new ObjectWithDependencyOneAndTwo(
                        $objectManager->get(DependencyOne::class),
                        $objectManager->get(DependencyTwo::class),
                        $arguments['someText'] ?? 'factoryText'
                    );
                }
            );

        $this->assertEquals(
            [
                new ObjectWithDependencyOneAndTwo(new DependencyOne(), new DependencyTwo(), 'factoryText'),
                new ObjectWithDependencyOneAndTwo(new DependencyOne(), new DependencyTwo(), 'my_custom_text'),
            ],
            [
                $objectManager->create(ObjectWithDependencyOneAndTwo::class),
                $objectManager->create(ObjectWithDependencyOneAndTwo::class, ['someText' => 'my_custom_text'])
            ]
        );
    }

    #[Test]
    public function usesCustomFactoryForSharedObjectCreation()
    {
        $objectManager = ObjectManager::new()
            ->withFactory(
                ObjectWithDependencyOneAndTwo::class,
                function (ObjectManagerInterface $objectManager, array $arguments) {
                    return new ObjectWithDependencyOneAndTwo(
                        $objectManager->get(DependencyOne::class),
                        $objectManager->get(DependencyTwo::class),
                        'sharedObject'
                    );
                }
            );

        $this->assertEquals(
            new ObjectWithDependencyOneAndTwo(new DependencyOne(), new DependencyTwo(), 'sharedObject'),
            $objectManager->get(ObjectWithDependencyOneAndTwo::class)
        );
    }

    #[Test]
    public function creationOfObjectWithScalarArgumentFailsWhenNoValueProvided()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Argument \$someString is required");

        $objectManager = ObjectManager::new();
        $objectManager->create(DependencyWithStringArgument::class);
    }
}
