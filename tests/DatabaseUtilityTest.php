<?php

/**
 * Copyright (c) 2026 EcomDev B.V.
 *
 * This file is dual-licensed under:
 * 1. GNU General Public License v3.0 (GPL-3.0-or-later) - see LICENSE
 * 2. Commercial License - see COMMERCIAL-LICENSE.md
 *
 * SPDX-License-Identifier: GPL-3.0-or-later OR LicenseRef-Commercial
 */

declare(strict_types=1);

namespace EcomDev\Magento2TestEssentials;

use EcomDev\TestContainers\MagentoData\DbContainer;
use EcomDev\TestContainers\MagentoData\DbContainerBuilder;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class DatabaseUtilityTest extends TestCase
{
    private static ?DbContainer $container;

    private DatabaseUtility $databaseUtility;
    private ObjectManager $objectManager;

    public static function setUpBeforeClass(): void
    {
        self::$container = DbContainerBuilder::mysql()
            ->withSampleData()
            ->withMagentoVersion('2.4.9')
            ->build();
    }

    public static function tearDownAfterClass(): void
    {
        self::$container = null;
    }

    protected function setUp(): void
    {
        $connection = self::$container->getConnectionSettings();
        $this->objectManager = IntegrationUtility::setupDatabaseObjects(
            DeploymentConfig::new()
                ->withDatabaseConnection(
                    $connection->host,
                    $connection->user,
                    $connection->password,
                    $connection->database
                )
        );

        $this->databaseUtility = DatabaseUtility::create($this->objectManager);
    }

    #[Test]
    public function checksTableExistence()
    {
        $this->assertTrue(
            $this->databaseUtility->isTableExists('catalog_product_entity')
        );
        $this->assertFalse(
            $this->databaseUtility->isTableExists('non_existing_table')
        );
    }

    #[Test]
    public function readsAllDataFromTable()
    {
        $this->assertEquals(
            [
                [0, 'admin'],
                [1, 'default'],
            ],
            $this->databaseUtility->tableData('store', ['store_id', 'code'])
        );
    }

    #[Test]
    public function readsFilteredDataFromTable()
    {
        $this->assertEquals(
            [
                ['24-WG080', 'bundle', 11],
            ],
            $this->databaseUtility->tableData(
                'catalog_product_entity',
                ['sku', 'type_id', 'attribute_set_id'],
                ['type_id = ?' => 'bundle']
            )
        );
    }


    #[Test]
    public function insertsDataIntoTable()
    {
        $this->databaseUtility->populateTable(
            'catalog_product_entity',
            ['sku', 'type_id', 'attribute_set_id'],
            [
                ['first_sku', 'simple', 11],
                ['second_sku', 'configurable', 11]
            ],
        );

        $this->assertEquals(
            [
                ['first_sku', 'simple', 11],
                ['second_sku', 'configurable', 11]
            ],
            $this->databaseUtility->tableData(
                'catalog_product_entity',
                [ 'sku', 'type_id', 'attribute_set_id'],
                ['sku IN(?)' => ['first_sku', 'second_sku']]
            )
        );
    }
}
