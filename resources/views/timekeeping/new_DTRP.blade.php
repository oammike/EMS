@extends('layouts.main')

@section('metatags')
<title>New DTRP | Employee Management System</title>

@endsection

@section('content')


  <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
       <i class="fa fa-file-text-o"></i> File DTRP <small>
        : {{$user->firstname}} {{$user->lastname}} </small>
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
       
      </div>

      <!-- ******** THE PANE ********** -->
          <div class="row">
             <!-- <div class="col-lg-1 col-sm-4  col-xs-9"></div> -->
             <div class="col-lg-12 col-sm-4 col-xs-12" ><!--style="background-color:#fff"--><br/><br/>
                <form class="col-lg-12" id="plotSched" name="plotSched" accept-charset="UTF-8" enctype="multipart/form-data">
                  <br/><br/>
                      <div class="box-info" style="background: rgba(256, 256, 256, 0.5)">
                     

                        <div id="pane_VL" class="modal-body-upload" style="padding:20px;">
                            <div class="options_vl">
                              <div style="width:100%; " class="pull-left">

                               
                                <div class="info-box bg-blue">

                               

                                  <span class="info-box-icon"><i class="fa fa-file-text-o"></i></span>
                                  <div class="info-box-content">
                                    <span class="info-box-text">New <strong>DTRP</strong> for :</span>
                                   

                                    
                                    <div class="progress"><div id="percentage" class="progress-bar" style="width:100%"></div>
                                    </div>
                                    <span class="progress-description">
                                      <h4 class="pull-right" > {{$user->firstname}} {{$user->lastname}} </span>
                                    </span>
                                  </div>
                                  <!-- /.info-box-content -->
                                </div>
                                <!-- /.info-box -->

                              </div><br/>

                              <div class="row">
                                  <div class="col-lg-4">
                                    <h4>Type of DTR Problem:</h4>

                                    <label style="font-size: x-large;padding-top: 25px" class="text-success"><input type="radio" name="logType" value="1" required="required" /> &nbsp; &nbsp;<i class="fa fa-sign-in"></i> DTRP IN</label>
                                     &nbsp; &nbsp; &nbsp; &nbsp;

                                    <label  style="font-size: x-large;padding-top: 25px" class="text-danger"><input type="radio" name="logType" value="2" required="required" /> &nbsp; &nbsp;<i class="fa fa-sign-out"></i> DTRP OUT</label>
                                   
                                   <h4><br/>Specify Log Details:</h4>
                                   <label for="productionDate">Production Date: <input required type="text" class="dates form-control datepicker" name="productionDate" id="productionDate" value="{{ date('m/d/Y', strtotime($productionDate)) }}" /></label>
                                   <br/><br/>
                                   <label for="actualDate">Actual Date of Log: <br/>
                                    <small style="font-weight: normal;">* Indicate the actual date of your log for the indicated production date </small>
                                    <input required type="text" class="dates form-control datepicker" name="actualDate" id="actualDate" value="{{ date('m/d/Y', strtotime($actualDate)) }}" /></label><br/><br/>

                                    <label>Log Time</label>
                                    <div class="row">
                                        <div class="col-sm-4">
                                          <select id="hour" name="hour" class="form-control">
                                            <option value="0">hour</option>
                                             @for($h=1; $h<=12; $h++)
                                             <option value="{{$h}}">{{$h}}</option>
                                             @endfor

                                           </select>
                                          
                                        </div>

                                        <div class="col-sm-4">
                                          <select id="minute" name="minute" class="form-control">
                                            <option value="00">00</option>
                                             @for($h=1; $h<60; $h++)
                                             <?php $number = $h; $length = 2; $string = substr(str_repeat(0, $length).$number, - $length); ?>
                                             <option value="{{$string}}">{{$string}}</option>
                                             @endfor

                                           </select>
                                          
                                        </div>
                                        <div class="col-sm-4">
                                          <label><input type="radio" name="ampm" value="AM" checked="checked" /> AM</label>
                                          <label><input type="radio" name="ampm" value="PM" /> PM</label>
                                        </div>
                                    </div> 
                                  </div>

                               

                                  <div class="col-lg-8">
                                    <div id="vl_more">
                                      <h4>Reason:</h4>
                                     
                                      <p style="font-size: smaller">Select reason why you're filing a DTRP:</p>

                                      <!-- Custom Tabs -->
                                      <div class="nav-tabs-custom">
                                       
                                        <ul class="nav nav-tabs">
                                          <?php $active=0; ?>
                                          @foreach($allCat as $a) 
                                            @if($active)
                                            <li>
                                            @else
                                            <li class="active">
                                            <?php $active=1;?>
                                            @endif
                                              <a href="#tab_{{$a[0]->catID}}" data-toggle="tab"><strong class="text-primary "> {{$a[0]->category}} </strong></a>
                                            </li>
                                          @endforeach

                                        </ul>
                                        
                                        <div class="tab-content">
                                          <?php $active=0; ?>
                                          @foreach($allCat as $a) 
                                            @if($active)
                                              <div class="tab-pane" id="tab_{{$a[0]->catID}}"> 
                                            @else
                                              <div class="tab-pane active" id="tab_{{$a[0]->catID}}"> 
                                              <?php $active=1;?>
                                            @endif
                                                <h1 class="pull-right" style="color:#dedede"> {{$a[0]->category}}</h1>
                                                <div class="row" style="margin-top:50px">
                                                  <div class="col-lg-12">

                                                    <?php $subs = collect($dtrpCategories)->where('catID',$a[0]->catID)->groupBy('subcatID'); ?>
                                                    
                                                    @foreach($subs as $s)
                                                      <div class="col-lg-6">
                                                        <h5 class="text-danger"> {{$s[0]->subCat}} </h5>

                                                        <?php $subcats = collect($dtrpCategories)->where('catID',$a[0]->catID)->where('subcatID',$s[0]->subcatID); ?>
                                                        @foreach($subcats as $r)

                                                            <label style="font-size: smaller;"><input type="radio" class="reasons" name="reason" data-warning="{{$r->warning}}" value="{{$r->id}}" /> {{$r->reason}}</label><div class="clearfix"></div>
                                                        @endforeach
                                                      </div>
                                                      
                                                      
                                                    @endforeach
                                                  </div>

                                                  
                                                </div>
                                                  <!-- /.row -->
                                                

                                              </div><!--end pane1 -->
                                              <!-- /.tab-pane -->
                                          @endforeach


                                          <div class="row">
                                            <div class="col-lg-12 text-success" id="warnings" style="font-style: italic;padding:20px; ">
                                              
                                            </div>
                                          </div>


                                          <input style="margin-top: 20px" type="file" name="attachments" id="attachments" />
                                          <label style="margin-top: 20px">Notes:</label><br/>
                                          <textarea name="notes" id="notes" class="form-data" cols="70" rows="4"></textarea>

                                        </div>
                                        <!-- /.tab-content -->
                                      </div>
                                      <!-- nav-tabs-custom -->


                                      <div class="clearfix"></div>

                                      <a href="{{ action('DTRController@show',$user->id) }}" class="back btn btn-default btn-lg pull-right" style="font-weight:bold;margin-top:0px; z-index:999"><i class="fa fa-calendar"></i> &nbsp;DTR page</a>
                                      <a id="save" class="btn btn-danger btn-lg pull-right" style="margin-right:25px" > <i class="fa fa-upload" ></i> Submit DTRP </a>
                                      <a id="newone" href="{{action('UserDTRPController@newDTRP')}}" class="btn btn-success btn-lg pull-right" style="margin-right:25px" > <i class="fa fa-upload" ></i> File New DTRP </a>




                                    </div>
                                        
                                     
                                     
                                      
                                    </div>
                                  </div>

                              </div>

                            </div><!--end options_vl-->



                          
                       
                          
                        </div><!--end pane vl-->

                        
                      
                       <div class="clearfix"></div><br/><br/>

                      </div><!--end box-info -->

                      <h2> <br/><br/></h2>
                      <input type="hidden" name="userid" id="userid" value="{{$user->id}}" />
                      <input type="hidden" name="isSigned" id="isSigned" value="{{$isSigned}}" />
                      

                </form>
              </div> <!-- end col-lg-10 -->

          

                        
                            
             
              <!-- <div class="col-lg-1 col-sm-4  col-xs-9"></div> -->
              <div class="holder"></div>


              <div class="clearfix"></div>
              <p><small><?php $n = \Carbon\Carbon::now('GMT+8'); ?>{{$n}} </small></p>
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
   $( ".datepicker" ).datepicker({dateFormat:"YYYY-mm-dd"});

  $(function () {
     'use strict';
     var _token = "{{ csrf_token() }}";
     $('#newone').fadeOut();
     $('.reasons').on('click',function(e){

      var warning = $(this).attr('data-warning');
      $('#warnings').html(warning);

      if(!warning)
        $('#attachments').fadeOut();
      else{
        $('#attachments').fadeIn();
      }

     });

     $('#save').on('click',function(e)
     {
          e.preventDefault(); e.stopPropagation();
          var _token = "{{ csrf_token() }}";
          var user_id = $('#userid').val(); //$(this).attr('data-userid');
          var productionDate = $('#productionDate').val();
          var actualDate = $('#actualDate').val();
          var logType = $('input[name="logType"]:checked').val();
          var hour = $('#hour').val();
          var minute = $('#minute').val();
          var ampm = $('input[name="ampm"]:checked').val();
          var notes = $('#notes').val();

          
          var reason = $('input[name="reason"]:checked').val();
          var requirements = $('input[name="reason"]:checked').attr('data-warning');
          
          if(!requirements)
            var attachments = null;
          else
            var attachments = $('#attachments')[0].files[0];

          var data = new FormData();

          data.append('attachments',attachments);
          data.append('userid',user_id);
          data.append('productionDate',productionDate);
          data.append('actualDate',actualDate);
          data.append('logType',logType);
          data.append('hour',hour);
          data.append('minute',minute);
          data.append('ampm',ampm);
          data.append('notes',notes);
          data.append('reason',reason);
          
          data.append('_token',_token);
          console.log(data);

          if(!logType){
              $.notify("Please indicate if this is a DTRP IN or DTRP OUT request.",{className:"error",globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );
          }

          else if(hour == '0')
          {
            $.notify("Please indicate log time of your DTRP request.",{className:"error",globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );
          }
          else
          {
              if(!reason){
                $.notify("Please select a reason for your DTRP request.",{className:"error",globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );
                return false;
          
              }
              if (attachments == null && requirements) {
                $.notify("Please select required attachment for DTRP filing.",{className:"error",globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );

             }else{

                $('a#save').fadeOut();
                $.ajax({
                    url: "{{action('UserDTRPController@newDTRP_process')}}",
                    type:'POST',
                    //contentType: 'multipart/form-data', 
                    contentType: false,       // The content type used when sending data to the server.
                    cache: false,             // To unable request pages to be cached
                    processData:false,
                    data:data,
                    dataType: 'json',
                    
                    success: function(response){
                      $('#save').fadeOut();
                      if (response.success == '1'){
                        $.notify("DTRP sent for review and approval.",{className:"success",globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );
                        $('#newone').fadeIn();
                      }else {
                          $.notify("Something went wrong. Please try again later.",{className:"success", globalPosition:'right middle',autoHideDelay:3000, clickToHide:true} );
                           $('a#save').fadeIn();
                          
                        }
                      
                      console.log(response);
                      
                      // window.setTimeout(function(){
                      //   window.location.href = "{{action('UserController@userRequests',$user->id)}}";
                      // }, 2000);
                    },
                    error: function(response){
                      console.log(response);
                      $.notify("Sorry, cannot process your request right now due to: \n(Error "+response.status+") "+ response.statusText+"\n\nContact our IT team to ensure you are accessing EMS via secure link using FortiClient VPN instead of web portal.\n\nSend an email to: \nitgroup@openaccessbpo.com [for Makati employees]\n itdavao@openaccessbpo.com [for Davao employees]",{className:"error", globalPosition:'right middle',autoHideDelay:15000, clickToHide:true} );
                      $('a#save').fadeIn();

                    }
                  });

             }

          }

           
     });
  });

  

  
</script>
<!-- end Page script -->


@stop