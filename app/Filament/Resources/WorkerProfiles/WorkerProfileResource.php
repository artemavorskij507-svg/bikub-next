<?php

namespace App\Filament\Resources\WorkerProfiles;

use App\Filament\Resources\WorkerProfiles\Pages\CreateWorkerProfile;
use App\Filament\Resources\WorkerProfiles\Pages\EditWorkerProfile;
use App\Filament\Resources\WorkerProfiles\Pages\ListWorkerProfiles;
use App\Filament\Resources\WorkerProfiles\Pages\ViewWorkerProfile;
use App\Models\WorkerProfile;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Infolists\Components\TextEntry;
use Fahiem\FilamentPinpoint\PinpointEntry;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class WorkerProfileResource extends Resource
{
    protected static ?string $model = WorkerProfile::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedIdentification;
    protected static string|\UnitEnum|null $navigationGroup = 'People';
    protected static ?string $navigationLabel = 'Worker Profiles';
    public static function form(Schema $schema): Schema { return $schema->components([
        Section::make('Real worker identity')->columns(2)->schema([
            Select::make('user_id')->relationship('user','email')->required()->searchable()->preload()->unique(ignoreRecord:true),
            TextInput::make('display_name')->maxLength(255), Select::make('worker_type')->options(['courier'=>'Courier','worker'=>'Worker','driver'=>'Driver'])->required(),
            Select::make('status')->options(['pending'=>'Pending','approved'=>'Approved','rejected'=>'Rejected','suspended'=>'Suspended'])->disabled()->dehydrated(false)->helperText('Approval and suspension require the audited onboarding workflow.'),
            TextInput::make('phone')->tel()->maxLength(255), TextInput::make('vehicle_type')->maxLength(255), TextInput::make('service_area')->maxLength(255),
            Checkbox::make('can_deliver'), Checkbox::make('can_move'), Checkbox::make('can_handle_eco'), Checkbox::make('can_do_handyman'), Checkbox::make('can_tow'), Checkbox::make('can_run_errands'),
        ]),
    ]); }
    public static function table(Table $table): Table { return $table->columns([
        TextColumn::make('user.email')->label('Existing user')->searchable(), TextColumn::make('display_name')->placeholder('Not set'),
        TextColumn::make('worker_type')->badge(), TextColumn::make('status')->badge(), TextColumn::make('availability.status')->label('Availability')->badge()->placeholder('Offline / not set'),
        IconColumn::make('can_deliver')->boolean(), TextColumn::make('service_area')->placeholder('Not set'),
    ])->recordActions([ViewAction::make(), EditAction::make()]); }
    public static function infolist(Schema $schema): Schema { return $schema->components([
        Section::make('Worker location telemetry')->schema([
            TextEntry::make('user.email')->label('Worker account'),
            TextEntry::make('availability.status')->label('Presence')->badge()->placeholder('Offline / not set'),
            TextEntry::make('last_ping')->label('Latest real GPS ping')->state(fn(WorkerProfile $record)=>$record->latestLocationPing()?->captured_at?->format('Y-m-d H:i:s') ?? 'No real GPS ping yet'),
        ])->columns(3),
        Section::make('Latest real location')->description('Read-only Leaflet map. Hidden until a real browser ping exists.')->schema([
            PinpointEntry::make('latest_location')->provider('leaflet')->pins(fn(WorkerProfile $record)=>($ping=$record->latestLocationPing())?[['lat'=>(float)$ping->latitude,'lng'=>(float)$ping->longitude,'label'=>$record->display_name ?: $record->user?->name]]:[])->height(360)->columnSpanFull(),
        ])->visible(fn(WorkerProfile $record)=>$record->locationPings()->exists()),
    ]); }
    public static function getPages(): array { return ['index'=>ListWorkerProfiles::route('/'),'create'=>CreateWorkerProfile::route('/create'),'view'=>ViewWorkerProfile::route('/{record}'),'edit'=>EditWorkerProfile::route('/{record}/edit')]; }
}
