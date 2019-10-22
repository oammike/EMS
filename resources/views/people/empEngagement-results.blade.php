@extends('layouts.main')

@section('metatags')
<title>Voting Results | EMS</title>
@endsection

@section('content')


  <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>&nbsp;</h1>
      <ol class="breadcrumb">
        <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="{{action('EngagementController@show',$id)}}"> Contests</a></li>
        <li class="active">Voting Results</li>
      </ol>
    </section>

     <section class="content">
      <!-- ******** THE DATATABLE ********** -->
          <div class="row">
             <div class="col-lg-12">

              <div class="box box-primary"  style="background: rgba(256, 256, 256, 0.4);padding:30px">

                <!-- Bar chart -->
                <div class="box box-primary">
                  <div class="box-header with-border">
                    <i class="fa fa-bar-chart-o"></i>

                    <h3 class="box-title">Voting Results</h3>

                    <div class="box-tools pull-right">
                      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                      </button>
                      <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                    </div>
                  </div>
                  <div class="box-body">
                    <div id="bar-chart" style="height: 300px;"></div>
                  </div>
                  <!-- /.box-body-->
                </div>
                <!-- /.box -->

                <p><i class="fa fa-exclamation-circle"></i> Note: All votes are tallied using point rank system to allow a fair voting system regardless of how big the voting population of each program/department.</p>
                
                
                





                



              </div><!--end box-primary-->


             

             

          </div><!--end main row-->
      </section>

      <div class="modal fade" id="warning" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title text-danger" id="myModalLabel"><i class="fa fa-exclamation-triangle"></i> Disclaimer</h4>
              
            </div>
            <div class="modal-body">
              <p>Understand that some of these stories may contain sensitive topics and themes that some people may find triggering.</p>
              <p>Proceeding means that you understand the potential risks. Each story includes a trigger warning to give you caution before you start reading.</p>
            </div>
            <div class="modal-footer no-border">
              {{ Form::open(['route' => ['employeeEngagement.voteNow',$id], 'method'=>'GET','class'=>'btn-outline pull-right', 'id'=> "show" ]) }} 

                <button type="submit" class="btn btn-primary"><i class="fa fa-check"></i> Proceed </button>
              
              <button type="button" class="btn btn-default" data-dismiss="modal">No, thank you.</button>{{ Form::close() }}
            </div>
          </div>
        </div>
      </div>
      

      
 

    



@endsection


@section('footer-scripts')
<link href="{{ asset( 'public/js/bootstrap-daterangepicker/daterangepicker.css' ) }}" rel="stylesheet">
<script type="text/javascript" src="{{ asset( 'public/js/chartjs/Chart.min.js' ) }}"></script>
<script type="text/javascript" src="{{ asset( 'public/js/bootstrap-daterangepicker/daterangepicker.js' ) }}"></script>


<!-- Morris.js charts -->
<script src="{{URL::asset('public/js/raphael.min.js')}}"></script>
<script src="{{URL::asset('public/js/morris.min.js')}}"></script>


<script>
  $.widget.bridge('uibutton', $.ui.button);
</script>



<!-- Page script -->
<script>


 
  $(function () {
   'use strict';

   
/*
     * BAR CHART
     * ---------
     */

    var bar_data = {
      //data : [['January', 10], ['February', 8], ['March', 4], ['April', 13], ['May', 17], ['June', 9]],

      data: [
              @foreach($finalTally as $tally) 

              ["Entry: <br/><span class='text-success'>{{$tally['title']}}</span><br/><strong>({{$tally["grandTotal"]}}%)</strong>",{{$tally["grandTotal"]}}],
              @endforeach

      ],
      color: '#3c8dbc'
    }
    $.plot('#bar-chart', [bar_data], {
      grid  : {
        borderWidth: 1,
        borderColor: '#f3f3f3',
        tickColor  : '#f3f3f3'
      },
      series: {
        bars: {
          show    : true,
          barWidth: 0.5,
          align   : 'center'
        }
      },
      xaxis : {
        mode      : 'categories',
        tickLength: 0
      }
    })
    /* END BAR CHART */
   

  

  
       
        
      
      
   });

   

</script>
<!-- end Page script -->



@stop