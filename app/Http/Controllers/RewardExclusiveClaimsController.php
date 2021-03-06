<?php

namespace OAMPI_Eval\Http\Controllers;

use Illuminate\Http\Request;
use OAMPI_Eval\Http\Requests;
use OAMPI_Eval\ActivityLog;
use OAMPI_Eval\RewardExclusiveClaim;
use OAMPI_Eval\RewardExlusive;
use OAMPI_Eval\User;
use \Mail;
use \Response;

class RewardExclusiveClaimsController extends Controller
{

  protected $pagination_items = 50;

  public function __construct()
  {
    $this->middleware('auth');
    $this->pagination_items = 50;
  }

  public function index()
  {
    $data = [
      'contentheader_title' => "Manage Exclusive Claims",
      'active_page_nav' => 'rewards',
      'include_rewards_scripts' => TRUE,
      'include_datatables' => TRUE,
      'include_jqueryform' => TRUE,
      'items_per_page' => 50
    ];
    debug($data['active_page_nav']);
    return view('rewards/manage_exclusive_claims', $data);
  }



  public function list_claims(Request $request,$show_redeemed = FALSE)
  {
      $columns = array('','name','campaign','cost');

      $skip = $request->input('start');
      $take = $request->input('length', $this->pagination_items);

      $fetch_all = $request->input('fetch_all', FALSE);
      $data = new \stdClass();

      $query = RewardExclusiveClaim::with('user','reward','reward.campaign')->where('status','=',"pending");
      $order = $request->input('order.0.column',0);
      if( $order != 0 ){
        $query = $query->orderBy($columns[$order], $request->input('order.0.dir'));
      } else {
        $query = $query->orderBy('created_at', 'asc');
      }

      $query->skip($skip)->take($take);
      $data->data = $query->get();
      $data->iTotalRecords =  RewardExclusiveClaim::count();
      $data->recordsFiltered = $data->iTotalRecords;

      return response()->json( $data );
  }

  public function deny_claim(Request $request, $reward_id = 0){
    $error = false;
    $user_id = \Auth::user()->id;
    $user = User::with('points')->find($user_id);
    $claim = RewardExclusiveClaim::with('reward')->find($reward_id);
    $cost = $claim->reward->cost;
    if($user->points()->increment('points', $cost)){
      $record = new ActivityLog;
      $record->initiator_id = $user_id;
      $record->target_id = $claim->user_id;
      $record->description = "Refunded points because the exclusive reward: ".$claim->reward->name." you tried to claim was denied.";
      if($record->save()) {
        $claim->status = "denied";
        $claim->approver_id = $user_id;
        if($claim->save()) {
          $error = false;
        }else{
          $user->points()->decrement('points',$cost);
          $record->delete();
          $error = true;
        }
      }else{
        $user->points()->decrement('points',$cost);
        $error = true;
      }
    }else{
      $error = true;
    }

    if($error){
      return response()->json([
        'success' => false,
        'message' => "could not refund points for " . $user->firstname . " " . $user->lastname
      ], 422);
    }else{
      return response()->json([
        'success' => true,
        'message' => "Exclusive Reward Claim succesfully denied. ".$user->firstname . " " . $user->lastname. " has been refunded " . $cost . " points"
      ], 200);
    }

  }

  public function approve_claim(Request $request,$reward_id = 0){

    $claim = RewardExclusiveClaim::find($reward_id);

    if($claim){
      $claim->status = "approved";
      $claim->approver_id = \Auth::user()->id;
      $claim->save();
      return response()->json([
        'success' => true,
        'message' => "Message Sent"
      ], 200);

      /*
      $attachment = $request->input('attachment',"");
      Mail::send('emails.voucher',
        [
          'voucher_name'=>$claim->voucher->name,
          'instructions' => $request->input('instructions',"")
        ],
        function ($m) use ($claim, $attachment) {

          $m->from('OES@openaccessbpo.net', 'OES-OAMPI Evaluation System');
          $m->to($claim->user->email, $claim->user->firstname." ".$claim->user->lastname)->subject('OAM Rewards - Voucher Claim');
          if($attachment!=""){
            $file = Input::file('photo');
            $tempDirectory = storage_path('temp');
            $fileName = $file->getClientOriginalName();
            $file->move($tempDirectory, $fileName);
            $pathToFile = $tempDirectory . '/' . $fileName;
            $m->attach($pathToFile);
          }

          $claim->redeemed = true;
          $claim->save();

          return response()->json([
            'success' => true,
            'message' => "Message Sent"
          ], 422);

        }
      );
      */
    }else{
      return response()->json([
        'success' => false,
        'message' => "Invalid Exclusive Reward Claim ID"
      ], 422);
    }


  }

}
