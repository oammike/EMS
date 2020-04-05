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

class UserCWSController extends Controller
{
    protected $user;
   	protected $user_cws;
    use Traits\TimekeepingTraits;
    use Traits\UserTraits;



     public function __construct(User_CWS $user_cws)
    {
        $this->middleware('auth');
        $this->user_cws = $user_cws;
        $this->user =  User::find(Auth::user()->id);
    }

    
    public function deleteCWS(Request $request)
    {
        //Next, delete all user-notif associated with this:
        $cws = User_CWS::find($request->id);

        if (is_null($cws)) return view('empty');

        $theNotif = Notification::where('relatedModelID', $cws->id)->where('type',6)->get();
        if (count($theNotif) > 0){
            DB::table('user_Notification')->where('notification_id','=',$theNotif->first()->id)->delete();
            
        }
        $deletedID = $cws->id;
        $cws->delete();

        // 
        // $theNotif = Notification::where('relatedModelID',$cws->id)->where('type',6)->first();
        // $allUserNotifs = User_Notification::where('notification_id',$theNotif->id)->delete();

        // $this->user_cws->destroy($request->id);
        return response()->json(['id'=>$deletedID, 'success'=>1]);// back();, 'theNotif'=>$theNotif
    }

    public function deleteThisCWS($id)
    {
        //Next, delete all user-notif associated with this:
        $cws = User_CWS::find($id);
        if (is_null($cws)) return view('empty');

        $deletedID = $cws->id;

        //**** send notification to the sender
        $theNotif = Notification::where('relatedModelID', $cws->id)->where('type',6)->get();

        //then remove those sent notifs to the approvers since it has already been approved/denied
        if (count($theNotif) > 0)
            DB::table('user_Notification')->where('notification_id','=',$theNotif->first()->id)->delete();


        // $theNotif = Notification::where('relatedModelID',$cws->id)->where('type',6)->first();
        // $allUserNotifs = User_Notification::where('notification_id',$theNotif->id)->delete();

        $this->user_cws->destroy($id);
        return back();

    }

    public function destroy($id)
    {
        $this->user_cws->destroy($id);
        return back();
    }

    public function requestCWS(Request $request)
    {

        
       
        $shift = $request->shift;
        $selectedDate = $request->selectedDate;
        $time1 = $request->timestart_old;// Carbon::parse($request->selectedDate.$request->timestart_old,"Asia/Manila");
        $time2 = $request->timeend_old;
        $reason_cws = $request->reason_cws;

        if (strpos($time1, "to") !==false )
        {
            $timeStart_old = Carbon::parse($selectedDate.str_replace("to", "", $time1),"Asia/Manila")->format('H:i:s');
            $timeEnd_old = Carbon::parse($request->selectedDate.$time2,"Asia/Manila")->format('H:i:s');
        } else if (strpos($time2, "to") !== false)
        {
            $timeStart_old = Carbon::parse($selectedDate.str_replace("to", "", $time2),"Asia/Manila")->format('H:i:s');
            $timeEnd_old = Carbon::parse($request->selectedDate.$time1,"Asia/Manila")->format('H:i:s');
        } else if (strpos($time1,'day') !== false || strpos($time2,'day') !== false )
        {
            $timeStart_old ="00:00:00";
            $timeEnd_old = "00:00:00";
        } else { $timeStart_old =null;
            $timeEnd_old = null;}



        $bio1 = Biometrics::where('productionDate',$selectedDate)->get();  
        if ($bio1->isEmpty()){
            $biometrics = new Biometrics;
            $biometrics->productionDate = $selectedDate;
            $biometrics->save();
            $bio = $biometrics;
        } else $bio = $bio1->first();

        //return response()->json(['selectedDate'=>$selectedDate, 'bio'=>$bio]);
        
        $cws = new User_CWS;
        $cws->user_id = $request->id;
        $cws->biometrics_id = $bio->id;

         if ($shift == '-1'){
            $cws->timeStart = date('H:i:s', strtotime("00:00:00"));
            $cws->timeEnd = date('H:i:s', strtotime("00:00:00"));
            $isRD = true;

        }else {
            $ws = explode('-', $shift);
            $cws->timeStart = date('H:i:s', strtotime($ws[0]));
            $cws->timeEnd = date('H:i:s', strtotime($ws[1]));
            $isRD = false;

        }
        
        $cws->timeStart_old = $timeStart_old;
        $cws->timeEnd_old = $timeEnd_old;
        $cws->notes = $reason_cws;
        $cws->isRD = $isRD;

        $employee = User::find($cws->user_id);
        $approvers = $employee->approvers;
        $anApprover = $this->checkIfAnApprover($approvers, $this->user);
        $TLapprover = $this->getTLapprover($employee->id, $this->user->id);

        
        if ($anApprover)
        {
            $cws->isApproved = true; $TLsubmitted=true; $cws->approver = $TLapprover;
        } else { $cws->isApproved = null; $TLsubmitted=false;$cws->approver = null; }

         $cws->save();


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
            $notification->relatedModelID = $cws->id;
            $notification->type = 6;
            $notification->from = $cws->user_id;
            $notification->save();

            foreach ($employee->approvers as $approver) {
                $TL = ImmediateHead::find($approver->immediateHead_id);
                $nu = new User_Notification;
                $nu->user_id = $TL->userData->id;
                $nu->notification_id = $notification->id;
                $nu->seen = false;
                $nu->save();

                // NOW, EMAIL THE TL CONCERNED
            
                $email_heading = "New CWS Request from: ";
                $email_body = "Employee: <strong> ". $employee->lastname.", ". $employee->firstname ."  </strong><br/>
                               Schedule: <strong> ".$request->DproductionDate  . " [".$shift[0]." - ". $shift[1]."] </strong> <br/>";
                $actionLink = action('UserCWSController@show',$cws->id);
               
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
                    
                        $email_heading = "CWS Request from: ";
                        $email_body = "Employee: <strong> ". $employee->lastname.", ". $employee->firstname ."  </strong><br/>
                                       Date: <strong> ".$cws->timeStart  . " to ". $cws->timeEnd. " </strong> <br/>";
                        $actionLink = action('UserCWSController@show',$cws->id);

                    }

                
                }
            }
            

        }

         /* -------------- log updates made --------------------- */
         $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
            fwrite($file, "-------------------\n". $employee->id .",". $employee->lastname." CWS submission ". date('M d h:i:s'). " by ". $this->user->firstname.", ".$this->user->lastname."\n");
            fclose($file);
         

        if ($anApprover) return response()->json(['success'=>1]);
        else return response()->json(['success'=>0]);
        
        //return response()->json(['id'=>$id, 'shift'=>$shift,'selectedDate'=>$selectedDate, 'timeEnd_old'=>$timeEnd_old, 'timeStart_old'=>$timeStart_old]);
          



    }

    public function show($id)
    {
    	$cws = User_CWS::find($id);

        //--- update notification
             if (Input::get('seen')){
                $markSeen = User_Notification::where('notification_id',Input::get('notif'))->where('user_id',$this->user->id)->get();
                if (count($markSeen)>0)
                {
                    $markSeen->first()->seen = true;
                    $markSeen->first()->push();
                }
            } 

        if (is_null($cws)) //just mark as seen and return empty view
        {
            return view('empty');

        } else{

            $user = User::find($cws->user_id);
            $profilePic = $this->getProfilePic($user->id);
            $leadershipcheck = ImmediateHead::where('employeeNumber', $user->employeeNumber)->first();
            // $date1 = Carbon::parse(Biometrics::find($cws->biometrics_id)->productionDate);
            // $payrollPeriod = Paycutoff::where('fromDate','>=', strtotime())->get(); //->where('toDate','<=',strtotime(Biometrics::find($cws->biometrics_id)->productionDate))->first();

            if (!empty($leadershipcheck)){ $camps = $leadershipcheck->campaigns->sortBy('name'); } else $camps = $user->campaign;

            $details = new Collection;
            $details->push(['productionDate'=>date('M d, Y - l',strtotime(Biometrics::find($cws->biometrics_id)->productionDate)), 
                //'payrollPeriod'=>$payrollPeriod,
                'workshift_old'=>date('h:i A', strtotime($cws->timeStart_old))." - ".date('h:i A', strtotime($cws->timeEnd_old)),
                'workshift_new'=>date('h:i A', strtotime($cws->timeStart))." - ".date('h:i A', strtotime($cws->timeEnd)) ]);
            

            return view('timekeeping.show-cws', compact('user', 'profilePic','camps', 'cws','details'));

            }

    	

    }


    public function store(Request $request)
    {

        $roles = UserType::find($this->user->userType_id)->roles->pluck('label'); 
        $canChangeSched =  ($roles->contains('CHANGE_EMPLOYEE_SCHEDULE')) ? '1':'0';

    	

    	$cws = new User_CWS;
    	$cws->user_id = $request->user_id;
    	$cws->biometrics_id = $request->biometrics_id;
    	$shift = explode('-', $request->timeEnd);
    	$cws->timeStart = date('H:i:s', strtotime($shift[0]));
    	$cws->timeEnd = date('H:i:s', strtotime($shift[1]));
    	$cws->timeStart_old = date('H:i:s', strtotime($request->timeStart_old));
    	$cws->timeEnd_old = date('H:i:s', strtotime($request->timeEnd_old));
    	$cws->isRD = $request->isRD;

    	

        $employee = User::find($cws->user_id);
        $approvers = $employee->approvers;
        $anApprover = $this->checkIfAnApprover($approvers, $this->user);

        
        if ($anApprover)
        {
            $cws->isApproved = true; $TLsubmitted=true; $cws->approver = $request->approver;
        } else { $cws->isApproved = null; $TLsubmitted=false;$cws->approver = $request->approver; }



    	
    	$cws->save();

    	
    	

    	if (!$anApprover) //(!$TLsubmitted && !$canChangeSched)
    	{//--- notify the  APPROVERS

            $notification = new Notification;
            $notification->relatedModelID = $cws->id;
            $notification->type = 6;
            $notification->from = $cws->user_id;
            $notification->save();

            foreach ($employee->approvers as $approver) {
                $TL = ImmediateHead::find($approver->immediateHead_id);
                $nu = new User_Notification;
                $nu->user_id = $TL->userData->id;
                $nu->notification_id = $notification->id;
                $nu->seen = false;
                $nu->save();

                // NOW, EMAIL THE TL CONCERNED
            
                $email_heading = "New CWS Request from: ";
                $email_body = "Employee: <strong> ". $employee->lastname.", ". $employee->firstname ."  </strong><br/>
                               Schedule: <strong> ".$request->DproductionDate  . " [".$shift[0]." - ". $shift[1]."] </strong> <br/>";
                $actionLink = action('UserCWSController@show',$cws->id);
               
                 Mail::send('emails.generalNotif', ['user' => $TL, 'employee'=>$employee, 'email_heading'=>$email_heading, 'email_body'=>$email_body, 'actionLink'=>$actionLink], function ($m) use ($TL) 
                 {
                    $m->from('OES@openaccessbpo.net', 'OES-OAMPI Evaluation System');
                    $m->to($TL->userData->email, $TL->lastname.", ".$TL->firstname)->subject('New CWS request');     

                    /* -------------- log updates made --------------------- */
                         $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
                            fwrite($file, "-------------------\n Email sent to ". $TL->userData->email."\n");
                            fclose($file);                      
                

                }); //end mail


            
            }


    		// $TL = ImmediateHead::find(ImmediateHead_Campaign::find($cws->approver)->immediateHead_id);
      //           $nu = new User_Notification;
      //           $nu->user_id = $TL->userData->id;
      //           $nu->notification_id = $notification->id;
      //           $nu->seen = false;
      //           $nu->save();


	        
            

    	}

         /* -------------- log updates made --------------------- */
         $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
            fwrite($file, "-------------------\n". $employee->id .",". $employee->lastname." CWS submission ". date('M d h:i:s'). " by ". $this->user->firstname.", ".$this->user->lastname."| An approver: ".$anApprover."\n");
            fclose($file);
        


    	return redirect()->back();
    	//return redirect()->action('DTRController@show', $cws->user_id);
    }

    public function process(Request $request)
    {
        $cws = User_CWS::find($request->id);

        if(count((array)$cws)>0)
        {
            $cws->approver = $this->getTLapprover($cws->user_id, $this->user->id);
        
            if ($request->isApproved == 1){
                $cws->isApproved = true;

            }  else {
                $cws->isApproved=false;
            }

            $cws->push();

            /* -------------- log updates made --------------------- */

             $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
                fwrite($file, "-------------------\n [". $cws->id."] CWS update ". date('M d h:i:s'). " by [". $this->user->id."], ".$this->user->lastname."\n");
                fclose($file);


             //**** send notification to the sender
            $theNotif = Notification::where('relatedModelID', $cws->id)->where('type',6)->get();

            //then remove those sent notifs to the approvers since it has already been approved/denied
            if (count($theNotif) > 0)
                DB::table('user_Notification')->where('notification_id','=',$theNotif->first()->id)->delete();


            $unotif = $this->notifySender($cws,$theNotif->first(),6);
            $user = User::find($cws->user_id);
            (is_null($user->nickname)) ? $f = $user->firstname : $f = $user->nickname;

            return response()->json(['success'=>1, 'firstname'=>$f, 'lastname'=>$user->lastname, 'unotif'=>$unotif]);



        }else return response()->json(['cws'=>$cws, 'success'=>'0']);
        
        


    }

    public function update($id, Request $request)
    {

    	$cws = User_CWS::find($id);
    	$cws->approver = $this->getTLapprover($cws->user_id, $this->user->id);
        
            if ($request->isApproved == 1){
                $cws->isApproved = true;

            }  else {
                $cws->isApproved=false;
            }

            $cws->push();

          /* -------------- log updates made --------------------- */
         $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
            fwrite($file, "-------------------\n [". $cws->id."] CWS update ". date('M d h:i:s'). " by [". $this->user->id."], ".$this->user->lastname."\n");
            fclose($file);



         //**** send notification to the sender
        $theNotif = Notification::where('relatedModelID', $cws->id)->where('type',6)->get();

        //then remove those sent notifs to the approvers since it has already been approved/denied
        if (count($theNotif) > 0)
            DB::table('user_Notification')->where('notification_id','=',$theNotif->first()->id)->delete();


        $unotif = $this->notifySender($cws,$theNotif->first(),6);
        $employee = User::find($cws->user_id);

        return  $cws;

    	// return  $cws;


    }
}
