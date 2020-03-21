@extends('layouts.main')

@section('metatags')
<title>Leave Credits | OAMPI Employee Management System</title>

<style type="text/css">
/* Sortable items */

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

@section('content')


  <!-- Content Header (Page header) -->
    <section class="content-header">
      
      <ol class="breadcrumb">
        <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="{{action('UserController@index')}}"> Employees</a></li>
        <li class="active">Leave Credits</li>
      </ol>
    </section>

     <section class="content">
      

          <div class="row" style="margin-top:50px">
           
            

            <div class="col-lg-12"> <!-- ******************* LEFT COLUMN ***************** -->

              <div class="box box-default" style="background: rgba(256, 256, 256, 0.5)">

              <!-- TABLE: LEFT -->
                <div class="box-header">

                 
                 <!--  <h2 class="text-center"> <img class="text-center" src="{{asset('public/img/logo-transparent.png')}}" width="90" /></h2>
                  <h3 class="text-center"> Update Employee Data <br/></h3> -->
                  

                </div>
                <div class="box-body" style="background: rgba(256, 256, 256, 0.5)">
                  


                  <div class="nav-tabs-custom">

                    <!-- ********** UPPER TABS ***************  -->
                    <ul class="nav nav-tabs pull-right">

                      
                      <li class="active" ><a href="#leaveCredits_pane" data-toggle="tab"><i class="fa fa-bar-chart"></i>&nbsp;&nbsp; Leave Credits</a></li>
                      

                      
                     
                      
                      
                      @if( is_null($personnel->nickname) )
                      <li class="pull-left header"><i class="fa fa-2x fa-address-card"></i>&nbsp;&nbsp; {{$personnel->firstname}} {{$personnel->lastname}} </li>

                      @else
                      <li class="pull-left header"><i class="fa fa-2x fa-address-card"></i>&nbsp;&nbsp; {{$personnel->nickname}} {{$personnel->lastname}} </li>
                      @endif
                    </ul> 

                    
                    <div class="tab-content no-padding">
                      <!-- ********* LEAVE CREDITS PANE ************ -->

                      <div class="chart tab-pane active id="leaveCredits_pane" style="position: relative; height: auto;">
                        <p style="padding: 20px; font-size: smaller"><i class="fa fa-info-circle"></i> Note: Regular employees who have  completed six  (6)  months  shall be  entitled  to  five   (5)  days  of  sick  leave and five (5) days of vacation leave. Employee  will  earn  an  additional  0.42  leave credits after the 5th and 20th day of the month worked. Probationary, contractual employees may be  allowed to  take  sick/vacation  leave days  but will  not be  considered  as  paid  leave. Even if  the employee  has no  leave credit  yet,  filing of  LWOP (leave without pay) is  mandatory as  basis of  excused absence/s. </p>

                        <div style="padding:10px" class="bg-primary text-center">
                          <p><i class="fa fa-2x fa-plane"></i>  All 2020 VL beginning balances are from your earned credits from the previous years, minus all used credits up until <strong>Feb 05, 2020</strong>. <br/>Remaining leave credits are then derived by deducting all filed leave submissions via EMS starting Feb.06 onwards, and then adding regular leave credit earnings every end of cutoff period (0.42 for full time employees, 0.21 for part time employees).</p>
                        </div>
                        <br/>

                        <div style="padding:10px" class="bg-danger text-center">
                          <p><i class="fa fa-2x  fa-stethoscope"></i>  All 2020 SL beginning balances are from your earned credits from the previous years, minus all used credits up until<strong class="text-danger"> Mar 05, 2020.</strong> <br/>Remaining leave credits are then derived by deducting all filed leave submissions via EMS starting Mar.06 onwards, and then adding regular leave credit earnings every end of cutoff period (0.42 for full time employees, 0.21 for part time employees).</p>
                        </div>
                        <table class="table" style="margin-top: 40px">
                          <tr>
                              <td colspan="3" style="background-color: #e6e6e6;">
                                <h4 class="text-primary pull-left"><i class="fa fa-bar-chart"></i>&nbsp; Leave Credits</h4>
                              </td>
                            </tr>

                            
                            <!-- ************ VACATION LEAVE *************-->
                            <tr>
                              <td><h4><i class="fa fa-plane"></i> Vacation Leave</h4> <br/> <a class="btn btn-default btn-xs" href="{{action('UserVLController@create')}}"><i class="fa fa-upload"></i> File New VL</a> </td>
                              <td colspan="2">
                              <table id="vl" class="table">
                                <thead>
                                  <th class="text-center">Year</th>
                                  <th class="text-center">Beginning Balance</th>
                                  <th class="text-center">Used</th>
                                 
                                  <th class="text-center">Earnings</th>
                                  <th class="text-center">Total Remaining</th>
                                  <th class="text-center">Actions</th>
                                </thead>
                                <tbody>

                                  @if (count($personnel->vlCredits) <= 0)
                                  <tr>
                                    <td colspan="6"><h4 class="text-center text-gray"><br/>No data available.</h4></td>
                                  </tr>

                                  @else
                                      <?php $ctr=1;?>
                                      @foreach($personnel->vlCredits->sortByDesc('creditYear') as $v)
                                      <tr>
                                        <td class="text-center">{{$v->creditYear}} </td>
                                        <td class="text-center">{{$v->beginBalance}} </td>
                                        <td class="text-center">

                                          @if ($v->used == '0.00') ( {{$v->used}} )&nbsp;&nbsp;&nbsp; @else
                                          <!-- ******** collapsible box ********** -->
                                          <div class="box collapsed-box" style="margin-top: 0px">
                                            <div class="box-header">
                                             ( {{$v->used}} )
                                             &nbsp;&nbsp;&nbsp;
                                              <div class="box-tools pull-right">
                                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                                                </button>
                                              </div>
                                              <!-- /.box-tools -->
                                            </div>
                                            <!-- /.box-header -->
                                            <div class="box-body">
                                              <p style="font-size: x-small;">All filed VLs in EMS: </p>
                                              
                                                @foreach($allVLs as $vl)
                                                  <?php if(strpos($vl->leaveStart, (string)$v->creditYear) !== false) { ?>
                                                    <a class="btn btn-xs btn-default" data-toggle="modal" data-target="#myModal_VL{{$vl->id}}"><i class="fa fa-plane"></i> VL ({{$vl->totalCredits}} ) : {{date('M d',strtotime($vl->leaveStart))}}</a><br/>
                                                  <?php } ?>


                                                  <div class="modal fade" id="myModal_VL{{$vl->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                          <div class="modal-content">
                                                                  <div class="modal-header">
                                                                          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span>
                                                                          <span class="sr-only">Close</span></button>
                                                                          <h4 class="modal-title text-black" id="myModalLabel"><i class="fa fa-plane"></i> Vacation Leave</h4>
                                                                  </div>
                                                                  <div class="modal-body-upload" style="padding:20px;">

                                                                          <!-- DIRECT CHAT PRIMARY -->
                                                                          <div class="box box-primary direct-chat direct-chat-primary">
                                                                            <div class="box-body">
                                                                                <!-- Conversations are loaded here -->
                                                                                  <div class="direct-chat-messages">
                                                                                  <!-- Message. Default to the left -->
                                                                                  <div class="direct-chat-msg">
                                                                                          <div class="direct-chat-info clearfix">
                                                                                                  <span class="direct-chat-name pull-left">{{$personnel->firstname}}</span>
                                                                                                  <span class="direct-chat-timestamp pull-right">{{$vl->created_at}} </span>
                                                                                          </div>
                                                                                          <!-- /.direct-chat-info -->
                                                                                          <a href="{{action('UserController@show',$personnel->id)}}" target="_blank">
                                                                                          <img src="../../public/img/employees/{{$personnel->id}}.jpg" class="img-circle pull-left" alt="User Image" width="70" /></a>
                                                                                          <div class="direct-chat-text" style="width:85%; left:30px; background-color:#fcfdfd">
                                                                                                  <p class="text-left"><br/>
                                                                                                    I would like to file a <strong class="text-danger">{{number_format($vl->totalCredits,0)}}-day </strong><strong>VACATION LEAVE</strong> <br/><br/>
                                                                                                    <strong>VL credits used: </strong>
                                                                                                    <span class="text-danger">{{$vl->totalCredits}}</span><br/>
                                                                                                    <strong> &nbsp;&nbsp;Reason: </strong><em>{{$vl->notes}} </em></p>
                                                                                                          <div class="row">
                                                                                                                  <div class="col-sm-12"> 
                                                                                                                          <div class="row">
                                                                                                                                  <div class="col-sm-6"><h5 class="text-primary">From: </h5></div>
                                                                                                                                  <div class="col-sm-6"><h5 class="text-primary">Until: </h5></div>
                                                                                                                                  <div class="col-sm-6" style="font-size: 12px">
                                                                                                                                    @if ($vl->halfdayFrom == '1' && $vl->halfdayTo == '1')
                                                                                                                                    <p><strong>{{$vl->leaveStart}} (Whole day) </strong></p>
                                                                                                                                    @else
                                                                                                                                    <p><strong>{{$vl->leaveStart}} </strong></p>
                                                                                                                                    @endif
                                                                                                                                  </div>

                                                                                                                                  <div class="col-sm-6" style="font-size: 12px">
                                                                                                                                          <p><strong>{{$vl->leaveEnd}}</strong></p></div>

                                                                                                                                  <div class="col-sm-3"> </div>
                                                                                                                          </div>
                                                                                                                  </div>
                                                                                                          </div>
                                                                                          </div>
                                                                                  </div>
                                                                                  <!-- /.direct-chat-text -->
                                                                                  </div>
                                                                                                      
                                                                                  <!-- /.direct-chat-msg -->
                                                                                  <!-- Message to the right -->
                                                                                  <div class="direct-chat-msg right" style="margin-top:50px">
                                                                                    <div class="direct-chat-default clearfix">
                                                                                                    
                                                                                                    <span class="direct-chat-timestamp pull-left">
                                                                                                      {{$vl->updated_at}} </span>
                                                                                          </div>
                                                                                          <img class="direct-chat-img" src="../../public/img/oam_favicon1-55027f4ev1_site_icon-256x256.png" alt="Message User Image">
                                                                                    
                                                                                    
                                                                                          <div class="direct-chat-text bg-green" >
                                                                                                  <h5><i class="fa fa-thumbs-up"></i> Approved</h5>
                                                                                          </div>

                                                                                  </div>
                                                                                         <!-- /.direct-chat-text -->
                                                                            </div>
                                                                                  <!-- /.direct-chat-msg -->
                                                                          </div>
                                                                          <!--/.direct-chat-messages-->

                                                                  </div>
                                                          <!--/.direct-chat-messages-->
                                                          </div>
                                                   <!-- /.box-body -->
                                                   </div>
                                                  <!--/.direct-chat -->
                                                  </div>
                                                  <!--end DTRP modal-->
                                                  
                                                
                                                @endforeach
                                              
                                              
                                            </div>
                                            <!-- /.box-body -->
                                          </div>
                                         <!-- ******** end collapsible box ********** -->@endif


                                        </td>
                                        
                                        <td  class="text-center">
                                          
                                          <?php $earn=0; $deets=""; 
                                                foreach ($allEarnings as $e){ 
                                                  if(strpos($e->period, (string)$v->creditYear) !== false){ 
                                                          $deets .= date('M d',strtotime($e->period)).' : ' . $e->credits.'<br/>';
                                                          $earn += $e->credits; }
                                                } ?>
                                          @if ($earn == 0) {{$earn}} @else

                                          <!-- ******** collapsible box ********** -->
                                          <div class="box collapsed-box" style="margin-top: 0px">
                                            <div class="box-header">
                                              {{$earn}} &nbsp;&nbsp;&nbsp;
                                              <div class="box-tools pull-right">
                                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                                                </button>
                                              </div>
                                              <!-- /.box-tools -->
                                            </div>
                                            <!-- /.box-header -->
                                            <div class="box-body">
                                              <p style="font-size: x-small;" class="text-right"> {!! $deets !!}</p>
                                              
                                            </div>
                                            <!-- /.box-body -->
                                          </div>
                                         <!-- ******** end collapsible box ********** -->
                                         @endif


                                 
                                          
                                        </td>
                                        <td class="text-center @if($ctr==1) text-success" style="font-size: larger; font-weight: bold; @endif">{{ number_format( (($v->beginBalance - $v->used)-$v->paid)+ $earn, 2) }}</td>
                                        <td class="text-center">
                                         
                                         @if($canUpdateLeaves)

                                          <a class="editLeave btn btn-xs btn-default" data-leavetype="vl" data-leaveid="{{$v->id}}" data-toggle="modal"  data-target="#myModal_edit_vl{{$v->id}}" style="margin-right: 5px"><i class="fa fa-pencil"></i> Edit </a> 
                                          <a data-toggle="modal"  data-target="#myModal_vl{{$v->id}}"  class="btn btn-xs btn-default" style="margin-right: 5px"><i class="fa fa-trash"></i> Delete</a>

                                          @endif

                                        </td>
                                      </tr>


                                      @if ($canUpdateLeaves)

                                      @include('layouts.modals-del', [
                                        'modelRoute'=>'user_vl.deleteCredit',
                                        'modelID' => $v->id, 
                                        'modelType' =>'_vl',
                                        'modalMessage'=> "Are you sure you want to delete this " .$v->creditYear. " VL credit? ",
                                        'modelName'=>"VL Credit ", 
                                        'modalTitle'=>'Delete', 
                                        'formID'=>'delVL',
                                        'icon'=>'glyphicon-trash' ])

                                        @include('layouts.modals-editLeave', [
                                        'modelRoute'=>'user_vl.editCredits',
                                        'modelID' => $v->id, 
                                        'modalIcon' => 'fa-plane',
                                        'modelType' =>'_vl',
                                        'modalMessage'=> " ",
                                        'modelName'=>"VL Credit ", 
                                        'modalTitle'=>'Edit', 
                                        'formID'=>'editVL',
                                        'icon'=>'glyphicon-up' ])

                                        @endif



                                      <?php $ctr++; ?>
                                      @endforeach
                                  @endif
                                  <tr><td colspan="6">

                                    @if (count($personnel->vlCredits) <= (date('Y')-2008) && $canUpdateLeaves )
                                    <a data-toggle="modal" style="margin-top: 10px" data-target="#myModal_addVL{{$personnel->id}}" href="#" class="btn btn-xs btn-primary pull-right"><i class="fa fa-plus-circle"></i> Add VL Credits</a>
                                    <!-- <a data-toggle="modal" style="margin-top: 10px" data-target="#underConstruction" href="#" class="btn btn-xs btn-primary pull-right"><i class="fa fa-plus-circle"></i> Add VL Credits</a> -->

                                  </td>
                                    @endif</tr>

                                  
                                </tbody>
                              </table>

                              </td>
                            </tr>

                            <!-- ************ SICK LEAVE *************-->
                            
                            <tr>
                              <td><h4><i class="fa fa-plane"></i> Sick Leave</h4> <br/> <a class="btn btn-default btn-xs" href="{{action('UserSLController@create')}}"><i class="fa fa-upload"></i> File New SL</a> </td>
                              <td colspan="2">
                              <table id="vl" class="table">
                                <thead>
                                  <th class="text-center">Year</th>
                                  <th class="text-center">Beginning Balance</th>
                                  <th class="text-center">Used</th>
                                 
                                  <th class="text-center">Earnings</th>
                                  <th class="text-center">Total Remaining</th>
                                  <th class="text-center">Actions</th>
                                </thead>
                                <tbody>

                                  @if (count($personnel->slCredits) <= 0)
                                  <tr>
                                    <td colspan="6"><h4 class="text-center text-gray"><br/>No data available.</h4></td>
                                  </tr>

                                  @else
                                      <?php $ctr=1;?>
                                      @foreach($personnel->slCredits->sortByDesc('creditYear') as $v)
                                      <tr>
                                        <td class="text-center">{{$v->creditYear}} </td>
                                        <td class="text-center">{{$v->beginBalance}} </td>
                                        <td class="text-center">

                                          @if ($v->used == '0.00') ( {{$v->used}} )&nbsp;&nbsp;&nbsp; @else
                                          <!-- ******** collapsible box ********** -->
                                          <div class="box collapsed-box" style="margin-top: 0px">
                                            <div class="box-header">
                                             ( {{$v->used}} )
                                             &nbsp;&nbsp;&nbsp;
                                              <div class="box-tools pull-right">
                                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                                                </button>
                                              </div>
                                              <!-- /.box-tools -->
                                            </div>
                                            <!-- /.box-header -->
                                            <div class="box-body">
                                              <p style="font-size: x-small;">All filed SLs in EMS: </p>
                                              
                                                @foreach($allSLs as $vl)
                                                  <?php if(strpos($vl->leaveStart, (string)$v->creditYear) !== false) { ?>
                                                    <a class="btn btn-xs btn-default" data-toggle="modal" data-target="#myModal_SL{{$vl->id}}"><i class="fa fa-stethoscope"></i> SL ({{$vl->totalCredits}} ) : {{date('M d',strtotime($vl->leaveStart))}}</a><br/>
                                                  <?php } ?>


                                                  <div class="modal fade" id="myModal_SL{{$vl->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                          <div class="modal-content">
                                                                  <div class="modal-header">
                                                                          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span>
                                                                          <span class="sr-only">Close</span></button>
                                                                          <h4 class="modal-title text-black" id="myModalLabel"><i class="fa fa-stethoscope"></i> Sick Leave</h4>
                                                                  </div>
                                                                  <div class="modal-body-upload" style="padding:20px;">

                                                                          <!-- DIRECT CHAT PRIMARY -->
                                                                          <div class="box box-primary direct-chat direct-chat-primary">
                                                                            <div class="box-body">
                                                                                <!-- Conversations are loaded here -->
                                                                                  <div class="direct-chat-messages">
                                                                                  <!-- Message. Default to the left -->
                                                                                  <div class="direct-chat-msg">
                                                                                          <div class="direct-chat-info clearfix">
                                                                                                  <span class="direct-chat-name pull-left">{{$personnel->firstname}}</span>
                                                                                                  <span class="direct-chat-timestamp pull-right">{{$vl->created_at}} </span>
                                                                                          </div>
                                                                                          <!-- /.direct-chat-info -->
                                                                                          <a href="{{action('UserController@show',$personnel->id)}}" target="_blank">
                                                                                          <img src="../../public/img/employees/{{$personnel->id}}.jpg" class="img-circle pull-left" alt="User Image" width="70" /></a>
                                                                                          <div class="direct-chat-text" style="width:85%; left:30px; background-color:#fcfdfd">
                                                                                                  <p class="text-left"><br/>
                                                                                                    I would like to file a <strong class="text-danger">{{number_format($vl->totalCredits,0)}}-day </strong><strong>SICK LEAVE</strong> <br/><br/>
                                                                                                    <strong>SL credits used: </strong>
                                                                                                    <span class="text-danger">{{$vl->totalCredits}}</span><br/>
                                                                                                    <strong> &nbsp;&nbsp;Reason: </strong><em>{{$vl->notes}} </em></p>
                                                                                                          <div class="row">
                                                                                                                  <div class="col-sm-12"> 
                                                                                                                          <div class="row">
                                                                                                                                  <div class="col-sm-6"><h5 class="text-primary">From: </h5></div>
                                                                                                                                  <div class="col-sm-6"><h5 class="text-primary">Until: </h5></div>
                                                                                                                                  <div class="col-sm-6" style="font-size: 12px">
                                                                                                                                    @if ($vl->halfdayFrom == '1' && $vl->halfdayTo == '1')
                                                                                                                                    <p><strong>{{$vl->leaveStart}} (Whole day) </strong></p>
                                                                                                                                    @else
                                                                                                                                    <p><strong>{{$vl->leaveStart}} </strong></p>
                                                                                                                                    @endif
                                                                                                                                  </div>

                                                                                                                                  <div class="col-sm-6" style="font-size: 12px">
                                                                                                                                          <p><strong>{{$vl->leaveEnd}}</strong></p></div>

                                                                                                                                  <div class="col-sm-3"> </div>
                                                                                                                          </div>
                                                                                                                  </div>
                                                                                                          </div>
                                                                                          </div>
                                                                                  </div>
                                                                                  <!-- /.direct-chat-text -->
                                                                                  </div>
                                                                                                      
                                                                                  <!-- /.direct-chat-msg -->
                                                                                  <!-- Message to the right -->
                                                                                  <div class="direct-chat-msg right" style="margin-top:50px">
                                                                                    <div class="direct-chat-default clearfix">
                                                                                                    
                                                                                                    <span class="direct-chat-timestamp pull-left">
                                                                                                      {{$vl->updated_at}} </span>
                                                                                          </div>
                                                                                          <img class="direct-chat-img" src="../../public/img/oam_favicon1-55027f4ev1_site_icon-256x256.png" alt="Message User Image">
                                                                                    
                                                                                    
                                                                                          <div class="direct-chat-text bg-green" >
                                                                                                  <h5><i class="fa fa-thumbs-up"></i> Approved</h5>
                                                                                          </div>

                                                                                  </div>
                                                                                         <!-- /.direct-chat-text -->
                                                                            </div>
                                                                                  <!-- /.direct-chat-msg -->
                                                                          </div>
                                                                          <!--/.direct-chat-messages-->

                                                                  </div>
                                                          <!--/.direct-chat-messages-->
                                                          </div>
                                                   <!-- /.box-body -->
                                                   </div>
                                                  <!--/.direct-chat -->
                                                  </div>
                                                  <!--end DTRP modal-->
                                                  
                                                
                                                @endforeach
                                              
                                              
                                            </div>
                                            <!-- /.box-body -->
                                          </div>
                                         <!-- ******** end collapsible box ********** -->@endif


                                        </td>
                                        
                                        <td  class="text-center">
                                          
                                          <?php $earnSL=0; $deets=""; 
                                                foreach ($allEarnings_SL as $e){ 
                                                  if(strpos($e->period, (string)$v->creditYear) !== false){ 
                                                          $deets .= date('M d',strtotime($e->period)).' : ' . $e->credits.'<br/>';
                                                          $earnSL += $e->credits; }
                                                } ?>
                                          @if ($earnSL == 0) {{$earnSL}} @else

                                          <!-- ******** collapsible box ********** -->
                                          <div class="box collapsed-box" style="margin-top: 0px">
                                            <div class="box-header">
                                              {{$earnSL}} &nbsp;&nbsp;&nbsp;
                                              <div class="box-tools pull-right">
                                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                                                </button>
                                              </div>
                                              <!-- /.box-tools -->
                                            </div>
                                            <!-- /.box-header -->
                                            <div class="box-body">
                                              <p style="font-size: x-small;" class="text-right"> {!! $deets !!}</p>
                                              
                                            </div>
                                            <!-- /.box-body -->
                                          </div>
                                         <!-- ******** end collapsible box ********** -->
                                         @endif


                                 
                                          
                                        </td>
                                        <td class="text-center @if($ctr==1) text-success" style="font-size: larger; font-weight: bold; @endif">{{ number_format( (($v->beginBalance - $v->used)-$v->paid)+ $earnSL, 2) }}</td>
                                        <td class="text-center">
                                         
                                        

                                          @if($canUpdateLeaves)
                                          <a class="editLeave btn btn-xs btn-default" data-leavetype="vl" data-leaveid="{{$v->id}}" data-toggle="modal"  data-target="#myModal_edit_sl{{$v->id}}" style="margin-right: 5px"><i class="fa fa-pencil"></i> Edit </a> 
                                          <a data-toggle="modal"  data-target="#myModal_sl{{$v->id}}"  class="btn btn-xs btn-default" style="margin-right: 5px"><i class="fa fa-trash"></i> Delete</a>
                                          @endif

                                         

                                        </td>
                                      </tr>


                                    


                                        @if($canUpdateLeaves)
                                      
                                          @include('layouts.modals-del', [
                                            'modelRoute'=>'user_sl.deleteCredit',
                                            'modelID' => $v->id, 
                                            'modelType' =>'_sl',
                                            'modalMessage'=> "Are you sure you want to delete this " .$v->creditYear. " SL credit? ",
                                            'modelName'=>"SL Credit ", 
                                            'modalTitle'=>'Delete', 
                                            'formID'=>'delSL',
                                            'icon'=>'glyphicon-trash' ])

                                            @include('layouts.modals-editLeave', [
                                            'modelRoute'=>'user_sl.editCredits',
                                            'modelID' => $v->id, 
                                            'modalIcon' =>'fa-stethoscope',
                                            'modelType' =>'_sl',
                                            'modalMessage'=> " ",
                                            'modelName'=>"SL Credit ", 
                                            'modalTitle'=>'Edit', 
                                            'formID'=>'editSL',
                                            'icon'=>'glyphicon-up' ])

                                          @endif




                                      <?php $ctr++; ?>
                                      @endforeach
                                  @endif
                                  <tr><td colspan="6">

                                    @if (count($personnel->slCredits) <= (date('Y')-2008) && $canUpdateLeaves )
                                    <a data-toggle="modal" style="margin-top: 10px" data-target="#myModal_addSL{{$personnel->id}}" href="#" class="btn btn-xs btn-primary pull-right"><i class="fa fa-plus-circle"></i> Add SL Credits</a>
                                    <!-- <a data-toggle="modal" style="margin-top: 10px" data-target="#underConstruction" href="#" class="btn btn-xs btn-primary pull-right"><i class="fa fa-plus-circle"></i> Add VL Credits</a> -->

                                  </td>
                                    @endif</tr>

                                  
                                </tbody>
                              </table>

                              </td>
                            </tr>



                            
                        </table>

                      </div><!-- end LEAVE CREDITS pane -->


                    </div>
                  </div>
                  <!-- /.nav-tabs-custom -->

                  
                </div>
                <!-- /.box-body -->

                <div class="box-footer clearfix" style="background:none">
                 
                  
                </div>
                <!-- /.box-footer -->
              </div><!-- /.box -->
            </div><!--end left -->

            @include('layouts.modals-addVL', [
                    'modelRoute'=>'user_vl.addCredits',
                    'modelID' => $personnel->id, 
                    'modalMessage'=> " ",
                    'modelName'=>"VL Credit ", 
                    'modalTitle'=>'Add New', 
                    'formID'=>'submitVL',
                    'icon'=>'glyphicon-up' ])

            @include('layouts.modals-addSL', [
                    'modelRoute'=>'user_sl.addCredits',
                    'modelID' => $personnel->id, 
                    'modalMessage'=> " ",
                    'modelName'=>"SL Credit ", 
                    'modalTitle'=>'Add New', 
                    'formID'=>'submitSL',
                    'icon'=>'glyphicon-up' ])


<div class="modal fade" id="underConstruction" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
          <h4 class="modal-title" id="myModalLabel"><i class="fa fa-exclamation-triangle"></i> Module Under Construction</h4>
        
      </div>
      <div class="modal-body">
        We're still working on this one...
      </div>
      <div class="modal-footer no-border">
        
         
        
        <button type="button" class="btn btn-default" data-dismiss="modal">Okay</button>
      </div>
    </div>
  </div>
</div>
           

           


            
           

          
          </div><!-- end row -->

       
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
   $( ".datepicker" ).datepicker();

       

});





   




   


   

 
</script>
<!-- end Page script -->


@stop