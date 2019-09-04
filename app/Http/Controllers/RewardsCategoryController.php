<?php

namespace OAMPI_Eval\Http\Controllers;

use Illuminate\Http\Request;
use OAMPI_Eval\Http\Requests;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Input;
use OAMPI_Eval\RewardCategory;
use OAMPI_Eval\RewardCategoryTier;
use OAMPI_Eval\ActivityLog;
use DB;

class RewardsCategoryController extends Controller
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
        'contentheader_title' => "Manage Reward Categories",
        'active_page_nav' => 'rewards',
        'include_datatables' => TRUE,
        'include_jqueryform' => TRUE,
        'items_per_page' => $this->pagination_items
      ];
      debug($data['active_page_nav']);
      return view('rewards/manage_rewardcategories', $data);
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
        'name'       => 'required'        
      ]);
      // store
      $category = new RewardCategory;
      $category->name       = Input::get('name');

      if($category->save()) {
        
        
        $tiers = Input::get('tier');
        $costs = Input::get('cost');
        
        if(count($tiers)!=count($costs)){
          $category->delete();
          return response()->json([
            'success' => false,
            'message' => array('Please fill out the variants field properly.')
          ], 422);
          
        }else{
          DB::beginTransaction();

          foreach($tiers as $key=>$current_tier){
            if(ctype_digit($costs[$key])){
              $tier = new RewardCategoryTier;
              $tier->description = $current_tier;
              $tier->cost = $costs[$key];
              $tier->category_id = $category->id;
              $tier->save();
            }else{
              DB::rollBack();
              $category->delete();
              return response()->json([
                'success' => false,
                'message' => 'Tier '.$key.' has an invalid price value. Please enter only numbers (no commas or periods).'
              ], 422);
            }
          }
          DB::commit();

          return response()->json([
            'success' => true,
            'message' => 'reward category added'
          ], 200);
        }
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
          'name' => 'required'
        ]);
        // store
        $category = RewardCategory::find($id);
        $old_name = $category->name;

        $category->name       = Input::get('name');
        if($category->save()) {
          if($old_name!=$category->name){
            $user_id = \Auth::user()->id;
            $record = new ActivityLog;
            $record->initiator_id       = $user_id;
            $record->target_id      = $user_id;
            $record->description = "renamed category: ".$old_name." to ".$category->name;
            $record->save();
          }
          
          $tiers = Input::get('tier');
          $costs = Input::get('cost');
          
          DB::beginTransaction();
          RewardCategoryTier::where('category_id',$category->id)->delete();

          foreach($tiers as $key=>$current_tier){
            if(ctype_digit($costs[$key])){
              $tier = new RewardCategoryTier;
              $tier->description = $current_tier;
              $tier->cost = $costs[$key];
              $tier->category_id = $category->id;
              $tier->save();
            }else{
              DB::rollBack();
              $category->delete();
              return response()->json([
                'success' => false,
                'message' => 'Tier '.$key.' has an invalid price value. Please enter only numbers (no commas or periods).'
              ], 422);
            }
          }
          DB::commit();
          
          return response()->json([
            'success' => true,
            'message' => 'reward category successfully updated'
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
        $category = RewardCategory::find($id);
        DB::beginTransaction();
        RewardCategoryTier::where('category_id',$id)->delete();
        if($category->delete()){
          DB::commit();
          return response()->json([
            'success' => true,
            'message' => 'reward category successfully deleted'
          ], 200);
        }else{
          DB::rollBack();
          return response()->json([
            'success' => false,
            'message' => 'Could not write to database. Please try again later.'
          ], 422);
        }
    }
    
    public function list_categories(Request $request,$page = 0)
    {
      $columns = array('name','created_at');
    
      $skip = $request->input('start');
      $take = $request->input('length', $this->pagination_items);
      
      $fetch_all = $request->input('fetch_all', FALSE);
      $data = new \stdClass();    
      $query = RewardCategory::with('rewards');
      
      $order = $request->input('order.0.column',0);
      
      $query->orderBy($columns[$order], $request->input('order.0.dir'));
      
      
      $query->skip($skip)->take($take);
      $data->data = $query->get();
      $data->iTotalRecords =  RewardCategory::count();
      $data->recordsFiltered = $data->iTotalRecords;
      
      return response()->json( $data );
    }
    
    public function list_category_tiers($id)
    {
      $data = new \stdClass();    
      $data = RewardCategoryTier::where('category_id',$id)->orderBy('id','asc')->get();
      return response()->json( $data );
    }
}
