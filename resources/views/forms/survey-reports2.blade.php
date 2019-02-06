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
                
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <div class="row">
                <div class="col-md-12">
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
                
              </div>
              <!-- /.row -->
            </div>
            <!-- ./box-body -->
            <div class="box-footer">
              
              <div class="row">


                
                
              </div>
              <!-- /.row -->
              
            </div>
            <!-- /.box-footer -->


            <div class="box-footer">
              <h3 class="text-primary"><i class="fa fa-check"></i> Compliance per Program</h3><br/>


              @foreach($programData->sortBy('name') as $p)

              @if ($p['logo'] == "white_logo_small.png")

                  <span class="info-box-icon" style=" width: 180px;margin-left: 20px; background:url(../../public/img/{{$p['logo']}}) no-repeat; background-color: #dedede ">
                    <h4 style="padding-top: 25px"><a href="{{action('CampaignController@show',$p['id'])}}" target="_blank">{{$p['name']}}</a> </h4>
                    
                  </span>

                @else
                  <span class="info-box-icon" style="background-color: #fff; border:solid 1px #0073b7; overflow: hidden; width: 180px;margin-left: 20px">
                    <a href="{{action('CampaignController@show',$p['id'])}}" target="_blank">
                    <img src="../../public/img/{{$p['logo']}}" width="140px" /></a></span>
                @endif

              <div class="info-box bg-blue pull-left" style="width: 25%; margin-right: 10px">
                

                  <div class="info-box-content" style="margin-left: 0px;">
                   
                    <span class="info-box-number" style="color:#ffda46">{{ number_format($p['respondents']/$p['total']*100 ,1)}}% <span style="font-size: x-small;"> complete</span></span>
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
      colors   : [  '#3c8dbc', '#8ccb2c', '#ffe417','#f39c12','#fd1e1e',],//',
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