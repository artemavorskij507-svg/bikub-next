<?php

namespace App\Filament\Resources\Orders;

use App\Filament\Resources\Orders\Pages\EditOrder;
use App\Filament\Resources\Orders\Pages\ListOrders;
use App\Models\Order;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;
    protected static string|\UnitEnum|null $navigationGroup = 'Orders';
    protected static ?string $recordTitleAttribute = 'order_number';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Order request')->schema([
                TextInput::make('order_number')->disabled()->dehydrated(false),
                TextInput::make('service_scenario_key')->label('Scenario')->disabled()->dehydrated(false),
                TextInput::make('customer_name')->disabled()->dehydrated(false),
                TextInput::make('customer_email')->disabled()->dehydrated(false),
                TextInput::make('customer_phone')->disabled()->dehydrated(false),
                DateTimePicker::make('scheduled_at')->seconds(false),
                Textarea::make('internal_notes')->rows(5),
            ])->columns(2),
            Section::make('Submitted intake')->description('Validated scenario intake captured with the order.')->schema([
                Textarea::make('metadata.intake')
                    ->label('Intake payload')
                    ->formatStateUsing(fn ($state) => json_encode($state ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))
                    ->rows(12)
                    ->disabled()
                    ->dehydrated(false),
            ]),
            Section::make('Latest price quote')->description('Read-only estimate. No payment provider is connected.')->schema([
                TextInput::make('estimated_total')->prefix('NOK')->disabled()->dehydrated(false),
                Textarea::make('latest_quote')
                    ->formatStateUsing(function ($state, $record) {
                        $quote = $record?->latestPriceQuote();
                        return $quote ? json_encode(['status' => $quote->status, 'currency' => $quote->currency, 'total' => $quote->total, 'breakdown' => $quote->breakdown], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : 'No quote generated.';
                    })->rows(10)->disabled()->dehydrated(false),
            ])->columns(2),
            Section::make('Lifecycle events')->schema([
                Textarea::make('lifecycle_events')
                    ->formatStateUsing(fn ($state, $record) => $record ? json_encode($record->events()->get(['event_type', 'from_status', 'to_status', 'created_at'])->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : 'No lifecycle events.')
                    ->rows(12)->disabled()->dehydrated(false),
            ]),
            Section::make('Dispatch')->description('Audit-backed dispatch state. Real browser location pings are counted; no customer tracking or map is exposed.')->schema([
                TextInput::make('dispatch_assignment')
                    ->formatStateUsing(fn ($state, $record) => $record?->activeDispatchAssignment()?->assignedUser?->name ?? 'Not assigned')
                    ->disabled()->dehydrated(false),
                Textarea::make('dispatch_events')
                    ->formatStateUsing(fn ($state, $record) => $record ? json_encode($record->dispatchEvents()->get(['event_type', 'payload', 'note', 'created_at'])->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : 'No dispatch events.')
                    ->rows(10)->disabled()->dehydrated(false),
                TextInput::make('location_ping_count')
                    ->label('Real location pings')
                    ->formatStateUsing(fn ($state, $record) => $record?->workerLocationPings()->count() ?? 0)
                    ->disabled()->dehydrated(false),
                TextInput::make('last_worker_progress')
                    ->label('Latest worker progress')
                    ->formatStateUsing(fn ($state, $record) => $record?->dispatchEvents()->where('event_type', 'like', 'worker.%')->first()?->event_type ?? 'No worker progress recorded')
                    ->disabled()->dehydrated(false),
            ])->columns(2),
            Section::make('Read-only foundation')->description('Lifecycle events are recorded by the Order Engine. Status transitions, payments and dispatch are intentionally not editable here.'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('order_number')->searchable()->copyable(),
            TextColumn::make('scenario.title')->label('Service')->searchable(),
            TextColumn::make('service_scenario_key')->label('Scenario key')->toggleable(),
            TextColumn::make('customer_name')->searchable()->placeholder('Not provided'),
            TextColumn::make('customer_email')->searchable()->toggleable(),
            TextColumn::make('customer_phone')->searchable()->toggleable(),
            TextColumn::make('status')->badge()->formatStateUsing(fn ($state) => str($state instanceof \BackedEnum ? $state->value : $state)->replace('_', ' ')->title()),
            TextColumn::make('payment_status')->badge()->formatStateUsing(fn ($state) => str($state instanceof \BackedEnum ? $state->value : $state)->replace('_', ' ')->title()),
            TextColumn::make('estimated_total')->money('NOK')->placeholder('Manual review'),
            TextColumn::make('priceQuotes.status')->label('Latest quote')->badge()->limitList(1),
            TextColumn::make('events_count')->counts('events')->label('Events'),
            TextColumn::make('dispatch_events_count')->counts('dispatchEvents')->label('Dispatch events'),
            TextColumn::make('submitted_at')->dateTime()->sortable(),
        ])->filters([
            SelectFilter::make('status')->options(array_combine(array_map(fn($s) => $s->value, \App\Enums\OrderStatus::cases()), array_map(fn($s) => ucfirst(str_replace('_', ' ', $s->value)), \App\Enums\OrderStatus::cases()))),
            SelectFilter::make('payment_status')->options(['not_required'=>'Not required','pending'=>'Pending']),
        ])->recordActions([EditAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => ListOrders::route('/'), 'edit' => EditOrder::route('/{record}/edit')];
    }
}
