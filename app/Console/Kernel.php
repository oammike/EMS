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

        $schedule->command('data:extract Lebua')->dailyAt('00:15')->withoutOverlapping()->appendOutputTo($logpath);
        $schedule->command('data:extract An Other Associates Ltd.')->dailyAt('00:20')->withoutOverlapping()->appendOutputTo($logpath);
        $schedule->command('data:extract Ava Women')->dailyAt('00:25')->withoutOverlapping()->appendOutputTo($logpath);
        $schedule->command('data:extract Boostability')->dailyAt('00:30')->withoutOverlapping()->appendOutputTo($logpath);
        $schedule->command('data:extract DilMil')->dailyAt('00:35')->withoutOverlapping()->appendOutputTo($logpath);
        $schedule->command('data:extract DMOPC')->dailyAt('00:40')->withoutOverlapping()->appendOutputTo($logpath);
        $schedule->command('data:extract ED Training')->dailyAt('00:45')->withoutOverlapping()->appendOutputTo($logpath);
        $schedule->command('data:extract SKUVantage')->dailyAt('00:50')->withoutOverlapping()->appendOutputTo($logpath);
        $schedule->command('data:extract TurnTo')->dailyAt('00:55')->withoutOverlapping()->appendOutputTo($logpath);
        $schedule->command('data:extract SheerID')->dailyAt('01:00')->withoutOverlapping()->appendOutputTo($logpath);
        $schedule->command('data:extract Glassdoor')->dailyAt('01:05')->withoutOverlapping()->appendOutputTo($logpath);
        $schedule->command('data:extract IMO')->dailyAt('01:10')->withoutOverlapping()->appendOutputTo($logpath);
        $schedule->command('data:extract TaskRabbit')->dailyAt('01:20')->withoutOverlapping()->appendOutputTo($logpath);
        $schedule->command('data:extract Bird')->dailyAt('01:25')->withoutOverlapping()->appendOutputTo($logpath);
        $schedule->command('data:extract Mous')->dailyAt('01:30')->withoutOverlapping()->appendOutputTo($logpath);
        $schedule->command('data:extract Quora')->dailyAt('01:35')->withoutOverlapping()->appendOutputTo($logpath);
        $schedule->command('data:extract WorldVentures')->dailyAt('01:40')->withoutOverlapping()->appendOutputTo($logpath);


        
    }
}
