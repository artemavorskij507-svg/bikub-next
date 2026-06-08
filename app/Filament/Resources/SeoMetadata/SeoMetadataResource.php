<?php

namespace App\Filament\Resources\SeoMetadata;

use App\Filament\Resources\SeoMetadata\Pages\CreateSeoMetadata;
use App\Filament\Resources\SeoMetadata\Pages\EditSeoMetadata;
use App\Filament\Resources\SeoMetadata\Pages\ListSeoMetadata;
use App\Filament\Resources\SeoMetadata\Schemas\SeoMetadataForm;
use App\Filament\Resources\SeoMetadata\Tables\SeoMetadataTable;
use App\Models\SeoMetadata;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SeoMetadataResource extends Resource
{
    protected static ?string $model = SeoMetadata::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMagnifyingGlass;

    protected static string|\UnitEnum|null $navigationGroup = 'Content';

    protected static ?string $navigationLabel = 'SEO Metadata';

    protected static ?string $modelLabel = 'SEO metadata';

    protected static ?string $pluralModelLabel = 'SEO metadata';

    protected static ?int $navigationSort = 40;

    protected static ?string $recordTitleAttribute = 'path';

    public static function form(Schema $schema): Schema
    {
        return SeoMetadataForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SeoMetadataTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSeoMetadata::route('/'),
            'create' => CreateSeoMetadata::route('/create'),
            'edit' => EditSeoMetadata::route('/{record}/edit'),
        ];
    }
}
