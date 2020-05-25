<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  
  <title>Health Declaration Form</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  
  <link rel="icon" href="{{asset('public/img/oam_favicon1-55027f4ev1_site_icon-32x32.png')}}" sizes="32x32"/> 
  <link rel="icon" href="{{asset('public/img/oam_favicon1-55027f4ev1_site_icon-256x256.png')}}" sizes="192x192"/> 
  <link rel="apple-touch-icon-precomposed" href="{{asset('public/img/oam_favicon1-55027f4ev1_site_icon-256x256.png')}}"/> 
  <meta name="msapplication-TileImage" content="{{asset('public/img/oam_favicon1-55027f4ev1_site_icon.png')}}"/>

  <meta name="csrf-token" content="{{ csrf_token() }}">
  

 <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
 <link href="{{asset('public'.elixir('css/all.css'))}}" rel="stylesheet" />
 <link href="{{asset('public/css/font-awesome.min.css')}}" rel="stylesheet">
 <!-- Add IntroJs styles -->
<link href="{{asset('public/css/introjs.css')}}" rel="stylesheet">
 <style type="text/css"> 
 .navbar{ -webkit-box-shadow:6px 6px 13px 0px rgba(0, 0, 0, 0.19); }
 .skin-blue .main-header .navbar, .skin-blue .main-header .logo{background-color: #1a8fcb;}
 .content-wrapper{

   background:url("{{ asset('public/img/bg_swish.png')}}") bottom right fixed no-repeat;
   background-color: #fdfdfd; /*#f5f9fc;*/

   }

   #modalMain{ background:url("{{ asset('public/img/big_transparent.png')}}") bottom right no-repeat #fff }
   .main-footer {background-color: #0678b2; color:#fff;}
   .main-footer a{color:#fff;} </style>
  <!-- Font Awesome -->
  <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css"> -->

  <!-- Ionicons -->
 <!--  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css"> -->
  <!-- Theme style -->
  
  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

</head>

<body class="content-wrapper" style="padding-left: 0px; margin-left: 0px">
     <section class="content">
     

     
      <div class="row">
        <div class="col-lg-2 text-center"> <img src="storage/uploads/logo_2020_170.png" /></div>
        <div class="col-lg-8">
         
          <h1 class="text-center" >Health Declaration Form</h1>
          
          <div class="clearfix"></div>
          <hr />

          <label class="pull-right">Date: {{ $today->format('l M d, Y') }}</label>
          <label>Name: </label> <input disabled="disabled" class="form-control" type="text" style="width:50%;" name="employee" value="{{$user->lastname}},{{$user->firstname}} ">

          @foreach($questions as $question)
          <?php $n = $question->ordering+1; ?>

           

              @if($question->ordering == 1)
               <div class="qs" id="q{{$question->ordering}}"> 
                <br/><br/>
                <h4>{{$question->question}} </h4>
                  <h1 class="pull-right">
                    <label class="text-danger"><input type="radio" name="ans{{$question->ordering}}" value="1"  data-next="q{{$n}}" data-qid="{{$question->id}}"  /> Yes</label> &nbsp;&nbsp; <label class="text-success"><input type="radio" name="ans{{$question->ordering}}" value="0"  data-qid="{{$question->id}}" data-next="q{{$n}}" /> No</label>
                  </h1>
                  <ul class="pull-left">
                    @foreach($symptoms as $s)
                    <li><label><input type="checkbox" disabled="disabled" name="symptoms" value="{{$s->id}}" /> {{$s->name}} </label></li>
                    @endforeach
                  </ul>
                  <div class="clearfix"></div>
                  <a style="display: none;" data-next="q{{$n}}"  data-qid="{{$question->id}}" class="btn btn-default btn-md pull-left">Next &raquo;</a>

              @elseif($question->ordering == 2)
              <div class="qs" id="q{{$question->ordering}}" style="display: none"> 
                <br/><br/>
                <h4>{{$question->question}} </h4>
                <h5> <i class="fa fa-exclamation-circle"></i> There is Close Contact with a confirmed covid-19 patient,  <br/>1) if you had face-to-face contact within 1-meter and for more than 15 minutes; or <br/>2) if you had direct physical contact."</h5>

                <h1 class="text-center">
                  <label class="text-danger"><input type="radio" name="ans{{$question->ordering}}" data-qid="{{$question->id}}" value="1"  data-next="q{{$n}}"  /> Yes</label> &nbsp;&nbsp; <label class="text-success"><input type="radio" name="ans{{$question->ordering}}"  data-qid="{{$question->id}}" value="0"  data-next="q{{$n}}" /> No</label>
                </h1>

              @elseif($question->ordering == 5)
              <div class="qs" id="q{{$question->ordering}}" style="display: none"> 
                <br/><br/>
                <h4>{{$question->question}} </h4>
                  <ul>
                    @foreach($diagnosis as $s)
                    <li><label class="diagnose"><input type="checkbox"  name="diagnosis" value="{{$s->id}}" /> {{$s->name}} </label></li>
                    @endforeach
                    <li><label class="text-success"><input id="none" type="checkbox"  name="diagnosis" value="0"  data-next="q{{$n}}" /> NONE OF THE ABOVE</label></li>

                  </ul>

                  <a style="display: none;" data-next="q{{$n}}"  data-qid="{{$question->id}}" class="btn btn-default btn-md pull-left">Next &raquo;</a>
              @else

              <div class="qs" id="q{{$question->ordering}}" style="display: none"> 
                <br/><br/>
                <h4>{{$question->question}} </h4>

                <h1 class="text-center">
                  <label class="text-danger"><input type="radio" name="ans{{$question->ordering}}" data-qid="{{$question->id}}" value="1"  data-next="q{{$n}}"  /> Yes</label> &nbsp;&nbsp; <label class="text-success"><input type="radio" name="ans{{$question->ordering}}"  data-qid="{{$question->id}}" value="0"  data-next="q{{$n}}" /> No</label>
                </h1>

              @endif
            </div>

          @endforeach


        
          




          <div class="qs" id="q6" style="display: none"> 
            <br/><br/>
            <h4>Declaration<br/></h4>
            <p><i class="text-success fa fa-check fa-2x"></i> The information I have given is true, correct, and complete. <br/><br/>
            <i class="text-success fa fa-check fa-2x"></i> I acknowledge that completing this Health Declaration Form is required by DTI-DOLE in relation to reducing the risk of COVID-19 in the workplace and the information provided herein may be used and/or shared by the Company as  required.  I understand that I am required by RA 11469, Bayanihan to Heal as One Act, to provide truthful information. </p>

            <a id="submit" class="btn btn-success"> Acknowledge &amp; Submit Form </a>


            
            
          </div>

          
          


         
          
        </div>
        <div class="col-lg-2">
          <p class="pull-right" style="width:200px;margin-top: 50px; color: #666;font-size: x-small;">
            11th flr. Glorietta 2 Corporate Center <br/>Makati City, Philippines 1224<br/>
            https://wwww.openaccessbpo.com<br/><br/>
            Employee Hotline: <strong>0917-896-0634</strong>
          </p>
        </div>

        

      </div><!-- end row -->





       
     </section>

     <!-- REQUIRED JS SCRIPTS -->
    <script type="text/javascript" src="{{asset('public'.elixir('js/all.js'))}}"></script>
    <script type="text/javascript" src="{{asset('public/js/notify.min.js')}}"></script>

     <script type="text/javascript">
       
       $(function () {
        'use strict';
        var sel_symptoms = [];
        var sel_diagnosis = [];
        var declarations = [];

        $('#submit').on('click',function(){

          var ans1 = declarations[0]['answer'];
          var ans2 = declarations[1]['answer'];

          
          console.log("ans1: "+ ans1);
          console.log("ans2: "+ ans2);
          console.log(declarations);

          var _token = "{{ csrf_token() }}";
    
          $.ajax({
              url: "{{action('HomeController@healthForm_process')}}",
              type:'POST',
              data:{ 
                'sel_symptoms': sel_symptoms,
                'sel_diagnosis': sel_diagnosis,
                'declarations' : declarations,
                '_token':_token
              },
              success: function(response){
                console.log('------');
                console.log(response);
                
                $('#submit').fadeOut();

                //check answers from 1 || 2
                if (ans1 == '1' || ans2 == '1')
                {
                  //$.notify("Thank you for filling out our health declaration form.\nYou may now proceed to your EMS Dashboard.",{className:"error",globalPosition:'right middle',autoHideDelay:3000, clickToHide:true} );
                  alert('\n\nSince you answered “YES” to questions 1 and/or 2, you are REQUIRED to: \n\nimmediately NOTIFY our Company Nurses and WAIT FOR NURSES’ ASSESSMENT AND ADVICE prior to going to AND/or REPORTING BACK TO the office.');
                   window.location.replace("{{action('HomeController@home')}}");
                  

                }else
                {
                  $.notify("Thank you for filling out our health declaration form.\nYou may now proceed to your EMS Dashboard.",{className:"success",globalPosition:'right middle',autoHideDelay:3000, clickToHide:true} );
                  window.location.replace("{{action('HomeController@home')}}");

                }

                
                //setTimeout(function(){ window.location = "{{action('HomeController@home')}}"; }, 3000);
                // setTimeout(function() {
                //   window.location.href = "{{action('HomeController@home')}}";
                // }, 3000);
              }
              
          });


            
        });

        $('a.btn').on('click',function(){
          var cbox = $(this).siblings("ul").children().find(':checked');
          var nxt = $(this).attr('data-next');
          var qid = $(this).attr('data-qid');
          

          if ((cbox.length >= 1)){
             $('.qs').fadeOut('fast');
             $('#'+nxt).show("slide", { direction: "right" }, 500);

             if(nxt == 'q2')
             {
                $.each(cbox, function(index, v){
                  sel_symptoms.push(v['value']);
                });

             }

             if(nxt == 'q6')
             {
                $.each(cbox, function(index, v){
                  sel_diagnosis.push(v['value']);
                });

                if(cbox.length == 1 && cbox[0]['value']=='0')
                {
                  var d = {'question':qid, 'answer':cbox[0]['value']};
                  declarations.push(d);

                }else{
                  var d = {'question':qid, 'answer':'1'};
                  declarations.push(d);

                }



                

             }
             

          }

        });

       

        $('#none, .diagnose').on('click',function(){
          
          var nxt = $(this).attr('data-next');

          if ($('#none').is(':checked')) 
          {
            var sibs = $('.diagnose');//.siblings();
            var s = sibs.find('input').attr('disabled',true);
            sibs.css('text-decoration','line-through');

            if (sibs.length > 0)
              $('#q5 a').fadeIn();
            else
              $('#q5 a').fadeOut();

            //$('.qs').fadeOut('fast');
            //$('#'+nxt).show("slide", { direction: "right" }, 500);

          }
          else {
            var sibs = $('.diagnose');//.siblings();
            var s = sibs.find('input').attr('disabled',false);
            sibs.css('text-decoration','none');
            console.log(sibs.length);
            if (sibs.find(':checked').length > 0)
              $('#q5 a').fadeIn();
            else
              $('#q5 a').fadeOut();

          }
        });

        $('input[type="radio"]').on('click',function(){
            console.log("ans");

            var nxt = $(this).attr('data-next');
            var qid = $(this).attr('data-qid');
            var ans = $(this).val();
            console.log(ans);

            if ( ans == '1'  && (nxt == 'q2') )
            {
              $.notify("Kindly indicate those symptoms by \nchecking the check boxes on the left.",{className:"error",globalPosition:'right middle',autoHideDelay:5000, clickToHide:true} );
              $('input[name="symptoms"]').attr('disabled',false);
              $('#q1 a').fadeIn();

              var d = {'question':qid, 'answer':ans};
              declarations.push(d);
              //return false;
              //$('.qs').fadeOut();$('#'+nxt).fadeIn();
            }else
            {
              var d = {'question':qid, 'answer':ans};
              declarations.push(d);

              $('.qs').fadeOut('fast');
              $('#'+nxt).show("slide", { direction: "right" }, 500);
            }
        });
      });
     </script>

</body>
</html>
          



