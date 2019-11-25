<?php

namespace OAMPI_Eval\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Input;
use Carbon\Carbon;
use Excel;
use \DB;
use \PDF;
use \Mail;
use \App;
use \Response;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;

use OAMPI_Eval\Http\Requests;
use OAMPI_Eval\User;
use OAMPI_Eval\UserType;
use OAMPI_Eval\Campaign;
use OAMPI_Eval\Team;
use OAMPI_Eval\ImmediateHead;
use OAMPI_Eval\NewPA_Competencies;
use OAMPI_Eval\NewPA_Competency_Descriptor;
use OAMPI_Eval\NewPA_Components;
use OAMPI_Eval\NewPA_Form;
use OAMPI_Eval\NewPA_Form_Competencies;
use OAMPI_Eval\NewPA_Form_Components;
use OAMPI_Eval\NewPA_Form_Goal;
use OAMPI_Eval\NewPA_Goal;
use OAMPI_Eval\NewPA_Objective;
use OAMPI_Eval\NewPA_TeamSetting;
use OAMPI_Eval\NewPA_Type;

use OAMPI_Eval\ImmediateHead_Campaign;
use OAMPI_Eval\Movement_ImmediateHead;
use OAMPI_Eval\Movement_Positions;
use OAMPI_Eval\Position;
use OAMPI_Eval\Notification;
use OAMPI_Eval\User_Notification;

class NewPA_Form_Controller extends Controller
{
    protected $user;
    protected $newPA_form;
    use Traits\EvaluationTraits;

     public function __construct(NewPA_Form $newPA_form)
    {
        $this->middleware('auth');
        $this->user =  User::find(Auth::user()->id);
        $this->newPA_form = $newPA_form;
    }



    public function index()
    {
    	return view('evaluation.newPA-index');

    }

    public function create()
    {
    	$roles = NewPA_Type::all();
        $objectives = NewPA_Objective::all();
        $competencies = NewPA_Competencies::all();
    	return view('evaluation.newPA-create',compact('roles','objectives','competencies'));

    }

    public function getFormTypeSettings()
    {
        $id = Input::get('id');
        $data= DB::table('newPA_type')->where('newPA_type.id',$id)->
                join('newPA_form_components','newPA_form_components.typeID','=','newPA_type.id')->
                join('newPA_components','newPA_form_components.componentID','=','newPA_components.id')->
                join('newPA_form_competencies','newPA_form_competencies.typeID','=','newPA_type.id')->
                join('newPA_competencies','newPA_form_competencies.competencyID','=','newPA_competencies.id')->
                select('newPA_type.name as roleType','newPA_components.id as componentID', 'newPA_components.name as component','newPA_form_components.weight as componentWeight','newPA_form_competencies.id as competencyID','newPA_competencies.name as competency','newPA_form_competencies.weight as competencyWeight')->get();
        $data_components = collect($data)->groupBy('component');
        $data_competencies = collect($data)->groupBy('competency');

        // $data_competencies = DB::table('newPA_type')->where('newPA_type.id',$id)->
        //                     join()
        return response()->json(['components'=>$data_components,'competencies'=>$data_competencies,'allData'=>$data]);
    }
}
