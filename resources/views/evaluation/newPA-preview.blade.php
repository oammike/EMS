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
                            <td>Points</td>
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
                                  <select name="rating_{{$goal[0]->goalID}}" class="form-control">
                                    <option value="0">* select rating *</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                  </select>
                                </td>
                                <td></td>
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
                          <th width="30%">Critical Incidents</th>
                         
                          <th>Weight</th>
                          <th width="10%">Rating</th>
                          <th>Points</th>
                        </tr>
                      </thead>
                      <tbody>

                        @foreach($allCompetencies as $comp)
                        <tr>
                          <td style="font-size: larger">
                            {{$comp[0]->competency}}<br/><br/>

                            <div style="font-size: smaller; width: 90%">
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
                          
                         
                          <td>
                            <label>Strengths </label><textarea rows="7" id="strengths_{{$comp[0]->competencyID}}" class="form-control"></textarea><br/><br/>
                            <label>Areas For Improvement </label><textarea rows="7" id="afi_{{$comp[0]->competencyID}}" class="form-control"></textarea>
                          </td>
                          <td>{{$comp[0]->competencyWeight}} %</td>
                          <td>
                            <select name="ratingComp_{{$comp[0]->competencyID}}" class="form-control">
                                    <option value="0">* select rating *</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                  </select>
                          </td>
                          <td></td>
                        </tr>

                        @endforeach
                        

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
      case '1': txt_strength.append("*** "+ reptxt.replace('                              ','')); break;
      case '2': txt_afi.append("*** "+ reptxt.replace('                              ','')); break;
    }



     $('p#desc_'+item).fadeOut();
   

  });


  


   
   




    // ********* Step show event **********************

        
      
      
   });

   

</script>
<!-- end Page script -->



@stop