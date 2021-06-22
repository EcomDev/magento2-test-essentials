<?php


namespace EcomDev\Magento2TestEssentials\Store;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;

class StoreManager implements StoreManagerInterface
{
    /**
     * Collection of websites by id
     *
     * @var Website[]
     */
    private $websites = [];

    /**
     * Id of default website
     *
     * @var int
     */
    private $defaultWebsite = 0;

    /**
     * List of stores by id
     *
     * @var Store[]
     */
    private $stores = [];

    /**
     * List of store groups by id
     *
     * @var StoreGroup[]
     */
    private $groups = [];

    /**
     * Current store
     *
     * @var int
     */
    private $currentStore = 0;

    /**
     * Flag for checking if single store is enabled
     *
     * @var bool
     */
    private $isAllowedSingleStoreMode = true;

    /**
     * Crates a new instance of store manager
     *
     * @return self
     */
    public static function new(): self
    {
        return (new self())
            ->withWebsite(Website::new(0, 'base'))
            ->withStore(Store::new(0, 'admin'))
            ->withGroup(StoreGroup::new(0, 'admin'));
    }

    /* @inerhitDoc */
    public function setIsSingleStoreModeAllowed($value)
    {
        $this->isAllowedSingleStoreMode = $value;
    }

    /**
     * Checks if single store mode is applicable
     *
     * @return bool
     */
    public function hasSingleStore()
    {
        return count($this->stores) < 3;
    }

    /**
     * Checks if only single store is available and single store mode is enabled
     *
     * @return bool
     */
    public function isSingleStoreMode()
    {
        return $this->isAllowedSingleStoreMode && $this->hasSingleStore();
    }

    public function getStore($storeId = null)
    {
        if ($storeId === null && $this->isSingleStoreMode()) {
            end($this->stores);
            $storeId = key($this->stores);
            reset($this->stores);
        }

        if ($storeId === null) {
            $storeId = $this->currentStore;
        }

        if ($storeId === true) {
            return $this->getDefaultStoreView();
        }

        if (isset($this->stores[$storeId])) {
            return $this->stores[$storeId];
        }

        foreach ($this->stores as $store) {
            if ($store->getCode() === $storeId) {
                return $store;
            }
        }

        throw new NoSuchEntityException(
            __("The store that was requested wasn't found. Verify the store and try again.")
        );
    }

    /* @inerhitDoc */
    public function getStores($withDefault = false, $codeKey = false)
    {
        $result = [];
        foreach ($this->stores as $store) {
            if ($store->getId() === 0 && !$withDefault) {
                continue;
            }

            $key = $codeKey ? $store->getCode() : $store->getId();

            $result[$key] = $store;
        }

        return $result;
    }

    /* @inerhitDoc */
    public function getWebsite($websiteId = null)
    {
        if ($websiteId === null) {
            $websiteId = $this->getStore()->getWebsiteId();
        }

        if ($websiteId === true) {
            $websiteId = $this->defaultWebsite;
        }

        if (isset($this->websites[$websiteId])) {
            return $this->websites[$websiteId];
        }

        foreach ($this->websites as $website) {
            if ($website->getCode() === $websiteId) {
                return $website;
            }
        }

        throw new NoSuchEntityException(
            __("The website that was requested wasn't found. Verify the website and try again.")
        );
    }

    /* @inerhitDoc */
    public function getWebsites($withDefault = false, $codeKey = false)
    {
        $result = [];
        foreach ($this->websites as $website) {
            if ($website->getId() === 0 && !$withDefault) {
                continue;
            }

            $key = $codeKey ? $website->getCode() : $website->getId();
            $result[$key] = $website;
        }
        return $result;
    }

    /* @inerhitDoc */
    public function reinitStores()
    {
        $this->currentStore = 0;
    }

    /* @inerhitDoc */
    public function getDefaultStoreView()
    {
        $website = $this->getWebsite($this->defaultWebsite);
        $group = $this->getGroup($website->getDefaultGroupId());
        return $this->getStore($group->getDefaultStoreId());
    }

    /* @inerhitDoc */
    public function getGroup($groupId = null)
    {
        if ($groupId === null) {
            $groupId = $this->getStore()->getStoreGroupId();
        }

        if ($groupId === true) {
            $groupId = $this->getWebsite(true)->getDefaultGroupId();
        }

        if (!isset($this->groups[$groupId])) {
            throw new NoSuchEntityException(
                __("The store group that was requested wasn't found. Verify the store group and try again.")
            );
        }

        return $this->groups[$groupId];
    }

    /* @inerhitDoc */
    public function getGroups($withDefault = false)
    {
        $result = [];
        foreach ($this->groups as $group) {
            if ($group->getId() === 0 && !$withDefault) {
                continue;
            }

            $result[$group->getId()] = $group;
        }

        return $result;
    }

    /* @inheritDoc */
    public function setCurrentStore($store)
    {
        if ($store !== null) {
            $store = $this->getStore($store);
            $this->currentStore = $store->getId();
            return $this;
        }

        $this->currentStore = $store;
        return $this;
    }

    /**
     * Copies store manager with added store
     */
    public function withStore(Store $store): self
    {
        $storeManager = clone $this;
        $storeManager->stores[$store->getId()] = $store;
        return $storeManager;
    }

    /**
     * Copies store manager with added store group
     */
    public function withGroup(StoreGroup $group): self
    {
        $storeManager = clone $this;
        $storeManager->groups[$group->getId()] = $group;
        return $storeManager;
    }

    /**
     * Copies store manager with added website
     */
    public function withWebsite(Website $website): self
    {
        $storeManager = clone $this;
        $storeManager->websites[$website->getId()] = $website;
        return $storeManager;
    }

    /**
     * Copies store manager with modified default website id
     */
    public function withDefaultWebsite(int $websiteId): self
    {
        $storeManager = clone $this;
        $storeManager->defaultWebsite = $websiteId;
        return $storeManager;
    }
}
