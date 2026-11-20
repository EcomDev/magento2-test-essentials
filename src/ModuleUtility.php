<?php

declare(strict_types=1);

namespace EcomDev\Magento2TestEssentials;

use InvalidArgumentException;
use Magento\Framework\Component\ComponentRegistrar;

final class ModuleUtility
{
    public static function etcFile(string $moduleName, string $file): string
    {
        $registrar = new ComponentRegistrar();

        $modulePath = $registrar->getPath(
            ComponentRegistrar::MODULE,
            $moduleName
        );

        $filePath = realpath(sprintf('%s/etc/%s', $modulePath, $file));
        if (!$filePath) {
            throw new InvalidArgumentException(sprintf('File %s does not exist in module %s', $file, $moduleName));
        }

        return $filePath;
    }
}
