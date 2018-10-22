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

    public function process(Request $request)
    {
        //return $request;
        $DTRP = User_DTRP::find($request->id);

         /* -------------- log updates made --------------------- */
         $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
         fwrite($file, "-------------------\n [". $DTRP->id."] id  \n". "[". $DTRP->isApproved."] isApproved \n"."notes: ".$DTRP->notes."\n");
         fclose($file);


        
        if ($request->isApproved == 1)
        {
            $DTRP->isApproved = true; 
            // $DTRlog = Logs::where('user_id',$DTRP->user_id)->where('biometrics_id',$DTRP->biometrics_id)->where('logType_id',1)->orderBy('updated_at','DESC')->first();
            // $DTRlog->logTime = $DTRP->timeStart;
            // $DTRlog->save();

        } else $DTRP->isApproved=false;

        $DTRP->approvedBy = $this->getTLapprover($DTRP->user_id, $this->user->id);
        $DTRP->save();

          /* -------------- log updates made --------------------- */
         $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
         if($DTRP->isApproved)
            fwrite($file, "-------------------\n [". $DTRP->id."] DTRP ".LogType::find($DTRP->logType_id)->name." - Approved ". date('M d h:i:s'). " by [". $this->user->id."], ".$this->user->lastname."\n");
        else fwrite($file, "-------------------\n [". $DTRP->id."] DTRP ".LogType::find($DTRP->logType_id)->name." - Denied ". date('M d h:i:s'). " by [". $this->user->id."], ".$this->user->lastname."\n");
            
            fclose($file);

        return  $DTRP;


    }

    public function show($id)
    {
        $DTRP = User_DTRP::find($id);
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
        

        //--- update notification
         if (Input::get('seen')){
            $markSeen = User_Notification::where('notification_id',Input::get('notif'))->where('user_id',$this->user->id)->first();
            $markSeen->seen = true;
            $markSeen->push();

        }
        //return $details;
        return view('timekeeping.show-DTRP', compact('user', 'profilePic','camps', 'DTRP','details'));

    }

    public function store(Request $request)
    {
    	
    }

    public function update($id, Request $request)
    {
        $OT = User_OT::find($id);
        if ($request->isApproved == 1) $OT->isApproved = true; else $OT->isApproved=false;
        $OT->push();

         /* -------------- log updates made --------------------- */
         $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
            fwrite($file, "-------------------\n [". $OT->id."] OT update ". date('M d h:i:s'). " by [". $this->user->id."], ".$this->user->lastname."\n");
            fclose($file);

        return  $OT;

    }
}
