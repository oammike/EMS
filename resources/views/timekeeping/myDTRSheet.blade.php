@extends('layouts.main')


@section('metatags')
  <title>DTR Sheet | {{$user->firstname}}</title>
    <meta name="description" content="profile page">
 <link href="{{URL::asset('storage/resources/js/bootstrap-material-datetimepicker.css')}}" rel="stylesheet" /> 
<!--  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" /> -->
 
@stop


@section('content')




<section class="content-header">

      <h1>
      <i class="fa fa-calendar"></i> My <strong class="text-primary">D</strong>aily <strong class="text-primary">T</strong>ime <strong class="text-primary">R</strong>ecord
        <small></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        
        <li class="active">My DTR</li>
      </ol>
    </section>

    <!-- Main content -->
   
    <section class="content">
      <div class="row">
        
        <div class="col-xs-12">
          


          <!-- Profile Image -->
                          <div class="box box-primary"   style="background: rgba(256, 256, 256, 0.4)">

                            <div class="box-body box-profile"   style="background: rgba(256, 256, 256, 0.4)">
                               <!-- Widget: user widget style 1 -->
                                    <div class="box box-widget widget-user"   style="background: rgba(256, 256, 256, 0.4)">
                                      <!-- Add the bg color to the header using any of the bg-* classes -->
                                      <div class="widget-user-header bg-black" style="background: url('{{URL:: asset("public/img/bg.jpg")}}') top left;">
                                        
                                        <h3 style="text-shadow: 1px 2px #000000; text-transform:uppercase" class="widget-user-username">{{$user->lastname}}, {{$user->firstname}} {{$user->middlename}}</h3>
                                        <h5 style="text-shadow: 1px 2px #000000;"  class="widget-user-desc">{{$user->position->name}} </h5>
                                      </div>
                                      <div class="widget-user-image">
                                        
                                         @if ( file_exists('public/img/employees/'.$user->id.'.jpg') )
                                          <img src="{{asset('public/img/employees/'.$user->id.'.jpg')}}" class="user-image" alt="User Image">
                                          @else
                                          <img src="{{asset('public/img/useravatar.png')}}" class="user-image" alt="User Image">

                                            @endif

                                          
                                        <br/>
                                      </div>
                                      <div class="box-footer"  style="background: rgba(256, 256, 256, 0.4)">
                                        <div class="row">
                                          <div class="col-sm-3">
                                            <div class="description-block">
                                              <p class="description-header"><i class="fa fa-users margin-r-5"></i> Department / Program : </p>
                                              <span class="description-text text-primary">
                                              
                                              {!! $camps !!}

                                              
                                              </span>

                                              
                                            </div>
                                            <!-- /.description-block -->
                                          </div>
                                          <!-- /.col -->
                                          <div class="col-sm-3">
                                            <div class="description-block">
                                              <p class="description-header"><i class="fa fa-street-view margin-r-5"></i> Immediate Head: </p>
                                              <span class="description-text text-primary">{{$immediateHead->firstname}} {{$immediateHead->lastname}}</span>
                                            </div>
                                            <!-- /.description-block -->
                                          </div>
                                          <!-- /.col -->
                                          <div class="col-sm-3">
                                            <div class="description-block">
                                              <p class="description-header"><i class="fa fa-address-card-o margin-r-5"></i> Bio AccessCode : </p>
                                              <span class="description-text text-primary">{{$user->accesscode}} </span>
                                            </div>
                                            <!-- /.description-block -->
                                          </div>
                                          <!-- /.col -->
                                          <div class="col-sm-3 ">
                                            <div class="description-block">
                                              <p class="description-header"><i class="fa fa-envelope-o margin-r-5"></i> E-mail:</p>
                                              <span><a href="mailto:{{$user->email}}"> {{$user->email}}</a></span>
                                            </div>
                                            <!-- /.description-block -->
                                          </div>
                                          <!-- /.col -->

                                          <!-- START CUSTOM TABS -->
     

                                         
                                          
                                          <!-- /.row -->
                                          <!-- END CUSTOM TABS -->
                                          
                                          <!-- /.col -->
                                          

                                      </div>
                                    </div>
                                    <!-- /.widget-user -->

                                    <div class="row">
                                          <!--   <div class="col-lg-1 col-sm-12"></div> -->
                                            <div class="col-lg-12 col-sm-12"><br/><br/>
                                              
                                              
                                                <div class="row">
                                                  <div class="col-lg-5 text-right"><a class="btn btn-default btn-sm" href="{{action('DTRController@show',['id'=>$user->id,'from'=>$prevFrom, 'to'=>$prevTo])}}">
                                                  <i class="fa fa-arrow-left"></i> </a></div>
                                                  <div class="col-lg-2">
                                                    <select class="form-control" name="payPeriod" id="payPeriod">
                                                      @if($cutoffID == 0)
                                                      <option value='0'>* Select cutoff period *</option>
                                                      @endif
                                                      @foreach($paycutoffs as $cutoffs)
                                                      <option value="{{$cutoffs->id}}" @if($cutoffID == $cutoffs->id) selected="selected" @endif data-fromDate="{{$cutoffs->fromDate}}" data-toDate="{{$cutoffs->toDate}}">{{ date('M d, Y', strtotime($cutoffs->fromDate)) }} - {{date('M d, Y', strtotime($cutoffs->toDate))}} </option>
                                                      @endforeach
                                                    </select>
                                                   

                                                  </div>
                                                  <div class="col-lg-5 text-left"><a class="btn btn-default btn-sm" href="{{action('DTRController@show',['id'=>$user->id,'from'=>$nextFrom, 'to'=>$nextTo])}}">
                                                  <i class="fa fa-arrow-right"></i></a>

                                                  <table class="table table-bordered pull-right text-center" style="width: 60%;margin-right: 30px">
                                                    <tr>
                                                      <th class="text-center" style="background: rgba(72, 178, 219, 0.4);font-size: smaller"><i class="fa fa-plane"></i> VL Credits</th>
                                                      <th class="text-center"style="background: rgba(234, 0, 0, 0.4);font-size: smaller;"><i class="fa fa-stethoscope"></i> SL Credits</th>
                                                    </tr>
                                                    <tr>
                                                      <td style="background: rgba(72, 178, 219, 0.4); font-size: smaller;color:#000; font-weight: bolder;" >{{$currentVLbalance}} </td>
                                                      <td style="background: rgba(234, 0, 0, 0.4); font-size: smaller;color:#000;font-weight: bolder;" >{{$currentSLbalance}} </td>
                                                    </tr>
                                                    
                                                  </table>


                                                </div>

                                                </div>

                                                <h4 class="text-center"><br/><br/>
                                                <small>Cutoff Period: </small><br/>
                                                <span class="text-success">&nbsp;&nbsp; {{$cutoff}} &nbsp;&nbsp; </span>

                                                  
                                              </h4>

                                              <!-- ********** DTR BUTTONS ************** -->

                                              @if ($anApprover || (!$employeeisBackoffice && $isWorkforce) )

                                              <h5 class="pull-left text-danger">&nbsp;&nbsp;&nbsp;<i class="fa fa-lock"></i> 

                                                @if (count($payrollPeriod) == 1)

                                                DTR for this production date is locked.

                                                @else

                                                DTR Sheet is Locked 

                                                @endif
                                                <a id="unlockByTL" class="btn btn-md btn-primary pull-left" style="margin-left: 5px;"><i class="fa fa-unlock"></i> Unlock DTR Now </a></h5> 

                                              @else

                                              <h5 class="pull-left text-danger">&nbsp;&nbsp;&nbsp;<i class="fa fa-lock"></i> DTR Sheet is Locked 
                                                <a id="unlock" class="btn btn-xs btn-default pull-left" style="margin-left: 5px;"><i class="fa fa-unlock"></i> Request Unlock </a></h5> 


                                              @endif
                                              
                                              


                                             <a target="_blank" href="{{action('LogsController@viewRawBiometricsData', $user->id)}}" class="btn btn-xs btn-default pull-right"><i class="fa fa-search"></i> View Uploaded Biometrics</a>
                                             
                                               <a href="{{action('UserController@createSchedule', $user->id)}}" style="margin-right: 5px" class="btn btn-xs btn-default pull-right"><i class="fa fa-calendar-plus-o"></i>  Plot Work Sched</a>
                                               
                                               <a href="{{action('UserController@userRequests', $user->id)}}" style="margin-right: 5px" class="btn btn-xs btn-default pull-right"><i class="fa fa-file-o"></i>  DTR Requests</a>

                                                <a href="{{action('UserController@show', $user->id)}}" style="margin-right: 5px" class="btn btn-xs btn-default pull-right"><i class="fa fa-address-card-o"></i>  My Profile</a>
                                              <br/><br/>
                                              <!-- ********** DTR BUTTONS ************** -->
                                              
                                              <table id="biometrics" class="table table-bordered table-striped">
                                                  <thead>
                                                  <tr class="text-success">
                                                    
                                                    <th class="text-center" style="width:15%">Production Date</th>
                                                    <td class="text-center"></td>
                                                    <th class="text-center" style="width:15%">Work Shift</th>
                                                    <th class="text-center" style="width:12%">IN</th>
                                                    <th class="text-center" style="width:12%">OUT</th>
                                                    
                                                    <th class="text-center">Hrs. Worked</th>
                                                    
                                                    <th class="text-center"  style="width:10%">OT<br/>(Billable hrs.)</th>
                                                    <th class="text-center"  style="width:10%">OT<br/>(Approved hrs.)</th>
                                                    <th  class="text-center">UT<br/>(hours)</th>
                                                   

                                                     
                                                  </tr>
                                                  </thead>
                                                  <tbody style="font-family: 'Source Sans Pro','Helvetica Neue',Helvetica,Arial,sans-serif; font-size:0.9em">

                                                    

                                                    @if (count($myDTRSheet) == 0)
                                                    <tr>
                                                      <td colspan='10' class="text-center"><h2 class="text-center text-default"><br/><br/><i class="fa fa-clock-o"></i>&nbsp;&nbsp; No Biometrics Data Available</h2><small>Kindly check again at the end of work day or tomorrow for the updated biometrics data.</small><br/><br/><br/></td>
                                                    </tr>

                                                    @else
                                                     

                                                     @foreach ($myDTRSheet as $data)

                                                    
                                                     <input type="hidden" name="dtr" class="biometrics" value="{{$data['biometrics_id']}}" />



                                                     <tr>
                                                        
                                                        <td class="text-center"><small class="text-success"><strong title="Verified DTR entry"><i class="fa fa-2x fa-check-square-o"></i></strong></small> &nbsp;&nbsp; {{ date('M d, Y',strtotime($data->productionDate)) }} </td>

                                                        <!-- we determin here if WFH -->
                                                        <?php $hasWFH = collect($wfhData)->where('biometrics_id',$data['biometrics_id']);
                                                              $ecqStatus = collect($ecq)->where('biometrics_id',$data['biometrics_id'])->sortByDesc('created_at') 

                                                              //1=AHW | 2=Hotel Stayer | 3=Shuttler | 4= Walkers | 5= Dwellers | 6= Carpool Driver | 7= Carpool Passenger
                                                              ?>
                                                        
                                                        @if(count($ecqStatus) > 0)

                                                          @if($ecqStatus->first()->workStatus == 1) <!-- WFH -->
                                                          <td class="text-left"><a title="Work From Home" class="btn btn-xs btn-success" href="{{action('LogsController@viewRawBiometricsData', $user->id)}}#{{$data['biometrics_id']}}" target="_blank"><i class="fa fa-home"></i>  </a> {{ date('l',strtotime($data->productionDate)) }}</td>

                                                          @elseif($ecqStatus->first()->workStatus == 2) <!-- Hotel -->
                                                          <td class="text-left"><a title="Hotel Stayer" class="btn btn-xs bg-purple" href="{{action('LogsController@viewRawBiometricsData', $user->id)}}#{{$data['biometrics_id']}}" target="_blank"><i class="fa fa-shopping-bag"></i>  </a> {{ date('l',strtotime($data->productionDate)) }} </td>

                                                          @elseif($ecqStatus->first()->workStatus == 3)
                                                          <td class="text-left"><a title="Shuttler" class="btn btn-xs btn-warning" href="{{action('LogsController@viewRawBiometricsData', $user->id)}}#{{$data['biometrics_id']}}" target="_blank"><i class="fa fa-car"></i> </a> {{ date('l',strtotime($data->productionDate)) }}  </td>

                                                          @elseif($ecqStatus->first()->workStatus == 4)
                                                          <td class="text-left"><a title="Walker" class="btn btn-xs btn-danger" href="{{action('LogsController@viewRawBiometricsData', $user->id)}}#{{$data['biometrics_id']}}" target="_blank"><i class="fa fa-blind"></i> </a> {{ date('l',strtotime($data->productionDate)) }}  </td>

                                                          @elseif($ecqStatus->first()->workStatus == 5)
                                                          <td class="text-left"><a title="Dweller" class="btn btn-xs bg-aqua" href="{{action('LogsController@viewRawBiometricsData', $user->id)}}#{{$data['biometrics_id']}}" target="_blank"><i class="fa fa-bath"></i> </a> {{ date('l',strtotime($data->productionDate)) }}  </td>

                                                          @elseif($ecqStatus->first()->workStatus == 6)
                                                          <td class="text-left"><a title="Carpool Driver" class="btn btn-xs" style="background-color: #da12f3;color:#fff" href="{{action('LogsController@viewRawBiometricsData', $user->id)}}#{{$data['biometrics_id']}}" target="_blank"><i class="fa fa-tachometer"></i> </a> {{ date('l',strtotime($data->productionDate)) }}  </td>

                                                          @elseif($ecqStatus->first()->workStatus == 7)
                                                          <td class="text-left"><a title="Carpool Passenger" class="btn btn-xs" style="background-color: #1219f3; color:#fff" href="{{action('LogsController@viewRawBiometricsData', $user->id)}}#{{$data['biometrics_id']}}" target="_blank"><i class="fa fa-users"></i> </a> {{ date('l',strtotime($data->productionDate)) }}  </td>

                                                          @endif

                                                        @elseif(count($hasWFH) > 0 && $user->isWFH)
                                                        <td class="text-left"><a title="Work From Home" class="btn btn-xs btn-success" href="{{action('LogsController@viewRawBiometricsData', $user->id)}}#{{$data['biometrics_id']}}" target="_blank"><i class="fa fa-home"></i> </a> {{ date('l',strtotime($data->productionDate)) }} </td>
                                                        @elseif($user->isWFH && $data->workshift == '* RD * - * RD *')
                                                        <td class="text-left"><a title="Work From Home" class="btn btn-xs btn-success" href="{{action('LogsController@viewRawBiometricsData', $user->id)}}#{{$data['biometrics_id']}}" target="_blank"><i class="fa fa-home"></i>  </a> {{ date('l',strtotime($data->productionDate)) }} </td>
                                                        @else

                                                        <td class="text-left"><a title="No ECQ Status" class="btn btn-xs btn-default" href="{{action('LogsController@viewRawBiometricsData', $user->id)}}#{{$data['biometrics_id']}}" target="_blank"> {{ date('l',strtotime($data->productionDate)) }} </a></td>
                                                        @endif



                                                        

                                                         <!---- *****  WORK SCHED --------->
                                                         <td class="text-center"> {!! $data->workshift !!} </td>
                                                        
                                                        <!---- ***** end WORK SCHED --------->


                                                        <!-- ******** LOG IN ********* -->
                                                        <td class="text-center">{!! $data->timeIN !!}</td>
                                                        <td class="text-center">{!! $data->timeOUT !!} </td>
                                                        <td class="text-center">{!! $data->hoursWorked !!}</td>

                                                        @if($data->OT_billable == '0.00')
                                                        <td class="text-center">{{$data->OT_billable}}  </td>

                                                        @else <td class="text-center"><strong>{{$data->OT_billable}}  </strong></td>@endif
                                                        
                                                        <!-- **************** APPROVED OT **************** -->
                                                        @if($data->OT_approved == '0.00')
                                                        <td class="text-center">{{$data->OT_approved}} </td>

                                                        @else <td class="text-center"><strong>{{$data->OT_approved}} </strong></td>@endif

                                                        <!-- **************** UDERTIME **************** -->
                                                        @if ($data->UT == '0.00')
                                                        <td class="text-center">{{$data->UT}}</td>

                                                        @else <td class="text-center"><strong>{{$data->UT}}</strong></td> @endif
                                                      </tr>
                                                     
                                                        




                                                        
                                                        


                                                        
                                                       
                                              
                                                      @endforeach
                                                  @endif

                                                  
                                                  </tbody>
                                                  
                                              </table>

                                              
                                            </div>
                                            <!-- /.col -->
                                           <!--  <div class="col-lg-1 col-sm-12"></div> -->

                                           
                                    </div>

                            </div>
                            

                            <!-- /.box-body -->
                          </div>

        </div>
        

      </div>

         

     

    </section>

@stop

@section('footer-scripts')
<script src="{{URL::asset('storage/resources/js/bootstrap-material-datetimepicker.js')}}"></script>
<script src="{{URL::asset('storage/resources/js/moment.min.js')}}"></script>





<script type="text/javascript">
$(function () 
{

  $( ".datepicker" ).on('click', function()
    { 
      $(this).datepicker().datepicker("show"); 
      $(this).datepicker({dateFormat:"YYYY-mm-dd"}); console.log("datepicker");
    })

  $('.container').fadeOut();
  $('.container#workshiftOptions').fadeOut();
  $('.container#login').fadeOut();
  $('.container#logout').fadeOut();
  $('.container#leave').fadeOut(); 
  
  //complicated clock
  $('.timepick').bootstrapMaterialDatePicker({format: 'hh:mm a',monthPicker: false, date: false, year:false, shortTime:true}); //wickedpicker(options);


  $('a.reportDTRP').on('click',function()
    { $('input[type="checkbox"]').removeAttr('checked');
      $('.container').hide(); $('.detail').hide(); 
  });


 

  //$('.moredays').fadeOut();
  //****** main checkboxes : CWS | DTRP IN | DTRP OUT | leaves ************
  $('input[type=checkbox]').change(function () {

    $('#leaveDetails').hide();
    
    var choice = $(this).val();
    
    if( $(this).is(':checked') )
    {
      switch(choice){
        case '1': { $('.container#workshiftOptions').fadeIn(); 
                    $('input[id="leave"]').prop('checked',false);
                    $('.container#leave').fadeOut();
                     $('#leaveDetails').fadeIn(); $('.addDays').fadeIn();$('button#upload').fadeIn();
                  }break;
        case '2': { $('.container#login').fadeIn(); $('input[id="leave"]').attr('checked',false);$('.container#leave').fadeOut();$('button#upload').fadeIn();}break;
        case '3': { $('.container#logout').fadeIn(); $('input[id="leave"]').attr('checked',false);$('.container#leave').fadeOut();$('button#upload').fadeIn();}break;
        case '4': { 
                    $('.container#leave').fadeIn(); 
                    $('input[id="login"]').attr('checked',false);
                    $('input[id="logout"]').attr('checked',false);
                    $('input[id="workshift"]').attr('checked',false);
                    $('.container#workshiftOptions').fadeOut();
                    $('.container#login').fadeOut(); 
                    $('.container#logout').fadeOut();
                    $('button#upload').fadeOut();

                  }break;
        
      }


      


    } else {

      switch(choice){
        case '1': { $('.container#workshiftOptions').fadeOut(); }break;
        case '2': { $('.container#login').fadeOut(); }break;
        case '3': { $('.container#logout').fadeOut(); }break;
        case '4': { $('.container#leave').fadeOut(); }break;
      }

    }
    
       

  }); //end main checkboxes


  //$(document).on('change','select.othrs.form-control',function(){

  /* ----- OT -------- */



  $('select.othrs.form-control').on('change',function(){

     var timeStart = $(this).find('option:selected').attr('data-timestart');
     var timeEnd = $(this).find('option:selected').attr('data-timeend');
     var fh = $(this).find(':selected').val();

     //console.log('start: ' + timeStart);
     //console.log('end: ' + timeEnd);

     $('input[name="OTstart"]').val(timeStart);
     $('input[name="OTend"]').val(timeEnd);

     if (fh !== 0) $('#uploadOT').fadeIn();
    else $('#uploadOT').fadeOut();

    console.log('selected:');
    console.log(fh);


  }); //end timeEnd check if on change



  $('a, .tooltip').tooltip().css({"cursor":"pointer"});


  $('#payPeriod').on('change',function()
  {
    var fromDate = $(this).find('option:selected').attr('data-fromDate'); //$(this).find('option:selected').val();
    var toDate = $(this).find('option:selected').attr('data-toDate');

    window.location.href= "{{url('/')}}/user_dtr/{{$user->id}}?from="+fromDate+"&to="+toDate;
  }); //end payPeriod



  $('.widget-user-image img').on('mouseover', function(e){
    $(this).css({"cursor":"pointer"});
  }).on('click', function(){
    window.open("{{action('UserController@show',$user->id)}} ","_blank"); //location.href = "{{action('UserController@show',$user->id)}} ";
  });


  /* ---- for stylish notification ------ */
  $('#upload').on('click', function(){

      if ($('input#workshift').is(':checked'))
              if ($('select[name="timeEnd"] :selected').val() !== "0" )
                $.notify("CWS saved for approval.",{className:"success", globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );


      if ($('input#login').is(':checked'))
              if ($('input[name="login"]').val() !== "" )
                $.notify("DTRP - IN saved for approval.",{className:"success", globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );


      if ($('input#logout').is(':checked'))
              if ($('input[name="logout"]').val() !== "" )
                $.notify("DTRP - OUT saved for approval.",{className:"success", globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );


    });
  /* ---- for stylish notification ------ */





 /* ---- DTR LOCKING ------ */
 // $('#unlock').fadeOut();
  $('#lockDTR').on('click', function(){

    var reply = confirm("This will mark your DTR as verified and will be submitted to Finance for payroll processing. Any form of dispute will be proccessed in the next payroll cutoff.\n\n Clicking 'OK' means you agree that all entries in this DTR are correct.");

    if (reply == true){
      
     var dtrshit = $('input[name="dtr"].biometrics');
     var dtrsheet = [];

     for (var c = 0; c < dtrshit.length; c++)
     {
      var valu = dtrshit[c].value;
      productionDate = $('input[name="productionDate_'+valu+'"]').val();
      ws = $('input[name="workshift_'+valu+'"]').val();
      timeIN = $('input[name="logIN_'+valu+'"]').val();
      timeOUT = $('input[name="logOUT_'+valu+'"]').val();
      hoursWorked = $('input[name="workedHours_'+valu+'"]').val();
      OT_billable = $('input[name="OT_billable_'+valu+'"]').val();
      OT_approved = $('input[name="OT_approved_'+valu+'"]').val();
      UT = $('input[name="UT_'+valu+'"]').val();
      dtrsheet[c] = {"id":valu, "productionDate": productionDate, "workshift": ws, "timeIN":timeIN, "timeOUT":timeOUT, "hoursWorked": hoursWorked,"OT_billable":OT_billable, "OT_approved": OT_approved, "UT":UT};
      //console.log(dtrsheet[c].value);
     }

     var _token = "{{ csrf_token() }}";
     $.ajax({
                  url: "{{action('DTRController@processSheet', $user->id)}}",
                  type:'POST',
                  data:{ 

                    'dtrsheet': dtrsheet,'_token':_token
                  },

                 
                  success: function(res)
                  {
                    console.log(res);

                    $.notify("DTR sheet marked verified for payroll processing.",{className:"success", globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );
                    $('#lockDTR').removeClass('btn btn-success btn-md').addClass("text-success").html("<i class='fa fa-check fa-2x'></i> DTR Verified and Locked ");
                    $('#unlock').fadeIn().css({"visibility":"visible"});

                   $('a[data-original-title="File this OT"], a[data-original-title="Report DTRP "]').hide();
                   $('#lockDTR').delay(1000).animate({ height: 'toggle', opacity: 'toggle' }, 'slow');
                    //location.reload(true);
                    //window.location = "{{action('HomeController@index')}}";
                     
                  }, error: function(res){
                    console.log("ERROR");
                    $.notify("Something went wrong. Please try again.",{className:"error", globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );
                  }


        });



     //console.log(dtrsheet);

    }
    else{
      $.notify("Make sure to validate your DTR sheet on or before cutoff period!",{className:"error", globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );
    }

  });

  $('#unlock').on('click',function(){

    var _token = "{{ csrf_token() }}";
    var payrollPeriod = [];

    @foreach($payrollPeriod as $p)
        var el = "{{$p}}";
        payrollPeriod.push(el);
    @endforeach
     $.ajax({
                  url: "{{action('DTRController@requestUnlock', $user->id)}}",
                  type:'POST',
                  data:{ 

                    'payrollPeriod': payrollPeriod,
                    '_token':_token

                  },

                 
                  success: function(res)
                  {
                    console.log(res);

                    $.notify(res.message,{className:"success", globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );
                    $('#unlock').fadeOut();
                    
                    //location.reload(true);
                    //window.location = "{{action('HomeController@index')}}";
                     
                  }, error: function(res){
                    console.log("ERROR");
                    console.log(res);
                    $.notify(res.message,{className:"error", globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );
                  }


        });
    
    


  });


   $('#unlockByTL').on('click',function(){

    var _token = "{{ csrf_token() }}";
    var payrollPeriod = [];

    @foreach($payrollPeriod as $p)
        var el = "{{$p}}";
        payrollPeriod.push(el);
    @endforeach
     $.ajax({
                  url: "{{action('DTRController@unlock', $user->id)}}",
                  type:'POST',
                  data:{ 

                    'payrollPeriod': payrollPeriod,
                    'unlockByTL':true,
                    '_token':_token

                  },

                 
                  success: function(res)
                  {
                    console.log(res);
                    $('#unlockByTL').delay(1000).animate({ height: 'toggle', opacity: 'toggle' }, 'slow');


                    $.notify(res.message,{className:"success", globalPosition:'top right',autoHideDelay:3000, clickToHide:true} );

                    setTimeout(function(){
                      //location.reload(true);
                      var fromDate = "{{$paystart}}"; //$(this).find('option:selected').val();
                      var toDate = "{{$payend}}";
                      window.location.href= "{{url('/')}}/user_dtr/{{$user->id}}?from="+fromDate+"&to="+toDate;
                    },3000);
                    
                    //;
                    //window.location = "{{action('HomeController@index')}}";
                     
                  }, error: function(res){
                    console.log("ERROR");
                    console.log(res);
                    $.notify(res.message,{className:"error", globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );
                  }


        });
    
    


  });

 /* ---- DTR LOCKING ------ */



  

 


    $(".incr-btn").on("click", function (e) {
            var $button = $(this);
            var oldValue = $button.parent().find('.quantity').val();
            var maxval = $('input[name="quantity"').attr('data-max'); // $button.parent().find('.quantity').attr('data-max');
            console.log(maxval);

            if (oldValue >= maxval )
              {
                $button.parent().find('.incr-btn[data-action="increase"]').addClass('inactive');
                $('#plus').fadeOut();
              } else
              {
                $button.parent().find('.incr-btn[data-action="decrease"]').removeClass('inactive');
                $button.parent().find('.incr-btn[data-action="increase"]').removeClass('inactive');
                $('#plus').fadeIn();

              }
            
            if ($button.data('action') == "increase") {
                var newVal = Math.round((parseFloat(oldValue) + 0.5),2);
                if (newVal >= maxval) $('#plus').fadeOut();
            } else {
                // Don't allow decrementing below 1
                if (oldValue > 1) {
                    var newVal = parseFloat(oldValue) - 0.5;
                } else {
                    newVal = 1;
                    $button.addClass('inactive');
                }
                $('#plus').fadeIn();
            }
            $button.parent().find('.quantity').val(newVal);
            e.preventDefault();
    }); //end incr-btn


    

}); //END function()




    





</script>
@stop