@extends('layouts.main')


@section('metatags')
  <title>{{$user->firstname}}'s DTRP</title>
    <meta name="description" content="profile page">

@stop


@section('content')




<section class="content-header">

      <h1>DTRP - {{$details[0]['logType']}} <small></small>
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

                            <div class="box-body box-profile" style="background: rgba(256, 256, 256, 0.1)">
                               <!-- Widget: user widget style 1 -->
                                    <div class="box box-widget widget-user"  style="background: rgba(256, 256, 256, 0.1)">
                                      <!-- Add the bg color to the header using any of the bg-* classes -->
                                      <div class="widget-user-header bg-black" style="background: url('{{URL:: asset("public/img/bg.jpg")}}') top left;">
                                        <h3 style="text-shadow: 1px 2px #000000; text-transform:uppercase" class="widget-user-username">{{$user->lastname}}, {{$user->firstname}} {{$user->middlename}}</h3>
                                        <h5 style="text-shadow: 1px 2px #000000;"  class="widget-user-desc">{{$user->position->name}} </h5>
                                      </div>
                                      <div class="widget-user-image"><a  class="user-image"href="{{action('UserController@show',$user->id)}}"><img  width="90" src="{{$profilePic}}" class="user-image" alt="User Image"></a></div>
                                      <div class="box-footer" style="background: rgba(256, 256, 256, 0.1)">
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

                                             
                                              <h4><i class="fa fa-clock-o"></i> Daily Time Record Problem - {{$details[0]['logType']}}<br/><br/><br/></h4>

                                              <table class="table">
                                                 {{ Form::open(['route' => ['user_dtrp.process', $DTRP->id], 'method'=>'post','class'=>'col-lg-12', 'id'=> 'editOT' ]) }}
                                                <tr>
                                                  <th>Date Requested</th>
                                                  <th>Production Date</th>
                                                  <th style="width:30%">Details</th>
                                                  <th></th>
                                                </tr>
                                                <tr>
                                                  <td>{{$DTRP->created_at}} </td>
                                                  <td>{{$details[0]['productionDate']}} </td>

                                                 
                                                  <td>
                                                   
                                                    <strong>{{$details[0]['logType']}} time:  &nbsp;</strong> {{$details[0]['logTime']}}<br/>

                                                    
                                                    
                                                    <strong>NOTES: </strong> <em> <small>{{$details[0]['notes']}}</small></em></td>

                                                  @if (is_null($DTRP->isApproved))
                                                  <td>
                                                    <a href="#" id="approve" data-action="1" class="updateCWS btn btn-xs btn-success pull-right"><i class="fa fa-thumbs-up"></i> Approve</a>
                                                    <a style="margin-right: 5px" href="#" id="reject" data-action="0" class="updateCWS btn btn-xs btn-danger pull-right"><i class="fa fa-thumbs-down"></i> Deny</a>

                                                  </td>
                                                  @else

                                                  <td>
                                                    @if($DTRP->isApproved) <h4 class="text-success">Approved</h4>
                                                    @else <h4 class="text-danger">Denied</h4>@endif

                                                  </td>


                                                  @endif
                                                </tr>
                                                {{Form::close()}}
                                              </table>
                                              <br/><br/><br/>
                                               <a style="margin-left: 5px" href="{{action('DTRController@show', $user->id)}}" class="btn btn-flat btn-sm btn-default pull-right"><i class="fa fa-calendar"></i> View DTR</a>

                                              <a href="{{action('NotificationController@index')}}" class="text-primary pull-left"><i class="fa fa-arrow-left"></i> Back to All Notifications</a>
                                              <br/><br/>
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


    $('.updateCWS').on('click', function(e)
    {
      e.preventDefault(); e.stopPropagation();
      var _token = "{{ csrf_token() }}";
      var cwsAction = $(this).attr('data-action');

      $.ajax({
                url: "{{action('UserDTRPController@process')}}",
                type:'POST',
                data:{

                  
                  'id': {{$DTRP->id}},
                  'isApproved': cwsAction,
                  '_token':_token
                },

               
                success: function(response)
                {
                  console.log(response);
                  $('.table').fadeOut();
                  var htmcode = "<span class=\"success\"> <i class=\"fa fa-save\"></i> DTRP Log IN updated. <br /><br/>";
                  $('#alert-submit').addClass('alert alert-success').fadeIn().html(htmcode).delay(2000).fadeOut(function()
                  { 
                    //window.location = "{{action('DTRController@show', $user->id)}}";
                    window.location.replace("../notification");
                  }); 
                }
      });


    });


  });
</script>
@stop