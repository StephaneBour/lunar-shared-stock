<?php

namespace StephaneBour\LunarSharedStock;

use Illuminate\Support\ServiceProvider;
use Lunar\Admin\Support\Facades\LunarPanel;
use Lunar\Models\Product;
use Lunar\Models\ProductVariant;
use StephaneBour\LunarSharedStock\Models\Concerns\HasSharedStock;
use StephaneBour\LunarSharedStock\Observers\ProductVariantObserver;

class SharedStockServiceProvider extends ServiceProvider
{
    /**
     * Enregistrement des services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/lunar-shared-stock.php',
            'lunar-shared-stock'
        );
    }

    /**
     * Bootstrap des services.
     */
    public function boot(): void
    {
        // Configuration
        $this->publishes([
            __DIR__.'/../config/lunar-shared-stock.php' => config_path('lunar-shared-stock.php'),
        ], 'lunar-shared-stock-config');

        // Migrations
        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');

        // Translations
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'lunar-shared-stock');

        $this->publishes([
            __DIR__.'/../resources/lang' => $this->app->langPath('vendor/lunar-shared-stock'),
        ], 'lunar-shared-stock-translations');

        // Views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'lunar-shared-stock');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/lunar-shared-stock'),
        ], 'lunar-shared-stock-views');

        // IMPORTANT : Étendre les modèles Lunar AVANT d'enregistrer l'observer
        $this->extendLunarModels();

        // Enregistrer les macros
        $this->registerMacros();

        // Observer pour synchroniser le stock (APRÈS l'extension des modèles)
        ProductVariant::observe(ProductVariantObserver::class);

        // Enregistrer le plugin Filament avec Lunar
        $this->registerLunarPlugin();
    }

    /**
     * Enregistrer le plugin avec le panel Lunar.
     */
    protected function registerLunarPlugin(): void
    {
        // Enregistrer les extensions d'abord
        LunarPanel::extensions([
            \Lunar\Admin\Filament\Resources\ProductResource\Pages\ManageProductVariants::class => \StephaneBour\LunarSharedStock\Filament\Extensions\ManageProductVariantsPageExtension::class,
        ]);

        // Puis enregistrer le plugin
        LunarPanel::panel(fn ($panel) => $panel->plugin(LunarSharedStockPlugin::make()));
    }

    /**
     * Étendre les modèles Lunar avec le trait HasSharedStock.
     */
    protected function extendLunarModels(): void
    {
        // Utiliser le mixin pour ajouter les méthodes du trait HasSharedStock
        Product::mixin(new HasSharedStock());
    }

    /**
     * Enregistrer les macros pour les modèles.
     */
    protected function registerMacros(): void
    {
        // Les macros sont maintenant définies dans le trait HasSharedStock
        // Cette méthode est conservée pour d'éventuelles futures extensions
    }
}
