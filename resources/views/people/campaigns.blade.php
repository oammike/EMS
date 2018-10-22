@extends('layouts.main')

@section('metatags')
<title>Departments/Programs | OAMPI Evaluation System</title>
@endsection

@section('bodyClasses')
sidebar-collapse
@endsection

@section('content')


  <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        OAMPI Programs / Departments
        
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Programs</li>
      </ol>
    </section>

     <section class="content">
      <!-- ******** THE DATATABLE ********** -->
          <div class="row">
             <div class="col-lg-12">
              <div class="box box-primary" style="background: none;">
                      <div class="box-header ">
                      </div><!--end box-header-->
                      
                      <div class="box-body">

                        @foreach ($campaigns as $campaign)

                         @include('layouts.modals', [
                          'modelRoute'=>'campaign.destroy',
                          'modelID' => $campaign["id"], 
                          'modelName'=>$campaign["name"], 
                          'modalTitle'=>'Delete', 
                          'modalMessage'=>'Are you sure you want to delete this?', 
                          'formID'=>'deleteCamp',
                          'icon'=>'glyphicon-trash' ])

                        <!-- ******** collapsible box ********** -->
                                                <div class="box box-default collapsed-box"  style="background: none;">
                                                <div class="box-header with-border" style="background:#fff">
                                                  <h3 style="cursor:pointer" class="box-title text-primary" data-widget="collapse">
                                                    @if ($campaign["logo"]["filename"] !== null)
                                                    <img class="pull-left" src={{ asset('public/img/'.$campaign["logo"]["filename"]) }} width="145"/>

                                                    @else
                                                    <img src="{{asset('public/img/oam_favicon1-55027f4ev1_site_icon-32x32.png')}}" > <span class="text-right">{{$campaign['name']}} </span> 

                                                    @endif
                                                    </h3>
                                                 



                                                  <div class="box-tools pull-right">
                                                    
                                                    <button type="button" class="btn btn-box-tool" data-widget="collapse" title="Show members"><i class="fa fa-plus"></i>
                                                    </button>
                                                  </div>
                                                  <!-- /.box-tools -->
                                                </div>
                                                <!-- /.box-header -->
                                                <div class="box-body">
                                                  <!-- @if (Auth::user()->userType_id == 1 || Auth::user()->userType_id == 2 ) 
                                                   <a style="margin-left:20px; margin-bottom:30px;" class="btn btn-xs btn-default pull-right" data-toggle="modal" data-target="#myModal{{$campaign['id']}}"><i class="fa fa-th-large"></i> Options</a>
                                                   @endif -->
                                                   <div class="clearfix"></div>
                                                  <?php $ctr=1; ?>


                                                  @foreach( $campaign['leaders'] as $leader)


                                                  <div class="col-lg-6 col-md-12 col-sm-12 pull-left">
                                                    <a target="_blank" href="{{action('UserController@show',$leader['id'])}}">
                                                    
                                                    <h4 class="text-success">
                                                       

                                                     
                                                  </h4></a>

                                                  
                                                    <ul style="list-style:none">
                                                     


                                                      <!-- DIRECT CHAT SUCCESS -->
                                                       <div class="box box-default direct-chat direct-chat-default" @if (count($leader['members']) <= 2) style="max-height:250px" @endif>
                                                          <div class="box-header with-border">
                                                            
                                                            <!-- THE TL -->
                                                            <h3 class="box-title" style="width:80%"><a target="_blank" href="{{action('UserController@show',$leader['id'])}}">
                                                              @if ( file_exists('public/img/employees/'.$leader["id"].'.jpg') )
                                                              <img src={{asset('public/img/employees/'.$leader["id"].'.jpg')}} class="img-circle pull-left" alt="User Image" height="60" style="padding-right:5px">
                                                              @else
                                                                <img src="{{asset('public/img/oam_favicon1-55027f4ev1_site_icon-32x32.png')}}" class="img-circle pull-left" height="60" alt="Employee Image"style="padding-right:5px">

                                                                @endif
                                                              <span style="text-transform:uppercase"> {{$leader['tl']}}</span></a><br/>
                                                              <small >{{$leader['position']}}</small>

                                                            </h3>

                                                            <div class="box-tools pull-right">
                                                              <span data-toggle="tooltip" title="{{count($leader['members'])}} members" class="badge bg-green">{{count($leader['members'])}}</span>

                                                               @if (count($leader['members']) == 0 && $canDelete)
                                                              <a href="" title="Remove leader from program/campaign" data-toggle="modal" data-target="#myModal_leader{{$leader['ImmediateHead_Campaigns_id']}}"><i class="fa fa-trash"></i></a>

                                                              @include('layouts.modals-leader', [
                                                                'modelRoute'=>'immediateHeadCampaign.disable',
                                                                'modelID' => $leader['ImmediateHead_Campaigns_id'], 
                                                                'modelName'=>" ". $leader['tl'] . " from ". $campaign['name'], 
                                                                'modalTitle'=>'Remove leader: ', 
                                                                'modalMessage'=>'Are you sure you want to remove him/her from this program/campaign?', 
                                                                'formID'=>'disableIH',
                                                                'icon'=>'glyphicon-trash' ])
                      


                                                              @endif
                                                              <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                                              </button>
                                                              
                                                            </div>
                                                          </div>
                                                          <!-- /.box-header -->


                                                          <div class="box-body">
                                                            <!-- Conversations are loaded here -->
                                                            <div class="direct-chat-messages" @if(count($leader['members'] > 7)) style="min-height:380px" @endif>
                                                              <!-- Message. Default to the left -->
                                                              <div class="direct-chat-msg">
                                                                <div class="direct-chat-info clearfix">
                                                                  <span class="direct-chat-name pull-left">Member(s)</span>
                                                                  <!-- <span class="direct-chat-timestamp pull-right">23 Jan 2:00 pm</span> -->
                                                                </div>
                                                                <!-- /.direct-chat-info -->
                                                                
                                                                <!-- /.direct-chat-img -->
                                                                 @foreach($leader['members'] as $member)

                                                                <div class="btn-group pull-left" style="margin-top:10px">
                                                                    <button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown">
                                                                      <span class="caret"></span>
                                                                    </button>
                                                                    <ul class="dropdown-menu ">
                                                                      <li><a target="_blank" href="{{action('UserController@editUser',$member->id)}}"><i class="fa fa-pencil"></i> Edit Profile</a></li>
                                                                      <li><a target="_blank" href="{{action('MovementController@changePersonnel', $member->id)}} "><i class="fa fa-exchange"></i> Personnel Change Notice</a></li>
                                                                      <li><a target="_blank" href="{{action('DTRController@show', $member->id)}} "><i class="fa fa-calendar"></i> View DTR</a></li>
                                                                      
                                                                    </ul>
                                                                </div>

                                                                <a target="_blank" href="{{action('UserController@show',$member->id)}}" style="text-transform:uppercase;">
                                                                @if ( file_exists('public/img/employees/'.$member->id.'.jpg') )
                                                                <img src={{asset('public/img/employees/'.$member->id.'.jpg')}} class="user-image pull-left" alt="User Image" width="60" style="margin:5px; border:solid 1px #d2d6de"><!-- direct-chat-img -->
                                                                @else
                                                                  <img src="{{asset('public/img/oam_favicon1-55027f4ev1_site_icon-32x32.png')}}" class="img-circle pull-left" width="60"  alt="Employee Image"style="padding-right:5px; margin:5px">
                                                                  @endif</a>

                                                                <div class="direct-chat-text pull-left" style="width:80%; margin-left:5px">
                                                                   <a class="text-black" target="_blank" href="{{action('UserController@show',$member->id)}}" style="text-transform:uppercase; font-weight:bold">{{$member->lastname}}, {{$member->firstname}} </a> <br/>
                                                                   <small><em>{{OAMPI_Eval\Position::find($member->position_id)->name }}</em></small>
                                                                </div>

                                                                <div class="clearfix">&nbsp;</div>

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
                                                          <div class="box-footer">
                                                            
                                                          </div>
                                                          <!-- /.box-footer-->
                                                        </div>
                                                        <!--/.direct-chat -->

                                                     <?php // @if ($leader['employeeNumber'] !== $member->employeeNumber && $member->campaign_id == $campaign['id']) ?>

                                                      
                                                     <?php // @endif ?>

                                                    

                                                    </ul>
                                                    
                                                    

                                                  </div>
                                                  @if ($ctr % 4 == 0)

                                                 <!--  <div class="clearfix" style="padding-bottom:50px;"></div> -->

                                                 

                                                  @endif
                                                  <?php $ctr++; ?>



                                                  @endforeach

                                                  
                                                </div>
                                                <!-- /.box-body -->
                                              </div>
                                              <!-- ******** end collapsible box ********** -->

                        @endforeach
                      </div><!--end box-body-->
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
 $('#myTeam').DataTable({
    "scrollX": false,
    //"iDisplayLength": 25,
    "responsive": true,
    "lengthMenu": [[5, 20, 50, -1], [5, 20, 50, "All"]],
    "aoColumns": [
                  { "bSortable": false },
                  {"width":200},
                  {"width":200},
                  {"width":400},
                  {"width":100},
                  {"width":100},
                  {"width":90},
                  { "bSortable": false },
    ] 
    
   
   });


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

<script type="text/javascript" src="{{asset('public/js/dashboard.js')}}"></script>

@stop