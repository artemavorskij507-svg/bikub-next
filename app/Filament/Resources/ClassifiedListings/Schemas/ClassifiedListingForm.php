<?php

namespace App\Filament\Resources\ClassifiedListings\Schemas;

use App\Models\ClassifiedCategory;
use App\Models\ClassifiedListing;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ClassifiedListingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Listing')->columns(2)->schema([
                TextInput::make('listing_number')->disabled()->dehydrated(false),
                TextInput::make('title')->required()->maxLength(120),
                TextInput::make('slug')->required()->alphaDash()->unique(ignoreRecord: true)->maxLength(255),
                Select::make('classified_category_id')
                    ->label('Category')
                    ->options(fn () => ClassifiedCategory::orderBy('name')->pluck('name', 'id'))
                    ->searchable(),
                Textarea::make('description')->required()->columnSpanFull(),
                TextInput::make('price_amount')->numeric()->label('Price'),
                TextInput::make('currency')->default('NOK')->maxLength(3),
                TextInput::make('condition')->maxLength(80),
                TextInput::make('location')->required()->maxLength(120),
                TextInput::make('image_path')->maxLength(255),
                Toggle::make('is_featured'),
            ]),
            Section::make('Moderation')->columns(2)->schema([
                Select::make('status')->required()->options([
                    ClassifiedListing::STATUS_DRAFT => 'Draft',
                    ClassifiedListing::STATUS_PENDING => 'Pending',
                    ClassifiedListing::STATUS_APPROVED => 'Approved',
                    ClassifiedListing::STATUS_REJECTED => 'Rejected',
                    ClassifiedListing::STATUS_ARCHIVED => 'Archived',
                ]),
                DateTimePicker::make('published_at'),
                DateTimePicker::make('expires_at'),
                Textarea::make('moderation_note')->columnSpanFull(),
            ]),
            Section::make('Private contact')->columns(3)->schema([
                TextInput::make('contact_name')->maxLength(120),
                TextInput::make('contact_email')->email()->maxLength(190),
                TextInput::make('contact_phone')->maxLength(40),
            ]),
        ]);
    }
}
