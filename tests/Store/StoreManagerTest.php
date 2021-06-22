<?php
/**
 * Copyright © EcomDev B.V. All rights reserved.
 * See LICENSE for license details.
 */
declare(strict_types=1);

namespace EcomDev\Magento2TestEssentials\Store;

use Magento\Framework\Exception\NoSuchEntityException;
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
    public function currentStoreViewCanBeSetFromStoreCode()
    {
        $this->assertEquals(
            Store::new(6, "six"),
            StoreManager::new()
                ->withStore(Store::new(2, 'two'))
                ->withStore(Store::new(4, 'four'))
                ->withStore(Store::new(6, 'six'))
                ->setCurrentStore("six")
                ->getStore()
        );
    }

    /** @test */
    public function errorsOutWhenStoreDoesNotExistsById()
    {
        $this->expectException(NoSuchEntityException::class);
        $this->expectExceptionMessage("The store that was requested wasn't found. Verify the store and try again.");

        StoreManager::new()
            ->getStore(13);
    }

    /** @test */
    public function errorsOutWhenStoreDoesNotExistsByCode()
    {
        $this->expectException(NoSuchEntityException::class);
        $this->expectExceptionMessage("The store that was requested wasn't found. Verify the store and try again.");

        StoreManager::new()
            ->getStore("three");
    }

    /** @test */
    public function returnsDefaultStoreViewFromDefaultWebsite()
    {
        $storeManager = StoreManager::new()
            ->withWebsite(Website::new(1, 'one')->withDefaultGroup(1))
            ->withGroup(StoreGroup::new(1, 'one')->withDefaultStore(1))
            ->withStore(Store::new(1, 'one'))
            ->withStore(Store::new(2, 'two'))
            ->withDefaultWebsite(1);

        $this->assertEquals(
            Store::new(1, 'one'),
            $storeManager->getStore(true)
        );
    }

    /** @test */
    public function errorsOutWhenStoreCannotBeFoundFromDefaultWebsite()
    {
        $this->expectException(NoSuchEntityException::class);
        $this->expectExceptionMessage("The store that was requested wasn't found. Verify the store and try again.");

        StoreManager::new()
            ->withWebsite(Website::new(1, 'one')->withDefaultGroup(1))
            ->withGroup(StoreGroup::new(1, 'one')->withDefaultStore(4))
            ->withStore(Store::new(1, 'one'))
            ->withStore(Store::new(2, 'two'))
            ->withDefaultWebsite(1)
            ->getStore(true);
    }

    /** @test */
    public function errorsOutCurrentStoreCannotBeFound()
    {
        $this->expectException(NoSuchEntityException::class);
        $this->expectExceptionMessage("The store that was requested wasn't found. Verify the store and try again.");

        StoreManager::new()
            ->withStore(Store::new(1, 'one'))
            ->withStore(Store::new(2, 'two'))
            ->setCurrentStore(null)
            ->getStore();
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

    /** @test */
    public function returnsSingleStoreGroupById()
    {
        $this->assertEquals(
            StoreGroup::new(3, 'three'),
            StoreManager::new()
                ->withGroup(StoreGroup::new(1, 'one'))
                ->withGroup(StoreGroup::new(3, 'three'))
                ->withGroup(StoreGroup::new(5, 'five'))
                ->getGroup(3)
        );
    }

    /** @test */
    public function defaultsToAdminStore()
    {
        $defaultStore = StoreManager::new()
            ->withWebsite(Website::new(1, 'one'))
            ->withGroup(StoreGroup::new(1, 'one'))
            ->withStore(Store::new(1, 'one'))
            ->getDefaultStoreView();

        $this->assertEquals(
            Store::new(0, 'admin'),
            $defaultStore
        );
    }

    /** @test */
    public function usesStoreFromDefaultWebsite()
    {
        $defaultStore = StoreManager::new()
            ->withWebsite(Website::new(1, 'one')->withDefaultGroup(1))
            ->withGroup(StoreGroup::new(1, 'one')->withDefaultStore(1))
            ->withStore(Store::new(1, 'one'))
            ->withStore(Store::new(2, 'two'))
            ->withDefaultWebsite(1)
            ->getDefaultStoreView();

        $this->assertEquals(
            Store::new(1, 'one'),
            $defaultStore
        );
    }

    /** @test */
    public function usesGroupFromCurrentStoreWhenAccessedWithoutArguments()
    {
        $storeManager = StoreManager::new()
            ->withWebsite(Website::new(1, 'one')->withDefaultGroup(1))
            ->withWebsite(Website::new(2, 'two')->withDefaultGroup(2))
            ->withGroup(StoreGroup::new(1, 'one')->withDefaultStore(1))
            ->withGroup(StoreGroup::new(2, 'two')->withDefaultStore(2))
            ->withStore(Store::new(1, 'one'))
            ->withStore(Store::new(2, 'two')->withWebsite(2, 2))
            ->withDefaultWebsite(1)
            ->setCurrentStore(2);

        $this->assertEquals(
            StoreGroup::new(2, 'two')->withDefaultStore(2),
            $storeManager->getGroup()
        );
    }

    /** @test */
    public function usesGroupFromDefaultWebsiteWithTrueArgument()
    {
        $storeManager = StoreManager::new()
            ->withWebsite(Website::new(1, 'one')->withDefaultGroup(1))
            ->withWebsite(Website::new(2, 'two')->withDefaultGroup(2))
            ->withWebsite(Website::new(3, 'three')->withDefaultGroup(3))
            ->withGroup(StoreGroup::new(1, 'one'))
            ->withGroup(StoreGroup::new(2, 'two'))
            ->withGroup(StoreGroup::new(3, 'three'))
            ->withDefaultWebsite(3);

        $this->assertEquals(
            StoreGroup::new(3, 'three'),
            $storeManager->getGroup(true)
        );
    }

    /** @test */
    public function websiteErrorsOutWhenDefaultWebsiteDoesNotExists()
    {
        $this->expectException(NoSuchEntityException::class);
        $this->expectExceptionMessage("The website that was requested wasn't found. Verify the website and try again.");

        $storeManager = StoreManager::new()
            ->withWebsite(Website::new(1, 'one'))
            ->withDefaultWebsite(3);

        $storeManager->getWebsite(true);
    }

    /** @test */
    public function storeGroupErrorsOutWhenDefaultWebsiteDoesNotExists()
    {
        $this->expectException(NoSuchEntityException::class);
        $this->expectExceptionMessage("The website that was requested wasn't found. Verify the website and try again.");

        $storeManager = StoreManager::new()
            ->withWebsite(Website::new(1, 'one')->withDefaultGroup(1))
            ->withGroup(StoreGroup::new(1, 'one'))
            ->withDefaultWebsite(3);


        $storeManager->getGroup(true);
    }

    /** @test */
    public function storeGroupErrorsOutWhenDefaultGroupDoesNotExists()
    {
        $this->expectException(NoSuchEntityException::class);
        $this->expectExceptionMessage(
            "The store group that was requested wasn't found. Verify the store group and try again."
        );

        $storeManager = StoreManager::new()
            ->withWebsite(Website::new(1, 'one')->withDefaultGroup(3))
            ->withGroup(StoreGroup::new(1, 'one'))
            ->withDefaultWebsite(1);

        $storeManager->getGroup(true);
    }

    /** @test */
    public function resetsCurrentStoreViewOnReInitStoresCall()
    {
        $storeManager = StoreManager::new()
            ->withWebsite(Website::new(1, 'one')->withDefaultGroup(3))
            ->withGroup(StoreGroup::new(1, 'one'))
            ->withStore(Store::new(1, 'one'))
            ->withDefaultWebsite(1);

        $storeManagerWithCurrentStore = clone $storeManager;
        $storeManagerWithCurrentStore->setCurrentStore(1);
        $storeManagerWithCurrentStore->reinitStores();

        $this->assertEquals($storeManager, $storeManagerWithCurrentStore);
    }

    /** @test */
    public function notTheSingleStoreModeWhenEnoughStoresAreAvailable()
    {
        $storesManager = StoreManager::new()
            ->withStore(Store::new(1, 'one'))
            ->withStore(Store::new(2, 'two'))
        ;

        $this->assertFalse(
            $storesManager->isSingleStoreMode()
        );
    }

    /** @test */
    public function reportsSingleStoreModeWhenOneStoreIsAvailable()
    {
        $storesManager = StoreManager::new()
            ->withStore(Store::new(2, 'two'))
        ;

        $this->assertTrue(
            $storesManager->isSingleStoreMode()
        );
    }

    /** @test */
    public function returnsSingleStoreAsCurrentWhenSingleStore()
    {
        $storesManager = StoreManager::new()
            ->withStore(Store::new(2, 'two'))
        ;

        $this->assertEquals(
            Store::new(2, 'two'),
            $storesManager->getStore()
        );
    }

    /** @test */
    public function reportsHasSingleStoreWhenOneIsAvailable()
    {
        $storesManager = StoreManager::new()
            ->withStore(Store::new(2, 'two'))
        ;

        $this->assertTrue(
            $storesManager->hasSingleStore()
        );
    }

    /** @test */
    public function reportsDoesNotHaveSingleStoreWhenMoreThenOneIsAvailable()
    {
        $storesManager = StoreManager::new()
            ->withStore(Store::new(2, 'two'))
            ->withStore(Store::new(3, 'three'))
        ;

        $this->assertFalse(
            $storesManager->hasSingleStore()
        );
    }

    /** @test */
    public function disablesSingleStoreModeCheckAndAlwaysReturnsTrue()
    {
        $storesManager = StoreManager::new()
            ->withStore(Store::new(2, 'two'));

        $storesManager->setIsSingleStoreModeAllowed(false);

        $this->assertFalse($storesManager->isSingleStoreMode());
    }
}
