@extends('layouts.main')

@section('metatags')


<title>{{$tracker->name}} | EMS</title>
@endsection

@section('content')


  <!-- Content Header (Page header) -->
    <section class="content-header">
       <h1 class="pull-left">
        @if(is_null($logo))
          
          {{$program->name}}

        @elseif ($program->name=='IMO')
        <img src="../public/img/{{$logo[0]->filename}}" height="55" class="pull-left"  />


        @else
        <img src="./public/img/{{$logo[0]->filename}}" width="150px" class="pull-left" style="margin-top: 20px" />
        @endif
        
      </h1>
      <h2 class="pull-right" style="padding-top: 20px">{{$tracker->name}} </h2>
      


      <ol class="breadcrumb">
        <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="{{action('CampaignController@index')}}"> All Programs</a></li>
        <li class="active">{{$program->name}} Widgets</li>
      </ol>
    </section>

     <section class="content">
      
          <div class="row">
            <div class="col-sm-12">

                <div style="width: 10%" class="pull-left">
                  <label class="pull-left">From: </label><input type="text" id="from" class="datepicker form-control pull-left" placeholder="{{$todayStart->format('m/d/Y')}}" value="{{$todayStart->format('m/d/Y')}}" />
                  
                </div>
                <div style="width: 10%" class="pull-left">
                  
                  <label class="pull-left">To: </label><input type="text" id="to" class="datepicker form-control pull-left" value="{{$todayEnd->format('m/d/Y')}}" placeholder="{{$todayEnd->format('m/d/Y')}}" />
                </div>
                  

                  <a id="download" class="btn btn-success btn-sm pull-left" style="margin-top: 27px; margin-left: 5px">  
                  <?php //href="{{route('formSubmissions.rawData',['id'=>$form->id, 'from'=>$start,'to'=>$end,'page'=>$actualSubmissions->currentPage(),'dl'=>1])}}">
                  //href="formSubmissions/rawData/{{$form->id}}?from={{$start}}&to={{$end}}&page={{$actualSubmissions->currentPage()}}&dl=1"> ?>
                  <i class="fa fa-download"></i> Download Spreadsheet</a>


                  <div class="pull-right" style="width: 40%; margin-right: 10px;">
                     <label class="pull-left">Show All Tasks from: </label><input type="text" id="showfrom" name="showfrom" class="datepicker form-control pull-left" placeholder="{{$todayStart->format('m/d/Y')}}" value="{{$todayStart->format('m/d/Y')}}" style="width:20%;margin: 0 10px" />
                     <a class="btn btn-primary btn-sm" id="updatetable"><i class="fa fa-refresh"></i> Update Table</a>
                  </div>
                </div>
          </div>

          <!-- ******** THE DATATABLE ********** -->
          <div class="row">
             <div class="col-lg-12">



                
                <div class="nav-tabs-custom" style="background: rgba(256, 256, 256, 0.1); margin-top: 30px">
                          

                          <div class="tab-content" style="background: rgba(256, 256, 256, 0.7)">

                            <!-- **** ALL VERIFIED -->
                            <div class="tab-pane active" id="tab_verified"> 

                              <table class="table table-hover" id="table_verified">
                                <thead>
                                  <th>Date</th>
                                  
                                  <th>First name</th>
                                  <th>Last name</th>
                                  <th>Group</th>
                                  <th>Task</th>
                                  <th>Start</th>
                                  
                                  <th class="text-center">Breaks <br/>(mins)</th>
                                  <th>End</th>
                                  <th class="text-center">Task Duration <br/>(mins)</th>
                                  
                                 
                                        
                                 
                                </thead>
                                <tbody>
                                  @foreach($allTasks as $t)
                                  <tr>
                                    <td>{{$t->created_at}} </td>
                                    <td>{{$t->firstname}} </td>
                                    <td>{{$t->lastname}} </td>
                                    <td>{{$t->taskGroup}} </td>
                                    <td>{{$t->task}} </td>
                                    <td>{{$t->timeStart}} </td>

                                    <?php $break=collect($breaks)->where('taskID',$t->taskID); 
                                          $totalBreak = 0;
                                          foreach($break as $b){
                                            $totalBreak += $b['minuteBreaks'];
                                          }?>
                                    <td class="text-center">{{$totalBreak}} </td>
                                    <td>{{$t->timeEnd}} </td>

                                    <?php $duration = \Carbon\Carbon::parse($t->timeEnd,'Asia/Manila')->diffInMinutes(\Carbon\Carbon::parse($t->timeStart,'Asia/Manila')) - $totalBreak; ?>
                                    <td class="text-center" style="font-weight: bolder">{{$duration}} </td>
                                    
                                  </tr>

                                  @endforeach
                                  
                                  
                                </tbody>
                              </table>
                             
                            </div>

                            

                          </div>
                          <!-- /.tab-content -->
                </div>
                <!-- nav-tabs-custom -->

             </div><!--end col -->
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

    $( ".datepicker" ).datepicker();

    $('#updatetable').on('click',function(){
     
      var showfrom = $('#showfrom').val();

      @if($onlymine)

      window.location = "myTasks?program={{$program->id}}&showfrom="+showfrom;

      @else

      window.location = "allTasks?program={{$program->id}}&showfrom="+showfrom; 

      @endif
      

    });

    $('#download').on('click',function(){

      var from = $('#from').val();
      var to = $('#to').val();

      if (moment(from) <= moment(to))
        window.location = 'downloadTasks?program={{$program->id}}&from='+from+'&to='+to+'&page=$actualSubmissions->currentPage()&dl=1';
      else{
        $.notify("Invalid dates. Please check selected dates and try again.",{className:"error",globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} ); return false;
      }


    });

  
   
   $('#table_review,#table_issues,#table_verified').DataTable({
      "scrollX": true,
      
      "paging":true,
      "order": [[ 0, "desc" ]]
      });

  
      
   });

   

</script>
<!-- end Page script -->



@stop