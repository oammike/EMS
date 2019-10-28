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
    	return view('evaluation.newPA-index');

    }
}
