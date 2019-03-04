@extends('layouts.main')

@section('metatags')
<title>New Pre-shift OT Request | Employee Management System</title>

@endsection

@section('content')


  <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
       <i class="fa fa-clock-o"></i> New Overtime (Pre-shift)
       <small>: {{$user->firstname}} {{$user->lastname}} </small>
      </h1>
     
      <ol class="breadcrumb">
        <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">My Requests</li>
      </ol>
    </section>

     <section class="content">

      <div class="row">
        @if(Auth::user()->id == $user->id)
        <div class="col-lg-6"><a href="{{action('UserController@userRequests',$user->id)}} "><i class="fa fa-arrow-left"></i> Back to My Requests</a></div>
        @else
        <div class="col-lg-6"><a href="{{action('UserController@userRequests',$user->id)}} "><i class="fa fa-arrow-left"></i> Back to 
          @if(is_null($user->nickname)) {{$user->firstname}}'s  Requests
          @else {{$user->nickname}}'s  Requests
          @endif

        </a></div>

        @endif
        
        <div class="col-lg-6 text-right">
          <strong>
            @if(Auth::user()->id == $user->id)
            <a href="{{action('UserVLController@create')}}" class="btn btn-sm  bg-blue"><i class="fa fa-plane fa-2x"></i> VL</a>
            <a href="{{action('UserSLController@create')}}" class="btn btn-sm  btn-danger"><i class="fa fa-2x fa-stethoscope"></i> SL</a>
            <a href="{{action('UserController@show',$user->id)}}#ws"class="btn btn-sm  bg-green"><i class="fa fa-2x fa-calendar-times-o"> </i> CWS</a>
            <a href="{{action('UserLWOPController@create')}}" class="btn btn-sm  bg-yellow"><i class="fa fa-2x fa-meh-o"></i>  LWOP</a>

            @else
             <a href="{{action('UserVLController@create',['for'=>$user->id])}}" class="btn btn-sm  bg-blue"><i class="fa fa-plane fa-2x"></i> VL</a>
            <a href="{{action('UserSLController@create',['for'=>$user->id])}}" class="btn btn-sm  btn-danger"><i class="fa fa-2x fa-stethoscope"></i> SL</a>
            <a href="{{action('UserController@show',$user->id)}}#ws"class="btn btn-sm  bg-green"><i class="fa fa-2x fa-calendar-times-o"> </i> CWS</a>
            <a href="{{action('UserLWOPController@create',['for'=>$user->id])}}" class="btn btn-sm  bg-yellow"><i class="fa fa-2x fa-meh-o"></i>  LWOP</a>
            <a href="{{action('UserOBTController@create',['for'=>$user->id])}}"class="btn btn-sm  bg-purple"><i class="fa fa-2x fa-briefcase"></i>  OBT</a>



            @endif

          </strong>
        </div>
      </div>

      <!-- ******** THE PANE ********** -->
          <div class="row">
             <div class="col-lg-1 col-sm-4  col-xs-9"></div>
             <div class="col-lg-10 col-sm-4 col-xs-12" ><!--style="background-color:#fff"--><br/><br/>
                <form class="col-lg-12" id="plotSched" name="plotSched">
                      <div class="box-info" style="background: rgba(256, 256, 256, 0.5)">
                        <div id="pane_VL" class="modal-body-upload" style="padding:20px;">
                            <div class="options_vl">
                              <div style="width:100%; " class="pull-left">

                                <div class="info-box"  style="background-color: #ffea7f">
                                  <span class="info-box-icon"><i class="fa fa-clock-o"></i></span>
                                  <div class="info-box-content">
                                    <span class="info-box-text"><strong class="text-black">Overtime (Pre-shift) </strong> details :</span>
                                    <span class="info-box-number"><span class="text-primary">PS-OT </span><span id="credits_vl" data-credits="{{$used}}">  </span> 
                                    </span>

                                    <div class="progress"><?php $progressBar = (1/$creditsLeft)*100; ?>
                                      <div id="percentage" class="progress-bar" style="width: 100%"></div>
                                    </div>
                                    <span class="progress-description text-white">
                                      <span class="pull-left" id="vlfrom"></span> <span id="vlto" class="pull-right"> To: </span><span id="credits_vl" data-credits="{{$used}}" style="visibility: hidden;"></span> 
                                    </span>
                                  </div>
                                  <!-- /.info-box-content -->
                                </div>
                                <!-- /.info-box -->

                              </div><br/>

                              <div class="row">
                                  <div class="col-lg-6">
                                   <label for="vl_from">OT Start: <br/>
                                    <input required type="text" class="dates form-control datepicker pull-left" name="vl_from" id="vl_from" placeholder="select date" style="width: 130px" />
                                    <input type="text" name="startHour" id="startHour" placeholder="HH:mm" class="form-control pull-left" style="width:150px;margin-left: 5px" />
                                    </label><div id="alert-vl_from" style="margin-top:10px">
                                    <div id="wait" class="text-success"></div>

                                     
                                   </div>
                                 </div>
                                 <div class="col-lg-6" id="shift_choices">
                                    <label ><input type="radio" name="coveredshift" id="shift_whole" value="1" />&nbsp; &nbsp;<i class="fa fa-hourglass"></i> Billed</label>
                                    <br/>
                                    <label id="shift_first" ><input type="radio" name="coveredshift" id="shift_first" value="2" />&nbsp; &nbsp;<i class="fa fa-hourglass-start"></i> Non-Billed <span id="shiftFrom_1"></span> </label>
                                     <br/>
                                    <label ><input type="radio" name="coveredshift" id="shift_second" value="3" />&nbsp; &nbsp;<i class="fa fa-hourglass-end"></i>Patch <span id="shiftFrom_2"></span> </label><br/>

                                    
                                </div>
                              </div>

                            </div><!--end options_vl-->
                          
                          <div class="row">
                            <div class="col-lg-12">
                             
                              <div class="moredays">
                                <div class="row">
                                  <input type="hidden" name="timestart_old2" /><input type="hidden" name="timeend_old2" />
                                  <div class="col-lg-6">
                                    <label for="vl_from">OT End: <br/>
                                      <input required type="text" class="dates form-control datepicker pull-left" name="vl_to" id="vl_to" value="" style="width: 130px" placeholder="MM/DD/YYYY" />
                                    <input type="text" name="endHour" id="endHour" placeholder="HH:mm" class="form-control pull-left" style="width:150px;margin-left: 5px" />
                                  </label><div id="alert-vl_from" style="margin-top:10px"></div>

                                  <div id="hrsworked"><br/><br/>
                                      <label>Total Worked OT hour(s):</label>
                                      <input type="text" name="totalhours" id="totalhours" class="form-control" style="width: 100px" data-billable="" />
                                    </div>


                                  </div>
                                  <div class="col-lg-6">
                                    <div id="vl_more">
                                     
                                      
                                    </div>
                                  </div>
                                </div>
                              </div><!--end moredays-->

                              <label><r/><br/><br/> Notes: </label><br/>
                              <small>Kindly provide details about this pre-shift OT </small><br/> 
                              <textarea name="reason_vl" style="width:100%;"></textarea>
                            </div>
                          </div>

                          
                        </div><!--end pane vl-->

                        <div class="clearfix"></div>
                      <a onclick="javascript:window.history.back();" class="back btn btn-flat pull-left" style="font-weight:bold;margin-top:0px; z-index:999"><i class="fa fa-angle-double-left"></i> &nbsp;Back</a>
                      
                      <a id="save" data-timestart_old="" data-timeend_old="" data-requesttype="VL" data-date="{{$vl_from->format('Y-m-d H:i:s')}}" data-userid="{{$user->id}}" class="btn btn-default btn-md pull-right" style="margin-right:25px" > <i class="fa fa-upload" ></i> Submit for Approval </a>
                       <div class="clearfix"></div><br/><br/>

                      </div><!--end box-info -->

                      <h2> <br/><br/></h2>
                      <input type="hidden" name="timestart_old" value="" />
                      <input type="hidden" name="timeend_old" value="" />
                      <input type="hidden" name="timestart_old2" value="" />
                      <input type="hidden" name="timeend_old2" value="" />
                      <input type="hidden" name="leaveFrom" />
                      <input type="hidden" name="leaveTo" />
                      <input type="hidden" name="biometrics_id" id="biometrics_id" />
                      <input type="hidden" name="biometrics_id2" id="biometrics_id2" />
                      

                </form>
              </div> <!-- end col-lg-10 -->

          

                        
                            
             
              <div class="col-lg-1 col-sm-4  col-xs-9"></div>
              <div class="holder"></div>


              <div class="clearfix"></div>
          </div><!--end row-->


         
               

     
         




       
     </section>
          



@endsection


@section('footer-scripts')

<script src="{{URL::asset('public/js/moment.min.js')}}" ></script>
<script>
  $.widget.bridge('uibutton', $.ui.button);
</script>



<!-- Page script -->
<script>

  $(function () {
     'use strict';

     $('#startHour').fadeOut();$('.moredays').fadeOut();$('#save').fadeOut();

     $('#vl_to, #endHour').on('blur',function(){
      $.ajax({
          url: "{{action('UserOTController@getPSOTworkedhours',$user->id)}}",
                type:'GET',
                data:{ 
                 'startday': $('#vl_from').val(), 
                 'starttime': $('#startHour').val(),
                 'endday': $('#vl_to').val(), 
                 'endtime': $('#endHour').val(),
                 
                  '_token':_token
                },
          success: function(resp){
              $('#totalhours').val(resp.workedHours);
              $('#totalhours').attr('data-billable',resp.workedHours);
              $('#biometrics_id').val(resp.bioStart);
              $('#biometrics_id2').val(resp.bioEnd);
          }

     });
    });


     $('#vl_from').on('focusout', function(){

        var _token = "{{ csrf_token() }}";
        var dval = $('#vl_from').val()

        $('#wait').html('checking employee schedule and logs. Please wait...<i class="fa fa-refresh"></i> <br/><br/>');

        $.ajax({
          url: "{{action('UserOTController@getPSOTLogsForThisDate',$user->id)}}",
                type:'GET',
                data:{ 
                 'payday': $('#vl_from').val(), 
                 'logtype':'1', //login
                  '_token':_token
                },
           success: function(response){
                  console.log(response);
                  if(response.status=='1'){

                    $('#wait').fadeOut();$('.moredays').fadeIn();
                    $('#startHour').val(response.log).fadeIn();
                    $('#endHour').val(response.sched).fadeIn();
                    $('#totalhours').val(response.workedHours);
                    $('#totalhours').attr('data-billable',response.workedHours);

                    $('#vl_to').val(dval).prop('disabled',false);
                    $('#vl_to').prop('placeholder',dval);
                    $('#biometrics_id').val(response.biometrics_id);
                    $('#biometrics_id2').val(response.biometrics_id);
                    $('#save').fadeIn();


                  } else if(response.status=='2'){
                    $('#wait').html(response.message + '<br/><br/>').fadeIn();


                  }
                  else{
                    $('#wait').fadeOut();
                    $('#startHour').val(response.message).fadeIn();
                    $('#startHour').prop('disabled',true);
                    $('#vl_to').val(dval).prop('disabled',true);
                    if (response.message == "invalid date"){}
                      else{
                        $('.moredays').fadeOut();
                        $.notify("No biometrics found for that date. \n\nSubmit first a DTRP IN for that production date, and then file again for a pre-shift OT once DTRP IN is approved.",{className:"error", globalPosition:'right middle',autoHideDelay:5000, clickToHide:true} );
                      }
                  }
                    

                  //$('input[name="timestart_old"]').val(response.start);
                  //$('input[name="timeend_old"]').val(response.end);
                  

                  
                }

        });

        


     });


     //****** initialize for those with URL param from DTR
     var _token = "{{ csrf_token() }}";
             $.ajax({
                url: "{{action('UserController@getWorkSchedForTheDay',$user->id)}}",
                type:'POST',
                data:{ 
                 'vl_day': $('#vl_from').val(), 
                  '_token':_token
                },
                success: function(response){
                  console.log(response);
                  $('input[name="timestart_old"]').val(response.start);
                  $('input[name="timeend_old"]').val(response.end);
                  

                  
                }
              });
    //****** initialize for those with URL param from DTR

    
// $vl->biometrics_id = '';
//        $vl->billable_hours = '';
//        $vl-> = '';
//        $vl->timeStart = '';
//        $vl->timeEnd = '';
//        $vl-> = '';
//        $vl-> = '';
//        $vl-> = true;
//        $vl-> = '';
//        $vl-> = '';
//        $vl->approver = '';



     $('#save').on('click',function(e){
        e.preventDefault(); e.stopPropagation();
        var _token = "{{ csrf_token() }}";
        var user_id = $(this).attr('data-userid');
        var biometrics_id = $('#biometrics_id2').val();
        var reason_vl = $('textarea[name="reason_vl"]').val();

         if (reason_vl == ""){ $.notify("Please include a brief description about this pre-shift OT.",{className:"error", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );return false; }
        else
        {

          $.ajax({
                    url: "{{action('UserOTController@requestPSOT')}}",
                    type:'POST',
                    data:{ 
                      'id': user_id,
                      'user_id': user_id,
                      'biometrics_id': biometrics_id,
                      'billable_hours': $('#totalhours').attr('data-billable'),
                      'filed_hours': $('#totalhours').val(),
                      'timeStart': $('#startHour').val(),
                      'timeEnd': $('#endHour').val(),
                      'reason': reason_vl,
                      'billedType': $('input[name="coveredshift"]:checked').val(),
                      'preshift':true,
                      'isRD':false,

                      '_token':_token
                    },
                    success: function(response){
                      
                     $('#save').fadeOut();

                      if (response.success == '1')
                        $.notify("Pre-shift OT saved successfully.",{className:"success",globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );
                        else
                          $.notify("Pre-shift OT submitted for approval.",{className:"success", globalPosition:'right middle',autoHideDelay:3000, clickToHide:true} );
                      
                      console.log(response);
                      window.setTimeout(function(){
                        window.location.href = "{{action('UserController@userRequests',$user->id)}}";
                      }, 4000);
                    }
                  });
        }
        
      });  


     
     $( ".datepicker" ).datepicker({dateFormat:"YYYY-mm-dd"});

     //$('.moredays input').prop('disabled',true);
     


      $('#shift_whole, #shift2_whole').prop('checked',true);




function computeCredits(vl_from,vl_to,shift_from,shift_to,creditsleft)
  {
        var _token = "{{ csrf_token() }}";
        var user_id = "{{$user->id}}"; // $("#save").attr('data-userid');

        var date_from = moment(vl_from,"MM/DD/YYYY").format('YYYY-MM-DD');
        //var date_ctr = moment(vl_from,"MM/DD/YYYY");
        
        if (vl_to !== null)
          var date_to = moment(vl_to,"MM/DD/YYYY").format('YYYY-MM-DD');
        else date_to = null;

        var totalcredits = 0;

        console.log("IN--computeCredits");

        $.ajax({
                  url: "{{action('UserOBTController@getCredits')}}",
                  type:'POST',
                  data:{ 
                   'date_from': date_from, // $('#vl_from').val(),
                   'date_to': date_to,
                   'user_id': user_id,
                   'shift_from': shift_from,
                   'shift_to': shift_to, 
                   'creditsleft':creditsleft,
                    '_token':_token
                  },
                  success: function(response){
                    console.log("response from getCredits");
                    console.log(response);

                    if(response.hasLWOPalready==true)
                    {

                      $.notify("You've already filed for an LWOP covering that day.",{className:"error", globalPosition:'right middle',autoHideDelay:2000, clickToHide:true} );

                    }else{

                      $("span#credits_vl").html(response.credits);
                      $("span#credits_vl").attr('data-credits', response.credits);


                      

                      switch(response.shift_from)
                      {
                        case '1': {$('#shiftFrom_1,#shiftFrom_2').html(""); }break;
                        case '2': { $('#shiftFrom_1').html("<strong>[ "+response.displayShift+ " ]</strong>"); $('#shiftFrom_2').html(""); }break;
                        case '3': { $('#shiftFrom_2').html("<strong>[ "+response.displayShift+ " ]</strong>"); $('#shiftFrom_1').html("");}break;
                      }

                     

                     

                    }//end has vl already
                    
                    
                                      
                  }
                });

        return false;

  }

  function checkIfRestday(vl_day, reason_vl)
  {      


        var _token = "{{ csrf_token() }}";
        var rd = null;
                                 $.ajax({
                                    url: "{{action('UserController@getWorkSchedForTheDay',$user->id)}}",
                                    type:'POST',
                                    data:{ 
                                     'vl_day': vl_day, // $('#vl_from').val(), 
                                      '_token':_token
                                    },
                                    success: function(response){
                                      console.log(response.start);
                                      console.log(response.end);
                                      if (response.start === response.end){
                                        console.log("equal");
                                        alert("Actually, no need to file for leave. Selected date is your REST DAY!"); return false;
                                      }else {

                                        if (reason_vl == ""){  $.notify("Please include a brief reason about your leave for HR-Finance's review.",{className:"error", globalPosition:'right middle',autoHideDelay:4000, clickToHide:true} );return false; }
                                        else {
                                          return true;
                                        } 

                                      }
                                      
                                    }
                                  });

        

  }

  function getWorkSchedForTheDay(vl_day)
  {
    var _token = "{{ csrf_token() }}";

    $.ajax({
            url: "{{action('UserController@getWorkSchedForTheDay',$user->id)}}",
            type:'POST',
            data:{ 
             'vl_day': vl_day, // $('#vl_from').val(), 
              '_token':_token
            },
            success: function(response){

              $('input[name="timestart_old"]').val(response.start);
              $('input[name="timeend_old"]').val(response.end);


            }
          });
    return true;

  }

  function getCoveredShifts(coveredshift, leave_from, timestart_old, timeend_old)
  {
        switch(coveredshift)
            {
              case '1': {  //WHOLE DAY 

                            // var leaveStart = moment(leave_from+" "+timestart_old,"MM/D/YYYY h:m A");
                            // var leaveEnd = moment(leave_from+" "+timeend_old,"MM/D/YYYY h:m A");
                            var leaveStart = moment(timestart_old,"YYYY-MM-D H:m:s");
                            var leaveEnd = moment(timeend_old,"YYYY-MM-D H:m:s");

                        }break;

              case '2': { //1st half of shift
                            var leaveStart = moment(timestart_old,"YYYY-MM-D H:m:s");
                            var leaveEnd =  moment(timestart_old,"YYYY-MM-D H:m:s").add(4,'hours');


                        }break;
              case '3': { //2nd half
                            var leaveStart =moment(timestart_old,"YYYY-MM-D H:m:s").add(5,'hours');
                            // moment(leave_from+" "+timestart_old,"MM/D/YYYY h:m A").add(5,'hours');
                            var leaveEnd = moment(timeend_old,"YYYY-MM-D H:m:s");

                            

                        }break;
            }

            var shifts = {leaveStart: leaveStart, leaveEnd: leaveEnd};
            return shifts;


  }//end function getCoveredShifts

});

  


   

  
</script>
<!-- end Page script -->


@stop