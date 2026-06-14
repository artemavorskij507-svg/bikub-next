<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use App\Services\Finance\BillingDocumentService;
use App\Services\Finance\QuoteCalculationService;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('calculate_quote')->label('Calculate quote')->action(fn () => app(QuoteCalculationService::class)->calculateForOrder($this->record, auth()->user()))->visible(fn () => ! $this->record->latestPriceQuote()),
            Action::make('recalculate_quote')->label('Recalculate quote')->schema([Textarea::make('reason')->required()])->action(fn (array $data) => app(QuoteCalculationService::class)->recalculateForOrder($this->record, auth()->user(), $data['reason']))->visible(fn () => (bool) $this->record->latestPriceQuote()),
            Action::make('create_invoice')->label('Create draft invoice')->action(fn () => app(BillingDocumentService::class)->createDraftInvoiceForOrder($this->record, auth()->user())),
            Action::make('finance_control')->label('Open Finance Control')->url(route('filament.admin.pages.finance-control')),
            ...OrderResource::ownershipActions(),
        ];
    }
}
