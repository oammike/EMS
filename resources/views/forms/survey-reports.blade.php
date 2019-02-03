@extends('layouts.main')

@section('metatags')
<title>Survey Reports | Open Access EMS</title>
<link rel="stylesheet" type="text/css" href="{{URL::asset('public/js/formBuilder/demo.css')}}">
@endsection

@section('content')


  <!-- Content Header (Page header) -->
    <section class="content-header">
      <h4><i class="fa fa-file"></i> Report </h4>
      <ol class="breadcrumb">
        <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="{{action('SurveyController@index')}}"><i class="fa fa-question"></i> Surveys</a></li>
        <li class="active">Survey Report</li>
      </ol>
    </section>

     <section class="content">
          <div class="row">
            <div class="col-lg-1"></div>
            <div class="col-lg-10">

              <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">{{$survey->name}}</h3>

              <div class="box-tools pull-right">
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
                <div class="col-md-8">
                  <p class="text-center">
                    <strong>Average Satisfaction Rating</strong>
                  </p>

                  <!-- DONUT CHART -->
                  <div class="chart" id="sales-chart" style="height: 300px; position: relative;"></div>
                 
                  <!-- /.chart-responsive -->
                </div>
                <!-- /.col -->
                <div class="col-md-4">
                  <p class="text-center">
                    <strong>Survey Respondents</strong>
                  </p>

                  <div class="progress-group">
                    <span class="progress-text">Back Office ({{ number_format( ( $totalBackoffice/count($surveyData) )*100 ,1)}}%) </span>
                    <span class="progress-number"><b>{{$totalBackoffice}} </b>/ {{count($surveyData)}} </span>

                    <div class="progress sm">
                      <div class="progress-bar progress-bar-pink" style="width: {{( $totalBackoffice/count($surveyData) )*100 }}%"></div>
                    </div>
                  </div>
                  <!-- /.progress-group -->
                  <div class="progress-group">
                    <span class="progress-text">Operations  ({{ number_format( ( $totalOps/count($surveyData) )*100 ,1)}}%) </span>
                    <span class="progress-number"><b>{{$totalOps}} </b>/{{count($surveyData)}}</span>

                    <div class="progress sm">
                      <div class="progress-bar progress-bar-pink" style="width: {{( $totalOps/count($surveyData) )*100 }}%"></div>
                    </div>
                  </div>
                  <!-- /.progress-group -->
                  <p><br/></p>
                  <h4><i class="fa fa-users"></i> Respondent Type<br/><br/></h4>
                  <div class="progress-group">
                    <span class="progress-text">Promoters <span class="text-primary">( {{number_format(  ( count($promoters)/count($surveyData) )*100 ,1) }}% ) </span></span>
                    <span class="progress-number"><b>{{ count($promoters) }} </b>/ {{count($surveyData)}} </span>

                    <div class="progress sm">
                      <div class="progress-bar progress-bar-aqua" style="width: {{( count($promoters)/count($surveyData) )*100 }}%"></div>
                    </div>
                  </div>

                  <div class="progress-group">
                    <span class="progress-text">Passives <span class="text-primary">( {{number_format(  ( count($passives)/count($surveyData) )*100 ,1) }}% ) </span></span>
                    <span class="progress-number"><b>{{ count($passives) }}</b>/ {{count($surveyData)}}</span>

                    <div class="progress sm">
                      <div class="progress-bar progress-bar-green" style="width: {{( count($passives)/count($surveyData) )*100 }}%"></div>
                    </div>
                  </div>
                  <!-- /.progress-group -->
                  <div class="progress-group">
                    <span class="progress-text">Detractors <span class="text-primary">( {{number_format(  ( count($detractors)/count($surveyData) )*100 ,1) }}% ) </span></span>
                    <span class="progress-number"><b>{{ count($detractors) }}</b>/ {{count($surveyData)}}</span>

                    <div class="progress sm">
                      <div class="progress-bar progress-bar-red" style="width: {{( count($detractors)/count($surveyData) )*100 }}%"></div>
                    </div>
                  </div>
                  <!-- /.progress-group -->
                </div>
                <!-- /.col -->
              </div>
              <!-- /.row -->
            </div>
            <!-- ./box-body -->
            <div class="box-footer">
              <div class="row">

                @foreach($categoryTags as $ct)
                <div class="col-sm-3 col-xs-6">
                  <div class="description-block border-right">
                    <span class="description-percentage text-green"><i class="fa fa-caret-up"></i> %</span>
                    <h5 class="description-header"></h5>
                    <span class="description-text">{{$ct->label}} </span>
                  </div>
                  <!-- /.description-block -->
                </div>
                @endforeach
                
              </div>
              <!-- /.row -->
              
            </div>
            <!-- /.box-footer -->


            <div class="box-footer">
              <h3 class="text-primary"><i class="fa fa-check"></i> Compliance per Program</h3><br/>

              @foreach($programData->sortBy('name') as $p)
              <div class="info-box bg-blue pull-left" style="width: 48%; margin-right: 10px">
                @if ($p['logo'] == "white_logo_small.png")
                  <span class="info-box-icon"><img src="../../public/img/{{$p['logo']}}" width="50%" /></span>
                @else
                  <span class="info-box-icon" style="background-color: #fff"><img src="../../public/img/{{$p['logo']}}" width="80%" /></span>
                @endif

                  <div class="info-box-content">
                    <span class="info-box-text">{{$p['name']}} </span>
                    <span class="info-box-number" style="color:#ffda46">{{ number_format($p['respondents']/$p['total']*100 ,1)}}% </span>

                    <div class="progress">
                      <div class="progress-bar" style="width: {{$p['respondents']/$p['total']*100 }}%"></div>
                    </div>
                    <span class="progress-description">{{$p['respondents']}} out of {{$p['total']}} </span>
                  </div>
                  <!-- /.info-box-content -->
              </div>
              @endforeach

             


            </div>
          </div>
             

              

              
              

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

<script>




  $(function () {

    
   'use strict';

    var vals = [];
    var i = 0;
    @foreach($groupedRatings as $g)

    
          vals[i] = {label:"Rating: [{{$g[0]['rounded']}}]", value:"{{count($g)}}" };
          i++;
      
    @endforeach


    console.log(vals);

    var donut = new Morris.Donut({
      element  : 'sales-chart',
      resize   : true,
      colors   : [  '#3c8dbc', '#8ccb2c', '#ffe417','#fd1e1e',],//'#f39c12',
      data     : vals,
      hideHover: 'auto'
    });

  
   




   


      
    


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