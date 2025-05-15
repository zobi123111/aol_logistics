<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('invoices:process-pending')->everyMinute();
        $schedule->command('supplier:process-invoices')->everyMinute();
        $schedule->command('quickbooks:refresh-token')->everyThirtyMinutes(); 
        $schedule->command('email:send-pending')->everyMinute();
        $schedule->command('services:update-cost')->daily();
        $schedule->command('client-services:update-cost')->daily();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
