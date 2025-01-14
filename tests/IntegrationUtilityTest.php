<?php

namespace EcomDev\Magento2TestEssentials;

use EcomDev\TestContainers\MagentoData\DbConnectionSettings;
use EcomDev\TestContainers\MagentoData\DbContainerBuilder;
use Magento\Framework\App\ResourceConnection;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class IntegrationUtilityTest extends TestCase
{
    #[Test]
    public function createsDefaultConnectionConfiguredResourceModel()
    {
        $connection = $this->connectionSettings();
        $objectManager = IntegrationUtility::setupDatabaseObjects(
            DeploymentConfig::new()
                ->withDatabaseConnection(
                    $connection->host,
                    $connection->user,
                    $connection->password,
                    $connection->database
                )
        );

        /**
 * @var ResourceConnection $resourceConnection
*/
        $resourceConnection = $objectManager->get(ResourceConnection::class);
        $connection = $resourceConnection->getConnection();
        $select = $connection->select()
            ->from($resourceConnection->getTableName('catalog_product_entity'), ['type_id', 'count(entity_id)'])
            ->group('type_id');

        $this->assertEquals(
            [
                'simple' => 1891,
                'grouped' => 1,
                'configurable' => 147,
                'bundle' => 1
            ],
            $connection->fetchPairs($select)
        );
    }

    #[Test]
    public function createsResourceConnectionsWithCustomNames()
    {
        $connection = $this->connectionSettings();

        $objectManager = IntegrationUtility::setupDatabaseObjects(
            DeploymentConfig::new()
                ->withDatabaseConnection(
                    $connection->host,
                    $connection->user,
                    $connection->password,
                    $connection->database
                )
                ->withDatabaseConnection(
                    $connection->host,
                    $connection->user,
                    $connection->password,
                    $connection->database,
                    'indexer'
                )
        );

        $resourceConnection = $objectManager->get(ResourceConnection::class);

        $defaultConnection = $resourceConnection->getConnection();
        $indexerConnection = $resourceConnection->getConnection('indexer');
        $anotherConnection = $resourceConnection->getConnection('another');

        $this->assertSame($defaultConnection, $anotherConnection);
        $this->assertNotSame($defaultConnection, $indexerConnection);
    }

    private function connectionSettings(): DbConnectionSettings
    {
        $container = DbContainerBuilder::mysql()
            ->withSampleData()
            ->shared('utility');

        return $container->getConnectionSettings();
    }
}
