<?php

namespace OAMPI_Eval\Http\Controllers;

use Illuminate\Http\Request;
use OAMPI_Eval\Http\Requests;
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
      $query = VoucherClaims::with('user','voucher')->where('redeemed','=',0);
      
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
