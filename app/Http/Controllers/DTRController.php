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

      ($request->reportType == 'dailyLogs') ? $result = $this->fetchLockedDTRs($request->cutoff, $request->program,1) : $result = $this->fetchLockedDTRs($request->cutoff, $request->program,null);


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
                      // select('users.accesscode','users.id', 'users.firstname','users.middlename', 'users.lastname','users.nickname','positions.name as jobTitle','campaign.id as campID', 'campaign.name as program','immediateHead_Campaigns.id as tlID', 'immediateHead.firstname as leaderFname','immediateHead.lastname as leaderLname','floor.name as location','user_dtr.productionDate','user_dtr.workshift','user_dtr.isCWS_id', 'user_dtr.timeIN','user_dtr.timeOUT','user_dtr.isDTRP_in','user_dtr.isDTRP_out', 'user_dtr.hoursWorked','user_dtr.leaveType','user_dtr.leave_id', 'user_dtr.OT_billable','user_dtr.OT_approved','user_dtr.OT_id','user_dtr.UT', 'user_dtr.user_id','user_dtr.biometrics_id','user_dtr.updated_at')->
                       select('users.accesscode','users.id','users.isWFH', 'users.firstname','users.lastname','users.middlename', 'users.nickname','positions.name as jobTitle','campaign.id as campID', 'campaign.name as program','immediateHead_Campaigns.id as tlID', 'immediateHead.firstname as leaderFname','immediateHead.lastname as leaderLname','floor.name as location','user_dtr.productionDate','user_dtr.biometrics_id','user_dtr.workshift','user_dtr.isCWS_id as cwsID','user_dtr.leaveType','user_dtr.leave_id','user_dtr.timeIN','user_dtr.timeOUT','user_dtr.hoursWorked','user_dtr.OT_billable','user_dtr.OT_approved','user_dtr.OT_id','user_dtr.UT', 'user_dtr.user_id','user_dtr.updated_at')->
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
        $headers = ['Employee Code', 'Formal Name','Date','Day','Time IN','Time OUT','Hours','ECQ Status', 'OT billable','OT Approved','OT Start','OT End', 'OT hours','OT Reason'];
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
                        //return $ecqStats;

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

                            //13 headers
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

                            // $sheet->cells('A3', function($cells) {

                            //     $cells->setAlignment('right');

                            // });


                            // $sheet->row(2, function($cells) {

                            //     // call cell manipulation methods
                                
                            //     $cells->setFontColor('#ffffff');
                            //     $cells->setFontSize(14);
                            //     $cells->setFontWeight('bold');

                            // });

                           
                            
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

                                foreach ($dData as $key) 
                                {
                                  // -------- ACCESS CODE -------------
                                  $arr[$i] = strtoupper($key->accesscode); $i++;

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

                                  if ( strpos($tin, "SL") !== false || strpos($tin, "VL") !== false || strpos($tin, "RD") !== false || strpos($tin, "LWOP") !== false || strpos($tin, "OBT") !== false || strpos($tin, "ML") !== false || strpos($tin, "PL") !== false || strpos($tin, "No IN") !== false || strpos($tin, "No OUT") !== false || strpos($tin, "N / A") !== false || strpos($tin, "N/A") !== false )
                                  {
                                    $arr[$i] = $tin; $i++;
                                  }
                                  else
                                  {
                                    $arr[$i] = Carbon::parse($tin,'Asia/Manila')->format('h:i:s A'); $i++;
                                  }
                                  



                                  // -------- TIME OUT -------------
                                   $tout = strip_tags($key->timeOUT);

                                  if ( strpos($tout, "SL") !== false || strpos($tout, "VL") !== false || strpos($tout, "RD") !== false || strpos($tout, "LWOP") !== false || strpos($tout, "OBT") !== false || strpos($tout, "ML") !== false || strpos($tout, "PL") !== false || strpos($tout, "No IN") !== false || strpos($tout, "No OUT") !== false  || strpos($tin, "N / A") !== false || strpos($tin, "N/A") !== false)
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
                                     $arr[$i] = strip_tags($key->hoursWorked); $i++;

                                  }else
                                  $arr[$i] = strip_tags($key->hoursWorked); $i++;


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


                                      $arr[$i] = $s; $i++;
                                      $arr[$i] = $e; $i++;

                                      $arr[$i] = $fh; $i++;
                                      $arr[$i] = $r; $i++;
                                      
                                      


                                    }else
                                    {

                                      $deets = User_OT::find($key->OT_id);
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
                                    //$arr[$i] = "-"; $i++;

                                    
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


                            


                      // $lastrow= $excel->getActiveSheet()->getHighestRow(); 
                      // $excel->getActiveSheet()->getStyle('A4:A'.$lastrow)->getAlignment()->setWrapText(true); 
                      // $excel->getActiveSheet()->setBorder('A4:P'.$lastrow, 'thin');

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

       //Timekeeping Trait
      $payrollPeriod = $this->getPayrollPeriod($cutoffStart,$cutoffEnd);
      $paycutoffs = Paycutoff::orderBy('toDate','DESC')->get();

      DB::connection()->disableQueryLog();
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

        $allProgram = DB::table('campaign')->select('id','name','hidden')->where('hidden',null)->
                          where([
                            ['campaign.id', '!=','26'], //wv
                            ['campaign.id', '!=','35'], //ceb

                          ])->orderBy('name')->get();//
        $byTL = collect($allUsers)->groupBy('tlID');
        $allTL = $byTL->keys();
        //return collect($allUsers)->where('campID',7);

        $correct = Carbon::now('GMT+8'); //->timezoneName();

           if($this->user->id !== 564 ) {
              $file = fopen('public/build/rewards.txt', 'a') or die("Unable to open logs");
                fwrite($file, "-------------------\n Viewed Finance_DTRsheets on " . $correct->format('M d h:i A'). " by [". $this->user->id."] ".$this->user->lastname."\n");
                fclose($file);
            } 
        
      

      return view('timekeeping.financeReport',compact('payrollPeriod','paycutoffs','allProgram'));

    }

    public function getValidatedDTRs(Request $request)
    {
      //------ Report type 1= DTR logs | 2= Summary

      ($request->reportType == 'finance') ? $result = $this->fetchLockedDTRs($request->cutoff, $request->program,1) : $result = $this->fetchLockedDTRs($request->cutoff, $request->program,null);

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

        ($user->status_id == 12 || $user->status_id == 14) ? $isParttimer = true : $isParttimer=false;

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
        $hasVLCreditsAlready = DB::table('user_vlcredits')->where('user_id',$user->id)->where('creditYear',date('Y'))->get();
        $hasSLCreditsAlready = DB::table('user_slcredits')->where('user_id',$user->id)->where('creditYear',date('Y'))->get();
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
        $leave1 = Carbon::parse('first day of January '. date('Y'),"Asia/Manila")->format('Y-m-d');
        $leave2 = Carbon::parse('last day of December '.date('Y'),"Asia/Manila")->format('Y-m-d');
        $currentVLbalance ="N/A";
        $updatedVL = false;
        $currentSLbalance ="N/A";
        $updatedSL = false;


        /*if ($lengthOfService >= 6) //do this if only 6mos++
        {*/
          $today= date('m');//today();
          $avail = $user->vlCredits;
          $avail2 = $user->slCredits;

          $vlEarnings = DB::table('user_vlearnings')->where('user_vlearnings.user_id',$user->id)->
                              join('vlupdate','user_vlearnings.vlupdate_id','=', 'vlupdate.id')->
                              select('vlupdate.credits','vlupdate.period')->where('vlupdate.period','>', Carbon::parse(date('Y').'-01-01','Asia/Manila')->format('Y-m-d'))->get();
          $totalVLearned = collect($vlEarnings)->sum('credits');

          $slEarnings = DB::table('user_slearnings')->where('user_slearnings.user_id',$user->id)->
                              join('slupdate','user_slearnings.slupdate_id','=', 'slupdate.id')->
                              select('slupdate.credits','slupdate.period')->where('slupdate.period','>', Carbon::parse(date('Y').'-01-01','Asia/Manila')->format('Y-m-d'))->get();
          $totalSLearned = collect($slEarnings)->sum('credits');

          $approvedVLs = User_VL::where('user_id',$user->id)->where('isApproved',1)->where('leaveStart','>=',$leave1)->where('leaveEnd','<=',$leave2)->get();
          $approvedSLs = User_SL::where('user_id',$user->id)->where('isApproved',1)->where('leaveStart','>=',$leave1)->where('leaveEnd','<=',$leave2)->get();

            /************ for VL ************/
            if (count($avail)>0){
              $vls = $user->vlCredits->sortByDesc('creditYear');

              if($vls->contains('creditYear',date('Y')))
              {
                $updatedVL=true;
                $currentVLbalance= ($vls->first()->beginBalance - $vls->first()->used + $totalVLearned) - $vls->first()->paid;
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

              if($sls->contains('creditYear',date('Y')))
              {
                $updatedSL=true;
                $currentSLbalance= ($sls->first()->beginBalance - $sls->first()->used + $totalSLearned) - $sls->first()->paid;
                                   
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
             //return $currentPeriod;


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
             

                                                 

            if (  count($verifiedDTR) >= count($payrollPeriod)  )//|| ($currentPeriod[0] == $currentPeriod[1])
             {

                $myDTRSheet = $verifiedDTR;
                $paystart = $currentPeriod[0];
                $payend = $currentPeriod[1];
                $ecq = ECQ_Workstatus::where('user_id',$user->id)->get();
                $wfhData = Logs::where('user_id',$user->id)->where('manual',1)->get();//->where('biometrics_id','>=',$startWFH->id)
                return view('timekeeping.myDTRSheet', compact('ecq', 'wfhData', 'fromYr', 'payrollPeriod', 'anApprover','isWorkforce','employeeisBackoffice', 'TLapprover', 'DTRapprovers', 'canChangeSched', 'paycutoffs', 'shifts','shift4x11', 'cutoffID', 'myDTRSheet','camps','user','theImmediateHead', 'immediateHead','cutoff','noWorkSched', 'prevTo','prevFrom','nextTo','nextFrom','paystart','payend','currentVLbalance','currentSLbalance'));
 

             }



             // *************************** end VERIFIED DTR SHEET


             // ---------------------------
             // Determine first if FIXED OR SHIFTING sched
             // and then get WORKSCHED and RD sched
             // ---------------------------
             
             $endp = count($payrollPeriod)-1; 
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
                  $hybridSched_WS_monthly = collect($monthlyScheds)->where('isRD',0);
                  $hybridSched_RD_fixed = collect($user->fixedSchedule)->sortByDesc('schedEffectivity')->
                                          where('isRD',1)->groupBy('schedEffectivity');
                  $hybridSched_RD_monthly = collect($monthlyScheds)->where('isRD',1);

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

             //return (['hybridSched_WS_fixed' => $hybridSched_WS_fixed, 'hybridSched_WS_monthly' => $hybridSched_WS_monthly, 'hybridSched_RD_fixed' => $hybridSched_RD_fixed,'hybridSched_RD_monthly' => $hybridSched_RD_monthly]);

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
                  $schedForToday =  $actualSchedToday->schedForToday;
                  (count($schedForToday) > 0) ? $noWorkSched=false : $noWorkSched=true;
                  $RDsched = $actualSchedToday->RDsched;
                  $isFixedSched =  $actualSchedToday->isFixedSched;
                  $allRD = $actualSchedToday->allRD;

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
                                  'logIN' => $data[0]['logIN'],
                                  'logOUT'=>$data[0]['logOUT'],
                                  'lwopDetails'=>null,
                                  'OTattribute' => $data[0]['OTattribute'],
                                  'payday'=>$payday,
                                  'pendingDTRPin'=> $data[0]['pendingDTRPin'],
                                  'pendingDTRPout' => $data[0]['pendingDTRPout'],
                                  'productionDate'=> date('M d, Y', strtotime($payday)),
                                  'shiftEnd'=>$data[0]['shiftEnd'],
                                  'shiftEnd2'=>$data[0]['shiftEnd'],
                                  'shiftStart'=> $data[0]['shiftStart'],
                                  'shiftStart2'=>  $data[0]['shiftStart'],
                                  'UT'=>$data[0]['UT'],
                                  'usercws'=>$usercws,
                                  'userOT'=>$userOT,
                                  'workedHours'=> $data[0]['workedHours'],
                                   
                                   
                                   ]);

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
                                  

                                  // *********************************************************

                                  //**-- here we check if user is PART TIMER (5hr work ) only
                                  // 12: Part Time 14:Regular Part time

                                  // *********************************************************

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
                                    $shiftEnd = date('h:i A',strtotime($schedForToday['timeEnd']));
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
                                    

                                    





                                    if (empty($userLogOUT[0]['timing']))
                                    {
                                      //** but check mo muna kung may filed leave ba OR HOLIDAY
                                      if($userLogOUT[0]['hasLeave'] || $userLogOUT[0]['hasLWOP'] || $userLogOUT[0]['hasSL'] || $hasHolidayToday)
                                      {
                                        $data = $this->getWorkedHours($user,$userLogIN, $userLogOUT, $schedForToday,$shiftEnd,$payday,$isRDYest,$isParttimer);
                                        $workedHours= $data[0]['workedHours'];//$wh=$data[0]['wh'];
                                        $billableForOT = $data[0]['billableForOT'];
                                        $OTattribute = $data[0]['OTattribute'];
                                        $UT = $data[0]['UT'];
                                        
                                        //$coll->push(['ret workedHours:'=> $data, 'out'=>$userLogOUT]);

                                      }else{
                                            //meaning wala syang OUT talaga
                                          $workedHours= "N/A";
                                          $billableForOT = "-";
                                          $OTattribute = "-";
                                          $UT = "-";
                                      }
                                      

                                    }else{
                                      $data = $this->getWorkedHours($user,$userLogIN, $userLogOUT, $schedForToday,$shiftEnd,$payday,$isRDYest,$isParttimer);
                                      $workedHours= $data[0]['workedHours'];//$wh=$data[0]['wh'];
                                      
                                      $billableForOT = $data[0]['billableForOT'];
                                      $OTattribute = $data[0]['OTattribute'];
                                      $UT = $data[0]['UT'];
                                      
                                      //$coll->push(['ret workedHours:'=> $data, 'out'=>$userLogOUT]);

                                    } 
                                    //$coll->push($data);
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


                                              
                                      

                                  } //--- else not sameDayLog



                                  if(is_null($schedForToday)) {
                                      
                                      $myDTR->push(['isRDToday'=>null,'payday'=>$payday,'biometrics_id'=>$bioForTheDay->id,
                                                    'hasCWS'=>$hasCWS,
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
                                                    'isWFH'=>$isWFH,
                                                    'isOnsite'=>$isOnsite,
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
                                    $actualSchedKahapon = $this->getActualSchedForToday($user,$id,$prevDay->format('Y-m-d'),$bioForYest, $hybridSched,$isFixedSched,$hybridSched_WS_fixed,$hybridSched_WS_monthly, $hybridSched_RD_fixed, $hybridSched_RD_monthly, $workSched, $RDsched, $approvedCWS);

                                    if(count($userLogIN[0]['leave']) > 0  )$hasLeave=true; else $hasLeave=false;

                                    $myDTR->push([
                                      'approvedOT' => $approvedOT,
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
                                      'hasApprovedCWS'=>$hasApprovedCWS,
                                      'hasApprovedOT'=>$hasApprovedOT,
                                      'hasLeave' => $hasLeave, //$userLogIN[0]['hasLeave'],
                                      'hasPendingIN' => $userLogIN[0]['hasPendingDTRP'],
                                      'hasPendingOUT' => $userLogOUT[0]['hasPendingDTRP'],
                                      'isAproblemShift'=>$isAproblemShift,
                                      'isFixedSched'=>$isFixedSched,
                                      'isFlexitime'=>$schedForToday['isFlexitime'],
                                      'isRDToday'=>$isRDToday,  
                                      'isRD'=> 0,
                                      'isWFH'=>$isWFH,
                                      'isOnsite'=>$isOnsite,
                                      'leaveDetails'=>$userLogIN[0]['leave'],
                                      'logIN' => $userLogIN[0]['logTxt'],
                                      'logOUT'=>$userLogOUT[0]['logTxt'],
                                      'lwopDetails'=>$userLogIN[0]['lwop'],
                                      'OTattribute'=> $OTattribute,
                                      'payday'=> $payday,
                                      'pendingDTRPin'=> $userLogIN[0]['pendingDTRP'],
                                      'pendingDTRPout' =>$userLogOUT[0]['pendingDTRP'],
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


                                  } 


                            }//end else WORK DAY
                            

                      }//end else not null BioForTheDay

                       

                }//end if else noWorkSched

                endNoWorkSched: 
                //$noWorkSched = null; //*** we need to reset things

                //$wsch->push(['noWorkSched'=>$noWorkSched]);

                        
               


             }//END foreach payrollPeriod

            //return $myDTR;
            //return $myDTR->where('productionDate','Mar 23, 2020');


            $correct = Carbon::now('GMT+8'); //->timezoneName();

         

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

           $ecq = ECQ_Workstatus::where('user_id',$user->id)->get();
           $wfhData = Logs::where('user_id',$user->id)->where('manual',1)->get();//->where('biometrics_id','>=',$startWFH->id)
                
           
           return view('timekeeping.myDTR', compact('id', 'ecq','allECQ', 'wfhData', 'fromYr', 'entitledForLeaves', 'anApprover', 'TLapprover', 'DTRapprovers', 'canChangeSched', 'paycutoffs', 'shifts','shift4x11', 'partTimes','cutoffID','verifiedDTR', 'myDTR','camps','user','theImmediateHead', 'immediateHead','cutoff','noWorkSched', 'prevTo','prevFrom','nextTo','nextFrom','memo','notedMemo','payrollPeriod','currentVLbalance','currentSLbalance','isWorkforce','isBackoffice'));


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


}
