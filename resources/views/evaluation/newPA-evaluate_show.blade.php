@extends('layouts.main')

@section('metatags')
<title>New Evaluation: Performance Appraisal for | EMS</title>

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

                    <div class="row">
                      <div class="col-lg-5">
                        <img src="../public/img/employees/{{$user[0]->id}}.jpg" width="30%" class="pull-left" />
                        <h4 class="pull-left" style="padding-left:10px;width: 70%">{{$user[0]->lastname}}, {{$user[0]->firstname}} <em>({{$user[0]->nickname}})</em><br/>
                          <em style="font-size: smaller;">{{$user[0]->jobTitle}} </em><br/>
                          <span style="font-size: smaller;"><br/>{{$user[0]->program}} </span> </h4>
                      </div>

                      <div class="col-lg-3 text-center">
                        <img src="../public/img/oam_favicon1-55027f4ev1_site_icon-256x256.png" width="70" /> 
                      </div>
                      <div class="col-lg-4 text-right">
                        <h3 class="pull-left" style="padding-left:10px">Performance Evaluation<br/>
                        <span style="font-size: small;">Evaluation Period: <br/>
                          <label>From : 
                            {{ date('M d, Y', strtotime($evalCompetencies[0]->startPeriod))}}
                          </label>
                          <label>To  : 
                             {{ date('M d, Y', strtotime($evalCompetencies[0]->endPeriod))}}
                          </label> </span></h3>
                      </div>

                    </div>

                    
                   
                    
                    
                    <div class="clearfix"></div>
                    
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
                          <td></td>
                        </tr>

                          @if($comp[0]->componentName == "Goals & Objectives" )
                          <tr>
                            <td style="padding:5px 5px 0px 50px" width="50%"> Goals & Action statements</td>
                            <td width="20%">Goal distribution</td>
                            <td width="15%">Rating</td>
                            <td class="text-right">Points</td>
                          </tr>

                              @foreach($allGoals as $goal)
                              <?php $e = collect($evalGoals)->where('goal_id',$goal[0]->goalID); ?>
                              <tr>
                                <td style="padding:30px"> &nbsp; {{$goal[0]->statement}}
                                  <div style="width:90%;margin-top:30px; padding:20px;border:1px dotted #333; text-align: left;"><!--  -->
                                    <strong>Action/Activities :</strong><br/><div style=" white-space:pre-wrap;"> {!! $goal[0]->activities !!} </div><br/><br/>
                                    <strong>Target/KPI :</strong><br/><div style=" white-space:pre-wrap;"> {!! $goal[0]->activities !!}</div>

                                 
                                </div>
                                <br/><label>Notes / Comments: </label>
                                    <textarea disabled="disabled" class="goalcomments form-control" id="goalComment{{$goal[0]->goalID}}" style="white-space: pre-wrap;">
                                      
                                      {{$e->first()->notes}}

                                    </textarea>
                                </td>
                                <td>{{$goal[0]->goalWeight}} % </td>
                                <td>
                                  
                                  <h4 class="text-primary">{{$e->first()->rating}}</h4>
                                </td>
                                <td style="text-align: right;"><span class="goal_points" id="points_{{$goal[0]->goalID}}" style="font-weight: bold;">
                                  <?php $p = (($goal[0]->goalWeight/100) * $e->first()->rating) * ($comp[0]->componentWeight/100); ?>
                                  {{$p}}

                                </td>
                              </tr>
                              @endforeach

                          @endif

                        @endforeach

                        
                      </tbody>
                    </table>

                    <table id="competencylist" class="table table-hover">
                      <thead>
                        <tr>
                          <th width="40%">&nbsp;&nbsp;&nbsp;&nbsp;Competencies</th>
                          <th width="30%"> </th>
                         
                          <th>Weight</th>
                          <th width="10%">Rating</th>
                          <th>Points</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td colspan="5">

                        @foreach($allCompetencies as $comp)
                        <?php $c = collect($evalCompetencies)->where('competency_id',$comp[0]->competencyID); ?>
                        <?php $p = (($comp[0]->competencyWeight/100) * $c->first()->rating); ?>
                                 

                        <!-- ******** collapsible box ********** -->
                            <div class="box box-default collapsed-box">
                            <div class="box-header with-border">
                              <div class="row">
                                <div class="col-lg-6">
                                  <h3 class="box-title text-primary">{{$comp[0]->competency}}  </h3>
                                </div>
                                <div class="col-lg-3"></div>
                               
                                <div class="col-lg-2">
                                  <h3 class="box-title text-primary"><span id="evaluated-{{$comp[0]->competencyID}}">
                                    @if($c->first()->rating == '0.00')
                                    <i class="fa fa-exclamation-circle text-yellow"></i> {{$c->first()->rating}}</span>
                                    @else
                                    <i class="fa fa-check text-success"></i> {{$c->first()->rating}}</span>
                                    @endif
                                    
                                  </h3>
                                </div>
                                <div class="comp_points col-lg-1" id="comp-points_{{$comp[0]->competencyID}}" style="font-weight: bold;"> {{$p}} </div>
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
                                    <td style="font-size: larger" width="35%">
                                      

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

                                      <label>Strengths </label>
                                      <textarea disabled="disabled" rows="7" id="strengths_{{$comp[0]->competencyID}}" class="form-control" style="white-space: pre-wrap;">
                                        {{$c->first()->strengths}}
                                      </textarea><br/><br/>



                                    </td>
                                    
                                   
                                    <td width="35%" style="padding-left: 30px">
                                      
                                      <label>Areas For Improvement </label>
                                      <textarea  disabled="disabled" rows="7" id="afi_{{$comp[0]->competencyID}}" class="form-control" style="white-space: pre-wrap;">
                                        {{$c->first()->afi}}
                                      </textarea><br/><br/>

                                      
                                    </td>
                                    <td>{{$comp[0]->competencyWeight}} %</td>
                                    <td>
                                     

                                      <h4>{{$c->first()->rating}} </h4>
                                    </td>
                                    <td style="font-weight: bold;" id="point-comp_{{$comp[0]->competencyID}}"></td>


                                </tr>
                                <tr>
                                  <td colspan="5">
                                    <label>Notes / Comments </label>
                                      <textarea  disabled="disabled" rows="7" id="crit_{{$comp[0]->competencyID}}" class="form-control" style="white-space: pre-wrap;">
                                        {{$c->first()->notes}}
                                      </textarea>
                                    </td>
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

                    <p class="text-center" style="margin-top: 20px"><a id="saveEval" class="btn btn-lg btn-danger"><i class="fa fa-pencil"></i> Modify Evaluation</a></p>

                    
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
   //$( ".datepicker" ).datepicker();

   $('#overall').html("");
   var overallscore = {{$evalCompetencies[0]->finalRating}};
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


  $('#saveEval').on('click', function(){

    var goalRatings = [];
    var compRatings=[];
    var overall = $('#overall').html();
    var _token = "{{ csrf_token() }}";
    var period_mfrom = $('#period_mfrom').find(':selected').val();
    var period_dfrom = $('#period_dfrom').find(':selected').val();
    var period_mto = $('#period_mto').find(':selected').val();
    var period_dto = $('#period_dto').find(':selected').val();

    $('.rating_goal.form-control').find(':selected').each(function(key,value){

      var cid= $(this).parent().attr('data-goalID');
      var r = {goalID: cid, goalRating: value['value'], 
                points: $('#points_'+cid).attr('data-point'), goalComment: $('#goalComment'+cid).val()};
      goalRatings.push(r);


    });

    $('.rating_comp.form-control').find(':selected').each(function(key,value){

      var cid= $(this).parent().attr('data-compID');
      var r = {competencyID: cid, compRating: value['value'], 
                strengths: $('#strengths_'+cid).val(), afi: $('#afi_'+cid).val(), crit: $('#crit_'+cid).val()};
      compRatings.push(r);
    });

    

    console.log('GOALS: ');
    console.log(goalRatings);
    console.log('COMPETENCIES: ');
    console.log(compRatings);
    console.log('Overall:');
    console.log(overall);


    $.ajax({
              url:"{{action('NewPA_Evals_Controller@process')}}",
              type:'POST',
              data:{
                'goalRatings': goalRatings,
                'compRatings': compRatings,
                'overall': overall,
                'form_id': "{{$form[0]->id}}",
                'user_id': "{{$user[0]->id}}",
                'tl_id':"{{$user[0]->tlID}}",
                'period_mfrom': period_mfrom,
                'period_dfrom' : period_dfrom,
                'period_mto': period_mto,
                'period_dto': period_dto,
                '_token':_token},

              error: function(response)
              { console.log("Error saving eval form ");
              console.log(response); return false;
              },
              success: function(response)
              {

                console.log(response);
                $.notify("Appraisal Form saved.",{className:"success",globalPosition:'right center',autoHideDelay:7000, clickToHide:true} );
                $('#saveEval').fadeOut();
                //window.location = "{{action('NewPA_Form_Controller@index')}}";


              }
            });


  });


  


   
   




    // ********* Step show event **********************

        
      
      
   });

   

</script>
<!-- end Page script -->



@stop