@extends('layouts.main')

@section('metatags')
<title> {{$campaign->first()->name}} | OAMPI Evaluation System</title>
@endsection

@section('bodyClasses')

@endsection

@section('content')


  <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1 class="pull-left">
        @if(is_null($campaign[0]->filename))
          
          {{$campaign[0]->name}}

        @elseif ($campaign[0]->name=='IMO')
        <img src="../public/img/{{$campaign[0]->filename}}" height="55" class="pull-left"  />


        @else
        <img src="../public/img/{{$campaign[0]->filename}}" width="150px" class="pull-left" style="margin-top: 20px" />
        @endif
        
      </h1>
      

      @if(is_null($campaign[0]->filename))
      <a href="{{action('CampaignController@index')}}" class="btn btn-xs btn-default" style="margin-left: 60px"><i class="fa fa-arrow-left">
        @else
         <a href="{{action('CampaignController@index')}}" class="btn btn-xs btn-default" style="margin-top:30px; margin-left: 60px"><i class="fa fa-arrow-left">
          @endif
       



      </i> Back to all Programs </a>
      
      @if(!is_null($campaign[0]->campaign_id))
      <a style="margin-top:30px; margin-left: 10px" class="btn btn-xs btn-default" href="{{ url('campaignStats/'.$campaign[0]->campaign_id) }}">View Campaign Stats</a>
      
      <a style="margin-top:30px; margin-left: 10px" class="btn btn-xs btn-default" href="{{ url('agentStats/'.$campaign[0]->campaign_id) }}">View Agent Stats</a>
      
      @endif
      
      <div class="clearfix"></div>
      <ol class="breadcrumb">
        <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Programs</li>
      </ol>
    </section>

     <section class="content">
      <!-- ******** THE DATATABLE ********** -->
          <div class="row">
             <div class="col-lg-12">
              <div class="box box-primary" style="background: none"><br/><br/><br/>

                @foreach($campaign->groupBy('ihCamp_id') as $leader)

                  @if( !$leader->first()->disabled )

                      <div class="col-lg-6 col-md-12 col-sm-12 pull-left">

                          <ul style="list-style:none">
                            <!-- @if( !in_array($leader->first()->memberID, $userData->keys()->all() ) ) collapsed-box  @endif -->
                            <div class="box box-default direct-chat direct-chat-default @if(count($leader) <= 0) collapsed-box @endif " style="background: rgba(256, 256, 256, 0.4)">
                                <div class="box-header with-border">
                                  
                                  <!-- THE TL -->
                                  <h3 class="box-title" style="width:80%"><a target="_blank" href="{{action('UserController@show',$leader->first()->user_TL_id)}}">
                                    @if ( file_exists('public/img/employees/'.$leader->first()->user_TL_id.'.jpg') )
                                    <img src="{{asset('public/img/employees/'.$leader->first()->user_TL_id.'.jpg')}}"  class="img-circle pull-left" alt="User Image"  width="90" style="padding-right:5px">
                                    @else
                                      <img src="{{asset('public/img/useravatar.png')}}" class="img-circle pull-left" width="90" alt="Employee Image"style="padding-right:5px">

                                      @endif

                                      @if (empty($leader->first()->nickname))
                                       <span style="text-transform:uppercase"> {{$leader->first()->TLlname}}, {{$leader->first()->TLfname}}</span>
                                      @else
                                      <span style="text-transform:uppercase"> {{$leader->first()->TLlname}}, {{$leader->first()->nickname}}</span>

                                      @endif
                                  </a><br/>
                                    <small >{{$leader->first()->jobTitle}}</small>




                                  </h3>

                                  <div class="box-tools pull-right">

                                    
                                    
                                    @if($canEdit)
                                    <a href="" title="Remove leader from program/campaign" data-toggle="modal" data-target="#myModal_leader{{$leader->first()->ihCamp_id}}"><i class="fa fa-trash"></i></a>

                                    @include('layouts.modals-leader', [
                                      'modelRoute'=>'immediateHeadCampaign.disable',
                                      'modelID' => $leader->first()->ihCamp_id, 
                                      'modelName'=>" ". $leader->first()->TLfname . " from ". $campaign->first()->name, 
                                      'modalTitle'=>'Remove leader: ', 
                                      'modalMessage'=>'Are you sure you want to remove him/her from this program/campaign?', 
                                      'formID'=>'disableIH',
                                      'icon'=>'glyphicon-trash' ])

                                      @endif

                                    @if( in_array($leader->first()->memberID, $userData->keys()->all() ) )
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse" title="Hide"><i class="fa fa-minus"></i></button>
                                    <!-- <span data-toggle="tooltip" title="{{count($leader)}} members" class="badge bg-green">{{count($leader)}}</span> -->
                                    @endif



                                    
                                   
                                    
                                  </div>

                                </div>
                                <!-- /.box-header -->

                               
                                <div class="box-body" style="background: rgba(256, 256, 256, 0.4)">
                                  <!-- Conversations are loaded here -->
                                  <div class="direct-chat-messages" @if(count($leader)> 7) style="min-height:380px" @endif>
                                    <!-- Message. Default to the left -->
                                    <div class="direct-chat-msg">
                                       @if( in_array($leader->first()->memberID, $userData->keys()->all() ) ) 
                                      <div class="direct-chat-info clearfix">
                                        <span class="direct-chat-name pull-left">Member(s)</span>
                                        <!-- <span class="direct-chat-timestamp pull-right">23 Jan 2:00 pm</span> -->
                                      </div>
                                      @endif
                                      <!-- /.direct-chat-info -->
                                      
                                      <!-- /.direct-chat-img -->
                                       @foreach($leader as $member)

                                       @if( in_array($member->memberID, $userData->keys()->all() ) ) 

                                        @if($canEdit)
                                        <div class="btn-group pull-left" style="margin-top:10px">
                                            <button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown">
                                              <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu ">
                                              <li><a target="_blank" href="{{action('UserController@editUser',$member->memberID)}}"><i class="fa fa-pencil"></i> Edit Profile</a></li>
                                              <li><a target="_blank" href="{{action('MovementController@changePersonnel', $member->memberID)}} "><i class="fa fa-exchange"></i> Personnel Change Notice</a></li>
                                              <li><a target="_blank" href="{{action('DTRController@show', $member->memberID)}} "><i class="fa fa-calendar"></i> View DTR</a></li>
                                              
                                            </ul>
                                        </div>
                                        @endif

                                        <a target="_blank" href="{{action('UserController@show',$member->memberID)}}" style="text-transform:uppercase;">
                                        @if ( file_exists('public/img/employees/'.$member->memberID.'.jpg') )
                                        <img src={{asset('public/img/employees/'.$member->memberID.'.jpg')}} class="user-image pull-left" alt="User Image" width="60" style="margin:5px; border:solid 1px #d2d6de"><!-- direct-chat-img -->
                                        @else
                                          <img src="{{asset('public/img/oam_favicon1-55027f4ev1_site_icon-32x32.png')}}" class="img-circle pull-left" width="60"  alt="Employee Image"style="padding-right:5px; margin:5px">
                                          @endif</a>

                                        <div class="direct-chat-text pull-left" style="width:80%; margin-left:5px; background: rgba(179, 179, 179, 0.1)">
                                           <a class="text-black" target="_blank" href="{{action('UserController@show',$userData[$member->memberID][0]->id)}}" style="text-transform:uppercase; font-weight:bold">
                                            {{$userData[$member->memberID][0]->lastname }}, {{$userData[$member->memberID][0]->firstname }}
                                             </a> <br/>
                                           <small><em>{{$userData[$member->memberID][0]->jobTitle }}</em></small>
                                        </div>

                                        <div class="clearfix">&nbsp;</div>
                                         @endif <!--if may members-->

                                        @endforeach


                                      <!-- /.direct-chat-text -->
                                    </div>
                                    <!-- /.direct-chat-msg -->

                                    
                                    <!-- /.direct-chat-msg -->
                                  </div>
                                  <!--/.direct-chat-messages-->

                                  
                                  <!-- /.direct-chat-pane -->
                                </div>
                                <!-- /.box-body -->
                               


                                <div class="box-footer" style="background: rgba(256, 256, 256, 0.8)">
                                  
                                </div>
                                <!-- /.box-footer-->
                            </div>
                              <!--/.direct-chat -->

                           

                          

                          </ul>
                                                          
                                                          

                      </div>
                  @endif

                @endforeach

               






                <div class="clearfix"></div>
                     
              </div><!--end box-primary-->

          </div><!--end main row-->
      </section>
          



@endsection


@section('footer-scripts')

<script>
  $.widget.bridge('uibutton', $.ui.button);
</script>



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