@extends('layouts.main')


@section('metatags')
  <title>My Profile</title>
    <meta name="description" content="profile page">

@stop


@section('content')




<section class="content-header">

      <h1>
      Change Password
        <small></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li><a href="{{action('CampaignController@index')}}">All Campaigns</a></li>
        <li class="active">My Profile</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-heading"></div>
            <div class="box-body">
              <!--  <form name="changePassword" id="changePassword" method="POST">  -->
                {{ Form::open(['route' => 'user.updatePassword', 'class'=>'col-lg-12', 'id'=> 'changePassword','name'=>'changePassword' ]) }}

              <h4>{{$user->firstname}} {{$user->middlename}} {{$user->lastname}} <br/>
                <small>{{$user->position->name}} </small></h4> <p>&nbsp;</p> <p>&nbsp;</p>

               <label>Current Password: </label> <input required type="password" class="form-control" style="width:50%" name="currentPass" id="currentPass" />
                <div id="alert-currentPass" style="margin-top:10px; width:50%"></div>
               <label>New Password: </label> <input required type="password" class="form-control" style="width:50%" name="newPass" id="newPass" />
               <div id="alert-newPass" style="margin-top:10px; width:50%"></div>
               <label>Re-type New Password: </label> <input required type="password" class="form-control" style="width:50%" name="newPass2" id="newPass2" />
               <div id="alert-newPass2" style="margin-top:10px; width:50%">
                <span class="text-success"><i class="fa fa-check"></i> Password successfully updated. </span>

               </div>

               <p>&nbsp;</p> <p>&nbsp;</p>

                <input type="submit" class="btn btn-md btn-primary btn-flat" name='submit' value="Save" />
                    <div id="alert-submit" style="margin-top:20px"></div>

               
                  

                <!-- </form> -->
                {{Form::close()}}

            </div>

          </div>

        </div>

      </div>

     

    </section>

@stop

@section('footer-scripts')
<script>
  // $(function () {
  //   $("#members").DataTable({
  //     "responsive":true,
  //     "scrollX":true,
  //      "dom": '<"col-xs-1"f><"col-xs-11 text-right"l><"clearfix">rt<"bottom"ip><"clear">',
  //     "order": [[ 4, "desc" ]],
  //     "lengthChange": true,
  //     "oLanguage": {
  //        "sSearch": "<small>To refine search, simply type-in</small><br> <strong class='text-green'>Employee Name</strong> <strong>or</strong> <strong class='text-blue'>Email address</strong>:",
  //        "class": "pull-left"
  //      }
  //   });
    
  // });

$(function () {
   'use strict';

    $('#newPass,#newPass2').focusout(function(){ 
      
      validatePass();

    });

   //$('#changeNotice').validate();
   $('#currentPass').focusout(function(){ // Keyup function for check the user action in input
        var Username = $(this).val(); // Get the username textbox using $(this) or you can use directly $('#username')
        var AvailResult = $('#alert-currentPass');
        
        if(Username.length > 2) { // check if greater than 2 (minimum 3)
            AvailResult.html('Loading..'); // Preloader, use can use loading animation here
            var UrlToPass;
            

            // if (routeto === "Username") { UrlToPass =  }
            // else if (routeto === "Email") { UrlToPass = '{{ URL::route("user.index") }}' };

            $.post (
                '{{ URL::route("user.checkCurrentPassword") }}',{ "data": Username,},
                function( response ) {

                    if (response.correct){

                    AvailResult.removeClass('alert alert-danger').fadeIn();
                    AvailResult.html('<span class="text-success"><i class="fa fa-check"></i> Password is correct </span>');
                   
                
                    } else { 

                        AvailResult.removeClass().addClass('alert alert-danger');
                        AvailResult.html('<span class="danger">Incorrect password. Please try again </span>'); 
                        console.log(response.password);
                        console.log(response.submitted);
                        
                    }
                },'json'
            );
            

        } else{
            AvailResult.removeClass().addClass('alert alert-danger');
            AvailResult.html('Enter atleast 3 characters');
            
            
        }


    });

   $('#changePassword').on('submit', function(e) {

    var currentPass = $('#currentPass').val();
    var newPass = $('#newPass').val();
    var newPass2 = $('#newPass2').val();
    var h1 = $('#alert-currentPass');
    var h2 = $('#alert-newPass');
    var h3 = $('#alert-newPass2');
    var availability = $("#alert-newPass2");
    var _token = "{{ csrf_token() }}";

     if (!validateRequired(currentPass,h1,"") || !validateRequired(newPass,h2,"") || !validateRequired(newPass2,h3,"") || !validatePass() ){
      e.preventDefault(); e.stopPropagation(); return false; 
     } else {
              // $.ajax({
              //           url:"{{action('UserController@updatePassword')}}",
              //           type:'POST',
              //           data:{
              //             'currentPass': currentPass,
              //             'newPass': newPass,
              //             'id': "{{$user->id}}",
              //             _token:_token},

              //           error: function(response)
              //           { console.log(response); return false;
              //           },
              //           success: function(response)
              //           {
                          
              //             availability.removeClass();
              //             availability.html('');
              //             availability.addClass('alert alert-success').fadeIn();
              //             availability.html('<span> <i class="fa fa-check"></i> Your password is now updated. </span>');  

                         
              //           }
              // });

              $.post (
                '{{ URL::route("user.updatePassword") }}',{ 'currentPass': currentPass,
                          'newPass': newPass,
                          'id': "{{$user->id}}",
                          _token:_token},
                function( response ) {

                    if (response.correct){

                    availability.removeClass('alert alert-danger').fadeIn();
                    availability.html('<span class="text-success"><i class="fa fa-check"></i> Password is correct </span>');
                   
                
                    } else { 

                        availability.removeClass().addClass('alert alert-danger');
                        availability.html('<span class="danger">Incorrect password. Please try again </span>'); 
                        console.log(response.password);
                       
                        
                    }
                },'json'
            );


     }
   });
 });


function validatePass(){
      var newPass = $('#newPass').val();
      var newPass2 = $('#newPass2').val();
      
      var h2 = $('#alert-newPass');
      var h3 = $('#alert-newPass2');

      var availability = $("#alert-newPass2");

      if ( !validateRequired(newPass,h2,"") || !validateRequired(newPass2,h3,"")){
      e.preventDefault(); e.stopPropagation(); return false; 
     } else if ( newPass !== newPass2 ){

            availability.addClass('alert alert-warning').fadeIn();
            availability.html('<span> <i class="fa fa-warning"></i> Passwords do not match. </span>');   
             return false;  

     } else {
            availability.removeClass();
            availability.html('');
            return true;

     }
}


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
@stop