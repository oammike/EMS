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

class RoleController extends Controller
{
   protected $user;
   protected $role;

    public function __construct(Role $role)
    {
        $this->middleware('auth');
        $this->user =  User::find(Auth::user()->id);
        $this->role = $role;
    }

     public function index()
    {
        $canDoThis = UserType::find($this->user->userType_id)->roles->where('label','CREATE_NEW_ROLES');

        if (!$canDoThis->isEmpty())
        {
            $allroles = Role::all(); //groupBy('roleType_id')->get();
            $grouped = $allroles->groupBy('roleType_id');

            $roles = new Collection;
            foreach($grouped as $r)
            {
                $roles->push(['id'=>$r[0]['roleType_id'], 'name'=>RoleType::find($r[0]['roleType_id'])->name, 'items'=>$r]);

            }
            //return $roles;
            return view('people.user-roles', compact('roles'));

        } else return view("access-denied");

        
    }

    public function store(Request $request)
    {
    	$role = new Role;
    	$role->name = $request->name;
        $role->label = str_replace(" ", "_", strtoupper($request->name) );
        $role->roleType_id = $request->roleType_id;
    	$role->save();

        $response = ['id'=>$role->roleType_id, 'name'=>$role->name];

    	return response()->json($response);
    }

    public function create()
    {

    }
    public function show($id)
    {

    }

    public function destroy($id)
    {

    }

    public function update($id)
    {

    }
}
