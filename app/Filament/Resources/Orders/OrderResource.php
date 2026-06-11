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
            Section::make('Read-only foundation')->description('Lifecycle events are recorded by the Order Engine. Status transitions, payments and dispatch are intentionally not editable here.'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('order_number')->searchable()->copyable(),
            TextColumn::make('scenario.title')->label('Service')->searchable(),
            TextColumn::make('customer_name')->searchable()->placeholder('Not provided'),
            TextColumn::make('status')->badge(),
            TextColumn::make('payment_status')->badge(),
            TextColumn::make('submitted_at')->dateTime()->sortable(),
        ])->filters([
            SelectFilter::make('status')->options(array_combine(array_map(fn($s) => $s->value, \App\Enums\OrderStatus::cases()), array_map(fn($s) => ucfirst(str_replace('_', ' ', $s->value)), \App\Enums\OrderStatus::cases()))),
        ])->recordActions([EditAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => ListOrders::route('/'), 'edit' => EditOrder::route('/{record}/edit')];
    }
}
