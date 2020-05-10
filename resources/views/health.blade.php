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
@if ( isset($include_barista_scripts) && $include_barista_scripts===TRUE )
 <link rel="preload" as="script" href="{{asset('public/js/jsqr/decoder.js')}}">
@endif
@if ( isset($include_rewards_scripts) && $include_rewards_scripts===TRUE )
 <link href="{{asset('public/plugins/timepicker/bootstrap-timepicker.min.css')}}" rel="stylesheet">
 <link href="{{asset('public/css/rewardsfonts.css')}}" rel="stylesheet">
 <link href="{{asset('public/css/rewards.css')}}" rel="stylesheet">
 
@endif
</head>





  <!-- Content Header (Page header) -->
    <section class="content-header">
     
      <ol class="breadcrumb">
        <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Empty Records</li>
      </ol>
    </section>

     <section class="content">



                 <!-- ******** THE DATATABLE ********** -->
          <div class="row">
              <div class="col-lg-3 col-sm-4 col-xs-12" style="background-color:#fff">

                        
                            
              </div> 
              <div class="col-lg-3 col-sm-4  col-xs-9">
                
                
                      

          </div>
               

     
          <div class="row">

            <h4 class="text-center"> <br/><br/><br/><br/> No data found<br /><br/> <small>I'm afraid it might have already been deleted or it doesn't exist in our system. <br/><smaller>Kindly check the submitted data value and try again. Thank you</smaller></small>
              <br /><br/></h4>

            

          </div><!-- end row -->





       
     </section>
          



