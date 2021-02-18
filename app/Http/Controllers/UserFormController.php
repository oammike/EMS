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
use ZipArchive;
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
use OAMPI_Eval\UserForm_DisqFiling;
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

    public function index()
    {
        $correct = Carbon::now('GMT+8');
        $finance = Campaign::where('name','Finance')->first();
        $financeTeam = collect(DB::table('team')->where('campaign_id',$finance->id)->select('team.user_id')->get())->pluck('user_id')->toArray();
        (in_array($this->user->id, $financeTeam)) ? $isFinance= 1 : $isFinance=0;
        
        ($this->user->userType_id == 1 || $this->user->userType_id == 6 || $this->user->userType_id == 14 || ($this->user->userType_id==3 && $isFinance ) ) ? $canBIR=true : $canBIR=false;

        if($canBIR) {
            $file = fopen('storage/uploads/log.txt', 'a') or die("Unable to open logs");
                fwrite($file, "-------------------\n IDXview BIR - ". $correct->format('M d h:i A'). " by [". $this->user->id."] ".$this->user->lastname."\n");
                fclose($file);

            return view('forms.userForms-index');
        } 
        else
            return view('access-denied');

    }

    public function allPending()
    {
        DB::connection()->disableQueryLog();
        $allDisq = DB::table('users')->where('users.has2316',1)->where('users.hasSigned2316',null)->
                        leftJoin('team','team.user_id','=','users.id')->
                        leftJoin('campaign','campaign.id','=','team.campaign_id')->
                        select('users.employeeCode', 'users.lastname','users.firstname','users.nickname', 'users.id as userID','campaign.name as program')->
                        where([
                          ['users.status_id', '!=', 6],
                          ['users.status_id', '!=', 7],
                          ['users.status_id', '!=', 8],
                          ['users.status_id', '!=', 9],
                          ['users.status_id', '!=', 13],
                          ['users.status_id', '!=', 16],
                          ['team.floor_id', '!=',10],
                          ['team.floor_id', '!=',11],
                        ])->orderBy('users.lastname','ASC')->get();

        /*
                        leftJoin('positions','users.position_id','=','positions.id')->
                        leftJoin('team','team.user_id','=','users.id')->
                        leftJoin('campaign','team.campaign_id','=','campaign.id')->
                        select('users.id','users.lastname','users.firstname','users.nickname','campaign.name as program','positions.name as jobTitle', 'user_formDisqFiling.reasonID','user_formDisqFiling.created_at as dateSubmitted')->get();*/
        //return $allDisq;

        return view('forms.userForms-pendings',compact('allDisq')); 

    }

    public function auditTrail()
    {
        $correct = Carbon::now('GMT+8');
        $finance = Campaign::where('name','Finance')->first();
        $financeTeam = collect(DB::table('team')->where('campaign_id',$finance->id)->select('team.user_id')->get())->pluck('user_id')->toArray();
        (in_array($this->user->id, $financeTeam)) ? $isFinance= 1 : $isFinance=0;
        
        ($this->user->userType_id == 1 || $this->user->userType_id == 6 || $this->user->userType_id == 14 || ($this->user->userType_id==3 && $isFinance ) ) ? $canBIR=true : $canBIR=false;

        if($canBIR) {
            $file = fopen('storage/uploads/log.txt', 'a') or die("Unable to open logs");
                fwrite($file, "-------------------\n Trail BIR - ". $correct->format('M d h:i A'). " by [". $this->user->id."] ".$this->user->lastname."\n");
                fclose($file);

            DB::connection()->disableQueryLog();
            
            $allAccessed = DB::table('user_formAccess')->
            join('users','user_formAccess.accessedBy','=','users.id')->
            leftJoin('team','team.user_id','=','user_formAccess.accessedBy')->
            join('campaign','campaign.id','=','team.campaign_id')->
            select('users.firstname','users.lastname','users.nickname', 'campaign.name as program','campaign.id as campID', 'user_formAccess.user_formID as formID','user_formAccess.accessedBy','user_formAccess.created_at')->
            // where(
            //     'user_forms.isSigned',1
            // )->
            // where(
            //         ['users.hasSigned2316',1],
            //         ['user_forms.isSigned',1])->
            orderBy('user_formAccess.created_at','DESC')->get();
            
            $allForms = DB::table('user_forms')->
            join('users','user_forms.user_id','=','users.id')->
            leftJoin('team','team.user_id','=','user_forms.user_id')->
            join('campaign','campaign.id','=','team.campaign_id')->
            select('user_forms.id', 'users.id as ownerID','users.firstname as ownerFname','users.lastname as ownerLname','users.nickname as ownerNick',  'user_forms.filename')->get();
            // where(
            //     'user_forms.isSigned',1
            // )->
            // where(
            //         ['users.hasSigned2316',1],
            //         ['user_forms.isSigned',1])->
            //orderBy('user_forms.created_at','DESC')->get();




            return view('forms.userForms-auditTrail',compact('allForms','allAccessed'));
        } 
        else
            return view('access-denied');
        
    }

    public function bulkCreate(Request $request)
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

        $finance = Campaign::where('name','Finance')->first();
        $financeTeam = collect(DB::table('team')->where('campaign_id',$finance->id)->select('team.user_id')->get())->pluck('user_id')->toArray();
        (in_array($this->user->id, $financeTeam)) ? $isFinance= 1 : $isFinance=0;
        
        ($this->user->userType_id == 1 || $this->user->userType_id == 6 || $this->user->userType_id == 14 || ($this->user->userType_id==3 && $isFinance ) ) ? $canBIR=true : $canBIR=false;

                
            
                 

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

            if(!$canBIR) return view('access-denied');
            else
                return view('forms.userForms_bulk',compact('user','isAllowed','isSigned','formType'));

        }
        
         
        
    }

    public function bulkUploadFile(Request $request)
    {
        
        $attachments = $request->file('attachments');
        

        $today = Carbon::now('GMT+8')->format('Y-m-d_H_i_s');
        $destinationPath = storage_path() . '/uploads/forms/finance';
        //$destinationPath2 = storage_path() . '/uploads/forms/finance/bulk';
        $extension = Input::file('attachments')->getClientOriginalExtension(); // getting image extension
        if($request->isSigned)
        $fileName = $today.'-user-'.$request->userid.'-'.$request->formType.'_signed.'.$extension; // renameing image
        else
        $fileName = $today.'-user-'.$request->userid.'-'.$request->formType.'.'.$extension; // renameing image

        $attachments->move($destinationPath, $fileName); // uploading file to given path

        $path = $destinationPath."/".$fileName;

        $zip = new ZipArchive;
        $zipped = new Collection;
        $uploadedEmp = new Collection;

        if ($zip->open($path) === true) {
            for($i = 0; $i < $zip->numFiles; $i++) {
                $filename = $zip->getNameIndex($i);
                $fileinfo = pathinfo($filename);
                $zipped->push($filename);

                //process the files and associate it to corresponding employee
                if ( strpos($filename, "__") === false )
                {
                    $e = explode(".", $filename);
                    $emp = User::where('employeeCode',$e[0])->get();

                    if(count($emp) > 0)
                    {
                        $employee = $emp->first();
                        $uf = new UserForms;
                        $uf->user_id = $employee->id;

                        if($request->formType == "BIR2316_NQ")
                        {
                            $uf->formType = 'BIR2316';
                            $employee->disqForFiling = 1;
                        }else
                        {
                            $uf->formType = $request->formType;
                        }
                        
                        $uf->isSigned = $request->isSigned;


                        $uf->filename = $filename;
                        $uf->userUploader = $this->user->id;
                        $uf->save();

                        //update User table
                        //$u = User::find($request->userid);
                        ($request->isSigned) ? $employee->hasSigned2316 = 1 : $employee->has2316 = 1;

                        /*if($request->isSigned){

                            if($request->formType == 'BIR2316') { $employee->hasSigned2316 = 1; } 
                            if($request->formType == 'BIR2316') { $employee->hasSigned2316_NQ = 1; }


                        }else{
                            if($request->formType == 'BIR2316') { $employee->has2316 = 1; } 
                            if($request->formType == 'BIR2316') { $employee->has2316_NQ = 1; }

                           
                        } */

                       
                        $employee->push();

                        $uploadedEmp->push(['employee'=>$e, 'file'=>$filename]);

                    }

                    

                }
                //copy("zip://".$path."#".$filename, "/your/new/destination/".$fileinfo['basename']);
            }

            $zip->extractTo($destinationPath);
            $zip->close();                  
        }

            
              
              

            
        /* -------------- log updates made --------------------- */
        $file = fopen('storage/uploads/log.txt', 'a') or die("Unable to open logs");
        fwrite($file, "\n-------------------\n ".$request->formType." UP : ". $fileName ."  by [". $this->user->id."], ".$this->user->lastname."\n");
        fclose($file);

        

        

        return response()->json(['success'=>1,'zipped'=>$zipped, 'uploadedEmp'=>$uploadedEmp]);


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

    public function deleteSignedForm(Request $request)
    {
        $f = UserForms::where('user_id',$request->userID)->where('formType',$request->formType)->where('isSigned',$request->isSigned)->orderBy('id','DESC')->get();
        

        foreach ($f as $v) {
            $a = UserForm_Access::where('user_formID',$v->id)->delete();

            // foreach ($a as $k) {
            //     $k->delete();
            // }
            $v->delete();
        }

        $u = User::find($request->userID);
        $u->hasSigned2316 = null;
        $u->push();

        return redirect()->back();

    }

    public function disqualifyForFiling(Request $request)
    {
        $disqualify = new UserForm_DisqFiling;
        $disqualify->user_id = $this->user->id;

        /* REASONS
        1 = Individuals deriving other non-business, non-profession-related income in addition to compensation not otherwise subject to final tax.
        2 = Individuals deriving purely compensation income from a single employer, although the income of which has been correctly subjected to withholding tax, but whose spouse is not entitled to substituted filing. 
        3 = Non-resident aliens engaged in trade or business in the Philippines deriving purely compensation income or compensation income and other business or profession related income.
        */

        $disqualify->reasonID = $request->reason;
        $correct = Carbon::now('GMT+8'); 
        $disqualify->created_at = $correct->format('Y-m-d H:i:s');
        $disqualify->updated_at = $correct->format('Y-m-d H:i:s');
        $disqualify->save();

        //update employee user table
        $u = User::find($this->user->id);
        $u->disqForFiling = 1;
        $u->push();

        return response()->json(['success'=>1,'data'=>$disqualify]);


    }

    public function downloadUserForm(Request $request)
    {
        
        ($request->u) ? $userID=$request->u : $userID= $this->user->id;
        $user = User::find($userID);
        ($request->f) ? $formType = $request->f : $formType = "BIR2316";
        ($request->s) ? $signed=1 : $signed=0;
        $correct = Carbon::now('GMT+8'); 

        //
        $finance = Campaign::where('name','Finance')->first();
        $financeTeam = collect(DB::table('team')->where('campaign_id',$finance->id)->select('team.user_id')->get())->pluck('user_id')->toArray();
        (in_array($this->user->id, $financeTeam)) ? $isFinance= 1 : $isFinance=0;

        ($this->user->userType_id == 1 || $this->user->userType_id == 6 || $this->user->userType_id == 14 ) ? $isAllowed = true : $isAllowed=false;

        if($isAllowed || $this->user->id == $userID || $isFinance)
        {

            $item = UserForms::where('user_id',$userID)->where('formType',$formType)->where('isSigned',$signed)->orderBy('id','DESC')->get();
            if(count($item) > 0){

                //log access
                /*$l = new UserForm_Access;
                $l->user_formID = $item->first()->id;
                $l->accessedBy = $this->user->id;
                $l->created_at = $correct->format('Y-m-d H:i:s');
                $l->updated_at = $correct->format('Y-m-d H:i:s');
                $l->save();*/
                //return $l;
                $employee = User::find($userID);

                if($employee->disqForFiling)
                    return view('forms.userForms_downloadDisq',compact('user','isAllowed','signed','formType'));
                else
                    return view('forms.userForms_download',compact('user','isAllowed','signed','formType'));
                //return response()->file(storage_path('uploads/forms/finance/'.$item->first()->filename));
            }
            else return view('empty');

        } 
        else{


            //if($this->user->id !== 564 ) {
                $file = fopen('storage/uploads/log.txt', 'a') or die("Unable to open logs");
                fwrite($file, "-------------------\n Attempt UserForm View[".$userID."] - ". $correct->format('M d h:i A'). " by [". $this->user->id."] ".$this->user->lastname."\n");
                fclose($file);
            //}
                return view('access-denied');
        } 

        
        //return redirect(asset('storage/resources/'.$item->link)); //response()->json($item);
        //return response()->download(storage_path('/resources/'.$item->link));
        

    }

    public function getAllFormAccess()
    {
        DB::connection()->disableQueryLog();
        $correct = Carbon::now('GMT+8');
        $finance = Campaign::where('name','Finance')->first();
        $financeTeam = collect(DB::table('team')->where('campaign_id',$finance->id)->select('team.user_id')->get())->pluck('user_id')->toArray();
        (in_array($this->user->id, $financeTeam)) ? $isFinance= 1 : $isFinance=0;
        
        ($this->user->userType_id == 1 || $this->user->userType_id == 6 || $this->user->userType_id == 14 || ($this->user->userType_id==3 && $isFinance ) ) ? $canBIR=true : $canBIR=false;

        if($canBIR){
                $allAccessed = DB::table('user_formAccess')->
                join('users','user_formAccess.accessedBy','=','users.id')->
                leftJoin('team','team.user_id','=','user_formAccess.accessedBy')->
                join('campaign','campaign.id','=','team.campaign_id')->
                select('users.firstname','users.lastname','users.nickname', 'campaign.name as program','campaign.id as campID', 'user_formAccess.user_formID as formID','user_formAccess.accessedBy','user_formAccess.created_at')->
                // where(
                //     'user_forms.isSigned',1
                // )->
                // where(
                //         ['users.hasSigned2316',1],
                //         ['user_forms.isSigned',1])->
                orderBy('user_formAccess.created_at','DESC')->get();
                
                $allForms = DB::table('user_forms')->
                join('users','user_forms.user_id','=','users.id')->
                leftJoin('team','team.user_id','=','user_forms.user_id')->
                join('campaign','campaign.id','=','team.campaign_id')->
                select('user_forms.id', 'users.id as ownerID','users.firstname as ownerFname','users.lastname as ownerLname','users.nickname as ownerNick',  'user_forms.filename')->get();
                // where(
                //     'user_forms.isSigned',1
                // )->
                // where(
                //         ['users.hasSigned2316',1],
                //         ['user_forms.isSigned',1])->
                //orderBy('user_forms.created_at','DESC')->get();


        }else $allAccessed=new Collection;

        //return $allForms;

       
        
        return response()->json(['data'=>$allAccessed,'forms'=>$allForms]);
    }

    public function getAllForms()
    {
        DB::connection()->disableQueryLog();
        $correct = Carbon::now('GMT+8');
        $finance = Campaign::where('name','Finance')->first();
        $financeTeam = collect(DB::table('team')->where('campaign_id',$finance->id)->select('team.user_id')->get())->pluck('user_id')->toArray();
        (in_array($this->user->id, $financeTeam)) ? $isFinance= 1 : $isFinance=0;
        
        ($this->user->userType_id == 1 || $this->user->userType_id == 6 || $this->user->userType_id == 14 || ($this->user->userType_id==3 && $isFinance ) ) ? $canBIR=true : $canBIR=false;

        if($canBIR)
        $all = DB::table('user_forms')->
                join('users','user_forms.user_id','=','users.id')->
                leftJoin('team','team.user_id','=','user_forms.user_id')->
                join('campaign','campaign.id','=','team.campaign_id')->
                select('users.id','users.firstname','users.lastname','users.nickname', 'campaign.name as program','campaign.id as campID', 'user_forms.id as formID','users.has2316','users.hasSigned2316','users.disqForFiling','user_forms.isSigned', 'user_forms.filename','user_forms.created_at')->
                where(
                    'user_forms.isSigned',1
                )->
                // where(
                //         ['users.hasSigned2316',1],
                //         ['user_forms.isSigned',1])->
                orderBy('user_forms.created_at','DESC')->get();
        else $all=new Collection;
        
        return response()->json(['data'=>$all]);
    }

    public function uploadFile(Request $request)
    {
    	$correct = Carbon::now('GMT+8'); 
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
        $uf->created_at = $correct->format('Y-m-d H:i:s');
        $uf->updated_at = $correct->format('Y-m-d H:i:s');
    	$uf->save();

    	//update User table
    	$u = User::find($request->userid);
    	($request->isSigned) ? $u->hasSigned2316 = 1 : $u->has2316 = 1;

    	$u->push();

    	return response()->json(['success'=>1,'form'=>$uf]);


    }

    public function userTriggered()
    {
        DB::connection()->disableQueryLog();
        $allDisq = DB::table('user_formDisqFiling')->leftJoin('users','user_formDisqFiling.user_id','=','users.id')->
                        leftJoin('positions','users.position_id','=','positions.id')->
                        leftJoin('team','team.user_id','=','users.id')->
                        leftJoin('campaign','team.campaign_id','=','campaign.id')->
                        select('users.id','users.lastname','users.firstname','users.nickname','campaign.name as program','positions.name as jobTitle', 'user_formDisqFiling.reasonID','user_formDisqFiling.created_at as dateSubmitted')->get();

        return view('forms.userForms-disq',compact('allDisq')); 

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
            //return $item;
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
	            fwrite($file, "-------------------\n Attempt UserForm View[".$userID."] - ". $correct->format('M d h:i A'). " by [". $this->user->id."] ".$this->user->lastname."\n");
	            fclose($file);
	        //}
	            return view('access-denied');
    	} 

        
        //return redirect(asset('storage/resources/'.$item->link)); //response()->json($item);
        //return response()->download(storage_path('/resources/'.$item->link));
        

    }
}
