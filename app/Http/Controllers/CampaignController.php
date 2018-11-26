<?php

namespace OAMPI_Eval\Http\Controllers;


use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use \Response;
use \DB;
use Carbon\Carbon;


use OAMPI_Eval\Http\Requests;
use OAMPI_Eval\User;
use OAMPI_Eval\Role;
use OAMPI_Eval\UserType_Roles;
use OAMPI_Eval\Campaign;
use OAMPI_Eval\CampaignLogos;
use OAMPI_Eval\Status;
use OAMPI_Eval\UserType;
use OAMPI_Eval\Position;
use OAMPI_Eval\ImmediateHead;
use OAMPI_Eval\EvalType;
use OAMPI_Eval\RatingScale;
use OAMPI_Eval\EvalSetting;
use OAMPI_Eval\EvalForm;
use OAMPI_Eval\EvalDetail;
use OAMPI_Eval\Competency;
use OAMPI_Eval\Competency__Attribute;
use OAMPI_Eval\Attribute;
use OAMPI_Eval\Summary;
use OAMPI_Eval\PerformanceSummary;
use OAMPI_Eval\Movement;
use OAMPI_Eval\Team;
use OAMPI_Eval\ImmediateHead_Campaign;
    use OAMPI_Eval\AgentStats;
    use OAMPI_Eval\AgentScheds;
    

class CampaignController extends Controller
{
    protected $user;
    protected $campaign;

    public function __construct(Campaign $campaign)
    {
        $this->middleware('auth');
        $this->user =  User::find(Auth::user()->id);
        $this->campaign = $campaign;
    }




     public function index()
     {
        DB::connection()->disableQueryLog();
        
        if (Input::get('sort')=='Z'){

            $allCamps = DB::table('campaign')->where([
                        ['name','!=',""],
                        ['name','!='," "]
                    ])
                    ->leftJoin('campaign_logos','campaign_logos.campaign_id','=','campaign.id')
                    ->orderBy('name','DESC')->select('campaign.name','campaign.id','campaign_logos.filename')->get();

            $sort=2;
        }
        else {
            $sort=1;
            $allCamps = DB::table('campaign')->where([
                        ['name','!=',""],
                        ['name','!='," "]
                    ])
                    ->leftJoin('campaign_logos','campaign_logos.campaign_id','=','campaign.id')
                    ->orderBy('name','ASC')->select('campaign.name','campaign.id','campaign_logos.filename')->get();
        }


        $correct = Carbon::now('GMT+8');

        if($this->user->id !== 564 ) {
            $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
            fwrite($file, "-------------------\n Viewed all campaigns - ". $correct->format('M d h:i A'). " by [". $this->user->id."] ".$this->user->lastname."\n");
            fclose($file);
        }

       

        return view('people.campaigns-index',compact('allCamps','sort'));
    }


/*
        $roles = UserType::find($this->user->userType_id)->roles->pluck('label'); 
        $canDelete =  ($roles->contains('DELETE_EMPLOYEE')) ? '1':'0';


        $allcampaigns = Campaign::orderBy('name','ASC')->get();

        $allcamp = $allcampaigns->filter(function($c){
            return $c->name !== "" && $c->name !=' ';

        });

        $campaigns = new Collection;
        $coll = new Collection;

        foreach ($allcamp as $camp) {
            $leaders = new Collection;

           
            $leads = $camp->leaders;
            $l = $leads->filter(function($e){ return $e->firstname != '';});
            $lead = new Collection;




            foreach ($l as $ldr) {
                $checkActive = User::where('employeeNumber',$ldr->employeeNumber)->first();
                $lead->push($ldr);
            }



            foreach ($lead->sortBy('lastname') as $l) {

                $team = ImmediateHead_Campaign::where('immediateHead_id',$l->id)->where('campaign_id',$camp->id)->first();
                $ih = ImmediateHead::find($team->immediateHead_id);
                $members = DB::table('team')->where('ImmediateHead_Campaigns_id','=',$team->id)->leftJoin('users','team.user_id','=','users.id')->where('users.employeeNumber', '!=',$ih->employeeNumber)
                                ->where('users.status_id','!=','7')->where('users.status_id','!=','8')->where('users.status_id','!=',9)
                                ->select('users.id','users.lastname','users.firstname','users.middlename','users.position_id')->orderBy('lastname')->get();

             
               $leaderInfo = User::where('employeeNumber',$l->employeeNumber)->first();

               if (!empty($leaderInfo) && $team->disabled == null )
               {
                    //RESIGNED || TERMINATED || ENDO
                   if($leaderInfo->status_id != 7 && $leaderInfo->status_id != 8 && $leaderInfo->status_id != 9)
                   {
                        $leaders->push([
                         'tl'=> $l->lastname.", ". $l->firstname,
                         'id'=> $leaderInfo['id'], 
                         'employeeNumber'=> $l->employeeNumber, 
                         'position'=>$leaderInfo['position']['name'],
                         'members'=>$members,
                         'ImmediateHead_Campaigns_id' => $team->id,
                         
                      ]);

                   }

               }
      
               
            }
            $logo = null;

            $logo = $camp->logo;


             
            $campaigns->push(['id'=>$camp->id, 'name'=>$camp->name, 'leaders'=>$leaders, 'logo'=>$logo ]); //$leaders, "members"=> $teams
        }
        

       
       
        return view('people.campaigns', compact('campaigns','canDelete'));
        //return $campaigns;
        */
    


   

    

    public function getAllCampaigns()
    {

        $exclude = Input::get('except');

        if ($exclude){
            $campaigns = Campaign::where('id', '!=', $exclude)->orderBy('name','ASC')->get();


        }else {
            $campaigns = Campaign::orderBy('name','ASC')->get();
        }

        $campaign = $campaigns->filter(function($c){
            return $c->name != '' && $c->name != ' '; 
        });

        return response()->json($campaign);


    }

    public function getAllLeaders($id)
    {

        //$leaders = $this->campaign->find($id)->leaders->sortBy('lastname');
        $tls = $this->campaign->find($id)->leaders->sortBy('lastname');
        //return response()->json($tls->sortByDesc('id'));
        //return $tls;
        $tl = $tls->filter(function ($t){ return ($t->lastname != '' );});

        $coll = new Collection;

        foreach ($tl as $key) {

            $tlid = ImmediateHead_Campaign::where('campaign_id',$id)->where('immediateHead_id',$key->id)->first();
            $emp = User::where('employeeNumber',$key->employeeNumber)->first();
             if ($emp->status_id != 7  && $emp->status_id != 8  && $emp->status_id != 9 )
            $coll->push(['id'=>$tlid->id, 'lastname'=>$key->lastname, 'firstname'=>$key->firstname]);
        }

        //return $tls;
        return $coll;
       // return response()->json( array_values($tl) );
        //return Response::json( array_values($tl) );

    }

    public function create()
    {
        //if ($this->user->userType_id == 1 || $this->user->userType_id == 2)
        $canCreate = UserType::find($this->user->userType_id)->roles->where('label','ADD_NEW_PROGRAM');
        
        if ($canCreate->isEmpty())

        {
            return view("access-denied");
            
        } else  return view('people.campaign-new'); 
        

    }

    public function store(Request $request)
    {
        $camp = new Campaign;
        $camp->name = $request->name;
        $camp->save();
        return response()->json($camp);

    }
    public function show($id)
    {
        DB::connection()->disableQueryLog();
        if (is_null(Campaign::find($id))) return view('empty');

        $roles = UserType::find($this->user->userType_id)->roles->pluck('label'); //->where('label','MOVE_EMPLOYEES');
        $canEdit =  ($roles->contains('EDIT_EMPLOYEE')) ? '1':'0';


        $campaign1 = DB::table('campaign')->where('campaign.id',$id)-> //Campaign::find($id);
                        leftJoin('campaign_logos','campaign_logos.campaign_id','=','campaign.id')->
                        leftJoin('immediateHead_Campaigns','campaign.id','=','immediateHead_Campaigns.campaign_id')->
                      
                        leftJoin('immediateHead','immediateHead_Campaigns.immediateHead_id','=','immediateHead.id')->
                        leftJoin('users','users.employeeNumber','=','immediateHead.employeeNumber')->
                        where([
                            ['status_id','!=',7],
                            ['status_id','!=',8],
                            ['status_id','!=',9],
                        ])->
                        leftJoin('positions','users.position_id','=','positions.id')->
                        leftJoin('team','team.immediateHead_Campaigns_id','=','immediateHead_Campaigns.id')->
                        select('campaign.id as campaign_id','campaign.name','campaign_logos.filename', 'immediateHead.firstname as TLfname','immediateHead.lastname as TLlname','positions.name as jobTitle', 'immediateHead_Campaigns.id as ihCamp_id','users.id as user_TL_id','users.nickname', 'team.user_id as memberID','immediateHead_Campaigns.disabled as disabled','campaign.has_vicidial')->
                        orderBy('users.lastname','ASC')->get();

        $uData = DB::table('team')->where('team.campaign_id',$id)->
                        leftJoin('users','users.id','=','team.user_id')->
                         where([
                            ['status_id','!=',7],
                            ['status_id','!=',8],
                            ['status_id','!=',9],
                        ])->
                        leftJoin('positions','users.position_id','=','positions.id')->
                        select('users.firstname','users.nickname','users.lastname','users.id','positions.name as jobTitle')->get();
                        //join('team')->get();

                        // leftJoin('team','team.campaign_id','=','campaign.id')->
                        // leftJoin('immediateHead_Campaigns','team.immediateHead_Campaigns_id','=','immediateHead_Campaigns.id')->
                        

                        //select('immediateHead.firstname','immediateHead.lastname','campaign.name as program')->get();
                        /*
                        join('users','team.user_id','=','users.id')->where([
                            ['status_id','!=',7],
                            ['status_id','!=',8],
                            ['status_id','!=',9],
                        ])->
                        join('positions','users.position_id','=','positions.id')->
                        select('campaign.name','campaign_logos.filename','users.firstname','users.lastname', 
                               'users.nickname', 'positions.name as jobTitle','users.id as user_id')->
                        orderBy('users.lastname','ASC')->get();*/

        $campaigns = collect($campaign1);
        $campaign = $campaigns->sortBy('TLlname');

        $u = collect($uData);
        $userData = $u->groupBy('id');
        //return $campaign->groupBy('ihCamp_id');

         $correct = Carbon::now('GMT+8');

        if($this->user->id !== 564 ) {
            $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
            fwrite($file, "-------------------\n Viewed Campaign ID: ".Campaign::find($id)->name. " | ". $correct->format('M d h:i A'). " by [". $this->user->id."] ".$this->user->lastname."\n");
            fclose($file);
        }

       
        return view('people.campaigns-show',compact('campaign','userData','canEdit'));

    }

    public function destroy($id)
    {
         $canDoThis = UserType::find($this->user->userType_id)->roles->where('label','DELETE_PROGRAM');

         if ($canDoThis->isEmpty())
        {
             return view("access-denied");

        } else {
            $this->campaign->destroy($id);
            return back();

        }
        

    }
    
    public function update($id)
    {

    }
    
    public function getIndividualStat(Request $request){
      $return_data = new \stdClass();
      $return_data->stats = [];
      $pause_codes = [];
      if($request->input('user_id','')===''){
        $return_data->error = "invalid parameters";
      } else {
        $default = Carbon::createFromTimestamp(1);
      
        $user_id = $request->input('user_id','');
        $start = $request->input('start',$default->timestamp);
        $end = $request->input('end',$default->timestamp);
        $stats = DB::table('agent_stats')
            ->select('campaign_id', DB::raw('UNIX_TIMESTAMP(timestamp) as utimestamp'), DB::raw('(pause_duration + wait_duration + talk_duration + dispo_duration + dead_duration + customer_duration) as value'), 'pause_code')
            ->where('user_id','=',$user_id)
            ->whereRaw('timestamp >= FROM_UNIXTIME('.$start.')')
            ->whereRaw('timestamp <= FROM_UNIXTIME('.$end.')')
            ->orderBy('timestamp', 'asc')
            ->get();
            
        $stats_data = [];
        $current = Carbon::createFromTimestamp(0);
        $label = $current->format('M d');
        foreach ($stats as $stat) {
          $date = Carbon::now();
          $date->setTimestamp($stat->utimestamp);
          
          $difference = $current->diffInHours($date, false);
          if($difference > 24){
            $current = $date;
            $label = $current->format('M d');
          }
          
          $pause_code = trim($stat->pause_code)==="" ? "internal" : $stat->pause_code;
          if(!in_array($pause_code,$pause_codes)){
            $pause_codes[] = $pause_code;
          }
          $stats_data[$label][$pause_code] = isset($stats_data[$label][$pause_code]) ? $stats_data[$label][$pause_code] + $stat->value : $stat->value;    
          
        }
        
        $workhours = [];
        $agentcount = [];
        $labels = [];
        $datasets = [];
        foreach($stats_data as $key=>$value){
          $labels[] = $key;
          foreach($pause_codes as $code){
            if(isset($value[$code])) {
              $datasets[$code][] = $value[$code];
            } else {
              $datasets[$code][] = 0;
            }
          }
          
        }
        $stats = new \stdClass();
        $stats->labels = $labels;
        $stats->datasets = $datasets;
        $return_data->stats = $stats; 
      }
      return response()->json($return_data);
    }
    
    public function getStats(Request $request){
      $return_data = new \stdClass();
      $return_data->stats = [];
      $pause_codes = [];
      if($request->input('campaignId','')!==''){
        $return_data;
      } else {
        $default = Carbon::createFromTimestamp(1);
      
        $campaign_id = $request->input('campaign_id','');
        $start = $request->input('start',$default->timestamp);
        $end = $request->input('end',$default->timestamp);
        $stats = DB::table('agent_stats')
            ->select('campaign_id', DB::raw('UNIX_TIMESTAMP(timestamp) as utimestamp'), DB::raw('(pause_duration + wait_duration + talk_duration + dispo_duration + dead_duration + customer_duration) as value'), 'pause_code')
            ->where('campaign_id','=',$campaign_id)
            ->whereRaw('timestamp >= FROM_UNIXTIME('.$start.')')
            ->whereRaw('timestamp <= FROM_UNIXTIME('.$end.')')
            ->orderBy('timestamp', 'asc')
            ->get();
            
        $stats_data = [];
        $current = Carbon::createFromTimestamp(0);
        $label = $current->format('M d');
        foreach ($stats as $stat) {
          $date = Carbon::now();
          $date->setTimestamp($stat->utimestamp);
          
          $difference = $current->diffInHours($date, false);
          if($difference > 24){
            $current = $date;
            $label = $current->format('M d');
          }
          
          $pause_code = trim($stat->pause_code)==="" ? "internal" : $stat->pause_code;
          if(!in_array($pause_code,$pause_codes)){
            $pause_codes[] = $pause_code;
          }
          $stats_data[$label][$pause_code] = isset($stats_data[$label][$pause_code]) ? $stats_data[$label][$pause_code] + $stat->value : $stat->value;    
          
        }
        
        $workhours = [];
        $agentcount = [];
        $labels = [];
        $datasets = [];
        foreach($stats_data as $key=>$value){
          $labels[] = $key;
          foreach($pause_codes as $code){
            if(isset($value[$code])) {
              $datasets[$code][] = $value[$code];
            } else {
              $datasets[$code][] = 0;
            }
          }
          
        }
        $stats = new \stdClass();
        $stats->labels = $labels;
        $stats->datasets = $datasets;
        $return_data->stats = $stats; 
      }
      return response()->json($return_data);
    }
    
    public function getScheds(Request $request){
      $return_data = new \stdClass();
      $return_data->scheds = [];
      if($request->input('campaignId','')!==''){
        $return_data;
      } else {
        $default = Carbon::createFromTimestamp(1);
      
        $campaign_id = $request->input('campaign_id','');
        
        //test test
        $start = $request->input('start',$default->timestamp);
        $end = $request->input('end',$default->timestamp);
        
        $scheds = DB::table('agent_sched')
          ->select(DB::raw('UNIX_TIMESTAMP(timestamp) as utimestamp'),'duration','user_id')
          ->where('campaign_id','=',$campaign_id)
          ->whereRaw('timestamp >= FROM_UNIXTIME('.$start.')')
          ->whereRaw('timestamp <= FROM_UNIXTIME('.$end.')')
          ->orderBy('timestamp', 'asc')
          ->get();
          
        $scheds_data = [];
        $current = Carbon::createFromTimestamp(0);
        $label = $current->format('M d');
        $user_ids = [];
        foreach ($scheds as $sched) {
          $date = Carbon::now();
          $date->setTimestamp($sched->utimestamp);
          
          $difference = $current->diffInHours($date, false);
          if($difference > 24){
            $current = $date;
            $label = $current->format('M d');
            $user_ids = [];
          }else{
            $scheds_data[$label]['agent_count'] = sizeof($user_ids);
          }
          
          $scheds_data[$label]['hours'] = isset($scheds_data[$label]['hours']) ? $scheds_data[$label]['hours'] + $sched->duration : $sched->duration;
          if(!in_array($sched->user_id,$user_ids)){
            $user_ids[] = $sched->user_id;
          }
        }
        
        $workhours = [];
        $agentcount = [];
        $labels = [];
        foreach($scheds_data as $key=>$value){
          $labels[] = $key;
          $workhours[] = $value['hours'];
          $agentcount[] = isset($value['agent_count']) ? $value['agent_count'] : 0;
        }
        $scheds = new \stdClass();
        $scheds->labels = $labels;
        $scheds->workhours = $workhours;
        $scheds->agentcount = $agentcount;
        $return_data->scheds = $scheds;
        
      
      }
      return response()->json($return_data);
    }
    
    public function showStats($id){

        $campaign =  Campaign::find($id);
        return view('campaigns.showStats', compact('campaign'));
      
    }
    
    public function showAgentStats($id){
        $campaign =  Campaign::find($id);
        return view('campaigns.showAgentStats', compact('campaign'));
      
    }
    
    
    public function getAgentStats(Request $request){
      $return_data = new \stdClass();
      $return_data->stats = [];
      $pause_codes = [];
      $domain = "";
      if($request->input('campaignId','')!==''){
        $return_data;
      } else {
        $campaign_id = $request->input('campaign_id','');
        $campaign =  Campaign::find($campaign_id);
        if($campaign->name==="Cebu Pacific"){
          $domain = "208.74.77.172";
        }else{
          $domain = "208.74.77.167";
        }
        
        $group_codes = [
          27 => "ZEN",
          32 => "CIRCLES",
          44 => "POST",
          34 => "PATCH_C&group[]=PATCH_E&group[]=PATCH_S"
        ];
        
        $user_codes = [
          27 => "ZEN",
          32 => "CIRCLES",
          44 => "POST",
          34 => "PATCH"
        ];
        
        $auth = base64_encode("qa:Variable8");
        $context = stream_context_create(
          [
            'http' =>
            [
              'header' => "Content-Type: application/x-www-form-urlencoded\r\n"."Authorization: Basic ".$auth."\r\n",
              "method" => 'GET'
            ]
          ]
        );

        $default = Carbon::createFromTimestamp(1);
        $date_start = Carbon::createFromTimestamp($request->input('start',$default->timestamp));
        $date_end = Carbon::createFromTimestamp($request->input('end',$default->timestamp));
        $date_start->timezone = 'Asia/Singapore';
        $date_end->timezone = 'Asia/Singapore';
        $date_start->hour = 0;
        $date_start->minute = 0;
        $date_end->hour = 23;
        $date_end->minute = 59;
        $date_start = $date_start->toDateTimeString();
        $date_end = $date_end->toDateTimeString();
        
        $stats_url = "http://".$domain."/vicidial/AST_agent_performance_detail.php?query_date=".urlencode( $date_start )."&end_date=".urlencode( $date_end )."&group[]=".$group_codes[$campaign->id]."&user_group[]=".$user_codes[$campaign->id]."&shift=ALL&DB=&stage=&file_download=2";
        
        try{
          $result = file_get_contents($stats_url, false, $context);
          if ($result === false) {
            $return_data->error( "could not read user sched" );
          } else {
          
            file_put_contents("/var/www/html/evaluation/logs/agentstats".Auth::user()->id.".csv", $result);
            $handle = fopen('/var/www/html/evaluation/logs/agentstats'.Auth::user()->id.'.csv', "r");
            $header = true;
            $line = 1;
            $column_labels = [];
            $data = [];
            while ($csvLine = fgetcsv($handle, 1000, ",")) {
                
              if($line < 5){
                $line = $line + 1;
                continue;
              }
              
              if($line === 5){
                for($i=2;$i<count($csvLine);$i++){
                  $column_labels[] = $csvLine[$i];
                }
                $column_labels[] = "DED";
                $line = $line + 1;
                continue;
              }
              
              if($csvLine[0]==="TOTALS"){
                continue;
              } else {
                $username =  $csvLine[1];
                $user =  User::where('name', $csvLine[1])->first();
                
                if($user!==null){
                  $username = $user->firstname . " ". $user->lastname;
                  $data["DED"][$username] = 0;
                  for($i=2;$i<count($csvLine);$i++){
                    $data[$column_labels[$i-2]][$username] = $csvLine[$i];
                    
                    /** add all deductibles **/
                    if($column_labels[$id-2]==="TeamM" || $column_labels[$id-2]==="Coachi" || $column_labels[$id-2]==="Idle"){
                      $splitted = explode(":",$csvLine[$i]);
                      if(count($splitted)===1){
                        $data["DED"][$username] = $data["DED"][$username] + intval($splitted);
                      }
                      if(count($splitted)===2){
                        $duration = intval($splitted[1]) + (intval($splitted[0])*60);
                        $data["DED"][$username] = $data["DED"][$username] + $duration;
                      }
                      if(count($splitted)===3){
                        $duration = intval($splitted[2]) + (intval($splitted[1])*60) + (intval($splitted[0])*3600);
                        $data["DED"][$username] = $data["DED"][$username] + $duration;
                      }
                    }
                  }
                  /** CONVERT deductibles back to HH:MM:SS format **/
                  if($data["DED"][$username]!==0){
                    $hours = floor($data["DED"][$username] / 3600);
                    $data["DED"][$username] %= 3600;
                    $minutes = floor($data["DED"][$username] / 60);
                    $seconds = $startTotalSeconds - ($minutes * 60);
                    $data["DED"][$username] = sprintf("%02d",$hours) . ":" . sprintf("%02d",$minutes) . ":" .sprintf("%.1f", $seconds);
                  }
                  
                }
                $line = $line + 1;
              }
            
              
            }
            
            $return_data->columns = $column_labels;
            $return_data->data = $data;
            
            
            
          }
        }catch(Exception $e){
          $return_data->error  = "sched url unreachable" ;
        }

      }
      return response()->json($return_data);
    }

}
