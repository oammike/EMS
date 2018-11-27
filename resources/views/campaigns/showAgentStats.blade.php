@extends('layouts.main')


@section('metatags')
  <title>{{ $campaign->name }} Agent Stats</title>
    <meta name="description" content="{{ $campaign->name }} Agent Stats">

@stop


@section('content')




<section class="content-header">

      <h1><i class="fa fa-file-o"></i> {{ $campaign->name }} Agent Stats</h1>
      <a style="margin-top:30px; margin-left: 10px" class="btn btn-xs btn-default" href="{{ url('campaign/'.$campaign->id) }}"><i class="fa fa-arrow-left"></i> Back to Overview</a>
      <a style="margin-top:30px; margin-left: 10px" class="btn btn-xs btn-default" href="{{ url('campaignStats/'.$campaign->id) }}">View Campaign Stats</a>
      <ol class="breadcrumb">
        <li><a href="{{ action('HomeController@index') }}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="{{ action('CampaignController@index') }}">All Campaigns</a></li>
        <li><a href="{{ url('campaign/'.$campaign->id) }}">{{ $campaign->name }}</a></li>
        <li class="active">Stats</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      
      <!-- Agent Metrics Summary -->
      <div class="row">
        <div class="col-xs-12">
          <div class="box box-primary">
            <div class="box-header with-border">
              <i class="fa fa-bar-chart-o"></i>
              <h3 class="box-title">Agent Summary By Metrics</h3>
              <div class="box-tools pull-right">
                <div class="btn-group">
                  <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                    Display Metric &nbsp;
                    <span class="caret"></span>
                    <span class="sr-only">Toggle Dropdown</span>
                  </button>
                  <ul class="dropdown-menu" role="menu" id="dd_metrics">
                    <li><a href="#">Action</a></li>
                  </ul>
                </div>
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
              <canvas id="chart_metrics" width="720" height="960" />
            </div><!-- /.box-body-->
          </div><!-- /.box -->

        </div><!-- /.col -->
      </div><!-- /.row -->

    </section>
    
    <div id="chartjs-tooltip"></div>

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
    
    
    $('.bt_stats_export').on('click',function(){
      var mData = {
        "campaign_id": window.campaign_id,
        "start": window.start.unix(),
        "end": window.end.unix(),
        "_token": "{{ csrf_token() }}",
        "export": "TRUE"
      };
      $.ajax({
        url: "{{ url('/getAgentStats') }}",
        type: "POST",
        data: mData
      });  
    });
    

    
    function pad(num) {
      return ("0"+num).slice(-2);
    }
    function hhmmss(secs) {
      var minutes = Math.floor(secs / 60);
      secs = secs%60;
      var hours = Math.floor(minutes/60)
      minutes = minutes%60;
      return pad(hours)+":"+pad(minutes)+":"+pad(secs);
    }
    
    function fetchActivityData() {
      var mData = {
        "campaign_id": window.campaign_id,
        "start": window.start.unix(),
        "end": window.end.unix(),
        "_token": "{{ csrf_token() }}"
      };
      $.ajax({
        url: "{{ url('/getAgentStats') }}",
        type: "POST",
        data: mData,
        success: function(rtnData) {
          if(window.activityChart!==undefined) window.activityChart.destroy();
          
          
          $('#dd_metrics').empty();
          rtnData.columns.forEach(function(key,value){
            $('#dd_metrics').append($('<li><a href="#" class="metric_toggles" data-key="'+key+'">'+key+'</a></li>'));
          });
          
          $('.metric_toggles').click(function(){
            var is_time_units = false;
            
            var key = this.dataset.key;
            console.log(key);
            var labels = [];
            var values = [];
            var sortme = [];
            
            for (var name in rtnData.data[key]) {
              var actual_value = rtnData.data[key][name];
              var splitted = actual_value.split(":");
              if(splitted.length==1){
                sortme.push({name: name, value: rtnData.data[key][name]});
              }
              if(splitted.length==2){
                is_time_units = true;
                var duration = Number(splitted[1]) + (Number(splitted[0])*60);
                sortme.push({name: name, value: duration });
              }
              if(splitted.length==3){
                is_time_units = true;
                var duration = Number(splitted[2]) + (Number(splitted[1])*60) + (Number(splitted[0])*3600);
                sortme.push({name: name, value: duration});  
              }
              
            }
            
            var sorted = sortme.sort(function(a, b) {
              return b.value - a.value;
            });
            
            sorted.forEach(function(key,value){
              labels.push(key.name);
              values.push(key.value);
            });
            
            console.log('found '+ labels.length+ ' elements');
            
            if(labels.length>49){
              labels = labels.slice(0, 49);
              values = values.slice(0, 49);
            }
            
            var activityData = {
              type: 'horizontalBar',
              data: {
                labels: labels,
                datasets: [{
                  label: key,
                  backgroundColor: window.chartColors.red,
                  borderColor: window.chartColors.red,
                  borderWidth: 1,
                  data: values
                }]
              },
              options: {
                tooltips:{
                  mode: 'index',
                  intersect: true,
                  callbacks: {
                    label: function (tooltipItem, data) {
                      
                      if(is_time_units === true){
                        return data.datasets[tooltipItem.datasetIndex].label + ": " + hhmmss(tooltipItem.xLabel);
                      } else {
                        return tooltipItem.xLabel;
                      }
                      
                    }
                  }
                },
                scales: {
                  xAxes: [{
                    barPercentage: 0.4,
                    ticks: {
                      beginAtZero: true,
                      callback: value => {
                      
                        if(is_time_units === true){
                          return hhmmss(value);
                        }else{
                          return value;
                        }
                      }
                    }
                  }]
                },
                responsive: true,
                maintainAspectRatio: false,
                title: { display: (labels.length>=49) ? true : false, text: "Top 50" }
              }
            };
            if(window.activityChart!==undefined) window.activityChart.destroy();
            var ctx1 = document.getElementById("chart_metrics").getContext("2d");
            window.activityChart = new Chart(ctx1, activityData);
            
          });
          
          $('.metric_toggles').first().click();
        },
        error: function(rtnData) {
          console.log('error' + rtnData);
        }
      });
    }

    
    
    function loadActivityDate(start, end) {
        $('#daterange-btn1 span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        window.start = start;
        window.end = end;
        fetchActivityData();
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
      }, loadActivityDate
    );
    loadActivityDate(window.start, window.end);
    
  });
</script>
@stop