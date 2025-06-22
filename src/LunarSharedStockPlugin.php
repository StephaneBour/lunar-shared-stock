<?php

namespace StephaneBour\LunarSharedStock;

use Filament\Contracts\Plugin;
use Filament\Panel;
use StephaneBour\LunarSharedStock\Filament\Widgets\SharedStockConfigWidget;

class LunarSharedStockPlugin implements Plugin
{
    protected bool $hasSharedStockWidget = true;

    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'lunar-shared-stock';
    }

    public function register(Panel $panel): void
    {
        if ($this->hasSharedStockWidget()) {
            $panel->widgets([
                SharedStockConfigWidget::class,
            ]);
        }
    }

    public function boot(Panel $panel): void
    {
        // Méthode appelée lorsque le panel est utilisé
    }

    public function sharedStockWidget(bool $condition = true): static
    {
        $this->hasSharedStockWidget = $condition;

        return $this;
    }

    public function hasSharedStockWidget(): bool
    {
        return $this->hasSharedStockWidget;
    }
}
