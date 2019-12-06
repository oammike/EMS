<?php

namespace OAMPI_Eval\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use \DB;
use \Hash;
use Excel;
use \PDF;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use OAMPI_Eval\Http\Requests;
use OAMPI_Eval\User;
use OAMPI_Eval\Campaign;
use OAMPI_Eval\UserType;
use OAMPI_Eval\Team;
use OAMPI_Eval\ImmediateHead;
use OAMPI_Eval\Position;
use OAMPI_Eval\Status;

use OAMPI_Eval\Movement;
use OAMPI_Eval\Movement_ImmediateHead;
use OAMPI_Eval\ImmediateHead_Campaign;
use OAMPI_Eval\Notification;
use OAMPI_Eval\User_Notification;

use OAMPI_Eval\User_Memo;
use OAMPI_Eval\Memo;
use OAMPI_Eval\Logs;
use OAMPI_Eval\Biometrics;
use OAMPI_Eval\NotifType;

use OAMPI_Eval\Task;
use OAMPI_Eval\Task_Campaign;
use OAMPI_Eval\Task_User;
use OAMPI_Eval\Taskbreak_User;
use OAMPI_Eval\TaskGroup;

class TaskController extends Controller
{
    protected $user;
    protected $userNotifs;
    use Traits\UserTraits;
  

    public function __construct()
    {
        $this->middleware('auth');
        $this->user =  User::find(Auth::user()->id);
        $this->userNotifs = $this->user->notifications();
        
    }

    public function allTasks()
    {
      
      DB::connection()->disableQueryLog(); $correct=Carbon::now('GMT+8');

      $program = Campaign::find(Input::get('program'));

      if (empty($program)) return view('empty');
      $l = DB::table('campaign_logos')->where('campaign_id',$program->id)->get();
        (count($l) > 0) ? $logo = $l : $logo=null;
      
     

       if (is_null(Input::get('showfrom')))
        {
            $todayStart = Carbon::now('GMT+8')->startOfDay(); $todayEnd = Carbon::now('GMT+8')->endOfDay();
        }
        else {
            $todayStart = Carbon::parse(Input::get('showfrom'),'Asia/Manila')->startOfDay(); 
            $todayEnd = Carbon::parse(Input::get('showfrom'),'Asia/Manila')->endOfDay();
        }


      $allTasks = DB::table('task_campaign')->where('task_campaign.campaign_id',$program->id)->
      					join('task','task.campaign_id','=','task_campaign.campaign_id')->
      					join('taskgroup','task.groupID','=','taskgroup.id')->
      					join('task_user','task_user.task_id','=','task.id')->
      					join('users','users.id','=','task_user.user_id')->
      					join('positions','positions.id','=','users.position_id')->
      					select('task_campaign.name as tracker', 'task.id as taskID','task.name as task', 'taskgroup.name as taskGroup','taskgroup.id as groupID', 'task_user.id as submissionID','task_user.user_id','users.firstname','users.lastname', 'positions.name as jobTitle', 'task_user.timeStart','task_user.timeEnd','task_user.created_at')->
      					where('task_user.created_at','>=',$todayStart->format('Y-m-d H:i:s'))->
      					where('task_user.created_at','<=',$todayEnd->format('Y-m-d H:i:s'))->get();

      $actualSubmissions = DB::table('task_campaign')->where('task_campaign.campaign_id',$program->id)->
      					join('task','task.campaign_id','=','task_campaign.campaign_id')->
      					join('taskgroup','task.groupID','=','taskgroup.id')->
      					join('task_user','task_user.task_id','=','task.id')->
      					join('users','users.id','=','task_user.user_id')->
      					join('positions','positions.id','=','users.position_id')->
      					select('task_campaign.name as tracker', 'task.id as taskID','task.name as task', 'taskgroup.name as taskGroup','taskgroup.id as groupID', 'task_user.id as submissionID','task_user.user_id','users.firstname','users.lastname', 'positions.name as jobTitle', 'task_user.timeStart','task_user.timeEnd','task_user.created_at')->
      					where('task_user.created_at','>=',$todayStart->format('Y-m-d H:i:s'))->
      					where('task_user.created_at','<=',$todayEnd->format('Y-m-d H:i:s'))->paginate(500);

     $tracker = Task_Campaign::where('campaign_id',$program->id)->first();

      $allBreaks = DB::table('task_campaign')->where('task_campaign.campaign_id',$program->id)->
      					join('task','task.campaign_id','=','task_campaign.campaign_id')->
      					//join('taskgroup','task.groupID','=','taskgroup.id')->
      					join('task_user','task_user.task_id','=','task.id')->
      					join('taskbreak_user','taskbreak_user.task_userID','=','task_user.id')->
      					join('users','users.id','=','task_user.user_id')->
      					//join('positions','positions.id','=','users.position_id')->
      					select('task.id as taskID','task.name as task', 'task_user.id as submissionID','task_user.user_id','users.firstname','users.lastname', 'task_user.timeStart','task_user.timeEnd','taskbreak_user.timeStart as breakStart','taskbreak_user.timeEnd as breakEnd')->get();
      //Task_User::where()where('created_at','>=',$todayStart->format('Y-m-d H:i:s'))->where('created_at','<=',$todayEnd->format('Y-m-d H:i:s'))->get();
      $breaks = new Collection;
      foreach ($allBreaks as $break) {
      	$b = Carbon::parse($break->breakEnd,'Asia/Manila')->diffInMinutes(Carbon::parse($break->breakStart,'Asia/Manila'));
      	$duration = Carbon::parse($break->timeEnd,'Asia/Manila')->diffInMinutes(Carbon::parse($break->timeStart,'Asia/Manila'));
      	$totalDuration = $duration - $b;
      	$breaks->push(['taskID'=>$break->taskID,'submissionID'=>$break->submissionID,'minuteBreaks'=>$b,'totalDuration'=>$totalDuration,'duration'=>$duration, 'timeStart'=>$break->timeStart,'timeEnd'=>$break->timeEnd]);


      	# code...
      }

      if($this->user->id !== 564 ) {
                  $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
                    fwrite($file, "-------------------\n Viewed AllTasks [".$tracker->name."]  ". $correct->format('M d h:i A'). " by [". $this->user->id."] ".$this->user->lastname."\n");
                    fclose($file);
                } 

      return view('forms.widgets-NDY',compact('allTasks','breaks','program','todayStart','todayEnd','logo','tracker','actualSubmissions'));
      
    }

    public function download()
     {
        DB::connection()->disableQueryLog();
        $correct = Carbon::now('GMT+8');

        $campaign = Campaign::find(Input::get('program'));
        $tracker = Task_Campaign::where('campaign_id',$campaign->id)->first();

        $canAdminister = ( count(UserType::find($this->user->userType_id)->roles->where('label','QUERY_REPORTS'))>0 ) ? true : false;

        $data = new Collection;
        $data2 = new Collection;
        //$campaign = Campaign::where('name',"Postmates")->first();
        $camp = $campaign->logo;
        $logo = "../public/img/".$camp->filename;


        $from = Input::get('from');
        $to = Input::get('to');

        
        $todayStart = Carbon::parse(Input::get('from'),'Asia/Manila')->startOfDay(); 
        $todayEnd = Carbon::parse(Input::get('to'),'Asia/Manila')->endOfDay();
        


        $download = Input::get('dl');

        $rawData = new Collection;
        
        

        $submissions = DB::table('task_campaign')->where('task_campaign.campaign_id',$campaign->id)->
      					join('task','task.campaign_id','=','task_campaign.campaign_id')->
      					join('taskgroup','task.groupID','=','taskgroup.id')->
      					join('task_user','task_user.task_id','=','task.id')->
      					join('users','users.id','=','task_user.user_id')->
      					join('positions','positions.id','=','users.position_id')->
      					select('task_campaign.name as tracker', 'task.id as taskID','task.name as task', 'taskgroup.name as taskGroup','taskgroup.id as groupID', 'task_user.id as submissionID','task_user.user_id','users.firstname','users.lastname', 'positions.name as jobTitle', 'task_user.timeStart','task_user.timeEnd','task_user.created_at')->
      					where('task_user.created_at','>=',$todayStart->format('Y-m-d H:i:s'))->
      					where('task_user.created_at','<=',$todayEnd->format('Y-m-d H:i:s'))->get();

      	$allBreaks = DB::table('task_campaign')->where('task_campaign.campaign_id',$campaign->id)->
      					join('task','task.campaign_id','=','task_campaign.campaign_id')->
      					join('task_user','task_user.task_id','=','task.id')->
      					join('taskbreak_user','taskbreak_user.task_userID','=','task_user.id')->
      					join('users','users.id','=','task_user.user_id')->
      					select('task.id as taskID','task.name as task', 'task_user.id as submissionID','task_user.user_id','users.firstname','users.lastname', 'task_user.timeStart','task_user.timeEnd','taskbreak_user.timeStart as breakStart','taskbreak_user.timeEnd as breakEnd')->get();

	      $breaks = new Collection;
	      foreach ($allBreaks as $break) {
	      	$b = Carbon::parse($break->breakEnd,'Asia/Manila')->diffInMinutes(Carbon::parse($break->breakStart,'Asia/Manila'));
	      	$duration = Carbon::parse($break->timeEnd,'Asia/Manila')->diffInMinutes(Carbon::parse($break->timeStart,'Asia/Manila'));
	      	$totalDuration = $duration - $b;
	      	$breaks->push(['taskID'=>$break->taskID,'submissionID'=>$break->submissionID,'minuteBreaks'=>$b,'totalDuration'=>$totalDuration,'duration'=>$duration, 'timeStart'=>$break->timeStart,'timeEnd'=>$break->timeEnd]);


	      	# code...
	      }


    
        //$submissions = collect($form)->groupBy('taskGroup');

        //return $breaks;
        $headers = array("Date", "Agent Name","Group","Task","Start time","Breaks (mins)","End time","Task Duration");

       
        $coll = new Collection;


            
            
        if($download==1)
            {
                $sheetTitle = $tracker->name; 
                $description = $sheetTitle;

                if($this->user->id !== 564 ) {
                  $user = User::find(Input::get('id'));
                 
                  
                  $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
                    fwrite($file, "-------------------\n Downloaded CSV [".$sheetTitle."][".$from." - ".$to."] " . $correct->format('M d h:i A'). " by [". $this->user->id."] ".$this->user->lastname."\n");
                    fclose($file);
                }   
                Excel::create($sheetTitle,function($excel) use($submissions,$breaks, $sheetTitle, $headers,$description) 
                   {
                          $excel->setTitle($sheetTitle.' Summary Report');

                          // Chain the setters
                          $excel->setCreator('Programming Team')
                                ->setCompany('OpenAccess');

                          // Call them separately
                          $excel->setDescription($description);
                          $excel->sheet("Sheet 1", function($sheet) use ($submissions, $headers, $breaks)
                          {
                            $sheet->appendRow($headers);
                            foreach($submissions as $item)
                            {
                                $citem = count(array($item))-1;

                                $break = collect($breaks)->where('taskID',$item->taskID);
                                $totalBreak = 0;
                                foreach($break as $b){$totalBreak += $b['minuteBreaks'];}
                                $duration = Carbon::parse($item->timeEnd,'Asia/Manila')->diffInMinutes(Carbon::parse($item->timeStart,'Asia/Manila')) - $totalBreak; 

                                $arr = array($item->created_at, 
                                             $item->lastname.", ".$item->firstname,
                                             $item->taskGroup, //ID
                                             $item->task, //plan number
                                             $item->timeStart, //sponsor name
                                             $totalBreak,
                                             $item->timeEnd,
                                             $duration,
                                             
                                        
                                             
                                        );
                                $sheet->appendRow($arr);
                               

                            }

                         

                            
                         });//end sheet1

                        



                  })->export('xls');

                
                 
                  return "Download";

            }//end download
            

    }


    public function endBreak(Request $request)
    {
    	$correct = Carbon::now('GMT+8'); 
    	$breakID = Taskbreak_User::find($request->breakID);

    	$breakID->timeEnd = Carbon::parse($correct->format('Y-m-d')." ".$request->clocktime,'Asia/Manila')->format('Y-m-d H:i:s');
    	$breakID->save();

      if($this->user->id !== 564 ) {
                  $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
                    fwrite($file, "-------------------\n END break ". $correct->format('M d h:i A'). " by [". $this->user->id."] ".$this->user->lastname."\n");
                    fclose($file);
                } 

    	return $breakID;

    }

    public function endTask(Request $request)
    {
    	$correct = Carbon::now('GMT+8'); 
    	$taskID = Task_User::find($request->taskID);
    	$selectedtask = Task::find($request->seltask);

    	$taskID->task_id = $selectedtask->id;
    	$taskID->timeEnd = Carbon::parse($correct->format('Y-m-d')." ".$request->clocktime,'Asia/Manila')->format('Y-m-d H:i:s');
    	$taskID->save();

      if($this->user->id !== 564 ) {
                  $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
                    fwrite($file, "-------------------\n END task ". $correct->format('M d h:i A'). " by [". $this->user->id."] ".$this->user->lastname."\n");
                    fclose($file);
                } 

    	return $taskID;

    }

    public function startBreak(Request $request)
    {
    	$correct = Carbon::now('GMT+8'); 
    	$taskID = Task_User::find($request->taskID);

    	

    	$taskUser = new Taskbreak_User;
    	$taskUser->task_userID = $taskID->id;
    	$taskUser->timeStart = Carbon::parse($correct->format('Y-m-d')." ".$request->clocktime,'Asia/Manila')->format('Y-m-d H:i:s');
    	$taskUser->created_at = $correct->format('Y-m-d H:i:s');
    	$taskUser->updated_at = $correct->format('Y-m-d H:i:s');
    	$taskUser->save();

      if($this->user->id !== 564 ) {
                  $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
                    fwrite($file, "-------------------\n START break ". $correct->format('M d h:i A'). " by [". $this->user->id."] ".$this->user->lastname."\n");
                    fclose($file);
                } 

    	return $taskUser;

    }

    public function startTask(Request $request)
    {
    	$correct = Carbon::now('GMT+8'); 
    	$taskUser = new Task_User;
    	$taskUser->user_id = $this->user->id;
    	$taskUser->timeStart = Carbon::parse($correct->format('Y-m-d')." ".$request->clocktime,'Asia/Manila')->format('Y-m-d H:i:s');
    	$taskUser->save();

      if($this->user->id !== 564 ) {
                  $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
                    fwrite($file, "-------------------\n START task ". $correct->format('M d h:i A'). " by [". $this->user->id."] ".$this->user->lastname."\n");
                    fclose($file);
                } 


    	return $taskUser;
    }
}
