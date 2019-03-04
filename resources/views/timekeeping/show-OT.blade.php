@extends('layouts.main')


@section('metatags')
  <title>{{$user->firstname}}'s OT</title>
    <meta name="description" content="profile page">

@stop


@section('content')




<section class="content-header">

      <h1>Overtime Request<small></small>
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
                          <div class="box box-primary"  style="background: rgba(256, 256, 256, 0.5)">

                            <div class="box-body box-profile"  style="background: rgba(256, 256, 256, 0.1)">
                               <!-- Widget: user widget style 1 -->
                                    <div class="box box-widget widget-user" style="background: rgba(256, 256, 256, 0.1)">
                                      <!-- Add the bg color to the header using any of the bg-* classes -->
                                      <div class="widget-user-header bg-black" style="background: url('{{URL:: asset("public/img/bg.jpg")}}') top left;">
                                        <h3 style="text-shadow: 1px 2px #000000; text-transform:uppercase" class="widget-user-username">{{$user->lastname}}, {{$user->firstname}} {{$user->middlename}}</h3>
                                        <h5 style="text-shadow: 1px 2px #000000;"  class="widget-user-desc">{{$user->position->name}} </h5>
                                      </div>
                                      <div class="widget-user-image"><a  class="user-image"href="{{action('UserController@show',$user->id)}}"><img  width="90" src="{{$profilePic}}" class="user-image" alt="User Image"></a></div>
                                      <div class="box-footer"  style="background: rgba(256, 256, 256, 0.1)">
                                        <div class="row">
                                          <div class="col-sm-6">
                                            <div class="description-block">
                                              <p class="description-header"><i class="fa fa-users margin-r-5"></i> Department / Program : </p>
                                              <span class="description-text text-primary">
                                              @if(count($camps) > 1)

                                                  @foreach($camps as $ucamp)
                                                      {{$ucamp->name}} , 

                                                  @endforeach

                                              @else
                                              {{$camps->first()->name}}

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

                                              
                                              <h4><i class="fa fa-clock-o"></i> Overtime<br/><br/><br/></h4>

                                              <table class="table">
                                                 {{ Form::open(['route' => ['user_ot.process', $OT->id], 'method'=>'post','class'=>'col-lg-12', 'id'=> 'processOT' ]) }}
                                                <tr>
                                                  <th>Date Requested</th>
                                                  <th>Production Date</th>
                                                  <th style="width:30%">OT details</th>
                                                  <th></th>
                                                </tr>
                                                <tr>
                                                  <td>{{$OT->created_at}} </td>
                                                  <td>{{$details[0]['productionDate']}} </td>

                                                 
                                                  <td>

                                                    <strong>Billable hours:  &nbsp;</strong> {{$details[0]['billableHours']}}<br/>
                                                    <strong class="text-danger">Filed OT hours: {{$details[0]['filedHours']}}</strong><br/>
                                                    Time: <strong>{{$details[0]['timeStart']}} - {{$details[0]['timeEnd']}} </strong> <br/>
                                                    <strong>Reason: </strong> <em> <small>{{$details[0]['reason']}}</small></em></td>

                                                  @if (is_null($OT->isApproved))
                                                  <td>
                                                    <a href="#" id="approve" data-action="1" class="updateCWS btn btn-sm btn-success pull-left" style="margin-right: 5px"><i class="fa fa-thumbs-up"></i> Approve</a>
                                                    <a href="#" id="reject" data-action="0" class="updateCWS btn btn-sm btn-danger pull-left"><i class="fa fa-thumbs-down"></i> Deny</a>

                                                  </td>
                                                  @else

                                                  <td>
                                                    @if($OT->isApproved) <h4 class="text-success">Approved</h4>
                                                    @else <h4 class="text-danger">Denied</h4>@endif

                                                  </td>


                                                  @endif
                                                </tr>
                                                <tr>
                                                  <td colspan="4" align="right"> <br/><br/>
                                                    <a href="{{action('NotificationController@index')}}" class="text-primary pull-left"><i class="fa fa-arrow-left"></i> Back to All Notifications</a>
                                             

                                                    @if (Auth::user()->id == $OT->user_id && $OT->isApproved)

                                                     <a class="process btn btn-xs btn-primary text-center" data-action="delete"><i class="fa fa-undo"></i> Revoke </a>

                                                    @else

                                                     <a class="process btn btn-xs btn-primary text-center" data-action="delete"><i class="fa fa-trash"></i> Delete </a>

                                                    @endif
                                                    
                                                   <!--  <a class="process btn btn-xs btn-warning" data-action="edit"><i class="fa fa-pencil"></i> Edit </a> -->
                                                  <a href="{{action('DTRController@show', $user->id)}}" class="btn btn-xs btn-default"><i class="fa fa-calendar"></i> View DTR</a>

                                                  <a style="margin-left: 5px" href="{{action('UserController@userRequests', $user->id)}}" class="btn btn-xs btn-flat btn-sm btn-default pull-right"><i class="fa fa-clipboard"></i> View All Requests</a>

                                                </td>
                                                </tr>
                                                {{Form::close()}}
                                              </table><br/><br/><br/><br/>
                                              <div id="alert-submit"></div>
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


    $('.process').on('click', function(e)
    {
      e.preventDefault(); e.stopPropagation();
      var _token = "{{ csrf_token() }}";
      var OTaction = $(this).attr('data-action');

      @if($OT->isApproved)
      var reply = confirm("\n\nAre you sure you want to revoke this approved Overtime request? ");

      @else
      var reply = confirm("\n\nAre you sure you want to delete this Overtime request? ");
      @endif

        if (reply == true)
        {

          $.ajax({
                    url: "{{action('UserOTController@deleteOT', $OT->id)}}",
                    type:'POST',
                    data:{

                      'id': {{$OT->id}},
                      '_token':_token
                    },
                    success: function(response)
                    {
                      console.log(response);
                      $('.table').fadeOut();
                      var htmcode = "<span class=\"success\"> <i class=\"fa fa-save\"></i> OT request deleted. <br /><br/>";
                      $('#alert-submit').addClass('alert alert-success').fadeIn().html(htmcode).delay(2000).fadeOut(
                        function(event)
                        { 
                         window.location.replace("../notification");
                       }); 
                    }
                  });
        } else {}
    });


    $('.updateCWS').on('click', function(e)
    {
      e.preventDefault(); e.stopPropagation();
      var _token = "{{ csrf_token() }}";
      var OTaction = $(this).attr('data-action');

      $.ajax({
                url: "{{action('UserOTController@update', $OT->id)}}",
                type:'PUT',
                data:{

                  'id': {{$OT->id}},
                  'isApproved':OTaction,
                  '_token':_token
                },

               
                success: function(response)
                {
                  console.log(response);
                  $('.table').fadeOut();
                  var htmcode = "<span class=\"success\"> <i class=\"fa fa-save\"></i> OT request updated. <br /><br/>";
                  $('#alert-submit').addClass('alert alert-success').fadeIn().html(htmcode).delay(2000).fadeOut(function(event)
                  { 
                   window.location.replace("../notification");
                   console.log(response);
                    
                  }); 
                }
      });


    });

});

</script>
@stop