  <header class="main-header">

    <!-- Logo -->
    <a href="{{ action('HomeController@home') }}" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><img src="{{ asset('public/img/new-oam-logo-small.png')}}" width="48" style="margin: 0 auto;" /></span>
      <!-- public/img/pridelogo.png -->
      <!-- logo for regular state and mobile devices -->

      <!-- public/img/pridelogo.png -->
      <span class="logo-lg pull-left"><img src="{{ asset('public/img/white_logo_small.png')}}" width="40" /><small> <strong style="font-size: 20px;padding-top:2px" class="pull-right"> E.M.S </strong><span class="pull-right text-left" style="line-height:0.8em;padding:15px 10px 0 5px;font-size: 14px"> Open <br/>Access</span> </small></span>
    </a>

    <!-- Header Navbar -->
    <nav class="navbar navbar-static-top" role="navigation">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>
      <!-- Navbar Right Menu -->
      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <!-- Messages: style can be found in dropdown.less-->
          
          <li style="color:#fff791; font-size: x-small; margin-top:5px;padding-right: 50px; font-weight: bold; ">Got payroll issues and/or concerns? <br/><strong style="font-size: 1.5em"> <a class="btn btn-warning btn-xs" target="_blank" href="http://ticketing.openaccess.bpo/"><i class="fa fa-fax"></i> Launch Payroll Help Desk</a> </strong></li>
          <li style="color:#fff; font-size: x-small; margin-top:5px;padding-right: 20px">Employee Hotline: <br/><strong style="font-size: 1.5em"> <i class="fa fa-phone-square"></i> 0917-896-0634</strong></li>
          <li class="dropdown messages-menu">
            <!-- Menu toggle button -->
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <i class="fa fa-envelope-o"></i>
             <!-- <span class="label label-danger">1</span>-->
            </a>
            <ul class="dropdown-menu">
              <li class="header">You have a message</li>
              <li>
                <!-- inner menu: contains the messages -->
                <ul class="menu">
                  <li><!-- start message -->
                    <a href="#"  data-toggle="modal" data-target="#message">
                      <div class="pull-left">
                        <!-- User Image -->
                        <img src="{{asset('public/img/oam_favicon1-55027f4ev1_site_icon-32x32.png')}}" class="img-circle" alt="User Image">
                      </div>
                      <!-- Message title and timestamp -->
                      <h4>
                       Welcome!
                        <small><i class="fa fa-clock-o"></i> 5 mins</small>
                      </h4>
                      <!-- The message -->
                      <p>Need help using this system?</p>
                    </a>
                  </li>
                  <!-- end message -->
                </ul>
                <!-- /.menu -->
              </li>
              <li class="footer"><a href="#">See All Messages</a></li>
            </ul>
          </li>
          <!-- /.messages-menu -->

          <!-- Notifications Menu -->
          <li class="dropdown notifications-menu">
            <!-- Menu toggle button -->
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <i class="fa fa-bell-o"></i>
              <?php $notifications = OAMPI_Eval\User::find(Auth::user()->id)->notifications;
                    $unseenNotifs = OAMPI_Eval\User_Notification::where('user_id',Auth::user()->id)->where('seen',false)->orderBy('created_at','DESC')->get(); ?>

               @if (!Auth::user()->updatedPass)

                      @if ( count($unseenNotifs) > 0 )
                    <span class="label label-danger"><span class="notifyCount">{{count($unseenNotifs)+1}} </span> </span>

                    @else
                     <span class="label label-danger"><span class="notifyCount">1 </span> </span>
                    @endif

               @else
               @if (count($unseenNotifs)!==0)<span class="label label-danger"><span class="notifyCount">{{count($unseenNotifs)}} </span> </span>@endif
               @endif

              
             
              
            </a>
            <ul class="dropdown-menu">
              <?php if ( count($unseenNotifs) > 0 ){ ?>
              <li class="header">You have notifications</li>
              <li>
                <!-- Inner Menu: contains the notifications -->
                <ul class="menu">



                    <?php foreach( $unseenNotifs as $notif){ 
                      $detail = $notif->detail; // OAMPI_Eval\Notification::find($notif->notification_id); ?>
                     
                    <li class="bg-warning"><!-- start notification -->

                      <?php switch($detail->type){
                        case 1: { $actionlink = action('UserController@changePassword'); break; } //change of password
                        case 2: { $actionlink = route('movement.show', array('id' => $detail->relatedModelID, 'notif'=>$detail->id, 'seen' => true )); break; } //movement
                        case 3: { $actionlink = action('MovementController@show',['id'=>$detail->relatedModelID,'notif'=>$detail->id,'seen'=>true]); break; } //change status
                        case 4: { $actionlink =route('movement.show', array('id' => $detail->relatedModelID, 'notif'=>$detail->id, 'seen' => true )); break; } //change position
                        case 5: { $actionlink = action('EvalFormController@show',['id'=>$detail->relatedModelID, 'notif'=>$detail->id, 'seen'=>true, 'updateStatus'=>'true']); break; } //new regularization eval
                        case 6: { $actionlink = action('UserCWSController@show',['id'=>$detail->relatedModelID, 'notif'=>$detail->id, 'seen'=>true, 'updateStatus'=>'true']); break; } //change position
                        case 7: { $actionlink = action('UserOTController@show',['id'=>$detail->relatedModelID, 'notif'=>$detail->id, 'seen'=>true, 'updateStatus'=>'true']); break; } //OT
                        case 8: { $actionlink = action('UserDTRPController@show',['id'=>$detail->relatedModelID, 'notif'=>$detail->id, 'seen'=>true, 'updateStatus'=>'true']); break; } //DTRP IN
                        case 9: { $actionlink = action('UserDTRPController@show',['id'=>$detail->relatedModelID, 'notif'=>$detail->id, 'seen'=>true, 'updateStatus'=>'true']); break; } //DTRP OUT
                        case 10: { $actionlink = action('UserVLController@show',['id'=>$detail->relatedModelID, 'notif'=>$detail->id, 'seen'=>true, 'updateStatus'=>'true']); break; } //VACATION LEAVE
                        case 11: { $actionlink = action('UserSLController@show',['id'=>$detail->relatedModelID, 'notif'=>$detail->id, 'seen'=>true, 'updateStatus'=>'true']); break; } //VACATION LEAVE

                        case 12: { $actionlink = action('UserLWOPController@show',['id'=>$detail->relatedModelID, 'notif'=>$detail->id, 'seen'=>true, 'updateStatus'=>'true']); break; } //lwop LEAVE

                        case 13: { $actionlink = action('UserOBTController@show',['id'=>$detail->relatedModelID, 'notif'=>$detail->id, 'seen'=>true, 'updateStatus'=>'true']); break; } //OBT LEAVE

                        case 14: { $actionlink = action('DTRController@seenzoned',['id'=>$detail->id, 'seen'=>true, 'updateStatus'=>'true']); break; } //Unlock DTR
                        

                        case 15: { $actionlink = action('UserOTController@show',['id'=>$detail->relatedModelID, 'notif'=>$detail->id, 'seen'=>true, 'updateStatus'=>'true']);  break; } //PS-OT

                        case 16: { $actionlink = action('UserFamilyleaveController@show',['id'=>$detail->relatedModelID, 'notif'=>$detail->id, 'seen'=>true, 'updateStatus'=>'true']);  break; } //ML

                        case 17: { $actionlink = action('UserFamilyleaveController@show',['id'=>$detail->relatedModelID, 'notif'=>$detail->id, 'seen'=>true, 'updateStatus'=>'true']);  break; } //PL

                        case 18: { $actionlink = action('UserFamilyleaveController@show',['id'=>$detail->relatedModelID, 'notif'=>$detail->id, 'seen'=>true, 'updateStatus'=>'true']);  break; } //SPL 

                        case 19: { $actionlink = action('DTRController@seenzonedPD',['id'=>$detail->id, 'seen'=>true, 'updateStatus'=>'true']); break; } //Unlock DTR Production date

                        case 21: { $actionlink = action('UserVLController@showVTO',['id'=>$detail->relatedModelID, 'notif'=>$detail->id, 'seen'=>true, 'updateStatus'=>'true']); break; } //VTO LEAVE

                        default:{ $actionlink = action('UserController@changePassword'); break; }
                     
                      }?>
                      <a href="{{$actionlink}}">
                        <i class="<?php echo $detail->info->icon?>"></i>
                        <?php echo $detail->info->title?> <br/><small> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <?php echo $detail->info->detail; ?> <em><?php echo Carbon\Carbon::now()->diffForHumans($detail->created_at,true) ?> ago</em> </small>
                      </a>
                    </li>



                   <?php  } ?>


                  @if (!Auth::user()->updatedPass)

                  <li class="bg-warning"><!-- start notification -->
                      <a href="{{action('UserController@changePassword')}} ">
                        <i class="fa fa-key"></i>
                        Change your default password <br/><small> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; for security purposes <em><?php echo Carbon\Carbon::now()->diffForHumans(Auth::user()->created_at, true); ?> ago</em> </small>
                      </a>
                    </li>

                  @endif

                  

                    
                  

                  
                  <!-- end notification -->
                </ul>
              </li>
              <li class="footer"><a href="{{action('NotificationController@index')}} ">View all</a></li>
             <?php } else { ?>

             @if(!Auth::user()->updatedPass)
             <li class="header">1 new reminder</li>
              <li>
                <!-- Inner Menu: contains the notifications -->
                <ul class="menu">

                 
                 <li class="bg-warning"><!-- start notification -->
                      <a href="{{action('UserController@changePassword')}} ">
                        <i class="fa fa-key"></i>
                        Change your default password <br/><small> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; for security purposes. <em><?php echo Carbon\Carbon::now()->diffForHumans(Auth::user()->created_at, true); ?> ago</em> </small>
                      </a>
                    </li>
                  
                  <!-- end notification -->
                </ul>
              </li>
              <li class="footer"><a href="{{action('NotificationController@index')}} ">View Past Notifications</a></li>

              

             @else

              <li class="header">No new notifications</li>
              <li>
                <!-- Inner Menu: contains the notifications -->
                <ul class="menu">

                 
                  <li><!-- start notification -->
                    
                  </li>
                  
                  <!-- end notification -->
                </ul>
              </li>
              <li class="footer"><a href="{{action('NotificationController@index')}} ">View Past Notifications</a></li>


             @endif

             
             


              <?php } ?>
            </ul>
          </li>
         
          <!-- User Account Menu -->
          <li class="dropdown user user-menu">
            <!-- Menu Toggle Button -->
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <!-- The user image in the navbar-->

               @if ( file_exists('public/img/employees/'.Auth::user()->id.'.jpg') )
              <img src="{{asset('public/img/employees/'.Auth::user()->id.'.jpg')}}" class="user-image" alt="User Image">
              @else
                <img src="{{asset('public/img/useravatar.png')}}" class="user-image" alt="User Image">

              @endif



             
              <!-- hidden-xs hides the username on small devices so only the image appears. -->
              <span class="hidden-xs"> {{Auth::user()->name}} </span>
            </a>
            <ul class="dropdown-menu">
              <!-- The user image in the menu -->
              <li class="user-header">

                 @if ( file_exists('public/img/employees/'.Auth::user()->id.'.jpg') )
              <img src="{{asset('public/img/employees/'.Auth::user()->id.'.jpg')}}" class="img-circle" alt="User Image">
              @else
                <img src="{{asset('public/img/oam_favicon1-55027f4ev1_site_icon-32x32.png')}}" class="img-circle" alt="User Image">

                @endif
                

                <p>
                  @if (is_null(Auth::user()->nickname))
                 <strong> {{ Auth::user()->firstname }} {{ Auth::user()->lastname }} </strong> <br/><small><em> {{  Auth::user()->position->name }}</em><br/>
                  @else
                  <strong> {{ Auth::user()->nickname }} {{ Auth::user()->lastname }} </strong> <br/><small><em> {{  Auth::user()->position->name }}</em><br/>
                  

                  @endif

                  @if ( count(Auth::user()->campaign) > 1)

                    @foreach( Auth::user()->campaign as $camp)
                    <strong>{{$camp->name}} , </strong></small>
                    @endforeach
                 @else
                 <strong> {{  Auth::user()->campaign->first()->name }}</strong></small>

                 @endif
                  
                </p>
              </li>
              <!-- Menu Body -->
              <li class="user-body">

                <div class="row">
                  <div class="col-xs-6 text-center">
                    <a href="{{Auth::user()->floor->first()->bioIN}}" target="_blank"><i class="fa fa-hand-o-up"></i> <small>{{Auth::user()->floor->first()->name}} <br/>Biometrics</small> <strong>IN</strong> </a>
                  </div>
                 <!--  <div class="col-xs-4 text-center">
                    <a target="_blank" href="http://172.17.0.51/accessone" target="_blank"> HRIS</a>  /http://192.168.0.51/accessone/login
                  </div> -->
                  <div class="col-xs-6 text-center">
                   <a href="{{Auth::user()->floor->first()->bioOUT}}" target="_blank"><i class="fa fa-hand-o-down"></i> <small>{{Auth::user()->floor->first()->name}} <br/>Biometrics</small> <strong>OUT</strong>  </a>
                  </div>
                </div>
                <!-- /.row -->
              </li>
              <!-- Menu Footer-->
              <li class="user-footer">
                <!-- <div class="pull-left">
                  <a href="{{action('UserController@show',Auth::user()->id)}}" class="btn btn-default btn-flat">Profile</a>
                </div> -->
                <!-- <div class="pull-right"> -->
                  <a href="{{action('HomeController@logout')}}" class="btn bg-red btn-flat"><i class="fa fa-sign-out"></i> Sign out</a> <!-- {url('logout')} -->
                <!-- </div> -->
              </li>
            </ul>
          </li>
          <!-- Control Sidebar Toggle Button -->
          <li  data-step="4" data-intro="Lastly, please don't forget to <br/><strong>change your default password<br/> </strong> if you haven't done it yet, okay?<br/><br/>Have a great day ahead!" data-position='right'>
            <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
          </li>
        </ul>
      </div>
    </nav>
  </header>




  <div class="modal fade" id="message" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
          <h2 class="modal-title" id="myModalLabel" style="line-height: 0.8em"> Welcome to<br/><small> OAMPI Employee Management System</small></h2>
        
      </div>
      <div class="modal-body">
        
        <p><br/><br/>Before anything else, please make sure you've already changed your default password for security purposes.<br/><br/></p>
        <p>Read thoroughly the guidelines and instructions when evaluating an employee.<br/> If you need help using the system, see the Quick User Guide: <br /><br/>
        <p class="text-center">  <a href="http://172.17.0.2/coffeebreak/wp-content/uploads/2017/01/Quick-User-Guide-OES.pdf" target="_blank" class="btn btn-sm btn-default btn-flat"><i class="fa fa-files-o"></i> View Quick User Guide</a>
        </p></p>
        <p><strong><br/><br/>For suggestions, system bugs, and/or technical concerns, </strong> please send an e-mail to: <a href="malto:mpamero@openaccessbpo.com" >mpamero@openaccessbpo.com</a></p>
      </div>
      <div class="modal-footer no-border">
            
         
        
        <button type="button" class="btn btn-primary" data-dismiss="modal">Got it!</button>
      </div>
    </div>
  </div>
</div>