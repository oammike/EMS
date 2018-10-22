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
use OAMPI_Eval\User_OT;

class UserNotificationController extends Controller
{
    protected $user;
   	protected $user_ot;
    use Traits\TimekeepingTraits;
    use Traits\UserTraits;

     public function __construct(User_Notification $user_notification)
    {
        $this->middleware('auth');
        $this->user_notification = $user_notification;
        $this->user =  User::find(Auth::user()->id);
    }

    public function getApprovalNotifications($id)
    {
        $forApprovals = $this->getDashboardNotifs(); //$this->getApprovalNotifs();
        
        return Datatables::collection($forApprovals)->make(true);
        //return response()->json($forApprovals);

    }

     public function deleteNotif(Request $request)
    {

        $this->user_notification->destroy($request->id);
        return response()->json(['success'=>'true']);
    }



    public function deleteRequest($id, Request $request)
    {

    	$notif = User_Notification::find($id);

    	switch ($request->notifType) {
    		case '6': { 
    					$relatedModel = User_CWS::find($notif->detail->relatedModelID);}
    			
    			break;
    		
    		case '7': { 
    					$relatedModel = User_OT::find($notif->detail->relatedModelID);
    					}
    			
    			break;

    		case '8': { $relatedModel = User_DTRP::find($notif->detail->relatedModelID);}
    			
    			break;

    		case '9': { $relatedModel = User_DTRP::find($notif->detail->relatedModelID); }
    			
    			break;

    	}

    	$relatedModel->delete();
    	$notif->delete();
    	return back(); 
    	
    }

    public function process(Request $request)
    {

        $unotif = User_Notification::find($request->id);
        $unotif->seen = true;
        $unotif->save();
        return $unotif;

    }
}
