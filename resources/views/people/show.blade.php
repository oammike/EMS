@extends('layouts.main')


@section('metatags')

@if (substr(strtoupper($user->lastname),-1) == 'S')
      <title>{{$greeting}} {{$user->lastname}}' Profile</title>
      @else
      <title>{{$greeting}} {{$user->lastname}}'s Profile</title>
      @endif

  
    <meta name="description" content="profile page">
    <link rel="stylesheet" href="{{URL::asset('public/css/coverphoto.css')}}" />
    <link rel="stylesheet" href="{{URL::asset('public/css/croppie.css')}}" />


<link href="{{URL::asset('public/css/fullcalendar.min.css')}}" rel='stylesheet' />
<link href="{{URL::asset('public/css/fullcalendar.print.min.css')}}" rel='stylesheet' media='print' />
   
    <style type="text/css">
    .ui-draggable {cursor: move; }
    .fc-unthemed td.fc-today{background-color: #fffca7;}
    a.fc-day-number {font-weight: 600}
    .fc-event {font-size: 1em;}
    .gcal-event.smaller{font-size: 10px;}

    .coverphoto, .output {
      max-width: 1024px;
      height: auto;
      /*border: 1px solid black;*/
      /*margin: 10px auto;*/
    }
    .widget-user .widget-user-image-profilepage{}
    .widget-user .widget-user-image-profilepage>img{width: 200px}
    .theFuture {background-color: #fff; }  /*feffd8;}e1eff7; }*/
    .thePast {background-color: #e6e6e6;}
  </style>


@stop


@section('content')




<section class="content-header">

      <h1>
      <i class="fa fa-address-card-o"></i>
      @if (substr(strtoupper($greeting),-1) == 'S')
      <span style="text-transform: uppercase;"> {{$greeting}}' Profile
      @else
      <span style="text-transform: uppercase;"> {{$greeting}}'s Profile
      @endif
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

            <div class="box-body box-profile" style="max-width:1024px; margin: 0 auto">
              <!-- Widget: user widget style 1 -->
              <div class="box box-widget widget-user">

                <div class="widget-user-image-profilepage" style="z-index:100">

                    @if ( file_exists('public/img/employees/'.$user->id.'.jpg') )
                    <img src="{{asset('public/img/employees/'.$user->id.'.jpg')}}" style="width:190px;top:149px;left:8%;" class="user-image" alt="User Image">
                    @else
                    <img src="{{asset('public/img/useravatar.png')}}" class="user-image" alt="User Image">
                    @endif
                    
                </div> <!--end profilepage img -->

                @if ($hasNewPhoto && $canEditEmployees)
                <a href="{{action('UserController@updateProfilepic',$user->id)}}" class="btn btn-xs btn-default" style="position: absolute;top:340px;left:48px;  z-index: 999"><i class="fa fa-pencil"></i> Edit Pic</a>
                @endif
                 

                <!-- Add the bg color to the header using any of the bg-* classes -->
                @if ($user->hascoverphoto !== null)
                <?php $cover = URL::to('/') . "/storage/uploads/cover-".$user->id."_".$user->hascoverphoto.".png";// URL::asset("public/img/cover/".$user->id.".jpg"); ?>
               <div class="coverphoto output widget-user-header-profilepage bg-black" style=" background-size:1024px auto; background: url('{{$cover}}') center no-repeat;"> <!--  -->
                @else
                <div class="coverphoto output widget-user-header-profilepage bg-black" style="background: url('{{URL:: asset("public/img/newcover.jpg")}}')top center no-repeat; background-size:1024px auto">
                @endif

                 <input type="hidden" name="coverimg" id="coverimg" value="" />

                <div style="text-shadow: 1px 2px #000000; text-transform:uppercase;z-index:100;background-color: #000; padding:15px;filter: alpha(opacity=70); -moz-opacity: 0.7; -khtml-opacity: 0.7; opacity: 0.7;width: 68%; left:240px;margin-left: 0px;top:160px; " class="widget-user-username-profilepage">
                <h3 style="padding-left: 0px;">
                    <span style="filter: alpha(opacity=100); -moz-opacity: 1; -khtml-opacity: 1; opacity: 1; color:#fff,text-shadow: 1px 2px #000000; "> {{$user->firstname}} {{$user->lastname}} &nbsp;&nbsp; @if(!is_null($user->nickname)) <em style="font-size: smaller">({{$user->nickname}} )</em> @endif</span> <br/>

                    @if($isHR)
                    <span style="text-shadow: 1px 2px #000000; font-weight:bold;color:#54cbf9"  class="widget-user-desc-profilepage">{{$user->position->name}} </span> 
                    @endif
                    <br/>


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
                     <!--  <div class="col-lg-1 col-sm-12"></div> -->
                      <div class="col-lg-12 col-sm-12">

                        
                        <br/><br/>
                        <div class="clearfix"></div>
                        <!-- Custom Tabs -->
                        <div class="nav-tabs-custom">
                          <ul class="nav nav-tabs">
                           
                            <li><a href="#tab_1" data-toggle="tab"><strong class="text-primary ">EMPLOYEE DATA</strong></a></li>
                            <li class="active"><a href="#tab_2" data-toggle="tab"><strong class="text-primary">WORK SCHEDULE </strong></a></li>
                            <li><a href="#tab_3" data-toggle="tab"><strong class="text-primary">CONTACT INFO</strong></a></li>
                            <li><a href="#tab_4" data-toggle="tab"><strong class="text-primary">EVALUATIONS</strong></a></li>
                            <li><a href="#tab_stats" data-toggle="tab"><strong class="text-primary">AGENT STATS</strong></a></li>
                           
                            <li class="dropdown pull-right">
                              <a class="dropdown-toggle bg-green" data-toggle="dropdown" href="#">
                               <i class="fa fa-gear"></i> Actions <span class="caret"></span>
                              </a>
                              <ul class="dropdown-menu">

                                @if ($anApprover || $canEditEmployees)
                                 <li role="presentation">
                                  <a role="menuitem" tabindex="-1" href="{{action('DTRController@show',$user->id)}}">
                                    <i class="fa fa-calendar"></i> Daily Time Record</a></li>

                                @endif
                                @if ($canMoveEmployees)
                                <li role="presentation">
                                  <a role="menuitem" tabindex="-1" href="{{action('MovementController@changePersonnel',$user->id)}}">
                                    <i class="fa fa-exchange"></i> Movements</a></li>@endif

                                  @if ($canEditEmployees || $theOwner)
                                  <li role="presentation">
                                  <a role="menuitem" tabindex="-1" href="{{action('UserController@editContact',$user->id)}}">
                                    <i class="fa fa-tty"></i> Edit Contact Info</a></li>
                                  @endif
                                     
                                 @if ($canEditEmployees)
                                 <li role="presentation">
                                  <a role="menuitem" tabindex="-1" href="{{action('UserController@editUser',$user->id)}}">
                                    <i class="fa fa-address-card"></i> Edit Employee Data</a></li>@endif

                                 @if ($anApprover || $canEditEmployees)
                                 <li role="presentation">
                                  <a role="menuitem" tabindex="-1" href="{{action('UserController@createSchedule', $user->id)}}">
                                  <i class="fa fa-clock-o"></i> Edit Work Schedule</a></li>


                                 <li role="presentation">
                                  <a role="menuitem" tabindex="-1" href="{{action('UserController@editUser',['id'=>$user->id,'page'=>3])}}">
                                  <i class="fa fa-bar-chart"></i> Update Leave Credits</a></li>
                                  @endif


                                  @if(Auth::user()->id == $user->id)
                                  <li role="presentation">
                                  <a role="menuitem" tabindex="-1" href="{{action('UserController@myRequests',$user->id)}}">
                                  <i class="fa fa-clipboard"></i> View Requests</a></li>
                                 

                                  @elseif ($anApprover)
                                  <li role="presentation">
                                  <a role="menuitem" tabindex="-1" href="{{action('UserController@userRequests',$user->id)}}">
                                  <i class="fa fa-clipboard"></i> View Requests</a></li>
                                  @endif



                                  




                                 
                                
                                <!-- <li role="presentation" class="divider"></li>
                                <li role="presentation"><a role="menuitem" tabindex="-1" href="#">Change Status</a></li> -->
                              </ul>
                            </li>
                            
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

                                      @if (date("Y-m-d", strtotime($user->dateHired)) == "1970-01-01")
                                      <em>N/A</em>
                                      @else
                                      {{date("M d, Y", strtotime($user->dateHired)) }}
                                      @endif
                                     
                                   </div>



                                    <div class="text-left col-xs-12" style="border-bottom: solid 1px #eee; padding-bottom:15px;">
                                      <strong><i class="fa fa-list-alt margin-r-5"></i> Employee number : </strong>
                                     {{$user->employeeNumber}}</div>

                                      <div class="text-left col-xs-12" style="border-bottom: solid 1px #eee; padding-bottom:15px;">
                                        <strong><i class="fa fa-street-view margin-r-5"></i> Immediate Supervisor: </strong>
                                       {{$immediateHead->firstname}} {{$immediateHead->lastname}}
                                      </div>

                                      <div class="text-left col-xs-12" style="border-bottom: solid 1px #eee; padding-bottom:15px;">
                                        <strong><i class="fa fa-check-square-o margin-r-5"></i> Approver(s): </strong>
                                       
                                       <?php foreach($approvers as $app){
                                                      $lead = OAMPI_Eval\ImmediateHead::find($app->immediateHead_id);?>

                                                      &nbsp;{{$lead->firstname}} {{$lead->lastname}}&nbsp; <strong>|</strong> 

                                                    <?php } ?>
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
                               <a href="{{action('DTRController@show',$user->id)}}" class="btn btn-sm btn-default pull-left"><i class="fa fa-calendar"></i> View Daily Time Record</a>
                                <h3 class="text-center text-primary"><br/><br/><i class="fa fa-clock-o"></i>&nbsp;&nbsp; No Work Schedule defined</h3>
                                <p class="text-center"><small>Kindly inform immediate head OR Workforce Team to plot {{$user->firstname}}'s  work schedule.</small><br/><br/><br/>

                                @if ($anApprover || $canEditEmployees || ($isWorkforce && !$isBackoffice))  
                                <a href="{{action('UserController@createSchedule', $user->id)}}" class="btn btn-md btn-success"><i class="fa fa-calendar"></i> Plot Schedule Now</a>
                                @endif

                              </p>
                                
                                @else
                                <h4 style="margin-top:30px" id="ws"><i class="fa fa-clock-o"></i> WORK SCHEDULE 
                                 <!--  <a href="{{action('DTRController@show',$user->id)}}" class="btn btn-sm btn-success pull-right"><i class="fa fa-calendar"></i> View Daily Time Record</a> --><br/><br/></h4>
                                
                                

                                  
                                  
                                     
                                      <p><small>Click on the calendar dates to <strong>plot work schedule</strong> or file requests like <strong>SL | VL | LWOP | OBT. </strong></small></p>
                                      

                                  <div class="row">
                                    <div class="col-lg-12"><!--style="width:98%; height: 750px; position: absolute; background-color: #333; z-index: 999;filter: alpha(opacity=90); -moz-opacity: 0.9; -khtml-opacity: 0.9; opacity: 0.9; "-->
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

                             <div class="tab-pane" id="tab_4">
                              <h4 style="margin-top:30px"><i class="fa fa-file"></i> EVALUATIONS <br/><br/></h4>
                              <table class="table">
                              <?php $ctr=0; ?>
                             

                              @foreach ($userEvals->groupBy('evalTitle') as $eval)

                              


                               

                                  @foreach($eval as $ev)

                                    @if($ctr < 1)
                                      <tr>
                                        <th>{{ $eval[0]['evalTitle'] }}</th>
                                        <th>Date Evaluated</th>
                                        <th>Rating</th>
                                        <th>Eval Weight</th>
                                        <th>Final Rating</th>
                                        
                                        <th></th>

                                      </tr>
                                      @endif

                                      <tr>
                                      <td><small><em>by:{{ $ev['by'] }} </em><br>
                                        <strong>Period:</strong> {{$ev['sP']}} to {{$ev['eP']}} <br/>
                                        <strong>Days covered:</strong> {{$ev['daysHandled']}} of {{$ev['totalDays']}} </small> </td> 
                                      
                                      <td> {{ date('Y-m-d',strtotime($ev['details'][0]['created_at'])) }} </td>
                                      
                                       @if($canViewAllEvals)
                                      <td>{{$ev['details'][0]['overAllScore']}} </td>
                                      @else
                                      <td> <em>* Access Denied * </em></td>
                                      @endif

                                      <?php if ($ev['totalDays'] !== 0){ ?>

                                      <!-- ******* if may missing dates na minimal, add it to first eval nalang *********-->
                                      @if($ev['missing'] > 0 && $ev['missing']<= count($eval))

                                      <td>{{ number_format( ( ($ev['daysHandled']+$ev['missing'])/$ev['totalDays'])*100,2) }}% </td>
                                      <td>{{ number_format((  ($ev['daysHandled']+$ev['missing'])/$ev['totalDays'])* $ev['details'][0]['overAllScore'] ,2) }} </td>

                                      @else
                                      <td>{{ number_format( ($ev['daysHandled']/$ev['totalDays'])*100,2) }}% </td>
                                      <td>{{ $ev['grade'] }} </td>

                                      @endif

                                    <?php } else { ?>
                                      <td> - </td>
                                      <td> - </td>

                                    <?php } ?>

                                      
                                     

                                      <td>
                                        @if($canViewAllEvals)
                                        <a class="btn btn-xs btn-success" target="_blank" href="{{ action('EvalFormController@show',$ev['details'][0]['id'])}} "><i class="fa fa-file"></i> View </a>  
                                        <a class="btn btn-xs btn-primary" target="_blank" href="{{ action('EvalFormController@printEval',$ev['details'][0]['id'])}} "><i class="fa fa-print"></i> Print </a> 

                                          @if($canEditEmployees)
                                        <a class="btn btn-xs btn-default" target="_blank" data-toggle="modal" data-target="#myModalEVAL{{$ev['details'][0]['id']}}"><i class="fa fa-trash"></i>&nbsp; </a> 
                                          @include('layouts.modals-del', [
                                            'modelRoute'=>'evalForm.deleteThisEval',
                                            'modelID' => $ev['details'][0]['id'], 
                                            'modelName'=>"Eval by ".  $ev['by'],
                                            'modelType'=>"EVAL", 
                                            'modalTitle'=>'Delete', 
                                            'modalMessage'=>'Are you sure you want to delete this?', 
                                            'formID'=>'deleteEval',
                                            'icon'=>'glyphicon-trash' ])

                                          @endif
                                        @endif
                                      </td>
                                      
                                        
                                    </tr>

                                    <?php 

                                    $ctr++; 
                                    if(count($eval) == $ctr)
                                    {
                                      $ctr=0;
                                      if ( ( $ev['daysCtr'] < $ev['totalDays']) && ( $ev['daysCtr']+1 !== $ev['totalDays']) && ($ev['missing']> count($eval))  )
                                      {
                                        //$missing = $ev['totalDays']-$ev['daysCtr'];

                                       ?>
                                      <tr style="background-color: #d8f2ff">
                                        <th></th>
                                        <th>({{$ev['daysCtr']}} of {{$ev['totalDays']}} days ) completed</th>
                                        <th></th>
                                        <th class="text-danger">Missing evals</th>
                                        <th>{{$ev['missing']}}days </th>
                                        <th></th>

                                      </tr>


                                      <?php } else { ?>

                                      <tr style="background-color: #d8f2ff">
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th class="text-primary">Overall Rating</th>

                                        @if($ev['missing'] > 0 && $ev['missing']<= count($eval))
                                        <?php $diff = ((($ev['daysHandled']+$ev['missing'])/$ev['totalDays'])* $ev['details'][0]['overAllScore']) - $ev['grade'];  ?>
                                        <th style="font-size: x-large;" class="text-primary">{{number_format($diff+ $ev['finalGrade'],2)}} </th>
                                        @else
                                        <th style="font-size: x-large;" class="text-primary">{{$ev['finalGrade']}} </th>
                                        @endif

                                        <th></th>

                                      </tr>


                                      <?php }

                                    }   ?>

                                  @endforeach

                                    


                                    

                                  

                               

                                

                              @endforeach
                            </table>
                            </div>
                            <!-- /.tab-pane -->
                            
                            
                            
                            <div class="tab-pane" id="tab_stats">
                            
                              <div class="row">

                                <div class="col-lg-12">
                                  <div id="statloader" ><div class="overlay"><h2 class="text-default" align="center"><i class="fa fa-refresh fa-spin"></i> Loading schedules...</h2></div>
                                </div> 

                                <div class="row">
                                  <div class="col-xs-12">
                                    <div class="box box-primary">
                                      <div class="box-header with-border">
                                        <i class="fa fa-bar-chart-o"></i>
                                        <h3 class="box-title">Daily Activity Summary</h3>
                                        <div class="box-tools pull-right">
                                          Date Range
                                          <div class="btn-group" id="realtime" data-toggle="btn-toggle">
                                            <button type="button" class="btn btn-default pull-right" id="daterange-btn2">
                                              <span>
                                                <i class="fa fa-calendar"></i> Date range
                                              </span>
                                              <i class="fa fa-caret-down"></i>
                                            </button>
                                          </div>
                                        </div>
                                      </div>
                                      <div class="box-body">
                                        <canvas id="chart_activitysummary" width="720" height="480" />
                                      </div><!-- /.box-body-->
                                    </div><!-- /.box -->
                          
                                  </div><!-- /.col -->
                                </div><!-- /.row -->

                              </div>

                            </div>
                            <!-- /.tab-pane -->


                          </div>
                          <!-- /.tab-content -->
                        </div>
                        <!-- nav-tabs-custom -->
                      </div>
                      <!-- /.col -->
                      <!-- <div class="col-lg-1 col-sm-12"></div> -->

                     
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
        <div class="col-xs-1" id="holder"></div>

      </div>

     

    </section>

@stop

@section('footer-scripts')

<link href="{{ asset( 'public/js/bootstrap-daterangepicker/daterangepicker.css' ) }}" rel="stylesheet">
<script src="{{URL::asset('public/js/coverphoto.js')}}"></script>
<script src="{{URL::asset('public/js/moment.min.js')}}" ></script>
<script src="{{URL::asset('public/js/fullcalendar.min.js')}}" ></script>
<script src="{{URL::asset('public/js/gcal.min.js')}}" ></script>
<script type="text/javascript" src="{{ asset( 'public/js/chartjs/Chart.min.js' ) }}"></script>
<script type="text/javascript" src="{{ asset( 'public/js/bootstrap-daterangepicker/daterangepicker.js' ) }}"></script>

<script type="text/javascript">
    $(function() {
    
      window.user_id = {{ $user->id }};
      window.start = moment().subtract(6, 'days');
      window.end = moment();
      window.chartColors = {
        red: 'rgba(255, 99, 132, 0.8)',
        orange: 'rgba(255, 159, 64, 1.0)',
        yellow: 'rgba(255, 205, 86, 1.0)',
        green: 'rgba(171, 235, 198, 1.0)',
        blue: 'rgba(54, 162, 235, 1.0)',
        purple: 'rgba(153, 102, 255, 1.0)',
        grey: 'rgba(201, 203, 207, 1.0)',
        darkblue: 'rgba(33, 47, 61, 1.0)',
        lightblue: 'rgba(169, 204, 227, 1.0)',
        black: 'rgba(1, 1, 1, 1.0)'
      };
      window.colorKeys = [
        "red", "orange", "yellow", "green", "blue", "purple", "grey", "darkblue", "lightblue", "black"
      ];


      $('a#tab_4').on('click',function(){
     
        //log eval viewing;
        $.ajax({
                url: "{{action('HomeController@logAction','10')}}",
                type: "GET",
                data: {'action': '10'},
                success: function(response){
                          console.log(response);

              }

        });
    

      });
      
      function pad(num) {
        return ("0"+num).slice(-2);
      }
      
      function hhmmss(seconds) {
        return (Math.floor(seconds / 3600)) + ":" + ("0" + Math.floor(seconds / 60) % 60).slice(-2) + ":" + ("0" + seconds % 60).slice(-2)
      }
      
      function loadActivityDate(start, end) {
        $('#daterange-btn2 span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        window.start = start;
        window.end = end;
        fetchActivityData();
      }
      $('#daterange-btn2').daterangepicker(
        {
          ranges   : {
            'Last 7 Days' : [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month'  : [moment().startOf('month'), moment().endOf('month')],
            'Last Month'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
          },
          startDate: window.start,
          endDate  : window.end
        }, loadActivityDate
      );
      
      function fetchActivityData() {
        $('#statloader').show();
        var mData = {
          "user_id": window.user_id,
          "start": window.start.unix(),
          "end": window.end.unix(),
          "_token": "{{ csrf_token() }}"
        };
        $.ajax({
          url: "{{ url('/getIndividualStat') }}",
          type: "POST",
          data: mData,
          success: function(rtnData) {
            $('#statloader').hide();
            if(window.activityChart!==undefined) window.activityChart.destroy();
            console.log(rtnData);
            mDataSets = [];
            colorKey = 0;
            Object.keys(rtnData.stats.datasets).forEach(function(key){
                var value = rtnData.stats.datasets[key];
                console.log(key + ':' + value);
                var dset = {
                  label: key,
                  backgroundColor: window.chartColors[window.colorKeys[colorKey]],
                  data: value
                }
                colorKey++;
                mDataSets.push(dset);
            });
            
            
            var activityData = {
              type: 'bar',
              data: {
                labels: rtnData.stats.labels,
                datasets: mDataSets
              },
              options: {
                tooltips:{
                  mode: 'index',
                  intersect: true,
                  callbacks: {
                    label: function (tooltipItem, data) {
                      return data.datasets[tooltipItem.datasetIndex].label + ": " + hhmmss(tooltipItem.yLabel);
                    }
                  }
                },
                scales: {
                  xAxes: [{
                    stacked: true,
                    barPercentage: 0.4
                  }],
                  yAxes: [{
                    stacked: true,
                    ticks: {
                      beginAtZero: true,
                      callback: value => {
                        return hhmmss(value);
                      }
                    }
                  }]
                },
                responsive: true,
                maintainAspectRatio: false,
                title: { display: false }
              }
            };
            
            var ctx1 = document.getElementById("chart_activitysummary").getContext("2d");
            window.activityChart = new Chart(ctx1, activityData);
          },
          error: function(rtnData) {
            console.log('error' + rtnData);
          }
        });
      }
    
      //load chartjs for agent stats
      $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        if(e.target.hash === "#tab_stats"){
          loadActivityDate(window.start, window.end);
        }
      })

      $('#statloader').hide();
      $('#loader').hide();
      //$( ".datepicker" ).datepicker({dateFormat:"YYYY-mm-dd"});


       $('#calendar').fullCalendar({
            
            customButtons: {
              myCustomButton: {
                text: 'DTR Sheet',
                click: function() {
                  window.location = "{{action('DTRController@show',$user->id)}}";
                }
              },
              viewRequests: {
                text: 'DTR Requests',
                click: function() {
                  window.location = "{{action('UserController@userRequests',$user->id)}}";
                }
              }
            },

            @if ($anApprover)
            header: {
              right: 'title, prev,next today',
              center: '',
              
             left: 'myCustomButton, viewRequests' //month,agendaWeek,agendaDay'
            },
            @else
            header: {
              right: 'title, prev,next today',
              center: '',
              //left: ''
              left: 'myCustomButton, viewRequests' //month,agendaWeek,agendaDay'
            },
            @endif


            //defaultDate: '2017-09-12',
            defaultView: 'month',
            defaultDate: '<?php echo date('Y-m-d')?>',
            eventColor: '#67aa08',
            eventBorderColor:'#fff',
            eventBackgroundColor: '#c1ff71', //#fffca7',
            navLinks: true, // can click day/week names to navigate views
            editable: false,
            eventLimit: true, // allow "more" link when too many events
            eventOrder:"counter",
            //eventClick: function(calEvent, jsEvent, view) {

                //alert('Event: ' + calEvent.title);
                //alert('Coordinates: ' + jsEvent.pageX + ',' + jsEvent.pageY);
                //alert('View: ' + view.name);

                // change the border color just for fun
                //$(this).css('border-color', 'red');

            //},
            displayEventTime: false,
            showNonCurrentDates: false,
            //googleCalendarApiKey: 'AIzaSyBTE3gRTwEglwrJ6ZtiUn5ZHqc-lb3NNkk',
            eventSources: [

                 {

                  url:"{{action('UserController@getWorkSched', ['id'=>$user->id])}}",
                  type:'GET',
                  
                  error:function(){ alert('Error fetching schedule');},


                },
                {
                    //googleCalendarId: 'en.philippines#holiday@group.v.calendar.google.com',
                    className: 'gcal-event smaller', // an option!
                    backgroundColor: '#fff',// '#dd4b39'
                    textColor: '#000',
                    counter: 999
                },

                  
            ],
            loading: function (bool) {
               $('#loader').fadeIn(); // Add your script to show loading '<i class=fa fa-></i>'.
            },
            
            eventAfterAllRender: function (view) {
                $('#loader').fadeOut();
            },
            eventRender: function(event, element, view) {
                 if(event.icon){          
                    element.find(".fc-title").prepend("<i class='fa fa-"+event.icon+"'></i> ");
                 }else if(event.icon2){          
                    element.find(".fc-title").prepend("<i class='fa fa-2x fa-"+event.icon2+"'></i> ");
                 }else if(event.icon3)  element.find(".fc-title").prepend("<i class='fa fa-"+event.icon3+"'></i> ");

                
              },
            dayRender: function( date, cell ) { 
              if (date < moment() )cell.addClass('thePast');
              else  cell.addClass('theFuture')
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

            //navLinkDayClick
            //dayClick: function(date, jsEvent, view, resourceObj) {
            navLinkDayClick: function(date, jsEvent) {

             
              var clickedDate = $(jsEvent.target).parent(); 
              var clickedItem = $(jsEvent.target).parent().index();
              var rootElem = clickedDate.parent().parent();
              var allEvents = $(rootElem).next().get(); 
             
              var chenes1 =$(allEvents[0]['childNodes'][0]['childNodes']).length;
              var chenes2 =$(allEvents[0]['childNodes'][1]['childNodes']).length;

              

                var timeIN = $(allEvents[0]['childNodes'][0]['childNodes']).get(clickedItem).innerText;
                var timeOUT = null; //$(allEvents[0]['childNodes'][1]['childNodes']).get(clickedItem);//[clickedItem]['childNodes'][0]).get(); 

                //console.log(timeIN);
                var rdTxt = "Rest";
                var nosched = "NO";

                if (timeIN.indexOf(rdTxt) != -1) //-- it's RD, we need to check previous rest days then subtract index for timeOUT
                {
                  timeOUT="00:00:00";
                }else { 

                  if (timeIN.indexOf(nosched) != -1) //no schedule set
                    {
                      
                      $.notify("No saved work schedule.\n\n Check if your DAILY TIME RECORD hasn't been locked yet \nand then submit a DTRP for approval, or simply inform your immediate head to plot the work schedule for you.\n\nFor approvers, click on the 'Actions' button >> Edit Work Schedule.\n\nFor leave requests, go to 'My Requests' section and submit the approriate type of leave for approval.\nIf you're an approver, go to My Team >> Requests to file leaves for your agent.",{className:"error",globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );
                      return false;

                    }else 
                    { 
                          /* ---- bug fix for problem rows ----*/
                          if (chenes1 > chenes2)
                          {
                            timeOUT = $(allEvents[0]['childNodes'][1]['childNodes']).get(clickedItem-(chenes1-chenes2)).innerText;

                          } else //normal rows
                          {
                            timeOUT = $(allEvents[0]['childNodes'][1]['childNodes']).get(clickedItem).innerText;
                          }

                        

                    } //.innerText; }

               

                } // }

            
              
              //console.log(timeIN);
              //console.log(timeOUT);
              //}
              

              var currDate = moment();
              var selectedDate = date.format('MMM Do, YYYY dddd');
              var productionDate = date.format('YYYY-MM-DD');
              var initdate = date.format('M/D/YYYY');

                if (date.format() >= currDate.subtract(1,'days').format()){
                  //console.log("future");

                  var htmlcode = '<div class="modal fade" id="myModal'+date.format()+'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">';

                  /* --------------------- modal for APPROVERS ----------------*/
                  @if ($anApprover)

                  htmlcode +=            '<div class="modal-dialog">';
                  htmlcode +=              '<div class="modal-content">';
                  htmlcode +=                '<div class="modal-header">';
                                    
                  htmlcode +=                    '<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>';
                  htmlcode +=                    '<h4 class="modal-title text-default" id="myModalLabel">Plot Work Schedule </h4></div>';
                  htmlcode += '<form class="col-lg-12" id="plotSched" name="plotSched">';
                  htmlcode +=                '<input type="hidden" name="biometrics_id" value= />';//+event.biometrics_id+
                  htmlcode +=                '<input type="hidden" name="productionDate" value="'+ productionDate+' " />';
                  htmlcode +=                '<input type="hidden" name="user_id" value="{{$user->id}}" />';
                  htmlcode +=                '<input type="hidden" name="isRD" value="$isRD}}" />';
                  htmlcode +=                '<input type="hidden" name="approver" value="$approver" />';
                  htmlcode +=                '<input type="hidden" name="timeStart_old" value="'+timeIN+'" />';
                  htmlcode +=                '<input type="hidden" name="timeEnd_old" value="'+timeOUT+'" />';
                  htmlcode +=                '<div class="modal-body-upload" style="padding:20px;"><br/><br/>';
                  htmlcode +=                   '<p class="text-center"><a id="single" class="btn btn-lg btn-flat"><i class="fa fa-clock-o"></i> Single-day Schedule</a><a class="btn btn-lg btn-flat" href="{{action("UserController@createSchedule",$user->id)}}"><i class="fa fa-calendar"></i> Multiple-day Schedule</a></p>';
                  htmlcode +=                   '<div class="options">';

                   htmlcode +=                  '<h5 class="text-center"><br/><br/>New Work Schedule for : <br/><strong class="text-danger">'+selectedDate+'</strong> </h5>';

                  htmlcode +=                   '<div class="row"><div class="col-sm-2"></div>';
                  htmlcode +=                       '<div class="col-sm-4">';
                  htmlcode += '<label><input type="radio" class="schedtype" name="schedtype" id="fulltime" value="f" /> Full time</label>';
                                          
                  htmlcode +=                            '<select name="shift" id="shift_f" class="end form-control" style="margin-bottom:5px"><option value="0">* Select shift *</option><option value="-1">- REST DAY -</option>';
                                                @foreach ($shifts as $shift)
                                                   htmlcode+='<option value="{{$shift}}">{{$shift}} </option>';

                                               @endforeach
                  htmlcode +=                           '</select></div>';
                  htmlcode +=                       '<div class="col-sm-4">';
                  htmlcode += '<label><input type="radio" name="schedtype" class="schedtype" id="parttime" value="p" /> Part time</label>'
                                          
                  htmlcode +=                            '<select name="shift" id="shift_p" class="end form-control" style="margin-bottom:5px"><option value="0">* Select shift *</option><option value="-1">- REST DAY -</option>';
                                                @foreach ($partTimes as $pshift)
                                                   htmlcode+='<option value="{{$pshift}}">{{$pshift}} </option>';

                                               @endforeach
                  htmlcode +=                           '</select></div>';
                  htmlcode += '<div class="col-sm-2"></div></div></div>';

                  htmlcode +=                  '<div id="alert-upload" style="margin-top:10px"></div><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>';
                                    
                                    
                                    
                  htmlcode +=                  '<button type="button" class="btn btn-default btn-md pull-right " data-dismiss="modal"> <i class="fa fa-times"></i> Cancel</button>';
                  htmlcode +=                  '<button type="submit" id="save" data-date="'+date.format()+'" data-userID="{{$user->id}}" class="btn btn-success btn-md pull-right" style="margin-right:5px" > <i class="fa fa-save" ></i> Save Schedule </button></div> </form>';

                  htmlcode +=                '<div class="modal-body-generate"></div>';
                  htmlcode +=                '<div class="modal-footer no-border">';
                                    
                  htmlcode +=                '</div></div></div></div>';


                  $('#holder').html(htmlcode);
                  $('.options').fadeOut();
                  $('#shift_f,#shift_p').fadeOut();
                  $('#save').fadeOut();

                  $('#single').on('click', function(){ $('.options').fadeIn(); $('#save').fadeIn();} );

                  $('#myModal'+date.format()).modal('toggle');







                  /* --------------------- modal for REG USER ----------------*/
                  @else 

                  htmlcode +=            '<div class="modal-dialog">';
                  htmlcode +=              '<div class="modal-content"  id="modalMain">';
                  htmlcode +=                '<div class="modal-header">';
                                    
                  htmlcode +=                    '<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>';
                  htmlcode +=                    '<h4 class="modal-title text-default" id="myModalLabel">Submit DTR Requests: </h4></div><form class="col-lg-12" id="plotSched" name="plotSched">';
                  htmlcode +=                '<input type="hidden" name="biometrics_id" value=  />';
                  htmlcode +=                '<input type="hidden" name="productionDate" value="'+ productionDate+' " />';
                  htmlcode +=                '<input type="hidden" name="user_id" value="{{$user->id}}" />';
                  htmlcode +=                '<input type="hidden" name="isRD" value="$isRD}}" />';
                  htmlcode +=                '<input type="hidden" name="approver" value="$approver" />';
                  
                  htmlcode +=                '<div id="mainMenus" class="modal-body-upload myPanes" style="padding:20px; border:2px dotted #dedede"><br/><br/>';

                  htmlcode +=                   '<div class="row"><div class="col-lg-6"><p class="text-left"><a id="cws" class="btn btn-md btn-flat"><i class="fa fa-2x fa-clock-o"></i>&nbsp;&nbsp; Change Work Schedule <strong style="font-size:11px;font-weight:bolder">[CWS]</strong></a><br/></p>';
                  htmlcode +=                   '<p class="text-left"><a id="vl" class="btn btn-md btn-flat"><i class="fa fa-2x fa-plane"></i>&nbsp;&nbsp; Vacation Leave <strong style="font-size:11px;font-weight:bolder">[VL]</strong></a><br/></p><p class="text-left"><a id="sl" class="btn btn-sm btn-flat"><i class="fa fa-2x fa-stethoscope"></i>&nbsp;&nbsp; Sick Leave <strong style="font-size:11px;font-weight:bolder">[SL]</strong></a><br/></p>';
                  // htmlcode +=                   '<a id="el" class="btn btn-lg btn-flat"><i class="fa fa-2x fa-ambulance"></i>&nbsp;&nbsp; Emergency Leave <strong style="font-size:11px;font-weight:bolder">[EL]</strong></a><br/>'
                  htmlcode += '<p class="text-left"><a id="lwop" class="btn btn-md btn-flat" ><i class="fa fa-2x fa-meh-o"></i>&nbsp;&nbsp; Leave Without Pay <strong style="font-size:11px;font-weight:bolder">[LWOP]</strong> </a><br/></p></div><div class="col-lg-6"><p class="text-left"><a id="obt" class="btn btn-sm btn-flat"><i class="fa  fa-2x fa-suitcase"></i>&nbsp;&nbsp; Official Business Trip <strong style="font-size:11px;font-weight:bolder">[OBT] </strong> </a><br/><p class="text-left"><a id="ml" data-type="ML" class="btn btn-md btn-flat"><i class="fa  fa-2x fa-female"></i>&nbsp;&nbsp; Maternity Leave <strong style="font-size:11px;font-weight:bolder">[ML] </strong> </a><br/></p><p class="text-left"><a id="pl" data-type="PL" class="btn btn-md btn-flat"><i class="fa  fa-2x fa-male"></i>&nbsp;&nbsp; Paternity Leave <strong style="font-size:11px;font-weight:bolder">[PL] </strong> </a><br/></p><p class="text-left"><a id="spl" data-type="SPL" class="btn btn-md btn-flat"><i class="fa  fa-2x fa-street-view"></i>&nbsp;&nbsp; Single-Parent Leave <strong style="font-size:11px;font-weight:bolder">[SPL] </strong> </a><br/>';
                  htmlcode += '</p></div></div>';
                  
                 

                  htmlcode +=                  '<div id="alert-upload" style="margin-top:10px"></div><br/><br/><br/><br/>';
                                    
                                    
                                    
                  htmlcode +=                  '<button type="button" class="btn btn-default btn-md pull-right " data-dismiss="modal"> <i class="fa fa-times"></i> Cancel</button>';
                  htmlcode +=                  '</div> ';

                  htmlcode += '<div id="pane_CWS" class="modal-body-upload" style="padding:20px; border:2px dotted #dedede"><p>Current Work Schedule: <strong>'+timeIN+' '+timeOUT+'  </strong> </p>';
                   htmlcode +=                   '<div class="options_cws">';

                   htmlcode +=                  '<h4 class="text-center"><br/><br/>Request <span class="text-primary">New Work Schedule</span> for : <br/><strong class="text-danger">'+selectedDate+'</strong> </h4>';

                  htmlcode +=                   '<div class="row"><div class="col-sm-2"></div>';
                  htmlcode +=                       '<div class="col-sm-4">';
                                          
                   htmlcode += '<label><input type="radio" class="schedtype" name="schedtype" id="fulltime" value="f" /> Full time</label>';
                                          
                  htmlcode +=                            '<select name="shift" id="shift_f" class="end form-control" style="margin-bottom:5px"><option value="0">* Select shift *</option><option value="-1">- REST DAY -</option>';
                                                @foreach ($shifts as $shift)
                                                   htmlcode+='<option value="{{$shift}}">{{$shift}} </option>';

                                               @endforeach
                  htmlcode +=                           '</select></div>';
                  htmlcode +=                       '<div class="col-sm-4">';
                  htmlcode += '<label><input type="radio" name="schedtype" class="schedtype" id="parttime" value="p" /> Part time</label>'
                                          
                  htmlcode +=                            '<select name="shift" id="shift_p" class="end form-control" style="margin-bottom:5px"><option value="0">* Select shift *</option><option value="-1">- REST DAY -</option>';
                                                @foreach ($partTimes as $pshift)
                                                   htmlcode+='<option value="{{$pshift}}">{{$pshift}} </option>';

                                               @endforeach
                  htmlcode +=                           '</select></div>';

                  htmlcode += '                 <div class="col-sm-2"></div></div>';
                  htmlcode += '<div class="row"><div class="col-sm-12"><br/><label>Reason: </label><textarea required class="form-control" name="reason_cws" id="reason_cws" /></div></div>'
                  htmlcode += '</div>';
                  htmlcode += '<h2> <br/><br/></h2>';
                  htmlcode +=                '<input type="hidden" name="timestart_old" value="'+timeIN+'" />';
                  htmlcode +=                '<input type="hidden" name="timeend_old" value="'+timeOUT+'" />';
                  htmlcode +=                  '<a class="back btn btn-flat pull-left" style="margin-top:-30px"><i class="fa fa-angle-double-left"></i> &nbsp;Back</a><button type="button" class="btn btn-default btn-md pull-right " data-dismiss="modal"> <i class="fa fa-times"></i> Cancel</button>';
                  htmlcode +=                  '<button type="submit" id="save" data-timestart_old="'+timeIN+'" data-timeend_old="'+timeOUT+'" data-requesttype="CWS" data-date="'+date.format()+'" data-userid="{{$user->id}}" class="btn btn-primary btn-md pull-right" style="margin-right:5px" > <i class="fa fa-upload" ></i> Submit for Approval </button></div></form> ';



                  //----------------- VACATION LEAVE PANE ------------------------------------

                  htmlcode += '<div id="pane_VL" class="modal-body-upload" style="padding:20px; border:2px dotted #dedede">';
                   htmlcode +=         '<div class="options_vl">';

                   htmlcode += '<div style="width:100%; " class="pull-left">';
                   
                   htmlcode +=               '<div class="info-box bg-blue">';
                   htmlcode +=                '<span class="info-box-icon"><i class="fa fa-plane"></i></span>';

                   htmlcode +=                 '<div class="info-box-content">';
                   htmlcode +=                   '<span class="info-box-text"><strong>Vacation Leave</strong> details :</span>';
                   htmlcode +=                   '<span class="info-box-number"><span class="text-gray">Credits: </span><span id="credits_vl" data-credits="1.0"> 1.00</span></span>';

                   htmlcode +=                   '<div class="progress">';
                  htmlcode +=                      '<div class="progress-bar" style="width: 20%"></div>';
                  htmlcode +=                    '</div>';
                  htmlcode +=                    '<span class="progress-description">';
                  htmlcode +=                         ' <span class="pull-left">From: '+date.format('MMM DD ddd')+' </span> <span id="vlto" class="pull-right"> To: '+date.format('MMM DD ddd')+'</span>';
                  htmlcode +=                        '</span>';
                  htmlcode +=                  '</div>';
                  htmlcode +=                  '<!-- /.info-box-content -->';
                  htmlcode +=                '</div>';
                  htmlcode +=                '<!-- /.info-box -->';

                   htmlcode += '</div>';

                   htmlcode +=             ' <br/>';

                   htmlcode += '            <div class="row">';
                   htmlcode += '                <div class="col-lg-6">';
                   htmlcode += '                      <label for="vl_from">From: <input required type="text" class="dates form-control datepicker" name="vl_from" id="vl_from" value="'+initdate+'" /></label><div id="alert-vl_from" style="margin-top:10px"></div>';
                   htmlcode += '                </div>';
                   htmlcode += '              <div class="col-lg-6"> ';
                   htmlcode += '              <label ><input type="radio" name="coveredshift" id="shift_whole" value="1" />&nbsp; &nbsp;<i class="fa fa-hourglass"></i> Whole Day</label><br/>';
                   htmlcode += '              <label ><input type="radio" name="coveredshift" id="shift_first" value="2" />&nbsp; &nbsp;<i class="fa fa-hourglass-start"></i> 1st Half of Shift</label> <br/>';
                   htmlcode += '              <label ><input type="radio" name="coveredshift" id="shift_second" value="3" />&nbsp; &nbsp;<i class="fa fa-hourglass-end"></i> 2nd Half of Shift</label><br/>';
                   htmlcode += '              </div>';
                   htmlcode += '            </div>';
                   htmlcode += '        </div> ';

                  htmlcode +=              '<div class="row">';
                  htmlcode +='                  <div class="col-lg-12">';
                  htmlcode +='                  <a href="#" class="addDays"><i class="fa fa-plus"></i> Add more days</a><br/><br/>';

                  htmlcode +='                  <div class="moredays">';
                  htmlcode +='                    <div class="row"><input type="hidden" name="timestart_old2" /><input type="hidden" name="timeend_old2" />';
                   htmlcode += '                  <div class="col-lg-6">';
                   htmlcode += '                      <label for="vl_from">Until: <input required type="text" class="dates form-control datepicker" name="vl_to" id="vl_to"  /></label><div id="alert-vl_from" style="margin-top:10px"></div>';
                   htmlcode += '                  </div>';
                   htmlcode += '                  <div class="col-lg-6"><div id="vl_more"> ';
                   htmlcode += '                  <label ><input type="radio" name="coveredshift2" id="shift2_whole" value="1" /> &nbsp; &nbsp;<i class="fa fa-hourglass"></i> Whole Day</label><br/>';
                   htmlcode += '                  <label ><input type="radio" name="coveredshift2" id="shift2_first" value="2" /> &nbsp; &nbsp;<i class="fa fa-hourglass-start"></i> Half Day</label> <br/>';
                   // htmlcode += '                  <label ><input type="radio" name="coveredshift2" id="shift2_second" value="3" />&nbsp; &nbsp;<i class="fa fa-hourglass-end"></i> 2nd Half of Shift</label><br/>';
                   htmlcode += '</div>';
                   htmlcode += '                  </div>';
                   htmlcode += '                </div>';

                   htmlcode += '              </div>';
                  htmlcode+= '                  <label><r/><br/><br/> Notes: </label><br/><small>Kindly provide a brief description about your vacation leave</small><br/> <textarea name="reason_vl" style="width:100%;" /><h2> <br/><br/></h2></div>';
                  htmlcode +='              </div>';
                  // htmlcode +=                       '<div class="col-sm-6">';
                                          
                  // htmlcode += '</div><div class="col-sm-3"></div></div></div>';
                  // htmlcode += '<h2> <br/><br/></h2>';
                  htmlcode +=                '<input type="hidden" name="timestart_old" value="'+timeIN+'" />';
                  htmlcode +=                '<input type="hidden" name="timeend_old" value="'+timeOUT+'" />';
                   htmlcode +=                '<input type="hidden" name="leaveFrom" />';
                   htmlcode +=                '<input type="hidden" name="leaveTo" />';
                  htmlcode +=                  '<div class="clearfix"></div><a class="back btn btn-flat pull-left" style="font-weight:bold;margin-top:0px; z-index:999"><i class="fa fa-angle-double-left"></i> &nbsp;Back</a><button type="button" class="btn btn-default btn-md pull-right " data-dismiss="modal"> <i class="fa fa-times"></i> Cancel</button>';
                  htmlcode +=                  '<button type="submit" id="save" data-timestart_old="'+timeIN+'" data-timeend_old="'+timeOUT+'" data-requesttype="VL" data-date="'+date.format()+'" data-userid="{{$user->id}}" class="btn btn-primary btn-md pull-right" style="margin-right:5px" > <i class="fa fa-upload" ></i> Submit for Approval </button></div></form> ';


                  //------------------ SICK LEAVE PANE --------------------------
                  htmlcode += '<div id="pane_SL" class="modal-body-upload" style="padding:20px; border:2px dotted #dedede"><p>Current Work Schedule: <strong>'+timeIN+' '+timeOUT+'  </strong> </p>';
                   htmlcode +=                   '<div class="options_cws">';

                   htmlcode +=                  '<h4 class="text-center"><br/><br/>File a Sick Leave for : <br/><strong class="text-danger">'+selectedDate+'</strong> </h4>';

                  htmlcode +=                   '<div class="row"><div class="col-sm-3"></div>';
                  htmlcode +=                       '<div class="col-sm-6">';
                                          
                  htmlcode +=                            '<select name="shift" class="end form-control" style="margin-bottom:5px"><option value="0">* Select shift *</option><option value="-1">- REST DAY -</option>';
                                                @foreach ($shifts as $shift)
                                                   htmlcode+='<option value="{{$shift}}">{{$shift}} </option>';

                                               @endforeach
                  htmlcode +=                           '</select></div><div class="col-sm-3"></div></div></div>';
                  htmlcode += '<h2> <br/><br/></h2>';
                  htmlcode +=                '<input type="hidden" name="timestart_old" value="'+timeIN+'" />';
                  htmlcode +=                '<input type="hidden" name="timeend_old" value="'+timeOUT+'" />';
                  htmlcode +=                  '<a class="back btn btn-flat pull-left" style="margin-top:-30px"><i class="fa fa-angle-double-left"></i> &nbsp;Back</a><button type="button" class="btn btn-default btn-md pull-right " data-dismiss="modal"> <i class="fa fa-times"></i> Cancel</button>';
                  htmlcode +=                  '<button type="submit" id="save" data-timestart_old="'+timeIN+'" data-timeend_old="'+timeOUT+'" data-requesttype="CWS" data-date="'+date.format()+'" data-userid="{{$user->id}}" class="btn btn-primary btn-md pull-right" style="margin-right:5px" > <i class="fa fa-upload" ></i> Submit for Approval </button></div></form> ';


                  //------------------ EMERGENCY LEAVE PANE --------------------------
                  htmlcode += '<div id="pane_EL" class="modal-body-upload" style="padding:20px; border:2px dotted #dedede"><a class="back btn btn-flat pull-right"><i class="fa  fa-angle-double-left"></i> &nbsp;Back</a><p>Current Work Schedule: <strong>'+timeIN+' '+timeOUT+'  </strong> </p>';
                   htmlcode +=                   '<div class="options_cws">';

                   htmlcode +=                  '<h4 class="text-center"><br/><br/>File an Emergency Leave for : <br/><strong class="text-danger">'+selectedDate+'</strong> </h4>';

                  htmlcode +=                   '<div class="row"><div class="col-sm-3"></div>';
                  htmlcode +=                       '<div class="col-sm-6">';
                                          
                  htmlcode +=                            '<select name="shift" class="end form-control" style="margin-bottom:5px"><option value="0">* Select shift *</option><option value="-1">- REST DAY -</option>';
                                                @foreach ($shifts as $shift)
                                                   htmlcode+='<option value="{{$shift}}">{{$shift}} </option>';

                                               @endforeach
                  htmlcode +=                           '</select></div><div class="col-sm-3"></div></div></div>';
                  htmlcode += '<h2> <br/><br/></h2>';
                  htmlcode +=                '<input type="hidden" name="timestart_old" value="'+timeIN+'" />';
                  htmlcode +=                '<input type="hidden" name="timeend_old" value="'+timeOUT+'" />';
                  htmlcode +=                  '<button type="button" class="btn btn-default btn-md pull-right " data-dismiss="modal"> <i class="fa fa-times"></i> Cancel</button>';
                  htmlcode +=                  '<button type="submit" id="save" data-timestart_old="'+timeIN+'" data-timeend_old="'+timeOUT+'" data-requesttype="CWS" data-date="'+date.format()+'" data-userid="{{$user->id}}" class="btn btn-primary btn-md pull-right" style="margin-right:5px" > <i class="fa fa-upload" ></i> Submit for Approval </button></div></form> ';


                  //------------------ LWOP PANE --------------------------
                  htmlcode += '<div id="pane_LWOP" class="modal-body-upload" style="padding:20px; border:2px dotted #dedede"><p>Current Work Schedule: <strong>'+timeIN+' '+timeOUT+'  </strong> </p>';
                   htmlcode +=                   '<div class="options_cws">';

                   htmlcode +=                  '<h4 class="text-center"><br/><br/>File a Leave Without Pay for : <br/><strong class="text-danger">'+selectedDate+'</strong> </h4>';

                  htmlcode +=                   '<div class="row"><div class="col-sm-3"></div>';
                  htmlcode +=                       '<div class="col-sm-6">';
                                          
                  htmlcode +=                            '<select name="shift" class="end form-control" style="margin-bottom:5px"><option value="0">* Select shift *</option><option value="-1">- REST DAY -</option>';
                                                @foreach ($shifts as $shift)
                                                   htmlcode+='<option value="{{$shift}}">{{$shift}} </option>';

                                               @endforeach
                  htmlcode +=                           '</select></div><div class="col-sm-3"></div></div></div>';
                  htmlcode += '<h2> <br/><br/></h2>';
                  htmlcode +=                '<input type="hidden" name="timestart_old" value="'+timeIN+'" />';
                  htmlcode +=                '<input type="hidden" name="timeend_old" value="'+timeOUT+'" />';
                  htmlcode +=                  '<a class="back btn btn-flat pull-left" style="margin-top:-30px"><i class="fa fa-angle-double-left"></i> &nbsp;Back</a><button type="button" class="btn btn-default btn-md pull-right " data-dismiss="modal"> <i class="fa fa-times"></i> Cancel</button>';
                  htmlcode +=                  '<button type="submit" id="save" data-timestart_old="'+timeIN+'" data-timeend_old="'+timeOUT+'" data-requesttype="CWS" data-date="'+date.format()+'" data-userid="{{$user->id}}" class="btn btn-primary btn-md pull-right" style="margin-right:5px" > <i class="fa fa-upload" ></i> Submit for Approval </button></div></form> ';


                  //------------------ OBT PANE --------------------------
                  htmlcode += '<div id="pane_OBT" class="modal-body-upload" style="padding:20px; border:2px dotted #dedede"><p>Current Work Schedule: <strong>'+timeIN+' '+timeOUT+'  </strong> </p>';
                   htmlcode +=                   '<div class="options_cws">';

                   htmlcode +=                  '<h4 class="text-center"><br/><br/>File an Official Business Trip for : <br/><strong class="text-danger">'+selectedDate+'</strong> </h4>';

                  htmlcode +=                   '<div class="row"><div class="col-sm-3"></div>';
                  htmlcode +=                       '<div class="col-sm-6">';
                                          
                  htmlcode +=                            '<select name="shift" class="end form-control" style="margin-bottom:5px"><option value="0">* Select shift *</option><option value="-1">- REST DAY -</option>';
                                                @foreach ($shifts as $shift)
                                                   htmlcode+='<option value="{{$shift}}">{{$shift}} </option>';

                                               @endforeach
                  htmlcode +=                           '</select></div><div class="col-sm-3"></div></div></div>';
                  htmlcode += '<h2> <br/><br/></h2>';
                  htmlcode +=                '<input type="hidden" name="timestart_old" value="'+timeIN+'" />';
                  htmlcode +=                '<input type="hidden" name="timeend_old" value="'+timeOUT+'" />';
                  htmlcode +=                  '<a class="back btn btn-flat pull-left" style="margin-top:-30px"><i class="fa fa-angle-double-left"></i> &nbsp;Back</a><button type="button" class="btn btn-default btn-md pull-right " data-dismiss="modal"> <i class="fa fa-times"></i> Cancel</button>';
                  htmlcode +=                  '<button type="submit" id="save" data-timestart_old="'+timeIN+'" data-timeend_old="'+timeOUT+'" data-requesttype="CWS" data-date="'+date.format()+'" data-userid="{{$user->id}}" class="btn btn-primary btn-md pull-right" style="margin-right:5px" > <i class="fa fa-upload" ></i> Submit for Approval </button></div></form> ';



                  htmlcode +=                '<div class="modal-body-generate"></div>';
                  htmlcode +=                '<div class="modal-footer no-border">';
                                    
                  htmlcode +=                '</div></div></div></div>';




                  

                  $('#holder').html(htmlcode);

                  $('.options').fadeOut(); $('#pane_CWS, #pane_SL, #pane_VL,#pane_EL,#pane_LWOP,#pane_OBT,.moredays').hide();
                  $('#save').fadeOut();

                  $('#cws').on('click', function(){

                    @if (count($user->approvers) > 0)

                     
                      $('#mainMenus').fadeOut(function(){$('#pane_CWS').fadeIn(); $('.options').fadeIn(); $('#save').fadeIn();});
                      $('#shift_f,#shift_p').fadeOut(); 
                     

                    @else

                    
                       $.notify("Sorry, you can't submit work schedule requests yet.\n\nPlease inform HR to have your profile updated\n and indicate the necessary approver(s) for all of your requests. Thank you.",{globalPosition:'right middle',autoHideDelay:25000, clickToHide:true} );
                    

                  @endif
                   
                  });

                  $('#vl').on('click', function()
                  { 

                    var toPage = "../user_vl/create?from="+productionDate;// 
                    window.location = toPage;

                   
                   

                  });//END ON CLICK VL option

                  $('input[name="coveredshift"]').on('change', function(){
                    var vl_from = $('input[name="vl_from"]').val(); 
                    var vl_to = $('input[name="vl_to"]').val(); 
                    var shift2 = $('input[name="coveredshift2"]:checked').val();
                    var vl_credits = $("span#credits_vl");
                    var theshift = $(this).val();
                    var vl_from1 = moment(vl_from,"MM/DD/YYYY");

                        checkIfRestday(vl_from);

                        if (vl_to == "") computeCredits(vl_from1,null,theshift,shift2);
                        else computeCredits(vl_from1,vl_to,theshift,shift2);
                      

                    });




                  $('#sl').on('click', function()
                    {
                      var toPage = "../user_sl/create?from="+productionDate;// 
                      window.location = toPage; 
                      //$('#mainMenus').fadeOut(function(){$('#pane_SL').fadeIn(); $('.options').fadeIn(); $('#save').fadeIn();}); 
                  } );

                  
                  $('#el').on('click', function()
                    { $('#mainMenus').fadeOut(function(){$('#pane_EL').fadeIn(); $('.options').fadeIn(); $('#save').fadeIn();}); 
                  } );

                  $('#lwop').on('click', function()
                    { 
                      var toPage = "../user_lwop/create?from="+productionDate;// 
                      window.location = toPage;
                      //$('#mainMenus').fadeOut(function(){$('#pane_LWOP').fadeIn(); $('.options').fadeIn(); $('#save').fadeIn();}); 
                  } );

                  $('#obt').on('click', function()
                    { 
                      var toPage = "../user_obt/create?from="+productionDate;// 
                      window.location = toPage;
                      //$('#mainMenus').fadeOut(function(){$('#pane_OBT').fadeIn(); $('.options').fadeIn(); $('#save').fadeIn();}); 
                  } );


                  $('#ml,#pl,#spl').on('click', function(){
                      var ltype = $(this).attr('data-type');
                      var toPage = "../user_fl/create?for={{$user->id}}&type="+ltype+"&from="+productionDate;// 
                      window.location = toPage; 
                      //$('#mainMenus').fadeOut(function(){$('#pane_SL').fadeIn(); $('.options').fadeIn(); $('#save').fadeIn();}); 
                  });


                  $('.back').on('click', function(){$('#pane_CWS,#pane_VL, #pane_SL,#pane_EL,#pane_LWOP,#pane_OBT').hide(); $('#mainMenus').fadeIn()});

                  $('#myModal'+date.format()).modal('toggle');

                  $('.datepicker').datepicker({dateFormat:"YYYY-mm-dd"});

                  @endif
                } 

                else{
                  $.notify("Sorry, you are no longer allowed to plot schedule for this date.\nInstead, check if your DAILY TIME RECORD hasn't been locked yet and then submit a DTRP for approval. \nOR click on the 'Actions' button >> Edit Work Schedule if you are an approver.\n\nFor leave requests, go to 'My Requests' section and submit the approriate type of leave for approval.\nIf you're an approver, go to My Team >> Requests to file leaves for your agent.",{globalPosition:'right middle',autoHideDelay:25000, clickToHide:true} );
                  
                }

               // console.log('date', date);
               // console.log('day', date.format()); // date is a moment
               // console.log('coords', jsEvent.pageX, jsEvent.pageY);
              },
            

            
           
        });

   

     // $('.fc-today-button.fc-button.fc-state-default.fc-corner-left.fc-corner-right').trigger('click');
     $( ".datepicker" ).on('click', '#calendar', function(){ $(this).datepicker({dateFormat:"YYYY-mm-dd"}); console.log("datepicker");})

     $('#holder').on('click','.schedtype', function(){

                    var s = $(this).val();
                    console.log(s);

                    if (s == 'f'){ $("#shift_f").fadeIn(); $('#shift_p').fadeOut();}
                    else if ( s == 'p') { $("#shift_p").fadeIn(); $('#shift_f').fadeOut();}

                  });

     $('#holder').on('click','#save',function(e){


        @if($anApprover)
          /* -------------------------------------------------- APPROVER ---------------------*/
            e.preventDefault(); e.stopPropagation();
            var _token = "{{ csrf_token() }}";
            //var isParttime = $('input [name="schedtype"] :selected').val();
            

            if($('#parttime').is(':checked')) 
              var shift = $('select#shift_p :selected').val();
            else if($('#fulltime').is(':checked')) 
              var shift = $('select#shift_f :selected').val();
            
            var user_id = $(this).attr('data-userID');
            var selectedDate = $(this).attr('data-date');
            
           

            if (shift == "0"){
              alert("Please select a work shift. ");

              return false;
            } 
            else{

               //if (shift == "-1") alert("Set {{$user->firstname}} {{$user->lastname}}'s Work Shift to: * REST DAY * ?");
               //else alert("Set {{$user->firstname}} {{$user->lastname}}'s Work Shift to: " + shift+'?');

               $.ajax({
                    url: "{{action('MonthlyScheduleController@plot')}}",
                    type:'POST',
                    data:{ 
                      'id': user_id,
                      'shift': shift,
                      'selectedDate': selectedDate,
                      '_token':_token
                    },
                    success: function(response){
                      $('#calendar').fullCalendar( 'refetchEvents' );
                      $('.modal.fade').modal('hide');
                      console.log(response);
                    }
                  });


            }


        @else
            /* -------------------------------------------------- REG USER ---------------------*/
            var requestType = $(this).attr('data-requesttype');

            
            

            e.preventDefault(); e.stopPropagation();
            var _token = "{{ csrf_token() }}";
            
            if($('#parttime').is(':checked')) 
              var shift = $('select#shift_p :selected').val();
            else if($('#fulltime').is(':checked')) 
              var shift = $('select#shift_f :selected').val();
            
            var user_id = $(this).attr('data-userid');
            var selectedDate = $(this).attr('data-date');
            

            switch(requestType){
              case "CWS": 
                          {
                            var timestart_old = $('input[name="timestart_old"]').val();
                            var timeend_old =  $('input[name="timeend_old"]').val();
                            var reason_cws = $('textarea[name="reason_cws"]').val();

                            console.log(timestart_old);
                            console.log(timeend_old);

                            if (shift == "0"){
                               $.notify("Please select a work shift for this CWS.",{className:"error", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );
                               return false;
                            } else if( reason_cws == "" ){
                               $.notify("Kindly indicate reason of filing CWS for HR-Finance's review.",{className:"error", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );
                               return false;

                            } 
                            else{

                               // if (shift == "-1") alert("Send Request to Change Work Schedule to: * REST DAY *");
                               // else alert("Send Request to Change Work Schedule to: " + shift);

                               $.ajax({
                                    url: "{{action('UserCWSController@requestCWS')}}",
                                    type:'POST',
                                    data:{ 
                                      'id': user_id,
                                      'shift': shift,
                                      'selectedDate': selectedDate,
                                      'timestart_old': timestart_old,
                                      'timeend_old': timeend_old,
                                      'reason_cws': reason_cws,
                                      '_token':_token
                                    },
                                    success: function(response){
                                      
                                     

                                      if (response.success == '1')
                                        $.notify("CWS successfully saved.",{className:"success",globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );
                                        else
                                          $.notify("CWS saved for approval.",{className:"success", globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );
                                      console.log(response);


                                       $('#calendar').fullCalendar( 'refetchEvents' );
                                      $('.modal.fade').modal('hide');
                                    }
                                  });


                            }

                          }break;

              /*case "VL": 
                          {
                            var vl_from = $('input[name="vl_from"]').val(); // MM/dd/YYYY
                           

                            
                            console.log("pasok VL");
                            var timestart_old1 = $('input[name="timestart_old"]').val();
                            var timeend_old1 =  $('input[name="timeend_old"]').val();

                            
                            var vl_to = $('input[name="vl_to"]').val();

                            var coveredshift = $('input[name="coveredshift"]:checked').val();

                            var reason_vl = $('textarea[name="reason_vl"]').val();
                            var totalcredits = $('#credits_vl').attr('data-credits');

                            //if (checkIfRestday(vl_from, reason_vl))
                            //{
                              console.log("pasok checkIfRestday");

                                if (vl_to == "") //one-day leave lang sya
                                  {
                                      //check kung anong covered shift
                                      
                                      
                                        var coveredshifts = getCoveredShifts(coveredshift, vl_from, timestart_old1, timeend_old1);
                                        var leaveFrom = coveredshifts.leaveStart.format('YYYY-MM-D H:mm:ss');
                                        var leaveTo = coveredshifts.leaveEnd.format('YYYY-MM-D H:mm:ss');
                                        console.log("Start: " + leaveFrom);
                                        console.log("End: " + leaveTo);
                                      
                                  } 
                                  else
                                  {

                                      var mto = moment(vl_to,"MM/D/YYYY").format('YYYY-MM-D');
                                      var mfrom = moment(vl_from,"MM/D/YYYY").format('YYYY-MM-D')
                                      if ( moment(vl_to,"MM/D/YYYY").isBefore( moment(vl_from,"MM/D/YYYY")) )
                                      {
                                        
                                         $.notify("Invalid 'Until' date. Selected date is past your 'From' date.",{className:"error",globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );


                                        console.log("mto: ");
                                        console.log(mto);
                                        console.log("mfrom: ");
                                        console.log(mfrom); //return false;
                                      }
                                      else
                                      {

                                        if (reason_vl == ""){  alert("Please indicate reason of your leave."); return false; }
                                        else{

                                          console.log("pasok reason_vl");

                                              var coveredshifts = getCoveredShifts(coveredshift, vl_from, timestart_old1, timeend_old1);
                                              var leaveFrom = coveredshifts.leaveStart.format('YYYY-MM-D H:mm:ss');
                                              var coveredshift2 = $('input[name="coveredshift2"]:checked').val();
                                              var timestart_old2 = $('#pane_VL input[name="timestart_old2"]').val();
                                              var timeend_old2 = $('#pane_VL input[name="timeend_old2"]').val();

                                              //console.log(timestart_old2);
                                              //console.log(timeend_old2);


                                              switch(coveredshift2)
                                              {
                                                case '1': {var leaveEnd2 = moment(timeend_old2);
                                                          leaveTo = leaveEnd2.format('YYYY-MM-D HH:mm:ss');
                                                          }break; //wholeday
                                                case '2': {var l2 =  moment(timestart_old2); var leaveEnd2 = l2.add(4,'hours');
                                                          leaveTo = leaveEnd2.format('YYYY-MM-D HH:mm:ss');
                                                          
                                                }break; //; var leaveEnd2 = l2.add(60,'minutes').add(4,'hours');}break;
                                                default: {var leaveEnd2 = moment(timeend_old2);leaveTo = leaveEnd2.format('YYYY-MM-D HH:mm:ss');}break; //wholeday
                                              }
                                              
                                              //console.log(coveredshifts2);
                                              console.log('Leave from: '+ leaveFrom);
                                              console.log('Until: '+ leaveTo);

                                        }



                                        

                                      }
                                      

                                  }//end else checkIfRestday

                                  $('input[name="leaveFrom"]').val(leaveFrom);
                                  $('input[name="leaveTo"]').val(leaveTo);

                                  console.log("Do ajax");

                                  $.ajax({
                                        url: "{{action('UserVLController@requestVL')}}",
                                        type:'POST',
                                        data:{ 
                                          'id': user_id,
                                          'leaveFrom': leaveFrom,
                                          'leaveTo': leaveTo,
                                          'reason_vl': reason_vl,
                                          'totalcredits': totalcredits,
                                          'halfdayFrom': $('input[name="coveredshift"]:checked').val(),
                                          'halfdayTo': $('input[name="coveredshift2"]:checked').val(),
                                          '_token':_token
                                        },
                                        success: function(response){
                                          
                                         

                                          if (response.success == '1')
                                            $.notify("Vacation Leave saved successfully.",{className:"success",globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );
                                            else
                                              $.notify("Vacation Leave submitted for approval.",{className:"success", globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );
                                          
                                          console.log(response);
                                          $('#calendar').fullCalendar( 'refetchEvents' );
                                          $('.modal.fade').modal('hide');
                                        }
                                      });

                            //}//end if checkrestday

                            

                           
                                


                            
                          }break;


            */
            }//end switch

        @endif

          


     });
     
      $(".coverphoto").CoverPhoto({
        @if ($user->hascoverphoto !== null)
             <?php $cover = URL::to('/') . "/storage/uploads/cover-".$user->id."_".$user->hascoverphoto.".png";// URL::asset("public/img/cover/".$user->id.".jpg"); ?>
            currentImage:null, //"{{$cover}}",
        
        @else
            currentImage: "{{URL::asset('public/img/newcover.jpg')}}",
        @endif

        @if ($user->id == Auth::user()->id) 
          editable: true 
        @else 
          editable: false 

        @endif

      });

       $(".coverphoto").bind('cancelEditButton', function(evt) {
        window.location = "{{action('UserController@show', $user->id)}}";
       });

      $(".coverphoto").bind('coverPhotoUpdated', function(evt, dataUrl) {
        $(".output").empty();
        $("<img>").attr("src", dataUrl).appendTo(".output");
        var _token = "{{ csrf_token() }}";

         $.ajax({
                url:"{{action('UserController@updateCoverPhoto')}}",
                type:'POST',
                data:{

                  'data': dataUrl,
                 '_token':_token
                },

               
                success: function(response)
                {
                  
                        console.log(response);
                        
                      var htmcode = "<span class=\"success text-right\"> <i class=\"fa fa-save\"></i> Cover photo updated. <br /><br/>";
                     
                      
                      $('#alert-submit').addClass('alert alert-success').fadeIn().html(htmcode).delay(2000).fadeOut(function()
                        { 
                          window.location = "{{action('UserController@show', $user->id)}}";
                        }); 

                     

                  return false;

   

                }
              });
        console.log(dataUrl);
      });

      function computeCredits(vl_from,vl_to,shift_from,shift_to)
      {
        var _token = "{{ csrf_token() }}";
        var user_id = $("#save").attr('data-userid');

        var date_from = moment(vl_from,"MM/DD/YYYY").format('YYYY-MM-DD');
        //var date_ctr = moment(vl_from,"MM/DD/YYYY");
        
        if (vl_to !== null)
          var date_to = moment(vl_to,"MM/DD/YYYY").format('YYYY-MM-DD');
        else date_to = null;

        var totalcredits = 0;

        $.ajax({
                  url: "{{action('UserVLController@getCredits')}}",
                  type:'POST',
                  data:{ 
                   'date_from': date_from, // $('#vl_from').val(),
                   'date_to': date_to,
                   'user_id': user_id,
                   'shift_from': shift_from,
                   'shift_to': shift_to, 
                    '_token':_token
                  },
                  success: function(response){
                    console.log(response);
                    
                    $("span#credits_vl").html(response.credits); 
                    $("span#credits_vl").attr('data-credits', response.credits);
                  }
                });

        return false;

      }


      function checkIfRestday(vl_day, reason_vl) //(timestart_old1,timeend_old1,vl_from,coveredshift)
      {      


        var _token = "{{ csrf_token() }}";
        var rd = null;
                                 $.ajax({
                                    url: "{{action('UserController@getWorkSchedForTheDay',$user->id)}}",
                                    type:'POST',
                                    data:{ 
                                     'vl_day': vl_day, // $('#vl_from').val(), 
                                      '_token':_token
                                    },
                                    success: function(response){
                                      console.log(response.start);
                                      console.log(response.end);
                                      if (response.start === response.end){
                                        console.log("equal");
                                        alert("Actually, no need to file for leave. Selected date is your REST DAY!"); return false;
                                      }else {

                                        if (reason_vl == ""){  alert("Please indicate reason of your leave."); return false; }
                                        else return true;

                                      }
                                      
                                    }
                                  });

        

      }


      function getCoveredShifts(coveredshift, leave_from, timestart_old, timeend_old)
      {
        switch(coveredshift)
            {
              case '1': {  //WHOLE DAY 

                            var leaveStart = moment(leave_from+" "+timestart_old,"MM/D/YYYY h:m A");
                            var leaveEnd = moment(leave_from+" "+timeend_old,"MM/D/YYYY h:m A");

                        }break;

              case '2': { //1st half of shift
                            var leaveStart = moment(leave_from+" "+timestart_old,"MM/D/YYYY h:m A");
                            var leaveEnd =  moment(leave_from+" "+timestart_old,"MM/D/YYYY h:m A").add(4,'hours');


                        }break;
              case '3': { //2nd half
                            var leaveStart = moment(leave_from+" "+timestart_old,"MM/D/YYYY h:m A").add(5,'hours');
                            var leaveEnd =  moment(leave_from+" "+timeend_old,"MM/D/YYYY h:m A");

                            

                        }break;
            }

            var shifts = {leaveStart: leaveStart, leaveEnd: leaveEnd};
            return shifts;


      }//end function getCoveredShifts



    });
</script>

  


@stop