<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  @yield('metatags')
  <!-- Tell the browser to be responsive to screen width -->
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

   background:url("{{ asset('public/img/bg_swish.png')}}") bottom right fixed repeat-x;
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
<!--
BODY TAG OPTIONS:
=================
Apply one or more of the following classes to get the
desired effect
|---------------------------------------------------------|
| SKINS         | skin-blue                               |
|               | skin-black                              |
|               | skin-purple                             |
|               | skin-yellow                             |
|               | skin-red                                |
|               | skin-green                              |
|---------------------------------------------------------|
|LAYOUT OPTIONS | fixed                                   |
|               | layout-boxed                            |
|               | layout-top-nav                          |
|               | sidebar-collapse                        |
|               | sidebar-mini                            |
|---------------------------------------------------------|
-->
<body class="hold-transition skin-blue sidebar-mini @yield('bodyClasses') ">
<div class="wrapper">


  <!-- Main Header -->
@include('layouts.header')
  <!-- Left side column. contains the logo and sidebar -->

  @if(Auth::user()->userType_id == 4)
    @include('layouts.sidenav-agent')

  @else 
    @include('layouts.sidenav')

  @endif
 

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">

   @yield('content')
    
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <!-- Main Footer -->
  @include('layouts.footer')

  
      



  <!-- Control Sidebar -->
  <aside id="controlsidebar" class="control-sidebar control-sidebar-dark">
    <!-- Create the tabs -->
    <ul class="nav nav-tabs nav-justified control-sidebar-tabs">
      <li class="active"><a href="#control-sidebar-home-tab" data-toggle="tab"><i class="fa fa-users"></i> <br/><small>User Settings</small></a></li>
      
    </ul>
    <!-- Tab panes -->
    <div class="tab-content">
      <!-- Home tab content -->
      <div class="tab-pane active" id="control-sidebar-home-tab">
        <h3 class="control-sidebar-heading">User Settings:</h3>
        
        
        
        
        <!-- /.control-sidebar-menu -->

        <hr/>

        <h3 class="control-sidebar-heading">Password Management:</h3>
        <p><a href="{{action('UserController@changePassword')}}" class="btn btn-sm btn-default"> <i class="fa fa-key"></i> Change My Password</a></p>
        <!-- /.control-sidebar-menu -->

        <br/><br/><br/>

         <h3 class="control-sidebar-heading text-warning">Admin Settings:</h3>
          <hr/>

          <a class="btn btn-xs btn-success btn-flat" style="margin-bottom:5px" href="{{action('UserTypeController@index')}}"><i class="fa fa-search"></i> Manage User Types</a><br/>
          <a class="btn btn-xs btn-success btn-flat" style="margin-bottom:5px" href="{{action('RoleController@index')}} "><i class="fa fa-key"></i> Manage All Roles</a>


      </div>
      <!-- /.tab-pane -->
      
      <!-- /.tab-pane -->
    </div>
  </aside>
  <!-- /.control-sidebar -->
  <!-- Add the sidebar's background. This div must be placed
       immediately after the control sidebar -->
  <div class="control-sidebar-bg"></div>

 </div>
<!-- ./wrapper -->

<!-- REQUIRED JS SCRIPTS -->
<script type="text/javascript" src="{{asset('public'.elixir('js/all.js'))}}"></script>
<script type="text/javascript" src="{{asset('public/js/notify.min.js')}}"></script>


<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Variable to store your files
    var biometricsData;


     function prepareUpload(event)
      {
        biometricsData = event.target.files;
      }

    $('button.close').on('click',function(){
      $('.modal-body-upload').fadeIn();
      $('.modal-body-generate').fadeOut().html('');

    });

    
    // Add events
    $('input[type=file]').on('change', prepareUpload);

    $('#uploadBio').on('submit', function(e){
      <?php $icon = asset('public/img/logo-transparent.png'); $loader = asset('public/img/ajax-loader.gif');?>
      $('.modal-body-upload').fadeOut();
      $('.modal-body-generate').fadeIn().html('<h4 class="text-center"><br/><br/>Saving biometrics data into the database.<br/><br/><br/><span class="logo-mini"><img src="{{$icon}}" width="50" style="margin: 0 auto;" /></span><br/><img src="{{$loader}}" style="margin:0 auto;" /><br/><br/><br/>This could take a while. <br/>Have a break, have a Kitkat... ;) </h4>');
                

    });

     function alertAsk(){
      console.log('ask');
      $.notify("Ask Finance for your leave credits to-date.",{className:"error", globalPosition:'top left',autoHideDelay:7000, clickToHide:true} );

     }


<?php /*

    $('#uploadBio').on('submit',function(e){

      e.preventDefault(); e.stopPropagation();

      var biofile = $('#biometricsData').get(0).files.length;
      //var biometricsData = $('#biometricsData').val();
      var alertDiv = $('#alert-upload');
      var _token = $('meta[name="csrf-token"]').attr('content');

      console.log(biofile);
      if(biofile !== 0)
      {

        alertDiv.removeClass();
        alertDiv.html('');
        <?php $icon = asset('public/img/logo-transparent.png'); $loader = asset('public/img/ajax-loader.gif');?>

        var form = document.forms.namedItem("uploadBio"); // high importance!, here you need change "yourformname" with the name of your form
        var formdata = new FormData(form); // high importance!


        $.ajax({

          async: true,
          type: "POST",
          dataType: "json", // or html if you want...
          contentType: false, // high importance!
          url: "{{action('BiometricsController@upload')}}",
          data: formdata, // high importance!
          processData: false, // high importance!
          success: function(response)
            {
              
                if(typeof response.error === 'undefined')
                {
                    
                    console.log(response); 

                   
                     if(response.upload == 'success')
                       {
                        
                          $('.modal-body-generate').fadeIn().html('<h4 class="text-center"><br/><br/><span class="logo-mini"><img src="{{$icon}}" width="50" style="margin: 0 auto;" /></span><br/>Saving employee biometrics data into the database.<br/><br/><br/> This may take a while. <br/><img src="{{$loader}}" style="margin:0 auto;" /> <br/>Have a break, have a Kitkat...  </h4>');
                
                          console.log('did step1 = upload success');
                        
                       }
                     
                }
                else
                {
                    // Handle errors here
                    console.log('ERRORS from ajax1: ' + response.error);
                    $('.modal-body-generate').html('<h4 class="text-center text-danger"><br/><br/><span class="logo-mini"><img src="{{$icon}}" width="50" style="margin: 0 auto;" /></span><br/><br/><br/> Error uploading data.</h4><h5 class="text-center">Please try again.</h5> ');
           
                }

            },
            error: function(jqXHR, textStatus, errorThrown)
            {
                // Handle errors here
                console.log('ERRORS from main ajax: ' + textStatus);
                $('.modal-body-upload').fadeOut('fast',function(){

                  $('.modal-body-generate').fadeIn().html('<h4 class="text-center text-danger"><br/><br/><span class="logo-mini"><img src="{{$icon}}" width="50" style="margin: 0 auto;" /></span><br/><br/><br/> Error uploading data.</h4><h5 class="text-center">Please check the file and try again.</h5> ');
           
                });
                
                // STOP LOADING SPINNER
            },
            complete: function(response)
            {
              console.log('did step2 = complete');
              console.log(response);
              $('.modal-body-generate').fadeOut(function(){
                console.log('did step3= fadeOut');
                $('.modal-body-generate').fadeIn().html('<h4 class="text-center text-success"><br/><br/><span class="logo-mini"><img src="{{$icon}}" width="50" style="margin: 0 auto;" /></span><br/><br/><br/>Employee biometrics data saved.<br/><br/> </h4>');
                console.log('did step4= generating...');
              });   
              
                                    
            },
           
        });
        //return false;
        $('.modal-body-upload').fadeOut();
        console.log('exit ajax');

    




      } else{
        alertDiv.addClass('alert alert-danger').fadeIn();
        alertDiv.html('<span class="success"> <i class="fa fa-warning"></i> This field is required. </span>');  
      } return false;

      
     
      console.log('exit on uploaad');
    });//end upload 


*/ ?>



function validateRequired(param, availability, defaultval) {

        
        if (param == null){

          availability.addClass('alert alert-danger').fadeIn();
            availability.html('<span class="success"> <i class="fa fa-warning"></i> This field is required. </span>');  
             return false;
        }

        else if(param.length <= 0 || param === defaultval) { 
            
            availability.addClass('alert alert-danger').fadeIn();
            availability.html('<span class="success"> <i class="fa fa-warning"></i> This field is required. </span>');   
             return false;         
            

        } else{
            availability.removeClass();
            availability.html('');
            return true;
                      
        }
       

}

   
    

    

</script> 


     @yield('footer-scripts')

     

<script>

  $(function () {
   'use strict';


    $('#hrms').on('click', function(){
    $.ajax({
                url: "{{action('HomeController@logAction','14')}}",
                type: "GET",
                data: {'action': '14'},
                success: function(response){
                          console.log(response);
                          window.open('https://internal.openaccess.bpo/HR/hrms/', '_blank');

              }

        });

   });
 });
 </script>
 
@if ( isset($include_rewards_scripts) && $include_rewards_scripts===TRUE ) 
 <script src="{{ asset('/public/plugins/timepicker/bootstrap-timepicker.min.js') }}"></script>
 <script src="{{ asset('/public/plugins/pace/pace.js') }}"></script>
 <script src="{{ asset('/public/plugins/iCheck/icheck.min.js') }}"></script>
 
 @if ( isset($include_datatables) && $include_datatables===TRUE )
 <script src="{{ asset('/public/plugins/datatables/jquery.dataTables.js') }}"></script>
 <script src="{{ asset('/public/plugins/datatables/dataTables.bootstrap.min.js') }}"></script>
 @endif
 
 @if ( isset($include_jqueryform) && $include_jqueryform===TRUE )
 <script src="{{ asset('/public/plugins/jQuery/jquery.form.js') }}"></script>
 @endif
@endif
 
</body>
</html>
