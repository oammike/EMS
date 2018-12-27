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

        $schedule->command('data:extract Lebua')->dailyAt('09:50')->withoutOverlapping()->appendOutputTo($logpath);
        $schedule->command('data:extract An Other Associates Ltd.')->dailyAt('09:51')->withoutOverlapping()->appendOutputTo($logpath);
        $schedule->command('data:extract Ava Women')->dailyAt('09:52')->withoutOverlapping()->appendOutputTo($logpath);
        $schedule->command('data:extract Boostability')->dailyAt('09:53')->withoutOverlapping()->appendOutputTo($logpath);
        $schedule->command('data:extract DilMil')->dailyAt('09:54')->withoutOverlapping()->appendOutputTo($logpath);
        $schedule->command('data:extract DMOPC')->dailyAt('09:55')->withoutOverlapping()->appendOutputTo($logpath);
        $schedule->command('data:extract ED Training')->dailyAt('09:56')->withoutOverlapping()->appendOutputTo($logpath);
        $schedule->command('data:extract SKUVantage')->dailyAt('09:57')->withoutOverlapping()->appendOutputTo($logpath);
        $schedule->command('data:extract TurnTo')->dailyAt('09:58')->withoutOverlapping()->appendOutputTo($logpath);
        $schedule->command('data:extract SheerID')->dailyAt('09:59')->withoutOverlapping()->appendOutputTo($logpath);
        $schedule->command('data:extract Glassdoor')->dailyAt('09:49')->withoutOverlapping()->appendOutputTo($logpath);
        $schedule->command('data:extract IMO')->dailyAt('09:48')->withoutOverlapping()->appendOutputTo($logpath);
        $schedule->command('data:extract TaskRabbit')->dailyAt('09:47')->withoutOverlapping()->appendOutputTo($logpath);
        $schedule->command('data:extract Bird')->dailyAt('09:46')->withoutOverlapping()->appendOutputTo($logpath);
        $schedule->command('data:extract Mous')->dailyAt('10:00')->withoutOverlapping()->appendOutputTo($logpath);
        $schedule->command('data:extract Quora')->dailyAt('10:05')->withoutOverlapping()->appendOutputTo($logpath);
        $schedule->command('data:extract WorldVentures')->dailyAt('10:10')->withoutOverlapping()->appendOutputTo($logpath);


        
    }
}
