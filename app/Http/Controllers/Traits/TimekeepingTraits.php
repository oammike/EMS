<?php

namespace OAMPI_Eval\Http\Controllers\Traits;

use Carbon\Carbon;
use Excel;
use \PDF;
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
use OAMPI_Eval\UserType;
use OAMPI_Eval\Campaign;
use OAMPI_Eval\Status;
use OAMPI_Eval\Team;
use OAMPI_Eval\Floor;
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
use OAMPI_Eval\User_DTRP;
use OAMPI_Eval\User_DTRPinfo;
use OAMPI_Eval\User_CWS;
use OAMPI_Eval\User_MustLock;
use OAMPI_Eval\User_Notification;
use OAMPI_Eval\Notification;
use OAMPI_Eval\MonthlySchedules;
use OAMPI_Eval\FixedSchedules;
use OAMPI_Eval\User_OBT;
use OAMPI_Eval\User_OT;
use OAMPI_Eval\User_VL;
use OAMPI_Eval\User_VTO;
use OAMPI_Eval\User_SL;
use OAMPI_Eval\User_Familyleave;
use OAMPI_Eval\User_LWOP;
use OAMPI_Eval\User_RDoverride;
use OAMPI_Eval\User_LogOverride;
use OAMPI_Eval\Holiday;
use OAMPI_Eval\HolidayType;
use OAMPI_Eval\Paycutoff;

trait TimekeepingTraits
{

  

  

  public function checkIfAnApprover($approvers, $user)
  {
    $leaders = [];
    $ctr = 0;

    foreach ($approvers as $approver) {
      $leaders[$ctr] = ImmediateHead::find($approver->immediateHead_id)->employeeNumber;
      $ctr++;
    }

    if (in_array($user->employeeNumber, $leaders)) return true;
    else return false;

  }

  public function checkIfAnApprover2($approvers, $user)
  {
    $leaders = [];
    $ctr = 0;

    foreach ($approvers as $approver) {
      $leaders[$ctr] = ImmediateHead::find(ImmediateHead_Campaign::find($approver)->immediateHead_id)->employeeNumber;
      $ctr++;
    }

    if (in_array($user->employeeNumber, $leaders)) return true;
    else return false;

  }


  public function checkIfUndertime($type, $userLog, $schedForToday)
  {
    switch ($type) {
      case 'IN':
            if ($userLog > $schedForToday) return true; else return false;
        
        break;
      
      case 'OUT':
            if ($userLog < $schedForToday) return true; else return false;
        break;
    }
  }



  public function establishLeaves($id,$endShift,$leaveType,$thisPayrollDate,$schedForToday)
  {
    $alldaysVL=[];$hasVL=null;$vlDeet=null;$hasLeave=null;$vl=null;$hasPendingVL=null;


    /*-------- VACATION LEAVE  -----------*/
    $eod = Carbon::parse($thisPayrollDate,'Asia/Manila')->endOfDay();
    switch ($leaveType) {
      /*case 'VL': $vl1 = User_VL::where('user_id',$id)->where('leaveStart','<=',$endShift->format('Y-m-d H:i:s'))->orderBy('created_at','DESC')->get();
                  break;*/
      case 'VL': {
                  $vl2 =User_VL::where('user_id',$id)->where('productionDate',$thisPayrollDate)->orderBy('created_at','DESC')->get();
                  if(count($vl2) > 0) $vl1 = $vl2;
                  else
                      $vl1 = User_VL::where('user_id',$id)->where('leaveStart','<=',$eod->format('Y-m-d H:i:s'))->orderBy('created_at','DESC')->get();
                 }
                  break;
      
      case 'VTO': $vl1 = User_VTO::where('user_id',$id)->where('productionDate',$thisPayrollDate)->orderBy('created_at','DESC')->get();
                  break;
      case 'SL': {
                    $vl2 = User_SL::where('user_id',$id)->where('productionDate',$thisPayrollDate)->orderBy('created_at','DESC')->get();

                    if(count($vl2) > 0) $vl1 = $vl2;
                    else
                      $vl1 = User_SL::where('user_id',$id)->where('leaveStart','<=',$endShift->format('Y-m-d H:i:s'))->orderBy('created_at','DESC')->get();
                  }
                  break;
      case 'FL': $vl1 = User_Familyleave::where('user_id',$id)->where('leaveStart','<=',$endShift->format('Y-m-d H:i:s'))->orderBy('created_at','DESC')->get();
                  break;
      case 'LWOP': $vl1 = User_LWOP::where('user_id',$id)->where('leaveStart','<=',$endShift->format('Y-m-d H:i:s'))->orderBy('created_at','DESC')->get();
                  break;
      case 'OBT': $vl1 = User_OBT::where('user_id',$id)->orderBy('created_at','DESC')->get();
                  break;//where('leaveStart','<=',$endShift->format('Y-m-d H:i:s'))->
      
      
    }

    $vlcol= new Collection;$daysSakop=null;
    if (count($vl1) > 0)
    {
      $vl=true;
      
      //************* gawin mo to foreach family leave ************//
      foreach ($vl1 as $vacay) 
      {

        if ($leaveType == "VTO")
        {
          $daysSakop=1;
          array_push($alldaysVL, $vacay->productionDate);
          $vl= $vacay; 
          $hasVL=true;
          $hasLeave=true;
          $vlDeet= $vacay;
          (!is_null($vlDeet->isApproved)) ? $hasPendingVL=false : $hasPendingVL=true;

          break(1);

        }
        else
        {

          if ($vacay->productionDate !== null) $f_dayS = Carbon::parse($vacay->productionDate,'Asia/Manila');
          else $f_dayS = Carbon::parse($vacay->leaveStart,'Asia/Manila');
          
          $f_dayE = Carbon::parse($vacay->leaveEnd,'Asia/Manila');
          $full_leave = Carbon::parse($vacay->leaveEnd,'Asia/Manila')->addDays($vacay->totalCredits)->addDays(-1);
          $cf = $vacay->totalCredits;
          $fend = $f_dayE->format('Y-m-d');
          $cf2 = 1;

          
          if ($vacay->totalCredits <= 1)
          {
              if($schedForToday['isRD'] && ($schedForToday['timeStart']==$schedForToday['timeEnd'])) { }
              else
                array_push($alldaysVL, $f_dayS->format('Y-m-d'));


              //array_push($alldaysVL, $f_dayE->format('Y-m-d'));
              

          }else
          {
            $daysSakop = $f_dayE->diffInDays($f_dayS)+1;

            if($schedForToday['isRD'] && ($schedForToday['timeStart']==$schedForToday['timeEnd'])){ }
            else
            {
              while( $cf2 <= $daysSakop) {  // $cf){
            
                array_push($alldaysVL, $f_dayS->format('Y-m-d'));
                $f_dayS->addDays(1);
                $cf2++;
              }
              array_push($alldaysVL, $f_dayE->format('Y-m-d'));

            }

            

          }
          if(in_array($thisPayrollDate, $alldaysVL) ) {

            $vl= $vacay; 
            $hasVL=true;
            $hasLeave=true;
            $vlDeet= $vacay;
            (!is_null($vlDeet->isApproved)) ? $hasPendingVL=false : $hasPendingVL=true;

            break(1);
          }

        }

        
        
        
        
      }


      
    }else 
    {
      $vl=['X']; $hasVL = false;
      $vlDeet = null;
    }

    /*-------- VACATION LEAVE  -----------*/

    $theLeave = new Collection;
    $theLeave->leaveType = $vl;
    $theLeave->allDays = $alldaysVL;
    $theLeave->hasTheLeave = $hasVL;
    $theLeave->hasLeave = $hasLeave;
    $theLeave->details = $vlDeet;
    $theLeave->hasPending = $hasPendingVL;
    $theLeave->daysSakop = $daysSakop;
    $theLeave->schedForToday = $schedForToday;
    $theLeave->query = ['leaveStart<='=>$endShift->format('Y-m-d H:i:s'),'eod'=>$eod, 'vl1'=>$vl1, 'schedForToday'=>$schedForToday];

    return $theLeave;
    //return $vl1;


  }

  public function fetchLeaveCreditSummary()
  {


  }

  

  public function fetchLockedDTRs($c, $p, $reportType)
  {
      $cutoff = explode('_', $c);

      if($reportType == 3) $program = null;
      else
        $program = Campaign::find($p);

      DB::connection()->disableQueryLog();

      //------ Report type 1= DTR logs | 2= Summary | 3 = Trainee Summary
      if ($reportType == 1)
      {
        $allDTRs = DB::table('campaign')->where('campaign.id',$p)->
                      join('team','team.campaign_id','=','campaign.id')->
                      join('users','team.user_id','=','users.id')->
                      leftJoin('immediateHead_Campaigns','team.immediateHead_Campaigns_id','=','immediateHead_Campaigns.id')->
                      leftJoin('immediateHead','immediateHead_Campaigns.immediateHead_id','=','immediateHead.id')->
                      leftJoin('positions','users.position_id','=','positions.id')->
                      leftJoin('floor','team.floor_id','=','floor.id')->
                      join('user_dtr', function ($join) use ($cutoff) {
                          $join->on('users.id', '=', 'user_dtr.user_id')
                               ->where('user_dtr.productionDate', '>=', $cutoff[0])
                               ->where('user_dtr.productionDate', '<=', $cutoff[1]);
                      })->

                      //join('user_dtr','user_dtr.user_id','=','users.id')->
                      select('users.accesscode','users.id', 'users.firstname','users.lastname','users.middlename', 'users.nickname','positions.name as jobTitle','campaign.id as campID', 'campaign.name as program','immediateHead_Campaigns.id as tlID', 'immediateHead.firstname as leaderFname','immediateHead.lastname as leaderLname','floor.name as location','user_dtr.productionDate','user_dtr.biometrics_id','user_dtr.workshift','user_dtr.isCWS_id as cwsID','user_dtr.leaveType','user_dtr.leave_id','user_dtr.timeIN','user_dtr.timeOUT','user_dtr.hoursWorked','user_dtr.OT_billable','user_dtr.OT_approved','user_dtr.UT', 'user_dtr.user_id')->
                      where([
                          ['users.status_id', '!=', 6],
                          ['users.status_id', '!=', 7],
                          ['users.status_id', '!=', 8],
                          ['users.status_id', '!=', 9],
                          ['users.status_id', '!=', 13],
                          ['users.status_id', '!=', 16],
                          //['floor.id', '!=',10],
                          ['floor.id', '!=',11],
                      ])->orderBy('users.lastname')->get();
        $allUsers = DB::table('campaign')->where('campaign.id',$p)->
                      join('team','team.campaign_id','=','campaign.id')->
                      join('users','team.user_id','=','users.id')->
                      leftJoin('immediateHead_Campaigns','team.immediateHead_Campaigns_id','=','immediateHead_Campaigns.id')->
                      leftJoin('immediateHead','immediateHead_Campaigns.immediateHead_id','=','immediateHead.id')->
                      leftJoin('positions','users.position_id','=','positions.id')->
                      leftJoin('floor','team.floor_id','=','floor.id')->
                      
                      select('users.id', 'users.firstname','users.lastname','users.middlename', 'users.nickname','users.dateHired','positions.name as jobTitle','campaign.id as campID', 'campaign.name as program','immediateHead_Campaigns.id as tlID', 'immediateHead.firstname as leaderFname','immediateHead.lastname as leaderLname','users.employeeNumber','floor.name as location')->
                      where([
                          ['users.status_id', '!=', 6],
                          ['users.status_id', '!=', 7],
                          ['users.status_id', '!=', 8],
                          ['users.status_id', '!=', 9],
                          ['users.status_id', '!=', 13],
                          ['users.status_id', '!=', 16],
                          //['floor.id', '!=',10],
                          ['floor.id', '!=',11],
                      ])->orderBy('users.lastname')->get();
                      //return $allDTRs;

        $userArray = collect($allUsers)->pluck('id')->toArray();
        $dtrArray = collect($allDTRs)->pluck('id')->toArray();
        $pendings = array_diff($userArray, $dtrArray);

        //Timekeeping Trait
        $payrollPeriod = $this->getPayrollPeriod(Carbon::parse($cutoff[0],'Asia/Manila'),Carbon::parse($cutoff[1],'Asia/Manila'));

        $coll = new Collection;
        $coll->push(['payrollPeriod'=>$payrollPeriod, 'pendings'=>$pendings, 'userArray'=>$userArray, 'dtrArray'=>$dtrArray, 'users'=>$allUsers,'program'=>$program->name, 'total'=>count($allUsers),'cutoffstart'=>$cutoff[0], 'cutoffend'=>$cutoff[1], 'DTRs'=>$allDTRs,'submitted'=>count(collect($allDTRs)->groupBy('id'))]);


      }
      elseif($reportType == 3) //trainee summary
      {
        $allDTRs = DB::table('users')->where('users.status_id',2)->
                      join('team','team.user_id','=','users.id')->
                      join('campaign','team.campaign_id','=','campaign.id')->
                      leftJoin('immediateHead_Campaigns','team.immediateHead_Campaigns_id','=','immediateHead_Campaigns.id')->
                      leftJoin('immediateHead','immediateHead_Campaigns.immediateHead_id','=','immediateHead.id')->
                      leftJoin('positions','users.position_id','=','positions.id')->
                      leftJoin('floor','team.floor_id','=','floor.id')->
                      join('user_dtr', function ($join) use ($cutoff) {
                          $join->on('users.id', '=', 'user_dtr.user_id')
                               ->where('user_dtr.productionDate', '>=', $cutoff[0])
                               ->where('user_dtr.productionDate', '<=', $cutoff[1]);
                      })->

                      //join('user_dtr','user_dtr.user_id','=','users.id')->
                      select('users.id', 'users.firstname','users.lastname','users.middlename', 'users.nickname','positions.name as jobTitle','campaign.id as campID', 'campaign.name as program','immediateHead_Campaigns.id as tlID', 'immediateHead.firstname as leaderFname','immediateHead.lastname as leaderLname','floor.name as location','user_dtr.productionDate','user_dtr.timeIN', 'user_dtr.user_id')->
                      where([
                          ['floor.id', '!=',10],
                          ['floor.id', '!=',11],
                      ])->orderBy('users.lastname')->get();
        $allUsers = $allDTRs;

        $userArray = collect($allUsers)->pluck('id')->toArray();
        $dtrArray = collect($allDTRs)->pluck('id')->toArray();
        $pendings = array_diff($userArray, $dtrArray);

        //Timekeeping Trait
        $payrollPeriod = $this->getPayrollPeriod(Carbon::parse($cutoff[0],'Asia/Manila'),Carbon::parse($cutoff[1],'Asia/Manila'));

        $coll = new Collection;
        $coll->push(['payrollPeriod'=>$payrollPeriod, 'pendings'=>$pendings, 'userArray'=>$userArray, 'dtrArray'=>$dtrArray, 'users'=>$allUsers,'program'=>"TRAINEES", 'total'=>count($allUsers),'cutoffstart'=>$cutoff[0], 'cutoffend'=>$cutoff[1], 'DTRs'=>$allDTRs,'groupedDTRs'=>collect($allDTRs)->groupBy('id'), 'submitted'=>count(collect($allDTRs)->groupBy('id'))]);


      }
      elseif($reportType == 4) //trainee PASSED summary
      {
        $allDTRs = DB::table('users')->where('users.status_id',18)->
                      join('team','team.user_id','=','users.id')->
                      join('campaign','team.campaign_id','=','campaign.id')->
                      leftJoin('immediateHead_Campaigns','team.immediateHead_Campaigns_id','=','immediateHead_Campaigns.id')->
                      leftJoin('immediateHead','immediateHead_Campaigns.immediateHead_id','=','immediateHead.id')->
                      leftJoin('positions','users.position_id','=','positions.id')->
                      leftJoin('floor','team.floor_id','=','floor.id')->
                      join('user_dtr', function ($join) use ($cutoff) {
                          $join->on('users.id', '=', 'user_dtr.user_id')
                               ->where('user_dtr.productionDate', '>=', $cutoff[0])
                               ->where('user_dtr.productionDate', '<=', $cutoff[1]);
                      })->

                      //join('user_dtr','user_dtr.user_id','=','users.id')->
                      select('users.id', 'users.firstname','users.lastname','users.middlename', 'users.nickname','positions.name as jobTitle','campaign.id as campID', 'campaign.name as program','immediateHead_Campaigns.id as tlID', 'immediateHead.firstname as leaderFname','immediateHead.lastname as leaderLname','floor.name as location','user_dtr.productionDate','user_dtr.timeIN', 'user_dtr.user_id')->
                      where([
                          ['floor.id', '!=',10],
                          ['floor.id', '!=',11],
                      ])->orderBy('users.lastname')->get();
        $allUsers = $allDTRs;

        $userArray = collect($allUsers)->pluck('id')->toArray();
        $dtrArray = collect($allDTRs)->pluck('id')->toArray();
        $pendings = array_diff($userArray, $dtrArray);

        //Timekeeping Trait
        $payrollPeriod = $this->getPayrollPeriod(Carbon::parse($cutoff[0],'Asia/Manila'),Carbon::parse($cutoff[1],'Asia/Manila'));

        $coll = new Collection;
        $coll->push(['payrollPeriod'=>$payrollPeriod, 'pendings'=>$pendings, 'userArray'=>$userArray, 'dtrArray'=>$dtrArray, 'users'=>$allUsers,'program'=>"TRAINEES", 'total'=>count($allUsers),'cutoffstart'=>$cutoff[0], 'cutoffend'=>$cutoff[1], 'DTRs'=>$allDTRs,'submitted'=>count(collect($allDTRs)->groupBy('id'))]);


      }
      elseif($reportType == 5) //trainee FALLOUT summary
      {
        $allDTRs = DB::table('users')->where('users.status_id',19)->
                      join('team','team.user_id','=','users.id')->
                      join('campaign','team.campaign_id','=','campaign.id')->
                      leftJoin('immediateHead_Campaigns','team.immediateHead_Campaigns_id','=','immediateHead_Campaigns.id')->
                      leftJoin('immediateHead','immediateHead_Campaigns.immediateHead_id','=','immediateHead.id')->
                      leftJoin('positions','users.position_id','=','positions.id')->
                      leftJoin('floor','team.floor_id','=','floor.id')->
                      join('user_dtr', function ($join) use ($cutoff) {
                          $join->on('users.id', '=', 'user_dtr.user_id')
                               ->where('user_dtr.productionDate', '>=', $cutoff[0])
                               ->where('user_dtr.productionDate', '<=', $cutoff[1]);
                      })->

                      //join('user_dtr','user_dtr.user_id','=','users.id')->
                      select('users.id', 'users.firstname','users.lastname','users.middlename', 'users.nickname','positions.name as jobTitle','campaign.id as campID', 'campaign.name as program','immediateHead_Campaigns.id as tlID', 'immediateHead.firstname as leaderFname','immediateHead.lastname as leaderLname','floor.name as location','user_dtr.productionDate','user_dtr.timeIN', 'user_dtr.user_id')->
                      where([
                          ['floor.id', '!=',10],
                          ['floor.id', '!=',11],
                      ])->orderBy('users.lastname')->get();
        $allUsers = $allDTRs;

        $userArray = collect($allUsers)->pluck('id')->toArray();
        $dtrArray = collect($allDTRs)->pluck('id')->toArray();
        $pendings = array_diff($userArray, $dtrArray);

        //Timekeeping Trait
        $payrollPeriod = $this->getPayrollPeriod(Carbon::parse($cutoff[0],'Asia/Manila'),Carbon::parse($cutoff[1],'Asia/Manila'));

        $coll = new Collection;
        $coll->push(['payrollPeriod'=>$payrollPeriod, 'pendings'=>$pendings, 'userArray'=>$userArray, 'dtrArray'=>$dtrArray, 'users'=>$allUsers,'program'=>"TRAINEES", 'total'=>count($allUsers),'cutoffstart'=>$cutoff[0], 'cutoffend'=>$cutoff[1], 'DTRs'=>$allDTRs,'submitted'=>count(collect($allDTRs)->groupBy('id'))]);


      }
      elseif($reportType == 6) //NEW HIRES summary
      {
        $allDTRs = DB::table('users')->where('users.status_id',19)->
                      join('team','team.user_id','=','users.id')->
                      join('campaign','team.campaign_id','=','campaign.id')->
                      leftJoin('immediateHead_Campaigns','team.immediateHead_Campaigns_id','=','immediateHead_Campaigns.id')->
                      leftJoin('immediateHead','immediateHead_Campaigns.immediateHead_id','=','immediateHead.id')->
                      leftJoin('positions','users.position_id','=','positions.id')->
                      leftJoin('floor','team.floor_id','=','floor.id')->
                      join('user_dtr', function ($join) use ($cutoff) {
                          $join->on('users.id', '=', 'user_dtr.user_id')
                               ->where('user_dtr.productionDate', '>=', $cutoff[0])
                               ->where('user_dtr.productionDate', '<=', $cutoff[1]);
                      })->

                      //join('user_dtr','user_dtr.user_id','=','users.id')->
                      select('users.id', 'users.firstname','users.lastname','users.middlename', 'users.nickname','positions.name as jobTitle','campaign.id as campID', 'campaign.name as program','immediateHead_Campaigns.id as tlID', 'immediateHead.firstname as leaderFname','immediateHead.lastname as leaderLname','floor.name as location','user_dtr.productionDate','user_dtr.timeIN', 'user_dtr.user_id')->
                      where([
                          ['floor.id', '!=',10],
                          ['floor.id', '!=',11],
                      ])->orderBy('users.lastname')->get();
        $allUsers = $allDTRs;

        $userArray = collect($allUsers)->pluck('id')->toArray();
        $dtrArray = collect($allDTRs)->pluck('id')->toArray();
        $pendings = array_diff($userArray, $dtrArray);

        //Timekeeping Trait
        $payrollPeriod = $this->getPayrollPeriod(Carbon::parse($cutoff[0],'Asia/Manila'),Carbon::parse($cutoff[1],'Asia/Manila'));

        $coll = new Collection;
        $coll->push(['payrollPeriod'=>$payrollPeriod, 'pendings'=>$pendings, 'userArray'=>$userArray, 'dtrArray'=>$dtrArray, 'users'=>$allUsers,'program'=>"NEW HIRES", 'total'=>count($allUsers),'cutoffstart'=>$cutoff[0], 'cutoffend'=>$cutoff[1], 'DTRs'=>$allDTRs,'submitted'=>count(collect($allDTRs)->groupBy('id'))]);


      }
      else
      {
        $allDTRs = DB::table('campaign')->where('campaign.id',$p)->
                      join('team','team.campaign_id','=','campaign.id')->
                      join('users','team.user_id','=','users.id')->
                      leftJoin('immediateHead_Campaigns','team.immediateHead_Campaigns_id','=','immediateHead_Campaigns.id')->
                      leftJoin('immediateHead','immediateHead_Campaigns.immediateHead_id','=','immediateHead.id')->
                      leftJoin('positions','users.position_id','=','positions.id')->
                      leftJoin('floor','team.floor_id','=','floor.id')->
                      join('user_dtr', function ($join) use ($cutoff) {
                          $join->on('users.id', '=', 'user_dtr.user_id')
                               ->where('user_dtr.productionDate', '>=', $cutoff[0])
                               ->where('user_dtr.productionDate', '<=', $cutoff[1]);
                      })->

                      //join('user_dtr','user_dtr.user_id','=','users.id')->
                      select('users.id', 'users.firstname','users.lastname','users.middlename', 'users.nickname','positions.name as jobTitle','campaign.id as campID', 'campaign.name as program','immediateHead_Campaigns.id as tlID', 'immediateHead.firstname as leaderFname','immediateHead.lastname as leaderLname','floor.name as location','user_dtr.productionDate','user_dtr.timeIN', 'user_dtr.user_id')->
                      where([
                          ['users.status_id', '!=', 6],
                          ['users.status_id', '!=', 7],
                          ['users.status_id', '!=', 8],
                          ['users.status_id', '!=', 9],
                          ['users.status_id', '!=', 13],
                          ['users.status_id', '!=', 16],
                          //['floor.id', '!=',10],
                          ['floor.id', '!=',11],
                      ])->orderBy('users.lastname')->get();
        $allUsers = DB::table('campaign')->where('campaign.id',$p)->
                      join('team','team.campaign_id','=','campaign.id')->
                      join('users','team.user_id','=','users.id')->
                      leftJoin('immediateHead_Campaigns','team.immediateHead_Campaigns_id','=','immediateHead_Campaigns.id')->
                      leftJoin('immediateHead','immediateHead_Campaigns.immediateHead_id','=','immediateHead.id')->
                      leftJoin('positions','users.position_id','=','positions.id')->
                      leftJoin('floor','team.floor_id','=','floor.id')->
                      
                      select('users.id', 'users.firstname','users.lastname','users.middlename', 'users.nickname','users.dateHired','positions.name as jobTitle','campaign.id as campID', 'campaign.name as program','immediateHead_Campaigns.id as tlID', 'immediateHead.firstname as leaderFname','immediateHead.lastname as leaderLname','users.employeeNumber','floor.name as location')->
                      where([
                          ['users.status_id', '!=', 6],
                          ['users.status_id', '!=', 7],
                          ['users.status_id', '!=', 8],
                          ['users.status_id', '!=', 9],
                          ['users.status_id', '!=', 13],
                          ['users.status_id', '!=', 16],
                          //['floor.id', '!=',10],
                          ['floor.id', '!=',11],
                      ])->orderBy('users.lastname')->get();
                      //return $allDTRs;

        $userArray = collect($allUsers)->pluck('id')->toArray();
        $dtrArray = collect($allDTRs)->pluck('id')->toArray();
        $pendings = array_diff($userArray, $dtrArray);

        //Timekeeping Trait
        $payrollPeriod = $this->getPayrollPeriod(Carbon::parse($cutoff[0],'Asia/Manila'),Carbon::parse($cutoff[1],'Asia/Manila'));

        $coll = new Collection;
        $coll->push(['payrollPeriod'=>$payrollPeriod, 'pendings'=>$pendings, 'userArray'=>$userArray, 'dtrArray'=>$dtrArray, 'users'=>$allUsers,'program'=>$program->name, 'total'=>count($allUsers),'cutoffstart'=>$cutoff[0], 'cutoffend'=>$cutoff[1], 'DTRs'=>$allDTRs,'submitted'=>count(collect($allDTRs)->groupBy('id'))]);


      }
      
      
      

      return $coll;
  }




  public function generateShifts($timeFormat, $shiftType)
  {
    $data = array();

    switch ($shiftType) {
      case 'full': $addHr = 9;break; 
      case 'part': $addHr = 4; break;
      case '4x11': $addHr = 11; break;
    }

    for( $i = 1; $i <= 24; $i++)
        { 
          $time1 = Carbon::parse('1999-01-01 '.$i.':00:00');
          $time1b = Carbon::parse('1999-01-01 '.$i.':15:00');
          $time2 = Carbon::parse('1999-01-01 '.$i.':30:00');
          $time2b = Carbon::parse('1999-01-01 '.$i.':45:00');

          if($timeFormat == '12H')
          {
            array_push($data, $time1->format('h:i A')." - ".$time1->addHours($addHr)->format('h:i A'));
            array_push($data, $time1b->format('h:i A')." - ".$time1b->addHours($addHr)->format('h:i A'));
            array_push($data, $time2->format('h:i A')." - ".$time2->addHours($addHr)->format('h:i A'));
            array_push($data, $time2b->format('h:i A')." - ".$time2b->addHours($addHr)->format('h:i A'));

          } else
          {
            array_push($data, $time1->format('H:i')." - ".$time1->addHours($addHr)->format('H:i'));
            array_push($data, $time1b->format('H:i')." - ".$time1b->addHours($addHr)->format('H:i'));
            array_push($data, $time2->format('H:i')." - ".$time2->addHours($addHr)->format('H:i'));
            array_push($data, $time2b->format('H:i')." - ".$time2b->addHours($addHr)->format('H:i'));
          }
        }

    
    return $data;


  }

  public function getActualSchedForToday($user,$id,$payday,$bioForTheDay, $hybridSched,$isFixedSched,$hybridSched_WS_fixed,$hybridSched_WS_monthly, $hybridSched_RD_fixed, $hybridSched_RD_monthly, $workSched, $RDsched, $approvedCWS)
  {
    $carbonPayday = Carbon::parse($payday);
    ( count($approvedCWS) > 0 ) ? $hasApprovedCWS=true : $hasApprovedCWS=false;
    $daysOfWeek = array('Mon','Tue','Wed','Thu','Fri','Sat','Sun');

    
    $check_fixed_RD=null;$check_monthly_RD=null;$check_monthly_WS=null;$mc=null;$fc=null;$rd=null;$wd=null;

    $exemptEmp = DB::table('user_schedType')->where('user_id',$user->id)->join('schedType','schedType.id','=','user_schedType.schedType_id')->orderBy('user_schedType.created_at','DESC')->get();
    
    
      

    if ($hybridSched)
    {

        $dt  = $carbonPayday->dayOfWeek;
        switch($dt){
          case 0: $dayToday = 6; break;
          case 1: $dayToday = 0; break;
          default: $dayToday = $dt-1;
        } 

        //$check_fixed_WS = $hybridSched_WS_fixed->where('workday',$dayToday)->sortByDesc('created_at');
        $check_fixed_WS = $this->getLatestFixedSchedGrouped($hybridSched_WS_fixed,$payday,$dayToday);
        $check_fixed_WS_group = $hybridSched_WS_fixed; //collect($check_fixed_WS)->groupBy('schedEffectivity');

        //if (count($check_fixed_WS) > 0) //if may fixed WS, check mo kung ano mas updated vs monthly sched
        if($check_fixed_WS['workday'] !== null && $check_fixed_WS['created_at'] !== null)
        //if($check_fixed_WS !== null)
        {
          $check_monthly_WS = $hybridSched_WS_monthly->where('productionDate', $payday)->sortByDesc('created_at');
          //$coll->push(['check_monthly_WS'=>$check_monthly_WS]);
          if (count($check_monthly_WS) > 0)// if may monthly, compare it vs fixed
          {
            $stat =  "may monthly WS, compare it with fixed";

            //** but check first grouped Fixed WS; meaning more than 1 fixed scheds
            if (count($check_fixed_WS_group) > 1)
            {
              foreach ($check_fixed_WS_group as $g) 
              {
                //$coll->push(['pasok foreach'=>$g->first()]);

                if( Carbon::parse($check_monthly_WS->first()->created_at,'Asia/Manila')->format('Y-m-d H:i:s') > Carbon::parse($g->first()->created_at,'Asia/Manila')->format('Y-m-d H:i:s')  ) //mas bago si Monthly
                {

                  $workSched = $hybridSched_WS_monthly;

                  (count($hybridSched_RD_monthly) > 0) ? $RDsched = $hybridSched_RD_monthly : $RDsched = $hybridSched_RD_fixed;
                  $isFixedSched = false;
                  $noWorkSched =false;

                  break; //leave the loop kasi you already found the real sched



                }
                else //check mo muna validity nung WS na fixed. If no effectivity, then NO SCHED
                {
                  if ((Carbon::parse($g->first()->schedEffectivity)->startOfDay() <= $carbonPayday->startOfDay()) || $g->first()->schedEffectivity == null)
                  {
                    $workSched = $hybridSched_WS_fixed;
                    $RDsched = $hybridSched_RD_fixed;
                    $isFixedSched = true;
                    $noWorkSched = false;
                    break;

                    
                  }
                  /*else{
                    $noWorkSched = true;$workSched = null;$RDsched = null;$isFixedSched = false; $hasCWS=false;
                   }*/
                   else
                   {
                      /* DO NOTHING TILL YOU TRAVERSE THE WHOLE GROUP*/

                   }


                }
              }//end foreach grouped fixed sched

            }else
            {

              (is_object($check_fixed_WS)) ? $toparse = $check_fixed_WS->first()->created_at : $toparse = $check_fixed_WS['created_at'];

              if( Carbon::parse($check_monthly_WS->first()->created_at,'Asia/Manila')->format('Y-m-d H:i:s') > Carbon::parse($toparse,'Asia/Manila')->format('Y-m-d H:i:s')  ) //mas bago si Monthly
              {

                $workSched = $hybridSched_WS_monthly;

                (count($hybridSched_RD_monthly) > 0) ? $RDsched = $hybridSched_RD_monthly->where('productionDate', $payday) : $RDsched = $hybridSched_RD_fixed;
                //$RDsched = $hybridSched_RD_monthly->where('productionDate', $payday)->sortByDesc('created_at');

                $isFixedSched = false;
                $noWorkSched =false;



              }
              else //check mo muna validity nung WS na fixed. If no effectivity, then NO SCHED
              {
                (is_object($check_fixed_WS)) ? $toparse = $check_fixed_WS->first()->schedEffectivity : $toparse = $check_fixed_WS['schedEffectivity'];

                if ((Carbon::parse($toparse)->startOfDay() <= $carbonPayday->startOfDay()) || $toparse == null)
                {
                  $workSched = $hybridSched_WS_fixed;
                  $RDsched = $hybridSched_RD_fixed;
                  $isFixedSched = true;
                  $noWorkSched = false;

                  
                }
                /*else{
                  $noWorkSched = true;$workSched = null;$RDsched = null;$isFixedSched = false; $hasCWS=false;
                 }*/
                 else{
                  $workSched = $hybridSched_WS_monthly;

                  (count($hybridSched_RD_monthly) > 0) ? $RDsched = $hybridSched_RD_monthly : $RDsched = $hybridSched_RD_fixed;
                  
                  $isFixedSched = false;
                  $noWorkSched =false; $hasCWS=false;

                 }


              }

            }

            

          }//end if meron monthly sched
          else /* fixed sched na talaga sya, but not yet coz there's a possibility na RD yung that date sa monthly sched */
          {
            $checkKungMayRD = $hybridSched_RD_monthly->where('productionDate', $payday)->sortByDesc('created_at');
            if (count ($checkKungMayRD) > 0)
            {
              //ngayon, check mo kung sino mas updated, si monthlyRD or si fixed
              foreach ($check_fixed_WS_group as $g) 
              {
                //$coll->push(['pasok foreach'=>$g->first()]);

                if( Carbon::parse($checkKungMayRD->first()->created_at,'Asia/Manila')->format('Y-m-d H:i:s') > Carbon::parse($g->first()->created_at,'Asia/Manila')->format('Y-m-d H:i:s')  ) //mas bago si Monthly
                {

                  $workSched = $hybridSched_WS_monthly;

                  (count($hybridSched_RD_monthly) > 0) ? $RDsched = $hybridSched_RD_monthly : $RDsched = $hybridSched_RD_fixed;
                  $isFixedSched = false;
                  $noWorkSched =false;
                  break; //leave the loop kasi you already found the real sched



                }
                else //check mo muna validity nung WS na fixed. If no effectivity, then NO SCHED
                {
                  if ((Carbon::parse($g->first()->schedEffectivity)->startOfDay() <= $carbonPayday->startOfDay()) || $g->first()->schedEffectivity == null)
                  {
                    $workSched = $hybridSched_WS_fixed;
                    $RDsched = $hybridSched_RD_fixed;
                    $isFixedSched = true;
                    $noWorkSched = false;
                    break;

                    
                  }
                  /*else{
                    $noWorkSched = true;$workSched = null;$RDsched = null;$isFixedSched = false; $hasCWS=false;
                   }*/
                   else
                   {
                      /* DO NOTHING TILL YOU TRAVERSE THE WHOLE GROUP*/

                   }


                }
              }//end foreach grouped fixed sched


              

            }
            else
            {
              $stat =  "fixed na talaga";
              //check mo muna validity nung WS na fixed. If not effective, then NO SCHED

              (is_object($check_fixed_WS)) ? $toparse = $check_fixed_WS->first()->schedEffectivity : $toparse = $check_fixed_WS['schedEffectivity'];

              if ((Carbon::parse($toparse)->startOfDay() <= $carbonPayday->startOfDay()) || $toparse == null)
              {
                $workSched = $hybridSched_WS_fixed;
                $RDsched = $hybridSched_RD_fixed;
                $isFixedSched = true;
                $noWorkSched = false;
                
              }
              else
              {
                $stat =  "fixed na talaga | ELSE ng empty check_monthly_RD";

                //** new check: get mo yung next available fixed sched grouped by effectivity date
                $check_fixed_WS = $hybridSched_WS_fixed->where('workday',$dayToday)->sortByDesc('created_at')->groupBy('schedEffectivity');
                $wsCount = 0;
                foreach ($check_fixed_WS as $key) 
                {
                  if ($wsCount == 1)
                  {
                    $workSched = $key;
                    $RDsched =  $hybridSched_RD_fixed;
                    $isFixedSched = true;
                    $noWorkSched = false;
                    break;

                  } 
                  else $wsCount++;
                  
                } 

                //$noWorkSched = true;$workSched = null;$RDsched = null;$isFixedSched = true; $hasCWS=false;
                //$stat = $check_fixed_WS;
              }
            }//end if else checkKungMayRD
            

            
            //$check_monthly_RD = $hybridSched_RD_monthly->where('productionDate',$payday)->sortByDesc('created_at');
            // * not so fast. Check mo muna kung may monthly RD to be sure. Otherwise, fixed WS nga sya

          

             
          }

          //$coll->push(['status'=>"has fixed WS", 'stat'=>$stat, 'compared with hybrid'=>$hybridSched_WS_monthly] );
          


        } else //baka RD or monthly sched sya
        {
          //$check_fixed_RD = $hybridSched_RD_fixed->where('workday',$dayToday)->sortByDesc('created_at');
          $check_fixed_RD = $this->getLatestFixedSchedGrouped($hybridSched_RD_fixed,$payday,$dayToday);

          //if (count($check_fixed_RD) > 0) //if may fixed RD, check mo kung ano mas updated vs monthly sched
          if ( $check_fixed_RD['isRD'] && $check_fixed_RD['created_at'] !== null )//$check_fixed_RD['workday'] == null || 
          {
            $stat =  "if may fixed RD, check mo kung ano mas updated vs monthly sched";

            $check_monthly_RD = $hybridSched_RD_monthly->where('productionDate',$payday)->sortByDesc('created_at');

            if (count($check_monthly_RD) > 0) // if may monthly, compare it vs fixed
            {
              (is_object($check_fixed_RD)) ? $toparse = $check_fixed_RD->first()->created_at : $toparse = $check_fixed_RD['created_at'];

              if( Carbon::parse($check_monthly_RD->first()->created_at,'Asia/Manila')->format('Y-m-d H:i:s') > Carbon::parse($toparse,'Asia/Manila')->format('Y-m-d H:i:s') ) //mas bago si Monthly
              {
                $workSched = $hybridSched_WS_monthly;
                $RDsched = $hybridSched_RD_monthly;
                $isFixedSched = false;
                $noWorkSched = false;

              }
              else //FIXED RD SYA
              {
                //check mo muna validity nung RD na fixed. If not effective, then NO SCHED

                 (is_object($check_fixed_RD)) ? $toparse = $check_fixed_RD->first()->schedEffectivity : $toparse = $check_fixed_RD['schedEffectivity']; //$check_fixed_RD['schedEffectivity'];
                 if ((Carbon::parse($toparse)->startOfDay() <= $carbonPayday->startOfDay()) || $toparse['schedEffectivity'] == null)
                 {
                  $workSched = $hybridSched_WS_fixed;
                  $RDsched = $hybridSched_RD_fixed;
                  $isFixedSched = true;
                  $noWorkSched= false;

                 }
                 else
                 {
                  //$noWorkSched = true;$workSched = null;$RDsched = null;$isFixedSched = false; $hasCWS=false;
                  $workSched = $hybridSched_WS_fixed;
                  $RDsched = $hybridSched_RD_fixed;
                  $isFixedSched = true;
                  $noWorkSched= false; $hasCWS=false;

                 }

              }

            }
            else // no monthly RD -- meaning RD fixed na sya, BUT NOT YET! CHECK MO KUNG MAS UPDATED UNG WS IF EVER MERON
            {
              $check_monthly_WS = $hybridSched_WS_monthly->where('productionDate', $payday)->sortByDesc('created_at');

              if (count($check_monthly_WS) > 0)
              {
                
                (is_object($check_fixed_RD)) ? $toparse = $check_fixed_RD->created_at : $toparse = $check_fixed_RD['created_at'];
                if(Carbon::parse($check_monthly_WS->first()->created_at,'Asia/Manila')->format('Y-m-d H:i:s') > Carbon::parse($toparse,'Asia/Manila')->format('Y-m-d H:i:s')  )
                {
                  // $mc = Carbon::parse($check_monthly_WS->first()->created_at,'Asia/Manila')->format('Y-m-d H:i:s');
                  // $fc = Carbon::parse($check_fixed_RD->created_at,'Asia/Manila')->format('Y-m-d H:i:s');
                  $workSched = $hybridSched_WS_monthly;
                  $RDsched = $hybridSched_RD_monthly;
                  $isFixedSched = false;
                  $noWorkSched =false;
                }
                else
                  {
                    //check mo muna validity nung RD na fixed. If not effective, then NO SCHED

                    (is_object($check_fixed_RD)) ? $toparse = $check_fixed_RD->schedEffectivity : $toparse = $check_fixed_RD['schedEffectivity'];

                     if ((Carbon::parse($toparse)->startOfDay() <= $carbonPayday->startOfDay()) || $toparse == null)
                     {

                      $workSched = $hybridSched_WS_fixed;
                      $RDsched = $hybridSched_RD_fixed;
                      $isFixedSched = true;
                      $noWorkSched = false;
                     }
                     else
                     {
                      $noWorkSched = true;$workSched = null;$RDsched = null;$isFixedSched = false; $hasCWS=false;
                     }

                  }

              } else //walang monthly WS, Fixed sched na sya
              {
                 //check mo muna validity nung WS na fixed. If not effective, then NO SCHED

                 /*if ((Carbon::parse($check_fixed_RD->schedEffectivity)->startOfDay() <= $carbonPayday->startOfDay()) || $check_fixed_RD->first()->schedEffectivity == null)
                 {
                    $workSched = $hybridSched_WS_fixed;
                    $RDsched = $hybridSched_RD_fixed;
                    $isFixedSched = true;
                    $noWorkSched = false;
                 }
                 else
                 {*/
                  //$noWorkSched = true;$workSched = null;$RDsched = null;$isFixedSched = false; $hasCWS=false;
                   $workSched = $hybridSched_WS_fixed;
                    $RDsched = $hybridSched_RD_fixed;
                    $isFixedSched = true;
                    $noWorkSched = false; $hasCWS=false;
                 /*}*/


              }
             
            }

          }//end if no fixed RD

          else //---- no both fixed WS & RD, baka monthly sched. Check kung meron
          {
            $stat =  "no both fixed WS & RD, baka monthly sched";

            if (count($hybridSched_WS_monthly)>0 && count($hybridSched_RD_monthly)>0)
            {
              // if (count($hybridSched_WS_monthly) > 0)
              // {
                $workSched = $hybridSched_WS_monthly;
                $isFixedSched=false;

              // }// else $workSched = null;

              // if (count($hybridSched_RD_monthly) > 0)
              // {
                $RDsched = $hybridSched_RD_monthly;
              //}
              //else $RDsched = null;
                $stat =  "both hybrid has count";

            }else //waley na talaga
            {
              //final check sa fixed
              if ( count($hybridSched_RD_fixed->where('workday',$dayToday)->sortByDesc('created_at') ) > 0 )
              {
                $workSched = $hybridSched_WS_fixed;
                $RDsched = $hybridSched_RD_fixed;
                $stat =  "waley";

              }else{
                $workSched=null; $RDsched=null; $isFixedSched=false; $noWorkSched=true;$stat = "superduper waley";
                
              }
              


            }
            
            

          }//end else no both fixed RD & WS

          //$coll->push(['status'=>"NO fixed WS, baka RD", 'stat'=>$stat]);
          
          

        }//end else baka RD


    }
    


    if($isFixedSched)
    {
      $daysOfWeek = array('Mon','Tue','Wed','Thu','Fri','Sat','Sun'); // for Gregorian cal. Iba kasi jddayofweek sa PHP day
      $day = date('D', strtotime($payday)); //--- get his worksched and RDsched
      $theday = (string)$day;
      $numDay = array_search($theday, $daysOfWeek);

      $yest = date('D', strtotime(Carbon::parse($payday)->subDay()->format('Y-m-d')));
      $prevNumDay = array_search($yest, $daysOfWeek);



      if ($hasApprovedCWS)
      {
        if ($approvedCWS->first()->timeStart === '00:00:00' && $approvedCWS->first()->timeEnd === '00:00:00')
         {
            $isRDToday=true;
            $schedForToday = array('timeStart'=>'* RD *', 
                                    'timeEnd'=>'* RD *' ,
                                    'isFlexitime' => false,
                                    'isRD'=> true);

         } 
         else 
         {
            $isRDToday=false;
            $schedForToday = array('timeStart'=>$approvedCWS->first()->timeStart, 
                                    'timeEnd'=> $approvedCWS->first()->timeEnd,
                                    'isFlexitime' => false,
                                    'isRD'=> $approvedCWS->first()->isRD);
         }
         $RDsched1 = $RDsched;

          
          
    

      } else
      {
        $schedForToday = $this->getLatestFixedSchedGrouped($workSched,$payday,$numDay);//->toArray();
        $isRDToday = $schedForToday['isRD'];
        $RDsched1 = $RDsched;//$this->getLatestFixedSchedGrouped($RDsched,$payday,$numDay); //

      } 
          

      

    }else
    {
      // MONTHLY SCHED ANG NAKA PLOT
      if ($hasApprovedCWS)
      {
        // check mo muna kung mas updated ung plotted sched sa CWS
        if (is_null($workSched)){
          $isRDToday=false;
          $schedForToday = array('timeStart'=>$approvedCWS->first()->timeStart, 
                                            'timeEnd'=> $approvedCWS->first()->timeEnd,
                                            'isFlexitime' => false,
                                            'isRD'=> $approvedCWS->first()->isRD);

          $isRDToday=false;
          $RDsched1=null;
          $isFixedSched=false;
          $RDsched=null;

        }
        else 
        { 
          if ( count($workSched->where('productionDate',$payday)->all()) > 0 )
          {

             if ($workSched->where('productionDate',$payday)->sortByDesc('id')->first()->created_at > $approvedCWS->first()->updated_at )
              {
                $schedForToday = $workSched->where('productionDate',$payday)->sortByDesc('id')->first();
                $isRDToday = $workSched->where('productionDate',$payday)->sortByDesc('id')->first()->isRD;
                $RDsched1 = $RDsched;

              }else 
              {

                 if ($approvedCWS->first()->timeStart === '00:00:00' && $approvedCWS->first()->timeEnd === '00:00:00')
                 {
                  $isRDToday=true;
                  $schedForToday = array('timeStart'=>'* RD *', 
                                            'timeEnd'=>'* RD *' ,
                                            'isFlexitime' => false,
                                            'isRD'=> true);

                 } 
                 else 
                 {
                  $isRDToday=false;
                  $schedForToday = array('timeStart'=>$approvedCWS->first()->timeStart, 
                                            'timeEnd'=> $approvedCWS->first()->timeEnd,
                                            'isFlexitime' => false,
                                            'isRD'=> false); // $approvedCWS->first()->isRD);
                 }
                 $RDsched1 = $RDsched;



              }
              

          } else 
          {

                if ($approvedCWS->first()->timeStart === '00:00:00' && $approvedCWS->first()->timeEnd === '00:00:00')
                 {
                  $isRDToday=true;
                  $schedForToday = array('timeStart'=>'* RD *', 
                                            'timeEnd'=>'* RD *' ,
                                            'isFlexitime' => false,
                                            'isRD'=> true);

                 } 
                 else 
                 {
                  $isRDToday=false;
                  $schedForToday = array('timeStart'=>$approvedCWS->first()->timeStart, 
                                            'timeEnd'=> $approvedCWS->first()->timeEnd,
                                            'isFlexitime' => false,
                                            'isRD'=> false); // $approvedCWS->first()->isRD);
                 }
                 $RDsched1 = $RDsched;


          }
        }
        

      } else //walang CWS
      {
        if (is_null($workSched)){

          $schedForToday = null;
          $isRDToday = null; //$schedForToday['isRD'];
          $RDsched1 = $RDsched;
        }else
        {
          // know first kung anong meron, RD or workday
          $rd = $RDsched->where('productionDate',$payday)->sortByDesc('created_at');
          $wd = $workSched->where('productionDate',$payday)->sortByDesc('created_at');
          if (count($rd) > 0 )
          {
            //but first, make sure kung mas updated yung RD kesa WD
            if (count($wd) > 0)
            {
              if ($rd->first()->created_at > $wd->first()->created_at) //RD na talaga sya
              {
                $schedForToday = $rd->first();
                $isRDToday = true;
                $RDsched1 = $RDsched;
              }
              else //mas updated WS
              {
                $schedForToday = $wd->first();
                $isRDToday = false;
                $RDsched1 = $RDsched;
              }

            }else
            {
              $schedForToday = $rd->first();
              $isRDToday = true;
              $RDsched1 = $RDsched;


            }
            
          }else
          {
            $schedForToday = $wd->first();
            $isRDToday = false;
            $RDsched1 = $RDsched;
          }
          
          
          
          $stat ="final";

        }
          
      }

    }//end if else


    if (count($exemptEmp) > 0) 
    {
      if ($isRDToday)
       {
          $isRDToday=true;
          $schedForToday = array('timeStart'=>'* RD *', 
                                  'timeEnd'=>'* RD *' ,
                                  'isFlexitime' => false,
                                  'isRD'=> true);

       } 
       else 
       {
          
          $tIN = Logs::where('user_id',$user->id)->where('biometrics_id',$bioForTheDay->id)->where('logType_id',1)->orderBy('id','ASC')->get();
          if (count($tIN) > 0)
          {

            //get timein
            if($exemptEmp[0]->schedType_id == '2') //flexi 8hr
            {
                

                $dt = Carbon::parse($bioForTheDay->productionDate." ".$tIN->first()->logTime,'Asia/Manila')->format('H:i:s');
                $ds = explode(':', $dt);
                if($ds[1] == '00') {

                  $tstrt = Carbon::parse($payday." ".$ds[0].":00:00",'Asia/Manila')->format('H:i:s');
                  $tend = Carbon::parse($payday." ".$ds[0].":00:00",'Asia/Manila')->addHour(9)->format('H:i:s');
                 
                }
                elseif($ds[1] > '00' && $ds[1] <= '15'){
                  $tstrt = Carbon::parse($payday." ".$ds[0].":15:00",'Asia/Manila')->format('H:i:s');
                  $tend = Carbon::parse($payday." ".$ds[0].":15:00",'Asia/Manila')->addHour(9)->format('H:i:s');

                }
                elseif($ds[1] > 15 && $ds[1] <=30){
                  $tstrt = Carbon::parse($payday." ".$ds[0].":30:00",'Asia/Manila')->format('H:i:s');
                  $tend = Carbon::parse($payday." ".$ds[0].":30:00",'Asia/Manila')->addHour(9)->format('H:i:s');

                }
                elseif($ds[1] > 30 && $ds[1] <=45){
                  $tstrt = Carbon::parse($payday." ".$ds[0].":45:00",'Asia/Manila')->format('H:i:s');
                  $tend = Carbon::parse($payday." ".$ds[0].":45:00",'Asia/Manila')->addHour(9)->format('H:i:s');
                }
                elseif($ds[1] > 45){
                  $tstrt = Carbon::parse($payday." ".($ds[0]+1).":00:00",'Asia/Manila')->format('H:i:s');
                  $tend = Carbon::parse($payday." ".($ds[0]+1).":00:00",'Asia/Manila')->addHour(9)->format('H:i:s');

                }


            }
            else{
              $tstrt = $tIN->first()->logTime;
              $tend = Carbon::parse($bioForTheDay->productionDate." ".$tIN->first()->logTime,'Asia/Manila')->addHours(9)->format('H:i:s');



            }


              $isRDToday=false;
              $schedForToday = array('timeStart'=>$tstrt, 
                                    'timeEnd'=>$tend ,
                                    'isFlexitime' => false,
                                    'isRD'=> false);

          }
          else goto ProceedRegSched;
          
       }
       $RDsched1 = $RDsched;

       $c = new Collection;
       $c->schedForToday =  $schedForToday;
       $c->isRDToday = $isRDToday;
       $c->RDsched = $RDsched1;
       $c->isFixedSched = $isFixedSched;
       $c->allRD = $RDsched;

    }else
    {
      
      ProceedRegSched:

          $c = new Collection;
          $c->schedForToday =  collect($schedForToday)->toArray();
          $c->isRDToday = $isRDToday;
          $c->RDsched = $RDsched1;
          $c->isFixedSched = $isFixedSched;
          $c->allRD = $RDsched;

    }


        

    

    

    
    // $c->workSched = $workSched;
    /*$c->check_fixed_WS = $check_fixed_WS;
    $c->check_fixed_RD = $check_fixed_RD;
    $c->check_monthly_RD = $check_monthly_RD;
    $c->check_monthly_WS = $check_monthly_WS;
    $c->rd = $rd; $c->wd = $wd;*/
    // $c->hybridSched_RD_monthly = $hybridSched_RD_monthly;
    // $c->hybridSched_RD_fixed = $hybridSched_RD_fixed;
    // $c->mc = $mc;$c->fc=$fc;
   
    return $c;


  }


  public function getAllCWS($c, $json, $isDTRsummary)
  {
    $cutoff = explode('_', $c); //return $cutoff;
    $startCutoff = $cutoff[0]; //Biometrics::where('productionDate',$cutoff[0])->first();
    $endCutoff = $cutoff[1]; //Biometrics::where('productionDate',$cutoff[1])->first();
    $period = Biometrics::where('productionDate','>=',$startCutoff)->where('productionDate','<=',$endCutoff)->get();
    //return response()->json(['s'=>$startCutoff,'e'=>$endCutoff]);// $period;

    $allOTs = new Collection;
    $total = 0;

    foreach ($period as $p) {

       if ($isDTRsummary) 
       {
          $ot = DB::table('user_cws')->where([ 
                    ['user_cws.biometrics_id',$p->id]
                    ])->join('users','users.id','=','user_cws.user_id')->
                    join('biometrics','user_cws.biometrics_id','=','biometrics.id')->
                    join('team','team.user_id','=','user_cws.user_id')->
                    join('campaign','campaign.id','=','team.campaign_id')->
                  select('biometrics.productionDate','user_cws.isApproved','user_cws.isRD','user_cws.timeStart_old','user_cws.timeEnd_old','user_cws.timeStart','user_cws.timeEnd','users.employeeCode as accesscode', 'users.id as userID','users.lastname','users.firstname','campaign.name as program','user_cws.approver','user_cws.updated_at','user_cws.notes')->get();

       }
        
       else {
          $ot = DB::table('user_cws')->where([ 
                    ['user_cws.biometrics_id',$p->id]
                    ])->join('users','users.id','=','user_cws.user_id')->
                    join('biometrics','user_cws.biometrics_id','=','biometrics.id')->
                  select('biometrics.productionDate','user_cws.isApproved','user_cws.isRD','user_cws.timeStart_old','user_cws.timeEnd_old','user_cws.timeStart','user_cws.timeEnd','users.employeeCode as accesscode', 'users.id as userID','users.lastname','users.firstname')->get();

       }
        

      if (count($ot) > 0) {
        $allOTs->push($ot);
        $total += count($ot);
      }
        
    }
    if ($json)
      return response()->json(['CWS'=>$allOTs, 'total'=>$total, 'name'=>'Change Shift Schedule', 'cutoffstart'=>$startCutoff,'cutoffend'=>$endCutoff]);
    else
      return $allOTs;

  }



  public function getAllLeaves($c, $json)
  {
    $cutoff = explode('_', $c); //return $cutoff;
    $startCutoff = $cutoff[0]; //Biometrics::where('productionDate',$cutoff[0])->first();
    $endCutoff = $cutoff[1]; //Biometrics::where('productionDate',$cutoff[1])->first();
    //$period = Biometrics::where('productionDate','>=',$startCutoff)->where('productionDate','<=',$endCutoff)->get();
    //return response()->json(['s'=>$startCutoff,'e'=>$endCutoff]);// $period;

    DB::connection()->disableQueryLog();

    $allOTs = new Collection;
    $total = 0;
    $allLeaves = new Collection;

    $VL = DB::table('user_vl')->where([ 
                  ['user_vl.leaveStart','>=', $startCutoff." 00:00:00"],
                  ['user_vl.leaveEnd','<=', $endCutoff." 23:59:00"],
                  ])->join('users','users.id','=','user_vl.user_id')->
                  
                select('user_vl.productionDate','user_vl.leaveStart','user_vl.leaveEnd','user_vl.isApproved','user_vl.totalCredits','user_vl.halfdayFrom','user_vl.halfdayTo', 'user_vl.created_at', 'user_vl.notes','users.employeeCode as accesscode', 'users.id as userID','users.lastname','users.firstname')->get();
    $SL = DB::table('user_sl')->where([ 
                  ['user_sl.leaveStart','>=', $startCutoff." 00:00:00"],
                  ['user_sl.leaveEnd','<=', $endCutoff." 23:59:00"],
                  ])->join('users','users.id','=','user_sl.user_id')->
                  
                select('user_sl.productionDate','user_sl.leaveStart','user_sl.leaveEnd','user_sl.isApproved','user_sl.totalCredits','user_sl.halfdayFrom','user_sl.halfdayTo', 'user_sl.created_at', 'user_sl.notes','users.employeeCode as accesscode', 'users.id as userID','users.lastname','users.firstname')->get();

    $all_vto = DB::table('user_vto')->where([ 
                  ['user_vto.productionDate','>=', $startCutoff],
                  ['user_vto.productionDate','<=', $endCutoff],
                  ])->join('users','users.id','=','user_vto.user_id')->
                  select('user_vto.deductFrom', 'user_vto.productionDate', 'user_vto.startTime','user_vto.endTime', 'user_vto.isApproved','user_vto.totalHours','user_vto.created_at', 'user_vto.notes','users.employeeCode as accesscode', 'users.id as userID','users.lastname','users.firstname')->get();

    $LWOP = DB::table('user_lwop')->where([ 
                  ['user_lwop.leaveStart','>=', $startCutoff." 00:00:00"],
                  ['user_lwop.leaveEnd','<=', $endCutoff." 23:59:00"],
                  ])->join('users','users.id','=','user_lwop.user_id')->
                  
                select('user_lwop.productionDate','user_lwop.leaveStart','user_lwop.leaveEnd','user_lwop.isApproved','user_lwop.totalCredits','user_lwop.halfdayFrom','user_lwop.halfdayTo', 'user_lwop.created_at', 'user_lwop.notes','users.employeeCode as accesscode', 'users.id as userID','users.lastname','users.firstname')->get();

   //allocate 1month for FLs
    $startExt = Carbon::parse($startCutoff,'Asia/Manila')->addDays(-31);
    $endExt = Carbon::parse($endCutoff,'Asia/Manila')->addDays(31);
    $FL = DB::table('user_familyleaves')->where([ 
                  ['user_familyleaves.leaveStart','>=', $startExt->format('Y-m-d H:i:s')],
                  //['user_familyleaves.leaveEnd','<=', $endExt->format('Y-m-d H:i:s')],
                  
                  
                  ])->join('users','users.id','=','user_familyleaves.user_id')->
                  
                select('user_familyleaves.productionDate', 'user_familyleaves.leaveType', 'user_familyleaves.leaveStart','user_familyleaves.leaveEnd','user_familyleaves.isApproved','user_familyleaves.totalCredits','user_familyleaves.halfdayFrom','user_familyleaves.halfdayTo', 'user_familyleaves.created_at', 'user_familyleaves.notes','users.employeeCode as accesscode', 'users.id as userID','users.lastname','users.firstname')->get();

    
    $total = count($VL) + count($SL) + count($LWOP) + count($FL);

    if (count($VL) > 0) $allLeaves->push(['type'=>"VL", 'data'=>$VL]);
    if (count($SL) > 0) $allLeaves->push(['type'=>'SL', 'data'=>$SL]);
    if (count($LWOP) > 0) $allLeaves->push(['type'=>'LWOP', 'data'=>$LWOP]);
    if (count($FL) > 0) $allLeaves->push(['type'=>'FL', 'data'=>$FL]);
    if (count($all_vto) > 0) $allLeaves->push(['type'=>'VTO', 'data'=>$all_vto]);
        
    
    if ($json)
      return response()->json(['leaves'=>$allLeaves, 'total'=>$total, 'name'=>"leave", 'cutoffstart'=>$startCutoff,'cutoffend'=>$endCutoff]);
    else
      return $allLeaves;

  }

  public function getAllOT($c, $json,$user)
  {
    $cutoff = explode('_', $c); //return $cutoff;
    $startCutoff = $cutoff[0]; //Biometrics::where('productionDate',$cutoff[0])->first();
    $endCutoff = $cutoff[1]; //Biometrics::where('productionDate',$cutoff[1])->first();
    $period = Biometrics::where('productionDate','>=',$startCutoff)->where('productionDate','<=',$endCutoff)->get();
    //return response()->json(['s'=>$startCutoff,'e'=>$endCutoff]);// $period;

    $allOTs = new Collection;
    $total = 0;

    $specialChild = DB::table('user_specialPowers')->where('user_specialPowers.user_id',$user->id)->
                          leftJoin('user_specialPowers_programs','user_specialPowers_programs.specialPower_id','=','user_specialPowers.id')->get();
    (count($specialChild) > 0) ? $hasAccess=1 : $hasAccess=0;



   

      if($hasAccess){

            $ot = DB::table('user_specialPowers')->where('user_specialPowers.user_id',$user->id)->
                          leftJoin('user_specialPowers_programs','user_specialPowers_programs.specialPower_id','=','user_specialPowers.id')->
                          leftJoin('campaign','user_specialPowers_programs.program_id','=','campaign.id')->
                          leftJoin('team','user_specialPowers_programs.program_id','=','team.campaign_id')->
                          leftJoin('users','team.user_id','=','users.id')->
                          leftJoin('user_ot','user_ot.user_id','=','users.id')->
                          leftJoin('biometrics','biometrics.id','=','user_ot.biometrics_id')->
                          select('biometrics.productionDate','user_ot.id as leaveID', 'user_ot.isApproved','user_ot.filed_hours','user_ot.billable_hours', 'user_ot.timeStart','user_ot.timeEnd','user_ot.reason as notes', 'users.employeeCode as accesscode', 'users.id as userID','users.lastname','users.nickname', 'users.firstname','campaign.name as program','campaign.id as programID')->
                          where('biometrics.productionDate','<=',$endCutoff)->
                          where('biometrics.productionDate','>=',$startCutoff)->orderBy('user_ot.isApproved','ASC')->get();

                $allOTs->push($ot);
                $total += count($ot);

       
      }else{ 

          foreach ($period as $p) {

              $ot = DB::table('user_ot')->where([ 
                          ['user_ot.biometrics_id',$p->id]
                          ])->join('users','users.id','=','user_ot.user_id')->
                          leftJoin('team','team.user_id','=','user_ot.user_id')->
                          leftJoin('campaign','campaign.id','=','team.campaign_id')->
                          join('biometrics','user_ot.biometrics_id','=','biometrics.id')->
                        select('biometrics.productionDate','user_ot.id as leaveID', 'user_ot.isApproved','user_ot.filed_hours','user_ot.billable_hours', 'user_ot.timeStart','user_ot.timeEnd','user_ot.reason as notes', 'users.employeeCode as accesscode', 'users.id as userID','users.lastname','users.nickname', 'users.firstname','campaign.name as program','campaign.id as programID')->get();

              if (count($ot) > 0) {
                $allOTs->push($ot);
                $total += count($ot);
              }

          }
      
        
    }

   
    
    if ($json)
      return response()->json(['OTs'=>$allOTs,  'total'=>$total, 'name'=>'Overtime', 'cutoffstart'=>$startCutoff,'cutoffend'=>$endCutoff]);
    else
      return $allOTs;

  }

  public function getAllWorksched($c, $json,$regardless)
  {
    $cutoff = explode('_', $c); //return $cutoff;
    $startCutoff = $cutoff[0]; //Biometrics::where('productionDate',$cutoff[0])->first();
    $endCutoff = $cutoff[1]; //Biometrics::where('productionDate',$cutoff[1])->first();
    $period = Biometrics::where('productionDate','>=',$startCutoff)->where('productionDate','<=',$endCutoff)->get();
    //return response()->json(['s'=>$startCutoff,'e'=>$endCutoff]);// $period;
    $hybridSched_WS_fixed = null;$hybridSched_WS_monthly = null;$hybridSched_RD_fixed = null;$hybridSched_RD_monthly=null;$hybridSched=null;

    $allOTs = new Collection;
    $total = 0;
    $keme = new Collection;
    $jpsData = new Collection;
    

    //gawin mo lang to kung need pati kunin regardless kung locked or unlocked
    if($regardless)
    {
      DB::connection()->disableQueryLog();
      $allUsers = DB::table('users')->
                  join('team','team.user_id','=','users.id')->
                  join('campaign','campaign.id','=','team.campaign_id')->
                  leftJoin('immediateHead_Campaigns','team.immediateHead_Campaigns_id','=','immediateHead_Campaigns.id')->
                  leftJoin('immediateHead','immediateHead_Campaigns.immediateHead_id','=','immediateHead.id')->
                  leftJoin('positions','users.position_id','=','positions.id')->
                  leftJoin('floor','team.floor_id','=','floor.id')->
                  select('users.accesscode','users.id', 'users.firstname','users.middlename', 'users.lastname','users.nickname','users.dateHired','positions.name as jobTitle','campaign.id as campID', 'campaign.name as program','immediateHead_Campaigns.id as tlID', 'immediateHead.firstname as leaderFname','immediateHead.lastname as leaderLname','users.employeeNumber','users.employeeCode', 'floor.name as location','floor.id as floorID')->
                  where([
                  ['users.status_id', '!=', 7],
                  ['users.status_id', '!=', 8],
                  ['users.status_id', '!=', 9],
                  ['users.status_id', '!=', 13],
                  ['users.status_id', '!=', 16],
                  ['users.id','!=', 1], //Ben
                  ['users.id','!=', 184], //Henry
                  //['floor.id', '!=', 10], //taipei
                  ['floor.id', '!=', 11], //xiamen
                  ['campaign.hidden',null],
                  ])->orderBy('users.lastname')->get();

      $allEmp = collect($allUsers)->pluck('id')->toArray();
      $allLocked = DB::table('user_dtr')->where([ 
                ['user_dtr.productionDate','>=',$startCutoff],
                ['user_dtr.productionDate','<=',$endCutoff]
                ])->join('users','users.id','=','user_dtr.user_id')->select('user_dtr.productionDate','user_dtr.workshift','users.employeeCode as accesscode', 'users.id as userID','users.lastname','users.firstname')->get();

      
      foreach ($period as $p) 
      {
         $col = new Collection;

          // once na makuha lahat ng ng naglock, we need to check each employees na hindi naglock for each production date
          // *** this was taken out from DTR show
          
          $mgaMeron = collect($allLocked)->where('productionDate',$p->productionDate)->pluck('userID')->toArray();
          $hanapan = array_diff($allEmp, $mgaMeron);
          $bioForTheDay = Biometrics::where('productionDate', $p->productionDate)->first();

          // foreach ($hanapan as $k) {

          //   $exist= User_MustLock::where('user_id',$k)->where('productionDate',$p->productionDate)->get();

          //   if(count($exist) > 0){ }
          //   else
          //   {
          //     $ml = new User_MustLock;
          //     $ml->user_id = $k;
          //     $ml->productionDate = $p->productionDate;
          //     $ml->save();

          //   }
            
          //   # code...
          // }
          
          // foreach ($hanapan as $id) {

                
          //       //$user = User::find($u);
          //       $monthlyScheds = DB::table('monthly_schedules')->where('user_id',$id)->where('productionDate','>=',$startCutoff)->where('productionDate','<=',$endCutoff)->orderBy('created_at','DESC')->get();
          //        // if (count($monthlyScheds) > 0)
          //        // {
          //        //    if ( $user->fixedSchedule->isEmpty() )
          //        //    {

                   
          //        //      /*$workSched = MonthlySchedules::where('user_id',$id)->where('productionDate','>=', $currentPeriod[0])->where('productionDate','<=',$currentPeriod[1])->where('isRD',0)->get(); //Collection::make($monthlySched->where('isRD',0)->all());
          //        //      $RDsched = MonthlySchedules::where('user_id',$id)->where('productionDate','>=', $currentPeriod[0])->where('productionDate','<=',$currentPeriod[1])->where('isRD',1)->get();  //$monthlySched->where('isRD',1)->all();*/

          //        //      $workSched = collect($monthlyScheds)->where('isRD',0);
          //        //      $RDsched = collect($monthlyScheds)->where('isRD',1);
          //        //      $isFixedSched = false;
          //        //      $noWorkSched = false;
          //        //      $hybridSched = false;

          //        //    }else //------------------------- HYBRID SCHED ------------------
          //        //    {

          //        //      $hybridSched = true;
          //        //      $noWorkSched = false;
          //        //      $isFixedSched = false;

                      

          //        //      $hybridSched_WS_fixed = collect($user->fixedSchedule)->sortByDesc('schedEffectivity')->
          //        //                              where('isRD',0)->groupBy('schedEffectivity');
          //        //      $hybridSched_WS_monthly = collect($monthlyScheds)->sortByDesc('created_at')->where('isRD',0);
          //        //      $hybridSched_RD_fixed = collect($user->fixedSchedule)->sortByDesc('schedEffectivity')->
          //        //                              where('isRD',1)->groupBy('schedEffectivity');
          //        //      $hybridSched_RD_monthly = collect($monthlyScheds)->sortByDesc('created_at')->where('isRD',1);

          //        //      $RDsched=null;
          //        //      $workSched=null;

          //        //      /*--- and then compare which is the latest of those 2 scheds --*/


          //        //    }

          //        // } 
          //        // else
          //        // {
          //        //    if (count($user->fixedSchedule) > 0)
          //        //    {
                        

          //        //        $workSched = collect($user->fixedSchedule)->sortByDesc('schedEffectivity')->where('isRD',0)->groupBy('schedEffectivity');
          //        //        $RDsched = collect($user->fixedSchedule)->sortByDesc('schedEffectivity')->where('isRD',1)->groupBy('schedEffectivity');
          //        //        $isFixedSched =true;
          //        //        $noWorkSched = false;
          //        //        $workdays = new Collection;
                        

          //        //    } else
          //        //    {
          //        //        $noWorkSched = true;
          //        //        $workSched = null;
          //        //        $RDsched = null;
          //        //        $isFixedSched = false;
                        
          //        //    }
          //        // }

          //       // $approvedCWS  = User_CWS::where('user_id',$user->id)->where('biometrics_id',$bioForTheDay->id)->where('isApproved',1)->orderBy('updated_at','DESC')->get();


          //       //$actualSchedToday = $this->getActualSchedForToday($user,null,$p->productionDate,null, $hybridSched,$isFixedSched,$hybridSched_WS_fixed,$hybridSched_WS_monthly, $hybridSched_RD_fixed, $hybridSched_RD_monthly, $workSched, $RDsched, $approvedCWS);
          //       $col->push(['id'=>$id,'monthlyScheds'=>$monthlyScheds]);//, 'user'=>$user->lastname.", ".$user->firstname

          //       // $isRDToday = $actualSchedToday->isRDToday;
          //       // $actualSchedToday1 = $actualSchedToday;
          //       // $schedForToday =  $actualSchedToday->schedForToday;

            
            
          // }
          //$keme->push(['mgaMeron'=>$mgaMeron,'lahat'=>$allEmp, 'hanapan'=>count($hanapan), 'productionDate'=>$p->productionDate, 'allUsers'=>$allUsers]);
          // if (count($ot) > 0) {
          //   $allOTs->push($ot);

          //   $total += count($ot);
          // }
          $keme->push(['date'=>$p->productionDate,'meron'=>$mgaMeron, 'hanapan#'=>count($hanapan), 'sino'=>$hanapan]);
          
      }//end foreach



      if ($json)
        return response()->json(['keme'=>$keme, 'total'=>count($hanapan), 'CWS'=>$allLocked, 'all'=>count($allEmp), 'name'=>'Unlocked DTR', 'cutoffstart'=>$startCutoff,'cutoffend'=>$endCutoff]);
      else{
        $jpsData->push(['allEmp'=>$allEmp,'allUsers'=>$allUsers, 'unlocks'=>$keme]);
        return $jpsData;
      }

    }
    else
    {
      foreach ($period as $p) {
         $ot = DB::table('user_dtr')->where([ 
                      ['user_dtr.productionDate',$p->productionDate]
                      ])->join('users','users.id','=','user_dtr.user_id')->
                      
                    select('user_dtr.productionDate','user_dtr.workshift','users.employeeCode as accesscode', 'users.id as userID','users.lastname','users.firstname')->get();
        if (count($ot) > 0) {
          $allOTs->push($ot);
          $total += count($ot);
        }
          
      }
      if ($json)
        return response()->json(['CWS'=>$allOTs, 'total'=>$total, 'name'=>'Work Schedule', 'cutoffstart'=>$startCutoff,'cutoffend'=>$endCutoff]);
      else
        return $allOTs;

    }
    

  }

  public function getAllWorkedHolidays($c,$json)
  {
    $cutoff = explode('_', $c); 
    $startCutoff = $cutoff[0];
    $endCutoff = $cutoff[1];

    $allHolidays = Holiday::where('holidate','>=',$startCutoff)->where('holidate','<=',$endCutoff)->get();

    if (count($allHolidays) > 0)
    {
      $period = Biometrics::where('productionDate','>=',$startCutoff)->where('productionDate','<=',$endCutoff)->get();
      //return response()->json(['s'=>$startCutoff,'e'=>$endCutoff]);// $period;

      $allWorkedHolidays = new Collection;
      $total = 0;

      foreach ($allHolidays as $p) {

         $allops = DB::table('campaign')->where('campaign.isBackoffice',null)->
                      join('team','team.campaign_id','=','campaign.id')->
                      join('users','team.user_id','=','users.id')->
                      
                      where([
                          ['users.status_id', '!=', 6],
                          ['users.status_id', '!=', 7],
                          ['users.status_id', '!=', 8],
                          ['users.status_id', '!=', 9],
                          ['users.status_id', '!=', 13],
                          ['users.status_id', '!=', 16],
                      ])->orderBy('users.lastname')->
                      join('user_dtr','user_dtr.user_id','=','users.id')->
                      where('user_dtr.productionDate',$p->holidate)->
                      select('user_dtr.productionDate','user_dtr.OT_approved as filed_hours','user_dtr.OT_billable','user_dtr.timeIN as timeStart','user_dtr.timeOUT as timeEnd','users.employeeCode as accesscode','users.id as userID','users.lastname', 'users.firstname','campaign.name as program','user_dtr.hoursWorked','user_dtr.workshift','users.status_id')->
                      where('user_dtr.OT_approved','=','0.00')->
                      where([
                          ['user_dtr.timeIN','!=','<strong class="text-danger"> N / A </strong><a tit'],
                          ['user_dtr.timeIN','!=','<strong class="text-danger">No IN</strong><a title'],
                          ['user_dtr.timeIN','!=','LWOP'],
                          ['user_dtr.timeIN','!=','* RD *'],
                          ['user_dtr.timeIN','!=','SL'],
                          ['user_dtr.timeIN','!=','VL'],
                          ['user_dtr.timeIN','!=','ML'],
                          ['user_dtr.timeIN','!=','PL'],
                          ['user_dtr.timeIN','!=','SPL'],
                          ['user_dtr.timeIN','!=','VTO'],
                          ['user_dtr.timeIN','!=','LWOP for approval'],
                          ['user_dtr.timeIN','!=','LWOP denied'],
                          ['user_dtr.timeIN','!=','SL for approval'],
                          ['user_dtr.timeIN','!=','SL denied'],
                          ['user_dtr.timeIN','!=','VL for approval'],
                          ['user_dtr.timeIN','!=','VL denied'],
                          ['user_dtr.timeIN','!=','ML for approval'],
                          ['user_dtr.timeIN','!=','ML denied'],
                          ['user_dtr.timeIN','!=','PL for approval'],
                          ['user_dtr.timeIN','!=','PL denied'],
                          ['user_dtr.timeIN','!=','SPL for approval'],
                          ['user_dtr.timeIN','!=','SPL denied'],
                          ['user_dtr.timeIN','!=','VTO for approval'],
                          ['user_dtr.timeIN','!=','VTO denied']
                        ])->get();

         
        
          $allWorkedHolidays->push($allops);
          $total += count($allops);
        
          
      }

  
      
      if ($json)
        return response()->json(['WorkedHDs'=>$allWorkedHolidays,  'total'=>$total, 'name'=>'Worked Holidays', 'cutoffstart'=>$startCutoff,'cutoffend'=>$endCutoff]);
      else
        return $allWorkedHolidays;

    }
    else
    {
       return $allHolidays;

    }
    

  }

  public function getCWS($from, $to,$type,$user)
  {
    
    $startCutoff = Carbon::parse($from,'Asia/Manila'); 
    $endCutoff = Carbon::parse($to,'Asia/Manila');

    $startCutoffb = Biometrics::where('productionDate',$startCutoff->format('Y-m-d'))->get();
    $endCutoffb = Biometrics::where('productionDate',$endCutoff->format('Y-m-d'))->get();

    $specialChild = DB::table('user_specialPowers')->where('user_specialPowers.user_id',$user->id)->
                          leftJoin('user_specialPowers_programs','user_specialPowers_programs.specialPower_id','=','user_specialPowers.id')->get();
    (count($specialChild) > 0) ? $hasAccess=1 : $hasAccess=0;


    DB::connection()->disableQueryLog();
    if($hasAccess){

            $leaves = DB::table('user_specialPowers')->where('user_specialPowers.user_id',$user->id)->
                          leftJoin('user_specialPowers_programs','user_specialPowers_programs.specialPower_id','=','user_specialPowers.id')->
                          leftJoin('campaign','user_specialPowers_programs.program_id','=','campaign.id')->
                          leftJoin('team','user_specialPowers_programs.program_id','=','team.campaign_id')->
                          leftJoin('users','team.user_id','=','users.id')->
                          leftJoin('user_cws','user_cws.user_id','=','users.id')->
                          leftJoin('biometrics','biometrics.id','=','user_cws.biometrics_id')->
                          select('user_cws.isRD', 'user_cws.id as leaveID','biometrics.productionDate', 'user_cws.biometrics_id', 'user_cws.timeStart','user_cws.timeEnd','user_cws.timeStart_old','user_cws.timeEnd_old','user_cws.isApproved','user_cws.approver', 'user_cws.created_at', 'user_cws.notes','users.employeeCode as accesscode', 'users.id as userID','users.lastname','users.firstname','users.nickname', 'campaign.name as program', 'campaign.id as programID')->
                          where('biometrics.productionDate','<=',$endCutoffb->first()->productionDate)->
                          where('biometrics.productionDate','>=',$startCutoffb->first()->productionDate)->orderBy('user_cws.isApproved','ASC')->get();

    }else{
             $leaves = DB::table('user_cws')->where([ 
                  ['user_cws.biometrics_id','>=', $startCutoffb->first()->id],
                  //['user_vl.leaveEnd','<=', $endCutoff->format('Y-m-d')." 23:59:00"],
                  ])->join('users','users.id','=','user_cws.user_id')->
                  leftJoin('team','team.user_id','=','users.id')->
                  leftJoin('biometrics','biometrics.id','=','user_cws.biometrics_id')->
                  leftJoin('campaign','campaign.id','=','team.campaign_id')->select('user_cws.isRD', 'user_cws.id as leaveID','biometrics.productionDate', 'user_cws.biometrics_id', 'user_cws.timeStart','user_cws.timeEnd','user_cws.timeStart_old','user_cws.timeEnd_old','user_cws.isApproved','user_cws.approver', 'user_cws.created_at', 'user_cws.notes','users.employeeCode as accesscode', 'users.id as userID','users.lastname','users.firstname','users.nickname', 'campaign.name as program', 'campaign.id as programID')->where('user_cws.biometrics_id','<=',$endCutoffb->first()->id)->orderBy('user_cws.isApproved','ASC')->get();


    }
   

         

   
   
    return $leaves;

  }


  public function getLeaveEarnings($from, $to,$type,$emp)
  {
    
    $startCutoff = Carbon::parse($from,'Asia/Manila'); 
    $endCutoff = Carbon::parse($to,'Asia/Manila');


    DB::connection()->disableQueryLog();
    if($type=='VL'){ 
      $db = 'user_vlearnings'; $db_update='vlupdate';
    }
    else{
      $db = 'user_slearnings'; $db_update='slupdate';
    }

    //1 : Regular 0.42
    //2 : Part time 0.21
    //3 : New hires
    //4 : Irregular

    switch ($emp) 
    {
      case '1':
      {

         $earnings = DB::table('users')->where([ 
                      ['users.status_id', '!=', 2],
                      ['users.status_id', '!=', 3],
                      ['users.status_id', '!=', 6],
                      ['users.status_id', '!=', 7],
                      ['users.status_id', '!=', 8],
                      ['users.status_id', '!=', 9],
                      ['users.status_id', '!=', 10],
                      ['users.status_id', '!=', 11],
                      ['users.status_id', '!=', 12],
                      ['users.status_id', '!=', 13],
                      ['users.status_id', '!=', 14],
                      ['users.status_id', '!=', 16],
                      ['users.status_id', '!=', 17]
                    ])->
                    leftJoin($db,$db.'.user_id','=','users.id')->
                    join('team','team.user_id','=','users.id')->
                    join('campaign','team.campaign_id','=','campaign.id')->
                    leftJoin($db_update,$db_update.'.id','=',$db.'.'.$db_update.'_id')->
                    where($db_update.'.period','>=',$startCutoff->format('Y-m-d'))->
                    select('campaign.name as program', 'users.id as userID', 'users.lastname','users.firstname',$db.'.id',$db.'.'.$db_update.'_id',$db_update.'.period',$db_update.'.credits')->
                    where($db_update.'.period','<=',$endCutoff->format('Y-m-d'))->orderBy('users.lastname')->get();

         $people = collect($earnings)->groupBy('userID');
         $periods = collect($earnings)->pluck('period')->unique();

         $months = [];
         $month_updates = new Collection;


         //we create a collection for all months along with earnings
         foreach ($periods as $key) 
         {

            $m = Carbon::parse($key,'Asia/Manila');
            
            if (!in_array($m->format('M Y'), $months))
            { 
              array_push($months, $m->format('M Y'));
              $m1 =Carbon::parse($m,'Asia/Manila')->startOfMonth();
              $m2 =Carbon::parse($m,'Asia/Manila')->endOfMonth();

              $d = DB::select( DB::raw("SELECT ".$db_update.".id, ".$db_update.".credits,DATE_FORMAT(".$db_update.".period, '%Y-%m-%d')as datePeriod FROM ".$db_update." WHERE MONTH(".$db_update.".period) = :m AND DAY(".$db_update.".period) >= :d AND DAY(".$db_update.".period) <= :dt  ORDER BY ".$db_update.".period ASC"), array(
                       'm' => $m->format('m'),
                       'd' => $m1->format('d'),
                       'dt' =>$m2->format('d')
                     )); 

              //$peopleEarns = collect($earnings)->where('vlupdate_id', collect($d)->pluck('id')->flatten()); //collect($earnings)->where('vlupdate_id',,'people_earns'=>$peopleEarns 


              $month_updates->push(['month'=>$m->format('M Y'),'updateIDs'=>$d]);
              
            }
         }

        
        

      }break;

      
      
     
    }

    //return $people_earns;
    
    return (['periods'=>$periods,'months'=>$months, 'month_updates'=>$month_updates, 'people'=>$people, 'type'=>$type]);

   

  }

  

  public function getLeaves($from, $to,$type,$user)
  {
    
    $startCutoff = Carbon::parse($from,'Asia/Manila'); 
    $endCutoff = Carbon::parse($to,'Asia/Manila');

    $specialChild = DB::table('user_specialPowers')->where('user_specialPowers.user_id',$user->id)->
                          leftJoin('user_specialPowers_programs','user_specialPowers_programs.specialPower_id','=','user_specialPowers.id')->get();
    (count($specialChild) > 0) ? $hasAccess=1 : $hasAccess=0;


    DB::connection()->disableQueryLog();

    switch ($type) 
    {
      case 'VL':
      {
         if($hasAccess){

                $leaves = DB::table('user_specialPowers')->where('user_specialPowers.user_id',$user->id)->
                          leftJoin('user_specialPowers_programs','user_specialPowers_programs.specialPower_id','=','user_specialPowers.id')->
                          leftJoin('campaign','user_specialPowers_programs.program_id','=','campaign.id')->
                          leftJoin('team','user_specialPowers_programs.program_id','=','team.campaign_id')->
                          leftJoin('users','team.user_id','=','users.id')->
                          leftJoin('user_vl','user_vl.user_id','=','users.id')->
                          select('user_vl.id as leaveID','user_vl.productionDate', 'user_vl.leaveStart','user_vl.leaveEnd','user_vl.isApproved','user_vl.totalCredits','user_vl.halfdayFrom','user_vl.halfdayTo', 'user_vl.created_at', 'user_vl.notes','user_vl.forced', 'users.employeeCode as accesscode', 'users.id as userID','users.lastname','users.firstname','users.nickname', 'campaign.name as program', 'campaign.id as programID')->
                          where([ 
                                  ['user_vl.leaveStart','>=', $startCutoff->format('Y-m-d')." 00:00:00"],
                                  //['user_vl.leaveEnd','<=', $endCutoff->format('Y-m-d')." 23:59:00"],
                                  ])->get();

                /*$leaves = DB::table('user_vl')->where([ 
                  ['user_vl.leaveStart','>=', $startCutoff->format('Y-m-d')." 00:00:00"],
                  //['user_vl.leaveEnd','<=', $endCutoff->format('Y-m-d')." 23:59:00"],
                  ])->join('users','users.id','=','user_vl.user_id')->
                  leftJoin('team','team.user_id','=','users.id')->
                  leftJoin('campaign','campaign.id','=','team.campaign_id')->select('user_vl.id as leaveID','user_vl.productionDate', 'user_vl.leaveStart','user_vl.leaveEnd','user_vl.isApproved','user_vl.totalCredits','user_vl.halfdayFrom','user_vl.halfdayTo', 'user_vl.created_at', 'user_vl.notes','users.employeeCode as accesscode', 'users.id as userID','users.lastname','users.firstname','users.nickname', 'campaign.name as program', 'campaign.id as programID')->where('user_vl.leaveStart','<=',$endCutoff->format('Y-m-d')." 23:59:00")->orderBy('user_vl.isApproved','ASC')->
                  where('team.campaign_id','=',$specialChild[0]->program_id)->get();*/

         }else{
                $leaves = DB::table('user_vl')->where([ 
                  ['user_vl.leaveStart','>=', $startCutoff->format('Y-m-d')." 00:00:00"],
                  //['user_vl.leaveEnd','<=', $endCutoff->format('Y-m-d')." 23:59:00"],
                  ])->join('users','users.id','=','user_vl.user_id')->
                  leftJoin('team','team.user_id','=','users.id')->
                  leftJoin('campaign','campaign.id','=','team.campaign_id')->select('user_vl.id as leaveID','user_vl.productionDate', 'user_vl.leaveStart','user_vl.leaveEnd','user_vl.isApproved','user_vl.totalCredits','user_vl.halfdayFrom','user_vl.halfdayTo', 'user_vl.created_at', 'user_vl.notes','user_vl.forced', 'users.employeeCode as accesscode', 'users.id as userID','users.lastname','users.firstname','users.nickname', 'campaign.name as program', 'campaign.id as programID')->where('user_vl.leaveStart','<=',$endCutoff->format('Y-m-d')." 23:59:00")->orderBy('user_vl.isApproved','ASC')->get();
         }
          

         $pending_VL = count(collect($leaves)->where('isApproved',null));

         $pvt = $this->getPendings('VTO',$startCutoff,$endCutoff);
         $pending_VTO = count(collect($pvt)->where('isApproved',null));

         //get other pendings
         $psl = $this->getPendings('SL',$startCutoff,$endCutoff);
         $pending_SL = count(collect($psl)->where('isApproved',null));

         $plwop = $this->getPendings('LWOP',$startCutoff,$endCutoff);
         $pending_LWOP = count(collect($plwop)->where('isApproved',null));

         $pfl = $this->getPendings('FL',$startCutoff,$endCutoff);
         $pending_FL = count(collect($pfl)->where('isApproved',null));

      }break;

      case 'VTO':
      {
         if($hasAccess){

                $leaves = DB::table('user_specialPowers')->where('user_specialPowers.user_id',$user->id)->
                              leftJoin('user_specialPowers_programs','user_specialPowers_programs.specialPower_id','=','user_specialPowers.id')->
                              leftJoin('campaign','user_specialPowers_programs.program_id','=','campaign.id')->
                              leftJoin('team','user_specialPowers_programs.program_id','=','team.campaign_id')->
                              leftJoin('users','team.user_id','=','users.id')->
                              leftJoin('user_vto','user_vto.user_id','=','users.id')->
                              select('user_vto.id as leaveID','user_vto.productionDate', 'user_vto.startTime as leaveStart','user_vto.endTime as leaveEnd','user_vto.isApproved','user_vto.totalHours as totalCredits', 'user_vto.created_at', 'user_vto.notes','user_vto.forced','users.employeeCode as accesscode', 'users.id as userID','users.lastname','users.firstname','users.nickname', 'campaign.name as program', 'campaign.id as programID','user_vto.deductFrom')->where('user_vto.productionDate','<=',$endCutoff->format('Y-m-d'))->orderBy('user_vto.isApproved','ASC')->get();
         }else{

                $leaves = DB::table('user_vto')->where([ 
                  ['user_vto.productionDate','>=', $startCutoff->format('Y-m-d')],
                  //['user_vl.leaveEnd','<=', $endCutoff->format('Y-m-d')." 23:59:00"],
                  ])->join('users','users.id','=','user_vto.user_id')->
                  leftJoin('team','team.user_id','=','users.id')->
                  leftJoin('campaign','campaign.id','=','team.campaign_id')->select('user_vto.id as leaveID','user_vto.productionDate', 'user_vto.startTime as leaveStart','user_vto.endTime as leaveEnd','user_vto.isApproved','user_vto.totalHours as totalCredits', 'user_vto.created_at', 'user_vto.notes','user_vto.forced', 'users.employeeCode as accesscode', 'users.id as userID','users.lastname','users.firstname','users.nickname', 'campaign.name as program', 'campaign.id as programID','user_vto.deductFrom')->where('user_vto.productionDate','<=',$endCutoff->format('Y-m-d'))->orderBy('user_vto.isApproved','ASC')->get();

         }
          

         $pending_VTO = count(collect($leaves)->where('isApproved',null));

         $pvl = $this->getPendings('VL',$startCutoff,$endCutoff);
         $pending_VL = count(collect($pvl)->where('isApproved',null));

         //get other pendings
         $psl = $this->getPendings('SL',$startCutoff,$endCutoff);
         $pending_SL = count(collect($psl)->where('isApproved',null));

         $plwop = $this->getPendings('LWOP',$startCutoff,$endCutoff);
         $pending_LWOP = count(collect($plwop)->where('isApproved',null));

         $pfl = $this->getPendings('FL',$startCutoff,$endCutoff);
         $pending_FL = count(collect($pfl)->where('isApproved',null));

      }break;

      case 'SL':
      {
          if($hasAccess){
                  $leaves = DB::table('user_specialPowers')->where('user_specialPowers.user_id',$user->id)->
                          leftJoin('user_specialPowers_programs','user_specialPowers_programs.specialPower_id','=','user_specialPowers.id')->
                          leftJoin('campaign','user_specialPowers_programs.program_id','=','campaign.id')->
                          leftJoin('team','user_specialPowers_programs.program_id','=','team.campaign_id')->
                          leftJoin('users','team.user_id','=','users.id')->
                          leftJoin('user_sl','user_sl.user_id','=','users.id')->
                          select('user_sl.id as leaveID','user_sl.productionDate', 'user_sl.leaveStart','user_sl.leaveEnd','user_sl.isApproved','user_sl.totalCredits','user_sl.attachments', 'user_sl.halfdayFrom','user_sl.halfdayTo', 'user_sl.created_at', 'user_sl.notes','user_sl.forced', 'users.employeeCode as accesscode', 'users.id as userID','users.lastname','users.firstname','users.nickname', 'campaign.name as program', 'campaign.id as programID')->where('user_sl.leaveStart','<=',$endCutoff->format('Y-m-d')." 23:59:00")->
                          where('user_sl.leaveStart','>=', $startCutoff->format('Y-m-d')." 00:00:00")->orderBy('user_sl.isApproved','ASC')->get();

          }else{

                    $leaves = DB::table('user_sl')->where([ 
                    ['user_sl.leaveStart','>=', $startCutoff->format('Y-m-d')." 00:00:00"],
                    //['user_vl.leaveEnd','<=', $endCutoff->format('Y-m-d')." 23:59:00"],
                    ])->join('users','users.id','=','user_sl.user_id')->
                    leftJoin('team','team.user_id','=','users.id')->
                    leftJoin('campaign','campaign.id','=','team.campaign_id')->
                    select('user_sl.id as leaveID','user_sl.productionDate', 'user_sl.leaveStart','user_sl.leaveEnd','user_sl.isApproved','user_sl.totalCredits','user_sl.halfdayFrom','user_sl.halfdayTo', 'user_sl.created_at', 'user_sl.notes','user_sl.forced', 'users.employeeCode as accesscode', 'users.nickname', 'users.id as userID','users.lastname','users.firstname','campaign.name as program', 'campaign.id as programID')->where('user_sl.leaveStart','<=',$endCutoff->format('Y-m-d')." 23:59:00")->orderBy('user_sl.isApproved','ASC')->get();

          }
          
        

          $pending_SL = count(collect($leaves)->where('isApproved',null));

         //get other pendings
         $pvl = $this->getPendings('VL',$startCutoff,$endCutoff);
         $pending_VL = count(collect($pvl)->where('isApproved',null));

         //get other pendings
         $pvt = $this->getPendings('VTO',$startCutoff,$endCutoff);
         $pending_VTO = count(collect($pvt)->where('isApproved',null));

         $plwop = $this->getPendings('LWOP',$startCutoff,$endCutoff);
         $pending_LWOP = count(collect($plwop)->where('isApproved',null));

         $pfl = $this->getPendings('FL',$startCutoff,$endCutoff);
         $pending_FL = count(collect($pfl)->where('isApproved',null));

      }break;

      case 'LWOP':
      {
        if($hasAccess){

              $leaves = DB::table('user_specialPowers')->where('user_specialPowers.user_id',$user->id)->
                              leftJoin('user_specialPowers_programs','user_specialPowers_programs.specialPower_id','=','user_specialPowers.id')->
                              leftJoin('campaign','user_specialPowers_programs.program_id','=','campaign.id')->
                              leftJoin('team','user_specialPowers_programs.program_id','=','team.campaign_id')->
                              leftJoin('users','team.user_id','=','users.id')->
                              leftJoin('user_lwop','user_lwop.user_id','=','users.id')->
                              select('user_lwop.id as leaveID','user_lwop.productionDate', 'user_lwop.leaveStart','user_lwop.leaveEnd','user_lwop.isApproved','user_lwop.totalCredits','user_lwop.halfdayFrom','user_lwop.halfdayTo', 'user_lwop.created_at', 'user_lwop.notes','user_lwop.forced', 'users.employeeCode as accesscode', 'users.id as userID','users.lastname','users.firstname','users.nickname', 'campaign.name as program', 'campaign.id as programID')->where('user_lwop.leaveStart','<=',$endCutoff->format('Y-m-d')." 23:59:00")->
                              where('user_lwop.leaveStart','>=', $startCutoff->format('Y-m-d')." 00:00:00")->orderBy('user_lwop.isApproved','ASC')->get();


        }else{

                $leaves = DB::table('user_lwop')->where([ 
                        ['user_lwop.leaveStart','>=', $startCutoff->format('Y-m-d')." 00:00:00"],
                        //['user_vl.leaveEnd','<=', $endCutoff->format('Y-m-d')." 23:59:00"],
                        ])->join('users','users.id','=','user_lwop.user_id')->
                        leftJoin('team','team.user_id','=','users.id')->
                        leftJoin('campaign','campaign.id','=','team.campaign_id')->select('user_lwop.id as leaveID','user_lwop.productionDate', 'user_lwop.leaveStart','user_lwop.leaveEnd','user_lwop.isApproved','user_lwop.totalCredits','user_lwop.halfdayFrom','user_lwop.halfdayTo', 'user_lwop.created_at', 'user_lwop.notes','user_lwop.forced', 'users.employeeCode as accesscode', 'users.id as userID','users.nickname', 'users.lastname','users.firstname','campaign.name as program', 'campaign.id as programID')->where('user_lwop.leaveStart','<=',$endCutoff->format('Y-m-d')." 23:59:00")->orderBy('user_lwop.isApproved','ASC')->get();

        }
        


        $pending_LWOP = count(collect($leaves)->where('isApproved',null));
        //get other pendings
        $pvl = $this->getPendings('VL',$startCutoff,$endCutoff);
        $pending_VL = count(collect($pvl)->where('isApproved',null));

        $pvt = $this->getPendings('VTO',$startCutoff,$endCutoff);
        $pending_VTO = count(collect($pvt)->where('isApproved',null));

        $psl = $this->getPendings('SL',$startCutoff,$endCutoff);
        $pending_SL = count(collect($psl)->where('isApproved',null));

        $pfl = $this->getPendings('FL',$startCutoff,$endCutoff);
        $pending_FL = count(collect($pfl)->where('isApproved',null));


      }break;

      case 'FL':
      {
        if($hasAccess){
          $leaves = DB::table('user_specialPowers')->where('user_specialPowers.user_id',$user->id)->
                              leftJoin('user_specialPowers_programs','user_specialPowers_programs.specialPower_id','=','user_specialPowers.id')->
                              leftJoin('campaign','user_specialPowers_programs.program_id','=','campaign.id')->
                              leftJoin('team','user_specialPowers_programs.program_id','=','team.campaign_id')->
                              leftJoin('users','team.user_id','=','users.id')->
                              leftJoin('user_familyleaves','user_familyleaves.user_id','=','users.id')->
                              select('user_familyleaves.id as leaveID','user_familyleaves.productionDate', 'user_familyleaves.leaveStart','user_familyleaves.leaveEnd','user_familyleaves.isApproved','user_familyleaves.attachments', 'user_familyleaves.totalCredits','user_familyleaves.halfdayFrom','user_familyleaves.halfdayTo', 'user_familyleaves.created_at', 'user_familyleaves.notes','user_familyleaves.leaveType as FLtype','users.employeeCode as accesscode', 'users.id as userID','users.lastname','users.firstname','users.nickname', 'campaign.name as program', 'campaign.id as programID')->where('user_familyleaves.leaveStart','<=',$endCutoff->format('Y-m-d')." 23:59:00")->orderBy('user_familyleaves.isApproved','ASC')->get();

        }else{
          $leaves = DB::table('user_familyleaves')->where([ 
                  ['user_familyleaves.leaveStart','>=', $startCutoff->format('Y-m-d')." 00:00:00"],
                  //['user_vl.leaveEnd','<=', $endCutoff->format('Y-m-d')." 23:59:00"],
                  ])->join('users','users.id','=','user_familyleaves.user_id')->
                  leftJoin('team','team.user_id','=','users.id')->
                  leftJoin('campaign','campaign.id','=','team.campaign_id')->select('user_familyleaves.id as leaveID','user_familyleaves.productionDate', 'user_familyleaves.leaveStart','user_familyleaves.leaveEnd','user_familyleaves.isApproved','user_familyleaves.totalCredits','user_familyleaves.halfdayFrom','user_familyleaves.halfdayTo', 'user_familyleaves.created_at', 'user_familyleaves.notes','user_familyleaves.attachments', 'users.employeeCode as accesscode','users.nickname',  'users.id as userID','users.lastname','users.firstname','campaign.name as program','user_familyleaves.leaveType as FLtype', 'campaign.id as programID')->where('user_familyleaves.leaveStart','<=',$endCutoff->format('Y-m-d')." 23:59:00")->orderBy('user_familyleaves.isApproved','ASC')->get();

        }
        
        $pending_FL = count(collect($leaves)->where('isApproved',null));

        $psl = $this->getPendings('SL',$startCutoff,$endCutoff);
        $pending_SL = count(collect($psl)->where('isApproved',null));

        $plwop = $this->getPendings('LWOP',$startCutoff,$endCutoff);
        $pending_LWOP = count(collect($plwop)->where('isApproved',null));
        
        $pvl = $this->getPendings('VL',$startCutoff,$endCutoff);
        $pending_VL = count(collect($pvl)->where('isApproved',null));

        $pvt = $this->getPendings('VTO',$startCutoff,$endCutoff);
        $pending_VTO = count(collect($pvt)->where('isApproved',null));

      }break;
      
     
    }

   
    return ['leaves'=>$leaves,'pending_VL'=>$pending_VL, 'pending_SL'=>$pending_SL,'pending_LWOP'=>$pending_LWOP,'pending_FL'=>$pending_FL,'pending_VTO'=>$pending_VTO];

  }

  public function getPendings($type,$startCutoff,$endCutoff)
  {

    switch ($type) {
      case 'VL': $t = 'user_vl'; break;
      case 'VTO': $t = 'user_vto'; break;
      case 'SL': $t = 'user_sl'; break;
      case 'LWOP': $t = 'user_lwop'; break;
      case 'FL': $t = 'user_familyleaves'; break;

    }

    if($type=='VTO')
    {
      $leaves = DB::table($t)->where([ 
                  [$t.'.productionDate','>=', $startCutoff->format('Y-m-d')],
                  ])->join('users','users.id','=',$t.'.user_id')->
                  leftJoin('team','team.user_id','=','users.id')->
                  leftJoin('campaign','campaign.id','=','team.campaign_id')->select($t.'.startTime as leaveStart',$t.'.endTime as leaveEnd',$t.'.isApproved',$t.'.totalHours as totalCredits', $t.'.created_at', $t.'.notes','users.employeeCode as accesscode', 'users.id as userID','users.lastname','users.firstname','campaign.name as program')->where($t.'.productionDate','<=',$endCutoff->format('Y-m-d'))->get();

    }else{
      $leaves = DB::table($t)->where([ 
                  [$t.'.leaveStart','>=', $startCutoff->format('Y-m-d')." 00:00:00"],
                  //['user_vl.leaveEnd','<=', $endCutoff->format('Y-m-d')." 23:59:00"],
                  ])->join('users','users.id','=',$t.'.user_id')->
                  leftJoin('team','team.user_id','=','users.id')->
                  leftJoin('campaign','campaign.id','=','team.campaign_id')->select($t.'.leaveStart',$t.'.leaveEnd',$t.'.isApproved',$t.'.totalCredits',$t.'.halfdayFrom',$t.'.halfdayTo', $t.'.created_at', $t.'.notes','users.employeeCode as accesscode', 'users.id as userID','users.lastname','users.firstname','campaign.name as program')->where($t.'.leaveStart','<=',$endCutoff->format('Y-m-d')." 23:59:00")->get();

    }

    
    return $leaves;

  }

  

  

  

  

  

  public function getComplicatedWorkedHours($user_id, $userLogIN, $userLogOUT, $schedForToday,$shiftEnd,$isRDYest,$payday)
  {

    $data = new Collection;
    $chenes ="";
    $checkLate="init";
    $UT = 0; $actualIN=null; $actualOUT=null;$todayStart=null;$todayEnd=null;$isEarlyOUT=null;$isLateIN=null;

    $billableForOT=0; $endshift = Carbon::parse($shiftEnd); $diff = null; $OTattribute="";
    $campName = User::find($user_id)->campaign->first()->name;

    $hasHolidayToday = false;
    $thisPayrollDate = Biometrics::where('productionDate',$payday)->first();
    $holidayToday = Holiday::where('holidate', $payday)->get();

    $hasLWOP = null; $lwopDetails = new Collection; $hasPendingLWOP=false;
    $hasVL = null; $vlDetails = new Collection; $hasPendingVL=false;
    $hasSL = null; $slDetails = new Collection; $hasPendingSL=false;
    $hasOBT = null; $obtDetails = new Collection; $hasPendingOBT=false;
    $hasFL = null; $flDetails = new Collection; $hasPendingFL=false;

    $theDay = Carbon::parse($payday." ".$schedForToday['timeStart'],"Asia/Manila");
    /*$fix= Carbon::parse($payday." 23:59:00","Asia/Manila");*/
    // SINCE IT'S A COMPLICATED SCHED, MAKE THE STARTING POINT UP TILL END OF SHIFT
    $fix= Carbon::parse($payday." ".$schedForToday['timeStart'],"Asia/Manila")->addHours(9);
    $shiftStart = Carbon::parse($payday." ".$schedForToday['timeStart'],"Asia/Manila");





    /*------ WE CHECK FIRST IF THERE'S AN APPROVED VL | SL | LWOP -----*/
    

    $vl = User_VL::where('user_id',$user_id)->where('leaveStart','<=',$fix->format('Y-m-d H:i:s'))->where('leaveEnd','>=',$theDay->format('Y-m-d H:i:s'))->orderBy('created_at','DESC')->get();

    $sl = User_SL::where('user_id',$user_id)->where('leaveStart','<=',$fix->format('Y-m-d H:i:s'))->where('leaveEnd','>=',$theDay->format('Y-m-d H:i:s'))->orderBy('created_at','DESC')->get();

    $lwop = User_LWOP::where('user_id',$user_id)->where('leaveStart','<=',$fix->format('Y-m-d H:i:s'))->where('leaveEnd','>=',$theDay->format('Y-m-d H:i:s'))->orderBy('created_at','DESC')->get();

    $obt = User_OBT::where('user_id',$user_id)->where('leaveStart','<=',$fix->format('Y-m-d H:i:s'))->where('leaveEnd','>=',$theDay->format('Y-m-d H:i:s'))->orderBy('created_at','DESC')->get();

    $fl = User_Familyleave::where('user_id',$user_id)->where('leaveStart','<=',$fix->format('Y-m-d H:i:s'))->where('leaveEnd','>=',$theDay->format('Y-m-d H:i:s'))->orderBy('created_at','DESC')->get();




    /*-------- LEAVE WITHOUT PAY -----------*/
    if (count($lwop) > 0) 
    {
      $hasLWOP=true;
      $lwopDeet= $lwop->first();
      (!is_null($lwopDeet->isApproved)) ? $hasPendingLWOP=false : $hasPendingLWOP=true;

    }else{

      $hasLWOP = false;
      $lwopDeet = null;

    }

    /*-------- VACATION LEAVE  -----------*/
    if (count($vl) > 0) 
    {
      $hasVL=true;
      $vlDeet= $vl->first();
      (!is_null($vlDeet->isApproved)) ? $hasPendingVL=false : $hasPendingVL=true;

    }else{

      $hasVL = false;
      $vlDeet = null;

    }

    /*-------- OBT LEAVE  -----------*/
    if (count($obt) > 0) 
    {
      $hasOBT=true;
      $obtDeet= $obt->first();
      (!is_null($obtDeet->isApproved)) ? $hasPendingOBT=false : $hasPendingOBT=true;

    }else{

      $hasOBT = false;
      $obtDeet = null;

    }


     /*-------- SICK LEAVE  -----------*/
    if (count($sl) > 0) 
    {
      $hasSL=true;
      $slDeet= $sl->first();
      (!is_null($slDeet->isApproved)) ? $hasPendingSL=false : $hasPendingSL=true;

    }else{

      $hasSL = false;
      $slDeet = null;

    }


     /*-------- Family LEAVE  -----------*/
    if (count($fl) > 0) 
    {
      $hasFL=true;
      $flDeet= $fl->first();
      (!is_null($flDeet->isApproved)) ? $hasPendingFL=false : $hasPendingFL=true;

    }else{

      $hasFL = false;
      $flDeet = null;

    }




     $link = action('LogsController@viewRawBiometricsData',$user_id);
     $icons = "<a title=\"Verify Biometrics data\" class=\"pull-right text-gray\" target=\"_blank\" style=\"font-size:1.2em;\" href=\"$link#$thisPayrollDate->id\"><i class=\"fa fa-clock-o\"></i></a>";

    if (count($holidayToday) > 0) $hasHolidayToday = true;


        if (count($userLogIN[0]['logs']) > 0 && count($userLogOUT[0]['logs']) > 0)
        {
          //---- To get the right Worked Hours, check kung early pasok == get schedule Time
          //---- if late pumasok, get user timeIN
         
          //************ CHECK FOR LATEIN AND EARLY OUT ***************//

          if ($isRDYest)
          {
            $getBioID = $userLogOUT[0]['logs']->sortByDesc('created_at');
            $gbID = Biometrics::find($getBioID->first()->biometrics_id);
            $todayStart = Carbon::parse($payday." ".$schedForToday['timeStart'],"Asia/Manila"); //->format('Y-m-d H:i:s');
            $todayEnd = Carbon::parse($payday." ".$schedForToday['timeEnd'],"Asia/Manila")->addDay(); //->format('Y-m-d H:i:s');
            $actualIN = Carbon::parse($userLogOUT[0]['timing'],"Asia/Manila"); //->format('Y-m-d H:i:s');
            $actualOUT = Carbon::parse($userLogOUT[0]['timing'],"Asia/Manila"); //->format('Y-m-d H:i:s');

            if ($actualIN > $todayStart && $actualIN < $todayEnd) //late in == UNDERTIME
            {
              $checkLate = $actualIN->diffInMinutes($todayStart);
              
               //---- MARKETING TEAM CHECK: 15mins grace period
              
              
                 if ($checkLate > 2) $isLateIN = true; else $isLateIN= false;
              

            } else {$isLateIN=false;$checkLate = $gbID->productionDate."| ". $actualIN->format('Y-m-d H:i:s')." > ". $todayStart->format('Y-m-d H:i:s')." && ". $todayEnd->format('Y-m-d H:i:s');}


            if ($actualOUT > $todayStart && $actualOUT < $todayEnd) // EARLY OUT
            {
              $checkEarlyOut = $actualOUT->diffInMinutes($todayEnd);

               //---- MARKETING TEAM CHECK: 15mins grace period
              
                 if ($checkEarlyOut > 2) $isEarlyOUT = true; else $isEarlyOUT= false;
              

              
            } else $isEarlyOUT=false;

         
          

            if ($isEarlyOUT && $isLateIN)//use user's logs
            {
              $chenes ="both";

              $wh = $actualOUT->diffInMinutes($actualIN->addHour());
              $workedHours = number_format($wh/60,2);
              $billableForOT=0;
               if ($hasHolidayToday)
                  {
                    $workedHours .= "<br/> <strong>* ". $holidayToday->first()->name. " *</strong>";
                  }
              

            }
            else if ($isEarlyOUT){
              $wh = $actualOUT->diffInMinutes($todayStart->addHour());
              $workedHours = number_format($wh/60,2)."<br/><small>(early OUT)</small>";$billableForOT=0;
               if ($hasHolidayToday)
                  {
                    $workedHours .= "<br/> <strong>* ". $holidayToday->first()->name. " *</strong>";
                  }
            }
            else if ($isLateIN){
              $wh = $actualOUT->diffInMinutes($actualIN->addHour());
              $workedHours = number_format($wh/60,2)."<br/><small>(Late IN)</small>";$billableForOT=0;
               if ($hasHolidayToday)
                  {
                    $workedHours .= "<br/> <strong>* ". $holidayToday->first()->name. " *</strong>";
                  }
            }
            else {

               $wh = $actualOUT->diffInMinutes($todayStart->addHour());
                $out = Carbon::parse($userLogOUT[0]['timing'],"Asia/Manila")->format('H:i:s');
               $out2 = Carbon::parse($out);


              if ($wh > 480)
              {
                $workedHours =8.00; 
                //check first if Locked na DTR for that production date
                $verifiedDTR = User_DTR::where('productionDate',$payday)->where('user_id',$user_id)->get();
                if (count($verifiedDTR) > 0)
                  $icons = "<a title=\"Unlock DTR to file this OT\" class=\"pull-right text-gray\" style=\"font-size:1.2em;\"><i class=\"fa fa-credit-card\"></i></a>";
                else
                 $icons = "<a id=\"OT_".$payday."\"  data-toggle=\"modal\" data-target=\"#myModal_OT".$payday."\"  title=\"File this OT\" class=\"pull-right\" style=\"font-size:1.2em;\" href=\"#\"><i class=\"fa fa-credit-card\"></i></a>";

                
                $totalbill = number_format(($endshift->diffInMinutes($out2))/60,2);

                if ($totalbill > 0.5)
                {
                  $billableForOT = $totalbill; $OTattribute=$icons;
                }
                  
                else { $billableForOT = 0; $OTattribute="&nbsp;&nbsp;&nbsp;"; } 

                if ($hasHolidayToday)
                          {
                            $workedHours .= "<br/> <strong>* ". $holidayToday->first()->name. " *</strong>";
                          }

              } 
              else 
                { 
                  $workedHours = number_format($wh/60,2); $billableForOT=0; 
                   if ($hasHolidayToday)
                  {
                    $workedHours .= "<br/> <strong>* ". $holidayToday->first()->name. " *</strong>";
                  }
                }

            }





          }
          else
          {
            /* -------- We need to check for the schedule first for complicated shifts -----*/
             if ($schedForToday['timeStart'] == "00:00:00"){

                //$actualIN = Carbon::parse($payday." ".$userLogIN[0]['timing']->format('H:i:s'),"Asia/Manila"); 
                $actualIN = Carbon::parse($userLogIN[0]['timing']->format('Y-m-d H:i:s'),"Asia/Manila"); 
                $todayStart = Carbon::parse($payday." ".$schedForToday['timeStart'],"Asia/Manila")->addDay();
                $todayEnd = Carbon::parse($payday." ".$schedForToday['timeEnd'],"Asia/Manila")->addDay(); 
                //$actualOUT = Carbon::parse($payday." ".$userLogOUT[0]['timing']->format('H:i:s'),"Asia/Manila")->addDay(); 
                $actualOUT = Carbon::parse($userLogOUT[0]['timing']->format('Y-m-d H:i:s'),"Asia/Manila"); 
             } else{
                //$actualIN = Carbon::parse($payday." ".$userLogIN[0]['timing']->format('H:i:s'),"Asia/Manila"); 
                $actualIN = Carbon::parse($userLogIN[0]['timing']->format('Y-m-d H:i:s'),"Asia/Manila"); 
                $todayStart = Carbon::parse($payday." ".$schedForToday['timeStart'],"Asia/Manila");
                $todayEnd = Carbon::parse($payday." ".$schedForToday['timeEnd'],"Asia/Manila"); 

                if (empty($userLogOUT[0]['timing']))
                  $actualOUT = Carbon::parse($userLogOUT[0]['logs']->first()->format('Y-m-d H:i:s'),"Asia/Manila"); 
                else
                  $actualOUT = Carbon::parse($userLogOUT[0]['timing']->format('Y-m-d H:i:s'),"Asia/Manila"); 
                //$actualOUT = Carbon::parse($payday." ".$userLogOUT[0]['timing']->format('H:i:s'),"Asia/Manila"); 

             }



            //*** --- check if late time in and less than or equal to out
            if ($userLogIN[0]['timing']->format('H:i:s') > $schedForToday['timeStart']) // && $userLogIN[0]['timing']->format('H:i:s') <= $schedForToday['timeEnd'] )
            {

              if ($schedForToday['timeStart'] == "00:00:00")
                $checkLate = $actualIN->diffInHours($todayStart);//diffInHours(Carbon::parse($payday." 24:00:00", "Asia/Manila"));// $actualIN->diffInHours($todayStart);
             else 
              $checkLate = $todayStart->diffInHours($actualIN);

              //---- MARKETING TEAM CHECK: 15mins grace period
              
              
                 if ($checkLate >= 1.5) $isLateIN = true; else $isLateIN= false;
              
             

            }else {$isLateIN= false;}

            ( $actualOUT->format('H:i:s') < $schedForToday['timeEnd'] ) ? $isEarlyOUT = true : $isEarlyOUT= false;

          

            if ($isEarlyOUT && $isLateIN)//use user's logs
            {

              //$wh = Carbon::parse($userLogOUT[0]['timing'],"Asia/Manila")->diffInMinutes(Carbon::parse($userLogOUT[0]['timing'],"Asia/Manila")->addHour());
              $wh = $actualIN->addHour()->diffInMinutes($actualOUT);
              $workedHours = number_format($wh/60,2);
              $billableForOT=0;
              $UT = abs(number_format($wh/60,2) - 8.0);
               
            }
            else if ($isEarlyOUT){

              if ($schedForToday['timeStart'] == "00:00:00")
                {
                  //$wh =  Carbon::parse( $schedForToday['timeStart'],"Asia/Manila")->diffInMinutes($actualOUT);
                  $wh = $todayStart->addHour()->diffInMinutes($actualOUT);
                  $UT = abs(number_format($wh/60,2) - 8.0);
                  $workedHours = number_format($wh/60,2)."<br/><small>(early OUT)</small>".$icons."<br/>";$billableForOT=0;

                }
              else
               {

                $wh = $todayStart->addHour()->diffInMinutes($actualOUT);
                  $workedHours = number_format($wh/60,2)."<br/><small>(early OUT**)</small>". $icons;
                  $billableForOT=0;
                  $UT = abs(number_format($wh/60,2) - 8.0);
               
               }

            }
            else if ($isLateIN){
              $wh = $todayEnd->addHour()->diffInMinutes($actualIN);

              $workedHours = number_format($wh/60,2)."<br/><small>(Late IN) </small>".$icons;$billableForOT=0;
              
              $UT = abs(number_format($wh/60,2) - 8.0);
            }
            else 
            {
                $wh = $todayStart->diffInMinutes($actualOUT); //$wh = Carbon::parse($userLogOUT[0]['timing'],"Asia/Manila")->diffInMinutes(Carbon::parse($schedForToday['timeStart'],"Asia/Manila")->addHour());
              
               $out = Carbon::parse($userLogOUT[0]['timing'],"Asia/Manila")->format('H:i:s');
               $out2 = Carbon::parse($out);

                if ($wh > 480)
                { 
                  $workedHours =8.00; 

                  //check first if Locked na DTR for that production date
                  $verifiedDTR = User_DTR::where('productionDate',$payday)->where('user_id',$user_id)->get();
                  if (count($verifiedDTR) > 0)
                    $icons = "<a title=\"Unlock DTR to file this OT\" class=\"pull-right text-gray\" style=\"font-size:1.2em;\"><i class=\"fa fa-credit-card\"></i></a>";
                  else
                   $icons = "<a id=\"OT_".$payday."\"  data-toggle=\"modal\" data-target=\"#myModal_OT".$payday."\"  title=\"File this OT\" class=\"pull-right\" style=\"font-size:1.2em;\" href=\"#\"><i class=\"fa fa-credit-card\"></i></a>";

                  
                  $totalbill = number_format(($endshift->diffInMinutes($out2))/60,2);

                  if ($totalbill > 0.5)
                  {
                    $billableForOT = $totalbill; $OTattribute=$icons;
                  }  else { $billableForOT = 0;  $OTattribute="&nbsp;&nbsp;&nbsp;";} 

                   if ($hasHolidayToday)
                    {
                      $workedHours .= "<br/> <strong>* ". $holidayToday->first()->name. " *</strong>";
                    }

                } //number_format(($endshift->diffInMinutes($out2))/60,2);}
                else 
                  { $workedHours = number_format($wh/60,2); $billableForOT=0; 
                     if ($hasHolidayToday)
                    {
                      $workedHours .= "<br/> <strong>* ". $holidayToday->first()->name. " *</strong>";
                    }
                }
                 //$UT = number_format($wh/60,2) - 8.0;
              

            }


          }//end not RD yesterday



          if ($hasLWOP)
          {

                $link = action('UserController@myRequests',$user_id);
                $icons = "<a title=\"LWOP request\" class=\"pull-right text-primary\" target=\"_blank\" style=\"font-size:1em;\" href=\"$link\"><i class=\"fa fa-info-circle\"></i></a>";

                
                if ($lwopDeet->totalCredits >= '1.0'){
                  $log="<strong>* <small><em>Leave Without Pay </em></small></strong>".$icons;
                  //$workedHours = 8.0;
                  $workedHours .= "<br/>".$log;

                } 
                else if ($lwopDeet->totalCredits == '0.50'){
                        
                          $log="<strong>* <small><em>Half-day LWOP</em></small></strong> ".$icons;
                          //if (count($userLogIN[0]['logs']) < 1 && count($userLogOUT[0]['logs']) < 1) $workedHours = 4.0;
                          //else $workedHours = 8.0;

                          $workedHours .= "<br/>".$log;
                        
                }

          }//end if has LWOP

          
          
         
        } //end if not empty logs
        
        else
        {
          $WHcounter = 8.0;
          $link = action('UserController@myRequests',$user_id);
          $icons ="";
          $workedHours=null;$log="";
          $wh = null;
          //$wh = Carbon::parse($userLogOUT[0]['timing'],"Asia/Manila")->diffInMinutes(Carbon::parse($schedForToday['timeStart'],"Asia/Manila")->addHour());

          if ($hasVL)
          {
            $workedHours1 = $this->processLeaves($theDay->format('Y-m-d'),'VL',true,$wh,$vlDeet,$hasPendingVL,$icons,$userLogIN[0],$userLogOUT[0],$shiftEnd,$shiftStart);
            $workedHours .= $workedHours1[0]['workedHours'];
            $UT = $workedHours1[0]['UT'];
          }


          if ($hasOBT)
          {
            $workedHours1 = $this->processLeaves($theDay->format('Y-m-d'),'OBT',true,$wh,$obtDeet,$hasPendingOBT,$icons,$userLogIN[0],$userLogOUT[0],$shiftEnd,$shiftStart);
            $workedHours .= $workedHours1[0]['workedHours'];
            $UT = $workedHours1[0]['UT'];
          }



          if ($hasSL)
          {
            $workedHours1 = $this->processLeaves($theDay->format('Y-m-d'),'SL',true,$wh,$slDeet,$hasPendingSL,$icons,$userLogIN[0],$userLogOUT[0],$shiftEnd,$shiftStart);
            $workedHours .= $workedHours1[0]['workedHours'];
            $UT = $workedHours1[0]['UT'];
          }


          if ($hasFL)
          {
            $workedHours1 = $this->processLeaves($theDay->format('Y-m-d'),'FL',false,0,$flDeet,$hasPendingFL,$icons,$userLogIN[0],$userLogOUT[0],$shiftEnd,$shiftStart);
              $workedHours .= $workedHours1[0]['workedHours'];
              $UT = $workedHours1[0]['UT'];
          }


          if ($hasLWOP)
          {
            $workedHours1 = $this->processLeaves($theDay->format('Y-m-d'),'LWOP',true,$wh,$lwopDeet,$hasPendingLWOP,$icons,$userLogIN[0],$userLogOUT[0],$shiftEnd,$shiftStart);
            $workedHours .= $workedHours1[0]['workedHours'];
            $UT = $workedHours1[0]['UT'];

          }

          


          if ($hasHolidayToday) /***--- we will need to check if Non-Ops personnel, may pasok kasi pag OPS **/
          {
            $workedHours .= "(8.0)<br/> <strong>* " . $holidayToday->first()->name . " *</strong>";
          }

         if (!$hasVL && !$hasSL && !$hasLWOP && !$hasFL && !$hasHolidayToday && !$hasOBT){
            $workedHours = "<a title=\"Check your Biometrics data. \n It's possible that you pressed a wrong button, the machine malfunctioned, or you really didn't log in / out.\"><strong class=\"text-danger\">AWOL </strong></a>";
         }
        }

        $data->push(['checkLate'=>$checkLate,'isLateIN'=>$isLateIN,  'payday'=>$payday, 
          'workedHours'=>$workedHours, 'billableForOT'=>$billableForOT, 
          'OTattribute'=>$OTattribute ,'UT'=>$UT,
         'actualIN'=>$actualIN, 'actualOUT'=>$actualOUT, 
         'todayStart'=>$todayStart,'todayEnd'=>$todayEnd,
         'isEarlyOUT'=>$isEarlyOUT,'isLateIN'=>$isLateIN,
         'VL'=>$vl,'LWOP'=>$lwop
          // 'diffIN'=>$actualIN->diffInHours($actualOUT),
          ]);

        return $data;


  }



  public function getCutoffStartEnd()
  {
    $currPeriod =  Cutoff::first()->getCurrentPeriod();

    $currentPeriod = explode('_', $currPeriod);
    

    $cutoffStart = Carbon::parse($currentPeriod[0]." 00:00:00",'Asia/Manila'); //(Cutoff::first()->startingPeriod());
    $cutoffEnd = Carbon::parse($currentPeriod[1]." 00:00:00",'Asia/Manila'); //(Cutoff::first()->endingPeriod());
    //$cutoffID = Paycutoff::where('fromDate',$currentPeriod[0])->first()->id;

    $cID = Paycutoff::where('fromDate',$currentPeriod[0])->get();
    if ($cID->isEmpty())
    {
      //return $cID;
      $newPC = new Paycutoff;
      $newPC->fromDate = $cutoffStart;
      $newPC->toDate = $cutoffEnd;
      $newPC->save();

      $cutoffID = $newPC->id;

      

    } else
    {
      $cutoffID = $cID->first()->id;

    }

    return collect(['currPeriod'=>$currPeriod, 'currentPeriod'=>$currentPeriod, 'cutoffStart'=>$cutoffStart,'cutoffEnd'=>$cutoffEnd,'cutoffID'=>$cutoffID]);
  }


  public function getDTRPs($from,$to,$type,$user)
  {
    $f = Biometrics::where('productionDate',date('Y-m-d', strtotime($from)))->get();
    $t = Biometrics::where('productionDate',date('Y-m-d', strtotime($to)))->get();

    $specialChild = DB::table('user_specialPowers')->where('user_specialPowers.user_id',$user->id)->
                          leftJoin('user_specialPowers_programs','user_specialPowers_programs.specialPower_id','=','user_specialPowers.id')->get();
    (count($specialChild) > 0) ? $hasAccess=1 : $hasAccess=0;

    switch ($type) {
      case 'IN':{
                  $b = Carbon::parse($from,'Asia/Manila')->addDay(-1);
                  $b2 = Carbon::parse($to,'Asia/Manila');
                  $all = DB::table('user_dtrp')->where('user_dtrp.actualLogdate','>=',$b->format('Y-m-d'))->
                  where('user_dtrp.actualLogdate','<=',$b2->format('Y-m-d'))->
                  join('user_dtrpInfo','user_dtrpInfo.dtrp_id','=','user_dtrp.id')->
                  join('biometrics','biometrics.id','=','user_dtrp.biometrics_id')->
                  leftJoin('logType','user_dtrp.logType_id','=','logType.id')->
                  leftJoin('user_dtrpReasons','user_dtrpInfo.reasonID','=','user_dtrpReasons.id')->
                  leftJoin('users','user_dtrp.user_id','=','users.id')->
                  leftJoin('team','team.user_id','=','users.id')->
                  leftJoin('campaign','campaign.id','=','team.campaign_id')->

                  select('user_dtrp.actualLogdate','user_dtrp.id','user_dtrpInfo.id as infoID', 'user_dtrp.biometrics_id','user_dtrp.user_id','user_dtrp.notes','user_dtrp.logTime','user_dtrp.logType_id','user_dtrp.isApproved','user_dtrp.approvedBy','users.nickname', 'users.firstname','users.lastname','campaign.name as program','campaign.id as programID', 'user_dtrpInfo.attachments','user_dtrpReasons.name as reason','user_dtrpInfo.reasonID','user_dtrpInfo.clearedBy','user_dtrpInfo.created_at','biometrics.productionDate','user_dtrpInfo.isCleared as validated','user_dtrp.isApproved','user_dtrp.reviewed','logType.name as dtrpType')->
                  where('user_dtrp.logType_id','1')->get();

        }break;

      case 'OUT':{
                    $b = Carbon::parse($from,'Asia/Manila')->addDay(-1);
                    $b2 = Carbon::parse($to,'Asia/Manila');
                    $all = DB::table('user_dtrp')->where('user_dtrp.actualLogdate','>=',$b->format('Y-m-d'))->
                    //where('user_dtrp.actualLogdate','<=',$b2->format('Y-m-d'))->
                    join('user_dtrpInfo','user_dtrpInfo.dtrp_id','=','user_dtrp.id')->
                    join('biometrics','biometrics.id','=','user_dtrp.biometrics_id')->
                    leftJoin('logType','user_dtrp.logType_id','=','logType.id')->
                    leftJoin('user_dtrpReasons','user_dtrpInfo.reasonID','=','user_dtrpReasons.id')->
                    leftJoin('users','user_dtrp.user_id','=','users.id')->
                    leftJoin('team','team.user_id','=','users.id')->
                    leftJoin('campaign','campaign.id','=','team.campaign_id')->
                    select('user_dtrp.actualLogdate','user_dtrp.id','user_dtrpInfo.id as infoID', 'user_dtrp.biometrics_id','user_dtrp.user_id','user_dtrp.notes','user_dtrp.logTime','user_dtrp.logType_id','user_dtrp.isApproved','user_dtrp.approvedBy','users.nickname','users.firstname','users.lastname','campaign.name as program','campaign.id as programID','user_dtrpInfo.attachments','user_dtrpReasons.name as reason','user_dtrpInfo.reasonID','user_dtrpInfo.clearedBy','user_dtrpInfo.created_at','biometrics.productionDate','user_dtrpInfo.isCleared as validated','user_dtrp.isApproved','user_dtrp.reviewed','logType.name as dtrpType')->
                    where('user_dtrp.logType_id','2')->get();

      }
        # code...
        break;

      case 'OLD': {

                    if($hasAccess){


                          $all = DB::table('user_specialPowers')->where('user_specialPowers.user_id',$user->id)->
                          leftJoin('user_specialPowers_programs','user_specialPowers_programs.specialPower_id','=','user_specialPowers.id')->
                          leftJoin('campaign','user_specialPowers_programs.program_id','=','campaign.id')->
                          leftJoin('team','user_specialPowers_programs.program_id','=','team.campaign_id')->
                          leftJoin('users','team.user_id','=','users.id')->
                          leftJoin('user_dtrp','user_dtrp.user_id','=','users.id')->
                          leftJoin('biometrics','biometrics.id','=','user_dtrp.biometrics_id')->
                          leftJoin('logType','user_dtrp.logType_id','=','logType.id')->
                          select('user_dtrp.actualLogdate','user_dtrp.id','user_dtrp.biometrics_id','users.employeeCode', 'user_dtrp.user_id','user_dtrp.notes','user_dtrp.logTime','user_dtrp.logType_id','user_dtrp.isApproved','user_dtrp.approvedBy','users.nickname','users.firstname','users.lastname','campaign.name as program','campaign.id as programID','biometrics.productionDate','user_dtrp.isApproved','user_dtrp.reviewed','user_dtrp.updated_at', 'team.immediateHead_Campaigns_id as ihID','logType.name as dtrpType')->
                          where('biometrics.productionDate','<=',$t->first()->productionDate)->
                          where('biometrics.productionDate','>=',$f->first()->productionDate)->orderBy('user_dtrp.isApproved','ASC')->get();//

                    }else{

                          $all = DB::table('user_dtrp')->where('user_dtrp.biometrics_id','>=',$f->first()->id)->
                          where('user_dtrp.biometrics_id','<=',$t->first()->id)->
                          join('biometrics','biometrics.id','=','user_dtrp.biometrics_id')->
                          leftJoin('logType','user_dtrp.logType_id','=','logType.id')->
                          leftJoin('users','user_dtrp.user_id','=','users.id')->
                          leftJoin('team','team.user_id','=','users.id')->
                          leftJoin('campaign','campaign.id','=','team.campaign_id')->
                          select('user_dtrp.actualLogdate','user_dtrp.id','user_dtrp.biometrics_id','users.employeeCode', 'user_dtrp.user_id','user_dtrp.notes','user_dtrp.logTime','user_dtrp.logType_id','user_dtrp.isApproved','user_dtrp.approvedBy','users.nickname','users.firstname','users.lastname','campaign.name as program','campaign.id as programID','biometrics.productionDate','user_dtrp.isApproved','user_dtrp.reviewed','user_dtrp.updated_at','team.immediateHead_Campaigns_id as ihID','logType.name as dtrpType')->get();

                    }
                    
      }
        # code...
        break;
      
      default:
        # code...
        break;
    }

    
    return $all;

  }

  public function getFixedSchedules($startingPoint,$RDsched,$workSched,$coll,$counter) //USED FORR DTR
  {
        $dt  = $startingPoint->dayOfWeek;
        $data = new Collection;
        switch($dt){
          case 0: $dayToday = 6; break;
          case 1: $dayToday = 0; break;
          default: $dayToday = $dt-1;
        } 

        //-- we need to adjust: sun 0->6 | 1->0 | 2->1 | 3->2 |4->3 | 5->4 | 6->5
        if (in_array($dayToday, $RDsched->toArray()))
        {
          $coll->push(['title'=>'Rest day ',
                            'start'=>$startingPoint->format('Y-m-d'), // dates->format('Y-m-d H:i:s'),
                            'textColor'=> '#ccc',
                            'backgroundColor'=> '#fff',
                            'chenes'=>$startingPoint->format('Y-m-d'),
                            'dayToday'=>$dayToday ]);

        } else
        {
          $time1 = $workSched->where('workday',$dayToday)->sortByDesc('created_at')->first(); //->timeStart;
         
          $coll->push(['title'=> date('h:i A', strtotime($time1['timeStart'])) . " to ",// '09:00 AM ',
                                'start'=>$startingPoint->format('Y-m-d') . " ". $time1['timeStart'], //->format('Y-m-d H:i:s'),
                                'textColor'=> '#548807', //'#26577b',// '#409c45',
                                'backgroundColor'=> '#fff',
                              'chenes'=>$startingPoint->format('Y-m-d'),
                            'dayToday'=>$dayToday ]);
                        $coll->push(['title'=>date('h:i A', strtotime($time1['timeEnd'])),
                                  'start'=>$startingPoint->format('Y-m-d') . " ". $time1['timeEnd'],
                                  'textColor'=> '#bd3310', //'#0d2e46',// '#27a7f7',
                                  'backgroundColor'=> '#fff',
                                'chenes'=>$startingPoint->format('Y-m-d')]);

        }

        return $coll;
  }

  public function getFixedSchedules2($sched, $productionDate, $coll,$counter) //used for CALENDAR WS
  {

    //check first if may approved CWS
    $prodDate = Carbon::parse($productionDate, "Asia/Manila");

    if ($prodDate->isPast() && !$prodDate->isToday()) {
      $bgcolor = "#e6e6e6";
      $border = "#e6e6e6";
      $startColor = "#7b898e"; $endColor = "#7b898e";
    } else {
      $bgcolor="#fff"; $border="#fff";$startColor = "#548807"; $endColor = "#bd3310";
    }

    $bio = Biometrics::where('productionDate',$productionDate)->get();
    if (count($bio)>0){
      $cws = User_CWS::where('biometrics_id',$bio->first()->id)->where('user_id',$sched->user_id)->where('isApproved',1)->orderBy('updated_at','DESC')->get();

          if (count($cws)> 0){

            //check mo muna kung alin mas recent between the sched and cws
            if ($cws->first()->created_at > $sched->created_at){
              //check mo muna kung RD

              if ($cws->first()->timeStart == $cws->first()->timeEnd)
              {
                 //means 00:00:00 to 00:00:00

                 $coll->push(['title'=>'Rest day ',
                            'start'=>$productionDate . " 00:00:00", // dates->format('Y-m-d H:i:s'),
                            'end'=>$productionDate . " 00:00:00",
                            'textColor'=> '#ccc',
                            'backgroundColor'=> '#fff',
                            'chenes'=>$productionDate,
                            'biometrics_id'=> $bio->first()->id
                             ]);
                 $coll->push(['title'=>'.',
                                'start'=>$productionDate . " 00:00:00", // dates->format('Y-m-d H:i:s'),
                                'textColor'=> '#fff',
                                'borderColor'=>$border,
                                'backgroundColor'=> '#e6e6e6',
                                'chenes'=>$productionDate,'icon2'=>"bed", 'biometrics_id'=>null
                                 ]);

              }
              else {

                 $correctTime = Carbon::parse($productionDate . " ". $cws->first()->timeStart,"Asia/Manila");


                $coll->push(['title'=> date('h:i A', strtotime($cws->first()->timeStart)) . " to ",// '09:00 AM ',
                          'start'=>$productionDate . " ". $cws->first()->timeStart, //. $sched->timeStart, //->format('Y-m-d H:i:s'),
                          //'end' =>$productionDate . " ". $cws->first()->timeEnd,
                          'end'=>$correctTime->addHour(9)->format('Y-m-d H:i:s'),
                          'textColor'=>  $startColor,// '#548807',// '#409c45',
                          'backgroundColor'=> $bgcolor,
                          //'borderColor'=>"#fff",
                        'chenes'=>$productionDate,
                        'counter'=>$counter,'icon'=>"play-circle", 'biometrics_id'=> $bio->first()->id]);
                 $coll->push(['title'=>date('h:i A', strtotime($cws->first()->timeEnd)),
                                      'start'=>$productionDate . " ". $cws->first()->timeStart, //. $sched->timeEnd,
                                      'end'=>$correctTime->format('Y-m-d H:i:s'),
                                      'textColor'=> $endColor,// '#bd3310',// '#27a7f7',
                                      'backgroundColor'=> $bgcolor,
                                      //'borderColor'=>"#fff",
                                    'chenes'=>$productionDate,
                                    'counter'=>$counter+1,'icon'=>"stop-circle", 'biometrics_id'=> $bio->first()->id]);

              }

            } else{
              // else, get the sched not the cws
              goto proceedToSchedules;
            }
          } 
          else {
            //else, get the sched
            goto proceedToSchedules;
          }
    } 
    else 
    {
      //get the sched since no biometrics

      // but check first which of the plotted schedule is more updated
      
      proceedToSchedules:

        if ($sched->isRD){
           $coll->push(['title'=>'Rest day ',
                                'start'=>$productionDate . " 00:00:00", // dates->format('Y-m-d H:i:s'),
                                'end'=>$productionDate . " 00:00:00",
                                'textColor'=> '#ccc',
                                'backgroundColor'=> '#fff',
                                'chenes'=>$productionDate,'icon'=>" ", 'biometrics_id'=>null
                                 ]);
           $coll->push(['title'=>'.',
                                'start'=>$productionDate . " 00:00:00", // dates->format('Y-m-d H:i:s'),
                                'end'=>$productionDate . " 00:00:00",
                                'textColor'=> '#fff',
                                'borderColor'=>$border,
                                'backgroundColor'=> '#e6e6e6',
                                'chenes'=>$productionDate,'icon2'=>"bed", 'biometrics_id'=>null
                                 ]);


        }
        else {

          $correctTime = Carbon::parse($productionDate  ." ".$sched->timeStart,"Asia/Manila");

         $coll->push(['title'=> date('h:i A', strtotime($sched->timeStart)) . " to ",// '09:00 AM ',
                              'start'=>$productionDate  ." ".$sched->timeStart, //. $sched->timeStart, //->format('Y-m-d H:i:s'),
                              //'end'=>$productionDate ." ".$sched->timeEnd,
                              'end'=>$correctTime->addHour(9)->format('Y-m-d H:i:s'),
                              'textColor'=>  $startColor,// '#409c45',
                              'backgroundColor'=> $bgcolor,'borderColor'=>$border,
                            'chenes'=>$productionDate,
                            'counter'=>$counter,'icon'=>"play-circle", 'biometrics_id'=>null]);
         $coll->push(['title'=>date('h:i A', strtotime($sched->timeEnd)),
                                'start'=>$productionDate  ." ".$sched->timeStart, //. $sched->timeEnd,
                                //'end'=>$productionDate ." ".$sched->timeEnd,
                                 'end'=>$correctTime->format('Y-m-d H:i:s'),
                                'textColor'=> $endColor,// '#27a7f7',
                                'backgroundColor'=> $bgcolor,'borderColor'=>$border,
                              'chenes'=>$productionDate,
                              'counter'=>$counter+1,'icon'=>"stop-circle", 'biometrics_id'=>null]);

                             


        }
    }

    return $coll;

  }

  public function getHybrid_MonthlyWS($id, $currentPeriod)
  {
    $coll = new Collection;

    $workSched = MonthlySchedules::where('user_id',$id)->where('productionDate','>=', $currentPeriod[0])->where('productionDate','<=',$currentPeriod[1])->where('isRD',0)->orderBy('id','DESC')->get(); 
    $RDsched = MonthlySchedules::where('user_id',$id)->where('productionDate','>=', $currentPeriod[0])->where('productionDate','<=',$currentPeriod[1])->where('isRD',1)->get();
    $isFixedSched = false;
    $noWorkSched = false;

    $coll->push(['workSched'=> $workSched, 'RDsched'=>$RDsched, 'isFixedSched'=> $isFixedSched, 'noWorkSched'=>$noWorkSched]);
    return $coll;
  }

  public function getHybrid_FixedWS($user)
  {
    $coll = new Collection;

    $workSched = $user->fixedSchedule->where('isRD',0)->sortByDesc('updated_at');
    $RDsched = $user->fixedSchedule->where('isRD',1)->sortByDesc('updated_at')->pluck('workday');
    $isFixedSched =true;
    $noWorkSched = false;

    $coll->push(['workSched'=>$workSched, 'RDsched'=>$RDsched, 'isFixedSched'=>$isFixedSched, 'noWorkSched'=>$noWorkSched]);
    return $coll;

  }

  public function getLatestFixedSched($user,$numDay,$payday)
  {
    /*-- we need to get all instances of fixed sched and check their effectivity dates --*/
     //$schedForToday = $workSched->where('workday',$numDay)->first();
    $allFixedWS = FixedSchedules::where('user_id',$user->id)->where('workday',$numDay)->orderby('created_at','DESC')->get();

    //(count($allFixedWS) > 0) ?  $schedForToday = $allFixedWS->first() : $schedForToday = null;
    $ct = 0;
    
    foreach ($allFixedWS as $key) 
    {
        if( $allFixedWS[$ct]->schedEffectivity <= $payday || $allFixedWS[$ct]->schedEffectivity==null )
        {
          
          $schedForToday1 = $allFixedWS[$ct];
          break;

        } else {$ct++; $schedForToday1 = $allFixedWS->first(); } 
    }
    
    if (count($allFixedWS)==0) $schedForToday1 = ['timeStart'=>null, 'timeEnd'=>null,'isFlexitime'=>false ];
    $schedForToday = $schedForToday1;

    //return $schedForToday;
    return $schedForToday;


  }

  public function getLatestFixedSchedGrouped($workSched,$payday,$numDay)
  {
    $thesched = null;

    if (  count((array)$workSched) > 0)
      foreach ($workSched as $w) {
      
        if( $w->first()->schedEffectivity <= $payday || is_null($w->first()->schedEffectivity))
        {
          $thesched = collect($w)->where('workday',$numDay)->first();
          break;
        }
      }

    if (is_null($thesched))
    {
      //$sched = ['timeStart'=>null, 'timeEnd'=>'00:00:00','isFlexitime'=>false,'isRD'=>false, 'workday'=>null,'created_at'=>null ];
      $sched = ['timeStart'=>null, 'timeEnd'=>'null','isFlexitime'=>false,'isRD'=>true, 'workday'=>null,'created_at'=>null,'schedEffectivity'=>null ];
      // *** null meaning either wala talga or di pa effective yung sched

    } else $sched = $thesched;

    return $sched;




  }




  public function getLogDetails($type, $id, $biometrics_id, $logType_id, $schedForToday, $undertime, $problemArea, $isAproblemShift, $isRDYest,$schedKahapon, $isBackoffice)
  {


    $data = new Collection;
    $dtrpIN = null;
    $dtrpIN_id = null;
    $dtrpOUT_id = null;
    $dtrpOUT = null;
    $hasHolidayToday = false;
    $thisPayrollDate = Biometrics::find($biometrics_id)->productionDate;
    $holidayToday = Holiday::where('holidate', $thisPayrollDate)->get();
    $hasPendingDTRP = null; 
    $hasLeave = null; $leaveDetails = new Collection; $hasPendingLeave=null;
    $hasLWOP = null; $lwopDetails = new Collection; $hasPendingLWOP=false;
    $hasOBT = null; $obtDetails = new Collection; $hasPendingOBT=false;
    $hasVTO = null; $vtoDetails = new Collection; $hasPendingVTO=false;
    $hasFL = null; $hasVL=null; $hasSL=null; $flDetails = new Collection; $hasPendingFL=false;
    $pendingDTRP = null; 
    $UT=null;$log=null;$timing=null; $pal = null;$maxIn=null;$beginShift=null; $finishShift=null;
    $logPalugit=null;
    $palugitDate=null;$maxOut=null; $checker=null;$theNextday=null;
    //$userLog=null;

    
    $theDay = Carbon::parse($thisPayrollDate." ".$schedForToday['timeStart'],"Asia/Manila");
    $fix= Carbon::parse($thisPayrollDate." 23:59:00","Asia/Manila");

    $employee = User::find($id);

    ($employee->status_id == 12 || $employee->status_id == 14 ) ? $isPartTimer = true : $isPartTimer=false;

    //$beginShift = Carbon::parse($thisPayrollDate." ".$schedForToday['timeStart'],"Asia/Manila")->format('Y-m-d H:i:s');

    //*** check mo muna kung exempt employee
    $isExempt = null;
    $exemptEmp = DB::table('user_schedType')->where('user_id',$id)->join('schedType','schedType.id','=','user_schedType.schedType_id')->orderBy('user_schedType.created_at','DESC')->get();
    if (count($exemptEmp) > 0)
    {
      //$workSchedule = $
      $isExempt=1;
      
    }

    if($isExempt)
      {
        $u = Logs::where('user_id',$id)->where('biometrics_id',$biometrics_id)->where('logType_id','1')->orderBy('biometrics_id','ASC')->get();
        if(count($u) > 0)
        {
          $beginShift = Carbon::parse($thisPayrollDate." ".$u->first()->logTime,"Asia/Manila");
          $endShift =  Carbon::parse($thisPayrollDate." ".$u->first()->logTime,"Asia/Manila")->addHour(9);

        }
        else{
          $beginShift = Carbon::parse($thisPayrollDate." ".$schedForToday['timeStart'],"Asia/Manila");
          $endShift = Carbon::parse($thisPayrollDate." ".$schedForToday['timeStart'],"Asia/Manila")->addHour(9);

        }
        
      }
    else{
      $beginShift = Carbon::parse($thisPayrollDate." ".$schedForToday['timeStart'],"Asia/Manila");
      ($isPartTimer) ? $endShift = Carbon::parse($thisPayrollDate." ".$schedForToday['timeStart'],"Asia/Manila")->addHour(5) : $endShift = Carbon::parse($thisPayrollDate." ".$schedForToday['timeStart'],"Asia/Manila")->addHour(9);

    } 

    

    $tommorow = Carbon::parse($thisPayrollDate)->addDay();



    $alldays = [];$alldaysLWOP=[]; $alldaysFL=[]; $alldaysVL=[]; $alldaysSL=[]; $alldaysVTO=[]; $col =[];$fl=[];

 

    $vacationLeave = $this->establishLeaves($id,$endShift,'VL',$thisPayrollDate,$schedForToday);
    $vl = $vacationLeave->leaveType;
    $alldaysVL = $vacationLeave->allDays;
    $hasVL = $vacationLeave->hasTheLeave; 
    $hasLeave = $vacationLeave->hasLeave; 
    $vlDeet = $vacationLeave->details;
    $hasPendingVL = $vacationLeave->hasPending;


    $vtoff = $this->establishLeaves($id,$endShift,'VTO',$thisPayrollDate,$schedForToday);
    $vto = $vtoff->leaveType;
    $alldaysVTO = $vtoff->allDays;
    $hasVTO = $vtoff->hasTheLeave; 
    $vtoDeet = $vtoff->details;
    $hasPendingVTO = $vtoff->hasPending;



    $sickleave = $this->establishLeaves($id,$endShift,'SL',$thisPayrollDate,$schedForToday);
    $sl = $sickleave->leaveType;
    $alldaysSL = $sickleave->allDays;
    $hasSL = $sickleave->hasTheLeave; 
    //$hasLeave = $sickleave->hasLeave; 
    $slDeet = $sickleave->details;
    $hasPendingSL = $sickleave->hasPending;

    /*-------- LEAVE WITHOUT PAY -----------*/
    $noPay = $this->establishLeaves($id,$endShift,'LWOP',$thisPayrollDate,$schedForToday);
    $lwop = $noPay->leaveType;
    $alldaysLWOP = $noPay->allDays;
    $hasLWOP = $noPay->hasTheLeave; 
    //$hasLeave = $sickleave->hasLeave; 
    $lwopDeet = $noPay->details;
    $hasPendingLWOP = $noPay->hasPending;


    /*-------- OBT -----------*/
    $ob = $this->establishLeaves($id,$endShift,'OBT',$thisPayrollDate,$schedForToday);
    $obt = $ob->leaveType;
    $alldaysOBT = $ob->allDays;
    $hasOBT = $ob->hasTheLeave;  
    $obtDeet = $ob->details;
    $hasPendingOBT = $ob->hasPending;


     /*-------- OBT LEAVE  -----------*/
    // if (count($obt) > 0) 
    // {
    //   $hasOBT=true; $hasLeave = true; 
    //   $obtDeet= $obt->first();
    //   (!is_null($obtDeet->isApproved)) ? $hasPendingOBT=false : $hasPendingOBT=true;

    // }else{

    //   $hasOBT = false;
    //   $obtDeet = null;

    // }





    // $obt = User_OBT::where('user_id',$id)->where('leaveEnd','<=',$endShift->format('Y-m-d H:i:s'))->where('leaveStart','>=',$beginShift->format('Y-m-d H:i:s'))->orderBy('created_at','DESC')->get();



  
    $famL = User_Familyleave::where('user_id',$id)->where('leaveStart','<=',$endShift->format('Y-m-d H:i:s'))->orderBy('created_at','DESC')->get();
   
    if (count($famL) > 0)
    {
      //************* gawin mo to foreach family leave ************//
      foreach ($famL as $familyleave) 
      {
         
        $f_dayS = Carbon::parse($familyleave->leaveStart,'Asia/Manila');
        $f_dayE = Carbon::parse($familyleave->leaveEnd,'Asia/Manila');
        $full_leave = Carbon::parse($familyleave->leaveEnd,'Asia/Manila')->addDays($familyleave->totalCredits)->addDays(-1);
        $fend = $f_dayE->format('Y-m-d');

        $cf = $familyleave->totalCredits; // $f_dayS->diffInDays($f_dayE)+1;
        $cf2 = 1;

        if ($familyleave->totalCredits <= 1)
        {
            array_push($alldaysFL, $f_dayS->format('Y-m-d'));
            

        }else
        {
          while( $cf2 <= $cf){
          
            array_push($alldaysFL, $f_dayS->format('Y-m-d'));
            $f_dayS->addDays(1);
            $cf2++;
          }

        }
        
        array_push($col, ['pasok alldaysFL'=>$alldaysFL, 'thisPayrollDate'=>$thisPayrollDate]);

        //$flcol->push(['payday'=>$payday, 'full_leave'=>$full_leave]);

        if(in_array($thisPayrollDate, $alldaysFL) ) {

          $fl = $familyleave; 
          $hasFL=true; $hasLeave=true;
          $flDeet= $familyleave;

          (!is_null($flDeet->isApproved)) ? $hasPendingFL=false : $hasPendingFL=true;

          break(1);
        }
        
      }

      array_push($col, ['fl'=>$fl]);
      
    }else 
    {
      $fl=[];
      $hasFL = false; //$hasLeave=false;
      $flDeet = null;
    }
   

    $davao = Team::where('user_id',$id)->where('floor_id',9)->get();
    (count($davao) > 0) ? $isDavao = 1 : $isDavao=0;

    (count(Team::where('user_id',$id)->where('floor_id',10)->get()) > 0) ? $isTaipei = 1 : $isTaipei=0;
    (count(Team::where('user_id',$id)->where('floor_id',11)->get()) > 0) ? $isXiamen = 1 : $isXiamen=0;


    if (count($holidayToday) > 0){

      
      if($holidayToday->first()->holidayType_id == 4) // Davao
      {
          ($isDavao) ? $hasHolidayToday = 1 : $hasHolidayToday = 0;

      }elseif($holidayToday->first()->holidayType_id == 5) // Taipei
      {
          ($isTaipei) ? $hasHolidayToday = 1 : $hasHolidayToday = 0;

      }elseif($holidayToday->first()->holidayType_id == 6) // Xiamen
      {
          ($isXiamen) ? $hasHolidayToday = 1 : $hasHolidayToday = 0;

      }else{ $hasHolidayToday = 1; }

    } else $hasHolidayToday = 0;




    $hasApprovedDTRP1 = User_DTRP::where('user_id',$id)->where('isApproved',true)->where('biometrics_id',$biometrics_id)->where('logType_id',$logType_id)->orderBy('updated_at','DESC')->get();

    $pendingDTRP = User_DTRP::where('user_id',$id)->where('biometrics_id',$biometrics_id)->where('logType_id',$logType_id)->where('isApproved',null)->orderBy('id','DESC')->get();

    //***** but not yet, we reworked DTRP so check mo muna kung may pending DTRPinfo to be screened by Data Mgt:
    if (count($hasApprovedDTRP1) > 0)
    {
        $dtrpForValidation = User_DTRPinfo::where('dtrp_id',$hasApprovedDTRP1->first()->id)->get();
        if (count($dtrpForValidation) > 0)
        {
          if($dtrpForValidation->first()->isCleared)
            $hasApprovedDTRP = $hasApprovedDTRP1;
          elseif (is_null($dtrpForValidation->first()->isCleared)){
            $pendingDTRP = $hasApprovedDTRP1;
            //$hasApprovedDTRP = null;
            $hasApprovedDTRP = User_DTRP::where('user_id',1)->get();
          }
          else{
            //$hasApprovedDTRP = null;
             $hasApprovedDTRP = User_DTRP::where('user_id',1)->get();
             $pendingDTRP = User_DTRP::where('user_id',1)->get();
          }



        }
        else
        {
          //gawan na natin ng entry para mareview
         
          $hasApprovedDTRP = User_DTRP::where('user_id',1)->get();//wala lang, masabi lang na empty record null;// $collDTRP;
          $pendingDTRP = $hasApprovedDTRP1;

        }

    }else $hasApprovedDTRP = $hasApprovedDTRP1;


    ( count($pendingDTRP) > 0  ) ? $hasPendingDTRP=true : $hasPendingDTRP=false;

    

    ($beginShift->format('Y-m-d') == $endShift->format('Y-m-d')) ? $sameDayShift = true : $sameDayShift=false;
    $bioEnd=null;

    //if(count($hasApprovedDTRP) > 0){ $userLog = $hasApprovedDTRP; } 
    if(count($hasApprovedDTRP) > 0){ $userLog = $hasApprovedDTRP; } 
    else 
    {


              //fix for robert's case sa logout
              if ($logType_id== 2)
              {

                //kunin mo yung bio id ng log 9HRs from shiftstart or 5hrs if parttime
                if(!$isPartTimer)
                {
                  if($isExempt)
                  {
                     $bex = Biometrics::where('productionDate',$endShift->format('Y-m-d'))->first();
                     $userLog = Logs::where('user_id',$id)->where('biometrics_id',$bex->id)->where('logType_id','2')->orderBy('biometrics_id','ASC')->get();

                    $bEnd = $endShift; //Carbon::parse($endShift->format('Y-m-d')." ".$schedForToday['timeStart'],"Asia/Manila")->addHour(9);
                  }
                  else{
                    $bEnd = Carbon::parse($thisPayrollDate." ".$schedForToday['timeStart'],"Asia/Manila")->addHour(9);

                  }
                }
                else{$bEnd = Carbon::parse($thisPayrollDate." ".$schedForToday['timeStart'],"Asia/Manila")->addHour(4);}


                if(!$isExempt) //gawin mo lang to pag hindi exempt employee
                {
                   $bioEnd = Biometrics::where('productionDate',$bEnd->format('Y-m-d'))->get();

                    if (count($bioEnd) > 0)
                    {

                      //*** dito tayo maglagay ng RD kahapon & midnight sched checker
                      //*** if it's true, LOGOUT eh from today
                      /*if ($isRDYest && $isAproblemShift)
                      {
                       
                        $userLog = Logs::where('user_id',$id)->where('biometrics_id',$bioEnd->first()->id)->where('logType_id',$logType_id)->orderBy('biometrics_id','ASC')->get();
                      }
                      else if($isAproblemShift)
                      {
                        $userLog = Logs::where('user_id',$id)->where('biometrics_id',$bioEnd->first()->id)->where('logType_id',$logType_id)->orderBy('biometrics_id','ASC')->get();
                        
                      }
                      else*/
                        $userLog = Logs::where('user_id',$id)->where('biometrics_id',$bioEnd->first()->id)->where('logType_id',$logType_id)->orderBy('biometrics_id','ASC')->get();

                    }else
                    {
                      //******** HIDE MUNA TO CHECKOUT 
                      /*
                      //check mo muna kung RD nya and 12MN shart ng shift
                      if ($isRDYest && ($beginshift->format('H:i') == Carbon::now()->startOfDay()->format('H:i')))
                      {
                        //check mo kung end shift eh for today, else
                        array_push($col, "check RDyest");
                        if ($endShift->format('Y-m-d') == $thisPayrollDate) {  goto proceedToLogTomorrow; } 
                        else
                          goto theUsual;
                        
                      } else goto proceedWithBlank;
                      */

                      goto proceedWithBlank;


                    }

                }

              }else if($logType_id == 1)// && $isAproblemShift
              {

                if($isAproblemShift)
                {
                  //****check mo kung RD nya kahapon && 12MN
                  if($isRDYest && ($beginShift->format('H:i:s') >= '00:00:00' && $beginShift->format('H:i:s') <= '03:00:00' ) )
                  {
                    goto checkKahapon;

                  }else if ($isRDYest)
                  {
                    // kunin mo yung log for today
                    goto theUsual;
                  }
                  //else if 12MN and kahapon 12MN din shift nya, kunin mo yung shift for this bio instead
                  //else if ( $beginShift->format('H:i:s') == '00:00:00' && $schedKahapon['timeStart']=='00:00:00') goto theUsual;
                  else if ( $beginShift->format('H:i:s') == '00:00:00') goto checkKahapon;

                  else if ($beginShift->format('H:i:s') > '00:00:00' && $beginShift->format('H:i:s') <= '04:00:00' ) goto checkKahapon;

                  //*** dito mo ilagay yung extra check for 1am scheds
                  else if ($beginShift->format('H:i:s') > '00:00:00' && $beginShift->format('H:i:s') < '23:59:00' ) goto theUsual;
                  else goto proceedToLogTomorrow;

                }
                  //*** new fix for issues with LOGIN
                  //*** we need to check its grouped LogINS and if log is within maxIN (4hrs) and maxLate (2nd shift) or shift +5hrs
                
                  //** kung RD nya kahapon, eh di for today dapat log nya
                  //** pero check mo muna kung pang 12MN - 4am sched sya, so may posssibility kahapon pa sya naglog
                  //** max allowed time in is +-4hrs


                checkKahapon:
                  $probTime1 = Carbon::parse($thisPayrollDate." 00:00:00","Asia/Manila")->format('Y-m-d H:i:s');
                  $probTime2 = Carbon::parse($thisPayrollDate." 04:00:00","Asia/Manila")->format('Y-m-d H:i:s');
                  
                  $today = Biometrics::where('productionDate',$thisPayrollDate)->get();


                  //$beginShift = Carbon::parse($thisPayrollDate." ".$schedForToday['timeStart'],"Asia/Manila")->format('Y-m-d H:i:s');
                  $maxIn = Carbon::parse($thisPayrollDate." ".$schedForToday['timeStart'],"Asia/Manila")->subHour(6)->format('Y-m-d H:i:s');
                
                  

                  //array_push($col, ['beginShift'=>$beginShift->format('Y-m-d H:i:s'), 'probTime1'=> $probTime1, 'probTime2'=>$probTime2 ]);
                  if ($beginShift->format('Y-m-d H:i:s') >= $probTime1 && $beginShift->format('Y-m-d H:i:s') <= $probTime2)
                  {
                    /*-- check for logs within 6hr grace period for problem shifts --*/

                    //-- FIRST: get yung from yesterday
                                // pag wala pa rin, 
                                // Get from TOmmorrow ---
                                // except kung 12Mn start, sure na  kahapon yun

                    //$checker="dun sa beginshift";


                    /*if ($beginShift->format('Y-m-d H:i:s') == Carbon::parse($thisPayrollDate)->startOfDay()->format('Y-m-d H:i:s')){
                      $yest = Carbon::parse($thisPayrollDate)->subDay(1);
                      $bioYest = Biometrics::where('productionDate',$yest->format('Y-m-d'))->get();
                    }else{
                      $yest = Carbon::parse($thisPayrollDate);
                      $bioYest = Biometrics::where('productionDate',$yest->format('Y-m-d'))->get();

                    }*/
                    
                    //check mo muna yung max allowable pre-in
                    $maxIntime = Carbon::parse($thisPayrollDate." ".$schedForToday['timeStart'],"Asia/Manila")->subHour(6);
                    $maxInBio = Biometrics::where('productionDate',$maxIntime->format('Y-m-d'))->get();
                    $bioYest = $maxInBio;
                   

                    if (count($maxInBio) > 0)
                    {
                      //dapat kunin mo lang yung maximum allowable
                      //$logsKahapon = Logs::where('user_id',$id)->where('biometrics_id',$bioYest->first()->id)->where('logType_id',$logType_id)->orderBy('biometrics_id','ASC')->get();

                      $logsKahapon = Logs::where('user_id',$id)->where('biometrics_id',$maxInBio->first()->id)->where('logType_id',$logType_id)->
                                           where('logTime','>=',$maxIntime->format('H:i:s'))->orderBy('biometrics_id','ASC')->get();


                      $checker = $logsKahapon;

                      array_push($col,['pasok logsKahapon'=>"yes",'logsKahapon'=>$logsKahapon]);

                        if (count($logsKahapon) > 0) 
                        { 

                          //*** we introduce new checking, grouped log ins
                          $groupedIN = collect($logsKahapon)->groupBy('logTime');
                          $checker = $groupedIN;

                          if (count($groupedIN) > 1)
                          {
                                $userLog = null;
                                foreach ($groupedIN as $key) 
                                {
                                  $ddata = $key->first();
                                  $b= Biometrics::find($ddata->biometrics_id);
                                  
                                  // check if pasok yung logtime sa beginShift and max logout time
                                  $l = Carbon::parse($b->productionDate." ".$ddata->logTime,'Asia/Manila');
                                  $maxI = Carbon::parse($beginShift->format('Y-m-d H:i:s'),'Asia/Manila')->addHour(-6);

                                  array_push($col, ['l'=>$l->format('Y-m-d H:i:s'), 'maxIn'=> $maxI->format('Y-m-d H:i:s'), 'endshit'=>$endShift->format('Y-m-d H:i:s') ]);

                                  if ( $l->format('Y-m-d H:i:s') >= $maxI->format('Y-m-d H:i:s') && $l->format('Y-m-d H:i:s') <= $endShift->format('Y-m-d H:i:s') )
                                  {
                                    
                                    $userLog = $ddata; break;
                                  }
                                }//end foreach grouped IN

                                if(is_null($userLog)) goto proceedWithBlank;

                                $log = date('h:i:s A',strtotime($userLog->logTime));

                                 //get real Bio prodDate from the log
                                 $b = Biometrics::find($userLog->biometrics_id);

                                 $timing = Carbon::parse($b->productionDate." ".$userLog->logTime, "Asia/Manila");

                                 if (count($hasApprovedDTRP) > 0){$dtrpIN = true; $dtrpIN_id = $userLog->first()->id; }
                                 else { $dtrpIN = false; $dtrpIN_id = null; } 
                                  
                                    
                                    if ( ($beginShift < $timing)  && !$isAproblemShift) //--- meaning late sya
                                      {
                                        $UT  = $undertime + number_format(($beginShift->diffInMinutes($timing))/60,2);

                                      } else $UT = 0;

                                $checker=$col;

                                goto proceedToLeaves;

                          }
                          
                          else
                          {
                            $palugitDate = Carbon::parse($bioYest->first()->productionDate." ".$logsKahapon->first()->logTime,"Asia/Manila")->format('Y-m-d H:i:s');

                            $pal = $palugitDate;
                         

                            if ( $palugitDate >= $maxIn && $palugitDate <= $beginShift->format('Y-m-d H:i:s')  )
                            {
                              $userLog = $logsKahapon;
                              goto proceedWithLogs;

                            } else if ($palugitDate >= $beginShift->format('Y-m-d H:i:s') &&  $palugitDate <= $endShift->format('Y-m-d H:i:s')) //meaning late lang sya
                            {

                                $userLog = $logsKahapon;
                                goto proceedWithLogs;

                            } else goto checkTomorrowLogs;
                          }
                         

                          

                          
                          
                        } else //check mo muna for today before going tomorrow
                        {
                          $ul = Logs::where('user_id',$id)->where('biometrics_id',$biometrics_id)->where('logType_id',$logType_id)->orderBy('biometrics_id','ASC')->get();

                          if (count($ul) > 0) goto theUsual;
                          else goto checkTomorrowLogs; 

                        } 

                    

                    }//end if may bioYest
                    else
                    {

                      
                      //tomorrow in this case is maxLate IN
                      $tommorow = Carbon::parse($thisPayrollDate." ".$schedForToday['timeStart'],"Asia/Manila")->addHour(8);
                      $bioForTom = Biometrics::where('productionDate',$tommorow->format('Y-m-d'))->get(); 

                      $col->push(['bioForTom'=>$bioForTom]);

                      if (count($bioForTom) > 0){
                        $finishShift = Carbon::parse($bioForTom->first()->productionDate." ".$schedForToday['timeStart'],"Asia/Manila")->addHour(9);

                          $logPalugit = Logs::where('user_id',$id)->where('biometrics_id',$bioForTom->first()->id)->where('logType_id',$logType_id)->orderBy('biometrics_id','ASC')->get();

                          if (count($logPalugit) > 0) 
                          { 
                            //we need to check first if it is within the palugit period: meaning LATE
                            //if more than palugit: meaning for tomorrow's bio yun
                            $palugitDate = Carbon::parse($bioForTom->first()->productionDate." ".$logPalugit->first()->logTime,"Asia/Manila")->format('Y-m-d H:i:s');
                            $pal = $palugitDate;
                           

                            if ( $palugitDate >= $maxIn && $palugitDate <= $beginShift  )
                            {
                              $userLog = $logPalugit;
                              goto proceedWithLogs;

                            } else if ($palugitDate >= $beginShift->format('Y-m-d H:i:s') &&  $palugitDate <= $finishShift->format('Y-m-d H:i:s')) //meaning late lang sya
                            {

                                $userLog = $logPalugit;
                                goto proceedWithLogs;

                            } else goto proceedWithBlank;
                            
                          } else goto proceedWithBlank; 

                      } else goto proceedWithBlank;

                    } //end else if (count($logsKahapon) > 0) 
   
                    

                  } //end ($beginShift >= $probTime1 && $beginShift <= $probTime2) 
                  else
                    goto theUsual;

                

              }

              else {

                theUsual:

                  $checker="in sya";
                  $userLog = Logs::where('user_id',$id)->where('biometrics_id',$biometrics_id)->where('logType_id',$logType_id)->orderBy('biometrics_id','ASC')->get();

              } 
    }
             
          

 


      /*--- after getting the logs, IF (logIN_type) go to another filter pass
            else, just proceed -- */

     
            
     $probTime1 = Carbon::parse($thisPayrollDate." 00:00:00","Asia/Manila")->format('Y-m-d H:i:s');
     $probTime2 = Carbon::parse($thisPayrollDate." 03:00:00","Asia/Manila")->format('Y-m-d H:i:s');

     //$beginShift = Carbon::parse($thisPayrollDate." ".$schedForToday['timeStart'],"Asia/Manila")->format('Y-m-d H:i:s');

     //$checker = null; //['userLg'=>$userLog];
     $ulog1 = $userLog;
     
      if (is_null($userLog) || count($userLog)<1 )
      {  

        /* ------------ THIS IS WHERE WE CHECK FOR THOSE PROBLEM AREAS ---------
                                  perform this only for LOG INS                         */


          if ($logType_id == 1)
          {
                $maxIn = Carbon::parse($thisPayrollDate." ".$schedForToday['timeStart'],"Asia/Manila")->subHour(6)->format('Y-m-d H:i:s');
                
           
                if ($beginShift >= $probTime1 && $beginShift <= $probTime2)
                {
                  /*-- check for logs within 6hr grace period for problem shifts --*/

                  /*-- FIRST: get yung from yesterday
                              pag wala pa rin, 
                              Get from TOmmorrow --- */

                  $yest = Carbon::parse($thisPayrollDate)->addDay(-1);
                  $bioYest = Biometrics::where('productionDate',$yest->format('Y-m-d'))->get();

                  if (count($bioYest) > 0)
                  {

                    $logsKahapon = Logs::where('user_id',$id)->where('biometrics_id',$bioYest->first()->id)->where('logType_id',$logType_id)->orderBy('biometrics_id','ASC')->get();

                   

                      if (count($logsKahapon) > 0) 
                      { 
                       

                        $palugitDate = Carbon::parse($bioYest->first()->productionDate." ".$logsKahapon->first()->logTime,"Asia/Manila")->format('Y-m-d H:i:s');

                        $pal = $palugitDate;
                       

                        if ( $palugitDate >= $maxIn && $palugitDate <= $beginShift  )
                        {
                          $userLog = $logsKahapon;
                          goto proceedWithLogs;

                        } else if ($palugitDate >= $beginShift &&  $palugitDate <= $finishShift) //meaning late lang sya
                        {

                            $userLog = $logsKahapon;
                            goto proceedWithLogs;

                        } else goto checkTomorrowLogs;
                        
                      } else goto checkTomorrowLogs; 

                  

                  }//end if may bioYest
                  else
                  {

                    checkTomorrowLogs:

                    $tommorow = Carbon::parse($thisPayrollDate)->addDay();
                    $bioForTom = Biometrics::where('productionDate',$tommorow->format('Y-m-d'))->get();

                      

                    if (count($bioForTom) > 0){
                      //$finishShift = Carbon::parse($bioForTom->first()->productionDate." ".$schedForToday['timeStart'],"Asia/Manila")->addHour(9);

                        $logPalugit = Logs::where('user_id',$id)->where('biometrics_id',$bioForTom->first()->id)->where('logType_id',$logType_id)->orderBy('biometrics_id','ASC')->get();



                        if (count($logPalugit) > 0) 
                        { 
                          //we need to check first if it is within the palugit period: meaning LATE
                          //if more than palugit: meaning for tomorrow's bio yun
                          $palugitDate = Carbon::parse($bioForTom->first()->productionDate." ".$logPalugit->first()->logTime,"Asia/Manila")->format('Y-m-d H:i:s');
                          $pal = $palugitDate;
                         

                          if ( $palugitDate >= $maxIn && $palugitDate <= $beginShift  )
                          {
                            $userLog = $logPalugit;
                            goto proceedWithLogs;

                          } else if ($palugitDate >= $beginShift &&  $palugitDate <= $endShift) //meaning late lang sya
                          {

                              $userLog = $logPalugit;
                              goto proceedWithLogs;

                          } else goto proceedWithBlank;
                          
                        } else goto proceedWithBlank; 

                    } else goto proceedWithBlank;

                  } //end else if (count($logsKahapon) > 0) 
 
                  

                } //end ($beginShift >= $probTime1 && $beginShift <= $probTime2) 
                else //check mo kung may undertime IN
                {
                  $maxIn = Carbon::parse($thisPayrollDate." ".$schedForToday['timeStart'],"Asia/Manila")->addHour(7);//->format('Y-m-d H:i:s');
                  $currBio = Biometrics::where('productionDate',$maxIn->format('Y-m-d'))->get();
                  if (count($currBio) > 0) $cb=$currBio->first(); else goto proceedWithBlank;

                  $lateLogs = Logs::where('user_id',$id)->where('biometrics_id',$cb->id)->where('logType_id',$logType_id)->
                                  where('logTime','<=',$maxIn->format('H:i:s'))->orderBy('biometrics_id','ASC')->get();
                  //$keme=$maxIn;
                  if (count($lateLogs) > 0){  $userLog = $lateLogs; goto proceedWithLogs; } else {  goto proceedWithBlank; }

                }

          /* ------------ END THIS IS WHERE WE CHECK FOR THOSE PROBLEM AREAS : LOG IN ---------*/       
          } else if($logType_id == 2)
          {
            // if  12MN < beginShift > 3AM
             //$checker = ['enter'=>"12MN < beginShift > 3AM", 'beginShift'=>$beginShift->format('Y-m-d H:i:s'),'$probTime1'=>$probTime1,'$probTime2'=>$probTime2 ];

            $ulog1 = 'null2';

              proceedToLogTomorrow:

              //*** pero check mo muna kung complicated shift
              if ($isAproblemShift && $isRDYest)
              {
                $allowedOT = Carbon::parse($beginShift->format('Y-m-d H:i:s'),"Asia/Manila")->addDay(1)->addHour(17);
              }
              else
                $allowedOT = Carbon::parse($thisPayrollDate." ".$schedForToday['timeStart'],"Asia/Manila")->addHour(17);
              
              
              $bioForTom = Biometrics::where('productionDate',$allowedOT->format('Y-m-d'))->get();
              $bioNow =  Carbon::parse($thisPayrollDate." ".$schedForToday['timeStart'],"Asia/Manila");
              $bioForNow = Biometrics::where('productionDate',$bioNow->format('Y-m-d'))->get();
              $uLog1 = Logs::where('user_id',$id)->where('biometrics_id',$bioForNow->first()->id)->where('logType_id',$logType_id)->orderBy('created_at','ASC')->get();

              //$checker = ['aOT'=>$allowedOT];
              if (count($bioForTom) > 0)
              {
                
                $uLog = Logs::where('user_id',$id)->where('biometrics_id',$bioForTom->first()->id)->where('logType_id',$logType_id)->orderBy('created_at','ASC')->get();

                $theNextday = true;
               

                if (count($uLog) > 0)
                {
                  // check if yung log eh pasok pa sa allowed OT timeframe

                  $l = Carbon::parse($tommorow->format('Y-m-d')." ".$uLog->first()->logTime,"Asia/Manila");
                  //$b = Biometrics::find($uLog->first()->biometrics_id);
                  //$l = Carbon::parse(." ".$uLog->first()->logTime,"Asia/Manila");

                  
                  //$checker = ['tommorow'=>$tommorow, 'beginShift'=>$beginShift, 'uLog'=>$uLog,'bioForTom'=>$bioForTom,'allowedOT'=>$allowedOT,'thisPayrollDate'=>$thisPayrollDate];
                   $checker = ['ulog'=>$uLog,'l'=>$l->format('Y-m-d H:i:s'), 'b'=>$beginShift->format('Y-m-d H:i:s'), 'OT'=>$allowedOT->format('Y-m-d H:i:s') ];
                  if ( $l->format('Y-m-d H:i:s') > $beginShift->format('Y-m-d H:i:s') && $l->format('Y-m-d H:i:s') <= $allowedOT->format('Y-m-d H:i:s') )
                  {
                        $userLog = $uLog;
                        //goto proceedWithLogs;

                        $b= Biometrics::find($userLog->first()->biometrics_id);
                        /*if($isAproblemShift) 
                          {
                            $log = date('M d h:i:s A',strtotime(Carbon::parse($b->productionDate.' '.$userLog->first()->logTime,'Asia/Manila')->format('M d h:i:s A')));
                          } else $log = date('h:i:s A',strtotime($userLog->first()->logTime));*/
                          $log = date('M d h:i:s A',strtotime(Carbon::parse($b->productionDate.' '.$userLog->first()->logTime,'Asia/Manila')->format('M d h:i:s A')));



                        //$log = date("M d",strtotime($bioForTom->first()->productionDate))." ". date('h:i:s A',strtotime($userLog->first()->logTime));

                         //$timing =  Carbon::parse($userLog->first()->productionDate." ".$userLog->first()->logTime, "Asia/Manila");
                         $timing =  Carbon::parse(date("M d",strtotime($bioForTom->first()->productionDate))." ". date('h:i:s A',strtotime($userLog->first()->logTime)),'Asia/Manila');
                         
                         if (count($hasApprovedDTRP) > 0){
                            //$log = date('h:i:s A',strtotime($userLog->logTime));
                            switch ($logType_id) {
                              case 1:{ $dtrpIN = true; $dtrpIN_id = $userLog->first()->id; }break;
                              
                              case 2:{ $dtrpOUT = true; $dtrpOUT_id = $userLog->first()->id; }break;
                            }

                         }else {

                            switch ($logType_id) {
                              case 1:{ $dtrpIN = false; $dtrpIN_id = null; }break;
                              
                              case 2:{ $dtrpOUT = false; $dtrpOUT_id = null;  }break;
                            }
                         } 
                          
                          

                          //*********** APPLICABLE ONLY TO WORK DAY ********************//

                          if ($logType_id == 1) 
                          {
                            $parseThis = $schedForToday['timeStart'];
                            if ( (Carbon::parse($parseThis,"Asia/Manila") < $timing)  ) //&& !$problemArea[0]['problemShift']--- meaning late sya
                              {
                                $UT  = $undertime + number_format((Carbon::parse($parseThis,"Asia/Manila")->diffInMinutes($timing))/60,2);

                              } else $UT = 0;
                          }
                            
                          else if ($logType_id == 2)
                            $parseThis = $schedForToday['timeEnd'];
                            if (Carbon::parse($bioForTom->first()->productionDate.' '.$parseThis,"Asia/Manila") > $timing ) //&& !$problemArea[0]['problemShift']--- meaning early out sya
                              {
                                $UT  = $undertime + number_format((Carbon::parse($bioForTom->first()->productionDate.' '.$parseThis,"Asia/Manila")->diffInMinutes($timing))/60,2);//$undertime + 

                              } else $UT=$undertime;

                          


                  }//end if pasok sa alloted OT
                  else
                  {
                    //***** baka ang sched nya ay 3pm-12mn at undertime lang sya
                    if(count($uLog1) > 0)
                    {
                      $l = Carbon::parse($bioForNow->first()->productionDate." ".$uLog1->first()->logTime,"Asia/Manila");
                      if ( $l->format('Y-m-d H:i:s') > $beginShift->format('Y-m-d H:i:s') && $l->format('Y-m-d H:i:s') <= $allowedOT->format('Y-m-d H:i:s') )
                      {
                            $userLog = $uLog1;
                      }else goto proceedWithBlank;

                    }else goto proceedWithBlank;
                    

                  } //goto proceedWithBlank;

                } else
                {
                  //*** before u assume na blank, check mo muna kung undertime/halfday lang sya
                  //*** look for logout < endShift

                  //$checker = "baka undertime lang";
                  
                  

                  $logsToday = Logs::where('user_id',$id)->where('biometrics_id',$biometrics_id)->where('logType_id',$logType_id)->orderBy('created_at','ASC')->get();

                  if (count($logsToday) > 0)
                  {



                    $groupedToday = collect($logsToday)->groupBy('logTime');
                    $u=null;
                    $ulog1 = $groupedToday;// 'NOT NULL $u; waley log kasi RD na nya';

                    foreach ($groupedToday as $key) {

                      $l = Carbon::parse($thisPayrollDate.' '.$key->first()->logTime,'Asia/Manila');

                      //if pasok ung log within shift, undertime out sya
                      if( $l->format('Y-m-d H:i:s') > $beginShift->format('Y-m-d H:i:s') && $l->format('Y-m-d H:i:s') <= $endShift->format('Y-m-d H:i:s'))
                      {
                        $userLog = $key->first(); $u=$key->first();;
                        //$checker="may found log";
                        break;

                      }

                    }//end foreach

                    if (is_null($u))
                    {

                      //***** baka ang sched nya ay 3pm-12mn at undertime lang sya AND RD na bukas kaya wala syang Tom logs
                      $ulog1 = "2nd pass";
                      $checker="b"; goto proceedWithBlank;

                      
                     



                      
                    } 
                    else
                    {

                      
                      $b= Biometrics::find($userLog->biometrics_id);
                          if($isAproblemShift) 
                          {
                            $log = date('M d h:i:s A',strtotime(Carbon::parse($b->productionDate.' '.$userLog->logTime,'Asia/Manila')->format('M d h:i:s A')));
                          } else $log = date('h:i:s A',strtotime($userLog->logTime));


                      $timing = Carbon::parse($b->productionDate." ".$userLog->logTime, "Asia/Manila");
                      if (count($hasApprovedDTRP) > 0){$dtrpOUT = true; $dtrpOUT_id = $userLog->id; }
                      else {$dtrpOUT = false; $dtrpOUT_id = null;} 

                      $parseThis = $schedForToday['timeEnd'];
                      if (Carbon::parse($thisPayrollDate." ".$parseThis,"Asia/Manila") > $timing && !$isAproblemShift)
                      {
                        $UT  = $undertime + number_format((Carbon::parse($thisPayrollDate." ".$parseThis,"Asia/Manila")->diffInMinutes($timing))/60,2);
                      } else $UT=$undertime;

                      goto proceedToLeaves;

                          
                          

                    }//end may log from grouped

                  }else goto proceedWithBlank;
                  


                } 

              }
               //***** baka ang sched nya ay 3pm-12mn at undertime lang sya
              else if( count($uLog1) > 0 )
              {
                  // check if yung log eh pasok pa sa allowed timeframe

                  $l = Carbon::parse($bioForNow->first()->productionDate." ".$uLog1->first()->logTime,"Asia/Manila");
                  if ( $l->format('Y-m-d H:i:s') > $beginShift->format('Y-m-d H:i:s') && $l->format('Y-m-d H:i:s') <= $allowedOT->format('Y-m-d H:i:s') )
                  {
                        $userLog = $uLog1;
                  }else goto proceedWithBlank;

              }else goto proceedWithBlank;
             
              

              

            /*}*/ 
          /* ------------ END THIS IS WHERE WE CHECK FOR THOSE PROBLEM AREAS ---------*/       
          } else // ---proceed with the usual null logs
          {

                proceedWithBlank:

                               $link = action('LogsController@viewRawBiometricsData',$id);
                               $userLog=null;
                               
                               $icons = "<a title=\"Verify Biometrics data\" class=\"pull-right text-gray\" target=\"_blank\" style=\"font-size:1.2em;\" href=\"$link#$biometrics_id\"><i class=\"fa fa-clock-o\"></i></a>";
                                
                                
                               if ($hasHolidayToday  && $isBackoffice)//&& count($holidayToday) > 0
                               {
                                $log = "<strong class=\"text-danger\"> N / A </strong>". $icons;
                                $workedHours = $holidayToday->first()->name;

                               }else if ($hasHolidayToday && !$isBackoffice )// && count($holidayToday) > 0
                               {
                                if($logType_id == 1) {$log = "<strong class=\"text-danger\">No IN</strong>". $icons;}
                                else {$log = "<strong class=\"text-danger\">No OUT</strong>". $icons;}
                                $workedHours = $holidayToday->first()->name;

                               } 
                               else if ($hasLWOP)
                               {

                                  $link = action('UserController@myRequests',$id);
                                  $icons = "<a title=\"LWOP request\" class=\"pull-right text-primary\" target=\"_blank\" style=\"font-size:1.2em;\" href=\"$link\"><i class=\"fa fa-info-circle\"></i></a>";

                                  
                                  if ($lwopDeet->totalCredits >= '1.0'){
                                    $log="<small><em>Leave Without Pay </em></small>".$icons;
                                    $workedHours = 8.0;

                                  } 
                                  else if ($lwopDeet->totalCredits == '0.50'){
                                          
                                            $log="<small><em>Half-day LWOP </em></small>".$icons;
                                            if (count($userLog) <1) $workedHours = 4.0;
                                            else $workedHours = 8.0;
                                          
                                  }

                               }//end if has LWOP
                                
                                /*else if ($hasVTO)
                                 {

                                    $link = action('UserController@myRequests',$id);
                                    $icons = "<a title=\"VTO request\" class=\"pull-right text-primary\" target=\"_blank\" style=\"font-size:1.2em;\" href=\"$link\"><i class=\"fa fa-info-circle\"></i></a>";

                                    
                                    if ($lwopDeet->totalCredits >= '1.0'){
                                      $log="<small><em>Leave Without Pay </em></small>".$icons;
                                      $workedHours = 8.0;

                                    } 
                                    else if ($lwopDeet->totalCredits == '0.50'){
                                            
                                              $log="<small><em>Half-day LWOP </em></small>".$icons;
                                              if (count($userLog) <1) $workedHours = 4.0;
                                              else $workedHours = 8.0;
                                            
                                    }

                                  }//end if has VTO
                                  */
                                else
                                {
                                    if($logType_id == 1) $log =  "<strong class=\"text-danger\">No IN</strong>". $icons;
                                    else if ($logType_id == 2)$log = "<strong class=\"text-danger\">No OUT</strong>". $icons;
                                    $workedHours = "N/A";

                                }
                                
                                $timing=null; //Carbon::parse('22:22:22');
                                $UT = $undertime;

          }
        
          
      } 
      else
      {

         proceedWithLogs:

                        //**** we need to introduce new checking for Lei's case
                        //**** Check first if OUT > IN
                        //**** if yes, then good
                        //**** if not, then get the out from tomorrow

                        if ($logType_id== 2)
                        {

                          if ( empty($userLog->first()->logTime) || is_null($beginShift) || is_null($userLog) ) { $checker = "from proceedWithLogs"; goto proceedWithBlank;}
                          else
                          {  
                            
                            //check mo muna kung legit itong LogOUt if pasok within allowed OT
                            $allowedOT = Carbon::parse($beginShift->format('Y-m-d H:i:s'),"Asia/Manila")->addHour(18);
                            $b = Biometrics::find($userLog->first()->biometrics_id);
                            $l = Carbon::parse($b->productionDate.' '.$userLog->first()->logTime,'Asia/Manila');
                            
                            $checker = ['l'=>$l->format('Y-m-d H:i:s'), 'b'=>$beginShift->format('Y-m-d H:i:s'), 'allOT'=>$allowedOT->format('Y-m-d H:i:s')];

                            if( ($l->format('Y-m-d H:i:s') > $beginShift->format('Y-m-d H:i:s')) && ($l->format('Y-m-d H:i:s') <= $allowedOT->format('Y-m-d H:i:s')) )
                            
                            {
                              $checker="goto idealSituation";
                              goto idealSituation;

                            }
                            
                            /*if ( Carbon::parse($thisPayrollDate." ".$userLog->first()->logTime,'Asia/Manila')->format('Y-m-d H:i:s') > $beginShift )
                            
                            {
                              //*** ideal situation, go on...
                              //$checker="goto ideal situation";
                              //*** but make sure na within allowed max OT out sya
                              // else no logs
                              goto idealSituation;

                            }*/
                            else
                            {
                              $checker="goto non ideal";
                              

                              //********** we now introduce grouped logout checking
                              //********** if there are more than one, then check each set
                              $groupedLogs = collect($userLog)->groupBy('logTime');
                              if (count($groupedLogs) > 1)
                              {
                                $col = [];$userLog=null;
                                array_push($col, $groupedLogs);
                                foreach ($groupedLogs as $key) 
                                {
                                  $ddata = $key->first();
                                  
                                  // check if pasok yung logtime sa beginShift and max logout time
                                  $bioActual = Biometrics::find($key->first()->biometrics_id);
                                  $l = Carbon::parse($bioActual->productionDate." ".$key->first()->logTime,'Asia/Manila');
                                  $maxO = Carbon::parse($endShift,'Asia/Manila')->addHour(9);

                                  array_push($col, ['l'=>$l->format('Y-m-d H:i:s'), 'b'=> $beginShift, 'm'=>$maxO->format('Y-m-d H:i:s') ]);

                                  if ( $l->format('Y-m-d H:i:s') >= $beginShift->format('Y-m-d H:i:s') && $l->format('Y-m-d H:i:s') <= $maxO->format('Y-m-d H:i:s') )
                                  {
                                    
                                    $userLog = $ddata; break;
                                  }
                                 

                                 
                                    
                                }

                                if(is_null($userLog)) goto proceedWithBlank;



                                $checker = $col;
                                if($isAproblemShift) {
                                  $b= Biometrics::find($userLog->biometrics_id);
                                  $log = date('M d h:i:s A',strtotime(Carbon::parse($b->productionDate.' '.$userLog->logTime,'Asia/Manila')->format('M d h:i:s A')));
                                } else $log = date('h:i:s A',strtotime($userLog->logTime));

                                $b = Biometrics::find($userLog->biometrics_id);
                                $timing = Carbon::parse($b->productionDate." ".$userLog->logTime, "Asia/Manila");
                                 if (count($hasApprovedDTRP) > 0)
                                  {$dtrpOUT = true; $dtrpOUT_id = $userLog->id;}
                                else
                                  {$dtrpOUT = false; $dtrpOUT_id = null; }

                                  //*********** APPLICABLE ONLY TO WORK DAY ********************//

                                    $parseThis = $schedForToday['timeEnd'];
                                    if (Carbon::parse($thisPayrollDate." ".$parseThis,"Asia/Manila") > $timing && !$isAproblemShift)//!$problemArea[0]['problemShift']
                                     //--- meaning early out sya
                                      {
                                        $UT  = $undertime + number_format((Carbon::parse($thisPayrollDate." ".$parseThis,"Asia/Manila")->diffInMinutes($timing))/60,2);

                                      } else $UT=$undertime;
                                      goto proceedToLeaves;

                                  
                                  //*********** end APPLICABLE ONLY TO WORK DAY ********************//


                              }else
                              {

                                  //within the day shift pero walang logs, so baka nag OT sya kinabukasan an yung LogOUT
                                  //so we need to get logs from tomorrow within the 8hr period
                                  $tommorow = Carbon::parse($thisPayrollDate)->addDay();
                                  $allowedOT = Carbon::parse($thisPayrollDate." ".$schedForToday['timeStart'],"Asia/Manila")->addHour(22);
                                  $bioForTom = Biometrics::where('productionDate',$allowedOT->format('Y-m-d'))->get();
                                  if (count($bioForTom) > 0)
                                  {

                                    if (count($hasApprovedDTRP) > 0)
                                      $uLog = $hasApprovedDTRP;
                                    else
                                      $uLog = Logs::where('user_id',$id)->where('biometrics_id',$bioForTom->first()->id)->where('logType_id',$logType_id)->orderBy('created_at','ASC')->get();

                                    if (count($uLog) > 0)
                                    {

                                      // check if yung log eh pasok pa sa allowed OT timeframe
                                      $l = Carbon::parse($tommorow->format('Y-m-d')." ".$uLog->first()->logTime,"Asia/Manila");
                                      if ( $l->format('Y-m-d H:i:s') > $beginShift && $l->format('Y-m-d H:i:s') <= $allowedOT->format('Y-m-d H:i:s') )
                                      {
                                            $ulog1="witihin range";
                                            $userLog = $uLog;
                                            //goto proceedWithLogs;

                                             $b= $bioForTom->first();
                                              /*if($isAproblemShift) 
                                              {
                                                $log = date('M d h:i:s A',strtotime(Carbon::parse($b->productionDate.' '.$userLog->first()->logTime,'Asia/Manila')->format('M d h:i:s A')));
                                              } else $log = date('h:i:s A',strtotime($userLog->first()->logTime));*/
                                              $log = date('M d h:i:s A',strtotime(Carbon::parse($b->productionDate.' '.$userLog->first()->logTime,'Asia/Manila')->format('M d h:i:s A')));

                                           
                                             $timing = Carbon::parse(date("M d",strtotime($bioForTom->first()->productionDate))." ". date('h:i:s A',strtotime($userLog->first()->logTime)),'Asia/Manila');

                                             if (count($hasApprovedDTRP) > 0){
                                                //$log = date('h:i:s A',strtotime($userLog->logTime));
                                                switch ($logType_id) {
                                                  case 1:{ $dtrpIN = true; $dtrpIN_id = $userLog->first()->id; }break;
                                                  
                                                  case 2:{ $dtrpOUT = true; $dtrpOUT_id = $userLog->first()->id; }break;
                                                }

                                             }else {

                                                switch ($logType_id) {
                                                  case 1:{ $dtrpIN = false; $dtrpIN_id = null; }break;
                                                  
                                                  case 2:{ $dtrpOUT = false; $dtrpOUT_id = null;  }break;
                                                }
                                             } 
                                              
                                              

                                              //*********** APPLICABLE ONLY TO WORK DAY ********************//

                                              if ($logType_id == 1) 
                                              {
                                                $parseThis = $schedForToday['timeStart'];
                                                if ( (Carbon::parse($parseThis,"Asia/Manila") < $timing) ) // && !$problemArea[0]['problemShift']--- meaning late sya
                                                  {
                                                    $UT  = $undertime + number_format((Carbon::parse($parseThis,"Asia/Manila")->diffInMinutes($timing))/60,2);

                                                  } else $UT = 0;
                                              }
                                                
                                              else if ($logType_id == 2)
                                              {

                                                /*$parseThis = $schedForToday['timeEnd'];
                                                if (Carbon::parse($parseThis,"Asia/Manila") > $timing ) //&& !$problemArea[0]['problemShift']--- meaning early out sya
                                                  {
                                                    $UT  = $undertime + number_format((Carbon::parse($parseThis,"Asia/Manila")->diffInMinutes($timing))/60,2);

                                                  } else $UT=$undertime;*/


                                                

                                                /* emelda fix */
                                                if (strlen($schedForToday['timeEnd']) > 9)
                                                  $parseThis = Carbon::parse($schedForToday['timeEnd'],"Asia/Manila");
                                                else
                                                  $parseThis = Carbon::parse($thisPayrollDate.' '.$schedForToday['timeEnd'],"Asia/Manila");

                                                if ($parseThis > $timing ) //&& !$problemArea[0]['problemShift']--- meaning early out sya
                                                  {
                                                    $UT  = $undertime + number_format(($parseThis->diffInMinutes($timing))/60,2);

                                                  } else $UT=$undertime;
                                                 

                                              }
                                              
                                              $checker="non ideal, with $uLog, then proceedToLeaves";
                                              goto proceedToLeaves;

                                      }//end if pasok sa alloted OT
                                      else 
                                      { 
                                        
                                         //***** baka ang sched nya ay 3pm-12mn at undertime lang sya
                                        $bioNow =  Carbon::parse($thisPayrollDate." ".$schedForToday['timeStart'],"Asia/Manila");
                                        $bioForNow = Biometrics::where('productionDate',$bioNow->format('Y-m-d'))->get();
                                        $uLog = Logs::where('user_id',$id)->where('biometrics_id',$bioForNow->first()->id)->where('logType_id',$logType_id)->orderBy('created_at','ASC')->get();

                                        if (count($uLog) > 0)
                                        {
                                          $l = Carbon::parse($bioForNow->first()->productionDate." ".$uLog->first()->logTime,"Asia/Manila");

                                          //$ulog1=$uLog;//"hindi within range";
                                          
                                          if ( $l->format('Y-m-d H:i:s') > $beginShift->format('Y-m-d H:i:s') && $l->format('Y-m-d H:i:s') <= $endShift->format('Y-m-d H:i:s') )
                                          {
                                                // $cl = new Collection;
                                                // $ulog1 = $cl->push(['l >'=>$l->format('Y-m-d H:i:s'),'b'=>$beginShift->format('Y-m-d H:i:s'), '<='=>$endShift->format('Y-m-d H:i:s') ]);
                                                $userLog = $uLog;
                                          }else goto proceedWithBlank;

                                        }else goto proceedWithBlank;

                                        


                                        // $checker=['l'=>$l->format('Y-m-d H:i:s'),'bs'=>$beginShift,'aOT'=>$allowedOT->format('Y-m-d H:i:s')]; goto proceedWithBlank;
                                      }

                                    } else {  goto proceedWithBlank;} 

                                  }else {$checker="from non ideal, proceed Blank"; goto proceedWithBlank;}

                              }// end if may grouped Logs


                              
                            }//end else ideal situation

                          }//end if not empty userlog

                          

                        }//end if logtype 2 OUT
                        

                         idealSituation:

                          $b= Biometrics::find($userLog->first()->biometrics_id);
                          //if($isAproblemShift && $logType_id==2) 
                          //{

                          //  $log = date('M d h:i:s A',strtotime(Carbon::parse($b->productionDate.' '.$userLog->first()->logTime,'Asia/Manila')->format('M d h:i:s A')));
                          //} else{

                              //if($logType_id==2 && $beginShift->format('Y-m-d') !== $endShift->format('Y-m-d')) 
                                $log = date('M d h:i:s A',strtotime(Carbon::parse($b->productionDate.' '.$userLog->first()->logTime,'Asia/Manila')->format('M d h:i:s A')));
                             // else
                             //   $log = date('h:i:s A',strtotime($userLog->first()->logTime));
                          //} 

                         $timing = Carbon::parse($b->productionDate." ".$userLog->first()->logTime, "Asia/Manila");
                         

                         //$timing = Carbon::parse(date("M d",strtotime($bioForTom->first()->productionDate))." ". date('h:i:s A',strtotime($userLog->first()->logTime)),'Asia/Manila');
                         if (count($hasApprovedDTRP) > 0){
                            //$log = date('h:i:s A',strtotime($userLog->logTime));
                            switch ($logType_id) {
                              case 1:{ $dtrpIN = true; $dtrpIN_id = $hasApprovedDTRP->first()->id; /*$userLog->first()->id;*/ }break;
                              
                              case 2:{ $dtrpOUT = true; $dtrpOUT_id = $hasApprovedDTRP->first()->id; /*$userLog->first()->id; */ }break;
                            }

                         }else {

                            switch ($logType_id) {
                              case 1:{ $dtrpIN = false; $dtrpIN_id = null; }break;
                              
                              case 2:{ $dtrpOUT = false; $dtrpOUT_id = null;  }break;
                            }
                         } 
                          
                          
                          

                          //*********** APPLICABLE ONLY TO WORK DAY ********************//

                          if ($logType_id == 1) 
                          {
                            $parseThis = $schedForToday['timeStart'];
                            if ( (Carbon::parse($thisPayrollDate." ".$parseThis,"Asia/Manila") < $timing)  && !$isAproblemShift) //--- meaning late sya
                              {
                                $UT  = $undertime + number_format((Carbon::parse($thisPayrollDate." ".$parseThis,"Asia/Manila")->diffInMinutes($timing))/60,2);

                              } else $UT = 0;
                          }
                            
                          else if ($logType_id == 2)
                            $parseThis = $schedForToday['timeEnd'];
                            if (Carbon::parse($thisPayrollDate." ".$parseThis,"Asia/Manila") > $timing && !$isAproblemShift)//!$problemArea[0]['problemShift']
                             //--- meaning early out sya
                              {
                                $UT  = $undertime + number_format((Carbon::parse($thisPayrollDate." ".$parseThis,"Asia/Manila")->diffInMinutes($timing))/60,2);

                              } else $UT=$undertime;

                          
                          //*********** end APPLICABLE ONLY TO WORK DAY ********************//

      }//end if may login 


      proceedToLeaves:

      /*-------- VACATION LEAVE -----------*/
      if ($hasVL && $hasPendingVL)
      {
        $leaveDetails->push(['type'=>"VL for approval",'icon'=>'fa-info-circle', 'details'=>$vlDeet]);
        
      } else if($hasLeave && $hasVL || $hasVL)
      {
        ($vlDeet->isApproved) ? $leaveDetails->push(['type'=>"VL",'icon'=>'fa-plane', 'details'=>$vlDeet]) : $leaveDetails->push(['type'=>"VL denied",'icon'=>'fa-times', 'details'=>$vlDeet]);
      }

      /*-------- VTO LEAVE -----------*/
      if ($hasVTO && $hasPendingVTO)
      {
        $leaveDetails->push(['type'=>"VTO for approval",'icon'=>'fa-info-circle', 'details'=>$vtoDeet]);
        
      } else if($hasVTO)
      {
        ($vtoDeet->isApproved) ? $leaveDetails->push(['type'=>"VTO",'icon'=>'fa-history', 'details'=>$vtoDeet]) : $leaveDetails->push(['type'=>"VTO denied",'icon'=>'fa-times', 'details'=>$vtoDeet]);
      }


      /*-------- SICK LEAVE -----------*/
      if ($hasSL && $hasPendingSL)
      {
        $leaveDetails->push(['type'=>"SL for approval",'icon'=>'fa-info-circle', 'details'=>$slDeet]);
        
      } else if($hasLeave && $hasSL || $hasSL)
      {
        ($slDeet->isApproved) ? $leaveDetails->push(['type'=>"SL",'icon'=>'fa-stethoscope', 'details'=>$slDeet]) : $leaveDetails->push(['type'=>"SL denied",'icon'=>'fa-times', 'details'=>$slDeet]); 
      }

      /*-------- LEAVE WITHOUT PAY -----------*/
      if ($hasLWOP && $hasPendingLWOP)
      {
        //$hasLeave = true;
        $lwopDetails->push(['type'=>"LWOP for approval",'icon'=>'fa-info-circle', 'details'=>$lwopDeet]);
        
      } else if($hasLWOP)
      {
        //$hasLeave = true;
        ($lwopDeet->isApproved) ? $lwopDetails->push(['type'=>"LWOP",'icon'=>'fa-meh-o', 'details'=>$lwopDeet]) : $lwopDetails->push(['type'=>"LWOP denied",'icon'=>'fa-times', 'details'=>$lwopDeet]);
      }

      /*-------- OBT -----------*/
      if ($hasOBT && $hasPendingOBT)
      {
        $hasLeave = true;
        $leaveDetails->push(['type'=>"OBT for approval",'icon'=>'fa-info-circle', 'details'=>$obtDeet]);
        
      } else if($hasOBT)
      {
        $hasLeave = true;
        ($obtDeet->isApproved) ?  $leaveDetails->push(['type'=>"OBT",'icon'=>'fa-briefcase', 'details'=>$obtDeet]) : $leaveDetails->push(['type'=>"OBT denied",'icon'=>'fa-times', 'details'=>$obtDeet]);
      }

      /*-------- family LEAVE -----------*/
      if ($hasFL && $hasPendingFL)
      {
        switch ($flDeet->leaveType) {
          case 'ML':{$leaveDetails->push(['type'=>"ML for approval",'icon'=>'fa-info-circle', 'details'=>$flDeet]);}break;
          case 'MC':{$leaveDetails->push(['type'=>"MC for approval",'icon'=>'fa-info-circle', 'details'=>$flDeet]);}break;
          case 'PL':{$leaveDetails->push(['type'=>"PL for approval",'icon'=>'fa-info-circle', 'details'=>$flDeet]);}break;
          case 'SPL':{$leaveDetails->push(['type'=>"SPL for approval",'icon'=>'fa-info-circle', 'details'=>$flDeet]);}break;
             
        }
        
        
      } else if($hasLeave && $hasFL)
      {
        if ($flDeet->isApproved)
        {
          switch ($flDeet->leaveType) {
            case 'ML':{$leaveDetails->push(['type'=>"ML",'icon'=>'fa-female', 'details'=>$flDeet]);}break;
            case 'MC':{$leaveDetails->push(['type'=>"MC",'icon'=>'fa-female', 'details'=>$flDeet]);}break;
            case 'PL':{$leaveDetails->push(['type'=>"PL",'icon'=>'fa-male', 'details'=>$flDeet]);}break;
            case 'SPL':{$leaveDetails->push(['type'=>"SPL",'icon'=>'fa-street-view', 'details'=>$flDeet]);}break;       
          
          }

        }else
        {
          switch ($flDeet->leaveType) {
            case 'ML':{$leaveDetails->push(['type'=>"ML denied",'icon'=>'fa-times', 'details'=>$flDeet]);}break;
            case 'MC':{$leaveDetails->push(['type'=>"MC denied",'icon'=>'fa-times', 'details'=>$flDeet]);}break;
            case 'PL':{$leaveDetails->push(['type'=>"PL denied",'icon'=>'fa-times', 'details'=>$flDeet]);}break;
            case 'SPL':{$leaveDetails->push(['type'=>"SPL denied",'icon'=>'fa-times', 'details'=>$flDeet]);}break;       
          
          }

        }
        
        
      }


      // --------- here we check for the new LOG OVERRIDE ---------
      $hasLogOverride = User_LogOverride::where('user_id',$id)->where('productionDate',$thisPayrollDate)->where('logType_id',$logType_id)->orderBy('created_at','DESC')->get();

      if(count($hasLogOverride) > 0)
      {
        $logOverride = $hasLogOverride->first();
        $bioOverride =Biometrics::find($logOverride->affectedBio);
        
        $timing = Carbon::parse($bioOverride->productionDate." ".$logOverride->logTime,'Asia/Manila');
        $log = $timing->format('M d h:i:s A');
        $userLog = new Collection;
        $userLog->push(['id'=>$logOverride->id,'biometrics_id'=>$bioOverride->id,'user_id'=>$id, 'logTime'=>$logOverride->logTime,'logType_id',$logOverride->logType_id,'manual'=>null,'created_at'=>$logOverride->created_at, 'updated_at'=>$logOverride->updated_at]);

        if($hasApprovedDTRP)
        {

          $data->push(['beginShift'=> $beginShift,'biometrics_id'=>$biometrics_id,
                    
                    'dtrp'=>$hasApprovedDTRP->first(),
                    'dtrpIN'=>$dtrpIN, 'dtrpIN_id'=>$dtrpIN_id, 
                    'dtrpOUT'=>$dtrpOUT, 'dtrpOUT_id'=> $dtrpOUT_id,
                    'endShift'=> $endShift,
                    'isAproblemShift'=>$isAproblemShift,
                    'isRDYest'=>$isRDYest,
                    'finishShift'=>$finishShift,
                    'hasLeave'=>$hasLeave,'hasLWOP'=>$hasLWOP, 'hasSL'=>$hasSL, 'hasVTO'=>$hasVTO,
                    'hasPendingDTRP' => $hasPendingDTRP,
                    'leave'=>$leaveDetails,
                    //'leaveStart'=>$fix->format('Y-m-d H:i:s'),'leaveEnd'=>$theDay->format('Y-m-d H:i:s'),
                    'leaveStart'=>$beginShift->format('Y-m-d H:i:s'),'leaveEnd'=>$theDay->format('Y-m-d H:i:s'),
                    'logPalugit'=>$logPalugit,
                    'logs'=>$userLog,'lwop'=>$lwopDetails, 
                    'logTxt'=>$log,
                    'maxIn'=>$maxIn,
                    'maxOut'=> $maxOut,
                    'palugitDate' =>$palugitDate,
                    'pendingDTRP' => $pendingDTRP,
                    'sl'=>$slDeet,
                    'timing'=>$timing,'UT'=>$UT,
                    'vl'=>$vl,
                    'pal'=>$pal,
                    'CHECKER'=>''//$userLog, //$alldaysVL,
                    
                    ]);
        }else
        {
          $data->push(['beginShift'=> $beginShift,'biometrics_id'=>$biometrics_id,
                    
                    'dtrp'=>$hasApprovedDTRP,
                    'dtrpIN'=>$dtrpIN, 'dtrpIN_id'=>$dtrpIN_id, 
                    'dtrpOUT'=>$dtrpOUT, 'dtrpOUT_id'=> $dtrpOUT_id,
                    'endShift'=> $endShift,
                    'isAproblemShift'=>$isAproblemShift,
                    'isRDYest'=>$isRDYest,
                    'finishShift'=>$finishShift,
                    'hasLeave'=>$hasLeave,'hasLWOP'=>$hasLWOP, 'hasSL'=>$hasSL, 'hasVTO'=>$hasVTO,
                    'hasPendingDTRP' => $hasPendingDTRP,
                    'leave'=>$leaveDetails,
                    //'leaveStart'=>$fix->format('Y-m-d H:i:s'),'leaveEnd'=>$theDay->format('Y-m-d H:i:s'),
                    'leaveStart'=>$beginShift->format('Y-m-d H:i:s'),'leaveEnd'=>$theDay->format('Y-m-d H:i:s'),
                    'logPalugit'=>$logPalugit,
                    'logs'=>$userLog,'lwop'=>$lwopDetails, 
                    'logTxt'=>$log,
                    'maxIn'=>$maxIn,
                    'maxOut'=> $maxOut,
                    'palugitDate' =>$palugitDate,
                    'pendingDTRP' => $pendingDTRP,
                    'sl'=>$slDeet,
                    'timing'=>$timing,'UT'=>$UT,
                    'vl'=>$vl,
                    'pal'=>$pal,
                    'CHECKER'=>''//$userLog, //$alldaysVL,
                    
                    ]);

        }

       

      }else
      {
        if($hasApprovedDTRP)
        {
          $data->push(['beginShift'=> $beginShift,'biometrics_id'=>$biometrics_id,
                    
                    'dtrp'=>$hasApprovedDTRP->first(),
                    'dtrpIN'=>$dtrpIN, 'dtrpIN_id'=>$dtrpIN_id, 
                    'dtrpOUT'=>$dtrpOUT, 'dtrpOUT_id'=> $dtrpOUT_id,
                    'endShift'=> $endShift,
                    'isAproblemShift'=>$isAproblemShift,
                    'isRDYest'=>$isRDYest,
                    'finishShift'=>$finishShift,
                    'hasLeave'=>$hasLeave,'hasLWOP'=>$hasLWOP, 'hasSL'=>$hasSL,'hasVTO'=>$hasVTO,
                    'hasPendingDTRP' => $hasPendingDTRP,
                    'leave'=>$leaveDetails,
                    //'leaveStart'=>$fix->format('Y-m-d H:i:s'),'leaveEnd'=>$theDay->format('Y-m-d H:i:s'),
                    'leaveStart'=>$beginShift->format('Y-m-d H:i:s'),'leaveEnd'=>$endShift->format('Y-m-d H:i:s'),
                    'logPalugit'=>$logPalugit,
                    'logs'=>$userLog,'lwop'=>$lwopDetails, 
                    'logTxt'=>$log,
                    'maxIn'=>$maxIn,
                    'maxOut'=> $maxOut,
                    'palugitDate' =>$palugitDate,
                    'pendingDTRP' => $pendingDTRP,
                    'sl'=>$slDeet,
                    'timing'=>$timing,'UT'=>$UT,
                    'vl'=>$vl,
                    'pal'=>$pal,
                    'CHECKER'=>$ob //$ulog1 //$vacationLeave->query, //$alldaysVL,
                    
                    ]);

        }else
          $data->push(['beginShift'=> $beginShift,'biometrics_id'=>$biometrics_id,
                    
                    'dtrp'=>$hasApprovedDTRP,
                    'dtrpIN'=>$dtrpIN, 'dtrpIN_id'=>$dtrpIN_id, 
                    'dtrpOUT'=>$dtrpOUT, 'dtrpOUT_id'=> $dtrpOUT_id,
                    'endShift'=> $endShift,
                    'isAproblemShift'=>$isAproblemShift,
                    'isRDYest'=>$isRDYest,
                    'finishShift'=>$finishShift,
                    'hasLeave'=>$hasLeave,'hasLWOP'=>$hasLWOP, 'hasSL'=>$hasSL,'hasVTO'=>$hasVTO,
                    'hasPendingDTRP' => $hasPendingDTRP,
                    'leave'=>$leaveDetails,
                    //'leaveStart'=>$fix->format('Y-m-d H:i:s'),'leaveEnd'=>$theDay->format('Y-m-d H:i:s'),
                    'leaveStart'=>$beginShift->format('Y-m-d H:i:s'),'leaveEnd'=>$endShift->format('Y-m-d H:i:s'),
                    'logPalugit'=>$logPalugit,
                    'logs'=>$userLog,'lwop'=>$lwopDetails, 
                    'logTxt'=>$log,
                    'maxIn'=>$maxIn,
                    'maxOut'=> $maxOut,
                    'palugitDate' =>$palugitDate,
                    'pendingDTRP' => $pendingDTRP,
                    'sl'=>$slDeet,
                    'timing'=>$timing,'UT'=>$UT,
                    'vl'=>$vl,
                    'pal'=>$pal,
                    'CHECKER'=>$ob //$ulog1 //$vacationLeave->query, //$alldaysVL,
                    
                    ]);
        

       

      }

              

      return $data;
  }


  
 

  

  public function getPayrollPeriod($cutoffStart,$cutoffEnd)
  {
    $payrollPeriod = [];
    for($date = $cutoffStart; $date->lte($cutoffEnd); $date->addDay()) 
         {
            $payrollPeriod[] = $date->format('Y-m-d');
         }

    return $payrollPeriod;


  }


  public function getRDinfo($user_id, $biometrics,$isSameDayLog,$payday, $schedKahapon,$isFixedSched,$isPartTimer)
  {

      /* init $approvedOT */
      $legitRD = 0;
      $approvedOT=0; $OTattribute="";
      $hasHolidayToday = false;
      $hasPendingIN = null;
      $pendingDTRPin = null;
      $hasPendingOUT = null;
      $pendingDTRPout = null;$userLogOUT=null;$logOUT=null;$fromOverride=false;

      $thisPayrollDate = Biometrics::find($biometrics->id)->productionDate;
      $holidayToday = Holiday::where('holidate', $thisPayrollDate)->get();
      (Team::where('user_id',$user_id)->first()->floor_id == 9) ? $isDavao=true : $isDavao=false;

      (Team::where('user_id',$user_id)->first()->floor_id == 10 || Team::where('user_id',$user_id)->first()->floor_id == 11) ? $isTaipei=1 : $isTaipei=0;

      if( count($holidayToday) > 0 )
      {
        $h =  $holidayToday->first();

        if ($h->holidayType_id == 4)
        {
          if($isDavao)
            {
              $holidayToday = $hol; 
              $hasHolidayToday = true;

            } 
            else { $holidayToday=null; }
        }
        elseif($h->holidayType_id == 5) // Taipei holiday
        {

        }
        elseif($h->holidayType_id == 6) // Xiamen holiday
        {

        }
        else {

          if($isTaipei)
            {$holidayToday = null;  $hasHolidayToday = 0;} 
          else
          {$holidayToday = $holidayToday;  $hasHolidayToday = true;} 
        }

      }else
        $holidayToday = $holidayToday; //Holiday::where('holidate', $payday)->get();



      // check first if there's an RD override:
      $hasOverride = User_RDoverride::where('biometrics_id',$biometrics->id)->where('user_id',$user_id)->get();

      if(count($hasOverride) > 0)
      {
        $approvedOT = null; 
        //** fill in necessary details from the approved RD OT
        $shiftStart = "* RD *";
        $shiftEnd = "* RD *";
        $logIN = "* RD *";  //--- di nga sya pumasok
        $logOUT = "* RD *";
        
        $billableForOT = 0;
        $OTattribute = null;
        $UT = 0;
        $hasPendingIN = null;
        $hasApprovedDTRPin=null;
        $pendingDTRPin = null;
        $hasPendingOUT = null;
        $pendingDTRPout = null;
        $userLogIN=null; $userLogOUT=null;

        if($hasHolidayToday) {
          $workedHours = "N/A";
          $workedHours .= "<br /><strong>* " . $holidayToday->first()->name . " * </strong>";

        }else
          $workedHours = "N/A";

        goto pushData;
      }



      /* -- you still have to create module for checking and filing OTs */

      /* --------- check mo muna kung may approved DTRP on this day --------*/
      $hasApprovedDTRPin = User_DTRP::where('user_id',$user_id)->where('isApproved',true)->where('biometrics_id',$biometrics->id)->where('logType_id',1)->orderBy('updated_at','DESC')->get();

      if (count($hasApprovedDTRPin) > 0){ $userLogIN = $hasApprovedDTRPin;} 
      else 
        { 
          $userLogIN = Logs::where('user_id',$user_id)->where('biometrics_id',$biometrics->id)->where('logType_id',1)->orderBy('biometrics_id','ASC')->get(); 
        }



      if (count($userLogIN) == 0)
      {
            $pendingDTRPin = User_DTRP::where('user_id',$user_id)->where('isApproved',null)->where('biometrics_id',$biometrics->id)->where('logType_id',1)->orderBy('updated_at','DESC')->get();
            //--- ** baka naman may DTRP syang di pa approved? 

            if (count($pendingDTRPin) > 0)
            {
              $logIN = "(pending DTRP IN)";
              $hasPendingIN = true;
              $logOUT = "* RD *";
            } else {

              $logIN = "* RD *";  //--- di nga sya pumasok
              $logOUT = "* RD *";

            }
            $shiftStart = "* RD *";
            $shiftEnd = "* RD *";

            if ($hasHolidayToday)
              {
                

                if($holidayToday->first()->holidayType_id == 4)
                {
                  if($isDavao)
                  {
                    $workedHours = "(8.0) <br/><strong> * "; 
                    $workedHours .= $holidayToday->first()->name." *</strong>";
                  }
                  else
                  {
                    $workedHours="N/A"; 
                  }
                  
                  
                }else
                {
                  $workedHours = "(8.0) <br/><strong> * "; 
                  $workedHours .= $holidayToday->first()->name." *</strong>";
                }
               
                

              } else $workedHours="N/A"; 

              $UT = 0;
              $billableForOT=0;

      } 
      else
      {
          

          //--------------- RD REWORK ----------------------------------------------
          // get login, kung meron then get log out
          // if both meron, then legit RDOT + OPTION to disregard logs
          // if no OUT, then check for max allowed out of say 12HRs?
          //       if wala pa rin out, then NO RDOT OUT. Verify with Immediate Head for logs
          //       else, legit RDOT
          // else RD lang

          $logIN = Carbon::parse($thisPayrollDate." ".$userLogIN->first()->logTime,'Asia/Manila')->format('M d h:i:s A');
          //date('h:i:s A',strtotime($userLogIN->first()->logTime));
          $timeStart = Carbon::parse($payday." ".$userLogIN->first()->logTime,'Asia/Manila');

          //--------- dito papasok yung overrride
          // -------- check mo muna baka may manual override eh
          $manual = User_LogOverride::where('user_id',$user_id)->where('logType_id',2)->where('productionDate',$biometrics->productionDate)->get();

          if (count($manual) > 0) {$userLogOUT = $manual; $fromOverride=true;}
          else
          {
            $userLogOUT = Logs::where('user_id',$user_id)->where('biometrics_id',$biometrics->id)->where('logType_id',2)->orderBy('biometrics_id','ASC')->get();
          }
          

          if (count($userLogOUT) > 0) 
          {
            
            goto proceedToRDOT;

          } else
          {
            // we check for maximum allowed out of 12HRs from start of OT
            $maxOTOut = Carbon::parse($thisPayrollDate.' '.$userLogIN->first()->logTime,'Asia/Manila')->addHours(12);
            $bOut = Biometrics::where('productionDate',$maxOTOut->format('Y-m-d'))->get();

            if (count($bOut) > 0)
            {
              $userLogOUT = Logs::where('user_id',$user_id)->where('biometrics_id',$bOut->first()->id)->where('logType_id',2)->orderBy('biometrics_id','ASC')->get();

              if (count($userLogOUT) > 0)
              {
                goto proceedToRDOT;

              }else goto mayDTRPOUT;
              
              /*
              {
                $logOUT = "No RD-OT Out <br/><small>Verify with Immediate Head</small>";
                $workedHours="N/A"; 

                //if ($hasHolidayToday){ $workedHours .= "<br /><strong>* " . $holidayToday->first()->name." * </strong>"; }      
                $shiftStart = "* RD *";
                $shiftEnd = "* RD *";
                $UT = 0;
                $billableForOT=0;

                //check first if Locked na DTR for that production date
                $verifiedDTR = User_DTR::where('productionDate',$payday)->where('user_id',$user_id)->get();
                (count($verifiedDTR) > 0) ? $isLocked=true : $isLocked=false;

                if ($hasHolidayToday)
                 {
                    //check first if Locked na DTR for that production date
                    $workedHours .= "<br /><strong>* " . $holidayToday->first()->name . " * </strong>";
                    if($isLocked)
                      $icons = "<a title=\"Unlock DTR to file this HD-OT\" class=\"pull-right text-gray\" style=\"font-size:1.2em;\"><i class=\"fa fa-credit-card\"></i></a>";
                    else
                     $icons = "<a id=\"OT_".$payday."\"  data-toggle=\"modal\" data-target=\"#myModal_OT".$payday."\"  title=\"File this Holiday OT\" class=\"pull-right\" style=\"font-size:1.2em;\" href=\"#\"><i class=\"fa fa-credit-card\"></i></a>";

                 
                 }
                 
                 else
                 {
                    
                    if($isLocked) 
                    {
                      $workedHours .= "<br /><small> [* RD-OT *] </small> &nbsp;&nbsp;<a title='Unlock first to mark this as actual RD' class='btn btn-xs btn-default'><i class='fa fa-bed'></i> </a>";
                      $icons = "<a title=\"Unlock DTR to file this RD-OT\" class=\"pull-right text-gray\" style=\"font-size:1.2em;\"><i class=\"fa fa-credit-card\"></i></a>";
                    }
                    else {
                      $workedHours .= "<br /><small> [* RD-OT *] </small> &nbsp;&nbsp;<a data-toggle=\"modal\" data-target=\"#myModal_bypass_".$biometrics->id."\"   title='Mark as REST DAY' class='actualRD btn btn-xs btn-danger'><i class='fa fa-bed'></i> </a>";
                      $icons = "<a id=\"OT_".$payday."\"  data-toggle=\"modal\" data-target=\"#myModal_OT".$payday."\"  title=\"File this RD-OT\" class=\"pull-right\" style=\"font-size:1.2em;\" href=\"#\"><i class=\"fa fa-credit-card\"></i></a>";
                    }

                    
                 }
                
                $OTattribute = $icons;

                goto pushData;
                         


              }*/


            }else
            {
              //Check mo muna kung may approved DTRPout
              mayDTRPOUT:

              $hasApprovedDTRPout = User_DTRP::where('user_id',$user_id)->where('isApproved',true)->where('biometrics_id',$biometrics->id)->where('logType_id',2)->orderBy('updated_at','DESC')->get();

              if (count($hasApprovedDTRPout) > 0) { $userLogOUT = $hasApprovedDTRPout;} 
              else 
              {
                  $pendingDTRPout = User_DTRP::where('user_id',$user_id)->where('isApproved',null)->where('biometrics_id',$biometrics->id)->where('logType_id',2)->orderBy('updated_at','DESC')->get();//--- ** baka naman may DTRP syang di pa approved? 

                  if (count($pendingDTRPout) > 0)
                  {
                    $logOUT = "(pending)";
                    $hasPendingOUT = true;
                    $userLogOUT = $pendingDTRPout;

                  } else 
                  {
                    goto blankOTout;
                    
                  }

              }

              
            }
            

          }//end if no logout


          proceedToRDOT:

            //--- ** May issue: pano kung RD OT ng gabi, then kinabukasan na sya nag LogOUT. Need to check kung may approved OT from IH
            $rdOT = User_OT::where('biometrics_id',$biometrics->id)->where('user_id',$user_id)->get();
            
            if (count($rdOT) > 0)
            {
              if ($rdOT->first()->isApproved)
              {
                $approvedOT = $rdOT; 
                //** fill in necessary details from the approved RD OT
                $shiftStart = "* RD *";
                $shiftEnd = "* RD *";
                $logIN = $rdOT->first()->timeStart;
                $logOUT = $rdOT->first()->timeEnd;
                $workedHours = $rdOT->first()->filed_hours."<br/><small>[ * RD-OT * ]</small>";
                $billableForOT = $rdOT->first()->billable_hours;
                $OTattribute = null;
                $UT = 0;
                $hasPendingIN = null;
                $pendingDTRPin = null;
                $hasPendingOUT = null;
                $pendingDTRPout = null;

              }else
              {
                $approvedOT = $rdOT; 
                //** fill in necessary details from the approved RD OT
                $shiftStart = "* RD *";
                $shiftEnd = "* RD *";
                $logIN = $rdOT->first()->timeStart;
                $logOUT = $rdOT->first()->timeEnd;
                $workedHours = "0<br/><small>[ * RD-OT * ]</small>";
                $billableForOT = $rdOT->first()->billable_hours;
                $OTattribute = null;
                $UT = 0;
                $hasPendingIN = null;
                $pendingDTRPin = null;
                $hasPendingOUT = null;
                $pendingDTRPout = null;
              }
              

            }
            else
            {
                if( is_null($userLogOUT) || count($userLogOUT) == 0 )
                {

                    blankOTout:

                        $logOUT = "No RD-OT Out <br/><small>Verify with Immediate Head</small>";
                        $workedHours="N/A"; 

                        //if ($hasHolidayToday){ $workedHours .= "<br /><strong>* " . $holidayToday->first()->name." * </strong>"; }      
                        $shiftStart = "* RD *";
                        $shiftEnd = "* RD *";
                        $UT = 0;
                        $billableForOT=0;

                        //check first if Locked na DTR for that production date
                        $verifiedDTR = User_DTR::where('productionDate',$payday)->where('user_id',$user_id)->get();
                        (count($verifiedDTR) > 0) ? $isLocked=true : $isLocked=false;

                        if ($hasHolidayToday)
                         {
                            //check first if Locked na DTR for that production date
                            $workedHours .= "<br /><strong>* " . $holidayToday->first()->name . " * </strong>";
                            if($isLocked)
                              $icons = "<a title=\"Unlock DTR to file this HD-OT\" class=\"pull-right text-gray\" style=\"font-size:1.2em;\"><i class=\"fa fa-credit-card\"></i></a>";
                            else
                             $icons = "<a id=\"OT_".$payday."\"  data-toggle=\"modal\" data-target=\"#myModal_OT".$payday."\"  title=\"File this Holiday OT\" class=\"pull-right\" style=\"font-size:1.2em;\" href=\"#\"><i class=\"fa fa-credit-card\"></i></a>";

                         
                         }
                         
                         else
                         {
                            
                            if($isLocked) 
                            {
                              $workedHours .= "<br /><small> [* RD-OT *] </small> &nbsp;&nbsp;<a title='Unlock first to mark this as actual RD' class='btn btn-xs btn-default'><i class='fa fa-bed'></i> </a>";
                              $icons = "<a title=\"Unlock DTR to file this RD-OT\" class=\"pull-right text-gray\" style=\"font-size:1.2em;\"><i class=\"fa fa-credit-card\"></i></a>";
                            }
                            else {
                              $workedHours .= "<br /><small> [* RD-OT *] </small> &nbsp;&nbsp;<a data-toggle=\"modal\" data-target=\"#myModal_bypass_".$biometrics->id."\"   title='Mark as REST DAY' class='actualRD btn btn-xs btn-danger'><i class='fa fa-bed'></i> </a>";
                              $icons = "<a id=\"OT_".$payday."\"  data-toggle=\"modal\" data-target=\"#myModal_OT".$payday."\"  title=\"File this RD-OT\" class=\"pull-right\" style=\"font-size:1.2em;\" href=\"#\"><i class=\"fa fa-credit-card\"></i></a>";
                            }

                            
                         }
                        
                        $OTattribute = $icons;

            
                   
                    

                } else 
                { 

                  legitOT:
                          //--- legit OT, compute billable hours
                          //--- check mo muna kung normal or night diff logtype sya
                          //*** pero check mo muna kung may existing userlogs talaga
                          if(count($userLogIN) > 0){
                            
                           
                              if( $userLogOUT->first()->logTime > $userLogIN->first()->logTime)
                              {
                                ($fromOverride) ? $bio = Biometrics::find($userLogOUT->first()->affectedBio)->productionDate : $bio = Biometrics::find($userLogOUT->first()->biometrics_id)->productionDate; //->productionDate;
                              }
                              

                              else
                              {
                                $nextDay = Carbon::parse($payday)->addDay();
                                $b = Biometrics::where('productionDate',$nextDay->format('Y-m-d'))->get();
                                if (count($b) > 0)
                                  $bio = $b->first()->productionDate;
                                else $bio = Biometrics::find($userLogOUT->first()->biometrics_id)->productionDate;
                              }

                              

                           
                            
                            

                          }else{

                            if( $userLogOUT->first()->logTime > $hasApprovedDTRPin->first()->logTime)
                            $bio =Biometrics::find($userLogOUT->first()->biometrics_id)->productionDate;
                            else
                            {
                              $nextDay = Carbon::parse($payday)->addDay();
                              $b = Biometrics::where('productionDate',$nextDay->format('Y-m-d'))->get();
                              if (count($b) > 0)
                                $bio = $b->first()->productionDate;
                              else $bio = Biometrics::find($userLogOUT->first()->biometrics_id)->productionDate;
                            }



                          }
                          

                          
                          $logO = Carbon::parse($bio." ".$userLogOUT->first()->logTime, 'Asia/Manila'); 
                          $logOUT = $logO->format('M d h:i:s A'); 

                          //$timeStart = Carbon::parse($thisPayrollDate." ".$userLogIN->first()->logTime,'Asia/Manila');
                          $timeStart = Carbon::parse(Biometrics::find($userLogIN->first()->biometrics_id)->productionDate." ".$userLogIN->first()->logTime,'Asia/Manila'); 
                          //Carbon::parse($thisPayrollDate." 20:20:20",'Asia/Manila');
                         
                          $timeEnd = Carbon::parse($payday." ".$userLogOUT->first()->logTime, 'Asia/Manila');

                          $mindiff = $timeStart->diffInMinutes($logO);

                          //*** if RD OT hrs > 5, less 1hr break

                          if ($mindiff >= 300) $wh = $logO->diffInMinutes($timeStart->addHour(1));
                          else  $wh = $logO->diffInMinutes($timeStart); 
                          $workedHours = number_format($wh/60,2);

                          
                          //if ($workedHours > 5) $workedHours = $workedHours-1;

                          $billableForOT = $workedHours;

                          //check first if Locked na DTR for that production date
                          $verifiedDTR = User_DTR::where('productionDate',$payday)->where('user_id',$user_id)->get();
                          (count($verifiedDTR) > 0) ? $isLocked=true : $isLocked=false;

                         
                          if ($hasHolidayToday)
                          {
                            

                            if($holidayToday->first()->holidayType_id == 4)
                            {
                              if($isDavao)
                              {
                                $workedHours = "(8.0) <br/><strong> * "; 
                                $workedHours .= $holidayToday->first()->name." *</strong>";
                              }
                              else
                              {
                                $workedHours="N/A"; 
                              }
                              
                              
                            }else
                            {
                              $workedHours = "(8.0) <br/><strong> * "; 
                              $workedHours .= $holidayToday->first()->name." *</strong>";
                            }
                           
                            

                          } else $workedHours="N/A"; 

                           if ($hasHolidayToday)
                           {
                              if($holidayToday->first()->holidayType_id == 4)
                              {
                                if($isDavao)
                                {
                                  $workedHours = "(8.0) <br/><strong> * "; 
                                  $workedHours .= $holidayToday->first()->name." *</strong>";
                                  if($isLocked)
                                  $icons = "<a title=\"Unlock DTR to file this HD-OT\" class=\"pull-right text-gray\" style=\"font-size:1.2em;\"><i class=\"fa fa-credit-card\"></i></a>";
                                  else
                                   $icons = "<a id=\"OT_".$payday."\"  data-toggle=\"modal\" data-target=\"#myModal_OT".$payday."\"  title=\"File this Holiday OT\" class=\"pull-right\" style=\"font-size:1.2em;\" href=\"#\"><i class=\"fa fa-credit-card\"></i></a>";
                                }
                                else
                                {
                                  $workedHours="N/A"; 
                                  $workedHours .= "<br /><small> [* RD-OT *] </small> &nbsp;&nbsp;<a data-toggle=\"modal\" data-target=\"#myModal_bypass_".$biometrics->id."\"   title='Mark as REST DAY' class='actualRD btn btn-xs btn-danger'><i class='fa fa-bed'></i> </a>";
                                $icons = "<a id=\"OT_".$payday."\"  data-toggle=\"modal\" data-target=\"#myModal_OT".$payday."\"  title=\"File this RD-OT\" class=\"pull-right\" style=\"font-size:1.2em;\" href=\"#\"><i class=\"fa fa-credit-card\"></i></a>";

                                }
                                
                                
                              }else
                              {
                                //check first if Locked na DTR for that production date
                                $workedHours .= "<br /><strong>* " . $holidayToday->first()->name . " * </strong>";
                                $workedHours .= "<br /><small> [* RD-OT *] </small> &nbsp;&nbsp;<a data-toggle=\"modal\" data-target=\"#myModal_bypass_".$biometrics->id."\"   title='Mark as REST DAY' class='actualRD btn btn-xs btn-danger'><i class='fa fa-bed'></i> </a>";
                                

                                 if($isLocked)
                                  $icons = "<a title=\"Unlock DTR to file this HD-OT\" class=\"pull-right text-gray\" style=\"font-size:1.2em;\"><i class=\"fa fa-credit-card\"></i></a>";
                                  else{
                                    $icons = "<a id=\"OT_".$payday."\"  data-toggle=\"modal\" data-target=\"#myModal_OT".$payday."\"  title=\"File this Holiday RD-OT\" class=\"pull-right\" style=\"font-size:1.2em;\" href=\"#\"><i class=\"fa fa-credit-card\"></i></a>";
                                    // $icons = "<a id=\"OT_".$payday."\"  data-toggle=\"modal\" data-target=\"#myModal_OT".$payday."\"  title=\"File this Holiday OT\" class=\"pull-right\" style=\"font-size:1.2em;\" href=\"#\"><i class=\"fa fa-credit-card\"></i></a>";
                                  }
                                


                              }

                                
                           
                           }
                           
                           else
                           {
                              
                              if($isLocked) 
                              {
                                $workedHours .= "<br /><small> [* RD-OT *] </small> &nbsp;&nbsp;<a title='Unlock first to mark this as actual RD' class='btn btn-xs btn-default'><i class='fa fa-bed'></i> </a>";
                                $icons = "<a title=\"Unlock DTR to file this RD-OT\" class=\"pull-right text-gray\" style=\"font-size:1.2em;\"><i class=\"fa fa-credit-card\"></i></a>";
                              }
                              else {
                                $workedHours .= "<br /><small> [* RD-OT *] </small> &nbsp;&nbsp;<a data-toggle=\"modal\" data-target=\"#myModal_bypass_".$biometrics->id."\"   title='Mark as REST DAY' class='actualRD btn btn-xs btn-danger'><i class='fa fa-bed'></i> </a>";
                                $icons = "<a id=\"OT_".$payday."\"  data-toggle=\"modal\" data-target=\"#myModal_OT".$payday."\"  title=\"File this RD-OT\" class=\"pull-right\" style=\"font-size:1.2em;\" href=\"#\"><i class=\"fa fa-credit-card\"></i></a>";
                              }

                              
                           }
                          
                          $OTattribute = $icons;
                          $shiftStart = "* RD *";
                          $shiftEnd = "* RD *";
                          $UT = 0;
                }//end if-else null logout

            }//wala pang approved RD OT 


            


          //--------------- END REWORK ----------------------------------------------

       }//end if may login kahit RD

       pushData:

       $logOverrideIN=null; $logOverrideOUT=null;
       $hasLogOverrideIN = User_LogOverride::where('user_id',$user_id)->where('productionDate',$thisPayrollDate)->where('logType_id',1)->orderBy('created_at','DESC')->get();
       $hasLogOverrideOUT = User_LogOverride::where('user_id',$user_id)->where('productionDate',$thisPayrollDate)->where('logType_id',2)->orderBy('created_at','DESC')->get();
       // $hasLogOverrideIN = User_LogOverride::where('user_id',$user_id)->where('affectedBio',$biometrics->id)->where('logType_id',1)->get();
       // $hasLogOverrideOUT = User_LogOverride::where('user_id',$user_id)->where('affectedBio',$biometrics->id)->where('logType_id',2)->get();

        if(count($hasLogOverrideIN) > 0)
        {
          $logOverrideIN = $hasLogOverrideIN->first();
          $affectedBioIN = Biometrics::find($logOverrideIN->affectedBio);
          $lIN = Carbon::parse($affectedBioIN->productionDate.' '.$logOverrideIN->logTime,'Asia/Manila');
          $logIN =$lIN->format('M d H:i:s'); //$lIN->format('M d H:i:s A');
        }
        if(count($hasLogOverrideOUT) > 0)
        {
          $logOverrideOUT = $hasLogOverrideOUT->first();
          $affectedBioOUT = Biometrics::find($logOverrideOUT->affectedBio);
          $lOUT = Carbon::parse($affectedBioOUT->productionDate.' '.$logOverrideOUT->logTime,'Asia/Manila');
          $logOUT = $lOUT->format('M d H:i:s'); //->format('M d H:i:s A');
        }

        if ($logOverrideIN && $logOverrideOUT)
        {
          $workedHours = number_format($lIN->diffInMinutes($lOUT)/60,2);
          $wh=0;//workedHours;

          if ((float)$workedHours >= 5.0) $wh = (float)$workedHours-1;
          else $wh = $workedHours;

          $verifiedDTR = User_DTR::where('productionDate',$payday)->where('user_id',$user_id)->get();
          (count($verifiedDTR) > 0) ? $isLocked=true : $isLocked=false;
           
           if ($hasHolidayToday)
           {
              //check first if Locked na DTR for that production date
              $workedHours .= "<br /><strong>* " . $holidayToday->first()->name . " * </strong>";
              //$wh .= "<br /><strong>* " . $holidayToday->first()->name . " * </strong>";
              if($isLocked)
                $icons = "<a title=\"Unlock DTR to file this HD-OT\" class=\"pull-right text-gray\" style=\"font-size:1.2em;\"><i class=\"fa fa-credit-card\"></i></a>";
              else
               $icons = "<a id=\"OT_".$payday."\"  data-toggle=\"modal\" data-target=\"#myModal_OT".$payday."\"  title=\"File this Holiday OT\" class=\"pull-right\" style=\"font-size:1.2em;\" href=\"#\"><i class=\"fa fa-credit-card\"></i></a>";

           
           }
           
           else
           {
              
              if($isLocked) 
              {
                $workedHours .= "<br /><small> [* RD-OT *] </small> &nbsp;&nbsp;<a title='Unlock first to mark this as actual RD' class='btn btn-xs btn-default'><i class='fa fa-bed'></i> </a>";
                $icons = "<a title=\"Unlock DTR to file this RD-OT\" class=\"pull-right text-gray\" style=\"font-size:1.2em;\"><i class=\"fa fa-credit-card\"></i></a>";
              }
              else {
                $workedHours .= "<br /><small> [* RD-OT *] </small> &nbsp;&nbsp;<a data-toggle=\"modal\" data-target=\"#myModal_bypass_".$biometrics->id."\"   title='Mark as REST DAY' class='actualRD btn btn-xs btn-danger'><i class='fa fa-bed'></i> </a>";
                //$wh .= "<br /><small> [* RD-OT *] </small> &nbsp;&nbsp;<a data-toggle=\"modal\" data-target=\"#myModal_bypass_".$biometrics->id."\"   title='Mark as REST DAY' class='actualRD btn btn-xs btn-danger'><i class='fa fa-bed'></i> </a>";
                $icons = "<a id=\"OT_".$payday."\"  data-toggle=\"modal\" data-target=\"#myModal_OT".$payday."\"  title=\"File this RD-OT\" class=\"pull-right\" style=\"font-size:1.2em;\" href=\"#\"><i class=\"fa fa-credit-card\"></i></a>";
              }

              
           }
        
          $OTattribute = $icons;
          $shiftStart = "* RD *";
          $shiftEnd = "* RD *";
          $UT = 0;

          $collIN = new Collection;
          $collIN->push(['id'=>$logOverrideIN->id, 'biometrics_id'=>$logOverrideIN->affectedBio,'user_id'=>$user_id,'logTime'=>$logOverrideIN->logTime, 'logType_id'=>$logOverrideIN->logType_id,'manual'=>null,'created_at'=>$logOverrideIN->created_at,'updated_at'=>$logOverrideIN->updated_at]);
          $collOUT = new Collection;
          $collOUT->push(['id'=>$logOverrideOUT->id, 'biometrics_id'=>$logOverrideOUT->affectedBio,'user_id'=>$user_id,'logTime'=>$logOverrideOUT->logTime, 'logType_id'=>$logOverrideOUT->logType_id,'manual'=>null,'created_at'=>$logOverrideOUT->created_at,'updated_at'=>$logOverrideOUT->updated_at]);

          $rdOT = User_OT::where('biometrics_id',$biometrics->id)->where('user_id',$user_id)->get();
          if (count($rdOT) > 0)
          {
              if ($rdOT->first()->isApproved)
              {
                $approvedOT = $rdOT; 
                //** fill in necessary details from the approved RD OT
                // $shiftStart = "* RD *";
                // $shiftEnd = "* RD *";
                // $logIN = $rdOT->first()->timeStart;
                // $logOUT = $rdOT->first()->timeEnd;
                // $workedHours = $rdOT->first()->filed_hours."<br/><small>[ * RD-OT * ]</small>";
                // $billableForOT = $rdOT->first()->billable_hours;
                // $OTattribute = null;
                // $UT = 0;
                // $hasPendingIN = null;
                // $pendingDTRPin = null;
                // $hasPendingOUT = null;
                // $pendingDTRPout = null;

              }else
              {
                $approvedOT = $rdOT; 
                //** fill in necessary details from the approved RD OT
                // $shiftStart = "* RD *";
                // $shiftEnd = "* RD *";
                // $logIN = $rdOT->first()->timeStart;
                // $logOUT = $rdOT->first()->timeEnd;
                // $workedHours = "0<br/><small>[ * RD-OT * ]</small>";
                // $billableForOT = $rdOT->first()->billable_hours;
                // $OTattribute = null;
                // $UT = 0;
                // $hasPendingIN = null;
                // $pendingDTRPin = null;
                // $hasPendingOUT = null;
                // $pendingDTRPout = null;
              }
              

          }

          $data = new Collection;
          $data->push([
            'biometric_id'=>$biometrics->id,
            'shiftStart'=>$shiftStart, 
            'shiftEnd'=>$shiftEnd, 'logIN'=>$logIN, 
            'logOUT'=>$logOUT,'workedHours'=>$wh."<br/><small>[ * RD-OT * ]</small>", 
            'userLogIN'=>$collIN,
            'hasApprovedDTRPin'=>$hasApprovedDTRPin,
            'userLogOUT'=>$collOUT,'isSameDayLog'=>$isSameDayLog,
            'billableForOT'=>$wh,
            'OTattribute'=>$OTattribute, 'UT'=>$UT, 
            'dtrpIN'=>null,'dtrpIN_id'=>null, 'dtrpOUT'=>null,'dtrpOUT_id'=>null,
            'hasPendingIN' => $hasPendingIN,
            'pendingDTRPin'=> $pendingDTRPin,
            'hasPendingOUT' => $hasPendingOUT,
            'pendingDTRPout' => $pendingDTRPout,
            'schedKahapon' => $schedKahapon,
            'approvedOT'=>$approvedOT]);



            
            


        }
        else
        {
          $rdOT = User_OT::where('biometrics_id',$biometrics->id)->where('user_id',$user_id)->get();
          if (count($rdOT) > 0)
          {
              if ($rdOT->first()->isApproved)
              {
                $approvedOT = $rdOT; 

              }else
              {
                $approvedOT = $rdOT; 
              }
              

          }
           $data = new Collection;
           $data->push([
            'biometric_id'=>$biometrics->id,
            'shiftStart'=>$shiftStart, 
            'shiftEnd'=>$shiftEnd, 'logIN'=>$logIN, 
            'logOUT'=>$logOUT,'workedHours'=>$workedHours, 
            'userLogIN'=>$userLogIN,
            'hasApprovedDTRPin'=>$hasApprovedDTRPin,
            'userLogOUT'=>$userLogOUT,'isSameDayLog'=>$isSameDayLog,
            'billableForOT'=>$billableForOT, 'OTattribute'=>$OTattribute, 'UT'=>$UT, 
            'dtrpIN'=>null,'dtrpIN_id'=>null, 'dtrpOUT'=>null,'dtrpOUT_id'=>null,
            'hasPendingIN' => $hasPendingIN,
            'pendingDTRPin'=> $pendingDTRPin,
            'hasPendingOUT' => $hasPendingOUT,
            'pendingDTRPout' => $pendingDTRPout,
            'schedKahapon' => $schedKahapon,
            'approvedOT'=>$approvedOT]);

        }

       
       return $data;


   
  }


  public function getShiftingSchedules2($sched, $coll,$counter,$productionDate)
  {

    //return ['sched'=>$sched];

    //check first if may approved CWS
    $bio = Biometrics::where('productionDate',$productionDate->format('Y-m-d'))->get();
    $prodDate = Carbon::parse($productionDate->format('Y-m-d'),"Asia/Manila");

    if ($prodDate->isPast() && !$prodDate->isToday()) {
      $bgcolor = "#e6e6e6";
      $border = "#e6e6e6";
      $startColor = "#7b898e"; $endColor = "#7b898e";
    } else {
      $bgcolor="#fff"; $border="#fff";$startColor = "#548807"; $endColor = "#bd3310";
    }


    if ( !($bio->isEmpty() ) ){
      $cws = User_CWS::where('biometrics_id',$bio->first()->id)->where('user_id',$sched->user_id)->where('isApproved',1)->orderBy('updated_at','DESC')->get();

          if ( !($cws->isEmpty()) ){

            //check mo muna kung alin mas recent between the sched and cws
            if ($cws->first()->created_at > $sched->created_at){
              //check mo muna kung RD

              if ($cws->first()->timeStart == $cws->first()->timeEnd)
              {
                 //means 00:00:00 to 00:00:00
                 
                $correctTime = Carbon::parse($productionDate->format('Y-m-d') . " 00:00:00","Asia/Manila");

                 $coll->push(['title'=>'Rest day ',
                                'start'=>$productionDate->format('Y-m-d') . " 00:00:00", // dates->format('Y-m-d H:i:s'),
                                //'end'=>$sched->productionDate . " 00:00:00",
                                'end'=>$correctTime->addHour(9)->format('Y-m-d H:i:s'),
                                'textColor'=> '#ccc',
                                'backgroundColor'=> '#fff',
                                'chenes'=>$productionDate->format('Y-m-d'),'icon'=>" ", 'biometrics_id'=>$bio->first()->id
                                 ]);
                 $coll->push(['title'=>'..',
                                'start'=>$productionDate->format('Y-m-d') . " 00:00:00", // dates->format('Y-m-d H:i:s'),
                                'end'=>$correctTime->format('Y-m-d H:i:s'),
                                //'end'=>$sched->productionDate . " 00:00:00",
                                'textColor'=> '#fff',
                                'borderColor'=>$border,
                                'backgroundColor'=> '#e6e6e6',
                                'chenes'=>$productionDate->format('Y-m-d'),'icon2'=>"bed", 'biometrics_id'=>$bio->first()->id
                                 ]);



              }
              else {

                $correctTime = Carbon::parse($productionDate->format('Y-m-d') . " ".$cws->first()->timeStart,"Asia/Manila");

                $coll->push(['title'=> date('h:i A', strtotime($cws->first()->timeStart)) . " to ",// '09:00 AM ',
                          'start'=>$productionDate->format('Y-m-d') . " ".$cws->first()->timeStart, //. $sched->timeStart, //->format('Y-m-d H:i:s'),
                          //'end'=>$sched->productionDate . " ".$cws->first()->timeEnd,
                          'end'=>$correctTime->addHour(9)->format('Y-m-d H:i:s'),
                          'textColor'=> $startColor,// '#548807',// '#409c45',
                          'backgroundColor'=> $bgcolor,
                        'chenes'=>$productionDate->format('Y-m-d'),
                        'counter'=>$counter,
                        'icon'=>"play-circle",
                        'biometrics_id'=> $bio->first()->id]);
                 $coll->push(['title'=>date('h:i A', strtotime($cws->first()->timeEnd)),
                                      'start'=>$productionDate->format('Y-m-d') . " ".$cws->first()->timeStart, //. $sched->timeEnd,
                                      'textColor'=>  $endColor, //'#bd3310',// '#27a7f7',
                                      'backgroundColor'=> $bgcolor,
                                    'chenes'=>$productionDate->format('Y-m-d'),
                                    'counter'=>$counter+1,
                                  'icon'=>"stop-circle",
                                'biometrics_id'=> $bio->first()->id]);

              }

            } else{
              // else, get the sched not the cws
              goto proceedToSchedules;
            }
          } 
          else {
            //else, get the sched
            goto proceedToSchedules;
          }
    }
    else
    {
      proceedToSchedules:

          if($sched->isRD){
            $coll->push(['title'=>'Rest day ',
                                'start'=>$productionDate->format('Y-m-d') . " 00:00:00", // dates->format('Y-m-d H:i:s'),
                                'end'=>$productionDate->format('Y-m-d') . " 00:00:00", 
                                'textColor'=> '#ccc',
                                'backgroundColor'=> '#fff',
                                'chenes'=>$productionDate->format('Y-m-d'),'icon'=>" ", 'biometrics_id'=>null
                                 ]);
            $coll->push(['title'=>'..',
                                'start'=>$productionDate->format('Y-m-d') . " 00:00:00", // dates->format('Y-m-d H:i:s'),
                                'textColor'=> '#fff',
                                'borderColor'=>$border,
                                'backgroundColor'=> '#e6e6e6',
                                'chenes'=>$productionDate->format('Y-m-d'),'icon2'=>"bed", 'biometrics_id'=>null
                                 ]);



          } else{

            $correctTime = Carbon::parse($productionDate->format('Y-m-d') . " ".$sched->timeStart,"Asia/Manila");

           $coll->push(['title'=> date('h:i A', strtotime($sched->timeStart)) . " to ",// '09:00 AM ',
                                'start'=>$productionDate->format('Y-m-d') . " ".$sched->timeStart,//. $sched->timeStart, //->format('Y-m-d H:i:s'),
                                //'end'=>$sched->productionDate . " ".$sched->timeEnd,
                                'end'=>$correctTime->addHour(9)->format('Y-m-d H:i:s'),
                                'borderColor'=>$bgcolor,
                                'textColor'=> '#548807',// '#409c45',
                                'backgroundColor'=> $bgcolor,
                              'chenes'=>$productionDate->format('Y-m-d'),
                              'counter'=>$counter,'icon'=>"play-circle", 'biometrics_id'=>null]);
           $coll->push(['title'=>date('h:i A', strtotime($sched->timeEnd)),
                                  'start'=>$productionDate->format('Y-m-d') . " ".$sched->timeStart,//. $sched->timeEnd,
                                  //'end'=>$sched->productionDate . " ".$sched->timeEnd,
                                  'end'=>$correctTime->format('Y-m-d H:i:s'),
                                  'textColor'=> '#bd3310',// '#27a7f7',
                                  'backgroundColor'=> $bgcolor,
                                  'borderColor'=>$bgcolor,
                                'chenes'=>$productionDate->format('Y-m-d'),
                                'counter'=>$counter+1,'icon'=>"stop-circle", 'biometrics_id'=>null]);

                             

          }

    }



     return $coll;

  }

  public function getShiftingSchedules($workSched,$endDate,$RDsched,$coll,$counter)
  {
    //-------------- ELSE SHIFTING SCHED ----------------------
     $ws = $workSched->groupBy('productionDate');
     $data = new Collection;

      foreach ($ws as $key) {

        $keys = $key->sortByDesc('created_at')->first();
        if($keys->productionDate <= $endDate->format('Y-m-d'))
        {

          //eliminate dupes
          $dupes = $RDsched->where('productionDate', $keys->productionDate)->sortByDesc('id');
          
          if(count($dupes) > 0 ){ //meaning may sched na tagged as workDay pero RD dapat

            //check mo muna which one is more current, RD or workDay ba sya?
               if ($dupes->first()->created_at > $keys->created_at) {
                $coll->push(['title'=>'Rest day ',
                      'start'=>$keys->productionDate." 00:00:00", // dates->format('Y-m-d H:i:s'),
                      'textColor'=> '#ccc',
                      'backgroundColor'=> '#fff',
                      'chenes'=>$keys->productionDate]);
              } else 
              {
                  $coll->push(['title'=> date('h:i A', strtotime($keys->timeStart)) . " to ",// '09:00 AM ',
                          'start'=>$keys->productionDate . " ". $keys->timeStart, //->format('Y-m-d H:i:s'),
                          'textColor'=> '#548807',// '#409c45',
                          'backgroundColor'=> '#fff',
                        'chenes'=>$keys->productionDate]);
                  $coll->push(['title'=>date('h:i A', strtotime($keys->timeEnd)),
                            'start'=>$keys->productionDate . " ". $keys->timeEnd,
                            'textColor'=> '#bd3310',// '#27a7f7',
                            'backgroundColor'=> '#fff',
                          'chenes'=>$keys->productionDate]);

              }
              

           } else {
            


            $coll->push(['title'=> date('h:i A', strtotime($keys->timeStart)) . " to ",// '09:00 AM ',
                    'start'=>$keys->productionDate . " ". $keys->timeStart, //->format('Y-m-d H:i:s'),
                    'end' =>$keys->productionDate . " ". $keys->timeEnd,
                    'textColor'=> '#548807',// '#409c45',
                    'backgroundColor'=> '#fff',
                  'chenes'=>$keys->productionDate]);
            




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
              $coll->push(['title'=>'Rest day ',
                      'start'=>$key->first()->productionDate." 00:00:00", // dates->format('Y-m-d H:i:s'),
                      'textColor'=> '#ccc',
                      'backgroundColor'=> '#fff',
                    'chenes'=>$key->first()->productionDate]);

            }

              

          }
          
        } //end foreach restday



     //-------------- ELSE SHIFTING SCHED ----------------------

    return $coll;
  }


  public function getTLapprover($user_id, $leader_id)
  {

    $user = User::find($user_id);
    $leader = User::find($leader_id);
    $TLapprover = null;
    //$coll = new Collection;

    foreach ($user->approvers as $approver) {
      $tl = ImmediateHead::find(ImmediateHead_Campaign::find($approver->id)->immediateHead_id)->employeeNumber;
      //$coll->push(['$tl'=>$tl, 'leader'=>$leader->employeeNumber]);
      if ($tl == $leader->employeeNumber){
        $TLapprover=$approver->id;
        break;
      } 
    }

    return $TLapprover;
    //return $coll;
    
  }


  public function getUserWorksched($userID,$productionDate)
  {
   
    $allSched = DB::table('user_dtr')->where('user_dtr.user_id',$userID)->where([ 
                    ['user_dtr.productionDate',$productionDate]
                    ])->join('users','users.id','=','user_dtr.user_id')->
                    
                  select('user_dtr.productionDate','user_dtr.workshift','users.employeeCode as accesscode', 'users.id as userID','users.lastname','users.firstname')->get();
    return $allSched;

  }




  public function getWorkedHours($user, $userLogIN, $userLogOUT, $schedForToday,$shiftEnd, $payday, $isRDYest,$isPartTimer)
  {

    $employee = $user;// User::find($user_id);

    $koll=new Collection;
    $comp = new Collection;
    //($employee->status_id == 12 || $employee->status_id == 14 ) ? $isPartTimer = true : $isPartTimer=false;

    $data = new Collection;
    $billableForOT=0;
    $UT = 0;
    $OTattribute = "";
    $campName = User::find($user->id)->campaign->first()->name;
    $isBackoffice = Campaign::find(Team::where('user_id',$employee->id)->first()->campaign_id)->isBackoffice;

    $hasHolidayToday = false;$flexedWH=null;
    $hasLWOP = null; $lwopDetails = new Collection; $hasPendingLWOP=false;
    $hasVL = null; $vlDetails = new Collection; $hasPendingVL=false;
    $hasVTO = null; $vtoDetails = new Collection; $hasPendingVTO=false;
    $hasOBT = null; $obtDetails = new Collection; $hasPendingOBT=false;
    $hasFL = null; $flDetails = new Collection; $hasPendingFL=false;

    //$thisPayrollDate = Biometrics::where(find($biometrics->id)->productionDate;
    //**** Hack for Davao holiday

    $bioID = Biometrics::where('productionDate',$payday)->first();

    $hol = Holiday::where('holidate', $payday)->get();
    (Team::where('user_id',$user->id)->first()->floor_id == 9) ? $isDavao=true : $isDavao=false;

    (Team::where('user_id',$user->id)->first()->floor_id == 10 || Team::where('user_id',$user->id)->first()->floor_id == 11) ? $isTaipei=1 : $isTaipei=0;

    if( count($hol) > 0 )
    {
      $h =  $hol->first();

      if ($h->holidayType_id == 4)
      {
        if($isDavao)
          {
            $holidayToday = $hol; //Holiday::where('holidate', $payday)->get();
            $hasHolidayToday = true;

          } 
          else { $holidayToday=null; }
      }
      elseif($h->holidayType_id == 5) // Taipei holiday
      {
        if($isTaipei)
          {
            $holidayToday = $hol; //Holiday::where('holidate', $payday)->get();
            $hasHolidayToday = true;

          } 
          else { $holidayToday=null; }

      }
      elseif($h->holidayType_id == 6) // Xiamen holiday
      {
        if($isTaipei)
          {
            $holidayToday = $hol; //Holiday::where('holidate', $payday)->get();
            $hasHolidayToday = true;

          } 
          else { $holidayToday=null; }

      }
      else {

        if($isTaipei)
          {$holidayToday = null;  $hasHolidayToday = 0;} //Holiday::where('holidate', $payday)->get();}
        else
        {$holidayToday = $hol;  $hasHolidayToday = true;} //Holiday::where('holidate', $payday)->get();}
      }

    }else
      $holidayToday = null; //$hol; //Holiday::where('holidate', $payday)->get();

    


    $theDay = Carbon::parse($payday." ".$schedForToday['timeStart'],"Asia/Manila");
    $fix= Carbon::parse($payday." 23:59:00","Asia/Manila");

    //*** we now determine if EXEMPT employee for the work sched
    $isExempt = null;
    $exemptEmp = DB::table('user_schedType')->where('user_id',$user->id)->join('schedType','schedType.id','=','user_schedType.schedType_id')->orderBy('user_schedType.created_at','DESC')->get();
    if (count($exemptEmp) > 0) $isExempt=1;

    
    /*if ($isExempt)
    {
      $ptSched=false;
      $is4x11=false;
      $isPartTimerForeign=false;
      if(is_null($userLogIN[0]['timing']))
      {
        $bb = Biometrics::where('productionDate',$payday)->first();
        $exIn = Logs::where('user_id',$user->id)->where('biometrics_id',$bb->id)->where('logType_id','1')->get();
        if (count($exIn) > 0)
        {
          $in2 = Carbon::parse($payday." ".$exIn->first()->logTime,'Asia/Manila');
          $o2 = Carbon::parse($payday." ".$exIn->first()->logTime,'Asia/Manila')->addHour(8);
          $bb2 = Biometrics::where('productionDate',$o2->format('Y-m-d'))->first();
          $exOut = Logs::where('user_id',$user->id)->where('biometrics_id',$bb2->id)->where('logType_id','2')->get();

          if(count($exOut) > 0)
          {
            $out2 = Carbon::parse($o2->format('Y-m-d')." ".$exOut->first()->logTime,'Asia/Manila');

            $diffHours = $in2->diffInHours($out2);
            $checkSShift = $in2;
            $checkEndShift = Carbon::parse($in2->format('Y-m-d H:i:s'),"Asia/Manila")->addHour(9);

          }
          else{
            $diffHours = $in2->diffInHours(Carbon::parse($schedForToday['timeEnd'],"Asia/Manila"));
            $checkSShift = $in2;
            $checkEndShift = Carbon::parse($payday." ".$exIn->first()->logTime,'Asia/Manila')->addHour(8);

          }

          

        }else{
                $checkEndShift = Carbon::parse($schedForToday['timeEnd'],"Asia/Manila");

                //pangcheck pag 12MN end ng shift
                if ($checkEndShift->format('H:i:s') == '00:00:00') $checkEndShift->addHours(24);
                
                //check mo kung begin shift eh today tapos bukas na end ng shift: 3PM min

                $checkSShift = Carbon::parse($schedForToday['timeStart'],"Asia/Manila");
                if($checkSShift->format('H:i:s') > '15:00:00')$checkEndShift->addHours(24);

                $diffHours = $checkEndShift->diffInHours($checkSShift);

        }

      }else{
        $diffHours = $userLogIN[0]['timing']->diffInHours($userLogOUT[0]['timing']);
        $checkSShift = Carbon::parse($userLogIN[0]['timing']->format('Y-m-d H:i:s'),"Asia/Manila");
        $checkEndShift = Carbon::parse($userLogIN[0]['timing']->format('Y-m-d H:i:s'),"Asia/Manila")->addHour(9);

      }
      


    }else
    {*/
     
          //**** for checking Foreigners na contractual == kasi tagged lang sila as CONTRACTUAL [FOREIGN] :id=15
          $checkEndShift = Carbon::parse($schedForToday['timeEnd'],"Asia/Manila");

          //pangcheck pag 12MN end ng shift
          if ($checkEndShift->format('H:i:s') == '00:00:00') $checkEndShift->addHours(24);
          
          //check mo kung begin shift eh today tapos bukas na end ng shift: 3PM min

          $checkSShift = Carbon::parse($schedForToday['timeStart'],"Asia/Manila");
          if($checkSShift->format('H:i:s') > '15:00:00')$checkEndShift->addHours(24);

          $diffHours = $checkEndShift->diffInHours($checkSShift);

          ($diffHours <= 4) ? $ptSched = 1 : $ptSched = false;
          ($diffHours > 9) ? $is4x11 = true : $is4x11=false;
          ($diffHours <= 4 && $employee->status_id == 15 ) ? $isPartTimerForeign = true :  $isPartTimerForeign =false; 

    //}

    

    
    

    // ------- 10-15-2020 update: Check if there's user_preshift override

    $preshiftOverride = DB::table('user_preshiftOverride')->where('user_id',$user->id)->where('productionDate',$payday)->get();

    if(count($preshiftOverride) > 0)
    {
      $startOfShift = Carbon::parse($payday." ".$schedForToday['timeStart'],"Asia/Manila")->addDays(-1);
      $ptOverride = null;
      if ($isPartTimer) {

        // ----- we now have to check kung may PT-override
        $hasPToverride = DB::table('pt_override')->where('user_id',$user->id)->where('overrideStart','<=',$payday)->where('overrideEnd','>=',$payday)->get();

        if (count($hasPToverride) > 0)
        {
          $ptOverride=true;
          $endOfShift =  Carbon::parse($payday." ".$schedForToday['timeStart'],"Asia/Manila")->addDays(-1)->addHour(9);
        }
        else
        {
          $endOfShift =  Carbon::parse($payday." ".$schedForToday['timeStart'],"Asia/Manila")->addDays(-1)->addHour(4);

        }
        
      }
      else {
        ($is4x11) ? $endOfShift = Carbon::parse($payday." ".$schedForToday['timeStart'],"Asia/Manila")->addDays(-1)->addHour(11) : $endOfShift = Carbon::parse($payday." ".$schedForToday['timeStart'],"Asia/Manila")->addDays(-1)->addHour(9);
      }

    }else
    {
      $startOfShift = Carbon::parse($payday." ".$schedForToday['timeStart'],"Asia/Manila");
      $ptOverride = null;
      if ($isPartTimer || $ptSched) {

        // ----- we now have to check kung may PT-override
        $hasPToverride = DB::table('pt_override')->where('user_id',$user->id)->where('overrideStart','<=',$payday)->where('overrideEnd','>=',$payday)->get();

        if (count($hasPToverride) > 0)
        {
          $ptOverride=true;
          $endOfShift =  Carbon::parse($payday." ".$schedForToday['timeStart'],"Asia/Manila")->addHour(9);
        }
        else
        {
          $endOfShift =  Carbon::parse($payday." ".$schedForToday['timeStart'],"Asia/Manila")->addHour(4);

        }
        
      }
      else {
        ($is4x11) ? $endOfShift = Carbon::parse($payday." ".$schedForToday['timeStart'],"Asia/Manila")->addHour(11) : $endOfShift = Carbon::parse($payday." ".$schedForToday['timeStart'],"Asia/Manila")->addHour(9);
      }

    }
    

    $shiftStart = $startOfShift;

    $inTime = null;
    $outTime = null;$x=null;$y=null;

    $alldays=[]; $alldaysLWOP=[]; $alldaysFL=[]; $alldaysVL=[]; $alldaysSL=[]; $alldaysVTO=[];
    /*------ WE CHECK FIRST IF THERE'S AN APPROVED VL | SL | LWOP -----*/
    
    /*-------- VACATION LEAVE  -----------*/
    
    $vacationleave = $this->establishLeaves($user->id,$endOfShift,'VL',$payday,$schedForToday);
    $vl = $vacationleave->leaveType;
    $alldaysVL = $vacationleave->allDays;
    $hasVL = $vacationleave->hasTheLeave; 
    $hasLeave = $vacationleave->hasLeave; 
    $vlDeet = $vacationleave->details;
    $hasPendingVL = $vacationleave->hasPending;
    //$koll->push(['hasVL'=>$hasVL,'alldaysVL'=>$alldaysVL,'vlDeet'=>$vlDeet,'query'=>$vacationleave->query]);
    /*-------- VACATION LEAVE  -----------*/

    /*-------- VTO LEAVE  -----------*/
    
    $vtimeoff = $this->establishLeaves($user->id,$endOfShift,'VTO',$payday,$schedForToday);
    $vto = $vtimeoff->leaveType;
    $alldaysVTO = $vtimeoff->allDays;
    $hasVTO = $vtimeoff->hasTheLeave;  
    $vtoDeet = $vtimeoff->details;
    $hasPendingVTO = $vtimeoff->hasPending;
    /*-------- vto LEAVE  -----------*/


   
    $sickleave = $this->establishLeaves($user->id,$endOfShift,'SL',$payday,$schedForToday);
    $sl = $sickleave->leaveType;
    $alldaysSL = $sickleave->allDays;
    $hasSL = $sickleave->hasTheLeave; 
    //$hasLeave = $sickleave->hasLeave; 
    $slDeet = $sickleave->details;
    $hasPendingSL = $sickleave->hasPending;

    
     /*-------- SICK LEAVE  -----------*/



    $noPay = $this->establishLeaves($user->id,$endOfShift,'LWOP',$payday,$schedForToday);
    $lwop = $noPay->leaveType;
    $alldaysLWOP = $noPay->allDays;
    $hasLWOP = $noPay->hasTheLeave; 
    //$hasLeave = $sickleave->hasLeave; 
    $lwopDeet = $noPay->details; 
    $hasPendingLWOP = $noPay->hasPending;




    $obt = User_OBT::where('user_id',$user->id)->where('leaveEnd','<=',$endOfShift->format('Y-m-d H:i:s'))->where('leaveStart','>=',$startOfShift->format('Y-m-d H:i:s'))->orderBy('created_at','DESC')->get();
    //where('leaveStart','<=',$fix->format('Y-m-d H:i:s'))->where('leaveEnd','>=',$theDay->format('Y-m-d H:i:s'))->orderBy('created_at','DESC')->get();

    

    /*$familyL = $this->establishLeaves($user->id,$endOfShift,'LWOP',$payday,$schedForToday);
    $fl = $familyL->leaveType;
    $alldaysFL = $familyL->allDays;
    $hasFL = $familyL->hasTheLeave; 
    //$hasLeave = $sickleave->hasLeave; 
    $flDeet = $familyL->details; 
    $hasPendingFL = $familyL->hasPending;*/

    $famL = User_Familyleave::where('user_id',$user->id)->where('leaveStart','<=',$endOfShift->format('Y-m-d H:i:s'))->orderBy('created_at','DESC')->get();
   
    if (count($famL) > 0)
    {
      //************* gawin mo to foreach family leave ************//
      foreach ($famL as $familyleave) 
      {
         
        $f_dayS = Carbon::parse($familyleave->leaveStart,'Asia/Manila');
        $f_dayE = Carbon::parse($familyleave->leaveEnd,'Asia/Manila');
        $full_leave = Carbon::parse($familyleave->leaveEnd,'Asia/Manila')->addDays($familyleave->totalCredits)->addDays(-1);
        $fend = $f_dayE->format('Y-m-d');

        $cf = $familyleave->totalCredits; // $f_dayS->diffInDays($f_dayE)+1;
        $cf2 = 1;

        if ($familyleave->totalCredits <= 1)
        {
            array_push($alldaysFL, $f_dayS->format('Y-m-d'));
            

        }else
        {
          while( $cf2 <= $cf){
          
            array_push($alldaysFL, $f_dayS->format('Y-m-d'));
            $f_dayS->addDays(1);
            $cf2++;
          }

        }
        
        //array_push($col, ['pasok alldaysFL'=>$alldaysFL, 'thisPayrollDate'=>$thisPayrollDate]);

        //$flcol->push(['payday'=>$payday, 'full_leave'=>$full_leave]);

        if(in_array($payday, $alldaysFL) ) {

          $fl = $familyleave; 
          $hasFL=true; $hasLeave=true;
          $flDeet= $familyleave;

          (!is_null($flDeet->isApproved)) ? $hasPendingFL=false : $hasPendingFL=true;

          break(1);
        }
        
      }

      //array_push($col, ['fl'=>$fl]);
      
    }else 
    {
      $fl=[];
      $hasFL = false; //$hasLeave=false;
      $flDeet = null;
    }


    

    /*-------- OBT LEAVE  -----------*/
    if (count($obt) > 0) 
    {
      $hasOBT=true;
      $obtDeet= $obt->first();
      (!is_null($obtDeet->isApproved)) ? $hasPendingOBT=false : $hasPendingOBT=true;

    }else{

      $hasOBT = false;
      $obtDeet = null;

    }


 

    

    
          
    if ((count((array)$userLogIN[0]['logs']) > 0 && count((array)$userLogOUT[0]['logs']) > 0) && !is_null($userLogIN[0]['timing']) && !is_null($userLogOUT[0]['timing']) )
    {
      //---- To get the right Worked Hours, check kung early pasok == get schedule Time
      //---- if late pumasok, get user timeIN


      //************ CHECK FOR LATEIN AND EARLY OUT ***************//

      // $checkLate = $userLogIN[0]['timing']->diffInMinutes(Carbon::parse($schedForToday['timeStart']));
      // if ($checkLate > 1) $isLateIN = true; else $isLateIN= false;

      // $checkEarlyOut = $userLogOUT[0]['timing']->diffInMinutes(Carbon::parse($schedForToday['timeEnd'],"Asia/Manila"));
      // if ($checkEarlyOut > 1)  $isEarlyOUT = true; else $isEarlyOUT= false;


      

      $inTime = $userLogIN[0]['timing'];// Carbon::parse($payday." ".$t,'Asia/Manila');
      $outTime = $userLogOUT[0]['timing']; //Carbon::parse($payday." ".$t2,'Asia/Manila');


      $link = action('UserController@myRequests',$user->id);
      $icons ="";
      $workedHours=null;$log="";

      $t =$userLogIN[0]['timing']->format('H:i:s');
      $t2 =$userLogOUT[0]['timing']->format('H:i:s');

      $scheduleStart = $startOfShift; // Carbon::parse($payday." ".$schedForToday['timeStart'],"Asia/Manila");
      //$scheduleEnd = Carbon::parse($payday." ".$schedForToday['timeStart'],"Asia/Manila");

     

      //$koll->push(['userLogIN'=>$inTime->format('Y-m-d H:i'), 'scheduleStart'=>$scheduleStart->format('Y-m-d H:i')]);

      // ------- new check override kung holiday at backoffice
      if ($hasHolidayToday && $isBackoffice ) 
      {
        // hindi na from timeIN kundi start ng worksched
        // check mo muna kung nacomplete ba nya yung shift nya ng holiday
        //     if (earlier than shift) startOT from startng Shift
        //     else startOT from timeiN

        //check mo muna kung may approved HD OT
        $mayOT = User_OT::where('user_id',$user->id)->where('biometrics_id',$bioID->id)->where('isApproved',1)->get();

        if(count($mayOT) > 0)
        {
          $wh = $mayOT->first()->filed_hours*60; //number_format($mayOT->first()->filed_hours*60,2);

        }
        else
        {
            if( Carbon::parse($userLogIN[0]['timing'],'Asia/Manila') < $scheduleStart->format('Y-m-d H:i'))
            {
              $wh = Carbon::parse($userLogOUT[0]['timing'],'Asia/Manila')->diffInMinutes($scheduleStart);

            }else
            {
              $wh = Carbon::parse($userLogOUT[0]['timing'],'Asia/Manila')->diffInMinutes(Carbon::parse($userLogIN[0]['timing'],'Asia/Manila'));
            }

        }

        
        
        goto proceedWithNormal;
      }


      // If exempt, check mo lang kung nakatotal of 8hrs si flexi 8hr;
      // if flexi anytime naman, basta total of 45hrs in a week

      if ($isExempt)
      {
        if($exemptEmp[0]->schedType_id == '2') //flexi 8hr
        {
            $flexedWH = number_format( ($outTime->diffInMinutes($inTime) )/60,2);
            if ($flexedWH < 9.0)
            {
              $isLateIN=false; $isEarlyOUT=true;
            }else{
              $isLateIN=false; $isEarlyOUT=false;
            }

            $dt = Carbon::parse($userLogIN[0]['timing'],"Asia/Manila")->format('H:i:s');
            $ds = explode(':', $dt);
            if($ds[1] == '00') {

              $startOfShift = Carbon::parse($payday." ".$ds[0].":00:00",'Asia/Manila');
              $endOfShift = Carbon::parse($payday." ".$ds[0].":00:00",'Asia/Manila')->addHour(9);
             
            }
            elseif($ds[1] > '00' && $ds[1] <= '15'){
              $startOfShift = Carbon::parse($payday." ".$ds[0].":15:00",'Asia/Manila');
              $endOfShift = Carbon::parse($payday." ".$ds[0].":15:00",'Asia/Manila')->addHour(9);

            }
            elseif($ds[1] > 15 && $ds[1] <=30){
              $startOfShift = Carbon::parse($payday." ".$ds[0].":30:00",'Asia/Manila');
              $endOfShift = Carbon::parse($payday." ".$ds[0].":30:00",'Asia/Manila')->addHour(9);

            }
            elseif($ds[1] > 30 && $ds[1] <=45){
              $startOfShift = Carbon::parse($payday." ".$ds[0].":45:00",'Asia/Manila');
              $endOfShift = Carbon::parse($payday." ".$ds[0].":45:00",'Asia/Manila')->addHour(9);
            }
            elseif($ds[1] > 45){
              $startOfShift = Carbon::parse($payday." ".($ds[0]+1).":00:00",'Asia/Manila');
              $endOfShift = Carbon::parse($payday." ".($ds[0]+1).":00:00",'Asia/Manila')->addHour(9);

            }


        }
        else { $isLateIN=false; $isEarlyOUT=false; $startOfShift = Carbon::parse($userLogIN[0]['timing'],"Asia/Manila");
              $endOfShift =  Carbon::parse($userLogIN[0]['timing'],"Asia/Manila")->addHour(9); }

      }else
      {
          //$flexedWH = 33.33;

          if ($inTime->format('Y-m-d H:i') > $scheduleStart->format('Y-m-d H:i'))
          {
            //$checkLate = $userLogIN[0]['timing']->diffInMinutes(Carbon::parse($schedForToday['timeStart'], "Asia/Manila"));
            $checkLate = $inTime->diffInMinutes($scheduleStart);
            //---- MARKETING TEAM CHECK: 15mins grace period
              
              
                if ($checkLate > 2) $isLateIN = true; else $isLateIN= false;
                $isLateIN=true;
              

            
          } else $isLateIN= false;


          if ($userLogOUT[0]['timing']->format('Y-m-d H:i:s') < $endOfShift->format('Y-m-d H:i:s')) //Carbon::parse($payday." ".$schedForToday['timeEnd'],"Asia/Manila")->format('Y-m-d H:i:s'))
          {

            $checkEarlyOut = $userLogOUT[0]['timing']->diffInMinutes(Carbon::parse($payday." ".$schedForToday['timeEnd'],"Asia/Manila"));
            //---- MARKETING TEAM CHECK: 15mins grace period
              
                 if ($checkEarlyOut > 1) $isEarlyOUT = true; else $isEarlyOUT= false;
              
                //$koll->push(['keme'=>$isEarlyOUT]);
            
          } else $isEarlyOUT= false;

      }//else ng hindi exempt


      


      

      //
      //$koll->push(['1'=>$userLogOUT[0]['timing']->format('Y-m-d H:i:s'),'2'=>Carbon::parse($payday." ".$schedForToday['timeEnd'],"Asia/Manila")->format('Y-m-d H:i:s')]);

      if ($isEarlyOUT && $isLateIN)//use user's logs
      {
        $prod = Carbon::parse($userLogOUT[0]['timing'])->format('Y-m-d');
        //we now check if agent has SL VL LWOP to correct the worked hours & UT
        if($hasSL || $hasVL || $hasLWOP )
        {

          /*if($hasVL)
          {
            $lieoData = $this->processLeaveForLateInEarlyOut($vlDeet->totalCredits,$scheduleStart,$userLogOUT,$endOfShift,$isPartTimer,$isPartTimerForeign, $ptOverride,$is4x11);
            $billableForOT = $lieoData['billableForOT'];
            $UT = $lieoData['UT'];
            $workedHours = $lieoData['workedHours'];

          }

          if($hasSL)
          {
            $lieoData = $this->processLeaveForLateInEarlyOut($slDeet->totalCredits,$scheduleStart,$userLogOUT,$endOfShift,$isPartTimer,$isPartTimerForeign, $ptOverride, $is4x11);
            //$billableForOT = $lieoData->billableForOT;
            $UT = $lieoData['UT'];
            $workedHours = $lieoData['workedHours'];

          }*/
            

          if($hasVL)
          {
              if($vlDeet->totalCredits == '0.5') {
                $magStart = Carbon::parse($scheduleStart->format('Y-m-d H:i:s'),"Asia/Manila")->addHours(5);
                $wh = Carbon::parse($userLogOUT[0]['timing'],'Asia/Manila')->diffInMinutes($magStart)+240;

                $minsEarlyOut = $endOfShift->diffInMinutes(Carbon::parse($userLogOUT[0]['timing'],'Asia/Manila'));
                //if ($wh > 5 && !($isPartTimer || $isPartTimerForeign) ) $wh = $wh - 60;
          
                //we less 1hr for the break, BUT CHECK FIRST IF PART TIME OR NOT
                ($isPartTimer || $isPartTimerForeign) ? $workedHours = number_format(($wh)/60,2)."<br/><small>(late IN & early OUT)</small>" : $workedHours = number_format(($wh)/60,2)."<br/><small>(late IN & early OUT)</small>";

                $billableForOT=0;
                if ($isPartTimer || $isPartTimerForeign) {

                  ($ptOverride) ? $UT = number_format((480.0 - (($wh) - $minsLate) )/60,2) : $UT = number_format((240.0 - ($wh - $minsLate) )/60,2); //number_format((240.0 - $wh)/60,2);
                  
                }
                else {
                  if ($is4x11)
                    $UT = number_format((600.0 - (($wh) - ($minsEarlyOut)) )/60,2);  //number_format((480.0 - $wh)/60,2); 44.44;
                  else
                    $UT = number_format( ($minsEarlyOut) /60,2); 
                } 

              }
              else {
                $wh = Carbon::parse($userLogOUT[0]['timing'],'Asia/Manila')->diffInMinutes(Carbon::parse($userLogIN[0]['timing'],'Asia/Manila'));

                 $minsEarlyOut = $endOfShift->diffInMinutes(Carbon::parse($userLogOUT[0]['timing'],'Asia/Manila'));
                 $minsLate = $scheduleStart->diffInMinutes(Carbon::parse($userLogIN[0]['timing'],'Asia/Manila'));
                //if ($wh > 5 && !($isPartTimer || $isPartTimerForeign) ) $wh = $wh - 60;
            
                //we less 1hr for the break, BUT CHECK FIRST IF PART TIME OR NOT
                ($isPartTimer || $isPartTimerForeign) ? $workedHours = number_format(($wh)/60,2)."<br/><small>(late IN & early OUT)</small>" : $workedHours = number_format(($wh-60)/60,2)."<br/><small>(late IN & early OUT)</small>";

                $billableForOT=0; 

                if ($isPartTimer || $isPartTimerForeign) {

                  ($ptOverride) ? $UT = number_format((480.0 - (($wh-60) - $minsLate) )/60,2) : $UT = number_format((240.0 - ($wh - $minsLate) )/60,2); //number_format((240.0 - $wh)/60,2);
                  
                }
                else {
                  if ($is4x11)
                    $UT = number_format((600.0 - (($wh-60) - ($minsLate+$minsEarlyOut)) )/60,2);  //number_format((480.0 - $wh)/60,2); 44.44;
                  else
                    $UT = number_format( ($minsLate+$minsEarlyOut) /60,2); 
                } 
              }

              //$workedHours1 = $this->processLeaves($theDay->format('Y-m-d'),'VL',true,$wh,$vlDeet,$hasPendingVL,$icons,$userLogIN[0],$userLogOUT[0],$shiftEnd,$shiftStart);
              //$workedHours  =  number_format($wh/60,2);//.= $workedHours1[0]['workedHours'];
              //$minsLate = $scheduleStart->diffInMinutes(Carbon::parse($userLogIN[0]['timing'],'Asia/Manila'));
             

              //$workedHours = number_format(($wh-60)/60,2)."<br/><small>(late IN & early OUT)</small>";
              //$userLogIN[0]['timing']/60;

              //$stat = User::find($user_id)->status_id;
              //****** part time user





              // $UT = $workedHours1[0]['UT'];
              // $billableForOT = $workedHours1[0]['billableForOT'];
              // $OTattribute = $workedHours1[0]['OTattribute'];

          }

          if($hasSL)
          {
              if($slDeet->totalCredits == '0.5') {
                $magStart = Carbon::parse($scheduleStart->format('Y-m-d H:i:s'),"Asia/Manila")->addHours(5);
                $wh = Carbon::parse($userLogOUT[0]['timing'],'Asia/Manila')->diffInMinutes($magStart)+240;

                $minsEarlyOut = $endOfShift->diffInMinutes(Carbon::parse($userLogOUT[0]['timing'],'Asia/Manila'));
                //if ($wh > 5 && !($isPartTimer || $isPartTimerForeign) ) $wh = $wh - 60;
          
                //we less 1hr for the break, BUT CHECK FIRST IF PART TIME OR NOT
                ($isPartTimer || $isPartTimerForeign) ? $workedHours = number_format(($wh)/60,2)."<br/><small>(late IN & early OUT)</small>" : $workedHours = number_format(($wh)/60,2)."<br/><small>(late IN & early OUT)</small>";

                $billableForOT=0;
                if ($isPartTimer || $isPartTimerForeign) {

                  ($ptOverride) ? $UT = number_format((480.0 - (($wh) - $minsLate) )/60,2) : $UT = number_format((240.0 - ($wh - $minsLate) )/60,2); //number_format((240.0 - $wh)/60,2);
                  
                }
                else {
                  if ($is4x11)
                    $UT = number_format((600.0 - (($wh) - ($minsEarlyOut)) )/60,2);  //number_format((480.0 - $wh)/60,2); 44.44;
                  else
                    $UT = number_format( ($minsEarlyOut) /60,2); 
                } 

              }
              else {
                $wh = Carbon::parse($userLogOUT[0]['timing'],'Asia/Manila')->diffInMinutes(Carbon::parse($userLogIN[0]['timing'],'Asia/Manila'));

                 $minsEarlyOut = $endOfShift->diffInMinutes(Carbon::parse($userLogOUT[0]['timing'],'Asia/Manila'));
                 $minsLate = $scheduleStart->diffInMinutes(Carbon::parse($userLogIN[0]['timing'],'Asia/Manila'));
                //if ($wh > 5 && !($isPartTimer || $isPartTimerForeign) ) $wh = $wh - 60;
            
                //we less 1hr for the break, BUT CHECK FIRST IF PART TIME OR NOT
                ($isPartTimer || $isPartTimerForeign) ? $workedHours = number_format(($wh)/60,2)."<br/><small>(late IN & early OUT)</small>" : $workedHours = number_format(($wh-60)/60,2)."<br/><small>(late IN & early OUT)</small>";

                $billableForOT=0; 

                if ($isPartTimer || $isPartTimerForeign) {

                  ($ptOverride) ? $UT = number_format((480.0 - (($wh-60) - $minsLate) )/60,2) : $UT = number_format((240.0 - ($wh - $minsLate) )/60,2); //number_format((240.0 - $wh)/60,2);
                  
                }
                else {
                  if ($is4x11)
                    $UT = number_format((600.0 - (($wh-60) - ($minsLate+$minsEarlyOut)) )/60,2);  //number_format((480.0 - $wh)/60,2); 44.44;
                  else
                    $UT = number_format( ($minsLate+$minsEarlyOut) /60,2); 
                } 
              }

              

          }

          if($hasLWOP)
          {
              if($lwopDeet->totalCredits == '0.5') {

                //if halfday 2nd half
                if($lwopDeet->halfdayFrom == '3')
                {
                  $magStart = Carbon::parse($userLogIN[0]['timing'],"Asia/Manila");//->addHours(5);
                  $wh = Carbon::parse($userLogOUT[0]['timing'],'Asia/Manila')->diffInMinutes($magStart);

                }else
                {
                  $magStart = Carbon::parse($scheduleStart->format('Y-m-d H:i:s'),"Asia/Manila")->addHours(5);
                  $wh = Carbon::parse($userLogOUT[0]['timing'],'Asia/Manila')->diffInMinutes($magStart);

                }
                

                $minsEarlyOut = $endOfShift->diffInMinutes(Carbon::parse($userLogOUT[0]['timing'],'Asia/Manila'));
                //if ($wh > 5 && !($isPartTimer || $isPartTimerForeign) ) $wh = $wh - 60;
          
                //we less 1hr for the break, BUT CHECK FIRST IF PART TIME OR NOT
                ($isPartTimer || $isPartTimerForeign) ? $workedHours = number_format(($wh)/60,2)."<br/><small>(late IN & early OUT)</small>" : $workedHours = number_format(($wh)/60,2)."<br/><small>(late IN & early OUT)</small>";

                $billableForOT=0;
                if ($isPartTimer || $isPartTimerForeign) {

                  ($ptOverride) ? $UT = number_format((480.0 - (($wh) - $minsLate) )/60,2) : $UT = number_format((240.0 - ($wh - $minsLate) )/60,2); //number_format((240.0 - $wh)/60,2);
                  
                }
                else {
                  if ($is4x11)
                    $UT = number_format((600.0 - (($wh) - ($minsEarlyOut)) )/60,2);  //number_format((480.0 - $wh)/60,2); 44.44;
                  else
                    $UT = number_format( ($minsEarlyOut) /60,2); 
                } 

              }
              else {


                $wh = Carbon::parse($userLogOUT[0]['timing'],'Asia/Manila')->diffInMinutes(Carbon::parse($userLogIN[0]['timing'],'Asia/Manila'));

                 $minsEarlyOut = $endOfShift->diffInMinutes(Carbon::parse($userLogOUT[0]['timing'],'Asia/Manila'));
                 $minsLate = $scheduleStart->diffInMinutes(Carbon::parse($userLogIN[0]['timing'],'Asia/Manila'));
                //if ($wh > 5 && !($isPartTimer || $isPartTimerForeign) ) $wh = $wh - 60;
            
                //we less 1hr for the break, BUT CHECK FIRST IF PART TIME OR NOT
                ($isPartTimer || $isPartTimerForeign) ? $workedHours = number_format(($wh)/60,2)."<br/><small>(late IN & early OUT)</small>" : $workedHours = number_format(($wh-60)/60,2)."<br/><small>(late IN & early OUT)</small>";

                $billableForOT=0; 

                if ($isPartTimer || $isPartTimerForeign) {

                  ($ptOverride) ? $UT = number_format((480.0 - (($wh-60) - $minsLate) )/60,2) : $UT = number_format((240.0 - ($wh - $minsLate) )/60,2); //number_format((240.0 - $wh)/60,2);
                  
                }
                else {
                  if ($is4x11)
                    $UT = number_format((600.0 - (($wh-60) - ($minsLate+$minsEarlyOut)) )/60,2);  //number_format((480.0 - $wh)/60,2); 44.44;
                  else
                    $UT = number_format( ($minsLate+$minsEarlyOut) /60,2); 
                } 
              }

              

          }

          

         
          
        }
        else
        {

          if ($hasVTO)
          {

            $wh = Carbon::parse($userLogOUT[0]['timing'],'Asia/Manila')->diffInMinutes(Carbon::parse($userLogIN[0]['timing'],'Asia/Manila'));
            $minsLate = $scheduleStart->diffInMinutes(Carbon::parse($userLogIN[0]['timing'],'Asia/Manila'));
            $minsEarlyOut = 0; //$endOfShift->diffInMinutes(Carbon::parse($userLogOUT[0]['timing'],'Asia/Manila'));

            $workedHours1 = $this->processLeaves($theDay->format('Y-m-d'),'VTO',false,$wh,$vtoDeet,$hasPendingVTO,$icons,$userLogIN[0],$userLogOUT[0],$shiftEnd,$shiftStart);
            
            //$workedHours = $workedHours1[0]['workedHours'];
            $UT = number_format($minsLate/60,2);
            $workedHours = (float)$workedHours1[0]['actualHrs'] - $UT;
            $workedHours .= "<br/>".$workedHours1[0]['logDeets'];
            $workedHours .= "<small>( late IN )</small><br/>";

            //$UT = $workedHours1[0]['UT'];
            $actualHrs = $workedHours1[0]['actualHrs'];
            
            

          }//end if has VTO

          else{
                $wh = Carbon::parse($userLogOUT[0]['timing'],'Asia/Manila')->diffInMinutes(Carbon::parse($userLogIN[0]['timing'],'Asia/Manila'));
                $minsLate = $scheduleStart->diffInMinutes(Carbon::parse($userLogIN[0]['timing'],'Asia/Manila'));
                $minsEarlyOut = $endOfShift->diffInMinutes(Carbon::parse($userLogOUT[0]['timing'],'Asia/Manila'));

                //if ($wh > 5 && !($isPartTimer || $isPartTimerForeign) ) $wh = $wh - 60;
                
                //we less 1hr for the break, BUT CHECK FIRST IF PART TIME OR NOT
                ($isPartTimer || $isPartTimerForeign) ? $workedHours = number_format(($wh)/60,2)."<br/><small>(late IN & early OUT)</small>" : $workedHours = number_format(($wh-60)/60,2)."<br/><small>(late IN & early OUT)</small>";

                //$workedHours = number_format(($wh-60)/60,2)."<br/><small>(late IN & early OUT)</small>";
                $billableForOT=0; //$userLogIN[0]['timing']/60;

                //$stat = User::find($user_id)->status_id;
                //****** part time user

                if ($isPartTimer || $isPartTimerForeign) {

                  ($ptOverride) ? $UT = number_format((480.0 - (($wh-60) - $minsLate) )/60,2) : $UT = number_format((240.0 - ($wh - $minsLate) )/60,2); //number_format((240.0 - $wh)/60,2);
                  
                }
                else {
                  if ($is4x11)
                    $UT = number_format((600.0 - (($wh-60) - ($minsLate+$minsEarlyOut)) )/60,2);  //number_format((480.0 - $wh)/60,2); 44.44;
                  else
                    $UT = number_format( ($minsLate+$minsEarlyOut) /60,2); 

                }

          }


          

        }

        

        



        

      }
      else if ($isEarlyOUT)
      {
         //$koll=["from"=>"isEarlyOUT"];
         //--- but u need to make sure if nag late out sya
          if (Carbon::parse($userLogOUT[0]['timing'],"Asia/Manila") > $endOfShift ) // Carbon::parse($payday." ".$schedForToday['timeEnd'],"Asia/Manila"))
          {
            if($isPartTimer | $isPartTimerForeign)
              ($ptOverride) ? $workedHours = 8.00 : $workedHours = 4.00; 
            else {
              ($is4x11) ? $workedHours = 10.00 : $workedHours = 8.00;
            }

            //check first if Locked na DTR for that production date
            $verifiedDTR = User_DTR::where('productionDate',$payday)->where('user_id',$user->id)->get();
            if (count($verifiedDTR) > 0)
              $icons = "<a title=\"Unlock DTR to File this OT\" class=\"pull-right text-gray\" style=\"font-size:1.2em;\" ><i class=\"fa fa-credit-card\"></i></a>";
            else
            {
              if($hasHolidayToday && $isBackoffice)
                $icons = "<a  id=\"OT_".$payday."\"  data-toggle=\"modal\" data-target=\"#myModal_OT".$payday."\"   title=\"File this Holiday OT\" class=\"pull-right\" style=\"font-size:1.2em;\" href=\"#\"><i class=\"fa fa-credit-card\"></i></a>";
              else
                $icons = "<a  id=\"OT_".$payday."\"  data-toggle=\"modal\" data-target=\"#myModal_OT".$payday."\"   title=\"File this OT\" class=\"pull-right\" style=\"font-size:1.2em;\" href=\"#\"><i class=\"fa fa-credit-card\"></i></a>";


            }

            $totalbill = number_format(($endOfShift->diffInMinutes(Carbon::parse($userLogOUT[0]['timing'],"Asia/Manila") ))/60,2);

            if ($totalbill > 0.5){
              $billableForOT = $totalbill;
              $OTattribute = $icons;
            }
              
            else
            {
              if($hasHolidayToday && $isBackoffice)
              {
                $billableForOT = $totalbill;
                $OTattribute = $icons;

              }
              else
              {
                $billableForOT = $totalbill;
                $OTattribute = "&nbsp;&nbsp;&nbsp;";

              }
              
            } 
          }
          else if($hasHolidayToday && $isBackoffice)
          {
            $wh = Carbon::parse($userLogOUT[0]['timing'],'Asia/Manila')->diffInMinutes(Carbon::parse($userLogIN[0]['timing'],'Asia/Manila'));
            $totalbill = number_format($wh/60,2);
            $icons = "<a  id=\"OT_".$payday."\"  data-toggle=\"modal\" data-target=\"#myModal_OT".$payday."\"   title=\"File this Holiday OT\" class=\"pull-right\" style=\"font-size:1.2em;\" href=\"#\"><i class=\"fa fa-credit-card\"></i></a>";

          }
            
          else
          {

            /*--- WE NEED TO CHECK FIRST KUNG MAY LEGIt LEAVES SYA ***/
            $comp->push(['out'=>Carbon::parse($userLogOUT[0]['timing'],"Asia/Manila"),'sched'=>Carbon::parse($payday." ".$schedForToday['timeStart'],"Asia/Manila")]);
            
            //$noSec = Carbon::parse($schedForToday['timeStart'],'Asia/Manila');
            $wh = Carbon::parse($userLogOUT[0]['timing']->format('Y-m-d H:i'),"Asia/Manila")->diffInMinutes($startOfShift);
           

            if ($wh >= 300 ) $wh = $wh-60; 



            if ($hasSL)
            {
              $workedHours1 = $this->processLeaves($theDay->format('Y-m-d'),'SL',true,$wh,$slDeet,$hasPendingSL,$icons,$userLogIN[0],$userLogOUT[0],$shiftEnd,$shiftStart);
              $workedHours = $workedHours1[0]['workedHours'];
              $UT = $workedHours1[0]['UT'];

            }//end if has SL
           
           if ($hasVL)
            {
              $workedHours1 = $this->processLeaves($theDay->format('Y-m-d'),'VL',true,$wh,$vlDeet,$hasPendingVL,$icons,$userLogIN[0],$userLogOUT[0],$shiftEnd,$shiftStart);
              $workedHours .= $workedHours1[0]['workedHours'];
              $UT = $workedHours1[0]['UT'];

            }//end if has VL

            if ($hasVTO)
            {
              $workedHours1 = $this->processLeaves($theDay->format('Y-m-d'),'VTO',false,$wh,$vtoDeet,$hasPendingVTO,$icons,$userLogIN[0],$userLogOUT[0],$shiftEnd,$shiftStart);
              $workedHours .= $workedHours1[0]['workedHours'];
              $UT = $workedHours1[0]['UT'];

            }//end if has VTO
            

             if ($hasOBT)
              {
                $workedHours1 = $this->processLeaves($theDay->format('Y-m-d'),'OBT',true,$wh,$obtDeet,$hasPendingOBT,$icons,$userLogIN[0],$userLogOUT[0],$shiftEnd,$shiftStart);
                $workedHours .= $workedHours1[0]['workedHours'];
                $UT = $workedHours1[0]['UT'];
                     
              }//end if has OBT



             if ($hasLWOP)
              {
                $workedHours1 = $this->processLeaves($theDay->format('Y-m-d'),'LWOP',true,$wh,$lwopDeet,$hasPendingLWOP,$icons,$userLogIN[0],$userLogOUT[0],$shiftEnd,$shiftStart);
                $workedHours .= $workedHours1[0]['workedHours'];
                $UT = $workedHours1[0]['UT'];

              }//end if has LWOP


              if ($hasFL)
              {
                $workedHours1 = $this->processLeaves($theDay->format('Y-m-d'),'FL',true,$wh,$flDeet,$hasPendingFL,$icons,$userLogIN[0],$userLogOUT[0],$shiftEnd,$shiftStart);
                $workedHours .= $workedHours1[0]['workedHours'];
                $UT = $workedHours1[0]['UT'];

              }//end if has LWOP
            

            if (!$hasSL && !$hasVL && !$hasLWOP && !$hasOBT && !$hasFL && !$hasVTO)
            {
              $workedHours .= number_format($wh/60,2)."<br/><small>(early OUT)</small>";

              //$stat = User::find($user_id)->status_id;
              //****** part time user

              if ($isPartTimer || $isPartTimerForeign) {
                ($ptOverride) ? $UT = round((480.0 - $wh )/60,2) : $UT = round((240.0 - $wh)/60,2); 
              }
              else
              {
                if ($is4x11)
                  $UT = round((600.0 - $wh )/60,2);  //number_format((480.0 - $wh)/60,2); 44.44;
                else
                  $UT = round((480.0 - $wh )/60,2); 
              }



              $billableForOT=0;
            }

            if ($hasHolidayToday)
            {
              $workedHours .= "<br/> <strong>* ". $holidayToday->first()->name. " *</strong>";
            }



          }//end ifelse

        
      }//end if EarlyOUT

      else if($isLateIN)
      {
        $koll=["from"=>"isLateIN"];
        //--- but u need to make sure if nag late out sya
        //    otherwise, super undertime talaga sya
        $minsLate = $scheduleStart->diffInMinutes(Carbon::parse($userLogIN[0]['timing'],'Asia/Manila'));

        if (Carbon::parse($userLogOUT[0]['timing'],"Asia/Manila") > $endOfShift) // Carbon::parse($schedForToday['timeEnd'],"Asia/Manila") )
        {
          
          if ($isPartTimer || $ptSched || $isPartTimerForeign)
          {
            //$wh = $endOfShift->diffInMinutes(Carbon::parse($userLogIN[0]['timing'],"Asia/Manila"));
            $wh = Carbon::parse($userLogOUT[0]['timing'],"Asia/Manila")->diffInMinutes(Carbon::parse($userLogIN[0]['timing'],"Asia/Manila"));
          } else
          {
            //$wh = $endOfShift->diffInMinutes(Carbon::parse($userLogIN[0]['timing'],"Asia/Manila")->addMinutes(60));
            $e1 = $endOfShift->format('Y-m-d H:i');
            $timing = Carbon::parse($userLogIN[0]['timing'],"Asia/Manila")->format('Y-m-d H:i');

            $wh = Carbon::parse($e1,'Asia/Manila')->diffInMinutes(Carbon::parse($timing,'Asia/Manila')); //$endOfShift->diffInMinutes(Carbon::parse($userLogIN[0]['timing'],"Asia/Manila"));
            if ($wh >=300) $wh =  Carbon::parse($e1,'Asia/Manila')->diffInMinutes(Carbon::parse($timing,'Asia/Manila')->addMinutes(60));//$endOfShift->diffInMinutes(Carbon::parse($userLogIN[0]['timing'],"Asia/Manila")->addMinutes(60));
            
          }


         
          /* ---- but we need to check Jeff's case of multiple requessts
                  bakit sya lateIN? baka may valid SL | VL |OBT */


            if ($hasSL)
            {
              //$wh = Carbon::parse($schedForToday['timeEnd'],"Asia/Manila")->diffInMinutes(Carbon::parse($userLogIN[0]['timing'],"Asia/Manila"));
              $wh = $endOfShift->diffInMinutes(Carbon::parse($userLogIN[0]['timing'],"Asia/Manila"));

              $workedHours1 = $this->processLeaves($theDay->format('Y-m-d'),'SL',true,$wh,$slDeet,$hasPendingSL,$icons,$userLogIN[0],$userLogOUT[0],$shiftEnd,$shiftStart);
              $workedHours .= $workedHours1[0]['workedHours'];
              $UT = $workedHours1[0]['UT'];
              $billableForOT = $workedHours1[0]['billableForOT'];
              $OTattribute = $workedHours1[0]['OTattribute'];

            }//end if has SL

            else if ($hasVL)
            {
              $workedHours1 = $this->processLeaves($theDay->format('Y-m-d'),'VL',true,$wh,$vlDeet,$hasPendingVL,$icons,$userLogIN[0],$userLogOUT[0],$shiftEnd,$shiftStart);
              $workedHours .= $workedHours1[0]['workedHours'];
              $UT = $workedHours1[0]['UT'];
              $billableForOT = $workedHours1[0]['billableForOT'];
              $OTattribute = $workedHours1[0]['OTattribute'];

            }//end if has VL

            

            else if ($hasVTO)
            {
              $workedHours1 = $this->processLeaves($theDay->format('Y-m-d'),'VTO',false,$wh,$vtoDeet,$hasPendingVTO,$icons,$userLogIN[0],$userLogOUT[0],$shiftEnd,$shiftStart);
              
              //$workedHours = $workedHours1[0]['workedHours'];
              //------- we need to fix yung over worked hours since  VTO naman --------
              

              $shifthrs = $startOfShift->diffInHours($endOfShift);



              

               // **** here we check kung yung VTO eh pre or post. Pag to cover preshift
              // ang worked hours bale eh UT + worked hours
              if (date('h',strtotime($vtoDeet->startTime)) == $startOfShift->format('h'))
              {
                $UT = 0; //number_format($minsLate/60,2);
                 
                if ($shifthrs > 4) {
                  //check mo muna kung UNPAID VTO
                  if($vtoDeet->deductFrom == 'LWOP')
                    $workedHours = number_format((float)$workedHours1[0]['workedHours'],2); // number_format((float)$workedHours1[0]['actualHrs'],2); 
                  else
                    $workedHours = ($shifthrs-1) - $UT;
                }
                else
                  $workedHours = number_format((float)$workedHours1[0]['actualHrs'] - $UT,2);

                $workedHours .= "<br/>".$workedHours1[0]['logDeets'];

              }else
              {
                $UT = number_format($minsLate/60,2);
                if ($shifthrs > 4)
                  $workedHours = ($shifthrs-1) - $UT;
                else
                  $workedHours = (float)$workedHours1[0]['actualHrs'] - $UT;

                $workedHours .= "<br/>".$workedHours1[0]['logDeets'];
                 
                $workedHours .= "<small>( late IN ) </small><br/>";

              }
             

              //$UT = $workedHours1[0]['UT'];
              $actualHrs = $workedHours1[0]['actualHrs'];
              
              

            }//end if has VTO
             

            else if ($hasOBT)
              {

                  $workedHours1 = $this->processLeaves($theDay->format('Y-m-d'),'OBT',true,$wh,$obtDeet,$hasPendingOBT,$icons,$userLogIN[0],$userLogOUT[0],$shiftEnd,$shiftStart);
                  $workedHours .= $workedHours1[0]['workedHours'];
                  $UT = $workedHours1[0]['UT'];

              }//end if has OBT


            else if ($hasLWOP)
              {
                  $e1 = Carbon::parse($userLogOUT[0]['timing'],"Asia/Manila")->format('Y-m-d H:i');
                  $timing = Carbon::parse($userLogIN[0]['timing'],"Asia/Manila")->format('Y-m-d H:i');

                  $wh = Carbon::parse($e1,'Asia/Manila')->diffInMinutes(Carbon::parse($timing,'Asia/Manila')); 
                  $workedHours1 = $this->processLeaves($theDay->format('Y-m-d'),'LWOP',true,$wh,$lwopDeet,$hasPendingLWOP,$icons,$userLogIN[0],$userLogOUT[0],$shiftEnd,$shiftStart);
                  $workedHours = $workedHours1[0]['workedHours'];
                  $UT = $workedHours1[0]['UT'];
                  $billableForOT = $workedHours1[0]['billableForOT'];
                  $OTattribute = $workedHours1[0]['OTattribute'];

              }//end if has LWOP


            else if ($hasFL)
              {
                  $workedHours1 = $this->processLeaves($theDay->format('Y-m-d'),'FL',true,$wh,$flDeet,$hasPendingFL,$icons,$userLogIN[0],$userLogOUT[0],$shiftEnd,$shiftStart);
                  $workedHours .= $workedHours1[0]['workedHours'];
                  $UT = $workedHours1[0]['UT'];

              }//end if has LWOP

            else
            {
              
               $workedHours = number_format($wh/60,2)."<br/><small>[ Late IN ]</small>";$billableForOT=0;
               if ($hasHolidayToday){ $workedHours .= "<br/> <strong>* ". $holidayToday->first()->name. " *</strong>";}


                //check first if Locked na DTR for that production date
                  $verifiedDTR = User_DTR::where('productionDate',$payday)->where('user_id',$user->id)->get();
                  if (count($verifiedDTR) > 0)
                    $icons = "<a title=\"Unlock DTR to File this OT\" class=\"pull-right text-gray\" style=\"font-size:1.2em;\" ><i class=\"fa fa-credit-card\"></i></a>";
                  else
                    $icons = "<a  id=\"OT_".$payday."\"  data-toggle=\"modal\" data-target=\"#myModal_OT".$payday."\"   title=\"File this OT\" class=\"pull-right\" style=\"font-size:1.2em;\" href=\"#\"><i class=\"fa fa-credit-card\"></i></a>";
                 

              
                
                //$totalbill = number_format($endOfShift->diffInMinutes(Carbon::parse($userLogOUT[0]['timing'],"Asia/Manila") )/60,2);

                //**** we need to check first kung naka 8hrs of work na
                //**** kaso pano kung part-timer lang??
                if ( number_format($wh/60,2) > 8.0 )
                {
                  if ($isPartTimer || $ptSched || $isPartTimerForeign)
                  {
                    if($ptOverride)  { $UT = round((480.0 - $wh )/60,2); $UT2 = 480.0 - $wh; }
                    else { $UT = number_format($minsLate/60,2); } 

                    $totalbill = number_format( ($userLogIN[0]['timing']->diffInMinutes($userLogOUT[0]['timing'] ) - 480) /60,2);
                   

                  }
                  else $totalbill = number_format( $endOfShift->diffInMinutes($userLogOUT[0]['timing'] )/60,2);
                }
                else //need muna nya macomplete 8hrs of work
                {
                  if ($isPartTimer || $ptSched || $isPartTimerForeign)
                  {
                    if($ptOverride)  { $UT = round((480.0 - $wh )/60,2); $UT2 = 480.0 - $wh; }
                    else { $UT = number_format((240.0 - $wh)/60,2);$UT2 = 240.0 - $wh; } 

                    if( number_format($wh/60,2) > 8.0 )
                      $totalbill = number_format( $endOfShift->diffInMinutes($userLogOUT[0]['timing'] )/60,2);
                    else
                      $totalbill = 0;

                  }
                  
                  else
                    {
                      if ($is4x11) {
                        $UT = round((600.0 - $wh )/60,2);  //number_format((480.0 - $wh)/60,2); 44.44;
                        $UT2 = 600.0 - $wh;
                      }
                      else {
                        $UT = round((480.0 - $wh )/60,2); 
                        $UT2 = 480.0 - $wh; 
                      }

                      $sSh = Carbon::parse($startOfShift->format('Y-m-d H:i'),'Asia/Manila')->addHours(5);

                      //kunin mo muna ilang hours bago sya nagOUT kung legit OT
                      $bagoMagout = number_format( (($userLogOUT[0]['timing']->diffInMinutes($endOfShift))/60)-$UT,2);

                      if ($bagoMagout > $UT)
                        $totalbill = $bagoMagout;
                      else
                        $totalbill =  number_format(($userLogOUT[0]['timing']->diffInMinutes($sSh)-480 )/ 60,2);   //$sSh->diffInMinutes($userLogOUT[0]['timing'] )/60,2);
                    }


                  

                }

                

                
                  
                
                //$totalbill = 33.33;
                

                //if ($totalbill > 0.25)
                if ($totalbill >= 0.01)
                {
                  $billableForOT = $totalbill;
                  $OTattribute = $icons;
                }
                  
                else { $billableForOT = 0; /*$totalbill*/; $OTattribute = "&nbsp;&nbsp;&nbsp;";} 

                //$stat = User::find($user_id)->status_id;
                //****** part time user

                /*if ($isPartTimer || $isPartTimerForeign)
                {
                  ($ptOverride) ? $UT = round((480.0 - $wh )/60,2) : $UT = round((240.0 - $wh)/60,2); 

                }
                
                else
                  {
                    if ($is4x11)
                      $UT = round((600.0 - $wh )/60,2);  //number_format((480.0 - $wh)/60,2); 44.44;
                    else
                      $UT = round((480.0 - $wh )/60,2); 
                  }*/

                  



            } //normal LateIN process


           

        }
        else //super undertime sya
        {
            //$wh = Carbon::parse($payday." ".$schedForToday['timeEnd'],"Asia/Manila")->diffInMinutes(Carbon::parse($userLogIN[0]['timing'],"Asia/Manila")->addMinutes(60));
            $comp->push(['out'=>Carbon::parse($userLogOUT[0]['timing'],"Asia/Manila"),'sched'=>Carbon::parse($payday." ".$schedForToday['timeStart'],"Asia/Manila")]);

            $wh = $endOfShift->diffInMinutes(Carbon::parse($userLogIN[0]['timing'],"Asia/Manila")->addMinutes(60));
            
             
              if ($hasSL)
              {
                $workedHours1 = $this->processLeaves($theDay->format('Y-m-d'),'SL',true,$wh,$slDeet,$hasPendingSL,$icons,$userLogIN[0],$userLogOUT[0],$shiftEnd,$shiftStart);
                $workedHours .= $workedHours1[0]['workedHours'];
                $UT = $workedHours1[0]['UT'];

              }//end if has SL
              

               if ($hasVL)
              {
                $workedHours1 = $this->processLeaves($theDay->format('Y-m-d'),'VL',true,$wh,$vlDeet,$hasPendingVL,$icons,$userLogIN[0],$userLogOUT[0],$shiftEnd,$shiftStart);
                $workedHours .= $workedHours1[0]['workedHours'];
                $UT = $workedHours1[0]['UT'];
              }//end if has VL

              if ($hasVTO)
              {
                $workedHours1 = $this->processLeaves($theDay->format('Y-m-d'),'VTO',false,$wh,$vtoDeet,$hasPendingVTO,$icons,$userLogIN[0],$userLogOUT[0],$shiftEnd,$shiftStart);
                $workedHours .= $workedHours1[0]['workedHours'];
                $UT = $workedHours1[0]['UT'];

              }//end if has VTO

               if ($hasOBT)
              {

                  $workedHours1 = $this->processLeaves($theDay->format('Y-m-d'),'OBT',true,$wh,$obtDeet,$hasPendingOBT,$icons,$userLogIN[0],$userLogOUT[0],$shiftEnd,$shiftStart);
                  $workedHours .= $workedHours1[0]['workedHours'];
                  $UT = $workedHours1[0]['UT'];

              }//end if has OBT


              if ($hasLWOP)
              {
                  $wh = $endOfShift->diffInMinutes(Carbon::parse($userLogIN[0]['timing'],"Asia/Manila")); //->addMinutes(60));
                  $workedHours1 = $this->processLeaves($theDay->format('Y-m-d'),'LWOP',true,$wh,$lwopDeet,$hasPendingLWOP,$icons,$userLogIN[0],$userLogOUT[0],$shiftEnd,$shiftStart);
                  $workedHours .= $workedHours1[0]['workedHours'];
                  $UT = $workedHours1[0]['UT'];

              }//end if has LWOP


              if ($hasFL)
              {
                $workedHours1 = $this->processLeaves($theDay->format('Y-m-d'),'FL',true,$wh,$flDeet,$hasPendingFL,$icons,$userLogIN[0],$userLogOUT[0],$shiftEnd,$shiftStart);
                $workedHours .= $workedHours1[0]['workedHours'];
                $UT = $workedHours1[0]['UT'];
              }//end if has FL



              if (!$hasSL && !$hasVL && !$hasLWOP && !$hasOBT && !$hasFL && !$hasVTO)
                {
                  $workedHours .= number_format($wh/60,2)."<br/><small>(Late IN)</small>";

                  //$stat = User::find($user->id)->status_id;
                  //****** part time user

                  if ($isPartTimer || $ptSched) 
                  {
                    ($ptOverride) ? $UT = round((480.0 - $wh )/60,2) : $UT = round((240.0 - $wh)/60,2); //33.33; //
                  }
                  
                  else
                    {
                      if ($is4x11)
                        $UT = round((600.0 - $wh )/60,2);  //number_format((480.0 - $wh)/60,2); 44.44;
                      else
                        $UT = round((480.0 - $wh )/60,2); 
                    }

                   

                 

                  //check mo muna kung nag OUT sya ng sobra sa ShiftEnd nya
                  $schedEnd = Carbon::parse($payday." ".$schedForToday['timeEnd'],"Asia/Manila");
                  $outNya = Carbon::parse($userLogOUT[0]['timing'],"Asia/Manila");

                  if ($outNya->format('Y-m-d H:i:s') > $schedEnd->format('Y-m-d H:i:s') ){
                    $billableForOT= number_format($outNya->diffInMinutes($schedEnd)/60,2);

                    //check first if Locked na DTR for that production date
                  $verifiedDTR = User_DTR::where('productionDate',$payday)->where('user_id',$user->id)->get();
                  if (count($verifiedDTR) > 0)
                    $OTattribute = "<a title=\"Unlock DTR to file this OT\" class=\"pull-right text-gray\" style=\"font-size:1.2em;\"><i class=\"fa fa-credit-card\"></i></a>";
                  else
                   $OTattribute = "<a id=\"OT_".$payday."\"  data-toggle=\"modal\" data-target=\"#myModal_OT".$payday."\"  title=\"File this OT\" class=\"pull-right\" style=\"font-size:1.2em;\" href=\"#\"><i class=\"fa fa-credit-card\"></i></a>";
                 


                    
                  }
                  else
                    $billableForOT=0;
                }

              if ($hasHolidayToday)
                  {
                    $workedHours .= "<br/> <strong>* ". $holidayToday->first()->name. " *</strong>";
                  }



        }//end else super undertime
        
      }//end if lateIN
      else {
        //$koll=["from"=>$isPartTimer,'diffHours'=>$diffHours];

        normalProcess:

        if($isExempt)
        {
          $wh = Carbon::parse($userLogOUT[0]['logTxt'],"Asia/Manila")->diffInMinutes( Carbon::parse($userLogIN[0]['logTxt'],"Asia/Manila")->addHour(1) );

        }
        else
        {

            if ($isPartTimer || $ptSched || $isPartTimerForeign ) {
              $wh = Carbon::parse($userLogOUT[0]['logTxt'],"Asia/Manila")->diffInMinutes(Carbon::parse($payday." ".$schedForToday['timeStart'],"Asia/Manila"));
            }
            else
            {

              //fix Mochow case na 3.02HR instead of 4
              if ($diffHours <= 4){
                $wh = Carbon::parse($userLogOUT[0]['logTxt'],"Asia/Manila")->diffInMinutes(Carbon::parse($payday." ".$schedForToday['timeStart'],"Asia/Manila"));
              }
              else{
                $wh = Carbon::parse($userLogOUT[0]['logTxt'],"Asia/Manila")->diffInMinutes(Carbon::parse($payday." ".$schedForToday['timeStart'],"Asia/Manila")->addHour());
              }

            }

        }

        
          


         
            proceedWithNormal:

                /* --- NOTE: shiftEnd is date('h:i A') --- */

                if ($wh > 480) //more than 8hrwork
                {
                  if($isPartTimer || $isPartTimerForeign )
                    ($ptOverride) ? $workedHours = '8.00' : $workedHours = '8.00 <br/>(PT)';
                  else 
                  {
                    if($hasHolidayToday && $isBackoffice)
                    {
                      $UT = 0;
                      //additional check kung may approved OT time na eh di yun dapat ang lalabas
                      if(count($mayOT) > 0)
                        $workedHours = $mayOT->first()->filed_hours; //number_format($wh/60,2); 

                      else{
                            if ($wh >= 300) $wh = $wh-60;
                            $workedHours = number_format($wh/60,2); 

                      }

                    }else
                    {
                      if($isExempt) {
                        //determin of course kung Flexi anytime; pag flexi 8hr kasi, mas of 8 lang lalabas dapat
                        if($exemptEmp[0]->schedType_id == '2') //flexi 8hr
                        {
                          $workedHours = 8.00;
                        } else $workedHours = number_format($wh/60,2);
                      }//Carbon::parse($userLogOUT[0]['logTxt'],"Asia/Manila")->diffInMinutes( Carbon::parse($userLogIN[0]['logTxt'],"Asia/Manila")->addHour(1) );//
                      else
                      {
                        ($is4x11) ? $workedHours = 10.00 : $workedHours = 8.00;
                      }
                      

                    }
                     
                  }
                  $UT=0; //workedHours =8.00;

                  //check first if Locked na DTR for that production date
                  $verifiedDTR = User_DTR::where('productionDate',$payday)->where('user_id',$user->id)->get();
                  if (count($verifiedDTR) > 0)
                    $icons = "<a title=\"Unlock DTR to file an OT\" class=\"pull-right text-gray\" style=\"font-size:1.2em;\"><i class=\"fa fa-credit-card\"></i></a>";
                  else
                  {
                    if($hasHolidayToday && $isBackoffice)
                      $icons = "<a id=\"OT_".$payday."\"  data-toggle=\"modal\" data-target=\"#myModal_OT".$payday."\"  title=\"File this Holiday OT\" class=\"pull-right\" style=\"font-size:1.2em;\" href=\"#\"><i class=\"fa fa-credit-card\"></i></a>";
                    else
                      $icons = "<a id=\"OT_".$payday."\"  data-toggle=\"modal\" data-target=\"#myModal_OT".$payday."\"  title=\"File this OT\" class=\"pull-right\" style=\"font-size:1.2em;\" href=\"#\"><i class=\"fa fa-credit-card\"></i></a>";
                   
                  }

                  //------ override for holiday ng backoffice ---------
                  // if ($hasHolidayToday && $isBackoffice) $icons="";
                  // ---------------------------------------------------

                 if(strlen($userLogOUT[0]['logTxt']) >= 18) //hack for LogOUT with date
                 {
                    $t = Carbon::parse($userLogOUT[0]['logTxt'],'Asia/Manila');

                    if($hasHolidayToday && $isBackoffice)
                      $totalbill = $workedHours; //number_format($wh/60,2);//number_format( $endOfShift->diffInMinutes($userLogOUT[0]['timing'] )/60,2);
                    
                    elseif ($isPartTimer || $isPartTimerForeign)
                    {
                          if($ptOverride)
                            $totalbill = number_format(($wh - 480)/60,2);
                          else
                          {
                            ($wh > 480) ? $totalbill = number_format(($wh - 480)/60,2) : $totalbill=0;
                          } 
                    } else{

                      if($isExempt) $totalbill = 0;
                      else
                        $totalbill = number_format( $endOfShift->diffInMinutes($userLogOUT[0]['timing'] )/60,2);
                    } 

                 } 
                 else{ 
                    $t = Carbon::parse($userLogOUT[0]['timing'],'Asia/Manila')->format('H:i:s');

                    if($hasHolidayToday && $isBackoffice)
                      $totalbill = $workedHours; //number_format($wh/60,2);  //number_format( $endOfShift->diffInMinutes($userLogOUT[0]['timing'] )/60,2);
                    else 
                    {
                      if ($isPartTimer || $isPartTimerForeign)
                      {
                        if($ptOverride)
                          $totalbill = number_format(($wh - 480)/60,2);
                        else
                        {
                          ($wh > 480) ? $totalbill = number_format(($wh - 480)/60,2) : $totalbill=0;
                        } 
                      }
                      else{
                        if($isExempt) $totalbill = 0;
                        else
                          $totalbill = number_format( $endOfShift->diffInMinutes($userLogOUT[0]['timing'] )/60,2);
                      }
                    }
                    //$totalbill = 244.44;
                 }

                   //------ override for holiday ng backoffice ---------
                  // if ($hasHolidayToday && $isBackoffice) //$totalbill=0.0;
                  // ---------------------------------------------------


                  

                  if ($totalbill >= 0.01)  //($totalbill >= 0.25)
                  {
                    $billableForOT = $totalbill;
                    $OTattribute = $icons;
                  }
                  else if ($hasHolidayToday && $isBackoffice)
                  {
                    $billableForOT = $totalbill;
                    $OTattribute = $icons;

                  }else { $billableForOT = 0; /* $totalbill*/; $OTattribute= "&nbsp;&nbsp;&nbsp;";} 

                  if ($hasHolidayToday)
                      {
                        $workedHours .= "<br/> <strong>[* ". $holidayToday->first()->name. " *]</strong>";
                      }


                }

                else if($wh >= 240 && $wh < 300) //more than 4hr work
                {
                  if ($isPartTimer || $ptSched || $isPartTimerForeign)
                  {
                      ($ptOverride) ? $workedHours ="8.00": $workedHours ="4.00"; $UT = 0;// number_format(240 - $wh,2);
                  }else
                  {
                    //we need to make sure deduct 1hr break
                     

                    if($hasHolidayToday && $isBackoffice)
                    {
                       //additional check kung may approved OT time na eh di yun dapat ang lalabas
                      if(count($mayOT) > 0)
                        $workedHours = $mayOT->first()->filed_hours; //number_format($wh/60,2); 
                      else{
                            if ($wh >= 300) $wh = $wh-60;
                            $workedHours = number_format($wh/60,2);
                      }
                      
                      $UT = 0;

                    }
                    else
                      {
                        $workedHours = number_format($wh/60,2);
                        ($is4x11) ? $UT = number_format(10 - $workedHours,2) : $UT = number_format(8 - $workedHours,2);
                      }

                    
                  }

                  //check first if Locked na DTR for that production date
                  $verifiedDTR = User_DTR::where('productionDate',$payday)->where('user_id',$user->id)->get();
                  if (count($verifiedDTR) > 0)
                    $icons = "<a title=\"Unlock DTR to file an OT\" class=\"pull-right text-gray\" style=\"font-size:1.2em;\"><i class=\"fa fa-credit-card\"></i></a>";
                  else
                  {
                    if($hasHolidayToday && $isBackoffice)
                      $icons = "<a id=\"OT_".$payday."\"  data-toggle=\"modal\" data-target=\"#myModal_OT".$payday."\"  title=\"File this Holiday OT\" class=\"pull-right\" style=\"font-size:1.2em;\" href=\"#\"><i class=\"fa fa-credit-card\"></i></a>";
                    else
                      $icons = "<a id=\"OT_".$payday."\"  data-toggle=\"modal\" data-target=\"#myModal_OT".$payday."\"  title=\"File this OT\" class=\"pull-right\" style=\"font-size:1.2em;\" href=\"#\"><i class=\"fa fa-credit-card\"></i></a>";
                  }
                   

                  if(strlen($userLogOUT[0]['logTxt']) >= 18) //hack for LogOUT with date
                  {
                      $t = Carbon::parse($userLogOUT[0]['logTxt'],'Asia/Manila');
                      if ($isPartTimer || $ptSched || $isPartTimerForeign)
                      {
                        if($ptOverride)
                          $totalbill = number_format(($wh - 480)/60,2);
                        else
                        {
                          ($wh > 480) ? $totalbill = number_format(($wh - 480)/60,2) : $totalbill=0;
                        } 
                      }
                      else if($hasHolidayToday && $isBackoffice)
                      {
                        $totalbill = $workedHours;

                      }
                      else $totalbill = number_format(($wh - 480)/60,2);
                    
                  }else{ 
                    $t = Carbon::parse($userLogOUT[0]['logTxt'],'Asia/Manila');
                    if($hasHolidayToday && $isBackoffice)
                      $totalbill =$workedHours;
                    else 
                    {
                      //check mo muna kung entitled ba talga o baka part timer lang
                      if ($isPartTimer || $ptSched || $isPartTimerForeign)  {
                        //($wh > 480) ? $totalbill = number_format( $endOfShift->diffInMinutes($t)/60,2) : $totalbill=0.0;
                        ($wh > 480) ? $totalbill = number_format(($wh - 480)/60,2) : $totalbill=0;
                      }
                      else
                        $totalbill = number_format( $endOfShift->diffInMinutes($t)/60,2);

                    }
                    
                  }


                  

                  if ($totalbill >= 0.01) // ($totalbill > 0.25)
                  {
                    $billableForOT = $totalbill;
                    $OTattribute = $icons;
                  }
                  else  if ($hasHolidayToday && $isBackoffice)
                  {
                    $billableForOT = $totalbill;
                    $OTattribute = $icons;

                  } 
                  else { $billableForOT = 0; /* $totalbill*/; $OTattribute= "&nbsp;&nbsp;&nbsp;";} 

                  if ($hasHolidayToday)
                      {
                        $workedHours .= "<br/> <strong>[* ". $holidayToday->first()->name. " *]</strong>";
                      }


                } //end 240
                else 
                {
                  if($hasHolidayToday && $isBackoffice)
                  {

                      //additional check kung may approved OT time na eh di yun dapat ang lalabas
                      if(count($mayOT) > 0){
                        $workedHours = $mayOT->first()->filed_hours; // 
                        $billableForOT = $mayOT->first()->filed_hours; //number_format($wh/60,2);
                      }
                      else {
                        $workedHours = number_format($wh/60,2); //$wh;
                        $billableForOT = number_format($wh/60,2); 
                      }

                      $icons = "<a id=\"OT_".$payday."\"  data-toggle=\"modal\" data-target=\"#myModal_OT".$payday."\"  title=\"File this Holiday OT\" class=\"pull-right\" style=\"font-size:1.2em;\" href=\"#\"><i class=\"fa fa-credit-card\"></i></a>"; 
                      
                      
                      $workedHours .= "<br/> <strong>[* ". $holidayToday->first()->name. " *]</strong>";
                      $OTattribute = $icons;
                      $UT=0;
                  }
                  else
                  {
                    $workedHours = number_format($wh/60,2);
                    $billableForOT=0; 
                      if ($hasHolidayToday)
                      {
                        $workedHours .= "<br/> <strong>[* ". $holidayToday->first()->name. " *]</strong>";
                      }

                  }
                    
                }//end else di overworked, sakto lang

                  //$UT = '0';
            
          


      } //endif else normal logs
      
     
    } //end if may login and logout
    else
    {
      if($isPartTimer || $ptSched || $isPartTimerForeign)
        ($ptOverride) ? $WHcounter = 8.0 : $WHcounter = 4.0;
      else {
        ($is4x11) ? $WHcounter = 10.0 : $WHcounter = 8.0; 
      }

      $UT=0;
      $link = action('UserController@myRequests',$user->id);
      $icons ="";
      $workedHours=null;$log="";


      if ($hasVL)
      {
          $workedHours1 = $this->processLeaves($theDay->format('Y-m-d'),'VL',false,$WHcounter,$vlDeet,$hasPendingVL,$icons,$userLogIN[0],$userLogOUT[0],$shiftEnd,$shiftStart);
          $workedHours .= $workedHours1[0]['workedHours'];
          $UT = $workedHours1[0]['UT'];
          $billableForOT = $workedHours1[0]['billableForOT'];
          $OTattribute = $workedHours1[0]['OTattribute'];

          

      }//end if has VL

      if ($hasVTO)
      {
          $workedHours1 = $this->processLeaves($theDay->format('Y-m-d'),'VTO',false,$WHcounter,$vtoDeet,$hasPendingVTO,$icons,$userLogIN[0],$userLogOUT[0],$shiftEnd,$shiftStart);
          $workedHours .= $workedHours1[0]['workedHours'];
          $UT = $workedHours1[0]['UT'];

          

      }//end if has VTO


      if ($hasOBT)
      {
          $workedHours1 = $this->processLeaves($theDay->format('Y-m-d'),'OBT',false,$WHcounter,$obtDeet,$hasPendingOBT,$icons,$userLogIN[0],$userLogOUT[0],$shiftEnd,$shiftStart);
          $workedHours .= $workedHours1[0]['workedHours'];
          $UT = $workedHours1[0]['UT'];

          

      }//end if has OBT



      if ($hasSL)
      {
          $workedHours1 = $this->processLeaves($theDay->format('Y-m-d'),'SL',false,$WHcounter,$slDeet,$hasPendingSL,$icons,$userLogIN[0],$userLogOUT[0],$shiftEnd,$shiftStart);
          $workedHours .= $workedHours1[0]['workedHours'];
          $UT = $workedHours1[0]['UT'];

         
      }//end if has SL



      if ($hasLWOP)
      {
          $workedHours1 = $this->processLeaves($theDay->format('Y-m-d'),'LWOP',false,0,$lwopDeet,$hasPendingLWOP,$icons,$userLogIN[0],$userLogOUT[0],$shiftEnd,$shiftStart);
          $workedHours .= $workedHours1[0]['workedHours'];
          $UT = $workedHours1[0]['UT'];
          $billableForOT = $workedHours1[0]['billableForOT'];
          $OTattribute = $workedHours1[0]['OTattribute'];

         

      }//end if has LWOP


      if ($hasFL)
      {
          $workedHours1 = $this->processLeaves($theDay->format('Y-m-d'),'FL',false,0,$flDeet,$hasPendingFL,$icons,$userLogIN[0],$userLogOUT[0],$shiftEnd, $shiftStart);
          $workedHours .= $workedHours1[0]['workedHours'];
          $UT = $workedHours1[0]['UT'];

         

      }//end if has FL

      

      /***--- we will need to check if Non-Ops personnel, may pasok kasi pag OPS**/
      if ($hasHolidayToday)  
      {

        $whs = "(8.0)<br/> <strong>[* " . $holidayToday->first()->name . " *]</strong>";
        $workedHours .=$whs;
      }

     if (!$hasVL && !$hasSL && !$hasLWOP &&  !$hasOBT && !$hasFL && !$hasHolidayToday){

        //$workedHours = "<a title=\"Check your Biometrics data. \n It's possible that you pressed a wrong button, the machine malfunctioned, or you really didn't log in / out.\"><strong class=\"text-danger\">AWOL </strong></a>";
     }
    }

     

    $data->push([
                  'koll'=>['diffHours'=>$diffHours, 'checkEndShift'=>$checkEndShift,'checkSShift'=>$checkSShift],
                  'billableForOT'=>$billableForOT,
                  'holidayToday'=>$holidayToday, 
                  'hdToday'=>$holidayToday,
                  'schedForToday'=>$schedForToday,
                  'OTattribute'=>$OTattribute, 
                  
                  //'wh'=>$wh,'comp'=>$comp,
                  'isBackoffice'=>$isBackoffice,
                  'workedHours'=>$workedHours, //$koll, // $wh,// 
                  'UT'=>$UT, 'VL'=>$hasVL, 'SL'=>$hasSL, 'FL'=>$hasFL,  'LWOP'=>$hasLWOP, 'VTO'=>$vtimeoff ]);
   



    return $data;


  }

  public function getWorkedOThours($log,$sched,$prodDate1, $prodDate2)
  {
    $lg = Carbon::parse($prodDate1." ".$log,'Asia/Manila');
    $schd = Carbon::parse($prodDate2." ".$sched,'Asia/Manila');

    return $lg->diffInMinutes($schd);
  }





  public function getWorkSchedForTheDay1($user, $leaveDay, $hasPending,$isStylized)
  {
      DB::connection()->disableQueryLog();
      //$user = User::find($id);
      $vl_to = $leaveDay;

      $productionDate = Carbon::parse($vl_to,'Asia/Manila');
      $pd = $productionDate->format('Y-m-d');

      $today = Carbon::now('GMT+8'); //create(date('Y'), date('m'), date('d'),0,0,0, 'Asia/Manila');
      $endDate = Carbon::parse($vl_to,'Asia/Manila');
      
      $coll = new Collection;
      $coll2 = new Collection;
      $counter = 0;
      $totalMschedules = count($user->monthlySchedules);
      $totalFschedules = count($user->fixedSchedule);

        
      $noWorkSched = true;
      $startPt = null;
      $sched = null; $schedule=new Collection;
      $stat = "";
      $workSched_monthly=null; $RDsched_monthly=null; $workSched_fixed=null; $RDsched_fixed=null;



      $workSched_monthly = MonthlySchedules::where('user_id',$user->id)->where('productionDate',$pd)->
                            where('isRD',0)->orderBy('productionDate','ASC')->get();
      $RDsched_monthly = MonthlySchedules::where('user_id',$user->id)->where('productionDate',$pd)->where('isRD',1)->
                            orderBy('created_at','DESC')->get();
      $workSched_fixed = collect(FixedSchedules::where('user_id',$user->id)->where('isRD',0)->orderBy('created_at','DESC')->get())->
                          groupBy('schedEffectivity');
      $RDsched_fixed = collect(FixedSchedules::where('user_id',$user->id)->where('isRD',1)->orderBy('created_at','DESC')->get())->
                          groupBy('schedEffectivity');

      //return (['workSched_monthly'=>$workSched_monthly,'RDsched_monthly'=>$RDsched_monthly, 'workSched_fixed'=>$workSched_fixed,'RDsched_fixed'=>$RDsched_fixed]);

      if (!is_null($RDsched_fixed)) //** understood na if no fixed RD, there's no fixed sched at all
      {

        $dt  = $productionDate->dayOfWeek;
          switch($dt){
            case 0: $dayToday = 6; break;
            case 1: $dayToday = 0; break;
            default: $dayToday = $dt-1;
          }

        $actual_fixed_WS = $this->getLatestFixedSchedGrouped($workSched_fixed,$productionDate->format('Y-m-d'),$dayToday);
        $actual_fixed_RD = $this->getLatestFixedSchedGrouped($RDsched_fixed,$productionDate->format('Y-m-d'),$dayToday);

        //return response()->json(['actual_fixed_WS'=>$actual_fixed_WS,'actual_fixed_RD'=>$actual_fixed_RD]);

        if (is_null($actual_fixed_RD['workday']) && is_null($actual_fixed_WS['workday']) )
        {
          //** meaning di pa effective, so check monthly sched instead
          goto monthlySchedNa;
        }
        else
        {
          //** check which is more updated, fixed or monthly

          if ($actual_fixed_WS['workday'] == null)
          {
            //** if walang fixed WS, assumed na its fixed RD
            //** pero check muna vs monthly
            if (count($RDsched_monthly) > 0)
            {
              if ($RDsched_monthly[0]->created_at > $actual_fixed_RD['created_at'])
                $sched = $RDsched_monthly[0];
              else $sched = $actual_fixed_RD;
            }
            else //kung walang RD monthly, baka WS monthly
            {
              if (count($workSched_monthly) > 0)
              {
                //compare mo alin mas latest between the two
                if ($workSched_monthly[0]->created_at > $actual_fixed_RD['created_at'])
                  $sched = $workSched_monthly[0];
                else
                  $sched = $actual_fixed_RD;

              }
              else
              $sched= $actual_fixed_RD;
            }   

            

          }

          if ($actual_fixed_RD['workday'] == null)
          {
            //** if walang fixed RD, assumed na its fixed WS
            //** pero check muna vs monthly
            if (count($workSched_monthly) > 0)
            {
              if ($workSched_monthly[0]->created_at > $actual_fixed_WS['created_at'])
                $sched = $workSched_monthly[0];
              else $sched = $actual_fixed_WS;
            }
            else
            {
              if (count($RDsched_monthly) > 0)
              {
                if ($RDsched_monthly[0]->created_at > $actual_fixed_WS['created_at'])
                {
                  $sched = $RDsched_monthly[0];

                }else $sched = $actual_fixed_WS;

              }else  $sched= $actual_fixed_WS; 
              

              

            }
            //else $sched= $actual_fixed_WS;  

          }

          if($actual_fixed_WS['workday'] !== null && $actual_fixed_RD['workday']!==null)
          {
            //kunin mo sino pinaka latest
            if ($actual_fixed_WS['created_at'] > $actual_fixed_RD['created_at'] )
              $sched = $actual_fixed_WS;
            else
              $sched = $actual_fixed_RD;
          }

          
        }

        
      }
      else 
      {
        //*** no fixed, therefore monthly sched na sya
        monthlySchedNa:

            if (count($workSched_monthly) > 0)
              $sched = $workSched_monthly[0];
            else
              $sched = $RDsched_monthly[0];

      }

      //** now that we have the sched, check if may CWS

      //return response()->json(['SCHED'=>$sched,'actual_fixed_WS'=>$actual_fixed_WS,'actual_fixed_RD'=>$actual_fixed_RD, 'workSched_monthly'=>$workSched_monthly,'RDsched_monthly'=>$RDsched_monthly,'workSched_fixed'=>$workSched_fixed,'RDsched_fixed'=>$RDsched_fixed ]);
     
      $bio = Biometrics::where('productionDate',$pd)->get();
      if ($productionDate->isPast() && !$productionDate->isToday()) {
              $bgcolor = "#e6e6e6";
              $border = "#e6e6e6";
              $startColor = "#7b898e"; $endColor = "#7b898e";
            } else {
              $bgcolor="#fff"; $border="#fff";$startColor = "#548807"; $endColor = "#bd3310";
            }


      if (count($bio)>0)
      {
        $b = $bio->first(); 
        $cws = User_CWS::where('biometrics_id',$b->id)->where('user_id',$user->id)->where('isApproved',1)->orderBy('updated_at','DESC')->get();
        //return (['cws'=>$cws]);
          if (count($cws)> 0)
          {

            //check mo muna kung alin mas recent between the sched and cws
            

            //but before that, check mo kung manually set as null yung created at, if yes:CWS NA SYA
            if( is_object($sched) ){
              $cwsNa=true;
              $s = $sched->created_at;
            }else {
              $cwsNa=false;
              $s = $sched['created_at'];
            }

            if ($cwsNa || $cws->first()->created_at > $s){

              //check mo muna kung RD

              if ($cws->first()->timeStart == $cws->first()->timeEnd)
              {
                if($isStylized === true)
                {
                  //means 00:00:00 to 00:00:00

                   $schedule->push(['title'=>'Rest day ',
                              'start'=>$productionDate->format('Y-m-d') . " 00:00:00", // dates->format('Y-m-d H:i:s'),
                              'end'=>$productionDate->format('Y-m-d') . " 00:00:00",
                              'textColor'=> '#ccc',
                              'backgroundColor'=> '#fff',
                              'chenes'=>$productionDate,
                              'biometrics_id'=> $b->id
                               ]);
                   $schedule->push(['title'=>'.',
                                  'start'=>$productionDate->format('Y-m-d') . " 00:00:00", // dates->format('Y-m-d H:i:s'),
                                  'textColor'=> '#fff',
                                  'borderColor'=>$border,
                                  'backgroundColor'=> '#e6e6e6',
                                  'chenes'=>$productionDate->format('Y-m-d'),'icon2'=>"bed", 'biometrics_id'=>null
                                   ]);


                }
                else
                {
                  $schedule = $cws->first();

                }

                return $schedule;
                 
              }
              else 
              {

                 $correctTime = Carbon::parse($productionDate->format('Y-m-d') . " ". $cws->first()->timeStart,"Asia/Manila");

                 if($isStylized===true)
                 { //return (['isStylized'=>$isStylized]);
                    $schedule->push(['title'=> date('h:i A', strtotime($cws->first()->timeStart)) . " to ",// '09:00 AM ',
                          'start'=>$productionDate->format('Y-m-d') . " ". $cws->first()->timeStart, 
                          'end'=>$correctTime->addHour(9)->format('Y-m-d H:i:s'),
                          'textColor'=>  $startColor,// '#548807',// '#409c45',
                          'backgroundColor'=> $bgcolor,
                          'icon'=>"play-circle", 'biometrics_id'=> $b->id]);

                    $schedule->push(['title'=>date('h:i A', strtotime($cws->first()->timeEnd)),
                                      'start'=>$productionDate->format('Y-m-d') . " ". $cws->first()->timeStart, //. $sched->timeEnd,
                                      'end'=>$correctTime->format('Y-m-d H:i:s'),
                                      'textColor'=> $endColor,// '#bd3310',// '#27a7f7',
                                      'backgroundColor'=> $bgcolor,
                                      'icon'=>"stop-circle", 'biometrics_id'=> $b->id]);
                    return $schedule;

                 } else return $cws->first();

                 
                 

              }

            } else{
              // else, get the sched not the cws
              goto proceedToSchedules;
            }
          } 
          else 
          {

            //else, get the sched

            

            
            proceedToSchedules: 
            //return (['isStylized'=>$isStylized, 'sched'=>$sched]);

                if($isStylized === true)
                {
                  if ($sched->isRD)
                  {
                     $schedule->push(['title'=>'Rest day ',
                                          'start'=>$pd . " 00:00:00", // dates->format('Y-m-d H:i:s'),
                                          'end'=>$pd . " 00:00:00",
                                          'textColor'=> '#ccc',
                                          'backgroundColor'=> '#fff',
                                          'chenes'=>$pd,'icon'=>" ", 'biometrics_id'=>$b->id
                                           ]);
                     $schedule->push(['title'=>'.',
                                          'start'=>$pd . " 00:00:00", // dates->format('Y-m-d H:i:s'),
                                          'end'=>$pd . " 00:00:00",
                                          'textColor'=> '#fff',
                                          'borderColor'=>$border,
                                          'backgroundColor'=> '#e6e6e6',
                                          'chenes'=>$pd,'icon2'=>"bed", 'biometrics_id'=>$b->id
                                           ]);


                  }
                  else 
                  {
                    $correctTime = Carbon::parse($pd  ." ".$sched->timeStart,"Asia/Manila");
                    $schedule->push(['title'=> date('h:i A', strtotime($sched->timeStart)) . " to ",
                                  'end'=>$correctTime->addHour(9)->format('Y-m-d H:i:s'),
                                  'textColor'=>  $startColor,// '#409c45',
                                  'backgroundColor'=> $bgcolor,'borderColor'=>$border,
                                  'icon'=>"play-circle", 'biometrics_id'=>$b->id]);
                    $schedule->push(['title'=>date('h:i A', strtotime($sched->timeEnd)),
                                          'start'=>$pd  ." ".$sched->timeStart,
                                          'end'=>$correctTime->format('Y-m-d H:i:s'),
                                          'textColor'=> $endColor,// '#27a7f7',
                                          'backgroundColor'=> $bgcolor,'borderColor'=>$border,
                                          'icon'=>"stop-circle", 'biometrics_id'=>$b->id]);

                  }
                  return $schedule;
                }
                else
                  //return $b;
                  return $sched; //** we just return raw schedule
                
                  
          }
      }
      else
      {
        //** just return the sched
        return $sched;

      } 

    
  }


  public function processLeaveForLateInEarlyOut($totalCredits,$scheduleStart,$userLogOUT,$endOfShift,$isPartTimer,$isPartTimerForeign, $ptOverride, $is4x11)
  {
      $workedHours = null;
      $billableForOT = 0;

      if($totalCredits == '0.5') {
        $magStart = Carbon::parse($scheduleStart->format('Y-m-d H:i:s'),"Asia/Manila")->addHours(5);
        $wh = Carbon::parse($userLogOUT[0]['timing'],'Asia/Manila')->diffInMinutes($magStart)+240;
        //$workedHours = $wh;

        $minsEarlyOut = $endOfShift->diffInMinutes(Carbon::parse($userLogOUT[0]['timing'],'Asia/Manila'));
        //if ($wh > 5 && !($isPartTimer || $isPartTimerForeign) ) $wh = $wh - 60;
  
        //we less 1hr for the break, BUT CHECK FIRST IF PART TIME OR NOT
        ($isPartTimer || $isPartTimerForeign) ? $workedHours = number_format(($wh)/60,2)."<br/><small>(late IN & early OUT)</small>" : $workedHours = number_format(($wh)/60,2)."<br/><small>(late IN & early OUT)</small>";

        $billableForOT=0;
        if ($isPartTimer || $isPartTimerForeign) {

          ($ptOverride) ? $UT = number_format((480.0 - (($wh) - $minsEarlyOut) )/60,2) : $UT = number_format((240.0 - ($wh - $minsEarlyOut) )/60,2); //number_format((240.0 - $wh)/60,2);
          
        }
        else {
          if ($is4x11)
            $UT = number_format((600.0 - (($wh) - ($minsEarlyOut)) )/60,2);  //number_format((480.0 - $wh)/60,2); 44.44;
          else
            $UT = number_format( ($minsEarlyOut) /60,2); 
        } 

      }
      else {
        $wh = Carbon::parse($userLogOUT[0]['timing'],'Asia/Manila')->diffInMinutes(Carbon::parse($userLogIN[0]['timing'],'Asia/Manila'));
        //$workedHours = $wh;

         $minsEarlyOut = $endOfShift->diffInMinutes(Carbon::parse($userLogOUT[0]['timing'],'Asia/Manila'));
         $minsLate = $scheduleStart->diffInMinutes(Carbon::parse($userLogIN[0]['timing'],'Asia/Manila'));
        //if ($wh > 5 && !($isPartTimer || $isPartTimerForeign) ) $wh = $wh - 60;
    
        //we less 1hr for the break, BUT CHECK FIRST IF PART TIME OR NOT
        ($isPartTimer || $isPartTimerForeign) ? $workedHours = number_format(($wh)/60,2)."<br/><small>(late IN & early OUT)</small>" : $workedHours = number_format(($wh-60)/60,2)."<br/><small>(late IN & early OUT)</small>";

        $billableForOT=0; 

        if ($isPartTimer || $isPartTimerForeign) {

          ($ptOverride) ? $UT = number_format((480.0 - (($wh-60) - $minsLate) )/60,2) : $UT = number_format((240.0 - ($wh - $minsLate) )/60,2); //number_format((240.0 - $wh)/60,2);
          
        }
        else {
          if ($is4x11)
            $UT = number_format((600.0 - (($wh-60) - ($minsLate+$minsEarlyOut)) )/60,2);  //number_format((480.0 - $wh)/60,2); 44.44;
          else
            $UT = number_format( ($minsLate+$minsEarlyOut) /60,2); 
        } 
      }

      $d = new Collection;
      $d->push(['workedHours'=>$workedHours,'billableForOT'=>$billableForOT,'UT'=>$UT]);

      return $d;

  }



  public function processLeaves($payday, $leaveType,$withIssue,$wh, $deet,$hasPending,$icons,$ins,$outs,$shiftEnd, $shiftStart)//$userLogIN[0]['logs'] || $userLogOUT[0]['logs']
  {
    $log=null;
    switch ($leaveType) {
      case 'OBT':{$link = action('UserOBTController@show',$deet->id);$lTitle = "OBT request";
                    if($deet->isApproved)
                    {

                      $i = "fa-suitcase";
                      $l = "OBT";
                      $label = "Official Business Trip";
                      $workedHours = 8.0;
                    } else
                    {
                       $i = "fa-times";
                        $l = "OBT";
                        $label = "OBT denied";
                        $workedHours = 0.0;
                    }
                    
              # code...
              }break;

      case 'LWOP':{$link = action('UserLWOPController@show',$deet->id);$lTitle = "LWOP request";
                    if($deet->isApproved)
                    {
                      
                      $i = "fa-meh-o";
                      $l = "LWOP";
                      $label = "Leave Without Pay";
                      $workedHours = " ";

                    }else{
                      $i = "fa-times";
                      $l = "LWOP";
                      $label = "LWOP DENIED";
                      $workedHours = 0.0;

                    }
                    
              # code...
              }break;

      case 'SL':{$link = action('UserSLController@show',$deet->id);$lTitle = "SL request";
                    if($deet->isApproved)
                    {

                      $i = "fa-stethoscope";
                      $l = "SL";
                      $label = "Sick Leave";
                      $workedHours = 8.0;
                    }else
                    {
                      $i = "fa-times";
                      $l = "SL";
                      $label = "SL DENIED";
                      $workedHours = 0.0;
                    }
                    
              # code...
              }break;

     case 'VL':{$link = action('UserVLController@show',$deet->id);$lTitle = "VL request";
                    if($deet->isApproved)
                    {
                      
                      
                      $i = "fa-plane";
                      $l = "VL";
                      $label = " Vacation Leave";
                      $workedHours = 8.0;

                    }else
                    {
                      $i = "fa-times";
                      $l = "VL";
                      $label = "VL DENIED";
                      $workedHours = 0.0;
                    }
                    
              # code...
              }break;

     case 'FL':{
                    $link = action('UserFamilyleaveController@show',$deet->id);

                    $theleave = User_Familyleave::find($deet->id);

                    if($theleave->isApproved)
                    {
                      switch ($theleave->leaveType) {
                        case 'ML':{
                                      $i = "fa-female";
                                      $lTitle = "ML request";
                                      $l = "ML";
                                      $label = "Maternity Leave";
                        }break;



                        case 'MC':{
                                      $i = "fa-female";
                                      $lTitle = "MC request";
                                      $l = "MC";
                                      $label = "Magna Carta Leave";
                        }break;

                        case 'PL':{
                                      $i = "fa-male";
                                      $lTitle = "PL request";
                                      $l = "PL";
                                      $label = "Paternity Leave";
                        }break;

                        case 'SPL':{
                                      $i = "fa-street-view";
                                      $lTitle = "SPL request";
                                      $l = "SPL";
                                      $label = "Single-Parent Leave";
                        }break;
                        
                        
                      }

                    }else{
                      switch ($theleave->leaveType) {
                        case 'ML':{
                                      $i = "fa-info-circle";
                                      $lTitle = "ML request";
                                      $l = "ML";
                                      $label = "ML denied";
                        }break;

                        case 'MC':{
                                      $i = "fa-info-circle";
                                      $lTitle = "MC request";
                                      $l = "MC";
                                      $label = "MC denied";
                        }break;

                        case 'PL':{
                                      $i = "fa-info-circle";
                                      $lTitle = "PL request";
                                      $l = "PL";
                                      $label = "PL denied";
                        }break;

                        case 'SPL':{
                                      $i = "fa-info-circle";
                                      $lTitle = "SPL request";
                                      $l = "SPL";
                                      $label = "SPL denied";
                        }break;
                        
                        
                      }
                    }
                    
                    
                    $workedHours = "N/A";
              # code...
              }break;

     case 'VTO':{$link = action('UserVLController@showVTO',$deet->id);$lTitle = "VTO request";
                    if($deet->isApproved)
                    {
                      
                      
                      $i = "fa-history";
                      $l = "VTO";
                      $label = " Voluntary Time Off ";

                      //but check first if unpaid VTO
                      if ($deet->deductFrom == 'LWOP')
                        $workedHours = round(number_format(8 - $deet->totalHours,2),PHP_ROUND_HALF_DOWN);// 8.0;
                      else
                        $workedHours = round(number_format($wh/60 + $deet->totalHours,2),PHP_ROUND_HALF_DOWN);// 8.0;


                      if ($workedHours > 8) $workedHours = $workedHours--;
                      //if ($workedHours > 8) $workedHours = 8.0; //$workedHours--;

                    }else
                    {
                      $i = "fa-times";
                      $l = "VTO";
                      $label = "VTO DENIED";
                      $workedHours = 0.0;
                    }
                    
              # code...
              }break;
      
      
    }

      
      $icons .= "<a title=\"".$lTitle."\" class=\"pull-right text-primary\" target=\"_blank\" style=\"font-size:1em;\" href=\"$link\"><i class=\"fa fa-info-circle\"></i></a><div class='clearfix'></div>";
      $OTicons = "<a id=\"OT_".$payday."\"  data-toggle=\"modal\" data-target=\"#myModal_OT".$payday."\"  title=\"File this OT\" class=\"pull-right\" style=\"font-size:1.2em;\" href=\"#\"><i class=\"fa fa-credit-card\"></i></a>";
      $coll = new Collection;
      $UT = 0;
      $actualHrs = null;
      $billableForOT=null;$OTattribute=null;

      if ($withIssue)
      {
        if ($deet->totalCredits >= '1.0'){

          if($hasPending){
            $workedHours = "<strong class='text-danger'>AWOL</strong><br/>";
            $log="<strong><small><i class=\"fa ".$i."\"></i> <em> ".$l." for approval </em></small></strong>".$icons;
            
          }else{

            if($leaveType=='LWOP') $workedHours = 0;
            //else $workedHours = 8.0;
            $log="<strong><small><i class=\"fa ".$i."\"></i> <em>[  ".$label." ] </em></small></strong>".$icons;
            
          }
          
          
          $workedHours .= "<br/>".$log;

        }
        else if ($deet->totalCredits == '0.50' || $deet->totalCredits == '0.25')
        {

           

             if($hasPending)
            {

              if ($deet->halfdayFrom == 2){
                $log="<strong><small><i class=\"fa ".$i." \"></i> <em> 1st Shift ".$l." (for approval) </em></small></strong>".$icons;
                $workedHours = number_format(($wh/60),2);$billableForOT=0; //."<br/><small>[Late IN]</small>"
                $UT = round(((420.0 - $wh)/60),2); //4h instead of 8H
              }
              
                
              else if ($deet->halfdayFrom == 3){
                $log="<strong><small><i class=\"fa ".$i." \"></i> <em> 2nd Shift ".$l." (for approval) </em></small></strong>".$icons;
                //$workedHours = number_format(($wh/60)+5,2)."<br/><small>[Late IN]</small>";$billableForOT=0;
                $workedHours = number_format(($wh/60),2)."<br/><small>[Late IN]</small>";$billableForOT=0;
                //$UT = round(((240.0 - $wh)/60)-1,2); //4h instead of 8H
                $UT = round(((480.0 - $wh)/60),2); //4h instead of 8H
              }
              else
              {
                $log="<strong><small><i class=\"fa ".$i." \"></i> <em> Half-day ".$l." (for approval) </em></small></strong>".$icons;
                $billableForOT=0;
              }
              
              
                    //no logs, meaning halfday AWOL sya
                    if (count($ins) < 1 && count($outs) < 1) 
                    {
                      $log.="<br/><strong class='text-danger'><small><em>[ Half-day AWOL ]</em></small></strong>";
                      $workedHours = "<strong class='text-danger'>AWOL</strong>";
                      $workedHours .= "<br/>".$log;
                    }
              


              

            }else
            {

              if ($deet->halfdayFrom == 2){

                  $stat = User::find($deet->user_id)->status_id;
                    //****** part time user



                  $log="<strong><small><i class=\"fa ".$i."\"></i> <em> 1st Shift ".$l." </em></small></strong>".$icons;
                  if (!empty($ins) && !empty($outs)  ) 
                  { //&& ($leaveType !== 'OBT' && $leaveType !== 'VL')
                      
                      $workedHours = number_format(($wh/60),2); //."<br/><small>[ *Late IN* ]</small>";

                      if ($stat == 12 || $stat ==14) //kung Part timer or foreign part timer
                      {
                        //$startTime = Carbon::parse($shiftStart->format('Y-m-d H:i:s'),'Asia/Manila')->addHours(5);
                        //check mo muna kung entilted sya for OT
                        if ($workedHours > 8.0)
                        {
                          $startTime = Carbon::parse($shiftStart->format('Y-m-d H:i:s'),'Asia/Manila')->addHours(5);
                          $ending = Carbon::parse($outs['logTxt'],'Asia/Manila');

                          $workedHours = number_format(($wh/60),2); //number_format($ending->diffInMinutes($startTime) / 60,2);
                          $billableForOT = $workedHours - 8.0; $OTattribute = $OTicons;
                          //elseif($workedHours <= 8.0){ $billableForOT = 0; $OTattribute=null; $workedHours=8.0; }

                        }else
                        {
                          $workedHours += 2.0;
                          $UT = round((240.0 - $wh)/60,2); 
                          $billableForOT=0;

                        }
                        
                      }
                      else
                      {//dagdagan mo ng 4hrs
                        if($leaveType !== 'LWOP')
                        {
                          //$workedHours += 4.0;
                          $startTime = Carbon::parse($shiftStart->format('Y-m-d H:i:s'),'Asia/Manila')->addHours(5);
                          $ending = Carbon::parse($outs['logTxt'],'Asia/Manila');

                          $workedHours = number_format($ending->diffInMinutes($startTime) / 60,2);

                          if($workedHours > 8.0) {$billableForOT = $workedHours - 8.0; $OTattribute = $OTicons;}

                          elseif($workedHours <= 8.0){ $billableForOT = 0; $OTattribute=null; $workedHours=8.0; }


                          /*$UT = round((480.0 - $wh)/60,2);  //full 8h work dapat

                          if($workedHours > 8.0) $billableForOT = $workedHours - 8.0;
                          $OTattribute = $OTicons;*/


                        }
                        else //for cases na halfday LWOP pero nagOT at more than 8hrs na
                        {
                          $startTime = Carbon::parse($shiftStart->format('Y-m-d H:i:s'),'Asia/Manila')->addHours(5);
                          $ending = Carbon::parse($outs['logTxt'],'Asia/Manila');

                          $workedHours = number_format($ending->diffInMinutes($startTime) / 60,2);

                          /*if($workedHours >= 5.0)//may 1hr break dapat
                            $workedHours = number_format(($wh-60)/60 ,2);*/


                          if($workedHours > 8.0) {$billableForOT = $workedHours - 8.0; $OTattribute = $OTicons;}
                          elseif($workedHours > 4.0 && $workedHours < 8.0) { $billableForOT = 0; $OTattribute=null; $workedHours=4.0; }
                          elseif($workedHours <= 8.0){ $billableForOT = 0; $OTattribute=null; }

                        }
                        
                        

                        
                      }
                  }
                  else {
                    $workedHours = number_format(($wh/60)+5,2); //"<br/><small>[ Late IN ]</small>";
                    $UT = round(((240.0 - $wh)/60)-1,2); //4h instead of 8H
                    $billableForOT = 0;
                    $OTattribute = $OTicons;
                  }

                  

                  
                
                
              }
              else if ($deet->halfdayFrom == 3){
                $log="<strong><small><i class=\"fa ".$i."\"></i> <em>[  2nd Shift ".$l." ] </em></small></strong>".$icons;
                if (!empty($ins) && !empty($outs)  )//&& ($leaveType !== 'OBT' && $leaveType !== 'VL')
                {
                  //add +1 kasi may minus sa break
                  //$workedHours = number_format(($wh/60)+1,2);
                  if($leaveType == 'VL' || $leaveType == 'SL')
                    $workedHours = 8.0; //number_format(($wh/60)+4,2);
                  else {
                    $wh1 = number_format(($wh/60),2);
                    if($wh1 > 4.0 && $wh1 < 8.0) { $billableForOT = 0; $OTattribute=null; $workedHours=4.0; }
                    else{ $billableForOT = 0; $OTattribute=null; $workedHours=$wh1; }
                  }
                    
                  

                  
                  $UT = 0;
                }
                else
                {
                  $workedHours = number_format(($wh/60)+5,2); //."<br/><small>[ Late IN ]</small>"
                  $UT = round(((240.0 - $wh)/60)-1,2); //4h instead of 8H
                }




                $billableForOT=0;
                
              }
              else{
                 $log="<strong><small><i class=\"fa ".$i."\"></i> <em>[  Half-day ".$l."  ]</em></small></strong>".$icons;
                 $workedHours = number_format(($wh/60)+5,2)."<br/><small>[ Late IN ]</small>";$billableForOT=0;
                 $UT = round(((240.0 - $wh)/60)-1,2); //4h instead of 8H
                 $billableForOT = 0;
               }



              
                   
            } 

            $workedHours .= "<br/>".$log;
                  
                    
                  
        }// end if 0.5 credits

      
      }
      else
      {
        if($leaveType == "VTO")
        {
          if($hasPending)
            {

              $log="<strong><small><i class=\"fa ".$i."\"></i> <em>[ ".$l."  ] (for approval) </em></small></strong>".$icons;
               if ($deet->deductFrom == 'LWOP')
                  $WHcounter = number_format(round(8 - $deet->totalHours),2);
               else
                  $WHcounter = number_format($wh/60,2);


              if ($WHcounter == 9) $WHcounter = $WHcounter - 1.0;
              $workedHours = $WHcounter;
              $actualHrs = $WHcounter;
              $workedHours .= "<br/>".$log;
            }
            else if( !($deet->isApproved) ){
              $log="<strong><small><i class=\"fa ".$i."\"></i> <em>[ ".$l."  ] (denied) </em></small></strong>".$icons;
              $WHcounter = number_format($wh/60,2);

              if ($WHcounter == 9) $WHcounter = $WHcounter - 1.0;
              $workedHours = $WHcounter;
              $actualHrs = $WHcounter;
              $workedHours .= "<br/>".$log;

            }else
            {

              $log="<strong><small><i class=\"fa ".$i."\"></i> <em>[ ".$deet->totalHours." hr VTO  ] </em></small></strong>".$icons;

              //but check first if unpaid VTO
              if ($deet->deductFrom == 'LWOP')
                  $WHcounter = number_format((8 - $deet->totalHours),2);
              else
                  $WHcounter = number_format(round($wh/60 + $deet->totalHours),2);

              //if ($WHcounter == 9) $WHcounter = $WHcounter - 1.0;
              if ($WHcounter > 8) $WHcounter = 8.00;
              $workedHours = $WHcounter;
              $actualHrs = $WHcounter;
              $workedHours .= "<br/>".$log;

            }
            $billableForOT=0;
            $OTattribute=null;


              
        }
        else
        {
          if ($deet->totalCredits >= '1.0')
          {
            $billableForOT=0;
            $OTattribute=null;

            if($hasPending){
              $workedHours = "<strong class='text-danger'>AWOL</strong><br/>";
              $log="<strong><small><i class=\"fa ".$i."\"></i> <em> ".$l." for approval </em></small></strong>".$icons;
            }else{
              //$workedHours .=" ";
              $log="<strong><small><i class=\"fa ".$i."\"></i> <em>[ ".$label." ]</em></small></strong>".$icons;
            }
            
            if($leaveType=='LWOP') $workedHours  .= "0.0<br/>".$log;
            else
            $workedHours .= "<br/>".$log;

          } 
          else if ($deet->totalCredits == '0.50' || $deet->totalCredits == '0.25'){

              if($hasPending){
                if ($deet->halfdayFrom == 2)
                  $log="<strong><small><i class=\"fa ".$i." \"></i> <em> [1st Shift ".$l."] (for approval) </em></small></strong>".$icons;
                else if ($deet->halfdayFrom == 3)
                  $log="<strong><small><i class=\"fa ".$i." \"></i> <em> [2nd Shift ".$l."] (for approval) </em></small></strong>".$icons;
                else
                  $log="<strong><small><i class=\"fa ".$i." \"></i> <em> [ Half-day ".$l."] (for approval) </em></small></strong>".$icons;
                
                
                      //no logs, meaning halfday AWOL sya
                      if (count($ins) < 1 && count($outs) < 1) 
                        $log.="<br/><strong class='text-danger'><small><em>Half-day AWOL</em></small></strong>";

                $workedHours = "<strong class='text-danger'>AWOL</strong>";
                $workedHours .= "<br/>".$log;

              }else{

                if ($deet->halfdayFrom == 2)
                  $log="<strong><small><i class=\"fa ".$i."\"></i> <em> [ 1st Shift ".$l." ]</em></small></strong>".$icons;
                else if ($deet->halfdayFrom == 3)
                  $log="<strong><small><i class=\"fa ".$i."\"></i> <em> [ 2nd Shift ".$l." ]</em></small></strong>".$icons;
                else
                  $log="<strong><small><i class=\"fa ".$i."\"></i> <em>[ Half-day ".$l."  ]</em></small></strong>".$icons;



                 $stat = User::find($deet->user_id)->status_id;
                    //****** part time user

                  if (!empty($ins) && !empty($outs)  ) 
                  { //&& ($leaveType !== 'OBT' && $leaveType !== 'VL')
                      
                      $workedHours = number_format(($wh/60),2); //."<br/><small>[ *Late IN* ]</small>";

                      if ($stat == 12 || $stat ==14)
                      {
                        $workedHours += 2.0;
                        $UT = round((240.0 - $wh)/60,2); 
                        $billableForOT=0;
                      }
                      else
                      {//dagdagan mo ng 4hrs
                        $workedHours += 4.0;
                        $UT = round((480.0 - $wh)/60,2);  //full 8h work dapat


                        if($workedHours > 8.0) {$billableForOT = $workedHours - 8.0; $OTattribute = $OTicons;}
                        elseif($workedHours > 4.0 && $workedHours < 8.0) { $billableForOT = 0; $OTattribute=null; $workedHours=4.0; }
                        elseif($workedHours <= 8.0){ $billableForOT = 0; $OTattribute=null; }

                        //if($workedHours > 8.0) $billableForOT = $workedHours - 8.0;
                      }
                  }
                  else {
                    $workedHours = number_format(($wh/60)+5,2); //."<br/><small>[ Late IN ]</small>";
                    $UT = round(((240.0 - $wh)/60)-1,2); //4h instead of 8H
                    $billableForOT = 0;

                    if($workedHours > 4.0 && $workedHours < 8.0) { $OTattribute=null; $workedHours=4.0; }
                    elseif($workedHours <= 8.0){ $billableForOT = 0; $OTattribute=null; }
                  }




                
                      if (count($ins) < 1 && count($outs) < 1) 
                        $log.="<br/><strong class='text-danger'><small><em>[ Half-day AWOL ]</em></small></strong>";

                //if($leaveType=='LWOP') $workedHours  = 0.0;     
                /*else $workedHours = '4.0';
                $WHcounter = 4.0;*/
                $workedHours .= "<br/>".$log;
                $OTattribute = $OTicons;
              }
                    
                      
                    
          }// end if 0.5 credits
          else
          {
            //just output value; most likely part timer filed this
            if($hasPending)
            {
                if ($deet->halfdayFrom == 2)
                  $log="<strong><small><i class=\"fa ".$i." \"></i> <em> [1st Shift ".$l."] (for approval) </em></small></strong>".$icons;
                else if ($deet->halfdayFrom == 3)
                  $log="<strong><small><i class=\"fa ".$i." \"></i> <em> [2nd Shift ".$l."] (for approval) </em></small></strong>".$icons;
                else
                  $log="<strong><small><i class=\"fa ".$i." \"></i> <em> [ Half-day ".$l."] (for approval) </em></small></strong>".$icons;
                
                
                      //no logs, meaning halfday AWOL sya
                      if (count($ins) < 1 && count($outs) < 1) 
                        $log.="<br/><strong class='text-danger'><small><em>Half-day AWOL</em></small></strong>";

                $workedHours = "<strong class='text-danger'>AWOL</strong>";
                $workedHours .= "<br/>".$log;

            }
            else
            {

                if ($deet->halfdayFrom == 2)
                  $log="<strong><small><i class=\"fa ".$i."\"></i> <em> [ 1st Shift ".$l." ]</em></small></strong>".$icons;
                else if ($deet->halfdayFrom == 3)
                  $log="<strong><small><i class=\"fa ".$i."\"></i> <em> [ 2nd Shift ".$l." ]</em></small></strong>".$icons;
                else
                  $log="<strong><small><i class=\"fa ".$i."\"></i> <em>[ Half-day ".$l."  ]</em></small></strong>".$icons;

                
                      if (count($ins) < 1 && count($outs) < 1) 
                        $log.="<br/><strong class='text-danger'><small><em>[ Half-day AWOL ]</em></small></strong>";

                if($leaveType=='LWOP') $workedHours  = 0.0;     
                else $workedHours = 4 - $deet->totalCredits;
                $WHcounter = 4 - $deet->totalCredits;

                $workedHours .= "<br/>".$log;
            }

          }

        }//end non VTO
        

      }//end withIssue

      $coll->push(['workedHours'=>$workedHours,'UT'=>$UT,'withIssue'=>$withIssue,'actualHrs'=>$actualHrs,'logDeets'=>$log ,'billableForOT'=>$billableForOT,'OTattribute'=>$OTattribute]);
      return $coll;


  }//end processleaves

  public function saveCWS($request)
  {
    $roles = UserType::find($this->user->userType_id)->roles->pluck('label'); 
    $canChangeSched =  ($roles->contains('CHANGE_EMPLOYEE_SCHEDULE')) ? '1':'0'; 

      $cws = new User_CWS;
      $cws->user_id = $request->user_id;
      $cws->biometrics_id = $request->biometrics_id;
      $cws->notes = $request->cwsnote;

      if ($request->timeEnd == "RD")
      {
        $cws->timeStart_old = date('H:i:s', strtotime($request->timeStart_old));
        $cws->timeEnd_old = date('H:i:s', strtotime($request->timeEnd_old));
        $cws->timeStart = "00:00:00";
        $cws->timeEnd = "00:00:00";


      } else {
        $shift = explode('-', $request->timeEnd);
        $cws->timeStart = date('H:i:s', strtotime($shift[0]));
        $cws->timeEnd = date('H:i:s', strtotime($shift[1]));
        $cws->timeStart_old = date('H:i:s', strtotime($request->timeStart_old));
        $cws->timeEnd_old = date('H:i:s', strtotime($request->timeEnd_old));

      }
      
      $cws->isRD = $request->isRD;
       
      


      // if (!empty($request->TLapprover) && $canChangeSched)
      // {
      //   $cws->isApproved = true; $TLsubmitted=true; $cws->approver = $request->TLapprover;
      // } else { $cws->isApproved = null; $TLsubmitted=false; $cws->approver=null; }

       $employee = User::find($cws->user_id);
        //$approvers = $employee->approvers;

       $approvers = $employee->approvers;
       $anApprover = $this->checkIfAnApprover($approvers, $this->user);
       
       $roles = UserType::find($this->user->userType_id)->roles->pluck('label'); 
       $canCWS =  ($roles->contains('CAN_CWS')) ? '1':'0';

        /* -------- get this user's department. If Backoffice, WFM can't access this ------*/
        $isBackoffice = ( Campaign::find(Team::where('user_id',$employee->id)->first()->campaign_id)->isBackoffice ) ? true : false;
        $isWorkforce =  ($roles->contains('STAFFING_MANAGEMENT')) ? '1':'0';

        //Timekeeping Trait
        $anApprover = $this->checkIfAnApprover($approvers, $this->user);

        if ( ($anApprover && $isBackoffice)
          || ($isWorkforce && !$isBackoffice)
          || $this->user->userType_id==1 
          || $this->user->userType_id==2 
          || $this->user->userType_id==5)

         // if ($anApprover)
        {
            $cws->isApproved = true; $TLsubmitted=true; $cws->approver = $request->approver;
        } else { $cws->isApproved = null; $TLsubmitted=false;$cws->approver = $request->approver; }


      
      $cws->save();

      //--- notify the TL concerned
      //$employee = User::find($cws->user_id);

     if (!$anApprover && $isBackoffice) //(!$TLsubmitted && !$canChangeSched)
      {
        //$TL = ImmediateHead::find(ImmediateHead_Campaign::find($cws->approver)->immediateHead_id);
       // $TL = ImmediateHead::find(ImmediateHead_Campaign::find($cws->approver)->immediateHead_id)->userData;

          $notification = new Notification;
          $notification->relatedModelID = $cws->id;
          $notification->type = 6;
          $notification->from = $cws->user_id;
          $notification->save();

          foreach ($employee->approvers as $approver) {
                $TL = ImmediateHead::find($approver->immediateHead_id);
                $nu = new User_Notification;
                $nu->user_id = $TL->userData->id;
                $nu->notification_id = $notification->id;
                $nu->seen = false;
                $nu->save();

                // NOW, EMAIL THE TL CONCERNED
          
                $email_heading = "New CWS Request from: ";

                if ($request->timeEnd == "RD"){
                  $email_body = "Employee: <strong> ". $employee->lastname.", ". $employee->firstname ."  </strong><br/>
                         Schedule: <strong> ".$request->DproductionDate  . " [ Rest Day ] </strong> <br/>";

                } else {
                  $email_body = "Employee: <strong> ". $employee->lastname.", ". $employee->firstname ."  </strong><br/>
                         Schedule: <strong> ".$request->DproductionDate  . " [".$shift[0]." - ". $shift[1]."] </strong> <br/>";
                }
                
                $actionLink = action('UserCWSController@show',$cws->id);

                //  Mail::send('emails.generalNotif', ['user' => $TL, 'employee'=>$employee, 'email_heading'=>$email_heading, 'email_body'=>$email_body, 'actionLink'=>$actionLink], function ($m) use ($TL) 
                //  {
                //     $m->from('OES@openaccessbpo.net', 'OES-OAMPI Evaluation System');
                //     $m->to($TL->userData->email, $TL->lastname.", ".$TL->firstname)->subject('New CWS request');     

                //     /* -------------- log updates made --------------------- */
                //          $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
                //             fwrite($file, "-------------------\n Email sent to ". $TL->userData->email."\n");
                //             fclose($file);                      
                

                // }); //end mail
         
          }
  

      }

         /* -------------- log updates made --------------------- */
          
         $file = fopen('storage/uploads/log.txt', 'a') or die("Unable to open logs");
            fwrite($file, "-------------------\n". $employee->id .",". $employee->lastname." CWS submission ". date('M d h:i:s'). " by ". $this->user->firstname.", ".$this->user->lastname."| An approver: ".$anApprover."\n");
            fclose($file);
      //return redirect()->back();
      //return redirect()->action('DTRController@show', $cws->user_id);
    

  }

  public function saveDTRP($request, $type,$requestor)
  {
    $dtrp = new User_DTRP;
    $dtrp->user_id = $request->user_id;
    $dtrp->biometrics_id = $request->biometrics_id;

     //notify the approver, create first the notif
    $notif = new Notification;

    switch ($type) {
      case 'IN': {  
                    $login = Carbon::parse($request->login)->format('H:i:s'); // new Carbon(Input::get('login'), "Asia/Manila"); 
                    $reason = $request->loginReason;
                    $dtrp->logTime = $login;
                    $dtrp->logType_id = 1;
                    $dtrp->notes = $request->loginReason;
                    $notif->type = 8; //LOG IN
                  } break;

      case 'OUT': {  
                    $logout = new Carbon(Input::get('logout'), "Asia/Manila"); 
                    $reason = $request->logoutReason;
                    $dtrp->logTime = $logout;
                    $dtrp->logType_id = 2;
                    $dtrp->notes = $request->logoutReason;
                    $notif->type = 9; //LOG out
                  } break;
      
     
    }
     
     $dtrp->save();
    
     $notif->relatedModelID = $dtrp->id;
     $notif->from = $request->user_id;
     $notif->save();

     foreach ($requestor->approvers as $key) {
      $TL = ImmediateHead::find($key->immediateHead_id)->userData;
      //$coll->push(['dtrp'=>$dtrp, 'tl'=>$TL]);
      $tlNotif = new User_Notification;
      $tlNotif->user_id = $TL->id;
      $tlNotif->notification_id = $notif->id;
      $tlNotif->seen = false;
      $tlNotif->save();

       # code...
     }

  }

 public function saveLeave($request, $requestor)
  {
    // $dtrp = new User_DTRP;
    // $dtrp->user_id = $request->user_id;
    // $dtrp->biometrics_id = $request->biometrics_id;

     //notify the approver, create first the notif
    //$notif = new Notification;

    $type = $request->leave;
    $leave = new Collection;

    switch ($type) {
      case 'vl': {  
                   $leave->push(['leaveStart'=>$request->leaveStart, 'leaveEnd'=>$request->leaveEnd,'totalCredits'=>$request->totalCredits, 'halfdayFrom'=>$request->halfdayFrom, 'halfdayTo'=>$request->halfdayTo, 'notes'=>$request->details]);
                  } break;

      case 'sl': {  
                    // $logout = new Carbon(Input::get('logout'), "Asia/Manila"); 
                    // $reason = $request->logoutReason;
                    // $dtrp->logTime = $logout;
                    // $dtrp->logType_id = 2;
                    // $dtrp->notes = $request->logoutReason;
                    // $notif->type = 9; //LOG out
                  } break;
      case 'lwop': {}break;
      case 'obt' : {}break;
      
     
    }
     
     // $dtrp->save();
    
     // $notif->relatedModelID = $dtrp->id;
     // $notif->from = $request->user_id;
     // $notif->save();

     // foreach ($requestor->approvers as $key) {
     //  $TL = ImmediateHead::find($key->immediateHead_id)->userData;
     //  //$coll->push(['dtrp'=>$dtrp, 'tl'=>$TL]);
     //  $tlNotif = new User_Notification;
     //  $tlNotif->user_id = $TL->id;
     //  $tlNotif->notification_id = $notif->id;
     //  $tlNotif->seen = false;
     //  $tlNotif->save();

     //   # code...
     // }

    return $leave;

  }
  



}//end trait



?>