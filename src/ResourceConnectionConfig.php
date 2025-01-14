<?php

namespace EcomDev\Magento2TestEssentials;

use Magento\Framework\App\ResourceConnection\ConfigInterface;

final class ResourceConnectionConfig implements ConfigInterface
{
    public function __construct(private readonly DeploymentConfig $deploymentConfig)
    {
    }

    public function getConnectionName($resourceName)
    {
        if ($this->deploymentConfig->get('db/connection/' . $resourceName)) {
            return $resourceName;
        }

        return 'default';
    }
}