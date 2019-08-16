@extends('layouts.main')


@section('metatags')
  <title>Daily Time Record | {{$user->firstname}}</title>
    <meta name="description" content="profile page">
 <link href="{{URL::asset('storage/resources/js/bootstrap-material-datetimepicker.css')}}" rel="stylesheet" /> 
 <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
 <style type="text/css">
   #unlock { visibility: hidden;  }
 </style>
@stop


@section('content')




<section class="content-header">

      <h1>
      <i class="fa fa-calendar"></i> My <strong class="text-primary">D</strong>aily <strong class="text-primary">T</strong>ime <strong class="text-primary">R</strong>ecord
        <small></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        
        <li class="active">My DTR</li>
      </ol>
    </section>

    <!-- Main content -->
   
    <section class="content">
      <div class="row">
        
        <div class="col-xs-12">
          


          <!-- Profile Image -->
                          <div class="box box-primary"   style="background: rgba(256, 256, 256, 0.4)">

                            <div class="box-body box-profile"   style="background: rgba(256, 256, 256, 0.4)">
                               <!-- Widget: user widget style 1 -->
                                    <div class="box box-widget widget-user"   style="background: rgba(256, 256, 256, 0.4)">
                                      <!-- Add the bg color to the header using any of the bg-* classes -->
                                      <div class="widget-user-header bg-black" style="background: url('{{URL:: asset("public/img/bg_swish.png")}}') bottom left;">
                                        
                                        <h3 style="text-shadow: 1px 2px #000000; text-transform:uppercase" class="widget-user-username">{{$user->lastname}}, {{$user->firstname}} {{$user->middlename}} &nbsp;@if(!is_null($user->nickname)) (<small><em style="color:#fff">{{$user->nickname}}</em> </small>) @endif</h3>
                                        <h5 style="text-shadow: 1px 2px #000000;"  class="widget-user-desc">{{$user->position->name}} </h5>
                                      </div>
                                      <div class="widget-user-image" style="top:10px; left:95%">
                                        
                                         @if ( file_exists('public/img/employees/'.$user->id.'.jpg') )
                                          <img src="{{asset('public/img/employees/'.$user->id.'.jpg')}}" class="user-image" alt="User Image">
                                          @else
                                          <img src="{{asset('public/img/useravatar.png')}}" class="user-image" alt="User Image">

                                            @endif

                                          
                                        <br/>
                                      </div>
                                      <div class="box-footer"  style="background: rgba(256, 256, 256, 0.4)">
                                        <div class="row">
                                          <div class="col-sm-3">
                                            <div class="description-block">
                                              <p class="description-header"><i class="fa fa-users margin-r-5"></i> Department / Program : </p>
                                              <span class="description-text text-primary">
                                              {!! $camps !!}
                                              </span>

                                              
                                            </div>
                                            <!-- /.description-block -->
                                          </div>
                                          <!-- /.col -->
                                          <div class="col-sm-3">
                                            <div class="description-block">
                                              <p class="description-header"><i class="fa fa-street-view margin-r-5"></i> Immediate Head: </p>
                                              <a target="_blank" href="../user/{{$immediateHead->userData->id}}"><span class="description-text text-primary">{{$immediateHead->firstname}} {{$immediateHead->lastname}}</span></a>
                                            </div>
                                            <!-- /.description-block -->
                                          </div>
                                          <!-- /.col -->
                                          <div class="col-sm-3">
                                            <div class="description-block">
                                              <p class="description-header"><i class="fa fa-address-card-o margin-r-5"></i> Employee Number : </p>
                                              <span class="description-text text-primary">{{$user->employeeNumber}} </span>
                                            </div>
                                            <!-- /.description-block -->
                                          </div>
                                          <!-- /.col -->
                                          <div class="col-sm-3 ">
                                            <div class="description-block">
                                              <p class="description-header"><i class="fa fa-envelope-o margin-r-5"></i> E-mail:</p>
                                              <span><a href="mailto:{{$user->email}}"> {{$user->email}}</a></span>
                                            </div>
                                            <!-- /.description-block -->
                                          </div>
                                          <!-- /.col -->

                                          <!-- START CUSTOM TABS -->
     

                                         
                                          
                                          <!-- /.row -->
                                          <!-- END CUSTOM TABS -->
                                          
                                          <!-- /.col -->
                                          

                                      </div>
                                    </div>
                                    <!-- /.widget-user -->

                                    <div class="row">
                                          <!--   <div class="col-lg-1 col-sm-12"></div> -->
                                            <div class="col-lg-12 col-sm-12"><br/><br/>
                                              
                                              
                                                <div class="row">
                                                  <div class="col-lg-5 text-right"><a class="btn btn-default btn-sm" href="{{action('DTRController@show',['id'=>$user->id,'from'=>$prevFrom, 'to'=>$prevTo])}}">
                                                  <i class="fa fa-arrow-left"></i> </a></div>
                                                  <div class="col-lg-2">
                                                    <select class="form-control" name="payPeriod" id="payPeriod">
                                                      @if($cutoffID == 0)
                                                      <option value='0'>* Select cutoff period *</option>
                                                      @endif
                                                      @foreach($paycutoffs as $cutoffs)
                                                      <option value="{{$cutoffs->id}}" @if($cutoffID == $cutoffs->id) selected="selected" @endif data-fromDate="{{$cutoffs->fromDate}}" data-toDate="{{$cutoffs->toDate}}">{{ date('M d, Y', strtotime($cutoffs->fromDate)) }} - {{date('M d, Y', strtotime($cutoffs->toDate))}} </option>
                                                      @endforeach
                                                    </select>
                                                   

                                                  </div>
                                                  <div class="col-lg-5 text-left"><a class="btn btn-default btn-sm" href="{{action('DTRController@show',['id'=>$user->id,'from'=>$nextFrom, 'to'=>$nextTo])}}">
                                                  <i class="fa fa-arrow-right"></i></a></div>

                                                </div>

                                                <h4 class="text-center"><br/><br/>
                                                <small>Cutoff Period: </small><br/>
                                                <span class="text-success">&nbsp;&nbsp; {{$cutoff}} &nbsp;&nbsp; </span>

                                                  
                                              </h4>

                                              <!-- ********** DTR BUTTONS ************** -->
                                              
                                              @if(count($payrollPeriod) > 1 && ( count($myDTR) >= count($payrollPeriod) ) )
                                              <a id="lockDTR" class="btn btn-primary btn-md pull-left"><i class="fa fa-unlock"></i> Lock Entire DTR Sheet </a>
                                              @endif
                                              <a id="unlock" class="btn btn-sm btn-default pull-left" style="margin-left: 5px;"><i class="fa fa-unlock"></i> Request Unlock </a>
                                              <a target="_blank" href="{{action('LogsController@viewRawBiometricsData', $user->id)}}" class="btn btn-xs btn-primary pull-right"><i class="fa fa-search"></i> View Uploaded Biometrics</a>

                                              <a href="{{action('UserController@createSchedule', $user->id)}}" style="margin-right: 5px" class="btn btn-xs btn-primary pull-right"><i class="fa fa-calendar-plus-o"></i>  Plot Work Sched</a>

                                              <a href="{{action('UserController@userRequests', $user->id)}}" style="margin-right: 5px" class="btn btn-xs btn-primary pull-right"><i class="fa fa-file-o"></i>  DTR Requests</a>

                                              <a href="{{action('UserController@show', $user->id)}}" style="margin-right: 5px" class="btn btn-xs btn-primary pull-right"><i class="fa fa-address-card-o"></i>  My Profile</a>
                                              <br/><br/>

                                              <!-- ********** DTR BUTTONS ************** -->
                                              
                                              <table id="biometrics" class="table table-bordered table-striped">
                                                  <thead>
                                                  <tr class="text-success">
                                                    
                                                    <th class="text-center" style="width:15%">Production Date</th>
                                                    <td class="text-center"></td>
                                                    <th class="text-center" style="width:15%">Work Shift</th>
                                                    <th class="text-center" style="width:12%">IN</th>
                                                    <th class="text-center" style="width:12%">OUT</th>
                                                    
                                                    <th class="text-center">Hrs. Worked</th>
                                                    
                                                    <th class="text-center"  style="width:10%">OT<br/>(Billable hrs.)</th>
                                                    <th class="text-center"  style="width:10%">OT<br/>(Approved hrs.)</th>
                                                    <th  class="text-center">UT<br/>(hours)</th>
                                                   

                                                     
                                                  </tr>
                                                  </thead>
                                                  <tbody style="font-family: 'Source Sans Pro','Helvetica Neue',Helvetica,Arial,sans-serif; font-size:0.9em">

                                                    

                                                    @if (count($myDTR) == 0)
                                                    <tr>
                                                      <td colspan='10' class="text-center"><h2 class="text-center text-default"><br/><br/><i class="fa fa-clock-o"></i>&nbsp;&nbsp; No Biometrics Data Available</h2><small>Kindly check again at the end of work day or tomorrow for the updated biometrics data.</small><br/><br/><br/></td>
                                                    </tr>

                                                    @else
                                                     

                                                     @foreach ($myDTR as $data)

                                                    
                                                     <input type="hidden" name="dtr" class="biometrics" value="{{$data['biometrics_id']}}" />



                                                     <tr>
                                                        
                                                        <td class="text-right">
                                                          <!-- ******** PRODUCTION DATE ******* -->

                                                          <!-- <small style="font-size:x-small;">[{{$data['biometrics_id']}}]</small> -->
                                                          &nbsp;&nbsp; {{ $data['productionDate'] }} 

                                                          <input type="hidden" name="productionDate_{{$data['biometrics_id']}}" class="dtr_{{$data['biometrics_id']}}" value="{{ $data['productionDate'] }}">

                                                             @if(!$data['hasLeave'] && ( !is_null($data['shiftStart']) && !is_null($data['shiftEnd']) ) ) 
                                                             <!-- ****** we wont need the pushpins for DTRP kasi LEAVE today **** -->
                                                            
                                                                @if(count($user->approvers) > 0)
                                                                 <strong>

                                                                  @if ( count($verifiedDTR->where('productionDate',$data['payday'])) > 0 )
                                                                  <a id="unlockPD_{{$data['biometrics_id']}}" style="font-size: larger;" title="Request to Unlock " class="unlockPD pull-left btn btn-xs btn-default" data-production_date="{{ $data['payday'] }}" data-biometrics_id="{{$data['biometrics_id']}}"> <i class="fa fa-lock"></i> </a>

                                                                  @else

                                                                  <a style="font-size: larger;margin-right: 2px" title="Lock DTR " class="lockDTR2 pull-left btn btn-xs btn-primary" data-production_date="{{ $data['productionDate'] }}" data-biometrics_id="{{$data['biometrics_id']}}"> <i class="fa fa-unlock"></i> </a>
                                                                  <a style="font-size: larger;" data-toggle="modal" data-target="#myModal_{{$data['payday']}}" title="Report DTRP " class="reportDTRP text-red pull-left btn btn-xs btn-default" href="#" > <i class="fa fa-thumb-tack"></i></a>

                                                                  @endif
                                                                    

                                                                    

                                                                  </strong>
                                                                    @include('layouts.modals-DTRissue', [
                                                                          'modelRoute'=>'user_cws.store',
                                                                          'modelID' => '_'.$data["payday"], 
                                                                          'modelName'=>"DTR issue ", 
                                                                          'modalTitle'=>'Report', 
                                                                          'Dday' =>$data["day"],
                                                                          'DproductionDate' =>$data["productionDate"],
                                                                          'biometrics_id'=> $data["biometrics_id"],
                                                                          'approver' => $user->supervisor->immediateHead_Campaigns_id,
                                                                          'isRD'=> $data["isRD"],
                                                                          'timeStart_old'=>$data['shiftStart'],
                                                                          'timeEnd_old'=>$data['shiftEnd'],
                                                                          'formID'=>'reportIssue',
                                                                          'icon'=>'glyphicon-up' ])
                                                                @else

                                                                 <a style="font-size: larger;" data-toggle="modal" data-target="#noApprover" title="Report DTRP " class="reportDTRP text-red pull-left btn btn-xs btn-default" href="#" > <i class="fa fa-thumb-tack"></i></a>

                                                               

                                                                <!-- MODAL FOR NO APPROVER SET -->
                                                                <div class="modal fade text-left" id="noApprover" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                                  <div class="modal-dialog">
                                                                    <div class="modal-content">
                                                                      <div class="modal-header">
                                                                        
                                                                          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                                          <h4 class="modal-title text-danger" id="myModalLabel"><i class="fa fa-exclamation-triangle"></i> No Approver defined.</h4>
                                                                        
                                                                      </div>
                                                                      <div class="modal-body">
                                                                      
                                                                       Please inform HR to update your profile <br/>and set the necessary approver(s) for all of your request submissions. <br/><br/>Thank you.
                                                                      </div>
                                                                      <div class="modal-footer no-border">
                                                                        <button type="button" class="btn btn-default" data-dismiss="modal">Okay</button>
                                                                      </div>
                                                                    </div>
                                                                  </div>
                                                                </div>
                                                                <!-- MODAL FOR NO APPROVER SET -->




                                                                @endif <!--end if user->approvers -->

                                                            @else

                                                                  @if ( count($verifiedDTR->where('productionDate',$data['payday'])) > 0  && (count($user->approvers) > 0) )

                                                                  <a id="unlockPD_{{$data['biometrics_id']}}" style="font-size: larger;" title="Request to Unlock " class="unlockPD pull-left btn btn-xs btn-default" data-production_date="{{ $data['payday'] }}" data-biometrics_id="{{$data['biometrics_id']}}"> <i class="fa fa-lock"></i> </a>

                                                                  @else

                                                                  <a style="font-size: larger;margin-right: 2px" title="Lock DTR " class="lockDTR2 pull-left btn btn-xs btn-primary" data-production_date="{{ $data['productionDate'] }}" data-biometrics_id="{{$data['biometrics_id']}}"> <i class="fa fa-unlock"></i> </a>
                                                                  <!-- <a style="font-size: larger;" data-toggle="modal" data-target="#myModal_{{$data['payday']}}" title="Report DTRP " class="reportDTRP text-red pull-left btn btn-xs btn-default" href="#" > <i class="fa fa-thumb-tack"></i></a> -->

                                                                  @endif
                                                               

                                                            @endif <!--end if not hasleave && shiftStart-->
                                                           
                                                          </td>

                                                        <td class="text-center">{{ $data['day'] }} </td>

                                                         <!---- *****  WORK SCHED --------->

                                                         @if ($data['shiftStart'] == null || $data['shiftEnd'] == null)
                                                        <td class="text-center text-danger"><strong><em>No Work Schedule </em></strong>
                                                          
                                                          <input type="hidden" name="workshift_{{$data['biometrics_id']}}" class="dtr_{{$data['biometrics_id']}}" value="No Work Schedule">

                                                          @if ($anApprover)
                                                         <a title="Plot Work Schedule" class="pull-right" href="{{action('UserController@show',$user->id)}}/createSchedule"> <i class="fa fa-calendar"></i></a> 
                                                         @else
                                                         <a title="Inform TL or Workforce to plot your sched" class="text-orange pull-right" href="#"> <i class="fa fa-info"></i>

                                                         @endif
                                                       </td>

                                                        @else

                                                          @if($data['hasCWS']=='1')

                                                          <td class="text-center">
                                                           @if($data['isFlexitime']) <strong class="text-green"><i class="fa fa-refresh"></i> Flexi Time</strong><br/> @endif 
                                                           {!! $data['shiftStart2'] !!} - {!! $data['shiftEnd2'] !!}<strong><a data-toggle="modal" data-target="#myModal_CWS{{$data['payday']}}" title="View Details" class="@if ($data['usercws'][0]['isApproved'])text-green @elseif ( is_null($data['usercws'][0]['isApproved']) ) text-orange @else text-gray @endif pull-right" href="#" > <i class="fa fa-info-circle"></i></a></strong> </td>

                                                            
                                                             <input type="hidden" name="workshift_{{$data['biometrics_id']}}" class="dtr_{{$data['biometrics_id']}}" value="{{$data['shiftStart2']}} - {{$data['shiftEnd2']}}" />

                                                             <input type="hidden" name="cws_id_{{$data['biometrics_id']}}" class="dtr_{{$data['biometrics_id']}}" value="{{$data['usercws'][0]['id']}}" />


                                                          @else

                                                            @if($theImmediateHead || $anApprover)<!-- || $canChangeSched -->
                                                            <td class="text-center">
                                                               @if($data['isFlexitime']) <strong class="text-green"><i class="fa fa-refresh"></i> Flexi Sched</strong><br/> @endif 
                                                               {!! $data['shiftStart2'] !!} - {!! $data['shiftEnd2'] !!} <!-- <strong><a data-toggle="modal" data-target="#editCWS_{{$data['payday']}}" title="Change Work Sched " class="text-primary pull-right" href="#" > <i class="fa fa-pencil"></i></a></strong> --> </td>
                                                            

                                                            @else
                                                            <td class="text-center">
                                                               @if($data['isFlexitime']) <strong class="text-green"><i class="fa fa-refresh"></i> Flexi Sched</strong><br/> @endif 
                                                               {!! $data['shiftStart2'] !!} - {!! $data['shiftEnd2'] !!} <!-- <strong><a data-toggle="modal" data-target="#myModal_{{$data['payday']}}" title="Report DTRP " class="text-primary pull-right" href="#" > <i class="fa fa-flag-checkered"></i></a></strong> --> </td>
                                                            @endif

                                                             
                                                              <input type="hidden" name="workshift_{{$data['biometrics_id']}}" class="dtr_{{$data['biometrics_id']}}" value="{{$data['shiftStart2']}} - {{$data['shiftEnd2']}}" />

                                                              <input type="hidden" name="cws_id_{{$data['biometrics_id']}}" class="dtr_{{$data['biometrics_id']}}" value="0" />

                                                         @endif

                                                         
                                                         
                                                        
                                                        @endif
                                                        <!---- ***** end WORK SCHED --------->


                                                        <!-- ******** LOG IN ********* -->

                                                        @if($data['hasLeave'])

                                                            <td class="text-center">

                                                              @if (!empty($data['logIN']) && !$data['hasLeave'])

                                                                  {!! $data['logIN'] !!}
                                                                  

                                                              @else
                                                                <!-- **** new layout pag may 1st or 2nd shift leave -->
                                                                @if ($data['leaveDetails'][0]['details']['totalCredits'] == "0.50")

                                                                  @if( $data['leaveDetails'][0]['details']['halfdayFrom'] == 2  )

                                                                    <strong style="font-size: x-small"><em><i class="fa {{$data['leaveDetails'][0]['icon']}} "></i>&nbsp;&nbsp; 1st shift {!! $data['leaveDetails'][0]['type'] !!}</em> </strong><br/>
                                                                    {!! $data['logIN'] !!}

                                                                 

                                                                  @else

                                                                  
                                                                  {!! $data['logIN'] !!}
                                                                    <!-- <br/>
                                                                    <strong style="font-size: x-small"><em><i class="fa {{$data['leaveDetails'][0]['icon']}} "></i>&nbsp;&nbsp;{!! $data['leaveDetails'][0]['type'] !!}</em> </strong> -->

                                                                  @endif

                                                                @else


                                                                    <strong style="font-size: x-small"><em><i class="fa {{$data['leaveDetails'][0]['icon']}} "></i>&nbsp;&nbsp;{!! $data['leaveDetails'][0]['type'] !!}</em> </strong>

                                                                @endif

                                                                
                                                            


                                                              @endif
                                                              
                                                               
                                                                <input type="hidden" name="logIN_{{$data['biometrics_id']}}" class="dtr_{{$data['biometrics_id']}}" value="{{$data['leaveDetails'][0]['type']}}" />
 
                                                            
                                                          </td>
                                                          <input type="hidden" name="isDTRPin_{{$data['biometrics_id']}}" class="dtr_{{$data['biometrics_id']}}" value="0">


                                                       @elseif($data['hasLWOP'])

                                                            <td class="text-center">

                                                              @if (!empty($data['logIN']) && !$data['hasLWOP'])

                                                                  {!! $data['logIN'] !!}
                                                                  

                                                              @else
                                                              <!-- **** new layout pag may 1st or 2nd shift leave -->
                                                                @if ($data['lwopDetails'][0]['details']['totalCredits'] == "0.50")

                                                                  @if( $data['lwopDetails'][0]['details']['halfdayFrom'] == 2  )

                                                                    <strong style="font-size: x-small"><em><i class="fa {{$data['lwopDetails'][0]['icon']}} "></i>&nbsp;&nbsp; 1st shift {!! $data['lwopDetails'][0]['type'] !!}</em> </strong><br/>
                                                                    {!! $data['logIN'] !!}

                                                                 

                                                                  @else

                                                                  
                                                                  {!! $data['logIN'] !!}
                                                                  

                                                                  @endif

                                                                @else

                                                                    <strong style="font-size: x-small"><em><i class="fa {{$data['lwopDetails'][0]['icon']}} "></i>&nbsp;&nbsp;{!! $data['lwopDetails'][0]['type'] !!}</em> </strong>
                                                             
                                                                @endif





                                                             


                                                              @endif
                                                           
                                                              
                                                               
                                                                <input type="hidden" name="logIN_{{$data['biometrics_id']}}" class="dtr_{{$data['biometrics_id']}}" value="{{$data['lwopDetails'][0]['type']}}" />
 
                                                            
                                                          </td>
                                                          <input type="hidden" name="isDTRPin_{{$data['biometrics_id']}}" class="dtr_{{$data['biometrics_id']}}" value="0">

                                                        

                                                        @else

                                                            <td class="text-center">

                                                              @if ($data['isRDToday'])

                                                                    @if ( (strpos($data['logIN'], "RD") !== false)  )
                                                                        {!! $data['logIN'] !!}
                                                                    @else
                                                                        ** Rest Day ** <br/>
                                                                        <small>({!! $data['logIN'] !!})</small>
                                                                    @endif

                                                              @else

                                                                  {!! $data['logIN'] !!}

                                                              @endif



                                                              
                                                             

                                                              
                                                               <input type="hidden" name="logIN_{{$data['biometrics_id']}}" class="dtr_{{$data['biometrics_id']}}" value="{{$data['logIN']}}">

                                                            @if ($data['dtrpIN'] == true || $data['hasPendingIN']== true)
                                                            <input type="hidden" name="isDTRPin_{{$data['biometrics_id']}}" class="dtr_{{$data['biometrics_id']}}" value="{{$data['dtrpIN_id']}}">

                                                             <strong><a data-toggle="modal" title="View Details" @if($data['hasPendingIN']) data-target="#myModal_dtrpDetail{{$data['pendingDTRPin']['0']['id']}}"  class="text-purple pull-right" @else data-target="#myModal_dtrpDetail{{$data['dtrpIN_id']}}"  class="text-green pull-right" @endif href="#" > <i class="fa fa-info-circle"></i> &nbsp;&nbsp;</a></strong> 

                                                            @else
                                                             <input type="hidden" name="isDTRPin_{{$data['biometrics_id']}}" class="dtr_{{$data['biometrics_id']}}" value="0">

                                                            @endif
                                                          </td>

                                                        @endif <!--end if has leave-->

                                                        
                                                         @if ($data['dtrpIN'] == true )
                                                         <?php  $data_notifType = '8'; 
                                                                $data_notifID = $data["payday"];
                                                                $data_id = $data['dtrpIN_id'];  ?>

                                                      
                                                             @include('layouts.modals-detailsDTRP', [
                                                                      'data-notifType'=> $data_notifType,
                                                                      'data-notifID'=> $data_notifID,
                                                                      'dataid'=>$data_id,
                                                                      'modelID' => $data["dtrpIN_id"], 
                                                                      'modalTitle'=>'View DTRP Details', 
                                                                      'icon'=>'glyphicon-up' ])

                                                         @elseif ($data['hasPendingIN'] == true )
                                                          <?php  $data_notifType = '8'; 
                                                                $data_notifID = $data["payday"]; 
                                                                $data_id = $data['pendingDTRPin']['0']['id']; ?>

                                                              @include('layouts.modals-detailsDTRP', [
                                                                      'data-notifType'=> $data_notifType,
                                                                      'data-notifID'=> $data_notifID,
                                                                      'dataid'=>$data_id, 
                                                                      'modelID' => $data["pendingDTRPin"][0]['id'], 
                                                                      'modalTitle'=>'View DTRP Details', 
                                                                      'icon'=>'glyphicon-up' ])

                                                         @endif



                                                          <!-- ******** LOG OUT ********* -->


                                                          @if($data['hasLeave'])

                                                             <td class="text-center">

                                                              @if (!empty($data['logOUT']) && !$data['hasLeave'])

                                                                  {!! $data['logOUT'] !!} 

                                                              @else

                                                              <!-- **** new layout pag may 1st or 2nd shift leave -->
                                                                @if ($data['leaveDetails'][0]['details']['totalCredits'] == "0.50")

                                                                  @if( $data['leaveDetails'][0]['details']['halfdayFrom'] == 3  )

                                                                    {!! $data['logOUT'] !!}<br/>
                                                                    <strong style="font-size: x-small"><em><i class="fa {{$data['leaveDetails'][0]['icon']}} "></i>&nbsp;&nbsp; 2nd shift {!! $data['leaveDetails'][0]['type'] !!}</em> </strong>
                                                                    

                                                                  @else
                                                                    {!! $data['logOUT'] !!}

                                                                  @endif



                                                                @else


                                                                  <strong style="font-size: x-small"><em><i class="fa {{$data['leaveDetails'][0]['icon']}} "></i>&nbsp;&nbsp;{!! $data['leaveDetails'][0]['type'] !!}</em> </strong>

                                                                @endif



                                                                


                                                             
                                                              @endif
                                                              

                                                              
                                                              <input type="hidden" name="logOUT_{{$data['biometrics_id']}}" class="dtr_{{$data['biometrics_id']}}" value="{{$data['leaveDetails'][0]['type']}}" />
                                                            
                                                          </td>

                                                          <input type="hidden" name="isDTRPout_{{$data['biometrics_id']}}" class="dtr_{{$data['biometrics_id']}}" value="0">


                                                        @elseif($data['hasLWOP'])

                                                             <td class="text-center">

                                                              @if (!empty($data['logOUT']) && !$data['hasLWOP'])

                                                                  {!! $data['logOUT'] !!} 

                                                              @else

                                                              <!-- **** new layout pag may 1st or 2nd shift leave -->
                                                              
                                                                @if ($data['lwopDetails'][0]['details']['totalCredits'] == "0.50")

                                                                  @if( $data['lwopDetails'][0]['details']['halfdayFrom'] == 3  )

                                                                    {!! $data['logOUT'] !!}<br/>
                                                                    <strong style="font-size: x-small"><em><i class="fa {{$data['lwopDetails'][0]['icon']}} "></i>&nbsp;&nbsp; 2nd shift {!! $data['lwopDetails'][0]['type'] !!}</em> </strong>
                                                                    

                                                                  @else
                                                                    {!! $data['logOUT'] !!}

                                                                  @endif

                                                                @else  
                                                                <strong style="font-size: x-small"><em><i class="fa {{$data['lwopDetails'][0]['icon']}} "></i>&nbsp;&nbsp;{!! $data['lwopDetails'][0]['type'] !!}</em> </strong>
                                                             

                                                                @endif 
                                                                

                                                                

                                                              @endif
                                                              

                                                              
                                                              <input type="hidden" name="logOUT_{{$data['biometrics_id']}}" class="dtr_{{$data['biometrics_id']}}" value="{{$data['lwopDetails'][0]['type']}}" />
                                                            
                                                          </td>

                                                          <input type="hidden" name="isDTRPout_{{$data['biometrics_id']}}" class="dtr_{{$data['biometrics_id']}}" value="0">

                                                        @else
                                                              <td class="text-center">{!! $data['logOUT'] !!} 

                                                                
                                                                <input type="hidden" name="logOUT_{{$data['biometrics_id']}}" class="dtr_{{$data['biometrics_id']}}" value="{{$data['logOUT']}}" />

                                                              @if ($data['dtrpOUT'] == true  ||  $data['hasPendingOUT']== true)
                                                              <input type="hidden" name="isDTRPout_{{$data['biometrics_id']}}" class="dtr_{{$data['biometrics_id']}}" value="{{$data['dtrpOUT_id']}}">

                                                                 <strong>
                                                                  <a data-toggle="modal" title="View Details" @if($data['hasPendingOUT']) class="text-purple pull-right" data-target="#myModal_dtrpDetail{{$data['pendingDTRPout'][0]['id']}}" @else data-target="#myModal_dtrpDetail{{$data['dtrpOUT_id']}}" class="text-green pull-right" @endif href="#" > <i class="fa fa-info-circle"></i> &nbsp;&nbsp;</a></strong> 

                                                              @else
                                                               <input type="hidden" name="isDTRPout_{{$data['biometrics_id']}}" class="dtr_{{$data['biometrics_id']}}" value="0">

                                                              @endif
                                                              </td>


                                                        @endif <!--end if hasleave-->

                                                        

                                                        @if ($data['dtrpOUT'] == true )
                                                        <?php  $data_notifType = '9'; 
                                                                $data_notifID = $data["payday"];
                                                                $data_id = $data['dtrpOUT_id'];  ?>


                                                            @include('layouts.modals-detailsDTRP', [
                                                                       'data-notifType'=> $data_notifType,
                                                                      'data-notifID'=> $data_notifID,
                                                                      'dataid'=>$data_id, 
                                                                      'modelID' => $data["dtrpOUT_id"], 
                                                                      'modalTitle'=>'View DTRP Details', 
                                                                      'icon'=>'glyphicon-up' ]); 
                                                            

                                                        @elseif ($data['hasPendingOUT'] == true )
                                                        <?php  $data_notifType = '9'; 
                                                                $data_notifID = $data["payday"]; 
                                                               $data_id = $data['pendingDTRPout']['0']['id']; ?>

                                                              @include('layouts.modals-detailsDTRP', [
                                                                       'data-notifType'=> $data_notifType,
                                                                      'data-notifID'=> $data_notifID,
                                                                      'dataid'=>$data_id, 
                                                                      'modelID' => $data["pendingDTRPout"][0]['id'], 
                                                                      'modalTitle'=>'View DTRP Details', 
                                                                      'icon'=>'glyphicon-up' ]);

                                                         @endif

                                                       


                                                         <!-- ******** WORKED HOURS ********* -->


                                                         

                                                            <td class="text-center"> 
                                                              @if($data['isFlexitime'])<strong class="text-green"><i class="fa fa-refresh"></i> Flexi Sched</strong><br/> @endif 

                                                              @if($data['isFlexitime']) <span style="text-decoration:line-through"><em> @endif
                                                              
                                                              

                                                              
                                                              @if ($data['hasLeave'])

                                                              <!--$data['leaveDetails'][0]['type'] !!}-->
                                                              {!! $data['workedHours'] !!}
                                                              <!-- <i class="fa {{$data['leaveDetails'][0]['icon'] }}"></i> -->
                                                               <input type="hidden" name="workedHours_{{$data['biometrics_id']}}" class="dtr_{{$data['biometrics_id']}}" value="{{$data['workedHours']}}" />

                                                               <input type="hidden" name="leaveID_{{$data['biometrics_id']}}" class="dtr_{{$data['biometrics_id']}}" value="{{$data['leaveDetails'][0]['details']['id']}}" />

                                                               <input type="hidden" name="leaveType_{{$data['biometrics_id']}}" class="dtr_{{$data['biometrics_id']}}" value="{{$data['leaveDetails'][0]['type']}}" />


                                                               @else
                                                               <input type="hidden" name="workedHours_{{$data['biometrics_id']}}" class="dtr_{{$data['biometrics_id']}}" value="{{$data['workedHours']}}" />

                                                               

                                                                  @if($data['hasLWOP'])

                                                                  <input type="hidden" name="leaveType_{{$data['biometrics_id']}}" class="dtr_{{$data['biometrics_id']}}" value="{{$data['lwopDetails'][0]['type']}}" />
                                                                  <input type="hidden" name="leaveID_{{$data['biometrics_id']}}" class="dtr_{{$data['biometrics_id']}}" value="{{$data['lwopDetails'][0]['details']['id']}}" />

                                                                  @else

                                                                  <input type="hidden" name="leaveID_{{$data['biometrics_id']}}" class="dtr_{{$data['biometrics_id']}}" value="0" />

                                                                  <input type="hidden" name="leaveType_{{$data['biometrics_id']}}" class="dtr_{{$data['biometrics_id']}}" value="0" />

                                                                  @endif

                                                                  {!! $data['workedHours'] !!}
                                                               

                                                               @endif

                                                                

                                                                <!-- @if (strpos($data['workedHours'],"AWOL") !== false)
                                                                  <strong><a data-toggle="modal" data-target="#editCWS_{{$data['payday']}}" title="File Leave " class="text-primary pull-right" href="#" > <i class="fa fa-briefcase"></i></a></strong>
                                                              @endif -->

                                                              @if($data['isFlexitime'])</em> </span> @endif
                                                            </td>


                                                       

                                                        @if($data['hasLeave'])

                                                             <td class="text-center"> 0

                                                              <!--  <td class="text-center"><a href="{{action('UserController@myRequests',$user->id)}}"><strong style="font-size: x-small"><em><i class="fa {{$data['leaveDetails'][0]['icon']}} "></i>&nbsp;&nbsp;{!! $data['leaveDetails'][0]['type'] !!}</em> </strong></a> -->

                                                               
                                                               <input type="hidden" name="OT_billable_{{$data['biometrics_id']}}" class="dtr_{{$data['biometrics_id']}}" value="{{$data['leaveDetails'][0]['type']}}" />
                                                            
                                                            <!--  <strong><a data-toggle="modal" title="View Details" @if($data['hasPendingIN']) data-target="#myModal_dtrpDetail{{$data['pendingDTRPin']['0']['id']}}"  class="text-purple pull-right" @else data-target="#myModal_dtrpDetail{{$data['dtrpIN_id']}}"  class="text-green pull-right" @endif href="#" > <i class="fa fa-info-circle"></i> &nbsp;&nbsp;</a></strong> 
 -->
                                                            
                                                          </td>

                                                         @elseif ($data['hasLWOP'])

                                                            <td class="text-center"> 0

                                                            <!--  <td class="text-center"><a href="{{action('UserController@myRequests',$user->id)}}"><strong style="font-size: x-small"><em><i class="fa {{$data['lwopDetails'][0]['icon']}} "></i>&nbsp;&nbsp;{!! $data['lwopDetails'][0]['type'] !!}</em> </strong></a>
 -->
                                                               
                                                                <!-- <input type="hidden" name="logIN_{{$data['biometrics_id']}}" class="dtr_{{$data['biometrics_id']}}" value="{{$data['lwopDetails'][0]['type']}}" /> -->

                                                                 <input type="hidden" name="OT_billable_{{$data['biometrics_id']}}" class="dtr_{{$data['biometrics_id']}}" value="{{$data['lwopDetails'][0]['type'] }}" />
                                                            
                                                           
                                                            
                                                          </td>  

                                                       <!-- **************** OVERTIME **************** -->    
                                                        @else

                                                            @if ($data['hasOT'])                                     
                                                                           
                                                                    <!-- ** we now process any pre and post OTs ** -->
                                                                    <td class="text-center"> 
                                                                        @if( count($data['userOT']) > 1)

                                                                            <?php $postOT = collect($data['userOT'])->where('preshift',null); ?>
                                                                            <?php $preOT = collect($data['userOT'])->where('preshift',1); ?>

                                                                            @if (count($preOT) > 0)

                                                                               {{$preOT->first()->billable_hours}} <a style="font-weight: bold;font-size: smaller;" class="text-success" target="_blank" href="../user_ot/{{$preOT->first()->id}}" title="Pre-shift OT Details">(Pre-shift)</a><br/>
                                                                             

                                                                            @endif

                                                                            @if (count($postOT) > 0)

                                                                              {!! $data['billableForOT'] !!} <span style="font-weight: bold; font-size: smaller;" >(Post)</span>

                                                                              @if($postOT->first()->isApproved)

                                                                                 <a data-toggle="modal" data-target="#myModal_OT_details{{$data['payday']}}" title="View Details" class="pull-right text-green" style="font-size:1.2em;" href="#"><i class="fa fa-credit-card"></i></a></td>

                                                                              @elseif($postOT->first()->isApproved == '0')
                                                                                   <a data-toggle="modal" data-target="#myModal_OT_details{{$data['payday']}}" title="View Details" class="pull-right text-gray" style="font-size:1.2em;" href="#"><i class="fa fa-credit-card"></i></a></td>

                                                                              @else

                                                                                   <a data-toggle="modal" data-target="#myModal_OT_details{{$data['payday']}}" title="Pending Approval" class="pull-right text-orange" style="font-size:1.2em;" href="#"><i class="fa fa-credit-card"></i></a></td>

                                                                              @endif
                                                                             

                                                                              @include('layouts.modals-OT_details2', [
                                                                              'modelRoute'=>'user_ot.store',
                                                                              'modelID' => '_OT_details'.$data["payday"], 
                                                                              'modelName'=>"Overtime ", 
                                                                              'modalTitle'=>'OT Details', 
                                                                              'Dday' =>$data["day"],
                                                                              'DproductionDate' =>$data["productionDate"],
                                                                              'biometrics_id'=> $data["biometrics_id"],
                                                                              'approver' => $user->supervisor->immediateHead_Campaigns_id,
                                                                              'isRD'=> $data["isRD"],
                                                                              'formID'=>'submitOT',
                                                                              'otData'=>$postOT,
                                                                              'icon'=>'glyphicon-up' ])

                                                                            @endif

                                                                            

                                                                        @else

                                                                            {!! $data['billableForOT'] !!} 

                                                                            @if(count($data['approvedOT']) > 0)

                                                                                @if(is_null($data['approvedOT']->first()->preshift))
                                                                                     <a data-toggle="modal" data-target="#myModal_OT_details{{$data['payday']}}" title="View Details" class="pull-right @if ($data['approvedOT']->first()->isApproved)text-green @else text-gray @endif" style="font-size:1.2em;" href="#"><i class="fa fa-credit-card"></i></a></td>

                                                                                     @include('layouts.modals-OT_details', [
                                                                                    'modelRoute'=>'user_ot.store',
                                                                                    'modelID' => '_OT_details'.$data["payday"], 
                                                                                    'modelName'=>"Overtime ", 
                                                                                    'modalTitle'=>'OT Details', 
                                                                                    'Dday' =>$data["day"],
                                                                                    'DproductionDate' =>$data["productionDate"],
                                                                                    'biometrics_id'=> $data["biometrics_id"],
                                                                                    'approver' => $user->supervisor->immediateHead_Campaigns_id,
                                                                                    'isRD'=> $data["isRD"],
                                                                                    'formID'=>'submitOT',
                                                                                    'icon'=>'glyphicon-up' ])

                                                                                @else

                                                                                   {!! $data['OTattribute'] !!}
                                                                                    @include('layouts.modals-OT', [
                                                                                      'modelRoute'=>'user_ot.store',
                                                                                      'modelID' => '_OT'.$data["payday"], 
                                                                                      'modelName'=>"Overtime ", 
                                                                                      'modalTitle'=>'Submit', 
                                                                                      'Dday' =>$data["day"],
                                                                                      'DproductionDate' =>$data["productionDate"],
                                                                                      'biometrics_id'=> $data["biometrics_id"],
                                                                                      'approver' => $user->supervisor->immediateHead_Campaigns_id,
                                                                                      'isRD'=> $data["isRD"],
                                                                                      'formID'=>'submitOT',
                                                                                      'icon'=>'glyphicon-up' ])


                                                                                @endif

                                                                            @else

                                                                              @if ($data['userOT']->first()->isApproved)
                                                                                  <a data-toggle="modal" data-target="#myModal_OT_details{{$data['payday']}}" title="View Details" class="pull-right text-green" style="font-size:1.2em;" href="#"><i class="fa fa-credit-card"></i></a></td>

                                                                              @elseif ($data['userOT']->first()->isApproved == '0')
                                                                                  <a data-toggle="modal" data-target="#myModal_OT_details{{$data['payday']}}" title="View Details" class="pull-right text-gray" style="font-size:1.2em;" href="#"><i class="fa fa-credit-card"></i></a></td>

                                                                              @else

                                                                                  <a data-toggle="modal" data-target="#myModal_OT_details{{$data['payday']}}" title="Pending Approval" class="pull-right text-orange" style="font-size:1.2em;" href="#"><i class="fa fa-credit-card"></i></a></td>

                                                                              @endif 
                                                                      

                                                                                      @include('layouts.modals-OT_details', [
                                                                                      'modelRoute'=>'user_ot.store',
                                                                                      'modelID' => '_OT_details'.$data["payday"], 
                                                                                      'modelName'=>"Overtime ", 
                                                                                      'modalTitle'=>'OT Details', 
                                                                                      'Dday' =>$data["day"],
                                                                                      'DproductionDate' =>$data["productionDate"],
                                                                                      'biometrics_id'=> $data["biometrics_id"],
                                                                                      'approver' => $user->supervisor->immediateHead_Campaigns_id,
                                                                                      'isRD'=> $data["isRD"],
                                                                                      'formID'=>'submitOT',
                                                                                      'icon'=>'glyphicon-up' ])

                                                                            

                                                                            @endif
                                                                         

                                                                        @endif
                                                                   


                                                                 


                                                             
                                                                 
                                                                 <input type="hidden" name="OT_billable_{{$data['biometrics_id']}}" class="dtr_{{$data['biometrics_id']}}" value="{{$data['billableForOT']}}" />

                                                                
                                                            <!--else if data[hasOT]-->
                                                            @else
                                                            <td class="text-center">
                                                              @if($data['isFlexitime']) <strong class="text-green"><i class="fa fa-refresh"></i> Flexi Sched</strong><br/> @endif 
                                                              {!! $data['billableForOT'] !!} {!! $data['OTattribute'] !!} </td>

                                                             
                                                              <input type="hidden" name="OT_billable_{{$data['biometrics_id']}}" class="dtr_{{$data['biometrics_id']}}" value="{{$data['billableForOT']}}" />

                                                            @endif

                                                        @endif <!--end if has leave -->



                                                        
                                                       <!-- **************** APPROVED OT **************** -->

                                                        <td class="text-center">
                                                          @if( empty($data['approvedOT']) ) 0 
                                                          <input type="hidden" name="OT_approved_{{$data['biometrics_id']}}" class="dtr_{{$data['biometrics_id']}}" value="0" /> 
                                                          <input type="hidden" name="OT_id_{{$data['biometrics_id']}}" class="dtr_{{$data['biometrics_id']}}" value="0" />



                                                          @else 

                                                            <?php $allOT=0;?>

                                                            @foreach($data['approvedOT'] as $aOT)

                                                                @if($aOT['isApproved'])
                                                                <?php $allOT += $aOT['filed_hours']; ?>
                                                                <strong>
                                                                    
                                                                    @if($aOT['preshift'])
                                                                      &nbsp;<small>(Pre-shift)</small>
                                                                    @else
                                                                     &nbsp;<small>(post-shift)</small>
                                                                    @endif
                                                                    {{$aOT['filed_hours']}} 

                                                                </strong> <br/>
                                                                @else

                                                                  0  @if($aOT['preshift'])
                                                                      &nbsp;<small>(Pre-shift)</small>
                                                                     @else
                                                                      &nbsp;<small>(post-shift)</small>
                                                                    @endif
                                                                  <br/>

                                                                @endif



                                                            @endforeach

                                                            @if($allOT!==0)<strong class="text-success">TOTAL: {{number_format($allOT,2)}}</strong>
                                                            <input type="hidden" name="OT_approved_{{$data['biometrics_id']}}" class="dtr_{{$data['biometrics_id']}}" value="{{number_format($allOT,2)}}" />
                                                            @else
                                                             <input type="hidden" name="OT_approved_{{$data['biometrics_id']}}" class="dtr_{{$data['biometrics_id']}}" value="0" /> 
                                                            @endif

                                                           
                                                          
                                                          <!-- <input type="hidden" name="OT_approved_{{$data['biometrics_id']}}" class="dtr_{{$data['biometrics_id']}}" value="{{$data['approvedOT']->first()['filed_hours']}}" /> -->
                                                          
                                                           <input type="hidden" name="OT_id_{{$data['biometrics_id']}}" class="dtr_{{$data['biometrics_id']}}" value="{{$data['userOT']->first()['id']}}" />
                                                        @endif</td>



                                                         <!-- **************** UDERTIME **************** -->

                                                        @if($data['hasLeave'] || $data['hasLWOP'])
                                                        <td class="text-center">N/A</td>
                                                        <input type="hidden" name="UT_{{$data['biometrics_id']}}" class="dtr_{{$data['biometrics_id']}}" value="n/a" />
                                                        @else  
                                                        <td class="text-center">{{$data['UT']}}</td>
                                                        <input type="hidden" name="UT_{{$data['biometrics_id']}}" class="dtr_{{$data['biometrics_id']}}" value="{{$data['UT']}}" />
                                                        @endif
                                                        
                                                         
                                                       
                                                        
                                                        
                                                     </tr>
                                                     @if ($data['hasCWS'] =='1')

                                                     <?php $data_notifType = '6'; $data_notifID = $data["payday"]; $data_id = $data['usercws'][0]['id'];  ?>

                                                          @if ($anApprover)
                                                              @include('layouts.modals-details', [
                                                                      
                                                                      'modelID' => '_CWS'.$data["payday"], 
                                                                      'modalTitle'=>'View CWS Details', 
                                                                      'icon'=>'glyphicon-up',
                                                                      'data-notifType'=> $data_notifType,
                                                                      'data-notifID'=> $data_notifID,
                                                                      'dataid'=>$data_id ])


                                                          @else
                                                              @include('layouts.modals-details', [
                                                                  
                                                                  'modelID' => '_CWS'.$data["payday"], 
                                                                  'modalTitle'=>'View CWS Details', 
                                                                  'icon'=>'glyphicon-up', 'dataid'=>null ])

                                                          @endif
                                                         

                                                     @endif

                                                        




                                                         <!-- @if ($theImmediateHead || $anApprover)
                                                            @include('layouts.modals-editDTR', [
                                                                  'modelRoute'=>'user_cws.store',
                                                                  'modelID' => '_'.$data["payday"], 
                                                                  'modelName'=>"Employee DTR ", 
                                                                  'modalTitle'=>'Edit', 
                                                                  'Dday' =>$data["day"],
                                                                  'DproductionDate' =>$data["productionDate"],
                                                                  'biometrics_id'=> $data["biometrics_id"],
                                                                  'approver' => $user->supervisor->immediateHead_Campaigns_id,
                                                                  'isRD'=> $data["isRD"],
                                                                  'timeStart_old'=>$data['shiftStart'],
                                                                  'timeEnd_old'=>$data['shiftEnd'],
                                                                  'formID'=>'reportIssue',
                                                                  'icon'=>'glyphicon-up' ])

                                                        @else -->
                                                            
                                                       <!--  @endif -->


                                                        @if ($data['hasOT'])
                                                            

                                                        @else

                                                        <!-- ******** hindi basta basta pwede magfile ng OT. Check muna kung
                                                                      may pending CWS, if Yes show alert ****** -->

                                                            @if($data['hasCWS'])

                                                                @if ( $data['usercws'][0]['isApproved']!== 0 && $data['usercws'][0]['isApproved'] == null ) <!--pending pa cws -->
                                                                      @include('layouts.modals-OT_cannot', [
                                                                      
                                                                      'modelID' => '_OT'.$data["payday"], 
                                                                      'modelName'=>"Overtime ", 
                                                                      'modalTitle'=>'Submit',
                                                                      'formID'=>'submitOT',
                                                                      'icon'=>'glyphicon-up' ])
                                                                @else

                                                                    @include('layouts.modals-OT', [
                                                                      'modelRoute'=>'user_ot.store',
                                                                      'modelID' => '_OT'.$data["payday"], 
                                                                      'modelName'=>"Overtime ", 
                                                                      'modalTitle'=>'Submit', 
                                                                      'Dday' =>$data["day"],
                                                                      'DproductionDate' =>$data["productionDate"],
                                                                      'biometrics_id'=> $data["biometrics_id"],
                                                                      'approver' => $user->supervisor->immediateHead_Campaigns_id,
                                                                      'isRD'=> $data["isRD"],
                                                                      'formID'=>'submitOT',
                                                                      'icon'=>'glyphicon-up' ])

                                                                @endif
                                                            
                                                            @else

                                                                @include('layouts.modals-OT', [
                                                                  'modelRoute'=>'user_ot.store',
                                                                  'modelID' => '_OT'.$data["payday"], 
                                                                  'modelName'=>"Overtime ", 
                                                                  'modalTitle'=>'Submit', 
                                                                  'Dday' =>$data["day"],
                                                                  'DproductionDate' =>$data["productionDate"],
                                                                  'biometrics_id'=> $data["biometrics_id"],
                                                                  'approver' => $user->supervisor->immediateHead_Campaigns_id,
                                                                  'isRD'=> $data["isRD"],
                                                                  'formID'=>'submitOT',
                                                                  'icon'=>'glyphicon-up' ])


                                                            @endif

                                                            
                                                        @endif

                                                        


                                                        
                                                       
                                              
                                                      @endforeach
                                                  @endif

                                                  
                                                  </tbody>
                                                  
                                              </table>

                                              
                                            </div>
                                            <!-- /.col -->
                                           <!--  <div class="col-lg-1 col-sm-12"></div> -->

                                           
                                    </div>

                            </div>
                            

                            <!-- /.box-body -->
                          </div>

        </div>
        

      </div>

      <!----------------- MEMO ---------------->
      @if (!is_null($memo) && $notedMemo != true)
        @include('layouts.modals-memo', [
                                  'modelRoute'=>'user_memo.store',
                                  'modelID' => $memo->id, 
                                  'modelName'=>$memo->title, 
                                  'modalTitle'=>$memo->title, 
                                  'modalMessage'=> $memo->body, 
                                  'formID'=>'memo',
                                  'icon'=>'glyphicon-check' ])
      @endif

         

     

    </section>

@stop

@section('footer-scripts')
<script src="{{URL::asset('storage/resources/js/bootstrap-material-datetimepicker.js')}}"></script>
<script src="{{URL::asset('storage/resources/js/moment.min.js')}}"></script>





<script type="text/javascript">
$(function () 
{

  /*----------------- MEMO ----------------*/

    $(window).bind("load", function() {

       @if (!is_null($memo) && $notedMemo != true)
       $('#memo'+{{$memo->id}}).modal({backdrop: 'static', keyboard: false, show: true});
       @endif
  });
      
   @if (!is_null($memo) && $notedMemo != true)
      $('#yesMemo').on('click',function(){

        var _token = "{{ csrf_token() }}";
        

        //--- update user notification first
        $.ajax({
            url: "{{action('UserMemoController@saveUserMemo')}}",
            type:'POST',
            data:{ 
              'id': "{{$memo->id}}",
              '_token':_token
            },

            success: function(res){
                    console.log(res);
            },
          });

      });
   @endif




  $( ".datepicker" ).on('click', function()
    { 
      $(this).datepicker().datepicker("show"); 
      $(this).datepicker({dateFormat:"YYYY-mm-dd"}); console.log("datepicker");
    })

  $('.container').fadeOut();
  $('.container#workshiftOptions').fadeOut();
  $('.container#login').fadeOut();
  $('.container#logout').fadeOut();
  $('.container#leave').fadeOut(); 
  
  //complicated clock
  $('.timepick').bootstrapMaterialDatePicker({format: 'hh:mm a',monthPicker: false, date: false, year:false, shortTime:true}); //wickedpicker(options);


  $('a.reportDTRP').on('click',function()
    { $('input[type="checkbox"]').removeAttr('checked');
      $('.container').hide(); $('.detail').hide(); 
  });


 

  //$('.moredays').fadeOut();
  //****** main checkboxes : CWS | DTRP IN | DTRP OUT | leaves ************
  $('input[type=checkbox]').change(function () {

    $('#leaveDetails').hide();
    
    var choice = $(this).val();
    var allboxes = $('input[type="checkbox"]').filter(':checked').length;

    
    if( $(this).is(':checked') )
    {
      switch(choice){
        case '1': { $('.container#workshiftOptions').fadeIn(); 
                    $('input[id="leave"]').prop('checked',false);
                    $('.container#leave').fadeOut();
                    $('#leaveDetails').fadeIn(); $('.addDays').fadeIn();$('button#upload').fadeIn();
                    $('textarea[name="cwsnote"]').prop('required',true);
                    $('select#fulltimes').prop('required',true);
                    $('select#fulltimes').prop('disabled',true);
                    $('select#parttimes').prop('required',true);
                    $('select#parttimes').prop('disabled',true);

                    $('input[name="shifttype"]').on('click',function(){
                      {
                      var shifttype = $(this).val();
                      if(shifttype == 'full'){
                        $('select#fulltimes').prop('disabled',false); $('select#parttimes').prop('disabled',true);
                         if ($('input[name="timeEnd"]').val() == "0" )
                          $.notify("Please fill out required field before submitting.",{className:"error", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );
                        else
                          $.notify("Please fill out required field before submitting.",{className:"error", globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );

                      }else{
                        $('select#parttimes').prop('disabled',false); $('select#fulltimes').prop('disabled',true);
                         if ($('input[name="timeEnd"]').val() == "0" )
                          $.notify("Please fill out required field before submitting.",{className:"error", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );
                        else
                          $.notify("Please fill out required field before submitting.",{className:"error", globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );
                          }

                    }

                    });
                    
                   

                  }break;
        case '2': { 

                    $('.container#login').fadeIn(); 
                    $('input[id="leave"]').attr('checked',false);
                    $('.container#leave').fadeOut();
                    $('button#upload').fadeIn();
                    $('textarea[name="loginReason"]').prop('required',true);
                    $('input[name="login"]').prop('required',true);

                    if( $('input#workshift').is(':checked') ){
                      console.log("checked");
                    }else
                      $('.container#login').append('<input type="radio" name="shifttype" value="0" checked="checked"/>');
                  }break;

        case '3': { $('.container#logout').fadeIn(); 
                    $('input[id="leave"]').attr('checked',false);
                    $('.container#leave').fadeOut();$('button#upload').fadeIn();
                    $('textarea[name="logoutReason"]').prop('required',true);
                    $('input[name="logout"]').prop('required',true);

                    if($('input#workshift').is(':checked')){

                    }else
                      $('.container#logout').append('<input type="radio" name="shifttype" value="0" checked="checked"/>');


                  }break;
        case '4': { 
                    $('.container#leave').fadeIn(); 
                    $('input[id="login"]').attr('checked',false);
                    $('input[id="logout"]').attr('checked',false);
                    $('input[id="workshift"]').attr('checked',false);
                    $('.container#workshiftOptions').fadeOut();
                    $('.container#login').fadeOut(); 
                    $('.container#logout').fadeOut();
                    $('button#upload').fadeOut();

                  }break;
        
      }

      if (allboxes>0) $('button.submit').prop('disabled',false); else $('button.submit').prop('disabled',true);


      


    } else {
      if (allboxes>0) $('button.submit').prop('disabled',false); else $('button.submit').prop('disabled',true);
      switch(choice){
        case '1': { $('.container#workshiftOptions').fadeOut();$('textarea[name="cwsnote"]').prop('required',false);
                    $('select#fulltimes').prop('required',false); $('select#parttimes').prop('required',false); }break;
        case '2': { $('.container#login').fadeOut(); $('textarea[name="loginReason"]').prop('required',false);
                    $('input[name="login"]').prop('required',false);}break;
        case '3': { $('.container#logout').fadeOut(); $('textarea[name="logoutReason"]').prop('required',false);
                    $('input[name="logout"]').prop('required',false);}break;
        case '4': { $('.container#leave').fadeOut(); }break;
      }

    }
    
       

  }); //end main checkboxes

$('select.end.form-control').on('change',function(){
  var selval = $(this).children("option:selected").val();
  $('input[name="timeEnd"]').val(selval);
  console.log(selval);

});

$('button#uploadOT').fadeOut();
 $('select.othrs.form-control').on('change',function(){

     var timeStart = $(this).find('option:selected').attr('data-timestart');
     var timeEnd = $(this).find('option:selected').attr('data-timeend');
     var fh = $(this).find(':selected').val();

     //console.log('start: ' + timeStart);
     //console.log('end: ' + timeEnd);

     $('input[name="OTstart"]').val(timeStart);
     $('input[name="OTend"]').val(timeEnd);

     if (fh === '0')$('button#uploadOT').fadeOut(); 
     else $('button#uploadOT').fadeIn();

    console.log('selected:');
    console.log(fh);


  }); //end timeEnd check if on change



  $('a').tooltip().css({"cursor":"pointer"});

  $('#payPeriod').on('change',function()
  {
    var fromDate = $(this).find('option:selected').attr('data-fromDate'); //$(this).find('option:selected').val();
    var toDate = $(this).find('option:selected').attr('data-toDate');

    window.location.href= "{{url('/')}}/user_dtr/{{$user->id}}?from="+fromDate+"&to="+toDate;
  }); //end payPeriod



  $('.widget-user-image img').on('mouseover', function(e){
    $(this).css({"cursor":"pointer"});
  }).on('click', function(){
    window.open("{{action('UserController@show',$user->id)}} ","_blank"); //location.href = "{{action('UserController@show',$user->id)}} ";
  });


  /* ---- for stylish notification ------ */
  $('#upload').on('click', function(){

      if ($('input#workshift').is(':checked'))
      {
              //var txtl = $('textarea[name="cwsnote"]').val().trim().length;
              //console.log("length: "+ txtl);
              //if ($('select[name="timeEnd"] :selected').val() !== "0" )
              if ($('input[name="timeEnd"]').val() !== "0" )
                $.notify("CWS saved for approval.",{className:"success", globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );
              else
                $.notify("Please fill out required field before submitting.",{className:"error", globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );
      }


      if ($('input#login').is(':checked'))
              if ($('input[name="login"]').val() !== "" &&  $('textarea [name="loginReason"]').val() !== "")
                $.notify("DTRP - IN saved for approval.",{className:"success", globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );


      if ($('input#logout').is(':checked')){
              //$('input [name="shifttype"]').();

              if ($('input[name="logout"]').val() !== ""  &&  $('textarea [name="logoutReason"]').val() !== "")
                $.notify("DTRP - OUT saved for approval.",{className:"success", globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );
      }


    });
  /* ---- for stylish notification ------ */




 /* ---- DTR LOCKING ------ */
  $('#unlock').fadeOut();
  $('#lockDTR').on('click', function(){

    var reply = confirm("\n\nThis will mark your DTR as verified and will be submitted to Finance for payroll processing. Any form of dispute found after this verification will be proccessed in the next payroll cutoff instead.\n\n Clicking 'OK' means that you agree all entries in this DTR sheet are correct.");

    if (reply == true){
      
     var dtrshit = $('input[name="dtr"].biometrics');
     var dtrsheet = [];

     for (var c = 0; c < dtrshit.length; c++)
     {
      var valu = dtrshit[c].value;
      productionDate = $('input[name="productionDate_'+valu+'"]').val();
      ws = $('input[name="workshift_'+valu+'"]').val();
      cws_id = $('input[name="cws_id_'+valu+'"]').val();
      timeIN = $('input[name="logIN_'+valu+'"]').val();
      timeOUT = $('input[name="logOUT_'+valu+'"]').val();
      dtrpIN = $('input[name="isDTRPin_'+valu+'"]').val();
      dtrpOUT = $('input[name="isDTRPout_'+valu+'"]').val();

      hoursWorked = $('input[name="workedHours_'+valu+'"]').val();
      leaveID = $('input[name="leaveID_'+valu+'"]').val();
      leaveType = $('input[name="leaveType_'+valu+'"]').val();
      OT_billable = $('input[name="OT_billable_'+valu+'"]').val();
      OT_approved = $('input[name="OT_approved_'+valu+'"]').val();
      OT_id = $('input[name="OT_id_'+valu+'"]').val();
     

      UT = $('input[name="UT_'+valu+'"]').val();
      dtrsheet[c] = {"id":valu, "productionDate": productionDate, "workshift": ws, "cws_id":cws_id,  "timeIN":timeIN, "timeOUT":timeOUT, "dtrpIN":dtrpIN, "dtrpOUT":dtrpOUT, "hoursWorked": hoursWorked,"leaveID":leaveID, "leaveType":leaveType, "OT_billable":OT_billable, "OT_approved": OT_approved,"OT_id":OT_id, "UT":UT};
      console.log(dtrsheet[c].value);
     }

     var _token = "{{ csrf_token() }}";
     $.ajax({
                  url: "{{action('DTRController@processSheet', $user->id)}}",
                  type:'POST',
                  data:{ 

                    'dtrsheet': dtrsheet,'_token':_token
                  },

                 
                  success: function(res)
                  {
                    console.log(res);

                    $.notify("DTR sheet marked verified for payroll processing.",{className:"success", globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );
                    $('#lockDTR').removeClass('btn btn-success btn-md').addClass("text-success").html("<i class='fa fa-check fa-2x'></i> DTR Verified and Locked ");
                    $('#unlock').fadeIn().css({"visibility":"visible"});

                   $('a[data-original-title="File this OT"], a[data-original-title="Report DTRP "]').hide();
                   $('#lockDTR').delay(1000).animate({ height: 'toggle', opacity: 'toggle' }, 'slow');
                    location.reload(true);
                    //window.location = "{{action('HomeController@index')}}";
                     
                  }, error: function(res){
                    console.log("ERROR");
                    $.notify("Something went wrong. Please try again.",{className:"error", globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );
                  }


        });



     //console.log(dtrsheet);

    }
    else{
      $.notify("Make sure to validate your DTR sheet on or before cutoff period!",{className:"error", globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );
    }

  });


  $('.lockDTR2').on('click', function(){
    var productionDate = $(this).attr('data-production_date');
    var biometrics_id = $(this).attr('data-biometrics_id');

    var reply = confirm("\n\nLock DTR for "+productionDate+".\n\n Clicking 'OK' means that you agree all entries in this production date are all correct.");

    if (reply == true){
      
     var dtrshit = $('input[name="dtr"].biometrics');
     var dtrsheet = [];

      productionDate = productionDate;
      ws = $('input[name="workshift_'+biometrics_id+'"]').val();
      cws_id = $('input[name="cws_id_'+biometrics_id+'"]').val();
      timeIN = $('input[name="logIN_'+biometrics_id+'"]').val();
      timeOUT = $('input[name="logOUT_'+biometrics_id+'"]').val();
      dtrpIN = $('input[name="isDTRPin_'+biometrics_id+'"]').val();
      dtrpOUT = $('input[name="isDTRPout_'+biometrics_id+'"]').val();

      hoursWorked = $('input[name="workedHours_'+biometrics_id+'"]').val();
      leaveID = $('input[name="leaveID_'+biometrics_id+'"]').val();
      leaveType = $('input[name="leaveType_'+biometrics_id+'"]').val();
      OT_billable = $('input[name="OT_billable_'+biometrics_id+'"]').val();
      OT_approved = $('input[name="OT_approved_'+biometrics_id+'"]').val();
      OT_id = $('input[name="OT_id_'+biometrics_id+'"]').val();
     

      UT = $('input[name="UT_'+biometrics_id+'"]').val();
      dtrsheet[0] = {"id":biometrics_id, "productionDate": productionDate, "workshift": ws, "cws_id":cws_id,  "timeIN":timeIN, "timeOUT":timeOUT, "dtrpIN":dtrpIN, "dtrpOUT":dtrpOUT, "hoursWorked": hoursWorked,"leaveID":leaveID, "leaveType":leaveType, "OT_billable":OT_billable, "OT_approved": OT_approved,"OT_id":OT_id, "UT":UT};
      console.log(dtrsheet[0].value);
    

     var _token = "{{ csrf_token() }}";
     $.ajax({
                  url: "{{action('DTRController@processSheet', $user->id)}}",
                  type:'POST',
                  data:{ 

                    'dtrsheet': dtrsheet,'_token':_token
                  },

                 
                  success: function(res)
                  {
                    console.log(res);

                    $.notify("DTR sheet marked verified for payroll processing.",{className:"success", globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );
                    $('#lockDTR').removeClass('btn btn-success btn-md').addClass("text-success").html("<i class='fa fa-check fa-2x'></i> DTR Verified and Locked ");
                    $('#unlock').fadeIn().css({"visibility":"visible"});

                   $('a[data-original-title="File this OT"], a[data-original-title="Report DTRP "]').hide();
                   $('#lockDTR').delay(1000).animate({ height: 'toggle', opacity: 'toggle' }, 'slow');
                    location.reload(true);
                    //window.location = "{{action('HomeController@index')}}";
                     
                  }, error: function(res){
                    console.log("ERROR");
                    $.notify("Something went wrong. Please try again.",{className:"error", globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );
                  }


        });



     //console.log(dtrsheet);

    }
    else{
      $.notify("Make sure to validate your DTR sheet on or before cutoff period!",{className:"error", globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );
    }

  });


  $('.unlockPD.pull-left.btn.btn-xs.btn-default').on('click', function(){
    var productionDate = $(this).attr('data-production_date');
    var biometrics_id = $(this).attr('data-biometrics_id');

    var reply = confirm("\n\nSend request to UNLOCK DTR for production date: "+productionDate+".\n\n ");

    if (reply == true){
      
     
    

     var _token = "{{ csrf_token() }}";
     var payrollPeriod = [];
     
        payrollPeriod.push(productionDate);
    
     $.ajax({
                  url: "{{action('DTRController@requestUnlock', $user->id)}}",
                  type:'POST',
                  data:{ 

                    'payrollPeriod': payrollPeriod,
                    '_token':_token

                  },

                 
                  success: function(res)
                  {
                    console.log(res);

                    $.notify(res.message,{className:"success", globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );
                    $('#unlock_'+biometrics_id).fadeOut();
                    location.reload(true);
                    //window.location = "{{action('HomeController@index')}}";
                     
                  }, error: function(res){
                    console.log("ERROR");
                    console.log(res);
                    $.notify(res.message,{className:"error", globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );
                  }


        });
    



     //console.log(dtrsheet);

    }
    else{
      $.notify("Make sure to validate your DTR sheet on or before cutoff period!",{className:"error", globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );
    }

  });

 /* ---- DTR LOCKING ------ */



  @if ($anApprover) /* ---- APPROVER ONLY ------ */

  //-- DELETE CWS from modal
  $('.delBtn').on('click',function(e){ 
    var cwsID = $(this).attr('data-id');
    var _token = "{{ csrf_token() }}";
    
        $.ajax({
              url: "{{action('UserCWSController@deleteCWS')}}",
              type:'POST',
              data:{ 
                'id': cwsID,
                '_token':_token
              },
              success: function(response){
                console.log(response);
                location.reload(true);
              }
              
              });
  }); //end delBtn





  //********** APPROVE, DENY buttons on approver's view of DTRP modal details ************
  $('.row').on('click', '.process', function(e)
    {
      e.preventDefault(); e.stopPropagation();
      var _token = "{{ csrf_token() }}";
      var processAction = $(this).attr('data-action');
      var id = $(this).attr('data-id');
      var notif = $(this).attr('data-notifID');
      var notifType = $(this).attr('data-notifType');
      console.log("process: " + processAction);
      console.log("ID: " + id);

          //--- update user notification first
          $.ajax({
              url: "{{action('UserNotificationController@process')}}",
              type:'POST',
              data:{ 
                'id': notif,
                '_token':_token
              },

             
              success: function(response)
              {
                console.log(response);

                switch(notifType){
                    case '6': { //cws

                                  $.ajax({
                                            url: "{{action('UserCWSController@process')}}",
                                            type:'POST',
                                            data:{ 

                                              'id': id,
                                              'isApproved': processAction,
                                              '_token':_token
                                            },

                                           
                                            success: function(res)
                                            {
                                              console.log(res);
                                              $('#myModal_CWS'+notif).modal('hide');
                                              location.reload(true);
                                              //window.location = "{{action('HomeController@index')}}";
                                               
                                            }, error: function(res){
                                              console.log("ERROR");
                                            }


                                  });

                              }break; 

                    case '7': {//ot
                                  $.ajax({
                                            url: "{{action('UserOTController@process')}}",
                                            type:'POST',
                                            data:{ 

                                              'id': id,
                                              'isApproved': processAction,
                                              '_token':_token
                                            },

                                           
                                            success: function(res)
                                            {
                                              console.log(res);
                                              $('#myModal_DTRP'+notif).modal('hide');
                                              location.reload(true);
                                              //window.location = "{{action('HomeController@index')}}";
                                               
                                            }
                                  });
                              }break; 

                    case '8': {//in
                                  // var h =  $('select[name="hour"]').find(':selected').val();
                                  // var m = $('select[name="min"]').find(':selected').val();
                                  // var a = $('input[name="am" ]:checked').val();

                                  // if(h==0 || m==0){

                                  //   $.notify("Please select time",{className:"error", globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );

                                  // }else{

                                  //   var login = h+':'+m+' '+a;
                                    $.ajax({
                                              url: "{{action('UserDTRPController@process')}}",
                                              type:'POST',
                                              data:{ 

                                                'id': id,
                                                'isApproved': processAction,
                                                //'login': login,
                                                '_token':_token
                                              },

                                             
                                              success: function(res)
                                              {
                                                console.log(res);
                                                $('#myModal_DTRP'+notif).modal('hide');
                                                location.reload(true);
                                               // window.location = "{{action('HomeController@index')}}";
                                                 
                                              }
                                    });

                                 // }

                                  

                              }break; 

                    case '9': { //out
                                $.ajax({
                                            url: "{{action('UserDTRPController@process')}}",
                                            type:'POST',
                                            data:{ 

                                              'id': id,
                                              'isApproved': processAction,
                                              '_token':_token
                                            },

                                           
                                            success: function(res)
                                            {
                                              console.log(res);
                                              $('#myModal_DTRP'+notif).modal('hide');
                                              location.reload(true);
                                             // window.location = "{{action('HomeController@index')}}";
                                               
                                            }
                                  });

                              }break;
                  }

                 
                 
              }
          });

    });

  @endif  /* ---- END APPROVER ONLY ------ */





    $(".incr-btn").on("click", function (e) {
            var $button = $(this);
            var oldValue = $button.parent().find('.quantity').val();
            var maxval = $('input[name="quantity"').attr('data-max'); // $button.parent().find('.quantity').attr('data-max');
            console.log(maxval);

            if (oldValue >= maxval )
              {
                $button.parent().find('.incr-btn[data-action="increase"]').addClass('inactive');
                $('#plus').fadeOut();
              } else
              {
                $button.parent().find('.incr-btn[data-action="decrease"]').removeClass('inactive');
                $button.parent().find('.incr-btn[data-action="increase"]').removeClass('inactive');
                $('#plus').fadeIn();

              }
            
            if ($button.data('action') == "increase") {
                var newVal = Math.round((parseFloat(oldValue) + 0.5),2);
                if (newVal >= maxval) $('#plus').fadeOut();
            } else {
                // Don't allow decrementing below 1
                if (oldValue > 1) {
                    var newVal = parseFloat(oldValue) - 0.5;
                } else {
                    newVal = 1;
                    $button.addClass('inactive');
                }
                $('#plus').fadeIn();
            }
            $button.parent().find('.quantity').val(newVal);
            e.preventDefault();
    }); //end incr-btn


    

}); //END function()




    





</script>
@stop