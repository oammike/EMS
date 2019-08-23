<?php

/*
 * Taken from
 * https://github.com/laravel/framework/blob/5.2/src/Illuminate/Auth/Console/stubs/make/controllers/HomeController.stub
 */

namespace OAMPI_Eval\Http\Controllers;
use OAMPI_Eval\ActivityLog;
use OAMPI_Eval\Reward;
use OAMPI_Eval\RewardCategoryTier as Tier;
use OAMPI_Eval\Orders;
use OAMPI_Eval\User;
use OAMPI_Eval\Point;
use OAMPI_Eval\Utilities\PrintItem;
use OAMPI_Eval\Http\Requests;
use Illuminate\Http\Request;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

/**
 * Class HomeController
 * @package App\Http\Controllers
 */
class RewardsHomeController extends Controller
{
  protected $pagination_items = 50;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->pagination_items = 50;
    }

    /**
     * Show the application dashboard.
     *
     * @return Response
     */
    public function index()
    {
      $history = \DB::table('activity_log')
        ->where('initiator_id', \Auth::user()->id)
        ->orWhere('target_id', \Auth::user()->id)
        ->leftJoin('users as initiator', 'initiator.id', '=', 'activity_log.initiator_id')
        ->leftJoin('users as target', 'target.id', '=', 'activity_log.target_id')
        ->select('activity_log.description', 'activity_log.initiator_id', 'activity_log.target_id', 'activity_log.created_at','initiator.name as initiator_name','target.name as target_name')
        ->orderBy('activity_log.id','desc')
        ->get();
      
      //$this->layout->contentheader_title = 'Welcome Back!';
      $data = [
        'include_rewards_scripts' => TRUE,
        'contentheader_title' => "Points History",
        'history' => $history
      ];
      return view('rewards/home_dashboard', $data);
    }
    
    public function rewards_catalog(){
      $skip = 0 * $this->pagination_items;
      $take = $this->pagination_items;
      $rewards = Reward::with("category")->orderBy('name', 'asc')->skip($skip)->take($take)->get();
      $data = [
        'include_rewards_scripts' => TRUE,
        'contentheader_title' => "Rewards Catalog",
        'items_per_page' => $this->pagination_items,
        'rewards' => $rewards
      ];
      
      
      return view('rewards/home_rewards_catalog', $data);
    }
    
    
    public function rewards_catalog_list(Request $request,$page = 0){
      $skip = $request->input('start', ($page * $this->pagination_items));
      $take = $request->input('length', $this->pagination_items);
      $fetch_all = $request->input('fetch_all', FALSE);
      $data = new \stdClass();    
      $data->data = Reward::orderBy('name', 'asc')->skip($skip)->take($take)->get();
      
      return view('rewards/home_rewards_catalog', $data);
    }
    
    public function claim_reward(Request $request,$reward_id = 0){
      $tier = $request->input('tier',0);
      $error = false;
      $error_message = "";
      $user_id = \Auth::user()->id;
      
      if($tier==0){
        return response()->json([
          'exception' => $error_message,
          'success' => false,
          'message' => array('invalid reward variant.')
        ], 422);
      }
      
      $data = new \stdClass();
      if(intval($reward_id) > 0){
        $user = User::with('points','team')->find($user_id);
        $reward = Reward::find($reward_id);
        Tier::find($tier);
        
        if($user->points==null){
          $record = new Point;
          $record->idnumber = $user_id;
          $record->points = 10;
          $record->save();
        }
        
        if($user->points == null || $reward->cost > $user->points->points ){
          return response()->json([
            'success' => false,
            'message' => 'You do not have enough points to claim this reward. Your current points: '.$user->points->points.' (required: '.$reward->cost.")"
          ], 422);
        }
        
          if($user->points()->decrement('points', $reward->cost)){
            $record = new ActivityLog;
            $record->initiator_id       = $user_id;
            $record->target_id      = $user_id;
            $record->description = "claimed ".$reward->name." for ".$reward->cost." points";
            
            $order = new Orders;
            $order->user_id = $user_id;
            $order->reward_id = $reward->id;
            $order->status = "PENDING";
            $order->save();
            
            if($record->save()) {
              try{
                $micro = microtime(true);
                /* Information for the receipt */
                $items = array(
                    new PrintItem("Item: ".$reward->name, $reward->cost),
                    new PrintItem("Remaining Points:", $user->points->points)
                );
              
                $logo = EscposImage::load(base_path() . "/public/img/oam_logo.png", false);
                $connector = new NetworkPrintConnector("172.18.31.200", 9100);
                $printer = new Printer($connector);
  
                $printer -> setJustification(Printer::JUSTIFY_CENTER);
                $printer -> graphics($logo);
                $printer -> setJustification(Printer::JUSTIFY_CENTER);
                $printer -> setTextSize(2,2);
                $printer -> text("OAM Rewards\n");
                $printer -> setTextSize(1,1);              
                $printer -> text($user->name." (".$user->idnumber.")\n");
                $printer -> text(date("m/d/Y h:i a")."\n");
                $printer -> text("Receipt Number:".sprintf('%08d', $record->id)."\n");
                $printer -> feed(1);
                
                $printer -> setJustification(Printer::JUSTIFY_LEFT);
                foreach ($items as $item) {
                  $printer -> text($item);
                }
                
                $printer -> feed(1);
                $printer -> setJustification(Printer::JUSTIFY_CENTER);
                $printer -> qrCode($user->employeeNumber.'@@@'.$micro.'@@@'.$record->id, Printer::QR_ECLEVEL_L, 9, Printer::QR_MODEL_2);
                $printer -> feed(1);
                $printer -> cut();
                $printer -> close();
                
                QrCode::size(500)
                  ->format('svg')
                  ->generate(
                    $user->employeeNumber.'@@@'.$micro.'@@@'.$record->id,
                    public_path('media/qr/'.$user->employeeNumber.'_'.$record->id.'.svg'));
                  
                return response()->json([
                  'success' => true,
                  'idnumber' => $user->employeeNumber,
                  'micro' => $micro,
                  'order_id' => $record->id,
                  'file' => '/public/media/qr/'.$user->employeeNumber.'_'.$record->id.'.svg'
                ], 200);
              }catch(\Exception $e){
                $error_message = $e->getMessage();
                $record->delete();
                $user->points()->increment('points',$reward->cost);
                $error = true;
              }
            }else{
              $user->points()->increment('points',$reward->cost);
              $error = true;
              $error_message = "could not log the activity";
            }
          }else{
            $error = true;
            $error_message = "could not re-allocate points properly";
          }
        
        if($error){
          return response()->json([
            'success' => false,
            'message' => array('Could not write to database. Please try again later.'),
            'error'   => $error_message
          ], 422);
        }
        
      } else {
        return response()->json([
          'exception' => $error_message,
          'success' => false,
          'message' => array('invalid reward item.')
        ], 422);
      }      
    }
    
    
    //v6pZWpyj
    
    //initintdev : coLoRs;
    //db.init-int.com, initial_int
}