@extends('layouts.main')

@section('metatags')
@if ($user->id == Auth::user()->id) 
         <title>My Requests | Employee Management System</title>
       @else 
          @if(empty($user->nickname))<title>{{$user->firstname}}'s Requests | Employee Management System</title>  
          @else <title>{{$user->nickname}}'s Requests | Employee Management System</title> 
          @endif
      @endif


@endsection

@section('content')


  <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
       <i class="fa fa-clipboard"></i> 
       @if ($user->id == Auth::user()->id) 
          My Requests 
       @else 
          @if(empty($user->nickname)) {{$user->firstname}}'s Requests 
          @else {{$user->nickname}}'s Requests 
          @endif
      @endif
      </h1>
     
      <ol class="breadcrumb">
        <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">My Requests</li>
      </ol>
    </section>

     <section class="content">

      <div class="row">
        <div class="col-lg-12" >
           @if ($user->id == Auth::user()->id) 

          <span style="text-align: left; font-size:12px"><em>Click on the buttons below to file new requests:</em></span><br/><br/>
          <p class="text-center">
            <strong>
              <a href="{{action('UserSLController@create')}}" class="btn btn-sm btn-danger"><i class="fa fa-2x fa-stethoscope"></i> New Sick Leave <br/><strong>(SL)</strong></a>

              <a class="btn btn-sm bg-blue" href="{{action('UserVLController@create')}}"><i class="fa fa-2x fa-plane"></i> New Vacation Leave <br/>
                <strong>(VL)</strong></a>

              <a href="{{action('UserLWOPController@create')}}" class="btn btn-sm bg-orange"><i class="fa fa-meh-o fa-2x"></i> New Leave Without Pay <br/> <strong>(LWOP)</strong></a>

              <a href="{{action('UserOTController@create',['for'=>$user->id])}}" class="btn btn-sm" style="background-color: #ffea7f"><i class="fa fa-clock-o fa-2x"></i> New Meeting/Huddle <br/> <strong>(PS-OT)</strong></a> 

              <a href="{{action('UserController@show',$user->id)}}#ws" class="btn btn-sm bg-green"><i class="fa fa-2x fa-calendar-times-o"></i> Change Work Schedule  <br/><strong>(CWS)</strong></a>

              <a  href="{{action('UserOBTController@create')}}" class="btn btn-sm bg-purple"><i class="fa fa-2x fa-briefcase"></i> Official Business Trip <br/> <strong>(OBT)</strong></a></strong>
              <br/><br/>

              <a  href="{{action('UserFamilyleaveController@create',['for'=>$user->id,'type'=>'ML'])}}" class="btn btn-sm btn-default"><i class="fa fa-2x fa-female"></i> Maternity Leave  <br/><strong>(ML)</strong></a>

              <a  href="{{action('UserFamilyleaveController@create',['for'=>$user->id,'type'=>'PL'])}}" class="btn btn-sm btn-default"><i class="fa fa-2x fa-male"></i> Paternity Leave  <br/><strong>(PL)</strong></a>

              <a  href="{{action('UserFamilyleaveController@create',['for'=>$user->id,'type'=>'SPL'])}}" class="btn btn-sm btn-default"><i class="fa fa-2x fa-street-view"></i> Single-Parent Leave  <br/><strong>(SPL)</strong></a>

             <br/><br/><br/>
           </p>
         
           @elseif ($forOthers)
           <span style="text-align: left; font-size:12px"><em>Click on the buttons below to file new requests for <a href="{{action('UserController@show',$user->id)}}"> <strong>{{$user->firstname}} {{$user->lastname}}</strong></a> :</em></span><br/><br/>
          <p class="text-center">
            <strong>

              <a href="{{action('UserSLController@create',['for'=>$user->id])}}" class="btn btn-sm btn-danger"><i class="fa fa-2x fa-stethoscope"></i> New Sick Leave <br/> <strong>(SL)</strong></a>

              <a class="btn btn-sm bg-blue" href="{{action('UserVLController@create',['for'=>$user->id])}}"><i class="fa fa-2x fa-plane"></i> New Vacation Leave <br/><strong>(VL)</strong></a>

              <a href="{{action('UserLWOPController@create',['for'=>$user->id])}}" class="btn btn-sm bg-orange"><i class="fa fa-meh-o fa-2x"></i> New Leave Without Pay  <br/><strong>(LWOP)</strong></a>

              <a href="{{action('UserOTController@create',['for'=>$user->id])}}" class="btn btn-sm" style="background-color: #ffea7f"><i class="fa fa-clock-o fa-2x"></i> New Meeting/Huddle <br/> <strong>(PS-OT)</strong></a> 

              <a href="{{action('UserController@show',$user->id)}}#ws" class="btn btn-sm bg-green"><i class="fa fa-2x fa-calendar-times-o"></i> Change Work Schedule  <br/><strong>(CWS)</strong></a>

              <a  href="{{action('UserOBTController@create',['for'=>$user->id])}}" class="btn btn-sm bg-purple"><i class="fa fa-2x fa-briefcase"></i> Official Business Trip  <br/><strong>(OBT)</strong></a><br/><br/>

              <a  href="{{action('UserFamilyleaveController@create',['for'=>$user->id,'type'=>'ML'])}}" class="btn btn-sm btn-default"><i class="fa fa-2x fa-female"></i> Maternity Leave  <br/><strong>(ML)</strong></a>

              <a  href="{{action('UserFamilyleaveController@create',['for'=>$user->id,'type'=>'PL'])}}" class="btn btn-sm btn-default"><i class="fa fa-2x fa-male"></i> Paternity Leave  <br/><strong>(PL)</strong></a>

              <a  href="{{action('UserFamilyleaveController@create',['for'=>$user->id,'type'=>'SPL'])}}" class="btn btn-sm btn-default"><i class="fa fa-2x fa-street-view"></i> Single-Parent Leave  <br/><strong>(SPL)</strong></a>
            </strong>
           </p><br/>
           
         
           @endif




          

        </div>
      </div>



                 <!-- ******** THE DATATABLE ********** -->
          <div class="row">
             <div class="col-lg-1 col-sm-4  col-xs-9">
              </div>
              <div class="col-lg-10 col-sm-4 col-xs-12" ><!--style="background-color:#fff"-->
                <a class="btn btn-xs btn-default btn-flat pull-left" href="{{action('DTRController@show',$user->id)}}"><i class="fa fa-calendar"></i>@if(empty($user->firstname)) {{$user->firstname}}'s @else {{$user->nickname}}'s  @endif DTR Sheet</a> <br/><br/>
                <table class="table no-margin table-bordered table-striped" id="requests" style="background: rgba(256, 256, 256, 0.3)" ></table>

                        <br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>
                            
              </div> 
              <div class="col-lg-1 col-sm-4  col-xs-9">
              </div>
              <div class="holder"></div>


              <div class="clearfix"></div>
          </div>


          <div class="modal fade" id="myModal'+data_id+'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                  </button>
                  <h4 class="modal-title" id="myModalLabel"> Delete '+requesttype+'</h4>
                </div>
                <div class="modal-body">
                  <br/><br/>Are you sure you want to delete this request?<br/>
                </div>
                <div class="modal-footer no-border">
                  <form action="./user_cws/deleteThisCWS/'+data_id+'" method="POST" class="btn-outline pull-right" id="deleteReq">
                  <input type="hidden" name="notifType" value="'+full.typeid+'" />
                  <button type="submit" class="btn btn-primary glyphicon-trash glyphicon ">Yes</button>
                  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                  <input type="hidden" name="_token" value="'+_token+'" /> 
                </div>
              </div>
            </div>
          </div>
               

     
         




       
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
   
        $("#requests").DataTable({

          @if (is_null($user)) "ajax": "{{ action('UserController@getMyRequests') }}",

          @else  "ajax": "{{ action('UserController@getMyRequests',['id'=>$user->id])}}",
          @endif

            "processing":true,
            "stateSave": false,
            "lengthMenu": [10, 50, 100],//[5, 20, 50, -1],
            "columns": [
                 
                  { title: "Type", defaultContent: "<i>none</i>" , data:'type', width:'180', render:function( data, type, full, meta ){return '<i class="fa '+full.icon+'"></i>&nbsp;&nbsp; '+data;}}, // width:'180'},  
                  { title: "Production Date", defaultContent: " ", width:'140',data:'productionDate',render:function(data){
                        //return moment(data,"M d, Y").format('MMM DD, YYYY - ddd')
                        return data
                    } 
                  }, //,width:'180'}, // 1YYYY-MM-DD hh:mm:ss
                  { title: "Updated", defaultContent: " ", data:'details.updated_at',width:'190',render:function(data){return moment(data,"YYYY-MM-DD hh:mm:ss").format('YYYY-MM-DD - ddd HH:mm A')} }, //,width:'180'}, // 1
                  { title: "Status", defaultContent: " ", data:'details.isApproved', width:'100', render:function(data,type,full,meta){
                    if (full.details.isApproved == '1') return '<strong class="label bg-aqua"><i class="fa fa-thumbs-up"></i>&nbsp; Approved</strong>';
                    else if  (full.details.isApproved == '0') return '<span class="label bg-gray"><i class="fa fa-thumbs-down"></i>&nbsp; Denied</span>';
                    else return '<span class="label bg-info text-black"><i class="fa fa-exclamation-circle"></i>&nbsp; Pending Approval</span>';
                  }}, 
                  { title: "Actions",defaultContent:" ", data: "details.id", class:'text-center', sorting:false, 
                    render: function ( data, type, full, meta )
                    {

                      var _token = "{{ csrf_token() }}";
                      var data_id = full.details.id;
                      var user_id = full.details.user_id;
                      var nickname = full.nickname;
                      var formattedDate = full.details.created_at;
                      var requesttype = full.type;
                      var typeid =full.typeid;
                      var productiondate = full.productionDate;
                       var mc1 ="";
                       var icon = full.icon;

                      @if ( file_exists('public/img/employees/'.Auth::user()->id.'.jpg') )
                      var userimg= "../public/img/employees/"+user_id+".jpg";
                      @else
                      var userimg= "{{asset('public/img/useravatar.png')}}";
                      @endif

                    

                      var modalcode = '<div class="modal fade" id="myModal_DTRP'+data_id+'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button><h4 class="modal-title text-black" id="myModalLabel"><i class="fa '+icon+'"></i> '+requesttype+'</h4></div> <div class="modal-body-upload" style="padding:20px;">';

                        modalcode += '<!-- DIRECT CHAT PRIMARY -->';
                        modalcode += '<div class="box box-primary direct-chat direct-chat-primary">';

                        modalcode += '  <div class="box-body">';
                        modalcode += '    <!-- Conversations are loaded here -->';
                        modalcode += '    <div class="direct-chat-messages">';
                        modalcode += '      <!-- Message. Default to the left -->';
                        modalcode += '      <div class="direct-chat-msg">';
                        modalcode += '        <div class="direct-chat-info clearfix">';
                        modalcode += '          <span class="direct-chat-name pull-left">'+nickname+'</span>';
                        modalcode += '          <span class="direct-chat-timestamp pull-right">'+formattedDate+'</span>';
                        modalcode += '        </div>';
                        modalcode += '        <!-- /.direct-chat-info -->';
                        modalcode += '        <a href="../user/'+user_id+'" target="_blank"><img src="'+userimg+'" class="img-circle pull-left" alt="User Image" width="70" /></a>';
                        modalcode += '        <div class="direct-chat-text" style="width:85%; left:30px; background-color:#fcfdfd">';

                        switch (typeid)
                        {
                            //CWS
                            case '6': { 

                                        var deleteLink = "../user_cws/deleteThisCWS/"+data_id;
                                        var timeStart = full.details.timeStart;
                                        var timeEnd = full.details.timeEnd;
                                        var timeStart_old = full.details.timeStart_old;
                                        var timeEnd_old = full.details.timeEnd_old;
                                        var notes = full.details.notes;

                                       var shiftStart_new = new Date(full.productionDate+ " "+timeStart).toLocaleString('en-US', { hour: 'numeric', minute:'numeric', hour12: true });
                                       var shiftEnd_new = new Date(full.productionDate+ " "+timeEnd).toLocaleString('en-US', { hour: 'numeric', minute:'numeric',hour12: true });
                                       modalcode += '<p class="text-left">';
                                        modalcode += 'I would like to request a <strong>CHANGE OF WORK SCHEDULE</strong>.<br/> <br/><strong>Reason: </strong><em>'+notes+'</em></p>';
                                        modalcode += '<div class="row"><div class="col-sm-12"> <div class="row"><div class="col-sm-4"><h5 class="text-primary">Production Date</h5><p style="font-weight:bold"><small>'+full.productionDate+'<br/> ['+full.productionDay+']</small></p></div>';
                                        modalcode += '<div class="col-sm-4"><h5 class="text-primary">Old Schedule:</h5></div><div class="col-sm-4"><h5 class="text-primary">New Schedule</h5></div>';
                                        var shiftStart = new Date(full.productionDate+ " "+timeStart_old).toLocaleString('en-US', { hour: 'numeric', minute:'numeric', hour12: true });
                                        var shiftEnd = new Date(full.productionDate+ " "+timeEnd_old).toLocaleString('en-US', { hour: 'numeric', minute:'numeric',hour12: true });

                                        mc1 += '<div class="col-sm-4" style="font-size: 12px">';

                                        if(timeStart_old == "00:00:00" && timeEnd_old== "00:00:00")
                                          mc1 += '<p>Shift: <br/><strong>Rest Day </strong></p>';
                                        else
                                          mc1 += '<p>Shift: <br/><strong>'+shiftStart+'  -  '+shiftEnd+' </strong></p>';

                                        mc1 += '</div><div class="col-sm-4" style="font-size: 12px">';

                                        if(timeStart == "00:00:00" && timeEnd=="00:00:00")
                                          mc1 += '<p>Shift: <br/><strong>Rest Day </strong></p></div>';
                                        else
                                          mc1 += '<p>Shift: <br/><strong>'+shiftStart_new+' -  '+shiftEnd_new+'</strong></p></div>';

                                      //mc1 += '<div class="row"><div class="col-sm-12">'+full.deets.notes+'</div></div>';
                                  } break;

                            case '7': {  /*------ OVERTIME --------*/
                                            var deleteLink = "../user_ot/deleteOT/"+data_id;
                                            var shiftStart_new = new Date(full.productionDate+ " "+full.details.timeStart).toLocaleString('en-US', { hour: 'numeric', minute:'numeric', hour12: true });
                                            var shiftEnd_new = new Date(full.productionDate+ " "+full.details.timeEnd).toLocaleString('en-US', { hour: 'numeric', minute:'numeric',hour12: true });

                                            modalcode += '<p class="text-left">';
                                            modalcode += 'I would like to file an <strong>OT </strong> for <strong>'+full.productionDate+'</strong></p>';
                                            modalcode += '<div class="row">';

                                            modalcode +='<div class="col-sm-6" style="font-size: 12px"><h5 class="text-primary">OT Details:</h5>';
                                            modalcode +=' <p class="text-left"><strong>Start: </strong>'+full.details.timeStart;
                                            modalcode +='<br/><strong>End : </strong>'+full.details.timeEnd;
                                            modalcode += '<br/><strong>Billable Hours: </strong>'+full.details.billable_hours;
                                            modalcode += '<br/><strong>Filed Hours worked: </strong>'+full.details.filed_hours;
                                            modalcode += '</p></div> <div class="col-sm-5" style="font-size: 12px"><h5 class="text-primary">Reason:</h5>';
                                            modalcode += '<p class="text-left"><em>'+full.details.reason+'</em></p> </div>';
                                        };break;


                            case '8': {  /*------- DTRP IN --------*/
                                          var deleteLink = "../user_dtrp/deleteThisDTRP/"+data_id;
                                          var shiftStart_new = new Date(full.productionDate+ " "+full.details.timeStart).toLocaleString('en-US', { hour: 'numeric', minute:'numeric', hour12: true });
                                          var shiftEnd_new = new Date(full.productionDate+ " "+full.details.timeEnd).toLocaleString('en-US', { hour: 'numeric', minute:'numeric',hour12: true });

                                          modalcode += '<p class="text-left"> ';
                                          modalcode += 'I would like to file a <strong>DTRP IN</strong>. See details below:</p>';
                                          modalcode += '<div class="row"><div class="col-sm-12"> <div class="row"><div class="col-sm-4" style="font-size: 12px"><h5 class="text-primary">Production Date</h5><p style="font-weight:bold">'+full.productionDate+'</p></div>';
                                          modalcode += '<div class="col-sm-4" style="font-size: 12px"><h5 class="text-primary">Log IN Time</h5>';
                                                  modalcode += '<p><br/><strong>'+full.details.logTime +'</strong></p></div><div class="col-sm-4"><h5 class="text-primary">Notes</h5>';

                                                  modalcode += '<p><br/><em>'+full.details.notes+'</em></p></div>';
                                        };break;


                            case '9': { /*------- DTRP out --------*/
                                         var deleteLink = "../user_dtrp/deleteThisDTRP/"+data_id;
                                         var shiftStart_new = new Date(full.productionDate+ " "+full.details.timeStart).toLocaleString('en-US', { hour: 'numeric', minute:'numeric', hour12: true });
                                         var shiftEnd_new = new Date(full.productionDate+ " "+full.details.timeEnd).toLocaleString('en-US', { hour: 'numeric', minute:'numeric',hour12: true });

                                        modalcode += '<p class="text-left">';
                                        modalcode += 'I would like to file a <strong>DTRP OUT</strong>. See details below:</p>';
                                        modalcode += '<div class="row"><div class="col-sm-12"> <div class="row"><div class="col-sm-4" style="font-size: 12px"><h5 class="text-primary">Production Date</h5><p style="font-weight:bold">'+full.productionDate+'<br/> ['+full.productionDay+']</p></div>';

                                        modalcode += '<div class="col-sm-4 style="font-size: 12px""><h5 class="text-primary">Log OUT Time</h5>';
                                                modalcode += '<p><br/><strong>'+full.details.logTime +'</strong></p></div><div class="col-sm-4"><h5 class="text-primary">Notes</h5>';
                                                modalcode += '<p><br/><em>'+full.details.notes+'</em></p></div>';
                                        };break;

                             case '10': { /*------- VACATION LEAVE --------*/

                                          var deleteLink = "../user_vl/deleteThisVL/"+data_id;
                                          var leaveStart = moment(full.details.leaveStart); //,"MM/D/YYYY h:m A");
                                          var leaveEnd = moment(full.details.leaveEnd); //,"MM/D/YYYY h:m A");
                                          var duration = moment.duration(leaveEnd.diff(leaveStart));
                                          var hours = duration.asHours();
                                          var halfFrom = full.details.halfdayFrom;
                                          var halfTo = full.details.halfdayTo;

                                           if (full.details.totalCredits % 1 === 0) var totalcreds = Math.floor(full.details.totalCredits);
                                          else{
                                                if(full.details.totalCredits == '0.50') var totalcreds = "half";
                                                  else var totalcreds = full.details.totalCredits;

                                          } 

                                          modalcode += '<p class="text-left"><br/>';
                                          modalcode += 'I would like to file a <strong class="text-danger">'+totalcreds+'-day </strong><strong>VACATION LEAVE &nbsp;&nbsp;</strong> <br/><br/>';
                                         

                                         // modalcode += '<strong>VL credits used: </strong><span class="text-danger">'+full.deets.totalCredits+'</span><br/>';
                                          modalcode += '<strong>Reason: </strong><em>'+full.details.notes+'</em></p>';
                                          modalcode += '<div class="row"><div class="col-sm-12"> <div class="row">';
                                          modalcode += '<div class="col-sm-6"><h5 class="text-primary">From: </h5></div><div class="col-sm-6"><h5 class="text-primary">Until: </h5></div>';

                                          mc1 += '<div class="col-sm-6" style="font-size: 12px">';

                                          if (halfFrom == '1' && halfTo == '1')
                                          {
                                            mc1 += '<p><strong>'+leaveStart.format("MMM DD, ddd")+' (Whole day) </strong></p>';

                                          } else {
                                            mc1 += '<p><strong>'+leaveStart.format("MMM DD, ddd hh:mm A")+' </strong></p>';
                                          }
                                          
                                          mc1 += '</div><div class="col-sm-6" style="font-size: 12px">';

                                          // if (halfTo == '1')
                                          //   mc1 += '<p><strong>'+leaveEnd.format("MMM DD, YYYY")+' (Whole day) </strong></p></div>';
                                          // else
                                            mc1 += '<p><strong>'+leaveEnd.format("MMM DD, ddd hh:mm A")+'</strong></p></div>';

                                        } break;


                             case '11': { /*------- SICK LEAVE --------*/

                                          var deleteLink = "../user_sl/deleteThisSL/"+data_id;
                                          var leaveStart = moment(full.details.leaveStart); //,"MM/D/YYYY h:m A");
                                          var leaveEnd = moment(full.details.leaveEnd); //,"MM/D/YYYY h:m A");
                                          var duration = moment.duration(leaveEnd.diff(leaveStart));
                                          var hours = duration.asHours();
                                          var halfFrom = full.details.halfdayFrom;
                                          var halfTo = full.details.halfdayTo;

                                           if (full.details.totalCredits % 1 === 0) var totalcreds = Math.floor(full.details.totalCredits);
                                          else{
                                                if(full.details.totalCredits == '0.50') var totalcreds = "half";
                                                  else var totalcreds = full.details.totalCredits;

                                          } 

                                          modalcode += '<p class="text-left"><br/>';
                                          modalcode += 'I would like to file a <strong class="text-danger">'+totalcreds+'-day </strong><strong>SICK LEAVE &nbsp;&nbsp;</strong>' ;  
                                          if (full.details.attachments != null)
                                          modalcode += '<span class="pull-right" style="font-size:smaller"><i class="fa fa-paperclip"></i> <a href="../user_sl/medCert/'+full.details.id+'" target="_blank">Medical Certificate <br/> &nbsp; &nbsp; &nbsp; &nbsp;attached</a></span>';
                                         
                                          modalcode += ' <br/><br/><strong>Reason: </strong><em>'+full.details.notes+'</em></p>';
                                          
                                          modalcode += '<div class="row"><div class="col-sm-12"> <div class="row">';
                                          modalcode += '<div class="col-sm-6"><h5 class="text-primary">From: </h5></div><div class="col-sm-6"><h5 class="text-primary">Until: </h5></div>';

                                          mc1 += '<div class="col-sm-6" style="font-size: 12px">';

                                          if (halfFrom == '1' && halfTo == '1')
                                          {
                                            mc1 += '<p><strong>'+leaveStart.format("MMM DD, ddd")+' (Whole day) </strong></p>';

                                          } else {
                                            mc1 += '<p><strong>'+leaveStart.format("MMM DD, ddd hh:mm A")+' </strong></p>';
                                          }
                                          
                                          mc1 += '</div><div class="col-sm-6" style="font-size: 12px">';

                                          // if (halfTo == '1')
                                          //   mc1 += '<p><strong>'+leaveEnd.format("MMM DD, YYYY")+' (Whole day) </strong></p></div>';
                                          // else
                                            mc1 += '<p><strong>'+leaveEnd.format("MMM DD, ddd hh:mm A")+'</strong></p></div>';
                                           

                                        } break;

                             case '12': { /*------- lwop LEAVE --------*/

                                          var deleteLink = "../user_lwop/deleteThisLWOP/"+data_id;
                                          var leaveStart = moment(full.details.leaveStart); //,"MM/D/YYYY h:m A");
                                          var leaveEnd = moment(full.details.leaveEnd); //,"MM/D/YYYY h:m A");
                                          var duration = moment.duration(leaveEnd.diff(leaveStart));
                                          var hours = duration.asHours();
                                          var halfFrom = full.details.halfdayFrom;
                                          var halfTo = full.details.halfdayTo;

                                           if (full.details.totalCredits % 1 === 0) var totalcreds = Math.floor(full.details.totalCredits);
                                          else{
                                                if(full.details.totalCredits == '0.50') var totalcreds = "half";
                                                  else var totalcreds = full.details.totalCredits;

                                          } 

                                          modalcode += '<p class="text-left"><br/>';
                                          modalcode += 'I would like to file a <strong class="text-danger">'+totalcreds+'-day </strong><strong>LEAVE WITHOUT PAY &nbsp;&nbsp;</strong> <br/><br/>';

                                          modalcode += '<strong>Reason: </strong><em>'+full.details.notes+'</em></p>';
                                          modalcode += '<div class="row"><div class="col-sm-12"> <div class="row">';
                                          modalcode += '<div class="col-sm-6"><h5 class="text-primary">From: </h5></div><div class="col-sm-6"><h5 class="text-primary">Until: </h5></div>';

                                          mc1 += '<div class="col-sm-6" style="font-size: 12px">';

                                          if (halfFrom == '1' && halfTo == '1')
                                          {
                                            mc1 += '<p><strong>'+leaveStart.format("MMM DD, ddd")+' (Whole day) </strong></p>';

                                          } else {
                                            mc1 += '<p><strong>'+leaveStart.format("MMM DD, ddd hh:mm A")+' </strong></p>';
                                          }
                                          
                                          mc1 += '</div><div class="col-sm-6" style="font-size: 12px">';

                                          mc1 += '<p><strong>'+leaveEnd.format("MMM DD, ddd hh:mm A")+'</strong></p></div>';

                                        } break;


                            case '13': { /*------- OBT LEAVE --------*/

                                          var deleteLink = "../user_obt/deleteThisOBT/"+data_id;
                                          var leaveStart = moment(full.details.leaveStart); //,"MM/D/YYYY h:m A");
                                          var leaveEnd = moment(full.details.leaveEnd); //,"MM/D/YYYY h:m A");
                                          var duration = moment.duration(leaveEnd.diff(leaveStart));
                                          var hours = duration.asHours();
                                          var halfFrom = full.details.halfdayFrom;
                                          var halfTo = full.details.halfdayTo;

                                           if (full.details.totalCredits % 1 === 0) var totalcreds = Math.floor(full.details.totalCredits);
                                          else{
                                                if(full.details.totalCredits == '0.50') var totalcreds = "half";
                                                  else var totalcreds = full.details.totalCredits;

                                          } 

                                          modalcode += '<p class="text-left"><br/>';
                                          modalcode += 'I would like to file a <strong class="text-danger">'+totalcreds+'-day </strong><strong>OFFICIAL BUSINESS TRIP &nbsp;&nbsp;</strong> <br/><br/>';
                                          //html = $.parseHTML( );
                                          modalcode += '<strong>Reason: </strong><em>'+full.details.notes+'</em></p>';
                                          modalcode += '<div class="row"><div class="col-sm-12"> <div class="row">';
                                          modalcode += '<div class="col-sm-6"><h5 class="text-primary">From: </h5></div><div class="col-sm-6"><h5 class="text-primary">Until: </h5></div>';

                                          mc1 += '<div class="col-sm-6" style="font-size: 12px">';

                                          if (halfFrom == '1' && halfTo == '1')
                                          {
                                            mc1 += '<p><strong>'+leaveStart.format("MMM DD, ddd")+' (Whole day) </strong></p>';

                                          } else {
                                            mc1 += '<p><strong>'+leaveStart.format("MMM DD, ddd hh:mm A")+' </strong></p>';
                                          }
                                          
                                          mc1 += '</div><div class="col-sm-6" style="font-size: 12px">';

                                          mc1 += '<p><strong>'+leaveEnd.format("MMM DD, ddd hh:mm A")+'</strong></p></div>';

                                        } break;

                            case '15': {  /*------ PRE-SHIFT OVERTIME --------*/

                                          var billedType=" ";

                                            if(full.billedType == '1') billedType="Billed";
                                            else if (full.billedType == '2') billedType="Non-Billed";
                                            else if (full.billedType == '3') billedType="Patch";
                                            else billedType="Billed";

                                          var deleteLink = "../user_ot/deleteOT/"+data_id;
                                          var shiftStart_new = new Date(full.productionDate+ " "+full.details.timeStart).toLocaleString('en-US', { hour: 'numeric', minute:'numeric', hour12: true });
                                          var shiftEnd_new = new Date(full.productionDate+ " "+full.details.timeEnd).toLocaleString('en-US', { hour: 'numeric', minute:'numeric',hour12: true });

                                          modalcode += '<p class="text-left">';
                                          modalcode += 'I would like to file a <strong>Meeting/Huddle </strong> for <strong>'+full.productionDate+'</strong></p>';
                                          modalcode += '<div class="row">';

                                          modalcode +='<div class="col-sm-6" style="font-size: 12px"><h5 class="text-primary">OT Details:</h5>';
                                          modalcode +=' <p class="text-left"><strong>Start: </strong>'+full.details.timeStart;
                                          modalcode +='<br/><strong>End : </strong>'+full.details.timeEnd;
                                          modalcode += '<br/><strong>Billable Hours: </strong>'+full.details.billable_hours;
                                          modalcode += '<br/><strong>Filed Hours worked: </strong>'+full.details.filed_hours;
                                          modalcode += '<br/><strong>Type: </strong><span class="text-danger" style="font-size:larger">'+billedType;
                                          modalcode += '</p></div> <div class="col-sm-5" style="font-size: 12px"><h5 class="text-primary">Reason:</h5>';
                                          modalcode += '<p class="text-left"><em>'+full.details.reason+'</em></p> </div>';
                                      };break;


                            case '16': { /*------- Maternity LEAVE --------*/

                                          var deleteLink = "../user_fl/deleteThisSL/"+data_id;
                                          var leaveStart = moment(full.details.leaveStart); //,"MM/D/YYYY h:m A");
                                          var leaveEnd = moment(full.details.leaveEnd); //,"MM/D/YYYY h:m A");
                                          var duration = moment.duration(leaveEnd.diff(leaveStart));
                                          var hours = duration.asHours();
                                          var halfFrom = full.details.halfdayFrom;
                                          var halfTo = full.details.halfdayTo;

                                           if (full.details.totalCredits % 1 === 0) var totalcreds = Math.floor(full.details.totalCredits);
                                          else{
                                                if(full.details.totalCredits == '0.50') var totalcreds = "half";
                                                  else var totalcreds = full.details.totalCredits;

                                          } 

                                          modalcode += '<p class="text-left"><br/>';
                                          modalcode += 'I would like to file a <strong class="text-danger">'+totalcreds+'-day </strong><strong>MATERNITY LEAVE &nbsp;&nbsp;</strong>' ;  
                                          if (full.details.attachments != null)
                                          modalcode += '<span class="pull-right" style="font-size:smaller"><i class="fa fa-paperclip"></i> <a href="../user_fl/requirements/'+full.details.id+'" target="_blank">Requirements <br/> &nbsp; &nbsp; &nbsp; &nbsp;attached</a></span>';
                                         
                                          modalcode += ' <br/><br/><strong>Reason: </strong><em>'+full.details.notes+'</em></p>';
                                          
                                          modalcode += '<div class="row"><div class="col-sm-12"> <div class="row">';
                                          modalcode += '<div class="col-sm-6"><h5 class="text-primary">From: </h5></div><div class="col-sm-6"><h5 class="text-primary">Until: </h5></div>';

                                          mc1 += '<div class="col-sm-6" style="font-size: 12px">';

                                          if (halfFrom == '1' && halfTo == '1')
                                          {
                                            mc1 += '<p><strong>'+leaveStart.format("MMM DD, ddd")+' (Whole day) </strong></p>';

                                          } else {
                                            mc1 += '<p><strong>'+leaveStart.format("MMM DD, ddd hh:mm A")+' </strong></p>';
                                          }
                                          
                                          mc1 += '</div><div class="col-sm-6" style="font-size: 12px">';

                                          // if (halfTo == '1')
                                          //   mc1 += '<p><strong>'+leaveEnd.format("MMM DD, YYYY")+' (Whole day) </strong></p></div>';
                                          // else
                                            mc1 += '<p><strong>'+leaveEnd.format("MMM DD, ddd hh:mm A")+'</strong></p></div>';
                                           

                                        } break;
                            case '17': { /*------- Maternity LEAVE --------*/

                                          var deleteLink = "../user_fl/deleteThisSL/"+data_id;
                                          var leaveStart = moment(full.details.leaveStart); //,"MM/D/YYYY h:m A");
                                          var leaveEnd = moment(full.details.leaveEnd); //,"MM/D/YYYY h:m A");
                                          var duration = moment.duration(leaveEnd.diff(leaveStart));
                                          var hours = duration.asHours();
                                          var halfFrom = full.details.halfdayFrom;
                                          var halfTo = full.details.halfdayTo;

                                           if (full.details.totalCredits % 1 === 0) var totalcreds = Math.floor(full.details.totalCredits);
                                          else{
                                                if(full.details.totalCredits == '0.50') var totalcreds = "half";
                                                  else var totalcreds = full.details.totalCredits;

                                          } 

                                          modalcode += '<p class="text-left"><br/>';
                                          modalcode += 'I would like to file a <strong class="text-danger">'+totalcreds+'-day </strong><strong>PATERNITY LEAVE &nbsp;&nbsp;</strong>' ;  
                                          if (full.details.attachments != null)
                                          modalcode += '<span class="pull-right" style="font-size:smaller"><i class="fa fa-paperclip"></i> <a href="../user_fl/requirements/'+full.details.id+'" target="_blank">Requirements <br/> &nbsp; &nbsp; &nbsp; &nbsp;attached</a></span>';
                                         
                                          modalcode += ' <br/><br/><strong>Reason: </strong><em>'+full.details.notes+'</em></p>';
                                          
                                          modalcode += '<div class="row"><div class="col-sm-12"> <div class="row">';
                                          modalcode += '<div class="col-sm-6"><h5 class="text-primary">From: </h5></div><div class="col-sm-6"><h5 class="text-primary">Until: </h5></div>';

                                          mc1 += '<div class="col-sm-6" style="font-size: 12px">';

                                          if (halfFrom == '1' && halfTo == '1')
                                          {
                                            mc1 += '<p><strong>'+leaveStart.format("MMM DD, ddd")+' (Whole day) </strong></p>';

                                          } else {
                                            mc1 += '<p><strong>'+leaveStart.format("MMM DD, ddd hh:mm A")+' </strong></p>';
                                          }
                                          
                                          mc1 += '</div><div class="col-sm-6" style="font-size: 12px">';

                                          // if (halfTo == '1')
                                          //   mc1 += '<p><strong>'+leaveEnd.format("MMM DD, YYYY")+' (Whole day) </strong></p></div>';
                                          // else
                                            mc1 += '<p><strong>'+leaveEnd.format("MMM DD, ddd hh:mm A")+'</strong></p></div>';
                                           

                                        } break;

                            case '18': { /*------- Maternity LEAVE --------*/

                                          var deleteLink = "../user_fl/deleteThisSL/"+data_id;
                                          var leaveStart = moment(full.details.leaveStart); //,"MM/D/YYYY h:m A");
                                          var leaveEnd = moment(full.details.leaveEnd); //,"MM/D/YYYY h:m A");
                                          var duration = moment.duration(leaveEnd.diff(leaveStart));
                                          var hours = duration.asHours();
                                          var halfFrom = full.details.halfdayFrom;
                                          var halfTo = full.details.halfdayTo;

                                           if (full.details.totalCredits % 1 === 0) var totalcreds = Math.floor(full.details.totalCredits);
                                          else{
                                                if(full.details.totalCredits == '0.50') var totalcreds = "half";
                                                  else var totalcreds = full.details.totalCredits;

                                          } 

                                          modalcode += '<p class="text-left"><br/>';
                                          modalcode += 'I would like to file a <strong class="text-danger">'+totalcreds+'-day </strong><strong>SINGLE-PARENT LEAVE &nbsp;&nbsp;</strong>' ;  
                                          if (full.details.attachments != null)
                                          modalcode += '<span class="pull-right" style="font-size:smaller"><i class="fa fa-paperclip"></i> <a href="../user_fl/requirements/'+full.details.id+'" target="_blank">Requirements <br/> &nbsp; &nbsp; &nbsp; &nbsp;attached</a></span>';
                                         
                                          modalcode += ' <br/><br/><strong>Reason: </strong><em>'+full.details.notes+'</em></p>';
                                          
                                          modalcode += '<div class="row"><div class="col-sm-12"> <div class="row">';
                                          modalcode += '<div class="col-sm-6"><h5 class="text-primary">From: </h5></div><div class="col-sm-6"><h5 class="text-primary">Until: </h5></div>';

                                          mc1 += '<div class="col-sm-6" style="font-size: 12px">';

                                          if (halfFrom == '1' && halfTo == '1')
                                          {
                                            mc1 += '<p><strong>'+leaveStart.format("MMM DD, ddd")+' (Whole day) </strong></p>';

                                          } else {
                                            mc1 += '<p><strong>'+leaveStart.format("MMM DD, ddd hh:mm A")+' </strong></p>';
                                          }
                                          
                                          mc1 += '</div><div class="col-sm-6" style="font-size: 12px">';

                                          // if (halfTo == '1')
                                          //   mc1 += '<p><strong>'+leaveEnd.format("MMM DD, YYYY")+' (Whole day) </strong></p></div>';
                                          // else
                                            mc1 += '<p><strong>'+leaveEnd.format("MMM DD, ddd hh:mm A")+'</strong></p></div>';
                                           

                                        } break;

                                      
                         }

                                    
                        modalcode += mc1;
                        modalcode += '<div class="col-sm-3"> </div></div></div></div>    </div>';

                        modalcode += '        </div>';
                        modalcode += '        <!-- /.direct-chat-text -->';
                        modalcode += '      </div>';
                        modalcode += '      <!-- /.direct-chat-msg -->';


                        if(full.details.isApproved !== null)
                        {
                          modalcode += '      <!-- Message to the right -->';
                          modalcode += '      <div class="direct-chat-msg right" style="margin-top:50px">';
                          modalcode += '        <div class="direct-chat-default clearfix">';

                          if(full.approver !== null)
                          {
                            if(full.approver.nickname == null)
                              modalcode += '          <span class="direct-chat-name pull-right">'+full.approver.firstname+'</span>';
                              else
                              modalcode += '          <span class="direct-chat-name pull-right">'+full.approver.nickname+'</span>';

                          }

                              
                          modalcode += '          <span class="direct-chat-timestamp pull-left">'+full.details.updated_at+' </span>';
                          modalcode += '        </div>';
                          modalcode += '        <!-- /.direct-chat-info -->';
                          modalcode += '        <img class="direct-chat-img" src="'+full.tlPic+'" alt="Message User Image"><!-- /.direct-chat-img -->';

                          if(full.details.isApproved)
                          {
                            modalcode += '        <div class="direct-chat-text bg-green" >'; 
                            modalcode += '<h5><i class="fa fa-thumbs-up"></i> Approved</h5></div>';

                          } else if(full.details.isApproved == 0){
                            modalcode += '        <div class="direct-chat-text bg-red" >'; 
                            modalcode += '<h5><i class="fa fa-thumbs-down"></i> Denied</h5></div>';
                          }
                          
                          
                          modalcode += '        </div>';
                          modalcode += '        <!-- /.direct-chat-text -->';
                          modalcode += '      </div>';
                          modalcode += '      <!-- /.direct-chat-msg -->';
                          modalcode += '    </div>';
                          modalcode += '    <!--/.direct-chat-messages-->';

                          modalcode += '    </div>';
                          modalcode += '    <!--/.direct-chat-messages-->';


                          modalcode += '  </div>';
                          modalcode += '  <!-- /.box-body -->';
                          
                          modalcode += '</div>';
                          modalcode += '<!--/.direct-chat -->';

                        } //end if not null
                        else
                        {
                          modalcode +='<h4 class="bg-yellow" style="padding:5px">Pending Approval </h4>';

                          @if (($anApprover && $isBackoffice) || ($isWorkforce && !$isBackoffice))
                          modalcode +='<br/><br/><a class="processThis btn btn-lg btn-success" data-approve="1" data-formtype="'+typeid+'"  data-dataid="'+data_id+'"><i class="fa fa-thumbs-up"></i> Approve</a> &nbsp;&nbsp&nbsp;<a class="processThis btn btn-lg btn-danger" data-approve="0" data-formtype="'+typeid+'" data-dataid="'+data_id+'"><i class="fa fa-thumbs-down"></i> Deny</a> <br/><br/><br/>';

                          @endif

                          modalcode += '        </div>';
                          modalcode += '        <!-- /.direct-chat-text -->';
                          modalcode += '      </div>';
                          modalcode += '      <!-- /.direct-chat-msg -->';
                          modalcode += '    </div>';
                          modalcode += '    <!--/.direct-chat-messages-->';

                          modalcode += '    <!--/.direct-chat-messages-->';
                          modalcode += '  </div>';
                          modalcode += '  <!-- /.box-body -->';
                          
                          modalcode += '</div>';
                          modalcode += '<!--/.direct-chat -->';

                        }

                        //modalcode +=' <div class="modal-footer no-border">';

                        //modalcode +='</div>';
                        modalcode+= '</div></div></div><!--end DTRP modal-->';

                         /*--------------------------------------------*/

                        if (full.details.isApproved == null){ var delMessage ="Are you sure you want to cancel this request?"; var v = "Cancel"; }
                        else { 

                          if (typeid == '7'){
                            var delMessage = "Are you sure you want to revoke this approved overtime?";  
                            var v = "Revoke";
                          }
                          else if(typeid == '10')
                          {
                            var delMessage = "Are you sure you want to revoke this vacation leave?";  
                            var v = "Revoke";

                          } 
                          else if(typeid == '11')
                          {
                            var delMessage = "Are you sure you want to revoke this sick leave?";  
                            var v = "Revoke";

                          }else if(typeid == '12')
                          {
                            var delMessage = "Are you sure you want to revoke this approved LWOP?";  
                            var v = "Revoke";

                          }else if(typeid == '13')
                          {
                            var delMessage = "Are you sure you want to revoke this approved OBT?";  
                            var v = "Revoke";

                          }else {
                            var delMessage = "Are you sure you want to delete this request?";  
                            var v = "Delete"; 

                          }
                        }

                        var delModal ='<div class="modal fade" id="myModal'+data_id+'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button><h4 class="modal-title" id="myModalLabel">' +v+' '+requesttype+'</h4></div><div class="modal-body"><br/><br/>'+delMessage+'<br/></div><div class="modal-footer no-border"><form action="'+deleteLink+'" method="POST" class="btn-outline pull-right" id="deleteReq"><input type="hidden" name="notifType" value="'+full.typeid+'" /><input type="hidden" name="redirect" value="1" /><button type="submit" class="btn btn-primary "><i class="fa fa-check"></i> Yes</button><button type="button" class="btn btn-default" data-dismiss="modal">Close</button><input type="hidden" name="_token" value="'+_token+'" /> </form></div></div></div></div>';
                      
                     

                      var typeid = full.typeid;

                      
                      if(full.details.isApproved)
                      {
                        //for specific leave types, LEAVES can be revoked and deleted. CWS cant be just deleted
                        switch (typeid)
                        {
                          //VL
                          case '10': { 

                                      if (full.irrevocable) //if DTR is not yet locked, you can still edit || LOCKED == dtrSheet->where(biometrics_id) exists
                                      {



                                        return '<a data-toggle="modal" data-target="#myModal_DTRP'+full.details.id+'" target="_blank" style="margin-top:5px" class="viewbtn btn btn-xs btn-flat text-primary"><i class="fa fa-info"></i>&nbsp; View </a><div class="clearfix"></div>'+modalcode;
                                      } else {

                                        return '<a data-toggle="modal" data-target="#myModal_DTRP'+full.details.id+'" target="_blank" style="margin-top:5px" class="viewbtn btn btn-xs btn-flat text-primary"><i class="fa fa-info"></i>&nbsp; View </a><!-- <a style="margin-top:5px" class="viewbtn btn btn-xs btn-flat text-primary"><i class="fa fa-pencil"></i>&nbsp; Edit</a> --><a data-toggle="modal" data-target="#myModal'+full.details.id+'"  href="#" class="btn btn-flat btn-xs text-default" title="Revoke" style="margin-top:5px"><i class="fa fa-undo"></i> Revoke </a><div class="clearfix"></div>'+modalcode+delModal;

                                      }

                                     }break;
                          default: {

                                      if (full.irrevocable)
                                      {

                                        /*---- but if an approver, deletable dapat ---*/
                                        var apprv = "{{$anApprover}}";
                                        if ( apprv ) {
                                          return '<a data-toggle="modal" data-target="#myModal_DTRP'+full.details.id+'" target="_blank" style="margin-top:5px" class="viewbtn btn btn-xs btn-flat text-primary"><i class="fa fa-info"></i>&nbsp; View Details</a><a data-toggle="modal" data-target="#myModal'+full.details.id+'"  href="#" class="btn btn-flat btn-xs text-default" title="Delete" style="margin-top:5px"><i class="fa fa-trash"></i> Delete </a><div class="clearfix"></div>'+modalcode+delModal;

                                          
                                        }else{
                                          return '<a data-toggle="modal" data-target="#myModal_DTRP'+full.details.id+'" target="_blank" style="margin-top:5px" class="viewbtn btn btn-xs btn-flat text-primary"><i class="fa fa-info"></i>&nbsp; View Details</a><div class="clearfix"></div>'+modalcode;

                                        }
                                        


                                      }else{
                                        return '<a data-toggle="modal" data-target="#myModal_DTRP'+full.details.id+'" target="_blank" style="margin-top:5px" class="viewbtn btn btn-xs btn-flat text-primary"><i class="fa fa-info"></i>&nbsp; View Details</a><a data-toggle="modal" data-target="#myModal'+full.details.id+'"  href="#" class="btn btn-flat btn-xs text-default" title="Revoke" style="margin-top:5px"><i class="fa fa-undo"></i> Revoke </a><div class="clearfix"></div>'+modalcode+delModal;


                                      }

                                      

                                    } break;

                          
                        }
                        

                      }else if(full.details.isApproved == '0')
                          return '<a data-toggle="modal" data-target="#myModal_DTRP'+full.details.id+'" target="_blank" style="margin-top:5px" class="viewbtn btn btn-xs btn-flat text-primary"><i class="fa fa-info"></i>&nbsp; View Details</a> <a data-toggle="modal" data-target="#myModal'+full.details.id+'"  href="#" class="btn btn-flat btn-xs text-default" title="Delete" style="margin-top:5px"><i class="fa fa-trash"></i> Delete</a><div class="clearfix"></div>'+modalcode+delModal;
                        else
                          return '<a data-toggle="modal" data-target="#myModal_DTRP'+full.details.id+'" target="_blank" style="margin-top:5px" class="viewbtn btn btn-xs btn-flat text-primary"><i class="fa fa-info"></i>&nbsp; View Details</a> <a data-toggle="modal" data-target="#myModal'+full.details.id+'"  href="#" class="btn btn-flat btn-xs text-default" title="Delete" style="margin-top:5px"><i class="fa fa-times"></i> Cancel </a><div class="clearfix"></div>'+modalcode+delModal;
                      
                    
                }}
               

              ],
             

            "responsive":true,
            //"scrollX":false,
            "dom": '<"col-xs-1"f><"col-xs-11 text-right"l><"clearfix">rt<"bottom"ip><"clear">',
            "order": [[ 2, 'DESC' ]],
            "lengthChange": true,
            "oLanguage": {
               "sSearch": "<strong>All Submitted Requests</strong> <br/><br/>To re-order entries, click the sort icon on the right of column headers. <br/>To filter out results, just type in the search box anything you want to look for:",
               "class": "pull-left"
             },

                      
              });



        $('#requests').on('click','.processThis',function(e){
              e.preventDefault();
              var isApproved = $(this).attr('data-approve');
              var formtype = $(this).attr('data-formtype');
              var dataid = $(this).attr('data-dataid');
              var _token = "{{ csrf_token() }}";

              switch(formtype){
                case '6': {var requesttype="Change Work Schedule"; var processlink = "{{action('UserCWSController@process')}}"; }break;
                case '7': {var requesttype="Overtime"; var processlink = "{{action('UserOTController@process')}}"; }break;
                case '8': {var requesttype="DTRP IN"; var processlink = "{{action('UserDTRPController@process')}}"; }break;
                case '9': {var requesttype="DTRP IN"; var processlink = "{{action('UserDTRPController@process')}}"; }break;
                case '10': {var requesttype="Vacation Leave"; var processlink = "{{action('UserVLController@process')}}"; }break;
                case '11': {var requesttype="Sick Leave"; var processlink = "{{action('UserSLController@process')}}"; }break;
                case '12': {var requesttype="Leave Without Pay"; var processlink = "{{action('UserLWOPController@process')}}"; }break;
                case '13': {var requesttype="Official Business Leave"; var processlink = "{{action('UserOBTController@process')}}"; }break;
                case '15': {var requesttype="Meeting/Huddle (Pre-Shift)"; var processlink = "{{action('UserOTController@process')}}"; }break;
                case '16': {var requesttype="Maternity Leave (ML)"; var processlink = "{{action('UserFamilyleaveController@process')}}"; }break;
                case '17': {var requesttype="Paternity Leave (PL)"; var processlink = "{{action('UserFamilyleaveController@process')}}"; }break;
                case '18': {var requesttype="Single-Parent Leave (SPL)"; var processlink = "{{action('UserFamilyleaveController@process')}}"; }break;
              }

              $.ajax({
                url: processlink,
                type:'POST',
                data:{ 
                  'id': dataid,
                  'isApproved': isApproved,
                  '_token':_token
                },
                success: function(res){
                  $('#myModal_DTRP'+dataid).modal('hide');

                    if (isApproved == '1')
                     $.notify("Submitted "+requesttype+ " for "+res.firstname+" :  Approved.",{className:"success",globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );
                   else
                     $.notify("Submitted "+requesttype+ " for "+res.firstname+" :  Denied.",{className:"error",globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );

                    $("#requests").DataTable().ajax.reload();

                },
                error: function(){
                  $('#myModal_DTRP'+dataid).modal('hide');

                   $.notify("An error occured. Please try again later.",{className:"error",globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );

                    $("#requests").DataTable().ajax.reload();
                }


              });

               
              
        }); 
         

        
      
      
   });

   

  /*
   * Custom Label formatter
   * ----------------------
   */
  function labelFormatter(label, series) {
    return '<div style="font-size:13px; text-align:center; padding:2px; color: #fff; font-weight: 600;">'
        + label
        + "<br>"
        + Math.round(series.percent) + "%</div>";
  }
</script>
<!-- end Page script -->
<!-- 
<script type="text/javascript" src="{{asset('public/js/dashboard.js')}}"></script> -->

@stop