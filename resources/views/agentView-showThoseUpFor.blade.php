@extends('layouts.main')

@section('metatags')
<title>Dashboard | OAMPI Evaluation System</title>
@endsection

@section('content')


  <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Dashboard
        <small>{{ $myCampaign }} </small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Dashboard</li>
      </ol>
    </section>

     <section class="content">



                 <!-- ******** THE DATATABLE ********** -->
          <div class="row">
             
              <div class="col-lg-2 col-sm-2  col-xs-12"></div>
                
              <div class="col-lg-8 col-sm-8  col-xs-12">
                 <h1 class="text-center" style="color:#C1C8D1"><br/><br/>Show me all of my :</h1>

                          {{ Form::open(['route' => 'evalForm.grabAllWhosUpFor', 'class'=>'', 'id'=> 'showAll' ]) }}
                <div class="col-lg-11"><select name="evalType_id" class="form-control pull-left">
                  <option value="0"> --  Select One -- </option>
                  @foreach ($evalTypes as $evalType)
                  <option value="{{$evalType->id}}"><?php if ($evalType->id==1 ) echo date('Y'); else if($evalType->id==2){ if( date('m')>=7 && date('m')<=12 )echo date('Y'); else echo date('Y')-1;  } ?> {{$evalType->name}}</option>
                  @endforeach
                </select>
                </div>
                <div class="col-lg-1">
                 {{Form::submit(' Go ', ['class'=>'btn btn-md btn-success', 'id'=>'showEvalBtn', 'style'=>"margin-bottom:20px;"])}}</div>

              </div>
              <div class="col-lg-2 col-sm-2  col-xs-12"></div>
              {{Form::close()}}
              <br/><br/><br/><hr/>
                      

          </div>

      

     
          <div class="row">

            

            <h4 class="text-center"> <small>Showing: </small><br/>{{$evalSetting->name}} <br /> <br/></h4>

            @if ($doneEval[$employee->id]['evalForm_id'] == null )

            <h3 class="text-center text-success"><br /><br />No entries found.</h3>

            @else

            
            <div class="col-lg-3 col-sm-6 col-xs-12">
               <!-- Widget: user widget style 1 -->
                          <div class="box box-widget widget-user-2">
                            <!-- Add the bg color to the header using any of the bg-* classes 
                            done & agent = gray
                            done & leader = darkgreen black
                            !done & leader = black
                            !done & agent = green

                          -->
                            <div class="widget-user-header  @if ($doneEval[$employee->id]['evaluated'] && ($employee->userType_id == '4') ) bg-gray @elseif ($doneEval[$employee->id]['evaluated'] && ($employee->userType_id !== '4') ) bg-black @elseif (!$doneEval[$employee->id]['evaluated'] && ($employee->userType_id !== '4') )bg-darkgreen  @else bg-green @endif">

                              <div class="widget-user-image">
                                 @if ( file_exists('public/img/employees/'.$employee->id.'.jpg') )
                                <img src="{{asset('public/img/employees/'.$employee->id.'.jpg')}}" class="img-circle" alt="User Image">
                                @else
                                  <img src="{{asset('public/img/oam_favicon1-55027f4ev1_site_icon-32x32.png')}}" class="img-circle" alt="Employee Image">

                                  @endif
                               
                                
                              </div>
                              <!-- /.widget-user-image -->
                              <h3 class="widget-user-username"> {{$employee->lastname }}, {{$employee->firstname}} </h3>
                              <h5 class="widget-user-desc"> {{$employee->position->name}} <br /><em>{{$employee->status->name}} </em></h5>
                             
                            </div>
                            <div class="box-footer no-padding">
                              
                              <ul class="nav nav-stacked">
                                <li><a href="#">Date Hired <span class="pull-right badge bg-gray">{{ date_format(date_create($employee->dateHired), "M d, Y")  }} </span></a></li>
                                <li><a href="#">Absences <span class="pull-right badge bg-red">N/A</span></a></li>
                                <li><a href="#">Tardiness <span class="pull-right badge bg-red">N/A</span></a></li>
                                <li><a href="#">Issued Disciplinary Actions <span class="pull-right badge bg-red">N/A</span></a></li>

                                

                                @if ($doneEval[$employee->id]['evaluated' ]&& $doneEval[$employee->id]['score'] != 0)
                                <li><a href="#">
                                  <span class="pull-left"><strong>Current Rating</strong> <br/>
                                    <small>{{$doneEval[$employee->id]['endPeriod'] }} </small></span><h3 class="pull-right text-danger">{{$doneEval[$employee->id]['score']}} %</h3></a></li>
                              

                                @else

                                <li><a href="#">
                                  <span class="pull-left">{{$evalSetting->name}} <br/>
                                    <small>
                                   
                                     Period covered: </small></span><h5 class="pull-right text-danger"> {{$doneEval[$employee->id]['startPeriod'] }} <br/>to<br/> {{$doneEval[$employee->id]['endPeriod'] }}</h5></a></li>
                              

                                @endif

                              </ul>
                                
                              
                               <p class="text-center"><a class="btn btn-md btn-default" href="{{action('EvalFormController@show',$doneEval[$employee->id]['evalForm_id']) }} "><i class="fa fa-check"></i> See Evaluation</a></p>
                              
                            <div class="clearfix"></div>


                            </div>
                          </div>
                          <!-- /.widget-user -->

            </div><!--end employee card-->
           

            @endif
          </div><!-- end row -->



       
     </section>
          



@endsection


@section('footer-scripts')

<script>
  $.widget.bridge('uibutton', $.ui.button);
</script>



<!-- Page script -->
<script>
 $('#myTeam').DataTable({
    "scrollX": false,
    //"iDisplayLength": 25,
    "responsive": true,
    "lengthMenu": [[5, 20, 50, -1], [5, 20, 50, "All"]],
    "aoColumns": [
                  { "bSortable": false },
                  {"width":200},
                  {"width":200},
                  {"width":400},
                  {"width":100},
                  {"width":100},
                  {"width":90},
                  { "bSortable": false },
    ] 
    
   
   });


  $(function () {
   'use strict';
   

   


      
      
   });

   

  /*
   * Custom Label formatter
   * ----------------------
   */
  function labelFormatter(label, series) {
    return '<div style="font-size:13px; text-align:center; padding:2px; color: #fff; font-weight: 600;">'
        + label
        + "<br>"
        + Math.round(series.percent) + "%</div>";
  }
</script>
<!-- end Page script -->

<script type="text/javascript" src="{{asset('public/js/dashboard.js')}}"></script>

@stop