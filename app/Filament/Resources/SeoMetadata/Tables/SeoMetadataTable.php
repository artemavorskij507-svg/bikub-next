<?php

namespace App\Filament\Resources\SeoMetadata\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SeoMetadataTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('path')->searchable()->copyable()->placeholder('Model-owned metadata'),
                TextColumn::make('locale')->badge()->sortable(),
                TextColumn::make('seo_title')->label('SEO title')->searchable()->limit(50)->placeholder('Not set'),
                TextColumn::make('robots')->badge()->placeholder('Not set'),
                TextColumn::make('updated_at')->dateTime()->sortable(),
            ])
            ->filters([
                //
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
