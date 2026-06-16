<?php

namespace App\Filament\Pages;

use App\Models\DeliveryPageConfig;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Actions\Action;
use Filament\Schemas\Schema;

class DeliveryPageEditor extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-truck';

    protected static ?string $navigationLabel = 'Delivery Page';

    protected static string|\UnitEnum|null $navigationGroup = 'Content';

    protected static ?int $navigationSort = 5;

    protected static ?string $title = 'Delivery Page Editor';

    protected string $view = 'filament.pages.delivery-page-editor';

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public function mount(): void
    {
        $stored = DeliveryPageConfig::get('page', []);
        $this->form->fill($stored ?: []);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Tabs::make('Delivery page sections')
                    ->columnSpanFull()
                    ->tabs([
                        Tabs\Tab::make('Hero & Stats')
                            ->schema([
                                Section::make('Statistikk (4 metrics)')
                                    ->description('Tall vist under segmentkortene.')
                                    ->collapsible()
                                    ->schema([
                                        Repeater::make('stats')
                                            ->label('Statistikk')
                                            ->addActionLabel('+ Legg til stat')
                                            ->maxItems(4)
                                            ->defaultItems(0)
                                            ->schema([
                                                TextInput::make('value')->label('Verdi')->required()->placeholder('10 000+'),
                                                TextInput::make('label')->label('Beskrivelse')->required()->placeholder('items available'),
                                                Select::make('icon')->label('Ikon')->options([
                                                    'bag' => 'Handlepose',
                                                    'store' => 'Butikk',
                                                    'clock' => 'Klokke',
                                                    'star' => 'Stjerne',
                                                ])->required(),
                                            ])
                                            ->columns(3),
                                    ]),

                                Section::make('Fordeler (benefit strip)')
                                    ->description('4 fordelsikoner under butikkrekken.')
                                    ->collapsible()
                                    ->schema([
                                        Repeater::make('benefits')
                                            ->label('Fordeler')
                                            ->addActionLabel('+ Legg til fordel')
                                            ->maxItems(4)
                                            ->defaultItems(0)
                                            ->schema([
                                                TextInput::make('title')->label('Tittel')->required()->placeholder('Secure payment'),
                                                TextInput::make('subtitle')->label('Undertittel')->placeholder('Protected checkout'),
                                                Select::make('icon')->label('Ikon')->options([
                                                    'lock' => 'Lås',
                                                    'gift' => 'Gave',
                                                    'phone' => 'Telefon',
                                                    'spark' => 'Gnist',
                                                ])->required(),
                                            ])
                                            ->columns(3),
                                    ]),
                            ]),

                        Tabs\Tab::make('Produkter (Dagligvarer)')
                            ->schema([
                                Section::make('Hero slides — Dagligvarer')
                                    ->collapsible()
                                    ->schema([
                                        Repeater::make('segments.products.slides')
                                            ->label('Slides')
                                            ->addActionLabel('+ Legg til slide')
                                            ->maxItems(8)
                                            ->defaultItems(0)
                                            ->schema([
                                                TextInput::make('eyebrow')->label('Eyebrow')->placeholder('BiKuBe Levering'),
                                                TextInput::make('title')->label('Tittel')->required()->placeholder('Ferske varer, pakket med omsorg'),
                                                TextInput::make('image')->label('Bilde-URL')->url()->placeholder('/images/bikube/delivery/segments/groceries/1.png'),
                                                Toggle::make('active')->label('Aktiv')->default(true),
                                            ])
                                            ->columns(2),
                                    ]),

                                Section::make('Produktkatalog — Dagligvarer')
                                    ->collapsible()
                                    ->schema([
                                        Repeater::make('segments.products.products')
                                            ->label('Produkter')
                                            ->addActionLabel('+ Legg til produkt')
                                            ->maxItems(12)
                                            ->defaultItems(0)
                                            ->schema([
                                                TextInput::make('title')->label('Navn')->required()->placeholder('Bananer'),
                                                TextInput::make('subtitle')->label('Mengde/type')->placeholder('1 kg'),
                                                TextInput::make('price')->label('Pris')->required()->placeholder('129 NOK'),
                                                TextInput::make('old_price')->label('Gammel pris')->placeholder('152 NOK'),
                                                TextInput::make('badge')->label('Badge')->placeholder('-15%'),
                                                TextInput::make('image')->label('Bilde-URL')->url()->columnSpanFull()->placeholder('/images/bikube/delivery/products-real/bananas.png'),
                                            ])
                                            ->columns(3),
                                    ]),

                                Section::make('Promo-bannere — Dagligvarer')
                                    ->collapsible()
                                    ->schema([
                                        Repeater::make('segments.products.promos')
                                            ->label('Promoer')
                                            ->addActionLabel('+ Legg til promo')
                                            ->maxItems(3)
                                            ->defaultItems(0)
                                            ->schema([
                                                TextInput::make('title')->label('Tittel')->required(),
                                                TextInput::make('subtitle')->label('Undertittel'),
                                                TextInput::make('image')->label('Bilde-URL')->url()->columnSpanFull(),
                                            ])
                                            ->columns(2),
                                    ]),

                                Section::make('Butikker — Dagligvarer')
                                    ->collapsible()
                                    ->schema([
                                        Repeater::make('segments.products.stores')
                                            ->label('Butikker')
                                            ->addActionLabel('+ Legg til butikk')
                                            ->maxItems(12)
                                            ->defaultItems(0)
                                            ->schema([
                                                TextInput::make('name')->label('Navn')->required()->placeholder('REMA 1000'),
                                                TextInput::make('logo')->label('Logo-URL')->url()->placeholder('/images/bikube/delivery/stores/rema1000.svg'),
                                                TextInput::make('rating')->label('Vurdering')->placeholder('4.8'),
                                                TextInput::make('eta')->label('ETA')->placeholder('35 min'),
                                            ])
                                            ->columns(2),
                                    ]),
                            ]),

                        Tabs\Tab::make('Ferdigmat')
                            ->schema([
                                Section::make('Hero slides — Ferdigmat')
                                    ->collapsible()
                                    ->schema([
                                        Repeater::make('segments.meals.slides')
                                            ->label('Slides')
                                            ->addActionLabel('+ Legg til slide')
                                            ->maxItems(8)
                                            ->defaultItems(0)
                                            ->schema([
                                                TextInput::make('eyebrow')->label('Eyebrow'),
                                                TextInput::make('title')->label('Tittel')->required(),
                                                TextInput::make('image')->label('Bilde-URL')->url()->columnSpanFull(),
                                                Toggle::make('active')->label('Aktiv')->default(true),
                                            ])
                                            ->columns(2),
                                    ]),

                                Section::make('Produktkatalog — Ferdigmat')
                                    ->collapsible()
                                    ->schema([
                                        Repeater::make('segments.meals.products')
                                            ->label('Produkter')
                                            ->addActionLabel('+ Legg til rett')
                                            ->maxItems(12)
                                            ->defaultItems(0)
                                            ->schema([
                                                TextInput::make('title')->label('Navn')->required(),
                                                TextInput::make('subtitle')->label('Type'),
                                                TextInput::make('price')->label('Pris')->required(),
                                                TextInput::make('badge')->label('Badge'),
                                                TextInput::make('image')->label('Bilde-URL')->url()->columnSpanFull(),
                                            ])
                                            ->columns(2),
                                    ]),

                                Section::make('Promo-bannere — Ferdigmat')
                                    ->collapsible()
                                    ->schema([
                                        Repeater::make('segments.meals.promos')
                                            ->label('Promoer')
                                            ->addActionLabel('+ Legg til promo')
                                            ->maxItems(3)
                                            ->defaultItems(0)
                                            ->schema([
                                                TextInput::make('title')->label('Tittel')->required(),
                                                TextInput::make('subtitle')->label('Undertittel'),
                                                TextInput::make('image')->label('Bilde-URL')->url()->columnSpanFull(),
                                            ])
                                            ->columns(2),
                                    ]),

                                Section::make('Restauranter — Ferdigmat')
                                    ->collapsible()
                                    ->schema([
                                        Repeater::make('segments.meals.stores')
                                            ->label('Restauranter')
                                            ->addActionLabel('+ Legg til restaurant')
                                            ->maxItems(12)
                                            ->defaultItems(0)
                                            ->schema([
                                                TextInput::make('name')->label('Navn')->required(),
                                                TextInput::make('logo')->label('Logo-URL')->url()->columnSpanFull(),
                                                TextInput::make('rating')->label('Vurdering'),
                                                TextInput::make('eta')->label('ETA'),
                                            ])
                                            ->columns(2),
                                    ]),
                            ]),

                        Tabs\Tab::make('Stor leveranse')
                            ->schema([
                                Section::make('Hero slides — Bulky')
                                    ->collapsible()
                                    ->schema([
                                        Repeater::make('segments.bulky.slides')
                                            ->label('Slides')
                                            ->addActionLabel('+ Legg til slide')
                                            ->maxItems(8)
                                            ->defaultItems(0)
                                            ->schema([
                                                TextInput::make('eyebrow')->label('Eyebrow'),
                                                TextInput::make('title')->label('Tittel')->required(),
                                                TextInput::make('image')->label('Bilde-URL')->url()->columnSpanFull(),
                                                Toggle::make('active')->label('Aktiv')->default(true),
                                            ])
                                            ->columns(2),
                                    ]),

                                Section::make('Tjenestekatalog — Bulky')
                                    ->collapsible()
                                    ->schema([
                                        Repeater::make('segments.bulky.products')
                                            ->label('Tjenester')
                                            ->addActionLabel('+ Legg til tjeneste')
                                            ->maxItems(12)
                                            ->defaultItems(0)
                                            ->schema([
                                                TextInput::make('title')->label('Navn')->required(),
                                                TextInput::make('subtitle')->label('Type'),
                                                TextInput::make('price')->label('Pris')->required(),
                                                TextInput::make('badge')->label('Badge'),
                                                TextInput::make('image')->label('Bilde-URL')->url()->columnSpanFull(),
                                            ])
                                            ->columns(2),
                                    ]),

                                Section::make('Promo-bannere — Bulky')
                                    ->collapsible()
                                    ->schema([
                                        Repeater::make('segments.bulky.promos')
                                            ->label('Promoer')
                                            ->addActionLabel('+ Legg til promo')
                                            ->maxItems(3)
                                            ->defaultItems(0)
                                            ->schema([
                                                TextInput::make('title')->label('Tittel')->required(),
                                                TextInput::make('subtitle')->label('Undertittel'),
                                                TextInput::make('image')->label('Bilde-URL')->url()->columnSpanFull(),
                                            ])
                                            ->columns(2),
                                    ]),

                                Section::make('Partnere — Bulky')
                                    ->collapsible()
                                    ->schema([
                                        Repeater::make('segments.bulky.stores')
                                            ->label('Partnere')
                                            ->addActionLabel('+ Legg til partner')
                                            ->maxItems(12)
                                            ->defaultItems(0)
                                            ->schema([
                                                TextInput::make('name')->label('Navn')->required(),
                                                TextInput::make('logo')->label('Logo-URL')->url()->columnSpanFull(),
                                                TextInput::make('rating')->label('Vurdering'),
                                                TextInput::make('eta')->label('Slot/ETA'),
                                            ])
                                            ->columns(2),
                                    ]),
                            ]),
                    ]),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        DeliveryPageConfig::set('page', $data);

        Notification::make()
            ->title('Lagret')
            ->body('Delivery-siden er oppdatert.')
            ->success()
            ->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Lagre')
                ->action('save')
                ->color('success'),
        ];
    }
}
