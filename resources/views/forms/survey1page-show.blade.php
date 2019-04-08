@extends('layouts.main')

@section('metatags')
<title>Survey | Open Access EMS</title>
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
                    <div id="instructions" style="background: rgba(0, 0, 0, 0.4); position: absolute;bottom:45px; font-size: 0.95em;color:#fff; z-index: 100;padding: 5px; text-align: left;" ></div>
                     
                     <!-- /.info-box -->
                     <div style="position: absolute;top: 50px;left:25px; width: 95%">

                      <div class="info-box" style="min-height: 20px;background-color: #666">
                        

                        <div class="info-box-content">
                          

                          <div class="progress">
                            
                          </div>
                         
                        </div>
                        <!-- /.info-box-content -->
                      </div>
                     </div>
                    
                    <!-- /.info-box -->
                    <h5 id="currItem" data-val="{{$startFrom}}" style="position: absolute;top:0px">{{$startFrom}}</h5>
                    <?php $ctr=1; ?>
                    @foreach($questions as $q)

                    <img class="question{{$ctr}}" src="../storage/uploads/@if(is_null($q->img))backto90s-30.jpg @else{{$q->img}}@endif" style="filter: alpha(opacity=60); opacity: 0.4; position: relative; top:0px; left:0px;" width="100%" />
                    
                   
                      
                      
                       <div class="question{{$ctr}}" style="background: rgba(0, 0, 0, 0.5); padding:30px; min-height: 287px; position: absolute; top:15%;left:25px; width: 95%">
                        <h4 class="question{{$ctr}} text-center" style="width: 100%; text-align: center;color:#e6e469" >{!! $q->question !!}</h4>

                        <br/><br/>

                        @if($q->responseType==1)

                        <div class="row">
                          <?php switch ($id) {
                            case '3':{
                                        $imgs = ['bnw.png','disney.jpg','carnival.jpg','scifi.png']; $ctimg=0;
                            }break;

                            case '4':{
                                        $imgs = ['bnw.png','disney.jpg','carnival.jpg','scifi.png']; $ctimg=0;
                            }break;
                            
                            default: {
                                        $imgs = ['','','','']; $ctimg=0;
                            }break;
                          }  ?>

                          @foreach($options as $o)
                          
                          @if($id==4)<div class="col-lg-4">@else <div class="col-lg-3"> @endif
                            <label @if($id != 4) style="color: #e6e469" @else style="color:#fff" @endif>
                              @if($id != 4) <img src="../storage/uploads/{{$imgs[$ctimg]}}" height="110" />@endif <br/>

                              <input type="radio" data-rtype="s" name="answer{{$q->id}}" value="{{$o->id}}"  id="answer{{$ctr}}_{{$o->ordering}}" /> [{{$o->value}}] {{$o->label}}  </label>&nbsp;&nbsp;&nbsp;


                          </div>
                          <?php $ctimg++;?>
                          @endforeach


                           <textarea class="form-control" style="width: 70%; margin:0 auto;" name="notes" id="notes_q{{$q->id}}" placeholder="Comments/Suggestions"></textarea>

                         </div>

                        

                        @else

                          <textarea name="essay" id="essay"></textarea>

                        @endif
                           <div class="clearfix"><br/></div>

                           @if ($userSurvey->isDone)
                              <h4 class="text-danger">Thank you for participating in this survey.</h4>
                              <p style="color:#fff">We'll send out further details about the party once everything is finalized.</p>
                              
                              @if($canViewAll)<a href="{{action('SurveyController@report',$id)}}" class="btn btn-xs btn-danger">See survey results</a>@endif
                           @else
                              <a id="submit" data-questionid="{{$q->id}}" class="next btn btn-lg btn-success" data-item="{{$ctr+1}}">Submit <i class="fa fa-upload"></i></a>


                           @endif
                        </div>

                 
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


 

      // hide submit button if done with survey
      @if($userSurvey->isDone)
        $('#submit').hide();
      @endif


  @if($id==4) //Performers survey
  $('textarea').fadeOut();
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

        @if ($id == 4) //Performers survey
          var r = $(this).val();
          if (r == 17) $('textarea').fadeIn();
          else $('textarea').fadeOut();
        @endif

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

   




  $('#submit').on('click',function(){

    var item = $(this).attr('data-item');

    // we now save his answer
      var _token = "{{ csrf_token() }}";
      var questionid = $(this).attr('data-questionid');
      var survey_optionsid = $('input[name="answer'+questionid+'"]:checked').val();
      console.log("questionid: " + questionid);
      //console.log(survey_optionsid);



    //check first required values
    if ( ($('input[name="answer'+questionid+'"]:checked').length > 0) )
    {
      
      //get if may existing comment
      var comment = $('#notes_q'+questionid).val();
     
      $.ajax({
                url: "{{action('SurveyController@saveItem')}}",
                type:'POST',
                data:{ 
                  'questionid': questionid,
                  'survey_optionsid': survey_optionsid,
                  'comment': comment,
                  'survey_id': "{{$id}}",
                  'survey_userid':"{{$userSurvey->id}}",
                  '_token':_token
                },
                success: function(response){
                  console.log(response);

                  $('#submit').fadeOut();
                   $.notify("We got your vote! \nThank you for participating in this survey!",{className:"success",globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );
                    $.ajax({
                            url: "{{action('SurveyController@saveSurvey')}}",
                            type:'POST',
                            data:{ 

                              'survey_id': "{{$id}}",
                              '_token':_token
                            },
                            success: function(response){
                              console.log(response);

                               //setTimeout(function(){ location.replace('../surveyResults/{{$id}}'); }, 3000);

                              
                            }
                          });
                   

                  
                }
              });
      
      
      
    } else{

      $.notify("Please choose an option. \nFilling out the form will help us gather needed data to make our year end party fun and more awesome!",{className:"error",globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );
      return false;

    }

      

  });

 

   


      
    


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