<?php

namespace App\Filament\Resources\ServiceScenarios\Schemas;

use App\Models\ServiceScenario;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ServiceScenarioForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Scenario identity')->columns(2)->schema([
                    Select::make('category_id')->relationship('category', 'title')->searchable()->preload(),
                    TextInput::make('scenario_key')->required()->regex('/^[a-z0-9]+(?:[.-][a-z0-9]+)*$/')->unique(ignoreRecord: true),
                    TextInput::make('slug')->required()->alphaDash(),
                    TextInput::make('service_type')->required(),
                    TextInput::make('title')->required(),
                    Select::make('status')->options(array_combine(ServiceScenario::STATUSES, array_map('ucfirst', ServiceScenario::STATUSES)))->required()->default('draft'),
                    TextInput::make('subtitle')->columnSpanFull(),
                    Textarea::make('description')->columnSpanFull()->rows(4),
                ]),
                Section::make('Execution contract')->columns(3)->schema([
                    Toggle::make('requires_pickup_address'),
                    Toggle::make('requires_dropoff_address'),
                    Toggle::make('requires_worker')->default(true),
                    Toggle::make('requires_partner'),
                    Toggle::make('requires_payment')->default(true),
                    Toggle::make('supports_scheduling'),
                    Toggle::make('supports_live_tracking'),
                ]),
                Section::make('Pricing and configuration')->columns(2)->schema([
                    TextInput::make('base_price')->numeric()->minValue(0)->prefix('NOK'),
                    TextInput::make('currency')->default('NOK')->required()->maxLength(3),
                    TextInput::make('sort_order')->numeric()->default(0)->required(),
                    Textarea::make('metadata')->json()->rows(8),
                    Textarea::make('form_schema')->json()->rows(8),
                ])->collapsible(),
            ]);
    }
}
