<?php

namespace OAMPI_Eval\Http\Controllers;

use Illuminate\Http\Request;
use OAMPI_Eval\Http\Requests;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Input;
use OAMPI_Eval\Donation;
use OAMPI_Eval\ActivityLog;

class RewardDonationsController extends Controller
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
        'contentheader_title' => "Manage Donations",
        'active_page_nav' => 'rewards',
        'include_rewards_scripts' => TRUE,
        'include_datatables' => TRUE,
        'include_jqueryform' => TRUE,
        'items_per_page' => 50
      ];
      debug($data['active_page_nav']);
      return view('rewards/manage_donation_categories', $data);
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
        'description'       => 'required',
        //'point_value' => 'required|numeric',
        'minimum' => 'numeric',
        'attachment_image' => 'required|image'
      ]);      
      
      // store
      $donation = new Donation;
      $donation->name       = Input::get('name');
      $donation->description      = Input::get('description');
      //$donation->point_value      = Input::get('point_value');
      $donation->minimum      = Input::get('minimum');
      
      if($donation->save()) {
        $file = Input::file('attachment_image');
        $tempDirectory = storage_path('temp');
        $fileName = $file->getClientOriginalName();        
        $file->move($tempDirectory, $fileName);
      
        $pathToFile = $tempDirectory . '/' . $fileName;
        $media = $donation->addMedia($pathToFile)->toMediaLibrary();
        $donation->attachment_image = $media->getUrl();
        $donation->save();
        return response()->json([
          'success' => true,
          'message' => 'donation entry added'
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
          'description' => 'required',
          //'point_value' => 'required|numeric',
          'minimum' => 'numeric'
        ]);
        // store
        $donation = Donation::find($id);
        //$old_value = $donation->point_value;
        $old_minimum = $donation->minimum;
        //$donation->point_value       = Input::get('point_value');
        $donation->minimum       = Input::get('minimum');
        if($donation->save()) {
          //if($old_value!=$donation->point_value || $old_minimum!=$donation->minimum){
          if($old_minimum!=$donation->minimum){
            $user_id = \Auth::user()->id;
            $record = new ActivityLog;
            $record->initiator_id       = $user_id;
            $record->target_id      = $user_id;
            $record->description = "changed ".$donation->name."'s minimum to: ".$donation->minimum;            
            $record->save(); 
          }
          return response()->json([
            'success' => true,
            'message' => 'donation successfully updated'
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
        $donation = Donation::find($id);        
        
        if($donation->delete()){
          return response()->json([
            'success' => true,
            'message' => 'donation entry successfully deleted'
          ], 200);
        }else{
          return response()->json([
            'success' => false,
            'message' => 'Could not write to database. Please try again later.'
          ], 422);
        }
    }

    public function list_donations(Request $request,$page = 0)
    {
      $columns = array('','name','minimum');
    
      $skip = $request->input('start');
      $take = $request->input('length', $this->pagination_items);
      
      $fetch_all = $request->input('fetch_all', FALSE);
      $data = new \stdClass();      
      
      $order = $request->input('order.0.column',0);
      if( $order != 0 ){
        $query = Donation::orderBy($columns[$order], $request->input('order.0.dir'));
      } else {
        $query = Donation::orderBy('name', 'asc');
      }
      
      $query->skip($skip)->take($take);
      $data->data = $query->get();
      $data->iTotalRecords =  Donation::count();
      $data->recordsFiltered = $data->iTotalRecords;
      
      return response()->json( $data );
    }
}
