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
    use Traits\UserTraits;

     public function __construct(NewPA_Form $newPA_form)
    {
        $this->middleware('auth');
        $this->user =  User::find(Auth::user()->id);
        $this->newPA_form = $newPA_form;
    }



    public function index()
    {
      $forms = NewPA_Form::where('user_id',$this->user->id)->get();
      return view('evaluation.newPA-index',compact('forms'));

    }

    public function create()
    {
        // henry, lisa,nate, joy, e, florendo, qhaye, reese, bobby,arvie,agabao,crizzy
        $allowed = [184,334,464,1784,1611,305,163,307,2502,564,3264,3204,724 ];

        if (!in_array($this->user->id, $allowed)) return view('access-denied');

        $roles = NewPA_Type::all();
        $objectives = NewPA_Objective::all();
        $competencies = NewPA_Competencies::all();
        $correct = Carbon::now('GMT+8');
        $user = $this->user;

        //********** generate all subordinates ***********
        $coll = new Collection;

        $access = UserType::find($this->user->userType_id)->roles->pluck('label'); //->where('label','MOVE_EMPLOYEES');
        $canDelete =  ($access->contains('DELETE_EMPLOYEE')) ? '1':'0';
        $canUpdateLeaves =  ($access->contains('UPDATE_LEAVES')) ? '1':'0';


        $leader = ImmediateHead::where('employeeNumber', $this->user->employeeNumber)->get();
        if ($leader->isEmpty()) $leadershipcheck=null;
        else $leadershipcheck= $leader->first();

        $campaigns = "";

        if (is_null($leadershipcheck)) //get user's current team
        {
          $campaigns = $this->user->campaign->first()->name;
        } else
        {
              // $camps = $leadershipcheck->campaigns->sortBy('name')->pluck('name'); 
              // $campaigns1 = new Collection;
              // foreach($camps as $camp) $campaigns .= " | "." ". $camp;
        }

        //

        /* --------- optimize ---------- */
        
        

        if (is_null($leadershipcheck))
        {
          $allTeams = DB::table('team')->where('team.campaign_id',$this->user->campaign->first()->id)->
                          join('users','team.user_id','=','users.id')->
                          join('positions','users.position_id','=','positions.id')->
                          join('campaign','campaign.id','=','team.campaign_id')->
                          select('campaign.name as program','campaign.id as programID','campaign.isBackoffice', 'users.id','users.firstname','users.lastname','users.nickname','positions.name as position','users.id as userID','users.email')->
                          orderBy('users.lastname','ASC')->
                          where('users.status_id','!=',7)->
                          where('users.status_id','!=',8)->
                          where('users.status_id','!=',9)->get();
          $allData = $allTeams;
          //$allTeams = collect($allTeams1)->groupBy('program');
         
        } else {
          $allTeams1 = //DB::table('team')->where('team.campaign_id',$this->user->campaign->first()->id)->
                      DB::table('immediateHead_Campaigns')->where('immediateHead_id',$leadershipcheck->id)->
                           join('team','team.campaign_id','=','immediateHead_Campaigns.campaign_id')->
                           join('campaign','campaign.id','=','team.campaign_id')->
                           
                           join('immediateHead','immediateHead.id','=','immediateHead_Campaigns.immediateHead_id')->
                           //select('immediateHead_Campaigns.campaign_id','campaign.name as program', 'team.user_id')->get();
                          join('users','team.user_id','=','users.id')->
                          join('positions','users.position_id','=','positions.id')->
                          leftJoin('campaign_logos','team.campaign_id','=','campaign_logos.campaign_id')->
                          select('campaign.name as program','campaign.id as programID','campaign_logos.filename', 'campaign.isBackoffice', 'users.id', 'users.firstname','users.lastname','users.nickname','users.email','positions.name as position','users.id as userID','team.immediateHead_Campaigns_id as TLid')->
                          orderBy('users.lastname','ASC')->
                          where('users.status_id','!=',7)->
                          where('users.status_id','!=',8)->
                          where('users.status_id','!=',9)->get();

          //** ALLTEAMS == lahat ng under sayo, along with their own men grouped per campaign 
          $allTeams = collect($allTeams1)->sortBy('program')->groupBy('program');

          //** ALLDATA == flat array of all men
          $allData = collect($allTeams1)->sortBy('lastname');

        
        }
        /* --------- optimize ---------- */

        $myTree = new Collection;
        $mySubordinates = $this->getMySubordinates($this->user->employeeNumber);
        foreach ($mySubordinates as $sub) {
          
          if ($sub['subordinates'] !== null)
          {
            $members = DB::table('immediateHead_Campaigns')->where('immediateHead_Campaigns.immediateHead_id',$sub['ihID'])->
                            join('team','team.immediateHead_Campaigns_id','=','immediateHead_Campaigns.id')->
                            join('users','users.id','=','team.user_id')->
                            join('campaign','team.campaign_id','=','campaign.id')->
                            join('positions','users.position_id','=','positions.id')->
                            select('users.id','users.employeeNumber', 'users.nickname', 'users.firstname','users.lastname','users.userType_id', 'positions.name as jobTitle','users.email', 'campaign.name as program','campaign.id as programID', 'immediateHead_Campaigns.disabled')->
                            where('campaign.hidden',null)->
                            where('users.status_id','!=',7)->
                            where('users.status_id','!=',8)->
                            where('users.status_id','!=',9)->orderBy('users.lastname','ASC')->get();
                            //leftJoin('campaign','campaign.id','=','team.campaign_id')->get();
                            // 
                            // 
                            // 
             
            

            $n = collect($members)->pluck('userType_id','employeeNumber');
            $nextLevel = collect($n)->reject(function ($value,$key) {
                              return $value == 4;
                          });

            $myTree->push(['level'=>'2', 'parentID'=>$this->user->id, 'tl_userID'=>$sub['id'], 'firstname'=>$sub['firstname'],'lastname'=>$sub['lastname'],'nickname'=>$sub['nickname'],'jobTitle'=>$sub['position'], 'members'=>$members]);

            
            foreach ($nextLevel as $key => $value) {

              $check = ImmediateHead::where('employeeNumber',$key)->get();
              if (count($check) > 0)
              {
                $tluser = User::where('employeeNumber', $key)->first();
                $level3 = DB::table('immediateHead_Campaigns')->where('immediateHead_Campaigns.immediateHead_id',$check->first()->id)->
                            join('team','team.immediateHead_Campaigns_id','=','immediateHead_Campaigns.id')->
                            join('users','team.user_id','=','users.id')->
                            join('positions','users.position_id','=','positions.id')->
                            join('campaign','team.campaign_id','=','campaign.id')->
                            select('users.id','users.employeeNumber','users.nickname', 'users.firstname','users.lastname','users.userType_id', 'positions.name as jobTitle','users.email', 'campaign.name as program','campaign.id as programID','immediateHead_Campaigns.disabled')->
                            where('campaign.hidden',null)->
                            where('users.status_id','!=',7)->
                            where('users.status_id','!=',8)->
                            where('users.status_id','!=',9)->orderBy('users.lastname','ASC')->get();

                $n = collect($level3)->pluck('userType_id','employeeNumber');
                $nextLevel = collect($n)->reject(function ($value,$key) {
                              return $value == 4;
                          });

                $myTree->push(['level'=>'3','parentID'=>$sub['id'], 'tl_userID'=>$tluser->id, 'firstname'=>$tluser->firstname, 'lastname'=>$tluser->lastname, 'nickname'=>$tluser->nickname, 'members'=>$level3]);

                //*** LEVEL 4 
                foreach ($nextLevel as $key => $value) {

                  $check = ImmediateHead::where('employeeNumber',$key)->get();
                  if (count($check) > 0)
                  {
                    $tluser = User::where('employeeNumber', $key)->first();
                    $level4 = DB::table('immediateHead_Campaigns')->where('immediateHead_Campaigns.immediateHead_id',$check->first()->id)->
                                join('team','team.immediateHead_Campaigns_id','=','immediateHead_Campaigns.id')->
                                join('users','team.user_id','=','users.id')->
                                join('positions','users.position_id','=','positions.id')->
                                join('campaign','team.campaign_id','=','campaign.id')->
                                select('users.id','users.employeeNumber','users.nickname', 'users.firstname','users.lastname','users.userType_id', 'positions.name as jobTitle','users.email', 'campaign.name as program','campaign.id as programID','immediateHead_Campaigns.disabled')->
                                where('campaign.hidden',null)->
                                where('users.status_id','!=',7)->
                                where('users.status_id','!=',8)->
                                where('users.status_id','!=',9)->orderBy('users.lastname','ASC')->get();

                    $n = collect($level4)->pluck('userType_id','employeeNumber');
                    $nextLevel = collect($n)->reject(function ($value,$key) {
                                  return $value == 4;
                              });

                    $myTree->push(['level'=>'4','parentID'=>$sub['id'], 'tl_userID'=>$tluser->id, 'firstname'=>$tluser->firstname, 'lastname'=>$tluser->lastname, 'nickname'=>$tluser->nickname, 'members'=>$level4]);

                    //*** LEVEL 5
                    foreach ($nextLevel as $key => $value) {

                        $check = ImmediateHead::where('employeeNumber',$key)->get();
                        if (count($check) > 0)
                        {
                          $tluser = User::where('employeeNumber', $key)->first();
                          $level5 = DB::table('immediateHead_Campaigns')->where('immediateHead_Campaigns.immediateHead_id',$check->first()->id)->
                                      join('team','team.immediateHead_Campaigns_id','=','immediateHead_Campaigns.id')->
                                      join('users','team.user_id','=','users.id')->
                                      join('positions','users.position_id','=','positions.id')->
                                      join('campaign','team.campaign_id','=','campaign.id')->
                                      select('users.id','users.employeeNumber','users.nickname', 'users.firstname','users.lastname','users.userType_id', 'positions.name as jobTitle','users.email','campaign.id as programID', 'campaign.name as program','immediateHead_Campaigns.disabled')->
                                      where('campaign.hidden',null)->
                                      where('users.status_id','!=',7)->
                                      where('users.status_id','!=',8)->
                                      where('users.status_id','!=',9)->orderBy('users.lastname','ASC')->get();

                          $n = collect($level5)->pluck('userType_id','employeeNumber');
                          $nextLevel = collect($n)->reject(function ($value,$key) {
                                        return $value == 4;
                                    });

                          $myTree->push(['level'=>'5','parentID'=>$sub['id'], 'tl_userID'=>$tluser->id, 'firstname'=>$tluser->firstname, 'lastname'=>$tluser->lastname, 'nickname'=>$tluser->nickname, 'members'=>$level5]);

                         


                        }//end if an immediateHead
                      }//END LEVEL 5




                  }//end if an immediateHead
                }//END LEVEL 4




              }//end if an immediateHead
              
            }//end foreach nextlevel

            
          }
        }

        if($this->user->id !== 564 ) {
                      $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
                        fwrite($file, "-------------------\n Viewed My Team on ".$correct->format('Y-m-d H:i')." by [". $this->user->id."] ".$this->user->lastname."\n");
                        fclose($file);
                    }
        //return response()->json(["myTree"=>$myTree,"mySubordinates"=>$mySubordinates]);//$allTeams;// $myTree;
      return view('evaluation.newPA-create',compact('roles','objectives','competencies','mySubordinates','myTree','user'));

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


    
    public function preview($id)
    {

      $form = DB::table('newPA_form')->where('newPA_form.id',$id)->
                  leftJoin('newPA_type','newPA_form.typeID','=','newPA_type.id')->
                  leftJoin('newPA_form_goal','newPA_form_goal.formID','=','newPA_form.id',)->
                  leftJoin('newPA_goal','newPA_form_goal.goalID','=','newPA_goal.id')->
                  leftJoin('newPA_form_components','newPA_form_components.typeID','=','newPA_form.typeID')->
                  leftJoin('newPA_components','newPA_form_components.componentID','=','newPA_components.id')->
                  leftJoin('newPA_form_competencies','newPA_form_competencies.typeID','=','newPA_type.id')->
                  leftJoin('newPA_competencies','newPA_form_competencies.competencyID','=','newPA_competencies.id')->
                  //leftJoin('newPA_competency_descriptor','newPA_competencies.id','=','newPA_competency_descriptor.competencyID')->
                  select('newPA_form.name','newPA_form.typeID','newPA_components.name as componentName','newPA_form_components.weight as componentWeight', 'newPA_goal.statement','newPA_goal.activities','newPA_form_goal.weight as goalWeight','newPA_form_goal.id as goalID','newPA_competencies.id as competencyID', 'newPA_competencies.name as competency','newPA_form_competencies.weight as competencyWeight')->get();
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


                  //return response()->json(['Components'=>$allComponents, 'Goals'=>$allGoals,'Competencies'=>$allCompetencies,'descriptors'=>$descriptors]);
      return view('evaluation.newPA-preview',compact('allGoals','allCompetencies','descriptors','allComponents','form'));

    }

    public function process(Request $request)
    {
        // $goal1 = $request->goal1;
        // $goal2 = $request->goal2;
        // $goal3 = $request->goal3;
        // $goal4 = $request->goal4;
        // $goal5 = $request->goal5;
        $goalids = $request->goalids;
        $newGoals = $request->newgoals;

        $type = NewPA_Type::find($request->typeid);


        $newForm = new NewPA_Form;
        $newForm->typeID = $request->typeid;
        $newForm->user_id = $this->user->id;
        $newForm->name = $type->name." Appraisal Form";
        $newForm->description = "appraisal form for ".$type->name." by: ".$this->user->firstname." ".$this->user->lastname;
        $newForm->save();

        //save the goals you created
        $ctr=0;
        foreach ($goalids as $g) {
          $goal = new NewPA_Goal;
          $goal->user_id = $this->user->id;
          $goal->objectiveID = $g;
          $goal->typeID = $request->typeid;
          $goal->statement = $newGoals[$ctr]['statement'];
          $goal->activities = $newGoals[$ctr]['actions'];
          $goal->targets = " ";
          $goal->activities = $newGoals[$ctr]['actions'];
          $goal->save();

          $form_goal =  new NewPA_Form_Goal;
          $form_goal->formID = $newForm->id;
          $form_goal->goalID = $goal->id;
          $form_goal->weight = $newGoals[$ctr]['weight'];
          $form_goal->save();
          $ctr++;
        }
        

        return response()->json(['newGoals'=>$newGoals, 'goalids'=>$goalids]);
    }

    public function show()
    {

    }


}
