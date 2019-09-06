<?php

namespace OAMPI_Eval\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Yajra\Datatables\Facades\Datatables;
use \Mail;
use \PDF;
use \DB;
use Carbon\Carbon;

use OAMPI_Eval\Http\Requests;
use OAMPI_Eval\User;
use OAMPI_Eval\User_Notification;
use OAMPI_Eval\Notification;
use OAMPI_Eval\UserType;
use OAMPI_Eval\Team;
use OAMPI_Eval\Floor;
use OAMPI_Eval\ImmediateHead;
use OAMPI_Eval\ImmediateHead_Campaign;
use OAMPI_Eval\EvalType;
use OAMPI_Eval\RatingScale;
use OAMPI_Eval\EvalSetting;
use OAMPI_Eval\EvalForm;
use OAMPI_Eval\EvalDetail;
use OAMPI_Eval\Competency;
use OAMPI_Eval\Competency__Attribute;
use OAMPI_Eval\Attribute;
use OAMPI_Eval\Summary;
use OAMPI_Eval\PerformanceSummary;
use OAMPI_Eval\Movement;
use OAMPI_Eval\Manpower;
use OAMPI_Eval\Campaign;
use OAMPI_Eval\Position;
use OAMPI_Eval\Status;
use OAMPI_Eval\PersonnelChange;
use OAMPI_Eval\Movement_ImmediateHead;
use OAMPI_Eval\Movement_Positions;
use OAMPI_Eval\Movement_Status;
use OAMPI_Eval\User_Leader;

class ManpowerController extends Controller
{
    protected $user;
    protected $manpower;

    public function __construct(Manpower $manpower)
    {
        $this->middleware('auth');
        $this->user =  User::find(Auth::user()->id);
        $this->manpower = $manpower;
    }

    public function index()
    {
        $correct = Carbon::now('GMT+8');
    	$personnel = $this->user;
        $hr = Campaign::where('name',"HR")->first();
        $team = Team::where('user_id',$personnel->id)->where('campaign_id',$hr->id)->get();
        (count($team) > 0) ? $canDelete=true : $canDelete=false;

        $allStatus = Status::all();
        $foreignStatus = DB::table('manpower_foreignStatus')->select('id','name')->get(); //return collect($foreignStatus)->where('id',1);
        $progress = DB::table('manpower_progress')->select('id','name')->get();
        $allRequests = DB::table('manpower')->join('users','manpower.user_id','=','users.id')->
                            join('campaign','manpower.campaign_id','=','campaign.id')->
                            join('manpower_reason','manpower.manpower_reason_id','=','manpower_reason.id')->
                            join('manpower_type','manpower.manpower_type_id','=','manpower_type.id')->
                            join('manpower_source','manpower.manpower_source_id','=','manpower_source.id')->
                            join('positions','manpower.position_id','=','positions.id')->
                            //leftJoin('manpower_status','manpower.manpower_status_id','=','manpower_status.id')->
                            join('manpower_progress','manpower.progress_id','=','manpower_progress.id')->
                            //join('statuses','manpower_status.status_id','=','statuses.id')->
                            //join('manpower_foreignStatus','manpower.manpower_foreignStatus_id','=','manpower_foreignStatus.id')->
                            select('manpower.id', 'users.firstname','users.nickname','users.lastname','users.id as userID','campaign.name as program','campaign.id as programID','manpower_reason.name as reason','manpower_type.name as type','manpower_source.name as source','manpower.manpower_status_id as status','positions.name as jobTitle','manpower.manpower_foreignStatus_id as foreignStatus','manpower.trainingStart','manpower.howMany','manpower.currentCount', 'manpower.notes', 'manpower_progress.name as progress','manpower.lob','manpower_progress.id as progressID','manpower.created_at','manpower.mktgBoost')->get();

        if($this->user->id !== 564)
        {
            $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
            //fwrite($file, "-------------------\n Email sent to ". $newTL->userData->email."\n");
            fwrite($file, "\n Manpower index by: [". $this->user->id."] ". $this->user->lastname." on ". $correct->format('Y-m-d H:i:s')."\n");
            fclose($file);
        }
                            //return $allRequests[0];

        return view('people.manpower-index', compact('personnel','allRequests','progress','allStatus','foreignStatus','canDelete'));
    }

    public function create()
    {
        DB::connection()->disableQueryLog();
        $correct = Carbon::now('GMT+8');

    	$personnel = $this->user;
        $reasons = DB::table('manpower_reason')->select('id','name')->get(); 
        $types = DB::table('manpower_type')->select('id','name')->get(); 
        $sources = DB::table('manpower_source')->select('id','name')->get();
        $programs = DB::table('campaign')->select('id','name','hidden')->where('hidden',null)->orderBy('name','ASC')->get(); 
        $positions =  DB::table('positions')->select('id','name')->orderBy('name','ASC')->get(); 
        $foreign = DB::table('manpower_foreignStatus')->select('name','id')->get();
        $statuses = DB::table('manpower_status')->
                        join('statuses','manpower_status.status_id','=','statuses.id')->select('statuses.name','statuses.id')->
                        orderBy('name','ASC')->get();

        if($this->user->id !== 564)
        {
            $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
            //fwrite($file, "-------------------\n Email sent to ". $newTL->userData->email."\n");
            fwrite($file, "\n Manpower create by: [". $this->user->id."] ". $this->user->lastname." on ". $correct->format('Y-m-d H:i:s')."\n");
            fclose($file);
        }

        
    	return view('people.manpower-create', compact('personnel','reasons','types','sources','programs','positions','statuses','foreign'));
    }

    public function deleteRequest(Request $request)
    {
        $req = Manpower::find($request->id);
        $req->delete();

        $correct = Carbon::now('GMT+8');

        if($this->user->id !== 564)
        {
            $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
            //fwrite($file, "-------------------\n Email sent to ". $newTL->userData->email."\n");
            fwrite($file, "\n Manpower del by: [". $this->user->id."] ". $this->user->lastname." on ". $correct->format('Y-m-d H:i:s')."\n");
            fclose($file);
        }
        return response()->json(['done'=>true, 'id'=>$request->id]);

    }


    public function saveRequest(Request $request)
    {
        $correct = Carbon::now('GMT+8');
        $req = new Manpower;
        $req->user_id = $this->user->id;
        $req->campaign_id = $request->campaign_id;
        $req->manpower_reason_id = $request->manpower_reason_id;
        $req->manpower_type_id = $request->manpower_type_id;
        $req->howMany = $request->howMany;
        $req->manpower_source_id = $request->manpower_source_id;
        $req->position_id = $request->position_id;
        $req->LOB = $request->lob;
        $req->manpower_status_id = $request->manpower_status_id;
        $req->manpower_foreignStatus_id = $request->manpower_foreignStatus_id;
        $req->trainingStart = date('Y-m-d',strtotime($request->trainingStart));
        $req->notes = $request->notes;
        $req->mktgBoost = $request->mktgBoost;
        $req->created_at = $correct->format('Y-m-d H:i:s');
        $req->updated_at = $correct->format('Y-m-d H:i:s');
        $req->save();


        // NOW, EMAIL team concerned
        $allStatus = Status::all();
        $foreignStatus = DB::table('manpower_foreignStatus')->select('id','name')->get();

        $request = DB::table('manpower')->where('manpower.id',$req->id)->
                        join('users','manpower.user_id','=','users.id')->
                        join('campaign','manpower.campaign_id','=','campaign.id')->
                        join('manpower_reason','manpower.manpower_reason_id','=','manpower_reason.id')->
                        join('manpower_type','manpower.manpower_type_id','=','manpower_type.id')->
                        join('manpower_source','manpower.manpower_source_id','=','manpower_source.id')->
                        join('positions','manpower.position_id','=','positions.id')->join('manpower_progress','manpower.progress_id','=','manpower_progress.id')->
                        select('manpower.id', 'users.firstname','users.nickname','users.lastname','users.id as userID','campaign.name as program','campaign.id as programID','manpower_reason.name as reason','manpower_type.name as type','manpower_source.name as source','manpower.manpower_status_id as status','positions.name as jobTitle','manpower.manpower_foreignStatus_id as foreignStatus','manpower.trainingStart','manpower.howMany','manpower.currentCount', 'manpower.notes', 'manpower_progress.name as progress','manpower.lob','manpower_progress.id as progressID','manpower.created_at','manpower.mktgBoost')->get();

        $employee = $this->user;

        if($req->mktgBoost)
            $HRs = [385,563,564,730]; //added Adrian
        else
            $HRs = [385,563,564]; //jaynee, ms.a 385

        foreach($HRs as $h)
        {
            $hr = User::find($h);                 
             Mail::send('emails.manpower', [ 'employee'=>$employee, 'request'=>$request,'allStatus'=>$allStatus, 'foreignStatus'=>$foreignStatus], function ($m) use ($hr, $employee,$correct,$request) 
             {
                $m->from('EMS@openaccessbpo.net', 'EMS | OAMPI Employee Management System');
                $m->to($hr->email, $hr->lastname.', '.$hr->firstname)->subject('Manpower Request - '. '('.$request[0]->howMany.') '. $request[0]->jobTitle );     

                                 
            

            }); //end mail

        }
        /* -------------- log updates made --------------------- */
                     $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
                        //fwrite($file, "-------------------\n Email sent to ". $newTL->userData->email."\n");
                        fwrite($file, "\n Manpower Req by: ". $this->user->firstname." ". $this->user->lastname." on ". $correct->format('Y-m-d H:i:s')."\n");
                        fclose($file);     
        

        return $req;

    }

    public function show($id)
    {
    	
    }

    public function updateCount(Request $request)
    {
        $correct = Carbon::now('GMT+8');
        $req = Manpower::find($request->id);
        $req->currentCount = $request->currentCount;
        //$req->created_at = $correct->format('Y-m-d H:i:s');
        $req->updated_at = $correct->format('Y-m-d H:i:s');

        $req->push();

        $correct = Carbon::now('GMT+8');

        if($this->user->id !== 564)
        {
            $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
            //fwrite($file, "-------------------\n Email sent to ". $newTL->userData->email."\n");
            fwrite($file, "\n Manpower upd-CNT by: [". $this->user->id."] ". $this->user->lastname." on ". $correct->format('Y-m-d H:i:s')."\n");
            fclose($file);
        }
        return $req;

    }

    public function updateNotes(Request $request)
    {
        $correct = Carbon::now('GMT+8');
        $req = Manpower::find($request->id);
        $req->notes = $request->notes;
        //$req->created_at = $correct->format('Y-m-d H:i:s');
        $req->updated_at = $correct->format('Y-m-d H:i:s');

        $req->push();

        $correct = Carbon::now('GMT+8');

        if($this->user->id !== 564)
        {
            $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
            //fwrite($file, "-------------------\n Email sent to ". $newTL->userData->email."\n");
            fwrite($file, "\n Manpower upd-NOTE by: [". $this->user->id."] ". $this->user->lastname." on ". $correct->format('Y-m-d H:i:s')."\n");
            fclose($file);
        }

        return $req;

    }

    public function updateRequest(Request $request)
    {
        $correct = Carbon::now('GMT+8');
        $req = Manpower::find($request->id);
        $req->progress_id = $request->progress;

        //** if completed na
        if($request->progress == 4)
            $req->currentCount = $req->howMany;

        //$req->created_at = $correct->format('Y-m-d H:i:s');
        $req->updated_at = $correct->format('Y-m-d H:i:s');
        $req->push();

        $correct = Carbon::now('GMT+8');

        if($this->user->id !== 564)
        {
            $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
            //fwrite($file, "-------------------\n Email sent to ". $newTL->userData->email."\n");
            fwrite($file, "\n Manpower upd-PROGRESS by: [". $this->user->id."] ". $this->user->lastname." on ". $correct->format('Y-m-d H:i:s')."\n");
            fclose($file);
        }

        return $req;

    }





}
