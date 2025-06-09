<?php

namespace App\Models\Cms;

use SolutionForest\InspireCms\Base\Enums\UserActivity;
use SolutionForest\InspireCms\Helpers\AuthHelper;
use SolutionForest\InspireCms\Models\Contracts\User as UserContract;
use SolutionForest\InspireCms\Models\User as InspireUser;

class User extends InspireUser implements UserContract
{
    const DEMO_EMAIL = 'demo@solutionforest.net';
    const DEMO_PASSWORD = '12345678';

    protected static function booting()
    {
        parent::booting();
        static::creating(function (self $user) {
            $user->email_confirmed_at = $user->freshTimestamp();
        });
        static::created(function (self $user) {
            // Assign demo role to the user if it exists
            $user->syncRoles(['Demo']);
        });
    }

    public function isSuperAdmin()
    {
        // return true; // Uncomment this line to configure role's permission on cms panel
        return false;
    }
    

    public function sendEmailVerificationNotification()
    {
        // Skip email verification for demo
    }

    public function handleActivity($activity)
    {
        switch ($activity) {
            case UserActivity::Login:

                $this->updateQuietly([
                    'last_logged_in_at' => now(),

                    // Reset failed login attempt count
                    'failed_login_attempt' => 0,
                    'last_lockouted_at' => null,
                ]);

                $this->userActivities()->updateOrCreate([
                    'ip_address' => request()->ip(),
                ], [
                    'last_logged_in_at_utc' => now()->utc(),
                ]);

                break;

            case UserActivity::Logout:

                $this->userActivities()->updateOrCreate([
                    'ip_address' => request()->ip(),
                ], [
                    'last_logged_out_at_utc' => now()->utc(),
                ]);

                break;

            case UserActivity::FailedLogin:

                $failedLoginAttempt = intval($this->failed_login_attempt ?? 0);
                $failedLoginAttempt++;

                // Skip the lockout logic for demo
                // if ($this->hasExceededMaxLoginAttempts($failedLoginAttempt)) {
                //     $this->last_lockouted_at = now();
                // }

                $this->failed_login_attempt = $failedLoginAttempt;

                $this->saveQuietly();

                break;

            case UserActivity::PasswordReset:

                $this->updateQuietly([
                    'last_password_change_date' => now(),
                ]);

                break;

            case UserActivity::LockoutReset:

                $this->updateQuietly([
                    'failed_login_attempt' => 0,
                    'last_lockouted_at' => null,
                ]);

                break;

            default:
                break;
        }
    }
}
