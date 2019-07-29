<?php

namespace OAMPI_Eval\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use \DB;
use \Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use OAMPI_Eval\Http\Requests;
use OAMPI_Eval\User;
use OAMPI_Eval\Campaign;
use OAMPI_Eval\UserType;
use OAMPI_Eval\Team;
use OAMPI_Eval\ImmediateHead;
use OAMPI_Eval\Position;
use OAMPI_Eval\Status;
use OAMPI_Eval\EvalType;
use OAMPI_Eval\EvalSetting;
use OAMPI_Eval\EvalForm;
use OAMPI_Eval\Movement;
use OAMPI_Eval\Movement_ImmediateHead;
use OAMPI_Eval\ImmediateHead_Campaign;
use OAMPI_Eval\Notification;
use OAMPI_Eval\User_Notification;
use OAMPI_Eval\User_CWS;
use OAMPI_Eval\User_DTRP;
use OAMPI_Eval\User_OT;
use OAMPI_Eval\User_Memo;
use OAMPI_Eval\Memo;
use OAMPI_Eval\Logs;
use OAMPI_Eval\Biometrics;
use OAMPI_Eval\NotifType;
use OAMPI_Eval\FormBuilder;
use OAMPI_Eval\FormBuilder_Items;
use OAMPI_Eval\FormBuilderElem_Values;
use OAMPI_Eval\FormBuilderElements;
use OAMPI_Eval\FormBuilderSubtypes;
use OAMPI_Eval\FormSubmissions;
use OAMPI_Eval\FormSubmissionsUser;


class HomeController extends Controller
{
    protected $user;
    protected $userNotifs;
    use Traits\UserTraits;
  

    public function __construct()
    {
        $this->middleware('auth');
        $this->user =  User::find(Auth::user()->id);
        $this->userNotifs = $this->user->notifications();
        
    }

    public function gallery()
    {
      $correct = Carbon::now('GMT+8'); //->timezoneName();
      $album = Input::get('a');

      


      if (empty($album))
        return view('gallery',['album'=>null]);
      else
      {
        $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
         if($this->user->id !== 564 ) {

          switch ($album) {

            case '16':{
               fwrite($file, "-------------------\n Davao Health [". $this->user->id."] ".$this->user->lastname." on". $correct->format('M d h:i A'). "\n");

            }break;

            case '15':{
               fwrite($file, "-------------------\n G2 Photobooth [". $this->user->id."] ".$this->user->lastname." on". $correct->format('M d h:i A'). "\n");

            }break;

            case '14':{
               fwrite($file, "-------------------\n G2 AfterParty [". $this->user->id."] ".$this->user->lastname." on". $correct->format('M d h:i A'). "\n");

            }break;

            case '13':{
               fwrite($file, "-------------------\n G2 Launch [". $this->user->id."] ".$this->user->lastname." on". $correct->format('M d h:i A'). "\n");

            }break;
            
            case '12':{
               fwrite($file, "-------------------\n Pride2019 [". $this->user->id."] ".$this->user->lastname." on". $correct->format('M d h:i A'). "\n");

            }break;
            case '11':{
               fwrite($file, "-------------------\n Viewed HealthWellness [". $this->user->id."] ".$this->user->lastname." on". $correct->format('M d h:i A'). "\n");

            }break;
            case '10':{
               fwrite($file, "-------------------\n Viewed BTS [". $this->user->id."] ".$this->user->lastname." on". $correct->format('M d h:i A'). "\n");

            }break;

            case '9':{
               fwrite($file, "-------------------\n Viewed 5Mayo [". $this->user->id."] ".$this->user->lastname." on". $correct->format('M d h:i A'). "\n");

            }break;
            case '8':{
               fwrite($file, "-------------------\n Viewed Pajama [". $this->user->id."] ".$this->user->lastname." on". $correct->format('M d h:i A'). "\n");

            }break;

            case '7':{
               fwrite($file, "-------------------\n Viewed HappyHr [". $this->user->id."] ".$this->user->lastname." on". $correct->format('M d h:i A'). "\n");

            }break;

            case '6':{
               fwrite($file, "-------------------\n Viewed Catriona by [". $this->user->id."] ".$this->user->lastname." on". $correct->format('M d h:i A'). "\n");

            }break;

            case '5':{
               fwrite($file, "-------------------\n Viewed Physical by [". $this->user->id."] ".$this->user->lastname." on". $correct->format('M d h:i A'). "\n");

            }break;

            case '4':{
               fwrite($file, "-------------------\n Viewed Cam2 by [". $this->user->id."] ".$this->user->lastname." on". $correct->format('M d h:i A'). "\n");

            }break;

            case '3':{
               fwrite($file, "-------------------\n Viewed Cam1 by [". $this->user->id."] ".$this->user->lastname." on". $correct->format('M d h:i A'). "\n");

            }break;

            case '2':{
               fwrite($file, "-------------------\n Viewed Booth by [". $this->user->id."] ".$this->user->lastname." on". $correct->format('M d h:i A'). "\n");

            }break;

            case '1':{
               fwrite($file, "-------------------\n Viewed Year End 2018 by [". $this->user->id."] ".$this->user->lastname." on". $correct->format('M d h:i A'). "\n");

            }break;
            
            default:{
               fwrite($file, "-------------------\n Viewed Year End 2018 by [". $this->user->id."] ".$this->user->lastname." on". $correct->format('M d h:i A'). "\n");
            }
              # code...
              break;
          }
                                  
                                   
                                    fclose($file);
                                } 
        return view('gallery',compact('album'));
      }
    }

    public function getImages()
    {
      $album = Input::get('a');
      $col = new Collection;

      switch ($album) {
        case '1': {

                    //Back to the 90s
      
                  for($i=1; $i<=175; $i++){
                    $col->push(['lowsrc'=>"storage/uploads/thumb-backto90s-".$i.".jpg",
                                'fullsrc'=>"storage/uploads/backto90s-".$i.".jpg",
                                'description'=>"Back to the 90s Year End Party",
                                'category'=>"2018 Year End Party"]);

                  }

        } break;

        case '2': {
 
                    //Back to the 90s PHOTOBOOTH
      
                  for($i=1; $i<=120; $i++){
                    $col->push(['lowsrc'=>"storage/uploads/thumb-booth-".$i.".jpg",
                                'fullsrc'=>"storage/uploads/booth-".$i.".jpg",
                                'description'=>"Photo booth: Back to the 90s Year End Party",
                                'category'=>"Photo booth: 2018 Year End Party"]);

                  }

        } break;

        case '3': {
 
                    //Back to the 90s PHOTOBOOTH
      
                  for($i=1; $i<=333; $i++){
                    $col->push(['lowsrc'=>"storage/uploads/thumb-official2018cam1-".$i.".jpg",
                                'fullsrc'=>"storage/uploads/official2018cam1-".$i.".jpg",
                                'description'=>"Cam1: Back to the 90s Year End Party",
                                'category'=>"2018 Year End Party [cam1]"]);

                  }

        } break;


        case '4': {
 
                    //Back to the 90s PHOTOBOOTH
      
                  for($i=1; $i<=330; $i++){
                    $col->push(['lowsrc'=>"storage/uploads/thumb-official2018cam2-".$i.".jpg",
                                'fullsrc'=>"storage/uploads/official2018cam2-".$i.".jpg",
                                'description'=>"Cam2: Back to the 90s Year End Party",
                                'category'=>"2018 Year End Party [cam2]"]);

                  }

        } break;

        case '5': {
 
                    //LETS GET PHYSICAL
      
                  for($i=137; $i>=1; $i--){
                    $col->push(['lowsrc'=>"storage/uploads/thumb-letsgetphysical-".$i.".jpg",
                                'fullsrc'=>"storage/uploads/letsgetphysical-".$i.".jpg",
                                'description'=>"Let's Get Physical",
                                'category'=>"Let's Get Physical"]);

                  }

        } break;


        case '6': {
 
                    //CATRIONA
      
                  for($i=1; $i<=46; $i++){
                    $col->push(['lowsrc'=>"storage/uploads/thumb-cat-".$i.".jpg",
                                'fullsrc'=>"storage/uploads/cat-".$i.".jpg",
                                'description'=>"Catriona Gray Homecoming",
                                'category'=>"Catriona Gray Homecoming"]);

                  }

        } break;

        case '7': {
 
                    //happy hr
      
                  for($i=1; $i<=53; $i++){
                    $col->push(['lowsrc'=>"storage/uploads/thumb-happyhr-".$i.".jpg",
                                'fullsrc'=>"storage/uploads/happyhr-".$i.".jpg",
                                'description'=>"Happy Hour [Sat Apr.20 2019]",
                                'category'=>"Happy Hour"]);

                  }

        } break;

        case '8': {
 
                    //pajama hr
      
                  for($i=1; $i<=16; $i++){
                    $col->push(['lowsrc'=>"storage/uploads/pajama-".$i.".jpg",
                                'fullsrc'=>"storage/uploads/pajama-".$i.".jpg",
                                'description'=>"Wear Your Pajama To Work Day [Tue Apr.16 2019]",
                                'category'=>"Wear Your Pajama To Work Day"]);

                  }

        } break;

        case '9': {
 
                    //CINCO
      
                  for($i=1; $i<=153; $i++){
                    $col->push(['lowsrc'=>"storage/uploads/thumb-cinco-".$i.".jpg",
                                'fullsrc'=>"storage/uploads/cinco-".$i.".jpg",
                                'description'=>"Cinco De Mayo [Sat May 04 2019]",
                                'category'=>"Cinco De Mayo [Sat May 04 2019]"]);

                  }

        } break;


        case '10': {
 
                    //BTS
      
                  for($i=1; $i<=82; $i++){
                    $col->push(['lowsrc'=>"storage/uploads/thumb-bts-".$i.".jpg",
                                'fullsrc'=>"storage/uploads/bts-".$i.".jpg",
                                'description'=>"BTS: We Speak Your Language [Fri May 17 2019] Photos by: Wendy Pilar",
                                'category'=>"BTS: We Speak Your Language [Fri May 17 2019]"]);

                  }

        } break;

        case '11': {
 
                    //Health and Wellness
      
                  for($i=1; $i<=32; $i++){
                    $col->push(['lowsrc'=>"storage/uploads/thumb-health-".$i.".jpg",
                                'fullsrc'=>"storage/uploads/health-".$i.".jpg",
                                'description'=>"Health &amp; Wellness Program [Fri May 24 2019] Photos by: Wendy Pilar",
                                'category'=>"Health &amp; Wellness Program [Fri May 24 2019] Photos by: Wendy Pillar"]);

                  }

        } break;

        case '12': {
 
                    //Pride 2019
      
                  for($i=1; $i<=60; $i++){
                    $col->push(['lowsrc'=>"storage/uploads/thumb-pride2019-".$i.".jpg",
                                'fullsrc'=>"storage/uploads/pride2019-".$i.".jpg",
                                'description'=>"Pride March [Sat Jun 29 2019] Photos by: Wendy Pilar",
                                'category'=>"Pride March [Sat Jun 29 2019] Photos by: Wendy Pillar"]);

                  }

        } break;

        case '13': {
 
                    //G2 LAUNCHING 2019
      
                  for($i=1; $i<=30; $i++){
                    $col->push(['lowsrc'=>"storage/uploads/thumb-g2-ribbon-mike-".$i.".jpg",
                                'fullsrc'=>"storage/uploads/g2-ribbon-mike-".$i.".jpg",
                                'description'=>"G2 Office Launching 07/12/2019 (Photo by: Mike Pamero)",
                                'category'=>"G2 Office Launching 07/12/2019 (Photo by: Mike Pamero)"]);
                  }

                  for($i=1; $i<=61; $i++){
                    $col->push(['lowsrc'=>"storage/uploads/thumb-g2-ribbon-artem-".$i.".jpg",
                                'fullsrc'=>"storage/uploads/g2-ribbon-artem--".$i.".jpg",
                                'description'=>"G2 Office Launching 07/12/2019 (Photo by: Artem Levykin)",
                                'category'=>"G2 Office Launching 07/12/2019 (Photo by: Artem Levykin)"]);
                  }

                   for($i=1; $i<=51; $i++){
                    $col->push(['lowsrc'=>"storage/uploads/thumb-g2-ribbon-wendy-".$i.".jpg",
                                'fullsrc'=>"storage/uploads/g2-ribbon-wendy-".$i.".jpg",
                                'description'=>"G2 Office Launching 07/12/2019 (Photo by: Wendy Pilar)",
                                'category'=>"G2 Office Launching 07/12/2019 (Photo by: Wendy Pilar)"]);
                  }

        } break;

        case '14': {
 
                    //G2 AFTER PARTY 2019
      
                  for($i=1; $i<=60; $i++){
                    $col->push(['lowsrc'=>"storage/uploads/thumb-g2-after-mike-".$i.".jpg",
                                'fullsrc'=>"storage/uploads/g2-after-mike-".$i.".jpg",
                                'description'=>"G2 Office Launching - After Party @ Pura Vida 07/12/2019 (Photo by: Mike Pamero)",
                                'category'=>"G2 Office Launching - After Party @ Pura Vida 07/12/2019 (Photo by: Mike Pamero)"]);

                  }

        } break;

        case '15': {
 
                    //G2 Photobooth
      
                  for($i=1; $i<=50; $i++){
                    $col->push(['lowsrc'=>"storage/uploads/thumb-g2-".$i.".jpg",
                                'fullsrc'=>"storage/uploads/g2-".$i.".jpg",
                                'description'=>"G2 Office Launching - Photobooth 07/12/2019 (Photo by: Pose &amp; Print)",
                                'category'=>"G2 Office Launching - Photobooth 07/12/2019 (Photo by: Pose &amp; Print)"]);

                  }

        } break;

        case '16': {
 
                    //Davao Health
      
                  for($i=1; $i<=12; $i++){
                    $col->push(['lowsrc'=>"storage/uploads/davao-".$i.".jpg",
                                'fullsrc'=>"storage/uploads/davao-".$i.".jpg",
                                'description'=>"Davao Health &amp; Wellness 07/25/2019 (Photo by: Dang Maulingan)",
                                'category'=>"Davao Health &amp; Wellness 07/25/2019 (Photo by: Dang Maulingan)"]);

                  }

        } break;


        // case '7': {
 
        //             //OAM FAMILY
        //           $allUsers = User::orderBy('lastname', 'ASC')->get();
        //           $users = $allUsers->filter(function($emp){
        //               return $emp->lastname != '' && $emp->lastname != ' ';

        //           });
        //           $activeUsers = $allUsers->filter(function($emp){
        //             return $emp->lastname != '' && $emp->lastname != ' ' && $emp->status_id !== 7 && $emp->status_id !== 8 && $emp->status_id !== 9 ;
        //           });

        //           foreach ($activeUsers as $key) {
        //              $col->push(['lowsrc'=>"public/img/employees/".$key->id.".jpg",
        //                         'fullsrc'=>"public/img/employees/".$key->id.".jpg",
        //                         'description'=>$key->lastname.", ".$key->firstname,
        //                         'category'=>"Open Access Family"]);
        //            } 
      
                  

        // } break;


        
        default: {

                  //Spooky Winners
      
                  for($i=1; $i<=7; $i++){
                    $col->push(['lowsrc'=>"storage/uploads/thumb-spookywinners-".$i.".jpg",
                                'fullsrc'=>"storage/uploads/spookywinners-".$i.".jpg",
                                'description'=>"Halloween 2018 Winners",
                                'category'=>"Halloween 2018 Winners"]);

                  }

                  //Spooky Team
                  
                  for($i=1; $i<=12; $i++){
                    $col->push(['lowsrc'=>"storage/uploads/thumb-spooky-entries".$i.".jpg",
                                'fullsrc'=>"storage/uploads/spooky-entries".$i.".jpg",
                                'description'=>"Open Access Spooky Team 2018",
                                'category'=>"Open Access Spooky Team 2018"]);

                  }

                   // Runner Euniz
                  for($i=1; $i<=6; $i++){
                    $col->push(['lowsrc'=>"storage/uploads/thumb-runner-euniz".$i.".jpg",
                                'fullsrc'=>"storage/uploads/runner-euniz".$i.".jpg",
                                'description'=>"2018 TCS New York City Marathon - Euniz Cantos",
                                'category'=>"2018 TCS New York City Marathon"]);

                  }

                   // Runner Clint
                  for($i=1; $i<=5; $i++){
                    $col->push(['lowsrc'=>"storage/uploads/thumb-runner-clint".$i.".jpg",
                                'fullsrc'=>"storage/uploads/runner-clint".$i.".jpg",
                                'description'=>"2018 TCS New York City Marathon - Clint Ortiz",
                                'category'=>"2018 TCS New York City Marathon"]);

                  }

                   // Runner Jeff
                  for($i=1; $i<=5; $i++){
                    $col->push(['lowsrc'=>"storage/uploads/thumb-runner-jeff".$i.".jpg",
                                'fullsrc'=>"storage/uploads/runner-jeff".$i.".jpg",
                                'description'=>"2018 TCS New York City Marathon - Jeff Aspacio",
                                'category'=>"2018 TCS New York City Marathon"]);

                  }


                    // Dress up
                  for($i=1; $i<=24; $i++){
                    $col->push(['lowsrc'=>"storage/uploads/thumb-dressupwinner".$i.".jpg",
                                'fullsrc'=>"storage/uploads/dressupWinner".$i.".jpg",
                                'description'=>"Dress Up Your Leader Contest - CS WEEK 2018",
                                'category'=>"Dress Up Your Leader 2018"]);

                  }


                   // CS WEEK
                  for($i=1; $i<=32; $i++){
                    $col->push(['lowsrc'=>"storage/uploads/thumb-photobooth".$i.".jpg",
                                'fullsrc'=>"storage/uploads/photobooth".$i.".jpg",
                                'description'=>"Thank you all for participating in this year's CS Week!",
                                'category'=>"Photobooth"]);

                  }

                  // CS WEEK
                  for($i=1; $i<=22; $i++){
                    $col->push(['lowsrc'=>"storage/uploads/thumb-cs_".$i.".jpg",
                                'fullsrc'=>"storage/uploads/cs_".$i.".jpg",
                                'description'=>"Thank you all for participating in this year's CS Week!",
                                'category'=>"CS Week 2018"]);

                  }

                  //appreciation
                  for($i=1; $i<=10; $i++){
                    $col->push(['lowsrc'=>"storage/uploads/thumb-appreciate".$i.".jpg",
                                'fullsrc'=>"storage/uploads/appreciate".$i.".jpg",
                                'description'=>"\"Gratitude can transform common days into Thanksgiving, turn routine jobs into joy and change opportunities into blessings.\" - William Arthur Ward ",
                                'category'=>"CS Week 2018"]);

                  }

                  //donuts
                  for($i=1; $i<=24; $i++){
                    $col->push(['lowsrc'=>"storage/uploads/thumb-donuts".$i.".jpg",
                                'fullsrc'=>"storage/uploads/donuts".$i.".jpg",
                                'description'=>"Dunkin' is in the house! Get your free donuts and coffee at the 8th floor pantry today. Happy <a href=\"https://www.instagram.com/explore/tags/csweek/\" target=\"_blank\">#CSWeek </a>everyone! ",
                                'category'=>"Dunkin' In The House"]);

                  }


                  //wellness
                  for($i=1; $i<=11; $i++){
                    $col->push(['lowsrc'=>"storage/uploads/thumb-wellness".$i.".jpg",
                                'fullsrc'=>"storage/uploads/wellness".$i.".jpg",
                                'description'=>"Thanks to all employees who visited the booths at the 8th floor and to our partners for helping us keep our employees' health in check.",
                                'category'=>"Health and Wellness Program"]);

                  }

                   if($this->user->id !== 564 ) {
                                  $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
                                    fwrite($file, "-------------------\n Viewed Gallery by [". $this->user->id."] ".$this->user->lastname."\n");
                                    fclose($file);
                                } 



        } break;
      }

      

      return response()->json($col);
    }

    
    public function index()
    {
        
       
       $coll = new Collection;
       $user = $this->user; 
       DB::connection()->disableQueryLog();
       $forms = new Collection;



       if ( is_null($user->nickname) ) $greeting = $user->firstname;
       else $greeting = $user->nickname;

       $leadershipcheck = ImmediateHead::where('employeeNumber', $this->user->employeeNumber)->first();
       $canDo = UserType::find($this->user->userType_id)->roles->where('label','QUERY_REPORTS');
        if (count($canDo)> 0 ) $reportsTeam=1; else $reportsTeam=0;

       /* ---------------------------------------------------------*/
       /* --- WE NOW CHECK FOR CAMPAIGN WIDGETS from FormBuilder --*/

       if (empty($leadershipcheck)) {
            $myCampaign = collect($this->user->campaign->first()->id);$prg=$myCampaign;} 
       else { 
              $myCampaign = $leadershipcheck->myCampaigns->groupBy('campaign_id')->keys();
              $prg = collect(Campaign::where('name',"Postmates")->first()->id); //for widget
              }//end if else

          foreach ($myCampaign as $c) {
            $d = DB::table('campaign_forms')->where('campaign_id','=',$c)->
                  join('formBuilder','campaign_forms.formBuilder_id','=','formBuilder.id')->
                  join('campaign','campaign_forms.campaign_id','=','campaign.id')->
                  leftJoin('formBuilder_items','formBuilder_items.formBuilder_id','=','campaign_forms.formBuilder_id')->
                  leftJoin('formBuilder_elements','formBuilder_items.formBuilder_elemID','=', 'formBuilder_elements.id')->//get();
                  leftJoin('formBuilderSubtypes','formBuilder_items.formBuilder_subTypeID','=','formBuilderSubtypes.id')->
                  leftJoin('formBuilderElem_values','formBuilderElem_values.formBuilder_itemID','=','formBuilder_items.id')->
                  select('campaign.name as program','formBuilder.title as widgetTitle','campaign_forms.enabled','formBuilder_elements.type as type', 
                    'formBuilderSubtypes.name as subType','formBuilder_items.label as label','formBuilder_items.name as itemName','formBuilder_items.placeholder','formBuilder_items.required','formBuilder_items.formOrder','formBuilder_items.id as itemID','formBuilder.id as formID', 'formBuilderElem_values.value','formBuilderElem_values.label as optionLabel', 'formBuilderElem_values.formBuilder_itemID as selectGroup','formBuilderElem_values.selected', 'formBuilder_items.className')->orderBy('formBuilder.id','DESC')->get(); 


                  if (!empty($d)) $forms->push($d);

          }//end foreach

          if (!empty($forms) && !$reportsTeam){

            
            $widget = collect($forms->first());
            $groupedForm = $widget->groupBy('widgetTitle');
            $groupedSelects = $widget->groupBy('selectGroup');

            //return $groupedForm;

           

          }else
          {

            if ($reportsTeam==1){

              $prg = Campaign::where('name',"Postmates")->first();
              $d = DB::table('campaign_forms')->where('campaign_id','=',$prg->id)->
                    join('formBuilder','campaign_forms.formBuilder_id','=','formBuilder.id')->
                    join('campaign','campaign_forms.campaign_id','=','campaign.id')->
                    leftJoin('formBuilder_items','formBuilder_items.formBuilder_id','=','campaign_forms.formBuilder_id')->
                    leftJoin('formBuilder_elements','formBuilder_items.formBuilder_elemID','=', 'formBuilder_elements.id')->//get();
                    leftJoin('formBuilderSubtypes','formBuilder_items.formBuilder_subTypeID','=','formBuilderSubtypes.id')->
                    leftJoin('formBuilderElem_values','formBuilderElem_values.formBuilder_itemID','=','formBuilder_items.id')->
                    select('campaign.name as program','formBuilder.title as widgetTitle','campaign_forms.enabled','formBuilder_elements.type as type', 
                      'formBuilderSubtypes.name as subType','formBuilder_items.label as label','formBuilder_items.name as itemName','formBuilder_items.placeholder','formBuilder_items.required','formBuilder_items.formOrder','formBuilder_items.id as itemID','formBuilder.id as formID', 'formBuilderElem_values.value','formBuilderElem_values.label as optionLabel', 'formBuilderElem_values.formBuilder_itemID as selectGroup','formBuilderElem_values.selected', 'formBuilder_items.className')->get(); 


                    if (!empty($d)) $forms->push($d);

                    $widget = collect($forms->first());
                    $groupedForm = $widget->groupBy('widgetTitle');
                    $groupedSelects = $widget->groupBy('selectGroup');

            }else {
              $groupedForm = null; $groupedSelects=null;
            }
            
          }

          $forApprovals = $this->getDashboardNotifs();// $this->getApprovalNotifs();USER TRAIT

            /************* PERFORMANCE EVALS ***************/
            $userEvals = new Collection; $performance = new Collection;

             $userEvals1 = $user->evaluations->sortBy('created_at')->filter(function($eval){
                                      return $eval->overAllScore > 0;

                          }); //->pluck('created_at','evalSetting_id','overAllScore'); 

            
            $byDateEvals =  $userEvals1->groupBy(function($pool) {
                                          return Carbon::parse($pool->created_at)->format('Y-m-d');
                                      });

            $userEvals = new Collection;

            foreach ($byDateEvals as $evs) {
              $key = $evs->unique('overAllScore');

              switch ($key->first()->evalSetting_id) {
                    case '1':{
                                $performance->push(['type'=>date('Y', strtotime($key->first()->startPeriod))." Jan-Jun", 'score'=>$key->first()->overAllScore]);

                            }
                       
                        break;

                    case '2':{
                                $performance->push(['type'=>date('Y', strtotime($key->first()->startPeriod))." Jul-Dec", 'score'=>$key->first()->overAllScore]);

                    }
                        # code...
                        break;
                    
                    
                }

            };

            /************* for SURVEY WIDGET ***************/
            $doneS = DB::table('survey_user')->where('user_id',$this->user->id)->where('isDone',1)->get();
            (count($doneS) > 0) ? $doneSurvey=1 : $doneSurvey=0;


            /************* for TIMEKEEPING WIDGET ***************/

            //check if user has already logged in

            $startToday = Carbon::now('GMT+8');
            
            if ($startToday->format('H:i') > date('H:i',strtotime('12AM')) && $startToday->format('H:i') <= date('H:i', strtotime('8AM')) ) //for those with 11pm-8am shift
            {
              
              $tomBio = Biometrics::where('productionDate', Carbon::now('GMT+8')->addHours(-12)->format('Y-m-d'))->get();
              //return $tomBio;
              if (count($tomBio) > 0)
                $b = $tomBio->first();
              else {
                $b = new Biometrics;
                $b->productionDate = Carbon::now('GMT+8')->addHours(-12)->format('Y-m-d');
                $b->save();

              }
              

            }else {

              
              $tomBio = Biometrics::where('productionDate', Carbon::now('GMT+8')->format('Y-m-d'))->get();
              if (count($tomBio) > 0)
                $b = $tomBio->first();
              else {
                $b = new Biometrics;
                $b->productionDate = Carbon::now('GMT+8')->format('Y-m-d');
                $b->save();

              }
             
            }

            $loggedIn = Logs::where('user_id',$this->user->id)->where('logType_id','1')->where('biometrics_id',$b->id)->get();

            if (count($loggedIn) > 0) $alreadyLoggedIN=true; else $alreadyLoggedIN=false;
            
            //return response()->json(['startToday'=>$startToday->format('H:i'), 'log'=> $loggedIn, 'alreadyLoggedIN'=>$alreadyLoggedIN]);// $loggedIn;

               


                /************* SHOUT OUT ***************/
                $bago = Carbon::today()->subWeeks(1);
                $annivs = Carbon::today()->subWeeks(1)->subYear(1);
                $annivs2 = Carbon::today()->subYear(1);
                $anniv10s = Carbon::today()->subYear(10)->startOfYear();
                $anniv10e = Carbon::today()->subYear(10)->endOfYear();
                $anniv5s = Carbon::today()->subYear(5)->startOfYear();
                $anniv5e = Carbon::today()->subYear(5)->endOfYear();
                
                $newHires = DB::table('users')->where('dateHired','>=',$bago->format('Y-m-d'))->where('status_id','!=',6)->where('status_id','!=',7)->where('status_id','!=',8)->where('status_id','!=',9)->where('status_id','!=',7)->leftJoin('positions','users.position_id','=', 'positions.id')->leftJoin('team','team.user_id','=','users.id')->leftJoin('campaign','campaign.id','=','team.campaign_id')->leftJoin('campaign_logos','campaign.id','=','campaign_logos.campaign_id')->select('users.id','users.hascoverphoto',  'users.firstname','users.lastname','users.nickname','positions.name','campaign_logos.filename','team.campaign_id', 'users.dateHired')->orderBy('users.dateHired','DESC')->get();

                $firstYears = DB::table('users')->where('dateHired','>=',$annivs->format('Y-m-d H:i:s'))->where('dateHired','<=',$annivs2->format('Y-m-d H:i:s'))->where('status_id','!=',6)->where('status_id','!=',7)->where('status_id','!=',8)->where('status_id','!=',9)->leftJoin('positions','users.position_id','=', 'positions.id')->leftJoin('team','team.user_id','=','users.id')->leftJoin('campaign','campaign.id','=','team.campaign_id')->leftJoin('campaign_logos','campaign.id','=','campaign_logos.campaign_id')->select('users.id','users.hascoverphoto',  'users.firstname','users.lastname','users.nickname','positions.name','campaign_logos.filename','team.campaign_id', 'users.dateHired')->orderBy('users.dateHired','DESC')->get();

                $tenYears = DB::table('users')->where('dateHired','>=',$anniv10s->format('Y-m-d H:i:s'))->where('dateHired','<=',$anniv10e->format('Y-m-d H:i:s'))->where('status_id','!=',6)->where('status_id','!=',7)->where('status_id','!=',8)->where('status_id','!=',9)->leftJoin('positions','users.position_id','=', 'positions.id')->leftJoin('team','team.user_id','=','users.id')->leftJoin('campaign','campaign.id','=','team.campaign_id')->leftJoin('campaign_logos','campaign.id','=','campaign_logos.campaign_id')->select('users.id','users.hascoverphoto',  'users.firstname','users.lastname','users.nickname','positions.name','campaign_logos.filename','team.campaign_id', 'users.dateHired')->orderBy('users.dateHired','DESC')->get();

                $fiveYears = DB::table('users')->where('dateHired','>=',$anniv5s->format('Y-m-d H:i:s'))->where('dateHired','<=',$anniv5e->format('Y-m-d H:i:s'))->where('status_id','!=',6)->where('status_id','!=',7)->where('status_id','!=',8)->where('status_id','!=',9)->leftJoin('positions','users.position_id','=', 'positions.id')->leftJoin('team','team.user_id','=','users.id')->leftJoin('campaign','campaign.id','=','team.campaign_id')->leftJoin('campaign_logos','campaign.id','=','campaign_logos.campaign_id')->select('users.id','users.hascoverphoto',  'users.firstname','users.lastname','users.nickname','positions.name','campaign_logos.filename','team.campaign_id', 'users.dateHired')->orderBy('team.campaign_id','ASC')->get();

                //$filtered = array_group_by($newHires,'id');

                //return $firstYears;

               
                $evalTypes = EvalType::all();
                //$evalSetting = EvalSetting::all()->first();
                // --------- temporarily we set it for Semi annual of July to Dec 
                $evalSetting = EvalSetting::find(2);

                
                $currentPeriod = Carbon::create((date("Y")-1), $evalSetting->startMonth, $evalSetting->startDate,0,0,0, 'Asia/Manila');
                $endPeriod = Carbon::create((date("Y")-1), $evalSetting->endMonth, $evalSetting->endDate,0,0,0, 'Asia/Manila');

                $doneEval = new Collection;
                $pendingEval = new Collection;

                
                $leadershipcheck = ImmediateHead::where('employeeNumber', $this->user->employeeNumber)->get();


                //******* show memo for test people only jill,paz,ems,joy,raf,jaja, lothar, inguengan
                /*$testgroup = [564,508,1644,1611,1784,1786,491, 471, 367,1,184,344];
                if (in_array($this->user->id, $testgroup))
                {*/
                  /*----------- check for available MEMOS --------------*/
                    $activeMemo = Memo::where('active',1)->where('type','modal')->orderBy('created_at','DESC')->get();
                    if (count($activeMemo)>0){
                      $memo = $activeMemo->first();

                      //check if nakita na ni user yung memo
                      $seenMemo = User_Memo::where('user_id',$this->user->id)->where('memo_id',$memo->id)->get();
                      if (count($seenMemo)>0)
                        $notedMemo = true;
                      else $notedMemo = false;

                    }else { $notedMemo=false; $memo=null; } 
/*
                }else {$notedMemo=false; $memo=null;}*/


                

               


                 /*----------- check if done with TOUR --------------*/

                $tour = Memo::where('active',1)->where('type',"tour")->orderBy('created_at','DESC')->get();
                if (count($tour)>0){
                  $siteTour = $tour->first();
                 // $memo=null; $notedMemo=true;

                  //check if nakita na ni user yung memo
                  $toured = User_Memo::where('user_id',$this->user->id)->where('memo_id',$siteTour->id)->get();
                  if (count($toured)>0)
                    $notedTour = true;
                  else $notedTour = false;

                }else { $notedTour=false; $siteTour=null; } 
                

                //return $pass = bcrypt('mramido'); //$2y$10$IQqrVA8oK9uedQYK/8Z4Ae9ttvkGr/rGrwrQ6JVKdobMBt/5Mj4Ja

                // ------------------------------------------------------------------------------ if user has no subordinates -----------
               
                
                //return $prg;
                if (( ($this->user->userType->name == "HR admin") && count($leadershipcheck)==0 ) || strpos($this->user->userType->name, "agent")!==false   )
                    

                { //  AGENT or ADMIN pero agent level
                    $employee = User::find($this->user->id);
                    $meLeader = $employee->supervisor->first(); 
                    //return redirect()->route('user.show',['id'=>$this->user->id]);
                    //return redirect('UserController@show',$this->user->id);
                    //return $groupedSelects;
                    
                    return view('dashboard-agent', compact('performance','doneSurvey', 'firstYears','tenYears','fiveYears', 'newHires',  'unseenNotifs',  'currentPeriod','endPeriod', 'evalTypes', 'evalSetting', 'user','greeting','groupedForm','groupedSelects','reportsTeam','memo','notedMemo','alreadyLoggedIN','prg','siteTour','notedTour'));
                    


                // ------------------------------------------------------------------------------ endif user has no subordinates -----------

                } else {

                    //-- Initialize Approvals Dashlet

                   
                    return view('dashboard', compact('performance', 'doneSurvey', 'firstYears','tenYears','fiveYears', 'newHires', 'forApprovals', 'unseenNotifs', 'mySubordinates', 'currentPeriod','endPeriod', 'evalTypes', 'evalSetting', 'user','greeting','groupedForm','groupedSelects','reportsTeam','memo','notedMemo','alreadyLoggedIN','prg','siteTour','notedTour'));
                   


                } 

      
        
    }

    public function module()
    {
        return view('under-construction');
    }

    public function logAction($action)
    {
      $correct = Carbon::now('GMT+8'); //->timezoneName();

      switch ($action) {
        case '1':{
                    if($this->user->id !== 564 ) {
                      $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
                        fwrite($file, "-------------------\n Clicked slider ad by [". $this->user->id."] ".$this->user->lastname."\n");
                        fclose($file);
                    } 

        }
          # code...
          break;
        case '2':{
                    if($this->user->id !== 564 ) {
                      $user = User::find(Input::get('id'));
                      $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
                        fwrite($file, "-------------------\n Viewed DTR of: ".$user->lastname."[".$user->id."] --" . $correct->format('M d h:i A'). " by [". $this->user->id."] ".$this->user->lastname."\n");
                        fclose($file);
                    } 

        }break;

        case '3':{
                    if($this->user->id !== 564 ) {
                      $user = User::find(Input::get('id'));
                      $formID = Input::get('formid');
                      $usersubmit = Input::get('usersubmit');
                      $file = fopen('public/build/postmates.txt', 'a') or die("Unable to open logs");
                        fwrite($file, "-------------------\n [".$formID."] ; [".$usersubmit."] ; widget ; " . $correct->format('M d h:i A'). " by ;". $this->user->id."; ".$this->user->lastname."\n");
                        fclose($file);
                    } 

        }break;

        case '4':{
                    if($this->user->id !== 564 ) {
                      $user = User::find(Input::get('id'));
                      $formID = Input::get('formid');
                      $usersubmit = Input::get('usersubmit');
                      $file = fopen('public/build/postmates.txt', 'a') or die("Unable to open logs");
                        fwrite($file, "-------------------\n [".$formID."] ; [".$usersubmit."] ; tab ; " . $correct->format('M d h:i A'). " by ;". $this->user->id."; ".$this->user->lastname."\n");
                        fclose($file);
                    } 

        }break;

        case '5':{
                    if($this->user->id !== 564 ) {
                      $user = User::find(Input::get('id'));
                      $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
                        fwrite($file, "-------------------\n Played G2 Video by [". $this->user->id."] ".$this->user->lastname." on ". $correct->format('M d h:i A').  "\n");
                        fclose($file);
                    } 

        }break;

        case '6':{
                    if($this->user->id !== 564 ) {
                      $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
                        fwrite($file, "-------------------\n View 360Pose by [". $this->user->id."] ".$this->user->lastname." on ". $correct->format('M d h:i A').  "\n");
                        fclose($file);
                    } 

        }

        case '7':{
                    if($this->user->id !== 564 ) {
                      $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
                        fwrite($file, "-------------------\n View Booth by [". $this->user->id."] ".$this->user->lastname." on ". $correct->format('M d h:i A').  "\n");
                        fclose($file);
                    } 

        }

        case '8':{
                    if($this->user->id !== 564 ) {
                      $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
                        fwrite($file, "-------------------\n View YEP1 by [". $this->user->id."] ".$this->user->lastname." on ". $correct->format('M d h:i A').  "\n");
                        fclose($file);
                    } 

        }

        case '9':{
                    if($this->user->id !== 564 ) {
                      $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
                        fwrite($file, "-------------------\n View YEP2 by [". $this->user->id."] ".$this->user->lastname." on ". $correct->format('M d h:i A').  "\n");
                        fclose($file);
                    } 

        }
        
        case '10':{
                    if($this->user->id !== 564 ) {
                      $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
                        fwrite($file, "-------------------\n View EvalSum [". $this->user->id."] ".$this->user->lastname." on ". $correct->format('M d h:i A').  "\n");
                        fclose($file);
                    } 

        }

        case '11':{
                    if($this->user->id !== 564 ) {
                      $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
                        fwrite($file, "-------------------\n View Physical [". $this->user->id."] ".$this->user->lastname." on ". $correct->format('M d h:i A').  "\n");
                        fclose($file);
                    } 

        }

         case '12':{
                    if($this->user->id !== 564 ) {
                      $user = User::find(Input::get('id'));
                      $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
                        fwrite($file, "-------------------\n Played Queen by [". $this->user->id."] ".$this->user->lastname." on ". $correct->format('M d h:i A').  "\n");
                        fclose($file);
                    } 

        }break;

         case '13':{
                    if($this->user->id !== 564 ) {
                      $user = User::find(Input::get('id'));
                      $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
                        fwrite($file, "-------------------\n Played Parokya by [". $this->user->id."] ".$this->user->lastname." on ". $correct->format('M d h:i A').  "\n");
                        fclose($file);
                    } 

        }break;
        
      }
      return response()->json(['success'=>"1"]);

           
    }
}
