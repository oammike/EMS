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
use OAMPI_Eval\Holiday;
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
use OAMPI_Eval\User_SL;
use OAMPI_Eval\User_SLcredits;
use OAMPI_Eval\VLupdate;
use OAMPI_Eval\SLupdate;
use OAMPI_Eval\Notification;
use OAMPI_Eval\User_Notification;
use OAMPI_Eval\User_SLearnings;

class UserSLController extends Controller
{
    protected $user;
    protected $user_sl;
    use Traits\TimekeepingTraits;
    use Traits\UserTraits;


     public function __construct(User_SL $user_sl)
    {
        $this->middleware('auth');
        $this->user_sl = $user_sl;
        $this->user =  User::find(Auth::user()->id);
    }

    public function addCredits(Request $request)
    {
        $sl = new User_SLcredits;
        $sl->creditYear = $request->creditYear;

        $user = User::find($request->user_id);

        $lastUpdated = Carbon::parse($request->lastUpdated,"Asia/Manila");

        $sl->lastUpdated = $lastUpdated->format('Y-m-d H:i:s');
        $sl->beginBalance = $request->beginBalance;

        
        $sl->used = $request->used;
        $sl->paid = $request->paid;
        $sl->user_id =$request->user_id;
        $sl->save();

        return back();
        
    }

    public function addSLearnings(Request $request)
    {
        $sl = new User_SLearnings;
        $sl->user_id = $request->user_id;
        $sl->slupdate_id = $request->slupdate_id;
        $sl->save();

        return back();
        
    }

    public function addSLupdate(Request $request)
    {
        $sl = new SLupdate;
        $sl->period = Carbon::parse($request->newperiod,'Asia/Manila')->format('Y-m-d');
        $sl->credits = $request->newearning;
        $sl->save();

        return back();
        
    }



    public function checkExisting(Request $request)
    {
        $existingLeave=0;

        $vf = Carbon::parse($request->leaveStart,"Asia/Manila");
        $mayExisting = User_SL::where('user_id',$request->user_id)->where('leaveEnd','>',$vf->format('Y-m-d H:i:s'))->get();
        $interval = new \DateInterval("P1D");
        foreach ($mayExisting as $key) {
                $period = new \DatePeriod(new \DateTime(Carbon::parse($key->leaveStart,'Asia/Manila')->format('Y-m-d')),$interval, new \DateTime(Carbon::parse($key->leaveEnd,'Asia/Manila')->addDays(1)->format('Y-m-d')));
                //** we need to add 1 more day kasi di incuded sa loop ung leaveEnd

                foreach ($period as $p) 
                {
                    if($p->format('M d, Y') == $vf->format('M d, Y') ){
                        $existingLeave=true;
                        goto mayExistingReturn;
                        //break 2;
                    }
                }
                
        }

        if ($existingLeave==0)
            return response()->json(['existing'=>0]);


        mayExistingReturn:

            return response()->json(['existing'=>$existingLeave, 'sl'=>$mayExisting]);    

    }

    public function create(Request $request)
    {
        $foreignPartime = null;
        //check first kung may plotted sched and if approver submitted
        if(is_null($request->for))
            {
                $user = $this->user;
                $forSomeone = null;
            }
            else{

                $user = User::find($request->for);
                $forSomeone = $user;

                

            }
                 

        if (count( (array)$user) <1) return view('empty');
        else
        {
            ($user->status_id == 12 || $user->status_id == 14) ? $isParttimer = true : $isParttimer=false;
            //$isBackoffice = ( Campaign::find(Team::where('user_id',$user->id)->first()->campaign_id)->isBackoffice ) ? true : false;


            //check mo kung leave for himself or if for others and approver sya
            $approvers = $user->approvers;
            //Timekeeping Trait
            $anApprover = $this->checkIfAnApprover($approvers, $this->user);

            $correct = Carbon::now('GMT+8'); //->timezoneName();

                       if($this->user->id !== 564 ) {
                          $file = fopen('public/build/rewards.txt', 'a') or die("Unable to open logs");
                            fwrite($file, "-------------------\n Tried [SL]: ".$user->lastname."[".$user->id."] --" . $correct->format('M d h:i A'). " by [". $this->user->id."] ".$this->user->lastname."\n");
                            fclose($file);
                        } 

            $roles = UserType::find($this->user->userType_id)->roles->pluck('label'); //->where('label','MOVE_EMPLOYEES');
            /* -------- get this user's department. If Backoffice, WFM can't access this ------*/
            $isBackoffice = ( Campaign::find(Team::where('user_id',$user->id)->first()->campaign_id)->isBackoffice ) ? true : false;
            $isWorkforce =  ($roles->contains('STAFFING_MANAGEMENT')) ? '1':'0';

            if(!is_null($request->for) && !$anApprover && ($isWorkforce && $isBackoffice) ) return view('access-denied');

            if ($user->fixedSchedule->isEmpty() && $user->monthlySchedules->isEmpty())
            {
                $title = 'No Work Schedule found ';
                $message =  '<br/><br/><br/><br/> No work schedule defined<br /><br/> <i class="fa fa-calendar"></i> <small>Please inform immediate head or Workforce <br/>to have your work schedule plotted before you can file any work-related requests. <br/><br/>Thank you.</smaller></small>
                  <br /><br/>';
                  return view('empty-page', compact('message','title'));

            } 
            else
            {
                /*--- we need to check first kung may approver set na ---*/
                if (count($approvers)<1 ){
                    $title = 'No Approver defined ';
                    $message =  '<br/><br/><br/><br/><span class="text-danger"><i class="fa fa-exclamation-triangle"></i> No Approver defined</span><br /><br/><small>Please inform HR to update your profile <br/>and set the necessary approver(s) for all of your request submissions. <br/><br/>Thank you.</smaller></small>
                      <br /><br/>';
                      return view('empty-page', compact('message','title'));
                }else
                {

                    $today=Carbon::today();/*------- check first if user is entitled for a leave (Regualr employee or lengOfService > 6mos) *********/
                    $lengthOfService = Carbon::parse($user->dateHired,"Asia/Manila")->diffInMonths($today);

                    if ($lengthOfService >= 1)
                    {
                        if (empty($request->from))
                            $vl_from = Carbon::today();
                        else $vl_from = Carbon::parse($request->from,"Asia/Manila");

                        
                        $hasSavedCredits=false;

                        $savedCredits = User_SLcredits::where('user_id', $user->id)->where('creditYear',date('Y'))->get();

                        $vlEarnings = DB::table('user_slearnings')->where('user_slearnings.user_id',$user->id)->
                              join('slupdate','user_slearnings.slupdate_id','=', 'slupdate.id')->
                              select('slupdate.credits','slupdate.period')->where('slupdate.period','>',Carbon::parse(date('Y').'-01-01','Asia/Manila')->format('Y-m-d'))->get();
                        $totalVLearned = collect($vlEarnings)->sum('credits');

                       

                        $allAdvancedSL = DB::table('user_advancedSL')->where('user_advancedSL.user_id',$user->id)->
                                                    select('user_advancedSL.id','user_advancedSL.total', 'user_advancedSL.periodStart','user_advancedSL.periodEnd','user_advancedSL.created_at')->
                                                    where('user_advancedSL.periodStart','>=',Carbon::now('GMT+8')->startOfYear()->format('Y-m-d'))->
                                                    orderBy('user_advancedSL.periodEnd','DESC')->get(); //
                        $totalAdv = 0;
                        foreach($allAdvancedSL as $a)
                        {
                            $totalAdv += $a->total;
                        }


                        $allVTOs = DB::table('user_vto')->where('user_vto.user_id',$user->id)->where('user_vto.isApproved',1)->
                                        where('user_vto.productionDate','>=',Carbon::now('GMT+8')->startOfYear()->format('Y-m-d'))->
                                        where('user_vto.productionDate','<=',Carbon::now('GMT+8')->endOfYear()->format('Y-m-d'))->
                                            select('user_vto.totalHours','user_vto.productionDate', 'user_vto.deductFrom')->
                                            orderBy('user_vto.productionDate','DESC')->get(); //
                        $totalVTO = 0;
                        foreach($allVTOs as $v)
                        {
                            $totalVTO += ($v->totalHours* 0.125);
                        }

                        
                        
                            /*---- check mo muna kung may holiday today to properly initialize credits used ---*/
                            $holiday = Holiday::where('holidate',$vl_from->format('Y-m-d'))->get();
                            if (count(Holiday::where('holidate',$vl_from->format('Y-m-d'))->get()) > 0 && $isBackoffice)
                            {
                                $used = '0.00';
                                if (count($savedCredits)>0){
                                     $hasSavedCredits = true;
                                     $creditsLeft = $savedCredits->first()->beginBalance - $savedCredits->first()->used;
                                 }else 
                                 {
                                    //check muna kung may existing approved VLs
                                    $approvedVLs = User_SL::where('user_id',$user->id)->where('isApproved',true)->get();
                                    if (count($approvedVLs) > 0 )
                                    {
                                        $usedC = 0;
                                        foreach ($approvedVLs as $key) {
                                            $usedC += $key->totalCredits;
                                        }
                                        $creditsLeft = (0.84 * $today->format('m')) - $usedC ;
                                        $creditsLeft2 = (0.84 * $today->format('m')) - $usedC ;
                                    }
                                    else {
                                        $creditsLeft = (0.84 * $today->format('m')) ;$creditsLeft2 = (0.84 * $today->format('m')) ;
                                    }
                                    
                                 }

                            }
                            else{

                                $schedForTheDay = $this->getWorkSchedForTheDay1($user,$vl_from,null,false);
                               
                                //if 4HRs lang work nya, part timer sya or foreign na part timer
                                //dapat half lang credit nila
                                if( Carbon::parse($schedForTheDay->timeStart,'Asia/Manila')->diffInHours(Carbon::parse($schedForTheDay->timeEnd,'Asia/Manila')) > 4)
                                    $foreignPartime = 0;
                                    //credits = 1;
                                else
                                    $foreignPartime = 1; // 0.5;


                                ($isParttimer || $foreignPartime) ? $used = 0.5 : $used = 1.00; 

                                if (count($savedCredits)>0){
                                    $hasSavedCredits = true;
                                     

                                     $creditsLeft = ($savedCredits->first()->beginBalance - $savedCredits->first()->used - $used) + $totalVLearned - ($totalVTO + $totalAdv);
                                     $creditsLeft2 = ($savedCredits->first()->beginBalance - $savedCredits->first()->used) + $totalVLearned - ($totalVTO + $totalAdv);
                                 }else 
                                 {

                                    //check muna kung may existing approved VLs
                                    $approvedVLs = User_SL::where('user_id',$user->id)->where('isApproved',true)->get();
                                    if (count($approvedVLs) > 0 )
                                    {
                                        $usedC = 0;
                                        foreach ($approvedVLs as $key) {
                                            $usedC += $key->totalCredits;
                                        }
                                        $creditsLeft =((0.84 * $today->format('m')) - $usedC) - $used ;
                                        $creditsLeft2 =((0.84 * $today->format('m')) - $usedC);
                                    }
                                    else {
                                        $creditsLeft = (0.84 * $today->format('m')) - $used ;
                                        $creditsLeft2 = (0.84 * $today->format('m'));
                                    }
                                }
                            }  



                        //($creditsLeft2 >= 0.25) ? $canSL=1 : $canSL=0;  //return (['creditsleft'=>$creditsLeft2, 'vl_from'=>$vl_from]);
                        if ($isParttimer) {
                            ($creditsLeft2 >= 0.25) ? $canSL=1 : $canSL=0;
                        }else
                        {
                            ($creditsLeft2 >= 0.5) ? $canSL=1 : $canSL=0;
                        }

                        return view('timekeeping.user-sl_create',compact('user', 'forSomeone', 'vl_from','creditsLeft','used','hasSavedCredits','isParttimer','foreignPartime','canSL'));

                    }else return view('access-denied');

                } /*--- end kung may approver set na ---*/
                

            }

        }
        
         
        
    }

    public function deleteAdvSL( $id )
    {
        //User_SLearnings::find($id)->delete();
        DB::table('user_advancedSL')->where('id',$id)->delete();
        return response()->json(['success'=>1]); 

    }

    public function deleteCredit( $id )
    {
        $sl = User_SLcredits::find($id);
        $sl->delete();

        return back();

    }

    public function deleteSLearning( $id )
    {
        //User_SLearnings::find($id)->delete();
        DB::table('user_slearnings')->where('id',$id)->delete();
        return response()->json(['success'=>1]); 

    }


    public function deleteThisSL($id, Request $request)
    {
        $theVL = User_SL::find($id);

        //find all notifications related to that OT
        $theNotif = Notification::where('relatedModelID', $theVL->id)->where('type',$request->notifType)->get();

        if (count($theNotif) > 0){
            $allNotifs = User_Notification::where('notification_id', $theNotif->first()->id)->get();
            foreach ($allNotifs as $key) {
                $key->delete();
                
            }
        }
        

     /***** once saved, update your leave credits ***/
        $userVLs = User_SLcredits::where('user_id',$theVL->user_id)->orderBy('creditYear','DESC')->get();
        if (count($userVLs) > 0 )
        {
            $vlcredit = $userVLs->first();
            $vlcredit->used -= $theVL->totalCredits;
            $vlcredit->push();
        }


        $theVL->delete();


        if ($request->redirect == '1')
            return redirect()->back();
        else return response()->json(['success'=>"ok"]);
        
    }

    public function editCredits($id, Request $request)
    {
        $vl = User_SLcredits::find($id);
        $vl->beginBalance = $request->beginBalance;
        $vl->creditYear = $request->creditYear;
        $vl->used = $request->used;
        $vl->paid = $request->paid;
        $vl->push();

        return back();

    }

    public function getCredits(Request $request)
    {
        $user = User::find($request->user_id);
        ($user->status_id == 12 || $user->status_id == 14) ? $isParttimer = true : $isParttimer=false;
        $isBackoffice = ( Campaign::find(Team::where('user_id',$user->id)->first()->campaign_id)->isBackoffice ) ? true : false;

        $vl_from = Carbon::parse($request->date_from,"Asia/Manila");
        $vf = Carbon::parse($request->date_from,"Asia/Manila");
        $dateFrom = Carbon::parse($request->date_from,"Asia/Manila");
        $creditsleft =$request->creditsleft; //less 1 day from default
        $coll = new Collection;
        
        $shift_from = $request->shift_from;$shift_to = $request->shift_to;
        $schedules = new Collection;
        $displayShift = ""; $credits = 0;

        $hasVLalready=false;
        

        /*** we need to check first kung may existing pending or approved VL na
             para iwas doble filing **/

        //$mayExisting = User_VL::where('user_id',$user->id)->where('leaveStart','>=',$vf->startOfDay()->format('Y-m-d H:i:s'))->where('leaveStart','<',$vf->addDay()->format('Y-m-d H:i:s'))->where('leaveStart','<',$vf->addDay()->format('Y-m-d H:i:s'))get();

        $mayExisting = User_SL::where('user_id',$user->id)->where('leaveEnd','>=',$vf->endOfDay()->format('Y-m-d H:i:s'))->get();
        $interval = new \DateInterval("P1D");
        foreach ($mayExisting as $key) {
                $period = new \DatePeriod(new \DateTime(Carbon::parse($key->leaveStart,'Asia/Manila')->format('Y-m-d')),$interval, new \DateTime(Carbon::parse($key->leaveEnd,'Asia/Manila')->format('Y-m-d')));

                foreach ($period as $p) {
                    if($p->format('M d, Y') == $vf->format('M d, Y') ){
                        $hasVLalready=true;
                        $coll->push($p->format('M d, Y'));
                        goto mayExistingReturn;
                        //break 2;
                    }
                }
                
        }


            //*** if date range is submitted [from-to]

            $colldates = new Collection;
            

            if ( !is_null($request->date_to) && $request->date_to !== "" )
            {
                
                $holidays = 0;
                $vl_to =Carbon::parse($request->date_to,"Asia/Manila");
                $v=null;
                
                //$ct=0;
                while ($vl_from->format('Y-m-d') <= $vl_to->format('Y-m-d')) {
                    
                    $v = $vl_from->format('Y-m-d');
                    $schedForTheDay = $this->getWorkSchedForTheDay1($user,$v,$mayExisting,false);


                    if (is_null($schedForTheDay->isApproved) && $schedForTheDay->timeStart !== $schedForTheDay->timeEnd && !$schedForTheDay->isRD)
                    {
                        $credits++;
                        //** means mag credit ka lang pag sched na wala nang approval at hindi RD
                    }

                    if (count(Holiday::where('holidate',$vl_from->format('Y-m-d'))->get()) > 0) $holidays++;

                    //$colldates->push(['ct'=>$ct, 'credits'=>$credits, 'isRD'=>$schedForTheDay->isRD,'schedForTheDay'=>$schedForTheDay]);

                    $vl_from = $vl_from->addDays(1);//$ct++;

                }

                //$toCredit = $ct - $credits;

                //if ($shift_from == '2' || $shift_from=='3') $credits -= 0.5;
                if ($shift_to == '2' && $request->date_to !== null)
                {
                    //check mo muna kung RD to or holiday, wag ka na mag deduct
                    $schedForTheDay = $this->getWorkSchedForTheDay1($user,$vl_to,$mayExisting,false);
                    //if ( strpos($schedForTheDay['title'], "Rest") !== false || count(Holiday::where('holidate',$vl_to->format('Y-m-d'))->get())>0 ){ }
                    if ( $schedForTheDay->timeStart === $schedForTheDay->timeEnd || count(Holiday::where('holidate',$vl_to->format('Y-m-d'))->get())>0 ){ }
                    
                    /*else if (count(Holiday::where('holidate',$vl_to->format('Y-m-d'))->get()) > 0){ }*/
                    else $credits -= 0.5;
                } 


                $s = $this->getWorkSchedForTheDay1($user,Carbon::parse($request->date_from,"Asia/Manila"),$mayExisting,false);


                switch ($shift_from) {
                    case '2':{ 
                                $credits -= 0.5; 
                                $start = Carbon::parse($s->timeStart)->format('h:i A');
                                $end = Carbon::parse($s->timeStart)->addHour(4)->format('h:i A');
                                // Carbon::parse($schedForTheDay['start'])->addHour(4)->format('h:i A');
                                $displayShift = $start." - ".$end;
                                
                             }break;

                    
                    case '3':{ 
                                $credits -= 0.5; 
                                $start = Carbon::parse($s->timeEnd)->addHour(-4)->format('h:i A');
                                $end = Carbon::parse($s->timeEnd)->format('h:i A');
                                $displayShift = $start." - ".$end;

                             }break;
                }


                
                $credits -= $holidays;
                $creditsleft -= $credits;
                $creditsleft++; //fix for initially 1 credit deducted from loading
                

            } else
            {
                $schedForTheDay = $this->getWorkSchedForTheDay1($user,$vl_from,$mayExisting,false);

                //if 4HRs lang work nya, part timer sya
                //dapat half lang credit nila
                if( Carbon::parse($schedForTheDay->timeStart,'Asia/Manila')->diffInHours(Carbon::parse($schedForTheDay->timeEnd)) > 4) {
                    
                    $isPartForeign=false;$credits = 1;
                }
                else {
                    $isPartForeign=true;
                    $credits = 0.5;
                }
                //return response()->json(['vl_from'=>$vl_from, 'dateto'=>$request->date_to]);
                
                //return $schedForTheDay;

                //if ($shift_from == '2' || $shift_from=='3') $credits -= 0.5;

                switch ($shift_from) {
                    case '2':{ 
                                if (count(Holiday::where('holidate',$vl_from->format('Y-m-d'))->get()) > 0 && $isBackoffice)
                                {
                                    $credits = 0;
                                } else 
                                {
                                    ($isParttimer || $isPartForeign) ? $credits = 0.25 : $credits = 0.5; 
                                }

                                $start = Carbon::parse($schedForTheDay->first()->timeStart)->format('h:i A');
                                $end = Carbon::parse($schedForTheDay->first()->timeStart)->addHour(4)->format('h:i A');
                                $displayShift = $start." - ".$end;
                                //$creditsleft -= $credits;
                             }break;

                    
                    case '3':{ 
                                
                                if (count(Holiday::where('holidate',$vl_from->format('Y-m-d'))->get()) > 0 && $isBackoffice)
                                {
                                    $credits = 0;
                                } else 
                                {
                                    ($isParttimer || $isPartForeign) ? $credits = 0.25 : $credits = 0.5; 
                                }

                                $start = Carbon::parse($schedForTheDay->first()->timeEnd)->addHour(-4)->format('h:i A');
                                $end = Carbon::parse($schedForTheDay->first()->timeEnd)->format('h:i A');
                                $displayShift = $start." - ".$end;
                                //$creditsleft -= $credits;
                             }break;
                    default:{
                                
                                if (count(Holiday::where('holidate',$vl_from->format('Y-m-d'))->get()) > 0 && $isBackoffice)
                                {
                                    $credits = 0;
                                } else 
                                {
                                    ($isParttimer || $isPartForeign) ? $credits = 0.5 : $credits = 1.00; 
                                }

                                $displayShift =  Carbon::parse($schedForTheDay->first()->timeStart)->format('h:i A'). " - ". Carbon::parse($schedForTheDay->first()->timeEnd)->format('h:i A');
                                //$creditsleft;

                            }
                }

            }

            /*-------------we now check for excess filing, file it as LWOP instead ------------------ */
            $creditsToEarn = 0;
            $forLWOP=0;

            if ($creditsleft < 0)
            {
                $creditsToEarn = ( 12 - date('m') )* 0.84;

                if($creditsToEarn < abs($creditsleft))
                {
                    $forLWOP = $creditsToEarn - abs($creditsleft);
                }

            }

            return response()->json(['creditsleft'=>$creditsleft,'creditsToEarn'=>$creditsToEarn,'credits'=>$credits]);//'colldates'=>$colldates
            //return response()->json(['request->date_to'=>$request->date_to, 'shift_from'=>$shift_from, 'hasVLalready'=>$hasVLalready, 'creditsToEarn'=>$creditsToEarn, 'forLWOP'=>abs($forLWOP), 'creditsleft'=>number_format($creditsleft,2), 'credits'=> number_format(abs($credits),2) , 'shift_from'=>$shift_from, 'shift_to'=>$shift_to,'displayShift'=>$displayShift,  'schedForTheDay'=>$schedForTheDay]);



        /*}//end may existing nang VL application*/

        mayExistingReturn:
        return response()->json(['hasVLalready'=>$hasVLalready,'existingVL'=>$coll, 'creditsToEarn'=>0, 'forLWOP'=>0, 'creditsleft'=>0, 'credits'=> 0 , 'shift_from'=>$shift_from, 'shift_to'=>$shift_to,'displayShift'=>$displayShift,  'schedForTheDay'=>null]);


    }

    public function item($id)
    {
        $item = User_SL::find($id);
        if (count($item)>0)
        return response()->file(storage_path('uploads/'.$item->attachments));
        else 
            return view('empty');

    }


    public function process(Request $request)
    {
        $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
        fwrite($file, "-------------------\n [". $request->id."] VL REQUEST \n");
        fclose($file);
        $vl = User_SL::find($request->id);
        $vl->approver = $this->getTLapprover($vl->user_id, $this->user->id);
        
        if ($request->isApproved == 1){
            $vl->isApproved = true;

        }  else {
            $vl->isApproved=false;
        }

        $correct = Carbon::now('GMT+8');
        $vl->updated_at = $correct->format('Y-m-d H:i:s');

        $vl->save();

        /***** once saved, update your leave credits ***/
        $userVLs = User_SLcredits::where('user_id',$vl->user_id)->orderBy('creditYear','DESC')->get();
        if (count($userVLs) > 0 && $vl->isApproved)
        {
            $vlcredit = $userVLs->first();
            $vlcredit->used += $vl->totalCredits;
            $vlcredit->push();
        }

         //**** send notification to the sender
        $theNotif = Notification::where('relatedModelID', $vl->id)->where('type',11)->get();

        //then remove those sent notifs to the approvers since it has already been approved/denied
        if (count($theNotif) > 0)
            DB::table('user_Notification')->where('notification_id','=',$theNotif->first()->id)->delete();


        $unotif = $this->notifySender($vl,$theNotif->first(),11);

        /* //Next, delete all user-notif associated with this:
        $theNotif = Notification::where('relatedModelID',$vl->id)->where('type',6)->first();
        $allUserNotifs = User_Notification::where('notification_id',$theNotif->id)->delete(); */
        
          /* -------------- log updates made --------------------- */
         $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
            fwrite($file, "-------------------\n [". $vl->id."] VL update ". date('M d h:i:s'). " by [". $this->user->id."], ".$this->user->lastname."\n");
            fclose($file);

        $user = User::find($vl->user_id);

        (is_null($user->nickname)) ? $f = $user->firstname : $f = $user->nickname;
        return response()->json(['success'=>1, 'firstname'=>$f, 'lastname'=>$user->lastname, 'unotif'=>$unotif]);


    }


    public function requestSL(Request $request)
    {

        
        $vl = new User_SL;
        $vl->user_id = $request->userid;
        $vl->productionDate = $request->productionDate;
        $vl->leaveStart =  $request->leaveFrom;
        $vl->leaveEnd = $request->leaveTo;
        $vl->notes = $request->reason_vl;
        $vl->totalCredits= $request->totalcredits;
        $vl->forced = $request->forced;
        $vl->halfdayFrom = $request->halfdayFrom;
        $vl->halfdayTo = $request->halfdayTo;
        $attachments = $request->file('attachments');

        $employee = User::find($request->userid);
        //return response()->json(['user'=>$employee,'id'=>$request->userid, 'attachments'=>$attachments,'totalcredits'=>$request->totalcredits]);

        $approvers = $employee->approvers;
        $anApprover = $this->checkIfAnApprover($approvers, $this->user);
        $TLapprover = $this->getTLapprover($employee->id, $this->user->id);

         // get WFM
        $wfm = collect(DB::table('team')->where('campaign_id',50)->
                    leftJoin('users','team.user_id','=','users.id')->
                    select('team.user_id')->
                    where('users.status_id',"!=",7)->
                    where('users.status_id',"!=",8)->
                    where('users.status_id',"!=",9)->
                    where('users.status_id',"!=",13)->get())->pluck('user_id');
        $isWorkforce = in_array($this->user->id, $wfm->toArray());
        $employeeisBackoffice = ( Campaign::find(Team::where('user_id',$employee->id)->first()->campaign_id)->isBackoffice ) ? true : false;





        $correct = Carbon::now('GMT+8'); $key=null;
        
        if ( ($isWorkforce && ($this->user->id !== $employee->id) )
            || ($anApprover && $employeeisBackoffice) 
            || (!$employeeisBackoffice && $isWorkforce && ($this->user->id !== $employee->id) ) )
        {
            $vl->isApproved = true; $TLsubmitted=true; 
            if ($isWorkforce) 
                $vl->approver = $this->user->id;
            else
                $vl->approver = $TLapprover;

            


            //Update leave credits
            $key = User_SLcredits::where('user_id',$employee->id)->where('creditYear',Carbon::parse($request->leaveFrom,'Asia/Manila')->format('Y'))->get();
            
            if ( count($key) > 0) {
                $vlcred = $key->first();
                $vlcred->used += $request->totalcredits;
                $vlcred->lastUpdated = $correct->format('Y-m-d H:i:s');
                $vlcred->push();
                //$coll->push($key);

                $vl->created_at = $correct->format('Y-m-d H:i:s');
                $vl->updated_at = $correct->format('Y-m-d H:i:s');
                $vl->save();

                $success = 1; $msg = "SL saved successfully.";
            }else {
                $success = -1; $msg = "No leave credits available";
            }
            



        } else 
        { 
            $vl->isApproved = null; $TLsubmitted=false;$vl->approver = null; 
            $vl->created_at = $correct->format('Y-m-d H:i:s');
            $vl->updated_at = $correct->format('Y-m-d H:i:s');
            $vl->save();

            $success = 1; $msg = "SL saved successfully.";
        }




       



        

        if (!empty($attachments))
        {
              $today = Carbon::today()->format('Y-m-d_H_i_s');
              $destinationPath = storage_path() . '/uploads/';
              $extension = Input::file('attachments')->getClientOriginalExtension(); // getting image extension
              $fileName = $today.'-user-'.$employee->id.'-SLfile.'.$extension; // renameing image
              $attachments->move($destinationPath, $fileName); // uploading file to given path

              
              $vl->attachments = $fileName;

            
                /* -------------- log updates made --------------------- */
            $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
            fwrite($file, "\n-------------------\n New MedCert uploaded : ". $fileName ." ". date('M d h:i:s'). " by ". $this->user->firstname.", ".$this->user->lastname."\n");
            fclose($file);
            
              
        }

        $correct = Carbon::now('GMT+8');
        $vl->created_at = $correct->format('Y-m-d H:i:s');
        $vl->updated_at = $correct->format('Y-m-d H:i:s');
        $vl->save();

        if ( !$vl->isApproved && ( ($anApprover && !$employeeisBackoffice)  || (!$anApprover && !$isWorkforce) || (!$anApprover && $employeeisBackoffice) ) )//(!$TLsubmitted && !$canChangeSched)
        {
            /***** once saved, update your leave credits ***/
            $userVLs = User_SLcredits::where('user_id',$employee->id)->orderBy('creditYear','DESC')->get();
            if (count($userVLs) > 0 && $vl->isApproved)
            {
                $vlcredit = $userVLs->first();
                $vlcredit->used += $vl->totalCredits;
                $vlcredit->push();
            }

            $notification = new Notification;
            $notification->relatedModelID = $vl->id;
            $notification->type = 11;
            $notification->from = $vl->user_id;
            $notification->save();

            if ($employeeisBackoffice){

                foreach ($employee->approvers as $approver) {
                    $TL = ImmediateHead::find($approver->immediateHead_id);
                    $nu = new User_Notification;
                    $nu->user_id = $TL->userData->id;
                    $nu->notification_id = $notification->id;
                    $nu->seen = false;
                    $nu->save();

                    // NOW, EMAIL THE TL CONCERNED
                
                    $email_heading = "New Sick Leave Request from: ";
                    $email_body = "Employee: <strong> ". $employee->lastname.", ". $employee->firstname ."  </strong><br/>
                                   Date: <strong> ".$vl->leaveStart  . " to ". $vl->leaveEnd. " </strong> <br/>";
                    $actionLink = action('UserSLController@show',$vl->id);
                   
                     /*Mail::send('emails.generalNotif', ['user' => $TL, 'employee'=>$employee, 'email_heading'=>$email_heading, 'email_body'=>$email_body, 'actionLink'=>$actionLink], function ($m) use ($TL) 
                     {
                        $m->from('OES@openaccessbpo.net', 'OES-OAMPI Evaluation System');
                        $m->to($TL->userData->email, $TL->lastname.", ".$TL->firstname)->subject('New CWS request');     

                        
                             $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
                                fwrite($file, "-------------------\n Email sent to ". $TL->userData->email."\n");
                                fclose($file);                      
                    

                    }); //end mail */


            
                }

            }

            //-- we now notify all WFM
            if(!$employeeisBackoffice)
            {
                foreach ($wfm as $approver) {
                    //$TL = ImmediateHead::find($approver->immediateHead_id);
                    //-- make sure not to send nofication kung WFM agent ang sender
                    if ($this->user->id !== $approver)
                    {

                        $nu = new User_Notification;
                        $nu->user_id = $approver;
                        $nu->notification_id = $notification->id;
                        $nu->seen = false;
                        $nu->save();

                        // NOW, EMAIL THE TL CONCERNED
                    
                        $email_heading = "New SL Request from: ";
                        $email_body = "Employee: <strong> ". $employee->lastname.", ". $employee->firstname ."  </strong><br/>
                                       Date: <strong> ".$vl->leaveStart  . " to ". $vl->leaveEnd. " </strong> <br/>";
                        $actionLink = action('UserSLController@show',$vl->id);

                    }

                
                }
            }


        }


    
         /* -------------- log updates made --------------------- */
         $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
            fwrite($file, "-------------------\n". $employee->id .",". $employee->lastname." SL submission ". date('M d h:i:s'). " by ". $this->user->firstname.", ".$this->user->lastname."\n");
            fclose($file);
         

       

        if ($anApprover) return response()->json(['success'=>$success,'vl'=>$vl, 'message'=>$msg,'key'=>$key]);
        else return response()->json(['success'=>0,'vl'=>$vl,'message'=>"for approval",'key'=>$key]);




    }

    public function show($id)
    {

        //--- update notification
         if (Input::get('seen')){
            $markSeen = User_Notification::where('notification_id',Input::get('notif'))->where('user_id',$this->user->id)->get();
            if (count($markSeen)>0)
            {
                $markSeen->first()->seen = true;
                $markSeen->first()->push();
            }
                

        }


        $vl = User_SL::find($id);

        if (is_null($vl)) return view('empty');

        $user = User::find($vl->user_id);
        $profilePic = $this->getProfilePic($user->id);
        $leadershipcheck = ImmediateHead::where('employeeNumber', $user->employeeNumber)->first();
        // $date1 = Carbon::parse(Biometrics::find($cws->biometrics_id)->productionDate);
        // $payrollPeriod = Paycutoff::where('fromDate','>=', strtotime())->get(); //->where('toDate','<=',strtotime(Biometrics::find($cws->biometrics_id)->productionDate))->first();

        if (!empty($leadershipcheck)){ $camps = $leadershipcheck->campaigns->sortBy('name'); } else $camps = $user->campaign;

        $approvers = $user->approvers;
        $anApprover = $this->checkIfAnApprover($approvers, $this->user);

        $details = new Collection;

        $details->push(['from'=>date('M d - D',strtotime($vl->leaveStart)), 'to'=>date('M d - D',strtotime($vl->leaveEnd)),
            'totalCredits'=>$vl->totalCredits,
            'forced'=> ($vl->forced=='0') ? "No" : "Yes",
            'dateRequested'=>date('M d, Y - D ', strtotime($vl->created_at)),
            'notes'=> $vl->notes ]);
        

        
        //return $details;
        return view('timekeeping.show-SL', compact('user', 'profilePic','camps', 'vl','details','anApprover'));


    }

    public function updateCredits()
    {
        $for = Input::get('for');
        $credits = Input::get('credits');
        $period = Carbon::parse($for,"Asia/Manila");

        $coll = new Collection;

        $done = DB::table('slupdate')->where('period',$period->format('Y-m-d'))->get();

        if (count($done) > 0){

        }else{

            $updates = new SLupdate;
            $updates->period = $period->format('Y-m-d');
            $updates->credits = $credits;
            $updates->save();

            $allLeaves = User_SLcredits::where('creditYear',$period->format('Y'))->get();

            foreach ($allLeaves as $key) {

                $key->beginBalance += $credits;
                $key->lastUpdated = Carbon::now('GMT+8')->format('Y-m-d H:i:s');
                $key->save();
                $coll->push($key);
            }

        }

        return $coll;
    }


     public function uploadCredits(Request $request)
    {
        $today = date('Y-m-d');
        
        $bioFile = $request->file('biometricsData');
        if (!empty($bioFile))
        {
              //$destinationPath = 'uploads'; // upload path
              $destinationPath = storage_path() . '/uploads/';
              $extension = Input::file('biometricsData')->getClientOriginalExtension(); // getting image extension
              $fileName = $today.'-slredits.'.$extension; // renameing image
              $bioFile->move($destinationPath, $fileName); // uploading file to given path

                $file = fopen($destinationPath.$fileName, 'r');
                $coll = new Collection;
                $ctr=0;
                DB::connection()->disableQueryLog();
                while (($result = fgetcsv($file)) !== false)
                {
                    $user = User::find($result[0]);
                    if ($user) {
                        $vlCredits = User_SLcredits::where('user_id',$user->id)->where('creditYear',date('Y'))->delete();
                        //foreach($vlCredits as $vl){ $vl->delete(); }

                        $newCredit = new User_SLcredits;
                        $newCredit->user_id = $user->id;
                        $newCredit->beginBalance = $result[1];
                        $newCredit->used = abs($result[2]);
                        $newCredit->paid =0.0;
                        $newCredit->creditYear = date('Y');
                        $newCredit->lastUpdated = $result[3];
                        $newCredit->save();
                        $coll->push($newCredit);

                    }
                    

                            

                        
                }//end while

                fclose($file);

               return response()->json($coll);


              
        }
        else return response()->json(['success'=>false]);
        

    } 


}
