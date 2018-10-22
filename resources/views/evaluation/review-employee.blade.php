@extends('layouts.main')

@section('metatags')
<title>Review Evaluation | OAMPI Evaluation System</title>
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
        <small>Period:<strong> {{$startPeriod->format('M d')}} to  {{$endPeriod->format('M d, Y')}}  </strong> </small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Review Evaluation</li>
      </ol>
    </section>

     <section class="content">

          <div class="row">
            
            <div class="col-lg-4 col-sm-5 col-xs-12">
               <!-- Widget: user widget style 1 -->
                          <div class="box box-widget widget-user-2">
                            <!-- Add the bg color to the header using any of the bg-* classes -->
                            
                            <div class="widget-user-header  @if ($doneEval[$employee->id]['evaluated'] && ($employee->userType_id == '3') ) bg-gray @elseif ($doneEval[$employee->id]['evaluated'] && ($employee->userType_id != '3') ) bg-darkgreen @elseif (!$doneEval[$employee->id]['evaluated'] && ($employee->userType_id != '3') )bg-green  @else bg-black @endif">

                              <div class="widget-user-image">
                                @if ( file_exists('public/img/employees/'.$employee->id.'.jpg') )
                                <img src="{{asset('public/img/employees/'.$employee->id.'.jpg')}}" class="img-circle" alt="User Image">
                                @else
                                  <img src="{{asset('public/img/useravatar.png')}}" class="user-image" alt="Employee Image">

                                  @endif
                               
                               
                                
                              </div>
                              <!-- /.widget-user-image -->
                              <h3 class="widget-user-username"> {{$employee->lastname }}, {{$employee->firstname}} </h3>
                              <h5 class="widget-user-desc">{{$employee->position->name}}<br/>
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

                               
                                <!-- <li><a href="#">
                                  <span class="pull-left">Previous Evaluation <br/>
                                    <small>(date of Prev eval goes here)</small></span><h3 class="pull-right text-danger">N/A</h3></a></li> -->

                               
                                <li><a href="#">
                                  <span class="pull-left">Date Evaluated <br/>
                                    <small>{{$evalForm->created_at}} </small></span> </a>
                                    <h5 class="pull-right"> by: {{$evaluator->firstname}} {{$evaluator->lastname}} &nbsp;&nbsp;</h5>

                                  </li>


                               
                                
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
                                    <th class="text-center">%</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Description</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  @foreach ($ratingScale as $rs)
                                  <tr id="rating-{{$rs->percentage}}" class="ratingTable" data-salaryIncrease="{{$rs->increase}}">
                                    <td><h4>{{$rs->label}}</h4></td>
                                    <td class="text-center">{{$rs->maxRange}}</td>
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
                             <h1 style="line-height:0.8" class="text-danger pull-right text-right" id="overallScore"  data-score="{{$evalForm->overAllScore}}"><br/><span style="font-size:0.6em;" class="text-gray"></h1>
                            <div class="clearfix"></div>
                             <h1 style="line-height:0.8; margin-top:-10px" class="text-danger pull-right text-right" id="descriptions"></h1>

                             <p class="pull-right" style="border:2px dotted #fff; padding:10px; background-color:#f39c12; font-weight:bold; font-size:0.9em; color:#fff"><em>I've already reviewed my evaluation, and understand that by clicking the button below will save all of my feedback and affix my digital signature to this evaluation.</em><br/>
                             <a id="reviewBtn" href="#" class="btn btn-md btn-default pull-right" style="margin-top:5px"><i class="fa fa-check"></i> Affix Digital Signature </a></p><div class="clearfix"></div> <!-- onclick="window.print();return false;"  -->
                             
                       
                          </div>
                        </div>


                        <!-- <h1 style="margin-left:2em; line-height:0.8" class="text-danger pull-right text-right" id="overallScore" data-score="{{$evalForm->overAllScore}}"></h1>
                       

                        <h5><strong>Instructions:</strong></h5>
                        <p> This Evaluation Form has a graduated scale from 5 to 1 with suggested qualitative definitions. Take your time, give thought to each performance factor and choose a numerical rating which best describes the employee's performance for each section 
                           Refer to the Grading Scale table for the rating range and definitions;</p> -->
                           
                        @if (Auth::user()->userType_id !== 4 && $allowed)
                        <div class="clearfix"></div>
                        <a href="{{action('EvalFormController@edit', $evalForm->id)}} " id="submitBtn" class="btn btn-sm btn-default pull-right" style="margin-bottom:20px"><i class="fa fa-pencil"></i> Modify Evaluation</a>
                        @endif

                        <div class="loader callout callout-info col-xs-12 pull-right" style="text-align:center"><p><i class="fa fa-save"></i><strong> Saving Evaluation Form...</strong> <i class="fa fa-spinner fa-spin"></i></p></div>
                        <br/><br/>

                        <div class="clearfix"></div>

                        <div class="nav-tabs-custom">
                          <ul class="nav nav-tabs">
                            <li class="active bg-gray"><a href="#evalform" data-toggle="tab"><strong>Competency Evaluation</strong></a></li>
                            <li class="bg-gray"><a href="#summary" data-toggle="tab" style="font-weight:bold">Performance Summary</a></li>
                            <li class="bg-gray"><a href="#details" data-toggle="tab" style="font-weight:bold">Guidelines</a></li> 
                          </ul>

                          {{ Form::open(['route' => 'evalForm.store', 'class'=>'col-lg-12', 'id'=> 'saveEval' ]) }}
                          <input type="hidden" name="evalForm_id" id="evalForm_id" value="{{$evalForm->id}}" />
                          <input type="hidden" name="salaryIncrease" id="salaryIncrease" value="0" />
                          


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
                                          <?php $cnt=0; $printed=0; ?>
                                          @foreach ($formEntries as $entry)

                                             @if ($cnt+1 >= count($formEntries)) <!--last entry-->

                                             @else


                                                @if ($entry['competency'] == $formEntries[$cnt+1]['competency'])
                                                <!-- ******** collapsible box ********** -->
                                                      <div class="box box-default ">
                                                      <div class="box-header with-border">
                                                        <h3 class="box-title text-primary">{{$entry['competency']}} </h3>



                                                        <div class="box-tools pull-right">
                                                          <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
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
                                                                   
                                                                    
                                                                    <label>{{$entry['attribute']}}</label>
                                                                    <textarea class="form-control" disabled="disabled" style="height:300px">{{$entry['value']}} </textarea><br/><br/>

                                                                     <label>{{$formEntries[$cnt+1]['attribute']}}</label>
                                                                    <textarea class="form-control" name="att_{{$entry['detailID']+1}}" id="att_{{$entry['detailID']+1}}" >{{$formEntries[$cnt+1]['value']}} </textarea><br/><br/>
                                                                    
                                                                   
                                                                </td>
                                                                <td class="text-center">
                                                                  <h4 class="text-primary"><?php $num =  ( $entry['percentage']*$ratingScale[0]->label ) / 100; echo number_format((($num/$maxScore)*100),2)  ?></h4>
                                                                </td>
                                                                @if ($employee->userType_id == 4 )
                                                                <td class="text-center "><h4 class="scores" value="0"><?php echo  ( number_format(((($entry['percentage']*$entry['rating']->label)/100)/2.65)*100,2) );?></h4></td>
                                                                @else

                                                                <td class="text-center "><h4 class="scores" value="0"><?php echo  ( number_format(((($entry['percentage']*$entry['rating']->label)/100)/5)*100,2) );?></h4></td>


                                                                @endif
                                                                

                                                               

                                                                <td class="text-center text-danger"><h3>{{$entry['rating']->label}}</h3>
                                                                  
                                                                </td>

                                                                
                                                                
                                                              </tr>

                                                        </table>
                                                      </div>
                                                      <!-- /.box-body -->
                                                    </div>
                                                    <!-- ******** end collapsible box ********** -->

                                                @else

                                                  @if ($cnt+1 >= count($formEntries)) <!--last entry-->

                                                  @else

                                                  @endif


                                                    

                                                @endif

                                                   


                                               
                                                    
                                                  <?php $cnt++;  ?>


                                            @endif
                                         
                                          @endforeach
                                        
                                          
                                          
                              

                              

                              
                              </div>
                              <!-- /.tab-pane -->
                            
                            <div class="tab-pane" id="summary"><br/><br/>
                              <div class="box box-default">
                                <div class="box-header"><h4>Overall Performance Summary</h4></div>
                                <div class="box-body">
                                  <table class="table table-bordered">
                                    <?php $ctr=1; $indexCtr=0; ?>
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
                                      <td><textarea class="form-control" name="val_{{$ctr}}_{{$col->id}}" id="val_{{$ctr}}_{{$col->id}}" disabled="disabled">{{$performanceSummary[$indexCtr]}} </textarea></td>
                                      <?php $indexCtr++;?>
                                      
                                      @endforeach
                                    </tr>
                                    @endif

                                    @if ( $summary['rows'] !== null)
                                     @foreach ($summary['rows'] as $row)
                                    <tr>
                                     
                                      <th class="text-right" valign="middle">{{$row->name}} </th>
                                      <td><textarea class="form-control"  name="val_{{$ctr}}_{{$row->id}}" id="val_{{$ctr}}_{{$row->id}}" disabled="disabled">{{$performanceSummary[$indexCtr]}}</textarea></td>
                                      <?php $indexCtr++;?>

                                      
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


                                <table class="table" style="width:400px; margin: 0 auto;">
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

                                </table><br/>

                                <h6><i><b>The company may in its discretion, grant a salary increase or a bonus equivalent to to 1% to 5% of the employee's monthly salary.  In case the current salary is already maximum pay for the campaign, only a bonus may be given.  A positive review  does not guarantee salary increases or bonus, nor does it imply continued employment</b></i></h6>
                                 <br/>

                                  <p>This Evaluation Form has a graduated scale from 5 to 1 with suggested qualitative definitions. Take your time, give thought to each performance factor and choose a numerical rating which best describes the employee's performance for each section (See second tab: Competencies Definition Guide Sheet). The rating range and definitions are as follows:</p>
                                  <p><br/>
5 = Outstanding Performance - (Few employees will score consistently at this level) Overall performance is characterized by exceptionally high quality and quantity of work in the accomplishment of tasks and projects, all responsibilities and duties are conducted in a proper manner and he/she uses job related skills in a superior manner; aggressively seeks out and assumes responsibilities that are above and beyond the position requirements.</p>
<hr/>
<p>4 = Exceeds Expectations - Overall performance is better than the average. Requires a degree of supervision that is typical for the position and experience level of the employee. All duties and responsibilities are conducted in a proper manner. Seeks out and assumes responsibilities which are beyond the job requirements. Efficiently uses time and resources in carrying out assignments.</p>
<hr/><p>3 = Meets Expectations - Performance of most duties is adequate. Performs tasks that are requested but rarely undertakes work outside of his/her defined area. Requires some direction and review of major parts of assignments.</p>
<hr/><p>2 = Improvement Desired - Performance of most duties is barely adequate. Hesitates to undertake work outside of his/her defined area. Requires direction and review of major parts of assignments.</p>
<hr/><p>1 = Unsatisfactory Performance - Performance rarely meets minimum acceptable standards. Assignments are frequently done late and/or incompletely. Detailed direction and frequent progress checks are required. Performance is far too low and major improvement is needed. 
<hr/><br/><br/>

<h4>The Coaching Process:<br/>
 PRESENTING THE PERFORMANCE EVALUATION TO THE EMPLOYEE</h4>

 <p>Present a copy of the evaluation to the employee. Make sure he/she understands both the purpose and method of the evaluation. Discuss each of the performance factors, the reason each score was given as well as the improvement required. Any objections or differences of opinion which the employee may have with any part of the evaluation are to be noted in the comments sections. Both the supervisor and the employee sign and date the form. An original and one copy are required. The original is placed in the employee's personal file and the employee receives a copy.</p>
                                
                                

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

  $('.loader').hide();
   $('#reviewBtn').on('click', function(e){
    
     $('.loader').fadeIn();
      $('.loader').html('');
      $.ajax({
           url: "{{action('EvalFormController@updateReview', $evalForm->id)}}",
           type: 'POST',
           data: {

                      "id" : "{{$evalForm->id}}",
                      "coachingDone": true,
                     
                       
                      @foreach ($formEntries as $entry)

                      //if odd numbered id, start group rating
                      //else if even, just skip it


                         "att_{{$entry['detailID']}}": $('#att_{{$entry["detailID"]}}').val(),
                       
                       
                      @endforeach

                      
                      "_token":"{{ csrf_token() }}"
                      
                  },
            


           success: function(response) {
            
              $('.loader').html('<p><i class="fa fa-save"></i><strong> Evaluation Form Saved</strong> </p>');
              console.log("summary: ");
              console.log(response);
              $('.loader').delay(3000).fadeOut(function() {
                window.location = "{{action('EvalFormController@show', $evalForm->id)}}";
              });

            
          }
      }); e.preventDefault(); e.stopPropagation();




   }); //end on submit



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

var rating = parseFloat($('#overallScore').attr('data-score'));
   var trueGrade = 100-((100-rating)*0.5);


   //$('#overallScore').html(trueGrade+' % ');
   $('#overallScore').html(rating+' % ');
 <?php $idxctr = 1; ?>
   @foreach ($ratingScale as $rs)

   @if( $idxctr == count($ratingScale))
      
      if ( rating.toPrecision(4) <= {{$rs->maxRange}} ){

     @else

      if (rating.toPrecision(4) <= {{$rs->maxRange}} && rating.toPrecision(4)>{{$rs->maxRange - $ratingScale[$idxctr]->maxRange }}){
        $("#descriptions").html('<div class="clearfix"></div><span style="font-size:0.6em;" class="text-gray">{{$rs->status}}  <i class="fa {{$rs->icon}} "></i> </span>');
        

     @endif




    //if (rating.toPrecision(4) <= (100-((100-{{$rs->maxScore}})*0.5)) && rating.toPrecision(4) > (100-((100-{{$rs->percentage}})*0.5))-10 ){
   
      
      $('.ratingTable').removeClass('bg-green');
      $("#rating-"+{{$rs->percentage}} ).addClass('bg-green');

      
          
           /* -- new ratings by Finance -- */

          if (rating <= 101 && rating >= 97.5){
            $("#descriptions").append('<br/><span style="font-size:0.4em" class="text-danger"><i class="fa fa-money"></i> Salary Increase: <strong>5 %</strong></span>');
            $('input#salaryIncrease').attr("value",5);

          } else if ( rating <= 97.49 && rating >= 89.50 ){ 
            $("#descriptions").append('<br/><span style="font-size:0.4em" class="text-danger"><i class="fa fa-money"></i> Salary Increase: <strong>4 %</strong></span>');
            $('input#salaryIncrease').attr("value",4);

          } else if ( rating <= 89.49 && rating >= 84.50 ){
            $("#descriptions").append('<br/><span style="font-size:0.4em" class="text-danger"><i class="fa fa-money"></i> Salary Increase: <strong>3 %</strong></span>');
            $('input#salaryIncrease').attr("value",3);

          } else if ( rating <= 84.49 && rating >= 80 ){
            $("#descriptions").append('<br/><span style="font-size:0.4em" class="text-danger"><i class="fa fa-money"></i> Salary Increase: <strong>2 %</strong></span>');
            $('input#salaryIncrease').attr("value",2);

          } else if ( rating <= 79.99 && rating >= 70 ){
                  $("#descriptions").append('<br/><span style="font-size:0.4em" class="text-danger"><i class="fa fa-money"></i> Salary Increase: <strong>1 %</strong></span>');
                  $('input#salaryIncrease').attr("value",2);

          } else{
            $("#descriptions").append('<br/><span style="font-size:0.4em" class="text-gray"><i class="fa fa-money"></i> Salary Increase: <strong>none</strong></span>');
            $('input#salaryIncrease').attr("value",0);

          }

        
        

    } 



    <?php $idxctr++;?>

    @endforeach

   

      
      
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