@extends('layouts.main')

@section('metatags')
<title>Surveys | Open Access EMS</title>
<link rel="stylesheet" type="text/css" href="{{URL::asset('public/js/formBuilder/demo.css')}}">
@endsection

@section('content')


  <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1><i class="fa fa-file"></i> All Surveys </h1>
      <ol class="breadcrumb">
        <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Surveys</li>
      </ol>
    </section>

     <section class="content">
          <div class="row">
            <div class="col-lg-1"></div>
            <div class="col-lg-10">

              <h3><br/><br/>  </h3>
              <table class="table" id="surveys">
                <tr style="background: rgba(255, 255, 255, 0.8);">
                  <th>Survey Title</th>
                  <th>Description</th>
                  <th>Survey Period</th>
                  <th>Actions</th>
                </tr>

                @foreach($surveys as $s)
                <tr>
                  <td>{{$s->name}} </td>
                  <td>{{$s->description}} </td>
                  <td>{{$s->startDate}} <br/>to<br/> {{$s->endDate}}  </td>
                  <td><a href="{{action('SurveyController@report',$s->id)}}" class="btn btn-xs btn-primary" style="margin:1px"><i class="fa fa-search"></i> View Reports </a> <br/> 
                      <a class="btn btn-xs btn-success" style="margin:1px"><i class="fa fa-download"></i> Download </a><br/> 
                      <a class="btn btn-xs btn-default" style="margin:1px"><i class="fa fa-trash"></i> Delete</a> 
                  </td>
                </tr>

                @endforeach
              </table>

              

              
              

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