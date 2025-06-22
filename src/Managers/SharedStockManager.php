<?php

namespace StephaneBour\LunarSharedStock\Managers;

use Lunar\Models\Product;

class SharedStockManager
{
    /**
     * Synchroniser le stock de toutes les variantes d'un produit.
     */
    public function syncStock(Product $product, int $totalStock): void
    {
        if (! $product->usesSharedStock()) {
            return;
        }

        // Mettre à jour toutes les variantes avec le même stock
        $product->variants()->update(['stock' => $totalStock]);
    }

    /**
     * Décrémenter le stock intelligemment.
     */
    public function decrementStock(Product $product, int $quantity): bool
    {
        $variants = $product->variants->sortByDesc('stock');
        $remaining = $quantity;

        foreach ($variants as $variant) {
            if ($remaining <= 0) {
                break;
            }

            $availableStock = $variant->stock;
            $availableBackorder = config('lunar-shared-stock.shared_backorder', true)
                ? $variant->backorder
                : 0;

            $totalAvailable = $availableStock + $availableBackorder;
            $toDecrease = \min($totalAvailable, $remaining);

            if ($toDecrease <= 0) {
                continue;
            }

            // Décrémenter d'abord le stock, puis les backorders
            if ($availableStock >= $toDecrease) {
                $variant->decrement('stock', $toDecrease);
            } else {
                $stockDecrease = $availableStock;
                $backorderDecrease = $toDecrease - $stockDecrease;

                if ($stockDecrease > 0) {
                    $variant->decrement('stock', $stockDecrease);
                }

                if ($backorderDecrease > 0 && config('lunar-shared-stock.shared_backorder', true)) {
                    $variant->decrement('backorder', $backorderDecrease);
                }
            }

            $remaining -= $toDecrease;
        }

        return $remaining === 0;
    }
}
