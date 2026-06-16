<?php

namespace App\Filament\Resources\ClassifiedListings\Tables;

use App\Models\ClassifiedListing;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ClassifiedListingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('listing_number')->searchable()->copyable(),
                TextColumn::make('title')->searchable()->sortable()->limit(42),
                TextColumn::make('category.name')->label('Category')->sortable(),
                TextColumn::make('owner.email')->label('Owner')->searchable(),
                TextColumn::make('price_amount')->money('NOK')->sortable(),
                TextColumn::make('location')->sortable(),
                TextColumn::make('status')->badge()->sortable(),
                IconColumn::make('is_featured')->boolean(),
                TextColumn::make('published_at')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->options([
                    ClassifiedListing::STATUS_PENDING => 'Pending',
                    ClassifiedListing::STATUS_APPROVED => 'Approved',
                    ClassifiedListing::STATUS_REJECTED => 'Rejected',
                    ClassifiedListing::STATUS_ARCHIVED => 'Archived',
                ]),
            ])
            ->recordActions([
                Action::make('approve')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (ClassifiedListing $record) => $record->status !== ClassifiedListing::STATUS_APPROVED)
                    ->action(fn (ClassifiedListing $record) => $record->forceFill([
                        'status' => ClassifiedListing::STATUS_APPROVED,
                        'published_at' => $record->published_at ?? now(),
                        'moderated_at' => now(),
                        'moderated_by_id' => auth()->id(),
                    ])->save()),
                Action::make('reject')
                    ->color('danger')
                    ->schema([
                        Textarea::make('moderation_note')->required()->label('Reason'),
                    ])
                    ->action(fn (ClassifiedListing $record, array $data) => $record->forceFill([
                        'status' => ClassifiedListing::STATUS_REJECTED,
                        'moderation_note' => $data['moderation_note'],
                        'moderated_at' => now(),
                        'moderated_by_id' => auth()->id(),
                    ])->save()),
                Action::make('archive')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->action(fn (ClassifiedListing $record) => $record->forceFill([
                        'status' => ClassifiedListing::STATUS_ARCHIVED,
                        'moderated_at' => now(),
                        'moderated_by_id' => auth()->id(),
                    ])->save()),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }
}
