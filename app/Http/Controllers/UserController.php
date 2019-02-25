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
      $wf = UserType::find($this->user->userType_id)->roles->where('label','STAFFING_MANAGEMENT');

      ($this->user->userType_id == 11) ? $wfAgent=true : $wfAgent=false;
      
      (count($canDoThis)> 0 ) ? $hasUserAccess=1 : $hasUserAccess=0;
      (count($wf) > 0) ? $isWorkforce=1 : $isWorkforce=0;
      
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
        return view('people.employee-index', compact('myCampaign', 'hasUserAccess','isWorkforce','wfAgent'));
    }

     public function index_inactive()
    {
       

      $myCampaign = $this->user->campaign; 
      $canDoThis = UserType::find($this->user->userType_id)->roles->where('label','EDIT_EMPLOYEE');
      if (count($canDoThis)> 0 ) $hasUserAccess=1; else $hasUserAccess=0;

        return view('people.employee-inactive', compact( 'hasUserAccess'));
    }

    public static function getVLcredits()
    {
      // $avail = $this->user->availableVL;
      // $today=Carbon::today();

      // if (count($avail)>0){

      // }else {
      //   $approvedVLs = User_VL::where('user_id',$this->user->id)->where('isApproved',1)->get();

      //   if (count($approvedVLs)>0){
      //     $bal = 0.0;
      //     foreach ($approvedVLs as $key) {
      //       $bal += $key->totalCredits;
      //     }

      //     $currentBalance = (0.84 * $today->format('m')) - $bal;

      //   }else{
      //     $currentBalance = (0.84 * $today->format('m'));
      //   }
        
      // }
      return $this->user->id;
      //return $currentBalance;
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

    public function createSchedule($id)
    {

        $user = User::find($id);

        $roles = UserType::find($this->user->userType_id)->roles->pluck('label'); 
        $canCWS =  ($roles->contains('CAN_CWS')) ? '1':'0';

        /* -------- get this user's department. If Backoffice, WFM can't access this ------*/
        $isBackoffice = ( Campaign::find(Team::where('user_id',$user->id)->first()->campaign_id)->isBackoffice ) ? true : false;
        $isWorkforce =  ($roles->contains('STAFFING_MANAGEMENT')) ? '1':'0';

        $approvers = $user->approvers;

        //Timekeeping Trait
        $canPlot = $this->checkIfAnApprover($approvers, $this->user);

        //if (!$canCWS || !$canPlot) 
        if ($canCWS || $canPlot 
          || ($isWorkforce && !$isBackoffice)
          || $this->user->userType_id==1 
          || $this->user->userType_id==2 
          || $this->user->userType_id==5)
        {
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

        }else return view('cws-denied');




        


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

     public function deleteThisUser($id)
    {
      $u = User::find($id);
      $u->delete();

      return redirect()->back();

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




    public function editUser($id, Request $request)
    {
        DB::connection()->disableQueryLog(); 

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

           

             /* ------- optimize ---------*/
            $leaders = DB::table('immediateHead_Campaigns')->
                join('immediateHead','immediateHead_Campaigns.immediateHead_id','=','immediateHead.id')->
                join('campaign','immediateHead_Campaigns.campaign_id','=','campaign.id')->
                leftJoin('users','immediateHead.employeeNumber','=', 'users.employeeNumber')->
                join('positions','positions.id','=','users.position_id')->
                select('immediateHead_Campaigns.id','positions.name as position','users.lastname','users.firstname','campaign.name as campaign','campaign.id as campaign_id')->
                orderBy('users.lastname','ASC')->
                where('users.status_id','!=',7)->
                where('users.status_id','!=',8)->
                where('users.status_id','!=',9)->
                get();


            //--- GENERATE TEAM MATES : UserTrait
            
            $teamMates = $this->getTeammates($id);

            //--end team mates

            //---- Check now if already has approver, if none set immediate head as approver
            $approvers = $personnel->approvers;
            $currentTLcamp = Campaign::where('id',ImmediateHead_Campaign::find($personnelTL_ihCampID)->campaign_id)->get();

           

            return view('people.employee-edit', compact('fromYr','canEditEmployees','canUpdateLeaves', 'page', 'approvers','teamMates', 'currentTLcamp', 'personnelTL_ihCampID', 'users','floors', 'userTypes', 'leaders', 'myCampaign', 'campaigns', 'personnel','personnelTL', 'statuses','changes', 'positions'));


        } 
       
    }

    public function getAllActiveUsers(){

        DB::connection()->disableQueryLog();
       

        /* ------- faster method ----------- */


        $users = DB::table('users')->where([
                    ['status_id', '!=', 7],
                    ['status_id', '!=', 8],
                    ['status_id', '!=', 9],
                ])->
        leftJoin('team','team.user_id','=','users.id')->
        leftJoin('campaign','team.campaign_id','=','campaign.id')->
        leftJoin('immediateHead_Campaigns','team.immediateHead_Campaigns_id','=','immediateHead_Campaigns.id')->
        leftJoin('immediateHead','immediateHead_Campaigns.immediateHead_id','=','immediateHead.id')->
        leftJoin('positions','users.position_id','=','positions.id')->
        leftJoin('floor','team.floor_id','=','floor.id')->
        select('users.id', 'users.firstname','users.lastname','users.nickname','users.dateHired','positions.name as jobTitle','campaign.id as campID', 'campaign.name as program','immediateHead.firstname as leaderFname','immediateHead.lastname as leaderLname','users.employeeNumber','floor.name as location')->orderBy('users.lastname')->get();

        return response()->json(['data'=>$users]);

         /* ------- faster method ----------- */        


        
        //return Datatables::collection($allUsers)->make(true);
       
    }

    public function getAllInactiveUsers(){

        DB::connection()->disableQueryLog();

        $all = User::orderBy('lastname', 'ASC')->get();

       /* ------- faster method ----------- */

        $users = DB::table('users')->where([
                    ['status_id', '!=', 1],
                    ['status_id', '!=', 2],
                    ['status_id', '!=', 3],
                    ['status_id', '!=', 4],
                    ['status_id', '!=', 5],
                    ['status_id', '!=', 6],
                    ['status_id', '!=', 10],
                    ['status_id', '!=', 11],
                    ['status_id', '!=', 12],
                ])->
        leftJoin('team','team.user_id','=','users.id')->
        leftJoin('campaign','team.campaign_id','=','campaign.id')->
        leftJoin('immediateHead_Campaigns','team.immediateHead_Campaigns_id','=','immediateHead_Campaigns.id')->
        leftJoin('immediateHead','immediateHead_Campaigns.immediateHead_id','=','immediateHead.id')->
        leftJoin('positions','users.position_id','=','positions.id')->
        leftJoin('statuses','users.status_id','=','statuses.id')->
        leftJoin('floor','team.floor_id','=','floor.id')->
        select('users.id', 'users.firstname','users.lastname','users.nickname','users.dateHired','positions.name as jobTitle','campaign.name as program','immediateHead.firstname as leaderFname','immediateHead.lastname as leaderLname','users.employeeNumber','statuses.name as employeeStatus')->orderBy('users.lastname','ASC')->get();

        return response()->json(['data'=>$users]);

         /* ------- faster method ----------- */  
        //return Datatables::collection($allUsers)->make(true);

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

    public function getMyRequests($id)
    {

     

      if (is_null($id))
      {
        $cws = User_CWS::where('user_id',$this->user->id)->orderBy('updated_at','DESC')->get();
        $dtrp = User_DTRP::where('user_id',$this->user->id)->where('logType_id',1)->orderBy('updated_at','DESC')->get();
        $dtrp_out = User_DTRP::where('user_id',$this->user->id)->where('logType_id',2)->orderBy('updated_at','DESC')->get();
        $ot = User_OT::where('user_id',$this->user->id)->orderBy('updated_at','DESC')->get();
        $vl = User_VL::where('user_id',$this->user->id)->orderBy('updated_at','DESC')->get();
        $sl = User_SL::where('user_id',$this->user->id)->orderBy('updated_at','DESC')->get();
        $lwop = User_LWOP::where('user_id',$this->user->id)->orderBy('updated_at','DESC')->get();
        $obt = User_OBT::where('user_id',$this->user->id)->orderBy('updated_at','DESC')->get();

        ($this->user->nickname !== null) ? $nickname = $this->user->nickname." ".$this->user->lastname : $nickname = $this->user->firstname." ".$this->user->lastname;


      } else{

        $user = User::find($id);
        $cws = User_CWS::where('user_id',$user->id)->orderBy('updated_at','DESC')->get();
        $dtrp = User_DTRP::where('user_id',$user->id)->where('logType_id',1)->orderBy('updated_at','DESC')->get();
        $dtrp_out = User_DTRP::where('user_id',$user->id)->where('logType_id',2)->orderBy('updated_at','DESC')->get();
        $ot = User_OT::where('user_id',$user->id)->orderBy('updated_at','DESC')->get();
        $vl = User_VL::where('user_id',$user->id)->orderBy('updated_at','DESC')->get();
        $sl = User_SL::where('user_id',$user->id)->orderBy('updated_at','DESC')->get();
        $lwop = User_LWOP::where('user_id',$user->id)->orderBy('updated_at','DESC')->get();
        $obt = User_OBT::where('user_id',$user->id)->orderBy('updated_at','DESC')->get();


        ($user->nickname !== null) ? $nickname = $user->nickname." ".$user->lastname : $nickname = $user->firstname." ".$user->lastname;
      }
      
      $requests = new Collection;

      /* ------------------------
         Determine if requests are still covered by this cutoff,
         if way past cutoff, you cannot revoke
        ---------------------------*/

        $currPeriod =  Cutoff::first()->getCurrentPeriod();
        $currentPeriod = explode('_', $currPeriod);
        $cutoffStart = new Carbon(Cutoff::first()->startingPeriod(),'Asia/Manila');
        //$cutoffEnd = new Carbon(Cutoff::first()->endingPeriod(),'Asia/Manila');
        $pastPayroll = $cutoffStart->subDays(15);
      

      /*------ CWS --------*/

      foreach ($cws as $key) {
        if (is_null($key->approver)) {$approver=null; $tlPic = asset('public/img/useravatar.png');}
        else
        {
          $approver = User::where('employeeNumber', ImmediateHead_Campaign::find($key->approver)->immediateHeadInfo->employeeNumber)->select('id','firstname','nickname', 'lastname')->first();
          if ( file_exists('public/img/employees/'.$approver->id.'.jpg') ) $tlPic = asset('public/img/employees/'.$approver->id.'.jpg');
          else $tlPic = asset('public/img/useravatar.png');
        }

        $prod = Biometrics::find($key->biometrics_id)->productionDate;
        $productionDay = Carbon::parse($prod,"Asia/Manila")->format('l');
        $prodDate = Carbon::parse($prod,"Asia/Manila");

        ($prodDate<$pastPayroll) ? $irrevocable=true : $irrevocable=false;
        $requests->push(['type'=>"Change Work Schedule",'irrevocable'=>$irrevocable, 'productionDate'=>$prodDate->format('M d, Y'),'productionDay'=>$productionDay,  "typeid"=>'6','approver'=>$approver, 'tlPic'=>$tlPic, 'icon'=>"fa-calendar-times-o",'nickname'=>$nickname, 'details'=>$key]);
      }




       /*------ OVERTIME --------*/

      foreach ($ot as $key) {
        if (is_null($key->approver)) {$approver=null; $tlPic = asset('public/img/useravatar.png');}
         else
        {
          $approver = User::where('employeeNumber', ImmediateHead_Campaign::find($key->approver)->immediateHeadInfo->employeeNumber)->select('id','firstname','nickname', 'lastname')->first();
          if ( file_exists('public/img/employees/'.$approver->id.'.jpg') ) $tlPic = asset('public/img/employees/'.$approver->id.'.jpg');
          else $tlPic = asset('public/img/useravatar.png');
        }

        $prod = Biometrics::find($key->biometrics_id)->productionDate;
        $productionDay = Carbon::parse($prod,"Asia/Manila")->format('l');
        $prodDate = Carbon::parse($prod,"Asia/Manila");

        ($prodDate<$pastPayroll) ? $irrevocable=true : $irrevocable=false;

        $requests->push(['type'=>"Overtime",'irrevocable'=>$irrevocable, 'productionDate'=>$prodDate->format('M d, Y'),'productionDay'=>$productionDay,   "typeid"=>'7','icon'=>"fa-hourglass",'approver'=>$approver, 'tlPic'=>$tlPic,'nickname'=>$nickname,   'details'=>$key]);
      }




       /*------ DTRP in --------*/

      foreach ($dtrp as $key) {
        if (is_null($key->approvedBy)) {$approver=null; $tlPic = asset('public/img/useravatar.png');}
         else
        {
          $approver = User::where('employeeNumber', ImmediateHead_Campaign::find($key->approvedBy)->immediateHeadInfo->employeeNumber)->select('id','firstname','nickname', 'lastname')->first();
          if ( file_exists('public/img/employees/'.$approver->id.'.jpg') ) $tlPic = asset('public/img/employees/'.$approver->id.'.jpg');
          else $tlPic = asset('public/img/useravatar.png');
        }

        $prod = Biometrics::find($key->biometrics_id)->productionDate;
        $productionDay = Carbon::parse($prod,"Asia/Manila")->format('l');
        $prodDate = Carbon::parse($prod,"Asia/Manila");

        ($prodDate<$pastPayroll) ? $irrevocable=true : $irrevocable=false;

        $requests->push(['type'=>"DTR Problem - TIME IN", 'irrevocable'=>$irrevocable, 'productionDate'=>$prodDate->format('M d, Y'),'productionDay'=>$productionDay,   "typeid"=>'8','approver'=>$approver, 'tlPic'=>$tlPic, 'icon'=>"fa-sign-in",'nickname'=>$nickname,  'details'=>$key]);
      }


       /*------ DTRP out --------*/

      foreach ($dtrp_out as $key) {
        if (is_null($key->approvedBy)) {$approver=null; $tlPic = asset('public/img/useravatar.png');}
         else
        {
          $approver = User::where('employeeNumber', ImmediateHead_Campaign::find($key->approvedBy)->immediateHeadInfo->employeeNumber)->select('id','firstname','nickname', 'lastname')->first();
          if ( file_exists('public/img/employees/'.$approver->id.'.jpg') ) $tlPic = asset('public/img/employees/'.$approver->id.'.jpg');
          else $tlPic = asset('public/img/useravatar.png');
        }

        $prod = Biometrics::find($key->biometrics_id)->productionDate;
        $productionDay = Carbon::parse($prod,"Asia/Manila")->format('l');

        
        $prodDate = Carbon::parse($prod,"Asia/Manila");

        ($prodDate<$pastPayroll) ? $irrevocable=true : $irrevocable=false;

        $requests->push(['type'=>"DTR Problem - TIME OUT",'irrevocable'=>$irrevocable, 'productionDate'=>$prodDate->format('M d, Y'),'productionDay'=>$productionDay,   "typeid"=>'9','approver'=>$approver, 'tlPic'=>$tlPic, 'icon'=>"fa-sign-out",'nickname'=>$nickname,  'details'=>$key]);
      }



       /*------ VACATION LEAVE --------*/

      foreach ($vl as $key) {
        if (is_null($key->approver)) {$approver=null; $tlPic = asset('public/img/useravatar.png');}
         else
        {
          $approver = User::where('employeeNumber', ImmediateHead_Campaign::find($key->approver)->immediateHeadInfo->employeeNumber)->select('id','firstname','nickname', 'lastname')->first();
          if ( file_exists('public/img/employees/'.$approver->id.'.jpg') ) $tlPic = asset('public/img/employees/'.$approver->id.'.jpg');
          else $tlPic = asset('public/img/useravatar.png');
        }

        $prod = $key->leaveStart; //Biometrics::find($key->biometrics_id)->productionDate;
        $productionDay = Carbon::parse($prod,"Asia/Manila")->format('l');
        $prodDate = Carbon::parse($prod,"Asia/Manila");

        ($prodDate<$pastPayroll) ? $irrevocable=true : $irrevocable=false;

        $requests->push(['type'=>"Vacation Leave",'irrevocable'=>$irrevocable, 'productionDate'=>$prodDate->format('M d, Y'),'productionDay'=>$productionDay,   "typeid"=>'10','icon'=>"fa-plane",'approver'=>$approver, 'tlPic'=>$tlPic,'nickname'=>$nickname,  'details'=>$key]);
      }

       /*------ SICK LEAVE --------*/

      foreach ($sl as $key) {
        if (is_null($key->approver)) {$approver=null; $tlPic = asset('public/img/useravatar.png');}
         else
        {
          $approver = User::where('employeeNumber', ImmediateHead_Campaign::find($key->approver)->immediateHeadInfo->employeeNumber)->select('id','firstname','nickname', 'lastname')->first();
          if ( file_exists('public/img/employees/'.$approver->id.'.jpg') ) $tlPic = asset('public/img/employees/'.$approver->id.'.jpg');
          else $tlPic = asset('public/img/useravatar.png');
        }

        $prod = $key->leaveStart; //Biometrics::find($key->biometrics_id)->productionDate;
        $productionDay = Carbon::parse($prod,"Asia/Manila")->format('l');
        $prodDate = Carbon::parse($prod,"Asia/Manila");

        ($prodDate<$pastPayroll) ? $irrevocable=true : $irrevocable=false;

        $requests->push(['type'=>"Sick Leave",'irrevocable'=>$irrevocable, 'productionDate'=>$prodDate->format('M d, Y'),'productionDay'=>$productionDay,   "typeid"=>'11','icon'=>"fa-stethoscope",'approver'=>$approver, 'tlPic'=>$tlPic,'nickname'=>$nickname,  'details'=>$key]);
      }



       /*------ LWOP  --------*/

      foreach ($lwop as $key) {
        if (is_null($key->approver)) {$approver=null; $tlPic = asset('public/img/useravatar.png');}
         else
        {
          $approver = User::where('employeeNumber', ImmediateHead_Campaign::find($key->approver)->immediateHeadInfo->employeeNumber)->select('id','firstname','nickname', 'lastname')->first();
          if ( file_exists('public/img/employees/'.$approver->id.'.jpg') ) $tlPic = asset('public/img/employees/'.$approver->id.'.jpg');
          else $tlPic = asset('public/img/useravatar.png');
        }

        $prod = $key->leaveStart; //Biometrics::find($key->biometrics_id)->productionDate;
        $productionDay = Carbon::parse($prod,"Asia/Manila")->format('l');
        $prodDate = Carbon::parse($prod,"Asia/Manila");

        ($prodDate<$pastPayroll) ? $irrevocable=true : $irrevocable=false;

        $requests->push(['type'=>"Leave Without Pay",'irrevocable'=>$irrevocable, 'productionDate'=>$prodDate->format('M d, Y'),'productionDay'=>$productionDay,   "typeid"=>'12','icon'=>"fa-meh-o",'approver'=>$approver, 'tlPic'=>$tlPic,'nickname'=>$nickname,  'details'=>$key]);
      }


      /*------ OBT  --------*/

      foreach ($obt as $key) {
        if (is_null($key->approver)) {$approver=null; $tlPic = asset('public/img/useravatar.png');}
         else
        {
          $approver = User::where('employeeNumber', ImmediateHead_Campaign::find($key->approver)->immediateHeadInfo->employeeNumber)->select('id','firstname','nickname', 'lastname')->first();
          if ( file_exists('public/img/employees/'.$approver->id.'.jpg') ) $tlPic = asset('public/img/employees/'.$approver->id.'.jpg');
          else $tlPic = asset('public/img/useravatar.png');
        }

        $prod = $key->leaveStart; //Biometrics::find($key->biometrics_id)->productionDate;
        $productionDay = Carbon::parse($prod,"Asia/Manila")->format('l');
        $prodDate = Carbon::parse($prod,"Asia/Manila");

        ($prodDate<$pastPayroll) ? $irrevocable=true : $irrevocable=false;

        $requests->push(['type'=>"Official Business Trip",'irrevocable'=>$irrevocable, 'productionDate'=>$prodDate->format('M d, Y'),'productionDay'=>$productionDay,   "typeid"=>'13','icon'=>"fa-suitcase",'approver'=>$approver, 'tlPic'=>$tlPic,'nickname'=>$nickname,  'details'=>$key]);
      }

      //$requests->push(['cws'=>$cws,'dtrp'=>$dtrp,'ot'=>$ot,'vl'=>$vl]);




      return Datatables::collection($requests)->make(true);
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
      $sched = new Collection;
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
          $workSched_fixed = FixedSchedules::where('user_id',$user->id)->where('isRD',0)->orderBy('created_at','DESC')->get();
                             // $user->fixedSchedule->where('isRD',0);
          $RDsched_fixed = FixedSchedules::where('user_id',$user->id)->where('isRD',1)->orderBy('created_at','DESC')->get();
                           //$user->fixedSchedule->where('isRD',1); //->pluck('workday');

          $isFixedSched =false;
          $noWorkSched = false;
         
         //$coll2->push(['start'=>$startingPoint, 'end'=>$endDate]);
          

              $dt  = $startingPoint->dayOfWeek;
              switch($dt){
                case 0: $dayToday = 6; break;
                case 1: $dayToday = 0; break;
                default: $dayToday = $dt-1;
              } 

              //------- need to fix this one, loop for each sched dapat to check kung efffective na ung sched

              $wd_fixed = FixedSchedules::where('user_id',$user->id)->where('workday',$dayToday)->orderBy('created_at','DESC')->first();
                         // $user->fixedSchedule->where('workday',$dayToday)->sortByDesc('created_at')->first();
              
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
              //------- need to fix this one, loop for each sched dapat to check kung efffective na ung sched

             


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
                  $workSched = FixedSchedules::where('user_id',$user->id)->where('isRD',0)->orderBy('created_at','DESC')->get();
                               //$user->fixedSchedule->where('isRD',0);
                  $RDsched = FixedSchedules::where('user_id',$user->id)->where('isRD',1)->orderBy('created_at','DESC')->select('workday')->get();
                             // $user->fixedSchedule->where('isRD',1)->pluck('workday');
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
                  $wd_fixed1 = FixedSchedules::where('user_id',$user->id)->where('workday',$dayToday)->orderBy('created_at','DESC')->get();
                               //$user->fixedSchedule->where('workday',$dayToday)->sortByDesc('created_at'); //->first();
                 

                  //check first kung pasok sa effectivity date

                  foreach ($wd_fixed1 as $wd_fixed) {

                      if ( (Carbon::parse($wd_fixed->schedEffectivity) <= $startingPoint) || $wd_fixed->schedEffectivity == null )
                      {
                        $coll = $this->getFixedSchedules2($wd_fixed,$startingPoint->format('Y-m-d'),$coll,$counter);
                        $sched = $coll->first();
                        break;
                      }
                    
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
      $startingPoint =Carbon::create(date('Y'),date('m'), date('d'),0,0,0, 'Asia/Manila')->subMonths(1);
      $endDate = Carbon::create(date('Y'), date('m'), date('d'),0,0,0, 'Asia/Manila')->addMonths(2); 
      
      $coll = new Collection;
      $coll2 = new Collection;
      $counter = 0;
      $totalMschedules = count($user->monthlySchedules);
      $totalFschedules = count($user->fixedSchedule);

      $coll2->push(['totalMschedules'=>$totalMschedules, 'totalFschedules'=>$totalFschedules ]);

      //return $coll2;
      

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

          $workSched_monthly = MonthlySchedules::where('user_id',$id)->where('isRD',0)->orderBy('productionDate','ASC')->get(); 
          $RDsched_monthly = MonthlySchedules::where('user_id',$id)->where('isRD',1)->orderBy('created_at','DESC')->get(); 
          $workSched_fixed = FixedSchedules::where('user_id',$id)->where('isRD',0)->orderby('created_at','DESC')->get();
                             // $user->fixedSchedule->where('isRD',0);
          $RDsched_fixed = FixedSchedules::where('user_id',$id)->where('isRD',1)->orderby('created_at','DESC')->get();
                           // $user->fixedSchedule->where('isRD',1); //->pluck('workday');

          $isFixedSched =false;
          $noWorkSched = false;


         
         //$coll2->push(['workSched'=>$workSched_monthly]);
          $kawnt = 0;
          while ($startingPoint < $endDate) 
          {
            $coll2->push(['startingPoint'=>$startingPoint->format('Y-m-d H:i:s')]);

              $dt  = $startingPoint->dayOfWeek;
              switch($dt){
                case 0: $dayToday = 6; break;
                case 1: $dayToday = 0; break;
                default: $dayToday = $dt-1;
              } 
              /* ---------------------------------------- OLD WAY -----------------------------------------------------------*/          

              $wd_fixed = FixedSchedules::where('user_id',$user->id)->where('workday',$dayToday)->orderBy('created_at','DESC')->first();

              if ((Carbon::parse($wd_fixed->schedEffectivity)->startOfDay() <= $startingPoint->startOfDay()) || $wd_fixed->schedEffectivity == null)
              {


                    $latest_fixed = $workSched_fixed->where('workday',$dayToday)->sortByDesc('created_at');
                    if ( $workSched_monthly->contains('productionDate',$startingPoint->format('Y-m-d')) || count($latest_fixed)>0 )
                    {
                      

                      $latest1 = $workSched_monthly->where('productionDate',$startingPoint->format('Y-m-d'))->sortByDesc('id');
                      if (count($latest1)>0) $latest = $latest1->first(); else $latest=null;

                      $latestRD =$RDsched_monthly->where('productionDate',$startingPoint->format('Y-m-d'));
                      
                      $coll2->push(['latestMOnthly'=>$latest, 'latest_fixed'=>$latest_fixed, 'latestRD'=>$latestRD]);

                      if ( !($latest_fixed->isEmpty()) ) // (count($latest_fixed)>0) 
                      {

                            //*** check mo muna kung may laman latest
                            if (count($latest)>0)
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


                            } //end if may laman $latest monthly sched
                            else{

                                    if (!($latestRD->isEmpty()) )
                                    {
                                          if($latestRD->first()->created_at > $latest_fixed->first()->created_at)
                                            $coll = $this->getShiftingSchedules2($latestRD->first(), $coll,$counter);
                                          else $coll = $this->getFixedSchedules2($latest_fixed->first(),$startingPoint->format('Y-m-d'),$coll,$counter);
                                                    // $this->getShiftingSchedules2($latest_fixed->first(), $coll,$counter);


                                    } else
                                    {
                                      $coll = $this->getFixedSchedules2($latest_fixed->first(),$startingPoint->format('Y-m-d'),$coll,$counter);
                                              //$this->getFixedSchedules2($wd_fixed,$startingPoint->format('Y-m-d'),$coll,$counter);
                                    }

                            }//end else if may laman latest
                            
                           
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
                       
                       $coll2->push(['latestRD'=>$latest, 'fixedRD'=>$latest_fixed]);

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
                $coll2->push($coll);
                /*$coll->push(['title'=>"NO SCHEDULE",
                                'start'=>$startingPoint->format('Y-m-d H:i:s'),
                                'textColor'=> '#fd940a',// '#409c45',
                                'icon'=>" ",
                                'backgroundColor'=> '#fff']);
                $coll->push(['title'=>" ",
                                'start'=>$startingPoint->format('Y-m-d H:i:s'),
                                'textColor'=> '#fd940a',// '#409c45',
                                'icon3'=>"calendar-o",
                                'backgroundColor'=> '#fff']);*/

              }

              $startingPoint->addDay();
              $counter++; $kawnt++;
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

                  /*-----

                  Here's whaat you wanna do: 
                  1) get all fixedWS for that dayToday

                  allFixedWS = FixedSchedules where (workday, $daytoday) orderby (created_at, DESC)

                  2) Foreach (allFixedWS as fixed_ws)
                    $ctr = 0;

                    if ($allFixedWS[$ctr]->schedEffectivity <= $startingPoint) || null ==> use it and exit loop
                    else $ctr++


                  -------*/
                  $allFixedWS = FixedSchedules::where('user_id',$user->id)->where('workday',$dayToday)->orderby('created_at','DESC')->get();
                  $ct = 0;

                  foreach ($allFixedWS as $key) 
                  {
                    if( $allFixedWS[$ct]->schedEffectivity <= $startingPoint->format('Y-m-d') || $allFixedWS[$ct]->schedEffectivity==null )
                    {
                      $coll = $this->getFixedSchedules2($allFixedWS[$ct],$startingPoint->format('Y-m-d'),$coll,$counter);
                      break;

                    } else $ct++;
                  }

                  //check first kung pasok sa effectivity date
                  /*if ( (Carbon::parse($wd_fixed->schedEffectivity) <= $startingPoint) || $wd_fixed->schedEffectivity == null )
                  {
                    $coll = $this->getFixedSchedules2($wd_fixed,$startingPoint->format('Y-m-d'),$coll,$counter);
                  }
                  */


                  

                 
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

      return $coll2;
       return response()->json($coll);

      
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

    /****** show YOUR OWN REQUESTS *******/
    public function myRequests($id)
    {
      if ($id==$this->user->id) $user=$this->user;
      else $user = User::find($id);

       $approvers = $user->approvers;
       $canView = $this->checkIfAnApprover($approvers, $this->user);

       $roles = UserType::find($this->user->userType_id)->roles->pluck('label'); 
        /* -------- get this user's department. If Backoffice, WFM can't access this ------*/
        $isBackoffice = ( Campaign::find(Team::where('user_id',$user->id)->first()->campaign_id)->isBackoffice ) ? true : false;
        $isWorkforce =  ($roles->contains('STAFFING_MANAGEMENT')) ? '1':'0';

      if (is_null($user)) return view('empty');
      else
        if ($canView || $this->user->id == $id || ($isWorkforce && !$isBackoffice))
          return view('people.myRequests',['user'=>$user,'forOthers'=>false,'anApprover'=>false]);
        else return view('access-denied');

    }

    

    /***** show your subordinates' requests *******/
     public function userRequests($id)
    {
      $user = User::find($id);


      if (is_null($user)) return view('empty');
      else{

        $approvers = $user->approvers;

        //Timekeeping Trait
        $canView = $this->checkIfAnApprover($approvers, $this->user);

        $roles = UserType::find($this->user->userType_id)->roles->pluck('label'); 
        /* -------- get this user's department. If Backoffice, WFM can't access this ------*/
        $isBackoffice = ( Campaign::find(Team::where('user_id',$user->id)->first()->campaign_id)->isBackoffice ) ? true : false;
        $isWorkforce =  ($roles->contains('STAFFING_MANAGEMENT')) ? '1':'0';


        if ($canView || $this->user->id == $id || ($isWorkforce && !$isBackoffice))
          return view('people.myRequests',['user'=>$user,'forOthers'=>true,'anApprover'=>$canView]);
        else
          return view('access-denied');

      }
      

    }



    
    


    public function myTeam()
    {
      $coll = new Collection;

        $roles = UserType::find($this->user->userType_id)->roles->pluck('label'); //->where('label','MOVE_EMPLOYEES');
        $canDelete =  ($roles->contains('DELETE_EMPLOYEE')) ? '1':'0';
        $canUpdateLeaves =  ($roles->contains('UPDATE_LEAVES')) ? '1':'0';


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
                          select('campaign.name as program','campaign.id as programID','campaign.isBackoffice', 'users.id','users.firstname','users.lastname','users.nickname','positions.name as position','users.id as userID')->
                          orderBy('users.lastname','ASC')->
                          where('users.status_id','!=',7)->
                          where('users.status_id','!=',8)->
                          where('users.status_id','!=',9)->get();
          //$allTeams = collect($allTeams1)->groupBy('program');
         
        } else {
          $allTeams1 = //DB::table('team')->where('team.campaign_id',$this->user->campaign->first()->id)->
                      DB::table('immediateHead_Campaigns')->where('immediateHead_id',$leadershipcheck->id)->
                           join('team','team.campaign_id','=','immediateHead_Campaigns.campaign_id')->
                           join('campaign','campaign.id','=','team.campaign_id')->
                           //select('immediateHead_Campaigns.campaign_id','campaign.name as program', 'team.user_id')->get();
                          join('users','team.user_id','=','users.id')->
                          join('positions','users.position_id','=','positions.id')->
                          
                          select('campaign.name as program','campaign.id as programID','campaign.isBackoffice', 'users.id', 'users.firstname','users.lastname','users.nickname','positions.name as position','users.id as userID')->
                          orderBy('users.lastname','ASC')->
                          where('users.status_id','!=',7)->
                          where('users.status_id','!=',8)->
                          where('users.status_id','!=',9)->get();
          $allTeams = collect($allTeams1)->sortBy('program')->groupBy('program');
          // $mySubordinates = $this->getMySubordinates($this->user->employeeNumber);
          // return $allTeams->first()[0]->lastname;

        
        }


        /* --------- optimize ---------- */



     
        $mySubordinates = $this->getMySubordinates($this->user->employeeNumber);

        if($this->user->id !== 564 ) {
          $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
            fwrite($file, "-------------------\n Viewed My Team by [". $this->user->id."] ".$this->user->lastname."\n");
            fclose($file);
        }

       //return $allTeams[0]->firstname;
        return view('people.myTeam',compact('campaigns','canDelete','canUpdateLeaves', 'allTeams','mySubordinates','leadershipcheck'));

        
      
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
        $user = User::find($id); 
        $roles = UserType::find($this->user->userType_id)->roles->pluck('label'); //->where('label','MOVE_EMPLOYEES');
        $canMoveEmployees =  ($roles->contains('MOVE_EMPLOYEE')) ? '1':'0';
        $canEditEmployees =  ($roles->contains('EDIT_EMPLOYEE')) ? '1':'0';
        $canChangeSched =  ($roles->contains('CHANGE_EMPLOYEE_SCHEDULE')) ? '1':'0';

        /* -------- get this user's department. If Backoffice, WFM can't access this ------*/
        $isBackoffice = ( Campaign::find(Team::where('user_id',$user->id)->first()->campaign_id)->isBackoffice ) ? true : false;
        $isWorkforce =  ($roles->contains('STAFFING_MANAGEMENT')) ? '1':'0';
        

        $canViewAllEvals = false;

        $canCWS =  ($roles->contains('CAN_CWS')) ? '1':'0';

        $hrDept = Campaign::where('name',"HR")->first();

        

        if (is_null($user)) return view('empty');

        ($user->id == $this->user->id) ? $theOwner = true : $theOwner=false;
        

        (is_null($user->nickname) || $user->nickname == " " || empty($user->nickname) ) ? $greeting = $user->firstname : $greeting = $user->nickname;

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

          
           
           $userEvals1 = $user->evaluations->sortByDesc('startPeriod')->filter(function($eval){
                                  return $eval->overAllScore > 0;

                      }); //->pluck('created_at','evalSetting_id','overAllScore'); 

        
        $byDateEvals =  $userEvals1->groupBy(function($pool) {
                                      return Carbon::parse($pool->created_at)->format('Y-m-d');
                                  });

        $userEvals = new Collection;
        $userEvals2 = new Collection;
        $guserEvals = new Collection;
        foreach ($byDateEvals as $evs) {
          $eval = $evs->unique('overAllScore');
          $evalT = EvalType::find($eval->first()->evalSetting_id);
          $evalY = date('Y', strtotime($eval->first()->startPeriod));
          $evalTitle = $evalY.  " ". $evalT->name;
          
          $tiel = ImmediateHead_Campaign::find($evs->first()->evaluatedBy)->immediateHeadInfo; 
          $tl = ImmediateHead::find($tiel->immediateHead_id);
          $evalBy = $tiel->firstname. " " . $tiel->lastname;

          $userEvals2->push(['evalType'=>$evalT->name, 'evalTypeID'=>$evalT->id, 'evalTitle'=>$evalTitle,'evalY'=>$evalY, 'evalBy'=>$evalBy, 'eval'=>$eval]);
        };

        $guserEvals = $userEvals2->groupBy('evalTitle');

        //*********** we need to compute real score for multiple evals **************

        foreach ($guserEvals as $e) {
          if (count($e) > 1 )
          {

            $setting = EvalSetting::find($e[0]['eval'][0]['evalSetting_id']);
            if ( ($setting->id == 1) || ($setting->id == 2) ) //if semi annuals
            {
              $start = Carbon::parse($e[0]['evalY']."-".$setting->startMonth."-".$setting->startDate,"Asia/Manila");
              $end = Carbon::parse($e[0]['evalY']."-".$setting->endMonth."-".$setting->endDate,"Asia/Manila");
              $totalDays = $start->diffInDays($end);

            }else{
              $hired = User::find($e[0]['eval'][0]['user_id'])->dateHired;
              $start = Carbon::parse($hired,"Asia/Manila");
              $end = Carbon::parse($hired,"Asia/Manila")->addMonths(6);
              $totalDays = $start->diffInDays($end);
            }
            
            $finalGrade = 0;
            $daysCtr = 0;

            foreach ($e as $ev) {

              
                $startP =Carbon::parse($ev['eval'][0]['startPeriod'],"Asia/Manila");
                $endP = Carbon::parse($ev['eval'][0]['endPeriod'],"Asia/Manila");
                $daysHandled = $startP->diffInDays($endP);

              //$end = Carbon::parse($e[0]['evalY']."-".$setting->endMonth."-".$setting->endDate,"Asia/Manila");

              if ($totalDays !== 0){

                $g = number_format(($ev['eval']->first()->overAllScore * ($daysHandled/$totalDays)),2);
                $userEvals->push([
                'evalTitle'=> $ev['evalTitle'],
                'by'=>$ev['evalBy'],
                'start'=>$start->format('Y-m-d'),
                'end'=>$end->format('Y-m-d'),
                'totalDays'=>$totalDays, 
                'sP'=>$startP->format('Y-m-d'), 
                'eP'=> $endP->format('Y-m-d'), 
                'daysHandled'=>$daysHandled, 
                'percentage'=> $daysHandled/$totalDays,
                'grade'=> $g,
                'finalGrade'=> $finalGrade+=$g,
                'daysCtr'=> $daysCtr+=$daysHandled,
                'missing'=> $totalDays-$daysCtr,
                'details'=>$ev['eval']]);

              }
              
            else
             
              $userEvals->push([
                'evalTitle'=> $ev['evalTitle'],
                'by'=>$ev['evalBy'],
                'start'=>$start->format('Y-m-d'),
                'end'=>$end->format('Y-m-d'),
                'totalDays'=>$totalDays, 
                'sP'=>$startP->format('Y-m-d'), 
                'eP'=> $endP->format('Y-m-d'), 
                'daysHandled'=>$daysHandled, 
                'percentage'=> 0,
                'grade'=> $g,
                'finalGrade'=> $finalGrade+=$g,
                'daysCtr'=> $daysCtr+=$daysHandled,
                'missing'=> $totalDays-$daysCtr,
                'details'=>$ev['eval']]);



            }

          }else 
          {
            $setting = EvalSetting::find($e[0]['eval'][0]['evalSetting_id']);
            if ( ($setting->id == 1) || ($setting->id == 2) ) //if semi annuals
            {
              $start = Carbon::parse($e[0]['evalY']."-".$setting->startMonth."-".$setting->startDate,"Asia/Manila");
              $end = Carbon::parse($e[0]['evalY']."-".$setting->endMonth."-".$setting->endDate,"Asia/Manila");
              $totalDays = $start->diffInDays($end);

            }else{
              $hired = User::find($e[0]['eval'][0]['user_id'])->dateHired;
              $start = Carbon::parse($hired,"Asia/Manila");
              $end = Carbon::parse($hired,"Asia/Manila")->addMonths(6);
              $totalDays = $start->diffInDays($end);
            }
            
            $finalGrade = 0;
            $daysCtr = 0;

            foreach ($e as $ev) 
            {

              
                $startP =Carbon::parse($ev['eval'][0]['startPeriod'],"Asia/Manila");
                $endP = Carbon::parse($ev['eval'][0]['endPeriod'],"Asia/Manila");
                $daysHandled = $startP->diffInDays($endP);

              //$end = Carbon::parse($e[0]['evalY']."-".$setting->endMonth."-".$setting->endDate,"Asia/Manila");

              if ($totalDays !== 0){

                $g = number_format(($ev['eval']->first()->overAllScore * ($daysHandled/$totalDays)),2);
                $userEvals->push([
                'evalTitle'=> $ev['evalTitle'],
                'by'=>$ev['evalBy'],
                'start'=>$start->format('Y-m-d'),
                'end'=>$end->format('Y-m-d'),
                'totalDays'=>$totalDays, 
                'sP'=>$startP->format('Y-m-d'), 
                'eP'=> $endP->format('Y-m-d'), 
                'daysHandled'=>$daysHandled, 
                'percentage'=> $daysHandled/$totalDays,
                'grade'=> $g,
                'finalGrade'=> $finalGrade+=$g,
                'daysCtr'=> $daysCtr+=$daysHandled,
                'missing'=> $totalDays-$daysCtr,
                'details'=>$ev['eval']]);

              }
              
            else
             
              $userEvals->push([
                'evalTitle'=> $ev['evalTitle'],
                'by'=>$ev['evalBy'],
                'start'=>$start->format('Y-m-d'),
                'end'=>$end->format('Y-m-d'),
                'totalDays'=>$totalDays, 
                'sP'=>$startP->format('Y-m-d'), 
                'eP'=> $endP->format('Y-m-d'), 
                'daysHandled'=>$daysHandled, 
                'percentage'=> 0,
                'grade'=> $g,
                'finalGrade'=> $finalGrade+=$g,
                'daysCtr'=> $daysCtr+=$daysHandled,
                'missing'=>$totalDays-$daysCtr,
                'details'=>$ev['eval']]);



            }



          } //END NO MULTIPLE EVALS$userEvals->push($e);

        }






        $approvers = $user->approvers;

        //Timekeeping Trait
        $anApprover = $this->checkIfAnApprover($approvers, $this->user);

        $correct = Carbon::now('GMT+8'); //->timezoneName();
        
        
        if($this->user->id !== 564 ) {
          $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
            fwrite($file, "-------------------\n Viewed Profile of user: ".$user->lastname."[".$user->id."] --" . $correct->format('M d h:i A'). " by [". $this->user->id."] ".$this->user->lastname."\n");
            fclose($file);
        }
        


        
            
        
        if ($anApprover || $canEditEmployees 
          || $this->user->campaign_id == $hrDept->id 
          || $theOwner || ($isWorkforce && !$isBackoffice)
          || $immediateHead->employeeNumber == $this->user->employeeNumber 
          || $this->user->employeeNumber==$leader_L1->employeeNumber
          || $this->user->employeeNumber==$leader_L0->employeeNumber 
          || $this->user->employeeNumber==$leader_PM->employeeNumber 
          || ($this->user->id == 83)  )  
        {

          //****** for capability to see all eval grades:
           if (Team::where('user_id',$this->user->id)->first()->campaign_id == $hrDept->id || $theOwner
              || $immediateHead->employeeNumber == $this->user->employeeNumber || $this->user->employeeNumber==$leader_L1->employeeNumber
              || $this->user->employeeNumber==$leader_L0->employeeNumber || $this->user->employeeNumber==$leader_PM->employeeNumber 
              || $this->user->userType_id == 1 )
              $canViewAllEvals = true; 

           

            $shifts = $this->generateShifts('12H');
            
            return view('people.show', compact('isWorkforce','isBackoffice', 'theOwner', 'canViewAllEvals','anApprover', 'approvers', 'user', 'greeting', 'immediateHead','canCWS','canPlotSchedule', 'canChangeSched', 'canMoveEmployees', 'canEditEmployees', 'camps','workSchedule', 'userEvals','shifts'));

            
            
        } else return view('people.profile', compact('anApprover', 'approvers', 'user','immediateHead','canCWS','canChangeSched', 'canMoveEmployees', 'canEditEmployees', 'camps','workSchedule', 'greeting'));
                   
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
        // $employee->startTraining = date("Y-m-d h:i:sa", strtotime(Input::get('startTraining')));
        // $employee->endTraining = date("Y-m-d h:i:sa", strtotime(Input::get('endTraining')));

        $startTraining = Input::get('startTraining');
        if( ($startTraining !== 'MM/DD/YYYY') && !empty($startTraining) && $startTraining !== '01/01/1970' && $startTraining !== '0000-00-00')
          $employee->startTraining = date("Y-m-d", strtotime(Input::get('startTraining')));
        else $employee->startTraining=null;

        $endTraining = Input::get('endTraining');
        if(($endTraining !== 'MM/DD/YYYY') && !empty($endTraining) && $endTraining !== '01/01/1970' && $endTraining !== '0000-00-00')
          $employee->endTraining = date("Y-m-d", strtotime(Input::get('endTraining')));
        else $employee->endTraining=null;

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
        $employee->nickname = Input::get('nickname');
        $employee->external = Input::get('external');

     
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

                 /* -------------- log updates made --------------------- */
         $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
            fwrite($file, "-------------------\n New Cover photo uploaded - ". date('M d h:i:s'). " by [". $this->user->id."] ".$this->user->lastname."\n");
            fclose($file);

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
