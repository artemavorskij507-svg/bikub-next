<?php

namespace App\Filament\Resources\PublicSitePages;

use App\Filament\Resources\PublicSitePages\Pages\CreatePublicSitePage;
use App\Filament\Resources\PublicSitePages\Pages\EditPublicSitePage;
use App\Filament\Resources\PublicSitePages\Pages\ListPublicSitePages;
use App\Models\PublicSitePage;
use BackedEnum;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;

class PublicSitePageResource extends Resource
{
    protected static ?string $model = PublicSitePage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedGlobeAlt;

    protected static string|\UnitEnum|null $navigationGroup = 'Content';

    protected static ?string $navigationLabel = 'Public Site Builder';

    protected static ?int $navigationSort = 5;

    protected static ?string $modelLabel = 'Site Page';

    protected static ?string $pluralModelLabel = 'Site Pages';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Page Settings')->schema([
                Grid::make(2)->schema([
                    Select::make('template_key')
                        ->label('Template')
                        ->options(PublicSitePage::TEMPLATE_KEYS)
                        ->required(),

                    TextInput::make('route_path')
                        ->label('Route path')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->placeholder('/category/delivery'),

                    TextInput::make('linked_category_slug')
                        ->label('Linked category slug')
                        ->nullable()
                        ->placeholder('delivery'),

                    Select::make('publish_status')
                        ->label('Publish status')
                        ->options([
                            'draft'     => 'Draft',
                            'published' => 'Published',
                            'archived'  => 'Archived',
                        ])
                        ->default('draft')
                        ->required(),

                    DateTimePicker::make('published_at')
                        ->label('Publish at (schedule)')
                        ->seconds(false)
                        ->nullable(),
                ]),
            ]),

            Section::make('Sections & Items')
                ->description('Use the Sections tab below to add/edit sections and their items after saving the page.')
                ->schema([])
                ->collapsible(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('route_path')
                    ->label('Route')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('template_key')
                    ->label('Template')
                    ->badge()
                    ->formatStateUsing(fn ($state) => PublicSitePage::TEMPLATE_KEYS[$state] ?? $state),

                Tables\Columns\TextColumn::make('publish_status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'published' => 'success',
                        'draft'     => 'warning',
                        'archived'  => 'gray',
                        default     => 'gray',
                    }),

                Tables\Columns\TextColumn::make('sections_count')
                    ->label('Sections')
                    ->counts('sections'),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated')
                    ->since()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('publish_status')
                    ->options([
                        'draft'     => 'Draft',
                        'published' => 'Published',
                        'archived'  => 'Archived',
                    ]),

                Tables\Filters\SelectFilter::make('template_key')
                    ->options(PublicSitePage::TEMPLATE_KEYS),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('preview')
                    ->label('Preview')
                    ->icon(Heroicon::OutlinedEye)
                    ->url(fn (PublicSitePage $record) => route('admin.public-site-preview', $record))
                    ->openUrlInNewTab(),
            ])
            ->defaultSort('updated_at', 'desc');
    }

    public static function getRelationManagers(): array
    {
        return [
            RelationManagers\SectionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListPublicSitePages::route('/'),
            'create' => CreatePublicSitePage::route('/create'),
            'edit'   => EditPublicSitePage::route('/{record}/edit'),
        ];
    }
}
