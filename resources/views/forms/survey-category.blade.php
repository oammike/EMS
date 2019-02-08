@extends('layouts.main')

@section('metatags')
<title>Survey Reports | Open Access EMS</title>
<link rel="stylesheet" type="text/css" href="{{URL::asset('public/js/formBuilder/demo.css')}}">
@endsection

@section('content')


  <!-- Content Header (Page header) -->
    <section class="content-header">
      <h4><i class="fa fa-file"></i> Survey Data </h4>
      <ol class="breadcrumb">
        <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="{{action('SurveyController@index')}}"><i class="fa fa-question"></i> Surveys</a></li>
        <li class="active">Survey Data</li>
      </ol>
    </section>

     <section class="content">
          <div class="row">
            <div class="col-lg-1"></div>
            <div class="col-lg-10">

              <div class="box">
                  <div class="box-header with-border">
                    <h3 class="box-title">{{$category->label}}</h3>


                    <div class="box-tools pull-right">
                      <a class="btn btn-xs btn-default" style="margin-right: 5px" href="{{action('SurveyController@report','1')}}"><i class="fa fa-arrow-left"></i> Back to Summary</a>
                      <a class="btn btn-xs btn-default" style="margin-right: 35px"><i class="fa fa-download"></i> Download Raw Data</a>
                      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                      </button>
                      <div class="btn-group">
                        <button type="button" class="btn btn-box-tool dropdown-toggle" data-toggle="dropdown">
                          <i class="fa fa-wrench"></i></button>
                        <ul class="dropdown-menu" role="menu">
                          <li><a href="#">Action</a></li>
                          <li><a href="#">Another action</a></li>
                          <li><a href="#">Something else here</a></li>
                          <li class="divider"></li>
                          <li><a href="#">Separated link</a></li>
                        </ul>

                      </div> 
                      <!-- <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button> -->
                    </div>
                  </div>
                  <!-- /.box-header -->
                  <div class="box-body">
                    <div class="row">
                      <div class="col-md-12">
                        <h5 class="text-center">Data as of: <span class="text-primary">{{ $asOf }}</span> </h5>
                        <p class="text-center">
                          <strong>Survey Rating Responses</strong>
                        </p>
                        <div class="chart">
                          <canvas id="barChart" style="height:330px"></canvas>
                        </div>

                        <!-- BAR CHART -->
                       
                        <!-- /.chart-responsive -->
                      </div>
                     
                    </div>
                    <!-- /.row -->
                  </div>
                  

                
              </div><!--end box -->
             

             <!-- ./box-body -->
                  <div class="box-footer">
                    <h4 class="text-center" style="padding:20px">Survey Questions</h4>
                    <div class="row">

                      <div class="col-lg-12" style="background-color: #000;min-height:670px; overflow: hidden;">

                        <?php $counter = 0 ?>
                        @foreach($chartData->sortBy('questionID') as $cd)



                          @if ($counter == 0)
                           <a class="nxt question{{$counter}} btn btn-default btn-md" data-totalc='{{count($chartData)}}'data-curr="{{$counter}}" style="z-index: 999; position: absolute;right: 0">
                          Next <i class="fa fa-arrow-right"></i> </a> 
                            <a id="prev0" class="prev question{{$counter}} btn btn-default btn-md pull-right" data-totalc='{{count($chartData)}}' data-curr="{{$counter}}" style="z-index: 999; position: absolute;right: 80px; display: none">
                              <i class="fa fa-arrow-left"></i> Prev </a> 

                        <img class="question{{$counter}}" src="../../storage/uploads/{{$cd['bg']}}" style="filter: alpha(opacity=30); opacity: 0.3; position: absolute; top:16px; left:6px;" width="99%" />

                          @else

                            @if($counter == count($chartData)-1)


                            <a class="prev question{{$counter}} btn btn-default btn-md pull-right" data-totalc='{{count($chartData)}}' data-curr="{{$counter}}" style="z-index: 999; position: absolute;display: none; right: 80px">
                              <i class="fa fa-arrow-left"></i> Prev </a> 

                            @else

                            <a class="nxt question{{$counter}} btn btn-default btn-md" data-totalc='{{count($chartData)}}' data-curr="{{$counter}}" style="z-index: 999; position: absolute;right: 0;display: none">
                          Next <i class="fa fa-arrow-right"></i> </a> 
                            <a class="prev question{{$counter}} btn btn-default btn-md pull-right" data-totalc='{{count($chartData)}}' data-curr="{{$counter}}" style="z-index: 999; position: absolute;display: none; right: 80px">
                              <i class="fa fa-arrow-left"></i> Prev </a> 

                            @endif

                            

                          <img class="question{{$counter}}" src="../../storage/uploads/{{$cd['bg']}}" style="filter: alpha(opacity=30); opacity: 0.3; position: absolute; top:16px; left:6px; display: none;" width="99%" />

                          @endif
                          
                        <div class="question{{$counter}}" style="width: 95%; position: absolute;top: 0px;" >
                          <div class="row">
                            <div class="col-lg-1">
                              @if ($counter == 0)
                              <div class="question{{$counter}}" style="background-color:{{$colors[$counter]}};padding:20px; min-height: 230px;writing-mode: vertical-rl; text-orientation: mixed;">{{$category->label}}</div>
                              @else
                              <div class="question{{$counter}}" style="background-color:{{$colors[$counter]}};padding:20px; min-height: 230px; display: none; writing-mode: vertical-rl; text-orientation: mixed;">{{$category->label}}</div>

                              @endif
                            </div>
                            <div class="col-lg-4">
                              
                              @if ($counter==0)
                              <h4 class="question{{$counter}}" style="color:#fff;margin-top: 50px; padding: 59px; background:rgba(0,0,0,0.5)">
                              @else
                              <h4 class="question{{$counter}}" style="color:#fff;margin-top: 50px; padding: 59px; background:rgba(0,0,0,0.5); display: none">
                              @endif

                                {{$cd['question']}}
                              </h4>

                            </div>

                            <div class="col-lg-7">

                              @if ($counter == 0)
                              <div class="comments{{$counter}}" style="background: rgba(255,255,255,0.8); max-height: 550px; overflow-y: scroll; padding:20px;margin-top:50px">

                              @else

                              <div class="comments{{$counter}}" style="background: rgba(255,255,255,0.8); max-height: 550px; overflow-y: scroll; padding:20px;margin-top:50px; display: none">

                              @endif

                                <h2>Comments <em style="font-size: 0.5em;">({{count($cd['notes'])}})</em></h2>
                                
                                <?php $ct=0 ?>
                                @foreach($cd['notes'] as $n)

                                  <p style="color:#000; margin-bottom: 20px; clear: both">

                                    <span style="font-size: smaller; " class="pull-right">{{ date('M d, Y h:i A', strtotime($n->created_at))}} </span><br/>
                                    <em> {{$n->comments}}</em> <br/>
                                    <span style="color:#b78a01">
                                    @for ($i = 1; $i <= $cd['ratings'][$ct]; $i++)
                                    <i class="fa fa-star"></i>
                                    @endfor

                                    @for ($c = 5; $c > $cd['ratings'][$ct]; $c-- ) 
                                    <i class="fa fa-star-o"></i>
                                    @endfor
                                    </span>


                                    <?php  $tenure = \Carbon\Carbon::parse($n->dateHired,"Asia/Manila")->diffInMonths(); 

                                    if ($tenure < 13) $mos = "less than a year";
                                      else if ($tenure >=13 && $tenure <= 36) $mos = "1-3 years";
                                      else $mos = "3+ years"; ?>
                                    @if ($n->isBackoffice)

                                    <strong class="pull-right"> - Back Office <em style="font-size: smaller;">(been with Open Access for {{$mos}}  )</em> </strong>


                                    @else
                                    <strong class="pull-right"> - Operations <em style="font-size: smaller;">(been with Open Access for {{$mos}}  )</em> </strong>

                                    @endif
                                    <div class="clearfix"><br/><br/></div>

                                  </p>
                                  <?php $ct++; ?>
                                @endforeach
                              </div>
                              

                            </div>
                          </div>
                                
                                <br/><br/>
                        </div>
                        <?php $counter++;?>
                        @endforeach

                        <br/><br/>

                      </div><!--end col-->

       
                      
                    </div>
                    <!-- /.row -->
                    
                  </div>
                  <!-- /.box-footer -->


              

              
              

            </div>
           <div class="col-lg-1"></div>


            

            

          </div><!-- end row -->





       
     </section>
          



@endsection


@section('footer-scripts')

<script>
  $.widget.bridge('uibutton', $.ui.button);
</script>



<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.3.1/jquery.rateyo.min.js"></script>
 -->


<!-- Page script -->
<link href="{{ asset( 'public/js/bootstrap-daterangepicker/daterangepicker.css' ) }}" rel="stylesheet">
<script type="text/javascript" src="{{ asset( 'public/js/chartjs/Chart.min.js' ) }}"></script>
<script type="text/javascript" src="{{ asset( 'public/js/bootstrap-daterangepicker/daterangepicker.js' ) }}"></script>


<!-- Morris.js charts -->
<script src="{{URL::asset('public/js/raphael.min.js')}}"></script>
<script src="{{URL::asset('public/js/morris.min.js')}}"></script>


<!-- ChartJS -->
<script src="{{ asset( 'public/js/bower_components/chart.js/Chart.js' ) }}"></script>
<!-- FastClick -->
<script src="{{ asset( 'public/js/bower_components//fastclick/lib/fastclick.js' ) }}"></script>



<script>

$(function () {

  'use strict';

  
  var areaChartData = {
      labels  : ['(1s)', '(2s)', '(3s)', '(4s)', '(5s)'],
      datasets: [
<?php $ctr=0; ?>
      @foreach($chartData as $c)
      
        {
          label               : "{{$c['question']}}",
          fillColor           : '{{$colors[$ctr]}}',
          strokeColor         : '#fff',
          pointHighlightFill  : '#fff',
          data                : [ "{{$c['1s']}}", "{{$c['2s']}} ", "{{$c['3s']}}" , "{{$c['4s']}}", "{{$c['5s']}}" ]
        },

        <?php $ctr++; ?>
      @endforeach
        
      ]
    }

  
    //-------------
    //- BAR CHART -
    //-------------
    var barChartCanvas                   = $('#barChart').get(0).getContext('2d')
    var barChart                         = new Chart(barChartCanvas)
    var barChartData                     = areaChartData
    //barChartData.datasets[1].fillColor   = '#00a65a'
    //barChartData.datasets[1].strokeColor = '#00a65a'
    //barChartData.datasets[1].pointColor  = '#00a65a'
    var barChartOptions                  = {
      //Boolean - Whether the scale should start at zero, or an order of magnitude down from the lowest value
      scaleBeginAtZero        : true,
      //Boolean - Whether grid lines are shown across the chart
      scaleShowGridLines      : true,
      //String - Colour of the grid lines
      scaleGridLineColor      : 'rgba(0,0,0,.05)',
      //Number - Width of the grid lines
      scaleGridLineWidth      : 1,
      //Boolean - Whether to show horizontal lines (except X axis)
      scaleShowHorizontalLines: true,
      //Boolean - Whether to show vertical lines (except Y axis)
      scaleShowVerticalLines  : true,
      //Boolean - If there is a stroke on each bar
      barShowStroke           : true,
      //Number - Pixel width of the bar stroke
      barStrokeWidth          : 2,
      //Number - Spacing between each of the X value sets
      barValueSpacing         : 5,
      //Number - Spacing between data sets within X values
      barDatasetSpacing       : 1,
      //String - A legend template
      legendTemplate          : '<i class="fa fa-check"></i> <ul class="<%=name.toLowerCase()%>-legend"><% for (var i=0; i<datasets.length; i++){%><li><span style="background-color:<%=datasets[i].fillColor%>"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>',
      //Boolean - whether to make the chart responsive
      responsive              : true,
      maintainAspectRatio     : true
    }

    barChartOptions.datasetFill = false
    barChart.Bar(barChartData, barChartOptions)

   
 

   });



$('a.nxt').on('click',function(){

    var cur = $(this).attr('data-curr');
    var cur1 = parseInt(cur)+1;
    var totalc = $(this).attr('data-totalc');

    if ( parseInt(totalc)-1 == cur1 ){
      console.log($('.nxt.question'+cur)); //.fadeOut();
      console.log("last");
    }
    

   

    $('.question'+cur).fadeOut();
    $('.question'+cur1).css("display","block");
    $('.comments'+cur1).css("display","block");


  });

$('a.prev').on('click',function(){

    var cur = $(this).attr('data-curr');
    var cur1 = parseInt(cur)-1;
    var totalc = $(this).attr('data-totalc');
    $('.question'+cur).fadeOut();
    $('.question'+cur1).css("display","block");
    $('.comments'+cur1).css("display","block");

    console.log(cur1);
    if (cur1 == '0') $('a#prev0').css("display","none");
      //$('a.prev.question0.btn.btn-default.btn-md.pull-right').css('display','none');

    


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


@stop