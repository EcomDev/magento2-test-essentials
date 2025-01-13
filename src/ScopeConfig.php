<?php

namespace EcomDev\Magento2TestEssentials;

use EcomDev\Magento2TestEssentials\Store\StoreManager;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Fake implementation of scoped config
 *
 * Allows testing functionalities
 * that rely on custom configuration value to change its behaviour
 */
final class ScopeConfig implements ScopeConfigInterface
{
    private function __construct(
        private readonly StoreManager $storeManager,
        private readonly ArrayConfig $data,
    ) {
    }

    public static function new(): self
    {
        return new self(StoreManager::new(), ArrayConfig::new());
    }

    /**
     * Configures custom storage manager for testing
     */
    public function withStoreManager(StoreManager $storeManager): self
    {
        return new self($storeManager, $this->data);
    }

    /**
     * Adds configuration value in the specific store
     */
    public function withStoreValue(string $code, string $path, mixed $value): self
    {
        return new self(
            $this->storeManager,
            $this->data->withValue(
                sprintf('%s/%s', ScopeInterface::SCOPE_STORES, $code),
                $path,
                $value
            )
        );
    }

    /**
     * Adds configuration value in the specific website
     */
    public function withWebsiteValue(string $code, string $path, mixed $value): self
    {
        return new self(
            $this->storeManager,
            $this->data->withValue(
                sprintf('%s/%s', ScopeInterface::SCOPE_WEBSITES, $code),
                $path,
                $value
            )
        );
    }

    /**
     * Adds configuration value for default scope
     */
    public function withDefaultValue(string $path, mixed $value): self
    {
        return new self(
            $this->storeManager,
            $this->data->withValue(
                ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
                $path,
                $value
            )
        );
    }

    /**
     * Returns merged configuration value for requested scope and path
     */
    public function getValue($path, $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $scopeCode = null): mixed
    {
        $scope = match ($scopeType) {
            ScopeInterface::SCOPE_WEBSITE, ScopeInterface::SCOPE_WEBSITES => sprintf(
                '%s/%s',
                ScopeInterface::SCOPE_WEBSITES, $scopeCode
            ),
            ScopeInterface::SCOPE_STORE, ScopeInterface::SCOPE_STORES  => sprintf(
                '%s/%s',
                ScopeInterface::SCOPE_STORES, $scopeCode
            ),
            default => ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
        };

        $fallbackValue = match ($scopeType) {
            ScopeInterface::SCOPE_WEBSITES, ScopeInterface::SCOPE_WEBSITE => $this->getValue($path, ScopeConfigInterface::SCOPE_TYPE_DEFAULT),
            ScopeInterface::SCOPE_STORES, ScopeInterface::SCOPE_STORE => $this->getValue(
                $path,
                ScopeInterface::SCOPE_WEBSITE,
                $this->storeManager->getWebsite(
                    $this->storeManager->getStore($scopeCode)->getWebsiteId()
                )->getCode()
            ),
            default => null,
        };

        $value = $this->data->getValueByPath($scope, $path);

        if (is_array($value) && is_array($fallbackValue)) {
            return array_replace_recursive($fallbackValue, $value);
        }

        return $value ?? $fallbackValue;
    }

    /**
     * Checks if truthy value is set in the configuration
     */
    public function isSetFlag($path, $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $scopeCode = null): bool
    {
        return !!$this->getValue($path, $scopeType, $scopeCode);
    }
}
