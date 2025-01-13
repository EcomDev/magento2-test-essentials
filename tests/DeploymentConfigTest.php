<?php

namespace EcomDev\Magento2TestEssentials;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class DeploymentConfigTest extends TestCase
{
    #[Test]
    public function availableWhenInstallDateSet()
    {
        $this->assertTrue(
            DeploymentConfig::new()
                ->withSetting('install/date', (new \DateTime())->format('Y-m-d H:i:s'))
                ->isAvailable()
        );
    }

    #[Test]
    public function dbIsNotAvailableWhenNoDbConnection()
    {
        $this->assertFalse(
            DeploymentConfig::new()->isDbAvailable()
        );
    }

    #[Test]
    public function reportsDbAvailableWhenSettingsProvided()
    {
        $this->assertTrue(
            DeploymentConfig::new()
                ->withDatabaseConnection('db', 'magento', 'magento', 'magento')
                ->isDbAvailable()
        );
    }

    #[Test]
    public function generatesReadyToUseDbConnectionSettings()
    {
        $this->assertEquals(
            [
                'table_prefix' => 'magento_',
                'connection' => [
                    'default' => [
                        'host' => 'db',
                        'username' => 'magento',
                        'password' => 'magento_pwd',
                        'dbname' => 'magento2',
                        'engine' => 'innodb',
                        'initStatements' => [
                            'SET NAMES utf8',
                        ],
                        'active' => 1,
                        'driver_options' => [
                            \PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false
                        ],
                    ]
                ],
            ],
            DeploymentConfig::new()
                ->withDatabaseConnection('db', 'magento', 'magento_pwd', 'magento2', 'magento_')
                ->getConfigData('db')
        );
    }
}
