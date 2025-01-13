<?php

/**
 * Copyright © EcomDev B.V. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace EcomDev\Magento2TestEssentials\Store;

use Magento\Store\Api\Data\GroupInterface;
use Magento\Store\Api\Data\GroupExtensionInterface;

/**
 * Implementation that imitates real store group model behaviour
 */
final class StoreGroup implements GroupInterface
{
    private string $name;
    private int $websiteId = 0;
    private int $rootCategoryId = 2;
    private int $defaultStoreId = 0;
    private ?GroupExtensionInterface $extensionAttributes = null;

    private function __construct(private int $id, private string $code)
    {
        $this->name = sprintf('Store Group %s', ucfirst($code));
    }

    /**
     * Creates a new instance of store group with provided arguments
     */
    public static function new(int $id, string $code): self
    {
        return new self($id, $code);
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

    public function getWebsiteId(): int
    {
        return $this->websiteId;
    }

    public function setWebsiteId($websiteId): self
    {
        $this->websiteId = $websiteId;
        return $this;
    }

    public function getRootCategoryId(): int
    {
        return $this->rootCategoryId;
    }

    public function setRootCategoryId($rootCategoryId): self
    {
        $this->rootCategoryId = $rootCategoryId;
        return $this;
    }

    public function getDefaultStoreId()
    {
        return $this->defaultStoreId;
    }

    public function setDefaultStoreId($defaultStoreId)
    {
        $this->defaultStoreId = $defaultStoreId;
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

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode($code): self
    {
        $this->code = $code;
        return $this;
    }

    public function getExtensionAttributes(): ?GroupExtensionInterface
    {
        return $this->extensionAttributes;
    }

    public function setExtensionAttributes(GroupExtensionInterface $extensionAttributes): self
    {
        $this->extensionAttributes = $extensionAttributes;
        return $this;
    }

    /**
     * Creates a copy of store group with changed name
     */
    public function withName(string $name): self
    {
        $storeGroup = clone $this;
        $storeGroup->name = $name;
        return $storeGroup;
    }

    /**
     * Creates a copy of store group with changed website id
     */
    public function withWebsite(int $websiteId): self
    {
        $storeGroup = clone $this;
        $storeGroup->websiteId = $websiteId;
        return $storeGroup;
    }

    /**
     * Create a copy of store group with changed root category id
     */
    public function withRootCategory(int $categoryId): self
    {
        $storeGroup = clone $this;
        $storeGroup->rootCategoryId = $categoryId;
        return $storeGroup;
    }

    /**
     * Creates a copy of store group with modified default store
     */
    public function withDefaultStore(int $storeId): self
    {
        $storeGroup = clone $this;
        $storeGroup->defaultStoreId = $storeId;
        return $storeGroup;
    }
}
