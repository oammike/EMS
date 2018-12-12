@extends('layouts.main')


@section('metatags')
  <title>{{ $campaign->name }} Stats</title>
    <meta name="description" content="{{ $campaign->name }} Stats">

@stop


@section('content')




<section class="content-header">

      <h1><i class="fa fa-file-o"></i> {{ $campaign->name }} Stats</h1>
      <a style="margin-top:30px; margin-left: 10px" class="btn btn-xs btn-default" href="{{ url('campaign/'.$campaign->id) }}"><i class="fa fa-arrow-left"></i> Back to Overview</a>
      
      <a style="margin-top:30px; margin-left: 10px" class="btn btn-xs btn-default" href="{{ url('agentStats/'.$campaign->id) }}">View Agent Stats</a>
            
      
      <ol class="breadcrumb">
        <li><a href="{{ action('HomeController@index') }}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="{{ action('CampaignController@index') }}">All Campaigns</a></li>
        <li><a href="{{ url('campaign/'.$campaign->id) }}">{{ $campaign->name }}</a></li>
        <li class="active">Stats</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      
      <!-- Schedule Adherence -->
      <div class="row">
        <div class="col-xs-12">
          <div class="box box-primary">
            <div class="box-header with-border">
              <i class="fa fa-bar-chart-o"></i>
              <h3 class="box-title">Work Hours Summary</h3>
              <div class="box-tools pull-right">
                <div class="btn-group" id="realtime" data-toggle="btn-toggle">
                  <button type="button" class="btn btn-default pull-right" id="daterange-btn1">
                    <span>
                      <i class="fa fa-calendar"></i> Date range
                    </span>
                    <i class="fa fa-caret-down"></i>
                  </button>
                </div>
                <div class="btn-group">
                  <button type="button" class="btn btn-default" id="bt_stats_export">
                    <span>
                      <i class="fa fa-download"></i> Export
                    </span>
                  </button>
                </div>
              </div>
            </div>
            <div class="box-body">
              <canvas id="chart_workhours" width="720" height="480" />
            </div><!-- /.box-body-->
          </div><!-- /.box -->

        </div><!-- /.col -->
      </div><!-- /.row -->
      
      <!-- Agent Stats -->
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
     

    </section>
    
    <div id="chartjs-tooltip"></div>
      
      <form action="{{ url('/exportAgentActivity') }}" id="get_agent_stats_form" method="POST">
      <input type="hidden" name="campaign_id" value="{{ $campaign->id }}" />
      <input type="hidden" name="_token" value="{{ csrf_token() }}"  />
      <input type="hidden" name="export" value="TRUE"  />
      <input type="hidden" name="start" value="" id="frm_start"  />
      <input type="hidden" name="end" value="" id="frm_end"  />
    </form>

@stop

@section('footer-scripts')
<link href="{{ asset( 'public/js/bootstrap-daterangepicker/daterangepicker.css' ) }}" rel="stylesheet">
<script type="text/javascript" src="{{ asset( 'public/js/chartjs/Chart.min.js' ) }}"></script>
<script type="text/javascript" src="{{ asset( 'public/js/bootstrap-daterangepicker/daterangepicker.js' ) }}"></script>
<script type="text/javascript">
  $(function () {
  
    
    window.start = moment().subtract(6, 'days');
    window.end = moment();
    window.campaign_id = {{ $campaign->id }};
    
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
    
    
$('#bt_stats_export').click(function(){
      $('#frm_start').val(window.start.unix());
      $('#frm_end').val(window.end.unix());
      $('#get_agent_stats_form').submit(function(){
      });
      
    });
    
    function pad(num) {
      return ("0"+num).slice(-2);
    }
    function hhmmss(seconds) {
      return (Math.floor(seconds / 3600)) + ":" + ("0" + Math.floor(seconds / 60) % 60).slice(-2) + ":" + ("0" + seconds % 60).slice(-2)
      
      /*
      var minutes = Math.floor(secs / 60);
      secs = secs%60;
      var hours = Math.floor(minutes/60)
      minutes = minutes%60;
      if(hours<99){
        return pad(hours)+":"+pad(minutes)+":"+pad(secs);
      } else {
        return hours+":"+pad(minutes)+":"+pad(secs);
      }
      */
      
    }
    
    function fetchWorkHoursData() {
      var mData = {
        "campaign_id": window.campaign_id,
        "start": window.start.unix(),
        "end": window.end.unix(),
        "_token": "{{ csrf_token() }}"
      };
      $.ajax({
        url: "{{ url('/getScheds') }}",
        type: "POST",
        data: mData,
        success: function(rtnData) {
          if(window.schedChart!==undefined) window.schedChart.destroy();
          console.log(rtnData);
          var schedData = {
            type: 'bar',
            data: {
              labels: rtnData.scheds.labels,
              datasets: [
                {
                  type: 'bar', 
                  label: 'Total Work Hours',
                  yAxisID: 'A',
                  data: rtnData.scheds.workhours,
                  backgroundColor: window.chartColors.red,
                  borderColor: 'white',
                  borderWidth: 2
                },
                {
                  type: 'line',
                  label: 'Agent Count',
                  yAxisID: 'B',
                  data: rtnData.scheds.agentcount,
                  borderColor: window.chartColors.blue,
                  borderWidth: 2,
                  fill: false
                }
              ]
            },
            options: {
              scales: {
                xAxes: [{
                  barPercentage: 0.4
                }],
                yAxes: [
                  {
                    id: 'A',
                    type: 'linear',
                    position: 'left',
                    ticks: {
                      beginAtZero: true,
                      callback: value => {
                        return hhmmss(value);
                      }
                    }
                  },
                  {
                    id: 'B',
                    type: 'linear',
                    position: 'right'
                  }
                ]
              },
              tooltips: {
                mode: 'index',
                intersect: true,
                callbacks: {
                  label: function (tooltipItem, data) {
                      if (tooltipItem.datasetIndex === 1) {
                          return "Agents: " + tooltipItem.yLabel.toString();
                      } else if (tooltipItem.datasetIndex === 0) {
                          return "Total Hours: " + hhmmss(tooltipItem.yLabel);
                      }
                  }
                }

              },
              responsive: true,
              maintainAspectRatio: false,
              title: { display: false }
              
            }
          };
          
          var ctx2 = document.getElementById("chart_workhours").getContext("2d");
          window.schedChart = new Chart(ctx2, schedData);
        },
        error: function(rtnData) {
          console.log('error' + rtnData);
        }
      });
    }
    
    function fetchActivityData() {
      var mData = {
        "campaign_id": window.campaign_id,
        "start": window.start.unix(),
        "end": window.end.unix(),
        "_token": "{{ csrf_token() }}"
      };
      $.ajax({
        url: "{{ url('/getStats') }}",
        type: "POST",
        data: mData,
        success: function(rtnData) {
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

    
    function loadWorkHoursDate(start, end) {
        $('#daterange-btn1 span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        window.start = start;
        window.end = end;
        fetchWorkHoursData();
    }
    $('#daterange-btn1').daterangepicker(
      {
        ranges   : {
          'Last 7 Days' : [moment().subtract(6, 'days'), moment()],
          'Last 30 Days': [moment().subtract(29, 'days'), moment()],
          'This Month'  : [moment().startOf('month'), moment().endOf('month')],
          'Last Month'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        startDate: window.start,
        endDate  : window.end
      }, loadWorkHoursDate
    );
    loadWorkHoursDate(window.start, window.end);
    
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
    loadActivityDate(window.start, window.end);
    
  });
</script>
@stop