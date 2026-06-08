<?php

namespace App\Filament\Resources\ServiceScenarios\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FieldsRelationManager extends RelationManager
{
    protected static string $relationship = 'fields';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('field_key')->required()->regex('/^[a-z0-9_]+$/')->maxLength(255),
            TextInput::make('label')->required()->maxLength(255),
            Select::make('type')->required()->options([
                'text' => 'Text', 'textarea' => 'Textarea', 'number' => 'Number', 'select' => 'Select',
                'checkbox' => 'Checkbox', 'date' => 'Date', 'time' => 'Time', 'file' => 'File',
            ]),
            Toggle::make('required'),
            Toggle::make('is_active')->default(true),
            TextInput::make('sort_order')->numeric()->default(0)->required(),
            Textarea::make('options')->json()->rows(5),
            Textarea::make('validation_rules')->json()->rows(5),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('field_key')->searchable(),
            TextColumn::make('label')->searchable(),
            TextColumn::make('type')->badge(),
            IconColumn::make('required')->boolean(),
            IconColumn::make('is_active')->boolean(),
            TextColumn::make('sort_order')->sortable(),
        ])->headerActions([CreateAction::make()])->recordActions([EditAction::make(), DeleteAction::make()]);
    }
}
