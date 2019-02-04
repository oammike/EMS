@extends('layouts.main')

@section('metatags')
<title>Survey Results | Open Access EMS</title>
<link rel="stylesheet" type="text/css" href="{{URL::asset('public/js/formBuilder/demo.css')}}">
@endsection

@section('content')


  <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1><i class="fa fa-file"></i> {{$survey->name}} </h1>
      <ol class="breadcrumb">
        <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="{{action('SurveyController@index')}}"><i class="fa fa-question"></i> Surveys</a></li>
        <li class="active">{{$survey->name}} </li>
      </ol>
    </section>

     <section class="content">



          
               

     
          <div class="row">
            <div class="col-lg-1"></div>
            <div class="col-lg-10">

              <!-- <div class="chart" style="width: 25%; float: right;">
                <div class="chart" id="sales-chart" style="height: 250px;"></div>
                <p style="font-size: 0.8em;" class="text-center">* ( your employee <strong>N</strong>et <strong>P</strong>romoter <strong>S</strong>core )</p>
              </div> -->

              <h1 class="text-primary"><br/><br/>Thank you</h1>
                   <h3 style="font-weight: lighter;"> for completing the Employee Experience Survey and sharing your thoughts and insights - we value them. <br/><br/>

                  <span style="font-weight:100; color: #5e6c7b; font-size: 0.8em;">Your answers will help us understand what's working, what's not, and where we can improve. 
                  Your feedback will help us boost your experience and happiness at Open Access for years to come. </span><br/><br/></h3>

                  
                     @if($promoter)

                     <div style="border: 2px dotted #fff; padding:30px; background: rgba(255, 255, 255, 0.4);">
                       <label><input type="checkbox" name="bepart" id="bepart" /> &nbsp;&nbsp; <em>I would like to be a part of Employee Engagement Committee.</em> </label>
                      

                     </div><!--end dotted box-->

                     @endif

                     @if($detractor)

                     <div style="border: 2px dotted #fff; padding:30px; background: rgba(255, 255, 255, 0.4);">
                       <label><input type="checkbox" name="bepart" id="bepart" /> &nbsp;&nbsp; <em>I would like join a focused group discussion to address top concerns.</em> </label>
                      

                     </div><!--end dotted box-->

                     @endif

             
              <div class="info-box bg-blue">
              <span class="info-box-icon"><img src="../public/img/white_logo_small.png" width="90%" /></span>

              <div class="info-box-content">
                <span class="info-box-text"></span>
                <span class="info-box-number">{{$percentage}}% </span>

                <div class="progress">
                  <div class="progress-bar" style="width: {{$percentage}}%"></div>
                </div>
                <span class="progress-description">
                      {{$completed}} <small>out of</small> {{$actives}} Open Access Employees ( Makati | Davao ) have completed the Employee Experience Survey
                    </span>
              </div>
              <!-- /.info-box-content -->
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

   var vals =[];

    vals[0] = { label:"eNPS", value:{{$nps}} };

   // var donut = new Morris.Donut({
   //            element  : 'sales-chart',
   //            resize   : true,
   //            colors   : [ '{{$color}} ' ],
   //            data     : vals,
   //            hideHover: 'auto'
   //          });



   @if($extraData)

    $('#bepart').prop('checked','true');


   @endif


   $('input[name="bepart"]').on('click',function(){

    var c = $('#bepart').is(':checked');
    var _token = "{{ csrf_token() }}";
    if (c) {var bepart = '1'; var msg = "Thank you for taking interest to be part of it. We will keep you posted for further details."; var cname = "success"} else {var bepart='0'; var msg= "Okay, this is noted. Hope you'd change your mind and reconsider to take part on this."; var cname="error"; } 



      $.ajax({
                url: "{{action('SurveyController@bePart')}}",
                type:'POST',
                data:{ 
                  
                  'survey_id': '{{$survey->id}}',
                  'bepart': bepart,
                  'nps': '{{$nps}}',
                  '_token':_token
                },
                success: function(response){
                  console.log(response);
                   $.notify(msg,{className:cname,globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );
                   

                }
              });
    

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