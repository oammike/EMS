@extends('layouts.main')

@section('metatags')
<title>New VTO Request | Employee Management System</title>

@endsection

@section('content')


  <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
       <i class="fa fa-history"></i> New Voluntary Time Off
      <small> : {{$user->firstname}} {{$user->lastname}} </small>
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
            <a href="{{action('UserSLController@create')}}"  class="btn btn-sm  btn-danger"><i class="fa fa-2x fa-stethoscope"></i> SL</a>
            <a href="{{action('UserController@show',$user->id)}}#ws"  class="btn btn-sm  bg-green"><i class="fa fa-2x fa-calendar-times-o"> </i> CWS</a>
            <a href="{{action('UserLWOPController@create')}}" class="btn btn-sm  bg-yellow"><i class="fa fa-meh-o fa-2x"></i> LWOP</a>
            <a href="{{action('UserOBTController@create')}}" class="btn btn-sm  bg-purple"><i class="fa fa-2x fa-briefcase"></i>  OBT</a>

            @else
            <a href="{{action('UserSLController@create',['for'=>$user->id])}}"  class="btn btn-sm  btn-danger"><i class="fa fa-2x fa-stethoscope"></i> SL</a>
            <a href="{{action('UserController@show',$user->id)}}#ws"  class="btn btn-sm  bg-green"><i class="fa fa-2x fa-calendar-times-o"> </i> CWS</a>
            <a href="{{action('UserLWOPController@create',['for'=>$user->id])}}" class="btn btn-sm  bg-yellow"><i class="fa fa-meh-o fa-2x"></i> LWOP</a>
            <a href="{{action('UserOBTController@create',['for'=>$user->id])}}" class="btn btn-sm  bg-purple"><i class="fa fa-2x fa-briefcase"></i>  OBT</a>


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

                                <div class="info-box bg-blue">
                                  <span class="info-box-icon"><i class="fa fa-history"></i></span>
                                  <div class="info-box-content">
                                    
                                    <h3>VTO Details</h3>
                                   

                                    
                                  </div>
                                  <!-- /.info-box-content -->
                                </div>
                                <!-- /.info-box -->

                              </div><br/>

                              <div class="row">
                                  <div class="col-lg-6">

                                    <div class="row">
                                      <div class="col-sm-6">
                                        <label for="vl_from">From: <input required type="text" class="dates form-control datepicker" name="vl_from" id="vl_from" value="{{$vl_from->format('m/d/Y')}}" /></label><div id="alert-vl_from" style="margin-top:10px"></div>
                                      </div>
                                      <div class="col-sm-6"></div>
                                    </div>

                                    <div class="row">
                                      <div class="col-sm-4">
                                         <label for="vl_from">Time Start: <input required type="text" class="form-control" name="timeStart" id="timeStart" placeholder="HH:MM" /></label><div id="alert-timeStart" style="margin-top:10px"></div>
                                      </div>
                                      <div class="col-sm-2 text-left" style="padding-top: 10px">
                                        <label><input type="radio" name="amStart" value="AM" checked="checked"> AM</label>
                                        <label><input type="radio" name="amStart" value="PM"> PM</label>
                                      </div>
                                      <div class="col-sm-4">
                                        <label for="vl_from">Time End: <input required type="text" class="form-control" name="timeEnd" id="timeEnd" placeholder="HH:MM" /></label><div id="alert-timeEnd" style="margin-top:10px"></div>
                                      </div>
                                      <div class="col-sm-2 text-left" style="padding-top: 10px">
                                        <label><input type="radio" name="amEnd" value="AM" checked="checked"> AM</label>
                                        <label><input type="radio" name="amEnd" value="PM"> PM</label>
                                      </div>
                                    </div>

                                    <div class="row">
                                      <div class="col-sm-12">
                                        <label for="totalhours">Total Hours: <input required type="text" class="form-control " name="totalhours" id="totalhours" placeholder="xx.xx" /></label><div id="alert-hours" style="margin-top:10px"></div>
                                      </div>
                                    </div>

                                    <div class="row">
                                      <div class="col-sm-6">
                                        <label for="totalhours">Is this a <span class="text-danger"> Forced leave / Client-mandated</span>?</label><div id="alert-hours" style="margin-top:10px"></div>
                                      </div>
                                      <div class="col-sm-6">
                                        <label><input type="radio" name="forced" value="1" checked="checked"> Yes </label>&nbsp;&nbsp;
                                        <label><input type="radio" name="forced" value="0"> No </label>
                                        <div id="alert-forced" style="margin-top:10px"></div>
                                      </div>
                                    </div>
                                   

                                   
                                 </div>

                                 <div class="col-lg-6" id="shift_choices">

                                    <h4 class="text-primary">
                                      @if($currentVLbalance !== "N/A" && $currentSLbalance !== "N/A")
                                      <i class="fa fa-plane"></i> VL credits: <strong>{{number_format($currentVLbalance,2) }} </strong> </h4>

                                      @else
                                      <i class="fa fa-plane"></i> VL credits: <strong>N/A </strong> </h4>
                                      @endif

                                    <h4 class="text-danger"> 
                                      @if($currentVLbalance !== "N/A" && $currentSLbalance !== "N/A")
                                      <i class="fa fa-stethoscope"></i> SL credits: <strong>{{ number_format($currentSLbalance,2) }} </strong> </h4>
                                      @else
                                      <i class="fa fa-stethoscope"></i> SL credits: <strong>N/A </strong> </h4>
                                      @endif


                                    <h5><br/><br/>Deduct Using:</h5>

                                    @if($currentVLbalance !== "N/A")
                                      @if(number_format($currentVLbalance,2) > 0.0 || ($isNDY && $currentVLbalance !== "N/A") )
                                      <label style="margin-right: 15px"><input type="radio" name="useCredits" value="VL" @if($useCredits=='VL') checked="checked" @endif> VL</label>
                                      @endif
                                    @endif

                                    @if($currentSLbalance !== "N/A")
                                      @if(number_format($currentSLbalance,2) > 0.0 || ($isNDY && $currentSLbalance !== "N/A") )
                                      <label style="margin-right: 15px"><input type="radio" name="useCredits" value="SL"  @if($useCredits=='SL') checked="checked" @endif> SL</label>
                                      @endif
                                    @endif

                                    <label style="margin-right: 15px"><input type="radio" name="useCredits" value="AdvSL"  @if($useCredits=='AdvSL') checked="checked" @endif> Advanced SL</label>
                                    <label style="margin-right: 15px"><input type="radio" name="useCredits" value="LWOP"  @if($useCredits=='LWOP') checked="checked" @endif> LWOP</label>
                                    <div id="deduct" style="margin-top: 20px"><i class="fa fa-exclamation-circle"></i> Total credits to be deducted: <strong></strong> </div>
                                   
                                </div>
                              </div>

                            </div><!--end options_vl-->
                          
                          <div class="row">
                            <div class="col-lg-12">
                              

                              <label><r/><br/><br/> Notes: </label><br/>
                              <small>Brief description about this Voluntary Time Off</small><br/> 
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


     //****** initialize for those with URL param from DTR
     var _token = "{{ csrf_token() }}";
             $.ajax({
                url: "{{action('UserController@getWorkSchedForTheDay',$user->id)}}",
                type:'POST',
                data:{ 
                 'vl_day': $('#vl_from').val(), 
                 'isStylized':false,
                  '_token':_token
                },
                success: function(response){
                   console.log('from getWorkSchedForTheDay');
                          console.log(response);
                  $('input[name="timestart_old"]').val(response.timeStart);
                  $('input[name="timeend_old"]').val(response.timeEnd);

                  if (response.timeStart === response.timeEnd || response.isRD == 1)
                  {
                    alert("Actually, no need to file for leave. Selected date is your REST DAY!"); return false;
                  }
                  

                  
                }
              });
    //****** initialize for those with URL param from DTR

    




     $('#save').on('click',function(e){
            e.preventDefault(); e.stopPropagation();
            var _token = "{{ csrf_token() }}";
            var isNDY = "{{$isNDY}}";
            var user_id = $(this).attr('data-userid');
            var selectedDate = $(this).attr('data-date');
            var requestType = $(this).attr('data-requesttype');
            var timestart = $('input[name="timeStart"]').val();
            var timeend = $('input[name="timeEnd"]').val();
            var amStart = $('input[name="amStart"]:checked').val();
            var amEnd = $('input[name="amEnd"]:checked').val();
            

            var vl_from = $('input[name="vl_from"]').val(); // MM/dd/YYYY
            

           
            var reason_vl = $('textarea[name="reason_vl"]').val();
            var totalhours = $('input[name="totalhours"]').val();

            var mfrom = moment(vl_from,"MM/D/YYYY").format('YYYY-MM-D');
           
            
            var leaveFrom = moment(vl_from,"MM/D/YYYY").format('YYYY-MM-D H:mm:ss'); 

            var useCredits = $('input[name="useCredits"]:checked').val();
            var forced = $('input[name="forced"]:checked').val();
           

            var mayExisting = checkExisting(leaveFrom,_token);
            console.log('amStart:amEnd');
            console.log(amStart + '-'+ amEnd);
            console.log(useCredits);

            if (mayExisting)
            {
              $.notify("An existing VTO has already been filed covering those dates.\nIf you wish to file a new one, go to employee\'s DTR Requests page and cancel previously submitted VTO.",{className:"error", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );return false; 

            }
            else
            {

              if (reason_vl == ""){ $.notify("Please include a brief reason about your leave for HR-Finance's review.",{className:"error", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );return false; }
              else
              {
                    var isValidTime = /^([01]?[0-9]|2[0-3]):[0-5][0-9]$/.test(timestart);
                    var isValidTime2 = /^([01]?[0-9]|2[0-3]):[0-5][0-9]$/.test(timeend);


                    if (isValidTime && isValidTime2)
                    {
                      $('input[name="leaveFrom"]').val(leaveFrom);

                      if ( $.isNumeric(totalhours) == false )
                      {
                        $.notify("Invalid total hours indicated.",{className:"error", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );return false;

                      }else
                      {

                         if( (totalhours*0.125) > parseFloat("{{$currentVLbalance}}") && useCredits=="VL"  && (isNDY !== '1') )
                         {
                          $.notify("insufficient VL credits. Please try submitting using LWOP. ",{className:"error", globalPosition:'right middle',autoHideDelay:5000, clickToHide:true} ); return false;
                         } 
                          
                        else if( (totalhours*0.125) > parseFloat("{{$currentSLbalance}}") && useCredits=="SL" && (isNDY !== '1') )
                         {
                          $.notify("insufficient SL credits. Please try submitting using LWOP. ",{className:"error", globalPosition:'right middle',autoHideDelay:5000, clickToHide:true} ); return false;
                         }
                         else
                         {
                            console.log("Do ajax");
                            $.ajax({
                                  url: "{{action('UserVLController@requestVTO')}}",
                                  type:'POST',
                                  data:{ 
                                    'id': user_id,
                                    'leaveFrom': leaveFrom,
                                    'reason_vl': reason_vl,
                                    'totalhours': totalhours,
                                    'timeStart': timestart+' '+amStart,
                                    'timeEnd':  timeend+' '+amEnd,
                                    'useCredits': useCredits,
                                    'forced': forced,
                                    '_token':_token
                                  },
                                  success: function(response2){
                                    
                                    $('#save').fadeOut();

                                    if (response2.success == '1')
                                      $.notify("VTO saved successfully.",{className:"success",globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );
                                      else
                                        $.notify("VTO submitted for approval.",{className:"success", globalPosition:'right middle',autoHideDelay:3000, clickToHide:true} );
                                    
                                    console.log(response2);
                                    window.setTimeout(function(){
                                      window.location.href = "{{action('UserController@userRequests',$user->id)}}";
                                    }, 2000);
                                  }
                                });

                         }


                       

                      }

                      
                      
                    }
                    else
                    {
                      $.notify("Invalid time format. Please make sure you indicate the correct start & end time for this VTO ",{className:"error", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );return false;
                    }

                    
              }


            }//end if else may existing

                
      }); 


     
     $( ".datepicker" ).datepicker({dateFormat:"YYYY-mm-dd"});

     



     $('#vl_from').on('focusout', function(){
          var vl_from = $('input[name="vl_from"]').val(); 
          var vl_from1 = moment(vl_from,"MM/DD/YYYY");
          var vl_to = $('input[name="vl_to"]').val();
          var toval = moment(vl_from,"MM/DD/YYYY").format('MMM DD ddd');
          var theshift = $('input[name="coveredshift"]:checked').val();
          var shift2 = $('input[name="coveredshift2"]:checked').val();
          var creditsleft = '{{$creditsLeft}}';
          $("#vlfrom").html("From: "+toval);


          console.log('creditsleft');
          console.log(creditsleft);
          if (vl_to == "") computeCredits(vl_from1,null,theshift,shift2,creditsleft);
          else computeCredits(vl_from1,vl_to,theshift,shift2,creditsleft);

          var _token = "{{ csrf_token() }}";
          $.ajax({
                url: "{{action('UserController@getWorkSchedForTheDay',$user->id)}}",
                type:'POST',
                data:{ 
                 'vl_day': $('#vl_from').val(), 
                 'isStylized':false,
                  '_token':_token
                },
                success: function(response){
                  console.log(response);
                  $('input[name="timestart_old"]').val(response.timeStart);
                  $('input[name="timeend_old"]').val(response.timeEnd);
                }
              });
      });

     $('#timeStart,#timeEnd').on('focusout',function(){

      var t = $(this).val();
      var isValidTime = /^([01]?[0-9]|2[0-3]):[0-5][0-9]$/.test(t);

     // alert("Invalid Time Format.\nMake sure it's in this time format [hh:mm] ");

     });


     $('#totalhours').on('focusout',function(){

      var isNDY = "{{$isNDY}}";

      var val = $(this).val();
      var useCredits = $('input[name="useCredits"]:checked').val();

      if ( $.isNumeric(val) == false ) alert("Invalid number of hours indicated!");
      else
      {
        var creds = 0.125 * val;

        if(isNDY !== '1')
        {
           if( creds > parseFloat({{$currentVLbalance}}) && useCredits=="VL" ) 
            alert("Note that you have insufficient VL leave credits."); 
          else if( creds > parseFloat({{$currentSLbalance}}) && useCredits=="SL" )
            alert("Note that you have insufficient SL leave credits."); 

        }

       

        $('#deduct strong').html('');
        $('#deduct strong').html(creds);


      }
      

     });




      



function checkExisting(leaveFrom,_token){

  var hasExisting = null;
  $.ajax({
                url: "{{action('UserVLController@checkExisting')}}",
                type:'POST',
                async: false,
                data:{ 
                 'leaveStart': leaveFrom,
                 'user_id': "{{$user->id}}",
                 '_token': _token
                },
                success: function(response)
                {
                  console.log('from checkExisting:');
                  console.log(response);

                  if (response.existing === 0){
                    hasExisting = false;
                  }else
                    hasExisting = true;

                }

  });

  return hasExisting;

}

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

        //console.log("IN--computeCredits");
        console.log('passed items:');
        console.log('date_from: '+ date_from+ ' date_to: '+ date_to, 'creditsleft: '+ creditsleft);

        $.ajax({
                  url: "{{action('UserVLController@getCredits')}}",
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
                    console.log("from: getCredits");
                    console.log(response);

                    if(response.hasVLalready==true)
                    {

                      $.notify("You've already filed for a vacation leave covering that day.",{className:"error", globalPosition:'right middle',autoHideDelay:2000, clickToHide:true} );

                    }else{

                      $("span#credits_vl").html(response.credits);
                      $("span#credits_vl").attr('data-credits', response.credits);
                      var cl = parseFloat(response.creditsleft);
                      console.log("response.creditsleft");
                      console.log(response.creditsleft);
                      console.log("cl");
                      console.log(cl);
                      $("#creditsleft").html(cl.toFixed(2));
                      $("#creditsleft").attr('data-left',cl.toFixed(2));
                      

                      switch(response.shift_from)
                      {
                        case '1': {$('#shiftFrom_1,#shiftFrom_2').html(""); }break;
                        case '2': { $('#shiftFrom_1').html("<strong>[ "+response.displayShift+ " ]</strong>"); $('#shiftFrom_2').html(""); }break;
                        case '3': { $('#shiftFrom_2').html("<strong>[ "+response.displayShift+ " ]</strong>"); $('#shiftFrom_1').html("");}break;
                      }

                     console.log("100 - "+parseFloat(response.credits)+'/'+parseFloat("{{$creditsLeft}}"));
                      var bar = parseFloat(100-(parseFloat(response.credits)/(parseFloat("{{$creditsLeft}}") +1 )*100));
                      $('#percentage').css({width:bar+'%'});



                      if (parseFloat(response.forLWOP) > 0)
                      $.notify("You no longer have enough VL credits left to cover your "+ response.credits+ " day leave. \n\n You'll earn an additional "+response.creditsToEarn+ " leave credits towards the end of the year, \nbut the remaining ("+ response.forLWOP +") needed credits will be filed as an LWOP instead.",{className:"error", globalPosition:'right middle',autoHideDelay:25000, clickToHide:true} );

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
                                     'isStylized':false,
                                      '_token':_token
                                    },
                                    success: function(response){

                                      //console.log(response.start);
                                      //console.log(response.end);
                                      if (response.timeStart === response.timeEnd){
                                        //console.log("equal");
                                        console.log('res:');
                                        console.log(response);
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
             'isStylized':false,
              '_token':_token
            },
            success: function(response){

              $('input[name="timestart_old"]').val(response.timeStart);
              $('input[name="timeend_old"]').val(response.timeEnd);


            }
          });
    return true;

  }

  function getCoveredShifts(coveredshift, leave_from,leave_to, timestart_old, timeend_old)
  {
        switch(coveredshift)
            {
              case '1': {  //WHOLE DAY 

                            // var leaveStart = moment(leave_from+" "+timestart_old,"MM/D/YYYY h:m A");
                            // var leaveEnd = moment(leave_from+" "+timeend_old,"MM/D/YYYY h:m A");
                            var leaveStart = moment(leave_from+" "+timestart_old,"YYYY-MM-D H:m:s");
                            var leaveEnd = moment(leave_to+" "+timeend_old,"YYYY-MM-D H:m:s");

                        }break;

              case '2': { //1st half of shift
                            var leaveStart = moment(leave_from+" "+timestart_old,"YYYY-MM-D H:m:s");
                            var leaveEnd =  moment(leave_from+" "+timestart_old,"YYYY-MM-D H:m:s").add(4,'hours');


                        }break;
              case '3': { //2nd half
                            var leaveStart =moment(leave_from+" "+timestart_old,"YYYY-MM-D H:m:s").add(5,'hours');
                            // moment(leave_from+" "+timestart_old,"MM/D/YYYY h:m A").add(5,'hours');
                            var leaveEnd = moment(leave_to+" "+timeend_old,"YYYY-MM-D H:m:s");

                            

                        }break;
            }

            var shifts = {leaveStart: leaveStart, leaveEnd: leaveEnd};
            return shifts;


  }//end function getCoveredShifts

});

  


   

  
</script>
<!-- end Page script -->


@stop