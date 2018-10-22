@extends('layouts.main')

@section('metatags')
<title>Form Builder | Open Access EMS</title>
<link rel="stylesheet" type="text/css" href="{{URL::asset('public/js/formBuilder/demo.css')}}">
@endsection

@section('content')


  <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1><i class="fa fa-file"></i> Form Builder </h1>
      <ol class="breadcrumb">
        <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Access Denied</li>
      </ol>
    </section>

     <section class="content">



          
               

     
          <div class="row">
            <div class="col-lg-1"></div>
            <div class="col-lg-10">

              <div id="stage1" class="build-wrap"></div>
              <form class="render-wrap"></form>
              <button id="edit-form">Edit Form</button>
              <div class="action-buttons">
                <h2>Actions</h2>
                <button id="showData" type="button">Show Data</button>
                <button id="clearFields" type="button">Clear All Fields</button>
                <button id="getData" type="button">Get Data</button>
                <button id="getXML" type="button">Get XML Data</button>
                <button id="getJSON" type="button">Get JSON Data</button>
                <button id="getJS" type="button">Get JS Data</button>
                <button id="setData" type="button">Set Data</button>
                <button id="addField" type="button">Add Field</button>
                <button id="removeField" type="button">Remove Field</button>
                <button id="testSubmit" type="submit">Test Submit</button>
                <button id="resetDemo" type="button">Reset Demo</button>
                
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

<script src="{{URL::asset('public/js/formBuilder/vendor.js')}}"></script>
<script src="{{URL::asset('public/js/formBuilder/form-builder.min.js')}}"></script>
<script src="{{URL::asset('public/js/formBuilder/form-render.min.js')}}"></script>
<script src="{{URL::asset('public/js/formBuilder/demo.js')}}"></script>


<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.3.1/jquery.rateyo.min.js"></script>
 -->


<!-- Page script -->
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