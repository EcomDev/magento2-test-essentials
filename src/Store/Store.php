<?php
/**
 * Copyright © EcomDev B.V. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);


namespace EcomDev\Magento2TestEssentials\Store;

use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Api\Data\StoreExtensionInterface;

class Store implements StoreInterface
{
    /**
     * Store id
     *
     * @var int
     */
    private $id;

    /**
     * Store code
     *
     * @var string
     */
    private $code;

    /**
     * Store website id
     *
     * @var int
     */
    private $websiteId = 0;

    /**
     * Store group id
     *
     * @var int
     */
    private $storeGroupId = 0;

    /**
     * Is store active
     *
     * @var int
     */
    private $isActive = 1;

    /**
     * Name of the store
     *
     * @var string
     */
    private $name = '';

    /**
     * Store extension attributes
     *
     * @var StoreExtensionInterface|null
     */
    private $extensionAttributes;

    /**
     * Creates an object internally
     */
    private function __construct(int $id, string $code)
    {
        $this->id = $id;
        $this->code = $code;
        $this->name = sprintf('Store %s', ucfirst($code));
    }

    /**
     * Creates a new store instance
     */
    public static function new(int $id, string $code): self
    {
        return new self($id, $code);
    }

    /**
     * Creates instance of store with custom website and store group
     */
    public function withWebsite(int $websiteId, int $storeGroup): self
    {
        $store = clone $this;
        $store->websiteId = $websiteId;
        $store->storeGroupId = $storeGroup;
        return $store;
    }

    /**
     * Crates a copy of store with disabled state
     */
    public function withDeactivatedState(): self
    {
        $store = clone $this;
        $store->isActive = 0;
        return $store;
    }


    /**
     * Creates a copy of store with specified name
     */
    public function withName(string $name): self
    {
        $store = clone $this;
        $store->name = $name;
        return $store;
    }

    /* @inheritDoc */
    public function getId()
    {
        return $this->id;
    }

    /* @inheritDoc */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /* @inheritDoc */
    public function getCode()
    {
        return $this->code;
    }

    /* @inheritDoc */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /* @inheritDoc */
    public function getName()
    {
        return $this->name;
    }

    /* @inheritDoc */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /* @inheritDoc */
    public function getWebsiteId()
    {
        return $this->websiteId;
    }

    /* @inheritDoc */
    public function setWebsiteId($websiteId)
    {
        $this->websiteId = $websiteId;
        return $this;
    }

    /* @inheritDoc */
    public function getStoreGroupId()
    {
        return $this->storeGroupId;
    }

    /* @inheritDoc */
    public function setStoreGroupId($storeGroupId)
    {
        $this->storeGroupId = $storeGroupId;
        return $this;
    }

    /* @inheritDoc */
    public function setIsActive($isActive)
    {
        $this->isActive = (int)$isActive;
        return $this;
    }

    /* @inheritDoc */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /* @inheritDoc */
    public function getExtensionAttributes()
    {
        return $this->extensionAttributes;
    }

    /* @inheritDoc */
    public function setExtensionAttributes(StoreExtensionInterface $extensionAttributes)
    {
        $this->extensionAttributes = $extensionAttributes;
        return $this;
    }
}
