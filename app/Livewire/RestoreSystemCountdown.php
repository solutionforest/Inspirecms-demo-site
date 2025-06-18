<?php

namespace App\Livewire;

use App\Console\Commands\RestoreSystem;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Str;
use Livewire\Component;

class RestoreSystemCountdown extends Component
{
    public $remainingSeconds = null;

    public function mount()
    {
        // only allow authenticated users
        if (! auth()->check()) {
            abort(403);
        }
        $this->updateCountdown();
    }

    public function updateCountdown()
    {
        // Check if the countdown feature is enabled
        if (env('APP_RESTORE_COUNTDOWN_ENABLED', true) === false) {
            $this->remainingSeconds = null;
            return;
        }

        // Find the event by command name
        $event = collect(Schedule::events())
            ->first(fn($e) => Str::contains($e->command, RestoreSystem::COMMAND_NAME));

        if (! $event) {
            $this->remainingSeconds = null;
            return;
        }

        // Laravel 8+ offers nextRunDate()
        $nextRun = $event->nextRunDate();  
        $this->remainingSeconds = now()->diffInSeconds($nextRun);
    }

    public function getDisplayMessage()
    {
        if ($this->remainingSeconds === null) {
            return 'The system will reset in every ' . RestoreSystem::SCHEDULE_IN_MINS . ' minutes.';
        }

        $minutes = floor($this->remainingSeconds / 60);
        $seconds = $this->remainingSeconds % 60;

        if ($minutes < 0 || $seconds < 0) {
            return "The system restoration is not scheduled.";
        }

        if ($minutes <= 0) {
            return "The system will restore in {$seconds} seconds.";
        }

        return "The system will restore in {$minutes} minutes and {$seconds} seconds.";
    }

    public function render()
    {
        return view('livewire.restore-system-countdown');
    }
}