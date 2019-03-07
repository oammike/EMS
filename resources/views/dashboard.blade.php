@extends('layouts.main')

@section('metatags')
<title>Dashboard | Employee Management System</title>
<style type="text/css">
.box.box-widget.widget-user-2{min-height: 455px;}

#myCarousel p {color:#666;}
</style>

<style type="text/css">
    input[type="text"]{ background: none; border: none; border-bottom: solid 2px #666 }
    /* Change Autocomplete styles in Chrome*/
input:-webkit-autofill,
input:-webkit-autofill:hover, 
input:-webkit-autofill:focus
textarea:-webkit-autofill,
textarea:-webkit-autofill:hover
textarea:-webkit-autofill:focus,
select:-webkit-autofill,
select:-webkit-autofill:hover,
select:-webkit-autofill:focus {
  border:none;
  -webkit-text-fill-color: #333;
  -webkit-box-shadow: 0 0 0px 1000px #f2fcff inset;
  transition: background-color #f2fcff ease-in-out 0s;
}
.tab-content label {font-weight: normal}

</style>



@endsection

@section('content')


  <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Dashboard
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Dashboard</li>
      </ol>
    </section>

     <section class="content"><br/><br/><br/>

          

          <div class="row">

            <div class="col-lg-7 col-sm-6 col-xs-12">

               @if(count($groupedForm)>0 && !$reportsTeam )
                <!-- ************* POSTMATE WIDGET CHART ************ -->
                 @include('layouts.widget-Postmates')
                @endif

                @if($reportsTeam==1)
                  <!-- ************* POSTMATE WIDGET CHART ************ -->
                 @include('layouts.widget-Reports')

                @endif
               

              <!-- For approvals -->
                  <div class="box box-info"style="background: rgba(256, 256, 256, 0.5)">
                    <div class="box-header with-border">
                      <h3 class="box-title pull-left">For Approvals <small>(<span class="text-danger" id="approvalcount"></span>)</small></h3>

                      <div class="box-tools pull-right">
                        <button type="button" id="refresh" title="Refresh Approvals" class="btn btn-box-tool"><i class="fa fa-refresh"></i>
                        </button>
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                        <!-- <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button> -->
                      </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                      <div class="table-responsive">
                        <table id="requests" class="table no-margin" style="background: rgba(256, 256, 256, 0.4)" >
                          <!-- <thead>
                          <tr>
                            <th>Request &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                            <th>Production Date</th>
                            <th>Actions &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                          </tr>
                          </thead>
                          <tbody id="notifdata">
                            
                          
                          
                          </tbody> -->
                        </table>


                        @foreach($forApprovals as $notifs)


                         

                                @include('layouts.modals2', [
                                    'modelRoute'=>'user_notification.deleteRequest',
                                    'modelID' => $notifs['id'], 
                                    'modelName'=> $notifs['type'],
                                    'notifType' => $notifs['typeID'], 
                                    'modalTitle'=>'Delete', 
                                    'modalTitle2'=>'POST', 
                                    'modalMessage'=>'Are you sure you want to delete this?', 
                                    'formID'=>'deleteReq',
                                    'icon'=>'glyphicon-trash' ])
                               

                               

                            @endforeach
                      </div>
                      <!-- /.table-responsive -->
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer" style="background: rgba(35, 165, 220, 0.5)">
                       <!--  <a href="javascript:void(0)" class="btn btn-sm btn-primary btn-flat pull-right">View All Requests</a>
                    --> </div>
                    <!-- /.box-footer -->
                  </div>
                <!-- /.box-info -->

              <!-- ************* TIMEKEEPING BACKUP ************ -->
              <div class="box box-info" style="background: rgba(256, 256, 256, 0.6)">
                <div class="box-header with-border">
                      <h3 class="box-title">Timekeeping</h3>

                      <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                        <!-- <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button> -->
                      </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                      <p style="padding:30px; font-size: smaller;"><strong class="text-orange"><i class="fa fa-exclamation-triangle"></i> Note:</strong> Only use this  widget <span class="text-danger">in case of biometric hardware malfunction or unavailability </span> in your office floor. Any timestamp recorded in this system serves only as a backup data for timekeeping purposes.</p>
                      <h2 class="text-center text-primary"><?php echo date('l M d, Y'); ?> <br/> <span id="clock" style="font-weight: bolder;"></span> <br/><span class="text-gray" style="font-size:0.8em;">(Asia/Manila)</span> </h2>
                      <br/><br/>
                      <p class="text-center">

                        @if (!$alreadyLoggedIN)
                          <a id="btn_timein" data-timetype="1" class="timekeeping btn btn-md bg-green"><i class="fa fa-clock-o"></i> System CHECK IN </a>
                        @endif
                          <button id="btn_breakin" data-timetype="4" class="timekeeping btn btn-md btn-default"><i class="fa fa-hourglass-half"></i> Breaktime START </button> 
                          <button type="button" id="btn_breakout" data-timetype="3" class="timekeeping btn btn-md btn-default"><i class="fa fa-hourglass"></i> Breaktime END </button>
                          <a id="btn_timeout" data-timetype="2" class="timekeeping btn btn-md btn-danger"><i class="fa fa-clock-o"></i> System CHECK OUT </a>
                      </p>
                    </div>
              </div>



              <div class="box box-info" style="background: rgba(256, 256, 256, 0.6)">
                    <div class="box-header with-border">
                      <h3 class="box-title">Evaluations</h3>

                      <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                        <!-- <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button> -->
                      </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">{{ Form::open(['route' => 'evalForm.grabAllWhosUpFor', 'class'=>'', 'id'=> 'showAll' ]) }}
                      <h2 class="text-center" style="color:#9c9fa0">Show all those who are up for :</h2>
                               
                               <select name="evalType_id" class="form-control pull-left">
                                <option value="0"> --  Select One -- </option>
                                
                                @foreach ($evalTypes as $evalType)
                               
                                 <option value="{{$evalType->id}}"><?php if ($evalType->id==1 ) echo date('Y'); else if($evalType->id==2){ if( date('m')>=7 && date('m')<=12 )echo date('Y'); else echo date('Y')-1;  } ?> {{$evalType->name}}</option>
                                @endforeach
                              </select>
                        
                       <p class="text-right"> 
                      {{Form::submit(' Go ', ['class'=>'btn btn-md btn-primary', 'id'=>'showEvalBtn', 'style'=>"margin-top:20px;"])}}</p>

                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer"  style="background: rgba(35, 165, 220, 0.5)">
                    
                    </div>
                    <!-- /.box-footer -->
                     {{Form::close()}}
              </div><!-- /.box -->

                 



               



                 <!-- ************* PERFORMANCE CHART ************ -->
                 @include('layouts.charts')

               

             </div><!--end LEFT -->
             
            

              

              <div class="col-lg-5 col-sm-6 col-xs-12">

                @if ($doneSurvey == 0)
                <!--EES -->
                <div class="box box-info" style="background: rgba(256, 256, 256, 0.6)">
                    <div class="box-header with-border">
                      <h3 class="box-title">EES 2019</h3>

                      <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                        <!-- <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button> -->
                      </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">

                        <div class="item active text-center" >
                          <img src="./storage/uploads/ees.jpg" style="z-index: 2" width="95%" />
                          <br/><br/>
                          <a class="btn btn-lg btn-primary" href="./survey/1">Start Survey</a><br/><br/>
                        </div>
                    </div>
                  </div>

                  @endif


                
                


<?php /*
                  <div class="box box-info" style="background: rgba(256, 256, 256, 0.6)">
                    <div class="box-header with-border">
                      <h3 class="box-title">Videos</h3>

                      <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                        <!-- <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button> -->
                      </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                      <p style="font-size: smaller;" class="text-center">Watch <strong class="text-primary">Open Access BPO</strong> <strong>Back To The '90s</strong> Year End Party <br/> <strong class="text-orange" style="font-size: 1.8em">Same-Day Edit <span class="text-primary"> video</span>  </strong> 
                               </p>

                                <p align="center">Photos uploaded in our <a href="{{ action('HomeController@gallery',['a'=>1]) }}"><i class="fa fa-picture-o"></i> 2018 Year End Party Album</a> 
                                <a style="font-size: smaller;" href="https://www.instagram.com/explore/tags/BackToThe90s/">#WeSpeakYourLanguage #OABackToThe90s #The2018YEP</a></p> 
                              
                              <video id="teaser" src="storage/uploads/sde-back_to_the_90s.webm" width="100%" loop controls></video>
                    </div>
                  </div>


                  <div class="box box-info" style="background: rgba(256, 256, 256, 0.6)">
                    <div class="box-header with-border">
                      <h3 class="box-title">Videos</h3>

                      <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                        <!-- <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button> -->
                      </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                      <p class="text-center"><strong class="text-orange" style="font-size: 1.8em">Speak Your Language <span class="text-primary"> Video Contest!</span></strong></p>
                      <video id="teaser" src="storage/uploads/alessio.webm" width="100%" loop controls></video>

                     

                                <p style="font-size: 1em;" align="center"><strong>HOW TO JOIN: </strong><br/>
                                  1. Take a video of yourself and state your favorite word/phrase in your mother language. Translate it in English (no need for translation if your mother language is English) and state a brief explanation as to why it is your favorite. <br/>
                                  Video Template: Hi! I’m <state your name> and my mother language is <your mother language>. My favorite word /phrase is <state the word/phrase>. In English, it translates to <state English translation>. This is my favorite because <state your reason>. <br/>
                                  2.  Make sure your entry is free from profanity, obscenity, or any objectionable or inappropriate content. <br/>
                                  3.  Entry must not be more than 30 seconds long. <br/>
                                  4.  You may edit the videos by including the words/phrases, but don't overdo it.  You may also use actual props to add spice to your entry.  <br/>
                                  5.  To qualify, complete the following steps before uploading your entry on Instagram: <br/>
                                  Follow @OpenAccessBPO on Instagram<br/>
                                  Include the following hashtags in your caption: #WeSpeakYourLanguage #OAonIMLD #OpenAccessBPO <br/>
                                   Set your account to Public so we can view your post <br/>
                                  6.  Deadline of submission is until 12:00 AM, February 27, 2019. (Pro tip: The earlier you have it up, the better your chances are in gaining more likes!)<br/>
                                  7.  Winners will be announced on February 28, 2019 via Zimbra and Open Access BPO's official Instagram and Facebook page.<br/>
                                  8.  This contest is open to all Open Access BPO Makati and Davao site employees. <br/><br/>
                                   CRITERIA: <br/>
                                   30% - Creativity/Originality: Uniqueness of the chosen word/phrase<br/>
                                  30% -  Message content: Relevance of chosen word/phrase and context of explanation<br/>
                                  30% - Video quality:  Audio and visual quality, editing (if applicable), adherence to time limit <br/>
                                  10% - People’s Choice/ Most Likes:  Videos will be ranked based on the number of likes and the corresponding points will be added to the final judges’ score <br/><br/>
                                   CASH PRIZES:  <br/>
                                   3rd place – Php 1,000.00 <br/> 
                                   2nd place – Php 1,500.00 <br/> 
                                   1st place – Php 2,500.00 <br/> 









                                  </p> 
                              
                              
                    </div>
                  </div>
                  */ ?>
               

                

                 


                <!-- SHOUT OUT -->
                  <div class="box box-info" style="background: rgba(256, 256, 256, 0.6)">
                    <div class="box-header with-border">
                      <h3 class="box-title">Shout Out</h3>

                      <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                        <!-- <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button> -->
                      </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body" id="ads">
                     


                        <div id="myCarousel" class="carousel slide" data-ride="carousel">
                          <!-- Indicators -->
                          <!-- <ol class="carousel-indicators">
                            <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
                            <li data-target="#myCarousel" data-slide-to="1"></li>
                            <li data-target="#myCarousel" data-slide-to="2"></li>
                            <li data-target="#myCarousel" data-slide-to="3"></li>
                          </ol> -->

                          <!-- Wrapper for slides -->
                          <div class="carousel-inner" role="listbox">
                            
                            @include('layouts.slider')

                          </div><!--end CAROUSEL -->

                          <!-- Left and right controls -->
                          <a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev">
                            <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                            <span class="sr-only">Previous</span>
                          </a>
                          <a class="right carousel-control" href="#myCarousel" role="button" data-slide="next">
                            <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                            <span class="sr-only">Next</span>
                          </a>
                        </div>
                      

                    </div>
                    <!-- /.box-body -->
                    
                    
                  </div>
                <!-- /.box -->



                
                <!--VIDEOS -->
                


              </div><!--end RIGHT -->


             
             


             
             
              <br/><br/><br/><hr/>

            
                      

          </div><!--end of row -->
       
     </section>

     <!----------------- MEMO ---------------->
      @if (!is_null($memo) && $notedMemo != true)
        @include('layouts.modals-memo', [
                                  'modelRoute'=>'user_memo.store',
                                  'modelID' => $memo->id, 
                                  'modelName'=>$memo->title, 
                                  'modalTitle'=>$memo->title, 
                                  'modalMessage'=> $memo->body, 
                                  'formID'=>'memo',
                                  'icon'=>'glyphicon-check' ])
      @endif

    

          



@endsection


@section('footer-scripts')
<!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <!-- <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="{{ asset('public/js/popper.min.js') }}"></script>
    <script src="{{ asset('public/js/bootstrap.min.js') }}"></script> -->


<script>
  $.widget.bridge('uibutton', $.ui.button);
</script>





<!-- Page script -->
<!-- Morris.js charts -->

<script type="text/javascript" src="{{asset('public/js/morris.min.js')}}"></script>
<script type="text/javascript" src="{{asset('public/js/raphael.min.js')}}"></script>
<script type="text/javascript" src="{{asset('public/js/intro.js')}}"></script>
   
<script>

  $(function () {
   'use strict';

   /* ---- VIDEO PLAYER -------- */
   /*var vid = document.getElementById("teaser");
   vid.onplay = function() {
        //alert("The video has started to play");
        $.ajax({
                url: "{{action('HomeController@logAction','5')}}",
                type: "GET",
                data: {'action': '5'},
                success: function(response){
                          console.log(response);

              }

        });
    };*/
     /* ---- VIDEO PLAYER -------- */

   $(window).bind("load", function() {

      getNewNotifications();
      startTime();

     @if (!is_null($memo) && $notedMemo != true)
     $('#memo'+{{$memo->id}}).modal({backdrop: 'static', keyboard: false, show: true});
     @endif

     // * if(!is_null($siteTour) && $notedTour != true)
     
     // introJs().setOption('doneLabel', "Got it. Don't show this to me again").start().oncomplete(function(){
     //  $('#controlsidebar').addClass('control-sidebar-open');
     //  var _token = "{{ csrf_token() }}";
        

     //    //--- update user notification first
     //    $.ajax({
     //        url: "{{action('UserMemoController@saveUserMemo')}}",
     //        type:'POST',
     //        data:{ 
     //          'id': "{{$siteTour->id}}",
     //          '_token':_token
     //        },

     //        success: function(res){
     //                console.log(res);
     //        },
     //      });

     //  console.log("open it");
     // });
     // * endif

     


});

   /*------------- TIMEKEEPING --------------*/
   //QUORA
   // MOUS
   // AVA
   // WORLDVENTURES
   // OPS


   function startTime() 
   {
    
      var d = new Date();
      var utc = d.getTime() + (d.getTimezoneOffset() * 60000);
      var today = new Date(utc + (3600000*8));

      
      var h = today.getHours() > 12 ? today.getHours() - 12 : today.getHours();
      var m = today.getMinutes();
      var s = today.getSeconds();

      var am_pm = today.getHours() >= 12 ? "PM" : "AM";

      m = checkTime(m);
      s = checkTime(s);

      document.getElementById('clock').innerHTML =
      h + ":" + m + ":" + s + " " + am_pm
      var t = setTimeout(startTime, 500);
   }

  function checkTime(i) {
      if (i < 10) {i = "0" + i};  // add zero in front of numbers < 10
      return i;
  }


   $('.timekeeping').on('click', function(){

      var logtype_id = $(this).attr('data-timetype');
      var btn = $(this);
      var _token = "{{ csrf_token() }}";

      var clocktime = $('#clock').text();
      $.ajax({
          url: "{{action('LogsController@saveDashboardLog')}}",
          type:'POST',
          data:{ 
            'logtype_id': logtype_id,
            'clocktime': clocktime,
            '_token':_token
          },

          success: function(res){
                  console.log(res);

                  switch(logtype_id){
                    case '1': {
                                  $.notify("System CHECK IN successful. \n\nYou may check your DTR Sheet to verify.\nShould you find any form of data discrepancy, kindy submit a DTRP for approval.",{className:"success",globalPosition:'left top',autoHideDelay:7000, clickToHide:true} );
                                  btn.fadeOut("slow");
                                } break;
                    case '2': { 
                                  $.notify("System CHECK OUT successful. \n\nDon't forget to sign out from E.M.S as well. See you later, and take care.",{className:"success",globalPosition:'left top',autoHideDelay:7000, clickToHide:true} );
                                  btn.fadeOut("slow");

                              }break;
                    case '3': { 
                                  $.notify("End Breaktime. \n\n",{className:"success",globalPosition:'left top',autoHideDelay:7000, clickToHide:true} );
                                  btn.attr('disabled',true);
                                  $('#btn_breakin').attr('disabled',false);

                              }break;
                    case '4': { 

                              btn.attr("disabled",'disabled');
                              $('#btn_breakout').attr('disabled',false);


                              $.notify("BREAKTIME. \n\nDon't forget to click the Breaktime END button once you get back from your break.",{className:"success",globalPosition:'left top',autoHideDelay:7000, clickToHide:true} ); }break;

                  }
                  
                  
          },
          error: function(res){
                $.notify("Sorry, an error occured while saving your logs. \n\nPlease try again later.",{className:"error",globalPosition:'left top',autoHideDelay:7000, clickToHide:true} );

          }
        }); 
  });



   /*----------------- MEMO ----------------*/

  
      
   @if (!is_null($memo) && $notedMemo != true)
      $('#yesMemo').on('click',function(){

        var _token = "{{ csrf_token() }}";
        

        //--- update user notification first
        $.ajax({
            url: "{{action('UserMemoController@saveUserMemo')}}",
            type:'POST',
            data:{ 
              'id': "{{$memo->id}}",
              '_token':_token
            },

            success: function(res){
                    console.log(res);
                    location.replace('./survey/1');
            },
          });

      });
   @endif
      

   




  /*---------- POSTMATES WIDGET ----------- */
   @if(count($groupedForm)>0)

   $('.escalation_online_order, .escalation_phone, .escalation_tablet').hide();
   //.merchant_closed_confirmation, .Open_confirmation, .confirmed_options 
   //
   $('select[name="escalation"]').on('change', function(){ 

      var escalation = $('select[name="escalation"] :selected').val();

      switch (escalation)
      {
        case "online_order": { $('.escalation_online_order').fadeIn();$('.escalation_phone, .escalation_tablet, .req').hide();} break;
        case "phone":  { $('.escalation_phone').fadeIn();$('.escalation_online_order, .escalation_tablet').hide();} break;
        case "tablet": { $('.escalation_tablet').fadeIn();$('.escalation_online_order, .escalation_phone').hide();} break;
        default:  $('.escalation_online_order, .escalation_phone, .escalation_tablet').hide();
      }

   

   });

  


   /********** signal verification *************/

   $('.select_6, .select_7, .select_8, .label_6_2, .label_7_2, .label_8_2').hide();

  $('select.formItem').on('change',function(){


    var itemName = $(this)[0]['name'];
    var formID = $(this).attr('data-formID');
    var selectedItem = $('select[name='+itemName+'] :selected').val();
    var itemOrder = $(this)[0]['tabIndex'];
    var s = "."+selectedItem.toLowerCase()+"_"+itemName;
    //var elemID = $(this)[0]['id'];

    if (itemOrder=='5' && formID=='2'){
      $('.label_6_2, .label_7_2, .label_8_2').fadeOut();
    } else if (itemOrder=='6' && formID=='2'){
      $('.label_7_2, .label_8_2, .label_9_2').fadeOut();
    } else if (itemOrder=='7' && formID=='2'){
      $('.label_8_2, .label_9_2, .label_10_2').fadeOut();
    }




    if (selectedItem.toLowerCase() == "yes" || selectedItem.toLowerCase() == "no"){
      $('select_'+(itemOrder+1), 'select_'+itemOrder).hide();
      var s = ".confirmed_"+itemName;
      
      var x = $(s);
      //var y = x[0]['children'][2];

      //y.

      // console.log("className:");
      // console.log(x[1]['className']);
     if (x.length == 0 
            || x[1]['className'] == "select_7_2 formItem form-control confirmed_merchant_refused_confirmation"
            || (x.length == 2 && x.selector ==".confirmed_merchant_cash_only_confirmation") )
          {
        var newItem = $(this).parent();
        console.log("parent: ");
        $('.added').fadeOut();
        console.log(newItem);
        var htmlcode ='<label class="added pull-left" style="font-weight: bolder; padding-top: 20px; display: inline-block;"><strong>Confirmed</strong>';
        htmlcode += '     <select id="x" data-from="'+itemName+'" class="form-control formItem">';
        htmlcode += '         <option value="Confirmed_By_Phone" >Confirmed By Phone</option>';
        htmlcode += '         <option value="Confirmed_By_Voicemail">Confirmed By Voicemail</option>';
        htmlcode += '         <option value="Confirmed_Online">Confirmed Online</option>';
        htmlcode == '     </select></label>';

        newItem.append(htmlcode);
      } else $(s).fadeIn();

      //$(y).html('<option value="Confirmed_By_Phone">Confirmed By Phone</option><option value="Confirmed_By_Voicemail">Confirmed By Voicemail</option><option value="Confirmed_Online">Confirmed Online</option>');
      

    }
    else {

      $('.added').fadeOut();

      if(formID == '2'){
        //$('label[name="'+itemName+'"]').fadeOut();//'select.select_'+(itemOrder+1), 'select.select_'+itemOrder, 
        $('.label_'+itemOrder+'_'+formID,'.label_'+(itemOrder+1)+'_'+formID).fadeOut();
        $(s).fadeIn();
      }
      

      console.log("OPEN : "+ s );
    console.log(s);
    console.log("order: "+itemOrder+" | selectedItem: "+selectedItem+" | index: "+itemOrder)
    }
   

    console.log("FormID: "+formID);

    

   });





   $('.submit').on('click',function(e){
      e.preventDefault();

      $('input,textarea,select').filter('[required]:visible').each(
            function(){
              var checkCt=0;
              var v = $(this).val();
              if (v == ""){
                $(this).css('border',"solid 3px #e24527");
                return false;
              } 
              
                $(this).css('border',"none");
                if (v == "- select one -") 
                  return false;
                     
              
            }
        ).promise().done(function(){
          var _token = "{{ csrf_token() }}";
          var formItems_select = $('select.formItem').filter(':visible');
          var formItems_input = $('input.formItem').filter(':visible');
          var formItems_textarea = $('textarea.formItem').filter(':visible');
          
          var formItems ={}; //, inputs: formItems_input, textareas: formItems_textarea }
          var ctr=0;

          formItems_input.each(function(){
             var n = $(this);
             if (n[0]['name'] !== "agent") {
              formItems[ ctr+'_'+n[0]['id'] ] = $(this).val();
             }
             
             ctr++;
          });

          formItems_select.each(function(){
             var n = $(this);
             formItems[ ctr+'_'+n[0]['id'] ] = $(this).val();
             if (n[0]['id']=='x'){
              formItems[ n[0]['id']+'_from' ] = $(this).attr('data-from');
             }
             ctr++;
          });

          formItems_textarea.each(function(){
             var n = $(this);
             formItems[ ctr+'_'+n[0]['id'] ] = $(this).val();
             ctr++;
          });
          console.log(formItems);
            $.ajax({
                        url: "{{action('FormSubmissionsController@process')}}",
                        type:'POST',
                        data:{ 
                          'formItems': formItems,
                          'user_id':"{{Auth::user()->id}}",
                          '_token':_token
                        },

                       
                        success: function(res)
                        {
                          console.log(res);
                          $.ajax({
                                      url: "{{action('HomeController@logAction','3')}}",
                                      type: "GET",
                                      data: {'action': '3','formid': res.formid, 'usersubmit':res.usersubmit},
                                      success: function(response){
                                                console.log(response);

                                    }

                          });

                          if (res.status == '0')
                            $.notify(res.error,{className:"error",globalPosition:'right center',autoHideDelay:7000, clickToHide:true} );
                          else {
                            $('button[name="submit"]').fadeOut();
                            $.notify("Form successfully submitted.",{className:"success",globalPosition:'right center',autoHideDelay:7000, clickToHide:true} );
                            window.setTimeout(function(){
                                          window.location.href = "{{action('HomeController@index')}}";
                                        }, 2000);
                          }

                           
                        }, error: function(res){
                          console.log("ERROR");
                          $.notify("An error occured. Please try re-submitting later.",{className:"error",globalPosition:'right center',autoHideDelay:7000, clickToHide:true} );
                            
                        }


              });
        });
   
  
    
   });


   

   @endif
  /*---------- END POSTMATES WIDGET ----------- */



   

@if(count($performance)>0)
@include('layouts.charts-scripts')
@endif


   $('#myCarousel').carousel();

   var datatable = $("#requests").DataTable({
                      "ajax": "{{action('UserNotificationController@getApprovalNotifications', Auth::user()->id)}}",
                      "processing":true,
                      "stateSave":false,
                      "lengthMenu":[5, 10, 50],
                      "pageLength": 5,
                      "order": [ 0, "desc" ],
                      "columns": [
                            { sorting:false, title: "", defaultContent: "<i>none</i>" , data:'id', render:function(data,type,full,meta){return '<span style="visibility:hidden">'+data+'</span><i class="fa '+full.icon+'"></i>';}}, 
                            
                            { title: "Requests", defaultContent: "<i>none</i>" , data:'type', render:function(data,type,full,meta){return '<a href="#" data-toggle="modal" data-target="#myModal_DTRP'+full.id+'"> <strong>'+data+'</strong></a><br/><small>from: '+full.user+' - </small>';}},  
                            { title: "Date Filed", defaultContent: " ", data:'productionDate', render:function(data,type,full){
                              var formattedDate = new Date(full.deets.created_at);
                              var formattedDate2 = new Date(data);
                              var d1 = formattedDate.getDate();
                              var mo = formattedDate.toLocaleString('en-us', { month: "short" });

                              //formattedDate.getMonth();
                              //mo += 1;  // JavaScript months are 0-11
                              var y1 = formattedDate.getDay();
                              var weekday = new Array(7);
                                  weekday[0] =  "SUN";
                                  weekday[1] = "MON";
                                  weekday[2] = "TUE";
                                  weekday[3] = "WED";
                                  weekday[4] = "THU";
                                  weekday[5] = "FRI";
                                  weekday[6] = "SAT";

                                  var n = weekday[y1];


                              var d = formattedDate2.getDate();
                              var m =  formattedDate2.getMonth();
                              //m += 1;  // JavaScript months are 0-11
                              var y = formattedDate2.getFullYear();
                              return mo+'. '+d1+' '+n+'<br/><small><em>for: '+data+'</em></small>';}}, 

                            { title: "Actions", data:'id', class:'text-center', width:'180', sorting:false, 
                              render: function ( data, type, full, meta ) {
                              var _token = "{{ csrf_token() }}";
                              var formattedDate = new Date(full.deets.created_at);
                              var profileimg = "./public/img/employees/"+full.requestor+".jpg";
                              var icon = "";

                              @if ( file_exists('public/img/employees/'.Auth::user()->id.'.jpg') )
                              var userimg= "./public/img/employees/"+full.user_id+".jpg";

                              @else
                              var userimg= "{{asset('public/img/useravatar.png')}}";

                              @endif

                              switch(full.typeID)
                              {
                                case 6: icon="fa-calendar-times-o";break;
                                case 7: icon="fa-hourglass";break;
                                case 8: icon="fa-sign-in";break;
                                case 9: icon="fa-sign-out";break;
                                case 10: icon='fa-plane';break;
                                case 11: icon='fa-stethoscope';break;
                                case 12: icon='fa-meh-o';break;
                                case 13: icon='fa-suitcase';break;
                                case 14: icon="fa-unlock";break;
                                case 15: icon="fa-clock-o";break;
                                case 16: icon="fa-female";break;
                                case 17: icon="fa-male";break;
                                case 18: icon="fa-street-view";break;
                              }
            

                              
                              var modalcode = '<div class="modal fade" id="myModal_DTRP'+data+'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button><h4 class="modal-title text-black" id="myModalLabel"><i class="fa '+icon+'"></i> '+full.type+'</h4></div> <div class="modal-body-upload" style="padding:20px;">';

                              modalcode += '<!-- DIRECT CHAT PRIMARY -->';
          modalcode += '<div class="box box-default direct-chat">';

          modalcode += '  <div class="box-body">';
          modalcode += '    <!-- Conversations are loaded here -->';
          modalcode += '    <div class="direct-chat-messages">';
          modalcode += '      <!-- Message. Default to the left -->';
          modalcode += '      <div class="direct-chat-msg">';
          modalcode += '        <div class="direct-chat-info clearfix">';
          modalcode += '          <span class="direct-chat-name pull-left">'+full.nickname+'</span>';
          modalcode += '          <span class="direct-chat-timestamp pull-right">'+formattedDate+'</span>';
          modalcode += '        </div>';
          modalcode += '        <!-- /.direct-chat-info -->';
          modalcode += '        <a href="./user/'+full.requestor+'" target="_blank"><img src="'+profileimg+'" class="img-circle pull-left" alt="User Image" width="70" /></a>';
          modalcode += '        <div class="direct-chat-text" style="width:85%; left:30px; background-color:#fcfdfd">';
          
                              
                              var mc1 ="";
                              var delModal ='<div class="modal fade" id="myModal'+full.id+'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button><h4 class="modal-title" id="myModalLabel"> Delete '+full.type+'</h4></div><div class="modal-body">Are you sure you want to delete this?</div><div class="modal-footer no-border"><form action="./user_notification/deleteRequest/'+full.id+'" method="POST" class="btn-outline pull-right" id="deleteReq"><input type="hidden" name="notifType" value="'+full.typeID+'" /><button type="submit" class="btn btn-primary glyphicon-trash glyphicon ">Yes</button><button type="button" class="btn btn-default" data-dismiss="modal">Close</button><input type="hidden" name="_token" value="'+_token+'" /> </div></div></div></div>';

                              

                                        
                              switch (full.typeID)
                              {
                                //CWS
                                case 6: { 
                                           var shiftStart_new = new Date(full.productionDate+ " "+full.deets.timeStart).toLocaleString('en-US', { hour: 'numeric', minute:'numeric', hour12: true });
                                           var shiftEnd_new = new Date(full.productionDate+ " "+full.deets.timeEnd).toLocaleString('en-US', { hour: 'numeric', minute:'numeric',hour12: true });
                                           modalcode += '<p class="text-left">Hi {{$greeting}} ! <br/>';
                                            modalcode += 'I would like to request a <strong>CHANGE OF WORK SCHEDULE</strong>. <br/><strong>Reason: </strong><em>'+full.deets.notes+'</em></p>';
                                            modalcode += '<div class="row"><div class="col-sm-12"> <div class="row"><div class="col-sm-4"><h5 class="text-primary">Production Date</h5><p style="font-weight:bold">'+full.productionDate+'<br/> ['+full.productionDay+']</p></div>';
                                            modalcode += '<div class="col-sm-4"><h5 class="text-primary">Old Schedule:</h5></div><div class="col-sm-4"><h5 class="text-primary">New Schedule</h5></div>';
                                            var shiftStart = new Date(full.productionDate+ " "+full.deets.timeStart_old).toLocaleString('en-US', { hour: 'numeric', minute:'numeric', hour12: true });
                                            var shiftEnd = new Date(full.productionDate+ " "+full.deets.timeEnd_old).toLocaleString('en-US', { hour: 'numeric', minute:'numeric',hour12: true });

                                            mc1 += '<div class="col-sm-4" style="font-size: 12px">';

                                            if(full.deets.timeStart_old == "00:00:00" && full.deets.timeEnd_old== "00:00:00")
                                              mc1 += '<p>Shift: <br/><strong>Rest Day </strong></p>';
                                            else
                                              mc1 += '<p>Shift: <br/><strong>'+shiftStart+'  -  '+shiftEnd+' </strong></p>';

                                            mc1 += '</div><div class="col-sm-4" style="font-size: 12px">';

                                            if(full.deets.timeStart == "00:00:00" && full.deets.timeEnd=="00:00:00")
                                              mc1 += '<p>Shift: <br/><strong>Rest Day </strong></p></div>';
                                            else
                                              mc1 += '<p>Shift: <br/><strong>'+shiftStart_new+' -  '+shiftEnd_new+'</strong></p></div>';

                                          //mc1 += '<div class="row"><div class="col-sm-12">'+full.deets.notes+'</div></div>';
                                      } break;

                                case 7: {
                                            var shiftStart_new = new Date(full.productionDate+ " "+full.deets.timeStart).toLocaleString('en-US', { hour: 'numeric', minute:'numeric', hour12: true });
                                            var shiftEnd_new = new Date(full.productionDate+ " "+full.deets.timeEnd).toLocaleString('en-US', { hour: 'numeric', minute:'numeric',hour12: true });

                                            modalcode += '<p class="text-left">Hi {{$greeting}} ! ';
                                            modalcode += 'I would like to file an <strong>OT </strong> for <strong>'+full.productionDate+' ['+full.productionDay+']</strong></p>';
                                            modalcode += '<div class="row">';

                                            modalcode +='<div class="col-sm-6" style="font-size: 12px"><h5 class="text-primary">OT Details:</h5>';
                                            modalcode +=' <p class="text-left"><strong>Start: </strong>'+full.deets.timeStart;
                                            modalcode +='<br/><strong>End : </strong>'+full.deets.timeEnd;
                                            modalcode += '<br/><strong>Billable Hours: </strong>'+full.deets.billable_hours;
                                            modalcode += '<br/><strong>Filed Hours worked: </strong>'+full.deets.filed_hours;

                                            if (full.deets.billedType == '1')
                                            modalcode += '<br/><strong>OT Type: </strong> Billed';
                                            else if (full.deets.billedType == '2')
                                              modalcode += '<br/><strong>OT Type: </strong> Non-Billed';
                                            else if (full.deets.billedType == '3')
                                              modalcode += '<br/><strong>OT Type: </strong> Patch';
                                            else modalcode += '<br/><strong>OT Type: </strong> Billed';

                                            modalcode += '</p></div> <div class="col-sm-5" style="font-size: 12px"><h5 class="text-primary">Reason:</h5>';
                                            modalcode += '<p class="text-left"><em>'+full.deets.reason+'</em></p> </div>';
                                        };break;
                                
                                // ---- DTRP IN
                                case 8: { 
                                          var shiftStart_new = new Date(full.productionDate+ " "+full.deets.timeStart).toLocaleString('en-US', { hour: 'numeric', minute:'numeric', hour12: true });
                                          var shiftEnd_new = new Date(full.productionDate+ " "+full.deets.timeEnd).toLocaleString('en-US', { hour: 'numeric', minute:'numeric',hour12: true });

                                          modalcode += '<p class="text-left">Hi {{$greeting}} ! ';
                                          modalcode += 'I would like to file a <strong>DTRP IN</strong>. See details below:</p>';
                                          modalcode += '<div class="row"><div class="col-sm-12"> <div class="row"><div class="col-sm-4" style="font-size: 12px"><h5 class="text-primary">Production Date</h5><p style="font-weight:bold">'+full.productionDate+'<br/> ['+full.productionDay+']</p></div>';
                                          modalcode += '<div class="col-sm-4" style="font-size: 12px"><h5 class="text-primary">Log IN Time</h5>';
                                                  modalcode += '<p><br/><strong>'+full.deets.logTime +'</strong></p></div><div class="col-sm-4"><h5 class="text-primary">Notes</h5>';

                                                  modalcode += '<p><br/><em>'+full.deets.notes+'</em></p></div>';};break;

                                // ---- DTRP OUT
                                case 9: { 

                                         var shiftStart_new = new Date(full.productionDate+ " "+full.deets.timeStart).toLocaleString('en-US', { hour: 'numeric', minute:'numeric', hour12: true });
                                         var shiftEnd_new = new Date(full.productionDate+ " "+full.deets.timeEnd).toLocaleString('en-US', { hour: 'numeric', minute:'numeric',hour12: true });

                                          modalcode += '<p class="text-left">Hi {{$greeting}} ! ';
                                          modalcode += 'I would like to file a <strong>DTRP OUT</strong>. See details below:</p>';
                                          modalcode += '<div class="row"><div class="col-sm-12"> <div class="row"><div class="col-sm-4" style="font-size: 12px"><h5 class="text-primary">Production Date</h5><p style="font-weight:bold">'+full.productionDate+'<br/> ['+full.productionDay+']</p></div>';

                                          modalcode += '<div class="col-sm-4 style="font-size: 12px""><h5 class="text-primary">Log OUT Time</h5>';
                                                  modalcode += '<p><br/><strong>'+full.deets.logTime +'</strong></p></div><div class="col-sm-4"><h5 class="text-primary">Notes</h5>';
                                                  modalcode += '<p><br/><em>'+full.deets.notes+'</em></p></div>';
                                      };break;

                                //VACATION LEAVE
                                case 10: { 

                                          var leaveStart = moment(full.deets.leaveStart); //,"MM/D/YYYY h:m A");
                                          var leaveEnd = moment(full.deets.leaveEnd); //,"MM/D/YYYY h:m A");
                                          var duration = moment.duration(leaveEnd.diff(leaveStart));
                                          var hours = duration.asHours();
                                          

                                          if (full.deets.totalCredits % 1 === 0) var totalcreds = Math.floor(full.deets.totalCredits);
                                          else{

                                            if(full.deets.totalCredits == '0.50') var totalcreds = "half";
                                              else var totalcreds = full.deets.totalCredits;
                                            }

                                          modalcode += '<p class="text-left">Hi {{$greeting}} ! <br/>';
                                          modalcode += 'I would like to file a <strong class="text-danger">'+totalcreds+'-day </strong><strong>VACATION LEAVE &nbsp;&nbsp;</strong><br/><br/>';
                                         

                                         // modalcode += '<strong>VL credits used: </strong><span class="text-danger">'+full.deets.totalCredits+'</span><br/>';
                                          modalcode += '<strong>Reason: </strong><em>'+full.deets.notes+'</em></p>';
                                          modalcode += '<div class="row"><div class="col-sm-12"> <div class="row">';
                                          modalcode += '<div class="col-sm-6"><h5 class="text-primary">From: </h5></div><div class="col-sm-6"><h5 class="text-primary">Until: </h5></div>';

                                               


                                                  mc1 += '<div class="col-sm-6" style="font-size: 12px">';

                                                  
                                                  mc1 += '<p><strong>'+leaveStart.format("MMM DD, ddd hh:mm A")+' </strong></p>';

                                                  mc1 += '</div><div class="col-sm-6" style="font-size: 12px">';
                                                  mc1 += '<p><strong>'+leaveEnd.format("MMM DD, ddd hh:mm A")+'</strong></p></div>';

                                                  //mc1 += '<div class="row"><div class="col-sm-12">'+full.deets.notes+'</div></div>';
                                            } break;


                                
                                //SICK LEAVE
                                case 11: { 

                                          var leaveStart = moment(full.deets.leaveStart); //,"MM/D/YYYY h:m A");
                                          var leaveEnd = moment(full.deets.leaveEnd); //,"MM/D/YYYY h:m A");
                                          var duration = moment.duration(leaveEnd.diff(leaveStart));
                                          var hours = duration.asHours();
                                          

                                          if (full.deets.totalCredits % 1 === 0) var totalcreds = Math.floor(full.deets.totalCredits);
                                          else{

                                            if(full.deets.totalCredits == '0.50') var totalcreds = "half";
                                              else var totalcreds = full.deets.totalCredits;
                                            }

                                          modalcode += '<p class="text-left">Hi {{$greeting}} ! <br/>';
                                          modalcode += 'I would like to file a <strong class="text-danger">'+totalcreds+'-day </strong><strong>SICK LEAVE &nbsp;&nbsp;</strong>';
                                         

                                          if (full.deets.attachments != null)
                                          modalcode += '<span class="pull-right" style="font-size:smaller"><i class="fa fa-paperclip"></i> <a href="./user_sl/medCert/'+full.deets.id+'" target="_blank">Medical Certificate <br/> &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp;attached</a></span> ';

                                          modalcode += '<br/><br/><strong>Reason: </strong><em>'+full.deets.notes+'</em></p>';
                                          modalcode += '<div class="row"><div class="col-sm-12"> <div class="row">';
                                          modalcode += '<div class="col-sm-6"><h5 class="text-primary">From: </h5></div><div class="col-sm-6"><h5 class="text-primary">Until: </h5></div>';

                                               


                                                  mc1 += '<div class="col-sm-6" style="font-size: 12px">';

                                                  
                                                  mc1 += '<p><strong>'+leaveStart.format("MMM DD, ddd hh:mm A")+' </strong></p>';

                                                  mc1 += '</div><div class="col-sm-6" style="font-size: 12px">';
                                                  mc1 += '<p><strong>'+leaveEnd.format("MMM DD, ddd hh:mm A")+'</strong></p></div>';

                                                  //mc1 += '<div class="row"><div class="col-sm-12">'+full.deets.notes+'</div></div>';
                                            } break;


                                

                                //LWOP LEAVE
                                case 12: { 

                                          var leaveStart = moment(full.deets.leaveStart); //,"MM/D/YYYY h:m A");
                                          var leaveEnd = moment(full.deets.leaveEnd); //,"MM/D/YYYY h:m A");
                                          var duration = moment.duration(leaveEnd.diff(leaveStart));
                                          var hours = duration.asHours();
                                          

                                          if (full.deets.totalCredits % 1 === 0) var totalcreds = Math.floor(full.deets.totalCredits);
                                          else{

                                            if(full.deets.totalCredits == '0.50') var totalcreds = "half";
                                              else var totalcreds = full.deets.totalCredits;
                                            }

                                          modalcode += '<p class="text-left">Hi {{$greeting}} ! <br/>';
                                          modalcode += 'I would like to file a <strong class="text-danger">'+totalcreds+'-day </strong><strong>LEAVE WITHOUT PAY &nbsp;&nbsp;</strong><br/><br/>';
                                         

                                         // modalcode += '<strong>VL credits used: </strong><span class="text-danger">'+full.deets.totalCredits+'</span><br/>';
                                          modalcode += '<strong>Reason: </strong><em>'+full.deets.notes+'</em></p>';
                                          modalcode += '<div class="row"><div class="col-sm-12"> <div class="row">';
                                          modalcode += '<div class="col-sm-6"><h5 class="text-primary">From: </h5></div><div class="col-sm-6"><h5 class="text-primary">Until: </h5></div>';

                                               


                                                  mc1 += '<div class="col-sm-6" style="font-size: 12px">';

                                                  
                                                  mc1 += '<p><strong>'+leaveStart.format("MMM DD, ddd hh:mm A")+' </strong></p>';

                                                  mc1 += '</div><div class="col-sm-6" style="font-size: 12px">';
                                                  mc1 += '<p><strong>'+leaveEnd.format("MMM DD, ddd hh:mm A")+'</strong></p></div>';

                                                  //mc1 += '<div class="row"><div class="col-sm-12">'+full.deets.notes+'</div></div>';
                                            } break;

                                

                                //OBT LEAVE
                                case 13: { 

                                          var leaveStart = moment(full.deets.leaveStart); //,"MM/D/YYYY h:m A");
                                          var leaveEnd = moment(full.deets.leaveEnd); //,"MM/D/YYYY h:m A");
                                          var duration = moment.duration(leaveEnd.diff(leaveStart));
                                          var hours = duration.asHours();
                                          

                                          if (full.deets.totalCredits % 1 === 0) var totalcreds = Math.floor(full.deets.totalCredits);
                                          else{

                                            if(full.deets.totalCredits == '0.50') var totalcreds = "half";
                                              else var totalcreds = full.deets.totalCredits;
                                            }

                                          modalcode += '<p class="text-left">Hi {{$greeting}} ! <br/>';
                                          modalcode += 'I would like to file a <strong class="text-danger">'+totalcreds+'-day </strong><strong>OFFICIAL BUSINESS TRIP &nbsp;&nbsp;</strong><br/><br/>';
                                         

                                         // modalcode += '<strong>VL credits used: </strong><span class="text-danger">'+full.deets.totalCredits+'</span><br/>';
                                          modalcode += '<strong>Reason: </strong><em>'+full.deets.notes+'</em></p>';
                                          modalcode += '<div class="row"><div class="col-sm-12"> <div class="row">';
                                          modalcode += '<div class="col-sm-6"><h5 class="text-primary">From: </h5></div><div class="col-sm-6"><h5 class="text-primary">Until: </h5></div>';

                                               


                                                  mc1 += '<div class="col-sm-6" style="font-size: 12px">';

                                                  
                                                  mc1 += '<p><strong>'+leaveStart.format("MMM DD, ddd hh:mm A")+' </strong></p>';

                                                  mc1 += '</div><div class="col-sm-6" style="font-size: 12px">';
                                                  mc1 += '<p><strong>'+leaveEnd.format("MMM DD, ddd hh:mm A")+'</strong></p></div>';

                                                  //mc1 += '<div class="row"><div class="col-sm-12">'+full.deets.notes+'</div></div>';
                                            } break;

                                  

                                   

                                  //DTR UNLOCK
                                case 14: { 

                                          var prodFrom = moment(full.productionFrom);
                                          var prodTo = moment(full.productionTo);


                                          modalcode += '<p class="text-left">Hi {{$greeting}} !</p> <br/>';
                                          modalcode += '<p class="text-center">I would like to file to have my  <strong>DTR Sheet</strong> from <br/><span class="text-danger">'+prodFrom.format("MMM. DD YYYY - ddd")+'</span> to <span class="text-danger">'+prodTo.format("MMM. DD YYYY - ddd")+'</span> <strong><br/>UNLOCKED</strong><br/><br/><a href="./seen-unlockRequest/'+full.notification_id+'?seen=true" class="btn btn-xs btn-primary"><i class="fa fa-calendar"></i> View DTR Sheet<a><br/></p>';
                                         

                                        
                                          modalcode += '<div class="row"><div class="col-sm-12">';
                                          modalcode += '<div class="col-sm-6"></div><div class="col-sm-6"></div>';

                                               


                                                  mc1 += '<div class="col-sm-6" style="font-size: 12px">';

                                                  
                                                  mc1 += '<p><strong> </strong></p>';

                                                  mc1 += '</div><div class="col-sm-6" style="font-size: 12px">';
                                                  mc1 += '<p><strong></strong></p></div>';

                                                  //mc1 += '<div class="row"><div class="col-sm-12">'+full.deets.notes+'</div></div>';
                                            } break;

                                //PRE SHIFT OT 
                                case 15: { 
                                            var billedType=" ";

                                            if(full.deets.billedType == '1') billedType="Billed";
                                            else if (full.deets.billedType == '2') billedType="Non-Billed";
                                            else if (full.deets.billedType == '3') billedType="Patch";
                                            else billedType="Billed";


                                            var shiftStart_new = new Date(full.productionDate+ " "+full.deets.timeStart).toLocaleString('en-US', { hour: 'numeric', minute:'numeric', hour12: true });
                                            var shiftEnd_new = new Date(full.productionDate+ " "+full.deets.timeEnd).toLocaleString('en-US', { hour: 'numeric', minute:'numeric',hour12: true });

                                            modalcode += '<p class="text-left">Hi {{$greeting}} ! ';
                                            modalcode += 'I would like to file a <strong>Pre-Shift OT </strong> for <strong>'+full.productionDate+' ['+full.productionDay+']</strong></p>';
                                            modalcode += '<div class="row">';

                                            modalcode +='<div class="col-sm-6" style="font-size: 12px"><h5 class="text-primary">Pre-shift OT Details:</h5>';
                                            modalcode +=' <p class="text-left"><strong>Start: </strong>'+full.deets.timeStart;
                                            modalcode +='<br/><strong>End : </strong>'+full.deets.timeEnd;
                                            modalcode += '<br/><strong>Billable Hours: </strong>'+full.deets.billable_hours;
                                            modalcode += '<br/><strong>Filed Hours worked: </strong>'+full.deets.filed_hours;
                                            modalcode += '<br/><strong>Type: </strong><span class="text-danger" style="font-size:larger">'+billedType;
                                            modalcode += '<span></p></div> <div class="col-sm-5" style="font-size: 12px"><h5 class="text-primary">Reason:</h5>';
                                            modalcode += '<p class="text-left"><em>'+full.deets.reason+'</em></p> </div>';



                              

                                                  //mc1 += '<div class="row"><div class="col-sm-12">'+full.deets.notes+'</div></div>';
                                            } break;

                                 //ML
                                case 16: { 

                                          var leaveStart = moment(full.deets.leaveStart); //,"MM/D/YYYY h:m A");
                                          var leaveEnd = moment(full.deets.leaveEnd); //,"MM/D/YYYY h:m A");
                                          var duration = moment.duration(leaveEnd.diff(leaveStart));
                                          var hours = duration.asHours();
                                          

                                          if (full.deets.totalCredits % 1 === 0) var totalcreds = Math.floor(full.deets.totalCredits);
                                          else{

                                            if(full.deets.totalCredits == '0.50') var totalcreds = "half";
                                              else var totalcreds = full.deets.totalCredits;
                                            }

                                          modalcode += '<p class="text-left">Hi {{$greeting}} ! <br/>';
                                          modalcode += 'I would like to file a <strong class="text-danger">'+totalcreds+'-day </strong><strong>MATERNITY LEAVE &nbsp;&nbsp;</strong>';
                                         

                                          if (full.deets.attachments != null)
                                          modalcode += '<span class="pull-right" style="font-size:smaller"><i class="fa fa-paperclip"></i> <a href="./user_fl/requirements/'+full.deets.id+'" target="_blank">Requirements <br/> &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp;attached</a></span> ';

                                          modalcode += '<br/><br/><strong>Reason: </strong><em>'+full.deets.notes+'</em></p>';
                                          modalcode += '<div class="row"><div class="col-sm-12"> <div class="row">';
                                          modalcode += '<div class="col-sm-6"><h5 class="text-primary">From: </h5></div><div class="col-sm-6"><h5 class="text-primary">Until: </h5></div>';

                                               


                                                  mc1 += '<div class="col-sm-6" style="font-size: 12px">';

                                                  
                                                  mc1 += '<p><strong>'+leaveStart.format("MMM DD, ddd hh:mm A")+' </strong></p>';

                                                  mc1 += '</div><div class="col-sm-6" style="font-size: 12px">';
                                                  mc1 += '<p><strong>'+leaveEnd.format("MMM DD, ddd hh:mm A")+'</strong></p></div>';

                                                  //mc1 += '<div class="row"><div class="col-sm-12">'+full.deets.notes+'</div></div>';
                                            } break;

                                  //PL
                                case 17: { 

                                          var leaveStart = moment(full.deets.leaveStart); //,"MM/D/YYYY h:m A");
                                          var leaveEnd = moment(full.deets.leaveEnd); //,"MM/D/YYYY h:m A");
                                          var duration = moment.duration(leaveEnd.diff(leaveStart));
                                          var hours = duration.asHours();
                                          

                                          if (full.deets.totalCredits % 1 === 0) var totalcreds = Math.floor(full.deets.totalCredits);
                                          else{

                                            if(full.deets.totalCredits == '0.50') var totalcreds = "half";
                                              else var totalcreds = full.deets.totalCredits;
                                            }

                                          modalcode += '<p class="text-left">Hi {{$greeting}} ! <br/>';
                                          modalcode += 'I would like to file a <strong class="text-danger">'+totalcreds+'-day </strong><strong>PATERNITY LEAVE &nbsp;&nbsp;</strong>';
                                         

                                          if (full.deets.attachments != null)
                                          modalcode += '<span class="pull-right" style="font-size:smaller"><i class="fa fa-paperclip"></i> <a href="./user_fl/requirements/'+full.deets.id+'" target="_blank">Requirements <br/> &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp;attached</a></span> ';

                                          modalcode += '<br/><br/><strong>Reason: </strong><em>'+full.deets.notes+'</em></p>';
                                          modalcode += '<div class="row"><div class="col-sm-12"> <div class="row">';
                                          modalcode += '<div class="col-sm-6"><h5 class="text-primary">From: </h5></div><div class="col-sm-6"><h5 class="text-primary">Until: </h5></div>';

                                               


                                                  mc1 += '<div class="col-sm-6" style="font-size: 12px">';

                                                  
                                                  mc1 += '<p><strong>'+leaveStart.format("MMM DD, ddd hh:mm A")+' </strong></p>';

                                                  mc1 += '</div><div class="col-sm-6" style="font-size: 12px">';
                                                  mc1 += '<p><strong>'+leaveEnd.format("MMM DD, ddd hh:mm A")+'</strong></p></div>';

                                                  //mc1 += '<div class="row"><div class="col-sm-12">'+full.deets.notes+'</div></div>';
                                            } break;

                                 //SPL
                                case 18: { 

                                          var leaveStart = moment(full.deets.leaveStart); //,"MM/D/YYYY h:m A");
                                          var leaveEnd = moment(full.deets.leaveEnd); //,"MM/D/YYYY h:m A");
                                          var duration = moment.duration(leaveEnd.diff(leaveStart));
                                          var hours = duration.asHours();
                                          

                                          if (full.deets.totalCredits % 1 === 0) var totalcreds = Math.floor(full.deets.totalCredits);
                                          else{

                                            if(full.deets.totalCredits == '0.50') var totalcreds = "half";
                                              else var totalcreds = full.deets.totalCredits;
                                            }

                                          modalcode += '<p class="text-left">Hi {{$greeting}} ! <br/>';
                                          modalcode += 'I would like to file a <strong class="text-danger">'+totalcreds+'-day </strong><strong>SINGLE-PARENT LEAVE &nbsp;&nbsp;</strong>';
                                         

                                          if (full.deets.attachments != null)
                                          modalcode += '<span class="pull-right" style="font-size:smaller"><i class="fa fa-paperclip"></i> <a href="./user_fl/requirements/'+full.deets.id+'" target="_blank">Requirements <br/> &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp;attached</a></span> ';

                                          modalcode += '<br/><br/><strong>Reason: </strong><em>'+full.deets.notes+'</em></p>';
                                          modalcode += '<div class="row"><div class="col-sm-12"> <div class="row">';
                                          modalcode += '<div class="col-sm-6"><h5 class="text-primary">From: </h5></div><div class="col-sm-6"><h5 class="text-primary">Until: </h5></div>';

                                               


                                                  mc1 += '<div class="col-sm-6" style="font-size: 12px">';

                                                  
                                                  mc1 += '<p><strong>'+leaveStart.format("MMM DD, ddd hh:mm A")+' </strong></p>';

                                                  mc1 += '</div><div class="col-sm-6" style="font-size: 12px">';
                                                  mc1 += '<p><strong>'+leaveEnd.format("MMM DD, ddd hh:mm A")+'</strong></p></div>';

                                                  //mc1 += '<div class="row"><div class="col-sm-12">'+full.deets.notes+'</div></div>';
                                            } break;


                              }

                              //modalcode += '</div><div class="row"><div class="col-sm-4"> '+full.productionDate+'<br/> ['+full.productionDay+'] </div>';
                              modalcode += mc1;
                              modalcode += '<div class="col-sm-3"> </div></div></div></div>    </div>';

                              modalcode += '        </div>';
          modalcode += '        <!-- /.direct-chat-text -->';
          modalcode += '      </div>';
          modalcode += '      <!-- /.direct-chat-msg -->';

          if(full.typeID != 14) //do this only if NON UNLOCK DTR request
          {
              modalcode += '      <!-- Message to the right -->';
              modalcode += '      <div class="direct-chat-msg right" style="margin-top:50px">';
              modalcode += '        <div class="direct-chat-default clearfix">';
              modalcode += '          <span class="direct-chat-name pull-right"></span>';
              modalcode += '          <span class="direct-chat-timestamp pull-left"> </span>';
              modalcode += '        </div>';
              modalcode += '        <!-- /.direct-chat-info -->';
              modalcode += '        <img class="direct-chat-img" src="'+userimg+'" alt="Message User Image"><!-- /.direct-chat-img -->';
              modalcode += '        <div class="direct-chat-text direct-chat-default" style="background-color:#d2d6de" >'; //style="background-color:#fff;border-color:#ddd"

              modalcode += '<a href="#" class="process btn btn-flat btn-sm pull-right btn-danger" data-notifType="'+full.typeID+'" data-action="0" data-notifID="'+full.id+'" data-id="'+full.deets.id+'" data-dismiss="modal"style="margin-right:5px;" > <i class="fa fa-thumbs-down" ></i> Deny </a><a href="#" class="process btn btn-flat btn-success btn-sm pull-right " data-notifType="'+full.typeID+'" data-action="1" data-notifID="'+data+'" data-id="'+full.deets.id+'" data-dismiss="modal"style="margin-right:5px;" > <i class="fa fa-thumbs-up" ></i> Approve </a><div class="clearfix"></div>';
              
              modalcode += '        </div>';
              modalcode += '        <!-- /.direct-chat-text -->';
              modalcode += '      </div>';
              modalcode += '      <!-- /.direct-chat-msg -->';
              modalcode += '    </div>';
              modalcode += '    <!--/.direct-chat-messages-->';
          }


          modalcode += '  </div>';
          modalcode += '  <!-- /.box-body -->';
          
          modalcode += '</div>';
          modalcode += '<!--/.direct-chat -->';

                              modalcode +=' <div class="modal-footer no-border">';

                              modalcode +='</div></div></div></div>'+ delModal;


                              //********* UNLOCK DTR has a different set of action buttons eh
                              if(full.typeID == 14)
                                
                                return '<a href="./seen-unlockRequest/'+full.notification_id+'?seen=true" class="btn btn-flat btn-xs text-primary"><i class="fa fa-calendar"></i> Open DTR Sheet</a>'+modalcode;
                              else
                              return '<a data-notifType="'+full.typeID+'" data-action="1" data-notifID="'+full.id+'" data-id="'+full.deets.id+'" href="#" class="process btn btn-flat btn-xs text-success"><i class="fa fa-thumbs-up"></i> Approve</a><a data-notifType="'+full.typeID+'" data-action="0" data-notifID="'+full.id+'" data-id="'+full.deets.id+'" href="#" class="process btn btn-flat btn-xs text-danger"><i class="fa fa-thumbs-down"></i> Deny</a><a data-toggle="modal" data-target="#myModal'+full.id+'"  href="#" class="btn btn-flat btn-xs text-default"><i class="fa fa-trash"></i> Delete</a>'+modalcode;}}
                            

                        ],
                       

                      "responsive":true,
                     
                      // '<a data-notifType="'+typeID+'" data-action="1" data-notifID="'+id+'" data-id="'+deets.id+'" href="#" class="process btn btn-flat btn-xs text-success"><i class="fa fa-thumbs-up"></i> Approve</a><a data-notifType="'+typeID+'" data-action="0" data-notifID="'+id+'" data-id="'+deets.id+'" href="#" class="btn btn-flat btn-xs text-danger"><i class="fa fa-thumbs-down"></i> Deny</a><a data-toggle="modal" data-target="#myModal'+id+'"  href="#" class="btn btn-flat btn-xs text-default"><i class="fa fa-trash"></i> Delete</a>'
                      

                
        });

   $('.table-responsive #requests').on('click', '.process', function(e)
    {
      e.preventDefault(); e.stopPropagation();
      var _token = "{{ csrf_token() }}";
      var processAction = $(this).attr('data-action');
      var id = $(this).attr('data-id');
      var notif = $(this).attr('data-notifID');
      var notifType = $(this).attr('data-notifType');
      console.log("process: " + processAction);
      console.log("ID: " + id);

      //--- update user notification first
      $.ajax({
          url: "{{action('UserNotificationController@process')}}",
          type:'POST',
          data:{ 
            'id': notif,
            '_token':_token
          },

         
          success: function(response2)
          {
            switch(notifType){
                    case '6': { //cws
                                  $.ajax({
                                            url: "{{action('UserCWSController@process')}}",
                                            type:'POST',
                                            data:{ 

                                              'id': id,
                                              'isApproved': processAction,
                                              '_token':_token
                                            },

                                           
                                            success: function(res)
                                            {
                                              console.log(res);
                                              $('#myModal_DTRP'+notif).modal('hide');

                                              if (processAction == '1')
                                               $.notify("Submitted CWS request by "+res.firstname+" "+res.lastname+ " : Approved.",{className:"success",globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );
                                             else
                                               $.notify("Submitted CWS request by "+res.firstname+" "+res.lastname+ " :  Denied.",{className:"error",globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );
                                              //window.location = "{{action('HomeController@index')}}";
                                               
                                            }, error: function(res){
                                              console.log("ERROR");
                                            }


                                  });

                              }break; 

                    case '7': {//ot
                                  console.log("pasok sa case");
                                  $.ajax({
                                            url: "{{action('UserOTController@process')}}",
                                            type:'POST',
                                            data:{ 

                                              'id': id,
                                              'isApproved': processAction,
                                              '_token':_token
                                            },

                                           
                                            success: function(res)
                                            {
                                              if (res.success == '0') console.log("nag error");
                                              else console.log("success first ajax");

                                              console.log(res);

                                              $('#myModal_DTRP'+notif).modal('hide');
                                              if (processAction == '1')
                                               $.notify("Submitted OT Request by "+res.firstname+" "+res.lastname+ " : Approved.",{className:"success",globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );
                                             else
                                               $.notify("Submitted OT Request by "+res.firstname+" "+res.lastname+ " :  Denied.",{className:"error",globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );
                                              //window.location = "{{action('HomeController@index')}}";
                                                 
                                               
                                            },
                                            error: function(res)
                                            {
                                              console.log("may error eh");
                                              console.log(res);
                                            }
                                  });
                              }break; 

                    case '8': {//in
                                  $.ajax({
                                            url: "{{action('UserDTRPController@process')}}",
                                            type:'POST',
                                            data:{ 

                                              'id': id,
                                              'isApproved': processAction,
                                              '_token':_token
                                            },

                                           
                                            success: function(res)
                                            {
                                              console.log(res);
                                              $('#myModal_DTRP'+notif).modal('hide');

                                              if (processAction == '1')
                                               $.notify("Submitted DTRP - IN by "+res.firstname+" "+res.lastname+ " : Approved.",{className:"success",globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );
                                             else
                                               $.notify("Submitted DTRP - IN by "+res.firstname+" "+res.lastname+ " :  Denied.",{className:"error",globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );
                                             

                                               
                                            },
                                            error: function(res)
                                            {
                                              console.log("may error eh");
                                              console.log(res);
                                            }
                                  });

                              }break; 

                    case '9': { //out
                                $.ajax({
                                            url: "{{action('UserDTRPController@process')}}",
                                            type:'POST',
                                            data:{ 

                                              'id': id,
                                              'isApproved': processAction,
                                              '_token':_token
                                            },

                                           
                                            success: function(res)
                                            {
                                              console.log(res);
                                              $('#myModal_DTRP'+notif).modal('hide');

                                              if (processAction == '1')
                                               $.notify("Submitted DTRP - OUT by "+res.firstname+" "+res.lastname+ " : Approved.",{className:"success",globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );
                                             else
                                               $.notify("Submitted DTRP - OUT by "+res.firstname+" "+res.lastname+ " :  Denied.",{className:"error",globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );
                                             
                                             // window.location = "{{action('HomeController@index')}}";
                                               
                                            }
                                  });

                              }break;

                    case '10': { //VL
                                  $.ajax({
                                            url: "{{action('UserVLController@process')}}",
                                            type:'POST',
                                            data:{ 

                                              'id': id,
                                              'isApproved': processAction,
                                              '_token':_token
                                            },

                                           
                                            success: function(res)
                                            {
                                              console.log(res);
                                              $('#myModal_DTRP'+notif).modal('hide');

                                              if (processAction == '1')
                                               $.notify("Submitted VL request by "+res.firstname+" "+res.lastname+ " : Approved.",{className:"success",globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );
                                             else
                                               $.notify("Submitted VL request by "+res.firstname+" "+res.lastname+ " :  Denied.",{className:"error",globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );
                                              //window.location = "{{action('HomeController@index')}}";
                                               
                                            }, error: function(res){
                                              console.log("ERROR");
                                              console.log(res);
                                            }


                                  });

                              }break;


                    case '11': { //SL
                                  $.ajax({
                                            url: "{{action('UserSLController@process')}}",
                                            type:'POST',
                                            data:{ 

                                              'id': id,
                                              'isApproved': processAction,
                                              '_token':_token
                                            },

                                           
                                            success: function(res)
                                            {
                                              console.log(res);
                                              $('#myModal_DTRP'+notif).modal('hide');

                                              if (processAction == '1')
                                               $.notify("Submitted SL request by "+res.firstname+" "+res.lastname+ " : Approved.",{className:"success",globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );
                                             else
                                               $.notify("Submitted SL request by "+res.firstname+" "+res.lastname+ " :  Denied.",{className:"error",globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );
                                              //window.location = "{{action('HomeController@index')}}";
                                               
                                            }, error: function(res){
                                              console.log("ERROR");
                                              console.log(res);
                                            }


                                  });

                              }break;

                    case '12': { //LWOP
                                  $.ajax({
                                            url: "{{action('UserLWOPController@process')}}",
                                            type:'POST',
                                            data:{ 

                                              'id': id,
                                              'isApproved': processAction,
                                              '_token':_token
                                            },

                                           
                                            success: function(res)
                                            {
                                              console.log(res);
                                              $('#myModal_DTRP'+notif).modal('hide');

                                              if (processAction == '1')
                                               $.notify("Submitted LWOP request by "+res.firstname+" "+res.lastname+ " : Approved.",{className:"success",globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );
                                             else
                                               $.notify("Submitted LWOP request by "+res.firstname+" "+res.lastname+ " :  Denied.",{className:"error",globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );
                                              //window.location = "{{action('HomeController@index')}}";
                                               
                                            }, error: function(res){
                                              console.log("ERROR");
                                              console.log(res);
                                            }


                                  });

                              }break; 


                    case '13': { //OBT
                                  $.ajax({
                                            url: "{{action('UserOBTController@process')}}",
                                            type:'POST',
                                            data:{ 

                                              'id': id,
                                              'isApproved': processAction,
                                              '_token':_token
                                            },

                                           
                                            success: function(res)
                                            {
                                              console.log(res);
                                              $('#myModal_DTRP'+notif).modal('hide');

                                              if (processAction == '1')
                                               $.notify("Submitted OBT request by "+res.firstname+" "+res.lastname+ " : Approved.",{className:"success",globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );
                                             else
                                               $.notify("Submitted OBT request by "+res.firstname+" "+res.lastname+ " :  Denied.",{className:"error",globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );
                                              //window.location = "{{action('HomeController@index')}}";
                                               
                                            }, error: function(res){
                                              console.log("ERROR");
                                              console.log(res);
                                            }


                                  });

                              }break;  

                    case '15': { //PS OT
                                  $.ajax({
                                            url: "{{action('UserOTController@process')}}",
                                            type:'POST',
                                            data:{ 

                                              'id': id,
                                              'isApproved': processAction,
                                              '_token':_token
                                            },

                                           
                                            success: function(res)
                                            {
                                              console.log(res);
                                              $('#myModal_DTRP'+notif).modal('hide');

                                              if (processAction == '1')
                                               $.notify("Submitted Pre-Shift OT request by "+res.firstname+" "+res.lastname+ " : Approved.",{className:"success",globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );
                                             else
                                               $.notify("Submitted Pre-Shift OT request by "+res.firstname+" "+res.lastname+ " :  Denied.",{className:"error",globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );
                                              //window.location = "{{action('HomeController@index')}}";
                                               
                                            }, error: function(res){
                                              console.log("ERROR");
                                              console.log(res);
                                            }


                                  });

                              }break;

                    case '16': { //ML
                                  $.ajax({
                                            url: "{{action('UserFamilyleaveController@process')}}",
                                            type:'POST',
                                            data:{ 

                                              'id': id,
                                              'isApproved': processAction,
                                              '_token':_token
                                            },

                                           
                                            success: function(res)
                                            {
                                              console.log(res);
                                              $('#myModal_DTRP'+notif).modal('hide');

                                              if (processAction == '1')
                                               $.notify("Submitted ML request by "+res.firstname+" "+res.lastname+ " : Approved.",{className:"success",globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );
                                             else
                                               $.notify("Submitted ML request by "+res.firstname+" "+res.lastname+ " :  Denied.",{className:"error",globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );
                                              //window.location = "{{action('HomeController@index')}}";
                                               
                                            }, error: function(res){
                                              console.log("ERROR");
                                              console.log(res);
                                            }


                                  });

                              }break;

                    case '17': { //PL
                                  $.ajax({
                                            url: "{{action('UserFamilyleaveController@process')}}",
                                            type:'POST',
                                            data:{ 

                                              'id': id,
                                              'isApproved': processAction,
                                              '_token':_token
                                            },

                                           
                                            success: function(res)
                                            {
                                              console.log(res);
                                              $('#myModal_DTRP'+notif).modal('hide');

                                              if (processAction == '1')
                                               $.notify("Submitted PL request by "+res.firstname+" "+res.lastname+ " : Approved.",{className:"success",globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );
                                             else
                                               $.notify("Submitted PL request by "+res.firstname+" "+res.lastname+ " :  Denied.",{className:"error",globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );
                                              //window.location = "{{action('HomeController@index')}}";
                                               
                                            }, error: function(res){
                                              console.log("ERROR");
                                              console.log(res);
                                            }


                                  });

                              }break;

                    case '18': { //SSPL
                                  $.ajax({
                                            url: "{{action('UserFamilyleaveController@process')}}",
                                            type:'POST',
                                            data:{ 

                                              'id': id,
                                              'isApproved': processAction,
                                              '_token':_token
                                            },

                                           
                                            success: function(res)
                                            {
                                              console.log(res);
                                              $('#myModal_DTRP'+notif).modal('hide');

                                              if (processAction == '1')
                                               $.notify("Submitted SPL request by "+res.firstname+" "+res.lastname+ " : Approved.",{className:"success",globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );
                                             else
                                               $.notify("Submitted SPL request by "+res.firstname+" "+res.lastname+ " :  Denied.",{className:"error",globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );
                                              //window.location = "{{action('HomeController@index')}}";
                                               
                                            }, error: function(res){
                                              console.log("ERROR");
                                              console.log(res);
                                            }


                                  });

                              }break;
                  }
            
            getNewNotifications();

             
          }
      });

       





          
                 

    });


   var getNewNotifications = function (datatable) {
    
    $('.modal').modal('hide');


    $.getJSON("{{action('UserNotificationController@getApprovalNotifications', Auth::user()->id)}}", function (response,datatable) 
    {
      //console.log(response);
      console.log("----------");
      var dt = $("#requests").DataTable();
      dt.ajax.reload();
      console.log(response);
      $('#approvalcount').html(response.recordsTotal);
     
    });
    };

    $('#refresh').on('click', function(e, datatable){
       $.getJSON("{{action('UserNotificationController@getApprovalNotifications', Auth::user()->id)}}", function (response,datatable) 
        {
          //console.log(response);
          console.log("---------");
          var dt = $("#requests").DataTable();
          dt.ajax.reload();
          $('#approvalcount').html(response.recordsTotal);
        });
    });

    setInterval(getNewNotifications, 90000); // Ask for new notifications every 1.5min
   
    
      
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
<!-- end Page script 

<script type="text/javascript" src="{{asset('public/js/dashboard.js')}}"></script>-->

@stop