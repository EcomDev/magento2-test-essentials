<?php

/**
 * Copyright © EcomDev B.V. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace EcomDev\Magento2TestEssentials\Store;

use Magento\Store\Api\Data\StoreExtensionInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class StoreTest extends TestCase
{
    #[Test]
    public function returnsPassedStoreId()
    {
        $this->assertEquals(
            1,
            Store::new(1, 'one')->getId()
        );
    }

    #[Test]
    public function returnsPassedStoreCode()
    {
        $this->assertEquals(
            'two',
            Store::new(2, 'two')->getCode()
        );
    }

    #[Test]
    public function nameDefaultsToGeneratedValueFromCode()
    {
        $this->assertEquals(
            'Store Three',
            Store::new(3, 'three')->getName()
        );
    }

    #[Test]
    public function customNameOverridesGeneratedValue()
    {
        $this->assertEquals(
            'Custom Name',
            Store::new(3, 'three')
                ->withName('Custom Name')
                ->getName()
        );
    }

    #[Test]
    public function defaultsToAdminWebsite()
    {
        $this->assertEquals(
            0,
            Store::new(1, 'code')->getWebsiteId()
        );
    }

    #[Test]
    public function defaultsToAdminStore()
    {
        $this->assertEquals(
            0,
            Store::new(1, 'code')->getStoreGroupId()
        );
    }

    #[Test]
    public function overridesWebsiteWithCustomValue()
    {
        $this->assertEquals(
            1,
            Store::new(1, 'code')
                ->withWebsite(1, 2)
                ->getWebsiteId()
        );
    }

    #[Test]
    public function overridesStoreGroupWithCustomValue()
    {
        $this->assertEquals(
            2,
            Store::new(1, 'code')
                ->withWebsite(1, 2)
                ->getStoreGroupId()
        );
    }

    #[Test]
    public function defaultsToEnabledStoreView()
    {
        $this->assertEquals(
            1,
            Store::new(1, 'one')->getIsActive()
        );
    }

    #[Test]
    public function disablesStoreView()
    {
        $this->assertEquals(
            0,
            Store::new(1, 'one')
                ->withDeactivatedState()
                ->getIsActive()
        );
    }

    #[Test]
    public function mutatesStoreData()
    {
        $store = Store::new(1, 'one');
        $store->setId(2)
            ->setCode('two')
            ->setWebsiteId(2)
            ->setStoreGroupId(3)
            ->setIsActive(0)
            ->setName('Store Number Two');

        $this->assertEquals(
            Store::new(2, 'two')
                ->withName('Store Number Two')
                ->withWebsite(2, 3)
                ->withDeactivatedState(),
            $store
        );
    }

    #[Test]
    public function defaultsExtensionAttributesToNull()
    {
        $this->assertSame(
            null,
            Store::new(1, "one")->getExtensionAttributes()
        );
    }

    #[Test]
    public function allowsToSetExtensionAttributesOnStore()
    {
        $value = new class implements StoreExtensionInterface {
        };

        $this->assertSame(
            $value,
            Store::new(1, 'two')
                ->setExtensionAttributes($value)
                ->getExtensionAttributes()
        );
    }
}
