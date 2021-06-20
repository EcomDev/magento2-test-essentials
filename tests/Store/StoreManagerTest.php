<?php
/**
 * Copyright © EcomDev B.V. All rights reserved.
 * See LICENSE for license details.
 */
declare(strict_types=1);

namespace EcomDev\Magento2TestEssentials\Store;

use PHPUnit\Framework\TestCase;

class StoreManagerTest extends TestCase
{
    /** @test */
    public function containsAdminWebsiteByDefault()
    {
        $this->assertEquals(
            [
                Website::new(0, 'base')
            ],
            StoreManager::new()->getWebsites(true)
        );
    }

    /** @test */
    public function returnsAddedWebsitesGroupedById()
    {
        $this->assertEquals(
            [
                2 => Website::new(2, 'two'),
                3 => Website::new(3, 'three'),
                5 => Website::new(5, 'five'),
            ],
            StoreManager::new()
                ->withWebsite(Website::new(2, 'two'))
                ->withWebsite(Website::new(3, 'three'))
                ->withWebsite(Website::new(5, 'five'))
                ->getWebsites()
        );
    }

    /** @test */
    public function returnsAddedWebsitesGroupedByCode()
    {
        $this->assertEquals(
            [
                'base' => Website::new(0, 'base'),
                'two' => Website::new(2, 'two'),
                'three' => Website::new(3, 'three'),
                'five' => Website::new(5, 'five'),
            ],
            StoreManager::new()
                ->withWebsite(Website::new(2, 'two'))
                ->withWebsite(Website::new(3, 'three'))
                ->withWebsite(Website::new(5, 'five'))
                ->getWebsites(true, true)
        );
    }

    /** @test */
    public function returnsWebsiteById()
    {
        $this->assertEquals(
            Website::new(3, 'three'),
            StoreManager::new()
                ->withWebsite(Website::new(2, 'two'))
                ->withWebsite(Website::new(3, 'three'))
                ->withWebsite(Website::new(5, 'five'))
                ->getWebsite(3)
        );
    }

    /** @test */
    public function returnsWebsiteByCode()
    {
        $this->assertEquals(
            Website::new(2, 'two'),
            StoreManager::new()
                ->withWebsite(Website::new(2, 'two'))
                ->withWebsite(Website::new(3, 'three'))
                ->withWebsite(Website::new(5, 'five'))
                ->getWebsite('two')
        );
    }

    /** @test */
    public function returnsDefaultWebsiteWithTrueAsAnArgument()
    {
        $this->assertEquals(
            Website::new(3, 'three'),
            StoreManager::new()
                ->withWebsite(Website::new(2, 'two'))
                ->withWebsite(Website::new(3, 'three'))
                ->withWebsite(Website::new(5, 'five'))
                ->withDefaultWebsite(3)
                ->getWebsite(true)
        );
    }

    /** @test */
    public function containsAdminStoreByDefault()
    {
        $this->assertEquals(
            [
                Store::new(0, 'admin')
            ],
            StoreManager::new()
                ->getStores(true)
        );
    }

    /** @test */
    public function returnsStoresByIds()
    {
        $this->assertEquals(
            [
                2 => Store::new(2, 'two'),
                4 => Store::new(4, 'four'),
                6 => Store::new(6, 'six'),
            ],
            StoreManager::new()
                ->withStore(Store::new(2, 'two'))
                ->withStore(Store::new(4, 'four'))
                ->withStore(Store::new(6, 'six'))
                ->getStores()
        );
    }

    /** @test */
    public function returnsStoresByCode()
    {
        $this->assertEquals(
            [
                'admin' => Store::new(0, 'admin'),
                'two' => Store::new(2, 'two'),
                'four' => Store::new(4, 'four'),
                'six' => Store::new(6, 'six'),
            ],
            StoreManager::new()
                ->withStore(Store::new(2, 'two'))
                ->withStore(Store::new(4, 'four'))
                ->withStore(Store::new(6, 'six'))
                ->getStores(true, true)
        );
    }

    /** @test */
    public function returnsStoreById()
    {
        $this->assertEquals(
            Store::new(4, 'four'),
            StoreManager::new()
                ->withStore(Store::new(2, 'two'))
                ->withStore(Store::new(4, 'four'))
                ->withStore(Store::new(6, 'six'))
                ->getStore(4)
        );
    }

    /** @test */
    public function returnsStoreByCode()
    {
        $this->assertEquals(
            Store::new(2, 'two'),
            StoreManager::new()
                ->withStore(Store::new(2, 'two'))
                ->withStore(Store::new(4, 'four'))
                ->withStore(Store::new(6, 'six'))
                ->getStore('two')
        );
    }

    /** @test */
    public function returnsCurrentlySetStoreView()
    {
        $this->assertEquals(
            Store::new(2, 'two'),
            StoreManager::new()
                ->withStore(Store::new(2, 'two'))
                ->withStore(Store::new(4, 'four'))
                ->withStore(Store::new(6, 'six'))
                ->setCurrentStore(2)
                ->getStore()
        );
    }

    /** @test */
    public function currentStoreViewAffectsCurrentWebsite()
    {
        $this->assertEquals(
            Website::new(2, 'website_two'),
            StoreManager::new()
                ->withWebsite(Website::new(2, 'website_two'))
                ->withStore(Store::new(2, 'two')->withWebsite(2, 0))
                ->withStore(Store::new(4, 'four')->withWebsite(2, 0))
                ->withStore(Store::new(6, 'six'))
                ->setCurrentStore(4)
                ->getWebsite()
        );
    }

    /** @test */
    public function containsAdminStoreGroupByDefault()
    {
        $this->assertEquals(
            [
                StoreGroup::new(0, 'admin')
            ],
            StoreManager::new()
                ->getGroups(true)
        );
    }

    /** @test */
    public function returnsStoreGroupsById()
    {
        $this->assertEquals(
            [
                1 => StoreGroup::new(1, 'one'),
                3 => StoreGroup::new(3, 'three'),
                5 => StoreGroup::new(5, 'five'),
            ],
            StoreManager::new()
                ->withGroup(StoreGroup::new(1, 'one'))
                ->withGroup(StoreGroup::new(3, 'three'))
                ->withGroup(StoreGroup::new(5, 'five'))
                ->getGroups()
        );
    }
}
