@extends('layouts.main')


@section('metatags')
  <title>Plot Work Schedule for {{$user->firstname}}</title>
    <meta name="description" content="profile page">
<!-- <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css"> -->
@stop


@section('content')




<section class="content-header">

      <h1>
      Plot Work Schedule
        <small>for {{$user->firstname}} {{$user->lastname}} </small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        
        <li class="active">Plot Employee Work Schedule</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        
        
        
        <div class="col-xs-12">
         
            <div class="box box-primary">

              <div class="box-body box-profile" style="padding-top:50px">
                
                {{ Form::open(['route' => ['fixedSchedule.store'], 'method'=>'post','class'=>'col-lg-12', 'id'=> 'updateForm' , 'name'=>'updateForm']) }}
                <!-- 'novalidate'=>'novalidate' -->
                <input type="hidden" name="user_id" id="user_id" value="{{$user->id}}" />

                <div class="row">

                  <!-- ****** SCHEDULE COL ********** -->
                  <div class="col-lg-7">
                        <blockquote><p class="text-success">Specify type of schedule</p><small> Is this a fixed or a shifting schedule?<br/><br/>
                        <em style="font-size:.9em;">Shifting schedule means employee would have to change his/her work shift at some point in time depending on workforce availability and/or management's discretion.</em><br/></small><br/>

                          <label><input type="radio" name="type" value="FIXED"/> Fixed </label>
                          <label style="margin-left:20px"><input type="radio" name="type" value="SHIFTING"/> Shifting Schedule</label>

                        </blockquote>

                        

                        <blockquote class="steps" id="step2a"><p class="text-success"> Select Effectivity Date</p><small> Indicate the effectivity date of this work schedule.</small><br/><br/>
                            <div class="row">
                                <div class="col-sm-6">
                                  <label>From</label>
                                  <input  type="text" class="dates form-control datepicker" style="width:80%" name="effectivityFrom" id="effectivityFrom" /> 
                                  <div id="alert-effectivityDate" style="margin-top:10px"></div>
                                </div>
                                <div class="col-sm-6">
                                  <label>To</label>
                                  <input  type="text" class="dates form-control datepicker" style="width:80%" name="effectivityTo" id="effectivityTo" /> 
                                  <div id="alert-effectivityDate" style="margin-top:10px"></div>
                                  <a id="nextButton2" class="btn btn-sm btn-default pull-left">Next <i class="fa fa-arrow-right"></i> </a>
                                </div> 

                                <div id="alert-effectivityDate" style="margin-top:10px"></div>
                            </div>
                            <div class="clearfix"></div>

                        </blockquote>
                        <div class="clearfix"></div>

                        <blockquote class="steps" id="step2b1"><p class="text-success"> Select Effectivity Date</p><small> Make this schedule effective starting this date:</small>
                            <div class="row">
                                <div class="col-sm-12">
                                  <label>Effective starting</label>
                                  <input type="text" class="dates1 form-control datepicker" style="width:80%" name="schedEffectivity" id="schedEffectivity" /> 
                                  <div id="alert-schedEffectivity" style="margin-top:10px"></div>
                                </div>
                            </div>
                            <div class="clearfix"></div>

                        </blockquote>

                        <div class="clearfix"></div>



                        <blockquote class="steps" id="step2b"><p class="text-success">Rest Days</p><small> Indicate employee's day off for this schedule.</small><br/><br/>
                          <div class="row">
                            <div class="col-sm-6">
                              <?php for ($ctr=0; $ctr <= 6; $ctr++){?>
                              <label><input data-id="{{$ctr}}" type="checkbox" name="restdays[]" value="{{$ctr}}"> {{jddayofweek($ctr,1) }} </label><br/> <!--$weekdays[$ctr] -->
                              <?php }?> 
                              <div id="alert-restdays" style="margin-top:10px"></div>
                            </div>
                          <div class="col-sm-6"><a id="nextButton" class="btn btn-sm btn-default">Next <i class="fa fa-arrow-right"></i> </a></div>
                        </div>


                        </blockquote>
                        <div class="clearfix"></div>



                        <blockquote class="steps" id="step3a"><p class="text-success">Indicate Work Shifts</p><small> Specify the work day(s) and time of this shift.</small><br/>
                         
                         
                          <div class="row">
                            <div class="col-md-12">
                                 <div id="addShifts" style="margin-top:5px"></div>
                                 <br/><br/>
                                 
                            </div>
                           </div>
                         

                          


                        </blockquote>
                  </div><!--end 1st col -->
                  <!-- ****** END SCHEDULE COL ********** -->

                  
                   <!-- ****** SCHED CARD COL ********** -->
                  <div class="col-lg-5">
                     <!-- Widget: user widget style 1 -->
                     <h3 class="text-center">Work Schedule for: </h3>
                      <div class="box box-widget widget-user-2">
                        <!-- Add the bg color to the header using any of the bg-* classes -->
                        <div class="widget-user-header bg-green"><a href="{{action('UserController@show',$user->id)}}">
                          <div class="widget-user-image">
                            <img class="img-circle" src="{{$img}} " alt="User Avatar">
                          </div></a>
                          <!-- /.widget-user-image -->
                          <h3 class="widget-user-username"> {{$user->firstname}} {{$user->lastname}} </h3>
                          <h5 class="widget-user-desc">{{$user->position->name}} </h5>
                        </div>
                        <div class="box-footer no-padding">
                          <ul class="nav nav-stacked">
                            <li><a href="#"><i class="fa fa-calendar"></i>&nbsp;&nbsp; Type of Schedule <span id="result_type" class="pull-right badge bg-black"></span></a></li>
                            <li id="effectiveOn"><a href="#"><i class="fa fa-thumbs-up"></i>&nbsp;&nbsp; Effecitivity <span class="pull-right badge bg-black"></span></a></li>
                            <li><a href="#"><i class="fa fa-bed"></i>&nbsp;&nbsp; Rest Days <span id="result_RD" class="pull-right badge bg-black"></span></a></li>
                            <div class="clearfix"></div>
                            <li><a href="#"><i class="fa fa-exchange"></i>&nbsp;&nbsp; Flexi Time <span id="result_flexi" class="pull-right badge bg-black"></span></a></li>
                            <li><a href="#"><i class="fa fa-clock-o"></i>&nbsp;&nbsp; Work Shifts <table style="margin-top:15px" class="table" id="result_shift"></table></a></li>
                            
                            
                          </ul>
                        </div><br/><br/>
                      </div>

                      <!-- <a id="saveSched" style="display:none" href="" class="btn btn-md btn-danger pull-right"><i class="fa fa-save"></i> Save Schedule</a> -->
                      <button type="submit" id="saveSched" style="display:none" class="btn btn-md btn-danger"><i class="fa fa-save"></i> Save Schedule</button>
                      <br/><br/>
                      <div id="alert-submit" style="margin-top:10px"></div>
                      <!-- /.widget-user -->

                      <br/><br/>

                      @if(count($teammates)>0)
                      <blockquote class="steps" id="step5"><p class="text-success">(Optional) Appy this Same sched to...</p><small> Select employees who will also have this same schedule and then click "Save Schedule":</small><br/>

                       @foreach ($teammates as $pip)
                       <label style="margin-left:20px; width:90%; font-size:0.7em;border-bottom:1px dotted #777 "><input type="checkbox"  name="applySchedTo[]" value="{{$pip['id']}}"/><img class="img-circle pull-left" width="50" src="{{$pip['pic']}}" />&nbsp;&nbsp; {{$pip['lastname']}}, {{$pip['firstname']}}<br/><small style="font-size:0.75em;">{{$pip['position']}}</small> </label><br/>
                       @endforeach
                     </blockquote>
                     @endif

                      <blockquote class="steps" id="step4">
                        <p class="text-success"><!-- Flexi Time?</p><small> Applicable mostly to managerial positions only.</small><br/> -->

                                <!--  <label><input type="hidden" name="isFlexitime" value="YES"/> Yes </label> -->
                                 <label style="margin-left:20px"><input type="hidden"  name="isFlexitime" value="NO"/></label>
                               </blockquote>
                  </div><!--end 2nd col-->
                  <!-- ****** END SCHED CARD COL ********** -->

                </div><!--end main row-->



                
                  

                
                {{Form::close()}}

              </div>
              

              <!-- /.box-body -->
            </div>

        </div>
         

        
      </div>

     


      <div class="modal fade" id="plantCamote" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title text-success" id="myModalLabel"> All Day Rest Day??</h4>
              
            </div>
            <div class="modal-body-upload">
             <form class="form-control" style="border:none">

              <br/><br/>

               <h4 class="text-center">Wow! Everyday is a rest day.<br/><i class="fa fa-leaf text-success"></i> <small> Go home and plant some camote intead. <i class="fa fa-leaf text-success"></i><br/>Please, make yourself useful.</small> </h4><br/><br/> 
              
              
              
              <button type="button" class="btn btn-default btn-md pull-right " data-dismiss="modal"> <i class="fa fa-times"></i> Okay, fine.</button>
            </form>

            </div>

            <div class="modal-body-generate"></div>
            <div class="modal-footer no-border">
              
            </div>
          </div>
        </div>
      </div>

     

    </section>

@stop

@section('footer-scripts')

 
<script>
$(function () {
    $( ".datepicker" ).datepicker({dateFormat:"YYYY-mm-dd"});
    $(".steps").hide();
    $('#effectiveOn').hide();

    $('input[name="type"]').on('click', function(){

      var shiftType = $(this).val();

      switch(shiftType){
        case "FIXED": { 
                        $('#step2b1').fadeIn();
                        $('#step2a').hide();
                        $('#result_type').html("FIXED");
                        $('#effectiveOn').fadeOut();
                        $('#updateForm').attr('action', "{{action('FixedScheduleController@store')}}");

                      }break;

        case "SHIFTING": { 

                          $('#step2a').fadeIn();
                          $('#step2b').hide();
                          $('#step2b1').hide();
                          $('#step3a').hide();
                          $('#result_type').html("SHIFTING");
                          $('#effectiveOn').fadeIn();
                          $('#updateForm').attr('action', "{{action('MonthlyScheduleController@store')}}");

                          }break;
      }

      

    });

    $('input[name="isFlexitime"]').on('click', function(){
      var flexiTime = $(this).val();
      var flexiTime = "NO";
      $('#saveSched').fadeIn();
      @if(count($teammates)>0)$('#step5').fadeIn();@endif
      //$('#result_flexi').html(flexiTime);
      updateScheduleCard();
    });

    

   $('input[name="restdays[]"]').on('click',function(){

        
        var dayChecked = $(this).attr("value");
        var idChecked = $(this).attr("data-id");
        var init_rdselected = $('input[name="restdays[]"]:checkbox:checked').length;
        var notRD = $('input[name="restdays[]"]:checkbox:not(:checked)').map(function(){
                    return this.value;
                  }).get();
        

        var RDstring = ""; 
        var restdays = $('input[name="restdays[]"]:checkbox:checked').map(function() {
                  return this.value;
              }).get();

       
       // console.log("RDstring: "+ restdays);

        $.each( restdays, function( key, value ) {
              var weekdays = ["Mondays", "Tuesdays", "Wednesdays", "Thursdays", "Fridays", "Saturdays","Sundays"];
              RDstring += weekdays[value]+"<br/>";
             
              //console.log("check " + weekdays[value]);


            }); $('#result_RD').html(RDstring);


        //------ setup now the work schedules based from non RDs
                $('#addShifts').html('');
                $('#addShifts').fadeIn();
                
                
              
      

              $.each( notRD, function( key, value ) {
                    var weekdays = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday","Sunday"];
                    var htmlcode ='<div class="row">';
                    htmlcode += '<div class="col-lg-3">';
                    htmlcode += '<input type="hidden" name="selectedWorkDays[]" id="selectedWorkDays" value="'+weekdays[value]+'" />';
                    htmlcode += '                                                     <select name="workday[]" class="days form-control" style="margin-bottom:5px">';
                    htmlcode += "<option value=\""+value+"\">"+weekdays[value]+"</option>";
                    htmlcode += '                                      </select></div><div class="col-lg-6">';
                    htmlcode +='<label style="font-size:x-small"><input required="required" type="radio" class="schedtype" data-week="'+weekdays[value]+'" name="schedtype_'+weekdays[value]+'" value="full">&nbsp; Full time </label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label style="font-size:x-small"><input required="required" type="radio" data-week="'+weekdays[value]+'" class="schedtype" name="schedtype_'+weekdays[value]+'" value="part">&nbsp; Part time</label></div><div class="col-lg-4">';
    
                                                                      
                    htmlcode +='                                                   <select required id="scheds_'+weekdays[value]+'"  name="timeEnd[]" class="end form-control" style="margin-bottom:5px" disabled="disabled"><option class="none" value="0">* Select Work Shift *</option>';
                                                                        @foreach ($shifts as $shift)
                    htmlcode +='                                                       <option class="full" value="{{$shift}}">{{$shift}} </option>';
                                                                        @endforeach

                                                                         @foreach ($partTimes as $shift2)
                    htmlcode +='                                                       <option class="parttime" value="{{$shift2}}">{{$shift2}} </option>';
                                                                        @endforeach
                    htmlcode +='                                                   </select></div>';
             
                $('#addShifts').append(htmlcode);

                   // console.log('appended: '+weekdays[value]);
                  }); 

              

        $('.schedtype').on('click', function(){
          var chosentype = $(this).val();
          var dday = $(this).attr('data-week');

          switch(chosentype){
            case 'full':{ $('select#scheds_'+dday).prop('disabled',false); $('select#scheds_'+dday+' option.none').prop('selected','selected'); $('option.full').css('display','block'); $('option.parttime').css('display','none'); } break;
            case 'part':{ $('select#scheds_'+dday).prop('disabled',false); $('select#scheds_'+dday+' option.none').prop('selected','selected'); $('option.parttime').css('display','block'); $('option.full').css('display','none');} break;

          }
        });

        //------ end setup work schedule


        if ( $('input[name="restdays[]"]:checked').length > 0)
        {
            $('#step3a').fadeIn();
            //and then remove the next button
            $('#nextButton').fadeOut();

        } else $('#step3a').fadeOut();


        var rdselected = $('input[name="restdays[]"]:checkbox:checked').length;

        if (rdselected == 7)
        {
          //alert('Wow! Everyday is a rest day. Go home and plant camotes instead...');
          $('#plantCamote').modal('toggle');
          $('#step3a').fadeOut();
        } 
        if (checkIfStillAllowed()){
          $('#addShift').fadeIn();
        } 
        else //we need to remove yung added RD from the list of added 
        {
          var totalSelect = $('select.days.form-control').length;
          var toRemove =  totalSelect+1 - init_rdselected;
          // console.log("totalSelect: "+ totalSelect);
          // console.log("init_rdselected: "+ init_rdselected);
          // console.log("Toremove: "+toRemove);

          
          //  $("#addShifts div.row:last-child").remove();
        

        }
        updateScheduleCard();

        
   });





    $(document).on('blur','input.dates',function(){

      var effectiveFrom = $('#effectivityFrom').val().length;
      var effectiveTo = $('#effectivityTo').val().length;

      var effectFrom = $('#effectivityFrom').val();
      var effectiveTo = $('#effectivityTo').val();

      if ((effectiveTo == 0) || (effectivityFrom == 0))
      {
        $('#step2b').hide();$('#step2b1').hide();$('#step3a').hide(); $('#nextButton2').fadeOut();} 

        else 
      { 
        $('#step2b').fadeIn(); 
        $('#nextButton').fadeIn();
        $('#effectiveOn').html('<a href="#"><i class="fa fa-thumbs-up"></i>&nbsp;&nbsp; Effecitivity <span class="pull-right badge bg-black">'+effectFrom+' - '+effectiveTo+'</span></a>');
      }

    });

    $(document).on('blur','input.dates1',function(){

      var effectiveFrom = $('#schedEffectivity').val().length;
      
      var effectFrom = $('#schedEffectivity').val();
    
      if (effectivityFrom == 0)
      {
        $('#step2b').hide();$('#step3a').hide(); $('#nextButton2').fadeOut();} 

        else 
      { 
        $('#step2b').fadeIn(); 
        $('#nextButton').fadeIn();
        $('#effectiveOn').html('<a href="#"><i class="fa fa-thumbs-up"></i>&nbsp;&nbsp; Effecitivity <span class="pull-right badge bg-black">'+effectFrom+' </span></a>');
      }

    });




    $('#nextButton').on('click', function(){ $('#step3a').fadeIn(); $(this).fadeOut();});
    $('#nextButton2').on('click', function(){ $('#step3a').fadeIn(); $(this).fadeOut();});

    



    $(document).on('change','select.end.form-control',function(){
        if(checkIfStillAllowed()) //$('#saveSched').fadeIn();
        updateScheduleCard();
        $('#step4').fadeIn();
        $('#saveSched').fadeIn();

        @if(count($teammates)>0)$('#step5').fadeIn();@endif
        //$('#result_flexi').html(flexiTime);
        updateScheduleCard();

    }); //end timeEnd check if on change

    $(document).on('change','select',function(){updateScheduleCard();});




   


function checkIfStillAllowed()
{

       var restdays = $('input:checkbox:checked').map(function() {
                 // console.log(this.value);
                  return this.value;
              }).get();
       var workdays = $('select[name="workday[]"] :selected').val(); //.map(function(i, el) {
       var addDays =  $('select[name="workday[]"] :selected').val();
       var totalSelect = $('select.days.form-control');


        var RD = jQuery.makeArray(restdays);
        var WD = jQuery.makeArray(workdays);
        var AD = jQuery.makeArray(addDays);

        //--- get total added workdays, if more than non-RD then enough of adding na
        var allowed = 7-RD.length;

        // console.log("totalSelect: "+totalSelect.length);
        // console.log('allowed: '+ allowed);

        if(totalSelect == allowed) return false;
        else if (totalSelect.length < allowed) return true;
        else return false; 
         
        

}

function updateScheduleCard()
{
  // ------- add in the work shifts to the employee card
            
            var daysOfWeek = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday","Sunday"];
            var selectedDays = $('select.days.form-control :selected').map(function(i, el) {
              return $(el).val();
            });
            // var selectedStart = $('select.start.form-control :selected').map(function(i, el) {
            //   return $(el).val();
            // });
            var selectedEnd = $('select.end.form-control :selected').map(function(i, el) {
              return $(el).val();
            });

            //$schedTable = '<table class="table"><tr>';
            var ct=0;
            var schedString = "";
            //$('#result_shift').html(schedString);
            $.each(selectedDays,function(key, val){
              schedString += "<tr><td><strong>"+daysOfWeek[val]+"</strong></td><td>"+ selectedEnd[ct]+"</td></tr>";
              
              ct++;
            });

            $('#result_shift').html(schedString+"</table>");
         

            // ------- end add in the work shifts to the employee card
}






});

</script>
@stop