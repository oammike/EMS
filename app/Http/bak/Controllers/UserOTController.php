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
use OAMPI_Eval\User_OT;

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

    public function show($id)
    {
        $OT = User_OT::find($id);
        $user = User::find($OT->user_id);
        $profilePic = $this->getProfilePic($user->id);
        $leadershipcheck = ImmediateHead::where('employeeNumber', $user->employeeNumber)->first();
        // $date1 = Carbon::parse(Biometrics::find($cws->biometrics_id)->productionDate);
        // $payrollPeriod = Paycutoff::where('fromDate','>=', strtotime())->get(); //->where('toDate','<=',strtotime(Biometrics::find($cws->biometrics_id)->productionDate))->first();

        if (!empty($leadershipcheck)){ $camps = $leadershipcheck->campaigns->sortBy('name'); } else $camps = $user->campaign;

        $details = new Collection;
        $details->push(['productionDate'=>date('M d, Y - l',strtotime(Biometrics::find($OT->biometrics_id)->productionDate)), 
            'dateRequested'=>date('M d, Y - l ', strtotime($OT->created_at)),
            'billableHours'=>$OT->billable_hours,
            'filedHours'=> $OT->filed_hours,
            'timeStart' => date('h:i A', strtotime($OT->timeStart)),
            'timeEnd'=>date('h:i A', strtotime($OT->timeEnd)),
            'reason'=> $OT->reason ]);
        

        //--- update notification
         if (Input::get('seen')){
            $markSeen = User_Notification::where('notification_id',Input::get('notif'))->where('user_id',$this->user->id)->first();
            $markSeen->seen = true;
            $markSeen->push();

        }
        //return $details;
        return view('timekeeping.show-OT', compact('user', 'profilePic','camps', 'OT','details'));

    }

    public function store(Request $request)
    {

    	

    	$OT = new User_OT;
    	$OT->user_id = $request->user_id;
    	$OT->biometrics_id = $request->biometrics_id;
    	$OT->billable_hours = $request->billableHours;
    	$OT->filed_hours = $request->filedHours;
        $OT->timeStart = $request->OTstart;
        $OT->timeEnd = $request->OTend;
    	$OT->isRD = $request->isRD;
        $OT->reason = $request->reason;
    	$OT->isApproved = null;

    	if ($request->TLsubmitted == 1)
		{
			$OT->isApproved = true; $TLsubmitted=true;
		} else { $OT->isApproved = null; $TLsubmitted=false; }


    	$OT->approver = null; //$request->approver; *------- set to null muna. Will have value depende kung sinong approver mag approve
    	$OT->save();

    	//--- notify the TL concerned
    	$employee = User::find($OT->user_id);

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

    public function update($id, Request $request)
    {
        $OT = User_OT::find($id);
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

            return  $OT;

        } else return false;
        

    }

    public function deleteOT($id)
    {
        $theOT = User_OT::find($id);

        //find all notifications related to that OT
        $theNotif = Notification::where('relatedModelID', $theOT->id)->where('type',7)->get();

        if (count($theNotif) > 0){
            $allNotifs = User_Notification::where('notification_id', $theNotif->first()->id)->get();
            foreach ($allNotifs as $key) {
                $key->delete();
                
            }
        }
        $theOT->delete();
        return response()->json(['success'=>"ok"]);


    }


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

            return  $OT;

        } else return false;



      

    }

}
