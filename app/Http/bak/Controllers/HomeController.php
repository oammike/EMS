<?php

namespace OAMPI_Eval\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use \DB;
use \Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use OAMPI_Eval\Http\Requests;
use OAMPI_Eval\User;
use OAMPI_Eval\UserType;
use OAMPI_Eval\Team;
use OAMPI_Eval\ImmediateHead;
use OAMPI_Eval\Position;
use OAMPI_Eval\Status;
use OAMPI_Eval\EvalType;
use OAMPI_Eval\EvalSetting;
use OAMPI_Eval\EvalForm;
use OAMPI_Eval\Movement;
use OAMPI_Eval\Movement_ImmediateHead;
use OAMPI_Eval\ImmediateHead_Campaign;
use OAMPI_Eval\Notification;
use OAMPI_Eval\User_Notification;
use OAMPI_Eval\User_CWS;
use OAMPI_Eval\User_DTRP;
use OAMPI_Eval\User_OT;
use OAMPI_Eval\Biometrics;
use OAMPI_Eval\NotifType;


class HomeController extends Controller
{
    protected $user;
    protected $userNotifs;
    use Traits\UserTraits;
    

    public function __construct()
    {
        $this->middleware('auth');
        $this->user =  User::find(Auth::user()->id);
        $this->userNotifs = $this->user->notifications();
        
    }

    
    public function index()
    {
        
       
       $coll = new Collection;

       $leadershipcheck = ImmediateHead::where('employeeNumber', $this->user->employeeNumber)->first();

       if (empty($leadershipcheck)) {
            $myCampaign = $this->user->campaign;

            // ------ if an agent and not under HR dept or finance, dont show him the dashboard
            if (($myCampaign->first()->name !== "HR" || $myCampaign->first()->name !== "Finance") && $this->user->userType_id == '4') { 
                return redirect()->action('UserController@show',['id'=>$this->user->id]);}
                //return redirect('/myEvals'); }
       } 
       else { 
              $myCampaign = $leadershipcheck->myCampaigns;
            }

    


      //-- USER TRAIT
            $forApprovals = $this->getApprovalNotifs();
       

         
        $evalTypes = EvalType::all();
        //$evalSetting = EvalSetting::all()->first();
        // --------- temporarily we set it for Semi annual of July to Dec 
        $evalSetting = EvalSetting::find(2);

        
        $currentPeriod = Carbon::create((date("Y")-1), $evalSetting->startMonth, $evalSetting->startDate,0,0,0, 'Asia/Manila');
        $endPeriod = Carbon::create((date("Y")-1), $evalSetting->endMonth, $evalSetting->endDate,0,0,0, 'Asia/Manila');

        $doneEval = new Collection;
        $pendingEval = new Collection;

        
        $leadershipcheck = ImmediateHead::where('employeeNumber', $this->user->employeeNumber)->get();
        


        // ------------------------------------------------------------------------------ if user has no subordinates -----------
       
        
        
        if (( ($this->user->userType->name == "HR admin") && count($leadershipcheck)==0 ) || ($this->user->userType->name == "agent") )
            

        { //  AGENT or ADMIN pero agent level
            $employee = User::find($this->user->id);
            $meLeader = $employee->supervisor->first(); 
            //return redirect()->route('user.show',['id'=>$this->user->id]);
            return redirect('/myEvals');
            //return view('dashboard-agent', compact('evalTypes', 'evalSetting', 'employee', 'myCampaign'));
            


        // ------------------------------------------------------------------------------ endif user has no subordinates -----------

        } else {

            //-- Initialize Approvals Dashlet

           //return $forApprovals; 

            return view('dashboard', compact('forApprovals', 'unseenNotifs', 'mySubordinates', 'currentPeriod','endPeriod', 'myCampaign', 'evalTypes', 'evalSetting'));
            //return $pass = bcrypt('emelda'); $2y$10$IQqrVA8oK9uedQYK/8Z4Ae9ttvkGr/rGrwrQ6JVKdobMBt/5Mj4Ja


        } 

  
        
    }

    public function module()
    {
        return view('under-construction');
    }
}
