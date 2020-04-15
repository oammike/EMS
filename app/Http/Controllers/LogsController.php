<?php

namespace OAMPI_Eval\Http\Controllers;

use Carbon\Carbon;
use Excel;
use \PDF;
use \App;
use \DB;
use \Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;

use Illuminate\Http\Request;

use Yajra\Datatables\Facades\Datatables;

use OAMPI_Eval\Http\Requests;
use OAMPI_Eval\User;
use OAMPI_Eval\Campaign;
use OAMPI_Eval\Status;
use OAMPI_Eval\Team;
use OAMPI_Eval\Floor;
use OAMPI_Eval\UserType;
use OAMPI_Eval\Position;
use OAMPI_Eval\ImmediateHead;
use OAMPI_Eval\ImmediateHead_Campaign;
use OAMPI_Eval\EvalForm;
use OAMPI_Eval\EvalSetting;
use OAMPI_Eval\Schedule;
use OAMPI_Eval\Restday;
use OAMPI_Eval\Cutoff;
use OAMPI_Eval\Biometrics;
use OAMPI_Eval\Biometrics_Uploader;
use OAMPI_Eval\Logs;
use OAMPI_Eval\LogType;
use OAMPI_Eval\TempUpload;
use OAMPI_Eval\User_DTR;
use OAMPI_Eval\User_LogOverride;
use OAMPI_Eval\User_Unlocks;

class LogsController extends Controller
{
    protected $user;
    protected $logs;
    protected $paycutoff;
    use Traits\TimekeepingTraits;

     public function __construct(Logs $logs)
    {
        $this->middleware('auth');
        $this->logs = $logs;
        $this->user =  User::find(Auth::user()->id);
        $this->paycutoff = Cutoff::first();
    }

    public function index()
    {
        
        //return $this->cutoff->first()->startingPeriod(). " - " . $paycutoff->endingPeriod();
    }

    public function deleteBio($id)
    {
        $delLog = Logs::find($id);
        
        $delLog->delete();
        return redirect()->back();
    }

    public function allLogs()
    {
        $user = $this->user;
        if (Input::get('date'))
            $start = Carbon::parse(Input::get('date'),'Asia/Manila');
        else
            $start = Carbon::now('GMT+8');


        $correct = Carbon::now('GMT+8'); //->timezoneName();

        if($this->user->id !== 564 ) {
        $file = fopen('public/build/rewards.txt', 'a') or die("Unable to open logs");
        fwrite($file, "-------------------\n AllLogs track on " . $correct->format('M d h:i A'). " by [". $this->user->id."] ".$this->user->lastname."\n");
        fclose($file);
        } 

        
        return view('timekeeping.allLogs',compact('user','start'));
    }

    public function allLogs_download()
    {
        DB::connection()->disableQueryLog();
        $correct = Carbon::now('GMT+8');
        $canAdminister = ( count(UserType::find($this->user->userType_id)->roles->where('label','QUERY_REPORTS'))>0 ) ? true : false;

        $from = Input::get('from');
        
        $download = Input::get('dl');

        $rawData = new Collection;

        if (is_null(Input::get('from')))
        {
            $daystart = Carbon::now('GMT+8')->startOfDay(); $dayend = Carbon::now('GMT+8')->endOfDay();
        }
        else {
            $daystart = Carbon::parse(Input::get('from'),'Asia/Manila')->startOfDay(); 
            $dayend = Carbon::parse(Input::get('from'),'Asia/Manila')->endOfDay();
        }

        $bio = Biometrics::where('productionDate',$daystart->format('Y-m-d'))->get();
        if (count($bio) > 0)
        {
            $form = DB::table('logs')->where('biometrics_id',$bio->first()->id)->
                    leftJoin('logType','logs.logType_id','=','logType.id')->
                    leftJoin('users','logs.user_id','=','users.id')->
                    leftJoin('team','users.id','=','team.user_id')->
                    leftJoin('campaign','team.campaign_id','=','campaign.id')->
                    select('users.id', 'users.accesscode', 'users.firstname','users.lastname','campaign.name as program', 'logs.logTime','logs.created_at as serverTime', 'logType.name as logType','logs.manual')->
                    orderBy('users.lastname')->get();


            $allDTRPs = DB::table('user_dtrp')->where('user_dtrp.biometrics_id',$bio->first()->id)->
                        leftJoin('logType','user_dtrp.logType_id','=','logType.id')->
                        leftJoin('users','user_dtrp.user_id','=','users.id')->
                        leftJoin('team','users.id','=','team.user_id')->
                        leftJoin('campaign','team.campaign_id','=','campaign.id')->
                        select('users.id', 'users.accesscode', 'users.firstname','users.lastname','campaign.name as program', 'user_dtrp.logTime','logType.name as logType','user_dtrp.isApproved','user_dtrp.notes', 'user_dtrp.created_at as submitted' )->
                        orderBy('users.lastname')->get();
        
            $allUnlocks = DB::table('user_unlocks')->where('user_unlocks.productionDate',$bio->first()->productionDate)-> 
                        leftJoin('users','user_unlocks.user_id','=','users.id')->
                        leftJoin('team','users.id','=','team.user_id')->
                        leftJoin('campaign','team.campaign_id','=','campaign.id')->
                        select('users.id', 'users.accesscode', 'users.firstname','users.lastname','campaign.name as program','user_unlocks.created_at as submitted' )->
                        orderBy('users.lastname')->get();


        
            $headers = array("AccessCode", "Last Name","First Name","Program","Log Time","Log Type","Server Timestamp","Onsite | WFH");
            $headers2 = array("AccessCode", "Last Name","First Name","Program","Log Time","Log Type","Approved","Notes","Submitted");
            $headers3 = array("AccessCode", "Last Name","First Name","Program","Requested");
            $sheetTitle = "All EMS User Logs Tracker [".$daystart->format('M d l')."]";
            $description = " ". $sheetTitle;

            if($this->user->id !== 564 ) {
              $file = fopen('public/build/rewards.txt', 'a') or die("Unable to open logs");
                fwrite($file, "-------------------\n DL_allLogs_csv [".$daystart->format('Y-m-d')."] " . $correct->format('M d h:i A'). " by [". $this->user->id."] ".$this->user->lastname."\n");
                fclose($file);
            } 

            //return $allDTRPs;
            
            


           Excel::create($sheetTitle,function($excel) use($form,$allDTRPs, $allUnlocks, $sheetTitle, $headers,$headers2,$headers3,$description,$daystart) 
           {
                  $excel->setTitle($sheetTitle.' Summary Report');

                  // Chain the setters
                  $excel->setCreator('Programming Team')
                        ->setCompany('OpenAccess');

                  // Call them separately
                  $excel->setDescription($description);
                  $excel->sheet($daystart->format('M d l'), function($sheet) use ($form, $headers)
                  {
                    $sheet->appendRow($headers);
                    foreach($form as $item)
                    {
                        $t = Carbon::parse($item->serverTime);

                        ($item->manual && User::find($item->id)->isWFH) ? $loc='WFH' : $loc='Onsite';
                        
                        $arr = array($item->accesscode, 
                                     $item->lastname,
                                     $item->firstname,
                                     $item->program, //ID
                                     $item->logTime, //plan number
                                     $item->logType,
                                     
                                     $t->format('H:i:s'),
                                     $loc 

                                     );
                        $sheet->appendRow($arr);

                    }
                    
                 });//end sheet1


                  $excel->sheet('All DTRPs', function($sheet) use ($allDTRPs,$headers2)
                  {
                    $sheet->appendRow($headers2);
                    foreach($allDTRPs as $item)
                    {
                        $t = Carbon::parse($item->submitted);

                       if($item->isApproved == null)
                            $a="Pending Approval";
                       else if($item->isApproved == 1)
                            $a="Yes";
                       else $a="No";
                        
                        $arr = array($item->accesscode, 
                                     $item->lastname,
                                     $item->firstname,
                                     $item->program, //ID
                                     $item->logTime, //plan number
                                     $item->logType,
                                     $a,
                                     $item->notes,
                                     $t->format('H:i:s'),
                                     

                                     );
                        $sheet->appendRow($arr);

                    }
                    
                 });//end sheet2


                  $excel->sheet('Unlocks', function($sheet) use ($allUnlocks,$headers3)
                  {
                    $sheet->appendRow($headers3);
                    foreach($allUnlocks as $item)
                    {
                        $t = Carbon::parse($item->submitted);

                      
                        
                        $arr = array($item->accesscode, 
                                     $item->lastname,
                                     $item->firstname,
                                     $item->program, //ID
                                     $t->format('H:i:s'),
                                     );
                        $sheet->appendRow($arr);

                    }
                    
                 });//end sheet2

           })->export('xls');

           return "Download";

        }else
        {
            return view('empty');
        }

        

                        

    }

    public function getAllLogs()
    {
        if (Input::get('date'))
            $productionDate = Carbon::parse(Input::get('date'),'Asia/Manila');
        else
            $productionDate = Carbon::now('GMT+8');

        $bio = Biometrics::where('productionDate',$productionDate->format('Y-m-d'))->get();
        if (count($bio) > 0)
        {
            $allLogs = DB::table('logs')->where('biometrics_id',$bio->first()->id)->
            leftJoin('users','logs.user_id','=','users.id')->
            leftJoin('team','team.user_id','=','users.id')->
            leftJoin('campaign','team.campaign_id','=','campaign.id')->
            select('users.id as userID','users.accesscode',  'users.lastname','users.firstname','campaign.name as program','logs.logType_id','logs.logTime','logs.created_at')->orderBy('users.lastname','ASC')->get();

        }else
        {
            $allLogs = null;

        }

        

        return response()->json(['data'=>$allLogs, 'count'=>count($allLogs)]);

    }

    public function getWFH()
    {
        if (Input::get('date'))
            $productionDate = Carbon::parse(Input::get('date'),'Asia/Manila');
        else
            $productionDate = Carbon::now('GMT+8');

        $wfh = DB::table('logs')->where('logs.manual',1)->where('logs.created_at','>=',$productionDate->startOfDay()->format('Y-m-d H:i:s'))->where('logs.created_at','<=',$productionDate->endOfDay()->format('Y-m-d H:i:s'))->
            leftJoin('users','logs.user_id','=','users.id')->
            leftJoin('team','team.user_id','=','users.id')->
            leftJoin('campaign','team.campaign_id','=','campaign.id')->
            select('users.id as userID','users.accesscode',  'users.lastname','users.firstname','campaign.name as program','logs.logType_id','logs.logTime','logs.created_at')->orderBy('logs.created_at','DESC')->get();

        return response()->json(['data'=>$wfh, 'count'=>count($wfh)]);

    }

    

    public function myDTR()
    {
        //$roles = UserType::find($this->user->userType_id)->roles->pluck('label'); //->where('label','MOVE_EMPLOYEES');
        //$canMoveEmployees =  ($roles->contains('MOVE_EMPLOYEE')) ? '1':'0';
        //$canEditEmployees =  ($roles->contains('EDIT_EMPLOYEE')) ? '1':'0';

        //$hrDept = Campaign::where('name',"HR")->first();

       // return $this->paycutoff->startingPeriod(). " - " . $this->paycutoff->endingPeriod();

        $user = $this->user; //User::find($id); 
        $leadershipcheck = ImmediateHead::where('employeeNumber', $user->employeeNumber)->first();
        $immediateHead = ImmediateHead::find(ImmediateHead_Campaign::find($user->supervisor->immediateHead_Campaigns_id)->immediateHead_id);

         if (!empty($leadershipcheck)){ $camps = $leadershipcheck->campaigns->sortBy('name'); } else $camps = $user->campaign;

        $dtr = $this->user->logs->sortBy('id')->groupBy('biometrics_id');
        //return $myDTR;
        $myDTR = new Collection;

         foreach ($dtr as $daily) {

            $logIN = $daily->where('logType_id',1)->sortBy('id')->pluck('logTime'); //->get();
            $logOUT = $daily->where('logType_id',2)->sortBy('id')->pluck('logTime'); //->get();

            if (count($logIN) > 0)
            {
                $in = $logIN->first();
                $timeStart = Carbon::parse($in);

            }  else { $in=null; $timeStart=null; }
            if (count($logOUT) > 0)
            {
                $out = $logOUT->first();
                $timeEnd = Carbon::parse($out); 
            } else { $out=null; $timeEnd=null; }

            if ($in !== null && $out !== null)
            {
                //$coll->push(['in'=>$in, 'out'=>$out]);
                $workedHours = $timeEnd->diffInMinutes($timeStart->addHour());

            } else $workedHours=null;

            //DB::table('user_dtr')->insert(['user_id'=>$key[0]->user_id, 'timeIN']);
         $myDTR->push(['biometrics_id'=>$daily[0]->biometrics_id, 'user_id'=>$daily[0]->user_id, 'Time IN'=> $in, 'Time OUT'=> $out, 'Hours Worked'=> round($workedHours/60,2) ]);
         }
         //return $myDTR;

        return view('timekeeping.myDTR', compact('myDTR','camps','user'));
    }

    public function saveDashboardLog(Request $request)
    {
        $log = new Logs;
        $now = Carbon::now('GMT+8');//$now->format('H:i:s'); 
        $log->logTime = Carbon::parse($now->format('Y-m-d')." ".$request->clocktime,'Asia/Manila')->format('H:i:s');
        $log->logType_id = $request->logtype_id;
        $log->manual = true;
        $log->user_id = $this->user->id;

        $bio = Biometrics::where('productionDate',$now->format('Y-m-d'))->get();
        if (count($bio) > 0)
            $log->biometrics_id = $bio->first()->id;
        else{
            $b = new Biometrics;
            $b->productionDate = $now->format('Y-m-d');
            $b->save();
            $log->biometrics_id = $b->id;
        }
        $log->created_at = $now->format('Y-m-d H:i:s');
        $log->save();

        return response()->json(['success'=>'1','logs'=>$log]);

    }

    public function saveBioLog(Request $request)
    {
        $b = Carbon::parse($request->productionDate." ".$request->logTime,'Asia/Manila');
        $bio = Biometrics::where('productionDate',$b->format('Y-m-d'))->get();
        if (count($bio) > 0)
        {
            $l = new Logs;
            $l->biometrics_id = $bio->first()->id;
            $l->logTime = $request->logTime;
            $l->logType_id = $request->logType_id;
            $l->user_id = $request->user_id;
            $l->save();

            //** add an override
            if ($request->productionDate_target)
            {
                $b2 = Carbon::parse($request->productionDate_target." ".$request->logTime,'Asia/Manila');
                $bio2 = Biometrics::where('productionDate',$b2->format('Y-m-d'))->get();

                if(count($bio2) > 0)
                {
                    $override_bio = $bio2->first();
                }else
                {
                    $ob = new Biometrics;
                    $ob->productionDate = $b2->format('Y-m-d');
                    $ob->created_at = Carbon::now('GMT+8')->format('Y-m-d H:i:s');
                    $ob->save();
                    $override_bio = $ob;

                }

                $override = new User_LogOverride;
                $override->user_id = $request->user_id;
                $override->productionDate = $override_bio->productionDate; //$request->productionDate_target;
                $override->affectedBio = $bio->first()->id; 
                $override->logTime = $request->logTime;
                $override->logType_id = $request->logType_id;
                $override->created_at = Carbon::now('GMT+8')->format('Y-m-d H:i:s');
                $override->save();

                return response()->json(['success'=>1, 'msg'=>"Biometric with Log Override saved.",'data'=>$l]);

            }
            else
                return response()->json(['success'=>1, 'msg'=>"Biometric saved.",'data'=>$l]);

        }else
            return response()->json(['success'=>0, 'msg'=>"No Biometric data for that productionDate."]);
    }


    public function saveDailyUserLogs(Request $request)
    {
        DB::connection()->disableQueryLog();
        $biometrics_id = $request->biometrics_id;

        $ctr = 0;
        // DB::table('temp_uploads')->select('employeeNumber','logTime','logType')->where('productionDate',date('Y-m-d', strtotime($request->productionDate)))->chunk(100, function($biosToGet, $biometrics_id, $ctr)
        // {
        //  foreach ($biosToGet as $bio) {
        //      $logType = LogType::where('code',$bio->logType)->first()->id;
        //      $user_id = User::where('employeeNumber',$bio->employeeNumber)->first()->id;

        //      DB::table('logs')->insert(['user_id'=>$user_id, 'logTime'=>$bio->logTime, 'logType_id'=>$logType, 'biometrics_id'=>$biometrics_id]);
        //      $ctr++;
        //  }
        // })->get();


        $productionDate = date('Y-m-d', strtotime($request->productionDate));
        $biosToGet = TempUpload::where('productionDate',$productionDate)->get();
        foreach ($biosToGet as $bio) {
                $logType = LogType::where('code', strtoupper($bio->logType) )->first()->id;
                $user_id = User::where('accesscode',$bio->employeeNumber)->get();
                if (count($user_id) > 0 )
                {
                    DB::table('logs')->insert(['user_id'=>$user_id->first()->id, 'logTime'=>$bio->logTime, 'logType_id'=>$logType, 'biometrics_id'=>$biometrics_id]);

                    //save actual user DTR table
                    // switch ($logType) {
                    //  case '1':
                    //              $logIN = $bio->logTime;
                    //      break;

                    //  case '2':
                    //              $logOUT = $bio->logTime;
                    //      break;

                    //  case '3':
                    //              $breakIN = $bio->logTime;
                    //      break;

                    //  case '4':
                    //              $breakOUT = $bio->logTime;
                    //      break;

                    //  case '5':
                    //              $breakOUT = $bio->logTime;
                    //      break;

                    //  case '6':
                    //              $breakIN = $bio->logTime;
                    //      break;
                        
                    //  default:
                    //              $logIN = $bio->logTime;
                    //      break;
                    // }

                    // DB::table('user_dtr')->insert(['user_id'=>$user_id->first()->id, 'timeIN'=> ]);


                    /* ---------------SAVE USER_DTR -------------*/

                    //$logIN = $daily->where('logType_id',1)->sortBy('id')->pluck('logTime'); //->get();
                    // $logOUT = $daily->where('logType_id',2)->sortBy('id')->pluck('logTime'); //->get();

                    // if (count($logIN) > 0)
                    // {
                    //  $in = $logIN->first();
                    //  $timeStart = Carbon::parse($in);

                    // }  else { $in=null; $timeStart=null; }
                    // if (count($logOUT) > 0)
                    // {
                    //  $out = $logOUT->first();
                    //  $timeEnd = Carbon::parse($out); 
                    // } else { $out=null; $timeEnd=null; }

                    // if ($in !== null && $out !== null)
                    // {
                    //  //$coll->push(['in'=>$in, 'out'=>$out]);
                    //  $workedHours = $timeEnd->diffInMinutes($timeStart);
                    // } else $workedHours=null;


                    /* ---------------END SAVE DTR --------------*/


                    $ctr++;

                }//enf if
                

                
            }
        return response()->json(['save'=>'success', 'records'=>$ctr]);

        

    }

    public function viewRawBiometricsData($id)
    {
        $user = User::find($id);
        if (is_null($user)) return view('empty');
        else
        {
            $roles = UserType::find($this->user->userType_id)->roles->pluck('label'); //->where('label','MOVE_EMPLOYEES');
            $canUpload =  ($roles->contains('UPLOAD_BIOMETRICS')) ? '1':'0';

            DB::connection()->disableQueryLog();
            $dtr = DB::table('logs')->where('user_id',$user->id)->//leftJoin('users','logs.user_id','=','users.id')->
            //         
                     leftJoin('logType','logs.logType_id','=','logType.id')->
                     leftJoin('biometrics','logs.biometrics_id','=','biometrics.id')->

                     select('biometrics.id as id','biometrics.productionDate as Production_Date', 'logType.name as Log_Type','logs.logTime','logs.id as logID')->
                     orderBy('biometrics.productionDate','DESC')->get();

            return view('timekeeping.rawBio', compact('dtr','id','canUpload','user'));

        }
        

       
       /* $record = new Collection;
        $record1 = new Collection;
        
        foreach ($dtr as $daily) {

            $rdata = new Collection;
            foreach ($daily as $data) {

              $rdata->push(['Employee Number'=>User::find($data['user_id'])->employeeNumber, 'Log Type'=>LogType::find($data->logType_id)->name, 'Log Time'=>$data->logTime]);
           }
            $record1->push(['id'=>$daily[0]['biometrics_id'], 'Production Date'=>date('Y-m-d D',strtotime(Biometrics::find($daily[0]['biometrics_id'])->productionDate)),'data'=>$rdata]);
        }

        //return $record;
        $record = $record1->sortByDesc('id');*/
        

    }

    public function wfh()
    {
        $user = $this->user;
        if (Input::get('date'))
            $start = Carbon::parse(Input::get('date'),'Asia/Manila');
        else
            $start = Carbon::now('GMT+8');


        $correct = Carbon::now('GMT+8'); //->timezoneName();

        if($this->user->id !== 564 ) {
        $file = fopen('public/build/rewards.txt', 'a') or die("Unable to open logs");
        fwrite($file, "-------------------\n WFH track on " . $correct->format('M d h:i A'). " by [". $this->user->id."] ".$this->user->lastname."\n");
        fclose($file);
        } 

        
        return view('people.wfh',compact('user','start'));
    }

    public function wfh_download()
    {
        DB::connection()->disableQueryLog();
        $correct = Carbon::now('GMT+8');
        $canAdminister = ( count(UserType::find($this->user->userType_id)->roles->where('label','QUERY_REPORTS'))>0 ) ? true : false;

        $from = Input::get('from');
        
        $download = Input::get('dl');

        $rawData = new Collection;

        if (is_null(Input::get('from')))
        {
            $daystart = Carbon::now('GMT+8')->startOfDay(); $dayend = Carbon::now('GMT+8')->endOfDay();
        }
        else {
            $daystart = Carbon::parse(Input::get('from'),'Asia/Manila')->startOfDay(); 
            $dayend = Carbon::parse(Input::get('from'),'Asia/Manila')->endOfDay();
        }

        $form = DB::table('logs')->where('logs.manual',1)->where('logs.created_at','>=',$daystart->format('Y-m-d H:i:s'))->
                    where('logs.created_at','<=',$dayend->format('Y-m-d H:i:s'))->
                    leftJoin('logType','logs.logType_id','=','logType.id')->
                    leftJoin('users','logs.user_id','=','users.id')->
                    leftJoin('team','users.id','=','team.user_id')->
                    leftJoin('campaign','team.campaign_id','=','campaign.id')->
                    select('users.accesscode', 'users.firstname','users.lastname','campaign.name as program', 'logs.logTime','logs.created_at as serverTime', 'logType.name as logType')->
                    orderBy('logs.created_at','DESC')->get();
        

        
        $headers = array("AccessCode", "Last Name","First Name","Program","Log Time","Log Type","Server Timestamp");
        $sheetTitle = "WFH Tracker [".$daystart->format('M d l')."]";
        $description = " ". $sheetTitle;

        if($this->user->id !== 564 ) {
          $file = fopen('public/build/rewards.txt', 'a') or die("Unable to open logs");
            fwrite($file, "-------------------\n DL_wfh_csv [".$daystart->format('Y-m-d')."] " . $correct->format('M d h:i A'). " by [". $this->user->id."] ".$this->user->lastname."\n");
            fclose($file);
        } 

        

       Excel::create($sheetTitle,function($excel) use($form, $sheetTitle, $headers,$description,$daystart) 
       {
              $excel->setTitle($sheetTitle.' Summary Report');

              // Chain the setters
              $excel->setCreator('Programming Team')
                    ->setCompany('OpenAccess');

              // Call them separately
              $excel->setDescription($description);
              $excel->sheet($daystart->format('M d l'), function($sheet) use ($form, $headers)
              {
                $sheet->appendRow($headers);
                foreach($form as $item)
                {
                    $t = Carbon::parse($item->serverTime);
                    
                    $arr = array($item->accesscode, 
                                 $item->lastname,
                                 $item->firstname,
                                 $item->program, //ID
                                 $item->logTime, //plan number
                                 $item->logType,
                                 
                                 $t->format('H:i:s') 
                                 );
                    $sheet->appendRow($arr);

                }
                
             });//end sheet1

       })->export('xls');

       return "Download";

                        

    }
}
