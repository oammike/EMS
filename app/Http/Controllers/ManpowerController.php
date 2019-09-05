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

    	$personnel = $this->user;
        return view('people.manpower-index', compact('personnel'));
    }

    public function create()
    {
        DB::connection()->disableQueryLog();

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

        
    	return view('people.manpower-create', compact('personnel','reasons','types','sources','programs','positions','statuses','foreign'));
    }

    public function saveRequest(Request $request)
    {
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
        $req->save();

        return $req;

    }

    public function show($id)
    {
    	
    }




}
