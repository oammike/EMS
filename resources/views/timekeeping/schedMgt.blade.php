@extends('layouts.main')

@section('metatags')
<title>Schedule Management | EMS</title>

<style type="text/css">
    /* Sortable items */

    #qr-code-container{
    margin: 0 auto;
    width: 480px;
    height: 480px;
    background-size: cover;
    background-repeat: no-repeat;
    background-position: 50% 50%;
    }

    .sortable-list {
    background: none; /* #fcedc6;*/
    list-style: none;
    margin: 0;
    min-height: 60px;
    padding: 10px;
    }
    .sortable-item {
    background-color: #fcedc6;

    cursor: move;

    font-weight: bold;
    margin: 2px;
    padding: 10px 0;
    text-align: center;
    }

    /* Containment area */

    #containment {
    background-color: #FFA;
    height: 230px;
    }


    /* Item placeholder (visual helper) */

    .placeholder {
    background-color: #ccc;
    border: 3px dashed #fcedc0;
    min-height: 150px;
    width: 180px;
    float: left;
    margin-bottom: 5px;
    padding: 45px;
    }
</style>
@endsection

@section('bodyClasses')
<!--sidebar-collapse-->
@endsection

@section('content')


  <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Schedule Management
        
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        
        <li class="active">Employees</li>
      </ol>
    </section>

     <section class="content">
     

          <div class="row" style="margin-top:50px">
           
            

            <div class="col-lg-12"> <!-- ******************* LEFT COLUMN ***************** -->

              <div class="row">
                <div class="col-lg-6">
                  <div class="row">
                    <div class="col-lg-5"> <h4> From: <input id="from"  type="text" name="from" placeholder="{{$from}}" value="{{$from}}" class="datepicker form-control" /></h4> </div>
                    <div class="col-lg-5"> <h4> Until: <input id="to" type="text" name="to" placeholder="{{$to}}" value="{{$to}}" class="datepicker form-control" /></h4> </div>
                    <div class="col-lg-2"><a id="update" style="margin-top: 20px" class="pull-left btn btn-default btn-sm">Update Table</a> </div>
                  </div>
                 
                 
                 
                </div>
                <div class="col-lg-6">
                  <!-- <a class="btn btn-primary pull-right" style="margin-left: 2px"> Export CSV </a> -->
                 
                  
                </div>
              </div>

              <!-- TABLE: LEFT -->
              <div class="box-body">


                <!-- Custom Tabs -->
                <div class="nav-tabs-custom">
                  <a class="pull-right btn btn-primary btn-md" href="{{action('UserController@leaveMgt',['type'=>'VL','from'=>$from, 'to'=>$to])}}"><i class="fa fa-calendar"></i> Leave Management</a>
                  <ul class="nav nav-tabs">


                    <li @if($type =='CWS') class="active" @endif ><a <a href="{{action('UserController@schedMgt',['type'=>'CWS','from'=>$from, 'to'=>$to])}}"><strong class="text-primary "><i class="fa fa-2x fa-calendar"></i> CWS 
                      @if($pending_CWS)<span class="label label-warning" style="font-size: small;"> ({{count($pending_CWS)}}) </span> @endif </strong></a></li>

                      <li @if($type =='OT') class="active" @endif ><a href="{{action('UserController@schedMgt',['type'=>'OT','from'=>$from, 'to'=>$to])}}" ><strong class="text-primary"><i class="fa fa-2x fa-hourglass-half"></i> Overtime (OT)

                      @if($pending_OT)<span class="label label-warning" style="font-size: small;"> ({{count($pending_OT)}}) </span> @endif</strong></a></li>


                    <li @if($type =='DTRP') class="active" @endif ><a href="#" ><strong class="text-primary"><i class="fa fa-2x fa-history"></i> DTRP In | DTRP Out 
                      @if($pending_DTRP)<span class="label label-warning" style="font-size: small;"> ({{$pending_DTRP}}) </span> @endif</strong></a></li>
                    
                     @if ($isAdmin) 
                     <!--  <a href="{{action('UserController@create')}} " class="btn btn-sm btn-primary  pull-right"><i class="fa fa-plus"></i> Add New Employee</a>
                      -->
                      @endif


                  </ul>


                  

                  <div class="tab-content">
                    @if($canCredit)
                     
                    <!--  <a href="{{action('UserController@leaveMgt_summary')}}" class="btn btn-default pull-right" style="margin-left: 5px"><i class="fa fa-download"></i>  Summary </a> -->

                     
                    @endif

                    <div class="tab-pane active" id="tab_1"> <!-- ACTIVE EMPLOYEES -->
                      <h1 class="pull-right" style="color:#dedede">{{$label}}</h1>
                      <div class="row" style="margin-top:50px">

                        <table class="table no-margin table-bordered table-striped" id="active" width="95%" style="margin:0 auto;" >

                            @if($type=="CWS")
                              <thead>
                              <th>Lastname</th>
                              <th width="10%">Firstname</th>
                              <th>Program</th>
                              
                              <th>Production Date</th>
                              <th>Old Work Shift</th>
                              <th>New Work Shift</th>
                              <th>Status</th>
                              <th width="18%">Action</th>
                            </thead>
                            <tbody>
                                @foreach($allLeave as $vl)

                                 <tr id="row{{$vl->leaveID}}" @if( is_null($vl->isApproved)) style="background-color:#fcfdc4" @endif >
                                    <td>{{$vl->lastname}}</td>
                                    <td style="font-size: x-small;">{{$vl->firstname}}</td>
                                    <td>{{$vl->program}}</td>
                                    <td>{{date('Y-m-d', strtotime($vl->productionDate))}}</td>

                                   


                                    <td style="font-size:smaller;">
                                      
                                     

                                      @if($vl->isRD)
                                       <strong class="text-default"> Rest Day</strong><br/><br/>
                                      @else
                                      <strong class="text-default"> {{date('h:i A', strtotime($vl->timeStart_old))}} - {{date('h:i A', strtotime($vl->timeEnd_old))}}</strong>
                                      @endif

                                     
                                   

                                    </td>
                                    <td style="font-size:smaller;">

                                      @if($vl->timeStart == $vl->timeEnd)
                                        <strong class="text-success">Rest Day</strong><br/><br/>
                                      @else

                                        <strong class="text-success">{{date('h:i A', strtotime($vl->timeStart))}} - {{date('h:i A', strtotime($vl->timeEnd))}}</strong>

                                      @endif
                                   

                                    </td>


                                    <td>
                                      @if($vl->isApproved)
                                      <strong class="text-primary"><i class="fa fa-thumbs-up"></i> Approved</strong>
                                      @elseif($vl->isApproved=='0')
                                      <strong class="text-default"><i class="fa fa-thumbs-down"></i> Denied</strong>
                                      @else
                                      <strong class="text-orange"><i class="fa fa-exclamation-circle"></i> Pending</strong>
                                      @endif
                                    </td>
                                    <td>
                                      <?php $unrevokeable = \Carbon\Carbon::now()->addDays(-14); ?>

                                      @if($vl->timeStart < $unrevokeable)
                                      
                                        <a class="btn btn-xs btn-default" data-toggle="modal" data-target="#leaveModal{{$vl->leaveID}}"><i class="fa fa-info-circle"></i> Details </a>
                                        

                                       
                                          @if($vl->productionDate !== null)
                                              <a target="_blank" class="btn btn-xs btn-default" href="{{url('/')}}/user_dtr/{{$vl->userID}}?from={{date('Y-m-d',strtotime($vl->productionDate))}}&to={{date('Y-m-d',strtotime($to))}}"><i class="fa fa-calendar"></i> DTR </a>
                                          @else
                                              <a target="_blank" class="btn btn-xs btn-default" href="{{url('/')}}/user_dtr/{{$vl->userID}}?from={{date('Y-m-d',strtotime($vl->timeStart))}}&to={{date('Y-m-d',strtotime($to))}}"><i class="fa fa-calendar"></i> DTR </a>

                                          @endif
                                          

                                      

                                        @if($vl->isApproved != '1')<a class="btn btn-xs btn-default" data-toggle="modal" data-target="#delete{{$vl->leaveID}}"><i class="fa fa-trash"></i> </a>@endif
                                      
                                      @else
                                        @if($vl->isApproved == '1')
                                        <a class="btn btn-xs btn-default" data-toggle="modal" data-target="#leaveModal{{$vl->leaveID}}"><i class="fa fa-info-circle"></i> Details </a>

                                        @else
                                        <a class="btn btn-xs btn-warning" data-toggle="modal" data-target="#leaveModal{{$vl->leaveID}}"><i class="fa fa-pencil"></i> Update </a>
                                         
                                        @endif

                                       

                                          @if($vl->productionDate !== null)
                                            <a target="_blank" class="btn btn-xs btn-default" href="{{url('/')}}/user_dtr/{{$vl->userID}}?from={{date('Y-m-d',strtotime($vl->productionDate))}}&to={{date('Y-m-d',strtotime($to))}}"><i class="fa fa-calendar"></i> DTR </a>
                                          @else
                                            <a target="_blank" class="btn btn-xs btn-default" href="{{url('/')}}/user_dtr/{{$vl->userID}}?from={{date('Y-m-d',strtotime($vl->timeStart))}}&to={{date('Y-m-d',strtotime($to))}}"><i class="fa fa-calendar"></i> DTR </a>

                                          @endif
                                          

                                        @if($vl->isApproved != '1')
                                        <a class="btn btn-xs btn-default" data-toggle="modal" data-target="#delete{{$vl->leaveID}}"><i class="fa fa-trash"></i> </a>
                                        @endif
                                      @endif
                                    </td>
                                  </tr>

                                  <!-- MODALS -->
                                  <div class="modal fade" id="leaveModal{{$vl->leaveID}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                      <div class="modal-content">
                                        <div class="modal-header">
                                          
                                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                            <h4 class="modal-title text-success" id="myModalLabel"><i class="fa fa-calendar"></i> {{$label}} Details </h4>
                                          
                                        </div> 
                                        <div class="modal-body-upload" style="padding:20px;">

                                          <h4 class="pull-right" style="text-align: right;"> <a href="{{action('UserController@show',$vl->userID)}}" target="_blank"><i class="fa fa-2x fa-user"></i></a> {{$vl->lastname}}, {{$vl->firstname}} <em>({{$vl->nickname}} )</em><br/> 
                                            <strong><a href="{{action('CampaignController@show',$vl->programID)}}" target="_blank">{{$vl->program}}</a> </a></strong> <br/><br/></h4>

                                           <div class="row">

                                               
                                                <div class="col-sm-12">

                                                  <div class="row">
                                                    
                                                    
                                                    <div class="col-sm-2"><h5 class="text-primary"></h5></div>
                                                    <div class="col-sm-4 text-center"><h5 class="text-primary text-center">Work Shift</h5></div>
                                                    <div class="col-sm-6"><h5 class="text-primary">Notes</h5></div>
                                                    
                                                    
                                                  </div>

                                                   <div class="row">
                                                    
                                                    
                                                      <div class="col-sm-2">
                                                        <p><strong>{{date('M d, Y', strtotime($vl->productionDate))}}  </strong></p>
                                                      </div>

                                                      <div class="col-sm-5 text-left" style="font-size: 12px">
                                                        OLD: 
                                                        @if($vl->isRD)
                                                           <strong class="text-default"> Rest Day</strong>
                                                          @else
                                                          {{date('h:i A',strtotime($vl->timeStart_old))}} - {{date('h:i A',strtotime($vl->timeEnd_old))}}
                                                        @endif
                                                         

                                                        <br/><br/>
                                                        NEW: 
                                                         @if($vl->timeStart == $vl->timeEnd)
                                                            <strong class="text-success">Rest Day</strong><br/><br/>
                                                          @else
                                                          {{date('h:i A',strtotime($vl->timeStart))}} - {{date('h:i A',strtotime($vl->timeEnd))}}
                                                         @endif
                                                         

                                                        </div>


                                                      
                                                      <div class="col-sm-5">
                                                       <p>{{$vl->notes}} </p>
                                                      </div>

                                                   

                                                    

                                                    
                                                  
                                                  </div>

                                                 
                                                </div>

                                                 
                                                 
                                           </div>

                                           @if($vl->isApproved)

                                           <h4 class="text-success pull-right"><br/><br/><i class="fa fa-thumbs-up"></i> Approved</h4>
                                           
                                           
                                           
                                          
                                           @elseif($vl->isApproved=='0')
                                           <h4 class="text-danger pull-right"><br/><br/><i class="fa fa-thumbs-down"></i> Denied</h4>

                                           @else
                                          
                                              <button type="button" class="btn btn-default btn-md pull-right" data-dismiss="modal"style="margin-right:5px; margin-top:50px" > <i class="fa fa-times" ></i> Close </button>
                                              <?php ($type=='FL') ? $t=$vl->FLtype : $t=$type; ?>
                                              <a href="#" class="process btn btn-danger btn-md pull-right" data-leaveType="{{$t}}" data-action="0" data-id="{{$vl->leaveID}}" data-dismiss="modal"style="margin-right:5px; margin-top:50px" > <i class="fa fa-thumbs-down" ></i> Deny </a>
                                              <a href="#" class="process btn btn-success btn-md pull-right" data-leaveType="{{$t}}" data-action="1"  data-id="{{$vl->leaveID}}" data-dismiss="modal"style="margin-right:5px; margin-top:50px" > <i class="fa fa-thumbs-up" ></i> Approve </a>

                                          @endif

                                       
                                        </div> 
                                        <div class="modal-footer no-border">
                                          
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                  <!-- END MODALS -->

                                  <!-- DELETE MODAL -->
                                  <div class="modal fade" id="delete{{$vl->leaveID}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                      <div class="modal-content">
                                        <div class="modal-header">
                                          <button type="button" class="close" data-dismiss="modal">
                                            <span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
                                          </button>
                                          <h4 class="modal-title" id="myModalLabel">Delete this {{$label}}</h4></div>

                                          <div class="modal-body"><br/><br/>Are you sure you want to delete this {{$type}} request by <strong>{{$vl->firstname}} {{$vl->lastname}} of {{$vl->program}} </strong>?<br/></div>
                                          <div class="modal-footer no-border">

                                            <form action="{{$deleteLink}}{{$vl->leaveID}}" method="POST" class="btn-outline pull-right" id="deleteReq">
                                              <?php $typeid = $notifType;?>
                                              <input type="hidden" name="notifType" value="{{$typeid}}" />
                                              <input type="hidden" name="redirect" value="1" />
                                              <button type="submit" class="btn btn-primary "><i class="fa fa-check"></i> Yes</button>
                                              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                              <input type="hidden" name="_token" value="{{ csrf_token() }}" /> 
                                            </form>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                  <!-- END DELETE -->

                              

                                @endforeach
                            </tbody>
                            
                            @elseif($type=="OT")
                              <thead>
                                <th>Lastname</th>
                                <th width="10%">Firstname</th>
                                <th>Program</th>
                                
                                <th>Production Date</th>
                                
                                <th>Time</th>
                                <th>Status</th>
                                <th width="18%">Action</th>
                              </thead>
                              <tbody>
                                  @foreach($allLeave as $vl)

                                   <tr id="row{{$vl->leaveID}}" @if( is_null($vl->isApproved)) style="background-color:#fcfdc4" @endif >
                                      <td>{{$vl->lastname}}</td>
                                      <td style="font-size: x-small;">{{$vl->firstname}}</td>
                                      <td>{{$vl->program}}</td>
                                      <td>{{date('Y-m-d', strtotime($vl->productionDate))}}</td>

                                     


                                      
                                      <td style="font-size:smaller;">

                                        {{date('h:i A', strtotime($vl->timeStart))}} - {{date('h:i A', strtotime($vl->timeEnd))}} &nbsp; <strong class="text-success">[{{$vl->filed_hours}}] </strong>hours

                                     

                                      </td>


                                      <td>
                                        @if($vl->isApproved)
                                        <strong class="text-primary"><i class="fa fa-thumbs-up"></i> Approved</strong>
                                        @elseif($vl->isApproved=='0')
                                        <strong class="text-default"><i class="fa fa-thumbs-down"></i> Denied</strong>
                                        @else
                                        <strong class="text-orange"><i class="fa fa-exclamation-circle"></i> Pending</strong>
                                        @endif
                                      </td>
                                      <td>
                                        <?php $unrevokeable = \Carbon\Carbon::now()->addDays(-14); ?>

                                        @if($vl->timeStart < $unrevokeable)
                                        
                                          <a class="btn btn-xs btn-default" data-toggle="modal" data-target="#leaveModal{{$vl->leaveID}}"><i class="fa fa-info-circle"></i> Details </a>
                                          

                                         
                                            @if($vl->productionDate !== null)
                                                <a target="_blank" class="btn btn-xs btn-default" href="{{url('/')}}/user_dtr/{{$vl->userID}}?from={{date('Y-m-d',strtotime($vl->productionDate))}}&to={{date('Y-m-d',strtotime($to))}}"><i class="fa fa-calendar"></i> DTR </a>
                                            @else
                                                <a target="_blank" class="btn btn-xs btn-default" href="{{url('/')}}/user_dtr/{{$vl->userID}}?from={{date('Y-m-d',strtotime($vl->timeStart))}}&to={{date('Y-m-d',strtotime($to))}}"><i class="fa fa-calendar"></i> DTR </a>

                                            @endif
                                            

                                        

                                          @if($vl->isApproved != '1')<a class="btn btn-xs btn-default" data-toggle="modal" data-target="#delete{{$vl->leaveID}}"><i class="fa fa-trash"></i> </a>@endif
                                        
                                        @else
                                          @if($vl->isApproved == '1')
                                          <a class="btn btn-xs btn-default" data-toggle="modal" data-target="#leaveModal{{$vl->leaveID}}"><i class="fa fa-info-circle"></i> Details </a>

                                          @else
                                          <a class="btn btn-xs btn-warning" data-toggle="modal" data-target="#leaveModal{{$vl->leaveID}}"><i class="fa fa-pencil"></i> Update </a>
                                           
                                          @endif

                                         

                                            @if($vl->productionDate !== null)
                                              <a target="_blank" class="btn btn-xs btn-default" href="{{url('/')}}/user_dtr/{{$vl->userID}}?from={{date('Y-m-d',strtotime($vl->productionDate))}}&to={{date('Y-m-d',strtotime($to))}}"><i class="fa fa-calendar"></i> DTR </a>
                                            @else
                                              <a target="_blank" class="btn btn-xs btn-default" href="{{url('/')}}/user_dtr/{{$vl->userID}}?from={{date('Y-m-d',strtotime($vl->timeStart))}}&to={{date('Y-m-d',strtotime($to))}}"><i class="fa fa-calendar"></i> DTR </a>

                                            @endif
                                            

                                          @if($vl->isApproved != '1')
                                          <a class="btn btn-xs btn-default" data-toggle="modal" data-target="#delete{{$vl->leaveID}}"><i class="fa fa-trash"></i> </a>
                                          @endif
                                        @endif
                                      </td>
                                    </tr>

                                    <!-- MODALS -->
                                    <div class="modal fade" id="leaveModal{{$vl->leaveID}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                      <div class="modal-dialog">
                                        <div class="modal-content">
                                          <div class="modal-header">
                                            
                                              <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                              <h4 class="modal-title text-success" id="myModalLabel"><i class="fa fa-calendar"></i> {{$label}} Details </h4>
                                            
                                          </div> 
                                          <div class="modal-body-upload" style="padding:20px;">

                                            <h4 class="pull-right" style="text-align: right;"> <a href="{{action('UserController@show',$vl->userID)}}" target="_blank"><i class="fa fa-2x fa-user"></i></a> {{$vl->lastname}}, {{$vl->firstname}} <em>({{$vl->nickname}} )</em><br/> 
                                              <strong><a href="{{action('CampaignController@show',$vl->programID)}}" target="_blank">{{$vl->program}}</a> </a></strong> <br/><br/></h4>

                                             <div class="row">

                                                 
                                                  <div class="col-sm-12">

                                                    <div class="row">
                                                      
                                                      
                                                      <div class="col-sm-2"><h5 class="text-primary"></h5></div>
                                                      <div class="col-sm-4 text-center"><h5 class="text-primary text-center">Time</h5></div>
                                                      <div class="col-sm-6"><h5 class="text-primary">Notes</h5></div>
                                                      
                                                      
                                                    </div>

                                                     <div class="row">
                                                      
                                                      
                                                        <div class="col-sm-2">
                                                          <p><strong>{{date('M d, Y', strtotime($vl->productionDate))}}  </strong></p>
                                                        </div>

                                                        <div class="col-sm-5 text-left" style="font-size: 12px">
                                                           
                                                          
                                                            {{date('h:i A',strtotime($vl->timeStart))}} - {{date('h:i A',strtotime($vl->timeEnd))}}<br/>
                                                            <strong>[ {{$vl->filed_hours}} ] hour(s) </strong>
                                                         
                                                           

                                                         
                                                           

                                                          </div>


                                                        
                                                        <div class="col-sm-5">
                                                         <p>{{$vl->notes}} </p>
                                                        </div>

                                                     

                                                      

                                                      
                                                    
                                                    </div>

                                                   
                                                  </div>

                                                   
                                                   
                                             </div>

                                             @if($vl->isApproved)

                                             <h4 class="text-success pull-right"><br/><br/><i class="fa fa-thumbs-up"></i> Approved</h4>
                                             
                                             
                                             
                                            
                                             @elseif($vl->isApproved=='0')
                                             <h4 class="text-danger pull-right"><br/><br/><i class="fa fa-thumbs-down"></i> Denied</h4>

                                             @else
                                            
                                                <button type="button" class="btn btn-default btn-md pull-right" data-dismiss="modal"style="margin-right:5px; margin-top:50px" > <i class="fa fa-times" ></i> Close </button>
                                                <?php ($type=='FL') ? $t=$vl->FLtype : $t=$type; ?>
                                                <a href="#" class="process btn btn-danger btn-md pull-right" data-leaveType="{{$t}}" data-action="0" data-id="{{$vl->leaveID}}" data-dismiss="modal"style="margin-right:5px; margin-top:50px" > <i class="fa fa-thumbs-down" ></i> Deny </a>
                                                <a href="#" class="process btn btn-success btn-md pull-right" data-leaveType="{{$t}}" data-action="1"  data-id="{{$vl->leaveID}}" data-dismiss="modal"style="margin-right:5px; margin-top:50px" > <i class="fa fa-thumbs-up" ></i> Approve </a>

                                            @endif

                                         
                                          </div> 
                                          <div class="modal-footer no-border">
                                            
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                    <!-- END MODALS -->

                                    <!-- DELETE MODAL -->
                                    <div class="modal fade" id="delete{{$vl->leaveID}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                      <div class="modal-dialog">
                                        <div class="modal-content">
                                          <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal">
                                              <span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
                                            </button>
                                            <h4 class="modal-title" id="myModalLabel">Delete this {{$label}}</h4></div>

                                            <div class="modal-body"><br/><br/>Are you sure you want to delete this {{$type}} request by <strong>{{$vl->firstname}} {{$vl->lastname}} of {{$vl->program}} </strong>?<br/></div>
                                            <div class="modal-footer no-border">

                                              <form action="{{$deleteLink}}{{$vl->leaveID}}" method="POST" class="btn-outline pull-right" id="deleteReq">
                                                <?php $typeid = $notifType;?>
                                                <input type="hidden" name="notifType" value="{{$typeid}}" />
                                                <input type="hidden" name="redirect" value="1" />
                                                <button type="submit" class="btn btn-primary "><i class="fa fa-check"></i> Yes</button>
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                <input type="hidden" name="_token" value="{{ csrf_token() }}" /> 
                                              </form>
                                            </div>
                                          </div>
                                        </div>
                                      </div>
                                    <!-- END DELETE -->

                                

                                  @endforeach

                              </tbody>

                            @endif
                         
                          
                        </table>  
                      </div>
                        <!-- /.row -->
                      

                    </div><!--end pane1 -->
                    <!-- /.tab-pane -->

                  </div>
                  <!-- /.tab-content -->
                </div>
                <!-- nav-tabs-custom -->


                 
              </div>
                <!-- /.box-body -->
              <div class="box-footer clearfix" style="background:none">
               
                
              </div>
                <!-- /.box-footer -->
              <!-- /.box -->
            </div><!--end left -->

           


           


            
           

          
          </div><!-- end row -->

       
     </section>
          
  



@endsection


@section('footer-scripts')

<script>
  $.widget.bridge('uibutton', $.ui.button);
</script>



<!-- Page script <small> '+full.employeeNumber+'</small> -->

 <?php if($isAdmin) { ?> 
 <script type="text/javascript">

 $(function () {
   'use strict';
  
  
    $('.process').on('click',function(){
      var t = $(this).attr('data-leaveType');
      var dataid = $(this).attr('data-id');
      var isApproved = $(this).attr('data-action');
      var f = $('#from').val();
      var to = $('#to').val();
      var _token = "{{ csrf_token() }}";

      if(isApproved=='1')
        var appr="Approved";
      else var appr = "Denied";
     

      switch(t){
        case 'CWS':  var processlink = "{{action('UserCWSController@process')}}"; break;
        case 'OT':  var processlink = "{{action('UserOTController@process')}}"; break;
        case 'DTRP':  var processlink = "{{action('UserVLController@processVTO')}}"; break;
      }

      confirm('You are about to set this '+t +' as: '+appr);

      $.ajax({
                url: processlink,
                type:'POST',
                data:{ 
                  'id': dataid,
                  'isApproved': isApproved,
                  '_token':_token
                },
                success: function(res){
                  console.log(res);
                  $('#leaveModal'+dataid).modal('hide');

                    if (isApproved == '1') {
                      $('#row'+dataid).fadeOut();
                      $.notify("Requested "+t+ " marked Approved.",{className:"success",globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );
                      window.setTimeout(function(){
                        window.location.href = "{{url('/')}}/schedule_management?type={{$type}}&from="+f+"&to="+to;
                      }, 3000);
                     //;window.location.href = "{{url('/')}}/leave_management?type={{$type}}&from="+f+"&to="+to;
                    }
                   else {
                    $('#row'+dataid).fadeOut();
                    $.notify("Submitted "+requesttype+ " for "+res.firstname+" :  Denied.",{className:"error",globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );
                    window.setTimeout(function(){
                        window.location.href = "{{url('/')}}/schedule_management?type={{$type}}&from="+f+"&to="+to;
                      }, 3000);
                     // $.notify("Submitted "+requesttype+ " for "+res.firstname+" :  Denied.",{className:"error",globalPosition:'top right',autoHideDelay:7000, clickToHide:true} ); window.location.href = "{{url('/')}}/leave_management?type={{$type}}&from="+f+"&to="+to;
                   }
                   

                },
                error: function(){
                  console.log(res);
                  $('#leaveModal'+dataid).modal('hide');

                   $.notify("An error occured. Please try again later.",{className:"error",globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );

                    
                }


              });

    });

    $("#active").DataTable({

                     
                      "deferRender": true,
                      "processing":true,
                      "stateSave": false,
                      "order": [ 6, "desc" ],
                      "lengthMenu": [20, 100, 500],
                      "dom": '<"col-xs-1"f><"col-xs-11 text-right"l><"clearfix">rt<"bottom"ip><"clear">',
                      
                      //"lengthChange": true,
                      "oLanguage": {
                         "sSearch": "<strong>Refine Results</strong> <br/>To re-order entries, click on the sort icon to the right of column headers. <br/>To filter out results, just type in the search box anything you want to look for:",
                         "class": "pull-left"
                       },

                
        });

        
        var emps = $("#active").DataTable();
        var table_length = emps.page.info().recordsTotal;

          console.log("count");
          console.log(table_length);
       



   
});

 </script>

<?php } else { ?>

  <script type="text/javascript">

    $(function () {
   'use strict';

   $("#active").DataTable({
                      "ajax": "{{ action('UserController@getAllActiveUsers') }}",
                     "processing":true,
                     "deferRender": true,
                      //"stateSave": true,
                      "lengthMenu": [20, 100, 500],//[5, 20, 50, -1], 

                       "columns": [
                           { title: " ", data:'id', width:'90', class:'text-center', sorting:false, search:true, render: function ( data, type, full, meta ) {
                               var l = data+".jpg";
                              var profilepic =  "{{ url('/') }}/public/img/employees/"+l;
                              return '<a target="_blank" href="user/'+data+'"><img src="'+profilepic+'" class="img-circle" alt="No image" width="90" /></a><br/><small> '+full.employeeNumber+'<br/>[ '+data+' ]</small>';} },

                              //return '<a href="user/'+full.id+'"><img src="'+data+'" class="img-circle" alt="User Image" width="60" height="60" />

                            { title: "Name", defaultContent: "<i>none</i>" , data:'lastname', width:'200', render:function(data,type,full,meta){
                               if (full.nickname == null){
                                
                                return '<a style="font-weight:bolder" href="user/'+full.id+'" target="_blank">'+full.lastname.toUpperCase()+', '+full.firstname.toUpperCase()+' </a>';

                               }
                                
                              else
                                return '<a style="font-weight:bolder" href="user/'+full.id+'" target="_blank">'+full.lastname.toUpperCase()+', '+full.firstname+' <br/><small><em>( '+full.nickname+' )</em></small></a>';}}, 
                            
                             { title: "Position : Program", defaultContent: " ", data:'jobTitle',width:'200', render:function(data, type, full, meta ){
                              return'<small>'+data+'</small><br/><strong>'+full.program+' &nbsp;<a target="_blank" class="text-black" href="./campaign/'+full.campID+'"><i class="fa fa-external-link"></i></a></strong>';
                            } }, // 1
                             { title: "Date Hired " ,defaultContent: "<i>empty</i>", data:'dateHired',width:'80', render:function(data,type,full,meta){

                              var m = moment(data).format('YYYY-MM-DD');
                              return m;
                             } }, // 2
                            
                            { title: "Immediate Head", defaultContent: " ", data:'leaderFname',width:'90', render:function(data,type,full,meta){
                              return '<small>'+data+" "+full.leaderLname+'</small>';
                            }}, // 1
                             { title: "Location", defaultContent: " ", data:'location',width:'50', render:function(data,type,full,meta){
                              return data;
                            }}, // 1
                            
                           
                            
                           
                            

                        ],
                       

                      //"responsive":true,
                      //"scrollX":false,
                      "dom": '<"col-xs-1"f><"col-xs-11 text-right"l><"clearfix">rt<"bottom"ip><"clear">',
                      "order": [ 1, "asc" ],
                      //"lengthChange": true,
                      "oLanguage": {
                         "sSearch": "<strong>Refine Results</strong> <br/>To re-order entries, click on the sort icon to the right of column headers. <br/>To filter out results, just type in the search box anything you want to look for:",
                         "class": "pull-left"
                       },

                
        });



    




});

 </script>

<?php } ?>
<script>
  
  $(function () {
   'use strict';

   $( ".datepicker" ).datepicker();

   $('#update').on('click',function(){
      var f = $('#from').val();
      var t = $('#to').val();
      console.log(f);
      console.log(t);

      window.location.href = "{{url('/')}}/schedule_management?type={{$type}}&from="+f+"&to="+t;

   });
  


   

   $('.teamOption, .saveBtn').hide();


   $('.teamMovement').on('click', function(e) {
      e.preventDefault();
      var memberID = $(this).attr('memberID');
      var holder = "#teamOption";
      $(this).fadeOut();
      $(holder+memberID).fadeIn();
   });

   $('select[name="team"]').change(function(){    

    var memberID = $(this).find(':selected').attr('memberID'); // $(this).val();
    var newTeam = $(this).find(':selected').val();
    var saveBtn = $('#save'+memberID).fadeIn();


    
  });

   $(".saveBtn").on("click", function(){
    var memberID = $(this).attr('memberID');
    var newTeam = $("#teamOption"+memberID+" select[name=team]").find(':selected').val(); // $(this).val();
     var _token = "{{ csrf_token() }}";

    $.ajax({
                      url:"{{action('UserController@moveToTeam')}} ",
                      type:'POST',
                      data:{memberID:memberID, newTeam:newTeam, _token:_token},
                      error: function(response)
                      {
                          $("#teamOption"+memberID).fadeOut();
                        $("#teamMovement"+memberID).fadeIn();
                        
                        console.log("Error moving: "+newTeam);

                          
                          return false;
                      },
                      success: function(response)
                      {

                        $("#teamOption"+memberID).fadeOut();
                        $("#teamMovement"+memberID).fadeIn();
                        $("#row"+memberID).delay(1000).fadeOut('slow');
                        console.log("Moved to: "+newTeam);
                        console.log(response);

                          return true;
                      }
                  });




    

   });

   


      
      
   });

   

 
</script>
<!-- end Page script -->


@stop