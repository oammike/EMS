<?php

namespace OAMPI_Eval\Http\Controllers;

use OAMPI_Eval\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Input;
use OAMPI_Eval\Reward;
use OAMPI_Eval\RewardCategory;
use OAMPI_Eval\ActivityLog;
class RewardController extends Controller
{
    protected $pagination_items = 50;

    public function __construct()
    {
      $this->middleware('auth');
      $this->pagination_items = 50;
    }
    
    /**
     * get list of the resource in JSON.
     *
     * @return \Illuminate\Http\Response
     */
    public function list_json($page)
    {
      
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $data = [
        'contentheader_title' => "Manage Rewards",
        'active_page_nav' => 'rewards',
        'include_rewards_scripts' => TRUE,
        'include_datatables' => TRUE,
        'include_jqueryform' => TRUE,
        'items_per_page' => 50,
        'categories'  => RewardCategory::all()
      ];
      debug($data['active_page_nav']);
      return view('rewards/manage_rewards', $data);
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
        'description'      => 'required',
        'preptime' => 'required|numeric',
        'cost' => 'required|numeric',
        'photo' => 'required|image'
        
      ]);
      $category_id = Input::get('category_id');
      if($category_id!=0){
        $category = RewardCategory::find($category_id);
        if(!$category){
          return response()->json(['category'=>'Your selected reward category does not exist.'], 422);
        }
      }else{
        return response()->json(['category'=>'Please select a proper category.'], 422);
      }
      
      // store
      $reward = new Reward;
      $reward->name       = Input::get('name');
      $reward->description      = Input::get('description');
      $reward->preptime      = Input::get('preptime');
      $reward->cost      = Input::get('cost');
      $reward->category_id      = $category_id;
      
      
      if($reward->save()) {
        $file = Input::file('photo');
        $tempDirectory = storage_path('temp');
        $fileName = $file->getClientOriginalName();        
        $file->move($tempDirectory, $fileName);
      
        $pathToFile = $tempDirectory . '/' . $fileName;
        $media = $reward->addMedia($pathToFile)->toMediaLibrary();
        $reward->attachment_image = $media->getUrl();
        $reward->save();
        return response()->json([
          'success' => true,
          'message' => 'reward entry added'
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
          'prep_time' => 'required|numeric',
          'description' => 'required',
        ]);
        
        $category_id = Input::get('category_id');
        if($category_id!=0){
          $category = RewardCategory::find($category_id);
          if(!$category){
            return response()->json([
              'success' => false,
              'message' => array('category'=>'Your selected reward category does not exist.')
            ], 422);
          }
        }
        
        // store
        $reward = Reward::find($id);
        $old_cost = $reward->prep_time;
        $reward->category_id = $category_id;

        $reward->preptime       = Input::get('prep_time');
        $reward->description      = Input::get('description');
        if($reward->save()) {
          if($old_cost!=$reward->prep_time){
            $user_id = \Auth::user()->id;
            $record = new ActivityLog;
            $record->initiator_id       = $user_id;
            $record->target_id      = $user_id;
            $record->description = "changed ".$reward->name."'s preparation time to ".$reward->prep_time;
            
            $record->save(); 
          }
          return response()->json([
            'success' => true,
            'message' => 'reward successfully updated'
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
        $reward = Reward::find($id);        
        
        if($reward->delete()){
          return response()->json([
            'success' => true,
            'message' => 'reward category successfully deleted'
          ], 200);
        }else{
          return response()->json([
            'success' => false,
            'message' => 'Could not write to database. Please try again later.'
          ], 422);
        }
    }
    
    public function list_rewards(Request $request,$page = 0)
    {
      $columns = array('','name','description','preptime');
    
      $skip = $request->input('start');
      $take = $request->input('length', $this->pagination_items);
      
      $fetch_all = $request->input('fetch_all', FALSE);
      $data = new \stdClass();    
      $query = Reward::with('category');
      
      $order = $request->input('order.0.column',0);
      if( $order != 0 ){
        $query->orderBy($columns[$order], $request->input('order.0.dir'));
      } else {
        $query->orderBy('id', 'asc');
      }
      
      $query->skip($skip)->take($take);
      $data->data = $query->get();
      $data->iTotalRecords =  Reward::count();
      $data->recordsFiltered = $data->iTotalRecords;
      
      return response()->json( $data );
    }
}
