<?php

namespace App\Filament\Resources\ServicePages\Schemas;

use App\Models\ServicePage;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ServicePageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Service identity')
                    ->columns(2)
                    ->schema([
                        TextInput::make('scenario_key')->maxLength(255),
                        TextInput::make('service_slug')->required()->maxLength(255),
                        TextInput::make('locale')->default('nb')->required()->maxLength(12),
                        Select::make('status')->options(array_combine(ServicePage::STATUSES, array_map('ucfirst', ServicePage::STATUSES)))->default('draft')->required(),
                    ]),
                Section::make('Content')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('hero_image')->collection('hero')->disk('public')->image()->maxSize(5120)->helperText('Optional real hero image. No placeholder is generated.'),
                        TextInput::make('title')->required()->maxLength(255),
                        TextInput::make('subtitle')->maxLength(255),
                        Textarea::make('short_description')->rows(3),
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
