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
use OAMPI_Eval\User_VL;
use OAMPI_Eval\User_LWOP;
use OAMPI_Eval\User_OBT;
use OAMPI_Eval\User_VLcredits;
use OAMPI_Eval\Notification;
use OAMPI_Eval\User_Notification;

class UserOBTController extends Controller
{

	protected $user;
   	protected $user_obt;
    use Traits\TimekeepingTraits;
    use Traits\UserTraits;


     public function __construct(User_OBT $user_obt)
    {
        $this->middleware('auth');
        $this->user_obt = $user_obt;
        $this->user =  User::find(Auth::user()->id);
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
                 

        if (count($user) <1) return view('empty');
        else
        {
            //check mo kung leave for himself or if for others and approver sya
            $approvers = $user->approvers;
            //Timekeeping Trait
            $anApprover = $this->checkIfAnApprover($approvers, $this->user);

            $correct = Carbon::now('GMT+8'); //->timezoneName();

                       if($this->user->id !== 564 ) {
                          $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
                            fwrite($file, "-------------------\n Tried [OBT]: ".$user->lastname."[".$user->id."] --" . $correct->format('M d h:i A'). " by [". $this->user->id."] ".$this->user->lastname."\n");
                            fclose($file);
                        } 

            if(!is_null($request->for) && !$anApprover ) return view('access-denied');

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
                    $message =  '<br/><br/><br/><br/><span class="text-danger"><i class="fa fa-exclamation-triangle"></i> No Approver defined</span><br /><br/><small>Please inform HR to update your profile <br/>and set the necessary approver(s) for all of your request submissions. <br/><br/>Thank you.</smaller></small>
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
                                $approvedVLs = User_VL::where('user_id',$this->user->id)->where('isApproved',true)->get();
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
                                $approvedVLs = User_VL::where('user_id',$this->user->id)->where('isApproved',true)->get();
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

                    
                    
                    return view('timekeeping.user-obt_create',compact('user', 'vl_from','creditsLeft','used','hasSavedCredits'));


                }

                

            }

        }


    }


    public function deleteThisOBT($id, Request $request)
    {
        $theVL = User_OBT::find($id);

        //find all notifications related to that OT
        $theNotif = Notification::where('relatedModelID', $theVL->id)->where('type',$request->notifType)->get();

        if (count($theNotif) > 0){
            $allNotifs = User_Notification::where('notification_id', $theNotif->first()->id)->get();
            foreach ($allNotifs as $key) {
                $key->delete();
                
            }
        }

        


        $theVL->delete();

        if ($request->redirect == '1')
            return redirect()->back();
        else return response()->json(['success'=>"ok"]);
        
    }


    public function getCredits(Request $request)
    {
    	$user = User::find($request->user_id);
    	$vl_from = Carbon::parse($request->date_from,"Asia/Manila");
        $vf = Carbon::parse($request->date_from,"Asia/Manila");
        $dateFrom = Carbon::parse($request->date_from,"Asia/Manila");
        $creditsleft =$request->creditsleft; //less 1 day from default
    	$coll = new Collection;
    	
    	$shift_from = $request->shift_from;$shift_to = $request->shift_to;
    	$schedules = new Collection;
        $displayShift = "";

        $hasVLalready=false;
    	

        /*** we need to check first kung may existing pending or approved VL na
             para iwas doble filing **/

        //$mayExisting = User_VL::where('user_id',$user->id)->where('leaveStart','>=',$vf->startOfDay()->format('Y-m-d H:i:s'))->where('leaveStart','<',$vf->addDay()->format('Y-m-d H:i:s'))->where('leaveStart','<',$vf->addDay()->format('Y-m-d H:i:s'))get();

        $mayExisting = User_OBT::where('user_id',$user->id)->where('leaveEnd','>=',$vf->endOfDay()->format('Y-m-d H:i:s'))->get();
        $interval = new \DateInterval("P1D");

        /*if (count($mayExisting) > 0)
        {*/

            foreach ($mayExisting as $key) {
                $period = new \DatePeriod(new \DateTime(Carbon::parse($key->leaveStart,'Asia/Manila')->format('Y-m-d')),$interval, new \DateTime(Carbon::parse($key->leaveEnd,'Asia/Manila')->format('Y-m-d')));

                foreach ($period as $p) {
                    if($p->format('M d, Y') == $vf->format('M d, Y') ){
                        $hasVLalready=true;
                        $coll->push($p->format('M d, Y'));
                        goto mayExistingReturn;
                        //break 2;
                    }
                }
                
            }
            
            

        /*} else{*/

            //*** if date range is submitted [from-to]

            if ( !is_null($request->date_to) && $request->date_to !== "" )
            {
                $credits = 0;
                $holidays = 0;
                $vl_to =Carbon::parse($request->date_to,"Asia/Manila");
                
                while ($vl_from <= $vl_to) {
                    //$schedForTheDay = \App::call("App\Http\UserController@getWorkSchedForTheDay($user->id,[$request->vl_day=>$vl_from])");
                    //app('App\Http\Controllers\UserController')->getWorkSchedForTheDay($user->id,[$request->vl_day=>$vl_from]);
                    $schedForTheDay = $this->getWorkSchedForTheDay($user,$vl_from,$mayExisting);
                    if ( strpos($schedForTheDay['title'], "Rest") !== false ){ /*do not add anything since its rest day */ }
                        else $credits++;

                    if (count(Holiday::where('holidate',$vl_from->format('Y-m-d'))->get()) > 0) $holidays++;

                    $vl_from->addDay();
                }

                //if ($shift_from == '2' || $shift_from=='3') $credits -= 0.5;
                if ($shift_to == '2' && $request->date_to !== null)
                {
                    //check mo muna kung RD to or holiday, wag ka na mag deduct
                    $schedForTheDay = $this->getWorkSchedForTheDay($user,$vl_to,$mayExisting);
                    if ( strpos($schedForTheDay['title'], "Rest") !== false || count(Holiday::where('holidate',$vl_to->format('Y-m-d'))->get())>0 ){ }
                    /*else if (count(Holiday::where('holidate',$vl_to->format('Y-m-d'))->get()) > 0){ }*/
                    else $credits -= 0.5;
                } 


                $s = $this->getWorkSchedForTheDay($user,Carbon::parse($request->date_from,"Asia/Manila"),$mayExisting);


                switch ($shift_from) {
                    case '2':{ 
                                $credits -= 0.5; 
                                $start = Carbon::parse($s['start'])->format('h:i A');
                                $end = Carbon::parse($s['start'])->addHour(4)->format('h:i A');
                                // Carbon::parse($schedForTheDay['start'])->addHour(4)->format('h:i A');
                                $displayShift = $start." - ".$end;
                                
                             }break;

                    
                    case '3':{ 
                                $credits -= 0.5; 
                                $start = Carbon::parse($s['end'])->addHour(-4)->format('h:i A');
                                $end = Carbon::parse($s['end'])->format('h:i A');
                                $displayShift = $start." - ".$end;

                             }break;
                }


                
                $credits -= $holidays;
                $creditsleft -= $credits;
                $creditsleft++; //fix for initially 1 credit deducted from loading
                

            } else
            {
                $credits = 1;
                //return response()->json(['vl_from'=>$vl_from, 'dateto'=>$request->date_to]);
                $schedForTheDay = $this->getWorkSchedForTheDay($user,$vl_from,$mayExisting);
                //return $schedForTheDay;

                //if ($shift_from == '2' || $shift_from=='3') $credits -= 0.5;

                switch ($shift_from) {
                    case '2':{ 
                                (count(Holiday::where('holidate',$vl_from->format('Y-m-d'))->get()) > 0) ? $credits = 0 : $credits -= 0.5; 
                                $start = Carbon::parse($schedForTheDay['start'])->format('h:i A');
                                $end = Carbon::parse($schedForTheDay['start'])->addHour(4)->format('h:i A');
                                $displayShift = $start." - ".$end;
                                (is_null($creditsleft)) ? $creditsleft = $credits : $creditsleft = (float)$creditsleft - $credits;
                                
                             }break;

                    
                    case '3':{ 
                                (count(Holiday::where('holidate',$vl_from->format('Y-m-d'))->get()) > 0) ? $credits = 0 : $credits -= 0.5; 
                                $start = Carbon::parse($schedForTheDay['end'])->addHour(-4)->format('h:i A');
                                $end = Carbon::parse($schedForTheDay['end'])->format('h:i A');
                                $displayShift = $start." - ".$end;
                                 (is_null($creditsleft)) ? $creditsleft = $credits : (float)$creditsleft - $credits;
                                //$creditsleft -= $credits;
                             }break;
                    default:{
                                (count(Holiday::where('holidate',$vl_from->format('Y-m-d'))->get()) > 0) ? $credits = 0 : $credits = 1.00;
                                $displayShift =  Carbon::parse($schedForTheDay['start'])->format('h:i A'). " - ". Carbon::parse($schedForTheDay['end'])->format('h:i A');
                                //$creditsleft;

                            }
                }

            }

            /*-------------we now check for excess filing, file it as LWOP instead ------------------ */
            $creditsToEarn = 0;
            $forLWOP=0;

           


            return response()->json(['shift_from'=>$shift_from, 'hasVLalready'=>$hasVLalready, 'creditsToEarn'=>$creditsToEarn, 'forLWOP'=>abs($forLWOP), 'creditsleft'=>$creditsleft, 'credits'=> number_format(abs($credits),2) , 'shift_from'=>$shift_from, 'shift_to'=>$shift_to,'displayShift'=>$displayShift,  'schedForTheDay'=>$schedForTheDay]);



        /*}//end may existing nang VL application*/

        mayExistingReturn:
        return response()->json(['hasOBTalready'=>$hasVLalready, 'creditsToEarn'=>0, 'forLWOP'=>0, 'creditsleft'=>0, 'credits'=> 0 , 'shift_from'=>$shift_from, 'shift_to'=>$shift_to,'displayShift'=>$displayShift,  'schedForTheDay'=>null]);


    }


    public function process(Request $request)
    {

        /* -------------- log updates made --------------------- */
        $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
        fwrite($file, "-------------------\n [". $request->id."] OBT REQUEST \n");
        fclose($file);


        $vl = User_OBT::find($request->id);
        $vl->approver = $this->getTLapprover($vl->user_id, $this->user->id);
        
        if ($request->isApproved == 1){
            $vl->isApproved = true;

        }  else {
            $vl->isApproved=false;
        }

        $vl->save();

        
         //**** send notification to the sender
        $theNotif = Notification::where('relatedModelID', $vl->id)->where('type',13)->get();

        //then remove those sent notifs to the approvers since it has already been approved/denied
        if (count($theNotif) > 0)
            DB::table('user_Notification')->where('notification_id','=',$theNotif->first()->id)->delete();


        $unotif = $this->notifySender($vl,$theNotif->first(),13);

        
        
          /* -------------- log updates made --------------------- */
         $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
            fwrite($file, "-------------------\n [". $vl->id."] OBT update ". date('M d h:i:s'). " by [". $this->user->id."], ".$this->user->lastname."\n");
            fclose($file);

        $user = User::find($vl->user_id);

        (is_null($user->nickname)) ? $f = $user->firstname : $f = $user->nickname;
        return response()->json(['success'=>1, 'firstname'=>$f, 'lastname'=>$user->lastname, 'unotif'=>$unotif]);


    }

    public function requestOBT(Request $request)
    {

        
       $vl = new User_OBT;
       $vl->user_id = $request->id;
       $vl->leaveStart =  $request->leaveFrom;

        //----- pag blank yung TO:, meaning wholeday sya. So add 9Hours sa FROM
        if($request->leaveTo == '0000-00-00 00:00:00' || is_null($request->leaveTo)){

             $to =Carbon::parse($request->leaveFrom,"Asia/Manila")->addHour(9);
             $vl->leaveEnd = $to->format('Y-m-d H:i:s');

         } else $vl->leaveEnd = $request->leaveTo;
        
        $vl->notes = $request->reason_vl;
        $vl->totalCredits= $request->totalcredits;
        $vl->halfdayFrom = $request->halfdayFrom;
        $vl->halfdayTo = $request->halfdayTo;
        

        $employee = User::find($request->id);

        $approvers = $employee->approvers;
        $anApprover = $this->checkIfAnApprover($approvers, $this->user);
        $TLapprover = $this->getTLapprover($employee->id, $this->user->id);

        
        if ($anApprover)
        {
            $vl->isApproved = true; $TLsubmitted=true; $vl->approver = $TLapprover;
        } else { $vl->isApproved = null; $TLsubmitted=false;$vl->approver = null; }


        $vl->save();

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


        if (!$anApprover) //(!$TLsubmitted && !$canChangeSched)
        {//--- notify the  APPROVERS

            $notification = new Notification;
            $notification->relatedModelID = $vl->id;
            $notification->type = 13;
            $notification->from = $vl->user_id;
            $notification->save();

            foreach ($employee->approvers as $approver) {
                $TL = ImmediateHead::find($approver->immediateHead_id);
                $nu = new User_Notification;
                $nu->user_id = $TL->userData->id;
                $nu->notification_id = $notification->id;
                $nu->seen = false;
                $nu->save();

                // NOW, EMAIL THE TL CONCERNED
            
                $email_heading = "New Official Business Trip Request from: ";
                $email_body = "Employee: <strong> ". $employee->lastname.", ". $employee->firstname ."  </strong><br/>
                               Date: <strong> ".$vl->leaveStart  . " to ". $vl->leaveEnd. " </strong> <br/>";
                $actionLink = action('UserOBTController@show',$vl->id);
               
                 /*Mail::send('emails.generalNotif', ['user' => $TL, 'employee'=>$employee, 'email_heading'=>$email_heading, 'email_body'=>$email_body, 'actionLink'=>$actionLink], function ($m) use ($TL) 
                 {
                    $m->from('OES@openaccessbpo.net', 'OES-OAMPI Evaluation System');
                    $m->to($TL->userData->email, $TL->lastname.", ".$TL->firstname)->subject('New CWS request');     

                    
                         $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
                            fwrite($file, "-------------------\n Email sent to ". $TL->userData->email."\n");
                            fclose($file);                      
                

                }); //end mail */


            
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
                    
                        $email_heading = "New OBT Request from: ";
                        $email_body = "Employee: <strong> ". $employee->lastname.", ". $employee->firstname ."  </strong><br/>
                                       Date: <strong> ".$vl->leaveStart  . " to ". $vl->leaveEnd. " </strong> <br/>";
                        $actionLink = action('UserOBTController@show',$vl->id);

                    }

                
                }
            }
            

        }

         /* -------------- log updates made --------------------- */
         $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
            fwrite($file, "-------------------\n". $employee->id .",". $employee->lastname." OBT submission ". date('M d h:i:s'). " by ". $this->user->firstname.", ".$this->user->lastname."\n");
            fclose($file);
         

        if ($anApprover) return response()->json(['success'=>1,'vl'=>$vl]);
        else return response()->json(['success'=>0,'vl'=>$vl]);




    }


    public function show($id)
    {

        //--- update notification
         if (Input::get('seen')){
            $markSeen = User_Notification::where('notification_id',Input::get('notif'))->where('user_id',$this->user->id)->get();
            if (count($markSeen)>0)
            {
                $markSeen->first()->seen = true;
                $markSeen->first()->push();
            }
                

        }


        $vl = User_OBT::find($id);

        if (is_null($vl)) return view('empty');

        $user = User::find($vl->user_id);
        $profilePic = $this->getProfilePic($user->id);
        $leadershipcheck = ImmediateHead::where('employeeNumber', $user->employeeNumber)->first();
        // $date1 = Carbon::parse(Biometrics::find($cws->biometrics_id)->productionDate);
        // $payrollPeriod = Paycutoff::where('fromDate','>=', strtotime())->get(); //->where('toDate','<=',strtotime(Biometrics::find($cws->biometrics_id)->productionDate))->first();

        if (!empty($leadershipcheck)){ $camps = $leadershipcheck->campaigns->sortBy('name'); } else $camps = $user->campaign;

        $details = new Collection;

        $details->push(['from'=>date('M d - D',strtotime($vl->leaveStart)), 'to'=>date('M d - D',strtotime($vl->leaveEnd)),
            'totalCredits'=>$vl->totalCredits,
            'dateRequested'=>date('M d, Y - D ', strtotime($vl->created_at)),
            'notes'=> $vl->notes ]);
        

        
        //return $details;
        return view('timekeeping.show-OBT', compact('user', 'profilePic','camps', 'vl','details'));


    }


}
