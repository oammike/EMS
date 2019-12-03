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



                 <!-- ******** THE DATATABLE ********** -->
          <div class="row">


            <div class="col-lg-7 col-sm-6 col-xs-12">


              @if(count($groupedForm)>0 && !$reportsTeam )
                <!-- ************* POSTMATE WIDGET CHART ************ -->

                     @if($fromGuideline)
                      @include('layouts.widget-Guideline')
                     @endif

                     @if($fromPostmate)
                     @include('layouts.widget-Postmates')
                     @endif
            @endif




 <!--VIDEOS -->
               <div class="box box-info" style="background: rgba(256, 256, 256, 0.6)">
                    <div class="box-header with-border">
                      <h3 class="box-title">Videos  <a class="btn btn-xs btn-default" href="{{action('HomeController@videogallery')}}"><i class="fa fa-video-camera"></i> Watch All</a></h3>
                      <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                        <!-- <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button> -->
                      </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                      <p class="text-center"><strong class="text-primary">Our Company <span class="text-orange">Mission, Vision, and Core Values </span> </strong></p>
                     <video id="teaser" src="storage/uploads/MVC.mp4" width="100%" loop controls></video>
                       
                      
                    </div>
                </div>



             

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


             
             <!-- ************* PERFORMANCE CHART ************ -->
             @include('layouts.charts')
            </div>
             
            

              

              <div class="col-lg-5 col-sm-6 col-xs-12">

               

                <?php /*
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
                <!-- /.end SHOUT OUT -->

               
                  
                

                
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
 1st place – Php 2,500.00 <br/> </p> 
                              
                              
                    </div>
                  </div>


                <!--VIDEOS -->
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
                      <p class="text-center"><strong class="text-orange" style="font-size: 1.8em">Join our <span class="text-primary"> #HobbyMonth Activity!</span></strong></p>
                      <video id="teaser" src="storage/uploads/hobbymonth.webm" width="100%" loop controls></video>

                     

                                <p style="font-size: 0.8em;" align="center">Upload a photo or two showing your favorite hobbies and include a short description telling us why you love doing it or how you came to love doing it - we're all ears! <br/><br/>
                                  Don't forget to include our official hashtags as seen in this post and tag @OpenAccessBPO. Also, comment DONE on our IG post so we can easily track your posts and TAG your teammates so we can learn a little bit more about them, too. On February 1st, we'll choose 5 employees (via lottery) who'll get the chance to be featured in our pagfe and take part in one of the exciting things coming our way this year! <br/><br/>

                                #WeSpeakYourLanguage #OAonHobbyMonth #HobbyMonth</p> 
                              
                              
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
                      <p style="font-size: smaller;" class="text-center">Watch <strong class="text-primary">Open Access BPO</strong> <strong>Back To The '90s</strong> Year End Party <br/> <strong class="text-orange" style="font-size: 1.8em">Same-Day Edit <span class="text-primary"> video</span>  </strong> 
                               </p>

                                <p align="center">Photos uploaded in our <a href="{{ action('HomeController@gallery',['a'=>1]) }}"><i class="fa fa-picture-o"></i> 2018 Year End Party Album</a> 
                                <a style="font-size: smaller;" href="https://www.instagram.com/explore/tags/BackToThe90s/">#WeSpeakYourLanguage #OABackToThe90s #The2018YEP</a></p> 
                              
                              <video id="teaser" src="storage/uploads/sde-back_to_the_90s.webm" width="100%" loop controls></video>
                    </div>
                  </div>
                  */ ?>


               


              </div>



            
           
             
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
   var vid = document.getElementById("teaser");
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
    };
     /* ---- VIDEO PLAYER -------- */

   /*----------------- MEMO ----------------*/

    $(window).bind("load", function() {

       startTime();

       // ********* temporarily disable memo EES ************** 
       // @if (!is_null($memo) && $notedMemo != true)
       // $('#memo'+{{$memo->id}}).modal({backdrop: 'static', keyboard: false, show: true});
       // @endif
       // ********* temporarily disable memo EES ************** 

      // * if(!is_null($siteTour) && $notedTour != true)
     
      //  introJs().setOption('doneLabel', "Got it. Don't show this to me again").start().oncomplete(function(){
      //   $('#controlsidebar').addClass('control-sidebar-open');
      //   var _token = "{{ csrf_token() }}";
          

      //     //--- update user notification first
      //     $.ajax({
      //         url: "{{action('UserMemoController@saveUserMemo')}}",
      //         type:'POST',
      //         data:{ 
      //           'id': "{{$siteTour->id}}",
      //           '_token':_token
      //         },

      //         success: function(res){
      //                 console.log(res);
      //         },
      //       });

      //   console.log("open it");
      //  });
      //  * endif


  });
      
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

   

   /*------------- TIMEKEEPING --------------*/
   //QUORA
   // MOUS
   // AVA
   // WORLDVENTURES
   // OPS


   function startTime() {
    
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
                                $.notify("End Breaktime. \n\nLet's get back to work.",{className:"success",globalPosition:'left top',autoHideDelay:7000, clickToHide:true} );
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

    /*---------- GUIDELINE WIDGET ----------- */
   @if(count($groupedForm)>0 && $fromGuideline)

      $('select.formItem').on('change',function(){
        var itemName = $(this)[0]['name'];
        var formID = $(this).attr('data-formID');
        var selectedItem = $('select[name='+itemName+'] :selected').val();
        var itemOrder = $(this)[0]['tabIndex'];
        var s = "."+selectedItem.toLowerCase()+"_"+itemName;

        console.log('itemName:' + itemName);
        console.log('formID: '+ formID);
        console.log('selectedItem: ' + selectedItem);
        console.log('itemOrder: '+ itemOrder);
        console.log('s: '+ s);

        if (selectedItem == 'xx'){
          $('#addPayroll_'+formID).html('<input required type="text" id="newPayroll_'+formID+'" placeholder="add new Payroll Provider" class="form-control" />');
        }else $('#addPayroll_'+formID).html('');
      });

      $('.submit').on('click',function(e){
          e.preventDefault();
          var formID = $(this).attr('data-formid');

          var newPayroll = $('#newPayroll_'+formID).val();

          if (newPayroll == ''){ $('#newPayroll_'+formID).css('border',"solid 3px #e24527");return false;}
          else
          {
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
              var formItems_radio = $('input.radio-group').filter(':checked');

              console.log("Radio");
              console.log(formItems_radio);
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

              formItems_radio.each(function(){
                 var n = $(this);
                 formItems[ ctr+'_'+n[0]['id'] ] = $(this).val();
                 ctr++;
              });
              console.log(formItems);

              //formItems['newPayroll'] = newPayroll;
              $.ajax({
                            url: "{{action('FormSubmissionsController@process')}}",
                            type:'POST',
                            data:{ 
                              'formItems': formItems,
                              'newPayroll': newPayroll,
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
       

          }
          



          
  
    
      });


   

   @endif
  /*---------- END GUIDELINE WIDGET ----------- */


   /*---------- POSTMATES WIDGET ----------- */
   @if(count($groupedForm)>0 && $fromPostmate)

       $('#playbook').on('click',function(){

          $.ajax({
                  url: "{{action('HomeController@logAction','P')}}",
                  type: "GET",
                  data: {'action': 'P'},
                  success: function(response){
                            console.log(response);

                }

          });

       });

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

        if (selectedItem.toLowerCase() == "yes" || selectedItem.toLowerCase() == "no")
        {
          $('select_'+(itemOrder+1), 'select_'+itemOrder).hide();
          var s = ".confirmed_"+itemName;
          
          console.log('value of .confirmed_itemName : ');
          console.log(itemName);
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
          } else {
            $(s).fadeIn();
            var newItem = $(this).parent();
            console.log("parent else: ");
            
            console.log(newItem);
          }

          console.log('val of x :');
          console.log(x);
          console.log('val of s :');
          console.log(s);

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
                console.log("V is:"); console.log(v);
                if (v == "" || v==0){
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
                                        data: {'action': '3', 'formid': res.formid, 'usersubmit':res.usersubmit},
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





@include('layouts.charts-scripts')

     

      // Fix for charts under tabs
      $('.box ul.nav a').on('shown.bs.tab', function () {
        area.redraw();
        
       
      });

//*************** END CHARTS



   $('#myCarousel').carousel();

 


   var getNewNotifications = function (datatable) {
    
    $('.modal').modal('hide');

    $.getJSON("{{action('UserNotificationController@getApprovalNotifications', Auth::user()->id)}}", function (response,datatable) 
    {
      //console.log(response);
      console.log("----------");
      var dt = $("#requests").DataTable();
      dt.ajax.reload();
     
    });
    };

    $('#refresh').on('click', function(e, datatable){
       $.getJSON("{{action('UserNotificationController@getApprovalNotifications', Auth::user()->id)}}", function (response,datatable) 
        {
          //console.log(response);
          console.log("---------");
          var dt = $("#requests").DataTable();
          dt.ajax.reload();
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