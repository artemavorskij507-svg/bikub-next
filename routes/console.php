<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('security:expire-reviewer-access')->daily()->withoutOverlapping();
Schedule::command('security:reviewer-access-expiring-soon --days=7')->daily()->withoutOverlapping();
Schedule::command('security:governance-escalate-overdue --notify')->daily()->withoutOverlapping();

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
