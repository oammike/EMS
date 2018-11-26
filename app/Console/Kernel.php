<?php

namespace OAMPI_Eval\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
      Commands\Zenefits::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
        
        $logpath = "/var/www/html/evaluation/logs/".microtime(true).".log";
        $schedule->command('data:extract Patch')->dailyAt('00:00')->withoutOverlapping()->appendOutputTo($logpath);
        $schedule->command('data:extract Postmates')->dailyAt('01:00')->withoutOverlapping()->appendOutputTo($logpath);
        $schedule->command('data:extract Zenefits')->dailyAt('04:00')->withoutOverlapping()->appendOutputTo($logpath);
        $schedule->command('data:extract Circles.Life')->dailyAt('04:15')->withoutOverlapping()->appendOutputTo($logpath);
        $schedule->command('data:extract Adore')->dailyAt('04:30')->withoutOverlapping()->appendOutputTo($logpath);
        
    }
}
