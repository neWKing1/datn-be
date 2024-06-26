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
        // $schedule->command('inspire')->hourly();
         $schedule->command('app:promotion-command')->withoutOverlapping()->everyMinute()->timezone('Asia/Ho_Chi_Minh');
         $schedule->command('app:voucher-command')->withoutOverlapping()->everyMinute()->timezone('Asia/Ho_Chi_Minh');


//         $schedule->command('app:order-command')->withoutOverlapping()->everyFiveSeconds()->timezone('Asia/Ho_Chi_Minh');
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
