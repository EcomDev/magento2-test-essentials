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
        string $tablePrefix = ''
    ): self {
        return $this->withSetting('db/table_prefix', $tablePrefix)
            ->withSetting('db/connection/default/host', $host)
            ->withSetting('db/connection/default/username', $username)
            ->withSetting('db/connection/default/password', $password)
            ->withSetting('db/connection/default/dbname', $dbname)
            ->withSetting('db/connection/default/active', 1)
            ->withSetting('db/connection/default/engine', 'innodb')
            ->withSetting('db/connection/default/initStatements', ['SET NAMES utf8'])
            ->withSetting('db/connection/default/driver_options', [
            PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false
        ]);
    }

    public function withSetting(string $path, mixed $value): self
    {
        $data = $this->data->withValue('file', $path, $value);
        return new self($data);
    }
}
