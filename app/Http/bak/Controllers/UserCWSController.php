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
        $this->user_cws->destroy($request->id);
        return back();
    }

    public function destroy($id)
    {
        $this->user_cws->destroy($id);
        return back();
    }

    public function show($id)
    {
    	$cws = User_CWS::find($id);

        if (count($cws) < 1){
            return view('empty');
        }
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
    	

    	//--- update notification
    	 if (Input::get('seen')){
            $markSeen = User_Notification::where('notification_id',Input::get('notif'))->where('user_id',$this->user->id)->first();
            $markSeen->seen = true;
            $markSeen->push();

        }
    	return view('timekeeping.show-cws', compact('user', 'profilePic','camps', 'cws','details'));

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

    	if ($request->TLsubmitted == 1 || $canChangeSched)
		{
			$cws->isApproved = true; $TLsubmitted=true;
		} else { $cws->isApproved = null; $TLsubmitted=false; }


    	$cws->approver = $request->approver;
    	$cws->save();

    	
    	$employee = User::find($cws->user_id);

    	if (!$TLsubmitted && !$canChangeSched)
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
            fwrite($file, "-------------------\n". $employee->id .",". $employee->lastname." CWS submission ". date('M d h:i:s'). " by ". $this->user->firstname.", ".$this->user->lastname."\n");
            fclose($file);
        


    	return redirect()->back();
    	//return redirect()->action('DTRController@show', $cws->user_id);
    }

    public function process(Request $request)
    {

         /* -------------- log updates made --------------------- */
         $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
            fwrite($file, "-------------------\n [". $request->id."] CWS REQUEST \n");
            fclose($file);


        $cws = User_CWS::find($request->id);
        $cws->approver = $this->getTLapprover($cws->user_id, $this->user->id);
        
        if ($request->isApproved == 1){
            $cws->isApproved = true;

        }  else {
            $cws->isApproved=false;
        }

        $cws->save();

        //Next, delete all user-notif associated with this:
        $theNotif = Notification::where('relatedModelID',$cws->id)->where('type',6)->first();
        $allUserNotifs = User_Notification::where('notification_id',$theNotif->id)->delete();
        
          /* -------------- log updates made --------------------- */
         $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
            fwrite($file, "-------------------\n [". $cws->id."] CWS update ". date('M d h:i:s'). " by [". $this->user->id."], ".$this->user->lastname."\n");
            fclose($file);

        return  $theNotif;


    }

    public function update($id, Request $request)
    {

    	$cws = User_CWS::find($id);
    	if ($request->isApproved == 1) $cws->isApproved = true; else $cws->isApproved=false;
    	$cws->push();

          /* -------------- log updates made --------------------- */
         $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
            fwrite($file, "-------------------\n [". $cws->id."] CWS update ". date('M d h:i:s'). " by [". $this->user->id."], ".$this->user->lastname."\n");
            fclose($file);

    	return  $cws;


    }
}
