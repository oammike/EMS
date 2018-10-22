@extends('layouts.main')

@section('metatags')
<title>Print Evaluation | OAMPI Evaluation System</title>
@endsection

@section('bodyClasses')
sidebar-collapse
@endsection

@section('content')


  <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1 class="text-center">
         {{$evalType->name}}<br/>
        <small>Period:<strong> {{$startPeriod->format('M d')}} to  {{$endPeriod->format('M d, Y')}}  </strong> </small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#" onclick="window.print();return false;" class="btn btn-sm btn-default pull-left"><i class="fa fa-print"></i> Print </a></li>
      </ol>
      
    </section>

     <section class="content">

          <div class="row">
            
            

            <div class="col-lg-12">
              <div class="box box-primary">
                      <div class="box-header ">
                          
                             
                        

                      
                      </div><!--end box-header-->
                      
                      <div class="box-body">
                       

                        <div class="row">
                          <div class="col-sm-8">
                             
                                @if ( file_exists('public/img/employees/'.$employee->id.'.jpg') )
                                <img src="{{asset('public/img/employees/'.$employee->id.'.jpg')}}" class="img-circle pull-left" style="margin-right:10px" width="80" alt="User Image">
                                @else
                                  <img src="{{asset('public/img/useravatar.png')}}" class="user-image pull-left" alt="Employee Image" width="80" style="margin-right:10px">

                                  @endif

                                  <h4 class="widget-user-username" style="line-height:0.8em"> {{$employee->lastname }}, {{$employee->firstname}} </h4>
                              <h5 class="widget-user-desc">{{$employee->position->name}}<br/>
                             <strong> {{$employee->campaign[0]->name}} </strong> </h5>
                               
                              

                          </div>

                          <div class="col-sm-4">

                             
                             <h1 style="line-height:0.8" class="text-danger pull-right text-right" id="overallScore"  data-score="{{$evalForm->overAllScore}}"><br/><span style="font-size:0.6em;" class="text-gray"></h1>
                            <div class="clearfix"></div>
                             <h1 style="line-height:0.8; margin-top:-10px" class="text-danger pull-right text-right" id="descriptions"></h1>
                       
                          </div>
                        </div>

                        <div class="row">
                          <div class="col-sm-4">
                            <div class="box-footer no-padding">

                                <ul class="nav nav-stacked">
                                  <li><a href="#">Date Hired <span class="pull-right badge bg-gray">{{ date_format(date_create($employee->dateHired), "M d, Y")  }} </span></a></li>
                                 <!--  <li><a href="#">Absences <span class="pull-right badge bg-red">N/A</span></a></li>
                                  <li><a href="#">Tardiness <span class="pull-right badge bg-red">N/A</span></a></li>
                                  <li><a href="#">Received Disciplinary Actions <span class="pull-right badge bg-red">N/A</span></a></li> -->

                                  
                                  <li><a href="#">
                                    <span class="pull-left">Date Evaluated <br/>
                                      <small>{{$evalForm->created_at}} </small></span> </a>
                                      <h5 class="pull-right"> by: {{$evaluator->firstname}} {{$evaluator->lastname}} &nbsp;&nbsp;</h5>

                                    </li>


                                 
                                  
                                </ul>
                               <div class="clearfix"></div>
                            </div>

                          </div>

                           <div class="col-sm-3"></div>

                          <div class="col-sm-5">
                            <table class="table" style="margin: 0 auto;">
                                  
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

                                </table> 
                            


                          </div>
                        </div>



                        
                        
                        <div class="clearfix text-center bg-gray"><h5><strong>Grading Scale and Definitions</strong></h5></div>

                        <div class="box box-widget widget-user-2">
                            <!-- Add the bg color to the header using any of the bg-* classes -->
                           
                            <div class="box-footer">
                              <table class="table">
                                <thead>
                                  <tr>
                                    <th class="text-center">Scale</th>
                                    <th class="text-center">%</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center"></th>
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

                        





                        <div class="nav-tabs-custom">
                          

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

                                                        <div style="font-style:italic; color:gray">

                                                           @if (!empty($entry['value']))
                                                                    <label>{{$entry['attribute']}}</label><br />{{$entry['value']}}
                                                                    <br/><br/>
                                                                    @endif

                                                                     @if (!empty($formEntries[$cnt+1]['value']))

                                                                     <br/><br/><label>{{$formEntries[$cnt+1]['attribute']}}</label><br/>{{$formEntries[$cnt+1]['value']}}
                                                                    <br/>
                                                                    @endif
                                                                    

                                                        </div>
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
                                        
                                          
                                <!-- ******** SUMMARY ******* -->
                                <div class="box box-default">
                                <div class="box-header"><h4>Overall Performance Summary</h4></div>
                                <div class="box-body">
                                  <table class="table table-bordered">
                                    <?php $ctr=1; $indexCtr=0; ?>
                                    @foreach ($summaries as $summary)
                                         @if (!empty($performanceSummary[$indexCtr]) || $performanceSummary[$indexCtr] !== "   " || $performanceSummary[$indexCtr] !== "")
                                        
                                          <tr >
                                            <td colspan="2"><strong>{{$ctr}}.  {{$summary['header']}} </strong><br/>
                                              {{ $summary['details'] }}<br/><br/></td>
                                          </tr>
                                        @endif
                                    
                                        @if ( $summary['columns'] !== null)
                                         
                                          <tr> 
                                            @foreach ($summary['columns'] as $col)
                                              @if (!empty($performanceSummary[$indexCtr]) || $performanceSummary[$indexCtr]!== '   ' || $performanceSummary[$indexCtr] !== '')
                                              <th class="bg-gray text-center">{{$col->name}} </th>
                                              @endif
                                            @endforeach
                                          </tr>
                                          
                                          <tr>
                                             @foreach ($summary['columns'] as $col)

                                               @if (!empty($performanceSummary[$indexCtr]) || $performanceSummary[$indexCtr]!== '   ' || $performanceSummary[$indexCtr] !== '')
                                            <td>{{$performanceSummary[$indexCtr]}}<p>&nbsp;</p></td>
                                              @endif
                                            <?php $indexCtr++;?>
                                            
                                            @endforeach
                                          </tr>
                                         
                                        @endif

                                        @if ( $summary['rows'] !== null)

                                       
                                             @foreach ($summary['rows'] as $row)

                                                 @if (!empty($performanceSummary[$indexCtr]) || $performanceSummary[$indexCtr]!== "    " || $performanceSummary[$indexCtr] !== "")
                                                    <tr>
                                                     
                                                      <th class="text-right" valign="middle">{{$row->name}} </th>
                                                      <td>{{$performanceSummary[$indexCtr]}}<p>&nbsp;</p></td>
                                                      

                                                      
                                                    </tr>
                                                @endif

                                                <?php $indexCtr++;?>

                                            @endforeach
                                     
                                        
                                        @endif
                                    
                                    
                                    <?php $ctr++; ?>
                                   
                                    @endforeach
                                  </table>

                                </div>
                              </div>

                                <!- ********* END SUMMARY ********** -->          
                              

                              

                              
                              </div>
                              <!-- /.tab-pane -->
                            
                            
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

          } else if ( rating <= 97.4 && rating >= 89.5 ){ 
            $("#descriptions").append('<br/><span style="font-size:0.4em" class="text-danger"><i class="fa fa-money"></i> Salary Increase: <strong>4 %</strong></span>');
            $('input#salaryIncrease').attr("value",4);

          } else if ( rating <= 89.4 && rating >= 84.5 ){
            $("#descriptions").append('<br/><span style="font-size:0.4em" class="text-danger"><i class="fa fa-money"></i> Salary Increase: <strong>3 %</strong></span>');
            $('input#salaryIncrease').attr("value",3);

          } else if ( rating <= 84.4 && rating >= 80 ){
            $("#descriptions").append('<br/><span style="font-size:0.4em" class="text-danger"><i class="fa fa-money"></i> Salary Increase: <strong>2 %</strong></span>');
            $('input#salaryIncrease').attr("value",2);

          } else if ( rating <= 79.9 && rating >= 70 ){
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