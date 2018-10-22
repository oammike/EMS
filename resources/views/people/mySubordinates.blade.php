@extends('layouts.main')

@section('metatags')
<title>My Subordinates | OAMPI Evaluation System</title>
@endsection

@section('content')


  <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        My Team
        
      </h1>
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
                        <h2 class="text-gray pull-right">Marketing</h2>
                        

                      </div><!--end box-header-->

                      <div class="box-body">
                        <table class="table no-margin table-bordered table-striped" id="manpower" style="background: rgba(256, 256, 256, 0.3)" ></table>
                      </div>
                      
                      
              </div><!--end box-primary-->


              <div class="box box-primary"  style="background: rgba(256, 256, 256, 0.4)">
                      <div class="box-header ">
                        
                      </div><!--end box-header-->
                      
                      <div class="box-body">
                        <?php $ctr=1; ?>


                        @foreach( $mySubordinates as $myMen)


                        <div class="col-xs-6 pull-left">

                          @if (count($myMen['subordinates']) > 0 )

                          <!-- ******** collapsible box ********** -->
                                                <div class="box collapsed-box">
                                                <div class="box-header with-border">
                                                      <!-- /.info-box -->
                                                      
                                                      @if ($myMen['isLeader'])
                                                      <div class="info-box bg-gray">
                                                      @else
                                                      <div class="info-box bg-white">
                                                        @endif

                                                         <a href="{{action('UserController@show', $myMen['id'])}} ">
                                                          <span class="info-box-icon">
                                                         

                                                      @if ( file_exists('public/img/employees/'.$myMen['id'].'.jpg') )
                                                        <img src={{asset('public/img/employees/'.$myMen['id'].'.jpg')}} class="img-circle pull-left" alt="User Image" width="95%" style="margin-left:2px;margin-top:2px">
                                                        @else
                                                          <img src="{{asset('public/img/oam_favicon1-55027f4ev1_site_icon-32x32.png')}}" class="img-circle pull-left" alt="Employee Image"style="padding-right:5px">

                                                          @endif

                                                        </span></a>

                                                        <div class="info-box-content">
                                                            <span class="info-box-text"><strong>{{$myMen['lastname']}}, {{$myMen['firstname']}} </strong> </span>
                                                            <span class="info-box-number" style="font-weight:normal"><small>{{$myMen['position']}}</small> </span>

                                                            <div class="progress">
                                                               
                                                              <div class="progress-bar" style="width: {{ count($myMen['completedEvals'])/count($myMen['subordinates']) * 100 }}%"></div>
                                                             
                                                            </div>
                                                                <span class="progress-description">
                                                                 
                                                                 <strong>{{ count($myMen['completedEvals']) }}  / {{ count($myMen['subordinates'] )}}</strong> - Completed Evaluations
                                                                 
                                                                 
                                                                </span>

                                                        </div>
                                                        <!-- /.info-box-content -->


                                                      </div>
                                                      <!-- /.info-box -->
                                                      <a href="{{action('UserController@userRequests',$myMen['id'])}}" class="btn btn-xs btn-warning pull-right" style="margin-left:5px"><i class="fa fa-clipboard"></i>  Requests</a>
                                                      <a href="{{action('DTRController@show',$myMen['id'])}}" class="btn btn-xs btn-primary pull-right" style="margin-left:5px"><i class="fa fa-clock-o"></i> View DTR</a>
                                                      <a href="{{action('UserController@show', $myMen['id'])}}" class="btn btn-xs btn-success pull-right"  style="margin-left:5px"><i class="fa fa-user"></i> Profile </a>
                                                      <a href="{{action('MovementController@changePersonnel',$myMen['id'])}}" class="btn btn-xs btn-danger pull-right"><i class="fa fa-exchange"></i> Movement </a>
                                                      




                                                  <div class="box-tools pull-right">
                                                    <button type="button" class="btn btn-box-tool" data-widget="collapse" title="See Details"><i class="fa fa-plus"></i>
                                                    </button>
                                                  </div>
                                                  <!-- /.box-tools -->


                                                </div>
                                                <!-- /.box-header -->

                                                <div class="box-body">
                                                  @if (count($myMen['completedEvals']) !== 0)
                                                  <table class="table">
                                                    <tr>
                                                      <th>Employee</th>
                                                      <th>Rating</th>
                                                      <th>Date Evaluated</th>
                                                    </tr>

                                                  @foreach ($myMen['completedEvals'] as $eval) 
                                                  <tr>
                                                    <td>{{$eval->owner->lastname}}, {{$eval->owner->firstname}}  </td>
                                                    <td>{{$eval->overAllScore}}</td>
                                                    <td>{{date("M d, Y", strtotime($eval->updated_at)) }}</td>

                                                  </tr>

                                                  @endforeach

                                                </table>

                                                @else
                                                <p><em>No completed evaluations yet. </em></p>

                                                @endif

                                                 
                                                </div>
                                                <!-- /.box-body -->
                                              </div>
                          <!-- ******** end collapsible box ********** -->


                          @else

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
                                                      <a href="{{action('UserController@userRequests',$myMen['id'])}}" class="btn btn-xs btn-warning pull-right" style="margin-left:5px"><i class="fa fa-clipboard"></i>  Requests</a>
                                                       <a href="{{action('DTRController@show',$myMen['id'])}}" class="btn btn-xs btn-primary pull-right" style="margin-left:5px"><i class="fa fa-clock-o"></i> View DTR</a>
                                                      <a href="{{action('UserController@show', $myMen['id'])}}" class="btn btn-xs btn-success pull-right"  style="margin-left:5px"><i class="fa fa-user"></i> Profile </a>
                                                      <a href="{{action('MovementController@changePersonnel',$myMen['id'])}}" class="btn btn-xs btn-danger pull-right"><i class="fa fa-exchange"></i> Movement </a>
                                                      






                                                  


                                                </div>
                                                <!-- /.box-header -->

                                                <div class="box-body">

                                                 
                                                </div>
                                                <!-- /.box-body -->
                                              </div>
                          <!-- ******** end collapsible box ********** -->





                          @endif

                              



                               


                        </div><!--end col-xs-6 -->





                         



                        
                        

                        @endforeach
                      </div><!--end box-body-->
              </div><!--end box-primary-->

          </div><!--end main row-->
      </section>
          



@endsection


@section('footer-scripts')

<script>
  $.widget.bridge('uibutton', $.ui.button);
</script>



<!-- Page script -->
<script>
 

   

</script>
<!-- end Page script -->



@stop