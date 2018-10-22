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
use OAMPI_Eval\User_OT;
use OAMPI_Eval\User_Notification;
use OAMPI_Eval\MonthlySchedules;
use OAMPI_Eval\FixedSchedules;
use OAMPI_Eval\NotifType;

trait UserTraits
{

	public function getApprovalNotifs()
	{
	   $unseenNotifs = User_Notification::where('user_id',$this->user->id)->where('seen',false)->orderBy('created_at','DESC')->get(); 
       $coll = new Collection;
       $forApprovals = new Collection;
       $approvalTypes = [6,7,8,9];

       foreach( $unseenNotifs as $notif){ 
                      $detail = $notif->detail;

                      if ( in_array($detail->type, $approvalTypes) )
                      {
                        $emp = User::find($detail->from);
                        switch ($detail->type) {
                          case 6: //CWS
                                  {
                                    $cws = User_CWS::find($detail->relatedModelID);
                                    if (count($cws)>0)
                                    $forApprovals->push(['user'=>$emp->firstname . " ". $emp->lastname, 
                                                  'user_id'=>$notif->user_id, 'id'=>$notif->id, 
                                                  'type'=>NotifType::find($detail->type)->title, 
                                                  'typeID'=>$detail->type,
                                                  'created_at'=> $detail->created_at->format('M d, Y'),
                                                  'productionDate'=>date('M d, Y', strtotime(Biometrics::find($cws->biometrics_id)->productionDate)),
                                                  'productionDay'=>date('D', strtotime(Biometrics::find($cws->biometrics_id)->productionDate)),
                                                  'deets'=> $cws]);
                                   else
                                       $forApprovals->push(['user'=>$emp->firstname . " ". $emp->lastname,  
                                                  'user_id'=>$notif->user_id, 'id'=>$notif->id, 
                                                  'type'=>NotifType::find($detail->type)->title,
                                                  'typeID'=>$detail->type, 
                                                  'created_at'=> $detail->created_at->format('M d, Y'),
                                                  'productionDate'=>null,
                                                   'productionDay'=>null,
                                                  'deets'=> null]);

                                   

                                  }break;
                          case 7: //OT
                                  {
                                    $ots = User_OT::find($detail->relatedModelID);
                                    if (count($ots)>0)
                                    $forApprovals->push(['user'=>$emp->firstname . " ". $emp->lastname, 
                                                  'user_id'=>$notif->user_id, 'id'=>$notif->id, 
                                                  'type'=>NotifType::find($detail->type)->title,
                                                  'typeID'=>$detail->type, 
                                                  'created_at'=> $detail->created_at->format('M d, Y'),
                                                  'productionDate'=>date('M d,Y', strtotime(Biometrics::find($ots->biometrics_id)->productionDate)),
                                                   'productionDay'=>date('D', strtotime(Biometrics::find($ots->biometrics_id)->productionDate)),
                                                  'deets'=> $ots]);
                                  else

                                       $forApprovals->push(['user'=>$emp->firstname . " ". $emp->lastname,  
                                                  'user_id'=>$notif->user_id, 'id'=>$notif->id, 
                                                  'type'=>NotifType::find($detail->type)->title,
                                                  'typeID'=>$detail->type, 
                                                  'created_at'=> $detail->created_at->format('M d, Y'),
                                                  'productionDate'=>null,
                                                   'productionDay'=>null,
                                                  'deets'=> null]);

                                  }break;
                          case 8: //LOGIN
                                  {
                                    $in = User_DTRP::find($detail->relatedModelID);
                                    if (count($in)>0)
                                    $forApprovals->push(['user'=>$emp->firstname . " ". $emp->lastname, 
                                                  'user_id'=>$notif->user_id, 'id'=>$notif->id, 
                                                  'type'=>NotifType::find($detail->type)->title, 
                                                  'typeID'=>$detail->type,
                                                  'created_at'=> $detail->created_at->format('M d, Y'),
                                                  'productionDate'=>date('M d,Y', strtotime(Biometrics::find($in->biometrics_id)->productionDate)),
                                                   'productionDay'=>date('D', strtotime(Biometrics::find($in->biometrics_id)->productionDate)),
                                                  'deets'=> $in]);
                                  else
                                       $forApprovals->push(['user'=>$emp->firstname . " ". $emp->lastname,  
                                                  'user_id'=>$notif->user_id, 'id'=>$notif->id, 
                                                  'type'=>NotifType::find($detail->type)->title,
                                                  'typeID'=>$detail->type, 
                                                  'created_at'=> $detail->created_at->format('M d, Y'),
                                                  'productionDate'=>null,
                                                   'productionDay'=>null,
                                                  'deets'=> null]);


                                  }break;
                          case 9: //LOGOUT
                                  {
                                    $out = User_DTRP::find($detail->relatedModelID);
                                    if (count($out)>0)
                                    $forApprovals->push(['user'=>$emp->firstname . " ". $emp->lastname,  
                                                  'user_id'=>$notif->user_id, 'id'=>$notif->id, 
                                                  'type'=>NotifType::find($detail->type)->title,
                                                  'typeID'=>$detail->type, 
                                                  'created_at'=> $detail->created_at->format('M d, Y'),
                                                  'productionDate'=>date('M d,Y', strtotime(Biometrics::find($out->biometrics_id)->productionDate)),
                                                   'productionDay'=>date('D', strtotime(Biometrics::find($out->biometrics_id)->productionDate)),
                                                  'deets'=> $out]);
                                     else
                                       $forApprovals->push(['user'=>$emp->firstname . " ". $emp->lastname,  
                                                  'user_id'=>$notif->user_id, 'id'=>$notif->id, 
                                                  'type'=>NotifType::find($detail->type)->title,
                                                  'typeID'=>$detail->type, 
                                                  'created_at'=> $detail->created_at->format('M d, Y'),
                                                  'productionDate'=>null,
                                                   'productionDay'=>null,
                                                  'deets'=> null]);

                                  }break;
                          
                          
                        }
                        
                      }
                      //$coll->push(['relatedID'=> $notif->detail]);
                      //$forApprovals->push(['item'=>])
        }

       

       //----- END NOTIF

        return $forApprovals;
	}

  public function getDashboardNotifs()
  {
       $notifs = User_Notification::where('user_id',$this->user->id)->orderBy('created_at','DESC')->get(); 
       $coll = new Collection;
       $forApprovals = new Collection;
       $approvalTypes = [6,7,8,9];

       foreach( $notifs as $notif){ 
                      $detail = $notif->detail;

                      if ( in_array($detail->type, $approvalTypes) )
                      {
                        $emp = User::find($detail->from);
                        switch ($detail->type) {
                          case 6: //CWS
                                  {
                                    $cws = User_CWS::find($detail->relatedModelID);
                                    if (count($cws) > 0)
                                    {
                                      if (is_null($cws->isApproved) )
                                      $forApprovals->push(['user'=>$emp->firstname . " ". $emp->lastname, 
                                                    'user_id'=>$notif->user_id, 'id'=>$notif->id, 
                                                    'type'=>NotifType::find($detail->type)->title, 
                                                    'typeID'=>$detail->type,
                                                    'created_at'=> $detail->created_at->format('M d, Y'),
                                                    'productionDate'=>date('M d, Y', strtotime(Biometrics::find($cws->biometrics_id)->productionDate)),
                                                    'productionDay'=>date('D', strtotime(Biometrics::find($cws->biometrics_id)->productionDate)),
                                                    'deets'=> $cws]);

                                    }
                                    
                                   

                                  }break;
                          case 7: //OT
                                  {
                                    $ots = User_OT::find($detail->relatedModelID);
                                    if (count($ots) > 0)
                                    {
                                        if (is_null($ots->isApproved))
                                        $forApprovals->push(['user'=>$emp->firstname . " ". $emp->lastname, 
                                                      'user_id'=>$notif->user_id, 'id'=>$notif->id, 
                                                      'type'=>NotifType::find($detail->type)->title,
                                                      'typeID'=>$detail->type, 
                                                      'created_at'=> $detail->created_at->format('M d, Y'),
                                                      'productionDate'=>date('M d,Y', strtotime(Biometrics::find($ots->biometrics_id)->productionDate)),
                                                       'productionDay'=>date('D', strtotime(Biometrics::find($ots->biometrics_id)->productionDate)),
                                                      'deets'=> $ots]);

                                    }
                                    
                                  }break;
                          case 8: //LOGIN
                                  {
                                    $in = User_DTRP::find($detail->relatedModelID);
                                    if (count($in) > 0)
                                    { 
                                        if (is_null($in->isApproved))
                                        $forApprovals->push(['user'=>$emp->firstname . " ". $emp->lastname, 
                                                      'user_id'=>$notif->user_id, 'id'=>$notif->id, 
                                                      'type'=>NotifType::find($detail->type)->title, 
                                                      'typeID'=>$detail->type,
                                                      'created_at'=> $detail->created_at->format('M d, Y'),
                                                      'productionDate'=>date('M d,Y', strtotime(Biometrics::find($in->biometrics_id)->productionDate)),
                                                       'productionDay'=>date('D', strtotime(Biometrics::find($in->biometrics_id)->productionDate)),
                                                      'deets'=> $in]);
                                    }

                                  }break;
                          case 9: //LOGOUT
                                  {
                                    $out = User_DTRP::find($detail->relatedModelID);
                                    if (count($out) > 0) {
                                      if (is_null($out->isApproved))
                                      $forApprovals->push(['user'=>$emp->firstname . " ". $emp->lastname,  
                                                    'user_id'=>$notif->user_id, 'id'=>$notif->id, 
                                                    'type'=>NotifType::find($detail->type)->title,
                                                    'typeID'=>$detail->type, 
                                                    'created_at'=> $detail->created_at->format('M d, Y'),
                                                    'productionDate'=>date('M d,Y', strtotime(Biometrics::find($out->biometrics_id)->productionDate)),
                                                     'productionDay'=>date('D', strtotime(Biometrics::find($out->biometrics_id)->productionDate)),
                                                    'deets'=> $out]);

                                    }
                                    

                                  }break;
                          
                          
                        }
                        
                      }
                      //$coll->push(['relatedID'=> $notif->detail]);
                      //$forApprovals->push(['item'=>])
        }

       

       //----- END NOTIF

        return $forApprovals;
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
}

?>