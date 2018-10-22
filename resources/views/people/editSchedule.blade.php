@extends('layouts.main')


@section('metatags')
  <title>Edit {{$user->firstname}}'s Schedule</title>
    <meta name="description" content="profile page">

@stop


@section('content')




<section class="content-header">

      <h1>
      Employee Profile
        <small></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        
        <li class="active">Employee Profile</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-1"></div>
        <div class="col-xs-10">
          


          <!-- Profile Image -->
                          <div class="box box-primary">

                            <div class="box-body box-profile">
                               <!-- Widget: user widget style 1 -->
                                    <div class="box box-widget widget-user">
                                      <!-- Add the bg color to the header using any of the bg-* classes -->
                                      <div class="widget-user-header bg-black" style="background: url('{{URL:: asset("public/img/bg.jpg")}}') top left;">
                                        <h3 class="widget-user-username">{{$user->lastname}}, {{$user->firstname}} {{$user->middlename}}</h3>
                                        <h5 class="widget-user-desc">{{$user->position->name}} </h5>
                                      </div>
                                      <div class="widget-user-image">
                                        
                                         @if ( file_exists('public/img/employees/'.$user->id.'.jpg') )
                                          <img src="{{asset('public/img/employees/'.$user->id.'.jpg')}}" class="user-image" alt="User Image">
                                          @else
                                          <img src="{{asset('public/img/useravatar.png')}}" class="user-image" alt="User Image">

                                            @endif

                                          
                                        
                                      </div>
                                      <div class="box-footer">
                                        <div class="row">
                                          <div class="col-sm-6 border-right">
                                            <div class="description-block">
                                              <p class="description-header"><i class="fa fa-users margin-r-5"></i> Department / Program : </p>
                                              <span class="description-text text-primary">
                                              @if(count($user->campaign) > 1)

                                                  @foreach($user->campaign as $ucamp)
                                                      {{$ucamp->name}} , 

                                                  @endforeach

                                              @else
                                              {{$user->campaign[0]->name}}

                                              @endif
                                              </span>
                                            </div>
                                            <!-- /.description-block -->
                                          </div>
                                          <!-- /.col -->
                                          <div class="col-sm-6 ">
                                            <div class="description-block">
                                              <p class="description-header"><i class="fa fa-envelope-o margin-r-5"></i> E-mail:</p>
                                              <span><a href="mailto:{{$user->email}}"> {{$user->email}}</a></span>
                                            </div>
                                            <!-- /.description-block -->
                                          </div>
                                          <!-- /.col -->

                                          <!-- START CUSTOM TABS -->
     

                                          <p>&nbsp;</p><p>&nbsp;</p>
                                          <div class="row">
                                            <div class="col-lg-1 col-sm-12"></div>
                                            <div class="col-lg-10 col-sm-12">
                                              <!-- Custom Tabs -->
                                              <div class="nav-tabs-custom">
                                                <ul class="nav nav-tabs">
                                                  <li class="active"><a href="#tab_2" data-toggle="tab">EDIT WORK SCHEDULE</a></li>
                                                 
                                                 
                                                  <li class="dropdown pull-right">
                                                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                                                     <i class="fa fa-gear"></i> Actions <span class="caret"></span>
                                                    </a>
                                                    <ul class="dropdown-menu">
                                                      @if ($canMoveEmployees)
                                                      <li role="presentation"><a role="menuitem" tabindex="-1" href="{{action('MovementController@changePersonnel',$user->id)}}">Movements</a></li>@endif
                                                       @if ($canEditEmployees)
                                                       <li role="presentation"><a role="menuitem" tabindex="-1" href="{{action('UserController@editUser',$user->id)}}">Edit Employment Data</a></li>@endif

                                                       <li role="presentation"><a role="menuitem" tabindex="-1" href="{{action('UserController@editSchedule',$user->id)}}">Edit Work Schedule</a></li>
                                                       <li role="presentation"><a role="menuitem" tabindex="-1" href="{{action('UserController@editContact',$user->id)}}">Edit Contact Info</a></li>
                                                      
                                                      <li role="presentation" class="divider"></li>
                                                      <li role="presentation"><a role="menuitem" tabindex="-1" href="#">Change Status</a></li>
                                                    </ul>
                                                  </li>
                                                  
                                                </ul>
                                                <div class="tab-content">
                                                   {{ Form::open(['route' => ['user.updateSchedule', $user->id], 'method'=>'put','class'=>'col-lg-12', 'id'=> 'updateForm' ]) }}
                                                   <input type="hidden" name="user_id" value="{{$user->id}} " />
                                                 


                                                  <div class="tab-pane active" id="tab_2">
                                                    <div id="alert-submit" style="margin-top:10px"></div>
                                                    <h4 style="margin-top:30px">
                                                      <input type="submit" id="submit" href="#" class="btn btn-sm btn-success pull-right" value="Save Changes"></input>
                                                      <i class="fa fa-clock-o"></i> WORK SCHEDULE <br/><br/></h4>
                                                    <div class="row">
                                                      <div class="col-lg-7"><strong> Shifts: </strong> <a href="#" id="addShift" class="btn btn-xs btn-primary "><i class="fa fa-plus"></i> Add Work Shift</a> <br/>
                                                        <table class="table no-border">

                                                          <tr>
                                                            <th>Day</th>
                                                            <th class="text-center">In</th>
                                                            <th class="text-center">Out</th>
                                                            <th></th>
                                                          </tr>
                                                         

                                                        @foreach($user->schedules as $sched)
                                                        <tr>
                                                         <td>

                                                            <select data-id="{{$sched->id}}" name="workday[]" class="form-control">
                                                              <option data-id="{{$sched->id}}" value="Sundays" @if ( $sched->workday == "Sundays" ) selected="selected" @endif >Sundays</option>
                                                              <option data-id="{{$sched->id}}" value="Mondays" @if ( $sched->workday == "Mondays" ) selected="selected" @endif >Mondays</option>
                                                              <option data-id="{{$sched->id}}" value="Tuesdays" @if ( $sched->workday == "Tuesdays" ) selected="selected" @endif >Tuesdays</option>
                                                              <option data-id="{{$sched->id}}" value="Wednesdays" @if ( $sched->workday == "Wednesdays" ) selected="selected" @endif >Wednesdays</option>
                                                              <option data-id="{{$sched->id}}" value="Thursdays" @if ( $sched->workday == "Thursdays" ) selected="selected" @endif >Thursdays</option>
                                                              <option data-id="{{$sched->id}}" value="Fridays" @if ( $sched->workday == "Fridays" ) selected="selected" @endif >Fridays</option>
                                                              <option data-id="{{$sched->id}}" value="Saturdays" @if ( $sched->workday == "Saturdays" ) selected="selected" @endif >Saturdays</option>

                                                              
                                                            </select></td> 
                                                         <td>
                                                          
                                                          <select name="timeStart[]" class="form-control">
                                                            <?php  for( $i = 1; $i <= 24; $i++){ $min=0; $time1 = date('h:i A', strtotime('1999-01-01'.$i.":".$min.":00")); $min+=30; $time2 = date('h:i A', strtotime('1999-01-01'.$i.":".$min.":00")); ?>
                                                              <option @if( date('h:i A', strtotime('1999-01-01'. $sched->timeStart)) == $time1 ) selected="selected" @endif value="{{$time1}}">{{$time1}} </option>
                                                              <option @if( date('h:i A', strtotime('1999-01-01'. $sched->timeStart)) == $time2 ) selected="selected" @endif value="{{$time2}}">{{$time2}} </option>
                                                            <?php } ?>
                                                          </select></td>

                                                          <td>
                                                          
                                                          <select name="timeEnd[]" class="form-control">
                                                            <?php  for( $i = 1; $i <= 24; $i++){ $min=0; $time1 = date('h:i A', strtotime('1999-01-01'.$i.":".$min.":00")); $min+=30; $time2 = date('h:i A', strtotime('1999-01-01'.$i.":".$min.":00")); ?>
                                                              <option @if( date('h:i A', strtotime('1999-01-01'. $sched->timeEnd)) == $time1 ) selected="selected" @endif value="{{$time1}}">{{$time1}} </option>
                                                              <option @if( date('h:i A', strtotime('1999-01-01'. $sched->timeEnd)) == $time2 ) selected="selected" @endif value="{{$time2}}">{{$time2}} </option>
                                                            <?php } ?>
                                                          </select>
                                                       
                                                      </td>
                                                      <td> <a title="Delete" data-toggle="modal" data-target="#myModal{{$sched->id}}" class="btn btn-sm btn-default pull-left"><i class="fa fa-trash"></i></a></td>
                                                       </tr>
                                                      

                                                       
                                                        @endforeach

                                                        
                                                         </table>

                                                         <div id="addShifts"></div>
                                                         </div>
                                                      <div class="col-lg-3">
                                                       
                                                        <strong> Rest Days: </strong>
                                                      <br/> <br/> <br/>
                                                        <?php $ctr=0;?>
                                                        @foreach($days as $d)
                                                         <label><input data-id="{{$ctr}}" type="checkbox" name="restdays[]" value="{{$d}}" @if ($user->restdays->contains('RD',$d)) checked="checked" @endif> {{$d}} </label><br/>
                                                         <?php $ctr++;?>
                                                         @endforeach


                                                        
                                                       </div>

                                                       <div class="col-lg-2"><strong>Flexible schedule</strong><br/><br/>
                                                        <label>  <input type="radio" name="isFlexi" value="1" @if(count($user->schedules) > 0) @if($user->schedules->first()->isFlexi) checked="checked" @endif @endif></input> Yes </label> 
                                                       <label style="margin-left:10px"> <input type="radio" name="isFlexi" value="0" @if ( (!count($user->schedules) > 0) || ($user->schedules->first()->isFlexi !=1) )  checked="checked" @endif></input> No</label> </div>

                                                       
                                                    </div>
                                                    
                                                  

                                                  </div>
                                                  <!-- /.tab-pane -->



                                                   {{Form::close()}}
                                                </div>
                                                <!-- /.tab-content --> 
                                              </div>
                                              <!-- nav-tabs-custom -->
                                            </div>
                                            <!-- /.col -->
                                            <div class="col-lg-1 col-sm-12"></div>

                                           
                                          </div>
                                          <!-- /.row -->
                                          <!-- END CUSTOM TABS -->
                                          
                                          <!-- /.col -->
                                          

                                      </div>
                                    </div>
                                    <!-- /.widget-user -->

                            </div>
                            

                            <!-- /.box-body -->
                          </div>

        </div>
        <div class="col-xs-1"></div>

      </div>

      @foreach($user->schedules as $sched)
      @include('layouts.modals', [
                          'modelRoute'=>'schedule.destroy',
                          'modelID' => $sched->id, 
                          'modelName'=>"Schedule", 
                          'modalTitle'=>'Delete', 
                          'modalMessage'=>'Are you sure you want to delete this schedule?', 
                          'formID'=>'deleteSched',
                          'icon'=>'glyphicon-trash' ])

      @endforeach

     

    </section>

@stop

@section('footer-scripts')
<script>


  
  $(function () {
   'use strict';

   // -------- reset all default options, remove checked RDs
   var restdays = $('input[name="restdays[]"]:checkbox:checked').map(function() {
              return this.value;
          }).get();

        $.each( restdays, function( key, value ) {
          $('select[name="workday[]"] option:contains("'+value+'")').remove();

        });

  //---------------------------------------------------------

    var htmlcode ='<div class="row">';
        htmlcode += '<div class="col-lg-4">';
       
        htmlcode += '                                                     <select name="addDay[]" class="form-control" style="margin-bottom:5px">';
        htmlcode += '                                                       <option value="0" selected="selected"> - Select a day - </option>';

        @foreach ($days as $d)
        htmlcode += '                                                       <option value="{{$d}}"> {{$d}} </option>';

        @endforeach
        htmlcode += '                                      </select></div><div class="col-lg-3">';
        htmlcode += '                                       <select name="addStart[]" class="form-control" style="margin-bottom:5px"><option value="0">-select time-</option>';
                                                            <?php  for( $i = 1; $i <= 24; $i++){ $min=0; $time1 = date('h:i A', strtotime('1999-01-01'.$i.":".$min.":00")); $min+=30; $time2 = date('h:i A', strtotime('1999-01-01'.$i.":".$min.":00")); ?>
        htmlcode +='                                                       <option value="{{$time1}}">{{$time1}} </option>';
        htmlcode +='                                                       <option value="{{$time2}}">{{$time2}} </option>';
                                                            <?php } ?>
        htmlcode +='                                                  </select></div><div class="col-lg-3">';
                                                          
        htmlcode +='                                                   <select name="addEnd[]" class="form-control" style="margin-bottom:5px"><option value="0">-select time-</option>';
                                                            <?php  for( $i = 1; $i <= 24; $i++){ $min=0; $time1 = date('h:i A', strtotime('1999-01-01'.$i.":".$min.":00")); $min+=30; $time2 = date('h:i A', strtotime('1999-01-01'.$i.":".$min.":00")); ?>
        htmlcode +='                                                       <option value="{{$time1}}">{{$time1}} </option>';
        htmlcode +='                                                       <option value="{{$time2}}">{{$time2}} </option>';
                                                            <?php } ?>
        htmlcode +='                                                   </select></div><div class="col-lg-2">';


//<a id="saveShift" class="btn btn-sm btn-success"><i class="fa fa-save"></i> Save Shift</a></div></div>
   

   $('#addShift').on('click', function(e) {
    e.preventDefault();

       
        $('#addShifts').fadeIn();
        $('#addShifts').append(htmlcode);
        var restdays = $('input:checkbox:checked').map(function() {
              return this.value;
          }).get();

        $.each( restdays, function( key, value ) {
          $('select[name="addDay[]"] option:contains("'+value+'")').remove();

        });



   });

   

   //update day options pag nag set ng RD

   $('input[name="restdays[]"]').on('click',function(){

        var dayChecked = $(this).attr("value");
        var idChecked = $(this).attr("data-id");
        
        if ($(this).is(":checked")) {
          $('select[name="workday[]"] option:contains("'+dayChecked+'")').remove();
           $('select[name="addDay[]"] option:contains("'+dayChecked+'")').remove();
        }
        else {
          //$('select[name="workday[]"] option:contains("'+dayChecked+'")').remove();
          var addcode = "";

          @foreach ($days as $d)
            addcode += '<option value="{{$d}}"> {{$d}} </option>';
          @endforeach
          
          $('select[name="workday[]"]')
            .find('option')
            .remove()
            .end()
            .append(addcode);
          $('select[name="addDay[]"]')
            .find('option')
            .remove()
            .end()
            .append(addcode);

            var restdays = $('input[name="restdays[]"]:checkbox:checked').map(function() {
                  return this.value;
              }).get();

            $.each( restdays, function( key, value ) {
              $('select[name="workday[]"] option:contains("'+value+'")').remove();
              $('select[name="addDay[]"] option:contains("'+value+'")').remove();

            });

          

        } 

   });

  

   $('#updateForm').on('submit', function(e) {

        e.preventDefault(); e.stopPropagation();
        console.log('Enter submit');
          var _token = "{{ csrf_token() }}";
          
          var workday = $('select[name="workday[]"]').map(function(){
                  return this.value
              }).get();
          
          var schedIDs = $("select[name='workday[]']").map( function () {
              return $(this).attr("data-id");
          }).get();
          
          var timeStart = $('select[name="timeStart[]"]').map(function(){
                  return this.value
              }).get();

          var timeEnd = $('select[name="timeEnd[]"]').map(function(){
                  return this.value
              }).get();

          
          //var restdays = $('input[name="restdays[]" ]:checked').val();
          var restdays = $('input[name="restdays[]"]:checkbox:checked').map(function() {
              return this.value;
          }).get();

          var isFlexi = $('input[name="isFlexi"]:checked').val(); //$('input[name="isFlexi"]:checkbox:checked').val(); //.map(function() {
          //     return this.value;
          // }).get();

          var addDay = $('select[name="addDay[]"]').map(function(){
                  return this.value
              }).get();

          var addStart = $('select[name="addStart[]"]').map(function(){
                  return this.value
              }).get();

          var addEnd = $('select[name="addEnd[]"]').map(function(){
                  return this.value
              }).get();

          var user_id = $('input[name="user_id').val();
         
         console.log("flexi: " + isFlexi);
          

           $.ajax({
                url:"{{action('UserController@updateSchedule', $user->id)}}",
                type:'POST',
                data:{

                  'schedIDs': schedIDs,
                  'workday': workday,
                  'timeStart': timeStart,
                  'timeEnd': timeEnd,
                  'restdays': restdays,
                  
                  '_token':_token
                },

               
                success: function(response2)
                {
                  console.log(response2);

                  $.ajax({
                      url:"{{action('ScheduleController@store')}}",
                      type:'POST',
                      data:{
                        'user_id': user_id,
                        'addDay': addDay,
                        'addStart': addStart,
                        'addEnd': addEnd,
                        'isFlexi': isFlexi,// $('input[name="isFlexi"]:checkbox:checked').val(),
                        
                        '_token':_token
                      },

                     
                      success: function(response)
                      {
                        console.log(response);
                        console.log("flexi2: " + isFlexi);
                        $('#submit').fadeOut();
                      var htmcode = "<span class=\"success\"> <i class=\"fa fa-save\"></i> Employee Schedule updated. <br /><br/>";
                     
                      
                      $('#alert-submit').addClass('alert alert-success').fadeIn().html(htmcode).delay(2000).fadeOut(function()
                        { 
                          window.location = "{{action('UserController@show', $user->id)}}";
                        }); 

                      }
                    });

                  return false;


                   
                   

                }
              });

         

          
         
          return false;
        }); //end updateForm

       

});
   


</script>
@stop