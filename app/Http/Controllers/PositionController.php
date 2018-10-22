<?php

namespace OAMPI_Eval\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;

use OAMPI_Eval\Http\Requests;
use OAMPI_Eval\User;
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
use OAMPI_Eval\PersonnelChange;
use OAMPI_Eval\Movement_ImmediateHead;
use OAMPI_Eval\Movement_Positions;
use OAMPI_Eval\Movement_Status;

class PositionController extends Controller
{
   protected $user;
    protected $position;

    public function __construct(Position $position)
    {
        $this->middleware('auth');
        $this->user =  User::find(Auth::user()->id);
        $this->position = $position;
    }

     public function index()
    {
        //return view('dashboard');
    }

    public function store(Request $request)
    {
    	$pos = new Position;
    	$pos->name = $request->name;
    	$pos->save();

    	return response()->json($pos);
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
