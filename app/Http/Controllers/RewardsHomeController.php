<?php

/*
 * Taken from
 * https://github.com/laravel/framework/blob/5.2/src/Illuminate/Auth/Console/stubs/make/controllers/HomeController.stub
 */

namespace OAMPI_Eval\Http\Controllers;
use OAMPI_Eval\ActivityLog;
use OAMPI_Eval\Reward;
use OAMPI_Eval\RewardExclusive;
use OAMPI_Eval\RewardExclusiveClaim;
use OAMPI_Eval\Donation;
use OAMPI_Eval\DonationIntent;
use OAMPI_Eval\Voucher;
use OAMPI_Eval\VoucherClaims;
use OAMPI_Eval\RewardCategoryTier as Tier;
use OAMPI_Eval\Orders;
use OAMPI_Eval\Coffeeshop;
use OAMPI_Eval\User;
use OAMPI_Eval\Team;
use OAMPI_Eval\Point;
use OAMPI_Eval\Utilities\PrintItem;
use OAMPI_Eval\Http\Requests;
use Illuminate\Http\Request;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Input;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

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
        $this->middleware('auth',['except' => ['barista','create_order','check_points','fetch_coffees']]);

        $this->pagination_items = 50;
        $this->maxDailyOrder = 10000;
        $this->initLoad = 100;
        $this->cup_discount = 5;
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

    public function statistics($start = 0, $end = 0){
      if($start==0 || $end==0){
        $now = Carbon::now();
        $now->hour = 0;
        $now->minute = 0;
        $now->second = 0;

        $midnight = Carbon::now();
        $midnight->hour = 23;
        $midnight->minute = 59;
        $midnight->second = 59;
      }else{
        $now = Carbon::createFromTimestamp($start);
        $now->hour = 0;
        $now->minute = 0;
        $now->second = 0;

        $midnight = Carbon::createFromTimestamp($end);
        $midnight->hour = 23;
        $midnight->minute = 59;
        $midnight->second = 59;
      }

      $orders = DB::select(DB::raw("
                  SELECT orders.id, orders.created_at as 'order_date', rewards.name as 'reward_name', users.id as userid, users.employeeNumber as 'employee_number', UPPER(users.lastname) as 'last', UPPER(users.firstname) as 'first', users.nickname as 'nick', campaign.id as 'cid', campaign.name as 'campaign_name'
                  FROM
                    orders
                    LEFT JOIN users on orders.user_id = users.id
                    LEFT JOIN user_leaders on user_leaders.user_id = users.id
                    LEFT JOIN immediateHead_Campaigns on user_leaders.immediateHead_Campaigns_id = immediateHead_Campaigns.id
                    LEFT JOIN campaign on immediateHead_Campaigns.campaign_id = campaign.id
                    LEFT JOIN rewards on orders.reward_id = rewards.id
                  WHERE orders.created_at BETWEEN '".$now->toDateTimeString()."' AND '".$midnight->toDateTimeString()."'
                  ORDER BY orders.created_at asc
                "));

      $order_id = 0;
      $coffee_count = [];
      $campaigns = [];

      foreach ($orders as $key => $order) {
        if($order_id!=$order->id){
          $order_id = $order->id;
          if(array_key_exists($order->reward_name,$coffee_count)){
            $coffee_count[$order->reward_name] = $coffee_count[$order->reward_name] + 1;
          }else{
            $coffee_count[$order->reward_name] = 1;
          }

          if(array_key_exists($order->campaign_name,$campaigns)){
            $campaigns[$order->campaign_name] = $campaigns[$order->campaign_name] + 1;
          }else{
            $campaigns[$order->campaign_name] = 1;
          }
        }
      }

      $data = [
        'todays_orders' => $orders,
        'include_rewards_scripts' => FALSE,
        'contentheader_title' => "Orders History",
        'count' => $coffee_count,
        'campaigns' => $campaigns
      ];

      if($start==0 || $end==0){
        return view('rewards/statistics', $data);
      }else{
        return response()->json([
          'orders' => $orders,
          'count' => $coffee_count,
          'campaigns' => $campaigns
        ], 200);
      }
    }

    public function rewards_catalog()
    {

      // check first if from Davao
      $floor = Team::where('user_id',\Auth::user()->id)->first()->floor_id;

      $noaccess = [6,7,8,9];

      ($floor == 9 || in_array(\Auth::user()->status_id, $noaccess)) ? $noCoffee = true : $noCoffee=false;

      // if ($floor == 9 || in_array(\Auth::user()->status_id, $noaccess))
      //   return view('access-denied');
      // else
      //{
        //check shop if OPEN
        $shop = Coffeeshop::orderBy('id','DESC')->first();

        // let's check first if we've already reached max limit of order per day
        $startDay = Carbon::now('GMT+8')->startOfDay();
        $endDay = Carbon::now('GMT+8')->endOfDay();
        $allOrders = DB::table('orders')->where('created_at','>=',$startDay->format('Y-m-d H:i:s'))->
                          where('created_at','<',$endDay->format('Y-m-d H:i:s'))->
                          where('status','PRINTED')->get();


        $skip = 0 * $this->pagination_items;
        $take = $this->pagination_items;
        $rewards = Reward::with("category")->orderBy('name', 'asc')->skip($skip)->take($take)->get();

        $user_id = \Auth::user()->id;
        $user = User::with('points','team')->find($user_id);

        $exclusives = RewardExclusive::where('campaign_id', $user->team->campaign_id)->get();

        $orders = Orders::with('item')
                ->where([
                  ['user_id','=',$user_id],
                  ['status','=','PENDING'],
                ])
                ->get();

        date_default_timezone_set('Asia/Singapore');
        $t=time();
        $interval=15*60;
        $last = $t - $t % $interval;
        $next = $last + $interval;

        $time = strftime('%H:%M %p', $next);
        $maxedOut = 0;

        if(count($allOrders) >= $this->maxDailyOrder)
        {
          $msg = "Sorry, we've already reached maximum daily limit of redeemable drinks.<br/>Please try again tomorrow.";
          $maxedOut = 1;
        }
        else
        {
          if ($shop->status !== "OPEN")
          {
            if ($shop->status == "ON_BREAK")
              $msg ='Sorry, our barista is currently <br><span style="font-size:4.5em; class="text-yellow">ON BREAK</span>';

            else
              $msg = 'Sorry, we\'re <br><span style="font-size:5em; class="text-yellow">CLOSED</span>';

          } else $msg="";
        }

        $vouchers = Voucher::all();
        $donations = Donation::all();

        $data = [

            'time' => $time,
            'include_rewards_scripts' => TRUE,
            'contentheader_title' => "Rewards Catalog",
            'items_per_page' => $this->pagination_items,
            'rewards' => $rewards,
            'remaining_points' => is_null($user->points) ? $this->initLoad : $user->points->points,
            'orders' => $orders,
            'msg' => $msg,
            'shop'=>$shop,
            'maxedOut'=>$maxedOut,
            'vouchers' => $vouchers,
            'donations' => $donations,
            'noCoffee' => $noCoffee,
            'exclusives' => $exclusives
          ];

        if( \Auth::user()->id !== 564 )
        {
          $file = fopen('public/build/rewards.txt', 'a') or die("Unable to open logs");
          fwrite($file, "-------------------\n Checkout Catalog on ".Carbon::now('GMT+8')->format('Y-m-d H:i')." by [". \Auth::user()->id."] ".\Auth::user()->lastname."\n");
          fclose($file);
        }


        return view('rewards/home_rewards_catalog', $data);


      //}

    }

    public function barista($code){

      $code = str_replace(".", "", trim($code) );

      $id = DB::table('users')
            ->select('id')
            ->whereRaw('concat(`id`,`employeeNumber`)=?',[$code])->first();
      if($id->id){
        $user_id = $id->id;
        $user = User::with('points','team')->find($user_id);
      }else{
        $user = null;
        $user_id = 0;
      }

      $skip = 0 * $this->pagination_items;
      $take = $this->pagination_items;
      $rewards = Reward::with("category")->orderBy('name', 'asc')->skip($skip)->take($take)->get();
      $order_id = $rewards->first()->id;
      $order_name = $rewards->first()->name;
      $data = [
        'rewards' => $rewards,
        'include_barista_scripts' => true,
        'user' => $user,
        'user_id' => $user_id,
        'order_id' => $order_id,
        'order_name' => $order_name,
        'code' => $code
      ];
      app('debugbar')->disable();
        return view('barista-home', $data);
    }

    public function fetch_coffees(){
      $rewards = Reward::with("category")->orderBy('name', 'asc')->get();
      $out_reward = [];
      foreach($rewards as $reward){
        $cat = $reward->category->name;
        $category = ($cat == "Coffee" || $cat == "Latte" || $cat == "Flavored Coffee" || $cat == "Choco Drinks" )  ? "Coffee" : $reward->category->name;
        $out_reward[] = [
          "id" => $reward->id,
          "name" => $reward->name,
          "description" => $reward->description,
          "attachment_image" => $reward->attachment_image,
          "cost" => $reward->cost,
          "category" => $category
        ];
      }

      return response('{"rewards":' . json_encode($out_reward)   . "}", 200)
                  ->header('Content-Type', 'application/json');
    }

    public function check_points($code){
      $code = str_replace(".", "", trim($code) );
      $id = DB::table('users')
            ->select('id')
            ->whereRaw('concat(`id`,`employeeNumber`)=?',[$code])->first();
      if($id->id){
        $user = User::with('points','team')->find($id->id);

        if($user->points==null){
          if($user->points!==0){
            $record = new Point;
            $record->idnumber = $user->id;
            $record->points = $this->initLoad;// 10;
            $record->save();
            $points = $this->initLoad;// 10;
          }
        }else{
          $points = $user->points->points;
        }

        $name = $user->firstname." ".$user->middlename." ".$user->lastname;
      }else{
        $points = 0;
        $name = "";
      }


      return response()->json([
        'points' => $points,
        'name' => $name
      ], 200);
    }

    public function cancel_order($id){
      $user_id = \Auth::user()->id;
      $user = User::with('points','team')->find($user_id);
      $order = DB::table('orders')
              ->where([
                ['user_id','=',$user_id],
                ['id','=',$id],
                ['status','=','PENDING'],
              ]);

      $details = $order->first();
      $reward_id = $details->reward_id;
      $reward = Reward::find($reward_id);
      $user->points()->increment('points', $reward->cost);

      $refund = $reward->cost + $user->points->points;
      $order->update(['status' => 'CANCELLED']);
      return response()->json([
        'success' => true,
        'refund' => $refund
      ], 200);

    }

    public function print_qr($employee_id){
      //$user = User::with('points','team')->find($employee_id);
      $connector = new NetworkPrintConnector("172.22.18.200", 9100);
      $printer = new Printer($connector);
      $printer -> setJustification(Printer::JUSTIFY_CENTER);
      $printer -> qrCode("835051714", Printer::QR_ECLEVEL_L, 6,Printer::QR_MODEL_2);

      $printer -> feed(1);
      $printer -> feed(1);

      $printer -> cut();
      $printer -> close();
    }

    public function print_order($code){
      //sql = select id from users where concat(id,employeeNumber) = $code
      $id = DB::table('users')
            ->select('id')
            ->where(DB::raw('concat(`id`,`employeeNumber`)'),'=',$code)
            ->get();

      $user_id = $id[0]->id;

      $order = DB::table('orders')
              ->where([
                ['user_id','=',$user_id],
                ['status','=','PENDING'],
              ])
              ->oldest()
              ->first();

      if ($order) {
        $reward = Reward::find($order->reward_id);
        $user = User::with('points','team')->find($user_id);

        $micro = microtime(true);
        /* Information for the receipt */
        $items = array(
            new PrintItem("Name: ".$user->firstname." ".$user->lastname),
            new PrintItem("Item: ".$reward->name, "Cost: ".$reward->cost),
            new PrintItem("Remaining Points:", $user->points->points)
        );

        try{

          $logo = EscposImage::load(base_path() . "/public/img/oam_logo.png", false);
          $connector = new NetworkPrintConnector("172.22.18.200", 9100);
          $printer = new Printer($connector);

          $printer -> setJustification(Printer::JUSTIFY_CENTER);
          $printer -> graphics($logo);
          $printer -> setJustification(Printer::JUSTIFY_CENTER);
          $printer -> setTextSize(2,2);
          $printer -> text("Open Access BPO Rewards\n");
          $printer -> setTextSize(1,1);
          $printer -> text(date("m/d/Y h:i a")."\n");
          $printer -> text("Receipt Number:".sprintf('%08d', $order->id)."\n");
          $printer -> feed(1);

          $printer -> setJustification(Printer::JUSTIFY_LEFT);
          foreach ($items as $item) {
            $printer -> text($item);
          }
          $printer -> feed(1);
          $printer -> cut();
          $printer -> close();

          DB::table('orders')
            ->where('id', $order->id)
            ->update(['status' => 'CLAIMED']);

          return response()->json([
            'success' => true,
            'message' => "no pending order"
          ], 200);

        }catch(\Exception $e){
          return response()->json([
            'success' => false,
            'message' => "printer error"
          ], 200);
        }


      }else{
        return response()->json([
          'success' => false,
          'message' => "no pending order"
        ], 200);
      }


    }

    public function create_order(){
      date_default_timezone_set('Asia/Singapore');

      $code = str_replace(".", "", trim(Input::get('code')) );
      $reward_id = Input::get('order_id');
      $owncup = Input::get('owncup',"");
      $debug = Input::get('debug',false);

      $id = DB::table('users')
            ->select('id')
            ->whereRaw('concat(`id`,`employeeNumber`)=?',[$code])->first();

      $user_id = $id->id;


      $user = User::with('points','team')->find($user_id);
      $reward = Reward::find($reward_id);

        if($user->points==null){
          if($user->points!==0){
            $record = new Point;
            $record->idnumber = $user_id;
            $record->points = $this->initLoad; // 10;
            $record->save();
          }
        }

        if($user->points == null || $reward->cost > $user->points->points ){
          return response()->json([
            'success' => false,
            'message' => 'You do not have enough points to claim this reward. Your current points: '.$user->points->points.' (required: '.$reward->cost.")"
          ], 200);
        }

      $cost = $reward->cost;
      if($owncup==="owncup"){
        $cost = $cost - $this->cup_discount;
      }

      if($user->points()->decrement('points', $cost)){
        $record = new ActivityLog;
        $record->initiator_id       = $user_id;
        $record->target_id      = $user_id;
        $record->description = "claimed ".$reward->name." for ".$cost." points using barista app";

        $order = new Orders;
        $order->user_id = $user_id;
        $order->reward_id = $reward->id;
        $order->status = "PRINTED";
        $order->save();

        if($record->save()) {
          try{


            $micro = microtime(true);
            $current_points = $user->points->points - $cost;
            //if($debug==false){


              $items = array(
                  new PrintItem("Name: ".$user->firstname." ".$user->lastname),
                  new PrintItem("Item: ".$reward->name, "Cost: ".$cost),
                  new PrintItem("Remaining Points:", $current_points)
              );

              $logo = EscposImage::load(base_path() . "/public/img/oam_logo.png", false);
              $connector = new NetworkPrintConnector("172.22.18.200", 9100);
              $printer = new Printer($connector);

              $printer -> setJustification(Printer::JUSTIFY_CENTER);
              $printer -> graphics($logo,Printer::IMG_DOUBLE_WIDTH|Printer::IMG_DOUBLE_HEIGHT);
              $printer -> setJustification(Printer::JUSTIFY_CENTER);
              $printer -> setTextSize(2,2);
              $printer -> text("Open Access BPO Rewards\n");
              $printer -> feed(1);
              $printer -> setTextSize(1,1);
              $printer -> text(date("m/d/Y h:i a")."\n");
              $printer -> text("Receipt Number:".sprintf('%08d', $order->id)."\n");
              $printer -> feed(2);

              $printer -> setJustification(Printer::JUSTIFY_LEFT);
              $printer -> setTextSize(1,1);
              foreach ($items as $item) {
                $printer -> text($item);
              }
              if($owncup==="owncup"){
                $printer -> text("NOTE: I'LL BRING MY OWN CUP ");
              }
              $printer -> feed(2);
              $printer -> cut();
              $printer -> close();
            //}


            return response()->json([
                  'owncup'=>$owncup,
                  'success' => true,
                  'idnumber' => $user_id,
                  'micro' => $micro,
                  'order_id' => $record->id,
                  'label' => $reward->name,
                  'file' => '/public/media/qr/'.$user_id.'.svg',
                  'points' => $current_points
                ], 200);
          }catch(\Exception $e){
            $error_message = "Could not access the printer.";
            $record->delete();
            $order->delete();
            $user->points()->increment('points',$cost);
            $error = true;
          }
        }else{
          $user->points()->increment('points',$cost);
          $error = true;
          $error_message = "Could not complete your order. Please try again later.";
        }
      }else{
        $error = true;
        $error_message = "Could not complete your order. Please try again later.";
      }

      if($error){
        return response()->json([
          'success' => false,
          'error' => array('Could not write to database. Please try again later.'),
          'message'   => $error_message
        ], 200);
      }

    }


    public function rewards_catalog_list(Request $request,$page = 0){
      $skip = $request->input('start', ($page * $this->pagination_items));
      $take = $request->input('length', $this->pagination_items);
      $fetch_all = $request->input('fetch_all', FALSE);
      $data = new \stdClass();
      $data->data = Reward::orderBy('name', 'asc')->skip($skip)->take($take)->get();

      return view('rewards/home_rewards_catalog', $data);
    }

    public function get_qr($user_id){
      $user = User::find($user_id);
      QrCode::size(500)
        ->format('svg')
        ->errorCorrection('H')
        ->generate(
          $user_id . $user->employeeNumber,
          public_path('media/qr/'.$user_id.'.svg'));

      return response()->json([
        'success' => true,
        'idnumber' => $user_id,
        'file' => '/public/media/qr/'.$user_id.'.svg'
      ], 200);



    }

    public function send_points(){

      $points_to_send = Input::get('amount');
      $recipient_id = Input::get('recipient_id');

      if(!is_numeric($points_to_send)){
        return response()->json([
          'success' => false,
          'message' => "Please enter a proper amount of points to send. (must be numeric value)"
        ], 422);
      }

      if(!is_numeric($recipient_id)){
        return response()->json([
          'success' => false,
          'message' => "Please select a recipient of your points"
        ], 422);
      }

      $user_id = \Auth::user()->id;
      $user = User::with('points','team')->find($user_id);
      $recipient = User::with('points','team')->find($recipient_id);
      if($user->points == null || $points_to_send > $user->points->points ){
        return response()->json([
          'success' => false,
          'message' => 'Failed to send '.$points_to_send.' because you only have '.$user->points->points.' points remaining.'
        ], 422);
      }

      $user->points()->decrement('points', $points_to_send);
      $recipient->points()->increment('points', $points_to_send);

      $record = new ActivityLog;
      $record->initiator_id       = $user_id;
      $record->target_id      = $recipient_id;
      $record->description = "Shared ".$points_to_send." points";

      return response()->json([
        'success' => true,
        'message' => 'Points sent successfully'
      ], 422);


    }

    public function claim_reward(Request $request,$reward_id = 0){

      date_default_timezone_set('Asia/Singapore');
      $now = strtotime('now');
      $tier = $request->input('tier',0);
      $debug = $request->input('debug',0);
      $time = $request->input('time',"now");
      $owncup = $request->input('owncup',"");

      $time = strtotime($time);

      $error = false;
      $error_message = "";
      $user_id = \Auth::user()->id;

      if($tier==0){
        return response()->json([
          'exception' => $error_message,
          'success' => false,
          'message' => array('Please select a proper reward variant.')
        ], 422);
      }

      if($time<$now){
        return response()->json([
          'exception' => $error_message,
          'success' => false,
          'message' => array('Your desired pick-up time must be in the future.'),
          'time' => $time,
          'now' => $now
        ], 422);
      }

      $data = new \stdClass();
      if(intval($reward_id) > 0){
        $user = User::with('points','team')->find($user_id);
        $reward = Reward::find($reward_id);
        Tier::find($tier);

        if($user->points==null){
          if($user->points!==0){
            $record = new Point;
            $record->idnumber = $user_id;
            $record->points = $this->initLoad;// 10;
            $record->save();
          }
        }

        $cost = $reward->cost;
        if($owncup==="owncup"){
          $cost = $cost - $this->cup_discount;
        }

        if($user->points == null || $cost > $user->points->points ){
          return response()->json([
            'success' => false,
            'message' => 'You do not have enough points to claim this reward. Your current points: '.$user->points->points.' (required: '.$cost.")"
          ], 422);
        }

          if($user->points()->decrement('points', $cost)){
            $record = new ActivityLog;
            $record->initiator_id       = $user_id;
            $record->target_id      = $user_id;
            $record->description = "claimed ".$reward->name." for ".$cost." points using EMS portal";

            $order = new Orders;
            $order->user_id = $user_id;
            $order->reward_id = $reward->id;
            $order->status = "PENDING";
            $order->save();

            if($record->save()) {
              try{
                $micro = microtime(true);
                $current_points = $user->points->points - $cost;

                $items = array(
                    new PrintItem("Name: ".$user->firstname." ".$user->lastname),
                    new PrintItem("Item: ".$reward->name, "Cost: ".$cost),
                    new PrintItem("Pickup by: ".$request->input('time',"NOW")),
                    new PrintItem("Remaining Points:", $current_points)
                );




                    $logo = EscposImage::load(base_path() . "/public/img/oam_logo.png", false);
                    $connector = new NetworkPrintConnector("172.22.18.200", 9100);
                    $printer = new Printer($connector);

                    $printer -> setJustification(Printer::JUSTIFY_CENTER);
                    $printer -> graphics($logo,Printer::IMG_DOUBLE_WIDTH|Printer::IMG_DOUBLE_HEIGHT);
                    $printer -> setJustification(Printer::JUSTIFY_CENTER);
                    $printer -> setTextSize(2,2);
                    $printer -> text("Open Access BPO Rewards\n");
                    $printer -> feed(1);
                    $printer -> setTextSize(1,1);
                    $printer -> text(date("m/d/Y h:i a")."\n");
                    $printer -> text("Receipt Number:".sprintf('%08d', $order->id)."\n");
                    $printer -> feed(2);

                    $printer -> setJustification(Printer::JUSTIFY_LEFT);
                    $printer -> setTextSize(1,1);
                    foreach ($items as $item) {
                      $printer -> text($item);
                    }
                    if($owncup==="owncup"){
                      $printer -> text("NOTE: I'LL BRING MY OWN CUP ");
                    }
                    $printer -> feed(2);
                    $printer -> cut();
                    $printer -> close();

                    $order->status = "PRINTED";
                    $order->save();




                /*
                QrCode::size(500)
                  ->format('svg')
                  ->errorCorrection('H')
                  ->generate(
                    $user_id . $user->employeeNumber,
                    public_path('media/qr/'.$user_id.'.svg'));
                    */

                return response()->json([
                  'owncup'=>$owncup,
                  'time'=>$time,
                  'success' => true,
                  'idnumber' => $user_id,
                  'micro' => $micro,
                  'order_id' => $record->id,
                  'label' => $reward->name,
                  'file' => '/public/media/qr/'.$user_id.'.svg',
                  'points' => $current_points
                ], 200);
              }catch(\Exception $e){
                $error_message = $e->getMessage();
                $record->delete();
                $order->delete();
                $user->points()->increment('points',$cost);
                $error = true;
              }
            }else{
              $user->points()->increment('points',$cost);
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

    public function donate_reward_points(Request $request){
      date_default_timezone_set('Asia/Singapore');
      $now = strtotime('now');
      $error = false;
      $error_message = "";
      $user_id = \Auth::user()->id;
      $user = User::with('points','team')->find($user_id);
      $donation_id = $request->input('donation_id',0);
      $value = $request->input('value',0);
      $donation = Donation::find($donation_id);


      $email = $request->input('email','');
      $phone = $request->input('phone','');

      if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return response()->json([
          'exception' => null,
          'success' => false,
          'message' => 'Please enter a valid email address.'
        ], 422);
      }

      if (!preg_match('/^09[0-9]{9}+$/', $phone)) {
        return response()->json([
          'exception' => null,
          'success' => false,
          'message' => 'Please enter a valid phone number.'
        ], 422);
      }

      if($donation->minimum > $value){
        return response()->json([
          'exception' => null,
          'success' => false,
          'message' => 'This donation requires a minimum point value of '.$donation->minimum
        ], 422);
      }

      if($user->points == null || $value > $user->points->points ){
        return response()->json([
          'success' => false,
          'message' => 'You do not have enough points to donate to '.$donation->name
        ], 422);
      }

      $donation_intent = new DonationIntent;
      $donation_intent->user_id       = $user_id;
      $donation_intent->donation_id      = $donation_id;
      $donation_intent->donated_points      = $value;
      $donation_intent->email      = $email;
      $donation_intent->phone      = $phone;
      if($donation_intent->save()) {

        if($user->points()->decrement('points', $value)){
          $record = new ActivityLog;
          $record->initiator_id       = $user_id;
          $record->target_id      = $user_id;
          $record->description = "donated ".$value." points to : ".$donation->name;
          if($record->save()) {
            $message = "Donation successful.";
          }else{
            $user->points()->increment('points',$cost);
            $donation_intent->delete();
            $error = true;
            $error_message = "failed to complete your donation. could not log the activity.";
          }
        }else{
          $donation_intent->delete();
          $error = true;
          $error_message = "could not re-allocate points properly";
        }

        return response()->json([
          'success' => !$error,
          'message' => $error_message
        ], $error ? 422 : 200);

      }else{
        return response()->json([
          'success' => false,
          'message' => array('Could not write to database. Please try again later.')
        ], 422);
      }

    }

    public function claim_voucher(Request $request,$reward_id = 0){

      date_default_timezone_set('Asia/Singapore');
      $now = strtotime('now');
      $error = false;
      $error_message = "";
      $user_id = \Auth::user()->id;
      $accepted = $request->input('agree',0);
      $vemail = $request->input('vemail','');
      $vphone = $request->input('vphone','');

      if (!filter_var($vemail, FILTER_VALIDATE_EMAIL)) {
        return response()->json([
          'exception' => null,
          'success' => false,
          'message' => 'Please enter a valid email address.'
        ], 422);
      }

      if (!preg_match('/^09[0-9]{9}+$/', $vphone)) {
        return response()->json([
          'exception' => null,
          'success' => false,
          'message' => 'Please enter a valid phone number.'
        ], 422);
      }

      if($accepted!=1 && $accepted!="1"){
        return response()->json([
          'exception' => null,
          'success' => false,
          'message' => 'Please agree to the tax deduction agreement.'
        ], 422);
      }

      if(!Hash::check($request->input('password',0), \Auth::user()->password)){
        return response()->json([
          'password' => \Auth::user()->password,
          'exception' => null,
          'success' => false,
          'message' => 'Invalid password.'
        ], 422);
      }



      $claims_today = VoucherClaims::where([
                  ['user_id','=',$user_id],
                  ['created_at','>=',Carbon::today()]
                ])->get();
      if($claims_today->count()>0){
        return response()->json([
          'exception' => null,
          'success' => false,
          'message' => 'You have already claimed a voucher today. Please come back tomorrow to claim another.'
        ], 422);
      }

      $data = new \stdClass();
      if(intval($reward_id) > 0){
        $user = User::with('points','team')->find($user_id);
        $reward = Voucher::find($reward_id);
        if($reward->quantity <= 0){
          return response()->json([
            'success' => false,
            'message' => 'Sorry, all vouchers for ' . $reward->name . ' has already been claimed'
          ], 422);
        }
        if($user->points==null){
          if($user->points!==0){
            $record = new Point;
            $record->idnumber = $user_id;
            $record->points = $this->initLoad;// 10;
            $record->save();
          }
        }

        $cost = $reward->cost;
        if($user->points == null || $cost > $user->points->points ){
          return response()->json([
            'success' => false,
            'message' => 'You do not have enough points to claim this voucher. Your current points: '.$user->points->points.' (required: '.$cost.")"
          ], 422);
        }


          $reward->decrement('quantity',1);
          if($user->points()->decrement('points', $cost)){
            $record = new ActivityLog;
            $record->initiator_id       = $user_id;
            $record->target_id      = $user_id;
            $record->description = "claimed voucher: ".$reward->name." for ".$cost." points using EMS portal";
            if($record->save()) {
              $current_points = $user->points->points - $cost;

              $claim = new VoucherClaims;
              $claim->email = $request->input('vemail',0);
              $claim->phone = $request->input('vphone',0);
              $claim->user_id = $user_id;
              $claim->voucher_id = $reward_id;
              if($claim->save()){
                return response()->json([
                  'success' => true,
                  'label' => $reward->name,
                  'points' => $current_points
                ], 200);
              }else{
                $record->delete();
                $user->points()->increment('points',$cost);
                $error = true;
                $error_message = "could not write to voucher claims table";
              }
            }else{
              $user->points()->increment('points',$cost);
              $error = true;
              $error_message = "could not log the activity";
            }
          }else{
            $reward->increment('quantity',1);
            $error = true;
            $error_message = "could not re-allocate points properly";
          }

        if($error){
          return response()->json([
            'success' => false,
            'error' => array('Could not write to database. Please try again later.'),
            'message'   => $error_message
          ], 422);
        }

      } else {
        return response()->json([
          'exception' => $error_message,
          'success' => false,
          'message' => array('invalid voucher item.')
        ], 422);
      }
    }

    public function claim_exclusive(Request $request,$reward_id = 0){

      date_default_timezone_set('Asia/Singapore');
      $now = strtotime('now');
      $error = false;
      $error_message = "";
      $user_id = \Auth::user()->id;

      $data = new \stdClass();
      if(intval($reward_id) > 0){
        $user = User::with('points','team')->find($user_id);
        $reward = RewardExclusive::find($reward_id);

        if($user->points==null){
          if($user->points!==0){
            $record = new Point;
            $record->idnumber = $user_id;
            $record->points = $this->initLoad;// 10;
            $record->save();
          }
        }

        $cost = $reward->cost;
        if($user->points == null || $cost > $user->points->points ){
          return response()->json([
            'success' => false,
            'message' => 'You do not have enough points to claim this reward. Your current points: '.$user->points->points.' (required: '.$cost.")"
          ], 422);
        }



          if($user->points()->decrement('points', $cost)){
            $record = new ActivityLog;
            $record->initiator_id       = $user_id;
            $record->target_id      = $user_id;
            $record->description = "claimed exclusive reward: ".$reward->name." for ".$cost." points using EMS portal";
            if($record->save()) {
              $current_points = $user->points->points - $cost;

              $claim = new RewardExclusiveClaim;
              $claim->status = "pending";
              $claim->user_id = $user_id;
              $claim->exclusive_id = $reward_id;
              if($claim->save()){
                return response()->json([
                  'success' => true,
                  'label' => $reward->name,
                  'points' => $current_points
                ], 200);
              }else{
                $record->delete();
                $user->points()->increment('points',$cost);
                $error = true;
                $error_message = "could not write to voucher claims table";
              }
            }else{
              $user->points()->increment('points',$cost);
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
            'error' => array('Could not write to database. Please try again later.'),
            'message'   => $error_message
          ], 422);
        }

      } else {
        return response()->json([
          'exception' => $error_message,
          'success' => false,
          'message' => array('invalid exclusive reward item.')
        ], 422);
      }
    }



    //v6pZWpyj

    //initintdev : coLoRs;
    //db.init-int.com, initial_int
}