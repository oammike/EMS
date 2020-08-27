@extends('layouts.main')

@section('metatags')
<title>Leave Management | EMS</title>

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
        Leave Management
        
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
                  <ul class="nav nav-tabs">


                    <li @if($type =='VL') class="active" @endif ><a <a href="{{action('UserController@leaveMgt',['type'=>'VL','from'=>$from, 'to'=>$to])}}"><strong class="text-primary "><i class="fa fa-2x fa-plane"></i> Vacation Leaves 
                      @if($pending_VL)<span class="label label-warning" style="font-size: small;"> ({{$pending_VL}}) </span> @endif </strong></a></li>
                    <li @if($type =='SL') class="active" @endif ><a href="{{action('UserController@leaveMgt',['type'=>'SL','from'=>$from, 'to'=>$to])}}" ><strong class="text-primary"><i class="fa fa-2x fa-stethoscope"></i> Sick Leaves 
                      @if($pending_SL)<span class="label label-warning" style="font-size: small;"> ({{$pending_SL}}) </span> @endif</strong></a></li>
                    <li @if($type =='LWOP') class="active" @endif ><a href="{{action('UserController@leaveMgt',['type'=>'LWOP','from'=>$from, 'to'=>$to])}}" ><strong class="text-primary"><i class="fa fa-2x fa-meh-o"></i> LWOP 
                      @if($pending_LWOP)<span class="label label-warning" style="font-size: small;"> ({{$pending_LWOP}}) </span> @endif</strong></a></li>

                     <li @if($type =='VTO') class="active" @endif ><a <a href="{{action('UserController@leaveMgt',['type'=>'VTO','from'=>$from, 'to'=>$to])}}"><strong class="text-primary "><i class="fa fa-2x fa-history"></i> VTO
                      @if($pending_VTO)<span class="label label-warning" style="font-size: small;"> ({{$pending_VTO}}) </span> @endif </strong></a></li>
                    
                    <li @if($type =='FL') class="active" @endif ><a href="{{action('UserController@leaveMgt',['type'=>'FL','from'=>$from, 'to'=>$to])}}" ><strong class="text-primary"><i class="fa fa-2x fa-male"></i><i class="fa fa-2x fa-female"></i> ML | PL | SPL 
                      @if($pending_FL)<span class="label label-warning" style="font-size: small;"> ({{$pending_FL}}) </span> @endif</strong></a></li>
                     @if ($isAdmin) 
                     <!--  <a href="{{action('UserController@create')}} " class="btn btn-sm btn-primary  pull-right"><i class="fa fa-plus"></i> Add New Employee</a>
                      -->
                      @endif


                  </ul>


                  

                  <div class="tab-content">
                    @if($canCredit)
                     
                     <a href="{{action('UserController@leaveMgt_summary')}}" class="btn btn-default pull-right" style="margin-left: 5px"><i class="fa fa-download"></i>  Summary </a>

                     <a  data-toggle="modal" data-target="#myModal_giveNewEearning" class="btn btn-default pull-right" style="margin-left: 5px"><i class="fa fa-line-chart"></i> Give Credits </a>

                    <a  data-toggle="modal" data-target="#myModal_addNewEearning" class="btn btn-default pull-right" style="margin-left: 5px"><i class="fa fa-plus"></i> New </a>
                     @if($type =='VL')
                     <a href="{{action('UserController@leaveMgt_earnings',['type'=>'VL','emp'=>'1','from'=>$from,'to'=>$to])}}" class="btn btn-primary pull-right"><i class="fa fa-calendar-check-o"></i> VL Earnings</a>
                     @elseif($type =='SL')
                     <a href="{{action('UserController@leaveMgt_earnings',['type'=>'SL','emp'=>'1','from'=>$from,'to'=>$to])}}" class="btn btn-danger pull-right"><i class="fa fa-calendar-check-o"></i> SL Earnings</a>
                     @endif
                    @endif

                    <div class="tab-pane active" id="tab_1"> <!-- ACTIVE EMPLOYEES -->
                      <h1 class="pull-right" style="color:#dedede">{{$label}}</h1>
                      <div class="row" style="margin-top:50px">

                        <table class="table no-margin table-bordered table-striped" id="active" width="95%" style="margin:0 auto;" >

                          <thead>
                            <th>Lastname</th>
                            <th width="10%">Firstname</th>
                            <th>Program</th>
                            <th width="10%">Credits</th>
                            <th>Period</th>
                            <th>Status</th>
                            <th width="28%">Action</th>
                          </thead>
                          <tbody>
                            @foreach($allLeave as $vl)

                             <tr id="row{{$vl->leaveID}}" @if( is_null($vl->isApproved)) style="background-color:#fcfdc4" @endif >
                                <td>{{$vl->lastname}}</td>
                                <td style="font-size: x-small;">{{$vl->firstname}}</td>
                                <td>{{$vl->program}}</td>

                                @if($type=='VTO')

                                <td>{{ number_format($vl->totalCredits*0.125,2) }}</td>
                                @else
                                <td>{{$vl->totalCredits}}</td>

                                @endif


                                <td style="font-size: x-small;">
                                  @if($type=='FL')
                                  <?php switch ($vl->FLtype) {
                                    case 'ML': $l="Maternity Leave";break;case 'PL': $l="Paternity Leave";break;case 'SPL': $l="Single Parent Leave";break;
                                  } ?>
                                  <strong>{{$l}} </strong><br/>
                                  {{date('M d h:i A', strtotime($vl->leaveStart))}} - {{date('M d h:i A', strtotime($vl->leaveEnd))}}  
                                  @else
                                  <small>{{$label}} </small><br/>

                                      @if($type=='VTO')
                                      <strong>{{date('M d', strtotime($vl->productionDate))}}</strong> {{date('h:i A', strtotime($vl->leaveStart))}} - <strong>{{date('M d', strtotime($vl->productionDate))}}  </strong>{{date('h:i A', strtotime($vl->leaveEnd))}}
                                      @else
                                       <strong>{{date('M d', strtotime($vl->leaveStart))}}</strong> {{date('h:i A', strtotime($vl->leaveStart))}} - <strong>{{date('M d', strtotime($vl->leaveEnd))}}  </strong>{{date('h:i A', strtotime($vl->leaveEnd))}}

                                      @endif

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

                                  @if($vl->leaveStart < $unrevokeable)
                                  
                                    <a class="btn btn-xs btn-default" data-toggle="modal" data-target="#leaveModal{{$vl->leaveID}}"><i class="fa fa-info-circle"></i> Details </a>
                                    <a class="btn btn-xs btn-default" href="{{action('UserVLController@showCredits',$vl->userID)}}" target="_blank"><i class="fa fa-calendar-check-o"></i> Leave Credits </a>

                                    @if($type=='VTO')
                                      <a target="_blank" class="btn btn-xs btn-default" href="{{url('/')}}/user_dtr/{{$vl->userID}}?from= {{$vl->productionDate}}&to={{$vl->productionDate}}"><i class="fa fa-calendar"></i> DTR </a>
                                    @else
                                      <a target="_blank" class="btn btn-xs btn-default" href="{{url('/')}}/user_dtr/{{$vl->userID}}?from={{date('Y-m-d',strtotime($vl->leaveStart))}}&to={{date('Y-m-d',strtotime($to))}}"><i class="fa fa-calendar"></i> DTR </a>

                                    @endif

                                    @if($vl->isApproved != '1')<a class="btn btn-xs btn-default" data-toggle="modal" data-target="#delete{{$vl->leaveID}}"><i class="fa fa-trash"></i> </a>@endif
                                  
                                  @else
                                    @if($vl->isApproved == '1')
                                    <a class="btn btn-xs btn-default" data-toggle="modal" data-target="#leaveModal{{$vl->leaveID}}"><i class="fa fa-info-circle"></i> Details </a>

                                    @else
                                    <a class="btn btn-xs btn-warning" data-toggle="modal" data-target="#leaveModal{{$vl->leaveID}}"><i class="fa fa-pencil"></i> Update </a>
                                     
                                    @endif

                                    <a class="btn btn-xs btn-default" href="{{action('UserVLController@showCredits',$vl->userID)}}" target="_blank"><i class="fa fa-calendar-check-o"></i> Leave Credits </a>

                                    @if($type=='VTO')
                                      <a target="_blank" class="btn btn-xs btn-default" href="{{url('/')}}/user_dtr/{{$vl->userID}}?from={{date('Y-m-d',strtotime($vl->productionDate))}}&to={{date('Y-m-d',strtotime($to))}}"><i class="fa fa-calendar"></i> DTR </a>
                                    @else
                                      <a target="_blank" class="btn btn-xs btn-default" href="{{url('/')}}/user_dtr/{{$vl->userID}}?from={{date('Y-m-d',strtotime($vl->leaveStart))}}&to={{date('Y-m-d',strtotime($to))}}"><i class="fa fa-calendar"></i> DTR </a>

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
                                                
                                                <div class="col-sm-4 text-center"><h5 class="text-primary text-center">Covered Date(s)</h5></div>
                                                <div class="col-sm-2"><h5 class="text-primary">Credits</h5></div>
                                                <div class="col-sm-6"><h5 class="text-primary">Notes</h5></div>
                                                
                                                
                                              </div>

                                               <div class="row">
                                                
                                                @if($type=='VTO')
                                                  <div class="col-sm-4 text-center" style="font-size: 12px">{{date('M d, Y [h:i A]',strtotime($vl->productionDate." ".$vl->leaveStart))}}<br/>TO<br/> {{date('M d, Y [h:i A]',strtotime($vl->productionDate." ".$vl->leaveEnd))}}</div>


                                                  <div class="col-sm-2">
                                                    <p><strong> {{ number_format($vl->totalCredits*0.125,2)}} </strong></p>
                                                  </div>
                                                  <div class="col-sm-6">
                                                   <p>Deduct from: <strong>[{{$vl->deductFrom}}]</strong> <br/>{{$vl->notes}} </p>
                                                  </div>

                                                @else
                                                  <div class="col-sm-4 text-center" style="font-size: 12px">{{date('M d, Y [h:i A]',strtotime($vl->leaveStart))}}<br/>TO<br/> {{date('M d, Y [h:i A]',strtotime($vl->leaveEnd))}}</div>


                                                  <div class="col-sm-2">
                                                    <p><strong> {{$vl->totalCredits}} </strong></p>
                                                  </div>
                                                  <div class="col-sm-6">
                                                   <p>{{$vl->notes}} </p>
                                                  </div>

                                                @endif


                                                

                                                
                                              
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
                                          <?php if ($type=='FL'){
                                                  switch ($vl->FLtype) {
                                                    case 'ML': $typeid = 16; break;
                                                    case 'PL': $typeid = 17; break;
                                                    case 'SPL': $typeid = 18; break;
                                                  }
                                                }else $typeid = $notifType;?>
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

            <?php ($type=='VL') ? $route="user_vl.addVLupdate" : $route="user_sl.addSLupdate"; ?>
            @if($type=='VL')
            @include('layouts.modals-addNewEarnings', [
                    'modelRoute'=>$route,
                    'modelID' => '', 
                    'modalMessage'=> " ",
                    'modelName'=>$type." Earnings ", 
                    'modalTitle'=>'Add New', 
                    'formID'=>'submitSLearn',
                    'icon'=>'glyphicon-up' ])

            @include('layouts.modals-giveNewEarnings', [
                    'modelRoute'=>$route,
                    'modelID' => '', 
                    'modalMessage'=> " ",
                    'modelName'=>$type." Earnings ", 
                    'modalTitle'=>'Give New', 
                    'formID'=>'submitSLearn',
                    'icon'=>'glyphicon-up' ])

            @endif


           


            
           

          
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
        case 'VL':  var processlink = "{{action('UserVLController@process')}}"; break;
        case 'VTO':  var processlink = "{{action('UserVLController@processVTO')}}"; break;
        case 'SL':  var processlink = "{{action('UserSLController@process')}}";break
        case 'LWOP':  var processlink = "{{action('UserLWOPController@process')}}";break;
        case 'FL':  var processlink = "{{action('UserFamilyleaveController@process')}}";break;
        case 'ML':  var processlink = "{{action('UserFamilyleaveController@process')}}";break;
        case 'PL':  var processlink = "{{action('UserFamilyleaveController@process')}}";break;
        case 'SPL':  var processlink = "{{action('UserFamilyleaveController@process')}}";break;
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
                      window.setTimeout(function(){
                        window.location.href = "{{url('/')}}/leave_management?type={{$type}}&from="+f+"&to="+to;
                      }, 3000);
                     /*$.notify("Requested "+t+ " marked Approved.",{className:"success",globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );window.location.href = "{{url('/')}}/leave_management?type={{$type}}&from="+f+"&to="+to;*/
                    }
                   else {
                    $('#row'+dataid).fadeOut();
                    window.setTimeout(function(){
                        window.location.href = "{{url('/')}}/leave_management?type={{$type}}&from="+f+"&to="+to;
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
                      "order": [ 5, "desc" ],
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

      window.location.href = "{{url('/')}}/leave_management?type={{$type}}&from="+f+"&to="+t;

   });
  

   $('table').on('click','.claimedcard',function(){
      var cardid = $(this).attr('data-cardid');
      var empname = $(this).attr('data-name');
      var chck = $(this);
      alert("Claim reward card for employee: "+empname);
      var _token = "{{ csrf_token() }}";

      $.ajax({
                      url:"{{action('UserController@claimedcard')}} ",
                      type:'POST',
                      data:{id:cardid, _token:_token},
                      error: function(response)
                      {
                          
                        console.log("Error saving data: ");

                          
                          return false;
                      },
                      success: function(response)
                      {

                        chck.attr('disabled',true);
                        console.log(response);

                          return true;
                      }
                  });


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