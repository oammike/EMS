<?php

namespace OAMPI_Eval\Console\Commands;

use Illuminate\Console\Command;
use Storage;
use Carbon\Carbon;
use OAMPI_Eval\User;
use OAMPI_Eval\AgentStats;

class Tester extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tester';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'writes to console whatever';
    
    protected $context;
    
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
      $out = strtotime('8:31:54') - strtotime('TODAY');
      $this->info("seconds: ".$out);
        
    }
}
