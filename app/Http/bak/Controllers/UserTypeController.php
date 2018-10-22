<?php

namespace OAMPI_Eval\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;

use OAMPI_Eval\Http\Requests;
use OAMPI_Eval\User;
use OAMPI_Eval\UserType;
use OAMPI_Eval\UserType_Roles;
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
use OAMPI_Eval\Campaign;
use OAMPI_Eval\Position;
use OAMPI_Eval\Status;
use OAMPI_Eval\Role;
use OAMPI_Eval\RoleType;
use OAMPI_EvalRoleType;
use OAMPI_Eval\PersonnelChange;
use OAMPI_Eval\Movement_ImmediateHead;
use OAMPI_Eval\Movement_Positions;
use OAMPI_Eval\Movement_Status;

class UserTypeController extends Controller
{
    protected $user;
   	protected $userType;

    public function __construct(UserType $userType)
    {
        $this->middleware('auth');
        $this->user =  User::find(Auth::user()->id);
        $this->userType = $userType;
    }

     public function index()
    {
         $canDoThis = UserType::find($this->user->userType_id)->roles->where('label','ADD_PERMISSIONS');
         

         if (!$canDoThis->isEmpty())
         {
            $allroles = Role::all(); //groupBy('roleType_id')->get();
             $allUserTypes = UserType::all();
            $grouped = $allroles->groupBy('roleType_id');

            $roles = new Collection;
            foreach($grouped as $r)
            {
                $roles->push(['name'=>RoleType::find($r[0]['roleType_id'])->name, 'items'=>$r]);

            }
            //return $roles;

            $userRoles = new Collection;
            foreach( $allUserTypes as $u)
            {
                $arr = array();
                foreach( $u->roles as $a ){
                    $arr[] = $a->id;

                }
                $userRoles->push(['userType'=>$u->name, 'description'=>$u->description, 'id'=>$u->id, 'roles'=>$arr]); //'description'=>$u->description,
            }
            return view('people.userType-index', compact('roles', 'allUserTypes', 'userRoles'));
            //return $userRoles;

         } else return view("access-denied");

    	 
    }

    public function create()
    {
    	$allroles = Role::all(); //groupBy('roleType_id')->get();
    	 $allUserTypes = UserType::all();
        $grouped = $allroles->groupBy('roleType_id');

        $roles = new Collection;
        foreach($grouped as $r)
        {
            $roles->push(['name'=>RoleType::find($r[0]['roleType_id'])->name, 'items'=>$r]);

        }
        //return $roles;

        $userRoles = new Collection;
        foreach( $allUserTypes as $u)
        {
        	$arr = array();
        	foreach( $u->roles as $a ){
        		$arr[] = $a->id;

        	}
        	$userRoles->push(['userType'=>$u->name, 'id'=>$u->id, 'roles'=>$arr]); //'description'=>$u->description,
        }
        return view('people.userType-create', compact('roles', 'allUserTypes', 'userRoles'));


    }

    public function store(Request $request)
    {

        //check first if already existing

        $existing = UserType::find($request->id);
        $roles = $request->roles;

        if ( count($existing) == 0 )
        {
            $userType = new UserType;
            $userType->name = $request->name;
            $userType->description = $request->description;
            $userType->save();


           

            foreach($roles as $role){
                $userType_Role = new UserType_Roles;
                $userType_Role->userType_id = $userType->id;
                $userType_Role->role_id = $role;
                $userType_Role->save();


            }

           

        } else 
        {
           

            foreach($roles as $role){

                $uRole = UserType_Roles::where('userType_id', $request->id)->where('role_id',$role)->get();

                if ( count($uRole) == 0  ){
                    $userType_Role = new UserType_Roles;
                    $userType_Role->userType_id = $request->id;
                    $userType_Role->role_id = $role;
                    $userType_Role->save();

                } 




            }



        }

         // now compare saved vs submitted values
         // and then delete those unchecked roles
          $saved = UserType_Roles::where('userType_id', $request->id)->get()->pluck('role_id')->toArray();
         

          if ( count($saved) !== count($roles) )
          {
            $differences = array_diff($saved, $roles);
            $data = array();

            foreach ($differences as $diff) {
                $todelete = UserType_Roles::where('userType_id', $request->id)->where('role_id',$diff)->first();
                UserType_Roles::destroy($todelete->id);
            }

             return response()->json($data);
          } else {
           $data = ['savedItems'=>count($saved), 'sentData'=>count($roles)];
            return response()->json($data);
          }
        
        
    }

    
}
