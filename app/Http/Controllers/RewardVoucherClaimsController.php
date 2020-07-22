<?php

namespace OAMPI_Eval\Http\Controllers;

use Illuminate\Http\Request;
use OAMPI_Eval\Http\Requests;
use OAMPI_Eval\ActivityLog;
use OAMPI_Eval\Voucher;
use OAMPI_Eval\VoucherClaims;
use OAMPI_Eval\User;
use \Mail;

class RewardVoucherClaimsController extends Controller
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
      'contentheader_title' => "Manage Voucher Claims",
      'active_page_nav' => 'rewards',
      'include_rewards_scripts' => TRUE,
      'include_datatables' => TRUE,
      'include_jqueryform' => TRUE,
      'items_per_page' => 50
    ];
    debug($data['active_page_nav']);
    return view('rewards/manage_voucher_claims', $data);
  }

  public function list_claims(Request $request,$page = 0)
  {
      $columns = array('','name','quantity','cost');
    
      $skip = $request->input('start');
      $take = $request->input('length', $this->pagination_items);
      
      $fetch_all = $request->input('fetch_all', FALSE);
      $data = new \stdClass();      
      $query = VoucherClaims::with('user','voucher')->where('redeemed','=',0)->where('denied','=',0);
      
      $order = $request->input('order.0.column',0);
      if( $order != 0 ){
        $query = $query->orderBy($columns[$order], $request->input('order.0.dir'));
      } else {
        $query = $query->orderBy('created_at', 'asc');
      }
      
      $query->skip($skip)->take($take);
      $data->data = $query->get();
      $data->iTotalRecords =  VoucherClaims::count();
      $data->recordsFiltered = $data->iTotalRecords;
      
      return response()->json( $data );


      /*
      $orders = DB::select(DB::raw("
                  SELECT
                    voucher_claims.id as 'vid', voucher_claims.email as 'vemail', voucher_claims.phone as 'vphone',
                    users.id as userid, users.employeeNumber as 'employee_number', UPPER(users.lastname) as 'last', UPPER(users.firstname) as 'first', users.nickname as 'nick',
                    campaign.id as 'cid', campaign.name as 'campaign_name',
                    vouchers.name as 'vname', vouchers.attachment_image as 'attachment_image'
                  FROM
                    voucher_claims
                    LEFT JOIN users on voucher_claims.user_id = users.id
                    LEFT JOIN user_leaders on user_leaders.user_id = users.id
                    LEFT JOIN immediateHead_Campaigns on user_leaders.immediateHead_Campaigns_id = immediateHead_Campaigns.id
                    LEFT JOIN campaign on immediateHead_Campaigns.campaign_id = campaign.id                    
                    LEFT JOIN vouchers on voucher_claims.voucher_id = vouchers.id
                  WHERE voucher_claims.redeemed = 0
                  ORDER BY voucher_claims.created_at asc
                  GROUP BY vid
                "));
      */
  }

  public function deny_claim(Request $request, $reward_id = 0){
    $error = false;
    $user_id = \Auth::user()->id;
    $user = User::with('points')->find($user_id);
    $claim = VoucherClaims::with('voucher')->find($reward_id);
    $cost = $claim->voucher->cost;
    if($user->points()->increment('points', $cost)){
      $record = new ActivityLog;
      $record->initiator_id = $user_id;
      $record->target_id = $user_id;
      $record->description = "Refunded points because the voucher: ".$claim->voucher->name." you tried to claim was denied";
      if($record->save()) {
        $claim = VoucherClaims::find($reward_id);        
        $claim->denied = 1;
        if($claim->save()){
          $error = false;
        }else{
          $record->delete();
          $user->points()->decrement('points',$cost);
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
        'message' => "Voucher claim succesfully denied. ".$user->firstname . " " . $user->lastname. " has been refunded " . $cost . " points"
      ], 200);    
    }
    
  }

  public function confirm_claim(Request $request,$reward_id = 0){
    
    $claim = VoucherClaims::find($reward_id);

    if($claim){
      $claim->redeemed = true;
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
        'message' => "Invalid Voucher Claim ID"
      ], 422);
    }

    
  }

}
