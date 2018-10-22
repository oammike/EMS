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
use OAMPI_Eval\User_Leader;
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

class UserController extends Controller
{
    protected $user;
    use Traits\UserTraits;
    use Traits\TimekeepingTraits;

     public function __construct(User $user)
    {
        $this->middleware('auth');
        $this->user =  User::find(Auth::user()->id);
    }

    public function index()
    {
       

      $myCampaign = $this->user->campaign; 
      $canDoThis = UserType::find($this->user->userType_id)->roles->where('label','EDIT_EMPLOYEE');
      if (count($canDoThis)> 0 ) $hasUserAccess=1; else $hasUserAccess=0;
      //if (!empty($canDoThis)) return $canDoThis; else return "cant";

     

      //$TLs = ImmediateHead_Campaign::where('campaign_id', $myCampaign->id)->orderBy('lastname','ASC')->get();

      if (count($myCampaign) > 1){
          $leaders = new Collection;
          foreach ($myCampaign as $c) {
              $leaders->push($c->leaders);
             
          }
      } else $leaders = $myCampaign->first()->leaders;
      $TLs = $leaders->sortBy('lastname')->unique();
           

        
        
        $campaigns = Campaign::orderBy('name', 'ASC')->get();
         $allUsers = User::orderBy('lastname', 'ASC')->take(20)->get();

            $users = $allUsers->filter(function($emp){
                return $emp->lastname != '' && $emp->lastname != ' ';

            });

            $activeUsers = $allUsers->filter(function($emp){
                return $emp->lastname != '' && $emp->lastname != ' ' && $emp->status_id !== 7 && $emp->status_id !== 8 && $emp->status_id !== 9 ;
                  });

            $inactiveUsers1 = $allUsers->filter(function($emp){
                return $emp->lastname != '' && $emp->lastname != ' ' && ($emp->status_id == 7 || $emp->status_id == 8 || $emp->status_id == 9);
                  });


        $statuses = Status::all();

        $allUsers = new Collection;
        $inactiveUsers = new Collection;

        //return $inactiveUsers1;

        foreach ($inactiveUsers1 as $a) 
        {

           if ( file_exists('public/img/employees/'.$a->id.'.jpg') )
           {
            $img = asset('public/img/employees/'.$a->id.'.jpg');
           } else {
            $img = asset('public/img/useravatar.png');
           }
                         

           
           $teamInfo = Team::where('user_id',$a->id)->first();
           $leadershipcheck = ImmediateHead::where('employeeNumber', $a->employeeNumber)->first();
           $camps = "";
           if (empty($leadershipcheck)) 
            {
               $d = [
                  'profilepic'=>$img,
                  'lastname'=>$a->lastname,
                  'firstname'=>$a->firstname,
                  'id'=>$a->id,
                  'email'=>$a->email,
                  'position'=> $a->position->name,
                  'employeeNumber'=> $a->employeeNumber,
                  'campaign' => Campaign::find($teamInfo->campaign_id)->name,
                  'floor'=> Floor::find($teamInfo->floor_id)->name,
                  'hired'=>$a->dateHired,
                  'status'=> Status::find($a->status_id)->name,];


            } else
            {
                $ct = 1;

                foreach ($leadershipcheck->myCampaigns as $c) {
                    if ($ct == count($leadershipcheck->myCampaigns)) $camps .= Campaign::find($c->campaign_id)->name;
                    else $camps .= Campaign::find($c->campaign_id)->name . ", ";
                    
                    $ct++;
                }
                       $d = [
                    'profilepic'=>$img,
                    'lastname'=>$a->dateHired,//$a->lastname,
                    'firstname'=>$a->dateHired,//$a->firstname,
                    'id'=>$a->id,
                    'email'=>$a->email,
                    'position'=> $a->position->name,
                    'employeeNumber'=> $a->employeeNumber,
                    'campaign' => $camps,
                    'floor'=> Floor::find($teamInfo->floor_id)->name,
                    'hired'=>$a->dateHired,
                    'status'=> Status::find($a->status_id)->name,
                    
                    ];
                  


            }$inactiveUsers->push($d);
        }
        
       
       //  return Datatables::collection($inactiveUsers)->make(true);
       //return $inactiveUsers;
        return view('people.employee-index', compact('TLs', 'myCampaign', 'activeUsers', 'inactiveUsers', 'hasUserAccess'));
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
            $floors = Floor::all();
             
            $leaders = new Collection;
            

            foreach ($TLs as $tl) {
                $hisPOsition = User::where('employeeNumber', $tl->employeeNumber)->first();

                //check for multiple campaign handle
                if (count($tl->campaign) > 1) 
                {
                    foreach ($tl->campaign as $t) {
                       $leaders->push([
                        'id'=>$tl->id,
                        'position'=>$hisPOsition->position->name,
                        'lastname'=> $tl->lastname,
                        'firstname'=>$tl->firstname." - ". $t->name,
                        'campaign'=>$t->name ]);
                        }
                    

                } else
                {
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

    public function createSchedule($id)
    {

        $user = User::find($id);

        $roles = UserType::find($this->user->userType_id)->roles->pluck('label'); 
        $canCWS =  ($roles->contains('CAN_CWS')) ? '1':'0';

        if (!$canCWS) return view('cws-denied');


        $fellowTeam = Team::where('immediateHead_Campaigns_id',$user->team->immediateHead_Campaigns_id)->get();
        $teams = new Collection;
        //return $fellowTeam;
        $img = $this->getProfilePic($user->id);

       //return $fellowTeam->first()->userInfo; //->sortBy('lastname');

        foreach ($fellowTeam as $pip) {

            if ($pip->user_id !== (int)$id && $this->isInactive($pip->user_id)==false )
            $teams->push([  'id'=> $pip->user_id,
                                'firstname'=>User::find($pip->user_id)->firstname,
                                'lastname'=>User::find($pip->user_id)->lastname,
                                'pic' => $this->getProfilePic($pip->user_id),
                                'position' => User::find($pip->user_id)->position->name]);
        }

        $teammates = $teams->sortBy('lastname');
        $shifts = $this->generateShifts('12H');

        //return $teammates;

        return view('timekeeping.create-user-schedule', compact('user','img','shifts', 'teammates'));


    }

     public function changePassword()
    {
        $user = $this->user;
        return view('people.changePassword', compact('user'));

    }

    public function checkCurrentPassword()
    {
        $pass = Input::get('data');
        
        if ( Hash::check($pass, $this->user->password) )
        {
            $response = array('status' => 'success' , 'correct' => true, 'password' => $this->user->password);
        } else { $response = array('status' => 'success' , 'correct' => false, 'password' => $this->user->password, 'submitted'=> bcrypt($pass) ); } 

        
        return response()->json($response);
    }


    public function destroy($id)
    {
        $this->user->destroy($id);
        return back();
    }

     public function downloadAllUsers()
    {

      Excel::create('All Employee Data', function($excel) {

        

          // Set the title
          $excel->setTitle('All Employee Data');

          // Chain the setters
          $excel->setCreator('Mike Pamero')
                ->setCompany('OAMPI');

          // Call them separately
          $excel->setDescription('Contains all employee data');

          $excel->sheet('Active', function($sheet) {

           
            $employees = User::where('lastname','!=','')->where('status_id','!=','7')->where('status_id','!=','8')->where('status_id','!=','9')->orderBy('lastname', 'ASC')->get();

            $sheet->appendRow(array('Employee Number', 'Lastname', 'Firstname', 'Dept/Program','Position','Status', 'Date Hired', 'Current Address', 'Permanent Address', 'Mobile Number', 'Telephone', 'Work Schedule', 'RD'));

            foreach($employees as $emp){
              
              $empSchedule = "";
              $empRD = "";
              $hisSchedules = $emp->schedules;
              foreach ($hisSchedules as $sched) {
                  $empSchedule .= $sched->workday." ".date('h:i A', strtotime('1999-01-01'.$sched->timeStart))." - ". date('h:i A', strtotime('1999-01-01'.$sched->timeEnd))."; ";

              }

              $hisRD = $emp->restdays;
              foreach ($hisRD as $rd)
              {
                    $empRD .= $rd->RD. ", ";

              }
                $arr = array($emp->employeeNumber,
                    $emp->lastname,
                    $emp->firstname,
                    Campaign::find(Team::where('user_id',$emp->id)->first()->campaign_id)->name, 
                    $emp->position->name, 
                    $emp->status->name,
                    Carbon::createFromFormat('Y-m-d H:i:s',$emp->dateHired),
                    $emp->currentAddress1." , ".$emp->currentAddress2." , ". $emp->currentAddress3,
                    $emp->permanentAddress1." , ".$emp->permanentAddress2." , ". $emp->permanentAddress3,
                    $emp->mobileNumber,
                    $emp->phoneNumber,
                    $empSchedule,
                    $empRD);
              
              $sheet->appendRow($arr);

            }

              

          });

          //Resigned summary
          $excel->sheet('Resigned', function($sheet) {

            $evals = User::where('lastname','!=','')->where('status_id','7')->orderBy('lastname','ASC')->get(); //get only Regularization forms
           $sheet->appendRow(array('Employee Number', 'Lastname', 'Firstname', 'Dept/Program','Position','Status', 'Date Hired'));

            foreach($evals as $eval){
              
                $arr = array($eval->employeeNumber,$eval->lastname,$eval->firstname,Campaign::find(Team::where('user_id',$eval->id)->first()->campaign_id)->name, $eval->position->name, $eval->status->name,Carbon::createFromFormat('Y-m-d H:i:s',$eval->dateHired));
              
              $sheet->appendRow($arr);

            }

              

          });

          //Terminated summary
          $excel->sheet('Terminated', function($sheet) {

            $evals = User::where('lastname','!=','')->where('status_id','8')->orderBy('lastname','ASC')->get(); //get only Regularization forms
           $sheet->appendRow(array('Employee Number', 'Lastname', 'Firstname', 'Dept/Program','Position','Status', 'Date Hired'));

            foreach($evals as $eval){
              
                $arr = array($eval->employeeNumber,$eval->lastname,$eval->firstname,Campaign::find(Team::where('user_id',$eval->id)->first()->campaign_id)->name, $eval->position->name, $eval->status->name,Carbon::createFromFormat('Y-m-d H:i:s',$eval->dateHired));
              
              $sheet->appendRow($arr);

            }

              

          });


      })->export('xls');

      return "Download";
    }

    public function editSchedule($id)
    {
        $user = User::find($id);
        
        

        $roles = UserType::find($this->user->userType_id)->roles->pluck('label'); //->where('label','MOVE_EMPLOYEES');
        $canMoveEmployees =  ($roles->contains('MOVE_EMPLOYEE')) ? '1':'0';
        $canEditEmployees =  ($roles->contains('EDIT_EMPLOYEE')) ? '1':'0';
        $days = array("Sundays", "Mondays", "Tuesdays","Wednesdays","Thursdays","Fridays","Saturdays");

        $hrDept = Campaign::where('name',"HR")->first();

        // check if viewing is allowed
        $supervisor = ImmediateHead::find(ImmediateHead_Campaign::find($user->team->immediateHead_Campaigns_id)->immediateHead_id)->employeeNumber;

        if ($canEditEmployees || $this->user->id == $id || $supervisor == $this->user->employeeNumber ) 
        {
            $user = User::find($id); 
            $immediateHead = ImmediateHead::find(ImmediateHead_Campaign::find($user->team->immediateHead_Campaigns_id)->immediateHead_id);
           
            // $hisTeam = $user->team()->where('campaign_id','16')->first();
            // return $hisTeam;


            return view('people.editSchedule', compact('user','immediateHead', 'canMoveEmployees', 'canEditEmployees','days'));

            
            
        } else return view('access-denied');

    }

    public function editShifts($id)
    {

        $user = User::find($id);

        $roles = UserType::find($this->user->userType_id)->roles->pluck('label'); 
        $canCWS =  ($roles->contains('CAN_CWS')) ? '1':'0';

        if (!$canCWS) return view('cws-denied');


        $fellowTeam = Team::where('immediateHead_Campaigns_id',$user->team->immediateHead_Campaigns_id)->get();
        $teams = new Collection;
        //return $fellowTeam;
        $img = $this->getProfilePic($user->id);

       //return $fellowTeam->first()->userInfo; //->sortBy('lastname');

        foreach ($fellowTeam as $pip) {

            if ($pip->user_id !== (int)$id && $this->isInactive($pip->user_id)==false )
            $teams->push([  'id'=> $pip->user_id,
                                'firstname'=>User::find($pip->user_id)->firstname,
                                'lastname'=>User::find($pip->user_id)->lastname,
                                'pic' => $this->getProfilePic($pip->user_id),
                                'position' => User::find($pip->user_id)->position->name]);
        }

        $teammates = $teams->sortBy('lastname');
        $shifts = $this->generateShifts('12H');

        // ------ now check if you have saved worked schedules -------

            $workSchedule = new Collection;

        if (count($user->fixedSchedule) == 0) $workSchedule = null;
        else {
          $fsched = $user->fixedSchedule->where('isRD',0)->sortBy('workday')->groupBy('workday');
          $rdays = $user->fixedSchedule->where('isRD',1)->sortBy('workday')->groupBy('workday');;
          $workdays = new Collection;
          $rds = new Collection;
          

          foreach ($fsched as $key) {
            $workdays->push($key->sortByDesc('created_at')->first());
           
          }
          foreach ($rdays as $key) {
            $k = $key->sortByDesc('created_at')->first();
            $rds->push($k);
            
           
          }
          //$workSchedule->push(['type'=>'fixed', 'workDays'=>$user->fixedSchedule->where('isRD',0) , 'RD'=> $user->fixedSchedule->where('isRD',1)]);
          $workSchedule->push(['type'=>'fixed', 'workDays'=>$workdays , 'RD'=>$rds ]);
        
        }

        return view('timekeeping.edit-user-fixedSchedule', compact('user','img','shifts', 'teammates'));


    }

    public function editContact($id)
    {
        $user = User::find($id);
        

        $roles = UserType::find($this->user->userType_id)->roles->pluck('label'); //->where('label','MOVE_EMPLOYEES');
        $canMoveEmployees =  ($roles->contains('MOVE_EMPLOYEE')) ? '1':'0';
        $canEditEmployees =  ($roles->contains('EDIT_EMPLOYEE')) ? '1':'0';

        $hrDept = Campaign::where('name',"HR")->first();

        // check if viewing is not an agent, an HR personnel, or the owner
        if ($canEditEmployees || $this->user->id == $id  )  //($this->user->userType_id == 1 || $this->user->userType_id == 2)
        {
            $user = User::find($id); 
            $immediateHead = ImmediateHead::find(ImmediateHead_Campaign::find($user->team->immediateHead_Campaigns_id)->immediateHead_id);
           
            // $hisTeam = $user->team()->where('campaign_id','16')->first();
            // return $hisTeam;


            return view('people.editContact', compact('user','immediateHead', 'canMoveEmployees', 'canEditEmployees'));

            
            
        } else return view('access-denied');
    }




    public function editUser($id)
    {

        $canDoThis = UserType::find($this->user->userType_id)->roles->where('label','EDIT_EMPLOYEE');

        if ($canDoThis->isEmpty())
        {
            return view('access-denied');

        } else 
        {

        
            
            $personnel = User::find($id);
            
            $personnelTL = ImmediateHead::find(ImmediateHead_Campaign::find($personnel->supervisor->immediateHead_Campaigns_id)->immediateHead_id);
            $personnelTL_ihCampID = ImmediateHead_Campaign::find($personnel->supervisor->immediateHead_Campaigns_id)->id;

            $TLs = ImmediateHead::where('lastname','!=','')->orderBy('lastname','ASC')->get();

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
           
                return view('people.employee-edit', compact('approvers','teamMates', 'currentTLcamp', 'personnelTL_ihCampID', 'users','floors', 'userTypes', 'leaders', 'myCampaign', 'campaigns', 'personnel','personnelTL', 'statuses','changes', 'positions'));


        } 
       
    }

    public function getAllActiveUsers(){

        $all = User::orderBy('lastname', 'ASC')->take(20)->get();

        $users = $all->filter(function($emp){
            return $emp->lastname != '' && $emp->lastname != ' ' && $emp->status_id !== 7 && $emp->status_id !== 8 && $emp->status_id !== 9 ;

        });
        
        $allUsers = new Collection;

        foreach ($users->sortBy('lastname') as $a) {

           if ( file_exists('public/img/employees/'.$a->id.'.jpg') )
           {
            $img = asset('public/img/employees/'.$a->id.'.jpg');
           } else {
            $img = asset('public/img/useravatar.png');
           }
                         

           $status = ($a->isPublished ? "Published": "<em>Draft</em>");
           //$hisSupervisor = ImmediateHead_Campaign::find($a->immediateHead_Campaigns_id);
           //$hisSupervisor = $a->supervisor->first();
           $teamInfo = Team::where('user_id',$a->id)->first();
           //$supervisor = ImmediateHead::find(ImmediateHead_Campaign::find($a->supervisor->immediateHead_Campaigns_id)->immediateHead_id);
           $leadershipcheck = ImmediateHead::where('employeeNumber', $a->employeeNumber)->first();
           $camps = "";



          

          if (empty($leadershipcheck)) 
          {
             $d = [
                'profilepic'=>$img,
                'lastname'=>$a->lastname,
                'firstname'=>$a->firstname,
                'id'=>$a->id,
                'email'=>$a->email,
                'position'=> $a->position->name,
                'employeeNumber'=> $a->employeeNumber,
                'campaign' => Campaign::find($teamInfo->campaign_id)->name,
                //'immediateHead'=> $supervisor->firstname." ". $supervisor->lastname,
                //'status'=> Status::find($a->status_id)->name,
                'hired'=>date('Y-m-d',strtotime($a->dateHired)), 

                // 'campaign' => $a->campaign[0]->name,
           //      
           //      'immediateHead' => $hisSupervisor, // $hisSupervisor->firstname." ".$hisSupervisor->lastname,
                ];


          } else
          {
            $ct = 1;

            foreach ($leadershipcheck->myCampaigns as $c) {
                $ih = ImmediateHead::find($c->immediateHead_id);
                $ihCamp = ImmediateHead_Campaign::where('immediateHead_id', $c->immediateHead_id)->where('campaign_id', $c->campaign_id)->get();

                     //   if ($ihCamp->disabled != true)
                     //  {
                     //     if (count($leadershipcheck->myCampaigns) <= 1) $camps .= Campaign::find($c->campaign_id)->name;
                     //      else $camps .= Campaign::find($c->campaign_id)->name . ", ";

                     //  } else { }


                    //$camps .= $c->immediateHead_id; //Campaign::find($c->campaign_id)->name; //->pivot->campaign_id;
                if (!$c->disabled)
                  $camps .= Campaign::find($c->campaign_id)->name . ", ";
                else { }
               
                $ct++;
            }

            $d = [
              'profilepic'=>$img,
              'lastname'=>$a->dateHired, //$a->lastname,
              'firstname'=>$a->dateHired, //$a->firstname,
              'id'=>$a->id,
              'email'=>$a->dateHired, //$a->email,
              'position'=> $a->position->name,
              'employeeNumber'=> $a->dateHired, // $a->employeeNumber,
              'campaign' => $camps,
              //'immediateHead'=> $supervisor->firstname." ". $supervisor->lastname,
              'hired'=>date('Y-m-d',strtotime($a->dateHired)), 
              //'status'=> Status::find($a->status_id)->name,
              //date('Y-M-d', strtotime($a->dateHired))
              //'immediateHead' => $supervisor, // $hisSupervisor->firstname." ".$hisSupervisor->lastname,
            ];


          }
          $allUsers->push($d);
        }
        //return $allUsers;
        return Datatables::collection($allUsers)->make(true);
       
    }

    public function getAllInactiveUsers(){

        $all = User::orderBy('lastname', 'ASC')->get();

        $users = $all->filter(function($emp){
            return $emp->lastname != '' && $emp->lastname != ' ' && ($emp->status_id == 7 || $emp->status_id == 8 || $emp->status_id == 9);

        });
        
        $allUsers = new Collection;

        foreach ($users->sortBy('lastname') as $a) {

           if ( file_exists('public/img/employees/'.$a->id.'.jpg') )
           {
            $img = asset('public/img/employees/'.$a->id.'.jpg');
           } else {
            $img = asset('public/img/useravatar.png');
           }
                         

           $status = ($a->isPublished ? "Published": "<em>Draft</em>");
           //$hisSupervisor = ImmediateHead_Campaign::find($a->immediateHead_Campaigns_id);
           //$hisSupervisor = $a->supervisor->first();
           $teamInfo = Team::where('user_id',$a->id)->first();
           //$supervisor = ImmediateHead::find(ImmediateHead_Campaign::find($a->supervisor->immediateHead_Campaigns_id)->immediateHead_id);
           $leadershipcheck = ImmediateHead::where('employeeNumber', $a->employeeNumber)->first();
           $camps = "";

          // return Campaign::find($teamInfo->campaign_id)->name;

          if (empty($leadershipcheck)) 
          {
             $d = [
                'profilepic'=>$img,
                'lastname'=>$a->lastname,
                'firstname'=>$a->firstname,
                'id'=>$a->id,
                'email'=>$a->email,
                'position'=> $a->position->name,
                'employeeNumber'=> $a->employeeNumber,
                'campaign' => Campaign::find($teamInfo->campaign_id)->name,
                //'immediateHead'=> $supervisor->firstname." ". $supervisor->lastname,
                'status'=> Status::find($a->status_id)->name,

                // 'campaign' => $a->campaign[0]->name,
           //      
           //      'immediateHead' => $hisSupervisor, // $hisSupervisor->firstname." ".$hisSupervisor->lastname,
                ];


          } else
          {
            $ct = 1;

            foreach ($leadershipcheck->myCampaigns as $c) {
                $ih = ImmediateHead::find($c->immediateHead_id);
                $ihCamp = ImmediateHead_Campaign::where('immediateHead_id', $c->immediateHead_id)->where('campaign_id', $c->campaign_id)->get();

               //   if ($ihCamp->disabled != true)
               //  {
               //     if (count($leadershipcheck->myCampaigns) <= 1) $camps .= Campaign::find($c->campaign_id)->name;
               //      else $camps .= Campaign::find($c->campaign_id)->name . ", ";

               //  } else { }


              //$camps .= $c->immediateHead_id; //Campaign::find($c->campaign_id)->name; //->pivot->campaign_id;
                if (!$c->disabled)
                  $camps .= Campaign::find($c->campaign_id)->name . ", ";
                else { }
               
                $ct++;
            }
                   $d = [
                'profilepic'=>$img,
                'lastname'=>$a->lastname,
                'firstname'=>$a->firstname,
                'id'=>$a->id,
                'email'=>$a->email,
                'position'=> $a->position->name,
                'employeeNumber'=> $a->employeeNumber,
                'campaign' => $camps,
                //'immediateHead'=> $supervisor->firstname." ". $supervisor->lastname,
                'status'=> Status::find($a->status_id)->name,
                'hired'=>date('M d, Y', strtotime($a->dateHired))
                //'immediateHead' => $supervisor, // $hisSupervisor->firstname." ".$hisSupervisor->lastname,
                ];


          }
             



            $allUsers->push($d);
        }
        //return $allUsers;
        return Datatables::collection($allUsers)->make(true);

    }

    public function getAllUsers(){

        $all = User::orderBy('lastname', 'ASC')->get();

        $users = $all->filter(function($emp){
            return $emp->lastname != '' && $emp->lastname != ' ';

        });
        
        $allUsers = new Collection;

        foreach ($users->sortBy('lastname') as $a) {

           if ( file_exists('public/img/employees/'.$a->id.'.jpg') )
           {
            $img = asset('public/img/employees/'.$a->id.'.jpg');
           } else {
            $img = asset('public/img/useravatar.png');
           }
                         

           $status = ($a->isPublished ? "Published": "<em>Draft</em>");
           //$hisSupervisor = ImmediateHead_Campaign::find($a->immediateHead_Campaigns_id);
           //$hisSupervisor = $a->supervisor->first();
           $teamInfo = Team::where('user_id',$a->id)->first();
           //$supervisor = ImmediateHead::find(ImmediateHead_Campaign::find($a->supervisor->immediateHead_Campaigns_id)->immediateHead_id);
           $leadershipcheck = ImmediateHead::where('employeeNumber', $a->employeeNumber)->first();
           $camps = "";

          // return Campaign::find($teamInfo->campaign_id)->name;

          if (empty($leadershipcheck)) 
          {
             $d = [
                'profilepic'=>$img,
                'lastname'=>$a->lastname,
                'firstname'=>$a->firstname,
                'id'=>$a->id,
                'email'=>$a->email,
                'position'=> $a->position->name,
                'employeeNumber'=> $a->employeeNumber,
                'campaign' => Campaign::find($teamInfo->campaign_id)->name,
                //'immediateHead'=> $supervisor->firstname." ". $supervisor->lastname,
                'status'=> Status::find($a->status_id)->name,

                // 'campaign' => $a->campaign[0]->name,
           //      
           //      'immediateHead' => $hisSupervisor, // $hisSupervisor->firstname." ".$hisSupervisor->lastname,
                ];


          } else
          {
            $ct = 1;

            foreach ($leadershipcheck->myCampaigns as $c) {
                if ($ct == count($leadershipcheck->myCampaigns)) $camps .= Campaign::find($c->campaign_id)->name;
                else $camps .= Campaign::find($c->campaign_id)->name . ", ";
                
                $ct++;
            }
                   $d = [
                'profilepic'=>$img,
                'lastname'=>$a->lastname,
                'firstname'=>$a->firstname,
                'id'=>$a->id,
                'email'=>$a->email,
                'position'=> $a->position->name,
                'employeeNumber'=> $a->employeeNumber,
                'campaign' => $camps,
                //'immediateHead'=> $supervisor->firstname." ". $supervisor->lastname,
                'status'=> Status::find($a->status_id)->name,
                //'immediateHead' => $supervisor, // $hisSupervisor->firstname." ".$hisSupervisor->lastname,
                ];


          }
             



            $allUsers->push($d);
        }
        //return $allUsers;
        return Datatables::collection($allUsers)->make(true);       
    }

    public function getWorkSchedForTheDay($id, Request $request)
    {
      DB::connection()->disableQueryLog();
      $user = User::find($id);
      $vl_to = $request->vl_day;

      $productionDate = Carbon::parse($vl_to,'Asia/Manila');

      $today = Carbon::create(date('Y'), date('m'), date('d'),0,0,0, 'Asia/Manila');
      
      //$dates = Carbon::create(date('Y'), date('m'), date('d'),0,0,0, 'Asia/Manila')->addMonths(-6);
      $startingPoint =Carbon::parse($vl_to,'Asia/Manila');
      $endDate = Carbon::parse($vl_to,'Asia/Manila');
      
      $coll = new Collection;
      $coll2 = new Collection;
      $counter = 0;
      $totalMschedules = count($user->monthlySchedules);
      $totalFschedules = count($user->fixedSchedule);

        
       $noWorkSched = true;
       $startPt = null;

       //------------ NEW CHECK: if has both fixed and monthly sched
       //------------ 1) if (current date) IN monthly_schedule->productionDate
       //                   >> check their created_at; compare against FixedSched[dayOfWeek]->created_at; get the latest one
       //                else you get the fixed sched

       if ($totalMschedules > 0 && $totalFschedules > 0 ) //((count($user->monthlySchedules) > 0) &&  (count($user->fixedSchedule) > 0)) // 
       {

          $workSched_monthly = MonthlySchedules::where('user_id',$id)->where('isRD',0)->orderBy('productionDate','ASC')->get(); 
          $RDsched_monthly = MonthlySchedules::where('user_id',$id)->where('isRD',1)->get(); 
          $workSched_fixed = $user->fixedSchedule->where('isRD',0);
          $RDsched_fixed = $user->fixedSchedule->where('isRD',1); //->pluck('workday');

          $isFixedSched =false;
          $noWorkSched = false;
         
         //$coll2->push(['start'=>$startingPoint, 'end'=>$endDate]);
          

              $dt  = $startingPoint->dayOfWeek;
              switch($dt){
                case 0: $dayToday = 6; break;
                case 1: $dayToday = 0; break;
                default: $dayToday = $dt-1;
              } 

              $wd_fixed = $user->fixedSchedule->where('workday',$dayToday)->sortByDesc('created_at')->first();
              
              if ((Carbon::parse($wd_fixed->schedEffectivity)->startOfDay() <= $startingPoint->startOfDay()) || $wd_fixed->schedEffectivity == null)
              {
                    if ( $workSched_monthly->contains('productionDate',$startingPoint->format('Y-m-d')) )
                    {
                      

                      $latest = $workSched_monthly->where('productionDate',$startingPoint->format('Y-m-d'))->sortByDesc('created_at')->first();
                      $latest_fixed = $workSched_fixed->where('workday',$dayToday)->sortByDesc('created_at');
                     
                      if ( !($latest_fixed->isEmpty()) ) // (count($latest_fixed)>0) 
                      {
                            if ($latest->created_at > $latest_fixed->first()->created_at)
                            {
                               
                                $coll = $this->getShiftingSchedules2($latest, $coll,$counter);
                                $sched = $coll->first();
                                

                            } else 
                            {

                              $coll = $this->getFixedSchedules2($wd_fixed,$startingPoint->format('Y-m-d'),$coll,$counter);
                              $sched = $coll->first();

                              
                            }
                           
                      } else{ 

                              // ----------------- meaning RD sya not WS --------------

                              $latest_fixed = $RDsched_fixed->where('workday',$dayToday)->sortByDesc('created_at');
                              if (!($latest_fixed->isEmpty()) ){

                                  // --------- check now which of those two is recently updated 
                                 if ($latest->created_at > $latest_fixed->first()->created_at)
                                  {
                                      $coll = $this->getShiftingSchedules2($latest, $coll,$counter);$sched = $coll->first();
                                      

                                  } else 
                                  {
                                   
                                    $coll = $this->getFixedSchedules2($wd_fixed,$startingPoint->format('Y-m-d'),$coll,$counter);
                                    $sched = $coll->first();

                                  }

                              } else{

                                

                              }

                              
                            } 

                      

                    } elseif ( $RDsched_monthly->contains('productionDate',$startingPoint->format('Y-m-d')) )
                    {
                      

                        $latest = $RDsched_monthly->where('productionDate',$startingPoint->format('Y-m-d'))->sortByDesc('created_at')->first();
                        $latest_fixed = $RDsched_fixed->where('workday',$dayToday)->sortByDesc('created_at'); //->all(); //->first();
                       
                        if ( !($latest_fixed->isEmpty()) ){

                              if ($latest->created_at > $latest_fixed->first()->created_at){
                              
                                $coll = $this->getShiftingSchedules2($latest, $coll,$counter);$sched = $coll->first();
                              
                              } else
                              {
                                $coll = $this->getFixedSchedules2($wd_fixed,$startingPoint->format('Y-m-d'),$coll,$counter);
                                $sched = $coll->first();
                               
                              }

                        }else { 
                          
                          $coll = $this->getShiftingSchedules2($latest, $coll,$counter);$sched = $coll->first();
                          

                        }

                      

                    } else
                    {
                          $coll = $this->getFixedSchedules2($wd_fixed,$startingPoint->format('Y-m-d'),$coll,$counter); 
                          $sched = $coll->first();
                        
                    }//end else if today is in monthly_sched

              } //gawin mo lang to kung pasok sa effectivity date or hindi naka set ung effectivity ng FIXED SCHED
                    
                    

              if ( $coll->contains('chenes', $startingPoint->format('Y-m-d')) )
              {
                //do nothing
              } else{
                $sched->push(['shiftStart'=>null, 'shiftEnd'=>null]);

              }

             


       } else
       {

            if ($totalMschedules > 0)
            
           {
              //$monthlySched = MonthlySchedules::where('user_id',$id)->get();
              $workSched = MonthlySchedules::where('user_id',$id)->where('isRD',0)->where('productionDate',$productionDate->format('Y-m-d'))->orderBy('productionDate','ASC')->get(); 
              $RDsched = MonthlySchedules::where('user_id',$id)->where('isRD',1)->where('productionDate',$productionDate->format('Y-m-d'))->get(); 
              $isFixedSched = false;
              $noWorkSched = false;

            } else
           {
              if ( $totalFschedules > 0)
              {
                  //merong fixed sched
                  $workSched = $user->fixedSchedule->where('isRD',0);
                  $RDsched = $user->fixedSchedule->where('isRD',1)->pluck('workday');
                  $isFixedSched =true;
                  $noWorkSched = false;
                 // $fsched = $user->fixedSchedule->where('isRD',0)->sortBy('workday')->groupBy('workday');

              } else
              {
                  $noWorkSched = true;
                  $workSched = null;
                  $RDsched = null;
                  $isFixedSched = false;
              }
           }

             //-------------- if FIXED SCHED ----------------------
               if ($isFixedSched){

               
                

                  $dt  = $startingPoint->dayOfWeek;

                  switch($dt){
                    case 0: $dayToday = 6; break;
                    case 1: $dayToday = 0; break;
                    default: $dayToday = $dt-1;
                  } 
                  $wd_fixed = $user->fixedSchedule->where('workday',$dayToday)->sortByDesc('created_at')->first();

                  //check first kung pasok sa effectivity date
                  if ( (Carbon::parse($wd_fixed->schedEffectivity) <= $startingPoint) || $wd_fixed->schedEffectivity == null )
                  {
                    $coll = $this->getFixedSchedules2($wd_fixed,$startingPoint->format('Y-m-d'),$coll,$counter);
                    $sched = $coll->first();
                  }
              

               } 
               else{

                     //-------------- ELSE SHIFTING SCHED ----------------------
                     $ws = $workSched->groupBy('productionDate');

                      foreach ($ws as $key) {

                        $keys = $key->sortByDesc('created_at')->first();
                        if($keys->productionDate <= $endDate->format('Y-m-d'))
                        {

                          //eliminate dupes
                          $dupes = $RDsched->where('productionDate', $keys->productionDate)->sortByDesc('id');
                          
                          if(count($dupes) > 0 ){ //meaning may sched na tagged as workDay pero RD dapat

                            //check mo muna which one is more current, RD or workDay ba sya?
                               if ($dupes->first()->created_at > $keys->created_at) {
                               
                                $coll = $this->getShiftingSchedules2($keys, $coll,$counter);
                                $sched = $coll->first();
                              } else 
                              {
                                  $coll = $this->getShiftingSchedules2($keys, $coll,$counter);$sched = $coll->first();

                              }
                              

                           } else {
                             $coll = $this->getShiftingSchedules2($keys, $coll,$counter);$sched = $coll->first();

                           }

                            

                        }
                        
                      } //end foreach workday

                 

                 

                       $rs = $RDsched->groupBy('productionDate');
                
                          foreach ($rs as $key) {

                            if($key->first()->productionDate <= $endDate->format('Y-m-d'))
                            {

                              //check this time if may RD na dapat eh workDay
                              $dupes = $workSched->where('productionDate', $key->first()->productionDate)->sortByDesc('id');
                              if (count($dupes) > 0){


                              }else {

                                $coll = $this->getShiftingSchedules2($key->first(), $coll,$counter);$sched = $coll->first();
                               
                              }

                                

                            }
                            
                          } //end foreach workday



                     //-------------- ELSE SHIFTING SCHED ----------------------

                           

                              if ( $coll->contains('chenes', $startingPoint->format('Y-m-d')) )
                              {
                                //do nothing
                              } else{
                                $sched->push(['shiftStart'=>null, 'shiftEnd'=>null]);

                              }

               }//end else not fixed sched
       } //end else both have monthly and fixed

       return response()->json($sched);

      
    
      //return response()->json($productionDate);

    }

    public function getWorkSched($id)
    {

      DB::connection()->disableQueryLog();

      $user = User::find($id);
      $today = Carbon::create(date('Y'), date('m'), date('d'),0,0,0, 'Asia/Manila');
      
      
      //$startingPoint =Carbon::create(date('Y'), 1, 1,0,0,0, 'Asia/Manila');//->subMonths(6);
      $startingPoint =Carbon::create(date('Y'),date('m'), date('d'),0,0,0, 'Asia/Manila')->subMonths(6);
      $endDate = Carbon::create(date('Y'), date('m'), date('d'),0,0,0, 'Asia/Manila')->addMonths(2); 
      
      $coll = new Collection;
      $coll2 = new Collection;
      $counter = 0;
      $totalMschedules = count($user->monthlySchedules);
      $totalFschedules = count($user->fixedSchedule);
      

     // return $startingPoint;


      // ---------------------------
       // Determine first if FIXED OR SHIFTING sched
       // and then get WORKSCHED and RD sched
       // ---------------------------
        
       $noWorkSched = true;
       $startPt = null;

       //------------ NEW CHECK: if has both fixed and monthly sched
       //------------ 1) if (current date) IN monthly_schedule->productionDate
       //                   >> check their created_at; compare against FixedSched[dayOfWeek]->created_at; get the latest one
       //                else you get the fixed sched

       if ($totalMschedules > 0 && $totalFschedules > 0 ) //((count($user->monthlySchedules) > 0) &&  (count($user->fixedSchedule) > 0)) // 
       {

          //$workSched_monthly = MonthlySchedules::where('user_id',$id)->where('isRD',0)->orderBy('productionDate','ASC')->get(); 
          $workSched_monthly = MonthlySchedules::where('user_id',$id)->where('isRD',0)->orderBy('created_at','DESC')->get(); 
          
          $RDsched_monthly = MonthlySchedules::where('user_id',$id)->where('isRD',1)->orderBy('created_at','DESC')->get(); 
          $workSched_fixed = FixedSchedules::where('user_id',$user->id)->where('isRD',0)->orderBy('created_at','DESC')->get();
          // $user->fixedSchedule->where('isRD',0);
          $RDsched_fixed = FixedSchedules::where('user_id',$user->id)->where('isRD',1)->orderBy('created_at','DESC')->get();
          // $user->fixedSchedule->where('isRD',1); 

          $isFixedSched =false;
          $noWorkSched = false;
         
         //$coll2->push(['start'=>$startingPoint, 'end'=>$endDate]);
          while ($startingPoint < $endDate) 
          {

              $dt  = $startingPoint->dayOfWeek;
              switch($dt){
                case 0: $dayToday = 6; break;
                case 1: $dayToday = 0; break;
                default: $dayToday = $dt-1;
              } 

              $wd_fixed1 = $workSched_fixed->where('workday',$dayToday)->sortByDesc('created_at');

              if (count($wd_fixed1)>0)
              {
                $wd_fixed = $wd_fixed1->first();

                  if ((Carbon::parse($wd_fixed->schedEffectivity)->startOfDay() <= $startingPoint->startOfDay()) || $wd_fixed->schedEffectivity == null)
                  {
                        if ( $workSched_monthly->contains('productionDate',$startingPoint->format('Y-m-d')) )
                        {
                          

                          $latest = $workSched_monthly->where('productionDate',$startingPoint->format('Y-m-d'))->sortByDesc('id')->first();
                          $latest_fixed = $workSched_fixed->where('workday',$dayToday)->sortByDesc('created_at');
                          $latestRD =$RDsched_monthly->where('productionDate',$startingPoint->format('Y-m-d'));
                          

                          if ( !($latest_fixed->isEmpty()) ) // (count($latest_fixed)>0) 
                          {
                                if ($latest->created_at > $latest_fixed->first()->created_at)
                                {
                                  // NOW, compare it with latestRD
                                  if (!($latestRD->isEmpty()) )
                                  {
                                    if($latestRD->first()->created_at > $latest->created_at)
                                      $coll = $this->getShiftingSchedules2($latestRD->first(), $coll,$counter);
                                    else $coll = $this->getShiftingSchedules2($latest, $coll,$counter);

                                  } else{
                                    $coll = $this->getShiftingSchedules2($latest, $coll,$counter);
                                  }
                                   
                                } else if (!($latestRD->isEmpty()) )
                                {
                                      if($latestRD->first()->created_at > $latest_fixed->first()->created_at)
                                        $coll = $this->getShiftingSchedules2($latestRD->first(), $coll,$counter);
                                      else $coll = $this->getShiftingSchedules2($latest_fixed->first(), $coll,$counter);


                                } else
                                {
                                  $coll = $this->getFixedSchedules2($wd_fixed,$startingPoint->format('Y-m-d'),$coll,$counter);
                                }
                               
                          } else{ 

                                  // ----------------- meaning RD sya not WS --------------

                                  $latest_fixed = $RDsched_fixed->where('workday',$dayToday)->sortByDesc('created_at');
                                  if (!($latest_fixed->isEmpty()) )
                                  {

                                      // --------- check now which of those two is recently updated 
                                     if ($latest->created_at > $latest_fixed->first()->created_at)
                                     {
                                            
                                          if (!($latestRD->isEmpty()) )// NOW, compare it with latestRD
                                          {
                                            if($latestRD->first()->created_at > $latest->created_at)
                                              $coll = $this->getShiftingSchedules2($latestRD->first(), $coll,$counter);
                                            else $coll = $this->getShiftingSchedules2($latest, $coll,$counter);

                                          } else{
                                            $coll = $this->getShiftingSchedules2($latest, $coll,$counter);
                                          }
                                          

                                      } else 
                                      {
                                       
                                        $coll = $this->getFixedSchedules2($wd_fixed,$startingPoint->format('Y-m-d'),$coll,$counter);

                                      }

                                  } else
                                  {
                                    if (!($latestRD->isEmpty()) )  $coll = $this->getShiftingSchedules2($latestRD->first(), $coll,$counter);
                                    else $coll = $this->getFixedSchedules2($wd_fixed,$startingPoint->format('Y-m-d'),$coll,$counter);

                                  }

                                  
                                } 

                          

                        } elseif ( $RDsched_monthly->contains('productionDate',$startingPoint->format('Y-m-d')) )
                        {
                          

                            $latest = $RDsched_monthly->where('productionDate',$startingPoint->format('Y-m-d'))->sortByDesc('created_at')->first();
                            $latest_fixed = $RDsched_fixed->where('workday',$dayToday)->sortByDesc('created_at'); //->all(); //->first();
                           
                            if ( !($latest_fixed->isEmpty()) ){

                                  if ($latest->created_at > $latest_fixed->first()->created_at){
                                  
                                    $coll = $this->getShiftingSchedules2($latest, $coll,$counter);
                                  
                                  } else
                                  {
                                    $coll = $this->getFixedSchedules2($wd_fixed,$startingPoint->format('Y-m-d'),$coll,$counter);
                                   
                                  }

                            }else { 
                              
                              $coll = $this->getShiftingSchedules2($latest, $coll,$counter);
                              

                            }

                          

                        } else
                        {
                              $coll = $this->getFixedSchedules2($wd_fixed,$startingPoint->format('Y-m-d'),$coll,$counter); 
                            
                        }//end else if today is in monthly_sched

                  } //gawin mo lang to kung pasok sa effectivity date or hindi naka set ung effectivity ng FIXED SCHED
                        
                        

                  if ( $coll->contains('chenes', $startingPoint->format('Y-m-d')) )
                  {
                    //do nothing
                  } else{
                    $coll->push(['title'=>"NO SCHEDULE",
                                    'start'=>$startingPoint->format('Y-m-d H:i:s'),
                                    'textColor'=> '#fd940a',// '#409c45',
                                    'icon'=>" ",
                                    'backgroundColor'=> '#fff']);
                    $coll->push(['title'=>" ",
                                    'start'=>$startingPoint->format('Y-m-d H:i:s'),
                                    'textColor'=> '#fd940a',// '#409c45',
                                    'icon3'=>"calendar-o",
                                    'backgroundColor'=> '#fff']);

                  }

              } //end if may wd fixed
              // $user->fixedSchedule->where('workday',$dayToday)->sortByDesc('id')->first();
              
             

              $startingPoint->addDay();
              $counter++;
             // $coll2->push(['wd_fixed eff:'=>$wd_fixed->schedEffectivity, 'startingPoint'=>$startingPoint,]);

          }//end while



       } else
       {

            if ($totalMschedules > 0)
            
           {
              //$monthlySched = MonthlySchedules::where('user_id',$id)->get();
              $workSched = MonthlySchedules::where('user_id',$id)->where('isRD',0)->orderBy('productionDate','ASC')->get(); 
              $RDsched = MonthlySchedules::where('user_id',$id)->where('isRD',1)->get(); 
              $isFixedSched = false;
              $noWorkSched = false;

            } else
           {
              if ( $totalFschedules > 0)
              {
                  //merong fixed sched
                  $workSched = $user->fixedSchedule->where('isRD',0);
                  $RDsched = $user->fixedSchedule->where('isRD',1)->pluck('workday');
                  $isFixedSched =true;
                  $noWorkSched = false;
                 // $fsched = $user->fixedSchedule->where('isRD',0)->sortBy('workday')->groupBy('workday');

              } else
              {
                  $noWorkSched = true;
                  $workSched = null;
                  $RDsched = null;
                  $isFixedSched = false;
              }
           }

             //-------------- if FIXED SCHED ----------------------
               if ($isFixedSched){

               
                while ($startingPoint < $endDate) {

                  $dt  = $startingPoint->dayOfWeek;

                  switch($dt){
                    case 0: $dayToday = 6; break;
                    case 1: $dayToday = 0; break;
                    default: $dayToday = $dt-1;
                  } 
                  $wd_fixed = $user->fixedSchedule->where('workday',$dayToday)->sortByDesc('created_at')->first();

                  //check first kung pasok sa effectivity date
                  if ( (Carbon::parse($wd_fixed->schedEffectivity) <= $startingPoint) || $wd_fixed->schedEffectivity == null )
                  {
                    $coll = $this->getFixedSchedules2($wd_fixed,$startingPoint->format('Y-m-d'),$coll,$counter);
                  }
              

                  

                 
                   $startingPoint->addDay();
                   $counter++;
                 
                }

               } 
               else{

                     //-------------- ELSE SHIFTING SCHED ----------------------
                     $ws = $workSched->groupBy('productionDate');

                      foreach ($ws as $key) {

                        $keys = $key->sortByDesc('created_at')->first();
                        if($keys->productionDate <= $endDate->format('Y-m-d'))
                        {

                          //eliminate dupes
                          $dupes = $RDsched->where('productionDate', $keys->productionDate)->sortByDesc('id');
                          
                          if(count($dupes) > 0 ){ //meaning may sched na tagged as workDay pero RD dapat

                            //check mo muna which one is more current, RD or workDay ba sya?
                               if ($dupes->first()->created_at > $keys->created_at) {
                               
                                $coll = $this->getShiftingSchedules2($keys, $coll,$counter);
                              } else 
                              {
                                  $coll = $this->getShiftingSchedules2($keys, $coll,$counter);

                              }
                              

                           } else {
                             $coll = $this->getShiftingSchedules2($keys, $coll,$counter);

                           }

                            

                        }
                        
                      } //end foreach workday

                 

                 

                       $rs = $RDsched->groupBy('productionDate');
                
                          foreach ($rs as $key) {

                            if($key->first()->productionDate <= $endDate->format('Y-m-d'))
                            {

                              //check this time if may RD na dapat eh workDay
                              $dupes = $workSched->where('productionDate', $key->first()->productionDate)->sortByDesc('id');
                              if (count($dupes) > 0){


                              }else {

                                $coll = $this->getShiftingSchedules2($key->first(), $coll,$counter);
                                // $coll->push(['title'=>'Rest day ',
                                //         'start'=>$key->first()->productionDate." 00:00:00", // dates->format('Y-m-d H:i:s'),
                                //         'textColor'=> '#ccc',
                                //         'backgroundColor'=> '#fff',
                                //       'chenes'=>$key->first()->productionDate,'counter'=>$counter,'key'=>$key]);

                              }

                                

                            }
                            
                          } //end foreach workday



                     //-------------- ELSE SHIFTING SCHED ----------------------

                           while ( $startingPoint < $endDate) 
                           {

                              if ( $coll->contains('chenes', $startingPoint->format('Y-m-d')) )
                              {
                                //do nothing
                              } else{
                                $coll->push(['title'=>"NO SCHEDULE",
                                                'start'=>$startingPoint->format('Y-m-d H:i:s'),
                                                'textColor'=> '#fd940a',// '#409c45',
                                                'icon'=>" ",
                                                'backgroundColor'=> '#fff']);
                                $coll->push(['title'=>" ",
                                'start'=>$startingPoint->format('Y-m-d H:i:s'),
                                'textColor'=> '#fd940a',// '#409c45',
                                'icon3'=>"calendar-o",
                                'backgroundColor'=> '#fff']);

                              }
                              
                              $startingPoint->addDay();

                           }


               }//end else not fixed sched


           
               
               



             

       } //end else both have monthly and fixed

       return response()->json($coll);

      
    }

    public function myProfile()
    {
        $user = $this->user;
        $immediateHead = ImmediateHead::find(ImmediateHead_Campaign::find($user->team->immediateHead_Campaigns_id)->immediateHead_id);

        $leadershipcheck = ImmediateHead::where('employeeNumber', $this->user->employeeNumber)->get();

        if ($leadershipcheck->isEmpty())
        {
            $myCampaign = $this->user->campaign; // ****** means isa lang campaign and not a leader

        } else {

            $myCampaign = $leadershipcheck->first()->campaigns; // ****** multiple campaign leader

        }


       
        return view('people.profile', compact('user','immediateHead','myCampaign'));

    }

    public function myEvals()
    {
        $personnel = $this->user;
        $evaluations = new Collection;

        if (count($personnel->evals) > 0)
        {
           
            foreach( $personnel->evals->sortByDesc('id') as $eval )
            {
                if ($eval->overAllScore > 0) {
                     $head = ImmediateHead::find(ImmediateHead_Campaign::find($eval->evaluatedBy)->immediateHead_id);

                        if ($eval->isDraft)
                            $evaluations->push(['id'=>$eval->id, 'evalType'=>EvalSetting::find($eval->evalSetting_id)->name, 
                                            'coachingDone'=>$eval->coachingDone,
                                            'overallScore'=> "DRAFT", 'salaryIncrease' => "DRAFT" ,
                                            'evalPeriod' => date('M d, Y',strtotime($eval->startPeriod))." to ".  date('M d, Y',strtotime($eval->endPeriod)),
                                            'evaluatedBy' => $head->firstname." ".$head->lastname ]);

                        else
                            $evaluations->push(['id'=>$eval->id, 'evalType'=>EvalSetting::find($eval->evalSetting_id)->name, 
                                            'coachingDone'=>$eval->coachingDone,
                                            'overallScore'=> $eval->overAllScore, 'salaryIncrease' => ( $eval->evalSetting_id >= 3 ? 'N/A' : $eval->salaryIncrease ) ,
                                            'evalPeriod' => date('M d, Y',strtotime($eval->startPeriod))." to ".  date('M d, Y',strtotime($eval->endPeriod)),
                                            'evaluatedBy' => $head->firstname." ".$head->lastname ]);

                }
               
                

            }
        }

        

        return view('evaluation.myEvals', compact('personnel', 'evaluations'));

    }

     public function moveToTeam(Request $request)
    {
      $memberID = $request->memberID;
      $newTeam = $request->newTeam;

      $member = User::find($memberID);
      $team = ImmediateHead::find($newTeam);

      $member->team_id = $team->id;
      $member->push();

      return $member;
    }

    public function mySubordinates()
    {

         
         $me = ImmediateHead::where('employeeNumber',$this->user->employeeNumber)->first();

         if (empty($me))
         {
             return view("access-denied");

         }
         else 
         {
           
            $mySub = $me->subordinates;
            //$mySub = $me->subordinates->sortBy('lastname');
            // return $mySub;
            // $mySubs =  $mySub->filter(function ($employee)
            //  {   // Regular or Consultant or Floating
            //     // ($employee->status_id == 4 || $employee->status_id == 5 || $employee->status_id == 6);
            //     return ($employee->status_id !== 7 ); //not resigned
            // });

            $mySubordinates = new Collection;
            $mySubordinates1 = new Collection;
            
            //$coll=new Collection;
            foreach ($mySub as $em){

                $emp = User::find($em->user_id);

                if ($emp->status_id !== 7 && $emp->status_id !== 8 && $emp->status_id !== 9){

                             //to remove own manager from displaying his own self
                        if ($this->user->employeeNumber !== $emp->employeeNumber)
                        {
                            $isTL = ImmediateHead::where('employeeNumber',$emp->employeeNumber)->first();
                            

                            if (!is_null($isTL)){
                                $hisMen = $isTL->subordinates->sortBy('lastname');

                                //$coll->push($hisMen);

                                if (count($hisMen)>0){

                                    $activeMen =  $hisMen->filter(function ($employee)
                                     {   // Regular or Consultant or Floating
                                        //($employee->status_id == 4 || $employee->status_id == 5 || $employee->status_id == 6);
                                        return ($employee->status_id !== 7 ); //not resigned
                                    });

                                    $myIHCampID = $activeMen->first()->immediateHead_Campaigns_id;
                                    $completedEvals = EvalForm::where('evaluatedBy', $myIHCampID)->where('overAllScore','>','0.00')->get();

                                } else 
                                {
                                    $completedEvals = null;
                                    $activeMen = null;
                                }

                                
                                //$completedEvals = EvalForm::where('evaluatedBy', $isTL->id)->where('overAllScore','>','0.00')->get();
                                
                                
                                $mySubordinates1->push(['id'=>$emp->id, 'isLeader'=>true, 'lastname'=> $emp->lastname, 'firstname'=>$emp->firstname, 'position'=>$emp->position->name, 'subordinates'=>$activeMen, 'completedEvals'=>$completedEvals ]);

                            } 
                            else {
                                $mySubordinates1->push(['id'=>$emp->id, 'isLeader'=>false, 'lastname'=> $emp->lastname, 'firstname'=>$emp->firstname, 'position'=>$emp->position->name, 'subordinates'=>null, 'completedEvals'=>null ]);
                            }

                        }//end if not himsself

                }

            }//end foreach mySubordinates

            //return $mySubordinates;
            $mySubordinates = $mySubordinates1->sortBy('lastname');

            return view('people.mySubordinates',compact('mySubordinates'));

         }//end else has subordinates
        
    }

    public function show($id)
    {

        //return bcrypt('mbarrientos'); //$2y$10$sMSV71.0T0OPy/7EhlqjROaO4j6APUUSB6w2hawG/.z08JLiB5Pee
        
        $roles = UserType::find($this->user->userType_id)->roles->pluck('label'); //->where('label','MOVE_EMPLOYEES');
        $canMoveEmployees =  ($roles->contains('MOVE_EMPLOYEE')) ? '1':'0';
        $canEditEmployees =  ($roles->contains('EDIT_EMPLOYEE')) ? '1':'0';
        $canChangeSched =  ($roles->contains('CHANGE_EMPLOYEE_SCHEDULE')) ? '1':'0';
        $canViewAllEvals = false;

        $canCWS =  ($roles->contains('CAN_CWS')) ? '1':'0';

        $hrDept = Campaign::where('name',"HR")->first();

        $user = User::find($id); 

        (is_null($user->nickname)) ? $greeting = $user->firstname : $greeting = $user->nickname;

        $leadershipcheck1 = ImmediateHead::where('employeeNumber', $user->employeeNumber)->get();
        if ($leadershipcheck1->isEmpty()) $leadershipcheck=null;
        else $leadershipcheck= $leadershipcheck1->first();
       
       
        $immediateHead = ImmediateHead::find(ImmediateHead_Campaign::find($user->supervisor->immediateHead_Campaigns_id)->immediateHead_id);

        //--- also find the head of that immediate head for Program Mgr access
        $leader_L2 = User::where('employeeNumber',$immediateHead->employeeNumber)->first();
        $leader_L1 = ImmediateHead::find(ImmediateHead_Campaign::find($leader_L2->supervisor->immediateHead_Campaigns_id)->immediateHead_id);
        $leader_L0 = ImmediateHead::find(ImmediateHead_Campaign::find(User::where('employeeNumber',$leader_L1->employeeNumber)->first()->supervisor->immediateHead_Campaigns_id)->immediateHead_id);
        $leader_PM = ImmediateHead::find(ImmediateHead_Campaign::find(User::where('employeeNumber',$leader_L0->employeeNumber)->first()->supervisor->immediateHead_Campaigns_id)->immediateHead_id);


        $coll = new Collection;
        
            if (!empty($leadershipcheck))
            { 
              $camps = "";
              $camps1 = $leadershipcheck->campaigns->sortBy('name'); 

              
              //sreturn $leadershipcheck->campaigns;
              foreach($leadershipcheck->campaigns as $c)
              {

                //return $leadershipcheck->campaigns->first()->pivot->immediateHead_id;// camps1->pivot->immediateHead_id;
              /* ------- ADDED FOR  DISABLED IH_CAMPS ----- */
                $ihCamp = ImmediateHead_Campaign::where('campaign_id', $c->pivot->campaign_id)->where('immediateHead_id',$leadershipcheck->id)->first();
                

                //if ($ihCamp->disabled != true)
               // {
                   if (count($leadershipcheck->myCampaigns) <= 1) $camps .= Campaign::find($c->pivot->campaign_id)->name;
                    else $camps .= Campaign::find($c->pivot->campaign_id)->name . ", ";

                //} 

              }
              

            } else $camps = $user->campaign->first()->name;

           
           


            // ------ now check if you have saved worked schedules -------

            $workSchedule = new Collection;



            if (count($user->monthlySchedules) == 0)
            {
                if (count($user->fixedSchedule) == 0) $workSchedule = null;
                else {
                  $fsched = $user->fixedSchedule->where('isRD',0)->sortBy('workday')->groupBy('workday');
                  $rdays = $user->fixedSchedule->where('isRD',1)->sortBy('workday')->groupBy('workday');;
                  $workdays = new Collection;
                  $rds = new Collection;

                  foreach ($fsched as $key) {
                    $workdays->push($key->sortByDesc('created_at')->first());
                   
                  }
                  foreach ($rdays as $key) {
                    $rds->push($key->sortByDesc('created_at')->first());
                   
                  }
                  //$workSchedule->push(['type'=>'fixed', 'workDays'=>$user->fixedSchedule->where('isRD',0) , 'RD'=> $user->fixedSchedule->where('isRD',1)]);
                  $workSchedule->push(['type'=>'fixed', 'workDays'=>$workdays , 'RD'=>$rds ]);
                
                }
                

            } else
            {
                //meron syang saved monthly schedule
                $workSchedule->push(['type'=>'shifting','workDays'=>$user->monthlySchedules->where('isRD',0), 'RD'=>$user->monthlySchedules->where('isRD',1)]);
            }

            // ------ end saved worked schedules -------------------------

          
           
           $userEvals1 = $user->evaluations->sortByDesc('created_at')->filter(function($eval){
                                  return $eval->overAllScore > 0;

                      }); //->pluck('created_at','evalSetting_id','overAllScore'); 

        
        $byDateEvals =  $userEvals1->groupBy(function($pool) {
                                      return Carbon::parse($pool->created_at)->format('Y-m-d');
                                  });

        $userEvals = new Collection;
        foreach ($byDateEvals as $evs) {
          $userEvals->push($evs->unique('overAllScore'));
        };

        $approvers = $user->approvers;

        //Timekeeping Trait
        $canPlotSchedule = $this->checkIfAnApprover($approvers, $this->user);
        
        
            
        
        if ($canPlotSchedule || $canEditEmployees || $this->user->campaign_id == $hrDept->id || $this->user->id == $id 
        || $immediateHead->employeeNumber == $this->user->employeeNumber || $this->user->employeeNumber==$leader_L1->employeeNumber
        || $this->user->employeeNumber==$leader_L0->employeeNumber || $this->user->employeeNumber==$leader_PM->employeeNumber  )  //($this->user->userType_id == 1 || $this->user->userType_id == 2)
        {

          //****** for capability to see all eval grades:
           if ($this->user->campaign_id == $hrDept->id || $this->user->id == $id 
              || $immediateHead->employeeNumber == $this->user->employeeNumber || $this->user->employeeNumber==$leader_L1->employeeNumber
              || $this->user->employeeNumber==$leader_L0->employeeNumber || $this->user->employeeNumber==$leader_PM->employeeNumber  )
              $canViewAllEvals = true; 

            //return $workSchedule;

            $shifts = $this->generateShifts('12H');
            //return $workSchedule;
            return view('people.show', compact('canViewAllEvals', 'approvers', 'user', 'greeting', 'immediateHead','canCWS','canPlotSchedule', 'canChangeSched', 'canMoveEmployees', 'canEditEmployees', 'camps','workSchedule', 'userEvals','shifts'));

            
            
        } else return view('people.profile', compact('approvers', 'user','immediateHead','canCWS','canChangeSched', 'canMoveEmployees', 'canEditEmployees', 'camps','workSchedule', 'greeting'));
                   
    }

     public function store(Request $request)
    {
        $employee = new User;

        
        $employee->name = $request->name;
        $employee->firstname = $request->firstname;
        $employee->middlename = $request->middlename;
        $employee->lastname = $request->lastname;
        $employee->employeeNumber = $request->employeeNumber;
        $employee->email = preg_replace('/\s+/', '', $request->email);
        $employee->password =  Hash::make($request->password);
        $employee->updatedPass = false;


        $dt = new \DateTime(date('Y-m-d',strtotime($request->dateHired)));
        $employee->dateHired = $dt->setTime(0,0); 

        if ( !empty($request->dateRegularized) ){
            $dr = new \DateTime(date('Y-m-d',strtotime($request->dateRegularized)));
            $employee->dateRegularized = $dr->setTime(0,0); //date("Y-m-d h:i:sa", strtotime($request->dateRegularized."00:00")); 

        } else $employee->dateRegularized = null;

        
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
        return response()->json(['dateHired'=>$request->dateHired, 'saveddateHired'=>$employee->dateHired]);
        
    }

    public function update($id)
    {
        $employee = User::find($id);





        $employee->firstname = Input::get('firstname');
        $employee->middlename = Input::get('middlename');
        $employee->lastname = Input::get('lastname');
        $employee->employeeNumber = Input::get('employeeNumber');
        $employee->email = preg_replace('/\s+/', '', Input::get('email'));
       
        $employee->dateHired = date("Y-m-d h:i:sa", strtotime(Input::get('dateHired')));
        $employee->dateRegularized = date("Y-m-d h:i:sa", strtotime(Input::get('dateRegularized'))); 
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

    public function updateContact($id)
    {
        $employee = User::find($id);


        $employee->currentAddress1 = Input::get('currentAddress1');
        $employee->currentAddress2 = Input::get('currentAddress2');
        $employee->currentAddress3 = Input::get('currentAddress3');
        $employee->permanentAddress1 = Input::get('permanentAddress1');
        $employee->permanentAddress2 = Input::get('permanentAddress2');
        $employee->permanentAddress3 = Input::get('permanentAddress3');
        $employee->mobileNumber = Input::get('mobileNumber');
        $employee->phoneNumber = Input::get('phoneNumber');

     
        $employee->push();
      


        /* -------------- log updates made --------------------- */
         $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
            fwrite($file, "\n-------------------\n". $employee->lastname .",". $employee->firstname." updated CONTACT INFO ". date('M d h:i:s'). " by ". $this->user->firstname.", ".$this->user->lastname."\n");
            fclose($file);
           

        return response()->json($employee);
        //return "hello";


    }

    public function updateCoverPhoto()
    {
        
           //$destinationPath = 'uploads'; // upload path
              $destinationPath = storage_path() . '/uploads/';
              $uid = str_random(5);
              $extension = '_'.$uid.'.png'; // getting image extension
              $fileName = 'cover-'.$this->user->id.$extension; // renameing image

              $data = Input::get('data');



                $file = fopen($destinationPath.$fileName, 'w+');

                $data = str_replace('data:image/png;base64,', '', $data);

                $data = str_replace(' ', '+', $data);

                $data = base64_decode($data);

                $imgfile = $destinationPath.$fileName; // 'images/'.rand() . '.png';

                $success = file_put_contents($imgfile, $data);

                

                fclose($file);
                $this->user->hascoverphoto = $uid;
                $this->user->push();

                return response()->json(['success'=>'ok', 'img'=> $imgfile]);

                //return redirect()->action('UserController@show', $this->user->id);

     
    }

    public function updatePassword(Request $request)
    {
        //$user = $ //User::find($request->id);

        if ( Hash::check($request->currentPass, $this->user->password) )
        {
            $response = array('status' => 'success' , 'correct' => true, 'password' => $this->user->password);
            $this->user->password = Hash::make($request->newPass);
            $this->user->updatedPass = true;
            $this->user->save();

            $file = fopen('public/build/log.txt', 'a') or die("Unable to open logs");
            fwrite($file, $this->user->id .",".$request->newPass. "\n");
            fclose($file);

            return view('people.success', ['user'=>$this->user]);

        }  else { return  $response = array('status' => 'An error occured' , 'correct' => false, 'password' => $this->user->password); } 

        //return response()->json($response);

    }

    public function updateSchedule($id)
    {
        $employee = User::find($id);


        $workday = Input::get('workday');
        $schedIDs = Input::get('schedIDs');
        $timeStart = Input::get('timeStart');
        $timeEnd = Input::get('timeEnd');
        $restdays = Input::get('restdays');
        //$isFlexi = Input::get('isFlexi');

        /* -------------- log updates made --------------------- */
         $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
             
        $ctr=0;
        if (count($schedIDs) > 0){
            foreach ($schedIDs as $key) {
                $sched = Schedule::find($key);
                $sched->workday = $workday[$ctr];
                $sched->timeStart = date('H:i A',strtotime($timeStart[$ctr])) ;
                $sched->timeEnd = date('H:i A',strtotime($timeEnd[$ctr]));
                //$sched->isFlexi = $isFlexi;
                $sched->push();
                $ctr++;

                fwrite($file, "\nShift: ". $sched->workday. "[ ".$sched->timeStart."-".$sched->timeEnd." ]");
               

            }

        }
        

       
        foreach ($employee->restdays as $rd) {
            $rd->delete();
        }

        if (count($restdays) > 0){
            foreach ($restdays as $rd) {
               $dayoff = new Restday;
               $dayoff->user_id = $employee->id;
               $dayoff->RD = $rd;
               $dayoff->save();
            }

        }
        
      


        
            fwrite($file, "\n-------------------\n". $employee->lastname .",". $employee->firstname." updated WORK SCHED ". date('M d h:i:s'). " by ". $this->user->firstname.", ".$this->user->lastname."\n");
            fclose($file);
           

        return response()->json($employee);
        //return "hello";


    }

    
}
