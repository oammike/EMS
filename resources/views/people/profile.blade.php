@extends('layouts.main')


@section('metatags')
  <title>{{$greeting}} {{$user->lastname}} 's Profile</title>
    <meta name="description" content="profile page">
    <link href="{{URL::asset('public/css/fullcalendar.min.css')}}" rel='stylesheet' />
<link href="{{URL::asset('public/css/fullcalendar.print.min.css')}}" rel='stylesheet' media='print' />
   
    <style type="text/css">
    .ui-draggable {cursor: move; }
    .fc-unthemed td.fc-today{background-color: #fffca7;}
    a.fc-day-number {font-weight: 600}
    .fc-event {font-size: 1em;}

    .coverphoto, .output {
      max-width: 1024px;
      height: auto;
      /*border: 1px solid black;*/
      /*margin: 10px auto;*/
    }
    .widget-user .widget-user-image-profilepage{}
    .widget-user .widget-user-image-profilepage>img{width: 200px}
  </style>


@stop


@section('content')




<section class="content-header">

      <h1 class="text-primary"><i class="fa fa-user"></i> {{$greeting}}'s Profile
        <small></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        
        <li class="active">Employee Profile</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-1"></div>
        <div class="col-xs-10">
          


          <!-- Profile Image -->
                          <div class="box box-primary" >

                            <div class="box-body box-profile"style="max-width:1024px; margin: 0 auto">
                               <!-- Widget: user widget style 1 -->
                                    <div class="box box-widget widget-user">

                                      <div class="widget-user-image-profilepage" style="z-index:100">
                  <!-- <div style="border: dotted 1px #fff; width: 180px; height: 180px;">
                 -->  
                   @if ( file_exists('public/img/employees/'.$user->id.'.jpg') )
                    <img src="{{asset('public/img/employees/'.$user->id.'.jpg')}}" style="width:190px;top:149px;left:8%;" class="user-image" alt="User Image">
                    @else
                    <img src="{{asset('public/img/useravatar.png')}}" class="user-image" alt="User Image">

                      @endif

                </div> <!--end profilepage img -->
                
                 

                <!-- Add the bg color to the header using any of the bg-* classes -->
                @if ($user->hascoverphoto !== null)
                <?php $cover = URL::to('/') . "/storage/uploads/cover-".$user->id."_".$user->hascoverphoto.".png";// URL::asset("public/img/cover/".$user->id.".jpg"); ?>
                <div class="coverphoto output widget-user-header-profilepage bg-black" style="background-size:1024px auto; background: url('{{$cover}}') center no-repeat; ">
                @else
                <div class="coverphoto output widget-user-header-profilepage bg-black" style="background: url('{{URL:: asset("public/img/newcover.jpg")}}') top center no-repeat; background-size:1024px auto">
                @endif

                 <input type="hidden" name="coverimg" id="coverimg" value="" />

                <div style="text-shadow: 1px 2px #000000; text-transform:uppercase;z-index:100;background-color: #000; padding:15px;filter: alpha(opacity=70); -moz-opacity: 0.7; -khtml-opacity: 0.7; opacity: 0.7;width: 68%; left:240px;margin-left: 0px;top:160px; " class="widget-user-username-profilepage">
                <h3 style="padding-left: 0px;">
                    <span style="filter: alpha(opacity=100); -moz-opacity: 1; -khtml-opacity: 1; opacity: 1; color:#fff,text-shadow: 1px 2px #000000; "> {{$user->firstname}} {{$user->lastname}} &nbsp;&nbsp; @if(!is_null($user->nickname)) <em style="font-size: smaller">({{$user->nickname}} )</em> @endif</span> <br/>

                    @if($isHR)
                    <span style="text-shadow: 1px 2px #000000; font-weight:bold;color:#54cbf9"  class="widget-user-desc-profilepage">{{$user->position->name}} </span> <br/>
                    @endif


                  </h3>
                  <small style="font-size:12px; "><img src="{{ asset('public/img/new-oam-logo-small.png')}}" width="15" style="margin: 0 auto;" /> {!! $camps !!} <br/>
                  <i class="fa fa-envelope-o margin-r-5"></i><span style="text-transform: lowercase; font-size: smaller">(internal): </span> <a href="mailto:{{$user->email}}"> {{$user->email}}</a> <br/>
                  @if ($user->external !== "") <i class="fa fa-envelope-o margin-r-5"></i> <span style="text-transform: lowercase; font-size: smaller">(external): </span> <a href="mailto:{{$user->external}}"> {{$user->external}}</a>  @endif</small>
                  </div>

                
                  
                </div>

                                      <div id="alert-submit" class="text-right" style="margin-top:10px"></div>
                                      
                                      <div class="box-footer">
                                        <div class="row">
                                           
                                          <!-- START CUSTOM TABS -->
     
                                          <div class="row">
                                            <div class="col-lg-1 col-sm-12"></div>
                                            <div class="col-lg-10 col-sm-12">

                                              
                                              <br/><br/>
                                              <div class="clearfix"></div>
                                              <!-- Custom Tabs -->
                                              <div class="nav-tabs-custom">
                                                <ul class="nav nav-tabs">
                                                  <li><a href="#tab_1" data-toggle="tab">EMPLOYEE DATA</a></li>
                                                  <li class="active"><a href="#tab_2" data-toggle="tab">WORK SCHEDULE </a></li>
                                                  <!-- <li><a href="#tab_3" data-toggle="tab">CONTACT INFO</a></li> -->
                                                 
                                                
                                                  
                                                </ul>
                                                <div class="tab-content">
                                                  <div class="tab-pane" id="tab_1">
                                                    <div class="row" > 
                                                      <div class="clearfix" style="padding-top:30px;">&nbsp;</div>
                                                      <div class="col-xs-1"></div>
                                                      <div class="col-xs-10">
                                                         <div class="text-left col-xs-12" style="border-bottom: solid 1px #eee; padding-bottom:15px;">

                                                         <div class="text-left col-xs-12" style="border-bottom: solid 1px #eee; padding-bottom:15px;">
                                                          <strong><i class="fa fa-smile-o margin-r-5"></i> Employment Status : </strong>
                                                          @if( $user->status_id == '6' || $user->status_id=='7' || $user->status_id=='8' || $user->status_id=='9' ) 

                                                            <strong class="text-danger" style="font-size: larger;"> {{$user->status->name}}</strong>

                                                          @else
                                                           {{$user->status->name}}

                                                          @endif
                                                        </div>

                                                           


                                                           <div class="text-left col-xs-12" style="border-bottom: solid 1px #eee; padding-bottom:15px;">
                                                          <strong><i class="fa fa-calendar margin-r-5"></i> Date Hired : </strong>
                                                           {{date("M d, Y", strtotime($user->dateHired)) }}</div>



                                                        

                                                            <div class="text-left col-xs-12" style="border-bottom: solid 1px #eee; padding-bottom:15px;">
                                                            <strong><i class="fa fa-street-view margin-r-5"></i> Immediate Supervisor: </strong>
                                                           {{$immediateHead->firstname}} {{$immediateHead->lastname}}




                                                            </div>


                                                          <div class="clearfix"></div>
                                                         
                                                  
                                                          <br />

                                                          
                                                          

                                                         
                                                         

                                                         

                                                          </div> 
                                                          <div class="col-xs-1"></div>
                                                      </div>


                                         


                                                      </div>
                                                      <!-- /.row -->
                                                    

                                                  </div><!--end pane1 -->
                                                  <!-- /.tab-pane -->



                                                  <div class="tab-pane active" id="tab_2">
                                                    

                                                    @if (empty($workSchedule))
                                                    <h3 class="text-center text-primary"><br/><br/><i class="fa fa-clock-o"></i>&nbsp;&nbsp; No Work Schedule defined</h3>
                                                    <p class="text-center"><small>Kindly inform Workforce Team or immediate head to plot {{$user->firstname}}'s  work schedule.</small><br/><br/><br/>
                                                    

                                                    @if ($anApprover || $canEditEmployees) <a href="{{action('UserController@createSchedule', $user->id)}}" class="btn btn-md btn-success"><i class="fa fa-calendar"></i> Plot Schedule Now</a>
                                                    @endif

                                                  </p>
                                                    
                                                    @else
                                                    <h4 style="margin-top:30px"><i class="fa fa-clock-o"></i> WORK SCHEDULE <br/><br/></h4>

                                                    <div class="row">
                                                      <div class="col-lg-12">
                                                        <div id="loader" ><div class="overlay"><h2 class="text-default" align="center"><i class="fa fa-refresh fa-spin"></i> Loading schedules...</h2></div>
                                                        </div> 

                                                        <div id='calendar'></div>
                                                      </div>
                                                    </div>

                                                    

                                                    @endif

                                                   
                                                    
                                                   

                                                  </div>
                                                  <!-- /.tab-pane -->



                                                  <div class="tab-pane" id="tab_3">
                                                    <h4 style="margin-top:30px"><i class="fa fa-pencil"></i> CONTACT INFORMATION <br/><br/></h4>
                                                    <div class="row">
                                                      <div class="col-lg-4"><strong> Current Address: </strong><br/>
                                                        {{$user->currentAddress1}} <br/>
                                                        {{$user->currentAddress2}}<br/>
                                                        {{$user->currentAddress3}}</div>
                                                      <div class="col-lg-4"><strong> Permanent Address: </strong>
                                                      <br/>
                                                        {{$user->permanentAddress1}} <br/>
                                                        {{$user->permanentAddress2}}  <br/>
                                                        {{$user->permanentAddress3}} </div>

                                                        <div class="col-lg-4"><strong><i class="fa fa-mobile"></i> Mobile Number: </strong>
                                                        {{$user->mobileNumber}}  <br/><br/>
                                                        <strong><i class="fa fa-phone"></i> Telephone Number: </strong>{{$user->phoneNumber}} <br/>
                                                        </div>
                                                    </div>
                                                  </div>
                                                  <!-- /.tab-pane -->
                                                </div>
                                                <!-- /.tab-content -->
                                              </div>
                                              <!-- nav-tabs-custom -->
                                            </div>
                                            <!-- /.col -->
                                            <div class="col-lg-1 col-sm-12"></div>

                                           
                                          </div>
                                          <!-- /.row -->
                                          <!-- END CUSTOM TABS -->
                                          
                                          <!-- /.col -->
                                          

                                      </div>
                                    </div>
                                    <!-- /.widget-user -->

                            </div>
                            

                            <!-- /.box-body -->
                          </div>

        </div>
        <div class="col-xs-1"></div>

      </div>

     

    </section>

@stop

@section('footer-scripts')

<script src="{{URL::asset('public/js/moment.min.js')}}" ></script>
<script src="{{URL::asset('public/js/fullcalendar.min.js')}}" ></script>
<script src="{{URL::asset('public/js/gcal.min.js')}}" ></script>




<script type="text/javascript">
    $(function() {

     

       $('#calendar').fullCalendar({
            
            customButtons: {
              myCustomButton: {
                text: 'View Daily Time Record',
                click: function() {
                  window.location = "{{action('DTRController@show',$user->id)}}";
                }
              }
            },

            
         
            header: {
              right: 'title, prev,next today',
              center: '',
              //left: ''
              left: 'myCustomButton' //month,agendaWeek,agendaDay'
            },
          


            //defaultDate: '2017-09-12',
            defaultView: 'month',
            defaultDate: '<?php echo date('Y-m-d')?>',
            eventColor: '#67aa08',
            eventBorderColor:'#fff',
            eventBackgroundColor: '#c1ff71', //#fffca7',
            navLinks: false, // can click day/week names to navigate views
            editable: false,
            eventLimit: true, // allow "more" link when too many events
            eventOrder:"counter",
            
            displayEventTime: false,
            showNonCurrentDates: false,
            //googleCalendarApiKey: 'AIzaSyBTE3gRTwEglwrJ6ZtiUn5ZHqc-lb3NNkk',
            eventSources: [
                {
                    //googleCalendarId: 'en.philippines#holiday@group.v.calendar.google.com',
                    className: 'gcal-event smaller', // an option!
                    backgroundColor: '#fff',// '#dd4b39'
                    textColor: '#dd4b39'
                },
                 {

                  url:"{{action('UserController@getWorkSched', ['id'=>$user->id])}}",
                  type:'GET',
                  
                  error:function(){ alert('Error fetching schedule');},


                },

                  
            ],
            loading: function (bool) {
               $('#loader').fadeIn(); // Add your script to show loading '<i class=fa fa-></i>'.
            },
            
            eventAfterAllRender: function (view) {
                $('#loader').fadeOut();
            },
            eventRender: function(event, element) {
                 if(event.icon){          
                    element.find(".fc-title").prepend("<i class='fa fa-"+event.icon+"'></i> ");
                 }
              },
            navLinks: true,
            selectable: false,
            unselectAuto: false,
            select: function (start, end, jsEvent, view) {
                $("#calendar").fullCalendar('addEventSource', [{
                    start: start,
                    end: end,
                    rendering: 'background',
                    block: true,
                }, ]);
                $("#calendar").fullCalendar("unselect");
            },
            selectOverlap: function(event) {
                return ! event.block;
            },
            navLinkDayClick: function(date, jsEvent) {}

            

            
           
        });


    
     
      



    });
</script>

  





@stop