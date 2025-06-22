<?php

namespace StephaneBour\LunarSharedStock\Listeners;

use Lunar\Events\PaymentAttemptEvent;
use Lunar\Models\Order;
use Lunar\Models\ProductVariant;
use StephaneBour\LunarSharedStock\Managers\SharedStockManager;

class SharedStockOrderListener
{
    /**
     * Gérer l'événement de paiement réussi.
     */
    public function handle(PaymentAttemptEvent $event): void
    {
        if (! $event->paymentAuthorize->success) {
            return;
        }

        $order = Order::find($event->paymentAuthorize->orderId);

        if (! $order) {
            return;
        }

        $this->processOrderStock($order);
    }

    /**
     * Traiter le stock pour une commande.
     */
    protected function processOrderStock(Order $order): void
    {
        $order->lines->each(function ($line): void {
            $variant = ProductVariant::find($line->purchasable_id);

            if (! $variant || ! $variant->product) {
                return;
            }

            $product = $variant->product;
            $quantity = $line->quantity;

            // Vérifier si le produit utilise le stock partagé
            if ($product->usesSharedStock()) {
                $this->decrementSharedStock($product, $quantity);
            } else {
                $this->decrementIndividualStock($variant, $quantity);
            }
        });
    }

    /**
     * Décrémenter le stock partagé d'un produit.
     *
     * @param mixed $product
     */
    protected function decrementSharedStock($product, int $quantity): void
    {
        $success = app(SharedStockManager::class)->decrementStock($product, $quantity);

        if (! $success) {
            // Log ou gérer l'erreur si le stock ne peut pas être décrémenté
            \Illuminate\Support\Facades\Log::warning('Stock partagé insuffisant', [
                'product_id' => $product->id,
                'requested_quantity' => $quantity,
                'available_stock' => $product->available_stock,
            ]);
        }
    }

    /**
     * Décrémenter le stock individuel d'une variante.
     *
     * @param mixed $variant
     */
    protected function decrementIndividualStock($variant, int $quantity): void
    {
        $stock = $variant->stock;
        $backorder = $variant->backorder;

        if ($stock >= $quantity) {
            $variant->decrement('stock', $quantity);
        } elseif ($stock + $backorder >= $quantity) {
            $variant->decrement('stock', $stock);
            $variant->decrement('backorder', $quantity - $stock);
        } else {
            $variant->decrement('stock', $stock);
            $variant->decrement('backorder', $backorder);

            // Log la situation où il n'y a pas assez de stock
            \Illuminate\Support\Facades\Log::warning('Stock individuel insuffisant', [
                'variant_id' => $variant->id,
                'requested_quantity' => $quantity,
                'available_stock' => $stock,
                'available_backorder' => $backorder,
            ]);
        }
    }
}
