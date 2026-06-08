<?php

namespace App\Filament\Resources\CmsPages\Schemas;

use App\Models\CmsPage;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CmsPageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Page identity')
                    ->columns(2)
                    ->schema([
                        Select::make('type')->options(array_combine(CmsPage::TYPES, array_map('ucfirst', CmsPage::TYPES)))->required(),
                        TextInput::make('locale')->default('nb')->required()->maxLength(12),
                        TextInput::make('slug')->required()->maxLength(255),
                        Select::make('status')->options(array_combine(CmsPage::STATUSES, array_map('ucfirst', CmsPage::STATUSES)))->default('draft')->required(),
                    ]),
                Section::make('Content')
                    ->schema([
                        TextInput::make('title')->required()->maxLength(255),
                        TextInput::make('subtitle')->maxLength(255),
                        Textarea::make('body')->rows(16)->columnSpanFull(),
                    ]),
                Section::make('Publication')
                    ->schema([
                        DateTimePicker::make('published_at')->seconds(false),
                    ])
                    ->collapsible(),
            ]);
    }
}
