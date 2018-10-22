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
use OAMPI_Eval\Holiday;
use OAMPI_Eval\HolidayType;




class DTRController extends Controller
{
    protected $user;
   	protected $user_dtr;
    use Traits\TimekeepingTraits;



     public function __construct(User_DTR $user_dtr)
    {
        $this->middleware('auth');
        $this->user_dtr = $user_dtr;
        $this->user =  User::find(Auth::user()->id);
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
          }
        }

        return Redirect::back(); 
       // return response()->json($coll);

      } else{
        return Redirect::back();
      }

    }

    


    public function myDTR()
    {

    	$cutoff = date('M d, Y', strtotime(Cutoff::first()->startingPeriod())). " - " . date('M d, Y',strtotime(Cutoff::first()->endingPeriod()));

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

    	return view('timekeeping.myDTR', compact('myDTR','camps','user','immediateHead', 'cutoff'));
    }


    public function show($id, Request $request )
    {
        DB::connection()->disableQueryLog();
        $collect = new Collection; 
        $coll = new Collection;
        $roles = UserType::find($this->user->userType_id)->roles->pluck('label'); //->where('label','MOVE_EMPLOYEES');
        $canViewOtherDTR =  ($roles->contains('VIEW_OTHER_DTR')) ? '1':'0';
        $canViewTeamDTR =  ($roles->contains('VIEW_SUBORDINATE_DTR')) ? '1':'0';
        $canChangeSched =  ($roles->contains('CHANGE_EMPLOYEE_SCHEDULE')) ? '1':'0';
        $hrDept = Campaign::where('name',"HR")->first();
        $financeDept = Campaign::where('name',"Finance")->first();
        $paycutoffs = Paycutoff::all();

        $user = User::find($id);


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

        
       

        // check if viewing is not an agent, an HR personnel, or the owner, or youre the immediateHead, our you're Program Manager



        //Timekeeping Trait
        $anApprover = $this->checkIfAnApprover($approvers, $this->user);
        
        if ($canViewOtherDTR || $anApprover || $this->user->id == $id 
        || ($theImmediateHead 
        || $this->user->employeeNumber==$leader_L1->employeeNumber
        || $this->user->employeeNumber==$leader_L0->employeeNumber 
        || $this->user->employeeNumber==$leader_PM->employeeNumber ) && $canViewTeamDTR )  //($this->user->userType_id == 1 || $this->user->userType_id == 2)
        {     
          if(empty($request->from) && empty($request->to) )
          {

            $currPeriod =  Cutoff::first()->getCurrentPeriod();
            $currentPeriod = explode('_', $currPeriod);
            $cutoffStart = new Carbon(Cutoff::first()->startingPeriod());
            $cutoffEnd = new Carbon(Cutoff::first()->endingPeriod());
            //$cutoffID = Paycutoff::where('fromDate',$currentPeriod[0])->first()->id;

            $cID = Paycutoff::where('fromDate',$currentPeriod[0])->get();
            if ($cID->isEmpty())
            {
              //return $cID;
              $newPC = new Paycutoff;
              $newPC->fromDate = $cutoffStart;
              $newPC->toDate = $cutoffEnd;
              $newPC->save();

              $cutoffID = $newPC->id;

              

            } else
            {
              $cutoffID = $cID->first()->id;

            }

                
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
              $prevF= Carbon::createFromDate(null,date('m',strtotime($currentPeriod[0])),Cutoff::first()->second+1);
              $prevFrom = $prevF->subMonth()->format('Y-m-d');
              
              $prevTo = Carbon::parse($currentPeriod[0])->subDay()->format('Y-m-d');
              $nextFrom = Carbon::parse($currentPeriod[1])->addDay()->format('Y-m-d');
              $nextTo = Carbon::createFromDate(null,date('m',strtotime($currentPeriod[1]))+1,Cutoff::first()->first)->format('Y-m-d');
              
            }
            else
            {
              $m = date('m',strtotime($currentPeriod[0]));
              
              $prevFrom = Carbon::createFromDate(null,$m,Cutoff::first()->first+1)->format('Y-m-d');
              $prevTo = Carbon::parse($currentPeriod[0])->subDay()->format('Y-m-d');
              $nextFrom = Carbon::parse($currentPeriod[1])->addDay()->format('Y-m-d');
              $nextTo = Carbon::createFromDate(null,date('m',strtotime($currentPeriod[1])),Cutoff::first()->second)->format('Y-m-d');
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
             $shiftStart2=null;$shiftEnd2=null;
             $hybridSched = null;


             // ---------------------------
             // Determine first if FIXED OR SHIFTING sched
             // and then get WORKSCHED and RD sched
             // ---------------------------
              
             $noWorkSched = true;
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

                }else //hybrid sya
                {

                  $hybridSched = true;
                  $noWorkSched = false;
                  //$workdays = new Collection;

                  $hybridSched_WS_fixed = $user->fixedSchedule->where('isRD',0)->sortByDesc('updated_at');
                  $hybridSched_WS_monthly = MonthlySchedules::where('user_id',$id)->where('productionDate','>=', $currentPeriod[0])->where('productionDate','<=',$currentPeriod[1])->where('isRD',0)->orderBy('updated_at','DESC')->get(); 
                  $hybridSched_RD_fixed = $user->fixedSchedule->where('isRD',1);
                  $hybridSched_RD_monthly = MonthlySchedules::where('user_id',$id)->where('productionDate','>=', $currentPeriod[0])->where('productionDate','<=',$currentPeriod[1])->where('isRD',1)->orderBy('updated_at','DESC')->get();

                  /*--- and then compare which is the latest of those 2 scheds --*/


                }
                



             } else
             {
                if (count($user->fixedSchedule) > 0)
                {
                    //merong fixed sched
                    $workSched = $user->fixedSchedule->where('isRD',0);
                    $RDsched = $user->fixedSchedule->where('isRD',1)->pluck('workday');
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
            

             $shifts = $this->generateShifts('12H');
              
             foreach ($payrollPeriod as $payday) 
             {

              $hasCWS = false; $hasApprovedCWS=false; $hasOT=false; $hasApprovedOT=false;

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

                         //**************************************************************
                          //       this is where we check the hybrids and classify accordingly
                          //**************************************************************

                        if ($hybridSched)
                        {
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

                            if (count($check_monthly_WS) > 0)
                            {
                              if( $check_monthly_WS->first()->created_at > $check_fixed_WS->first()->created_at ) //mas bago si Monthly
                              {
                                $workSched = MonthlySchedules::where('user_id',$id)->where('productionDate','>=', $currentPeriod[0])->where('productionDate','<=',$currentPeriod[1])->where('isRD',0)->get(); 
                                $RDsched = MonthlySchedules::where('user_id',$id)->where('productionDate','>=', $currentPeriod[0])->where('productionDate','<=',$currentPeriod[1])->where('isRD',1)->get();  //$monthlySched->where('isRD',1)->all();
                                $isFixedSched = false;
                                $noWorkSched = false;

                              }
                              else
                              {
                                //check mo muna validity nung WS na fixed. If not effective, then NO SCHED

                                 if ((Carbon::parse($check_fixed_WS->first()->schedEffectivity)->startOfDay() <= $carbonPayday->startOfDay()) || $check_fixed_WS->first()->schedEffectivity == null)
                                 {

                                  $workSched = $user->fixedSchedule->where('isRD',0);
                                  $RDsched = $user->fixedSchedule->where('isRD',1)->pluck('workday');
                                  $isFixedSched =true;
                                  $noWorkSched = false;

                                 }
                                 else
                                 {
                                  $noWorkSched = true;$workSched = null;$RDsched = null;$isFixedSched = false; $hasCWS=false;
                                 }


                              }

                            }
                            else //fixed sched na talaga sya
                            {
                              //check mo muna validity nung WS na fixed. If not effective, then NO SCHED



                               if ((Carbon::parse($check_fixed_WS->first()->schedEffectivity)->startOfDay() <= $carbonPayday->startOfDay()) || $check_fixed_WS->first()->schedEffectivity == null)
                               {


                                $workSched = $user->fixedSchedule->where('isRD',0);
                                $RDsched = $user->fixedSchedule->where('isRD',1)->pluck('workday');
                                $isFixedSched =true;
                                $noWorkSched = false;

                               }
                               else
                               {
                                $noWorkSched = true;$workSched = null;$RDsched = null;$isFixedSched = false; $hasCWS=false;

                                 
                               }

                            }

                          }else //baka RD
                          {
                            $check_fixed_RD = $hybridSched_RD_fixed->where('workday',$dayToday)->sortByDesc('created_at');

                            if (count($check_fixed_RD) > 0) //check mo muna vs monthly sched
                            {
                              $check_monthly_RD = $hybridSched_RD_monthly->where('productionDate',$payday)->sortByDesc('created_at');

                              if (count($check_monthly_RD) > 0) //compare it with fixed RD
                              {
                                if( $check_monthly_RD->first()->created_at > $check_fixed_RD->first()->created_at ) //mas bago si Monthly
                                {
                                  $workSched = MonthlySchedules::where('user_id',$id)->where('productionDate','>=', $currentPeriod[0])->where('productionDate','<=',$currentPeriod[1])->where('isRD',0)->get(); 
                                  $RDsched = MonthlySchedules::where('user_id',$id)->where('productionDate','>=', $currentPeriod[0])->where('productionDate','<=',$currentPeriod[1])->where('isRD',1)->get();  //$monthlySched->where('isRD',1)->all();
                                  $isFixedSched = false;
                                  $noWorkSched = false;

                                }
                                else //FIXED RD SYA
                                {
                                  //check mo muna validity nung WS na fixed. If not effective, then NO SCHED

                                   if ((Carbon::parse($check_fixed_RD->first()->schedEffectivity)->startOfDay() <= $carbonPayday->startOfDay()) || $check_fixed_RD->first()->schedEffectivity == null)
                                   {

                                    $workSched = $user->fixedSchedule->where('isRD',0);
                                    $RDsched = $user->fixedSchedule->where('isRD',1)->pluck('workday');
                                    $isFixedSched =true;
                                    $noWorkSched = false;

                                   }
                                   else
                                   {
                                    $noWorkSched = true;$workSched = null;$RDsched = null;$isFixedSched = false; $hasCWS=false;
                                   }

                                }

                              }
                              else //meaning RD fixed na sya
                              {
                                //check mo muna validity nung WS na fixed. If not effective, then NO SCHED

                                   if ((Carbon::parse($check_fixed_RD->first()->schedEffectivity)->startOfDay() <= $carbonPayday->startOfDay()) || $check_fixed_RD->first()->schedEffectivity == null)
                                   {

                                    $workSched = $user->fixedSchedule->where('isRD',0);
                                    $RDsched = $user->fixedSchedule->where('isRD',1)->pluck('workday');
                                    $isFixedSched =true;
                                    $noWorkSched = false;

                                   }
                                   else
                                   {
                                    $noWorkSched = true;$workSched = null;$RDsched = null;$isFixedSched = false; $hasCWS=false;
                                   }

                              }

                            }
                            else //wala from both fixed sched, meaning MONTHLY SCHED SYA
                            {

                              $workSched = MonthlySchedules::where('user_id',$id)->where('productionDate','>=', $currentPeriod[0])->where('productionDate','<=',$currentPeriod[1])->where('isRD',0)->get(); 
                              $RDsched = MonthlySchedules::where('user_id',$id)->where('productionDate','>=', $currentPeriod[0])->where('productionDate','<=',$currentPeriod[1])->where('isRD',1)->get();  //$monthlySched->where('isRD',1)->all();
                              $isFixedSched = false;
                              $noWorkSched = false;

                            }

                          }

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

                            //$coll->push(['fromWDtoRD'=>$fromWDtoRD]);

                            
                            if ($noWorkSched) {  


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
                                            //'usercws'=>$usercws->sortByDesc('updated_at')->first(),
                                            'usercws'=>$usercws->sortByDesc('updated_at'),
                                            'userOT'=>$userOT,
                                            'hasOT'=>$hasOT,
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
                              if (count($fromRDtoWD)>0 ) $isRDToday=false; 
                              else if ( count($fromWDtoRD) > 0 ) $isRDToday=true; 
                              else {
                                if($isFixedSched)
                                  $isRDToday = $RDsched->contains($numDay); 
                                  else
                                  {
                                    $rd = $monthlySched->where('isRD',1)->where('productionDate',$payday)->all(); 

                                    if (count($rd)<= 0 ) 
                                      $isRDToday=false; else $isRDToday=true;

                                    //$coll->push(['rd'=>$rd, 'isRDToday'=>$isRDToday]); 
                                  }

                              }
                            
                           

                            }

                            //$coll->push(['isRDToday'=>$isRDToday, 'fromWDtoRD'=>$fromWDtoRD,'fromRDtoWD'=>$fromRDtoWD]);
                             $coll->push(['payday'=>$payday, 'isFixedSched'=>$isFixedSched]);

                              
                              
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
                                          $myDTR->push(['payday'=>$payday,
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

                                         $myDTR->push(['payday'=>$payday,
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


                                  if ($isFixedSched)
                                  {
                                    if ($hasApprovedCWS)
                                    {
                                      if ( count($workSched->where('workday',$numDay)->all()) > 0 )
                                      {
                                        $schedForToday = array('timeStart'=>$approvedCWS->first()->timeStart, 
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
                                      $schedForToday = $workSched->where('workday',$numDay)->first();
                                  }
                                  else
                                  {
                                      if ($hasApprovedCWS)
                                      {
                                        //--- hack for flexitime
                                        //$coll->push(["forFlexiHack"=>$workSched]);
                                        if ( count($workSched->where('productionDate',$payday)->all()) > 0 )
                                        {
                                          $schedForToday = array('timeStart'=>$approvedCWS->first()->timeStart, 
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
                                        
                                       

                                      } else 
                                        $schedForToday = $workSched->where('productionDate',$payday)->first();

                                  }//endelse if fixedSched

                                  
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

                                  

                                  if ( $s >= Carbon::parse($payday." 00:00:00","Asia/Manila") &&  $s <=  Carbon::parse($payday." 05:00:00","Asia/Manila") )
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

                                  if ($shiftStart >= date('h:i A', strtotime("06:00:00")) && $shiftStart <= date('h:i A', strtotime("14:59:00")))
                                  {
                                    $sameDayLog = true; 

                                  } else{
                                    $sameDayLog = false;

                                  }

                                  $coll->push(['isAproblemShift'=>$isAproblemShift,'s'=>$s, 'schedForToday'=>$schedForToday, 'sameDayLog'=>$sameDayLog,
                                    //'shiftStart'=>Carbon::parse($payday." ".$shiftStart,"Asia/Manila"), 
                                    'range'=>Carbon::parse($payday." 00:00:00","Asia/Manila") . " to ". date('h:i A', strtotime('05:00:00')),
                                    ]);

                                  if ($sameDayLog)
                                  {
                                    

                                    $userLogIN = $this->getLogDetails('WORK', $id, $bioForTheDay->id, 1, $schedForToday, $UT,$problemArea);
                                    $userLogOUT = $this->getLogDetails('WORK', $id, $bioForTheDay->id, 2, $schedForToday,0,$problemArea); //$userLogIN[0]['UT']

                                    


                                    $data = $this->getWorkedHours($user->id,$userLogIN, $userLogOUT, $schedForToday,$shiftEnd,$payday);
                                    //$coll->push(['payday'=>$payday, 'userLogIN'=>$userLogIN, 'userLogOUT'=>$userLogOUT]);
                                    $coll->push(['workedHours:'=> "Workday sameDayLog - LOG IN (WH) "]);

                                   
                                      $workedHours=  $data[0]['workedHours'];
                                      $billableForOT = $data[0]['billableForOT'];
                                      $OTattribute = $data[0]['OTattribute'];

                                

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
                                          $rd = $monthlySched->where('isRD',1)->where('productionDate',$prevDay->format('Y-m-d'))->first();  
                                          if (empty($rd)) 
                                            $isRDYest=false; else $isRDYest=true;
                                        }

                                       

                                      /*-------------------------------------------
                                          Problem shifts: 12MN-5am
                                      ---------------------------------------------*/
                                     
                                        $userLogIN = $this->getLogDetails('WORK', $id, $bioForTheDay->id, 1, $schedForToday, $UT, $problemArea);
                                        $coll->push(['datafrom'=>"else NOT Problem shift",'data IN'=>$userLogIN ]);
                                      //}

                                      

                                     //********** LOG OUT ***************

                                            if ($isAproblemShift)
                                            {

                                              if(empty($bioForTom))
                                                $userLogOUT[0]= array('logTxt'=> "No Data", 
                                                                      'UT'=>0,'logs'=>null,'dtrpIN'=>null,'dtrpIN_id'=>null, 'dtrpOUT'=>null,'dtrpOUT_id'=>null);
                                              else
                                              $userLogOUT = $this->getLogDetails('WORK', $id, $bioForTom->id, 2, $schedForToday,0, $problemArea);
                                              $coll->push(['datafrom'=>"else  Problem shift",'data OUT'=>$userLogOUT ]);
                                            }
                                           
                                            else
                                            { 
                                              if(empty($bioForTom))
                                                $userLogOUT[0]= array('logTxt'=> "No Data", 
                                                                      'UT'=>0,'logs'=>null,'dtrpIN'=>null,'dtrpIN_id'=>null, 'dtrpOUT'=>null,'dtrpOUT_id'=>null);
                                              else
                                              {
                                                 
                                                   $userLogOUT = $this->getLogDetails('WORK', $id, $bioForTheDay->id, 2, $schedForToday,0, $problemArea);
                                                    $coll->push(['datafrom'=>"Normal out",'data OUT'=>$userLogOUT ]);
                                                
                                                   

                                              }
                                            }

                                              if($isRDYest || $isAproblemShift || !$sameDayLog)
                                              {
                                                $data = $this->getComplicatedWorkedHours($user->id,$userLogIN, $userLogOUT, $schedForToday,$shiftEnd,$isRDYest,$payday);
                                                $coll->push(['workedHours'=>"(isRDYest || isAproblemShift || !sameDayLog) [CWH]"]);
                                              }
                                              else
                                                {
                                                  $data = $this->getWorkedHours($user->id,$userLogIN, $userLogOUT, $schedForToday,$shiftEnd, $payday);
                                                 $coll->push(['datafrom'=>"(ELSE isRDYest || isAproblemShift || !sameDayLog) [WH]"]);
                                                }
                                              

                                              $workedHours=$data[0]['workedHours'];
                                              $billableForOT = $data[0]['billableForOT'];
                                              $OTattribute = $data[0]['OTattribute'];
                                              $UT = $data[0]['UT'];

                                              //$coll->push(['sched'=>$schedForToday]);

                                             

                                      

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

                                  } else{
                                    $myDTR->push(['isAproblemShift'=>$isAproblemShift, 'payday'=> $payday,
                                        'biometrics_id'=>$bioForTheDay->id,
                                        'hasCWS'=>$hasCWS,
                                        //'usercws'=>$usercws->sortByDesc('updated_at')->first(),
                                        'usercws'=>$usercws,
                                        'userOT'=>$userOT,
                                        'hasOT'=>$hasOT,
                                        'hasApprovedOT'=>$hasApprovedOT,
                                        'hasPendingIN' => $userLogIN[0]['hasPendingDTRP'],
                                         'pendingDTRPin'=> $userLogIN[0]['pendingDTRP'],
                                         'hasPendingOUT' =>null, //$userLogOUT[0]['hasPendingDTRP'],
                                         'pendingDTRPout' =>null, //$userLogOUT[0]['pendingDTRP'],
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
                                       'wholeOUT' =>$userLogOUT]);


                                  } 


                            }//end else WORK DAY

                            

                             


                      }//end else not null BioForTheDay

                       

                }//end if else noWorkSched

                endNoWorkSched:
                $noWorkSched = null; //*** we need to reset things

                 
                 
     

             }//END foreach payrollPeriod

             //return $coll;
             $TLapprover = $this->getTLapprover($user->id, $this->user->id);
            // $coll->push(['anApprover'=>$anApprover, 'TLapprover'=>$TLapprover]);
           //return $myDTR;
           return view('timekeeping.myDTR', compact('anApprover', 'TLapprover', 'DTRapprovers', 'canChangeSched', 'paycutoffs', 'shifts','cutoffID', 'myDTR','camps','user','theImmediateHead', 'immediateHead','cutoff','noWorkSched', 'prevTo','prevFrom','nextTo','nextFrom'));


        } else return view('access-denied');

    }


}
