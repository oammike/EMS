<?php

namespace OAMPI_Eval\Http\Controllers;

use Carbon\Carbon;
use Excel;
use \PDF;
use \Mail;
use \App;
use \DB;
use \Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;

use Illuminate\Http\Request;

use Yajra\Datatables\Facades\Datatables;

use OAMPI_Eval\Http\Requests;
use OAMPI_Eval\User;
use OAMPI_Eval\User_DTRPinfo;
use OAMPI_Eval\User_DTRPreport;
use OAMPI_Eval\User_LogOverride;
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
use OAMPI_Eval\Restday;
use OAMPI_Eval\Cutoff;
use OAMPI_Eval\Biometrics;
use OAMPI_Eval\Biometrics_Uploader;
use OAMPI_Eval\Logs;
use OAMPI_Eval\LogType;
use OAMPI_Eval\TempUpload;
use OAMPI_Eval\User_DTR;
use OAMPI_Eval\MonthlySchedules;
use OAMPI_Eval\FixedSchedules;
use OAMPI_Eval\Paycutoff;
use OAMPI_Eval\User_CWS;
use OAMPI_Eval\User_DTRP;
use OAMPI_Eval\Notification;
use OAMPI_Eval\User_Notification;

class UserDTRPController extends Controller
{
    protected $user;
    protected $user_dtrp;
    use Traits\TimekeepingTraits;
    use Traits\UserTraits;



     public function __construct(User_DTRP $user_dtrp)
    {
        $this->middleware('auth');
        $this->user_dtrp = $user_dtrp;
        $this->user =  User::find(Auth::user()->id);
    }

    public function deleteThisDTRP($id,Request $request)
    {
        $theDTRP = User_DTRP::find($id); $stamp = Carbon::now('GMT+8');
        $owner = User::find($theDTRP->user_id);
        if (is_null($theDTRP)) return view('empty');

        $deletedID = $theDTRP->id;

        //**** send notification to the sender
        $theNotif = Notification::where('relatedModelID', $theDTRP->id)->where('type',$request->notifType)->get();

        //then remove those sent notifs to the approvers since it has already been approved/denied
        if (count($theNotif) > 0)
            DB::table('user_Notification')->where('notification_id','=',$theNotif->first()->id)->delete();

        $file = fopen('storage/uploads/dtrplogs.txt', 'a') or die("Unable to open logs");
                fwrite($file, "-------------------\n Delete DTRP of [".$owner->id."] on ".$stamp->format('Y-m-d H:i')." by [". $this->user->id."] ".$this->user->lastname."\n");
                fclose($file);
        



        $theDTRP->delete();
        if ($request->redirect == '1')
            return redirect()->back();
        else return response()->json(['success'=>"ok"]);


        
    }

     public function manage()
    {
      $roles = UserType::find($this->user->userType_id)->roles->pluck('label'); 
      (Input::get('from')) ? $from = Input::get('from') : $from = Carbon::now()->addDays(-7)->format('m/d/Y'); 
      (Input::get('to')) ? $to = Input::get('to') : $to = Carbon::now()->endOfMonth()->addDays(14)->format('m/d/Y'); //date('m/d/Y');

      (Input::get('type')) ? $type = Input::get('type') : $type = 'IN';
      $stamp = Carbon::now('GMT+8');

      /*$isAdmin =  ($roles->contains('ADMIN_LEAVE_MANAGEMENT')) ? '1':'0';
      $canCredit =  ($roles->contains('UPDATE_LEAVES')) ? '1':'0';*/
      $canManage =  ($roles->contains('UPLOAD_BIOMETRICS')) ? '1':'0';

      if(!$canManage){
        $file = fopen('storage/uploads/dtrplogs.txt', 'a') or die("Unable to open logs");
                fwrite($file, "-------------------\n Tried DTRP MGT on ".$stamp->format('Y-m-d H:i')." by [". $this->user->id."] ".$this->user->lastname."\n");
                fclose($file);return view('access-denied');
      } 

      $allDTRP = $this->getDTRPs($from,$to,$type);
      
      $allOld = $this->getDTRPs($from,$to,"OLD");

      if($type=="IN") { 
            $allPendings = count(collect($allDTRP)->where('validated',null)); 
            $allDTRPOut = $this->getDTRPs($from,$to,"OUT");
            $allIns = count(collect($allDTRP)->where('validated',null));
            $allOuts = count(collect($allDTRPOut)->where('validated',null));
       }
      else if($type=="OUT"){ 
            //$allPendings = count(collect($allDTRP)->where('isApproved',null)); 
            $allPendings = count(collect($allDTRP)->where('validated',null)); 
            $allDTRPIn = $this->getDTRPs($from,$to,"IN"); 
            $allDTRPOut = $this->getDTRPs($from,$to,"OUT"); 
            $allIns = count(collect($allDTRPIn)->where('validated',null));
            $allOuts =count(collect($allDTRP)->where('validated',null)); 
        }
       else
        {
            $allPendings = count(collect($allDTRP)->where('isApproved',null)); 
            $allIns = count(collect($this->getDTRPs($from,$to,"IN"))->where('validated',null));
            $allOuts =count(collect($this->getDTRPs($from,$to,"OUT"))->where('validated',null));

        }
      
      
      
      $allOlds = count(collect($allOld)->where('isApproved',null));

      //return response()->json(['type'=>$type,'allDTRP'=>$allDTRP, 'allDTRPout'=>$allDTRPOut]);

      switch ($type) {
        case 'IN':{  $label = "DTRP IN" ;  $deleteLink = url('/')."/user_dtrp/deleteThisDTRP/"; $notifType = 8; } break;
        case 'OUT':{  $label = "DTRP OUT" ;  $deleteLink = url('/')."/user_dtrp/deleteThisDTRP/"; $notifType = 9; } break;
        case 'OLD':{  $label = "OLD DTRP PROCESS" ;  $deleteLink = url('/')."/user_dtrp/deleteThisDTRP/"; $notifType = 10; } break;
        default: { $label = "DTRP IN";   $deleteLink = url('/')."/user_dtrp/deleteThisDTRP/"; $notifType = 8;} break;
      }

      $storageLoc = url('/') . '/storage/uploads';

      //return $allPendings;
      //return response()->json(['type'=>$type, 'from'=>$from,'to'=>$to,'allDTRP'=>$allDTRP]);

      if($this->user->id !== 564 ) {
              $file = fopen('storage/uploads/dtrplogs.txt', 'a') or die("Unable to open logs");
                fwrite($file, "-------------------\n DTRPMGT on ".$stamp->format('Y-m-d H:i')." by [". $this->user->id."] ".$this->user->lastname."\n");
                fclose($file);
            }


            $leaders = DB::table('immediateHead_Campaigns')->
                join('immediateHead','immediateHead_Campaigns.immediateHead_id','=','immediateHead.id')->
                join('campaign','immediateHead_Campaigns.campaign_id','=','campaign.id')->
                leftJoin('users','immediateHead.employeeNumber','=', 'users.employeeNumber')->
                join('positions','positions.id','=','users.position_id')->
                select('immediateHead_Campaigns.id','positions.name as position','users.lastname','users.firstname','campaign.name as program','campaign.id as campaign_id','immediateHead_Campaigns.disabled')->
                orderBy('users.lastname','ASC')->
                where('users.status_id','!=',7)->
                where('users.status_id','!=',8)->
                where('users.status_id','!=',9)->
                where('immediateHead_Campaigns.disabled',null)->
                get(); 
                          

      return view('timekeeping.dtrpMgt',compact('canManage', 'from','to','type','label', 'deleteLink','notifType','allDTRP','allPendings','allIns', 'allOuts','allOlds','storageLoc','leaders'));

    }

    public function newDTRP()
    {
        $u = Input::get('u'); $isSigned=false;
        $p = Input::get('p');
        $a = Input::get('a');

        ($p) ? $productionDate=$p : $productionDate= Carbon::now('GMT+8')->format('Y-m-d');
        ($a) ? $actualDate=$a : $actualDate= Carbon::now('GMT+8')->format('Y-m-d');


        ($u) ?  $user = User::find(Input::get('u')) : $user = $this->user ;

        $roles = UserType::find($this->user->userType_id)->roles->pluck('label'); //->where('label','MOVE_EMPLOYEES');
        /* -------- get this user's department. If Backoffice, WFM can't access this ------*/
        $isBackoffice = ( Campaign::find(Team::where('user_id',$user->id)->first()->campaign_id)->isBackoffice ) ? true : false;
        $isWorkforce =  ($roles->contains('STAFFING_MANAGEMENT')) ? '1':'0';

        // $advent = Team::where('user_id',$user->id)->where('campaign_id',58)->get();
        // (count($advent) > 0) ? $isAdvent = 1 : $isAdvent=0;

        // $davao = Team::where('user_id',$user->id)->where('floor_id',9)->get();
        // (count($davao) > 0) ? $isDavao = 1 : $isDavao=0;

        // $ndy = Team::where('user_id',$user->id)->where('campaign_id',54)->get();
        // (count($ndy) > 0) ? $isNDY = 1 : $isNDY=0;


        //check mo kung leave for himself or if for others and approver sya
        $approvers = $user->approvers;
        //Timekeeping Trait
        $anApprover = $this->checkIfAnApprover($approvers, $this->user);

        if(!is_null($u) && !$anApprover && !$isWorkforce ) return view('access-denied'); //  || ($isWorkforce && $isBackoffice)



        $dtrpCategories = DB::table('user_dtrpReasons')->leftJoin('user_dtrpCategory','user_dtrpCategory.id','=','user_dtrpReasons.category_id')->
                                leftJoin('user_dtrpSubcategory','user_dtrpReasons.subcat_id','=','user_dtrpSubcategory.id')->
                                select('user_dtrpCategory.label as category','user_dtrpCategory.description','user_dtrpSubcategory.label as subCat','user_dtrpReasons.id','user_dtrpSubcategory.id as subcatID','user_dtrpCategory.id as catID', 'user_dtrpSubcategory.ordering','user_dtrpSubcategory.message as warning','user_dtrpReasons.name as reason','user_dtrpReasons.flag_HR','user_dtrpReasons.flag_DA')->get();
        $allCat = collect($dtrpCategories)->groupBy('category');
        $allSubcat = collect($dtrpCategories)->groupBy('subcatID');
        
        $correct=Carbon::now('GMT+7');

        $file = fopen('storage/uploads/dtrplogs.txt', 'a') or die("Unable to open logs");
        
        fwrite($file, "-------------------\n New_DTRP on ". $correct->format('M d h:i:s'). " by [". $this->user->id."], ".$this->user->lastname."\n");
                fclose($file);

        return view('timekeeping.new_DTRP', compact('user','isSigned','allCat','allSubcat','dtrpCategories','productionDate','actualDate'));

    }

    public function newDTRP_process(Request $request)
    {
        $attachments = $request->attachments;
        $userID = $request->userid;
        $productionDate = Carbon::parse($request->productionDate,'Asia/Manila');
        $actualDate = $request->actualDate;
        $log = Carbon::parse($actualDate.' '.$request->hour.":".$request->minute." ".$request->ampm,'Asia/Manila');
        $notes = $request->notes;
        $reason = $request->reason;
        $correct = Carbon::now('GMT+8');



        $b = Biometrics::where('productionDate',$productionDate->format('Y-m-d'))->get();
        $u = User::find($userID);
        $approvers = $u->approvers;
        //Timekeeping Trait
        $anApprover = $this->checkIfAnApprover($approvers, $this->user);
       

        //save the DTRP
        $dtrp = new User_DTRP;
        $dtrp->user_id = $userID;
        $dtrp->biometrics_id = $b->first()->id;
        $dtrp->actualLogdate = $log->format('Y-m-d');
        $dtrp->logTime = $log->format('H:i:s');
        $dtrp->logType_id = $request->logType;
        $dtrp->notes = $notes;
        ($anApprover) ? $dtrp->isApproved =1 : $dtrp->isApproved = null;
        $dtrp->approvedBy = $this->getTLapprover($u->id, $this->user->id);
        $dtrp->created_at = $correct->format('Y-m-d H:i:s');
        $dtrp->updated_at = $correct->format('Y-m-d H:i:s');
        $dtrp->save();

        
        if(empty($attachments) || $attachments=="null") $fileName=null;
        else
        {
            $destinationPath = storage_path() . '/uploads';
            $extension = Input::file('attachments')->getClientOriginalExtension(); // getting image extension
            $fileName = $correct->format('Y-m-d_H_i_s').'-user-'.$request->userid.'_DTRP_'.$request->logType.'.'.$extension; // renameing image
            $attachments->move($destinationPath, $fileName); // uploading file to given path

        }
        
      
        $dtrpInfo = new User_DTRPinfo;
        $dtrpInfo->dtrp_id = $dtrp->id;
        $dtrpInfo->reasonID = $reason;
        $dtrpInfo->attachments = $fileName;
        $dtrpInfo->clearedBy = $this->user->id;
        $dtrpInfo->created_at = $correct->format('Y-m-d H:i:s');
        $dtrpInfo->updated_at = $correct->format('Y-m-d H:i:s');
        $dtrpInfo->save();

        if($dtrp->logType_id == 1) $notifType=8;
        else if($dtrp->logType_id == 2) $notifType=9;

        //***** notifications
        $notif = new Notification;
        $notif->relatedModelID = $dtrp->id;
        $notif->type = $notifType;
        $notif->from = $userID;
        $notif->save();

        foreach ($approvers as $key) {
              $TL = ImmediateHead::find($key->immediateHead_id)->userData;
              //$coll->push(['dtrp'=>$dtrp, 'tl'=>$TL]);
              $tlNotif = new User_Notification;
              $tlNotif->user_id = $TL->id;
              $tlNotif->notification_id = $notif->id;
              $tlNotif->seen = false;
              $tlNotif->save();

               # code...
         }


        $file = fopen('storage/uploads/dtrplogs.txt', 'a') or die("Unable to open logs");
        fwrite($file, "-------------------\n DTRP_".$request->logType." for [".$u->id."] on ". $correct->format('Y M d h:i A'). " by [". $this->user->id."] ".$this->user->lastname."\n");
        fclose($file);
        


        return response()->json(['success'=>1,'dtrp'=>$dtrp,'dtrpInfo'=>$dtrpInfo]);
          

    }

    public function newDTRP_validate(Request $request)
    {
        //*** we need to check first if OLD Processs or not
        //    if old process, make manual overrides and that's it


            $dtrpInfo = User_DTRPinfo::find($request->infoID);
            $dtrpInfo->isCleared = $request->isApproved;
            $dtrpInfo->clearedBy = $this->user->id;
            $dtrpInfo->push();
            $correct=Carbon::now('GMT+7');

            $file = fopen('storage/uploads/dtrplogs.txt', 'a') or die("Unable to open logs");
            if($request->isApproved)
                fwrite($file, "-------------------\n [". $dtrpInfo->id."] DTRPinfo - Validated ". $correct->format('M d h:i:s'). " by [". $this->user->id."], ".$this->user->lastname."\n");
            else fwrite($file, "-------------------\n [". $dtrpInfo->id."] DTRPinfo - Rejected ". $correct->format('M d h:i:s'). " by [". $this->user->id."], ".$this->user->lastname."\n");
                    fclose($file);

            // we now check if approver still hasnt approved it
            // if pending, apply the same action from DTRPinfo

            $dtrp = User_DTRP::find($dtrpInfo->dtrp_id);
            
            if( is_null($dtrp->isApproved)) {
                $dtrp->isApproved = $request->isApproved;

                if ($dtrp->logType_id == 1) {
                    $theNotif = Notification::where('relatedModelID', $dtrp->id)->where('type',8)->get();
                    //then remove those sent notifs to the approvers since it has already been approved/denied
                    if (count($theNotif) > 0)
                        DB::table('user_Notification')->where('notification_id','=',$theNotif->first()->id)->delete();

                    //$unotif = $this->notifySender($DTRP,$theNotif->first(),8);
                }
                else {
                    $theNotif = Notification::where('relatedModelID', $dtrp->id)->where('type',9)->get();
                    //then remove those sent notifs to the approvers since it has already been approved/denied
                    if (count($theNotif) > 0)
                        DB::table('user_Notification')->where('notification_id','=',$theNotif->first()->id)->delete();
                }

                $dtrp->push();

            }

            //we now create manual overrides if it is marked VALID
            if($request->isApproved) {
                $o = new User_LogOverride;
                $o->user_id = $dtrp->user_id;
                $pd = Biometrics::find($dtrp->biometrics_id);
                $o->productionDate = $pd->productionDate;
                (is_null($dtrp->actualLogdate)) ? $o->affectedBio = $pd->id : $o->affectedBio = Biometrics::where('productionDate',$dtrp->actualLogdate)->first()->id;
                
                $o->logTime = $dtrp->logTime;
                $o->logType_id = $dtrp->logType_id;
                $o->created_at = $correct->format('Y-m-d H:i:s');
                $o->updated_at = $correct->format('Y-m-d H:i:s');
                $o->save();

                

            }

            //**** we now record those who's DTRP are invalid
            if($request->isApproved == 0 && $dtrp->isApproved)
            {
                $report = new User_DTRPreport;
                $report->user_id = $dtrp->user_id;
                $pd = Biometrics::find($dtrp->biometrics_id);
                $report->productionDate = $pd->productionDate;
                if(is_null($dtrp->actualLogdate)) {
                    $report->actualLog = $pd->productionDate." ".$dtrp->logTime;
                }else{
                    $report->actualLog = $dtrp->actualLogdate." ".$dtrp->logTime;
                }

                $report->approvedBy = $dtrp->approvedBy;
                $report->dateApproved = $dtrp->updated_at;
                $report->logType_id = $dtrp->logType_id;
                $report->notes = $dtrp->notes;
                $report->verifiedBy = $this->user->id;
                $report->remarks = $request->remarks;
                $report->attachments = $dtrpInfo->attachments;
                $report->created_at = $correct->format('Y-m-d H:i:s');
                $report->updated_at = $correct->format('Y-m-d H:i:s');
                $report->save();


            }


       
        

        return response()->json(['success'=>1,'dtrp'=>$dtrp]);



    }

    public function process(Request $request)
    {
        //return $request;
        $DTRP = User_DTRP::find($request->id);
        $correct = Carbon::now('GMT+8');
        if(count((array)$DTRP)>0)
        {
            $DTRP->approvedBy = $this->getTLapprover($DTRP->user_id, $this->user->id);
        
            if ($request->isApproved == 1){
                $DTRP->isApproved = true;

            }  else {
                $DTRP->isApproved=false;
            }

            $DTRP->push();

            /* -------------- log updates made --------------------- */

             $file = fopen('storage/uploads/dtrplogs.txt', 'a') or die("Unable to open logs");
                if($DTRP->isApproved)
            fwrite($file, "-------------------\n [". $DTRP->id."] DTRP ".LogType::find($DTRP->logType_id)->name." - Approved ". $correct->format('M d h:i:s'). " by [". $this->user->id."], ".$this->user->lastname."\n");
            else fwrite($file, "-------------------\n [". $DTRP->id."] DTRP ".LogType::find($DTRP->logType_id)->name." - Denied ". $correct->format('M d h:i:s'). " by [". $this->user->id."], ".$this->user->lastname."\n");
                fclose($file);


             //**** send notification to the sender
            if ($DTRP->logType_id == 1) {
                $theNotif = Notification::where('relatedModelID', $DTRP->id)->where('type',8)->get();
                //then remove those sent notifs to the approvers since it has already been approved/denied
                if (count($theNotif) > 0)
                DB::table('user_Notification')->where('notification_id','=',$theNotif->first()->id)->delete();

                $unotif = $this->notifySender($DTRP,$theNotif->first(),8);
            }
            else {
                $theNotif = Notification::where('relatedModelID', $DTRP->id)->where('type',9)->get();

                if (count($theNotif) > 0)
                DB::table('user_Notification')->where('notification_id','=',$theNotif->first()->id)->delete();

                $unotif = $this->notifySender($DTRP,$theNotif->first(),9);
            }

            


            
            $user = User::find($DTRP->user_id);
            (is_null($user->nickname)) ? $f = $user->firstname : $f = $user->nickname;

            return response()->json(['success'=>1, 'firstname'=>$f, 'lastname'=>$user->lastname, 'unotif'=>$unotif]);

        }else return response()->json(['DTRP'=>$DTRP, 'success'=>'0']);

    }



    public function show($id)
    {
        $DTRP = User_DTRP::find($id);

        //--- update notification
             if (Input::get('seen')){
                $markSeen = User_Notification::where('notification_id',Input::get('notif'))->where('user_id',$this->user->id)->first();
                $markSeen->seen = true;
                $markSeen->push();

            } 

         if (is_null($DTRP)) //just mark as seen and return empty view
        {
            return view('empty');

        } else {

            $user = User::find($DTRP->user_id);
            $profilePic = $this->getProfilePic($user->id);
            $leadershipcheck = ImmediateHead::where('employeeNumber', $user->employeeNumber)->first();
            // $date1 = Carbon::parse(Biometrics::find($cws->biometrics_id)->productionDate);
            // $payrollPeriod = Paycutoff::where('fromDate','>=', strtotime())->get(); //->where('toDate','<=',strtotime(Biometrics::find($cws->biometrics_id)->productionDate))->first();

            if (!empty($leadershipcheck)){ $camps = $leadershipcheck->campaigns->sortBy('name'); } else $camps = $user->campaign;

            $details = new Collection;

            $logType = LogType::find($DTRP->logType_id)->name;
            $details->push(['productionDate'=>date('M d, Y - l',strtotime(Biometrics::find($DTRP->biometrics_id)->productionDate)), 
                'dateRequested'=>date('M d, Y - l ', strtotime($DTRP->created_at)),
                
                'logTime' => date('h:i A', strtotime($DTRP->logTime)),
                'logType'=>$logType,
                'notes'=> $DTRP->notes ]);
            

            
            return view('timekeeping.show-DTRP', compact('user', 'profilePic','camps', 'DTRP','details'));

        }


        

    }

    public function store(Request $request)
    {
        /**** look for Timekeeping trait: $this->saveDTRP instead *****/
    }

    public function update($id, Request $request)
    {
        $DTRP = User_DTRP::find($id);
        if(count($DTRP)>0)
        {
            if ($request->isApproved == 1)
            {
                $DTRP->isApproved = true;
                
            }  else{
                $DTRP->isApproved=false;
            } 
            
            $DTRP->approvedBy = $this->getTLapprover($DTRP->user_id, $this->user->id);
            $DTRP->push();

             /* -------------- log updates made --------------------- */
             $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
                fwrite($file, "-------------------\n [". $DTRP->id."] DTRP update ". date('M d h:i:s'). " by [". $this->user->id."], ".$this->user->lastname."\n");
                fclose($file);



             //**** send notification to the sender
                if ($DTRP->logType_id == 1) {
                    $theNotif = Notification::where('relatedModelID', $DTRP->id)->where('type',8)->get();
                     //then remove those sent notifs to the approvers since it has already been approved/denied
                    if (count($theNotif) > 0)
                        DB::table('user_Notification')->where('notification_id','=',$theNotif->first()->id)->delete();

                    $unotif = $this->notifySender($DTRP,$theNotif->first(),8);
                }
                else {
                    $theNotif = Notification::where('relatedModelID', $DTRP->id)->where('type',9)->get();
                    if (count($theNotif) > 0)
                        DB::table('user_Notification')->where('notification_id','=',$theNotif->first()->id)->delete();

                    $unotif = $this->notifySender($DTRP,$theNotif->first(),9);
                }

               


                
                $user = User::find($DTRP->user_id);
                (is_null($user->nickname)) ? $f = $user->firstname : $f = $user->nickname;

                return response()->json(['success'=>1, 'firstname'=>$f, 'lastname'=>$user->lastname, 'unotif'=>$unotif]);

        }else return response()->json(['DTRP'=>$DTRP, 'success'=>'0']);

    }
}
