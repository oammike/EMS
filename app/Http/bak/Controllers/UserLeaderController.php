<?php

namespace OAMPI_Eval\Http\Controllers;

use Illuminate\Http\Request;
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
use OAMPI_Eval\User_CWS;
use OAMPI_Eval\Notification;
use OAMPI_Eval\User_Notification;
use OAMPI_Eval\User_Leader;

class UserLeaderController extends Controller
{
    protected $user;
   	protected $user_leader;
    use Traits\TimekeepingTraits;
    use Traits\UserTraits;



     public function __construct(User_Leader $user_leader)
    {
        $this->middleware('auth');
        $this->user_leader = $user_leader;
        $this->user =  User::find(Auth::user()->id);
    }

    public function store(Request $request)
    {
    	$leaders = Input::get('leader'); 
    	$apply = $request->applySame;
        $userID = Input::get('user_id');
        $user = User::find($userID);
        $teammates = Input::get('teammates');



    	if (empty($leaders)){
    		if (count($user->approvers) > 0){ 
                foreach ($user->approvers as $key) {

                    DB::table('user_leaders')->where('immediateHead_Campaigns_id',$key->id)->where('user_id',$user->id)->delete();
                    
               }
            }
            return back();

    	} else{

    		//----do it for the rest of the team
            if ($apply !== null) { 
                foreach ($teammates as $team) {
                    $emp = User::find($team);
                    foreach ($emp->approvers as $key) {

                        DB::table('user_leaders')->where('immediateHead_Campaigns_id',$key->id)->where('user_id',$emp->id)->delete();
                    }

                    foreach ($leaders as $key) {
                        $approver = new User_Leader;
                        $approver->immediateHead_Campaigns_id = $key;
                        $approver->user_id = $emp->id;
                        $approver->save();
                    }
                }

                foreach ($user->approvers as $key) {
                    // if ( !in_array($key->id, $leaders) )
                    // {
                        DB::table('user_leaders')->where('immediateHead_Campaigns_id',$key->id)->where('user_id',$user->id)->delete();
                        
                   // }
                }

                foreach ($leaders as $key) {
                    $approver = new User_Leader;
                    $approver->immediateHead_Campaigns_id = $key;
                    $approver->user_id = $user->id;
                    $approver->save();
                }
                
            return back();    

            } 
            else
            {
                
                foreach ($user->approvers as $key) {
                    // if ( !in_array($key->id, $leaders) )
                    // {
                        DB::table('user_leaders')->where('immediateHead_Campaigns_id',$key->id)->where('user_id',$user->id)->delete();
                        
                   // }
                }

                foreach ($leaders as $key) {
                    $approver = new User_Leader;
                    $approver->immediateHead_Campaigns_id = $key;
                    $approver->user_id = $user->id;
                    $approver->save();
                }

            }
            

    	}
        return back();

    }

    public function destroy($id)
    {
      $this->user_leader->destroy($id);
      return back();

    }
}
