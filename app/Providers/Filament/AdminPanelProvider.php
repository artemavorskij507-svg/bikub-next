<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Dashboard;
use Filament\Enums\ThemeMode;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Http\Middleware\SetBikubeLocale;
use Prunacatalin\FilamentLocaleSwitcher\Http\Middleware\ApplyLocale;
use Prunacatalin\FilamentLocaleSwitcher\LocaleSwitchPlugin;
use Benriadh1\FilamentTranslationManager\BenriadhFilamentTranslationManagerPlugin;
class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->databaseNotifications()
            ->brandName('BiKuBe Admin OS')
            ->brandLogo(fn (): HtmlString => new HtmlString(view('filament.brand-logo')->render()))
            ->brandLogoHeight('2.75rem')
            ->darkMode(isForced: true)
            ->defaultThemeMode(ThemeMode::Dark)
            ->renderHook(
                PanelsRenderHook::STYLES_AFTER,
                fn (): HtmlString => new HtmlString(view('filament.admin-theme')->render().view('filament.theme-palette-assets')->render()),
            )
            ->renderHook(PanelsRenderHook::TOPBAR_END, fn (): HtmlString => new HtmlString(view('filament.theme-palette-picker')->render()))
            ->renderHook(
                PanelsRenderHook::TOPBAR_LOGO_AFTER,
                fn (): HtmlString => new HtmlString(view('filament.admin-top-nav')->render()),
            )
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn (): HtmlString => new HtmlString(view('filament.admin-ui-translator')->render()),
            )
            ->spa()
            ->globalSearch(false)
 	    ->plugin(LocaleSwitchPlugin::make())
	    ->plugin(BenriadhFilamentTranslationManagerPlugin::make())
            ->maxContentWidth(Width::Full)
            ->colors([
                'primary' => Color::Emerald,
                'success' => Color::Green,
                'warning' => Color::Amber,
                'danger' => Color::Red,
                'info' => Color::Sky,
                'gray' => Color::Slate,
            ])
           ->navigationGroups([
    __('bikube.admin.operations'),
    __('bikube.admin.dispatch'),
    __('bikube.admin.orders'),
    __('bikube.admin.people'),
    __('bikube.admin.services'),
    __('bikube.admin.finance'),
    __('bikube.admin.support'),
    __('bikube.admin.content'),
    __('bikube.admin.system'),
])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                ApplyLocale::class,
                SetBikubeLocale::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
