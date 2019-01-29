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

       

      $myCampaign = $this->user->campaign; 
      $canDoThis = UserType::find($this->user->userType_id)->roles->where('label','EDIT_EMPLOYEE');
      if (count($canDoThis)> 0 ) $hasUserAccess=1; else $hasUserAccess=0;

        
        /*$campaigns = Campaign::orderBy('name', 'ASC')->get();*/
         $allUsers = User::orderBy('lastname', 'ASC')->get();//->where('status_id','!=',7)->where('status_id','!=',8)->where('status_id','!=',9)->get();

            $users = $allUsers->filter(function($emp){
                return $emp->lastname != '' && $emp->lastname != ' ';

            });

            $activeUsers = $allUsers->filter(function($emp){
                return $emp->lastname != '' && $emp->lastname != ' ' && $emp->status_id !== 7 && $emp->status_id !== 8 && $emp->status_id !== 9 ;
                  });

            $inactiveUsers1 = $allUsers->filter(function($emp){
                return $emp->lastname != '' && $emp->lastname != ' ' && ($emp->status_id == 7 || $emp->status_id == 8 || $emp->status_id == 9);
                  });


        /*$statuses = Status::all();*/

        /*$allUsers = new Collection;*/
        $inactiveUsers = count($inactiveUsers1);

        //return $inactiveUsers1;
        $correct = Carbon::now('GMT+8');

        if($this->user->id !== 564 ) {
            $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
            fwrite($file, "-------------------\n Viewed all users - ". $correct->format('M d h:i A'). " by [". $this->user->id."] ".$this->user->lastname."\n");
            fclose($file);
        }
       
       //  return Datatables::collection($inactiveUsers)->make(true);
       //return $inactiveUsers;
        return view('people.employee-index', compact('myCampaign', 'hasUserAccess'));
    }

    

   

    public function create()
    {

        $canDoThis = UserType::find($this->user->userType_id)->roles->where('label','ADD_NEW_EMPLOYEE');

        if ( !$canDoThis->isEmpty() ) { 

        // if ($this->user->userType_id == 1 || $this->user->userType_id == 2)
        // {

            $TLs = ImmediateHead::where('lastname','!=','')->orderBy('lastname','ASC')->get();
            //$TLs = $TL->filter(function($t){return $t->lastname != '';});

            $myCampaign = $this->user->campaign; 
            $users = User::where('lastname','!=','')->orderBy('lastname', 'ASC')->get();
            $personnel = $this->user;
            
            $statuses1 = Status::all();
            $statuses = $statuses1->sortBy('orderNum');
            $userTypes = UserType::all();
            $positions = Position::where('name','!=','')->orderBy('name','ASC')->get();
            $hrDept = Campaign::where('name','HR')->first();
            $hrs = $hrDept->leaders; // ImmediateHead::where('campaign_id', $hrDept->id)->get();

            $campaigns = Campaign::where('name','!=','')->orderBy('name','ASC')->get(); // all(); // = Campaign::where('id', '!=', $personnel->campaign_id)->orderBy('name', 'ASC')->get();
            $floors = Floor::orderBy('name','ASC')->get();
             
            $leaders = new Collection;
            

            foreach ($TLs as $tl) {
                $hisPOsition = User::where('employeeNumber', $tl->employeeNumber)->first();

                //check for multiple campaign handle
                if (count($tl->campaign) > 1) 
                {
                  if($tl->status_id != 7 && $tl->status_id != 8 && $tl->status_id != 9   ){

                    foreach ($tl->campaign as $t) {
                       $leaders->push([
                        'id'=>$tl->id,
                        'position'=>$hisPOsition->position->name,
                        'lastname'=> $tl->lastname,
                        'firstname'=>$tl->firstname." - ". $t->name,
                        'campaign'=>$t->name ]);
                        }
                  }
                    

                } else
                {
                  if($tl->status_id != 7 && $tl->status_id != 8 && $tl->status_id != 9   )
                    $leaders->push([
                    'id'=>$tl->id,
                    'position'=>$hisPOsition['position'],
                    'lastname'=> $tl->lastname,
                    'firstname'=>$tl->firstname,
                    'campaign'=>$tl->campaigns->first()->name ]);

                }


                
            }

            $hrPersonnels = new Collection;
            foreach ($hrs as $tl) {
                $hisPOsition = User::where('employeeNumber', $tl->employeeNumber)->first();
                if($tl->status_id != 7 && $tl->status_id != 8 && $tl->status_id != 9   )
                $hrPersonnels->push([
                    'id'=>$tl->id,
                    'position'=>$hisPOsition->position->name,
                    'lastname'=> $tl->lastname,
                    'firstname'=>$tl->firstname,
                    'campaign'=>$tl->campaigns->first()->name ]);
            }

               // return $campaigns;
                return view('people.employee-new', compact('users','userTypes','floors', 'leaders',  'hrPersonnels', 'myCampaign', 'campaigns', 'personnel','statuses','changes', 'positions'));
            } else return view("access-denied");
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

        $canEditEmployees1 = UserType::find($this->user->userType_id)->roles->where('label','EDIT_EMPLOYEE');
        $canUpdateLeaves1 = UserType::find($this->user->userType_id)->roles->where('label','UPDATE_LEAVES');
        $page = $request->page;

        ($canEditEmployees1->isEmpty()) ? $canEditEmployees=false : $canEditEmployees=true;
        ($canUpdateLeaves1->isEmpty()) ? $canUpdateLeaves=false : $canUpdateLeaves=true;

        if (!$canEditEmployees && !$canUpdateLeaves)
        {
            return view('access-denied');

        } else 
        {

        
            
            $personnel = User::find($id);
            
            $personnelTL = ImmediateHead::find(ImmediateHead_Campaign::find($personnel->supervisor->immediateHead_Campaigns_id)->immediateHead_id);
            $personnelTL_ihCampID = ImmediateHead_Campaign::find($personnel->supervisor->immediateHead_Campaigns_id)->id;

            $TLs = ImmediateHead::where('lastname','!=','')->orderBy('lastname','ASC')->get();
            $fromYr = Carbon::parse($personnel->dateHired)->addMonths(6)->format('Y');

            // $TLs = $allTL->filter( function($t){
            //     return $t->firstname != '';

            // });  //where('campaign_id',$personnel->campaign_id)->
            $myCampaign = $this->user->campaign->first(); 
            
            $users = User::orderBy('lastname', 'ASC')->get();
           
            
            $statuses1 = Status::all(); //->sortBy('orderNum');
            $statuses = $statuses1->sortBy('orderNum');
            $userTypes = UserType::all();
            $positions = Position::where('name','!=','')->orderBy('name','ASC')->get();
            

            $campaigns = Campaign::where('name','!=','')->orderBy('name','ASC')->get();// $camp->filter(function($c){ return $c->name !== '' && $c->name !==' ';});
            $floors = Floor::all();

            $leaders = new Collection;
            //return $TLs;
            foreach ($TLs as $tl) {
                $hisPosition = Position::find($tl->userData['position_id']); //User::where('employeeNumber', $tl->employeeNumber)->first();

                //check for multiple campaign handle


                if (count($tl->campaigns) > 1) 
                {
                   
                    foreach ($tl->campaigns as $t) {

                      
                       $tlCamp = ImmediateHead_Campaign::where('immediateHead_id', $tl->id)->where('campaign_id', $t->id)->first();
                       $currstat = User::where('employeeNumber', $tl->employeeNumber)->first();
                        if ($currstat->status_id !== 7 && $currstat->status_id !== 8 && $currstat->status_id !== 9 && $currstat->status_id !== 10 && $personnel->employeeNumber !== $tl->employeeNumber) 
                        {
                           $leaders->push([
                          'id'=>$tlCamp->id,
                          'position'=>$hisPosition['name'],
                          'lastname'=> $tl->lastname,
                          'firstname'=>$tl->firstname." - ". $t->name,
                          'campaign'=>$t->name ]);

                        }
                      
                       
                    }


                } else
                {
                   

                    $tlCamp = ImmediateHead_Campaign::where('immediateHead_id', $tl->id)->where('campaign_id',$tl->campaigns->first()->id)->first(); //->where('campaign_id', $tl->campaign->first()->id)->get();
                    $currstat = User::where('employeeNumber', $tl->employeeNumber)->first();

                     if ($currstat['status_id'] !== 7 && $currstat['status_id'] !== 8 && $currstat['status_id'] !== 9 && $currstat['status_id'] !== 10 && $personnel->employeeNumber !== $tl->employeeNumber) 
                        {
                          $leaders->push([
                          'id'=>$tlCamp->id,
                          
                          'position'=> $hisPosition['name'] , //$hisPOsition->position->name,
                          'lastname'=> $tl->lastname,
                          'firstname'=>$tl->firstname,
                          'campaign'=>$tl->campaigns->first()->name ]);

                         
                        }

                }
                
            }

            //--- GENERATE TEAM MATES : UserTrait
            
            $teamMates = $this->getTeammates($id);

            //--end team mates

            //---- Check now if already has approver, if none set immediate head as approver
            $approvers = $personnel->approvers;
            $currentTLcamp = Campaign::where('id',ImmediateHead_Campaign::find($personnelTL_ihCampID)->campaign_id)->get();


            return view('people.employee-edit', compact('fromYr','canEditEmployees','canUpdateLeaves', 'page', 'approvers','teamMates', 'currentTLcamp', 'personnelTL_ihCampID', 'users','floors', 'userTypes', 'leaders', 'myCampaign', 'campaigns', 'personnel','personnelTL', 'statuses','changes', 'positions'));


        } 
       
    }

    public function saveSurvey(Request $request)
    {

    }

   
    public function show($id)
    {

        //return bcrypt('mbarrientos'); //$2y$10$sMSV71.0T0OPy/7EhlqjROaO4j6APUUSB6w2hawG/.z08JLiB5Pee

        
        DB::connection()->disableQueryLog(); 
        //$survey = Survey::find($id);

        $questions = DB::table('surveys')->where('surveys.id',$id)->
                        //where('form_submissions_users.created_at','>=',$from)->
                        //where('form_submissions_users.created_at','<=',$to)->
                        join('survey_questions','survey_questions.survey_id','=','surveys.id')->
                        //join('survey_options','surveys.id','=','survey_options.survey_id')->
                        //join('formBuilder_items','form_submissions.formBuilder_itemID','=','formBuilder_items.id')->
                        //leftJoin('users','form_submissions_users.user_id','=','users.id')->
                        select('surveys.name','survey_questions.ordering','survey_questions.img', 'survey_questions.value as question', 'survey_questions.responseType')->
                        orderBy('survey_questions.ordering','ASC')->get();
        $options = DB::table('survey_options')->where('survey_options.survey_id',$id)->
                         leftJoin('options','options.id','=','survey_options.options_id')->
                         select('options.label','options.value','options.ordering')->
                         orderBy('options.ordering','ASC')->get();

        $totalItems = count($questions);
        $survey = new Collection;
        $survey->push(['answers'=>$options, 'questions'=>$questions]);
        $startFrom = 1;
        //return $questions;

        return view('forms.survey-show', compact('survey','totalItems','questions','startFrom','options'));


                   
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

    public function update($id)
    {
        $employee = User::find($id);




        $employee->nickname = Input::get('nickname');
        $employee->firstname = Input::get('firstname');
        $employee->middlename = Input::get('middlename');
        $employee->lastname = Input::get('lastname');
        $employee->gender = Input::get('gender');
        $employee->accesscode = Input::get('accesscode');
        $employee->employeeNumber = Input::get('employeeNumber');
        $employee->email = preg_replace('/\s+/', '', Input::get('email'));

        $bday = Input::get('birthday');
        if(($bday !== 'MM/DD/YYYY') && !empty($bday) && $bday !== '01/01/1970' && $bday !== '0000-00-00')
          $employee->birthday = date("Y-m-d", strtotime(Input::get('birthday')));
       
        $employee->dateHired = date("Y-m-d h:i:sa", strtotime(Input::get('dateHired')));
        $employee->dateRegularized = date("Y-m-d h:i:sa", strtotime(Input::get('dateRegularized'))); 
        $employee->startTraining = date("Y-m-d h:i:sa", strtotime(Input::get('startTraining')));
        $employee->endTraining = date("Y-m-d h:i:sa", strtotime(Input::get('endTraining')));
        $employee->userType_id =  Input::get('userType_id');
        $employee->status_id = Input::get('status_id');
        $employee->position_id = Input::get('position_id');
        $employee->leadOverride = Input::get('leadOverride');
        $employee->push();
      

        /* ------------- WE NEED TO UPDATE AS WELL THE TEAMS WHERE HE/SHE BELONGS ---------- */

        $team = Team::where('user_id',$employee->id)->first(); //$employee->team();//->where('campaign_id',)->first();
       

       
        
        $team->immediateHead_Campaigns_id = Input::get('immediateHead_Campaigns_id');
        $team->campaign_id = Input::get('campaign_id'); 
        $team->floor_id = Input::get('floor_id'); 
        $team->push();


        /* -------------- log updates made --------------------- */
         $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
            fwrite($file, "-------------------\n". $employee->lastname .",". $employee->firstname." updated ". date('M d h:i:s'). " by ". $this->user->firstname.", ".$this->user->lastname."\n");
            fclose($file);
           

        return response()->json($team);
        //return "hello";

    }




    
}
