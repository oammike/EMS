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
use OAMPI_Eval\NewPA_Form_User;
use OAMPI_Eval\NewPA_Evals;
use OAMPI_Eval\NewPA_Evals_Goal;
use OAMPI_Eval\NewPA_Evals_Competencies;
use OAMPI_Eval\NewPA_Evals_Professionalism;
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

class NewPA_Evals_Controller extends Controller
{
    protected $user;
    protected $newPA_evals;
    use Traits\EvaluationTraits;
    use Traits\UserTraits;

     public function __construct(NewPA_Evals $newPA_evals)
    {
        $this->middleware('auth');
        $this->user =  User::find(Auth::user()->id);
        $this->newPA_evals = $newPA_evals;
    }



    public function index()
    {
    }

    public function destroy($id)
    {
        $this->newPA_evals->destroy($id);
        return back();

    }

    public function process(Request $request)
    {
    	$correct = Carbon::now('GMT+8');
    	$goalRatings= $request->goalRatings;
        $compRatings =  $request->compRatings;
        $overall = $request->overall;
        $user = User::find($request->user_id);
        $immediateHead = ImmediateHead_Campaign::find($request->tl_id);
        $mfrom = $request->period_mfrom;
        $dfrom = $request->period_dfrom;
        $mto = $request->period_mto;
        $dto = $request->period_dto;
        $form = NewPA_Form::find($request->form_id);

        //withou scorecards:
        $nocards = collect(NewPA_Type::where('withScorecards',0)->get())->pluck('id')->toArray();
        //return $nocards;

        $startPeriod = Carbon::parse($mfrom." ".$dfrom.",".$correct->format('Y'), 'Asia/Manila');
        $endPeriod = Carbon::parse($mto." ".$dto.",".$correct->format('Y'), 'Asia/Manila');

        //$coll = new Collection;

        //return response()->json(['startPeriod'=>$startPeriod,'endPeriod'=>$endPeriod, 'goalRatings'=>$goalRatings, 'compRatings'=>$compRatings, 'overall'=>$overall, 'user'=>$user, 'immediateHead'=>$immediateHead]);

        $eval = new NewPA_Evals;
        $eval->user_id = $user->id;
        $eval->evaluatedBy = $immediateHead->id;
        $eval->finalRating = $overall;
        $eval->form_id =  $form->id;
        $eval->startPeriod = $startPeriod->format('Y-m-d');
        $eval->endPeriod = $endPeriod->format('Y-m-d');
        $eval->save();

        if( in_array($form->typeID, $nocards))
        {
            //$ctr=0;
        	foreach ($goalRatings as $g) {

                //$coll->push(['g'=>$g['goalID'], 'ctr'=>$ctr]);

        		$evalGoal = new NewPA_Evals_Goal;
        		
    			$evalGoal->eval_id = $eval->id;
    			$evalGoal->goal_id = $g['goalID'];
    			$evalGoal->rating = $g['goalRating'];
    			$evalGoal->notes = $g['goalComment'];
    			$evalGoal->save();
                //$ctr++;

        		
        	}
        	
        }

        //return $coll;

        foreach ($compRatings as $c) {

        	$comp = new NewPA_Evals_Competencies;
        	$comp->eval_id = $eval->id;
        	$comp->competency_id = $c['competencyID'] ;
        	$comp->rating = $c['compRating'] ;
        	$comp->strengths = $c['strengths'];
        	$comp->afi = $c['afi'];
        	$comp->notes = $c['crit'];
        	$comp->save();
        	# code...
        }



        return response()->json(['startPeriod'=>$startPeriod,'endPeriod'=>$endPeriod, 'goalRatings'=>$goalRatings, 'compRatings'=>$compRatings, 'overall'=>$overall, 'user'=>$user, 'immediateHead'=>$immediateHead]);

    }

    public function show($id)
    {
      

      $months = [];

      for ($m=1; $m<=12; $m++)
      {
        array_push($months, Carbon::parse($m."/1/2020")->format('M'));
      }

     
                  

      $form = DB::table('newPA_evals')->where('newPA_evals.id',$id)->
                  leftJoin('newPA_form','newPA_form.id','=','newPA_evals.form_id')->
                  leftJoin('newPA_type','newPA_form.typeID','=','newPA_type.id')->
                  leftJoin('newPA_form_goal','newPA_form_goal.formID','=','newPA_form.id')->
                  leftJoin('newPA_goal','newPA_form_goal.goalID','=','newPA_goal.id')->
                  leftJoin('newPA_form_components','newPA_form_components.typeID','=','newPA_form.typeID')->
                  leftJoin('newPA_components','newPA_form_components.componentID','=','newPA_components.id')->
                  leftJoin('newPA_form_competencies','newPA_form_competencies.typeID','=','newPA_type.id')->
                  leftJoin('newPA_competencies','newPA_form_competencies.competencyID','=','newPA_competencies.id')->
                  // join('newPA_evals_goal','newPA_evals_goal.eval_id','=','newPA_evals.id')->
                  // join('newPA_evals_competencies','newPA_evals_competencies.eval_id','=','newPA_evals.id')->
                  //leftJoin('newPA_competency_descriptor','newPA_competencies.id','=','newPA_competency_descriptor.competencyID')->
                  select('newPA_form.id', 'newPA_form.name','newPA_form.typeID','newPA_components.name as componentName','newPA_form_components.weight as componentWeight', 'newPA_goal.statement','newPA_goal.activities','newPA_goal.targets', 'newPA_form_goal.weight as goalWeight','newPA_form_goal.id as goalID','newPA_competencies.id as competencyID', 'newPA_competencies.name as competency','newPA_form_competencies.weight as competencyWeight','newPA_evals.user_id')->get();
                      // 'newPA_evals_goal.rating as goalRating','newPA_evals_goal.notes as goalNotes','newPA_evals_competencies.rating as compRating','newPA_evals_competencies.strengths','newPA_evals_competencies.afi','newPA_evals_competencies.notes as criticalIncidents'

      $evalGoals = DB::table('newPA_evals')->where('newPA_evals.id',$id)->
                        leftJoin('newPA_evals_goal','newPA_evals_goal.eval_id','=','newPA_evals.id')->
                        get();
      $evalCompetencies = DB::table('newPA_evals')->where('newPA_evals.id',$id)->
                        leftJoin('newPA_evals_competencies','newPA_evals_competencies.eval_id','=','newPA_evals.id')->
                        get();
      //return $evalCompetencies;

     
                  //'newPA_competency_descriptor.descriptor','newPA_competency_descriptor.competencyID as descriptorID'
                  //get();
      $allComponents = collect($form)->groupBy('componentWeight');
      $allGoals = collect($form)->groupBy('goalID');
      $allCompetencies = collect($form)->groupBy('competency');
      //$descriptors = collect($form)->groupBy('descriptorID');
      $descriptors = DB::table('newPA_form')->where('newPA_form.id',$id)->
                        leftJoin('newPA_form_competencies','newPA_form_competencies.typeID','=','newPA_form.typeID')->
                        leftJoin('newPA_competency_descriptor','newPA_form_competencies.competencyID','=','newPA_competency_descriptor.competencyID')->
                        select('newPA_form_competencies.competencyID','newPA_competency_descriptor.descriptor','newPA_competency_descriptor.id')->get();

       $user = DB::table('users')->where('users.id',$form[0]->user_id)->join('positions','positions.id','=','users.position_id')->
                  join('team','team.user_id','=','users.id')->
                  join('campaign','team.campaign_id','=','campaign.id')->
                  select('users.id','users.firstname','users.nickname','users.lastname','positions.name as jobTitle','campaign.name as program','team.immediateHead_Campaigns_id as tlID')->get(); 


      //return response()->json(['Components'=>$allComponents, 'Goals'=>$allGoals,'Competencies'=>$allCompetencies,'descriptors'=>$descriptors]);
      return view('evaluation.newPA-evaluate_show',compact('allGoals','allCompetencies','descriptors','allComponents','form','user','months','evalGoals','evalCompetencies'));

    }
}
