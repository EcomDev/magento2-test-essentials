<?php
/**
 * Copyright © EcomDev B.V. All rights reserved.
 * See LICENSE for license details.
 */
declare(strict_types=1);

namespace EcomDev\Magento2TestEssentials\Store;

use Magento\Store\Api\Data\WebsiteExtensionInterface;
use PHPUnit\Framework\TestCase;

class WebsiteTest extends TestCase
{
    /** @test */
    public function usesIdFromProvidedArguments()
    {
        $this->assertEquals(
            1,
            Website::new(1, 'one')
                ->getId()
        );
    }

    /** @test */
    public function usesCodeFromProvidedArguments()
    {
        $this->assertEquals(
            'two',
            Website::new(2, 'two')
                ->getCode()
        );
    }

    /** @test */
    public function defaultsToNameGeneratedFromCode()
    {
        $this->assertEquals(
            'Website Three',
            Website::new(3, 'three')
                ->getName()
        );
    }

    /** @test */
    public function allowsToOverrideName()
    {
        $this->assertEquals(
            'Custom Website Name',
            Website::new(2, 'two')
                ->withName('Custom Website Name')
                ->getName()
        );
    }

    /** @test */
    public function defaultsGroupIdToZero()
    {
        $this->assertEquals(
            0,
            Website::new(1, 'one')
                ->getDefaultGroupId()
        );
    }

    /** @test */
    public function allowsToOverrideDefaultGroup()
    {
        $this->assertEquals(
            2,
            Website::new(1, 'one')
                ->withDefaultGroup(2)
                ->getDefaultGroupId()
        );
    }

    /** @test */
    public function mutatesCurrentWebsite()
    {
        $website = Website::new(1, "one");

        $website->setId(4)
            ->setCode("four")
            ->setName("Modified Name")
            ->setDefaultGroupId(3);

        $this->assertEquals(
            Website::new(4, "four")
                ->withName("Modified Name")
                ->withDefaultGroup(3),
            $website
        );
    }

    /** @test */
    public function defaultsExtensionAttributesToNull()
    {
        $this->assertSame(
            null,
            Website::new(1, 'one')
                ->getExtensionAttributes()
        );
    }

    /** @test */
    public function allowsSettingExtensionAttributesToCustomValue()
    {
        $value = new class implements WebsiteExtensionInterface {

        };

        $this->assertSame(
            $value,
            Website::new(2, 'two')
                ->setExtensionAttributes($value)
                ->getExtensionAttributes()
        );
    }
}
