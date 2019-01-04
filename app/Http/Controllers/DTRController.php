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
    public function processSheet($id, Request $request)
    {
      //$user = User::find($id);
      $dtrSheet = $request->dtrsheet;
      $coll = new Collection;

      foreach ($dtrSheet as $d) {
        $dtr = new User_DTR;
        $dtr->user_id = $id;
        $dtr->biometrics_id = $d['id'];
        $dtr->productionDate = Carbon::parse($d['productionDate'],"Asia/Manila")->format('Y-m-d');
        $dtr->workshift = $d['workshift'];
        $dtr->timeIN = $d['timeIN'];
        $dtr->timeOUT = $d['timeOUT'];
        $dtr->hoursWorked = $d['hoursWorked'];
        $dtr->OT_billable = $d['OT_billable'];
        $dtr->OT_approved = $d['OT_approved'];
        $dtr->UT = $d['UT'];
        $dtr->save();
        $coll->push($dtr);
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
        $notification->type = 14; //UNLOCK DTR
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


        return response()->json(['success'=>'1', 'message'=>"DTR Unlock request sent for approval."]);


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

  



    public function show($id, Request $request )
    {
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
        $hrDept = Campaign::where('name',"HR")->first();
        $financeDept = Campaign::where('name',"Finance")->first();
        $paycutoffs = Paycutoff::all();

        

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

        
       

        // check if viewing is not an agent, an HR personnel, or the owner, or youre the immediateHead, our you're Program Manager



        //Timekeeping Trait
        $anApprover = $this->checkIfAnApprover($approvers, $this->user);
        $fromYr = Carbon::parse($user->dateHired)->addMonths(6)->format('Y');
        
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
            

            $cutoffStart = new Carbon($currentPeriod[0],'Asia/Manila'); //(Cutoff::first()->startingPeriod());
            $cutoffEnd = new Carbon($currentPeriod[1],'Asia/Manila'); //(Cutoff::first()->endingPeriod());
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
              //$prevF= Carbon::createFromDate(null,date('m',strtotime($currentPeriod[0])),Cutoff::first()->second+1);
              $prevF= Carbon::createFromDate(date('Y',strtotime($currentPeriod[0])),date('m',strtotime($currentPeriod[0])),Cutoff::first()->second+1);
              $prevFrom = $prevF->subMonth()->format('Y-m-d');
              
              $prevTo = Carbon::parse($currentPeriod[0])->subDay()->format('Y-m-d');
              $nextFrom = Carbon::parse($currentPeriod[1])->addDay()->format('Y-m-d');
              $nextTo = Carbon::createFromDate(date('Y',strtotime($currentPeriod[1])),date('m',strtotime($currentPeriod[1]))+1,Cutoff::first()->first)->format('Y-m-d');
              //$nextTo = Carbon::createFromDate(null,date('m',strtotime($currentPeriod[1]))+1,Cutoff::first()->first)->format('Y-m-d');
              
            }
            else
            {
              $m = date('m',strtotime($currentPeriod[0]));
              $y = date('Y',strtotime($currentPeriod[0]));
              
              //$prevFrom = Carbon::createFromDate(null,$m,Cutoff::first()->first+1)->format('Y-m-d');
              $prevFrom = Carbon::createFromDate($y,$m,Cutoff::first()->first+1)->format('Y-m-d');
              $prevTo = Carbon::parse($currentPeriod[0])->subDay()->format('Y-m-d');
              $nextFrom = Carbon::parse($currentPeriod[1])->addDay()->format('Y-m-d');
              $nextTo = Carbon::createFromDate(date('Y',strtotime($currentPeriod[1])),date('m',strtotime($currentPeriod[1])),Cutoff::first()->second)->format('Y-m-d');
              //$nextTo = Carbon::createFromDate(null,date('m',strtotime($currentPeriod[1])),Cutoff::first()->second)->format('Y-m-d');
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
             
             $alreadyVerified = User_DTR::where('user_id',$user->id)->where('productionDate',$payrollPeriod[0])->get();
             if (count($alreadyVerified)>0){

              $myDTRSheet = new Collection;

              foreach ($payrollPeriod as $key) {
                $mDsh = User_DTR::where('user_id',$user->id)->where('productionDate',$key)->orderBy('created_at','DESC')->get();

                if (count($mDsh)>0){
                  $myDTRSheet->push($mDsh->first());

                }
              }

              


              return view('timekeeping.myDTRSheet', compact('fromYr', 'payrollPeriod', 'anApprover', 'TLapprover', 'DTRapprovers', 'canChangeSched', 'paycutoffs', 'shifts','cutoffID', 'myDTRSheet','camps','user','theImmediateHead', 'immediateHead','cutoff','noWorkSched', 'prevTo','prevFrom','nextTo','nextFrom'));

             }
             

           // *************************** VERIFIED DTR SHEET






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



                          $coll2->push($payday);

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
                            // FixedSchedules::where('user_id',$user->id)->where('workday',$dayToday)->orderBy('created_at','DESC')->get();
                            // $hybridSched_WS_fixed->where('workday',$dayToday)->sortByDesc('created_at');

                            if (count($check_fixed_WS) > 0) //if may fixed WS, check mo kung ano mas updated vs monthly sched
                            {
                              $check_monthly_WS = $hybridSched_WS_monthly->where('productionDate', $payday)->sortByDesc('created_at');
                              //$coll->push(['check_monthly_WS'=>$check_monthly_WS]);

                              if (count($check_monthly_WS) > 0)// if may monthly, compare it vs fixed
                              {
                                if( $check_monthly_WS->first()->created_at > $check_fixed_WS->first()->created_at  ) //mas bago si Monthly
                                {

                                  $workSched = $hybridSched_WS_monthly;
                                  $RDsched = $hybridSched_RD_monthly;
                                  $isFixedSched = false;
                                  $noWorkSched =false;
                                  
                                  // $collec = $this->getHybrid_MonthlyWS($id, $currentPeriod);
                                  // $workSched =$collec[0]['workSched']; $RDsched = $collec[0]['RDsched'];$isFixedSched = $collec[0]['isFixedSched'];
                                  // $noWorkSched = $collec[0]['noWorkSched'];


                                }
                                else //check mo muna validity nung WS na fixed. If no effectivity, then NO SCHED
                                {
                                  if ((Carbon::parse($check_fixed_WS->first()->schedEffectivity)->startOfDay() <= $carbonPayday->startOfDay()) || $check_fixed_WS->first()->schedEffectivity == null)
                                  {
                                    $workSched = $hybridSched_WS_fixed;
                                    $RDsched = $hybridSched_RD_fixed;
                                    $isFixedSched = true;
                                    $noWorkSched = false;
                                    // $collec = $this->getHybrid_FixedWS($user);
                                    // $workSched = $collec[0]['workSched']; $RDsched = $collec[0]['RDsched']; $isFixedSched =$collec[0]['isFixedSched'];
                                    // $noWorkSched = $collec[0]['noWorkSched'];
                                    
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
                                  if ($check_monthly_RD->first()->created_at > $check_fixed_WS->first()->created_at) //mas updated yung RD so di sya WS
                                  {
                                    $workSched = $hybridSched_WS_monthly;
                                    $RDsched =  $hybridSched_RD_monthly;
                                    $isFixedSched = false;
                                    $noWorkSched = false;
                                    // $collec = $this->getHybrid_MonthlyWS($id, $currentPeriod);
                                    // $workSched =$collec[0]['workSched']; $RDsched = $collec[0]['RDsched'];$isFixedSched = $collec[0]['isFixedSched'];
                                    // $noWorkSched = $collec[0]['noWorkSched'];

                                  } else
                                  {
                                    //check mo muna validity nung WS na fixed. If not effective, then NO SCHED

                                     if ((Carbon::parse($check_fixed_WS->first()->schedEffectivity)->startOfDay() <= $carbonPayday->startOfDay()) || $check_fixed_WS->first()->schedEffectivity == null)
                                     {

                                      $workSched = $hybridSched_WS_fixed;
                                      $RDsched = $hybridSched_RD_fixed;
                                      $isFixedSched = true;
                                      $noWorkSched = false;

                                      // $workSched = $user->fixedSchedule->where('isRD',0);
                                      // $RDsched = $user->fixedSchedule->where('isRD',1)->pluck('workday');
                                      // $isFixedSched =true;
                                      // $noWorkSched = false;
                                      

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
                                  if( $check_monthly_RD->first()->created_at > $check_fixed_RD->first()->created_at ) //mas bago si Monthly
                                  {
                                    $workSched = $hybridSched_WS_monthly;
                                    $RDsched = $hybridSched_RD_monthly;
                                    $isFixedSched = false;
                                    $noWorkSched = false;
                                    // $collec = $this->getHybrid_MonthlyWS($id, $currentPeriod);
                                    // $workSched =$collec[0]['workSched']; $RDsched = $collec[0]['RDsched'];$isFixedSched = $collec[0]['isFixedSched'];
                                    // $noWorkSched = $collec[0]['noWorkSched'];

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

                                      // $collec = $this->getHybrid_FixedWS($user);
                                      // $workSched = $collec[0]['workSched']; $RDsched = $collec[0]['RDsched']; $isFixedSched =$collec[0]['isFixedSched'];
                                      // $noWorkSched = $collec[0]['noWorkSched'];

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
                                    if($check_monthly_WS->first()->created_at > $check_fixed_RD->first()->created_at  )
                                    {
                                      $workSched = $hybridSched_WS_monthly;
                                      $RDsched = $hybridSched_RD_monthly;
                                      $isFixedSched = false;
                                      $noWorkSched =false;
                                      // $collec = $this->getHybrid_MonthlyWS($id, $currentPeriod);
                                      // $workSched =$collec[0]['workSched']; $RDsched = $collec[0]['RDsched'];$isFixedSched = $collec[0]['isFixedSched'];
                                      // $noWorkSched = $collec[0]['noWorkSched'];
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

                                          // $collec = $this->getHybrid_FixedWS($user);
                                          // $workSched = $collec[0]['workSched']; $RDsched = $collec[0]['RDsched']; $isFixedSched =$collec[0]['isFixedSched'];
                                          // $noWorkSched = $collec[0]['noWorkSched'];

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

                                      // $collec = $this->getHybrid_FixedWS($user);
                                      // $workSched = $collec[0]['workSched']; $RDsched = $collec[0]['RDsched']; $isFixedSched =$collec[0]['isFixedSched'];
                                      // $noWorkSched = $collec[0]['noWorkSched'];

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
                                  if($isFixedSched) {$isRDToday = $RDsched->contains('workday',$numDay); 
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

                                    //if ($shiftStart >= date('h:i A', strtotime("06:00:00")) && $shiftStart <= date('h:i A', strtotime("14:59:00")))
                                    $ss = Carbon::parse($payday." ".$shiftStart,"Asia/Manila");
                                    $sixam = Carbon::parse($payday." 04:00:00","Asia/Manila");
                                    $threepm = Carbon::parse($payday." 14:59:00","Asia/Manila");
                                    if ($ss >= $sixam && $ss <= $threepm )
                                    {
                                      $sameDayLog = true; 

                                    } else{
                                      $sameDayLog = false;

                                    }

                                    //$coll->push(['isAproblemShift'=>$isAproblemShift,'s'=>$s, 'schedForToday'=>$schedForToday, 'sameDayLog'=>$sameDayLog, 'shiftStart'=>$shiftStart,'range'=>Carbon::parse($payday." 00:00:00","Asia/Manila") . " to ". date('h:i A', strtotime('05:00:00')),]);

                                    if ($sameDayLog)
                                    {
                                      

                                      $userLogIN = $this->getLogDetails('WORK', $id, $bioForTheDay->id, 1, $schedForToday, $UT,$problemArea);
                                      $userLogOUT = $this->getLogDetails('WORK', $id, $bioForTheDay->id, 2, $schedForToday,0,$problemArea);
                                      $coll->push(['IN'=>$userLogIN, 'OUT'=>$userLogOUT]); //$userLogIN[0]['UT']

                                      


                                      $data = $this->getWorkedHours($user->id,$userLogIN, $userLogOUT, $schedForToday,$shiftEnd,$payday);
                                      //$coll->push(['payday'=>$payday, 'userLogIN'=>$userLogIN, 'userLogOUT'=>$userLogOUT]);
                                      $coll->push(['ret workedHours:'=> $data, 'out'=>$userLogOUT]);

                                     
                                        $workedHours= $data[0]['workedHours'];
                                        $billableForOT = $data[0]['billableForOT'];
                                        $OTattribute = $data[0]['OTattribute'];
                                        $UT = $data[0]['UT'];

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
                                       
                                          $userLogIN = $this->getLogDetails('WORK', $id, $bioForTheDay->id, 1, $schedForToday, $UT, $problemArea);
                                          $coll->push(['datafrom'=>"else NOT Problem shift",'data IN'=>$userLogIN ]);
                                        //}

                                        

                                       //********** LOG OUT ***************

                                              if ($isAproblemShift)
                                              {

                                                if(empty($bioForTom))
                                                  $userLogOUT[0]= array('logTxt'=> "No Data", 
                                                                        'UT'=>0,'logs'=>null,'dtrpIN'=>null,'dtrpIN_id'=>null, 'dtrpOUT'=>null,'dtrpOUT_id'=>null, 'hasPendingDTRP'=>null,'pendingDTRP'=>null);
                                                else
                                                $userLogOUT = $this->getLogDetails('WORK', $id, $bioForTom->id, 2, $schedForToday,0, $problemArea);
                                                $coll->push(['datafrom'=>"else  Problem shift",'data OUT'=>$userLogOUT ]);
                                              }
                                             
                                              else
                                              { 
                                                if(empty($bioForTom))
                                                  $userLogOUT[0]= array('logTxt'=> "No Data", 
                                                                        'UT'=>0,'logs'=>null,'dtrpIN'=>null,'dtrpIN_id'=>null, 'dtrpOUT'=>null,'dtrpOUT_id'=>null, 'hasPendingDTRP'=>null,'pendingDTRP'=>null);
                                                else
                                                {
                                                   
                                                     $userLogOUT = $this->getLogDetails('WORK', $id, $bioForTheDay->id, 2, $schedForToday,0, $problemArea);
                                                      $coll->push(['datafrom'=>"Normal out",'data OUT'=>$userLogOUT ]);
                                                  
                                                     

                                                }

                                                $coll->push(['IN'=>$userLogIN, 'OUT'=>$userLogOUT]);

                                              }

                                                if($isRDYest || $isAproblemShift || !$sameDayLog)
                                                {
                                                  $data = $this->getComplicatedWorkedHours($user->id,$userLogIN, $userLogOUT, $schedForToday,$shiftEnd,$isRDYest,$payday);
                                                  $coll->push(['workedHours'=>"(isRDYest || isAproblemShift || !sameDayLog)", 'checkLate'=>$data[0]['checkLate'],'biometricsID'=>$bioForTheDay->id]);
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

                                                //$coll->push(['LWOP'=>$data]);

                                               

                                        

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

                                         // 'VL'=>$VLs, 'LWOP'=>$LWOPs

                                       ]);


                                    } 


                              }//end else WORK DAY

                              //$coll2->push(['isRDToday'=>$isRDToday, 'fromRDtoWD'=>$fromRDtoWD,'fromWDtoRD'=>$fromWDtoRD,'schedForToday'=>$schedForToday,  'workSched'=> $workSched, 'RDsched'=>$RDsched]);

                              

                               


                        }//end else not null BioForTheDay

                         

                  }//end if else noWorkSched

                  endNoWorkSched:
                  //$noWorkSched = null; //*** we need to reset things
             }//END foreach payrollPeriod

            
             
            // $coll->push(['anApprover'=>$anApprover, 'TLapprover'=>$TLapprover]);
           //return $coll;

           //return response()->json(['coll'=> $coll]);

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



           return view('timekeeping.myDTR', compact('fromYr', 'entitledForLeaves', 'anApprover', 'TLapprover', 'DTRapprovers', 'canChangeSched', 'paycutoffs', 'shifts','cutoffID', 'myDTR','camps','user','theImmediateHead', 'immediateHead','cutoff','noWorkSched', 'prevTo','prevFrom','nextTo','nextFrom','memo','notedMemo'));


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
        $theNotif = Notification::where('relatedModelID', $theDTR->first()->id)->where('type',14)->get();

        //return $theNotif;

        //then remove those sent notifs to the approvers since it has already been approved/denied
        if (count($theNotif) > 0 )
        {

            DB::table('user_Notification')->where('notification_id','=',$theNotif->first()->id)->delete();
            $unotif = $this->notifySender($theDTR->first(),$theNotif->first(),14);
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

         $dtr = User_DTR::where('user_id',$user->id)->where('productionDate',$key)->get();
         
         if (count($dtr)>0){ $dtr->first()->delete();} 
      }

      



      return response()->json(['success'=>'1', 'message'=>"DTR Unlocked."]);

     



    }


}
