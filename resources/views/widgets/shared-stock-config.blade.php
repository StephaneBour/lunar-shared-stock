<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            {{ $this->getHeading() }}
        </x-slot>

        <x-slot name="description">
            {{ $this->getDescription() }}
        </x-slot>

        <x-slot name="headerActions">
            @foreach ($this->getHeaderActions() as $action)
                {{ $action }}
            @endforeach
        </x-slot>

        <div class="space-y-4">
            @if ($this->record?->shared_stock ?? false)
                <!-- État activé -->
                <div class="rounded-lg bg-green-50 dark:bg-green-900/20 p-4">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            <x-heroicon-o-check-circle class="h-6 w-6 text-green-500" />
                        </div>
                        <div class="flex-1">
                            <h3 class="text-sm font-medium text-green-800 dark:text-green-200">
                                {{ __('lunar-shared-stock::shared-stock.shared_stock_enabled') }}
                            </h3>
                        </div>
                    </div>
                </div>

                <!-- Statistiques -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">                    
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3">
                        <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                            {{ __('lunar-shared-stock::shared-stock.variants_count') }}
                        </div>
                        <div class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                            {{ $this->record->variants->count() }}
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3">
                        <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                            {{ __('lunar-shared-stock::shared-stock.stock_status') }}
                        </div>
                        <div class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                            {{ __('lunar-shared-stock::shared-stock.active') }}
                        </div>
                    </div>
                </div>
            @else
                <!-- État désactivé -->
                <div class="rounded-lg bg-gray-50 dark:bg-gray-900/50 p-4">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            <x-heroicon-o-x-circle class="h-6 w-6 text-gray-400" />
                        </div>
                        <div class="flex-1">
                            <h3 class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                {{ __('lunar-shared-stock::shared-stock.shared_stock_disabled') }}
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                {{ __('lunar-shared-stock::shared-stock.manual_sync_desc') }}
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Informations sur les variantes -->
                @if ($this->record?->variants?->count() > 0)
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                <x-heroicon-o-information-circle class="h-5 w-5 text-blue-500" />
                            </div>
                            <div class="flex-1">
                                <h4 class="text-sm font-medium text-blue-800 dark:text-blue-200">
                                    {{ $this->record->variants->count() }} {{ __('lunar-shared-stock::shared-stock.variants') }}
                                </h4>
                                <p class="text-sm text-blue-700 dark:text-blue-300">
                                    {{ __('lunar-shared-stock::shared-stock.redistribution_tip') }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </x-filament::section>

    <x-filament-actions::modals />
</x-filament-widgets::widget>