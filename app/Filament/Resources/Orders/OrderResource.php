<?php

namespace App\Filament\Resources\Orders;

use App\Filament\Resources\Orders\Pages\EditOrder;
use App\Filament\Resources\Orders\Pages\ListOrders;
use App\Filament\Resources\Orders\Pages\ViewOrder;
use App\Models\Order;
use App\Models\User;
use App\Services\Account\CustomerOwnershipService;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use App\Services\Support\SupportTicketService;
use App\Filament\Resources\SupportTickets\SupportTicketResource;
use Filament\Forms\Components\Select;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Infolists\Components\TextEntry;
use Fahiem\FilamentPinpoint\PinpointEntry;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    public static function canAccess(): bool { return config('database.default') === 'sqlite' && ! extension_loaded('pdo_sqlite') ? auth()->check() : (auth()->user()?->can('admin.orders.view') ?? false); }
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
        ])->recordActions([ViewAction::make(), Action::make('create_support')->label('Create support ticket')->schema([TextInput::make('subject')->default(fn(Order $record)=>'Order issue: '.$record->order_number)->required(),Select::make('category')->options(SupportTicketResource::categories())->default('order_issue')->required(),Select::make('priority')->options(SupportTicketResource::priorities())->default('normal')->required(),Textarea::make('summary'),Textarea::make('internal_note')])->action(function(Order $record,array $data){$assignment=$record->activeDispatchAssignment();$ticket=app(SupportTicketService::class)->createTicket(['subject'=>$data['subject'],'category'=>$data['category'],'priority'=>$data['priority'],'summary'=>$data['summary']??null,'source'=>'admin','visibility'=>'internal','order_id'=>$record->id,'dispatch_assignment_id'=>$assignment?->id,'worker_profile_id'=>$assignment?->assignedUser?->workerProfile?->id],auth()->user());if(filled($data['internal_note']??null))app(SupportTicketService::class)->addMessage($ticket,['body'=>$data['internal_note'],'message_type'=>'internal_note','visibility'=>'internal'],auth()->user());redirect(SupportTicketResource::getUrl('view',['record'=>$ticket]));}), EditAction::make()]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Order and real telemetry')->schema([
                TextEntry::make('order_number')->label('Order'),
                TextEntry::make('status')->badge(),
                TextEntry::make('customer_name')->label('Customer'),
                TextEntry::make('latest_ping_status')
                    ->label('Latest real GPS ping')
                    ->state(fn (Order $record) => $record->workerLocationPings()->first()?->captured_at?->format('Y-m-d H:i:s') ?? 'No real GPS ping yet'),
            ])->columns(2),
            Section::make('Customer Ownership')->description('Account access is granted only through this explicit ownership relation.')->schema([
                TextEntry::make('ownership_status')->label('Ownership status')->state(fn (Order $record) => $record->customer_id ? 'Linked' : 'Unlinked')->badge(),
                TextEntry::make('customer.name')->label('Account owner')->placeholder('Ownership not linked'),
                TextEntry::make('customer.email')->label('Owner email')->placeholder('Ownership not linked'),
                TextEntry::make('account_visibility')->label('Account visibility')->state(fn (Order $record) => $record->customer_id ? 'Visible to linked account' : 'Admin-only'),
            ])->columns(2),
            Section::make('Support')->description('Customer-linked support visibility follows the explicit order owner.')->schema([TextEntry::make('support_open')->label('Open tickets')->state(fn(Order $record)=>$record->supportTickets()->whereNotIn('status',['resolved','closed'])->count()),TextEntry::make('support_total')->label('Total tickets')->state(fn(Order $record)=>$record->supportTickets()->count()),TextEntry::make('support_latest')->label('Latest ticket')->state(fn(Order $record)=>optional($record->supportTickets()->first(),fn($t)=>$t->ticket_number.' · '.str($t->status)->replace('_',' ')->title().' · '.str($t->priority)->title().' · '.($t->assignee?->name??'Unassigned').' · '.($t->last_message_at?->diffForHumans()??'No messages'))??'No support tickets')->url(function(Order $record){$ticket=$record->supportTickets()->first();return $ticket?SupportTicketResource::getUrl('view',['record'=>$ticket]):null;}),TextEntry::make('support_all')->label('All linked tickets')->state(fn(Order $record)=>$record->supportTickets()->get()->map(fn($ticket)=>$ticket->ticket_number.' · '.str($ticket->status)->replace('_',' ')->title())->join(' | ')?:'No support tickets')])->columns(2),
            Section::make('Latest worker location')
                ->description('Read-only Leaflet map from the latest real worker_location_pings record.')
                ->schema([
                    PinpointEntry::make('latest_worker_location')
                        ->provider('leaflet')
                        ->pins(fn (Order $record) => ($ping = $record->workerLocationPings()->first()) ? [[
                            'lat' => (float) $ping->latitude,
                            'lng' => (float) $ping->longitude,
                            'label' => $record->order_number,
                        ]] : [])
                        ->height(360)
                        ->columnSpanFull(),
                ])
                ->visible(fn (Order $record) => $record->workerLocationPings()->exists()),
        ]);
    }

    public static function getPages(): array
    {
        return ['index' => ListOrders::route('/'), 'view' => ViewOrder::route('/{record}'), 'edit' => EditOrder::route('/{record}/edit')];
    }

    public static function ownershipActions(): array
    {
        return [
            Action::make('link_customer')
                ->label('Link customer account')
                ->schema([
                    Select::make('customer_id')->label('Customer account')->options(User::query()->orderBy('name')->pluck('email', 'id'))->searchable()->required(),
                    Textarea::make('reason')->helperText('This account will be able to view this order in /account.')->required(),
                ])
                ->action(fn (Order $record, array $data) => app(CustomerOwnershipService::class)->linkOrderToCustomer($record, User::findOrFail($data['customer_id']), auth()->user(), $data['reason']))
                ->visible(fn (Order $record) => ! $record->customer_id),
            Action::make('unlink_customer')
                ->label('Unlink customer account')
                ->color('danger')
                ->requiresConfirmation()
                ->schema([Textarea::make('reason')->required()])
                ->action(fn (Order $record, array $data) => app(CustomerOwnershipService::class)->unlinkOrderFromCustomer($record, auth()->user(), $data['reason']))
                ->visible(fn (Order $record) => (bool) $record->customer_id),
        ];
    }
}
