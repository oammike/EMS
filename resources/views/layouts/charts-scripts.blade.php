//*************** CHARTS

     //BAR CHART

    @if(count($performance) == 1) 
    
    var bar = new Morris.Bar({
      element: 'bar-chart',
      resize: true,
      data: [

        @foreach($performance as $p)

        {y:'{{$p["type"]}}', 'score':"{{$p['score']}} "},

        @endforeach
       
      ],
      barColors: ['#67bcec'],// ['#ec9950'],
      xkey: 'y',
      ykeys: ['score'],
      labels: ['Eval Score'],
      hideHover: 'auto'
    });

     

      // Fix for charts under tabs
      $('.box ul.nav a').on('shown.bs.tab', function () {
        area.redraw();
        
       
      });

//*************** END CHARTS

@else

  var areaChartData = {
      //labels  : ['2016 Jul-Dec','2017 Jan-Jun', '2017 Jul-Dec', '2018 Jan-Jun'],
      labels  : [ @foreach($performance as $p) "{{$p['type']}}", @endforeach
      ],
      
      datasets: [
        {
          label               : 'Electronics',
          fillColor           : 'rgba(210, 214, 222, 1)',
          strokeColor         : 'rgba(251,143,48,1)',
          pointColor          : 'rgba(210, 214, 222, 1)',
          pointStrokeColor    : '#c1c7d1',
          pointHighlightFill  : '#fff',
          pointHighlightStroke: 'rgba(220,220,220,1)',
          data                : [ @foreach($performance as $p) "{{$p['score']}}", @endforeach
      ],
          
        },
        
      ]
    }

    var areaChartOptions = {
      //Boolean - If we should show the scale at all
      showScale               : true,
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
      //Boolean - Whether the line is curved between points
      bezierCurve             : true,
      //Number - Tension of the bezier curve between points
      bezierCurveTension      : 0.3,
      //Boolean - Whether to show a dot for each point
      pointDot                : false,
      //Number - Radius of each point dot in pixels
      pointDotRadius          : 4,
      //Number - Pixel width of point dot stroke
      pointDotStrokeWidth     : 1,
      //Number - amount extra to add to the radius to cater for hit detection outside the drawn point
      pointHitDetectionRadius : 20,
      //Boolean - Whether to show a stroke for datasets
      datasetStroke           : true,
      //Number - Pixel width of dataset stroke
      datasetStrokeWidth      : 2,
      //Boolean - Whether to fill the dataset with a color
      datasetFill             : true,
      //String - A legend template
      legendTemplate          : '<ul class="<%=name.toLowerCase()%>-legend"><% for (var i=0; i<datasets.length; i++){%><li><span style="background-color:<%=datasets[i].lineColor%>"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>',
      //Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
      maintainAspectRatio     : true,
      //Boolean - whether to make the chart responsive to window resizing
      responsive              : true
    }

   //-------------
    //- LINE CHART -
    //--------------
    var lineChartCanvas          = $('#lineChart').get(0).getContext('2d')
    var lineChart                = new Chart(lineChartCanvas)
    var lineChartOptions         = areaChartOptions
    lineChartOptions.datasetFill = false
    lineChart.Line(areaChartData, lineChartOptions)


@endif


$('#ads').on('click',function(){
  console.log("cliced");
  $.ajax({
            url: "{{action('HomeController@logAction','1')}}",
            type: "GET",
            data: {'action': '1'},
            success: function(response){
                      console.log(response);

          }

});
  

});