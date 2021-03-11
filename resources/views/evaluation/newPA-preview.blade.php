@extends('layouts.main')

@section('metatags')
<title>Preview: Performance Appraisal | EMS</title>

<meta name="viewport" content="width=device-width">
<link rel="stylesheet" href="../public/css/bootstrap.min.css">
<link rel="stylesheet" href="../public/js/jquery-ui.css">
<!-- Include SmartWizard CSS -->
<link href="../public/css/smart_wizard.css" rel="stylesheet" type="text/css" />

<!-- Optional SmartWizard theme -->
<link href="../public/css/smart_wizard_theme_circles.css" rel="stylesheet" type="text/css" />
<link href="../public/css/smart_wizard_theme_arrows.css" rel="stylesheet" type="text/css" />
<link href="../public/css/smart_wizard_theme_dots.css" rel="stylesheet" type="text/css" />


<style type="text/css">
 #weight1,#weight2,#weight3,#weight4,#weight5 {
    width: 3em;
    height: 1.6em;
    top: 50%;
    margin-top: -.8em;
    text-align: center;
    line-height: 1.6em;

  }

#slider1 .ui-slider-range,
#slider2 .ui-slider-range,
#slider3 .ui-slider-range,
#slider4 .ui-slider-range,
#slider5 .ui-slider-range { background: #729fcf; }
</style>
@endsection

@section('content')


  <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>&nbsp;</h1>
      <ol class="breadcrumb">
        <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="{{action('NewPA_Form_Controller@index')}}"> Performance Appraisal</a></li>
        <li class="active">New</li>
      </ol>
    </section>

     <section class="content">
      <!-- ******** THE DATATABLE ********** -->
          <div class="row">
             <div class="col-lg-12">

              <div class="box box-primary"  style="padding:30px">
               
                
                
                <!-- ****** STEP 3 COMPETENCIES ********** -->
                <div id="step-3" class="col-lg-12" style="padding:20px;background: rgba(256, 256, 256, 0.6);">
                   
                    <img src="../../public/img/oam_favicon1-55027f4ev1_site_icon-256x256.png" width="70" class="pull-left" /> 
                    <h3 class="pull-left" style="padding-left:10px;width: 90% ">{{$form[0]->name}}<br/></h3>
                    <h2 id="overall" class="pull-right" style="padding: 20px; background-color: #dedede; color: #fff"></h2>
                    <div class="clearfix"></div>
                    <p class="pull-right">Overall Rating</p>
                    <table class="table" id="reviewform">
                      <thead>
                        <tr>
                          <th>
                            
                            
                          </th>
                        </tr>

                      </thead>
                      <tbody>

                        @foreach($allComponents as $comp)
                        <tr>
                          <td style="font-size:larger;" class="text-primary"><i class="fa fa-check text-success"></i> &nbsp;{{$comp[0]->componentName}} </td><td> </td>
                          <td><strong class="text-success" style="font-size:larger">{{$comp[0]->componentWeight}} % </strong></td>
                          @if($comp[0]->componentName == "Professionalism")
                              <td>
                                <label>Rating</label>
                                <select name="rating_professionalism" class="rating_professionalism form-control" data-goalweight="{{$goal[0]->goalWeight}}" data-componentweight="{{$comp[0]->componentWeight}}" data-goalID="{{$goal[0]->goalID}}">
                                    <option value="0">* select rating *</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                  </select>
                              </td>
                              <td style="text-align: right;"><span class="goal_points" id="points_{{$goal[0]->goalID}}" style="font-weight: bold;">0.00</td>
                          @else
                              <td></td>
                          @endif
                        </tr>

                          @if($comp[0]->componentName == "Goals & Objectives" )
                          <tr>
                            <td style="padding:5px 5px 0px 50px" width="50%"> Goals & Action statements</td>
                            <td width="20%">Goal distribution</td>
                            <td width="15%">Rating</td>
                            <td class="text-right">Points</td>
                          </tr>

                              @foreach($allGoals as $goal)
                              <tr>
                                <td style="padding:30px"> &nbsp; {{$goal[0]->statement}}
                                  <div style="width:90%;margin-top:30px; padding:20px;border:1px dotted #333; text-align: left;"><!--  -->
                                    <strong>Action/Activities :</strong><br/><div style=" white-space:pre-wrap;"> {!! $goal[0]->activities !!} </div><br/><br/>
                                    <strong>Target/KPI :</strong><br/><div style=" white-space:pre-wrap;"> {!! $goal[0]->activities !!}</div>
                                 
                                </div>
                                </td>
                                <td>{{$goal[0]->goalWeight}} % </td>
                                <td>
                                  <select name="rating_{{$goal[0]->goalID}}" class="rating_goal form-control" data-goalweight="{{$goal[0]->goalWeight}}" data-componentweight="{{$comp[0]->componentWeight}}" data-goalID="{{$goal[0]->goalID}}">
                                    <option value="0">* select rating *</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                  </select>
                                </td>
                                <td style="text-align: right;"><span class="goal_points" id="points_{{$goal[0]->goalID}}" style="font-weight: bold;">0.00</td>
                              </tr>
                              @endforeach

                          @elseif( $comp[0]->componentName == "Professionalism" )
                          <tr>
                            <td colspan="3" class="bg-gray" style="padding-left: 50px;" >
                              <div class="row">
                               
                                <div class="col-lg-1" style="font-weight: bold;">Rating</div>
                                <div class="col-lg-4 text-center" style="font-weight: bold;">Guidelines</div>
                                <div class="col-lg-4 text-center" style="font-weight: bold;">Qty Infractions by yearend</div>
                                <div class="col-lg-3" style="font-weight: bold;">Penalty category by yearend</div>
                              </div>

                              <div class="row">
                                
                                <div class="col-lg-1"><br/>5</div>
                                <div class="col-lg-4"><br/>
                                    <strong>OUTSTANDING PERFORMANCE</strong><br/><br/>

                                    <em style="font-size: small;">Behavior consistently exceeds the standards outstandingly
                                    Performance effects program-wide or companywide impact</em>
                                </div>
                                <div class="col-lg-4 text-center">0</div>
                                <div class="col-lg-3">None</div>
                              </div>
                              <div class="row">
                                
                                <div class="col-lg-1"><br/>4</div>
                                <div class="col-lg-4"><br/>
                                  <strong>EXCEEDS EXPECTATIONS </strong><br/><br/>

                                    <em style="font-size: small;"> Consistently exhibits behavior  and encourages others to do so impacting members even outside their immediate scope<br/>
                                    Drives impact within team<br/>
                                    Advocates and promotes core values by encouraging others</em>
                                </div>
                                <div class="col-lg-4 text-center">1-2 <br/>
                                  1 minor offense or 1 cleansed DA
                                </div>
                                <div class="col-lg-3">MINOR [VERBAL]</div>
                              </div>
                              <div class="row">
                                
                                <div class="col-lg-1"><br/>3</div>
                                <div class="col-lg-4"><br/><strong>MEETS EXPECTATIONS</strong><br/><br/>

                                    <em style="font-size: small;"> Exhibits behavior consistently<br/>
                                    Regularly demonstrates the competency
                                </div>
                                <div class="col-lg-4 text-center">3-4 <br/>
                                      Two Level 1 offenses or Two instances (repeated) of the same Level 1 offense
                                </div>
                                <div class="col-lg-3">MINOR [WRITTEN]</div>
                              </div>
                              <div class="row">
                                
                                <div class="col-lg-1"><br/>2</div>
                                <div class="col-lg-4"><br/><strong>IMPROVEMENT NEEDED</strong><br/><br/>
                                      <em style="font-size: small;">Exhibits behavior inconsistently<br/>
                                      Needs improvement for Performance Improvement or<br/>
                                        Performance Development (for those new to current        
                                        position)</em></div>
                                <div class="col-lg-4 text-center">Three or more Level 1offenses and/or One Level 2 offense</div>
                                <div class="col-lg-3">SERIOUS [FINAL WRITTEN]</div>
                              </div>
                              <div class="row">
                                
                                <div class="col-lg-1"><br/>1</div>
                                <div class="col-lg-4"><br/><strong>UNSATISFACTORY PERFORMANCE</strong><br/><br/>
                                    <em style="font-size: small;">Never or almost never exhibits behavior</em></div>
                                <div class="col-lg-4 text-center">5 or more <br/>
                                Two or more Level 2 offenses and or at least 1 level 3 offense</div>
                                <div class="col-lg-3">SERIOUS/GRAVE [FINAL WRITTEN or SUSPENSION]</div>
                              </div>
                              
                            </td>
                            
                       
                            
                            
                          </tr>

                          @endif

                        @endforeach

                        
                      </tbody>
                    </table>

                    <table id="competencylist" class="table table-hover">
                      <thead>
                        <tr>
                          <th width="40%">&nbsp;&nbsp;&nbsp;&nbsp;Competencies</th>
                          <th width="30%">Critical Incidents</th>
                         
                          <th>Weight</th>
                          <th width="10%">Rating</th>
                          <th>Points</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td colspan="5">

                        @foreach($allCompetencies as $comp)

                        <!-- ******** collapsible box ********** -->
                            <div class="box box-default collapsed-box">
                            <div class="box-header with-border">
                              <div class="row">
                                <div class="col-lg-6">
                                  <h3 class="box-title text-primary">{{$comp[0]->competency}}  </h3>
                                </div>
                                <div class="col-lg-3"></div>
                               
                                <div class="col-lg-2">
                                  <h3 class="box-title text-primary"><small id="evaluated-{{$comp[0]->competencyID}}">
                                    <i class="fa fa-exclamation-circle text-yellow"></i></small>
                                  </h3>
                                </div>
                                <div class="comp_points col-lg-1" id="comp-points_{{$comp[0]->competencyID}}" style="font-weight: bold;">0.00</div>
                              </div>
                              



                              <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                                </button>
                              </div>
                              <!-- /.box-tools -->
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">
                              <table width="100%">
                                <tr>
                                    <td style="font-size: larger" width="45%">
                                      {{$comp[0]->competency}}<br/><br/>

                                      <div style="font-size: smaller; width: 100%">
                                        <?php $desc = collect($descriptors)->where('competencyID',$comp[0]->competencyID); ?>
                                        @foreach($desc as $d)
                                        
                                          
                                        <p id="desc_{{$d->id}}"   style="width: 90%">
                                        
                                          <a data-target="{{$d->id}}" data-id="{{$comp[0]->competencyID}}" data-type="1" class="descs btn btn-xs btn-success " style="margin-left: 2px"><i class="fa fa-thumbs-up"></i> </a> 
                                          <a  data-target="{{$d->id}}" data-id="{{$comp[0]->competencyID}}" data-type="2"  class="descs btn btn-xs btn-danger " style="margin-left: 2px"><i class="fa fa-thumbs-down"></i> </a> 
                                          <a  data-target="{{$d->id}}" data-id="{{$comp[0]->competencyID}}"  data-type="0"  class="descs btn btn-xs btn-primary  " style="margin-left: 2px"><i class="fa fa-ban"></i> </a>&nbsp;&nbsp;{{$d->descriptor}} 
                                        </p>
                                          

                                        @endforeach
                                      </div>


                                    </td>
                                    
                                   
                                    <td width="30%">
                                      <label>Strengths </label><textarea rows="7" id="strengths_{{$comp[0]->competencyID}}" class="form-control"></textarea><br/><br/>
                                      <label>Areas For Improvement </label><textarea rows="7" id="afi_{{$comp[0]->competencyID}}" class="form-control"></textarea>
                                    </td>
                                    <td valign="top">{{$comp[0]->competencyWeight}} %</td>
                                    <td valign="top">
                                      <select name="ratingComp_{{$comp[0]->competencyID}}" class="rating_comp form-control" data-compID="{{$comp[0]->competencyID}}" data-componentweight="{{$comp[0]->competencyWeight}}">
                                              <option value="0">* select rating *</option>
                                              <option value="1">1</option>
                                              <option value="2">2</option>
                                              <option value="3">3</option>
                                              <option value="4">4</option>
                                              <option value="5">5</option>
                                            </select>
                                    </td>
                                    <td style="font-weight: bold;" id="point-comp_{{$comp[0]->competencyID}}"></td>
                                </tr>
                              </table>

                              
                            </div>
                            <!-- /.box-body -->
                          </div>
                          <!-- ******** end collapsible box ********** -->


                        

                        @endforeach
                        </td>


                      </tr>
                        

                      </tbody>

                    </table>

                    
                <div class="clearfix"></div>
              </div>
              <div class="clearfix"></div>


                
            

       


             



              </div><!--end box-primary-->


             

             

          </div><!--end main row-->
      </section>

      

      
 

    



@endsection


@section('footer-scripts')

<script>
  $.widget.bridge('uibutton', $.ui.button);
</script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>




<!-- Page script -->
<script>
 
  $(function () {
   'use strict';

   // var handle = $( "#weight1" );
   //  $( "#slider1" ).slider({
   //    
   //    
   //  });

   $('.rating_goal.form-control').on('change',function(){

      var rate = $(this).find(':selected').val();
      var componentWeight = $(this).attr('data-componentweight');
      var goalWeight = $(this).attr('data-goalweight');
      var goalID = $(this).attr('data-goalID');

      var overallscore = 0;
      var allgoal= 0;
      var allcomp = 0;

      var totalpoint = rate * (((componentWeight/100) * goalWeight)/100);

      $('#points_'+goalID).html("");
      $('#points_'+goalID).append(totalpoint.toFixed(2));
      $('#points_'+goalID).attr('data-point', totalpoint.toFixed(2));


      var allGoalPoints = $('.goal_points');//.attr('data-point');
      var allCompPoints = $('.comp_points.col-lg-1'); //.attr('data-point');

      

      $.each( allGoalPoints, function(key,val){

        allgoal += parseFloat(val.innerText);

      });
      

      $.each( allCompPoints, function(key,val){

        allcomp += parseFloat(val.innerText);

      });
      
      overallscore = allgoal+allcomp;
      if (overallscore > 5.0) overallscore=5.00

      
      $('#overall').html("");
      $('#overall').append(overallscore.toFixed(2));

      if(overallscore > 4.5){
        $('#overall').css('background-color',"#1a8fcb");
      }else if(overallscore >= 4.0){
         $('#overall').css('background-color',"#64d254");
      }else if(overallscore >= 3.1){
        $('#overall').css('background-color',"#666");
      }else if(overallscore >= 2.0){
        $('#overall').css('background-color',"#ea8f1b");
      }else{
        $('#overall').css('background-color',"#ff1212");
      }




   });

   $('.rating_professionalism.form-control').on('change',function(){

      var rate = $(this).find(':selected').val();
      var componentWeight = $(this).attr('data-componentweight');
      var goalWeight = $(this).attr('data-goalweight');
      var goalID = $(this).attr('data-goalID');

      var overallscore = 0;
      var allgoal= 0;
      var allcomp = 0;

      var totalpoint = rate * (((componentWeight/100) * goalWeight)/100);

      $('#points_'+goalID).html("");
      $('#points_'+goalID).append(totalpoint.toFixed(2));
      $('#points_'+goalID).attr('data-point', totalpoint.toFixed(2));


      var allGoalPoints = $('.goal_points');//.attr('data-point');
      var allCompPoints = $('.comp_points.col-lg-1'); //.attr('data-point');

      

      $.each( allGoalPoints, function(key,val){

        allgoal += parseFloat(val.innerText);

      });
      

      $.each( allCompPoints, function(key,val){

        allcomp += parseFloat(val.innerText);

      });
      
      overallscore = allgoal+allcomp;
      if (overallscore > 5.0) overallscore=5.00

      
      $('#overall').html("");
      $('#overall').append(overallscore.toFixed(2));

      if(overallscore > 4.5){
        $('#overall').css('background-color',"#1a8fcb");
      }else if(overallscore >= 4.0){
         $('#overall').css('background-color',"#64d254");
      }else if(overallscore >= 3.1){
        $('#overall').css('background-color',"#666");
      }else if(overallscore >= 2.0){
        $('#overall').css('background-color',"#ea8f1b");
      }else{
        $('#overall').css('background-color',"#ff1212");
      }




   });

   $('.rating_comp.form-control').on('change',function(){

      var rate = $(this).find(':selected').val();
      var componentWeight = $(this).attr('data-componentweight');
      var compID = $(this).attr('data-compID');

      var overallscore = 0;
      var allgoal= 0;
      var allcomp = 0;

      var totalpoint = rate * (componentWeight/100);

      $('#point-comp_'+compID).html("");
      $('#point-comp_'+compID).append(totalpoint.toFixed(2));

      if(rate !== 0){

        $('#evaluated-'+compID).html("");
        $('#evaluated-'+compID).append('<i class="fa fa-check text-success"></i>&nbsp;<span class="label label-default" style="font-size:larger">'+rate+'</span>');
        $('#comp-points_'+compID).html("");
        $('#comp-points_'+compID).append(totalpoint.toFixed(2));
        $('#comp-points_'+compID).attr('data-point', totalpoint.toFixed(2));
      }else{
        $('#evaluated-'+compID).html("");
        $('#comp-points_'+compID).html("");
        $('#evaluated-'+compID).append('<i class="fa fa-exclamation-circle text-yellow"></i>');
        $('#comp-points_'+compID).attr('data-point',0);

      }

      var allGoalPoints = $('.goal_points');//.attr('data-point');
      var allCompPoints = $('.comp_points.col-lg-1'); //.attr('data-point');

      

      $.each( allGoalPoints, function(key,val){

        allgoal += parseFloat(val.innerText);

        console.log(overallscore);

      });
      

      $.each( allCompPoints, function(key,val){

        allcomp += parseFloat(val.innerText);

      });
      
      overallscore = allgoal+allcomp;
      if (overallscore > 5.0) overallscore=5.00

      
      $('#overall').html("");
      $('#overall').append(overallscore.toFixed(2));

      if(overallscore > 4.5){
        $('#overall').css('background-color',"#1a8fcb");
      }else if(overallscore >= 4.0){
         $('#overall').css('background-color',"#64d254");
      }else if(overallscore >= 3.1){
        $('#overall').css('background-color',"#666");
      }else if(overallscore >= 2.0){
        $('#overall').css('background-color',"#ea8f1b");
      }else{
        $('#overall').css('background-color',"#ff1212");
      }

    
   });

  $('a.descs').on('click',function(){
    var item = $(this).attr('data-target');
    var id = $(this).attr('data-id');
    var clickType =  $(this).attr('data-type');
    var par = $('p#desc_'+item);
    var txt_strength = $('#strengths_'+id);
    var txt_afi = $('#afi_'+id);

    var reptxt = par[0]['lastChild']['data'];

    //var reptxt = txt.replace(/(\r\n|\n|\r)/gm, "");

    console.log(par[0]);
    console.log(reptxt);
    

    switch(clickType){
      case '1': txt_strength.append("* "+ reptxt.replace('\n','')); break;
      case '2': txt_afi.append("* "+ reptxt.replace('\n','')); break;
    }



     $('p#desc_'+item).fadeOut();
   

  });


  


   
   




    // ********* Step show event **********************

        
      
      
   });

   

</script>
<!-- end Page script -->



@stop