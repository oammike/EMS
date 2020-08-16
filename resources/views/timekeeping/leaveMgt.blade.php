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
                    <div class="col-lg-2"><a id="update" style="margin-top: 20px" class="pull-left btn btn-default btn-lg">Update Table</a> </div>
                  </div>
                 
                 
                 
                </div>
                <div class="col-lg-6"></div>
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
                    <li @if($type =='FL') class="active" @endif ><a href="{{action('UserController@leaveMgt',['type'=>'FL','from'=>$from, 'to'=>$to])}}" ><strong class="text-primary"><i class="fa fa-2x fa-male"></i><i class="fa fa-2x fa-female"></i> ML | PL | SPL 
                      @if($pending_FL)<span class="label label-warning" style="font-size: small;"> ({{$pending_FL}}) </span> @endif</strong></a></li>
                     @if ($isAdmin) 
                     <!--  <a href="{{action('UserController@create')}} " class="btn btn-sm btn-primary  pull-right"><i class="fa fa-plus"></i> Add New Employee</a>
                      -->
                      @endif


                  </ul>
                  

                  <div class="tab-content">
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

                             <tr @if( is_null($vl->isApproved)) style="background-color:#fcfdc4" @endif >
                                <td>{{$vl->lastname}}</td>
                                <td style="font-size: x-small;">{{$vl->firstname}}</td>
                                <td>{{$vl->program}}</td>
                                <td>{{$vl->totalCredits}}</td>
                                <td style="font-size: x-small;">
                                  @if($type=='FL')
                                  <?php switch ($vl->FLtype) {
                                    case 'ML': $l="Maternity Leave";break;case 'PL': $l="Paternity Leave";break;case 'SPL': $l="Single Parent Leave";break;
                                  } ?>
                                  <strong>{{$l}} </strong><br/>
                                  {{date('M d h:i A', strtotime($vl->leaveStart))}} - {{date('M d h:i A', strtotime($vl->leaveEnd))}}  
                                  @else
                                  <small>{{$label}} </small><br/>
                                  <strong>{{date('M d', strtotime($vl->leaveStart))}}</strong> {{date('h:i A', strtotime($vl->leaveStart))}} - <strong>{{date('M d', strtotime($vl->leaveEnd))}}  </strong>{{date('h:i A', strtotime($vl->leaveEnd))}}
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
                                  <a target="_blank" class="btn btn-xs btn-default" href="{{url('/')}}/user_dtr/{{$vl->userID}}?from={{date('Y-m-d',strtotime($vl->leaveStart))}}&to={{date('Y-m-d',strtotime($to))}}"><i class="fa fa-calendar"></i> View DTR </a>
                                  <a class="btn btn-xs btn-default"><i class="fa fa-info-circle"></i> Details </a>
                                  <a class="btn btn-xs btn-default"><i class="fa fa-calendar-check-o"></i> Leave Credits </a>
                                  @else
                                  <a class="btn btn-xs btn-warning"><i class="fa fa-pencil"></i> Update </a>
                                  <a class="btn btn-xs btn-default"><i class="fa fa-calendar-check-o"></i> Leave Credits </a>
                                  @endif
                                </td>
                              </tr>

                          

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
          
  
<!-- Confirm Modal -->
<div class="modal fade" id="qrModal" tabindex="-1" role="dialog" aria-labelledby="qrModalLabel">
  
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="qrModalLabel">Employee QR Tag</h4>
        </div>
        <div class="box modal-body">
          <div id="qr_code_wrapper">
            <p>QR Code for <span id="qrModalName"></span>:</p>
              <p><span id="claimer_error" class="help-block"></span></p>
            <div id="qr-code-container"></div>
          </div>
          
          <div class="overlay" id="qr_loader"> 
            <i class="fa fa-refresh fa-spin"></i>
          </div>
        </div>
        <div class="modal-footer">
          <button id="modalConfirmClose" type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button id="modalConfirmYes" type="button" class="btn btn-primary">Print</button>
        </div>
      </div>
    </div>
  
</div>


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

   $('table').on('click','.wfh',function(){
      var id = $(this).attr('data-cardid');
      var empname = $(this).attr('data-name');
      var chck = $(this).prop('checked');
      if (chck == true) {
        var enableWFH = 1;
        alert("Enable Work From Home for employee: "+empname);
      }

      else {
        var enableWFH = 0;
        alert("Disable Work From Home for employee: "+empname);
      }


      
      var _token = "{{ csrf_token() }}";

      $.ajax({
                      url:"{{action('UserController@wfh')}} ",
                      type:'POST',
                      data:{id:id, enableWFH:enableWFH,  _token:_token},
                      error: function(response)
                      {
                          
                        console.log("Error saving data: ");

                          
                          return false;
                      },
                      success: function(response)
                      {

                        //chck.attr('disabled',true);
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