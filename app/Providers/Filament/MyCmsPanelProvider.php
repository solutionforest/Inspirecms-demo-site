<?php

namespace App\Providers\Filament;

use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use SolutionForest\InspireCms\CmsPanelProvider;
use SolutionForest\InspireCms\View\Components\Alert;

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
                    PanelsRenderHook::PAGE_START,
                    function () {

                        $alert = Alert::make('The database will reset every 30 minutes.')
                            ->type('danger')
                            ->withAttributes([
                                'class' => 'mt-3',
                            ]);

                        return $alert->render();
                    }
                );
            });
    }
}
