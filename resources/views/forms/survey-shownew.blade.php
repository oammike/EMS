@extends('layouts.main')

@section('metatags')
<title>Surveys | Open Access EMS</title>
<link rel="stylesheet" type="text/css" href="{{URL::asset('public/js/formBuilder/demo.css')}}">
@endsection

@section('content')


  <!-- Content Header (Page header) -->
    <section class="content-header">
      <h4><i class="fa fa-file"></i> {{$survey->name}} </h4>
      <ol class="breadcrumb">
        <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li>Surveys</li>
        <li class="active"> {{$survey->name}} </li>
      </ol>
    </section>

     <section class="content">



          
               

     
          <div class="row">
            <div class="col-lg-1"></div>
            <div class="col-lg-10">
              <table class="table" style="margin-top: 20px; width: 100%">
                
                <tr>
                  <td class="text-center" style="overflow: hidden; background-color: #000; position: absolute; height: 670px; width: 100%">
                    <div id="instructions" style="background: rgba(0, 0, 0, 0.4); position: absolute;bottom:45px; font-size: 0.95em;color:#fff; z-index: 100;padding: 5px; text-align: left;" ><i class="fa fa-info"></i> <em>The items above describe statements about different aspects of our work here at Open Access.<br/>
Indicate your range of agreement or disagreement by <span class="text-orange" style="font-weight: bolder;">clicking the appropriate radio button</span> OR <span class="text-orange" style="font-weight: bolder;">pressing keys 1-5 on your keyboard</span>. <br/>(Ranging from <strong>5 </strong> for when it's so awesome, all the way down to<strong> 1 </strong> when it's so horrible.) </em></div>
                     
                     <!-- /.info-box -->
                     <div style="position: absolute;top: 50px;left:25px; width: 95%">

                      <div class="info-box" style="min-height: 20px;background-color: #666">
                        

                        <div class="info-box-content">
                          

                          <div class="progress">
                            <div class="progress-bar" style="width:{{ ($startFrom/$totalItems)*100 }} %"></div>
                          </div>
                          <span class="progress-description" style="color:#73e9ff">
                               {{ ($startFrom/$totalItems)*100 }}%
                              </span>
                        </div>
                        <!-- /.info-box-content -->
                      </div>
                     </div>
                    
                    <!-- /.info-box -->
                    <h5 id="currItem" data-val="{{$startFrom}}" style="position: absolute;top:0px">{{$startFrom}}</h5>
                    <?php $ctr=1; ?>
                    @foreach($questions as $q)



                    @if($ctr==1 && is_null($latest) && ($extraDataNa != '1'))
                    <img class="question{{$ctr}}" src="../storage/uploads/@if(is_null($q->img))backto90s-30.jpg @else{{$q->img}}@endif" style="filter: alpha(opacity=60); opacity: 0.4; position: relative; top:0px; left:0px;" width="100%" />
                    @else
                      @if($extraDataNa != '1')
                      <img class="question{{$ctr}}" src="../storage/uploads/@if(is_null($q->img))backto90s-30.jpg @else{{$q->img}}@endif" style="filter: alpha(opacity=60); opacity: 0.4; position: relative; display:none; top:0px; left:0px;" width="100%" />
                      @endif
                    @endif
                     <!--  -->
                   
                      
                      @if($ctr==1 && is_null($latest))
                       <div class="question{{$ctr}}" style="background: rgba(255, 255, 255, 0.85); padding:30px; min-height: 287px; position: absolute; top:15%;left:25px; width: 95%">
                        <h2 class="question{{$ctr}} text-center" style="width: 100%; text-align: center;" >{{ $q->question }}</h2>

                        <br/><br/>

                        @if($q->responseType==1)

                        <div style="background-image: linear-gradient(to right, red , yellow, aqua, #5bc8ff); width: 70%; height: 5px; margin:0 auto;" >
                          <i class="fa fa-3x fa-frown-o pull-left" style="margin-top: 7px"></i>
                          <i class="fa fa-3x fa-smile-o pull-right" style="margin-top: 7px"></i>
                        </div><br/><br/><br/>

                          @foreach($options as $o)
                          <label><input type="radio" data-rtype="s" name="answer{{$q->id}}" value="{{$o->id}}"  id="answer{{$ctr}}_{{$o->ordering}}" /> [{{$o->value}}] {{$o->label}}  </label>&nbsp;&nbsp;&nbsp;

                          @endforeach

                           <textarea class="form-control" style="width: 70%; margin:0 auto;" name="notes" id="notes_q{{$q->ordering}}" placeholder="Notes / Comments"></textarea>

                        @else

                          <textarea name="essay" id="essay"></textarea>

                        @endif
                           <div class="clearfix"><br/></div>
                            <a id="next{{$ctr}}" data-questionid="{{$q->id}}" class="next btn btn-lg btn-primary" data-item="{{$ctr+1}}">Next <i class="fa fa-arrow-right"></i></a>
                        </div>

                      <!-- ******* LAST SLIDE *********** -->
                      @elseif (($ctr == $totalItems) && ($extraDataNa != '1'))

                      <div class="question{{$ctr}}" style="background: rgba(255, 255, 255, 0.8); padding:30px; min-height: 287px; position: absolute; top:15%;left:25px; width: 95%; display: none">
                        <h2 class="question{{$ctr}} text-center" style="width: 100%; text-align: center;" >{{ $q->question }}</h2>
                        <br/><br/>

                        @if($q->responseType==1)
                        <div style="background-image: linear-gradient(to right, red , yellow, aqua, #5bc8ff); width: 70%; height: 5px; margin:0 auto;" >
                          <i class="fa fa-3x fa-frown-o pull-left" style="margin-top: 7px"></i>
                          <i class="fa fa-3x fa-smile-o pull-right" style="margin-top: 7px"></i>
                        </div><br/><br/><br/>

                          @foreach($options as $o)
                          <label><input type="radio" data-rtype="s" name="answer{{$q->id}}" value="{{$o->id}}"  id="answer{{$ctr}}_{{$o->ordering}}"/> [{{$o->value}}] {{$o->label}}  </label>&nbsp;&nbsp;&nbsp;

                          @endforeach
                           <textarea class="form-control" style="width: 70%; margin:0 auto;" name="notes" id="notes_q{{$ctr}}" placeholder="Notes / Comments"></textarea>

                        @else

                          <textarea class="form-control" name="essay" id="essay" placeholder="type in your answer"></textarea>
                         
                          
                        @endif
                            <div class="clearfix"><br/></div>

                          
                             <a id="submit1" class="btn btn-lg btn-primary" data-questionid="{{$q->id}}" data-item="{{$ctr+1}}">One more to go... <i class="fa fa-arrow-right"></i></a>

                          
                        </div>




                        <!-- ******* LAST SLIDE *********** -->

                      @elseif ( $extraDataNa != '1')
                     <div class="question{{$ctr}}" style="background: rgba(255, 255, 255, 0.8); padding:30px; min-height: 287px; position: absolute; top:15%;left:25px; width: 95%; display: none">
                        <h2 class="question{{$ctr}} text-center" style="width: 100%; text-align: center;" >{{ $q->question }}</h2>
                        <br/><br/>

                        @if($q->responseType==1)
                           <div style="background-image: linear-gradient(to right, red , yellow, aqua, #5bc8ff); width: 70%; height: 5px; margin:0 auto;" >
                              <i class="fa fa-3x fa-frown-o pull-left" style="margin-top: 7px"></i>
                              <i class="fa fa-3x fa-smile-o pull-right" style="margin-top: 7px"></i>
                            </div><br/><br/><br/>

                              @foreach($options as $o)
                              <label><input type="radio" data-rtype="s" name="answer{{$q->id}}" value="{{$o->id}}" id="answer{{$ctr}}_{{$o->ordering}}" /> [{{$o->value}}] {{$o->label}}  </label>&nbsp;&nbsp;&nbsp;

                              @endforeach
                               <textarea class="form-control" style="width: 70%; margin:0 auto;" name="notes" id="notes_q{{$q->ordering}}" placeholder="Notes / Comments"></textarea>

                        @else

                          <textarea class="form-control" name="essay" id="essay"  placeholder="type in your answer"></textarea>
                          <div class="clearfix"><br/></div>
                            <a id="next{{$ctr}}" data-questionid="{{$q->id}}" class="nextEssay btn btn-lg btn-primary" data-item="{{$ctr+1}}">Next <i class="fa fa-arrow-right"></i></a>
                          

                        @endif
                           <div class="clearfix"><br/></div>
                            <a id="next{{$ctr}}" data-questionid="{{$q->id}}" class="next btn btn-lg btn-primary" data-item="{{$ctr+1}}">Next <i class="fa fa-arrow-right"></i></a>

                          
                        </div>
                      @endif
                      <!-- $survey[0]['questions'][7]->question -->
                      
                    <?php $ctr++; ?>
                    @endforeach


                    <img class="extra" src="../storage/uploads/pulse2020.jpg" style="filter: alpha(opacity=60); opacity: 0.4; position: relative; display:none; top:0px; left:0px;" width="100%" />
                    <div class="extra" style="background: rgba(255, 255, 255, 0.8); padding:30px; min-height: 287px; position: absolute; top:15%;left:25px; width: 95%; display: none">
                            <h4 class="extra text-center" style="width: 100%; text-align: center;" >Help us improve our employee engagement activities by filling out the form below: </h4>
                            <br/>
                            <div class="row">
                              <div class="col-xs-6 text-left">
                                <label>What best describes your gender?</label><br/>
                                <label><input type="radio" data-rtype="g" value="Female" name="gender"> Female</label>&nbsp;&nbsp;
                                <label><input type="radio" data-rtype="g" value="Male" name="gender"> Male</label>&nbsp;&nbsp;
                                <label><input type="radio" data-rtype="g" value="Prefer Not to Say" name="gender"> Prefer not to say</label>&nbsp;&nbsp;
                                <label><input type="radio" data-rtype="g" value="Self describe" name="gender"> Prefer to Self-describe</label>&nbsp;&nbsp;
                                <input type="text" name="genderdesc" class="form-control genderdesc" id="genderdesc" style="width: 70%" placeholder="indicate you gender identity" />
                              
                                <div class="clearfix"></div>
                                <label><br/>Education Level:</label><br/>
                                <label><input type="radio" data-rtype="e" value="High School" name="education"> High School</label>&nbsp;&nbsp;
                                <label><input type="radio" data-rtype="e" value="College" name="education"> College</label>&nbsp;&nbsp;
                                <label><input type="radio" data-rtype="e" value="Postgraduate" name="education"> Postgraduate</label>&nbsp;&nbsp;
                                <label><input type="radio" data-rtype="e" value="Others" name="education"> Others</label><br/>
                                <label class="course"  style="width: 100%" ><span>Course:</span> <input type="text" name="course" class="form-control course" id="course"  style="width: 70%"  /></label>
                              </div>
                              <div class="col-xs-6 text-left">
                                 <label>Hobbies and Interests</label><br/>
                                <textarea class="form-control" name="hobbies" id="hobbies" style="width: 80%"></textarea>

                               
                                <br/>
                                 <label>Current Town/City/Province of Residence</label>
                                <input required="required" type="text" class="form-control" name="city" id="city" placeholder="indicate current city,town or province" style="width: 80%"  />
                                <br/>
                                <label>Daily Commute Time (to and from the office)</label><br/>
                                <label class="pull-left" style="width:120px"><input type="text" class="form-control" name="hrs" id="hrs" placeholder="00" style="width:40%"  />hour(s)</label>
                                <label class="pull-left" style="margin-left:-60px"><input type="text" class="form-control" name="mins" id="mins" placeholder="00" style="width:40%"  />minutes</label>
                               
                              </div>
                            </div>
                            <div class="row">
                              <div class="col-xs-6 text-left"><br/>
                                
                              </div>
                              <div class="col-xs-6 text-left">
                                </div>
                            </div>
                            
                            

                            <a style="margin-top: 20px" id="submit2" class="btn btn-lg btn-success" data-questionid="{{$totalItems+1}}" data-item="{{$totalItems+1}}">Submit <i class="fa fa-check"></i></a>
                    </div>


                  </td>
                </tr>

                
               
              </table>

              
              

            </div>
           <div class="col-lg-1"></div>


            

            

          </div><!-- end row -->





       
     </section>
          



@endsection


@section('footer-scripts')

<script>
  $.widget.bridge('uibutton', $.ui.button);
</script>



<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.3.1/jquery.rateyo.min.js"></script>
 -->


<!-- Page script -->
<script>



  $(function () {

    
   'use strict';

   


   $('.next').fadeOut();//css('display','none');
   $('textarea[name="notes"]').fadeOut();
   $('.course').fadeOut();
   $('.genderdesc').fadeOut();

   @if($extraDataNa == 1)

       $('.question{{$startFrom}}').css('display','none');
       $('.extra').fadeIn();//css("display","block");
       $('#instructions').fadeOut();
       $('.info-box').fadeOut();

   @endif

   @if( !is_null($latest))

      var perc = (({{$latest->ordering+1}}/{{$totalItems}})*100).toFixed(0);

      $('.question{{$latest->ordering}}').hide();
      $('.question{{$latest->ordering}}').css('display','none');
      $('.question{{$latest->ordering + 1}}').fadeIn();//css("display","block");
      $('.question{{$latest->ordering + 1}}').css("display","block");
      $('.progress-description').html(perc+" %");
      $('.progress-bar').css('width',perc+"%");

      $('#currItem').attr('data-val',{{$latest->ordering + 1}});

      // hide submit button if done with survey
      @if($userSurvey->isDone)
        $('#submit').hide();
      @endif

   @endif

   $('input:radio').on('click',function(){

      var rtype = $(this).attr('data-rtype');

      if (rtype == 's') //rbutton for Surveys
      {
        var val=$('#currItem').attr('data-val');
        var bval = val;
        if (val >= 27) val = parseInt(val)+2;


        $('#next'+bval).fadeIn(); //css('display','block')
        console.log(val);

        
        var r = $(this).val();
        if (r != 3) $('#notes_q'+val).fadeIn();
        else $('#notes_q'+val).fadeOut();

      } else if(rtype == 'e')
      { //rbutton for educ

        var educ = $(this).val();

        if (educ == 'College' || educ == 'Postgraduate'){
          $('.course').fadeIn();
          $('.course span').html('Course: ');
        }else if(educ == 'Others'){
          $('.course').fadeIn();
          $('.course span').html('Please specify: ');

        }else $('.course').fadeOut();

      }else if(rtype == 'g'){
        var desc =  $(this).val();
        if (desc == 'Self describe') $('#genderdesc').fadeIn();
        else $('#genderdesc').fadeOut();
      }
      

   });

   $('.next').on('click',function(){
      var item = $(this).attr('data-item');
      var curr = item-1;
      var perc = ((item/{{$totalItems}})*100).toFixed(0);
      //alert("Next: "+curr);
      

      $('#currItem').html(item);
      $('#currItem').attr('data-val',item);



      // we now save his answer
      var _token = "{{ csrf_token() }}";
      var questionid = $(this).attr('data-questionid');
      var survey_optionsid = $('input[name="answer'+questionid+'"]:checked').val();
      console.log("questionid: " + questionid);
      //console.log(survey_optionsid);

      //get if may existing comment
      var comment = $('#notes_q'+questionid).val();
     
      $.ajax({
                url: "{{action('SurveyController@saveItem')}}",
                type:'POST',
                data:{ 
                  'questionid': questionid,
                  'survey_optionsid': survey_optionsid,
                  'comment': comment,
                  '_token':_token
                },
                success: function(response){
                  console.log(response);

                  $('.question'+curr).hide();
                  $('.question'+item).fadeIn();//css("display","block");
                  $('.question'+item).css("display","block");
                  $('.progress-description').html(perc+" %");
                  $('.progress-bar').css('width',perc+"%");
                }
              });
      

   });

   $('.nextEssay').on('click',function(){
      var item = $(this).attr('data-item');
      var curr = item-1;
      var perc = ((item/{{$totalItems}})*100).toFixed(0);
     
      var openended = $(this).siblings('textarea');
      var totalItems = '{{$totalItems}}';
      

      $('#currItem').html(item);
      $('#currItem').attr('data-val',item);

      console.log("length:");
      console.log(openended.val().length);


      if (openended.val().length <= 3 ) $.notify("We would like to hear from you. \nFilling out the form will help us gather needed data to make every employee's experience more awesome at Open Access.",{className:"error",globalPosition:'right center',autoHideDelay:7000, clickToHide:true} );
      else 
      {
         // we now save his answer
        var _token = "{{ csrf_token() }}";
        var questionid = $(this).attr('data-questionid');
        var survey_optionsid = $('input[name="answer'+questionid+'"]:checked').val();
        console.log("questionid: " + questionid);
        //console.log(survey_optionsid);

        //get if may existing comment
        var comment = $('#notes_q'+questionid).val();
       
         $.ajax({
                url: "{{action('SurveyController@saveItem')}}",
                type:'POST',
                data:{ 
                  'questionid': questionid,
                  'survey_optionsid': 'e',
                  'survey_id': '{{$id}}',
                  'answer': openended.val(),
                  '_token':_token
                },
                success: function(response){
                  console.log(response);

                  if(curr < totalItems)
                  {
                    $('.question'+curr).hide();
                    $('.question'+item).fadeIn();//css("display","block");
                    $('.question'+item).css("display","block");
                    $('.progress-description').html(perc+" %");
                    $('.progress-bar').css('width',perc+"%");
                  }
                  else
                  {
                    $('.question'+curr).hide();
                    $('.extra').fadeIn();//css("display","block");
                    $('#instructions').fadeOut();
                    $('.info-box').fadeOut();

                  }
                  

                  
                 

                }
              });


      }



     
      

   });




   
  $('#submit1').on('click',function(){

    var questionid = $(this).attr('data-questionid');
    var item = $(this).attr('data-item');
    var curr = item-1;
    var openended = $(this).siblings('textarea');

    console.log($('#essay').val().length);


    
    if (openended.val().length <= 3 ) $.notify("We would like to hear from you. \nFilling out the form will help us gather needed data to make every employee's experience more awesome at Open Access.",{className:"error",globalPosition:'right center',autoHideDelay:7000, clickToHide:true} );
    else 
    {

      $(this).fadeOut();
      var _token = "{{ csrf_token() }}";

      $.ajax({
                url: "{{action('SurveyController@saveItem')}}",
                type:'POST',
                data:{ 
                  'questionid': questionid,
                  'survey_optionsid': 'e',
                  'survey_id': '{{$id}}',
                  'answer': $('#essay').val(),
                  '_token':_token
                },
                success: function(response){
                  console.log(response);

                  $('.question'+curr).hide();
                  $('.extra').fadeIn();//css("display","block");
                  $('#instructions').fadeOut();
                  $('.info-box').fadeOut();
                 

                }
              });
      


     
    }

   });


  $('#submit2').on('click',function(){

    //check if gay
    if ($('input[name="gender"]:checked').val() == 'Self describe')
    {
      if ($('#genderdesc').val() == ''){

        $.notify("All fields are required. \nFilling out the form will help us gather needed data to make every employee's experience more awesome at Open Access.",{className:"error",globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );return false;
      }
      
    } else if ($('input[name="education"]:checked').val() != 'High School'){

      if($('#course').val() == ''){
         $.notify("All fields are required. \nFilling out the form will help us gather needed data to make every employee's experience more awesome at Open Access.",{className:"error",globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );return false;
      }

    }



    //check first required values
    if ( ($('input[name="gender"]:checked').length > 0) && 

         ($('input[name="education"]:checked').length > 0) && ($('#hobbies').val().length > 0) && ( $('#hrs').val().length > 0 || $('#mins').val().length > 0 ) && $('#city').val().length > 0 )
    {
      //console.log($('#hobbies').val() );
      var _token = "{{ csrf_token() }}";
      var g = $('input[name="gender"]:checked').val();

      if (g == "Self describe"){ var gender = $('input[name="genderdesc"]').val(); }
      else var gender = g;

      var ed = $('input[name="education"]:checked').val();
      if (ed == "High School"){ var course = "Senior High"}
      else { var course = $('input[name="course"]').val();}

      var currentlocation = $('input[name="city"]').val();
      var hr = $('input[name="hrs"]').val();
      var mins = $('input[name="mins"]').val();

      $.ajax({
                url: "{{action('SurveyController@saveItem')}}",
                type:'POST',
                data:{ 
                  
                  'survey_optionsid': 'x',
                  'survey_id': '{{$id}}',
                  'gender': gender,
                  'education': ed,
                  'course': course,
                  'currentlocation': currentlocation,
                  'hr': hr,
                  'mins': mins,
                  'hobbiesinterest': $('#hobbies').val(),
                  '_token':_token
                },
                success: function(response){
                  console.log(response);
                  $('#submit2').fadeOut();
                   $.notify("Form submitted successfully!",{className:"success",globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );
                   location.replace('../surveyResults/{{$id}}');

                }
              });
      
    } else{

      $.notify("All fields are required. \nFilling out the form will help us gather needed data to make every employee's experience more awesome at Open Access.",{className:"error",globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );
      return false;

    }

      

  });

  $('body').keyup(function (e){

    var val=$('#currItem').attr('data-val');
    //var con = $("answer"+val+"_1");
    //alert("Curr: "+val);
    //console.log(con);

    if (e.keyCode == 49) {
      document.getElementById("answer"+val+"_1").click();
   } else if (e.keyCode == 50) {
      //$('.answer1').prop('checked',false);
      //$('#answer1_2').attr('checked',true);
      document.getElementById("answer"+val+"_2").click();
   } else if (e.keyCode == 51) {
      //$('.answer1').prop('checked',false);
      //$('#answer1_3').attr('checked',true);
      document.getElementById("answer"+val+"_3").click();
   } else if (e.keyCode == 52) {
     //$('.answer1').prop('checked',false);
      //$('#answer1_4').attr('checked',true);
      document.getElementById("answer"+val+"_4").click();
   } else if (e.keyCode == 53) {
      //$('.answer1').prop('checked',false);
      //$('#answer1_5').attr('checked',true);
     document.getElementById("answer"+val+"_5").click();
   } 
 })

   


      
    


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
<!-- end Page script -->


@stop