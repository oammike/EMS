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
use OAMPI_Eval\User_Familyleave;
use OAMPI_Eval\User_LWOP;
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


  // public function checkVLcredits($user)
  // {
  //   $avail = $user->availableVL;
  //   return $avail;

  // }


  public function generateShifts($timeFormat, $shiftType)
  {
    $data = array();

    switch ($shiftType) {
      case 'full': $addHr = 9;break; 
      case 'part': $addHr = 4; break;
    }

    for( $i = 1; $i <= 24; $i++)
        { 
          $time1 = Carbon::parse('1999-01-01 '.$i.':00:00');
          $time2 = Carbon::parse('1999-01-01 '.$i.':30:00');

          if($timeFormat == '12H')
          {
            array_push($data, $time1->format('h:i A')." - ".$time1->addHours($addHr)->format('h:i A'));
            array_push($data, $time2->format('h:i A')." - ".$time2->addHours($addHr)->format('h:i A'));

          } else
          {
            array_push($data, $time1->format('H:i')." - ".$time1->addHours($addHr)->format('H:i'));
            array_push($data, $time2->format('H:i')." - ".$time2->addHours($addHr)->format('H:i'));
          }
        }

    
    return $data;


  }

  public function getActualSchedForToday($user,$id,$payday,$bioForTheDay, $hybridSched,$isFixedSched,$hybridSched_WS_fixed,$hybridSched_WS_monthly, $hybridSched_RD_fixed, $hybridSched_RD_monthly, $workSched, $RDsched, $approvedCWS)
  {
    $carbonPayday = Carbon::parse($payday);
    ( count($approvedCWS) > 0 ) ? $hasApprovedCWS=true : $hasApprovedCWS=false;
    $daysOfWeek = array('Mon','Tue','Wed','Thu','Fri','Sat','Sun');

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
        if($check_fixed_WS['workday'] !== null)
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

              if( Carbon::parse($check_monthly_WS->first()->created_at,'Asia/Manila')->format('Y-m-d H:i:s') > Carbon::parse($check_fixed_WS->first()->created_at,'Asia/Manila')->format('Y-m-d H:i:s')  ) //mas bago si Monthly
              {

                $workSched = $hybridSched_WS_monthly;

                (count($hybridSched_RD_monthly) > 0) ? $RDsched = $hybridSched_RD_monthly : $RDsched = $hybridSched_RD_fixed;
                $isFixedSched = false;
                $noWorkSched =false;



              }
              else //check mo muna validity nung WS na fixed. If no effectivity, then NO SCHED
              {
                if ((Carbon::parse($check_fixed_WS->first()->schedEffectivity)->startOfDay() <= $carbonPayday->startOfDay()) || $check_fixed_WS->first()->schedEffectivity == null)
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

              if ((Carbon::parse($check_fixed_WS->first()->schedEffectivity)->startOfDay() <= $carbonPayday->startOfDay()) || $check_fixed_WS->first()->schedEffectivity == null)
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
          


        } else //baka RD
        {
          //$check_fixed_RD = $hybridSched_RD_fixed->where('workday',$dayToday)->sortByDesc('created_at');
          $check_fixed_RD = $this->getLatestFixedSchedGrouped($hybridSched_RD_fixed,$payday,$dayToday);

          //if (count($check_fixed_RD) > 0) //if may fixed RD, check mo kung ano mas updated vs monthly sched
          if ($check_fixed_RD['workday'] !== null)
          {
            $stat =  "if may fixed RD, check mo kung ano mas updated vs monthly sched";

            $check_monthly_RD = $hybridSched_RD_monthly->where('productionDate',$payday)->sortByDesc('created_at');

            if (count($check_monthly_RD) > 0) // if may monthly, compare it vs fixed
            {
              if( Carbon::parse($check_monthly_RD->first()->created_at,'Asia/Manila')->format('Y-m-d H:i:s') > Carbon::parse($check_fixed_RD->first()->created_at,'Asia/Manila')->format('Y-m-d H:i:s') ) //mas bago si Monthly
              {
                $workSched = $hybridSched_WS_monthly;
                $RDsched = $hybridSched_RD_monthly;
                $isFixedSched = false;
                $noWorkSched = false;

              }
              else //FIXED RD SYA
              {
                //check mo muna validity nung RD na fixed. If not effective, then NO SCHED

                 if ((Carbon::parse($check_fixed_RD->first()->schedEffectivity)->startOfDay() <= $carbonPayday->startOfDay()) || $check_fixed_RD->first()->schedEffectivity == null)
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
                if(Carbon::parse($check_monthly_WS->first()->created_at,'Asia/Manila')->format('Y-m-d H:i:s') > Carbon::parse($check_fixed_RD->first()->created_at,'Asia/Manila')->format('Y-m-d H:i:s')  )
                {
                  $workSched = $hybridSched_WS_monthly;
                  $RDsched = $hybridSched_RD_monthly;
                  $isFixedSched = false;
                  $noWorkSched =false;
                }
                else
                  {
                    //check mo muna validity nung RD na fixed. If not effective, then NO SCHED

                     if ((Carbon::parse($check_fixed_RD->first()->schedEffectivity)->startOfDay() <= $carbonPayday->startOfDay()) || $check_fixed_RD->first()->schedEffectivity == null)
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

                 if ((Carbon::parse($check_fixed_RD->first()->schedEffectivity)->startOfDay() <= $carbonPayday->startOfDay()) || $check_fixed_RD->first()->schedEffectivity == null)
                 {
                    $workSched = $hybridSched_WS_fixed;
                    $RDsched = $hybridSched_RD_fixed;
                    $isFixedSched = true;
                    $noWorkSched = false;
                 }
                 else
                 {
                  //$noWorkSched = true;$workSched = null;$RDsched = null;$isFixedSched = false; $hasCWS=false;
                   $workSched = $hybridSched_WS_fixed;
                    $RDsched = $hybridSched_RD_fixed;
                    $isFixedSched = true;
                    $noWorkSched = false; $hasCWS=false;
                 }


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
        /*
          $ws =$this->getLatestFixedSchedGrouped($workSched,$payday,$numDay);
          if ( is_null($ws->workday))
          {
            //di sya worksched, but CWS from RD
            $ws =$this->getLatestFixedSchedGrouped($RDsched,$payday,$numDay);

          }else
          {
            if ($ws->created_at > $approvedCWS->first()->updated_at )
            {
              $schedForToday = $ws; //$workSched->where('workday',$numDay)->first();
              $isRDToday = $ws->isRD;
              $RDsched1 = $RDsched;
              

            } else
            {
              $schedForToday = array('timeStart'=>$approvedCWS->first()->timeStart, 
                                    'timeEnd'=> $approvedCWS->first()->timeEnd,
                                    'isFlexitime' => false,
                                    'isRD'=> $approvedCWS->first()->isRD);
              $isRDToday = $approvedCWS->first()->isRD;
              $RDsched1 = $RDsched; //$this->getLatestFixedSchedGrouped($RDsched,$payday,$numDay);
              

            } 

          }*/

          
          
    

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

        if ( count($workSched->where('productionDate',$payday)->all()) > 0 )
        {

           if ($workSched->where('productionDate',$payday)->sortByDesc('id')->first()->created_at > $approvedCWS->first()->updated_at )
            {
              $schedForToday = $workSched->where('productionDate',$payday)->sortByDesc('id')->first();
              $isRDToday = $workSched->where('productionDate',$payday)->sortByDesc('id')->first()->isRD;
              $RDsched1 = $RDsched;

            }else 
            {

              // $schedForToday = array('timeStart'=>$approvedCWS->first()->timeStart, 
              //                   'timeEnd'=> $approvedCWS->first()->timeEnd, 
              //                   'isFlexitime'=>false,
              //                   'isRD'=>$approvedCWS->first()->isRD);

              // $isRDToday = $approvedCWS->first()->isRD;
              // $RDsched1 = $RDsched;

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



            }
            

        } else 
        {
          // $schedForToday = array('timeStart'=>$approvedCWS->first()->timeStart, 
          //                       'timeEnd'=> $approvedCWS->first()->timeEnd, 
          //                       'isFlexitime'=>false,
          //                       'isRD'=>$workSched->where('productionDate',$payday)->first()->isRD);
          // $isRDToday = $workSched->where('productionDate',$payday)->first()->isRD;
          // $RDsched1 = $RDsched;

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


        }

      } else //walang CWS
      {
        if (is_null($workSched)){
          /*$day = date('D', strtotime($payday)); //--- get his worksched and RDsched
          $theday = (string)$day;
          $numDay = array_search($theday, $daysOfWeek);
          $schedForToday = $this->getLatestFixedSchedGrouped($workSched,$payday,$numDay);*/
          $schedForToday = null;
          $isRDToday = null; //$schedForToday['isRD'];
          $RDsched1 = $RDsched;
        }else
        {
          // know first kung anong meron, RD or workday
          $rd = $RDsched->where('productionDate',$payday)->sortByDesc('id');
          $wd = $workSched->where('productionDate',$payday)->sortByDesc('id');
          if (count($rd) > 0 )
          {
            $schedForToday = $rd->first();
            $isRDToday = true;
            $RDsched1 = $RDsched;

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

    $c = new Collection;
    $c->schedForToday =  collect($schedForToday)->toArray();
    $c->isRDToday = $isRDToday;
    $c->RDsched = $RDsched1;
    $c->isFixedSched = $isFixedSched;
    $c->allRD = $RDsched;
    
    return $c;


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
            $workedHours1 = $this->processLeaves('VL',true,$wh,$vlDeet,$hasPendingVL,$icons,$userLogIN[0],$userLogOUT[0],$shiftEnd);
            $workedHours .= $workedHours1[0]['workedHours'];
            $UT = $workedHours1[0]['UT'];
          }


          if ($hasOBT)
          {
            $workedHours1 = $this->processLeaves('OBT',true,$wh,$obtDeet,$hasPendingOBT,$icons,$userLogIN[0],$userLogOUT[0],$shiftEnd);
            $workedHours .= $workedHours1[0]['workedHours'];
            $UT = $workedHours1[0]['UT'];
          }



          if ($hasSL)
          {
            $workedHours1 = $this->processLeaves('SL',true,$wh,$slDeet,$hasPendingSL,$icons,$userLogIN[0],$userLogOUT[0],$shiftEnd);
            $workedHours .= $workedHours1[0]['workedHours'];
            $UT = $workedHours1[0]['UT'];
          }


          if ($hasFL)
          {
            $workedHours1 = $this->processLeaves('FL',false,0,$flDeet,$hasPendingFL,$icons,$userLogIN[0],$userLogOUT[0],$shiftEnd);
              $workedHours .= $workedHours1[0]['workedHours'];
              $UT = $workedHours1[0]['UT'];
          }


          if ($hasLWOP)
          {
            $workedHours1 = $this->processLeaves('LWOP',true,$wh,$lwopDeet,$hasPendingLWOP,$icons,$userLogIN[0],$userLogOUT[0],$shiftEnd);
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
    

    $cutoffStart = new Carbon($currentPeriod[0],'Asia/Manila'); //(Cutoff::first()->startingPeriod());
    $cutoffEnd = new Carbon($currentPeriod[1],'Asia/Manila'); //(Cutoff::first()->endingPeriod());
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

    return collect(['currentPeriod'=>$currentPeriod, 'cutoffStart'=>$cutoffStart,'cutoffEnd'=>$cutoffEnd,'cutoffID'=>$cutoffID]);
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

    if (count($workSched) > 0)
      foreach ($workSched as $w) {
      
        if( $w->first()->schedEffectivity <= $payday || is_null($w->first()->schedEffectivity))
        {
          $thesched = collect($w)->where('workday',$numDay)->first();
          break;
        }
      }

    if (is_null($thesched))
    {
      $sched = ['timeStart'=>null, 'timeEnd'=>null,'isFlexitime'=>false,'isRD'=>true, 'workday'=>null ];
      // *** null meaning either wala talga or di pa effective yung sched

    } else $sched = $thesched;

    return $sched;




  }




  public function getLogDetails($type, $id, $biometrics_id, $logType_id, $schedForToday, $undertime, $problemArea, $isAproblemShift, $isRDYest)
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
    $hasFL = null; $flDetails = new Collection; $hasPendingFL=false;
    $pendingDTRP = null; 
    $UT=null;$log=null;$timing=null; $pal = null;$maxIn=null;$beginShift=null; $finishShift=null;
    $logPalugit=null;
    $palugitDate=null;$maxOut=null; $checker=null;$theNextday=null;
    //$userLog=null;

    
    $theDay = Carbon::parse($thisPayrollDate." ".$schedForToday['timeStart'],"Asia/Manila");
    $fix= Carbon::parse($thisPayrollDate." 23:59:00","Asia/Manila");

    $employee = User::find($id);

    ($employee->status_id == 12 || $employee->status_id == 14 ) ? $isPartTimer = true : $isPartTimer=false;



  

     /*------ WE CHECK FIRST IF THERE'S AN APPROVED VL | SL | LWOP -----*/
    $vl = User_VL::where('user_id',$id)->where('leaveStart','<=',$fix->format('Y-m-d H:i:s'))->where('leaveEnd','>=',$theDay->format('Y-m-d H:i:s'))->orderBy('created_at','DESC')->get();

    $sl = User_SL::where('user_id',$id)->where('leaveStart','<=',$fix->format('Y-m-d H:i:s'))->where('leaveEnd','>=',$theDay->format('Y-m-d H:i:s'))->orderBy('created_at','DESC')->get();

    $lwop = User_LWOP::where('user_id',$id)->where('leaveStart','<=',$fix->format('Y-m-d H:i:s'))->where('leaveEnd','>=',$theDay->format('Y-m-d H:i:s'))->orderBy('created_at','DESC')->get();

    $obt = User_OBT::where('user_id',$id)->where('leaveStart','<=',$fix->format('Y-m-d H:i:s'))->where('leaveEnd','>=',$theDay->format('Y-m-d H:i:s'))->orderBy('created_at','DESC')->get();

    $fl = User_Familyleave::where('user_id',$id)->where('leaveStart','<=',$fix->format('Y-m-d H:i:s'))->where('leaveEnd','>=',$theDay->format('Y-m-d H:i:s'))->orderBy('created_at','DESC')->get();



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
      $hasVL=true; $hasLeave = true;
      $vlDeet= $vl->first();
      (!is_null($vlDeet->isApproved)) ? $hasPendingVL=false : $hasPendingVL=true;

    }else{

      $hasVL = false;
      $vlDeet = null;

    }


    /*-------- OBT LEAVE  -----------*/
    if (count($obt) > 0) 
    {
      $hasOBT=true; $hasLeave = true; $hasLeave = true;
      $obtDeet= $obt->first();
      (!is_null($obtDeet->isApproved)) ? $hasPendingOBT=false : $hasPendingOBT=true;

    }else{

      $hasOBT = false;
      $obtDeet = null;

    }


    /*-------- SICK LEAVE  -----------*/
    if (count($sl) > 0) 
    {
      $hasSL=true; $hasLeave = true;
      $slDeet= $sl->first();
      (!is_null($slDeet->isApproved)) ? $hasPendingSL=false : $hasPendingSL=true;

    }else{

      $hasSL = false;
      $slDeet = null;

    }

    /*-------- FAMILY LEAVE  -----------*/
    if (count($fl) > 0) 
    {
      $hasFL=true; $hasLeave = true;
      $flDeet= $fl->first();
      (!is_null($flDeet->isApproved)) ? $hasPendingFL=false : $hasPendingFL=true;

    }else{

      $hasFL = false;
      $flDeet = null;

    }





    if (count($holidayToday) > 0) $hasHolidayToday = true;

    $hasApprovedDTRP = User_DTRP::where('user_id',$id)->where('isApproved',true)->where('biometrics_id',$biometrics_id)->where('logType_id',$logType_id)->orderBy('updated_at','DESC')->get();

    $pendingDTRP = User_DTRP::where('user_id',$id)->where('biometrics_id',$biometrics_id)->where('logType_id',$logType_id)->where('isApproved',null)->orderBy('id','DESC')->get();

    ( count($pendingDTRP) > 0  ) ? $hasPendingDTRP=true : $hasPendingDTRP=false;

    //$beginShift = Carbon::parse($thisPayrollDate." ".$schedForToday['timeStart'],"Asia/Manila")->format('Y-m-d H:i:s');
    $beginShift = Carbon::parse($thisPayrollDate." ".$schedForToday['timeStart'],"Asia/Manila");

    ($isPartTimer) ? $endShift = Carbon::parse($thisPayrollDate." ".$schedForToday['timeStart'],"Asia/Manila")->addHour(5) : $endShift = Carbon::parse($thisPayrollDate." ".$schedForToday['timeStart'],"Asia/Manila")->addHour(9);

    ($beginShift->format('Y-m-d') == $endShift->format('Y-m-d')) ? $sameDayShift = true : $sameDayShift=false;
    

    if(count($hasApprovedDTRP) > 0){ $userLog = $hasApprovedDTRP; } 
    else 
    {

              //fix for robert's case sa logout
              if ($logType_id== 2)
              {

                //kunin mo yung bio id ng log 9HRs from shiftstart or 5hrs if parttime
                (!$isPartTimer) ? $bEnd = Carbon::parse($thisPayrollDate." ".$schedForToday['timeStart'],"Asia/Manila")->addHour(9) : $bEnd = Carbon::parse($thisPayrollDate." ".$schedForToday['timeStart'],"Asia/Manila")->addHour(5);
                  

                $bioEnd = Biometrics::where('productionDate',$bEnd->format('Y-m-d'))->get();

                if (count($bioEnd) > 0)
                {
                  $userLog = Logs::where('user_id',$id)->where('biometrics_id',$bioEnd->first()->id)->where('logType_id',$logType_id)->orderBy('biometrics_id','ASC')->get();

                }else goto proceedWithBlank;

              }else if($logType_id == 1)// && $isAproblemShift
              //*** new fix for issues with LOGIN
              //*** we need to check its grouped LogINS and if log is within maxIN (4hrs) and maxLate (2nd shift) or shift +5hrs
              {
                $checker="else if in sya";
                //$userLog=null;
                /*if ($isRDYest)
                {*/
                  //** kung RD nya kahapon, eh di for today dapat log nya
                  //** pero check mo muna kung pang 12MN - 4am sched sya, so may posssibility kahapon pa sya naglog
                  //** max allowed time in is +-4hrs
                  $probTime1 = Carbon::parse($thisPayrollDate." 00:00:00","Asia/Manila")->format('Y-m-d H:i:s');
                  $probTime2 = Carbon::parse($thisPayrollDate." 04:00:00","Asia/Manila")->format('Y-m-d H:i:s');
                  
                  $today = Biometrics::where('productionDate',$thisPayrollDate)->get();


                  //$beginShift = Carbon::parse($thisPayrollDate." ".$schedForToday['timeStart'],"Asia/Manila")->format('Y-m-d H:i:s');
                  $maxIn = Carbon::parse($thisPayrollDate." ".$schedForToday['timeStart'],"Asia/Manila")->subHour(6)->format('Y-m-d H:i:s');
                
                  $col = [];

                  //array_push($col, ['l'=>$l->format('Y-m-d H:i:s'), 'b'=> $beginShift->format('Y-m-d H:i:s'), 'm'=>$maxO->format('Y-m-d H:i:s') ]);
                  if ($beginShift->format('Y-m-d H:i:s') >= $probTime1 && $beginShift->format('Y-m-d H:i:s') <= $probTime2)
                  {
                    /*-- check for logs within 6hr grace period for problem shifts --*/

                    //-- FIRST: get yung from yesterday
                                // pag wala pa rin, 
                                // Get from TOmmorrow ---
                                // except kung 12Mn start, sure na  kahapon yun

                    if ($beginShift->format('Y-m-d H:i:s') == Carbon::parse($thisPayrollDate)->startOfDay()->format('Y-m-d H:i:s')){
                      $yest = Carbon::parse($thisPayrollDate)->subDay(1);
                      $bioYest = Biometrics::where('productionDate',$yest->format('Y-m-d'))->get();
                    }else{
                      $yest = Carbon::parse($thisPayrollDate);
                      $bioYest = Biometrics::where('productionDate',$yest->format('Y-m-d'))->get();

                    }
                    
                    

                    if (count($bioYest) > 0)
                    {

                      $logsKahapon = Logs::where('user_id',$id)->where('biometrics_id',$bioYest->first()->id)->where('logType_id',$logType_id)->orderBy('biometrics_id','ASC')->get();

                     

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
                         

                          

                          
                          
                        } else goto checkTomorrowLogs; 

                    

                    }//end if may bioYest
                    else
                    {

                      
                      //tomorrow in this case is maxLate IN
                      $tommorow = Carbon::parse($thisPayrollDate." ".$schedForToday['timeStart'],"Asia/Manila")->addHour(4);
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
             
          

      //} 


      /*--- after getting the logs, IF (logIN_type) go to another filter pass
            else, just proceed -- */

     $tommorow = Carbon::parse($thisPayrollDate)->addDay();
            
     $probTime1 = Carbon::parse($thisPayrollDate." 00:00:00","Asia/Manila")->format('Y-m-d H:i:s');
     $probTime2 = Carbon::parse($thisPayrollDate." 03:00:00","Asia/Manila")->format('Y-m-d H:i:s');

     //$beginShift = Carbon::parse($thisPayrollDate." ".$schedForToday['timeStart'],"Asia/Manila")->format('Y-m-d H:i:s');

     $checker = null; //['userLg'=>$userLog];
      if (is_null($userLog) || count($userLog)<1 )
      {  

        /* ------------ THIS IS WHERE WE CHECK FOR THOSE PROBLEM AREAS ---------
                                  perform this only for LOG INS                         */


          if ($logType_id == 1){

                
                
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

                  } //end else if (count($logsKahapon) > 0) 
 
                  

                } //end ($beginShift >= $probTime1 && $beginShift <= $probTime2) 

          /* ------------ END THIS IS WHERE WE CHECK FOR THOSE PROBLEM AREAS : LOG IN ---------*/       
          } else if($logType_id == 2)
          {
            // if  12MN < beginShift > 3AM
             $checker = ['enter'=>"12MN < beginShift > 3AM", 'beginShift'=>$beginShift->format('Y-m-d H:i:s'),'$probTime1'=>$probTime1,'$probTime2'=>$probTime2 ];
            /*if (($beginShift->format('Y-m-d H:i:s') >= $probTime1) && ($beginShift->format('Y-m-d H:i:s') <= $probTime2)) // if shift is NOT within the day
            {
                  //-- check for logs within 6hr grace period for problem shifts --

                  proceedToLogTomorrow:
                  
                  
                  $bioForTom = Biometrics::where('productionDate',$tommorow->format('Y-m-d'))->get();

                  if (count($bioForTom) > 0){
                    //$finishShift = $endShift; // Carbon::parse($thisPayrollDate." ".$schedForToday['timeStart'],"Asia/Manila")->addHour(9);// 


                      if (!$isAproblemShift)
                        $logPalugit = Logs::where('user_id',$id)->where('biometrics_id',$biometrics_id)->where('logType_id',$logType_id)->orderBy('biometrics_id','ASC')->get();
                      
                      else
                        //$logPalugit = Logs::where('user_id',$id)->where('biometrics_id',$biometrics_id)->where('logType_id',$logType_id)->orderBy('biometrics_id','ASC')->get();
                        $logPalugit = Logs::where('user_id',$id)->where('biometrics_id',$bioForTom->first()->id)->where('logType_id',$logType_id)->orderBy('biometrics_id','ASC')->get();

                      $checker = ['enterPalugit'=>$logPalugit];


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
                            $checker="problem shift";
                            goto proceedWithLogs;

                          } else {$checker="from level 4"; goto proceedWithBlank;}

                        }else{

                          $userLog = $logPalugit;
                          //$checker="non problem shif";
                          $checker=['beginShift'=>$beginShift, '12mn'=>$probTime1, '3am'=>$probTime2];
                          goto proceedWithLogs;
                        }
                       

                        
                        
                      } else { $checker="from level3"; goto proceedWithBlank; }

                  } else {$checker="level2"; goto proceedWithBlank;}
                  

            } 
            else if (!$isAproblemShift)
            {
              //check mo muna baka undertime lang

              goto proceedWithBlank;

            } 
            else 
            {*/
              //within the day shift pero walang logs, so baka nag OT sya kinabukasan an yung LogOUT
              //so we need to get logs from tomorrow within the 8hr period
              //$tommorow = Carbon::parse($thisPayrollDate)->addDay();

              proceedToLogTomorrow:

              
              $allowedOT = Carbon::parse($thisPayrollDate." ".$schedForToday['timeStart'],"Asia/Manila")->addHour(17);
              $bioForTom = Biometrics::where('productionDate',$allowedOT->format('Y-m-d'))->get();

              $checker = ['aOT'=>$allowedOT];
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

                  //$checker = ['l'=>$l->format('Y-m-d H:i:s'), 'b'=>$beginShift->format('Y-m-d H:i:s'), 'OT'=>$allowedOT->format('Y-m-d H:i:s') ];
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
                            if (Carbon::parse($parseThis,"Asia/Manila") > $timing ) //&& !$problemArea[0]['problemShift']--- meaning early out sya
                              {
                                $UT  = $undertime + number_format((Carbon::parse($parseThis,"Asia/Manila")->diffInMinutes($timing))/60,2);

                              } else $UT=$undertime;

                          


                  }//end if pasok sa alloted OT
                  else goto proceedWithBlank;

                } else
                {
                  //*** before u assume na blank, check mo muna kung undertime/halfday lang sya
                  //*** look for logout < endShift

                  $checker = "baka undertime lang";
                  
                  $logsToday = Logs::where('user_id',$id)->where('biometrics_id',$biometrics_id)->where('logType_id',$logType_id)->orderBy('created_at','ASC')->get();

                  if (count($logsToday) > 0)
                  {

                    $groupedToday = collect($logsToday)->groupBy('logTime');
                    $u=null;

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

                    if (is_null($u)){$checker="b"; goto proceedWithBlank;} 
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

              }else goto proceedWithBlank;
             
              

              

            /*}*/ 
          /* ------------ END THIS IS WHERE WE CHECK FOR THOSE PROBLEM AREAS ---------*/       
          } else // ---proceed with the usual null logs
          {

                proceedWithBlank:

                               $link = action('LogsController@viewRawBiometricsData',$id);
                               $userLog=null;
                               
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
                                                $parseThis = $schedForToday['timeEnd'];
                                                if (Carbon::parse($parseThis,"Asia/Manila") > $timing ) //&& !$problemArea[0]['problemShift']--- meaning early out sya
                                                  {
                                                    $UT  = $undertime + number_format((Carbon::parse($parseThis,"Asia/Manila")->diffInMinutes($timing))/60,2);

                                                  } else $UT=$undertime;

                                              }
                                              
                                              $checker="non ideal, with $uLog, then proceedToLeaves";
                                              goto proceedToLeaves;

                                      }//end if pasok sa alloted OT
                                      else { $checker=['l'=>$l->format('Y-m-d H:i:s'),'bs'=>$beginShift,'aOT'=>$allowedOT->format('Y-m-d H:i:s')]; goto proceedWithBlank;}

                                    } else {  goto proceedWithBlank;} //$checker=collect($userLog)->groupBy('logTime');
                                    //"non ideal, empty $uLog"; '1'=>Carbon::parse($thisPayrollDate." ".$userLog->first()->logTime,'Asia/Manila')->format('Y-m-d H:i:s'),
                                    //'2'=>$beginShift->format('Y-m-d H:i:s')
                                    //['grouped'=>collect($userLog)->groupBy('logTime')]

                                  }else {$checker="from non ideal, proceed Blank"; goto proceedWithBlank;}

                              }// end if may grouped Logs


                              
                            }//end else ideal situation

                          }//end if not empty userlog

                          

                        }//end if logtype 2 OUT
                        

                         idealSituation:

                          $b= Biometrics::find($userLog->first()->biometrics_id);
                          if($isAproblemShift && $logType_id==2) 
                          {

                            $log = date('M d h:i:s A',strtotime(Carbon::parse($b->productionDate.' '.$userLog->first()->logTime,'Asia/Manila')->format('M d h:i:s A')));
                          } else{

                              ($logType_id==2 && $beginShift->format('Y-m-d') !== $endShift->format('Y-m-d')) ? $log = date('M d h:i:s A',strtotime(Carbon::parse($b->productionDate.' '.$userLog->first()->logTime,'Asia/Manila')->format('M d h:i:s A'))) : $log = date('h:i:s A',strtotime($userLog->first()->logTime));
                          } 




                         $timing = Carbon::parse($b->productionDate." ".$userLog->first()->logTime, "Asia/Manila");
                         

                         //$timing = Carbon::parse(date("M d",strtotime($bioForTom->first()->productionDate))." ". date('h:i:s A',strtotime($userLog->first()->logTime)),'Asia/Manila');
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
        
      } else if($hasLeave && $hasVL)
      {
        ($vlDeet->isApproved) ? $leaveDetails->push(['type'=>"VL",'icon'=>'fa-plane', 'details'=>$vlDeet]) : $leaveDetails->push(['type'=>"VL denied",'icon'=>'fa-times', 'details'=>$vlDeet]);
      }


      /*-------- SICK LEAVE -----------*/
      if ($hasSL && $hasPendingSL)
      {
        $leaveDetails->push(['type'=>"SL for approval",'icon'=>'fa-info-circle', 'details'=>$slDeet]);
        
      } else if($hasLeave && $hasSL)
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
          case 'PL':{$leaveDetails->push(['type'=>"PL for approval",'icon'=>'fa-info-circle', 'details'=>$flDeet]);}break;
          case 'SPL':{$leaveDetails->push(['type'=>"SPL for approval",'icon'=>'fa-info-circle', 'details'=>$flDeet]);}break;
             
        }
        
        
      } else if($hasLeave && $hasFL)
      {
        if ($flDeet->isApproved)
        {
          switch ($flDeet->leaveType) {
            case 'ML':{$leaveDetails->push(['type'=>"ML",'icon'=>'fa-female', 'details'=>$flDeet]);}break;
            case 'PL':{$leaveDetails->push(['type'=>"PL",'icon'=>'fa-male', 'details'=>$flDeet]);}break;
            case 'SPL':{$leaveDetails->push(['type'=>"SPL",'icon'=>'fa-street-view', 'details'=>$flDeet]);}break;       
          
          }

        }else
        {
          switch ($flDeet->leaveType) {
            case 'ML':{$leaveDetails->push(['type'=>"ML denied",'icon'=>'fa-times', 'details'=>$flDeet]);}break;
            case 'PL':{$leaveDetails->push(['type'=>"PL denied",'icon'=>'fa-times', 'details'=>$flDeet]);}break;
            case 'SPL':{$leaveDetails->push(['type'=>"SPL denied",'icon'=>'fa-times', 'details'=>$flDeet]);}break;       
          
          }

        }
        
        
      }



       $data->push(['biometrics_id'=>$biometrics_id, 'logPalugit'=>$logPalugit,
                      'palugitDate' =>$palugitDate,
                       'beginShift'=> $beginShift,
                       'maxOut'=> $maxOut,
                    'leave'=>$leaveDetails, 'hasLeave'=>$hasLeave, 
                    'logs'=>$userLog,'lwop'=>$lwopDetails, 'hasLWOP'=>$hasLWOP, 'hasSL'=>$hasSL,
                    'sl'=>$slDeet,
                    'UT'=>$UT, 'logTxt'=>$log,
                    'hasPendingDTRP' => $hasPendingDTRP,
                    'pendingDTRP' => $pendingDTRP, 
                    'dtrpIN'=>$dtrpIN, 'dtrpIN_id'=>$dtrpIN_id, 
                    'dtrpOUT'=>$dtrpOUT, 'dtrpOUT_id'=> $dtrpOUT_id,
                    'timing'=>$timing, 'pal'=>$pal,'maxIn'=>$maxIn,'beginShift'=>$beginShift,'finishShift'=>$finishShift,
                    'arg1'=>$checker,
                    'isAproblemShift'=>$isAproblemShift,
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
      $pendingDTRPout = null;$userLogOUT=null;$logOUT=null;

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
                $workedHours = "(8.0) <br/><strong> * "; 
                $workedHours .= $holidayToday->first()->name." *</strong>";

              } else $workedHours="N/A"; 

              $UT = 0;
              $billableForOT=0;

      } 
      else
      {
          $logIN = date('h:i:s A',strtotime($userLogIN->first()->logTime));
          $timeStart = Carbon::parse($payday." ".$userLogIN->first()->logTime,'Asia/Manila');

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
                  $userLogIN = Logs::where('user_id',$user_id)->where('biometrics_id',$biometrics->id)->where('logType_id',1)->orderBy('biometrics_id','ASC')->get();
                  $userLogOUT = Logs::where('user_id',$user_id)->where('biometrics_id',$biometrics->id)->where('logType_id',2)->orderBy('biometrics_id','ASC')->get();
                }

             }

             
          } //end if sameday log

          else
          {
            //--- NEXT DAY LOG OUT
            //--- pero i-allow mo lang na maximum of 18H from login time
            //$nextDay = Carbon::parse($payday)->addDay();
            $nextDay = Carbon::parse($payday." ".$userLogIN->first()->logTime,'Asia/Manila')->addHour(18);
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
                  $uLogout = Logs::where('user_id',$user_id)->where('biometrics_id',$bioForTomorrow->id)->where('logType_id',2)->orderBy('biometrics_id','ASC')->get();

                  //** check mo muna kung pasok yung log sa allowed RD OT
                  if (count($uLogout) > 0){

                    if (Carbon::parse($bioForTomorrow->productionDate." ".$uLogout->first()->logTime,"Asia/Manila")->format('Y-m-d H:i:s') <= $nextDay->format('Y-m-d H:i:s'))
                      $userLogOUT = $uLogout;
                    else $userLogOUT = null;

                  }else $userLogOUT = $uLogout;


                }

              
             }

          } //end else not sameday log
            


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

                legitOT:
                        //--- legit OT, compute billable hours
                        //--- check mo muna kung normal or night diff logtype sya
                        //*** pero check mo muna kung may existing userlogs talaga
                        if(count($userLogIN) > 0){
                          
                          if( $userLogOUT->first()->logTime > $userLogIN->first()->logTime)
                          $bio =Biometrics::find($userLogOUT->first()->biometrics_id)->productionDate;
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
                        $timeStart = Carbon::parse($thisPayrollDate." 20:20:20",'Asia/Manila');
                       
                        $timeEnd = Carbon::parse($payday." ".$userLogOUT->first()->logTime, 'Asia/Manila');

                        $wh = $logO->diffInMinutes($timeStart->addHour(1)); //--- pag RD OT, no need to add breaktime 1HR
                        $workedHours = number_format($wh/60,2);
                        $billableForOT = $workedHours;

                        if ($hasHolidayToday)
                        {
                          
                          $workedHours .= "<br /><strong>* " . $holidayToday->first()->name . " * </strong>";

                        } else $workedHours .= "<br /><small> [* RD-OT *] </small>";


                         if ($hasHolidayToday)
                         {
                            //check first if Locked na DTR for that production date
                            $verifiedDTR = User_DTR::where('productionDate',$payday)->where('user_id',$user_id)->get();
                            if (count($verifiedDTR) > 0)
                              $icons = "<a title=\"Unlock DTR to file this HD-OT\" class=\"pull-right text-gray\" style=\"font-size:1.2em;\"><i class=\"fa fa-credit-card\"></i></a>";
                            else
                             $icons = "<a id=\"OT_".$payday."\"  data-toggle=\"modal\" data-target=\"#myModal_OT".$payday."\"  title=\"File this Holiday OT\" class=\"pull-right\" style=\"font-size:1.2em;\" href=\"#\"><i class=\"fa fa-credit-card\"></i></a>";

                         
                         }
                         
                         else
                         {
                            //check first if Locked na DTR for that production date
                              $verifiedDTR = User_DTR::where('productionDate',$payday)->where('user_id',$user_id)->get();
                              if (count($verifiedDTR) > 0)
                                $icons = "<a title=\"Unlock DTR to file this RD-OT\" class=\"pull-right text-gray\" style=\"font-size:1.2em;\"><i class=\"fa fa-credit-card\"></i></a>";
                              else
                               $icons = "<a id=\"OT_".$payday."\"  data-toggle=\"modal\" data-target=\"#myModal_OT".$payday."\"  title=\"File this RD-OT\" class=\"pull-right\" style=\"font-size:1.2em;\" href=\"#\"><i class=\"fa fa-credit-card\"></i></a>";

                            
                         }
                        
                        $OTattribute = $icons;
                        $shiftStart = "* RD *";
                        $shiftEnd = "* RD *";
                        $UT = 0;
              }//end if-else null logout

          }//wala pang approved RD OT 

          



       }//end if may login kahit RD

       $data = new Collection;
       $data->push(['shiftStart'=>$shiftStart, 
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
        'approvedOT'=>$approvedOT]);
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




  public function getWorkedHours($user_id, $userLogIN, $userLogOUT, $schedForToday,$shiftEnd, $payday, $isRDYest)
  {

    $employee = User::find($user_id);

    ($employee->status_id == 12 || $employee->status_id == 14 ) ? $isPartTimer = true : $isPartTimer=false;

    $data = new Collection;
    $billableForOT=0;
    $UT = 0;
    $OTattribute = "";
    $campName = User::find($user_id)->campaign->first()->name;

    $hasHolidayToday = false;
    $hasLWOP = null; $lwopDetails = new Collection; $hasPendingLWOP=false;
    $hasVL = null; $vlDetails = new Collection; $hasPendingVL=false;
    $hasOBT = null; $obtDetails = new Collection; $hasPendingOBT=false;
    $hasFL = null; $flDetails = new Collection; $hasPendingFL=false;

    //$thisPayrollDate = Biometrics::where(find($biometrics->id)->productionDate;
    $holidayToday = Holiday::where('holidate', $payday)->get();


    $theDay = Carbon::parse($payday." ".$schedForToday['timeStart'],"Asia/Manila");
    $fix= Carbon::parse($payday." 23:59:00","Asia/Manila");

    ($isPartTimer) ? $endOfShift =  Carbon::parse($payday." ".$schedForToday['timeStart'],"Asia/Manila")->addHour(5) :  $endOfShift = Carbon::parse($payday." ".$schedForToday['timeStart'],"Asia/Manila")->addHour(9);

    $inTime = null;
    $outTime = null;$x=null;$y=null;


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

     /*-------- FAMILY LEAVE  -----------*/
    
    

    if (count($fl) > 0) 
    {
      $hasFL=true;
      $flDeet= $fl->first();
      (!is_null($flDeet->isApproved)) ? $hasPendingFL=false : $hasPendingFL=true;

    }else{

      $hasFL = false;
      $flDeet = null;

    }

    if (count($holidayToday) > 0) $hasHolidayToday = true;


    
          
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


      $link = action('UserController@myRequests',$user_id);
      $icons ="";
      $workedHours=null;$log="";

      $t =$userLogIN[0]['timing']->format('H:i:s');
      $t2 =$userLogOUT[0]['timing']->format('H:i:s');

      $scheduleStart = Carbon::parse($payday." ".$schedForToday['timeStart'],"Asia/Manila");
      //$scheduleEnd = Carbon::parse($payday." ".$schedForToday['timeStart'],"Asia/Manila");

     




      if ($inTime->format('Y-m-d H:i:s') > $scheduleStart->format('Y-m-d H:i:s'))
      {
        //$checkLate = $userLogIN[0]['timing']->diffInMinutes(Carbon::parse($schedForToday['timeStart'], "Asia/Manila"));
        $checkLate = $inTime->diffInMinutes($scheduleStart);
        //---- MARKETING TEAM CHECK: 15mins grace period
          
          
            if ($checkLate > 2) $isLateIN = true; else $isLateIN= false;
            $isLateIN=true;
          

        
      } else $isLateIN= false;


      if ($userLogOUT[0]['timing']->format('Y-m-d H:i:s') < Carbon::parse($payday." ".$schedForToday['timeEnd'],"Asia/Manila")->format('Y-m-d H:i:s'))
      {
        $checkEarlyOut = $userLogOUT[0]['timing']->diffInMinutes(Carbon::parse($payday." ".$schedForToday['timeEnd'],"Asia/Manila"));
        //---- MARKETING TEAM CHECK: 15mins grace period
          
             if ($checkEarlyOut > 1) $isEarlyOUT = true; else $isEarlyOUT= false;
          

        
      } else $isEarlyOUT= false;

    

      if ($isEarlyOUT && $isLateIN)//use user's logs
      {
        $prod = Carbon::parse($userLogOUT[0]['timing'])->format('Y-m-d');

        $wh = Carbon::parse($userLogOUT[0]['timing'],'Asia/Manila')->diffInMinutes(Carbon::parse($userLogIN[0]['timing'],'Asia/Manila'));
        $minsLate = $scheduleStart->diffInMinutes(Carbon::parse($userLogIN[0]['timing'],'Asia/Manila'));
        //we less 1hr for the break
        $workedHours = number_format(($wh-60)/60,2)."<br/><small>(late IN & early OUT)</small>";
        $billableForOT=0; //$userLogIN[0]['timing']/60;

        $stat = User::find($user_id)->status_id;
        //****** part time user

        if ($stat == 12 || $stat ==14)
          $UT = number_format((240.0 - ($wh - $minsLate) )/60,2); //number_format((240.0 - $wh)/60,2);
        else
          $UT = number_format((480.0 - (($wh-60) - $minsLate) )/60,2);  //number_format((480.0 - $wh)/60,2); 44.44;
        

      }
      else if ($isEarlyOUT)
      {
         //--- but u need to make sure if nag late out sya
          if (Carbon::parse($userLogOUT[0]['timing'],"Asia/Manila") > Carbon::parse($payday." ".$schedForToday['timeEnd'],"Asia/Manila"))
          {
            $workedHours = 8.00;

            //check first if Locked na DTR for that production date
            $verifiedDTR = User_DTR::where('productionDate',$payday)->where('user_id',$user_id)->get();
            if (count($verifiedDTR) > 0)
              $icons = "<a title=\"Unlock DTR to File this OT\" class=\"pull-right text-gray\" style=\"font-size:1.2em;\" ><i class=\"fa fa-credit-card\"></i></a>";
            else
              $icons = "<a  id=\"OT_".$payday."\"  data-toggle=\"modal\" data-target=\"#myModal_OT".$payday."\"   title=\"File this OT\" class=\"pull-right\" style=\"font-size:1.2em;\" href=\"#\"><i class=\"fa fa-credit-card\"></i></a>";

            
             //$totalbill = number_format((Carbon::parse($shiftEnd,"Asia/Manila")->diffInMinutes(Carbon::parse($userLogOUT[0]['timing'],"Asia/Manila") ))/60,2);
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

            $wh = Carbon::parse($userLogOUT[0]['timing']->format('Y-m-d H:i:s'),"Asia/Manila")->diffInMinutes(Carbon::parse($payday." ".$schedForToday['timeStart'],"Asia/Manila")->addHour());



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


              if ($hasFL)
              {
                $workedHours1 = $this->processLeaves('FL',true,$wh,$flDeet,$hasPendingFL,$icons,$userLogIN[0],$userLogOUT[0],$shiftEnd);
                $workedHours .= $workedHours1[0]['workedHours'];
                $UT = $workedHours1[0]['UT'];

              }//end if has LWOP
            

            if (!$hasSL && !$hasVL && !$hasLWOP && !$hasOBT && !$hasFL)
            {
              $workedHours .= number_format($wh/60,2)."<br/><small>(early OUT)</small>";

              $stat = User::find($user_id)->status_id;
              //****** part time user

              if ($stat == 12 || $stat ==14)
                $UT = round((240.0 - $wh)/60,2); 
              else
                $UT = round((480.0 - $wh)/60,2); 

              $billableForOT=0;
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

        if (Carbon::parse($userLogOUT[0]['timing'],"Asia/Manila") > $endOfShift) // Carbon::parse($schedForToday['timeEnd'],"Asia/Manila") )
        {
          //$wh = Carbon::parse($schedForToday['timeEnd'],"Asia/Manila")->diffInMinutes(Carbon::parse($userLogIN[0]['timing'],"Asia/Manila")->addMinutes(60));

          $wh = $endOfShift->diffInMinutes(Carbon::parse($userLogIN[0]['timing'],"Asia/Manila")->addMinutes(60));
         
          /* ---- but we need to check Jeff's case of multiple requessts
                  bakit sya lateIN? baka may valid SL | VL |OBT */


            if ($hasSL)
            {
              //$wh = Carbon::parse($schedForToday['timeEnd'],"Asia/Manila")->diffInMinutes(Carbon::parse($userLogIN[0]['timing'],"Asia/Manila"));
              $wh = $endOfShift->diffInMinutes(Carbon::parse($userLogIN[0]['timing'],"Asia/Manila"));

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


            else if ($hasFL)
              {
                  $workedHours1 = $this->processLeaves('FL',true,$wh,$flDeet,$hasPendingFL,$icons,$userLogIN[0],$userLogOUT[0],$shiftEnd);
                  $workedHours .= $workedHours1[0]['workedHours'];
                  $UT = $workedHours1[0]['UT'];

              }//end if has LWOP

            else
            {
              
               $workedHours = number_format($wh/60,2)."<br/><small>[ Late IN ]</small>";$billableForOT=0;
               if ($hasHolidayToday){ $workedHours .= "<br/> <strong>* ". $holidayToday->first()->name. " *</strong>";}


                //check first if Locked na DTR for that production date
                  $verifiedDTR = User_DTR::where('productionDate',$payday)->where('user_id',$user_id)->get();
                  if (count($verifiedDTR) > 0)
                    $icons = "<a title=\"Unlock DTR to File this OT\" class=\"pull-right text-gray\" style=\"font-size:1.2em;\" ><i class=\"fa fa-credit-card\"></i></a>";
                  else
                    $icons = "<a  id=\"OT_".$payday."\"  data-toggle=\"modal\" data-target=\"#myModal_OT".$payday."\"   title=\"File this OT\" class=\"pull-right\" style=\"font-size:1.2em;\" href=\"#\"><i class=\"fa fa-credit-card\"></i></a>";
                 

              
                
                //$totalbill = number_format($endOfShift->diffInMinutes(Carbon::parse($userLogOUT[0]['timing'],"Asia/Manila") )/60,2);
                $totalbill = number_format( $endOfShift->diffInMinutes($userLogOUT[0]['timing'] )/60,2);
                //$totalbill = 33.33;
                

                if ($totalbill > 0.5)
                {
                  $billableForOT = $totalbill;
                  $OTattribute = $icons;
                }
                  
                else { $billableForOT = 0; /*$totalbill*/; $OTattribute = "&nbsp;&nbsp;&nbsp;";} 

                $stat = User::find($user_id)->status_id;
                //****** part time user

                if ($stat == 12 || $stat ==14)
                $UT = round((240.0 - $wh)/60,2); 
                else
                  $UT = round((480.0 - $wh)/60,2); 



            } //normal LateIN process


           

        }
        else //super undertime sya
        {
            //$wh = Carbon::parse($payday." ".$schedForToday['timeEnd'],"Asia/Manila")->diffInMinutes(Carbon::parse($userLogIN[0]['timing'],"Asia/Manila")->addMinutes(60));
            $wh = $endOfShift->diffInMinutes(Carbon::parse($userLogIN[0]['timing'],"Asia/Manila")->addMinutes(60));
            
             
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


              if ($hasFL)
              {
                $workedHours1 = $this->processLeaves('FL',true,$wh,$flDeet,$hasPendingFL,$icons,$userLogIN[0],$userLogOUT[0],$shiftEnd);
                $workedHours .= $workedHours1[0]['workedHours'];
                $UT = $workedHours1[0]['UT'];
              }//end if has FL



              if (!$hasSL && !$hasVL && !$hasLWOP && !$hasOBT && !$hasFL)
                {
                  $workedHours .= number_format($wh/60,2)."<br/><small>(Late IN)</small>";

                  $stat = User::find($user_id)->status_id;
                  //****** part time user

                  if ($stat == 12 || $stat ==14)
                  $UT = round((240.0 - $wh)/60,2); //33.33; //
                  else
                    $UT =round((480.0 - $wh)/60,2); //33.33; //


                 

                  //check mo muna kung nag OUT sya ng sobra sa ShiftEnd nya
                  $schedEnd = Carbon::parse($payday." ".$schedForToday['timeEnd'],"Asia/Manila");
                  $outNya = Carbon::parse($userLogOUT[0]['timing'],"Asia/Manila");

                  if ($outNya->format('Y-m-d H:i:s') > $schedEnd->format('Y-m-d H:i:s') ){
                    $billableForOT= number_format($outNya->diffInMinutes($schedEnd)/60,2);

                    //check first if Locked na DTR for that production date
                  $verifiedDTR = User_DTR::where('productionDate',$payday)->where('user_id',$user_id)->get();
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

         $wh = Carbon::parse($userLogOUT[0]['timing'],"Asia/Manila")->diffInMinutes(Carbon::parse($payday." ".$schedForToday['timeStart'],"Asia/Manila")->addHour());


         
            proceedWithNormal:

                /* --- NOTE: shiftEnd is date('h:i A') --- */

                if ($wh > 480)
                {
                  $workedHours =8.00; 

                  //check first if Locked na DTR for that production date
                  $verifiedDTR = User_DTR::where('productionDate',$payday)->where('user_id',$user_id)->get();
                  if (count($verifiedDTR) > 0)
                    $icons = "<a title=\"Unlock DTR to file an OT\" class=\"pull-right text-gray\" style=\"font-size:1.2em;\"><i class=\"fa fa-credit-card\"></i></a>";
                  else
                   $icons = "<a id=\"OT_".$payday."\"  data-toggle=\"modal\" data-target=\"#myModal_OT".$payday."\"  title=\"File this OT\" class=\"pull-right\" style=\"font-size:1.2em;\" href=\"#\"><i class=\"fa fa-credit-card\"></i></a>";

                   if(strlen($userLogOUT[0]['logTxt']) >= 18) //hack for LogOUT with date
                   {
                    $t = Carbon::parse($userLogOUT[0]['logTxt'],'Asia/Manila');//->format('Y-m-d H:i:s');

                    /*if($isPartTimer)
                      $shift_end = Carbon::parse($payday." ".$schedForToday['timeStart'],"Asia/Manila")->addHour(5);
                    else
                      $shift_end = Carbon::parse($payday." ".$schedForToday['timeStart'],"Asia/Manila")->addHour(9);*/

                    $totalbill = number_format( $endOfShift->diffInMinutes($userLogOUT[0]['timing'] )/60,2);
                    //$totalbill = number_format((Carbon::parse($payday." ".$shiftEnd,"Asia/Manila")->diffInMinutes(Carbon::parse($t,"Asia/Manila") ))/60,2);
                    
                    //$totalbill = 133.33;
                    //$totalbill = number_format((Carbon::parse($payday." ".$shiftEnd,"Asia/Manila")->diffInMinutes(Carbon::parse($t,"Asia/Manila") ))/60,2);

                   }
                    
                  else{ 
                    $t = Carbon::parse($userLogOUT[0]['timing'],'Asia/Manila')->format('H:i:s');
                    //$totalbill = number_format((Carbon::parse($payday." ".$shiftEnd,"Asia/Manila")->diffInMinutes(Carbon::parse($payday." ".$t,"Asia/Manila") ))/60,2);
                    $totalbill = number_format( $endOfShift->diffInMinutes($userLogOUT[0]['timing'] )/60,2);
                    //$totalbill = 244.44;
                  }


                  

                  if ($totalbill > 0.5)
                  {
                    $billableForOT = $totalbill;
                    $OTattribute = $icons;
                  }
                    
                  else { $billableForOT = 0; /* $totalbill*/; $OTattribute= "&nbsp;&nbsp;&nbsp;";} 

                  if ($hasHolidayToday)
                      {
                        $workedHours .= "<br/> <strong>[* ". $holidayToday->first()->name. " *]</strong>";
                      }


                } 
                else 
                  { 
                    $workedHours = number_format($wh/60,2); $billableForOT=0; 
                      if ($hasHolidayToday)
                      {
                        $workedHours .= "<br/> <strong>[* ". $holidayToday->first()->name. " *]</strong>";
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


      if ($hasFL)
      {
          $workedHours1 = $this->processLeaves('FL',false,0,$flDeet,$hasPendingFL,$icons,$userLogIN[0],$userLogOUT[0],$shiftEnd);
          $workedHours .= $workedHours1[0]['workedHours'];
          $UT = $workedHours1[0]['UT'];

         

      }//end if has FL

      


      if ($hasHolidayToday) /***--- we will need to check if Non-Ops personnel, may pasok kasi pag OPS **/
      {
        $workedHours .= "(8.0)<br/> <strong>[* " . $holidayToday->first()->name . " *]</strong>";
      }

     if (!$hasVL && !$hasSL && !$hasLWOP &&  !$hasOBT && !$hasFL && !$hasHolidayToday){

        //$workedHours = "<a title=\"Check your Biometrics data. \n It's possible that you pressed a wrong button, the machine malfunctioned, or you really didn't log in / out.\"><strong class=\"text-danger\">AWOL </strong></a>";
     }
    }

     

    $data->push([
                  'holidayToday'=>$holidayToday, 'schedForToday'=>$schedForToday, 
                  'checkLate'=>"nonComplicated", 'workedHours'=>$workedHours, 
                  'billableForOT'=>$billableForOT, 'OTattribute'=>$OTattribute,
                  'UT'=>$UT, 'VL'=>$hasVL, 'SL'=>$hasSL, 'LWOP'=>$hasLWOP ]);
    /*              $t=$userLogIN[0]['timing']->format('H:i:s');
                  $o = Carbon::parse($payday." ".$schedForToday['timeStart'],"Asia/Manila")->format('Y-m-d H:i:s')
    $data->push(['checkLate'=>"nonComplicated", 'workedHours'=>$workedHours, 
                  'billableForOT'=>Carbon::parse($payday." ".$t,'Asia/Manila')->format('Y-m-d H:i:s'), 'OTattribute'=>$OTattribute,
                  'UT'=>, 'VL'=>$hasVL, 'SL'=>$hasSL, 'LWOP'=>$hasLWOP ]);*/



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

      $today = Carbon::create(date('Y'), date('m'), date('d'),0,0,0, 'Asia/Manila');
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

      // return (['workSched_monthly'=>$workSched_monthly,'RDsched_monthly'=>$RDsched_monthly, 'workSched_fixed'=>$workSched_fixed,'RDsched_fixed'=>$RDsched_fixed]);

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
            else $sched= $actual_fixed_RD;  

            

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
            else $sched= $actual_fixed_WS;  

          }

          //return (['sched'=>$sched]);
          
          //return (['dayToday'=>$dayToday, 'workSched_monthly'=>$workSched_monthly,'RDsched_monthly'=>$RDsched_monthly, 'actual_fixed_WS'=>$actual_fixed_WS,'actual_fixed_RD'=>$actual_fixed_RD,'workSched_fixed'=>$workSched_fixed,'RDsched_fixed'=>$RDsched_fixed]);
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
        $cws = User_CWS::where('biometrics_id',$b->id)->where('user_id',$sched->user_id)->where('isApproved',1)->orderBy('updated_at','DESC')->get();
        //return (['cws'=>$cws]);
          if (count($cws)> 0)
          {

            //check mo muna kung alin mas recent between the sched and cws
            if ($cws->first()->created_at > $sched->created_at){
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



  public function processLeaves($leaveType,$withIssue,$wh, $deet,$hasPending,$icons,$ins,$outs,$shiftEnd)//$userLogIN[0]['logs'] || $userLogOUT[0]['logs']
  {
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
                      $workedHours = "N/A";
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
            //else $workedHours = 8.0;
            $log="<strong><small><i class=\"fa ".$i."\"></i> <em>[  ".$label." ] </em></small></strong>".$icons;
            
          }
          
          
          $workedHours .= "<br/>".$log;

        }
        else if ($deet->totalCredits == '0.50'){

            if($hasPending){
              if ($deet->halfdayFrom == 2){
                $log="<strong><small><i class=\"fa ".$i." \"></i> <em> 1st Shift ".$l." (for approval) </em></small></strong>".$icons;
                $workedHours = number_format(($wh/60),2)."<br/><small>[Late IN]</small>";$billableForOT=0;
                $UT = round(((420.0 - $wh)/60),2); //4h instead of 8H
              }
              
                
              else if ($deet->halfdayFrom == 3){
                $log="<strong><small><i class=\"fa ".$i." \"></i> <em> 2nd Shift ".$l." (for approval) </em></small></strong>".$icons;
                $workedHours = number_format(($wh/60)+5,2)."<br/><small>[Late IN]</small>";$billableForOT=0;
                $UT = round(((240.0 - $wh)/60)-1,2); //4h instead of 8H
              }
              else
                $log="<strong><small><i class=\"fa ".$i." \"></i> <em> Half-day ".$l." (for approval) </em></small></strong>".$icons;
              
              
                    //no logs, meaning halfday AWOL sya
                    if (count($ins) < 1 && count($outs) < 1) 
                      $log.="<br/><strong class='text-danger'><small><em>[ Half-day AWOL ]</em></small></strong>";

              $workedHours = "<strong class='text-danger'>AWOL</strong>";
              $workedHours .= "<br/>".$log;

            }else{

              if ($deet->halfdayFrom == 2){

                 $stat = User::find($deet->user_id)->status_id;
                    //****** part time user

                $log="<strong><small><i class=\"fa ".$i."\"></i> <em> 1st Shift ".$l." </em></small></strong>".$icons;
                if (!empty($ins) && !empty($outs)  ) { //&& ($leaveType !== 'OBT' && $leaveType !== 'VL')
                  $workedHours = number_format(($wh/60),2); //."<br/><small>[ *Late IN* ]</small>";

                  

                    if ($stat == 12 || $stat ==14)
                    $UT = round((240.0 - $wh)/60,2); 
                    else
                      $UT = round((480.0 - $wh)/60,2);  //full 8h work dapat
                }
                else {
                  $workedHours = number_format(($wh/60)+5,2)."<br/><small>[ Late IN ]</small>";
                  $UT = round(((240.0 - $wh)/60)-1,2); //4h instead of 8H
                }

                $billableForOT=0;
                
                
              }
              else if ($deet->halfdayFrom == 3){
                $log="<strong><small><i class=\"fa ".$i."\"></i> <em>[  2nd Shift ".$l." ] </em></small></strong>".$icons;
                if (!empty($ins) && !empty($outs)  )//&& ($leaveType !== 'OBT' && $leaveType !== 'VL')
                {
                  //add +1 kasi may minus sa break
                  $workedHours = number_format(($wh/60)+1,2);//."<br/><small>[ *Late IN* ]</small>"
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
               }

              
                    if (count($ins) < 1 && count($outs) < 1) 
                      $log.="<br/><strong class='text-danger'><small><em>[ Half-day AWOL ]</em></small></strong>";
              

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
            
            $log="<strong><small><i class=\"fa ".$i."\"></i> <em>[ ".$label." ]</em></small></strong>".$icons;
          }
          
          if($leaveType=='LWOP') $workedHours  .= "0.0<br/>".$log;
          else
          $workedHours .= "<br/>".$log;

        } 
        else if ($deet->totalCredits == '0.50'){

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

              
                    if (count($ins) < 1 && count($outs) < 1) 
                      $log.="<br/><strong class='text-danger'><small><em>[ Half-day AWOL ]</em></small></strong>";

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