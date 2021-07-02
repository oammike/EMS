@extends('layouts.main')

@section('metatags')
<title>Surveys | Open Access EMS</title>
<link rel="stylesheet" type="text/css" href="{{URL::asset('public/js/formBuilder/demo.css')}}">
@endsection

@section('content')


  <!-- Content Header (Page header) -->
    <section class="content-header">
      <h4><i class="fa fa-file"></i> {{$survey->name}} </h4>
      <ol class="breadcrumb">
        <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li>Surveys</li>
        <li class="active"> {{$survey->name}} </li>
      </ol>
    </section>

     <section class="content">



          
               

     
          <div class="row">
            <div class="col-lg-1"></div>
            <div class="col-lg-10">
               
               {!! $intro->body !!}

                <p class="text-center"><br/><br/><a class="text-center btn btn-lg btn-success" href="{{action('SurveyController@show',$survey->id)}}">Start Survey <i class="fa fa-arrow-right"></i></a></p>


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
<script>



  $(function () {

    
   'use strict';

   
 


   });

   

 
</script>
<!-- end Page script -->


@stop