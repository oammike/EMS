<?php

namespace OAMPI_Eval\Http\Controllers;


use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use \Response;
use \DB;


use OAMPI_Eval\Http\Requests;
use OAMPI_Eval\User;
use OAMPI_Eval\Role;
use OAMPI_Eval\UserType_Roles;
use OAMPI_Eval\Campaign;
use OAMPI_Eval\CampaignLogos;
use OAMPI_Eval\Status;
use OAMPI_Eval\UserType;
use OAMPI_Eval\Position;
use OAMPI_Eval\ImmediateHead;
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
use OAMPI_Eval\Team;
use OAMPI_Eval\ImmediateHead_Campaign;

class CampaignController extends Controller
{
    protected $user;
    protected $campaign;

    public function __construct(Campaign $campaign)
    {
        $this->middleware('auth');
        $this->user =  User::find(Auth::user()->id);
        $this->campaign = $campaign;
    }

     public function index()
    {

        $roles = UserType::find($this->user->userType_id)->roles->pluck('label'); //->where('label','MOVE_EMPLOYEES');
        $canDelete =  ($roles->contains('DELETE_EMPLOYEE')) ? '1':'0';


        $allcampaigns = Campaign::orderBy('name','ASC')->get();

        $allcamp = $allcampaigns->filter(function($c){
            return $c->name !== "" && $c->name !=' ';

        });

        $campaigns = new Collection;
        $coll = new Collection;

        foreach ($allcamp as $camp) {
            $leaders = new Collection;

            /*
            $leaders = Team::where('campaign_id',$camp->id)->get()->groupBy('immediateHead_id' );//$camp->leaders;
            $teams = new Collection;
            

            foreach ($leaders as $lead) {
                   $theLeader = ImmediateHead::find($lead->first()->immediateHead_id);
                  // $member = User::find()


                   $teams->push(['tl'=>$theLeader, 'userData'=>$theLeader->userData]);
            }*/

            $leads = $camp->leaders;
            $l = $leads->filter(function($e){ return $e->firstname != '';});
            $lead = new Collection;




            foreach ($l as $ldr) {
                $checkActive = User::where('employeeNumber',$ldr->employeeNumber)->first();
               //$coll->push(['employeeNumber'=>$ldr->employeeNumber, 'checkActive'=>$checkActive]);
               // if ($checkActive->status_id !== 7 && $checkActive->status_id !== 8 && $checkActive->status_id !== 9)
                    $lead->push($ldr);
            }



            foreach ($lead->sortBy('lastname') as $l) {

                //$members = DB::table('users')->leftJoin('team','users.id','=','team.user_id')->get();
                $team = ImmediateHead_Campaign::where('immediateHead_id',$l->id)->where('campaign_id',$camp->id)->first();
                $ih = ImmediateHead::find($team->immediateHead_id);
                $members = DB::table('team')->where('ImmediateHead_Campaigns_id','=',$team->id)->leftJoin('users','team.user_id','=','users.id')->where('users.employeeNumber', '!=',$ih->employeeNumber)
                                ->where('users.status_id','!=','7')->where('users.status_id','!=','8')->where('users.status_id','!=',9)
                                ->select('users.id','users.lastname','users.firstname','users.middlename','users.position_id')->orderBy('lastname')->get();

             
               $leaderInfo = User::where('employeeNumber',$l->employeeNumber)->first();

               if (!empty($leaderInfo) && $team->disabled == null )
               {
                    //RESIGNED || TERMINATED || ENDO
                   if($leaderInfo->status_id != 7 && $leaderInfo->status_id != 8 && $leaderInfo->status_id != 9)
                   {
                        $leaders->push([
                         'tl'=> $l->lastname.", ". $l->firstname,
                         'id'=> $leaderInfo['id'], 
                         'employeeNumber'=> $l->employeeNumber, 
                         'position'=>$leaderInfo['position']['name'],
                         'members'=>$members,
                         'ImmediateHead_Campaigns_id' => $team->id,
                         //'position'=>$leaderInfo['position']['name'], 
                         //'members'=>$teams
                      ]);

                   }

               }
      
               
            }
            $logo = null;

            $logo = $camp->logo;


             
            $campaigns->push(['id'=>$camp->id, 'name'=>$camp->name, 'leaders'=>$leaders, 'logo'=>$logo ]); //$leaders, "members"=> $teams
        }
        //return $campaigns;

       
       
        return view('people.campaigns', compact('campaigns','canDelete'));
        //return $campaigns;
    }

    

    public function getAllCampaigns()
    {

        $exclude = Input::get('except');

        if ($exclude){
            $campaigns = Campaign::where('id', '!=', $exclude)->orderBy('name','ASC')->get();


        }else {
            $campaigns = Campaign::orderBy('name','ASC')->get();
        }

        $campaign = $campaigns->filter(function($c){
            return $c->name != '' && $c->name != ' '; 
        });

        return response()->json($campaign);


    }

    public function getAllLeaders($id)
    {

        //$leaders = $this->campaign->find($id)->leaders->sortBy('lastname');
        $tls = $this->campaign->find($id)->leaders->sortBy('lastname');
        //return response()->json($tls->sortByDesc('id'));
        //return $tls;
        $tl = $tls->filter(function ($t){ return $t->lastname != '';});

        $coll = new Collection;

        foreach ($tl as $key) {

            $tlid = ImmediateHead_Campaign::where('campaign_id',$id)->where('immediateHead_id',$key->id)->first();
            $coll->push(['id'=>$tlid->id, 'lastname'=>$key->lastname, 'firstname'=>$key->firstname]);
        }

        //return $tls;
        return $coll;
       // return response()->json( array_values($tl) );
        //return Response::json( array_values($tl) );

    }

    public function create()
    {
        //if ($this->user->userType_id == 1 || $this->user->userType_id == 2)
        $canCreate = UserType::find($this->user->userType_id)->roles->where('label','ADD_NEW_PROGRAM');
        
        if ($canCreate->isEmpty())

        {
            return view("access-denied");
            
        } else  return view('people.campaign-new'); 
        

    }

    public function store(Request $request)
    {
        $camp = new Campaign;
        $camp->name = $request->name;
        $camp->save();
        return response()->json($camp);

    }
    public function show($id)
    {

    }

    public function destroy($id)
    {
         $canDoThis = UserType::find($this->user->userType_id)->roles->where('label','DELETE_PROGRAM');

         if ($canDoThis->isEmpty())
        {
             return view("access-denied");

        } else {
            $this->campaign->destroy($id);
            return back();

        }
        

    }

    public function update($id)
    {

    }
}
