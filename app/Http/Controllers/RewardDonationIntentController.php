<?php

namespace OAMPI_Eval\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use OAMPI_Eval\Http\Requests;
use OAMPI_Eval\ActivityLog;
use OAMPI_Eval\Donation;
use OAMPI_Eval\DonationIntent;
use OAMPI_Eval\User;
use \Mail;
use \Response;

class RewardDonationIntentController extends Controller
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
      'contentheader_title' => "Manage Donation Intents",
      'active_page_nav' => 'rewards',
      'include_rewards_scripts' => TRUE,
      'include_datatables' => TRUE,
      'include_jqueryform' => TRUE,
      'items_per_page' => 50
    ];
    debug($data['active_page_nav']);
    return view('rewards/manage_donation_intents', $data);
  }

  public function export_claims(Request $request){
    $headers = array(
        "Content-type" => "text/csv",
        "Content-Disposition" => "attachment; filename=export.csv",
        "Pragma" => "no-cache",
        "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
        "Expires" => "0"
      );
    $orders = \DB::select(\DB::raw("
          SELECT
            voucher_claims.id as 'vid', voucher_claims.email as 'vemail', voucher_claims.phone as 'vphone',voucher_claims.redeemed, voucher_claims.denied,
            users.id as userid, users.employeeNumber as 'employee_number', UPPER(users.lastname) as 'last', UPPER(users.firstname) as 'first', users.nickname as 'nick',
            campaign.id as 'cid', campaign.name as 'campaign_name',
            vouchers.name as 'vname', voucher_claims.created_at as 'date_requested', voucher_claims.updated_at as 'date_processed'
          FROM
            voucher_claims
            LEFT JOIN users on voucher_claims.user_id = users.id
            LEFT JOIN user_leaders on user_leaders.user_id = users.id
            LEFT JOIN immediateHead_Campaigns on user_leaders.immediateHead_Campaigns_id = immediateHead_Campaigns.id
            LEFT JOIN campaign on immediateHead_Campaigns.campaign_id = campaign.id                    
            LEFT JOIN vouchers on voucher_claims.voucher_id = vouchers.id
          GROUP BY vid
          ORDER BY voucher_claims.created_at asc
                    
        "));
    $callback = function() use ($orders) {
        $file = fopen('php://output', 'w');
        
        $columns = ["Lastname","Firstname","Nickname","Campaign","Voucher","Email","Phone","Claimed","Denied","Date Requested","Date Processed"];
        fputcsv($file, $columns);
        foreach($orders as $order){
          $csvLine = [];
          $csvLine[] = $order->last; 
          $csvLine[] = $order->first; 
          $csvLine[] = $order->nick; 
          $csvLine[] = $order->campaign_name;
          $csvLine[] = $order->vname;
          $csvLine[] = $order->vemail;
          $csvLine[] = $order->vphone;
          $csvLine[] = ($order->redeemed==1) ? "YES" : "NO" ;
          $csvLine[] = ($order->denied==1) ? "YES" : "NO" ;
          $csvLine[] = $order->date_requested;
          $csvLine[] = $order->date_processed;
          fputcsv($file, $csvLine);          
        }        
        fclose($file);
      };   

    return Response::stream($callback, 200, $headers);
  }

  public function list_intents(Request $request,$show_redeemed = FALSE)
  {
      $columns = array('','name','quantity','cost');
    
      $skip = $request->input('start');
      $take = $request->input('length', $this->pagination_items);
      
      $fetch_all = $request->input('fetch_all', FALSE);
      $data = new \stdClass();      
      
      //if($show_redeemed ==TRUE){
        //$query = VoucherClaims::with('user','voucher')->where('redeemed','=','1')->orWhere('denied','=','1');
      //}else{
        $query = DonationIntent::with('user','donation')->where('status','=','unset');
      //}
      
      $order = $request->input('order.0.column',0);
      if( $order != 0 ){
        $query = $query->orderBy($columns[$order], $request->input('order.0.dir'));
      } else {
        $query = $query->orderBy('created_at', 'asc');
      }
      
      $query->skip($skip)->take($take);
      $data->data = $query->get();
      $data->iTotalRecords =  DonationIntent::count();
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

  public function deny_intent(Request $request){
    $error = false;
    $user_id = \Auth::user()->id;    
    
    $claim = DonationIntent::with('donation')->find($request->input('intent_id',0));
    $user = User::with('points')->find($claim->user_id); 
    $cost = $claim->donated_points;
    if($user->points()->increment('points', $cost)){
      $record = new ActivityLog;
      $record->initiator_id = $user_id;
      $record->target_id = $claim->user_id;
      $record->description = "Refunded points because the donation you intended for: ".$claim->donation->name." was denied";
      if($record->save()) {        
        $claim->status = 'denied';
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
        'message' => "Donation intent denied. ".$user->firstname . " " . $user->lastname. " has been refunded " . $cost . " points"
      ], 200);    
    }
    
  }

  public function approve_intent(Request $request){
    
    $claim = DonationIntent::find(Input::get('intent_id'));

    if($claim){
      $claim->status = 'approved';
      $claim->save();
      return response()->json([
        'success' => true,
        'message' => "Approved"
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
        'message' => "Invalid Donation Intent ID"
      ], 422);
    }

    
  }

}
