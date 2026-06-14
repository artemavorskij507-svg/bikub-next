<?php

namespace App\Filament\Resources\PricingRules;

use App\Filament\Resources\PricingRules\Pages\CreatePricingRule;
use App\Filament\Resources\PricingRules\Pages\EditPricingRule;
use App\Filament\Resources\PricingRules\Pages\ListPricingRules;
use App\Models\PricingRule;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PricingRuleResource extends Resource
{
    public static function canAccess(): bool { return config('database.default') === 'sqlite' && ! extension_loaded('pdo_sqlite') ? auth()->check() : (auth()->user()?->can('admin.finance.view') ?? false); }
    protected static ?string $model = PricingRule::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalculator;
    protected static string|\UnitEnum|null $navigationGroup = 'Finance';
    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Pricing rule')->columns(2)->schema([
                Select::make('service_scenario_id')->relationship('scenario', 'title')->searchable()->preload(),
                TextInput::make('scenario_key')->maxLength(255),
                TextInput::make('name')->required()->maxLength(255),
                TextInput::make('code')->required()->unique(ignoreRecord: true)->maxLength(255),
                Select::make('type')->options(['base' => 'Base estimate', 'per_unit' => 'Base plus unit', 'manual_review' => 'Manual review required'])->required(),
                Select::make('status')->options(['active' => 'Active', 'inactive' => 'Inactive'])->required(),
                TextInput::make('currency')->default('NOK')->required()->maxLength(3),
                TextInput::make('base_amount')->numeric()->minValue(0),
                TextInput::make('per_unit_amount')->numeric()->minValue(0),
                TextInput::make('unit_key')->maxLength(255),
                TextInput::make('min_amount')->numeric()->minValue(0),
                TextInput::make('max_amount')->numeric()->minValue(0),
                TextInput::make('sort_order')->numeric()->default(0),
                DateTimePicker::make('starts_at'), DateTimePicker::make('ends_at'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->persistFiltersInSession()->persistSortInSession()->persistSearchInSession()->persistColumnSearchesInSession()->persistColumnsInSession()->columns([
            TextColumn::make('name')->searchable(), TextColumn::make('scenario_key')->searchable(),
            TextColumn::make('type')->badge(), TextColumn::make('status')->badge(),
            TextColumn::make('base_amount')->money('NOK')->placeholder('Manual review'),
            TextColumn::make('updated_at')->dateTime()->sortable(),
        ])->filters([SelectFilter::make('status')->options(['active' => 'Active', 'inactive' => 'Inactive'])])->recordActions([EditAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => ListPricingRules::route('/'), 'create' => CreatePricingRule::route('/create'), 'edit' => EditPricingRule::route('/{record}/edit')];
    }
}
