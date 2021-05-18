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
use OAMPI_Eval\User_DTRP;
use OAMPI_Eval\MonthlySchedules;
use OAMPI_Eval\FixedSchedules;
use OAMPI_Eval\Paycutoff;
use OAMPI_Eval\User_CWS;
use OAMPI_Eval\Notification;
use OAMPI_Eval\User_Notification;
use OAMPI_Eval\User_OT;
use OAMPI_Eval\User_SL;
use OAMPI_Eval\User_VL;
use OAMPI_Eval\User_VTO;
use OAMPI_Eval\User_LWOP;
use OAMPI_Eval\User_OBT;
use OAMPI_Eval\User_Familyleave;
use OAMPI_Eval\Holiday;
use OAMPI_Eval\HolidayType;
use OAMPI_Eval\Memo;
use OAMPI_Eval\User_Memo;
use OAMPI_Eval\User_RDoverride;
use OAMPI_Eval\User_Unlocks;
use OAMPI_Eval\ECQ_Workstatus;




class DTRController extends Controller
{
    protected $user;
    protected $user_dtr;
    use Traits\TimekeepingTraits;
    use Traits\UserTraits;



     public function __construct(User_DTR $user_dtr)
    {
        $this->middleware('auth');
        $this->user_dtr = $user_dtr;
        $this->user =  User::find(Auth::user()->id);
    }

    public function zendesk(Request $request)
    {
      $method = $request->method;
      $url = $request->url;
      $data = false;

        $curl = curl_init();

        switch ($method)
        {
            case "POST":
                curl_setopt($curl, CURLOPT_POST, 1);

                if ($data)
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                break;
            case "PUT":
                curl_setopt($curl, CURLOPT_PUT, 1);
                break;
            default:
                if ($data)
                    $url = sprintf("%s?%s", $url, http_build_query($data));
        }

        // Optional Authentication:
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_USERPWD, "oareports@circles.asia:Oa1234567*");

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($curl);

        curl_close($curl);

        return response()->json($result);
    }

    public function dtrSheets()
    {
      //Timekeeping trait getCutoffStartEnd()
      $cutoffData = $this->getCutoffStartEnd();
      $cutoffStart = $cutoffData['cutoffStart'];//->cutoffStart;
      $cutoffEnd = $cutoffData['cutoffEnd'];

       //Timekeeping Trait
      $payrollPeriod = $this->getPayrollPeriod($cutoffStart,$cutoffEnd);
      $paycutoffs = Paycutoff::orderBy('toDate','DESC')->get();

      DB::connection()->disableQueryLog();
      $allUsers = DB::table('users')->where([
                    ['status_id', '!=', 6],
                    ['status_id', '!=', 7],
                    ['status_id', '!=', 8],
                    ['status_id', '!=', 9],
                ])->
        leftJoin('team','team.user_id','=','users.id')->
        leftJoin('campaign','team.campaign_id','=','campaign.id')->
        leftJoin('immediateHead_Campaigns','team.immediateHead_Campaigns_id','=','immediateHead_Campaigns.id')->
        leftJoin('immediateHead','immediateHead_Campaigns.immediateHead_id','=','immediateHead.id')->
        leftJoin('positions','users.position_id','=','positions.id')->
        leftJoin('floor','team.floor_id','=','floor.id')->
        select('users.id', 'users.firstname','users.lastname','users.nickname','users.dateHired','positions.name as jobTitle','campaign.id as campID', 'campaign.name as program','immediateHead_Campaigns.id as tlID', 'immediateHead.firstname as leaderFname','immediateHead.lastname as leaderLname','users.employeeNumber','floor.name as location')->orderBy('users.lastname')->get();

        $allProgram = DB::table('campaign')->select('id','name','hidden')->where('hidden',null)->orderBy('name')->get();//
        $byTL = collect($allUsers)->groupBy('tlID');
        $allTL = $byTL->keys();
        //return collect($allUsers)->where('campID',7);

        $correct = Carbon::now('GMT+8'); //->timezoneName();

           if($this->user->id !== 564 ) {
              $file = fopen('storage/uploads/log.txt', 'a') or die("Unable to open logs");
                fwrite($file, "-------------------\n Viewed DTRsheets on " . $correct->format('M d h:i A'). " by [". $this->user->id."] ".$this->user->lastname."\n");
                fclose($file);
            } 
        
      

      return view('timekeeping.dtrSheet-index',compact('payrollPeriod','paycutoffs','allProgram'));

    }

    public function downloadDTRsheet(Request $request)
    {
      $dtr = $request->dtr;
      $cutoff = explode('_', $request->cutoff);
      $cutoffStart = Carbon::parse($request->cutoffstart,'Asia/Manila');
      $cutoffEnd = Carbon::parse($request->cutoffend,'Asia/Manila');

      

      DB::connection()->disableQueryLog();
      $correct = Carbon::now('GMT+8'); //->timezoneName();

      if($request->reportType == 'dailyLogs')
      {
        $program = Campaign::find($request->program);
        $pname = $program->name;
        $headers = ['Employee Code', 'Formal Name','Date','Day','Time IN','Time OUT','Hours', 'OT billable','OT Approved','OT Start','OT End', 'OT hours','OT Reason','Locked Timestamp'];
        $reportType = 'dailyLogs';

        $result = $this->fetchLockedDTRs($request->cutoff, $request->program,1);
        $allDTRs = DB::table('campaign')->where('campaign.id',$request->program)->
                      join('team','team.campaign_id','=','campaign.id')->
                      join('users','team.user_id','=','users.id')->
                      leftJoin('immediateHead_Campaigns','team.immediateHead_Campaigns_id','=','immediateHead_Campaigns.id')->
                      leftJoin('immediateHead','immediateHead_Campaigns.immediateHead_id','=','immediateHead.id')->
                      leftJoin('positions','users.position_id','=','positions.id')->
                      leftJoin('floor','team.floor_id','=','floor.id')->
                      join('user_dtr', function ($join) use ($cutoff) {
                          $join->on('users.id', '=', 'user_dtr.user_id')
                               ->where('user_dtr.productionDate', '>=', $cutoff[0])
                               ->where('user_dtr.productionDate', '<=', $cutoff[1]);
                      })->
                      select('users.accesscode','users.employeeCode','users.id','users.isWFH', 'users.firstname','users.lastname','users.middlename', 'users.nickname','positions.name as jobTitle','campaign.id as campID', 'campaign.name as program','immediateHead_Campaigns.id as tlID', 'immediateHead.firstname as leaderFname','immediateHead.lastname as leaderLname','floor.name as location','user_dtr.productionDate','user_dtr.biometrics_id','user_dtr.workshift','user_dtr.isCWS_id as cwsID','user_dtr.leaveType','user_dtr.leave_id','user_dtr.timeIN','user_dtr.timeOUT','user_dtr.hoursWorked','user_dtr.OT_billable','user_dtr.OT_approved','user_dtr.OT_id','user_dtr.UT', 'user_dtr.user_id','user_dtr.updated_at','user_dtr.created_at')->
                      where([
                          ['users.status_id', '!=', 7],
                          ['users.status_id', '!=', 8],
                          ['users.status_id', '!=', 9],
                          ['users.status_id', '!=', 13],
                          ['users.status_id', '!=', 16],
                      ])->orderBy('users.lastname')->get();

        $allUsers = DB::table('campaign')->where('campaign.id',$request->program)->
                      join('team','team.campaign_id','=','campaign.id')->
                      join('users','team.user_id','=','users.id')->
                      leftJoin('immediateHead_Campaigns','team.immediateHead_Campaigns_id','=','immediateHead_Campaigns.id')->
                      leftJoin('immediateHead','immediateHead_Campaigns.immediateHead_id','=','immediateHead.id')->
                      leftJoin('positions','users.position_id','=','positions.id')->
                      leftJoin('floor','team.floor_id','=','floor.id')->
                      
                      select('users.accesscode','users.id', 'users.firstname','users.middlename', 'users.lastname','users.nickname','users.dateHired','positions.name as jobTitle','campaign.id as campID', 'campaign.name as program','immediateHead_Campaigns.id as tlID', 'immediateHead.firstname as leaderFname','immediateHead.lastname as leaderLname','users.employeeNumber','floor.name as location')->
                      where([
                          ['users.status_id', '!=', 7],
                          ['users.status_id', '!=', 8],
                          ['users.status_id', '!=', 9],
                          ['users.status_id', '!=', 13],
                          ['users.status_id', '!=', 16],
                      ])->orderBy('users.lastname')->get();
        
        if($this->user->id !== 564 ) {
              $file = fopen('public/build/rewards.txt', 'a') or die("Unable to open logs");
                fwrite($file, "-------------------\n DL_FINANCE cutoff: -- ".$cutoffStart->format('M d')." on " . $correct->format('M d h:i A'). " for Program: ".$program->name. " by [". $this->user->id."] ".$this->user->lastname."\n");
                fclose($file);
        } 

      }
      elseif ($request->reportType == 'trainees')
      {
        $program =null;
        $pname="Trainees";
        $headers = ['Trainee Code', 'Formal Name','Date','Day','Time IN','Time OUT','Hours', 'OT billable','OT Approved','OT Start','OT End', 'OT hours','OT Reason','Locked Timestamp'];
        
        $reportType = 'trainees';

        $reportType = $request->reportType;

        $result = null; //$this->fetchLockedDTRs($request->cutoff, null,3);
        $stat = $request->stat;

        if($stat == 'p') $statid=18;
        elseif ($stat == 'f') $statid=19;
        elseif ($stat == 'nh')$statid=3;
        else $statid = 2;


        if($stat == 'nh'){
          $monthAgo = Carbon::now('GMT+8')->addDays(-30);
          $allDTRs = DB::table('users')->where([
                  ['status_id', '<=', $statid],
                          ])->
                  leftJoin('team','team.user_id','=','users.id')->
                  leftJoin('campaign','team.campaign_id','=','campaign.id')->
                  leftJoin('immediateHead_Campaigns','team.immediateHead_Campaigns_id','=','immediateHead_Campaigns.id')->
                  leftJoin('immediateHead','immediateHead_Campaigns.immediateHead_id','=','immediateHead.id')->
                  leftJoin('positions','users.position_id','=','positions.id')->
                  leftJoin('statuses','users.status_id','=','statuses.id')->
                  leftJoin('userType','userType.id','=','users.userType_id')->
                  leftJoin('floor','team.floor_id','=','floor.id')->
                  join('user_dtr', function ($join) use ($cutoff) {
                          $join->on('users.id', '=', 'user_dtr.user_id')
                               ->where('user_dtr.productionDate', '>=', $cutoff[0])
                               ->where('user_dtr.productionDate', '<=', $cutoff[1]);
                      })->
                  select('users.accesscode','users.traineeCode', 'users.employeeCode','users.id','users.isWFH', 'users.firstname','users.lastname','users.middlename', 'users.nickname','positions.name as jobTitle','campaign.id as campID', 'campaign.name as program','immediateHead_Campaigns.id as tlID', 'immediateHead.firstname as leaderFname','immediateHead.lastname as leaderLname','floor.name as location','user_dtr.productionDate','user_dtr.biometrics_id','user_dtr.workshift','user_dtr.isCWS_id as cwsID','user_dtr.leaveType','user_dtr.leave_id','user_dtr.timeIN','user_dtr.timeOUT','user_dtr.hoursWorked','user_dtr.OT_billable','user_dtr.OT_approved','user_dtr.OT_id','user_dtr.UT', 'user_dtr.user_id','user_dtr.updated_at','user_dtr.created_at')->
                  where('users.status_id','!=',2)->where('users.endTraining','!=',null)->where('users.endTraining','>=',$monthAgo->format('Y-m-d H:i:s'))->
                      orderBy('users.lastname')->get();

                      

        }
        else {
          $allDTRs = DB::table('users')->where('users.status_id',$statid)->
                      join('team','team.user_id','=','users.id')->
                      leftJoin('campaign','team.campaign_id','=','campaign.id')->
                      leftJoin('immediateHead_Campaigns','team.immediateHead_Campaigns_id','=','immediateHead_Campaigns.id')->
                      leftJoin('immediateHead','immediateHead_Campaigns.immediateHead_id','=','immediateHead.id')->
                      leftJoin('positions','users.position_id','=','positions.id')->
                      leftJoin('floor','team.floor_id','=','floor.id')->
                      join('user_dtr', function ($join) use ($cutoff) {
                          $join->on('users.id', '=', 'user_dtr.user_id')
                               ->where('user_dtr.productionDate', '>=', $cutoff[0])
                               ->where('user_dtr.productionDate', '<=', $cutoff[1]);
                      })->
                      select('users.accesscode','users.traineeCode', 'users.employeeCode','users.id','users.isWFH', 'users.firstname','users.lastname','users.middlename', 'users.nickname','positions.name as jobTitle','campaign.id as campID', 'campaign.name as program','immediateHead_Campaigns.id as tlID', 'immediateHead.firstname as leaderFname','immediateHead.lastname as leaderLname','floor.name as location','user_dtr.productionDate','user_dtr.biometrics_id','user_dtr.workshift','user_dtr.isCWS_id as cwsID','user_dtr.leaveType','user_dtr.leave_id','user_dtr.timeIN','user_dtr.timeOUT','user_dtr.hoursWorked','user_dtr.OT_billable','user_dtr.OT_approved','user_dtr.OT_id','user_dtr.UT', 'user_dtr.user_id','user_dtr.updated_at','user_dtr.created_at')->
                      orderBy('users.lastname')->get();

        }

        
     
        
        if($this->user->id !== 564 ) {
              $file = fopen('storage/uploads/log.txt', 'a') or die("Unable to open logs");
                fwrite($file, "-------------------\n DL_TRAINEES_[".$stat."] summary: -- ".$cutoffStart->format('M d')." on " . $correct->format('M d h:i A'). " for Program: ".$program->name. " by [". $this->user->id."] ".$this->user->lastname."\n");
                fclose($file);
        } 

      }
      
      else
      {
        $program = Campaign::find($request->program);
        $pname = $program->name;
        $headers = ['Employee Name', 'Immediate Head','Production Date', 'Current Schedule','CWS | Reason', 'Time IN', 'Time OUT', 'DTRP IN', 'DTRP OUT','OT Start','OT End', 'OT hours','OT Reason','Leave','Reason','Verified'];
        $reportType = null;

        $result = $this->fetchLockedDTRs($request->cutoff, $request->program,null);
        $allDTRs = DB::table('campaign')->where('campaign.id',$request->program)->
                      join('team','team.campaign_id','=','campaign.id')->
                      join('users','team.user_id','=','users.id')->
                      leftJoin('immediateHead_Campaigns','team.immediateHead_Campaigns_id','=','immediateHead_Campaigns.id')->
                      leftJoin('immediateHead','immediateHead_Campaigns.immediateHead_id','=','immediateHead.id')->
                      leftJoin('positions','users.position_id','=','positions.id')->
                      leftJoin('floor','team.floor_id','=','floor.id')->
                      join('user_dtr', function ($join) use ($cutoff) {
                          $join->on('users.id', '=', 'user_dtr.user_id')
                               ->where('user_dtr.productionDate', '>=', $cutoff[0])
                               ->where('user_dtr.productionDate', '<=', $cutoff[1]);
                      })->
                      select('users.accesscode','users.employeeCode','users.id','users.isWFH', 'users.firstname','users.lastname','users.middlename', 'users.nickname','positions.name as jobTitle','campaign.id as campID', 'campaign.name as program','immediateHead_Campaigns.id as tlID', 'immediateHead.firstname as leaderFname','immediateHead.lastname as leaderLname','floor.name as location','user_dtr.productionDate','user_dtr.biometrics_id','user_dtr.workshift','user_dtr.isCWS_id as cwsID','user_dtr.leaveType','user_dtr.leave_id','user_dtr.timeIN','user_dtr.timeOUT','user_dtr.hoursWorked','user_dtr.OT_billable','user_dtr.OT_approved','user_dtr.OT_id','user_dtr.UT', 'user_dtr.user_id','user_dtr.updated_at','user_dtr.created_at')->
                      where([
                          ['users.status_id', '!=', 7],
                          ['users.status_id', '!=', 8],
                          ['users.status_id', '!=', 9],
                          ['users.status_id', '!=', 13],
                          ['users.status_id', '!=', 16],
                      ])->orderBy('users.lastname')->get();
     
        $allUsers = DB::table('campaign')->where('campaign.id',$request->program)->
                      join('team','team.campaign_id','=','campaign.id')->
                      join('users','team.user_id','=','users.id')->
                      leftJoin('immediateHead_Campaigns','team.immediateHead_Campaigns_id','=','immediateHead_Campaigns.id')->
                      leftJoin('immediateHead','immediateHead_Campaigns.immediateHead_id','=','immediateHead.id')->
                      leftJoin('positions','users.position_id','=','positions.id')->
                      leftJoin('floor','team.floor_id','=','floor.id')->
                      
                      select('users.accesscode','users.id', 'users.firstname','users.middlename', 'users.lastname','users.nickname','users.dateHired','positions.name as jobTitle','campaign.id as campID', 'campaign.name as program','immediateHead_Campaigns.id as tlID', 'immediateHead.firstname as leaderFname','immediateHead.lastname as leaderLname','users.employeeNumber','floor.name as location')->
                      where([
                          ['users.status_id', '!=', 7],
                          ['users.status_id', '!=', 8],
                          ['users.status_id', '!=', 9],
                          ['users.status_id', '!=', 13],
                          ['users.status_id', '!=', 16],
                      ])->orderBy('users.lastname')->get();

        if($this->user->id !== 564 ) {
              $file = fopen('storage/uploads/log.txt', 'a') or die("Unable to open logs");
                fwrite($file, "-------------------\n DL Billables cutoff: -- ".$cutoffStart->format('M d')." on " . $correct->format('M d h:i A'). " for Program: ".$program->name. " by [". $this->user->id."] ".$this->user->lastname."\n");
                fclose($file);
        }               
      }


      $allDTR = collect($allDTRs)->groupBy('id');
      

      $description = "DTR sheet for cutoff period: ".$cutoffStart->format('M d')." to ".$cutoffEnd->format('M d');
           

      //return $allDTR;
      if ($request->dltype == '1') // DTR sheets
      {

        

        $dtr = $request->dtr;
        $cutoff = explode('_', $request->cutoff);

        //$cutoffStart = Carbon::parse($request->cutoffstart,'Asia/Manila');
        

        $ecqStats = DB::table('eqc_workstatus')->join('biometrics','eqc_workstatus.biometrics_id','=','biometrics.id')->
                        join('ecq_statuses','eqc_workstatus.workStatus','=','ecq_statuses.id')->
                        join('users','eqc_workstatus.user_id','=','users.id')->
                        select('eqc_workstatus.id as ecqID','eqc_workstatus.biometrics_id','biometrics.productionDate','ecq_statuses.name as ecqStatus','users.id as userID')->get();
        
        

        Excel::create($pname."_".$cutoffStart->format('M-d'),function($excel) use($reportType, $program,$pname, $allDTR, $allDTRs,$ecqStats, $cutoffStart, $cutoffEnd, $headers,$description) 
               {
                      

                      if($reportType == 'dailyLogs')
                      {
                        
                        $excel->setTitle($cutoffStart->format('Y-m-d').' to '. $cutoffEnd->format('Y-m-d').'_'.$program->name.' DTR Sheet');

                        // Chain the setters
                        $excel->setCreator('Programming Team')
                              ->setCompany('OpenAccess');

                        // Call them separately
                        $excel->setDescription($description);

                        $payday = $cutoffStart;

                        $excel->sheet($payday->format('M d')."_".substr($payday->format('l'), 0,3), function($sheet) use ($program, $allDTR, $allDTRs, $ecqStats, $cutoffStart, $cutoffEnd, $headers,$payday)
                          {

                            //12 headers
                            $header1 = ['Open Access BPO','','','','','','','','','','','','',''];
                            $header1b = ['Daily Time Record','','','','','','','','','','','','',''];
                            $header2 = [$cutoffStart->format('D, m/d/Y')." - ". $cutoffEnd->format('D, m/d/Y') ,'Program: ',strtoupper($program->name),'','','','','','','','','','','','',''];
                            $header2b = ['','','','','','','','','','','','','',''];

                            
                            // Set width for a single column
                            //$sheet->setWidth('A', 35);

                            $sheet->setFontSize(12);
                            $sheet->setOrientation("landscape");



                            $sheet->appendRow($header1);
                            $sheet->appendRow($header1b);
                            $sheet->appendRow($header2);
                            $sheet->appendRow($header2b);

                            $sheet->cells('A1:P3', function($cells) {

                                // call cell manipulation methods
                                $cells->setBackground('##1a8fcb');
                                $cells->setFontColor('#ffffff');
                                $cells->setFontSize(13);
                                

                            });
                            $sheet->cells('A1', function($cells) {

                                $cells->setFontSize(14);
                                $cells->setFontWeight('bold');

                            });

                          
                           
                            
                            $sheet->appendRow($headers);

                            $sheet->row(5, function($row) {
                                // Set font size
                                $row->setFontSize(13);
                                $row->setBackground('#dedede');
                                //$row->setFontWeight('bold');

                              });
                            // Set height for a single row
                            //$sheet->setHeight(2, 80);
                            //$sheet->setHeight(3, 50);        

                            $arr = [];
                            $startrow = 6;

                            foreach($allDTR as $employeeDTR)
                            {
                              $i = 0;
                              //$dData = collect($employeeDTR)->sortBy('productionDate')->where('productionDate',$payday->format('Y-m-d'));
                              $dData = collect($allDTRs)->where('id',$employeeDTR->first()->id)->sortBy('productionDate');

                              if (count($dData) > 0)
                              {

                                //'Employee Code'::'Formal Name'::'Date'::'Day'::
                                // Time IN'::'Time OUT'::'Hours':: 'OT billable'::'OT Approved'::'OT Start'::'OT End'::'OT hours'::'OT Reason'

                                foreach ($dData as $key) 
                                {
                                  // -------- ACCESS CODE -------------
                                  $arr[$i] = strtoupper($key->employeeCode); $i++;

                                  // -------- FORMAL NAME -------------
                                  $arr[$i] = strtoupper($key->lastname).", ".strtoupper($key->firstname)." ".strtoupper($key->middlename); $i++;
                                  

                                  // -------- DATE -------------
                                  // ** Production Date
                                  // check if there's holiday
                                  $holiday = Holiday::where('holidate',$key->productionDate)->get();

                                  (count($holiday) > 0) ? $hday=$holiday->first()->name : $hday = "";

                                  //$arr[$i] = $payday->format('M d D')." ". $hday; $i++;
                                  $arr[$i] = date('m/d/y',strtotime($key->productionDate)); $i++; //; $payday->format('m/d/y')." ". $hday; $i++;

                                  // -------- DAY -------------
                                  $arr[$i] = date('D',strtotime($key->productionDate))." ". $hday; $i++;


                                  // -------- TIME IN -------------
                                  $tin = strip_tags($key->timeIN);

                                  if ( strpos($tin, "SL") !== false || strpos($tin, "VL") !== false || strpos($tin, "VTO") !== false || strpos($tin, "RD") !== false || strpos($tin, "LWOP") !== false || strpos($tin, "OBT") !== false || strpos($tin, "ML") !== false || strpos($tin, "PL") !== false || strpos($tin, "No IN") !== false || strpos($tin, "No OUT") !== false || strpos($tin, " N / A ") !== false || strpos($tin, "N / A") !== false || strpos($tin, "N/A") !== false )
                                  {
                                    $arr[$i] = $tin; $i++;
                                  }
                                  else
                                  {
                                    $arr[$i] = Carbon::parse($tin,'Asia/Manila')->format('h:i:s A'); $i++;
                                  }
                                  



                                  // -------- TIME OUT -------------
                                   $tout = strip_tags($key->timeOUT);

                                  if ( strpos($tout, "SL") !== false || strpos($tout, "VL") !== false || strpos($tout, "VTO") !== false || strpos($tout, "RD") !== false || strpos($tout, "LWOP") !== false || strpos($tout, "OBT") !== false || strpos($tout, "ML") !== false || strpos($tout, "MC") !== false || strpos($tout, "PL") !== false || strpos($tout, "No IN") !== false || strpos($tout, "No OUT") !== false || strpos($tout, " N / A ") !== false  || strpos($tout, "N / A") !== false || strpos($tout, "N/A") !== false)
                                  {
                                    $arr[$i] = $tout; $i++;
                                  }
                                  else
                                  {
                                    $arr[$i] = Carbon::parse($tout,'Asia/Manila')->format('h:i:s A'); $i++;
                                  }

                                  


                                  // -------- WORKED HOURS  -------------
                                  if (strlen($key->hoursWorked) > 5)
                                  {
                                     $wh = strip_tags($key->hoursWorked);

                                     if( strpos($wh,"[") !== false)
                                     {
                                        $cleanWH = explode("[", $wh);
                                        $arr[$i] =  $cleanWH[0]; $i++;

                                     }else if ( strpos($wh, "(")!==false )
                                     {
                                        $cleanWH = explode("(", $wh);
                                        $arr[$i] =  $cleanWH[0]; $i++;

                                     }else
                                     {
                                        $cleanWH = explode(" ", $wh);
                                        $arr[$i] =  $cleanWH[0]; $i++;

                                     }
                                      //$arr[$i] = $wh; $i++;

                                  }else{ 
                                    $arr[$i] = strip_tags($key->hoursWorked); $i++;
                                  }


                                  /*
                                  // -------- ECQ STATUS  -------------
                                  $ecq = collect($ecqStats)->where('biometrics_id',$key->biometrics_id)->where('userID',$key->id);
                                  if (count($ecq) > 0)
                                  {
                                    $arr[$i] = $ecq->first()->ecqStatus; $i++;
                                  }
                                  else
                                  {
                                    ($key->isWFH) ? $arr[$i] = "AHW" : $arr[$i]= "Onsite";
                                    $i++;
                                  }
                                  */
                                  

                                  

                                  // -------- OT BILLABLE HOURS  -------------
                                  $arr[$i] = strip_tags($key->OT_billable); $i++;


                                  // -------- OT approved HOURS  -------------
                                  $arr[$i] = strip_tags($key->OT_approved); $i++;


                                  //--------- OT notes ----------------------
                                  if (!empty($key->OT_id))
                                  {

                                    $allOT = User_OT::where('user_id',$key->id)->where('biometrics_id',$key->biometrics_id)->get();

                                    if (count($allOT) > 1)
                                    {
                                      $s = ""; $e =""; $fh=""; $r=""; $c=1;
                                      foreach ($allOT as $o) 
                                      {
                                        $s .= "[".$c."] ".$o->timeStart." | ";
                                        $e .= "[".$c."] ".$o->timeEnd." | ";

                                        switch ($o->billedType) {
                                          case '1': $otType = "billed"; break;
                                          case '2': $otType = "non-billed"; break;
                                          case '3': $otType = "patch"; break;
                                          default: $otType = "billed"; break;
                                        }

                                        if ($o->isApproved)
                                        {
                                          $fh .= "[".$c."] ".$o->filed_hours." (".$otType.") | ";
                                          

                                        }else{
                                          
                                          $fh .= "**[".$c."] ".$o->filed_hours." ( DENIED ) | ";
                                          

                                        }
                                        $r .= $c.".) ".$o->reason."  | "; $c++;


                                      }


                                      // ------ 'OT Start'::'OT End'::'OT hours'::'OT Reason'
                                      $arr[$i] = $s; $i++;
                                      $arr[$i] = $e; $i++;

                                      $arr[$i] = $fh; $i++;
                                      $arr[$i] = $r; $i++;
                                      $arr[$i] = date('M d, h:i:s A', strtotime($key->created_at)); $i++;
                                      
                                      


                                    }else
                                    {

                                      $deets = User_OT::find($key->OT_id);
                                      if (is_object($deets))
                                      {

                                        // ------ 'OT Start'::'OT End'

                                        $arr[$i] = $deets->timeStart; $i++;
                                        $arr[$i] = $deets->timeEnd; $i++;

                                        switch ($deets->billedType) {
                                          case '1': $otType = "billed"; break;
                                          case '2': $otType = "non-billed"; break;
                                          case '3': $otType = "patch"; break;
                                          default: $otType = "billed"; break;
                                        }


                                        // ---- ::'OT hours'::'OT Reason'
                                        if ($deets->isApproved)
                                        {
                                          $arr[$i] = $deets->filed_hours; $i++;// ( ".$otType." )";
                                          //$arr[$i] = "ap";//$deets->reason; $i++;

                                        }else{
                                          $arr[$i] = "DENIED";$i++; // "** ".$deets->filed_hours." ( DENIED )"; 
                                          //$arr[$i] = "den"; //$deets->reason; $i++;

                                        }

                                        $tout2 = strip_tags($key->timeOUT);

                                        if( strpos($tout2, "RD") !== false ){ $arr[$i] = "0"; $i++; $arr[$i] = date('M d, h:i:s A', strtotime($key->created_at)); $i++; }
                                        else { $arr[$i] = $deets->reason; $i++; $arr[$i] = date('M d, h:i:s A', strtotime($key->created_at)); $i++; }//  $i++; }$key->OT_id."_OTID_from_isObject(deets)&allOTcount<1"

                                        

                                      }
                                      else
                                      {
                                        $arr[$i] = "n/a"; $i++;
                                        $arr[$i] = "n/a"; $i++;
                                        $arr[$i] = "n/a"; $i++;
                                        $arr[$i] = "n/a"; $i++;
                                        $arr[$i] = " "; $i++;
                                        $arr[$i] = date('M d, h:i:s A', strtotime($key->created_at)); $i++;

                                      }

                                      

                                    }

                                    
                                    

                                  }else{
                                    $arr[$i] = "-"; $i++;
                                    $arr[$i] = "-"; $i++;
                                    $arr[$i] = "-"; $i++;
                                    $arr[$i] = "-"; $i++;//waley blank lahat: ".$key->OT_id
                                    //$arr[$i] = " "; $i++;
                                    $arr[$i] = date('M d, h:i:s A', strtotime($key->created_at)); $i++;


                                   

                                    
                                  }

                                  //$arr[$i] = "-"; $i++;

                                  //reset
                                  $sheet->appendRow($arr);

                                  if($startrow%2)
                                  {
                                    
                                    $sheet->cells('A'.$startrow.':P'.$startrow, function($cells) {

                                        // call cell manipulation methods
                                        $cells->setBackground('#d8dcf1'); 
                                        $cells->setAlignment('left');  

                                    });

                                  }
                                  else
                                  {
                                    $sheet->cells('A'.$startrow.':P'.$startrow, function($cells) {

                                        // call cell manipulation methods
                                        $cells->setBackground('#ffffff'); 
                                        $cells->setAlignment('left');  

                                    });

                                  }
                                  $startrow++;
                                  $i=0;
                                }

                              }else{
                                

                                $arr[$i] = " - "; $i++;
                                $arr[$i] = " - "; $i++;
                                $arr[$i] = " <unverified> "; $i++;
                                $arr[$i] = " <unverified> "; $i++;
                                $arr[$i] = " <unverified> "; $i++;
                                $arr[$i] = " <unverified> "; $i++; // ** get the sched here
                                $arr[$i] = " <unverified> "; $i++;
                                $arr[$i] = " <unverified> "; $i++;
                                $arr[$i] = " <unverified> "; $i++;
                                $arr[$i] = " <unverified> "; $i++;
                                $arr[$i] = " <unverified> "; $i++;
                                $arr[$i] = " <unverified> "; $i++;
                                $arr[$i] = " <unverified> "; $i++;
                                $arr[$i] = " <unverified> "; $i++;
                                
                               

                                $sheet->appendRow($arr);

                              }

                              

                              
                            

                              

                            }//end foreach employee

                            $lastrow= $sheet->getHighestRow(); 


                            $sheet->getStyle('A4:P'.$lastrow)->getAlignment()->setWrapText(true); 
                            $sheet->setBorder('A4:P'.$lastrow, 1);

                            
                            //****** for SIGNATURE
                        


                            
                          });//end sheet1

                          //$payday->addDay();

                        //} while ( $payday->format('Y-m-d') <= $cutoffEnd->format('Y-m-d') );

                      }
                      elseif($reportType == 'trainees')
                      {
                        
                        $excel->setTitle($cutoffStart->format('Y-m-d').' to '. $cutoffEnd->format('Y-m-d').'_Trainees DTR Sheet');

                        // Chain the setters
                        $excel->setCreator('Programming Team')
                              ->setCompany('OpenAccess');

                        // Call them separately
                        $excel->setDescription($description);

                        $payday = $cutoffStart;

                        $excel->sheet($payday->format('M d')."_".substr($payday->format('l'), 0,3), function($sheet) use ($program,$pname, $allDTR, $allDTRs, $ecqStats, $cutoffStart, $cutoffEnd, $headers,$payday)
                          {

                            //12 headers
                            $header1 = ['Open Access BPO','','','','','','','','','','','','',''];
                            $header1b = ['Daily Time Record','','','','','','','','','','','','',''];
                            $header2 = [$cutoffStart->format('D, m/d/Y')." - ". $cutoffEnd->format('D, m/d/Y') ,'Status: ','All Trainees','','','','','','','','','','','','',''];
                            $header2b = ['','','','','','','','','','','','','',''];

                            
                            // Set width for a single column
                            //$sheet->setWidth('A', 35);

                            $sheet->setFontSize(12);
                            $sheet->setOrientation("landscape");



                            $sheet->appendRow($header1);
                            $sheet->appendRow($header1b);
                            $sheet->appendRow($header2);
                            $sheet->appendRow($header2b);

                            $sheet->cells('A1:P3', function($cells) {

                                // call cell manipulation methods
                                $cells->setBackground('##1a8fcb');
                                $cells->setFontColor('#ffffff');
                                $cells->setFontSize(13);
                                

                            });
                            $sheet->cells('A1', function($cells) {

                                $cells->setFontSize(14);
                                $cells->setFontWeight('bold');

                            });

                          
                           
                            
                            $sheet->appendRow($headers);

                            $sheet->row(5, function($row) {
                                // Set font size
                                $row->setFontSize(13);
                                $row->setBackground('#dedede');
                                //$row->setFontWeight('bold');

                              });
                            // Set height for a single row
                            //$sheet->setHeight(2, 80);
                            //$sheet->setHeight(3, 50);        

                            $arr = [];
                            $startrow = 6;

                            foreach($allDTR as $employeeDTR)
                            {
                              $i = 0;
                              //$dData = collect($employeeDTR)->sortBy('productionDate')->where('productionDate',$payday->format('Y-m-d'));
                              $dData = collect($allDTRs)->where('id',$employeeDTR->first()->id)->sortBy('productionDate');

                              if (count($dData) > 0)
                              {

                                //'Employee Code'::'Formal Name'::'Date'::'Day'::
                                // Time IN'::'Time OUT'::'Hours':: 'OT billable'::'OT Approved'::'OT Start'::'OT End'::'OT hours'::'OT Reason'

                                foreach ($dData as $key) 
                                {
                                  // -------- ACCESS CODE -------------
                                  $arr[$i] = strtoupper($key->traineeCode); $i++;

                                  // -------- FORMAL NAME -------------
                                  $arr[$i] = strtoupper($key->lastname).", ".strtoupper($key->firstname)." ".strtoupper($key->middlename); $i++;
                                  

                                  // -------- DATE -------------
                                  // ** Production Date
                                  // check if there's holiday
                                  $holiday = Holiday::where('holidate',$key->productionDate)->get();

                                  (count($holiday) > 0) ? $hday=$holiday->first()->name : $hday = "";

                                  //$arr[$i] = $payday->format('M d D')." ". $hday; $i++;
                                  $arr[$i] = date('m/d/y',strtotime($key->productionDate)); $i++; //; $payday->format('m/d/y')." ". $hday; $i++;

                                  // -------- DAY -------------
                                  $arr[$i] = date('D',strtotime($key->productionDate))." ". $hday; $i++;


                                  // -------- TIME IN -------------
                                  $tin = strip_tags($key->timeIN);

                                  if ( strpos($tin, "SL") !== false || strpos($tin, "VL") !== false || strpos($tin, "VTO") !== false || strpos($tin, "RD") !== false || strpos($tin, "LWOP") !== false || strpos($tin, "OBT") !== false || strpos($tin, "ML") !== false || strpos($tin, "PL") !== false || strpos($tin, "No IN") !== false || strpos($tin, "No OUT") !== false || strpos($tin, " N / A ") !== false || strpos($tin, "N / A") !== false || strpos($tin, "N/A") !== false )
                                  {
                                    $arr[$i] = $tin; $i++;
                                  }
                                  else
                                  {
                                    $arr[$i] = Carbon::parse($tin,'Asia/Manila')->format('h:i:s A'); $i++;
                                  }
                                  



                                  // -------- TIME OUT -------------
                                   $tout = strip_tags($key->timeOUT);

                                  if ( strpos($tout, "SL") !== false || strpos($tout, "VL") !== false || strpos($tout, "VTO") !== false || strpos($tout, "RD") !== false || strpos($tout, "LWOP") !== false || strpos($tout, "OBT") !== false || strpos($tout, "ML") !== false || strpos($tout, "MC") !== false || strpos($tout, "PL") !== false || strpos($tout, "No IN") !== false || strpos($tout, "No OUT") !== false || strpos($tout, " N / A ") !== false  || strpos($tout, "N / A") !== false || strpos($tout, "N/A") !== false)
                                  {
                                    $arr[$i] = $tout; $i++;
                                  }
                                  else
                                  {
                                    $arr[$i] = Carbon::parse($tout,'Asia/Manila')->format('h:i:s A'); $i++;
                                  }

                                  


                                  // -------- WORKED HOURS  -------------
                                  if (strlen($key->hoursWorked) > 5)
                                  {
                                     $wh = strip_tags($key->hoursWorked);

                                     if( strpos($wh,"[") !== false)
                                     {
                                        $cleanWH = explode("[", $wh);
                                        $arr[$i] =  $cleanWH[0]; $i++;

                                     }else if ( strpos($wh, "(")!==false )
                                     {
                                        $cleanWH = explode("(", $wh);
                                        $arr[$i] =  $cleanWH[0]; $i++;

                                     }else
                                     {
                                        $cleanWH = explode(" ", $wh);
                                        $arr[$i] =  $cleanWH[0]; $i++;

                                     }
                                      //$arr[$i] = $wh; $i++;

                                  }else{ 
                                    $arr[$i] = strip_tags($key->hoursWorked); $i++;
                                  }


                                  

                                  // -------- OT BILLABLE HOURS  -------------
                                  $arr[$i] = strip_tags($key->OT_billable); $i++;


                                  // -------- OT approved HOURS  -------------
                                  $arr[$i] = strip_tags($key->OT_approved); $i++;


                                  //--------- OT notes ----------------------
                                  if (!empty($key->OT_id))
                                  {

                                    $allOT = User_OT::where('user_id',$key->id)->where('biometrics_id',$key->biometrics_id)->get();

                                    if (count($allOT) > 1)
                                    {
                                      $s = ""; $e =""; $fh=""; $r=""; $c=1;
                                      foreach ($allOT as $o) 
                                      {
                                        $s .= "[".$c."] ".$o->timeStart." | ";
                                        $e .= "[".$c."] ".$o->timeEnd." | ";

                                        switch ($o->billedType) {
                                          case '1': $otType = "billed"; break;
                                          case '2': $otType = "non-billed"; break;
                                          case '3': $otType = "patch"; break;
                                          default: $otType = "billed"; break;
                                        }

                                        if ($o->isApproved)
                                        {
                                          $fh .= "[".$c."] ".$o->filed_hours." (".$otType.") | ";
                                          

                                        }else{
                                          
                                          $fh .= "**[".$c."] ".$o->filed_hours." ( DENIED ) | ";
                                          

                                        }
                                        $r .= $c.".) ".$o->reason."  | "; $c++;


                                      }


                                      // ------ 'OT Start'::'OT End'::'OT hours'::'OT Reason'
                                      $arr[$i] = $s; $i++;
                                      $arr[$i] = $e; $i++;

                                      $arr[$i] = $fh; $i++;
                                      $arr[$i] = $r; $i++;
                                      $arr[$i] = date('M d, h:i:s A', strtotime($key->created_at)); $i++;
                                      
                                      


                                    }else
                                    {

                                      $deets = User_OT::find($key->OT_id);
                                      if (is_object($deets))
                                      {

                                        // ------ 'OT Start'::'OT End'

                                        $arr[$i] = $deets->timeStart; $i++;
                                        $arr[$i] = $deets->timeEnd; $i++;

                                        switch ($deets->billedType) {
                                          case '1': $otType = "billed"; break;
                                          case '2': $otType = "non-billed"; break;
                                          case '3': $otType = "patch"; break;
                                          default: $otType = "billed"; break;
                                        }


                                        // ---- ::'OT hours'::'OT Reason'
                                        if ($deets->isApproved)
                                        {
                                          $arr[$i] = $deets->filed_hours; $i++;// ( ".$otType." )";
                                          //$arr[$i] = "ap";//$deets->reason; $i++;

                                        }else{
                                          $arr[$i] = "DENIED";$i++; // "** ".$deets->filed_hours." ( DENIED )"; 
                                          //$arr[$i] = "den"; //$deets->reason; $i++;

                                        }

                                        $tout2 = strip_tags($key->timeOUT);

                                        if( strpos($tout2, "RD") !== false ){ $arr[$i] = "0"; $i++; $arr[$i] = date('M d, h:i:s A', strtotime($key->created_at)); $i++; }
                                        else { $arr[$i] = $deets->reason; $i++; $arr[$i] = date('M d, h:i:s A', strtotime($key->created_at)); $i++; }//  $i++; }$key->OT_id."_OTID_from_isObject(deets)&allOTcount<1"

                                        

                                      }
                                      else
                                      {
                                        $arr[$i] = "n/a"; $i++;
                                        $arr[$i] = "n/a"; $i++;
                                        $arr[$i] = "n/a"; $i++;
                                        $arr[$i] = "n/a"; $i++;
                                        $arr[$i] = " "; $i++;
                                        $arr[$i] = Carbon::parse($key->created_at, 'Asia/Manila')->format('M d, h:i:s A');
                                        //, strtotime($key->created_at)); 
                                        $i++;

                                      }

                                      

                                    }

                                    
                                    

                                  }else{
                                    $arr[$i] = "-"; $i++;
                                    $arr[$i] = "-"; $i++;
                                    $arr[$i] = "-"; $i++;
                                    $arr[$i] = "-"; $i++;//waley blank lahat: ".$key->OT_id
                                    //$arr[$i] = " "; $i++;
                                    $arr[$i] = Carbon::parse($key->created_at, 'Asia/Manila')->format('M d, h:i:s A'); //date('M d, h:i:s A', strtotime($key->created_at)); 
                                    $i++;


                                   

                                    
                                  }

                                  //$arr[$i] = "-"; $i++;

                                  //reset
                                  $sheet->appendRow($arr);

                                  if($startrow%2)
                                  {
                                    
                                    $sheet->cells('A'.$startrow.':P'.$startrow, function($cells) {

                                        // call cell manipulation methods
                                        $cells->setBackground('#d8dcf1'); 
                                        $cells->setAlignment('left');  

                                    });

                                  }
                                  else
                                  {
                                    $sheet->cells('A'.$startrow.':P'.$startrow, function($cells) {

                                        // call cell manipulation methods
                                        $cells->setBackground('#ffffff'); 
                                        $cells->setAlignment('left');  

                                    });

                                  }
                                  $startrow++;
                                  $i=0;
                                }

                              }else{
                                

                                $arr[$i] = " - "; $i++;
                                $arr[$i] = " - "; $i++;
                                $arr[$i] = " <unverified> "; $i++;
                                $arr[$i] = " <unverified> "; $i++;
                                $arr[$i] = " <unverified> "; $i++;
                                $arr[$i] = " <unverified> "; $i++; // ** get the sched here
                                $arr[$i] = " <unverified> "; $i++;
                                $arr[$i] = " <unverified> "; $i++;
                                $arr[$i] = " <unverified> "; $i++;
                                $arr[$i] = " <unverified> "; $i++;
                                $arr[$i] = " <unverified> "; $i++;
                                $arr[$i] = " <unverified> "; $i++;
                                $arr[$i] = " <unverified> "; $i++;
                                $arr[$i] = " <unverified> "; $i++;
                                
                               

                                $sheet->appendRow($arr);

                              }

                              

                              
                            

                              

                            }//end foreach employee

                            $lastrow= $sheet->getHighestRow(); 


                            $sheet->getStyle('A4:P'.$lastrow)->getAlignment()->setWrapText(true); 
                            $sheet->setBorder('A4:P'.$lastrow, 1);

                            
                            //****** for SIGNATURE
                        


                            
                          });//end sheet1

                          

                      }

                      
                      else
                      {
                        $excel->setTitle($cutoffStart->format('Y-m-d').' to '. $cutoffEnd->format('Y-m-d').'_'.$pname.' DTR Sheet');

                        // Chain the setters
                        $excel->setCreator('Programming Team')
                              ->setCompany('OpenAccess');

                        // Call them separately
                        $excel->setDescription($description);

                        $payday = $cutoffStart;
                        do
                        {

                          $excel->sheet($payday->format('M d')."_".substr($payday->format('l'), 0,3), function($sheet) use ($program,$pname, $allDTR, $cutoffStart, $cutoffEnd, $headers,$payday)
                          {

                            $header1 = ['Open Access BPO | Daily Time Record','','','','','','','','','','','','','','',''];
                            $header2 = [$cutoffStart->format('D, m/d/Y'),'Program: ',strtoupper($pname),'','','','','','','','','','','','',''];

                            
                            // Set width for a single column
                            //$sheet->setWidth('A', 35);

                            $sheet->setFontSize(20);
                            $sheet->setOrientation("landscape");



                            $sheet->appendRow($header1);
                            $sheet->appendRow($header2);
                            $sheet->cells('A1:P2', function($cells) {

                                // call cell manipulation methods
                                $cells->setBackground('##1a8fcb');
                                $cells->setFontColor('#ffffff');
                                $cells->setFontSize(40);
                                $cells->setFontWeight('bold');

                            });


                            $sheet->row(2, function($cells) {

                                // call cell manipulation methods
                                
                                $cells->setFontColor('#ffffff');
                                $cells->setFontSize(40);
                                $cells->setFontWeight('bold');

                            });

                           
                            
                            $sheet->appendRow($headers);

                            $sheet->row(3, function($row) {
                                // Set font size
                                $row->setFontSize(18);
                                $row->setFontWeight('bold');

                              });
                            // Set height for a single row
                            $sheet->setHeight(2, 80);
                            $sheet->setHeight(3, 50);

                            // Freeze the first column
                            //$sheet->freezeFirstColumn();

                            // $sheet->setAutoSize(array(
                            //     'A',
                            // ));

                            // Set width for a single column
                            $sheet->setWidth('A', 40);
                            $sheet->setWidth('D', 22);
                            $sheet->setWidth('F', 12);
                            $sheet->setWidth('G', 12);
                            $sheet->setWidth('J', 12);
                            $sheet->setWidth('K', 12);
                            $sheet->setWidth('L', 9);
                            $sheet->setWidth('P', 9);

                            

                            $arr = [];


                            foreach($allDTR as $employeeDTR)
                            {
                              $i = 0;
                              $dData = collect($employeeDTR)->where('productionDate',$payday->format('Y-m-d'));

                              if (count($dData) > 0)
                              {
                                $arr[$i] = strtoupper($dData->first()->lastname).", ".strtoupper($dData->first()->firstname)." ".strtoupper($dData->first()->middlename); $i++;
                                $arr[$i] = strtoupper($dData->first()->leaderFname)." ".strtoupper($dData->first()->leaderLname); $i++;

                                // ** Production Date
                                // check if there's holiday
                                $holiday = Holiday::where('holidate',$payday->format('Y-m-d'))->get();

                                (count($holiday) > 0) ? $hday=$holiday->first()->name : $hday = "";

                                $arr[$i] = $payday->format('M d D')." ". $hday; $i++;
                                $arr[$i] = strip_tags($dData->first()->workshift); $i++; // ** get the sched here
                                

                                //*** CWS
                                if (!empty($dData->first()->isCWS_id)){
                                  $deets = User_CWS::find($dData->first()->isCWS_id);

                                  if(is_object($deets))
                                  {
                                    $arr[$i] = ' (old sched: '.$deets->timeStart_old. ' - '.$deets->timeEnd_old.' ) | '.$deets->notes; $i++;

                                  }
                                  else
                                  {
                                    $arr[$i] = ' n/a '; $i++;
                                  }

                                  

                                }else{
                                  $arr[$i] = "-"; $i++;
                                }


                                $arr[$i] = strip_tags($dData->first()->timeIN); $i++;
                                $arr[$i] = strip_tags($dData->first()->timeOUT); $i++;

                                //*** DTRP IN
                                if (!empty($dData->first()->isDTRP_in)){
                                  $deets = User_DTRP::find($dData->first()->isDTRP_in);

                                  $arr[$i] = $deets->notes; $i++;

                                }else{
                                  $arr[$i] = "-"; $i++;
                                }

                                //*** DTRP OUT
                                if (!empty($dData->first()->isDTRP_out)){
                                  $deets = User_DTRP::find($dData->first()->isDTRP_out);

                                  $arr[$i] = $deets->notes; $i++;

                                }else{
                                  $arr[$i] = "-"; $i++;
                                }


                                //*** OT
                                if (!empty($dData->first()->OT_id)){

                                  $allOT = User_OT::where('user_id',$dData->first()->id)->where('biometrics_id',$dData->first()->biometrics_id)->get();

                                  if (count($allOT) > 1)
                                  {
                                    $s = ""; $e =""; $fh=""; $r=""; $c=1;
                                    foreach ($allOT as $o) 
                                    {
                                      $s .= "[".$c."] ".$o->timeStart." | ";
                                      $e .= "[".$c."] ".$o->timeEnd." | ";

                                      switch ($o->billedType) {
                                        case '1': $otType = "billed"; break;
                                        case '2': $otType = "non-billed"; break;
                                        case '3': $otType = "patch"; break;
                                        default: $otType = "billed"; break;
                                      }

                                      if ($o->isApproved)
                                      {
                                        $fh .= "[".$c."] ".$o->filed_hours." (".$otType.") | ";
                                        

                                      }else{
                                        
                                        $fh .= "**[".$c."] ".$o->filed_hours." ( DENIED ) | ";
                                        

                                      }
                                      $r .= $c.".) ".$o->reason."  | "; $c++;


                                    }


                                    $arr[$i] = $s; $i++;
                                    $arr[$i] = $e; $i++;

                                    $arr[$i] = $fh; $i++;
                                    $arr[$i] = $r; $i++;
                                    
                                    


                                  }else
                                  {

                                    $deets = User_OT::find($dData->first()->OT_id);
                                    if (is_object($deets))
                                    {
                                      $arr[$i] = $deets->timeStart; $i++;
                                      $arr[$i] = $deets->timeEnd; $i++;
                                      switch ($deets->billedType) {
                                        case '1': $otType = "billed"; break;
                                        case '2': $otType = "non-billed"; break;
                                        case '3': $otType = "patch"; break;
                                        default: $otType = "billed"; break;
                                      }
                                      if ($deets->isApproved)
                                      {
                                        $arr[$i] = $deets->filed_hours." ( ".$otType." )"; $i++;
                                        $arr[$i] = $deets->reason; $i++;

                                      }else{
                                        $arr[$i] = "** ".$deets->filed_hours." ( DENIED )"; $i++;
                                        $arr[$i] = $deets->reason; $i++;

                                      }

                                    }
                                    else
                                    {
                                      $arr[$i] = "n/a"; $i++;
                                      $arr[$i] = "n/a"; $i++;
                                      
                                      
                                        $arr[$i] = "n/a"; $i++;
                                        $arr[$i] = "n/a"; $i++;

                                      

                                    }

                                    

                                  }

                                  
                                  

                                }else{
                                  $arr[$i] = "-"; $i++;
                                  $arr[$i] = "-"; $i++;
                                  $arr[$i] = "-"; $i++;
                                  $arr[$i] = "-"; $i++;
                                }
                                
                                
                                
                                
                                
                                
                                

                                //if marami contents ang hours worked, may leave details yun
                                if (strlen($dData->first()->hoursWorked) > 5)
                                {

                                  //$arr[$i] = strip_tags($dData->first()->hoursWorked); $i++;
                                  if (empty($dData->first()->leaveType)){
                                    $arr[$i] =" - "; $i++;
                                  } else
                                  {
                                    

                                    //add in kung half leave
                                    if (strpos($dData->first()->hoursWorked, "1st") !== false ) 
                                      $arr[$i] =$dData->first()->leaveType."\n"."1st half of Shift";
                                    else if(strpos($dData->first()->hoursWorked, "2nd") !== false ) 
                                      $arr[$i] =$dData->first()->leaveType."\n"."2nd half of Shift";
                                    else
                                      $arr[$i] =$dData->first()->leaveType;
                                    $i++;
                                  }

                                  //then we look for its detail
                                  if ($dData->first()->leaveType == "SL") 
                                  //( strpos(strtoupper($dData->first()->leaveType), "SICK") !== false )
                                  {
                                    if (empty($dData->first()->leave_id))
                                    {
                                      $deets = User_SL::where('user_id',$dData->first()->id)->where('leaveStart','>=', $dData->first()->productionDate)->first();

                                    } else {
                                      $deets = User_SL::find($dData->first()->leave_id);
                                    }
                                    
                                    // 

                                    $arr[$i] = $deets->notes; $i++; 
                                    $arr[$i] = Carbon::parse($dData->first()->updated_at,'Asia/Manila')->format('Y-m-d H:i:s'); $i++;

                                  } 
                                  elseif ($dData->first()->leaveType == "VL")
                                  {
                                     if (empty($dData->first()->leave_id))
                                      {
                                        $deets = User_VL::where('user_id',$dData->first()->id)->where('leaveStart','>=', $dData->first()->productionDate)->first();

                                      } else {
                                        $deets = User_VL::find($dData->first()->leave_id);
                                      }
                                      
                                      // 

                                      $arr[$i] = $deets->notes; $i++; 
                                      $arr[$i] = Carbon::parse($dData->first()->updated_at,'Asia/Manila')->format('Y-m-d H:i:s'); $i++;

                                  } elseif ($dData->first()->leaveType == "LWOP")
                                  {
                                    if (empty($dData->first()->leave_id))
                                    {
                                      $deets = User_LWOP::where('user_id',$dData->first()->id)->where('leaveStart','>=', $dData->first()->productionDate)->first();

                                    } else {
                                      $deets = User_LWOP::find($dData->first()->leave_id);
                                    }
                                    $arr[$i] = $deets->notes; $i++; 
                                    $arr[$i] = Carbon::parse($dData->first()->updated_at,'Asia/Manila')->format('Y-m-d H:i:s'); $i++;

                                  } elseif ($dData->first()->leaveType == "OBT")
                                  { 
                                    if (empty($dData->first()->leave_id))
                                    {
                                      $deets = User_OBT::where('user_id',$dData->first()->id)->where('leaveStart','>=', $dData->first()->productionDate)->first();

                                    } else {
                                      $deets = User_OBT::find($dData->first()->leave_id);
                                    }

                                    $arr[$i] = $deets->notes; $i++; 
                                    $arr[$i] = Carbon::parse($dData->first()->updated_at,'Asia/Manila')->format('Y-m-d H:i:s'); $i++;
                                  
                                  } elseif ($dData->first()->leaveType == "PL" || $dData->first()->leaveType == "ML"|| $dData->first()->leaveType == "MC" || $dData->first()->leaveType == "SPL")
                                  { 
                                    if (empty($dData->first()->leave_id))
                                    {
                                      $deets = User_Familyleave::where('user_id',$dData->first()->id)->where('leaveStart','>=', $dData->first()->productionDate)->first();

                                    } else {
                                      $deets = User_Familyleave::find($dData->first()->leave_id);
                                    }
                                    
                                    // 

                                    $arr[$i] = $deets->notes; $i++; 
                                    $arr[$i] = Carbon::parse($dData->first()->updated_at,'Asia/Manila')->format('Y-m-d H:i:s'); $i++;
                                  
                                  }
                                  else {  $arr[$i] = "-"; $i++; $arr[$i] = Carbon::parse($dData->first()->updated_at,'Asia/Manila')->format('Y-m-d H:i:s') ;} // $i++; $arr[$i] = "-"; $i++;
                                   

                                }else {
                                  $arr[$i] = "-"; $i++; $arr[$i] = "-"; $i++;
                                  $arr[$i] = Carbon::parse($dData->first()->updated_at,'Asia/Manila')->format('Y-m-d H:i:s'); $i++;

                                }


                              }else{
                                $arr[$i] = strtoupper($employeeDTR->first()->lastname).", ".strtoupper($employeeDTR->first()->firstname)." ".strtoupper($employeeDTR->first()->middlename) ; $i++;
                                $arr[$i] = strtoupper($employeeDTR->first()->leaderFname) ." ". strtoupper($employeeDTR->first()->leaderLname); $i++;
                                $arr[$i] = $payday->format('M d D'); $i++;
                                $arr[$i] = " <unverified> "; $i++;
                                $arr[$i] = " <unverified> "; $i++; // ** get the sched here
                                $arr[$i] = " <unverified> "; $i++;
                                $arr[$i] = " <unverified> "; $i++;
                                $arr[$i] = " <unverified> "; $i++;
                                $arr[$i] = " <unverified> "; $i++;
                                $arr[$i] = " <unverified> "; $i++;
                                $arr[$i] = " <unverified> "; $i++;
                                $arr[$i] = " <unverified> "; $i++;
                                $arr[$i] = " <unverified> "; $i++;
                                $arr[$i] = " <unverified> "; $i++;
                                $arr[$i] = " <unverified> "; $i++;
                                $arr[$i] = " <unverified> "; $i++;

                              }

                              

                              $sheet->appendRow($arr);
                              //$sheet->getStyle('A4:A200')->getAlignment()->setWrapText(true);
                              //$sheet->setBorder('A1:F10', 'thin');
                              

                              

                            }//end foreach employee

                            $lastrow= $sheet->getHighestRow(); 


                            $sheet->getStyle('A4:P'.$lastrow)->getAlignment()->setWrapText(true); 
                            //$sheet->getStyle('E4:P'.$lastrow)->getAlignment()->setWrapText(true); 
                            //$sheet->getStyle('J4:M'.$lastrow)->getAlignment()->setWrapText(true); 
                            //$sheet->getStyle('O4:P'.$lastrow)->getAlignment()->setWrapText(true); 
                            $sheet->setBorder('A4:P'.$lastrow, 'thin');

                            
                            //****** for SIGNATURE
                            $m = "D".($lastrow+5).":E".($lastrow+5);
                            $sheet->mergeCells($m);
                            $sheet->cell('D'.($lastrow+5), function($cell) {

                              $cell->setValue('___________________________________________');
                              $cell->setAlignment('center');
                              $cell->setBorder('solid');

                            });

                            $m = "D".($lastrow+6).":E".($lastrow+6);
                            $sheet->mergeCells($m);
                            $sheet->cell('D'.($lastrow+6), function($cell) {

                              $cell->setValue('OLGA PONCE');
                              $cell->setAlignment('center');
                              $cell->setBorder('solid');
                              $cell->setFontSize(30);

                            });

                            $m = "D".($lastrow+7).":E".($lastrow+7);
                            $sheet->mergeCells($m);
                            $sheet->cell('D'.($lastrow+7), function($cell) {

                              $cell->setValue('Finance Consultant');
                              $cell->setAlignment('center');

                              
                            });


                            $m2 = "A".($lastrow+5).":B".($lastrow+5);
                            $sheet->mergeCells($m2);

                            $sheet->cell('A'.($lastrow+5), function($cell) {

                              $cell->setValue('____________________________________________________________'); 
                              $cell->setAlignment('center');

                            });

                            $m2 = "A".($lastrow+6).":B".($lastrow+6);
                            $sheet->mergeCells($m2);

                            $sheet->cell('A'.($lastrow+6), function($cell) {

                              $cell->setValue(' ');
                            });

                            $m2 = "A".($lastrow+7).":B".($lastrow+7);
                            $sheet->mergeCells($m2);
                            $sheet->cell('A'.($lastrow+7), function($cell) {

                              $cell->setValue('Program Manager (signature over printed name)');
                              $cell->setAlignment('center');
                              $cell->setFontSize(26);

                              
                            });


                            
                          });//end sheet1

                          $payday->addDay();

                        } while ( $payday->format('Y-m-d') <= $cutoffEnd->format('Y-m-d') );

                      }


                            


                      // $lastrow= $excel->getActiveSheet()->getHighestRow(); 
                      // $excel->getActiveSheet()->getStyle('A4:A'.$lastrow)->getAlignment()->setWrapText(true); 
                      // $excel->getActiveSheet()->setBorder('A4:P'.$lastrow, 'thin');

              })->export('xls');return "Download";

      }else
      {

        

        Excel::create("Billable Tracker_".$pname,function($excel) use($program, $pname, $allDTR, $cutoffStart, $cutoffEnd, $headers,$description) 
               {
                      $excel->setTitle($cutoffStart->format('Y-m-d').' to '. $cutoffEnd->format('Y-m-d').'_'.$pname.' DTR Sheet');

                      // Chain the setters
                      $excel->setCreator('Programming Team')
                            ->setCompany('OpenAccess');

                      // Call them separately
                      $excel->setDescription($description);
                      $payday = $cutoffStart;


                      $excel->sheet("DTR Summary", function($sheet) use ($program, $allDTR, $cutoffStart, $cutoffEnd, $headers,$payday)
                        {
                          $header1 = ['Open Access BPO | DTR Summary','','','','','','','','','','','','','','',''];
                          $header2 = [$cutoffStart->format('M d Y')." to ". $cutoffEnd->format('M d Y') ,'Program/Department: ',strtoupper($program->name),'','','','','','','','','','','','',''];

                          $sheet->setFontSize(17);
                          $sheet->appendRow($header1);
                          $sheet->appendRow($header2);
                          $sheet->cells('A1:Z2', function($cells) {

                              // call cell manipulation methods
                              $cells->setBackground('##1a8fcb');
                              $cells->setFontColor('#ffffff');
                              $cells->setFontSize(18);
                              $cells->setFontWeight('bold');

                          });
                          $sheet->row(2, function($cells) {

                              // call cell manipulation methods
                              
                              $cells->setFontColor('#dedede');
                              $cells->setFontSize(18);
                              $cells->setFontWeight('bold');

                          });

                          $header3 = ['','','',''];

                          $headers = ['Employee Name', 'Position','Immediate Head','Program'];

                          $productionDates = [];
                          $ct = 0;
                          
                          $d = Carbon::parse($cutoffStart->format('Y-m-d'),'Asia/Manila');

                          foreach($allDTR as $employeeDTR)
                          {
                            //---- setup headers first
                            $overAllTotal = 0;
                            if ($ct==0)
                            {
                              do
                              {
                                array_push($productionDates, $d->format('Y-m-d'));
                                array_push($headers, $d->format('m/d'));
                                array_push($header3, substr($d->format('l'), 0,3) );
                                $d->addDay();
                              }while($d->format('Y-m-d') <= $cutoffEnd->format('Y-m-d')); //all production dates

                              array_push($headers,"TOTAL");

                              $sheet->appendRow($header3);
                              $sheet->appendRow($headers);
                              $sheet->row(3, function($cells) {
                                $cells->setFontSize(18);
                                $cells->setFontWeight('bold');
                                $cells->setAlignment('center');
                              });
                              $sheet->row(4, function($cells) {
                                $cells->setFontSize(18);
                                $cells->setFontWeight('bold');
                                $cells->setAlignment('center');
                              });
                              $ct++;

                              goto addFirstEmployee;


                            }else
                            {

                              addFirstEmployee:

                              $i = 0;
                              $totalHours = 0;
                              $arr = [];

                              $arr[$i] = $employeeDTR->first()->lastname.", ".$employeeDTR->first()->firstname." ".$employeeDTR->first()->middlename; $i++;
                              $arr[$i] = $employeeDTR->first()->jobTitle; $i++;
                              $arr[$i] = $employeeDTR->first()->leaderFname." ".$employeeDTR->first()->leaderLname; $i++;
                              $arr[$i] = $employeeDTR->first()->program; $i++;

                              foreach ($productionDates as $prodDate) 
                              {
                                 $entry = collect($employeeDTR)->where('productionDate',$prodDate);

                                 if (count($entry) > 0)
                                 {
                                  $e = strip_tags($entry->first()->hoursWorked);
                                  if ( strpos($e, '[') !== false )
                                  {
                                    $x = explode('[', $e);
                                    $totalHours += (float)$x[0];
                                    $overAllTotal += $totalHours;
                                    $arr[$i] = $e; //."_x-".$totalHours; //number_format((float)$x[0], 2, '.', '');
                                  }else
                                  {
                                    if (is_numeric($e)){
                                      $arr[$i] = number_format((float)$e, 2, '.', ''); //."_num-".$totalHours;
                                      $totalHours += (float)$e;
                                      $overAllTotal += $totalHours;
                                    }
                                    else
                                      $arr[$i] = $e; //."_".$totalHours;

                                   
                                  }
                                  
                                 
                                  
                                  $i++;

                                 }else
                                 {
                                  $arr[$i] = '<unverified>'; $i++;
                                 }
                              }

                              $arr[$i]= number_format($totalHours,2);
                              $sheet->appendRow($arr); $ct++;

                             


                            }//end if else not initial header setup
                            

                          }//end foreach employee
                            // Freeze the first column

                          $sheet->setColumnFormat(array(
                            'E' => '0.00','F' => '0.00','G' => '0.00','H' => '0.00','I' => '0.00','J' => '0.00','K' => '0.00','L' => '0.00','M' => '0.00','N' => '0.00','O' => '0.00','P' => '0.00','Q' => '0.00','R' => '0.00','S' => '0.00','T' => '0.00','U' => '0.00'));

                          $sheet->cells("E3:U".($ct+4), function($cells) {
                            $cells->setAlignment('center');
                          });

                          $sheet->freezeFirstColumn();


                        }); //end DTR Summary sheet

                      //***** OT *********
                      $excel->sheet("OT Summary", function($sheet) use ($program, $allDTR, $cutoffStart, $cutoffEnd, $headers,$payday)
                        {
                          $header1 = ['Open Access BPO | DTR Summary','','','','','','','','','','','','','','',''];
                          $header2 = [$cutoffStart->format('M d Y')." to ". $cutoffEnd->format('M d Y') ,'Program/Department: ',strtoupper($program->name),'','','','','','','','','','','','',''];

                          $sheet->setFontSize(17);
                          $sheet->appendRow($header1);
                          $sheet->appendRow($header2);
                          $sheet->cells('A1:CG2', function($cells) {

                              // call cell manipulation methods
                              $cells->setBackground('##1a8fcb');
                              $cells->setFontColor('#ffffff');
                              $cells->setFontSize(18);
                              $cells->setFontWeight('bold');

                          });
                          $sheet->row(2, function($cells) {

                              // call cell manipulation methods
                              
                              $cells->setFontColor('#dedede');
                              $cells->setFontSize(18);
                              $cells->setFontWeight('bold');

                          });

                          $header3 = ['','','',''];

                          $headers = ['Employee Name', 'Position','Immediate Head','Program'];

                          $productionDates = [];
                          $ct = 0;
                          
                          $d = Carbon::parse($cutoffStart->format('Y-m-d'),'Asia/Manila');

                          foreach($allDTR as $employeeDTR)
                          {
                            //---- setup headers first
                            $overAllTotal = 0;
                            if ($ct==0)
                            {
                              do
                              {
                                array_push($productionDates, $d->format('Y-m-d'));
                                array_push($headers, $d->format('m/d'));
                                
                                array_push($header3, $d->format('l') );
                                array_push($header3, ' ' );array_push($header3, ' ' );array_push($header3, ' ' );array_push($header3, ' ' );
                                array_push($headers, 'Start time');
                                array_push($headers, 'End time');
                                array_push($headers, 'Type');
                                array_push($headers, 'Remarks');
                                $d->addDay();
                              }while($d->format('Y-m-d') <= $cutoffEnd->format('Y-m-d')); //all production dates

                              array_push($headers,"TOTAL");

                              $sheet->appendRow($header3);
                              $sheet->appendRow($headers);
                              $sheet->row(3, function($cells) {
                                $cells->setFontSize(17);
                                $cells->setFontWeight('bold');
                                $cells->setAlignment('center');
                              });
                              $sheet->row(4, function($cells) {
                                $cells->setFontSize(18);
                                $cells->setFontWeight('bold');
                                $cells->setAlignment('center');
                              });
                              $ct++;

                              goto addFirstEmployee;


                            }else
                            {

                              addFirstEmployee:

                              $i = 0;
                              $totalHours = 0;
                              $arr = [];

                              $arr[$i] = $employeeDTR->first()->lastname.", ".$employeeDTR->first()->firstname." ".$employeeDTR->first()->middlename; $i++;
                              $arr[$i] = $employeeDTR->first()->jobTitle; $i++;
                              $arr[$i] = $employeeDTR->first()->leaderFname." ".$employeeDTR->first()->leaderLname; $i++;
                              $arr[$i] = $employeeDTR->first()->program; $i++;

                              foreach ($productionDates as $prodDate) 
                              {
                                 $entry = collect($employeeDTR)->where('productionDate',$prodDate);

                                 if (count($entry) > 0)
                                 {
                                  $e = strip_tags($entry->first()->OT_approved);
                                  $o = $entry->first()->OT_id;
                                  if ( !empty($o) )
                                  {
                                    $ot = User_OT::find($entry->first()->OT_id);

                                    switch ($ot->billedType) {
                                      case '1':{ $otType = "Billed"; }break;
                                      case '2':{ $otType = "Non-Billed"; }break;
                                      case '3':{ $otType = "Patch"; }break;
                                      default:{ $otType = "Billed"; }break;
                                    }
                                    
                                    $totalHours += (float)$e;
                                    $overAllTotal += $totalHours;

                                    $arr[$i] = (float)$e; $i++; 
                                    $arr[$i] = $ot->timeStart; $i++;
                                    $arr[$i] = $ot->timeEnd; $i++;
                                    $arr[$i] = $otType; $i++;
                                    $arr[$i] = $ot->reason; $i++;

                                  }else
                                  {
                                    $arr[$i] = 0.0; $i++; 
                                    $arr[$i] = 0.0; $i++; 
                                    $arr[$i] = 0.0; $i++; 
                                    $arr[$i] = 0.0; $i++; 
                                    $arr[$i] = 0.0; $i++; 
                                   
                                  }
                                  
                                 
                                  
                                  

                                 }else
                                 {
                                  $arr[$i] = 0.0; $i++; 
                                    $arr[$i] = 0.0; $i++; 
                                    $arr[$i] = 0.0; $i++; 
                                    $arr[$i] = 0.0; $i++; 
                                    $arr[$i] = 0.0; $i++; 
                                 }
                              }

                              $arr[$i]= number_format($totalHours,2);
                              $sheet->appendRow($arr); $ct++;

                             


                            }//end if else not initial header setup
                            

                          }//end foreach employee
                            // Freeze the first column

                          // $sheet->setColumnFormat(array(
                          //   'E' => '0.00','F' => '0.00','G' => '0.00','H' => '0.00','I' => '0.00','J' => '0.00','K' => '0.00','L' => '0.00','M' => '0.00','N' => '0.00','O' => '0.00','P' => '0.00','Q' => '0.00','R' => '0.00','S' => '0.00','T' => '0.00','U' => '0.00'));

                          $sheet->cells("E3:CG".($ct+4), function($cells) {
                            $cells->setAlignment('center');
                          });

                          $sheet->freezeFirstColumn();


                        }); //end DTR Summary sheet

                      
                      //***** TARDINESS *********
                      $excel->sheet("Undertime", function($sheet) use ($program, $allDTR, $cutoffStart, $cutoffEnd, $headers,$payday)
                        {
                          $header1 = ['Open Access BPO | DTR Summary','','','','','','','','','','','','','','',''];
                          $header2 = [$cutoffStart->format('M d Y')." to ". $cutoffEnd->format('M d Y') ,'Program/Department: ',strtoupper($program->name),'','','','','','','','','','','','',''];

                          $sheet->setFontSize(17);
                          $sheet->appendRow($header1);
                          $sheet->appendRow($header2);




                          $sheet->cells('A1:P2', function($cells) {

                              // call cell manipulation methods
                              $cells->setBackground('##1a8fcb');
                              $cells->setFontColor('#ffffff');
                              $cells->setFontSize(18);
                              $cells->setFontWeight('bold');

                          });
                          $sheet->row(2, function($cells) {

                              // call cell manipulation methods
                              
                              $cells->setFontColor('#dedede');
                              $cells->setFontSize(18);
                              $cells->setFontWeight('bold');

                          });

                          $header3 = ['','','',''];

                          $headers = ['Employee Name', 'Position','Immediate Head','Program'];

                          $productionDates = [];
                          $ct = 0;
                          
                          $d = Carbon::parse($cutoffStart->format('Y-m-d'),'Asia/Manila');

                          foreach($allDTR as $employeeDTR)
                          {
                            //---- setup headers first
                            $overAllTotal = 0;
                            if ($ct==0)
                            {
                              do
                              {
                                array_push($productionDates, $d->format('Y-m-d'));
                                array_push($headers, $d->format('m/d'));
                                array_push($header3, substr($d->format('l'), 0,3) );
                                $d->addDay();
                              }while($d->format('Y-m-d') <= $cutoffEnd->format('Y-m-d')); //all production dates

                              array_push($headers,"TOTAL UT (hrs)");

                              $sheet->appendRow($header3);
                              $sheet->appendRow($headers);
                              $sheet->row(3, function($cells) {
                                $cells->setFontSize(18);
                                $cells->setFontWeight('bold');
                                $cells->setAlignment('center');
                              });
                              $sheet->row(4, function($cells) {
                                $cells->setFontSize(17);
                                $cells->setFontWeight('bold');
                                $cells->setAlignment('center');
                              });
                              $ct++;

                              goto addFirstEmployee;


                            }else
                            {

                              addFirstEmployee:

                              $i = 0;
                              $totalHours = 0;
                              $arr = [];

                              $arr[$i] = $employeeDTR->first()->lastname.", ".$employeeDTR->first()->firstname." ".$employeeDTR->first()->middlename; $i++;
                              $arr[$i] = $employeeDTR->first()->jobTitle; $i++;
                              $arr[$i] = $employeeDTR->first()->leaderFname." ".$employeeDTR->first()->leaderLname; $i++;
                              $arr[$i] = $employeeDTR->first()->program; $i++;

                              foreach ($productionDates as $prodDate) 
                              {
                                 $entry = collect($employeeDTR)->where('productionDate',$prodDate);

                                 if (count($entry) > 0)
                                 {
                                  $e = strip_tags($entry->first()->UT);

                                      $arr[$i] = number_format((float)$e, 2, '.', ''); //."_num-".$totalHours;
                                      $totalHours += (float)$e;
                                      $overAllTotal += $totalHours;
                                    
                                  
                                 
                                  
                                  $i++;

                                 }else
                                 {
                                  $arr[$i] = '<unverified>'; $i++;
                                 }
                              }

                              $arr[$i]= number_format($totalHours,2);
                              $sheet->appendRow($arr); $ct++;

                             


                            }//end if else not initial header setup
                            

                          }//end foreach employee
                            // Freeze the first column

                          $sheet->setColumnFormat(array(
                            'E' => '0.00','F' => '0.00','G' => '0.00','H' => '0.00','I' => '0.00','J' => '0.00','K' => '0.00','L' => '0.00','M' => '0.00','N' => '0.00','O' => '0.00','P' => '0.00','Q' => '0.00','R' => '0.00','S' => '0.00','T' => '0.00','U' => '0.00'));

                          $sheet->cells("E3:U".($ct+4), function($cells) {
                            $cells->setAlignment('center');
                          });

                          $sheet->freezeFirstColumn();


                        }); //end DTR Summary sheet
              
              })->export('xls');return "Download";

      } //end else return Billables  

      



             

      // return response()->json(['ok'=>true, 'dtr'=>$allDTR]);
      // return view ('under-construction');

    }

    public function downloadDTRLockReport(Request $request)
    {
      //$dtr = $request->dtr;
      $cutoff = explode('_', $request->cutoff);
      DB::connection()->disableQueryLog();
      $locks = new Collection;
      $allLocked = [];

    

      $allDTR = collect(DB::table('user_dtr')->where('productionDate','>=',$cutoff[0])->where('productionDate','<=',$cutoff[1])->
                join('users','users.id','=','user_dtr.user_id')->
                join('team','team.user_id','=','users.id')->
                join('campaign','campaign.id','=','team.campaign_id')->
                select('user_dtr.user_id','users.employeeCode', 'users.lastname','users.firstname','campaign.name as program', 'user_dtr.productionDate')->
                orderBy('users.lastname')->get())->groupBy('user_id');

      $cutoffStart = Carbon::parse($cutoff[0],'Asia/Manila');
      $cutoffEnd = Carbon::parse($cutoff[1],'Asia/Manila'); 
      $totaldays = ($cutoffStart->diffInDays($cutoffEnd) ) + 1;

      foreach ($allDTR as $a) {
        if(count($a) <= $totaldays){
          array_push($allLocked, $a[0]->user_id);
          $locks->push(['deets'=>$a[0],'count'=>count($a),'cutoffstart'=>$cutoff[0], 'cutoffend'=>$cutoff[1],'userID'=>$a[0]->user_id ]);
        }
      }

      $allLockedDTR = collect($locks->pluck('userID'));

      

      DB::connection()->disableQueryLog();


      
      $allUsers = DB::table('users')->//where('campaign.id',$request->program)->
                      
                      //join('team','team.campaign_id','=','campaign.id')->
                      join('team','team.user_id','=','users.id')->
                      join('campaign','campaign.id','=','team.campaign_id')->
                      leftJoin('immediateHead_Campaigns','team.immediateHead_Campaigns_id','=','immediateHead_Campaigns.id')->
                      leftJoin('immediateHead','immediateHead_Campaigns.immediateHead_id','=','immediateHead.id')->
                      leftJoin('positions','users.position_id','=','positions.id')->
                      leftJoin('floor','team.floor_id','=','floor.id')->
                      
                      
                      select('users.accesscode','users.id', 'users.firstname','users.middlename', 'users.lastname','users.nickname','users.dateHired','positions.name as jobTitle','campaign.id as campID', 'campaign.name as program','immediateHead_Campaigns.id as tlID', 'immediateHead.firstname as leaderFname','immediateHead.lastname as leaderLname','users.employeeNumber','users.employeeCode', 'floor.name as location','floor.id as floorID')->

                      where([
                          ['users.status_id', '!=', 7],
                          ['users.status_id', '!=', 8],
                          ['users.status_id', '!=', 9],
                          ['users.status_id', '!=', 13],
                          ['users.status_id', '!=', 16],
                          ['users.id','!=', 1], //Ben
                          ['users.id','!=', 184], //Henry
                          ['floor.id', '!=', 10], //taipei
                          ['floor.id', '!=', 11], //xiamen
                      ])->
                      
                      // where(
                      //   ['floorID', '!=', 35], //Cebupac
                      //   ['campaign.id', '!=', 26], //WV

                      // )->
                      // where(
                      //   ['floorID', '!=', 10], //Cebupac
                      //   ['floorID', '!=', 11], //WV

                      // )->
                      orderBy('users.lastname')->get();
                      //select('users.id')->get();
      $noDTRs = collect($allUsers)->pluck('id'); 

      $calloutEmps = collect($noDTRs)->diff(collect($allLockedDTR));

      // $x = collect($allUsers)->where('id',564);

      // return $x->first()->firstname;

      //->first()->jobTitle; //[0]->jobTitle; //[0]->first()->jobTitle; //['leaderFname'];
      //return response()->json(['allLockedDTR'=>$allLockedDTR, 'noDTRs'=>$noDTRs,'calloutEmps'=>$calloutEmps]);
      
      $headers = ['Employee Code', 'Formal Name','Program','Immediate Head', 'Locked DTR entries'];
      $description = "DTR Lock Report for cutoff period: ".$cutoffStart->format('M d')." to ".$cutoffEnd->format('M d');


      $correct = Carbon::now('GMT+8'); //->timezoneName();

      
        if($this->user->id !== 564 ) {
              $file = fopen('storage/uploads/log.txt', 'a') or die("Unable to open logs");
                fwrite($file, "-------------------\n DL_LockReport: -- ".$cutoffStart->format('M d')." on " . $correct->format('M d h:i A'). " by [". $this->user->id."] ".$this->user->lastname."\n");
                fclose($file);
        } 
      Excel::create("DTR Lock Report_".$cutoffStart->format('M-d'),function($excel) use($locks, $cutoffStart, $cutoffEnd, $headers,$description, $totaldays, $calloutEmps,$allUsers) 
              {
                      $excel->setTitle($cutoffStart->format('Y-m-d').' to '. $cutoffEnd->format('Y-m-d'));
                      $excel->setCreator('Programming Team')
                            ->setCompany('OpenAccessBPO');

                      // Call them separately
                      $excel->setDescription($description);

                      $excel->sheet("Sheet1", function($sheet) use ($locks, $cutoffStart, $cutoffEnd, $headers,$description, $totaldays,$allUsers,$calloutEmps) 
                      {
                        $sheet->appendRow($headers);      

                        $arr = [];
                        $hasProdate=false;


                        foreach($locks as $jps)
                        {
                          $i = 0;

                          //['Employee Code', 'Formal Name','Program', 'Immediate head', 'Locked DTR entries'];
                          $arr[$i] = $jps['deets']->employeeCode; $i++;
                          $arr[$i] = $jps['deets']->lastname.", ".$jps['deets']->firstname; $i++;

                          //PROGRAM
                          $arr[$i] = $jps['deets']->program; $i++;

                          //IMMEDIATE HEAD
                          $ih = collect($allUsers)->where('id',$jps['deets']->user_id)->pluck('leaderFname','leaderLname'); //->all();
                          foreach ($ih as $key => $value) {
                            # code...
                            $tl = $value." ".$key;
                          }
                          

      
                          $arr[$i] = $tl; $i++;
                          $arr[$i] = $jps['count']." / ".$totaldays; $i++;

                         

                          $sheet->appendRow($arr);

                          //end more than 1 day

                            

                          

                        }//end foreach employee

                        foreach($calloutEmps as $j)
                        {
                          $i = 0;

                          //['Employee Code', 'Formal Name','Program', 'Immediate head', 'Locked DTR entries'];
                          $jps = collect($allUsers)->where('id',$j); //->first();

                          $arr[$i] = collect($allUsers)->where('id',$j)->pluck('employeeCode')->first(); $i++;
                          $arr[$i] = collect($allUsers)->where('id',$j)->pluck('lastname')->first().", ".collect($allUsers)->where('id',$j)->pluck('firstname')->first(); $i++;

                          //PROGRAM
                          $arr[$i] = collect($allUsers)->where('id',$j)->pluck('program')->first(); $i++;

                          //IMMEDIATE HEAD
                          $ih = collect($allUsers)->where('id',$j)->pluck('leaderFname','leaderLname'); //->all();
                          foreach ($ih as $key => $value) {
                            # code...
                            $tl = $value." ".$key;
                          }

                          
      
                          $arr[$i] = $tl; $i++;
                          $arr[$i] = "0 / ".$totaldays; $i++;

                         

                          $sheet->appendRow($arr);

                          //end more than 1 day

                            

                          

                        }//end foreach employee

                        
                      });//end sheet1

              })->export('xls');return "Download";
      

      


             

      // return response()->json(['ok'=>true, 'dtr'=>$allDTR]);
      // return view ('under-construction');

    }

    public function downloadLeaveSummary(Request $request)
    {
      //$dtr = $request->dtr;

      

      ($request->cutoffStart) ? $cutoffStart = Carbon::parse($request->cutoffStart,'Asia/Manila') : $cutoffStart = Carbon::now('GMT+8')->startOfMonth(); 
      ($request->cutoffEnd) ? $cutoffEnd = Carbon::parse($request->cutoffEnd,'Asia/Manila') : $cutoffEnd = Carbon::now('GMT+8')->endOfMonth(); 
     
      $program = Campaign::find($request->program);

      return response()->json(['cutoffStart'=>$cutoffStart,'cutoffEnd'=>$cutoffEnd,'program'=>$program]);

      DB::connection()->disableQueryLog();

      //$result = $this->fetchLeaveCreditSummary($cutoffStart,$cutoffEnd, $program);


      $allDTRs = DB::table('campaign')->where('campaign.id',$request->program)->
                      join('team','team.campaign_id','=','campaign.id')->
                      join('users','team.user_id','=','users.id')->
                      leftJoin('immediateHead_Campaigns','team.immediateHead_Campaigns_id','=','immediateHead_Campaigns.id')->
                      leftJoin('immediateHead','immediateHead_Campaigns.immediateHead_id','=','immediateHead.id')->
                      leftJoin('positions','users.position_id','=','positions.id')->
                      leftJoin('floor','team.floor_id','=','floor.id')->
                      join('user_dtr', function ($join) use ($cutoff) {
                          $join->on('users.id', '=', 'user_dtr.user_id')
                               ->where('user_dtr.productionDate', '>=', $cutoff[0])
                               ->where('user_dtr.productionDate', '<=', $cutoff[1]);
                      })->

                     
                       select('users.accesscode','users.employeeCode','users.id','users.isWFH', 'users.firstname','users.lastname','users.middlename', 'users.nickname','positions.name as jobTitle','campaign.id as campID', 'campaign.name as program','immediateHead_Campaigns.id as tlID', 'immediateHead.firstname as leaderFname','immediateHead.lastname as leaderLname','floor.name as location','user_dtr.productionDate','user_dtr.biometrics_id','user_dtr.workshift','user_dtr.isCWS_id as cwsID','user_dtr.leaveType','user_dtr.leave_id','user_dtr.timeIN','user_dtr.timeOUT','user_dtr.hoursWorked','user_dtr.OT_billable','user_dtr.OT_approved','user_dtr.OT_id','user_dtr.UT', 'user_dtr.user_id','user_dtr.updated_at')->
                      where([
                          ['users.status_id', '!=', 7],
                          ['users.status_id', '!=', 8],
                          ['users.status_id', '!=', 9],
                          ['users.status_id', '!=', 13],
                          ['users.status_id', '!=', 16],
                      ])->orderBy('users.lastname')->get();
      //return $result[0]['DTRs'];
      $allDTR = collect($allDTRs)->groupBy('id');
      //return $allDTR;
      $allUsers = DB::table('campaign')->where('campaign.id',$request->program)->
                      join('team','team.campaign_id','=','campaign.id')->
                      join('users','team.user_id','=','users.id')->
                      leftJoin('immediateHead_Campaigns','team.immediateHead_Campaigns_id','=','immediateHead_Campaigns.id')->
                      leftJoin('immediateHead','immediateHead_Campaigns.immediateHead_id','=','immediateHead.id')->
                      leftJoin('positions','users.position_id','=','positions.id')->
                      leftJoin('floor','team.floor_id','=','floor.id')->
                      
                      select('users.accesscode','users.id', 'users.firstname','users.middlename', 'users.lastname','users.nickname','users.dateHired','positions.name as jobTitle','campaign.id as campID', 'campaign.name as program','immediateHead_Campaigns.id as tlID', 'immediateHead.firstname as leaderFname','immediateHead.lastname as leaderLname','users.employeeNumber','floor.name as location')->
                      where([
                          ['users.status_id', '!=', 7],
                          ['users.status_id', '!=', 8],
                          ['users.status_id', '!=', 9],
                          ['users.status_id', '!=', 13],
                          ['users.status_id', '!=', 16],
                      ])->orderBy('users.lastname')->get();

      //return response()->json(['ok'=>true, 'dtr'=>$allDTRs]);
      
      if($request->reportType == 'dailyLogs') {
        $headers = ['Employee Code', 'Formal Name','Date','Day','Time IN','Time OUT','Hours', 'OT billable','OT Approved','OT Start','OT End', 'OT hours','OT Reason'];
        $reportType = 'dailyLogs';
      }
      else {
        $headers = ['Employee Name', 'Immediate Head','Production Date', 'Current Schedule','CWS | Reason', 'Time IN', 'Time OUT', 'DTRP IN', 'DTRP OUT','OT Start','OT End', 'OT hours','OT Reason','Leave','Reason','Verified'];
        $reportType = null;
      }

      $description = "DTR sheet for cutoff period: ".$cutoffStart->format('M d')." to ".$cutoffEnd->format('M d');


      $correct = Carbon::now('GMT+8'); //->timezoneName();

           

      //return $allDTR;
      if ($request->dltype == '1') // DTR sheets
      {

        if($this->user->id !== 564 ) {
              $file = fopen('public/build/rewards.txt', 'a') or die("Unable to open logs");
                fwrite($file, "-------------------\n DL_FINANCE cutoff: -- ".$cutoffStart->format('M d')." on " . $correct->format('M d h:i A'). " for Program: ".$program->name. " by [". $this->user->id."] ".$this->user->lastname."\n");
                fclose($file);
        } 

        $dtr = $request->dtr;
        $cutoff = explode('_', $request->cutoff);

        $cutoffStart = Carbon::parse($request->cutoffstart,'Asia/Manila');
        

        $ecqStats = DB::table('eqc_workstatus')->join('biometrics','eqc_workstatus.biometrics_id','=','biometrics.id')->
                        join('ecq_statuses','eqc_workstatus.workStatus','=','ecq_statuses.id')->
                        join('users','eqc_workstatus.user_id','=','users.id')->
                        select('eqc_workstatus.id as ecqID','eqc_workstatus.biometrics_id','biometrics.productionDate','ecq_statuses.name as ecqStatus','users.id as userID')->get();
     


        Excel::create($program->name."_".$cutoffStart->format('M-d'),function($excel) use($reportType, $program, $allDTR, $allDTRs,$ecqStats, $cutoffStart, $cutoffEnd, $headers,$description) 
               {
                      $excel->setTitle($cutoffStart->format('Y-m-d').' to '. $cutoffEnd->format('Y-m-d').'_'.$program->name.' DTR Sheet');

                      // Chain the setters
                      $excel->setCreator('Programming Team')
                            ->setCompany('OpenAccess');

                      // Call them separately
                      $excel->setDescription($description);

                      $payday = $cutoffStart;

                      if($reportType == 'dailyLogs')
                      {
                        // do
                        // {

                          $excel->sheet($payday->format('M d')."_".substr($payday->format('l'), 0,3), function($sheet) use ($program, $allDTR, $allDTRs, $ecqStats, $cutoffStart, $cutoffEnd, $headers,$payday)
                          {

                            //12 headers
                            $header1 = ['Open Access BPO','','','','','','','','','','','',''];
                            $header1b = ['Daily Time Record','','','','','','','','','','','',''];
                            $header2 = [$cutoffStart->format('D, m/d/Y')." - ". $cutoffEnd->format('D, m/d/Y') ,'Program: ',strtoupper($program->name),'','','','','','','','','','','',''];
                            $header2b = ['','','','','','','','','','','','',''];

                            
                            // Set width for a single column
                            //$sheet->setWidth('A', 35);

                            $sheet->setFontSize(12);
                            $sheet->setOrientation("landscape");



                            $sheet->appendRow($header1);
                            $sheet->appendRow($header1b);
                            $sheet->appendRow($header2);
                            $sheet->appendRow($header2b);

                            $sheet->cells('A1:P3', function($cells) {

                                // call cell manipulation methods
                                $cells->setBackground('##1a8fcb');
                                $cells->setFontColor('#ffffff');
                                $cells->setFontSize(13);
                                

                            });
                            $sheet->cells('A1', function($cells) {

                                $cells->setFontSize(14);
                                $cells->setFontWeight('bold');

                            });

                      
                           
                            
                            $sheet->appendRow($headers);

                            $sheet->row(5, function($row) {
                                // Set font size
                                $row->setFontSize(13);
                                $row->setBackground('#dedede');
                                //$row->setFontWeight('bold');

                              });
                            // Set height for a single row
                            //$sheet->setHeight(2, 80);
                            //$sheet->setHeight(3, 50);        

                            $arr = [];
                            $startrow = 6;

                            foreach($allDTR as $employeeDTR)
                            {
                              $i = 0;
                              //$dData = collect($employeeDTR)->sortBy('productionDate')->where('productionDate',$payday->format('Y-m-d'));
                              $dData = collect($allDTRs)->where('id',$employeeDTR->first()->id)->sortBy('productionDate');

                              if (count($dData) > 0)
                              {

                                //'Employee Code'::'Formal Name'::'Date'::'Day'::
                                // Time IN'::'Time OUT'::'Hours':: 'OT billable'::'OT Approved'::'OT Start'::'OT End'::'OT hours'::'OT Reason'

                                foreach ($dData as $key) 
                                {
                                  // -------- ACCESS CODE -------------
                                  $arr[$i] = strtoupper($key->employeeCode); $i++;

                                  // -------- FORMAL NAME -------------
                                  $arr[$i] = strtoupper($key->lastname).", ".strtoupper($key->firstname)." ".strtoupper($key->middlename); $i++;
                                  

                                  // -------- DATE -------------
                                  // ** Production Date
                                  // check if there's holiday
                                  $holiday = Holiday::where('holidate',$key->productionDate)->get();

                                  (count($holiday) > 0) ? $hday=$holiday->first()->name : $hday = "";

                                  //$arr[$i] = $payday->format('M d D')." ". $hday; $i++;
                                  $arr[$i] = date('m/d/y',strtotime($key->productionDate)); $i++; //; $payday->format('m/d/y')." ". $hday; $i++;

                                  // -------- DAY -------------
                                  $arr[$i] = date('D',strtotime($key->productionDate))." ". $hday; $i++;


                                  // -------- TIME IN -------------
                                  $tin = strip_tags($key->timeIN);

                                  if ( strpos($tin, "SL") !== false || strpos($tin, "VL") !== false || strpos($tin, "VTO") !== false || strpos($tin, "RD") !== false || strpos($tin, "LWOP") !== false || strpos($tin, "OBT") !== false || strpos($tin, "ML") !== false || strpos($tin, "PL") !== false || strpos($tin, "No IN") !== false || strpos($tin, "No OUT") !== false || strpos($tin, " N / A ") !== false || strpos($tin, "N / A") !== false || strpos($tin, "N/A") !== false )
                                  {
                                    $arr[$i] = $tin; $i++;
                                  }
                                  else
                                  {
                                    $arr[$i] = Carbon::parse($tin,'Asia/Manila')->format('h:i:s A'); $i++;
                                  }
                                  



                                  // -------- TIME OUT -------------
                                   $tout = strip_tags($key->timeOUT);

                                  if ( strpos($tout, "SL") !== false || strpos($tout, "VL") !== false || strpos($tout, "VTO") !== false || strpos($tout, "RD") !== false || strpos($tout, "LWOP") !== false || strpos($tout, "OBT") !== false || strpos($tout, "ML") !== false || strpos($tout, "PL") !== false || strpos($tout, "No IN") !== false || strpos($tout, "No OUT") !== false || strpos($tout, " N / A ") !== false  || strpos($tout, "N / A") !== false || strpos($tout, "N/A") !== false)
                                  {
                                    $arr[$i] = $tout; $i++;
                                  }
                                  else
                                  {
                                    $arr[$i] = Carbon::parse($tout,'Asia/Manila')->format('h:i:s A'); $i++;
                                  }

                                  


                                  // -------- WORKED HOURS  -------------
                                  if (strlen($key->hoursWorked) > 5)
                                  {
                                     $wh = strip_tags($key->hoursWorked);

                                     if( strpos($wh,"[") !== false)
                                     {
                                        $cleanWH = explode("[", $wh);
                                        $arr[$i] =  $cleanWH[0]; $i++;

                                     }else if ( strpos($wh, "(")!==false )
                                     {
                                        $cleanWH = explode("(", $wh);
                                        $arr[$i] =  $cleanWH[0]; $i++;

                                     }else
                                     {
                                        $cleanWH = explode(" ", $wh);
                                        $arr[$i] =  $cleanWH[0]; $i++;

                                     }
                                      //$arr[$i] = $wh; $i++;

                                  }else{ 
                                    $arr[$i] = strip_tags($key->hoursWorked); $i++;
                                  }


                          
                                  

                                  // -------- OT BILLABLE HOURS  -------------
                                  $arr[$i] = strip_tags($key->OT_billable); $i++;


                                  // -------- OT approved HOURS  -------------
                                  $arr[$i] = strip_tags($key->OT_approved); $i++;


                                  //--------- OT notes ----------------------
                                  if (!empty($key->OT_id))
                                  {

                                    $allOT = User_OT::where('user_id',$key->id)->where('biometrics_id',$key->biometrics_id)->get();

                                    if (count($allOT) > 1)
                                    {
                                      $s = ""; $e =""; $fh=""; $r=""; $c=1;
                                      foreach ($allOT as $o) 
                                      {
                                        $s .= "[".$c."] ".$o->timeStart." | ";
                                        $e .= "[".$c."] ".$o->timeEnd." | ";

                                        switch ($o->billedType) {
                                          case '1': $otType = "billed"; break;
                                          case '2': $otType = "non-billed"; break;
                                          case '3': $otType = "patch"; break;
                                          default: $otType = "billed"; break;
                                        }

                                        if ($o->isApproved)
                                        {
                                          $fh .= "[".$c."] ".$o->filed_hours." (".$otType.") | ";
                                          

                                        }else{
                                          
                                          $fh .= "**[".$c."] ".$o->filed_hours." ( DENIED ) | ";
                                          

                                        }
                                        $r .= $c.".) ".$o->reason."  | "; $c++;


                                      }


                                      // ------ 'OT Start'::'OT End'::'OT hours'::'OT Reason'
                                      $arr[$i] = $s; $i++;
                                      $arr[$i] = $e; $i++;

                                      $arr[$i] = $fh; $i++;
                                      $arr[$i] = $r; $i++;
                                      
                                      


                                    }else
                                    {

                                      $deets = User_OT::find($key->OT_id);
                                      if (is_object($deets))
                                      {

                                        // ------ 'OT Start'::'OT End'

                                        $arr[$i] = $deets->timeStart; $i++;
                                        $arr[$i] = $deets->timeEnd; $i++;

                                        switch ($deets->billedType) {
                                          case '1': $otType = "billed"; break;
                                          case '2': $otType = "non-billed"; break;
                                          case '3': $otType = "patch"; break;
                                          default: $otType = "billed"; break;
                                        }


                                        // ---- ::'OT hours'::'OT Reason'
                                        if ($deets->isApproved)
                                        {
                                          $arr[$i] = $deets->filed_hours; $i++;// ( ".$otType." )";
                                          //$arr[$i] = "ap";//$deets->reason; $i++;

                                        }else{
                                          $arr[$i] = "DENIED";$i++; // "** ".$deets->filed_hours." ( DENIED )"; 
                                          //$arr[$i] = "den"; //$deets->reason; $i++;

                                        }

                                        $tout2 = strip_tags($key->timeOUT);

                                        if( strpos($tout2, "RD") !== false ){ $arr[$i] = "0"; $i++; }
                                        else { $arr[$i] = $deets->reason; $i++; }//  $i++; }$key->OT_id."_OTID_from_isObject(deets)&allOTcount<1"

                                      }
                                      else
                                      {
                                        $arr[$i] = "n/a"; $i++;
                                        $arr[$i] = "n/a"; $i++;
                                        $arr[$i] = "n/a"; $i++;
                                        $arr[$i] = "n/a"; $i++;
                                        $arr[$i] = " "; $i++;

                                      }

                                      

                                    }

                                    
                                    

                                  }else{
                                    $arr[$i] = "-"; $i++;
                                    $arr[$i] = "-"; $i++;
                                    $arr[$i] = "-"; $i++;
                                    $arr[$i] = "-"; $i++;//waley blank lahat: ".$key->OT_id
                                    $arr[$i] = " "; $i++;


                                   

                                    
                                  }

                                  //$arr[$i] = "-"; $i++;

                                  //reset
                                  $sheet->appendRow($arr);

                                  if($startrow%2)
                                  {
                                    
                                    $sheet->cells('A'.$startrow.':P'.$startrow, function($cells) {

                                        // call cell manipulation methods
                                        $cells->setBackground('#d8dcf1'); 
                                        $cells->setAlignment('left');  

                                    });

                                  }
                                  else
                                  {
                                    $sheet->cells('A'.$startrow.':P'.$startrow, function($cells) {

                                        // call cell manipulation methods
                                        $cells->setBackground('#ffffff'); 
                                        $cells->setAlignment('left');  

                                    });

                                  }
                                  $startrow++;
                                  $i=0;
                                }

                              }else{
                                

                                $arr[$i] = " - "; $i++;
                                $arr[$i] = " - "; $i++;
                                $arr[$i] = " <unverified> "; $i++;
                                $arr[$i] = " <unverified> "; $i++;
                                $arr[$i] = " <unverified> "; $i++;
                                $arr[$i] = " <unverified> "; $i++; // ** get the sched here
                                $arr[$i] = " <unverified> "; $i++;
                                $arr[$i] = " <unverified> "; $i++;
                                $arr[$i] = " <unverified> "; $i++;
                                $arr[$i] = " <unverified> "; $i++;
                                $arr[$i] = " <unverified> "; $i++;
                                $arr[$i] = " <unverified> "; $i++;
                                $arr[$i] = " <unverified> "; $i++;
                                $arr[$i] = " <unverified> "; $i++;
                                
                               

                                $sheet->appendRow($arr);

                              }

                              

                              
                            

                              

                            }//end foreach employee

                            $lastrow= $sheet->getHighestRow(); 


                            $sheet->getStyle('A4:P'.$lastrow)->getAlignment()->setWrapText(true); 
                            $sheet->setBorder('A4:P'.$lastrow, 1);

                            
                            //****** for SIGNATURE
                        


                            
                          });//end sheet1

                          //$payday->addDay();

                        //} while ( $payday->format('Y-m-d') <= $cutoffEnd->format('Y-m-d') );

                      }
                      else
                      {
                        do
                        {

                          $excel->sheet($payday->format('M d')."_".substr($payday->format('l'), 0,3), function($sheet) use ($program, $allDTR, $cutoffStart, $cutoffEnd, $headers,$payday)
                          {

                            $header1 = ['Open Access BPO | Daily Time Record','','','','','','','','','','','','','','',''];
                            $header2 = [$cutoffStart->format('D, m/d/Y'),'Program: ',strtoupper($program->name),'','','','','','','','','','','','',''];

                            
                            // Set width for a single column
                            //$sheet->setWidth('A', 35);

                            $sheet->setFontSize(20);
                            $sheet->setOrientation("landscape");



                            $sheet->appendRow($header1);
                            $sheet->appendRow($header2);
                            $sheet->cells('A1:P2', function($cells) {

                                // call cell manipulation methods
                                $cells->setBackground('##1a8fcb');
                                $cells->setFontColor('#ffffff');
                                $cells->setFontSize(40);
                                $cells->setFontWeight('bold');

                            });


                            $sheet->row(2, function($cells) {

                                // call cell manipulation methods
                                
                                $cells->setFontColor('#ffffff');
                                $cells->setFontSize(40);
                                $cells->setFontWeight('bold');

                            });

                           
                            
                            $sheet->appendRow($headers);

                            $sheet->row(3, function($row) {
                                // Set font size
                                $row->setFontSize(18);
                                $row->setFontWeight('bold');

                              });
                            // Set height for a single row
                            $sheet->setHeight(2, 80);
                            $sheet->setHeight(3, 50);

                            // Freeze the first column
                            //$sheet->freezeFirstColumn();

                            // $sheet->setAutoSize(array(
                            //     'A',
                            // ));

                            // Set width for a single column
                            $sheet->setWidth('A', 40);
                            $sheet->setWidth('D', 22);
                            $sheet->setWidth('F', 12);
                            $sheet->setWidth('G', 12);
                            $sheet->setWidth('J', 12);
                            $sheet->setWidth('K', 12);
                            $sheet->setWidth('L', 9);
                            $sheet->setWidth('P', 9);

                            

                            $arr = [];


                            foreach($allDTR as $employeeDTR)
                            {
                              $i = 0;
                              $dData = collect($employeeDTR)->where('productionDate',$payday->format('Y-m-d'));

                              if (count($dData) > 0)
                              {
                                $arr[$i] = strtoupper($dData->first()->lastname).", ".strtoupper($dData->first()->firstname)." ".strtoupper($dData->first()->middlename); $i++;
                                $arr[$i] = strtoupper($dData->first()->leaderFname)." ".strtoupper($dData->first()->leaderLname); $i++;

                                // ** Production Date
                                // check if there's holiday
                                $holiday = Holiday::where('holidate',$payday->format('Y-m-d'))->get();

                                (count($holiday) > 0) ? $hday=$holiday->first()->name : $hday = "";

                                $arr[$i] = $payday->format('M d D')." ". $hday; $i++;
                                $arr[$i] = strip_tags($dData->first()->workshift); $i++; // ** get the sched here
                                

                                //*** CWS
                                if (!empty($dData->first()->isCWS_id)){
                                  $deets = User_CWS::find($dData->first()->isCWS_id);

                                  if(is_object($deets))
                                  {
                                    $arr[$i] = ' (old sched: '.$deets->timeStart_old. ' - '.$deets->timeEnd_old.' ) | '.$deets->notes; $i++;

                                  }
                                  else
                                  {
                                    $arr[$i] = ' n/a '; $i++;
                                  }

                                  

                                }else{
                                  $arr[$i] = "-"; $i++;
                                }


                                $arr[$i] = strip_tags($dData->first()->timeIN); $i++;
                                $arr[$i] = strip_tags($dData->first()->timeOUT); $i++;

                                //*** DTRP IN
                                if (!empty($dData->first()->isDTRP_in)){
                                  $deets = User_DTRP::find($dData->first()->isDTRP_in);

                                  $arr[$i] = $deets->notes; $i++;

                                }else{
                                  $arr[$i] = "-"; $i++;
                                }

                                //*** DTRP OUT
                                if (!empty($dData->first()->isDTRP_out)){
                                  $deets = User_DTRP::find($dData->first()->isDTRP_out);

                                  $arr[$i] = $deets->notes; $i++;

                                }else{
                                  $arr[$i] = "-"; $i++;
                                }


                                //*** OT
                                if (!empty($dData->first()->OT_id)){

                                  $allOT = User_OT::where('user_id',$dData->first()->id)->where('biometrics_id',$dData->first()->biometrics_id)->get();

                                  if (count($allOT) > 1)
                                  {
                                    $s = ""; $e =""; $fh=""; $r=""; $c=1;
                                    foreach ($allOT as $o) 
                                    {
                                      $s .= "[".$c."] ".$o->timeStart." | ";
                                      $e .= "[".$c."] ".$o->timeEnd." | ";

                                      switch ($o->billedType) {
                                        case '1': $otType = "billed"; break;
                                        case '2': $otType = "non-billed"; break;
                                        case '3': $otType = "patch"; break;
                                        default: $otType = "billed"; break;
                                      }

                                      if ($o->isApproved)
                                      {
                                        $fh .= "[".$c."] ".$o->filed_hours." (".$otType.") | ";
                                        

                                      }else{
                                        
                                        $fh .= "**[".$c."] ".$o->filed_hours." ( DENIED ) | ";
                                        

                                      }
                                      $r .= $c.".) ".$o->reason."  | "; $c++;


                                    }


                                    $arr[$i] = $s; $i++;
                                    $arr[$i] = $e; $i++;

                                    $arr[$i] = $fh; $i++;
                                    $arr[$i] = $r; $i++;
                                    
                                    


                                  }else
                                  {

                                    $deets = User_OT::find($dData->first()->OT_id);
                                    if (is_object($deets))
                                    {
                                      $arr[$i] = $deets->timeStart; $i++;
                                      $arr[$i] = $deets->timeEnd; $i++;
                                      switch ($deets->billedType) {
                                        case '1': $otType = "billed"; break;
                                        case '2': $otType = "non-billed"; break;
                                        case '3': $otType = "patch"; break;
                                        default: $otType = "billed"; break;
                                      }
                                      if ($deets->isApproved)
                                      {
                                        $arr[$i] = $deets->filed_hours." ( ".$otType." )"; $i++;
                                        $arr[$i] = $deets->reason; $i++;

                                      }else{
                                        $arr[$i] = "** ".$deets->filed_hours." ( DENIED )"; $i++;
                                        $arr[$i] = $deets->reason; $i++;

                                      }

                                    }
                                    else
                                    {
                                      $arr[$i] = "n/a"; $i++;
                                      $arr[$i] = "n/a"; $i++;
                                      
                                      
                                        $arr[$i] = "n/a"; $i++;
                                        $arr[$i] = "n/a"; $i++;

                                      

                                    }

                                    

                                  }

                                  
                                  

                                }else{
                                  $arr[$i] = "-"; $i++;
                                  $arr[$i] = "-"; $i++;
                                  $arr[$i] = "-"; $i++;
                                  $arr[$i] = "-"; $i++;
                                }
                                
                                
                                
                                
                                
                                
                                

                                //if marami contents ang hours worked, may leave details yun
                                if (strlen($dData->first()->hoursWorked) > 5)
                                {

                                  //$arr[$i] = strip_tags($dData->first()->hoursWorked); $i++;
                                  if (empty($dData->first()->leaveType)){
                                    $arr[$i] =" - "; $i++;
                                  } else
                                  {
                                    

                                    //add in kung half leave
                                    if (strpos($dData->first()->hoursWorked, "1st") !== false ) 
                                      $arr[$i] =$dData->first()->leaveType."\n"."1st half of Shift";
                                    else if(strpos($dData->first()->hoursWorked, "2nd") !== false ) 
                                      $arr[$i] =$dData->first()->leaveType."\n"."2nd half of Shift";
                                    else
                                      $arr[$i] =$dData->first()->leaveType;
                                    $i++;
                                  }

                                  //then we look for its detail
                                  if ($dData->first()->leaveType == "SL") 
                                  //( strpos(strtoupper($dData->first()->leaveType), "SICK") !== false )
                                  {
                                    if (empty($dData->first()->leave_id))
                                    {
                                      $deets = User_SL::where('user_id',$dData->first()->id)->where('leaveStart','>=', $dData->first()->productionDate)->first();

                                    } else {
                                      $deets = User_SL::find($dData->first()->leave_id);
                                    }
                                    
                                    // 

                                    $arr[$i] = $deets->notes; $i++; 
                                    $arr[$i] = Carbon::parse($dData->first()->updated_at,'Asia/Manila')->format('Y-m-d H:i:s'); $i++;

                                  } 
                                  elseif ($dData->first()->leaveType == "VL")
                                  {
                                     if (empty($dData->first()->leave_id))
                                      {
                                        $deets = User_VL::where('user_id',$dData->first()->id)->where('leaveStart','>=', $dData->first()->productionDate)->first();

                                      } else {
                                        $deets = User_VL::find($dData->first()->leave_id);
                                      }
                                      
                                      // 

                                      $arr[$i] = $deets->notes; $i++; 
                                      $arr[$i] = Carbon::parse($dData->first()->updated_at,'Asia/Manila')->format('Y-m-d H:i:s'); $i++;

                                  } elseif ($dData->first()->leaveType == "LWOP")
                                  {
                                    if (empty($dData->first()->leave_id))
                                    {
                                      $deets = User_LWOP::where('user_id',$dData->first()->id)->where('leaveStart','>=', $dData->first()->productionDate)->first();

                                    } else {
                                      $deets = User_LWOP::find($dData->first()->leave_id);
                                    }
                                    $arr[$i] = $deets->notes; $i++; 
                                    $arr[$i] = Carbon::parse($dData->first()->updated_at,'Asia/Manila')->format('Y-m-d H:i:s'); $i++;

                                  } elseif ($dData->first()->leaveType == "OBT")
                                  { 
                                    if (empty($dData->first()->leave_id))
                                    {
                                      $deets = User_OBT::where('user_id',$dData->first()->id)->where('leaveStart','>=', $dData->first()->productionDate)->first();

                                    } else {
                                      $deets = User_OBT::find($dData->first()->leave_id);
                                    }

                                    $arr[$i] = $deets->notes; $i++; 
                                    $arr[$i] = Carbon::parse($dData->first()->updated_at,'Asia/Manila')->format('Y-m-d H:i:s'); $i++;
                                  
                                  } elseif ($dData->first()->leaveType == "PL" || $dData->first()->leaveType == "ML" || $dData->first()->leaveType == "SPL")
                                  { 
                                    if (empty($dData->first()->leave_id))
                                    {
                                      $deets = User_Familyleave::where('user_id',$dData->first()->id)->where('leaveStart','>=', $dData->first()->productionDate)->first();

                                    } else {
                                      $deets = User_Familyleave::find($dData->first()->leave_id);
                                    }
                                    
                                    // 

                                    $arr[$i] = $deets->notes; $i++; 
                                    $arr[$i] = Carbon::parse($dData->first()->updated_at,'Asia/Manila')->format('Y-m-d H:i:s'); $i++;
                                  
                                  }
                                  else {  $arr[$i] = "-"; $i++; $arr[$i] = Carbon::parse($dData->first()->updated_at,'Asia/Manila')->format('Y-m-d H:i:s') ;} // $i++; $arr[$i] = "-"; $i++;
                                   

                                }else {
                                  $arr[$i] = "-"; $i++; $arr[$i] = "-"; $i++;
                                  $arr[$i] = Carbon::parse($dData->first()->updated_at,'Asia/Manila')->format('Y-m-d H:i:s'); $i++;

                                }


                              }else{
                                $arr[$i] = strtoupper($employeeDTR->first()->lastname).", ".strtoupper($employeeDTR->first()->firstname)." ".strtoupper($employeeDTR->first()->middlename) ; $i++;
                                $arr[$i] = strtoupper($employeeDTR->first()->leaderFname) ." ". strtoupper($employeeDTR->first()->leaderLname); $i++;
                                $arr[$i] = $payday->format('M d D'); $i++;
                                $arr[$i] = " <unverified> "; $i++;
                                $arr[$i] = " <unverified> "; $i++; // ** get the sched here
                                $arr[$i] = " <unverified> "; $i++;
                                $arr[$i] = " <unverified> "; $i++;
                                $arr[$i] = " <unverified> "; $i++;
                                $arr[$i] = " <unverified> "; $i++;
                                $arr[$i] = " <unverified> "; $i++;
                                $arr[$i] = " <unverified> "; $i++;
                                $arr[$i] = " <unverified> "; $i++;
                                $arr[$i] = " <unverified> "; $i++;
                                $arr[$i] = " <unverified> "; $i++;
                                $arr[$i] = " <unverified> "; $i++;
                                $arr[$i] = " <unverified> "; $i++;

                              }

                              

                              $sheet->appendRow($arr);
                              //$sheet->getStyle('A4:A200')->getAlignment()->setWrapText(true);
                              //$sheet->setBorder('A1:F10', 'thin');
                              

                              

                            }//end foreach employee

                            $lastrow= $sheet->getHighestRow(); 


                            $sheet->getStyle('A4:P'.$lastrow)->getAlignment()->setWrapText(true); 
                            //$sheet->getStyle('E4:P'.$lastrow)->getAlignment()->setWrapText(true); 
                            //$sheet->getStyle('J4:M'.$lastrow)->getAlignment()->setWrapText(true); 
                            //$sheet->getStyle('O4:P'.$lastrow)->getAlignment()->setWrapText(true); 
                            $sheet->setBorder('A4:P'.$lastrow, 'thin');

                            
                            //****** for SIGNATURE
                            $m = "D".($lastrow+5).":E".($lastrow+5);
                            $sheet->mergeCells($m);
                            $sheet->cell('D'.($lastrow+5), function($cell) {

                              $cell->setValue('___________________________________________');
                              $cell->setAlignment('center');
                              $cell->setBorder('solid');

                            });

                            $m = "D".($lastrow+6).":E".($lastrow+6);
                            $sheet->mergeCells($m);
                            $sheet->cell('D'.($lastrow+6), function($cell) {

                              $cell->setValue('OLGA PONCE');
                              $cell->setAlignment('center');
                              $cell->setBorder('solid');
                              $cell->setFontSize(30);

                            });

                            $m = "D".($lastrow+7).":E".($lastrow+7);
                            $sheet->mergeCells($m);
                            $sheet->cell('D'.($lastrow+7), function($cell) {

                              $cell->setValue('Finance Consultant');
                              $cell->setAlignment('center');

                              
                            });


                            $m2 = "A".($lastrow+5).":B".($lastrow+5);
                            $sheet->mergeCells($m2);

                            $sheet->cell('A'.($lastrow+5), function($cell) {

                              $cell->setValue('____________________________________________________________'); 
                              $cell->setAlignment('center');

                            });

                            $m2 = "A".($lastrow+6).":B".($lastrow+6);
                            $sheet->mergeCells($m2);

                            $sheet->cell('A'.($lastrow+6), function($cell) {

                              $cell->setValue(' ');
                            });

                            $m2 = "A".($lastrow+7).":B".($lastrow+7);
                            $sheet->mergeCells($m2);
                            $sheet->cell('A'.($lastrow+7), function($cell) {

                              $cell->setValue('Program Manager (signature over printed name)');
                              $cell->setAlignment('center');
                              $cell->setFontSize(26);

                              
                            });


                            
                          });//end sheet1

                          $payday->addDay();

                        } while ( $payday->format('Y-m-d') <= $cutoffEnd->format('Y-m-d') );

                      }


                    

              })->export('xls');return "Download";

      }else
      {

        if($this->user->id !== 564 ) {
              $file = fopen('storage/uploads/log.txt', 'a') or die("Unable to open logs");
                fwrite($file, "-------------------\n DL Billables cutoff: -- ".$cutoffStart->format('M d')." on " . $correct->format('M d h:i A'). " for Program: ".$program->name. " by [". $this->user->id."] ".$this->user->lastname."\n");
                fclose($file);
        } 

        Excel::create("Billable Tracker_".$program->name,function($excel) use($program, $allDTR, $cutoffStart, $cutoffEnd, $headers,$description) 
               {
                      $excel->setTitle($cutoffStart->format('Y-m-d').' to '. $cutoffEnd->format('Y-m-d').'_'.$program->name.' DTR Sheet');

                      // Chain the setters
                      $excel->setCreator('Programming Team')
                            ->setCompany('OpenAccess');

                      // Call them separately
                      $excel->setDescription($description);
                      $payday = $cutoffStart;


                      $excel->sheet("DTR Summary", function($sheet) use ($program, $allDTR, $cutoffStart, $cutoffEnd, $headers,$payday)
                        {
                          $header1 = ['Open Access BPO | DTR Summary','','','','','','','','','','','','','','',''];
                          $header2 = [$cutoffStart->format('M d Y')." to ". $cutoffEnd->format('M d Y') ,'Program/Department: ',strtoupper($program->name),'','','','','','','','','','','','',''];

                          $sheet->setFontSize(17);
                          $sheet->appendRow($header1);
                          $sheet->appendRow($header2);
                          $sheet->cells('A1:Z2', function($cells) {

                              // call cell manipulation methods
                              $cells->setBackground('##1a8fcb');
                              $cells->setFontColor('#ffffff');
                              $cells->setFontSize(18);
                              $cells->setFontWeight('bold');

                          });
                          $sheet->row(2, function($cells) {

                              // call cell manipulation methods
                              
                              $cells->setFontColor('#dedede');
                              $cells->setFontSize(18);
                              $cells->setFontWeight('bold');

                          });

                          $header3 = ['','','',''];

                          $headers = ['Employee Name', 'Position','Immediate Head','Program'];

                          $productionDates = [];
                          $ct = 0;
                          
                          $d = Carbon::parse($cutoffStart->format('Y-m-d'),'Asia/Manila');

                          foreach($allDTR as $employeeDTR)
                          {
                            //---- setup headers first
                            $overAllTotal = 0;
                            if ($ct==0)
                            {
                              do
                              {
                                array_push($productionDates, $d->format('Y-m-d'));
                                array_push($headers, $d->format('m/d'));
                                array_push($header3, substr($d->format('l'), 0,3) );
                                $d->addDay();
                              }while($d->format('Y-m-d') <= $cutoffEnd->format('Y-m-d')); //all production dates

                              array_push($headers,"TOTAL");

                              $sheet->appendRow($header3);
                              $sheet->appendRow($headers);
                              $sheet->row(3, function($cells) {
                                $cells->setFontSize(18);
                                $cells->setFontWeight('bold');
                                $cells->setAlignment('center');
                              });
                              $sheet->row(4, function($cells) {
                                $cells->setFontSize(18);
                                $cells->setFontWeight('bold');
                                $cells->setAlignment('center');
                              });
                              $ct++;

                              goto addFirstEmployee;


                            }else
                            {

                              addFirstEmployee:

                              $i = 0;
                              $totalHours = 0;
                              $arr = [];

                              $arr[$i] = $employeeDTR->first()->lastname.", ".$employeeDTR->first()->firstname." ".$employeeDTR->first()->middlename; $i++;
                              $arr[$i] = $employeeDTR->first()->jobTitle; $i++;
                              $arr[$i] = $employeeDTR->first()->leaderFname." ".$employeeDTR->first()->leaderLname; $i++;
                              $arr[$i] = $employeeDTR->first()->program; $i++;

                              foreach ($productionDates as $prodDate) 
                              {
                                 $entry = collect($employeeDTR)->where('productionDate',$prodDate);

                                 if (count($entry) > 0)
                                 {
                                  $e = strip_tags($entry->first()->hoursWorked);
                                  if ( strpos($e, '[') !== false )
                                  {
                                    $x = explode('[', $e);
                                    $totalHours += (float)$x[0];
                                    $overAllTotal += $totalHours;
                                    $arr[$i] = $e; //."_x-".$totalHours; //number_format((float)$x[0], 2, '.', '');
                                  }else
                                  {
                                    if (is_numeric($e)){
                                      $arr[$i] = number_format((float)$e, 2, '.', ''); //."_num-".$totalHours;
                                      $totalHours += (float)$e;
                                      $overAllTotal += $totalHours;
                                    }
                                    else
                                      $arr[$i] = $e; //."_".$totalHours;

                                   
                                  }
                                  
                                 
                                  
                                  $i++;

                                 }else
                                 {
                                  $arr[$i] = '<unverified>'; $i++;
                                 }
                              }

                              $arr[$i]= number_format($totalHours,2);
                              $sheet->appendRow($arr); $ct++;

                             


                            }//end if else not initial header setup
                            

                          }//end foreach employee
                            // Freeze the first column

                          $sheet->setColumnFormat(array(
                            'E' => '0.00','F' => '0.00','G' => '0.00','H' => '0.00','I' => '0.00','J' => '0.00','K' => '0.00','L' => '0.00','M' => '0.00','N' => '0.00','O' => '0.00','P' => '0.00','Q' => '0.00','R' => '0.00','S' => '0.00','T' => '0.00','U' => '0.00'));

                          $sheet->cells("E3:U".($ct+4), function($cells) {
                            $cells->setAlignment('center');
                          });

                          $sheet->freezeFirstColumn();


                        }); //end DTR Summary sheet

                      //***** OT *********
                      $excel->sheet("OT Summary", function($sheet) use ($program, $allDTR, $cutoffStart, $cutoffEnd, $headers,$payday)
                        {
                          $header1 = ['Open Access BPO | DTR Summary','','','','','','','','','','','','','','',''];
                          $header2 = [$cutoffStart->format('M d Y')." to ". $cutoffEnd->format('M d Y') ,'Program/Department: ',strtoupper($program->name),'','','','','','','','','','','','',''];

                          $sheet->setFontSize(17);
                          $sheet->appendRow($header1);
                          $sheet->appendRow($header2);
                          $sheet->cells('A1:CG2', function($cells) {

                              // call cell manipulation methods
                              $cells->setBackground('##1a8fcb');
                              $cells->setFontColor('#ffffff');
                              $cells->setFontSize(18);
                              $cells->setFontWeight('bold');

                          });
                          $sheet->row(2, function($cells) {

                              // call cell manipulation methods
                              
                              $cells->setFontColor('#dedede');
                              $cells->setFontSize(18);
                              $cells->setFontWeight('bold');

                          });

                          $header3 = ['','','',''];

                          $headers = ['Employee Name', 'Position','Immediate Head','Program'];

                          $productionDates = [];
                          $ct = 0;
                          
                          $d = Carbon::parse($cutoffStart->format('Y-m-d'),'Asia/Manila');

                          foreach($allDTR as $employeeDTR)
                          {
                            //---- setup headers first
                            $overAllTotal = 0;
                            if ($ct==0)
                            {
                              do
                              {
                                array_push($productionDates, $d->format('Y-m-d'));
                                array_push($headers, $d->format('m/d'));
                                
                                array_push($header3, $d->format('l') );
                                array_push($header3, ' ' );array_push($header3, ' ' );array_push($header3, ' ' );array_push($header3, ' ' );
                                array_push($headers, 'Start time');
                                array_push($headers, 'End time');
                                array_push($headers, 'Type');
                                array_push($headers, 'Remarks');
                                $d->addDay();
                              }while($d->format('Y-m-d') <= $cutoffEnd->format('Y-m-d')); //all production dates

                              array_push($headers,"TOTAL");

                              $sheet->appendRow($header3);
                              $sheet->appendRow($headers);
                              $sheet->row(3, function($cells) {
                                $cells->setFontSize(17);
                                $cells->setFontWeight('bold');
                                $cells->setAlignment('center');
                              });
                              $sheet->row(4, function($cells) {
                                $cells->setFontSize(18);
                                $cells->setFontWeight('bold');
                                $cells->setAlignment('center');
                              });
                              $ct++;

                              goto addFirstEmployee;


                            }else
                            {

                              addFirstEmployee:

                              $i = 0;
                              $totalHours = 0;
                              $arr = [];

                              $arr[$i] = $employeeDTR->first()->lastname.", ".$employeeDTR->first()->firstname." ".$employeeDTR->first()->middlename; $i++;
                              $arr[$i] = $employeeDTR->first()->jobTitle; $i++;
                              $arr[$i] = $employeeDTR->first()->leaderFname." ".$employeeDTR->first()->leaderLname; $i++;
                              $arr[$i] = $employeeDTR->first()->program; $i++;

                              foreach ($productionDates as $prodDate) 
                              {
                                 $entry = collect($employeeDTR)->where('productionDate',$prodDate);

                                 if (count($entry) > 0)
                                 {
                                  $e = strip_tags($entry->first()->OT_approved);
                                  $o = $entry->first()->OT_id;
                                  if ( !empty($o) )
                                  {
                                    $ot = User_OT::find($entry->first()->OT_id);

                                    switch ($ot->billedType) {
                                      case '1':{ $otType = "Billed"; }break;
                                      case '2':{ $otType = "Non-Billed"; }break;
                                      case '3':{ $otType = "Patch"; }break;
                                      default:{ $otType = "Billed"; }break;
                                    }
                                    
                                    $totalHours += (float)$e;
                                    $overAllTotal += $totalHours;

                                    $arr[$i] = (float)$e; $i++; 
                                    $arr[$i] = $ot->timeStart; $i++;
                                    $arr[$i] = $ot->timeEnd; $i++;
                                    $arr[$i] = $otType; $i++;
                                    $arr[$i] = $ot->reason; $i++;

                                  }else
                                  {
                                    $arr[$i] = 0.0; $i++; 
                                    $arr[$i] = 0.0; $i++; 
                                    $arr[$i] = 0.0; $i++; 
                                    $arr[$i] = 0.0; $i++; 
                                    $arr[$i] = 0.0; $i++; 
                                   
                                  }
                                  
                                 
                                  
                                  

                                 }else
                                 {
                                  $arr[$i] = 0.0; $i++; 
                                    $arr[$i] = 0.0; $i++; 
                                    $arr[$i] = 0.0; $i++; 
                                    $arr[$i] = 0.0; $i++; 
                                    $arr[$i] = 0.0; $i++; 
                                 }
                              }

                              $arr[$i]= number_format($totalHours,2);
                              $sheet->appendRow($arr); $ct++;

                             


                            }//end if else not initial header setup
                            

                          }//end foreach employee
                            // Freeze the first column

                          // $sheet->setColumnFormat(array(
                          //   'E' => '0.00','F' => '0.00','G' => '0.00','H' => '0.00','I' => '0.00','J' => '0.00','K' => '0.00','L' => '0.00','M' => '0.00','N' => '0.00','O' => '0.00','P' => '0.00','Q' => '0.00','R' => '0.00','S' => '0.00','T' => '0.00','U' => '0.00'));

                          $sheet->cells("E3:CG".($ct+4), function($cells) {
                            $cells->setAlignment('center');
                          });

                          $sheet->freezeFirstColumn();


                        }); //end DTR Summary sheet

                      
                      //***** TARDINESS *********
                      $excel->sheet("Undertime", function($sheet) use ($program, $allDTR, $cutoffStart, $cutoffEnd, $headers,$payday)
                        {
                          $header1 = ['Open Access BPO | DTR Summary','','','','','','','','','','','','','','',''];
                          $header2 = [$cutoffStart->format('M d Y')." to ". $cutoffEnd->format('M d Y') ,'Program/Department: ',strtoupper($program->name),'','','','','','','','','','','','',''];

                          $sheet->setFontSize(17);
                          $sheet->appendRow($header1);
                          $sheet->appendRow($header2);




                          $sheet->cells('A1:P2', function($cells) {

                              // call cell manipulation methods
                              $cells->setBackground('##1a8fcb');
                              $cells->setFontColor('#ffffff');
                              $cells->setFontSize(18);
                              $cells->setFontWeight('bold');

                          });
                          $sheet->row(2, function($cells) {

                              // call cell manipulation methods
                              
                              $cells->setFontColor('#dedede');
                              $cells->setFontSize(18);
                              $cells->setFontWeight('bold');

                          });

                          $header3 = ['','','',''];

                          $headers = ['Employee Name', 'Position','Immediate Head','Program'];

                          $productionDates = [];
                          $ct = 0;
                          
                          $d = Carbon::parse($cutoffStart->format('Y-m-d'),'Asia/Manila');

                          foreach($allDTR as $employeeDTR)
                          {
                            //---- setup headers first
                            $overAllTotal = 0;
                            if ($ct==0)
                            {
                              do
                              {
                                array_push($productionDates, $d->format('Y-m-d'));
                                array_push($headers, $d->format('m/d'));
                                array_push($header3, substr($d->format('l'), 0,3) );
                                $d->addDay();
                              }while($d->format('Y-m-d') <= $cutoffEnd->format('Y-m-d')); //all production dates

                              array_push($headers,"TOTAL UT (hrs)");

                              $sheet->appendRow($header3);
                              $sheet->appendRow($headers);
                              $sheet->row(3, function($cells) {
                                $cells->setFontSize(18);
                                $cells->setFontWeight('bold');
                                $cells->setAlignment('center');
                              });
                              $sheet->row(4, function($cells) {
                                $cells->setFontSize(17);
                                $cells->setFontWeight('bold');
                                $cells->setAlignment('center');
                              });
                              $ct++;

                              goto addFirstEmployee;


                            }else
                            {

                              addFirstEmployee:

                              $i = 0;
                              $totalHours = 0;
                              $arr = [];

                              $arr[$i] = $employeeDTR->first()->lastname.", ".$employeeDTR->first()->firstname." ".$employeeDTR->first()->middlename; $i++;
                              $arr[$i] = $employeeDTR->first()->jobTitle; $i++;
                              $arr[$i] = $employeeDTR->first()->leaderFname." ".$employeeDTR->first()->leaderLname; $i++;
                              $arr[$i] = $employeeDTR->first()->program; $i++;

                              foreach ($productionDates as $prodDate) 
                              {
                                 $entry = collect($employeeDTR)->where('productionDate',$prodDate);

                                 if (count($entry) > 0)
                                 {
                                  $e = strip_tags($entry->first()->UT);

                                      $arr[$i] = number_format((float)$e, 2, '.', ''); //."_num-".$totalHours;
                                      $totalHours += (float)$e;
                                      $overAllTotal += $totalHours;
                                    
                                  
                                 
                                  
                                  $i++;

                                 }else
                                 {
                                  $arr[$i] = '<unverified>'; $i++;
                                 }
                              }

                              $arr[$i]= number_format($totalHours,2);
                              $sheet->appendRow($arr); $ct++;

                             


                            }//end if else not initial header setup
                            

                          }//end foreach employee
                            // Freeze the first column

                          $sheet->setColumnFormat(array(
                            'E' => '0.00','F' => '0.00','G' => '0.00','H' => '0.00','I' => '0.00','J' => '0.00','K' => '0.00','L' => '0.00','M' => '0.00','N' => '0.00','O' => '0.00','P' => '0.00','Q' => '0.00','R' => '0.00','S' => '0.00','T' => '0.00','U' => '0.00'));

                          $sheet->cells("E3:U".($ct+4), function($cells) {
                            $cells->setAlignment('center');
                          });

                          $sheet->freezeFirstColumn();


                        }); //end DTR Summary sheet
              
              })->export('xls');return "Download";

      } //end else return Billables  

      



             

      // return response()->json(['ok'=>true, 'dtr'=>$allDTR]);
      // return view ('under-construction');

    }



    public function financeReports()
    {
      $cutoffData = $this->getCutoffStartEnd();
      $cutoffStart = $cutoffData['cutoffStart'];//->cutoffStart;
      $cutoffEnd = $cutoffData['cutoffEnd'];

      $type = Input::get('type');

       //Timekeeping Trait
      $payrollPeriod = $this->getPayrollPeriod($cutoffStart,$cutoffEnd);
      $paycutoffs = Paycutoff::orderBy('toDate','DESC')->get();

      DB::connection()->disableQueryLog();
      $allProgram = DB::table('campaign')->select('id','name','hidden')->where('hidden',null)->
                          where([
                            ['campaign.id', '!=','26'], //wv
                            ['campaign.id', '!=','35'], //ceb

                          ])->orderBy('name')->get();//
        /*$byTL = collect($allUsers)->groupBy('tlID');
        $allTL = $byTL->keys();*/
        

        $correct = Carbon::now('GMT+8'); //->timezoneName();

           if($this->user->id !== 564 ) {
              $file = fopen('public/build/rewards.txt', 'a') or die("Unable to open logs");
                fwrite($file, "-------------------\n Viewed Finance_DTRsheets on " . $correct->format('M d h:i A'). " by [". $this->user->id."] ".$this->user->lastname."\n");
                fclose($file);
            } 
        
      

      if($type == 't'){
        $stat = Input::get('stat');
        return view('timekeeping.financeTraineeReport',compact('payrollPeriod','paycutoffs','allProgram','stat'));
      }
      else
        return view('timekeeping.financeReport',compact('payrollPeriod','paycutoffs','allProgram'));

    }

    public function finance_JPS()
    {
      DB::connection()->disableQueryLog();

      $templates = collect([
                              ['id'=>1,'name'=>'Overtime'],
                              ['id'=>2,'name'=>'Leaves'],
                              ['id'=>3,'name'=>'Change Shift Schedules'],
                              ['id'=>4,'name'=>'Work Schedules'],
                              ['id'=>5,'name'=>'Ops Worked Holiday(s)'],

      ]);
      


      $cutoffData = $this->getCutoffStartEnd();
      $cutoffStart = $cutoffData['cutoffStart'];//->cutoffStart;
      $cutoffEnd = $cutoffData['cutoffEnd'];
      //Timekeeping Trait
      $payrollPeriod = $this->getPayrollPeriod($cutoffStart,$cutoffEnd);
      $paycutoffs = Paycutoff::orderBy('toDate','DESC')->get();

      
      $allUsers = DB::table('users')->where([
                    ['status_id', '!=', 6],
                    ['status_id', '!=', 7],
                    ['status_id', '!=', 8],
                    ['status_id', '!=', 9],
                    ['users.status_id', '!=', 13],
                    ['users.status_id', '!=', 16],
                ])->
                  leftJoin('team','team.user_id','=','users.id')->
                  leftJoin('campaign','team.campaign_id','=','campaign.id')->
                  leftJoin('immediateHead_Campaigns','team.immediateHead_Campaigns_id','=','immediateHead_Campaigns.id')->
                  leftJoin('immediateHead','immediateHead_Campaigns.immediateHead_id','=','immediateHead.id')->
                  leftJoin('positions','users.position_id','=','positions.id')->
                  leftJoin('floor','team.floor_id','=','floor.id')->
                  select('users.id', 'users.firstname','users.lastname','users.nickname','users.dateHired','positions.name as jobTitle','campaign.id as campID', 'campaign.name as program','immediateHead_Campaigns.id as tlID', 'immediateHead.firstname as leaderFname','immediateHead.lastname as leaderLname','users.employeeNumber','floor.name as location')->orderBy('users.lastname')->get();

       

        $correct = Carbon::now('GMT+8'); //->timezoneName();

           if($this->user->id !== 564 ) {
              $file = fopen('public/build/rewards.txt', 'a') or die("Unable to open logs");
                fwrite($file, "-------------------\n Viewed Finance_DTRsheets on " . $correct->format('M d h:i A'). " by [". $this->user->id."] ".$this->user->lastname."\n");
                fclose($file);
            } 
        
      

      return view('timekeeping.finance_JPS',compact('payrollPeriod','paycutoffs','templates'));

    }


    public function finance_getJPs(Request $request)
    {
      //------ Template type 1= OT | 2= Leaves | 3= CWS | 4=Work sched
      switch ($request->template) {
        case 1: $result = $this->getAllOT($request->cutoff,1,$this->user); break;
        case 2: $result = $this->getAllLeaves($request->cutoff,1); break;
        case 3: ($request->DTRsummary) ? $result = $this->getAllCWS($request->cutoff,1,1) : $result = $this->getAllCWS($request->cutoff,1,null); break;
        case 4: $result = $this->getAllWorksched($request->cutoff,1,0); break;

        case 5: $result = $this->getAllWorkedHolidays($request->cutoff,1); break;
        case 6: $result = $this->getAllWorksched($request->cutoff,1,1); break; //Work sched regardless kung locked or unlocked
      }

      

      return $result;
      /*$jps = $result[0];
      $sched = $this->getUserWorksched($jps[0]->userID,$jps[0]->productionDate);
      return response()->json(['sched'=>$sched]);*/

    }

    public function finance_dlJPS(Request $request)
    {
      // $dtr = $request->dtr;
      $cutoff = $request->cutoffstart."_".$request->cutoffend;
      DB::connection()->disableQueryLog();
      $cutoffStart = Carbon::parse($request->cutoffstart,'Asia/Manila');
      $cutoffEnd = Carbon::parse($request->cutoffend,'Asia/Manila');
      

      //------ Template type 1= OT | 2= Leaves | 3= CWS
      switch ($request->template) {
        case '1': { $jpsData = $this->getAllOT($cutoff,0,$this->user);} break;
        case '2': { $jpsData = $this->getAllLeaves($cutoff,0); } break;
        case '3': { 
                    if ($request->DTRsummary) 
                      $jpsData = $this->getAllCWS($cutoff,0,1); 
                    else 
                      $jpsData = $this->getAllCWS($cutoff,0,null); 

                    //$jpsData = $result; 
                  } break;
        case '4': {$jpsData = $this->getAllWorksched($cutoff,0,0);} break;
        case '5': {$jpsData = $this->getAllWorkedHolidays($cutoff,0); } break;
        case '6': {$result = $this->getAllWorksched($cutoff,0,1); $jpsData = $result[0]['unlocks']; $allEmp = $result[0]['allEmp']; $allUsers = $result[0]['allUsers'];} break;//;
      }

      
      //return $result;

      
      $template = $request->template;

      switch ($template) {
        case '1': { $headers = ['EmployeeCode', 'EmployeeName','ShiftDate','StartDate','StartTime','EndDate','EndTime','Status','HoursFiled', 'HoursApproved']; $type="Overtime"; } break;
        case '2': { $headers = ['EmployeeCode', 'EmployeeName','LeaveDate','LeaveCode','Quantity','Status','Comment', 'DateFiled','Approver Remarks']; $type="LeaveFiling";} break;
        case '3': { 
                      $type="ChangeShiftSchedules";
                      ($request->DTRsummary) ? $headers = ['AccessCode', 'EmployeeName','Program', 'ShiftDate','Old Schedule','New Schedule','Status','Notes','Approver'] : $headers = ['EmployeeCode', 'EmployeeName','ShiftDate','Status','CurrentDailySchedule','NewDailySchedule','CurrentDayType','NewDayType'];  
                  } break; 
        case '4': { $headers = ['EmployeeCode', 'EmployeeName','ShiftDate','Status','CurrentDailySchedule','NewDailySchedule','CurrentDayType','NewDayType']; $type="WorkSchedules"; } break; 

        case '5': { $headers = ['EmployeeCode', 'EmployeeName','ShiftDate','StartDate','StartTime','EndDate','EndTime','Status','HoursFiled', 'HoursApproved']; $type="Overtime"; } break;
        case '6': { $headers = ['EmployeeCode', 'EmployeeName','Program','Immediate Head','Unlocked Date','DTR Sheet']; $type="Unlocked DTRs"; } break; 
      
      }

      $description = $type." for cutoff: ".$cutoffStart->format('M d')." to ".$cutoffEnd->format('M d');

      /*$jps = $jpsData[0];
      $sched = $this->getUserWorksched($jps[0]->userID,$jsp[0]->productionDate);
      return response()->json(['sched'=>$sched]);*/

      $correct = Carbon::now('GMT+8'); 

      if ($template == '1') // OVERTIME
      {

        if($this->user->id !== 564 ) {
              $file = fopen('public/build/rewards.txt', 'a') or die("Unable to open logs");
                fwrite($file, "-------------------\n JPS_".$type." cutoff: -- ".$cutoffStart->format('M d')." on " . $correct->format('M d h:i A'). " by [". $this->user->id."] ".$this->user->lastname."\n");
                fclose($file);
        } 

        Excel::create($type."_".$cutoffStart->format('M-d'),function($excel) use($type, $jpsData, $cutoffStart, $cutoffEnd, $headers,$description) 
              {
                      $excel->setTitle($cutoffStart->format('Y-m-d').' to '. $cutoffEnd->format('Y-m-d').'_'.$type);
                      $excel->setCreator('Programming Team')
                            ->setCompany('OpenAccessBPO');

                      // Call them separately
                      $excel->setDescription($description);

                      $excel->sheet("Sheet1", function($sheet) use ($type, $jpsData, $cutoffStart, $cutoffEnd, $headers,$description)
                      {
                        $sheet->appendRow($headers);      

                        $arr = [];

                        foreach($jpsData as $jps)
                        {
                          $i = 0;

                          if(count($jps) > 1)
                          {
                            foreach ($jps as $j) 
                            {
                              $c=0;
                              $arr[$c] = $j->accesscode; $c++;
                              $arr[$c] = $j->lastname.", ".$j->firstname; $c++;


                              //-----

                              $isParttimer=false;
                              
                              $theHoliday = Holiday::where('holidate',$j->productionDate)->get();
                              $isHoliday = (count($theHoliday) > 0) ? true : false;

                              $isBackoffice = ( Campaign::find(Team::where('user_id',$j->userID)->first()->campaign_id)->isBackoffice ) ? true : false;

                              $isDavao = ( Team::where('user_id',$j->userID)->first()->floor_id == 9) ? true : false;


                              //-----we get first employee's schedule from locked DTR

                              $isLocked = false;
                              $sched = $this->getUserWorksched($j->userID,$j->productionDate);
                              if(count($sched) > 0)
                              {
                                $isLocked = true;
                                $hoursFiled = $j->billable_hours; $hoursApproved =$j->filed_hours;
                                if ( ($sched[0]->workshift !== '* RD * - * RD *') && (strpos($sched[0]->workshift, 'RD') === false) )  
                                {
                                  $wshift = explode('-',$sched[0]->workshift);
                                  $s = Carbon::parse($j->productionDate." ".$j->timeStart,'Asia/Manila');
                                  $u = User::find($j->userID);
                                  ($u->status_id == 12 || $u->status_id == 14) ? $isParttimer = true : $isParttimer=false;

                                  if($isHoliday) 
                                  {
                                    //****** we first check if holiday is exclusive for Davao
                                    if($theHoliday->first()->holidayType_id == 4)
                                    {
                                      if ($isDavao)
                                      {
                                        $startDate = Carbon::parse($j->productionDate." ".$wshift[0],'Asia/Manila');

                                        if($isBackoffice){
                                          $startTime = Carbon::parse($startDate->format('Y-m-d')." ".$j->timeStart,'Asia/Manila');
                                          $endDate =  Carbon::parse($startDate->format('Y-m-d')." ".$j->timeStart,'Asia/Manila')->addHours($j->filed_hours)->addHours(1);
                                          $endTime = Carbon::parse($endDate->format('Y-m-d')." ".$j->timeEnd,'Asia/Manila')->addHours(1);
                                        }
                                        else {
                                            $startTime = $startDate; //Carbon::parse($startDate->format('Y-m-d')." ".$wshift[0],'Asia/Manila');
                                            $endDate =  Carbon::parse($startDate->format('Y-m-d')." ".$j->timeStart,'Asia/Manila')->addHours($j->filed_hours);
                                            $endTime = Carbon::parse($endDate->format('Y-m-d')." ".$j->timeEnd,'Asia/Manila')->addHours(1);
                                          
                                        }

                                      }
                                      else //hindi taga davao, so wala dapat syang holiday
                                      {

                                      }

                                    }
                                    else //regular holiday
                                    {
                                      $startDate = Carbon::parse($j->productionDate." ".$wshift[0],'Asia/Manila');

                                      if($isBackoffice){

                                        // check mo muna kung logIN is before workshift, if yes : disregard early log but get StartShift
                                        // if not, meaning late sya for holiday full pay
                                        if(Carbon::parse($startDate->format('Y-m-d')." ".$j->timeStart,'Asia/Manila')->format('Y-m-d H:i') < $startDate->format('Y-m-d H:i') )
                                          $startTime = Carbon::parse($j->productionDate." ".$wshift[0],'Asia/Manila');
                                        else
                                          $startTime = Carbon::parse($startDate->format('Y-m-d')." ".$j->timeStart,'Asia/Manila');

                                        
                                        $endDate =  Carbon::parse($startDate->format('Y-m-d')." ".$j->timeStart,'Asia/Manila')->addHours($j->filed_hours)->addHours(1);
                                        $endTime = Carbon::parse($endDate->format('Y-m-d')." ".$j->timeEnd,'Asia/Manila')->addHours(1);
                                      }
                                      else {
                                          $startTime = $startDate; //Carbon::parse($startDate->format('Y-m-d')." ".$wshift[0],'Asia/Manila');
                                          $endDate =  Carbon::parse($startDate->format('Y-m-d')." ".$j->timeStart,'Asia/Manila')->addHours($j->filed_hours);
                                          $endTime = Carbon::parse($endDate->format('Y-m-d')." ".$j->timeEnd,'Asia/Manila')->addHours(1);
                                        
                                      }

                                    }
                                    
                                    

                                  }
                                  else {
                                    $startDate = Carbon::parse($j->productionDate." ".$wshift[0],'Asia/Manila')->addHours(9);
                                    $startTime = Carbon::parse($startDate->format('Y-m-d')." ".$j->timeStart,'Asia/Manila');
                                    $endDate =  Carbon::parse($startDate->format('Y-m-d')." ".$j->timeStart,'Asia/Manila')->addHours($j->filed_hours);
                                    $endTime = Carbon::parse($endDate->format('Y-m-d')." ".$j->timeEnd,'Asia/Manila');
                                  }

                                  
                                  
                                }
                                else if ( ($sched[0]->workshift === '* RD * - * RD *') ||  strpos($sched[0]->workshift, 'RD') !== false )
                                {
                                  $wshift = explode('-',$sched[0]->workshift);
                                  $s = Carbon::parse($j->productionDate." ".$j->timeStart,'Asia/Manila');
                                  $startDate = $s;
                                  $endDate =  Carbon::parse($startDate->format('Y-m-d')." ".$j->timeStart,'Asia/Manila')->addHours($j->filed_hours);
                                  $startTime = Carbon::parse($startDate->format('Y-m-d')." ".$j->timeStart,'Asia/Manila');

                                  if( $j->filed_hours >= 5.0)
                                    $endTime = Carbon::parse($endDate->format('Y-m-d')." ".$j->timeEnd,'Asia/Manila')->addHours(1);
                                  else
                                    $endTime = Carbon::parse($endDate->format('Y-m-d')." ".$j->timeEnd,'Asia/Manila');
                                  
                                }
                                else
                                {

                                  $s = Carbon::parse($j->productionDate." ".$j->timeStart,'Asia/Manila');
                                  $e =  Carbon::parse($j->productionDate." ".$j->timeEnd,'Asia/Manila');//->addHours($jps[0]->filed_hours);
                                  $endDate =  Carbon::parse($j->productionDate." ".$j->timeStart,'Asia/Manila')->addHours($j->filed_hours);
                                  $startDate = $s;
                                  $startTime = $s;
                                  $endTime = $e;
                                  $wshift = array( $j->timeStart,$j->timeEnd );
                                }
                                

                              }
                              else
                              {
                                $s = Carbon::parse($j->productionDate." ".$j->timeStart,'Asia/Manila');
                                $e =  Carbon::parse($j->productionDate." ".$j->timeEnd,'Asia/Manila');//->addHours($jps[0]->filed_hours);
                                $endDate =  Carbon::parse($j->productionDate." ".$j->timeStart,'Asia/Manila')->addHours($j->filed_hours);
                                $startDate = $s;
                                $startTime = $s;
                                $endTime = $e;
                                $hoursFiled = $j->billable_hours; $hoursApproved =$j->filed_hours;

                              }

                              //***------- new for HOLIDAY OT------------ ***
                              // if HOLIDAY TODAY, check if (backoffice) -> nothing changes sa filed OT
                              // else ops sya
                              // if sched nya today is * RD * -> nothing changes
                              // else add 8hrs / 4hrs kung PT
                              


                              if ($isHoliday)
                              {
                                //****** we first check if holiday is exclusive for Davao
                                    if($theHoliday->first()->holidayType_id == 4)
                                    {
                                      if ($isDavao)
                                      {
                                        if ($isBackoffice){ $hoursFiled = $j->billable_hours; $hoursApproved =$j->filed_hours; goto proceedSaving1; }
                                        else
                                        {
                                          if(count($sched) > 0)
                                          {
                                            if (($sched[0]->workshift === '* RD * - * RD *') || strpos($sched[0]->workshift, 'RD') !== false) {  $hoursFiled = $j->billable_hours; $hoursApproved =$j->filed_hours; goto proceedSaving1; }
                                            else
                                            {
                                              // check muna kung PT or not
                                              

                                              if ($isParttimer)
                                              {
                                                
                                                $hoursFiled = $j->billable_hours + 4.0;
                                                $hoursApproved = $j->filed_hours + 4.0;
                                              }
                                              else
                                              {
                                                
                                                $hoursFiled = $j->billable_hours + 8.0;
                                                $hoursApproved = $j->filed_hours + 8.0;

                                              }
                                            }

                                          }
                                          else{ $hoursFiled = $j->billable_hours; $hoursApproved =$j->filed_hours; goto proceedSaving1;}

                                        }
                                        

                                        
                                      }
                                      else //hindi taga davao, so wala dapat syang holiday
                                      {
                                        $hoursFiled = $j->billable_hours; $hoursApproved =$j->filed_hours; goto proceedSaving1;

                                      }

                                    }
                                    else //regular holiday lang
                                    {
                                      if ($isBackoffice){ $hoursFiled = $j->billable_hours; $hoursApproved =$j->filed_hours; goto proceedSaving1; }
                                      else
                                      {
                                        if(count($sched) > 0)
                                        {
                                          if (($sched[0]->workshift === '* RD * - * RD *')||strpos($sched[0]->workshift, 'RD') !== false) {  $hoursFiled = $j->billable_hours; $hoursApproved =$j->filed_hours; goto proceedSaving1; }
                                          else
                                          {
                                            // check muna kung PT or not
                                            

                                            if ($isParttimer)
                                            {
                                              
                                              $hoursFiled = $j->billable_hours + 4.0;
                                              $hoursApproved = $j->filed_hours + 4.0;
                                            }
                                            else
                                            {
                                              
                                              $hoursFiled = $j->billable_hours + 8.0;
                                              $hoursApproved = $j->filed_hours + 8.0;

                                            }
                                          }

                                        }
                                        else{ $hoursFiled = $j->billable_hours; $hoursApproved =$j->filed_hours; goto proceedSaving1;}

                                      }

                                    }

                                
                                
                              }
                              else
                              {
                                $hoursFiled = $j->billable_hours; $hoursApproved =$j->filed_hours; goto proceedSaving1;

                              }
                              
                              //***------- end for HOLIDAY OT------------ ***

                              proceedSaving1:

                                  if ($isLocked)
                                  {
                                    //*** ShiftDate
                                    $arr[$c] = $s->format('m/d/Y'); $c++;



                                    //*** StartDate
                                    $arr[$c] = $startDate->format('m/d/Y'); $c++;

                                    //*** StartTime
                                    $arr[$c] = $startTime->format('h:i A'); $c++;

                                    //*** EndDate
                                    $arr[$c] = $endDate->format('m/d/Y'); $c++;

                                    //*** EndTime
                                    $arr[$c] = $endTime->format('h:i A'); $c++;

                                    //*** Status
                                    if($j->isApproved == '1') $stat = "Approved";
                                    else if ($j->isApproved == '0') $stat = "Denied";
                                    else $stat = "Pending Approval";

                                    $arr[$c] = $stat; $c++;




                                    //*** HoursFiled
                                    $arr[$c] = $hoursApproved; $c++; //$hoursFiled;

                                     //*** HoursApproved
                                    $arr[$c] = $hoursApproved; $c++;

                                

                                    $sheet->appendRow($arr);

                                  }
                                  else
                                     skipSaving:
                                    //do nothing;
                                  

                             


                              
                            }

                          }
                          else
                          {
                            //-----
                            $arr[$i] = $jps[0]->accesscode; $i++;
                            $arr[$i] = $jps[0]->lastname.", ".$jps[0]->firstname; $i++;

                            $isParttimer=false;
                            

                            $theHoliday = Holiday::where('holidate',$jps[0]->productionDate)->get();
                            $isHoliday = (count($theHoliday) > 0) ? true : false;
                            $isBackoffice = ( Campaign::find(Team::where('user_id',$jps[0]->userID)->first()->campaign_id)->isBackoffice ) ? true : false;
                            $isDavao = ( Team::where('user_id',$jps[0]->userID)->first()->floor_id == 9) ? true : false;


                            //-----we get first employee's schedule from locked DTR

                            $sched = $this->getUserWorksched($jps[0]->userID,$jps[0]->productionDate);
                            if(count($sched) > 0)
                            {
                              if (($sched[0]->workshift !== '* RD * - * RD *') && strpos($sched[0]->workshift, 'RD') === false)
                              {
                                $wshift = explode('-',$sched[0]->workshift);
                                $s = Carbon::parse($jps[0]->productionDate." ".$jps[0]->timeStart,'Asia/Manila');

                                if($isHoliday) {
                                  $u = User::find($jps[0]->userID);
                                  ($u->status_id == 12 || $u->status_id == 14) ? $isParttimer = true : $isParttimer=false;
                                  $startDate = Carbon::parse($jps[0]->productionDate." ".$wshift[0],'Asia/Manila');

                                  if($isBackoffice)
                                    $startTime = Carbon::parse($startDate->format('Y-m-d')." ".$jps[0]->timeStart,'Asia/Manila');
                                  else
                                    $startTime = $startDate; //Carbon::parse($startDate->format('Y-m-d')." ".$wshift[0],'Asia/Manila');
                                }
                                else {
                                  $startDate = Carbon::parse($jps[0]->productionDate." ".$wshift[0],'Asia/Manila')->addHours(9);
                                  $startTime = Carbon::parse($startDate->format('Y-m-d')." ".$jps[0]->timeStart,'Asia/Manila');
                                }

                                $endDate =  Carbon::parse($startDate->format('Y-m-d')." ".$jps[0]->timeStart,'Asia/Manila')->addHours($jps[0]->filed_hours);
                                
                                $endTime = Carbon::parse($endDate->format('Y-m-d')." ".$jps[0]->timeEnd,'Asia/Manila');
                                
                              }
                              else if (($sched[0]->workshift === '* RD * - * RD *') || strpos($sched[0]->workshift, 'RD') !== false)
                              {
                                $wshift = explode('-',$sched[0]->workshift);
                                $s = Carbon::parse($jps[0]->productionDate." ".$jps[0]->timeStart,'Asia/Manila');
                                $startDate = $s;
                                $endDate =  Carbon::parse($startDate->format('Y-m-d')." ".$jps[0]->timeStart,'Asia/Manila')->addHours($jps[0]->filed_hours);
                                $startTime = Carbon::parse($startDate->format('Y-m-d')." ".$jps[0]->timeStart,'Asia/Manila');

                                if( $jps[0]->filed_hours >= 5.0)
                                  $endTime = Carbon::parse($endDate->format('Y-m-d')." ".$jps[0]->timeEnd,'Asia/Manila')->addHours(1);
                                else
                                  $endTime = Carbon::parse($endDate->format('Y-m-d')." ".$jps[0]->timeEnd,'Asia/Manila');
                                
                              }
                              else
                              {

                                $s = Carbon::parse($jps[0]->productionDate." ".$jps[0]->timeStart,'Asia/Manila');
                                $e =  Carbon::parse($jps[0]->productionDate." ".$jps[0]->timeEnd,'Asia/Manila');//->addHours($jps[0]->filed_hours);
                                $endDate =  Carbon::parse($jps[0]->productionDate." ".$jps[0]->timeStart,'Asia/Manila')->addHours($jps[0]->filed_hours);
                                $startDate = $s;
                                $startTime = $s;
                                $endTime = $e;
                                $wshift = array( $jps[0]->timeStart,$jps[0]->timeEnd );
                              }
                              

                            }
                            else
                            {
                              $s = Carbon::parse($jps[0]->productionDate." ".$jps[0]->timeStart,'Asia/Manila');
                              $e =  Carbon::parse($jps[0]->productionDate." ".$jps[0]->timeEnd,'Asia/Manila');//->addHours($jps[0]->filed_hours);
                              $endDate =  Carbon::parse($jps[0]->productionDate." ".$jps[0]->timeStart,'Asia/Manila')->addHours($jps[0]->filed_hours);
                              $startDate = $s;
                              $startTime = $s;
                              $endTime = $e;
                              $hoursFiled = $jps[0]->billable_hours; $hoursApproved =$jps[0]->filed_hours;

                            }

                            //***------- new for HOLIDAY OT------------ ***
                            // if HOLIDAY TODAY, check if (backoffice) -> nothing changes sa filed OT
                            // else ops sya
                            // if sched nya today is * RD * -> nothing changes
                            // else add 8hrs / 4hrs kung PT

                            if ($isHoliday)
                            {
                              //****** we first check if holiday is exclusive for Davao
                                  if($theHoliday->first()->holidayType_id == 4)
                                  {
                                    if ($isDavao)
                                    {
                                      if ($isBackoffice){ $hoursFiled = $jps[0]->billable_hours; $hoursApproved =$jps[0]->filed_hours; goto proceedSaving; }
                                      else
                                      {
                                        if(count($sched) > 0)
                                        {
                                          if (($sched[0]->workshift === '* RD * - * RD *')||strpos($sched[0]->workshift, 'RD') !== false) {  $hoursFiled =$jps[0]->billable_hours; $hoursApproved =$jps[0]->filed_hours; goto proceedSaving; }
                                          else
                                          {
                                            // check muna kung PT or not
                                            

                                             if ($isParttimer)
                                              {
                                                
                                                $hoursFiled = $jps[0]->billable_hours + 4.0;
                                                $hoursApproved = $jps[0]->filed_hours + 4.0;
                                              }
                                              else
                                              {
                                                
                                                $hoursFiled = $jps[0]->billable_hours + 8.0;
                                                $hoursApproved = $jps[0]->filed_hours + 8.0;

                                              }
                                          }

                                        }
                                        else{ $hoursFiled = $jps[0]->billable_hours; $hoursApproved =$jps[0]->filed_hours; goto proceedSaving;}

                                      }
                                      

                                      
                                    }
                                    else //hindi taga davao, so wala dapat syang holiday
                                    {
                                      $hoursFiled = $jps[0]->billable_hours; $hoursApproved =$jps[0]->filed_hours; goto proceedSaving;

                                    }

                                  }
                                  else //regular holiday lang
                                  {
                                    if ($isBackoffice){ $hoursFiled = $jps[0]->billable_hours; $hoursApproved =$jps[0]->filed_hours; goto proceedSaving; }
                                    else
                                    {
                                      if(count($sched) > 0)
                                      {
                                        if (($sched[0]->workshift === '* RD * - * RD *')||strpos($sched[0]->workshift, 'RD') !== false) { $hoursFiled = $jps[0]->billable_hours; $hoursApproved =$jps[0]->filed_hours; goto proceedSaving; }
                                        else
                                        {
                                          // check muna kung PT or not
                                          

                                          if ($isParttimer)
                                          {
                                            
                                            $hoursFiled = $jps[0]->billable_hours + 4.0;
                                            $hoursApproved = $jps[0]->filed_hours + 4.0;
                                          }
                                          else
                                          {
                                            
                                            $hoursFiled = $jps[0]->billable_hours + 8.0;
                                            $hoursApproved = $jps[0]->filed_hours + 8.0;

                                          }
                                        }

                                      }
                                      else{ $hoursFiled = $jps[0]->billable_hours; $hoursApproved =$jps[0]->filed_hours; goto proceedSaving;}

                                    }

                                  }
                            }
                            else
                            {
                              $hoursFiled = $jps[0]->billable_hours; $hoursApproved =$jps[0]->filed_hours; goto proceedSaving;

                            }
                            
                            //***------- end for HOLIDAY OT------------ ***

                            proceedSaving:
                                //*** ShiftDate
                                $arr[$i] = $s->format('m/d/Y'); $i++;



                                //*** StartDate
                                $arr[$i] = $startDate->format('m/d/Y'); $i++;

                                //*** StartTime
                                $arr[$i] = $startTime->format('h:i A'); $i++;

                                //*** EndDate
                                $arr[$i] = $endDate->format('m/d/Y'); $i++;

                                //*** EndTime
                                $arr[$i] = $endTime->format('h:i A'); $i++;

                                //*** Status
                                if($jps[0]->isApproved == '1') $stat = "Approved";
                                else if ($jps[0]->isApproved == '0') $stat = "Denied";
                                else $stat = "Pending Approval";

                                $arr[$i] = $stat; $i++;




                                //*** HoursFiled
                                $arr[$i] = $hoursApproved; $i++; //$hoursFiled; 

                                 //*** HoursApproved
                                $arr[$i] = $hoursApproved; $i++;

                            

                            $sheet->appendRow($arr);

                          }

                        }//end foreach employee

                        
                      });//end sheet1

              })->export('xls');return "Download";
      }
      else if ($template == '2') // LEAVES
      {

        if($this->user->id !== 564 ) {
              $file = fopen('storage/uploads/log.txt', 'a') or die("Unable to open logs");
                fwrite($file, "-------------------\n JPS_".$type." cutoff: -- ".$cutoffStart->format('M d')." on " . $correct->format('M d h:i A'). " by [". $this->user->id."] ".$this->user->lastname."\n");
                fclose($file);
        } 

        //return response()->json(['results'=>$jpsData, 'headers'=>$headers]);

        Excel::create($type."_".$cutoffStart->format('M-d'),function($excel) use($type, $jpsData, $cutoffStart, $cutoffEnd, $headers,$description) 
              {
                      $excel->setTitle($cutoffStart->format('Y-m-d').' to '. $cutoffEnd->format('Y-m-d').'_'.$type);
                      $excel->setCreator('Programming Team')
                            ->setCompany('OpenAccessBPO');

                      // Call them separately
                      $excel->setDescription($description);

                      $excel->sheet("Sheet1", function($sheet) use ($type, $jpsData, $cutoffStart, $cutoffEnd, $headers,$description)
                      {
                        $sheet->appendRow($headers);      

                        $arr = [];
                        $hasProdate=false;

                        foreach($jpsData as $jps)
                        {
                          $i = 0;

                          if(count($jps['data']) > 1) //maraming instance ng leave
                          {
                            foreach ($jps['data'] as $j) 
                            {
                              $c=0;
                              //AccessCode', 'EmployeeName','LeaveDate','LeaveCode','Quantity','Status','Comment', 'DateFiled','Approver Remarks

                              //******* we need to check first yung mga more than 1day
                              if( $jps['type'] == 'FL' &&  $j->totalCredits > 1)
                              {
                                //$c2 = $c;
                                for($f=0; $f < $j->totalCredits; $f++)
                                {
                                  //***** verify first kung pasok pa sa cutoff yung date ng leave
                                  $s = Carbon::parse($j->leaveStart,'Asia/Manila')->addDays($f);

                                  if( $s->format('Y-m-d') < $cutoffStart->format('Y-m-d'))
                                  {
                                    //if start ng leave eh past na ng cutoff start, gawin mong start ung cutoff mismo
                                    $s = Carbon::parse($cutoffStart->format('Y-m-d H:i:s'),'Asia/Manila')->addDays($f);
                                    $e = Carbon::parse($cutoffStart->format('Y-m-d H:i:s'),'Asia/Manila')->addDays($f)->addHours(9);

                                  }else
                                  {
                                    //pasok within the range yung leave sa cutoff period
                                    $e = Carbon::parse($cutoffStart->format('Y-m-d H:i:s'),'Asia/Manila')->addDays($f)->addHours(9);
                                    //$e =  Carbon::parse($j->leaveEnd,'Asia/Manila');

                                  }
                                  
                                  $pd = $s->format('m/d/Y');


                                  if( $s->format('Y-m-d H:i') <= $cutoffEnd->format('Y-m-d H:i'))
                                  {
                                    $arr[$c] = $j->accesscode; $c++;
                                    $arr[$c] = $j->lastname.", ".$j->firstname; $c++;


                                    ($jps['type'] == 'FL') ? $leaveCode = $j->leaveType : $leaveCode = $jps['type'];
                                    $qty = 1; //$j->totalCredits;

                                    

                                    //*** LeaveDate
                                    $arr[$c] = $pd; $c++; //$s->format('m/d/Y'); $c++;

                                    //*** LeaveCOde
                                    if($leaveCode == 'ML'){
                                      $arr[$c] = "SSSML"; $c++;
                                    }else{
                                      $arr[$c] = $leaveCode; $c++;
                                    }
                                    
                                    

                                    //*** Quantity
                                    $arr[$c] = $qty; $c++;
                                    

                                    //*** Status
                                    if($j->isApproved == '1') $stat = "Approved";
                                    else if ($j->isApproved == '0') $stat = "Denied";
                                    else $stat = "Pending Approval";

                                    $arr[$c] = $stat; $c++;

                                    //*** Comment
                                    $arr[$c] = $j->notes; $c++;

                                    //*** Date Files
                                    $arr[$c] = date('m/d/Y', strtotime($j->created_at)); $c++;

                                    //remarks
                                    $arr[$c] = $stat; $c++;
                     

                                    $sheet->appendRow($arr);
                                    $c = 0;


                                  }//end IF ONLY pasok pa sa cutoff

                                  

                                }//end forloop


                              }else
                              {
                                //-------- check if VTO
                                if($jps['type'] == 'VTO')
                                {
                                  ($j->deductFrom == "AdvSL") ? $leaveCode = "SL" : $leaveCode = $j->deductFrom; 

                                  // kunin mo muna worksched
                                  $sched = $this->getUserWorksched($j->userID,$j->productionDate);
                                  if (count($sched) > 0 && ($sched[0]->workshift !== '* RD * - * RD *') )
                                  {
                                    $wsched = explode('-', $sched[0]->workshift);
                                    $startShift = Carbon::parse($j->productionDate." ". $wsched[0])->addHours(9);

                                    $s = Carbon::parse($startShift->format('Y-m-d')." ". $j->startTime,'Asia/Manila');
                                    $e = Carbon::parse($startShift->format('Y-m-d')." ". $j->endTime,'Asia/Manila');

                                  }
                                  else //walang locked DTR sched
                                  {
                                    $s = Carbon::parse($j->productionDate." ". $j->startTime,'Asia/Manila');
                                    $e = Carbon::parse($j->productionDate." ". $j->endTime,'Asia/Manila');

                                  }

                                  $qty = number_format((float)$j->totalHours*0.125,2);
                                  $pd =  date('m/d/Y',strtotime($j->productionDate));
                                  

                                }
                                else //VL | SL | LWOP | ML | SPL | PL | MC
                                {
                                    ($jps['type'] == 'FL') ? $leaveCode = $j->leaveType : $leaveCode = $jps['type'];//establish leave COde
                                    $qty = $j->totalCredits;

                                    //gawin mo lang kapag halfday leaves
                                    if ($j->totalCredits <= 0.5 )
                                    {
                                      $sched = $this->getUserWorksched($j->userID,date('Y-m-d',strtotime($j->leaveStart)));
                                      if (count($sched) > 0 && ($sched[0]->workshift !== '* RD * - * RD *') )
                                      {
                                        //need to check kung 1st half/2nd half of shift
                                        $wsched = explode('-', $sched[0]->workshift);

                                        if ($j->halfdayFrom == 3)
                                        {
                                          //if parttimer
                                          $u = User::find($j->userID);
                                          ($u->status_id == 12 || $u->status_id == 14) ? $isParttimer = true : $isParttimer=false;

                                          
                                          if($j->productionDate){
                                            $lstart = Carbon::parse($j->productionDate,'Asia/Manila');
                                            $hasProdate=true;
                                          }else{
                                            $lstart = Carbon::parse($j->leaveStart,'Asia/Manila');
                                          }

                                          if($isParttimer)
                                          {
                                            $pt = DB::table('pt_override')->where('user_id',$u->id)->get();
                                            

                                            if (count($pt) > 0)
                                            {
                                              if ( Carbon::parse($pt[0]->overrideEnd,'Asia/Manila') >= $lstart )
                                              {
                                                ($hasProdate) ? $s = Carbon::parse($lstart->format('Y-m-d')." ".$wsched[0],'Asia/Manila') : $s = Carbon::parse($lstart->format('Y-m-d')." ".$wsched[0],'Asia/Manila')->addHours(5);

                                              }
                                              else
                                              {
                                                ($hasProdate) ? $s = Carbon::parse($lstart->format('Y-m-d')." ".$wsched[0],'Asia/Manila') : $s = Carbon::parse($lstart->format('Y-m-d')." ".$wsched[0],'Asia/Manila')->addHours(2);

                                              }
                                            }
                                            else //partime schedule nga sya for today
                                            {
                                              ($hasProdate) ? $s = Carbon::parse($lstart->format('Y-m-d')." ".$wsched[0],'Asia/Manila') : $s = Carbon::parse($lstart->format('Y-m-d')." ".$wsched[0],'Asia/Manila')->addHours(2);
                                               

                                            }
                                          }
                                          
                                          else
                                          {
                                            ($hasProdate) ? $s = Carbon::parse($lstart->format('Y-m-d')." ".$wsched[0],'Asia/Manila') : $s = Carbon::parse($lstart->format('Y-m-d')." ".$wsched[0],'Asia/Manila')->addHours(5);
                                             
                                             //$e = Carbon::parse($jps['data'][0]->leaveEnd,'Asia/Manila');

                                          }

                                        }
                                        else //hindi sya start ng 2nd half
                                        {
                                          ($j->productionDate) ? $s = Carbon::parse($j->productionDate,'Asia/Manila') : $s = Carbon::parse($j->leaveStart,'Asia/Manila');
                                          
                                          $e =  Carbon::parse($j->leaveEnd,'Asia/Manila');

                                        }

                                      }
                                      else 
                                      {
                                        ($j->productionDate) ? $s = Carbon::parse($j->productionDate,'Asia/Manila') : $s = Carbon::parse($j->leaveStart,'Asia/Manila');
                                        
                                        $e =  Carbon::parse($j->leaveEnd,'Asia/Manila');//->addHours($jps['data'][0]->filed_hours);
                                      }

                                    }
                                    
                                    else //whole day leaves sya
                                    {
                                      $s = Carbon::parse($j->leaveStart,'Asia/Manila');
                                      $e =  Carbon::parse($j->leaveEnd,'Asia/Manila');

                                    }
                                    $pd = $s->format('m/d/Y');
                                }

                                //check mo muna kung pasok sa cutoff

                                // **fix for VTOs na tumawid ng araw (20th pero 21st nag end)
                                $tawid = Carbon::parse($cutoffEnd->format('Y-m-d'),'Asia/Manila')->addDays(1);
                                
                                //if( $s->format('Y-m-d') >= $cutoffStart->format('Y-m-d') && $e->format('Y-m-d') <= $cutoffEnd->format('Y-m-d'))
                                if( $s->format('Y-m-d') >= $cutoffStart->format('Y-m-d') && $e->format('Y-m-d') <= $tawid->format('Y-m-d'))
                                {
                                  $arr[$c] = $j->accesscode; $c++;
                                  $arr[$c] = $j->lastname.", ".$j->firstname; $c++;

                                  

                                 

                                  //*** LeaveDate
                                  $arr[$c] = $pd; $c++; //$s->format('m/d/Y'); $c++;

                                  //*** LeaveCOde
                                  //$arr[$c] = $leaveCode; $c++;
                                  //*** LeaveCOde
                                    if($leaveCode == 'ML'){
                                      $arr[$c] = "SSSML"; $c++;
                                    }else{
                                      $arr[$c] = $leaveCode; $c++;
                                    }
                                    
                                  

                                  //*** Quantity
                                  $arr[$c] = $qty; $c++;
                                  

                                  //*** Status
                                  if($j->isApproved == '1') $stat = "Approved";
                                  else if ($j->isApproved == '0') $stat = "Denied";
                                  else $stat = "Pending Approval";

                                  $arr[$c] = $stat; $c++;

                                  //*** Comment
                                  $arr[$c] = $j->notes; $c++;

                                  //*** Date Files
                                  $arr[$c] = date('m/d/Y', strtotime($j->created_at)); $c++;

                                  //remarks
                                  $arr[$c] = $stat; $c++;
                   

                                  $sheet->appendRow($arr);

                                }//end pasok sa cutoff
                                

                              }//end ng ifelse more than 1 day

                              
                              
                            }//end foreach jps as j

                          }
                          else
                          {
                            if( $jps['type']=='FL' && $jps['data'][0]->totalCredits > 1)
                            {
                              //AccessCode', 'EmployeeName','LeaveDate','LeaveCode','Quantity','Status','Comment', 'DateFiled','Approver Remarks
                              for($h=0; $h < $jps['data'][0]->totalCredits; $h++)
                              {
                                //($h==0) ? $i += $h : $i ++;
                                $s = Carbon::parse($jps['data'][0]->leaveStart,'Asia/Manila')->addDays($h);

                                if( $s->format('Y-m-d') < $cutoffStart->format('Y-m-d'))
                                {
                                  //if start ng leave eh past na ng cutoff start, gawin mong start ung cutoff mismo
                                  $s = Carbon::parse($cutoffStart->format('Y-m-d H:i:s'),'Asia/Manila')->addDays($f);
                                  $e = Carbon::parse($cutoffStart->format('Y-m-d H:i:s'),'Asia/Manila')->addDays($f)->addHours(9);

                                }else
                                {
                                  $e = Carbon::parse($cutoffStart->format('Y-m-d H:i:s'),'Asia/Manila')->addDays($f)->addHours(9);
                                  //$e = Carbon::parse($jps['data'][0]->leaveEnd,'Asia/Manila');

                                }
                                

                                $pd = $s->format('m/d/Y');

                                if($s->format('Y-m-d H:i') <= $cutoffEnd->format('Y-m-d H:i'))
                                {
                                  $arr[$i] = $jps['data'][0]->accesscode; $i++;
                                  $arr[$i] = $jps['data'][0]->lastname.", ".$jps['data'][0]->firstname; $i++;
                                  
                                  //establish leave COde

                                  ($jps['type'] == 'FL') ? $leaveCode = $jps['data'][0]->leaveType : $leaveCode = $jps['type'];
                                  $qty = 1; //$jps['data'][0]->totalCredits;

                                  

                                  //*** LeaveDate
                                  $arr[$i] = $pd; $i++; // $s->format('m/d/Y'); $i++;

                                  //*** LeaveCOde
                                  //$arr[$i] = $leaveCode; $i++;
                                  //*** LeaveCOde
                                    if($leaveCode == 'ML'){
                                      $arr[$i] = "SSSML"; $i++;
                                    }else{
                                      $arr[$i] = $leaveCode; $i++;
                                    }
                                    
                                  

                                  //*** Quantity
                                  $arr[$i] = $qty; $i++;
                                  

                                  //*** Status
                                  if($jps['data'][0]->isApproved == '1') $stat = "Approved";
                                  else if ($jps['data'][0]->isApproved == '0') $stat = "Denied";
                                  else $stat = "Pending Approval";

                                  $arr[$i] = $stat; $i++;

                                  //*** Comment
                                  $arr[$i] = $jps['data'][0]->notes; $i++;

                                  //*** Date Files
                                  $arr[$i] = date('m/d/Y', strtotime($jps['data'][0]->created_at)); $i++;

                                  //remarks
                                  $arr[$i] = $stat; $i++;

                                  $sheet->appendRow($arr);
                                  $i = 0;

                                }//end if pasok sa cutoff period
                                

                              }//end for loop
                              

                            }else
                            {
                              //AccessCode', 'EmployeeName','LeaveDate','LeaveCode','Quantity','Status','Comment', 'DateFiled','Approver Remarks
                              $arr[$i] = $jps['data'][0]->accesscode; $i++;
                              $arr[$i] = $jps['data'][0]->lastname.", ".$jps['data'][0]->firstname; $i++;

                              //-------- check if VTO
                              if($jps['type'] == 'VTO')
                              {
                                ($jps['data'][0]->deductFrom == "AdvSL") ? $leaveCode = "SL" : $leaveCode = $jps['data'][0]->deductFrom; 

                                // kunin mo muna worksched
                                $sched = $this->getUserWorksched($jps['data'][0]->userID,$jps['data'][0]->productionDate);
                                if (count($sched) > 0 && ($sched[0]->workshift !== '* RD * - * RD *') )
                                {
                                  $wsched = explode('-', $sched[0]->workshift);
                                  $startShift = Carbon::parse($jps['data'][0]->productionDate." ". $wsched[0])->addHours(9);

                                  $s = Carbon::parse($startShift->format('Y-m-d')." ". $jps['data'][0]->startTime,'Asia/Manila');
                                  $e = Carbon::parse($startShift->format('Y-m-d')." ". $jps['data'][0]->endTime,'Asia/Manila');

                                }
                                else //walang locked DTR sched
                                {
                                  $s = Carbon::parse($jps['data'][0]->productionDate." ". $jps['data'][0]->startTime,'Asia/Manila');
                                  $e = Carbon::parse($jps['data'][0]->productionDate." ". $jps['data'][0]->endTime,'Asia/Manila');

                                }

                                $qty = number_format((float)$jps['data'][0]->totalHours*0.125,2);
                                $pd = date('m/d/Y',strtotime($jps['data'][0]->productionDate));
                                

                              }
                              else //VL | SL | LWOP | ML | SPL | PL
                              {
                                //establish leave COde

                                ($jps['type'] == 'FL') ? $leaveCode = $jps['data'][0]->leaveType : $leaveCode = $jps['type'];
                                $qty = $jps['data'][0]->totalCredits;

                                //gawin mo lang kapag halfday leaves
                                if ($jps['data'][0]->totalCredits <= 0.5 )
                                {
                                  $sched = $this->getUserWorksched($jps['data'][0]->userID,$jps['data'][0]->leaveStart);
                                  if (count($sched) > 0 && ($sched[0]->workshift !== '* RD * - * RD *') )
                                  {
                                    //need to check kung 1st half/2nd half of shift
                                    $wsched = explode('-', $sched[0]->workshift);

                                    if ($jps['data'][0]->halfdayFrom == 3)
                                    {
                                      //if parttimer
                                      $u = User::find($jps['data'][0]->userID);
                                      ($u->status_id == 12 || $u->status_id == 14) ? $isParttimer = true : $isParttimer=false;

                                      
                                      if($jps['data'][0]->productionDate)
                                        {
                                          $lstart =Carbon::parse($jps['data'][0]->productionDate,'Asia/Manila');
                                          $hasProdate=true;
                                        }
                                      else 
                                        { 
                                          $lstart =Carbon::parse($jps['data'][0]->leaveStart,'Asia/Manila');
                                        }

                                      if($isParttimer)
                                      {
                                        $pt = DB::table('pt_override')->where('user_id',$u->id)->get();
                                        
                                        
                                        if (count($pt) > 0)
                                        {
                                          if ( Carbon::parse($pt[0]->overrideEnd,'Asia/Manila') >= $lstart )
                                          {
                                            ($hasProdate) ? $s = Carbon::parse($lstart->format('Y-m-d')." ".$wsched[0],'Asia/Manila') : $s = Carbon::parse($lstart->format('Y-m-d')." ".$wsched[0],'Asia/Manila')->addHours(5);
                                            

                                          }
                                          else
                                          {
                                            ($hasProdate) ? $s = Carbon::parse($lstart->format('Y-m-d')." ".$wsched[0],'Asia/Manila') : $s = Carbon::parse($lstart->format('Y-m-d')." ".$wsched[0],'Asia/Manila')->addHours(2);
                                            

                                          }
                                        }
                                        else //partime schedule nga sya for today
                                        {
                                           ($hasProdate) ? $s = Carbon::parse($lstart->format('Y-m-d')." ".$wsched[0],'Asia/Manila') : $s = Carbon::parse($lstart->format('Y-m-d')." ".$wsched[0],'Asia/Manila')->addHours(2);

                                        }
                                      }

                                      else
                                      {
                                        ($hasProdate) ? $s = Carbon::parse($lstart->format('Y-m-d')." ".$wsched[0],'Asia/Manila') : $s = Carbon::parse($lstart->format('Y-m-d')." ".$wsched[0],'Asia/Manila')->addHours(5);
                                         
                                         //$e = Carbon::parse($jps['data'][0]->leaveEnd,'Asia/Manila');

                                      }

                                    }
                                    else //hindi sya start ng 2nd half
                                    {
                                      ($jps['data'][0]->productionDate) ? $s = Carbon::parse($jps['data'][0]->productionDate,'Asia/Manila') : $s = Carbon::parse($jps['data'][0]->leaveStart,'Asia/Manila');
                                      $e =  Carbon::parse($jps['data'][0]->leaveEnd,'Asia/Manila');

                                    }

                                  }
                                  else 
                                  {
                                    ($jps['data'][0]->productionDate) ? $s = Carbon::parse($jps['data'][0]->productionDate,'Asia/Manila') : $s = Carbon::parse($jps['data'][0]->leaveStart,'Asia/Manila');
                                    
                                    $e =  Carbon::parse($jps['data'][0]->leaveEnd,'Asia/Manila');//->addHours($jps['data'][0]->filed_hours);
                                  }

                                }
                                else //whole day leaves sya
                                {
                                  $s = Carbon::parse($jps['data'][0]->leaveStart,'Asia/Manila');
                                  $e =  Carbon::parse($jps['data'][0]->leaveEnd,'Asia/Manila');

                                }

                                
                                $pd = $s->format('m/d/Y');

                              }

                             

                              //*** LeaveDate
                              $arr[$i] = $pd; $i++; // $s->format('m/d/Y'); $i++;

                              //*** LeaveCOde
                              //$arr[$i] = $leaveCode; $i++;
                              //*** LeaveCOde
                                    if($leaveCode == 'ML'){
                                      $arr[$i] = "SSSML"; $i++;
                                    }else{
                                      $arr[$i] = $leaveCode; $i++;
                                    }
                                    
                              

                              //*** Quantity
                              $arr[$i] = $qty; $i++;
                              

                              //*** Status
                              if($jps['data'][0]->isApproved == '1') $stat = "Approved";
                              else if ($jps['data'][0]->isApproved == '0') $stat = "Denied";
                              else $stat = "Pending Approval";

                              $arr[$i] = $stat; $i++;

                              //*** Comment
                              $arr[$i] = $jps['data'][0]->notes; $i++;

                              //*** Date Files
                              $arr[$i] = date('m/d/Y', strtotime($jps['data'][0]->created_at)); $i++;

                              //remarks
                              $arr[$i] = $stat; $i++;

                              $sheet->appendRow($arr);

                            }//end more than 1 day

                            

                          }

                        }//end foreach employee

                        
                      });//end sheet1

              })->export('xls');return "Download";
      }
      else if ($template == '3') // CWS
      {

        if($this->user->id !== 564 ) {
              $file = fopen('public/build/rewards.txt', 'a') or die("Unable to open logs");
                fwrite($file, "-------------------\n JPS_".$type." cutoff: -- ".$cutoffStart->format('M d')." on " . $correct->format('M d h:i A'). " by [". $this->user->id."] ".$this->user->lastname."\n");
                fclose($file);
        } 

        Excel::create($type."_".$cutoffStart->format('M-d'),function($excel) use($type, $jpsData, $cutoffStart, $cutoffEnd, $headers,$description, $request) 
              {
                      $excel->setTitle($cutoffStart->format('Y-m-d').' to '. $cutoffEnd->format('Y-m-d').'_'.$type);
                      $excel->setCreator('Programming Team')
                            ->setCompany('OpenAccessBPO');

                      // Call them separately
                      $excel->setDescription($description);

                      $excel->sheet("Sheet1", function($sheet) use ($type, $jpsData, $cutoffStart, $cutoffEnd, $headers,$description,$request)
                      {
                        $sheet->appendRow($headers);      

                        $arr = [];

                        foreach($jpsData as $jps)
                        {
                          $i = 0;

                          if(count($jps) > 1)
                          {
                            if($request->DTRsummary)
                            {
                              foreach ($jps as $j) //['AccessCode', 'EmployeeName','Program', 'ShiftDate','Old Schedule','New Schedule','Status', 'Notes', Approver']
                              {
                                $c=0;
                                $arr[$c] = $j->accesscode; $c++;
                                $arr[$c] = $j->lastname.", ".$j->firstname; $c++;


                                $s_old = Carbon::parse($j->productionDate." ".$j->timeStart_old,'Asia/Manila');
                                $e_old = Carbon::parse($j->productionDate." ".$j->timeEnd_old,'Asia/Manila');
                                $s = Carbon::parse($j->productionDate." ".$j->timeStart,'Asia/Manila');
                                $e =  Carbon::parse($j->productionDate." ".$j->timeEnd,'Asia/Manila');

                                 //*** Program
                                $arr[$c] = $j->program; $c++;

                                //*** ShiftDate
                                $arr[$c] = $s->format('m/d/Y'); $c++;

                                

                                //*** CurrentDailySchedule FLEXI-TIME 8 HOURS
                               if($j->timeStart_old == "00:00:00" && $j->timeEnd_old == "00:00:00")
                               {
                                   $arr[$c] = "RD"; $c++;
                               }
                               else{
                                 $arr[$c] = $s_old->format('h:i A')." - ".$e_old->format('h:i A'); $c++;
                               }

                                //*** NewDailySchedule
                                if($j->timeStart == "00:00:00" && $j->timeEnd == "00:00:00")
                               {
                                   $arr[$c] = "RD";$c++;
                               }
                               else {
                                $arr[$c] = $s->format('h:i A')." - ".$e->format('h:i A'); $c++;
                               }

                                //*** Status
                                if($j->isApproved == '1') $stat = "Approved";
                                else if ($j->isApproved == '0') $stat = "Denied";
                                else $stat = "Pending Approval";

                                $arr[$c] = $stat; $c++;


                                //*** Notes

                                $arr[$c] = $j->notes; $c++;

                                 //*** Approver
                                if($j->approver)
                                {
                                  $app = ImmediateHead::find(ImmediateHead_Campaign::find($j->approver)->immediateHead_id);
                                  $approver = $app->firstname." ".$app->lastname." on [".date('M d,Y h:i A', strtotime($j->updated_at))."]";

                                }
                                else
                                {
                                  $approver = "WFM team";
                                }

                                $arr[$c] = $approver; $c++;


                                $sheet->appendRow($arr);
                                
                              }

                            }
                            else
                            {
                              foreach ($jps as $j) 
                              {
                                $c=0;
                                $arr[$c] = $j->accesscode; $c++;
                                $arr[$c] = $j->lastname.", ".$j->firstname; $c++;


                                $s_old = Carbon::parse($j->productionDate." ".$j->timeStart_old,'Asia/Manila');
                                $e_old = Carbon::parse($j->productionDate." ".$j->timeEnd_old,'Asia/Manila');
                                $s = Carbon::parse($j->productionDate." ".$j->timeStart,'Asia/Manila');
                                $e =  Carbon::parse($j->productionDate." ".$j->timeEnd,'Asia/Manila');

                                //*** ShiftDate
                                $arr[$c] = $s->format('m/d/Y'); $c++;

                                 //*** Status
                                if($j->isApproved == '1') $stat = "Approved";
                                else if ($j->isApproved == '0') $stat = "Denied";
                                else $stat = "Pending Approval";

                                $arr[$c] = $stat; $c++;

                                //*** CurrentDailySchedule FLEXI-TIME 8 HOURS
                               if($j->timeStart_old == "00:00:00" && $j->timeEnd_old == "00:00:00")
                               {
                                   $arr[$c] = "FLEXI-TIME 8 HOURS"; $c++;
                               }
                               else{
                                 $arr[$c] = $s_old->format('h:i A')." - ".$e_old->format('h:i A'); $c++;
                               }

                                //*** NewDailySchedule
                                if($j->timeStart == "00:00:00" && $j->timeEnd == "00:00:00")
                               {
                                   $arr[$c] = "FLEXI-TIME 8 HOURS";$c++;
                               }
                               else {
                                $arr[$c] = $s->format('h:i A')." - ".$e->format('h:i A'); $c++;
                               }
                                

                                //*** CurrentDayType
                                if($j->isRD){
                                  $arr[$c] = "Rest Day"; $c++;
                                }
                                else{
                                  $arr[$c] = "Regular Day"; $c++;
                                }

                                //*** NewDayType
                                if($j->timeStart == "00:00:00" && $j->timeEnd == "00:00:00"){
                                  $arr[$c] = "Rest Day"; $c++;
                                }
                                else{
                                  $arr[$c] = "Regular Day"; $c++;
                                }



                                $sheet->appendRow($arr);
                                
                              }

                            }
                            

                          }
                          else
                          {
                            //Employee AccessCode', 'EmployeeName','ShiftDate','Status','CurrentDailySchedule','NewDailySchedule','CurrentDayType','NewDayType',
                            $arr[$i] = $jps[0]->accesscode; $i++;
                            $arr[$i] = $jps[0]->lastname.", ".$jps[0]->firstname; $i++;

                            $s_old = Carbon::parse($jps[0]->productionDate." ".$jps[0]->timeStart_old,'Asia/Manila');
                            $e_old = Carbon::parse($jps[0]->productionDate." ".$jps[0]->timeEnd_old,'Asia/Manila');
                            $s = Carbon::parse($jps[0]->productionDate." ".$jps[0]->timeStart,'Asia/Manila');
                            $e =  Carbon::parse($jps[0]->productionDate." ".$jps[0]->timeEnd,'Asia/Manila');

                            //*** ShiftDate
                            $arr[$i] = $s->format('m/d/Y'); $i++;

                             //*** Status
                            if($jps[0]->isApproved == '1') $stat = "Approved";
                            else if ($jps[0]->isApproved == '0') $stat = "Denied";
                            else $stat = "Pending Approval";

                            $arr[$i] = $stat; $i++;

                            //*** CurrentDailySchedule
                            if($jps[0]->timeStart_old == "00:00:00" && $jps[0]->timeEnd_old == "00:00:00")
                             {
                                 $arr[$i] = "FLEXI-TIME 8 HOURS";$i++;
                             }
                            else {
                             $arr[$i] = $s_old->format('h:i A')." - ".$e_old->format('h:i A'); $i++;
                            }

                            //*** NewDailySchedule
                            if($jps[0]->timeStart == "00:00:00" && $jps[0]->timeEnd == "00:00:00")
                             {
                                 $arr[$i] = "FLEXI-TIME 8 HOURS";$i++;
                             }
                             else {
                              $arr[$i] = $s->format('h:i A')." - ".$e->format('h:i A'); $i++;
                             }
                              

                            //*** CurrentDayType
                            if($jps[0]->isRD){
                              $arr[$i] = "Rest Day"; $i++;
                            }
                            else{
                              $arr[$i] = "Regular Day"; $i++;
                            }

                            //*** NewDayType
                            if($jps[0]->timeStart == "00:00:00" && $jps[0]->timeEnd == "00:00:00"){
                              $arr[$i] = "Rest Day"; $i++;
                            }
                            else{
                              $arr[$i] = "Regular Day"; $i++;
                            }

                           

                            $sheet->appendRow($arr);

                          }

                        }//end foreach employee

                        
                      });//end sheet1

              })->export('xls');return "Download";
      }
      else if ($template == '4') // worksched
      {

        if($this->user->id !== 564 ) {
              $file = fopen('public/build/rewards.txt', 'a') or die("Unable to open logs");
                fwrite($file, "-------------------\n JPS_".$type." cutoff: -- ".$cutoffStart->format('M d')." on " . $correct->format('M d h:i A'). " by [". $this->user->id."] ".$this->user->lastname."\n");
                fclose($file);
        } 

        Excel::create($type."_".$cutoffStart->format('M-d'),function($excel) use($type, $jpsData, $cutoffStart, $cutoffEnd, $headers,$description) 
              {
                      $excel->setTitle($cutoffStart->format('Y-m-d').' to '. $cutoffEnd->format('Y-m-d').'_'.$type);
                      $excel->setCreator('Programming Team')
                            ->setCompany('OpenAccessBPO');

                      // Call them separately
                      $excel->setDescription($description);

                      $excel->sheet("Sheet1", function($sheet) use ($type, $jpsData, $cutoffStart, $cutoffEnd, $headers,$description)
                      {
                        $sheet->appendRow($headers);      

                        $arr = [];

                        foreach($jpsData as $jps)
                        {
                          $i = 0;

                          if(count($jps) > 1)
                          {
                            foreach ($jps as $j) 
                            {
                              $c=0;
                              $arr[$c] = $j->accesscode; $c++;
                              $arr[$c] = $j->lastname.", ".$j->firstname; $c++;


                              $s = Carbon::parse($j->productionDate." 00:00:00",'Asia/Manila');
                             

                              //*** ShiftDate
                              $arr[$c] = $s->format('m/d/Y'); $c++;

                               //*** Status
                              $stat = "Approved";
                              

                              $arr[$c] = $stat; $c++;

                              //*** CurrentDailySchedule FLEXI-TIME 8 HOURS
                              //check mo kung exempt employee
                              $ex = DB::table('user_schedType')->where('user_schedType.user_id',$j->userID)->
                                      join('schedType','schedType.id','=','user_schedType.schedType_id')->select('user_schedType.user_id','schedType.name')->get();

                              if(count($ex) > 0)
                              {
                                //exempt employee
                                if (($j->workshift == "* RD * - * RD *") || strpos($j->workshift, 'RD') !== false)
                                 {
                                     $arr[$c] = $ex[0]->name; $c++; 
                                     $arr[$c] = $ex[0]->name;$c++;
                                     $arr[$c] = "Rest Day"; $c++;
                                     $arr[$c] = "Rest Day"; $c++;
                                 }
                                 else{
                                   $arr[$c] = $ex[0]->name; $c++;
                                   $arr[$c] = $ex[0]->name; $c++; 
                                   $arr[$c] = "Regular Day"; $c++;
                                   $arr[$c] = "Regular Day"; $c++;
                                 }
                              }
                              else
                              {
                                if (($j->workshift == "* RD * - * RD *") || strpos($j->workshift, 'RD') !== false)
                                 {
                                     $arr[$c] = "FLEXI-TIME 8 HOURS"; $c++; 
                                     $arr[$c] = "FLEXI-TIME 8 HOURS";$c++;
                                     $arr[$c] = "Rest Day"; $c++;
                                     $arr[$c] = "Rest Day"; $c++;
                                 }
                                 else{
                                   $arr[$c] = strip_tags($j->workshift); $c++;
                                   $arr[$c] = strip_tags($j->workshift); $c++; 
                                   $arr[$c] = "Regular Day"; $c++;
                                   $arr[$c] = "Regular Day"; $c++;
                                 }

                              }

                             

                            

                              $sheet->appendRow($arr);
                              
                            }

                          }
                          else
                          {
                            //Employee AccessCode', 'EmployeeName','ShiftDate','Status','CurrentDailySchedule','NewDailySchedule','CurrentDayType','NewDayType',
                            $arr[$i] = $jps[0]->accesscode; $i++;
                            $arr[$i] = $jps[0]->lastname.", ".$jps[0]->firstname; $i++;

                           
                            $s = Carbon::parse($jps[0]->productionDate." 00:00:00",'Asia/Manila');
                           

                            //*** ShiftDate
                            $arr[$i] = $s->format('m/d/Y'); $i++;

                             //*** Status
                            $stat = "Approved";
                            

                            $arr[$i] = $stat; $i++;


                            //*** CurrentDailySchedule FLEXI-TIME 8 HOURS
                              //check mo kung exempt employee
                              $ex = DB::table('user_schedType')->where('user_schedType.user_id',$jps[0]->userID)->
                                      join('schedType','schedType.id','=','user_schedType.schedType_id')->select('user_schedType.user_id','schedType.name')->get();

                              if(count($ex) > 0)
                              {
                                //exempt employee
                                if(($jps[0]->workshift == "* RD * - * RD *") || strpos($jps[0]->workshift, 'RD') !== false)
                                 {
                                     $arr[$c] = $ex[0]->name; $c++; 
                                     $arr[$c] = $ex[0]->name;$c++;
                                     $arr[$c] = "Rest Day"; $c++;
                                     $arr[$c] = "Rest Day"; $c++;
                                 }
                                 else{
                                   $arr[$c] = $ex[0]->name; $c++;
                                   $arr[$c] = $ex[0]->name; $c++; 
                                   $arr[$c] = "Regular Day"; $c++;
                                   $arr[$c] = "Regular Day"; $c++;
                                 }
                              }
                              else
                              {
                                  //*** CurrentDailySchedule
                                  if(($jps[0]->workshift == "* RD * - * RD *") || strpos($jps[0]->workshift, 'RD') !== false)
                                  {
                                       $arr[$i] = "FLEXI-TIME 8 HOURS";$i++; 
                                       $arr[$i] = "FLEXI-TIME 8 HOURS";$i++;
                                       $arr[$i] = "Rest Day"; $i++;
                                       $arr[$i] = "Rest Day"; $i++;
                                   }
                                  else {
                                   $arr[$i] = strip_tags($jps[0]->workshift); $i++;
                                   $arr[$i] = strip_tags($jps[0]->workshift); $i++;
                                   $arr[$i] = "Regular Day"; $i++;
                                  $arr[$i] = "Regular Day"; $i++;
                                  }

                              }




                            
                            $sheet->appendRow($arr);

                          }

                        }//end foreach employee

                        
                      });//end sheet1

              })->export('xls');return "Download";
      }
      else if ($template == '5') // HOLIDAY OPS
      {

        if($this->user->id !== 564 ) {
              $file = fopen('public/build/rewards.txt', 'a') or die("Unable to open logs");
                fwrite($file, "-------------------\n JPS_".$type." cutoff: -- ".$cutoffStart->format('M d')." on " . $correct->format('M d h:i A'). " by [". $this->user->id."] ".$this->user->lastname."\n");
                fclose($file);
        } 

        Excel::create($type."_HD-Ops_".$cutoffStart->format('M-d'),function($excel) use($type, $jpsData, $cutoffStart, $cutoffEnd, $headers,$description) 
              {
                      $excel->setTitle($cutoffStart->format('Y-m-d').' to '. $cutoffEnd->format('Y-m-d').'_'.$type);
                      $excel->setCreator('Programming Team')
                            ->setCompany('OpenAccessBPO');

                      // Call them separately
                      $excel->setDescription($description);

                      $excel->sheet("Sheet1", function($sheet) use ($type, $jpsData, $cutoffStart, $cutoffEnd, $headers,$description)
                      {
                        $sheet->appendRow($headers);      

                        $arr = [];

                        foreach($jpsData as $jps)
                        {
                          $i = 0;

                          if(count($jps) > 1)
                          {
                            foreach ($jps as $j) 
                            {
                              $c=0;
                              $arr[$c] = $j->accesscode; $c++;
                              $arr[$c] = $j->lastname.", ".$j->firstname; $c++;


                              //-----

                              $isParttimer=false;
                              $isHoliday = true; //(count(Holiday::where('holidate',$j->productionDate)->get()) > 0) ? true : false;
                              $isBackoffice = false; // ( Campaign::find(Team::where('user_id',$j->userID)->first()->campaign_id)->isBackoffice ) ? true : false;


                              //-----we get first employee's schedule from locked DTR

                              $sched = $j->workshift;
                              // if(count($sched) > 0)
                              // {
                                $hoursFiled = $j->filed_hours; $hoursApproved =$j->filed_hours;
                                if (($sched !== '* RD * - * RD *') && strpos($sched, 'RD') === false)
                                {
                                  $wshift = explode('-',$sched);

                                  if (strpos($j->timeStart, 'shift') !== false) {
                                      $s = Carbon::parse($j->productionDate." ".$wshift[0],'Asia/Manila');
                                      $startDate = Carbon::parse($j->productionDate." ".$wshift[0],'Asia/Manila')->addHours(5);
                                      //Carbon::parse($j->productionDate,'Asia/Manila');
                                      $startTime = Carbon::parse($j->productionDate." ".$wshift[0],'Asia/Manila')->addHours(5);
                                  }
                                  else {
                                      $s = Carbon::parse($j->timeStart,'Asia/Manila');
                                      $startDate = Carbon::parse($j->timeStart,'Asia/Manila');
                                      $startTime = $startDate;
                                  }
                                    


                                  /*if (($j->timeStart === "1st shift LWOP") || ($j->timeStart == "1st shift SL") || ($j->timeStart == "1st shift VL")){
                                    $s = Carbon::parse($wshift[0],'Asia/Manila')->addHours(5);
                                    //$s = Carbon::parse($j->timeStart,'Asia/Manila');
                                  }
                                  else
                                    $s = Carbon::parse($j->timeStart,'Asia/Manila');*/

                                  
                                    
                                    ($j->status_id == 12 || $j->status_id == 14) ? $isParttimer = true : $isParttimer=false;
                                    

                                     //Carbon::parse($startDate->format('Y-m-d')." ".$wshift[0],'Asia/Manila');
                                  
                                  if (($j->timeEnd == '<strong class="text-danger">No IN</strong><a title') || ($j->timeEnd == '<strong class="text-danger">No OUT</strong><a title') || (strpos($j->timeEnd, 'shift') !== false) || (strpos($j->timeEnd, 'OUT') !== false)) {
                                    $endDate =  Carbon::parse(strip_tags($wshift[1]),'Asia/Manila');
                                    $endTime = Carbon::parse(strip_tags($wshift[1]),'Asia/Manila');
                                  }
                                  else {
                                    $endDate =  Carbon::parse(strip_tags($j->timeEnd),'Asia/Manila');
                                    $endTime = Carbon::parse(strip_tags($j->timeEnd),'Asia/Manila');
                                  }

                                  
                                  
                                  
                                }
                                
                                else
                                {

                                  $s = Carbon::parse($j->timeStart,'Asia/Manila');

                                  if ( strpos($j->timeEnd,'No') !== false  || (strpos($j->timeEnd, 'shift') !== false)) {
                                    
                                    $e =  Carbon::parse($j->timeStart,'Asia/Manila'); //no legit out, so same in nlang
                                    $endDate =   Carbon::parse($j->timeStart,'Asia/Manila');


                                  }
                                  else {
                                    //$endDate =  Carbon::parse($j->timeEnd,'Asia/Manila');
                                    //$endTime = Carbon::parse($j->timeEnd,'Asia/Manila');

                                    $e =  Carbon::parse(strip_tags($j->timeEnd),'Asia/Manila');//->addHours($jps[0]->filed_hours);
                                    $endDate =  Carbon::parse(strip_tags($j->timeEnd),'Asia/Manila');
                                  }


                                  
                                  $startDate = $s;
                                  $startTime = $s;
                                  $endTime = $e;
                                  $wshift = array( $j->timeStart,$j->timeEnd );
                                }
                                

                              //}
                              // else
                              // {
                              //   $s = Carbon::parse($j->productionDate." ".$j->timeStart,'Asia/Manila');
                              //   $e =  Carbon::parse($j->productionDate." ".$j->timeEnd,'Asia/Manila');//->addHours($jps[0]->filed_hours);
                              //   $endDate =  Carbon::parse($j->productionDate." ".$j->timeStart,'Asia/Manila')->addHours($j->filed_hours);
                              //   $startDate = $s;
                              //   $startTime = $s;
                              //   $endTime = $e;
                              //   $hoursFiled = $j->billable_hours; $hoursApproved =$j->filed_hours;

                              // }

                              //***------- new for HOLIDAY OT------------ ***
                              // if HOLIDAY TODAY, check if (backoffice) -> nothing changes sa filed OT
                              // else ops sya
                              // if sched nya today is * RD * -> nothing changes
                              // else add 8hrs / 4hrs kung PT
                              


                              if ($isHoliday)
                              {
                                    if (($sched === '* RD * - * RD *')||strpos($sched, 'RD') !== false) {  $hoursFiled = $j->filed_hours; $hoursApproved =$j->filed_hours; goto proceedSaving1; }
                                    else
                                    {
                                      // check muna kung PT or not
                                      if ($isParttimer)
                                      {
                                        
                                        $hoursFiled =  4.0;
                                        $hoursApproved =  4.0;
                                      }
                                      else
                                      {

                                         if (($sched !== '* RD * - * RD *') && strpos($sched, 'RD') === false)
                                         {
                                            if (strpos($j->timeStart, 'shift') !== false) {
                                                $hoursFiled = 4.0;
                                                $hoursApproved = 4.0;
                                            }
                                            else {
                                                $hoursFiled = $j->filed_hours + 8.0;
                                                $hoursApproved = $j->filed_hours + 8.0;
                                            }
                                          }
                                          else
                                          {
                                            $hoursFiled = $j->filed_hours + 8.0;
                                            $hoursApproved = $j->filed_hours + 8.0;

                                          }
                                        
                                        

                                      }
                                    }

                                  

                                
                              }
                              
                              
                              //***------- end for HOLIDAY OT------------ ***

                              proceedSaving1:
                                  //*** ShiftDate
                                  $arr[$c] = $s->format('m/d/Y'); $c++;



                                  //*** StartDate
                                  $arr[$c] = $startDate->format('m/d/Y'); $c++;

                                  //*** StartTime
                                  $arr[$c] = $startTime->format('h:i A'); $c++;

                                  //*** EndDate
                                  $arr[$c] = $endDate->format('m/d/Y'); $c++;

                                  //*** EndTime
                                  $arr[$c] = $endTime->format('h:i A'); $c++;

                                  //*** Status
                                  $stat = "Approved";

                                  $arr[$c] = $stat; $c++;




                                  //*** HoursFiled
                                  $arr[$c] = $hoursFiled; $c++;

                                   //*** HoursApproved
                                  $arr[$c] = $hoursApproved; $c++;

                              

                              $sheet->appendRow($arr);


                              
                            }

                          }
                          else
                          {
                            //-----
                            $arr[$i] = $jps[0]->accesscode; $i++;
                            $arr[$i] = $jps[0]->lastname.", ".$jps[0]->firstname; $i++;

                            $isParttimer=false;
                            $isHoliday = true; // (count((array)Holiday::where('holidate',$jps[0]->productionDate)->get()) > 0) ? true : false;
                            $isBackoffice = false;// ( Campaign::find(Team::where('user_id',$jps[0]->userID)->first()->campaign_id)->isBackoffice ) ? true : false;

                            //-----we get first employee's schedule from locked DTR

                              $sched = $jps[0]->workshift;
                              // if(count($sched) > 0)
                              // {
                                $hoursFiled = $jps[0]->filed_hours; $hoursApproved =$jps[0]->filed_hours;
                                if (($sched !== '* RD * - * RD *') && strpos($sched, 'RD') === false)
                                {
                                  $wshift = explode('-',$sched);

                                  if (($jps[0]->timeStart == "1st shift LWOP") || ($jps[0]->timeStart == "1st shift SL") || ($jps[0]->timeStart == "1st shift VL"))
                                  {
                                     $s = Carbon::parse($wshift[0],'Asia/Manila')->addHours(5);
                                  }
                                  else
                                    $s = Carbon::parse($jps[0]->timeStart,'Asia/Manila');

                                  
                                    
                                    ($jps[0]->status_id == 12 || $jps[0]->status_id == 14) ? $isParttimer = true : $isParttimer=false;
                                    $startDate = Carbon::parse($jps[0]->timeStart,'Asia/Manila');

                                    $startTime = $startDate; //Carbon::parse($startDate->format('Y-m-d')." ".$wshift[0],'Asia/Manila');
                                  

                                  if ($j->timeEnd == '<strong class="text-danger">No IN</strong><a title') {
                                    $endDate =  Carbon::parse($wshift[1],'Asia/Manila');
                                    $endTime = Carbon::parse($wshift[1],'Asia/Manila');
                                  }
                                  else {
                                    $endDate =  Carbon::parse($jps[0]->timeEnd,'Asia/Manila');
                                    $endTime = Carbon::parse($jps[0]->timeEnd,'Asia/Manila');
                                  }
                                
                                  
                                }
                                
                                else
                                {

                                  $s = Carbon::parse($jps[0]->timeStart,'Asia/Manila');
                                  $e =  Carbon::parse($jps[0]->timeEnd,'Asia/Manila');//->addHours($jps[0]->filed_hours);
                                  $endDate =  Carbon::parse($jps[0]->timeEnd,'Asia/Manila');
                                  $startDate = $s;
                                  $startTime = $s;
                                  $endTime = $e;
                                  $wshift = array( $jps[0]->timeStart,$jps[0]->timeEnd );
                                }
                                

                              


                              if ($isHoliday)
                              {
                                    if (($sched === '* RD * - * RD *') || strpos($sched, 'RD') !== false) {  $hoursFiled = $jps[0]->filed_hours; $hoursApproved =$jps[0]->filed_hours; goto proceedSaving; }
                                    else
                                    {
                                      // check muna kung PT or not
                                      if ($isParttimer)
                                      {
                                        
                                        $hoursFiled =  4.0;
                                        $hoursApproved =  4.0;
                                      }
                                      else
                                      {
                                        
                                        $hoursFiled = $jps[0]->filed_hours + 8.0;
                                        $hoursApproved = $jps[0]->filed_hours + 8.0;

                                      }
                                    }

                                  

                                
                              }
                              
                              
                              //***------- end for HOLIDAY OT------------ ***

                              proceedSaving:
                                  //*** ShiftDate
                                  $arr[$i] = $s->format('m/d/Y'); $i++;



                                  //*** StartDate
                                  $arr[$i] = $startDate->format('m/d/Y'); $i++;

                                  //*** StartTime
                                  $arr[$i] = $startTime->format('h:i A'); $i++;

                                  //*** EndDate
                                  $arr[$i] = $endDate->format('m/d/Y'); $i++;

                                  //*** EndTime
                                  $arr[$i] = $endTime->format('h:i A'); $i++;

                                  //*** Status
                                  $stat = "Approved";
                                  

                                  $arr[$i] = $stat; $i++;




                                  //*** HoursFiled
                                  $arr[$i] = $hoursFiled; $i++;

                                   //*** HoursApproved
                                  $arr[$i] = $hoursApproved; $i++;

                              

                              $sheet->appendRow($arr);


                            //++++++++++++++++++++++++++++++++++++++++++++++++++++



                          }

                        }//end foreach employee

                        
                      });//end sheet1

              })->export('xls');return "Download";
      }
      else if ($template == '6') // Unlocked DTRs
      {

        if($this->user->id !== 564 ) {
              $file = fopen('storage/uploads/dtrplogs.txt', 'a') or die("Unable to open logs");
                fwrite($file, "-------------------\n JPS_".$type." cutoff: -- ".$cutoffStart->format('M d')." on " . $correct->format('M d h:i A'). " by [". $this->user->id."] ".$this->user->lastname."\n");
                fclose($file);
        } 

        //return $jpsData;
        $arr=[];
        $kol = new Collection;

        // foreach($jpsData as $jps)
        //                 {
        //                   $i = 0;

        //                   //EmployeeCode', 'EmployeeName','Program','Immediate Head','Unlocked Date','DTR Sheet'

        //                   // kunin mo muna details kung sino from the list of sino sino
        //                   foreach ($jps['sino'] as $k) 
        //                   {
        //                     $emp = collect($allUsers)->where('id',$k);
        //                     $arr[$i] = $emp->first()->employeeCode; $i++;
        //                     $arr[$i] = $emp->first()->lastname.", ".$emp->first()->firstname; $i++;
        //                     $arr[$i] = $emp->first()->program; $i++;
        //                     $arr[$i] = $emp->first()->leaderFname." ".$emp->first()->leaderLname; $i++;
        //                     $arr[$i] = $jps['date']; $i++;
        //                     $arr[$i] = action('DTRController@show',$k); $i++;
        //                     //$sheet->appendRow($arr);
        //                     $kol->push($arr);

        //                   }
                          

        //                 }//end foreach employee
        // return $kol;
        return $jpsData;

        // Excel::create($type."_".$cutoffStart->format('M-d'),function($excel) use($type, $jpsData,$allEmp, $allUsers, $cutoffStart, $cutoffEnd, $headers,$description) 
        //       {
        //               $excel->setTitle($cutoffStart->format('Y-m-d').' to '. $cutoffEnd->format('Y-m-d').'_'.$type);
        //               $excel->setCreator('Programming Team')
        //                     ->setCompany('OpenAccessBPO');

                      
        //               $excel->setDescription($description);

        //               $excel->sheet("Sheet1", function($sheet) use ($type, $jpsData,$allEmp, $allUsers, $cutoffStart, $cutoffEnd, $headers,$description)
        //               {
        //                 $sheet->appendRow($headers);      

        //                 $arr = [];

        //                 /*jpsData = unlocks{  date: Y-m-d
        //                                       meron: [ .. ]
        //                                       hanapan: xx
        //                                       sino: {}
        //                                     }
        //                 */

        //                 foreach($jpsData as $jps)
        //                 {
        //                   $i = 0;

        //                   //EmployeeCode', 'EmployeeName','Program','Immediate Head','Unlocked Date','DTR Sheet'

        //                   // kunin mo muna details kung sino from the list of sino sino
        //                   foreach ($jps['sino'] as $k) 
        //                   {
        //                     $emp = collect($allUsers)->where('id',$k);
        //                     $arr[$i] = $emp->first()->employeeCode; $i++;
        //                     $arr[$i] = $emp->first()->lastname.", ".$emp->first()->firstname; $i++;
        //                     $arr[$i] = $emp->first()->program; $i++;
        //                     $arr[$i] = $emp->first()->leaderFname." ".$emp->first()->leaderLname; $i++;
        //                     $arr[$i] = $jps['date']; $i++;
        //                     $arr[$i] = action('DTRController@show',$k); $i++;
        //                     $sheet->appendRow($arr);

        //                   }
                          

        //                 }//end foreach employee

                        
        //               });//end sheet1

        //       })->export('xls');return "Download";
      }

      

      //return response()->json(['data'=>$jpsData,'template'=>$template, 'cutoffstart'=>$cutoffStart,'cutoffend'=>$cutoffEnd]);



    }

    public function lockReport()
    {
      //Timekeeping trait getCutoffStartEnd()
      $cutoffData = $this->getCutoffStartEnd();
      $cutoffStart = $cutoffData['cutoffStart'];//->cutoffStart;
      $cutoffEnd = $cutoffData['cutoffEnd'];
      $correct = Carbon::now('GMT+8'); 

       //Timekeeping Trait
      $payrollPeriod = $this->getPayrollPeriod($cutoffStart,$cutoffEnd);
      $paycutoffs = Paycutoff::orderBy('toDate','DESC')->get();

      $financeDept = Campaign::where('name',"Finance")->first();
      $finance = Team::where('user_id',$this->user->id)->where('campaign_id',$financeDept->id)->get();
      (count($finance) > 0) ? $isFinance = 1 : $isFinance=0;

      if ( $this->user->userType_id ==1 || $isFinance) {
        
        if($this->user->id !== 564 ) {
              $file = fopen('storage/uploads/log.txt', 'a') or die("Unable to open logs");
                fwrite($file, "-------------------\n DTRLockReport on " . $correct->format('M d h:i A'). " by [". $this->user->id."] ".$this->user->lastname."\n");
                fclose($file);
            } 

        $sp = storage_path();
        return view('timekeeping.dtrSheet-locks',compact('payrollPeriod','paycutoffs','sp'));
      }
      else {
        if($this->user->id !== 564 ) {
              $file = fopen('storage/uploads/log.txt', 'a') or die("Unable to open logs");
                fwrite($file, "-------------------\n Attempt LockReport on " . $correct->format('M d h:i A'). " by [". $this->user->id."] ".$this->user->lastname."\n");
                fclose($file);
            } 
        return view('access-denied');
      }

    }

    public function getAllNotLocked(Request $request)
    {



      $cutoff = explode('_', $request->c);
      DB::connection()->disableQueryLog();
      $locks = new Collection;
      $ps = Carbon::parse($cutoff[0],'Asia/Manila');
      $pe = Carbon::parse($cutoff[1],'Asia/Manila');
      $totaldays = ($ps->diffInDays($pe))+1;

      $allDTR = collect(DB::table('user_dtr')->where('productionDate','>=',$cutoff[0])->where('productionDate','<=',$cutoff[1])->
                join('users','users.id','=','user_dtr.user_id')->
                join('team','team.user_id','=','users.id')->
                join('campaign','campaign.id','=','team.campaign_id')->
                select('user_dtr.user_id','users.lastname','users.firstname','campaign.name as program', 'user_dtr.productionDate')->
                orderBy('users.lastname')->get())->groupBy('user_id');

      foreach ($allDTR as $a) {
        if(count($a) <= $totaldays)
          $locks->push(['deets'=>$a[0],'count'=>count($a),'cutoffstart'=>$cutoff[0], 'cutoffend'=>$cutoff[1],'totaldays'=>$totaldays ]);
      }

      return $locks; //[0]['deets']->program;
    }



    public function getValidatedDTRs(Request $request)
    {
      //------ Report type 1= DTR logs | 2= Summary

      if ($request->reportType == 'finance')
        $result = $this->fetchLockedDTRs($request->cutoff, $request->program,1);
      elseif($request->reportType == 'trainees')
      {
        $stat = $request->stat;
        /*
        if($stat == 'p')
          $result = $this->fetchLockedDTRs($request->cutoff, null,4);
        elseif ($stat == 'f')
          $result = $this->fetchLockedDTRs($request->cutoff, null,5);
        else
          $result = $this->fetchLockedDTRs($request->cutoff, null,3);
          */

        //$rate = 750.00;
        if($stat == 'p') $statid=18;
        elseif ($stat == 'f') $statid=19;
        elseif ($stat == 'nh')$statid=3;
        else $statid = 2;
        $cutoff = explode('_', $request->cutoff);


        if($stat == 'nh'){
          $monthAgo = Carbon::now('GMT+8')->addDays(-30);
          $allDTRs = DB::table('users')->where([
                  ['status_id', '<=', $statid],
                          ])->
                  leftJoin('team','team.user_id','=','users.id')->
                  leftJoin('campaign','team.campaign_id','=','campaign.id')->
                  leftJoin('immediateHead_Campaigns','team.immediateHead_Campaigns_id','=','immediateHead_Campaigns.id')->
                  leftJoin('immediateHead','immediateHead_Campaigns.immediateHead_id','=','immediateHead.id')->
                  leftJoin('positions','users.position_id','=','positions.id')->
                  leftJoin('statuses','users.status_id','=','statuses.id')->
                  leftJoin('userType','userType.id','=','users.userType_id')->
                  leftJoin('floor','team.floor_id','=','floor.id')->
                  leftJoin('trainee_rate','trainee_rate.floor_id','=','floor.id')->
                  join('user_dtr', function ($join) use ($cutoff) {
                          $join->on('users.id', '=', 'user_dtr.user_id')
                               ->where('user_dtr.productionDate', '>=', $cutoff[0])
                               ->where('user_dtr.productionDate', '<=', $cutoff[1]);
                      })->
                  select('users.accesscode','users.traineeCode', 'users.employeeCode','users.id','users.isWFH', 'users.firstname','users.lastname','users.middlename', 'users.nickname','positions.name as jobTitle','campaign.id as campID', 'campaign.name as program','immediateHead_Campaigns.id as tlID', 'immediateHead.firstname as leaderFname','immediateHead.lastname as leaderLname','floor.name as location','user_dtr.productionDate','user_dtr.biometrics_id','user_dtr.workshift','user_dtr.isCWS_id as cwsID','user_dtr.leaveType','user_dtr.leave_id','user_dtr.timeIN','user_dtr.timeOUT','user_dtr.hoursWorked','user_dtr.OT_billable','user_dtr.OT_approved','user_dtr.OT_id','user_dtr.UT', 'user_dtr.user_id','user_dtr.updated_at','user_dtr.created_at','trainee_rate.rate as dailyRate')->
                  where('users.status_id','!=',2)->where('users.endTraining','!=',null)->where('users.endTraining','>=',$monthAgo->format('Y-m-d H:i:s'))->
                      orderBy('users.lastname')->get();

                      

        }
        else {
          $allDTRs = DB::table('users')->where('users.status_id',$statid)->
                      join('team','team.user_id','=','users.id')->
                      leftJoin('campaign','team.campaign_id','=','campaign.id')->
                      leftJoin('immediateHead_Campaigns','team.immediateHead_Campaigns_id','=','immediateHead_Campaigns.id')->
                      leftJoin('immediateHead','immediateHead_Campaigns.immediateHead_id','=','immediateHead.id')->
                      leftJoin('positions','users.position_id','=','positions.id')->
                      leftJoin('floor','team.floor_id','=','floor.id')->
                      leftJoin('trainee_rate','trainee_rate.floor_id','=','floor.id')->
                      join('user_dtr', function ($join) use ($cutoff) {
                          $join->on('users.id', '=', 'user_dtr.user_id')
                               ->where('user_dtr.productionDate', '>=', $cutoff[0])
                               ->where('user_dtr.productionDate', '<=', $cutoff[1]);
                      })->
                      select('users.accesscode','users.traineeCode', 'users.employeeCode','users.id','users.isWFH', 'users.firstname','users.lastname','users.middlename', 'users.nickname','positions.name as jobTitle','campaign.id as campID', 'campaign.name as program','immediateHead_Campaigns.id as tlID', 'immediateHead.firstname as leaderFname','immediateHead.lastname as leaderLname','floor.name as location','user_dtr.productionDate','user_dtr.biometrics_id','user_dtr.workshift','user_dtr.isCWS_id as cwsID','user_dtr.leaveType','user_dtr.leave_id','user_dtr.timeIN','user_dtr.timeOUT','user_dtr.hoursWorked','user_dtr.OT_billable','user_dtr.OT_approved','user_dtr.OT_id','user_dtr.UT', 'user_dtr.user_id','user_dtr.updated_at','user_dtr.created_at','trainee_rate.rate as dailyRate')->
                      orderBy('users.lastname')->get();

        }





        // $allDTRs = DB::table('users')->where('users.status_id',$statid)->
        //               join('team','team.user_id','=','users.id')->
        //               leftJoin('campaign','team.campaign_id','=','campaign.id')->
        //               leftJoin('immediateHead_Campaigns','team.immediateHead_Campaigns_id','=','immediateHead_Campaigns.id')->
        //               leftJoin('immediateHead','immediateHead_Campaigns.immediateHead_id','=','immediateHead.id')->
        //               leftJoin('positions','users.position_id','=','positions.id')->
        //               leftJoin('floor','team.floor_id','=','floor.id')->
        //               leftJoin('trainee_rate','trainee_rate.floor_id','=','floor.id')->
        //               join('user_dtr', function ($join) use ($cutoff) {
        //                   $join->on('users.id', '=', 'user_dtr.user_id')
        //                        ->where('user_dtr.productionDate', '>=', $cutoff[0])
        //                        ->where('user_dtr.productionDate', '<=', $cutoff[1]);
        //               })->
        //               select('users.accesscode','users.traineeCode', 'users.employeeCode','users.id','users.isWFH', 'users.firstname','users.lastname','users.middlename', 'users.nickname','positions.name as jobTitle','campaign.id as campID', 'campaign.name as program','immediateHead_Campaigns.id as tlID', 'immediateHead.firstname as leaderFname','immediateHead.lastname as leaderLname','floor.name as location','user_dtr.productionDate','user_dtr.biometrics_id','user_dtr.workshift','user_dtr.isCWS_id as cwsID','user_dtr.leaveType','user_dtr.leave_id','user_dtr.timeIN','user_dtr.timeOUT','user_dtr.hoursWorked','user_dtr.OT_billable','user_dtr.OT_approved','user_dtr.OT_id','user_dtr.UT', 'user_dtr.user_id','user_dtr.updated_at','user_dtr.created_at','trainee_rate.rate as dailyRate')->
        //               orderBy('users.lastname')->get();
        $groupedDTRs = collect($allDTRs)->groupBy('user_id');
        $total = count($groupedDTRs);

        $traineeDTR = new Collection;


        foreach($groupedDTRs as $employeeDTR)
        {
          $i = 0;
          //$dData = collect($employeeDTR)->sortBy('productionDate')->where('productionDate',$payday->format('Y-m-d'));
          //$dData = collect($allDTRs)->where('id',$employeeDTR->first()->id)->sortBy('productionDate');

          if (count($employeeDTR) > 0)
          {

            //'Employee Code'::'Formal Name'::'Date'::'Day'::
            // Time IN'::'Time OUT'::'Hours':: 'OT billable'::'OT Approved'::'OT Start'::'OT End'::'OT hours'::'OT Reason'
            $traineeHR = 0;
            foreach ($employeeDTR as $key) 
            {
              
              

              // -------- DATE -------------
              // ** Production Date
              // check if there's holiday
              $holiday = Holiday::where('holidate',$key->productionDate)->get();

              (count($holiday) > 0) ? $hday=$holiday->first()->name : $hday = "";

              // -------- WORKED HOURS  -------------
              if (strlen($key->hoursWorked) > 5)
              {
                 $wh = strip_tags($key->hoursWorked);

                 if( strpos($wh,"[") !== false)
                 {
                    $cleanWH = explode("[", $wh);
                    $traineeHR +=  (float)$cleanWH[0]; 

                 }else if ( strpos($wh, "(")!==false )
                 {
                    $cleanWH = explode("(", $wh);
                    $traineeHR +=  (float)$cleanWH[0]; 

                 }else
                 {
                    $cleanWH = explode(" ", $wh);
                    $traineeHR +=  (float)$cleanWH[0];

                 }
                  //$arr[$i] = $wh; $i++;

              }else{ 

                if( strpos($key->hoursWorked,"N") !== false)
                  $traineeHR += 0; //strip_tags($key->hoursWorked);
                else
                  $traineeHR += (float)strip_tags($key->hoursWorked);
              }

              


              
            }

            $sahod = number_format(($traineeHR/8)*$key->dailyRate,2);

            $traineeDTR->push(['id'=>$key->user_id, 'firstname'=>$key->firstname,'lastname'=>$key->lastname,'workedHours'=>$traineeHR,'jobTitle'=>$key->jobTitle,'leaderFname'=>$key->leaderFname,'leaderLname'=>$key->leaderLname,'rate'=>$key->dailyRate,'sahod'=>$sahod]);
            $traineeHR=0;

          }else{}
          

        }//end foreach employee

        return response()->json(['DTRs'=>$allDTRs,'total'=>$total,'submitted'=>$total, 'program'=>'TRAINEES', 'groupedDTRs'=>$groupedDTRs,'cutoffstart'=>$cutoff[0],'cutoffend'=>$cutoff[1],'traineeDTR'=>$traineeDTR]);

       
      }
      else
         $result = $this->fetchLockedDTRs($request->cutoff, $request->program,null);

      return $result[0];

    }

    public function manage(Request $request)
    {
      if (count($request->issue) >= 1) {

        $requestor = User::find($request->user_id);

        $coll = new Collection;
        foreach ($request->issue as $key) {

          switch ($key) {
            case '1': { /*Timekeeping trait */ $this->saveCWS($request); } break;
            case '2': { /*Timekeeping trait */ $this->saveDTRP($request,"IN",$requestor); }break;
            case '3': { /*Timekeeping trait */ $this->saveDTRP($request,"OUT",$requestor); }break;
            case '4': { /*Timekeeping trait */ //$res = $this->saveLeave($request,$requestor); 
                      }break;
          }
        }

        return Redirect::back(); 
       // return response()->json($res);

      } else{
        return Redirect::back();
      }

    }


    public function overrideRD($id,Request $request)
    {
      $bio = Biometrics::find($id);
      $u = User::find($request->user_id);

      $existing = User_RDoverride::where('biometrics_id',$bio->id)->where('user_id',$u->id)->get();
      if (count($existing) > 0)
      {
        return redirect()->back();

      }else
      {
        $override = new User_RDoverride;
        $override->biometrics_id = $bio->id;
        $override->user_id = $u->id;
        $override->created_at = Carbon::now('GMT+8')->format('Y-m-d H:i:s');
        $override->save();
        return redirect()->back(); //action('DTRController@show',$u->id);
      }

      //return response()->json(['bio'=>$bio,'u'=>$u]);
    }


    //******* used to create DTR sheet entries for locking
    //******* a.k.a function lock()
    public function processSheet($id, Request $request)
    {
      //$user = User::find($id);
      $dtrSheet = $request->dtrsheet;
      $coll = new Collection;

      foreach ($dtrSheet as $d) {

        // we need to double check first kung existing na for that prodDate
        $pdate = Carbon::parse($d['productionDate'],"Asia/Manila")->format('Y-m-d');
        $existing = User_DTR::where('user_id',$id)->where('productionDate',$pdate)->get();

        if (count($existing) > 0)
        {
          $dtr = $existing->first();
          $dtr->workshift = $d['workshift'];
          $dtr->isCWS_id = $d['cws_id'];
          $dtr->timeIN = $d['timeIN'];
          $dtr->timeOUT = $d['timeOUT'];
          $dtr->isDTRP_in = $d['dtrpIN'];
          $dtr->isDTRP_out = $d['dtrpOUT'];
          $dtr->hoursWorked = $d['hoursWorked'];
          $dtr->leave_id = $d['leaveID'];
          $dtr->leaveType = $d['leaveType'];

          $dtr->OT_billable = $d['OT_billable'];
          $dtr->OT_approved = $d['OT_approved'];
          $dtr->OT_id = $d['OT_id'];
          $dtr->UT = $d['UT'];
          $dtr->push();
          $coll->push($dtr);


        }else
        {
          $dtr = new User_DTR;
          $dtr->user_id = $id;
          $dtr->biometrics_id = $d['id'];
          $dtr->productionDate = Carbon::parse($d['productionDate'],"Asia/Manila")->format('Y-m-d');
          $dtr->workshift = $d['workshift'];
          $dtr->isCWS_id = $d['cws_id'];
          $dtr->timeIN = $d['timeIN'];
          $dtr->timeOUT = $d['timeOUT'];
          $dtr->isDTRP_in = $d['dtrpIN'];
          $dtr->isDTRP_out = $d['dtrpOUT'];
          $dtr->hoursWorked = $d['hoursWorked'];
          $dtr->leave_id = $d['leaveID'];
          $dtr->leaveType = $d['leaveType'];

          $dtr->OT_billable = $d['OT_billable'];
          $dtr->OT_approved = $d['OT_approved'];
          $dtr->OT_id = $d['OT_id'];
          $dtr->UT = $d['UT'];
          $dtr->save();
          $coll->push($dtr);

        }
        
      }

      
      //$dtr->save();

      

      return response()->json($coll);

    }


    public function requestUnlock($id, Request $request)
    {
      $user = User::find($id);
      $payrollPeriod = $request->payrollPeriod;

     

      /*----------------------------
      This is where you check if lagpas na ng sahod
      If lagpas na ng sahod, only Finance can unlock. Else, approvers may still do

      if ($payrollPeriod[0]) == 21 -> sahod is nextMonth 10th ==> cutoff
      if ($payrollPeriod[0]) == 06 -> sahod is this month 25th ==> cutoff

      if (date_today > $cutoff) -> only Finance can unlock, send notif to Finance admin only
      else send notif to all approvers
      ------------------------------*/

      $dtr = User_DTR::where('user_id',$user->id)->where('productionDate',Carbon::parse($payrollPeriod[0],'Asia/Manila')->format('Y-m-d'))->get();

      if(count($dtr)<= 0)
      {
        return response()->json(['success'=>'0', 'message'=>"No User DTR record found."]);
      } else{

        $notification = new Notification;
        $notification->relatedModelID = $dtr->first()->id;

        (count($payrollPeriod) > 1) ? $nType = 14 : $nType =19;

        $notification->type = $nType; //UNLOCK DTR
        $notification->from = $user->id;
        $notification->save();

        foreach ($user->approvers as $approver) {
          $TL = ImmediateHead::find($approver->immediateHead_id);

          
          $nu = new User_Notification;
          $nu->user_id = $TL->userData->id;
          $nu->notification_id = $notification->id;
          $nu->seen = false;
          $nu->save();

          
          

          # code...
        }

        foreach ($payrollPeriod as $p) {

          $existingUnlock = User_Unlocks::where('user_id',$user->id)->where('productionDate',$p)->get();

          if(count($existingUnlock) <= 0)
          {
            $unl = new User_Unlocks;
            $unl->user_id = $user->id;
            $unl->productionDate = $p;
            $unl->created_at = Carbon::now('GMT+8');
            $unl->save();

          }
          

        }


        return response()->json(['success'=>'1', 'message'=>"DTR Unlock request sent for approval.", 'count'=>count($payrollPeriod)]);


      }

      



      /*if (!$anApprover) //(!$TLsubmitted && !$canChangeSched)
        {//--- notify the  APPROVERS

            

            foreach ($employee->approvers as $approver) {
                
                // NOW, EMAIL THE TL CONCERNED
            
                $email_heading = "New Vacation Leave Request from: ";
                $email_body = "Employee: <strong> ". $employee->lastname.", ". $employee->firstname ."  </strong><br/>
                               Date: <strong> ".$vl->leaveStart  . " to ". $vl->leaveEnd. " </strong> <br/>";
                $actionLink = action('UserVLController@show',$vl->id);
               
                //  Mail::send('emails.generalNotif', ['user' => $TL, 'employee'=>$employee, 'email_heading'=>$email_heading, 'email_body'=>$email_body, 'actionLink'=>$actionLink], function ($m) use ($TL) 
                //  {
                //     $m->from('OES@openaccessbpo.net', 'OES-OAMPI Evaluation System');
                //     $m->to($TL->userData->email, $TL->lastname.", ".$TL->firstname)->subject('New CWS request');     

                    
                //          $file = fopen('storage/uploads/log.txt', 'a') or die("Unable to open logs");
                //             fwrite($file, "-------------------\n Email sent to ". $TL->userData->email."\n");
                //             fclose($file);                      
                

                // }); //end mail 


            
            }
            

        } */

    }

    
    public function seenzoned($id)
    {
      //$theNotif = Notification::find($id);
      $seen = User_Notification::where('notification_id',$id)->where('user_id',$this->user->id)->get();
      $theNotif = Notification::find($id);

      ($theNotif->from == $this->user->id) ? $theSender=true : $theSender=false;


      if (count($seen)>0)
      {
        $seen->first()->seen = true;
        $seen->first()->save();
        //$coll = new Collection;

        
        if ($theSender)
        $fromDate = Carbon::parse(Biometrics::find($theNotif->relatedModelID)->productionDate,"Asia/Manila");
        else{
          //now redirect it to the DTR sheet
          $theDTR = User_DTR::find(Notification::find($seen->first()->notification_id)->relatedModelID);
          if (count((array)$theDTR)>0) $fromDate = Carbon::parse($theDTR->productionDate,"Asia/Manila");
          else return view('empty');

        }
        

        if ($fromDate->format('d') == '06')
        {
          $toDate = Carbon::parse($fromDate->format('Y-m')."-20","Asia/Manila");
        }else{

          if($theSender)
          {
            $td = Carbon::parse(Biometrics::find($theNotif->relatedModelID)->productionDate,"Asia/Manila")->addMonth();
            
          }
          else {
            $td = Carbon::parse($theDTR->productionDate,"Asia/Manila")->addMonth();
          }
          
          
          $toDate = Carbon::parse($td->format('Y-m')."-05","Asia/Manila");

        }

        if ($theSender)
          return redirect()->action('DTRController@show',['id'=>$theNotif->from, 'from'=>$fromDate->format('Y-m-d'), 'to'=>$toDate->format('Y-m-d')]);
          
        else
          return redirect()->action('DTRController@show',['id'=>$theDTR->user_id, 'from'=>$fromDate->format('Y-m-d'), 'to'=>$toDate->format('Y-m-d')]);

        
      } else
      {
        return view('empty');
      }

      
    }

    //*** This is Production Date specific
    public function seenzonedPD($id)
    {
      //$theNotif = Notification::find($id);
      $seen = User_Notification::where('notification_id',$id)->where('user_id',$this->user->id)->get();
      $theNotif = Notification::find($id);


      ($theNotif->from == $this->user->id) ? $theSender=true : $theSender=false;


      if (count($seen)>0)
      {
        $seen->first()->seen = true;
        $seen->first()->save();
        //$coll = new Collection;

        
        if ($theSender)
        {
          //**check mo muna kung existing pa USer_DTR, else Biometrics na nilipat ung relatedModel
          if (User_DTR::find($theNotif->relatedModelID) !== null){
            $fromDate = Carbon::parse(User_DTR::find($theNotif->relatedModelID)->productionDate,"Asia/Manila");
            $toDate = Carbon::parse(User_DTR::find($theNotif->relatedModelID)->productionDate,"Asia/Manila");

          }else{
            $fromDate = Carbon::parse(Biometrics::find($theNotif->relatedModelID)->productionDate,"Asia/Manila");
            $toDate = Carbon::parse(Biometrics::find($theNotif->relatedModelID)->productionDate,"Asia/Manila");
          }
          

        }
        
        else{
          //now redirect it to the DTR sheet
          $theDTR = User_DTR::find(Notification::find($seen->first()->notification_id)->relatedModelID);
          if (count((array)$theDTR)>0){
            $fromDate = Carbon::parse($theDTR->productionDate,"Asia/Manila");
            $toDate = Carbon::parse($theDTR->productionDate,"Asia/Manila");
          } 
          else return view('empty');

        }
        

      

        if ($theSender)
          return redirect()->action('DTRController@show',['id'=>$theNotif->from, 'from'=>$fromDate->format('Y-m-d'), 'to'=>$toDate->format('Y-m-d')]);
          
        else
          return redirect()->action('DTRController@show',['id'=>$theDTR->user_id, 'from'=>$fromDate->format('Y-m-d'), 'to'=>$toDate->format('Y-m-d')]);

        
      } else
      {
        return view('empty');
      }

      
    }

  



    public function show($id, Request $request )
    {
      //return $pass = bcrypt('rcruz'); //$2y$10$IQqrVA8oK9uedQYK/8Z4Ae9ttvkGr/rGrwrQ6JVKdobMBt/5Mj4Ja
        DB::connection()->disableQueryLog();
        $user = User::find($id);

        if (is_null($user)) return view('empty');

        ($user->status_id == 12 || $user->status_id == 14) ? $isParttimer = true : $isParttimer=false;

        $collect = new Collection; 
        $coll = new Collection;
        $coll2 = new Collection;
        $roles = UserType::find($this->user->userType_id)->roles->pluck('label'); //->where('label','MOVE_EMPLOYEES');
        $canViewOtherDTR =  ($roles->contains('VIEW_OTHER_DTR')) ? '1':'0';
        $canViewTeamDTR =  ($roles->contains('VIEW_SUBORDINATE_DTR')) ? '1':'0';
        $canPreshift = ($roles->contains('UPLOAD_BIOMETRICS')) ? '1' : '0';
        $canChangeSched =  ($roles->contains('CHANGE_EMPLOYEE_SCHEDULE')) ? '1':'0';

        /* -------- get this user's department. If Backoffice, WFM can't access this ------*/
        $isBackoffice = ( Campaign::find(Team::where('user_id',$user->id)->first()->campaign_id)->isBackoffice ) ? true : false;
        $isWorkforce =  ($roles->contains('STAFFING_MANAGEMENT')) ? '1':'0';

        $hrDept = Campaign::where('name',"HR")->first();
        $financeDept = Campaign::where('name',"Finance")->first();

        $financeteam = Team::where('user_id',$this->user->id)->where('campaign_id',$financeDept->id)->get();
        (count($financeteam) > 0) ? $isFinance=1 : $isFinance=0;

        $ndy = Team::where('user_id',$user->id)->where('campaign_id',54)->get();
        (count($ndy) > 0) ? $isNDY = 1 : $isNDY=0;


        $paycutoffs = Paycutoff::orderBy('id','DESC')->get();// return $paycutoffs;

        

        /*------- check first if user is entitled for a leave (Regualr employee or lengthOfService > 6mos) *********/
        $today=Carbon::now('GMT+8'); //today();
        $phY = Carbon::now('GMT+8')->format('Y');
        $lengthOfService = Carbon::parse($user->dateHired,"Asia/Manila")->diffInMonths($today);
        $hasVLCreditsAlready = DB::table('user_vlcredits')->where('user_id',$user->id)->where('creditYear',$phY)->get();
        $hasSLCreditsAlready = DB::table('user_slcredits')->where('user_id',$user->id)->where('creditYear',$phY)->get();
        ($lengthOfService >= 6 || count($hasVLCreditsAlready) > 0 || count($hasSLCreditsAlready) > 0 ) ? $entitledForLeaves=true : $entitledForLeaves=false;


        $leadershipcheck = ImmediateHead::where('employeeNumber', $user->employeeNumber)->first();
        $immediateHead = ImmediateHead::find(ImmediateHead_Campaign::find($user->supervisor->immediateHead_Campaigns_id)->immediateHead_id);
        if($immediateHead->employeeNumber == $this->user->employeeNumber ) $theImmediateHead = true; else $theImmediateHead=false;

        //if (!empty($leadershipcheck)){ $camps = $leadershipcheck->campaigns->sortBy('name'); } else $camps = $user->campaign;
        if (!empty($leadershipcheck))
            { 
              $camps = "";
              $camps1 = $leadershipcheck->campaigns->sortBy('name'); 

              
              //sreturn $leadershipcheck->campaigns;
              foreach($leadershipcheck->campaigns as $c)
              {

                //return $leadershipcheck->campaigns->first()->pivot->immediateHead_id;// camps1->pivot->immediateHead_id;
              /* ------- ADDED FOR  DISABLED IH_CAMPS ----- */
                $ihCamp = ImmediateHead_Campaign::where('campaign_id', $c->pivot->campaign_id)->where('immediateHead_id',$leadershipcheck->id)->first();
                $cmp = Campaign::find($c->pivot->campaign_id);

                if($ihCamp->disabled){ //do nothing

                }else
                {
                  if (count($leadershipcheck->myCampaigns) <= 1){
                    
                    $camps .='<a href="../campaign/'.$cmp->id.'" target="_blank" >'. $cmp->name.' </a>';
                   }
                    
                    else $camps .= '<a href="../campaign/'.$cmp->id.'" target="_blank" >'. $cmp->name.' </a> , ';

                }

                   

                

              }
              

            } else $camps = '<a href="../campaign/'.$user->campaign->first()->id.'" target="_blank" >'.$user->campaign->first()->name.'</a>';

            if (strlen($camps) < 1)
              $camps = '<a href="../campaign/'.$user->campaign->first()->id.'" target="_blank" >'.$user->campaign->first()->name.'</a>';







       // check if viewing is not an agent, an HR personnel, or the owner, or youre the immediateHead $this->user->campaign_id == $hrDept->id 
        
        //--- also find the head of that immediate head for Program Mgr access
        $leader_L2 = User::where('employeeNumber',$immediateHead->employeeNumber)->first();

        if (count((array)$leader_L2->supervisor) > 0) {
          $leader_L1 = ImmediateHead::find(ImmediateHead_Campaign::find($leader_L2->supervisor->immediateHead_Campaigns_id)->immediateHead_id);
          $leader_L0 = ImmediateHead::find(ImmediateHead_Campaign::find(User::where('employeeNumber',$leader_L1->employeeNumber)->first()->supervisor->immediateHead_Campaigns_id)->immediateHead_id);
          $leader_PM = ImmediateHead::find(ImmediateHead_Campaign::find(User::where('employeeNumber',$leader_L0->employeeNumber)->first()->supervisor->immediateHead_Campaigns_id)->immediateHead_id);
        }
        else {
          $leader_L1 = null;$leader_L0 =null; $leader_PM=null;
        }

        
        

        // $coll = new Collection;

        $DTRapprovers = '<strong>';
        $ctr = 0;


        /**************  LEAVE CREDITS ****************/
        $leave1 = Carbon::parse('first day of January '. $today->format('Y'),"Asia/Manila")->format('Y-m-d');
        $leave2 = Carbon::parse('last day of December '.$today->format('Y'),"Asia/Manila")->format('Y-m-d');
        $currentVLbalance ="N/A";
        $updatedVL = false;
        $currentSLbalance ="N/A";
        $updatedSL = false;


        /*if ($lengthOfService >= 6) //do this if only 6mos++
        {*/
          $today= Carbon::now('GMT+8')->format('m'); //date('m');//today();
          $avail = $user->vlCredits;
          $avail2 = $user->slCredits;

          

          $vlEarnings = DB::table('user_vlearnings')->where('user_vlearnings.user_id',$user->id)->
                              join('vlupdate','user_vlearnings.vlupdate_id','=', 'vlupdate.id')->
                              select('vlupdate.credits','vlupdate.period')->where('vlupdate.period','>=', Carbon::parse($phY.'-01-01','Asia/Manila')->format('Y-m-d'))->orderBy('vlupdate.period','DESC')->get(); 
          $totalVLearned = collect($vlEarnings)->sum('credits');

          $slEarnings = DB::table('user_slearnings')->where('user_slearnings.user_id',$user->id)->
                              join('slupdate','user_slearnings.slupdate_id','=', 'slupdate.id')->
                              select('slupdate.credits','slupdate.period')->where('slupdate.period','>=', Carbon::parse($phY.'-01-01','Asia/Manila')->format('Y-m-d'))->orderBy('slupdate.period','DESC')->get();

          $totalSLearned = collect($slEarnings)->sum('credits');

          $approvedVLs = User_VL::where('user_id',$user->id)->where('isApproved',1)->where('leaveStart','>=',$leave1)->where('leaveEnd','<=',$leave2)->get();
          $approvedSLs = User_SL::where('user_id',$user->id)->where('isApproved',1)->where('leaveStart','>=',$leave1)->where('leaveEnd','<=',$leave2)->get();

          $vtoVL = User_VTO::where('user_id',$user->id)->where('isApproved',1)->where('productionDate','>=',$leave1)->where('productionDate','<=',$leave2)->where('deductFrom','VL')->get();
          $totalVTO_vl = number_format(collect($vtoVL)->sum('totalHours') * 0.125,2);

          $vtoSL = User_VTO::where('user_id',$user->id)->where('isApproved',1)->where('productionDate','>=',$leave1)->where('productionDate','<=',$leave2)->where('deductFrom','SL')->get();
          $vtoSL2 = User_VTO::where('user_id',$user->id)->where('isApproved',1)->where('productionDate','>=',$leave1)->where('productionDate','<=',$leave2)->where('deductFrom','AdvSL')->get();
          $totalVTO_sl1 = number_format(collect($vtoSL)->sum('totalHours') * 0.125,2);
          $totalVTO_sl2 = number_format(collect($vtoSL2)->sum('totalHours') * 0.125,2);
          $totalVTO_sl = $totalVTO_sl1 + $totalVTO_sl2;

          $canVL = 0; $canSL = 0;

            /************ for VL ************/
            if (count($avail)>0){
              $vls = $user->vlCredits->sortByDesc('creditYear');

              if($vls->contains('creditYear',$phY))
              {
                $updatedVL=true;
                $currentVLbalance= number_format(($vls->first()->beginBalance - $vls->first()->used + $totalVLearned) - $vls->first()->paid - $totalVTO_vl,2);

                if($isParttimer) {if($currentVLbalance >= 0.25) $canVL=1;}
                else  {if($currentVLbalance >= 0.5) $canVL=1;}
              }
              else
              {
                $currentVLbalance = "N/A";
                
                // if (count($approvedVLs)>0)
                // {
                //   $bal = 0.0;
                //   foreach ($approvedVLs as $key) {
                //     $bal += $key->totalCredits;
                //   }

                //   $currentVLbalance = "N/A"; //(0.84 * $today) - $bal;

                // }else{

                //   $currentVLbalance = "N/A";// (0.84 * $today);
                // }

              } 



            }else {
              
              $currentVLbalance = "N/A";

              // if (count($approvedVLs)>0){
              //   $bal = 0.0;
              //   foreach ($approvedVLs as $key) {
              //     $bal += $key->totalCredits;
              //   }

              //   $currentVLbalance = "N/A";// (0.84 * $today) - $bal;

              // }else{

              //   $currentVLbalance = "N/A";// (0.84 * $today);
              // }
              
            }


            /************ for SL ************/
             if (count($avail2)>0)
             {
              $sls = $user->slCredits->sortByDesc('creditYear');

              if($sls->contains('creditYear',$phY))
              {
                $updatedSL=true;

                //get advanced SLs
                $adv = DB::table('user_advancedSL')->where('user_id',$user->id)->get();

                $advancedSL = 0;
                foreach ($adv as $a) {
                  if ( date('Y') == date('Y', strtotime($a->periodStart)) )
                    $advancedSL += $a->total;
                }

                $currentSLbalance = number_format((($sls->first()->beginBalance - $sls->first()->used + $totalSLearned) - $sls->first()->paid)-$advancedSL - $totalVTO_sl,2);

                if($isParttimer) {if($currentSLbalance >= 0.25) $canSL=1;}
                else{ if($currentSLbalance >= 0.5) $canSL=1;}
                                   
              }
              else
              {
                $currentSLbalance = "N/A";
                
                // if (count($approvedSLs)>0)
                // {
                //   $bal = 0.0;
                //   foreach ($approvedSLs as $key) {
                //     $bal += $key->totalCredits;
                //   }

                //   $currentSLbalance = "N/A";// (0.84 * $today) - $bal;

                // }else{

                //   $currentSLbalance = "N/A";// (0.84 * $today);
                // }

              }
            }else {
              
              $currentSLbalance = "N/A";

              // if (count($approvedSLs)>0){
              //   $bal = 0.0;
              //   foreach ($approvedSLs as $key) {
              //     $bal += $key->totalCredits;
              //   }

              //   $currentSLbalance = "N/A";// (0.84 * $today) - $bal;

              // }else{

              //   $currentSLbalance = "N/A";// (0.84 * $today);
              // }
              
            }

        //}
        
            

        /* ------- APPROVERS *-------*/
        $approvers = $user->approvers;
        foreach ($approvers as $key) {
          $DTRapprovers .= ImmediateHead::find($key->immediateHead_id)->firstname . " ";
          $DTRapprovers .= ImmediateHead::find($key->immediateHead_id)->lastname ; $ctr++;

          if ($ctr < count($user->approvers)) $DTRapprovers.= " | ";
        }
        $DTRapprovers .= '</strong>'; 
        $TLapprover = $this->getTLapprover($user->id, $this->user->id);

        // get WFM
        $wfm = collect(DB::table('team')->where('campaign_id',50)->
                    leftJoin('users','team.user_id','=','users.id')->
                    select('team.user_id')->
                    where('users.status_id',"!=",7)->
                    where('users.status_id',"!=",8)->
                    where('users.status_id',"!=",9)->
                    where('users.status_id',"!=",13)->get())->pluck('user_id');
        $isWorkforce = in_array($this->user->id, $wfm->toArray());
        $employeeisBackoffice = $isBackoffice; // ( Campaign::find(Team::where('user_id',$user->id)->first()->campaign_id)->isBackoffice ) ? true : false;

        $specialChild = DB::table('user_specialPowers')->where('user_specialPowers.user_id',$this->user->id)->
                          leftJoin('user_specialPowers_programs','user_specialPowers_programs.specialPower_id','=','user_specialPowers.id')->
                          select('user_specialPowers_programs.program_id')->get();
        
        if (count($specialChild) > 0){
          $sc = collect($specialChild)->pluck('program_id')->toArray();

          (in_array($user->supervisor->campaign_id, $sc)) ? $hasAccess=1 : $hasAccess=0;
        }else $hasAccess=0;

        
       

        // check if viewing is not an agent, an HR personnel, or the owner, or youre the immediateHead, our you're Program Manager



        //Timekeeping Trait
        $anApprover = $this->checkIfAnApprover($approvers, $this->user);
        $fromYr = Carbon::parse($user->dateHired)->addMonths(6)->format('Y');

        ($this->user->employeeNumber==$leader_L1->employeeNumber) ? $leader_lv1=1 : $leader_lv1=0;
        ($this->user->employeeNumber==$leader_L0->employeeNumber) ? $leader_lv2=1 : $leader_lv2=0; 
        ($this->user->employeeNumber==$leader_PM->employeeNumber) ? $leader_lv3=1 : $leader_lv3=0;
        
        if ( ($isWorkforce && !$isBackoffice) || $canViewOtherDTR || $anApprover || $this->user->id == $id || $hasAccess 
        || ($theImmediateHead || $leader_lv1 || $leader_lv2 || $leader_lv3 ) && $canViewTeamDTR )  
        {     
          if(empty($request->from) && empty($request->to) )
          {
            $currentPeriod = array();

            //Timekeeping trait getCutoffStartEnd()
            $cData = $this->getCutoffStartEnd();
            $cutoffStart = Carbon::parse($cData['currentPeriod'][0],'Asia/Manila');//->cutoffStart;
            $cutoffEnd = $cData['cutoffEnd'];
            $cutoffID = $cData['cutoffID'];
            $currentPeriod[0]= $cData['currentPeriod'][0];
            $currentPeriod[1]= $cData['currentPeriod'][1];

                
          }else 
          {
            $currentPeriod[0] = $request->from;
            $currentPeriod[1] = $request->to;
            $cutoffStart = new Carbon($currentPeriod[0]);
            $cutoffEnd = new Carbon($currentPeriod[1]);

            if (count($cid = Paycutoff::where('fromDate',$request->from)->get())>0)
              $cutoffID = $cid->first()->id;
            else $cutoffID=0;
           

          }


          //return $cData;

              
           $getday = explode('-',$currentPeriod[0]); 
           if ($getday[2] < Cutoff::first()->second)
            {
              //$prevF= Carbon::createFromDate(null,date('m',strtotime($currentPeriod[0])),Cutoff::first()->second+1);
              $prevF= Carbon::createFromDate(date('Y',strtotime($currentPeriod[0])),date('m',strtotime($currentPeriod[0])),Cutoff::first()->second+1);
              $prevFrom = $prevF->subMonth()->format('Y-m-d');
              
              $prevTo = Carbon::parse($currentPeriod[0])->subDay()->format('Y-m-d');
              $nextFrom = Carbon::parse($currentPeriod[1])->addDay()->format('Y-m-d');
              $nextTo = Carbon::createFromDate(date('Y',strtotime($currentPeriod[1])),date('m',strtotime($currentPeriod[1]))+1,Cutoff::first()->first)->format('Y-m-d');

            }
            else
            {
              $m = date('m',strtotime($currentPeriod[0]));
              $y = date('Y',strtotime($currentPeriod[0]));

              $prevFrom = Carbon::createFromDate($y,$m,Cutoff::first()->first+1)->format('Y-m-d');
              $prevTo = Carbon::parse($currentPeriod[0])->subDay()->format('Y-m-d');
              $nextFrom = Carbon::parse($currentPeriod[1])->addDay()->format('Y-m-d');
              $nextTo = Carbon::createFromDate(date('Y',strtotime($currentPeriod[1])),date('m',strtotime($currentPeriod[1])),Cutoff::first()->second)->format('Y-m-d');
            }

             $cutoff = date('M d, Y', strtotime($currentPeriod[0])). " - ". date('M d,Y', strtotime($currentPeriod[1])); 
             //return $cutoff;


             // ---------------------------
             // Generate cutoff period
             //----------------------------

             $payrollPeriod = [];
             
             $noWorkSched = false;

             //Timekeeping Trait
             $payrollPeriod = $this->getPayrollPeriod($cutoffStart,$cutoffEnd); 
             //return response()->json(['cData'=>$cData, 's'=>$cutoffStart,'e'=>$cutoffEnd, 'payrollPeriod'=>$payrollPeriod]); //$payrollPeriod;
            

             // ---------------------------  INITIALIZATIONS
             $myDTR = new Collection;
             $daysOfWeek = array('Mon','Tue','Wed','Thu','Fri','Sat','Sun'); // for Gregorian cal. Iba kasi jddayofweek sa PHP day
             $coll = new Collection; $nightShift=""; $panggabi=""; $approvedOT=0; $billableForOT=0; $UT=0; $workSched=null; 
             $hasApprovedCWS=false; $usercws=null;$userOT=null; $OTattribute=""; $hasOT=false; $hasApprovedOT=false; $isFlexitime=null;$workedHours=null; $isRDToday=null;
             $hybridSched_WS_fixed = null;$hybridSched_WS_monthly = null;$hybridSched_RD_fixed = null;$hybridSched_RD_monthly=null;

             $hasLeave=null;
             $shiftStart2=null;$shiftEnd2=null;
             $hybridSched = null;
             $shifts = $this->generateShifts('12H','full');
             $shift4x11 = $this->generateShifts('12H','4x11');
             $partTimes = $this->generateShifts('12H','part');


             $noWorkSched = true;
             $holiday = " ";



             $allECQ = DB::table('ecq_statuses')->select('id','name')->orderBy('id')->get();
             

             // *************************** VERIFIED DTR SHEET
             $verifiedDTR = User_DTR::where('user_id',$user->id)->where('productionDate','>=',$currentPeriod[0])->
                                                  where('productionDate','<=',$currentPeriod[1])->orderBy('productionDate','ASC')->get();
             $startWFH = Biometrics::where('productionDate',date('Y-m-d', strtotime($currentPeriod[0])) )->first();
             

                                                 
            $cp0 =$currentPeriod[0]; 
            $cp1 = $currentPeriod[1];
            $paystart = $currentPeriod[0];
            $payend = $currentPeriod[1];
            /*if (  count($verifiedDTR) >= count($payrollPeriod)  )//|| ($currentPeriod[0] == $currentPeriod[1])
             {

                $myDTRSheet = $verifiedDTR;
                
                $ecq = ECQ_Workstatus::where('user_id',$user->id)->get();
                $wfhData = Logs::where('user_id',$user->id)->where('manual',1)->get();//->where('biometrics_id','>=',$startWFH->id)
                return view('timekeeping.myDTRSheet', compact('ecq', 'wfhData', 'fromYr', 'payrollPeriod', 'anApprover','isWorkforce','employeeisBackoffice', 'TLapprover', 'DTRapprovers', 'canChangeSched', 'paycutoffs', 'shifts','shift4x11', 'cutoffID', 'myDTRSheet','camps','user','theImmediateHead', 'immediateHead','cutoff','noWorkSched', 'prevTo','prevFrom','nextTo','nextFrom','paystart','payend','currentVLbalance','currentSLbalance','cp0','cp1'));
 

             }*/



             // *************************** end VERIFIED DTR SHEET


             // ---------------------------
             // Determine first if FIXED OR SHIFTING sched
             // and then get WORKSCHED and RD sched
             // ---------------------------
             
             $endp = count($payrollPeriod)-1; 
             //return response()->json(['p'=>$payrollPeriod,'cutoffStart'=>$cutoffStart,'end'=>$cutoffEnd]);

             $groupedFixedSched = collect($user->fixedSchedule)->sortByDesc('schedEffectivity')->groupBy('schedEffectivity');
             $monthlyScheds = DB::table('monthly_schedules')->where('user_id',$user->id)->where('productionDate','>=',$payrollPeriod[0])->where('productionDate','<=',$payrollPeriod[$endp])->orderBy('created_at','DESC')->get();

             //return collect($user->fixedSchedule)->sortByDesc('schedEffectivity')->where('isRD',1)->groupBy('schedEffectivity');

            
             //if (count($user->monthlySchedules) > 0)
             if (count($monthlyScheds) > 0)
             {


                /* ------ check mo muna kung hybrid sched ----*/
                if ( $user->fixedSchedule->isEmpty() )
                {

               
                  /*$workSched = MonthlySchedules::where('user_id',$id)->where('productionDate','>=', $currentPeriod[0])->where('productionDate','<=',$currentPeriod[1])->where('isRD',0)->get(); //Collection::make($monthlySched->where('isRD',0)->all());
                  $RDsched = MonthlySchedules::where('user_id',$id)->where('productionDate','>=', $currentPeriod[0])->where('productionDate','<=',$currentPeriod[1])->where('isRD',1)->get();  //$monthlySched->where('isRD',1)->all();*/

                  $workSched = collect($monthlyScheds)->where('isRD',0);
                  $RDsched = collect($monthlyScheds)->where('isRD',1);
                  $isFixedSched = false;
                  $noWorkSched = false;
                  $hybridSched = false;

                }else //------------------------- HYBRID SCHED ------------------
                {

                  $hybridSched = true;
                  $noWorkSched = false;
                  $isFixedSched = false;

                  

                  $hybridSched_WS_fixed = collect($user->fixedSchedule)->sortByDesc('schedEffectivity')->
                                          where('isRD',0)->groupBy('schedEffectivity');
                  $hybridSched_WS_monthly = collect($monthlyScheds)->sortByDesc('created_at')->where('isRD',0);
                  $hybridSched_RD_fixed = collect($user->fixedSchedule)->sortByDesc('schedEffectivity')->
                                          where('isRD',1)->groupBy('schedEffectivity');
                  $hybridSched_RD_monthly = collect($monthlyScheds)->sortByDesc('created_at')->where('isRD',1);

                  $RDsched=null;
                  $workSched=null;

                  /*--- and then compare which is the latest of those 2 scheds --*/


                }
                



             } 
             else
             {
                if (count($user->fixedSchedule) > 0)
                {
                    

                    $workSched = collect($user->fixedSchedule)->sortByDesc('schedEffectivity')->where('isRD',0)->groupBy('schedEffectivity');
                    $RDsched = collect($user->fixedSchedule)->sortByDesc('schedEffectivity')->where('isRD',1)->groupBy('schedEffectivity');
                    $isFixedSched =true;
                    $noWorkSched = false;
                    $workdays = new Collection;
                    

                } else
                {
                    $noWorkSched = true;
                    $workSched = null;
                    $RDsched = null;
                    $isFixedSched = false;
                    
                }
             }

             //*** we now determine if EXEMPT employee for the work sched
             $isExempt = null;
             $exemptEmp = DB::table('user_schedType')->where('user_id',$user->id)->join('schedType','schedType.id','=','user_schedType.schedType_id')->orderBy('user_schedType.created_at','DESC')->get();
             if (count($exemptEmp) > 0)
             {
                //$workSchedule = $
                $isExempt=1;
                
             }

             // ---------------------------
             // Start Payroll generation
             // ---------------------------
            
             $schedRecord = [];
             $schedCtr = 0;
             $wsch = new Collection;$x=null;$y=null;$isWFH=null; $isOnsite=null;

             $forRD = new Collection;

         
             foreach ($payrollPeriod as $payday) 
             {
                
                $bioForTheDay1 = Biometrics::where('productionDate',$payday)->get();//first();

                if(count($bioForTheDay1) <= 0) break;
                else $bioForTheDay =  $bioForTheDay1->first();


                $carbonPayday = Carbon::parse($payday);
                $nextDay = Carbon::parse($payday)->addDay();
                $prevDay = Carbon::parse($payday)->subDay();
                $bioForTom = Biometrics::where('productionDate',$nextDay->format('Y-m-d'))->first();
                $bioForYest = Biometrics::where('productionDate',$prevDay->format('Y-m-d'))->first();
                if ( is_null($bioForTom) )
                {
                  $bioForTomorrow = new Collection;
                  $bioForTomorrow->push(['productionDate'=>$nextDay->format('Y-m-d')]);
                }
                else
                  $bioForTomorrow = $bioForTom;


                //**** check if working from home
                
                $wfh = Logs::where('user_id',$user->id)->where('biometrics_id',$bioForTheDay->id)->where('manual',1)->get();

                if($user->isWFH )// ||
                {
                  (count($wfh) > 0) ? $isWFH=true : $isWFH=false; 
                }else  {$isWFH=false; $isOnsite=true;}


                $hasCWS = false; $hasApprovedCWS=false; $hasOT=false; $hasApprovedOT=false;
                $hasLWOP=false; 
                $yest = date('D', strtotime(Carbon::parse($payday)->subDay()->format('Y-m-d')));
                $prevNumDay = array_search($yest, $daysOfWeek);


                

                $holidayToday = Holiday::where('holidate', $payday)->get();
                (count($holidayToday) > 0) ? $hasHolidayToday = true : $hasHolidayToday = false;

                if ($schedCtr==0) array_push($schedRecord, 'null');

                  //--- We now check if employee has a CWS submitted for this day
                  //**************************************************************
                  //      CWS & OT & DTRPs
                  //**************************************************************

                  $usercws =  User_CWS::where('user_id',$id)->where('biometrics_id',$bioForTheDay->id)->orderBy('updated_at','DESC')->get();
                  $approvedCWS  = User_CWS::where('user_id',$id)->where('biometrics_id',$bioForTheDay->id)->where('isApproved',1)->orderBy('updated_at','DESC')->get();
                  

                  if ( count($usercws) > 0 ) $hasCWS=true;
                  if ( count($approvedCWS) > 0 ) $hasApprovedCWS=true;


                  $userOT = User_OT::where('user_id',$id)->where('biometrics_id',$bioForTheDay->id)->get();
                  $approvedOT  = User_OT::where('user_id',$id)->where('biometrics_id',$bioForTheDay->id)->where('isApproved',1)->orderBy('updated_at','DESC')->get();
                  if ( count($userOT) > 0 ) $hasOT=true;
                  if ( count($approvedOT) > 0 ) $hasApprovedOT=true;



                 /* +++++++++++++++++ NEW PROCEDURE ++++++++++++++++++++++++++++++
  
                  We now get the actual sched for today to minimize queries and processes
                  comparing hybrids and approved CWS
                  ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ */


                  $actualSchedToday = $this->getActualSchedForToday($user,$id,$payday,$bioForTheDay, $hybridSched,$isFixedSched,$hybridSched_WS_fixed,$hybridSched_WS_monthly, $hybridSched_RD_fixed, $hybridSched_RD_monthly, $workSched, $RDsched, $approvedCWS);

                  $isRDToday = $actualSchedToday->isRDToday;
                  $actualSchedToday1 = $actualSchedToday;
                  $schedForToday =  $actualSchedToday->schedForToday;
                  (count($schedForToday) > 0) ? $noWorkSched=false : $noWorkSched=true;
                  $RDsched = $actualSchedToday->RDsched;
                  $isFixedSched =  $actualSchedToday->isFixedSched;
                  $allRD = $actualSchedToday->allRD;

                  //$workSched = $actualSchedToday->workSched;
                  /*$check_fixed_WS = $actualSchedToday->check_fixed_WS;
                   $check_fixed_RD =$actualSchedToday->check_fixed_RD;
                   $check_monthly_RD =$actualSchedToday->check_monthly_RD;
                   $check_monthly_WS =$actualSchedToday->check_monthly_WS;
                   $ard = $hybridSched;
                   $wd = $actualSchedToday->wd;*/
                   /*$hybridSched_RD_monthly = $actualSchedToday->hybridSched_RD_monthly;
                   $hybridSched_RD_fixed = $actualSchedToday->hybridSched_RD_fixed;*/

                  //another check if part timer: FOR FOREIGN CONTRACTUAL. check if 4h > work hours
                  /*( Carbon::parse($schedForToday['timeStart'],'Asia/Manila')->diffInHours(Carbon::parse($schedForToday['timeEnd'],'Asia/Manila')) > 4) ? $isParttimer=false : $isParttimer=true;

                  return response()->json(['isParttimer'=>$isParttimer, 'schedForToday'=>$schedForToday]);*/
                  

                  $actualSchedKahapon = $this->getActualSchedForToday($user,$id,$prevDay->format('Y-m-d'),$bioForYest, $hybridSched,$isFixedSched,$hybridSched_WS_fixed,$hybridSched_WS_monthly, $hybridSched_RD_fixed, $hybridSched_RD_monthly, $workSched, $RDsched, $approvedCWS);
                  $schedKahapon=$actualSchedKahapon->schedForToday;

                  
          

                /* +++++++++++++++++ END NEW PROCEDURE ++++++++++++++++++++++++++++++*/
  
                if($noWorkSched)
                {

                  if( is_null($bioForTheDay) ) 
                  {
                          $logIN = "<strong class=\"text-success\">No <br/>Biometrics</strong>";
                          $logOUT = "<strong class=\"text-success\">No <br/>Biometrics</strong>";
                          $workedHours = 'N/A';

                  }
                  else
                  {
                    $usercws = User_CWS::where('user_id',$id)->where('biometrics_id',$bioForTheDay->id)->orderBy('updated_at','DESC')->get();
                    if ( count($usercws) > 0 ) $hasCWS=true;

                     $link = action('LogsController@viewRawBiometricsData',$id);
                     $icons = "<a title=\"Verify Biometrics data\" class=\"pull-right text-danger\" style=\"font-size:1.2em;\" target=\"_blank\" href=\"".$link."\"><i class=\"fa fa-clock-o\"></i></a>";

                      $userLogIN = Logs::where('user_id',$id)->where('biometrics_id',$bioForTheDay->id)->where('logType_id',1)->orderBy('biometrics_id','ASC')->get();
                       if (count($userLogIN)==0)
                       {  
                          
                          $logIN = "<strong class=\"text-danger\">No IN</strong>".$icons;
                          $shiftStart = null;
                          $shiftEnd = "<em>No Saved Sched</em>";
                          $workedHours = "N/A";
                          
                       } else
                       {
                          $logIN = date('h:i A',strtotime($userLogIN->first()->logTime));
                          $timeStart = Carbon::parse($userLogIN->first()->logTime);
                       }


                      //--- RD OT, but check first if VALID. It should have a LogOUT AND approved OT
                      $userLogOUT = Logs::where('user_id',$id)->where('biometrics_id',$bioForTheDay->id)->where('logType_id',2)->orderBy('biometrics_id','ASC')->get();

                      //--- ** May issue: pano kung RD OT ng gabi, then kinabukasan na sya nag LogOUT. Need to check kung may approved OT from IH
                      if( count($userLogOUT)==0 )
                      {
                        $logOUT = "<strong class=\"text-danger\">No OUT</strong>".$icons;
                        $shiftStart = "<em>No Saved Sched</em>";
                        $shiftEnd = "<em>No Saved Sched</em>";

                        if ($hasHolidayToday)
                        {

                          $workedHours = "(8.0) <br/><strong>* ". $holidayToday->first()->name. " *</strong>";
                          

                        } else
                        {
                          
                          $workedHours = "N/A";
                          

                        }
                          
                      } else //--- legit OT, compute billable hours
                      {  
                        $logOUT = date('h:i A',strtotime($userLogOUT->first()->logTime));
                        $timeEnd = Carbon::parse($userLogOUT->first()->logTime);
                        $shiftStart = null;
                        $shiftEnd = "<em>No Saved Sched</em>";

                        if ($hasHolidayToday)
                        {
                           
                           
                            
                            $workedHours = "(8.0)<br/><strong>* ". $holidayToday->first()->name. " *</strong>";
                            

                        } else
                        {
                           
                            $workedHours = "N/A";

                        }
                           
                      }

                       $myDTR->push(['isRDToday'=>null,'payday'=>$payday,
                           'biometrics_id'=>$bioForTheDay->id,
                           'hasCWS'=>$hasCWS,
                            //'usercws'=>$usercws->sortByDesc('updated_at')->first(),
                           'usercws'=>$usercws->sortByDesc('updated_at'),
                           'userOT'=>$userOT,
                           'hasOT'=>$hasOT,
                           'hasLeave'=> null,
                           'isRD'=>0,
                           'isFlexitime'=>$isFlexitime,
                           'productionDate'=> date('M d, Y', strtotime($payday)),
                           'preshift'=>null,
                           'day'=> date('D',strtotime($payday)),
                           'shiftStart'=> null,
                           'shiftEnd'=>$shiftEnd,

                           'shiftStart2'=> $shiftStart2,
                           'shiftEnd2'=>$shiftEnd2,
                           'logIN' => $logIN,
                           'logOUT'=>$logOUT,
                           'dtrpIN'=>null,
                           'dtrpIN_id'=>null,
                           'dtrpOUT'=> null,
                           'dtrpOUT_id'=> null,
                           'hasPendingIN' => null,
                           'isWFH'=>$isWFH,
                           'isOnsite'=>$isOnsite,
                           'hasLeave' => null,
                           'leaveDetails'=>null,
                           'hasLWOP' => null,
                           'lockedNa'=> 0,
                           'lwopDetails'=>null,

                           'pendingDTRPin'=> null,
                           'hasPendingOUT' =>null, //$userLogOUT[0]['hasPendingDTRP'],
                           'pendingDTRPout' =>null,
                           'workedHours'=> $workedHours,
                           'billableForOT' => $billableForOT,
                           'OTattribute'=>$OTattribute,
                           'UT'=>$UT,
                           'isFixedSched'=>$isFixedSched,
                            'hasApprovedCWS'=>$hasApprovedCWS,
                           'approvedOT' => $approvedOT]);

                  }// end if isnull bioForToday
                }

                else //Has Work Sched
                {

                      if( is_null($bioForTheDay) ) 
                      {
                              $logIN = "<strong class=\"text-success\">No <br/>Biometrics</strong>";
                              $logOUT = "<strong class=\"text-success\">No <br/>Biometrics</strong>";
                              $workedHours = 'N/A';
                              

                      } else
                      {
                       

                          //**************************************************************
                          //       HYBRID SCHEDULES --------this is where we check the hybrids and classify accordingly
                          //**************************************************************

                          $check_fixed_RD=null; $check_monthly_RD=null; //initializes
                       
                          //**************************************************************
                          //       FIXED SCHED
                          //**************************************************************



                           //---------------------------
                          // to check for non same-day logs on a Rest Day, kunin mo yung prev sched
                          // if sameDayLog yun, proceed with normal RD process
                          // if prev sched is RD as well, kunin mo next sched
                          // if shift  is between 3am-2:59PM, yung logs nya eh within the day
                          // if ( ( $schedForToday->timeStart >= date('H:i:s',strtotime('03:00:00')) ) && ($schedForToday->timeStart <= date('H:i:s',strtotime('14:59:00'))) )
                          // {
                            $sameDayLog = true;
                          //} else $sameDayLog=false;

                            
                            
                            $UT = 0;
                            
                            //---------------------------------- Check if RD nya today

  
                              
                            //**************************************************************
                            //       Rest Day SCHED
                            //**************************************************************

                            if ($isRDToday)
                            {

                              $data = $this->getRDinfo($id, $bioForTheDay,null,$payday,$schedKahapon,$isFixedSched,$isParttimer);

                              //check for preshift
                              $usePreshift = DB::table('user_preshiftOverride')->where('productionDate',$payday)->where('user_id',$user->id)->get();

                              //*** pang-check kung locked na, dapat ang ipakita mo eh kung ano yung na-lock
                              $lockedNa = User_DTR::where('productionDate',$payday)->where('user_id',$user->id)->get();
                              if (count($lockedNa) > 0)
                              {
                                  $lockedSched = explode('-', $lockedNa->first()->workshift);
                                  $myDTR->push([

                                      'allData'=>$data,
                                      'approvedOT' => $data[0]['approvedOT'],
                                      'billableForOT' => $data[0]['billableForOT'],
                                      'biometrics_id'=>$bioForTheDay->id,
                                      'day'=> date('D',strtotime($payday)),
                                      'dtrpIN'=>$data[0]['dtrpIN'],
                                      'dtrpOUT'=>$data[0]['dtrpOUT'],
                                      'dtrpIN_id'=>$data[0]['dtrpIN_id'],
                                      'dtrpOUT_id'=>$data[0]['dtrpOUT_id'],
                                      'hasApprovedCWS'=> $hasApprovedCWS,
                                      'hasApprovedOT'=>$hasApprovedOT,
                                      'hasCWS'=>$hasCWS,
                                      'hasLeave' => null,
                                      'hasLWOP' => null,
                                      'hasVTO'=>null,
                                      'hasOT'=>$hasOT,
                                      'hasPendingIN' => $data[0]['hasPendingIN'],
                                      'hasPendingOUT' => $data[0]['hasPendingOUT'],
                                      'isRDToday'=>$isRDToday, 
                                      'isRD'=>$isRDToday,
                                      'isFixedSched'=>$isFixedSched,
                                      'isFlexitime'=> $isFlexitime,
                                      'isWFH'=>$isWFH,
                                      'isOnsite' =>$isOnsite,
                                      'leaveDetails'=>null,
                                      'lockedNa'=>1,
                                      'logIN' => $lockedNa->first()->timeIN,
                                      'logOUT'=>$lockedNa->first()->timeOUT,
                                      'lwopDetails'=>null,
                                      'OTattribute' => $data[0]['OTattribute'],
                                      'payday'=>$payday,
                                      'pendingDTRPin'=> $data[0]['pendingDTRPin'],
                                      'pendingDTRPout' => $data[0]['pendingDTRPout'],
                                      'preshift'=>$usePreshift,
                                      'productionDate'=> date('M d, Y', strtotime($payday)),
                                      'shiftEnd'=>$lockedSched[1],
                                      'shiftEnd2'=>$lockedSched[1],
                                      'shiftStart'=> $lockedSched[0],
                                      'shiftStart2'=>  $lockedSched[1],
                                      'UT'=>$lockedNa->first()->UT,
                                      'usercws'=>$usercws,
                                      'userOT'=>$userOT,
                                      'workedHours'=> $lockedNa->first()->hoursWorked,
                                      'hdToday'=>null,
                                      'backOffice' =>null
                                       
                                       
                                       ]);

                               

                              }else
                              {
                                  $myDTR->push([

                                    'allData'=>$data,
                                    'approvedOT' => $data[0]['approvedOT'],
                                    'billableForOT' => $data[0]['billableForOT'],
                                    'biometrics_id'=>$bioForTheDay->id,
                                    'day'=> date('D',strtotime($payday)),
                                    'dtrpIN'=>$data[0]['dtrpIN'],
                                    'dtrpOUT'=>$data[0]['dtrpOUT'],
                                    'dtrpIN_id'=>$data[0]['dtrpIN_id'],
                                    'dtrpOUT_id'=>$data[0]['dtrpOUT_id'],
                                    'hasApprovedCWS'=> $hasApprovedCWS,
                                    'hasApprovedOT'=>$hasApprovedOT,
                                    'hasCWS'=>$hasCWS,
                                    'hasLeave' => null,
                                    'hasLWOP' => null,
                                    'hasVTO'=>null,
                                    'hasOT'=>$hasOT,
                                    'hasPendingIN' => $data[0]['hasPendingIN'],
                                    'hasPendingOUT' => $data[0]['hasPendingOUT'],
                                    'isRDToday'=>$isRDToday, 
                                    'isRD'=>$isRDToday,
                                    'isFixedSched'=>$isFixedSched,
                                    'isFlexitime'=> $isFlexitime,
                                    'isWFH'=>$isWFH,
                                    'isOnsite' =>$isOnsite,
                                    'leaveDetails'=>null,
                                    'lockedNa'=> 0,
                                    'logIN' => $data[0]['logIN'],
                                    'logOUT'=>$data[0]['logOUT'],
                                    'lwopDetails'=>null,
                                    'OTattribute' => $data[0]['OTattribute'],
                                    'payday'=>$payday,
                                    'pendingDTRPin'=> $data[0]['pendingDTRPin'],
                                    'pendingDTRPout' => $data[0]['pendingDTRPout'],
                                    'preshift'=>$usePreshift,
                                    'productionDate'=> date('M d, Y', strtotime($payday)),
                                    'shiftEnd'=>$data[0]['shiftEnd'],
                                    'shiftEnd2'=>$data[0]['shiftEnd'],
                                    'shiftStart'=> $data[0]['shiftStart'],
                                    'shiftStart2'=>  $data[0]['shiftStart'],
                                    'UT'=>$data[0]['UT'],
                                    'usercws'=>$usercws,
                                    'userOT'=>$userOT,
                                    'workedHours'=> $data[0]['workedHours'],
                                    'hdToday'=>null,
                                    'backOffice' =>null
                                     
                                     
                                     ]);

                             

                              }//end RD if locked na

                              
                            }//end if isRDToday


                            //**************************************************************
                            //       WORK DAY
                            //**************************************************************
                            else  
                            {
                                  $problemArea = new Collection;

                                  $s = Carbon::parse($payday." ".$schedForToday['timeStart'],"Asia/Manila");
                                  $s2 = Carbon::parse($payday." ".$schedForToday['timeEnd'],"Asia/Manila");

                                  //}
                                  

                                  /* *******************************************************

                                  here we check if user is PART TIMER (5hr work ) only
                                  12: Part Time 14:Regular Part time

                                  BUT!! we need to verify if employee is EXEMPT/flexi sched
                                  if (flexi ANYTIME) = no lates, just get IN & OUT: dapat 45hrs total in a week
                                  if (flexi 8hr) = get nearest 15min IN and OUT. Dapat naka >= 8hr sya


                                     ******************************************************** */

                                  

                                  $shiftStart = date('h:i A',strtotime($schedForToday['timeStart']));

                                  if ($isParttimer)
                                  {
                                    if (is_null($schedForToday['timeStart']))
                                    {
                                      $shiftStart2 = '<span class="text-danger" style="font-weight:bold">No Work Sched</span>';
                                      $schedForToday = collect([
                                        'timeStart'=>null, 
                                        'timeEnd'=>null,'isFlexitime'=>false,'isRD'=>0]);
                                      $shiftEnd = null;

                                    }else
                                    {

                                        // ----- we now have to check kung may PT-override
                                        $hasPToverride = DB::table('pt_override')->where('user_id',$user->id)->where('overrideStart','<=',$payday)->where('overrideEnd','>=',$payday)->get();

                                        if (count($hasPToverride) > 0)
                                        {
                                          $pt = Carbon::parse($payday." ".$schedForToday['timeStart'],"Asia/Manila")->addHours(9);
                                          $shiftEnd =  date('h:i A',strtotime($pt->format('H:i:s')));
                                          $f = $schedForToday['isFlexitime'];
                                          $schedForToday = collect(['timeStart'=>$s->format('H:i:s'), 'timeEnd'=>$pt->format('H:i:s'),'isFlexitime'=>$f,'isRD'=>0]);

                                        }
                                        else
                                        {
                                          $pt = Carbon::parse($payday." ".$schedForToday['timeStart'],"Asia/Manila")->addHours(4);
                                          $shiftEnd =  date('h:i A',strtotime($pt->format('H:i:s')));
                                          $f = $schedForToday['isFlexitime'];
                                          $schedForToday = collect(['timeStart'=>$s->format('H:i:s'), 'timeEnd'=>$pt->format('H:i:s'),'isFlexitime'=>$f,'isRD'=>0]);


                                        }
                                        
                                        
                                    }
                                   
                                   
                                  }else
                                  {
                                    if ($isExempt)
                                    {
                                      $exemptIN = Logs::where('user_id',$user->id)->where('biometrics_id',$bioForTheDay->id)->where('logType_id',1)->orderBy('id','DESC')->get();

                                      if($exemptEmp[0]->schedType_id == '1') //ANYTIME
                                      {
                                        if(count($exemptIN) > 0) { 
                                          $shiftStart = date('h:i A',strtotime($exemptIN->first()->logTime)); 
                                          $shiftEnd = Carbon::parse($bioForTheDay->productionDate." ".$exemptIN->first()->logTime,'Asia/Manila')->addHour(9)->format('h:i A');
                                        }
                                        else $shiftEnd = date('h:i A',strtotime($schedForToday['timeEnd']));

                                      }
                                      elseif($exemptEmp[0]->schedType_id == '2') //FLEXI 8
                                      {
                                        if(count($exemptIN) > 0) 
                                        { 

                                          $dt = date('h:i A',strtotime($exemptIN->first()->logTime));
                                          $ds = explode(':', $exemptIN->first()->logTime);
                                          if($ds[1] == '00') {

                                            $shiftStart = Carbon::parse($bioForTheDay->productionDate." ".$ds[0].":00".":".$ds[2],'Asia/Manila')->format('h:i A');
                                            $shiftEnd = Carbon::parse($bioForTheDay->productionDate." ".$ds[0].":00".":".$ds[2],'Asia/Manila')->addHour(9)->format('h:i A');
                                          }
                                          elseif($ds[1] > '00' && $ds[1] <= '15'){
                                            $shiftStart = Carbon::parse($bioForTheDay->productionDate." ".$ds[0].":15".":".$ds[2],'Asia/Manila')->format('h:i A');
                                            $shiftEnd = Carbon::parse($bioForTheDay->productionDate." ".$ds[0].":15".":".$ds[2],'Asia/Manila')->addHour(9)->format('h:i A');
                                          }
                                          elseif($ds[1] > 15 && $ds[1] <=30){
                                            $shiftStart = Carbon::parse($bioForTheDay->productionDate." ".$ds[0].":30".":".$ds[2],'Asia/Manila')->format('h:i A');
                                            $shiftEnd = Carbon::parse($bioForTheDay->productionDate." ".$ds[0].":30".":".$ds[2],'Asia/Manila')->addHour(9)->format('h:i A');
                                          }
                                          elseif($ds[1] > 30 && $ds[1] <=45){
                                            $shiftStart = Carbon::parse($bioForTheDay->productionDate." ".$ds[0].":45".":".$ds[2],'Asia/Manila')->format('h:i A');
                                            $shiftEnd = Carbon::parse($bioForTheDay->productionDate." ".$ds[0].":45".":".$ds[2],'Asia/Manila')->addHour(9)->format('h:i A');
                                          }
                                          elseif($ds[1] > 45){
                                            $shiftStart = Carbon::parse($bioForTheDay->productionDate." ".($ds[0]+1).":00:".$ds[2],'Asia/Manila')->format('h:i A');
                                            $shiftEnd = Carbon::parse($bioForTheDay->productionDate." ".($ds[0]+1).":00:".$ds[2],'Asia/Manila')->addHour(9)->format('h:i A');
                                          }
                                        }
                                        else $shiftEnd = date('h:i A',strtotime($schedForToday['timeEnd']));

                                      }
                                      else $shiftEnd = date('h:i A',strtotime($schedForToday['timeEnd']));

                                    }
                                    else $shiftEnd = date('h:i A',strtotime($schedForToday['timeEnd']));
                                  }
                                  
                                  //Morning: #c7b305 bcaa0f
                                  //Eve:#6754c1
                                  $mn = Carbon::parse($payday." 00:00:00", "Asia/Manila");
                                  $noon = Carbon::parse($payday." 11:59:00", "Asia/Manila");

                                  if (is_null($shiftStart)){
                                    $shiftStart2 = '<span class="text-danger" style="font-weight:bold">No Work Sched</span>';

                                  }else
                                  {
                                    if ( $s >= $mn && $s <= $noon ) {
                                      $shiftStart2 = '<span style="color:#bcaa0f; font-weight:bold">'. $shiftStart. '</span>';
                                    } else $shiftStart2 = '<span style="color:#6754c1; font-weight:bold">'. $shiftStart. '</span>';
                                    if  ( $s2 >=$mn && $s2 <= $noon ) {
                                     $shiftEnd2 = '<span style="color:#bcaa0f; font-weight:bold">'. $shiftEnd. '</span>';
                                    } else  $shiftEnd2 = '<span style="color:#6754c1; font-weight:bold">'. $shiftEnd. '</span>';

                                  }

                                  

                                  

                                  if ( ($s->format('Y-m-d H:i:s') >= $mn->format('Y-m-d H:i:s') &&  $s->format('Y-m-d H:i:s') <=  Carbon::parse($payday." 03:00:00","Asia/Manila")->format('Y-m-d H:i:s') ) || $s2->format('H:i:s')=='00:00:00')  
                                  {
                                    $isAproblemShift = true;
                                  } else
                                  {
                                    $isAproblemShift = false;
                                  }
                                  

                                  /*----------------------------
                                    SAME DAY LOGS: shiftstart = (6am-2:59PM)
                                    PROBLEM shifts: 12MN - 5:30am

                                  ------------------------------*/

                                  //if ($shiftStart >= date('h:i A', strtotime("06:00:00")) && $shiftStart <= date('h:i A', strtotime("14:59:00")))
                                  $ss = Carbon::parse($payday." ".$shiftStart,"Asia/Manila");
                                  $sixam = Carbon::parse($payday." 02:00:00","Asia/Manila");
                                  $threepm = Carbon::parse($payday." 14:59:00","Asia/Manila");
                                  if ($ss->format('Y-m-d H:i:s') >= $sixam->format('Y-m-d H:i:s') && $ss->format('Y-m-d H:i:s') <= $threepm->format('Y-m-d H:i:s') )
                                  {
                                    $sameDayLog = true; 

                                  } else{
                                    $sameDayLog = false;

                                  }

                                  if ($sameDayLog)
                                  {
                                    

                                    if($isFixedSched)
                                    {
                                      //if(is_array($allRD)) { $coll->push(['an array'=>true]);
                                      if (count($allRD->first()->where('workday',$prevNumDay)) > 0)
                                        {$isRDYest = true;} //$RDsched->contains($prevNumDay); }
                                      else 
                                        $isRDYest = false;
                                    }
                                          
                                    else
                                    {
                                      if ($hybridSched)
                                      {
                                        if (!is_null($RDsched))
                                          $rd = $RDsched->where('productionDate',$prevDay->format('Y-m-d'))->first();
                                        else
                                          $rd = null;

                                      }else{
                                        if (!is_null($RDsched))
                                              $rd = $RDsched->where('isRD',1)->where('productionDate',$prevDay->format('Y-m-d'))->first();  
                                            else
                                              $rd = null;

                                      }  
                                      if (empty($rd)) 
                                        $isRDYest=false; else $isRDYest=true;
                                    }

                                    $userLogIN = $this->getLogDetails('WORK', $id, $bioForTheDay->id, 1, $schedForToday, $UT,$problemArea,$isAproblemShift,$isRDYest,$schedKahapon,$isBackoffice);
                                    $userLogOUT = $this->getLogDetails('WORK', $id, $bioForTheDay->id, 2, $schedForToday,0,$problemArea,$isAproblemShift,$isRDYest,$schedKahapon,$isBackoffice);



                                    //**** we now process WORKED HOURS *******

                                    if (empty($userLogOUT[0]['timing']))
                                    {
                                      //** but check mo muna kung may filed leave ba OR HOLIDAY|| $userLogOUT[0]['hasVTO']
                                      if($userLogOUT[0]['hasLeave'] || $userLogOUT[0]['hasLWOP'] || $userLogOUT[0]['hasSL'] || $hasHolidayToday)
                                      {
                                        $data = $this->getWorkedHours($user,$userLogIN, $userLogOUT, $schedForToday,$shiftEnd,$payday,$isRDYest,$isParttimer);
                                        $workedHours= $data[0]['workedHours'];//$wh=$data[0]['wh'];
                                        $billableForOT = $data[0]['billableForOT'];
                                        $OTattribute = $data[0]['OTattribute'];
                                        $UT = $data[0]['UT'];
                                        $backOffice= null;
                                        ($hasHolidayToday) ?  $hdToday=$data[0]['hdToday'] : $hdToday=null;
                                        
                                        //$coll->push(['ret workedHours:'=> $data, 'out'=>$userLogOUT]);

                                      }else{
                                            //meaning wala syang OUT talaga
                                          $workedHours= "N/A";
                                          $billableForOT = "-";
                                          $OTattribute = "-";
                                          $UT = "-";
                                          $backOffice=null;
                                          ($hasHolidayToday) ?  $hdToday=$data[0]['hdToday'] : $hdToday=null;
                                      }
                                      

                                    }else{
                                      $data = $this->getWorkedHours($user,$userLogIN, $userLogOUT, $schedForToday,$shiftEnd,$payday,$isRDYest,$isParttimer);
                                      $workedHours= $data[0]['workedHours'];//$wh=$data[0]['wh'];
                                      
                                      $billableForOT = $data[0]['billableForOT'];
                                      $OTattribute = $data[0]['OTattribute'];
                                      $UT = $data[0]['UT'];
                                      $backOffice = $data[0]['isBackoffice'];
                                      $hdToday = $data[0]['hdToday'];

                                      
                                      //$coll->push(['ret workedHours:'=> $data, 'out'=>$userLogOUT]);

                                    } 
                                   
                                

                                  } //--- end sameDayLog
                                  else
                                  {
                                      // we need to setup now cases like Farah
                                      // if !sameDayLog, check muna shiftStart: IF dehadong time, kunin mo yung TIMEIN pang kahapon within shiftStart subHours(5)
                                      //                                        if meron, then ok LOGIN
                                      // if wala, kunin mo login (today within shiftStart & shiftEnd) == LATE SYA
                                      //          IF waley, AWOL
                                      // for the LOGOUT, get log today normally

                                      // if shift is 12MN - 5AM -> PROBLEM AREA
                                      //----------------------------------------
                                      if($isFixedSched)
                                          $isRDYest = $actualSchedKahapon->isRDToday; //true; //$RDsched->contains($prevNumDay); 
                                        else
                                        {
                                          if ($hybridSched)
                                          {
                                            if (!is_null($RDsched))
                                              $rd = $RDsched->where('productionDate',$prevDay->format('Y-m-d'))->first();
                                            else
                                              $rd = null;

                                           

                                          }else{

                                            if (!is_null($RDsched))
                                              $rd = $RDsched->where('isRD',1)->where('productionDate',$prevDay->format('Y-m-d'))->first();  
                                            else
                                              $rd = null;

                                          } 
                                          if (empty($rd)) 
                                            $isRDYest=false; else $isRDYest=true;
                                        }

                                       

                                      /*-------------------------------------------
                                          Problem shifts: 12MN-5am
                                      ---------------------------------------------*/
                                     
                                        $userLogIN = $this->getLogDetails('WORK', $id, $bioForTheDay->id, 1, $schedForToday, $UT, $problemArea,$isAproblemShift,$isRDYest,$schedKahapon,$isBackoffice);
                                        //$coll->push(['datafrom'=>"else NOT Problem shift",'data IN'=>$userLogIN ]);
                                      //}

                                      

                                     //********** LOG OUT ***************

                                            
                                        //if(count((array)$bioForTom) > 0){
                                          $userLogOUT = $this->getLogDetails('WORK', $id, $bioForTheDay->id, 2, $schedForToday,0, $problemArea,$isAproblemShift,$isRDYest,$schedKahapon,$isBackoffice);
                                              //$coll->push(['datafrom'=>"Normal out",'data OUT'=>$userLogOUT ]);

                                          
                                        /*}
                                        else
                                        {
                                          $userLogOUT[0]= array('logTxt'=> "No Data", 
                                                                'UT'=>0,'logs'=>null,'dtrpIN'=>null,'dtrpIN_id'=>null, 'dtrpOUT'=>null,'dtrpOUT_id'=>null, 'hasPendingDTRP'=>null,'pendingDTRP'=>null);

                                        }*/

                                            $data = $this->getWorkedHours($user,$userLogIN, $userLogOUT, $schedForToday,$shiftEnd, $payday,$isRDYest,$isParttimer);

                                        //$coll->push(['bioForTheDay'=>$bioForTheDay->id, 'schedForToday'=> $schedForToday, 'problemArea'=> $problemArea]); 
                                            //$coll->empty();
                                            $coll->push(['payday'=>$payday,'data'=>$data]);

                                        $workedHours= $data[0]['workedHours']; //$wh=$data[0]['wh'];
                                        $billableForOT = $data[0]['billableForOT'];
                                        $OTattribute = $data[0]['OTattribute'];
                                        $UT = $data[0]['UT'];
                                        $VLs = $data[0]['VL'];
                                        $LWOPs = $data[0]['LWOP'];
                                        $backOffice = $data[0]['isBackoffice'];
                                        $hdToday = $data[0]['hdToday'];


                                        //$VTOba = $$data[0]['VTO'];


                                              
                                      

                                  } //--- else not sameDayLog


                                  //check for preshift
                                  $usePreshift = DB::table('user_preshiftOverride')->where('productionDate',$payday)->where('user_id',$user->id)->get();

                                  if(is_null($schedForToday)) {
                                      
                                      $myDTR->push(['isRDToday'=>null,'payday'=>$payday,'biometrics_id'=>$bioForTheDay->id,
                                                    'hasCWS'=>$hasCWS,
                                                    'usercws'=>$usercws,
                                                    'userOT'=>$userOT,
                                                    'hasOT'=>$hasOT,
                                                    'hdToday' =>null,
                                                    'backOffice'=>null,
                                                    'isRD'=>0,
                                                    'isFlexitime'=>$isFlexitime,
                                                    'productionDate'=> date('M d, Y', strtotime($payday)),
                                                    'day'=> date('D',strtotime($payday)),
                                                    'shiftStart'=> null,
                                                    'shiftEnd'=>null,
                                                    'shiftStart2'=> $shiftStart2,
                                                    'shiftEnd2'=>$shiftEnd2,
                                                    'hasPendingIN' => null,
                                                    'pendingDTRPin'=> null,
                                                    'hasPendingOUT' =>null, //$userLogOUT[0]['hasPendingDTRP'],
                                                    'pendingDTRPout' =>null, //$userLogOUT[0]['pendingDTRP'],
                                                    'preshift'=>$usePreshift,
                                                    'hasLeave' => $userLogIN[0]['hasLeave'],
                                                    'leaveDetails'=>$userLogIN[0]['leave'],
                                                    'hasLWOP' => $userLogIN[0]['hasLWOP'],
                                                    'isWFH'=>$isWFH,
                                                    'isOnsite'=>$isOnsite,
                                                    'lwopDetails'=>$userLogIN[0]['lwop'],
                                                    'lockedNa' => 0,
                                                    'logIN' => $userLogIN[0]['logTxt'],
                                                    'logOUT'=>$userLogOUT[0]['logTxt'],
                                                    'dtrpIN'=>$userLogIN[0]['dtrpIN'],
                                                    'dtrpIN_id'=>$userLogIN[0]['dtrpIN_id'],
                                                    'dtrpOUT'=>$userLogOUT[0]['dtrpOUT'],
                                                    'dtrpOUT_id'=>$userLogOUT[0]['dtrpOUT_id'],
                                                    'workedHours'=> $workedHours,
                                                    'billableForOT' => $billableForOT,
                                                    'OTattribute'=>$OTattribute,
                                                    'UT'=>$UT,
                                                    'approvedOT' => $approvedOT]);

                                  } 
                                  else{
                                    $actualSchedKahapon = $this->getActualSchedForToday($user,$id,$prevDay->format('Y-m-d'),$bioForYest, $hybridSched,$isFixedSched,$hybridSched_WS_fixed,$hybridSched_WS_monthly, $hybridSched_RD_fixed, $hybridSched_RD_monthly, $workSched, $RDsched, $approvedCWS);

                                    if(count($userLogIN[0]['leave']) > 0  )$hasLeave=true; else $hasLeave=false;


                                    //*** pang-check kung locked na, dapat ang ipakita mo eh kung ano yung na-lock
                                    $lockedNa = User_DTR::where('productionDate',$payday)->where('user_id',$user->id)->get();
                                    if (count($lockedNa) > 0)
                                    {
                                      $myDTR->push([
                                      'approvedOT' => $approvedOT,

                                      'backOffice' => $backOffice,
                                      'billableForOT' => $lockedNa->first()->OT_billable,
                                      'biometrics_id'=>$bioForTheDay->id,
                                      'day'=> date('D',strtotime($payday)),
                                      'dtrpIN'=>$userLogIN[0]['dtrpIN'],
                                      'dtrpIN_id'=>$userLogIN[0]['dtrpIN_id'],
                                      'dtrpOUT'=> $userLogOUT[0]['dtrpOUT'],
                                      'dtrpOUT_id'=> $userLogOUT[0]['dtrpOUT_id'],
                                      'hasCWS'=>$hasCWS,
                                      'hasLWOP' => $userLogIN[0]['hasLWOP'],
                                      'hasOT'=>$hasOT,
                                      'hasVTO' => $userLogOUT[0]['hasVTO'],
                                      'hasApprovedCWS'=>$hasApprovedCWS,
                                      'hasApprovedOT'=>$hasApprovedOT,
                                      'hasLeave' => $hasLeave, //$userLogIN[0]['hasLeave'],
                                      'hasPendingIN' => $userLogIN[0]['hasPendingDTRP'],
                                      'hasPendingOUT' => $userLogOUT[0]['hasPendingDTRP'],
                                      'hdToday'=>$hdToday,

                                      'isAproblemShift'=>$isAproblemShift,
                                      'isFixedSched'=>$isFixedSched,
                                      'isFlexitime'=>$schedForToday['isFlexitime'],
                                      'isRDToday'=>$isRDToday,  
                                      'isRD'=> 0,
                                      'isWFH'=>$isWFH,
                                      'isOnsite'=>$isOnsite,
                                      'leaveDetails'=>$userLogIN[0]['leave'],
                                      'lockedNa'=> 1,
                                      'logIN' => $lockedNa->first()->timeIN,
                                      'logOUT'=>$lockedNa->first()->timeOUT,
                                      'lwopDetails'=>$userLogIN[0]['lwop'],
                                      'OTattribute'=> $OTattribute,
                                      'outs'=>$userLogOUT[0],
                                      'payday'=> $payday,
                                      'pendingDTRPin'=> $userLogIN[0]['pendingDTRP'],
                                      'pendingDTRPout' =>$userLogOUT[0]['pendingDTRP'],
                                      'preshift'=>$usePreshift,
                                      'productionDate'=> date('M d, Y', strtotime($payday)),
                                      'sameDayLog'=>$sameDayLog,
                                      'schedForToday'=>$schedForToday,
                                      'shiftStart'=> $shiftStart,
                                      'shiftEnd'=>$shiftEnd,
                                      'shiftStart2'=> $shiftStart2,
                                      'shiftEnd2'=>$shiftEnd2,
                                      'usercws'=>$usercws,
                                      'userOT'=>$userOT,
                                      'UT'=>$lockedNa->first()->UT,
                                      'wholeIN' => $userLogIN,
                                      'wholeOUT' =>$userLogOUT,
                                      'workedHours'=> $lockedNa->first()->hoursWorked,
                                      //'wh' => $wh,
                                      //'alldata'=>$data

                                     ]);



                                    }else
                                    {
                                      $myDTR->push([
                                      'approvedOT' => $approvedOT,
                                      'backOffice' => $backOffice,
                                      'billableForOT' => $billableForOT,
                                      'biometrics_id'=>$bioForTheDay->id,
                                      'day'=> date('D',strtotime($payday)),
                                      'dtrpIN'=>$userLogIN[0]['dtrpIN'],
                                      'dtrpIN_id'=>$userLogIN[0]['dtrpIN_id'],
                                      'dtrpOUT'=> $userLogOUT[0]['dtrpOUT'],
                                      'dtrpOUT_id'=> $userLogOUT[0]['dtrpOUT_id'],
                                      'hasCWS'=>$hasCWS,
                                      'hasLWOP' => $userLogIN[0]['hasLWOP'],
                                      'hasOT'=>$hasOT,
                                      'hasVTO' => $userLogOUT[0]['hasVTO'],
                                      'hasApprovedCWS'=>$hasApprovedCWS,
                                      'hasApprovedOT'=>$hasApprovedOT,
                                      'hasLeave' => $hasLeave, //$userLogIN[0]['hasLeave'],
                                      'hasPendingIN' => $userLogIN[0]['hasPendingDTRP'],
                                      'hasPendingOUT' => $userLogOUT[0]['hasPendingDTRP'],
                                      'hdToday'=>$hdToday,

                                      'isAproblemShift'=>$isAproblemShift,
                                      'isFixedSched'=>$isFixedSched,
                                      'isFlexitime'=>$schedForToday['isFlexitime'],
                                      'isRDToday'=>$isRDToday,  
                                      'isRD'=> 0,
                                      'isWFH'=>$isWFH,
                                      'isOnsite'=>$isOnsite,
                                      'leaveDetails'=>$userLogIN[0]['leave'],
                                      'lockedNa' => 0,
                                      'logIN' => $userLogIN[0]['logTxt'],
                                      'logOUT'=>$userLogOUT[0]['logTxt'],
                                      'lwopDetails'=>$userLogIN[0]['lwop'],
                                      'OTattribute'=> $OTattribute,
                                      'outs'=>$userLogOUT[0],
                                      'payday'=> $payday,
                                      'pendingDTRPin'=> $userLogIN[0]['pendingDTRP'],
                                      'pendingDTRPout' =>$userLogOUT[0]['pendingDTRP'],
                                      'preshift'=>$usePreshift,
                                      'productionDate'=> date('M d, Y', strtotime($payday)),
                                      'sameDayLog'=>$sameDayLog,
                                      'schedForToday'=>$schedForToday,
                                      'shiftStart'=> $shiftStart,
                                      'shiftEnd'=>$shiftEnd,
                                      'shiftStart2'=> $shiftStart2,
                                      'shiftEnd2'=>$shiftEnd2,
                                      'usercws'=>$usercws,
                                      'userOT'=>$userOT,
                                      'UT'=>$UT, //$userLogOUT[0]['UT'],
                                      'wholeIN' => $userLogIN,
                                      'wholeOUT' =>$userLogOUT,
                                      'workedHours'=> $workedHours,
                                      //'wh' => $wh,
                                      //'alldata'=>$data

                                     ]);



                                    }//end if locked na

                                    
                                  } 


                            }//end else WORK DAY
                            

                      }//end else not null BioForTheDay

                       

                }//end if else noWorkSched

                endNoWorkSched: 
                //$noWorkSched = null; //*** we need to reset things

                //$wsch->push(['noWorkSched'=>$noWorkSched]);

                        
               


             }//END foreach payrollPeriod

            //return $myDTR;

            if(Input::get('debug'))
            {
              $d = Carbon::parse(Input::get('debug'),'Asia/Manila')->format('M d, Y');
              //return $d;
              return $myDTR->where('productionDate',$d);
            }
            


            $correct = Carbon::now('GMT+8'); //->timezoneName();

         

           /*if($this->user->id !== 564 ) {
              $file = fopen('storage/uploads/log.txt', 'a') or die("Unable to open logs");
                fwrite($file, "-------------------\n Viewed DTR of: ".$user->lastname."[".$user->id."] --" . $correct->format('M d h:i A'). " by [". $this->user->id."] ".$this->user->lastname."\n");
                fclose($file);
            } */


            /*----------- check for available MEMOS --------------*/
                $activeMemo = Memo::where('active',1)->orderBy('created_at','DESC')->get();
                if (count($activeMemo)>0){
                  $memo = $activeMemo->first();

                  //check if nakita na ni user yung memo
                  $seenMemo = User_Memo::where('user_id',$this->user->id)->where('memo_id',$memo->id)->get();
                  if (count($seenMemo)>0)
                    $notedMemo = true;
                  else $notedMemo = false;

                }else { $notedMemo=false; $memo=null; } 

                $notedMemo=true; $memo=null; //override for tour

           $ecq = ECQ_Workstatus::where('user_id',$user->id)->get();
           $wfhData = Logs::where('user_id',$user->id)->where('manual',1)->get();//->where('biometrics_id','>=',$startWFH->id)
                
           //return response()->json(['currentVLbalance'=>$currentVLbalance,'currentSLbalance'=>$currentSLbalance]);

           
           return view('timekeeping.myDTR', compact('id', 'ecq','allECQ', 'wfhData', 'fromYr', 'entitledForLeaves',  'TLapprover', 'DTRapprovers', 'canChangeSched', 'paycutoffs', 'shifts','shift4x11', 'partTimes','cutoffID','verifiedDTR', 'myDTR','camps','user','theImmediateHead', 'immediateHead','cutoff','noWorkSched', 'prevTo','prevFrom','nextTo','nextFrom','memo','notedMemo','payrollPeriod','currentVLbalance','currentSLbalance','isFinance', 'canPreshift', 'vlEarnings','slEarnings','isParttimer', 'canVL','canSL','isNDY','cp0','cp1','isExempt','exemptEmp','paystart','payend','anApprover','hasAccess','isWorkforce','isBackoffice','leader_lv1','leader_lv2','leader_lv3','canViewTeamDTR','canViewOtherDTR'));


        } else return view('access-denied');

    }

    public function unlock($id, Request $request)
    {
      $user = User::find($id);
      $payrollPeriod = $request->payrollPeriod;
      $coll = new Collection;


      $theDTR = User_DTR::where('user_id',$user->id)->where('productionDate',$payrollPeriod[0])->get();
      if (count($theDTR)>0)
      {
        //**** send notification to the sender
        (count($payrollPeriod) > 1) ? $unlockType = 14 : $unlockType = 19;

        $theNotif = Notification::where('relatedModelID', $theDTR->first()->id)->where('type',$unlockType)->get();

        //return $theNotif;

        //then remove those sent notifs to the approvers since it has already been approved/denied
        if (count($theNotif) > 0 )//&& ($this->user->id !== $user->id)
        {

            DB::table('user_Notification')->where('notification_id','=',$theNotif->first()->id)->delete();

            
              $unotif = $this->notifySender($theDTR->first(),$theNotif->first(),$unlockType);
              /*------ now that we're about to delete the User_DTR, no more way of referencing to it for the NOTIFICATIOn,
               only by modifying the relatedModelID into biometrics ID na --------*/

               $updateNotif = $theNotif->first();

               $biometrics = Biometrics::where('productionDate',$payrollPeriod[0])->get();

               if(count($biometrics)>0){
                $updateNotif->relatedModelID = $biometrics->first()->id;
                $updateNotif->push();
               }

            

            
        }
        else{

            //else no notif sent because it's initiated by TL himself
             }

        
        
               

      }

      foreach ($payrollPeriod as $key) {

         $dtr = User_DTR::where('user_id',$user->id)->where('productionDate',$key)->delete();
         
         //if (count($dtr)>0){ $dtr->delete();} 
      }

      



      return response()->json(['success'=>'1', 'message'=>"DTR Unlocked."]);

     



    }

    public function unlockByApprover($id, Request $request)
    {
      $user = User::find($id);
      $payrollPeriod = $request->payrollPeriod;
      foreach ($payrollPeriod as $key) {

        $existingUnlock = User_Unlocks::where('user_id',$user->id)->where('productionDate',$key)->get();

          if(count($existingUnlock) <= 0)
          {
            $unl = new User_Unlocks;
            $unl->user_id = $user->id;
            $unl->productionDate = $key;
            $unl->created_at = Carbon::now('GMT+8')->format('Y-m-d H:i:s');
            $unl->save();

          }

         $dtr = User_DTR::where('user_id',$user->id)->where('productionDate',$key)->delete();
         
         //if (count($dtr)>0){ $dtr->delete();} 
      }

      return response()->json(['success'=>'1', 'message'=>"DTR is now unlocked."]);

    }

    public function updateECQ(Request $request)
    {

      $start = $request->pstart;
      $end = $request->pend;
      $ecq = $request->ecq;

      if ($start == $end)
      {
        $d = Carbon::parse($start,'Asia/Manila')->format('Y-m-d');
        $bio = Biometrics::where('productionDate',$d)->get();

        if (count($bio) > 0)
          $biometrics = $bio->first();
        else 
        {
          $biometrics = new Biometrics;
          $biometrics->productionDate = $d;
          $biometrics->save();

        }

        $ecqStat = new ECQ_Workstatus;
        $ecqStat->user_id = $request->user_id;
        $ecqStat->biometrics_id = $biometrics->id;
        $ecqStat->workStatus = $ecq;
        $ecqStat->save();

        return response()->json(['start'=>$start, 'end'=>$end, 'ecq'=>$ecq, 'success'=>1, 'message'=>'ECQ status saved successfully.']);
          
      }
      else
      {
        $s = Carbon::parse($start,'Asia/Manila');
        $e = Carbon::parse($end,'Asia/Manila');

        if ($e->format('Y-m-d') > $s->format('Y-m-d'))
        {
          while($s->format('Y-m-d') <= $e->format('Y-m-d'))
          {
             $bio = Biometrics::where('productionDate',$s->format('Y-m-d'))->get();
             if (count($bio) > 0)
                $biometrics = $bio->first();
             else 
             {
                $biometrics = new Biometrics;
                $biometrics->productionDate = $s->format('Y-m-d');
                $biometrics->save();

             }

             $ecqStat = new ECQ_Workstatus;
             $ecqStat->user_id = $request->user_id;
             $ecqStat->biometrics_id = $biometrics->id;
             $ecqStat->workStatus = $ecq;
             $ecqStat->save();

             $s->addDay();
          }

          return response()->json(['start'=>$start, 'end'=>$end, 'ecq'=>$ecq, 'success'=>1, 'message'=>'ECQ status saved successfully.']);
          

        }else //error on end date
        {
          return response()->json(['start'=>$start, 'end'=>$end, 'ecq'=>$ecq, 'success'=>0, 'message'=>'Invalid end date. Please try again.']);

        }

      }

      
    }


    public function usePreshift($id, Request $request)
    {
      $user = User::find($id);
      $payrollPeriod = $request->payrollPeriod;
      $correct = Carbon::now('GMT+8');

     

      /*----------------------------
      This is where you check if lagpas na ng sahod
      If lagpas na ng sahod, only Finance can unlock. Else, approvers may still do

      if ($payrollPeriod[0]) == 21 -> sahod is nextMonth 10th ==> cutoff
      if ($payrollPeriod[0]) == 06 -> sahod is this month 25th ==> cutoff

      if (date_today > $cutoff) -> only Finance can unlock, send notif to Finance admin only
      else send notif to all approvers
      ------------------------------*/

      $dtr = DB::table('user_preshiftOverride')->where('productionDate',$payrollPeriod[0])->where('user_id',$user->id)->get();

      if(count($dtr)<= 0)
      {
        DB::table('user_preshiftOverride')->insertGetId([
          'user_id'=> $user->id,
          'productionDate'=>$payrollPeriod[0] ]);

        if($this->user->id !== 564 ) {
              $file = fopen('storage/uploads/log.txt', 'a') or die("Unable to open logs");
                fwrite($file, "-------------------\n Preshift: ".$payrollPeriod[0]."[".$user->id."] --" . $correct->format('M d h:i A'). " by [". $this->user->id."] ".$this->user->lastname."\n");
                fclose($file);
            }

        return response()->json(['success'=>'0', 'message'=>"Pre-shift logs and OT enabled for this production date."]);
      } else{
        return response()->json(['success'=>'1', 'message'=>"Pre-shift logs already enabled", 'count'=>count($payrollPeriod)]);


      }


    }

    public function disablePreshift($id, Request $request)
    {
      $user = User::find($id);
      $payrollPeriod = $request->payrollPeriod;
      $correct = Carbon::now('GMT+8');

     

      /*----------------------------
      This is where you check if lagpas na ng sahod
      If lagpas na ng sahod, only Finance can unlock. Else, approvers may still do

      if ($payrollPeriod[0]) == 21 -> sahod is nextMonth 10th ==> cutoff
      if ($payrollPeriod[0]) == 06 -> sahod is this month 25th ==> cutoff

      if (date_today > $cutoff) -> only Finance can unlock, send notif to Finance admin only
      else send notif to all approvers
      ------------------------------*/

      $dtr = DB::table('user_preshiftOverride')->where('productionDate',$payrollPeriod[0])->delete();
     // if($this->user->id !== 564 ) {
              $file = fopen('storage/uploads/log.txt', 'a') or die("Unable to open logs");
                fwrite($file, "-------------------\n Revoke Preshift: ".$payrollPeriod[0]."[".$user->id."] --" . $correct->format('M d h:i A'). " by [". $this->user->id."] ".$this->user->lastname."\n");
                fclose($file);
      //      }
      return response()->json(['success'=>'1', 'message'=>"Pre-shift logs disabled for this production date.", 'count'=>count($payrollPeriod)]);


      


    }


    public function wfm_DTRsummary()
    {
      DB::connection()->disableQueryLog();

      $financeDept = Campaign::where('name',"Finance")->first();
      $finance = Team::where('user_id',$this->user->id)->where('campaign_id',$financeDept->id)->get();
      (count($finance) > 0) ? $isFinance = 1 : $isFinance=0;

      $dataMgt = Team::where('user_id',$this->user->id)->where('campaign_id',19)->get();
      (count($dataMgt) > 0) ? $isDataMgt = 1 : $isDataMgt=0;

      $wfm = Team::where('user_id',$this->user->id)->where('campaign_id',50)->get();
      (count($wfm) > 0) ? $isWFM = 1 : $isWFM=0;

      $hr = Team::where('user_id',$this->user->id)->where('campaign_id',10)->get();
      (count($hr) > 0) ? $isHR = 1 : $isHR=0;

      $correct = Carbon::now('GMT+8'); //->timezoneName();

      if(!$isFinance && !$isHR && !$isWFM && !$isDataMgt) {
          $file = fopen('storage/uploads/dtrplogs.txt', 'a') or die("Unable to open logs");
                    fwrite($file, "-------------------\n ATTEMPTING WFM_dtrSummary on " . $correct->format('M d h:i A'). " by [". $this->user->id."] ".$this->user->lastname."\n");
                    fclose($file);
                 
          return view('access-denied');
      }
      else
      {
          $templates = collect([
                                ['id'=>1,'name'=>'Overtime'],
                                ['id'=>2,'name'=>'Leaves'],
                                ['id'=>3,'name'=>'Change Work Schedules'],
                                //['id'=>4,'name'=>'Work Schedules'],
                                //['id'=>5,'name'=>'Ops Worked Holiday(s)'],

          ]);
          


          $cutoffData = $this->getCutoffStartEnd();
          $cutoffStart = $cutoffData['cutoffStart'];//->cutoffStart;
          $cutoffEnd = $cutoffData['cutoffEnd'];

           //Timekeeping Trait
          $payrollPeriod = $this->getPayrollPeriod($cutoffStart,$cutoffEnd);
          $paycutoffs = Paycutoff::orderBy('toDate','DESC')->get();

          
          $allUsers = DB::table('users')->where([
                        ['status_id', '!=', 6],
                        ['status_id', '!=', 7],
                        ['status_id', '!=', 8],
                        ['status_id', '!=', 9],
                        ['users.status_id', '!=', 13],
                        ['users.status_id', '!=', 16],
                    ])->
            leftJoin('team','team.user_id','=','users.id')->
            leftJoin('campaign','team.campaign_id','=','campaign.id')->
            leftJoin('immediateHead_Campaigns','team.immediateHead_Campaigns_id','=','immediateHead_Campaigns.id')->
            leftJoin('immediateHead','immediateHead_Campaigns.immediateHead_id','=','immediateHead.id')->
            leftJoin('positions','users.position_id','=','positions.id')->
            leftJoin('floor','team.floor_id','=','floor.id')->
            select('users.id', 'users.firstname','users.lastname','users.nickname','users.dateHired','positions.name as jobTitle','campaign.id as campID', 'campaign.name as program','immediateHead_Campaigns.id as tlID', 'immediateHead.firstname as leaderFname','immediateHead.lastname as leaderLname','users.employeeNumber','floor.name as location')->orderBy('users.lastname')->get();

           
            

               if($this->user->id !== 564 ) {
                  $file = fopen('public/build/rewards.txt', 'a') or die("Unable to open logs");
                    fwrite($file, "-------------------\n Viewed WFM_dtrSummary on " . $correct->format('M d h:i A'). " by [". $this->user->id."] ".$this->user->lastname."\n");
                    fclose($file);
                } 
            
          

          return view('timekeeping.wfm_DTRsummary',compact('payrollPeriod','paycutoffs','templates'));

      }



      

    }


}
