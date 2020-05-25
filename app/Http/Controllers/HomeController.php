<?php

namespace OAMPI_Eval\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use \DB;
use \Hash;
use Excel;
use \Mail;
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
use OAMPI_Eval\User_CCTV;
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

use OAMPI_Eval\Task;
use OAMPI_Eval\Task_Campaign;
use OAMPI_Eval\Task_User;
use OAMPI_Eval\Taskbreak_User;
use OAMPI_Eval\TaskGroup;
use OAMPI_Eval\Symptoms_User;
use OAMPI_Eval\Symptoms_Declaration;


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
      $total = null;

      


      if (empty($album))
        return view('gallery',['album'=>null]);
      else
      {
        $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
         if($this->user->id !== 564 ) {

          switch ($album) {

            case '24':{
               $total = 118;
               fwrite($file, "-------------------\n Official_Mono by [". $this->user->id."] ".$this->user->lastname." on". $correct->format('M d h:i A'). "\n");

            }break;

            case '23':{
               $total = 87;
               fwrite($file, "-------------------\n PB2_Mono by [". $this->user->id."] ".$this->user->lastname." on". $correct->format('M d h:i A'). "\n");

            }break;

            case '22':{
               $total = 104;
               fwrite($file, "-------------------\n PB1_Mono by [". $this->user->id."] ".$this->user->lastname." on". $correct->format('M d h:i A'). "\n");

            }break;

            case '21':{
               $total = 534;
               fwrite($file, "-------------------\n Monochrome by [". $this->user->id."] ".$this->user->lastname." on". $correct->format('M d h:i A'). "\n");

            }break;

            case '20':{
               fwrite($file, "-------------------\n OktoberGall by [". $this->user->id."] ".$this->user->lastname." on". $correct->format('M d h:i A'). "\n");

            }break;

            case '19':{
               fwrite($file, "-------------------\n Booth CSweek2019 [". $this->user->id."] ".$this->user->lastname." on". $correct->format('M d h:i A'). "\n");

            }break;

            case '18':{
               fwrite($file, "-------------------\n TBT CSweek2018 [". $this->user->id."] ".$this->user->lastname." on". $correct->format('M d h:i A'). "\n");

            }break;

            case '17':{
               fwrite($file, "-------------------\n Davao Anniv [". $this->user->id."] ".$this->user->lastname." on". $correct->format('M d h:i A'). "\n");

            }break;

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
        return view('gallery',compact('album','total'));
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
      
                  for($i=143; $i>=1; $i--){
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

        case '17': {
 
                    //Davao anniv
      
                  for($i=1; $i<=36; $i++){
                    $col->push(['lowsrc'=>"storage/uploads/davao-anniv-".$i.".jpg",
                                'fullsrc'=>"storage/uploads/davao-anniv-".$i.".jpg",
                                'description'=>"Open Access BPO - Davao 4th Year Anniversary",
                                'category'=>"Open Access BPO - Davao 4th Year Anniversary"]);

                  }

        } break;


        //CS WEEK 2018 THROWBACK

        case '18': {

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


        }break;


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

        case '19': {
 
                    //CSWEEK 2019 BOOTH
      
                  for($i=1; $i<=65; $i++){
                    $col->push(['lowsrc'=>"storage/uploads/cs2019booth-".$i."_thumb.jpg",
                                'fullsrc'=>"storage/uploads/cs2019booth-".$i.".jpg",
                                'description'=>"Photobooth CS Week 2019",
                                'category'=>"Photobooth CS Week 2019"]);

                  }

        } break;


        case '20': {
 
                    //Davao anniv
      
                  for($i=1; $i<=91; $i++){
                    $col->push(['lowsrc'=>"storage/uploads/oktoberfest2019_thumb-".$i.".jpg",
                                'fullsrc'=>"storage/uploads/oktoberfest2019-".$i.".jpg",
                                'description'=>"Oktoberfest 2019 @ The Ruins, Poblacion \n[Photo credits: Mike Pamero]",
                                'category'=>"Oktoberfest 2019"]);

                  }

                  for($i=94; $i<=152; $i++){
                    $col->push(['lowsrc'=>"storage/uploads/oktoberfest2019_thumb-".$i.".jpg",
                                'fullsrc'=>"storage/uploads/oktoberfest2019-".$i.".jpg",
                                'description'=>"Oktoberfest 2019 @ The Ruins, Poblacion \n[Photo credits: Mike Pamero]",
                                'category'=>"Oktoberfest 2019"]);

                  }

        } break;

        case '21': {
 
                    //MONOCHROME 
      
                  for($i=1; $i<=534; $i++){
                    $col->push(['lowsrc'=>"storage/uploads/monochrome_thumb-".$i.".jpg",
                                'fullsrc'=>"storage/uploads/monochrome-".$i.".jpg",
                                'description'=>"Monochrome Year End Party @ Makati Shangri-La [12.15.2019] \n[Photo credits: Mike Pamero]",
                                'category'=>"Monochrome Year End Party"]);

                  }

                  

        } break;

       case '22': {
 
                    //MONOCHROME PHOTOBOOTH 1
      
                  for($i=1; $i<=104; $i++){
                    $col->push(['lowsrc'=>"storage/uploads/monochrome_booth1-".$i.".jpg",
                                'fullsrc'=>"storage/uploads/monochrome_booth1-".$i.".jpg",
                                'description'=>"Monochrome Year End Party (Photobooth 1) @ Makati Shangri-La [12.15.2019] \n",
                                'category'=>"Monochrome Year End Party Photobooth"]);

                  }


                  

        } break;

         case '23': {
 
                    //MONOCHROME PHOTOBOOTH 2
      
                  for($i=1; $i<=87; $i++){
                    $col->push(['lowsrc'=>"storage/uploads/monochrome_booth2-".$i.".jpg",
                                'fullsrc'=>"storage/uploads/monochrome_booth2-".$i.".jpg",
                                'description'=>"Monochrome Year End Party (Photobooth 2) @ Makati Shangri-La [12.15.2019] \n",
                                'category'=>"Monochrome Year End Party Photobooth"]);

                  }


                  

        } break;

        case '24': {
 
                    //MONOCHROME official
      
                  for($i=1; $i<=118; $i++){
                    $col->push(['lowsrc'=>"storage/uploads/mono_official-".$i.".jpg",
                                'fullsrc'=>"storage/uploads/mono_official-".$i.".jpg",
                                'description'=>"Monochrome Year End Party (Official) @ Makati Shangri-La [12.15.2019] \n",
                                'category'=>"Monochrome Year End Party (Official)"]);

                  }


                  

        } break;
        
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

    public function health()
    {
      return view('health');
    }

    public function healthForm()
    {
      $user = $this->user;
      $symptoms = DB::table('symptoms')->where('diagnosis',null)->select('id','name')->get();
      $diagnosis = DB::table('symptoms')->where('diagnosis',1)->select('id','name')->get();
      $questions = DB::table('symptoms_questions')->orderBy('ordering')->select('id','question','ordering')->get();
      $today = Carbon::now('GMT+8');
      return view('healthform', compact('user','today','symptoms','diagnosis','questions'));
    }

    public function healthForm_download()
    {
        DB::connection()->disableQueryLog();
        $correct = Carbon::now('GMT+8');
        $canAdminister = true;// ( count(UserType::find($this->user->userType_id)->roles->where('label','QUERY_REPORTS'))>0 ) ? true : false;

        $from = Input::get('from');
        
        $download = Input::get('dl');

        $rawData = new Collection;

        if (is_null(Input::get('from')))
        {
            $daystart = Carbon::now('GMT+8')->startOfDay(); $dayend = Carbon::now('GMT+8')->endOfDay();
        }
        else {
            $daystart = Carbon::parse(Input::get('from'),'Asia/Manila')->startOfDay(); 
            $dayend = Carbon::parse(Input::get('from'),'Asia/Manila')->endOfDay();
        }

       

        


        $allAnswers = DB::table('symptoms_user')->where('symptoms_user.created_at','>=',$daystart->format('Y-m-d H:i:s'))->
                  where('symptoms_user.created_at','<=',$dayend->format('Y-m-d H:i:s'))->
                  join('users','users.id','=','symptoms_user.user_id')->
                  leftJoin('team','users.id','=','team.user_id')->
                  leftJoin('campaign','team.campaign_id','=','campaign.id')->
                  select('users.firstname','users.lastname','users.id as user_id','campaign.name as program','symptoms_user.id as declareID', 'symptoms_user.question_id', 'symptoms_user.answer', 'symptoms_user.created_at')->
                  orderBy('symptoms_user.question_id','ASC')->get(); 

        $allSymptoms = DB::table('symptoms_user')->where('symptoms_user.created_at','>=',$daystart->format('Y-m-d H:i:s'))->
                  where('symptoms_user.created_at','<=',$dayend->format('Y-m-d H:i:s'))->
                  join('users','users.id','=','symptoms_user.user_id')->
                  join('symptoms_declare','symptoms_declare.user_answerID','=','symptoms_user.id')->
                  join('symptoms','symptoms.id','=','symptoms_declare.symptoms_id')->
                  select('users.firstname','users.lastname','users.id as user_id','symptoms_user.question_id', 'symptoms_user.answer','symptoms_declare.user_answerID as declareID', 'symptoms_declare.symptoms_id','symptoms.name as symptom', 'symptoms_declare.isDiagnosis', 'symptoms_user.created_at')->
                  orderBy('symptoms_user.created_at','DESC')->get(); 
        


        $allQuestions = DB::table('symptoms_questions')->get();
        $allRespondents = collect($allAnswers)->groupBy('user_id');

            // $q = collect($allECQ)->where('user_id',1097);
        //return response()->json(['allQuestions'=> $allQuestions, 'allAnswers'=>$allAnswers, 'allSymptoms'=>$allSymptoms,'allRespondents'=>$allRespondents]);

        
        $headers = array("Submitted", "Last Name","First Name","Program");

        foreach ($allQuestions as $q) {
          array_push($headers, $q->question);
        }
        
        $sheetTitle = "Health Declaration Form Responses [".$daystart->format('M d l')."]";
        $description = " ". $sheetTitle;

        if($this->user->id !== 564 ) {
          $file = fopen('public/build/rewards.txt', 'a') or die("Unable to open logs");
            fwrite($file, "-------------------\n DL_healthResponses [".$daystart->format('Y-m-d')."] " . $correct->format('M d h:i A'). " by [". $this->user->id."] ".$this->user->lastname."\n");
            fclose($file);
        }    


           Excel::create($sheetTitle,function($excel) use($allRespondents, $allAnswers,$allSymptoms, $headers,$description,$daystart) 
           {
                  $excel->setTitle('Health Declaration Form Responses');

                  // Chain the setters
                  $excel->setCreator('Programming Team')
                        ->setCompany('OpenAccess');

                  // Call them separately
                  $excel->setDescription($description);
                  $excel->sheet($daystart->format('M d l'), function($sheet) use ($allRespondents, $allAnswers,$allSymptoms, $headers)
                  {
                    $sheet->appendRow($headers);
                    foreach($allRespondents as $item)
                    {
                        //$t = Carbon::parse($item->serverTime);

                        // look for the answers
                        if ($item[0]->answer == 1)
                        {
                          $symp = collect($allSymptoms)->where('user_id',$item[0]->user_id)->where('declareID',$item[0]->declareID)->pluck('symptom');
                          $ans1 = "Yes : [ ";

                          foreach ($symp as $s) {
                            $ans1 .= $s.', ';
                          }
                          $ans1 .= "]";

                        }else $ans1 = "No";

                        
                        if (count($item) >= 5)
                        {
                          
                          $diagnosis = collect($allSymptoms)->where('user_id',$item[0]->user_id)->where('declareID',$item[4]->declareID)->pluck('symptom');

                          if ($item[4]->answer == 1)
                          {
                            $ans5 = "Yes : [ ";

                            foreach ($diagnosis as $s) {
                              $ans5 .= $s.', ';
                            }
                            $ans5 .= "]";

                          }else $ans5 = "No";


                          ($item[1]->answer == 1) ? $ans2="Yes" : $ans2="No";
                          ($item[2]->answer == 1) ? $ans3="Yes" : $ans3="No";
                          ($item[3]->answer == 1) ? $ans4="Yes" : $ans4="No";

                          $submitted = date('M d h:i A', strtotime($item[0]->created_at));
                          $arr = array(
                                     $submitted,
                                     $item[0]->lastname,
                                     $item[0]->firstname,
                                     $item[0]->program,
                                     $ans1, //Q1
                                     $ans2, //Q1
                                     $ans3,
                                     $ans4,
                                     $ans5 //Q1
                                     
                                     );
                          $sheet->appendRow($arr);

                        }
                        

                    }
                    
                 });//end sheet1



           })->export('xls');

           return "Download";

        

        

                        

    }

    public function healthForm_getAll()
    {
      if (Input::get('date'))
            $productionDate = Carbon::parse(Input::get('date'),'Asia/Manila');
        else
            $productionDate = Carbon::now('GMT+8');

        
            $allLogs = DB::table('symptoms_user')->where('symptoms_user.created_at','>=',$productionDate->startOfDay()->format('Y-m-d H:i:s'))->
                        where('symptoms_user.created_at','<=',$productionDate->endOfDay()->format('Y-m-d H:i:s'))->
            leftJoin('users','symptoms_user.user_id','=','users.id')->
            leftJoin('team','team.user_id','=','users.id')->
            leftJoin('campaign','team.campaign_id','=','campaign.id')->
            select('symptoms_user.id as declareID','users.id as userID','users.accesscode',  'users.lastname','users.firstname','campaign.name as program','symptoms_user.question_id','symptoms_user.answer','symptoms_user.created_at')->orderBy('users.lastname','ASC')->get();

            $allSymptoms = DB::table('symptoms_declare')->where('symptoms_declare.created_at','>=',$productionDate->startOfDay()->format('Y-m-d H:i:s'))->
                                where('symptoms_declare.created_at','<=',$productionDate->endOfDay()->format('Y-m-d H:i:s'))->
                                leftJoin('symptoms','symptoms_declare.symptoms_id','=','symptoms.id')->
                                where('symptoms_declare.isDiagnosis','=',null)->
                                select('symptoms_declare.user_answerID as declareID','symptoms.name')->get();
            
            $allDiagnose = DB::table('symptoms_declare')->where('symptoms_declare.created_at','>=',$productionDate->startOfDay()->format('Y-m-d H:i:s'))->
                                where('symptoms_declare.created_at','<=',$productionDate->endOfDay()->format('Y-m-d H:i:s'))->
                                leftJoin('symptoms','symptoms_declare.symptoms_id','=','symptoms.id')->
                                where('symptoms_declare.isDiagnosis','=',1)->
                                select('symptoms_declare.user_answerID as declareID','symptoms.name')->get();


            $allSubmissions = collect($allLogs)->groupBy('userID');
            $healthForms = new Collection;
            //return $allSubmissions;
            foreach ($allSubmissions as $s) {
              if($s[0]->answer == 1)
              {
                $symp = collect($allSymptoms)->where('declareID',$s[0]->declareID)->pluck('name');

              }else $symp=[];

              if($s[0]->answer == 1)
              {
                $symp = collect($allSymptoms)->where('declareID',$s[0]->declareID)->pluck('name');

              }else $symp=[];

              if($s[4]->answer == 1)
              {
                $diag = collect($allDiagnose)->where('declareID',$s[4]->declareID)->pluck('name');

              }else $diag=[];


              $healthForms->push(['firstname'=>$s[0]->firstname,'lastname'=>$s[0]->lastname,'userID'=>$s[0]->userID,'accesscode'=>$s[0]->accesscode,
                                  'program'=>$s[0]->program,'answer1'=>$s[0]->answer,'answer2'=>$s[1]->answer, 'symptoms'=>$symp,'diagnosis'=>$diag, 'created_at'=>$s[0]->created_at]);
            }

            //return $healthForms;
            

        

        

        return response()->json(['data'=>$healthForms, 'count'=>count($healthForms),'symptoms'=>$allSymptoms]);//count($allLogs)
    }

    public function healthForm_process(Request $request)
    {
      $symptoms = $request->sel_symptoms;
      $diagnosis = $request->sel_diagnosis;
      $declarations = $request->declarations;
      $now = Carbon::now('GMT+8');

      $employee = $this->user;
      $ihCamp = ImmediateHead_Campaign::find(Team::where('user_id',$this->user->id)->first()->immediateHead_Campaigns_id);
      $program = Campaign::find($ihCamp->campaign_id);
      $tl = User::where('employeeNumber',ImmediateHead::find($ihCamp->immediateHead_id)->employeeNumber)->first();
      $notYet = true;

      foreach ($declarations as $d) {
        $symptomsUser = new Symptoms_User;
        $symptomsUser->user_id = $this->user->id;
        $symptomsUser->question_id = $d['question'];
        $symptomsUser->answer = $d['answer'];
        $symptomsUser->created_at = $now->format('Y-m-d H:i:s');
        $symptomsUser->save();

        //save symptoms declaration
        if(($d['question'] == '1' && $d['answer'] == '1') || ($d['question']=='2' && $d['answer']=='1'))
        {
          if($d['question']=='1')
          {
            foreach ($symptoms as $s) {
              $sd = new Symptoms_Declaration;
              $sd->user_id = $this->user->id;
              $sd->symptoms_id = $s;
              $sd->user_answerID = $symptomsUser->id;
              $sd->created_at = $now->format('Y-m-d H:i:s');
              $sd->save();
            }

          }
            

          // NOW, EMAIL THE TL CONCERNED

          if($notYet)
          {
            Mail::send('emails.hdf', ['now'=>$now->format('M d, l'), 'tl' => $tl,'program'=>$program, 'employee'=>$employee], function ($m) use ($tl, $employee) 
             {
                $m->from('EMS@openaccessbpo.net', 'EMS | OAMPI Employee Management System');
                $m->to($tl->email, $tl->lastname)->subject('Health Declaration Alert');     

                /* -------------- log updates made --------------------- */
                     $file = fopen('public/build/rewards.txt', 'a') or die("Unable to open logs");
                        fwrite($file, "-------------------\n Email sent to ". $tl->email."\n");
                        fwrite($file, "\n New HDF alert:  ". $employee->firstname." ".$employee->lastname."\n");
                        fclose($file);                      
            

            }); //end mail

            $notYet=false;


          }
                               
             

            
            
        }

        if($d['question'] == '5')
        {
          //last question
          foreach ($diagnosis as $d) {
            if ($d != '0')
            {
              $dg = new Symptoms_Declaration;
              $dg->user_id = $this->user->id;
              $dg->symptoms_id = $d;
              $dg->user_answerID = $symptomsUser->id;
              $dg->isDiagnosis = 1;
              $dg->created_at = $now->format('Y-m-d H:i:s');
              $dg->save();

            }
          }

        }




        

      }



      return response()->json(['symptoms'=>$symptoms,'diagnosis'=>$diagnosis,'declarations'=>$declarations, 'success'=>1]);

    }

    public function healthForm_report()
    {

      DB::connection()->disableQueryLog();
      $user = $this->user;
      $from = Team::where('user_id',$user->id)->first()->campaign_id;
      $canView = [10,71,16];

      if (!in_array($from, $canView)) return view('access-denied');
      
      if (Input::get('date'))
          $start = Carbon::parse(Input::get('date'),'Asia/Manila');
      else
          $start = Carbon::now('GMT+8');


      $correct = Carbon::now('GMT+8'); //->timezoneName();

      if($this->user->id !== 564 ) {
      $file = fopen('public/build/rewards.txt', 'a') or die("Unable to open logs");
      fwrite($file, "-------------------\n HealthForms track on " . $correct->format('M d h:i A'). " by [". $this->user->id."] ".$this->user->lastname."\n");
      fclose($file);
      }

      return view('people.healthForms_report',compact('user','start'));
    }

    public function home()
    {

      $coll = new Collection;
      $user = $this->user; 
      DB::connection()->disableQueryLog();
      $forms = new Collection;
      $groupedTasks=null;
      $trackerNDY=null;
      $pendingTask=null;
      $hasPendingTask=null;
      $pendingTaskBreak=null;
      $hasPendingTaskBreak=null;
      $fromNDY=null;


      if ( is_null($user->nickname) ) $greeting = $user->firstname;
      else $greeting = $user->nickname;

      $leadershipcheck = ImmediateHead::where('employeeNumber', $this->user->employeeNumber)->first();
      $canDo = UserType::find($this->user->userType_id)->roles->where('label','QUERY_REPORTS');
      if (count($canDo)> 0 ) $reportsTeam=1; else $reportsTeam=0;

      /* ---------------------------------------------------------*/
      /* --- WE NOW CHECK FOR CAMPAIGN WIDGETS from FormBuilder --*/

      $prg = Campaign::where('name',"Postmates")->first()->id; //for widget
      $prg2 = Campaign::where('name',"Guideline")->first()->id; 
      $prg3 = Campaign::where('name',"NDY")->first()->id; 

      if (empty($leadershipcheck)) {
          $myCampaign = collect($this->user->campaign->first()->id);
          ($myCampaign->contains($prg3)) ? $fromNDY=false : $fromNDY=false;
          ($myCampaign->contains($prg2)) ? $fromGuideline=true : $fromGuideline=false; 
          ($myCampaign->contains($prg)) ? $fromPostmate=true : $fromPostmate=false;


         
      } 
      else { 
            $myCampaign = $leadershipcheck->myCampaigns->groupBy('campaign_id')->keys();
            

            ($myCampaign->contains($prg3)) ? $fromNDY=false : $fromNDY=false; 
            ($myCampaign->contains($prg2)) ? $fromGuideline=true : $fromGuideline=false; 
            ($myCampaign->contains($prg)) ? $fromPostmate=true : $fromPostmate=false; 


      }

      //******************* TASK TRACKER : NDY *************************
      /*if ($fromNDY)
      {
        $trackerNDY = DB::table('task')->where('task.campaign_id',$prg3)->
                    join('taskgroup','task.groupID','=','taskgroup.id')->
                    join('task_campaign','task_campaign.campaign_id','=','task.campaign_id')->
                    select('taskgroup.id as groupID', 'taskgroup.name as taskgroup','task.name as task','task.id','task_campaign.name as tracker','task_campaign.activated')->
                    orderBy('task.id','ASC')->get();
        $groupedTasks = collect($trackerNDY)->groupBy('taskgroup');

        $pending = Task_User::where('user_id',$this->user->id)->where('timeEnd',null)->orderBy('id','DESC')->get();
        

        if ( count($pending) >= 1 ){

          $pendingTask = $pending->first();
          $pendingBreak = Taskbreak_User::where('task_userID',$pendingTask->id)->where('timeEnd',null)->orderBy('id','DESC')->get();
          $hasPendingTask = 1;
        }else {
          $pendingBreak=[];
          $pendingTask = null; $hasPendingTask=0;
        }


        if ( count($pendingBreak) >= 1 ){
          $pendingTaskBreak = $pendingBreak->first();
          $hasPendingTaskBreak = 1;
        }else {*/
          $pendingTaskBreak = null; $hasPendingTaskBreak=0;
        //}

        //return $groupedTasks;
      //}

      //******************* TASK TRACKER : NDY *************************



      //if (!empty($forms) && !$reportsTeam){
      
      if ($fromPostmate || $fromGuideline) 
      {   

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
        }

        

        $widget = $forms->first(); //return response()->json($widget[0]);
        $groupedForm = collect($widget)->groupBy('widgetTitle');
        $groupedSelects = collect($widget)->groupBy('selectGroup');
        $campform = $widget[0]->formID;//['formID'];

      }else
      {

        if ($reportsTeam){

          //$prg = Campaign::where('name',"Postmates")->first();
          $d = DB::table('campaign_forms')->where('campaign_id','=',$prg)->
                join('formBuilder','campaign_forms.formBuilder_id','=','formBuilder.id')->
                join('campaign','campaign_forms.campaign_id','=','campaign.id')->
                leftJoin('formBuilder_items','formBuilder_items.formBuilder_id','=','campaign_forms.formBuilder_id')->
                leftJoin('formBuilder_elements','formBuilder_items.formBuilder_elemID','=', 'formBuilder_elements.id')->//get();
                leftJoin('formBuilderSubtypes','formBuilder_items.formBuilder_subTypeID','=','formBuilderSubtypes.id')->
                leftJoin('formBuilderElem_values','formBuilderElem_values.formBuilder_itemID','=','formBuilder_items.id')->
                select('campaign.name as program','formBuilder.title as widgetTitle','campaign_forms.enabled','formBuilder_elements.type as type', 
                  'formBuilderSubtypes.name as subType','formBuilder_items.label as label','formBuilder_items.name as itemName','formBuilder_items.placeholder','formBuilder_items.required','formBuilder_items.formOrder','formBuilder_items.id as itemID','formBuilder.id as formID', 'formBuilderElem_values.value','formBuilderElem_values.label as optionLabel', 'formBuilderElem_values.formBuilder_itemID as selectGroup','formBuilderElem_values.selected', 'formBuilder_items.className')->get(); 

          $d2 = DB::table('campaign_forms')->where('campaign_id','=',$prg2)->
                join('formBuilder','campaign_forms.formBuilder_id','=','formBuilder.id')->
                join('campaign','campaign_forms.campaign_id','=','campaign.id')->
                leftJoin('formBuilder_items','formBuilder_items.formBuilder_id','=','campaign_forms.formBuilder_id')->
                leftJoin('formBuilder_elements','formBuilder_items.formBuilder_elemID','=', 'formBuilder_elements.id')->//get();
                leftJoin('formBuilderSubtypes','formBuilder_items.formBuilder_subTypeID','=','formBuilderSubtypes.id')->
                leftJoin('formBuilderElem_values','formBuilderElem_values.formBuilder_itemID','=','formBuilder_items.id')->
                select('campaign.name as program','formBuilder.title as widgetTitle','campaign_forms.enabled','formBuilder_elements.type as type', 
                  'formBuilderSubtypes.name as subType','formBuilder_items.label as label','formBuilder_items.name as itemName','formBuilder_items.placeholder','formBuilder_items.required','formBuilder_items.formOrder','formBuilder_items.id as itemID','formBuilder.id as formID', 'formBuilderElem_values.value','formBuilderElem_values.label as optionLabel', 'formBuilderElem_values.formBuilder_itemID as selectGroup','formBuilderElem_values.selected', 'formBuilder_items.className')->get(); 


                //return (['d'=>$d,'d2'=>$d2]);

                if (!empty($d)) $forms->push(collect($d)->groupBy('widgetTitle'));
                if (!empty($d2)) $forms->push(collect($d2)->groupBy('widgetTitle'));

                //return $forms;

                $widget = collect($forms);
                $groupedForm = $forms; //$widget->groupBy('widgetTitle');
                $groupedSelects = $widget->groupBy('selectGroup');
                $campform = '1'; //$forms->first()[0]->formID;//['formID'];

        }else {
          $groupedForm = null; $groupedSelects=null; $campform=null;
        }
        
      }

      
      //return $groupedForm;
      

      $forApprovals = $this->getDashboardNotifs();// $this->getApprovalNotifs();USER TRAIT

      /************* PERFORMANCE EVALS ***************/
      /*$userEvals = new Collection; $performance = new Collection;
      $userEvals1 = $user->evaluations->sortBy('created_at')->filter(function($eval){
                                return $eval->overAllScore > 0;

                    }); //->pluck('created_at','evalSetting_id','overAllScore'); 

      
      $byDateEvals =  $userEvals1->groupBy(function($pool) {
                                    return Carbon::parse($pool->created_at)->format('Y-m-d');
                                });*/

      $userEvals = new Collection;

      /*foreach ($byDateEvals as $evs) {
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

      };*/

      /************* for SURVEY WIDGET ***************/
      $doneS = DB::table('survey_user')->where('user_id',$this->user->id)->where('isDone',1)->get();
      (count($doneS) > 0) ? $doneSurvey=1 : $doneSurvey=0;


      /************* for TIMEKEEPING WIDGET ***************/

      //check if user has already logged in

      $startToday = Carbon::now('GMT+8');
      // check mo muna kung may biodata na for today:

      
      if (( $startToday->format('H:i') > Carbon::now('GMT+8')->startOfDay()->format('H:i')) && ($startToday->format('H:i') <= Carbon::parse(date('Y-m-d').' 8:00:00','Asia/Manila')->format('H:i')) ) //for those with 11pm-8am shift
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


      $alreadyLoggedIN=false;
      //if (count($loggedIn) > 0) $alreadyLoggedIN=true; else $alreadyLoggedIN=false;//return response()->json(['alreadyLoggedIN'=>$alreadyLoggedIN]);

      //-- IDOLS ----
        $idols = new Collection;
        $top3 = new Collection;
        //ANDALES, ANICETO, AQUINO, DAWIS, DICEN, OCAMPO, PICANA, SIBAL, SIMON, SUAREZ, YLMAZ, ZUNZU
        $idolIDs = [ 1585, 40, 2277, 3175, 2328, 531,3112, 2708, 3027, 685, 3260, 2723];
        $top3s = [1686,674,829];

        foreach ($top3s as $i) {
          $u = DB::table('users')->where('users.id',$i)->join('positions','users.position_id','=','positions.id')->
                    join('team','team.user_id','=','users.id')->join('campaign','team.campaign_id','=','campaign.id')->
                    leftJoin('campaign_logos','campaign_logos.campaign_id','=','team.campaign_id')->
                    select('users.id', 'users.firstname','users.nickname','users.lastname','positions.name as jobTitle','campaign.name as program','campaign_logos.filename')->get();
          $top3->push($u[0]);
        }

        foreach ($idolIDs as $i) {
          $u = DB::table('users')->where('users.id',$i)->join('positions','users.position_id','=','positions.id')->
                    join('team','team.user_id','=','users.id')->join('campaign','team.campaign_id','=','campaign.id')->
                    leftJoin('campaign_logos','campaign_logos.campaign_id','=','team.campaign_id')->
                    select('users.id', 'users.firstname','users.nickname','users.lastname','positions.name as jobTitle','campaign.name as program','campaign_logos.filename')->get();
          $idols->push($u[0]);
        }

        $ct1=0; $songs = ["There's No Easy Way","Rolling in the Deep","Be My Lady"]; 
        $titles=[" to our very first <br/><strong>Open Access Idol Winner!</strong> "," to our <strong>Open Access Idol <br/>2nd Placer</strong>"," to our <strong>Open Access Idol <br/>3rd Placer</strong>"]; 
        $pics=["monochrome-393.jpg","monochrome-362.jpg","monochrome-344.jpg"];

      //-- end idols

      //-- SHOUT OUT --
        $bago = Carbon::today()->subWeeks(1);
        $annivs = Carbon::today()->subWeeks(1)->subYear(1);
        $annivs2 = Carbon::today()->subYear(1);
        $anniv10s = Carbon::today()->subYear(10)->startOfYear();
        $anniv10e = Carbon::today()->subYear(10)->endOfYear();
        $anniv5s = Carbon::today()->subYear(5)->startOfYear();
        $anniv5e = Carbon::today()->subYear(5)->endOfYear();
        
        $newHires = DB::table('users')->where('dateHired','>=',$bago->format('Y-m-d'))->where('status_id','!=',16)->where('status_id','!=',6)->where('status_id','!=',7)->where('status_id','!=',8)->where('status_id','!=',9)->where('status_id','!=',7)->leftJoin('positions','users.position_id','=', 'positions.id')->leftJoin('team','team.user_id','=','users.id')->leftJoin('campaign','campaign.id','=','team.campaign_id')->leftJoin('campaign_logos','campaign.id','=','campaign_logos.campaign_id')->select('users.id','users.hascoverphoto',  'users.firstname','users.lastname','users.nickname','positions.name','campaign_logos.filename','team.campaign_id', 'users.dateHired')->orderBy('users.dateHired','DESC')->get();

        $firstYears = DB::table('users')->where('dateHired','>=',$annivs->format('Y-m-d H:i:s'))->where('dateHired','<=',$annivs2->format('Y-m-d H:i:s'))->where('status_id','!=',6)->where('status_id','!=',7)->where('status_id','!=',8)->where('status_id','!=',9)->leftJoin('positions','users.position_id','=', 'positions.id')->leftJoin('team','team.user_id','=','users.id')->leftJoin('campaign','campaign.id','=','team.campaign_id')->leftJoin('campaign_logos','campaign.id','=','campaign_logos.campaign_id')->select('users.id','users.hascoverphoto',  'users.firstname','users.lastname','users.nickname','positions.name','campaign_logos.filename','team.campaign_id', 'users.dateHired')->orderBy('users.dateHired','DESC')->get();

        $tenYears = DB::table('users')->where('dateHired','>=',$anniv10s->format('Y-m-d H:i:s'))->where('dateHired','<=',$anniv10e->format('Y-m-d H:i:s'))->where('status_id','!=',6)->where('status_id','!=',7)->where('status_id','!=',8)->where('status_id','!=',9)->leftJoin('positions','users.position_id','=', 'positions.id')->leftJoin('team','team.user_id','=','users.id')->leftJoin('campaign','campaign.id','=','team.campaign_id')->leftJoin('campaign_logos','campaign.id','=','campaign_logos.campaign_id')->select('users.id','users.hascoverphoto',  'users.firstname','users.lastname','users.nickname','positions.name','campaign_logos.filename','team.campaign_id', 'users.dateHired')->orderBy('users.dateHired','DESC')->get();

        $fiveYears = DB::table('users')->where('dateHired','>=',$anniv5s->format('Y-m-d H:i:s'))->where('dateHired','<=',$anniv5e->format('Y-m-d H:i:s'))->where('status_id','!=',6)->where('status_id','!=',7)->where('status_id','!=',8)->where('status_id','!=',9)->leftJoin('positions','users.position_id','=', 'positions.id')->leftJoin('team','team.user_id','=','users.id')->leftJoin('campaign','campaign.id','=','team.campaign_id')->leftJoin('campaign_logos','campaign.id','=','campaign_logos.campaign_id')->select('users.id','users.hascoverphoto',  'users.firstname','users.lastname','users.nickname','positions.name','campaign_logos.filename','team.campaign_id', 'users.dateHired')->orderBy('team.campaign_id','ASC')->get();

      //-- end SHOUTOUT

               
      $evalTypes = EvalType::all();
      //$evalSetting = EvalSetting::all()->first();
      // --------- temporarily we set it for Semi annual of July to Dec 
      $evalSetting = EvalSetting::find(2);

      
      $currentPeriod = Carbon::create((date("Y")-1), $evalSetting->startMonth, $evalSetting->startDate,0,0,0, 'Asia/Manila');
      $endPeriod = Carbon::create((date("Y")-1), $evalSetting->endMonth, $evalSetting->endDate,0,0,0, 'Asia/Manila');

      $doneEval = new Collection;
      $pendingEval = new Collection;

      
      $leadershipcheck = ImmediateHead::where('employeeNumber', $this->user->employeeNumber)->get();


      //******* show MEMO for test people only jill,paz,ems,joy,raf,jaja, lothar, inguengan
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
      //-- END MEMO

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
      

      

      //---------- new feature: log user activity as cctv BACKUP------
      $endHr = Carbon::now('GMT+8');
      $startHr = Carbon::now('GMT+8')->addHours(-4);
      $alreadyIn = User_CCTV::where('user_id',$this->user->id)->where('logType','1')->where('created_at','>=',$startHr->format('Y-m-d H:i:s'))->where('created_at','<=',$endHr->format('Y-m-d H:i:s'))->get();

      if (count($alreadyIn) > 0){}
      else 
      {
        $cctv = new User_CCTV;
        $cctv->user_id = $this->user->id;
        $cctv->logType = 1;
        $cctv->created_at = $startToday->format('Y-m-d H:i:s');
        $cctv->save();

      }

      

      //---------- end cctv backup ------



      //return $pass = bcrypt('ben2020'); //$2y$10$IQqrVA8oK9uedQYK/8Z4Ae9ttvkGr/rGrwrQ6JVKdobMBt/5Mj4Ja

      // --------- if user has no subordinates -----------
      if (( ($this->user->userType->name == "HR admin") && count($leadershipcheck)==0 ) || $this->user->userType_id==4)
      { //  AGENT or ADMIN pero agent level
          $employee = User::find($this->user->id);
          $meLeader = $employee->supervisor->first(); 
          //return redirect()->route('user.show',['id'=>$this->user->id]);

          return view('dashboard-agent', compact('startToday', 'campform','pendingTask','hasPendingTask','pendingTaskBreak','hasPendingTaskBreak', 'groupedTasks','trackerNDY', 'fromNDY', 'fromGuideline','prg', 'prg2', 'fromPostmate','idols','top3','doneSurvey', 'firstYears','tenYears','fiveYears', 'newHires',  'currentPeriod','endPeriod', 'evalTypes', 'evalSetting', 'user','greeting','groupedForm','groupedSelects','reportsTeam','memo','notedMemo','alreadyLoggedIN', 'siteTour','notedTour','ct1','songs','pics','titles'));
          
      // ----------- endif user has no subordinates -----------

      } else {

          //-- Initialize Approvals Dashlet

         //return $groupedForm[0];
          return view('dashboard', compact('startToday', 'campform', 'pendingTask','hasPendingTask','pendingTaskBreak','hasPendingTaskBreak', 'groupedTasks','trackerNDY', 'fromNDY','fromGuideline','prg', 'prg2', 'fromPostmate', 'idols','top3', 'doneSurvey', 'firstYears','tenYears','fiveYears', 'newHires', 'forApprovals', 'currentPeriod','endPeriod', 'evalTypes', 'evalSetting', 'user','greeting','groupedForm','groupedSelects','reportsTeam','memo','notedMemo','alreadyLoggedIN', 'siteTour','notedTour','ct1','songs','pics','titles'));
         


      } 

      
        
    }

    
    public function index()
    {
      DB::connection()->disableQueryLog();
      //check mo muna kung may health form na for today
      $startToday = Carbon::now('GMT+8');
      $todayS = Carbon::now('GMT+8')->startOfDay();
      $todayE = Carbon::now('GMT+8')->endOfDay();
      $hasForm = DB::table('symptoms_user')->where('user_id',$this->user->id)->where('created_at','>=',$todayS->format('Y-m-d H:i:s'))
                  ->where('created_at','<=',$todayE->format('Y-m-d H:i:s'))->get();

      if(count($hasForm) == 0) return redirect()->route('page.health');


      $coll = new Collection;
      $user = $this->user; 
      
      $forms = new Collection;
      $groupedTasks=null;
      $trackerNDY=null;
      $pendingTask=null;
      $hasPendingTask=null;
      $pendingTaskBreak=null;
      $hasPendingTaskBreak=null;
      $fromNDY=null;


      if ( is_null($user->nickname) ) $greeting = $user->firstname;
      else $greeting = $user->nickname;

      $leadershipcheck = ImmediateHead::where('employeeNumber', $this->user->employeeNumber)->first();
      $canDo = UserType::find($this->user->userType_id)->roles->where('label','QUERY_REPORTS');
      if (count($canDo)> 0 ) $reportsTeam=1; else $reportsTeam=0;

      /* ---------------------------------------------------------*/
      /* --- WE NOW CHECK FOR CAMPAIGN WIDGETS from FormBuilder --*/

      $prg = Campaign::where('name',"Postmates")->first()->id; //for widget
      $prg2 = Campaign::where('name',"Guideline")->first()->id; 
      $prg3 = Campaign::where('name',"NDY")->first()->id; 

      if (empty($leadershipcheck)) {
          $myCampaign = collect($this->user->campaign->first()->id);
          ($myCampaign->contains($prg3)) ? $fromNDY=false : $fromNDY=false;
          ($myCampaign->contains($prg2)) ? $fromGuideline=true : $fromGuideline=false; 
          ($myCampaign->contains($prg)) ? $fromPostmate=true : $fromPostmate=false;


         
      } 
      else { 
            $myCampaign = $leadershipcheck->myCampaigns->groupBy('campaign_id')->keys();
            

            ($myCampaign->contains($prg3)) ? $fromNDY=false : $fromNDY=false; 
            ($myCampaign->contains($prg2)) ? $fromGuideline=true : $fromGuideline=false; 
            ($myCampaign->contains($prg)) ? $fromPostmate=true : $fromPostmate=false; 


      }

      //******************* TASK TRACKER : NDY *************************
      /*if ($fromNDY)
      {
        $trackerNDY = DB::table('task')->where('task.campaign_id',$prg3)->
                    join('taskgroup','task.groupID','=','taskgroup.id')->
                    join('task_campaign','task_campaign.campaign_id','=','task.campaign_id')->
                    select('taskgroup.id as groupID', 'taskgroup.name as taskgroup','task.name as task','task.id','task_campaign.name as tracker','task_campaign.activated')->
                    orderBy('task.id','ASC')->get();
        $groupedTasks = collect($trackerNDY)->groupBy('taskgroup');

        $pending = Task_User::where('user_id',$this->user->id)->where('timeEnd',null)->orderBy('id','DESC')->get();
        

        if ( count($pending) >= 1 ){

          $pendingTask = $pending->first();
          $pendingBreak = Taskbreak_User::where('task_userID',$pendingTask->id)->where('timeEnd',null)->orderBy('id','DESC')->get();
          $hasPendingTask = 1;
        }else {
          $pendingBreak=[];
          $pendingTask = null; $hasPendingTask=0;
        }


        if ( count($pendingBreak) >= 1 ){
          $pendingTaskBreak = $pendingBreak->first();
          $hasPendingTaskBreak = 1;
        }else {*/
          $pendingTaskBreak = null; $hasPendingTaskBreak=0;
        //}

        //return $groupedTasks;
      //}

      //******************* TASK TRACKER : NDY *************************



      //if (!empty($forms) && !$reportsTeam){
      
      if ($fromPostmate || $fromGuideline) 
      {   

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
        }

        

        $widget = $forms->first(); //return response()->json($widget[0]);
        $groupedForm = collect($widget)->groupBy('widgetTitle');
        $groupedSelects = collect($widget)->groupBy('selectGroup');
        $campform = $widget[0]->formID;//['formID'];

      }else
      {

        if ($reportsTeam){

          //$prg = Campaign::where('name',"Postmates")->first();
          $d = DB::table('campaign_forms')->where('campaign_id','=',$prg)->
                join('formBuilder','campaign_forms.formBuilder_id','=','formBuilder.id')->
                join('campaign','campaign_forms.campaign_id','=','campaign.id')->
                leftJoin('formBuilder_items','formBuilder_items.formBuilder_id','=','campaign_forms.formBuilder_id')->
                leftJoin('formBuilder_elements','formBuilder_items.formBuilder_elemID','=', 'formBuilder_elements.id')->//get();
                leftJoin('formBuilderSubtypes','formBuilder_items.formBuilder_subTypeID','=','formBuilderSubtypes.id')->
                leftJoin('formBuilderElem_values','formBuilderElem_values.formBuilder_itemID','=','formBuilder_items.id')->
                select('campaign.name as program','formBuilder.title as widgetTitle','campaign_forms.enabled','formBuilder_elements.type as type', 
                  'formBuilderSubtypes.name as subType','formBuilder_items.label as label','formBuilder_items.name as itemName','formBuilder_items.placeholder','formBuilder_items.required','formBuilder_items.formOrder','formBuilder_items.id as itemID','formBuilder.id as formID', 'formBuilderElem_values.value','formBuilderElem_values.label as optionLabel', 'formBuilderElem_values.formBuilder_itemID as selectGroup','formBuilderElem_values.selected', 'formBuilder_items.className')->get(); 

          $d2 = DB::table('campaign_forms')->where('campaign_id','=',$prg2)->
                join('formBuilder','campaign_forms.formBuilder_id','=','formBuilder.id')->
                join('campaign','campaign_forms.campaign_id','=','campaign.id')->
                leftJoin('formBuilder_items','formBuilder_items.formBuilder_id','=','campaign_forms.formBuilder_id')->
                leftJoin('formBuilder_elements','formBuilder_items.formBuilder_elemID','=', 'formBuilder_elements.id')->//get();
                leftJoin('formBuilderSubtypes','formBuilder_items.formBuilder_subTypeID','=','formBuilderSubtypes.id')->
                leftJoin('formBuilderElem_values','formBuilderElem_values.formBuilder_itemID','=','formBuilder_items.id')->
                select('campaign.name as program','formBuilder.title as widgetTitle','campaign_forms.enabled','formBuilder_elements.type as type', 
                  'formBuilderSubtypes.name as subType','formBuilder_items.label as label','formBuilder_items.name as itemName','formBuilder_items.placeholder','formBuilder_items.required','formBuilder_items.formOrder','formBuilder_items.id as itemID','formBuilder.id as formID', 'formBuilderElem_values.value','formBuilderElem_values.label as optionLabel', 'formBuilderElem_values.formBuilder_itemID as selectGroup','formBuilderElem_values.selected', 'formBuilder_items.className')->get(); 


                //return (['d'=>$d,'d2'=>$d2]);

                if (!empty($d)) $forms->push(collect($d)->groupBy('widgetTitle'));
                if (!empty($d2)) $forms->push(collect($d2)->groupBy('widgetTitle'));

                //return $forms;

                $widget = collect($forms);
                $groupedForm = $forms; //$widget->groupBy('widgetTitle');
                $groupedSelects = $widget->groupBy('selectGroup');
                $campform = '1'; //$forms->first()[0]->formID;//['formID'];

        }else {
          $groupedForm = null; $groupedSelects=null; $campform=null;
        }
        
      }

      
      //return $groupedForm;
      

      $forApprovals = $this->getDashboardNotifs();// $this->getApprovalNotifs();USER TRAIT

      /************* PERFORMANCE EVALS ***************/
      /*$userEvals = new Collection; $performance = new Collection;
      $userEvals1 = $user->evaluations->sortBy('created_at')->filter(function($eval){
                                return $eval->overAllScore > 0;

                    }); //->pluck('created_at','evalSetting_id','overAllScore'); 

      
      $byDateEvals =  $userEvals1->groupBy(function($pool) {
                                    return Carbon::parse($pool->created_at)->format('Y-m-d');
                                });*/

      $userEvals = new Collection;

      /*foreach ($byDateEvals as $evs) {
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

      };*/

      /************* for SURVEY WIDGET ***************/
      $doneS = DB::table('survey_user')->where('user_id',$this->user->id)->where('isDone',1)->get();
      (count($doneS) > 0) ? $doneSurvey=1 : $doneSurvey=0;


      /************* for TIMEKEEPING WIDGET ***************/

      //check if user has already logged in

      
      // check mo muna kung may biodata na for today:

      
      if (( $startToday->format('H:i') > Carbon::now('GMT+8')->startOfDay()->format('H:i')) && ($startToday->format('H:i') <= Carbon::parse(date('Y-m-d').' 8:00:00','Asia/Manila')->format('H:i')) ) //for those with 11pm-8am shift
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


      $alreadyLoggedIN=false;
      //if (count($loggedIn) > 0) $alreadyLoggedIN=true; else $alreadyLoggedIN=false;//return response()->json(['alreadyLoggedIN'=>$alreadyLoggedIN]);

      //-- IDOLS ----
        $idols = new Collection;
        $top3 = new Collection;
        //ANDALES, ANICETO, AQUINO, DAWIS, DICEN, OCAMPO, PICANA, SIBAL, SIMON, SUAREZ, YLMAZ, ZUNZU
        $idolIDs = [ 1585, 40, 2277, 3175, 2328, 531,3112, 2708, 3027, 685, 3260, 2723];
        $top3s = [1686,674,829];

        foreach ($top3s as $i) {
          $u = DB::table('users')->where('users.id',$i)->join('positions','users.position_id','=','positions.id')->
                    join('team','team.user_id','=','users.id')->join('campaign','team.campaign_id','=','campaign.id')->
                    leftJoin('campaign_logos','campaign_logos.campaign_id','=','team.campaign_id')->
                    select('users.id', 'users.firstname','users.nickname','users.lastname','positions.name as jobTitle','campaign.name as program','campaign_logos.filename')->get();
          $top3->push($u[0]);
        }

        foreach ($idolIDs as $i) {
          $u = DB::table('users')->where('users.id',$i)->join('positions','users.position_id','=','positions.id')->
                    join('team','team.user_id','=','users.id')->join('campaign','team.campaign_id','=','campaign.id')->
                    leftJoin('campaign_logos','campaign_logos.campaign_id','=','team.campaign_id')->
                    select('users.id', 'users.firstname','users.nickname','users.lastname','positions.name as jobTitle','campaign.name as program','campaign_logos.filename')->get();
          $idols->push($u[0]);
        }

        $ct1=0; $songs = ["There's No Easy Way","Rolling in the Deep","Be My Lady"]; 
        $titles=[" to our very first <br/><strong>Open Access Idol Winner!</strong> "," to our <strong>Open Access Idol <br/>2nd Placer</strong>"," to our <strong>Open Access Idol <br/>3rd Placer</strong>"]; 
        $pics=["monochrome-393.jpg","monochrome-362.jpg","monochrome-344.jpg"];

      //-- end idols

      //-- SHOUT OUT --
        $bago = Carbon::today()->subWeeks(1);
        $annivs = Carbon::today()->subWeeks(1)->subYear(1);
        $annivs2 = Carbon::today()->subYear(1);
        $anniv10s = Carbon::today()->subYear(10)->startOfYear();
        $anniv10e = Carbon::today()->subYear(10)->endOfYear();
        $anniv5s = Carbon::today()->subYear(5)->startOfYear();
        $anniv5e = Carbon::today()->subYear(5)->endOfYear();
        
        $newHires = DB::table('users')->where('dateHired','>=',$bago->format('Y-m-d'))->where('status_id','!=',16)->where('status_id','!=',6)->where('status_id','!=',7)->where('status_id','!=',8)->where('status_id','!=',9)->where('status_id','!=',7)->leftJoin('positions','users.position_id','=', 'positions.id')->leftJoin('team','team.user_id','=','users.id')->leftJoin('campaign','campaign.id','=','team.campaign_id')->leftJoin('campaign_logos','campaign.id','=','campaign_logos.campaign_id')->select('users.id','users.hascoverphoto',  'users.firstname','users.lastname','users.nickname','positions.name','campaign_logos.filename','team.campaign_id', 'users.dateHired')->orderBy('users.dateHired','DESC')->get();

        $firstYears = DB::table('users')->where('dateHired','>=',$annivs->format('Y-m-d H:i:s'))->where('dateHired','<=',$annivs2->format('Y-m-d H:i:s'))->where('status_id','!=',6)->where('status_id','!=',7)->where('status_id','!=',8)->where('status_id','!=',9)->leftJoin('positions','users.position_id','=', 'positions.id')->leftJoin('team','team.user_id','=','users.id')->leftJoin('campaign','campaign.id','=','team.campaign_id')->leftJoin('campaign_logos','campaign.id','=','campaign_logos.campaign_id')->select('users.id','users.hascoverphoto',  'users.firstname','users.lastname','users.nickname','positions.name','campaign_logos.filename','team.campaign_id', 'users.dateHired')->orderBy('users.dateHired','DESC')->get();

        $tenYears = DB::table('users')->where('dateHired','>=',$anniv10s->format('Y-m-d H:i:s'))->where('dateHired','<=',$anniv10e->format('Y-m-d H:i:s'))->where('status_id','!=',6)->where('status_id','!=',7)->where('status_id','!=',8)->where('status_id','!=',9)->leftJoin('positions','users.position_id','=', 'positions.id')->leftJoin('team','team.user_id','=','users.id')->leftJoin('campaign','campaign.id','=','team.campaign_id')->leftJoin('campaign_logos','campaign.id','=','campaign_logos.campaign_id')->select('users.id','users.hascoverphoto',  'users.firstname','users.lastname','users.nickname','positions.name','campaign_logos.filename','team.campaign_id', 'users.dateHired')->orderBy('users.dateHired','DESC')->get();

        $fiveYears = DB::table('users')->where('dateHired','>=',$anniv5s->format('Y-m-d H:i:s'))->where('dateHired','<=',$anniv5e->format('Y-m-d H:i:s'))->where('status_id','!=',6)->where('status_id','!=',7)->where('status_id','!=',8)->where('status_id','!=',9)->leftJoin('positions','users.position_id','=', 'positions.id')->leftJoin('team','team.user_id','=','users.id')->leftJoin('campaign','campaign.id','=','team.campaign_id')->leftJoin('campaign_logos','campaign.id','=','campaign_logos.campaign_id')->select('users.id','users.hascoverphoto',  'users.firstname','users.lastname','users.nickname','positions.name','campaign_logos.filename','team.campaign_id', 'users.dateHired')->orderBy('team.campaign_id','ASC')->get();

      //-- end SHOUTOUT

               
      $evalTypes = EvalType::all();
      //$evalSetting = EvalSetting::all()->first();
      // --------- temporarily we set it for Semi annual of July to Dec 
      $evalSetting = EvalSetting::find(2);

      
      $currentPeriod = Carbon::create((date("Y")-1), $evalSetting->startMonth, $evalSetting->startDate,0,0,0, 'Asia/Manila');
      $endPeriod = Carbon::create((date("Y")-1), $evalSetting->endMonth, $evalSetting->endDate,0,0,0, 'Asia/Manila');

      $doneEval = new Collection;
      $pendingEval = new Collection;

      
      $leadershipcheck = ImmediateHead::where('employeeNumber', $this->user->employeeNumber)->get();


      //******* show MEMO for test people only jill,paz,ems,joy,raf,jaja, lothar, inguengan
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
      //-- END MEMO

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
      

      

      //---------- new feature: log user activity as cctv BACKUP------
      $endHr = Carbon::now('GMT+8');
      $startHr = Carbon::now('GMT+8')->addHours(-4);
      $alreadyIn = User_CCTV::where('user_id',$this->user->id)->where('logType','1')->where('created_at','>=',$startHr->format('Y-m-d H:i:s'))->where('created_at','<=',$endHr->format('Y-m-d H:i:s'))->get();

      if (count($alreadyIn) > 0){}
      else 
      {
        $cctv = new User_CCTV;
        $cctv->user_id = $this->user->id;
        $cctv->logType = 1;
        $cctv->created_at = $startToday->format('Y-m-d H:i:s');
        $cctv->save();

      }

      

      //---------- end cctv backup ------



      //return $pass = bcrypt('ben2020'); //$2y$10$IQqrVA8oK9uedQYK/8Z4Ae9ttvkGr/rGrwrQ6JVKdobMBt/5Mj4Ja

      // --------- if user has no subordinates -----------
      if (( ($this->user->userType->name == "HR admin") && count($leadershipcheck)==0 ) || $this->user->userType_id==4)
      { //  AGENT or ADMIN pero agent level
          $employee = User::find($this->user->id);
          $meLeader = $employee->supervisor->first(); 
          //return redirect()->route('user.show',['id'=>$this->user->id]);

          return view('dashboard-agent', compact('startToday', 'campform','pendingTask','hasPendingTask','pendingTaskBreak','hasPendingTaskBreak', 'groupedTasks','trackerNDY', 'fromNDY', 'fromGuideline','prg', 'prg2', 'fromPostmate','idols','top3','doneSurvey', 'firstYears','tenYears','fiveYears', 'newHires',  'currentPeriod','endPeriod', 'evalTypes', 'evalSetting', 'user','greeting','groupedForm','groupedSelects','reportsTeam','memo','notedMemo','alreadyLoggedIN', 'siteTour','notedTour','ct1','songs','pics','titles'));
          
      // ----------- endif user has no subordinates -----------

      } else {

          //-- Initialize Approvals Dashlet

         //return $groupedForm[0];
          return view('dashboard', compact('startToday', 'campform', 'pendingTask','hasPendingTask','pendingTaskBreak','hasPendingTaskBreak', 'groupedTasks','trackerNDY', 'fromNDY','fromGuideline','prg', 'prg2', 'fromPostmate', 'idols','top3', 'doneSurvey', 'firstYears','tenYears','fiveYears', 'newHires', 'forApprovals', 'currentPeriod','endPeriod', 'evalTypes', 'evalSetting', 'user','greeting','groupedForm','groupedSelects','reportsTeam','memo','notedMemo','alreadyLoggedIN', 'siteTour','notedTour','ct1','songs','pics','titles'));
         


      } 

      
        
    }

    public function logout() 
    {
        //---------- new feature: log user activity as cctv BACKUP------
        $cctv = new User_CCTV;
        $cctv->user_id = $this->user->id;
        $cctv->logType = 2;
        $cctv->created_at = Carbon::now('GMT+8')->format('Y-m-d H:i:s');
        $cctv->save();

        //---------- end cctv backup ------
        auth()->logout();
        return redirect('/');
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
                        fwrite($file, "-------------------\n Played SDE_Monochrome by [". $this->user->id."] ".$this->user->lastname." on ". $correct->format('M d h:i A').  "\n");
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

         case 'P':{
                    if($this->user->id !== 564 ) {
                      $user = User::find(Input::get('id'));
                      $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
                        fwrite($file, "-------------------\n Playbook by [". $this->user->id."] ".$this->user->lastname." on ". $correct->format('M d h:i A').  "\n");
                        fclose($file);
                    } 

        }break;


        case 'C':{
                    if($this->user->id !== 564 ) {
                      //$user = User::find(Input::get('id'));
                      $viewed = Input::get('viewed');
                      $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
                        fwrite($file, "-------------------\n ChartClick by [". $this->user->id."] ".$this->user->lastname." for ".$viewed. " on ". $correct->format('M d h:i A').  "\n");
                        fclose($file);
                        return response()->json(['done'=>true,'viewed'=>$viewed]);
                    } 

        }break;

        case 'T':{
                    if($this->user->id !== 564 ) {
                      //$user = User::find(Input::get('id'));
                      $viewed = Input::get('viewed');
                      $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
                        fwrite($file, "-------------------\n TreeClick by [". $this->user->id."] ".$this->user->lastname." for ".$viewed. " on ". $correct->format('M d h:i A').  "\n");
                        fclose($file);
                        return response()->json(['done'=>true,'viewed'=>$viewed]);
                    } 

        }break;

         case 'VG':{
                    if($this->user->id !== 564 ) {
                      $user = User::find(Input::get('id'));
                      $vg = Input::get('vg');
                      $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
                        fwrite($file, "-------------------\n VidGall [".$vg."] by [". $this->user->id."] ".$this->user->lastname." on ". $correct->format('M d h:i A').  "\n");
                        fclose($file);
                    } 

        }break;

        
        
      }
      return response()->json(['success'=>"1"]);

           
    }



    public function videogallery()
    {
      if($this->user->id !== 564 ) {
        $correct = Carbon::now('GMT+8'); 
        $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
          fwrite($file, "-------------------\n VidGallery by [". $this->user->id."] ".$this->user->lastname." on ". $correct->format('M d h:i A').  "\n");
          fclose($file);
      } 

      return view('videogallery');
    }
}
