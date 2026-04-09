<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ============================================
// MICROLAB — Invoice Email Notification Schedule
// Laravel 11 style (no Kernel.php)
// Tukar masa dengan ubah nilai dalam dailyAt('HH:MM')
// ============================================

// 1. Payment Reminder (3 hari sebelum due) + Overdue Notice + Monthly Report (1hb)
Schedule::command('invoice:reminders')
    ->dailyAt('08:00')
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/invoice-reminders.log'));

// 2. New Invoice Notification (summary hujung hari)
Schedule::command('invoice:notify-new')
    ->dailyAt('18:00')
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/invoice-new.log'));

