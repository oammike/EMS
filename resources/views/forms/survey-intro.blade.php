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
               
               <h2 style="padding-bottom: 50px" class="text-center">Welcome to Employee <strong class="text-primary">Pulse Check 2020</strong>!</h2>
               <img class="pull-right" src="../../storage/uploads/pulse2020.jpg" width="40%" style="padding:10px" /><br/><br/>
               <p style="font-size: large;">Hi Open Access family,</p><br/><br/>

               <p style="font-size: large;">What a year! Truly unprecedented and one for the books. </p>

               <p style="font-size: large;">As we have stepped into the second half of 2020, we would love to hear from you about how we did during the first half.  </p><br/>
                 
               <p style="font-size: large;">This Employee Pulse Check is designed to help us understand our specific strengths and identify our areas for improvement to assess how we did these last six months in the context of our vision and values:  look also at how we took care of business and each other at the start of the pandemic early this year, and now adapting to a new normal. </p> <br/>

               <p style="font-size: large;">We want to know how we can make your experience better. </p>
                 
               <p style="font-size: large;">Your voice matters. Please answer each of the 15  questions honestly. Your responses are confidential and will not have any negative impact on you or your standing at work.</p><br/>

               <p style="font-size: large;">Let us continue to work together at making life at Open Access better!</p><br/>
               <strong  style="font-size: large;" >Joy</strong><br/><br/>

               <div style="background: rgba(0, 0, 0, 0.15); color: #333; padding:20px" >
                <h4><i class="fa fa-lock"></i> Privacy Notice</h4><br/><br/>
                 
                <h5><i class="fa fa-info-circle"></i> What information is collected</h5>
                <p>We will collect feedback you provide through our EMS tool in which the employee master list is configured. Any access to information related to your employment records are for data processing: employee name, email address, IP address, gender, tenure, department, education level, nationality, and employment status. All collected data is strictly confidential. None of the information you provide will be disclosed or shared to any third party.</p><br/>

                <h5><i class="fa fa-info-circle"></i> Consent</h5>

                <p>By clicking on “Start Survey”, I confirm that I have read the privacy statement above, and express my consent for Open Access BPO to collect, record, organize my personal information and feedback as part of the Employee Experience Survey; and affirm my right to privacy and my right to transparency pursuant to the provisions of the Republic Act No. 10173 of the Philippines, Data Privacy Act of 2012 and its corresponding Implementing Rules and Regulations. </p>

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