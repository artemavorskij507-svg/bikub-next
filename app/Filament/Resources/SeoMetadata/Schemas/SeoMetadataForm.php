<?php

namespace App\Filament\Resources\SeoMetadata\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SeoMetadataForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Target')
                    ->columns(2)
                    ->schema([
                        TextInput::make('path')->maxLength(255),
                        TextInput::make('locale')->default('nb')->required()->maxLength(12),
                        TextInput::make('owner_type')->disabled()->dehydrated(false),
                        TextInput::make('owner_id')->disabled()->dehydrated(false),
                    ]),
                Section::make('Search metadata')
                    ->schema([
                        TextInput::make('seo_title')->maxLength(255),
                        Textarea::make('seo_description')->rows(3),
                        TextInput::make('canonical_url')->url()->maxLength(255),
                        TextInput::make('robots')->placeholder('index,follow')->maxLength(255),
                    ]),
                Section::make('Open Graph')
                    ->schema([
                        TextInput::make('og_title')->maxLength(255),
                        Textarea::make('og_description')->rows(3),
                        TextInput::make('og_image')->url()->maxLength(255),
                    ])
                    ->collapsible(),
            ]);
    }
}
