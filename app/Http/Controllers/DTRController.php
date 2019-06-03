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
use OAMPI_Eval\User_LWOP;
use OAMPI_Eval\User_OBT;
use OAMPI_Eval\User_Familyleave;
use OAMPI_Eval\Holiday;
use OAMPI_Eval\HolidayType;
use OAMPI_Eval\Memo;
use OAMPI_Eval\User_Memo;




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

        $allProgram = DB::table('campaign')->select('id','name')->orderBy('name')->get();//
        $byTL = collect($allUsers)->groupBy('tlID');
        $allTL = $byTL->keys();
        //return collect($allUsers)->where('campID',7);

        $correct = Carbon::now('GMT+8'); //->timezoneName();

           if($this->user->id !== 564 ) {
              $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
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

      $program = Campaign::find($request->program);

      DB::connection()->disableQueryLog();
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

                      //join('user_dtr','user_dtr.user_id','=','users.id')->
                      select('users.id', 'users.firstname','users.middlename', 'users.lastname','users.nickname','positions.name as jobTitle','campaign.id as campID', 'campaign.name as program','immediateHead_Campaigns.id as tlID', 'immediateHead.firstname as leaderFname','immediateHead.lastname as leaderLname','floor.name as location','user_dtr.productionDate','user_dtr.workshift','user_dtr.isCWS_id', 'user_dtr.timeIN','user_dtr.timeOUT','user_dtr.isDTRP_in','user_dtr.isDTRP_out', 'user_dtr.hoursWorked','user_dtr.leaveType','user_dtr.leave_id', 'user_dtr.OT_billable','user_dtr.OT_approved','user_dtr.OT_id','user_dtr.UT', 'user_dtr.user_id','user_dtr.biometrics_id','user_dtr.updated_at')->
                      where([
                          ['users.status_id', '!=', 7],
                          ['users.status_id', '!=', 8],
                          ['users.status_id', '!=', 9],
                      ])->orderBy('users.lastname')->get();
      $allDTR = collect($allDTRs)->groupBy('id');
      $allUsers = DB::table('campaign')->where('campaign.id',$request->program)->
                      join('team','team.campaign_id','=','campaign.id')->
                      join('users','team.user_id','=','users.id')->
                      leftJoin('immediateHead_Campaigns','team.immediateHead_Campaigns_id','=','immediateHead_Campaigns.id')->
                      leftJoin('immediateHead','immediateHead_Campaigns.immediateHead_id','=','immediateHead.id')->
                      leftJoin('positions','users.position_id','=','positions.id')->
                      leftJoin('floor','team.floor_id','=','floor.id')->
                      
                      select('users.id', 'users.firstname','users.middlename', 'users.lastname','users.nickname','users.dateHired','positions.name as jobTitle','campaign.id as campID', 'campaign.name as program','immediateHead_Campaigns.id as tlID', 'immediateHead.firstname as leaderFname','immediateHead.lastname as leaderLname','users.employeeNumber','floor.name as location')->
                      where([
                          ['users.status_id', '!=', 7],
                          ['users.status_id', '!=', 8],
                          ['users.status_id', '!=', 9],
                      ])->orderBy('users.lastname')->get();

      //return response()->json(['ok'=>true, 'dtr'=>$allDTRs]);
      
      $headers = ['Employee Name', 'Immediate Head','Production Date', 'Current Schedule','CWS | Reason', 'Time IN', 'Time OUT', 'DTRP IN', 'DTRP OUT','OT Start','OT End', 'OT hours','OT Reason','Leave','Reason','Verified'];
      $description = "DTR sheet for cutoff period: ".$cutoffStart->format('M d')." to ".$cutoffEnd->format('M d');


      $correct = Carbon::now('GMT+8'); //->timezoneName();

           

      //return $allDTR;
      if ($request->dltype == '1') // DTR sheets
      {

        if($this->user->id !== 564 ) {
              $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
                fwrite($file, "-------------------\n DL DTR cutoff: -- ".$cutoffStart->format('M d')." on " . $correct->format('M d h:i A'). " for Program: ".$program->name. " by [". $this->user->id."] ".$this->user->lastname."\n");
                fclose($file);
        } 

        $dtr = $request->dtr;
        $cutoff = explode('_', $request->cutoff);
        $cutoffStart = Carbon::parse($request->cutoffstart,'Asia/Manila');

        Excel::create($program->name."_".$cutoffStart->format('M-d'),function($excel) use($program, $allDTR, $cutoffStart, $cutoffEnd, $headers,$description) 
               {
                      $excel->setTitle($cutoffStart->format('Y-m-d').' to '. $cutoffEnd->format('Y-m-d').'_'.$program->name.' DTR Sheet');

                      // Chain the setters
                      $excel->setCreator('Programming Team')
                            ->setCompany('OpenAccess');

                      // Call them separately
                      $excel->setDescription($description);
                      $payday = $cutoffStart;

                      do
                      {

                        $excel->sheet($payday->format('M d')."_".substr($payday->format('l'), 0,3), function($sheet) use ($program, $allDTR, $cutoffStart, $cutoffEnd, $headers,$payday)
                        {

                          $header1 = ['Open Access BPO | Daily Time Record','','','','','','','','','','','','','','',''];
                          $header2 = [$cutoffStart->format('l, M d Y'),'Program/Department: ',strtoupper($program->name),'','','','','','','','','','','','',''];

                          
                          // Set width for a single column
                          //$sheet->setWidth('A', 35);

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
                              $cells->setFontSize(15);
                              $cells->setFontWeight('bold');

                          });

                         
                          
                          $sheet->appendRow($headers);

                          $sheet->row(3, function($row) {
                              // Set font size
                              $row->setFontSize(15);
                              $row->setFontWeight('bold');

                            });
                          // Set height for a single row
                          $sheet->setHeight(3, 50);

                          // Freeze the first column
                          $sheet->freezeFirstColumn();

                          

                          $arr = [];


                          foreach($allDTR as $employeeDTR)
                          {
                            $i = 0;
                            $dData = collect($employeeDTR)->where('productionDate',$payday->format('Y-m-d'));

                            if (count($dData) > 0)
                            {
                              $arr[$i] = $dData->first()->lastname.", ".$dData->first()->firstname." ".$dData->first()->middlename; $i++;
                              $arr[$i] = $dData->first()->leaderFname." ".$dData->first()->leaderLname; $i++;

                              // ** Production Date
                              // check if there's holiday
                              $holiday = Holiday::where('holidate',$payday->format('Y-m-d'))->get();

                              (count($holiday) > 0) ? $hday=$holiday->first()->name : $hday = "";

                              $arr[$i] = $payday->format('M d l')." ". $hday; $i++;
                              $arr[$i] = strip_tags($dData->first()->workshift); $i++; // ** get the sched here
                              
                              //*** CWS
                              if (!empty($dData->first()->isCWS_id)){
                                $deets = User_CWS::find($dData->first()->isCWS_id);

                                $arr[$i] = ' (old sched: '.$deets->timeStart_old. ' - '.$deets->timeEnd_old.' ) | '.$deets->notes; $i++;

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
                                $deets = User_OT::find($dData->first()->OT_id);

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
                                  $arr[$i] =$dData->first()->leaveType; $i++;
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
                              $arr[$i] = $employeeDTR->first()->lastname.", ".$employeeDTR->first()->firstname." ".$employeeDTR->first()->middlename ; $i++;
                              $arr[$i] = $employeeDTR->first()->leaderFname." ".$employeeDTR->first()->leaderLname; $i++;
                              $arr[$i] = $payday->format('M d l'); $i++;
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
                            

                            

                          }//end foreach employee


                          
                        });//end sheet1

                        $payday->addDay();

                      } while ( $payday->format('Y-m-d') <= $cutoffEnd->format('Y-m-d') );      



              })->export('xls');return "Download";

      }else
      {

        if($this->user->id !== 564 ) {
              $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
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
                              $cells->setFontSize(15);
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
                                $cells->setFontSize(12);
                                $cells->setFontWeight('bold');
                                $cells->setAlignment('center');
                              });
                              $sheet->row(4, function($cells) {
                                $cells->setFontSize(14);
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


                      $excel->sheet("OT Summary", function($sheet) use ($program, $allDTR, $cutoffStart, $cutoffEnd, $headers,$payday)
                        {
                          $header1 = ['Open Access BPO | DTR Summary','','','','','','','','','','','','','','',''];
                          $header2 = [$cutoffStart->format('M d Y')." to ". $cutoffEnd->format('M d Y') ,'Program/Department: ',strtoupper($program->name),'','','','','','','','','','','','',''];

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
                              $cells->setFontSize(15);
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
                                $cells->setFontSize(12);
                                $cells->setFontWeight('bold');
                                $cells->setAlignment('center');
                              });
                              $sheet->row(4, function($cells) {
                                $cells->setFontSize(14);
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

              })->export('xls');return "Download";

      } //end else return Billables  

      



             

      // return response()->json(['ok'=>true, 'dtr'=>$allDTR]);
      // return view ('under-construction');

    }

    public function getValidatedDTRs(Request $request)
    {
      $cutoff = explode('_', $request->cutoff);

      $program = Campaign::find($request->program);

      DB::connection()->disableQueryLog();
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

                      //join('user_dtr','user_dtr.user_id','=','users.id')->
                      select('users.id', 'users.firstname','users.lastname','users.middlename', 'users.nickname','positions.name as jobTitle','campaign.id as campID', 'campaign.name as program','immediateHead_Campaigns.id as tlID', 'immediateHead.firstname as leaderFname','immediateHead.lastname as leaderLname','floor.name as location','user_dtr.productionDate','user_dtr.timeIN', 'user_dtr.user_id')->
                      where([
                          ['users.status_id', '!=', 7],
                          ['users.status_id', '!=', 8],
                          ['users.status_id', '!=', 9],
                      ])->orderBy('users.lastname')->get();
      $allUsers = DB::table('campaign')->where('campaign.id',$request->program)->
                      join('team','team.campaign_id','=','campaign.id')->
                      join('users','team.user_id','=','users.id')->
                      leftJoin('immediateHead_Campaigns','team.immediateHead_Campaigns_id','=','immediateHead_Campaigns.id')->
                      leftJoin('immediateHead','immediateHead_Campaigns.immediateHead_id','=','immediateHead.id')->
                      leftJoin('positions','users.position_id','=','positions.id')->
                      leftJoin('floor','team.floor_id','=','floor.id')->
                      
                      select('users.id', 'users.firstname','users.lastname','users.middlename', 'users.nickname','users.dateHired','positions.name as jobTitle','campaign.id as campID', 'campaign.name as program','immediateHead_Campaigns.id as tlID', 'immediateHead.firstname as leaderFname','immediateHead.lastname as leaderLname','users.employeeNumber','floor.name as location')->
                      where([
                          ['users.status_id', '!=', 7],
                          ['users.status_id', '!=', 8],
                          ['users.status_id', '!=', 9],
                      ])->orderBy('users.lastname')->get();
                      //return $allDTRs;

      $userArray = collect($allUsers)->pluck('id')->toArray();
      $dtrArray = collect($allDTRs)->pluck('id')->toArray();
      $pendings = array_diff($userArray, $dtrArray);

      //Timekeeping Trait
      $payrollPeriod = $this->getPayrollPeriod(Carbon::parse($cutoff[0],'Asia/Manila'),Carbon::parse($cutoff[1],'Asia/Manila'));
      //return $dtrArray;

      return response()->json(['payrollPeriod'=>$payrollPeriod, 'pendings'=>$pendings, 'userArray'=>$userArray, 'dtrArray'=>$dtrArray, 'users'=>$allUsers,'program'=>$program->name, 'total'=>count($allUsers),'cutoffstart'=>$cutoff[0], 'cutoffend'=>$cutoff[1], 'DTRs'=>$allDTRs,'submitted'=>count(collect($allDTRs)->groupBy('id'))]);

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

      $dtr = User_DTR::where('user_id',$user->id)->where('productionDate',$payrollPeriod[0])->get();

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

                    
                //          $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
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
          if (count($theDTR)>0) $fromDate = Carbon::parse($theDTR->productionDate,"Asia/Manila");
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
          $fromDate = Carbon::parse(Biometrics::find($theNotif->relatedModelID)->productionDate,"Asia/Manila");
          $toDate = Carbon::parse(Biometrics::find($theNotif->relatedModelID)->productionDate,"Asia/Manila");

        }
        
        else{
          //now redirect it to the DTR sheet
          $theDTR = User_DTR::find(Notification::find($seen->first()->notification_id)->relatedModelID);
          if (count($theDTR)>0){
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

        /*--check floor for Beta testing --*/
        //$saanLocated = DB::table('team')->where('user_id','=',$user->id)->get();

        //if($saanLocated[0]->floor_id != 1 && $saanLocated[0]->floor_id != 2  )
        // 37 = BD
        // 12 = Lebua
        // 16 = Marketing
        // 10 = Finance
        // 31 = SheerID
        // 32 = Circles
        // 47 = Advance Wellness
        // 42 - Bird
        // 48 - another
        // 26 = WV

        // if ($saanLocated[0]->campaign_id != 10 && 
        //     $saanLocated[0]->campaign_id != 12 && 
        //     $saanLocated[0]->campaign_id != 16 && 
        //     $saanLocated[0]->campaign_id != 26 &&
        //     $saanLocated[0]->campaign_id != 37 && 
        //     $saanLocated[0]->campaign_id != 31 && 
        //     $saanLocated[0]->campaign_id != 32 && 
        //     $saanLocated[0]->campaign_id != 42 && 
        //     $saanLocated[0]->campaign_id != 47 && 
        //     $saanLocated[0]->campaign_id != 48 
        //      )
        //     {
        //             $message = '<br/><br/><h1><i class="fa fa-file-code-o fa-2x"></i></h1>';
        //             $message .='<h3>DTR Module Under Construction </h3>';
        //             $message .='<p>Viewing of DTR sheet is currently available for all 5F employees as test groups only: <br/>
        //              <strong>Advance Wellness <br/>
        //              AnOther <br/>
        //              Bird <br/>
        //              Circles.Life <br/>
        //              Business Dev <br/>
        //              Finance <br />
        //              Marketing <br/> 
        //              Lebua <br/>
        //              SheerID </strong>. <br/><br/><br/> <em>Workforce and Programming Team is still working on streamlining DTR processes for the rest of our office floors. <br/>We will let you know once we are done with beta testing.</em><br/><br/> Thank you.</p>';

        //             $correct = Carbon::now('GMT+8'); //->timezoneName();

        //              if($this->user->id !== 564 ) {
        //                 $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
        //                   fwrite($file, "-------------------\n Tried to View DTR of: ".$user->lastname."[".$user->id."] --" . $correct->format('M d h:i A'). " by [". $this->user->id."] ".$this->user->lastname."\n");
        //                   fclose($file);
        //               }  

        //             return view('empty-page',['message'=>$message, 'title'=>"DTR Under Construction"]);
        //     }
        

        $collect = new Collection; 
        $coll = new Collection;
        $coll2 = new Collection;
        $roles = UserType::find($this->user->userType_id)->roles->pluck('label'); //->where('label','MOVE_EMPLOYEES');
        $canViewOtherDTR =  ($roles->contains('VIEW_OTHER_DTR')) ? '1':'0';
        $canViewTeamDTR =  ($roles->contains('VIEW_SUBORDINATE_DTR')) ? '1':'0';
        $canChangeSched =  ($roles->contains('CHANGE_EMPLOYEE_SCHEDULE')) ? '1':'0';

        /* -------- get this user's department. If Backoffice, WFM can't access this ------*/
        $isBackoffice = ( Campaign::find(Team::where('user_id',$user->id)->first()->campaign_id)->isBackoffice ) ? true : false;
        $isWorkforce =  ($roles->contains('STAFFING_MANAGEMENT')) ? '1':'0';

        $hrDept = Campaign::where('name',"HR")->first();
        $financeDept = Campaign::where('name',"Finance")->first();
        $paycutoffs = Paycutoff::orderBy('id','DESC')->get();// return $paycutoffs;

        

        /*------- check first if user is entitled for a leave (Regualr employee or lengthOfService > 6mos) *********/
        $today=Carbon::today();
        $lengthOfService = Carbon::parse($user->dateHired,"Asia/Manila")->diffInMonths($today);
        ($lengthOfService >= 6) ? $entitledForLeaves=true : $entitledForLeaves=false;


        $leadershipcheck = ImmediateHead::where('employeeNumber', $user->employeeNumber)->first();
        $immediateHead = ImmediateHead::find(ImmediateHead_Campaign::find($user->supervisor->immediateHead_Campaigns_id)->immediateHead_id);
        if($immediateHead->employeeNumber == $this->user->employeeNumber ) $theImmediateHead = true; else $theImmediateHead=false;

        if (!empty($leadershipcheck)){ $camps = $leadershipcheck->campaigns->sortBy('name'); } else $camps = $user->campaign;



       // check if viewing is not an agent, an HR personnel, or the owner, or youre the immediateHead $this->user->campaign_id == $hrDept->id 
        
        //--- also find the head of that immediate head for Program Mgr access
        $leader_L2 = User::where('employeeNumber',$immediateHead->employeeNumber)->first();
        $leader_L1 = ImmediateHead::find(ImmediateHead_Campaign::find($leader_L2->supervisor->immediateHead_Campaigns_id)->immediateHead_id);
        $leader_L0 = ImmediateHead::find(ImmediateHead_Campaign::find(User::where('employeeNumber',$leader_L1->employeeNumber)->first()->supervisor->immediateHead_Campaigns_id)->immediateHead_id);
        $leader_PM = ImmediateHead::find(ImmediateHead_Campaign::find(User::where('employeeNumber',$leader_L0->employeeNumber)->first()->supervisor->immediateHead_Campaigns_id)->immediateHead_id);

        // $coll = new Collection;

        $DTRapprovers = '<strong>';
        $ctr = 0;

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
        $employeeisBackoffice = ( Campaign::find(Team::where('user_id',$user->id)->first()->campaign_id)->isBackoffice ) ? true : false;

        
       

        // check if viewing is not an agent, an HR personnel, or the owner, or youre the immediateHead, our you're Program Manager



        //Timekeeping Trait
        $anApprover = $this->checkIfAnApprover($approvers, $this->user);
        $fromYr = Carbon::parse($user->dateHired)->addMonths(6)->format('Y');
        
        if ( ($isWorkforce && !$isBackoffice) || $canViewOtherDTR || $anApprover || $this->user->id == $id 
        || ($theImmediateHead 
        || $this->user->employeeNumber==$leader_L1->employeeNumber
        || $this->user->employeeNumber==$leader_L0->employeeNumber 
        || $this->user->employeeNumber==$leader_PM->employeeNumber ) && $canViewTeamDTR )  //($this->user->userType_id == 1 || $this->user->userType_id == 2)
        {     
          if(empty($request->from) && empty($request->to) )
          {
            $currentPeriod = array();

            //Timekeeping trait getCutoffStartEnd()
            $cData = $this->getCutoffStartEnd();
            $cutoffStart = $cData['cutoffStart'];//->cutoffStart;
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


             // ---------------------------
             // Generate cutoff period
             //----------------------------

             $payrollPeriod = [];
             
             $noWorkSched = false;

             //Timekeeping Trait
             $payrollPeriod = $this->getPayrollPeriod($cutoffStart,$cutoffEnd);
            

             // ---------------------------  INITIALIZATIONS
             $myDTR = new Collection;
             $daysOfWeek = array('Mon','Tue','Wed','Thu','Fri','Sat','Sun'); // for Gregorian cal. Iba kasi jddayofweek sa PHP day
             $coll = new Collection; $nightShift=""; $panggabi=""; $approvedOT=0; $billableForOT=0; $UT=0; $workSched=null; 
             $hasApprovedCWS=false; $usercws=null;$userOT=null; $OTattribute=""; $hasOT=false; $hasApprovedOT=false; $isFlexitime=null;$workedHours=null;
             $hasLeave=null;
             $shiftStart2=null;$shiftEnd2=null;
             $hybridSched = null;
             $shifts = $this->generateShifts('12H');
             $noWorkSched = true;




             // *************************** VERIFIED DTR SHEET
             $verifiedDTR = User_DTR::where('user_id',$user->id)->where('productionDate','>=',$currentPeriod[0])->
                                                  where('productionDate','<=',$currentPeriod[1])->orderBy('productionDate','ASC')->get();
            
            
           // return response()->json(['verified'=>count($verifiedDTR), 'payroll'=> count($payrollPeriod)]); 
                                                                                     
             if (  count($verifiedDTR) >= count($payrollPeriod)  )//|| ($currentPeriod[0] == $currentPeriod[1])
             {

                /*$myDTRSheet = new Collection;

                foreach ($payrollPeriod as $key) {
                  $mDsh = User_DTR::where('user_id',$user->id)->where('productionDate',$key)->orderBy('created_at','DESC')->get();

                  if (count($mDsh)>0){
                    $myDTRSheet->push($mDsh->first());

                  }
                }*/
                $myDTRSheet = $verifiedDTR;

                  $paystart = $currentPeriod[0];
                  $payend = $currentPeriod[1];

                  return view('timekeeping.myDTRSheet', compact('fromYr', 'payrollPeriod', 'anApprover','isWorkforce','employeeisBackoffice', 'TLapprover', 'DTRapprovers', 'canChangeSched', 'paycutoffs', 'shifts','cutoffID', 'myDTRSheet','camps','user','theImmediateHead', 'immediateHead','cutoff','noWorkSched', 'prevTo','prevFrom','nextTo','nextFrom','paystart','payend'));
 

             }



             // *************************** end VERIFIED DTR SHEET


             // ---------------------------
             // Determine first if FIXED OR SHIFTING sched
             // and then get WORKSCHED and RD sched
             // ---------------------------
              
             if (count($user->monthlySchedules) > 0)
             {


                /* ------ check mo muna kung hybrid sched ----*/
                if ( $user->fixedSchedule->isEmpty() )
                {
                  $monthlySched = MonthlySchedules::where('user_id',$id)->where('productionDate','>=', $currentPeriod[0])->where('productionDate','<=',$currentPeriod[1])->get();
               
               
                  $workSched = MonthlySchedules::where('user_id',$id)->where('productionDate','>=', $currentPeriod[0])->where('productionDate','<=',$currentPeriod[1])->where('isRD',0)->get(); //Collection::make($monthlySched->where('isRD',0)->all());
                  $RDsched = MonthlySchedules::where('user_id',$id)->where('productionDate','>=', $currentPeriod[0])->where('productionDate','<=',$currentPeriod[1])->where('isRD',1)->get();  //$monthlySched->where('isRD',1)->all();
                  $isFixedSched = false;
                  $noWorkSched = false;
                  $hybridSched = false;

                }else //------------------------- HYBRID SCHED ------------------
                {

                  $hybridSched = true;
                  $noWorkSched = false;
                  //$workdays = new Collection;
                  $isFixedSched = false;

                  $hybridSched_WS_fixed = FixedSchedules::where('user_id',$user->id)->where('isRD',0)->orderBy('created_at','DESC')->get();
                   //$user->fixedSchedule->where('isRD',0)->sortByDesc('updated_at');
                  $hybridSched_WS_monthly = MonthlySchedules::where('user_id',$id)->where('productionDate','>=', $currentPeriod[0])->where('productionDate','<=',$currentPeriod[1])->where('isRD',0)->orderBy('updated_at','DESC')->get(); 
                  $hybridSched_RD_fixed = FixedSchedules::where('user_id',$user->id)->where('isRD',1)->orderBy('created_at','DESC')->get();
                  // $user->fixedSchedule->where('isRD',1);
                  $hybridSched_RD_monthly = MonthlySchedules::where('user_id',$id)->where('productionDate','>=', $currentPeriod[0])->where('productionDate','<=',$currentPeriod[1])->where('isRD',1)->orderBy('updated_at','DESC')->get();

                  /*--- and then compare which is the latest of those 2 scheds --*/


                }
                



             } else
             {
                if (count($user->fixedSchedule) > 0)
                {
                    //merong fixed sched
                    $workSched = FixedSchedules::where('user_id',$user->id)->where('isRD',0)->orderBy('created_at','DESC')->get();
                    // $user->fixedSchedule->where('isRD',0);
                    $RDsched = FixedSchedules::where('user_id',$user->id)->where('isRD',1)->orderBy('created_at','DESC')->get(); 
                    //$user->fixedSchedule->where('isRD',1)->pluck('workday');
                    $isFixedSched =true;
                    $noWorkSched = false;


                    //--- taken from user show()
                    //$workSched = new Collection;
                    
                    //$fsched = $user->fixedSchedule->where('isRD',0)->sortBy('workday')->groupBy('workday');
                    //$rdays = $user->fixedSchedule->where('isRD',1)->sortBy('workday')->groupBy('workday');;
                    $workdays = new Collection;
                    

                } else
                {
                    $noWorkSched = true;
                    $workSched = null;
                    $RDsched = null;
                    $isFixedSched = false;
                }
             }



             // ---------------------------
             // Start Payroll generation
             // ---------------------------
            

            
              
             foreach ($payrollPeriod as $payday) 
             {
                $hasCWS = false; $hasApprovedCWS=false; $hasOT=false; $hasApprovedOT=false;
                $hasLWOP=false;

                $bioForTheDay = Biometrics::where('productionDate',$payday)->first();
                $carbonPayday = Carbon::parse($payday);
                $nextDay = Carbon::parse($payday)->addDay();
                $prevDay = Carbon::parse($payday)->subDay();
                $bioForTom = Biometrics::where('productionDate',$nextDay->format('Y-m-d'))->first();
                if ( is_null($bioForTom) )
                {
                  $bioForTomorrow = new Collection;
                  $bioForTomorrow->push(['productionDate'=>$nextDay->format('Y-m-d')]);
                }
                else
                  $bioForTomorrow = $bioForTom;

                $holidayToday = Holiday::where('holidate', $payday)->get();
                (count($holidayToday) > 0) ? $hasHolidayToday = true : $hasHolidayToday = false;
                               
                  
                  if($noWorkSched)
                  {

                    if( is_null($bioForTheDay) ) 
                    {
                            $logIN = "<strong class=\"text-success\">No <br/>Biometrics</strong>";
                            $logOUT = "<strong class=\"text-success\">No <br/>Biometrics</strong>";
                            $workedHours = 'N/A';
                            

                    } else
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

                             $myDTR->push(['payday'=>$payday,
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

                                 'hasLeave' => null,
                                  'leaveDetails'=>null,
                                  'hasLWOP' => null,
                                  'lwopDetails'=>null,

                                       'pendingDTRPin'=> null,
                                       'hasPendingOUT' =>null, //$userLogOUT[0]['hasPendingDTRP'],
                                       'pendingDTRPout' =>null,
                                 'workedHours'=> $workedHours,
                                 'billableForOT' => $billableForOT,
                                 'OTattribute'=>$OTattribute,
                                 'UT'=>$UT,
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
                          //--- We now check if employee has a CWS submitted for this day
                            //**************************************************************
                            //      CWS & OT & DTRPs
                            //**************************************************************

                          $usercws = User_CWS::where('user_id',$id)->where('biometrics_id',$bioForTheDay->id)->orderBy('updated_at','DESC')->get();
                          $approvedCWS  = User_CWS::where('user_id',$id)->where('biometrics_id',$bioForTheDay->id)->where('isApproved',1)->orderBy('updated_at','DESC')->get();
                          

                          if ( count($usercws) > 0 ) $hasCWS=true;
                          if ( count($approvedCWS) > 0 ) $hasApprovedCWS=true;


                          $userOT = User_OT::where('user_id',$id)->where('biometrics_id',$bioForTheDay->id)->get();
                          $approvedOT  = User_OT::where('user_id',$id)->where('biometrics_id',$bioForTheDay->id)->where('isApproved',1)->orderBy('updated_at','DESC')->get();
                          if ( count($userOT) > 0 ) $hasOT=true;
                          if ( count($approvedOT) > 0 ) $hasApprovedOT=true;



                          //$coll2->push($payday);

                           //**************************************************************
                            //       HYBRID SCHEDULES --------this is where we check the hybrids and classify accordingly
                            //**************************************************************

                          $check_fixed_RD=null; $check_monthly_RD=null; //initializes
                          
                          if ($hybridSched)
                          {
                            $collec = new Collection;
                            $dt  = $carbonPayday->dayOfWeek;
                            switch($dt){
                              case 0: $dayToday = 6; break;
                              case 1: $dayToday = 0; break;
                              default: $dayToday = $dt-1;
                            } 

                            $check_fixed_WS = $hybridSched_WS_fixed->where('workday',$dayToday)->sortByDesc('created_at');


                            if (count($check_fixed_WS) > 0) //if may fixed WS, check mo kung ano mas updated vs monthly sched
                            {
                              $check_monthly_WS = $hybridSched_WS_monthly->where('productionDate', $payday)->sortByDesc('created_at');
                              //$coll->push(['check_monthly_WS'=>$check_monthly_WS]);

                              if (count($check_monthly_WS) > 0)// if may monthly, compare it vs fixed
                              {
                                if( Carbon::parse($check_monthly_WS->first()->created_at,'Asia/Manila')->format('Y-m-d H:i:s') > Carbon::parse($check_fixed_WS->first()->created_at,'Asia/Manila')->format('Y-m-d H:i:s')  ) //mas bago si Monthly
                                {

                                  $workSched = $hybridSched_WS_monthly;
                                  $RDsched = $hybridSched_RD_monthly;
                                  $isFixedSched = false;
                                  $noWorkSched =false;



                                }
                                else //check mo muna validity nung WS na fixed. If no effectivity, then NO SCHED
                                {
                                  if ((Carbon::parse($check_fixed_WS->first()->schedEffectivity)->startOfDay() <= $carbonPayday->startOfDay()) || $check_fixed_WS->first()->schedEffectivity == null)
                                  {
                                    $workSched = $hybridSched_WS_fixed;
                                    $RDsched = $hybridSched_RD_fixed;
                                    $isFixedSched = true;
                                    $noWorkSched = false;

                                    
                                  }
                                  else{
                                    $noWorkSched = true;$workSched = null;$RDsched = null;$isFixedSched = false; $hasCWS=false;
                                   }


                                }

                              }
                              else //fixed sched na talaga sya
                              {
                                
                                //$check_monthly_RD = $hybridSched_RD_monthly->where('productionDate',$payday)->sortByDesc('created_at');
                                // * not so fast. Check mo muna kung may monthly RD to be sure. Otherwise, fixed WS nga sya

                                $check_monthly_RD = $hybridSched_RD_monthly->where('productionDate',$payday)->sortByDesc('created_at');
                                //MonthlySchedules::where('user_id',$user->id)->where('isRD','1')->where('productionDate',$payday)->orderBy('created_at','DESC')->get();

                                if ($check_monthly_RD->isEmpty())
                                { //check mo muna validity nung WS na fixed. If not effective, then NO SCHED
                                  if ((Carbon::parse($check_fixed_WS->first()->schedEffectivity)->startOfDay() <= $carbonPayday->startOfDay()) || $check_fixed_WS->first()->schedEffectivity == null)
                                  {
                                    $workSched = $hybridSched_WS_fixed;
                                    $RDsched = $hybridSched_RD_fixed;
                                    $isFixedSched = true;
                                    $noWorkSched = false;
                                    
                                  }
                                   else
                                   {
                                    $noWorkSched = true;$workSched = null;$RDsched = null;$isFixedSched = true; $hasCWS=false;
                                  }


                                } else
                                {
                                  if ( Carbon::parse($check_monthly_RD->first()->created_at,'Asia/Manila')->format('Y-m-d H:i:s') > Carbon::parse($check_fixed_WS->first()->created_at,'Asia/Manila')->format('Y-m-d H:i:s')) //mas updated yung RD so di sya WS
                                  {
                                    $workSched = $hybridSched_WS_monthly;
                                    $RDsched =  $hybridSched_RD_monthly;
                                    $isFixedSched = false;
                                    $noWorkSched = false;


                                  } else
                                  {
                                    //check mo muna validity nung WS na fixed. If not effective, then NO SCHED

                                     if ((Carbon::parse($check_fixed_WS->first()->schedEffectivity)->startOfDay() <= $carbonPayday->startOfDay()) || $check_fixed_WS->first()->schedEffectivity == null)
                                     {

                                      $workSched = $hybridSched_WS_fixed;
                                      $RDsched = $hybridSched_RD_fixed;
                                      $isFixedSched = true;
                                      $noWorkSched = false;
                                     }
                                     else
                                     {
                                      $noWorkSched = true;$workSched = null;$RDsched = null;$isFixedSched = false; $hasCWS=false;
                                     }

                                  }

                                }

                                 
                              }


                            } else //baka RD
                            {
                              $check_fixed_RD = $hybridSched_RD_fixed->where('workday',$dayToday)->sortByDesc('created_at');

                              if (count($check_fixed_RD) > 0) //if may fixed RD, check mo kung ano mas updated vs monthly sched
                              {
                                $check_monthly_RD = $hybridSched_RD_monthly->where('productionDate',$payday)->sortByDesc('created_at');

                                if (count($check_monthly_RD) > 0) // if may monthly, compare it vs fixed
                                {
                                  if( Carbon::parse($check_monthly_RD->first()->created_at,'Asia/Manila')->format('Y-m-d H:i:s') > Carbon::parse($check_fixed_RD->first()->created_at,'Asia/Manila')->format('Y-m-d H:i:s') ) //mas bago si Monthly
                                  {
                                    $workSched = $hybridSched_WS_monthly;
                                    $RDsched = $hybridSched_RD_monthly;
                                    $isFixedSched = false;
                                    $noWorkSched = false;

                                  }
                                  else //FIXED RD SYA
                                  {
                                    //check mo muna validity nung RD na fixed. If not effective, then NO SCHED

                                     if ((Carbon::parse($check_fixed_RD->first()->schedEffectivity)->startOfDay() <= $carbonPayday->startOfDay()) || $check_fixed_RD->first()->schedEffectivity == null)
                                     {
                                      $workSched = $hybridSched_WS_fixed;
                                      $RDsched = $hybridSched_RD_fixed;
                                      $isFixedSched = true;
                                      $noWorkSched= false;

                                     }
                                     else
                                     {
                                      $noWorkSched = true;$workSched = null;$RDsched = null;$isFixedSched = false; $hasCWS=false;
                                     }

                                  }

                                }
                                else // no monthly RD -- meaning RD fixed na sya, BUT NOT YET! CHECK MO KUNG MAS UPDATED UNG WS IF EVER MERON
                                {
                                  $check_monthly_WS = $hybridSched_WS_monthly->where('productionDate', $payday)->sortByDesc('created_at');

                                  if (count($check_monthly_WS) > 0)
                                  {
                                    if(Carbon::parse($check_monthly_WS->first()->created_at,'Asia/Manila')->format('Y-m-d H:i:s') > Carbon::parse($check_fixed_RD->first()->created_at,'Asia/Manila')->format('Y-m-d H:i:s')  )
                                    {
                                      $workSched = $hybridSched_WS_monthly;
                                      $RDsched = $hybridSched_RD_monthly;
                                      $isFixedSched = false;
                                      $noWorkSched =false;
                                    }
                                    else
                                      {
                                        //check mo muna validity nung RD na fixed. If not effective, then NO SCHED

                                         if ((Carbon::parse($check_fixed_RD->first()->schedEffectivity)->startOfDay() <= $carbonPayday->startOfDay()) || $check_fixed_RD->first()->schedEffectivity == null)
                                         {

                                          $workSched = $hybridSched_WS_fixed;
                                          $RDsched = $hybridSched_RD_fixed;
                                          $isFixedSched = true;
                                          $noWorkSched = false;
                                         }
                                         else
                                         {
                                          $noWorkSched = true;$workSched = null;$RDsched = null;$isFixedSched = false; $hasCWS=false;
                                         }

                                      }

                                  } else //walang monthly WS, Fixed sched na sya
                                  {
                                     //check mo muna validity nung WS na fixed. If not effective, then NO SCHED

                                     if ((Carbon::parse($check_fixed_RD->first()->schedEffectivity)->startOfDay() <= $carbonPayday->startOfDay()) || $check_fixed_RD->first()->schedEffectivity == null)
                                     {
                                        $workSched = $hybridSched_WS_fixed;
                                        $RDsched = $hybridSched_RD_fixed;
                                        $isFixedSched = true;
                                        $noWorkSched = false;
                                     }
                                     else
                                     {
                                      $noWorkSched = true;$workSched = null;$RDsched = null;$isFixedSched = false; $hasCWS=false;
                                     }


                                  }
                                 
                                }

                              }//end if no fixed RD

                              else //---- no both fixed WS & RD, baka monthly sched. Check kung meron
                              {
                                if (count($hybridSched_WS_monthly)>0 && count($hybridSched_RD_monthly)>0)
                                {
                                  if (count($hybridSched_WS_monthly) > 0)
                                  {
                                    $workSched = $hybridSched_WS_monthly;

                                  } else $workSched = null;

                                  if (count($hybridSched_RD_monthly) > 0)
                                  {
                                    $RDsched = $hybridSched_RD_monthly;
                                  }
                                  else $RDsched = null;

                                }else //waley na talaga
                                {
                                  $workSched=null; $RDsched=null; $isFixedSched=false; $noWorkSched=true;

                                }
                                


                              }//end else no both fixed RD & WS
                              
                              

                            }//end else baka RD

                          } //end hybrid sched


                         
                            //**************************************************************
                            //       FIXED SCHED
                            //**************************************************************

                            if($isFixedSched)
                            {
                                $day = date('D', strtotime($payday)); //--- get his worksched and RDsched
                                $theday = (string)$day;
                                $numDay = array_search($theday, $daysOfWeek);

                                $yest = date('D', strtotime(Carbon::parse($payday)->subDay()->format('Y-m-d')));
                                $prevNumDay = array_search($yest, $daysOfWeek);
                                //$coll->push(['day'=>$day,'theday-1'=>$yest, 'prevNumDay'=>$prevNumDay]);
                            }


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

                               /* -- CHECK FIRST IF MAY APPROVED CWS from RD into working sched --*/
                              $fromRDtoWD = User_CWS::where('user_id',$id)->where('isApproved',true)->where('biometrics_id',$bioForTheDay->id)->where('timeStart_old',"00:00:00")->where('timeEnd_old',"00:00:00")->orderBy('updated_at','DESC')->get();
                              $fromWDtoRD = User_CWS::where('user_id',$id)->where('isApproved',true)->where('biometrics_id',$bioForTheDay->id)->where('timeStart',"00:00:00")->where('timeEnd',"00:00:00")->orderBy('updated_at','DESC')->get();

                              //$coll->push(['payday'=>$payday, 'fromWDtoRD'=>$fromWDtoRD]);

                              //return $coll2->push(['noWorkSched'=>$noWorkSched]);

                              if ($noWorkSched) 
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
                                        { $workedHours = "(8.0)<br/><strong>* ". $holidayToday->first()->name. " *</strong>";
                                        } else { $workedHours = "N/A";}
                                           
                                      }

                                       
                                       //return $myDTR;

                                }// end if isnull bioForToday

                                $myDTR->push(['payday'=>$payday,
                                             'biometrics_id'=>$bioForTheDay->id,
                                             'hasCWS'=>$hasCWS,
                                             'hasLeave'=>$hasLeave,
                                              //'usercws'=>$usercws->sortByDesc('updated_at')->first(),
                                              'usercws'=>$usercws->sortByDesc('updated_at'),
                                              'userOT'=>$userOT,
                                              'hasOT'=>$hasOT,

                                              'hasLeave' => null,
                                                'leaveDetails'=>null,
                                                'hasLWOP' => null,
                                                'lwopDetails'=>null,


                                             'isRD'=>0,
                                             'isFlexitime'=>$isFlexitime,
                                             'productionDate'=> date('M d, Y', strtotime($payday)),
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
                                             'pendingDTRPin'=> null,
                                             'hasPendingOUT' =>null, //$userLogOUT[0]['hasPendingDTRP'],
                                             'pendingDTRPout' =>null,
                                             'workedHours'=> $workedHours,
                                             'billableForOT' => $billableForOT,
                                             'OTattribute'=>$OTattribute,
                                             'UT'=>$UT,
                                             'approvedOT' => $approvedOT]);


                                goto endNoWorkSched;

                              } //END NOWORKSCHED
                              else
                              {
                                /* ---------- july 2018 re-work algorithm ---------- */

                                // ---------- if both may to-from CWS, compare which is more updated
                                //. a) fromRD vs fromWD
                                //  b) a-winner vs RDsched

                                // ---------- else if fromRD > 0 ->rdToday = false
                                //----------- else if fromWD > 0 ->rdToday = true

                               
                                //$coll->push(["RDtoWD"=>$fromRDtoWD]);

                                if (count($fromRDtoWD)>0 ) //but we need to check first alin mas updated: cws or the plotted sched
                                {
                                  $coll->push(["count ni RDtoWD"=>count($fromRDtoWD), "first"=>$fromRDtoWD->first()]);
                                  
                                  //but we need to verify first which is more latest
                                  if ($isFixedSched)
                                  {
                                    
                                    //$dschedule = $user->fixedSchedule->where('isRD',1)->where('workday', $numDay)->sortByDesc('updated_at'); 
                                    $ds = $RDsched->where('workday',$numDay);

                                    (count($ds)>0) ? $dschedule = $ds->first() : $dschedule=null;
                                    //$dschedule = $RDsched->where('workday',$numDay)->first();
                                    

                                  } else {

                                    $ds = $RDsched->where('productionDate',$payday);
                                    (count($ds)>0) ? $dschedule = $ds->first() : $dschedule=null;
                                    //$dschedule = $RDsched->where('productionDate',$payday)->first();
                                    

                                  }
                                  //$coll->push(["dschedule"=>$dschedule, "RDsched"=>$RDsched, 'isFixedSched'=>$isFixedSched, 'numDay'=>$numDay]);

                                  /******** aug 2018 fix: do only this if not null dsched, else it's an RDtoWD sched na ********/
                                  if (is_null($dschedule)) //meaning WORK DAY NA SYA due to CWS
                                  {
                                    $isRDToday=false; 

                                  } else
                                  {

                                    if ($fromRDtoWD->first()->updated_at > $dschedule->created_at )
                                    $isRDToday=false; 
                                    else
                                      {
                                        if($isFixedSched)
                                          $isRDToday = $RDsched->contains($numDay); 
                                          else
                                          {
                                            if ($hybridSched)
                                            {

                                              $rd = $RDsched;

                                            }else
                                            {
                                              $rd = $monthlySched->where('isRD',1)->where('productionDate',$payday)->all(); 
                                            }
                                            

                                            if (count($rd)<= 0 ) 
                                              $isRDToday=false; else $isRDToday=true;

                                            //$coll->push(['rd'=>$rd, 'isRDToday'=>$isRDToday]); 
                                          }

                                      }//end if else fromRDtoWD > dschedule

                                  }//end isnull dschedule
                                  
                                  
                                  

                                  
                                  

                                } 
                                else if ( count($fromWDtoRD) > 0 )
                                { 

                                  //but we need to verify first which is more latest
                                  if ($isFixedSched)
                                  {
                                    $ds = $workSched->where('workday',$numDay);

                                    (count($ds)>0) ? $dschedule = $ds->first() : $dschedule=null;
                                    //$dschedule = $workSched->where('workday',$numDay)->first();

                                  }
                                    
                                  else {
                                    $ds = $workSched->where('productionDate',$payday);

                                    (count($ds)>0) ? $dschedule = $ds->first() : $dschedule=null;
                                    
                                  }

                                  if (is_null($dschedule)) $isRDToday=true;
                                  else{
                                    ($fromWDtoRD->first()->updated_at > $dschedule->created_at ) ? $isRDToday=true :$isRDToday=false;
                                  }

                                  

                                  
                                }//end fromWDtoRD

                                
                                else 
                                { /*------- FOR REGULAR, NON-HYBRID SCHEDULES -------*/
                                  if($isFixedSched) {

                                    $isRDToday = $this->getLatestFixedSched($user,$numDay,$payday)->isRD;
                                    //$isRDToday = $RDsched->contains('workday',$numDay); 
                                  //$coll2->push(['from: '=>"reg isFixed", 'RDsched'=>$RDsched, 'numDay'=>$numDay]); 
                                }
                                  else
                                  {
                                    $rd = $RDsched->where('isRD',1)->where('productionDate',$payday)->all(); 
                                    if (count($rd)<= 0 ) $isRDToday=false; else $isRDToday=true;

                                    $coll->push(['from: '=>"regular else"]);
                                  }
                                }
                              
                             

                              }

                             

                                
                                
                              //**************************************************************
                              //       Rest Day SCHED
                              //**************************************************************

                              if ($isRDToday)
                              {
                                if($sameDayLog)
                                {
                                  

                                   $data = $this->getRDinfo($id, $bioForTheDay,true,$payday);
                                   //$coll->push(['data'=>$data, 'isRDToday'=>$isRDToday]);
                                   $coll->push(['data from:'=>"sameDayLog > RDToday > Restday"]);
                                      $myDTR->push(['isRDToday'=>$isRDToday, 'payday'=>$payday,
                                         'biometrics_id'=>$bioForTheDay->id,
                                         'hasCWS'=>$hasCWS,
                                         //'usercws'=>$usercws->sortByDesc('updated_at')->first(),
                                         'usercws'=>$usercws,
                                         'userOT'=>$userOT,
                                         'hasPendingIN' => $data[0]['hasPendingIN'],
                                         'pendingDTRPin'=> $data[0]['pendingDTRPin'],
                                         'hasPendingOUT' => $data[0]['hasPendingOUT'],
                                         'pendingDTRPout' => $data[0]['pendingDTRPout'],
                                         'hasApprovedCWS'=> $hasApprovedCWS,
                                         'hasOT'=>$hasOT,
                                         'hasApprovedOT'=>$hasApprovedOT,
                                         'isRD'=>$isRDToday,
                                         'isFlexitime'=> $isFlexitime,
                                         'productionDate'=> date('M d, Y', strtotime($payday)),
                                         'day'=> date('D',strtotime($payday)),
                                         'shiftStart'=> $data[0]['shiftStart'],
                                         'shiftEnd'=>$data[0]['shiftEnd'],

                                          'hasLeave' => null,
                                          'leaveDetails'=>null,
                                          'hasLWOP' => null,
                                          'lwopDetails'=>null,


                                   'shiftStart2'=>  $data[0]['shiftStart'],
                                   'shiftEnd2'=>$data[0]['shiftEnd'],
                                         'logIN' => $data[0]['logIN'],
                                         'logOUT'=>$data[0]['logOUT'],
                                         'dtrpIN'=>$data[0]['dtrpIN'],
                                         'dtrpOUT'=>$data[0]['dtrpOUT'],
                                         'dtrpIN_id'=>$data[0]['dtrpIN_id'],
                                         'dtrpOUT_id'=>$data[0]['dtrpOUT_id'],
                                         'workedHours'=> $data[0]['workedHours'],
                                         'billableForOT' => $data[0]['billableForOT'],
                                         'OTattribute' => $data[0]['OTattribute'],
                                         'UT'=>$data[0]['UT'],
                                         'approvedOT' => $data[0]['approvedOT']]);

                                }
                                else //****** not sameDayLog
                                {
                                    $data = $this->getRDinfo($id, $bioForTheDay,false,$payday);
                                    $coll->push(['data from:'=>"notsameDayLog > RDToday > Restday"]);

                                     $myDTR->push(['isRDToday'=>$isRDToday, 'payday'=>$payday,
                                         'biometrics_id'=>$bioForTheDay->id,
                                         'hasCWS'=>$hasCWS,
                                         //'usercws'=>$usercws->sortByDesc('updated_at')->first(),
                                         'usercws'=>$usercws,
                                         'userOT'=>$userOT,
                                         'hasApprovedCWS'=> $hasApprovedCWS,
                                         'hasOT'=>$hasOT,
                                         'hasApprovedOT'=>$hasApprovedOT,
                                         'hasPendingIN' => $data[0]['hasPendingIN'],
                                         'pendingDTRPin'=> $data[0]['pendingDTRPin'],
                                         'hasPendingOUT' => $data[0]['hasPendingOUT'],
                                         'pendingDTRPout' => $data[0]['pendingDTRPout'],
                                         'isRD'=>$isRDToday,
                                         'isFlexitime'=> $isFlexitime,
                                         'productionDate'=> date('M d, Y', strtotime($payday)),
                                         'day'=> date('D',strtotime($payday)),
                                         'shiftStart'=> $data[0]['shiftStart'],
                                         'shiftEnd'=>$data[0]['shiftEnd'],
                                         'shiftStart2'=>  $data[0]['shiftStart'],

                                          'hasLeave' => null,
                                          'leaveDetails'=>null,
                                          'hasLWOP' => null,
                                          'lwopDetails'=>null,

                                   'shiftEnd2'=>$data[0]['shiftEnd'],
                                         'logIN' => $data[0]['logIN'],
                                         'logOUT'=>$data[0]['logOUT']."<br/><small>".$bioForTomorrow->productionDate."</small>",
                                         'dtrpIN'=>$data[0]['dtrpIN'],
                                         'dtrpOUT'=>$data[0]['dtrpOUT'],
                                         'dtrpIN_id'=>$data[0]['dtrpIN_id'],
                                         'dtrpOUT_id'=>$data[0]['dtrpOUT_id'],
                                         'workedHours'=> $data[0]['workedHours'],
                                         'billableForOT' => $data[0]['billableForOT'],
                                         'OTattribute' => $data[0]['OTattribute'],
                                         'UT'=>$data[0]['UT'],
                                         'approvedOT' => $data[0]['approvedOT']]);

                                

                                }//end RD not SAME DAY LOG

                                  

                                      //$coll->push(['isRDToday'=>$isRDToday]);
                              }//end if isRDToday


                              //**************************************************************
                              //       WORK DAY
                              //**************************************************************
                              else  
                              {
                                    $problemArea = new Collection;
                                    $problemArea->push(['problemShift'=>false, 'allotedTimeframe'=>null, 'biometrics_id'=>null]);
                                    //$isAproblemShift = false;


                                    /* --------------- handle proper schedule for today for FIXED OR MONTHLY ---------*/
                                    if ($isFixedSched)
                                    {
                                      if ($hasApprovedCWS)
                                      {
                                        if ( count($workSched->where('workday',$numDay)->all()) > 0 )
                                        {
                                          

                                          $ws = $this->getLatestFixedSched($user,$numDay,$payday);
                                            
                                          if ($ws->created_at > $approvedCWS->first()->updated_at )
                                          {
                                            $schedForToday = $ws; //$workSched->where('workday',$numDay)->first();

                                          } else $schedForToday = array('timeStart'=>$approvedCWS->first()->timeStart, 
                                                                'timeEnd'=> $approvedCWS->first()->timeEnd,
                                                                'isFlexitime' =>  $workSched->where('workday',$numDay)->first()->isFlexitime,
                                                                'isRD'=> $workSched->where('workday',$numDay)->first()->isRD);

                                        } else
                                        {
                                          $schedForToday = array('timeStart'=>$approvedCWS->first()->timeStart, 
                                                                'timeEnd'=> $approvedCWS->first()->timeEnd,
                                                                'isFlexitime' => false,
                                                                'isRD'=> null);
                                        }
                                        
                                       

                                      } else
                                      {
                                        $schedForToday = $this->getLatestFixedSched($user,$numDay,$payday);
                                          

                                      } 
                                        
                                    }
                                    else //-- find schedForToday for MONTHLY SCHEDULES
                                    {
                                        if ($hasApprovedCWS)
                                        {
                                          //--- hack for flexitime
                                          
                                          /*--- july 2018 fix ----*/
                                          // check mo muna kung mas updated ung plotted sched sa CWS

                                          if ( count($workSched->where('productionDate',$payday)->all()) > 0 )
                                          {

                                             if ($workSched->where('productionDate',$payday)->sortByDesc('id')->first()->created_at > $approvedCWS->first()->updated_at )
                                              {
                                                $schedForToday = $workSched->where('productionDate',$payday)->sortByDesc('id')->first();

                                              }else $schedForToday = array('timeStart'=>$approvedCWS->first()->timeStart, 
                                                                  'timeEnd'=> $approvedCWS->first()->timeEnd, 
                                                                  'isFlexitime'=>$workSched->where('productionDate',$payday)->first()->isFlexitime,
                                                                  'isRD'=>$workSched->where('productionDate',$payday)->first()->isRD);

                                          } else 
                                          {
                                            $schedForToday = array('timeStart'=>$approvedCWS->first()->timeStart, 
                                                                  'timeEnd'=> $approvedCWS->first()->timeEnd, 
                                                                  'isFlexitime'=>false,
                                                                  'isRD'=>null);

                                          }

                                         
                                          
                                          
                                         

                                        } else //walang CWS
                                          $schedForToday = $workSched->where('productionDate',$payday)->sortByDesc('id')->first();

                                    }//endelse if fixedSched

                                    /* --------------- END handle proper schedule for today for FIXED OR MONTHLY ---------*/

                
                                    $s = Carbon::parse($payday." ".$schedForToday['timeStart'],"Asia/Manila");
                                    $s2 = Carbon::parse($payday." ".$schedForToday['timeEnd'],"Asia/Manila");


                                    $shiftStart = date('h:i A',strtotime($schedForToday['timeStart']));
                                    $shiftEnd = date('h:i A',strtotime($schedForToday['timeEnd']));

                                    //Morning: #c7b305 bcaa0f
                                    //Eve:#6754c1
                                    $mn = Carbon::parse($payday." 00:00:00", "Asia/Manila");
                                    $noon = Carbon::parse($payday." 11:59:00", "Asia/Manila");

                                    if ( $s >= $mn && $s <= $noon ) {
                                      $shiftStart2 = '<span style="color:#bcaa0f; font-weight:bold">'. $shiftStart. '</span>';
                                    } else $shiftStart2 = '<span style="color:#6754c1; font-weight:bold">'. $shiftStart. '</span>';
                                    if  ( $s2 >=$mn && $s2 <= $noon ) {
                                     $shiftEnd2 = '<span style="color:#bcaa0f; font-weight:bold">'. $shiftEnd. '</span>';
                                    } else  $shiftEnd2 = '<span style="color:#6754c1; font-weight:bold">'. $shiftEnd. '</span>';

                                    

                                    if ( $s->format('Y-m-d H:i:s') >= Carbon::parse($payday." 00:00:00","Asia/Manila")->format('Y-m-d H:i:s') &&  $s->format('Y-m-d H:i:s') <=  Carbon::parse($payday." 05:00:00","Asia/Manila")->format('Y-m-d H:i:s') )
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

                                    //$coll->push(['isAproblemShift'=>$isAproblemShift,'s'=>$s, 'schedForToday'=>$schedForToday, 'sameDayLog'=>$sameDayLog, 'shiftStart'=>$shiftStart,'range'=>Carbon::parse($payday." 00:00:00","Asia/Manila") . " to ". date('h:i A', strtotime('05:00:00')),]);

                                    if ($sameDayLog)
                                    {
                                      

                                      $userLogIN = $this->getLogDetails('WORK', $id, $bioForTheDay->id, 1, $schedForToday, $UT,$problemArea,$isAproblemShift);
                                      $userLogOUT = $this->getLogDetails('WORK', $id, $bioForTheDay->id, 2, $schedForToday,0,$problemArea,$isAproblemShift);
                                      $coll->push(['IN'=>$userLogIN, 'OUT'=>$userLogOUT]); 

                                      if (empty($userLogOUT[0]['timing']))
                                      {
                                        //** but check mo muna kung may filed leave ba
                                        if($userLogOUT[0]['hasLeave'] || $userLogOUT[0]['hasLWOP'] || $userLogOUT[0]['hasSL'])
                                        {
                                          $data = $this->getWorkedHours($user->id,$userLogIN, $userLogOUT, $schedForToday,$shiftEnd,$payday);
                                          $workedHours= $data[0]['workedHours'];
                                          $billableForOT = $data[0]['billableForOT'];
                                          $OTattribute = $data[0]['OTattribute'];
                                          $UT = $data[0]['UT'];
                                          $coll->push(['ret workedHours:'=> $data, 'out'=>$userLogOUT]);

                                        }else{
                                              //meaning wala syang OUT talaga
                                            $workedHours= "N/A";
                                            $billableForOT = "-";
                                            $OTattribute = "-";
                                            $UT = "-";
                                        }
                                        

                                      }else{
                                        $data = $this->getWorkedHours($user->id,$userLogIN, $userLogOUT, $schedForToday,$shiftEnd,$payday);
                                        $workedHours= $data[0]['workedHours'];
                                        $billableForOT = $data[0]['billableForOT'];
                                        $OTattribute = $data[0]['OTattribute'];
                                        $UT = $data[0]['UT'];
                                        $coll->push(['ret workedHours:'=> $data, 'out'=>$userLogOUT]);

                                      } 
                                      // //$coll->push(['payday'=>$payday, 'userLogIN'=>$userLogIN, 'userLogOUT'=>$userLogOUT]);
                                      
                                     
                                        

                                        // $VLs = $data[0]['VL'];
                                        // $LWOPs = $data[0]['LWOP'];

                                  

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
                                            $isRDYest = $RDsched->contains($prevNumDay); 
                                          else
                                          {
                                            if ($hybridSched)
                                            {
                                              $rd = $RDsched->where('productionDate',$prevDay->format('Y-m-d'))->first();

                                            }else $rd = $monthlySched->where('isRD',1)->where('productionDate',$prevDay->format('Y-m-d'))->first();  
                                            if (empty($rd)) 
                                              $isRDYest=false; else $isRDYest=true;
                                          }

                                         

                                        /*-------------------------------------------
                                            Problem shifts: 12MN-5am
                                        ---------------------------------------------*/
                                       
                                          $userLogIN = $this->getLogDetails('WORK', $id, $bioForTheDay->id, 1, $schedForToday, $UT, $problemArea,$isAproblemShift);
                                          $coll->push(['datafrom'=>"else NOT Problem shift",'data IN'=>$userLogIN ]);
                                        //}

                                        

                                       //********** LOG OUT ***************

                                              if ($isAproblemShift)
                                              {

                                                if(count($bioForTom) > 0)
                                                  $userLogOUT = $this->getLogDetails('WORK', $id, $bioForTom->id, 2, $schedForToday,0, $problemArea,$isAproblemShift);
                                                  
                                                else
                                                  $userLogOUT[0]= array('logTxt'=> "No Data", 
                                                                        'UT'=>0,'logs'=>null,'dtrpIN'=>null,'dtrpIN_id'=>null, 'dtrpOUT'=>null,'dtrpOUT_id'=>null, 'hasPendingDTRP'=>null,'pendingDTRP'=>null);
                                                
                                                $coll->push(['sameDayLog'=>$sameDayLog, 'datafrom'=>"else  Problem shift",'data OUT'=>$userLogOUT ]);
                                              }
                                             
                                              else
                                              { 
                                                if(count($bioForTom) > 0){
                                                  $userLogOUT = $this->getLogDetails('WORK', $id, $bioForTheDay->id, 2, $schedForToday,0, $problemArea,$isAproblemShift);
                                                      $coll->push(['datafrom'=>"Normal out",'data OUT'=>$userLogOUT ]);

                                                  
                                                }
                                                else
                                                {
                                                  $userLogOUT[0]= array('logTxt'=> "No Data", 
                                                                        'UT'=>0,'logs'=>null,'dtrpIN'=>null,'dtrpIN_id'=>null, 'dtrpOUT'=>null,'dtrpOUT_id'=>null, 'hasPendingDTRP'=>null,'pendingDTRP'=>null);

                                                }

                                                $coll->push(['IN'=>$userLogIN, 'OUT'=>$userLogOUT]);

                                              }

                                                if($isRDYest || $isAproblemShift || !$sameDayLog)
                                                {
                                                  $data = $this->getComplicatedWorkedHours($user->id,$userLogIN, $userLogOUT, $schedForToday,$shiftEnd,$isRDYest,$payday);
                                                  //$coll->push(['workedHours'=>"(isRDYest || isAproblemShift || !sameDayLog)", 'checkLate'=>$data[0]['checkLate'],'biometricsID'=>$bioForTheDay->id]);
                                                }
                                                else
                                                  {
                                                    $data = $this->getWorkedHours($user->id,$userLogIN, $userLogOUT, $schedForToday,$shiftEnd, $payday);
                                                   //$coll->push(['datafrom'=>"(ELSE isRDYest || isAproblemShift || !sameDayLog) [WH]"]);
                                                  }
                                                

                                                $workedHours=$data[0]['workedHours'];
                                                $billableForOT = $data[0]['billableForOT'];
                                                $OTattribute = $data[0]['OTattribute'];
                                                $UT = $data[0]['UT'];
                                                $VLs = $data[0]['VL'];
                                                $LWOPs = $data[0]['LWOP'];

                                        

                                    } //--- else not sameDayLog



                                    if(is_null($schedForToday)) {
                                        
                                        $myDTR->push(['payday'=>$payday,
                                       'biometrics_id'=>$bioForTheDay->id,
                                       'hasCWS'=>$hasCWS,
                                        //'usercws'=>$usercws->sortByDesc('updated_at')->first(),
                                        'usercws'=>$usercws,
                                        'userOT'=>$userOT,
                                        'hasOT'=>$hasOT,
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

                                       'hasLeave' => $userLogIN[0]['hasLeave'],
                                       'leaveDetails'=>$userLogIN[0]['leave'],
                                       'hasLWOP' => $userLogIN[0]['hasLWOP'],
                                       'lwopDetails'=>$userLogIN[0]['lwop'],

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
                                      $myDTR->push(['isRDToday'=>$isRDToday, 'isAproblemShift'=>$isAproblemShift, 'payday'=> $payday,
                                          'biometrics_id'=>$bioForTheDay->id,
                                          'hasCWS'=>$hasCWS,
                                          //'usercws'=>$usercws->sortByDesc('updated_at')->first(),
                                          'usercws'=>$usercws,
                                          'userOT'=>$userOT,
                                          'hasOT'=>$hasOT,
                                          'hasApprovedOT'=>$hasApprovedOT,
                                          'hasPendingIN' => $userLogIN[0]['hasPendingDTRP'],
                                           'pendingDTRPin'=> $userLogIN[0]['pendingDTRP'],
                                           'hasPendingOUT' => $userLogOUT[0]['hasPendingDTRP'],
                                           'pendingDTRPout' =>$userLogOUT[0]['pendingDTRP'],

                                           'hasLeave' => $userLogIN[0]['hasLeave'],
                                          'leaveDetails'=>$userLogIN[0]['leave'],
                                          'hasLWOP' => $userLogIN[0]['hasLWOP'],
                                          'lwopDetails'=>$userLogIN[0]['lwop'],


                                          'isRD'=> 0,
                                          'isFlexitime'=>$schedForToday['isFlexitime'], //$isFlexitime,
                                          'productionDate'=> date('M d, Y', strtotime($payday)),
                                          'hasApprovedCWS'=>$hasApprovedCWS,
                                         'day'=> date('D',strtotime($payday)),
                                         'shiftStart'=> $shiftStart,
                                         'shiftEnd'=>$shiftEnd,
                                         'shiftStart2'=> $shiftStart2,
                                         'shiftEnd2'=>$shiftEnd2,
                                         'logIN' => $userLogIN[0]['logTxt'],
                                         'logOUT'=>$userLogOUT[0]['logTxt'],
                                         'dtrpIN'=>$userLogIN[0]['dtrpIN'],
                                         'dtrpIN_id'=>$userLogIN[0]['dtrpIN_id'],
                                         'dtrpOUT'=> $userLogOUT[0]['dtrpOUT'],
                                         'dtrpOUT_id'=> $userLogOUT[0]['dtrpOUT_id'],
                                         'workedHours'=> $workedHours,
                                         'billableForOT' => $billableForOT,
                                         'OTattribute'=> $OTattribute,
                                         'UT'=>$UT, //$userLogOUT[0]['UT'],
                                         'approvedOT' => $approvedOT,
                                         'wholeIN' => $userLogIN,
                                         'wholeOUT' =>$userLogOUT,
                                         'schedForToday'=>$schedForToday

                                         // 'VL'=>$VLs, 'LWOP'=>$LWOPs

                                       ]);


                                    } 


                              }//end else WORK DAY
                              

                        }//end else not null BioForTheDay

                         

                  }//end if else noWorkSched

                  endNoWorkSched:
                  //$noWorkSched = null; //*** we need to reset things
             }//END foreach payrollPeriod

          //return $coll;


            $correct = Carbon::now('GMT+8'); //->timezoneName();

          //return $myDTR;

           if($this->user->id !== 564 ) {
              $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
                fwrite($file, "-------------------\n Viewed DTR of: ".$user->lastname."[".$user->id."] --" . $correct->format('M d h:i A'). " by [". $this->user->id."] ".$this->user->lastname."\n");
                fclose($file);
            } 


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


           //return response()->json(['isFixedSched'=>$isFixedSched]);



           return view('timekeeping.myDTR', compact('fromYr', 'entitledForLeaves', 'anApprover', 'TLapprover', 'DTRapprovers', 'canChangeSched', 'paycutoffs', 'shifts','cutoffID','verifiedDTR', 'myDTR','camps','user','theImmediateHead', 'immediateHead','cutoff','noWorkSched', 'prevTo','prevFrom','nextTo','nextFrom','memo','notedMemo','payrollPeriod'));


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
        if (count($theNotif) > 0 )
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


}
