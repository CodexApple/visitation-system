<?php

namespace App\Providers;

use BezhanSalleh\PanelSwitch\PanelSwitch;
use Blade;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::unguard();

        PanelSwitch::configureUsing(function (PanelSwitch $panelSwitch) {
                $panelSwitch
                    ->simple()
                    ->visible(fn (): bool => auth()->user()->isAdmin())
                    ->canSwitchPanels(fn (): bool => auth()->user()->isAdmin());
        });
    }
}
