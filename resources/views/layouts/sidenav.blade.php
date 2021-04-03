<?php $u = OAMPI_Eval\User::find(Auth::user()->id);

            $lengthOfservice = \Carbon\Carbon::parse($u->dateHired,"Asia/Manila")->diffInMonths();

            //why?? because of the freaking server setting na di maayos ni IT
            $phY = \Carbon\Carbon::now('GMT+8')->format('Y');
            $leave1 = \Carbon\Carbon::parse('first day of January '. $phY,"Asia/Manila")->format('Y-m-d');
            $leave2 = \Carbon\Carbon::parse('last day of December '.$phY,"Asia/Manila")->format('Y-m-d');
            $currentVLbalance ="N/A";
            $updatedVL = false;
            $currentSLbalance ="N/A";
            $updatedSL = false;


            //if ($lengthOfservice > 6) //do this if only 6mos++
            //{
              $today= \Carbon\Carbon::now('GMT+8')->format('m'); //date('m');//today();
              $avail = $u->vlCredits;
              $avail2 = $u->slCredits;

              $vlEarnings = DB::table('user_vlearnings')->where('user_vlearnings.user_id',$u->id)->
                               join('vlupdate','user_vlearnings.vlupdate_id','=', 'vlupdate.id')->
                               select('vlupdate.credits','vlupdate.period')->where('vlupdate.period','>',\Carbon\Carbon::parse($phY.'-01-01','Asia/Manila')->format('Y-m-d'))->get();
              $totalVLearned = collect($vlEarnings)->sum('credits');

              $slEarnings = DB::table('user_slearnings')->where('user_slearnings.user_id',$u->id)->
                               join('slupdate','user_slearnings.slupdate_id','=', 'slupdate.id')->
                               select('slupdate.credits','slupdate.period')->where('slupdate.period','>',\Carbon\Carbon::parse($phY.'-01-01','Asia/Manila')->format('Y-m-d'))->get();
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
                    $currentVLbalance= number_format(($vls->first()->beginBalance - $vls->first()->used) + $totalVLearned - $vls->first()->paid - $totalVTO_vl,2);
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

                  if($sls->contains('creditYear',$phY))
                  {
                    $updatedSL=true;

                    //get advanced SLs
                    $adv = DB::table('user_advancedSL')->where('user_id',$u->id)->get();

                    $advancedSL = 0;
                    foreach ($adv as $a) {
                      if ( date('Y') == date('Y', strtotime($a->periodStart)) )
                        $advancedSL += $a->total;
                    }

                    $currentSLbalance= number_format((($sls->first()->beginBalance - $sls->first()->used) + $totalSLearned - $sls->first()->paid)-$advancedSL - $totalVTO_sl,2);

                  }
                  else{

                    /*if (count($approvedSLs)>0)
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

      <!-- search form (Optional) -->
      <!-- <form action="#" method="get" class="sidebar-form">
        <div class="input-group">
          <input type="text" name="q" class="form-control" placeholder="Search...">
              <span class="input-group-btn">
                <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
        </div>
      </form>  -->

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




      <!-- /.search form -->

      <!-- Sidebar Menu -->
      <ul class="sidebar-menu">
        <li class="header">MY TOOLS</li>

        <!-- Optionally, you can add icons to the links -->
        <li class="@if (Request::is('home')) active @endif"><a href="{{ action('HomeController@home') }}"><i class="fa fa-2x fa-dashboard"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span>Dashboard</span></a></li>


        <!-- <li class="treeview @if ( Request::is('employeeEngagement*') ) active @endif">
          <a href="#"><i class="fa fa-2x fa-trophy"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span>Contests</span><i class="fa fa-angle-left pull-right"></i></a>
          <ul class="treeview-menu">
            <li @if (Request::is('employeeEngagement*')) class="active" @endif style="padding-left:20px"><a href="{{action('EngagementController@show',1)}}"><i class="fa fa-moon-o"></i>Frightful Tales</a> </li>
          </ul>
        </li> -->

        <?php //9 == Davao
              $floor = DB::table('team')->where('team.user_id', Auth::user()->id)->first()->floor_id; ?>



        <!-- **** GALLERY ******** <span class="label label-success" style="font-size:0.5em; margin-left:5px; margin-bottom: -5px"><strong> New! </strong></span>-->

        <!-- ******** OPEN WALL ****** -->
        <?php $walls = OAMPI_Eval\Engagement::where('id','>',8)->where('created_at','>','2021-01-01 00:00:00')->orderBy('id','DESC')->get();
              $pastwalls = OAMPI_Eval\Engagement::where('id','>',8)->where('created_at','<','2021-01-01 00:00:00')->orderBy('id','DESC')->get(); ?>

        <li class="treeview @if (Request::is('employeeEngagement*')) active @endif">
         <a href="#"><i class="fa fa-2x fa-smile-o"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span>Activities </span>
            <!-- <span class="label label-success" style="font-size:0.5em; margin-left:5px; margin-bottom: -5px"><strong> New! </strong></span> -->
            <i class="fa fa-angle-left pull-right"></i></a>
          <ul class="treeview-menu">


            @foreach($walls as $w)

              @if($w->active)
              <li @if (Request::is('employeeEngagement*')) class="active" @endif style="padding-left:20px"><a href="{{action('EngagementController@show',$w->id)}}"><i class="fa fa-th-large"></i> {{$w->name}} </a></li>

              @else
              <li @if (Request::is('userRewards/about')) class="active" @endif style="padding-left:20px"><a  target="_blank"href="{{action('EngagementController@wall',$w->id)}}"><i class="fa fa-th-large"></i> {{$w->name}} </a> </li>

              @endif

            @endforeach
           


          </ul>

          <br/>

          <ul class="treeview-menu">
            <p class="text-center text-gray">Past Activities</p>

            @foreach($pastwalls as $w)

              @if($w->active)
              <li @if (Request::is('employeeEngagement*')) class="active" @endif style="padding-left:20px"><a href="{{action('EngagementController@show',$w->id)}}"><i class="fa fa-th-large"></i> {{$w->name}} </a></li>

              @else
              <li @if (Request::is('userRewards/about')) class="active" @endif style="padding-left:20px"><a  target="_blank"href="{{action('EngagementController@wall',$w->id)}}"><i class="fa fa-th-large"></i> {{$w->name}} </a> </li>

              @endif

            @endforeach
            <li @if (Request::is('userRewards/about')) class="active" @endif style="padding-left:20px"><a  target="_blank"href="{{action('EngagementController@wall',5)}}"><i class="fa fa-th-large"></i> WFH/Onsite Photo Wall </a> </li>


          </ul>


        </li>

        <li class="treeview @if ( Request::is('gallery') ) active @endif">
          <a href="#"><i class="fa fa-2x fa-picture-o"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span>Gallery</span><i class="fa fa-angle-left pull-right"></i></a>
          <ul class="treeview-menu">

            <li @if (Request::is('videogallery')) class="active" @endif style="padding-left:20px"><a href="{{action('HomeController@videogallery')}}"><i class="fa fa-video-camera"></i>All Videos</a> </li>

            <li @if (Request::is('gallery')) class="active" @endif style="padding-left:20px"><a href="{{ action('HomeController@gallery',['a'=>25]) }}"><i class="fa fa-picture-o"></i> Background Images </a> </li>

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

        <li class="treeview @if (Request::is('user_forms*') ) active @endif">
          <a href="#"><i class="fa fa-2x fa-file-o"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span>My BIR 2316 </span>

            <i class="fa fa-angle-left pull-right"></i></a>
          <ul class="treeview-menu">

            @if($u->has2316)
            
            
              @if($u->hasSigned2316)
              <li @if (Request::is('viewUserForm*')) class="active" @endif style="padding-left:20px"><a target="_blank" href="{{action('UserFormController@viewUserForm',['u'=>$u->id, 'f'=>'BIR2316', 's'=>1])}}"><i class="fa fa-download"></i> Download digital copy </a> </li>
              <li @if (Request::is('downloadUserForm*')) class="active" @endif style="padding-left:20px"><a href="#" style="text-decoration: line-through;"><i class="fa fa-check-square-o"></i> Upload Signed Copy </a> 

              @else
              <li @if (Request::is('downloadUserForm*')) class="active" @endif style="padding-left:20px"><a target="_blank" href="{{action('UserFormController@downloadUserForm',['u'=>$u->id, 'f'=>'BIR2316'])}}"><i class="fa fa-download"></i> Download digital copy </a> </li>
              <li @if (Request::is('viewUserForm*')) class="active" @endif style="padding-left:20px"><a href="{{action('UserFormController@create',['s'=>1,'t'=>'BIR2316'])}}"><i class="fa fa-upload"></i> Upload Signed PDF </a> </li>
              </li>

              @endif

            @else
            <li @if (Request::is('viewUserForm*')) class="active" @endif style="padding-left:20px"><a title="Not Yet Available" style="text-decoration: line-through;"  href="{{action('UserFormController@viewUserForm',['u'=>$u->id, 'f'=>'BIR2316'])}}"><i class="fa fa-download"></i> Download digital copy </a> </li>

            @endif

           
           

          </ul>
        </li>

        <li  data-step="3" data-intro="Or head over to your <br/><span style='font-weight:bold' class='text-danger'>'DTR Sheet'</span> and click on the push-pin icons to file a DTRP for that specific production date.<br/> <img src='public/img/dtr.jpg' /><br/> <em> (assuming TL or WFM has already plotted your work schedule) </em><br/><br/><strong class='text-orange'><i class='fa fa-exclamation-triangle'></i> Reminder:</strong> If you're from Operations, coordinate with your immediate head and/or Workforce Management for leave application process." data-position='right'  @if ( Request::is('user_dtr*') ) class="active" @endif ><a  @if ( Request::is('user_dtr*') ) class="active" @endif href="{{action('DTRController@show',Auth::user()->id)}}"><i class="fa fa-2x fa-calendar"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  My DTR</a></li>

        <li @if (Request::is("user/".Auth::user()->id)) class="active" @endif><a href="{{action('UserController@show',Auth::user()->id)}}" ><i class="fa fa-2x fa-address-card"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  <span>My Profile</span></a></li>

       <!--  <li  data-step="2" data-intro="..you may go to <br/><span style='font-weight:bold' class='text-danger'>'My Requests</span>' page and then select the type of request you want to submit. <br/><br/><strong>Note:</strong> Always include a brief reason when submitting requests." data-position='right'  @if ( Request::is('myRequests*') ) class="active" @endif ><a  @if ( Request::is('myRequests*') ) class="active" @endif href="{{action('UserController@myRequests',Auth::user()->id)}}"><i class="fa fa-2x fa-clipboard"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  My Requests</a>

       </li> -->


       @if( Auth::user()->isAleader )
        <li @if (Request::is('myTeam')) class="active" @endif><a href="{{action('UserController@myTeam')}}" ><i class="fa fa-2x fa-users"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  <span>My Team</span></a></li>
        @endif

        <li @if (Request::is('myEvals')) class="active" @endif><a href="{{action('UserController@myEvals')}}" ><i class="fa fa-2x fa-file-o"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  <span>My Evals</span></a></li>


      <li class="treeview @if (Request::is('performance*')) active @endif">
          <a href="#"><i class="fa fa-2x fa-thumbs-up"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span>Performance</span><i class="fa fa-angle-left pull-right"></i></a>
          <ul class="treeview-menu">
            <li @if (Request::is('performance*')) class="active" @endif style="padding-left:20px"><a href="{{action('NewPA_Form_Controller@index')}}"><i class="fa fa-clipboard"></i> View All Forms</a> </li>
            <li @if (Request::is('performance*')) class="active" @endif style="padding-left:20px"><a href="{{action('NewPA_Form_Controller@allForms')}}"><i class="fa fa-clipboard"></i> Admin View</a> </li>


          </ul>
      </li>

      <li class="treeview @if (Request::is('user/'.Auth::user()->id)) active @endif">
          <a href="#"><i class="fa fa-2x fa-clipboard"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span>Requests</span><i class="fa fa-angle-left pull-right"></i></a>
          <ul class="treeview-menu">
            <li @if (Request::is('gallery')) class="active" @endif style="padding-left:20px"><a href="{{action('UserController@myRequests',Auth::user()->id)}}"><i class="fa fa-calendar"></i> DTR Requests </a> </li>
            <li @if (Request::is('gallery')) class="active" @endif style="padding-left:20px"><a href="{{ action('ManpowerController@index') }}"><i class="fa fa-users"></i> Manpower Requests </a> </li>

          </ul>
        </li>


        <li class="treeview @if (Request::is('userRewards*') || Request::is('reward*') || Request::is('award*')) active @endif">
          <a href="#"><i class="fa fa-2x fa-gift"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span>Rewards </span>

            <i class="fa fa-angle-left pull-right"></i></a>
          <ul class="treeview-menu">
            <li @if (Request::is('userRewards/about')) class="active" @endif style="padding-left:20px"><a href="{{action('UserController@rewards_about')}}"><i class="fa fa-info-circle"></i> About </a> </li>
            <li @if (Request::is('userRewards/barista')) class="active" @endif style="padding-left:20px"><a href="{{action('UserController@rewards_barista')}}"><i class="fa fa-coffee"></i> Barista </a> </li>
            <li @if (Request::is('rewards*')) class="active" @endif style="padding-left:20px"><a href="{{action('RewardsHomeController@rewards_catalog')}}"><i class="fa fa-book"></i> Catalog </a> </li>
            <li @if (Request::is('rewardTransactions')) class="active" @endif style="padding-left:20px"><a href="{{action('UserController@rewards_transactions')}}"><i class="fa fa-cart-arrow-down"></i> My Transactions </a> </li>
            <li @if (Request::is('userRewards')) class="active" @endif style="padding-left:20px"><a href="{{ action('UserController@rewards') }}"><i class="fa fa-exchange"></i> Transfer Points </a> </li>
            <li @if (Request::is('awardPoints')) class="active" @endif style="padding-left:20px"><a href="{{ action('UserController@rewards_award') }}"><i class="fa fa-trophy"></i> Award Points </a> </li>

          @if(Auth::user()->id==491 || Auth::user()->id==83 || Auth::user()->id==564 || Auth::user()->id==222 )
            <hr/>
              <li style="padding-left:20px"><a href="{{ url('/manage-rewards-exclusives') }}"> Manage Exclusive Rewards </a> </li>
              <li style="padding-left:20px"><a href="{{ url('/manage-exclusive-claims') }}"> Manage Exclusive Claims </a> </li>

            <hr/>
              <li style="padding-left:20px"><a href="{{ action('RewardsHomeController@statistics') }}"> Order Stats </a> </li>
              <li style="padding-left:20px"><a href="{{ url('/manage-vouchers') }}"> Manage Vouchers </a> </li>
              <li style="padding-left:20px"><a href="{{ url('/manage-voucher-claims') }}"> Manage Voucher Claims </a> </li>
              <li style="padding-left:20px"><a href="{{ url('/manage-donations') }}"> Donation Categories </a> </li>
              <li style="padding-left:20px"><a href="{{ url('/manage-donation-intents') }}"> Manage Donation Intents </a> </li>
          @endif

          </ul>
        </li>


        <li class="treeview @if ( Request::is('survey*') ) active @endif">
          <a href="#"><i class="fa fa-2x fa-question-circle"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span>Surveys</span><i class="fa fa-angle-left pull-right"></i></a>
          <ul class="treeview-menu">


              <li @if (Request::is('survey*')) class="active" @endif style="padding-left:20px"><a href="{{action('SurveyController@report',6)}} "><i class="fa fa-question-circle"></i> <span>Pulse Check 2020</span> </a> </li>
              <li @if (Request::is('survey*')) class="active" @endif style="padding-left:20px"><a href="{{action('SurveyController@report',6)}} "><i class="fa fa-question-circle"></i> <span>Pulse Check 2020 Report</span> </a> </li>

             <!--  <li @if (Request::is('survey*')) class="active" @endif style="padding-left:20px"><a href="{{ action('SurveyController@show',5)}}"><i class="fa fa-file-o"></i>360° Survey </a> </li>
            <li @if (Request::is('survey*')) class="active" @endif style="padding-left:20px"><a href="{{ action('SurveyController@show',4)}}"><i class="fa fa-microphone"></i>2019 Year End Party Artists</a> </li>
            <li @if (Request::is('survey*')) class="active" @endif style="padding-left:20px"><a href="{{ action('SurveyController@show',3)}}"><i class="fa fa-beer"></i> 2019 Year End Party Theme </a></li> -->
            <!-- <li @if (Request::is('survey*')) class="active" @endif style="padding-left:20px"><a id="photobooth" href="{{action('SurveyController@report',1)}} "><i class="fa fa-question-circle"></i> <span> EES 2019 </span> </a> </li> -->


          </ul>
        </li>











        <li class="header">OAMPI SYSTEM</li>

        <li class="treeview @if ( Request::is('health*') ) active @endif">
            <a href="#"><i class="fa fa-user-md"></i>&nbsp;<span>Clinical Services</span><i class="fa fa-angle-left pull-right"></i></a>
            <ul class="treeview-menu">


              <li @if (Request::is('health*')) class="active" @endif style="padding-left:20px"><a href="{{ action('HomeController@healthForm')}}"><i class="fa fa-medkit"></i> Fill Out Health Form</a> </li>
              <li @if (Request::is('health*')) class="active" @endif style="padding-left:20px"><a href="{{ action('HomeController@healthForm_report')}}"><i class="fa fa-file-o"></i> View All Responses</a> </li>




            </ul>
        </li>


        <li><a href="http://172.17.0.2/coffeebreak/" target="_blank"><i class="fa fa-coffee" ></i> <img src="{{ asset('public/img/logo_coffeebreak.png')}}" width="100" /> <span></span></a></li>

         <li class="treeview @if (Request::is('campaign*')) active @endif">
          <a href="#"><i class="fa fa-sitemap"></i> <span>Departments / Programs</span> <i class="fa fa-angle-left pull-right"></i></a>
          <ul class="treeview-menu">
           <?php if ( OAMPI_Eval\UserType::find(Auth::user()->userType_id)->roles->pluck('label')->contains('ADD_NEW_PROGRAM') ){ ?>
            <li @if (Request::is('campaign/create')) class="active" @endif  style="padding-left:20px"><a href="{{action('CampaignController@create')}} "><i class="fa fa-plus"></i> Add New </a></li>
            <?php }  ?>

            <li style="padding-left:20px" @if (Request::is('campaign')) class="active" @endif ><a href="{{action('CampaignController@index')}} "><i class="fa fa-users"></i> View All</a></li>

            <li style="padding-left:20px" @if (Request::is('campaign')) class="active" @endif ><a href="{{action('CampaignController@orgChart')}} "><i class="fa fa-sitemap"></i> Organizational Chart</a></li>
          </ul>
        </li>


        <li class="treeview @if ( Request::is('movement') || Request::is('editUser*') ) active @endif">
          <a href="#"><i class="fa fa-users"></i> <span>Employees</span> <i class="fa fa-angle-left pull-right"></i></a>
          <ul class="treeview-menu">

            <?php if ( OAMPI_Eval\UserType::find(Auth::user()->userType_id)->roles->pluck('label')->contains('ADMINISTER_CLEARANCE') ){ ?>
              <li @if (Request::is('user')) class="active" @endif style="padding-left:20px"><a href="#" data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#clearanceModal"><i class="fa fa-file-o"></i> Clearance Forms </a></li>
             <?php }  ?>

            <?php if ( OAMPI_Eval\UserType::find(Auth::user()->userType_id)->roles->pluck('label')->contains('ADD_NEW_EMPLOYEE') ){ ?>
            <li @if (Request::is('user/create')) class="active" @endif style="padding-left:20px"><a href="{{action('UserController@create')}} "><i class="fa fa-plus"></i> Add New </a></li>
            <?php }  ?>

            <li style="padding-left:20px"@if (Request::is('user')) class="active" @endif><a href="{{action('UserController@index')}} "><i class="fa fa-users"></i> View All</a></li>

            <?php if ( OAMPI_Eval\UserType::find(Auth::user()->userType_id)->roles->pluck('label')->contains('VIEW_ALL_EVALS') ){ ?>
            <li style="padding-left:20px"><a href="{{action('UserController@downloadAllUsers')}} "><i class="fa fa-download"></i> Download Masterlist</a></li>
             <?php }  ?>

            <?php if ( OAMPI_Eval\UserType::find(Auth::user()->userType_id)->roles->pluck('label')->contains('MOVE_EMPLOYEE') ){ ?>
            <li style="padding-left:20px" @if (Request::is('movement*')) class="active" @endif><a href="{{action('MovementController@index')}}"><i class="fa fa-exchange"></i> <span>Movements</span></a></li>
          <?php }else if ( OAMPI_Eval\UserType::find(Auth::user()->userType_id)->roles->pluck('label')->contains('MANAGE_TEAM_DISTRIBUTION') ) {?>
          <li style="padding-left:20px" @if (Request::is('movement*')) class="active" @endif><a href="{{action('MovementController@index')}}"><i class="fa fa-exchange"></i> <span>Team Distribution</span></a></li>
          <?php }  ?>


            <!-- <li style="padding-left:20px"@if (Request::is('movement*')) class="active" @endif ><a href="{{action('MovementController@index')}}"><i class="fa fa-users"></i> <span>Personnel Change Notice</span></a></li> -->

          </ul>
        </li>


         <?php if ( OAMPI_Eval\UserType::find(Auth::user()->userType_id)->roles->pluck('label')->contains('ACCESS_SETTINGS') ){ ?>
        <li class="treeview @if (Request::is('evalSetting') || Request::is('evalForm*') || Request::is('pendingEvals*')) active @endif">
          <a href="#">
            <i class="fa fa-check-square-o"></i> <span>Evaluation</span>
            <i class="fa fa-angle-left pull-right"></i>
          </a>
          <ul class="treeview-menu">

          <?php if ( OAMPI_Eval\UserType::find(Auth::user()->userType_id)->roles->pluck('label')->contains('VIEW_ALL_EVALS') ){ ?>
            <li style="padding-left:20px"><a href="{{action('EvalFormController@downloadReport')}} "><i class="fa fa-download"></i> Download Summary</a></li>
            <li  @if (Request::is('evalForm')) class="active" @endif  style="padding-left:20px"><a href="{{action('EvalFormController@index')}} "><i class="fa fa-file-o"></i> View All</a></li>
             <li  @if (Request::is('pendingEvals')) class="active" @endif  style="padding-left:20px"><a href="{{action('EvalFormController@allPendings',['type'=>6])}}" class="text-yellow"><i class="fa fa-file-o"></i> For Approvals</a></li>
             <?php }  ?>


            <li style="padding-left:20px" @if (Request::is('evalSetting')) class="active" @endif ><a href="{{action('EvalSettingController@index')}} "><i class="fa fa-gears"></i> Settings</a></li>



             <?php if ( OAMPI_Eval\UserType::find(Auth::user()->userType_id)->roles->pluck('label')->contains('CREATE_EVALS') ){ ?>
            <!-- <li style="padding-left:20px"><a href="#"><i class="fa fa-plus"></i> Create New </a></li> -->
             <?php }  ?>

            <!-- <li style="padding-left:20px"><a href="{{action('EvalFormController@printBlankEval', Auth::user()->id)}} "><i class="fa fa-plus"></i> Print Blank </a></li> -->




          </ul>
        </li>  <?php }  ?>


        <!-- FINANCE FORMS -->
        <li class="treeview @if (Request::is('bulkCreate')) active @endif">
          <a href="#">
            <i class="fa fa-calculator"></i> <span>Finance</span>
            <i class="fa fa-angle-left pull-right"></i>
          </a>
          <ul class="treeview-menu">
            <li style="padding-left:20px"><a href="{{action('UserFormController@index')}} "><i class="fa fa-file-o"></i> View All Signed Forms</a></li>
            <li style="padding-left:20px"><a href="{{action('UserFormController@auditTrail')}} "><i class="fa fa-search"></i> Audit Trail</a></li>
            <li style="padding-left:20px"><a href="{{action('UserFormController@bulkCreate')}} "><i class="fa fa-upload"></i> Upload BIR Forms</a></li>
           <!--  <li  @if (Request::is('evalForm')) class="active" @endif  style="padding-left:20px"><a href="{{action('EvalFormController@index')}} "><i class="fa fa-file-o"></i> View All</a></li> -->
          </ul>
        </li>  




        <li class="treeview @if (Request::is('immediateHead*')) active @endif">
          <a href="#"><i class="fa fa-street-view"></i> <span>Leaders</span> <i class="fa fa-angle-left pull-right"></i></a>
          <ul class="treeview-menu">

             <?php if ( OAMPI_Eval\UserType::find(Auth::user()->userType_id)->roles->pluck('label')->contains('ADD_LEADER') ){ ?>
            <li @if (Request::is('immediateHead/create')) class="active" @endif  style="padding-left:20px"><a href="{{action('ImmediateHeadController@create')}} "><i class="fa fa-plus"></i> Add New Leader</a></li>
             <?php }  ?>
            <li style="padding-left:20px" @if (Request::is('immediateHead')) class="active" @endif><a href="{{action('ImmediateHeadController@index')}} "><i class="fa fa-users"></i> View All</a></li>
          </ul>
        </li>






         <li class="treeview  @if ( Request::is('user_dtr*') || Request::is('user_vl*') || Request::is('user_sl*') || Request::is('DTRlockReport*') || Request::is('track_EMSaccess') ) active @endif ">
          <a href="#" class="text-yellow">
            <i class="fa fa-clock-o"></i> <span>Timekeeping</span>
            <i class="fa fa-angle-left pull-right"></i>
          </a>
          <ul class="treeview-menu">

            <li style="padding-left:20px" @if ( Request::is('user_dtr*') ) class="active" @endif ><a  @if ( Request::is('user_*') ) class="active" @endif href="{{action('DTRController@show',Auth::user()->id)}}"><i class="fa fa-calendar"></i> My DTR</a></li>
           <!--  <li style="padding-left:20px"><a href="{{action('HomeController@module')}}"><i class="fa fa-bed"></i> Leaves</a></li>
            <li style="padding-left:20px"><a href="{{action('HomeController@module')}}"><i class="fa fa-tachometer"></i> OT / UT</a></li> --><!--
            <li style="padding-left:20px"><a href="{{action('HomeController@module')}}"><i class="fa fa-pencil"></i> CWS</a></li>
            <li style="padding-left:20px"><a href="{{action('HomeController@module')}}"><i class="fa fa-calendar-times-o"></i> File DTRP</a></li> -->
            <li style="padding-left:20px" @if ( Request::is('user_vl*') ) class="active" @endif ><a href="{{action('UserVLController@showCredits',Auth::user()->id)}}"><i class="fa fa-bar-chart"></i> Leave Credits</a></li>

            <li style="padding-left:20px" @if ( Request::is('user_vl*') ) class="active" @endif ><a href="{{action('DTRController@financeReports')}}"><i class="fa fa-clipboard"></i> Finance Reports</a></li>

            <li style="padding-left:20px" @if ( Request::is('user_vl*') ) class="active" @endif ><a href="{{action('DTRController@finance_JPS')}}"><i class="fa fa-calculator"></i>JPS Templates </a></li>

            <li style="padding-left:20px" @if ( Request::is('DTRP_management*') ) class="active" @endif ><a href="{{action('UserDTRPController@manage')}}" class="text-yellow"><i class="fa fa-info-circle"></i> DTRP Management</a></li>

            <li style="padding-left:20px" @if ( Request::is('leave_management*') ) class="active" @endif ><a href="{{action('UserController@leaveMgt')}}"><i class="fa fa-calendar"></i> Leave Management</a></li>

            <li style="padding-left:20px" @if ( Request::is('schedule_management*') ) class="active" @endif ><a href="{{action('UserController@schedMgt')}}"><i class="fa fa-history"></i> Schedule Management</a></li>

            <li style="padding-left:20px" @if ( Request::is('track_EMSaccess') ) class="active" @endif ><a href="{{action('LogsController@emsAccess')}}" class="text-yellow"><i class="fa fa-user-secret"></i> System Access Tracker</a></li>



            <hr /><!--  -->

            <li style="padding-left:20px" @if ( Request::is('DTRlockReport*') ) class="active" @endif ><a href="{{action('DTRController@lockReport')}}"><i class="fa fa-lock"></i> DTR Lock Report</a></li>


            <li style="padding-left:20px" @if ( Request::is('user_dtr*') ) class="active" @endif ><a href="{{action('DTRController@dtrSheets')}}"><i class="fa fa-download"></i> DTR Sheets</a></li>

            <li style="padding-left:20px" @if ( Request::is('user_vl*') ) class="active" @endif ><a href="{{action('DTRController@wfm_DTRsummary')}}"><i class="fa fa-file-o"></i>DTR Summary </a></li>


            <li style="padding-left:20px" @if ( Request::is('allLogs') ) class="active" @endif ><a href="{{action('LogsController@allLogs')}}"><i class="fa fa-clock-o"></i> User Logs</a></li>

            <?php if ( OAMPI_Eval\UserType::find(Auth::user()->userType_id)->roles->pluck('label')->contains('UPLOAD_BIOMETRICS') ){ ?>
            <li style="padding-left:20px"><a href="#" data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#myModal_upload"><i class="fa fa-upload"></i>Upload Biometrics</a></li>

            <li style="padding-left:20px"><a href="#" data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#myModal_upload2"><i class="fa fa-upload"></i>Upload Finance CSV</a></li>

            <li style="padding-left:20px"><a href="#" data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#myModal_upload3"><i class="fa fa-upload"></i>Upload VL Credits</a></li>

            <li style="padding-left:20px"><a href="#" data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#myModal_upload4"><i class="fa fa-upload"></i>Upload SL Credits</a></li>

            <li style="padding-left:20px"><a href="{{action('BiometricsController@workSched_upload')}}" data-backdrop="static" data-keyboard="false" ><i class="fa fa-upload"></i>Upload Work Sched</a></li>

            <li style="padding-left:20px"><a href="{{action('BiometricsController@ecq_upload')}}" data-backdrop="static" data-keyboard="false" ><i class="fa fa-ambulance"></i>ECQ Work Status</a></li>

            <?php } ?><br/>
          </ul>
        </li>











       <?php //if ( OAMPI_Eval\UserType::find(Auth::user()->userType_id)->roles->pluck('label')->contains('UPLOAD_NEW_RESOURCE') ){ ?>
        <li @if (Request::is('oampi-resources')) class="active" @endif><a href="{{action('ResourceController@index')}}" ><i class="fa fa-book"></i> <span>Resources</span></a></li>
        <?php  //endif ?>



       <!--  <li @if (Request::is('oampi-resources')) class="active" @endif><a href="{{action('FormBuilderController@index')}}"><i class="fa fa-list"></i> <span>Reports</span></a></li> -->



















<!--
        <li><a href="http://172.17.0.51/accessone/login" target="_blank"><i class="fa fa-file"></i> <span>HRIS</span></a></li>
         <li><a href="http://oampayroll.openaccessbpo.com/" target="_blank"><i class="fa fa-usd"></i> <span>Payroll</span></a></li> -->


      </ul>
      <!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
  </aside>

  @if ( OAMPI_Eval\UserType::find(Auth::user()->userType_id)->roles->pluck('label')->contains('UPLOAD_BIOMETRICS') )
  @include('layouts.modals-upload', [
                                'modelRoute'=>'biometrics.uploadSched',
                                'modelID' => '_upload5',
                                'modelName'=>"Work Schedule",
                                'modalTitle'=>'Upload',
                                'modalMessage'=>'Select CSV file to upload (*.csv):',
                                'formID'=>'uploadBio5',
                                'icon'=>'glyphicon-up' ])
  @include('layouts.modals-upload', [
                                'modelRoute'=>'user_sl.uploadCredits',
                                'modelID' => '_upload4',
                                'modelName'=>"SL Credits",
                                'modalTitle'=>'Upload',
                                'modalMessage'=>'Select CSV file to upload (*.csv):',
                                'formID'=>'uploadBio4',
                                'icon'=>'glyphicon-up' ])
  @include('layouts.modals-upload', [
                                'modelRoute'=>'user_vl.uploadCredits',
                                'modelID' => '_upload3',
                                'modelName'=>"VL Credits",
                                'modalTitle'=>'Upload',
                                'modalMessage'=>'Select CSV file to upload (*.csv):',
                                'formID'=>'uploadBio3',
                                'icon'=>'glyphicon-up' ])


  @include('layouts.modals-upload', [
                                'modelRoute'=>'biometrics.uploadFinanceCSV',
                                'modelID' => '_upload2',
                                'modelName'=>"Finance CSV file ",
                                'modalTitle'=>'Upload',
                                'modalMessage'=>'Select CSV file to upload (*.csv):',
                                'formID'=>'uploadBio2',
                                'icon'=>'glyphicon-up' ])

   @include('layouts.modals-upload', [
                                'modelRoute'=>'biometrics.upload',
                                'modelID' => '_upload',
                                'modelName'=>"Biometrics file ",
                                'modalTitle'=>'Upload',
                                'modalMessage'=>'Select biometrics file to upload (*.csv):',
                                'formID'=>'uploadBio',
                                'icon'=>'glyphicon-up' ])

@endif

@if ( OAMPI_Eval\UserType::find(Auth::user()->userType_id)->roles->pluck('label')->contains('ADMINISTER_CLEARANCE') )
<div class="modal fade" id="clearanceModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">

          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
          <h4 class="modal-title" id="myModalLabel"><img src="{{asset('public/img/oabpo_logo_new.jpg')}}" width="90px" align="center" />Launch HR Clearance Forms</h4>

      </div>
      <div class="modal-body">

        <p>You are about to launch the <em>demo version</em> of our new <strong>Clearance Form.</strong> <br/>This will launch a new tab where you will input your EMS login credentials to continue.</p>

        <br/><br/>

      </div>
      <div class="modal-footer no-border">





          <button type="button" id="hrms" class="yes btn btn-primary btn-md"  data-dismiss="modal"><i class="fa fa-check-square-o"></i> Proceed </button>
          <!-- <a href="#" id="no" class="no btn btn-success btn-md "> Proceed anyway </a> -->

          <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>



      </div>
    </div>
  </div>
</div>

@endif





