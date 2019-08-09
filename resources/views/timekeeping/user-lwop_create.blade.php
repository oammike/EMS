@extends('layouts.main')

@section('metatags')
<title>New Leave Without Pay Request | Employee Management System</title>

@endsection

@section('content')


  <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
       <i class="fa fa-meh-o"></i> New Leave Without Pay
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
            <a href="{{action('UserOBTController@create')}}" class="btn btn-sm  bg-purple"><i class="fa fa-2x fa-briefcase"></i>  OBT</a>

            @else
            <a href="{{action('UserVLController@create',['for'=>$user->id])}}" class="btn btn-sm  bg-blue"><i class="fa fa-plane fa-2x"></i> VL</a>
            <a href="{{action('UserSLController@create',['for'=>$user->id])}}" class="btn btn-sm  btn-danger"><i class="fa fa-2x fa-stethoscope"></i> SL</a>
            <a href="{{action('UserController@show',$user->id)}}#ws"class="btn btn-sm  bg-green"><i class="fa fa-2x fa-calendar-times-o"> </i> CWS</a>
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

                                <div class="info-box bg-yellow">
                                  <span class="info-box-icon"><i class="fa fa-meh-o"></i></span>
                                  <div class="info-box-content">
                                    <span class="info-box-text"><strong class="text-black">Leave Without Pay </strong> details :</span>
                                    <span class="info-box-number"><span class="text-black">Days: </span><span id="credits_vl" data-credits="{{$used}}"> {{$used}} </span> 
                                    </span>

                                    <div class="progress"><?php $progressBar = (1/$creditsLeft)*100; ?>
                                      <div id="percentage" class="progress-bar" style="width: 100%"></div>
                                    </div>
                                    <span class="progress-description text-black">
                                      <span class="pull-left" id="vlfrom">From: {{$vl_from->format('M d l')}} </span> <span id="vlto" class="pull-right"> To: </span><span id="credits_vl" data-credits="{{$used}}" style="visibility: hidden;"></span> 
                                    </span>
                                  </div>
                                  <!-- /.info-box-content -->
                                </div>
                                <!-- /.info-box -->

                              </div><br/>

                              <div class="row">
                                  <div class="col-lg-6">
                                   <label for="vl_from">From: <input required type="text" class="dates form-control datepicker" name="vl_from" id="vl_from" value="{{$vl_from->format('m/d/Y')}}" /></label><div id="alert-vl_from" style="margin-top:10px"></div>
                                 </div>
                                 <div class="col-lg-6" id="shift_choices">
                                    <label ><input type="radio" name="coveredshift" id="shift_whole" value="1" />&nbsp; &nbsp;<i class="fa fa-hourglass"></i> Whole Day</label>
                                    <br/>
                                    <label id="shift_first" ><input type="radio" name="coveredshift" id="shift_first" value="2" />&nbsp; &nbsp;<i class="fa fa-hourglass-start"></i> 1st Half of Shift <span id="shiftFrom_1"></span> </label>
                                     <br/>
                                    <label ><input type="radio" name="coveredshift" id="shift_second" value="3" />&nbsp; &nbsp;<i class="fa fa-hourglass-end"></i> 2nd Half of Shift <span id="shiftFrom_2"></span> </label><br/>
                                </div>
                              </div>

                            </div><!--end options_vl-->
                          
                          <div class="row">
                            <div class="col-lg-12">
                              <a href="#" class="addDays"><i class="fa fa-plus"></i> Add more days</a><br/><br/>
                              <div class="moredays">
                                <div class="row">
                                  <input type="hidden" name="timestart_old2" /><input type="hidden" name="timeend_old2" />
                                  <div class="col-lg-6">
                                    <label for="vl_from">Until: <input required type="text" class="dates form-control datepicker" name="vl_to" id="vl_to" value="" placeholder="MM/DD/YYYY" /></label><div id="alert-vl_from" style="margin-top:10px"></div>
                                  </div>
                                  <div class="col-lg-6">
                                    <div id="vl_more">
                                      <label ><input type="radio" name="coveredshift2" id="shift2_whole" value="1" /> &nbsp; &nbsp;<i class="fa fa-hourglass"></i> Whole Day</label><br/>
                                      <label ><input type="radio" name="coveredshift2" id="shift2_first" value="2" /> &nbsp; &nbsp;<i class="fa fa-hourglass-start"></i> Half Day</label> <br/>
                                      
                                    </div>
                                  </div>
                                </div>
                              </div><!--end moredays-->

                              <label><r/><br/><br/> Notes: </label><br/>
                              <small>Kindly provide a brief description about your leave</small><br/> 
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
                  '_token':_token
                },
                success: function(response){
                  console.log(response);
                  $('input[name="timestart_old"]').val(response.start);
                  $('input[name="timeend_old"]').val(response.end);
                  

                  
                }
              });
    //****** initialize for those with URL param from DTR

    




     $('#save').on('click',function(e){
        e.preventDefault(); e.stopPropagation();
            var _token = "{{ csrf_token() }}";
            var shift = $('select.end.form-control :selected').val();
            var user_id = $(this).attr('data-userid');
            var selectedDate = $(this).attr('data-date');
            var requestType = $(this).attr('data-requesttype');
            var timestart_old1 = $('input[name="timestart_old"]').val();
            var timeend_old1 =  $('input[name="timeend_old"]').val();

            var vl_from = $('input[name="vl_from"]').val(); // MM/dd/YYYY
            var vl_to = $('input[name="vl_to"]').val();

            var coveredshift = $('input[name="coveredshift"]:checked').val();

            var reason_vl = $('textarea[name="reason_vl"]').val();
            var totalcredits = $('#credits_vl').attr('data-credits');

            

            if (vl_to == "" || vl_to == vl_from) //one-day leave lang sya
                  {
                      //check kung anong covered shift
                      
                      
                        var coveredshifts = getCoveredShifts(coveredshift, vl_from, timestart_old1, timeend_old1);
                        var leaveFrom = coveredshifts.leaveStart.format('YYYY-MM-D H:mm:ss');
                        var leaveTo = coveredshifts.leaveEnd.format('YYYY-MM-D H:mm:ss');
                        //var leaveTo = null;
                        console.log("Start: " + leaveFrom);
                        console.log("End: " + leaveTo);

                        if (reason_vl == ""){  $.notify("Please include a brief reason about your leave for HR-Finance's review.",{className:"error", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );return false; }
                        else{

                          //$('input[name="leaveFrom"]').val(leaveFrom);
                             // $('input[name="leaveTo"]').val(leaveTo);

                              console.log("Do ajax");
                              if (totalcredits == '0' || totalcredits =='0.00')
                               {
                                //$.notify("Indicated date is actually a holiday. No need to file for a single-day LWOP for non-operations personnel.",{className:"success", globalPosition:'right middle',autoHideDelay:10000, clickToHide:true} );return false;
                                var reply = confirm("Indicated date is actually a holiday. No need to file for a single-day LWOP for non-operations personnel.\n\nClick OK to proceed if you're from Operations.");
                                console.log(reply);
                                if (reply == true)
                                {
                                  $.ajax({
                                    url: "{{action('UserLWOPController@requestLWOP')}}",
                                    type:'POST',
                                    data:{ 
                                      'id': user_id,
                                      'leaveFrom': leaveFrom,
                                      'leaveTo': leaveTo,
                                      'reason_vl': reason_vl,
                                      'totalcredits': totalcredits,
                                      'halfdayFrom': $('input[name="coveredshift"]:checked').val(),
                                      'halfdayTo': $('input[name="coveredshift2"]:checked').val(),
                                      '_token':_token
                                    },
                                    success: function(response){
                                      $('#save').fadeOut();
                                     // $(this).fadeOut();//prop('disabled',"disabled");

                                      if (response.success == '1')
                                        $.notify("LWOP saved successfully.",{className:"success",globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );
                                        else
                                          $.notify("LWOP submitted for approval.",{className:"success", globalPosition:'right middle',autoHideDelay:3000, clickToHide:true} );
                                      
                                      console.log(response);
                                      window.setTimeout(function(){
                                        window.location.href = "{{action('UserController@userRequests',$user->id)}}";
                                      }, 4000);
                                    }
                                  });
                                }
                              } else {
                                $.ajax({
                                    url: "{{action('UserLWOPController@requestLWOP')}}",
                                    type:'POST',
                                    data:{ 
                                      'id': user_id,
                                      'leaveFrom': leaveFrom,
                                      'leaveTo': leaveTo,
                                      'reason_vl': reason_vl,
                                      'totalcredits': totalcredits,
                                      'halfdayFrom': $('input[name="coveredshift"]:checked').val(),
                                      'halfdayTo': $('input[name="coveredshift2"]:checked').val(),
                                      '_token':_token
                                    },
                                    success: function(response){
                                      
                                     $('#save').fadeOut();

                                      if (response.success == '1')
                                        $.notify("LWOP saved successfully.",{className:"success",globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );
                                        else
                                          $.notify("LWOP submitted for approval.",{className:"success", globalPosition:'right middle',autoHideDelay:3000, clickToHide:true} );
                                      
                                      console.log(response);
                                      window.setTimeout(function(){
                                        window.location.href = "{{action('UserController@userRequests',$user->id)}}";
                                      }, 4000);
                                    }
                                  });

                              }
                              
            
                             
                        }
                      
                  } 
                  else
                  {

                      var mto = moment(vl_to,"MM/D/YYYY").format('YYYY-MM-D');
                      var mfrom = moment(vl_from,"MM/D/YYYY").format('YYYY-MM-D')
                      if ( moment(vl_to,"MM/D/YYYY").isBefore( moment(vl_from,"MM/D/YYYY")) )
                      {
                        
                         $.notify("Invalid 'Until' date. Selected date is past your 'From' date.",{className:"error",globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );


                      }
                      else
                      {

                        if (reason_vl == ""){ $.notify("Please include a brief reason about your leave for HR-Finance's review.",{className:"error", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );return false; }
                        else{

                          console.log("pasok reason_vl");

                              var coveredshifts = getCoveredShifts(coveredshift, vl_from, timestart_old1, timeend_old1);
                              var leaveFrom = coveredshifts.leaveStart.format('YYYY-MM-D H:mm:ss');
                              var coveredshift2 = $('input[name="coveredshift2"]:checked').val();
                              var timestart_old2 = $('input[name="timestart_old2"]').val();
                              var timeend_old2 = $('input[name="timeend_old2"]').val();

                              //console.log(timestart_old2);
                              //console.log(timeend_old2);


                              switch(coveredshift2)
                              {
                                case '1': {var leaveEnd2 = moment(timeend_old2);
                                          leaveTo = leaveEnd2.format('YYYY-MM-D HH:mm:ss');
                                          }break; //wholeday
                                case '2': {var l2 =  moment(timestart_old2); var leaveEnd2 = l2.add(4,'hours');
                                          leaveTo = leaveEnd2.format('YYYY-MM-D HH:mm:ss');
                                          
                                }break; //; var leaveEnd2 = l2.add(60,'minutes').add(4,'hours');}break;
                                default: {var leaveEnd2 = moment(timeend_old2);leaveTo = leaveEnd2.format('YYYY-MM-D HH:mm:ss');}break; //wholeday
                              }
                              
                              //console.log(coveredshifts2);
                              console.log('Leave from: '+ leaveFrom);
                              console.log('Until: '+ leaveTo);


                              $('input[name="leaveFrom"]').val(leaveFrom);
                              $('input[name="leaveTo"]').val(leaveTo);

                              console.log("Do ajax");
                              if (totalcredits == '0' || totalcredits =='0.00')
                               {
                                //$.notify("Indicated date is actually a holiday. No need to file for a single-day LWOP for non-operations personnel.",{className:"success", globalPosition:'right middle',autoHideDelay:10000, clickToHide:true} );return false;
                                var reply = confirm("Indicated date is actually a holiday. No need to file for a single-day LWOP for non-operations personnel.\n\nClick OK to proceed if you're from Operations.");

                                if (reply == true)
                                {
                                  $.ajax({
                                    url: "{{action('UserLWOPController@requestLWOP')}}",
                                    type:'POST',
                                    data:{ 
                                      'id': user_id,
                                      'leaveFrom': leaveFrom,
                                      'leaveTo': leaveTo,
                                      'reason_vl': reason_vl,
                                      'totalcredits': totalcredits,
                                      'halfdayFrom': $('input[name="coveredshift"]:checked').val(),
                                      'halfdayTo': $('input[name="coveredshift2"]:checked').val(),
                                      '_token':_token
                                    },
                                    success: function(response){
                                      $('#save').fadeOut();
                                     

                                      if (response.success == '1')
                                        $.notify("LWOP saved successfully.",{className:"success",globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );
                                        else
                                          $.notify("LWOP submitted for approval.",{className:"success", globalPosition:'right middle',autoHideDelay:3000, clickToHide:true} );
                                      
                                      console.log(response);
                                      window.setTimeout(function(){
                                        window.location.href = "{{action('UserController@userRequests',$user->id)}}";
                                      }, 4000);
                                    }
                                  });
                                }
                              } else{

                                $.ajax({
                                    url: "{{action('UserLWOPController@requestLWOP')}}",
                                    type:'POST',
                                    data:{ 
                                      'id': user_id,
                                      'leaveFrom': leaveFrom,
                                      'leaveTo': leaveTo,
                                      'reason_vl': reason_vl,
                                      'totalcredits': totalcredits,
                                      'halfdayFrom': $('input[name="coveredshift"]:checked').val(),
                                      'halfdayTo': $('input[name="coveredshift2"]:checked').val(),
                                      '_token':_token
                                    },
                                    success: function(response){
                                      $('#save').fadeOut();
                                     

                                      if (response.success == '1')
                                        $.notify("LWOP saved successfully.",{className:"success",globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );
                                        else
                                          $.notify("LWOP submitted for approval.",{className:"success", globalPosition:'right middle',autoHideDelay:3000, clickToHide:true} );
                                      
                                      console.log(response);
                                      window.setTimeout(function(){
                                        window.location.href = "{{action('UserController@userRequests',$user->id)}}";
                                      }, 4000);
                                    }
                                  });
                              }
            
                              
                                  

                        }



                        

                      }
                      

                  }//end else checkIfRestday

                  




          /*}//end if totalcredits == 0 */

                
      });  
     
     $( ".datepicker" ).datepicker({dateFormat:"YYYY-mm-dd"});

     //$('.moredays input').prop('disabled',true);
     $('.moredays').fadeOut(function()
        { 
          //var vl_from = $('input[name="vl_from"]').val(); 
          //getWorkSchedForTheDay(vl_from);

        }); //.css('visibility','hidden');

     $('input[name="coveredshift"]').on('change', function(){
                    var vl_from = $('input[name="vl_from"]').val(); 
                    var vl_to = $('input[name="vl_to"]').val(); 
                    var shift2 = $('input[name="coveredshift2"]:checked').val();
                    var vl_credits = $("span#credits_vl");
                    var theshift = $(this).val();
                    var vl_from1 = moment(vl_from,"MM/DD/YYYY");
                    var creditsleft = null;// $('#creditsleft').attr('data-left');
                    var reason_vl = $('textarea[name="reason_vl"]').val();

                    console.log("vl_from:");
                    console.log(vl_from);
                    console.log("vl_to");
                    console.log(vl_to);
                    console.log("shift2:");
                    console.log(shift2);
                    console.log("theshift");
                    console.log(theshift);

                        //checkIfRestday(vl_from,reason_vl);

                        if (vl_to == "") computeCredits(vl_from1,null,theshift,shift2,creditsleft);
                        else computeCredits(vl_from1,vl_to,theshift,shift2,creditsleft);
                      

      });


     $('#vl_from').on('focusout', function(){
          var vl_from = $('input[name="vl_from"]').val(); 
          var vl_from1 = moment(vl_from,"MM/DD/YYYY");
          var vl_to = $('input[name="vl_to"]').val();
          var toval = moment(vl_from,"MM/DD/YYYY").format('MMM DD ddd');
          var theshift = $('input[name="coveredshift"]:checked').val();
          var shift2 = $('input[name="coveredshift2"]:checked').val();
          var creditsleft = null;
          $("#vlfrom").html("From: "+toval);

          if (vl_to == "") computeCredits(vl_from1,null,theshift,shift2,creditsleft);
          else computeCredits(vl_from1,vl_to,theshift,shift2,creditsleft);

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
      });


      $('#shift_whole, #shift2_whole').prop('checked',true);

      $('.addDays').on("click", function(e){
        e.preventDefault();
        $('#vl_more').hide();
        $('.moredays').fadeIn(); $(this).fadeOut();
        $('.moredays input').prop('disabled',false);
        var creditsleft = 0;

        

        $('#vl_to').on('blur', function(){

          if ($('#vl_to').val() !== "")
          {
            $('#vl_more').fadeIn();
            var _token = "{{ csrf_token() }}";
             $.ajax({
                url: "{{action('UserController@getWorkSchedForTheDay',$user->id)}}",
                type:'POST',
                data:{ 
                 'vl_day': $('#vl_to').val(), 
                  '_token':_token
                },
                success: function(response){
                  console.log(response);
                  $('#pane_VL input[name="timestart_old2"]').val(response.start);
                  $('#pane_VL input[name="timeend_old2"]').val(response.end);
                  
                }
              });
              $('#shift_choices #shift_first').fadeOut();

          } else {  $('#vl_more').fadeOut();  $('#shift_choices #shift_first').fadeIn();}


          var vl_from = $('#vl_from').val();
          var shift_from = $('input[name="coveredshift"]:checked').val();
          var vl_to = $('#vl_to').val();
          var shift_to = $('input[name="coveredshift2"]:checked').val();

          
           // if ( moment(vl_to,"MM/D/YYYY").isBefore( moment(vl_from,"MM/D/YYYY")) )
           //  {
           //    alert("Invalid 'Until' date. Selected date is past your 'From' date.");
           //    return false;
           //  }else{
              computeCredits(vl_from,vl_to,shift_from,shift_to,creditsleft);

              var vl_to2 = $('#vl_to').val();

              var toval = moment(vl_to2,"MM/DD/YYYY").format('MMM DD ddd');
              $("#vlto").html("To: "+toval);

            //}

        });//end vl to focusout


        $('input[name="coveredshift2"]').on('change',function(){
          var selCS = $(this).val();
          var currcredit = null;
          var vl_from = $('#vl_from').val();
          var shift_from = $('input[name="coveredshift"]:checked').val();
          var vl_to = $('#vl_to').val();
          var shift_to = $('input[name="coveredshift2"]:checked').val();
          var creditsleft = 0;

          computeCredits(vl_from,vl_to,shift_from,shift_to,creditsleft);
          

          
        });


  
     
       
         

        
      
      
   });


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
                  url: "{{action('UserLWOPController@getCredits')}}",
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
                                      console.log('response from checkIfRestday:');
                                      console.log(response);
                                      //console.log(response.start);
                                      //console.log(response.end);
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