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
use OAMPI_Eval\Survey_Intro;

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

      return view('forms.survey-index',compact('surveys'));
      

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

    public function downloadRaw($id)
    {
      $survey = Survey::find($id); 
      $tenure3mos = Carbon::now('GMT+8')->addMonths(-3);

      //******* show memo for test people only ,ems,joy,jaja, ben, henry,ella,juls, belmonte
      $testgroup = [564,508,1644,1611,1784,491,1,184,887,3835];
      $keyGroup =  [564,1611,491,1,184,1099,628,3835];
      //(in_array($this->user->id, $testgroup)) ? $canAccess=true : $canAccess=false;
      (in_array($this->user->id, $keyGroup)) ? $canDL=true : $canDL=false;

      switch ($id) {
        case 1:
        {

          

          $allEmployees = DB::table('survey_user')->where('survey_user.survey_id',1)->
                       
                        join('users','users.id','=','survey_user.user_id')->
                        where('users.dateHired','<=',$tenure3mos->format('Y-m-d H:i:s'))->
                        join('team','team.user_id','=','survey_user.user_id')->
                        join('campaign','team.campaign_id','=','campaign.id')->
                        join('survey_extradata','survey_extradata.user_id','=','survey_user.user_id')->
                        leftJoin('survey_essays','survey_essays.user_id','=','survey_user.user_id')->
                       
                        select('users.id', 'users.firstname','users.lastname','users.dateHired', 'campaign.name as program','campaign.id as programID','campaign.isBackoffice as backOffice','team.floor_id','survey_extradata.gender','survey_extradata.education','survey_extradata.course', 'survey_extradata.currentlocation','survey_extradata.commuteTime','survey_extradata.hobbiesinterest','survey_essays.answer as essay')->
                        where('survey_user.isDone',1)->
                        where('team.floor_id','!=',10)->
                        where('team.floor_id','!=',11)->
                        where('campaign.id','=',$id)->
                        orderBy('users.lastname')->get(); //)->take(30); return response()->json($allEmployees);
                        //return $allEmployees;

          $allResp = DB::table('survey_questions')->where('survey_questions.survey_id',1)->
                        join('survey_responses','survey_responses.question_id','=','survey_questions.id')->

                        join('survey_user','survey_user.user_id','=','survey_responses.user_id')->
                        //join('survey_extradata','survey_extradata.user_id','=','survey_responses.user_id')->
                        join('users','users.id','=','survey_user.user_id')->
                        where('users.dateHired','<=',$tenure3mos->format('Y-m-d H:i:s'))->
                        //------leftJoin('survey_essays','survey_essays.user_id','=','users.id')->
                        //join('survey_notes','survey_notes.user_id','=','survey_user.user_id')->
                        join('team','team.user_id','=','survey_user.user_id')->
                        //join('campaign','team.campaign_id','=','campaign.id')->
                       

                        //join('campaign_logos','campaign_logos.campaign_id','=','campaign.id')->
                        //'survey_essays.answer as essay', 'survey_extradata.course','survey_extradata.currentlocation', 'campaign.name as program','campaign.id as programID','campaign.isBackoffice as backOffice',

                        select('survey_responses.user_id as userID', 'survey_responses.question_id as question', 'survey_responses.survey_optionsID as rating','team.floor_id')->
                        where('survey_user.isDone',1)->
                        where('team.floor_id','!=',10)->
                        where('team.floor_id','!=',11)->get();


          $allNotes = DB::table('survey_user')->where('survey_user.survey_id',1)->
                          join('users','survey_user.user_id','=','users.id')->
                          where('users.dateHired','<=',$tenure3mos->format('Y-m-d H:i:s'))->
                          join('survey_notes','survey_notes.user_id','=','survey_user.user_id')->
                          select('survey_user.user_id as userID','users.lastname','users.firstname', 'survey_notes.question_id','survey_notes.comments')->
                          where('survey_notes.comments','!=',null)->
                          orderBy('users.lastname')->get();
          $allQuestions = DB::table('survey_questions')->where('survey_id',1)->select('survey_questions.value as question','survey_questions.id')->get();

          $description = $survey->description;
          $headers = ['Lastname','Firstname', 'Program','Tenure','Gender','Education','Course','Current Location','Hobbies/Interests','Commute Time (mins)'];
          $c =10;
          $q = 1;
          foreach ($allQuestions as $key) {
            $headers[$c] = "Q".$q.": ".$key->question; $c++;$q++;
            $headers[$c] = "Notes/Comments";$c++;
          }
          

          Excel::create($survey->name,function($excel) use($id,$allEmployees,$allQuestions,$allNotes,$allResp, $survey, $headers,$description) 
               {
                      $excel->setTitle($survey->name.' Raw Data');

                      // Chain the setters
                      $excel->setCreator('Programming Team')
                            ->setCompany('OpenAccess');

                      // Call them separately
                      $excel->setDescription($description);
                      $excel->sheet("Sheet 1", function($sheet) use ($id,$allEmployees,$allQuestions,$allNotes,$allResp, $headers)
                      {
                        $sheet->appendRow($headers);

                        
                        $arr = [];

                        foreach($allEmployees as $employee)//collect($allEmployees)->where('programID',16)
                        {
                          $i = 0;

                          $arr[$i] = $employee->lastname; $i++;
                          $arr[$i] = $employee->firstname; $i++;
                          $arr[$i] = $employee->program; $i++;

                          //TENURE
                          $tenure = Carbon::parse($employee->dateHired,'Asia/Manila')->diffInYears(Carbon::now('Asia/Manila'));
                          if ($tenure == 0) {$arr[$i] = "< a yr";}
                          else if($tenure == 1) {$arr[$i] = "1 year";}
                          else $arr[$i] = $tenure . " year(s)";  

                          $i++;

                          $arr[$i] = $employee->gender; $i++;
                          $arr[$i] = $employee->education; $i++;
                          $arr[$i] = $employee->course; $i++;
                          $arr[$i] = $employee->currentlocation; $i++;
                          $arr[$i] = $employee->hobbiesinterest; $i++;
                          $arr[$i] = $employee->commuteTime; $i++;


                          $qCounter=1;
                          foreach ($allQuestions as $q) {

                            if($qCounter == count($allQuestions)){
                              $arr[$i]= $employee->essay;
                              //$arr[$i] = collect($allResp)->where('userID',$employee->id)->first()->essay;
                            } else
                            {

                              //---- RATING
                              $r = collect($allResp)->where('userID',$employee->id)->where('question',$q->id);
                              if (count($r) > 0)
                                $rating = $r->first()->rating;
                              else
                                $rating = null;

                              $arr[$i]= $rating; $i++;
                              
                              //---- NOTE
                              $n = collect($allNotes)->where('userID',$employee->id)->where('question_id',$q->id);
                              if (count($n)>0) $note = $n->first()->comments;
                              else $note=null;

                              $arr[$i]= $note; $i++;

                            }

                            $qCounter++;

                          }//end foreach questions



                            $sheet->appendRow($arr);

                        }//end foreach employee


                        
                     });//end sheet1



              })->export('xls');

              return "Download";

        }break;

        case 5:
        {

          $correct = Carbon::now('GMT+8'); //->timezoneName();

           if($this->user->id !== 564 ) {
              $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
                fwrite($file, "-------------------\n Downloaded 360 Survey -- " . $correct->format('M d h:i A'). " by [". $this->user->id."] ".$this->user->lastname."\n");
                fclose($file);
            } 
                          
          $alldata = DB::table('survey_responses')->where('survey_responses.survey_id',$id)->
                          join('survey_user','survey_responses.survey_userid','=','survey_user.id')->
                          join('survey_questions','survey_responses.question_id','=','survey_questions.id')->
                          join('survey_options','survey_responses.survey_optionsID','=','survey_options.id')->
                          join('options','survey_options.options_id','=','options.id')->
                          join('survey_notes','survey_notes.survey_userid','=','survey_user.id')->
                          join('users','survey_user.surveyFor','=','users.id')->
                          join('positions','users.position_id','=','positions.id')->
                          join('team','team.user_id','=','users.id')->
                          join('campaign','team.campaign_id','=','campaign.id')->
                          select('survey_user.surveyFor','survey_user.user_id as surveyBy','survey_responses.question_id','survey_questions.value as question','options.value as rating','survey_responses.optiontype','survey_notes.comments', 'users.firstname','users.lastname','positions.name as jobTitle','campaign.name as program')->get(); 
           $allUserID = collect($alldata)->pluck('surveyFor')->unique()->flatten();//groupBy('surveyFor');
           $allEmployees = collect($alldata)->groupBy('surveyFor');

           
           $allQuestions = collect($alldata)->sortBy('question_id')->pluck('question_id')->unique()->flatten();
           //return $allQuestions;

           $allCamp = collect($alldata)->pluck('program')->unique()->flatten();//groupBy('program');


           $description = $survey->description;
           $headers = ['Lastname','Firstname','Job Title', 'Program'];
           
            $q = 1;
            $c = 4;
            foreach ($allQuestions as $key) {
              $headers[$c] = "Skills/Behaviour_".$q." IMPORTANCE"; $c++;
              $headers[$c] = "Skills/Behaviour_".$q." COMPETENCE"; $c++;$q++;
              
            }
            $headers[$c] = "Notes/Comments";



           Excel::create($survey->name,function($excel) use($id,$allEmployees,$allQuestions,$alldata, $survey, $headers,$description) 
               {
                      $excel->setTitle($survey->name.' Raw Data');

                      // Chain the setters
                      $excel->setCreator('Programming Team')
                            ->setCompany('OpenAccess');

                      // Call them separately
                      $excel->setDescription($description);
                      $excel->sheet("All Data", function($sheet) use ($id,$allEmployees,$allQuestions,$alldata, $headers)
                      {
                        $sheet->appendRow($headers);

                        
                        $arr = [];

                        foreach($allEmployees as $employee)//collect($allEmployees)->where('programID',16)
                        {
                          $i = 0;

                          $arr[$i] = $employee->first()->lastname; $i++;
                          $arr[$i] = $employee->first()->firstname; $i++;
                          $arr[$i] = $employee->first()->jobTitle; $i++;
                          $arr[$i] = $employee->first()->program; $i++;


                          $qCounter=1;
                          foreach ($allQuestions as $q) {


                            $itm = collect($alldata)->where('surveyFor',$employee->first()->surveyFor)
                                                      ->where('question_id',$q)
                                                      ->where('optiontype',1)->pluck('rating');
                            (count($itm)>0) ? $arr[$i] = $itm[0] : $arr[$i] = null; 
                            $i++;

                            $itm2 = collect($alldata)->where('surveyFor',$employee->first()->surveyFor)
                                                      ->where('question_id',$q)
                                                      ->where('optiontype',2)->pluck('rating');
                            (count($itm2)>0) ? $arr[$i] = $itm2[0] : $arr[$i] = null; 
                            $i++;





                            $qCounter++;

                          }//end foreach questions

                          $cmt = collect($alldata)->where('surveyFor',$employee->first()->surveyFor)->pluck('comments');
                          $arr[$i] = $cmt[0];



                            $sheet->appendRow($arr);

                        }//end foreach employee


                        
                     });//end sheet1



              })->export('xls');

           

              return "Download";

        }break;

        case 6:
        {
          // Pulse 2020
          $allEmployees = DB::table('survey_user')->where('survey_user.survey_id',$survey->id)->
                       
                        join('users','users.id','=','survey_user.user_id')->
                        where('users.dateHired','<=',$tenure3mos->format('Y-m-d H:i:s'))->
                        where([
                              ['users.status_id', '!=', 6],
                              ['users.status_id', '!=', 16],
                              ['users.status_id', '!=', 7],
                              ['users.status_id', '!=', 8],
                              ['users.status_id', '!=', 9],
                                      ])->
                        join('survey_extradata','survey_extradata.user_id','=','survey_user.user_id')->
                        where('survey_extradata.survey_id','=',$survey->id)->
                        join('team','team.user_id','=','survey_user.user_id')->
                        join('campaign','team.campaign_id','=','campaign.id')->
                        leftJoin('survey_essays','survey_essays.user_id','=','survey_user.user_id')->
                        where('survey_essays.survey_id','=',$survey->id)->
                        select('users.id', 'users.firstname','users.lastname','users.dateHired', 'campaign.name as program','campaign.id as programID','campaign.isBackoffice as backOffice','team.floor_id','survey_extradata.gender','survey_extradata.education','survey_extradata.course', 'survey_extradata.currentlocation','survey_extradata.commuteTime','survey_extradata.hobbiesinterest', 'survey_essays.answer as essay','survey_essays.question_id','survey_user.survey_id as surveyID')->
                        where('team.floor_id','!=',10)->
                        where('team.floor_id','!=',11)->
                        orderBy('users.lastname')->get();

                       
         

          $allResp = DB::table('survey_questions')->where('survey_questions.survey_id',$id)->
                        join('survey_responses','survey_responses.question_id','=','survey_questions.id')->

                        join('survey_user','survey_user.user_id','=','survey_responses.user_id')->
                        where('survey_user.survey_id','=',$survey->id)->
                        //join('survey_extradata','survey_extradata.user_id','=','survey_responses.user_id')->
                        join('users','users.id','=','survey_user.user_id')->
                        where('users.dateHired','<=',$tenure3mos->format('Y-m-d H:i:s'))->
                        //------leftJoin('survey_essays','survey_essays.user_id','=','users.id')->
                        //join('survey_notes','survey_notes.user_id','=','survey_user.user_id')->
                        join('team','team.user_id','=','survey_user.user_id')->
                        //join('campaign','team.campaign_id','=','campaign.id')->
                       

                        //join('campaign_logos','campaign_logos.campaign_id','=','campaign.id')->
                        //'survey_essays.answer as essay', 'survey_extradata.course','survey_extradata.currentlocation', 'campaign.name as program','campaign.id as programID','campaign.isBackoffice as backOffice',

                        select('survey_responses.user_id as userID', 'survey_responses.question_id as question', 'survey_responses.survey_optionsID as rating','team.floor_id')->
                        where('survey_user.isDone',1)->
                        where('team.floor_id','!=',10)->
                        where('team.floor_id','!=',11)->
                        where('users.status_id',"!=",7)->
                        where('users.status_id',"!=",8)->
                        where('users.status_id',"!=",9)->
                        where('users.status_id',"!=",13)->
                        where('users.status_id',"!=",16)->get();

                        


          $allNotes = DB::table('survey_user')->where('survey_user.survey_id',$id)->
                          join('users','survey_user.user_id','=','users.id')->
                          where('users.dateHired','<=',$tenure3mos->format('Y-m-d H:i:s'))->
                          join('survey_notes','survey_notes.user_id','=','survey_user.user_id')->
                          where('survey_notes.survey_id','=',$survey->id)->
                          select('survey_user.user_id as userID','users.lastname','users.firstname', 'survey_notes.question_id','survey_notes.comments')->
                          
                          //where('survey_notes.survey_id','=',$id)->
                          where('survey_notes.comments','!=',null)->
                          orderBy('users.lastname')->get();

                          

          $allQuestions = DB::table('survey_questions')->where('survey_id',$id)->select('survey_questions.value as question','survey_questions.id')->get();

          $description = $survey->description;
          $headers = ['Lastname','Firstname', 'Program','Tenure','Gender','Education','Course','Current Location','Hobbies/Interests','Commute Time (mins)'];
          $c =10;
          $q = 1;
          foreach ($allQuestions as $key) {
            $headers[$c] = "Q".$q.": ".$key->question; $c++;$q++;
            $headers[$c] = "Notes/Comments";$c++;
          }
          
          //return response()->json(['allEmployees'=>$allEmployees, 'allQuestions'=>$allQuestions,'allNotes'=>$allNotes,'allResp'=>$allResp]);

          if($canDL)
          {
            //return response()->json(['allEmployees'=>$allEmployees, 'allQuestions'=>$allQuestions,'allNotes'=>$allNotes,'allResp'=>$allResp]);
            
                $file = fopen('public/build/rewards.txt', 'a') or die("Unable to open logs");
                  fwrite($file, "-------------------\n DL survey[".$id."] on ".Carbon::now('GMT+8')->format('Y-m-d H:i')." by [". \Auth::user()->id."] ".\Auth::user()->lastname."\n");
                  fclose($file);
            

                  //return response()->json(['allEmployees'=>$allEmployees,'allQuestions'=>$allQuestions,'allNotes'=>$allNotes,'allResp'=>$allResp]);

            Excel::create($survey->name,function($excel) use($id,$allEmployees,$allQuestions,$allNotes,$allResp, $survey, $headers,$description) 
               {
                      $excel->setTitle($survey->name.' Raw Data');

                      // Chain the setters
                      $excel->setCreator('Programming Team')
                            ->setCompany('OpenAccess');

                      // Call them separately
                      $excel->setDescription($description);
                      $excel->sheet("Sheet 1", function($sheet) use ($id,$allEmployees,$allQuestions,$allNotes,$allResp, $headers)
                      {
                        $sheet->appendRow($headers);

                        
                        $arr = [];

                        foreach($allEmployees as $employee)//collect($allEmployees)->where('programID',16)
                        {
                          $i = 0;

                          $arr[$i] = $employee->lastname; $i++;
                          $arr[$i] = $employee->firstname; $i++;
                          $arr[$i] = $employee->program; $i++;

                          //TENURE
                          $tenure = Carbon::parse($employee->dateHired,'Asia/Manila')->diffInYears(Carbon::now('Asia/Manila'));
                          if ($tenure == 0) {$arr[$i] = "< a yr";}
                          else if($tenure == 1) {$arr[$i] = "1 year";}
                          else $arr[$i] = $tenure . " year(s)";  

                          $i++;

                          $arr[$i] = $employee->gender; $i++;
                          $arr[$i] = $employee->education; $i++;
                          $arr[$i] = $employee->course; $i++;
                          $arr[$i] = $employee->currentlocation; $i++;
                          $arr[$i] = $employee->hobbiesinterest; $i++;
                          $arr[$i] = $employee->commuteTime; $i++;


                          $qCounter=1;
                          foreach ($allQuestions as $q) {

                            if($qCounter == 14){
                              $ans = collect($allEmployees)->where('id',$employee->id)->where('question_id',157);

                              if (count($ans) > 0)
                                 $arr[$i]= $ans->first()->essay;
                              else
                                 $arr[$i]= "--";

                              $i++;
                              //$arr[$i]= $employee->essay;
                              //$arr[$i] = collect($allResp)->where('userID',$employee->id)->first()->essay;
                            } else if($qCounter == 15){
                              $ans = collect($allEmployees)->where('id',$employee->id)->where('question_id',158);

                              if (count($ans) > 0)
                                 $arr[$i]= $ans->first()->essay;
                              else
                                 $arr[$i]= "--";

                              $i++;

                            }
                            else
                            {

                              //---- RATING
                              $r = collect($allResp)->where('userID',$employee->id)->where('question',$q->id);
                              if (count($r) > 0)
                                $rating = $r->first()->rating;
                              else
                                $rating = null;

                              $arr[$i]= $rating; $i++;
                              
                              //---- NOTE
                              $n = collect($allNotes)->where('userID',$employee->id)->where('question_id',$q->id);
                              if (count($n)>0) $note = $n->first()->comments;
                              else $note=null;

                              $arr[$i]= $note; $i++;

                            }

                            $qCounter++;

                          }//end foreach questions



                            $sheet->appendRow($arr);

                        }//end foreach employee


                        
                     });//end sheet1



              })->export('xls');

              return "Download";

          } else return view('access-denied');

          

        }break;
        
        default: return view('access-denied');
          # code...
          break;
      }


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

    public function getPrevious(Request $request)
    {
      if($request->anEssay)
      {
        $previousResponse = DB::table('survey_essays')->where('user_id',$this->user->id)->where('question_id',$request->questionid)->get();

      }else 
        $previousResponse = DB::table('survey_responses')->where('user_id',$this->user->id)->where('question_id',$request->questionid)->get();

      if (count($previousResponse) > 0)
      {
        //check mo kung may notes sya dun
        if($request->anEssay)
        {
          return response()->json(['hasResponse'=>1,'hasNotes'=>1, 'previousResponse'=>$previousResponse[0],'notes'=>$previousResponse[0]]);

        }
        else
        {
          $previousNotes = DB::table('survey_notes')->where('user_id',$this->user->id)->where('question_id',$request->questionid)->get();
          if (count($previousNotes) > 0)
          {
            return response()->json(['hasResponse'=>1,'hasNotes'=>1, 'previousResponse'=>$previousResponse[0],'notes'=>$previousNotes[0]]);

          }
          else
          {
            return response()->json(['hasResponse'=>1,'hasNotes'=>0, 'previousResponse'=>$previousResponse[0],'notes'=>$previousNotes]);

          }

        }
        
      }else
      return response()->json(['hasResponse'=>0,'hasNotes'=>0, 'previousResponse'=>$previousResponse, 'notes'=>null]);

    }

    public function intro($id)
    {
      //check mo kung done na sya sa survey
      $survey = Survey::find($id);
      $intro = Survey_Intro::where('survey_id',$survey->id)->first();
      $doneNa = DB::table('survey_user')->where('user_id',$this->user->id)->where('survey_id',$id)->where('isDone',1)->get();

      if (count($doneNa) > 0)
        return redirect()->action('SurveyController@show',$id);
      else
        return view('forms.survey-intro',compact('survey','intro'));

    }

    public function participants($id)
    {
        $type = Input::get('type');

        if (empty($type)) return view('empty');

        $testgroup = [564,508,1644,1611,1784,1786,491, 471, 367,1,184,344,3835];
        $keyGroup = [564,1611,1784,1,184,344,491,3835];
        //(in_array($this->user->id, $testgroup)) ? $canAccess=true : $canAccess=false;
        (in_array($this->user->id, $keyGroup)) ? $canViewAll=true : $canViewAll=false;

        if (!$canViewAll) return view('access-denied');
        else
            {   DB::connection()->disableQueryLog(); 
                $npsData = new Collection;

                $survey = Survey::find($id);

                $allResp = DB::table('survey_user')->where('survey_user.survey_id',$id)->
                              join('survey_responses','survey_responses.user_id','=','survey_user.user_id')->
                              where('survey_responses.survey_id',$id)->
                              where('survey_user.isDone',1)->
                              join('survey_extradata','survey_extradata.user_id','=','survey_user.user_id')->
                              where('survey_extradata.survey_id',$id)->
                              join('users','users.id','=','survey_user.user_id')->
                              join('team','team.user_id','=','survey_user.user_id')->
                              join('campaign','team.campaign_id','=','campaign.id')->
                              join('positions','users.position_id','=','positions.id')->
                              join('survey_questions_category','survey_questions_category.survey_questionID','=','survey_responses.question_id')->
                              join('categoryTags','categoryTags.id','=','survey_questions_category.categoryTag_id')->
                              select('survey_user.user_id as userID','survey_user.survey_id as surveyID','users.firstname','users.lastname' ,'survey_responses.question_id as question','survey_questions_category.categoryTag_id as categoryID','categoryTags.label as categoryLabel','survey_responses.survey_optionsID as rating','survey_extradata.beEEC','survey_extradata.forGD', 'campaign.name as program','campaign.id as programID','campaign.isBackoffice as backOffice','team.floor_id','positions.name as jobTitle')->get();
                

                
                if($id == 6)
                  $nspResponses = collect($allResp)->whereIn('question',[156]);
                else
                  $nspResponses = collect($allResp)->whereIn('question',[13,15,44,45,49]);

                $groupedNPS = collect($nspResponses)->groupBy('userID'); 

                //****** ALL NSP DATA

                  $eeCommittee = 0;
                  $forGD = 0;
                     foreach ($groupedNPS as $n) {
                          $nps = number_format(($n->pluck('rating')->sum())/count($n),2);

                          if ($n[0]->beEEC) $eeCommittee++;
                          if ($n[0]->forGD) $forGD++;

                          $l = Campaign::find($n[0]->programID)->logo['filename'];

                          if (empty($l)) $logo = "white_logo_small.png";
                          else $logo = $l;

                          if  ( file_exists('public/img/employees/'.$n[0]->userID.'.jpg') )
                            $pic = asset('public/img/employees/'.$n[0]->userID.'.jpg');
                          else
                            $pic = asset('public/img/useravatar.png');


                          $npsData->push(['respondentID'=>$n[0]->userID,'pic'=>$pic, 'programID'=>$n[0]->programID, 'program'=>$n[0]->program,'logo'=>$logo, 'respondent'=>$n[0]->lastname." , ". $n[0]->firstname,'jobTitle'=>$n[0]->jobTitle,  'nps'=>$nps,'roundedNPS'=>(string)round($nps),'eeCommittee'=>$n[0]->beEEC, 'forGD'=>$n[0]->forGD, 'backOffice'=> ($n[0]->backOffice==1) ? 1:0 ]);

                      }

                  $promoters = collect($npsData)->whereIn('roundedNPS',['4','5'])->where('eeCommittee',1)->sortBy('program')->groupBy('program');
                  //$passives = collect($npsData)->whereIn('roundedNPS',['3']);
                  $detractors = collect($npsData)->whereIn('roundedNPS',['1','2'])->where('forGD',1)->sortBy('program')->groupBy('program');

                 
                
                switch ($type) {
                    case '1':
                    {
                        $participants = $promoters;
                        $activity = "Interested to be part of <strong>Employee Engagement Committee</strong>";
                        
                        return view('forms.survey-participants',compact('participants','survey','activity','type'));
                        // response()->json(['promoters'=>$promoters, 'total'=> count($promoters)]);
                    }
                        break;

                    case '2':
                    {
                        $participants = $detractors;
                        $activity = "Interested to join a <strong>Group Discussion</strong>.";
                        return view('forms.survey-participants',compact('participants','survey','activity','type'));
                    }
                        break;
                    
                    default: return view('empty');
                        break;
                }
            }
    }

    public function report($id)
    {
      $survey = Survey::find($id);

      DB::connection()->disableQueryLog(); 

      $surveyData = new Collection;
      $npsData = new Collection;
      $programData = new Collection;
      $categoryData = new Collection;
      $ngayon = Carbon::now('GMT+8');
      $tenure3mos = Carbon::now('GMT+8')->addMonths(-3);



      switch ($id) {
        case 1: //EES 2019
                {

                  $allResp = DB::table('survey_questions')->where('survey_questions.survey_id',$id)->
                    join('survey_responses','survey_responses.question_id','=','survey_questions.id')->

                    join('survey_user','survey_user.user_id','=','survey_responses.user_id')->
                    join('survey_extradata','survey_extradata.user_id','=','survey_responses.user_id')->
                    join('users','users.id','=','survey_user.user_id')->
                    leftJoin('survey_essays','survey_essays.user_id','=','users.id')->
                    join('team','team.user_id','=','survey_user.user_id')->
                    join('campaign','team.campaign_id','=','campaign.id')->
                    join('survey_questions_category','survey_questions_category.survey_questionID','=','survey_responses.question_id')->
                    join('categoryTags','categoryTags.id','=','survey_questions_category.categoryTag_id')->

                    //join('campaign_logos','campaign_logos.campaign_id','=','campaign.id')->
                    select('survey_responses.user_id as userID','users.firstname','users.lastname' ,'survey_questions_category.categoryTag_id as categoryID','categoryTags.label as categoryLabel', 'survey_responses.question_id as question', 'survey_responses.survey_optionsID as rating','survey_essays.answer as essay', 'survey_extradata.beEEC','survey_extradata.forGD', 'campaign.name as program','campaign.id as programID','campaign.isBackoffice as backOffice','team.floor_id')->
                    where('survey_user.isDone',1)->
                    where('team.floor_id','!=',10)->
                    where('team.floor_id','!=',11)->
                    where([
                    ['users.status_id', '!=', 6],
                    ['users.status_id', '!=', 13],
                    ['users.status_id', '!=', 16],
                    ['users.status_id', '!=', 7],
                    ['users.status_id', '!=', 8],
                    ['users.status_id', '!=', 9],
                            ])->get();
                  $nspResponses = collect($allResp)->whereIn('question',[13,15,44,45,49]);
                 
                  $groupedResp = collect($allResp)->sortBy('lastname')->groupBy('userID');
                  $groupedNPS = collect($nspResponses)->groupBy('userID'); 
                  $groupedCat = collect($allResp)->groupBy('categoryID');

                  //return $groupedResp->take(100);

                  //****** ALL SURVEY DATA
                  foreach ($groupedResp as $key) 
                  {
                    $avg = number_format(collect($key)->pluck('rating')->avg(),2);
                    $surveyData->push(['respondentID'=>$key[0]->userID,'program'=>$key[0]->program,'programID'=>$key[0]->programID,'respondent'=>$key[0]->lastname." , ". $key[0]->firstname, 'rating'=>$avg, 'rounded'=>(string)round($avg), 'backOffice'=> ($key[0]->backOffice==1) ? 1:0 ]);
                    
                  }

                  $totalBackoffice = count(collect($surveyData)->where('backOffice',1));
                  $totalOps = count(collect($surveyData)->where('backOffice',0));
                  
                  $groupedRatings = collect($surveyData)->groupBy('rounded');

                  $programs = collect($surveyData)->sortBy('program')->groupBy('program'); 


                  //****** ALL NSP DATA

                  $eeCommittee = 0;
                  $forGD = 0;
                     foreach ($groupedNPS as $n) {
                          $nps = number_format(($n->pluck('rating')->sum())/count($n),2);

                          if ($n[0]->beEEC) $eeCommittee++;
                          if ($n[0]->forGD) $forGD++;
                          $npsData->push(['respondentID'=>$n[0]->userID,'program'=>$n[0]->program, 'respondent'=>$n[0]->lastname." , ". $n[0]->firstname, 'nps'=>$nps,'roundedNPS'=>(string)round($nps),'eeCommittee'=>$n[0]->beEEC, 'forGD'=>$n[0]->forGD, 'backOffice'=> ($n[0]->backOffice==1) ? 1:0 ]);

                      }

                  $promoters = collect($npsData)->whereIn('roundedNPS',['4','5']);
                  $passives = collect($npsData)->whereIn('roundedNPS',['3']);
                  $detractors = collect($npsData)->whereIn('roundedNPS',['1','2']);
                  $participants = ['eeCommittee'=>$eeCommittee,'totalPromoters'=> count($promoters),'eePercent'=>number_format($eeCommittee/count($promoters)*100,2), 'forGD'=>$forGD, 'totalDetractors'=>count($detractors), 'gdPercent'=> number_format($forGD/count($detractors)*100,2)];

                  $eNPS = round((count($promoters)/count($surveyData))*100) - round((count($detractors)/count($surveyData))*100);
                 

                  //****** ALL CAMPAIGN RELATED DATA
                  foreach ($programs->sort() as $p) {
                      $totalData = DB::table('team')->where('campaign_id',$p[0]['programID'])->
                                    join('users','users.id','=','team.user_id')->
                                    select('users.status_id','users.firstname','users.lastname','users.id')->
                                    where('users.status_id',"!=",7)->
                                    where('users.status_id',"!=",8)->
                                    where('users.status_id',"!=",9)->
                                    where('users.status_id',"!=",13)->
                                    where('team.floor_id','!=',10)->
                                    where('team.floor_id','!=',11)->get();
                      $total = count($totalData); //count(Team::where('campaign_id',$p[0]['programID'])->get());
                      $l = Campaign::find($p[0]['programID'])->logo['filename'];

                      if (empty($l)) $logo = "white_logo_small.png";
                      else $logo = $l;

                      $progAve = round(number_format($surveyData->where('programID',$p[0]['programID'])->pluck('rating')->avg(),1));

                      $programData->push(['id'=>$p[0]['programID'], 'name'=>$p[0]['program'],'respondents'=>count($p),'totalData'=>$totalData,  'total'=>$total, 'aveRating'=>$progAve, 'logo'=>$logo]);
                  }

                  //return collect($programData)->sortBy('name');



                  //****** ALL SUBMITTED ESSAYS
                  // the last question
                  $allEssays = DB::table('survey_questions')->where('survey_questions.id',52)->
                                    join('survey_essays','survey_essays.question_id','=','survey_questions.id')->
                                    join('users','survey_essays.user_id','=','users.id')->
                                    join('team','team.user_id','=','users.id')->
                                    join('campaign','team.campaign_id','=','campaign.id')->
                                    select('users.id','users.firstname','users.lastname','campaign.name as program','users.dateHired', 'survey_essays.answer','survey_essays.created_at')->orderBy('survey_essays.created_at','DESC')->get();

                  $groupedEssays = collect($allEssays)->sortBy('program')->groupBy('program');

                  $eq = DB::table('survey_questions')->where('responseType',2)->get();
                  (count($eq)>0) ? $essayQ = $eq[0] : $essayQ = null;

                  


                  //****** ALL CATEGORY RELATED DATA
                  foreach ($groupedCat as $key) {

                        //$r = collect($key)->pluck('rating')->
                        $r = number_format(collect($key)->pluck('rating')->avg(),2);
                        $categoryData->push(['categoryID'=>$key[0]->categoryID, 'aveRating'=>$r,'categoryName'=>$key[0]->categoryLabel]);
                      # code...
                  }

                 
                    //exclude Taipei and Xiamen
                    $actives = count(DB::table('users')->where('status_id','!=',7)->
                                    where('status_id','!=',8)->
                                    where('status_id','!=',9)->
                                    where('status_id','!=',13)->
                                    leftJoin('team','team.user_id','=','users.id')->
                                    select('users.id','users.lastname','team.floor_id','team.campaign_id')->
                                    where('team.floor_id','!=',10)->
                                    where('team.floor_id','!=',11)->get());//;return $actives;
                    $percentage = number_format( (  count($surveyData)/ $actives) * 100,2);
                    
                    

                    $asOf = Carbon::now('GMT+8')->format('M d, Y h:i A');


                    if($this->user->id !== 564 ) {
                      $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
                        fwrite($file, "-------------------\n Viewed Survey Report by [". $this->user->id."] ".$this->user->lastname."\n");
                        fclose($file);
                    }

                    //return $programData;

                    //******* show memo for test people only jill,paz,ems,joy,raf,jaja, lothar, inguengan,reese
                    $testgroup = [564,508,1644,1611,1784,1786,491, 471, 367,1,184,344,307];
                    $keyGroup = [564,1611,1784,1,184,344,491];
                    (in_array($this->user->id, $testgroup)) ? $canAccess=true : $canAccess=false;
                    (in_array($this->user->id, $keyGroup)) ? $canViewAll=true : $canViewAll=false;

                    if ($canAccess){
                    

                        return view('forms.survey-reports',compact('survey','participants', 'essayQ','canAccess','canViewAll', 'groupedEssays', 'categoryData', 'surveyData','npsData','groupedRatings','totalOps','totalBackoffice','promoters','passives','detractors','programData','eNPS','actives','percentage','asOf'));

                    }else
                        return view('forms.survey-reports2',compact('survey','participants', 'essayQ','canAccess','canViewAll', 'groupedEssays','categoryData', 'surveyData','npsData','groupedRatings','totalOps','totalBackoffice','promoters','passives','detractors','programData','eNPS','actives','percentage','asOf'));

                }break;

        case 3:  // Yearend party
                {

                  $allResp = DB::table('survey_user')->where('survey_user.survey_id',$id)->
                                  join('survey_responses','survey_responses.survey_userid','=','survey_user.id')->
                                  leftJoin('survey_notes','survey_notes.survey_userid','=','survey_user.id')->
                                  leftJoin('users','users.id','=','survey_responses.user_id')->
                                  leftJoin('team','team.user_id','=','survey_responses.user_id')->
                                  leftJoin('campaign','team.campaign_id','=','campaign.id')->
                                  //join('campaign_logos','campaign_logos.campaign_id','=','campaign.id')->
                                  select('survey_responses.user_id as userID','users.firstname','users.lastname', 'survey_responses.question_id as question', 'survey_responses.survey_optionsID as rating','survey_notes.comments', 'campaign.name as program','campaign.id as programID','campaign.isBackoffice as backOffice','team.floor_id')->
                                  where('survey_user.isDone',1)->
                                  where('team.floor_id','!=',10)->
                                  where('team.floor_id','!=',11)->get();
                                  // 
                                  // 

                  

                  $groupedResp = collect($allResp)->sortBy('lastname')->groupBy('userID');
                  
                  //return $groupedResp->take(100);

                  //****** ALL SURVEY DATA
                  foreach ($groupedResp as $key) 
                  {
                    //$avg = number_format(collect($key)->pluck('rating')->avg(),2);
                    $surveyData->push(['respondentID'=>$key[0]->userID,'program'=>$key[0]->program,'programID'=>$key[0]->programID,'respondent'=>$key[0]->lastname." , ". $key[0]->firstname, 'rating'=>$key[0]->rating, 'backOffice'=> ($key[0]->backOffice==1) ? 1:0 ]);
                    
                  }

                  $totalBackoffice = count(collect($surveyData)->where('backOffice',1));
                  $totalOps = count(collect($surveyData)->where('backOffice',0));
                  
                  $groupedRatings = collect($surveyData)->groupBy('rating');

                  $programs = collect($surveyData)->sortBy('program')->groupBy('program'); 


                 
                  
                 

                  //****** ALL CAMPAIGN RELATED DATA
                  foreach ($programs->sort() as $p) {
                      $totalData = DB::table('team')->where('campaign_id',$p[0]['programID'])->
                                    join('users','users.id','=','team.user_id')->
                                    select('users.status_id','users.firstname','users.lastname','users.id')->
                                    where('users.status_id',"!=",7)->
                                    where('users.status_id',"!=",8)->
                                    where('users.status_id',"!=",9)->
                                    where('users.status_id',"!=",13)->
                                    where('team.floor_id','!=',10)->
                                    where('team.floor_id','!=',11)->get();
                      $total = count($totalData); //count(Team::where('campaign_id',$p[0]['programID'])->get());
                      $l = Campaign::find($p[0]['programID'])->logo['filename'];

                      if (empty($l)) $logo = "white_logo_small.png";
                      else $logo = $l;

                      $progAve = round(number_format($surveyData->where('programID',$p[0]['programID'])->pluck('rating')->avg(),1));

                      $programData->push(['id'=>$p[0]['programID'], 'name'=>$p[0]['program'],'respondents'=>count($p),'totalData'=>$totalData,  'total'=>$total, 'aveRating'=>$progAve, 'logo'=>$logo]);
                  }

                  //return collect($programData)->sortBy('name');



                  //****** ALL SUBMITTED ESSAYS
                  // the last question
                  $allEssays = DB::table('survey_questions')->where('survey_questions.survey_id',$id)->
                                    join('survey_notes','survey_notes.question_id','=','survey_questions.id')->
                                    join('users','survey_notes.user_id','=','users.id')->
                                    join('team','team.user_id','=','users.id')->
                                    join('campaign','team.campaign_id','=','campaign.id')->
                                    select('users.id','users.firstname','users.lastname','campaign.name as program','users.dateHired', 'survey_notes.comments as answer','survey_notes.created_at')->orderBy('survey_notes.created_at','DESC')->get();

                  $groupedEssays = collect($allEssays)->sortBy('program')->groupBy('program');
                  //return $groupedEssays;

                  $essayQ = null;
                  $options = DB::table('survey_options')->where('survey_options.survey_id',$id)->
                         leftJoin('options','options.id','=','survey_options.options_id')->
                         select('survey_options.id', 'options.label','options.value','options.ordering')->
                         orderBy('options.ordering','ASC')->get();  
                         //return collect($options)->where('id',12)->pluck('label');
                 
                    //exclude Taipei and Xiamen
                    $actives = count(DB::table('users')->where('status_id','!=',7)->
                                    where('status_id','!=',8)->
                                    where('status_id','!=',9)->
                                    where('status_id','!=',13)->
                                    leftJoin('team','team.user_id','=','users.id')->
                                    select('users.id','users.lastname','team.floor_id','team.campaign_id')->
                                    where('team.floor_id','!=',10)->
                                    where('team.floor_id','!=',11)->get());//;return $actives;
                    $percentage = number_format( (  count($surveyData)/ $actives) * 100,2);
                    $participants=null;
                    

                    $asOf = Carbon::now('GMT+8')->format('M d, Y h:i A');


                    if($this->user->id !== 564 ) {
                      $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
                        fwrite($file, "-------------------\n Viewed Yearend Survey by [". $this->user->id."] ".$this->user->lastname."\n");
                        fclose($file);
                    }

                    //return $programData;

                    //******* show memo for test people only jill,paz,ems,joy,raf,jaja, lothar, inguengan
                    $testgroup = [564,508,1644,1611,1784,1786,491, 471, 367,1,184,344];
                    $keyGroup = [564,1611,1784,1,184,344,491];
                    (in_array($this->user->id, $testgroup)) ? $canAccess=true : $canAccess=false;
                    (in_array($this->user->id, $keyGroup)) ? $canViewAll=true : $canViewAll=false;

                    //return response()->json(['survey'=>$survey,'participants'=>$participants,'groupedEssays'=>$groupedEssays,'surveyData'=>$surveyData, 'groupedRatings'=>$groupedRatings,'totalOps'=>$totalOps,'totalBackoffice'=>$totalBackoffice,'programData'=>$programData,'percentage'=>$percentage]);

                    //return $groupedRatings;
                    //return response()->json($options[0]->ordering); //->rating;

                    if ($canAccess){
                    

                        return view('forms.survey-reports3',compact('survey','options', 'participants','canAccess','canViewAll', 'groupedEssays', 'surveyData','groupedRatings','totalOps','totalBackoffice','programData','actives','percentage','asOf'));

                    }else
                        return view('forms.survey-reports3',compact('survey','options', 'participants','canAccess','canViewAll', 'groupedEssays', 'surveyData','groupedRatings','totalOps','totalBackoffice','programData','actives','percentage','asOf'));

                }break;
        
        case 4:  // Performers 
                {

                  $allResp = DB::table('survey_user')->where('survey_user.survey_id',$id)->
                                  join('survey_responses','survey_responses.survey_userid','=','survey_user.id')->
                                  leftJoin('survey_notes','survey_notes.survey_userid','=','survey_user.id')->
                                  leftJoin('users','users.id','=','survey_responses.user_id')->
                                  leftJoin('team','team.user_id','=','survey_responses.user_id')->
                                  leftJoin('campaign','team.campaign_id','=','campaign.id')->
                                  //join('campaign_logos','campaign_logos.campaign_id','=','campaign.id')->
                                  select('survey_responses.user_id as userID','users.firstname','users.lastname', 'survey_responses.question_id as question', 'survey_responses.survey_optionsID as rating','survey_notes.comments', 'campaign.name as program','campaign.id as programID','campaign.isBackoffice as backOffice','team.floor_id')->
                                  where('survey_user.isDone',1)->
                                  where('team.floor_id','!=',10)->
                                  where('team.floor_id','!=',11)->get();
                                  // 
                                  // 

                  

                  $groupedResp = collect($allResp)->sortBy('lastname')->groupBy('userID');
                  
                  //return $groupedResp->take(100);

                  //****** ALL SURVEY DATA
                  foreach ($groupedResp as $key) 
                  {
                    //$avg = number_format(collect($key)->pluck('rating')->avg(),2);
                    $surveyData->push(['respondentID'=>$key[0]->userID,'program'=>$key[0]->program,'programID'=>$key[0]->programID,'respondent'=>$key[0]->lastname." , ". $key[0]->firstname, 'rating'=>$key[0]->rating, 'backOffice'=> ($key[0]->backOffice==1) ? 1:0 ]);
                    
                  }

                  $totalBackoffice = count(collect($surveyData)->where('backOffice',1));
                  $totalOps = count(collect($surveyData)->where('backOffice',0));
                  
                  $groupedRatings = collect($surveyData)->groupBy('rating');

                  $programs = collect($surveyData)->sortBy('program')->groupBy('program'); 


                 
                  
                 

                  //****** ALL CAMPAIGN RELATED DATA
                  foreach ($programs->sort() as $p) {
                      $totalData = DB::table('team')->where('campaign_id',$p[0]['programID'])->
                                    join('users','users.id','=','team.user_id')->
                                    select('users.status_id','users.firstname','users.lastname','users.id')->
                                    where('users.status_id',"!=",7)->
                                    where('users.status_id',"!=",8)->
                                    where('users.status_id',"!=",9)->
                                    where('users.status_id',"!=",13)->
                                    where('team.floor_id','!=',10)->
                                    where('team.floor_id','!=',11)->get();
                      $total = count($totalData); //count(Team::where('campaign_id',$p[0]['programID'])->get());
                      $l = Campaign::find($p[0]['programID'])->logo['filename'];

                      if (empty($l)) $logo = "white_logo_small.png";
                      else $logo = $l;

                      $progAve = round(number_format($surveyData->where('programID',$p[0]['programID'])->pluck('rating')->avg(),1));

                      $programData->push(['id'=>$p[0]['programID'], 'name'=>$p[0]['program'],'respondents'=>count($p),'totalData'=>$totalData,  'total'=>$total, 'aveRating'=>$progAve, 'logo'=>$logo]);
                  }

                  //return collect($programData)->sortBy('name');



                  //****** ALL SUBMITTED ESSAYS
                  // the last question
                  $allEssays = DB::table('survey_questions')->where('survey_questions.survey_id',$id)->
                                    join('survey_notes','survey_notes.question_id','=','survey_questions.id')->
                                    join('users','survey_notes.user_id','=','users.id')->
                                    join('team','team.user_id','=','users.id')->
                                    join('campaign','team.campaign_id','=','campaign.id')->
                                    select('users.id','users.firstname','users.lastname','campaign.name as program','users.dateHired', 'survey_notes.comments as answer','survey_notes.created_at')->orderBy('survey_notes.created_at','DESC')->get();

                  $groupedEssays = collect($allEssays)->sortBy('program')->groupBy('program');
                  //return $groupedEssays;

                  $essayQ = null;
                  $options = DB::table('survey_options')->where('survey_options.survey_id',$id)->
                         leftJoin('options','options.id','=','survey_options.options_id')->
                         select('survey_options.id', 'options.label','options.value','options.ordering')->
                         orderBy('options.ordering','ASC')->get();  
                         //return collect($options)->where('id',12)->pluck('label');
                 
                    //exclude Taipei and Xiamen
                    $actives = count(DB::table('users')->where('status_id','!=',7)->
                                    where('status_id','!=',8)->
                                    where('status_id','!=',9)->
                                    where('status_id','!=',13)->
                                    leftJoin('team','team.user_id','=','users.id')->
                                    select('users.id','users.lastname','team.floor_id','team.campaign_id')->
                                    where('team.floor_id','!=',10)->
                                    where('team.floor_id','!=',11)->get());//;return $actives;
                    $percentage = number_format( (  count($surveyData)/ $actives) * 100,2);
                    $participants=null;
                    

                    $asOf = Carbon::now('GMT+8')->format('M d, Y h:i A');


                    if($this->user->id !== 564 ) {
                      $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
                        fwrite($file, "-------------------\n Viewed Performers Result by [". $this->user->id."] ".$this->user->lastname."\n");
                        fclose($file);
                    }

                    //return $programData;

                    //******* show memo for test people only jill,paz,ems,joy,raf,jaja, lothar, inguengan
                    $testgroup = [564,508,1644,1611,1784,1786,491, 471, 367,1,184,344];
                    $keyGroup = [564,1611,1784,1,184,344,491];
                    (in_array($this->user->id, $testgroup)) ? $canAccess=true : $canAccess=false;
                    (in_array($this->user->id, $keyGroup)) ? $canViewAll=true : $canViewAll=false;

                    //return response()->json(['survey'=>$survey,'participants'=>$participants,'groupedEssays'=>$groupedEssays,'surveyData'=>$surveyData, 'groupedRatings'=>$groupedRatings,'totalOps'=>$totalOps,'totalBackoffice'=>$totalBackoffice,'programData'=>$programData,'percentage'=>$percentage]);

                    //return $groupedRatings;
                    //return response()->json($options[0]->ordering); //->rating;

                    if ($canAccess){
                    

                        return view('forms.survey-reports3',compact('survey','options', 'participants','canAccess','canViewAll', 'groupedEssays', 'surveyData','groupedRatings','totalOps','totalBackoffice','programData','actives','percentage','asOf'));

                    }else
                        return view('forms.survey-reports3',compact('survey','options', 'participants','canAccess','canViewAll', 'groupedEssays', 'surveyData','groupedRatings','totalOps','totalBackoffice','programData','actives','percentage','asOf'));

                }break;
        

        case 6: //EES 2020
                {

                  $allResp = DB::table('survey_questions')->where('survey_questions.survey_id',$id)->
                    join('survey_responses','survey_responses.question_id','=','survey_questions.id')->

                    join('survey_user','survey_user.user_id','=','survey_responses.user_id')->
                    join('survey_extradata','survey_extradata.user_id','=','survey_responses.user_id')->
                    join('users','users.id','=','survey_user.user_id')->
                    where('users.dateHired','<=',$tenure3mos->format('Y-m-d H:i:s'))->
                    leftJoin('survey_essays','survey_essays.user_id','=','users.id')->
                    join('team','team.user_id','=','survey_user.user_id')->
                    where('team.floor_id','!=',10)->
                    where('team.floor_id','!=',11)->
                    join('campaign','team.campaign_id','=','campaign.id')->
                    join('survey_questions_category','survey_questions_category.survey_questionID','=','survey_responses.question_id')->
                    join('categoryTags','categoryTags.id','=','survey_questions_category.categoryTag_id')->

                    //join('campaign_logos','campaign_logos.campaign_id','=','campaign.id')->
                    select('survey_responses.user_id as userID','users.dateHired', 'users.firstname','users.lastname' ,'survey_questions.survey_id as surveyID', 'survey_questions_category.categoryTag_id as categoryID','categoryTags.label as categoryLabel', 'survey_responses.question_id as question', 'survey_responses.survey_optionsID as rating','survey_essays.answer as essay', 'survey_extradata.beEEC','survey_extradata.forGD', 'campaign.name as program','campaign.id as programID','campaign.isBackoffice as backOffice','team.floor_id')->
                    where('survey_user.isDone',1)->
                    
                    where('survey_essays.survey_id',$id)->
                    where([
                    ['users.status_id', '!=', 2],
                    ['users.status_id', '!=', 6],
                    ['users.status_id', '!=', 7],
                    ['users.status_id', '!=', 8],
                    ['users.status_id', '!=', 9],
                    ['users.status_id', '!=', 13],
                    ['users.status_id', '!=', 16],
                    
                    
                            ])->get(); 
                  $nspResponses = collect($allResp)->whereIn('question',[156]);
                 
                  $groupedResp = collect($allResp)->sortBy('lastname')->groupBy('userID');
                  $groupedNPS = collect($nspResponses)->groupBy('userID'); 
                  $groupedCat = collect($allResp)->groupBy('categoryID');

                  //$completed = count(Survey_User::where('isDone',true)->where('survey_id',$id)->get());
                  $completed = count(DB::table('survey_user')->where('survey_user.isDone',true)->where('survey_user.survey_id',$id)->
                                join('users','survey_user.user_id','=','users.id')->
                                select('users.id','users.dateHired')->where('users.dateHired','<=',$tenure3mos->format('Y-m-d H:i:s'))->get());
                                
                  // $finished = DB::table('survey_user')->where('survey_user.survey_id',$id)->where('survey_user.isDone',true)->
                  //                 join('users','users.id','=','survey_user.user_id')->
                  //                 join()get());


                  //return $groupedResp->take(100);

                  //****** ALL SURVEY DATA
                  foreach ($groupedResp as $key) 
                  {
                    $tenured = Carbon::now('GMT+8')->diffInMonths(Carbon::parse($key[0]->dateHired));
                    $avg = number_format(collect($key)->pluck('rating')->avg(),2);

                    if ($key[0]->floor_id !== 10 && $key[0]->floor_id !== 11 )
                    $surveyData->push(['respondentID'=>$key[0]->userID,'program'=>$key[0]->program,'programID'=>$key[0]->programID,'tenure'=>$tenured, 'dateHired'=>$key[0]->dateHired, 'respondent'=>$key[0]->lastname." , ". $key[0]->firstname, 'rating'=>$avg, 'rounded'=>(string)round($avg), 'backOffice'=> ($key[0]->backOffice==1) ? 1:0 ]);
                    
                  }

                  //if (count($completed) == 0 ) return view('empty');

                  //$t1 = collect($surveyData)->where('tenure','<',43);
                  $t1 = collect($surveyData)->filter(function ($item) {
                            return $item['tenure'] <= 11;
                        });
                  $t2 = collect($surveyData)->filter(function ($item) {
                            return ($item['tenure'] >= 12) && ($item['tenure'] <= 36) ;
                        });

                  $t3 = collect($surveyData)->filter(function ($item) {
                            return ($item['tenure'] >= 37) && ($item['tenure'] <= 60) ;
                        });

                  $t4 = collect($surveyData)->filter(function ($item) {
                            return ($item['tenure'] >= 61) ;
                        });
                  

                  $ave1 = number_format($t1->pluck('rating')->avg(),3);
                  $ave2 = number_format($t2->pluck('rating')->avg(),3);
                  $ave3 = number_format($t3->pluck('rating')->avg(),3);
                  $ave4 = number_format($t4->pluck('rating')->avg(),3);

                 

                  $tenureCat = new Collection;
                  $tenureCat->push(["desc"=> "< a year", 'rating'=>$ave1]);
                  $tenureCat->push(["desc"=> "1-3 years", 'rating'=>$ave2]);
                  $tenureCat->push(["desc"=> "3.1 to 5 years", 'rating'=>$ave3]);
                  $tenureCat->push(["desc"=> "5++ years", 'rating'=>$ave4]);
                  

                  //return $surveyData;

                  $totalBackoffice = count(collect($surveyData)->where('backOffice',1));
                  $totalOps = count(collect($surveyData)->where('backOffice',0));
                  
                  $groupedRatings = collect($surveyData)->groupBy('rounded');

                  $programs = collect($surveyData)->sortBy('program')->groupBy('program'); 


                  //****** ALL NSP DATA

                  $eeCommittee = 0;
                  $forGD = 0;
                     foreach ($groupedNPS as $n) {
                          $nps = number_format(($n->pluck('rating')->sum())/count($n),2);

                          if ($n[0]->beEEC && ($n[0]->rating !== 3) ) $eeCommittee++;
                          if ($n[0]->forGD) $forGD++;
                          $npsData->push(['respondentID'=>$n[0]->userID,'program'=>$n[0]->program, 'respondent'=>$n[0]->lastname." , ". $n[0]->firstname, 'nps'=>$nps,'roundedNPS'=>(string)round($nps),'eeCommittee'=>$n[0]->beEEC, 'forGD'=>$n[0]->forGD, 'backOffice'=> ($n[0]->backOffice==1) ? 1:0 ]);

                      }

                  $promoters = collect($npsData)->whereIn('roundedNPS',['4','5']);
                  $passives = collect($npsData)->whereIn('roundedNPS',['3']);
                  $detractors = collect($npsData)->whereIn('roundedNPS',['1','2']);

                  ( count($promoters) > 0) ? $eePercent = number_format($eeCommittee/count($promoters)*100,2) : $eePercent=0;
                  ( count($detractors) > 0 ) ? $gdP =  number_format($forGD/count($detractors)*100,2) : $gdP=0;


                  $participants = ['eeCommittee'=>$eeCommittee,'groupedNPS'=>$groupedNPS, 'totalPromoters'=> count($promoters),'eePercent'=>$eePercent, 'forGD'=>$forGD, 'totalDetractors'=>count($detractors), 'gdPercent'=>$gdP];

                  if (count($surveyData) > 0)
                  {
                    $eNPS = round((count($promoters)/count($surveyData))*100) - round((count($detractors)/count($surveyData))*100);
                  }
                  else $eNPS=0;

                  //return response()->json(['participants'=>$participants, 'eNPS'=>$eNPS, 'promoters'=>$promoters, 'surveyData'=>$surveyData, 'detractors'=>$detractors]);
                 

                  //****** ALL CAMPAIGN RELATED DATA
                  foreach ($programs->sort() as $p) {
                      $totalData = DB::table('team')->where('campaign_id',$p[0]['programID'])->
                                    join('users','users.id','=','team.user_id')->
                                    where('users.dateHired','<=',$tenure3mos->format('Y-m-d H:i:s'))->
                                    select('users.status_id','users.firstname','users.lastname','users.id')->
                                    where('users.status_id',"!=",2)->
                                    where('users.status_id',"!=",6)->
                                    where('users.status_id',"!=",7)->
                                    where('users.status_id',"!=",8)->
                                    where('users.status_id',"!=",9)->
                                    where('users.status_id',"!=",13)->
                                    where('users.status_id',"!=",16)->
                                    where('team.floor_id','!=',10)->
                                    where('team.floor_id','!=',11)->get();
                      $total = count($totalData); //count(Team::where('campaign_id',$p[0]['programID'])->get());
                      $l = Campaign::find($p[0]['programID'])->logo['filename'];

                      if (empty($l)) $logo = "white_logo_small.png";
                      else $logo = $l;

                      $progAve = round(number_format($surveyData->where('programID',$p[0]['programID'])->pluck('rating')->avg(),1));

                      $programData->push(['id'=>$p[0]['programID'], 'name'=>$p[0]['program'],'respondents'=>count($p),'totalData'=>$totalData,  'total'=>$total, 'aveRating'=>$progAve, 'logo'=>$logo]);
                  }

                  //return collect($programData)->sortBy('name');



                  //****** ALL SUBMITTED ESSAYS
                  // the last question
                  $allEssays =  DB::table('survey_essays')->where('survey_essays.survey_id', $id)->
                  //DB::table('survey_questions')->where('survey_questions.id',158)->
                  //                  join('survey_essays','survey_essays.question_id','=','survey_questions.id')->
                                    join('users','survey_essays.user_id','=','users.id')->
                                    where('users.dateHired','<=',$tenure3mos->format('Y-m-d H:i:s'))->
                                    join('team','team.user_id','=','users.id')->
                                    join('campaign','team.campaign_id','=','campaign.id')->
                                    join('survey_questions','survey_questions.id','=','survey_essays.question_id')->
                                    select('users.id','users.firstname','users.lastname','campaign.name as program','campaign.id as programID', 'users.dateHired', 'survey_essays.answer','survey_essays.created_at','survey_questions.id as questionID','survey_questions.value as theQ')->orderBy('survey_essays.created_at','DESC')->get();

                  $groupedEssays = collect($allEssays)->sortBy('program')->groupBy('program');



                  $eq = DB::table('survey_questions')->where('survey_id',$id)->where('responseType',2)->get();
                  (count($eq)>0) ? $essayQ = $eq : $essayQ = null;

                 
                  


                  //****** ALL CATEGORY RELATED DATA
                  foreach ($groupedCat as $key) {

                        //$r = collect($key)->pluck('rating')->
                        $r = number_format(collect($key)->pluck('rating')->avg(),2);
                        $categoryData->push(['categoryID'=>$key[0]->categoryID, 'aveRating'=>$r,'categoryName'=>$key[0]->categoryLabel]);
                      # code...
                  }

                 
                    //exclude Taipei and Xiamen
                    $actives = count(DB::table('users')->where('status_id','!=',2)->
                                    where('users.dateHired','<=',$tenure3mos->format('Y-m-d H:i:s'))->
                                    where('status_id','!=',6)->
                                    where('status_id','!=',7)->
                                    where('status_id','!=',8)->
                                    where('status_id','!=',9)->
                                    where('status_id','!=',13)->
                                    where('status_id','!=',16)->
                                    leftJoin('team','team.user_id','=','users.id')->
                                    select('users.id','users.lastname','team.floor_id','team.campaign_id')->
                                    where('team.floor_id','!=',10)->
                                    where('team.floor_id','!=',11)->get());//;return $actives;
                    $percentage = number_format( (  count($surveyData)/ $actives) * 100,2);
                    
                    

                    $asOf = Carbon::now('GMT+8')->format('M d, Y h:i A');


                    if($this->user->id !== 564 ) {
                      $file = fopen('public/build/rewards.txt', 'a') or die("Unable to open logs");
                        fwrite($file, "-------------------\n Viewed Survey[".$id."] by [". $this->user->id."] ".$this->user->lastname."\n");
                        fclose($file);
                    }

                    //return $programData;

                    //******* show memo for test people only 
                    $testgroup = [564,508,1644,1611,1784,491,1,184,307,2502,887,163,3085,3835];
                    $keyGroup =  [564,508,1644,1611,1784,491,1,184,3835];

                    
                    (in_array($this->user->id, $testgroup)) ? $canAccess=true : $canAccess=false;
                    (in_array($this->user->id, $keyGroup)) ? $canViewAll=true : $canViewAll=false;

                    $file = fopen('public/build/rewards.txt', 'a') or die("Unable to open logs");
                    fwrite($file, "-------------------\n Report survey[".$id."] on ".Carbon::now('GMT+8')->format('Y-m-d H:i')." by [". \Auth::user()->id."] ".\Auth::user()->lastname."\n");
                    fclose($file);

                    if ($canAccess){
                    

                        return view('forms.survey-reports_2020',compact('survey','participants', 'essayQ','canAccess','canViewAll', 'groupedEssays', 'categoryData', 'surveyData','npsData','groupedRatings','totalOps','totalBackoffice','promoters','passives','detractors','programData','eNPS','actives','percentage','asOf','completed','tenureCat','allEssays'));

                    }else
                        return view('forms.survey-reports2',compact('survey','participants', 'essayQ','canAccess','canViewAll', 'groupedEssays','categoryData', 'surveyData','npsData','groupedRatings','totalOps','totalBackoffice','promoters','passives','detractors','programData','eNPS','actives','percentage','asOf','allEssays'));

                }break;
        
        default: return view('under-construction');
          # code...
          break;
      }


      
      

    }

    public function saveItem(Request $request)
    {
        $latestSurvey = Survey::orderBy('created_at','DESC')->get();

        if ($request->survey_optionsid == 'e'){
            // essay

            //check mo muna kung may existing na
            $meronNaEssay = DB::table('survey_essays')->where('user_id',$this->user->id)->where('question_id',$request->questionid)->get();
            if (count($meronNaEssay) > 0){
              DB::table('survey_essays')->where('user_id',$this->user->id)->where('question_id',$request->questionid)->delete();
            }

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

            return response()->json(['item'=>$item,'existing'=>null,'meronNaEssay'=>$meronNaEssay]);


        }else if ($request->survey_optionsid == 'x'){

            //extra daata
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

        else
        {

            //CHECK first kung for 360
            if ($request->surveytype == "360"){

              if($request->optiontype == 'submit'){

                  goto SkipPart;
                

              }else
              {
                $hasexisting = Survey_Response::where('survey_userid',$request->survey_userid)->
                                              where('optionType',$request->optiontype)->
                                              where('question_id',$request->questionid)->get();
                                              //return response()->json(['survey_userid'=>$request->survey_userid,
                                              //  'optiontype'=>$request->optiontype, 'existing'=>$hasexisting]);
                if (count($hasexisting) > 0){
                  Survey_Response::where('survey_userid',$request->survey_userid)->
                                                where('optionType',$request->optiontype)->
                                                where('question_id',$request->questionid)->delete();

                  $item = new Survey_Response;
                  $item->user_id = $this->user->id;
                  $item->question_id = $request->questionid;
                  $item->survey_optionsID = $request->survey_optionsid;
                  $item->optionType = $request->optiontype;

                } 
                else{

                  $item = new Survey_Response;
                  $item->user_id = $this->user->id;
                  $item->question_id = $request->questionid;
                  $item->survey_optionsID = $request->survey_optionsid;
                  $item->optionType = $request->optiontype;

                }

              }//end submit option

              
                                              

            }else 
            {

              //check mo muna kung may existing response na
              $meronNa = DB::table('survey_responses')->where('user_id',$this->user->id)->where('question_id',$request->questionid)->get();

              if (count($meronNa) > 0)
              {
                //baka may notes na, delete mo muna
                $meronNotes =  DB::table('survey_notes')->where('user_id',$this->user->id)->where('question_id',$request->questionid)->get();
                if (count($meronNotes) > 0) DB::table('survey_notes')->where('user_id',$this->user->id)->where('question_id',$request->questionid)->delete();

                DB::table('survey_responses')->where('user_id',$this->user->id)->where('question_id',$request->questionid)->delete();

              } 

              $item = new Survey_Response;
              $item->user_id = $this->user->id;
              $item->question_id = $request->questionid;
              $so = DB::table('survey_options')->where('id',$request->survey_optionsid)->get();
              $item->survey_optionsID = $so[0]->options_id;

              $hasexisting=null;

            }

            
            
            if (is_null($request->survey_id)){
              $item->survey_id = '6';
            }
            else {
              $item->survey_id = $request->survey_id;

              if ($request->survey_userid == '0')
              {
                //create muna new survey_userID
                $existing = Survey_User::where('user_id',$this->user->id)->
                                         where('survey_id',$request->survey_id)->
                                         where('surveyFor',$request->surveyfor)->get();
                if (count($existing) > 0){

                  $item->survey_userid =  $existing->first()->id;

                }else{
                  $userSurvey = new Survey_User;
                  $userSurvey->user_id = $this->user->id;
                  $userSurvey->survey_id = $request->survey_id;
                  $userSurvey->surveyFor = $request->surveyfor;
                  $userSurvey->startDate = Carbon::now('GMT+8')->format('Y-m-d H:i:s');
                  $userSurvey->lastItem = 1;
                  $userSurvey->isDraft = true;
                  $userSurvey->save();

                  $item->survey_userid =  $userSurvey->id;

                } 
                
              } else $item->survey_userid = $request->survey_userid;
            }

            $item->save();

            SkipPart:
            if ($request->comment !== '' || $request->optiontype == 'submit'){

              if($request->optiontype == 'submit')
              {

                $correct = Carbon::now('GMT+8'); //->timezoneName();

                         if($this->user->id !== 564 ) {
                            $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
                              fwrite($file, "-------------------\n Completed 360 Survey -- " . $correct->format('M d h:i A'). " by [". $this->user->id."] ".$this->user->lastname."\n");
                              fclose($file);
                          } 

                $cmt = new Survey_Notes;
                $cmt->user_id = $this->user->id;
                $cmt->question_id = $request->questionid;
                $cmt->comments = $request->comment;
                $cmt->survey_id = $request->survey_id;



                if ($request->survey_userid == '0')
                {
                  //create muna new survey_userID
                  $existing = Survey_User::where('user_id',$this->user->id)->
                                           where('survey_id',$request->survey_id)->
                                           where('surveyFor',$request->surveyfor)->get();
                  if (count($existing) > 0){

                    $cmt->survey_userid =  $existing->first()->id;

                  }else{
                    $userSurvey = new Survey_User;
                    $userSurvey->user_id = $this->user->id;
                    $userSurvey->survey_id = $request->survey_id;
                    $userSurvey->surveyFor = $request->surveyfor;
                    $userSurvey->startDate = Carbon::now('GMT+8')->format('Y-m-d H:i:s');
                    $userSurvey->lastItem = 144;
                    $userSurvey->isDraft = true;
                    $userSurvey->save();

                    $cmt->survey_userid =  $userSurvey->id;

                  } 
                  
                } else $cmt->survey_userid = $request->survey_userid;

                //update status to DONE
                $currstat = Survey_User::find($cmt->survey_userid);
                $currstat->isDone = true;
                $currstat->isDraft = false;
                $currstat->push();

                
                
              }else
              {

                $cmt = new Survey_Notes;
                $cmt->user_id = $this->user->id;
                $cmt->question_id = $request->questionid;
                $cmt->comments = $request->comment;
                if (is_null($request->survey_id)) {
                  $cmt->survey_id = '1';
                }
                else {
                  $cmt->survey_id = $request->survey_id;
                  $cmt->survey_userid = $request->survey_userid;
                }

              }

              $cmt->save();
              

              return response()->json($cmt);
            }//end if comment not empty



        }
        

        return response()->json(['item'=>$item,'existing'=>$hasexisting]);

    }

    public function saveSurvey(Request $request)
    {
      //update user survey
            $survey = Survey_User::where('user_id',$this->user->id)->where('survey_id',$request->survey_id)->first();
            $survey->isDraft = 0;
            $survey->isDone = true;
            $survey->save();
            return response()->json($survey);


    }

   
    public function show($id)
    {

        // check first kung may saved nang Survey-USer
        // if wala, create one

        DB::connection()->disableQueryLog(); 


        $survey = Survey::find($id);
        $user = $this->user;
        if (empty($survey)) return view('empty');


        //******* show memo for test people only 
        //                jill,paz,ems, joy,jaja ben,henry,reese,bobby,lagran,qhaye,joreen
        $testgroup = [564,508,1644,1611,1784,491,1,184,307,2502,887,163,3085,3835];
        $keyGroup =  [564,508,1644,1611,1784,491,1,184,3835];
        (in_array($this->user->id, $testgroup)) ? $canAccess=true : $canAccess=true;
        (in_array($this->user->id, $keyGroup)) ? $canViewAll=true : $canViewAll=false;

        if ($this->user->status_id == 2) $canAccess=false;

        if (!$canAccess && $id == 6) return view('access-denied');


        $us = Survey_User::where('user_id',$this->user->id)->where('survey_id',$id)->get();

        //return $us;
        

        if (count($us) >= 1 && ($id !== '5') ){
            $userSurvey = $us->first();
            //return $userSurvey;

            if ($userSurvey->isDone) return redirect('/surveyResults/'.$id);

            $extraDataNa=false;

            //now, update his latest submitted response
            $l = DB::table('survey_responses')->where('survey_responses.user_id',$this->user->id)->
                            where('survey_responses.survey_id',$id)->
                            join('survey_options','survey_responses.survey_optionsID','=','survey_options.id')->
                            leftJoin('surveys','survey_options.survey_id','=','surveys.id')->
                            join('survey_questions','survey_responses.question_id','=','survey_questions.id')->
                            select('survey_responses.id','survey_responses.user_id','survey_questions.ordering', 'survey_responses.question_id','survey_responses.survey_optionsID as answer')->
                            orderBy('survey_responses.id','DESC')->get();//  Survey_Response::where('user_id')

            
            if (count($l) > 0) {
                $latest = $l[0];
                

                //now, check kung may answer na sya from essay
                $ess =  DB::table('survey_essays')->where('survey_essays.user_id',$this->user->id)->
                            where('survey_essays.survey_id',$id)->
                            join('survey_questions','survey_essays.question_id','=','survey_questions.id')->
                            select('survey_questions.survey_id','survey_questions.ordering','survey_essays.question_id')->get();

                if (count($ess) > 0){

                     //return response()->json($ess);
                    $startFrom = $ess[0]->ordering;
                    //---- new check
                    if (count($ess) == 2 && $id=='6')
                    {
                      $extraDataNa = 1;
                      $startFrom = $latest->ordering;  
                    }
                    else{
                      $startFrom = $ess[0]->question_id;
                    }

                    //$e = array_pluck($ess,'survey_id');
                    //$extraDataNa = $e;
                    /*if (in_array($id, $e)) //meaning, may essay na nga sya for that survey, check na kung may extradata submitted
                    {
                        if(count($ess) > 1) $extraDataNa = 1;

                    } */

                }else  $startFrom = $latest->ordering; 

                $userSurvey->lastItem = $startFrom;
                $userSurvey->isDraft = true;
                $userSurvey->push();   

            }
            else {
                $latest = null;
                $startFrom = Survey_Question::find($us->first()->lastItem)->ordering;
            }
            
           

        }else {

            // do this only for non 360survey
            if ($id !== '5'){

              $userSurvey = new Survey_User;
              $userSurvey->user_id = $this->user->id;
              $userSurvey->survey_id = $id;
              $userSurvey->startDate = Carbon::now('GMT+8')->format('Y-m-d H:i:s');

              //hanapan mo muna ng Question #1
              $q1 = DB::table('survey_questions')->where('survey_id',$id)->where('ordering',1)->get();
              if(count($q1) > 0)
              {
                $userSurvey->lastItem = $q1[0]->id;

              }else  $userSurvey->lastItem = null;

              // if ($id == 1) $userSurvey->lastItem = 1;
              // else if ($id == 6)  $userSurvey->lastItem = 144;
              // else  $userSurvey->lastItem = null;

              $userSurvey->save();
              $latest=null;
              $startFrom = 1;
              $extraDataNa=false;


            }
            
        }

        //$survey = Survey::find($id);
        //return $e;

       


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
                         select('survey_options.id', 'options.label','options.value','options.ordering','survey_options.options_id as optionID')->
                         orderBy('options.ordering','ASC')->get();

        $extradata = ['travel time to and from office','hobbies and interest'];//

        $totalItems = count($questions);

        $mayEssayna = DB::table('survey_essays')->where('user_id',$this->user->id)->where('survey_id',$id)->get();

        //return response()->json(['startFrom'=>$startFrom, 'totalItems'=>count($questions)]);


        //******* show memo for test people only jill,paz,ems,joy,raf,jaja, lothar, inguengan
                    // $testgroup = [564,508,1644,1611,1784,1786,491, 471, 367,1,184,344];
                    // $keyGroup = [564,1611,1784,1,184,344,491,307];
                    // (in_array($this->user->id, $testgroup)) ? $canAccess=true : $canAccess=false;
                    // (in_array($this->user->id, $keyGroup)) ? $canViewAll=true : $canViewAll=false;

       // return $us;

        switch ($id) {
          case 2: return view('forms.survey-show', compact('id','survey', 'totalItems','questions','startFrom','options','userSurvey','latest','extradata','extraDataNa'));
            break;
          case 3: { 
                    if($this->user->id !== 564 ) {
                      $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
                        fwrite($file, "-------------------\n YrEnd Survey by [". $this->user->id."] ".$this->user->lastname."\n");
                        fclose($file);
                    }

                    return view('forms.survey1page-show', compact('canViewAll', 'id','survey', 'totalItems','questions','startFrom','options','userSurvey','latest','extradata','extraDataNa')); 

                  }
            break;

          case 4: { 
                    //******* 1 page type Survey
                    if($this->user->id !== 564 ) {
                      $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
                        fwrite($file, "-------------------\n Performers Survey by [". $this->user->id."] ".$this->user->lastname."\n");
                        fclose($file);
                    }

                    return view('forms.survey1page-show', compact('canViewAll', 'id','survey', 'totalItems','questions','startFrom','options','userSurvey','latest','extradata','extraDataNa')); 

                  }
            break;

          case 5: { 
                    //******* 360 Survey

                    // check users eligibility on viewing the survey
                    $leadercheck = ImmediateHead::where('employeeNumber',$this->user->employeeNumber)->get();
                    if (count($leadercheck) > 0){

                      $my = DB::table('immediateHead_Campaigns')->where('immediateHead_Campaigns.immediateHead_id',$leadercheck->first()->id)->
                                      join('campaign','immediateHead_Campaigns.campaign_id','=','campaign.id')->
                                      select('immediateHead_Campaigns.tier','campaign.levels')->
                                      get();
                      if ( $my[0]->tier < $my[0]->levels )
                      {
                        // get all reporting under me
                        $allCamp = DB::table('immediateHead_Campaigns')->where('immediateHead_Campaigns.immediateHead_id',$leadercheck->first()->id)->
                                      
                                      join('campaign','immediateHead_Campaigns.campaign_id','=','campaign.id')->
                                      join('team','team.immediateHead_Campaigns_id','=','immediateHead_Campaigns.id')->
                                      join('users','team.user_id','=','users.id')->
                                      //

                                      join('positions','users.position_id','=','positions.id')->
                                      //join('immediateHead','immediateHead.emplo','=','immediateHead.employeeNumber')->
                                      select('users.id as userID', 'users.nickname','users.firstname','users.lastname','campaign.name as program','immediateHead_Campaigns.tier','campaign.levels','positions.name as jobTitle')->
                                      where('users.status_id','!=',7)->
                                      where('users.status_id','!=',8)->
                                      where('users.status_id','!=',9)->
                                      where('users.status_id','!=',13)->
                                      where('users.status_id','!=',16)->
                                      orderBy('users.lastname')->get();

                                      //return $allcamp;

                          $qs = DB::table('surveys')->where('surveys.id',$id)->
                          join('survey_questions','survey_questions.survey_id','=','surveys.id')->
                          join('survey_questions_category','survey_questions_category.survey_questionID','=','survey_questions.id')->
                          join('categoryTags','categoryTags.id','=','survey_questions_category.categoryTag_id')->
                          select('surveys.name','survey_questions.ordering','survey_questions.img','survey_questions.id', 'survey_questions.value as question', 'survey_questions.responseType','categoryTags.label')->

                          orderBy('survey_questions.ordering','ASC')->get();


                        $correct = Carbon::now('GMT+8'); //->timezoneName();

                         if($this->user->id !== 564 ) {
                            $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
                              fwrite($file, "-------------------\n 360 Survey -- " . $correct->format('M d h:i A'). " by [". $this->user->id."] ".$this->user->lastname."\n");
                              fclose($file);
                          } 

                        $importance = collect($options)->whereIn('id',[18,19,20,21,22,23]);
                        $competence = collect($options)->whereIn('id',[24,25,26,27,28,29]);

                        $questions = collect($qs)->groupBy('label');

                        // get all pre existing survey-user submission
                        $surveyUser = DB::table('survey_user')->where('user_id',$this->user->id)->where('survey_id',$id)->
                                        select('id', 'surveyFor','isDone')->get();

                                       
                        $allAnswers = DB::table('survey_user')->where('survey_user.user_id',$this->user->id)->
                                                where('survey_user.survey_id',$id)->
                                                join('survey_responses','survey_user.id','=','survey_responses.survey_userid')->
                                                join('survey_options','survey_responses.survey_optionsID','=','survey_options.id')->
                                                join('options','options.id','=','survey_options.options_id')->
                                                select('survey_responses.question_id','survey_responses.survey_optionsID as answerID','options.value','survey_responses.optionType', 'survey_user.surveyFor','survey_user.user_id as surveyBy','survey_responses.created_at')->
                                                orderBy('survey_responses.created_at','DESC')->
                                                get();

                        $allComments = DB::table('survey_user')->where('survey_user.user_id',$this->user->id)->
                                                where('survey_user.survey_id',$id)->
                                                join('survey_notes','survey_user.id','=','survey_notes.survey_userid')->
                                                
                                                select( 'survey_user.surveyFor','survey_user.user_id as surveyBy','survey_notes.comments')->
                                                get();
                                                

                        (is_null($this->user->nickname)) ? $user = $this->user->firstname : $user = $this->user->nickname;

                        //return $competence;

                        if($this->user->id !== 564 ) {
                          $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
                            fwrite($file, "-------------------\n 360 Survey by [". $this->user->id."] ".$this->user->lastname."\n");
                            fclose($file);
                        }

                        return view('forms.survey360-show', compact('surveyUser', 'user', 'allCamp', 'canViewAll', 'id','survey', 'totalItems','questions','allAnswers','allComments', 'startFrom','options','userSurvey','latest','extradata','extraDataNa','importance','competence')); 


                      }else{ return view('empty');}    

                    }else return view('empty');
                    


                    

                  }
            break;

          case 6: {
                    $file = fopen('public/build/rewards.txt', 'a') or die("Unable to open logs");
                    fwrite($file, "-------------------\n Show survey[".$id."] on ".Carbon::now('GMT+8')->format('Y-m-d H:i')." by [". \Auth::user()->id."] ".\Auth::user()->lastname."\n");
                    fclose($file);
                    return view('forms.survey-shownew', compact('id','survey', 'totalItems','questions','startFrom','options','userSurvey','latest','extradata','extraDataNa','mayEssayna'));

          }break;
          
          default: {
                      //return response()->json(['startFrom'=>$startFrom, 'extraDataNa'=>$extraDataNa]);
                      return view('forms.survey-show', compact('id','survey', 'totalItems','questions','startFrom','options','userSurvey','latest','extradata','extraDataNa'));
                   }
            break;
        }


       


                   
    }

    public function showCategory($id)
    {
        DB::connection()->disableQueryLog(); 
        $category = Categorytag::find($id);

        if (empty($category)) return view('empty');

        //******* show memo for test people only jill,paz,ems,joy,raf,jaja, lothar, inguengan 508,1644,,491, 471, 367
        $testgroup = [564,1611,1784,1,184,344,3835];
        if (!in_array($this->user->id, $testgroup)){
            return view('access-denied');
        }

        $categoryData = DB::table('categoryTags')->where('categoryTags.id',$id)->
                        join('survey_questions_category','survey_questions_category.categoryTag_id','=','categoryTags.id')->
                        join('survey_questions','survey_questions_category.survey_questionID','=','survey_questions.id')->
                        join('survey_responses','survey_responses.question_id','=','survey_questions.id')->
                        leftJoin('survey_user','survey_user.user_id','=','survey_responses.user_id')->
                        join('team','team.user_id','=','survey_responses.user_id')->
                        //join('users','users.id','=','survey_responses.user_id')->
                        join('campaign','team.campaign_id','=','campaign.id')->
                        //leftJoin('survey_notes','survey_notes.user_id','=','survey_responses.user_id')->
                        select('categoryTags.label','survey_questions.value as question','survey_questions.survey_id as surveyID','survey_questions.id as questionID','survey_questions.img',  'survey_responses.survey_optionsID as answer', 'campaign.name as program', 'survey_responses.user_id', 'survey_user.isDone')->
                        where('survey_user.isDone',1)->get();
        $questions = collect($categoryData)->groupBy('questionID');
        $surveyID = $categoryData[0]->surveyID;
        
        //return $questions;

        $chartData = new Collection;
        $colors = [ "rgba(255,92,83,1)","#f99123","#f9e123","#37d04b","#3b8ee6","#6551d0","#d17de4","#ef5cac","#56f6ff","#9bda38"];
        
        //["rgba(255,92,83,1)","rgba(237,243,13,1)","rgba(58,217,218,1)","rgba(153,239,91,1)","rgba(255,92,83,1)","rgba(237,243,13,1)","rgba(58,217,218,1)","rgba(153,239,91,1)","rgba(255,92,83,1)","rgba(237,243,13,1)","rgba(58,217,218,1)","rgba(153,239,91,1)",];
        

        foreach ($questions as $key) {

            $q1 = collect($key)->whereIn('answer',[1]);
            $q2 = collect($key)->whereIn('answer',[2]);
            $q3 = collect($key)->whereIn('answer',[3]);
            $q4 = collect($key)->whereIn('answer',[4]);
            $q5 = collect($key)->whereIn('answer',[5]);
            //$u = collect($key)->sortBy('user_id')->pluck('user_id');
            

            $note = DB::table('survey_notes')->where('survey_notes.question_id',$key[0]->questionID)->where('comments','!=',null)->
                    //leftJoin('survey_responses','survey_notes.user_id','=', 'survey_responses.user_id')->
                    join('users','survey_notes.user_id','=','users.id')->
                    join('team','team.user_id','=','survey_notes.user_id')->
                    join('campaign','campaign.id','=','team.campaign_id')->
                    select('users.id as user_id','survey_notes.comments','survey_notes.created_at', 'users.dateHired','campaign.name as program','campaign.isBackoffice')->
                    orderBy('survey_notes.created_at','DESC')->get();//'survey_responses.survey_optionsID as rating',

            //***** get notes/comments
            $ratings = new Collection;
            foreach ($note as $n) {
                $r = Survey_Response::where('question_id',$key[0]->questionID)->where('user_id',$n->user_id)->first();

                $ratings->push($r->survey_optionsID);
            }
           $asOf = Carbon::now('GMT+8')->format('M d, Y h:i A');

            $chartData->push(['question'=>$key[0]->question,'questionID'=>$key[0]->questionID,'bg'=>$key[0]->img,  '1s'=>count($q1),'2s'=>count($q2),'3s'=>count($q3),'4s'=>count($q4),'5s'=>count($q5),'total'=>count($key),'notes'=>$note, 'ratings'=>$ratings]);

            # code...
        }


         if($this->user->id !== 564 ) {
          $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
            fwrite($file, "-------------------\n Viewed Survey Cat (".$id.") by [". $this->user->id."] ".$this->user->lastname."\n");
            fclose($file);
        }


        //return $chartData->sortBy('questionID');
        return view('forms.survey-category',compact('category','chartData','asOf','colors','surveyID'));

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

        switch ($id) {
          case 1: // EES 2019
                  {

                    $e = Survey_Extradata::where('user_id',$this->user->id)->where('survey_id',$survey->id)->get();
                    if (count($e) > 0) $extraData = $e->first()->beEEC;
                    else $extraData=null;

                    //$actives = count(DB::table('users')->where('status_id','!=',7)->where('status_id','!=',8)->where('status_id','!=',9)->
                    //                select('users.status_id')->get());

                    //exclude Taipei and Xiamen
                    $actives = count(DB::table('users')->where('status_id','!=',7)->
                                    where('status_id','!=',8)->
                                    where('status_id','!=',9)->
                                    where('status_id','!=',13)->
                                    leftJoin('team','team.user_id','=','users.id')->
                                    select('users.id','users.lastname','team.floor_id','team.campaign_id')->
                                    where('team.floor_id','!=',10)->
                                    where('team.floor_id','!=',11)->get());//;return $actives;
                    
                    // $actives = count(DB::table('users')->where('status_id','!=',7)->where('status_id','!=',8)->where('status_id','!=',9)->
                    //                 leftJoin('team','team.user_id','=','users.id')->
                    //                 select('users.id','team.floor_id')->
                    //                 where('team.floor_id','!=',10)->
                    //                 where('team.floor_id','!=',11)->get());

                    //return count($actives);
                                //);
                    $completed = count(Survey_User::where('isDone',true)->where('survey_id',$survey->id)->get());
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
            break;

          case 3: // year end survey
                  {

                    $extraData=null;

                    //exclude Taipei and Xiamen
                    $actives = count(DB::table('users')->where('status_id','!=',7)->
                                    where('status_id','!=',8)->
                                    where('status_id','!=',9)->
                                    where('status_id','!=',13)->
                                    leftJoin('team','team.user_id','=','users.id')->
                                    select('users.id','users.lastname','team.floor_id','team.campaign_id')->
                                    where('team.floor_id','!=',10)->
                                    where('team.floor_id','!=',11)->get());
                    $completed = count(Survey_User::where('isDone',true)->get());
                    $percentage = number_format(($completed / $actives)*100,2);

                    return view('forms.survey-results3',compact('survey','extraData','actives','completed','percentage'));

                  }break;
          
          case 6: // EES 2020
                  {


                    $e = Survey_Extradata::where('user_id',$this->user->id)->where('survey_id',$survey->id)->get();
                    if (count($e) > 0) $extraData = $e->first()->beEEC;
                    else $extraData=null;

                    //$actives = count(DB::table('users')->where('status_id','!=',7)->where('status_id','!=',8)->where('status_id','!=',9)->
                    //                select('users.status_id')->get());

                    //exclude Taipei and Xiamen
                    $actives = count(DB::table('users')->where('status_id','!=',2)->
                                    where('status_id','!=',6)->
                                    where('status_id','!=',7)->
                                    where('status_id','!=',8)->
                                    where('status_id','!=',9)->
                                    where('status_id','!=',13)->
                                    where('status_id','!=',16)->
                                    leftJoin('team','team.user_id','=','users.id')->
                                    select('users.id','users.lastname','team.floor_id','team.campaign_id')->
                                    where('team.floor_id','!=',10)->
                                    where('team.floor_id','!=',11)->get());//;return $actives;
                    
                    // $actives = count(DB::table('users')->where('status_id','!=',7)->where('status_id','!=',8)->where('status_id','!=',9)->
                    //                 leftJoin('team','team.user_id','=','users.id')->
                    //                 select('users.id','team.floor_id')->
                    //                 where('team.floor_id','!=',10)->
                    //                 where('team.floor_id','!=',11)->get());

                    //return count($actives);
                                //);
                    $completed = count(Survey_User::where('isDone',true)->where('survey_id',$survey->id)->get());
                    $percentage = number_format(($completed / $actives)*100,2);


                    // NPS questions: 156
                    $npsQuestions = DB::table('surveys')->where('surveys.id',$id)->
                                        join('survey_questions','survey_questions.survey_id','=','surveys.id')->
                                        join('survey_responses','survey_responses.question_id','=','survey_questions.id')->
                                        select('survey_questions.id as question','survey_responses.survey_optionsID as answer','survey_responses.user_id')->
                                        get();
                    $my = collect($npsQuestions);
                    $m = $my->where('user_id',$this->user->id);
                    $m2 = collect($m);
                    $n = $m2->whereIn('question',[156]);

                    if (count($n->pluck('answer')) > 0)
                      $nps = number_format(($n->pluck('answer')->sum())/count($n->pluck('answer')),2);
                    else $nps = 0;
                    //
                    $promoter=false;
                    $detractor=false;

                    if ($nps >= 4.0) {$color = "#3c8dbc"; $promoter=true; } //blue;
                    //else if ($nps > 3.6 && $nps <= 4.5 ) $color="#8ccb2c"; //green
                    else if ($nps >= 2.1 && $nps <= 3.9 ) $color="#ffe417"; //yellow
                    //else if ($nps >= 1.6 && $nps <= 2.1 ) $color="#f36b19"; //orange
                    else { $color="#fd1e1e"; $detractor=true; } //red

                   //return response()->json(['completed'=>$completed, 'actives'=>$actives]);

                    return view('forms.survey-results',compact('survey','extraData','actives','completed','percentage','nps','color','promoter','detractor'));

                  }
            break;

            default:
            # code...
            break;
        }
        

    }

    public function update($id)
    {
        

    }




    
}
