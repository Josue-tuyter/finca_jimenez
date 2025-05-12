<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Althinect\FilamentSpatieRolesPermissions\FilamentSpatieRolesPermissionsPlugin;
use App\Filament\Pages\Auth\EditProfile;
use App\Filament\Pages\Auth\PasswordReset\RequestPasswordReset;
use App\Filament\Widgets\HarvestsChart;
use App\Filament\Widgets\HarvestTrackingTypeOverview;
use App\Filament\Widgets\TextureVariationsChart;
use App\Filament\Widgets\FermentationsChart;
use App\Filament\Widgets\Fermentation_trackingTypeOverview;
use App\Filament\Widgets\Drying_trackingTypeOverview;
use App\Filament\Widgets\ColorVariationsChart;
use App\Filament\Widgets\DryingsChart;

use App\Filament\Widgets\TemperatureHumidityChart;
use App\Filament\Widgets\FermentationLocationChart;
use App\Filament\Widgets\WeightVsBucketsChart;
use App\Filament\Widgets\DatabaseLinkWidget;
use App\Filament\Widgets\DispatchesChart;

//use Filament\Pages\Auth\EditProfile as AuthEditProfile;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->PasswordReset(RequestPasswordReset::class)
            ->profile(EditProfile::class)
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
                
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                DatabaseLinkWidget::class,
                HarvestTrackingTypeOverview::class,
                HarvestsChart::class,
                TextureVariationsChart::class,
                FermentationsChart::class,
                Fermentation_trackingTypeOverview::class,
                DryingsChart::class,
                Drying_trackingTypeOverview::class,
                ColorVariationsChart::class,
                
                TemperatureHumidityChart::class,
                FermentationLocationChart::class,
                WeightVsBucketsChart::class,
                DispatchesChart::class,

 

                //Widgets\FilamentInfoWidget::class,
                
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            // ->profile(AuthEditProfile::class)
            ->sidebarCollapsibleOnDesktop()
            //->sidebarFullyCollapsibleOnDesktop()
            //->plugin(FilamentSpatieRolesPermissionsPlugin::make())





            ->brandName('Finca Jimenez')
            ->brandLogo(asset('images/finca_logo.png'))
            ->brandLogoHeight('3.5rem')
            ->favicon(asset('images/finca_favicon.ico'))
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}


