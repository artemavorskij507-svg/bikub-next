<?php

namespace App\Filament\Resources\ClassifiedCategories;

use App\Filament\Resources\ClassifiedCategories\Pages\CreateClassifiedCategory;
use App\Filament\Resources\ClassifiedCategories\Pages\EditClassifiedCategory;
use App\Filament\Resources\ClassifiedCategories\Pages\ListClassifiedCategories;
use App\Filament\Resources\ClassifiedCategories\Schemas\ClassifiedCategoryForm;
use App\Filament\Resources\ClassifiedCategories\Tables\ClassifiedCategoriesTable;
use App\Models\ClassifiedCategory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ClassifiedCategoryResource extends Resource
{
    protected static ?string $model = ClassifiedCategory::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;
    protected static string|\UnitEnum|null $navigationGroup = 'Content';
    protected static ?string $navigationLabel = 'Classified categories';
    protected static ?string $modelLabel = 'classified category';
    protected static ?string $pluralModelLabel = 'classified categories';
    protected static ?int $navigationSort = 35;
    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return ClassifiedCategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ClassifiedCategoriesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListClassifiedCategories::route('/'),
            'create' => CreateClassifiedCategory::route('/create'),
            'edit' => EditClassifiedCategory::route('/{record}/edit'),
        ];
    }
}
