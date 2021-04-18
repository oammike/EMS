<?php

namespace OAMPI_Eval\Http\Controllers;

use Illuminate\Http\Request;
use OAMPI_Eval\Http\Requests;
use OAMPI_Eval\ActivityLog;
use OAMPI_Eval\Voucher;
use OAMPI_Eval\VoucherClaims;
use OAMPI_Eval\User;
use \Mail;
use \Response;

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

  public function export_voucher_stats(Request $request){
    $reference_date = new \DateTime('NOW');
    $dt = new \DateTime($reference_date->format('Y-m-d'));
    $firstday = $dt->format('Y-01-01 00:00:00');
    $lastday = $dt->format('Y-m-t 23:59:59');
    $endtimestamp = strtotime($lastday);
    $starttimestamp = strtotime($firstday);


    $awards = \DB::select(\DB::raw("
          SELECT
            user_id, points, MONTH(created_at) AS `month`
          FROM reward_award
          WHERE
            created_at > FROM_UNIXTIME(".$starttimestamp.")
            AND created_at < FROM_UNIXTIME(".$endtimestamp.")
          ORDER BY user_id ASC, created_at ASC
        "));


    $users = \DB::select(\DB::raw("
          SELECT
            users.id as userid, UPPER(users.firstname) as 'first', UPPER(users.lastname) as 'last',
            campaign.id as 'cid', campaign.name as 'program',
            points.points as 'unused_points'

          FROM
            users
            LEFT JOIN user_leaders on user_leaders.user_id = users.id
            LEFT JOIN immediateHead_Campaigns on user_leaders.immediateHead_Campaigns_id = immediateHead_Campaigns.id
            LEFT JOIN campaign on immediateHead_Campaigns.campaign_id = campaign.id
            LEFT JOIN points on users.id = points.idnumber
          WHERE users.status_id not in (7, 8, 9, 16)
          ORDER BY users.id asc
        "));

    $claims_raw = \DB::select(\DB::raw("
          SELECT
            voucher_claims.user_id, vouchers.cost, MONTH( voucher_claims.created_at ) AS `month`
          FROM
            voucher_claims
            LEFT JOIN vouchers on voucher_claims.voucher_id = vouchers.id
          WHERE voucher_claims.created_at > FROM_UNIXTIME(". $starttimestamp .") AND voucher_claims.created_at < FROM_UNIXTIME(".$endtimestamp.")
          ORDER BY voucher_claims.user_id ASC, voucher_claims.created_at ASC
    "));

    $earnings = [];
    $max_month = 0;
    foreach($awards as $award){
      $earnings[$award->user_id][$award->month] = $award->points;
      if(intval($award->month)>$max_month){ $max_month = intval($award->month); }
    }

    $claims = [];
    foreach($claims_raw as $claim){
      $claims[$claim->user_id][$claim->month] = $claim->cost;
    }


    $headers = array(
        "Content-type" => "text/csv",
        "Content-Disposition" => "attachment; filename=Reward_Voucher_Points_Report.csv",
        "Pragma" => "no-cache",
        "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
        "Expires" => "0"
      );

    $callback = function() use ($users,$earnings,$claims,$max_month) {
        $months = ["JAN", "FEB", "MAR", "APR", "MAY", "JUN", "JUL", "AUG", "SEPT", "OCT", "NOV", "DEC"];
        $file = fopen('php://output', 'w');

        $columns = ["EMS ID","FIRSTNAME","LASTNAME","PROGRAM","Total Unused Points"];
        $i = 0;
        while($i < $max_month){
          $columns[] = $months[$i] . " earnings";
          $i++;
        }
        $columns[] = "Total Points Earned";
        $i = 0;
        while($i < $max_month){
          $columns[] = $months[$i] . " Voucher Claims";
          $i++;
        }
        $columns[] = "Total Voucher Claims";

        fputcsv($file, $columns);
        /*
        users.id as userid, users.employeeNumber as 'employee_number', UPPER(users.firstname) as 'first', UPPER(users.lastname) as 'last',
            campaign.id as 'cid', campaign.name as 'program',
            points.points as 'unused_points'
        */
        foreach($users as $user){
          $csvLine = [];
          $csvLine[] = $user->userid;
          $csvLine[] = $user->first;
          $csvLine[] = $user->last;
          $csvLine[] = $user->program;
          $csvLine[] = $user->unused_points;

          $i = 0;
          $total = 0;
          while($i < $max_month){
            $earned = isset($earnings[$user->userid][$i+1]) ? $earnings[$user->userid][$i+1] : 0;
            $total = $total + $earned;
            $csvLine[] = $earned;
            $i = $i + 1;
          }
          $csvLine[] = $total;

          $i = 0;
          $total = 0;
          while($i < $max_month){
            $earned = isset($claims[$user->userid][$i+1]) ? $claims[$user->userid][$i+1] : 0;
            $total = $total + $earned;
            $csvLine[] = $earned;
            $i = $i + 1;
          }
          $csvLine[] = $total;

          fputcsv($file, $csvLine);
        }
        fclose($file);
      };

    return Response::stream($callback, 200, $headers);
  }

  public function list_claims(Request $request,$show_redeemed = FALSE)
  {
      $columns = array('','name','quantity','cost');

      $skip = $request->input('start');
      $take = $request->input('length', $this->pagination_items);

      $fetch_all = $request->input('fetch_all', FALSE);
      $data = new \stdClass();

      //if($show_redeemed ==TRUE){
        //$query = VoucherClaims::with('user','voucher')->where('redeemed','=','1')->orWhere('denied','=','1');
      //}else{
        $query = VoucherClaims::with('user','voucher')->where('redeemed','=',0)->where('denied','=',0);
      //}

      $order = $request->input('order.0.column',0);
      if( $order != 0 ){
        $query = $query->orderBy($columns[$order], $request->input('order.0.dir'));
      } else {
        $query = $query->orderBy('created_at', 'asc');
      }

      $query->skip($skip)->take($take);
      $data->data = $query->get();
      $data->iTotalRecords =  $data->data->count();

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
