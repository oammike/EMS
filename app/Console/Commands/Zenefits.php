<?php

namespace OAMPI_Eval\Console\Commands;

use Illuminate\Console\Command;
use Storage;
use Carbon\Carbon;
use OAMPI_Eval\User;
use OAMPI_Eval\AgentStats;
use OAMPI_Eval\AgentScheds;

class Zenefits extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:extract {campaign}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Extracts agent data from specified {campaign}';
    
    protected $context;
    
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $auth = base64_encode("qa:Variable8");
        $this->context = stream_context_create(
          [
            'http' =>
            [
              'header' => "Content-Type: application/x-www-form-urlencoded\r\n"."Authorization: Basic ".$auth."\r\n",
              "method" => 'GET'
            ]
          ]
        );
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
      $time_start = microtime(true);
      $campaign = $this->argument('campaign');
      
      if($campaign===null || $campaign===""){
        $this->error('invalid args');
        exit;
      }
      
      $campaigns = array('Zenefits','Postmates','Cebu Pacific','Patch', 'Circles.Life');
      if (!in_array($campaign, $campaigns)) {
        $this->error('invalid args');
        exit;
      }
      
      if($campaign==="Cebu Pacific"){
        $domain = "208.74.77.172";
      }else{
        $domain = "208.74.77.167";
      }
      
      $date_start = Carbon::now();
      $date_end = Carbon::now();
      $date_start->timezone = 'Asia/Singapore';
      $date_end->timezone = 'Asia/Singapore';
      $date_start->subDay();
      $date_start->hour = 12;
      $date_start->minute = 0;
      $date_end->hour = 11;
      $date_end->minute = 59;
      $date_start = $date_start->toDateTimeString();
      $date_end = $date_end->toDateTimeString();
      
      $agents = User::whereHas(
        'campaign', function ($query) {
            $query->where('campaign.name', '=', $this->argument('campaign'));
        }
      )
      ->with('campaign')
      //->where('users.name','ztorres')
      //->take(10)  
      ->get();
      
      $added = 0;
      foreach($agents as $agent){
        if($added >= 5){
          //break;
        }
        
        $sched_url = "http://".$domain."/vicidial/user_stats.php?DB=&did_id=&did=&begin_date=".urlencode( $date_start )."&end_date=".urlencode( $date_end )."&user=".$agent->name."&submit=submit&file_download=2";
        try{
          $result = file_get_contents($sched_url, false, $this->context);
          if ($result === false) {
            $this->error( "could not read user sched" );
          } else {
            file_put_contents("/var/www/html/evaluation/logs/temp.csv", $result);
            $handle = fopen('/var/www/html/evaluation/logs/temp.csv', "r");
            
            $header = true;
            $line = 1;
            $latest_logged_sched = AgentScheds::where('user_id',$agent->id)->max('timestamp');
            while ($csvLine = fgetcsv($handle, 1000, ",")) {
              if($line === 1){
                //skip line that describes column headers
                $line = $line + 1;
                continue;
              }
              if ($header) {
                $header = false;
              } else {
                if($csvLine[1]==="TOTAL"){
                  $header = false;
                  continue;
                } else {
                  $timestamp = new Carbon(str_replace('"','',$csvLine[2]));
                  $this->info($timestamp->timestamp);
                  if( $timestamp > $latest_logged_sched ){
                    $event = $csvLine[1];
                    $duration = 0;
                    if($event==="LOGOUT"){
                      if(isset($csvLine[5])){
                      
                        $duration = 0;
                        $elements = explode(':', $csvLine[5]);
                        if(count($elements)>2){
                            $duration = $elements[2] + ($elements[1]*60) + ($elements[0]*3600);
                        } else {
                            $duration = $elements[1] + ($elements[0]*60);
                        }
                      }
                    }
                    
                    $sched = new AgentScheds;
                    $sched->user_id = $agent->id;
                    $sched->campaign_id = $agent->campaign()->first()->id;
                    $sched->event = $event;
                    $sched->duration = $duration;
                    $sched->timestamp = $timestamp;
                    $sched->save();
                    
                    $this->info($agent->name . " : " . $event." - ".$duration);
                  }
                  $line = $line + 1;
                }
              }
            }
          }
        }catch (Exception $e) {
          $this->error(  "sched url unreachable" );
        }
        

        $activity_url = "http://".$domain."/vicidial/user_stats.php?DB=&did_id=&did=&begin_date=".urlencode( $date_start )."&end_date=".urlencode( $date_end )."&user=".$agent->name."&submit=submit&file_download=7";
        $this->info( $activity_url );
        
        try{
          $result = file_get_contents($activity_url, false, $this->context);
          if ($result === false) {
            $this->error( "could not read user stats" );
          } else {
            file_put_contents("/var/www/html/evaluation/logs/temp.csv", $result);
            $handle = fopen('/var/www/html/evaluation/logs/temp.csv', "r");
            
            $header = true;
            $line = 1;
            $has_stat = false;
            $latest_logged_stat = AgentStats::where('user_id',$agent->id)->max('timestamp');
            while ($csvLine = fgetcsv($handle, 1000, ",")) {
              if($line === 1){
                //skip line that describes column headers
                $line = $line + 1;
                continue;
              }
              if ($header) {
                $header = false;
              } else {
              //trip
                if(!isset($csvLine[2]) || $csvLine[2]==="TOTALS"){
                  $header = false;
                  if($has_stat){
                    $added = $added + 1;
                    $has_stat = false;
                  }
                  break;
                } else {
                  $has_stat = true;
                  $timestamp = new Carbon($csvLine[2]);
                  $this->info($timestamp->timestamp);
                  
                  if( $timestamp > $latest_logged_stat ){
                    
                    $stat = new AgentStats;
                    $stat->user_id = $agent->id;
                    $stat->campaign_id = $agent->campaign()->first()->id;
                    $stat->pause_duration = $csvLine[3];
                    $stat->wait_duration = $csvLine[4];
                    $stat->talk_duration = $csvLine[5];
                    $stat->dispo_duration = $csvLine[6];
                    $stat->dead_duration = $csvLine[7];
                    $stat->customer_duration = $csvLine[8];
                    $stat->status = "";
                    $stat->pause_code = $csvLine[12];
                    $stat->timestamp = $timestamp;
                    $stat->save();
                    
                    $this->info($agent->name . " : " . $csvLine[2]." - ".$csvLine[3]." - ".$csvLine[12] );
                  }
                  $line = $line + 1;
                  
                }
              }
            }
          }
        }catch (Exception $e) {
          $this->error(  "activity url unreachable" );
        }
      }
        
        
      $time_end = microtime(true);
      $execution_time = ($time_end - $time_start)/60;      
      $this->info( 'Total Execution Time: '.$execution_time.' minutes');    
    }
}
