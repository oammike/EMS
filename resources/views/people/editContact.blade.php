@extends('layouts.main')


@section('metatags')
  <title>Edit {{$user->firstname}}'s Contact Info</title>
    <meta name="description" content="profile page">

@stop


@section('content')




<section class="content-header">

      <h1>
      Employee Profile
        <small></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        
        <li class="active">Employee Profile</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-1"></div>
        <div class="col-xs-10">
          


          <!-- Profile Image -->
                          <div class="box box-primary">

                            <div class="box-body box-profile">
                               <!-- Widget: user widget style 1 -->
                                    <div class="box box-widget widget-user">
                                      <!-- Add the bg color to the header using any of the bg-* classes -->
                                      <div class="widget-user-header bg-black" style="background: url('{{URL:: asset("public/img/bg.jpg")}}') top left;">
                                        <h3 class="widget-user-username">{{$user->lastname}}, {{$user->firstname}} {{$user->middlename}}</h3>
                                        <h5 class="widget-user-desc">{{$user->position->name}} </h5>
                                      </div>
                                      <div class="widget-user-image">
                                        
                                         @if ( file_exists('public/img/employees/'.$user->id.'.jpg') )
                                          <img src="{{asset('public/img/employees/'.$user->id.'.jpg')}}" class="user-image" alt="User Image">
                                          @else
                                          <img src="{{asset('public/img/useravatar.png')}}" class="user-image" alt="User Image">

                                            @endif

                                          
                                        
                                      </div>
                                      <div class="box-footer">
                                        <div class="row">
                                          <div class="col-sm-6 border-right">
                                            <div class="description-block">
                                              <p class="description-header"><i class="fa fa-users margin-r-5"></i> Department / Program : </p>
                                              <span class="description-text text-primary">
                                              @if(count($user->campaign) > 1)

                                                  @foreach($user->campaign as $ucamp)
                                                      {{$ucamp->name}} , 

                                                  @endforeach

                                              @else
                                              {{$user->campaign[0]->name}}

                                              @endif
                                              </span>
                                            </div>
                                            <!-- /.description-block -->
                                          </div>
                                          <!-- /.col -->
                                          <div class="col-sm-6 ">
                                            <div class="description-block">
                                              <p class="description-header"><i class="fa fa-envelope-o margin-r-5"></i> E-mail:</p>
                                              <span><a href="mailto:{{$user->email}}"> {{$user->email}}</a></span>
                                            </div>
                                            <!-- /.description-block -->
                                          </div>
                                          <!-- /.col -->

                                          <!-- START CUSTOM TABS -->
     

                                          <p>&nbsp;</p><p>&nbsp;</p>
                                          <div class="row">
                                            <div class="col-lg-1 col-sm-12"></div>
                                            <div class="col-lg-10 col-sm-12">
                                              <!-- Custom Tabs -->
                                              <div class="nav-tabs-custom">
                                                <ul class="nav nav-tabs">
                                                  <li class="active"><a href="#tab_2" data-toggle="tab">EDIT CONTACT INFO </a></li>
                                                 
                                                 
                                                 <!--  <li class="dropdown pull-right">
                                                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                                                     <i class="fa fa-gear"></i> Actions <span class="caret"></span>
                                                    </a>
                                                    <ul class="dropdown-menu">
                                                      @if ($canMoveEmployees)
                                                      <li role="presentation"><a role="menuitem" tabindex="-1" href="{{action('MovementController@changePersonnel',$user->id)}}">Movements</a></li>@endif
                                                       @if ($canEditEmployees)
                                                       <li role="presentation"><a role="menuitem" tabindex="-1" href="{{action('UserController@editUser',$user->id)}}">Edit Employment Data</a></li>@endif

                                                       <li role="presentation"><a role="menuitem" tabindex="-1" href="{{action('UserController@editSchedule',$user->id)}}">Edit Work Schedule</a></li>
                                                       <li role="presentation"><a role="menuitem" tabindex="-1" href="{{action('UserController@editContact',$user->id)}}">Edit Contact Info</a></li>
                                                      
                                                      <li role="presentation" class="divider"></li>
                                                      <li role="presentation"><a role="menuitem" tabindex="-1" href="#">Change Status</a></li>
                                                    </ul>
                                                  </li> -->
                                                  
                                                </ul>
                                                <div class="tab-content">
                                                  {{ Form::open(['route' => ['user.updateContact', $user->id], 'method'=>'put','class'=>'col-lg-12', 'id'=> 'updateForm' ]) }}
                                                  


                                                  <div class="tab-pane active" id="tab_2"> <div id="alert-submit" style="margin-top:10px"></div>
                                                    <h4 style="margin-top:30px"> <input type="submit" id="submit" href="#" class="btn btn-sm btn-success pull-right" value="Save Changes"></input>

                                                      <i class="fa fa-clock-o"></i> CONTACT INFORMATION  <br/><br/></h4>
                                                    <div class="row">
                                                      <div class="col-lg-8">
                                                        <strong> Nickname or Alias: </strong> <br/><br/>
                                                        <input required name="nickname" class="form-control" type="text" value="{{$user->nickname}}" style="margin-bottom:5px" @if(empty($user->nickname)) placeholder="Nickname or alias" @endif></input>
                                                        <br/><br/>
                                                        <strong> External E-mail: </strong> <br/><br/>
                                                        <input name="external" class="form-control" type="text" value="{{$user->external}}" style="margin-bottom:5px" @if(empty($user->external)) placeholder="@openaccessbpo.com OR @openaccessmarketing.com " @endif></input>

                                                        <strong><br/><br/> Current Address: </strong> <br/><br/>

                                                        <input required name="currentAddress1" class="form-control" type="text" value="{{$user->currentAddress1}}" style="margin-bottom:5px" @if(empty($user->currentAddress1)) placeholder="House / Bldg. / Lot number, Street" @endif></input>
                                                        <input required name="currentAddress2" class="form-control" type="text" value="{{$user->currentAddress2}}"style="margin-bottom:5px" @if(empty($user->currentAddress2)) placeholder="Brgy. / Municipality" @endif></input>
                                                        <input  name="currentAddress3" class="form-control" type="text" value="{{$user->currentAddress3}}"style="margin-bottom:5px" @if(empty($user->currentAddress3)) placeholder="City / Province, Zipcode" @endif></input>

                                                          <p>&nbsp;</p> <p>&nbsp;</p> 

                                                           <strong> Permanent Address: </strong><p>&nbsp;</p>
                                                            <input required name="permanentAddress1" class="form-control" type="text" value="{{$user->permanentAddress1}}" style="margin-bottom:5px" @if(empty($user->permanentAddress1)) placeholder="House / Bldg. / Lot number, Street" @endif></input>
                                                            <input required name="permanentAddress2" class="form-control" type="text" value="{{$user->permanentAddress2}}"style="margin-bottom:5px" @if(empty($user->permanentAddress2)) placeholder="Brgy. / Municipality" @endif></input>
                                                            <input  name="permanentAddress3" class="form-control" type="text" value="{{$user->permanentAddress3}}"style="margin-bottom:5px" @if(empty($user->permanentAddress3)) placeholder="City / Province, Zipcode" @endif></input>

                                                      </div>
                                                      <div class="col-lg-4">
                                                        <strong> Mobile Number: </strong>
                                                        <input required name="mobileNumber" class="form-control" type="text" value="{{$user->mobileNumber}}"style="margin-bottom:5px" @if(empty($user->mobileNumber)) placeholder="+63 xxx xxxxxxx" @endif ></input>
                                                        <p>&nbsp;</p> <p>&nbsp;</p> 
                                                        <strong> Telephone Number: </strong>
                                                        <input name="phoneNumber" class="form-control" type="text" value="{{$user->phoneNumber}}"style="margin-bottom:5px" @if(empty($user->phoneNumber)) placeholder="(xx) xxx-xxxx" @endif ></input>

                                                       
                                                       
                                                      <br/> <br/> <br/>

                                                     
                                                        
                                                        
                                                         
                                                       </div>

                                                       
                                                    </div>
                                                    
                                                   

                                                  </div>
                                                  <!-- /.tab-pane -->



                                                  {{Form::close()}}
                                                </div>
                                                <!-- /.tab-content -->
                                              </div>
                                              <!-- nav-tabs-custom -->
                                            </div>
                                            <!-- /.col -->
                                            <div class="col-lg-1 col-sm-12"></div>

                                           
                                          </div>
                                          <!-- /.row -->
                                          <!-- END CUSTOM TABS -->
                                          
                                          <!-- /.col -->
                                          

                                      </div>
                                    </div>
                                    <!-- /.widget-user -->

                            </div>
                            

                            <!-- /.box-body -->
                          </div>

        </div>
        <div class="col-xs-1"></div>

      </div>

     

    </section>

@stop

@section('footer-scripts')
<script>


  
  $(function () {
   'use strict';


   $('#updateForm').on('submit', function(e) {

    e.preventDefault();
    console.log('Enter submit');
      var _token = "{{ csrf_token() }}";
      //var userType_id = $('input[name="userType_id" ]:checked').val();
      var currentAddress1 = $('input[name="currentAddress1"]').val();
      var currentAddress2 = $('input[name="currentAddress2"]').val();
      var currentAddress3 = $('input[name="currentAddress3"]').val();
      var permanentAddress1 = $('input[name="permanentAddress1"]').val();
      var permanentAddress2 = $('input[name="permanentAddress2"]').val();
      var permanentAddress3 = $('input[name="permanentAddress3"]').val();
      var mobileNumber = $('input[name="mobileNumber"]').val();
      var phoneNumber = $('input[name="phoneNumber"]').val();
      var nickname = $('input[name="nickname"]').val();
      var external = $('input[name="external"]').val();
      

      // if (currentAddress1 == "{{$user->currentAddress1}}" && currentAddress2 == "{{$user->currentAddress2}}" && currentAddress3 == "{{$user->currentAddress3}}" && permanentAddress1 == "{{$user->permanentAddress1}}" && "{{$user->permanentAddress2}}" == permanentAddress2 && permanentAddress3 == "{{$user->permanentAddress3}}" && mobileNumber == "{{$user->mobileNumber}}" && phoneNumber == "{{$user->phoneNumber}}")
      // {
      //   alert("Nothing's changed...");
      //   return false;
      // }

       $.ajax({
            url:"{{action('UserController@updateContact', $user->id)}}",
            type:'POST',
            data:{

              
              'currentAddress1': currentAddress1,
              'currentAddress2': currentAddress2,
              'currentAddress3': currentAddress3,
              'permanentAddress1': permanentAddress1,
              'permanentAddress2': permanentAddress2,
              'permanentAddress3': permanentAddress3,
              'mobileNumber': mobileNumber,
              'phoneNumber': phoneNumber,
              'nickname': nickname,
              'external': external,
              '_token':_token
            },

           
            success: function(response2)
            {
              console.log(response2);
               $('#submit').fadeOut();
                      var htmcode = "<span class=\"success\"> <i class=\"fa fa-save\"></i> Employee  Contact Information updated. <br /><br/>";
                     
                      
                      $('#alert-submit').addClass('alert alert-success').fadeIn().html(htmcode).delay(2000).fadeOut(function(){ window.location = "{{action('UserController@show', $user->id)}}";}); 

            }
          });

     

      
     
      return false;
    }); //end addEmployee

       

});
   


</script>
@stop