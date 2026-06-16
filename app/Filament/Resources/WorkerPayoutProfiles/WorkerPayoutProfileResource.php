<?php

namespace App\Filament\Resources\WorkerPayoutProfiles;

use App\Filament\Resources\WorkerPayoutProfiles\Pages\ListWorkerPayoutProfiles;
use App\Models\WorkerPayoutProfile;
use App\Services\Finance\WorkerPayoutProfileService;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class WorkerPayoutProfileResource extends Resource
{
    protected static ?string $model = WorkerPayoutProfile::class;

    protected static string|UnitEnum|null $navigationGroup = 'Finance';

    protected static ?string $navigationLabel = 'Worker Payout Profiles';

    public static function canAccess(): bool
    {
        return auth()->user()?->can('admin.payouts.view') ?? false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('worker.name'),
                TextColumn::make('status')->badge(),
                TextColumn::make('payout_method')->badge(),
                IconColumn::make('bank_present')
                    ->state(fn (?WorkerPayoutProfile $record): bool => filled($record?->encrypted_bank_account) || filled($record?->encrypted_iban))
                    ->boolean(),
                IconColumn::make('vipps_present')
                    ->state(fn (?WorkerPayoutProfile $record): bool => filled($record?->encrypted_vipps_phone))
                    ->boolean(),
                TextColumn::make('tax_profile_status')->badge(),
                TextColumn::make('identity_profile_status')->badge(),
                TextColumn::make('submitted_at')->dateTime(),
            ])
            ->recordActions([
                Action::make('requestChanges')
                    ->form([Textarea::make('reason')->required()])
                    ->action(fn (WorkerPayoutProfile $record, array $data) => app(WorkerPayoutProfileService::class)->requestChanges($record, auth()->user(), $data['reason'])),
                Action::make('approve')
                    ->form([Textarea::make('note')->required()])
                    ->action(fn (WorkerPayoutProfile $record, array $data) => app(WorkerPayoutProfileService::class)->approve($record, auth()->user(), $data['note'])),
                Action::make('reject')
                    ->form([Textarea::make('reason')->required()])
                    ->action(fn (WorkerPayoutProfile $record, array $data) => app(WorkerPayoutProfileService::class)->reject($record, auth()->user(), $data['reason'])),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWorkerPayoutProfiles::route('/'),
        ];
    }
}
