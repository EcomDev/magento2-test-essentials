<?php

namespace EcomDev\Magento2TestEssentials;

/**
 * Internal class used as storage for configuration
 *
 * @internal
 */
final class ArrayConfig
{
    private function __construct(private readonly array $data = [])
    {
    }

    public static function new(): self
    {
        return new self();
    }

    public function withValue(string $scope, string $path, mixed $value): self
    {
        $parts = explode('/', $path);
        $data = $this->data;
        $current = &$data[$scope];
        foreach ($parts as $part) {
            $current = &$current[$part];
        }
        $current = $value;
        return new self($data);
    }

    public function getValueByPath(string $scope, string $path): mixed
    {
        if ($path === '') {
            return $this->data[$scope] ?? [];
        }

        $parts = explode('/', $path);
        $current = $this->data[$scope] ?? [];
        foreach ($parts as $part) {
            if (!isset($current[$part])) {
                return null;
            }
            $current = $current[$part];
        }
        return $current;
    }
}
