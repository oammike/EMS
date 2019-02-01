<?php

namespace OAMPI_Eval\Http\Controllers;

use Hash;
use Carbon\Carbon;
use Excel;
use \PDF;
use \DB;
use \App;
use \Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Yajra\Datatables\Facades\Datatables;

use OAMPI_Eval\Http\Requests;
use OAMPI_Eval\User;
use OAMPI_Eval\Cutoff;
use OAMPI_Eval\User_Leader;
use OAMPI_Eval\User_CWS;
use OAMPI_Eval\User_DTRP;
use OAMPI_Eval\User_VL;
use OAMPI_Eval\User_SL;
use OAMPI_Eval\User_LWOP;
use OAMPI_Eval\User_OT;
use OAMPI_Eval\User_OBT;
use OAMPI_Eval\Campaign;
use OAMPI_Eval\Status;
use OAMPI_Eval\Team;
use OAMPI_Eval\Floor;
use OAMPI_Eval\UserType;
use OAMPI_Eval\Position;
use OAMPI_Eval\ImmediateHead;
use OAMPI_Eval\ImmediateHead_Campaign;
use OAMPI_Eval\EvalForm;
use OAMPI_Eval\EvalSetting;
use OAMPI_Eval\Schedule;
use OAMPI_Eval\FixedSchedules;
use OAMPI_Eval\MonthlySchedules;
use OAMPI_Eval\Restday;
use OAMPI_Eval\Biometrics;
use OAMPI_Eval\User_VLcredits;
use OAMPI_Eval\User_SLcredits;
use OAMPI_Eval\EvalType;
use OAMPI_Eval\Survey;
use OAMPI_Eval\Survey_Question;
use OAMPI_Eval\Survey_Option;
use OAMPI_Eval\Survey_Question_Category;
use OAMPI_Eval\Survey_Response;
use OAMPI_Eval\Survey_Essay;
use OAMPI_Eval\Survey_Extradata;
use OAMPI_Eval\Survey_Notes;
use OAMPI_Eval\Survey_User;
use OAMPI_Eval\Options;
use OAMPI_Eval\Categorytag;

class SurveyController extends Controller
{
    protected $survey;
    use Traits\UserTraits;
    use Traits\TimekeepingTraits;

     public function __construct(Survey $survey)
    {
        $this->middleware('auth');
        $this->user =  User::find(Auth::user()->id);
    }

    public function index()
    {
      $surveys = Survey::all();
      return $surveys;

    }

    

   

    public function create()
    {

        
    }

    public function bePart(Request $request)
    {
        $extraData = Survey_Extradata::where('user_id',$this->user->id)->where('survey_id',$request->survey_id)->get();
        if (count($extraData) > 0) {

            $e = $extraData->first();

            if ($request->nps >= 4){
                
                $e->beEEC = $request->bepart;
                $e->save();
                return response()->json($e);

            } else if ($request->nps <= 2.5 ){

                
                $e->forGD = $request->bepart;
                $e->save();
                return response()->json($e);

            }else  return response()->json($e);
            
        }
        else
            return response()->json(['status'=>"no record"]);
    }

    

     public function deleteThisSurvey($id)
    {
      $u = User::find($id);
      $u->delete();

      return redirect()->back();

    }


    public function destroy($id)
    {
        $this->survey->destroy($id);
        return back();
    }

    

    public function editSurvey($id, Request $request)
    {

        

        
       
    }

    public function saveItem(Request $request)
    {
        if ($request->survey_optionsid == 'e'){

            $item = new Survey_Essay;
            $item->user_id = $this->user->id;
            $item->question_id = $request->questionid;
            $item->survey_id = $request->survey_id;
            $item->answer = $request->answer;
            $item->save();

            //update user survey
            $survey = Survey_User::where('user_id',$this->user->id)->where('survey_id',$item->survey_id)->first();
            // $survey->isDraft = 0;
            // $survey->isDone = true;
            $survey->lastItem = $request->questionid;
            $survey->save();


        }else if ($request->survey_optionsid == 'x'){

            $extra = new Survey_Extradata;
            $extra->user_id = $this->user->id;
            $extra->survey_id = $request->survey_id;
            $extra->gender = $request->gender;
            $extra->education = $request->education;
            $extra->course = $request->course;
            $extra->currentLocation = $request->currentlocation;

            if (!empty($request->hr)) $hr = $request->hr * 60;
            else $hr = 0;

            if (!empty($request->mins)) $mins = $request->mins;
            else $mins = 0;

            $extra->commuteTime = $hr + $mins;
            $extra->hobbiesinterest = $request->hobbiesinterest;
            $extra->save();

            //update user survey
            $survey = Survey_User::where('user_id',$this->user->id)->where('survey_id',$request->survey_id)->first();
            $survey->isDraft = 0;
            $survey->isDone = true;
            $survey->save();

            return response()->json($extra);


        }

        else{

            $item = new Survey_Response;
            $item->user_id = $this->user->id;
            $item->question_id = $request->questionid;
            $item->survey_optionsID = $request->survey_optionsid;
            $item->save();

            if ($request->comment !== ''){

                $cmt = new Survey_Notes;
                $cmt->user_id = $this->user->id;
                $cmt->question_id = $request->questionid;
                $cmt->comments = $request->comment;
                $cmt->save();
            }

        }
        

        return response()->json($item);

    }

    public function saveSurvey(Request $request)
    {

    }

   
    public function show($id)
    {

        // check first kung may saved nang Survey-USer
        // if wala, create one

        DB::connection()->disableQueryLog(); 

        $existing = Survey::find($id);
        if (empty($existing)) return view('empty');

        $us = Survey_User::where('user_id',$this->user->id)->where('survey_id',$id)->get();

        if (count($us) >= 1){
            $userSurvey = $us->first();

            if ($userSurvey->isDone) return redirect('/surveyResults/'.$id);

            $extraDataNa=false;

            //now, update his latest submitted response
            $l = DB::table('survey_responses')->where('survey_responses.user_id',$this->user->id)->
                            join('survey_options','survey_responses.survey_optionsID','=','survey_options.id')->
                            leftJoin('surveys','survey_options.survey_id','=','surveys.id')->
                            join('survey_questions','survey_responses.question_id','=','survey_questions.id')->
                            select('survey_responses.id','survey_responses.user_id','survey_questions.ordering', 'survey_responses.question_id','survey_responses.survey_optionsID as answer')->
                            orderBy('survey_responses.id','DESC')->get();//  Survey_Response::where('user_id')
            if (count($l) > 0) {
                $latest = $l[0];
                $userSurvey->lastItem = $latest->ordering;
                $userSurvey->isDraft = true;
                $userSurvey->push();

                //now, check kung may answer na sya from essay
                $ess =  DB::table('survey_essays')->where('survey_essays.user_id',$this->user->id)->
                            join('survey_questions','survey_essays.question_id','=','survey_questions.id')->
                            select('survey_questions.survey_id','survey_questions.ordering')->get();

                if (count($ess) > 0){

                    
                    $startFrom = $ess[0]->ordering;
                    $e = array_pluck($ess,'survey_id');

                    if (in_array($id, $e)) //meaning, may essay na nga sya for that survey, check na kung may extradata submitted
                    {
                        $extraDataNa = 1;

                    } 

                }else  $startFrom = $latest->ordering;    

            }
            else {
                $latest = null;
                $startFrom = 1;
            }
            
            //return response()->json($latest);

        }else {

            $userSurvey = new Survey_User;
            $userSurvey->user_id = $this->user->id;
            $userSurvey->survey_id = $id;
            $userSurvey->startDate = Carbon::now('GMT+8')->format('Y-m-d H:i:s');
            $userSurvey->lastItem = 1;
            $userSurvey->save();
            $latest=null;
            $startFrom = 1;
            $extraDataNa=false;

        }

        //$survey = Survey::find($id);
        //return $userSurvey;

        //return response()->json($latest);


        $questions = DB::table('surveys')->where('surveys.id',$id)->
                        //where('form_submissions_users.created_at','>=',$from)->
                        //where('form_submissions_users.created_at','<=',$to)->
                        join('survey_questions','survey_questions.survey_id','=','surveys.id')->
                        //join('survey_options','surveys.id','=','survey_options.survey_id')->
                        //join('formBuilder_items','form_submissions.formBuilder_itemID','=','formBuilder_items.id')->
                        //leftJoin('users','form_submissions_users.user_id','=','users.id')->
                        select('surveys.name','survey_questions.ordering','survey_questions.img','survey_questions.id', 'survey_questions.value as question', 'survey_questions.responseType')->
                        orderBy('survey_questions.ordering','ASC')->get();
        $options = DB::table('survey_options')->where('survey_options.survey_id',$id)->
                         leftJoin('options','options.id','=','survey_options.options_id')->
                         select('survey_options.id', 'options.label','options.value','options.ordering')->
                         orderBy('options.ordering','ASC')->get();

        $extradata = ['travel time to and from office','hobbies and interest'];

        $totalItems = count($questions);
        // $survey = new Collection;
        // $survey->push(['answers'=>$options, 'questions'=>$questions]);


        return view('forms.survey-show', compact('id','totalItems','questions','startFrom','options','userSurvey','latest','extradata','extraDataNa'));


                   
    }

     public function store(Request $request)
    {
        $employee = new User;

        
        $employee->name = $request->name;
        $employee->firstname = $request->firstname;
        $employee->middlename = $request->middlename;
        $employee->lastname = $request->lastname;
        $employee->nickname = $request->nickname;
        $employee->gender = $request->gender;
        $employee->employeeNumber = $request->employeeNumber;
        $employee->accesscode = $request->accesscode;
        $employee->email = preg_replace('/\s+/', '', $request->email);
        $employee->password =  Hash::make($request->password);
        $employee->updatedPass = false;


        $dt = new \DateTime(date('Y-m-d',strtotime($request->dateHired)));
        $employee->dateHired = $dt->setTime(0,0); 

        if (!empty($request->birthday) ){
          $bday = new \DateTime(date('Y-m-d',strtotime($request->birthday)));
          $employee->birthday = $bday;
        }

        if ( !empty($request->dateRegularized) ){
            $dr = new \DateTime(date('Y-m-d',strtotime($request->dateRegularized)));
            $employee->dateRegularized = $dr->setTime(0,0); //date("Y-m-d h:i:sa", strtotime($request->dateRegularized."00:00")); 

        } else $employee->dateRegularized = null;

        if ( !empty($request->startTraining) ){
            $dr = new \DateTime(date('Y-m-d',strtotime($request->startTraining)));
            $employee->startTraining = $dr->setTime(0,0); //date("Y-m-d h:i:sa", strtotime($request->dateRegularized."00:00")); 

        } else $employee->startTraining = null;

        if ( !empty($request->endTraining) ){
            $dr = new \DateTime(date('Y-m-d',strtotime($request->endTraining)));
            $employee->endTraining = $dr->setTime(0,0); //date("Y-m-d h:i:sa", strtotime($request->dateRegularized."00:00")); 

        } else $employee->endTraining = null;

        
        $employee->userType_id =  $request->userType_id;
        $employee->status_id = $request->status_id;
        $employee->position_id = $request->position_id;
        $employee->leadOverride = $request->leadOverride;

        //$employee->immediateHead_Campaigns_id = $request->immediateHead_Campaigns_id;
        /* $employee->campaign_id = $request->campaign_id;
        $employee->immediateHead_id = $request->immediateHead_id; */
        $employee->save();

        $team = new Team;
        $team->user_id = $employee->id;
        $team->immediateHead_Campaigns_id = $request->immediateHead_Campaigns_id;
        $team->campaign_id = $request->campaign_id;
        $team->floor_id = $request->floor_id;
        $team->save();

        //return response()->json($employee);
        return response()->json(['dateHired'=>$request->dateHired, 'saveddateHired'=>$employee->dateHired, 'user_id'=>$employee->id]);
        
    }

    public function surveyResults($id)
    {
        DB::connection()->disableQueryLog(); 

        $survey = Survey::find($id);
        $e = Survey_Extradata::where('user_id',$this->user->id)->where('survey_id',$survey->id)->get();
        if (count($e) > 0) $extraData = $e->first()->beEEC;
        else $extraData=null;

        //$actives = count(DB::table('users')->where('status_id','!=',7)->where('status_id','!=',8)->where('status_id','!=',9)->
        //                select('users.status_id')->get());

        //exclude Taipei and Xiamen
        $actives = count(DB::table('users')->where('status_id','!=',7)->where('status_id','!=',8)->where('status_id','!=',9)->
                        leftJoin('team','team.user_id','=','users.id')->
                        select('users.id','team.floor_id')->
                        where('team.floor_id','!=',10)->
                        where('team.floor_id','!=',11)->get());

        //return count($actives);
                    //);
        $completed = count(Survey_User::where('isDone',true)->get());
        $percentage = number_format(($completed / $actives)*100,2);


        // NPS questions: 13,15,44, 45, 49
        $npsQuestions = DB::table('surveys')->where('surveys.id',$id)->
                            join('survey_questions','survey_questions.survey_id','=','surveys.id')->
                            join('survey_responses','survey_responses.question_id','=','survey_questions.id')->
                            select('survey_questions.id as question','survey_responses.survey_optionsID as answer','survey_responses.user_id')->
                            get();
        $my = collect($npsQuestions);
        $m = $my->where('user_id',$this->user->id);
        $m2 = collect($m);
        $n = $m2->whereIn('question',[13,15,44,45,49]);
        $nps = number_format(($n->pluck('answer')->sum())/count($n->pluck('answer')),2);
        $promoter=false;
        $detractor=false;

        if ($nps >= 4.0) {$color = "#3c8dbc"; $promoter=true; } //blue;
        //else if ($nps > 3.6 && $nps <= 4.5 ) $color="#8ccb2c"; //green
        else if ($nps >= 2.1 && $nps <= 3.9 ) $color="#ffe417"; //yellow
        //else if ($nps >= 1.6 && $nps <= 2.1 ) $color="#f36b19"; //orange
        else { $color="#fd1e1e"; $detractor=true; } //red

       

        return view('forms.survey-results',compact('survey','extraData','actives','completed','percentage','nps','color','promoter','detractor'));

    }

    public function update($id)
    {
        

    }




    
}
