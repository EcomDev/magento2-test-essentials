<?php

/**
 * Copyright © EcomDev B.V. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace EcomDev\Magento2TestEssentials\Store;

use Magento\Store\Api\Data\WebsiteInterface;
use Magento\Store\Api\Data\WebsiteExtensionInterface;

/**
 * Implementation that imitates real store model behaviour
 */
final class Website implements WebsiteInterface
{
    private string $name;
    private int $defaultGroupId = 0;
    private ?WebsiteExtensionInterface $extensionAttributes = null;

    private function __construct(private int $id, private string $code)
    {
        $this->name = sprintf('Website %s', ucfirst($code));
    }

    /**
     * Creates new instance of website
     */
    public static function new(int $id, string $code): self
    {
        return new self($id, $code);
    }

    /**
     * Copies current website and modifies name in the copy
     */
    public function withName(string $name): self
    {
        $website = clone $this;
        $website->name = $name;
        return $website;
    }

    /**
     * Copies current website and assigns new default group into copy
     */
    public function withDefaultGroup(int $groupId): self
    {
        $website = clone $this;
        $website->defaultGroupId = $groupId;
        return $website;
    }


    public function getId(): int
    {
        return $this->id;
    }

    public function setId($id): self
    {
        $this->id = $id;
        return $this;
    }
    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode($code): self
    {
        $this->code = $code;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName($name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getDefaultGroupId(): int
    {
        return $this->defaultGroupId;
    }
    public function setDefaultGroupId($defaultGroupId): self
    {
        $this->defaultGroupId = $defaultGroupId;
        return $this;
    }

    public function getExtensionAttributes(): ?WebsiteExtensionInterface
    {
        return $this->extensionAttributes;
    }

    public function setExtensionAttributes(WebsiteExtensionInterface $extensionAttributes): self
    {
        $this->extensionAttributes = $extensionAttributes;
        return $this;
    }
}
