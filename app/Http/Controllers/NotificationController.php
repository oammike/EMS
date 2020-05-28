<?php

namespace OAMPI_Eval\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Yajra\Datatables\Facades\Datatables;
use \Mail;
use Carbon\Carbon;

use OAMPI_Eval\Http\Requests;
use OAMPI_Eval\User;
use OAMPI_Eval\User_Notification;
use OAMPI_Eval\Notification;
use OAMPI_Eval\UserType;
use OAMPI_Eval\NotifType;
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
use OAMPI_Eval\User_CWS;
use OAMPI_Eval\User_OT;
use OAMPI_Eval\User_DTRP;
use OAMPI_Eval\User_DTR;
use OAMPI_Eval\User_VL;
use OAMPI_Eval\User_VTO;
use OAMPI_Eval\User_SL;
use OAMPI_Eval\User_OBT;
use OAMPI_Eval\User_LWOP;
use OAMPI_Eval\User_Familyleave;
use OAMPI_Eval\Biometrics;

class NotificationController extends Controller
{
    protected $user;
    protected $notification;

    public function __construct(Notification $notification)
    {
        $this->middleware('auth');
        $this->user =  User::find(Auth::user()->id);
        $this->notification = $notification;
    }

     public function index()
    {
        //get all user's notif
        $allNotifs = new Collection;
        $yourNotif = $this->user->notifications->sortByDesc('id');
        $coll=new Collection;
        $ownNotif = null;
        $message =" ";

       // return $yourNotif;

        $yourNotifs = new Collection;
        
        //return $yourNotif;
        foreach($yourNotif->take(50) as $notif){

          if($notif->detail->from !== null)
          { // ****** if notif has an actual requestor

              if ($notif->detail->type == 5){ //if New Regularization eval, use ImmediateHeadCamp_id
                    //$fromData =ImmediateHead::find(ImmediateHead_Campaign::find($notif->detail->from)->immediateHead_id);
                    $ev = EvalForm::find($notif->detail->relatedModelID);

                    if (count($ev)>0){
                      $fromData = User::find($ev->user_id);
                      $fromDataID = $fromData->id;

                    ( is_null($fromData->nickname) ) ? $from = $fromData->firstname." ".$fromData->lastname : $from = $fromData->nickname." ".$fromData->lastname;

                   
                    $position = $fromData->position->name;
                    $campaign = Campaign::find(Team::where('user_id', $fromData->id)->first()->campaign_id)->name;
                    }

                    else{
                      $fromData=null;$position=null;$campaign=null;
                    }

                    

                    /************** 6:CWS, 7:OT, 8:DTRPin, 9:DTRPout, 10:VL, 11:SL, 14:Unlock, 19:PRodDate Unlock *************/
              }
              else if ($notif->detail->type == 6 || $notif->detail->type == 7 || 
                       $notif->detail->type == 8 || $notif->detail->type == 9 || 
                       $notif->detail->type == 10 || $notif->detail->type == 11 ||
                       $notif->detail->type == 12 || $notif->detail->type == 13 || 
                       $notif->detail->type == 14 || $notif->detail->type == 19 || $notif->detail->type == 21){ //if  request

                $fromData = User::find($notif->detail->from);
                
                ($fromData->id == $this->user->id) ? $ownNotif=true : $ownNotif=false;

                $hasIssue =false;


                    if ($ownNotif) //show the TL who approved instead
                    {
                      switch ($notif->detail->type) {
                        case '6': {
                                      $ih = ImmediateHead_Campaign::find(User_CWS::find($notif->detail->relatedModelID)->approver);
                                      if ( count((array)$ih) > 0 ){

                                        $fromData =ImmediateHead::find($ih->immediateHead_id);
                                      }else{
                                        $fromData = User::find(User_CWS::find($notif->detail->relatedModelID)->user_id)->id;
                                        $hasIssue = true;

                                      }

                                      //$fromData =ImmediateHead::find(ImmediateHead_Campaign::find(User_CWS::find($notif->detail->relatedModelID)->approver)->immediateHead_id);break;

                        } 
                        case '7': { 
                                    $otdetail = User_OT::find($notif->detail->relatedModelID);

                                    if ($otdetail)
                                    {
                                      $ih = ImmediateHead_Campaign::find($otdetail->approver);
                                      if ( count((array)$ih) > 0 ){

                                        $fromData =ImmediateHead::find($ih->immediateHead_id);
                                      }else{
                                        $fromData = User::find(User_OT::find($notif->detail->relatedModelID)->user_id)->id;
                                        $hasIssue = true;

                                      }

                                    }else
                                    {
                                      $fromData=null;$hasIssue=true;
                                    }
                                    
                                  }
                                  break;
                        case '8': {
                                    $ap = ImmediateHead_Campaign::find(User_DTRP::find($notif->detail->relatedModelID)->approvedBy);
                                    if (count((array)$ap) > 0)
                                    {
                                      $fromData =ImmediateHead::find($ap->immediateHead_id);

                                    }else
                                    {
                                      $fromData = User::find(User_DTRP::find($notif->detail->relatedModelID)->user_id)->id;
                                      $hasIssue = true;

                                    }
                                    break;


                         
                        }
                        case '9':{
                                    $ap = ImmediateHead_Campaign::find(User_DTRP::find($notif->detail->relatedModelID)->approvedBy);
                                    if (count((array)$ap) > 0)
                                    {
                                      $fromData =ImmediateHead::find($ap->immediateHead_id);

                                    }else
                                    {
                                      $fromData = User::find(User_DTRP::find($notif->detail->relatedModelID)->user_id)->id;
                                      $hasIssue = true;

                                    }
                                    break;
                        }
                        case '10': {

                                    $ih = ImmediateHead_Campaign::find(User_VL::find($notif->detail->relatedModelID)->approver);
                                    if ( count((array)$ih) > 0 ){

                                      $fromData =ImmediateHead::find($ih->immediateHead_id);
                                    }else{
                                      $fromData = User::find(User_VL::find($notif->detail->relatedModelID)->user_id)->id;
                                      $hasIssue = true;

                                    }

                                  } break;

                        case '11': {

                                    $ih = ImmediateHead_Campaign::find(User_SL::find($notif->detail->relatedModelID)->approver);
                                    if ( count((array)$ih) > 0 ){

                                      $fromData =ImmediateHead::find($ih->immediateHead_id);
                                    }else{
                                      $fromData = User::find(User_SL::find($notif->detail->relatedModelID)->user_id)->id;
                                      $hasIssue = true;

                                    }


                                  } break;

                                   
                        case '12': {

                                    $ih = ImmediateHead_Campaign::find(User_LWOP::find($notif->detail->relatedModelID)->approver);
                                    if ( count((array)$ih) > 0 ){

                                      $fromData =ImmediateHead::find($ih->immediateHead_id);
                                    }else{
                                      $fromData = User::find(User_LWOP::find($notif->detail->relatedModelID)->user_id)->id;
                                      $hasIssue = true;

                                    }


                                  } break;
                                  
                        case '13': {

                                    $ih = ImmediateHead_Campaign::find(User_OBT::find($notif->detail->relatedModelID)->approver);
                                    if ( count((array)$ih) > 0 ){

                                      $fromData =ImmediateHead::find($ih->immediateHead_id);
                                    }else{
                                      $fromData = User::find(User_OBT::find($notif->detail->relatedModelID)->user_id)->id;
                                      $hasIssue = true;

                                    }


                                  } break;
                        case '14': $fromData = null; break;
                        case '15': $fromData = User::find(User_OT::find($notif->detail->relatedModelID)->approver)->id;break;
                        case '16': $fromData =User::find(User_Familyleave::find($notif->detail->relatedModelID)->approver)->id;break;
                        case '17': $fromData =User::find(User_Familyleave::find($notif->detail->relatedModelID)->approver)->id;break;
                        case '18': $fromData =User::find(User_Familyleave::find($notif->detail->relatedModelID)->approver)->id;break;
                        case '19': $fromData = null; break;
                        case '21': {

                                    $ih = ImmediateHead_Campaign::find(User_VTO::find($notif->detail->relatedModelID)->approver);
                                    if ( count((array)$ih) > 0 ){

                                      $fromData =ImmediateHead::find($ih->immediateHead_id);
                                    }else{
                                      $fromData = User::find(User_VTO::find($notif->detail->relatedModelID)->user_id)->id;
                                      $hasIssue = true;

                                    }

                                  } break;

                      }
                      
                      if (!is_null($fromData) && !$hasIssue) {
                        $position = $fromData->userData->position->name;
                        $campaign = Campaign::find(Team::where('user_id', $fromData->userData->id)->first()->campaign_id)->name;
                        $fromDataID = $fromData->userData->id;

                      } else { $position=null; $campaign=null; $fromDataID=$notif->detail->from; }


                      if ( ($notif->detail->type !== 14 && $notif->detail->type !== 19) && !$hasIssue) //because UNLOCK DTR has no immediate head data
                      {
                        ( is_null($fromData->userData->nickname) ) ? $from = $fromData->userData->firstname." ".$fromData->userData->lastname : $from = $fromData->userData->nickname." ".$fromData->userData->lastname;

                        if ( file_exists('public/img/employees/'.$fromData->userData->id.'.jpg') )
                           {
                              $img = asset('public/img/employees/'.$fromData->userData->id.'.jpg');
                              $fromImage = '<img src="'.$img.'" class="user-image img-circle" alt="User Image" width="30"/> ';
                           }
                           else{
                              $img = asset('public/img/useravatar.png');
                              $fromImage ='<img src="'.$img.'" class="user-image img-circle" alt="User Image" width="30" /> ';
                           }

                      } else /*********** for UNLOCK DTR REQUEST **********/
                      {
                        $img = asset('public/img/oam_favicon1-55027f4ev1_site_icon-32x32.png');
                        $fromImage = '<img src="'.$img.' " class="user-image img-circle" />';
                        $from=null;
                      }

                      

                      
                    }else
                    {
                      ( is_null($fromData->nickname) ) ? $from = $fromData->firstname." ".$fromData->lastname : $from = $fromData->nickname." ".$fromData->lastname;
                      $position = $fromData->position->name;
                      $campaign = Campaign::find(Team::where('user_id', $fromData->id)->first()->campaign_id)->name;
                      $fromDataID = $fromData->id;

                      if ( file_exists('public/img/employees/'.$fromData->id.'.jpg') )
                         {
                            $img = asset('public/img/employees/'.$fromData->id.'.jpg');
                            $fromImage = '<img src="'.$img.'" class="user-image img-circle" alt="User Image" width="30"/> ';
                         }
                         else{
                            $img = asset('public/img/useravatar.png');
                            $fromImage ='<img src="'.$img.'" class="user-image img-circle" alt="User Image" width="30" /> ';
                         }

                     
                    }

             // }else if($notif->detail->type == 10 ){

              }else{

                    if ($notif->detail->type == 15 || $notif->detail->type == 16 || $notif->detail->type == 17 || $notif->detail->type == 18) //problema gawa ni WFM
                    {
                      $fromData= User::find($notif->detail->from);
                      $fromDataID = $fromData->id;
                      $position = Position::find($fromData->position_id)->name;
                      $camp = Campaign::where('id',Team::where('user_id',$fromData->id)->first()->campaign_id)->get();

                      $img = asset('public/img/employees/'.$fromData->id.'.jpg');
                      $fromImage = '<img src="'.$img.'" class="user-image img-circle" alt="User Image" width="30"/> ';

                    }else{

                      if (empty(ImmediateHead::find($notif->detail->from)) ){
                        $fromData= User::find($notif->detail->from);
                        $fromDataID = $fromData->id;
                        $position = Position::find($fromData->position_id)->name;
                        $camp = Campaign::where('id',Team::where('user_id',$fromData->id)->first()->campaign_id)->get();

                        if ( file_exists('public/img/employees/'.$fromData->id.'.jpg') )
                           {
                              $img = asset('public/img/employees/'.$fromData->id.'.jpg');
                              $fromImage = '<img src="'.$img.'" class="user-image img-circle" alt="User Image" width="30"/> ';
                           }
                           else{
                              $img = asset('public/img/useravatar.png');
                              $fromImage ='<img src="'.$img.'" class="user-image img-circle" alt="User Image" width="30" /> ';
                           }



                      }
                      else{
                        $fromData = ImmediateHead::find($notif->detail->from);
                        $fromDataID = $fromData->userData->id;
                        $position = Position::find($fromData->userData->position_id)->name;
                        $camp = $fromData->campaigns;

                        if ( file_exists('public/img/employees/'.$fromData->userData->id.'.jpg') )
                           {
                              $img = asset('public/img/employees/'.$fromData->userData->id.'.jpg');
                              $fromImage = '<img src="'.$img.'" class="user-image img-circle" alt="User Image" width="30"/> ';
                           }
                           else{
                              $img = asset('public/img/useravatar.png');
                              $fromImage ='<img src="'.$img.'" class="user-image img-circle" alt="User Image" width="30" /> ';
                           }
                      }
                      
                      
                      
                      
                
                      

                    }
                    
                    $from =  $fromData->firstname." ".$fromData->lastname;
                    $campaign =" ";

                      if(count($camp) > 1){
                              foreach ($camp as $c) {
                                  $campaign .= $c->name.", ";
                              }
                              
                      } else $campaign = $camp->first()->name;
                    


                     // TL requestor

                      if ($notif->detail->type == 15 || $notif->detail->type == 16 || $notif->detail->type == 17 || $notif->detail->type == 18) //problema gawa ni WFM
                      {
                        

                      }else{

                          

                      }

                         

                    

                    
                } 

          } else { $from=null; $position=null; $campaign=null; $tlimage=null; $fromDataID = null; } // **** notif is system generated, no requestor

            

          switch($notif->detail->type)
            {
                case 1: { $actionlink = action('UserController@changePassword'); 
                      $message = "Kindly update your default password for security purposes. Thank you."; 

                      $fromImage =null;

                      break; 


                      } //change of password

                case 2: {
                       // CHANGE OF PROGRAM DEPT
                          // $tlConcerned = null;
                          // $actionlink = null;
                          // $fromImage = null;
                          $mvt = Movement::find($notif->detail->relatedModelID);
                          if (count((array)$mvt)>0) 
                          {
                            $tlConcerned = ImmediateHead_Campaign::find($mvt->immediateHead_details->imHeadCampID_new);
                            $personConcerned = ImmediateHead::find($tlConcerned->immediateHead_id);

                                     //$personConcerned =  ImmediateHead::find(Movement::find($notif->detail->relatedModelID)->immediateHead_details->imHeadCampID_new);
                             
                             if($this->user->employeeNumber == $personConcerned->employeeNumber)
                             {

                                  $message = " has been transfered to your team. Click on the link above to learn more. ";
                                  $transfered = User::find(Movement::find($notif->detail->relatedModelID)->user_id);
                                  $fromDataID = $transfered->id;
                                  $from = $transfered->firstname." ".$transfered->lastname;
                                  $position = $transfered->position->name;
                                  if ( file_exists('public/img/employees/'.$transfered->id.'.jpg') )
                                     {
                                      $img = asset('public/img/employees/'.$transfered->id.'.jpg');
                                      $fromImage = '<img src="'.$img.'" class="user-image img-circle" alt="User Image" width="30"/> ';
                                     }
                                     else{
                                      $img = asset('public/img/useravatar.png');
                                      $fromImage ='<img src="'.$img.'" class="user-image img-circle" alt="User Image" width="30" /> ';
                                     }


                             } else $message = "requested a new employee movement. Click on the link above to learn more. ";

                             //check if interdepartment movement or not;
                             // if not then needs approval first

                             $canApprove = UserType::find($this->user->userType_id)->roles->where('label','APPROVE_MOVEMENTS')->first();
                             $actionlink = route('movement.show', array('id' => $notif->detail->relatedModelID, 'notif'=>$notif->detail->id, 'seen' => true )); 
                            // else 
                            //     $actionlink = route('movement.show', array('id' => $notif->detail->relatedModelID, 'notif'=>$notif->detail->id, 'seen' => true )); 
                          } else $actionlink = "";

                          break; 
                          }

                      

                case 3: { $actionlink = action('MovementController@show',$notif->detail->relatedModelID ); 
                            $mvtDeets = Movement::find($notif->detail->relatedModelID); 

                            if (count((array)$mvtDeets)>0)
                            {
                              $requestor = ImmediateHead::find($mvtDeets->requestedBy); 

                              if($this->user->id == $mvtDeets->user_id) //if it's the employee concerned
                               {

                                  $message = " has updated your position/job title";
                                  $transfered = User::find(Movement::find($notif->detail->relatedModelID)->notedBy);
                                  $fromDataID = $transfered->id;

                                  $from = $transfered->firstname." ".$transfered->lastname;
                                  $position = $transfered->position->name;
                                  if ( file_exists('public/img/employees/'.$transfered->id.'.jpg') )
                                       {
                                          $img = asset('public/img/employees/'.$transfered->id.'.jpg');
                                          $fromImage = '<img src="'.$img.'" class="user-image img-circle" alt="User Image" width="30"/> ';
                                       }
                                       else{
                                          $img = asset('public/img/useravatar.png');
                                          $fromImage ='<img src="'.$img.'" class="user-image img-circle" alt="User Image" width="30" /> ';
                                       }


                              } else if ( $this->user->employeeNumber == $requestor->employeeNumber ){ //if it's the TL who did the request

                                  $message = " has approved your submitted PCN ";
                                  $transfered = User::find(Movement::find($notif->detail->relatedModelID)->notedBy);
                                  $fromDataID = $transfered->id;

                                  $from = $transfered->firstname." ".$transfered->lastname;
                                  $position = $transfered->position->name;
                                  if ( file_exists('public/img/employees/'.$transfered->id.'.jpg') )
                                       {
                                          $img = asset('public/img/employees/'.$transfered->id.'.jpg');
                                          $fromImage = '<img src="'.$img.'" class="user-image img-circle" alt="User Image" width="30"/> ';
                                       }
                                       else{
                                          $img = asset('public/img/useravatar.png');
                                          $fromImage ='<img src="'.$img.'" class="user-image img-circle" alt="User Image" width="30" /> ';
                                       }

                              } else $message = "requested a change in job title. Click on the link above to learn more. "; // notif for HR

                            }
                            

                        break; 

                        } //change position
                case 4: {   $actionlink = action('MovementController@show',$notif->detail->id );
                            $mvtDeets = Movement::find($notif->detail->relatedModelID); 

                            if (count((array)$mvtDeets)>0)
                            {
                              $requestor = ImmediateHead::find($mvtDeets->requestedBy); 

                              if($this->user->id == $mvtDeets->user_id) //if it's the employee concerned
                               {

                                  $message = " has updated your employement status ";
                                  $transfered = User::find(Movement::find($notif->detail->relatedModelID)->notedBy);
                                  $fromDataID = $transfered->id;

                                  $from = $transfered->firstname." ".$transfered->lastname;
                                  $position = $transfered->position->name;
                                  if ( file_exists('public/img/employees/'.$transfered->id.'.jpg') )
                                       {
                                          $img = asset('public/img/employees/'.$transfered->id.'.jpg');
                                          $fromImage = '<img src="'.$img.'" class="user-image img-circle" alt="User Image" width="30"/> ';
                                       }
                                       else{
                                          $img = asset('public/img/useravatar.png');
                                          $fromImage ='<img src="'.$img.'" class="user-image img-circle" alt="User Image" width="30" /> ';
                                       }


                              } else if ( $this->user->employeeNumber == $requestor->employeeNumber ){ //if it's the TL who did the request

                                  $message = " has approved your submitted PCN ";
                                  $transfered = User::find(Movement::find($notif->detail->relatedModelID)->notedBy);
                                  $fromDataID = $transfered->id;

                                  $from = $transfered->firstname." ".$transfered->lastname;
                                  $position = $transfered->position->name;
                                  if ( file_exists('public/img/employees/'.$transfered->id.'.jpg') )
                                       {
                                          $img = asset('public/img/employees/'.$transfered->id.'.jpg');
                                          $fromImage = '<img src="'.$img.'" class="user-image img-circle" alt="User Image" width="30"/> ';
                                       }
                                       else{
                                          $img = asset('public/img/useravatar.png');
                                          $fromImage ='<img src="'.$img.'" class="user-image img-circle" alt="User Image" width="30" /> ';
                                       }

                              } else $message = "requested a new employee movement. Click on the link above to learn more. "; // notif for HR

                            }
                            
                            break; 
                          }


                // up for EMPLOYEE STATUS UPDATE
                case 5: {   $actionlink = "";//action('EvalFormController@show',['id'=>$notif->detail->relatedModelID, 'evaluatedBy'=>EvalForm::find($notif->detail->relatedModelID)->evaluatedBy, 'updateStatus'=>'true' ] ); //new Regularization eval
                            
                            $img = "";//asset('public/img/employees/'.$fromData->id.'.jpg');
                            $fromImage = '<img src="'.$img.'" class="user-image img-circle" alt="User Image" width="30"/> ';
                            $message = " is up for Employment Status update.";

                            break; 
                          }

                case 6: {   $actionlink = action('UserCWSController@show',['id'=>$notif->detail->relatedModelID, 'notif'=>$notif->detail->id, 'seen'=>'true' ] ); //new CWS
                            $thereq =User_CWS::find($notif->detail->relatedModelID);

                            if (is_null($thereq))
                            {
                              $theBio=null;
                              $message = " requested a <strong>CWS</strong>";
                              //$coll->push(['approved'=>"Data Not Found", 'thereq'=>$thereq,'bio'=>$theBio, 'id'=>$notif->id]);

                            }else{
                              $theBio = Biometrics::find($thereq->biometrics_id);

                              if ($ownNotif){
                                if ($thereq->isApproved){
                                  $message = " <strong>approved</strong> your CWS request";
                                }else $message = " <strong>denied</strong> your CWS request. I'm sorry. <strong><i class='fa fa-meh-o'></i></strong> ";
                                
                              }else
                              $message = " requested a <strong>CWS</strong> for <span class='text-danger'> ". date('M d, Y', strtotime($theBio->productionDate))."</span>";

                            }
                            
                            

                            break; 
                          }

                // new OT REQUEST
                case 7: {   
                            $actionlink = action('UserOTController@show',['id'=>$notif->detail->relatedModelID, 'notif'=>$notif->detail->id, 'seen'=>'true' ] ); //new overtime

                            $thereq =User_OT::find($notif->detail->relatedModelID);

                            if (is_null($thereq))
                            {
                              $message = " filed <strong>New Overtime</strong> request";

                            }else{

                              if ($ownNotif){
                                if ($thereq->isApproved){
                                  $message = " <strong>approved</strong> your Overtime request";
                                }else $message = " <strong>denied</strong> your Overtime request. I'm sorry. <strong><i class='fa fa-meh-o'></i></strong> ";
                                
                              }
                              else{
                                $message = " filed a new Overtime";
                              }

                            }

                            

                            break; 
                          }
                case 8: {   // DTRP LOG IN
                            $actionlink = action('UserDTRPController@show',['id'=>$notif->detail->relatedModelID,'notif'=>$notif->detail->id,'seen'=>'true']);
                            $thereq = User_DTRP::find($notif->detail->relatedModelID);
                            if (is_null($thereq))
                            {
                              $theBio=null;
                              $message = " filed a new <strong> DTRP - TIME IN</strong> ";
                              //$coll->push(['approved'=>"Data Not Found", 'thereq'=>$thereq,'bio'=>$theBio, 'id'=>$notif->id]);

                            }else{

                              if ($ownNotif){
                                if ($thereq->isApproved){
                                  $message = " <strong>approved</strong> your DTRP TIME IN request";
                                }else $message = " <strong>denied</strong> your DTRP TIME IN request. I'm sorry. <strong><i class='fa fa-meh-o'></i></strong> ";
                                
                              }
                              else{
                                $theBio = Biometrics::findOrFail($thereq->biometrics_id);
                                $message = " filed a new <strong> DTRP - TIME IN</strong> for <span class='text-danger'> ". date('M d, Y', strtotime($theBio->productionDate))."</span>";

                              }

                              
                            }
                            
                          }break; 

               // DTRP OUT
                case 9: {   
                            $actionlink = action('UserDTRPController@show',['id'=>$notif->detail->relatedModelID,'notif'=>$notif->detail->id,'seen'=>'true']);
                            $thereq = User_DTRP::find($notif->detail->relatedModelID);

                            if(is_null($thereq))
                            {
                              $theBio=null;
                              $message=" filed a new <strong>DTRP TIME OUT</strong>";

                            }else{

                              if ($ownNotif){
                                if ($thereq->isApproved){
                                  $message = " <strong>approved</strong> your DTRP TIME OUT request";
                                }else $message = " <strong>denied</strong> your DTRP TIME OUT request. I'm sorry. <strong><i class='fa fa-meh-o'></i></strong> ";
                                
                              }
                              else{
                                $theBio = Biometrics::findOrFail($thereq->biometrics_id);
                                $message = " filed a new <strong> DTRP - TIME OUT</strong> for <span class='text-danger'> ". date('M d, Y', strtotime($theBio->productionDate))."</span>";

                              }

                              
                            }

                           
                              
                          }break;

                // VACATION LEAVE
                case 10: {
                            $actionlink = action('UserVLController@show',['id'=>$notif->detail->relatedModelID, 'notif'=>$notif->detail->id, 'seen'=>'true' ] ); 
                            $thereq =User_VL::find($notif->detail->relatedModelID);
                            if (is_null($thereq)){
                              $theBio = null;
                              $message=" filed a <strong>VACATION LEAVE</strong>";
                              //$coll->push(['approved'=>"Data Not Found", 'thereq'=>$thereq,'bio'=>$theBio, 'id'=>$notif->id]);
                            }
                            else{

                              if ($ownNotif){
                                if ($thereq->isApproved){
                                  $message = " <strong>approved</strong> your VACATION LEAVE request";
                                }else $message = " <strong>denied</strong> your VACATION LEAVE request. I'm sorry. <strong><i class='fa fa-meh-o'></i></strong> ";
                                
                              }
                              else{
                                $theBio = Biometrics::where('productionDate', date('Y-m-d',strtotime($thereq->leaveStart)));
                                $message = " filed a <strong>VACATION LEAVE</strong> for <span class='text-danger'> ". date('M d, Y', strtotime($thereq->leaveStart))."</span>";

                                //  if (is_null($thereq->isApproved))
                                // $coll->push(['approved'=>"not yet", 'thereq'=>$thereq,'bio'=>$theBio, 'id'=>$notif->id]);
                                // else
                                //   $coll->push(["approved"=>"yes", 'thereq'=>$thereq,'bio'=>$theBio, 'id'=>$notif->id]);

                              }


                              
                            }

                            // $img = asset('public/img/employees/'.$fromData->id.'.jpg');
                            // $fromImage = '<img src="'.$img.'" class="user-image img-circle" alt="User Image" width="30"/> ';

                          }break;


                // SICK LEAVE
                case 11: {
                            $actionlink = action('UserSLController@show',['id'=>$notif->detail->relatedModelID, 'notif'=>$notif->detail->id, 'seen'=>'true' ] ); 
                            $thereq =User_SL::find($notif->detail->relatedModelID);
                            if (is_null($thereq)){
                              $theBio = null;
                              $message=" filed a <strong>SICK LEAVE</strong>";
                              //$coll->push(['approved'=>"Data Not Found", 'thereq'=>$thereq,'bio'=>$theBio, 'id'=>$notif->id]);
                            }
                            else{

                              if ($ownNotif){
                                if ($thereq->isApproved){
                                  $message = " <strong>approved</strong> your SICK LEAVE request";
                                }else $message = " <strong>denied</strong> your SICK LEAVE request. I'm sorry. <strong><i class='fa fa-meh-o'></i></strong> ";
                                
                              }
                              else{
                                $theBio = Biometrics::where('productionDate', date('Y-m-d',strtotime($thereq->leaveStart)));
                                $message = " filed a <strong>SICK LEAVE</strong> for <span class='text-danger'> ". date('M d, Y', strtotime($thereq->leaveStart))."</span>";

                             

                              }


                              
                            }

                         

                          }break;


                // LWOP LEAVE
                case 12: {
                            $actionlink = action('UserLWOPController@show',['id'=>$notif->detail->relatedModelID, 'notif'=>$notif->detail->id, 'seen'=>'true' ] ); 
                            $thereq =User_LWOP::find($notif->detail->relatedModelID);
                            if (is_null($thereq)){
                              $theBio = null;
                              $message=" filed a <strong>LEAVE WITHOUT PAY</strong>";
                              //$coll->push(['approved'=>"Data Not Found", 'thereq'=>$thereq,'bio'=>$theBio, 'id'=>$notif->id]);
                            }
                            else{

                              if ($ownNotif){
                                if ($thereq->isApproved){
                                  $message = " <strong>approved</strong> your LEAVE WITHOUT PAY request";
                                }else $message = " <strong>denied</strong> your LEAVE WITHOUT PAY request. I'm sorry. <strong><i class='fa fa-meh-o'></i></strong> ";
                                
                              }
                              else{
                                $theBio = Biometrics::where('productionDate', date('Y-m-d',strtotime($thereq->leaveStart)));
                                $message = " filed a <strong>LEAVE WITHOUT PAY </strong> for <span class='text-danger'> ". date('M d, Y', strtotime($thereq->leaveStart))."</span>";


                              }


                              
                            }


                          }break; 

                // OBT LEAVE
                case 13: {
                            $actionlink = action('UserOBTController@show',['id'=>$notif->detail->relatedModelID, 'notif'=>$notif->detail->id, 'seen'=>'true' ] ); 
                            $thereq =User_OBT::find($notif->detail->relatedModelID);
                            if (is_null($thereq)){
                              $theBio = null;
                              $message=" filed a <strong>OFFICIAL BUSINESS TRIP</strong>";
                              //$coll->push(['approved'=>"Data Not Found", 'thereq'=>$thereq,'bio'=>$theBio, 'id'=>$notif->id]);
                            }
                            else{

                              if ($ownNotif){
                                if ($thereq->isApproved){
                                  $message = " <strong>approved</strong> your OBT request";
                                }else $message = " <strong>denied</strong> your OBT request. I'm sorry. <strong><i class='fa fa-meh-o'></i></strong> ";
                                
                              }
                              else{
                                $theBio = Biometrics::where('productionDate', date('Y-m-d',strtotime($thereq->leaveStart)));
                                $message = " filed an <strong>OFFICIAL BUSINESS TRIP </strong> for <span class='text-danger'> ". date('M d, Y', strtotime($thereq->leaveStart))."</span>";


                              }


                              
                            }


                          }break; 

                // UNLOCK
                case 14: {   
                            $actionlink = action('DTRController@seenzoned',['id'=>$notif->detail->id, 'seen'=>'true' ] ); 
                            $thereq =User_DTR::find($notif->detail->relatedModelID);

                            if (is_null($thereq))
                            {
                              $theBio=null;
                              ($ownNotif) ? $message = " DTR unlock request: approved. "  : $message = " sent a <strong>DTR Sheet Unlock request</strong>."; //date('M d, Y', strtotime($thereq->productionDate))

                            }else{
                              $theBio = Biometrics::where('productionDate', date('Y-m-d',strtotime($thereq->productionDate)));

                              ($ownNotif) ? $message = " Request to unlock DTR for production date: ". date('M d, Y', strtotime($thereq->productionDate)) : $message = " is requesting for <strong>DTR Sheet Unlock</strong> for <span class='text-danger'> ". date('M d, Y', strtotime($thereq->productionDate))."</span>";
                            }
                            
                            // $img = asset('public/img/employees/'.$fromData->id.'.jpg');
                            // $fromImage = '<img src="'.$img.'" class="user-image img-circle" alt="User Image" width="30"/> ';


                            

                            // if (is_null($thereq))
                            // $coll->push(["approved"=>"yes", 'thereq'=>$thereq,'bio'=>$theBio, 'id'=>$notif->id]);

                            // else
                            //   $coll->push(['approved'=>"not yet", 'thereq'=>$thereq,'bio'=>$theBio, 'id'=>$notif->id]);

                            break; 
                          }

                // PSOT
                case 15: {
                            $actionlink = action('UserOTController@show',['id'=>$notif->detail->relatedModelID, 'notif'=>$notif->detail->id, 'seen'=>'true' ] ); 
                            $thereq =User_OT::find($notif->detail->relatedModelID);
                            if (is_null($thereq)){
                              $theBio = null;
                              $message=" filed a <strong>Pre-Shift OT</strong>";
                              //$coll->push(['approved'=>"Data Not Found", 'thereq'=>$thereq,'bio'=>$theBio, 'id'=>$notif->id]);
                            }
                            else{

                              if ($ownNotif){
                                if ($thereq->isApproved){
                                  $message = " <strong>approved</strong> your Pre-shift OT request";
                                }else $message = " <strong>denied</strong> your Pre-shift OT request. I'm sorry. <strong><i class='fa fa-meh-o'></i></strong> ";
                                
                              }
                              else{
                                //$theBio = Biometrics::where('productionDate', date('Y-m-d',strtotime($thereq->leaveStart)));
                                $theBio = Biometrics::find($thereq->biometrics_id);
                                $message = " filed a <strong>Pre-Shift OT</strong> for <span class='text-danger'> ". date('M d, Y', strtotime($theBio->productionDate))."</span>";


                              }


                              
                            }


                          }break; 

                // ML
                case 16: {
                            $actionlink = action('UserFamilyleaveController@show',['id'=>$notif->detail->relatedModelID, 'notif'=>$notif->detail->id, 'seen'=>'true' ] ); 
                            $thereq =User_Familyleave::find($notif->detail->relatedModelID);
                            if (is_null($thereq)){
                              $theBio = null;
                              $message=" filed a <strong>Maternity Leave</strong>";
                              //$coll->push(['approved'=>"Data Not Found", 'thereq'=>$thereq,'bio'=>$theBio, 'id'=>$notif->id]);
                            }
                            else{

                              if ($ownNotif){
                                if ($thereq->isApproved){
                                  $message = " <strong>approved</strong> your Maternity Leave request";
                                }else $message = " <strong>denied</strong> your Maternity Leave request. I'm sorry. <strong><i class='fa fa-meh-o'></i></strong> ";
                                
                              }
                              else{
                                $theBio = Biometrics::where('productionDate', date('Y-m-d',strtotime($thereq->leaveStart)));
                                $message = " filed a <strong>Maternity Leave</strong> for <span class='text-danger'> ". date('M d, Y', strtotime($thereq->leaveStart))."</span>";


                              }


                              
                            }


                          }break;

                // PL
                case 17: {
                            $actionlink = action('UserFamilyleaveController@show',['id'=>$notif->detail->relatedModelID, 'notif'=>$notif->detail->id, 'seen'=>'true' ] ); 
                            $thereq =User_Familyleave::find($notif->detail->relatedModelID);
                            if (is_null($thereq)){
                              $theBio = null;
                              $message=" filed a <strong>Paternity Leave</strong>";
                              //$coll->push(['approved'=>"Data Not Found", 'thereq'=>$thereq,'bio'=>$theBio, 'id'=>$notif->id]);
                            }
                            else{

                              if ($ownNotif){
                                if ($thereq->isApproved){
                                  $message = " <strong>approved</strong> your Paternity Leave request";
                                }else $message = " <strong>denied</strong> your Paternity Leave request. I'm sorry. <strong><i class='fa fa-meh-o'></i></strong> ";
                                
                              }
                              else{
                                $theBio = Biometrics::where('productionDate', date('Y-m-d',strtotime($thereq->leaveStart)));
                                $message = " filed a <strong>Paternity Leave</strong> for <span class='text-danger'> ". date('M d, Y', strtotime($thereq->leaveStart))."</span>";


                              }


                              
                            }


                          }break;

                // SPL
                case 18: {
                            $actionlink = action('UserFamilyleaveController@show',['id'=>$notif->detail->relatedModelID, 'notif'=>$notif->detail->id, 'seen'=>'true' ] ); 
                            $thereq =User_Familyleave::find($notif->detail->relatedModelID);
                            if (is_null($thereq)){
                              $theBio = null;
                              $message=" filed a <strong>Single-Parent Leave</strong>";
                              //$coll->push(['approved'=>"Data Not Found", 'thereq'=>$thereq,'bio'=>$theBio, 'id'=>$notif->id]);
                            }
                            else{

                              if ($ownNotif){
                                if ($thereq->isApproved){
                                  $message = " <strong>approved</strong> your Single-Parent Leave request";
                                }else $message = " <strong>denied</strong> your Single-Parent Leave request. I'm sorry. <strong><i class='fa fa-meh-o'></i></strong> ";
                                
                              }
                              else{
                                $theBio = Biometrics::where('productionDate', date('Y-m-d',strtotime($thereq->leaveStart)));
                                $message = " filed a <strong>Single-Parent Leave</strong> for <span class='text-danger'> ". date('M d, Y', strtotime($thereq->leaveStart))."</span>";


                              }


                              
                            }


                          }break; 

                // UNLOCK Productiondate
                case 19: {   
                            $actionlink = action('DTRController@seenzonedPD',['id'=>$notif->detail->id, 'seen'=>'true' ] ); 
                            $thereq = User_DTR::find($notif->detail->relatedModelID);//Biometrics::find($notif->detail->relatedModelID); //

                            if (count((array)$thereq) > 0)
                            {
                              $theBio=null;
                              ($ownNotif) ? $message = " Request DTR Unlock for ".$thereq->productionDate.". " : $message = " sent a <strong>DTR Production Date Unlock request</strong> for <span class='text-danger'> ". date('M d, Y', strtotime($thereq->productionDate))."</span>";

                            }else{
                              //$theBio = Biometrics::where('productionDate', date('Y-m-d',strtotime($thereq->productionDate)));
                              $thereq = Biometrics::find($notif->detail->relatedModelID); //

                              if (empty($thereq))
                              {
                                $message = "Requested DTR is now unlocked.";

                              }else
                              {
                                ($ownNotif) ? $message = " DTR entry for ".date('M d, Y',strtotime($thereq->productionDate))." is now unlocked. " : $message = " is requesting for <strong>DTR Sheet Unlock</strong>";

                              }

                              
                            }
                            
                            // $img = asset('public/img/employees/'.$fromData->id.'.jpg');
                            // $fromImage = '<img src="'.$img.'" class="user-image img-circle" alt="User Image" width="30"/> ';


                            

                            // if (is_null($thereq))
                            // $coll->push(["approved"=>"yes", 'thereq'=>$thereq,'bio'=>$theBio, 'id'=>$notif->id]);

                            // else
                            //   $coll->push(['approved'=>"not yet", 'thereq'=>$thereq,'bio'=>$theBio, 'id'=>$notif->id]);

                            break; 
                          }

                // VTO 
                case 21: {
                            $actionlink = action('UserVLController@showVTO',['id'=>$notif->detail->relatedModelID, 'notif'=>$notif->detail->id, 'seen'=>'true' ] ); 
                            $thereq =User_VTO::find($notif->detail->relatedModelID);
                            if (is_null($thereq)){
                              $theBio = null;
                              $message=" filed a <strong>VTO</strong>";
                              //$coll->push(['approved'=>"Data Not Found", 'thereq'=>$thereq,'bio'=>$theBio, 'id'=>$notif->id]);
                            }
                            else{

                              if ($ownNotif){
                                if ($thereq->isApproved){
                                  $message = " <strong>approved</strong> your VTO  request";
                                }else $message = " <strong>denied</strong> your VTO  request. I'm sorry. <strong><i class='fa fa-meh-o'></i></strong> ";
                                
                              }
                              else{
                                $theBio = Biometrics::where('productionDate', date('Y-m-d',strtotime($thereq->leaveStart)));
                                $message = " filed a <strong>VTO </strong> for <span class='text-danger'> ". date('M d, Y', strtotime($thereq->productionDate))."</span>";


                              }


                              
                            }

                        

                          }break;


            }

              //if (is_null($thereq->isApproved)){
                $notifT=NotifType::find($notif->detail->type);
                $yourNotifs->push(['id'=>$notif->id, 'seen'=> $notif->seen, 
                  'title'=> $notifT->title, 
                  'icon' => $notifT->icon, 
                  'from'=> $from,
                  'fromDataID'=>$fromDataID,
                  'fromImage'=>$fromImage,
                  'message' =>$message,
                  'position'=>$position,  
                  'campaign'=> $campaign,
                  'created_at'=>$notif->created_at->format('M d,Y'),
                  'ago'=>Carbon::now()->diffForHumans($notif->created_at, true), 
                  'actionlink'=>$actionlink]);

             // }
              

        }

        $allNotifs = $yourNotifs->groupBy('created_at');
        //return $allNotifs;
        // $coll = new Collection;
        // foreach ($allNotifs as $key) {
        //   $coll->push(count($key));

        // }

       // return $coll;
        

        $correct = Carbon::now('GMT+8'); //->timezoneName();

       if($this->user->id !== 564 ) {
          $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
            fwrite($file, "-------------------\n View All Notifs -- " . $correct->format('M d h:i A'). " by [". $this->user->id."] ".$this->user->lastname."\n");
            fclose($file);
        } 

        //return $allNotifs;

        return view('people.notification-index', compact('allNotifs'));
    }

    
}
