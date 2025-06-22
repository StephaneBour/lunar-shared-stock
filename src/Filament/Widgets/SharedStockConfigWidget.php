<?php

declare(strict_types=1);

namespace StephaneBour\LunarSharedStock\Filament\Widgets;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class SharedStockConfigWidget extends Widget implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    protected static string $view = 'lunar-shared-stock::widgets.shared-stock-config';

    protected int | string | array $columnSpan = 'full';

    public ?Model $record = null;

    public function mount(?Model $record = null): void
    {
        // Récupérer le record depuis différentes sources
        $this->record = $record
            ?? $this->getOwnerRecord()
            ?? request()->route('record');
    }

    protected function getOwnerRecord(): ?Model
    {
        // Dans une page de relation, récupérer le record parent
        $livewire = app('livewire');
        $component = $livewire->current();

        if ($component && \property_exists($component, 'ownerRecord')) {
            return $component->ownerRecord;
        }

        return null;
    }

    public function manageSharedStockAction(): Action
    {
        return Action::make('manageSharedStock')
            ->label(__('lunar-shared-stock::shared-stock.shared_stock_management'))
            ->icon('heroicon-o-cog-6-tooth')
            ->color('primary')
            ->modal()
            ->modalWidth('md')
            ->fillForm(function (): array {
                return [
                    'shared_stock' => $this->record?->shared_stock ?? false,
                ];
            })
            ->form([
                Section::make(__('lunar-shared-stock::shared-stock.shared_stock_management'))
                    ->description(__('lunar-shared-stock::shared-stock.shared_stock_management_desc'))
                    ->schema([
                        Toggle::make('shared_stock')
                            ->label(__('lunar-shared-stock::shared-stock.shared_stock'))
                            ->helperText(__('lunar-shared-stock::shared-stock.shared_stock_help'))
                            ->live(),
                    ]),
            ])
            ->action(function (array $data): void {
                if ($this->record) {
                    $this->record->forceFill([
                        'shared_stock' => $data['shared_stock'] ?? false,
                    ])->save();

                    Notification::make()
                        ->title(__('lunar-shared-stock::shared-stock.settings_saved'))
                        ->success()
                        ->send();

                    // Rafraîchir le widget
                    $this->dispatch('$refresh');
                } else {
                    Notification::make()
                        ->title(__('filament::resources/pages/edit-record.notifications.saved.title'))
                        ->body(__('lunar-shared-stock::shared-stock.no_info_available'))
                        ->danger()
                        ->send();

                    Log::warning('SharedStockConfigWidget: Aucun record trouvé lors de l\'action');
                }
            });
    }

    public function getHeading(): string
    {
        if ($this->record?->shared_stock ?? false) {
            return __('lunar-shared-stock::shared-stock.shared_stock_enabled');
        }

        return __('lunar-shared-stock::shared-stock.shared_stock_management');
    }

    public function getDescription(): ?string
    {
        return __('lunar-shared-stock::shared-stock.shared_stock_disabled');
    }

    protected function getHeaderActions(): array
    {
        return [
            $this->manageSharedStockAction(),
        ];
    }

    public function getActions(): array
    {
        return $this->getHeaderActions();
    }
}
