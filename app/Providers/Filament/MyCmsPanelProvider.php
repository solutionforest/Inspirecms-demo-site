<?php

namespace App\Providers\Filament;

use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;
use SolutionForest\InspireCms\CmsPanelProvider;

class MyCmsPanelProvider extends CmsPanelProvider
{
    protected function configureCmsPanel(\Filament\Panel $panel)
    {
        return parent::configureCmsPanel($panel)
            // Fill the login form with demo credentials
            ->login(\App\Filament\Resources\Pages\Auth\Login::class)
            // Disallow edit demo user, e.g. password
            ->profile(null)
            ->emailVerification(false)
            ->passwordReset(false)
            ->bootUsing(function () {
                FilamentView::registerRenderHook(
                    name: PanelsRenderHook::PAGE_START,
                    hook: fn () => Blade::render('@livewire(\'restore-system-countdown\')')
                );
            })
            ->renderHook(PanelsRenderHook::HEAD_START, function () {
                return view('components.gtag')->render();
            });
    }
}
