<?php

namespace App\Filament\Resources\PublicSitePages\RelationManagers;

use App\Models\PublicSiteSection;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SectionsRelationManager extends RelationManager
{
    protected static string $relationship = 'sections';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Section identity')->schema([
                Select::make('section_type')
                    ->label('Section type')
                    ->options(PublicSiteSection::SECTION_TYPES)
                    ->required(),

                TextInput::make('sort_order')
                    ->numeric()
                    ->default(0)
                    ->required(),

                Toggle::make('is_active')
                    ->default(true),
            ]),

            Section::make('Title / Subtitle (nb)')->schema([
                TextInput::make('title.nb')->label('Title (NO)')->maxLength(255),
                TextInput::make('subtitle.nb')->label('Subtitle (NO)')->maxLength(512),
            ]),

            Section::make('English')->schema([
                TextInput::make('title.en')->label('Title (EN)')->maxLength(255),
                TextInput::make('subtitle.en')->label('Subtitle (EN)')->maxLength(512),
            ])->collapsible(),

            Section::make('Ukrainian')->schema([
                TextInput::make('title.uk')->label('Title (UA)')->maxLength(255),
                TextInput::make('subtitle.uk')->label('Subtitle (UA)')->maxLength(512),
            ])->collapsible(),

            Section::make('Russian')->schema([
                TextInput::make('title.ru')->label('Title (RU)')->maxLength(255),
                TextInput::make('subtitle.ru')->label('Subtitle (RU)')->maxLength(512),
            ])->collapsible(),

            Section::make('Config')->schema([
                KeyValue::make('config')->label('Section config (key/value)')->nullable(),
            ])->collapsible(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('section_type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => PublicSiteSection::SECTION_TYPES[$state] ?? $state),
                TextColumn::make('sort_order')->label('Order')->sortable(),
                IconColumn::make('is_active')->boolean()->label('Active'),
                TextColumn::make('items_count')->label('Items')->counts('items'),
                TextColumn::make('updated_at')->since()->sortable(),
            ])
            ->defaultSort('sort_order')
            ->headerActions([CreateAction::make()])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }
}
