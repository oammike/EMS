@extends('layouts.main')

@section('metatags')
<title>DTRP Management | EMS</title>

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
        DTRP Management
        
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
                  <a class="pull-right btn btn-primary btn-md" style="margin-left: 5px" href="{{action('UserController@schedMgt',['type'=>'CWS','from'=>$from, 'to'=>$to])}}"><i class="fa fa-history"></i> Schedule Management</a>
                   <a class="pull-right btn btn-primary btn-md" href="{{action('UserController@leaveMgt',['type'=>'VL','from'=>$from, 'to'=>$to])}}"><i class="fa fa-plane"></i> Leave Management</a>
                  <ul class="nav nav-tabs">


                    <li @if($type =='IN') class="active" @endif ><a <a href="{{action('UserDTRPController@manage',['type'=>'IN','from'=>$from, 'to'=>$to])}}"><strong class="text-primary "><i class="fa fa-2x fa-sign-in"></i>DTRP IN 
                      <span class="label label-warning" style="font-size: small;"> {{$allIns}} </span>  </strong></a></li>
                    <li @if($type =='OUT') class="active" @endif ><a href="{{action('UserDTRPController@manage',['type'=>'OUT','from'=>$from, 'to'=>$to])}}" ><strong class="text-primary"><i class="fa fa-2x fa-sign-out"></i>DTRP OUT
                      <span class="label label-warning" style="font-size: small;"> ({{$allOuts}}) </span></strong></a></li>

                    <li @if($type =='OLD') class="active" @endif ><a href="{{action('UserDTRPController@manage',['type'=>'OLD','from'=>$from, 'to'=>$to])}}" ><strong class="text-primary"><i class="fa fa-2x fa-clock-o"></i> Old DTRP Process
                      <span class="label label-warning" style="font-size: small;"> ({{$allOlds}} ) </span></strong></a></li>
                   
                     


                  </ul>


                  

                  <div class="tab-content">
                    

                    <div class="tab-pane active" id="tab_1"> <!-- ACTIVE EMPLOYEES -->
                      @if($type == 'OLD')<br/><br/><a href="{{action('UserDTRPController@manage',['type'=>'OLD','from'=>$from, 'to'=>$to, 'dl'=>1])}}" class="btn btn-xs btn-success pull-right"><i class="fa fa-download"></i> Export CSV</a><br/>@endif
                      
                      <h1 class="pull-right" style="color:#dedede">{{$label}}</h1>
                      <div class="row" style="margin-top:50px">

                        <table class="table table-bordered table-striped" id="active" width="95%" style="margin:0 auto;" >

                          <thead>
                            <th>Lastname</th>
                            <th width="10%">Firstname</th>
                            <th>Program</th>
                            
                            <th>Production Date</th>
                            <th>Type</th>
                            <th>Approver Status</th>
                            
                            @if($type !== 'OLD')<th>Details</th><th>Validation Status</th>@endif
                            <th width="20%">Action</th>
                          </thead>
                          <tbody>
                            @foreach($allDTRP as $vl)

                             <tr id="row{{$vl->id}}" @if( is_null($vl->isApproved)) style="background-color:#fcfdc4" @endif >
                                <td>{{$vl->lastname}}</td>
                                <td style="font-size: x-small;">{{$vl->firstname}}</td>
                                <td>{{$vl->program}}</td>

                                <!--PROD DATE-->
                                <td style="font-size: smaller;">{{$vl->productionDate}} </td>
                                <td>{{$vl->dtrpType}}</td>
                                

                                <!--STATUS-->
                                <td style="font-size: smaller;">
                                    @if($vl->isApproved == '1')
                                    <strong class="text-primary"><i class="fa fa-thumbs-up"></i> Approved</strong>
                                    @elseif ($vl->isApproved == '0')
                                    <strong class="text-default"><i class="fa fa-thumbs-down"></i> Denied</strong>
                                    @else
                                    <strong class="text-orange"><i class="fa fa-exclamation-circle"></i> Pending</strong>
                                    @endif
                                </td>

                               

                                @if($type !== 'OLD')
                                   
                                  <!--DETAILS-->
                                  <td>
                                    <strong style="font-size: x-small;" class="text-danger">[{{$vl->reasonID}}] {{$vl->reason}}</strong><br/><br/>
                                    <p style="font-style: italic;">{{$vl->notes}}</p>
                                   
                                  </td>

                                   <!--VALIDATION-->
                                  <th>
                                    @if($vl->validated)
                                      <strong class="text-success"><i class="fa fa-thumbs-up"></i> Valid</strong>
                                      @elseif($vl->validated=='0')
                                      <strong class="text-default"><i class="fa fa-thumbs-down"></i> Rejected</strong>
                                      @else
                                        @if($vl->isApproved)
                                          <strong class="text-orange"><i class="fa fa-exclamation-circle"></i> Pending</strong>
                                        @elseif($vl->isApproved=='0')
                                          <strong ><i class="fa fa-thumbs-down"></i> Rejected</strong>
                                        @else
                                          <strong class="text-orange"><i class="fa fa-exclamation-circle"></i> Pending</strong>
                                        @endif

                                    @endif

                                  </th>

                                  <td>
                                  
                                  @if(is_null($vl->validated))

                                     @if($vl->isApproved=='0')
                                      <a class="btn btn-xs btn-primary" data-toggle="modal" data-target="#leaveModal{{$vl->id}}"><i class="fa fa-info-circle"></i> Details </a>

                                     @else
                                      <a class="btn btn-xs btn-success" data-toggle="modal" data-target="#leaveModal{{$vl->id}}"><i class="fa fa-check"></i> Validate </a>
                                      <a target="_blank" class="btn btn-xs btn-default" href="{{action('DTRController@show',['id'=>$vl->user_id,'from'=>$vl->productionDate,'to'=>date('Y-m-d',strtotime($to))])}} "><i class="fa fa-calendar"></i> DTR </a>
                                      <a class="btn btn-xs btn-default" data-toggle="modal" data-target="#delete{{$vl->id}}"><i class="fa fa-trash"></i> </a>
                                     @endif

                                  @else

                                    @if($vl->isApproved=='0')
                                    @else
                                     <a class="btn btn-xs btn-primary" data-toggle="modal" data-target="#leaveModal{{$vl->id}}"><i class="fa fa-info-circle"></i> Details </a>
                                    <a target="_blank" class="btn btn-xs btn-default" href="{{action('DTRController@show',['id'=>$vl->user_id,'from'=>$vl->productionDate,'to'=>date('Y-m-d',strtotime($to))])}}"><i class="fa fa-calendar"></i> DTR </a>
                                    <a class="btn btn-xs btn-default" data-toggle="modal" data-target="#delete{{$vl->id}}"><i class="fa fa-trash"></i> </a>
                                    @endif

                                 
                                  

                                  @endif
                                  
                                  
                                    
                                </td>

                                @else
                                  <td>
                                  
                                  @if($vl->reviewed)
                                      <a class="btn btn-xs btn-primary" data-toggle="modal" data-target="#leaveModal{{$vl->id}}"><i class="fa fa-info-circle"></i> Details </a>
                                  @else
                                      <a class="btn btn-xs btn-danger" data-toggle="modal" data-target="#leaveModal{{$vl->id}}"><i class="fa fa-info-circle"></i> Review </a>
                                  @endif

                                 
                                  <a target="_blank" class="btn btn-xs btn-default" href="{{action('DTRController@show',['id'=>$vl->user_id,'from'=>$vl->productionDate,'to'=>date('Y-m-d',strtotime($to))])}}"><i class="fa fa-calendar"></i> DTR </a>
                                  <a class="btn btn-xs btn-default" data-toggle="modal" data-target="#delete "><i class="fa fa-trash"></i> </a>
                                    
                                  </td>

 
                                @endif
                                
                             </tr>

                             
                                  <!-- MODALS -->
                                  <div class="modal fade" id="leaveModal{{$vl->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                      <div class="modal-content">
                                        <div class="modal-header">
                                          
                                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                            <h4 class="modal-title text-success" id="myModalLabel"><i class="fa fa-calendar"></i> {{$label}} Details </h4>
                                          
                                        </div> 
                                        <div class="modal-body-upload" style="padding:20px;">

                                          <h4 class="pull-right" style="text-align: right;"> <a href="{{action('UserController@show',$vl->user_id)}}" target="_blank"><i class="fa fa-2x fa-user"></i></a> {{$vl->lastname}}, {{$vl->firstname}} <em>({{$vl->nickname}} )</em><br/> 
                                            <strong><a href="{{action('CampaignController@show',$vl->programID)}}" target="_blank">{{$vl->program}}</a> </a></strong> <br/><br/></h4>

                                           <div class="row">

                                               
                                                <div class="col-sm-12">

                                                  <div class="row">
                                                    
                                                    
                                                    <div class="col-sm-2"><h5 class="text-primary">Production Date</h5></div>
                                                    <div class="col-sm-2 text-left"><h5 class="text-primary text-center">Log Details</h5></div>
                                                    <div class="col-sm-8"><h5 class="text-primary text-center">Reason</h5></div>
                                                    
                                                    
                                                  </div>

                                                   <div class="row">
                                                    
                                                    
                                                      <div class="col-sm-2">
                                                        <p><strong>{{date('M d, Y', strtotime($vl->productionDate))}}  </strong></p>
                                                      </div>

                                                      <div class="col-sm-2 text-left"><strong>{{date('h:i A',strtotime($vl->logTime))}}</strong></div>
                                                      <div class="col-sm-8">

                                                       @if($type == "OLD")
                                                          <?php ($vl->logType_id == '1') ? $lt = "[ DTRP IN ]" : $lt="[ DTRP OUT ]"; ?>
                                                          <p><strong class="text-danger"> {{$lt}}</strong> <br/>{{$vl->notes}} </p>
                                                       @else
                                                          <p><strong>{{$vl->reason}}</strong><br/> <em>{{$vl->notes}} </em></p>
                                                          <?php $link = $storageLoc."/".$vl->attachments; ?>
                                                          
                                                          <div style="border:1px dotted #666; padding:10px">

                                                            @if($vl->attachments)
                                                            <a style="margin:7px" class="btn btn-xs btn-default" href="{{$link}}" target="_blank"><i class="fa fa-paperclip"></i> View Attached Proof </a>
                                                            @endif
                                                            
                                                            <p style="font-size: small;">Requested: {{$vl->created_at}}<br/>
                                                              <?php $approver = collect($leaders)->where('id',$vl->approvedBy);
                                                              if (count($approver) > 0)
                                                                $by = $approver->first()->firstname." ".$approver->first()->lastname."<em> of ".$approver->first()->program."</em>";
                                                              else $by = "approver";?>
                                                            Approver: {!! $by !!}</p>
                                                          </div>

                                                       @endif
                                                      </div>

                                                   

                                                    

                                                    
                                                  
                                                  </div>

                                                 
                                                </div>

                                                 
                                                 
                                           </div>

                                           @if($type == "OLD")



                                              @if($vl->isApproved)

                                                  @if($vl->reviewed)

                                                      <div class="row">
                                                        <div class="col-lg-6"> <h5 class="text-success pull-left"><br/><br/><i class="fa fa-thumbs-up"></i> Approved by Approver</h5>
                                                        
                                                       </div>
                                                        <div class="col-lg-6">
                                                          <br/><br/><a target="_blank" class="btn btn-xs btn-default pull-right" href="{{action('DTRController@show',['id'=>$vl->user_id,'from'=>$vl->productionDate,'to'=>date('Y-m-d',strtotime($to))])}}"><i class="fa fa-calendar"></i> View DTR Page </a>

                                                          <div class="clearfix"></div>
                                                          <a style="margin-top: 5px" target="_blank" class="btn btn-xs btn-default pull-right" href="{{action('LogsController@emsAccess',['date'=>$vl->productionDate])}}"><i class="fa fa-check"></i> Verify System Access </a>
                                                          
                                                          

                                                         

                                                         

                                                          <!-- <a class="btn btn-md btn-primary pull-right" href="{{action('LogsController@viewRawBiometricsData', $vl->user_id)}}" target="_blank" style="margin-right:5px; margin-top:50px"><i class="fa fa-clock-o"></i> Manual Log Override</a>  -->
                                                        </div>
                                                      </div> 

                                                  @else

                                                      <div class="row">
                                                        <div class="col-lg-6"> <h5 class="text-success pull-left"><br/><br/><i class="fa fa-thumbs-up"></i> Approved by Approver</h5>
                                                         <div class="clearfix"></div>
                                                         <a target="_blank" class="btn btn-xs btn-default pull-left" href="{{action('DTRController@show',['id'=>$vl->user_id,'from'=>$vl->productionDate,'to'=>date('Y-m-d',strtotime($to))])}}"><i class="fa fa-calendar"></i> View DTR Page </a>
                                                         <div class="clearfix"></div>
                                                          <a style="margin-top: 7px" target="_blank" class="btn btn-xs btn-default pull-left" href="{{action('LogsController@emsAccess',['date'=>$vl->productionDate])}}"><i class="fa fa-check"></i> Verify System Access </a>

                                                       </div>
                                                        <div class="col-lg-6">
                                                          <br/><br/>
                                                          <label>Specify Actual Log Date:<br/><small style="font-style: italic; font-weight: normal;">for manual override</small></label>
                                                          <input id="actualDate_{{$vl->id}}"  type="text" name="actualDate" placeholder="{{date('m/d/Y', strtotime($vl->productionDate))}}" value="{{date('m/d/Y', strtotime($vl->productionDate))}}" class="datepicker form-control" />

                                                          <label class="text-primary">Reviewer Remarks:</label><br/>
                                                          <textarea class="form-control" id="remarks_{{$vl->id}}" name="remarks_{{$vl->id}}"></textarea> 

                                                           <a href="#" class="process btn btn-danger btn-md pull-right" data-leaveType="{{$label}}" data-action="0" data-id="{{$vl->id}}" data-infoID="{{$vl->id}}" data-dismiss="modal"style="margin-right:5px; margin-top:50px" > <i class="fa fa-thumbs-down" ></i> Reject </a>
                                                        <a href="#" class="process btn btn-success btn-md pull-right" data-leaveType="{{$label}}" data-action="1"  data-id="{{$vl->id}}" data-infoID="{{$vl->id}}" data-dismiss="modal"style="margin-right:5px; margin-top:50px" > <i class="fa fa-thumbs-up" ></i> Approve & Validate </a>

                                                          <!-- <a class="btn btn-md btn-primary pull-right" href="{{action('LogsController@viewRawBiometricsData', $vl->user_id)}}" target="_blank" style="margin-right:5px; margin-top:50px"><i class="fa fa-clock-o"></i> Manual Log Override</a>  -->
                                                        </div>
                                                      </div> 

                                                  @endif

                                                

                                               

                                                
                                                   
                                              @elseif($vl->isApproved=='0')
                                               <h4 class="text-danger pull-right"><br/><br/><i class="fa fa-thumbs-down"></i> Rejected</h4>

                                              @else

                                                 @if($vl->reviewed)

                                                      <div class="row">
                                                        <div class="col-lg-6"> <h5 class="text-success pull-left"><br/><br/><i class="fa fa-thumbs-up"></i> Approved by Approver</h5>
                                                        
                                                       </div>
                                                        <div class="col-lg-6">
                                                          <br/><br/><a target="_blank" class="btn btn-xs btn-default pull-right" href="{{action('DTRController@show',['id'=>$vl->user_id,'from'=>$vl->productionDate,'to'=>date('Y-m-d',strtotime($to))])}}"><i class="fa fa-calendar"></i> View DTR Page </a>
                                                        </div>
                                                      </div> 

                                                  @else

                                                      <div class="row">
                                                        <div class="col-lg-6"> 
                                                          <!-- <h5 class="text-success pull-left"><br/><br/>
                                                          <i class="fa fa-thumbs-up"></i> Approved by Approver</h5> -->
                                                         <div class="clearfix"></div>
                                                         <a target="_blank" class="btn btn-xs btn-default pull-left" href="{{action('DTRController@show',['id'=>$vl->user_id,'from'=>$vl->productionDate,'to'=>date('Y-m-d',strtotime($to))])}}"><i class="fa fa-calendar"></i> View DTR Page </a>
                                                         <div class="clearfix"></div>
                                                          <a style="margin-top: 5px" target="_blank" class="btn btn-xs btn-default pull-left" href="{{action('LogsController@emsAccess',['date'=>$vl->productionDate])}}"><i class="fa fa-check"></i> Verify System Access </a>
                                                          
                                                       </div>
                                                        <div class="col-lg-6">
                                                          <br/><br/>
                                                          <label>Specify Actual Log Date:<br/><small style="font-style: italic; font-weight: normal;">for manual override</small></label>
                                                          <input id="actualDate_{{$vl->id}}"  type="text" name="actualDate" placeholder="{{date('m/d/Y', strtotime($vl->productionDate))}}" value="{{date('m/d/Y', strtotime($vl->productionDate))}}" class="datepicker form-control" />

                                                          <label class="text-primary">Reviewer Remarks:</label><br/>
                                                          <textarea class="form-control" id="remarks_{{$vl->id}}" name="remarks_{{$vl->id}}"></textarea> 

                                                           <a href="#" class="process btn btn-danger btn-md pull-right" data-leaveType="{{$label}}" data-action="0" data-id="{{$vl->id}}" data-infoID="{{$vl->id}}" data-dismiss="modal"style="margin-right:5px; margin-top:50px" > <i class="fa fa-thumbs-down" ></i> Reject </a>
                                                        <a href="#" class="process btn btn-success btn-md pull-right" data-leaveType="{{$label}}" data-action="1"  data-id="{{$vl->id}}" data-infoID="{{$vl->id}}" data-dismiss="modal"style="margin-right:5px; margin-top:50px" > <i class="fa fa-thumbs-up" ></i> Approve & Validate </a>

                                                          <!-- <a class="btn btn-md btn-primary pull-right" href="{{action('LogsController@viewRawBiometricsData', $vl->user_id)}}" target="_blank" style="margin-right:5px; margin-top:50px"><i class="fa fa-clock-o"></i> Manual Log Override</a>  -->
                                                        </div>
                                                      </div> 

                                                  @endif

                                              
                                                 <!--  <button type="button" class="btn btn-default btn-md pull-right" data-dismiss="modal"style="margin-right:5px; margin-top:50px" > <i class="fa fa-times" ></i> Close </button>
                                                  
                                                
                                                  <a class="btn btn-md btn-primary pull-right" href="{{action('LogsController@viewRawBiometricsData', $vl->user_id)}}" target="_blank" style="margin-right:5px; margin-top:50px"><i class="fa fa-clock-o"></i> Manual Log Override</a>  -->

                                              @endif


                                           @else

                                              @if($vl->validated)

                                                   <h4 class="text-success pull-right"><br/><br/><i class="fa fa-thumbs-up"></i> Validated</h4>

                                              @elseif($vl->validated=='0')
                                                   <h4 class="text-danger pull-right"><br/><br/><i class="fa fa-thumbs-down"></i> Rejected</h4>


                                              @else
                                                  
                                                      <div class="clearfix"></div>
                                                      <label class="text-primary">Reviewer Remarks:</label><br/>
                                                      <textarea class="form-control" id="remarks_{{$vl->infoID}}" name="remarks_{{$vl->infoID}}"></textarea> 
                                                      
                                                      @if(is_null($vl->isApproved))
                                                        <button type="button" class="btn btn-default btn-md pull-right" data-dismiss="modal"style="margin-right:5px; margin-top:50px" > <i class="fa fa-times" ></i> Close </button>
                                                          <a href="#" class="process btn btn-danger btn-md pull-right" data-leaveType="{{$label}}" data-action="0" data-id="{{$vl->id}}" data-infoID="{{$vl->infoID}}" data-dismiss="modal"style="margin-right:5px; margin-top:50px" > <i class="fa fa-thumbs-down" ></i> Reject </a>
                                                          <a href="#" class="process btn btn-success btn-md pull-right" data-leaveType="{{$label}}" data-action="1"  data-id="{{$vl->id}}" data-infoID="{{$vl->infoID}}" data-dismiss="modal"style="margin-right:5px; margin-top:50px" > <i class="fa fa-thumbs-up" ></i> Validate &amp; Approve</a>

                                                       @elseif($vl->isApproved=='0')
                                                        <h5 class="text-danger pull-right"><br/><br/><i class="fa fa-thumbs-down"></i> Rejected by Approver</h5>

                                                       @else
                                                         <button type="button" class="btn btn-default btn-md pull-right" data-dismiss="modal"style="margin-right:5px; margin-top:50px" > <i class="fa fa-times" ></i> Close </button>
                                                          <a href="#" class="process btn btn-danger btn-md pull-right" data-leaveType="{{$label}}" data-action="0" data-id="{{$vl->id}}" data-infoID="{{$vl->infoID}}" data-dismiss="modal"style="margin-right:5px; margin-top:50px" > <i class="fa fa-thumbs-down" ></i> Reject </a>
                                                          <a href="#" class="process btn btn-success btn-md pull-right" data-leaveType="{{$label}}" data-action="1"  data-id="{{$vl->id}}" data-infoID="{{$vl->infoID}}" data-dismiss="modal"style="margin-right:5px; margin-top:50px" > <i class="fa fa-thumbs-up" ></i> Validate &amp; Approve </a>

                                                      @endif

                                              @endif

                                           @endif

                                           

                                       
                                        </div> 
                                        <div class="modal-footer no-border">
                                          
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                  <!-- END MODALS -->


                                   <!-- DELETE MODAL -->
                                  <div class="modal fade" id="delete{{$vl->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                      <div class="modal-content">
                                        <div class="modal-header">
                                          <button type="button" class="close" data-dismiss="modal">
                                            <span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
                                          </button>
                                          <h4 class="modal-title" id="myModalLabel">Delete this {{$label}}</h4></div>

                                          <div class="modal-body text-center"><br/><br/>Are you sure you want to delete this DTRP {{$type}} <br/>request by <strong>{{$vl->firstname}} {{$vl->lastname}} of {{$vl->program}} </strong>?<br/></div>
                                          <div class="modal-footer no-border">

                                            <form action="{{$deleteLink}}{{$vl->id}}" method="POST" class="btn-outline pull-right" id="deleteReq">
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


 <script type="text/javascript">

 $(function () {
   'use strict'; 

   $( ".datepicker" ).datepicker();
   $('#update').on('click',function(){
      var f = $('#from').val();
      var t = $('#to').val();
      console.log(f);
      console.log(t);

      window.location.href = "{{url('/')}}/DTRP_management?type={{$type}}&from="+f+"&to="+t;

   });
  
   $("#active").DataTable({
                "deferRender": true,
                "processing":true,
                "stateSave": true,
                "order": [ 6, "desc" ],
                "lengthMenu": [20, 100, 500],
                "dom": '<"col-xs-1"f><"col-xs-11 text-right"l><"clearfix">rt<"bottom"ip><"clear">',
                
                //"lengthChange": true,
                "oLanguage": {
                   "sSearch": "<strong>Refine Results</strong> <br/>To re-order entries, click on the sort icon to the right of column headers. <br/>To filter out results, just type in the search box anything you want to look for:",
                   "class": "pull-left"
                 },

                
        });


   $('.process').on('click',function(){
      var t = "{{$type}}";
      var dataid = $(this).attr('data-id');
      var infoID = $(this).attr('data-infoID');
      var isApproved = $(this).attr('data-action');
      var f = $('#from').val();
      var to = $('#to').val();
      var remarks = $('#remarks_'+infoID).val();
      var _token = "{{ csrf_token() }}";
      var actualDate=null;

      if(isApproved=='1')
        var appr="Valid";
      else var appr = "Rejected";
     

      switch(t){
        case 'OLD': {var processlink = "{{action('UserDTRPController@newDTRP_validate')}}"; actualDate = $('#actualDate_'+dataid).val(); }  break;
        case 'IN':  var processlink = "{{action('UserDTRPController@newDTRP_validate')}}"; break;
        case 'OUT':  var processlink = "{{action('UserDTRPController@newDTRP_validate')}}"; break;
      }

      
      var data = new FormData();

          data.append('id',dataid);
          data.append('infoID',infoID);
          data.append('isApproved',isApproved);
          data.append('logType',t);
          data.append('remarks',remarks);
          data.append('actualDate',actualDate);
          data.append('_token',_token);


      confirm('You are about to set this DTRP request as: '+appr);

      $.ajax({
                url: processlink,
                type:'POST',
                //contentType: 'multipart/form-data', 
                contentType: false,       // The content type used when sending data to the server.
                cache: false,             // To unable request pages to be cached
                processData:false,
                data:data,
                dataType: 'json',
                success: function(res){
                  console.log(res);
                  $('#leaveModal'+dataid).modal('hide');

                    if (isApproved == '1') {
                      $('#row'+dataid).fadeOut();
                      window.setTimeout(function(){
                        window.location.href = "{{url('/')}}/DTRP_management?type={{$type}}&from="+f+"&to="+to;
                      }, 1000);
                     
                    }
                   else {
                    $('#row'+dataid).fadeOut();
                    window.setTimeout(function(){
                        window.location.href = "{{url('/')}}/DTRP_management?type={{$type}}&from="+f+"&to="+to;
                      }, 1000);
                     
                   }
                   

                },
                error: function(){
                  console.log(res);
                  $('#leaveModal'+dataid).modal('hide');

                   $.notify("An error occured due to:\n(Error "+res.status+") "+ res.statusTex,{className:"error",globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );

                    
                }


              });

    });


  });
  
   
       



   


 </script>




<!-- end Page script -->


@stop