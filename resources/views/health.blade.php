<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  
  <title>Health Declaration Form</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  
  <link rel="icon" href="{{asset('public/img/oam_favicon1-55027f4ev1_site_icon-32x32.png')}}" sizes="32x32"/> 
  <link rel="icon" href="{{asset('public/img/oam_favicon1-55027f4ev1_site_icon-256x256.png')}}" sizes="192x192"/> 
  <link rel="apple-touch-icon-precomposed" href="{{asset('public/img/oam_favicon1-55027f4ev1_site_icon-256x256.png')}}"/> 
  <meta name="msapplication-TileImage" content="{{asset('public/img/oam_favicon1-55027f4ev1_site_icon.png')}}"/>

  <meta name="csrf-token" content="{{ csrf_token() }}">
  

 <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
 <link href="{{asset('public'.elixir('css/all.css'))}}" rel="stylesheet" />
 <link href="{{asset('public/css/font-awesome.min.css')}}" rel="stylesheet">
 <!-- Add IntroJs styles -->
<link href="{{asset('public/css/introjs.css')}}" rel="stylesheet">
 <style type="text/css"> 
 .navbar{ -webkit-box-shadow:6px 6px 13px 0px rgba(0, 0, 0, 0.19); }
 .skin-blue .main-header .navbar, .skin-blue .main-header .logo{background-color: #1a8fcb;}
 .content-wrapper{

   background:url("{{ asset('public/img/bg_swish.png')}}") bottom right fixed no-repeat;
   background-color: #fdfdfd; /*#f5f9fc;*/

   }

   #modalMain{ background:url("{{ asset('public/img/big_transparent.png')}}") bottom right no-repeat #fff }
   .main-footer {background-color: #0678b2; color:#fff;}
   .main-footer a{color:#fff;} </style>
  <!-- Font Awesome -->
  <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css"> -->

  <!-- Ionicons -->
 <!--  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css"> -->
  <!-- Theme style -->
  
  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

</head>

<body class="content-wrapper" style="padding-left: 0px; margin-left: 0px">
     <section class="content">
     

     
      <div class="row">
        <div class="col-lg-3 col-sm-12"></div>
        <div class="col-lg-6 col-sm-12">

          <h4 class="text-center"><img src="storage/uploads/logo_2020_170.png" /> <br/><br/><br/><br /><br/> 
          <small>
            In compliance with the  DTI-DOLE Interim Guidelines on Workplace Prevention and Control COVID19, 
            <br/> all employees must accomplish daily the Health Declaration Form <strong class="text-danger"> prior to reporting to work and entering the work area.</strong></smaller></small>
            <br /><br/></h4>

          <div class="row">
            <div class="col-sm-6 text-center">
              <a  style="margin-top: 5px" class="btn btn-success btn-lg" href="{{action('HomeController@healthForm')}}"> All Onsite Workers <br/><small> fill out Health Declaration form here </small><i class="fa fa-file-o pull-right"></i></a>
            </div>
            <div class="col-sm-6 text-center">
              <a style="margin-top: 5px" class="btn btn-default btn-lg" href="{{action('HomeController@home')}}"> At Home Workers <br/><small> proceed with EMS Dashboard</small> <i class="fa fa-home pull-right"></i></a>
            </div>
          </div>
          
          
          <div class="clearfix"></div>
          <h5 class="text-center" style="margin-top: 10%">
          
            Thank you for your cooperation.
            <br /><br/>
            </h5>

        </div>
        <div class="col-lg-3  col-sm-12"></div>

        

      </div><!-- end row -->





       
     </section>

</body>
</html>
          



