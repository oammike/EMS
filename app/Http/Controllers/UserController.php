<?php

namespace OAMPI_Eval\Http\Controllers;

use Hash;
use Carbon\Carbon;
use Excel;
use \PDF;
use \Mail;
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
use OAMPI_Eval\Role;
use OAMPI_Eval\User;
use OAMPI_Eval\Cutoff;
use OAMPI_Eval\User_Leader;
use OAMPI_Eval\User_CWS;
use OAMPI_Eval\User_DTRP;
use OAMPI_Eval\User_VL;
use OAMPI_Eval\User_VTO;
use OAMPI_Eval\User_SL;
use OAMPI_Eval\User_LWOP;
use OAMPI_Eval\User_OT;
use OAMPI_Eval\User_OBT;
use OAMPI_Eval\User_Familyleave;
use OAMPI_Eval\User_SpecialAccess;
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
use OAMPI_Eval\Point;
use OAMPI_Eval\Reward;
use OAMPI_Eval\Reward_Transfers;
use OAMPI_Eval\Reward_Award;
use OAMPI_Eval\Reward_Creditor;
use OAMPI_Eval\Reward_Feedback;
use OAMPI_Eval\Reward_Waysto;
use OAMPI_Eval\Orders;
use OAMPI_Eval\Paycutoff;
use OAMPI_Eval\Coffeeshop;

class UserController extends Controller
{
    protected $user;
    protected $pagination_items = 50;
    use Traits\UserTraits;
    use Traits\TimekeepingTraits;

     public function __construct(User $user)
    {
        $this->middleware('auth');
        $this->user =  User::find(Auth::user()->id);
        $this->pagination_items = 50;
        $this->initLoad =100;
    }

    public function index()
    {
       

      $myCampaign = $this->user->campaign; 
      $canDoThis = UserType::find($this->user->userType_id)->roles->where('label','EDIT_EMPLOYEE');
      $wf = UserType::find($this->user->userType_id)->roles->where('label','STAFFING_MANAGEMENT');
      $accessDir = Role::where('label','ACCESS_EMPLOYEE_DIRECTORY')->first();


      $today = Carbon::now('GMT+8');


      ($this->user->userType_id == 11) ? $wfAgent=true : $wfAgent=false;
      
      (count($canDoThis)> 0 ) ? $hasUserAccess=1 : $hasUserAccess=0;
      (count($wf) > 0) ? $isWorkforce=1 : $isWorkforce=0;

      $hr = Campaign::where('name','HR')->first();

      $hrTeam = collect(DB::table('team')->where('campaign_id',$hr->id)->select('team.user_id')->get())->pluck('user_id')->toArray();
      if (in_array($this->user->id, $hrTeam))
      {
        $isHR=1; $canAccessDir=1;
      }
      else { $isHR=false; $canAccessDir=false; }




      $specialAccess = User_SpecialAccess::where('user_id',$this->user->id)->where('role_id',$accessDir->id)->get();
      if (count($specialAccess) > 0 && !$isHR ) {

        // we need to check first kung valid date ba ung access nya
        if (is_null($specialAccess->first()->startDate))
          $canAccessDir=1; //meaning always accessbile sya kasi no date specified
        else
        {
          $accessibleFrom = Carbon::parse($specialAccess->first()->startDate,'Asia/Manila');
          $accessibleTo = Carbon::parse($specialAccess->first()->endDate,'Asia/Manila');

          if( $today->format('Y-m-d') >= $accessibleFrom->format('Y-m-d') && $today->format('Y-m-d') <= $accessibleTo->format('Y-m-d') )
            $canAccessDir=true;
          else $canAccessDir=false;

        }
        

        
      }// else { $canAccessDir=false; }



      //return response()->json(['isHR'=>$isHR,'canAccessDir?'=>$canAccessDir]);

      if(!$canAccessDir) return view('access-denied');

      //if(!$isHR) return view('access-denied');


      
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
      $canAccess = Role::where('label','ACCESS_INACTIVES')->first();
      $specialAccess = User_SpecialAccess::where('user_id',$this->user->id)->where('role_id',$canAccess->id)->get();

      $hr = Campaign::where('name','HR')->first();

      $hrTeam = collect(DB::table('team')->where('campaign_id',$hr->id)->select('team.user_id')->get())->pluck('user_id')->toArray();
      (in_array($this->user->id, $hrTeam)) ? $isHR=true : $isHR=false;

      if (!$isHR ){

        if (count($specialAccess) <= 0) return view('access-denied');
      } 

      if (count($canDoThis)> 0 ) $hasUserAccess=1; else $hasUserAccess=0;
      return view('people.employee-inactive', compact( 'hasUserAccess'));
    }


    public function index_floating()
    {
       

      $myCampaign = $this->user->campaign; 
      $canDoThis = UserType::find($this->user->userType_id)->roles->where('label','EDIT_EMPLOYEE');
      $canAccess = Role::where('label','ACCESS_FLOATING')->first();
      $specialAccess = User_SpecialAccess::where('user_id',$this->user->id)->where('role_id',$canAccess->id)->get();

      $hr = Campaign::where('name','HR')->first();

      $hrTeam = collect(DB::table('team')->where('campaign_id',$hr->id)->select('team.user_id')->get())->pluck('user_id')->toArray();
      (in_array($this->user->id, $hrTeam)) ? $isHR=true : $isHR=false;


     if (!$isHR ){

        if ( count($specialAccess) <= 0) return view('access-denied');
      }

      if (count($canDoThis)> 0 ) $hasUserAccess=1; else $hasUserAccess=0;
      return view('people.employee-floating', compact( 'hasUserAccess'));
    }


    public function index_pendings()
    {
       

      $myCampaign = $this->user->campaign; 
      $canDoThis = UserType::find($this->user->userType_id)->roles->where('label','EDIT_EMPLOYEE');
      $canAccess = Role::where('label','ACCESS_EMPLOYEE_DIRECTORY')->first();
      $specialAccess = User_SpecialAccess::where('user_id',$this->user->id)->where('role_id',$canAccess->id)->get();

      $hr = Campaign::where('name','HR')->first();

      $hrTeam = collect(DB::table('team')->where('campaign_id',$hr->id)->select('team.user_id')->get())->pluck('user_id')->toArray();
      (in_array($this->user->id, $hrTeam)) ? $isHR=true : $isHR=false;

      if (!$isHR ){

        if (count($specialAccess) <= 0) return view('access-denied');
      } 

      if (count($canDoThis)> 0 ) $hasUserAccess=1; else $hasUserAccess=0;
      return view('people.employee-pendings', compact( 'hasUserAccess'));
    }



    public function birthdayCelebrators()
    {
      $d = $this->getBdayCelebrators(Input::get('m_from'), Input::get('d_from'),Input::get('m_to'),Input::get('d_to'));

      return response()->json(['data'=>$d]);
      //return response()->json([ 'recordsTotal'=>count($d),'data'=>$d ]);
    }

    

   public function claimedcard(Request $request)
   {
      $user = User::find($request->id);
      $user->claimedCard = true;
      $user->save();
      return $user;

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
                if (count((array)$tl->campaign) > 1) 
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
                return view('people.employee-new', compact('users','userTypes','floors', 'leaders',  'hrPersonnels', 'myCampaign', 'campaigns', 'personnel','statuses', 'positions'));
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
        if ($canPlot 
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
          $shifts = $this->generateShifts('12H','full');
          $shifts4x11 = $this->generateShifts('12H','4x11');
          $partTimes = $this->generateShifts('12H','part');


          //return $teammates;

          return view('timekeeping.create-user-schedule', compact('user','img','shifts','shifts4x11','partTimes', 'teammates'));

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
        $shifts = $this->generateShifts('12H','full');
        $partTimes = $this->generateShifts('12H','part');


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

        $access = Role::where('label','ACCESS_EMPLOYEE_DIRECTORY')->first();
        $role_id = $access->id;
        $accessDir = User_SpecialAccess::where('user_id',$id)->where('role_id',$role_id)->get();

        $access2 = Role::where('label','ACCESS_INACTIVES')->first();
        $role2_id = $access2->id;
        $accessInactives = User_SpecialAccess::where('user_id',$id)->where('role_id',$role2_id)->get();
        $access3 = Role::where('label','ACCESS_FLOATING')->first();
        $role3_id = $access3->id;
        $accessFloating = User_SpecialAccess::where('user_id',$id)->where('role_id',$role3_id)->get();


        $hr = Campaign::where('name','HR')->first();
        $hrTeam = collect(DB::table('team')->where('campaign_id',$hr->id)->select('team.user_id')->get())->pluck('user_id')->toArray();
        (in_array($id, $hrTeam)) ? $isHR=true : $isHR=false;
        //return $accessDir;

        $alwaysAccessible=null;
        if ( count($accessDir) > 0  ) 
        {
          $hasSpecialAccess=true;
          if ($isHR) $alwaysAccessible=true;
          else if ( is_null($accessDir->first()->startDate) ) $alwaysAccessible=true;
          else $alwaysAccessible=false;


        } else{ $hasSpecialAccess=false; }

        ( count($accessInactives) > 0  ) ? $canAccessInactives = true : $canAccessInactives = false;
        ( count($accessFloating) > 0  ) ? $canAccessFloating = true : $canAccessFloating = false;


        //return $accessDir;

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

           
            //return $accessDir;
            return view('people.employee-edit', compact('isHR','alwaysAccessible','canAccessInactives','canAccessFloating', 'hasSpecialAccess','accessDir','role_id', 'fromYr','canEditEmployees','canUpdateLeaves', 'page', 'approvers','teamMates', 'currentTLcamp', 'personnelTL_ihCampID', 'users','floors', 'userTypes', 'leaders', 'myCampaign', 'campaigns', 'personnel','personnelTL', 'statuses', 'positions'));


        } 
       
    }

    public function getAllActiveUsers(){

        DB::connection()->disableQueryLog();


        $roles = UserType::find($this->user->userType_id)->roles->pluck('label'); //->where('label','MOVE_EMPLOYEES');
        $canEditEmployees =  ($roles->contains('EDIT_EMPLOYEE')) ? '1':'0';
       

        /* ------- faster method ----------- */

        if($canEditEmployees){

          $users = DB::table('users')->where([
                    ['status_id', '!=', 6],
                    ['status_id', '!=', 7],
                    ['status_id', '!=', 8],
                    ['status_id', '!=', 9],
                            ])->
                    leftJoin('team','team.user_id','=','users.id')->
                    leftJoin('campaign','team.campaign_id','=','campaign.id')->
                    leftJoin('immediateHead_Campaigns','team.immediateHead_Campaigns_id','=','immediateHead_Campaigns.id')->
                    leftJoin('immediateHead','immediateHead_Campaigns.immediateHead_id','=','immediateHead.id')->
                    leftJoin('positions','users.position_id','=','positions.id')->
                    leftJoin('statuses','users.status_id','=','statuses.id')->
                    leftJoin('userType','userType.id','=','users.userType_id')->
                    leftJoin('floor','team.floor_id','=','floor.id')->
                    
                    select('users.id','users.status_id', 'users.firstname','users.lastname','users.nickname','users.dateHired','statuses.name as status', 'positions.name as jobTitle','campaign.id as campID', 'campaign.name as program','immediateHead.firstname as leaderFname','immediateHead.lastname as leaderLname','users.employeeNumber','userType.name as userType','floor.name as location','users.isWFH as isWFH', 'users.claimedCard')->orderBy('users.lastname')->get();

        } else {

          $users = DB::table('users')->where([
                    ['status_id', '!=', 6],
                    ['status_id', '!=', 16],
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
                    select('users.id','users.status_id', 'users.firstname','users.lastname','users.nickname','users.dateHired','positions.name as jobTitle','campaign.id as campID', 'campaign.name as program','immediateHead.firstname as leaderFname','immediateHead.lastname as leaderLname','users.employeeNumber','floor.name as location')->orderBy('users.lastname')->get();
        }
        

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
                    ['status_id', '!=', 14],
                    ['status_id', '!=', 15],
                    ['status_id', '!=', 16],
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

    public function getAllFloatingUsers(){

        DB::connection()->disableQueryLog();

        $all = User::orderBy('lastname', 'ASC')->get();

       /* ------- faster method ----------- */

        $users = DB::table('users')->where('status_id', 6)->
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


    public function getAllPendingBdays(){

        DB::connection()->disableQueryLog();


        $roles = UserType::find($this->user->userType_id)->roles->pluck('label'); //->where('label','MOVE_EMPLOYEES');
        $canEditEmployees =  ($roles->contains('EDIT_EMPLOYEE')) ? '1':'0';
       

        /* ------- faster method ----------- */

        if($canEditEmployees){

          $users = DB::table('users')->where([
                    ['status_id', '!=', 6],
                    ['status_id', '!=', 7],
                    ['status_id', '!=', 8],
                    ['status_id', '!=', 9],
                            ])->
                    leftJoin('team','team.user_id','=','users.id')->
                    leftJoin('campaign','team.campaign_id','=','campaign.id')->
                    
                    leftJoin('positions','users.position_id','=','positions.id')->
                    
                    select('users.id','users.status_id', 'users.firstname','users.lastname','users.nickname','users.birthday', 'users.dateHired','positions.name as jobTitle','campaign.id as campID', 'campaign.name as program','users.employeeNumber')->
                    where('users.birthday','0000-00-00')->orderBy('users.lastname')->get();

        } else {

          $users = DB::table('users')->where([
                    ['status_id', '!=', 6],
                    ['status_id', '!=', 16],
                    ['status_id', '!=', 7],
                    ['status_id', '!=', 8],
                    ['status_id', '!=', 9],
                            ])->
                    leftJoin('team','team.user_id','=','users.id')->
                    leftJoin('campaign','team.campaign_id','=','campaign.id')->
                    leftJoin('positions','users.position_id','=','positions.id')->
                    select('users.id','users.status_id', 'users.firstname','users.lastname','users.nickname','users.birthday', 'users.dateHired','positions.name as jobTitle','campaign.id as campID', 'campaign.name as program','users.employeeNumber')->
                    where('users.birthday','0000-00-00')->orderBy('users.lastname')->get();
        }
        

        $month = 1;
        $young = Carbon::now()->format('Y') - 16;

        // $celebrators = DB::select( DB::raw("SELECT users.lastname,users.firstname,users.nickname,users.birthday,team.campaign_id FROM users INNER JOIN team ON team.user_id = users.id WHERE MONTH(users.birthday) = :month AND  users.status_id != 6 AND users.status_id != 7 AND users.status_id != 9  ORDER BY users.lastname ASC"), array(
        //              'month' => $month,
        //            ));

        $superyoung =  DB::select( DB::raw("SELECT users.id, users.status_id, users.firstname,users.lastname,users.nickname,users.birthday,users.dateHired,positions.name as jobTitle, campaign.id as campID, campaign.name as program, users.employeeNumber FROM users INNER JOIN team ON team.user_id = users.id INNER JOIN campaign ON team.campaign_id = campaign.id INNER JOIN positions ON users.position_id = positions.id WHERE YEAR(users.birthday) >= :young AND  users.status_id != 6 AND users.status_id != 7 AND users.status_id != 9  ORDER BY users.lastname ASC"), array(
                     'young' => $young,
                   ));

        $allpendings = array_merge($superyoung,$users);
        //return $allpendings;
        

        return response()->json(['data'=>$allpendings]);

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
      DB::connection()->disableQueryLog();
      if (is_null($id))
      {
        $cws = User_CWS::where('user_id',$this->user->id)->orderBy('updated_at','DESC')->get();
        $dtrp = User_DTRP::where('user_id',$this->user->id)->where('logType_id',1)->orderBy('updated_at','DESC')->get();
        $dtrp_out = User_DTRP::where('user_id',$this->user->id)->where('logType_id',2)->orderBy('updated_at','DESC')->get();
        $ot = User_OT::where('user_id',$this->user->id)->orderBy('updated_at','DESC')->get();
        $vl = User_VL::where('user_id',$this->user->id)->orderBy('updated_at','DESC')->get();
        $vto = User_VTO::where('user_id',$this->user->id)->orderBy('updated_at','DESC')->get();
        $sl = User_SL::where('user_id',$this->user->id)->orderBy('updated_at','DESC')->get();
        $lwop = User_LWOP::where('user_id',$this->user->id)->orderBy('updated_at','DESC')->get();
        $obt = User_OBT::where('user_id',$this->user->id)->orderBy('updated_at','DESC')->get();
        $ml = User_Familyleave::where('user_id',$this->user->id)->where('leaveType','ML')->orderBy('updated_at','DESC')->get();
        $pl = User_Familyleave::where('user_id',$this->user->id)->where('leaveType','PL')->orderBy('updated_at','DESC')->get();
        $spl = User_Familyleave::where('user_id',$this->user->id)->where('leaveType','SPL')->orderBy('updated_at','DESC')->get();

        ($this->user->nickname !== null) ? $nickname = $this->user->nickname." ".$this->user->lastname : $nickname = $this->user->firstname." ".$this->user->lastname;


      } else{

        $user = User::find($id);
        $cws = User_CWS::where('user_id',$user->id)->orderBy('updated_at','DESC')->get();
        $dtrp = User_DTRP::where('user_id',$user->id)->where('logType_id',1)->orderBy('updated_at','DESC')->get();
        $dtrp_out = User_DTRP::where('user_id',$user->id)->where('logType_id',2)->orderBy('updated_at','DESC')->get();
        $ot = User_OT::where('user_id',$user->id)->orderBy('updated_at','DESC')->get();
        $vl = User_VL::where('user_id',$user->id)->orderBy('updated_at','DESC')->get();
        $vto = User_VTO::where('user_id',$user->id)->orderBy('updated_at','DESC')->get();
        $sl = User_SL::where('user_id',$user->id)->orderBy('updated_at','DESC')->get();
        $lwop = User_LWOP::where('user_id',$user->id)->orderBy('updated_at','DESC')->get();
        $obt = User_OBT::where('user_id',$user->id)->orderBy('updated_at','DESC')->get();
        $ml = User_Familyleave::where('user_id',$user->id)->where('leaveType','ML')->orderBy('updated_at','DESC')->get();
        $pl = User_Familyleave::where('user_id',$user->id)->where('leaveType','PL')->orderBy('updated_at','DESC')->get();
        $spl = User_Familyleave::where('user_id',$user->id)->where('leaveType','SPL')->orderBy('updated_at','DESC')->get();


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
          if ($key->preshift)
            $approver = User::where('id',$key->approver)->select('id','firstname','nickname', 'lastname')->first();
          else
            $approver = User::where('employeeNumber', ImmediateHead_Campaign::find($key->approver)->immediateHeadInfo->employeeNumber)->select('id','firstname','nickname', 'lastname')->first();

          if ( file_exists('public/img/employees/'.$approver->id.'.jpg') ) $tlPic = asset('public/img/employees/'.$approver->id.'.jpg');
          else $tlPic = asset('public/img/useravatar.png');
        }

        $prod = Biometrics::find($key->biometrics_id)->productionDate;
        $productionDay = Carbon::parse($prod,"Asia/Manila")->format('l');
        $prodDate = Carbon::parse($prod,"Asia/Manila");

        ($prodDate<$pastPayroll) ? $irrevocable=true : $irrevocable=false;

        if($key->preshift)
          $requests->push(['type'=>"Overtime (Pre-shift)",'irrevocable'=>$irrevocable, 'productionDate'=>$prodDate->format('M d, Y'),'productionDay'=>$productionDay, "typeid"=>'15','icon'=>"fa-clock-o",'approver'=>$approver, 'tlPic'=>$tlPic,'nickname'=>$nickname,   'details'=>$key,'billedType'=>$key->billedType,'preshift'=>$key->preshift]);
        else
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
          //check first kung may immediateHead approver; else direct userID yon
          $hasIMhead = ImmediateHead_Campaign::find($key->approver);
          if (count( (array)$hasIMhead) > 0){
            $approver = User::where('employeeNumber', $hasIMhead->immediateHeadInfo->employeeNumber)->select('id','firstname','nickname', 'lastname')->first();
          }else{
            $approver = User::find($key->approver);

          }
          //$approver = User::where('employeeNumber', ImmediateHead_Campaign::find($key->approver)->immediateHeadInfo->employeeNumber)->select('id','firstname','nickname', 'lastname')->first();
          if ( file_exists('public/img/employees/'.$approver->id.'.jpg') ) $tlPic = asset('public/img/employees/'.$approver->id.'.jpg');
          else $tlPic = asset('public/img/useravatar.png');
        }

        $prod = $key->leaveStart; //Biometrics::find($key->biometrics_id)->productionDate;
        $productionDay = Carbon::parse($prod,"Asia/Manila")->format('l');
        $prodDate = Carbon::parse($prod,"Asia/Manila");

        ($prodDate<$pastPayroll) ? $irrevocable=true : $irrevocable=false;

        $requests->push(['type'=>"Vacation Leave",'irrevocable'=>$irrevocable, 'productionDate'=>$prodDate->format('M d, Y'),'productionDay'=>$productionDay,   "typeid"=>'10','icon'=>"fa-plane",'approver'=>$approver, 'tlPic'=>$tlPic,'nickname'=>$nickname,  'details'=>$key]);
      }

      /*------ VTO LEAVE --------*/

      foreach ($vto as $key) {
        if (is_null($key->approver)) {$approver=null; $tlPic = asset('public/img/useravatar.png');}
         else
        {
          $imh = ImmediateHead_Campaign::find($key->approver);

          if ($imh)
          {
            $approver = User::where('employeeNumber', $imh->immediateHeadInfo->employeeNumber)->select('id','firstname','nickname', 'lastname')->first();

          }else
          {
            if ($uap = User::find($key->approver))
              $approver = User::where('id', $uap->id)->select('id','firstname','nickname', 'lastname')->first();
            else
              $approver = null;

          }

          if ( file_exists('public/img/employees/'.$approver->id.'.jpg') ) $tlPic = asset('public/img/employees/'.$approver->id.'.jpg');
          else $tlPic = asset('public/img/useravatar.png');
        }

        $prod = $key->productionDate; //Biometrics::find($key->biometrics_id)->productionDate;
        $productionDay = Carbon::parse($prod,"Asia/Manila")->format('l');
        $prodDate = Carbon::parse($prod,"Asia/Manila");

        ($prodDate<$pastPayroll) ? $irrevocable=true : $irrevocable=false;

        $requests->push(['type'=>"VTO",'irrevocable'=>$irrevocable, 'productionDate'=>$prodDate->format('M d, Y'),'productionDay'=>$productionDay,   "typeid"=>'21','icon'=>"fa-history",'approver'=>$approver, 'tlPic'=>$tlPic,'nickname'=>$nickname,  'details'=>$key]);
      }

       /*------ SICK LEAVE --------*/

      foreach ($sl as $key) {
        if (is_null($key->approver)) {$approver=null; $tlPic = asset('public/img/useravatar.png');}
         else
        {
          $imh = ImmediateHead_Campaign::find($key->approver);
          if ($imh)
          {
            $approver = User::where('employeeNumber', $imh->immediateHeadInfo->employeeNumber)->select('id','firstname','nickname', 'lastname')->first();

          }else
          {
            if ($uap = User::find($key->approver))
              $approver = User::where('id', $uap->id)->select('id','firstname','nickname', 'lastname')->first();
            else
              $approver = null;

          }
          
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

      /*------ ML  --------*/
      foreach ($ml as $key) {
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

        $requests->push(['type'=>"Maternity Leave",'irrevocable'=>$irrevocable, 'productionDate'=>$prodDate->format('M d, Y'),'productionDay'=>$productionDay,   "typeid"=>'16','icon'=>"fa-female",'approver'=>$approver, 'tlPic'=>$tlPic,'nickname'=>$nickname,  'details'=>$key]);
      }

      /*------ PL  --------*/
      foreach ($pl as $key) {
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

        $requests->push(['type'=>"Paternity Leave",'irrevocable'=>$irrevocable, 'productionDate'=>$prodDate->format('M d, Y'),'productionDay'=>$productionDay,   "typeid"=>'17','icon'=>"fa-male",'approver'=>$approver, 'tlPic'=>$tlPic,'nickname'=>$nickname,  'details'=>$key]);
      }

       /*------ PL  --------*/
      foreach ($spl as $key) {
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

        $requests->push(['type'=>"Single-parent Leave",'irrevocable'=>$irrevocable, 'productionDate'=>$prodDate->format('M d, Y'),'productionDay'=>$productionDay,   "typeid"=>'18','icon'=>"fa-street-view",'approver'=>$approver, 'tlPic'=>$tlPic,'nickname'=>$nickname,  'details'=>$key]);
      }


      //return $requests;

      //return Datatables::collection($requests)->make(true);
      return response()->json(['data'=>$requests]);
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



    public function getWorkSchedForTheDay($id, Request $request)
    {
      $vl_to = Carbon::parse($request->vl_day,'Asia/Manila')->format('Y-m-d');
      $user = User::find($id);
      $isStylized = $request->isStylized;
      $sched = $this->getWorkSchedForTheDay1($user,$vl_to,null,$isStylized);
      //return (['vl_to'=>$vl_to, 'sched'=>$sched]);;
      return $sched;

    }

     public function getWorkSched($id)
    {

      DB::connection()->disableQueryLog();

      $user = User::find($id);
      $today = Carbon::create(date('Y'), date('m'), date('d'),0,0,0, 'Asia/Manila');
      
      
      //$startingPoint =Carbon::create(date('Y'), 1, 1,0,0,0, 'Asia/Manila');//->subMonths(6);
      $sp = Carbon::create(date('Y'),date('m'), date('d'),0,0,0, 'Asia/Manila')->subMonths(1);
      $startingPoint =Carbon::create(date('Y'),date('m'), 1,0,0,0, 'Asia/Manila')->subMonths(3);
      $endDate = Carbon::create(date('Y'), date('m'), date('d'),0,0,0, 'Asia/Manila')->addMonths(3); 
      
      $coll = new Collection;
      $coll2 = new Collection;
      $coll3 = new Collection;
      

      $counter = 0;
      $totalMschedules = DB::table('monthly_schedules')->where('user_id',$user->id)->where('productionDate','>=',$startingPoint->format('Y-m-d'))->where('productionDate','<',$endDate->format('Y-m-d'))->orderBy('id','DESC')->get(); 
      
      $totalFschedules = DB::table('fixed_schedules')->where('user_id',$user->id)->orderBy('id','DESC')->get();
      
      //return ['totalMschedules'=>$totalMschedules,'totalFschedules'=>$totalFschedules];

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

       if (count($totalMschedules) > 0 && count($totalFschedules) > 0 ) 

       {

          $workSched_monthly = collect($totalMschedules)->where('isRD',0);
          $RDsched_monthly =  collect($totalMschedules)->where('isRD',1);
          $workSched_fixed = collect($totalFschedules)->where('isRD',0)->sortByDesc('created_at');
          $RDsched_fixed = collect($totalFschedules)->where('isRD',1)->sortByDesc('created_at');
          

          $isFixedSched =false;
          $noWorkSched = false;
          $flag = null;

          while ($startingPoint < $endDate) 
          {
            

            $dt  = $startingPoint->dayOfWeek;
            switch($dt){
              case 0: $dayToday = 6; break;
              case 1: $dayToday = 0; break;
              default: $dayToday = $dt-1;
            } 


            /* ---- new way --- */
            (!is_null($workSched_monthly)) ? $monthly_wd = $workSched_monthly->where('productionDate',$startingPoint->format('Y-m-d'))->sortByDesc('created_at')->first() : $monthly_wd=null;

            (!is_null($RDsched_monthly)) ? $monthly_rd = $RDsched_monthly->where('productionDate',$startingPoint->format('Y-m-d'))->sortByDesc('created_at')->first() : $monthly_rd=null;

            (!is_null($workSched_fixed)) ? $fixed_wd = $workSched_fixed->where('workday',$dayToday)->sortByDesc('created_at')->first(): $fixed_wd=null;

            (!is_null($RDsched_fixed)) ? $fixed_rd = $RDsched_fixed->where('workday',$dayToday)->sortByDesc('created_at')->first() : $fixed_rd=null;

            $coll3->push(['monthly_wd'=>$monthly_wd, 'monthly_rd'=>$monthly_rd,'fixed_wd'=>$fixed_wd,'fixed_rd'=>$fixed_wd]);

            //----- first get WD
            if (is_null($monthly_wd)){
              if (!is_null($fixed_wd)) {
                //check mo muna effectivity date
                if (Carbon::parse($fixed_wd->schedEffectivity,'Asia/Manila')->startOfDay() <= $startingPoint->startOfDay() ){
                  $wd = $fixed_wd;
                  $flag = 'f';

                }
                  
                else $wd = null;

              }
              else{
                $wd = null;
              }

            }else {
              if (is_null($fixed_wd)){
                $wd = $monthly_wd;
                $flag = 'm';
              }
              else //parehas may value
              {
                //check mo muna effectivity date
                if (Carbon::parse($fixed_wd->schedEffectivity,'Asia/Manila')->startOfDay() <= $startingPoint->startOfDay() )
                {
                  //compare now which of them is latest
                  if (Carbon::parse($fixed_wd->created_at,'Asia/Manila')->format('Y-m-d H:i:s') > Carbon::parse($monthly_wd->created_at,'Asia/Manila')->format('Y-m-d H:i:s') )
                  {
                    $wd = $fixed_wd;
                    $flag = 'f';
                  } else {
                    $wd = $monthly_wd;
                    $flag = 'm';
                  }

                }else
                {
                  //automatic, monthly sched na kasi di pa pasok sa effectivity si fixed
                  $wd = $monthly_wd;
                  $flag = 'm';

                }

              }
            }


            // now we get RD
            if (is_null($monthly_rd)){
              if (!is_null($fixed_rd)){
                //check mo muna effectivity date
                if (Carbon::parse($fixed_rd->schedEffectivity,'Asia/Manila')->startOfDay() <= $startingPoint->startOfDay() ){
                  $rd = $fixed_rd;
                  $flag = 'f';

                }
                  
                else $rd = null;


              }else $rd = null;

            }else 
            {
              if (is_null($fixed_rd)){
                $rd = $monthly_rd;
                $flag = 'm';
              }
              else //parehas may value
              {
                if(!is_null($fixed_rd) && !is_null($monthly_rd)){
                  //kunin mo sino mas bago
                  //compare now which of them is latest
                    if (Carbon::parse($fixed_rd->created_at,'Asia/Manila')->format('Y-m-d H:i:s') > Carbon::parse($monthly_rd->created_at,'Asia/Manila')->format('Y-m-d H:i:s') )
                    {
                      $rd = $fixed_rd;
                      $flag = 'f';
                    } else {
                        $rd = $monthly_rd; $flag = 'm';
                      }


                }else {$rd=null;}
                
                

              }

            }


            // we now compare which is latest, RD sched or WD?
            
            if ($wd == null){
              if ($rd !== null) {
                ($flag == 'f') ? $coll = $this->getFixedSchedules2($rd,$startingPoint->format('Y-m-d'),$coll,$counter) : $coll = $this->getShiftingSchedules2($rd, $coll,$counter,$startingPoint);
              }
              else{
                
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


            }else {
              if ($rd == null){
                ($flag == 'f') ? $coll = $this->getFixedSchedules2($wd,$startingPoint->format('Y-m-d'),$coll,$counter) : $coll = $this->getShiftingSchedules2($wd, $coll,$counter,$startingPoint);
              }

                
              else{
                //parehas may value
                // we now compare which is latest, RD sched or WD?
                
                if (Carbon::parse($rd->created_at,'Asia/Manila')->format('Y-m-d H:i:s') > Carbon::parse($wd->created_at,'Asia/Manila')->format('Y-m-d H:i:s') ){
                  ($flag == 'f') ? $coll = $this->getFixedSchedules2($rd,$startingPoint->format('Y-m-d'),$coll,$counter) : $coll = $this->getShiftingSchedules2($rd, $coll,$counter,$startingPoint);

                }else{
                  ($flag == 'f') ? $coll = $this->getFixedSchedules2($wd,$startingPoint->format('Y-m-d'),$coll,$counter) : $coll = $this->getShiftingSchedules2($wd, $coll,$counter,$startingPoint);
                }

              }
            }
            

            /* ---- end new way --- */

            $startingPoint->addDay();
            $counter++; 
            
          }//end while

          //return $coll3;
       } 
       else
       {


          while ($startingPoint < $endDate)
          {
            if(count($totalMschedules) > 0){
              $workSched_monthly = collect($totalMschedules)->where('isRD',0)->where('productionDate',$startingPoint->format('Y-m-d'))->sortByDesc('created_at');
              $RDsched_monthly =  collect($totalMschedules)->where('isRD',1)->where('productionDate',$startingPoint->format('Y-m-d'))->sortByDesc('created_at');
         
              (!is_null($workSched_monthly)) ? $wd = $workSched_monthly->first() : $wd=null;

              (!is_null($RDsched_monthly)) ?$rd = $RDsched_monthly->first() : $rd=null;

              $isFixedSched = false;$noWorkSched = false;
            }
            else if( count($totalFschedules) > 0)
            {
              $dt  = $startingPoint->dayOfWeek;
              switch($dt){
                case 0: $dayToday = 6; break;
                case 1: $dayToday = 0; break;
                default: $dayToday = $dt-1;
              } 

              $workSched_fixed = collect($totalFschedules)->where('isRD',0)->where('workday',$dayToday)->sortByDesc('created_at');
              $RDsched_fixed = collect($totalFschedules)->where('isRD',1)->where('workday',$dayToday)->sortByDesc('created_at');

              (!is_null($workSched_fixed)) ? $wd = $workSched_fixed->first() : $wd=null;
              (!is_null($RDsched_fixed)) ? $rd = $RDsched_fixed->first() : $rd=null;
              $isFixedSched = true;$noWorkSched = false;

            }else{
              $wd=null; $rd=null;$isFixedSched = null;$noWorkSched = null;
            }

            $coll2->push(['wd'=>$wd,'rd'=>$rd]);

            // we now compare which is latest, RD sched or WD?
            if ($wd == null){
              if ($rd !== null){
                ($isFixedSched) ? $coll = $this->getFixedSchedules2($rd,$startingPoint->format('Y-m-d'),$coll,$counter) : $coll = $this->getShiftingSchedules2($rd, $coll,$counter,$startingPoint);
              }
              else{
                
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


            }else {
              if ($rd == null){
                ($isFixedSched) ? $coll = $this->getFixedSchedules2($wd,$startingPoint->format('Y-m-d'),$coll,$counter) : $coll = $this->getShiftingSchedules2($wd, $coll,$counter,$startingPoint);
              }

                
              else{
                //parehas may value
                // we now compare which is latest, RD sched or WD?
                if (Carbon::parse($rd->created_at,'Asia/Manila')->format('Y-m-d H:i:s') > Carbon::parse($wd->created_at,'Asia/Manila')->format('Y-m-d H:i:s') ){
                  ($isFixedSched) ? $coll = $this->getFixedSchedules2($rd,$startingPoint->format('Y-m-d'),$coll,$counter) : $coll = $this->getShiftingSchedules2($rd, $coll,$counter,$startingPoint);

                }else{
                  ($isFixedSched) ? $coll = $this->getFixedSchedules2($wd,$startingPoint->format('Y-m-d'),$coll,$counter) : $coll = $this->getShiftingSchedules2($wd, $coll,$counter,$startingPoint);
                }

              }
            }


            $startingPoint->addDay();
            $counter++;


          }//end while

           

       }//end else both have monthly and fixed

       //return $coll2;
       return response()->json($coll);

      
    }

    public function grantAccess(Request $request)
    {
      $user = User::find($request->user);
      // 0 = disabled, 1 =always enabled; 2=limited
      $directoryaccess = $request->directoryaccess;
      $inactiveAccess = $request->inactiveAccess;
      $floatingAccess = $request->floatingAccess;
      $role_id = $request->role_id;
      $existing=null;

      $access2 = Role::where('label','ACCESS_INACTIVES')->first();
      $access3 = Role::where('label','ACCESS_FLOATING')->first();
      

      if($inactiveAccess=='1')
      {
        $newAccess = new User_SpecialAccess;
        $newAccess->user_id = $user->id;
        $newAccess->role_id = $access2->id;
        $newAccess->save();

      }

      if($floatingAccess=='1')
      {
        $newAccess = new User_SpecialAccess;
        $newAccess->user_id = $user->id;
        $newAccess->role_id = $access3->id;
        $newAccess->save();

      }




      if($directoryaccess=='0')
      {
        $todelete = User_SpecialAccess::where('user_id',$user->id)->where('role_id',$role_id)->delete();
        

      }else if($directoryaccess == '1')
      {
        $newAccess = new User_SpecialAccess;
        $newAccess->user_id = $user->id;
        $newAccess->role_id = $role_id;
        $newAccess->save();

      }else if ($directoryaccess == '2')
      {
        $startDate = Carbon::parse($request->startDate,'Asia/Manila');
        $endDate =  Carbon::parse($request->endDate,'Asia/Manila');

        //first, look for exisiting access
        $existing = User_SpecialAccess::where('user_id',$user->id)->where('role_id',$role_id)->get();
        if ( count($existing) > 0 )
        {
          $e = $existing->first();
          $e->startDate = $startDate->format('Y-m-d');
          $e->endDate = $endDate->format('Y-m-d');
          $e->save();

        }else
        {
          $newAccess = new User_SpecialAccess;
          $newAccess->user_id = $user->id;
          $newAccess->role_id = $role_id;
          $newAccess->startDate = $startDate->format('Y-m-d');
          $newAccess->endDate = $endDate->format('Y-m-d');
          $newAccess->save();

        }
        

      }
      
      return response()->json(['existing'=>$existing,'user'=>$user]);
    }


    

    public function initializeRewards($floor_id)
    {
      $floor = Floor::find($floor_id);

      if (empty($floor)) return view('empty');

      ( empty(Input::get('points')) ) ? $points=10 : $points = Input::get('points');

      $allEmployees = DB::table('team')->where('floor_id',$floor_id)->
                          join('users','team.user_id','=','users.id')->
                          select('users.id','users.status_id')->
                          where([
                                  ['users.status_id', '!=', 6],
                                  ['users.status_id', '!=', 7],
                                  ['users.status_id', '!=', 8],
                                  ['users.status_id', '!=', 9],
                                          ])->get();
       $ct=0;
       foreach ($allEmployees as $e) {
         //check if has existing point
         $p = Point::where('idnumber',$e->id)->get();
          (count($p) > 0) ? $hasExisting=true : $hasExisting=false;

         if ($hasExisting){

              $pt = $p->first();
              $pt->points += $points;
              $pt->push();

         }else{

              $empReward = new Point;
              $empReward->idnumber = $e->id;
              $empReward->points = $points;
              $empReward->save();

         }
         $ct++;
       }

       return response()->json(['total awarded employees'=>$ct]);
    }


    public function leaveMgt()
    {
      $roles = UserType::find($this->user->userType_id)->roles->pluck('label'); 
      (Input::get('from')) ? $from = Input::get('from') : $from = Carbon::now()->addDays(-14)->format('m/d/Y'); 
      (Input::get('to')) ? $to = Input::get('to') : $to = date('m/d/Y');

      (Input::get('type')) ? $type = Input::get('type') : $type = 'VL';
      $stamp = Carbon::now('GMT+8');

      $isAdmin =  ($roles->contains('ADMIN_LEAVE_MANAGEMENT')) ? '1':'0';
      $canCredit =  ($roles->contains('UPDATE_LEAVES')) ? '1':'0';

      if(!$isAdmin){
        $file = fopen('storage/uploads/log.txt', 'a') or die("Unable to open logs");
                fwrite($file, "-------------------\n Tried lmgt_".$type." on ".$stamp->format('Y-m-d H:i')." by [". $this->user->id."] ".$this->user->lastname."\n");
                fclose($file);return view('access-denied');
      } 

      

      $allL = $this->getLeaves($from,$to,$type);
      $allLeave =$allL['leaves'];
      $pending_VL = $allL['pending_VL'];
      $pending_SL = $allL['pending_SL'];
      $pending_LWOP = $allL['pending_LWOP'];
      $pending_FL = $allL['pending_FL'];
      $pending_VTO = $allL['pending_VTO'];

      $update_credits=null;$update_periods=null;

      switch ($type) {
        case 'VL':{  $label = "Vacation Leave" ;  $deleteLink = url('/')."/user_vl/deleteThisVL/"; $notifType = 10; $u_c = DB::table('vlupdate')->where('period','>=',Carbon::now('GMT+8')->startOfYear()->format('Y-m-d'))->orderBy('period','DESC')->get();$update_credits = collect($u_c)->groupBy('period'); $update_periods = collect($u_c)->pluck('period')->unique();} break;
        case 'VTO':{  $label = "Voluntary Time Off" ;  $deleteLink = url('/')."/user_vl/deleteThisVTO/"; $notifType = 21;} break;
        case 'SL':{ $label = "Sick Leave" ;  $deleteLink = url('/')."/user_sl/deleteThisSL/"; $notifType = 11;} break;
        case 'LWOP':{ $label = "Leave Without Pay" ;  $deleteLink = url('/')."/user_lwop/deleteThisLWOP/"; $notifType = 12;} break;
        case 'FL':{ $label = "ML / PL / SPL " ;  $deleteLink = url('/')."/user_fl/deleteThisSL"; $notifType = 16;} break;
        default: { $label = "Vacation Leave";   $deleteLink = url('/')."/user_vl/deleteThisVL/"; $notifType = 10;} break;
      }

      if($this->user->id !== 564 ) {
              $file = fopen('storage/uploads/log.txt', 'a') or die("Unable to open logs");
                fwrite($file, "-------------------\n LeaveMGT_".$type." on ".$stamp->format('Y-m-d H:i')." by [". $this->user->id."] ".$this->user->lastname."\n");
                fclose($file);
            }


     

      return view('timekeeping.leaveMgt',compact('canCredit', 'isAdmin','from','to','allLeave','type','label','pending_VL','pending_SL','pending_LWOP','pending_FL','pending_VTO', 'deleteLink','notifType','update_periods','update_credits'));

    }

    public function leaveMgt_earnings()
    {
      $roles = UserType::find($this->user->userType_id)->roles->pluck('label'); 
      (Input::get('from')) ? $from = Input::get('from') : $from = Carbon::now()->addDays(-14)->format('m/d/Y'); 
      (Input::get('to')) ? $to = Input::get('to') : $to = date('m/d/Y');

      if(Input::get('type')) {
        $type = Input::get('type') ;
        $emp = Input::get('emp');
      }else {
        $type = 'VL';
        $emp = '1';
      }
      $stamp = Carbon::now('GMT+8');

      $isAdmin =  ($roles->contains('UPDATE_LEAVES')) ? '1':'0';

      if(!$isAdmin){
        $file = fopen('storage/uploads/log.txt', 'a') or die("Unable to open logs");
                fwrite($file, "-------------------\n Tried Earnings_".$type." on ".$stamp->format('Y-m-d H:i')." by [". $this->user->id."] ".$this->user->lastname."\n");
                fclose($file);return view('access-denied');
      } 

      

      $allL = $this->getLeaves($from,$to,$type);
      $earnings = $this->getLeaveEarnings($from,$to,$type,$emp);


      $allLeave =$allL['leaves'];
      $pending_VL = $allL['pending_VL'];
      $pending_SL = $allL['pending_SL'];
      $pending_LWOP = $allL['pending_LWOP'];
      $pending_FL = $allL['pending_FL'];
      $pending_VTO = $allL['pending_VTO'];

      switch ($emp) {
        case '1':{  $label = " Regular (0.42)" ;  $deleteLink = url('/')."/user_vl/deleteThisVL/"; $notifType = 10;} break;
        case '2':{  $label = "Part time (0.21)" ;  $deleteLink = url('/')."/user_vl/deleteThisVTO/"; $notifType = 21;} break;
        case '3':{ $label = "Sick Leave" ;  $deleteLink = url('/')."/user_sl/deleteThisSL/"; $notifType = 11;} break;
        case '4':{ $label = "Leave Without Pay" ;  $deleteLink = url('/')."/user_lwop/deleteThisLWOP/"; $notifType = 12;} break;
       default: { $label = "Regular (0.42)";   $deleteLink = url('/')."/user_vl/deleteThisVL/"; $notifType = 10;} break;
      }

      

      if($this->user->id !== 564 ) {
              $file = fopen('storage/uploads/log.txt', 'a') or die("Unable to open logs");
                fwrite($file, "-------------------\n LeaveMGT_".$type." on ".$stamp->format('Y-m-d H:i')." by [". $this->user->id."] ".$this->user->lastname."\n");
                fclose($file);
            }


     

      return view('timekeeping.leaveMgt_earnings',compact('isAdmin','from','to','allLeave','type','label','pending_VL','pending_SL','pending_LWOP','pending_FL','pending_VTO', 'deleteLink','notifType','emp','earnings'));

    }

    public function leaveMgt_summary()
    {
      $cutoffData = $this->getCutoffStartEnd();
      $cutoffStart = $cutoffData['cutoffStart'];//->cutoffStart;
      $cutoffEnd = $cutoffData['cutoffEnd'];

       //Timekeeping Trait
      $payrollPeriod = $this->getPayrollPeriod($cutoffStart,$cutoffEnd);
      $paycutoffs = Paycutoff::orderBy('toDate','DESC')->get();

      DB::connection()->disableQueryLog();
      $allUsers = DB::table('users')->where([
                    ['status_id', '!=', 6],
                    ['status_id', '!=', 7],
                    ['status_id', '!=', 8],
                    ['status_id', '!=', 9],
                    ['users.status_id', '!=', 13],
                    ['users.status_id', '!=', 16],
                ])->
        leftJoin('team','team.user_id','=','users.id')->
        leftJoin('campaign','team.campaign_id','=','campaign.id')->
        leftJoin('immediateHead_Campaigns','team.immediateHead_Campaigns_id','=','immediateHead_Campaigns.id')->
        leftJoin('immediateHead','immediateHead_Campaigns.immediateHead_id','=','immediateHead.id')->
        leftJoin('positions','users.position_id','=','positions.id')->
        leftJoin('floor','team.floor_id','=','floor.id')->
        select('users.id', 'users.firstname','users.lastname','users.nickname','users.dateHired','positions.name as jobTitle','campaign.id as campID', 'campaign.name as program','immediateHead_Campaigns.id as tlID', 'immediateHead.firstname as leaderFname','immediateHead.lastname as leaderLname','users.employeeNumber','floor.name as location')->orderBy('users.lastname')->get();

        $allProgram = DB::table('campaign')->select('id','name','hidden')->where('hidden',null)->
                          where([
                            ['campaign.id', '!=','26'], //wv
                            ['campaign.id', '!=','35'], //ceb

                          ])->orderBy('name')->get();//
        $byTL = collect($allUsers)->groupBy('tlID');
        $allTL = $byTL->keys();
        //return collect($allUsers)->where('campID',7);

        $correct = Carbon::now('GMT+8'); //->timezoneName();

           if($this->user->id !== 564 ) {
              $file = fopen('public/build/rewards.txt', 'a') or die("Unable to open logs");
                fwrite($file, "-------------------\n Viewed Finance_DTRsheets on " . $correct->format('M d h:i A'). " by [". $this->user->id."] ".$this->user->lastname."\n");
                fclose($file);
            } 
        
      

      return view('timekeeping.leaveMgt_summary',compact('payrollPeriod','paycutoffs','allProgram'));

    }


    public function listAllActive(){

        DB::connection()->disableQueryLog();
        $users = (array)DB::table('users')->where([
                    ['status_id', '!=', 6],
                    ['status_id', '!=', 16],
                    ['status_id', '!=', 7],
                    ['status_id', '!=', 8],
                    ['status_id', '!=', 9],
                            ])->
                    leftJoin('team','team.user_id','=','users.id')->
                    leftJoin('campaign','team.campaign_id','=','campaign.id')->
                    leftJoin('immediateHead_Campaigns','team.immediateHead_Campaigns_id','=','immediateHead_Campaigns.id')->
                    leftJoin('immediateHead','immediateHead_Campaigns.immediateHead_id','=','immediateHead.id')->
                    leftJoin('positions','users.position_id','=','positions.id')->
                    leftJoin('floor','team.floor_id','=','floor.id')->//,
                    select('users.id', 'users.lastname','users.firstname as fullname' ,'users.nickname','campaign.id as campID', 'campaign.name as program')
                    //select(DB::raw("CONCAT(users.lastname,', ',users.firstname,' (',users.nickname,') | ',campaign.name) AS fullname"),'campaign.id as campID')
                    //lists(DB::raw('CONCAT(firstname, " ", lastname)'), 'id')
                    //lists(DB::raw("CONCAT(users.lastname,', ',users.firstname,' (',users.nickname,') | ',campaign.name) AS fullname"),'campaign.id as campID');//

                    ->orderBy('users.lastname')->get();
        
        

        return response()->json($users);

         /* ------- faster method ----------- */        


        
        //return Datatables::collection($allUsers)->make(true);
       
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

        $correct = Carbon::now('GMT+8');

        


        /* --------- optimize ---------- */

        $myTree = new Collection;
        $mySubordinates = $this->getMySubordinates($this->user->employeeNumber);
        //return $mySubordinates;

        if (is_null($leadershipcheck) || count($mySubordinates) <= 1 ) {

          if($this->user->id !== 564 ) {
              $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
                fwrite($file, "-------------------\n Viewed My Team on ".$correct->format('Y-m-d H:i')." by [". $this->user->id."] ".$this->user->lastname."\n");
                fclose($file);
            }

          return view('people.myTeam',compact('allData','campaigns','canDelete','canUpdateLeaves', 'allTeams','mySubordinates','leadershipcheck'));

        }

       

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


        $allCampaigns = DB::table('campaign')->where('hidden',null)->select('id','name')->get(); //return  count($allCampaigns);->where('hidden',null)
        $colors = ["#ff476c","#D35400","#DC7633","#CB4335","#EC7063","#e463b3","#F39C12","#8ba642","#CA6F1E","#d17537","#F1C40F","#F7DC6F","#d4b328","#ffbf00","#d6c831","#8ed7ac","#09ad54","#63a8a3","#2874A6","#5499C7","#85C1E9","#2c67e5","#0632E0","#09b39c","#77aaff","#5588ff","#3366ff","#7992b3","#9f80a8","#7e43b1","#70025d","#673888"];
        $colorAssignment = [];

        

        $ctr=0;
        foreach ($allCampaigns as $c) {
          //array_push($colorAssignment, $c->id=>$colors[$ctr);
          $colorAssignment[$c->id]=$colors[$ctr];
          $ctr++;

          if ($ctr == count($colors)-1) $ctr=0;
        }


        
        /*R:"#ff476c","#D35400","#DC7633","#CB4335","#EC7063","#e463b3"
        o:"#F39C12","#F8C471","#CA6F1E","#d17537",
        Y: "#F1C40F","#F7DC6F","#d4b328","#ffbf00","#ffcf40"
        G:"#8ed7ac","#09ad54","#63a8a3","#2874A6","#5499C7","#85C1E9"
        B:"#2c67e5","#0632E0","#A4EFFA","#77aaff","#5588ff","#3366ff","#adcbe3"
        V: "#9f80a8","#7e43b1","#70025d","#673888"*/



        

        $user = DB::table('users')->where('users.id',$this->user->id)->
                    join('positions','positions.id','=','users.position_id')->
                    select('users.nickname','users.id','users.firstname','users.lastname','positions.name as position','users.email')->get();
                    //return $user[0]->firstname;

        

        

        $leaders = new Collection;

        if (count($allTeams) > 1 && (!is_null($leadershipcheck)))
        {
          foreach ($allTeams as $prog) {
            $tlgroup = collect($prog)->groupBy('TLid');
            foreach ($tlgroup as $key) {
              $ih = User::where('employeeNumber',ImmediateHead::find(ImmediateHead_Campaign::find($key->first()->TLid)->immediateHead_id)->employeeNumber)->get();
              if (count($ih) > 0)
                $andun = collect($allData)->where('userID',$ih->first()->id);

                if(count($andun) > 0)
                  $leaders->push(['TLid'=>$key->first()->TLid, 'firstname'=>$ih->first()->firstname,'lastname'=>$ih->first()->lastname,'id'=>$ih->first()->id,'program'=>$key->first()->TLid,'email'=>$ih->first()->email]);
              # code...
            }
            
           //$programs->push(['name'=>$prog->program])
           //$infoTL = 
          }

          

        }



          if($this->user->id !== 564 ) {
              $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
                fwrite($file, "-------------------\n ProgTREE on ".$correct->format('Y-m-d H:i')." by [". $this->user->id."] ".$this->user->lastname."\n");
                fclose($file);
            }

          //if($this->user->id == 2339) return (['mytree'=>$myTree,'mySubordinates'=>$mySubordinates]);

          return view('people.myTree',compact('myTree','colorAssignment', 'leaders', 'allData','campaigns','canDelete','canUpdateLeaves', 'allTeams','mySubordinates','leadershipcheck','user'));

        



        
          



        
      
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
       //return response()->json(['canView'=>$canView]);

       $roles = UserType::find($this->user->userType_id)->roles->pluck('label'); 
        /* -------- get this user's department. If Backoffice, WFM can't access this ------*/
        $isBackoffice = ( Campaign::find(Team::where('user_id',$user->id)->first()->campaign_id)->isBackoffice ) ? true : false;
        $isWorkforce =  ($roles->contains('STAFFING_MANAGEMENT')) ? '1':'0';

        $correct = Carbon::now('GMT+8');
        //log access
        if($this->user->id !== 564 ) {
                      
                      $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
                        fwrite($file, "-------------------\n Viewed REQUESTS [". $this->user->id."] ".$this->user->lastname." of [".$user->id."] on ". $correct->format('M d h:i A').  "\n");
                        fclose($file);
                    } 


      if (is_null($user)) return view('empty');


      else
        if ($canView || $this->user->id == $id || ($isWorkforce && !$isBackoffice))
          return view('people.myRequests',['user'=>$user,'forOthers'=>false,'anApprover'=>$canView,'isWorkforce'=>$isWorkforce,'isBackoffice'=>$isBackoffice]);
        else return view('access-denied');

    }


    public function rewards()
    {
      // check first if from Davao
      $floor = Team::where('user_id',$this->user->id)->first()->floor_id;
      $noaccess = [6,7,8,9];

      if (in_array($this->user->status_id, $noaccess))//$floor == 9 || 
         return view('access-denied');
      else
      {
        $today = Carbon::now('GMT+8')->startOfDay();
        $eod = Carbon::now('GMT+8')->endOfDay();
        $skip = 0 * $this->pagination_items;
        $take = $this->pagination_items;
        $rewards = Reward::with("category")->orderBy('name', 'asc')->skip($skip)->take($take)->get();
        
        $user_id = \Auth::user()->id;
        $user = User::with('points','team')->find($user_id);
        
        $orders = Orders::with('item')
                ->where([
                  ['user_id','=',$user_id],
                  ['status','=','PENDING'],
                ])
                ->get();


        // we now get any transfers made within the day
        $allTransfers = 0;
        $transfersMade = DB::table('reward_transfers')->where('from_user', $user_id)->
                              where('created_at','>=',$today->format('Y-m-d H:i:s'))->
                              where('created_at','<=',$eod->format('Y-m-d H:i:s'))->
                              select('transferedPoints')->get();
        if (count($transfersMade) > 0)
        {
          foreach($transfersMade as $t){
            $allTransfers += $t->transferedPoints;

          }
          
        }
       

        $data = [
          'include_rewards_scripts' => TRUE,
          'include_datatables' => TRUE,
          'contentheader_title' => "Rewards Catalog",
          'items_per_page' => $this->pagination_items,
          'rewards' => $rewards,
          'remaining_points' => is_null($user->points) ? 100 : $user->points->points,
          'orders' => $orders,
          'allTransfers'=>$allTransfers,
          'userID'=> $user_id
        ];

        if( \Auth::user()->id !== 564 ) {
                $file = fopen('public/build/rewards.txt', 'a') or die("Unable to open logs");
                  fwrite($file, "-------------------\n View Transfer on ".Carbon::now('GMT+8')->format('Y-m-d H:i')." by [". \Auth::user()->id."] ".\Auth::user()->lastname."\n");
                  fclose($file);
              }
        return view('rewards.transfer_points',$data);

      }
      
      

    }

    public function rewards_about()
    {
      // check first if from Davao
      
      $floor = Team::where('user_id',$this->user->id)->first()->floor_id;
      $noaccess = [6,7,8,9];

      if (in_array($this->user->status_id, $noaccess))//$floor == 9 || 
        return view('access-denied');
      else
      {
        $today = Carbon::now('GMT+8')->startOfDay();
        $eod = Carbon::now('GMT+8')->endOfDay();
        $skip = 0 * $this->pagination_items;
        $take = $this->pagination_items;
        $rewards = Reward::with("category")->orderBy('name', 'asc')->skip($skip)->take($take)->get();
        
        $user_id = \Auth::user()->id;
        $user = User::with('points','team')->find($user_id);
        
        $orders = Orders::with('item')
                ->where([
                  ['user_id','=',$user_id],
                  ['status','=','PENDING'],
                ])
                ->get();


        // we now get any transfers made within the day
        $allTransfers = 0;
        $transfersMade = DB::table('reward_transfers')->where('from_user', $user_id)->
                              where('created_at','>=',$today->format('Y-m-d H:i:s'))->
                              where('created_at','<=',$eod->format('Y-m-d H:i:s'))->
                              select('transferedPoints')->get();
        if (count($transfersMade) > 0)
        {
          foreach($transfersMade as $t){
            $allTransfers += $t->transferedPoints;

          }
          
        }

        if($this->user->id !== 564 ) {
                $file = fopen('public/build/rewards.txt', 'a') or die("Unable to open logs");
                  fwrite($file, "-------------------\n View About on ".Carbon::now('GMT+8')->format('Y-m-d H:i')." by [". $this->user->id."] ".$this->user->lastname."\n");
                  fclose($file);
              }
       

        $data = [
          'include_rewards_scripts' => TRUE,
          'contentheader_title' => "Rewards Catalog",
          'items_per_page' => $this->pagination_items,
          'rewards' => $rewards,
          'remaining_points' => is_null($user->points) ? 100 : $user->points->points,
          'orders' => $orders,
          'allTransfers'=>$allTransfers,
          'userID'=> $user_id
        ];

        //return view('access-denied');
        return view('rewards.about',$data);

      }
      
    }

    public function rewards_award()
    {
      // check first if from Davao
      $floor = Team::where('user_id',$this->user->id)->first()->floor_id;


      $noaccess = [6,7,8,9];

      if (in_array($this->user->status_id, $noaccess))//$floor == 9 || 
        return view('access-denied');
      else
      {
        $today = Carbon::now('GMT+8')->startOfDay();
        $eod = Carbon::now('GMT+8')->endOfDay();
        $skip = 0 * $this->pagination_items;
        $take = $this->pagination_items;

        // check first if user is a creditor
        $creditor = DB::table('reward_creditor')->where('reward_creditor.user_id',$this->user->id)->
                        join('reward_waysto','reward_creditor.waysto_id','=','reward_waysto.id')->get();
        $canAwardBday =  (count(collect($creditor)->where('name','Birthday Rewards') ) > 0) ? true:false;
        $bdayPoints = DB::table('reward_waysto')->where('name','Birthday Rewards')->first()->allowed_points;
        $canAwardAnniv =  (count(collect($creditor)->where('name','Work Anniversary') ) > 0) ? true:false;
        $annivPoints = DB::table('reward_waysto')->where('name','Work Anniversary')->first()->allowed_points;
        $months=["January",'February','March','April','May','June','July','August','September','October','November','December'];
       

        if (count($creditor) <= 0)
          return view('access-denied');
        else 
        {
          $rewards = Reward::with("category")->orderBy('name', 'asc')->skip($skip)->take($take)->get();
        
          $user_id = \Auth::user()->id;
          $user = User::with('points','team')->find($user_id);
          
          $orders = Orders::with('item')
                  ->where([
                    ['user_id','=',$user_id],
                    ['status','=','PENDING'],
                  ])
                  ->get();


          // we now get any transfers made within the day
          $allTransfers = 0;
          $transfersMade = DB::table('reward_transfers')->where('from_user', $user_id)->
                                where('created_at','>=',$today->format('Y-m-d H:i:s'))->
                                where('created_at','<=',$eod->format('Y-m-d H:i:s'))->
                                select('transferedPoints')->get();
          if (count($transfersMade) > 0)
          {
            foreach($transfersMade as $t){
              $allTransfers += $t->transferedPoints;

            }
            
          }

          $waysToEarn = DB::table('reward_waysto')->where('reward_waysto.automatic',null)->get();

         

          $data = [
            'include_rewards_scripts' => TRUE,
            'include_datatables' => TRUE,
            'contentheader_title' => "Rewards Award History",
            'items_per_page' => $this->pagination_items,
            'rewards' => $rewards,
            'remaining_points' => is_null($user->points) ? 100 : $user->points->points,
            'orders' => $orders,
            'allTransfers'=>$allTransfers,
            'waysToEarn'=>$waysToEarn,
            'creditor'=>$creditor,
            'canAwardAnniv'=>$canAwardAnniv,
            'canAwardBday' => $canAwardBday,
            'months'=>$months,
            'bdayPoints'=>$bdayPoints,
            'annivPoints'=>$annivPoints,
            'userID'=> $user_id
          ];

          if( \Auth::user()->id !== 564 ) {
                  $file = fopen('public/build/rewards.txt', 'a') or die("Unable to open logs");
                    fwrite($file, "-------------------\n View AwardPoints on ".Carbon::now('GMT+8')->format('Y-m-d H:i')." by [". \Auth::user()->id."] ".\Auth::user()->lastname."\n");
                    fclose($file);
                }
          //return $creditor;
          return view('rewards.award_points',$data);

        }
      }
      

      
      

    }

    public function rewards_award_history(Request $request){
      $data = new \stdClass();
      $data->success = false;
      $floor = Team::where('user_id',$this->user->id)->first()->floor_id;
      $noaccess = [6,7,8,9];
      if (in_array($this->user->status_id, $noaccess)) {
        $data->success = false;
        $data->message = "access denied";
      } else {
        $creditor = DB::table('reward_creditor')->where('reward_creditor.user_id',$this->user->id)->
                        join('reward_waysto','reward_creditor.waysto_id','=','reward_waysto.id')->get();
        if (count($creditor) <= 0) {
          $data->success = false;
          $data->message = "access denied";
        } else {
          $skip = $request->input('start');
          $take = $request->input('length', $this->pagination_items);
          $user_id = \Auth::user()->id;
          $search = "";
          $keyword = $request->input('search.value');
          if($keyword!=""){
            $search = " AND (users.firstname LIKE '%$keyword%' OR users.lastname LIKE '%$keyword%')";
          }

          $awards = \DB::select(\DB::raw("            
                      SELECT reward_award.points, reward_award.notes, reward_award.created_at,
                        users.firstname, users.lastname,
                        reward_waysto.name as way_title
                      FROM reward_award 
                      LEFT JOIN users ON reward_award.user_id = users.id
                      LEFT JOIN reward_waysto ON reward_award.waysto_id = reward_waysto.id
                      WHERE reward_award.awardedBy = $user_id " .
                        $search
                      . " ORDER BY reward_award.created_at DESC
                      LIMIT $skip, $take
          "));
          $total = count(\DB::table('reward_award')->where('awardedBy', '=', $user_id)->get());
          $data->success = true;
          $data->data = $awards;
          $data->message = "";
          $data->iTotalRecords = $total;
          $data->keyword = $keyword;
          $data->recordsFiltered = $total;
        }
      }
      return response()->json($data);
    }

    public function rewards_barista()
    {
      //check first if OPEN
      $shop = Coffeeshop::orderBy('id','DESC')->first();

      
        
     
      $roles = UserType::find($this->user->userType_id)->roles->pluck('label');
      $isBarista =  ($roles->contains('BARISTA')) ? '1':'0';

      if (!$isBarista)
        return view('access-denied');
      else
      {
        $today = Carbon::now('GMT+8')->startOfDay();
        $eod = Carbon::now('GMT+8')->endOfDay();
        $skip = 0 * $this->pagination_items;
        $take = $this->pagination_items;
        $rewards = Reward::with("category")->orderBy('name', 'asc')->skip($skip)->take($take)->get();
        
        $user_id = \Auth::user()->id;
        $user = User::with('points','team')->find($user_id);
        
        $orders = Orders::with('item')
                ->where([
                  ['user_id','=',$user_id],
                  ['status','=','PENDING'],
                ])
                ->get();


        // we now get any transfers made within the day
        $allTransfers = 0;
        $transfersMade = DB::table('reward_transfers')->where('from_user', $user_id)->
                              where('created_at','>=',$today->format('Y-m-d H:i:s'))->
                              where('created_at','<=',$eod->format('Y-m-d H:i:s'))->
                              select('transferedPoints')->get();
        if (count($transfersMade) > 0)
        {
          foreach($transfersMade as $t){
            $allTransfers += $t->transferedPoints;

          }
          
        }

        // reward items
        $allItems = Reward::all();
        $allOrders = DB::table('orders')->where('orders.created_at','>=',$today->format('Y-m-d H:i:s'))->
                        where('orders.created_at','<',$eod->format('Y-m-d H:i:s'))->
                        where('orders.status','PRINTED')->
                        join('rewards','orders.reward_id','=','rewards.id')->get(); //)->groupBy('reward_id');
                        

        $data = [
          'include_rewards_scripts' => TRUE,
          'contentheader_title' => "Rewards Catalog",
          'items_per_page' => $this->pagination_items,
          'rewards' => $rewards,
          'remaining_points' => is_null($user->points) ? 100 : $user->points->points,
          'orders' => $orders,
          'allTransfers'=>$allTransfers,
          'userID'=> $user_id,
          'today'=> $today->format('l M d, Y'),
          'allItems'=> $allItems,
          'allOrders' => $allOrders,
          'shop'=> $shop
        ];

        return view('rewards.shop',$data);

      }

      
      

      
    }

    public function rewards_coffeeshop(Request $request)
    {
      $shop = new Coffeeshop;
      $shop->barista_user = $this->user->id;
      $shop->status = $request->status;
      $shop->save();

     
      $file = fopen('public/build/rewards.txt', 'a') or die("Unable to open logs");
        fwrite($file, "-------------------\n Coffeeshop ".$request->status. " on ".Carbon::now('GMT+8')->format('Y-m-d H:i')." by [". \Auth::user()->id."] ".\Auth::user()->lastname."\n");
        fclose($file);
    

      return response()->json(['success'=>1, 'shop'=>$shop]);

    }


    public function rewards_feedback(Request $request)
    {
      $feedback = new Reward_Feedback;
      $feedback->user_id = $this->user->id;
      $feedback->notes = $request->feedback;
      $feedback->created_at = Carbon::now("GMT+8")->format('Y-m-d H:i:s');
      $feedback->updated_at = Carbon::now("GMT+8")->format('Y-m-d H:i:s');
      $feedback->save();
      if( \Auth::user()->id !== 564 ) {
                  $file = fopen('public/build/rewards.txt', 'a') or die("Unable to open logs");
                    fwrite($file, "-------------------\n Feedback on ".Carbon::now('GMT+8')->format('Y-m-d H:i')." by [". \Auth::user()->id."] ".\Auth::user()->lastname."\n");
                    fclose($file);
                }
      return response()->json(['success'=>1, 'feedback'=>$feedback]);
    }

    public function rewards_grantPoints(Request $request)
    {
      //check first if correct password
      /*$allRecipients = new Collection;
      foreach ($request->recipients as $u) {
        $allRecipients->push(User::find($u));
      }*/
      $coll = new Collection;
      $collstr ="";
      $coll2 = new Collection;
      $now = Carbon::now('GMT+8');

      if ( !(Hash::check($request->pw, $this->user->password) )) {
        return response()->json(['success'=>0, 'message'=>'Incorrect EMS Password. Unable to transfer reward points.','bcrypt'=>$request->pw]);
      }
      else 
      {
        switch ($request->awardtype) {
          case 'SPECIFIC': {

                              foreach ($request->recipients as $r) {

                                  $b = Point::where('idnumber',$r)->get();

                                  if (count($b) > 0){
                                    $beginningBal = $b->first()->points;

                                    $pt = Point::find($b->first()->id);
                                    $pt->points += (int)$request->points;
                                    $pt->updated_at = $now->format('Y-m-d H:i:s');
                                    $pt->push();

                                  }else {
                                    $beginningBal = $this->initLoad;
                                    $pt = new Point;
                                    $pt->idnumber = $r;
                                    $pt->points = $beginningBal;
                                    $pt->created_at = $now->format('Y-m-d H:i:s');
                                    $pt->updated_at = $now->format('Y-m-d H:i:s');
                                    $pt->save();
                                  }

                                  //check kung nabigyan na ng points for that engagement activity:
                                  if($request->engagement_id){
                                    $meronNa = DB::table('reward_award')->where('user_id',$r)->where('waysto_id',$request->waysto)->where('engagement_id',$request->engagement_id)->get();
                                    if (count($meronNa) > 0) { 
                                      //wag no na bigyan ng points
                                      $coll2->push($meronNa);
                                    }else
                                    {
                                      $award = new Reward_Award;
                                      $award->user_id = $r;
                                      $award->waysto_id = $request->waysto;
                                      $award->engagement_id = $request->engagement_id;
                                      $award->beginningBal = $beginningBal;
                                      $award->points = $request->points;
                                      $award->notes = $request->notes;
                                      $award->awardedBy = $this->user->id;
                                      $award->created_at = $now->format('Y-m-d H:i:s');
                                      $award->updated_at = $now->format('Y-m-d H:i:s');
                                      $award->save();
                                      $coll->push($award);
                                      $collstr .= $r.",";

                                    }
                                  }else
                                  {
                                    $award = new Reward_Award;
                                    $award->user_id = $r;
                                    $award->waysto_id = $request->waysto;
                                    $award->engagement_id=null;
                                    $award->beginningBal = $beginningBal;
                                    $award->points = $request->points;
                                    $award->notes = $request->notes;
                                    $award->awardedBy = $this->user->id;
                                    $award->created_at = $now->format('Y-m-d H:i:s');
                                    $award->updated_at = $now->format('Y-m-d H:i:s');
                                    $award->save();
                                    $coll->push($award);
                                    $collstr .= $r.",";
                                  }
                                  


                                  
                                }

                                if( \Auth::user()->id !== 564 ) {
                                      $file = fopen('public/build/rewards.txt', 'a') or die("Unable to open logs");
                                        fwrite($file, "-------------------\n Awarded ".$request->points. "pts to {". $collstr. "} on ".$now->format('Y-m-d H:i')." by [". \Auth::user()->id."] ".\Auth::user()->lastname."\n");
                                        fclose($file);
                                }

          }break;

          case 'BDAY': {

                            foreach ($request->recipients as $r) {

                                  

                                  //*** check mo muna baka nabigyan na sya ng bday reward
                                  $w = Reward_Waysto::where('name','Birthday Rewards')->first();
                                  
                                  $already = DB::select(DB::raw("SELECT reward_award.id, reward_award.user_id, reward_award.waysto_id, reward_award.created_at FROM reward_award WHERE reward_award.user_id = :u AND reward_award.waysto_id = :w AND YEAR(reward_award.created_at) = :y"),array(
                                     'y' => date('Y'),
                                     'u' => $r,
                                     'w' => $w->id
                                   ));

                                  if (count($already) > 0){
                                    $coll2->push($already);

                                  }else
                                  {
                                    $b = Point::where('idnumber',$r)->get();

                                    if (count($b) > 0){
                                      $beginningBal = $b->first()->points;

                                      $pt = Point::find($b->first()->id);
                                      $pt->points += (int)$request->points;
                                      $pt->updated_at = $now->format('Y-m-d H:i:s');
                                      $pt->save();

                                    }else {
                                      $beginningBal = $this->initLoad;
                                      $pt = new Point;
                                      $pt->idnumber = $r;
                                      $pt->points = $beginningBal + (int)$request->points;
                                      $pt->created_at = $now->format('Y-m-d H:i:s');
                                      $pt->updated_at = $now->format('Y-m-d H:i:s');
                                      $pt->save();
                                    }

                                    $award = new Reward_Award;
                                    $award->user_id = $r;
                                    $award->waysto_id = Reward_Waysto::where('name','Birthday Rewards')->first()->id;
                                    $award->beginningBal = $beginningBal;
                                    $award->points = $request->points;
                                    $award->notes = $request->notes;
                                    $award->awardedBy = 1;
                                    $award->created_at = $now->format('Y-m-d H:i:s');
                                    $award->updated_at = $now->format('Y-m-d H:i:s');
                                    $award->save();
                                    
                                    //$coll->push($award);
                                    $coll->push($award);
                                    $collstr .= $r.",";

                                  }


                                  
                                  
                                }

                                if( \Auth::user()->id !== 564 ) {
                                      $file = fopen('public/build/rewards.txt', 'a') or die("Unable to open logs");
                                        fwrite($file, "-------------------\n Bday ".$request->points. "pts to {". $collstr. "} on ".$now->format('Y-m-d H:i')." by [". \Auth::user()->id."] ".\Auth::user()->lastname."\n");
                                        fclose($file);
                                }

          }break;


          case 'ANNIV': {

                            $w = Reward_Waysto::where('name','Work Anniversary')->first();
                            foreach ($request->recipients as $r) {

                                  

                                  //*** check mo muna baka nabigyan na sya ng bday reward
                                  
                                  
                                  $already = DB::select(DB::raw("SELECT reward_award.id, reward_award.user_id, reward_award.waysto_id, reward_award.created_at FROM reward_award WHERE reward_award.user_id = :u AND reward_award.waysto_id = :w AND YEAR(reward_award.created_at) = :y"),array(
                                     'y' => date('Y'),
                                     'u' => $r,
                                     'w' => $w->id
                                   ));

                                  if (count($already) > 0){
                                    $coll2->push($already);

                                  }else
                                  {
                                    $b = Point::where('idnumber',$r)->get();

                                    if (count($b) > 0){
                                      $beginningBal = $b->first()->points;

                                      $pt = Point::find($b->first()->id);
                                      $pt->points += (int)$request->points;
                                      $pt->updated_at = $now->format('Y-m-d H:i:s');
                                      $pt->save();

                                    }else {
                                      $beginningBal = $this->initLoad;
                                      $pt = new Point;
                                      $pt->idnumber = $r;
                                      $pt->points = $beginningBal + (int)$request->points;
                                      $pt->created_at = $now->format('Y-m-d H:i:s');
                                      $pt->updated_at = $now->format('Y-m-d H:i:s');
                                      $pt->save();
                                    }

                                    $award = new Reward_Award;
                                    $award->user_id = $r;
                                    $award->waysto_id = $w->id;
                                    $award->beginningBal = $beginningBal;
                                    $award->points = $request->points;
                                    $award->notes = $request->notes;
                                    $award->awardedBy = 1;
                                    $award->created_at = $now->format('Y-m-d H:i:s');
                                    $award->updated_at = $now->format('Y-m-d H:i:s');
                                    $award->save();
                                    
                                    //$coll->push($award);
                                    $coll->push($award);
                                    $collstr .= $r.",";

                                    // NOW, EMAIL the awardee

                                    $awardee = User::find($r);
                                    $mike = User::find(564);
                                    $dateHired = Carbon::parse($awardee->dateHired,'Asia/Manila');
                                    $tenure = $dateHired->diffInYears(Carbon::now('GMT+8'))+1;
                                    
                                    $ddth = $dateHired->format('M d,Y');
                                 
                                     Mail::send('emails.anniv', ['awardee' => $awardee, 'tenure'=>$tenure,'ddth'=>$ddth], function ($m) use ($awardee, $tenure,$mike) 
                                     {
                                        $m->from('EMS@openaccessbpo.net', 'EMS | OAMPI Employee Management System');
                                        $m->to($awardee->email, $awardee->lastname.", ".$awardee->firstname)->subject('Happy Work Anniversary!');     

                                        /* -------------- log updates made --------------------- */
                                             $file = fopen('public/build/rewards.txt', 'a') or die("Unable to open logs");
                                                fwrite($file, "-------------------\n Email sent to ". $awardee->email."\n");
                                                fwrite($file, "\n AnnivGreet:  ". $awardee->firstname." ".$awardee->lastname. " tenure: ".$tenure."\n");
                                                fclose($file);                      
                                    

                                    }); //end mail

                                  }


                                  


                                  
                                  
                                }

                                if( \Auth::user()->id !== 564 ) {
                                      $file = fopen('public/build/rewards.txt', 'a') or die("Unable to open logs");
                                        fwrite($file, "-------------------\n WORKanniv ".$request->points. "pts to {". $collstr. "} on ".$now->format('Y-m-d H:i')." by [". \Auth::user()->id."] ".\Auth::user()->lastname."\n");
                                        fclose($file);
                                }


          }break;
          
          
        }


        
        return response()->json(['success'=>1,'awardees'=>$coll,'already'=>$coll2, 'total'=>count($coll)]);

      }



      
      
  


      

    }

    public function rewards_tenure()
    {
      DB::connection()->disableQueryLog(); 
      $coll =  new Collection; $coll2 = new Collection; $now= Carbon::now('GMT+8');
      $notes = Input::get('notes');

      switch (Input::get('tenure')) {
        case '6mos': {
                        $waysto_id = 1;
                        $s = Carbon::now('GMT+8')->subMonths(11);
                        $e = Carbon::now('GMT+8');
                        $tenures =  DB::table('users')->where('dateHired','<=',$e->format('Y-m-d H:i:s'))->where('dateHired','>=',$s->format('Y-m-d H:i:s'))->where('status_id','!=',6)->where('status_id','!=',7)->where('status_id','!=',8)->where('status_id','!=',9)->where('status_id','!=',13)->select('users.id',  'users.firstname','users.lastname','users.nickname', 'users.dateHired')->orderBy('users.lastname','ASC')->get();

                    }
                    break;
        
        case '1': {
                        $waysto_id = 2;
                        $s = Carbon::now('GMT+8')->subYear(3)->startOfYear();
                        $e = Carbon::now('GMT+8')->subYear(1)->endOfYear();
                        $tenures = DB::table('users')->where('dateHired','<=',$e->format('Y-m-d H:i:s'))->where('dateHired','>=',$s->format('Y-m-d H:i:s'))->
                          where('status_id','!=',6)->where('status_id','!=',7)->where('status_id','!=',8)->where('status_id','!=',9)->where('status_id','!=',13)->select('users.id',  'users.firstname','users.lastname','users.nickname', 'users.dateHired')->orderBy('users.dateHired','ASC')->get();

                    }
                    break;
      
        case '4': {
                        $waysto_id = 3;
                        $s = Carbon::now('GMT+8')->subYear(7)->startOfYear();
                        $e = Carbon::now('GMT+8')->subYear(4)->endOfYear();
                        $tenures = DB::table('users')->where('dateHired','<=',$e->format('Y-m-d H:i:s'))->where('dateHired','>=',$s->format('Y-m-d H:i:s'))->
                        where('status_id','!=',6)->where('status_id','!=',7)->where('status_id','!=',8)->where('status_id','!=',9)->where('status_id','!=',13)->select('users.id',  'users.firstname','users.lastname','users.nickname', 'users.dateHired')->orderBy('users.dateHired','ASC')->get();

                    }
                    break;

        case '8': {
                        $waysto_id = 4;
                        $s = Carbon::now('GMT+8')->subYear(9)->startOfYear();
                        $e = Carbon::now('GMT+8')->subYear(8)->endOfYear();
                        $tenures = DB::table('users')->where('dateHired','<=',$e->format('Y-m-d H:i:s'))->where('dateHired','>=',$s->format('Y-m-d H:i:s'))->
                        where('status_id','!=',6)->where('status_id','!=',7)->where('status_id','!=',8)->where('status_id','!=',9)->where('status_id','!=',13)->select('users.id',  'users.firstname','users.lastname','users.nickname', 'users.dateHired')->orderBy('users.dateHired','ASC')->get();

                    }
                    break;

        case '10': {
                        $waysto_id = 9;
                        $s = Carbon::now('GMT+8')->subYear(10)->startOfYear();
                        $e = Carbon::now('GMT+8')->subYear(10)->endOfYear();
                        $tenures = DB::table('users')->where('dateHired','<=',$e->format('Y-m-d H:i:s'))->where('status_id','!=',6)->where('status_id','!=',7)->where('status_id','!=',8)->where('status_id','!=',9)->where('status_id','!=',13)->select('users.id',  'users.firstname','users.lastname','users.nickname', 'users.dateHired')->orderBy('users.dateHired','DESC')->get();

                    }
                    break;            
      }
      
      $w = Reward_Waysto::find($waysto_id);
      foreach ($tenures as $r) {
          
                                  
          $already = DB::select(DB::raw("SELECT reward_award.id, reward_award.user_id, reward_award.waysto_id, reward_award.created_at FROM reward_award WHERE reward_award.user_id = :u AND reward_award.waysto_id = :w AND YEAR(reward_award.created_at) = :y AND MONTH(reward_award.created_at) = :m"),array(
             'y' => date('Y'),
             'm' => date('m'),
             'u' => (int)$r->id,
             'w' => (int)$w->id
           ));

          if (count($already) > 0){
            $coll2->push($already);

          }else
          {
            $b = Point::where('idnumber',$r->id)->get();

            if (count($b) > 0){
              $beginningBal = $b->first()->points;

              $pt = Point::find($b->first()->id);
              $pt->points += (int)$w->allowed_points;
              $pt->updated_at = $now->format('Y-m-d H:i:s');
              $pt->save();

            }else {
              $beginningBal = $this->initLoad;
              $pt = new Point;
              $pt->idnumber = $r->id;
              $pt->points = $beginningBal + (int)$w->allowed_points;
              $pt->created_at = $now->format('Y-m-d H:i:s');
              $pt->updated_at = $now->format('Y-m-d H:i:s');
              $pt->save();
            }

            $award = new Reward_Award;
            $award->user_id = $r->id;
            $award->waysto_id = $w->id;
            $award->beginningBal = $beginningBal;
            $award->points = $w->allowed_points;
            $award->notes = $notes;
            $award->awardedBy = 1;
            $award->created_at = $now->format('Y-m-d H:i:s');
            $award->updated_at = $now->format('Y-m-d H:i:s');
            $award->save();
            
            //$coll->push($award);
            $coll->push($award);
            //$collstr .= $r->id.",";

          }
      }

      //return response()->json(['total_awardees'=> count($tenures), 'awardees'=>$tenures, 'startPeriod'=>$s,'endPeriod'=>$e]);
      return response()->json(['success'=>1,'awardees'=>$coll,'already'=>$coll2, 'total'=>count($coll)]);
   

    }


    public function rewards_transactions()
    {
      // check first if from Davao
      
      $user = User::find($this->user->id);
      

      //if ($floor == 9 || in_array($this->user->status_id, $noaccess))
      $floor = Team::where('user_id',$this->user->id)->first()->floor_id;
      $noaccess = [6,7,8,9];

      if (in_array($this->user->status_id, $noaccess))//$floor == 9 || 
        return view('access-denied');
      else
      {
        $today = Carbon::now('GMT+8');
        $skip = 0 * $this->pagination_items;
        $take = $this->pagination_items;
        
        $myTransactions = DB::table('users')->where('users.id',$this->user->id)->
                              join('points','points.idnumber','=','users.id')->
                              join('orders','orders.user_id','=','users.id')->
                              join('rewards','rewards.id','=','orders.reward_id')->
                              select('points.points','rewards.name','rewards.cost', 'orders.created_at')->get();

       
        $vouchers = DB::table('voucher_claims')->where('voucher_claims.user_id',$this->user->id)->
                    join('vouchers','voucher_claims.voucher_id','=','vouchers.id')->get();

        $donations = DB::table('donation_intents')->where('donation_intents.user_id',$this->user->id)->
                    join('donation_catalog','donation_intents.donation_id','=','donation_catalog.id')->where('donation_intents.status','<>','denied')->get();




        // we now get any transfers made within the day
        $allTransfers = 0;
        $transfersMade = DB::table('reward_transfers')->where('from_user', $this->user->id)->
                              join('users','reward_transfers.to_user','=','users.id')->
                              select('users.firstname as to_fname','users.lastname as to_lname','users.nickname as to_nname','reward_transfers.transferedPoints','reward_transfers.notes','reward_transfers.created_at')->get();
                              // where('created_at','>=',$today->startOfDay()->format('Y-m-d H:i:s'))->
                              // where('created_at','<=',$today->endOfDay()->format('Y-m-d H:i:s'))->
                              //select('transferedPoints')->get();
        if (count($transfersMade) > 0)
        {
          foreach($transfersMade as $t){
            $allTransfers += $t->transferedPoints;

          }
          
        }

        $allReceived = 0;
        $pointsReceived = DB::table('reward_transfers')->where('reward_transfers.to_user', $this->user->id)->
                              join('users','reward_transfers.from_user','=','users.id')->
                              select('users.firstname as from_fname','users.lastname as from_lname','users.nickname as from_nname','reward_transfers.transferedPoints','reward_transfers.notes','reward_transfers.created_at')->get();
                              // where('created_at','>=',$today->startOfDay()->format('Y-m-d H:i:s'))->
                              // where('created_at','<=',$today->endOfDay()->format('Y-m-d H:i:s'))->
                              //select('transferedPoints')->get();
        if (count($pointsReceived) > 0)
        {
          foreach($pointsReceived as $t){
            $allReceived += $t->transferedPoints;

          }
          
        }

        $allAwards = 0;
        $awardsReceived = DB::table('reward_award')->where('reward_award.user_id', $this->user->id)->
                              join('users','reward_award.awardedBy','=','users.id')->
                              join('reward_waysto','reward_award.waysto_id','=','reward_waysto.id')->
                              select('users.firstname as from_fname','users.lastname as from_lname','users.nickname as from_nname','reward_award.points','reward_award.notes','reward_waysto.name as reason', 'reward_award.created_at')->get();
                              // where('created_at','>=',$today->startOfDay()->format('Y-m-d H:i:s'))->
                              // where('created_at','<=',$today->endOfDay()->format('Y-m-d H:i:s'))->
                              //select('transferedPoints')->get();
        if (count($awardsReceived) > 0)
        {
          foreach($awardsReceived as $t){
            $allAwards += $t->points;

          }
          
        }

        $totalEarnings = $allReceived + $allAwards;
        $allredeemed = count($vouchers) + count($myTransactions) + count($donations);
       

        $data = [
          'include_rewards_scripts' => TRUE,
          'items_per_page' => $this->pagination_items,
          'remaining_points' => is_null($user->points) ? $this->initLoad : $user->points->points,
          'allTransfers'=>$allTransfers,
          'allReceived'=>$allReceived,
          'allAwards'=>$allAwards,
          'totalEarnings'=>$totalEarnings,
          'myTransactions'=>$myTransactions,
          'transfersMade'=>$transfersMade,
          'pointsReceived'=>$pointsReceived,
          'awardsReceived'=>$awardsReceived,
          'vouchers'=>$vouchers,
          'donations'=>$donations,
          'allredeemed'=>$allredeemed

        ]; 

        if( \Auth::user()->id !== 564 ) {
                $file = fopen('public/build/rewards.txt', 'a') or die("Unable to open logs");
                  fwrite($file, "-------------------\n Transactions on ".Carbon::now('GMT+8')->format('Y-m-d H:i')." by [". \Auth::user()->id."] ".\Auth::user()->lastname."\n");
                  fclose($file);
              }
        return view('rewards.myTransactions',$data);

      }
      
      

    }


    public function rewardsTransfer(Request $request)
    {
      //check first if correct password
      $to = User::find($request->id_to);

      
      if ( !(Hash::check($request->pw, $this->user->password) ))
        return response()->json(['success'=>0, 'message'=>'Incorrect EMS Password. Unable to transfer reward points to','user'=>$to->lastname.', '.$to->firstname,'bcrypt'=>$request->pw]);
      else
      {
        $existing_from = DB::table('points')->where('idnumber',$this->user->id)->get();
        $existing_to = DB::table('points')->where('idnumber',$to->id)->get();

        if(count((array)$existing_from) > 0 )
        {
          //existing na yung nagtransfer
          $transfer = Point::find($existing_from[0]->id);
          if($request->points > $transfer->points)
            return response()->json(['message'=>'Sorry, you don\'t have enough points to transfer to.','user'=>$to->lastname.', '.$to->firstname,'success'=>0]);
          else
          {
            $transfer->points -= $request->points;
            $transfer->push();
            $beginningBal = null;$endingBal=null;

            
            if(count((array)$existing_to) > 0 )
            {
              $ntransfer = Point::find($existing_to[0]->id);
              $beginningBal = $ntransfer->points;
              $ntransfer->points += $request->points;
              $ntransfer->push();


            }else{
              // meaning wala pa, so default + transfered
              $beginningBal = 100;
              $ntransfer = new Point;
              $ntransfer->idnumber = $to->id;
              $ntransfer->points = $request->points + $beginningBal;
              $ntransfer->save();
            }

            // we now save record of transfer
            $record = new Reward_Transfers;
            $record->from_user = $this->user->id;
            $record->to_user = $to->id;
            $record->beginningBal = $beginningBal;
            $record->transferedPoints = $request->points;
            $record->notes = $request->notes;
            $record->save();

            

          }

        }else
        {
          //default 100pt from nagtransfer
          $beginningBal = 100;
          $transfer = new Point;
          $transfer->idnumber = $this->user->id;
          $transfer->points =  ($beginningBal - $request->points);
          $transfer->save();

          //then update yung pinaglipatan
           if(count((array)$existing_to) > 0 )
              {
                $ntransfer = Point::find($existing_to[0]->id);
                $beginningBal = $ntransfer->points;
                $ntransfer->points += $request->points;
                $ntransfer->push();


              }else{
                // meaning wala pa, so default + transfered
                $beginningBal = 100;
                $ntransfer = new Point;
                $ntransfer->idnumber = $to->id;
                $ntransfer->points = $request->points + $beginningBal;
                $ntransfer->save();
              }

          // we now save record of transfer
          $record = new Reward_Transfers;
          $record->from_user = $this->user->id;
          $record->to_user = $to->id;
          $record->beginningBal = $beginningBal;
          $record->transferedPoints = $request->points;
          $record->notes = $request->notes;
          $record->save();


        }

        if( \Auth::user()->id !== 564 ) {
              $file = fopen('public/build/rewards.txt', 'a') or die("Unable to open logs");
                fwrite($file, "-------------------\n Transfer ".$request->points. "pts to {". $request->id_to. "} on ".Carbon::now('GMT+8')->format('Y-m-d H:i')." by [". \Auth::user()->id."] ".\Auth::user()->lastname."\n");
                fclose($file);
            }

        return response()->json(['message'=>' reward points successfully transfered to: ','user'=>$to->lastname.', '.$to->firstname,'success'=>1]);
      }


      

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
        $hrTeam = collect(DB::table('team')->where('campaign_id',$hrDept->id)->select('team.user_id')->get())->pluck('user_id')->toArray();
        (in_array($this->user->id, $hrTeam)) ? $isHR=true : $isHR=false;


        

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
                $cmp = Campaign::find($c->pivot->campaign_id);

                if($ihCamp->disabled){ //do nothing

                }else
                {
                  if (count($leadershipcheck->myCampaigns) <= 1){
                    
                    $camps .='<a href="../campaign/'.$cmp->id.'" target="_blank" >'. $cmp->name.' </a>';
                   }
                    
                    else $camps .= '<a href="../campaign/'.$cmp->id.'" target="_blank" >'. $cmp->name.' </a> , ';

                }

                   

                

              }
              

            } else $camps = '<a href="../campaign/'.$user->campaign->first()->id.'" target="_blank" >'.$user->campaign->first()->name.'</a>';

            if (strlen($camps) < 1)
              $camps = '<a href="../campaign/'.$user->campaign->first()->id.'" target="_blank" >'.$user->campaign->first()->name.'</a>';

           
           


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

          
           
        //******* get user evaluations ***********
        //******* but first, check kung may movement na naregular sya
        //******* if meron, check for each eval kung pasok sa range
        //******* if pasok sa range, dun ang start ng counting of total days
        //******* if wala, proceed as normal counting for missing eval days

        $regularizedMvt = DB::table('movement')->where('movement.user_id',$user->id)->
                              leftJoin('movement_statuses','movement_statuses.movement_id','=','movement.id')->
                              select('movement.effectivity','movement.personnelChange_id as type','movement_statuses.status_id_old as oldStat','movement_statuses.status_id_new as newStat')->where('movement.personnelChange_id',3)->
                              orderBy('movement.effectivity','DESC')->get();

                              //Reg, Consultant, Project based, Regular Parttime
        $validMvt = collect($regularizedMvt)->whereIn('newStat',[4,5,11,15]);

        //return $validMvt->first()->effectivity;

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

            }elseif($setting->id == 4){ //EXTENDED CONTRACTUAL
              $hired = User::find($e[0]['eval'][0]['user_id'])->dateHired;
              $start = Carbon::parse($hired,"Asia/Manila");
              $end = Carbon::parse($hired,"Asia/Manila")->addMonths(3);
              $totalDays = $start->diffInDays($end);

            }
            else{
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

            }elseif($setting->id == 4){ //EXTENDED CONTRACTUAL
              $hired = User::find($e[0]['eval'][0]['user_id'])->dateHired;
              $start = Carbon::parse($hired,"Asia/Manila");
              $end = Carbon::parse($hired,"Asia/Manila")->addMonths(3);
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

                //*** here we check kung may applicable movement ba
                if(count($validMvt) > 0 && (($setting->id == 1) || ($setting->id == 2)) )
                {
                  $mvtEffectivity = Carbon::parse($validMvt->first()->effectivity,'Asia/Manila');
                  if ($mvtEffectivity->format('Y-m-d') >= $startP->format('Y-m-d') && $mvtEffectivity->format('Y-m-d')<= $endP->format('Y-m-d')  )
                  {


                    $totalDays = $mvtEffectivity->diffInDays($end);
                    
                    if($totalDays !== 0) 
                    {
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
                      'finalGrade'=> $finalGrade+=(float)$g,
                      'daysCtr'=> $daysCtr+=$daysHandled,
                      'missing'=> $totalDays-$daysCtr,
                      'details'=>$ev['eval']]);
                    }
                     else {
                      $g = number_format(($ev['eval']->first()->overAllScore),2);
                      $userEvals->push([
                      'evalTitle'=> $ev['evalTitle'],
                      'by'=>$ev['evalBy'],
                      'start'=>$start->format('Y-m-d'),
                      'end'=>$end->format('Y-m-d'),
                      'totalDays'=>$totalDays, 
                      'sP'=>$startP->format('Y-m-d'), 
                      'eP'=> $endP->format('Y-m-d'), 
                      'daysHandled'=>$daysHandled, 
                      'percentage'=> 100,
                      'grade'=> $g,
                      'finalGrade'=> $finalGrade+=(float)$g,
                      'daysCtr'=> $daysCtr+=$daysHandled,
                      'missing'=> $totalDays-$daysCtr,
                      'details'=>$ev['eval']]);
                     }
                      
                   
                    

                  }else
                  {
                    goto pushStuff;

                  }

                } else
                {
                  pushStuff:

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
                  'finalGrade'=> $finalGrade+=(float)$g,
                  'daysCtr'=> $daysCtr+=$daysHandled,
                  'missing'=> $totalDays-$daysCtr,
                  'details'=>$ev['eval']]);

                }

                

                

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
          $file = fopen('public/build/rewards.txt', 'a') or die("Unable to open logs");
            fwrite($file, "-------------------\n Viewed Profile of user: ".$user->lastname."[".$user->id."] --" . $correct->format('M d h:i A'). " by [". $this->user->id."] ".$this->user->lastname."\n");
            fclose($file);
        }

        //********* check for new ID picture *********
        
         $img = storage_path().'/uploads/id/'.$user->id.'_portrait.png';
         (file_exists($img)) ? $hasNewPhoto = true : $hasNewPhoto =false;
         
         
        
            
        
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

           

            $shifts = $this->generateShifts('12H','full');
            $partTimes = $this->generateShifts('12H','part');
            //return $partTimes;
            
            return view('people.show', compact('isWorkforce','isBackoffice', 'theOwner', 'canViewAllEvals','anApprover', 'approvers', 'user', 'greeting', 'immediateHead','canCWS', 'canChangeSched', 'canMoveEmployees', 'canEditEmployees', 'camps','workSchedule', 'userEvals','shifts','partTimes', 'hasNewPhoto','isHR'));

            
            
        } else return view('people.profile', compact('anApprover', 'approvers', 'user','immediateHead','canCWS','canChangeSched', 'canMoveEmployees', 'canEditEmployees', 'camps','workSchedule', 'greeting','isHR'));
                   
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
        $employee->employeeCode = $request->employeeCode;
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


         $correct = Carbon::now('GMT+8');
        //log access
        if($this->user->id !== 564 ) {
                      
                      $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
                        fwrite($file, "-------------------\n Viewed REQUESTS [". $this->user->id."] ".$this->user->lastname." of [".$user->id."] on ". $correct->format('M d h:i A').  "\n");
                        fclose($file);
                    } 


        if ($canView || $this->user->id == $id || ($isWorkforce && !$isBackoffice))
          return view('people.myRequests',['user'=>$user,'forOthers'=>true,'anApprover'=>$canView,'isWorkforce'=>$isWorkforce,'isBackoffice'=>$isBackoffice]);
        else
          return view('access-denied');

      }
      

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
        $employee->employeeCode = Input::get('employeeCode');
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

    public function updateBday($id, Request $request)
    {
        $employee = User::find($id);
        $employee->birthday = Carbon::parse($request->bmonth."/".$request->bdate."/".$request->byear)->format('Y-m-d');
        $employee->push();

        /* -------------- log updates made --------------------- */
         $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
            fwrite($file, "\n-------------------\n". $employee->lastname .",". $employee->firstname." BDAY ". Carbon::now('GMT+8')->format('Y-m-d H:i'). " by ". $this->user->firstname.", ".$this->user->lastname."\n");
            fclose($file);
        return redirect()->back();
           

        //return response()->json($employee);
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

    public function updateProfilepic($id)
    {
      $user = User::find($id);

      //return $inactiveUsers1;
        $correct = Carbon::now('GMT+8');

        if($this->user->id !== 564 ) {
            $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
            fwrite($file, "-------------------\n Cropped [".$id."] on " . $correct->format('M d h:i A'). " by [". $this->user->id."] ".$this->user->lastname."\n");
            fclose($file);
        }

      if (count($user)>1){
        $img = storage_path().'/uploads/id/'.$user->id.'_portrait.png';
        $imgfile = '../storage/uploads/id/'.$user->id.'_portrait.png';
        (file_exists($img)) ? $hasNewPhoto = true : $hasNewPhoto =false;
        
        return view('people.profilepic',compact('id', 'imgfile','hasNewPhoto'));

      } else {
        $user = collect(['id'=>$id]);
        //$user->push
        $img = storage_path().'/uploads/id/'.$id.'_portrait.png';
        $imgfile = '../storage/uploads/id/'.$id.'_portrait.png';
        (file_exists($img)) ? $hasNewPhoto = true : $hasNewPhoto =false;
       
        return view('people.profilepic',compact('id', 'imgfile','hasNewPhoto'));

      }

      

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


    public function wfh(Request $request)
    {
      $u = User::find($request->id);
      $u->isWFH = $request->enableWFH;
      $u->push();
      return $u;

    }


    public function workAnnivCelebrators()
    {
      $d = $this->getWorkAnnivs(Input::get('m_from'), Input::get('d_from'),Input::get('m_to'),Input::get('d_to'));

      return response()->json(['data'=>$d]);
      //return response()->json([ 'recordsTotal'=>count($d),'data'=>$d ]);
    }

    
}
