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
                <div class="col-md-8">
                  <h5 class="text-center">Data as of: <span class="text-primary">{{ $asOf }}</span> </h5>
                  <p class="text-center">
                    <strong>Average Satisfaction Rating</strong>
                  </p>

                  <!-- DONUT CHART -->
                  <div class="chart" id="sales-chart" style="height: 300px; position: relative;"></div>


                  <div class="info-box bg-blue" style="margin-top: 28px">
                    <span class="info-box-icon"><img src="../../public/img/white_logo_small.png" width="90%" /></span>

                    <div class="info-box-content">
                      <span class="info-box-text"></span>
                      <span class="info-box-number">{{$percentage}}% </span>

                      <div class="progress">
                        <div class="progress-bar" style="width: {{$percentage}}%"></div>
                      </div>
                      <span class="progress-description">
                            {{ count($surveyData)}} <small>out of</small> {{$actives}} Open Access Employees ( Makati | Davao ) <br/>
                            <span style="font-size: x-small;">have completed the Employee Experience Survey</span>
                          </span>
                    </div>
                    <!-- /.info-box-content -->
                  </div>
                 
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
                    <span class="progress-text">Promoters <span class="text-primary">( {{number_format(  round(( count($promoters)/count($surveyData) )*100 ,1)) }}% ) </span></span>
                    <span class="progress-number"><b>{{ count($promoters) }} </b>/ {{count($surveyData)}} </span>

                    <div class="progress sm">
                      <div class="progress-bar progress-bar-aqua" style="width: {{( count($promoters)/count($surveyData) )*100 }}%"></div>
                    </div>
                  </div>

                  <div class="progress-group">
                    <span class="progress-text">Passives <span class="text-primary">( {{number_format(  round(( count($passives)/count($surveyData) )*100 ,1))+1 }}% ) </span></span>
                    <span class="progress-number"><b>{{ count($passives) }}</b>/ {{count($surveyData)}}</span>

                    <div class="progress sm">
                      <div class="progress-bar progress-bar-green" style="width: {{( count($passives)/count($surveyData) )*100 }}%"></div>
                    </div>
                  </div>
                  <!-- /.progress-group -->
                  <div class="progress-group">
                    <span class="progress-text">Detractors <span class="text-primary">( {{number_format(  round(( count($detractors)/count($surveyData) )*100 ,1)) }}% ) </span></span>
                    <span class="progress-number"><b>{{ count($detractors) }}</b>/ {{count($surveyData)}}</span>

                    <div class="progress sm">
                      <div class="progress-bar progress-bar-red" style="width: {{( count($detractors)/count($surveyData) )*100 }}%"></div>
                    </div>
                  </div>
                  <!-- /.progress-group -->

                  <h1 style="margin-top: 55px; padding:25px; background:rgba(154,245,38,0.5); color:#666" class="text-center">eNPS : <strong style="color: #000">{{ $eNPS }} </strong><span style="font-size: 0.5em;"></span> </h1>
                </div>
                <!-- /.col -->
              </div>
              <!-- /.row -->
            </div>
            <!-- ./box-body -->
            <div class="box-footer">
              <h4 class="text-center" style="padding:20px">Average Rating per Category</h4>
              <div class="row">


                @foreach($categoryData as $ct)
                <div class="col-sm-3 col-xs-6">
                  <div class="description-block border-right">
                    
                    @if( $ct['aveRating'] >= 4.0 )
                    <h4 class="description-header text-blue"> {{$ct['aveRating']}} </h4>
                    @elseif ($ct['aveRating'] >= 3.8 && $ct['aveRating'] < 3.99 )
                    <h4 class="description-header text-green"> {{$ct['aveRating']}}  </h4>
                    @elseif ($ct['aveRating'] >= 3.5 && $ct['aveRating'] < 3.89 )
                    <h4 class="description-header text-orange"> {{$ct['aveRating']}}</h4>
                    @elseif ($ct['aveRating'] >= 2.0 && $ct['aveRating'] < 3.49 )
                    <h4 class="description-header text-red"> {{$ct['aveRating']}}</h4>
                    @elseif ($ct['aveRating'] < 1.99 )
                    <h4 class="description-header text-red"> {{$ct['aveRating']}} </h4>
                    @endif
                    <span class="description-text">
                      <a style="font-weight: lighter; font-size:0.9em; ; color: #333; text-decoration: underline;" href="{{action('SurveyController@showCategory',$ct['categoryID'])}} ">
                        {{$ct['categoryName']}} &nbsp; 
                        <span style="font-size: smaller;"><i class="fa fa-external-link"></i> </span> 
                      </a>
                    </span>
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

              @if ($p['respondents'] !== 0 && $p['total'] !== 0 )

              @if ($p['logo'] == "white_logo_small.png")

                  <span class="info-box-icon" style=" width: 180px;margin-left: 20px; background:url(../../public/img/{{$p['logo']}}) no-repeat; background-color: #dedede ">
                    <h4 style="padding-top: 25px">
                      <a href="{{action('CampaignController@show',$p['id'])}}" target="_blank">{{$p['name']}}</a>
                    </h4>
                    
                  </span>

                @else
                  <span class="info-box-icon" style="background-color: #fff; border:solid 1px #0073b7; overflow: hidden; width: 180px;margin-left: 20px">
                    <a href="{{action('CampaignController@show',$p['id'])}}" target="_blank">
                    <img src="../../public/img/{{$p['logo']}}" width="140px" /></a></span>
                @endif 

                
              @if ($p['respondents'] ==  $p['total'])
              <div class="info-box pull-left" style="width: 25%; margin-right: 10px; background-color: #75838c;">
              @else
              <div class="info-box bg-blue pull-left" style="width: 25%; margin-right: 10px;">
              @endif
                

                  <div class="info-box-content" style="margin-left: 0px;">
                   
                  @if ($p['respondents'] ==  $p['total'])
                    <span class="info-box-number" style="color:#fff">{{ number_format($p['respondents']/$p['total']*100 ,1)}}% <span style="font-size: x-small;"> complete</span></span>
                  @else
                  <span class="info-box-number" style="color:#ffda46">{{ number_format($p['respondents']/$p['total']*100 ,1)}}% <span style="font-size: x-small;"> complete</span></span>

                  @endif
                    <span class="progress-description">{{$p['respondents']}} / {{$p['total']}} <em style="font-size: smaller;">employee respondents</em> </span>
                    <div class="progress">
                      <div class="progress-bar" style="width: {{$p['respondents']/$p['total']*100 }}%"></div>
                    </div>

                    
                    Experience: 
                    <span style="color:#ffda46">
                      @for ($i = 1; $i <= $p['aveRating']; $i++)
                      <i class="fa fa-star"></i>
                      @endfor

                      @for ($c = 5; $c > $p['aveRating']; $c-- ) 
                      <i class="fa fa-star-o"></i>
                      @endfor
                      
                    </span><!-- <span class="label label-success" style="font-size: large;">5</span> -->
                    <span style="font-size: x-small;">&nbsp;&nbsp;(average)</span>
                  </div>
                  <!-- /.info-box-content -->
              </div>

              @endif

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
          
          // vals[i] = {label:"Rating: [{{$g[0]['rounded']}}]", value:"{{count($g)}}" };
          i++;
      
    @endforeach


    console.log(vals);

    var donut = new Morris.Donut({
      element  : 'sales-chart',
      resize   : true,
      colors   : [   '#8ccb2c', '#3c8dbc','#ffe417','#f39c12','#fd1e1e',],//',
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