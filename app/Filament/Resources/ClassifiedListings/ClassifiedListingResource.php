<?php

namespace App\Filament\Resources\ClassifiedListings;

use App\Filament\Resources\ClassifiedListings\Pages\CreateClassifiedListing;
use App\Filament\Resources\ClassifiedListings\Pages\EditClassifiedListing;
use App\Filament\Resources\ClassifiedListings\Pages\ListClassifiedListings;
use App\Filament\Resources\ClassifiedListings\Schemas\ClassifiedListingForm;
use App\Filament\Resources\ClassifiedListings\Tables\ClassifiedListingsTable;
use App\Models\ClassifiedListing;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ClassifiedListingResource extends Resource
{
    protected static ?string $model = ClassifiedListing::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMegaphone;
    protected static string|\UnitEnum|null $navigationGroup = 'Content';
    protected static ?string $navigationLabel = 'Classified listings';
    protected static ?string $modelLabel = 'classified listing';
    protected static ?string $pluralModelLabel = 'classified listings';
    protected static ?int $navigationSort = 34;
    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return ClassifiedListingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ClassifiedListingsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListClassifiedListings::route('/'),
            'create' => CreateClassifiedListing::route('/create'),
            'edit' => EditClassifiedListing::route('/{record}/edit'),
        ];
    }
}
