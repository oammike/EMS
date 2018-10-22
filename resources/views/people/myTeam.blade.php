@extends('layouts.main')

@section('metatags')
<title>My Team | OAMPI Evaluation System</title>
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
                        

                      </div><!--end box-header-->

                      <div class="box-body">
                        <table class="table no-margin table-bordered table-striped" id="manpower" style="background: rgba(256, 256, 256, 0.3)" >
                          <thead><tr>
                            <th></th>
                            <th>Lastname</th>
                            <th>Firstname</th>
                            <th>Title</th>
                            <th>Program / Dept.</th>
                            <th class="text-center" style="width: 45%">Actions</th>
                          </tr></thead>

                          <tbody>

                          @foreach($allTeams as $pip)
                          <tr>
                            <td><a href="{{action('UserController@show',$pip['id'])}}"> <img src="{{$pip['pic']}}" width="40" class="user-img" /></a></td>
                            <td><a href="{{action('UserController@show',$pip['id'])}}"> {{$pip['lastname'] }}</a></td>
                            <td><a href="{{action('UserController@show',$pip['id'])}}"> {{$pip['firstname']}} @if(!is_null($pip['nickname'])) <br/><strong style="font-size: x-small;"><em>({{$pip['nickname']}} )</em></strong> @endif </a></td>
                            <td><small>{{$pip['position']}}</small> </td>
                            <td>{{$pip['program']}} </td>
                            <td class="text-center">

                              @if ($pip['anApprover'] )
                               <a target="_blank" href="{{action('MovementController@changePersonnel',$pip['id'])}}" class="btn btn-xs btn-default" style="margin:2px"><i class="fa fa-exchange"></i> Movement </a>
                               @endif

                               @if ($pip['anApprover'] || $canUpdateLeaves )

                                @if(Auth::user()->id == $pip['id'])
                                <a href="{{action('UserController@myRequests',$pip['id'])}}" class="btn btn-xs btn-default" style="margin:2px"><i class="fa fa-clipboard"></i>  Requests</a>

                                @else
                                <a  target="_blank" href="{{action('UserController@userRequests',$pip['id'])}}" class="btn btn-xs btn-default" style="margin:2px"><i class="fa fa-clipboard"></i>  Requests</a>

                                @endif
                              
                              <a  target="_blank" href="{{action('DTRController@show',$pip['id'])}}" class="btn btn-xs btn-default" style="margin:2px"><i class="fa fa-calendar-o"></i>  DTR</a>
                              @endif

                              @if ($canUpdateLeaves)
                              <a  target="_blank" href="{{action('UserVLController@showCredits',$pip['id'])}}" class="btn btn-xs btn-default" style="margin:2px"><i class="fa fa-bar-chart"></i>  Leave Credits</a>
                              @endif


                              @if($pip['anApprover'])
                              <a  target="_blank" href="{{action('UserController@show',$pip['id'])}}#ws" class="btn btn-xs btn-default" style="margin:2px"><i class="fa fa-calendar-plus-o"></i>  Plot Sched</a>
                              @endif
                             

                              @if (!$pip['anApprover'] && !$canUpdateLeaves)
                              <a  target="_blank" href="{{action('UserController@show',$pip['id'])}}" class="btn btn-xs btn-default"><i class="fa fa-address-card-o"></i> View Profile </a>


                              @endif
                              

                            </td>
                          </tr>

                          @endforeach
                          </tbody>

                        </table>
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
                                                            <span class="info-box-text"><strong>{{$myMen['lastname']}}, {{$myMen['firstname']}} </strong> </span>
                                                            <span class="info-box-number" style="font-weight:normal"><small>{{$myMen['position']}}</small> </span>

                                                            <div class="progress">
                                                             
                                                            </div>
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
                                                      <a href="{{action('UserController@show',$myMen['id'])}}" class="btn btn-xs bg-purple"><i class="fa fa-address-card-o"></i> View Profile </a>
                                                      <a href="{{action('MovementController@changePersonnel',$myMen['id'])}}" class="btn btn-xs btn-warning pull-right" style="margin:2px"><i class="fa fa-exchange"></i> Movement </a>

                                                      <a href="{{action('UserController@userRequests',$myMen['id'])}}" class="btn btn-xs btn-success pull-right" style="margin:2px"><i class="fa fa-clipboard"></i>  Requests</a>
                                                      <a href="{{action('DTRController@show',$myMen['id'])}}" class="btn btn-xs btn-primary pull-right" style="margin:2px"><i class="fa fa-calendar-o"></i>  DTR</a>
                                                      <a href="{{action('UserController@show',$myMen['id'])}}#ws" class="btn btn-xs btn-danger pull-right" style="margin:2px"><i class="fa fa-calendar-plus-o"></i>  Plot Sched</a>
                                                      






                                                  


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

   $("#manpower").DataTable({
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
   
       
        
      
      
   });

   

</script>
<!-- end Page script -->



@stop