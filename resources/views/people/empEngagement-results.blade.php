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

                    <br/><br/><br/>
                    <table class="table table-hover">
                      <tr>
                        <th class="text-primary">Entries: </th>
                        <th>Votes (%)</th>
                      </tr>

                      @foreach($finalTally as $tally) 

                      <tr>
                        <td>{{$tally['title']}}</td>
                        <td>{{$tally["grandTotal"]}}%</td>
                      </tr>

                      @endforeach


                      
                    </table>
                    
                  </div>
                  <!-- /.box-body-->
                </div>
                <!-- /.box -->

                <p><i class="fa fa-exclamation-circle"></i> Note: All votes are tallied using point rank system to allow a fair voting system regardless of how big the voting population of each program/department.</p><br/><br/>

                <h3>Voting Breakdown:</h3>

                <table class="table table-bordered table-hover" style="background-color: #fff; overflow-y: scroll;">
                  <tr>
                    <th>Entry</th>
                    @foreach($tallyProg as $prog)
                      <th class="text-center">{{$prog[0]['camp']}}</th>
                    @endforeach
                    <th class="text-center">Total</th>
                  </tr>

                  @foreach($tallyEntry->reverse() as $entry)
                  <tr>
                    <td >
                      <?php $t = $finalTally->where('entryID',$entry[0]['entry']); ?>
                      {{$t->first()['title']}}
                     </td>
                    @foreach($tallyProg as $prog)
                      
                      <td class="text-center">@foreach($prog as $p)

                        
                          @if($p['entry'] === $t->first()['entryID'] ) 
                          {{$p['points']}}
                          @endif


                      @endforeach</td>
                    

                    @endforeach
                    <td class="text-center"> 
                      <?php $g = $finalTally->where('entryID',$entry[0]['entry']); ?> 
                      <small>({{$g->first()['totalPoints']}}/{{$g->first()['maxpoints']}})</small> <br/>
                      <strong>{{$g->first()['grandTotal']}} % </strong>
                    </td>
                    
                  </tr>
                  @endforeach
                  <tr>
                    <td></td>
                    @foreach($tallyProg as $prog)
                      <td class="text-center"><br/><strong>{{ number_format($prog[0]['entries'],2) }}</strong></td>
                    @endforeach
                    <td class="text-center">
                      <small>({{$finalTally->first()['maxpoints']}}/{{$finalTally->first()['maxpoints']}})</small><br/>
                    <strong>100.00 %</strong></td>
                  </tr>
                </table>
                
                
                





                



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

      data: [<?php $c=1 ?>
              @foreach($finalTally as $tally) 
              
              ["<span class='text-success'><strong> Entry # {{$c}}:</strong></span><br/><strong style='font-size:larger;'>({{$tally["grandTotal"]}}%)</strong> <br/> [{{$tally["actualVotes"]}}]",{{$tally["grandTotal"]}}],
              <?php $c++; ?>
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