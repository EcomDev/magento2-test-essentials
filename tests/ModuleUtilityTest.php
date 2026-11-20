<?php

declare(strict_types=1);

namespace EcomDev\Magento2TestEssentials;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ModuleUtilityTest extends TestCase
{
    #[Test]
    public function findsFilePathInCatalogModule()
    {
        $this->assertEquals(
            realpath('vendor/magento/module-catalog/etc/db_schema.xml'),
            ModuleUtility::etcFile('Magento_Catalog', 'db_schema.xml')
        );
    }

    #[Test]
    public function errorsWhenFileDoesNotExist()
    {
        $this->expectException(InvalidArgumentException::class);

        ModuleUtility::etcFile('Magento_Catalog', 'some-unknown.xml');
    }
}
