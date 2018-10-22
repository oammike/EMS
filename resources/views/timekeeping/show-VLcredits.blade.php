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
                        <p style="padding: 20px; font-size: smaller"><i class="fa fa-info-circle"></i> Note: Regular employees who have  completed six  (6)  months  shall be  entitled  to  five   (5)  days  of  sick  leave and five (5) days of vacation leave.  For every month thereafter, employee  will  earn  an  additional  0.84  days  per month worked. Probationary, contractual employees may be  allowed to  take  sick/vacation  leave days  but will  not be  considered  as  paid  leave. Even if  the employee  has no  leave credit  yet,  filing of  LWOP (leave without pay) is  mandatory as  basis of  excused absence/s. </p>
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
                              <td><h4><i class="fa fa-stethoscope"></i> Sick Leave</h4> <br/> <a class="btn btn-default btn-xs" href="{{action('UserSLController@create')}}"><i class="fa fa-upload"></i> File New SL</a></td>
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
                                    @if (count($personnel->slCredits) <= (date('Y')-2008) && $canUpdateLeaves)
                                    <a data-toggle="modal" style="margin-top: 10px" data-target="#myModal_addSL{{$personnel->id}}" href="#" class="btn btn-xs btn-primary pull-right"><i class="fa fa-plus-circle"></i> Add SL Credits</a>

                                    <!-- <a data-toggle="modal" style="margin-top: 10px" data-target="#underConstruction" href="#" class="btn btn-xs btn-primary pull-right"><i class="fa fa-plus-circle"></i> Add SL Credits</a> -->
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