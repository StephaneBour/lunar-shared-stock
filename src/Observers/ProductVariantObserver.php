<?php

namespace StephaneBour\LunarSharedStock\Observers;

use Lunar\Models\ProductVariant;

class ProductVariantObserver
{
    /**
     * Gérer l'événement "updated" du modèle ProductVariant.
     */
    public function updated(ProductVariant $variant): void
    {
        if (! config('lunar-shared-stock.auto_sync', true)) {
            return;
        }

        // Charger la relation product si elle n'est pas déjà chargée
        $product = $variant->product()->first();

        if (! $product) {
            return;
        }

        // Vérifier si le produit utilise le stock partagé
        if (! $this->productUsesSharedStock($product)) {
            return;
        }

        // Vérifier si le stock a changé
        if ($variant->wasChanged('stock')) {
            $this->handleStockChange($variant);
        }
    }

    /**
     * Gérer l'événement "creating" du modèle ProductVariant.
     */
    public function creating(ProductVariant $variant): void
    {
        // Charger la relation product si elle n'est pas déjà chargée
        $product = $variant->product()->first();

        if (! $product) {
            return;
        }

        // Vérifier si le produit utilise le stock partagé
        if (! $this->productUsesSharedStock($product)) {
            return;
        }

        // On prend le stock maximal des variantes existantes
        $maxStock = $product->variants()->max('stock') ?? 0;
        $variant->stock = $maxStock;
    }

    /**
     * Vérifier si un produit utilise le stock partagé.
     *
     * @param mixed $product
     */
    protected function productUsesSharedStock($product): bool
    {
        // Utiliser la méthode du mixin si elle existe, sinon utiliser la logique de base
        if (\method_exists($product, 'usesSharedStock')) {
            return $product->usesSharedStock();
        }

        // Logique de fallback
        return \boolval($product->getAttribute('shared_stock')) === true ||
               \in_array($product->productType?->handle ?? '', config('lunar-shared-stock.shared_stock_product_types', []));
    }

    /**
     * Gérer les changements de stock.
     */
    protected function handleStockChange(ProductVariant $variant): void
    {
        $originalStock = $variant->getOriginal('stock');
        $newStock = $variant->stock;
        $stockDifference = $newStock - $originalStock;

        if ($stockDifference < 0) {
            $this->handleStockDecrease($variant, \abs($stockDifference));
        } else {
            $this->handleStockIncrease($variant, $newStock);
        }
    }

    /**
     * Gérer la diminution de stock.
     */
    protected function handleStockDecrease(ProductVariant $originalVariant, int $decreaseAmount): void
    {
        $product = $originalVariant->product;

        // Récupérer toutes les variantes du produit sauf celle qui vient d'être modifiée
        $variants = $product->variants()->where('id', '!=', $originalVariant->id)->get();

        foreach ($variants as $variant) {
            $newStock = \max(0, $variant->stock - $decreaseAmount);
            $variant->updateQuietly(['stock' => $newStock]);
        }
    }

    /**
     * Gérer l'augmentation de stock.
     */
    protected function handleStockIncrease(ProductVariant $originalVariant, int $newStock): void
    {
        $product = $originalVariant->product;

        // Récupérer toutes les variantes du produit sauf celle qui vient d'être modifiée
        $variants = $product->variants()->where('id', '!=', $originalVariant->id)->get();

        foreach ($variants as $variant) {
            $variant->updateQuietly(['stock' => $newStock]);
        }
    }
}
