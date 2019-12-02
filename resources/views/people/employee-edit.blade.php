@extends('layouts.main')

@section('metatags')
<title>Update Employee | EMS </title>




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



.cropit-preview {
  /* You can specify preview size in CSS */
  width: 120px;
  height: 120px;
}
/* Show load indicator when image is being loaded */
.cropit-preview.cropit-image-loading .spinner {
  opacity: 1;
}

/* Show move cursor when image has been loaded */
.cropit-preview.cropit-image-loaded .cropit-preview-image-container {
  cursor: move;
}

/* Gray out zoom slider when the image cannot be zoomed */
.cropit-image-zoom-input[disabled] {
  opacity: .2;
}



/* The following styles are only relevant to when background image is enabled */

/* Translucent background image */
.cropit-preview-background {
  opacity: .2;
}

/*
 * If the slider or anything else is covered by the background image,
 * use non-static position on it
 */
input.cropit-image-zoom-input {
  position: relative;
}

/* Limit the background image by adding overflow: hidden */
#image-cropper {
  overflow: hidden;
}

</style>
@endsection

@section('content')


  <!-- Content Header (Page header) -->
    <section class="content-header">
      
      <ol class="breadcrumb">
        <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="{{action('UserController@index')}}"> All Employees</a></li>
        <li class="active">Update Employee</li>
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

                      @if ($canUpdateLeaves)
                      <li @if($page == '3') class="active"  @endif ><a href="#leaveCredits_pane" data-toggle="tab"><i class="fa fa-bar-chart"></i>&nbsp;&nbsp; Leave Credits</a></li>
                      @endif

                      @if ($canEditEmployees)
                      <li @if($page == '4') class="active"  @endif ><a href="#access_pane" data-toggle="tab" ><i class="fa fa-key"></i>&nbsp;&nbsp; Grant Access</a></li>

                      <li @if($page == '2') class="active"  @endif ><a href="#approvers_pane" data-toggle="tab" ><i class="fa fa-check-square-o"></i>&nbsp;&nbsp; Approvers</a></li>
                      
                      
                      <li @if($page == '1' || is_null($page) ) class="active"  @endif><a href="#profile" data-toggle="tab"><i class="fa fa-address-card-o"></i>&nbsp;&nbsp;  Employee Data</a></li>
                      @endif
                      
                      @if( is_null($personnel->nickname) )
                      <li class="pull-left header"><i class="fa fa-2x fa-address-card"></i>&nbsp;&nbsp; {{$personnel->firstname}} {{$personnel->lastname}} </li>

                      @else
                      <li class="pull-left header"><i class="fa fa-2x fa-address-card"></i>&nbsp;&nbsp; {{$personnel->nickname}} {{$personnel->lastname}} </li>
                      @endif
                    </ul> 

                    
                    <div class="tab-content no-padding">
                      
                      <!-- profile data -->
                      <div class="chart tab-pane @if(is_null($page) || $page == '1') active @endif" id="profile" style="position: relative; height:auto; background: rgba(256, 256, 256, 0.5)">
                        {{ Form::open(['route' => ['user.update', $personnel->id], 'method'=>'put','class'=>'col-lg-12', 'id'=> 'addEmployee' ]) }}
                            


                            <table class="table" style="width:85%; margin: 40px auto; background: rgba(256, 256, 256, 0.5)">
                                <tr>
                                  <td>
                                      <label>Employee Name: </label>
                                      <input type="text" name="firstname" id="firstname" value="{{$personnel->firstname}}" class="form-control required" />
                                      <div id="alert-firstname" style="margin-top:10px"></div>

                                      <input type="text" name="middlename" id="middlename"  value="{{$personnel->middlename}}" class="form-control required" />
                                      <div id="alert-middlename" style="margin-top:10px"></div>

                                      <input type="text" name="lastname" id="lastname" value="{{$personnel->lastname}}" class="form-control required" />
                                      <div id="alert-lastname" style="margin-top:10px"></div>

                                      <label>Nick Name: </label>
                                      <input tabindex="6" type="text" name="nickname" id="nickname" @if (empty($personnel->nickname) ) placeholder="Nickname or alias" @else value="{{$personnel->nickname}}" placeholder="{{$personnel->nickname}}" @endif  class="form-control " />
                                      <div id="alert-nickname" style="margin-top:10px"></div>

                                      <div class="clearfix" style="margin-top:20px"></div>
                                      <label> <input tabindex="8" type="radio" name="gender" required value="M" @if($personnel->gender=='M')checked="checked" @endif /> Male </label>&nbsp;&nbsp;&nbsp;&nbsp;
                                      <label> <input tabindex="9" type="radio" name="gender" required value="F" @if($personnel->gender=='F')checked="checked" @endif /> Female </label>

                                      <br/><br/><br/>

                                      <label>Position: </label>
                                      <select class="form-control" name="position_id" id="position_id">
                                        <option value="0">- Select job title - </option>

                                        @foreach ($positions as $pos)
                                        <option value="{{$pos->id}}" @if ($pos->id == $personnel->position_id) selected="selected" @endif>{{$pos->name}} </option>

                                        @endforeach
                                        <option value="-1">** <em>add new position</em> ** </option>
                                      </select>

                                      <div id="newpos"></div>

                                      <br/><br/>
                                      

                                      <label>Department / Program: </label>
                                      <select class="form-control" name="campaign_id" id="campaign_id">
                                        <option value="0">- Select one - </option>

                                        @foreach ($campaigns as $campaign)
                                        <option value="{{$campaign->id}}" @if ($campaign->id == $personnel->campaign[0]->id) selected="selected" @endif>{{$campaign->name}} </option>

                                        @endforeach


                                      </select><div id="alert-campaign" style="margin-top:10px"></div>  

                                      <div id="newTeam">
                                        <label>Immediate Supervisor: </label>
                                        <select name='immediateHead_Campaigns_id' id='immediateHead_Campaigns_id' class='form-control'  style="text-transform:uppercase">
                                          <option value='0'> -- Select Leader -- </option>
                                          @foreach (collect($leaders)->where('campaign_id',$personnel->campaign[0]->id) as $tl)
                                          <option value="{{$tl->id}}" @if ($tl->id == $personnelTL_ihCampID) selected="selected" @endif><span style="text-transform:uppercase"> {{$tl->lastname}}, </span>{{$tl->firstname}} ___ <em>({{$tl->position}})</em> </option>
                                          @endforeach
                                           </select><br/><div id='alert-immediateHead'></div>

                                      </div>  

                                  </td>
                                  <td>
                                    <label>Employee Number: </label> <input type="text" class="form-control required" name="employeeNumber" id="employeeNumber" value="{{$personnel->employeeNumber}}" /> 
                                     <div id="alert-employeeNumber" style="margin-top:10px"></div>


                                     <label>Biometrics Access Code: </label> <input tabindex="4" type="text" class="form-control required" name="accesscode" required id="accesscode" value="{{$personnel->accesscode}}" /> 
                                     <div id="alert-accesscode" style="margin-top:10px"></div>


                                     <label class="pull-left">OAMPI E-mail: &nbsp;&nbsp; </label> <input type="text" style="width:200px;" class="form-control required pull-left" name="oampi" id="oampi" value="{{$personnel->email}}" />
                                     <div id="alert-email" style="margin-top:10px"></div>

                                     <div class="clearfix" style="margin-top: 65px">&nbsp;</div>


                                     <label class="pull-left">Date of Birth:  </label> <input tabindex="14" type="text" class="form-control datepicker pull-left" style="margin-left: 15px; width:50%" name="birthday" id="birthday" @if($personnel->birthday ==='0000-00-00' || $personnel->birthday == '1970-01-01' || is_null($personnel->birthday) ) placeholder="MM/DD/YYYY" @else value="{{ date('m/d/Y',strtotime($personnel->birthday)) }}" @endif />
                                      <div id="alert-birthday" style="margin-top:10px"></div>



                                      <div class="clearfix" style="margin-top:90px"></div>

                                      @if( empty($personnel->leadOverride) )
                                     <label for="leadOverride"><input type="checkbox" name="leadOverride" id="leadOverride" value="1"></input> Override Leader settings</label><br/><em><small>This will enable a leader to be evaluated using agent-level competencies</small></em>

                                     @else
                                     <label for="leadOverride"><input type="checkbox" name="leadOverride" id="leadOverride" value="1" checked="checked"></input> Override Leader settings</label><br/><em><small>This will enable a leader to be evaluated using agent-level competencies</small></em>
                                     @endif

                                      <div class="clearfix" style="margin-top:25px"></div>

                                     <label>Floor location: </label>
                                      <select class="form-control" name="floor_id" id="floor_id" style="width:50%">
                                        <option value="0">- Select one - </option>

                                        @foreach ($floors as $floor)
                                        <option value="{{$floor->id}}" @if ($floor->id == $personnel->floor[0]->id) selected="selected" @endif>{{$floor->name}} </option>

                                        @endforeach

                                      </select><div id="alert-floor" style="margin-top:10px"></div> 



                                   </td>

                                </tr>

                                <tr>
                                  <td>
                                    <label>Training Start Date: </label> <input type="text" class="form-control datepicker" style="width:50%" name="startTraining" id="startTraining" @if($personnel->startTraining ==='0000-00-00' || $personnel->startTraining === '1970-01-01' || is_null($personnel->startTraining)) placeholder="MM/DD/YYYY" @else value="{{ date('m/d/Y',strtotime($personnel->startTraining)) }}" @endif  />
                                  

                                   <label>Training End Date: </label> <input type="text" class="form-control datepicker" style="width:50%" name="endTraining" id="endTraining" @if($personnel->endTraining ==='0000-00-00' || $personnel->endTraining === '1970-01-01' || is_null($personnel->endTraining)) placeholder="MM/DD/YYYY" @else value="{{ date('m/d/Y',strtotime($personnel->endTraining)) }}" @endif />
                                   

                                  </td>
                                  <td>
                                    <label>Date Hired: </label> <input required type="text" class="form-control datepicker" style="width:50%" name="dateHired" id="dateHired" value="{{date('m/d/Y',strtotime($personnel->dateHired) ) }} " />
                                   <div id="alert-dateHired" style="margin-top:10px"></div>

                                    <label>Date Regularized: </label> <input type="text" class="form-control datepicker" style="width:50%" name="dateRegularized" id="dateRegularized" <?php  if ( $personnel->dateRegularized !== null ) { ?> value="{{date('m/d/Y',strtotime($personnel->dateRegularized) ) }}" <?php } else {?> placeholder="specify date" <?php }; ?>   /> 
                                  <div id="alert-dateRegularized" style="margin-top:10px"></div></td>

                                </tr>

                                <tr>
                                  <td><label>System User Type: </label>
                                    <div id="alert-userType" style="margin-top:10px"></div>
                                    @foreach ($userTypes as $type)

                                    <label> <input type="radio" name="userType_id" required value="{{$type->id}}" @if ($type->id == $personnel->userType_id) checked="checked" @endif /> {{$type->name}} </label><br/>

                                    @endforeach
                                    
                                   
                                  </td>

                                  <td style="padding-top:0px">
                                    
                                    <label>Employment Status: </label>
                                    <div id="alert-status" style="margin-top:10px"></div>
                                    @foreach ($statuses as $status)

                                    <label> <input type="radio" name="status_id" required value="{{$status->id}}"@if ($status->id == $personnel->status_id) checked="checked" @endif /> {{$status->name}} </label><br/>

                                    @endforeach
                                  

                                     
                                  </td>

                                </tr>


                                <tr>
                                  <td colspan="3" style="background-color: #e6e6e6;">&nbsp;
                                    </td>
                                </tr>
                            </table>



                            <input name="name" id="name" type="hidden" /><input name="email" id="email" type="hidden" value="{{$personnel->email}} "/><input name="password" id="password" type="hidden"/>

                            <p class="text-center"> 
                        <a href="{{action('UserController@show', $personnel->id)}} " class="btn btn-md btn-default"><i class="fa fa-reply"></i> Back to Employee Profile</a>
                          <input type="submit" class="btn btn-md btn-success " name='submit' value="Save" />
                          
                          <div id="alert-submit" style="margin-top:20px"></div>
                        </p>
                        <p>&nbsp;</p>
                        </form>
                        

                      </div><!--end profile data pane -->


                      <div class="chart tab-pane @if( $page == '2') active @endif" id="approvers_pane" style="position: relative; height: 300px;">
                        <table class="table" style="margin-top: 40px">
                          <tr>
                              <td colspan="3" style="background-color: #e6e6e6;">
                                <h4 class="text-primary pull-left"><i class="fa fa-thumbs-up"></i>&nbsp;&nbsp; Approver(s)</h4>
                              <a data-toggle="modal" style="margin-top: 10px" data-target="#myModal_{{$personnel->id}}" href="#" class="btn btn-xs btn-primary pull-right"><i class="fa fa-cog"></i> Approver Settings</a>
                            </td>
                            </tr>

                            <tr>
                              <td><label>Approver(s) :</label></td>
                              <td>
                                
                                <?php foreach($approvers as $app){
                                  $lead = OAMPI_Eval\ImmediateHead::find($app->immediateHead_id);?>

                                  {{$lead->firstname}} {{$lead->lastname}}<br/>

                                <?php } ?> <br/>
                               

                              </td>
                              <td></td>
                            </tr>
                          
                        </table>
                        

                      </div><!-- end APPROVERS pane -->

                      <div class="chart tab-pane @if( $page == '4') active @endif" id="access_pane" style="position: relative; height: 300px;">
                        <table class="table" style="margin-top: 40px">
                          <tr>
                              <td colspan="3" style="background-color: #e6e6e6;">
                                <h4 class="text-primary pull-left"><i class="fa fa-key"></i>&nbsp;&nbsp; Grant Special Access</h4>
                              <a id="grantaccess" class="btn btn-md btn-success pull-right"><i class="fa fa-save"></i> Save Settings</a>
                            </td>
                            </tr>

                            <tr>
                              <td><label>Employee Directory:</label></td>
                              <td>

                                @if(!$hasSpecialAccess)
                                <label><input type="radio" name="directoryaccess" value="0" checked="checked" /> Disabled</label><br/>
                                @else
                                <label><input type="radio" name="directoryaccess" value="0" /> Disabled</label><br/>
                                
                                @endif

                                @if ($isHR || $alwaysAccessible)
                                <label><input type="radio" name="directoryaccess" value="1" checked="checked" /> Always Enabled</label><br/>
                                @else
                                <label><input type="radio" name="directoryaccess" value="1" /> Always Enabled</label><br/>
                                @endif

                                @if($hasSpecialAccess && !$isHR && !$alwaysAccessible)
                                <label><input type="radio" name="directoryaccess" value="2" checked="checked" /> Limited Access from</label>
                                <input class="datepicker" type="text" id="accessfrom" name="accessfrom" placeholder="MM/DD/YY" value="{{ date('m/d/Y', strtotime($accessDir->first()->startDate))}}" /> to  
                                <input class="datepicker" type="text" id="accessto" name="accessto" placeholder="MM/DD/YY" value="{{ date('m/d/Y', strtotime($accessDir->first()->endDate))}}" />

                                @else
                                <label><input type="radio" name="directoryaccess" value="2" /> Limited Access from</label>
                                <input class="datepicker" type="text" id="accessfrom" name="accessfrom" placeholder="MM/DD/YY" /> to  
                                <input class="datepicker" type="text" id="accessto" name="accessto" placeholder="MM/DD/YY" />

                                @endif
                                
                                
                               

                              </td>
                              <td></td>
                            </tr>
                          
                        </table>
                        

                      </div><!-- end APPROVERS pane -->


                      <!-- ********* LEAVE CREDITS PANE ************ -->

                      <div class="chart tab-pane @if($page=='3') active @endif" id="leaveCredits_pane" style="position: relative; height: auto;">
                        <table class="table" style="margin-top: 40px">
                          <tr>
                              <td colspan="3" style="background-color: #e6e6e6;">
                                <h4 class="text-primary pull-left"><i class="fa fa-bar-chart"></i>&nbsp; Leave Credits</h4>
                              </td>
                            </tr>

                            
                            <!-- ************ VACATION LEAVE *************-->
                            <tr>
                              <td><h4><i class="fa fa-plane"></i> Vacation Leave</h4></td>
                              <td colspan="2">
                              <table id="vl" class="table">
                                <thead>
                                  <th class="text-center">Year</th>
                                  <th class="text-center">Beginning Balance</th>
                                  <th class="text-center">Used</th>
                                  <th class="text-center">Paid</th>
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
                                        <td class="text-center">{{$v->used}} </td>
                                        <td class="text-center"> N/A </td>
                                        <td class="text-center @if($ctr==1) text-success" style="font-size: larger; font-weight: bold; @endif">{{ number_format( ($v->beginBalance - $v->used)-$v->paid, 2) }}</td>
                                        <td class="text-center">
                                          <!-- <a href="" class="btn btn-xs btn-default" style="margin-right: 5px"><i class="fa fa-list"></i> Details </a>  -->
                                          <a class="editLeave btn btn-xs btn-default" data-leavetype="vl" data-leaveid="{{$v->id}}" data-toggle="modal"  data-target="#myModal_edit_vl{{$v->id}}" style="margin-right: 5px"><i class="fa fa-pencil"></i> Edit </a> 
                                          <a data-toggle="modal"  data-target="#myModal_vl{{$v->id}}"  class="btn btn-xs btn-default" style="margin-right: 5px"><i class="fa fa-trash"></i> Delete</a></td>
                                      </tr>

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



                                      <?php $ctr++; ?>
                                      @endforeach
                                  @endif
                                  <tr><td colspan="6">

                                    @if (count($personnel->vlCredits) <= (date('Y')-2008))
                                    <a data-toggle="modal" style="margin-top: 10px" data-target="#myModal_addVL{{$personnel->id}}" href="#" class="btn btn-xs btn-primary pull-right"><i class="fa fa-plus"></i> Add VL Credits</a></td>
                                    @endif</tr>

                                  
                                </tbody>
                              </table>

                              </td>
                            </tr>

                            <!-- ************ SICK LEAVE *************-->
                            <tr>
                              <td><h4><i class="fa fa-stethoscope"></i> Sick Leave</h4></td>
                              <td colspan="2">
                              <table id="sl" class="table">
                                <thead>
                                  <th class="text-center">Year</th>
                                  <th class="text-center">Beginning Balance</th>
                                  <th class="text-center">Used</th>
                                  <th class="text-center">Paid</th>
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
                                        <td class="text-center">{{$v->used}} </td>
                                        <td class="text-center">{{$v->paid}}  </td>
                                        <td class="text-center @if($ctr==1) text-success" style="font-size: larger; font-weight: bold; @endif">{{ number_format(($v->beginBalance - $v->used)-$v->paid,2) }}</td>
                                        <td class="text-center">
                                          <!-- <a href="" class="btn btn-xs btn-default" style="margin-right: 5px"><i class="fa fa-list"></i> Details </a>  -->
                                          <a class="editLeave btn btn-xs btn-default" data-leavetype="vl" data-leaveid="{{$v->id}}" data-toggle="modal"  data-target="#myModal_edit_sl{{$v->id}}" style="margin-right: 5px"><i class="fa fa-pencil"></i> Edit </a> 
                                          <a data-toggle="modal"  data-target="#myModal_sl{{$v->id}}"  class="btn btn-xs btn-default" style="margin-right: 5px"><i class="fa fa-trash"></i> Delete</a></td>
                                      </tr>

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



                                      <?php $ctr++; ?>
                                      @endforeach
                                  @endif
                                  <tr><td colspan="6">
                                    @if (count($personnel->slCredits) <= (date('Y')-2008))
                                    <a data-toggle="modal" style="margin-top: 10px" data-target="#myModal_addSL{{$personnel->id}}" href="#" class="btn btn-xs btn-primary pull-right"><i class="fa fa-plus"></i> Add SL Credits</a>
                                    @endif
                                  </td></tr>
                                  
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

            @include('layouts.modals-approver', [
                    'modelRoute'=>'approver.store',
                    'modelID' => '_'.$personnel->id, 
                    'modalMessage'=> " ",
                    'modelName'=>"Approver ", 
                    'modalTitle'=>'Add New', 
                    'formID'=>'submitApprover',
                    'icon'=>'glyphicon-up' ])


           


            
           

          
          </div><!-- end row -->

       
     </section>
          



@endsection


@section('footer-scripts')

<script>
  $.widget.bridge('uibutton', $.ui.button);
</script>


<!-- <script src="{{URL::asset('public/js/jquery.cropit.js')}}"></script> -->



<!-- Page script -->
<script>
  
  $(function () {
   'use strict';

   
   // $('#image-cropper').cropit({width: 200, height:200,imageBackgroundBorderWidth: 15});

   //  $('.download-btn').click(function() {
   //    var imageData = $('#image-cropper').cropit('export');
   //    window.open(imageData);
   //  });


   $("select[name='position_id']").on('change', function(){
     var pos =  $(this).find(':selected').val();

     if (pos == "-1"){ //add new position
      var htmcode = "<br/><input required type='text' class='form-control' name='newPosition' id='newPosition' placeholder='Enter new job position' value='' />";

        $('#newpos').html(htmcode);
        console.log(htmcode);

     }
     
   });

   $('#grantaccess').on('click',function(){

    var selval = $('input[name="directoryaccess"]:checked');
    var startDate = $('#accessfrom').val();
    var endDate = $('#accessto').val();
    var _token = "{{ csrf_token() }}";

    if (selval.length < 1) { alert("Kindly indicate type of user access.");return false;}
    else if(selval.val() == '2' && (startDate == "" || endDate=="" )){
      alert("Kindly indicate start and end dates.");return false;

    }else{
      $.ajax({

          url: "{{action('UserController@grantAccess')}}",
          type: 'POST',
          data: {
              user: "{{$personnel->id}}",
              directoryaccess: selval.val(),
              role_id: "{{$role_id}}",
              startDate: startDate,
              endDate: endDate,
              _token: _token
          },
          error: function(response){

          },
          success: function(response){

                  console.log(response);

                  $.notify("User access for "+response['user'].firstname+" "+response['user'].lastname+" updated successfully.",{className:"success",globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );
                  setTimeout(function(){
                     window.location.reload(1);
                  }, 5000);

                  

          }

      });
    }
    //console.log(selval);

   });

   
    $("select[name='campaign_id']").on('change', function(){
                  var camp = $(this).find(':selected').val();

                  if (camp !== 0){
                    var _token = "{{ csrf_token() }}";

                     $.ajax({
                      url:"{{url('/')}}/campaign/"+camp+"/leaders",
                      type:'GET',
                      data:{id:camp, _token:_token},
                      error: function(response)
                      {
                         
                        
                        console.log("Error leader: "+response.id);

                          
                          return false;
                      },
                      success: function(response)
                      {
                        var htmlcode2 = "<label>Immediate Supervisor: </label><select name='immediateHead_Campaigns_id' id='immediateHead_Campaigns_id' class='form-control'>";
                        htmlcode2 += "<option value='0'> -- Select Leader -- </option>";
                        $.each(response, function(index) {

                          htmlcode2 +="<option value='"+response[index].id+"'>"+response[index].lastname+", "+response[index].firstname+"</option>";
                        
                        }); //end each

                        htmlcode2 +="</select><br/><div id='alert-immediateHead'></div>";

                        $('#newTeam').html(htmlcode2);



                      }//end success

                      }); //end ajax
                    }//end if

    }); //end select on change


$('input[name="dateHired"]').on('focusout',function(){ 
    var dateHired = $('input[name="dateHired"]').val();
    var dateReg = moment(dateHired,"MM/DD/YYYY");
    $('input[name="dateRegularized"]').val(dateReg.add(6,'months').format('MM/DD/Y'));

  });




   $('#addEmployee').on('submit', function(e) {

    e.preventDefault();
    console.log('Enter submit');
      var _token = "{{ csrf_token() }}";
      var userType_id = $('input[name="userType_id" ]:checked').val();
      
      var firstname = $('#firstname').val();
      var middlename = $('#middlename').val();
      var lastname = $('#lastname').val();
      var nickname = $('#nickname').val();
      var birthday = $('#birthday').val();
      var gender = $('input[name="gender" ]:checked').val();
      var status_id = $('input[name="status_id" ]:checked').val();
      var username = "{{$personnel->name}}";
      var employeeNumber = $('#employeeNumber').val();
      var accesscode = $('#accesscode').val();
      var campaign_id = $('select[name="campaign_id"]').find(':selected').val();
      var floor_id = $('select[name="floor_id"]').find(':selected').val();
     
      var email = $("#oampi").val();

      var immediateHead_Campaigns_id = $('select[name="immediateHead_Campaigns_id"]').find(':selected').val();
      var position_id = $('select[name="position_id"]').find(':selected').val();

      if ($('input[name="leadOverride"]').is(':checked')){
        var leadOverride = 1;
      } else var leadOverride = 0;
      
      var dateHired = $('#dateHired').val();
      var dateRegularized = $('#dateRegularized').val();

      var startTraining = $('#startTraining').val();
      var endTraining = $('#endTraining').val();

      var alertCampaign = $('#alert-campaign');
      var alertImmediateHead = $('#alert-immediateHead');
      var alertPosition = $('#alert-position');
      var alertDateHired = $('#alert-dateHired');
      var alertStatus = $('#alert-status');
      var alertUserType = $('#alert-userType');

     

     
      

     

      if (!validateRequired(campaign_id,alertCampaign,"0")) { 
        console.log('not valid Program '+campaign_id); e.preventDefault(); e.stopPropagation();
        return false;
      } else if (!validateRequired(immediateHead_Campaigns_id,alertImmediateHead,"0")){ 
        console.log('not valid Head'); e.preventDefault(); e.stopPropagation(); return false;
      } else if (!validateRequired(position_id,alertPosition,"0")){ 
          console.log('not valid Position '+position_id); e.preventDefault(); e.stopPropagation();return false;
      } else if (position_id == "-1"){

          var newPos = $('input[name="newPosition"]').val();
          if (!validateRequired(newPos,alertPosition,"Enter new job position"))
          {
            console.log('not valid PosValue: '+newPos); e.preventDefault(); e.stopPropagation(); return false; 
          } else {
            //save first the new position
            console.log("save pos first");
              $.ajax({
                                url:"{{action('PositionController@store')}} ",
                                type:'POST',
                                data:{
                                  'name': newPos,
                                  _token:_token},

                                error: function(response)
                                { console.log("Error saving position: "); return false;
                                },
                                success: function(response)
                                {
                                  var posID = response.id;
                                  console.log("Saved position");

                                  //check the rest of the form
                                  if( !validateRequired(dateHired,alertDateHired,"") || !validateRequired(status_id,alertStatus,"") || !validateRequired(userType_id,alertUserType,"")){
                                    e.preventDefault(); e.stopPropagation(); return false; 

                                  } else {

                                    //save employee
                                    console.log("Save employee then");
                                    
                                    saveEmployee(firstname,middlename,lastname,nickname,gender,birthday,employeeNumber,accesscode,email,dateHired,dateRegularized, startTraining, endTraining, userType_id,status_id,leadOverride,posID,campaign_id,floor_id,immediateHead_Campaigns_id, _token);

                                  }



                                }//end success

                    }); //end ajax

          
          }//end else valid new position

      } else {

           if( !validateRequired(dateHired,alertDateHired,"") || !validateRequired(status_id,alertStatus,"") || !validateRequired(userType_id,alertUserType,""))
           {
             e.preventDefault(); e.stopPropagation(); return false; 
           } else {
           
            

            setTimeout(saveEmployee(firstname,middlename,lastname,nickname,gender,birthday,employeeNumber,accesscode,email,dateHired,dateRegularized, startTraining, endTraining, userType_id,status_id,position_id,leadOverride,campaign_id,floor_id,immediateHead_Campaigns_id, _token ),1);
            
            
         }

       } //end else if -1
      
     
      return false;
    }); //end addEmployee





   $( ".datepicker" ).datepicker();

       

});


function saveEmployee(firstname,middlename,lastname,nickname,gender,birthday,employeeNumber,accesscode,email,dateHired,dateRegularized, startTraining, endTraining, userType_id,status_id,position_id,leadOverride,campaign_id,floor_id,immediateHead_Campaigns_id, _token){

   //save movement
   console.log("Enter function");

   $.ajax({
            url:"{{action('UserController@update', $personnel->id)}}",
            type:'PUT',
            data:{

              'nickname': nickname,
              'firstname': firstname,
              'middlename': middlename,
              'lastname': lastname,
              'gender':gender,
              'birthday':birthday,
              'employeeNumber': employeeNumber,
              'accesscode':accesscode,
              'email': email,
              'dateHired': dateHired,
              'dateRegularized': dateRegularized,
              'startTraining': startTraining,
              'endTraining': endTraining,
              'userType_id': userType_id,
              'status_id': status_id,
              'position_id': position_id,
              'leadOverride': leadOverride,
              'campaign_id': campaign_id,
              'floor_id':floor_id,
              'immediateHead_Campaigns_id': immediateHead_Campaigns_id,
              '_token':_token
            },

           
            success: function(response2)
            {
              console.log(response2);
               $('input[name="submit"').fadeOut();
                      var htmcode = "<span class=\"success\"> <i class=\"fa fa-save\"></i> Employee  data updated. <br /><br/>";
                     
                      htmcode += "<a href=\"{{action('UserController@show',$personnel->id)}}\" class='btn btn-sm btn-default text-black pull-right'><i class='fa fa-reply'></i> Back to Employee Profile </a> <br/><br/>";
                      
                      $('#alert-submit').addClass('alert alert-success').fadeIn().html(htmcode); 

            }
          });

return false;


   

}

function validateRequired(param, availability, defaultval) {

        
        if (param == null){

          availability.addClass('alert alert-danger').fadeIn();
            availability.html('<span class="success"> <i class="fa fa-warning"></i> This field is required. </span>');  
             return false;
        }

        else if(param.length <= 0 || param === defaultval) { 
            
            availability.addClass('alert alert-danger').fadeIn();
            availability.html('<span class="success"> <i class="fa fa-warning"></i> This field is required. </span>');   
             return false;         
            

        } else{
            availability.removeClass();
            availability.html('');
            return true;
                      
        }
       

}

   


   

 
</script>
<!-- end Page script -->

<!-- <script type="text/javascript" src="{{asset('public/js/jquery.validate.js')}}"></script> -->

@stop