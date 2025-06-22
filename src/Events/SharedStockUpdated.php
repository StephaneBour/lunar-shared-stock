<?php

namespace StephaneBour\LunarSharedStock\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Lunar\Models\Product;

class SharedStockUpdated
{
    use Dispatchable;
    use SerializesModels;

    /**
     * Le produit concerné.
     */
    public Product $product;

    /**
     * L'ancien stock total.
     */
    public int $oldStock;

    /**
     * Le nouveau stock total.
     */
    public int $newStock;

    /**
     * Créer une nouvelle instance de l'événement.
     */
    public function __construct(Product $product, int $oldStock, int $newStock)
    {
        $this->product = $product;
        $this->oldStock = $oldStock;
        $this->newStock = $newStock;

        Log::info('SharedStockUpdated', [
            'product_id' => $product->id,
            'old_stock' => $oldStock,
            'new_stock' => $newStock,
        ]);
    }
}
