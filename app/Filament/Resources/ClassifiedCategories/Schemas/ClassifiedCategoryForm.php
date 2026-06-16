<?php

namespace App\Filament\Resources\ClassifiedCategories\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ClassifiedCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Classified category')->columns(2)->schema([
                TextInput::make('name')->required()->maxLength(255),
                TextInput::make('slug')->required()->alphaDash()->unique(ignoreRecord: true)->maxLength(255),
                Textarea::make('description')->columnSpanFull(),
                TextInput::make('icon')->maxLength(255),
                TextInput::make('image_path')->label('Image path')->maxLength(255),
                TextInput::make('sort_order')->numeric()->default(0)->required(),
                Toggle::make('is_active')->default(true),
            ]),
        ]);
    }
}
