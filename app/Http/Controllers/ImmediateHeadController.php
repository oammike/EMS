<?php

namespace OAMPI_Eval\Http\Controllers;

use \DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use OAMPI_Eval\Http\Requests;
use OAMPI_Eval\User;
use OAMPI_Eval\ImmediateHead;
use OAMPI_Eval\Campaign;
use OAMPI_Eval\Status;
use OAMPI_Eval\UserType;
use OAMPI_Eval\Position;
use OAMPI_Eval\ImmediateHead_Campaign;
use OAMPI_Eval\Team;



use Illuminate\Support\Facades\Input;

class ImmediateHeadController extends Controller
{
    protected $user;
    protected $immediateHead;

    public function __construct(ImmediateHead $immediateHead)
    {
        $this->middleware('auth');
        $this->user =  User::find(Auth::user()->id);
        $this->immediateHead = $immediateHead;
    }

    
    public function index()
    {
        
        $allhead = ImmediateHead::where('lastname','!=','')->orderBy('lastname', 'ASC')->get();
        DB::connection()->disableQueryLog();

        $campaigns = DB::table('campaign')->where([
                                                    ['name','!=',''],
                                                    ['name','!=',' '],
                    ])->orderBy('name','ASC')->get();

        $allhead = DB::table('immediateHead')->where('immediateHead.lastname','!=','')->
                        leftJoin('users','immediateHead.employeeNumber','=','users.employeeNumber')->
                        where([
                            ['status_id', '!=', 7],
                            ['status_id', '!=', 8],
                            ['status_id', '!=', 9],
                        ])->
                        join('positions','users.position_id','=','positions.id')->
                        join('immediateHead_Campaigns','immediateHead_Campaigns.immediateHead_id','=','immediateHead.id')->
                        join('campaign','immediateHead_Campaigns.campaign_id','=','campaign.id')->
                        select('users.nickname','users.firstname', 'users.lastname','positions.name as jobTitle', 'immediateHead.id as ihID','users.id as userID','campaign.name as program', 'campaign.id as programID')->
                        orderBy('users.lastname','ASC')->get();

        $heads = collect($allhead);
        //return $campaigns[0]->name;
        //return $heads->groupBy('userID');
        return view('people.immediateHead-index', compact('heads', 'campaigns'));


    }

    public function store(Request $request)
    {
        $leader = new ImmediateHead;
        $leader->employeeNumber = $request->employeeNumber;
        $leader->firstname = $request->firstname;
        $leader->lastname = $request->lastname;
        //$leader->campaign_id = $request->campaign_id;
        $leader->save();

        $leaderCampaign = new ImmediateHead_Campaign;
        $leaderCampaign->immediateHead_id = $leader->id;

        if (!empty($request->campaign_id)) $leaderCampaign->campaign_id = $request->campaign_id;
        else $leaderCampaign->campaign_id = Team::find(User::where('employeeNumber',$request->employeeNumber)->first()->id)->campaign_id; 
        
        $leaderCampaign->save();

        //update that employee's user type to LEADER as well
        $employee = User::where('employeeNumber',$leader->employeeNumber)->first();
        $employee->userType_id = 3;
        $employee->push();

        return response()->json($leader);

    }

    

    public function getMembers($id){

        $members = ImmediateHead::find($id)->subordinates->sortBy('lastname');
        return response()->json($members);


    }

    public function getOtherTeams()
    {

        $exclude = Input::get('except');


        if ($exclude){
            $excludedTeam = ImmediateHead::find($exclude);
            $teams = ImmediateHead::where('id', '!=', $exclude)->where('campaign_id', $excludedTeam->campaign_id)->orderBy('lastname','ASC')->get();

        }else {
            $teams = ImmediateHead::orderBy('lastname','ASC')->get();
        }

        return response()->json($teams);

    }



    public function create()
    {
        //$myCampaign = $this->user->campaign; 
        //$TLs = ImmediateHead::where('campaign_id', $myCampaign->id)->orderBy('lastname','ASC')->get();
        $allusers = User::orderBy('lastname', 'ASC')->get();
        
         $users = $allusers->filter(function ($employee)
                                                                                { //we only need those agents and admin employees that aren't leaders and not resigned
                                                                                    return ($employee->userType_id == 4 ||  $employee->userType_id == 2) && $employee->status_id !== 7 && $employee->status_id !== 8 && $employee->status_id !== 9;
                                                                                });
        
        
         DB::connection()->disableQueryLog();
         $users = DB::table('users')->where([
                            ['status_id', '!=', 7],
                            ['status_id', '!=', 8],
                            ['status_id', '!=', 9],
                            ['userType_id','!=',3],
                            

         ])->join('team','team.user_id','=','users.id')->
         join('campaign','team.campaign_id','=','campaign.id')->
         join('statuses','users.status_id','=', 'statuses.id')->
         select('users.id as id','users.firstname','users.nickname','users.lastname','users.employeeNumber','campaign.id as programID', 'campaign.name as program','statuses.name as status')->
         orderBy('users.lastname','ASC')->get();
         //return $users;

        return view('people.immediateHead-create', compact('users'));

    }
    public function show($id)
    {

    }
    public function edit($id)
    {

    }

    public function destroy($id)
    {
        //we need first to demote that leader back to agent status:

        $demoted = ImmediateHead::find($id);

        $employee = User::where('employeeNumber',$demoted->employeeNumber)->first();
        $employee->userType_id = 4; //back to agent access
        $employee->push();

        
        $this->immediateHead->destroy($id);
        return back();

    }

    public function update($id)
    {

    }

    
}
