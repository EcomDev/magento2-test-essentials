<?php
/**
 * Copyright © EcomDev B.V. All rights reserved.
 * See LICENSE for license details.
 */
declare(strict_types=1);

namespace EcomDev\Magento2TestEssentials\Store;

use EcomDev\Magento2TestEssentials\Store\StoreGroup;
use Magento\Store\Api\Data\GroupExtensionInterface;
use PHPUnit\Framework\TestCase;

class StoreGroupTest extends TestCase
{
    /** @test */
    public function usesIdFromArguments()
    {
        $this->assertEquals(
            1,
            StoreGroup::new(1, 'one')
                ->getId()
        );
    }

    /** @test */
    public function usesCodeFromArguments()
    {
        $this->assertEquals(
            'two',
            StoreGroup::new(2, "two")
                ->getCode()
        );
    }

    /** @test */
    public function defaultsNameValueBasedOnCode()
    {
        $this->assertEquals(
            'Store Group Three',
            StoreGroup::new(3, 'three')
                ->getName()
        );
    }

    /** @test */
    public function overridesName()
    {
        $this->assertEquals(
            'Custom Name Value',
            StoreGroup::new(4, 'four')
                ->withName('Custom Name Value')
                ->getName()
        );
    }

    /** @test */
    public function defaultsToAdminWebsite()
    {
        $this->assertEquals(
            0,
            StoreGroup::new(2, 'two')->getWebsiteId()
        );
    }

    /** @test */
    public function overridesWebsiteUsed()
    {
        $this->assertEquals(
            99,
            StoreGroup::new(1, 'one')
                ->withWebsite(99)
                ->getWebsiteId()
        );
    }

    /** @test */
    public function defaultsToStandardRootCategory()
    {
        $this->assertEquals(
            2,
            StoreGroup::new(1, 'one')
                ->getRootCategoryId()
        );
    }

    /** @test */
    public function overridesRootCategory()
    {
        $this->assertEquals(
            103,
            StoreGroup::new(2, 'two')
                ->withRootCategory(103)
                ->getRootCategoryId()
        );
    }

    /** @test */
    public function defaultsToAdminStoreView()
    {
        $this->assertEquals(
            0,
            StoreGroup::new(2, 'two')
                ->getDefaultStoreId()
        );
    }

    /** @test */
    public function overridesDefaultStore()
    {
        $this->assertEquals(
            4,
            StoreGroup::new(2, 'two')
                ->withDefaultStore(4)
                ->getDefaultStoreId()
        );
    }

    /** @test */
    public function mutatesStoreGroup()
    {
        $storeGroup = StoreGroup::new(1, 'one');
        $storeGroup->setId(2)
            ->setCode('two')
            ->setName('Custom Second Name')
            ->setWebsiteId(3)
            ->setDefaultStoreId(4)
            ->setRootCategoryId(30);

        $this->assertEquals(
            StoreGroup::new(2, 'two')
                ->withWebsite(3)
                ->withDefaultStore(4)
                ->withRootCategory(30)
                ->withName('Custom Second Name'),
            $storeGroup
        );
    }

    /** @test */
    public function defaultsExtensionAttributesToNull()
    {
        $this->assertSame(
            null,
            StoreGroup::new(1, 'one')
                ->getExtensionAttributes()
        );
    }

    /** @test */
    public function setsCustomExtensionAttributes()
    {
        $value = new class implements GroupExtensionInterface {

        };

        $this->assertSame(
            $value,
            StoreGroup::new(1, 'one')
                ->setExtensionAttributes($value)
                ->getExtensionAttributes()
        );
    }
}
