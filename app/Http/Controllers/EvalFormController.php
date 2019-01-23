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
use OAMPI_Eval\EvalType;
use OAMPI_Eval\Movement;
use OAMPI_Eval\RatingScale;
use OAMPI_Eval\EvalSetting;
use OAMPI_Eval\EvalForm;
use OAMPI_Eval\EvalDetail;
use OAMPI_Eval\Competency;
use OAMPI_Eval\Competency__Attribute;
use OAMPI_Eval\Attribute;
use OAMPI_Eval\Summary;
use OAMPI_Eval\PerformanceSummary;
use OAMPI_Eval\ImmediateHead_Campaign;
use OAMPI_Eval\Movement_ImmediateHead;
use OAMPI_Eval\Movement_Positions;
use OAMPI_Eval\Position;
use OAMPI_Eval\Notification;
use OAMPI_Eval\User_Notification;
class EvalFormController extends Controller
{
    protected $user;
    protected $evalForm;
    use Traits\EvaluationTraits;

     public function __construct(EvalForm $evalForm)
    {
        $this->middleware('auth');
        $this->user =  User::find(Auth::user()->id);
        $this->evalForm = $evalForm;
    }



    public function index()
    {
        $t = Input::get('type');
        switch ($t) {
          case '1': $type = 1;
            # code...
            break;
          case '2': $type = 2;
            # code...
            break;
          case '3': $type = 3;
            # code...
            break;
          case '4': $type = 4;
            # code...
            break;
          
          default: $type=1;
            # code...
            break;
        }

        /*switch ($type) {
          case 1: {
                    $setting = EvalSetting::find(1);
                    $speriod = Carbon::parse(date('y')."-".$setting->startMonth."-".$setting->startDate)->format('Y-m-d H:i:s');
                    $eperiod = Carbon::parse(date('y')."-".$setting->endMonth."-".$setting->endDate)->format('Y-m-d H:i:s');}break;
                   
          case 2: {
                    $setting = EvalSetting::find(2);
                    $speriod = Carbon::parse(date('y')."-".$setting->startMonth."-".$setting->startDate)->format('Y-m-d H:i:s');
                    $eperiod = Carbon::parse(date('y')."-".$setting->endMonth."-".$setting->endDate." 00:00:00")->format('Y-m-d h:i:s');}break;
                   
          
          case 3: {
                    $setting = EvalSetting::find(3);
                    $speriod = Carbon::parse("first day of January")->format('Y-m-d');
                    $eperiod = Carbon::parse("last day of December")->format('Y-m-d');}break;

                 

          case 4: {
                    $setting = EvalSetting::find(4);
                    $speriod = Carbon::parse("first day of January")->format('Y-m-d');
                    $eperiod = Carbon::parse("last day of December")->format('Y-m-d');}break;

                 
          
          default: {
                    $setting = EvalSetting::find(1);
                    $speriod = Carbon::parse(date('y')."-".$setting->startMonth."-".$setting->startDate)->format('Y-m-d H:i:s');
                    $eperiod = Carbon::parse(date('y')."-".$setting->endMonth."-".$setting->endDate)->format('Y-m-d H:i:s');}break;
           
        }
         $allForms = EvalForm::where('evalSetting_id',$setting->id)->where('startPeriod','>=',$speriod)->where('endPeriod','<=',$eperiod)->where('overAllScore','!=','0')->orderBy('created_at','DESC')->take(100)->get();
        //$users = User::all();
        */

        
        $campaigns = Campaign::all();
        /*$coll = new Collection;

        $evaluations = new Collection;

        foreach ($allForms as $eval) {
          if ( !$eval->details->isEmpty() )
            $evaluator = ImmediateHead::find(ImmediateHead_Campaign::find($eval->evaluatedBy)->immediateHead_id);

          if (empty($evaluator)){

                if ($eval->evalSetting_id == 3 || $eval->evalSetting_id == 4) //regularization
                $evaluations->push(['id'=>$eval->id, 
                  'user_id'=>$eval->user_id, 
                  'increase'=> "N/A", 
                  'type'=>EvalSetting::find($eval->evalSetting_id)->name, 
                  'lastname'=> $eval->owner->lastname, 
                  'firstname'=> $eval->owner->firstname, 
                  'campaign'=> $eval->owner->campaign->first()->name, 
                  'head'=> null,
                  'score'=>$eval->overAllScore,
                  'dateEvaluated'=> $eval->created_at ]);

              else $evaluations->push(['id'=>$eval->id, 
                  'user_id'=>$eval->user_id, 
                  'increase'=> $eval->salaryIncrease, 
                  'type'=>EvalSetting::find($eval->evalSetting_id)->name, 
                  'lastname'=> $eval->owner->lastname, 
                  'firstname'=> $eval->owner->firstname, 
                  'campaign'=> $eval->owner->campaign->first()->name, 
                  'head'=> null,
                  'score'=>$eval->overAllScore,
                  'dateEvaluated'=> $eval->created_at ]);

          } else {


            $leader = User::where('employeeNumber',$evaluator->employeeNumber)->first();
            (empty($leader->nickname)) ? $fname = $leader->firstname : $fname = $leader->nickname;

            if ($eval->evalSetting_id == 3 || $eval->evalSetting_id == 4) //REGULARIZATION
            {
              if ($eval->isDraft)
                $evaluations->push(['id'=>$eval->id, 
                  'user_id'=>$eval->user_id, 
                  'increase'=>"N/A", 
                  'type'=> date("Y", strtotime($eval->startPeriod)). " ". EvalSetting::find($eval->evalSetting_id)->name, 
                  'lastname'=> $eval->owner->lastname, 
                  'firstname'=> $eval->owner->firstname, 
                  'campaign'=> Campaign::find(ImmediateHead_Campaign::find($eval->evaluatedBy)->campaign_id)->name, //$camp, //$eval->owner->campaign->first()->name, 
                  'head'=> $fname." ".$evaluator->lastname,
                  'score'=>"DRAFT",
                  'dateEvaluated'=> $eval->created_at->format('Y-m-d')]);
              
              else

                $evaluations->push(['id'=>$eval->id, 
                  'user_id'=>$eval->user_id, 
                  'increase'=>"N/A", 
                  'type'=>date("Y", strtotime($eval->startPeriod)). " ". EvalSetting::find($eval->evalSetting_id)->name, 
                  'lastname'=> $eval->owner->lastname, 
                  'firstname'=> $eval->owner->firstname, 
                  'campaign'=> Campaign::find(ImmediateHead_Campaign::find($eval->evaluatedBy)->campaign_id)->name, //$camp, //$eval->owner->campaign->first()->name, 
                  'head'=> $fname." ".$evaluator->lastname,
                  'score'=>$eval->overAllScore,
                  'dateEvaluated'=> $eval->created_at->format('Y-m-d') ]);

            }  else {

                if ($eval->isDraft)
                  $evaluations->push(['id'=>$eval->id, 
                  'user_id'=>$eval->user_id, 
                  'increase'=>"DRAFT", 
                  'type'=>date("Y", strtotime($eval->startPeriod)). " ". EvalSetting::find($eval->evalSetting_id)->name, 
                  'lastname'=> $eval->owner->lastname, 
                  'firstname'=> $eval->owner->firstname, 
                  'campaign'=> Campaign::find(ImmediateHead_Campaign::find($eval->evaluatedBy)->campaign_id)->name, //$eval->owner->campaign->first()->name, 
                  'head'=> $fname." ".$evaluator->lastname,
                  'score'=>"DRAFT",
                  'dateEvaluated'=>$eval->created_at->format('Y-m-d') ]);

                else
                  $evaluations->push(['id'=>$eval->id, 
                  'user_id'=>$eval->user_id, 
                  'increase'=>$eval->salaryIncrease, 
                  'type'=>date("Y", strtotime($eval->startPeriod)). " ". EvalSetting::find($eval->evalSetting_id)->name, 
                  'lastname'=> $eval->owner->lastname, 
                  'firstname'=> $eval->owner->firstname, 
                  'campaign'=> Campaign::find(ImmediateHead_Campaign::find($eval->evaluatedBy)->campaign_id)->name, //$eval->owner->campaign->first()->name, 
                  'head'=> $fname." ".$evaluator->lastname,
                  'score'=>$eval->overAllScore,
                  'dateEvaluated'=> $eval->created_at->format('Y-m-d') ]);

              }
                

          }

            
        }*/
       
        return view('evaluation.index', compact( 'type', 'campaigns'));
    }

   


     public function create()
    {

    }


    public function deleteThisEval($id)
    {
      $theEval = EvalForm::find($id);
      $theEval->delete();

      return redirect()->back();

    }

     public function destroy($id)
    {
      $this->evalForm->destroy($id);
      return back();

    }

    public function downloadReport()
    {

      Excel::create('Evaluation Summary', function($excel) {

        

          // Set the title
          $excel->setTitle('Evaluation Summary Report');

          // Chain the setters
          $excel->setCreator('Mike Pamero')
                ->setCompany('OAMPI');

          // Call them separately
          $excel->setDescription('Contains summary of Semi-annual and Regularization Evaluations');

          $janjun = date("Y") . " Jan-Jun Semi-annual";
  
         $juldec = date("Y")-1 . " Jul-Dec Semi-annual";
  
  
         $excel->sheet($janjun, function($sheet) {

            $evalTypes = EvalType::all();
            $evalSetting = EvalSetting::find(1);
            $currentPeriod = Carbon::create((date("Y")), $evalSetting->startMonth, $evalSetting->startDate,0,0,0, 'Asia/Manila');
            $endPeriod = Carbon::create((date("Y")), $evalSetting->endMonth, $evalSetting->endDate,0,0,0, 'Asia/Manila');

            $evals = EvalForm::where('evalSetting_id','1')->where('startPeriod','>=',$currentPeriod)->where('endPeriod','<=',$endPeriod)->orderBy('user_id','ASC')->get(); //get only jul-dec semi annual

            $sheet->appendRow(array('Employee Number', 'Lastname', 'Firstname', 'Dept/Program','Overall Score','Salary Increase', 'Evaluated By', 'Date Evaluated'));

            

            foreach($evals as $eval){

              if ( !$eval->details->isEmpty() )
              {
                $cmp = User::find($eval->owner->id)->campaign->first();
                if (empty($cmp))
                {
                      if ($eval->isDraft)
                      $arr = array($eval->owner->employeeNumber,
                      $eval->owner->lastname,
                      $eval->owner->firstname,
                      "none", 
                      "DRAFT", "DRAFT", 
                      //ImmediateHead::find($eval->evaluatedBy)->firstname." ".ImmediateHead::find($eval->evaluatedBy)->lastname, 
                      ImmediateHead::find(ImmediateHead_Campaign::find($eval->evaluatedBy)->immediateHead_id)->firstname." ".ImmediateHead::find(ImmediateHead_Campaign::find($eval->evaluatedBy)->immediateHead_id)->lastname, 
                      Carbon::createFromFormat('Y-m-d H:i:s',$eval->updated_at));
                    else
                      $arr = array($eval->owner->employeeNumber,
                      $eval->owner->lastname,
                      $eval->owner->firstname,
                      "none", 
                      $eval->overAllScore, $eval->salaryIncrease."%", 
                      ImmediateHead::find(ImmediateHead_Campaign::find($eval->evaluatedBy)->immediateHead_id)->firstname." ".ImmediateHead::find(ImmediateHead_Campaign::find($eval->evaluatedBy)->immediateHead_id)->lastname, 
                      Carbon::createFromFormat('Y-m-d H:i:s',$eval->updated_at));

                } else 
                {

                      if ($eval->isDraft)
                      $arr = array($eval->owner->employeeNumber,
                      $eval->owner->lastname,
                      $eval->owner->firstname,
                      $cmp->name, 
                      "DRAFT", "DRAFT", 
                      ImmediateHead::find(ImmediateHead_Campaign::find($eval->evaluatedBy)->immediateHead_id)->firstname." ".ImmediateHead::find(ImmediateHead_Campaign::find($eval->evaluatedBy)->immediateHead_id)->lastname, 
                      Carbon::createFromFormat('Y-m-d H:i:s',$eval->updated_at));
                    else
                      $arr = array($eval->owner->employeeNumber,
                      $eval->owner->lastname,
                      $eval->owner->firstname,
                      $cmp->name, 
                      $eval->overAllScore, $eval->salaryIncrease."%", 
                      ImmediateHead::find(ImmediateHead_Campaign::find($eval->evaluatedBy)->immediateHead_id)->firstname." ".ImmediateHead::find(ImmediateHead_Campaign::find($eval->evaluatedBy)->immediateHead_id)->lastname, 
                      Carbon::createFromFormat('Y-m-d H:i:s',$eval->updated_at));

                }
               

                

              
              $sheet->appendRow($arr);

              }

              

            }

              

          });
          $excel->sheet($juldec, function($sheet) {
          //$excel->sheet('2016 Jul-Dec Semi-annual', function($sheet) {

            $evalTypes = EvalType::all();
            $evalSetting = EvalSetting::find(2);
            $currentPeriod = Carbon::create((date("Y")-1), $evalSetting->startMonth, $evalSetting->startDate,0,0,0, 'Asia/Manila');
            $endPeriod = Carbon::create((date("Y")-1), $evalSetting->endMonth, $evalSetting->endDate,0,0,0, 'Asia/Manila');


            $evals = EvalForm::where('evalSetting_id','2')->where('startPeriod','>=',$currentPeriod)->where('endPeriod','<=',$endPeriod)->orderBy('user_id','ASC')->get(); //get only jul-dec semi annual
            //$evals = EvalForm::where('evalSetting_id','2')->orderBy('user_id','ASC')->get(); //get only jul-dec semi annual

            $sheet->appendRow(array('Employee Number', 'Lastname', 'Firstname', 'Dept/Program','Overall Score','Salary Increase', 'Evaluated By', 'Date Evaluated'));

            

            foreach($evals as $eval){

              if ( !$eval->details->isEmpty() )
              {
                $cmp = User::find($eval->owner->id)->campaign->first();
                if (empty($cmp))
                {
                      if ($eval->isDraft)
                      $arr = array($eval->owner->employeeNumber,
                      $eval->owner->lastname,
                      $eval->owner->firstname,
                      "none", 
                      "DRAFT", "DRAFT", 
                      //ImmediateHead::find($eval->evaluatedBy)->firstname." ".ImmediateHead::find($eval->evaluatedBy)->lastname, 
                      ImmediateHead::find(ImmediateHead_Campaign::find($eval->evaluatedBy)->immediateHead_id)->firstname." ".ImmediateHead::find(ImmediateHead_Campaign::find($eval->evaluatedBy)->immediateHead_id)->lastname, 
                      Carbon::createFromFormat('Y-m-d H:i:s',$eval->updated_at));
                    else
                      $arr = array($eval->owner->employeeNumber,
                      $eval->owner->lastname,
                      $eval->owner->firstname,
                      "none", 
                      $eval->overAllScore, $eval->salaryIncrease."%", 
                      ImmediateHead::find(ImmediateHead_Campaign::find($eval->evaluatedBy)->immediateHead_id)->firstname." ".ImmediateHead::find(ImmediateHead_Campaign::find($eval->evaluatedBy)->immediateHead_id)->lastname, 
                      Carbon::createFromFormat('Y-m-d H:i:s',$eval->updated_at));

                } else 
                {

                      if ($eval->isDraft)
                      $arr = array($eval->owner->employeeNumber,
                      $eval->owner->lastname,
                      $eval->owner->firstname,
                      $cmp->name, 
                      "DRAFT", "DRAFT", 
                      ImmediateHead::find(ImmediateHead_Campaign::find($eval->evaluatedBy)->immediateHead_id)->firstname." ".ImmediateHead::find(ImmediateHead_Campaign::find($eval->evaluatedBy)->immediateHead_id)->lastname, 
                      Carbon::createFromFormat('Y-m-d H:i:s',$eval->updated_at));
                    else
                      $arr = array($eval->owner->employeeNumber,
                      $eval->owner->lastname,
                      $eval->owner->firstname,
                      $cmp->name, 
                      $eval->overAllScore, $eval->salaryIncrease."%", 
                      ImmediateHead::find(ImmediateHead_Campaign::find($eval->evaluatedBy)->immediateHead_id)->firstname." ".ImmediateHead::find(ImmediateHead_Campaign::find($eval->evaluatedBy)->immediateHead_id)->lastname, 
                      Carbon::createFromFormat('Y-m-d H:i:s',$eval->updated_at));

                }
               

                

              
              $sheet->appendRow($arr);

              }

              

            }

              

          });

          $excel->sheet('Regularizations', function($sheet) {

            $evalTypes = EvalType::all();
            $evalSetting = EvalSetting::find(3);
            

            $evals = EvalForm::where('evalSetting_id','3')->orderBy('created_at','DESC')->get(); //get only jul-dec semi annual

            $sheet->appendRow(array('Employee Number', 'Lastname', 'Firstname', 'Dept/Program','Overall Score', 'Evaluated By', 'Date Evaluated', 'Date Hired'));

            

            foreach($evals as $eval){

              if ( !$eval->details->isEmpty() )
              {
                $cmp = User::find($eval->owner->id)->campaign->first();
                if (empty($cmp))
                {
                      if ($eval->isDraft)
                      $arr = array($eval->owner->employeeNumber,
                      $eval->owner->lastname,
                      $eval->owner->firstname,
                      "none", 
                      "DRAFT", 
                      //ImmediateHead::find($eval->evaluatedBy)->firstname." ".ImmediateHead::find($eval->evaluatedBy)->lastname, 
                      ImmediateHead::find(ImmediateHead_Campaign::find($eval->evaluatedBy)->immediateHead_id)->firstname." ".ImmediateHead::find(ImmediateHead_Campaign::find($eval->evaluatedBy)->immediateHead_id)->lastname, 
                      Carbon::createFromFormat('Y-m-d H:i:s',$eval->updated_at),
                      Carbon::createFromFormat('Y-m-d H:i:s',User::find($eval->owner->id)->dateHired));
                    else
                      $arr = array($eval->owner->employeeNumber,
                      $eval->owner->lastname,
                      $eval->owner->firstname,
                      "none", 
                      $eval->overAllScore, 
                      ImmediateHead::find(ImmediateHead_Campaign::find($eval->evaluatedBy)->immediateHead_id)->firstname." ".ImmediateHead::find(ImmediateHead_Campaign::find($eval->evaluatedBy)->immediateHead_id)->lastname, 
                      Carbon::createFromFormat('Y-m-d H:i:s',$eval->updated_at),
                      Carbon::createFromFormat('Y-m-d H:i:s',User::find($eval->owner->id)->dateHired));

                } else 
                {

                      if ($eval->isDraft)
                      $arr = array($eval->owner->employeeNumber,
                      $eval->owner->lastname,
                      $eval->owner->firstname,
                      $cmp->name, 
                      "DRAFT", 
                      ImmediateHead::find(ImmediateHead_Campaign::find($eval->evaluatedBy)->immediateHead_id)->firstname." ".ImmediateHead::find(ImmediateHead_Campaign::find($eval->evaluatedBy)->immediateHead_id)->lastname, 
                      Carbon::createFromFormat('Y-m-d H:i:s',$eval->updated_at),
                      Carbon::createFromFormat('Y-m-d H:i:s',User::find($eval->owner->id)->dateHired));

                    else
                      $arr = array($eval->owner->employeeNumber,
                      $eval->owner->lastname,
                      $eval->owner->firstname,
                      $cmp->name, 
                      $eval->overAllScore, 
                      ImmediateHead::find(ImmediateHead_Campaign::find($eval->evaluatedBy)->immediateHead_id)->firstname." ".ImmediateHead::find(ImmediateHead_Campaign::find($eval->evaluatedBy)->immediateHead_id)->lastname, 
                      Carbon::createFromFormat('Y-m-d H:i:s',$eval->updated_at),
                      Carbon::createFromFormat('Y-m-d H:i:s',User::find($eval->owner->id)->dateHired));

                }
               

                

              
              $sheet->appendRow($arr);

              }

              

            }

              

          });


          $excel->sheet('Extended Contractual', function($sheet) {

            $evalTypes = EvalType::all();
            $evalSetting = EvalSetting::find(4);
            

            $evals = EvalForm::where('evalSetting_id','4')->orderBy('created_at','DESC')->get(); //get only jul-dec semi annual

            $sheet->appendRow(array('Employee Number', 'Lastname', 'Firstname', 'Dept/Program','Overall Score', 'Evaluated By', 'Date Evaluated', 'Date Hired'));

            

            foreach($evals as $eval){

              if ( !$eval->details->isEmpty() )
              {
                $cmp = User::find($eval->owner->id)->campaign->first();
                if (empty($cmp))
                {
                      if ($eval->isDraft)
                      $arr = array($eval->owner->employeeNumber,
                      $eval->owner->lastname,
                      $eval->owner->firstname,
                      "none", 
                      "DRAFT", 
                      //ImmediateHead::find($eval->evaluatedBy)->firstname." ".ImmediateHead::find($eval->evaluatedBy)->lastname, 
                      ImmediateHead::find(ImmediateHead_Campaign::find($eval->evaluatedBy)->immediateHead_id)->firstname." ".ImmediateHead::find(ImmediateHead_Campaign::find($eval->evaluatedBy)->immediateHead_id)->lastname, 
                      Carbon::createFromFormat('Y-m-d H:i:s',$eval->updated_at),
                      Carbon::createFromFormat('Y-m-d H:i:s',User::find($eval->owner->id)->dateHired));
                    else
                      $arr = array($eval->owner->employeeNumber,
                      $eval->owner->lastname,
                      $eval->owner->firstname,
                      "none", 
                      $eval->overAllScore, 
                      ImmediateHead::find(ImmediateHead_Campaign::find($eval->evaluatedBy)->immediateHead_id)->firstname." ".ImmediateHead::find(ImmediateHead_Campaign::find($eval->evaluatedBy)->immediateHead_id)->lastname, 
                      Carbon::createFromFormat('Y-m-d H:i:s',$eval->updated_at),
                      Carbon::createFromFormat('Y-m-d H:i:s',User::find($eval->owner->id)->dateHired));

                } else 
                {

                      if ($eval->isDraft)
                      $arr = array($eval->owner->employeeNumber,
                      $eval->owner->lastname,
                      $eval->owner->firstname,
                      $cmp->name, 
                      "DRAFT", 
                      ImmediateHead::find(ImmediateHead_Campaign::find($eval->evaluatedBy)->immediateHead_id)->firstname." ".ImmediateHead::find(ImmediateHead_Campaign::find($eval->evaluatedBy)->immediateHead_id)->lastname, 
                      Carbon::createFromFormat('Y-m-d H:i:s',$eval->updated_at),
                      Carbon::createFromFormat('Y-m-d H:i:s',User::find($eval->owner->id)->dateHired));

                    else
                      $arr = array($eval->owner->employeeNumber,
                      $eval->owner->lastname,
                      $eval->owner->firstname,
                      $cmp->name, 
                      $eval->overAllScore, 
                      ImmediateHead::find(ImmediateHead_Campaign::find($eval->evaluatedBy)->immediateHead_id)->firstname." ".ImmediateHead::find(ImmediateHead_Campaign::find($eval->evaluatedBy)->immediateHead_id)->lastname, 
                      Carbon::createFromFormat('Y-m-d H:i:s',$eval->updated_at),
                      Carbon::createFromFormat('Y-m-d H:i:s',User::find($eval->owner->id)->dateHired));

                }
               

                

              
              $sheet->appendRow($arr);

              }

              

            }

              

          });



          //Regularization summary
          // $excel->sheet('Regularizations', function($sheet) {

          //   $evals = EvalForm::where('evalSetting_id','3')->orderBy('user_id','ASC')->get(); //get only Regularization forms
          //   $sheet->appendRow(array('Employee Number', 'Lastname', 'Firstname', 'Dept/Program','Overall Score', 'Evaluated By', 'Date Evaluated'));

          //   foreach($evals as $eval){
          //    $arr = array($eval->owner->employeeNumber,$eval->owner->lastname,$eval->owner->firstname,Campaign::find($eval->owner->campaign_id)->name, $eval->overAllScore, ImmediateHead::find($eval->evaluatedBy)->firstname." ".ImmediateHead::find($eval->evaluatedBy)->lastname, Carbon::createFromFormat('Y-m-d H:i:s',$eval->updated_at));
               
          //     $sheet->appendRow($arr);

          //   }

              

          // });


      })->export('xls');

      return "Download";
    }

    public function edit($id)
    {
      //check first if the one editing is the evaluator
      $coll = new Collection;
        $evalForm = EvalForm::find($id);
        $evaluator = ImmediateHead::find(ImmediateHead_Campaign::find($evalForm->evaluatedBy)->immediateHead_id);



        if ($this->user->employeeNumber == $evaluator->employeeNumber)
        {
            $details = $evalForm->details;


            $evalType = EvalType::find($evalForm->setting->evalType_id);
            $evalSetting = EvalSetting::find($evalForm->evalSetting_id);
            

            $employee = User::find($evalForm->user_id);

            $leadershipcheck = ImmediateHead::where('employeeNumber', $employee->employeeNumber)->get();
                    (count($leadershipcheck) > 0) ? $isLeader=true : $isLeader=false; 

            $ratingScale = RatingScale::all();

            $startPeriod = Carbon::createFromFormat('Y-m-d H:i:s',$evalForm->startPeriod);
            $endPeriod = Carbon::createFromFormat('Y-m-d H:i:s',$evalForm->endPeriod);

            

            $showPosition = $this->getCorrectPosition($employee,$startPeriod,$endPeriod);

            /* --------- END POSITION MOVEMENT -----------*/





            $competencyAttributes = $evalSetting->competencyAttributes;
            $competencies = $competencyAttributes->groupBy('competency_id');
            $formEntries = new Collection;
            $maxScore = 0;

            foreach ($competencies as $key ) {
                $attributes = new Collection;

                foreach ($key as $k) {
                    $attributes->push(Attribute::find($k->attribute_id)->name);
                }           
               
             }

            /* ---------- SETUP DETAILS -------------*/
            $ctr = 1;
            //return $details;

            foreach ($details as $detail) 
            {
                    $comp = Competency__Attribute::find($detail->competency__Attribute_id)->competency;
                    $attr = Competency__Attribute::find($detail->competency__Attribute_id)->attribute;

                    

                    $rating = RatingScale::find($detail->ratingScale_id); //->ratings; //->label;'ratings'=> $rating['label']

                    //$coll->push(['comp'=>$comp,'rating'=>$rating]);




                    // --------------- generate form elements based on leader/agent competencies

                   


                    if( $comp['acrossTheBoard'] ){

                        if ( !$isLeader || $employee->userType_id==4 || $employee->leadOverride  ){ //agent

                          $formEntries->push([
                              'competency'=> $comp['name'], 
                              'definitions'=>$comp['definitions'],
                              'percentage'=>$comp['agentPercentage'], 
                              'id'=>$comp['id'],
                              'detailID'=> $detail->id,
                              'attributes'=>$attributes, //$attr['name'],
                              'value'=> $detail->attributeValue,
                              'rating'=> $rating ]);

                               if ($ctr % 2 !=0 ){
                                $maxScore += $comp['agentPercentage']*5/100;
                               // var_dump("comp: ". $comp['percentage']*5/100);
                               } else {} //var_dump("even");
                         

                        } else {
                          $formEntries->push([
                              'competency'=> $comp['name'], 
                              'definitions'=>$comp['definitions'],
                              'percentage'=>$comp['percentage'], 
                              'id'=>$comp['id'],
                              'detailID'=> $detail->id,
                              'attributes'=>$attributes, //$attr['name'],
                              'value'=> $detail->attributeValue,
                              'rating'=> $rating ]);

                              if ($ctr % 2 !=0 ){
                                $maxScore += $comp['percentage']*5/100;
                               // var_dump("comp: ". $comp['percentage']*5/100);
                               } else {} //var_dump("even");

                        }

                      } else { //else not acrossTheBoard

                        if ( empty($comp['percentage']) && (!$isLeader || $employee->userType_id==4 || $employee->leadOverride ) ){ //agent sya
                          $formEntries->push([
                              'competency'=> $comp['name'], 
                              'definitions'=>$comp['definitions'],
                              'percentage'=>$comp['agentPercentage'], 
                              'id'=>$comp['id'],
                              'detailID'=> $detail->id,
                              'attributes'=>$attributes, //$attr['name'],
                              'value'=> $detail->attributeValue,
                              'rating'=> $rating ]);

                               if ($ctr % 2 !=0 ){
                                $maxScore += $comp['agentPercentage']*5/100;
                               // var_dump("comp: ". $comp['percentage']*5/100);
                               } else {} //var_dump("even");

                        } else if (!empty($comp['percentage']) && ($isLeader && $employee->userType_id!=4 ) && empty($employee->leadOverride) ){ //leader sya
                          $formEntries->push([
                              'competency'=> $comp['name'], 
                              'definitions'=>$comp['definitions'],
                              'percentage'=>$comp['percentage'], 
                              'id'=>$comp['id'],
                              'detailID'=> $detail->id,
                              'attributes'=>$attributes, //$attr['name'],
                              'value'=> $detail->attributeValue,
                              'rating'=> $rating ]);

                              if ($ctr % 2 !=0 ){
                                $maxScore += $comp['percentage']*5/100;
                               // var_dump("comp: ". $comp['percentage']*5/100);
                               } else {} //var_dump("even");


                        }

                      }


                      //$coll->push($formEntries);



                   
                   $ctr++;
            }
                //return $coll; //$formEntries->groupBy('competency');


                //return $coll;
                //get all Performance Summary values

                $allSummaries = Summary::all();
                $summaries = new Collection;

                foreach ($allSummaries as $key ) {
                   if (!($key->columns->isEmpty()) ) 
                    {
                        $cols = $key->columns;

                       
                    } else $cols=null;
                   if (!($key->rows->isEmpty()) )
                   { 
                        $rows = $key->rows;

                   }  else $rows = null;

                   //$summaryValue = PerformanceSummary::where('summary_id',$key->id)->where('evalForm_id',$evalForm->id)->first()->value;
                    $sValue = PerformanceSummary::where('summary_id',$key->id)->where('evalForm_id',$evalForm->id)->get(); //->first()->value;
                   if (count($sValue) > 0)
                      $summaryValue = PerformanceSummary::where('summary_id',$key->id)->where('evalForm_id',$evalForm->id)->first()->value;
                   else 
                      $summaryValue = null;

                   $summaries->push(['summaryID'=>$key->id,'summaryValue'=>$summaryValue,  'header'=>$key->heading,'details'=>$key->description, 'columns'=>$cols, 'rows'=>$rows]);
                }

                $perfSum = PerformanceSummary::where('evalForm_id', $evalForm->id)->get();

                if ($perfSum->isEmpty())
                {
                    $performanceSummary = null;

                } else {
                    $performanceSummary = new Collection;
                    $idx = 0;
                    foreach ($perfSum as $ps) {
                        $performanceSummary[$idx] = ['id'=>$ps->id, 'value'=> $ps->value];
                        $idx++;
                    }

                }

            /* ---------- END SETUP DETAILS -------- */

             
           //return $formEntries;
           return view('evaluation.edit-employee', compact('performanceSummary','startPeriod', 'endPeriod', 'evalType', 'employee', 'ratingScale', 'evalForm','evalSetting', 'formEntries','maxScore','summaries', 'details','showPosition','isLeader'));

        } else return "Sorry, you are not ". $evaluator->firstname." ".$evaluator->lastname. ". <br/> How dare you trying to edit this evaluation?!! :P ";// Redirect::route('evalForm.show',$id);
        
    }//end edit()



     public function getAllEval()
    {
        $type = Input::get('type');
        DB::connection()->disableQueryLog();
       

        switch ($type) {
          case 1: {
                    $setting = EvalSetting::find(1);
                    // $speriod = Carbon::parse(date('y')."-".$setting->startMonth."-".$setting->startDate)->format('Y-m-d H:i:s');
                    // $eperiod = Carbon::parse(date('y')."-".$setting->endMonth."-".$setting->endDate)->format('Y-m-d H:i:s');
                  }break;
                   
          case 2: {
                    $setting = EvalSetting::find(2);
                    // $speriod = Carbon::parse(date('y')."-".$setting->startMonth."-".$setting->startDate)->format('Y-m-d H:i:s');
                    // $eperiod = Carbon::parse(date('y')."-".$setting->endMonth."-".$setting->endDate." 00:00:00")->format('Y-m-d h:i:s');
                  }break;
                   
          
          case 3: {
                    $setting = EvalSetting::find(3);
                    // $speriod = Carbon::parse("first day of January")->format('Y-m-d');
                    // $eperiod = Carbon::parse("last day of December")->format('Y-m-d');
                  }break;

                 

          case 4: {
                    $setting = EvalSetting::find(4);
                    // $speriod = Carbon::parse("first day of January")->format('Y-m-d');
                    // $eperiod = Carbon::parse("last day of December")->format('Y-m-d');
                  }break;

                 
          
          default: {
                    $setting = EvalSetting::find(1);
                    // $speriod = Carbon::parse(date('y')."-".$setting->startMonth."-".$setting->startDate)->format('Y-m-d H:i:s');
                    // $eperiod = Carbon::parse(date('y')."-".$setting->endMonth."-".$setting->endDate)->format('Y-m-d H:i:s');
                  }break;
           
        }
         //$allForms = EvalForm::where('evalSetting_id',$setting->id)->where('startPeriod','>=',$speriod)->where('endPeriod','<=',$eperiod)->where('overAllScore','!=','0')->orderBy('created_at','DESC')->get();->where('startPeriod','>=',$speriod)->where('endPeriod','<=',$eperiod)

         $evaluations = DB::table('evalForm')->where('evalSetting_id',$setting->id)->where('overAllScore','!=','0')->leftJoin('immediateHead_Campaigns','evalForm.evaluatedBy','=','immediateHead_Campaigns.id')->leftJoin('immediateHead','immediateHead_Campaigns.immediateHead_id','=','immediateHead.id')->leftJoin('evalSetting','evalForm.evalSetting_id','=','evalSetting.id')->leftJoin('users','evalForm.user_id','=','users.id')->leftJoin('team','evalForm.user_id','=','team.user_id')->leftJoin('campaign','team.campaign_id','=','campaign.id')->select('evalForm.user_id','evalForm.id','evalForm.startPeriod as year', 'evalSetting.name as type', 'users.lastname','users.firstname','campaign.name as camp','immediateHead.firstname as headFname','immediateHead.lastname as headLname', 'evalForm.overAllScore','evalForm.created_at')->orderBy('evalForm.created_at','DESC')->get(); //chunk(100, 

        
         return response()->json(['data'=>$evaluations]);

        
       
    }


    public function grabAllWhosUpFor(Request $request)
    {
        $me1 = ImmediateHead::where('employeeNumber',$this->user->employeeNumber)->first();
        $mc = ImmediateHead_Campaign::where('immediateHead_id',$me1->id)->get();
        $myIHCampaignIDs = ImmediateHead_Campaign::where('immediateHead_id',$me1->id)->select('id')->get();
        
        $coll = new Collection;
        $me = $mc->first();
        $leadershipcheck = ImmediateHead::where('employeeNumber', $this->user->employeeNumber)->get();
        

        if ($this->user->userType_id !== 4 && !($leadershipcheck->isEmpty())) //if not AGENT, therefore may mga subordinates
        {
            $myActiveTeam = new Collection;

             

              $mySubs = ImmediateHead::find($me->immediateHead_id)->subordinates->sortBy('lastname');
               

               foreach ($mySubs as $k) {
                $emp = User::find($k->user_id);
                //7 - Resigned 8:Terminated; 9:Endo
                if ($emp->status_id !== 7 && $emp->status_id !== 8 && $emp->status_id !== 9 ) $myActiveTeam->push($emp);
               }

             
             
             
          
        } else { //else wala syang subordinates
            $employee = $this->user;
        }

        
        //return $mc;


        $mySubordinates = new Collection;
        $mySubordinates2 = new Collection;
       
         
        $evalTypes = EvalType::all();
        $evalSetting = EvalSetting::find($request->evalType_id);
        $doneEval = new Collection;



       /* -------- THIS IS A TEMPORARY SOLUTION TO HANDLE PERIODS ----- */

       

            switch ($request->evalType_id) {
                case 1: { //Jan-Jun semi-annual

                            $currentPeriod = Carbon::create((date("Y")), $evalSetting->startMonth, $evalSetting->startDate,0,0,0, 'Asia/Manila');
                            $endPeriod = Carbon::create((date("Y")), $evalSetting->endMonth, $evalSetting->endDate,0,0,0, 'Asia/Manila');
                            //$me = ImmediateHead::where('employeeNumber',$this->user->employeeNumber)->first();

                             


                            
                            //$me = ImmediateHead::where('employeeNumber',$this->user->employeeNumber)->first();
                            $coll = new Collection;

                            // $coll->push(['currentPeriod'=>$currentPeriod, 'endPeriod'=>$endPeriod]);
                            // return $coll;

                            if ($this->user->userType_id !== 4 && !($leadershipcheck->isEmpty())) //if not AGENT
                            {
                                  $mySubordinates1 =  $myActiveTeam->filter(function ($employee)
                                                      {   // Contrctual || Regular or Consultant or Floating or Contractual extended
                                                          return ($employee->status_id == 1 || $employee->status_id == 4 || $employee->status_id == 5 || $employee->status_id == 6 || $employee->status_id == 10 );
                                                      });
                                 
                                   /* ------------
                                  foreach ($mySubordinates1->sortBy('lastname') as $emp) {

                                    

                                          //We need to make sure emp is 6++ months already 

                                          $hired = Carbon::createFromFormat('Y-m-d H:i:s', $emp->dateHired);

                                          $serviceLength = $hired->diffInMonths($endPeriod);

                                          if ($serviceLength >= 6) $mySubordinates2->push($emp);

                                         
                                  }
                                 --------------- */




                                  //foreach ($mySubordinates2->sortBy('lastname') as $emp) {
                                  foreach ($mySubordinates1->sortBy('lastname') as $emp) {


                                          /* ------------

                                          We need to check if this subordinate has just been moved to you

                                          ---------------*/

                                          // GET ALL his IH movements from latest to oldest
                                          $checkMovements = Movement::where('user_id',$emp->id)->where('personnelChange_id','1')->where('isDone','1')->where('effectivity','>=',$currentPeriod->toDateString())->orderBy('effectivity','DESC')->get(); 

                                         if (count($checkMovements)>0)
                                         {
                                              $checkMovement = $checkMovements->first();

                                              // then isa-isahin mo yung movements, check mo kung ikaw ung latest TL
                                              foreach ($checkMovements as $mvt) {
                                                
                                                if( $myIHCampaignIDs->contains($mvt->immediateHead_details->imHeadCampID_new))
                                                {
                                                  $effective = Carbon::createFromFormat('Y-m-d H:i:s', $mvt->effectivity, 'Asia/Manila');

                                                      //pag yung movement from eh dateHired, meaning 1st time nya lang na-move..kunin mo yung effectivity start
                                                      if ($mvt->fromPeriod == $emp->dateHired)
                                                      {
                                                        $fr = Carbon::createFromFormat('Y-m-d H:i:s', $mvt->effectivity, 'Asia/Manila');
                                                      } 

                                                      else if (date('Y-m-d',strtotime($mvt->fromPeriod)) > date('Y-m-d',strtotime($currentPeriod)) )
                                                      { 

                                                          // **** GET THE EVAL RANGES, pag fromPeriod eh wayy past currentPeriod -- get current
                                                          //else if($checkMovement->fromPeriod < $currentPeriod->toDateString()){
                                                          
                                                             //$coll->push(['pasok'=>date('m',strtotime($mvt->fromPeriod)), 'currentPeriod'=>date('Y-m-d',strtotime($currentPeriod))]); 

                                                          //movement is within range pa, so kunin mo ung effectivity
                                                          if ( (date('Y-m-d',strtotime($mvt->effectivity)) <= date('Y-m-d',strtotime($endPeriod))) && (date('Y-m-d',strtotime($mvt->effectivity)) >= date('Y-m-d',strtotime($currentPeriod))))  
                                                            { $fr =  Carbon::createFromFormat('Y-m-d H:i:s', $mvt->effectivity, 'Asia/Manila'); 
                                                            $coll->push(['from'=>$fr, 'pasok'=>date('Y-m-d',strtotime($mvt->fromPeriod)), 'currentPeriod'=>date('Y-m-d',strtotime($currentPeriod))]);  }
                                                          else
                                                           {$fr = $currentPeriod->startOfDay(); $coll->push(['no'=>$mvt->immediateHead_details]);}

                                                      } else  {  $coll->push(['from'=>date('Y-m-d',strtotime($mvt->fromPeriod)), 'currentPeriod'=>$currentPeriod->toDateString()]); $fr = Carbon::createFromFormat('Y-m-d H:i:s', $mvt->effectivity, 'Asia/Manila'); } //Carbon::createFromFormat('Y-m-d H:i:s', $checkMovement->fromPeriod, 'Asia/Manila'); 

                                                      

                                                      if($mvt->effectivity < $endPeriod->toDateString()){
                                                          $to = $endPeriod->startOfDay(); // Carbon::createFromFormat('Y-m-d H:i:s', $tillWhen->first()->fromPeriod, 'Asia/Manila'); 

                                                      } else $to = Carbon::createFromFormat('Y-m-d H:i:s', $mvt->effectivity, 'Asia/Manila');     

                                                      /* ------------------ 

                                                          check if hindi sakop ng eval period yung pagkakamove ni employee sa yo
                                                          
                                                          ------------------ */

                                                          if ($mvt->effectivity > $endPeriod->startOfDay()){ //if effectivity ng movement eh hindi sakop

                                                            $doNotInclude = true;

                                                          } else { $doNotInclude=false; }
                                                           //$coll->push(['doNotInclude'=> $doNotInclude]);


                                                  break;
                                                  
                                                } // else $coll->push(['no'=>$mvt->immediateHead_details]);
                                                
                                              }
                                            


                                          } else { 

                                            //we now check first if there was STATUS MOVEMENT for newly regularized
                                            // GET ALL his STATUS MVT from latest to oldest

                                            //$checkRegularization = Movement::where('user_id',$emp->id)->where('personnelChange_id','3')->where('isDone','1')->where('effectivity','>=',$currentPeriod->toDateString())->where('effectivity','<=',$endPeriod->toDateString())->orderBy('effectivity','DESC')->get(); 

                                            $checkRegularization= DB::table('movement')->leftJoin('movement_statuses','movement.id','=','movement_statuses.movement_id')->where('movement.user_id',$emp->id)->where('movement.effectivity','>=',$currentPeriod->toDateString())->where('movement.effectivity','<=',$endPeriod->toDateString())->where('movement_statuses.status_id_new','4')->select('movement.effectivity')->get();

                                            if (count($checkRegularization) > 0)
                                            {
                                              
                                              $fr = Carbon::parse($checkRegularization[0]->effectivity,"Asia/Manila")->startOfDay();
                                              $to = $endPeriod->startOfDay();
                                              $doNotInclude = false;

                                            } else {
                                              $fr = $currentPeriod->startOfDay(); $to = $endPeriod->startOfDay(); $doNotInclude=false;
                                            }

                                          }

                                        

                                          $evalBy = User::find($emp->id)->supervisor->immediateHead_Campaigns_id;
                                          $existing = EvalForm::where('user_id', $emp->id)->where('evalSetting_id',1)->where('evaluatedBy', $evalBy)->where('endPeriod','<=',$to)->where('startPeriod','>=', $fr)->orderBy('id','DESC')->get();
                                          $coll->push(['existing'=>$existing]);
                                          


                                          if (count($existing) == 0 ){

                                             if ($doNotInclude) { /* do nothing */}
                                              else { 
                                                $doneEval[$emp->id] = ['evaluated'=>0,'isDraft'=>0, 'evalForm_id'=> null, 'score'=>null, 'startPeriod'=>$fr->format('M d, Y'), 'endPeriod'=>$to->format('M d, Y')];
                                                //$mySubordinates->push($emp);
                                                $leadershipcheck = ImmediateHead::where('employeeNumber', $emp->employeeNumber)->get();
                                                (count($leadershipcheck) > 0 || Position::find($emp->position_id)->leadershipRole) ? $isLead=true : $isLead=false;
                                                $mySubordinates->push(['data'=>$emp, 'isLead'=>$isLead]);
                                              } 

                                          } 
                                          else 
                                          {
                                              //$truegrade = 100-((100-(EvalForm::find( $existing->first()->id)->overAllScore))*0.5);
                                              // *** VERIFICATION FIRST NA DAPAT INCLUDED OR NOT
                                              if (!$doNotInclude)
                                              {
                                                $theeval = EvalForm::find( $existing->sortByDesc('id')->first()->id);
                                                $truegrade = $theeval->overAllScore;

                                                if ($theeval->isDraft == 1) 
                                                  $doneEval[$emp->id] = ['evaluated'=>1, 'isDraft'=>1, 'evalForm_id'=> $existing->first()->id, 'score'=>$truegrade, 'startPeriod'=>$fr->format('M d, Y'),'endPeriod'=>$to->format('M d, Y')];
                                                else
                                                //$doneEval[$emp->id] = ['evaluated'=>1, 'evalForm_id'=> $existing->first()->id, 'score'=>$truegrade, 'startPeriod'=>$currentPeriod->format('M d, Y'), 'endPeriod'=>$endPeriod->format('M d, Y')];
                                                $doneEval[$emp->id] = ['evaluated'=>1, 'isDraft'=>0, 'evalForm_id'=> $existing->first()->id, 'score'=>$truegrade, 'startPeriod'=>date('M d, Y', strtotime($existing->first()->startPeriod)), 'endPeriod'=>$endPeriod->format('M d, Y')];

                                              
                                              $leadershipcheck = ImmediateHead::where('employeeNumber', $emp->employeeNumber)->get();
                                              (count($leadershipcheck) > 0 || Position::find($emp->position_id)->leadershipRole) ? $isLead=true : $isLead=false;
                                              $mySubordinates->push(['data'=>$emp, 'isLead'=>$isLead]);

                                              }

                                              

                                              
                                          } 





                                  }//end foreach
                                

                                 
                                  /* ---------------------------------------------------------------- 

                                      GET PAST MEMBERS moved to you

                                  /* ---------------------------------------------------------------- */


                                  
                                      $changedImmediateHeads = new Collection;
                                      $doneMovedEvals = new Collection;
                                      
                                      $data = $this->getPastMemberEvals($mc, $evalSetting, $currentPeriod,$endPeriod,null);

                                      //return $data;


                                    //return $data;

                                      $changedImmediateHeads = $data->first()['changedImmediateHeads'];
                                      

                                      $doneMovedEvals = $data->first()['doneMovedEvals'];


                                  
                                  //return $coll;
                                  return view('showThoseUpFor', compact('mySubordinates', 'evalTypes', 'evalSetting', 'doneEval','doneMovedEvals','changedImmediateHeads','currentPeriod','endPeriod'));




                            } else {

                                  $existing = EvalForm::where('user_id', $employee->id)->where('evalSetting_id',1)->where('startPeriod',$currentPeriod->startOfDay())->get();


                                  if ($existing->isEmpty()) $doneEval[$employee->id] = ['evaluated'=>0, 'evalForm_id'=> null, 'score'=>null, 'startPeriod'=>$currentPeriod->format('M d, Y'), 'endPeriod'=>$endPeriod->format('M d, Y')];
                                  else {
                                      //$truegrade = 100-((100-(EvalForm::find( $existing->first()->id)->overAllScore))*0.5);

                                              $theeval = EvalForm::find( $existing->sortByDesc('id')->first()->id);
                                              $truegrade = $theeval->overAllScore;

                                              if ($theeval->isDraft) 
                                                $doneEval[$emp->id] = ['evaluated'=>1, 'isDraft'=>1, 'evalForm_id'=> $existing->first()->id, 'score'=>$truegrade, 'startPeriod'=>$currentPeriod->startOfDay()->format('M d, Y'), 'endPeriod'=>$endPeriod->startOfDay()->format('M d, Y')];
                                              else
                                              //$doneEval[$emp->id] = ['evaluated'=>1, 'evalForm_id'=> $existing->first()->id, 'score'=>$truegrade, 'startPeriod'=>$currentPeriod->format('M d, Y'), 'endPeriod'=>$endPeriod->format('M d, Y')];
                                              $doneEval[$emp->id] = ['evaluated'=>1, 'isDraft'=>0, 'evalForm_id'=> $existing->first()->id, 'score'=>$truegrade, 'startPeriod'=>date('M d, Y', strtotime($existing->first()->startPeriod)), 'endPeriod'=>$endPeriod->format('M d, Y')];



                                      //$truegrade = EvalForm::find( $existing->first()->id)->overAllScore;
                                      //if ($truegrade == 0) $doneEval[$employee->id] = ['evaluated'=>0, 'evalForm_id'=> $existing->first()->id, 'score'=>$truegrade, 'startPeriod'=>$currentPeriod->format('M d, Y'), 'endPeriod'=>$endPeriod->format('M d, Y')];
                                      //else
                                      //$doneEval[$employee->id] = ['evaluated'=>1, 'evalForm_id'=> $existing->first()->id, 'score'=>$truegrade, 'startPeriod'=>$currentPeriod->format('M d, Y'), 'endPeriod'=>$endPeriod->format('M d, Y')];
                                  }
                                  return view('agentView-showThoseUpFor', compact('employee', 'myCampaign', 'evalTypes', 'evalSetting', 'doneEval')); 


                            }//end else an agent


                      

                        } break;

                case 2: { //Jul-Dec semi-annual

                            //check first if it's too early to show. If yes, year-1. If July-Dec na, show current year

                            if (date('m') >= 7 && date('m')<= 12)
                            {
                              $currentPeriod = Carbon::create((date("Y")), $evalSetting->startMonth, $evalSetting->startDate,0,0,0, 'Asia/Manila');
                              $endPeriod = Carbon::create((date("Y")), $evalSetting->endMonth, $evalSetting->endDate,0,0,0, 'Asia/Manila');

                            } else
                            {
                              $currentPeriod = Carbon::create((date("Y")-1), $evalSetting->startMonth, $evalSetting->startDate,0,0,0, 'Asia/Manila');
                              $endPeriod = Carbon::create((date("Y")-1), $evalSetting->endMonth, $evalSetting->endDate,0,0,0, 'Asia/Manila');

                            }




                            
                            //$me = ImmediateHead::where('employeeNumber',$this->user->employeeNumber)->first();
                            $coll = new Collection;

                            // $coll->push(['currentPeriod'=>$currentPeriod, 'endPeriod'=>$endPeriod]);
                            // return $coll;

                            if ($this->user->userType_id !== 4 && !($leadershipcheck->isEmpty())) //if not AGENT
                            {
                                  $mySubordinates1 =  $myActiveTeam->filter(function ($employee)
                                                      {   // Contrctual || Regular or Consultant or Floating or Contractual extended
                                                          return ($employee->status_id == 1 || $employee->status_id == 4 || $employee->status_id == 5 || $employee->status_id == 6 || $employee->status_id == 10 );
                                                      });
                                 
                                  
                                  foreach ($mySubordinates1->sortBy('lastname') as $emp) {

                                     /* ------------

                                          We need to make sure emp is 6++ months already  */

                                          $hired = Carbon::createFromFormat('Y-m-d H:i:s', $emp->dateHired);

                                          $serviceLength = $hired->diffInMonths($endPeriod);

                                          if ($serviceLength >= 6) $mySubordinates2->push($emp);

                                          /* --------------- */
                                  }





                                  foreach ($mySubordinates2->sortBy('lastname') as $emp) {


                                          /* ------------

                                          We need to check if this subordinate has just been moved to you

                                          ---------------*/

                                          // GET ALL his IH movements from latest to oldest
                                          $checkMovements = Movement::where('user_id',$emp->id)->where('personnelChange_id','1')->where('isDone','1')->where('effectivity','>=',$currentPeriod->toDateString())->orderBy('effectivity','DESC')->get(); 

                                         if (count($checkMovements)>0)
                                         {
                                              $checkMovement = $checkMovements->first();

                                              // then isa-isahin mo yung movements, check mo kung ikaw ung latest TL
                                              foreach ($checkMovements as $mvt) {
                                                
                                                if( $myIHCampaignIDs->contains($mvt->immediateHead_details->imHeadCampID_new))
                                                {
                                                  

                                                  $effective = Carbon::createFromFormat('Y-m-d H:i:s', $mvt->effectivity, 'Asia/Manila');

                                                      //pag yung movement from eh dateHired, meaning 1st time nya lang na-move..kunin mo yung effectivity start
                                                      if ($mvt->fromPeriod == $emp->dateHired){
                                                        $fr = Carbon::createFromFormat('Y-m-d H:i:s', $mvt->effectivity, 'Asia/Manila'); 

                                                      } 

                                                      // **** GET THE EVAL RANGES, pag fromPeriod eh wayy past currentPeriod -- get current
                                                      //else if($checkMovement->fromPeriod < $currentPeriod->toDateString()){
                                                      else if (date('Y-m-d',strtotime($mvt->fromPeriod)) > date('Y-m-d',strtotime($currentPeriod)) ){ 
                                                         //$coll->push(['pasok'=>date('m',strtotime($mvt->fromPeriod)), 'currentPeriod'=>date('Y-m-d',strtotime($currentPeriod))]); 

                                                          //movement is within range pa, so kunin mo ung effectivity
                                                          if ( (date('Y-m-d',strtotime($mvt->effectivity)) <= date('Y-m-d',strtotime($endPeriod))) && (date('Y-m-d',strtotime($mvt->effectivity)) >= date('Y-m-d',strtotime($currentPeriod))))  
                                                            { $fr =  Carbon::createFromFormat('Y-m-d H:i:s', $mvt->effectivity, 'Asia/Manila'); 
                                                            $coll->push(['from'=>$fr, 'pasok'=>date('Y-m-d',strtotime($mvt->fromPeriod)), 'currentPeriod'=>date('Y-m-d',strtotime($currentPeriod))]);  }
                                                          else
                                                           {$fr = $currentPeriod->startOfDay(); $coll->push(['no'=>$mvt->immediateHead_details]);}

                                                      } else  {  $coll->push(['from'=>date('Y-m-d',strtotime($mvt->fromPeriod)), 'currentPeriod'=>$currentPeriod->toDateString()]); $fr = Carbon::createFromFormat('Y-m-d H:i:s', $mvt->effectivity, 'Asia/Manila'); } //Carbon::createFromFormat('Y-m-d H:i:s', $checkMovement->fromPeriod, 'Asia/Manila'); 

                                                      

                                                      if($mvt->effectivity < $endPeriod->toDateString()){
                                                          $to = $endPeriod->startOfDay(); // Carbon::createFromFormat('Y-m-d H:i:s', $tillWhen->first()->fromPeriod, 'Asia/Manila'); 

                                                      } else $to = Carbon::createFromFormat('Y-m-d H:i:s', $mvt->effectivity, 'Asia/Manila');     

                                                      /* ------------------ 

                                                          check if hindi sakop ng eval period yung pagkakamove ni employee sa yo
                                                          
                                                          ------------------ */

                                                          if ($mvt->effectivity > $endPeriod->startOfDay()){ //if effectivity ng movement eh hindi sakop

                                                            $doNotInclude = true;

                                                          } else { $doNotInclude=false; }
                                                           //$coll->push(['doNotInclude'=> $doNotInclude]);


                                                  break;
                                                  
                                                } // else $coll->push(['no'=>$mvt->immediateHead_details]);
                                                
                                              }
                                            


                                          } else { 
                                            $fr = $currentPeriod->startOfDay(); $to = $endPeriod->startOfDay(); $doNotInclude=false;

                                          }

                                        

                                          $evalBy = User::find($emp->id)->supervisor->immediateHead_Campaigns_id;
                                          $existing = EvalForm::where('user_id', $emp->id)->where('evalSetting_id',2)->where('evaluatedBy', $evalBy)->where('endPeriod','<=',$to)->where('startPeriod','>=', $fr)->orderBy('id','DESC')->get();
                                          $coll->push(['existing'=>$existing]);
                                          


                                          if (count($existing) == 0 ){

                                             if ($doNotInclude) { /* do nothing */}
                                              else { 
                                                $doneEval[$emp->id] = ['evaluated'=>0,'isDraft'=>0, 'evalForm_id'=> null, 'score'=>null, 'startPeriod'=>$fr->format('M d, Y'), 'endPeriod'=>$to->format('M d, Y')];
                                                //$mySubordinates->push($emp);
                                                $leadershipcheck = ImmediateHead::where('employeeNumber', $emp->employeeNumber)->get();
                                                (count($leadershipcheck) > 0 || Position::find($emp->position_id)->leadershipRole) ? $isLead=true : $isLead=false;
                                                $mySubordinates->push(['data'=>$emp, 'isLead'=>$isLead]);
                                              } 

                                          } 
                                          else 
                                          {
                                              //$truegrade = 100-((100-(EvalForm::find( $existing->first()->id)->overAllScore))*0.5);
                                              // *** VERIFICATION FIRST NA DAPAT INCLUDED OR NOT
                                              if (!$doNotInclude)
                                              {
                                                $theeval = EvalForm::find( $existing->sortByDesc('id')->first()->id);
                                                $truegrade = $theeval->overAllScore;

                                                if ($theeval->isDraft == 1) 
                                                  $doneEval[$emp->id] = ['evaluated'=>1, 'isDraft'=>1, 'evalForm_id'=> $existing->first()->id, 'score'=>$truegrade, 'startPeriod'=>$fr->format('M d, Y'),'endPeriod'=>$to->format('M d, Y')];
                                                else
                                                //$doneEval[$emp->id] = ['evaluated'=>1, 'evalForm_id'=> $existing->first()->id, 'score'=>$truegrade, 'startPeriod'=>$currentPeriod->format('M d, Y'), 'endPeriod'=>$endPeriod->format('M d, Y')];
                                                $doneEval[$emp->id] = ['evaluated'=>1, 'isDraft'=>0, 'evalForm_id'=> $existing->first()->id, 'score'=>$truegrade, 'startPeriod'=>date('M d, Y', strtotime($existing->first()->startPeriod)), 'endPeriod'=>$endPeriod->format('M d, Y')];

                                              
                                              $leadershipcheck = ImmediateHead::where('employeeNumber', $emp->employeeNumber)->get();
                                              (count($leadershipcheck) > 0 || Position::find($emp->position_id)->leadershipRole) ? $isLead=true : $isLead=false;
                                              $mySubordinates->push(['data'=>$emp, 'isLead'=>$isLead]);

                                              }

                                              

                                              
                                          } 





                                  }//end foreach
                                

                                 
                                  /* ---------------------------------------------------------------- 

                                      GET PAST MEMBERS moved to you

                                  /* ---------------------------------------------------------------- */


                                  
                                      $changedImmediateHeads = new Collection;
                                      $doneMovedEvals = new Collection;
                                      
                                      $data = $this->getPastMemberEvals($mc, $evalSetting, $currentPeriod,$endPeriod,null);

                                     


                                    //return $data;

                                      $changedImmediateHeads = $data->first()['changedImmediateHeads'];
                                      //return $mc;

                                      $doneMovedEvals = $data->first()['doneMovedEvals'];


                                  
                                   //return $evalSetting; //[0]['data']->firstname;
                                  return view('showThoseUpFor', compact('mySubordinates', 'evalTypes', 'evalSetting', 'doneEval','doneMovedEvals','changedImmediateHeads','currentPeriod','endPeriod'));




                            } else {

                                  $existing = EvalForm::where('user_id', $employee->id)->where('evalSetting_id',2)->where('startPeriod',$currentPeriod->startOfDay())->get();


                                  if ($existing->isEmpty()) $doneEval[$employee->id] = ['evaluated'=>0, 'evalForm_id'=> null, 'score'=>null, 'startPeriod'=>$currentPeriod->format('M d, Y'), 'endPeriod'=>$endPeriod->format('M d, Y')];
                                  else {
                                      //$truegrade = 100-((100-(EvalForm::find( $existing->first()->id)->overAllScore))*0.5);

                                              $theeval = EvalForm::find( $existing->sortByDesc('id')->first()->id);
                                              $truegrade = $theeval->overAllScore;

                                              if ($theeval->isDraft) 
                                                $doneEval[$emp->id] = ['evaluated'=>1, 'isDraft'=>1, 'evalForm_id'=> $existing->first()->id, 'score'=>$truegrade, 'startPeriod'=>$currentPeriod->startOfDay()->format('M d, Y'), 'endPeriod'=>$endPeriod->startOfDay()->format('M d, Y')];
                                              else
                                              //$doneEval[$emp->id] = ['evaluated'=>1, 'evalForm_id'=> $existing->first()->id, 'score'=>$truegrade, 'startPeriod'=>$currentPeriod->format('M d, Y'), 'endPeriod'=>$endPeriod->format('M d, Y')];
                                              $doneEval[$emp->id] = ['evaluated'=>1, 'isDraft'=>0, 'evalForm_id'=> $existing->first()->id, 'score'=>$truegrade, 'startPeriod'=>date('M d, Y', strtotime($existing->first()->startPeriod)), 'endPeriod'=>$endPeriod->format('M d, Y')];



                                      //$truegrade = EvalForm::find( $existing->first()->id)->overAllScore;
                                      //if ($truegrade == 0) $doneEval[$employee->id] = ['evaluated'=>0, 'evalForm_id'=> $existing->first()->id, 'score'=>$truegrade, 'startPeriod'=>$currentPeriod->format('M d, Y'), 'endPeriod'=>$endPeriod->format('M d, Y')];
                                      //else
                                      //$doneEval[$employee->id] = ['evaluated'=>1, 'evalForm_id'=> $existing->first()->id, 'score'=>$truegrade, 'startPeriod'=>$currentPeriod->format('M d, Y'), 'endPeriod'=>$endPeriod->format('M d, Y')];
                                  }
                                  return view('agentView-showThoseUpFor', compact('employee', 'myCampaign', 'evalTypes', 'evalSetting', 'doneEval')); 


                            }//end else not an agent


                        } break;

                case 3: { //Regularization
                          $changedImmediateHeads=null; $doneMovedEvals=null;
                          if ($this->user->userType_id !== 4 && !($leadershipcheck->isEmpty())) //if not AGENT
                         {
                            $mySubordinates1 = $myActiveTeam->filter(
                              function ($employee) {
                              return ($employee->status_id == 1 || $employee->status_id == 2 || $employee->status_id == 3 || $employee->status_id == 5 || $employee->status_id == 6 || $employee->status_id == 10); 
                            }); //filter out regular employees

                            foreach ($mySubordinates1 as $emp)
                            {
                               $leadershipcheck = ImmediateHead::where('employeeNumber', $emp->employeeNumber)->get();
                               (count($leadershipcheck) > 0 || Position::find($emp->position_id)->leadershipRole) ? $isLead=true : $isLead=false;
                               $mySubordinates->push(['data'=>$emp, 'isLead'=>$isLead]);
                            }

                            

                             $coll= new Collection;
                             
                             foreach ($mySubordinates1 as $emp) {
                               /* ------------

                                    We need to check if this subordinate has just been moved to you

                                    ---------------*/

                                    $currentPeriod = Carbon::createFromFormat('Y-m-d H:i:s',$emp->dateHired,'Asia/Manila');
                                    $cPeriod = Carbon::createFromFormat('Y-m-d H:i:s',$emp->dateHired,'Asia/Manila');
                                    $endPeriod = $cPeriod->addMonths(6);

                                    //$checkMovement = User::find($emp->id)->movements;
                                    $checkMovement = Movement::where('user_id',$emp->id)->where('personnelChange_id','1')->where('isDone',true)->where('effectivity','>=',$currentPeriod->startOfDay())->where('effectivity','<=',$endPeriod->startOfDay())->first();
                                   
                                    //$coll->push($checkMovement);

                                    if (!empty($checkMovement)){
                                       //$existing = EvalForm::where('user_id', $emp->id)->where('startPeriod',$currentPeriod)->get();
                                        $effective = Carbon::createFromFormat('Y-m-d H:i:s', $checkMovement->effectivity, 'Asia/Manila');

                                        //pag yung movement from eh dateHired, meaning 1st time nya lang na-move..kunin mo yung effectivity start
                                        if ($checkMovement->fromPeriod == $emp->dateHired){
                                          $fr = Carbon::createFromFormat('Y-m-d H:i:s', $checkMovement->effectivity, 'Asia/Manila'); 

                                        } 

                                        // **** GET THE EVAL RANGES, pag fromPeriod eh wayy past currentPeriod -- get current
                                        else if($checkMovement->fromPeriod < $currentPeriod){
                                            $fr = $currentPeriod->startOfDay(); // Carbon::createFromFormat('Y-m-d H:i:s', $tillWhen->first()->fromPeriod, 'Asia/Manila'); 

                                        } else $fr = Carbon::createFromFormat('Y-m-d H:i:s', $checkMovement->fromPeriod, 'Asia/Manila'); 

                                        if($checkMovement->effectivity < $endPeriod){
                                            $to = $endPeriod->startOfDay(); // Carbon::createFromFormat('Y-m-d H:i:s', $tillWhen->first()->fromPeriod, 'Asia/Manila'); 

                                        } else $to = Carbon::createFromFormat('Y-m-d H:i:s', $checkMovement->effectivity, 'Asia/Manila');     

                                    
                                  } else { 
                                      $fr = $currentPeriod->startOfDay(); $to = $endPeriod->startOfDay();

                                    }

                                    $evalBy = User::find($emp->id)->supervisor->immediateHead_Campaigns_id;
                                    $existing = EvalForm::where('evalSetting_id',$request->evalType_id)->where('user_id', $emp->id)->where('evaluatedBy', $evalBy)->where('endPeriod','<=',$to)->where('startPeriod','>=', $fr)->orderBy('id','DESC')->get();
                                    $coll->push($existing);
                            
                                    if (count($existing) == 0) $doneEval[$emp->id] = ['evaluated'=>0, 'isDraft'=>0, 'evalForm_id'=> null, 'score'=>null, 'startPeriod'=>$fr->format('M d, Y'), 'endPeriod'=>$endPeriod->format('M d, Y')];
                                    
                                    else {
                                        //$truegrade = 100-((100-(EvalForm::find( $existing->first()->id)->overAllScore))*0.5);

                                       $theeval = EvalForm::find( $existing->sortByDesc('id')->first()->id);
                                        $truegrade = $theeval->overAllScore;


                                        if ($theeval->isDraft) 
                                          $doneEval[$emp->id] = ['evaluated'=>1, 'isDraft'=>1, 'evalForm_id'=> $existing->sortByDesc('id')->first()->id, 'score'=>$truegrade, 'startPeriod'=>$currentPeriod->startOfDay()->format('M d, Y'), 'endPeriod'=>$endPeriod->startOfDay()->format('M d, Y')];
                                        else
                                        //$doneEval[$emp->id] = ['evaluated'=>1, 'evalForm_id'=> $existing->first()->id, 'score'=>$truegrade, 'startPeriod'=>$currentPeriod->format('M d, Y'), 'endPeriod'=>$endPeriod->format('M d, Y')];
                                        $doneEval[$emp->id] = ['evaluated'=>1, 'isDraft'=>0, 'evalForm_id'=> $existing->sortByDesc('id')->first()->id, 'score'=>$truegrade, 'startPeriod'=>date('M d, Y', strtotime($existing->sortByDesc('id')->first()->startPeriod)), 'endPeriod'=>$endPeriod->format('M d, Y')];

                                    } 



                            }//end foreach
                          
                            //return $coll;
                            // return $doneEval;

                            /* ---------------------------------------------------------------- 

                                GET PAST MEMBERS moved to you

                            /* ---------------------------------------------------------------- */

                          

                            $changedImmediateHeads = new Collection;
                            $doneMovedEvals = new Collection;
                            $data = $this->getPastMemberEvals($mc, $evalSetting,null,null,$request->evalType_id);

                            //return ['doneEval'=>count($data->first()['doneMovedEvals']), 'changedImmediateHeads'=>count($data->first()['changedImmediateHeads']),'changedHeads'=>count($data->first()['changedHeads'])];

                            //return $data;

                             $changedImmediateHeads1 = $data->first()['changedImmediateHeads'];
                            //return $changedImmediateHeads1;
                            foreach($changedImmediateHeads1 as $ch){
                              $stat = User::find($ch['user_id'])->status_id;
                              // contractual | trainee | probi | consult | extended | projectBased
                              if ($stat == 1 || $stat == 2 || $stat == 3 || $stat == 5 || $stat == 6 || $stat == 10 || $stat == 11)
                                $changedImmediateHeads->push($ch);
                            }
                            $doneMovedEvals = $data->first()['doneMovedEvals'];

                           
                            return view('showThoseUpFor', compact('mySubordinates', 'myCampaign', 'evalTypes', 'evalSetting', 'doneEval','doneMovedEvals','changedImmediateHeads','currentPeriod','endPeriod'));


                         } else {
                            $currentPeriod = Carbon::createFromFormat('Y-m-d H:i:s',$employee->dateHired,'Asia/Manila');
                            $existing = EvalForm::where('user_id', $employee->id)->where('evalSetting_id',$evalSetting->id)->where('startPeriod',$currentPeriod->format('Y-m-d H:i:s'))->orderBy('id','DESC')->get();
                            if ($existing->isEmpty()) {
                                    
                                    $toPeriod = Carbon::createFromFormat('Y-m-d H:i:s',$employee->dateHired,'Asia/Manila');
                                    $tPeriod = Carbon::createFromFormat('Y-m-d H:i:s',$employee->dateHired,'Asia/Manila');
                                    $endPeriod = $tPeriod->addMonths(6);
                                    $doneEval[$employee->id] = ['evaluated'=>0, 'evalForm_id'=> null, 'score'=>null, 'startPeriod'=>$toPeriod->format('M d, Y'), 'endPeriod'=>$endPeriod->format('M d, Y')];
                                } 
                                else {
                                    //$truegrade = 100-((100-(EvalForm::find( $existing->first()->id)->overAllScore))*0.5);
                                    $truegrade = EvalForm::find( $existing->first()->id)->overAllScore;

                                    if ($truegrade == 0){
                                        $endPeriod = Carbon::createFromFormat('Y-m-d H:i:s',$existing->first()->endPeriod,'Asia/Manila');
                                        $doneEval[$employee->id] = ['evaluated'=>0, 'evalForm_id'=> $existing->first()->id, 'score'=>$truegrade, 'startPeriod'=>$currentPeriod->format('M d, Y'), 'endPeriod'=>$endPeriod->format('M d, Y')];
                                    }  
                                    else {
                                        $endPeriod = Carbon::createFromFormat('Y-m-d H:i:s',$existing->first()->endPeriod,'Asia/Manila');
                                        $doneEval[$employee->id] = ['evaluated'=>1, 'evalForm_id'=> $existing->first()->id, 'score'=>$truegrade, 'startPeriod'=>$currentPeriod->format('M d, Y'), 'endPeriod'=>$endPeriod->format('M d, Y')];

                                    }
                                        
                                } 


                                return view('agentView-showThoseUpFor', compact('employee', 'myCampaign', 'evalTypes', 'evalSetting', 'doneEval'));
                         }// end else an agent

                             

                        } break;

                case 4: { // Extended Contractual
                          $changedImmediateHeads=null; $doneMovedEvals=null;
                          if ($this->user->userType_id !== 4 && !($leadershipcheck->isEmpty())) //if not AGENT
                         {
                            $mySubordinates1 = $myActiveTeam->filter(
                              function ($employee) {
                              return ($employee->status_id == 1 || $employee->status_id == 2 || $employee->status_id == 3 || $employee->status_id == 11 ); 
                            }); //filter out regular employees

                            foreach ($mySubordinates1 as $emp)
                            {
                               $leadershipcheck = ImmediateHead::where('employeeNumber', $emp->employeeNumber)->get();
                               (count($leadershipcheck) > 0 || Position::find($emp->position_id)->leadershipRole) ? $isLead=true : $isLead=false;
                               $mySubordinates->push(['data'=>$emp, 'isLead'=>$isLead]);
                            }


                             $coll= new Collection;
                             
                             foreach ($mySubordinates1 as $emp) {
                               /* ------------

                                    We need to check if this subordinate has just been moved to you

                                    ---------------*/

                                    $currentPeriod = Carbon::createFromFormat('Y-m-d H:i:s',$emp->dateHired,'Asia/Manila');
                                    $cPeriod = Carbon::createFromFormat('Y-m-d H:i:s',$emp->dateHired,'Asia/Manila');

                                    //********* for Project Based, one month period lang muna 
                                    if ($emp->status_id == 11)
                                      $endPeriod = $cPeriod->addMonths(1);
                                    else $endPeriod = $cPeriod->addMonths(3);


                                    //$checkMovement = User::find($emp->id)->movements;
                                    $checkMovement = Movement::where('user_id',$emp->id)->where('personnelChange_id','1')->where('isDone',true)->where('effectivity','>=',$currentPeriod->startOfDay())->where('effectivity','<=',$endPeriod->startOfDay())->first();
                                   
                                    //$coll->push($checkMovement);

                                    if (!empty($checkMovement)){
                                       //$existing = EvalForm::where('user_id', $emp->id)->where('startPeriod',$currentPeriod)->get();
                                        $effective = Carbon::createFromFormat('Y-m-d H:i:s', $checkMovement->effectivity, 'Asia/Manila');

                                        //pag yung movement from eh dateHired, meaning 1st time nya lang na-move..kunin mo yung effectivity start
                                        if ($checkMovement->fromPeriod == $emp->dateHired){
                                          $fr = Carbon::createFromFormat('Y-m-d H:i:s', $checkMovement->effectivity, 'Asia/Manila'); 

                                        } 

                                        // **** GET THE EVAL RANGES, pag fromPeriod eh wayy past currentPeriod -- get current
                                        else if($checkMovement->fromPeriod < $currentPeriod){
                                            $fr = $currentPeriod->startOfDay(); // Carbon::createFromFormat('Y-m-d H:i:s', $tillWhen->first()->fromPeriod, 'Asia/Manila'); 

                                        } else $fr = Carbon::createFromFormat('Y-m-d H:i:s', $checkMovement->fromPeriod, 'Asia/Manila'); 

                                        if($checkMovement->effectivity < $endPeriod){
                                            $to = $endPeriod->startOfDay(); // Carbon::createFromFormat('Y-m-d H:i:s', $tillWhen->first()->fromPeriod, 'Asia/Manila'); 

                                        } else $to = Carbon::createFromFormat('Y-m-d H:i:s', $checkMovement->effectivity, 'Asia/Manila');     

                                    
                                  } else { 
                                      $fr = $currentPeriod->startOfDay(); $to = $endPeriod->startOfDay();

                                    }

                                    $evalBy = User::find($emp->id)->supervisor->immediateHead_Campaigns_id;
                                    //$existing = EvalForm::where('user_id', $emp->id)->where('evaluatedBy', $evalBy)->where('endPeriod','<=',$to)->where('startPeriod','>=', $fr)->orderBy('id','DESC')->get();
                                    $existing = EvalForm::where('evalSetting_id',$request->evalType_id)->where('user_id', $emp->id)->where('evaluatedBy', $evalBy)->where('endPeriod','<=',$to)->where('startPeriod','>=', $fr)->orderBy('id','DESC')->get();
                                    $coll->push($existing);
                            
                                    if (count($existing) == 0) $doneEval[$emp->id] = ['evaluated'=>0, 'isDraft'=>0, 'evalForm_id'=> null, 'score'=>null, 'startPeriod'=>$fr->format('M d, Y'), 'endPeriod'=>$endPeriod->format('M d, Y')];
                                    
                                    else {
                                        //$truegrade = 100-((100-(EvalForm::find( $existing->first()->id)->overAllScore))*0.5);

                                       $theeval = EvalForm::find( $existing->sortByDesc('id')->first()->id);
                                        $truegrade = $theeval->overAllScore;


                                        if ($theeval->isDraft) 
                                          $doneEval[$emp->id] = ['evaluated'=>1, 'isDraft'=>1, 'evalForm_id'=> $existing->sortByDesc('id')->first()->id, 'score'=>$truegrade, 'startPeriod'=>$currentPeriod->startOfDay()->format('M d, Y'), 'endPeriod'=>$endPeriod->startOfDay()->format('M d, Y')];
                                        else
                                        //$doneEval[$emp->id] = ['evaluated'=>1, 'evalForm_id'=> $existing->first()->id, 'score'=>$truegrade, 'startPeriod'=>$currentPeriod->format('M d, Y'), 'endPeriod'=>$endPeriod->format('M d, Y')];
                                        $doneEval[$emp->id] = ['evaluated'=>1, 'isDraft'=>0, 'evalForm_id'=> $existing->sortByDesc('id')->first()->id, 'score'=>$truegrade, 'startPeriod'=>date('M d, Y', strtotime($existing->sortByDesc('id')->first()->startPeriod)), 'endPeriod'=>$endPeriod->format('M d, Y')];

                                    } 



                            }//end foreach
                          
                            //return $coll;
                            // return $doneEval;

                            /* ---------------------------------------------------------------- 

                                GET PAST MEMBERS moved to you

                            /* ---------------------------------------------------------------- */

                          

                            $changedImmediateHeads = new Collection;
                            $doneMovedEvals = new Collection;
                            $data = $this->getPastMemberEvals($mc, $evalSetting, null,null, $request->evalType_id);
                            //return $data;

                             $changedImmediateHeads1 = $data->first()['changedImmediateHeads'];
                            //return $changedImmediateHeads1;
                            foreach($changedImmediateHeads1 as $ch){
                              $stat = User::find($ch['user_id'])->status_id;
                              // contractual | trainee | probi | consult | extended | projectBased
                              if ($stat == 1 || $stat == 2 || $stat == 3 || $stat == 5 || $stat == 6 || $stat == 10 || $stat == 11)
                                $changedImmediateHeads->push($ch);
                            }
                            $doneMovedEvals = $data->first()['doneMovedEvals'];

                            //$doneMovedEvals=null;

                           
                            return view('showThoseUpFor', compact('mySubordinates', 'myCampaign', 'evalTypes', 'evalSetting', 'doneEval','doneMovedEvals','changedImmediateHeads','currentPeriod','endPeriod'));


                         } else {
                            $currentPeriod = Carbon::createFromFormat('Y-m-d H:i:s',$employee->dateHired,'Asia/Manila');
                            $existing = EvalForm::where('user_id', $employee->id)->where('evalSetting_id',$evalSetting->id)->where('startPeriod',$currentPeriod->format('Y-m-d H:i:s'))->orderBy('id','DESC')->get();
                            if ($existing->isEmpty()) {
                                    
                                    $toPeriod = Carbon::createFromFormat('Y-m-d H:i:s',$employee->dateHired,'Asia/Manila');
                                    $tPeriod = Carbon::createFromFormat('Y-m-d H:i:s',$employee->dateHired,'Asia/Manila');
                                    $endPeriod = $tPeriod->addMonths(3);
                                    $doneEval[$employee->id] = ['evaluated'=>0, 'evalForm_id'=> null, 'score'=>null, 'startPeriod'=>$toPeriod->format('M d, Y'), 'endPeriod'=>$endPeriod->format('M d, Y')];
                                } 
                                else {
                                    //$truegrade = 100-((100-(EvalForm::find( $existing->first()->id)->overAllScore))*0.5);
                                    $truegrade = EvalForm::find( $existing->first()->id)->overAllScore;

                                    if ($truegrade == 0){
                                        $endPeriod = Carbon::createFromFormat('Y-m-d H:i:s',$existing->first()->endPeriod,'Asia/Manila');
                                        $doneEval[$employee->id] = ['evaluated'=>0, 'evalForm_id'=> $existing->first()->id, 'score'=>$truegrade, 'startPeriod'=>$currentPeriod->format('M d, Y'), 'endPeriod'=>$endPeriod->format('M d, Y')];
                                    }  
                                    else {
                                        $endPeriod = Carbon::createFromFormat('Y-m-d H:i:s',$existing->first()->endPeriod,'Asia/Manila');
                                        $doneEval[$employee->id] = ['evaluated'=>1, 'evalForm_id'=> $existing->first()->id, 'score'=>$truegrade, 'startPeriod'=>$currentPeriod->format('M d, Y'), 'endPeriod'=>$endPeriod->format('M d, Y')];

                                    }
                                        
                                } 


                                return view('agentView-showThoseUpFor', compact('employee', 'myCampaign', 'evalTypes', 'evalSetting', 'doneEval'));
                         }// end else an agent

                             

                        } break;


               

                case 5: { // Revised 2018 contractual
                          $changedImmediateHeads=null; $doneMovedEvals=null;
                          if ($this->user->userType_id !== 4 && !($leadershipcheck->isEmpty())) //if not AGENT
                         {
                            $mySubordinates1 = $myActiveTeam->filter(
                              function ($employee) {
                              return ($employee->status_id == 1 || $employee->status_id == 2 || $employee->status_id == 3 ); 
                            }); //filter out regular employees

                            foreach ($mySubordinates1 as $emp)
                            {
                               $leadershipcheck = ImmediateHead::where('employeeNumber', $emp->employeeNumber)->get();
                               (count($leadershipcheck) > 0 || Position::find($emp->position_id)->leadershipRole) ? $isLead=true : $isLead=false;
                               $mySubordinates->push(['data'=>$emp, 'isLead'=>$isLead]);
                            }


                             $coll= new Collection;
                             
                             foreach ($mySubordinates1 as $emp) {
                               /* ------------

                                    We need to check if this subordinate has just been moved to you

                                    ---------------*/

                                    $currentPeriod = Carbon::createFromFormat('Y-m-d H:i:s',$emp->dateHired,'Asia/Manila');
                                    $cPeriod = Carbon::createFromFormat('Y-m-d H:i:s',$emp->dateHired,'Asia/Manila');
                                    $endPeriod = $cPeriod->addMonths(3);

                                    //$checkMovement = User::find($emp->id)->movements;
                                    $checkMovement = Movement::where('user_id',$emp->id)->where('personnelChange_id','1')->where('isDone',true)->where('effectivity','>=',$currentPeriod->startOfDay())->where('effectivity','<=',$endPeriod->startOfDay())->first();
                                   
                                    //$coll->push($checkMovement);

                                    if (!empty($checkMovement)){
                                       //$existing = EvalForm::where('user_id', $emp->id)->where('startPeriod',$currentPeriod)->get();
                                        $effective = Carbon::createFromFormat('Y-m-d H:i:s', $checkMovement->effectivity, 'Asia/Manila');

                                        //pag yung movement from eh dateHired, meaning 1st time nya lang na-move..kunin mo yung effectivity start
                                        if ($checkMovement->fromPeriod == $emp->dateHired){
                                          $fr = Carbon::createFromFormat('Y-m-d H:i:s', $checkMovement->effectivity, 'Asia/Manila'); 

                                        } 

                                        // **** GET THE EVAL RANGES, pag fromPeriod eh wayy past currentPeriod -- get current
                                        else if($checkMovement->fromPeriod < $currentPeriod){
                                            $fr = $currentPeriod->startOfDay(); // Carbon::createFromFormat('Y-m-d H:i:s', $tillWhen->first()->fromPeriod, 'Asia/Manila'); 

                                        } else $fr = Carbon::createFromFormat('Y-m-d H:i:s', $checkMovement->fromPeriod, 'Asia/Manila'); 

                                        if($checkMovement->effectivity < $endPeriod){
                                            $to = $endPeriod->startOfDay(); // Carbon::createFromFormat('Y-m-d H:i:s', $tillWhen->first()->fromPeriod, 'Asia/Manila'); 

                                        } else $to = Carbon::createFromFormat('Y-m-d H:i:s', $checkMovement->effectivity, 'Asia/Manila');     

                                    
                                  } else { 
                                      $fr = $currentPeriod->startOfDay(); $to = $endPeriod->startOfDay();

                                    }

                                    $evalBy = User::find($emp->id)->supervisor->immediateHead_Campaigns_id;
                                    //$existing = EvalForm::where('user_id', $emp->id)->where('evaluatedBy', $evalBy)->where('endPeriod','<=',$to)->where('startPeriod','>=', $fr)->orderBy('id','DESC')->get();
                                    $existing = EvalForm::where('evalSetting_id',$request->evalType_id)->where('user_id', $emp->id)->where('evaluatedBy', $evalBy)->where('endPeriod','<=',$to)->where('startPeriod','>=', $fr)->orderBy('id','DESC')->get();
                                    $coll->push($existing);
                            
                                    if (count($existing) == 0) $doneEval[$emp->id] = ['evaluated'=>0, 'isDraft'=>0, 'evalForm_id'=> null, 'score'=>null, 'startPeriod'=>$fr->format('M d, Y'), 'endPeriod'=>$endPeriod->format('M d, Y')];
                                    
                                    else {
                                        //$truegrade = 100-((100-(EvalForm::find( $existing->first()->id)->overAllScore))*0.5);

                                       $theeval = EvalForm::find( $existing->sortByDesc('id')->first()->id);
                                        $truegrade = $theeval->overAllScore;


                                        if ($theeval->isDraft) 
                                          $doneEval[$emp->id] = ['evaluated'=>1, 'isDraft'=>1, 'evalForm_id'=> $existing->sortByDesc('id')->first()->id, 'score'=>$truegrade, 'startPeriod'=>$currentPeriod->startOfDay()->format('M d, Y'), 'endPeriod'=>$endPeriod->startOfDay()->format('M d, Y')];
                                        else
                                        //$doneEval[$emp->id] = ['evaluated'=>1, 'evalForm_id'=> $existing->first()->id, 'score'=>$truegrade, 'startPeriod'=>$currentPeriod->format('M d, Y'), 'endPeriod'=>$endPeriod->format('M d, Y')];
                                        $doneEval[$emp->id] = ['evaluated'=>1, 'isDraft'=>0, 'evalForm_id'=> $existing->sortByDesc('id')->first()->id, 'score'=>$truegrade, 'startPeriod'=>date('M d, Y', strtotime($existing->sortByDesc('id')->first()->startPeriod)), 'endPeriod'=>$endPeriod->format('M d, Y')];

                                    } 



                            }//end foreach
                          
                            //return $coll;
                            // return $doneEval;

                            /* ---------------------------------------------------------------- 

                                GET PAST MEMBERS moved to you

                            /* ---------------------------------------------------------------- */

                          

                            $changedImmediateHeads = new Collection;
                            $doneMovedEvals = new Collection;
                            $data = $this->getPastMemberEvals($mc, $evalSetting, null,null, $request->evalType_id);

                             $changedImmediateHeads1 = $data->first()['changedImmediateHeads'];
                            //return $changedImmediateHeads1;
                            foreach($changedImmediateHeads1 as $ch){
                              $stat = User::find($ch['user_id'])->status_id;
                              // contractual | trainee | probi | consult | extended | projectBased
                              if ($stat == 1 || $stat == 2 || $stat == 3 || $stat == 5 || $stat == 6 || $stat == 10 || $stat == 11)
                                $changedImmediateHeads->push($ch);
                            }
                            $doneMovedEvals = $data->first()['doneMovedEvals'];

                           
                            return view('revisedContractual', compact('mySubordinates', 'myCampaign', 'evalTypes', 'evalSetting', 'doneEval','doneMovedEvals','changedImmediateHeads','currentPeriod','endPeriod'));


                         } else {
                            $currentPeriod = Carbon::createFromFormat('Y-m-d H:i:s',$employee->dateHired,'Asia/Manila');
                            $existing = EvalForm::where('user_id', $employee->id)->where('evalSetting_id',$evalSetting->id)->where('startPeriod',$currentPeriod->format('Y-m-d H:i:s'))->orderBy('id','DESC')->get();
                            if ($existing->isEmpty()) {
                                    
                                    $toPeriod = Carbon::createFromFormat('Y-m-d H:i:s',$employee->dateHired,'Asia/Manila');
                                    $tPeriod = Carbon::createFromFormat('Y-m-d H:i:s',$employee->dateHired,'Asia/Manila');
                                    $endPeriod = $tPeriod->addMonths(3);
                                    $doneEval[$employee->id] = ['evaluated'=>0, 'evalForm_id'=> null, 'score'=>null, 'startPeriod'=>$toPeriod->format('M d, Y'), 'endPeriod'=>$endPeriod->format('M d, Y')];
                                } 
                                else {
                                    //$truegrade = 100-((100-(EvalForm::find( $existing->first()->id)->overAllScore))*0.5);
                                    $truegrade = EvalForm::find( $existing->first()->id)->overAllScore;

                                    if ($truegrade == 0){
                                        $endPeriod = Carbon::createFromFormat('Y-m-d H:i:s',$existing->first()->endPeriod,'Asia/Manila');
                                        $doneEval[$employee->id] = ['evaluated'=>0, 'evalForm_id'=> $existing->first()->id, 'score'=>$truegrade, 'startPeriod'=>$currentPeriod->format('M d, Y'), 'endPeriod'=>$endPeriod->format('M d, Y')];
                                    }  
                                    else {
                                        $endPeriod = Carbon::createFromFormat('Y-m-d H:i:s',$existing->first()->endPeriod,'Asia/Manila');
                                        $doneEval[$employee->id] = ['evaluated'=>1, 'evalForm_id'=> $existing->first()->id, 'score'=>$truegrade, 'startPeriod'=>$currentPeriod->format('M d, Y'), 'endPeriod'=>$endPeriod->format('M d, Y')];

                                    }
                                        
                                } 


                                return view('agentView-showThoseUpFor', compact('employee', 'myCampaign', 'evalTypes', 'evalSetting', 'doneEval'));
                         }// end else an agent

                             

                        } break;

               
            
           
            }
            
            return $coll;
            
            return view('showThoseUpFor', compact('mySubordinates', 'evalTypes', 'evalSetting', 'doneEval','changedImmediateHeads','doneMovedEvals', 'currentPeriod','endPeriod'));
          
    }

     public function newEvaluation($user_id, $evalType_id)
    {

        $evalType = EvalType::find($evalType_id);
        $employee = User::find($user_id);

        $meLeader = $employee->supervisor->first();
        $ratingScale = RatingScale::all();
        $allSummaries = Summary::all();
        $summaries = new Collection;

        $leadershipcheck = ImmediateHead::where('employeeNumber', $employee->employeeNumber)->get();



        foreach ($allSummaries as $key ) {
           if (!($key->columns->isEmpty()) ) 
            {
                $cols = $key->columns;
            } else $cols=null;
           if (!($key->rows->isEmpty()) )
           { 
                $rows = $key->rows;

           }  else $rows = null;
           $summaries->push(['header'=>$key->heading,'details'=>$key->description, 'columns'=>$cols, 'rows'=>$rows]);
        }



        $evalSetting = EvalSetting::where('evalType_id',$evalType_id)->first();

        //$cp = Input::get('currentPeriod');
        //$ep = Input::get('endPeriod');
        $currentPeriod = new Carbon(Input::get('currentPeriod'));
        $endPeriod = new Carbon(Input::get('endPeriod'));

        $competencyAttributes = $evalSetting->competencyAttributes;
        $competencies = $competencyAttributes->groupBy('competency_id');
        $formEntries = new Collection;
        $maxScore = 0;

        $isLead = Input::get('isLead');
       
        if (empty(Input::get('oldPos')) ) $showPosition = $employee->position->name;
          else $showPosition = Input::get('oldPos');


        foreach ($competencies as $key ) {
            $attributes = new Collection;

            foreach ($key as $k) {
                $attributes->push(Attribute::find($k->attribute_id)->name);
            }

            $comp = Competency::find($key[0]->competency_id);
            
            if ( $comp->acrossTheBoard == '0') //check if this competency is for all
            {

                  if (  empty($comp->percentage)   )
                  { //para sa agent sya
                     // ************************ !!! always check first kung may value ung agentPercentage, if not empty 'percentage'=> $comp->agentPercentage
                          // verify mo muna kung agent o leader
                      // $leadershipcheck = ImmediateHead::where('employeeNumber', $employee->employeeNumber)->get();

                       if (  ( $employee->userType_id==4 ) || empty($isLead)  || $employee->leadOverride ){ //agent sya.  $leadershipcheck->isEmpty() ||

                            $formEntries->push([
                          'competency'=>$comp->name, 
                          'definitions'=> $comp->definitions, 
                          'percentage'=>$comp->agentPercentage,
                          'score'=>$comp->agentPercentage*5/100,
                          'id'=>$comp->id,
                          'attributes'=> $attributes]);
                           $maxScore += $comp->agentPercentage*5/100;


                       } else { } //deadma kasi leader sya
                          

                        

                  } else 
                  { 
                    // ---------- para sa leader sya
                    //verify first if employee is really a leader
                        
                          if  ( $employee->userType_id !== 4  && !empty($isLead) && empty($employee->leadOverride) ){ //if employee is not an agent and exists in leaders table && !($leadershipcheck->isEmpty())
                              $formEntries->push([
                                  'competency'=>$comp->name, 
                                  'definitions'=> $comp->definitions, 
                                  'percentage'=>$comp->percentage,
                                  'score'=>$comp->percentage*5/100,
                                  'id'=>$comp->id,
                                  'attributes'=> $attributes]);

                              $maxScore += $comp->percentage*5/100;
                              //var_dump($maxScore);
                               //var_dump("pasok ". $maxScore);
                          
                          } else { }//deadma kasi comp for agent
                    }//end else agentPercentage == null


              

            } else { //end else acrosstheboard sya

              // ************************ !!! always check first kung leader ba sya or agent
              if (  ( $employee->userType_id==4) || empty($isLead)  || $employee->leadOverride ) { //agent sya--- $leadershipcheck->isEmpty() || 
                $formEntries->push([
                      'competency'=>$comp->name, 
                      'definitions'=> $comp->definitions, 
                      'percentage'=>$comp->agentPercentage,
                      'score'=>$comp->agentPercentage*5/100,
                      'id'=>$comp->id,
                      'attributes'=> $attributes]);

                  $maxScore += $comp->agentPercentage*5/100;
                 // var_dump($maxScore);

              } else { //leader sya
                 $formEntries->push([
                    'competency'=>$comp->name, 
                    'definitions'=> $comp->definitions, 
                    'percentage'=>$comp->percentage,
                    'score'=>$comp->percentage*5/100,
                    'id'=>$comp->id,
                    'attributes'=> $attributes]);

                $maxScore += $comp->percentage*5/100;
                //var_dump($maxScore);


              }                


            }//end else acrosstheboard

        }//end foreach competencies

        
        $existingNaBa = EvalForm::where('evalSetting_id',$evalSetting->id)->where('user_id',$employee->id)->where('evaluatedBy',$meLeader->id)->where('startPeriod','>=',$currentPeriod->startOfDay())->where('endPeriod','<=',$endPeriod->startOfDay())->get();

        if ($existingNaBa->isEmpty())
        {
            $evalForm = new EvalForm;
            $evalForm->coachingDone = false;
            $evalForm->coachingTimestamp = null;
            $evalForm->overallScore = 0;
            $evalForm->salaryIncrease = 0;
            $evalForm->startPeriod = $currentPeriod;
            $evalForm->endPeriod = $endPeriod;
            $evalForm->evalSetting_id = $evalSetting->id;
            $evalForm->user_id = $employee->id;

            // *** We need to check first if ikaw ung current immediate head
            // *** if not, then check kung may movement si employee within the current period
            
            $hisCurrentIH = ImmediateHead::find(ImmediateHead_Campaign::find(Team::where('user_id',$employee->id)->first()->immediateHead_Campaigns_id)->immediateHead_id);

            $mcoll = new Collection;

            if ( $hisCurrentIH->employeeNumber == $this->user->employeeNumber ) 
            {
              $evalForm->evaluatedBy = $employee->supervisor->immediateHead_Campaigns_id;
              

            }
            else
            {
              $hasIHmovement = Movement::where('user_id', $employee->id)->where('personnelChange_id','1')->where('effectivity','>=',$currentPeriod->startOfDay())->get(); //where('effectivity','<=', $endPeriod->startOfDay())->get();

              if (!empty($hasIHmovement))
              {
                $me1 = ImmediateHead::where('employeeNumber',$this->user->employeeNumber)->first();
                $mc = ImmediateHead_Campaign::where('immediateHead_id',$me1->id)->select('id')->get();
                
                foreach ($mc as $key) {
                  $mcoll->push($key->id);
                }




                foreach ($hasIHmovement as $mvt) {
                  $ihMvt = Movement_ImmediateHead::where('movement_id',$mvt->id)->first();
                  

                  if (!empty($ihMvt))
                  {
                    if ( $mcoll->contains($ihMvt->imHeadCampID_old) ) 
                    {
                      $evalForm->evaluatedBy = $ihMvt->imHeadCampID_old;
                    }
                  }
                  
                }

              }  else
                // ** just give it to the current
                $evalForm->evaluatedBy = $employee->supervisor->immediateHead_Campaigns_id;

              
              /*$hasIHmovement = Movement::where('user_id', $employee->id)->where('personnelChange_id','1')->where('effectivity','>=',$currentPeriod->startOfDay())->where('effectivity','<=', $endPeriod->startOfDay())->first();

             
              if (!empty($hasIHmovement))
                $evalForm->evaluatedBy = $hasIHmovement->immediateHead_details->imHeadCampID_old;
              else
                // ** just give it to the current
                $evalForm->evaluatedBy = $employee->supervisor->immediateHead_Campaigns_id;*/


            }
            
            $evalForm->isDraft = false;
            $evalForm->save();


        } else
        {
            $evalForm = $existingNaBa->first();


        } 
        //return $mcoll;
       
      return view('evaluation.new-employee', compact('evalType', 'currentPeriod','endPeriod','employee', 'ratingScale', 'evalForm','evalSetting', 'formEntries','maxScore','summaries','showPosition','isLead'));
        
    }

     public function newContractual($user_id, $evalType_id)
    {

        $evalType = EvalType::find($evalType_id);
        $employee = User::find($user_id);

        $meLeader = $employee->supervisor->first();
        $ratingScale = RatingScale::all();
        $allSummaries = Summary::all();
        $summaries = new Collection;

        $leadershipcheck = ImmediateHead::where('employeeNumber', $employee->employeeNumber)->get();



        foreach ($allSummaries as $key ) {
           if (!($key->columns->isEmpty()) ) 
            {
                $cols = $key->columns;
            } else $cols=null;
           if (!($key->rows->isEmpty()) )
           { 
                $rows = $key->rows;

           }  else $rows = null;
           $summaries->push(['header'=>$key->heading,'details'=>$key->description, 'columns'=>$cols, 'rows'=>$rows]);
        }



        $evalSetting = EvalSetting::where('evalType_id',$evalType_id)->first();

        //$cp = Input::get('currentPeriod');
        //$ep = Input::get('endPeriod');
        $currentPeriod = new Carbon(Input::get('currentPeriod'));
        $endPeriod = new Carbon(Input::get('endPeriod'));

        $competencyAttributes = $evalSetting->competencyAttributes;
        $competencies = $competencyAttributes->groupBy('competency_id');
        $formEntries = new Collection;
        $maxScore = 0;

        $isLead = Input::get('isLead');
       
        if (empty(Input::get('oldPos')) ) $showPosition = $employee->position->name;
          else $showPosition = Input::get('oldPos');


        foreach ($competencies as $key ) {
            $attributes = new Collection;

            foreach ($key as $k) {
                $attributes->push(Attribute::find($k->attribute_id)->name);
            }

            $comp = Competency::find($key[0]->competency_id);
            
            if ( $comp->acrossTheBoard == '0') //check if this competency is for all
            {

                  if (  empty($comp->percentage)   )
                  { //para sa agent sya
                     // ************************ !!! always check first kung may value ung agentPercentage, if not empty 'percentage'=> $comp->agentPercentage
                          // verify mo muna kung agent o leader
                      // $leadershipcheck = ImmediateHead::where('employeeNumber', $employee->employeeNumber)->get();

                       if (  ( $employee->userType_id==4 ) || empty($isLead)  || $employee->leadOverride ){ //agent sya.  $leadershipcheck->isEmpty() ||

                            $formEntries->push([
                          'competency'=>$comp->name, 
                          'definitions'=> $comp->definitions, 
                          'percentage'=>$comp->agentPercentage,
                          'score'=>$comp->agentPercentage*5/100,
                          'id'=>$comp->id,
                          'attributes'=> $attributes]);
                           $maxScore += $comp->agentPercentage*5/100;


                       } else { } //deadma kasi leader sya
                          

                        

                  } else 
                  { 
                    // ---------- para sa leader sya
                    //verify first if employee is really a leader
                        
                          if  ( $employee->userType_id !== 4  && !empty($isLead) && empty($employee->leadOverride) ){ //if employee is not an agent and exists in leaders table && !($leadershipcheck->isEmpty())
                              $formEntries->push([
                                  'competency'=>$comp->name, 
                                  'definitions'=> $comp->definitions, 
                                  'percentage'=>$comp->percentage,
                                  'score'=>$comp->percentage*5/100,
                                  'id'=>$comp->id,
                                  'attributes'=> $attributes]);

                              $maxScore += $comp->percentage*5/100;
                              //var_dump($maxScore);
                               //var_dump("pasok ". $maxScore);
                          
                          } else { }//deadma kasi comp for agent
                    }//end else agentPercentage == null


              

            } else { //end else acrosstheboard sya

              // ************************ !!! always check first kung leader ba sya or agent
              if (  ( $employee->userType_id==4) || empty($isLead)  || $employee->leadOverride ) { //agent sya--- $leadershipcheck->isEmpty() || 
                $formEntries->push([
                      'competency'=>$comp->name, 
                      'definitions'=> $comp->definitions, 
                      'percentage'=>$comp->agentPercentage,
                      'score'=>$comp->agentPercentage*5/100,
                      'id'=>$comp->id,
                      'attributes'=> $attributes]);

                  $maxScore += $comp->agentPercentage*5/100;
                 // var_dump($maxScore);

              } else { //leader sya
                 $formEntries->push([
                    'competency'=>$comp->name, 
                    'definitions'=> $comp->definitions, 
                    'percentage'=>$comp->percentage,
                    'score'=>$comp->percentage*5/100,
                    'id'=>$comp->id,
                    'attributes'=> $attributes]);

                $maxScore += $comp->percentage*5/100;
                //var_dump($maxScore);


              }                


            }//end else acrosstheboard

        }//end foreach competencies

        
        $existingNaBa = EvalForm::where('evalSetting_id',$evalSetting->id)->where('user_id',$employee->id)->where('evaluatedBy',$meLeader->id)->where('startPeriod','>=',$currentPeriod->startOfDay())->where('endPeriod','<=',$endPeriod->startOfDay())->get();

        if ($existingNaBa->isEmpty())
        {
            $evalForm = new EvalForm;
            $evalForm->coachingDone = false;
            $evalForm->coachingTimestamp = null;
            $evalForm->overallScore = 0;
            $evalForm->salaryIncrease = 0;
            $evalForm->startPeriod = $currentPeriod;
            $evalForm->endPeriod = $endPeriod;
            $evalForm->evalSetting_id = $evalSetting->id;
            $evalForm->user_id = $employee->id;

            // *** We need to check first if ikaw ung current immediate head
            // *** if not, then check kung may movement si employee within the current period
            
            $hisCurrentIH = ImmediateHead::find(ImmediateHead_Campaign::find(Team::where('user_id',$employee->id)->first()->immediateHead_Campaigns_id)->immediateHead_id);

            $mcoll = new Collection;

            if ( $hisCurrentIH->employeeNumber == $this->user->employeeNumber ) 
            {
              $evalForm->evaluatedBy = $employee->supervisor->immediateHead_Campaigns_id;
              

            }
            else
            {
              $hasIHmovement = Movement::where('user_id', $employee->id)->where('personnelChange_id','1')->where('effectivity','>=',$currentPeriod->startOfDay())->get(); //where('effectivity','<=', $endPeriod->startOfDay())->get();

              if (!empty($hasIHmovement))
              {
                $me1 = ImmediateHead::where('employeeNumber',$this->user->employeeNumber)->first();
                $mc = ImmediateHead_Campaign::where('immediateHead_id',$me1->id)->select('id')->get();
                
                foreach ($mc as $key) {
                  $mcoll->push($key->id);
                }




                foreach ($hasIHmovement as $mvt) {
                  $ihMvt = Movement_ImmediateHead::where('movement_id',$mvt->id)->first();
                  

                  if (!empty($ihMvt))
                  {
                    if ( $mcoll->contains($ihMvt->imHeadCampID_old) ) 
                    {
                      $evalForm->evaluatedBy = $ihMvt->imHeadCampID_old;
                    }
                  }
                  
                }

              }  else
                // ** just give it to the current
                $evalForm->evaluatedBy = $employee->supervisor->immediateHead_Campaigns_id;


            }
            
            $evalForm->isDraft = false;
            $evalForm->save();


        } else
        {
            $evalForm = $existingNaBa->first();


        } 
        //return $mcoll;
       
      return view('evaluation.new-contractualEval', compact('evalType', 'currentPeriod','endPeriod','employee', 'ratingScale', 'evalForm','evalSetting', 'formEntries','maxScore','summaries','showPosition','isLead'));
        
    }


    public function printBlankEval($id)
    {
      $evalForm = EvalForm::find($id);

      if ( empty($evalForm) ) {
        return view('empty');

      } else
      {

        $employee = User::find($evalForm->user_id);
        $leadershipcheck = ImmediateHead::where('employeeNumber', $employee->employeeNumber)->get();
        $allowed = false;

        //if (ImmediateHead::find($employee->immediateHead_Campaigns_id)->employeeNumber == $this->user->employeeNumber ){ $allowed = true; }
        $allowed=true;

        if( $this->user->userType_id == 1 ||  $allowed || $this->user->id == $employee->id )
        {

              $details = $evalForm->details;

              $evalType = EvalType::find($evalForm->setting->evalType_id);
              $evalSetting = EvalSetting::find($evalForm->evalSetting_id);

              
              $ratingScale = RatingScale::all();
              //setup now the evaluation form
              // !! but check first if it's existing already, if not -- create new one

              $currentPeriod = new \DateTime(date("Y")."-".$evalForm->setting->startMonth."-".$evalForm->setting->startDate." 00:00:00"); // " "2010-07-05T06:00:00Z"); date_create(date("Y").",timezone_open("Europe/Oslo"));
              $currentPeriod->setTimeZone(new \DateTimeZone("Asia/Manila"));
              $currentPeriod->setTime(0,0,0);

              $existing = EvalForm::where('user_id', $employee->id)->where('startPeriod',$currentPeriod)->get();
              $doneEval = new Collection;
              if ($existing->isEmpty()) $doneEval[$employee->id] = ['evaluated'=>0, 'evalForm_id'=> null];
                  else $doneEval[$employee->id] = ['evaluated'=>1, 'evalForm_id'=> $existing->first()->id];

              

              $competencyAttributes = $evalForm->setting->competencyAttributes;
              $competencies = $competencyAttributes->groupBy('competency_id');
              $formEntries = new Collection;
              $maxScore = 0;

              $formEntries = new Collection;

              if ($details->isEmpty()){
                  
                  return $this->newEvaluation($employee->id,$evalType->id);

              } else
              {
                  foreach ($details as $detail) {
                      $comp = Competency__Attribute::find($detail->competency__Attribute_id)->competency;
                      $attr = Competency__Attribute::find($detail->competency__Attribute_id)->attribute;

                      $rating = RatingScale::find($detail->ratingScale_id); //->ratings; //->label;'ratings'=> $rating['label']


                       // --------------- generate form elements based on leader/agent competencies

                      if( $comp['acrossTheBoard'] ){

                        if ( $leadershipcheck->isEmpty()  ){ //agent

                       $formEntries->push(['competency'=> $comp['name'], 'definitions'=>$comp['definitions'],
                          'percentage'=>$comp['agentPercentage'], 
                          'attribute'=>$attr['name'],
                          'value'=> $detail->attributeValue,
                          'rating'=> $rating ]);
                         

                        } else {
                          $formEntries->push(['competency'=> $comp['name'], 'definitions'=>$comp['definitions'],
                          'percentage'=>$comp['percentage'], 
                          'attribute'=>$attr['name'],
                          'value'=> $detail->attributeValue,
                          'rating'=> $rating ]);

                        }

                      } else { //else not acrossTheBoard

                        if ( empty($comp['percentage']) && $leadershipcheck->isEmpty()){ //agent sya
                         $formEntries->push(['competency'=> $comp['name'], 'definitions'=>$comp['definitions'],
                          'percentage'=>$comp['agentPercentage'], 
                          'attribute'=>$attr['name'],
                          'value'=> $detail->attributeValue,
                          'rating'=> $rating ]);

                        } else if (!empty($comp['percentage']) && !$leadershipcheck->isEmpty()){ //leader sya
                          $formEntries->push(['competency'=> $comp['name'], 'definitions'=>$comp['definitions'],
                          'percentage'=>$comp['percentage'], 
                          'attribute'=>$attr['name'],
                          'value'=> $detail->attributeValue,
                          'rating'=> $rating ]);

                        }

                      }


                      // --------------- end generate form elements





                      
                  }

                  //get all Performance Summary values

                  $allSummaries = Summary::all();
                  $summaries = new Collection;

                  foreach ($allSummaries as $key ) {
                     if (!($key->columns->isEmpty()) ) 
                      {
                          $cols = $key->columns;

                          // foreach ($cols as $c) {
                          //     $colVal =  PerformanceSummary::where('summary_id',$key->id)->where('evalForm_id',$evalForm->id)->first()->value;
                          //     $colValues[] = 
                          // }
                      } else $cols=null;
                     if (!($key->rows->isEmpty()) )
                     { 
                          $rows = $key->rows;

                     }  else $rows = null;

                     $summaryValue = PerformanceSummary::where('summary_id',$key->id)->where('evalForm_id',$evalForm->id)->first()->value;
                     $summaries->push(['summaryID'=>$key->id,'summaryValue'=>$summaryValue,  'header'=>$key->heading,'details'=>$key->description, 'columns'=>$cols, 'rows'=>$rows]);
                  }

                  $perfSum = PerformanceSummary::where('evalForm_id', $evalForm->id)->get();

                  if ($perfSum->isEmpty()){
                      $performanceSummary = null;

                  } else {
                      $performanceSummary = new Collection;
                      $idx = 0;
                      foreach ($perfSum as $ps) {
                          $performanceSummary[$idx] = $ps->value;
                          $idx++;
                      }

                  }
                  

                   $startPeriod = Carbon::createFromFormat('Y-m-d H:i:s',$evalForm->startPeriod);
                   $endPeriod = Carbon::createFromFormat('Y-m-d H:i:s',$evalForm->endPeriod);
                   //$evaluator = ImmediateHead::find($evalForm->evaluatedBy);
                   $evaluator = ImmediateHead::find(ImmediateHead_Campaign::find($evalForm->evaluatedBy)->immediateHead_id);


                   
                    /* -------  for maxscore -----*/

                   foreach ($competencies as $key ) {
                       
                        $comp = Competency::find($key[0]->competency_id);


                        if( $comp['acrossTheBoard'] )
                        {

                              if ( $leadershipcheck->isEmpty()  ){ //agent

                                 $maxScore += $comp->agentPercentage*5/100;
                               

                              } else {
                                $maxScore += $comp->percentage*5/100;

                              }

                       } else 
                            { //else not acrossTheBoard

                                if ( empty($comp['percentage']) && $leadershipcheck->isEmpty()){ //agent sya
                                    $maxScore += $comp->agentPercentage*5/100;

                                } else if (!empty($comp['percentage']) && !$leadershipcheck->isEmpty()){ //leader sya
                                  $maxScore += $comp->percentage*5/100;

                                }

                        }

                      
                        
                       
                    }


                   

                 /* DMPDF */
                    //$pdf = PDF::loadView('evaluation.pdf', compact('allowed', 'doneEval', 'evaluator', 'startPeriod', 'endPeriod', 'performanceSummary', 'evalType', 'employee', 'ratingScale', 'evalForm','evalSetting', 'formEntries','maxScore','summaries'));
                    $pdf = PDF::loadView('evaluation.pdf-blank', compact('allowed', 'doneEval', 'evaluator', 'startPeriod', 'endPeriod', 'performanceSummary', 'evalType', 'employee', 'ratingScale', 'evalForm','evalSetting', 'formEntries','maxScore','summaries'));
                    

                   return $pdf->stream('eval_'.$employee->lastname."_".$employee->firstname.'.pdf');
                   
              
              } //end else not empty
         } else { return view('access-denied'); }//end if allowed

       }//end else not empty form
      


    }

     public function printBlankEmployee($id)
    {
      //$evalForm = EvalForm::find($id);

      $employee = User::find($id);
      $leadershipcheck = ImmediateHead::where('employeeNumber', $employee->employeeNumber)->get();

      // check if leader or agent
      if ( count($leadershipcheck) != 0 ) //use leadership form
      {
        $evalForm = EvalForm::find(196);

      } else {
        $evalForm = EvalForm::find(71);

      }

      if ( empty($evalForm) ) {
        return view('empty');

      } else
      {

        //$employee = User::find($evalForm->user_id);

        
        $allowed = false;

        //if (ImmediateHead::find($employee->immediateHead_Campaigns_id)->employeeNumber == $this->user->employeeNumber ){ $allowed = true; }
        $allowed=true;

        if( $this->user->userType_id == 1 ||  $allowed || $this->user->id == $employee->id )
        {

              $details = $evalForm->details;

              $evalType = EvalType::find($evalForm->setting->evalType_id);
              $evalSetting = EvalSetting::find($evalForm->evalSetting_id);

              
              $ratingScale = RatingScale::all();
              //setup now the evaluation form
              // !! but check first if it's existing already, if not -- create new one

              $currentPeriod = new \DateTime(date("Y")."-".$evalForm->setting->startMonth."-".$evalForm->setting->startDate." 00:00:00"); // " "2010-07-05T06:00:00Z"); date_create(date("Y").",timezone_open("Europe/Oslo"));
              $currentPeriod->setTimeZone(new \DateTimeZone("Asia/Manila"));
              $currentPeriod->setTime(0,0,0);

              $existing = EvalForm::where('user_id', $employee->id)->where('startPeriod',$currentPeriod)->get();
              $doneEval = new Collection;
              if ($existing->isEmpty()) $doneEval[$employee->id] = ['evaluated'=>0, 'evalForm_id'=> null];
                  else $doneEval[$employee->id] = ['evaluated'=>1, 'evalForm_id'=> $existing->first()->id];

              

              $competencyAttributes = $evalForm->setting->competencyAttributes;
              $competencies = $competencyAttributes->groupBy('competency_id');
              $formEntries = new Collection;
              $maxScore = 0;

              $formEntries = new Collection;

              if ($details->isEmpty()){
                  
                  return $this->newEvaluation($employee->id,$evalType->id);

              } else
              {
                  foreach ($details as $detail) {
                      $comp = Competency__Attribute::find($detail->competency__Attribute_id)->competency;
                      $attr = Competency__Attribute::find($detail->competency__Attribute_id)->attribute;

                      $rating = RatingScale::find($detail->ratingScale_id); //->ratings; //->label;'ratings'=> $rating['label']


                       // --------------- generate form elements based on leader/agent competencies

                      if( $comp['acrossTheBoard'] ){

                        if ( $leadershipcheck->isEmpty()  ){ //agent

                       $formEntries->push(['competency'=> $comp['name'], 'definitions'=>$comp['definitions'],
                          'percentage'=>$comp['agentPercentage'], 
                          'attribute'=>$attr['name'],
                          'value'=> $detail->attributeValue,
                          'rating'=> $rating ]);
                         

                        } else {
                          $formEntries->push(['competency'=> $comp['name'], 'definitions'=>$comp['definitions'],
                          'percentage'=>$comp['percentage'], 
                          'attribute'=>$attr['name'],
                          'value'=> $detail->attributeValue,
                          'rating'=> $rating ]);

                        }

                      } else { //else not acrossTheBoard

                        if ( empty($comp['percentage']) && $leadershipcheck->isEmpty()){ //agent sya
                         $formEntries->push(['competency'=> $comp['name'], 'definitions'=>$comp['definitions'],
                          'percentage'=>$comp['agentPercentage'], 
                          'attribute'=>$attr['name'],
                          'value'=> $detail->attributeValue,
                          'rating'=> $rating ]);

                        } else if (!empty($comp['percentage']) && !$leadershipcheck->isEmpty()){ //leader sya
                          $formEntries->push(['competency'=> $comp['name'], 'definitions'=>$comp['definitions'],
                          'percentage'=>$comp['percentage'], 
                          'attribute'=>$attr['name'],
                          'value'=> $detail->attributeValue,
                          'rating'=> $rating ]);

                        }

                      }


                      // --------------- end generate form elements





                      
                  }

                  //get all Performance Summary values

                  $allSummaries = Summary::all();
                  $summaries = new Collection;

                  foreach ($allSummaries as $key ) {
                     if (!($key->columns->isEmpty()) ) 
                      {
                          $cols = $key->columns;

                          // foreach ($cols as $c) {
                          //     $colVal =  PerformanceSummary::where('summary_id',$key->id)->where('evalForm_id',$evalForm->id)->first()->value;
                          //     $colValues[] = 
                          // }
                      } else $cols=null;
                     if (!($key->rows->isEmpty()) )
                     { 
                          $rows = $key->rows;

                     }  else $rows = null;

                     $summaryValue = PerformanceSummary::where('summary_id',$key->id)->where('evalForm_id',$evalForm->id)->first()->value;
                     $summaries->push(['summaryID'=>$key->id,'summaryValue'=>$summaryValue,  'header'=>$key->heading,'details'=>$key->description, 'columns'=>$cols, 'rows'=>$rows]);
                  }

                  $perfSum = PerformanceSummary::where('evalForm_id', $evalForm->id)->get();

                  if ($perfSum->isEmpty()){
                      $performanceSummary = null;

                  } else {
                      $performanceSummary = new Collection;
                      $idx = 0;
                      foreach ($perfSum as $ps) {
                          $performanceSummary[$idx] = $ps->value;
                          $idx++;
                      }

                  }
                  

                   $startPeriod = Carbon::createFromFormat('Y-m-d H:i:s',$evalForm->startPeriod);
                   $endPeriod = Carbon::createFromFormat('Y-m-d H:i:s',$evalForm->endPeriod);
                   $evaluator = ImmediateHead::find(ImmediateHead_Campaign::find($employee->supervisor->immediateHead_Campaigns_id)->immediateHead_id); //ImmediateHead::find($evalForm->evaluatedBy);
                   


                   
                    /* -------  for maxscore -----*/

                   foreach ($competencies as $key ) {
                       
                        $comp = Competency::find($key[0]->competency_id);


                        if( $comp['acrossTheBoard'] )
                        {

                              if ( $leadershipcheck->isEmpty()  ){ //agent

                                 $maxScore += $comp->agentPercentage*5/100;
                               

                              } else {
                                $maxScore += $comp->percentage*5/100;

                              }

                       } else 
                            { //else not acrossTheBoard

                                if ( empty($comp['percentage']) && $leadershipcheck->isEmpty()){ //agent sya
                                    $maxScore += $comp->agentPercentage*5/100;

                                } else if (!empty($comp['percentage']) && !$leadershipcheck->isEmpty()){ //leader sya
                                  $maxScore += $comp->percentage*5/100;

                                }

                        }

                      
                        
                       
                    }


                   

                 /* DMPDF */
                    //$pdf = PDF::loadView('evaluation.pdf', compact('allowed', 'doneEval', 'evaluator', 'startPeriod', 'endPeriod', 'performanceSummary', 'evalType', 'employee', 'ratingScale', 'evalForm','evalSetting', 'formEntries','maxScore','summaries'));
                    $pdf = PDF::loadView('evaluation.pdf-blankEmployee', compact('allowed', 'doneEval', 'evaluator', 'startPeriod', 'endPeriod', 'performanceSummary', 'evalType', 'employee', 'ratingScale', 'evalForm','evalSetting', 'formEntries','maxScore','summaries'));
                    

                   return $pdf->stream('eval_'.$employee->lastname."_".$employee->firstname.'.pdf');
                   
                   // $tempdir = sys_get_temp_dir();
                   //  $pdf = App::make('dompdf.wrapper');
                   //  //$pdf->setOptions(['defaultFont' => 'sans-serif', 'defaultPaperSize': "a4" ]);
                   //  $pdf->loadHTML('');
                   //  return $pdf->stream();

                    //                  $pdf = App::make('dompdf.wrapper');
                    // $pdf->loadHTML('<h1>Test</h1>');
                    // return $pdf->stream();

                                        /* ------- END DMPDF ---------*/


                                        /*-------- SNAPPY -----------*/
                    //                    $pdf = App::make('snappy.pdf.wrapper');
                    // $pdf->loadHTML('<h1>Test</h1>');
                    // return $pdf->inline();
                    /*-------- END SNAPPY ----------*/



                  //return view('evaluation.print', compact('allowed', 'doneEval', 'evaluator', 'startPeriod', 'endPeriod', 'performanceSummary', 'evalType', 'employee', 'ratingScale', 'evalForm','evalSetting', 'formEntries','maxScore','summaries'));
              
              } //end else not empty
         } else { return view('access-denied'); }//end if allowed

       }//end else not empty form
      


    }

    public function printEval($id)
    {
      $evalForm = EvalForm::find($id);

      if ( empty($evalForm) ) {
        return view('empty');

      } 
      else 
      {

        $employee = User::find($evalForm->user_id);
        $leadershipcheck = ImmediateHead::where('employeeNumber', $employee->employeeNumber)->get();
        $allowed = false;

       

        //if (ImmediateHead::find(ImmediateHead_Campaign::find($employee->immediateHead_Campaigns_id)->immediateHead_id)->employeeNumber == $this->user->employeeNumber ){ $allowed = true; }
        $canDoThis = UserType::find($this->user->userType_id)->roles->where('label','VIEW_ALL_EVALS');

        if (!empty($canDoThis)) $allowed=true;

        if( $this->user->userType_id == 1 || $allowed  || $this->user->id == $employee->id )
        {

              $details = $evalForm->details;

              $evalType = EvalType::find($evalForm->setting->evalType_id);
              $evalSetting = EvalSetting::find($evalForm->evalSetting_id);

              
              $ratingScale = RatingScale::all();
              //setup now the evaluation form
              // !! but check first if it's existing already, if not -- create new one

              $currentPeriod = new \DateTime(date("Y")."-".$evalForm->setting->startMonth."-".$evalForm->setting->startDate." 00:00:00"); // " "2010-07-05T06:00:00Z"); date_create(date("Y").",timezone_open("Europe/Oslo"));
              $currentPeriod->setTimeZone(new \DateTimeZone("Asia/Manila"));
              $currentPeriod->setTime(0,0,0);

              $existing = EvalForm::where('user_id', $employee->id)->where('startPeriod',$currentPeriod)->get();
              $doneEval = new Collection;
              if ($existing->isEmpty()) $doneEval[$employee->id] = ['evaluated'=>0, 'evalForm_id'=> null];
                  else $doneEval[$employee->id] = ['evaluated'=>1, 'evalForm_id'=> $existing->first()->id];

              

              $competencyAttributes = $evalForm->setting->competencyAttributes;
              $competencies = $competencyAttributes->groupBy('competency_id');
              $formEntries = new Collection;
              $maxScore = 0;

              $formEntries = new Collection;

              if ($details->isEmpty()){
                  
                  return $this->newEvaluation($employee->id,$evalType->id);

              } else
              {
                  foreach ($details as $detail) {
                      $comp = Competency__Attribute::find($detail->competency__Attribute_id)->competency;
                      $attr = Competency__Attribute::find($detail->competency__Attribute_id)->attribute;

                      $rating = RatingScale::find($detail->ratingScale_id); //->ratings; //->label;'ratings'=> $rating['label']


                       // --------------- generate form elements based on leader/agent competencies

                      if( $comp['acrossTheBoard'] ){

                        if ( $leadershipcheck->isEmpty() || $employee->userType_id==4  ){ //agent

                       $formEntries->push(['competency'=> $comp['name'], 'definitions'=>$comp['definitions'],
                          'percentage'=>$comp['agentPercentage'], 
                          'attribute'=>$attr['name'],
                          'value'=> $detail->attributeValue,
                          'rating'=> $rating ]);
                         

                        } else {
                          $formEntries->push(['competency'=> $comp['name'], 'definitions'=>$comp['definitions'],
                          'percentage'=>$comp['percentage'], 
                          'attribute'=>$attr['name'],
                          'value'=> $detail->attributeValue,
                          'rating'=> $rating ]);

                        }

                      } else { //else not acrossTheBoard

                        if ( empty($comp['percentage']) && ($leadershipcheck->isEmpty() || $employee->userType_id==4 ) ){ //agent sya
                         $formEntries->push(['competency'=> $comp['name'], 'definitions'=>$comp['definitions'],
                          'percentage'=>$comp['agentPercentage'], 
                          'attribute'=>$attr['name'],
                          'value'=> $detail->attributeValue,
                          'rating'=> $rating ]);

                        } else if (!empty($comp['percentage']) && (!$leadershipcheck->isEmpty() && $employee->userType_id!=4 ) ){ //leader sya
                          $formEntries->push(['competency'=> $comp['name'], 'definitions'=>$comp['definitions'],
                          'percentage'=>$comp['percentage'], 
                          'attribute'=>$attr['name'],
                          'value'=> $detail->attributeValue,
                          'rating'=> $rating ]);

                        }

                      }


                      // --------------- end generate form elements





                      
                  }

                  //get all Performance Summary values

                  $allSummaries = Summary::all();
                  $summaries = new Collection;

                  foreach ($allSummaries as $key ) {
                     if (!($key->columns->isEmpty()) ) 
                      {
                          $cols = $key->columns;

                          // foreach ($cols as $c) {
                          //     $colVal =  PerformanceSummary::where('summary_id',$key->id)->where('evalForm_id',$evalForm->id)->first()->value;
                          //     $colValues[] = 
                          // }
                      } else $cols=null;
                     if (!($key->rows->isEmpty()) )
                     { 
                          $rows = $key->rows;

                     }  else $rows = null;

                     $summaryValue = PerformanceSummary::where('summary_id',$key->id)->where('evalForm_id',$evalForm->id)->first()->value;
                     $summaries->push(['summaryID'=>$key->id,'summaryValue'=>$summaryValue,  'header'=>$key->heading,'details'=>$key->description, 'columns'=>$cols, 'rows'=>$rows]);
                  }

                  $perfSum = PerformanceSummary::where('evalForm_id', $evalForm->id)->get();

                  if ($perfSum->isEmpty()){
                      $performanceSummary = null;

                  } else {
                      $performanceSummary = new Collection;
                      $idx = 0;
                      foreach ($perfSum as $ps) {
                          $performanceSummary[$idx] = $ps->value;
                          $idx++;
                      }

                  }
                  

                   $startPeriod = Carbon::createFromFormat('Y-m-d H:i:s',$evalForm->startPeriod);
                   $endPeriod = Carbon::createFromFormat('Y-m-d H:i:s',$evalForm->endPeriod);
                   //$evaluator = ImmediateHead::find($evalForm->evaluatedBy);
                   $evaluator = ImmediateHead::find(ImmediateHead_Campaign::find($evalForm->evaluatedBy)->immediateHead_id);
                   $evaluatorData = User::where('employeeNumber', $evaluator->employeeNumber)->first();


                   /* -------  for maxscore -----*/

                   foreach ($competencies as $key ) {
                       
                        $comp = Competency::find($key[0]->competency_id);


                        if( $comp['acrossTheBoard'] )
                        {

                              if ( $leadershipcheck->isEmpty() || $employee->userType_id==4  ){ //agent

                                 $maxScore += $comp->agentPercentage*5/100;
                               

                              } else {
                                $maxScore += $comp->percentage*5/100;

                              }

                       } else 
                            { //else not acrossTheBoard

                                if ( empty($comp['percentage']) && ($leadershipcheck->isEmpty() || $employee->userType_id==4) ){ //agent sya
                                    $maxScore += $comp->agentPercentage*5/100;

                                } else if (!empty($comp['percentage']) && (!$leadershipcheck->isEmpty() && $employee->userType_id!=4 ) ){ //leader sya
                                  $maxScore += $comp->percentage*5/100;

                                }

                        }

                      
                        
                       
                    }


                    $pdf = PDF::loadView('evaluation.pdf', compact('allowed', 'doneEval', 'evaluator', 'evaluatorData', 'startPeriod', 'endPeriod', 'performanceSummary', 'evalType', 'employee', 'ratingScale', 'evalForm','evalSetting', 'formEntries','maxScore','summaries'));
                    

                   return $pdf->stream('eval_'.$employee->lastname."_".$employee->firstname.'.pdf');
                   
                 
              
              } //end else not empty
         } else {
            return view('access-denied');

         }//end if allowed

      }//end else not empty form
      


    }
  
   

    public function review($id)
    {
        $evalForm = EvalForm::find($id);

        if (count($evalForm)==0){
              return view('empty');
        } else { //check first if the one reviewing is the owner

            $employee = User::find($evalForm->user_id);
            $allowed = false;

          if ( $this->user->id !== $employee->id) return view('access-denied');

        }

        // before doing anything, check mo kung reviewed na
        // if yes, no need to review again, return mo lang ulit

        if ($evalForm->coachingDone) return redirect()->action('EvalFormController@show',$id);
        else 
        {

            // we need to check first the permissions who can view this particular eval
            // if SUPER ADMIN or your subordinate, then yes. Otherwise..cannot

            
            

            $leadershipcheck = ImmediateHead::where('employeeNumber', $employee->employeeNumber)->get();

            //if (ImmediateHead::find($employee->immediateHead_Campaigns_id)->employeeNumber == $this->user->employeeNumber ){ $allowed = true; }
            if ($evalForm->user_id == $this->user->id) $allowed=true;

            if( $this->user->userType_id == 1 ||  $allowed  ){ //|| $this->user->id == $employee->id

                  $details = $evalForm->details;

                  $evalType = EvalType::find($evalForm->setting->evalType_id);
                  $evalSetting = EvalSetting::find($evalForm->evalSetting_id);
                  

                  
                  $ratingScale = RatingScale::all();



                  //$evalSetting = EvalSetting::where('evalType_id',$evalType_id)->first();

                  //setup now the evaluation form
                  // !! but check first if it's existing already, if not -- create new one

                  $currentPeriod = new \DateTime(date("Y")."-".$evalForm->setting->startMonth."-".$evalForm->setting->startDate." 00:00:00"); // " "2010-07-05T06:00:00Z"); date_create(date("Y").",timezone_open("Europe/Oslo"));
                  $currentPeriod->setTimeZone(new \DateTimeZone("Asia/Manila"));
                  $currentPeriod->setTime(0,0,0);

                  $existing = EvalForm::where('user_id', $employee->id)->where('startPeriod',$currentPeriod)->get();
                  $doneEval = new Collection;
                  if ($existing->isEmpty()) $doneEval[$employee->id] = ['evaluated'=>0, 'evalForm_id'=> null];
                      else $doneEval[$employee->id] = ['evaluated'=>1, 'evalForm_id'=> $existing->first()->id];

                  

                  $competencyAttributes = $evalForm->setting->competencyAttributes;
                  $competencies = $competencyAttributes->groupBy('competency_id');
                  $formEntries = new Collection;
                  $maxScore = 0;

                  $formEntries = new Collection;

                  if ($details->isEmpty()){
                      
                      return $this->newEvaluation($employee->id,$evalType->id);

                  } else
                  {
                      foreach ($details as $detail) {
                          $comp = Competency__Attribute::find($detail->competency__Attribute_id)->competency;
                          $attr = Competency__Attribute::find($detail->competency__Attribute_id)->attribute;

                          $rating = RatingScale::find($detail->ratingScale_id); //->ratings; //->label;'ratings'=> $rating['label']
                          //var_dump($comp);

                          //if (empty($comp['agentPercentage'])){

                          // --------------- generate form elements based on leader/agent competencies

                          if( $comp['acrossTheBoard'] ){

                            if ( $leadershipcheck->isEmpty()  ){ //agent

                              $formEntries->push(['competency'=> $comp['name'], 'definitions'=>$comp['definitions'],
                              'percentage'=>$comp['agentPercentage'], 
                              'attribute'=>$attr['name'],
                              'id'=>$comp['id'],
                              'value'=> $detail->attributeValue,
                              'detailID'=> $detail->id,
                              'rating'=> $rating ]);
                             

                            } else {
                              $formEntries->push(['competency'=> $comp['name'], 'definitions'=>$comp['definitions'],
                              'percentage'=>$comp['percentage'], 
                              'attribute'=>$attr['name'],
                              'id'=>$comp['id'],
                              'value'=> $detail->attributeValue,
                              'detailID'=> $detail->id,
                              'rating'=> $rating ]);

                            }

                          } else { //else not acrossTheBoard

                            if ( empty($comp['percentage']) && $leadershipcheck->isEmpty()){ //agent sya
                              $formEntries->push(['competency'=> $comp['name'], 'definitions'=>$comp['definitions'],
                              'percentage'=>$comp['agentPercentage'], 
                              'attribute'=>$attr['name'],
                              'id'=>$comp['id'],
                              'value'=> $detail->attributeValue,
                              'detailID'=> $detail->id,
                              'rating'=> $rating ]);

                            } else if (!empty($comp['percentage']) && !$leadershipcheck->isEmpty()){ //leader sya
                              $formEntries->push(['competency'=> $comp['name'], 'definitions'=>$comp['definitions'],
                              'percentage'=>$comp['percentage'], 
                              'attribute'=>$attr['name'],
                              'id'=>$comp['id'],
                              'value'=> $detail->attributeValue,
                              'detailID'=> $detail->id,
                              'rating'=> $rating ]);

                            }

                          }


                          // --------------- end generate form elements
                          
                      }
                     // return $details; //$formEntries->groupBy('competency');



                      //get all Performance Summary values

                      $allSummaries = Summary::all();
                      $summaries = new Collection;

                      foreach ($allSummaries as $key ) {
                         if (!($key->columns->isEmpty()) ) 
                          {
                              $cols = $key->columns;

                              // foreach ($cols as $c) {
                              //     $colVal =  PerformanceSummary::where('summary_id',$key->id)->where('evalForm_id',$evalForm->id)->first()->value;
                              //     $colValues[] = 
                              // }
                          } else $cols=null;
                         if (!($key->rows->isEmpty()) )
                         { 
                              $rows = $key->rows;

                         }  else $rows = null;

                         $summaryValue = PerformanceSummary::where('summary_id',$key->id)->where('evalForm_id',$evalForm->id)->first()->value;
                         $summaries->push(['summaryID'=>$key->id,'summaryValue'=>$summaryValue,  'header'=>$key->heading,'details'=>$key->description, 'columns'=>$cols, 'rows'=>$rows]);
                      }

                      $perfSum = PerformanceSummary::where('evalForm_id', $evalForm->id)->get();

                      if ($perfSum->isEmpty()){
                          $performanceSummary = null;

                      } else {
                          $performanceSummary = new Collection;
                          $idx = 0;
                          foreach ($perfSum as $ps) {
                              $performanceSummary[$idx] = $ps->value;
                              $idx++;
                          }

                      }
                      

                       $startPeriod = Carbon::createFromFormat('Y-m-d H:i:s',$evalForm->startPeriod);
                       $endPeriod = Carbon::createFromFormat('Y-m-d H:i:s',$evalForm->endPeriod);
                       //$evaluator = ImmediateHead::find($evalForm->evaluatedBy);
                       $evaluator = ImmediateHead::find(ImmediateHead_Campaign::find($evalForm->evaluatedBy)->immediateHead_id);


                       /* -------  for maxscore -----*/

                       foreach ($competencies as $key ) {
                           
                            $comp = Competency::find($key[0]->competency_id);


                            if( $comp['acrossTheBoard'] )
                            {

                                  if ( $leadershipcheck->isEmpty()  ){ //agent

                                     $maxScore += $comp->agentPercentage*5/100;
                                   

                                  } else {
                                    $maxScore += $comp->percentage*5/100;

                                  }

                           } else 
                                { //else not acrossTheBoard

                                    if ( empty($comp['percentage']) && $leadershipcheck->isEmpty()){ //agent sya
                                        $maxScore += $comp->agentPercentage*5/100;

                                    } else if (!empty($comp['percentage']) && !$leadershipcheck->isEmpty()){ //leader sya
                                      $maxScore += $comp->percentage*5/100;

                                    }

                            }

                          
                            
                           
                        }






                       /* ------- end for maxscore ---- */

                      return view('evaluation.review-employee', compact('allowed', 'doneEval', 'evaluator', 'startPeriod', 'endPeriod', 'performanceSummary', 'evalType', 'employee', 'ratingScale', 'evalForm','evalSetting', 'formEntries','maxScore','summaries'));
                  }        

            } else {

              return view('access-denied');

            }


        }// end else !coachingDone
        


        
        
        
        
    }//end review()


    public function show($id)
    {
        $evalForm = EvalForm::find($id);

        if (count($evalForm)==0){
          return view('empty');
        }

        $coll = new Collection;

        $startPeriod = Carbon::createFromFormat('Y-m-d H:i:s',$evalForm->startPeriod);
        $endPeriod = Carbon::createFromFormat('Y-m-d H:i:s',$evalForm->endPeriod);
        $evaluator = ImmediateHead::find(ImmediateHead_Campaign::find($evalForm->evaluatedBy)->immediateHead_id);
       
        // we need to check first the permissions who can view this particular eval
        // if SUPER ADMIN or your subordinate, then yes. Otherwise..cannot

        $canDoThis = UserType::find($this->user->userType_id)->roles->where('label','VIEW_ALL_EVALS');


        
        $employee = User::find($evalForm->user_id);
        $allowed = false;

        //$leadershipcheck = ImmediateHead::where('employeeNumber', $employee->employeeNumber)->get();
        //** but we need to check if employee has previous POSITION MOVEMENT

        $checkMovements = Movement::where('user_id',$employee->id)->where('personnelChange_id','2')->where('isDone','1')->where('effectivity','>=',$startPeriod->toDateString())->where('effectivity','<=',$endPeriod->toDateString())->orderBy('effectivity','DESC')->get(); 

                if (count($checkMovements) > 0)
                {
                    //$coll2->push($checkMovements->first());

                    //determine if his old position is as a LEADER or not
                    $movedPos = $checkMovements->first();
                    $oldPos = Position::find(Movement_Positions::where('movement_id',$movedPos->id)->first()->position_id_old); 

                    if (empty($oldPos->leadershipRole)){
                        $isLead=false; 
                        $showPosition = "(Former ". $oldPos->name. ")";
                    } else $isLead = $oldPos->leadershipRole;

                }else {
                    //verify nga kung leader ba talaga or hindi
                    $leadershipcheck = ImmediateHead::where('employeeNumber', $employee->employeeNumber)->get();
                    (count($leadershipcheck) > 0) ? $isLead=true : $isLead=false;
                    $showPosition = $employee->position->name;
                }

        /* --------- END POSITION MOVEMENT -----------*/

        // check if ALLOWED TO EDIT
        //if (ImmediateHead::find($employee->immediateHead_Campaigns_id)->employeeNumber == $this->user->employeeNumber ){ $allowed = true; }

        if ( (ImmediateHead::find(ImmediateHead_Campaign::find($evalForm->evaluatedBy)->immediateHead_id)->employeeNumber == $this->user->employeeNumber  )) $allowed = true;

        if( $this->user->userType_id == 1 || $this->user->userType_id == 5 ||  $allowed || $this->user->id == $employee->id ){

              $details = $evalForm->details;



              $evalType = EvalType::find($evalForm->setting->evalType_id);
              $evalSetting = EvalSetting::find($evalForm->evalSetting_id);
              

              
              $ratingScale = RatingScale::all();



              //$evalSetting = EvalSetting::where('evalType_id',$evalType_id)->first();

              //setup now the evaluation form
              // !! but check first if it's existing already, if not -- create new one

              $currentPeriod = new \DateTime(date("Y")."-".$evalForm->setting->startMonth."-".$evalForm->setting->startDate." 00:00:00"); // " "2010-07-05T06:00:00Z"); date_create(date("Y").",timezone_open("Europe/Oslo"));
              $currentPeriod->setTimeZone(new \DateTimeZone("Asia/Manila"));
              $currentPeriod->setTime(0,0,0);

              $existing = EvalForm::where('user_id', $employee->id)->where('startPeriod',$currentPeriod)->get();
              $doneEval = new Collection;
              if ($existing->isEmpty()) $doneEval[$employee->id] = ['evaluated'=>0, 'evalForm_id'=> null];
                  else $doneEval[$employee->id] = ['evaluated'=>1, 'evalForm_id'=> $existing->first()->id];

              

              $competencyAttributes = $evalForm->setting->competencyAttributes;
              $competencies = $competencyAttributes->groupBy('competency_id');
              $formEntries = new Collection;
              $maxScore = 0;

              $formEntries = new Collection;

              if ($details->isEmpty()){
                  
                  return $this->newEvaluation($employee->id,$evalType->id);

              } else
              {

                  foreach ($details as $detail) {
                      $comp = Competency__Attribute::find($detail->competency__Attribute_id)->competency;
                      $attr = Competency__Attribute::find($detail->competency__Attribute_id)->attribute;

                      $rating = RatingScale::find($detail->ratingScale_id); //->ratings; //->label;'ratings'=> $rating['label']
                      //var_dump($comp);

                      //if (empty($comp['agentPercentage'])){

                      // --------------- generate form elements based on leader/agent competencies

                      $coll->push(['comp'=>$comp, 'attr'=>$attr, 'rating'=>$rating]);

                      if( $comp['acrossTheBoard'] ){

                        if ( !$isLead || $employee->userType_id==4 || $employee->leadOverride  ){ //agent

                          $formEntries->push(['competency'=> $comp['name'], 'definitions'=>$comp['definitions'],
                          'percentage'=>$comp['agentPercentage'], 
                          'attribute'=>$attr['name'],
                          'value'=> $detail->attributeValue,
                          'rating'=> $rating,
                          'detailID'=>$detail->id ]);
                         

                        } else {
                          $formEntries->push(['competency'=> $comp['name'], 'definitions'=>$comp['definitions'],
                          'percentage'=>$comp['percentage'], 
                          'attribute'=>$attr['name'],
                          'value'=> $detail->attributeValue,
                          'rating'=> $rating,
                          'detailID'=>$detail->id  ]);

                        }

                      } else { //else not acrossTheBoard

                        if ( empty($comp['percentage']) && (!$isLead || $employee->userType_id==4 || $employee->leadOverride ) ){ //agent sya
                          $formEntries->push(['competency'=> $comp['name'], 'definitions'=>$comp['definitions'],
                          'percentage'=>$comp['agentPercentage'], 
                          'attribute'=>$attr['name'],
                          'value'=> $detail->attributeValue,
                          'rating'=> $rating,
                          'detailID'=>$detail->id  ]);

                        } else if (!empty($comp['percentage']) && ($isLead && $employee->userType_id!=4 ) && empty($employee->leadOverride) ){ //leader sya
                          $formEntries->push(['competency'=> $comp['name'], 'definitions'=>$comp['definitions'],
                          'percentage'=>$comp['percentage'], 
                          'attribute'=>$attr['name'],
                          'value'=> $detail->attributeValue,
                          'rating'=> $rating,
                          'detailID'=>$detail->id  ]);

                        }

                      }


                      // --------------- end generate form elements
                      
                  }
              

                  //get all Performance Summary values

                  $allSummaries = Summary::all();
                  $summaries = new Collection;

                  foreach ($allSummaries as $key ) {
                     if (!($key->columns->isEmpty()) ) 
                      {
                          $cols = $key->columns;

                          // foreach ($cols as $c) {
                          //     $colVal =  PerformanceSummary::where('summary_id',$key->id)->where('evalForm_id',$evalForm->id)->first()->value;
                          //     $colValues[] = 
                          // }
                      } else $cols=null;
                     if (!($key->rows->isEmpty()) )
                     { 
                          $rows = $key->rows;

                     }  else $rows = null;

                     $summaryValue = PerformanceSummary::where('summary_id',$key->id)->where('evalForm_id',$evalForm->id)->first()->value;
                     $summaries->push(['summaryID'=>$key->id,'summaryValue'=>$summaryValue,  'header'=>$key->heading,'details'=>$key->description, 'columns'=>$cols, 'rows'=>$rows]);
                  }

                  $perfSum = PerformanceSummary::where('evalForm_id', $evalForm->id)->get();

                  if ($perfSum->isEmpty()){
                      $performanceSummary = null;

                  } else {
                      $performanceSummary = new Collection;
                      $idx = 0;
                      foreach ($perfSum as $ps) {
                          $performanceSummary[$idx] = $ps->value;
                          $idx++;
                      }

                  }
                  

                  


                   /* -------  for maxscore -----*/

                   foreach ($competencies as $key ) {
                       
                        $comp = Competency::find($key[0]->competency_id);


                        if( $comp['acrossTheBoard'] )
                        {

                              if ( !$isLead || $employee->userType_id==4  || $employee->leadOverride ){ //agent

                                 $maxScore += $comp->agentPercentage*5/100;
                               

                              } else {
                                $maxScore += $comp->percentage*5/100;

                              }

                       } else 
                            { //else not acrossTheBoard

                                if ( empty($comp['percentage']) && (!$isLead || $employee->userType_id==4 || $employee->leadOverride ) ){ //agent sya
                                    $maxScore += $comp->agentPercentage*5/100;

                                } else if (!empty($comp['percentage']) && ($isLead && $employee->userType_id!=4 && empty($employee->leadOverride) )){ //leader sya
                                  $maxScore += $comp->percentage*5/100;

                                }

                        }
                      
                    }


                   /* ------- end for maxscore ---- */

                   
                   // *** for viewwing from Notification, mark it as read
                   if (Input::get('seen')){
                      $markSeen = User_Notification::where('notification_id',Input::get('notif'))->where('user_id',$this->user->id)->first();
                      $markSeen->seen = true;
                      $markSeen->push();

                      $updateStatus = Input::get('updateStatus');

                  } else $updateStatus=null;

                  //return $coll;

                  //if ($isLead) return "true"; else return "false";

                  return view('evaluation.show-employee', compact('canDoThis', 'updateStatus', 'allowed', 'doneEval', 'evaluator', 'startPeriod', 'endPeriod', 'performanceSummary', 'evalType', 'employee', 'ratingScale', 'evalForm','evalSetting', 'formEntries','maxScore','summaries','showPosition'));
              }        

        } else {

          return view('access-denied');

        }

        
        
        
    }//end show()


    public function store(Request $request)
    {
        
        $evalForm = EvalForm::find($request->evalForm_id);


        if (!$evalForm->isEmpty)
        {
            $evalSetting = EvalSetting::find($evalForm->evalSetting_id);
            

            $competencyAttributes = $evalSetting->competencyAttributes;
            $competencies = $competencyAttributes->groupBy('competency_id');
            $coll = new Collection;

            $employee = User::find($evalForm->user_id);
            $leadershipcheck = ImmediateHead::where('employeeNumber', $employee->employeeNumber)->get();
            $isLead = $request->isLead;

           
            $coll = new Collection;

            foreach ($competencies as $competency ) {

                if (Competency::find($competency[0]->competency_id)->acrossTheBoard) 
                {
                    //determine if leader or not

                   

                       $c=1;
                        foreach ($competency as $comp) {

                            $evalDetail = new EvalDetail;
                            $evalDetail->evalForm_id = $request->evalForm_id;
                            $evalDetail->competency__Attribute_id = $comp->id;


                            

                            // -------- quick fix for autosave kahit di pa complete
                            $rscale = Input::get('ratingScaleID_'.$comp->competency_id);
                            

                            if ($rscale == "0")
                            {
                              $evalDetail->ratingScale_id = 5; //automatic zero
                               $coll->push(['rsID'=>5]);

                            } else {
                              $evalDetail->ratingScale_id = $rscale; //$request->ratingScaleID_.;
                              $coll->push(['rsID'=>"nonzero"]);

                            }

                            

                            $evalDetail->attributeValue =  Input::get('attributeValue_'.$comp->competency_id.'_'.$c);//$request->attributeValue_.$comp->id.'_'.$c;
                           
                            $c++;
                            $evalDetail->save();
                        } //end foreach competency attribute

                    


                    


                } 
                else // -----------  hindi across the board
                {

                  //check the properties of competency and match it whether agent or leader

                  if( empty(Competency::find($competency[0]->competency_id)->percentage) && ( empty($isLead) || $employee->userType_id==4 || $employee->leadOverride) )
                    { //if null percentage and an agent, save it

                      $c=1;
                        foreach ($competency as $comp) {

                            $evalDetail = new EvalDetail;
                            $evalDetail->evalForm_id = $request->evalForm_id;
                            $evalDetail->competency__Attribute_id = $comp->id;
                            //$evalDetail->ratingScale_id = Input::get('ratingScaleID_'.$comp->competency_id); //$request->ratingScaleID_.;
                            $evalDetail->attributeValue =  Input::get('attributeValue_'.$comp->competency_id.'_'.$c);//$request->attributeValue_.$comp->id.'_'.$c;

                            // -------- quick fix for autosave kahit di pa complete
                            $rscale = Input::get('ratingScaleID_'.$comp->competency_id);
                            

                            if ($rscale == "0")
                            {
                              $evalDetail->ratingScale_id = 5; //automatic zero
                               $coll->push(['rsID'=>5]);

                            } else {
                              $evalDetail->ratingScale_id = $rscale; //$request->ratingScaleID_.;
                              $coll->push(['rsID'=>"nonzero"]);

                            }


                           
                            $c++;
                            $evalDetail->save();
                        } //end foreach competency attribute

                  } else  if( empty(Competency::find($competency[0]->competency_id)->percentage) && (!(empty($isLead)) && $employee->userType_id != 4 && empty($employee->leadOverride)) ) {
                    //deadmahin mo lang kasi null percentage and leader sya

                  } else if ( !empty(Competency::find($competency[0]->competency_id)->percentage) && ( !(empty($isLead)) && $employee->userType_id != 4 && empty($employee->leadOverride)) ) {
                    //save mo kasi leader comp sya
                    $c=1;
                        foreach ($competency as $comp) {

                            $evalDetail = new EvalDetail;
                            $evalDetail->evalForm_id = $request->evalForm_id;
                            $evalDetail->competency__Attribute_id = $comp->id;
                            //$evalDetail->ratingScale_id = Input::get('ratingScaleID_'.$comp->competency_id); //$request->ratingScaleID_.;
                            $evalDetail->attributeValue =  Input::get('attributeValue_'.$comp->competency_id.'_'.$c);//$request->attributeValue_.$comp->id.'_'.$c;

                            // -------- quick fix for autosave kahit di pa complete
                            $rscale = Input::get('ratingScaleID_'.$comp->competency_id);
                            

                            if ($rscale == "0")
                            {
                              $evalDetail->ratingScale_id = 5; //automatic zero
                               $coll->push(['rsID'=>5]);

                            } else {
                              $evalDetail->ratingScale_id = $rscale; //$request->ratingScaleID_.;
                              $coll->push(['rsID'=>"nonzero"]);

                            }


                           
                            $c++;
                           $evalDetail->save();
                        } //end foreach competency attribute

                  }
                   

                }//end else hindi across




                
           
            }// end foreach grouped competencies

           

            $evalForm->coachingDone = $request->coachingDone;
            $evalForm->overAllScore = $request->total;
            $evalForm->salaryIncrease = $request->salaryIncrease;
            $evalForm->isDraft = $request->isDraft;
            $evalForm->push();


            //save Performance Summary
            $allSummaries = Summary::all();
            $summaries = new Collection;

            foreach ($allSummaries as $key ) {
               if (!($key->columns->isEmpty()) ) 
                {
                    $cols = $key->columns;
                } else $cols=null;
               if (!($key->rows->isEmpty()) )
               { 
                    $rows = $key->rows;

               }  else $rows = null;

               $summaries->push(['summaryID'=>$key->id, 'header'=>$key->heading,'details'=>$key->description, 'columns'=>$cols, 'rows'=>$rows]);
            }

            $psum = new Collection;

            $ctrSummary=1; 
            foreach ($summaries as $summary){
                
                                    if ( $summary['columns'] !== null)
                                    { 
                                    
                                       foreach ($summary['columns'] as $col)
                                       {
                                        $ps = new PerformanceSummary;
                                        $varname = 'val_'.$ctrSummary.'_'.$col->id;
                                        $ps->value = $request->$varname;
                                        //$ps->value = Input::get('val_'.$ctrSummary.'_'.$col->id);
                                        $ps->summary_id = $summary['summaryID'];
                                        $ps->evalForm_id = $evalForm->id;
                                        $ps->save();
                                        $psum->push($ps);

                                       }
                                      
                                   }

                                    if ( $summary['rows'] !== null) 
                                    {
                                        foreach ($summary['rows'] as $row)
                                        { 
                                            $ps = new PerformanceSummary;
                                            $var2 = 'val_'.$ctrSummary.'_'.$row->id;
                                            $ps->value = $request->$var2;
                                            //$ps->value = Input::get('val_'.$ctrSummary.'_'.$row->id);
                                            $ps->summary_id = $summary['summaryID'];
                                            $ps->evalForm_id = $evalForm->id;
                                            $ps->save();
                                            $psum->push($ps);
                                        }
                                    
                                    }
                                     
                                    
                                    
                                    $ctrSummary++;

            }//end foreach summaries

            /* -------------- log updates made --------------------- */
         $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
            fwrite($file, "-------------------\n EvalID: ". $evalForm->id ." for: ". $employee->lastname.", ". $employee->firstname." newly added ". date('M d h:i:s'). " by ". $this->user->lastname.", ".$this->user->firstname."\n");
            //fclose($file);


            
            // *** if eval type is REGULARIZATION, we need to inform HR admin that a new regularization must be updated
            if ($evalForm->evalSetting_id == 3)
            {

              // ***** add in NOTIFICATION for HR system notif

                         $notification = new Notification;
                            $notification->relatedModelID = $evalForm->id;
                            $notification->type = 5;
                            $notification->from = $evalForm->evaluatedBy;
                            $notification->save();


                            $hrAdmins = User::where('userType_id',5)->get();

                            foreach ($hrAdmins as $key ) {

                                $nu = new User_Notification;
                                $nu->user_id = $key->id;
                                $nu->notification_id = $notification->id;
                                $nu->seen = false;
                                $nu->save();

                                // NOW, EMAIL THE HR CONCERNED
                           /*
                             Mail::send('emails.regularizationNotice', ['tl'=> ImmediateHead::find(ImmediateHead_Campaign::find($evalForm->evaluatedBy)->immediateHead_id), 'evalForm'=>$evalForm, 'employee'=>$employee,'notification'=>$notification], function ($m) use ($key) 
                             {
                                $toHR = User::find($key->id);
                                $m->from('OES@openaccessbpo.net', 'OES-OAMPI Evaluation System');
                                $m->to($toHR->email, $toHR->firstname." ".$toHR->lastname)->subject('New Regularization Eval');     

                                /* -------------- log updates made --------------------- 
                                     $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
                                        fwrite($file, "-------------------\n Email sent to ". $toHR->email."\n");
                                        fclose($file);  
                                                           
                            

                            }); //end mail */
                            }
            }

             


            return response()->json(['saved'=>true, 'evalFormID' => $evalForm->id, 'psummary'=> $psum]);
            //return $psum;
        } //end if not empty
        else return response()->json(['saved'=>false, 'evalFormID' => '0', 'psummary'=>$psum]);

    }

   

    public function update($id, Request $request)
    {
        $evalForm = EvalForm::find($id);

        $details = $evalForm->details;


        $coll = new Collection;
             /* -------------- log updates made --------------------- */
         $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");

         $ctr = 1;
         $var = null;

        foreach ($details as $deet) {
            $attvar = "att_".$deet->id;
            $ratingvar = "rating_".$deet->id;

             //check if things were changed

           // if ($deet->ratingScale_id !== (int)Input::get($ratingvar)) {
                
                if ( Input::get($ratingvar) == null){
                    //if no val for new rating, just check the value
                    if (Input::get($attvar) == $deet->attributeValue) { } //same, do nothing
                    else {
                        $changeme = EvalDetail::find($deet->id);
                        $changeme->attributeValue = Input::get($attvar); //theres a new value
                        $changeme->push();
                    } 
                } else {
                    $changeme = EvalDetail::find($deet->id);
                    $changeme->ratingScale_id = (int)Input::get($ratingvar);
                    $changeme->attributeValue = Input::get($attvar);
                    $changeme->push();
                }                
               
           
        }
            $evalForm->coachingDone = $request->coachingDone;
            $evalForm->overAllScore = $request->overAllScore;
            $evalForm->salaryIncrease = $request->salaryIncrease;
            $evalForm->isDraft = $request->isDraft;
            $evalForm->push();


            //save Performance Summary
            $allSummaries = Summary::all();
            $summaries = new Collection;

            foreach ($allSummaries as $key ) {
               if (!($key->columns->isEmpty()) ) 
                {
                    $cols = $key->columns;
                } else $cols=null;
               if (!($key->rows->isEmpty()) )
               { 
                    $rows = $key->rows;

               }  else $rows = null;

               $summaries->push(['summaryID'=>$key->id, 'header'=>$key->heading,'details'=>$key->description, 'columns'=>$cols, 'rows'=>$rows]);
            }

            //$psum = new Collection;

            $ctrSummary=1; 
            foreach ($summaries as $summary){
                
                                    if ( $summary['columns'] !== null)
                                    { 
                                    
                                       foreach ($summary['columns'] as $col)
                                       {
                                        
                                        $varname = 'val_'.$ctrSummary.'_'.$col->id;
                                        $idvar = 'id_'.$ctrSummary.'_'.$col->id;

                                        $ps = PerformanceSummary::find((int)$request->$idvar);

                                        if (!$ps->isEmpty){
                                            if ($ps->value !== $request->$varname){
                                            $ps->value = $request->$varname;
                                            $ps->push();
                                        }

                                        }

                                        
                                        
                                        

                                       }
                                      
                                   }

                                    if ( $summary['rows'] !== null) 
                                    {
                                        foreach ($summary['rows'] as $row)
                                        { 
                                            
                                            $var2 = 'val_'.$ctrSummary.'_'.$row->id;
                                            $idvar2 = 'id_'.$ctrSummary.'_'.$row->id;

                                            $ps2 = PerformanceSummary::find((int)$request->$idvar2);
                                            if (count($ps2) !== 0){
                                                if ($ps2->value !== $request->$var2){
                                                $ps2->value = $request->$var2;
                                                $ps2->push();
                                            }

                                            }

                                            

                                           ;
                                        }
                                    
                                    }
                                     
                                    
                                    
                                    $ctrSummary++;

            }//end foreach summaries

       

        
            fwrite($file, "-------------------\n EvalID: ". $evalForm->id ." for: ". User::find($evalForm->user_id)->lastname.", ". User::find($evalForm->user_id)->firstname." updated ". date('M d h:i:s'). " by ". $this->user->firstname.", ".$this->user->lastname."\n");
            

             // *** if eval type is REGULARIZATION, we need to inform HR admin that a new regularization must be updated
           

           
            fclose($file);
        
        return response()->json($evalForm);

    }

    public function updateReview($id, Request $request)
    {
        $evalForm = EvalForm::find($id);

        $details = $evalForm->details;
        $ctr = 0;

        $arrStat = new Collection;


        
        foreach ($details as $deet) {
            $attvar = "att_".$deet->id;
            $empCheck = Competency__Attribute::find($deet->competency__Attribute_id)->attribute;
            $feedback = $empCheck->name;

            if ($feedback === "Employee Feedback"){

                    // if (Input::get($attvar) == $deet->attributeValue) { } //same, do nothing
                    // else {
                        $changeme = EvalDetail::find($deet->id);
                        $changeme->attributeValue = Input::get($attvar); //theres a new value
                        $changeme->push();
                    //} 
                        $arrStat->push($deet->id);

            }

            
           
          }
        $evalForm->coachingDone = true;
        $evalForm->coachingTimestamp = date('Y-m-d h:i:s');
        
        $evalForm->push();

        return response()->json($arrStat);

    }

    public function updatePeriod(Request $request)
    {
        $id = $request->id;
        $evalForm = EvalForm::find($id);

        $evalForm->startPeriod = date("Y-m-d", strtotime($request->newStart));
        $evalForm->endPeriod = date("Y-m-d", strtotime($request->newEnd));
        $evalForm->save();
        return redirect()->action('EvalFormController@show',$id);

    }

    //public function

    

}
