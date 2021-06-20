<?php
/**
 * Copyright © EcomDev B.V. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace EcomDev\Magento2TestEssentials\Store;

use Magento\Store\Api\Data\WebsiteInterface;
use Magento\Store\Api\Data\WebsiteExtensionInterface;

class Website implements WebsiteInterface
{
    private $id;
    private $code;
    private $name;
    private $defaultGroupId = 0;
    private $extensionAttributes;

    private function __construct(int $id, string $code)
    {
        $this->id = $id;
        $this->code = $code;
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

    /* @inerhitDoc  */
    public function getId()
    {
        return $this->id;
    }

    /* @inerhitDoc  */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /* @inerhitDoc  */
    public function getCode()
    {
        return $this->code;
    }

    /* @inerhitDoc  */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /* @inerhitDoc  */
    public function getName()
    {
        return $this->name;
    }

    /* @inerhitDoc  */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /* @inerhitDoc  */
    public function getDefaultGroupId()
    {
        return $this->defaultGroupId;
    }

    /* @inerhitDoc  */
    public function setDefaultGroupId($defaultGroupId)
    {
        $this->defaultGroupId = $defaultGroupId;
        return $this;
    }

    /* @inerhitDoc  */
    public function getExtensionAttributes()
    {
        return $this->extensionAttributes;
    }

    /* @inerhitDoc  */
    public function setExtensionAttributes(WebsiteExtensionInterface $extensionAttributes)
    {
        $this->extensionAttributes = $extensionAttributes;
        return $this;
    }
}
