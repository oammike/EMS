@extends('layouts.main')

@section('metatags')
<title>Modify Evaluation | OAMPI Evaluation System</title>
@endsection

@section('bodyClasses')
sidebar-collapse
@endsection

@section('content')


  <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
         {{$evalType->name}} Evaluation
        <small>Period:<strong> {{$startPeriod->format('M d')}} to  {{$endPeriod->format('M d, Y')}}  </strong> </small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Modify Evaluation</li>
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
                                <li><a href="#">Issued Disciplinary Actions <span class="pull-right badge bg-red">N/A</span></a></li>

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
                        <!-- h1 style="margin-left:2em; line-height:0.8" class="text-danger pull-right text-right" id="overallScore" data-value="{{$evalForm->overAllScore}}"></h1>
                       

                        <h5><strong>Instructions:</strong></h5>
                        <p> This Evaluation Form has a graduated scale from 5 to 1 with suggested qualitative definitions. Take your time, give thought to each performance factor and choose a numerical rating which best describes the employee's performance for each section 
                           Refer to the Grading Scale table for the rating range and definitions;</p>
                           <div class="clearfix"></div> -->

                        <div class="row">
                          <div class="col-lg-8">
                             <h5><strong>Instructions:</strong></h5>
                            <p> This Evaluation Form has a graduated scale from 5 to 1 with suggested qualitative definitions. Take your time, give thought to each performance factor and choose a numerical rating which best describes the employee's performance for each section 
                           Refer to the Grading Scale table for the rating range and definitions;</p>

                          </div>
                          <div class="col-lg-4">
                             <a class="pull-right text-yellow" id="how"><i class="fa fa-question-circle"></i></a>
                             <h1 style="line-height:0.8" class="text-danger pull-right text-right" id="overallScore"  data-value="{{$evalForm->overAllScore}}"><br/><span style="font-size:0.6em;" class="text-gray"></h1>
                             <div class="clearfix"></div>
                             <h1 style="line-height:0.8; margin-top:-10px" class="text-danger pull-right text-right" id="descriptions"></h1>
                       
                          </div>
                        </div>



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
                                         

                                           <?php $cnt=0; $printed=0; ?>
                                          @foreach ($formEntries as $entry)

                                             @if ($cnt+1 >= count($formEntries)) <!--last entry-->

                                             @else


                                                @if ($entry['competency'] == $formEntries[$cnt+1]['competency'])
                                         


                                              <!-- ******** collapsible box ********** -->
                                                <div class="box box-default collapsed-box">
                                                <div class="box-header with-border">
                                                  <h3 class="box-title text-primary">{{$entry['competency']}} <small id="evaluated-{{$entry['id']}}"><i class="fa fa-exclamation-circle text-yellow"></i></small> </h3>



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
                                                                  @if ( ($c % 2) == 0)
                                                                  <textarea class="form-control" name="att_{{$entry['id']}}-{{$c}}" id="att_{{$entry['id']}}-{{$c}}" data-detailID="{{$entry['detailID']+1}}">{{$formEntries[$cnt+1]['value']}}</textarea><br/><br/> <!--id="att_{{$entry['id']}}-{{$c}}"-->

                                                                  @else
                                                                  <textarea class="form-control" name="att_{{$entry['id']}}-{{$c}}" id="att_{{$entry['id']}}-{{$c}}" data-detailID="{{$entry['detailID']}}">{{$entry['value']}}</textarea><br/><br/>

                                                                  @endif
                                                              
                                                              <?php $c++; ?>
                                                              @endforeach
                                                          </td>
                                                          <td class="text-center">
                                                            <h4 class="text-primary"><?php $num =  ( $entry['percentage']*$ratingScale[0]->label ) / 100; echo number_format((($num/$maxScore)*100),2)  ?></h4>
                                                          </td>

                                                           @if ($employee->userType_id == 4 )
                                                                <td class="text-center "><h4 class="scores" id="score-{{$entry['id']}}" value="<?php echo  ( number_format(((($entry['percentage']*$entry['rating']->label)/100)/2.65)*100,2) );?>"><?php echo  ( number_format(((($entry['percentage']*$entry['rating']->label)/100)/2.65)*100,2) );?></h4></td>
                                                                @else

                                                                <td class="text-center "><h4 class="scores" id="score-{{$entry['id']}}" value="<?php echo  ( number_format(((($entry['percentage']*$entry['rating']->label)/100)/5)*100,2) );?>"><?php echo  ( number_format(((($entry['percentage']*$entry['rating']->label)/100)/5)*100,2) );?></h4></td>


                                                                @endif

                                                         

                                                          <td>
                                                            <select class="form-control text-center" name="rating" id="rating-{{$entry['id']}}" data-entryID="{{$entry['id']}}" data-detailID="{{$entry['detailID']}}" data-commentBox="att_{{$entry['id']}}-1">
                                                             <!--  <option value="0" data-computedValue="0">Select</option> -->
                                                              @foreach ($ratingScale as $r)
                                                                <option value="{{$r->id}}" title="{{$entry['id']}}" data-computedValue="<?php echo ($entry['percentage']*$r->label)/100; ?>" data-maxscore="{{$entry['percentage']}}" class="text-center" @if ($entry['rating']->label == $r->label) selected="selected" @endif>{{$r->label}} </option>

                                                              @endforeach
                                                            </select>
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
                                    <?php $ctr=1; $indexCtr=0;?>
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
                                      <td><textarea class="form-control" name="val_{{$ctr}}_{{$col->id}}" id="val_{{$ctr}}_{{$col->id}}" data-summaryID="{{$performanceSummary[$indexCtr]['id']}}">{{$performanceSummary[$indexCtr]['value']}} </textarea></td>
                                        <?php $indexCtr++;?>
                                      @endforeach
                                    </tr>
                                    @endif

                                    @if ( $summary['rows'] !== null)
                                     @foreach ($summary['rows'] as $row)
                                    <tr>
                                     
                                      <th class="text-right" valign="middle">{{$row->name}} </th>
                                      <td><textarea class="form-control"  name="val_{{$ctr}}_{{$row->id}}" id="val_{{$ctr}}_{{$row->id}}" data-summaryID="{{$performanceSummary[$indexCtr]['id']}}">{{$performanceSummary[$indexCtr]['value']}}</textarea></td>

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

                                 
                                <br/><br/>

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
</script>



<!-- Page script -->
<script>
  $(function () {
   'use strict';

  

   // -------- recalculate();
var rating = parseFloat($('#overallScore').attr('data-value'));
   var trueGrade = rating; // 100-((100-rating)*0.5);
   console.log("Rating: "+rating+'; overtransmuted: '+trueGrade);

   //$('#overallScore').html(trueGrade+' % ');
   $('#overallScore').html(rating+' % ');

 


 <?php $idxctr = 0; ?>
    @foreach ($ratingScale as $rs)

     @if( $idxctr == count($ratingScale))
      
      if ( trueGrade.toPrecision(4) <= {{$rs->maxRange}} ){

     @else

      if (trueGrade.toPrecision(4) <= {{$rs->maxRange}} && trueGrade.toPrecision(4)>{{$rs->maxRange - $ratingScale[$idxctr]->maxRange }}){
        $("#descriptions").html('<span style="font-size:0.6em;" class="text-gray">{{$rs->status}}  <i class="fa {{$rs->icon}} "></i> </span>');
        

     @endif

      
      $('.ratingTable').removeClass('bg-green');
      $("#rating-"+{{$rs->percentage}}).addClass('bg-green');


                  

    }
     <?php $idxctr++;?>

    @endforeach

    if ( (trueGrade.toPrecision(4) <= 101) && (trueGrade.toPrecision(4) >= 97.50)){
            // $("#descriptions").append('<br/><span style="font-size:0.4em" class="text-danger"><i class="fa fa-money"></i> Salary Increase: <strong>5 %</strong></span>');
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


   // -------- end recalculate

   $('.loader').hide();

   $('.submitBtn').on('click', function()
   {
     var initVal = $('#total').val();
     var isDraft = $(this).attr('data-isDraft');

     if ( initVal == 0) {$('#total').attr('value',"{{$evalForm->overAllScore}}");}
     
      var increasesal =  $('#salaryIncrease').val();
      console.log("ang increase: " + increasesal);


      //-- check required comments
      var mgareq = $('textarea').filter('[required]');
          var ectr=0;
              $.each(mgareq,function(i,val){

                if($(this).val() == "") ectr++;
                console.log("i: ");
                console.log($(this).val());
                
              });

          if (ectr > 0)
          {
              $('.loader').fadeIn();
              $('.loader').html('');
              $('.loader').html('<p><i class="fa fa-info-circle"></i><strong> Oops! You still have empty comment boxes to justify your selected rating. </strong><br/>Kindly fill them out first before submitting to HR. Thank you! </p>');
               //console.log("Rated: "+notYetRated);
               $('.loader').delay(8000).fadeOut(); return false;

          }
      //------- end check required comments
    
      $('.loader').fadeIn();
      $('.loader').html('');
      $.ajax({
           url: "{{action('EvalFormController@update', $evalForm->id)}}",
           type: 'PUT',
           data: {

                      "id" : "{{$evalForm->id}}",
                      "coachingDone": false,
                      "overAllScore": $('#total').val(),
                      "salaryIncrease": $('#salaryIncrease').val(),
                      "isDraft": isDraft,
                       <?php $counter = 1; ?>
                      @foreach ($formEntries as $entry)

                      //if odd numbered id, start group rating
                      //else if even, just skip it

                      @if ($counter % 2 != 0)
                      <!-- ( $entry['detailID'] % 2 != 0)-->
                      "ratingScaleID_{{$entry['id']}}": $('select[id=rating-{{$entry["id"]}}] option:selected').val(),
                      "rating_{{$entry['detailID']}}": $('select[id=rating-{{$entry["id"]}}] option:selected').val(),
                      <?php $c=1 ?>
                      @foreach ($entry['attributes'] as $att)
                        @if ($c >1)
                        "att_{{$entry['detailID']+$c-1}}": $('#att_{{$entry["id"]}}-{{$c}}').val(),
                        "attributeValue_{{$entry['id']}}_{{$c}}": $('#att_{{$entry["id"]}}-{{$c}}').val(),

                        @else
                         "attributeValue_{{$entry['id']}}_{{$c}}": $('#att_{{$entry["id"]}}-{{$c}}').val(),
                         "att_{{$entry['detailID']}}": $('#att_{{$entry["id"]}}-{{$c}}').val(),
                         @endif
                              
                              <?php $c++; ?>
                      @endforeach

                      <?php $counter++; ?>
                      @else 
                          //do nothing
                          <?php $counter++; ?>


                      @endif
                     

              
                       
                      @endforeach


                       <?php $ctr=1; ?>
                       @foreach ($summaries as $summary)
                          @if ( $summary['columns'] !== null)
                            @foreach ($summary['columns'] as $col)
                             "val_{{$ctr}}_{{$col->id}}" : $("#val_{{$ctr}}_{{$col->id}}").val(),
                             "id_{{$ctr}}_{{$col->id}}" : $("#val_{{$ctr}}_{{$col->id}}").attr('data-summaryID'),
                             
                            @endforeach
                          @endif

                          @if ( $summary['rows'] !== null)
                            @foreach ($summary['rows'] as $row)
                            "val_{{$ctr}}_{{$row->id}}" : $("#val_{{$ctr}}_{{$row->id}}").val(),
                            "id_{{$ctr}}_{{$row->id}}" : $("#val_{{$ctr}}_{{$row->id}}").attr('data-summaryID'),
                            
                            @endforeach
                          @endif

                        <?php $ctr++; ?>
                        @endforeach






                      
                      "_token":"{{ csrf_token() }}"
                      
                  },
            // error: function(response){
            //   $('.loader').html('<p><i class="fa fa-exclamation-circle"></i><strong> An error occured. Please Try again. </strong> </p>'); console.log(response);
            // },

           success: function(response) {
            
              $('.loader').html('<p><i class="fa fa-save"></i><strong> Evaluation Form Saved</strong> </p>');
              console.log("summary: ");
              console.log(response);
              $('.loader').delay(3000).fadeOut(function() {
                window.location = "{{action('EvalFormController@show', $evalForm->id)}}";
              });

            
          }
      });
    }); //end on submit



   $('select[name="rating"]').change(function()
   {   

      var selval = $(this).find(':selected').attr('data-computedValue'); // $(this).val();
      var id = $(this).attr('data-entryID');
      var itemval = $(this).val();

      if (selval == 0)
      {
        $("#evaluated-"+id).html('<i class="fa fa-exclamation-circle text-gray"></i>');
        $('#score-'+id).html("0%");
        $('#score-'+id).attr("value",'0.0');

        var overall = 0;

        var grades = $(".scores");
        console.log("The grades:");
        console.log(grades);

          for (var i=0; i< grades.length; i++){
            overall += parseFloat($(grades[i]).attr("value"));
            console.log('LOG GRADES: '+ parseFloat($(grades[i]).attr("value")));
          }

          
          $('input#total').attr("value",overall.toPrecision(4));
           //var finalScore = overall.toPrecision(4);
          //$('input#total').attr("value",finalScore);
          $('#overallScore').html(overall.toPrecision(4)+"%");


      } else 
      {
        var num = ($(this).find(':selected').attr('data-computedValue')/{{$maxScore}})*100; //($(this).val()/{{$maxScore}})*100;
        var overall = 0;

         $("#evaluated-"+id).html('<i class="fa fa-check text-green"></i>');


          $('#score-'+id).html(num.toPrecision(4)+"");
          $('#score-'+id).attr("value",num.toPrecision(4));
          var saved = $('#score-'+id).val();
          console.log("max score: {{$maxScore}} ");

          var grades = $(".scores");
          console.log("Ang grades: " + grades);

          for (var i=0; i< grades.length; i++){
            overall += parseFloat($(grades[i]).attr("value"));
            console.log('add: '+ parseFloat($(grades[i]).attr("value")));
          }

        //-------- require justification if 5 | 4 | 2| 1 ----------------------
          var cbid = $(this).attr('data-commentBox');
          var commentBox = $('textarea[name="'+cbid+'"]');
          

          if(itemval !== '3' )
          {
            
            commentBox.prop('required',true);

            if (commentBox.val() == "")
              alert('Don\'t forget to provide enough justification about this rating on the comment box provided.');

            console.log("itemval: "+itemval);
            console.log(commentBox);
            console.log('REQUIRED:');


            
          }else{
            
            commentBox.prop('required',false);
            console.log('REQUIRED:');

            var mgareq = $('textarea[value=""]').filter('[required]'); //.find('textarea[value==""]');
            console.log(mgareq);
          } 

          
          

          var trueGrade = 100-((100-overall)*0.5);
          var finalScore = overall.toPrecision(4);
          $('#overallScore').html('');

          $('#overallScore').html(trueGrade.toPrecision(4)+"%"); 
         //$('#overallScore').html(finalScore+"%");
          //console.log('id: '+ id + 'num: '+ num);
         // console.log('overall: '+ overall.toPrecision(4));

         //$('input#total').attr("value",overall.toPrecision(4));
         $('input#total').attr("value",trueGrade.toPrecision(4));
         

          console.log('TOTAL input val: '+ overall.toPrecision(4));

      }

      <?php $idxctr = 0; ?>
      @foreach ($ratingScale as $rs)

           @if( $idxctr == count($ratingScale))
            
              if ( trueGrade.toPrecision(4) <= {{$rs->maxRange}} ){

           @else

              if (trueGrade.toPrecision(4) <= {{$rs->maxRange}} && trueGrade.toPrecision(4)>{{$rs->maxRange - $ratingScale[$idxctr]->maxRange }}){
              $("#descriptions").html('<span style="font-size:0.6em;" class="text-gray">{{$rs->status}}  <i class="fa {{$rs->icon}} "></i> </span>');
              

           @endif

            
            $('.ratingTable').removeClass('bg-green');
            $('#rating-'+"{{$rs->percentage}}").addClass('bg-green');


                    

                }
            <?php $idxctr++;?>

      @endforeach

       @if ($evalType->id !== 3) // REGULARIZATION

           if ( (trueGrade.toPrecision(4) <= 101) && (trueGrade.toPrecision(4) >= 97.50))
           {
                  // $("#descriptions").append('<br/><span style="font-size:0.4em" class="text-danger"><i class="fa fa-money"></i> Salary Increase: <strong>5 %</strong></span>');
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

           if ( (trueGrade.toPrecision(4) <= 101) && (trueGrade.toPrecision(4) >= 97.50))
           {
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

       console.log("total overall: "+overall.toPrecision(4));
        
       
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