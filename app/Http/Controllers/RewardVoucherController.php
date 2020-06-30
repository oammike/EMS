<?php

namespace OAMPI_Eval\Http\Controllers;

use Illuminate\Http\Request;
use OAMPI_Eval\Http\Requests;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Input;
use OAMPI_Eval\Voucher;

class RewardVoucherController extends Controller
{
    protected $pagination_items = 50;

    public function __construct()
    {
      $this->middleware('auth');
      $this->pagination_items = 50;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $data = [
        'contentheader_title' => "Manage Voucher",
        'active_page_nav' => 'rewards',
        'include_rewards_scripts' => TRUE,
        'include_datatables' => TRUE,
        'include_jqueryform' => TRUE,
        'items_per_page' => 50
      ];
      debug($data['active_page_nav']);
      return view('rewards/manage_vouchers', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $this->validate($request, [
        'name'       => 'required',
        'cost' => 'required|numeric',
        'photo' => 'required|image',
        'quantity' => 'required|numeric'        
      ]);      
      
      // store
      $voucher = new Voucher;
      $voucher->name       = Input::get('name');
      $voucher->cost      = Input::get('cost');
      $voucher->quantity      = Input::get('quantity');      
      
      if($voucher->save()) {
        $file = Input::file('photo');
        $tempDirectory = storage_path('temp');
        $fileName = $file->getClientOriginalName();        
        $file->move($tempDirectory, $fileName);
      
        $pathToFile = $tempDirectory . '/' . $fileName;
        $media = $voucher->addMedia($pathToFile)->toMediaLibrary();
        $voucher->attachment_image = $media->getUrl();
        $voucher->save();
        return response()->json([
          'success' => true,
          'message' => 'voucher entry added'
        ], 200);
      }else{
        return response()->json([
          'success' => false,
          'message' => array('Could not write to database. Please try again later.')
        ], 422);
      }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
          'quantity' => 'required|numeric',
          'cost' => 'required|numeric'
        ]);
        // store
        $voucher = Voucher::find($id);
        $old_quantity = $voucher->quantity;
        $old_cost = $voucher->cost;
        $voucher->quantity       = Input::get('quantity');
        $voucher->cost       = Input::get('cost');
        if($reward->save()) {
          if($old_quantity!=$voucher->quantity || $old_cost!=$voucher->cost){
            $user_id = \Auth::user()->id;
            $record = new ActivityLog;
            $record->initiator_id       = $user_id;
            $record->target_id      = $user_id;
            $record->description = "changed ".$voucher->name."'s quantity to ".$voucher->quantity.", cost to: "+$voucher->cost;            
            $record->save(); 
          }
          return response()->json([
            'success' => true,
            'message' => 'voucher successfully updated'
          ], 200);
        }else{
          return response()->json([
            'success' => false,
            'message' => array('Could not write to database. Please try again later.')
          ], 422);
        }        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $voucher = Voucher::find($id);        
        
        if($voucher->delete()){
          return response()->json([
            'success' => true,
            'message' => 'voucher category successfully deleted'
          ], 200);
        }else{
          return response()->json([
            'success' => false,
            'message' => 'Could not write to database. Please try again later.'
          ], 422);
        }
    }

    public function list_vouchers(Request $request,$page = 0)
    {
      $columns = array('','name','quantity','cost');
    
      $skip = $request->input('start');
      $take = $request->input('length', $this->pagination_items);
      
      $fetch_all = $request->input('fetch_all', FALSE);
      $data = new \stdClass();      
      
      $order = $request->input('order.0.column',0);
      if( $order != 0 ){
        $query = Voucher::orderBy($columns[$order], $request->input('order.0.dir'));
      } else {
        $query = Voucher::orderBy('name', 'asc');
      }
      
      $query->skip($skip)->take($take);
      $data->data = $query->get();
      $data->iTotalRecords =  Voucher::count();
      $data->recordsFiltered = $data->iTotalRecords;
      
      return response()->json( $data );
    }
}
