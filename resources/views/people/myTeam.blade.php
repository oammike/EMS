@extends('layouts.main')

@section('metatags')
<title>My Team | EMS</title>
@endsection

@section('content')


  <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>&nbsp;</h1>
      <ol class="breadcrumb">
        <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="{{action('UserController@index')}}"> All Employees</a></li>
        <li class="active">My Team</li>
      </ol>
    </section>

     <section class="content">
      <!-- ******** THE DATATABLE ********** -->
          <div class="row">
             <div class="col-lg-12">
              <div class="box box-primary"  style="background: rgba(256, 256, 256, 0.4)">
                      <div class="box-header ">
                        <h4 class="text-blue pull-left"><i class="fa fa-users"></i><strong> Program / Department </strong> {{$campaigns}}</h4>
                        <a class="btn btn-md btn-success pull-right" target="_self" href="{{action('CampaignController@orgChart')}}"><i class="fa fa-sitemap"></i> View Org Chart</a>
                        

                      </div><!--end box-header-->

                      <div class="box-body">

                        @if (!is_null($leadershipcheck))
                         <!-- Custom Tabs -->
                        <div class="nav-tabs-custom">
                          <ul class="nav nav-tabs">
                           <?php $c=0; ?>
                           @foreach($allTeams as $pip)
                              @if ($c==0)
                              <li class="active"><a href="#tab_{{$pip[0]->programID}}" data-toggle="tab"><strong class="text-primary ">{{$pip[0]->program}} </strong></a></li>
                              @else
                              <li><a href="#tab_{{$pip[0]->programID}}" data-toggle="tab"><strong class="text-primary ">{{$pip[0]->program}} </strong></a></li>

                              @endif
                              <?php $c++;?>
                           @endforeach
                            
                           
                           
                            
                          </ul>

                          <div class="tab-content">

                            <?php $ctr = 0;?>

                            @foreach($allTeams as $pip)

                            <div class="tab-pane @if($ctr==0)active @endif" id="tab_{{$pip[0]->programID}}">
                              <div class="row" >
                                <div class="col-lg-12">

                                  <table class="table no-margin table-bordered table-striped" id="manpower_{{$pip[0]->programID}}" style="background: rgba(256, 256, 256, 0.3)" >
                                      <thead><tr>
                                        <th style="width:20px"></th>
                                        <th>Lastname</th>
                                        <th>Firstname</th>
                                        <th>Title</th>
                                        <th>Program / Dept.</th>
                                        <th class="text-center">Actions</th>
                                      </tr></thead>

                                      <tbody>

                                      
                                      
                                      @foreach($pip as $p)
                                      <tr>
                                        <td><a href="{{action('UserController@show',$p->id)}}"> 
                                          @if ( file_exists('public/img/employees/'.$p->id.'.jpg') )
                                          <img src="{{asset('public/img/employees/'.$p->id.'.jpg')}}" width="40" class="user-img" />
                                          @else
                                          <img src="{{asset('public/img/useravatar.png')}}" width="40" class="user-img" />
                                          @endif

                                        </a></td>
                                        <td><a href="{{action('UserController@show',$p->id)}}"> {{$p->lastname }}</a></td>
                                        <td><a href="{{action('UserController@show',$p->id)}}"> {{$p->firstname}} @if(!is_null($p->nickname)) <br/><strong style="font-size: x-small;"><em>({{$p->nickname}} )</em></strong> @endif </a></td>
                                        <td><small>{{$p->position}}</small> </td>
                                        <td>{{$p->program}} </td>
                                        <td class="text-center">

                                         
                                           <a target="_blank" href="{{action('MovementController@changePersonnel',$p->id)}}" class="btn btn-xs btn-default" style="margin:2px"><i class="fa fa-exchange"></i> Movement </a>

                                           <a  target="_blank" href="{{action('DTRController@show',$p->id)}}" class="btn btn-xs btn-default" style="margin:2px"><i class="fa fa-calendar-o"></i>  DTR</a>
                                          

                                           @if ( $canUpdateLeaves )

                                            @if(Auth::user()->id == $p->id)
                                            <a  target="_blank" href="{{action('UserController@myRequests',$p->id)}}" class="btn btn-xs btn-default" style="margin:2px"><i class="fa fa-clipboard"></i>  Requests</a>

                                            @else
                                            <a  target="_blank" href="{{action('UserController@userRequests',$p->id)}}" class="btn btn-xs btn-default" style="margin:2px"><i class="fa fa-clipboard"></i>  Requests</a>

                                            @endif
                                          
                                          
                                          @endif

                                          @if ($canUpdateLeaves)
                                          <a  target="_blank" href="{{action('UserVLController@showCredits',$p->id)}}" class="btn btn-xs btn-default" style="margin:2px"><i class="fa fa-bar-chart"></i>  Leave Credits</a>
                                          @endif


                                         @if ($p->isBackoffice)
                                          <a  target="_blank" href="{{action('UserController@createSchedule',$p->id)}}" class="btn btn-xs btn-default" style="margin:2px"><i class="fa fa-calendar-plus-o"></i>  Plot Sched</a>
                                          @endif
                                         

                                          @if ( !$canUpdateLeaves)
                                          <a  target="_blank" href="{{action('UserController@show',$p->id)}}" class="btn btn-xs btn-default"><i class="fa fa-address-card-o"></i> View Profile </a>


                                          @endif
                                          

                                        </td>
                                      </tr>
                                      @endforeach

                                      
                                      </tbody>

                                    </table>
                                  

                                </div>
                                
                              </div>
                              <!-- /.row -->
                              

                            </div><!--end pane1 -->
                            <!-- /.tab-pane -->

                            <?php $ctr++; ?>
                            @endforeach



                            
                            
                            
                           


                          </div>
                          <!-- /.tab-content -->
                        </div>
                        <!-- nav-tabs-custom -->
                        @else


                        <table class="table no-margin table-bordered table-striped" id="manpower_{{$allTeams[0]->programID}}" style="background: rgba(256, 256, 256, 0.3)" >
                                      <thead><tr>
                                        <th style="width:20px"></th>
                                        <th>Lastname</th>
                                        <th>Firstname</th>
                                        <th>Title</th>
                                        <th>Program / Dept.</th>
                                        <th class="text-center">Actions</th>
                                      </tr></thead>

                                      <tbody>

                                      
                                      
                                      @foreach($allTeams as $p)
                                      <tr>
                                        <td><a href="{{action('UserController@show',$p->id)}}"> 
                                          @if ( file_exists('public/img/employees/'.$p->id.'.jpg') )
                                          <img src="{{asset('public/img/employees/'.$p->id.'.jpg')}}" width="40" class="user-img" />
                                          @else
                                          <img src="{{asset('public/img/useravatar.png')}}" width="40" class="user-img" />
                                          @endif

                                        </a></td>
                                        <td><a href="{{action('UserController@show',$p->id)}}"> {{$p->lastname }}</a></td>
                                        <td><a href="{{action('UserController@show',$p->id)}}"> {{$p->firstname}} @if(!is_null($p->nickname)) <br/><strong style="font-size: x-small;"><em>({{$p->nickname}} )</em></strong> @endif </a></td>
                                        <td><small>{{$p->position}}</small> </td>
                                        <td>{{$p->program}} </td>
                                        <td class="text-center">

                                          <a  target="_blank" href="{{action('UserController@show',$p->id)}}" class="btn btn-xs btn-default"><i class="fa fa-address-card-o"></i> View Profile </a>



                                        </td>
                                      </tr>
                                      @endforeach

                                      
                                      </tbody>

                        </table>


                        @endif



                        
                      </div>
                      
                      
              </div><!--end box-primary-->


              @if (!empty($mySubordinates))
              <div class="box box-primary"  style="background: rgba(256, 256, 256, 0.4)">
                      <div class="box-header ">
                        <h2> My Team </h2>
                      </div><!--end box-header-->
                      
                      <div class="box-body">
                        <?php $ctr=1; ?>


                        @foreach( $mySubordinates as $myMen)


                        <div class="col-xs-6 pull-left">

                         
                            <!-- ******** collapsible box ********** -->
                                                <div class="box box-default collapsed-box">
                                                <div class="box-header with-border">
                                                      <!-- /.info-box -->
                                                      <div class="info-box bg-gray">
                                                        <span class="info-box-icon">
                                                          <a href="{{action('UserController@show', $myMen['id'])}} ">

                                                      @if ( file_exists('public/img/employees/'.$myMen['id'].'.jpg') )
                                                        <img src={{asset('public/img/employees/'.$myMen['id'].'.jpg')}} class="img-circle pull-left" alt="User Image" width="95%" style="margin-left:2px;margin-top:2px">
                                                        @else
                                                          <img src="{{asset('public/img/oam_favicon1-55027f4ev1_site_icon-32x32.png')}}" class="img-circle pull-left" alt="Employee Image"style="padding-right:5px">

                                                          @endif

                                                        </a>
                                                      </span>

                                                        <div class="info-box-content">
                                                          <div class="row">
                                                            <div class="col-lg-9">
                                                              <strong>{{$myMen['lastname']}}, {{$myMen['firstname']}} </strong><br/>
                                                              <small>{{$myMen['position']}}</small> 
                                                            </div>
                                                            <div class="col-lg-3">
                                                              @if ($myMen['logo'] != "white_logo_small.png")
                                                              <img src="{{asset('public/img/'.$myMen['logo'])}}" width="80%" />
                                                              @endif
                                                            </div>
                                                          </div>

                                                          <div class="progress"></div>
                                                                <span class="progress-description">
                                                                 
                                                                </span>

                                                        </div>
                                                        <!-- /.info-box-content -->
                                                      </div>
                                                      <!-- /.info-box -->
                                                      
                                                     
                                                      <!--<a href="{{action('UserController@show', $myMen['id'])}}" class="btn btn-xs btn-success pull-right"  style="margin-left:5px"><i class="fa fa-user"></i> Profile </a> 
                                                      <a href="{{action('UserController@userRequests',$myMen['id'])}}" class="btn btn-xs btn-warning pull-right" style="margin-left:5px"><i class="fa fa-clipboard"></i>  Requests</a>
                                                      <a href="{{action('DTRController@show',$myMen['id'])}}" class="btn btn-xs btn-primary pull-right" style="margin-left:5px"><i class="fa fa-clock-o"></i> View DTR</a>
                                                      <a href="{{action('MovementController@changePersonnel',$myMen['id'])}}" class="btn btn-xs btn-danger pull-right"><i class="fa fa-exchange"></i> Movement </a>-->
                                                      <a target="_blank" href="{{action('UserController@show',$myMen['id'])}}" class="btn btn-xs bg-purple"><i class="fa fa-address-card-o"></i> View Profile </a>
                                                      <a target="_blank"  href="{{action('MovementController@changePersonnel',$myMen['id'])}}" class="btn btn-xs btn-warning pull-right" style="margin:2px"><i class="fa fa-exchange"></i> Movement </a>

                                                      <a target="_blank"  href="{{action('UserController@userRequests',$myMen['id'])}}" class="btn btn-xs btn-success pull-right" style="margin:2px"><i class="fa fa-clipboard"></i>  Requests</a>
                                                      <a target="_blank"  href="{{action('DTRController@show',$myMen['id'])}}" class="btn btn-xs btn-primary pull-right" style="margin:2px"><i class="fa fa-calendar-o"></i>  DTR</a>
                                                      <a  target="_blank" href="{{action('UserController@createSchedule',$myMen['id'])}}" class="btn btn-xs btn-danger pull-right" style="margin:2px"><i class="fa fa-calendar-plus-o"></i>  Plot Sched</a>
                                                      






                                                  


                                                </div>
                                                <!-- /.box-header -->

                                                <div class="box-body">

                                                 
                                                </div>
                                                <!-- /.box-body -->
                                              </div>
                          <!-- ******** end collapsible box ********** -->






                              



                               


                        </div><!--end col-xs-6 -->





                         



                        
                        

                        @endforeach
                      </div><!--end box-body-->
              </div><!--end box-primary-->

              @endif

             

          </div><!--end main row-->
      </section>
          



@endsection


@section('footer-scripts')

<script>
  $.widget.bridge('uibutton', $.ui.button);
</script>



<!-- Page script -->
<script>
 
  $(function () {
   'use strict';

   @if (!is_null($leadershipcheck))
   @foreach($allTeams as $pip)
   $("#manpower_{{$pip[0]->programID}}").DataTable({
      "responsive":true,
      "lengthChange": true,
      "lengthMenu":[5, 10, 20,50],
      "pageLength": 7,
      //"scrollX":true,
      "stateSave": false,
      //"processing":true,
       "dom": '<"col-xs-1"f><"col-xs-11 text-right"l><"clearfix">rt<"bottom"ip><"clear">',
      "order": [[ 1, "asc" ]],
      
      "oLanguage": {
         "sSearch": "<small>To refine search, simply type-in</small><br> any values you want to look for:",
         "class": "pull-left"
       }
    });
   @endforeach
   @else
   $("#manpower_{{$allTeams[0]->programID}}").DataTable({
      "responsive":true,
      "lengthChange": true,
      "lengthMenu":[5, 10, 20,50],
      "pageLength": 7,
      //"scrollX":true,
      "stateSave": false,
      //"processing":true,
       "dom": '<"col-xs-1"f><"col-xs-11 text-right"l><"clearfix">rt<"bottom"ip><"clear">',
      "order": [[ 1, "asc" ]],
      
      "oLanguage": {
         "sSearch": "<small>To refine search, simply type-in</small><br> any values you want to look for:",
         "class": "pull-left"
       }
    });


   @endif
       
        
      
      
   });

   

</script>
<!-- end Page script -->



@stop