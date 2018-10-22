<?php

namespace OAMPI_Eval\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Yajra\Datatables\Facades\Datatables;
use \Mail;
use \PDF;

use OAMPI_Eval\Http\Requests;
use OAMPI_Eval\User;
use OAMPI_Eval\User_Notification;
use OAMPI_Eval\Notification;
use OAMPI_Eval\UserType;
use OAMPI_Eval\Team;
use OAMPI_Eval\Floor;
use OAMPI_Eval\ImmediateHead;
use OAMPI_Eval\ImmediateHead_Campaign;
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




class MovementController extends Controller
{
    protected $user;
    protected $movement;

    public function __construct(Movement $movement)
    {
        $this->middleware('auth');
        $this->user =  User::find(Auth::user()->id);
        $this->movement = $movement;
    }

     public function index()
    {

        $roles = UserType::find($this->user->userType_id)->roles->pluck('label'); //->where('label','MOVE_EMPLOYEES');
        $canMoveEmployees =  ($roles->contains('MOVE_EMPLOYEE')) ? '1':'0';
        $canDistributeTeam =  ($roles->contains('MANAGE_TEAM_DISTRIBUTION')) ? '1':'0';

        if (!$canMoveEmployees && !$canDistributeTeam){
            return view('access-denied');

        } else
        {
            //$myCampaign = $this->user->campaign; 
            $leadershipcheck = ImmediateHead::where('employeeNumber', $this->user->employeeNumber)->first();



            if ( empty($leadershipcheck) )
            {
                $myCampaign = $this->user->campaign; // ****** means isa lang campaign and not a leader

            } else {

                $myCampaign = $leadershipcheck->campaigns; // ****** multiple campaign leader

            }


            //$TLs = ImmediateHead::where('campaign_id', $myCampaign->id)->orderBy('lastname','ASC')->get();
            $TLs = Campaign::find($myCampaign->first()->id)->leaders;
            
            $users = User::where('lastname','!=','')->orderBy('lastname', 'ASC')->get();
            $campaigns = Campaign::orderBy('name', 'ASC')->get();
            $statuses = Status::all();

            $roles = UserType::find($this->user->userType_id)->roles->pluck('label'); //->where('label','MOVE_EMPLOYEES');
            $canMoveEmployees =  ($roles->contains('MOVE_EMPLOYEE')) ? '1':'0';
            $canDistributeTeam =  ($roles->contains('MANAGE_TEAM_DISTRIBUTION')) ? '1':'0';
            $movements = Movement::all();
            


            if (!$canMoveEmployees && $canDistributeTeam) //user can't move employees but can distribute == TL level
                return view('people.movement', compact('TLs','myCampaign'));
                
            else return view('people.movementAdmin', compact('users', 'myCampaign', 'campaigns','statuses', 'movements')); //assumed super admin or has HR roles


        }//end if else canmove

    	
        
    }

    public function changePersonnel($id)
    {

        $roles = UserType::find($this->user->userType_id)->roles->pluck('label'); //->where('label','MOVE_EMPLOYEES');
        $canMoveEmployees =  ($roles->contains('MOVE_EMPLOYEE')) ? '1':'0';
        $canMoveOthers =  ($roles->contains('EDIT_OTHERS\'_MOVEMENT')) ? '1':'0';
        $canDistributeTeam =  ($roles->contains('MANAGE_TEAM_DISTRIBUTION')) ? '1':'0';

        $coll = new Collection;

        
        

        if (!$canMoveEmployees && !$canDistributeTeam){
            return view('access-denied');

        } 
        else 
        {
            // first, look for any existing employee movement
            // if none, then present form..else display list with options

                $personnel = User::find($id);
                $sup = $personnel->supervisor;
                $immediateHead = ImmediateHead_Campaign::find($sup->immediateHead_Campaigns_id);
                //$immediateHead = ImmediateHead::find(ImmediateHead_Campaign::find($sup->immediateHead_Campaigns_id)->immediateHead_id); //ImmediateHead::find($sup->first()->immediateHead_id);


                if ($canMoveEmployees && !$canMoveOthers ) 
                { //not super admin but a leader so you need to display their name and from what campaign
                    
                    $req = ImmediateHead::where('employeeNumber',$this->user->employeeNumber)->first(); 
                    $requestor = ImmediateHead_Campaign::where('immediateHead_id',$req->id)->where('campaign_id',User::find($id)->team->campaign_id)->first();
                    $requestorPosition = Position::find($this->user->position_id)->name;
                    //$requestorCampaign = Campaign::find(ImmediateHead_Campaign::where('immediateHead_id',$requestor->id)->where('campaign_id',$personnel->team->campaign_id)->first()->campaign_id);

                    
                    $requestorCampaign = Campaign::find($requestor->campaign_id); //Campaign::find(ImmediateHead_Campaign::where('immediateHead_id',$requestor->id)->first()->campaign_id);

                    if ( file_exists('public/img/employees/'.$this->user->id.'-sign.png') )
                             {
                                $signatureRequestedBy = asset('public/img/employees/'.$this->user->id.'-sign.png');
                             } else {
                                $signatureRequestedBy = asset('public/img/employees/signature.png');
                             }

                       
                   


                    
                   

                } else {$requestor = null; $requestorPosition=null; $requestorCampaign=null;} //user is a super admin, he can assign who the requestor is

               


                $changes = PersonnelChange::all();
                $floors = Floor::all();
               


                if ($personnel->movements->isEmpty()) {



                    //$TLs = ImmediateHead::where('lastname','!=','')->orderBy('lastname','ASC')->get();
                    $TLs = ImmediateHead_Campaign::all();
                    //$myCampaign = $this->user->campaign; 
                    $users = User::where('lastname','!=','')->orderBy('lastname', 'ASC')->get();
                    
                    $statuses = Status::all();
                    $positions = Position::where('name','!=','')->orderBy('name','ASC')->get();
                    $hrDept = Campaign::where('name','HR')->first();
                    //$hrs = ImmediateHead::where('campaign_id', $hrDept->id)->get();
                    //$hrs = User::where('campaign_id', $hrDept->id)->get();
                    $hrs = Team::where('campaign_id', $hrDept->id)->get();

                   $campaigns = Campaign::where('id', '!=', $personnel->campaign_id)->where('name','!=','')->orderBy('name', 'ASC')->get();
                    $leaders1 = new Collection;

                    

                    foreach ($TLs as $tl) {

                        //$data = $tl->userData;// User::where('employeeNumber', $tl->employeeNumber)->first();
                        $data = ImmediateHead::find($tl->immediateHead_id)->userData;

                        if( !empty($data['firstname']) &&  !empty($data['lastname']) && $data['firstname'] !== " " && $data['lastname'] !== " " ) //to ensure no dummy DB entries
                        {
                            $hisPOsition = Position::where('id',$data['position_id'])->first();
                        

                            
                                $leaders1->push([
                                'id'=>$tl->id,
                                'position'=> $hisPOsition['name'], //Position::find($hisPOsition['position_id']), //->position,
                                'lastname'=>  $data['lastname'], //$tl->lastname,
                                'firstname'=> $data['firstname'], //$tl->firstname,
                                'campaign'=> Campaign::find($tl->campaign_id)->name, // $tl->campaigns->first()->name,
                                'campaign_id'=> $tl->campaign_id]); // $tl->campaigns->first()->id]);

                           

                        }
                        

                        
                    }

                    //return $coll;
                    $leaders = $leaders1->sortBy('lastname');
                   
                    $hrPersonnels = new Collection;

                    //return $hrs;

                    foreach ($hrs as $tl) {
                        //$hisPOsition = User::where('employeeNumber', $tl->employeeNumber)->first();

                        $data = User::find($tl->user_id); // $tl->userData;// User::where('employeeNumber', $tl->employeeNumber)->first();

                        //remove all resigned | terminated | endo
                        if ($data->status_id !== 7 && $data->status_id !== 8 && $data->status_id !== 9 )
                        {
                            
                             $hisPOsition = Position::where('id',$data->position_id)->first()->name;


                            //$hisPOsition = User::find($tl->user_id);
                            $hrPersonnels->push([
                                'id'=>$tl->id,
                                'position'=> $hisPOsition, //$posid, //hisPOsition['name'],
                                'lastname'=> $data->lastname,
                                'firstname'=>$data->firstname,
                                'campaign'=>$data->campaign[0]->name ]); //[0]->name ]);

                        }


                       
                    } 

                    
                       return view('people.changePersonnel', compact('users','leaders','requestor', 'signatureRequestedBy','requestorPosition', 'requestorCampaign', 'hrPersonnels',  'campaigns', 'floors', 'personnel', 'immediateHead', 'statuses','changes', 'positions'));//'myCampaign',

                } else 
                {
                    $moves = $personnel->movements->sortByDesc('effectivity');
                    $movements = new Collection;

                    foreach($moves as $m){

                        switch ($m->personnelChange_id) {
                            case 1: {
                                        $oldData = Movement_ImmediateHead::where('movement_id', $m->id)->first();
                                        


                                        $fromData = ImmediateHead_Campaign::find($oldData->imHeadCampID_old);

                                        $toData = ImmediateHead_Campaign::find($oldData->imHeadCampID_new);
                                        $from = ImmediateHead::find($fromData->immediateHead_id); //$fromData->immediateHead; //ImmediateHead_Campaign::find($oldData->imHeadCampID_old)->immediateHead;
                                        $to = ImmediateHead::find($toData->immediateHead_id); //$toData->immediateHead; // ImmediateHead_Campaign::find($oldData->imHeadCampID_new)->immediateHead;

                                        //$newValue = $to->firstname.' '.$to->lastname.' | '. $to->campaign[0]->name;
                                        $newValue = $to->firstname.' '.$to->lastname.' [ '. Campaign::find($toData->campaign_id)->name.' ]';
                                        $oldValue = $from->firstname.' '.$from->lastname.'  [ '. Campaign::find($fromData->campaign_id)->name.' ]';
                                       
                                        
                                        

                                    } break;
                            case 2: {
                                        $oldData = Movement_Positions::where('movement_id', $m->id)->first();
                                        $from = Position::find($oldData->position_id_old);
                                        $to = Position::find($oldData->position_id_new);
                                        $oldValue = $from->name;
                                        $newValue = $to->name;

                                    } break;
                            case 3: {
                                        $oldData = Movement_Status::where('movement_id', $m->id)->first();
                                        $from = Status::find($oldData->status_id_old);
                                        $to = Status::find($oldData->status_id_new);
                                        $oldValue = $from->name;
                                        $newValue = $to->name;

                                    } break;
                           
                        }
                        $movements->push([
                            'id'=> $m->id,
                            'user_id'=>$m->user_id,
                            'movementType'=> $m->type->name,
                            'oldValue'=> $oldValue,
                            'newValue'=> $newValue,
                            'fromPeriod'=> date('M. d, Y', strtotime($m->fromPeriod) ),
                            'effectivity'=> date('M. d, Y', strtotime($m->effectivity) ),
                              ]); //ImmediateHead::find($m->newHead_id)->campaign[0]->name
                    }

                   
                   
                    return view('people.movement-employee', compact('movements','canMoveOthers', 'requestor', 'personnel'));

                } //end else employee has existing movements



        }//end if else
        
    }

     public function createNew($id)
    {
        
        $changes = PersonnelChange::all();

         $roles = UserType::find($this->user->userType_id)->roles->pluck('label'); //->where('label','MOVE_EMPLOYEES');
        $canMoveEmployees =  ($roles->contains('MOVE_EMPLOYEE')) ? '1':'0';
        $canMoveOthers =  ($roles->contains('EDIT_OTHERS\'_MOVEMENT')) ? '1':'0';
        $canDistributeTeam =  ($roles->contains('MANAGE_TEAM_DISTRIBUTION')) ? '1':'0';



        $personnel = User::find($id);
        $sup = $personnel->supervisor;
        //$immediateHead = ImmediateHead::find(ImmediateHead_Campaign::find($sup->immediateHead_Campaigns_id)->immediateHead_id); 
        $immediateHead = ImmediateHead_Campaign::find($sup->immediateHead_Campaigns_id);



        $floors = Floor::all();
        $statuses = Status::all();
        $positions = Position::where('name','!=','')->orderBy('name','ASC')->get();

        //$TLs = ImmediateHead::where('lastname','!=','')->orderBy('lastname','ASC')->get();
        $TLs = ImmediateHead_Campaign::all();
        $users = User::where('lastname','!=','')->orderBy('lastname', 'ASC')->get();

        $hrDept = Campaign::where('name','HR')->first();
        $hrs = Team::where('campaign_id', $hrDept->id)->get();




        $campaigns = Campaign::where('id', '!=', $personnel->campaign_id)->where('name','!=','')->orderBy('name', 'ASC')->get();

            $leaders1 = new Collection;

            foreach ($TLs as $tl) {

                        //$data = $tl->userData;// User::where('employeeNumber', $tl->employeeNumber)->first();
                        $data = ImmediateHead::find($tl->immediateHead_id)->userData;

                        if( !empty($data['firstname']) &&  !empty($data['lastname']) && $data['firstname'] !== " " && $data['lastname'] !== " " ) //to ensure no dummy DB entries
                        {
                            $hisPOsition = Position::where('id',$data['position_id'])->first();
                                                    
                                $leaders1->push([
                                'id'=>$tl->id,
                                'position'=> $hisPOsition['name'], //Position::find($hisPOsition['position_id']), //->position,
                                'lastname'=>  $data['lastname'], //$tl->lastname,
                                'firstname'=> $data['firstname'], //$tl->firstname,
                                'campaign'=> Campaign::find($tl->campaign_id)->name, // $tl->campaigns->first()->name,
                                'campaign_id'=> $tl->campaign_id]); // $tl->campaigns->first()->id]);
                           

                        }
                                                
            }

            $leaders = $leaders1->sortBy('lastname');


            $hrPersonnels = new Collection;

            foreach ($hrs as $tl) {
                        //$hisPOsition = User::where('employeeNumber', $tl->employeeNumber)->first();

                        $data = User::find($tl->user_id); // $tl->userData;// User::where('employeeNumber', $tl->employeeNumber)->first();

                        //remove all resigned | terminated | endo
                        if ($data->status_id !== 7 && $data->status_id !== 8 && $data->status_id !== 9 )
                        {
                            
                             $hisPOsition = Position::where('id',$data->position_id)->first()->name;


                            //$hisPOsition = User::find($tl->user_id);
                            $hrPersonnels->push([
                                'id'=>$tl->id,
                                'position'=> $hisPOsition, //$posid, //hisPOsition['name'],
                                'lastname'=> $data->lastname,
                                'firstname'=>$data->firstname,
                                'campaign'=>$data->campaign[0]->name ]); //[0]->name ]);

                        }


                       
                    } 

            



             if ($canMoveEmployees && !$canMoveOthers ) { //not super admin but a leader so you need to display their name and from what campaign

                // *** you need to check if this employee has been moved or not
                    //$requestor = ImmediateHead::where('employeeNumber',$this->user->employeeNumber)->first(); 
                    $req = ImmediateHead::where('employeeNumber',$this->user->employeeNumber)->first(); 
                    $requestor = ImmediateHead_Campaign::where('immediateHead_id',$req->id)->where('campaign_id',User::find($id)->team->campaign_id)->first();
                    $requestorPosition = Position::find($this->user->position_id)->name;

                    if (count($requestor->myCampaigns) > 1) //if marami syang campaigns na hawak, indicate mo lahat
                    {
                        foreach ($requestor->myCampaigns as $key) {
                            $requestorCampaign .= Campaign::find($key->campaign_id)->name . ", ";
                        }

                    } else $requestorCampaign = Campaign::find($requestor->campaign_id);

                    if ( file_exists('public/img/employees/'.$req->userData->id.'-sign.png') )
                             {
                                $signatureRequestedBy = asset('public/img/employees/'.$req->userData->id.'-sign.png');
                             } else {
                                $signatureRequestedBy = asset('public/img/employees/signature.png');
                             }
                   

                } else {$requestor = null; $requestorPosition=null; $requestorCampaign=null;} //user is a super admin, he can assign who the requestor is

                //return $requestor;



                //*** add-on from submitted Regularization forms:
                //*** you may pass-on the evaluator para sya yung maging requested by
                if (!empty(Input::get('evaluatedBy')))
                {
                      $requestor = ImmediateHead_Campaign::find(Input::get('evaluatedBy'));
                      $req = ImmediateHead::find($requestor->immediateHead_id);
                      $requestorPosition = Position::find($req->userData->position_id)->name;
                      if (count($req->myCampaigns) > 1) //if marami syang campaigns na hawak, indicate mo lahat
                        {
                            foreach ($req->myCampaigns as $key) {
                                $requestorCampaign .= Campaign::find($key->campaign_id)->name . ", ";
                            }

                        } else $requestorCampaign = Campaign::find($requestor->campaign_id);

                        if ( file_exists('public/img/employees/'.$req->userData->id.'-sign.png') )
                             {
                                $signatureRequestedBy = asset('public/img/employees/'.$req->userData->id.'-sign.png');
                             } else {
                                $signatureRequestedBy = asset('public/img/employees/signature.png');
                             }


                }

              

                return view('people.changePersonnel', compact('signatureRequestedBy', 'users','canMoveOthers','requestor', 'requestorPosition', 'requestorCampaign','leaders','immediateHead', 'hrPersonnels', 'campaigns', 'floors', 'personnel','statuses','changes', 'positions')); //'myCampaign', 

    }

    public function findInstances(Request $request)
    {
        $movements = Movement::where('user_id', $request->user_id)->where('personnelChange_id', $request->movementType)->orderBy('id','DESC')->get();
        $reply = new Collection;
        $employeeToBeMoved = User::find($request->user_id);

        if ($movements->isEmpty()){

            //if no existing prior movements, check first whether Contractual, Trainee, Regular, etc:            

            //depends per type of movement
            switch ($request->movementType) {
                case '1': { //Program change

                           
                            $reply->push(['fromPeriod'=> "$employeeToBeMoved->dateHired", 'user_id'=>$employeeToBeMoved->id]);

                            } break;
                
                case '2': { //Position change
                            $reply->push(['fromPeriod'=> User::find($request->user_id)->dateHired]);

                            } break;

                case '3': { //Status change
                                switch ($request->old_id) {
                                    case '1': { $reply->push(['fromPeriod'=> User::find($request->user_id)->dateHired]);} break; //CONTRACTUAL
                                    case '2': { $reply->push(['fromPeriod'=> User::find($request->user_id)->dateHired]);} break; //TRAINEE
                                    case '3': { $reply->push(['fromPeriod'=> User::find($request->user_id)->dateHired]);} break; //PROBI
                                    case '4': { $reply->push(['fromPeriod'=> User::find($request->user_id)->dateRegularized]);} break; //REGULAR
                                    default : { $reply->push(['fromPeriod'=> User::find($request->user_id)->dateHired]);} break; //others
                                }

                            } break;            
            }

            
            return response()->json($reply);

           

        } else  //may existing movements na, kunin mo yung saved sa DB
        {
            $priorMovement = $movements->first();
            $reply->push(['fromPeriod'=> $priorMovement->effectivity ]);
            //$reply->push(['fromPeriod'=> User::find($request->user_id)->dateHired]);
            //return response()->json($movements->first());
            return response()->json($reply);
        }

    }

    public function getAllMovements(){
        $allmovements = Movement::orderBy('id','DESC')->get();


        $movements = new Collection;

        foreach ($allmovements as $m) {
            $user = User::find($m->user_id);

             if ( file_exists('public/img/employees/'.$user->id.'.jpg') )
             {
                $img = asset('public/img/employees/'.$user->id.'.jpg');
             } else {
                $img = asset('public/img/useravatar.png');
             }

             if (count($user->campaign) >= 1)
             {
                $movements->push([
                                'profilePic'=>$img,
                                'user_id'=>$user->id,
                                'id'=>$m->id,
                                'lastname'=>$user->lastname,
                                'firstname'=>$user->firstname,
                                'campaign'=> $user->campaign->first()->name,
                                'effectivity'=> date("M d, Y",strtotime( $m->effectivity)),
                                'type'=> PersonnelChange::find($m->personnelChange_id)->name ]);

             }else {
                $movements->push([
                                'profilePic'=>$img,
                                'user_id'=>$user->id,
                                'id'=>$m->id,
                                'lastname'=>$user->lastname,
                                'firstname'=>$user->firstname,
                                'campaign'=> $user->campaign->first(),
                                'effectivity'=> date("M d, Y",strtotime( $m->effectivity)),
                                'type'=> PersonnelChange::find($m->personnelChange_id)->name ]);

             }

            
        }
        
        return Datatables::collection($movements)->make(true);
    }

    public function store(Request $request)
    {

        $movement = new Movement;
        $movement->user_id = $request->user_id;
        // $movement->oldHead_id = $request->oldHead_id;
        // $movement->newHead_id = $request->newHead_id;

        if ($request->withinProgram == 'true') $movement->withinProgram = true;
        else $movement->withinProgram = false;

                        
        $movement->fromPeriod = date("Y-m-d", strtotime($request->fromPeriod));
        $movement->effectivity = date("Y-m-d", strtotime($request->effectivity));
        $movement->isApproved = $request->isApproved;
        $movement->isNotedFrom = true;
        $movement->isNotedTo = false;
        $movement->isDone = false;
        $movement->dateRequested = date("Y-m-d", strtotime($request->dateRequested));
        $movement->requestedBy = ImmediateHead_Campaign::find($request->requestedBy)->immediateHead_id;
        $movement->notedBy = $request->notedBy;
        $movement->personnelChange_id = $request->personnelChange_id;

        $movement->save();

        $old_id = $request->old_id;
        $new_id = $request->new_id;

        //get all HR ADMINs that will receive notifications
        $roles = UserType::find($this->user->userType_id)->roles->pluck('label'); //->where('label','MOVE_EMPLOYEES');
        $canMoveEmployees =  ($roles->contains('MOVE_EMPLOYEE')) ? '1':'0';
        


        // *** once you save a movement, determine what type it is and then save corresponding relational tables
        switch ($movement->personnelChange_id) {
            case 1: { //immediateHead
                        

                        /* ------- CHECK FIRST IF HR INTERVENTION IS NEEDED
                         - pag within campaign, no need. Just email the TL na pinaglipatan
                         - else needs approval from HR before saving
                         */ 

                        $moveHead = new Movement_ImmediateHead;
                        $moveHead->movement_id = $movement->id;
                        $moveHead->imHeadCampID_old = $old_id;
                        $moveHead->imHeadCampID_new = $new_id;
                        $moveHead->newFloor = $request->newFloor;
                        //$moveHead->newCampaign = $request->newCampaign;
                        $moveHead->oldFloor = $request->oldFloor;
                        $moveHead->save();




                        // ***** add in NOTIFICATION for TL

                            $notification = new Notification;
                            $notification->relatedModelID = $movement->id;
                            $notification->type = 2;
                            $notification->from = $movement->requestedBy;
                            $notification->save();


                         if (!$movement->withinProgram) 
                         { 
                            $employee = User::find($movement->user_id);
                             

                            if ($this->user->userType_id != 3 && $this->user->userType_id != 4) //if SUPER ADMINS and HR admin
                            {
                                if (  date("Y-m-d", strtotime($request->effectivity)) <= date("Y-m-d") ) 
                                 { 
                                    $updateTeam = $employee->team;
                                    $updateTeam->immediateHead_Campaigns_id = $new_id;
                                    $updateTeam->floor_id = $request->newFloor;
                                    $updateTeam->campaign_id = $request->campaign_id;
                                    $updateTeam->push();
                                    $movement->isDone = true;
                                 }                

                                    
                                $movement->isApproved = true;
                                $movement->push();

                                $newTL = ImmediateHead::find(ImmediateHead_Campaign::find($new_id)->immediateHead_id);


                                $nu = new User_Notification;
                                $nu->user_id = $newTL->userData->id;
                                $nu->notification_id = $notification->id;
                                $nu->seen = false;
                                $nu->save();


                                // ***** add in NOTIFICATION for employee involved

                                $notification2 = new Notification;
                                $notification2->relatedModelID = $movement->id;
                                $notification2->type = 2;
                                $notification2->from = $movement->requestedBy;
                                $notification2->save();

                                $nu = new User_Notification;
                                $nu->user_id = $movement->user_id;
                                $nu->notification_id = $notification2->id;
                                $nu->seen = false;
                                $nu->save();

                                // NOW, EMAIL THE TL CONCERNED
                               
                                 Mail::send('emails.personnelChange', ['user' => $newTL, 'employee'=>$employee, 'movement'=>$movement, 'notification'=>$notification], function ($m) use ($newTL, $employee) 
                                 {
                                    $m->from('OES@openaccessbpo.net', 'OES-OAMPI Evaluation System');
                                    $m->to($newTL->userData->email, $newTL->lastname)->subject('Personnel Change Notice');     

                                    /* -------------- log updates made --------------------- */
                                         $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
                                            fwrite($file, "-------------------\n Email sent to ". $newTL->userData->email."\n");
                                            fwrite($file, "\n New Movement:  ". $employee->firstname." ".$employee->lastname. " by ". $this->user->firstname. " ". $this->user->lastname."\n");
                                            fclose($file);                      
                                

                                }); //end mail

                                return response()->json(['withinProgram'=>$movement->withinProgram , 'id'=>$movement->id, 'info1'=>$employee->firstname." ".$employee->lastname, 'info2'=>date("M d, Y",strtotime($movement->effectivity) )]);
                           
                                      

                            } 

                            else //user is a typical leader
                            {
                                $nu = new User_Notification;
                                $nu->user_id = $movement->notedBy;
                                $nu->notification_id = $notification->id;
                                $nu->seen = false;
                                $nu->save();

                                $hrAdmins = User::where('userType_id',5)->get();

                                foreach ($hrAdmins as $key ) {

                                    $nu = new User_Notification;
                                    $nu->user_id = $key->id;
                                    $nu->notification_id = $notification->id;
                                    $nu->seen = false;
                                    $nu->save();
                                }
                                return response()->json(['withinProgram'=>$movement->withinProgram , 'id'=>$movement->id, 'info1'=>$employee->firstname." ".$employee->lastname, 'info2'=>date("M d, Y",strtotime($movement->effectivity) )]);


                            }//end if not typical leader or agent

                        } else 
                        { 
                            $employee = User::find($movement->user_id);


                             if (  date("Y-m-d", strtotime($request->effectivity)) <= date("Y-m-d") ) //if effectivity is past or today
                                {

                                    //get the Team table
                                    
                                    $updateTeam = $employee->team;
                                    $updateTeam->immediateHead_Campaigns_id = $new_id;
                                    $updateTeam->floor_id = $request->newFloor;
                                    $updateTeam->campaign_id = $request->campaign_id;
                                    $updateTeam->push();


                                    $movement->isDone = true;
                                }                

                                $movement->push();
                                $newTL = ImmediateHead::find(ImmediateHead_Campaign::find($new_id)->immediateHead_id);


                                $nu = new User_Notification;
                                $nu->user_id = $newTL->userData->id;
                                $nu->notification_id = $notification->id;
                                $nu->seen = false;
                                $nu->save();


                                 // ***** add in NOTIFICATION for HR concerned

                                 
                                $nu = new User_Notification;
                                $nu->user_id = $movement->notedBy;
                                $nu->notification_id = $notification->id;
                                $nu->seen = false;
                                $nu->save();


                                // ***** add in NOTIFICATION for employee involved

                                $notification2 = new Notification;
                                $notification2->relatedModelID = $movement->id;
                                $notification2->type = 2;
                                $notification2->from = $movement->requestedBy;
                                $notification2->save();

                                $nu = new User_Notification;
                                $nu->user_id = $movement->user_id;
                                $nu->notification_id = $notification2->id;
                                $nu->seen = false;
                                $nu->save();

                                // NOW, EMAIL THE TL CONCERNED
                               
                                 Mail::send('emails.personnelChange', ['user' => $newTL, 'employee'=>$employee, 'movement'=>$movement, 'notification'=>$notification], function ($m) use ($newTL, $employee) 
                                 {
                                    $m->from('OES@openaccessbpo.net', 'OES-OAMPI Evaluation System');
                                    $m->to($newTL->userData->email, $newTL->lastname)->subject('Personnel Change Notice');     

                                    /* -------------- log updates made --------------------- */
                                         $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
                                            fwrite($file, "-------------------\n Email sent to ". $newTL->userData->email."\n");
                                            fwrite($file, "\n New Movement:  ". $employee->firstname." ".$employee->lastname. " by ". $this->user->firstname. " ". $this->user->lastname."\n");
                                            fclose($file);                      
                                

                                }); //end mail

                            return response()->json(['withinProgram'=>$movement->withinProgram , 'id'=>$movement->id, 'info1'=>$employee->firstname." ".$employee->lastname, 'info2'=>date("M d, Y",strtotime($movement->effectivity) )]);
                        }//end else 
                        break; 
                    }
                        
            case 2: { 
                        $moveHead = new Movement_Positions;
                        $moveHead->movement_id = $movement->id;
                        $moveHead->position_id_old = $old_id;
                        $moveHead->position_id_new = $new_id;
                        $moveHead->save();

                        $employee = User::find($movement->user_id);

                        $notification = new Notification;
                        $notification->relatedModelID = $movement->id;
                        $notification->type = 3;
                        $notification->from = $movement->requestedBy;
                        $notification->save();

                        

                        // ***** if the one submitting is already an HR admin or admin, approved agad
                        // ***** no need to do HR notifications

                        if ($this->user->userType_id != 3 && $this->user->userType_id != 4) //if not leader and agent
                        {

                             
                             $employee->position_id = $moveHead->position_id_new;
                             $employee->push();
                                                 
                                                
                            //inform person concerned
                            

                            $nu = new User_Notification;
                            $nu->user_id = $movement->user_id;
                            $nu->notification_id = $notification->id;
                            $nu->seen = false;
                            $nu->save();


                                    $movement->isDone=true;
                                    $movement->isApproved = true;
                                    $movement->push();

                        }
                        else
                        {
                            // ***** else
                            // ***** add in NOTIFICATION for HR concerned
                         


                            $nu = new User_Notification;
                            $nu->user_id = $movement->notedBy;
                            $nu->notification_id = $notification->id;
                            $nu->seen = false;
                            $nu->save();

                            


                            // // ***** add in NOTIFICATION for employee involved

                            // $nu = new User_Notification;
                            // $nu->user_id = $movement->user_id;
                            // $nu->notification_id = $notification->id;
                            // $nu->seen = false;
                            // $nu->save();


                            // ***** add in NOTIFICATION for HR system notif

                            $hrAdmins = User::where('userType_id',5)->get();

                            foreach ($hrAdmins as $key ) {

                                $nu = new User_Notification;
                                $nu->user_id = $key->id;
                                $nu->notification_id = $notification->id;
                                $nu->seen = false;
                                $nu->save();
                            }

                        }
                        /* -------------- log updates made --------------------- */
                                         $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
                                            fwrite($file, "-------------------\n");
                                            fwrite($file, "\n New Position for:  ". $employee->firstname." ".$employee->lastname. " by ". $this->user->firstname. " ". $this->user->lastname."\n");
                                            fclose($file);   


                            return response()->json(['interCampaign'=>true, 'withinProgram'=>$movement->withinProgram , 'id'=>$movement->id, 'info1'=>$employee->firstname." ".$employee->lastname, 'info2'=>date("M d, Y",strtotime($movement->effectivity) )]);
                        break; 
                    }

            case 3: {   
                        $moveHead = new Movement_Status;
                        $moveHead->movement_id = $movement->id;
                        $moveHead->status_id_old = $old_id;
                        $moveHead->status_id_new = $new_id;
                        $moveHead->save();

                        $employee = User::find($movement->user_id);

                        $notification = new Notification;
                        $notification->relatedModelID = $movement->id;
                        $notification->type = 4;
                        $notification->from = $movement->requestedBy;
                        $notification->save();

                        

                        // ***** if the one submitting is already an HR admin or admin, approved agad
                        // ***** no need to do HR notifications

                        if ($this->user->userType_id != 3 && $this->user->userType_id != 4) //if SUPER ADMIN and HR
                        {

                             $employee->dateRegularized = $movement->effectivity;
                             $employee->status_id = $moveHead->status_id_new;
                             $employee->push();
                                                 
                                                
                            //inform person concerned
                            

                            $nu = new User_Notification;
                            $nu->user_id = $movement->user_id;
                            $nu->notification_id = $notification->id;
                            $nu->seen = false;
                            $nu->save();


                                    $movement->isDone=true;
                                    $movement->isApproved = true;
                                    $movement->push();

                        }
                        else
                        {
                            // ***** else
                            // ***** add in NOTIFICATION for HR concerned
                         


                            $nu = new User_Notification;
                            $nu->user_id = $movement->notedBy;
                            $nu->notification_id = $notification->id;
                            $nu->seen = false;
                            $nu->save();



                            // ***** add in NOTIFICATION for HR system notif

                            $hrAdmins = User::where('userType_id',5)->get();

                            foreach ($hrAdmins as $key ) {

                                $nu = new User_Notification;
                                $nu->user_id = $key->id;
                                $nu->notification_id = $notification->id;
                                $nu->seen = false;
                                $nu->save();
                            }

                        }

                           /* -------------- log updates made --------------------- */
                                         $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
                                            fwrite($file, "-------------------\n");
                                            fwrite($file, "\n New Status for:  ". $employee->firstname." ".$employee->lastname. " by ". $this->user->firstname. " ". $this->user->lastname."\n");
                                            fclose($file); 

                            
                            return response()->json(['interCampaign'=>true, 'withinProgram'=>$movement->withinProgram , 'id'=>$movement->id, 'info1'=>$employee->firstname." ".$employee->lastname, 'info2'=>date("M d, Y",strtotime($movement->effectivity) )]);



                        break; 
                    }
        }

        
    }

    


    public function updateMovement(Request $request){
        $toDashers = $request->toDashers;
        $fromDashers = $request->fromDashers;
        $teamto = ImmediateHead::find($request->teamtoID);
        $teamfrom = ImmediateHead::find($request->teamfromID);
        
        $movements = new Collection;
        $members = array(); 

        //get those added to

        if (!empty($toDashers)){
            $memberIDs = $teamto->subordinates->pluck('id')->toArray();
            $addedMembers = array_diff($toDashers, $memberIDs);

            foreach ($addedMembers as $id) {
                $movedMember = User::find($id);
                $movedMember->immediateHead_id = $teamto->id;
                $movedMember->push();
               $members[] = $movedMember->firstname . " ". $movedMember->lastname;
            }
            $movements->push(['team'=> $teamto->firstname." ". $teamto->lastname, 'members'=>$members]);

        }
        


        //get those added from
         if (!empty($fromDashers)){
            $members = array();
            $memberIDs = $teamfrom->subordinates->pluck('id')->toArray();
            $addedMembers = array_diff($fromDashers, $memberIDs);

            foreach ($addedMembers as $id) {
                $movedMember = User::find($id);
                $movedMember->immediateHead_id = $teamfrom->id;
                $movedMember->push();
               $members[] = $movedMember->firstname . " ". $movedMember->lastname;
            }
            $movements->push(['team'=> $teamfrom->name, 'members'=>$members]);

         }
        


        //dd($movedDashers);
        return $movements;
    }



    

    public function toTeam(Request $request)
    {
    	$memberID = $request->memberID;
    	$newTeam = $request->newTeam;

    	$member = User::find($memberID);
    	$team = ImmediateHead::find($newTeam);

    	$member->immediateHead_id = $team->id;
    	$member->push();

    	return $member;
    }

    public function edit($id)
    {
        // check first user permissions
        $coll = new Collection;
        $roles = UserType::find($this->user->userType_id)->roles->pluck('label'); //->where('label','MOVE_EMPLOYEES');
        $canEditMovement =  ($roles->contains('EDIT_MOVEMENT')) ? '1':'0';
        $canEditOtherMovements =  ($roles->contains('EDIT_OTHERS\'_MOVEMENT')) ? '1':'0';

        if (!$canEditMovement || !$canEditOtherMovements){
            return view('access-denied');

        } else
        {
                $movement = Movement::find($id);
                $movementTypes = PersonnelChange::all();
                $TLset = null;

                $personnel = User::find($movement->user_id);

                $floors = Floor::all();
                $hisFloor = Floor::find($personnel->team->floor_id);
               

                switch ($movement->personnelChange_id){

                    case 1: { 

                                if ($movement->withinProgram)
                                {
                                    $details = Movement_ImmediateHead::where('movement_id', $movement->id)->first(); 
                                    //$hisNew = ImmediateHead::find($details->imHeadCampID_new)->campaigns;
                                    $hisNew = Campaign::find(ImmediateHead_Campaign::find($details->imHeadCampID_new)->campaign_id);
                                    $hisNewIDvalue = $details->imHeadCampID_new;
                                    


                                    if (count($hisNew) > 1){
                                        $TLset = new Collection;
                                        $TLset1 = new Collection;

                                        foreach($hisNew as $h){
                                            
                                            $TLset->push(Campaign::find($h->id)->leaders);
                                        }

                                    } else {
                                        $TLset1 = Campaign::find($hisNew->id)->leaders;
                                        $TLset = $TLset1->filter(function($value, $key){ 
                                            return $value->lastname != "";
                                        });
                                       
                                    }

                                    $coll->push(['tlSet'=>$TLset]);

                                } else{

                                    $details = Movement_ImmediateHead::where('movement_id', $movement->id)->first(); 
                                    //$hisNew = ImmediateHead::find($details->imHeadCampID_new)->campaigns;
                                    $hisNew = Campaign::find(ImmediateHead_Campaign::find($details->imHeadCampID_new)->campaign_id);
                                    $hisOld = Campaign::find(ImmediateHead_Campaign::find($details->imHeadCampID_old)->campaign_id);
                                    

                                    $hisNewIDvalue = $details->imHeadCampID_new;
                                    $hisOldIDvalue = $details->imHeadCampID_old;

                                    if (count($hisNew) > 1){
                                        $TLset = new Collection;
                                        $TLset1 = new Collection;

                                        foreach($hisNew as $h){
                                            
                                            $TLset->push(Campaign::find($h->id)->leaders);
                                        }

                                    } else {
                                        $TLset1 = Campaign::find($hisNew->id)->leaders;
                                        $TLset = $TLset1->filter(function($value, $key){ 
                                            return $value->lastname != "";
                                        });
                                       
                                    }

                                    if (count($hisOld) > 1){
                                        $TLset = new Collection;
                                        $TLset1 = new Collection;

                                        foreach($hisOld as $h){
                                            
                                            $TLset->push(Campaign::find($h->id)->leaders);
                                        }

                                    } else {
                                        $TLset1 = Campaign::find($hisOld->id)->leaders;
                                        $TLset = $TLset1->filter(function($value, $key){ 
                                            return $value->lastname != "";
                                        });
                                       
                                    }

                                    $coll->push(['tlSet'=>$TLset]);
                                }
                                
                                
                                 //ImmediateHead::where('campaign_id',$hisNew->id)->orderBy('lastname', 'ASC')->get();

                            }break;
                    case 2: {
                                $details = Movement_Positions::where('movement_id', $movement->id)->first(); 
                                $hisNew = Position::find($details->position_id_new);
                                $hisNewIDvalue = $details->position_id_new;

                            }break;
                    case 3: {
                                $details = Movement_Status::where('movement_id', $movement->id)->first(); 
                                $hisNew = Status::find($details->status_id_new);
                                $hisNewIDvalue = $details->status_id_new;

                            }break;

                }


               
               

                    //$TLs = ImmediateHead::where('lastname','!=','')->orderBy('lastname','ASC')->get();
                    $TLs = ImmediateHead_Campaign::all();
                    
                    $hisCampaign = $personnel->campaign->first(); // User::find($movement->user_id)->campaign; 

                    $users = User::orderBy('lastname', 'ASC')->get();
                    
                    $statuses = Status::all();
                    $positions = Position::where('name','!=','')->orderBy('name','ASC')->get();
                    $hrDept = Campaign::where('name','HR')->first();

                    $hrs = Team::where('campaign_id', $hrDept->id)->get();
                    //$hrs = ImmediateHead::where('campaign_id', $hrDept->id)->get();


                    $campaigns = Campaign::where('id', '!=', $personnel->campaign_id)->where('name','!=','')->orderBy('name', 'ASC')->get();

                        $leaders1 = new Collection;

                        foreach ($TLs as $tl) {

                                    //$data = $tl->userData;// User::where('employeeNumber', $tl->employeeNumber)->first();
                                    $data = ImmediateHead::find($tl->immediateHead_id)->userData;

                                    if( !empty($data['firstname']) &&  !empty($data['lastname']) && $data['firstname'] !== " " && $data['lastname'] !== " " && ($data['status_id']!== 7 && $data['status_id']!== 8 && $data['status_id']!== 9) ) //to ensure no dummy DB entries
                                    {
                                        $hisPOsition = Position::where('id',$data['position_id'])->first();
                                                                
                                            $leaders1->push([
                                            'id'=>$tl->id,
                                            'position'=> $hisPOsition['name'], //Position::find($hisPOsition['position_id']), //->position,
                                            'lastname'=>  $data['lastname'], //$tl->lastname,
                                            'firstname'=> $data['firstname'], //$tl->firstname,
                                            'campaign'=> Campaign::find($tl->campaign_id)->name, // $tl->campaigns->first()->name,
                                            'campaign_id'=> $tl->campaign_id]); // $tl->campaigns->first()->id]);
                                        
                                       

                                    }
                                                            
                        }

                        $leaders = $leaders1->sortBy('lastname');



                    /*

                    $campaigns = Campaign::where('name','!=','')->where('id','!=',$hisCampaign->first()->id)->orderBy('name','ASC')->get();
                    
                    //$campaigns = Campaign::where('id', '!=', $personnel->campaign_id)->orderBy('name', 'ASC')->get();
                    $leaders = new Collection;
                    foreach ($TLs as $tl) {

                        //$data = $tl->userData;// User::where('employeeNumber', $tl->employeeNumber)->first();
                        $data = ImmediateHead::find($tl->immediateHead_id)->userData;
                        $hisPOsition = Position::where('id',$data['position_id'])->first();
                        

                        //check for multiple campaign handle
                        
                        // if (count($tl->campaigns) > 1) 
                        // {
                        //     foreach ($tl->campaigns as $t) {
                        //        $leaders->push([
                        //         'id'=>$tl->id,
                        //         'position'=>$hisPOsition['name'],
                        //         'lastname'=> $tl->lastname,
                        //         'firstname'=>$tl->firstname." - ". $t->name,
                        //         'campaign'=>$t->name ]);
                        //         }
                            

                        // } else
                        // {
                        //     $leaders->push([
                        //     'id'=>$tl->id,
                        //     'position'=> $hisPOsition['name'], //Position::find($hisPOsition['position_id']), //->position,
                        //     'lastname'=> $tl->lastname,
                        //     'firstname'=>$tl->firstname,
                        //     'campaign'=>$tl->campaigns->first()->name ]);

                        // }

                         $leaders->push([
                            'id'=>$tl->id,
                            'position'=> $hisPOsition['name'], //Position::find($hisPOsition['position_id']), //->position,
                            'lastname'=> $data['lastname'],
                            'firstname'=>$data['firstname'],
                            'campaign'=> Campaign::find($tl->campaign_id)->name ]); //$tl->campaigns->first()->name ]);


                        
                    }

                    */

                  //return $leaders->sortBy('lastname');

                    $hrPersonnels = new Collection;

                    foreach ($hrs as $tl) {
                        //$hisPOsition = User::where('employeeNumber', $tl->employeeNumber)->first();

                        $data = User::find($tl->user_id); // $tl->userData;// User::where('employeeNumber', $tl->employeeNumber)->first();


                        $hisPOsition = Position::where('id',$data['position_id'])->first();


                        //$hisPOsition = User::find($tl->user_id);
                        $hrPersonnels->push([
                            'id'=>$tl->user_id,
                            'position'=>$hisPOsition['name'],
                            'lastname'=> $data->lastname,
                            'firstname'=>$data->firstname,
                            'campaign'=>$data->campaign[0]->name ]); //[0]->name ]);
                    }

                        //return $leaders;
                    return $coll;
                        return view('people.changePersonnel-edit', compact('users','floors','hisFloor', 'movementTypes', 'leaders', 'TLset', 'hisNew', 'hisNewIDvalue',  'hrPersonnels', 'hisCampaign','personnel',  'campaigns', 'movement','statuses','positions'));


        }//end if else can Edit

        

    }

    public function approve($id)
    {
        $canApprove = UserType::find($this->user->userType_id)->roles->where('label','APPROVE_MOVEMENTS')->first();
        $coll = new Collection;
        if ($canApprove !== null)
        {
            $movement = Movement::find($id);
            $interCampaign = Input::get('interCampaign');

            if ($movement !== null){
                $movement->isApproved = true;
                

                switch ($movement->personnelChange_id) {
                    case 1: { //immediateHead
                                if ($interCampaign == "false") //update employee team details and do notifications
                                    {

                                        $moveHead = Movement_ImmediateHead::where('movement_id',$movement->id)->first();

                                        if (  date("Y-m-d", strtotime($movement->effectivity)) <= date("Y-m-d") ) //if effectivity is past or today
                                                    {

                                                        //get the Team table
                                                        $employee = User::find($movement->user_id);
                                                        $updateTeam = $employee->team;
                                                        $updateTeam->immediateHead_Campaigns_id = $moveHead->imHeadCampID_new;
                                                        $updateTeam->floor_id = $moveHead->newFloor;
                                                        $updateTeam->campaign_id = ImmediateHead_Campaign::find($moveHead->imHeadCampID_new)->campaign_id;
                                                        $updateTeam->push();


                                                        $movement->isDone = true;
                                                    }                

                                                $movement->push();
                                                $newTL = ImmediateHead::find(ImmediateHead_Campaign::find($moveHead->imHeadCampID_new)->immediateHead_id);

                                                // ***** look for NOTIFICATION for TL

                                                //$notification = Notification::where('relatedModelID',$movement->id)->where('type',2)->where('from',$movement->requestedBy)->first();
                                               // ***** add in NOTIFICATION for TL

                                                $notification = new Notification;
                                                $notification->relatedModelID = $movement->id;
                                                $notification->type = 2;
                                                $notification->from = $movement->requestedBy;
                                                $notification->save();

                                             

                                                $nu = new User_Notification;
                                                $nu->user_id = $newTL->userData->id;
                                                $nu->notification_id = $notification->id;
                                                $nu->seen = false;
                                                $nu->save();


                                                // NOW, EMAIL THE TL CONCERNED 1
                                               
                                                 Mail::send('emails.personnelChange', ['user' => $newTL, 'employee'=>$employee, 'movement'=>$movement, 'notification'=>$notification], function ($m) use ($newTL) 
                                                 {
                                                    $m->from('OES@openaccessbpo.net', 'OES-OAMPI Evaluation System');
                                                    $m->to($newTL->userData->email, $newTL->lastname)->subject('Personnel Change Notice');                           
                                                

                                                }); //end mail
                                                



                                                // ***** add in NOTIFICATION for employee involved

                                                $notification2 = new Notification;
                                                $notification2->relatedModelID = $movement->id;
                                                $notification2->type = 2;
                                                $notification2->from = $movement->requestedBy;
                                                $notification2->save();

                                                $nu = new User_Notification;
                                                $nu->user_id = $movement->user_id;
                                                $nu->notification_id = $notification2->id;
                                                $nu->seen = false;
                                                $nu->save();

                                    } //end if not intercampaign



                            break; 
                            }

                            //change position
                    case 2: { 
                                    $notification = new Notification;
                                    $notification->relatedModelID = $movement->id;
                                    $notification->type = 3;
                                    $notification->from = $movement->notedBy;
                                    $notification->save();

                                    $moveHead = Movement_Positions::where('movement_id',$movement->id)->first();
                                     $employee = User::find($movement->user_id);
                                    
                                     
                                     $employee->position_id = $moveHead->position_id_new;

                                     $employee->push();
                                                 
                                                
                                                //inform person concerned
                                                $nu = new User_Notification;
                                                $nu->user_id = $movement->user_id;
                                                $nu->notification_id = $notification->id;
                                                $nu->seen = false;
                                                $nu->save();

                                                //notify TL involved

                                                $nu = new User_Notification;
                                                $nu->user_id = $movement->requestedBy;
                                                $nu->notification_id = $notification->id;
                                                $nu->seen = false;
                                                $nu->save();

                                               


                                    $movement->isDone=true;
                                    $movement->push();


                                    $coll->push(['movehead'=>$moveHead->position_id_new, 'empNew'=>$employee->position_id]);

                                    break;
                            }

                    //change status
                    case 3: { 

                                    $notification = new Notification;
                                    $notification->relatedModelID = $movement->id;
                                    $notification->type = 4;
                                    $notification->from = $movement->notedBy;
                                    $notification->save();

                                    $moveHead = Movement_Status::where('movement_id',$movement->id)->first();
                                     $employee = User::find($movement->user_id);
                                     $employee->dateRegularized = $movement->effectivity;
                                     $employee->status_id = $moveHead->status_id_new;
                                     $employee->push();
                                                 
                                                
                                                //inform person concerned
                                                $nu = new User_Notification;
                                                $nu->user_id = $movement->user_id;
                                                $nu->notification_id = $notification->id;
                                                $nu->seen = false;
                                                $nu->save();

                                                //notify TL involved

                                                $nu = new User_Notification;
                                                $nu->user_id = $movement->requestedBy;
                                                $nu->notification_id = $notification->id;
                                                $nu->seen = false;
                                                $nu->save();

                                               


                                    $movement->isDone=true;
                                    $movement->push();
                                                



                        break;}
                }//end switch



                return response()->json($coll);
               // return response()->json($employee);

            } else return response()->json(['message'=>"An error occured. Movement does not exist. Please try again."]);
            

            
        } else return view('access-denied');
        

    }


    public function noted($id)
    {
        
            $movement = Movement::find($id);

            if ($movement !== null){

                switch (Input::get('type')) {
                    case 'isNotedFrom':
                        $movement->isNotedFrom = true;
                        break;
                    
                    case 'isNotedTo':
                        $movement->isNotedTo = true;
                        break;

                    case 'interCampaign':
                        $movement->isApproved = true;
                        
                        break;
                }
                
                $movement->push();
                return response()->json($movement);

           }  else return response()->json(['message'=>"An error occured. Movement does not exist. Please try again."]);
        

    }

   


    public function show($id)
    {
        $movement = Movement::find($id);

        if ( empty($movement)) return view('empty');

        //check first if agent lang, then you cant see movement of others
        if ($this->user->userType_id == 4 && ($movement->user_id !== $this->user->id)) return view('access-denied');

      

        $canAttachSignatures = UserType::find($this->user->userType_id)->roles->where('label','APPROVE_MOVEMENTS')->first();

        



        //check if this movement has been approved, then display all necessary signatures
        if($movement->isApproved)
        {
                $opsMgr = User::where('lastname','Chang')->where('firstname','Michael')->first();
                $notedBy = User::find($movement->notedBy);

                if ( file_exists('public/img/employees/'.$opsMgr->id.'-sign.png') )
                 {
                    $signatureOpsMgr = asset('public/img/employees/'.$opsMgr->id.'-sign.png');
                 } else {
                    $signatureOpsMgr = asset('public/img/employees/signature.png');
                 }

                 if ( file_exists('public/img/employees/'.$notedBy->id.'-sign.png') )
                 {
                    $signatureHR = asset('public/img/employees/'.$notedBy->id.'-sign.png');
                 } else {
                    $signatureHR = asset('public/img/employees/signature.png');
                 }

            
        } else {
            //return $movement;
            $signatureOpsMgr=null; $signatureHR = null;
        }


        $requestedBy = ImmediateHead::find($movement->requestedBy);

        //$requestedBy = ImmediateHead::find(ImmediateHead_Campaign::find($movement->requestedBy)->immediateHead_id);

                   
        

        if ($movement->isNotedFrom)
        {

            if ( file_exists('public/img/employees/'.$requestedBy->userData->id.'-sign.png') )
                 {
                    $signatureRequestedBy = asset('public/img/employees/'.$requestedBy->userData->id.'-sign.png');
                 } else {
                    $signatureRequestedBy = asset('public/img/employees/signature.png');
                 }

           
        }else 
        { 
            $signatureRequestedBy = null;           
            
            

        }
        //now check if user is the requestor
            if ($this->user->employeeNumber == $requestedBy->userData->employeeNumber) $isTheRequestor=true;
            else $isTheRequestor=false;


        $requestedTo = User::find($movement->user_id);


        if ($movement->isNotedTo)
        {
            

            if ( file_exists('public/img/employees/'.$requestedTo->id.'-sign.png') )
                 {
                    $signatureRequestedTo = asset('public/img/employees/'.$requestedTo->id.'-sign.png');
                 } else {
                    $signatureRequestedTo = asset('public/img/employees/signature.png');
                 }

           
        } else {
            $signatureRequestedTo = null;             

        }
        //now check if user is the employee concerned
            if ($this->user->employeeNumber == $requestedTo->employeeNumber) $noteTo = true;
            else $noteTo = false;


        

        if (Input::get('seen')){
            //$markSeen = User_Notification::where('notification_id',Input::get('notif'))->where('user_id',$this->user->id)->first();
            $mseen = User_Notification::where('notification_id',Input::get('notif'))->where('user_id',$this->user->id)->get();

            if (count($mseen) > 0)
            {
                $markSeen = $mseen->first();
                $markSeen->seen = true;
                $markSeen->push();

            } 
            

        }

       

        $personnel = User::find($movement->user_id);
        $hisFloor = Floor::find($personnel->team->floor_id);
        //$requestedBy = ImmediateHead::find(ImmediateHead_Campaign::find($movement->requestedBy)->immediateHead_id);
        $hrPersonnel = User::find($movement->notedBy);
        $transferredToMe = false;


         switch ($movement->personnelChange_id){

                    case 1: { 
                                $movementdetails = Movement_ImmediateHead::where('movement_id', $movement->id)->first(); 
                                
                               
                                $hisNewIDvalue = ImmediateHead::find(ImmediateHead_Campaign::find($movementdetails->imHeadCampID_new)->immediateHead_id);
                                $hisNew = $hisNewIDvalue->campaigns;



                                // $oldCampaign = ImmediateHead_Campaign::find($movementdetails->imHeadCampID_old)->campaign; //Campaign::find(ImmediateHead_Campaign::find($movementdetails->imHeadCampID_old)->campaign_id);
                                // $newCampaign = ImmediateHead_Campaign::find($movementdetails->imHeadCampID_new)->campaign; //Campaign::find(ImmediateHead_Campaign::find($movementdetails->imHeadCampID_new)->campaign_id);
                                
                                $oldCampaign = Campaign::find(ImmediateHead_Campaign::find($movementdetails->imHeadCampID_old)->campaign_id);
                                $newCampaign = Campaign::find(ImmediateHead_Campaign::find($movementdetails->imHeadCampID_new)->campaign_id);




                                if (count($hisNew) > 1){
                                    $TLset = new Collection;

                                    foreach($hisNew as $h){
                                        
                                        $TLset->push(Campaign::find($h->id)->leaders);
                                    }

                                } else {
                                    $TLset = Campaign::find($hisNew[0]->id)->leaders;
                                }

                                // now introduce another check: If ikaw ung TL na paglilipatan, no need to attach signatures

                                //return $hisNewIDvalue;

                                if ($this->user->employeeNumber == $hisNewIDvalue->employeeNumber)
                                      $transferredToMe = true;
                                else  $transferredToMe = false;


                                // --- another check: if interCampaign movement or not
                                // --- acknowledge if yes
                                // --- approve if not
                                if ($oldCampaign->id !== $newCampaign->id) $interCampaign = false; else $interCampaign= true;


                                
                                 //ImmediateHead::where('campaign_id',$hisNew->id)->orderBy('lastname', 'ASC')->get();
                                 return view('people.movement-show',compact('interCampaign','canAttachSignatures','oldCampaign','newCampaign', 'transferredToMe', 'noteTo','isTheRequestor', 'signatureOpsMgr', 'signatureHR', 'signatureRequestedTo', 'signatureRequestedBy', 'personnel','movement', 'movementdetails', 'hisNew','hisFloor', 'hisNewIDvalue', 'requestedBy', 'hrPersonnel'));


                            }break;
                    case 2: {
                                $movementdetails = Movement_Positions::where('movement_id', $movement->id)->first(); 
                                $hisNew = Position::find($movementdetails->position_id_new);
                                $hisOld = Position::find($movementdetails->position_id_old);
                                $hisNewIDvalue = $movementdetails->position_id_new;
                                $interCampaign=false;
                                
                                return view('people.movement-show',compact('interCampaign','canAttachSignatures','oldCampaign','newCampaign', 'transferredToMe', 'noteTo','isTheRequestor', 'signatureOpsMgr', 'signatureHR', 'signatureRequestedTo', 'signatureRequestedBy', 'personnel','movement', 'movementdetails', 'hisNew','hisOld', 'hisFloor', 'hisNewIDvalue', 'requestedBy', 'hrPersonnel'));


                            }break;
                    case 3: {
                                $movementdetails = Movement_Status::where('movement_id', $movement->id)->first(); 
                                $hisNew = Status::find($movementdetails->status_id_new);
                                $hisOld = Status::find($movementdetails->status_id_old);
                                $hisNewIDvalue = $movementdetails->status_id_new;
                                $interCampaign=true;
                                return view('people.movement-show',compact('interCampaign','canAttachSignatures','oldCampaign','newCampaign', 'transferredToMe', 'noteTo','isTheRequestor', 'signatureOpsMgr', 'signatureHR', 'signatureRequestedTo', 'signatureRequestedBy', 'personnel','movement', 'movementdetails', 'hisNew','hisOld', 'hisFloor', 'hisNewIDvalue', 'requestedBy', 'hrPersonnel'));


                            }break;

                }

        //$coll = new Collection;
       // $coll->push(['movementdetails'=>$movementdetails, 'oldCampaign'=>$oldCampaign, 'newCampaign'=>$newCampaign, 'canAttachSignatures'=>$canAttachSignatures, 'transferredToMe'=>$transferredToMe,'isApproved'=> $movement->isApproved, 'interCampaign'=>$interCampaign ]);
        
        
       
    }

    public function printPDF($id)
    {
        $movement = Movement::find($id);
        $employee = User::find($movement->user_id);
        $hisFloor = Floor::find($employee->team->floor_id);
        $reason = PersonnelChange::find($movement->personnelChange_id)->name;
        $requestedBy = ImmediateHead::find($movement->requestedBy);
        $hrPersonnel = User::find($movement->notedBy);

        //check if this movement has been approved, then display all necessary signatures
        if($movement->isApproved)
        {
                $opsMgr = User::where('lastname','Chang')->where('firstname','Michael')->first();
                $notedBy = User::find($movement->notedBy);

                if ( file_exists('public/img/employees/'.$opsMgr->id.'-sign.png') )
                 {
                    $signatureOpsMgr = asset('public/img/employees/'.$opsMgr->id.'-sign.png');
                 } else {
                    $signatureOpsMgr = asset('public/img/employees/signature.png');
                 }

                 if ( file_exists('public/img/employees/'.$notedBy->id.'-sign.png') )
                 {
                    $signatureHR = asset('public/img/employees/'.$notedBy->id.'-sign.png');
                 } else {
                    $signatureHR = asset('public/img/employees/signature.png');
                 }

            
        } else {
            //return $movement;
            $signatureOpsMgr=null; $signatureHR = null;
        }


        $requestedBy = ImmediateHead::find($movement->requestedBy);
        

        if ($movement->isNotedFrom)
        {

            if ( file_exists('public/img/employees/'.$requestedBy->userData->id.'-sign.png') )
                 {
                    $signatureRequestedBy = asset('public/img/employees/'.$requestedBy->userData->id.'-sign.png');
                 } else {
                    $signatureRequestedBy = asset('public/img/employees/signature.png');
                 }

           
        }else 
        { 
            $signatureRequestedBy = null;           
            
            

        }

        $requestedTo = User::find($movement->user_id);


        if ($movement->isNotedTo)
        {
            

            if ( file_exists('public/img/employees/'.$requestedTo->id.'-sign.png') )
                 {
                    $signatureRequestedTo = asset('public/img/employees/'.$requestedTo->id.'-sign.png');
                 } else {
                    $signatureRequestedTo = asset('public/img/employees/signature.png');
                 }

           
        } else {
            $signatureRequestedTo = null;             

        }

        switch ($movement->personnelChange_id){

                    case 1: { 
                                $movementdetails = Movement_ImmediateHead::where('movement_id', $movement->id)->first(); 
                                
                               
                                // $hisNewIDvalue = ImmediateHead::find($movementdetails->imHeadCampID_new);
                                // $hisNew = $hisNewIDvalue->campaigns;

                                $hisNewIDvalue = ImmediateHead::find(ImmediateHead_Campaign::find($movementdetails->imHeadCampID_new)->immediateHead_id);
                                $hisNew = $hisNewIDvalue->campaigns;

                                $oldCampaign = Campaign::find(ImmediateHead_Campaign::find($movementdetails->imHeadCampID_old)->campaign_id);
                                $newCampaign = Campaign::find(ImmediateHead_Campaign::find($movementdetails->imHeadCampID_new)->campaign_id);



                                // --- another check: if interCampaign movement or not
                                // --- acknowledge if yes
                                // --- approve if not
                                if ($oldCampaign->id !== $newCampaign->id) $interCampaign = false; else $interCampaign= true;


                                
                                 //ImmediateHead::where('campaign_id',$hisNew->id)->orderBy('lastname', 'ASC')->get();

                            }break;
                    case 2: {
                                $movementdetails = Movement_Positions::where('movement_id', $movement->id)->first(); 
                                $hisNew = Position::find($movementdetails->position_id_new);
                                $hisOld = Position::find($movementdetails->position_id_old);
                                $hisNewIDvalue = $movementdetails->position_id_new;
                                //return view('people.movement-show',compact('interCampaign','canAttachSignatures','oldCampaign','newCampaign', 'transferredToMe', 'noteTo','isTheRequestor', 'signatureOpsMgr', 'signatureHR', 'signatureRequestedTo', 'signatureRequestedBy', 'personnel','movement', 'movementdetails', 'hisNew','hisOld', 'hisFloor', 'hisNewIDvalue', 'requestedBy', 'hrPersonnel'));


                            }break;
                    case 3: {
                                $movementdetails = Movement_Status::where('movement_id', $movement->id)->first(); 
                                $hisNew = Status::find($movementdetails->status_id_new);
                                $hisOld = Status::find($movementdetails->status_id_old);
                                $hisNewIDvalue = $movementdetails->status_id_new;
                                $interCampaign=true;
                                //return view('people.movement-show',compact('interCampaign','canAttachSignatures','oldCampaign','newCampaign', 'transferredToMe', 'noteTo','isTheRequestor', 'signatureOpsMgr', 'signatureHR', 'signatureRequestedTo', 'signatureRequestedBy', 'personnel','movement', 'movementdetails', 'hisNew','hisOld', 'hisFloor', 'hisNewIDvalue', 'requestedBy', 'hrPersonnel'));


                            }break;

                }

        $pdf = PDF::loadView('evaluation.pdf-movement', compact('oldCampaign','newCampaign', 'hrPersonnel', 'movement', 'employee','reason','requestedBy', 'movementdetails', 'signatureOpsMgr', 'signatureHR', 'signatureRequestedTo', 'signatureRequestedBy', 'personnel','movement', 'movementdetails', 'hisNew','hisOld', 'hisFloor', 'hisNewIDvalue') );
        return $pdf->stream('movement_'.$employee->lastname."_".$employee->firstname.'.pdf');

    }

    public function destroy($id)
    {
        $this->movement->destroy($id);
        return back();

    }

    public function update(Request $request)
    {

       


        $movement = Movement::find($request->id);


        //$movement->new_id = $request->new_id;
        $movement->withinProgram = $request->withinProgram;
        $movement->effectivity = date("Y-m-d", strtotime($request->effectivity));
        $movement->isApproved = $request->isApproved;
        $movement->requestedBy = ImmediateHead_Campaign::find($request->requestedBy)->immediateHead_id;
        $movement->dateRequested = date("Y-m-d", strtotime($request->dateRequested));
        $movement->notedBy = $request->notedBy;


       
        
        //return response()->json($new_id) ;
        
        


        // *** once you save a movement, determine what type it is and then save corresponding relational tables
        switch ($movement->personnelChange_id) {
            case 1: { //immediateHead

                // generate MOvement_ImmediateHead from campaign and head id
                //$new_id = ImmediateHead_Campaign::where('campaign_id',$request->campaign_id)->where('immediateHead_id',$request->new_id)->first()->id;
                $new_id = $request->new_id;
                //get first id of old head
                $moveHead = Movement_ImmediateHead::where('movement_id',$movement->id)->first();
               // $old_id = $moveHead->imHeadCampID_old;


                $movement->push();

               
                
                        
                        //$moveHead->imHeadCampID_old = $old_id;
                        $moveHead->imHeadCampID_new = $new_id;
                        //$moveHead->imHeadCampID_new = $request->new_id;
                        $moveHead->newFloor = $request->newFloor;
                        //$moveHead->oldFloor = $request->oldFloor;
                        $moveHead->push();
                        

                        // -------- after creating movement, update employee profile
                        // -------- * check mo kung sakop ng effectivity date ung current date



                        if (  $movement->effectivity <= date("Y-m-d") ) //if effectivity is past or today
                        {

                            //get the Team table
                            $employee = User::find($movement->user_id);
                            $updateTeam = $employee->team;

                            
                            $updateTeam->immediateHead_Campaigns_id = $new_id;
                            $updateTeam->floor_id = $request->newFloor;
                            $updateTeam->campaign_id = $request->campaign_id;
                            $updateTeam->push();


                            $movement->isDone = true;
                            $movement->push();

                            /* -- this is now changed to Team relational DB
                            $newHead = ImmediateHead::find($new_id);

                            

                            $toUpdate = Team::where('user_id', $employee->id)->where('campaign_id')

                            $employee->campaign_id = $newHead->campaign_id;
                            $employee->immediateHead_id = $newHead->id; 
                            $employee->push();
                            */

                            
                            
                            

                        } else $movement->isDone = false;

                       $moveHead->save();
                       
                        
                        break; }
                        
            case 2: { 
                        $moveHead = Movement_Positions::where('movement_id',$movement->id)->first();

                        if($new_id !== $moveHead->position_id_new)
                        {
                            $moveHead->position_id_new = $new_id;
                            $moveHead->push();
                        }
                       
                        
                        break; }
            case 3: {   $moveHead = Movement_Status::where('movement_id',$movement->id)->first();
                        
                        if($request->new_id !== $moveHead->status_id_new)
                        {
                            $moveHead->status_id_new = $request->new_id;
                            $moveHead->push();
                        }
                        
                        // -------- after creating movement, update employee profile
                        // -------- * check mo kung sakop ng effectivity date ung current date



                        if (  $movement->effectivity <= date("Y-m-d") ) //if effectivity is past or today
                        {

                            //get the Team table
                            $employee = User::find($movement->user_id);
                            $employee->status_id = $request->new_id;
                            $employee->push();


                            $movement->isDone = true;
                            $movement->push();
                     
                            

                        } else $movement->isDone = false;

                        
                        break; }
        }


        return response()->json($moveHead) ;
                       

    }
}               

