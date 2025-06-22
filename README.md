# Lunar Shared Stock

A powerful Laravel package for Lunar ecommerce that provides automatic shared inventory management between product variants. This package allows you to synchronize stock levels automatically when variants are activated, ensuring consistent inventory management across all product options.

## ✨ Features

- **Automatic Stock Synchronization**: Automatically sync inventory when product variants are activated
- **Shared Inventory Management**: Share stock between multiple product variants
- **Backorder Support**: Intelligent handling of backorders in shared stock scenarios
- **Event-Driven Architecture**: Custom events for stock updates and synchronization
- **Filament Integration**: Seamless integration with Lunar's admin panel
- **Real-time Updates**: Live stock updates across all variants
- **Observer Pattern**: Automatic stock management through Laravel observers

## 📋 Requirements

### Minimum Requirements

- **PHP**: ^8.1
- **Laravel**: ^10.0 || ^11.0 || ^12.0
- **Lunar**: ^1.0
- **Filament**: ^3.0 || ^4.0

### Recommended Environment

- PHP 8.2+
- Laravel 11+
- MySQL 8.0+ or PostgreSQL 13+
- Redis for caching (optional but recommended)

## 🚀 Installation

### 1. Install via Composer

```bash
composer require stephanebour/lunar-shared-stock
```

### 2. Publish Configuration (Optional)

```bash
php artisan vendor:publish --tag="lunar-shared-stock-config"
```

### 3. Run Migrations

```bash
php artisan migrate
```

### 4. Publish Translations (Optional)

```bash
php artisan vendor:publish --tag="lunar-shared-stock-translations"
```

### 5. Publish Views (Optional)

```bash
php artisan vendor:publish --tag="lunar-shared-stock-views"
```

## ⚙️ Configuration

The configuration file `config/lunar-shared-stock.php` allows you to customize the package behavior:

```php
<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Automatic Synchronization
    |--------------------------------------------------------------------------
    |
    | Automatically synchronize stock when a variant is modified
    |
    */
    'auto_sync' => true,

    /*
    |--------------------------------------------------------------------------
    | Backorder Management
    |--------------------------------------------------------------------------
    |
    | How to handle backorders in shared stock
    |
    */
    'shared_backorder' => true,

    /*
    |--------------------------------------------------------------------------
    | Custom Events
    |--------------------------------------------------------------------------
    |
    | Enable custom event emission for shared stock
    |
    */
    'events' => [
        'stock_updated' => true,
    ],
];
```

## 🎯 Usage

### Basic Usage

The package automatically extends Lunar's `Product` model with shared stock functionality:

```php
use Lunar\Models\Product;

$product = Product::find(1);

// Check if product uses shared stock
if ($product->usesSharedStock()) {
    // Get total shared stock
    $totalStock = $product->total_stock;
    
    // Get available stock (including backorders)
    $availableStock = $product->available_stock;
    
    // Check availability for specific quantity
    $isAvailable = $product->isGloballyAvailable(5);
    
    // Decrement shared stock
    $product->decrementSharedStock(2);
}
```

### In Livewire Components

```php
// In your ProductPage Livewire component

#[Computed]
public function getTotalStockProperty(): int
{
    return $this->product->usesSharedStock() 
        ? $this->product->total_stock 
        : $this->variant->stock;
}

public function isAvailable(int $quantity = 1): bool
{
    return $this->product->usesSharedStock()
        ? $this->product->isGloballyAvailable($quantity)
        : $this->variant->stock >= $quantity;
}
```

### In Blade Templates

```blade
@if($product->usesSharedStock())
    <div class="stock-info">
        @if($product->total_stock == 1)
            <span class="text-red-600">Last item in stock!</span>
        @elseif($product->total_stock < 10)
            <span class="text-orange-600">Only {{ $product->total_stock }} left!</span>
        @elseif($product->total_stock <= 0)
            <span class="text-gray-600">Out of stock</span>
        @endif
    </div>
@endif
```

## 🔧 Advanced Integration

### Custom Event Listeners

Listen to stock update events:

```php
use StephaneBour\LunarSharedStock\Events\SharedStockUpdated;

// In your EventServiceProvider
protected $listen = [
    SharedStockUpdated::class => [
        YourCustomStockListener::class,
    ],
];
```

### Extending Functionality

You can extend the package functionality by creating custom managers:

```php
use StephaneBour\LunarSharedStock\Managers\SharedStockManager;

class CustomStockManager extends SharedStockManager
{
    public function customSyncLogic(): void
    {
        // Your custom synchronization logic
    }
}
```

## 🎨 Filament Integration

The package automatically integrates with Lunar's Filament admin panel. Features include:

- **Stock Overview Widget**: Real-time stock levels across all variants
- **Bulk Stock Operations**: Manage stock for multiple variants simultaneously
- **Stock History**: Track stock changes over time
- **Low Stock Alerts**: Get notified when stock levels are low

No additional configuration is required - the integration works out of the box.

## 🔄 Migration from Individual Stock

If you're migrating from individual variant stock management:

```php
// Create a migration to transfer existing stock data
php artisan make:migration migrate_to_shared_stock

// In your migration
public function up()
{
    // Your migration logic to consolidate variant stocks
}
```

## 🚨 Troubleshooting

### Common Issues

1. **Stock not syncing**: Ensure `auto_sync` is enabled in config
2. **Observer not working**: Check that the service provider is properly registered
3. **Filament integration issues**: Verify Filament version compatibility

## 🔗 Links

- [Lunar Documentation](https://docs.lunarphp.io/)
- [Laravel Documentation](https://laravel.com/docs)
- [Filament Documentation](https://filamentphp.com/docs)

---

**Made with ❤️ for the Laravel and Lunar community**
