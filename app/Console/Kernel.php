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

        $schedule->command('data:extract Lebua')->dailyAt('13:15')->withoutOverlapping()->appendOutputTo($logpath);
        $schedule->command('data:extract An Other Associates Ltd.')->dailyAt('13:20')->withoutOverlapping()->appendOutputTo($logpath);
        $schedule->command('data:extract Ava Women')->dailyAt('13:25')->withoutOverlapping()->appendOutputTo($logpath);
        $schedule->command('data:extract Boostability')->dailyAt('13:30')->withoutOverlapping()->appendOutputTo($logpath);
        $schedule->command('data:extract DilMil')->dailyAt('13:35')->withoutOverlapping()->appendOutputTo($logpath);
        $schedule->command('data:extract DMOPC')->dailyAt('13:40')->withoutOverlapping()->appendOutputTo($logpath);
        $schedule->command('data:extract ED Training')->dailyAt('13:45')->withoutOverlapping()->appendOutputTo($logpath);
        $schedule->command('data:extract SKUVantage')->dailyAt('13:50')->withoutOverlapping()->appendOutputTo($logpath);
        $schedule->command('data:extract TurnTo')->dailyAt('13:55')->withoutOverlapping()->appendOutputTo($logpath);
        $schedule->command('data:extract SheerID')->dailyAt('13:00')->withoutOverlapping()->appendOutputTo($logpath);
        $schedule->command('data:extract Glassdoor')->dailyAt('13:05')->withoutOverlapping()->appendOutputTo($logpath);
        $schedule->command('data:extract IMO')->dailyAt('13:10')->withoutOverlapping()->appendOutputTo($logpath);
        $schedule->command('data:extract TaskRabbit')->dailyAt('13:20')->withoutOverlapping()->appendOutputTo($logpath);
        $schedule->command('data:extract Bird')->dailyAt('13:25')->withoutOverlapping()->appendOutputTo($logpath);
        $schedule->command('data:extract Mous')->dailyAt('13:30')->withoutOverlapping()->appendOutputTo($logpath);
        $schedule->command('data:extract Quora')->dailyAt('13:35')->withoutOverlapping()->appendOutputTo($logpath);
        $schedule->command('data:extract WorldVentures')->dailyAt('13:40')->withoutOverlapping()->appendOutputTo($logpath);


        
    }
}
