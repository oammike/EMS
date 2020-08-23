@extends('layouts.main')

@section('metatags')
<title>Leave Earnings | EMS</title>

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
        {{$type}} Earnings
        
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
                  <a class="btn btn-success pull-right" style="margin-left: 2px"> Export CSV </a>

                  @if($type=='VL')
                  <a href="{{action('UserController@leaveMgt_earnings',['type'=>'SL','emp'=>$emp,'from'=>$from,'to'=>$to])}}" class="btn btn-danger pull-right"><i class="fa fa-stethoscope"></i> Sick Leave Earnings</a>
                  @else
                  <a href="{{action('UserController@leaveMgt_earnings',['type'=>'VL','emp'=>$emp,'from'=>$from,'to'=>$to])}}" class="btn btn-primary pull-right"><i class="fa fa-plane"></i> Vacation Leave Earnings</a>
                  @endif
                  
                </div>
              </div>

              <!-- TABLE: LEFT -->
              <div class="box-body">


                <!-- Custom Tabs -->
                <div class="nav-tabs-custom">
                  <ul class="nav nav-tabs">


                    <li @if($emp =='1') class="active" @endif ><a <a href="{{action('UserController@leaveMgt_earnings',['emp'=>'1', 'type'=>'VL','from'=>$from, 'to'=>$to])}}"><strong class="text-primary "> Regular Employees <em>(0.42)</em>
                      @if($emp=='2')<span class="label label-warning" style="font-size: small;"> ({{$pending_VL}}) </span> @endif </strong></a></li>
                    <li @if($emp =='2') class="active" @endif ><a href="{{action('UserController@leaveMgt_earnings',['type'=>'SL','from'=>$from, 'to'=>$to,'emp'=>'2'])}}" ><strong class="text-primary"> Part Time Employees <em>(0.21)</em>
                     </strong></a></li>
                    <li @if($type =='LWOP') class="active" @endif ><a href="{{action('UserController@leaveMgt',['type'=>'LWOP','from'=>$from, 'to'=>$to])}}" ><strong class="text-primary"> New Hires <em>(5.00)</em>
                      </strong></a></li>

                     <li @if($type =='VTO') class="active" @endif ><a <a href="{{action('UserController@leaveMgt',['type'=>'VTO','from'=>$from, 'to'=>$to])}}"><strong class="text-primary "> Irregular Earnings
                      </strong></a></li>
                    
                    
                     @if ($isAdmin) 
                     <!--  <a href="{{action('UserController@create')}} " class="btn btn-sm btn-primary  pull-right"><i class="fa fa-plus"></i> Add New Employee</a>
                      -->
                      @endif


                  </ul>


                  

                  <div class="tab-content">
                    <div class="tab-pane active" id="tab_1"> <!-- ACTIVE EMPLOYEES -->
                      <h1 class="pull-right" style="color:#dedede"> {{$type}} {{$label}}</h1>
                      <div class="row" style="margin-top:50px">

                        <table class="table no-margin table-bordered table-striped" id="active" width="95%" style="margin:0 auto;" >

                          <thead>
                            <th>Lastname</th>
                            <th>Firstname</th>
                            <th>Program</th>
                            @foreach($earnings['months'] as $m)
                            <th>{{$m}} </th>
                            @endforeach
                           
                            <th >Action</th>
                          </thead>
                          <tbody>
                            @foreach($earnings['people'] as $p)

                            <tr>
                              <td>{{$p[0]->lastname}}</td>
                              <td style="font-size: smaller;">{{$p[0]->firstname}}</td>
                              <td>{{$p[0]->program}}</td>
                              <?php $c = 0; ?>
                               @foreach( $earnings['month_updates'] as $m)

                                  @foreach($m['updateIDs'] as $u)
                                    <?php 
                                    ($type=='VL') ? $earned = collect($p)->where('vlupdate_id',$u->id) : $earned = collect($p)->where('slupdate_id',$u->id); 
                                    if (count($earned) > 0) $c += $earned->first()->credits ?>
                                  @endforeach

                                <td>{{ number_format($c,2) }}</td>

                                <?php $c=0; ?>
                              @endforeach
                              
                           
                              <td>action</td>
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
                      "order": [ 0, "asc" ],
                      "lengthMenu": [20, 100, 500],
                      "dom": '<"col-xs-1"f><"col-xs-11 text-right"l><"clearfix">rt<"bottom"ip><"clear">',
                      "scrollX":true,
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