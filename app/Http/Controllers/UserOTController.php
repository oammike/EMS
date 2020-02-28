<?php

namespace OAMPI_Eval\Http\Controllers;

use Carbon\Carbon;
use Excel;
use \PDF;
use \Mail;
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
use OAMPI_Eval\Holiday;
use OAMPI_Eval\Cutoff;
use OAMPI_Eval\Biometrics;
use OAMPI_Eval\Biometrics_Uploader;
use OAMPI_Eval\Logs;
use OAMPI_Eval\LogType;
use OAMPI_Eval\TempUpload;
use OAMPI_Eval\User_DTR;
use OAMPI_Eval\MonthlySchedules;
use OAMPI_Eval\FixedSchedules;
use OAMPI_Eval\Paycutoff;
use OAMPI_Eval\User_CWS;
use OAMPI_Eval\Notification;
use OAMPI_Eval\User_Notification;
use OAMPI_Eval\User_OT;
use OAMPI_Eval\User_VLcredits;

class UserOTController extends Controller
{
    protected $user;
    protected $user_ot;
    use Traits\TimekeepingTraits;
    use Traits\UserTraits;



     public function __construct(User_OT $user_ot)
    {
        $this->middleware('auth');
        $this->user_ot = $user_ot;
        $this->user =  User::find(Auth::user()->id);
    }


    public function deleteOT($id, Request $request)
    {
        $theOT = User_OT::find($id);

        //find all notifications related to that OT
        $theNotif = Notification::where('relatedModelID', $theOT->id)->where('type',7)->get();

        if (count($theNotif) > 0){
            DB::table('user_Notification')->where('notification_id','=',$theNotif->first()->id)->delete();
            
        }
        $theOT->delete();

        if ($request->redirect == '1')
            return redirect()->back();
        else return response()->json(['success'=>"ok"]);


    }

    public function create(Request $request)
    {

        if(is_null($request->for))
        {
            $user = $this->user;
            $forSomeone = null;
        }
        else{

            $user = User::find($request->for);
            $forSomeone = $user;

        }
                 

        if (count((array)$user) <1) return view('empty');
        else
        {
            //check mo kung leave for himself or if for others and approver sya
            $approvers = $user->approvers;
            $roles = UserType::find($this->user->userType_id)->roles->pluck('label');

            //Timekeeping Trait
            $anApprover = $this->checkIfAnApprover($approvers, $this->user);
            $isWorkforce =  ($roles->contains('STAFFING_MANAGEMENT')) ? '1':'0';

            //if(!is_null($request->for) && !$anApprover   ) return view('access-denied');

            if ($anApprover || $isWorkforce || $user->id == $this->user->id){
                if ($user->fixedSchedule->isEmpty() && $user->monthlySchedules->isEmpty())
                {
                    $title = 'No Work Schedule found ';
                    $message =  '<br/><br/><br/><br/> No work schedule defined<br /><br/> <i class="fa fa-calendar"></i> <small>Please inform immediate head or Workforce <br/>to have your work schedule plotted before you can file any work-related requests. <br/><br/>Thank you.</smaller></small>
                      <br /><br/>';
                      return view('empty-page', compact('message','title'));

                } 
                else 
                {
                     /*--- we need to check first kung may approver set na ---*/
                    if (count($approvers)<1 ){
                        $title = 'No Approver defined ';
                        $message =  '<br/><br/><br/><br/><span class="text-danger"><i class="fa fa-exclamation-triangle"></i> No Approver defined</span><br /><br/><small>Please inform immediate head or Workforce to update your profile <br/>and set the necessary approver(s) for all of your request submissions. <br/><br/>Thank you.</smaller></small>
                          <br /><br/>';
                          return view('empty-page', compact('message','title'));
                    }
                    else
                    {
                        /*------- check first if user is entitled for a leave (Regualr employee or lengOfService > 6mos) *********/
                        $today=Carbon::today();
                        //$lengthOfService = Carbon::parse($this->user->dateHired,"Asia/Manila")->diffInMonths($today);

                        
                        if (empty($request->from))
                            $vl_from = Carbon::today();
                        else $vl_from = Carbon::parse($request->from,"Asia/Manila");

                        
                        $hasSavedCredits=false;

                        $savedCredits = User_VLcredits::where('user_id', $this->user->id)->where('creditYear',date('Y'))->get();

                        
                        
                            /*---- check mo muna kung may holiday today to properly initialize credits used ---*/
                            $holiday = Holiday::where('holidate',$vl_from->format('Y-m-d'))->get();
                            if (count($holiday) > 0 ){
                                $used = '0.00'; //less 1 day assume wholeday initially
                                if (count($savedCredits)>0){
                                     $hasSavedCredits = true;
                                     $creditsLeft = $savedCredits->first()->beginBalance - $savedCredits->first()->used;
                                 }else {

                                    //check muna kung may existing approved VLs
                                    $approvedVLs = User_OT::where('user_id',$this->user->id)->where('isApproved',true)->get();
                                    if (count($approvedVLs) > 0 )
                                    {
                                        $usedC = 0;
                                        foreach ($approvedVLs as $key) {
                                            $usedC += $key->totalCredits;
                                        }
                                        $creditsLeft = (0.84 * $today->format('m')) - $usedC ;
                                    }
                                    else
                                    $creditsLeft = (0.84 * $today->format('m')) ;
                                 }
                                 
                            }
                            else{
                                $used='1.00';
                                if (count($savedCredits)>0){
                                    $hasSavedCredits = true;
                                     $creditsLeft = ($savedCredits->first()->beginBalance - $savedCredits->first()->used-1);
                                 }else {

                                    //check muna kung may existing approved VLs
                                    $approvedVLs = User_OT::where('user_id',$this->user->id)->where('isApproved',true)->get();
                                    if (count($approvedVLs) > 0 )
                                    {
                                        $usedC = 0;
                                        foreach ($approvedVLs as $key) {
                                            $usedC += $key->totalCredits;
                                        }
                                        $creditsLeft =((0.84 * $today->format('m')) - $usedC) - 1 ;
                                    }
                                    else
                                        $creditsLeft = (0.84 * $today->format('m'))-1 ;
                                }
                            } 

                        
                        $correct = Carbon::now('GMT+8'); //->timezoneName();

                       if($this->user->id !== 564 ) {
                          $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
                            fwrite($file, "-------------------\n Tried [PSOT]: ".$user->lastname."[".$user->id."] --" . $correct->format('M d h:i A'). " by [". $this->user->id."] ".$this->user->lastname."\n");
                            fclose($file);
                        } 
                        return view('timekeeping.user-psOT_create',compact('user', 'vl_from','creditsLeft','used','hasSavedCredits'));


                    }

                    

                }

            } else  return view('access-denied');

            

        }


    }

    public function getPSOTworkedhours($id)
    {
         $user = User::find($id);
         $startday = Input::get('startday');
         $starttime = Input::get('starttime');
         $endday = Input::get('endday');
         $endtime = Input::get('endtime');

         $bS = Biometrics::where('productionDate',Carbon::parse($startday,'Asia/Manila')->format('Y-m-d'))->orderBy('created_at','ASC')->get();
         $bE = Biometrics::where('productionDate',Carbon::parse($endday,'Asia/Manila')->format('Y-m-d'))->orderBy('created_at','ASC')->get();

         (count($bS) > 0) ? $bioStart = $bS->first()->id : $bioStart = null;
         (count($bE) > 0) ? $bioEnd =  $bE->first()->id : $bioEnd = null;

         $workedOTHours = $this->getWorkedOThours($starttime,$endtime,$startday,$endday);
         return response()->json(['status'=>'1','message'=>"hours worked",'workedHours'=> number_format($workedOTHours/60,2),'bioStart'=>$bioStart, 'bioEnd'=>$bioEnd]);

    }

    public function getPSOTLogsForThisDate($id)
    {
        $user = User::find($id);
        if(empty(Input::get('payday'))){
            return response()->json(['status'=>'0','message'=>"invalid date"]);

        }else{
            $d = Carbon::parse(Input::get('payday'),'Asia/Manila');
            $logType = Input::get('logtype');
            $bio = Biometrics::where('productionDate',$d->format('Y-m-d'))->orderBy('created_at','DESC')->get();
            $workedOTHours=0;

            if (count($bio) > 0){
                $biometrics = $bio->first();
                $userLog = Logs::where('biometrics_id',$biometrics->id)->where('user_id',$user->id)->where('logType_id',$logType)->orderBy('created_at','ASC')->get();
                if (count($userLog)>0)
                {
                    //get sched related
                    $dt  = $d->dayOfWeek;
                    switch($dt){
                          case 0: $dayToday = 6; break;
                          case 1: $dayToday = 0; break;
                          default: $dayToday = $dt-1;
                        } 
                    $fsched = DB::table('fixed_schedules')->where('user_id',$user->id)->where('workday',$dayToday)->orderBy('created_at','DESC')->get();
                    $msched = DB::table('monthly_schedules')->where('user_id',$user->id)->where('productionDate',$biometrics->productionDate)->orderBy('created_at','DESC')->get();
                    $cws = collect(DB::table('user_cws')->where('user_id',$user->id)->where('biometrics_id',$biometrics->id)->where('isApproved',1)->get());

                    if (count($fsched) > 0){
                        $fixed = collect($fsched)->first();

                        if (count($msched)>0){
                            //-- verify sino mas bago
                            $monthly=collect($msched)->first();

                            $tfixed = Carbon::parse($fixed->created_at,'Asia/Manila')->format('Y-m-d H:i:s');
                            $tmonth = Carbon::parse($monthly->created_at,'Asia/Manila')->format('Y-m-d H:i:s');

                            
                                ($tfixed > $tmonth) ? $s = $fixed : $s = $monthly;
                                if (count($cws) > 0){
                                    if (Carbon::parse($cws->first()->created_at,'Asia/Manila')->format('Y-m-d H:i:s') > Carbon::parse($s->created_at,'Asia/Manila')->format('Y-m-d H:i:s')) 
                                        $sch = $cws->first()->timeStart;
                                    else $sch = $s->timeStart;
                                }else{ $sch = $s->timeStart; }

                                

                                $workedOTHours = $this->getWorkedOThours($userLog->first()->logTime,$sch,$biometrics->productionDate,$biometrics->productionDate);
                                return response()->json(['status'=>'1','message'=>"logs found",'log'=>$userLog->first()->logTime, 'sched'=>$sch,'workedHours'=> number_format($workedOTHours/60,2),'biometrics_id'=>$biometrics->id]);

                            
                            

                        }else
                        {
                            //-- we need to verify first if its an RD OT or legit Preshift ot
                            //-- we need to adjust: sun 0->6 | 1->0 | 2->1 | 3->2 |4->3 | 5->4 | 6->5->toArray()
                            $s =collect($fsched)->first();
                            $sch = $s->timeStart;
                            if (count($cws) > 0){
                                    if (Carbon::parse($cws->first()->created_at,'Asia/Manila')->format('Y-m-d H:i:s') > Carbon::parse($s->created_at,'Asia/Manila')->format('Y-m-d H:i:s')) {
                                        $sch = $cws->first()->timeStart; $s = $cws->first();
                                    }
                                    else $sch = $s->timeStart;
                            }

                            if ($s->isRD){
                            return response()->json(['status'=>'0','message'=>"schedule for this day is RD. Please file as an RD OT instead from the DTR Sheet",'log'=>null,'sched'=>'RD']);

                            }else {

                                
                                $workedOTHours = $this->getWorkedOThours($userLog->first()->logTime,$sch,$biometrics->productionDate,$biometrics->productionDate);
                                return response()->json(['status'=>'1','message'=>"logs found",'log'=>$userLog->first()->logTime, 'sched'=>$sch,'workedHours'=> number_format($workedOTHours/60,2),'biometrics_id'=>$biometrics->id]);

                            }

                         

                        }

                        
                        


                    }else if (count($msched) > 0){
                        $s=collect($msched)->first();
                        $sch = $s->timeStart;
                        
                            if (count($cws) > 0){
                                    if (Carbon::parse($cws->first()->created_at,'Asia/Manila')->format('Y-m-d H:i:s') > Carbon::parse($s->created_at,'Asia/Manila')->format('Y-m-d H:i:s')) {
                                        $sch = $cws->first()->timeStart; $s = $cws->first();
                                    }
                                    else $sch = $s->timeStart;
                            }

                            if ($s->isRD){
                            return response()->json(['status'=>'0','message'=>"schedule for this day is RD. Please file as an RD OT instead from the DTR Sheet",'log'=>null,'sched'=>'RD']);

                            }else {
                                
                                $workedOTHours = $this->getWorkedOThours($userLog->first()->logTime,$sch,$biometrics->productionDate,$biometrics->productionDate);
                                return response()->json(['status'=>'1','message'=>"logs found",'log'=>$userLog->first()->logTime, 'sched'=>$sch,'workedHours'=> number_format($workedOTHours/60,2),'biometrics_id'=>$biometrics->id]);

                            }


                    }else
                    return response()->json(['status'=>'2','message'=>"logs found but no schedule saved",'log'=>$userLog->first()->logTime,'sched'=>null]);
                }else{
                
                return response()->json(['status'=>'0','message'=>"no bio logs found"]);
            }

            }else{
                $biometrics = null;
                return response()->json(['status'=>'0','message'=>"no biometrics data for that production date"]);
            }

        }



    }


    public function show($id)
    {
         

        $OT = User_OT::find($id);
        //--- update notification
             if (Input::get('seen')){
                $markSeen = User_Notification::where('notification_id',Input::get('notif'))->where('user_id',$this->user->id)->get();
                if (count($markSeen)>0)
                {
                    $markSeen->first()->seen = true;
                    $markSeen->first()->push();
                }
            } 

        if (is_null($OT)) //just mark as seen and return empty view
        {
            return view('empty');

        } else{

            $user = User::find($OT->user_id);
            $profilePic = $this->getProfilePic($user->id);
            $leadershipcheck = ImmediateHead::where('employeeNumber', $user->employeeNumber)->first();
            // $date1 = Carbon::parse(Biometrics::find($cws->biometrics_id)->productionDate);
            // $payrollPeriod = Paycutoff::where('fromDate','>=', strtotime())->get(); //->where('toDate','<=',strtotime(Biometrics::find($cws->biometrics_id)->productionDate))->first();

            if (!empty($leadershipcheck)){ $camps = $leadershipcheck->campaigns->sortBy('name'); } else $camps = $user->campaign;

            $details = new Collection;

            switch ($OT->billedType) {
                case '1': $bt = "Billed"; break;
                case '2': $bt = "Non-billed"; break;
                case '3': $bt = "Patch"; break;
                default: $bt = "Billed"; break;
            }

            $details->push(['productionDate'=>date('M d, Y - l',strtotime(Biometrics::find($OT->biometrics_id)->productionDate)), 
                'dateRequested'=>date('M d, Y - l ', strtotime($OT->created_at)),
                'billableHours'=>$OT->billable_hours,
                'billedType'=>$bt,
                'filedHours'=> $OT->filed_hours,
                'timeStart' => date('h:i A', strtotime($OT->timeStart)),
                'timeEnd'=>date('h:i A', strtotime($OT->timeEnd)),
                'reason'=> $OT->reason ]);

            $approvers = $user->approvers;
            //Timekeeping Trait
            $isApprover = $this->checkIfAnApprover($approvers, $this->user);
            

            
            //return $details;
            return view('timekeeping.show-OT', compact('user', 'profilePic','camps', 'OT','details','isApprover'));

            }
        

    }

    public function store(Request $request)
    {

        $productionDate = Biometrics::find($request->biometrics_id);
        $otendTime = Carbon::parse($productionDate->productionDate." ". $request->OTstart, "Asia/Manila")->addMinutes($request->filedHours*60);

        $OT = new User_OT;
        $OT->user_id = $request->user_id;
        $OT->biometrics_id = $request->biometrics_id;
        $OT->billable_hours = $request->billableHours;
        $OT->filed_hours = $request->filedHours;
        $OT->timeStart = Carbon::parse($request->OTstart,"Asia/Manila")->format('H:i:s');
        $OT->timeEnd = $otendTime->format($otendTime->format('H:i:s')); //Carbon::parse($request->OTend,"Asia/Manila");
        $OT->isRD = $request->isRD;
        $OT->reason = $request->reason;
        $OT->billedType = $request->billedtype;
        $OT->isApproved = null;

       
        $employee = User::find($OT->user_id);

        $approvers = $employee->approvers;

        //Timekeeping Trait
        $isApprover = $this->checkIfAnApprover($approvers, $this->user);

        if ($request->TLsubmitted == 1 || $isApprover)
        {
            $OT->isApproved = true; $TLsubmitted=true; $OT->approver =$employee->supervisor->immediateHead_Campaigns_id;
        } else { $OT->isApproved = null; $TLsubmitted=false;$OT->approver = null; }


         //$request->approver; *------- set to null muna. Will have value depende kung sinong approver mag approve
        $OT->save();

        //--- notify the TL concerned
        

        // get WFM
        $wfm = collect(DB::table('team')->where('campaign_id',50)->
                    leftJoin('users','team.user_id','=','users.id')->
                    select('team.user_id')->
                    where('users.status_id',"!=",7)->
                    where('users.status_id',"!=",8)->
                    where('users.status_id',"!=",9)->
                    where('users.status_id',"!=",13)->get())->pluck('user_id');
        $isWorkforce = in_array($this->user->id, $wfm->toArray());
        $employeeisBackoffice = ( Campaign::find(Team::where('user_id',$employee->id)->first()->campaign_id)->isBackoffice ) ? true : false;


        if (!$TLsubmitted)
        {
            $TL = ImmediateHead::find(ImmediateHead_Campaign::find($request->approver)->immediateHead_id);

            $notification = new Notification;
            $notification->relatedModelID = $OT->id;
            $notification->type = 7;
            $notification->from = $OT->user_id;
            $notification->save();

            foreach ($employee->approvers as $key) {
              $teamlead = ImmediateHead::find($key->immediateHead_id)->userData;
              $tlNotif = new User_Notification;
              $tlNotif->user_id = $teamlead->id;
              $tlNotif->notification_id = $notification->id;
              $tlNotif->seen = false;
              $tlNotif->save();

               # code...
             }

             //-- we now notify all WFM
            if(!$employeeisBackoffice)
            {
                foreach ($wfm as $approver) {
                    //$TL = ImmediateHead::find($approver->immediateHead_id);
                    //-- make sure not to send nofication kung WFM agent ang sender
                    if ($this->user->id !== $approver)
                    {

                        $nu = new User_Notification;
                        $nu->user_id = $approver;
                        $nu->notification_id = $notification->id;
                        $nu->seen = false;
                        $nu->save();

                        // NOW, EMAIL THE TL CONCERNED
                    
                        $email_heading = "New OT Request from: ";
                        $email_body = "Employee: <strong> ". $employee->lastname.", ". $employee->firstname ."  </strong><br/>Total Over time: <strong> [". $OT->filed_hours." hours] </strong>". date('h:i A', strtotime($OT->timeStart)). " - ". date('h:i A', strtotime($OT->timeEnd)). " <br/>";
                        $actionLink = action('UserOTController@show',$OT->id);

                    }

                
                }
            }
            

            

            /*
            // NOW, EMAIL THE TL CONCERNED
            
            $email_heading = "New Over Time Approval Request  from: ";
            $email_body = "Employee: <strong> ". $employee->lastname.", ". $employee->firstname ."  </strong><br/>Total Over time: <strong> [". $OT->filed_hours." hours] </strong>". date('h:i A', strtotime($OT->timeStart)). " - ". date('h:i A', strtotime($OT->timeEnd)). " <br/>";
            $actionLink = action('UserOTController@show',$OT->id);
           
             Mail::send('emails.generalNotif', ['user' => $TL, 'employee'=>$employee, 'email_heading'=>$email_heading, 'email_body'=>$email_body, 'actionLink'=>$actionLink], function ($m) use ($TL) 
             {
                $m->from('OES@openaccessbpo.net', 'OES-OAMPI Evaluation System');
                $m->to($TL->userData->email, $TL->lastname.", ".$TL->firstname)->subject('New Over Time Approval request');     

               
                     $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
                        fwrite($file, "-------------------\n Email sent to ". $TL->userData->email."\n");
                        fclose($file);                      
            

            }); //end mail */

        }

        /* -------------- log updates made --------------------- */
         $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
            fwrite($file, "-------------------\n". $employee->id .",". $employee->lastname." Over Time submission ". date('M d h:i:s'). " by ". $this->user->firstname.", ".$this->user->lastname."\n");
            fclose($file);
        


        return redirect()->back();
        //return redirect()->action('DTRController@show', $cws->user_id);
    }

    //*** this one is called from non-ajax form

    public function update($id, Request $request)
    {
        $OT = User_OT::find($id);
        if (count((array)$OT) >0 )
        {
            $OT->approver = $this->getTLapprover($OT->user_id,$this->user->id);

            $theNotif = Notification::where('relatedModelID', $OT->id)->where('type',7)->get();
            //$coll = new Collection;
            //$coll->push(['notif'=>$theNotif, 'thecol'=>DB::table('user_Notification')->where('notification_id','=',$theNotif->first()->id)->get() ]);

            if (count($theNotif) > 0){
                DB::table('user_Notification')->where('notification_id','=',$theNotif->first()->id)->delete();//->get(); //
                //$todelete->delete(); //DB::table('user_Notification')->where('notification_id','=',$theNotif->first()->id)->delete();

            } 
                
                
            if ($request->isApproved == 1){
                $OT->isApproved = true; 

            }else {
                $OT->isApproved=false;
            }
            $OT->push();

             /* -------------- log updates made --------------------- */
             $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
                fwrite($file, "-------------------\n [". $OT->id."] OT update ". date('M d h:i:s'). " by [". $this->user->id."], ".$this->user->lastname."\n");
                fclose($file);


            //**** send notification to the sender
            $unotif = $this->notifySender($OT,$theNotif->first(),7);

            $user = User::find($OT->user_id);
            (is_null($user->nickname)) ? $f = $user->firstname : $f = $user->nickname;

            //return response()->json(['theNotif'=>$theNotif]);//  $OT;
            return response()->json(['success'=>1, 'firstname'=>$f, 'lastname'=>$user->lastname, 'unotif'=>$unotif]);

        } else return false;
        

    }

    
    //***** this one is called in ajax generated actions

    public function process(Request $request)
    {
        $OT = User_OT::find($request->id);

        if (count($OT) >0 )
        {
            $OT->approver = $this->getTLapprover($OT->user_id,$this->user->id);
            
            
            if ($request->isApproved == 1){
                $OT->isApproved = true; 

            }else {
                $OT->isApproved=false;
            }
            $OT->push();

             /* -------------- log updates made --------------------- */
             $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
                fwrite($file, "-------------------\n [". $OT->id."] OT update ". date('M d h:i:s'). " by [". $this->user->id."], ".$this->user->lastname."\n");
                fclose($file);


            //**** send notification to the sender
            $theNotif = Notification::where('relatedModelID', $OT->id)->where('type',7)->get();

            //then remove those sent notifs to the approvers since it has already been approved/denied
            if (count($theNotif) > 0)
                DB::table('user_Notification')->where('notification_id','=',$theNotif->first()->id)->delete();


            $unotif = $this->notifySender($OT,$theNotif->first(),7);



            $user = User::find($OT->user_id);
            (is_null($user->nickname)) ? $f = $user->firstname : $f = $user->nickname;


            

            return response()->json(['success'=>1, 'firstname'=>$f, 'lastname'=>$user->lastname, 'unotif'=>$unotif]);

        } else return response()->json(['OT'=>$OT, 'success'=>'0']);



      

    }

    public function requestPSOT(Request $request)
    {
        
       $vl = new User_OT;
       $vl->user_id = $request->id;
       $vl->biometrics_id = $request->biometrics_id;
       $vl->billable_hours = $request->billable_hours;
       $vl->filed_hours = $request->filed_hours;
       $vl->timeStart = $request->timeStart;
       $vl->timeEnd = $request->timeEnd;
       $vl->reason = $request->reason;
       $vl->billedType = $request->billedType;
       $vl->preshift = true;
       $vl->isRD = false;
       $vl->isApproved = '';
       $vl->approver = '';



        $employee = User::find($request->id);

        $approvers = $employee->approvers;
        $anApprover = $this->checkIfAnApprover($approvers, $this->user);
        $TLapprover = $this->getTLapprover($employee->id, $this->user->id);

        // get WFM
        $wfm = collect(DB::table('team')->where('campaign_id',50)->
                    leftJoin('users','team.user_id','=','users.id')->
                    select('team.user_id')->
                    where('users.status_id',"!=",7)->
                    where('users.status_id',"!=",8)->
                    where('users.status_id',"!=",9)->
                    where('users.status_id',"!=",13)->get())->pluck('user_id');
        $isWorkforce = in_array($this->user->id, $wfm->toArray());
        $employeeisBackoffice = ( Campaign::find(Team::where('user_id',$employee->id)->first()->campaign_id)->isBackoffice ) ? true : false;
                    

        
        if ( ($isWorkforce && ($this->user->id !== $employee->id) )
            || ($anApprover && $employeeisBackoffice) 
            || (!$employeeisBackoffice && $isWorkforce && ($this->user->id !== $employee->id) ) )
        {
            $vl->isApproved = true; $TLsubmitted=true; 
            if ($isWorkforce) 
                $vl->approver = $this->user->id;
            else
                $vl->approver = $TLapprover;

        } else { $vl->isApproved = null; $TLsubmitted=false;$vl->approver = null; }


        $vl->save();

        if ( !$vl->isApproved && ( ($anApprover && !$employeeisBackoffice)  || (!$anApprover && !$isWorkforce) || (!$anApprover && $employeeisBackoffice) ) )//(!$TLsubmitted && !$canChangeSched)
        { 
            //--- notify the  APPROVERS
            $notification = new Notification;
            $notification->relatedModelID = $vl->id;
            $notification->type = 15; //for PS-OT
            $notification->from = $vl->user_id;
            $notification->save();


            if ($employeeisBackoffice){
                foreach ($employee->approvers as $approver) {
                    $TL = ImmediateHead::find($approver->immediateHead_id);
                    $nu = new User_Notification;
                    $nu->user_id = $TL->userData->id;
                    $nu->notification_id = $notification->id;
                    $nu->seen = false;
                    $nu->save();

                    // NOW, EMAIL THE TL CONCERNED
                
                    $email_heading = "New Preshift Request from: ";
                    $email_body = "Employee: <strong> ". $employee->lastname.", ". $employee->firstname ."  </strong><br/>
                                   Date: <strong> ".$vl->leaveStart  . " to ". $vl->leaveEnd. " </strong> <br/>";
                    $actionLink = action('UserOTController@show',$vl->id);
                   
                     /*Mail::send('emails.generalNotif', ['user' => $TL, 'employee'=>$employee, 'email_heading'=>$email_heading, 'email_body'=>$email_body, 'actionLink'=>$actionLink], function ($m) use ($TL) 
                     {
                        $m->from('OES@openaccessbpo.net', 'OES-OAMPI Evaluation System');
                        $m->to($TL->userData->email, $TL->lastname.", ".$TL->firstname)->subject('New CWS request');     

                        
                             $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
                                fwrite($file, "-------------------\n Email sent to ". $TL->userData->email."\n");
                                fclose($file);                      
                    

                    }); //end mail */


                
                }

            }
            

            //-- we now notify all WFM
            if(!$employeeisBackoffice)
            {
                foreach ($wfm as $approver) {
                    //$TL = ImmediateHead::find($approver->immediateHead_id);
                    //-- make sure not to send nofication kung WFM agent ang sender
                    if ($this->user->id !== $approver)
                    {

                        $nu = new User_Notification;
                        $nu->user_id = $approver;
                        $nu->notification_id = $notification->id;
                        $nu->seen = false;
                        $nu->save();

                        // NOW, EMAIL THE TL CONCERNED
                    
                        $email_heading = "New Preshift Request from: ";
                        $email_body = "Employee: <strong> ". $employee->lastname.", ". $employee->firstname ."  </strong><br/>
                                       Date: <strong> ".$vl->leaveStart  . " to ". $vl->leaveEnd. " </strong> <br/>";
                        $actionLink = action('UserOTController@show',$vl->id);

                    }

                
                }
            }
            

        }

         /* -------------- log updates made --------------------- */
         $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
            fwrite($file, "-------------------\n[". $vl->id ."],". $employee->lastname." PSOT submission ". date('M d h:i:s'). " by ". $this->user->firstname.", ".$this->user->lastname." for emp(".$employee->id.")\n");
            fclose($file);
         

        if (($anApprover && $employeeisBackoffice) || (!$employeeisBackoffice && $isWorkforce )) return response()->json(['success'=>1,'vl'=>$vl]);
        else return response()->json(['success'=>0,'vl'=>$vl]);




    }

}
