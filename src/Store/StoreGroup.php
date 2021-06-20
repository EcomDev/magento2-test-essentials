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
 * Implementation of store group that imitates real store group behaviour
 */
class StoreGroup implements GroupInterface
{
    private $id;
    private $code;
    private $name;
    private $websiteId = 0;
    private $rootCategoryId = 2;
    private $defaultStoreId = 0;
    private $extensionAttributes;

    private function __construct(int $id, string $code)
    {
        $this->id = $id;
        $this->code = $code;
        $this->name = sprintf('Store Group %s', ucfirst($code));
    }

    /**
     * Creates a new instance of store group with provided arguments
     */
    public static function new(int $id, string $code): self
    {
        return new self($id, $code);
    }

    /* @inerhitDoc */
    public function getId()
    {
        return $this->id;
    }

    /* @inerhitDoc */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /* @inerhitDoc */
    public function getWebsiteId()
    {
        return $this->websiteId;
    }

    /* @inerhitDoc */
    public function setWebsiteId($websiteId)
    {
        $this->websiteId = $websiteId;
        return $this;
    }

    /* @inerhitDoc */
    public function getRootCategoryId()
    {
        return $this->rootCategoryId;
    }

    /* @inerhitDoc */
    public function setRootCategoryId($rootCategoryId)
    {
        $this->rootCategoryId = $rootCategoryId;
        return $this;
    }

    /* @inerhitDoc */
    public function getDefaultStoreId()
    {
        return $this->defaultStoreId;
    }

    /* @inerhitDoc */
    public function setDefaultStoreId($defaultStoreId)
    {
        $this->defaultStoreId = $defaultStoreId;
        return $this;
    }

    /* @inerhitDoc */
    public function getName()
    {
        return $this->name;
    }

    /* @inerhitDoc */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /* @inerhitDoc */
    public function getCode()
    {
        return $this->code;
    }

    /* @inerhitDoc */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /* @inerhitDoc */
    public function getExtensionAttributes()
    {
        return $this->extensionAttributes;
    }

    /* @inerhitDoc */
    public function setExtensionAttributes(GroupExtensionInterface $extensionAttributes)
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
