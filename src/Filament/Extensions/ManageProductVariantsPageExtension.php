<?php

declare(strict_types=1);

namespace StephaneBour\LunarSharedStock\Filament\Extensions;

use Lunar\Admin\Support\Extending\RelationPageExtension;
use StephaneBour\LunarSharedStock\Filament\Widgets\SharedStockConfigWidget;

class ManageProductVariantsPageExtension extends RelationPageExtension
{
    public function headerWidgets(array $widgets): array
    {
        return [
            ...$widgets,
            // instance du widget Filament
            SharedStockConfigWidget::make(),
        ];
    }
}
