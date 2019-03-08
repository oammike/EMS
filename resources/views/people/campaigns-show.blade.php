@extends('layouts.main')

@section('metatags')
<title> {{$campaign->name}} | Open Access EMS</title>
@endsection

@section('bodyClasses')

@endsection

@section('content')


  <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1 class="pull-left">
        @if(is_null($logo))
          
          {{$campaign->name}}

        @elseif ($campaign->name=='IMO')
        <img src="../public/img/{{$logo[0]->filename}}" height="55" class="pull-left"  />


        @else
        <img src="../public/img/{{$logo[0]->filename}}" width="150px" class="pull-left" style="margin-top: 20px" />
        @endif
        
      </h1>
      

      @if(is_null($logo))
      <a href="{{action('CampaignController@index')}}" class="btn btn-xs btn-default" style="margin-left: 60px"><i class="fa fa-arrow-left">
        @else
         <a href="{{action('CampaignController@index')}}" class="btn btn-xs btn-default" style="margin-top:30px; margin-left: 60px"><i class="fa fa-arrow-left">
          @endif
       



      </i> Back to all Programs </a>
      
      @if(!is_null($campaign->has_vicidial))
        
        
      <a style="margin-top:30px; margin-left: 10px" class="btn btn-xs btn-default" href="{{ url('campaignStats/'.$campaign->id) }}">View Campaign Stats</a>
      <a style="margin-top:30px; margin-left: 10px" class="btn btn-xs btn-default" href="{{ url('agentStats/'.$campaign->id) }}">View Agent Stats</a>
      
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

                @foreach($TLs as $leader)
                <?php $hasMembers = collect($members)->where('tlID',$leader->tlID); 
                      $hisOwn = collect($members)->where('userID',$leader->userID);
                      $actualMembers = count($hasMembers) - count($hisOwn); //we need to less himself from the count  ?>

                 

                      <div class="col-lg-6 col-md-12 col-sm-12 pull-left">

                          <ul style="list-style:none">
                            <!-- if( !in_array($leader->first()->memberID, $userData->keys()->all() ) ) collapsed-box  endif -->
                            <div class="box box-default direct-chat direct-chat-default @if(count($hasMembers) <= 0) collapsed-box @endif " style="background: rgba(256, 256, 256, 0.4)">
                                <div class="box-header with-border">
                                  
                                  <!-- THE TL -->
                                  <h3 class="box-title" style="width:80%"><a target="_blank" href="{{action('UserController@show',$leader->userID)}}">
                                    @if ( file_exists('public/img/employees/'.$leader->userID.'.jpg') )
                                    <img src="{{asset('public/img/employees/'.$leader->userID.'.jpg')}}"  class="img-circle pull-left" alt="User Image"  width="90" style="padding-right:5px">
                                    @else
                                      <img src="{{asset('public/img/useravatar.png')}}" class="img-circle pull-left" width="90" alt="Employee Image"style="padding-right:5px">

                                      @endif

                                      @if (is_null($leader->TLnick) || empty($leader->TLnick)) 
                                       <span style="text-transform:uppercase"> {{$leader->TLlname}}, {{$leader->TLfname}}</span>
                                      @else
                                      <span style="text-transform:uppercase"> {{$leader->TLlname}}, {{$leader->TLnick}}</span>

                                      @endif
                                  </a><br/>
                                    <small >{{$leader->jobTitle}}</small>




                                  </h3>

                                  <div class="box-tools pull-right">

                                    
                                    
                                    @if($canEdit)
                                    <a href="" title="Remove leader from program/campaign" data-toggle="modal" data-target="#myModal_leader{{$leader->tlID}}"><i class="fa fa-trash"></i></a>

                                    @include('layouts.modals-leader', [
                                      'modelRoute'=>'immediateHeadCampaign.disable',
                                      'modelID' => $leader->tlID, 
                                      'modelName'=>" ". $leader->TLfname . " from ". $campaign->name, 
                                      'modalTitle'=>'Remove leader: ', 
                                      'modalMessage'=>'Are you sure you want to remove him/her from this program/campaign?', 
                                      'formID'=>'disableIH',
                                      'icon'=>'glyphicon-trash' ])

                                    @endif

                                    @if (count($hasMembers) > 0 )
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse" title="Hide"><i class="fa fa-minus"></i></button>
                                    @endif



                                    
                                   
                                    
                                  </div>

                                </div>
                                <!-- /.box-header -->

                               
                                <div class="box-body" style="background: rgba(256, 256, 256, 0.4)">
                                  <!-- Conversations are loaded here -->
                                  <div class="direct-chat-messages" @if(count($hasMembers) > 7) style="min-height:380px" @endif>
                                    <!-- Message. Default to the left -->
                                    <div class="direct-chat-msg">
                                      <!--  if( in_array($leader->first()->memberID, $userData->keys()->all() ) )  -->
                                      
                                      @if (count($hasMembers) > 0)
                                      <div class="direct-chat-info clearfix">
                                        <span class="direct-chat-name pull-left"> <span class="badge bg-orange">{{count($hasMembers)}} </span> &nbsp;&nbsp;Member(s) :</span>
                                        <!-- <span class="direct-chat-timestamp pull-right">23 Jan 2:00 pm</span> -->
                                      </div>
                                      @endif
                                      <!-- /.direct-chat-info -->
                                      
                                      <!-- /.direct-chat-img -->
                                       @foreach($hasMembers as $member)

                                        @if ($member->userID !== $leader->userID) <!--make sure TL doesnt appear on his own bucket-->

                                            @if($canEdit || ($isWorkforce && !$isBackoffice))
                                            <div class="btn-group pull-left" style="margin-top:10px">
                                                <button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown">
                                                  <span class="caret"></span>
                                                </button>
                                                <ul class="dropdown-menu ">
                                                  <li><a target="_blank" href="{{action('UserController@editUser',$member->userID)}}"><i class="fa fa-pencil"></i> Edit Profile</a></li>
                                                  <li><a target="_blank" href="{{action('MovementController@changePersonnel', $member->userID)}} "><i class="fa fa-exchange"></i> Movement</a></li>
                                                  <li><a target="_blank" href="{{action('DTRController@show', $member->userID)}} "><i class="fa fa-calendar"></i> View DTR</a></li>
                                                  
                                                </ul>
                                            </div>
                                            @endif

                                            <a target="_blank" href="{{action('UserController@show',$member->userID)}}" style="text-transform:uppercase;">
                                                @if ( file_exists('public/img/employees/'.$member->userID.'.jpg') )
                                                <img src={{asset('public/img/employees/'.$member->userID.'.jpg')}} class="user-image pull-left" alt="User Image" width="60" style="margin:5px; border:solid 1px #d2d6de"><!-- direct-chat-img -->
                                                @else
                                                  <img src="{{asset('public/img/oam_favicon1-55027f4ev1_site_icon-32x32.png')}}" class="img-circle pull-left" width="60"  alt="Employee Image"style="padding-right:5px; margin:5px">
                                                  @endif
                                            </a>

                                            <div class="direct-chat-text pull-left" style="width:80%; margin-left:5px; background: rgba(179, 179, 179, 0.1)">
                                               <a class="text-black" target="_blank" href="{{action('UserController@show',$member->userID)}}" style="text-transform:uppercase; font-weight:bold">
                                                {{$member->lastname}}, {{$member->firstname}} 

                                                @if(!is_null($member->nickname)) <em style="font-size: smaller;">({{$member->nickname}})</em> @endif
                                                 </a> <br/>
                                               
                                               <small><em>{{$member->jobTitle }}</em></small>
                                            </div>

                                            <div class="clearfix">&nbsp;</div>
                                        @endif
                                         

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