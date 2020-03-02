@extends('layouts.main')

@section('metatags')
<title>New Vacation Leave Request | Employee Management System</title>

@endsection

@section('content')


  <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
       <i class="fa fa-plane"></i> New Vacation Leave
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
                                  <span class="info-box-icon"><i class="fa fa-plane"></i></span>
                                  <div class="info-box-content">
                                    <span class="info-box-text"><strong>Vacation Leave</strong> details :</span>
                                    <span class="info-box-number"><span class="text-gray">Used: </span><span id="credits_vl" data-credits="{{$used}}"> {{$used}} </span> 
                                    <span class="pull-right" id="creditsleft" style="color:#fff11b" data-left="{{$creditsLeft}}">{{$creditsLeft}} </span>
                                    <span class="pull-right text-gray"> Remaining: &nbsp;&nbsp; </span> </span>

                                    @if($creditsLeft > 0)
                                    <div class="progress"><?php $progressBar = (1/$creditsLeft)*100; ?>
                                    @else
                                    <div class="progress"><?php $progressBar = 0; ?>
                                    @endif
                                      <div id="percentage" class="progress-bar" style="width: {{ 100-$progressBar }}%"></div>
                                    </div>
                                    <span class="progress-description">
                                      <span class="pull-left" id="vlfrom">From: {{$vl_from->format('M d l')}} </span> <span id="vlto" class="pull-right"> To: </span>
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
                              <small>Kindly provide a brief description about your vacation leave</small><br/> 
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

            var mfrom = moment(vl_from,"MM/D/YYYY").format('YYYY-MM-D');
            var twoweeks = moment(vl_from,"MM/D/YYYY").add(-14,'days');

            /* ------- 2wk NOTICE on VL
            if ( twoweeks.format('YYYY-MM-DD') <= moment().format('YYYY-MM-DD')) {
              $.notify("Sorry, this VL period is already past the submisison deadline. \nNote that all vacation leaves must be filed 2-weeks in advance. \n(i.e. before "+twoweeks.format('MMM D, YYYY')+")",{className:"error", globalPosition:'right middle',autoHideDelay:25000, clickToHide:true} );return false; 
            }*/


            console.log('two weeks');console.log(twoweeks);

            $.ajax({
                        url: "{{action('UserController@getWorkSchedForTheDay',$user->id)}}",
                        type:'POST',
                        async:false,
                        data:{ 
                         'vl_day': vl_from, 
                         'isStylized':false,
                          '_token':_token
                        },
                        success: function(response){
                          console.log('from getWorkSchedForTheDay');
                          console.log(response);
                          if (response.timeStart === response.timeEnd || response.isRD == 1)
                          {
                            alert("\n\nNo need to file for a vacation leave. \nThe selected date falls on a REST DAY."); return false;
                          }
                          else
                          {
                            /*if (totalcredits == '0' || totalcredits =='0.00')
                             {
                              $.notify("Indicated date is actually a holiday. No need to file for a single-day VL for non-ops personnel.\n\n For those in Operations, please file this as an LWOP instead.",{className:"success", globalPosition:'right middle',autoHideDelay:10000, clickToHide:true} );return false;
                                
                             }
                              
                            else
                            {*/
                              if (vl_to == "" || vl_to == vl_from) //one-day leave lang sya
                              {
                                  //check kung anong covered shift

                                  var mfrom = moment(vl_from,"MM/D/YYYY").format('YYYY-MM-D');
                                
                                  var coveredshifts = getCoveredShifts(coveredshift, mfrom,mfrom, timestart_old1, timeend_old1);
                                  var leaveFrom = coveredshifts.leaveStart.format('YYYY-MM-D H:mm:ss');
                                  var leaveTo = coveredshifts.leaveEnd.format('YYYY-MM-D H:mm:ss');
                                  console.log("Start: " + leaveFrom);
                                  console.log("End: " + leaveTo);

                                  var mayExisting = checkExisting(leaveFrom,_token);
                                  console.log('mayexisting:');
                                  console.log(mayExisting);

                                  if (mayExisting)
                                  {
                                    $.notify("An existing leave has already been filed for that date.\nIf you wish to file a new one, go to employee\'s DTR Requests page and cancel previously submitted leave.",{className:"error", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );return false; 

                                  }
                                  else
                                  {
                                    if (reason_vl == ""){  $.notify("Please include a brief reason about your leave for HR-Finance's review.",{className:"error", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );return false; }
                                    else
                                    {

                                      $('input[name="leaveFrom"]').val(leaveFrom);$('input[name="leaveTo"]').val(leaveTo);

                                          console.log("Do ajax");
                        
                                          $.ajax({
                                                url: "{{action('UserVLController@requestVL')}}",
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
                                                success: function(response1){
                                                  $('#save').fadeOut();
                                                 

                                                  if (response1.success == '1') {
                                                    $.notify("Vacation Leave saved successfully.",{className:"success",globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );
                                                  }else if (response1.success == '-1'){
                                                    $.notify(response1.message,{className:"success",globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );
                                                  }else
                                                      $.notify("Vacation Leave submitted for approval.",{className:"success", globalPosition:'right middle',autoHideDelay:3000, clickToHide:true} );
                                                  
                                                  console.log(response1);
                                                  window.setTimeout(function(){
                                                    window.location.href = "{{action('UserController@userRequests',$user->id)}}";
                                                  }, 4000);
                                                }
                                              });
                                              


                                    }


                                  }//end if mayexisting



                                          
                                        
                              } 
                              else
                              {

                                  var mto = moment(vl_to,"MM/D/YYYY").format('YYYY-MM-D');
                                  var mfrom = moment(vl_from,"MM/D/YYYY").format('YYYY-MM-D')
                                  if ( moment(vl_to,"MM/D/YYYY").isBefore( moment(vl_from,"MM/D/YYYY")) )
                                  {
                                    
                                     $.notify("Invalid 'Until' date. Selected date is past your 'From' date.",{className:"error",globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );


                                    console.log("mto: ");
                                    console.log(mto);
                                    console.log("mfrom: ");
                                    console.log(mfrom); //return false;
                                  }
                                  else
                                  {
                                    var coveredshifts = getCoveredShifts(coveredshift, mfrom,mto, timestart_old1, timeend_old1);
                                    var leaveFrom = coveredshifts.leaveStart.format('YYYY-MM-D H:mm:ss');
                                    var leaveTo = coveredshifts.leaveEnd.format('YYYY-MM-D H:mm:ss');
                                    var coveredshift2 = $('input[name="coveredshift2"]:checked').val();
                                    var timestart_old2 = $('input[name="timestart_old2"]').val();
                                    var timeend_old2 = $('input[name="timeend_old2"]').val();

                                    var mayExisting = checkExisting(leaveFrom,_token);
                                    console.log('mayexisting:');
                                    console.log(mayExisting);

                                    if (mayExisting)
                                    {
                                      $.notify("An existing leave has already been filed covering those dates.\nIf you wish to file a new one, go to employee\'s DTR Requests page and cancel previously submitted leave.",{className:"error", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );return false; 

                                    }
                                    else
                                    {

                                      if (reason_vl == ""){ $.notify("Please include a brief reason about your leave for HR-Finance's review.",{className:"error", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );return false; }
                                      else
                                      { 
                                            console.log('Leave from: '+ leaveFrom);
                                            console.log('Until: '+ leaveTo);


                                            $('input[name="leaveFrom"]').val(leaveFrom);
                                            $('input[name="leaveTo"]').val(leaveTo);

                                            console.log("Do ajax");
                          
                                            $.ajax({
                                                  url: "{{action('UserVLController@requestVL')}}",
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
                                                  success: function(response2){
                                                    
                                                    $('#save').fadeOut();

                                                    if (response2.success == '1')
                                                      $.notify("Vacation Leave saved successfully.",{className:"success",globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );
                                                      else
                                                        $.notify("Vacation Leave submitted for approval.",{className:"success", globalPosition:'right middle',autoHideDelay:3000, clickToHide:true} );
                                                    
                                                    console.log(response2);
                                                    window.setTimeout(function(){
                                                      window.location.href = "{{action('UserController@userRequests',$user->id)}}";
                                                    }, 2000);
                                                  }
                                                });
                                                

                                      }


                                    }//end if else may existing




                                    

                                  }//end if else invalid moment
                                  

                              }//end else checkIfRestday

                                    




                           // }//end if totalcredits == 0

                          }//end if else no need to file
                          

                          
                        }
            });


         

             

            

                
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
                    if (theshift == '1')
                    var creditsleft = "{{$creditsLeft}}";
                    else {
                      var creditsleft =  {{$creditsLeft}};
                      creditsleft += 0.5;
                    }

                    console.log("creditsleft: ");
                    console.log(creditsleft);

                    checkIfRestday(vl_from);

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


      $('#shift_whole, #shift2_whole').prop('checked',true);

      $('.addDays').on("click", function(e){
        e.preventDefault();
        $('#vl_more').hide();
        $('.moredays').fadeIn(); $(this).fadeOut();
        $('.moredays input').prop('disabled',false);
        var creditsleft = '{{$creditsLeft}}';

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
                 'isStylized':false,
                  '_token':_token
                },
                success: function(response){
                  console.log(response);
                  $('#pane_VL input[name="timestart_old2"]').val(response.timeStart);
                  $('#pane_VL input[name="timeend_old2"]').val(response.timeEnd);
                  
                }
              });
              $('#shift_choices #shift_first').fadeOut();

          } else {  $('#vl_more').fadeOut();  $('#shift_choices #shift_first').fadeIn();}


          var vl_from = $('#vl_from').val();
          var shift_from = $('input[name="coveredshift"]:checked').val();
          var vl_to = $('#vl_to').val();
          var shift_to = $('input[name="coveredshift2"]:checked').val();

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
          var creditsleft = '{{$creditsLeft}}';

          computeCredits(vl_from,vl_to,shift_from,shift_to,creditsleft);
          

          
        });

      }); //end onclick addDays



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