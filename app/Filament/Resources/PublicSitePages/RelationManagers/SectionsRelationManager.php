<?php

namespace App\Filament\Resources\PublicSitePages\RelationManagers;

use App\Models\PublicSiteSection;
use App\Models\PublicSiteSectionItem;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
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
        $safetyLabels = array_combine(
            PublicSiteSectionItem::ALLOWED_SAFETY_LABELS,
            PublicSiteSectionItem::ALLOWED_SAFETY_LABELS
        );

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

            Section::make('Title · Subtitle')->schema([
                TextInput::make('title.nb')->label('Title (NO)')->maxLength(255),
                TextInput::make('subtitle.nb')->label('Subtitle (NO)')->maxLength(512),
                TextInput::make('title.en')->label('Title (EN)')->maxLength(255),
                TextInput::make('subtitle.en')->label('Subtitle (EN)')->maxLength(512),
                TextInput::make('title.uk')->label('Title (UA)')->maxLength(255),
                TextInput::make('title.ru')->label('Title (RU)')->maxLength(255),
            ])->columns(2)->collapsible(),

            Section::make('Config')->schema([
                KeyValue::make('config')
                    ->label('Section config (e.g. segment: products)')
                    ->helperText('Use "segment" key with value products/meals/bulky to link to delivery segments.')
                    ->nullable(),
            ])->collapsible(),

            Section::make('Items')->schema([
                Repeater::make('items')
                    ->relationship('items')
                    ->orderColumn('sort_order')
                    ->collapsible()
                    ->itemLabel(fn (array $state): string => ($state['title']['nb'] ?? $state['item_type'] ?? 'Item'))
                    ->schema([
                        Select::make('item_type')
                            ->label('Type')
                            ->options(PublicSiteSectionItem::ITEM_TYPES)
                            ->required()
                            ->columnSpanFull(),

                        Toggle::make('is_active')->default(true)->inline(false),
                        TextInput::make('sort_order')->numeric()->default(0),

                        TextInput::make('title.nb')->label('Title (NO)')->maxLength(255),
                        TextInput::make('title.en')->label('Title (EN)')->maxLength(255),
                        TextInput::make('title.uk')->label('Title (UA)')->maxLength(255),
                        TextInput::make('title.ru')->label('Title (RU)')->maxLength(255),

                        TextInput::make('subtitle.nb')->label('Subtitle (NO)')->maxLength(512),
                        TextInput::make('subtitle.en')->label('Subtitle (EN)')->maxLength(512),

                        Textarea::make('body.nb')->label('Body (NO)')->rows(2)->maxLength(2000),
                        Textarea::make('body.en')->label('Body (EN)')->rows(2)->maxLength(2000),

                        TextInput::make('cta_label.nb')->label('CTA label (NO)')->maxLength(120),
                        TextInput::make('cta_label.en')->label('CTA label (EN)')->maxLength(120),

                        TextInput::make('cta_route')
                            ->label('CTA route or /path')
                            ->helperText('Named route or /internal path only. No external URLs.')
                            ->maxLength(255),

                        TextInput::make('image_path')
                            ->label('Image path (from public/)')
                            ->placeholder('images/bikube/delivery/slide-groceries.png')
                            ->maxLength(512),

                        TextInput::make('mobile_image_path')->label('Mobile image path')->nullable(),

                        TextInput::make('badge')
                            ->label('Badge')
                            ->helperText('No fake ratings (4.9) or fake KPIs (10 000+).')
                            ->maxLength(60),

                        Select::make('safety_label')
                            ->label('Safety label')
                            ->options($safetyLabels)
                            ->nullable(),

                        TextInput::make('linked_scenario_slug')
                            ->label('Linked scenario slug')
                            ->placeholder('delivery-groceries')
                            ->nullable(),

                        TextInput::make('icon')->label('Icon')->nullable(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]),
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
