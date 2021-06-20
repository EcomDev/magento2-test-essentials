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

    class DependencyWithStringArgument
    {
        private $someString;

        public function __construct(string $someString)
        {

            $this->someString = $someString;
        }
    }

    class ObjectWithDependencyOneAndTwo
    {
        private $dependencyOne;
        private $dependencyTwo;
        private $someText;

        public function __construct(DependencyOne $dependencyOne, DependencyTwo $dependencyTwo, string $someText = '')
        {
            $this->dependencyOne = $dependencyOne;
            $this->dependencyTwo = $dependencyTwo;
            $this->someText = $someText;
        }

        public function assertSame(ObjectWithDependencyOneAndTwo $other): void
        {
            Assert::assertSame($this->dependencyOne, $other->dependencyOne);
            Assert::assertSame($this->dependencyTwo, $other->dependencyTwo);
            Assert::assertSame($this->someText, $other->someText);
        }
    }
}

namespace Magento\Store\Api\Data {
    interface StoreExtensionInterface {

    }

    interface GroupExtensionInterface {

    }

    interface WebsiteExtensionInterface {

    }
}
