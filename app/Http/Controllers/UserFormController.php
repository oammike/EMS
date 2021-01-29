<?php

namespace OAMPI_Eval\Http\Controllers;

use Hash;
use Carbon\Carbon;
use Excel;
use \PDF;
use \Mail;
use \DB;
use \App;
use \Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Yajra\Datatables\Facades\Datatables;

use OAMPI_Eval\Http\Requests;
use OAMPI_Eval\Role;
use OAMPI_Eval\User;
use OAMPI_Eval\Cutoff;
use OAMPI_Eval\UserForms;
use OAMPI_Eval\UserForm_Access;
use OAMPI_Eval\User_Leader;
use OAMPI_Eval\User_CWS;
use OAMPI_Eval\User_DTRP;
use OAMPI_Eval\User_VL;
use OAMPI_Eval\User_VTO;
use OAMPI_Eval\User_SL;
use OAMPI_Eval\User_LWOP;
use OAMPI_Eval\User_OT;
use OAMPI_Eval\User_OBT;
use OAMPI_Eval\User_Familyleave;
use OAMPI_Eval\User_SpecialAccess;
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
use OAMPI_Eval\FixedSchedules;
use OAMPI_Eval\MonthlySchedules;
use OAMPI_Eval\Movement;
use OAMPI_Eval\Restday;
use OAMPI_Eval\Biometrics;
use OAMPI_Eval\User_VLcredits;
use OAMPI_Eval\User_SLcredits;
use OAMPI_Eval\EvalType;
use OAMPI_Eval\Point;
use OAMPI_Eval\Reward;
use OAMPI_Eval\Reward_Transfers;
use OAMPI_Eval\Reward_Award;
use OAMPI_Eval\Reward_Creditor;
use OAMPI_Eval\Reward_Feedback;
use OAMPI_Eval\Reward_Waysto;
use OAMPI_Eval\Orders;
use OAMPI_Eval\Holiday;
use OAMPI_Eval\Paycutoff;
use OAMPI_Eval\Coffeeshop;


class UserFormController extends Controller
{
    protected $user;
    protected $pagination_items = 50;
    use Traits\UserTraits;
    use Traits\TimekeepingTraits;

     public function __construct(UserForms $userforms)
    {
        $this->middleware('auth');
        $this->userforms = $userforms;
        $this->user =  User::find(Auth::user()->id);
        $this->pagination_items = 50;
        $this->initLoad =100;
    }

    public function create(Request $request)
    {
        $foreignPartime = null;
        //check first kung may plotted sched and if approver submitted
        if(is_null($request->for))
            {
                $user = $this->user;
                $forSomeone = null;
            }
            else{

                $user = User::find($request->for);
                $forSomeone = $user;

                
            }

        (is_null($request->t)) ? $formType = 'BIR2316' : $formType = $request->t;
        (is_null($request->s)) ? $isSigned = 0 : $isSigned = $request->s;

                
            
                 

        if (count( (array)$user) <1) return view('empty');
        else
        {
            ($this->user->userType_id == 1 || $this->user->userType_id == 6 || $this->user->userType_id == 14 ) ? $isAllowed = true : $isAllowed=false;

            $correct = Carbon::now('GMT+8'); 

            $roles = UserType::find($this->user->userType_id)->roles->pluck('label'); //->where('label','MOVE_EMPLOYEES');
            /* -------- get this user's department. If Backoffice, WFM can't access this ------*/
            $isBackoffice = ( Campaign::find(Team::where('user_id',$user->id)->first()->campaign_id)->isBackoffice ) ? true : false;
            $isWorkforce =  ($roles->contains('STAFFING_MANAGEMENT')) ? '1':'0';

            $correct = Carbon::now('GMT+8');

	        //if($this->user->id !== 564 ) {
	            $file = fopen('storage/uploads/log.txt', 'a') or die("Unable to open logs");
	            fwrite($file, "-------------------\n UserForm create - ". $correct->format('M d h:i A'). " by [". $this->user->id."] ".$this->user->lastname."\n");
	            fclose($file);
	        //}

            if(!$isAllowed && ($isSigned && $this->user->id !== $user->id)) return view('access-denied');
            return view('forms.userForms',compact('user','isAllowed','isSigned','formType'));

        }
        
         
        
    }

    public function uploadFile(Request $request)
    {
    	$uf = new UserForms;
    	$uf->user_id = $request->userid;
    	$uf->formType = $request->formType;
    	$uf->isSigned = $request->isSigned;
    	$attachments = $request->file('attachments');
    	

              $today = Carbon::now('GMT+8')->format('Y-m-d_H_i_s');
              $destinationPath = storage_path() . '/uploads/forms/finance';
              $extension = Input::file('attachments')->getClientOriginalExtension(); // getting image extension
              if($request->isSigned)
              	$fileName = $today.'-user-'.$request->userid.'-'.$request->formType.'_signed.'.$extension; // renameing image
              else
              	$fileName = $today.'-user-'.$request->userid.'-'.$request->formType.'.'.$extension; // renameing image

              $attachments->move($destinationPath, $fileName); // uploading file to given path

              
              $uf->filename = $fileName;

            
                /* -------------- log updates made --------------------- */
            $file = fopen('storage/uploads/log.txt', 'a') or die("Unable to open logs");
            fwrite($file, "\n-------------------\n ".$request->formType." UP : ". $fileName ."  by [". $this->user->id."], ".$this->user->lastname."\n");
            fclose($file);

    	$uf->userUploader = $this->user->id;
    	$uf->save();

    	//update User table
    	$u = User::find($request->userid);
    	($request->isSigned) ? $u->hasSigned2316 = 1 : $u->has2316 = 1;

    	$u->push();

    	return response()->json(['success'=>1,'form'=>$uf]);


    }

    public function viewUserForm(Request $request)
    {
    	
    	($request->u) ? $userID=$request->u : $userID= $this->user->id;
    	$type = $request->f;
    	($request->s) ? $signed=1 : $signed=0;
    	$correct = Carbon::now('GMT+8'); 

    	//
    	$finance = Campaign::where('name','Finance')->first();
    	$financeTeam = collect(DB::table('team')->where('campaign_id',$finance->id)->select('team.user_id')->get())->pluck('user_id')->toArray();
      	(in_array($this->user->id, $financeTeam)) ? $isFinance= 1 : $isFinance=0;

    	($this->user->userType_id == 1 || $this->user->userType_id == 6 || $this->user->userType_id == 14 ) ? $isAllowed = true : $isAllowed=false;

    	if($isAllowed || $this->user->id == $userID || $isFinance)
    	{

    		$item = UserForms::where('user_id',$userID)->where('formType',$type)->where('isSigned',$signed)->orderBy('id','DESC')->get();
    		if(count($item) > 0){

    			//log access
    			$l = new UserForm_Access;
    			$l->user_formID = $item->first()->id;
    			$l->accessedBy = $this->user->id;
    			$l->created_at = $correct->format('Y-m-d H:i:s');
    			$l->updated_at = $correct->format('Y-m-d H:i:s');
    			$l->save();
    			//return $l;
    			return response()->file(storage_path('uploads/forms/finance/'.$item->first()->filename));
    		}
    		else return view('empty');

    	} 
    	else{


	        //if($this->user->id !== 564 ) {
	            $file = fopen('storage/uploads/log.txt', 'a') or die("Unable to open logs");
	            fwrite($file, "-------------------\n Attempt UserForm View - ". $correct->format('M d h:i A'). " by [". $this->user->id."] ".$this->user->lastname."\n");
	            fclose($file);
	        //}
	            return view('access-denied');
    	} 

        
        //return redirect(asset('storage/resources/'.$item->link)); //response()->json($item);
        //return response()->download(storage_path('/resources/'.$item->link));
        

    }
}
