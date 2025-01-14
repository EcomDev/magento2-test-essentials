<?php

namespace EcomDev\Magento2TestEssentials;

use Magento\Framework\App\DeploymentConfig as MagentoDeploymentConfig;
use PDO;

final class DeploymentConfig extends MagentoDeploymentConfig
{
    private function __construct(
        private readonly ArrayConfig $data
    ) {
        $reader = new class ($data) extends MagentoDeploymentConfig\Reader {
            public function __construct(private readonly ArrayConfig $data)
            {
            }

            public function load($fileKey = null)
            {
                return $this->data->getValueByPath('file', '');
            }
        };

        parent::__construct($reader);
    }

    public static function new(): self
    {
        return new self(ArrayConfig::new());
    }

    public function withDatabaseConnection(
        string $host,
        string $username,
        string $password,
        string $dbname,
        string $connectionName = 'default'
    ): self {
        return $this
            ->withSetting($this->databaseSettingPath($connectionName, 'host'), $host)
            ->withSetting($this->databaseSettingPath($connectionName, 'username'), $username)
            ->withSetting($this->databaseSettingPath($connectionName, 'password'), $password)
            ->withSetting($this->databaseSettingPath($connectionName, 'dbname'), $dbname)
            ->withSetting($this->databaseSettingPath($connectionName, 'active'), 1)
            ->withSetting($this->databaseSettingPath($connectionName, 'engine'), 'innodb')
            ->withSetting($this->databaseSettingPath($connectionName, 'initStatements'), 'SET NAMES utf8')
            ->withSetting($this->databaseSettingPath($connectionName, 'driver_options'), [
                PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false
            ]);
    }

    public function withSetting(string $path, mixed $value): self
    {
        $data = $this->data->withValue('file', $path, $value);
        return new self($data);
    }

    public function withTablePrefix(string $prefix): self
    {
        return $this->withSetting('db/table_prefix', $prefix);
    }

    private function databaseSettingPath(string $connectionName, string $path)
    {
        return sprintf('db/connection/%s/%s', $connectionName, $path);
    }
}
