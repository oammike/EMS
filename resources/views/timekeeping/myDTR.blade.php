@extends('layouts.main')


@section('metatags')
  <title>Daily Time Record | {{$user->firstname}}</title>
    <meta name="description" content="profile page">
 <link href="{{URL::asset('storage/resources/js/bootstrap-material-datetimepicker.css')}}" rel="stylesheet" /> 
<!--  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" /> -->
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
                                        
                                        <h3 style="text-shadow: 1px 2px #000000; text-transform:uppercase" class="widget-user-username">{{$user->lastname}}, {{$user->firstname}}&nbsp;@if(!is_null($user->nickname)) (<small><em style="color:#fff">{{$user->nickname}}</em> </small>) @endif</h3>
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
                                              <p class="description-header"><i class="fa fa-address-card-o margin-r-5"></i> Biometric Code : </p>
                                              <span class="description-text text-primary">{{$user->accesscode}} </span>
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

                                    <div class="row" id="thedtr">
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
                                                  <i class="fa fa-arrow-right"></i></a>

                                                  <table class="table table-bordered pull-right text-center" style="width: 60%;margin-right: 30px">
                                                    <tr>
                                                      <th class="text-center" style="background: rgba(72, 178, 219, 0.4);font-size: smaller"><i class="fa fa-plane"></i> VL Credits</th>
                                                      <th class="text-center"style="background: rgba(234, 0, 0, 0.4);font-size: smaller;"><i class="fa fa-stethoscope"></i> SL Credits</th>
                                                    </tr>
                                                    <tr>

                                                      @if ($currentVLbalance == "N/A")
                                                      <td style="background: rgba(72, 178, 219, 0.1); font-size: smaller;color:#000; font-weight: bolder;" >
                                                        <a href="{{action('UserVLController@showCredits',$user->id)}} "> <span class="text-black">{{$currentVLbalance}}</span> <i class="fa fa-external-link"></i>  </small></td>

                                                      <td style="background: rgba(234, 0, 0, 0.4); font-size: smaller;color:#000;font-weight: bolder;" >{{$currentSLbalance}} </td>
                                                      @else
                                                      <td style="background: rgba(72, 178, 219, 0.1); font-size: smaller;color:#000; font-weight: bolder;" >
                                                        <a href="{{action('UserVLController@showCredits',$user->id)}} "> <span class="text-black">{{$currentVLbalance}}</span> <i class="fa fa-external-link"></i>  <br/><em style="font-size: xx-small;">as of {{date('M d', strtotime($vlEarnings[0]->period))}}  cutoff</em></small></td>

                                                          @if ($currentSLbalance == "N/A")
                                                          <td style="background: rgba(234, 0, 0, 0.1); font-size: smaller;color:#000;font-weight: bolder;" >
                                                          {{$currentSLbalance}}</td>

                                                          @else
                                                          <td style="background: rgba(234, 0, 0, 0.1); font-size: smaller;color:#000;font-weight: bolder;" >
                                                        <a href="{{action('UserVLController@showCredits',$user->id)}}#slpage"> <span class="text-black">{{$currentSLbalance}}</span> <i class="fa fa-external-link"></i>  <br/><em style="font-size: xx-small;">* as of {{date('M d', strtotime($slEarnings[0]->period))}}  cutoff</em></small></td>

                                                          @endif
                                                          
                                                      

                                                      @endif
                                                      
                                                    </tr>
                                                    
                                                  </table>

                                                </div>

                                                </div>

                                                <h4 class="text-center"><br/><br/>
                                                <small>Cutoff Period: </small><br/>
                                                <span class="text-success">&nbsp;&nbsp; {{$cutoff}} &nbsp;&nbsp; </span>

                                                  
                                              </h4>

                                              

                                              <!-- ********** DTR BUTTONS ************** -->
                                              
                                              @if(count($payrollPeriod) > 1 && ( count($myDTR) >= count($payrollPeriod) ) )
                                              <!-- <a id="lockDTR" class="btn btn-danger btn-md pull-left"><i class="fa fa-unlock"></i> Lock Entire DTR Sheet </a> -->
                                              @endif
                                              <a id="unlock" class="btn btn-sm btn-default pull-left" style="margin-left: 5px;"><i class="fa fa-unlock"></i> Request Unlock </a>
                                              <a target="_blank" href="{{action('LogsController@viewRawBiometricsData', $user->id)}}" class="btn btn-xs btn-primary pull-right"><i class="fa fa-search"></i> View Uploaded Biometrics</a>

                                              <a href="{{action('UserController@createSchedule', $user->id)}}" style="margin-right: 5px" class="btn btn-xs btn-primary pull-right"><i class="fa fa-calendar-plus-o"></i>  Plot Work Sched</a>

                                              <a href="{{action('UserController@userRequests', $user->id)}}" style="margin-right: 5px" class="btn btn-xs btn-primary pull-right"><i class="fa fa-file-o"></i>  DTR Requests</a>

                                              <a href="{{action('UserController@show', $user->id)}}" style="margin-right: 5px" class="btn btn-xs btn-primary pull-right"><i class="fa fa-address-card-o"></i>  My Profile</a>
                                              <br/><br/>

                                              <!-- ********** DTR BUTTONS ************** -->
                                              <!-- ********** DTR BUTTONS ************** -->

                                              @if ($anApprover || (!$isBackoffice && $isWorkforce) )

                                              <h5 class="pull-left text-danger">

                                                @if (count($payrollPeriod) == 1 && count($verifiedDTR->where('productionDate',$paystart)) > 0  )
                                                &nbsp;&nbsp;&nbsp;<i class="fa fa-lock"></i> 

                                                DTR for this production date is locked.
                                                <a id="unlockByTL" class="btn btn-md btn-success pull-left" style="margin-left: 5px;"><i class="fa fa-unlock"></i> Unlock DTR Now </a>

                                                @else

                                                

                                                @endif
                                                </h5> 

                                              @else

                                              <h5 class="pull-left text-danger"><!-- &nbsp;&nbsp;&nbsp;<i class="fa fa-lock"></i> DTR Sheet is Locked  -->
                                                <a id="unlock" class="btn btn-xs btn-default pull-left" style="margin-left: 5px;"><i class="fa fa-unlock"></i> Request Unlock </a></h5> 


                                              @endif
                                              


                                              
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
                                                          <p class="pull-right">&nbsp;&nbsp; {{ $data['productionDate'] }}
                                                            </p>

                                                          <input type="hidden" name="productionDate_{{$data['biometrics_id']}}" class="dtr_{{$data['biometrics_id']}}" value="{{ $data['productionDate'] }}">

                                                             @if(!$data['hasLeave'] && ( !is_null($data['shiftStart']) && !is_null($data['shiftEnd']) ) ) 
                                                             <!-- ****** we wont need the pushpins for DTRP kasi LEAVE today **** -->
                                                            
                                                                @if(count($user->approvers) > 0)
                                                                 <strong>

                                                                  @if ( count($verifiedDTR->where('productionDate',$data['payday'])) > 0 )
                                                                  <a id="unlockPD_{{$data['biometrics_id']}}" style="font-size: smaller;" title="Request to Unlock " class="unlockPD pull-left btn btn-xs btn-default" data-production_date="{{ $data['payday'] }}" data-biometrics_id="{{$data['biometrics_id']}}"> <i class="fa fa-lock"></i> </a>

                                                                  @else

                                                                  <!-- *** WE RESTRICT LOCKING IF MAY PENDING ***  -->
                                                                  @if( ($data['hasCWS'] && is_null($data['usercws'][0]->isApproved)) || ($data['hasPendingIN'] && $data['pendingDTRPin'][0]['reviewed']!='1' ) || ($data['hasPendingOUT'] && $data['pendingDTRPout'][0]['reviewed']!='1' ) || ($data['hasLeave'] && is_null($data['leaveDetails'][0]['details']['isApproved']) )||
                                                                  ($data['hasLWOP'] && is_null($data['lwopDetails'][0]['details']['isApproved']) ) ||
                                                                  ($data['hasOT'] && is_null($data['userOT'][0]->isApproved))  )
                                                                  <a style="font-size: smaller;margin-right: 2px" title="Cannot Lock DTR " class="cannot pull-left btn btn-xs btn-danger" data-production_date="{{ $data['productionDate'] }} " data-biometrics_id="{{$data['biometrics_id']}}"> <i class="fa fa-exclamation-triangle"></i> </a>


                                                                  @else

                                                                  <a style="font-size: smaller;margin-right: 2px" title="Lock DTR " class="lockDTR2 pull-left btn btn-xs btn-primary" data-production_date="{{ $data['productionDate'] }}" data-biometrics_id="{{$data['biometrics_id']}}"> <i class="fa fa-unlock"></i> </a>

                                                                  @endif 


                                                                  

                                                                  <a style="font-size: smaller;" data-toggle="modal" data-target="#myModal_{{$data['payday']}}" title="Report DTRP " class="reportDTRP text-red pull-left btn btn-xs btn-default" href="#" > <i class="fa fa-thumb-tack"></i></a>

                                                                    @if($canPreshift || $isWorkforce)

                                                                      @if(count($data['preshift']) <= 0)
                                                                      <a id="preshift_{{$data['biometrics_id']}}" style="font-size: x-small;" title="Use Pre-shift logs" class="preshift pull-left btn btn-xs btn-default" data-production_date="{{ $data['payday'] }}" data-biometrics_id="{{$data['biometrics_id']}}"> <i class="fa fa-history"></i> </a>
                                                                      @else
                                                                      <a id="preshiftD_{{$data['biometrics_id']}}" style="font-size: x-small;" title="Disable Pre-shift logs" class="preshiftD pull-left btn btn-xs btn-warning" data-production_date="{{ $data['payday'] }}" data-biometrics_id="{{$data['biometrics_id']}}"> <i class="fa fa-times"></i> </a>

                                                                      @endif
                                                                    @endif


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

                                                                      <!-- *** WE RESTRICT LOCKING IF MAY PENDING ***  && $data['leaveDetails'][0]['details']['isApproved'] !=='0'-->
                                                                      @if( ($data['hasCWS'] && is_null($data['usercws'][0]->isApproved)) || ($data['hasPendingIN'] && $data['pendingDTRPin'][0]['reviewed']!='1' ) || ($data['hasPendingOUT'] && $data['pendingDTRPout'][0]['reviewed']!='1' ) || 
                                                                      ($data['hasLeave'] && is_null($data['leaveDetails'][0]['details']['isApproved']) ) ||
                                                                      ($data['hasLWOP'] && is_null($data['lwopDetails'][0]['details']['isApproved']) ) ||
                                                                      ($data['hasOT'] && is_null($data['userOT'][0]->isApproved))  )
                                                                      <a style="font-size: smaller;margin-right: 2px" title="Cannot Lock DTR " class="cannot pull-left btn btn-xs btn-danger" data-production_date="{{ $data['productionDate'] }}" data-biometrics_id="{{$data['biometrics_id']}}"> <i class="fa fa-exclamation-triangle"></i> </a>



                                                                      @else

                                                                      <a style="font-size: smaller;margin-right: 2px" title="Lock DTR " class="lockDTR2 pull-left btn btn-xs btn-primary" data-production_date="{{ $data['productionDate'] }}" data-biometrics_id="{{$data['biometrics_id']}}"> <i class="fa fa-unlock"></i> </a>

                                                                      @endif 


                                                                  
                                                                  <!-- <a style="font-size: larger;" data-toggle="modal" data-target="#myModal_{{$data['payday']}}" title="Report DTRP " class="reportDTRP text-red pull-left btn btn-xs btn-default" href="#" > <i class="fa fa-thumb-tack"></i></a> -->
                                                                  <a style="font-size: smaller;" data-toggle="modal" data-target="#myModal_{{$data['payday']}}" title="Report DTRP " class="reportDTRP text-red pull-left btn btn-xs btn-default" href="#" > <i class="fa fa-thumb-tack"></i></a>

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

                                                                  @endif
                                                               

                                                            @endif <!--end if not hasleave && shiftStart-->
                                                           
                                                          </td>

                                                       

                                                         <!-- we determin here if WFH -->
                                                        <?php $hasWFH = collect($wfhData)->where('biometrics_id',$data['biometrics_id']);
                                                              $ecqStatus = collect($ecq)->where('biometrics_id',$data['biometrics_id'])->sortByDesc('created_at') 

                                                              //1=AHW | 2=Hotel Stayer | 3=Shuttler | 4= Walkers | 5= Dwellers | 6= Carpool Driver | 7= Carpool Passenger
                                                              ?>
                                                        
                                                        @if(count($ecqStatus) > 0)


                                                          <td class="text-left">

                                                          @if($ecqStatus->first()->workStatus == 1) <!-- WFH -->
                                                          <a title="Work From Home (click to update)" class="setECQ pull-left btn btn-xs btn-success" data-toggle="modal" data-target="#noECQ_{{$data['biometrics_id']}}" data-bioID="{{$data['biometrics_id']}}" data-placehold="{{ date('m/d/Y',strtotime($data['productionDate'])) }}"><i class="fa fa-home"></i>  </a> 
                                                          &nbsp;{{ date('D',strtotime($data['productionDate'])) }}

                                                          @elseif($ecqStatus->first()->workStatus == 2) <!-- Hotel -->
                                                          <a title="Hotel Stayer (click to update)" class="setECQ pull-left btn btn-xs bg-purple" data-toggle="modal" data-target="#noECQ_{{$data['biometrics_id']}}" data-bioID="{{$data['biometrics_id']}}" data-placehold="{{ date('m/d/Y',strtotime($data['productionDate'])) }}"><i class="fa fa-shopping-bag"></i>  </a> 
                                                          &nbsp;{{ date('D',strtotime($data['productionDate'])) }}

                                                          @elseif($ecqStatus->first()->workStatus == 3)
                                                          <a title="Shuttler (click to update)" class="setECQ pull-left btn btn-xs btn-warning" data-toggle="modal" data-target="#noECQ_{{$data['biometrics_id']}}" data-bioID="{{$data['biometrics_id']}}" data-placehold="{{ date('m/d/Y',strtotime($data['productionDate'])) }}"><i class="fa fa-car"></i> </a> 
                                                          &nbsp;{{ date('D',strtotime($data['productionDate'])) }}  

                                                          @elseif($ecqStatus->first()->workStatus == 4)
                                                          <a title="Walker (click to update)" class="setECQ pull-left btn btn-xs btn-danger" data-toggle="modal" data-target="#noECQ_{{$data['biometrics_id']}}" data-bioID="{{$data['biometrics_id']}}" data-placehold="{{ date('m/d/Y',strtotime($data['productionDate'])) }}"><i class="fa fa-blind"></i> </a> 
                                                          &nbsp;{{ date('D',strtotime($data['productionDate'])) }}  

                                                          @elseif($ecqStatus->first()->workStatus == 5)
                                                          <a title="Dweller (click to update)" class="setECQ pull-left btn btn-xs bg-aqua" data-toggle="modal" data-target="#noECQ_{{$data['biometrics_id']}}" data-bioID="{{$data['biometrics_id']}}" data-placehold="{{ date('m/d/Y',strtotime($data['productionDate'])) }}"><i class="fa fa-shower"></i> </a> 
                                                          &nbsp;{{ date('D',strtotime($data['productionDate'])) }} 

                                                          @elseif($ecqStatus->first()->workStatus == 6)
                                                          <a title="Carpool Driver (click to update)" class="setECQ pull-left btn btn-xs" style="background-color: #da12f3;color:#fff" data-toggle="modal" data-target="#noECQ_{{$data['biometrics_id']}}" data-bioID="{{$data['biometrics_id']}}" data-placehold="{{ date('m/d/Y',strtotime($data['productionDate'])) }}"><i class="fa fa-tachometer"></i> </a>&nbsp; {{ date('D',strtotime($data['productionDate'])) }}  

                                                          @elseif($ecqStatus->first()->workStatus == 7)
                                                          <a title="Carpool Passenger (click to update)" class="setECQ pull-left btn btn-xs" style="background-color: #1219f3; color:#fff" data-toggle="modal" data-target="#noECQ_{{$data['biometrics_id']}}" data-bioID="{{$data['biometrics_id']}}" data-placehold="{{ date('m/d/Y',strtotime($data['productionDate'])) }}"><i class="fa fa-users"></i> </a>&nbsp;  {{ date('D',strtotime($data['productionDate'])) }}  


                                                          @endif

                                                        


                                                                <!-- MODAL FOR NO ECQ SET -->
                                                                <div class="modal fade text-left" id="noECQ_{{$data['biometrics_id']}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                                  <div class="modal-dialog">
                                                                    <div class="modal-content">
                                                                      <div class="modal-header">
                                                                        
                                                                          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                                          <h4 class="modal-title text-danger" id="myModalLabel"><i class="fa fa-exclamation-triangle"></i> Set ECQ status</h4>
                                                                        
                                                                      </div>
                                                                      <div class="modal-body">
                                                                      
                                                                       Please select ECQ status, specify inclusive dates, and click 'Save' button. <br/><br/>
                                                                       
                                                                       @foreach($allECQ as $e)

                                                                    <?php switch($e->id){ 

                                                                          case '1' : { $fa="fa-home"; $attr=null; $btn='btn-success';}break; //WFH -->
                                                                          case '2' : { $fa="fa-shopping-bag"; $attr="null"; $btn='bg-purple';}break; //<!-- Hotel -->
                                                                          case '3' : { $fa="fa-car"; $attr=null; $btn='btn-warning';};break; 
                                                                          case '4' : { $fa="fa-blind"; $attr=null; $btn='btn-danger';};break;
                                                                          case '5' : { $fa="fa-shower"; $attr=null; $btn='bg-aqua';};break;
                                                                          case '6' : { $fa="fa-tachometer"; $attr="background-color: #da12f3;color:#fff"; $btn=null;};break;
                                                                          case '7' : {$fa="fa-users"; $attr="background-color: #1219f3;color:#fff"; $btn=null;};break;
                                                                        }?>


                                                                          @if($e->id === $ecqStatus->first()->workStatus)
                                                                          <label>
                                                                            <input type="radio" value="{{$e->id}}" name="ecqstat_{{$data['biometrics_id']}}" checked="checked" />&nbsp; <i class="fa {{$fa}}"> </i> {{$e->name}} 
                                                                          </label><br/>
                                                                          @else
                                                                          <label>
                                                                            <input type="radio" value="{{$e->id}}" name="ecqstat_{{$data['biometrics_id']}}" />&nbsp; <i class="fa {{$fa}}"> </i> {{$e->name}} 
                                                                          </label><br/>

                                                                          @endif

                                                                       @endforeach

                                                                       <br/><br/>
                                                                       <label>Effective from:</label>
                                                                       <input type="text" name="ecqStart_{{$data['biometrics_id']}}" 
                                                                            placeholder class="form-control datepicker"  /> <br/>

                                                                       <label>Effective until:</label>
                                                                       <input type="text" name="ecqEnd_{{$data['biometrics_id']}}" 
                                                                            placeholder="{{ date('m/d/Y',strtotime($data['productionDate'])) }}" class="form-control datepicker"  />
                                                                       
                                                                      </div>
                                                                      <div class="modal-footer no-border">
                                                                        <button type="button" class="btn btn-default btn-md pull-right " data-dismiss="modal"> <i class="fa fa-times"></i> Cancel</button>
                                                                        <button type="submit" class="updateECQ btn btn-success btn-md pull-right" data-bioID="{{$data['biometrics_id']}}" style="margin-right:5px" > <i class="fa fa-save" ></i> Save ECQ Status </button>
                                                                        
                                                                      </div>
                                                                    </div>
                                                                  </div>
                                                                </div>
                                                                <!-- MODAL FOR NO ecq SET -->

                                                          </td>

                                                        @elseif(count($hasWFH) > 0 && $user->isWFH)
                                                        <td class="text-left">
                                                          <a title="Work From Home (click to update)" class="setECQ pull-left btn btn-xs btn-success" data-toggle="modal" data-target="#noECQ_{{$data['biometrics_id']}}" data-bioID="{{$data['biometrics_id']}}" data-placehold="{{ date('m/d/Y',strtotime($data['productionDate'])) }}"><i class="fa fa-home"></i>  </a> 
                                                          &nbsp;{{ date('D',strtotime($data['productionDate'])) }}
                                                                <!-- MODAL FOR NO ECQ SET -->
                                                                <div class="modal fade text-left" id="noECQ_{{$data['biometrics_id']}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                                  <div class="modal-dialog">
                                                                    <div class="modal-content">
                                                                      <div class="modal-header">
                                                                        
                                                                          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                                          <h4 class="modal-title text-danger" id="myModalLabel"><i class="fa fa-exclamation-triangle"></i> Set ECQ status</h4>
                                                                        
                                                                      </div>
                                                                      <div class="modal-body">
                                                                      
                                                                       Please select ECQ status, specify inclusive dates, and click 'Save' button. <br/><br/>
                                                                       
                                                                       @foreach($allECQ as $e)

                                                                    <?php switch($e->id){ 

                                                                          case '1' : { $fa="fa-home"; $attr=null; $btn='btn-success';}break; //WFH -->
                                                                          case '2' : { $fa="fa-shopping-bag"; $attr="null"; $btn='bg-purple';}break; //<!-- Hotel -->
                                                                          case '3' : { $fa="fa-car"; $attr=null; $btn='btn-warning';};break; 
                                                                          case '4' : { $fa="fa-blind"; $attr=null; $btn='btn-danger';};break;
                                                                          case '5' : { $fa="fa-shower"; $attr=null; $btn='bg-aqua';};break;
                                                                          case '6' : { $fa="fa-tachometer"; $attr="background-color: #da12f3;color:#fff"; $btn=null;};break;
                                                                          case '7' : {$fa="fa-users"; $attr="background-color: #1219f3;color:#fff"; $btn=null;};break;
                                                                        }?>


                                                                          @if($e->id === 1)
                                                                          <label>
                                                                            <input type="radio" value="{{$e->id}}" name="ecqstat_{{$data['biometrics_id']}}" checked="checked" />&nbsp; <i class="fa {{$fa}}"> </i> {{$e->name}} 
                                                                          </label><br/>
                                                                          @else
                                                                          <label>
                                                                            <input type="radio" value="{{$e->id}}" name="ecqstat_{{$data['biometrics_id']}}" />&nbsp; <i class="fa {{$fa}}"> </i> {{$e->name}} 
                                                                          </label><br/>

                                                                          @endif

                                                                       @endforeach

                                                                       <br/><br/>
                                                                       <label>Effective from:</label>
                                                                       <input type="text" name="ecqStart_{{$data['biometrics_id']}}" 
                                                                            placeholder class="form-control datepicker"  /> <br/>

                                                                       <label>Effective until:</label>
                                                                       <input type="text" name="ecqEnd_{{$data['biometrics_id']}}" 
                                                                            placeholder="{{ date('m/d/Y',strtotime($data['productionDate'])) }}" class="form-control datepicker"  />
                                                                       
                                                                      </div>
                                                                      <div class="modal-footer no-border">
                                                                        <button type="button" class="btn btn-default btn-md pull-right " data-dismiss="modal"> <i class="fa fa-times"></i> Cancel</button>
                                                                        <button type="submit" class="updateECQ btn btn-success btn-md pull-right" data-bioID="{{$data['biometrics_id']}}" style="margin-right:5px" > <i class="fa fa-save" ></i> Save ECQ Status </button>
                                                                        
                                                                      </div>
                                                                    </div>
                                                                  </div>
                                                                </div>
                                                                <!-- MODAL FOR NO ecq SET -->

                                                        </td>
                                                        @elseif($user->isWFH && $data['shiftStart2'] == '* RD *')
                                                        <td class="text-left">
                                                          <a title="Work From Home (click to update)" class="setECQ pull-left btn btn-xs btn-success" data-toggle="modal" data-target="#noECQ_{{$data['biometrics_id']}}" data-bioID="{{$data['biometrics_id']}}" data-placehold="{{ date('m/d/Y',strtotime($data['productionDate'])) }}"><i class="fa fa-home"></i>  </a> 
                                                          &nbsp;{{ date('D',strtotime($data['productionDate'])) }}
                                                                <!-- MODAL FOR NO ECQ SET -->
                                                                <div class="modal fade text-left" id="noECQ_{{$data['biometrics_id']}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                                  <div class="modal-dialog">
                                                                    <div class="modal-content">
                                                                      <div class="modal-header">
                                                                        
                                                                          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                                          <h4 class="modal-title text-danger" id="myModalLabel"><i class="fa fa-exclamation-triangle"></i> Set ECQ status</h4>
                                                                        
                                                                      </div>
                                                                      <div class="modal-body">
                                                                      
                                                                       Please select ECQ status, specify inclusive dates, and click 'Save' button. <br/><br/>
                                                                       
                                                                       @foreach($allECQ as $e)

                                                                    <?php switch($e->id){ 

                                                                          case '1' : { $fa="fa-home"; $attr=null; $btn='btn-success';}break; //WFH -->
                                                                          case '2' : { $fa="fa-shopping-bag"; $attr="null"; $btn='bg-purple';}break; //<!-- Hotel -->
                                                                          case '3' : { $fa="fa-car"; $attr=null; $btn='btn-warning';};break; 
                                                                          case '4' : { $fa="fa-blind"; $attr=null; $btn='btn-danger';};break;
                                                                          case '5' : { $fa="fa-shower"; $attr=null; $btn='bg-aqua';};break;
                                                                          case '6' : { $fa="fa-tachometer"; $attr="background-color: #da12f3;color:#fff"; $btn=null;};break;
                                                                          case '7' : {$fa="fa-users"; $attr="background-color: #1219f3;color:#fff"; $btn=null;};break;
                                                                        }?>


                                                                          @if($e->id === 1)
                                                                          <label>
                                                                            <input type="radio" value="{{$e->id}}" name="ecqstat_{{$data['biometrics_id']}}" checked="checked" />&nbsp; <i class="fa {{$fa}}"> </i> {{$e->name}} 
                                                                          </label><br/>
                                                                          @else
                                                                          <label>
                                                                            <input type="radio" value="{{$e->id}}" name="ecqstat_{{$data['biometrics_id']}}" />&nbsp; <i class="fa {{$fa}}"> </i> {{$e->name}} 
                                                                          </label><br/>

                                                                          @endif

                                                                       @endforeach

                                                                       <br/><br/>
                                                                       <label>Effective from:</label>
                                                                       <input type="text" name="ecqStart_{{$data['biometrics_id']}}" 
                                                                            placeholder class="form-control datepicker"  /> <br/>

                                                                       <label>Effective until:</label>
                                                                       <input type="text" name="ecqEnd_{{$data['biometrics_id']}}" 
                                                                            placeholder="{{ date('m/d/Y',strtotime($data['productionDate'])) }}" class="form-control datepicker"  />
                                                                       
                                                                      </div>
                                                                      <div class="modal-footer no-border">
                                                                        <button type="button" class="btn btn-default btn-md pull-right " data-dismiss="modal"> <i class="fa fa-times"></i> Cancel</button>
                                                                        <button type="submit" class="updateECQ btn btn-success btn-md pull-right" data-bioID="{{$data['biometrics_id']}}" style="margin-right:5px" > <i class="fa fa-save" ></i> Save ECQ Status </button>
                                                                        
                                                                      </div>
                                                                    </div>
                                                                  </div>
                                                                </div>
                                                                <!-- MODAL FOR NO ecq SET -->
                                                        </td>
                                                        @else 

                                                        <td class="text-left">
                                                           <a  title="No GCQ status (click to update)" data-toggle="modal" data-target="#noECQ_{{$data['biometrics_id']}}" class="setECQ pull-left btn btn-xs btn-default" data-bioID="{{$data['biometrics_id']}}" data-placehold="{{ date('m/d/Y',strtotime($data['productionDate'])) }}" href="#" > <i class="text-danger fa fa-exclamation-triangle" style="font-size: x-small;"></i> &nbsp; {{ date('D',strtotime($data['productionDate'])) }}  </a>

                                                            <!-- MODAL FOR NO ECQ SET -->
                                                                <div class="modal fade text-left" id="noECQ_{{$data['biometrics_id']}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                                  <div class="modal-dialog">
                                                                    <div class="modal-content">
                                                                      <div class="modal-header">
                                                                        
                                                                          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                                          <h4 class="modal-title text-danger" id="myModalLabel"><i class="fa fa-exclamation-triangle"></i> No Community Quarantine work status defined.</h4>
                                                                        
                                                                      </div>
                                                                      <div class="modal-body">
                                                                      
                                                                       Please select GCQ work status, specify inclusive dates, and click 'Save' button. <br/><br/>
                                                                       @foreach($allECQ as $e)

                                                                    <?php switch($e->id){ 

                                                                          case '1' : { $fa="fa-home"; $attr=null; $btn='btn-success';}break; //WFH -->
                                                                          case '2' : { $fa="fa-shopping-bag"; $attr="null"; $btn='bg-purple';}break; //<!-- Hotel -->
                                                                          case '3' : { $fa="fa-car"; $attr=null; $btn='btn-warning';};break; 
                                                                          case '4' : { $fa="fa-blind"; $attr=null; $btn='btn-danger';};break;
                                                                          case '5' : { $fa="fa-shower"; $attr=null; $btn='bg-aqua';};break;
                                                                          case '6' : { $fa="fa-tachometer"; $attr="background-color: #da12f3;color:#fff"; $btn=null;};break;
                                                                          case '7' : {$fa="fa-users"; $attr="background-color: #1219f3;color:#fff"; $btn=null;};break;
                                                                        }?>


                                                                          <label>
                                                                            <input type="radio" value="{{$e->id}}" name="ecqstat_{{$data['biometrics_id']}}" />&nbsp; <i class="fa {{$fa}}"> </i> 
                                                                            {{$e->name}} @if ($e->id=='1') <em>(at home worker)</em> @endif
                                                                              
                                                                           

                                                                          </label><br/>

                                                                       @endforeach
                                                                       <br/><br/>
                                                                       <label>Effective from:</label>
                                                                       <input type="text" name="ecqStart_{{$data['biometrics_id']}}" 
                                                                            placeholder class="form-control datepicker"  /> <br/>

                                                                       <label>Effective until:</label>
                                                                       <input type="text" name="ecqEnd_{{$data['biometrics_id']}}" 
                                                                            placeholder="{{ date('m/d/Y',strtotime($data['productionDate'])) }}" class="form-control datepicker"  />
                                                                       
                                                                      </div>
                                                                      <div class="modal-footer no-border">
                                                                        <button type="button" class="btn btn-default btn-md pull-right " data-dismiss="modal"> <i class="fa fa-times"></i> Cancel</button>
                                                                        <button type="submit" class="updateECQ btn btn-success btn-md pull-right" data-bioID="{{$data['biometrics_id']}}" style="margin-right:5px" > <i class="fa fa-save" ></i> Save GCQ Status </button>
                                                                        
                                                                      </div>
                                                                    </div>
                                                                  </div>
                                                                </div>
                                                                <!-- MODAL FOR NO ecq SET -->



                                                         
                                                        </td>

                                                        @endif

                                                         <!---- *****  WORK SCHED --------->

                                                         @if ($data['shiftStart'] == null || $data['shiftEnd'] == null)
                                                        <td class="text-center text-danger">

                                                           @if($isExempt)
                                                               <strong class="text-primary" style="font-size:0.8em; font-style: italic;" >{{$exemptEmp[0]->name}} </strong><br/>
                                                               @endif

                                                          <strong style="font-size:0.6em;"><em>No Plotted Schedule </em></strong>
                                                          
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

                                                           <?php /*@if($data['isFlexitime']) <strong class="text-green"><i class="fa fa-refresh"></i> Flexi Time</strong><br/> @endif */ ?>

                                                            @if($isExempt)
                                                               <strong class="text-primary" style="font-size:0.6em; font-style: italic;" >{{$exemptEmp[0]->name}} </strong><br/>
                                                               @endif


                                                           {!! $data['shiftStart2'] !!} - {!! $data['shiftEnd2'] !!}<strong><a data-toggle="modal" data-target="#myModal_CWS{{$data['payday']}}" title="View Details" class="@if ($data['usercws'][0]['isApproved'])text-green @elseif ( is_null($data['usercws'][0]['isApproved']) ) text-orange @else text-gray @endif pull-right" href="#" > <i class="fa fa-info-circle"></i></a></strong> </td>

                                                            
                                                             <input type="hidden" name="workshift_{{$data['biometrics_id']}}" class="dtr_{{$data['biometrics_id']}}" value="{{$data['shiftStart']}} - {{$data['shiftEnd2']}}" />

                                                             <input type="hidden" name="cws_id_{{$data['biometrics_id']}}" class="dtr_{{$data['biometrics_id']}}" value="{{$data['usercws'][0]['id']}}" />

                                                             @include('layouts.modals-RDoverride', [
                                                                          'modelID' => $data['biometrics_id'],
                                                                          'modalTitle'=>'Mark as', 
                                                                          'modelName'=>"Rest Day",
                                                                          'user_id'=>$user->id,
                                                                          'modalMessage'=>"You are about to mark this production date as 'REST DAY' and disregard the displayed biometric logs, along with the entitlement of filing this as a RestDay-OT.",
                                                                          'modelRoute'=>'user_dtr.overrideRD',
                                                                          'modalTitle2'=>"POST",
                                                                          'formID'=>'bpass',
                                                                          'icon'=>'glyphicon-up' ])


                                                          @else

                                                            @if($theImmediateHead || $anApprover)<!-- || $canChangeSched -->
                                                            <td class="text-center">
                                                               <?php /*@if($data['isFlexitime']) <strong class="text-green"><i class="fa fa-refresh"></i> Flexi Sched</strong><br/> @endif */ ?>

                                                               @if($isExempt)
                                                               <strong class="text-primary" style="font-size:0.6em; font-style: italic;" >{{$exemptEmp[0]->name}} </strong><br/>
                                                               @endif
                                                               {!! $data['shiftStart2'] !!} - {!! $data['shiftEnd2'] !!} <!-- <strong><a data-toggle="modal" data-target="#editCWS_{{$data['payday']}}" title="Change Work Sched " class="text-primary pull-right" href="#" > <i class="fa fa-pencil"></i></a></strong> --> </td>
                                                            

                                                            @else
                                                            <td class="text-center">
                                                               
                                                               <?php /*@if($data['isFlexitime']) <strong class="text-green"><i class="fa fa-refresh"></i> Flexi Sched</strong><br/> @endif */ ?>

                                                               @if($isExempt)
                                                               <strong class="text-primary" style="font-size:0.6em; font-style: italic;" >{{$exemptEmp[0]->name}} </strong><br/>
                                                               @endif

                                                               {!! $data['shiftStart2'] !!} - {!! $data['shiftEnd2'] !!} <!-- <strong><a data-toggle="modal" data-target="#myModal_{{$data['payday']}}" title="Report DTRP " class="text-primary pull-right" href="#" > <i class="fa fa-flag-checkered"></i></a></strong> --> </td>
                                                            @endif

                                                             
                                                              <input type="hidden" name="workshift_{{$data['biometrics_id']}}" class="dtr_{{$data['biometrics_id']}}" value="{{$data['shiftStart']}} - {{$data['shiftEnd']}}" />

                                                              <input type="hidden" name="cws_id_{{$data['biometrics_id']}}" class="dtr_{{$data['biometrics_id']}}" value="0" />

                                                             

                                                                @include('layouts.modals-RDoverride', [
                                                                          'modelID' => $data['biometrics_id'],
                                                                          'modalTitle'=>'Mark as', 
                                                                          'modelName'=>"Rest Day",
                                                                          'user_id'=>$user->id,
                                                                          'modalMessage'=>"You are about to mark this production date as 'REST DAY' and disregard the displayed biometric logs, along with the entitlement of filing this as a RestDay-OT.",
                                                                          'modelRoute'=>'user_dtr.overrideRD',
                                                                          'modalTitle2'=>"POST",
                                                                          'formID'=>'bpass',
                                                                          'icon'=>'glyphicon-up' ])

                                                              

                                                         @endif

                                                         
                                                         
                                                        
                                                        @endif
                                                        <!---- ***** end WORK SCHED --------->


                                                        <!-- ******** LOG IN ********* -->

                                                        @if($data['hasLeave'])

                                                            <td class="text-center">

                                                              @if (!empty($data['logIN']) && !$data['hasLeave'])

                                                                  {!! $data['logIN'] !!}
                                                                   <input type="hidden" name="logIN_{{$data['biometrics_id']}}" class="dtr_{{$data['biometrics_id']}}" value="{{$data['logIN']}}" />
                                                                  

                                                              @else
                                                                <!-- **** new layout pag may 1st or 2nd shift leave -->
                                                                @if ($data['leaveDetails'][0]['details']['totalCredits'] == "0.50" || $data['leaveDetails'][0]['details']['totalCredits'] == "0.25")

                                                                  @if( $data['leaveDetails'][0]['details']['halfdayFrom'] == 2  )

                                                                    <strong style="font-size: x-small"><em><i class="fa {{$data['leaveDetails'][0]['icon']}} "></i>&nbsp;&nbsp; 1st shift {!! $data['leaveDetails'][0]['type'] !!}</em> </strong><br/>
                                                                    {!! $data['logIN'] !!}

                                                                    <input type="hidden" name="logIN_{{$data['biometrics_id']}}" class="dtr_{{$data['biometrics_id']}}" value="1st shift {{$data['leaveDetails'][0]['type']}}" />

                                                                 

                                                                  @else

                                                                  
                                                                  {!! $data['logIN'] !!}
                                                                  <input type="hidden" name="logIN_{{$data['biometrics_id']}}" class="dtr_{{$data['biometrics_id']}}" value="{{$data['logIN']}}" />
                                                                    
                                                                  @endif


                                                                  @if ($data['dtrpIN'] == true || $data['hasPendingIN']== true)
                                                                  <input type="hidden" name="isDTRPin_{{$data['biometrics_id']}}" class="dtr_{{$data['biometrics_id']}}" value="{{$data['dtrpIN_id']}}">

                                                                  <?php /*if ($data['hasPendingIN'])
                                                                          ($data['pendingDTRPin'][0]['reviewed']!='1') ? $instat = "text-green" : $instat = "text-purple"; 
                                                                        else
                                                                          $instat = "text-purple";*/
                                                                        ?>

                                                                   <strong><a data-toggle="modal" title="View Details" @if($data['hasPendingIN']) data-target="#myModal_dtrpDetail{{$data['pendingDTRPin']['0']['id']}}"  class="text-purple pull-right" @else data-target="#myModal_dtrpDetail{{$data['dtrpIN_id']}}"  class="text-green pull-right" @endif href="#" > <i class="fa fa-info-circle"></i> &nbsp;&nbsp;</a></strong> 

                                                                  @else
                                                                   <input type="hidden" name="isDTRPin_{{$data['biometrics_id']}}" class="dtr_{{$data['biometrics_id']}}" value="0">

                                                                  @endif



                                                                @else

                                                                  <!-- new if has VTO -->
                                                                  @if($data['hasVTO'])

                                                                     @if ( count($verifiedDTR->where('productionDate',$data['payday'])) > 0 )
                                                                       {!! $data['wholeIN'][0]['logTxt'] !!}<br/>
                                                                       {!! $data['logIN'] !!} 
                                                                     @else
                                                                        {!! $data['logIN'] !!} 
                                                                     @endif
                                                                    <!-- <strong style="font-size: x-small"><em><i class="fa {{$data['leaveDetails'][0]['icon']}} "></i>&nbsp;&nbsp;{!! $data['leaveDetails'][0]['type'] !!}</em> </strong> -->


                                                                  @else

                                                                    <strong style="font-size: x-small"><em><i class="fa {{$data['leaveDetails'][0]['icon']}} "></i>&nbsp;&nbsp;{!! $data['leaveDetails'][0]['type'] !!}</em> </strong>


                                                                  @endif


                                                                    
                                                                    <input type="hidden" name="logIN_{{$data['biometrics_id']}}" class="dtr_{{$data['biometrics_id']}}" value="{{$data['leaveDetails'][0]['type']}}" />

                                                                @endif

                                                                
                                                            


                                                              @endif
                                                              
                                                               
                                                                
 
                                                            
                                                          </td>
                                                          <input type="hidden" name="isDTRPin_{{$data['biometrics_id']}}" class="dtr_{{$data['biometrics_id']}}" value="0">


                                                       @elseif($data['hasLWOP'])

                                                            <td class="text-center">

                                                              @if (!empty($data['logIN']) && !$data['hasLWOP'])

                                                                  {!! $data['logIN'] !!}
                                                                  <input type="hidden" name="logIN_{{$data['biometrics_id']}}" class="dtr_{{$data['biometrics_id']}}" value="{{$data['logIN']}}" />
                                                                  

                                                              @else
                                                              <!-- **** new layout pag may 1st or 2nd shift leave -->
                                                                @if ($data['lwopDetails'][0]['details']['totalCredits'] == "0.50" || $data['lwopDetails'][0]['details']['totalCredits'] == "0.25")

                                                                  @if( $data['lwopDetails'][0]['details']['halfdayFrom'] == 2  )

                                                                    <strong style="font-size: x-small"><em><i class="fa {{$data['lwopDetails'][0]['icon']}} "></i>&nbsp;&nbsp; 1st shift {!! $data['lwopDetails'][0]['type'] !!}</em> </strong><br/>
                                                                    {!! $data['logIN'] !!}

                                                                     <input type="hidden" name="logIN_{{$data['biometrics_id']}}" class="dtr_{{$data['biometrics_id']}}" value="1st shift {{$data['lwopDetails'][0]['type']}}" />

                                                                  @else

                                                                  
                                                                  {!! $data['logIN'] !!}
                                                                   <input type="hidden" name="logIN_{{$data['biometrics_id']}}" class="dtr_{{$data['biometrics_id']}}" value="{{$data['logIN']}}" />
                                                                  

                                                                  @endif

                                                                @else

                                                                    <strong style="font-size: x-small"><em><i class="fa {{$data['lwopDetails'][0]['icon']}} "></i>&nbsp;&nbsp;{!! $data['lwopDetails'][0]['type'] !!}</em> </strong>

                                                                     <input type="hidden" name="logIN_{{$data['biometrics_id']}}" class="dtr_{{$data['biometrics_id']}}" value="{{$data['lwopDetails'][0]['type']}}" />
                                                             
                                                                @endif

                                                              @endif        
 
                                                            
                                                          </td>
                                                          <input type="hidden" name="isDTRPin_{{$data['biometrics_id']}}" class="dtr_{{$data['biometrics_id']}}" value="0">

                                                        

                                                        @else

                                                            <td class="text-center">

                                                              @if ($data['isRDToday'])

                                                                    @if ( (strpos($data['logIN'], "RD") !== false)  )
                                                                        {!! $data['logIN'] !!}
                                                                    @else
                                                                        ** Rest Day ** <br/>
                                                                        <small>( {!! $data['logIN'] !!} )</small>
                                                                    @endif

                                                              @else

                                                                  {!! $data['logIN'] !!}

                                                              @endif



                                                              
                                                             

                                                              
                                                               <input type="hidden" name="logIN_{{$data['biometrics_id']}}" class="dtr_{{$data['biometrics_id']}}" value="{{$data['logIN']}}">

                                                            @if ($data['dtrpIN'] == true || $data['hasPendingIN']== true)
                                                            <input type="hidden" name="isDTRPin_{{$data['biometrics_id']}}" class="dtr_{{$data['biometrics_id']}}" value="{{$data['dtrpIN_id']}}">

                                                            <?php /*if ($data['hasPendingIN'])
                                                                    ($data['pendingDTRPin'][0]['reviewed']!='1') ? $instat = "text-green" : $instat = "text-purple"; 
                                                                  else
                                                                    $instat = "text-purple";*/
                                                                  ?>

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
                                                                      'extra'=> null,
                                                                      'icon'=>'glyphicon-up' ])

                                                         @elseif ($data['hasPendingIN'] == true )
                                                          <?php  $data_notifType = '8'; 
                                                                $data_notifID = $data["payday"]; 
                                                                $data_id = $data['pendingDTRPin']['0']['id']; 

                                                                if($data["pendingDTRPin"][0]['isApproved']=='1' && is_null($data["pendingDTRPin"][0]['reviewed']) )
                                                                  {
                                                                    $extra = "Processing DTRP IN validation.";
                                                                    $mt = "Pending DTRP IN Validation";
                                                                  }
                                                                  elseif(is_null($data["pendingDTRPin"][0]['isApproved']) && is_null($data["pendingDTRPin"][0]['reviewed']) )
                                                                  {
                                                                    $extra = "Pending approval.";
                                                                    $mt = "Pending DTRP IN Request";
                                                                  } 
                                                                  else{
                                                                    $extra = ""; $mt="DTRP IN Details";
                                                                  } ?>

                                                              @include('layouts.modals-detailsDTRP', [
                                                                      'data-notifType'=> $data_notifType,
                                                                      'data-notifID'=> $data_notifID,
                                                                      'dataid'=>$data_id, 
                                                                      'modelID' => $data["pendingDTRPin"][0]['id'], 
                                                                      'modalTitle'=>$mt,
                                                                      'extra'=> $extra,
                                                                      'icon'=>'glyphicon-up' ])

                                                         @endif



                                                          <!-- ******** LOG OUT ********* -->


                                                          @if($data['hasLeave'])

                                                             <td class="text-center">

                                                              @if (!empty($data['logOUT']) && !$data['hasLeave'])

                                                                  {!! $data['logOUT'] !!} 
                                                                   <input type="hidden" name="logOUT_{{$data['biometrics_id']}}" class="dtr_{{$data['biometrics_id']}}" value="{{$data['logOUT']}}" />

                                                              @else

                                                              <!-- **** new layout pag may 1st or 2nd shift leave -->
                                                                @if ($data['leaveDetails'][0]['details']['totalCredits'] == "0.50" || $data['leaveDetails'][0]['details']['totalCredits'] == "0.25")

                                                                  @if( $data['leaveDetails'][0]['details']['halfdayFrom'] == 3  )

                                                                    {!! $data['logOUT'] !!}<br/>
                                                                    <strong style="font-size: x-small"><em><i class="fa {{$data['leaveDetails'][0]['icon']}} "></i>&nbsp;&nbsp; 2nd shift {!! $data['leaveDetails'][0]['type'] !!}</em> </strong>

                                                                    <input type="hidden" name="logOUT_{{$data['biometrics_id']}}" class="dtr_{{$data['biometrics_id']}}" value="2nd shift {{$data['leaveDetails'][0]['type']}}" />
                                                                    

                                                                  @else
                                                                    {!! $data['logOUT'] !!}
                                                                    <input type="hidden" name="logOUT_{{$data['biometrics_id']}}" class="dtr_{{$data['biometrics_id']}}" value="{{$data['logOUT']}}" />

                                                                  @endif


                                                                  @if ($data['dtrpOUT'] == true  ||  $data['hasPendingOUT']== true)

                                                                      @if($data['hasPendingOUT']== true)
                                                                        <input type="hidden" name="isDTRPout_{{$data['biometrics_id']}}" class="dtr_{{$data['biometrics_id']}}" value="{{$data['pendingDTRPout'][0]['id']}}">
                                                                        <strong>
                                                                            <a data-toggle="modal" title="View Details"  class="text-purple pull-right" data-target="#myModal_dtrpDetail{{$data['pendingDTRPout'][0]['id']}}" href="#" > <i class="fa fa-info-circle"></i> &nbsp;&nbsp;</a></strong> 

                                                                      @else

                                                                        <input type="hidden" name="isDTRPout_{{$data['biometrics_id']}}" class="dtr_{{$data['biometrics_id']}}" value="{{$data['dtrpOUT_id']}}">
                                                                         <strong>
                                                                            <a data-toggle="modal" title="View Details"  data-target="#myModal_dtrpDetail{{$data['dtrpOUT_id']}}" class="text-green pull-right"  href="#" > <i class="fa fa-info-circle"></i> &nbsp;&nbsp;</a></strong> 

                                                                      @endif

                                                                        

                                                                          

                                                                    @else
                                                                     <input type="hidden" name="isDTRPout_{{$data['biometrics_id']}}" class="dtr_{{$data['biometrics_id']}}" value="0">

                                                                  @endif <!--end pag leave with pending DTRP-->



                                                                @else

                                                                  <!-- new if has VTO -->


                                                                  @if($data['hasVTO'])

                                                                     @if ( count($verifiedDTR->where('productionDate',$data['payday'])) > 0 )
                                                                       {!! $data['wholeOUT'][0]['logTxt'] !!}<br/>
                                                                       {!! $data['logOUT'] !!} 
                                                                     @else
                                                                        {!! $data['logOUT'] !!} 
                                                                     @endif
                                                                    
                                                                    <!-- <strong style="font-size: x-small"><em><i class="fa {{$data['leaveDetails'][0]['icon']}} "></i>&nbsp;&nbsp;{!! $data['leaveDetails'][0]['type'] !!}</em> </strong> -->
                                                                  @else 

                                                                    <strong style="font-size: x-small"><em><i class="fa {{$data['leaveDetails'][0]['icon']}} "></i>&nbsp;&nbsp;{!! $data['leaveDetails'][0]['type'] !!}</em> </strong>

                                                                  @endif


                                                                 

                                                                  <input type="hidden" name="logOUT_{{$data['biometrics_id']}}" class="dtr_{{$data['biometrics_id']}}" value="{{$data['leaveDetails'][0]['type']}}" />

                                                                  @if ($data['dtrpOUT'] == true  ||  $data['hasPendingOUT']== true)

                                                                      @if($data['hasPendingOUT']== true)
                                                                        <input type="hidden" name="isDTRPout_{{$data['biometrics_id']}}" class="dtr_{{$data['biometrics_id']}}" value="{{$data['pendingDTRPout'][0]['id']}}">
                                                                        <strong>
                                                                            <a data-toggle="modal" title="View Details"  class="text-purple pull-right" data-target="#myModal_dtrpDetail{{$data['pendingDTRPout'][0]['id']}}" href="#" > <i class="fa fa-info-circle"></i> &nbsp;&nbsp;</a></strong> 

                                                                      @else

                                                                        <input type="hidden" name="isDTRPout_{{$data['biometrics_id']}}" class="dtr_{{$data['biometrics_id']}}" value="{{$data['dtrpOUT_id']}}">
                                                                         <strong>
                                                                            <a data-toggle="modal" title="View Details"  data-target="#myModal_dtrpDetail{{$data['dtrpOUT_id']}}" class="text-green pull-right"  href="#" > <i class="fa fa-info-circle"></i> &nbsp;&nbsp;</a></strong> 

                                                                      @endif

                                                                        

                                                                          

                                                                    @else
                                                                     <input type="hidden" name="isDTRPout_{{$data['biometrics_id']}}" class="dtr_{{$data['biometrics_id']}}" value="0">

                                                                  @endif <!--end pag leave with pending DTRP-->



                                                                @endif <!--end else -->
                                                             
                                                              @endif
                                                            
                                                          </td>

                                                          <input type="hidden" name="isDTRPout_{{$data['biometrics_id']}}" class="dtr_{{$data['biometrics_id']}}" value="0">


                                                         @elseif($data['hasLWOP'])

                                                             <td class="text-center">

                                                              @if (!empty($data['logOUT']) && !$data['hasLWOP'])

                                                                  {!! $data['logOUT'] !!} 
                                                                  <input type="hidden" name="logOUT_{{$data['biometrics_id']}}" class="dtr_{{$data['biometrics_id']}}" value="$data['logOUT']" />

                                                              @else

                                                              <!-- **** new layout pag may 1st or 2nd shift leave -->
                                                              
                                                                @if ($data['lwopDetails'][0]['details']['totalCredits'] == "0.50" || $data['lwopDetails'][0]['details']['totalCredits'] == "0.25")

                                                                  @if( $data['lwopDetails'][0]['details']['halfdayFrom'] == 3  )

                                                                    {!! $data['logOUT'] !!}<br/>
                                                                    <strong style="font-size: x-small"><em><i class="fa {{$data['lwopDetails'][0]['icon']}} "></i>&nbsp;&nbsp; 2nd shift {!! $data['lwopDetails'][0]['type'] !!}</em> </strong>

                                                                    <input type="hidden" name="logOUT_{{$data['biometrics_id']}}" class="dtr_{{$data['biometrics_id']}}" value="2nd shift {{$data['lwopDetails'][0]['type']}}" />
                                                                    

                                                                  @else
                                                                    {!! $data['logOUT'] !!}
                                                                    <input type="hidden" name="logOUT_{{$data['biometrics_id']}}" class="dtr_{{$data['biometrics_id']}}" value="{{$data['logOUT']}}" />

                                                                  @endif

                                                                @else  
                                                                <strong style="font-size: x-small"><em><i class="fa {{$data['lwopDetails'][0]['icon']}} "></i>&nbsp;&nbsp;{!! $data['lwopDetails'][0]['type'] !!}</em> </strong>

                                                                <input type="hidden" name="logOUT_{{$data['biometrics_id']}}" class="dtr_{{$data['biometrics_id']}}" value="{{$data['lwopDetails'][0]['type']}}" />
                                                             

                                                                @endif 
                                                                

                                                                

                                                              @endif
                                                              

                                                              
                                                              
                                                            
                                                          </td>

                                                          <input type="hidden" name="isDTRPout_{{$data['biometrics_id']}}" class="dtr_{{$data['biometrics_id']}}" value="0">

                                                        @else
                                                            <td class="text-center">

                                                              @if ($data['isRDToday'])

                                                                    @if ( (strpos($data['logOUT'], "RD") !== false)  )
                                                                        {!! $data['logOUT'] !!}
                                                                    @else
                                                                        ** Rest Day ** <br/>
                                                                        <small>( {!! $data['logOUT'] !!} )</small>
                                                                    @endif

                                                              @else

                                                                  {!! $data['logOUT'] !!}

                                                              @endif


                                                             

                                                                
                                                                <input type="hidden" name="logOUT_{{$data['biometrics_id']}}" class="dtr_{{$data['biometrics_id']}}" value="{{$data['logOUT']}}" />

                                                              @if ($data['dtrpOUT'] == true  ||  $data['hasPendingOUT']== true)

                                                                @if($data['hasPendingOUT']== true)
                                                                  <input type="hidden" name="isDTRPout_{{$data['biometrics_id']}}" class="dtr_{{$data['biometrics_id']}}" value="{{$data['pendingDTRPout'][0]['id']}}">
                                                                  <strong>
                                                                      <a data-toggle="modal" title="View Details"  class="text-purple pull-right" data-target="#myModal_dtrpDetail{{$data['pendingDTRPout'][0]['id']}}" href="#" > <i class="fa fa-info-circle"></i> &nbsp;&nbsp;</a></strong> 

                                                                @else

                                                                  <input type="hidden" name="isDTRPout_{{$data['biometrics_id']}}" class="dtr_{{$data['biometrics_id']}}" value="{{$data['dtrpOUT_id']}}">
                                                                   <strong>
                                                                      <a data-toggle="modal" title="View Details"  data-target="#myModal_dtrpDetail{{$data['dtrpOUT_id']}}" class="text-green pull-right"  href="#" > <i class="fa fa-info-circle"></i> &nbsp;&nbsp;</a></strong> 

                                                                @endif

                                                                  

                                                                    

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
                                                                          'extra' => null, 
                                                                          'icon'=>'glyphicon-up' ]); 
                                                            

                                                        @elseif ($data['hasPendingOUT'] == true )
                                                            <?php  $data_notifType = '9'; 
                                                                   $data_notifID = $data["payday"]; 
                                                                   $data_id = $data['pendingDTRPout']['0']['id'];
                                                                   if($data["pendingDTRPout"][0]['isApproved']=='1' && is_null($data["pendingDTRPout"][0]['reviewed'])){
                                                                    $extra = "Processing DTRP OUT validation.";
                                                                    $mtout ="Pending DTRP OUT Validation";
                                                                  }
                                                                  elseif(is_null($data["pendingDTRPout"][0]['isApproved']) && is_null($data["pendingDTRPout"][0]['reviewed'])){
                                                                    $extra = "Pending Approval";
                                                                    $mtout ="Pending DTRP OUT request";

                                                                  }
                                                                  else {
                                                                    $extra = "";
                                                                    $mtout ="DTRP OUT Details";
                                                                  }
                                                                   ?>

                                                                  @include('layouts.modals-detailsDTRP', [
                                                                           'data-notifType'=> $data_notifType,
                                                                            'data-notifID'=> $data_notifID,
                                                                            'dataid'=>$data_id, 
                                                                            'modelID' => $data["pendingDTRPout"][0]['id'],
                                                                            'extra' => $extra,
                                                                            'modalTitle'=>$mtout,
                                                                            'icon'=>'glyphicon-up' ]);

                                                        @endif

                                                       


                                                         <!-- ******** WORKED HOURS ********* -->


                                                         

                                                            <td class="text-center"> 
                                                              @if($data['isFlexitime'])<strong class="text-green"><i class="fa fa-refresh"></i> Flexi Sched</strong><br/> @endif 

                                                              @if($data['isFlexitime']) <span style="text-decoration:line-through"><em> @endif
                                                              
                                                              

                                                              
                                                              @if ($data['hasLeave'])

                                                                <!--$data['leaveDetails'][0]['type'] !!}-->
                                                               

                                                                <!-- new if has VTO -->
                                                                  @if($data['hasVTO'])

                                                                    @if($data['workedHours'] == "N/A")

                                                                      @if ($data['leaveDetails'][0]['details']['isApproved'])
                                                                        {{$data['leaveDetails'][0]['details']['totalHours']}}
                                                                      @else
                                                                        {{$data['workedHours']}}
                                                                      @endif
                                                                      <br/>

                                                                      <strong style="font-size: x-small"><em><i class="fa {{$data['leaveDetails'][0]['icon']}} "></i>&nbsp;&nbsp;{!! $data['leaveDetails'][0]['type'] !!}</em> </strong> &nbsp; 
                                                                     <strong><a target="_blank" class="text-primary" href="{{action('UserVLController@showVTO',$data['leaveDetails'][0]['details']['id'])}}"><i class="fa fa-info-circle"></i> </a> </strong>
                                                                    @else
                                                                       {!! $data['workedHours'] !!} 


                                                                    @endif

                                                                     
                                                                    
                                                                  @else 


                                                                  {!! $data['workedHours'] !!}

                                                                  @endif
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


                                                       

                                                       <!-- *********** OVERTIME ************* -->

                                                        @if($data['hasLeave'])

                                                             <td class="text-center"> {!! $data['billableForOT'] !!}{!! $data['OTattribute'] !!}   <br/>
                                                              

                                                              <!--  <td class="text-center"><a href="{{action('UserController@myRequests',$user->id)}}"><strong style="font-size: x-small"><em><i class="fa {{$data['leaveDetails'][0]['icon']}} "></i>&nbsp;&nbsp;{!! $data['leaveDetails'][0]['type'] !!}</em> </strong></a> -->

                                                               
                                                               <input type="hidden" name="OT_billable_{{$data['biometrics_id']}}" class="dtr_{{$data['biometrics_id']}}" value="{{$data['leaveDetails'][0]['type']}}" />
                                                            
                                                            <!--  <strong><a data-toggle="modal" title="View Details" @if($data['hasPendingIN']) data-target="#myModal_dtrpDetail{{$data['pendingDTRPin']['0']['id']}}"  class="text-purple pull-right" @else data-target="#myModal_dtrpDetail{{$data['dtrpIN_id']}}"  class="text-green pull-right" @endif href="#" > <i class="fa fa-info-circle"></i> &nbsp;&nbsp;</a></strong> 
 -->
                                                            
                                                          </td>

                                                         @elseif ($data['hasLWOP'])

                                                            <td class="text-center"> {!! $data['billableForOT'] !!} 
                                                              @if( $data['billableForOT'] !== 0) {!! $data['OTattribute'] !!}  @endif

                                                            <!--  <td class="text-center"><a href="{{action('UserController@myRequests',$user->id)}}"><strong style="font-size: x-small"><em><i class="fa {{$data['lwopDetails'][0]['icon']}} "></i>&nbsp;&nbsp;{!! $data['lwopDetails'][0]['type'] !!}</em> </strong></a>
 -->
                                                               
                                                                <!-- <input type="hidden" name="logIN_{{$data['biometrics_id']}}" class="dtr_{{$data['biometrics_id']}}" value="{{$data['lwopDetails'][0]['type']}}" /> -->

                                                                 <input type="hidden" name="OT_billable_{{$data['biometrics_id']}}" class="dtr_{{$data['biometrics_id']}}" value="{{$data['lwopDetails'][0]['type'] }}" />
                                                            
                                                           
                                                            
                                                          </td>  

                                                       <!-- **************** OVERTIME **************** -->    
                                                        @else

                                                            @if ( $data['hasOT'] && ($data['shiftStart'] != null && $data['shiftEnd'] !=null) )                                     
                                                                           
                                                                    <!-- ** we now process any pre and post OTs ** -->
                                                                    <td class="text-center"> 
                                                                        @if( count($data['userOT']) >= 1)

                                                                            <?php $postOT = collect($data['userOT'])->where('preshift',null); ?>
                                                                            <?php $preOT = collect($data['userOT'])->where('preshift',1); ?>

                                                                            @if (count($preOT) > 0)

                                                                               {{$preOT->first()->billable_hours}} <a style="font-weight: bold;font-size: smaller;" class="text-success" target="_blank" href="../user_ot/{{$preOT->first()->id}}" title="Meeting/Huddle Details">(Pre-shift)</a><br/>
                                                                             

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
                                                                             

                                                                              @if($data['shiftStart'] != null && $data['shiftEnd'] != null)
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

                                                                            @endif

                                                                            

                                                                        @else

                                                                            @if($data['shiftStart'] != null && $data['shiftEnd'] != null)

                                                                              <!-- show only if may work schedule -->
                                                                              {!! $data['billableForOT'] !!} 

                                                                            @endif

                                                                            @if(count((array)$data['approvedOT']) > 0)

                                                                                @if($data['approvedOT'] !== 0)
                                                                                  @if(is_null($data['approvedOT']->first()->preshift))
                                                                                       <a data-toggle="modal" data-target="#myModal_OT_details{{$data['payday']}}" title="View Details" class="pull-right @if ($data['approvedOT']->first()->isApproved)text-green @else text-gray @endif" style="font-size:1.2em;" href="#"><i class="fa fa-credit-card"></i></a></td>

                                                                                      @if($data['shiftStart'] != null && $data['shiftEnd'] != null)
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

                                                                                  @else

                                                                                     {!! $data['OTattribute'] !!}

                                                                                     @if($data['shiftStart'] != null && $data['shiftEnd'] != null)

                                                                                        @if( $data['backOffice'] && count($data['hdToday']) > 0  && strlen($data['logIN']) < 100)
                                                                                          @include('layouts.modals-OT_HD', [
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


                                                                                  @endif
                                                                                @endif

                                                                            @else

                                                                              @if ($data['userOT']->first()->isApproved)
                                                                                  <a data-toggle="modal" data-target="#myModal_OT_details{{$data['payday']}}" title="View Details" class="pull-right text-green" style="font-size:1.2em;" href="#"><i class="fa fa-credit-card"></i></a></td>

                                                                              @elseif ($data['userOT']->first()->isApproved == '0')
                                                                                  <a data-toggle="modal" data-target="#myModal_OT_details{{$data['payday']}}" title="View Details" class="pull-right text-gray" style="font-size:1.2em;" href="#"><i class="fa fa-credit-card"></i></a></td>

                                                                              @else

                                                                                  <a data-toggle="modal" data-target="#myModal_OT_details{{$data['payday']}}" title="Pending Approval" class="pull-right text-orange" style="font-size:1.2em;" href="#"><i class="fa fa-credit-card"></i></a></td>

                                                                              @endif 
                                                                      
                                                                                      @if($data['shiftStart'] != null && $data['shiftEnd'] != null)
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
                                                                         

                                                                        @endif
                                                                   


                                                                 


                                                             
                                                                 
                                                                 <input type="hidden" name="OT_billable_{{$data['biometrics_id']}}" class="dtr_{{$data['biometrics_id']}}" value="{{$data['billableForOT']}}" />

                                                                
                                                            <!--else if data[hasOT]-->
                                                            @else
                                                            <td class="text-center">
                                                              @if($data['isFlexitime']) <strong class="text-green"><i class="fa fa-refresh"></i> Flexi Sched</strong><br/> @endif 

                                                                
                                                              @if($data['shiftStart'] != null && $data['shiftEnd'] != null)

                                                                {!! $data['billableForOT'] !!} {!! $data['OTattribute'] !!} 
                                                                
                                                              @endif

                                                            </td>

                                                             
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
                                                        
                                                          @if($data['hasVTO'])
                                                          <td class="text-center">{{$data['UT']}}</td>
                                                          <input type="hidden" name="UT_{{$data['biometrics_id']}}" class="dtr_{{$data['biometrics_id']}}" value="{{$data['UT']}}" />
                                                          @else
                                                          <td class="text-center">{{$data['UT']}}</td> <!--N/A</td>-->
                                                          <input type="hidden" name="UT_{{$data['biometrics_id']}}" class="dtr_{{$data['biometrics_id']}}" value="n/a" />

                                                          @endif
                                                        
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
                                                          @if($data['lockedNa'] == '0')

                                                            @if($data['hasCWS'])

                                                                @if ( $data['usercws'][0]['isApproved']!== 0 && $data['usercws'][0]['isApproved'] == null ) <!--pending pa cws -->
                                                                      @include('layouts.modals-OT_cannot', [
                                                                      
                                                                      'modelID' => '_OT'.$data["payday"], 
                                                                      'modelName'=>"Overtime ", 
                                                                      'modalTitle'=>'Submit',
                                                                      'formID'=>'submitOT',
                                                                      'icon'=>'glyphicon-up' ])
                                                                @else

                                                                     @if($data['shiftStart'] != null && $data['shiftEnd'] != null)
                                                                        @if( $data['backOffice'] && count($data['hdToday']) > 0  && strlen($data['logIN']) < 100)
                                                                          @include('layouts.modals-OT_HD', [
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

                                                                @endif
                                                            
                                                            @else

                                                              @if($data['shiftStart'] != null && $data['shiftEnd'] != null)

                                                                @if( $data['backOffice'] && count($data['hdToday']) > 0 && strlen($data['logIN']) < 100 )
                                                                 @include('layouts.modals-OT_HD', [
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


                                                            @endif

                                                          @endif <!--end if locked na-->

                                                            
                                                        @endif

                                                        


                                                        
                                                       
                                              
                                                      @endforeach
                                                  @endif

                                                  
                                                  </tbody>
                                                  
                                              </table>

                                              
                                            </div>
                                            <!-- /.col -->
                                           <!--  <div class="col-lg-1 col-sm-12"></div> -->

                                           
                                    </div>
                                    <p><small style="font-size: x-small;">{{$cp0}}-{{$cp1}}</small></p>

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


  // for bypassing RD
  // $('.actualRD').on('click',function(){
  //     alert('This is rd');
  // });
  
  //complicated clock
  $('.timepick').bootstrapMaterialDatePicker({format: 'hh:mm a',monthPicker: false, date: false, year:false, shortTime:true}); //wickedpicker(options);


  $('#thedtr').on('change','#otmodal select',function(){
      var workedOT = $(this).find(':selected');
      var proddate = workedOT.attr('data-proddate');
      var ottimeS = moment(proddate+' '+workedOT.attr('data-timestart'),'HH:MM A');
      var duration = moment.duration({'minutes' : workedOT.val()*60});
      var otend = moment(proddate+' '+workedOT.attr('data-timestart'),'HH:MM A').add(duration); //moment(workedOT.attr('data-timestart'),'HH:MM A').add(workedOT.val()*60,'m');
     
            //var twoweeks = moment(vl_from,"MM/D/YYYY").add(-14,'days');
            console.log('otS:');console.log(ottimeS.add(1,'hours'));
            console.log('otEnd:');console.log(otend.add(duration));
            console.log('date:');console.log(proddate);

       //alert('Filing OT for: '+workedOT.val()+'\n '+'to : '+ workedOT.attr('data-timeend'));
  });
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
                    $('select#full4x11').prop('required',true);
                    $('select#full4x11').prop('disabled',true);

                    $('input[name="shifttype"]').on('click',function(){
                      {
                      var shifttype = $(this).val();
                      if(shifttype == 'full'){
                        $('select#fulltimes').prop('disabled',false); $('select#parttimes').prop('disabled',true);$('select#full4x11').prop('disabled',true);
                         if ($('input[name="timeEnd"]').val() == "0" )
                          $.notify("Please fill out required field before submitting.",{className:"error", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );
                        else
                          $.notify("Please fill out required field before submitting.",{className:"error", globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );

                      }else if(shifttype == '4x11'){
                        $('select#full4x11').prop('disabled',false); $('select#parttimes').prop('disabled',true);$('select#fulltimes').prop('disabled',true);
                         if ($('input[name="timeEnd"]').val() == "0" )
                          $.notify("Please fill out required field before submitting.",{className:"error", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );
                        else
                          $.notify("Please fill out required field before submitting.",{className:"error", globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );

                      }
                      else{
                        $('select#parttimes').prop('disabled',false); $('select#fulltimes').prop('disabled',true);$('select#full4x11').prop('disabled',true);
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



  /* - PRESHIFT option -*/
  $('.preshift.pull-left.btn.btn-xs.btn-default').on('click', function(){
    var productionDate = $(this).attr('data-production_date');
    var biometrics_id = $(this).attr('data-biometrics_id');

    var reply = confirm("\n\nThis will enable use of pre-shift Time IN and \nfiling of pre-shift OT (if applicable) on: "+productionDate+".\n\n ");

    if (reply == true){
      
     
    

     var _token = "{{ csrf_token() }}";
     var payrollPeriod = [];
     
        payrollPeriod.push(productionDate);
    
     $.ajax({
                  url: "{{action('DTRController@usePreshift', $user->id)}}",
                  type:'POST',
                  data:{ 

                    'payrollPeriod': payrollPeriod,
                    '_token':_token

                  },

                 
                  success: function(res)
                  {
                    console.log(res);

                    $.notify(res.message,{className:"success", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );
                    $('#unlock_'+biometrics_id).fadeOut();
                    //$.notify(res.,{className:"error", globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );

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
      $.notify("Pre-shift logs and OT disabled for this date",{className:"error", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );
    }

  });

  //disable preshift
  $('.preshiftD.pull-left.btn.btn-xs.btn-warning').on('click', function(){
    var productionDate = $(this).attr('data-production_date');
    var biometrics_id = $(this).attr('data-biometrics_id');

    var reply = confirm("\n\nThis will disable use of pre-shift Time IN and \nfiling of pre-shift OT (if applicable) on: "+productionDate+".\n\n ");

    if (reply == true){
      
     
    

     var _token = "{{ csrf_token() }}";
     var payrollPeriod = [];
     
        payrollPeriod.push(productionDate);
    
     $.ajax({
                  url: "{{action('DTRController@disablePreshift', $user->id)}}",
                  type:'POST',
                  data:{ 

                    'payrollPeriod': payrollPeriod,
                    '_token':_token

                  },

                 
                  success: function(res)
                  {
                    console.log(res);

                    $.notify(res.message,{className:"success", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );
                    $('#unlock_'+biometrics_id).fadeOut();
                    //$.notify(res.,{className:"error", globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );

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
      $.notify("Pre-shift logs and OT still enabled for this date",{className:"error", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );
    }

  });

 /* ---- DTR LOCKING ------ */
  $('.cannot').on('click', function(){

    alert("Sorry, can't lock your DTR due to pending requests. \nKindly coordinate with your approver before proceeding with DTR locking.");

  });

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


  $('#unlockByTL').on('click',function(){

    var _token = "{{ csrf_token() }}";
    var payrollPeriod = [];

    @foreach($payrollPeriod as $p)
        var el = "{{$p}}";
        payrollPeriod.push(el);
    @endforeach
     $.ajax({
                  url: "{{action('DTRController@unlock', $user->id)}}",
                  type:'POST',
                  data:{ 

                    'payrollPeriod': payrollPeriod,
                    'unlockByTL':true,
                    '_token':_token

                  },

                 
                  success: function(res)
                  {
                    console.log(res);
                    $('#unlockByTL').delay(1000).animate({ height: 'toggle', opacity: 'toggle' }, 'slow');


                    $.notify(res.message,{className:"success", globalPosition:'top right',autoHideDelay:3000, clickToHide:true} );

                    setTimeout(function(){
                      //location.reload(true);
                      var fromDate = "{{$paystart}}"; //$(this).find('option:selected').val();
                      var toDate = "{{$payend}}";
                      window.location.href= "{{url('/')}}/user_dtr/{{$user->id}}?from="+fromDate+"&to="+toDate;
                    },3000);
                    
                    //;
                    //window.location = "{{action('HomeController@index')}}";
                     
                  }, error: function(res){
                    console.log("ERROR");
                    console.log(res);
                    $.notify(res.message,{className:"error", globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );
                  }


        });
    
    


  });



 /* ---- DTR LOCKING ------ */


 // --------- ECQ SETTING ------------.btn-default
 $('.setECQ.pull-left.btn.btn-xs').on('click',function(){
    var pdate = $(this).attr('data-placehold');
   
    var bioID = $(this).attr('data-bioID');
    var pnameS = "ecqStart_"+bioID;
    var pnameE = "ecqEnd_"+bioID;
    $('input[name="'+pnameS+'"]').attr('placeholder',pdate);
    $('input[name="'+pnameE+'"]').attr('placeholder',pdate); 
    $('input[name="'+pnameS+'"]').val(pdate);
    $('input[name="'+pnameE+'"]').val(pdate); 

    var s = $('input[name="'+pnameS+'"]'); //.attr('placeholder',pdate);
    console.log(s);

 });


 $('.updateECQ.btn.btn-success.btn-md.pull-right').on('click',function(){

    var bioID = $(this).attr('data-bioID');
    var item = $(this);
    var pstart = $('#noECQ_'+bioID).find('input[name="ecqStart_'+bioID+'"]').val();
    var pend = $('#noECQ_'+bioID).find('input[name="ecqEnd_'+bioID+'"]').val();
    var ecq = $('#noECQ_'+bioID).find('input[name="ecqstat_'+bioID+'"]:checked').val(); //.find(':selected').val();

    if(ecq)
    {
      item.fadeOut();
      console.log('start: '+pstart+ ' end: '+pend);
      var _token = "{{ csrf_token() }}";
      $.ajax({
                  url: "{{action('DTRController@updateECQ')}}",
                  type:'POST',
                  data:{ 
                    'pstart' : pstart,
                    'pend': pend,
                    'ecq' : ecq,
                    'user_id': "{{$id}}",
                    '_token':_token
                  },

                 
                  success: function(res)
                  {
                    console.log(res);
                    if(res.success=='1')
                      $.notify("GCQ work status updated successfully.",{className:"success", globalPosition:'left',autoHideDelay:7000, clickToHide:true} );
                    else {
                      $.notify(res.message,{className:"error", globalPosition:'left',autoHideDelay:7000, clickToHide:true} );
                      item.fadeIn();
                    }

                    location.reload(true);
                    //window.location = "{{action('HomeController@index')}}";
                     
                  }, error: function(res){
                    console.log("ERROR");
                    $.notify("Something went wrong. Please try again.",{className:"error", globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );
                  }


        });
      
    } else alert('Please specify Community Quarantine work status.');
   

 });
 // --------- END ECQ SETTING --------



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