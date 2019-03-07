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
use OAMPI_Eval\User_CWS;
use OAMPI_Eval\User_Notification;
use OAMPI_Eval\Notification;
use OAMPI_Eval\MonthlySchedules;
use OAMPI_Eval\FixedSchedules;
use OAMPI_Eval\User_OBT;
use OAMPI_Eval\User_OT;
use OAMPI_Eval\User_VL;
use OAMPI_Eval\User_SL;
use OAMPI_Eval\User_LWOP;
use OAMPI_Eval\Holiday;
use OAMPI_Eval\HolidayType;

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


  // public function checkVLcredits($user)
  // {
  //   $avail = $user->availableVL;
  //   return $avail;

  // }


  public function generateShifts($timeFormat)
  {
    $data = array();

    for( $i = 1; $i <= 24; $i++)
    { 
      $time1 = Carbon::parse('1999-01-01 '.$i.':00:00');
      $time2 = Carbon::parse('1999-01-01 '.$i.':30:00');

      if($timeFormat == '12H')
      {
        array_push($data, $time1->format('h:i A')." - ".$time1->addHours(9)->format('h:i A'));
        array_push($data, $time2->format('h:i A')." - ".$time2->addHours(9)->format('h:i A'));

      } else
      {
        array_push($data, $time1->format('H:i')." - ".$time1->addHours(9)->format('H:i'));
        array_push($data, $time2->format('H:i')." - ".$time2->addHours(9)->format('H:i'));
      }
    }
    return $data;


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

    $theDay = Carbon::parse($payday." ".$schedForToday['timeStart'],"Asia/Manila");
    /*$fix= Carbon::parse($payday." 23:59:00","Asia/Manila");*/
    // SINCE IT'S A COMPLICATED SCHED, MAKE THE STARTING POINT UP TILL END OF SHIFT
    $fix= Carbon::parse($payday." ".$schedForToday['timeStart'],"Asia/Manila")->addHours(9);





    /*------ WE CHECK FIRST IF THERE'S AN APPROVED VL | SL | LWOP -----*/
    

    $vl = User_VL::where('user_id',$user_id)->where('leaveStart','<=',$fix->format('Y-m-d H:i:s'))->where('leaveEnd','>=',$theDay->format('Y-m-d H:i:s'))->orderBy('created_at','DESC')->get();

    $sl = User_SL::where('user_id',$user_id)->where('leaveStart','<=',$fix->format('Y-m-d H:i:s'))->where('leaveEnd','>=',$theDay->format('Y-m-d H:i:s'))->orderBy('created_at','DESC')->get();

    $lwop = User_LWOP::where('user_id',$user_id)->where('leaveStart','<=',$fix->format('Y-m-d H:i:s'))->where('leaveEnd','>=',$theDay->format('Y-m-d H:i:s'))->orderBy('created_at','DESC')->get();

    $obt = User_OBT::where('user_id',$user_id)->where('leaveStart','<=',$fix->format('Y-m-d H:i:s'))->where('leaveEnd','>=',$theDay->format('Y-m-d H:i:s'))->orderBy('created_at','DESC')->get();




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
              
              
                 if ($checkLate > 1) $isLateIN = true; else $isLateIN= false;
              

            } else {$isLateIN=false;$checkLate = $gbID->productionDate."| ". $actualIN->format('Y-m-d H:i:s')." > ". $todayStart->format('Y-m-d H:i:s')." && ". $todayEnd->format('Y-m-d H:i:s');}


            if ($actualOUT > $todayStart && $actualOUT < $todayEnd) // EARLY OUT
            {
              $checkEarlyOut = $actualOUT->diffInMinutes($todayEnd);

               //---- MARKETING TEAM CHECK: 15mins grace period
              
                 if ($checkEarlyOut > 1) $isEarlyOUT = true; else $isEarlyOUT= false;
              

              
            } else $isEarlyOUT=false;

            // if ($userLogIN[0]['timing']->format('H:i:s') > $schedForToday['timeStart'] && $userLogIN[0]['timing']->format('H:i:s') > $schedForToday['timeEnd'] )  $isLateIN = false; else $isLateIN= true;
            // if ($userLogOUT[0]['timing']->format('H:i:s') < $schedForToday['timeEnd'])  $isEarlyOUT = true; else $isEarlyOUT= false;

          

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
                $icons = "<a data-toggle=\"modal\" data-target=\"#myModal_OT".$payday."\" title=\"File this OT\"  class=\"pull-right\" style=\"font-size:1.2em;\" href=\"#\"><i class=\"fa fa-credit-card\"></i></a>";
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

                $actualIN = Carbon::parse($payday." ".$userLogIN[0]['timing']->format('H:i:s'),"Asia/Manila"); 
                $todayStart = Carbon::parse($payday." ".$schedForToday['timeStart'],"Asia/Manila")->addDay();
                $todayEnd = Carbon::parse($payday." ".$schedForToday['timeEnd'],"Asia/Manila")->addDay(); 
                $actualOUT = Carbon::parse($payday." ".$userLogOUT[0]['timing']->format('H:i:s'),"Asia/Manila")->addDay(); 
             } else{
                $actualIN = Carbon::parse($payday." ".$userLogIN[0]['timing']->format('H:i:s'),"Asia/Manila"); 
                $todayStart = Carbon::parse($payday." ".$schedForToday['timeStart'],"Asia/Manila");
                $todayEnd = Carbon::parse($payday." ".$schedForToday['timeEnd'],"Asia/Manila"); 
                $actualOUT = Carbon::parse($payday." ".$userLogOUT[0]['timing']->format('H:i:s'),"Asia/Manila"); 

             }



            //*** --- check if late time in and less than or equal to out
            if ($userLogIN[0]['timing']->format('H:i:s') > $schedForToday['timeStart']) // && $userLogIN[0]['timing']->format('H:i:s') <= $schedForToday['timeEnd'] )
            {

              if ($schedForToday['timeStart'] == "00:00:00")
                $checkLate = $actualIN->diffInHours($todayStart);//diffInHours(Carbon::parse($payday." 24:00:00", "Asia/Manila"));// $actualIN->diffInHours($todayStart);
             else 
              $checkLate = $todayStart->diffInHours($actualIN);

              //---- MARKETING TEAM CHECK: 15mins grace period
              
              
                 if ($checkLate > 1) $isLateIN = true; else $isLateIN= false;
              
             

            }else {$isLateIN= false;}

            ( $actualOUT->format('H:i:s') < $schedForToday['timeEnd'] ) ? $isEarlyOUT = true : $isEarlyOUT= false;

          

            if ($isEarlyOUT && $isLateIN)//use user's logs
            {

              //$wh = Carbon::parse($userLogOUT[0]['timing'],"Asia/Manila")->diffInMinutes(Carbon::parse($userLogOUT[0]['timing'],"Asia/Manila")->addHour());
              $wh = $actualIN->addHour()->diffInMinutes($actualOUT);
              $workedHours = number_format($wh/60,2);
              $billableForOT=0;
              $UT = abs(number_format($wh/60,2) - 8.0);
               // if ($hasHolidayToday)
               //    {
               //      $workedHours .= "<br/> <strong>* ". $holidayToday->first()->name. " * </strong>";
               //    }
              
               //    $chenes = "if ($isEarlyOUT && $isLateIN)";
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
                  $icons = "<a data-toggle=\"modal\" data-target=\"#myModal_OT".$payday."\"  title=\"File this OT\" class=\"pull-right\" style=\"font-size:1.2em;\" href=\"#\"><i class=\"fa fa-credit-card\"></i></a>";
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

          if ($hasVL)
          {

              $link = action('UserVLController@show',$vlDeet->id);
              $icons .= "<a title=\"VL request\" class=\"pull-right text-primary\" target=\"_blank\" style=\"font-size:1em;\" href=\"$link\"><i class=\"fa fa-info-circle\"></i></a><div class='clearfix'></div>";

              
              if ($vlDeet->totalCredits >= '1.0'){

                if($hasPendingVL){
                  $workedHours = "<strong class='text-danger'>AWOL <br/></strong>";
                  $log="<strong><small><i class=\"fa fa-plane\"></i> <em> VL for approval </em></small></strong>".$icons;
                }else{
                  $workedHours = 8.0;
                  $log="<strong><small><i class=\"fa fa-plane\"></i> <em> Vacation Leave </em></small></strong>".$icons;
                }
                
                
                $workedHours .= "<br/>".$log;

              } 
              else if ($vlDeet->totalCredits == '0.50'){

                  if($hasPendingVL){
                    if ($vlDeet->halfdayFrom == 2)
                      $log="<strong><small><i class=\"fa fa-plane\"></i> <em> 1st Shift VL (for approval) </em></small></strong>".$icons;
                    else if ($vlDeet->halfdayFrom == 3)
                      $log="<strong><small><i class=\"fa fa-plane\"></i> <em> 2nd Shift VL (for approval) </em></small></strong>".$icons;
                    else
                      $log="<strong><small><i class=\"fa fa-plane\"></i> <em> Half-day VL (for approval) </em></small></strong>".$icons;
                    
                    
                          //no logs, meaning halfday AWOL sya
                          if (count($userLogIN[0]['logs']) < 1 && count($userLogOUT[0]['logs']) < 1) 
                            $log.="<br/><strong class='text-danger'><small><em>Half-day AWOL</em></small></strong>";

                    $workedHours = "<strong class='text-danger'>AWOL</strong>";
                    $workedHours .= "<br/>".$log;

                  }else{

                    if ($vlDeet->halfdayFrom == 2)
                      $log="<strong><small><i class=\"fa fa-plane\"></i> <em> 1st Shift VL </em></small></strong>".$icons;
                    else if ($vlDeet->halfdayFrom == 3)
                      $log="<strong><small><i class=\"fa fa-plane\"></i> <em> 2nd Shift VL </em></small></strong>".$icons;
                    else
                      $log="<strong><small><i class=\"fa fa-plane\"></i> <em> Half-day VL  </em></small></strong>".$icons;

                    
                          if (count($userLogIN[0]['logs']) < 1 && count($userLogOUT[0]['logs']) < 1) 
                            $log.="<br/><strong class='text-danger'><small><em>Half-day AWOL</em></small></strong>";
                    $workedHours .= 4.0;
                    $WHcounter = 4.0;
                    $workedHours .= "<br/>".$log;
                  }
                        
                          
                        
            }// end if 0.5 credits

          }//end if has VL


          if ($hasOBT)
          {

              $link = action('UserOBTController@show',$obtDeet->id);
              $icons .= "<a title=\"OBT request\" class=\"pull-right text-primary\" target=\"_blank\" style=\"font-size:1em;\" href=\"$link\"><i class=\"fa fa-info-circle\"></i></a><div class='clearfix'></div>";

              
              if ($obtDeet->totalCredits >= '1.0'){

                if($hasPendingOBT){
                  $workedHours = "<strong class='text-danger'>AWOL</strong>";
                  $log="<strong><small><i class=\"fa fa-suitcase\"></i> <em> OBT for approval </em></small></strong>".$icons;
                }else{
                  $workedHours = 8.0;
                  $log="<strong><small><i class=\"fa fa-suitcase\"></i> <em> Offical Business Trip </em></small></strong>".$icons;
                }
                
                
                $workedHours .= "<br/>".$log;

              } 
              else if ($obtDeet->totalCredits == '0.50'){

                  if($hasPendingOBT){
                    if ($obtDeet->halfdayFrom == 2)
                      $log="<strong><small><i class=\"fa fa-suitcase\"></i> <em> 1st Shift OBT (for approval) </em></small></strong>".$icons;
                    else if ($obtDeet->halfdayFrom == 3)
                      $log="<strong><small><i class=\"fa fa-suitcase\"></i> <em> 2nd Shift OBT (for approval) </em></small></strong>".$icons;
                    else
                      $log="<strong><small><i class=\"fa fa-suitcase\"></i> <em> Half-day OBT (for approval) </em></small></strong>".$icons;
                    
                    
                          //no logs, meaning halfday AWOL sya
                          if (count($userLogIN[0]['logs']) < 1 && count($userLogOUT[0]['logs']) < 1) 
                            $log.="<br/><strong class='text-danger'><small><em>Half-day AWOL</em></small></strong>";

                    $workedHours = "<strong class='text-danger'>AWOL</strong>";
                    $workedHours .= "<br/>".$log;

                  }else{

                    if ($obtDeet->halfdayFrom == 2)
                      $log="<strong><small><i class=\"fa fa-suitcase\"></i> <em> 1st Shift OBT </em></small></strong>".$icons;
                    else if ($obtDeet->halfdayFrom == 3)
                      $log="<strong><small><i class=\"fa fa-suitcase\"></i> <em> 2nd Shift OBT </em></small></strong>".$icons;
                    else
                      $log="<strong><small><i class=\"fa fa-suitcase\"></i> <em> Half-day OBT  </em></small></strong>".$icons;

                    
                          if (count($userLogIN[0]['logs']) < 1 && count($userLogOUT[0]['logs']) < 1) 
                            $log.="<br/><strong class='text-danger'><small><em>Half-day AWOL</em></small></strong>";
                    $workedHours .= 4.0;
                    $WHcounter = 4.0;
                    $workedHours .= "<br/>".$log;
                  }
                        
                          
                        
            }// end if 0.5 credits

          }//end if has OBT



          if ($hasSL)
          {

              $link = action('UserSLController@show',$slDeet->id);
              $icons .= "<a title=\"SL request\" class=\"pull-right text-primary\" target=\"_blank\" style=\"font-size:1em;\" href=\"$link\"><i class=\"fa fa-info-circle\"></i></a><div class='clearfix'></div>";

              
              if ($slDeet->totalCredits >= '1.0'){

                if($hasPendingSL){
                  $workedHours = "<strong class='text-danger'>AWOL</strong>";
                  $log="<strong><small><i class=\"fa fa-stethoscope\"></i> <em> SL for approval </em></small></strong>".$icons;
                }else{
                  $workedHours = "N/A";
                  $log="<strong><small><i class=\"fa fa-stethoscope\"></i> <em> Sick Leave </em></small></strong>".$icons;
                }
                
                
                $workedHours .= "<br/>".$log;

              } 
              else if ($slDeet->totalCredits == '0.50'){

                  if($hasPendingSL){
                    if ($slDeet->halfdayFrom == 2)
                      $log="<strong><small><i class=\"fa fa-stethoscope\"></i> <em> 1st Shift SL (for approval) </em></small></strong>".$icons;
                    else if ($slDeet->halfdayFrom == 3)
                      $log="<strong><small><i class=\"fa fa-stethoscope\"></i> <em> 2nd Shift SL (for approval) </em></small></strong>".$icons;
                    else
                      $log="<strong><small><i class=\"fa fa-stethoscope\"></i> <em> Half-day SL (for approval) </em></small></strong>".$icons;
                    
                    
                          //no logs, meaning halfday AWOL sya
                          if (count($userLogIN[0]['logs']) < 1 && count($userLogOUT[0]['logs']) < 1) 
                            $log.="<br/><strong class='text-danger'><small><em>Half-day AWOL</em></small></strong>";

                    $workedHours = "<strong class='text-danger'>AWOL</strong>";
                    $workedHours .= "<br/>".$log;

                  }else{

                    if ($slDeet->halfdayFrom == 2)
                      $log="<strong><small><i class=\"fa fa-stethoscope\"></i> <em> 1st Shift SL </em></small></strong>".$icons;
                    else if ($slDeet->halfdayFrom == 3)
                      $log="<strong><small><i class=\"fa fa-stethoscope\"></i> <em> 2nd Shift SL </em></small></strong>".$icons;
                    else
                      $log="<strong><small><i class=\"fa fa-stethoscope\"></i> <em> Half-day SL  </em></small></strong>".$icons;

                    
                          if (count($userLogIN[0]['logs']) < 1 && count($userLogOUT[0]['logs']) < 1) 
                            $log.="<br/><strong class='text-danger'><small><em>Half-day AWOL</em></small></strong>";
                    $workedHours .= 4.0;
                    $WHcounter = 4.0;
                    $workedHours .= "<br/>".$log;
                  }
                        
                          
                        
            }// end if 0.5 credits

          }//end if has SL





          if ($hasLWOP)
          {

             $link = action('UserLWOPController@show',$lwopDeet->id);
              $icons = "<a title=\"LWOP request\" class=\"pull-right text-primary\" target=\"_blank\" style=\"font-size:1em;\" href=\"$link\"><i class=\"fa fa-info-circle\"></i></a><div class='clearfix'></div>";

              
              if ($lwopDeet->totalCredits >= '1.0'){

                if($hasPendingLWOP)
                  $log.="<strong>*<small><em>LWOP for approval </em></small></strong>".$icons;
                else
                  $log.="<strong>*<small><em>Leave Without Pay </em></small></strong>".$icons;

                $workedHours = 0.0;
                $WHcounter = 0.0;
                $workedHours .= "<br/>".$log;

              } 
              else if ($lwopDeet->totalCredits == '0.50')
              {

                  if($hasPendingLWOP){
                    if ($lwopDeet->halfdayFrom == 2)
                      $log.="<strong><small><i class=\"fa fa-meh-o\"></i> <em> 1st Shift LWOP (for approval) </em></small></strong>".$icons;
                    else if ($lwopDeet->halfdayFrom == 3)
                      $log.="<strong><small><i class=\"fa fa-meh-o\"></i> <em> 2nd Shift LWOP (for approval) </em></small></strong>".$icons;
                    else
                      $log.="<strong><small><i class=\"fa fa-meh-o\"></i> <em> Half-day LWOP (for approval) </em></small></strong>".$icons;
                    
                    
                          //no logs, meaning halfday AWOL sya
                          if (count($userLogIN[0]['logs']) < 1 && count($userLogOUT[0]['logs']) < 1) 
                            $log.="<br/><strong class='text-danger'><small><em>Half-day AWOL</em></small></strong>";

                    $workedHours = "<strong class='text-danger'>AWOL</strong>";
                    $workedHours .= "<br/>".$log;

                  }else{

                    if ($lwopDeet->halfdayFrom == 2)
                      $log.="<br/><strong><small><i class=\"fa fa-meh-o\"></i> <em> 1st Shift LWOP </em></small></strong>".$icons;
                    else if ($lwopDeet->halfdayFrom == 3)
                      $log.="<strong><small><i class=\"fa fa-meh-o\"></i> <em> 2nd Shift LWOP </em></small></strong>".$icons;
                    else
                      $log.="<br/><strong><small><i class=\"fa fa-meh-o\"></i> <em> Half-day LWOP  </em></small></strong>".$icons;

                    
                          if (count($userLogIN[0]['logs']) < 1 && count($userLogOUT[0]['logs']) < 1) 
                            $log.="<br/><strong class='text-danger'><small><em>Half-day AWOL</em></small></strong>";
                    //$workedHours = 0.0;
                    ($hasVL) ? $WHcounter -= 4.0 : $WHcounter=0;
                    $workedHours .= $WHcounter. "<br/>".$log;
                  }



                      
              }//end 0.5

          }//end if has LWOP

          


          if ($hasHolidayToday) /***--- we will need to check if Non-Ops personnel, may pasok kasi pag OPS **/
          {
            $workedHours .= "(8.0)<br/> <strong>* " . $holidayToday->first()->name . " *</strong>";
          }

         if (!$hasVL && !$hasSL && !$hasLWOP && !$hasHolidayToday && !$hasOBT)
            $workedHours = "<a title=\"Check your Biometrics data. \n It's possible that you pressed a wrong button, the machine malfunctioned, or you really didn't log in / out.\"><strong class=\"text-danger\">AWOL </strong></a>";
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

    if (count($allFixedWS) > 0) $schedForToday = $allFixedWS->first();
    /*$ct = 0;
    foreach ($allFixedWS as $key) 
    {
        if( $allFixedWS[$ct]->schedEffectivity <= $payday || $allFixedWS[$ct]->schedEffectivity==null )
        {
          
          $schedForToday1 = $allFixedWS[$ct];
          break;

        } else {$ct++; $schedForToday1 = $allFixedWS->first(); } 
    }

    $schedForToday = $schedForToday1;*/

    return $schedForToday;


  }


  public function getShiftingSchedules2($sched, $coll,$counter)
  {

    //check first if may approved CWS
    $bio = Biometrics::where('productionDate',$sched->productionDate)->get();
    $prodDate = Carbon::parse($sched->productionDate,"Asia/Manila");

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
                 
                $correctTime = Carbon::parse($sched->productionDate . " 00:00:00","Asia/Manila");

                 $coll->push(['title'=>'Rest day ',
                                'start'=>$sched->productionDate . " 00:00:00", // dates->format('Y-m-d H:i:s'),
                                //'end'=>$sched->productionDate . " 00:00:00",
                                'end'=>$correctTime->addHour(9)->format('Y-m-d H:i:s'),
                                'textColor'=> '#ccc',
                                'backgroundColor'=> '#fff',
                                'chenes'=>$sched->productionDate,'icon'=>" ", 'biometrics_id'=>$bio->first()->id
                                 ]);
                 $coll->push(['title'=>'..',
                                'start'=>$sched->productionDate . " 00:00:00", // dates->format('Y-m-d H:i:s'),
                                'end'=>$correctTime->format('Y-m-d H:i:s'),
                                //'end'=>$sched->productionDate . " 00:00:00",
                                'textColor'=> '#fff',
                                'borderColor'=>$border,
                                'backgroundColor'=> '#e6e6e6',
                                'chenes'=>$sched->productionDate,'icon2'=>"bed", 'biometrics_id'=>$bio->first()->id
                                 ]);



              }
              else {

                $correctTime = Carbon::parse($sched->productionDate . " ".$cws->first()->timeStart,"Asia/Manila");

                $coll->push(['title'=> date('h:i A', strtotime($cws->first()->timeStart)) . " to ",// '09:00 AM ',
                          'start'=>$sched->productionDate . " ".$cws->first()->timeStart, //. $sched->timeStart, //->format('Y-m-d H:i:s'),
                          //'end'=>$sched->productionDate . " ".$cws->first()->timeEnd,
                          'end'=>$correctTime->addHour(9)->format('Y-m-d H:i:s'),
                          'textColor'=> $startColor,// '#548807',// '#409c45',
                          'backgroundColor'=> $bgcolor,
                        'chenes'=>$sched->productionDate,
                        'counter'=>$counter,
                        'icon'=>"play-circle",
                        'biometrics_id'=> $bio->first()->id]);
                 $coll->push(['title'=>date('h:i A', strtotime($cws->first()->timeEnd)),
                                      'start'=>$sched->productionDate . " ".$cws->first()->timeStart, //. $sched->timeEnd,
                                      'textColor'=>  $endColor, //'#bd3310',// '#27a7f7',
                                      'backgroundColor'=> $bgcolor,
                                    'chenes'=>$sched->productionDate,
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
                                'start'=>$sched->productionDate . " 00:00:00", // dates->format('Y-m-d H:i:s'),
                                'end'=>$sched->productionDate . " 00:00:00", 
                                'textColor'=> '#ccc',
                                'backgroundColor'=> '#fff',
                                'chenes'=>$sched->productionDate,'icon'=>" ", 'biometrics_id'=>null
                                 ]);
            $coll->push(['title'=>'..',
                                'start'=>$sched->productionDate . " 00:00:00", // dates->format('Y-m-d H:i:s'),
                                'textColor'=> '#fff',
                                'borderColor'=>$border,
                                'backgroundColor'=> '#e6e6e6',
                                'chenes'=>$sched->productionDate,'icon2'=>"bed", 'biometrics_id'=>null
                                 ]);



          } else{

            $correctTime = Carbon::parse($sched->productionDate . " ".$sched->timeStart,"Asia/Manila");

           $coll->push(['title'=> date('h:i A', strtotime($sched->timeStart)) . " to ",// '09:00 AM ',
                                'start'=>$sched->productionDate . " ".$sched->timeStart,//. $sched->timeStart, //->format('Y-m-d H:i:s'),
                                //'end'=>$sched->productionDate . " ".$sched->timeEnd,
                                'end'=>$correctTime->addHour(9)->format('Y-m-d H:i:s'),
                                'borderColor'=>$bgcolor,
                                'textColor'=> '#548807',// '#409c45',
                                'backgroundColor'=> $bgcolor,
                              'chenes'=>$sched->productionDate,
                              'counter'=>$counter,'icon'=>"play-circle", 'biometrics_id'=>null]);
           $coll->push(['title'=>date('h:i A', strtotime($sched->timeEnd)),
                                  'start'=>$sched->productionDate . " ".$sched->timeStart,//. $sched->timeEnd,
                                  //'end'=>$sched->productionDate . " ".$sched->timeEnd,
                                  'end'=>$correctTime->format('Y-m-d H:i:s'),
                                  'textColor'=> '#bd3310',// '#27a7f7',
                                  'backgroundColor'=> $bgcolor,
                                  'borderColor'=>$bgcolor,
                                'chenes'=>$sched->productionDate,
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
            /*$coll->push(['title'=> date('h:i A', strtotime($keys->timeStart)) . " to ",// '09:00 AM ',
                    'start'=>$keys->productionDate . " ". $keys->timeStart, //->format('Y-m-d H:i:s'),
                    'textColor'=> '#548807',// '#409c45',
                    'backgroundColor'=> '#fff',
                  'chenes'=>$keys->productionDate]);
            $coll->push(['title'=>date('h:i A', strtotime($keys->timeEnd)),
                      'start'=>$keys->productionDate . " ". $keys->timeEnd,
                      'textColor'=> '#bd3310',// '#27a7f7',
                      'backgroundColor'=> '#fff',
                    'chenes'=>$keys->productionDate]);*/


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
 

  public function getLogDetails($type, $id, $biometrics_id, $logType_id, $schedForToday, $undertime, $problemArea, $isAproblemShift)
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
    $pendingDTRP = null; 
    $UT=null;$log=null;$timing=null; $pal = null;$maxIn=null;$beginShift=null; $finishShift=null;
    $logPalugit=null;
    $palugitDate=null;$maxOut=null;

    
    $theDay = Carbon::parse($thisPayrollDate." ".$schedForToday['timeStart'],"Asia/Manila");
    $fix= Carbon::parse($thisPayrollDate." 23:59:00","Asia/Manila");



  

     /*------ WE CHECK FIRST IF THERE'S AN APPROVED VL | SL | LWOP -----*/
    $vl = User_VL::where('user_id',$id)->where('leaveStart','<=',$fix->format('Y-m-d H:i:s'))->where('leaveEnd','>=',$theDay->format('Y-m-d H:i:s'))->orderBy('created_at','DESC')->get();

    $sl = User_SL::where('user_id',$id)->where('leaveStart','<=',$fix->format('Y-m-d H:i:s'))->where('leaveEnd','>=',$theDay->format('Y-m-d H:i:s'))->orderBy('created_at','DESC')->get();

    $lwop = User_LWOP::where('user_id',$id)->where('leaveStart','<=',$fix->format('Y-m-d H:i:s'))->where('leaveEnd','>=',$theDay->format('Y-m-d H:i:s'))->orderBy('created_at','DESC')->get();

    $obt = User_OBT::where('user_id',$id)->where('leaveStart','<=',$fix->format('Y-m-d H:i:s'))->where('leaveEnd','>=',$theDay->format('Y-m-d H:i:s'))->orderBy('created_at','DESC')->get();



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








    


    if (count($holidayToday) > 0) $hasHolidayToday = true;

     $hasApprovedDTRP = User_DTRP::where('user_id',$id)->where('isApproved',true)->where('biometrics_id',$biometrics_id)->where('logType_id',$logType_id)->orderBy('updated_at','DESC')->get();

      $pendingDTRP = User_DTRP::where('user_id',$id)->where('biometrics_id',$biometrics_id)->where('logType_id',$logType_id)->where('isApproved',null)->orderBy('id','DESC')->get();



      ( count($pendingDTRP) > 0  ) ? $hasPendingDTRP=true : $hasPendingDTRP=false;
                    
      

      if(count($hasApprovedDTRP) > 0){ 

          $userLog = $hasApprovedDTRP;

      } else {

              //fix for robert's case sa logout
              if ($logType_id== 2){

                $beginShift = Carbon::parse($thisPayrollDate." ".$schedForToday['timeStart'],"Asia/Manila");
                $endShift = Carbon::parse($thisPayrollDate." ".$schedForToday['timeEnd'],"Asia/Manila");
                
                //$maxEnd = Carbon::parse($thisPayrollDate." ".$schedForToday['timeEnd'],"Asia/Manila")->addHour(6);
                $probTime1 = Carbon::parse($thisPayrollDate." 04:00:00","Asia/Manila");
                $probTime2 = Carbon::parse($thisPayrollDate." 14:30:00","Asia/Manila");

                if (!($beginShift >= $probTime1 && $beginShift <= $probTime2) || is_null($schedForToday)) // if shift is NOT within the day
                {
                  $userLog = null;
                  //$userLog = Logs::where('user_id',$id)->where('biometrics_id',$biometrics_id)->where('logType_id',$logType_id)->orderBy('biometrics_id','ASC')->get();

                  goto proceedToLogTomorrow;

                }
                 else $userLog = Logs::where('user_id',$id)->where('biometrics_id',$biometrics_id)->where('logType_id',$logType_id)->orderBy('biometrics_id','ASC')->get();

              } else

              $userLog = Logs::where('user_id',$id)->where('biometrics_id',$biometrics_id)->where('logType_id',$logType_id)->orderBy('biometrics_id','ASC')->get();
      }
             
          

      //} 


      /*--- after getting the logs, IF (logIN_type) go to another filter pass
            else, just proceed -- */
            
     

      if (is_null($userLog) || count($userLog)<1)
      {  

        /* ------------ THIS IS WHERE WE CHECK FOR THOSE PROBLEM AREAS ---------
                                  perform this only for LOG INS                         */

          if ($logType_id == 1){

                $beginShift = Carbon::parse($thisPayrollDate." ".$schedForToday['timeStart'],"Asia/Manila")->format('Y-m-d H:i:s');
                
                $maxIn = Carbon::parse($thisPayrollDate." ".$schedForToday['timeStart'],"Asia/Manila")->subHour(6)->format('Y-m-d H:i:s');
                $probTime1 = Carbon::parse($thisPayrollDate." 00:00:00","Asia/Manila")->format('Y-m-d H:i:s');
                $probTime2 = Carbon::parse($thisPayrollDate." 05:00:00","Asia/Manila")->format('Y-m-d H:i:s');


                if ($beginShift >= $probTime1 && $beginShift <= $probTime2)
                {
                  /*-- check for logs within 6hr grace period for problem shifts --*/
                  
                  $tommorow = Carbon::parse($thisPayrollDate)->addDay();
                  $bioForTom = Biometrics::where('productionDate',$tommorow->format('Y-m-d'))->get();

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

                        } else if ($palugitDate >= $beginShift &&  $palugitDate <= $finishShift) //meaning late lang sya
                        {

                            $userLog = $logPalugit;
                            goto proceedWithLogs;

                        } else goto proceedWithBlank;
                        
                      } else goto proceedWithBlank; 

                  } else goto proceedWithBlank;
                  

                }  
          /* ------------ END THIS IS WHERE WE CHECK FOR THOSE PROBLEM AREAS : LOG IN ---------*/       
          } else if($logType_id == 2){

                


                if (!($beginShift >= $probTime1 && $beginShift <= $probTime2)) // if shift is NOT within the day
                {
                  /*-- check for logs within 6hr grace period for problem shifts --*/

                  proceedToLogTomorrow:
                  
                  $tommorow = Carbon::parse($thisPayrollDate)->addDay();
                  $bioForTom = Biometrics::where('productionDate',$tommorow->format('Y-m-d'))->get();

                  if (count($bioForTom) > 0){
                    $finishShift = Carbon::parse($thisPayrollDate." ".$schedForToday['timeStart'],"Asia/Manila")->addHour(9);// 


                      if ($isAproblemShift)
                        $logPalugit = Logs::where('user_id',$id)->where('biometrics_id',$biometrics_id)->where('logType_id',$logType_id)->orderBy('biometrics_id','ASC')->get();
                      
                      else
                        //$logPalugit = Logs::where('user_id',$id)->where('biometrics_id',$biometrics_id)->where('logType_id',$logType_id)->orderBy('biometrics_id','ASC')->get();
                        $logPalugit = Logs::where('user_id',$id)->where('biometrics_id',$bioForTom->first()->id)->where('logType_id',$logType_id)->orderBy('biometrics_id','ASC')->get();


                      if (count($logPalugit) > 0) 
                      { 
                        //we need to check first if it is within the palugit period: meaning LATE
                        //if more than palugit: meaning for tomorrow's bio yun
                        $palugitDate = Carbon::parse($thisPayrollDate." ".$logPalugit->first()->logTime,"Asia/Manila");
                        //$thisPayrollDate
                        //$bioForTom->first()->productionDate
                        $maxOut = Carbon::parse($thisPayrollDate." ".$schedForToday['timeStart'],"Asia/Manila")->addHour(16);
                        $pal = $palugitDate;

                        if ($isAproblemShift){
                          if ( (  $palugitDate >= $beginShift && $palugitDate <= $maxOut  ) || is_null($schedForToday) )
                          {
                            $userLog = $logPalugit;
                            goto proceedWithLogs;

                          } else goto proceedWithBlank;

                        }else{

                          $userLog = $logPalugit;
                          goto proceedWithLogs;
                        }
                       

                        
                        
                      } else goto proceedWithBlank; 

                  } else goto proceedWithBlank;
                  

                }  
          /* ------------ END THIS IS WHERE WE CHECK FOR THOSE PROBLEM AREAS ---------*/       
          } else // ---proceed with the usual null logs
          {

                proceedWithBlank:

                               $link = action('LogsController@viewRawBiometricsData',$id);
                               
                               $icons = "<a title=\"Verify Biometrics data\" class=\"pull-right text-gray\" target=\"_blank\" style=\"font-size:1.2em;\" href=\"$link#$biometrics_id\"><i class=\"fa fa-clock-o\"></i></a>";
                                
                                
                               if ($hasHolidayToday)
                               {
                                $log = "<strong class=\"text-danger\">N/A</strong>". $icons;
                                $workedHours = $holidayToday->first()->name;

                               } else if ($hasLWOP){

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

                         $log = date('h:i:s A',strtotime($userLog->first()->logTime));

                         $timing =  Carbon::parse($userLog->first()->productionDate." ".$userLog->first()->logTime, "Asia/Manila");
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
                          
                          
                          //if ($problemArea[0]['problemShift'])
                          //  $timing = Carbon::parse(Biometrics::find($userLog->first()->biometrics_id)->productionDate." ".$userLog->first()->logTime, "Asia/Manila");
                          //else
                          //  $timing = Carbon::parse($userLog->first()->logTime, "Asia/Manila");

                          //$timing2 = $userLog->first()->logTime;

                          //*********** APPLICABLE ONLY TO WORK DAY ********************//

                          if ($logType_id == 1) 
                          {
                            $parseThis = $schedForToday['timeStart'];
                            if ( (Carbon::parse($parseThis,"Asia/Manila") < $timing)  && !$problemArea[0]['problemShift']) //--- meaning late sya
                              {
                                $UT  = $undertime + number_format((Carbon::parse($parseThis,"Asia/Manila")->diffInMinutes($timing))/60,2);

                              } else $UT = 0;
                          }
                            
                          else if ($logType_id == 2)
                            $parseThis = $schedForToday['timeEnd'];
                            if (Carbon::parse($parseThis,"Asia/Manila") > $timing && !$problemArea[0]['problemShift']) //--- meaning early out sya
                              {
                                $UT  = $undertime + number_format((Carbon::parse($parseThis,"Asia/Manila")->diffInMinutes($timing))/60,2);

                              } else $UT=$undertime;

                          
                          //*********** end APPLICABLE ONLY TO WORK DAY ********************//

      }//end if may login 



      /*-------- VACATION LEAVE -----------*/
      if ($hasVL && $hasPendingVL)
      {
        $leaveDetails->push(['type'=>"VL for approval",'icon'=>'fa-info-circle', 'details'=>$vlDeet]);
        
      } else if($hasLeave)
      {
        $leaveDetails->push(['type'=>"Vacation Leave",'icon'=>'fa-plane', 'details'=>$vlDeet]);
      }


      /*-------- SICK LEAVE -----------*/
      if ($hasSL && $hasPendingSL)
      {
        $leaveDetails->push(['type'=>"SL for approval",'icon'=>'fa-info-circle', 'details'=>$slDeet]);
        
      } else if($hasLeave)
      {
        $leaveDetails->push(['type'=>"Sick Leave",'icon'=>'fa-stethoscope', 'details'=>$slDeet]);
      }

      /*-------- LEAVE WITHOUT PAY -----------*/
      if ($hasLWOP && $hasPendingLWOP)
      {
        $lwopDetails->push(['type'=>"LWOP for approval",'icon'=>'fa-info-circle', 'details'=>$lwopDeet]);
        
      } else if($hasLWOP)
      {
        $lwopDetails->push(['type'=>"Leave Without Pay",'icon'=>'fa-meh-o', 'details'=>$lwopDeet]);
      }



       $data->push([ 'logPalugit'=>$logPalugit,
                      'palugitDate' =>$palugitDate,
                       'beginShift'=> $beginShift,
                       'maxOut'=> $maxOut,
                    'leave'=>$leaveDetails, 'hasLeave'=>$hasLeave, 'logs'=>$userLog,'lwop'=>$lwopDetails, 'hasLWOP'=>$hasLWOP, 'hasSL'=>$hasSL,
                    'sl'=>$slDeet,
                    'UT'=>$UT, 'logTxt'=>$log,
                    'hasPendingDTRP' => $hasPendingDTRP,
                    'pendingDTRP' => $pendingDTRP, 
                    'dtrpIN'=>$dtrpIN, 'dtrpIN_id'=>$dtrpIN_id, 
                    'dtrpOUT'=>$dtrpOUT, 'dtrpOUT_id'=> $dtrpOUT_id,
                    'timing'=>$timing, 'pal'=>$pal,'maxIn'=>$maxIn,'beginShift'=>$beginShift,'finishShift'=>$finishShift,
                    'dtrp'=>$hasApprovedDTRP->first()]);

              

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


  public function getRDinfo($user_id, $biometrics,$isSameDayLog,$payday)
  {

    /* init $approvedOT */
      $approvedOT=0; $OTattribute="";
      $hasHolidayToday = false;
      $hasPendingIN = null;
      $pendingDTRPin = null;
      $hasPendingOUT = null;
      $pendingDTRPout = null;

    $thisPayrollDate = Biometrics::find($biometrics->id)->productionDate;
    $holidayToday = Holiday::where('holidate', $thisPayrollDate)->get();

     if (count($holidayToday) > 0) $hasHolidayToday = true;


      /* -- you still have to create module for checking and filing OTs */

      /* --------- check mo muna kung may approved DTRP on this day --------*/
      $hasApprovedDTRPin = User_DTRP::where('user_id',$user_id)->where('isApproved',true)->where('biometrics_id',$biometrics->id)->where('logType_id',1)->orderBy('updated_at','DESC')->get();

      if (count($hasApprovedDTRPin) > 0){ $userLogIN = $hasApprovedDTRPin;} 
      else { $userLogIN = Logs::where('user_id',$user_id)->where('biometrics_id',$biometrics->id)->where('logType_id',1)->orderBy('biometrics_id','ASC')->get(); }



      if (count($userLogIN) == 0)
      {
            $pendingDTRPin = User_DTRP::where('user_id',$user_id)->where('isApproved',null)->where('biometrics_id',$biometrics->id)->where('logType_id',1)->orderBy('updated_at','DESC')->get();
            //--- ** baka naman may DTRP syang di pa approved? 

            if (count($pendingDTRPin) > 0)
            {
              $logIN = "(pending)";
              $hasPendingIN = true;
            } else {

              $logIN = "* RD *";  //--- di nga sya pumasok
              $logOUT = "* RD *";

            }
            $shiftStart = "* RD *";
            $shiftEnd = "* RD *";

            if ($hasHolidayToday)
              {
                $workedHours = "(8.0) <br/><strong> * "; 
                $workedHours .= $holidayToday->first()->name." *</strong>";

              } else $workedHours="N/A"; 

              $UT = 0;
              $billableForOT=0;

      } 
      else
      {
          $logIN = date('h:i A',strtotime($userLogIN->first()->logTime));
          $timeStart = Carbon::parse($userLogIN->first()->logTime);

          if ($isSameDayLog) 
          {
            //Check mo muna kung may approved DTRPout
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

                } else {
                  $userLogOUT = Logs::where('user_id',$user_id)->where('biometrics_id',$biometrics->id)->where('logType_id',2)->orderBy('biometrics_id','ASC')->get();
                }

             }

             
          } //end if sameday log

          else
          {
            //--- NEXT DAY LOG OUT
            $nextDay = Carbon::parse($payday)->addDay();
            $bioForTomorrow = Biometrics::where('productionDate',$nextDay->format('Y-m-d'))->first();

            //Check mo muna kung may approved DTRPout
             $hasApprovedDTRPout = User_DTRP::where('user_id',$user_id)->where('isApproved',true)->where('biometrics_id',$bioForTomorrow->id)->where('logType_id',2)->orderBy('updated_at','DESC')->get();

             if (count($hasApprovedDTRPout) > 0)
             {

              $userLogOUT = $hasApprovedDTRPout;

             } else 
             {

               //--- ** baka naman may DTRP syang di pa approved? 
                $pendingDTRPout = User_DTRP::where('user_id',$user_id)->where('isApproved',null)->where('biometrics_id',$bioForTomorrow->id)->where('logType_id',2)->orderBy('updated_at','DESC')->get();

                if (count($pendingDTRPout) > 0)
                {
                  $logOUT = "(pending)";
                  $hasPendingOUT = true;
                  $userLogOUT = $pendingDTRPout;

                } else {

                  //--- di nga sya pumasok
                  $logOUT = "* RD *";
                  $userLogOUT = Logs::where('user_id',$user_id)->where('biometrics_id',$bioForTomorrow->id)->where('logType_id',2)->orderBy('biometrics_id','ASC')->get();

                }

              
             }

          }
            


          //--- ** May issue: pano kung RD OT ng gabi, then kinabukasan na sya nag LogOUT. Need to check kung may approved OT from IH
          $rdOT = User_OT::where('biometrics_id',$biometrics->id)->where('user_id',$user_id)->where('isApproved',1)->get();
          if (count($rdOT) > 0) $approvedOT = $rdOT; 

          if( is_null($userLogOUT) || count($userLogOUT)<1 )
          {
             $logOUT = "No OT-Out <br/><small>Verify with Immediate Head</small>";

              $workedHours="N/A"; 

              if ($hasHolidayToday)
              {
                
                $workedHours .= "<br /><strong>* " . $holidayToday->first()->name." * </strong>";

              }  

             
              $shiftStart = "* RD *";
              $shiftEnd = "* RD *";
              $UT = 0;
              $billableForOT=0;
              

          } else 
          { 
                //--- legit OT, compute billable hours
                $logOUT = date('h:i A',strtotime($userLogOUT->first()->logTime));
                $timeEnd = Carbon::parse($userLogOUT->first()->logTime);
                $wh = $timeEnd->diffInMinutes($timeStart->addHour()); //--- pag RD OT, no need to add breaktime 1HR
                $workedHours = number_format($wh/60,2);
                $billableForOT = $workedHours;

                if ($hasHolidayToday)
                {
                  
                  $workedHours .= "<br /><strong>* " . $holidayToday->first()->name . " * </strong>";

                } else $workedHours .= "<br /><small>* RD-OT * </small>";


                 if ($hasHolidayToday)
                 $icons = "<a  data-toggle=\"modal\" data-target=\"#myModal_OT".$payday."\"  title=\"File this OT\" class=\"pull-right\" style=\"font-size:1.2em;\" href=\"#\"><i class=\"fa fa-credit-card\"></i></a>";
               else
                $icons = "<a  data-toggle=\"modal\" data-target=\"#myModal_OT".$payday."\"  title=\"File this RD-OT\" class=\"pull-right\" style=\"font-size:1.2em;\" href=\"#\"><i class=\"fa fa-credit-card\"></i></a>";
               

                
                $OTattribute = $icons;
                $shiftStart = "* RD *";
                $shiftEnd = "* RD *";
                $UT = 0;
          }



       }//end if may login kahit RD

       $data = new Collection;
       $data->push(['shiftStart'=>$shiftStart, 
        'shiftEnd'=>$shiftEnd, 'logIN'=>$logIN, 
        'logOUT'=>$logOUT,'workedHours'=>$workedHours, 
        'billableForOT'=>$billableForOT, 'OTattribute'=>$OTattribute, 'UT'=>$UT, 
        'dtrpIN'=>null,'dtrpIN_id'=>null, 'dtrpOUT'=>null,'dtrpOUT_id'=>null,
        'hasPendingIN' => $hasPendingIN,
        'pendingDTRPin'=> $pendingDTRPin,
        'hasPendingOUT' => $hasPendingOUT,
        'pendingDTRPout' => $pendingDTRPout,
        'approvedOT'=>$approvedOT]);
       return $data;


   
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




  public function getWorkedHours($user_id, $userLogIN, $userLogOUT, $schedForToday,$shiftEnd, $payday)
  {

    $data = new Collection;
    $billableForOT=0;
    $UT = 0;
    $OTattribute = "";
    $campName = User::find($user_id)->campaign->first()->name;

    $hasHolidayToday = false;
    $hasLWOP = null; $lwopDetails = new Collection; $hasPendingLWOP=false;
    $hasVL = null; $vlDetails = new Collection; $hasPendingVL=false;
    $hasOBT = null; $obtDetails = new Collection; $hasPendingOBT=false;

    //$thisPayrollDate = Biometrics::where(find($biometrics->id)->productionDate;
    $holidayToday = Holiday::where('holidate', $payday)->get();


    $theDay = Carbon::parse($payday." ".$schedForToday['timeStart'],"Asia/Manila");
    $fix= Carbon::parse($payday." 23:59:00","Asia/Manila");

    /*------ WE CHECK FIRST IF THERE'S AN APPROVED VL | SL | LWOP -----*/
    

    $vl = User_VL::where('user_id',$user_id)->where('leaveStart','<=',$fix->format('Y-m-d H:i:s'))->where('leaveEnd','>=',$theDay->format('Y-m-d H:i:s'))->orderBy('created_at','DESC')->get();

    $sl = User_SL::where('user_id',$user_id)->where('leaveStart','<=',$fix->format('Y-m-d H:i:s'))->where('leaveEnd','>=',$theDay->format('Y-m-d H:i:s'))->orderBy('created_at','DESC')->get();

    $lwop = User_LWOP::where('user_id',$user_id)->where('leaveStart','<=',$fix->format('Y-m-d H:i:s'))->where('leaveEnd','>=',$theDay->format('Y-m-d H:i:s'))->orderBy('created_at','DESC')->get();

    $obt = User_OBT::where('user_id',$user_id)->where('leaveStart','<=',$fix->format('Y-m-d H:i:s'))->where('leaveEnd','>=',$theDay->format('Y-m-d H:i:s'))->orderBy('created_at','DESC')->get();



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



     if (count($holidayToday) > 0) $hasHolidayToday = true;



        if (count($userLogIN[0]['logs']) > 0 && count($userLogOUT[0]['logs']) > 0)
        {
          //---- To get the right Worked Hours, check kung early pasok == get schedule Time
          //---- if late pumasok, get user timeIN


          //************ CHECK FOR LATEIN AND EARLY OUT ***************//

          // $checkLate = $userLogIN[0]['timing']->diffInMinutes(Carbon::parse($schedForToday['timeStart']));
          // if ($checkLate > 1) $isLateIN = true; else $isLateIN= false;

          // $checkEarlyOut = $userLogOUT[0]['timing']->diffInMinutes(Carbon::parse($schedForToday['timeEnd'],"Asia/Manila"));
          // if ($checkEarlyOut > 1)  $isEarlyOUT = true; else $isEarlyOUT= false;


          $link = action('UserController@myRequests',$user_id);
          $icons ="";
          $workedHours=null;$log="";



          if ($userLogIN[0]['timing']->format('H:i:s') > $schedForToday['timeStart'])
          {
            $checkLate = $userLogIN[0]['timing']->diffInMinutes(Carbon::parse($schedForToday['timeStart'], "Asia/Manila"));
            //---- MARKETING TEAM CHECK: 15mins grace period
              
              
                 if ($checkLate > 1) $isLateIN = true; else $isLateIN= false;
              

            
          } else $isLateIN= false;


          if ($userLogOUT[0]['timing']->format('H:i:s') < $schedForToday['timeEnd'])
          {
            $checkEarlyOut = $userLogOUT[0]['timing']->diffInMinutes(Carbon::parse($schedForToday['timeEnd'],"Asia/Manila"));
            //---- MARKETING TEAM CHECK: 15mins grace period
              
                 if ($checkEarlyOut > 1) $isEarlyOUT = true; else $isEarlyOUT= false;
              

            
          } else $isEarlyOUT= false;

        

          if ($isEarlyOUT && $isLateIN)//use user's logs
          {
            $prod = Carbon::parse($userLogOUT[0]['timing'])->format('Y-m-d');

            $wh = Carbon::parse($userLogOUT[0]['timing'],'Asia/Manila')->diffInMinutes(Carbon::parse($userLogIN[0]['timing'],'Asia/Manila'));
            $workedHours = number_format($wh/60,2);
            $billableForOT=0; //$userLogIN[0]['timing']/60;
            $UT = number_format((480.0 - $wh)/60,2);
            

          }
          else if ($isEarlyOUT)
          {
             //--- but u need to make sure if nag late out sya
              if (Carbon::parse($userLogOUT[0]['timing'],"Asia/Manila") > Carbon::parse($schedForToday['timeEnd'],"Asia/Manila"))
              {
                $workedHours = 8.00;

                $icons = "<a title=\"File this OT\" class=\"pull-right\" style=\"font-size:1.2em;\" href=\"#\"><i class=\"fa fa-credit-card\"></i></a>";
                 $totalbill = number_format((Carbon::parse($shiftEnd,"Asia/Manila")->diffInMinutes(Carbon::parse($userLogOUT[0]['timing'],"Asia/Manila") ))/60,2);
                $totalbill = number_format((Carbon::parse($shiftEnd,"Asia/Manila")->diffInMinutes(Carbon::parse($userLogOUT[0]['timing'],"Asia/Manila") ))/60,2);

                if ($totalbill > 0.5){
                  $billableForOT = $totalbill;
                  $OTattribute = $icons;
                }
                  
                else
                {
                  $billableForOT = $totalbill;
                  $OTattribute = "&nbsp;&nbsp;&nbsp;";
                } 
              }
                
              else
              {

                /*--- WE NEED TO CHECK FIRST KUNG MAY LEGIt LEAVES SYA ***/

                $wh = Carbon::parse($userLogOUT[0]['timing'],"Asia/Manila")->diffInMinutes(Carbon::parse($schedForToday['timeStart'],"Asia/Manila")->addHour());


                if ($hasSL)
                {
                  $workedHours1 = $this->processLeaves('SL',true,$wh,$slDeet,$hasPendingSL,$icons,$userLogIN[0],$userLogOUT[0],$shiftEnd);
                  $workedHours .= $workedHours1[0]['workedHours'];
                  $UT = $workedHours1[0]['UT'];

                }//end if has SL
               
               if ($hasVL)
                {
                  $workedHours1 = $this->processLeaves('VL',true,$wh,$vlDeet,$hasPendingVL,$icons,$userLogIN[0],$userLogOUT[0],$shiftEnd);
                  $workedHours .= $workedHours1[0]['workedHours'];
                  $UT = $workedHours1[0]['UT'];

                }//end if has VL
                

                 if ($hasOBT)
                  {
                    $workedHours1 = $this->processLeaves('OBT',true,$wh,$obtDeet,$hasPendingOBT,$icons,$userLogIN[0],$userLogOUT[0],$shiftEnd);
                    $workedHours .= $workedHours1[0]['workedHours'];
                    $UT = $workedHours1[0]['UT'];
                         
                  }//end if has OBT



                 if ($hasLWOP)
                  {
                    $workedHours1 = $this->processLeaves('LWOP',true,$wh,$lwopDeet,$hasPendingLWOP,$icons,$userLogIN[0],$userLogOUT[0],$shiftEnd);
                    $workedHours .= $workedHours1[0]['workedHours'];
                    $UT = $workedHours1[0]['UT'];

                  }//end if has LWOP
                

                if (!$hasSL && !$hasVL && !$hasLWOP && !$hasOBT)
                {
                  $workedHours .= number_format($wh/60,2)."<br/><small>(early OUT)</small>";$UT = round((480.0 - $wh)/60,2); $billableForOT=0;
                  }

                if ($hasHolidayToday)
                {
                  $workedHours .= "<br/> <strong>* ". $holidayToday->first()->name. " *</strong>";
                }



              }//end ifelse

            
          }//end if EarlyOUT

          else if($isLateIN){

            //--- but u need to make sure if nag late out sya
            //    otherwise, super undertime talaga sya

            if (Carbon::parse($userLogOUT[0]['timing'],"Asia/Manila") > Carbon::parse($schedForToday['timeEnd'],"Asia/Manila") )
            {
              $wh = Carbon::parse($schedForToday['timeEnd'],"Asia/Manila")->diffInMinutes(Carbon::parse($userLogIN[0]['timing'],"Asia/Manila")->addMinutes(60));
             
              /* ---- but we need to check Jeff's case of multiple requessts
                      bakit sya lateIN? baka may valid SL | VL |OBT */


                if ($hasSL)
                {
                  $wh = Carbon::parse($schedForToday['timeEnd'],"Asia/Manila")->diffInMinutes(Carbon::parse($userLogIN[0]['timing'],"Asia/Manila"));

                  $workedHours1 = $this->processLeaves('SL',true,$wh,$slDeet,$hasPendingSL,$icons,$userLogIN[0],$userLogOUT[0],$shiftEnd);
                  $workedHours .= $workedHours1[0]['workedHours'];
                  $UT = $workedHours1[0]['UT'];

                }//end if has SL

                else if ($hasVL)
                {
                  $workedHours1 = $this->processLeaves('VL',true,$wh,$vlDeet,$hasPendingVL,$icons,$userLogIN[0],$userLogOUT[0],$shiftEnd);
                  $workedHours .= $workedHours1[0]['workedHours'];
                  $UT = $workedHours1[0]['UT'];

                }//end if has VL

                else if ($hasOBT)
                  {

                      $workedHours1 = $this->processLeaves('OBT',true,$wh,$obtDeet,$hasPendingOBT,$icons,$userLogIN[0],$userLogOUT[0],$shiftEnd);
                      $workedHours .= $workedHours1[0]['workedHours'];
                      $UT = $workedHours1[0]['UT'];

                  }//end if has OBT


                else if ($hasLWOP)
                  {
                      $workedHours1 = $this->processLeaves('LWOP',true,$wh,$lwopDeet,$hasPendingLWOP,$icons,$userLogIN[0],$userLogOUT[0],$shiftEnd);
                      $workedHours .= $workedHours1[0]['workedHours'];
                      $UT = $workedHours1[0]['UT'];

                  }//end if has LWOP

                else
                {
                  
                   $workedHours = number_format($wh/60,2)."<br/><small>(Late IN)</small>";$billableForOT=0;
                   if ($hasHolidayToday){ $workedHours .= "<br/> <strong>* ". $holidayToday->first()->name. " *</strong>";}
                  
                    $icons = "<a  data-toggle=\"modal\" data-target=\"#myModal_OT".$payday."\"   title=\"File this OT\" class=\"pull-right\" style=\"font-size:1.2em;\" href=\"#\"><i class=\"fa fa-credit-card\"></i></a>";
                     $totalbill = number_format((Carbon::parse($shiftEnd,"Asia/Manila")->diffInMinutes(Carbon::parse($userLogOUT[0]['timing'],"Asia/Manila") ))/60,2);
                    $totalbill = number_format((Carbon::parse($shiftEnd,"Asia/Manila")->diffInMinutes(Carbon::parse($userLogOUT[0]['timing'],"Asia/Manila") ))/60,2);

                    if ($totalbill > 0.5)
                    {
                      $billableForOT = $totalbill;
                      $OTattribute = $icons;
                    }
                      
                    else { $billableForOT = 0; /*$totalbill*/; $OTattribute = "&nbsp;&nbsp;&nbsp;";} 

                    $UT = round((480.0 - $wh)/60,2);


                } //normal LateIN process


               

            }
            else //super undertime sya
            {
                $wh = Carbon::parse($schedForToday['timeEnd'],"Asia/Manila")->diffInMinutes(Carbon::parse($userLogIN[0]['timing'],"Asia/Manila")->addMinutes(60));
                
                 
                  if ($hasSL)
                  {
                    $workedHours1 = $this->processLeaves('SL',true,$wh,$slDeet,$hasPendingSL,$icons,$userLogIN[0],$userLogOUT[0],$shiftEnd);
                    $workedHours .= $workedHours1[0]['workedHours'];
                    $UT = $workedHours1[0]['UT'];

                  }//end if has SL
                  

                   if ($hasVL)
                  {
                    $workedHours1 = $this->processLeaves('VL',true,$wh,$vlDeet,$hasPendingVL,$icons,$userLogIN[0],$userLogOUT[0],$shiftEnd);
                    $workedHours .= $workedHours1[0]['workedHours'];
                    $UT = $workedHours1[0]['UT'];
                  }//end if has VL

                   if ($hasOBT)
                  {

                      $workedHours1 = $this->processLeaves('OBT',true,$wh,$obtDeet,$hasPendingOBT,$icons,$userLogIN[0],$userLogOUT[0],$shiftEnd);
                      $workedHours .= $workedHours1[0]['workedHours'];
                      $UT = $workedHours1[0]['UT'];

                  }//end if has OBT


                  if ($hasLWOP)
                  {
                      $workedHours1 = $this->processLeaves('LWOP',true,$wh,$lwopDeet,$hasPendingLWOP,$icons,$userLogIN[0],$userLogOUT[0],$shiftEnd);
                      $workedHours .= $workedHours1[0]['workedHours'];
                      $UT = $workedHours1[0]['UT'];

                  }//end if has LWOP

                  if (!$hasSL && !$hasVL && !$hasLWOP && !$hasOBT)
                    {
                      $workedHours .= number_format($wh/60,2)."<br/><small>(Late IN)</small>";$UT = round((480.0 - $wh)/60,2); $billableForOT=0;
                    }

                  if ($hasHolidayToday)
                      {
                        $workedHours .= "<br/> <strong>* ". $holidayToday->first()->name. " *</strong>";
                      }



            }//end else super undertime
            
          }//end if lateIN
          else {

             $wh = Carbon::parse($userLogOUT[0]['timing'],"Asia/Manila")->diffInMinutes(Carbon::parse($schedForToday['timeStart'],"Asia/Manila")->addHour());

             
                proceedWithNormal:

                    if ($wh > 480)
                    {
                      $workedHours =8.00; 
                       $icons = "<a  data-toggle=\"modal\" data-target=\"#myModal_OT".$payday."\"  title=\"File this OT\" class=\"pull-right\" style=\"font-size:1.2em;\" href=\"#\"><i class=\"fa fa-credit-card\"></i></a>";
                      $totalbill = number_format((Carbon::parse($shiftEnd,"Asia/Manila")->diffInMinutes(Carbon::parse($userLogOUT[0]['timing'],"Asia/Manila") ))/60,2);

                      if ($totalbill > 0.5)
                      {
                        $billableForOT = $totalbill;
                        $OTattribute = $icons;
                      }
                        
                      else { $billableForOT = 0; /* $totalbill*/; $OTattribute= "&nbsp;&nbsp;&nbsp;";} 

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
                      }//end else di overworked, sakto lang

                      $UT = '0';
                
              


          } //endif else normal logs
          
         
        } //end if may login and logout
        else
        {
          $WHcounter = 8.0; $UT=0;
          $link = action('UserController@myRequests',$user_id);
          $icons ="";
          $workedHours=null;$log="";

          if ($hasVL)
          {
              $workedHours1 = $this->processLeaves('VL',false,$WHcounter,$vlDeet,$hasPendingVL,$icons,$userLogIN[0],$userLogOUT[0],$shiftEnd);
              $workedHours .= $workedHours1[0]['workedHours'];
              $UT = $workedHours1[0]['UT'];

              

          }//end if has VL


          if ($hasOBT)
          {
              $workedHours1 = $this->processLeaves('OBT',false,$WHcounter,$obtDeet,$hasPendingOBT,$icons,$userLogIN[0],$userLogOUT[0],$shiftEnd);
              $workedHours .= $workedHours1[0]['workedHours'];
              $UT = $workedHours1[0]['UT'];

              

          }//end if has OBT



          if ($hasSL)
          {
              $workedHours1 = $this->processLeaves('SL',false,$WHcounter,$slDeet,$hasPendingSL,$icons,$userLogIN[0],$userLogOUT[0],$shiftEnd);
              $workedHours .= $workedHours1[0]['workedHours'];
              $UT = $workedHours1[0]['UT'];

             
          }//end if has SL





          if ($hasLWOP)
          {
              $workedHours1 = $this->processLeaves('LWOP',false,0,$lwopDeet,$hasPendingLWOP,$icons,$userLogIN[0],$userLogOUT[0],$shiftEnd);
              $workedHours .= $workedHours1[0]['workedHours'];
              $UT = $workedHours1[0]['UT'];

             

          }//end if has LWOP

          


          if ($hasHolidayToday) /***--- we will need to check if Non-Ops personnel, may pasok kasi pag OPS **/
          {
            $workedHours .= "(8.0)<br/> <strong>* " . $holidayToday->first()->name . " *</strong>";
          }

         if (!$hasVL && !$hasSL && !$hasLWOP &&  !$hasOBT && !$hasHolidayToday)
            $workedHours = "<a title=\"Check your Biometrics data. \n It's possible that you pressed a wrong button, the machine malfunctioned, or you really didn't log in / out.\"><strong class=\"text-danger\">AWOL </strong></a>";
        }

        
        $data->push(['checkLate'=>"nonComplicated", 'workedHours'=>$workedHours, 
                      'billableForOT'=>$billableForOT, 'OTattribute'=>$OTattribute,
                      'UT'=>$UT, 'VL'=>$hasVL, 'SL'=>$hasSL, 'LWOP'=>$hasLWOP ]);



        return $data;


  }

  public function getWorkedOThours($log,$sched,$prodDate1, $prodDate2)
  {
    $lg = Carbon::parse($prodDate1." ".$log,'Asia/Manila');
    $schd = Carbon::parse($prodDate2." ".$sched,'Asia/Manila');

    return $lg->diffInMinutes($schd);
  }





  public function getWorkSchedForTheDay($user, $leaveDay, $hasPending)
    {
      DB::connection()->disableQueryLog();
      //$user = User::find($id);
      $vl_to = $leaveDay;

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
       $sched = null;

       //------------ NEW CHECK: if has both fixed and monthly sched
       //------------ 1) if (current date) IN monthly_schedule->productionDate
       //                   >> check their created_at; compare against FixedSched[dayOfWeek]->created_at; get the latest one
       //                else you get the fixed sched

       if ($totalMschedules > 0 && $totalFschedules > 0 ) //((count($user->monthlySchedules) > 0) &&  (count($user->fixedSchedule) > 0)) // 
       {

          $workSched_monthly = MonthlySchedules::where('user_id',$user->id)->where('isRD',0)->orderBy('productionDate','ASC')->get(); 
          $RDsched_monthly = MonthlySchedules::where('user_id',$user->id)->where('isRD',1)->get(); 
          $workSched_fixed = FixedSchedules::where('user_id',$user->id)->where('isRD',0)->orderBy('created_at','DESC')->get();
                             // $user->fixedSchedule->where('isRD',0);
          $RDsched_fixed = FixedSchedules::where('user_id',$user->id)->where('isRD',1)->orderBy('created_at','DESC')->get();
                          // $user->fixedSchedule->where('isRD',1); //->pluck('workday');

          $isFixedSched =false;
          $noWorkSched = false;
         
         //$coll2->push(['start'=>$startingPoint, 'end'=>$endDate]);
          

              $dt  = $startingPoint->dayOfWeek;
              switch($dt){
                case 0: $dayToday = 6; break;
                case 1: $dayToday = 0; break;
                default: $dayToday = $dt-1;
              } 

              $wd_fixed1 = $workSched_fixed->where('workday',$dayToday); 
                            //$user->fixedSchedule->where('workday',$dayToday)->sortByDesc('created_at')->first();

              if (count($wd_fixed1)>0) 
              {
                $wd_fixed = $wd_fixed1->first();

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
              } //end with WD fixed
              else /*continue on with monthly scheds */
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

                          $coll = $this->getFixedSchedules2($latest_fixed->first(),$startingPoint->format('Y-m-d'),$coll,$counter);
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
                               
                                $coll = $this->getFixedSchedules2($latest_fixed->first(),$startingPoint->format('Y-m-d'),$coll,$counter);
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
                            $coll = $this->getFixedSchedules2($latest_fixed->first(),$startingPoint->format('Y-m-d'),$coll,$counter);
                            $sched = $coll->first();
                           
                          }

                    }else { 
                      
                      $coll = $this->getShiftingSchedules2($latest, $coll,$counter);$sched = $coll->first();
                      

                    }

                  

                } else
                {
                      $RD_fixed = $RDsched_fixed->where('workday',$dayToday); 

                      if (count($RD_fixed) > 0)
                      {
                        $coll = $this->getFixedSchedules2($RD_fixed->first(),$startingPoint->format('Y-m-d'),$coll,$counter); 
                        $sched = $coll->first();
                      }
                      
                    
                }//end else if today is in monthly_sched




              }//end if else WD fixed
                          

              
              
                    
                    

              if ( $coll->contains('chenes', $startingPoint->format('Y-m-d')) )
              {
                //do nothing
               // $sched->push(['totalFschedules'=>count($totalFschedules), 'totalMschedules'=>$totalMschedules]);
              } else{
                $sched->push(['shiftStart'=>null, 'shiftEnd'=>null]);

              }

             


       } else
       {

            if ($totalMschedules > 0)
            
           {
              //$monthlySched = MonthlySchedules::where('user_id',$id)->get();
              $workSched = MonthlySchedules::where('user_id',$user->id)->where('isRD',0)->where('productionDate',$productionDate->format('Y-m-d'))->orderBy('productionDate','ASC')->get(); 
              $RDsched = MonthlySchedules::where('user_id',$user->id)->where('isRD',1)->where('productionDate',$productionDate->format('Y-m-d'))->get(); 
              $isFixedSched = false;
              $noWorkSched = false;

            } else
           {
              if ( $totalFschedules > 0)
              {
                  //merong fixed sched
                  $workSched = FixedSchedules::where('user_id',$user->id)->where('isRD',0)->orderBy('created_at','DESC')->get();
                              // $user->fixedSchedule->where('isRD',0);
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
                  $wd_fixed = FixedSchedules::where('user_id',$user->id)->where('workday',$dayToday)->orderBy('created_at','DESC')->get();
                              // $user->fixedSchedule->where('workday',$dayToday)->sortByDesc('created_at')->first();

                  //check first kung pasok sa effectivity date
                 $counter=0;
                  foreach ($wd_fixed as $key) 
                  {
                      if ( (Carbon::parse($key->schedEffectivity) <= $startingPoint) || $key->schedEffectivity == null )
                      {
                        
                        $coll = $this->getFixedSchedules2($key,$startingPoint->format('Y-m-d'),$coll,$counter);
                        $sched = $coll->first();
                        break;

                      } $counter++;
                  }


                  // if ( (Carbon::parse($wd_fixed->schedEffectivity) <= $startingPoint) || $wd_fixed->schedEffectivity == null )
                  // {
                  //   $coll = $this->getFixedSchedules2($wd_fixed,$startingPoint->format('Y-m-d'),$coll,$counter);
                  //   $sched = $coll->first();
                  // }
              

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

       //return response()->json($sched);
       return $sched;

      
    
      //return response()->json($productionDate);

    }



  public function processLeaves($leaveType,$withIssue,$wh, $deet,$hasPending,$icons,$ins,$outs,$shiftEnd)//$userLogIN[0]['logs'] || $userLogOUT[0]['logs']
  {
    switch ($leaveType) {
      case 'OBT':{
                    $link = action('UserOBTController@show',$deet->id);
                    $i = "fa-suitcase";
                    $lTitle = "OBT request";
                    $l = "OBT";
                    $label = "Official Business Trip";
                    $workedHours = 8.0;
              # code...
              }break;

      case 'LWOP':{
                    $link = action('UserLWOPController@show',$deet->id);
                    $i = "fa-meh-o";
                    $lTitle = "LWOP request";
                    $l = "LWOP";
                    $label = "Leave Without Pay";
                    $workedHours = " ";
              # code...
              }break;

     case 'SL':{
                    $link = action('UserSLController@show',$deet->id);
                    $i = "fa-stethoscope";
                    $lTitle = "SL request";
                    $l = "SL";
                    $label = "Sick Leave";
                    $workedHours = "N/A";
              # code...
              }break;

      case 'VL':{
                    $link = action('UserVLController@show',$deet->id);
                    $i = "fa-plane";
                    $lTitle = "VL request";
                    $l = "VL";
                    $label = " Vacation Leave";
                    $workedHours = 8.0;
              # code...
              }break;
      
      
    }

      
      $icons .= "<a title=\"".$lTitle."\" class=\"pull-right text-primary\" target=\"_blank\" style=\"font-size:1em;\" href=\"$link\"><i class=\"fa fa-info-circle\"></i></a><div class='clearfix'></div>";
      $coll = new Collection;
      $UT = 0;

      if ($withIssue){

        

        if ($deet->totalCredits >= '1.0'){

          if($hasPending){
            $workedHours = "<strong class='text-danger'>AWOL</strong><br/>";
            $log="<strong><small><i class=\"fa ".$i."\"></i> <em> ".$l." for approval </em></small></strong>".$icons;
            
          }else{

            if($leaveType=='LWOP') $workedHours = 0;
            else $workedHours = 8.0;
            $log="<strong><small><i class=\"fa ".$i."\"></i> <em> ".$label." </em></small></strong>".$icons;
            
          }
          
          
          $workedHours .= "<br/>".$log;

        }
        else if ($deet->totalCredits == '0.50'){

            if($hasPending){
              if ($deet->halfdayFrom == 2){
                $log="<strong><small><i class=\"fa ".$i." \"></i> <em> 1st Shift ".$l." (for approval) </em></small></strong>".$icons;
                $workedHours = number_format(($wh/60),2)."<br/><small>(Late IN)</small>";$billableForOT=0;
                $UT = round(((420.0 - $wh)/60),2); //4h instead of 8H
              }
              
                
              else if ($deet->halfdayFrom == 3){
                $log="<strong><small><i class=\"fa ".$i." \"></i> <em> 2nd Shift ".$l." (for approval) </em></small></strong>".$icons;
                $workedHours = number_format(($wh/60)+5,2)."<br/><small>(Late IN)</small>";$billableForOT=0;
                $UT = round(((240.0 - $wh)/60)-1,2); //4h instead of 8H
              }
              else
                $log="<strong><small><i class=\"fa ".$i." \"></i> <em> Half-day ".$l." (for approval) </em></small></strong>".$icons;
              
              
                    //no logs, meaning halfday AWOL sya
                    if (count($ins) < 1 && count($outs) < 1) 
                      $log.="<br/><strong class='text-danger'><small><em>Half-day AWOL</em></small></strong>";

              $workedHours = "<strong class='text-danger'>AWOL</strong>";
              $workedHours .= "<br/>".$log;

            }else{

              if ($deet->halfdayFrom == 2){

                $log="<strong><small><i class=\"fa ".$i."\"></i> <em> 1st Shift ".$l." </em></small></strong>".$icons;
                if (!empty($ins) && !empty($outs) && ($leaveType !== 'OBT' && $leaveType !== 'VL') ) {
                  $workedHours = number_format(($wh/60),2)."<br/><small>(Late IN)</small>";
                  $UT = round(((480.0 - $wh)/60),2); //full 8h work dapat
                }
                else {
                  $workedHours = number_format(($wh/60)+5,2)."<br/><small>(Late IN)</small>";
                  $UT = round(((240.0 - $wh)/60)-1,2); //4h instead of 8H
                }

                $billableForOT=0;
                
                
              }
              else if ($deet->halfdayFrom == 3){
                $log="<strong><small><i class=\"fa ".$i."\"></i> <em> 2nd Shift ".$l." </em></small></strong>".$icons;
                if (!empty($ins) && !empty($outs) && ($leaveType !== 'OBT' && $leaveType !== 'VL') )
                  $workedHours = number_format(($wh/60),2)."<br/><small>(Late IN)</small>";
                else
                  $workedHours = number_format(($wh/60)+5,2)."<br/><small>(Late IN)</small>";

                $billableForOT=0;
                $UT = round(((240.0 - $wh)/60)-1,2); //4h instead of 8H
              }
              else{
                $log="<strong><small><i class=\"fa ".$i."\"></i> <em> Half-day ".$l."  </em></small></strong>".$icons;
                 $workedHours = number_format(($wh/60)+5,2)."<br/><small>(Late IN)</small>";$billableForOT=0;
                 $UT = round(((240.0 - $wh)/60)-1,2); //4h instead of 8H
               }

              
                    if (count($ins) < 1 && count($outs) < 1) 
                      $log.="<br/><strong class='text-danger'><small><em>Half-day AWOL</em></small></strong>";
              

              $WHcounter = 8.0;
              $workedHours .= "<br/>".$log;


               $totalbill = number_format((Carbon::parse($shiftEnd,"Asia/Manila")->diffInMinutes(Carbon::parse($outs['timing'],"Asia/Manila") ))/60,2);
               $totalbill = number_format((Carbon::parse($shiftEnd,"Asia/Manila")->diffInMinutes(Carbon::parse($outs['timing'],"Asia/Manila") ))/60,2);

                    if ($totalbill > 0.5)
                    {
                      $billableForOT = $totalbill;
                      $OTattribute = $icons;
                    }
            }
                  
                    
                  
        }// end if 0.5 credits

        
       


      }else{

        if ($deet->totalCredits >= '1.0'){

          if($hasPending){
            $workedHours = "<strong class='text-danger'>AWOL</strong><br/>";
            $log="<strong><small><i class=\"fa ".$i."\"></i> <em> ".$l." for approval </em></small></strong>".$icons;
          }else{
            
            $log="<strong><small><i class=\"fa ".$i."\"></i> <em> ".$label." </em></small></strong>".$icons;
          }
          
          if($leaveType=='LWOP') $workedHours  .= "0.0<br/>".$log;
          else
          $workedHours .= "<br/>".$log;

        } 
        else if ($deet->totalCredits == '0.50'){

            if($hasPending){
              if ($deet->halfdayFrom == 2)
                $log="<strong><small><i class=\"fa ".$i." \"></i> <em> 1st Shift ".$l." (for approval) </em></small></strong>".$icons;
              else if ($deet->halfdayFrom == 3)
                $log="<strong><small><i class=\"fa ".$i." \"></i> <em> 2nd Shift ".$l." (for approval) </em></small></strong>".$icons;
              else
                $log="<strong><small><i class=\"fa ".$i." \"></i> <em> Half-day ".$l." (for approval) </em></small></strong>".$icons;
              
              
                    //no logs, meaning halfday AWOL sya
                    if (count($ins) < 1 && count($outs) < 1) 
                      $log.="<br/><strong class='text-danger'><small><em>Half-day AWOL</em></small></strong>";

              $workedHours = "<strong class='text-danger'>AWOL</strong>";
              $workedHours .= "<br/>".$log;

            }else{

              if ($deet->halfdayFrom == 2)
                $log="<strong><small><i class=\"fa ".$i."\"></i> <em> 1st Shift ".$l." </em></small></strong>".$icons;
              else if ($deet->halfdayFrom == 3)
                $log="<strong><small><i class=\"fa ".$i."\"></i> <em> 2nd Shift ".$l." </em></small></strong>".$icons;
              else
                $log="<strong><small><i class=\"fa ".$i."\"></i> <em> Half-day ".$l."  </em></small></strong>".$icons;

              
                    if (count($ins) < 1 && count($outs) < 1) 
                      $log.="<br/><strong class='text-danger'><small><em>Half-day AWOL</em></small></strong>";

              if($leaveType=='LWOP') $workedHours  = 0.0;     
              else $workedHours = '4.0';
              $WHcounter = 4.0;
              $workedHours .= "<br/>".$log;
            }
                  
                    
                  
        }// end if 0.5 credits
      

      }//end withIssue

      $coll->push(['workedHours'=>$workedHours,'UT'=>$UT,]);
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
        $approvers = $employee->approvers;
        $anApprover = $this->checkIfAnApprover($approvers, $this->user);

        
        if ($anApprover)
        {
            $cws->isApproved = true; $TLsubmitted=true; $cws->approver = $request->approver;
        } else { $cws->isApproved = null; $TLsubmitted=false;$cws->approver = $request->approver; }


      
      $cws->save();

      //--- notify the TL concerned
      //$employee = User::find($cws->user_id);

     if (!$anApprover) //(!$TLsubmitted && !$canChangeSched)
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
          
         $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
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