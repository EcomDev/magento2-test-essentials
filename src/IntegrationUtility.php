<?php

namespace EcomDev\Magento2TestEssentials;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\App\ResourceConnection\ConfigInterface;
use Magento\Framework\App\ResourceConnection\ConnectionAdapterInterface;
use Magento\Framework\DB\Adapter\Pdo\MysqlFactory;
use Magento\Framework\DB\Adapter\Pdo\Mysql as PdoMysql;
use Magento\Framework\DB\Adapter\SqlVersionProvider;
use Magento\Framework\DB\Logger\Quiet;
use Magento\Framework\DB\LoggerInterface;
use Magento\Framework\DB\Select\ColumnsRenderer;
use Magento\Framework\DB\Select\DistinctRenderer;
use Magento\Framework\DB\Select\ForUpdateRenderer;
use Magento\Framework\DB\Select\FromRenderer;
use Magento\Framework\DB\Select\GroupRenderer;
use Magento\Framework\DB\Select\HavingRenderer;
use Magento\Framework\DB\Select\LimitRenderer;
use Magento\Framework\DB\Select\OrderRenderer;
use Magento\Framework\DB\Select\SelectRenderer;
use Magento\Framework\DB\Select\UnionRenderer;
use Magento\Framework\DB\Select\WhereRenderer;
use Magento\Framework\DB\SelectFactory;
use Magento\Framework\Model\ResourceModel\Type\Db\ConnectionFactory;
use Magento\Framework\Model\ResourceModel\Type\Db\ConnectionFactoryInterface;
use Magento\Framework\Model\ResourceModel\Type\Db\Pdo\Mysql;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Setup\Declaration\Schema\Dto\Factories\Table;
use Magento\Framework\Setup\Declaration\Schema\Sharding;
use Psr\Log\LoggerInterface as PsrLoggerInterface;
use Psr\Log\NullLogger;

final class IntegrationUtility
{
    public static function withSetupDatabaseObjects(
        ObjectManager $objectManager,
        DeploymentConfig $deploymentConfig
    ): ObjectManager {

        return $objectManager
            ->withObject(\Magento\Framework\App\DeploymentConfig::class, $deploymentConfig)
            ->withObject(DeploymentConfig::class, $deploymentConfig)
            ->withDefaultArguments(Sharding::class, ['resources' => ['default']])
            ->withAutowire(Sharding::class)
            ->withAutowire(ConnectionFactory::class)
            ->withAutowire(SqlVersionProvider::class)
            ->withAutowire(Table::class)
            ->withAutowire(PdoMysql::class)
            ->withAutoResolvedArgument(PdoMysql::class, 'dtoFactoriesTable', Table::class)
            ->withAutoResolvedArgument(PdoMysql::class, 'serializer', SerializerInterface::class)
            ->withAutoResolvedArgument(PdoMysql::class, 'logger', LoggerInterface::class)
            ->withFactory(
                ConnectionFactoryInterface::class,
                static fn ($objectManager, $arguments) => $objectManager
                    ->create(ConnectionFactory::class, $arguments)
            )
            ->withObject(LoggerInterface::class, new Quiet())
            ->withObject(PsrLoggerInterface::class, new NullLogger())
            ->withObject(SerializerInterface::class, new Json())
            ->withFactory(SelectRenderer::class, static fn ($objectManager) =>
                // @phpstan-ignore argument.type
                new SelectRenderer([
                    'distinct' => [
                        'renderer' => $objectManager->create(DistinctRenderer::class),
                        'sort' => 100,
                        'part' => 'distinct',
                    ],
                    'columns' => [
                        'renderer' => $objectManager->create(ColumnsRenderer::class),
                        'sort' => 200,
                        'part' => 'columns',
                    ],
                    'union' => [
                        'renderer' => $objectManager->create(UnionRenderer::class),
                        'sort' => 300,
                        'part' => 'union',
                    ],
                    'from' => [
                        'renderer' => $objectManager->create(FromRenderer::class),
                        'sort' => 400,
                        'part' => 'from',
                    ],
                    'where' => [
                        'renderer' => $objectManager->create(WhereRenderer::class),
                        'sort' => 500,
                        'part' => 'where',
                    ],
                    'group' => [
                        'renderer' => $objectManager->create(GroupRenderer::class),
                        'sort' => 600,
                        'part' => 'group',
                    ],
                    'having' => [
                        'renderer' => $objectManager->create(HavingRenderer::class),
                        'sort' => 700,
                        'part' => 'having',
                    ],
                    'order' => [
                        'renderer' => $objectManager->create(OrderRenderer::class),
                        'sort' => 800,
                        'part' => 'order',
                    ],
                    'limit' => [
                        'renderer' => $objectManager->create(LimitRenderer::class),
                        'sort' => 900,
                        'part' => 'limitcount',
                    ],
                    'for_update' => [
                        'renderer' => $objectManager->create(ForUpdateRenderer::class),
                        'sort' => 1000,
                        'part' => 'forupdate'
                    ]
                ]))
            ->withAutowire(SelectFactory::class)
            ->withAutowire(MysqlFactory::class)
            ->withFactory(
                ConnectionAdapterInterface::class,
                static fn ($objectManager, $arguments) => $objectManager->create(Mysql::class, $arguments)
            )
            ->withAutoResolvedArgument(Mysql::class, 'mysqlFactory', MysqlFactory::class)
            ->withFactory(
                ConfigInterface::class,
                static fn (ObjectManager $objectManager) => $objectManager
                    ->create(ResourceConnectionConfig::class)
            )
            ->withAutowire(ResourceConnection::class);
    }

    public static function setupDatabaseObjects(DeploymentConfig $deploymentConfig): ObjectManager
    {
        return self::withSetupDatabaseObjects(ObjectManager::new(), $deploymentConfig);
    }

    /**
     * Configure sharding for database connections
     *
     * @param string[] $resources
     */
    public static function configureSharding(ObjectManager $objectManager, array $resources = []): ObjectManager
    {
        return $objectManager->withDefaultArguments(Sharding::class, [
            'resources' => ['default', ...$resources]
        ]);
    }
}
