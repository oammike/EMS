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
use OAMPI_Eval\User_DTRP;
use OAMPI_Eval\User_CWS;
use OAMPI_Eval\User_OBT;
use OAMPI_Eval\User_OT;
use OAMPI_Eval\User_VL;
use OAMPI_Eval\User_SL;
use OAMPI_Eval\User_LWOP;
use OAMPI_Eval\User_Familyleave;
use OAMPI_Eval\User_Notification;
use OAMPI_Eval\MonthlySchedules;
use OAMPI_Eval\FixedSchedules;
use OAMPI_Eval\NotifType;

trait UserTraits
{


  public function getDashboardNotifs()
  {
       $notifs = User_Notification::where('user_id',$this->user->id)->orderBy('created_at','DESC')->get(); 
       $coll = new Collection;
       $forApprovals = new Collection;

       /* -------- DONT FORGET TO UPDATE THIS AS WELL!!!! ------------ */
       $approvalTypes = [6,7,8,9,10,11,12,13,14,15,16,17,18,19];
       /* -------- DONT FORGET TO UPDATE THIS AS WELL!!!! ------------ */


       foreach( $notifs as $notif){ 
                      $detail = $notif->detail;

                      if ( in_array($detail->type, $approvalTypes) )
                      {
                        $emp = User::find($detail->from);
                        $camp = Campaign::find(Team::where('user_id',$emp->id)->first()->campaign_id);

                        if( is_null($emp->nickname) )
                          {
                            $greeting = $emp->firstname;$nick = $emp->firstname . " ". $emp->lastname;
                          } else {
                            $greeting = $emp->nickname;$nick = $emp->nickname." ".$emp->lastname;
                          }

                        
                        switch ($detail->type) {
                          case 6: //CWS
                                  {
                                    $cws = User_CWS::find($detail->relatedModelID);
                                    if (count($cws) > 0)
                                    {
                                      $b = Biometrics::find($cws->biometrics_id);

                                      if (empty($b))
                                      {
                                        $b = Biometrics::where('productionDate',date('Y-m-d',strtotime($cws->created_at)))->orderBy('id','DESC')->get();

                                        if (empty($b)){
                                          $bio = new Biometrics;
                                          $bio->productionDate = $cws->created_at;
                                          $bio->save();

                                        }
                                        else { $bio = $b->first(); }

                                        

                                      }else { $bio = $b; }

                                      if (is_null($cws->isApproved) )
                                      $forApprovals->push(['user'=>$greeting . " ". $emp->lastname, 'icon'=>"fa-calendar-times-o",
                                                    'requestor'=>$emp->id,
                                                    'nickname'=>$nick,
                                                    'program'=>$camp->name,
                                                    'user_id'=>$notif->user_id, 'id'=>$notif->id, 
                                                    'type'=>NotifType::find($detail->type)->title, 
                                                    'typeID'=>$detail->type,
                                                    'created_at'=> $detail->created_at->format('M d, Y'),
                                                    'productionDate'=>date('M d, Y', strtotime($bio->productionDate)),
                                                    'productionDay'=>date('D', strtotime($bio->productionDate)),
                                                    'deets'=> $cws]);

                                    }
                                    
                                   

                                  }break;
                          case 7: //OT
                                  {
                                    $ots = User_OT::find($detail->relatedModelID);

                                    

                                    if (count($ots) > 0)
                                    {
                                      $b = Biometrics::find($ots->biometrics_id);

                                      if (empty($b))
                                      {
                                        $b = Biometrics::where('productionDate',date('Y-m-d',strtotime($ots->created_at)))->orderBy('id','DESC')->get();

                                        if (empty($b)){
                                          $bio = new Biometrics;
                                          $bio->productionDate = $ots->created_at;
                                          $bio->save();

                                        }
                                        else { $bio = $b->first(); }


                                      }else { $bio = $b; }

                                        if (is_null($ots->isApproved))
                                        $forApprovals->push(['user'=>$greeting . " ". $emp->lastname, 'icon'=>"fa-hourglass",
                                                      'requestor'=>$emp->id,
                                                      'nickname'=>$nick, 
                                                      'program'=>$camp->name,
                                                      'user_id'=>$notif->user_id, 'id'=>$notif->id, 
                                                      'type'=>NotifType::find($detail->type)->title,
                                                      'typeID'=>$detail->type, 
                                                      'created_at'=> $detail->created_at->format('M d, Y'),
                                                      'productionDate'=>date('M d,Y', strtotime($bio->productionDate)),
                                                       'productionDay'=>date('D', strtotime($bio->productionDate)),
                                                      'deets'=> $ots]);

                                    }
                                    
                                  }break;
                          case 8: //LOGIN
                                  {
                                    $in = User_DTRP::find($detail->relatedModelID);
                                    if (count($in) > 0)
                                    { 
                                      $b = Biometrics::find($in->biometrics_id);

                                      if (empty($b))
                                      {
                                        $b = Biometrics::where('productionDate',date('Y-m-d',strtotime($in->created_at)))->orderBy('id','DESC')->get();

                                        if (empty($b)){
                                          $bio = new Biometrics;
                                          $bio->productionDate = $in->created_at;
                                          $bio->save();

                                        }
                                        else { $bio = $b->first(); }


                                      }else { $bio = $b; }

                                        if (is_null($in->isApproved))
                                        $forApprovals->push(['user'=>$greeting . " ". $emp->lastname, 'icon'=>"fa-sign-in",
                                                      'requestor'=>$emp->id,
                                                      'nickname'=>$nick,
                                                      'program'=>$camp->name,
                                                      'user_id'=>$notif->user_id, 'id'=>$notif->id, 
                                                      'type'=>NotifType::find($detail->type)->title, 
                                                      'typeID'=>$detail->type,
                                                      'created_at'=> $detail->created_at->format('M d, Y'),
                                                      'KEME'=>Biometrics::find($in->biometrics_id),
                                                      'productionDate'=>date('M d,Y', strtotime($bio->productionDate)),
                                                       'productionDay'=>date('D', strtotime($bio->productionDate)),
                                                      'deets'=> $in]);
                                    }

                                  }break;
                          case 9: //LOGOUT
                                  {
                                    $out = User_DTRP::find($detail->relatedModelID); 
                                    if (count($out) > 0) {

                                      $b = Biometrics::find($out->biometrics_id);

                                      if (empty($b))
                                      {
                                        $b = Biometrics::where('productionDate',date('Y-m-d',strtotime($out->created_at)))->orderBy('id','DESC')->get();

                                        if (empty($b)){
                                          $bio = new Biometrics;
                                          $bio->productionDate = $out->created_at;
                                          $bio->save();

                                        }
                                        else { $bio = $b->first(); }


                                      }else { $bio = $b; }


                                      if (is_null($out->isApproved))
                                      $forApprovals->push(['user'=>$greeting . " ". $emp->lastname, 'icon'=>"fa-sign-out",
                                                    'requestor'=>$emp->id,
                                                    'nickname'=>$nick, 
                                                    'program'=>$camp->name,
                                                    'user_id'=>$notif->user_id, 'id'=>$notif->id, 
                                                    'type'=>NotifType::find($detail->type)->title,
                                                    'typeID'=>$detail->type, 
                                                    'created_at'=> $detail->created_at->format('M d, Y'),
                                                    'KEME'=>Biometrics::find($out->biometrics_id),
                                                    'productionDate'=>date('M d,Y', strtotime($bio->productionDate)),
                                                     'productionDay'=>date('D', strtotime($bio->productionDate)),
                                                    'deets'=> $out]);

                                    }
                                    

                                  }break;
                          case 10: //VL
                                  {
                                    $vl = User_VL::find($detail->relatedModelID);
                                    if (count($vl) > 0)
                                    {
                                      
                                      if (is_null($vl->isApproved) )
                                      $forApprovals->push(['user'=>$greeting . " ". $emp->lastname, 'icon'=>"fa-plane",
                                                    'requestor'=>$emp->id,
                                                    'nickname'=>$nick,
                                                    'program'=>$camp->name,
                                                    'user_id'=>$notif->user_id, 'id'=>$notif->id, 
                                                    'type'=>NotifType::find($detail->type)->title, 
                                                    'typeID'=>$detail->type,
                                                    'created_at'=> $detail->created_at->format('M d, Y'),
                                                    'productionDate'=>date('M d, Y', strtotime($vl->leaveStart)),
                                                    'productionDay'=>date('D', strtotime($vl->leaveStart)),
                                                    'deets'=> $vl]);

                                    }
                                    
                                   

                                  }break;

                          case 11: //SL
                                  {
                                    $vl = User_SL::find($detail->relatedModelID);
                                    if (count($vl) > 0)
                                    {
                                      if (is_null($vl->isApproved) )
                                      $forApprovals->push(['user'=>$greeting . " ". $emp->lastname, 'icon'=>"fa-stethoscope",
                                                    'requestor'=>$emp->id,
                                                    'nickname'=>$nick,
                                                    'program'=>$camp->name,
                                                    'user_id'=>$notif->user_id, 'id'=>$notif->id, 
                                                    'type'=>NotifType::find($detail->type)->title, 
                                                    'typeID'=>$detail->type,
                                                    'created_at'=> $detail->created_at->format('M d, Y'),
                                                    'productionDate'=>date('M d, Y', strtotime($vl->leaveStart)),
                                                    'productionDay'=>date('D', strtotime($vl->leaveStart)),
                                                    'deets'=> $vl]);

                                    }
                                    
                                   

                                  }break;

                          case 12: //LWOP
                                  {
                                    $vl = User_LWOP::find($detail->relatedModelID);
                                    if (count($vl) > 0)
                                    {
                                      if (is_null($vl->isApproved) )
                                      $forApprovals->push(['user'=>$greeting . " ". $emp->lastname, 'icon'=>"fa-meh-o",
                                                    'requestor'=>$emp->id,
                                                    'nickname'=>$nick,
                                                    'program'=>$camp->name,
                                                    'user_id'=>$notif->user_id, 'id'=>$notif->id, 
                                                    'type'=>NotifType::find($detail->type)->title, 
                                                    'typeID'=>$detail->type,
                                                    'created_at'=> $detail->created_at->format('M d, Y'),
                                                    'productionDate'=>date('M d, Y', strtotime($vl->leaveStart)),
                                                    'productionDay'=>date('D', strtotime($vl->leaveStart)),
                                                    'deets'=> $vl]);

                                    }
                                    
                                   

                                  }break;

                          case 13: //obt
                                  {
                                    $vl = User_OBT::find($detail->relatedModelID);
                                    if (count($vl) > 0)
                                    {
                                      if (is_null($vl->isApproved) )
                                      $forApprovals->push(['user'=>$greeting . " ". $emp->lastname, 'icon'=>"fa-suitcase",
                                                    'requestor'=>$emp->id,
                                                    'nickname'=>$nick,
                                                    'program'=>$camp->name,
                                                    'user_id'=>$notif->user_id, 'id'=>$notif->id, 
                                                    'type'=>NotifType::find($detail->type)->title, 
                                                    'typeID'=>$detail->type,
                                                    'created_at'=> $detail->created_at->format('M d, Y'),
                                                    'productionDate'=>date('M d, Y', strtotime($vl->leaveStart)),
                                                    'productionDay'=>date('D', strtotime($vl->leaveStart)),
                                                    'deets'=> $vl]);

                                    }
                                    
                                   

                                  }break;

                          

                          case 14: //Unlock DTR
                                  {
                                    $dtr = User_DTR::find($detail->relatedModelID);
                                    if (count($dtr) > 0)
                                    {
                                      //if (is_null($dtr->isApproved) )
                                      $fromDate = Carbon::parse($dtr->productionDate,"Asia/Manila");

                                      if ($fromDate->format('d') == '06')
                                      {
                                        $toDate = Carbon::parse($fromDate->format('Y-m')."-20","Asia/Manila");
                                      }else{
                                        $td = Carbon::parse($dtr->productionDate,"Asia/Manila")->addMonth();
                                        $toDate = Carbon::parse($td->format('Y-m')."-05","Asia/Manila");

                                      }
                                      if ($emp->id !== $this->user->id){
                                        $forApprovals->push(['user'=>$greeting . " ". $emp->lastname, 'icon'=>"fa-unlock",
                                                    'requestor'=>$emp->id,
                                                    'nickname'=>$nick,
                                                    'program'=>$camp->name,
                                                    'user_id'=>$notif->user_id, 'id'=>$notif->id, 
                                                    'type'=>NotifType::find($detail->type)->title, 
                                                    'typeID'=>$detail->type,
                                                    'created_at'=> $detail->created_at->format('M d, Y'),
                                                    'productionDate'=>$fromDate->format('M d, Y'),
                                                    'productionFrom'=>$fromDate->format('Y-m-d'),
                                                    'productionTo'=>$toDate->format('Y-m-d'),
                                                    'productionDay'=>date('D', strtotime($dtr->productionDate)),
                                                    'notification_id'=>$notif->notification_id,
                                                    'deets'=> $dtr]);

                                      }
                                      

                                    }
                                    
                                   

                                  }break;


                          case 15: //Pre-shift OT
                                {
                                  $vl = User_OT::find($detail->relatedModelID);
                                  if (count($vl) > 0)
                                  {
                                    if (is_null($vl->isApproved) ){
                                      $bio = Biometrics::find($vl->biometrics_id);
                                      $forApprovals->push(['user'=>$greeting . " ". $emp->lastname, 'icon'=>"fa-clock-o",
                                                  'requestor'=>$emp->id,
                                                  'nickname'=>$nick,
                                                  'program'=>$camp->name,
                                                  'user_id'=>$notif->user_id, 'id'=>$notif->id, 
                                                  'type'=>NotifType::find($detail->type)->title, 
                                                  'typeID'=>$detail->type,
                                                  'created_at'=> $detail->created_at->format('M d, Y'),
                                                  'productionDate'=>date('M d, Y', strtotime($bio->productionDate)),
                                                  'productionDay'=>date('D', strtotime($bio->productionDate)),
                                                  'deets'=> $vl]);

                                    }
                                    

                                  }
                                  
                                 

                                }break;
                          
                          case 16: //ML
                                {
                                  $vl = User_Familyleave::find($detail->relatedModelID);
                                  if (count($vl) > 0)
                                  {
                                    if (is_null($vl->isApproved) ){
                                      //$bio = Biometrics::find($vl->biometrics_id);
                                      $forApprovals->push(['user'=>$greeting . " ". $emp->lastname, 'icon'=>"fa-female",
                                                  'requestor'=>$emp->id,
                                                  'nickname'=>$nick,
                                                  'program'=>$camp->name,
                                                  'user_id'=>$notif->user_id, 'id'=>$notif->id, 
                                                  'type'=>NotifType::find($detail->type)->title, 
                                                  'typeID'=>$detail->type,
                                                  'created_at'=> $detail->created_at->format('M d, Y'),
                                                  'productionDate'=>date('M d, Y', strtotime($vl->leaveStart)),
                                                  'productionDay'=>date('D', strtotime($vl->leaveStart)),
                                                  'deets'=> $vl]);

                                    }
                                    

                                  }
                                }break;
                          
                          case 17: //PL
                                {
                                  $vl = User_Familyleave::find($detail->relatedModelID);
                                  if (count($vl) > 0)
                                  {
                                    if (is_null($vl->isApproved) ){
                                      //$bio = Biometrics::find($vl->biometrics_id);
                                      $forApprovals->push(['user'=>$greeting . " ". $emp->lastname, 'icon'=>"fa-male",
                                                  'requestor'=>$emp->id,
                                                  'nickname'=>$nick,
                                                  'program'=>$camp->name,
                                                  'user_id'=>$notif->user_id, 'id'=>$notif->id, 
                                                  'type'=>NotifType::find($detail->type)->title, 
                                                  'typeID'=>$detail->type,
                                                  'created_at'=> $detail->created_at->format('M d, Y'),
                                                  'productionDate'=>date('M d, Y', strtotime($vl->leaveStart)),
                                                  'productionDay'=>date('D', strtotime($vl->leaveStart)),
                                                  'deets'=> $vl]);

                                    }
                                    

                                  }
                                }break;
                        
                          case 18: //SPL
                                {
                                  $vl = User_Familyleave::find($detail->relatedModelID);
                                  if (count($vl) > 0)
                                  {
                                    if (is_null($vl->isApproved) ){
                                      //$bio = Biometrics::find($vl->biometrics_id);
                                      $forApprovals->push(['user'=>$greeting . " ". $emp->lastname, 'icon'=>"fa-street-view",
                                                  'requestor'=>$emp->id,
                                                  'nickname'=>$nick,
                                                  'program'=>$camp->name,
                                                  'user_id'=>$notif->user_id, 'id'=>$notif->id, 
                                                  'type'=>NotifType::find($detail->type)->title, 
                                                  'typeID'=>$detail->type,
                                                  'created_at'=> $detail->created_at->format('M d, Y'),
                                                  'productionDate'=>date('M d, Y', strtotime($vl->leaveStart)),
                                                  'productionDay'=>date('D', strtotime($vl->leaveStart)),
                                                  'deets'=> $vl]);

                                    }
                                    

                                  }
                                }break;

                          case 19: //Unlock Specific ProductionDate DTR
                                  {
                                    $dtr = User_DTR::find($detail->relatedModelID);
                                    if (count($dtr) > 0)
                                    {
                                      //if (is_null($dtr->isApproved) )
                                      $fromDate = Carbon::parse($dtr->productionDate,"Asia/Manila");
                                      $toDate = Carbon::parse($dtr->productionDate,"Asia/Manila");

                                      if ($emp->id !== $this->user->id){
                                        $forApprovals->push(['user'=>$greeting . " ". $emp->lastname, 'icon'=>"fa-unlock",
                                                    'requestor'=>$emp->id,
                                                    'nickname'=>$nick,
                                                    'program'=>$camp->name,
                                                    'user_id'=>$notif->user_id, 'id'=>$notif->id, 
                                                    'type'=>NotifType::find($detail->type)->title, 
                                                    'typeID'=>$detail->type,
                                                    'created_at'=> $detail->created_at->format('M d, Y'),
                                                    'productionDate'=>$fromDate->format('M d, Y'),
                                                    'productionFrom'=>$fromDate->format('Y-m-d'),
                                                    'productionTo'=>$toDate->format('Y-m-d'),
                                                    'productionDay'=>date('D', strtotime($dtr->productionDate)),
                                                    'notification_id'=>$notif->notification_id,
                                                    'deets'=> $dtr]);

                                      }
                                      

                                    }
                                    
                                   

                                  }break;


                          
                        
                      }//end switch
                    }
                      
        }

       

       //----- END NOTIF
      return $forApprovals;
  }


  public function getDownline($user)
  {
     (count($lead=ImmediateHead::where('employeeNumber',$user->employeeNumber)->get()) > 0 ) ? $isLeader=true : $isLeader=false;

     if ($isLeader)
     {
        $allMen = DB::table('immediateHead_Campaigns')->where('immediateHead_id',$lead->first()->id)->
                      join('team','team.immediateHead_Campaigns_id','=','immediateHead_Campaigns.id')->
                      join('users','team.user_id','=','users.id')->
                      join('campaign','campaign.id','=','team.campaign_id')->
                      join('positions','positions.id','=','users.position_id')->
                      select('users.firstname','users.lastname','users.id','positions.name as jobTitle','campaign.name as program','team.immediateHead_Campaigns_id')->
                      where('users.status_id','!=',6)->
                      where('users.status_id','!=',7)->
                      where('users.status_id','!=',8)->
                      where('users.status_id','!=',9)->get();
        $allMen = collect($allMen)->flatten();

      foreach ($allMen as $key) {

        $u = User::find($key->id);
        $teams = $this->getDownline($u);

        if(!is_null($teams))
        {
          $allMen->push($teams);
        }
        
       
     }


     }else $allMen=null;



     return $allMen;
  }

  public function getMySubordinates($myEmployeeNumber)
  {

         
         $me = ImmediateHead::where('employeeNumber',$myEmployeeNumber)->first();

         if (empty($me))
         {
             return $me; //

         }
         else 
         {
           
            $mySub = $me->subordinates;


            $mySubordinates = new Collection;
            $mySubordinates1 = new Collection;
            
            //$coll=new Collection;
            foreach ($mySub as $em){

                $emp = User::find($em->user_id);
                $self = User::where('employeeNumber', $me->employeeNumber)->first();

                //we need to disregard top level leaders own self
                /*if ($emp->id !== $self->id)
                {*/
                  $empCamp1 = Campaign::find(Team::where('user_id',$emp->id)->first()->campaign_id);
                  $empCamp = $empCamp1->name;
                  

                  $l = Campaign::find(Team::where('user_id',$emp->id)->first()->campaign_id)->logo['filename'];

                  if (empty($l)) $logo = "white_logo_small.png";
                  else $logo = $l;

                  if ($emp->status_id !== 7 && $emp->status_id !== 8 && $emp->status_id !== 9){

                               //to remove own manager from displaying his own self
                          if ($myEmployeeNumber !== $emp->employeeNumber)
                          {
                              $isTL = ImmediateHead::where('employeeNumber',$emp->employeeNumber)->first();
                              

                              if (!is_null($isTL)){
                                  $hisMen = $isTL->subordinates->sortBy('lastname');

                                  $lead = ImmediateHead_Campaign::where('campaign_id',$empCamp1->id)->where('immediateHead_id',$isTL->id)->get();
                                  if (count($lead) > 0) $leaderID = $lead->first()->id;

                                  //$coll->push($hisMen);

                                  if (count($hisMen)>0){

                                      $activeMen =  $hisMen->filter(function ($employee)
                                       {   // Regular or Consultant or Floating
                                          //($employee->status_id == 4 || $employee->status_id == 5 || $employee->status_id == 6);
                                          return ($employee->status_id !== 7 && $employee->status_id !== 8 && $employee->status_id !== 9); //not resigned
                                      });

                                      $myIHCampID = $activeMen->first()->immediateHead_Campaigns_id;
                                      $completedEvals = EvalForm::where('evaluatedBy', $myIHCampID)->where('overAllScore','>','0.00')->get();

                                  } else 
                                  {
                                      $completedEvals = null;
                                      $activeMen = null;
                                  }

                                  
                                  //$completedEvals = EvalForm::where('evaluatedBy', $isTL->id)->where('overAllScore','>','0.00')->get();
                                  
                                  
                                  $mySubordinates1->push(['id'=>$emp->id, 'isLeader'=>true,'leaderID'=>$leaderID,'ihID'=>$isTL->id, 'lastname'=> $emp->lastname, 'firstname'=>$emp->firstname,'nickname'=>$emp->nickname,'email'=>$emp->email, 'position'=>$emp->position->name,'program'=>$empCamp,'programID'=>$empCamp1->id, 'logo'=>$logo, 'subordinates'=>$activeMen, 'completedEvals'=>$completedEvals ]);

                              } 
                              else {
                                  $mySubordinates1->push(['id'=>$emp->id, 'isLeader'=>false,'leaderID'=>null,'ihID'=>null,  'lastname'=> $emp->lastname, 'firstname'=>$emp->firstname,'nickname'=>$emp->nickname, 'email'=>$emp->email,'position'=>$emp->position->name,'program'=>$empCamp,'programID'=>$empCamp1->id,'logo'=>$logo, 'subordinates'=>null, 'completedEvals'=>null ]);
                              }

                          }//end if not himsself

                  }

               // }
                

            }//end foreach mySubordinates

            //
            $mySubordinates = $mySubordinates1->sortBy('lastname');
            return $mySubordinates;


         }//end else has subordinates
        
    }

	public function getTeammates($id)
	{
		$user = User::find($id);
		$teams = new Collection;
		
		$fellowTeam = Team::where('immediateHead_Campaigns_id',$user->team->immediateHead_Campaigns_id)->get(); 
            foreach ($fellowTeam as $pip) {

            if ($pip->user_id !== (int)$id && $this->isInactive($pip->user_id)==false )
            $teams->push([  'id'=> $pip->user_id,
                                'firstname'=>User::find($pip->user_id)->firstname,
                                'lastname'=>User::find($pip->user_id)->lastname,
                                'pic' => $this->getProfilePic($pip->user_id),
                                'position' => User::find($pip->user_id)->position->name]);
            }

            $teammates = $teams->sortBy('lastname');   
            return $teammates; 
	}

	public function getProfilePic($user_id)
	{
		if ( file_exists('public/img/employees/'.$user_id.'.jpg') )
             {
                $img = asset('public/img/employees/'.$user_id.'.jpg');
             } else {
                $img = asset('public/img/useravatar.png');
             }

         return $img;
	}


	public function isInactive($user_id)
	{

		$emp = User::find($user_id);
		// Resigned || Terminated || End of Contract
		if ($emp->status_id == 7 || $emp->status_id == 8 || $emp->status_id == 9 ) return true; else return false;
	}


  public function notifySender($requestItem,$notif,$notifType)
  {

    $uNotif = new User_Notification;
                  $uNotif->user_id = $requestItem->user_id;
                  $uNotif->notification_id = $notif->id;
                  $uNotif->seen = false;
                  $uNotif->save();


    /*switch ($notifType) {

      //********** CWS
      case '6':{
                  $uNotif = new User_Notification;
                  $uNotif->user_id = $requestItem->user_id;
                  $uNotif->notification_id = $notif->id;
                  $uNotif->seen = false;
                  $uNotif->save();
              
              }break;
      
       //********** OT
      case '7':{
                  $uNotif = new User_Notification;
                  $uNotif->user_id = $requestItem->user_id;
                  $uNotif->notification_id = $notif->id;
                  $uNotif->seen = false;
                  $uNotif->save();
              # code...
              }break;
      
      //********** DTRP IN
      case '8':{
              # code...
              }break;

      //********** DTRP OUT        
      case '9':{
              # code...
              }break;


      //********** VL REQUEST       
      case '10':{
              # code...
              }break;


      //********** SL REQUEST        
      case '11':{
              # code...
              }break;


      //********** LWOP        
      case '12':{
              # code...
              }break;

      //********** OBT REQUEST        
      case '13':{
              # code...
              }break;


      //********** UNLOCK DTR REQUEST        
      case '14':{
              # code...
              }break;
      
      default:
        # code...
        break;
    }*/

    return $uNotif;

  }
}

?>