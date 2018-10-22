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
use OAMPI_Eval\User_OT;
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
    $UT = 0;

    $billableForOT=0; $endshift = Carbon::parse($shiftEnd); $diff = null; $OTattribute="";
    $campName = User::find($user_id)->campaign->first()->name;

    $hasHolidayToday = false;
    //$thisPayrollDate = Biometrics::where(find($biometrics->id)->productionDate;
    $holidayToday = Holiday::where('holidate', $payday)->get();
    if (count($holidayToday) > 0) $hasHolidayToday = true;


        if (count($userLogIN[0]['logs']) > 0 && count($userLogOUT[0]['logs']) > 0)
        {
          //---- To get the right Worked Hours, check kung early pasok == get schedule Time
          //---- if late pumasok, get user timeIN
         
          //************ CHECK FOR LATEIN AND EARLY OUT ***************//

          if ($isRDYest)
          {
            $chenes = "RD yest";
            $todayStart = Carbon::parse($payday." ".$schedForToday['timeStart'],"Asia/Manila"); //->format('Y-m-d H:i:s');
            $todayEnd = Carbon::parse($payday." ".$schedForToday['timeEnd'],"Asia/Manila")->addDay(); //->format('Y-m-d H:i:s');
            $actualIN = Carbon::parse($userLogOUT[0]['timing'],"Asia/Manila"); //->format('Y-m-d H:i:s');
            $actualOUT = Carbon::parse($userLogOUT[0]['timing'],"Asia/Manila"); //->format('Y-m-d H:i:s');

            if ($actualIN > $todayStart && $actualIN < $todayEnd)
            {
              $checkLate = $actualIN->diffInMinutes($todayStart);
              
               //---- MARKETING TEAM CHECK: 15mins grace period
              
              if( $campName == "Marketing" || $campName == "Lebua")
              {
                 if ($checkLate > 15) $isLateIN = true; else $isLateIN= false;

              } else
              {
                 if ($checkLate > 1) $isLateIN = true; else $isLateIN= false;
              }

            } else $isLateIN=false;


            if ($actualOUT > $todayStart && $actualOUT < $todayEnd)
            {
              $checkEarlyOut = $actualOUT->diffInMinutes($todayEnd);

               //---- MARKETING TEAM CHECK: 15mins grace period
              if( $campName == "Marketing" || $campName == "Lebua")
              {
                 if ($checkEarlyOut > 15) $isEarlyOUT = true; else $isEarlyOUT= false;

              } else
              {
                 if ($checkEarlyOut > 1) $isEarlyOUT = true; else $isEarlyOUT= false;
              }

              
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
            
            $actualIN = Carbon::parse($userLogIN[0]['timing'],"Asia/Manila"); //->format('Y-m-d H:i:s');
            $actualOUT = Carbon::parse($userLogOUT[0]['timing'],"Asia/Manila"); //->format('Y-m-d H:i:s');

            if ($schedForToday['timeStart'] == "00:00:00")
              $todayStart = Carbon::parse($payday." ".$schedForToday['timeStart'],"Asia/Manila")->addDay(); //->format('Y-m-d H:i:s');
            else
              $todayStart = Carbon::parse($payday." ".$schedForToday['timeStart'],"Asia/Manila"); //->format('Y-m-d H:i:s');

            $todayEnd = Carbon::parse($payday." ".$schedForToday['timeEnd'],"Asia/Manila"); //->format('Y-m-d H:i:s');


            //*** --- check if late time in and less than or equal to out
            if ($userLogIN[0]['timing']->format('H:i:s') > $schedForToday['timeStart']) // && $userLogIN[0]['timing']->format('H:i:s') <= $schedForToday['timeEnd'] )
            {

              if ($schedForToday['timeStart'] == "00:00:00")
                $checkLate = $actualIN->diffInHours($todayStart);//diffInHours(Carbon::parse($payday." 24:00:00", "Asia/Manila"));// $actualIN->diffInHours($todayStart);
             else 
              $checkLate = $todayStart->diffInHours($actualIN);

              //---- MARKETING TEAM CHECK: 15mins grace period
              
              if( $campName == "Marketing" || $campName == "Lebua")
              {
                 if ($checkLate > 15) $isLateIN = true; else $isLateIN= false;

              } else
              {
                 if ($checkLate > 1) $isLateIN = true; else $isLateIN= false;
              }
             

            }else {$isLateIN= false; $checkLate="else";}

            if ( $actualOUT->format('H:i:s') < $schedForToday['timeEnd']) //$userLogOUT[0]['timing']->format('H:i:s')
            {
              $checkEarlyOut = $actualOUT->diffInMinutes($todayEnd);
              if( $campName == "Marketing" || $campName == "Lebua")
              {
                 if ($checkEarlyOut > 15) $isEarlyOUT = true; else $isEarlyOUT= false;

              } else
              {
                 if ($checkEarlyOut > 1) $isEarlyOUT = true; else $isEarlyOUT= false;
              }



            } else $isEarlyOUT= false;

          

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
              //$wh = $actualOUT->diffInMinutes(Carbon::parse($schedForToday['timeStart'],"Asia/Manila")->addHour(-23));
              //$wh = Carbon::parse( $schedForToday['timeStart'],"Asia/Manila")->diffInDays($actualOUT);
              /*if ($schedForToday['timeStart'] == "12:00:00")
                $wh = $actualOUT->addHour(-23)->diffInMinutes($todayStart);
              else
              $wh = $actualOUT->addHour()->diffInMinutes($todayStart);*/

              
              if ($schedForToday['timeStart'] == "00:00:00")
                {
                  //$wh =  Carbon::parse( $schedForToday['timeStart'],"Asia/Manila")->diffInMinutes($actualOUT);
                  $wh = $todayStart->addHour()->diffInMinutes($actualOUT);
                  $UT = abs(number_format($wh/60,2) - 8.0);
                  $workedHours = number_format($wh/60,2)."<br/><small>(early OUT)</small><br/>";$billableForOT=0;

                }
              else
               {

                //$checkLate = $actualIN->diffInHours($todayStart);// $actualIN->diffInHours($todayStart);
                  $wh =  Carbon::parse( $schedForToday['timeStart'],"Asia/Manila")->addHour(-23)->diffInMinutes($actualOUT);
                  $workedHours = number_format($wh/60,2)."<br/><small>(early OUT**)</small>";
                  $billableForOT=0;
                  $UT = abs(number_format($wh/60,2) - 8.0);
               
               }
               // if ($hasHolidayToday)
               //    {
               //      $workedHours .= "<br/> <strong>* ". $holidayToday->first()->name. " *</strong>";
               //    }

               //    $chenes = "isEarlyOUT";
            }
            else if ($isLateIN){
              //$wh = Carbon::parse($userLogOUT[0]['timing'],"Asia/Manila")->diffInMinutes(Carbon::parse($userLogOUT[0]['timing'],"Asia/Manila")->addHour());

              //$wh = $todayEnd->addHour()->diffInMinutes($actualOUT);
              $wh = Carbon::parse($schedForToday['timeEnd'],"Asia/Manila")->diffInMinutes(Carbon::parse($userLogIN[0]['timing'],"Asia/Manila")->addHour(-23));
              $workedHours = number_format($wh/60,2)."<br/><small>(Late IN)</small>";$billableForOT=0;
               // if ($hasHolidayToday)
               //    {
               //      $workedHours .= "<br/> <strong>* ". $holidayToday->first()->name. " *</strong>";
               //    }

               //    $chenes = "isLateIN";
              $UT = abs(number_format($wh/60,2) - 8.0);
            }
            else {
              $chenes = "else";
               $wh = Carbon::parse($userLogOUT[0]['timing'],"Asia/Manila")->diffInMinutes(Carbon::parse($schedForToday['timeStart'],"Asia/Manila")->addHour());
              
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

                   // if ($hasHolidayToday)
                   //  {
                   //    $workedHours .= "<br/> <strong>* ". $holidayToday->first()->name. " *</strong>";
                   //  }

                } //number_format(($endshift->diffInMinutes($out2))/60,2);}
                else 
                  { $workedHours = number_format($wh/60,2); $billableForOT=0; 
                    //  if ($hasHolidayToday)
                    // {
                    //   $workedHours .= "<br/> <strong>* ". $holidayToday->first()->name. " *</strong>";
                    // }
                }
                 //$UT = number_format($wh/60,2) - 8.0;
              

            }


          }//end not RD yesterday

          
          
         
        } //end if not empty logs
        else
        {

            if ($hasHolidayToday)
            {
              $workedHours = "(8.0)";
            }
            else
              $workedHours = "<a title=\"Check your Biometrics data. It's possible that you pressed a wrong button, or you really didn't log in / out.\"><strong class=\"text-danger\">AWOL</strong></a>";
        }




        if ($hasHolidayToday)
        {
          $workedHours .= "<br/> <strong>* ". $holidayToday->first()->name. " *</strong>";
        }


        $data->push(['checkLate'=>$chenes, 'payday'=>$payday, 
          'workedHours'=>$workedHours, 'billableForOT'=>$billableForOT, 
          'OTattribute'=>$OTattribute ,'UT'=>$UT
          // 'actualIN'=>$actualIN, 'actualOUT'=>$actualOUT, 
          // 'todayStart'=>$todayStart,'todayEnd'=>$todayEnd,
          // 'isEarlyOUT'=>$isEarlyOUT,'isLateIN'=>$isLateIN,
          // 'diffIN'=>$actualIN->diffInHours($actualOUT),
          ]);

        return $data;


  }
 

  public function getLogDetails($type, $id, $biometrics_id, $logType_id, $schedForToday, $undertime, $problemArea)
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
    $pendingDTRP = null;

    if (count($holidayToday) > 0) $hasHolidayToday = true;

      if ($problemArea[0]['problemShift']== true)
      {
      

        //***** CHECK FIRST IF THERE'S AN APPROVED DTRP,
        //***** ELSE USE RAW BIOMETRICS
        $hasApprovedDTRP = User_DTRP::where('user_id',$id)->where('isApproved',true)->where('biometrics_id',$problemArea[0]['biometrics_id'])->where('logType_id',$logType_id)->orderBy('updated_at','DESC')->get();

        $pendingDTRP = User_DTRP::where('user_id',$id)->where('biometrics_id',$problemArea[0]['biometrics_id'])->where('logType_id',$logType_id)->where('isApproved',null)->orderBy('id','DESC')->get();

        ( count($pendingDTRP) > 0  ) ? $hasPendingDTRP=true : $hasPendingDTRP=false;


        if(count($hasApprovedDTRP) > 0){ 

          //$ins = $hasApprovedDTRP->where('timeEnd',null);
          //$outs = $hasApprovedDTRP->where('timeStart',null);

         /* if ($logType_id == 1) //LOG IN
          {
            $userLog = $ins->first();

          } else { $userLog =  $outs->first(); }*/
          $userLog = $hasApprovedDTRP->first();


        }
        else{

          //check mo muna ung 12AM issues
          if ($schedForToday['timeStart'] == "00:00:00")
            $userLog = Logs::where('user_id',$id)->where('biometrics_id',$biometrics_id)->where('logType_id',$logType_id)->orderBy('biometrics_id','ASC')->get();
          else
            $userLog = Logs::where('user_id',$id)->where('biometrics_id',$problemArea[0]['biometrics_id'])->where('logType_id',$logType_id)->orderBy('biometrics_id','ASC')->get();

        }
      
       // else
        //  $userLog = Logs::where('user_id',$id)->where('biometrics_id',$biometrics_id)->where('logType_id',$logType_id)->orderBy('biometrics_id','ASC')->get();


      } else
      {
          //***** CHECK FIRST IF THERE'S AN APPROVED DTRP,
          //***** ELSE USE RAW BIOMETRICS
         

          //*** new check: pag logout eh less than 9AM, meaning login sya kahapon -- bio is for yesterday
          
         /* if ( $logType_id==2 && date('H:i:s', strtotime($schedForToday['timeEnd']) <= date('H:i:s', strtotime("09:00:00")) ))
          {
            $kahapon = Biometrics::find($biometrics_id);
            $dateKahapon = Carbon::parse($kahapon->productionDate)->subDay();
            $bioKahapon = Biometrics::where('productionDate',$dateKahapon)->get();

            $hasApprovedDTRP = User_DTRP::where('user_id',$id)->where('isApproved',true)->where('biometrics_id',$bioKahapon->first()->id)->where('logType_id',$logType_id)->orderBy('updated_at','DESC')->get();

            $pendingDTRP = User_DTRP::where('user_id',$id)->where('biometrics_id',$bioKahapon->first()->id)->where('logType_id',$logType_id)->where('isApproved',null)->orderBy('id','DESC')->get();
          } else
          { */
             $hasApprovedDTRP = User_DTRP::where('user_id',$id)->where('isApproved',true)->where('biometrics_id',$biometrics_id)->where('logType_id',$logType_id)->orderBy('updated_at','DESC')->get();

              $pendingDTRP = User_DTRP::where('user_id',$id)->where('biometrics_id',$biometrics_id)->where('logType_id',$logType_id)->where('isApproved',null)->orderBy('id','DESC')->get();
          //}


          ( count($pendingDTRP) > 0  ) ? $hasPendingDTRP=true : $hasPendingDTRP=false;
                        
          

          if(count($hasApprovedDTRP) > 0){ 

              $userLog = $hasApprovedDTRP;

          } else 
              $userLog = Logs::where('user_id',$id)->where('biometrics_id',$biometrics_id)->where('logType_id',$logType_id)->orderBy('biometrics_id','ASC')->get();

          

      } 

      if (is_null($userLog) || count($userLog)<1)
      {  
        $link = action('LogsController@viewRawBiometricsData',$id);
         //$icons = "<a title=\"Verify Biometrics data\" class=\"pull-right text-danger\" target=\"_blank\" style=\"font-size:1.2em;\" href=\"".$link."\"><i class=\"fa fa-clock-o\"></i></a>";
         $icons = "<a title=\"Verify Biometrics data\" class=\"pull-right text-gray\" target=\"_blank\" style=\"font-size:1.2em;\" href=\"$link#$biometrics_id\"><i class=\"fa fa-clock-o\"></i></a>";
          
          
         if ($hasHolidayToday)
         {
          $log = "<strong class=\"text-danger\">N/A</strong>". $icons;
          $workedHours = $holidayToday->first()->name;

         } else 
         {

          if($logType_id == 1) $log =  "<strong class=\"text-danger\">No IN</strong>". $icons;
          else if ($logType_id == 2)$log = "<strong class=\"text-danger\">No OUT</strong>". $icons;
          $workedHours = "N/A";

         }
          
          $timing=Carbon::parse('22:22:22');
          $UT = $undertime;
      } 
      else
      {
         $log = date('h:i:s A',strtotime($userLog->first()->logTime));

         $timing =  Carbon::parse($userLog->first()->logTime, "Asia/Manila");
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

          
          //*********** APPLICABLE ONLY TO WORK DAY ********************//

      }//end if may login 

       $data->push(['logs'=>$userLog, 
                    'UT'=>$UT, 'logTxt'=>$log,
                    'hasPendingDTRP' => $hasPendingDTRP,
                    'pendingDTRP' => $pendingDTRP, 
                    'dtrpIN'=>$dtrpIN, 'dtrpIN_id'=>$dtrpIN_id, 
                    'dtrpOUT'=>$dtrpOUT, 'dtrpOUT_id'=> $dtrpOUT_id,
                    'timing'=>$timing,
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

      if (count($hasApprovedDTRPin) > 0)
      {
        $userLogIN = $hasApprovedDTRPin;

      } else{
        
        $userLogIN = Logs::where('user_id',$user_id)->where('biometrics_id',$biometrics->id)->where('logType_id',1)->orderBy('biometrics_id','ASC')->get();
       
      }

       if (count($userLogIN) == 0)
       {  

          //--- ** baka naman may DTRP syang di pa approved? 
        $pendingDTRPin = User_DTRP::where('user_id',$user_id)->where('isApproved',null)->where('biometrics_id',$biometrics->id)->where('logType_id',1)->orderBy('updated_at','DESC')->get();

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

       } else
       {
          $logIN = date('h:i A',strtotime($userLogIN->first()->logTime));
          $timeStart = Carbon::parse($userLogIN->first()->logTime);

          if ($isSameDayLog) 
          {
            //Check mo muna kung may approved DTRPout
             $hasApprovedDTRPout = User_DTRP::where('user_id',$user_id)->where('isApproved',true)->where('biometrics_id',$biometrics->id)->where('logType_id',2)->orderBy('updated_at','DESC')->get();

             if (count($hasApprovedDTRPout) > 0)
             {

              $userLogOUT = $hasApprovedDTRPout;

             } else 
             {

              //--- ** baka naman may DTRP syang di pa approved? 
                $pendingDTRPout = User_DTRP::where('user_id',$user_id)->where('isApproved',null)->where('biometrics_id',$biometrics->id)->where('logType_id',2)->orderBy('updated_at','DESC')->get();

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
                $wh = $timeEnd->diffInMinutes($timeStart); //--- pag RD OT, no need to add breaktime 1HR
                $workedHours = number_format($wh/60,2);
                if ($hasHolidayToday)
                {
                  
                  $workedHours .= "<br /><strong>* " . $holidayToday->first()->name . " * </strong>";

                } 


                 if ($hasHolidayToday)
                 $icons = "<a  data-toggle=\"modal\" data-target=\"#myModal_OT".$payday."\"  title=\"File this OT\" class=\"pull-right\" style=\"font-size:1.2em;\" href=\"#\"><i class=\"fa fa-credit-card\"></i></a>";
               else
                $icons = "<a  data-toggle=\"modal\" data-target=\"#myModal_OT".$payday."\"  title=\"File this RD-OT\" class=\"pull-right\" style=\"font-size:1.2em;\" href=\"#\"><i class=\"fa fa-credit-card\"></i></a>";
               

                $billableForOT = $workedHours;
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
    //$thisPayrollDate = Biometrics::where(find($biometrics->id)->productionDate;
    $holidayToday = Holiday::where('holidate', $payday)->get();

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

          if ($userLogIN[0]['timing']->format('H:i:s') > $schedForToday['timeStart'])
          {
            $checkLate = $userLogIN[0]['timing']->diffInMinutes(Carbon::parse($schedForToday['timeStart'], "Asia/Manila"));
            //---- MARKETING TEAM CHECK: 15mins grace period
              
              if( $campName == "Marketing" || $campName == "Lebua")
              {
                 if ($checkLate > 15) $isLateIN = true; else $isLateIN= false;

              } else
              {
                 if ($checkLate > 1) $isLateIN = true; else $isLateIN= false;
              }

            
          } else $isLateIN= false;


          if ($userLogOUT[0]['timing']->format('H:i:s') < $schedForToday['timeEnd'])
          {
            $checkEarlyOut = $userLogOUT[0]['timing']->diffInMinutes(Carbon::parse($schedForToday['timeEnd'],"Asia/Manila"));
            //---- MARKETING TEAM CHECK: 15mins grace period
              if( $campName == "Marketing" || $campName == "Lebua")
              {
                 if ($checkEarlyOut > 15) $isEarlyOUT = true; else $isEarlyOUT= false;

              } else
              {
                 if ($checkEarlyOut > 1) $isEarlyOUT = true; else $isEarlyOUT= false;
              }

            
          } else $isEarlyOUT= false;

        

          if ($isEarlyOUT && $isLateIN)//use user's logs
          {

            $wh = Carbon::parse($userLogOUT[0]['timing'])->diffInMinutes(Carbon::parse($userLogOUT[0]['timing'],"Asia/Manila")->addHour());
            $workedHours = number_format($wh/60,2);
            $billableForOT=0; //$userLogIN[0]['timing']/60;
            $UT = 8.0 - $wh;
            

          }
          else if ($isEarlyOUT){

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
              $wh = Carbon::parse($userLogOUT[0]['timing'],"Asia/Manila")->diffInMinutes(Carbon::parse($schedForToday['timeStart'],"Asia/Manila")->addHour());
              $workedHours = number_format($wh/60,2)."<br/><small>(early OUT)</small>";$billableForOT=0;
               if ($hasHolidayToday)
                  {
                    $workedHours .= "<br/> <strong>* ". $holidayToday->first()->name. " *</strong>";
                  }

            }

            $UT = 8.0 - $wh;

            
          }
          else if ($isLateIN){

            //--- but u need to make sure if nag late out sya
            if (Carbon::parse($userLogOUT[0]['timing'],"Asia/Manila") > Carbon::parse($schedForToday['timeEnd'],"Asia/Manila") )
            {
             //$wh = Carbon::parse($schedForToday['timeEnd'],"Asia/Manila")->diffInMinutes(Carbon::parse($userLogOUT[0]['timing'],"Asia/Manila")->addMinutes(60));
               $wh = Carbon::parse($schedForToday['timeEnd'],"Asia/Manila")->diffInMinutes(Carbon::parse($userLogIN[0]['timing'],"Asia/Manila")->addMinutes(60));
              $workedHours = number_format($wh/60,2)."<br/><small>(Late IN)</small>";$billableForOT=0;
              if ($hasHolidayToday)
                  {
                    $workedHours .= "<br/> <strong>* ". $holidayToday->first()->name. " *</strong>";
                  }
              
              $icons = "<a  data-toggle=\"modal\" data-target=\"#myModal_OT".$payday."\"   title=\"File this OT\" class=\"pull-right\" style=\"font-size:1.2em;\" href=\"#\"><i class=\"fa fa-credit-card\"></i></a>";
               $totalbill = number_format((Carbon::parse($shiftEnd,"Asia/Manila")->diffInMinutes(Carbon::parse($userLogOUT[0]['timing'],"Asia/Manila") ))/60,2);
              $totalbill = number_format((Carbon::parse($shiftEnd,"Asia/Manila")->diffInMinutes(Carbon::parse($userLogOUT[0]['timing'],"Asia/Manila") ))/60,2);

              if ($totalbill > 0.5)
              {
                $billableForOT = $totalbill;
                $OTattribute = $icons;
              }
                
              else { $billableForOT = 0; /*$totalbill*/; $OTattribute = "&nbsp;&nbsp;&nbsp;";} 

              $UT = 8.0 - $wh;

            }
            else
            {
              //$wh = Carbon::parse($userLogOUT[0]['timing'],"Asia/Manila")->diffInMinutes(Carbon::parse($userLogOUT[0]['timing'],"Asia/Manila")->addMinutes(60));
               $wh = Carbon::parse($schedForToday['timeEnd'],"Asia/Manila")->diffInMinutes(Carbon::parse($userLogIN[0]['timing'],"Asia/Manila")->addMinutes(60));
              $workedHours = number_format($wh/60,2)."<br/><small>(Late IN)</small>";$billableForOT=0;
              if ($hasHolidayToday)
                  {
                    $workedHours .= "<br/> <strong>* ". $holidayToday->first()->name. " *</strong>";
                  }

            }
            
          }
          else {

             $wh = Carbon::parse($userLogOUT[0]['timing'],"Asia/Manila")->diffInMinutes(Carbon::parse($schedForToday['timeStart'],"Asia/Manila")->addHour());

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
          if ($hasHolidayToday)
          {
            $workedHours = "(8.0)<br/> <strong>* " . $holidayToday->first()->name . " *</strong>";
          } 

          else
            $workedHours = "<a title=\"Check your Biometrics data. \n It's possible that you pressed a wrong button, or you really didn't log in / out.\"><strong class=\"text-danger\">AWOL </strong></a>";
        }

        
        $data->push(['checkLate'=>"nonComplicated", 'workedHours'=>$workedHours, 
                      'billableForOT'=>$billableForOT, 'OTattribute'=>$OTattribute,
                      'UT'=>$UT ]);

        return $data;


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
                    $login = new Carbon(Input::get('login'), "Asia/Manila"); 
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

 
  public function saveCWS($request)
  {
    $roles = UserType::find($this->user->userType_id)->roles->pluck('label'); 
    $canChangeSched =  ($roles->contains('CHANGE_EMPLOYEE_SCHEDULE')) ? '1':'0'; 

      $cws = new User_CWS;
      $cws->user_id = $request->user_id;
      $cws->biometrics_id = $request->biometrics_id;

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
       
      


      if (!empty($request->TLapprover) && $canChangeSched)
      {
        $cws->isApproved = true; $TLsubmitted=true; $cws->approver = $request->TLapprover;
      } else { $cws->isApproved = null; $TLsubmitted=false; $cws->approver=null; }

      
      $cws->save();

      //--- notify the TL concerned
      $employee = User::find($cws->user_id);

      if (!$TLsubmitted && !$canChangeSched)
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
            fwrite($file, "-------------------\n". $employee->id .",". $employee->lastname." CWS submission ". date('M d h:i:s'). " by ". $this->user->firstname.", ".$this->user->lastname."\n");
            fclose($file);

      //return redirect()->back();
      //return redirect()->action('DTRController@show', $cws->user_id);
    

  }



}//end trait











                                


?>