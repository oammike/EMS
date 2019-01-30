@extends('layouts.main')

@section('metatags')
<title>Surveys | Open Access EMS</title>
<link rel="stylesheet" type="text/css" href="{{URL::asset('public/js/formBuilder/demo.css')}}">
@endsection

@section('content')


  <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1><i class="fa fa-file"></i> Survey Title Goes Here </h1>
      <ol class="breadcrumb">
        <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Access Denied</li>
      </ol>
    </section>

     <section class="content">



          
               

     
          <div class="row">
            <div class="col-lg-1"></div>
            <div class="col-lg-10">
              <table class="table" style="margin-top: 20px; width: 100%">
                
                <tr>
                  <td class="text-center" style="overflow: hidden; background-color: #000; position: absolute; height: 670px; width: 100%">
                    <div style="background: rgba(0, 0, 0, 0.4); position: absolute;top:10px; font-size: 0.95em;color:#fff; z-index: 100;padding: 5px" ><i class="fa fa-info"></i> <em>Click on the radio buttons below, OR use number keys <strong>1-5</strong> on your keyboard to give a rating.</em></div>
                     
                     <!-- /.info-box -->
                     <div style="position: absolute;bottom: 90px;left:25px; width: 95%">

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
                    <h1 id="currItem" data-val="{{$startFrom}}" style="position: absolute;top:0px">{{$startFrom}}</h1>
                    <?php $ctr=1; ?>
                    @foreach($questions as $q)

                    @if($ctr==1 && is_null($latest))
                    <img class="question{{$ctr}}" src="../storage/uploads/@if(is_null($q->img))survey.jpg @else{{$q->img}}@endif" style="filter: alpha(opacity=60); opacity: 0.4; position: relative; top:0px; left:0px;" width="100%" />
                    @else
                    <img class="question{{$ctr}}" src="../storage/uploads/@if(is_null($q->img))survey.jpg @else{{$q->img}}@endif" style="filter: alpha(opacity=60); opacity: 0.4; position: relative; display:none; top:0px; left:0px;" width="100%" />
                    @endif
                     <!--  -->
                   
                      
                      @if($ctr==1 && is_null($latest))
                       <div class="question{{$ctr}}" style="background: rgba(255, 255, 255, 0.85); padding:30px; min-height: 287px; position: absolute; top:25%;left:25px; width: 95%">
                        <h2 class="question{{$ctr}} text-center" style="width: 100%; text-align: center;" >{{ $q->question }}</h2>

                        <br/><br/>

                        @if($q->responseType==1)

                        <div style="background-image: linear-gradient(to right, red , yellow); width: 70%; height: 5px; margin:0 auto;" >
                          <i class="fa fa-3x fa-frown-o pull-left" style="margin-top: 7px"></i>
                          <i class="fa fa-3x fa-smile-o pull-right" style="margin-top: 7px"></i>
                        </div><br/><br/><br/>

                          @foreach($options as $o)
                          <label><input type="radio" name="answer{{$q->id}}" value="{{$o->id}}"  id="answer{{$ctr}}_{{$o->ordering}}" /> [{{$o->value}}] {{$o->label}}  </label>&nbsp;&nbsp;&nbsp;

                          @endforeach

                        @else

                          <textarea name="essay" id="essay"></textarea>

                        @endif
                            <br/><br/>

                          <a id="next{{$ctr}}" data-questionid="{{$q->id}}" class="next btn btn-lg btn-primary pull-right" data-item="{{$ctr+1}}">Next <i class="fa fa-arrow-right"></i></a>
                        </div>


                      @elseif($ctr == $totalItems)

                      <div class="question{{$ctr}}" style="background: rgba(255, 255, 255, 0.8); padding:30px; min-height: 287px; position: absolute; top:25%;left:25px; width: 95%; display: none">
                        <h2 class="question{{$ctr}} text-center" style="width: 100%; text-align: center;" >{{ $q->question }}</h2>
                        <br/><br/>

                        @if($q->responseType==1)
                        <div style="background-image: linear-gradient(to right, red , yellow); width: 70%; height: 5px; margin:0 auto;" >
                          <i class="fa fa-3x fa-frown-o pull-left" style="margin-top: 7px"></i>
                          <i class="fa fa-3x fa-smile-o pull-right" style="margin-top: 7px"></i>
                        </div><br/><br/><br/>

                          @foreach($options as $o)
                          <label><input type="radio" name="answer{{$q->id}}" value="{{$o->id}}"  id="answer{{$ctr}}_{{$o->ordering}}"/> [{{$o->value}}] {{$o->label}}  </label>&nbsp;&nbsp;&nbsp;

                          @endforeach

                        @else

                          <textarea class="form-control" name="essay" id="essay" placeholder="type in your comments and/or suggestions..."></textarea>

                        @endif
                            <br/><br/>

                            <a id="submit" class="btn btn-lg btn-success pull-right" data-questionid="{{$q->id}}" data-item="{{$ctr+1}}">Submit <i class="fa fa-check"></i></a>

                          
                        </div>

                      @else
                     <div class="question{{$ctr}}" style="background: rgba(255, 255, 255, 0.8); padding:30px; min-height: 287px; position: absolute; top:25%;left:25px; width: 95%; display: none">
                        <h2 class="question{{$ctr}} text-center" style="width: 100%; text-align: center;" >{{ $q->question }}</h2>
                        <br/><br/>

                        @if($q->responseType==1)
                       <div style="background-image: linear-gradient(to right, red , yellow); width: 70%; height: 5px; margin:0 auto;" >
                          <i class="fa fa-3x fa-frown-o pull-left" style="margin-top: 7px"></i>
                          <i class="fa fa-3x fa-smile-o pull-right" style="margin-top: 7px"></i>
                        </div><br/><br/><br/>

                          @foreach($options as $o)
                          <label><input type="radio" name="answer{{$q->id}}" value="{{$o->id}}" id="answer{{$ctr}}_{{$o->ordering}}" /> [{{$o->value}}] {{$o->label}}  </label>&nbsp;&nbsp;&nbsp;

                          @endforeach

                        @else

                          <textarea name="essay" id="essay"></textarea>

                        @endif
                            <br/><br/>

                            <a id="next{{$ctr}}" data-questionid="{{$q->id}}" class="next btn btn-lg btn-primary pull-right" data-item="{{$ctr+1}}">Next <i class="fa fa-arrow-right"></i></a>

                          
                        </div>
                      @endif
                      <!-- $survey[0]['questions'][7]->question -->
                      
                    <?php $ctr++; ?>
                    @endforeach
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
    var val=$('#currItem').attr('data-val');
    $('#next'+val).fadeIn(); //css('display','block')

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
      console.log(survey_optionsid);
      $.ajax({
                url: "{{action('SurveyController@saveItem')}}",
                type:'POST',
                data:{ 
                  'questionid': questionid,
                  'survey_optionsid': survey_optionsid,
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

   
  $('#submit').on('click',function(){

    var questionid = $(this).attr('data-questionid');


    if ($('#essay').val() == '' ) $.notify("We would like to hear from you, so kindly fill out the comment box. Thanks!",{className:"error",globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );
    else {
      $(this).fadeOut();
      var _token = "{{ csrf_token() }}";

      $.ajax({
                url: "{{action('SurveyController@saveItem')}}",
                type:'POST',
                data:{ 
                  'questionid': questionid,
                  'survey_optionsid': 'x',
                  'survey_id': '{{$id}}',
                  'answer': $('#essay').val(),
                  '_token':_token
                },
                success: function(response){
                  console.log(response);
                   $.notify("Thank you for taking the time answering our survey!",{className:"success",globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );

                }
              });
      


     
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