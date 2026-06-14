<?php

namespace App\Filament\Resources\SupportTickets;

use App\Filament\Resources\SupportTickets\Pages\CreateSupportTicket;
use App\Filament\Resources\SupportTickets\Pages\EditSupportTicket;
use App\Filament\Resources\SupportTickets\Pages\ListSupportTickets;
use App\Filament\Resources\SupportTickets\Pages\ViewSupportTicket;
use App\Models\SupportTicket;
use App\Services\Support\SupportTicketService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SupportTicketResource extends Resource
{
    protected static ?string $model = SupportTicket::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLifebuoy;

    protected static string|\UnitEnum|null $navigationGroup = 'Support';

    protected static ?string $recordTitleAttribute = 'ticket_number';

    public static function canAccess(): bool
    {
        return auth()->user()?->can('admin.support.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('admin.support.manage') ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()?->can('admin.support.manage') ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Ticket')->columns(2)->schema([
                TextInput::make('ticket_number')->disabled()->dehydrated(false),
                TextInput::make('subject')->required(),
                Select::make('status')->options(self::statuses())->required(),
                Select::make('priority')->options(self::priorities())->required(),
                Select::make('category')->options(self::categories())->required(),
                Select::make('visibility')->options([
                    'internal' => 'Internal',
                    'customer_visible' => 'Customer visible',
                    'worker_visible' => 'Worker visible',
                ])->required(),
                Select::make('order_id')->relationship('order', 'order_number')->searchable(),
                Select::make('customer_id')->relationship('customer', 'email')->searchable(),
                Select::make('worker_profile_id')->relationship('workerProfile', 'display_name')->searchable(),
                Select::make('worker_document_id')->relationship('workerDocument', 'document_type')->searchable(),
                Select::make('assigned_to_id')->relationship('assignee', 'email')->searchable(),
                Textarea::make('summary')->columnSpanFull(),
                SpatieMediaLibraryFileUpload::make('attachments')
                    ->collection('support_ticket_attachments')
                    ->disk('local')
                    ->visibility('private')
                    ->multiple()
                    ->columnSpanFull(),
            ]),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            View::make('filament.resources.support-tickets.view')->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->persistFiltersInSession()->persistSortInSession()->persistSearchInSession()->persistColumnSearchesInSession()->persistColumnsInSession()->columns([
                TextColumn::make('ticket_number')->searchable(),
                TextColumn::make('status')->badge()->formatStateUsing(fn ($state) => str($state)->replace('_', ' ')->title()),
                TextColumn::make('priority')->badge(),
                TextColumn::make('category')->badge()->formatStateUsing(fn ($state) => str($state)->replace('_', ' ')->title()),
                TextColumn::make('subject')->limit(42),
                TextColumn::make('order.order_number'),
                TextColumn::make('customer.email')->toggleable(),
                TextColumn::make('workerProfile.display_name')->label('Worker')->toggleable(),
                TextColumn::make('workerDocument.document_type')->label('Document')->toggleable(),
                TextColumn::make('assignee.name')->label('Assigned'),
                TextColumn::make('last_message_at')->since()->placeholder('No messages'),
                TextColumn::make('created_at')->since()->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')->options(self::statuses()),
                SelectFilter::make('priority')->options(self::priorities()),
                SelectFilter::make('category')->options(self::categories()),
                SelectFilter::make('assigned_to_id')->relationship('assignee', 'name')->label('Assigned to'),
                Filter::make('assigned_to_me')->query(fn (Builder $query) => $query->where('assigned_to_id', auth()->id())),
                Filter::make('unassigned')->query(fn (Builder $query) => $query->whereNull('assigned_to_id')),
                Filter::make('urgent')->query(fn (Builder $query) => $query->where('priority', 'urgent')),
                Filter::make('escalated')->query(fn (Builder $query) => $query->where('status', 'escalated')),
                Filter::make('created_today')->query(fn (Builder $query) => $query->whereDate('created_at', today())),
                Filter::make('updated_today')->query(fn (Builder $query) => $query->whereDate('updated_at', today())),
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('note')
                    ->label('Internal note')
                    ->schema([Textarea::make('body')->required()])
                    ->action(fn (SupportTicket $record, array $data) => app(SupportTicketService::class)->addMessage($record, [
                        'body' => $data['body'],
                        'message_type' => 'internal_note',
                        'visibility' => 'internal',
                    ], auth()->user()))
                    ->visible(fn (): bool => auth()->user()?->can('admin.support.internal_notes') ?? false),
                Action::make('customer_reply')
                    ->label('Customer reply')
                    ->schema([Textarea::make('body')->required()])
                    ->action(fn (SupportTicket $record, array $data) => app(SupportTicketService::class)->addMessage($record, [
                        'body' => $data['body'],
                        'message_type' => 'public_reply',
                        'visibility' => 'customer_visible',
                    ], auth()->user()))
                    ->visible(fn (SupportTicket $record): bool => filled($record->customer_id) && (auth()->user()?->can('admin.support.manage') ?? false)),
                Action::make('worker_reply')
                    ->label('Worker reply')
                    ->schema([Textarea::make('body')->required()])
                    ->action(fn (SupportTicket $record, array $data) => app(SupportTicketService::class)->addMessage($record, [
                        'body' => $data['body'],
                        'message_type' => 'public_reply',
                        'visibility' => 'worker_visible',
                    ], auth()->user()))
                    ->visible(fn (SupportTicket $record): bool => filled($record->worker_profile_id) && (auth()->user()?->can('admin.support.manage') ?? false)),
                Action::make('resolve')
                    ->schema([Textarea::make('note')->required()])
                    ->action(fn (SupportTicket $record, array $data) => app(SupportTicketService::class)->resolveTicket($record, auth()->user(), $data['note']))
                    ->visible(fn (SupportTicket $record): bool => ! in_array($record->status, ['resolved', 'closed'], true) && (auth()->user()?->can('admin.support.resolve') ?? false)),
                Action::make('assign_me')
                    ->label('Assign to me')
                    ->action(fn (SupportTicket $record) => app(SupportTicketService::class)->assignTicket($record, auth()->user(), auth()->user()))
                    ->visible(fn (): bool => auth()->user()?->can('admin.support.assign') ?? false),
                EditAction::make()->visible(fn (): bool => auth()->user()?->can('admin.support.manage') ?? false),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSupportTickets::route('/'),
            'create' => CreateSupportTicket::route('/create'),
            'view' => ViewSupportTicket::route('/{record}'),
            'edit' => EditSupportTicket::route('/{record}/edit'),
        ];
    }

    public static function statuses(): array
    {
        $values = ['open', 'pending_customer', 'pending_worker', 'pending_internal', 'escalated', 'resolved', 'closed'];

        return array_combine($values, array_map(fn ($value) => str($value)->replace('_', ' ')->title()->toString(), $values));
    }

    public static function priorities(): array
    {
        $values = ['low', 'normal', 'high', 'urgent'];

        return array_combine($values, array_map('ucfirst', $values));
    }

    public static function categories(): array
    {
        $values = ['order_issue', 'delivery_issue', 'worker_issue', 'payment_issue', 'document_issue', 'customer_question', 'system_issue', 'other'];

        return array_combine($values, array_map(fn ($value) => str($value)->replace('_', ' ')->title()->toString(), $values));
    }
}
