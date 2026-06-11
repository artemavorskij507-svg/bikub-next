<?php

namespace App\Filament\Resources\ServiceScenarios\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ServiceScenariosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->searchable()->sortable(),
                TextColumn::make('scenario_key')->searchable()->copyable(),
                TextColumn::make('category.title')->label('Category')->sortable(),
                TextColumn::make('status')->badge()->color(fn (string $state): string => match ($state) {
                    'active' => 'success', 'paused' => 'warning', 'archived' => 'gray', default => 'info',
                }),
                TextColumn::make('service_type')->badge()->sortable(),
                TextColumn::make('fields_count')->counts('fields')->label('Fields')->sortable(),
                TextColumn::make('base_price')->money(fn ($record) => $record->currency)->placeholder('Not configured'),
                IconColumn::make('requires_payment')->boolean()->label('Payment'),
                IconColumn::make('supports_live_tracking')->boolean()->label('Tracking'),
            ])
            ->filters([
                SelectFilter::make('status')->options([
                    'draft' => 'Draft', 'active' => 'Active', 'paused' => 'Paused', 'archived' => 'Archived',
                ]),
                SelectFilter::make('category_id')->relationship('category', 'title')->label('Category'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
