<?php

/**
 * Copyright © EcomDev B.V. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace EcomDev\Magento2TestEssentials;

use EcomDev\Magento2TestEssentials\Store\Store;
use EcomDev\Magento2TestEssentials\Store\StoreManager;
use EcomDev\Magento2TestEssentials\Store\Website;
use Magento\Store\Model\ScopeInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

class ScopeConfigTest extends TestCase
{
    #[Test]
    public function simpleDefaultValueRetrieval()
    {
        $this->assertEquals(
            'test',
            ScopeConfig::new()
                ->withDefaultValue('some/thing/enabled', 'test')
                ->getValue('some/thing/enabled')
        );
    }

    #[Test]
    public function defaultValueOverridenByWebsiteScope()
    {
        $this->assertEquals(
            'base_website_value',
            ScopeConfig::new()
                ->withStoreManager(
                    StoreManager::new()
                        ->withWebsite(
                            Website::new(1, 'base')
                        )
                )
                ->withDefaultValue('some/other/value', 'default_value')
                ->withWebsiteValue('base', 'some/other/value', 'base_website_value')
                ->withWebsiteValue('default', 'some/other/value', 'default_website_value')
                ->getValue('some/other/value', ScopeInterface::SCOPE_WEBSITE, 'base')
        );
    }

    #[Test]
    public function configurationValuesMergedFromRightStoreChain()
    {
        $this->assertEquals(
            [
                'path_one' => 'default_one_value',
                'path_two' => 'base_website_value',
                'path_three' => 'default_store_value',
            ],
            ScopeConfig::new()
                ->withStoreManager(
                    StoreManager::new()
                        ->withWebsite(
                            Website::new(1, 'base')
                        )
                        ->withStore(
                            Store::new(1, 'default')
                                ->withWebsite(1, 1)
                        )
                        ->withStore(
                            Store::new(2, 'english')
                                ->withWebsite(1, 1)
                        )
                )
                ->withDefaultValue('some/other/path_one', 'default_one_value')
                ->withDefaultValue('some/other/path_two', 'default_one_value')
                ->withDefaultValue('some/other/path_three', 'default_one_value')
                ->withWebsiteValue('base', 'some/other/path_two', 'base_website_value')
                ->withStoreValue('default', 'some/other/path_three', 'default_store_value')
                ->withStoreValue('english', 'some/other/path_three', 'eng_store_value')
                ->getValue('some/other', ScopeInterface::SCOPE_STORE, 'default')
        );
    }

    #[Test]
    public function mergesValuesOnlyIfBothPathsAreArraysOrOneIsNull()
    {
        $config = ScopeConfig::new()
            ->withStoreManager(
                StoreManager::new()
                    ->withWebsite(
                        Website::new(1, 'base')
                    )
                    ->withStore(
                        Store::new(1, 'default')
                            ->withWebsite(1, 1)
                    )
                    ->withStore(
                        Store::new(2, 'english')
                            ->withWebsite(1, 1)
                    )
            )
            ->withDefaultValue('some/other', 'default_one_value')
            ->withWebsiteValue('base', 'some/other/path_two', 'base_website_value')
            ->withStoreValue('default', 'some/other/path_three', 'default_store_value')
            ->withStoreValue('english', 'some/other', null);

        $this->assertEquals(
            [
                'path_two' => 'base_website_value',
                'path_three' => 'default_store_value',
            ],
            $config->getValue('some/other', ScopeInterface::SCOPE_STORE, 'default')
        );

        $this->assertEquals(
            [
                'path_two' => 'base_website_value'
            ],
            $config->getValue('some/other', ScopeInterface::SCOPE_STORE, 'english')
        );
    }

    #[Test]
    public function castsOnlyTruthyValuesInFlagCheck()
    {
        $config = ScopeConfig::new()
                ->withDefaultValue('some/flag/one', 'true')
                ->withDefaultValue('some/flag/two', '1')
                ->withDefaultValue('some/flag/three', '0')
                ->withDefaultValue('some/flag/four', 'false')
                ->withDefaultValue('some/flag/five', '')
        ;
        $this->assertEquals(
            [
                true,
                true,
                false,
                true,
                false
            ],
            [
                $config->isSetFlag('some/flag/one'),
                $config->isSetFlag('some/flag/two'),
                $config->isSetFlag('some/flag/three'),
                $config->isSetFlag('some/flag/four'),
                $config->isSetFlag('some/flag/five'),
            ]
        );
    }
}