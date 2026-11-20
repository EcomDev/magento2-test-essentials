<?php

/**
 * Copyright © EcomDev B.V. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace EcomDev\Magento2TestEssentials {

    use PHPUnit\Framework\Assert;

    class DependencyOne
    {
    }

    class DependencyTwo
    {
    }

    readonly class DependencyWithStringArgument
    {
        public function __construct(private string $someString)
        {
        }
    }

    readonly class ObjectWithDependencyOneAndTwo
    {
        public function __construct(
            private DependencyOne $dependencyOne,
            private DependencyTwo $dependencyTwo,
            private string $someText = ''
        ) {
        }

        public function assertSame(ObjectWithDependencyOneAndTwo $other): void
        {
            Assert::assertSame($this->dependencyOne, $other->dependencyOne);
            Assert::assertSame($this->dependencyTwo, $other->dependencyTwo);
            Assert::assertSame($this->someText, $other->someText);
        }
    }

    readonly class ObjectWithOptionalDependencyTwo
    {
        public function __construct(
            private DependencyOne $dependencyOne,
            private ?DependencyTwo $dependencyTwo = null
        ) {
        }
    }
}

namespace Magento\Store\Api\Data {
    interface StoreExtensionInterface
    {
    }

    interface GroupExtensionInterface
    {
    }

    interface WebsiteExtensionInterface
    {
    }
}
