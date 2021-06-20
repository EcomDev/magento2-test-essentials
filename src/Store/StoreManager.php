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

    public function setIsSingleStoreModeAllowed($value)
    {
        // TODO: Implement setIsSingleStoreModeAllowed() method.
    }

    public function hasSingleStore()
    {
        // TODO: Implement hasSingleStore() method.
    }

    public function isSingleStoreMode()
    {
        // TODO: Implement isSingleStoreMode() method.
    }

    public function getStore($storeId = null)
    {
        if ($storeId === null) {
            $storeId = $this->currentStore;
        }

        if (isset($this->stores[$storeId])) {
            return $this->stores[$storeId];
        }

        foreach ($this->stores as $store) {
            if ($store->getCode() === $storeId) {
                return $store;
            }
        }
    }

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

    public function getWebsite($websiteId = null)
    {
        if ($websiteId === null) {
            $websiteId = $this->getStore()->getWebsiteId();
        }

        if (isset($this->websites[$websiteId])) {
            return $this->websites[$websiteId];
        }

        foreach ($this->websites as $website) {
            if ($website->getCode() === $websiteId) {
                return $website;
            }
        }

        return $this->websites[$this->defaultWebsite];
    }

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

    public function reinitStores()
    {
        return $this;
    }

    public function getDefaultStoreView()
    {
        // TODO: Implement getDefaultStoreView() method.
    }

    public function getGroup($groupId = null)
    {
        // TODO: Implement getGroup() method.
    }

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

    public function setCurrentStore($store)
    {
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
