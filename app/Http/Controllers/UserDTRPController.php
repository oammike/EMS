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
use OAMPI_Eval\User_DTRP;
use OAMPI_Eval\Notification;
use OAMPI_Eval\User_Notification;

class UserDTRPController extends Controller
{
    protected $user;
   	protected $user_dtrp;
    use Traits\TimekeepingTraits;
    use Traits\UserTraits;



     public function __construct(User_DTRP $user_dtrp)
    {
        $this->middleware('auth');
        $this->user_dtrp = $user_dtrp;
        $this->user =  User::find(Auth::user()->id);
    }

    public function deleteThisDTRP($id,Request $request)
    {
        $theDTRP = User_DTRP::find($id);
        if (is_null($theDTRP)) return view('empty');

        $deletedID = $theDTRP->id;

        //**** send notification to the sender
        $theNotif = Notification::where('relatedModelID', $theDTRP->id)->where('type',$request->notifType)->get();

        //then remove those sent notifs to the approvers since it has already been approved/denied
        if (count($theNotif) > 0)
            DB::table('user_Notification')->where('notification_id','=',$theNotif->first()->id)->delete();


        $theDTRP->delete();
        if ($request->redirect == '1')
            return redirect()->back();
        else return response()->json(['success'=>"ok"]);


        
    }

    public function process(Request $request)
    {
        //return $request;
        $DTRP = User_DTRP::find($request->id);
        if(count($DTRP)>0)
        {
            $DTRP->approvedBy = $this->getTLapprover($DTRP->user_id, $this->user->id);
        
            if ($request->isApproved == 1){
                $DTRP->isApproved = true;

            }  else {
                $DTRP->isApproved=false;
            }

            $DTRP->push();

            /* -------------- log updates made --------------------- */

             $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
                if($DTRP->isApproved)
            fwrite($file, "-------------------\n [". $DTRP->id."] DTRP ".LogType::find($DTRP->logType_id)->name." - Approved ". date('M d h:i:s'). " by [". $this->user->id."], ".$this->user->lastname."\n");
            else fwrite($file, "-------------------\n [". $DTRP->id."] DTRP ".LogType::find($DTRP->logType_id)->name." - Denied ". date('M d h:i:s'). " by [". $this->user->id."], ".$this->user->lastname."\n");
                fclose($file);


             //**** send notification to the sender
            if ($DTRP->logType_id == 1) {
                $theNotif = Notification::where('relatedModelID', $DTRP->id)->where('type',8)->get();
                //then remove those sent notifs to the approvers since it has already been approved/denied
                if (count($theNotif) > 0)
                DB::table('user_Notification')->where('notification_id','=',$theNotif->first()->id)->delete();

                $unotif = $this->notifySender($DTRP,$theNotif->first(),8);
            }
            else {
                $theNotif = Notification::where('relatedModelID', $DTRP->id)->where('type',9)->get();

                if (count($theNotif) > 0)
                DB::table('user_Notification')->where('notification_id','=',$theNotif->first()->id)->delete();

                $unotif = $this->notifySender($DTRP,$theNotif->first(),9);
            }

            


            
            $user = User::find($DTRP->user_id);
            (is_null($user->nickname)) ? $f = $user->firstname : $f = $user->nickname;

            return response()->json(['success'=>1, 'firstname'=>$f, 'lastname'=>$user->lastname, 'unotif'=>$unotif]);

        }else return response()->json(['DTRP'=>$DTRP, 'success'=>'0']);

    }



    public function show($id)
    {
        $DTRP = User_DTRP::find($id);

        //--- update notification
             if (Input::get('seen')){
                $markSeen = User_Notification::where('notification_id',Input::get('notif'))->where('user_id',$this->user->id)->first();
                $markSeen->seen = true;
                $markSeen->push();

            } 

         if (is_null($DTRP)) //just mark as seen and return empty view
        {
            return view('empty');

        } else {

            $user = User::find($DTRP->user_id);
            $profilePic = $this->getProfilePic($user->id);
            $leadershipcheck = ImmediateHead::where('employeeNumber', $user->employeeNumber)->first();
            // $date1 = Carbon::parse(Biometrics::find($cws->biometrics_id)->productionDate);
            // $payrollPeriod = Paycutoff::where('fromDate','>=', strtotime())->get(); //->where('toDate','<=',strtotime(Biometrics::find($cws->biometrics_id)->productionDate))->first();

            if (!empty($leadershipcheck)){ $camps = $leadershipcheck->campaigns->sortBy('name'); } else $camps = $user->campaign;

            $details = new Collection;

            $logType = LogType::find($DTRP->logType_id)->name;
            $details->push(['productionDate'=>date('M d, Y - l',strtotime(Biometrics::find($DTRP->biometrics_id)->productionDate)), 
                'dateRequested'=>date('M d, Y - l ', strtotime($DTRP->created_at)),
                
                'logTime' => date('h:i A', strtotime($DTRP->logTime)),
                'logType'=>$logType,
                'notes'=> $DTRP->notes ]);
            

            
            return view('timekeeping.show-DTRP', compact('user', 'profilePic','camps', 'DTRP','details'));

        }


        

    }

    public function store(Request $request)
    {
    	/**** look for Timekeeping trait: $this->saveDTRP instead *****/
    }

    public function update($id, Request $request)
    {
        $DTRP = User_DTRP::find($id);
        if(count($DTRP)>0)
        {
            if ($request->isApproved == 1)
            {
                $DTRP->isApproved = true;
                
            }  else{
                $DTRP->isApproved=false;
            } 
            
            $DTRP->approvedBy = $this->getTLapprover($DTRP->user_id, $this->user->id);
            $DTRP->push();

             /* -------------- log updates made --------------------- */
             $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
                fwrite($file, "-------------------\n [". $DTRP->id."] DTRP update ". date('M d h:i:s'). " by [". $this->user->id."], ".$this->user->lastname."\n");
                fclose($file);



             //**** send notification to the sender
                if ($DTRP->logType_id == 1) {
                    $theNotif = Notification::where('relatedModelID', $DTRP->id)->where('type',8)->get();
                     //then remove those sent notifs to the approvers since it has already been approved/denied
                    if (count($theNotif) > 0)
                        DB::table('user_Notification')->where('notification_id','=',$theNotif->first()->id)->delete();

                    $unotif = $this->notifySender($DTRP,$theNotif->first(),8);
                }
                else {
                    $theNotif = Notification::where('relatedModelID', $DTRP->id)->where('type',9)->get();
                    if (count($theNotif) > 0)
                        DB::table('user_Notification')->where('notification_id','=',$theNotif->first()->id)->delete();

                    $unotif = $this->notifySender($DTRP,$theNotif->first(),9);
                }

               


                
                $user = User::find($DTRP->user_id);
                (is_null($user->nickname)) ? $f = $user->firstname : $f = $user->nickname;

                return response()->json(['success'=>1, 'firstname'=>$f, 'lastname'=>$user->lastname, 'unotif'=>$unotif]);

        }else return response()->json(['DTRP'=>$DTRP, 'success'=>'0']);

    }
}
