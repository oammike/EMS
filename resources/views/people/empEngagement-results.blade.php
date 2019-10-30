@extends('layouts.main')

@section('metatags')
<title>Voting Results: {{$finalTally->first()['activity']}} | EMS</title>
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
              <p><br/><i class="fa fa-exclamation-circle"></i> 
                  Note: 
                        <em>All votes are tallied using point rank system for a fair voting system regardless of how big the voting population of each program/department is. <br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        Entries that have zero votes are not shown in the graph and tally board.</em></p>

               <!-- Bar chart -->
                <div class="box box-primary">
                  <div class="box-header with-border">
                    <i class="fa fa-bar-chart-o"></i>

                    <h3 class="box-title">Voting Results: <em><a href="{{action('EngagementController@show',$id)}}"> {{$finalTally->first()['activity']}}</a></em></h3>

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

                      <?php $c=1; ?>
                      @foreach($finalTally as $tally) 

                      <tr>
                        <td>{{$c}}. {{$tally['title']}} <br/>by: <span style="font-size: x-small;">{{$tally['firstname']}} {{$tally['lastname']}} - <strong>{{$tally['program']}}</strong> </span></td>
                        <td>{{$tally["grandTotal"]}}%</td>
                      </tr>
                      <?php $c++; ?>
                      @endforeach


                      
                    </table>
                    
                  </div>
                  <!-- /.box-body-->
                </div>
                <!-- /.box -->

                <br/><br/>


                <!-- Bar chart -->
                <div class="box box-primary">
                  <div class="box-header with-border">
                    <i class="fa fa-bar-chart-o"></i>

                    <h3 class="box-title">Voting Breakdown: <em><a href="{{action('EngagementController@show',$id)}}"> {{$finalTally->first()['activity']}}</a></em></h3>

                    <div class="box-tools pull-right">
                      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                      </button>
                      <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                    </div>
                  </div>
                  <div class="box-body">
                    <table id='votetally' class="table table-bordered table-hover" style="background-color: #fff; overflow-y: scroll;">
                      <thead>
                        <tr>
                        <th>#</th>
                        <th>Entry</th>
                        <th>By</th>
                        <th class="text-center">Overall</th>
                        @foreach($tallyProg as $prog)
                          <th class="text-center">{{$prog[0]['camp']}}</th>
                        @endforeach
                        <th class="text-center">Points</th>
                        <th class="text-center">Overall</th>
                      </tr>
                        
                      </thead>

                      <tbody>
                        <?php $x=1;?>
                        @foreach($tallyEntry->reverse() as $entry)
                        <tr>
                          <td>{{$x}}</td>
                          <td >
                            <?php $t = $finalTally->where('entryID',$entry[0]['entry']); ?>
                             {{$t->first()['title']}}
                           </td>
                          <td style="font-size: x-small;">
                            @if($t->first()['nickname'])
                            {{$t->first()['nickname']}} {{$t->first()['lastname']}} - 
                            @else
                            {{$t->first()['firstname']}} {{$t->first()['lastname']}} -
                            @endif

                            <em style="font-weight: bold;">{{$t->first()['program']}}</em>
                          </td>

                          <?php $g = $finalTally->where('entryID',$entry[0]['entry']); ?> 

                          <td class="text-center"> 

                            <strong>{{$g->first()['grandTotal']}} % </strong><br/>
                            
                          </td>

                          @foreach($tallyProg as $prog)
                           
                            
                            <td class="text-center">@foreach($prog as $p)

                              
                                @if($p['entry'] === $t->first()['entryID'] ) 
                                {{$p['points']}}
                                @endif


                            @endforeach</td>
                          

                          @endforeach

                           
                          <td class="text-center"> 
                           
                            
                            
                            <small><span style="text-decoration: underline;">&nbsp; {{$g->first()['totalPoints']}} &nbsp;</span><br/>{{$g->first()['maxpoints']}}</small> 
                          </td>

                          <td class="text-center"> 
                            
                            
                            <strong>{{$g->first()['grandTotal']}} % </strong><br/>
                            
                          </td>
                          
                        </tr>
                        <?php $x++; ?>
                        @endforeach
                        
                      </tbody>

                      <tfoot>
                        <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><strong>100.00%</strong></td>
                        @foreach($tallyProg as $prog)
                          <td class="text-center"><strong>{{ number_format($prog[0]['entries'],2) }}</strong></td>
                        @endforeach
                        <td class="text-center">
                          <small>({{$finalTally->first()['maxpoints']}}/{{$finalTally->first()['maxpoints']}})</small></td>
                        <td class="text-center"><strong>100.00 %</strong></td>

                      </tr>
                        
                      </tfoot>


                      
                      
                    </table>
                  </div>
                  <!-- /.box-body-->
                </div>
                <!-- /.box -->




                

                


             </div>

             


             

             

          </div><!--end main row-->
      </section>

     
 

    



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

   $('#votetally').DataTable({
      "scrollX": true,
      "scrollY": 500,
      "paging":false,
      "order": [[ 3, "desc" ]]
      });

   
/*
     * BAR CHART
     * ---------
     */

    var bar_data = {
      //data : [['January', 10], ['February', 8], ['March', 4], ['April', 13], ['May', 17], ['June', 9]],

      data: [<?php $c=1 ?>
              @foreach($finalTally as $tally) 
              
              ["<span class='text-success'><strong> Entry # {{$c}}:</strong></span><br/><strong style='font-size:x-small;'>({{$tally["grandTotal"]}}%)</strong> <br/><small style='color:#dedede'>[{{$tally["actualVotes"]}}]</small>",{{$tally["grandTotal"]}}],
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