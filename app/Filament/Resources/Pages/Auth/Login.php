<?php

namespace App\Filament\Resources\Pages\Auth;

use App\Models\Cms\User as CmsUser;
use SolutionForest\InspireCms\Filament\Pages\Auth\Login as InspireCmsLogin;

class Login extends InspireCmsLogin
{
    public function mount(): void
    {
        parent::mount();

        $this->form->fill([
            'email' => CmsUser::DEMO_EMAIL,
            'password'=> CmsUser::DEMO_PASSWORD,
        ]);
    }
}
