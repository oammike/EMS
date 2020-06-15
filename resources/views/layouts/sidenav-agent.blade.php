<?php $u = OAMPI_Eval\User::find(Auth::user()->id); 
            
            $lengthOfservice = \Carbon\Carbon::parse($u->dateHired,"Asia/Manila")->diffInMonths();
            $leave1 = \Carbon\Carbon::parse('first day of January '. date('Y'),"Asia/Manila")->format('Y-m-d');
            $leave2 = \Carbon\Carbon::parse('last day of December '.date('Y'),"Asia/Manila")->format('Y-m-d');
            $currentVLbalance ="N/A";
            $updatedVL = false;
            $currentSLbalance ="N/A";
            $updatedSL = false;


            //if ($lengthOfservice > 6) //do this if only 6mos++
            //{
              $today= date('m');//today();
              $avail = $u->vlCredits;
              $avail2 = $u->slCredits;

              $vlEarnings = DB::table('user_vlearnings')->where('user_vlearnings.user_id',$u->id)->
                               join('vlupdate','user_vlearnings.vlupdate_id','=', 'vlupdate.id')->
                               select('vlupdate.credits','vlupdate.period')->where('vlupdate.period','>',\Carbon\Carbon::parse(date('Y').'-01-01','Asia/Manila')->format('Y-m-d'))->get();
              $totalVLearned = collect($vlEarnings)->sum('credits');

              $slEarnings = DB::table('user_slearnings')->where('user_slearnings.user_id',$u->id)->
                               join('slupdate','user_slearnings.slupdate_id','=', 'slupdate.id')->
                               select('slupdate.credits','slupdate.period')->where('slupdate.period','>',\Carbon\Carbon::parse(date('Y').'-01-01','Asia/Manila')->format('Y-m-d'))->get();
              $totalSLearned = collect($slEarnings)->sum('credits');

              $approvedVLs = OAMPI_Eval\User_VL::where('user_id',$u->id)->where('isApproved',1)->where('leaveStart','>=',$leave1)->where('leaveEnd','<=',$leave2)->get();
              $approvedSLs = OAMPI_Eval\User_SL::where('user_id',$u->id)->where('isApproved',1)->where('leaveStart','>=',$leave1)->where('leaveEnd','<=',$leave2)->get();

              $vtoVL = OAMPI_Eval\User_VTO::where('user_id',$u->id)->where('isApproved',1)->where('productionDate','>=',$leave1)->where('productionDate','<=',$leave2)->where('deductFrom','VL')->get();
              $totalVTO_vl = number_format(collect($vtoVL)->sum('totalHours') * 0.125,2);

              $vtoSL = OAMPI_Eval\User_VTO::where('user_id',$u->id)->where('isApproved',1)->where('productionDate','>=',$leave1)->where('productionDate','<=',$leave2)->where('deductFrom','SL')->get();
              $vtoSL2 = OAMPI_Eval\User_VTO::where('user_id',$u->id)->where('isApproved',1)->where('productionDate','>=',$leave1)->where('productionDate','<=',$leave2)->where('deductFrom','AdvSL')->get();
              $totalVTO_sl1 = number_format(collect($vtoSL)->sum('totalHours') * 0.125,2);
              $totalVTO_sl2 = number_format(collect($vtoSL2)->sum('totalHours') * 0.125,2);
              $totalVTO_sl = $totalVTO_sl1 + $totalVTO_sl2;

                /************ for VL ************/
                if (count($avail)>0){
                  $vls = $u->vlCredits->sortByDesc('creditYear');

                  if($vls->contains('creditYear',date('Y')))
                  {
                    $updatedVL=true;
                    $currentVLbalance= ($vls->first()->beginBalance - $vls->first()->used) + $totalVLearned - $vls->first()->paid - $totalVTO_vl;
                  }
                  else{
                    $currentVLbalance = "N/A";
                    
                    /*if (count($approvedVLs)>0)
                    {
                      $bal = 0.0;
                      foreach ($approvedVLs as $key) {
                        $bal += $key->totalCredits;
                      }

                      $currentVLbalance = (0.84 * $today) - $bal;

                    }else{

                      $currentVLbalance = (0.84 * $today);
                    }*/

                  } 



                }else {
                  

                  /*if (count($approvedVLs)>0){
                    $bal = 0.0;
                    foreach ($approvedVLs as $key) {
                      $bal += $key->totalCredits;
                    }

                    $currentVLbalance = (0.84 * $today) - $bal;

                  }else{

                    $currentVLbalance = (0.84 * $today);
                  }*/
                  $currentVLbalance = "N/A";
                  
                }


                /************ for SL ************/
                if (count($avail2)>0)
                {
                  $sls = $u->slCredits->sortByDesc('creditYear');

                  if($sls->contains('creditYear',date('Y')))
                  {
                    $updatedSL=true;

                    //get advanced SLs
                    $adv = DB::table('user_advancedSL')->where('user_id',$u->id)->get();

                    $advancedSL = 0;
                    foreach ($adv as $a) {
                      $advancedSL += $a->total;
                    }

                     $currentSLbalance= (($sls->first()->beginBalance - $sls->first()->used) + $totalSLearned - $sls->first()->paid)-$advancedSL - $totalVTO_sl;
                    
                  }
                  else{
                    
                   /* if (count($approvedSLs)>0)
                    {
                      $bal = 0.0;
                      foreach ($approvedSLs as $key) {
                        $bal += $key->totalCredits;
                      }

                      $currentSLbalance = (0.84 * $today) - $bal;

                    }else{

                      $currentSLbalance = (0.84 * $today);
                    }*/
                    $currentSLbalance="N/A";

                  }
                }else 
                {
                  $currentSLbalance="N/A";
                  /*if (count($approvedSLs)>0){
                    $bal = 0.0;
                    foreach ($approvedSLs as $key) {
                      $bal += $key->totalCredits;
                    }

                    $currentSLbalance = (0.84 * $today) - $bal;

                  }else{

                    $currentSLbalance = (0.84 * $today);
                  }*/
                  
                }

            //}
            
                //$avail = $this->user->availableVL;

            //check for updated credits:
            $updatedVLcredits = OAMPI_Eval\VLupdate::orderBy('period','DESC')->get();
            $updatedSLcredits = OAMPI_Eval\SLupdate::orderBy('period','DESC')->get();
            
                
            ?>
<aside class="main-sidebar">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">

      <!-- Sidebar user panel (optional) -->
      <div class="user-panel">
        <div class="pull-left image">
          <a href="{{action('UserController@show',Auth::user()->id)}}" class="user-image" >
           @if ( file_exists('public/img/employees/'.Auth::user()->id.'.jpg') )
               <img src="{{asset('public/img/employees/'.Auth::user()->id.'.jpg')}}" class="user-image" alt="User Image" width="50">
              @else
                <img src="{{asset('public/img/useravatar.png')}}" class="user-image" alt="User Image" width="40">

                @endif
              </a>

         
        </div>
        <div class="pull-left info">
         @if (is_null(Auth::user()->nickname))
          <small><strong>{{ Auth::user()->firstname }} {{ Auth::user()->lastname }}</strong></small><br/>

          @else
          <small><strong>{{ Auth::user()->nickname }} {{ Auth::user()->lastname }}</strong></small><br/>
          @endif
          <!-- Status -->
          
            
              <small class="text-success"><i class="fa fa-circle text-success"></i> Online</small> 
            
          
        </div>
      </div>


      <!-- ******************** LEAVE CREDIT COUNTER ***************-->
      @if(count($updatedVLcredits) > 0)
      <div class="row" data-step="1" data-intro="Hi @if(empty(Auth::user()->nickname)){{Auth::user()->firstname}} @else {{Auth::user()->nickname}}@endif!<br/>Welcome to Open Access EMS. <br/><br/>These are your updated leave credits as of <span class='text-danger'><strong>{{date('M d, Y',strtotime($updatedVLcredits->first()->period))}} </strong></span>.<br/><br/><strong>VL: </strong>{{$currentVLbalance}}<br/><strong> SL: </strong>{{$currentSLbalance}}<br/> <br/><strong class='text-primary'><i class='fa fa-info-circle'></i>Note: </strong><span style='font-size:0.7em'>Regular employees who have  completed six  (6)  months  shall be  entitled  to  five   (5)  days  of  sick  leave and five (5) days of vacation leave. Employee  will  earn  an  additional  <strong class='text-danger'>0.42</strong>  leave credits after the <strong class='text-danger'> 5th and 20th </strong>day of the month worked.</span><br/><br/>To file for leave requests..." data-position="right">

      @else

      <div class="row" data-step="1" data-intro="Hi @if(empty(Auth::user()->nickname)){{Auth::user()->firstname}} @else {{Auth::user()->nickname}}@endif!<br/>Welcome to Open Access EMS. <br/><br/>These are your updated leave credits as of <span class='text-danger'><strong>NOV. 20, 2018 </strong></span>.<br/><br/><strong>VL: </strong>{{$currentVLbalance}}<br/><strong> SL: </strong>{{$currentSLbalance}}<br/> <br/><strong class='text-primary'><i class='fa fa-info-circle'></i>Note: </strong><span style='font-size:0.7em'>Regular employees who have  completed six  (6)  months  shall be  entitled  to  five   (5)  days  of  sick  leave and five (5) days of vacation leave. Employee  will  earn  an  additional  <strong class='text-danger'>0.42</strong>  leave credits after the <strong class='text-danger'> 5th and 20th </strong>day of the month worked.</span><br/><br/>To file for leave requests..." data-position="right">

      @endif
        <div class="col-lg-1 col-sm-12"></div>
        <div class="col-lg-4 col-sm-12 text-center">
          <!-- <a id="askVL" class="btn btn-xs" title="Ask Finance for your leave credits to-date" onclick="javascript:alertAsk();"> <i class="fa fa-exclamation-triangle text-yellow"></i></a> -->
          
          <a href="{{action('UserVLController@showCredits',Auth::user()->id)}}"><span class="label"><i class="fa fa-plane"></i></span><span class="label label-primary">
             {{$currentVLbalance}} </span></a></div>

            
        <div class="col-lg-4 col-sm-12 text-center">
          <!-- <a id="askSL" class="btn btn-xs" title="Ask Finance for your leave credits to-date"  onclick="javascript:alertAsk();"> <i class="fa fa-exclamation-triangle text-yellow"></i></a> -->
         <a href="{{action('UserVLController@showCredits',Auth::user()->id)}}"><span class="label"> <i class="fa fa-stethoscope"></i></span><span class="label label-danger">{{$currentSLbalance}}</span></a></div>
          <div class="col-lg-2 col-sm-12"></div>
      </div>
      <div class="row">
        <div class="col-lg-1 col-sm-12"></div>
        <div class="col-lg-4 col-sm-12 text-center"><span class="label"> VL credit(s) 
          @if (!$updatedVL)
          <a href="{{action('UserVLController@showCredits',Auth::user()->id)}}" title="Request Immediate head or Finance to update your leave credits"> <i class="fa fa-exclamation-triangle text-yellow"></i></a></span>

          @endif
          </span>
        </div>
        <div class="col-lg-4 col-sm-12 text-center"><span class="label"> SL credit(s) 
          @if (!$updatedSL)
          <a title="Request Immediate head or Finance to update your leave credits"> <i class="fa fa-exclamation-triangle text-yellow"></i></a></span>

          @endif
          </span>
          <!-- <a title="Request HR/Finance to update your leave credits"> <i class="fa fa-exclamation-triangle text-yellow"></i></a></span> -->
        </div>
        <div class="col-lg-2 col-sm-12"></div>
      </div><p>&nbsp;</p>
      <!-- ******************** LEAVE CREDIT COUNTER ***************-->


            
     

      <!-- Sidebar Menu -->
      <ul class="sidebar-menu">
        <li class="header">MY TOOLS</li>
        <!-- Optionally, you can add icons to the links -->
        <!-- <li class="@if (Request::is('page')) active @endif"><a href="{{ action('HomeController@index') }}"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li> -->
        <li class="@if (Request::is('home')) active @endif"><a href="{{ action('HomeController@home') }}"><i class="fa fa-2x fa-dashboard"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span>Dashboard</span></a></li>

        
      <!--  <li class="treeview @if ( Request::is('employeeEngagement*') ) active @endif">
          <a href="#"><i class="fa fa-2x fa-trophy"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span>Contests</span><i class="fa fa-angle-left pull-right"></i></a>
          <ul class="treeview-menu">
            <li @if (Request::is('employeeEngagement*')) class="active" @endif style="padding-left:20px"><a href="{{action('EngagementController@show',1)}}"><i class="fa fa-moon-o"></i>Frightful Tales</a> </li>
          </ul>
        </li>
 -->
        <?php //9 == Davao
              $floor = DB::table('team')->where('team.user_id', Auth::user()->id)->first()->floor_id; ?>
        
        


        <li class="treeview @if (Request::is('employeeEngagement*')) active @endif">
          <a href="#" class="text-yellow"><i class="fa fa-2x fa-smile-o"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span>Activities </span>
            <span class="label label-success" style="font-size:0.5em; margin-left:5px; margin-bottom: -5px"><strong> New! </strong></span>
            <i class="fa fa-angle-left pull-right"></i></a>
          <ul class="treeview-menu">
            <li @if (Request::is('userRewards/about')) class="active" @endif style="padding-left:20px"><a href="{{action('EngagementController@show',14)}}"><i class="fa fa-th-large"></i> Wall [Week 7] </a></li><li @if (Request::is('userRewards/about')) class="active" @endif style="padding-left:20px"><a href="{{action('EngagementController@show',13)}}"><i class="fa fa-th-large"></i> Wall [Week 6] </a></li>
            <li @if (Request::is('userRewards/about')) class="active" @endif style="padding-left:20px"><a href="{{action('EngagementController@wall',12)}}"><i class="fa fa-th-large"></i> Wall [Week 5] </a></li>
            <li @if (Request::is('userRewards/about')) class="active" @endif style="padding-left:20px"><a target="_blank" href="{{action('EngagementController@wall',11)}}"><i class="fa fa-th-large"></i> Wall [Week 4] </a> </li>
            <li @if (Request::is('userRewards/about')) class="active" @endif style="padding-left:20px"><a target="_blank" href="{{action('EngagementController@wall',10)}}"><i class="fa fa-th-large"></i> Wall [Week 3] </a> </li>
            <li @if (Request::is('userRewards/about')) class="active" @endif style="padding-left:20px"><a  target="_blank"href="{{action('EngagementController@wall',9)}}"><i class="fa fa-th-large"></i> Wall [Week 2] </a> </li>
            <li @if (Request::is('userRewards/about')) class="active" @endif style="padding-left:20px"><a  target="_blank"href="{{action('EngagementController@wall',5)}}"><i class="fa fa-th-large"></i> Wall [Week 1] </a> </li>
            <!-- <li @if (Request::is('userRewards/about')) class="active" @endif style="padding-left:20px"><a href="{{action('EngagementController@show',6)}}"><i class="fa fa-child"></i> Zumba </a> </li>
            <li @if (Request::is('userRewards/about')) class="active" @endif style="padding-left:20px"><a href="{{action('EngagementController@show',7)}}"><i class="fa fa-child"></i> Aero Kickboxing </a> </li>
            <li @if (Request::is('userRewards/about')) class="active" @endif style="padding-left:20px"><a href="{{action('EngagementController@show',8)}}"><i class="fa fa-child"></i> Yoga </a> </li> -->
            

          </ul>
        </li>


        <li class="treeview @if ( Request::is('gallery') ) active @endif">
          <a href="#"><i class="fa fa-2x fa-picture-o"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span>Gallery</span><i class="fa fa-angle-left pull-right"></i></a>
          <ul class="treeview-menu">

            <li @if (Request::is('videogallery')) class="active" @endif style="padding-left:20px"><a href="{{action('HomeController@videogallery')}}"><i class="fa fa-video-camera"></i>All Videos</a> </li>

            <li @if (Request::is('gallery')) class="active" @endif style="padding-left:20px"><a href="{{ action('HomeController@gallery',['a'=>21]) }}"><i class="fa fa-beer"></i> 2019 Monochrome Party </a> </li>

             <li @if (Request::is('gallery')) class="active" @endif style="padding-left:20px"><a href="{{ action('HomeController@gallery',['a'=>24]) }}"><i class="fa fa-beer"></i> 2019 Monochrome Party <br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(Official cam) </a> </li>

              <li @if (Request::is('gallery')) class="active" @endif style="padding-left:20px"><a href="{{ action('HomeController@gallery',['a'=>22]) }}"><i class="fa fa-beer"></i> 2019 Monochrome <br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Photo Booth1 </a> </li>

              <li @if (Request::is('gallery')) class="active" @endif style="padding-left:20px"><a href="{{ action('HomeController@gallery',['a'=>23]) }}"><i class="fa fa-beer"></i> 2019 Monochrome <br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Photo Booth2 </a> </li>

           

            
             <li @if (Request::is('gallery')) class="active" @endif style="padding-left:20px"><a href="{{ action('HomeController@gallery',['a'=>1]) }}"><i class="fa fa-beer"></i> Back to the 90s </a> </li>

             <li @if (Request::is('gallery')) class="active" @endif style="padding-left:20px"><a href="{{ action('HomeController@gallery',['a'=>10]) }}"><i class="fa fa-camera"></i> BTS: We Speak Your<br/> Language <!--  <span class="label label-success" style="font-size:0.5em; margin-left:5px; margin-bottom: -5px"><strong> New! </strong></span> --></a> </li>

             <li @if (Request::is('gallery')) class="active" @endif style="padding-left:20px"><a id="phys" href="{{ action('HomeController@gallery',['a'=>6]) }}"><i class="fa fa-flag-checkered"></i> Catriona Homecoming </a> </li>

             <li @if (Request::is('gallery')) class="active" @endif style="padding-left:20px"><a href="{{ action('HomeController@gallery',['a'=>9]) }}"><i class="fa fa-beer"></i> Cinco De Mayo </a> </li>

             

             <li @if (Request::is('usergallery')) class="active" @endif style="padding-left:20px"><a href="{{action('GalleryController@show',1)}}"><i class="fa fa-picture-o"></i>CS WEEK 2019</a> </li>

            <li @if (Request::is('gallery')) class="active" @endif style="padding-left:20px"><a href="{{ action('HomeController@gallery',['a'=>19]) }}"><i class="fa fa-picture-o"></i>CS WEEK 2019 <br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Photobooth </a> </li>


             <li @if (Request::is('gallery')) class="active" @endif style="padding-left:20px"><a href="{{ action('HomeController@gallery',['a'=>17]) }}"><i class="fa fa-beer"></i> Open Access Davao<br/> 4th Year Anniversary </a> </li>

             <li @if (Request::is('gallery')) class="active" @endif style="padding-left:20px"><a href="{{ action('HomeController@gallery',['a'=>16]) }}"><i class="fa fa-picture-o"></i> Davao Health &amp; Wellness</a> </li>

             <li @if (Request::is('gallery')) class="active" @endif style="padding-left:20px"><a href="{{ action('HomeController@gallery',['a'=>13]) }}"><i class="fa fa-picture-o"></i> G2 Office Launching </a> </li>

             <li @if (Request::is('gallery')) class="active" @endif style="padding-left:20px"><a href="{{ action('HomeController@gallery',['a'=>15]) }}"><i class="fa fa-picture-o"></i> G2 Office Photobooth  </a> </li>

             <li @if (Request::is('gallery')) class="active" @endif style="padding-left:20px"><a href="{{ action('HomeController@gallery',['a'=>14]) }}"><i class="fa fa-beer"></i> G2 Office Launching <br/> - After Party </a> </li>

              <li @if (Request::is('gallery')) class="active" @endif style="padding-left:20px"><a href="{{ action('HomeController@gallery',['a'=>7]) }}"><i class="fa fa-beer"></i> Happy Hour</a> </li>

              <li @if (Request::is('gallery')) class="active" @endif style="padding-left:20px"><a href="{{ action('HomeController@gallery',['a'=>11]) }}"><i class="fa fa-child"></i> Health &amp; Wellness  <!-- <span class="label label-success" style="font-size:0.5em; margin-left:5px; margin-bottom: -5px"><strong> New! </strong></span> --></a> </li>

              <li @if (Request::is('gallery')) class="active" @endif style="padding-left:20px"><a id="phys" href="{{ action('HomeController@gallery',['a'=>5]) }}"><i class="fa fa-child"></i> Let's Get Physical </a> </li>

              <li @if (Request::is('gallery')) class="active" @endif style="padding-left:20px"><a href="{{ action('HomeController@gallery',['a'=>12]) }}"><i class="fa fa-child"></i> Pride March 2019  </a> </li>

              <li @if (Request::is('gallery')) class="active" @endif style="padding-left:20px"><a id="cam1" href="{{ action('HomeController@gallery',['a'=>3]) }}"><i class="fa fa-picture-o"></i> Official 2018 YEP [cam1] </a> </li>

              <li @if (Request::is('gallery')) class="active" @endif style="padding-left:20px"><a id="cam2" href="{{ action('HomeController@gallery',['a'=>4]) }}"><i class="fa fa-picture-o"></i> Official 2018 YEP [cam2] </a> </li>

               <li @if (Request::is('gallery')) class="active" @endif style="padding-left:20px"><a href="{{ action('HomeController@gallery',['a'=>20]) }}"><i class="fa fa-beer"></i> Oktoberfest 2019 </a> </li>

              <li @if (Request::is('gallery')) class="active" @endif style="padding-left:20px"><a id="photobooth" href="{{ action('HomeController@gallery',['a'=>2]) }}"><i class="fa fa-camera"></i> Photo Booth </a> </li>

              <li @if (Request::is('gallery')) class="active" @endif style="padding-left:20px"><a id="pose360" target="_blank" href="https://photos.google.com/share/AF1QipOz0s8djIAbsbczBsaQhmh_27gdAvTQTF_qndqKRMA3yUCqMZP4Uyw67TcjBYcr-w?key=OS11TTYtdUhKbnk5RjBlbjhlWmdmRHBJRmF3d1FR"><i class="fa fa-openid"></i> Pose 360&deg;  </a> </li>

              <li @if (Request::is('gallery')) class="active" @endif style="padding-left:20px"><a href="{{ action('HomeController@gallery',['a'=>8]) }}"><i class="fa fa-grav"></i> Wear Your Pajama To Work <!-- <span class="label label-success" style="font-size:0.5em; margin-left:5px; margin-bottom: -5px"><strong> New! </strong></span> --></a> </li>

              <li @if (Request::is('gallery')) class="active" @endif style="padding-left:20px"><a href="{{ action('HomeController@gallery') }}"><i class="fa fa-picture-o"></i> Past Events </a></li>
           
          </ul>
        </li>


         

        <li><a href="{{action('HomeController@healthForm')}}" ><i class="fa fa-2x fa-medkit"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  <span>Health Form</span></a></li>
        
        <li @if (Request::is('user/'.Auth::user()->id)) class="active" @endif><a href="{{action('UserController@show',Auth::user()->id)}}" > <i class="fa fa-2x fa-address-card-o"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span>My Profile</span></a></li>

        <li  data-step="3" data-intro="Or head over to your <br/><span style='font-weight:bold' class='text-danger'>'DTR Sheet'</span> and click on the push-pin icons to file a DTRP for that specific production date.<br/> <img src='public/img/dtr.jpg' /><br/><em> (assuming TL or WFM has already plotted your work schedule) </em><br/><br/><strong class='text-orange'><i class='fa fa-exclamation-triangle'></i> Reminder:</strong> If you're from Operations, coordinate with your immediate head and/or Workforce Management for leave application process." data-position='right' @if ( Request::is('user_dtr*') ) class="active" @endif ><a  @if ( Request::is('user_dtr*') ) class="active" @endif href="{{action('DTRController@show',Auth::user()->id)}}"><i class="fa fa-2x fa-calendar"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  My DTR</a></li>

         <li data-step="2" data-intro="..you may go to <br/><span style='font-weight:bold' class='text-danger'>'My Requests</span>' page and then select the type of request you want to submit. <br/><br/><strong>Note:</strong> Always include a brief reason when submitting requests." data-position='right'    @if ( Request::is('myRequests*') ) class="active" @endif ><a  @if ( Request::is('myRequests*') ) class="active" @endif href="{{action('UserController@myRequests',Auth::user()->id)}}"><i class="fa fa-2x fa-clipboard"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  My Requests</a></li>

         <li @if (Request::is('myTeam')) class="active" @endif><a href="{{action('UserController@myTeam')}}" ><i class="fa fa-2x fa-users"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  <span>My Team</span></a></li>


         <li @if (Request::is('myEvals')) class="active" @endif><a href="{{action('UserController@myEvals')}}" ><i class="fa fa-2x fa-file-o"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  <span>My Evals</span></a></li>

         @if($floor !== 9)
        <li class="treeview @if (Request::is('userRewards*') || Request::is('reward*') || Request::is('award*')) active @endif">
          <a href="#" ><i class="fa fa-2x fa-gift"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span>Rewards </span>
            
            <i class="fa fa-angle-left pull-right"></i></a>
          <ul class="treeview-menu">
            <li @if (Request::is('userRewards/about')) class="active" @endif style="padding-left:20px"><a href="{{action('UserController@rewards_about')}}"><i class="fa fa-info-circle"></i> About </a> </li>
            <li @if (Request::is('userRewards/barista')) class="active" @endif style="padding-left:20px"><a href="{{action('UserController@rewards_barista')}}"><i class="fa fa-coffee"></i> Barista </a> </li>
            <li @if (Request::is('rewards*')) class="active" @endif style="padding-left:20px"><a href="{{action('RewardsHomeController@rewards_catalog')}}"><i class="fa fa-book"></i> Catalog </a> </li>
            <li @if (Request::is('rewardTransactions')) class="active" @endif style="padding-left:20px"><a href="{{action('UserController@rewards_transactions')}}"><i class="fa fa-cart-arrow-down"></i> My Transactions </a> </li>
            <li @if (Request::is('userRewards')) class="active" @endif style="padding-left:20px"><a href="{{ action('UserController@rewards') }}"><i class="fa fa-exchange"></i> Transfer Points </a> </li>
             <li @if (Request::is('awardPoints')) class="active" @endif style="padding-left:20px"><a href="{{ action('UserController@rewards_award') }}"><i class="fa fa-trophy"></i> Award Points </a> </li>

          </ul>
        </li>
        @endif

          
        
     

        <li class="header">OAMPI SYSTEM</li>

        <li class="treeview @if ( Request::is('health*') ) active @endif">
            <a href="#"><i class="fa fa-user-md"></i>&nbsp;<span>Clinical Services</span><i class="fa fa-angle-left pull-right"></i></a>
            <ul class="treeview-menu">

              
              <li @if (Request::is('health*')) class="active" @endif style="padding-left:20px"><a href="{{ action('HomeController@healthForm')}}"><i class="fa fa-medkit"></i> Fill Out Health Form</a> </li>
              <li @if (Request::is('health*')) class="active" @endif style="padding-left:20px"><a href="{{ action('HomeController@healthForm_report')}}"><i class="fa fa-file-o"></i> View All Responses</a> </li>

              
              
             
            </ul>
        </li>

        <li><a href="http://172.17.0.2/coffeebreak/" target="_blank"><i class="fa fa-coffee" ></i> <img src="{{ asset('public/img/logo_coffeebreak.png')}}" width="100" /> <span></span></a></li>

        


          <li class="treeview @if (Request::is('user_dtr')) active @endif">
          <a href="#">
            <i class="fa fa-clock-o"></i> <span>Timekeeping</span>
            <i class="fa fa-angle-left pull-right"></i>
          </a>
          <ul class="treeview-menu">
         
            <li style="padding-left:20px" @if ( Request::is('user_*') ) class="active" @endif ><a  @if ( Request::is('user_*') ) class="active" @endif href="{{action('DTRController@show',Auth::user()->id)}}"><i class="fa fa-calendar"></i> My DTR</a></li>
           <!--  <li style="padding-left:20px"><a href="{{action('HomeController@module')}}"><i class="fa fa-bed"></i> Leaves</a></li>
            <li style="padding-left:20px"><a href="{{action('HomeController@module')}}"><i class="fa fa-tachometer"></i> OT / UT</a></li>
            <li style="padding-left:20px"><a href="{{action('HomeController@module')}}"><i class="fa fa-calendar-times-o"></i> File DTRP</a></li> -->
             <li style="padding-left:20px" @if ( Request::is('user_vl*') ) class="active" @endif ><a href="{{action('UserVLController@showCredits',Auth::user()->id)}}"><i class="fa fa-bar-chart"></i> Leave Credits</a></li>
          </ul>
        </li>
        



          <li @if (Request::is('oampi-resources')) class="active" @endif><a href="{{action('ResourceController@index')}}" ><i class="fa fa-book"></i> <span>Resources</span></a></li>

          <li class="treeview @if ( Request::is('survey*') ) active @endif">
            <a href="#"><i class="fa fa-question-circle"></i>&nbsp;<span>Surveys</span><i class="fa fa-angle-left pull-right"></i></a>
            <ul class="treeview-menu">

              
              <li @if (Request::is('survey*')) class="active" @endif style="padding-left:20px"><a href="{{ action('SurveyController@show',4)}}"><i class="fa fa-microphone"></i>Year End Party Artists <span class="label label-success" style="font-size:0.5em; margin-left:5px; margin-bottom: -5px"><strong> New! </strong></span></a> </li>
              <li @if (Request::is('survey*')) class="active" @endif style="padding-left:20px"><a href="{{ action('SurveyController@show',3)}}"><i class="fa fa-beer"></i> Year End Theme <span class="label label-success" style="font-size:0.5em; margin-left:5px; margin-bottom: -5px"><strong> New! </strong></span></a> </li>
              <li @if (Request::is('survey*')) class="active" @endif style="padding-left:20px"><a id="photobooth" href="{{action('SurveyController@show',1)}} "><i class="fa fa-question-circle"></i> <span>EES ( * 2019 Survey )</span> </a> </li>
              
             
            </ul>
          </li>

         
       
         
       <!-- 
        <li><a href="http://172.17.0.51/accessone/login" target="_blank"><i class="fa fa-file"></i> <span>HRIS</span></a></li>
        <li><a href="http://oampayroll.openaccessbpo.com/" target="_blank"><i class="fa fa-usd"></i> <span>Payroll</span></a></li> -->

        
      </ul>
      <!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
  </aside>