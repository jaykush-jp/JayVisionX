<?php

use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Scheduled tasks
|--------------------------------------------------------------------------
| Requires a single server cron entry that runs every minute:
|   * * * * * cd /var/www/JayVisionX && php artisan schedule:run >> /dev/null 2>&1
*/

// Publish scheduled social posts the moment their time arrives.
Schedule::command('posts:publish-due')->everyMinute()->withoutOverlapping();

// Send AI follow-up emails to open leads that are due.
Schedule::command('leads:ai-followup')->everyTenMinutes()->withoutOverlapping();
