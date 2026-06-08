<?php

namespace App\Filament\Resources\ServiceScenarios;

use App\Filament\Resources\ServiceScenarios\Pages\CreateServiceScenario;
use App\Filament\Resources\ServiceScenarios\Pages\EditServiceScenario;
use App\Filament\Resources\ServiceScenarios\Pages\ListServiceScenarios;
use App\Filament\Resources\ServiceScenarios\Schemas\ServiceScenarioForm;
use App\Filament\Resources\ServiceScenarios\Tables\ServiceScenariosTable;
use App\Filament\Resources\ServiceScenarios\RelationManagers\FieldsRelationManager;
use App\Models\ServiceScenario;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ServiceScenarioResource extends Resource
{
    protected static ?string $model = ServiceScenario::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;
    protected static string|\UnitEnum|null $navigationGroup = 'Services';
    protected static ?string $navigationLabel = 'Service Scenarios';
    protected static ?int $navigationSort = 30;
    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return ServiceScenarioForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ServiceScenariosTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            FieldsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListServiceScenarios::route('/'),
            'create' => CreateServiceScenario::route('/create'),
            'edit' => EditServiceScenario::route('/{record}/edit'),
        ];
    }
}
