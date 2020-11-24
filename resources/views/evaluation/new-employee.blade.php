@extends('layouts.main')

@section('metatags')
<title>New Evaluation | OAMPI Evaluation System</title>
<style type="text/css">
a.back-to-top {
  display: none;
  width: 60px;
  height: 60px;
  text-indent: -9999px;
  position: fixed;
  z-index: 999;
  right: 20px;
  bottom: 20px;
  background: #67aa08 url("{{asset('public/img')}}/up-arrow.png") no-repeat center 43%;
  -webkit-border-radius: 30px;
  -moz-border-radius: 30px;
  border-radius: 30px;
}
</style>
@endsection

@section('bodyClasses')
sidebar-collapse
@endsection

@section('content')


  <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
         {{$evalType->name}}

      <small>Period: <strong> {{$currentPeriod->format('M d')}}  to {{$endPeriod->format("M d, Y")}}  <?php /* {{ date("M d", strtotime($evalForm->startPeriod->date))  }} to {{ date("M d Y", strtotime($evalForm->endPeriod->date)) }} */ ?></strong></small>
      
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">New Evaluation</li>
      </ol>
    </section>

     <section class="content">

          <div class="row">
            
            <div class="col-lg-4 col-sm-5 col-xs-12">
               <!-- Widget: user widget style 1 -->
                          <div class="box box-widget widget-user-2">
                            <!-- Add the bg color to the header using any of the bg-* classes -->
                            <div class="widget-user-header  @if ($employee->userType_id == '3')  bg-gray @else bg-green @endif">

                              <div class="widget-user-image">
                                @if ( file_exists('public/img/employees/'.$employee->id.'.jpg') )
                                <img src="{{asset('public/img/employees/'.$employee->id.'.jpg')}}" class="img-circle" alt="User Image">
                                @else
                                  <img src="{{asset('public/img/useravatar.png')}}" class="user-image" alt="Employee Image">

                                  @endif
                              </div>
                              <!-- /.widget-user-image -->
                              <h3 class="widget-user-username"> {{$employee->lastname }}, {{$employee->firstname}} </h3>
                              @if(empty($showPosition))
                              <h5 class="widget-user-desc">{{$employee->position->name}}<br/>
                                @else
                              <h5 class="widget-user-desc">{{$showPosition}}<br/>
                              @endif
                             <strong> {{$employee->campaign[0]->name}} </strong> </h5>
                             
                            </div>
                            <div class="box-footer no-padding">

                              <ul class="nav nav-stacked">
                                <li><a href="#">Date Hired <span class="pull-right badge bg-gray">{{ date_format(date_create($employee->dateHired), "M d, Y")  }} </span></a></li>
                                <li><a href="#">Absences <span class="pull-right badge bg-red">N/A</span></a></li>
                                <li><a href="#">Tardiness <span class="pull-right badge bg-red">N/A</span></a></li>
                                <li><a href="#">Received Disciplinary Actions <span class="pull-right badge bg-red">N/A</span></a></li>

                                @if ($employee->userType_id == 4)
                                <li><h5 class="text-left text-success" style="margin-left:18px"><br/><strong>KPI Summary</strong></h5></li>
                                <li><a href="#">Accomplished Tasks <span class="pull-right badge">N/A</span></a></li>
                                <li><a href="#">Pending Tasks <span class="pull-right badge ">N/A</span></a></li>
                                <li><a href="#">Delays <span class="pull-right badge ">N/A</span></a></li>

                                @endif
                                
                              </ul>
                             <div class="clearfix"></div>


                            </div>
                          </div>
                          <!-- /.widget-user -->


                          <div class="box box-widget widget-user-2">
                            <!-- Add the bg color to the header using any of the bg-* classes -->
                            <div class="box-header"><h3 class="text-center">Grading Scale</h3></div>
                            <div class="box-footer">
                              <table class="table">
                                <thead>
                                  <tr>
                                    <th class="text-center">Scale</th>
                                   <!--  <th class="text-center">%</th> -->
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Description</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  @foreach ($ratingScale as $rs)
                                  <tr id="rating-{{$rs->percentage}}" class="ratingTable" data-salaryIncrease="{{$rs->increase}}">
                                    <td><h4>{{$rs->label}}</h4></td>
                                    <!-- <td class="text-center">{{$rs->maxRange}}</td> -->
                                    <td class="text-center">{{$rs->status}} </td>
                                    <td class="text-left"><small>{{$rs->description}}</small> </td>
                                  </tr>
                                  @endforeach

                                </tbody>
                              </table>

                             <div class="clearfix"></div>


                            </div>
                          </div>

            </div><!--end employee card-->

            <div class="col-lg-8 col-sm-7 col-xs-12">
              <div class="box box-primary">
                      <div class="box-header ">
                          
                             
                        

                      
                      </div><!--end box-header-->
                      
                      <div class="box-body">
                        <div class="row">
                          <div class="col-lg-8">
                             <h5><strong>Instructions:</strong></h5>
                            <p> This Evaluation Form has a graduated scale from 5 to 1 with suggested qualitative definitions. Take your time, give thought to each performance factor and choose a numerical rating which best describes the employee's performance for each section 
                           Refer to the Grading Scale table for the rating range and definitions;</p>

                          </div>
                          <div class="col-lg-4">
                             <a class="pull-right text-yellow" id="how"><i class="fa fa-question-circle"></i></a>
                             <h1 style="line-height:0.8" class="text-danger pull-right text-right" id="overallScore" value="0">0% <br/><span style="font-size:0.6em;" class="text-gray">Let's rate {{$employee->firstname}}'s<br/> performance  <i class="fa fa-smile-o"></i> </span></h1>
                             <div class="clearfix"></div>
                             <h1 style="line-height:0.8; margin-top:-10px" class="text-danger pull-right text-right" id="descriptions"></h1>
                       
                          </div>
                        </div>
                       

                       
                           <div class="clearfix"><br/><br/></div>


                        
                       

                        
                        
                        <a data-isDraft="0" class="submitBtn btn btn-sm btn-success pull-right" style="margin-bottom:20px"><i class="fa fa-check"></i> Submit Evaluation</a>
                        <a data-isDraft="1" class="submitBtn btn btn-sm btn-default pull-right" style="margin-bottom:20px; margin-right:10px"><i class="fa fa-save"></i> Save Draft</a>

                        <div class="loader callout callout-info col-xs-12 pull-right" style="text-align:center"><p><i class="fa fa-download"></i><strong> Saving Evaluation Form...</strong> <i class="fa fa-spinner fa-spin"></i></p></div>
                        <br/><br/>

                        <div class="clearfix"></div>

                        <div class="nav-tabs-custom">
                          <ul class="nav nav-tabs">
                            <li class="active bg-success"><a href="#evalform" data-toggle="tab"><strong>Competency Evaluation</strong></a></li>
                            <li class="bg-success"><a href="#summary" data-toggle="tab" style="font-weight:bold">Performance Summary</a></li>
                            <li class="bg-success"><a href="#details" data-toggle="tab" style="font-weight:bold">Guidelines</a></li> 
                          </ul>

                          {{ Form::open(['route' => 'evalForm.store', 'class'=>'col-lg-12', 'id'=> 'saveEval' ]) }}
                          <input type="hidden" name="evalForm_id" id="evalForm_id" value="{{$evalForm->id}}" />
                          <input type="hidden" name="salaryIncrease" id="salaryIncrease" value="0" />
                         <!-- {{Form::submit('Save Evaluation')}}  -->
                          


                          <div class="tab-content">
                            <div class="active tab-pane" id="evalform">

                              <table class="table table-striped">
                                
                                <input type="hidden" id="total" name="total" value="0" />
                                <tr>
                                  <th class="text-center">Competencies</th>
                                  <th style="width:15%" class="text-center">Max. Weight</th>
                                  <th style="width: 15%"class="text-center">Weighted Score</th>
                                  <th style="width:15%"class="text-center">Scale</th>
                                </tr>
                              </table>
                                          <?php $cnt=1; ?>
                                          @foreach ($formEntries as $entry)
                                         


                                              <!-- ******** collapsible box ********** -->
                                                <div class="box box-default collapsed-box">
                                                <div class="box-header with-border">
                                                  <h3 class="box-title text-primary">{{$cnt}}. {{$entry['competency']}} <small id="evaluated-{{$entry['id']}}"><i class="fa fa-exclamation-circle text-yellow"></i></small> </h3>



                                                  <div class="box-tools pull-right">
                                                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                                                    </button>
                                                  </div>
                                                  <!-- /.box-tools -->
                                                </div>
                                                <!-- /.box-header -->
                                                <div class="box-body">

                                                  <table class="table">
                                                        <tr>
                                                          <td style="width:55%"><?php echo $entry['definitions'] ?>
                                                              <div class="clearfix"></div>
                                                              <?php $c=1 ?>
                                                              @foreach ($entry['attributes'] as $att)
                                                              <label>{{$att}}</label>
                                                              <textarea class="form-control" name="att_{{$entry['id']}}-{{$c}}" id="att_{{$entry['id']}}-{{$c}}"></textarea><br/><br/>
                                                              <?php $c++; ?>
                                                              @endforeach
                                                          </td>
                                                          <td class="text-center">
                                                            <h4 class="text-primary"> <!-- ( {{$entry['percentage'] }} ) --> 
                                                             
                                                            <?php $num =  ( $entry['percentage']*$ratingScale[0]->label ) / 100; echo number_format((($num/$maxScore)*100),2)  ?></h4>
                                                          </td>

                                                          <td class="text-center text-danger"><h4 id="score-{{$entry['id']}}" class="scores" value="0">0.0</h4></td>

                                                          <td>
                                                            <select class="form-control text-center" name="rating" id="rating-{{$entry['id']}}" data-entryID="{{$entry['id']}} ">
                                                              <option value="0" data-computedValue="0">Select</option>
                                                              @foreach ($ratingScale as $r)
                                                                <option value="{{$r->id}}" title="{{$entry['id']}}" data-computedValue="<?php echo ($entry['percentage']*$r->label)/100; ?>" data-maxscore="{{$entry['percentage']}}" class="text-center">{{$r->label}} </option>

                                                              @endforeach
                                                            </select>
                                                          </td>

                                                          
                                                          
                                                        </tr>

                                                  </table>
                                                </div>
                                                <!-- /.box-body -->
                                              </div>
                                              <!-- ******** end collapsible box ********** -->


                                         
                                              
                                            <?php $cnt++; ?>


                                         
                                          @endforeach
                                        
                                          
                                          
                              

                              

                              
                              </div>
                              <!-- /.tab-pane -->
                            
                            <div class="tab-pane" id="summary"><br/><br/>
                              <div class="box box-default">
                                <div class="box-header"><h4>Overall Performance Summary</h4></div>
                                <div class="box-body">
                                  <table class="table table-bordered">
                                    <?php $ctr=1; ?>
                                    @foreach ($summaries as $summary)
                                    <tr >
                                      <td colspan="2"><strong>{{$ctr}}.  {{$summary['header']}} </strong><br/>
                                        {{ $summary['details'] }}<br/><br/></td>
                                    </tr>
                                    @if ( $summary['columns'] !== null)
                                    <tr>
                                      @foreach ($summary['columns'] as $col)
                                      <th class="bg-gray text-center">{{$col->name}} </th>
                                      @endforeach
                                    </tr>
                                    <tr>
                                       @foreach ($summary['columns'] as $col)
                                      <td><textarea class="form-control" name="val_{{$ctr}}_{{$col->id}}" id="val_{{$ctr}}_{{$col->id}}"></textarea></td>
                                      
                                      @endforeach
                                    </tr>
                                    @endif

                                    @if ( $summary['rows'] !== null)
                                     @foreach ($summary['rows'] as $row)
                                    <tr>
                                     
                                      <th class="text-right" valign="middle">{{$row->name}} </th>
                                      <td><textarea class="form-control"  name="val_{{$ctr}}_{{$row->id}}" id="val_{{$ctr}}_{{$row->id}}"></textarea></td>

                                      
                                    </tr>@endforeach
                                    
                                    @endif
                                    
                                    
                                    <?php $ctr++; ?>
                                    @endforeach
                                  </table>

                                </div>
                              </div>
                             
                            </div>
                            <!-- /.tab-pane -->

                            <div class="tab-pane" id="details"><br/><br/>
                              <div class="box box-default">
                                <div class="box-header"><h4>Evaluation Guidelines</h4></div>
                                <div class="box-body">
                                  <h5>Below are the following guidelines for using the performance assessment tool: </h5>

                                   <!-- <table class="table" style="width:400px; margin: 0 auto;">
                                      <tr>
                                        <td colspan="2" class="text-center"><h4>Salary Increase Metrics</h4></td>
                                      </tr>
                                      <tr>
                                        <td><strong>Total Score</strong></td>
                                        <td class="text-center"><strong>Salary Increase</strong></td>
                                      </tr>
                                      <tr>
                                        <td>100 - 97.5</td>
                                        <td class="text-center">5%</td>
                                        
                                      </tr>
                                      <tr>
                                        <td>97.4 - 89.5</td>
                                        <td class="text-center">4%</td>
                                      </tr>

                                      <tr>
                                        <td>89.4 - 84.5</td>
                                        <td class="text-center">3%</td>
                                      </tr>

                                      <tr>
                                        <td>84.4 - 80.0</td>
                                        <td class="text-center">2%</td>
                                      </tr>

                                       <tr>
                                        <td>79.99 - 70.0</td>
                                        <td class="text-center">1%</td>
                                      </tr>

                                      <tr>
                                        <td>69.99 below</td>
                                        <td class="text-center">none</td>
                                      </tr>

                                    </table> --></div><br/>
                                 <h6><i><b>The company may in its discretion, grant a salary increase or a bonus equivalent to to 1% to 5% of the employee's monthly salary.  In case the current salary is already maximum pay for the campaign, only a bonus may be given.  A positive review  does not guarantee salary increases or bonus, nor does it imply continued employment</b></i></h6>
                                 <br/>

                                  <p>This Evaluation Form has a graduated scale from 5 to 1 with suggested qualitative definitions. Take your time, give thought to each performance factor and choose a numerical rating which best describes the employee's performance for each section (See second tab: Competencies Definition Guide Sheet). The rating range and definitions are as follows:</p>
                                  <p><br/>5 = Outstanding Performance - (Few employees will score consistently at this level) Overall performance is characterized by exceptionally high quality and quantity of work in the accomplishment of tasks and projects, all responsibilities and duties are conducted in a proper manner and he/she uses job related skills in a superior manner; aggressively seeks out and assumes responsibilities that are above and beyond the position requirements.</p>
                                  <p>4 = Exceeds Expectations - Overall performance is better than the average. Requires a degree of supervision that is typical for the position and experience level of the employee. All duties and responsibilities are conducted in a proper manner. Seeks out and assumes responsibilities which are beyond the job requirements. Efficiently uses time and resources in carrying out assignments.</p>
                                  <p>3 = Meets Expectations - Performance of most duties is adequate. Performs tasks that are requested but rarely undertakes work outside of his/her defined area. Requires some direction and review of major parts of assignments.</p>
                                  <p>2 = Improvement Desired - Performance of most duties is barely adequate. Hesitates to undertake work outside of his/her defined area. Requires direction and review of major parts of assignments.</p>
                                  <p>1 = Unsatisfactory Performance - Performance rarely meets minimum acceptable standards. Assignments are frequently done late and/or incompletely. Detailed direction and frequent progress checks are required. Performance is far too low and major improvement is needed. 

                                    <h4><br/><br/>The Coaching Process:<br/>
                                      PRESENTING THE PERFORMANCE EVALUATION TO THE EMPLOYEE</h4>
                                      <p>Present a copy of the evaluation to the employee. Make sure he/she understands both the purpose and method of the evaluation. Discuss each of the performance factors, the reason each score was given as well as the improvement required. Any objections or differences of opinion which the employee may have with any part of the evaluation are to be noted in the comments sections. Both the supervisor and the employee sign and date the form. An original and one copy are required. The original is placed in the employee's personal file and the employee receives a copy.</p>
                                      <br/><br/>
                               

                                </div>
                              </div>
                              
                             
                            </div>
                            <!-- /.tab-pane -->
                          </div>
                          <!-- /.tab-content -->

                          {{Form::close()}}

                        </div>
                        <!-- /.nav-tabs-custom -->

                         
                     </div><!--end box body-->
                </div><!--end box primary -->

            </div><!--end right pane-->



           
          </div><!-- end row -->

       
     </section>
          



@endsection


@section('footer-scripts')

<script>
  $.widget.bridge('uibutton', $.ui.button);
    // create the back to top button
$('body').prepend('<a href="#" title="Back to Top" class="back-to-top">Back to Top</a>');

// change the value with how many pixels scrolled down the button will appear
var amountScrolled = 200;

$(window).scroll(function() {
  if ( $(window).scrollTop() > amountScrolled ) {
    $('a.back-to-top').fadeIn('slow');
  } else {
    $('a.back-to-top').fadeOut('slow');
  }
});

$('a.back-to-top, a.simple-back-to-top').click(function() {
  $('html, body').animate({
    scrollTop: 0
  }, 700);
  return false;
});
</script>



<!-- Page script -->
<script>
      $(function () {
       'use strict';
        $('#how').qtip({ // Grab some elements to apply the tooltip to
        content: {
            text: 'To compute the transmuted score: <br/><br/>[ 100 - ( (100 - sum of weighted scores) x 0.5 ) ] = FINAL SCORE',
            title: "How did you come up with that score?"
        },
        position: {
            my: 'bottom right',  // Position my top left...
            at: 'top left', // at the bottom right of...
            target: $('#how') // my target
        }
    })

   $('.loader').hide();

   $('.submitBtn').on('click', function()
   {
      var notYetRated = $('.fa.fa-exclamation-circle.text-yellow').length;
      var isDraft = $(this).attr('data-isDraft');
      console.log('isDraft: '+ isDraft);

      if (isDraft != "1")
      {

        if (notYetRated >= 2){
          $('.loader').fadeIn();
          $('.loader').html('');
          $('.loader').html('<p><i class="fa fa-info-circle"></i><strong> Oops! You got ('+notYetRated+') competencies not yet rated.</strong> </p>');
           console.log("Rated: "+notYetRated);
           $('.loader').delay(2000).fadeOut(); return false;

        } else if  (notYetRated == 1){
          $('.loader').fadeIn();
          $('.loader').html('');
          $('.loader').html('<p><i class="fa fa-info-circle"></i><strong> Oops! You still got ('+notYetRated+') competency not yet rated.</strong> </p>');
           console.log("Rated: "+notYetRated);
           $('.loader').delay(2000).fadeOut(); return false;
         }
        

      } 


        $('.loader').fadeIn();
        $('.loader').html('');
        $('.loader').html('<p> <i class="fa fa-spinner fa-spin"></i> Saving {{$employee->firstname}}\'s Evaluation... </p>')
        $.ajax({
             url: "{{action('EvalFormController@store')}}",
             type: 'POST',
             data: {

                        "evalForm_id" : $('#evalForm_id').val(),
                        "coachingDone": false,
                        "total": $('#total').val(),
                        "salaryIncrease": $('#salaryIncrease').val(),
                        "isDraft": isDraft,
                        "isLead": {{$isLead}},
                        @foreach ($formEntries as $entry)
                        
                        "ratingScaleID_{{$entry['id']}}": $('select[id=rating-{{$entry["id"]}}] option:selected').val(),
                          <?php $c=1 ?>
                          @foreach ($entry['attributes'] as $att)
                          "attributeValue_{{$entry['id']}}_{{$c}}": $('#att_{{$entry["id"]}}-{{$c}}').val(),
                            
                            <?php $c++; ?>
                          @endforeach
                        

                        @endforeach


                         <?php $ctr=1; ?>
                         @foreach ($summaries as $summary)
                            @if ( $summary['columns'] !== null)
                              @foreach ($summary['columns'] as $col)
                               "val_{{$ctr}}_{{$col->id}}" : $("#val_{{$ctr}}_{{$col->id}}").val(),
                              @endforeach
                            @endif

                            @if ( $summary['rows'] !== null)
                              @foreach ($summary['rows'] as $row)
                              "val_{{$ctr}}_{{$row->id}}" : $("#val_{{$ctr}}_{{$row->id}}").val(),
                              @endforeach
                            @endif

                          <?php $ctr++; ?>
                          @endforeach




                        
                        "_token":"{{ csrf_token() }}"
                        
                    },
             success: function(response) {
              if (response.saved)
              {
                $('.loader').html('');
                $('.loader').html('<p><i class="fa fa-save"></i><strong> Evaluation Form Saved</strong> </p>');
                console.log(response);
                console.log("summary: "+ response.psummary);
                $('.loader').delay(1000).fadeOut(function() {
                  window.location = "{{ url('/') }}/evalForm/"+response.evalFormID;
                });

              //} else { $('.loader').html('<p><i class="fa fa-exclamation-circle"></i><strong> An error occured. Please Try again. </strong> </p>'); console.log("summary: "+ response.psummary);}
              } else { $('.loader').html('<p><i class="fa fa-exclamation-circle"></i><strong> An error occured. Please Try again!. </strong> </p>'); console.log(response);}
            }
        }); 

      //}//end else all rated
    });



   $('select[name="rating"]').change(function(){   

  

          var selval = $(this).find(':selected').attr('data-computedValue'); // $(this).val();
          var id = $(this).attr('data-entryID');

          if (selval == 0)
          {
            $("#evaluated-"+id).html('<i class="fa fa-exclamation-circle text-gray"></i>');
            $('#score-'+id).html("0%");
            $('#score-'+id).attr("value",'0.0');

            var overall = 0;

            var grades = $(".scores");

              for (var i=0; i< grades.length; i++){
                overall += parseFloat($(grades[i]).attr("value"));
                //console.log('add: '+ parseFloat($(grades[i]).attr("value")));
              }

              
              $('input#total').attr("value",overall.toPrecision(4));
               //var finalScore = overall.toPrecision(4);
              //$('input#total').attr("value",finalScore);
              $('#overallScore').html(overall.toPrecision(4)+"%");


          } else {
            var num = ($(this).find(':selected').attr('data-computedValue')/{{$maxScore}})*100; //($(this).val()/{{$maxScore}})*100;
            var overall = 0;

             $("#evaluated-"+id).html('<i class="fa fa-check text-green"></i>');


              $('#score-'+id).html(num.toPrecision(4)+"");
              $('#score-'+id).attr("value",num.toPrecision(4));
              console.log("num: "+num);

              var grades = $(".scores");

              for (var i=0; i< grades.length; i++){
                overall += parseFloat($(grades[i]).attr("value"));
                console.log('add: '+ parseFloat($(grades[i]).attr("value")));
              }

              
              

              var trueGrade = 100-((100-overall)*0.5);
              var finalScore = overall.toPrecision(4);

              $('#overallScore').html(trueGrade.toPrecision(4)+"%"); 
            
             $('input#total').attr("value",trueGrade.toPrecision(4));
             console.log("TRUE GRADE: "+trueGrade.toPrecision(4));
              

          }
          <?php $idxctr = 1; ?>
          @foreach ($ratingScale as $rs)

           @if( $idxctr == count($ratingScale))
            
            if ( trueGrade.toPrecision(4) <= {{$rs->maxRange}} ){

           @else

            if (trueGrade.toPrecision(4) <= {{$rs->maxRange}} && trueGrade.toPrecision(4)>{{$rs->maxRange - $ratingScale[$idxctr]->maxRange }}){
              $("#descriptions").html('<div class="clearfix"></div><span style="font-size:0.6em;" class="text-gray">{{$rs->status}}  <i class="fa {{$rs->icon}} "></i> </span>');
              

           @endif

            
            $('.ratingTable').removeClass('bg-green');
            $("#rating-"+{{$rs->percentage}} ).addClass('bg-green');

             /* -- new ratings by Finance -- */

                // if ( (trueGrade.toPrecision(4) <= 101) && (trueGrade.toPrecision(4) >= 97.5)){
                //   $("#overallScore").append('<br/><span style="font-size:0.4em" class="text-danger"><i class="fa fa-money"></i> Salary Increase: <strong>5 %</strong></span><br/><span style="font-size:0.6em;" class="text-gray">{{$rs->status}}  <i class="fa {{$rs->icon}} "></i> </span>');
                //   $('input#salaryIncrease').attr("value",5);

                // } else if ( (trueGrade.toPrecision(4) <= 97.4) && (trueGrade.toPrecision(4) >= 89.5) ){ 
                //   $("#overallScore").append('<br/><span style="font-size:0.4em" class="text-danger"><i class="fa fa-money"></i> Salary Increase: <strong>4 %</strong></span><br/><span style="font-size:0.6em;" class="text-gray">{{$rs->status}}  <i class="fa {{$rs->icon}} "></i> </span>');
                //   $('input#salaryIncrease').attr("value",4);

                // } else if ( trueGrade.toPrecision(4) <= 89.4 && trueGrade.toPrecision(4) >= 84.5 ){
                //   $("#overallScore").append('<br/><span style="font-size:0.4em" class="text-danger"><i class="fa fa-money"></i> Salary Increase: <strong>3 %</strong></span><br/><span style="font-size:0.6em;" class="text-gray">{{$rs->status}}  <i class="fa {{$rs->icon}} "></i> </span>');
                //   $('input#salaryIncrease').attr("value",3);

                // } else if ( trueGrade.toPrecision(4) <= 84.4 && trueGrade.toPrecision(4) >= 80 ){
                //   $("#overallScore").append('<br/><span style="font-size:0.4em" class="text-danger"><i class="fa fa-money"></i> Salary Increase: <strong>2 %</strong></span><br/><span style="font-size:0.6em;" class="text-gray">{{$rs->status}}  <i class="fa {{$rs->icon}} "></i> </span>');
                //   $('input#salaryIncrease').attr("value",2);

                // } else{
                //   $("#overallScore").append('<br/><span style="font-size:0.4em" class="text-gray"><i class="fa fa-money"></i> Salary Increase: <strong>none</strong></span><br/><span style="font-size:0.6em;" class="text-gray">{{$rs->status}}  <i class="fa {{$rs->icon}} "></i> </span>');
                //   $('input#salaryIncrease').attr("value",0);

                // }

                        

          }
           <?php $idxctr++;?>

          @endforeach

          @if ($evalType->id !== 3) <!-- REGULARIZATION -->

          if ( (trueGrade.toPrecision(4) <= 101) && (trueGrade.toPrecision(4) >= 97.50)){
                  /*$("#descriptions").append('<br/><span style="font-size:0.4em" class="text-danger"><i class="fa fa-money"></i> Salary Increase: <strong>5 %</strong></span>');*/
                  $('input#salaryIncrease').attr("value",5);

          } else if ( (trueGrade.toPrecision(4) <= 97.49) && (trueGrade.toPrecision(4) >= 89.50) ){ 
                  // $("#descriptions").append('<br/><span style="font-size:0.4em" class="text-danger"><i class="fa fa-money"></i> Salary Increase: <strong>4 %</strong></span>');
                  $('input#salaryIncrease').attr("value",4);

                } else if ( trueGrade.toPrecision(4) <= 89.49 && trueGrade.toPrecision(4) >= 84.50 ){
                  // $("#descriptions").append('<br/><span style="font-size:0.4em" class="text-danger"><i class="fa fa-money"></i> Salary Increase: <strong>3 %</strong></span>');
                  $('input#salaryIncrease').attr("value",3);

                } else if ( trueGrade.toPrecision(4) <= 84.49 && trueGrade.toPrecision(4) >= 80 ){
                  // $("#descriptions").append('<br/><span style="font-size:0.4em" class="text-danger"><i class="fa fa-money"></i> Salary Increase: <strong>2 %</strong></span>');
                  $('input#salaryIncrease').attr("value",2);

                 } else if ( trueGrade.toPrecision(4) <= 79.99 && trueGrade.toPrecision(4) >= 70 ){
                  // $("#descriptions").append('<br/><span style="font-size:0.4em" class="text-danger"><i class="fa fa-money"></i> Salary Increase: <strong>1 %</strong></span>');
                  $('input#salaryIncrease').attr("value",2);


          } else {
            // $("#descriptions").append('<br/><span style="font-size:0.4em" class="text-gray"><i class="fa fa-money"></i> Salary Increase: <strong>none</strong></span>');
             $('input#salaryIncrease').attr("value",0);


          }

          @else

          if ( (trueGrade.toPrecision(4) <= 101) && (trueGrade.toPrecision(4) >= 97.50)){
                  // $("#descriptions").append('<br/><span style="font-size:0.4em" class="text-danger"><i class="fa fa-money"></i> Salary Increase: <strong>* N/A *</strong></span>');
                  $('input#salaryIncrease').attr("value",5);

          } else if ( (trueGrade.toPrecision(4) <= 97.49) && (trueGrade.toPrecision(4) >= 89.50) ){ 
                  // $("#descriptions").append('<br/><span style="font-size:0.4em" class="text-danger"><i class="fa fa-money"></i> Salary Increase: <strong>* N/A *</strong></span>');
                  $('input#salaryIncrease').attr("value",4);

                } else if ( trueGrade.toPrecision(4) <= 89.49 && trueGrade.toPrecision(4) >= 84.50 ){
                  // $("#descriptions").append('<br/><span style="font-size:0.4em" class="text-danger"><i class="fa fa-money"></i> Salary Increase: <strong>* N/A *</strong></span>');
                  $('input#salaryIncrease').attr("value",3);

                } else if ( trueGrade.toPrecision(4) <= 84.49 && trueGrade.toPrecision(4) >= 80 ){
                  // $("#descriptions").append('<br/><span style="font-size:0.4em" class="text-danger"><i class="fa fa-money"></i> Salary Increase: <strong>* N/A *</strong></span>');
                  $('input#salaryIncrease').attr("value",2);

                 } else if ( trueGrade.toPrecision(4) <= 79.99 && trueGrade.toPrecision(4) >= 70 ){
                  // $("#descriptions").append('<br/><span style="font-size:0.4em" class="text-danger"><i class="fa fa-money"></i> Salary Increase: <strong>* N/A *</strong></span>');
                  $('input#salaryIncrease').attr("value",2);


          } else {
            // $("#descriptions").append('<br/><span style="font-size:0.4em" class="text-gray"><i class="fa fa-money"></i> Salary Increase: <strong>* N/A *</strong></span>');
             $('input#salaryIncrease').attr("value",0);


          }


          @endif

          




         
          console.log("total transmuted grade: "+trueGrade.toPrecision(4));
          console.log("total final score: "+finalScore);
              
             
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
<?php /*
<script type="text/javascript" src="{{asset('public/js/dashboard.js')}}"></script> */ ?>

@stop