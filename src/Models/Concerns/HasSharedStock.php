<?php

namespace StephaneBour\LunarSharedStock\Models\Concerns;

use StephaneBour\LunarSharedStock\Managers\SharedStockManager;

class HasSharedStock
{
    /**
     * Les méthodes à mixin dans le modèle Product.
     */
    public function usesSharedStock()
    {
        return function (): bool {
            return \boolval($this->getAttribute('shared_stock')) === true ||
                   \in_array($this->productType?->handle ?? '', config('lunar-shared-stock.shared_stock_product_types', []));
        };
    }

    public function getTotalStockAttribute()
    {
        return function (): int {
            return $this->variants->min('stock');
        };
    }

    public function getAvailableStockAttribute()
    {
        return function (): int {
            $totalStock = $this->variants->min('stock');
            $totalBackorder = config('lunar-shared-stock.shared_backorder', true)
                ? $this->variants->min('backorder')
                : 0;

            return $totalStock + $totalBackorder;
        };
    }

    public function decrementSharedStock()
    {
        return function (int $quantity): bool {
            if (! $this->usesSharedStock()) {
                return false;
            }

            return app(SharedStockManager::class)->decrementStock($this, $quantity);
        };
    }

    public function isGloballyAvailable()
    {
        return function (int $quantity = 1): bool {
            if (! $this->usesSharedStock()) {
                return $this->variants->where('stock', '>=', $quantity)->exists() ||
                       $this->variants->where('backorder', '>=', $quantity)->exists();
            }

            return $this->available_stock >= $quantity;
        };
    }

    public function getStockDistribution()
    {
        return function (): array {
            if (! $this->usesSharedStock()) {
                return [];
            }

            return $this->variants->map(function ($variant) {
                return [
                    'variant_id' => $variant->id,
                    'sku' => $variant->sku,
                    'stock' => $variant->stock,
                    'backorder' => $variant->backorder,
                    'option_values' => $variant->values->pluck('name')->join(', '),
                ];
            })->toArray();
        };
    }

    public function getSharedStockStatus()
    {
        return function (): array {
            return [
                'uses_shared_stock' => $this->usesSharedStock(),
                'total_stock' => $this->total_stock,
                'available_stock' => $this->available_stock,
                'variant_count' => $this->variants->count(),
                'auto_sync' => config('lunar-shared-stock.auto_sync'),
            ];
        };
    }
}
