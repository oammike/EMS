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
        <li class="active"> {{$survey->name}}</li>
      </ol>
    </section>

     <section class="content">


          <div class="row">
            <div class="col-lg-1"></div>
            <div class="col-lg-10" style="background: rgba(255, 255, 255, 0.4);"><BR/><BR/>
              <p>Hello {{$user}}, <br/><br/>
                In line with our company’s renewed thrust towards establishing standards for effective leadership - we will soon launch the <strong>LEAD (Learn, Engage, Adapt, and Disrupt) Training Series</strong>. This is Open Access' pilot learning and development program that seeks to develop our employees.</p>
              <p>Our initial focus will be on the development of our Team Leaders and other front line support staff. We need you to answer this leadership assessment survey. Your evaluation of your direct reports’ current skill level on different leadership aspects is critical in identifying our training needs and priorities. This will be the basis for designing and developing course materials for training and workshop sessions.  </p>

              <p>Here are some things to help you:</p>
              <ul>
                <li>Scale Values: 6 highest; 1 lowest.</li>
                <li>Importance Scale - refers to how important you think skill/item is.</li>
                <li>Competence Scale - refers to your evaluation of the current skill level of your direct report.</li>
                <li>There are 10 leadership skills or behaviors included in this survey. If you believe there are certain skill/behavior gaps that need to be addressed that are not included here, then please add it in the free form text field at the end of the form.</li>
                <li>You need to submit separate evaluations for all your direct reports.</li>
              </ul>

              @foreach($allCamp as $c)
               <!-- ******** collapsible box ********** -->
               <?php $doneUser = collect($surveyUser)->where('surveyFor',$c->userID)->where('isDone',1);
                    $hasComment = collect($allComments)->where('surveyFor',$c->userID); ?>
                <div class="box box-default collapsed-box" id="emp_{{$c->userID}}">
                  <div class="box-header with-border">
                    <img src="{{asset('public/img/employees/'.$c->userID.'.jpg')}}" class="user-image" alt="User Image" width="50">
                    
                    @if (count($doneUser) > 0) 
                    <h3 class="box-title text-gray">
                    @else
                    <h3 class="box-title text-primary">
                    @endif

                      @if(is_null($c->nickname)) 
                      {{$c->lastname}}, {{$c->firstname}}<br/>
                      <span style="font-size: small;">{{$c->jobTitle}} | {{$c->program}}</span>
                      
                      @else
                      {{$c->lastname}}, {{$c->nickname}}<br/>
                       <span style="font-size: small;">{{$c->jobTitle}} | {{$c->program}}</span>
                     
                      @endif


                      @if (count($doneUser) > 0) 
                      <small id="evaluated-{{$c->userID}}"><i class="fa fa-check text-primary"></i></small> 
                      @else
                      <small id="evaluated-{{$c->userID}}"><i class="fa fa-exclamation-circle text-orange"></i></small> 
                      @endif
                    </h3>



                    <div class="box-tools pull-right">
                      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                      </button>
                    </div>
                    <!-- /.box-tools -->
                  </div>
                  <!-- /.box-header -->
                  <div class="box-body">

                     <table class="table" style="margin-top: 20px; width: 100%">
                        <tr>
                          <th class="text-primary" >List of Skills/Behavior</th>
                          <th class="text-primary text-center" width="23%">Importance</th>
                          <th class="text-primary text-center" width="23%">Competence</th>
                        </tr>

                        @foreach($questions as $q)
                        <tr>
                          <td colspan="3" style="font-size: larger; font-weight: bold; text-transform: uppercase;">{{$q[0]->label}} </td>
                        </tr>

                           <?php $su = collect($surveyUser)->where('surveyFor',$c->userID);
                                  (count($su) >= 1) ? $surveyUserID = $su->first()->id : $surveyUserID=0; 
                                  //$surveyUserID = count($su);
                                  ?>

                          @foreach($q as $qs)
                          <tr>
                            <td style="padding-left: 20px">{!! $qs->question !!}</td>
                            <td>
                              <select @if(count($doneUser) > 0) disabled="disabled" @endif name="importance_{{$qs->id}}" id="importance_{{$qs->id}}" class="form-control imp pull-left" data-entryID="{{$qs->id}}" data-for="{{$c->userID}}" data-surveyUserID="{{$surveyUserID}}" data-optiontype="1" style="width: 80%">
                                <option value="0">select importance</option>
                                @foreach($importance as $imp)

                                  <?php $existingval = collect($allAnswers)->where('surveyFor',$c->userID)->where('question_id',$qs->id)->where('optionType',1);

                                  if (count($existingval) > 0) {?>
                                    <option value="{{$imp->value}}" @if( $existingval->first()->value == $imp->value  ) selected="selected" @endif data-optionsid="{{$imp->id}}">{{$imp->label}} </option>

                                    <?php } else { ?>
                                    <option value="{{$imp->value}}" data-optionsid="{{$imp->id}}">{{$imp->label}} </option>  
                                    <?php } ?>
                                @endforeach
                                
                              </select>

                               <?php $existingval = collect($allAnswers)->where('surveyFor',$c->userID)->where('question_id',$qs->id)->where('optionType',1); ?>
                               @if(count($existingval) > 0)
                                <div id="rated_{{$qs->id}}_{{$c->userID}}">&nbsp;&nbsp; <i class="fa fa-check text-success"></i></div>
                                @else
                                <div id="rated_{{$qs->id}}_{{$c->userID}}">&nbsp;&nbsp; <i class="fa fa-exclamation-circle text-yellow"></i></div>
                                @endif
                              
                            </td>
                            <td>
                              <select  @if(count($doneUser) > 0) disabled="disabled" @endif name="competence{{$qs->id}}" id="competence{{$qs->id}}" class="form-control comp pull-left" data-entryID="{{$qs->id}}" data-for="{{$c->userID}}"  data-surveyUserID="{{$surveyUserID}}" data-optiontype="2"   style="width: 80%">
                                <option value="0">select competence</option>
                                @foreach($competence as $imp)

                                <?php $existingval = collect($allAnswers)->where('surveyFor',$c->userID)->where('question_id',$qs->id)->where('optionType',2);

                                  if (count($existingval) > 0) {?>
                                    <option value="{{$imp->value}}" @if( $existingval->first()->value == $imp->value  ) selected="selected" @endif data-optionsid="{{$imp->id}}">{{$imp->label}} </option>

                                    <?php } else { ?>
                                    <option value="{{$imp->value}}" data-optionsid="{{$imp->id}}">{{$imp->label}} </option>  
                                    <?php } ?>

                                @endforeach
                                
                              </select>

                              <?php $existingval = collect($allAnswers)->where('surveyFor',$c->userID)->where('question_id',$qs->id)->where('optionType',2); ?>
                               @if(count($existingval) > 0)
                                <div id="ratedcomp_{{$qs->id}}_{{$c->userID}}">&nbsp;&nbsp; <i class="fa fa-check text-success"></i></div>
                                @else
                                <div id="ratedcomp_{{$qs->id}}_{{$c->userID}}">&nbsp;&nbsp; <i class="fa fa-exclamation-circle text-yellow"></i></div>
                                @endif
                              

                            </td>
                          </tr>
                          @endforeach


                        @endforeach
                        <tr><td colspan="3"><label>Notes / Comments</label></td></tr>
                        <tr>
                          <td colspan="3" class="text-right">
                            <?php $su = collect($surveyUser)->where('surveyFor',$c->userID);
                                  (count($su) >= 1) ? $surveyUserID = $su->first()->id : $surveyUserID=0; 
                                  //$surveyUserID = count($su);
                                  ?>
                            <textarea name="notes" id="notes_{{$c->userID}}" class="form-control" style="margin:10px">@if(count($hasComment) > 0){!! $hasComment->first()->comments !!}@endif</textarea>

                            @if (count($doneUser) > 0) 
                            <a aria-disabled="true" class="submit btn disabled btn-lg" data-surveyUserID="{{$surveyUserID}}" data-for="{{$c->userID}}" data-employee="{{$c->firstname}} {{$c->lastname}} ">Submit</a> 
                            @else
                            <a class="submit btn btn-success btn-lg" data-surveyUserID="{{$surveyUserID}}" data-for="{{$c->userID}}" data-employee="{{$c->firstname}} {{$c->lastname}} ">Submit</a> 
                            @endif
                          </td>
                        </tr>


                    </table>
                  </div>
                  <!-- /.box-body -->
                </div>
                <!-- ******** end collapsible box ********** -->

              @endforeach

             
                
               


            </div>
           <div class="col-lg-1"></div>


            

            

          </div><!-- end row -->





       
     </section>
          



@endsection


@section('footer-scripts')

<script>
  $.widget.bridge('uibutton', $.ui.button);
</script>


<script type="text/javascript" src="{{asset('public/js/morris.min.js')}}"></script>
<script type="text/javascript" src="{{asset('public/js/raphael.min.js')}}"></script>
<script type="text/javascript" src="{{asset('public/js/intro.js')}}"></script>


<!-- Page script -->
<script>



  $(function () {

    
   'use strict';


 

  $('.submit.btn.btn-success.btn-lg').on('click',function(){

    var item = $(this).attr('data-item');
    var surveyfor = $(this).attr('data-for');
    var empname = $(this).attr('data-employee');
    var survey_userid = $(this).attr('data-surveyUserID');
    var missedItems = $('#emp_'+surveyfor+' table .fa.fa-exclamation-circle.text-yellow').length;
    var _token = "{{ csrf_token() }}";
    //console.log(missedItems);
    //console.log('for: '+datafor);

    var notYetRated = $('.fa.fa-exclamation-circle.text-yellow').length;


    if (missedItems > 0) {
     $.notify("You missed "+missedItems+ " item(s) for "+empname+". \nFilling out the form will help us gather needed data to improve our training course.",{className:"error",globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );
    }
    else{

      $('#evaluated-'+surveyfor).html('<i class="fa fa-check text-success"></i>');
      //$('div#emp_'+datafor).addClass("box box-default collapsed-box");
      $('div#emp_'+surveyfor+' button.btn.btn-box-tool').trigger("click");
      $('div#emp_'+surveyfor+' h3').removeClass('text-primary').addClass('text-gray');
      $('div#emp_'+surveyfor+' a.submit.btn.btn-success.btn-lg').fadeOut();
      $('div#emp_'+surveyfor+' select').prop('disabled','disabled');

       //get if may existing comment
      var comment = $('#notes_'+surveyfor).val();


      // SAVE ITEM
      $.ajax({
                url: "{{action('SurveyController@saveItem')}}",
                type:'POST',
                data:{ 
                  'questionid': 1,
                  'survey_optionsid': 0,
                  'optiontype': 'submit',
                  'surveytype':"360",
                  'survey_id':"{{$id}}",
                  "survey_userid" : survey_userid,
                  "surveyfor": surveyfor,
                  'comment': comment,
                  '_token':_token
                },
                success: function(response){
                  console.log(response);
                  $.notify("Data for "+empname + " saved successfully! \nThank you for participating in this survey.",{className:"success",globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );
                  return false;

                  
                }
              });



      




    }


    

    
      

  });

  // ****** IMPORTANCE *********
  $('select[class="form-control imp pull-left"]').change(function(){   

    var selval = $(this).find(':selected').val(); // $(this).val();
    var id = $(this).attr('data-entryID');
    var survey_optionsid = $(this).find(':selected').attr('data-optionsid');
    var surveyfor = $(this).attr('data-for');
    var survey_userid = $(this).attr('data-surveyUserID');
    var optiontype = $(this).attr('data-optiontype');
    var _token = "{{ csrf_token() }}";



    if (selval == 0)
    {
      $("#rated_"+id+"_"+surveyfor).html('&nbsp;&nbsp; <i class="fa fa-exclamation-circle text-yellow"></i>');
      console.log('rated '+ id);
      $.notify("Please choose an option. \nFilling out the form will help us gather needed data to improve our training materials",{className:"error",globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );
      return false;
      
    }else{
      $("#rated_"+id+"_"+surveyfor).html('&nbsp;&nbsp; <i class="fa fa-check text-success"></i>');

      // SAVE ITEM
      $.ajax({
                url: "{{action('SurveyController@saveItem')}}",
                type:'POST',
                data:{ 
                  'questionid': id,
                  'survey_optionsid': survey_optionsid,
                  'optiontype': optiontype,
                  'surveytype':"360",
                  'survey_id':"{{$id}}",
                  "survey_userid" : survey_userid,
                  "surveyfor": surveyfor,
                  'comment': '',
                  '_token':_token
                },
                success: function(response){
                  console.log(response);

                  
                }
              });



    }
  });

 // ****** COMPETENCE *********
  $('select[class="form-control comp pull-left"]').change(function(){ 


    var selval = $(this).find(':selected').val(); // $(this).val();
    var id = $(this).attr('data-entryID');
    var survey_optionsid = $(this).find(':selected').attr('data-optionsid');
    var optiontype = $(this).attr('data-optiontype');
    var surveyfor = $(this).attr('data-for');
    var survey_userid = $(this).attr('data-surveyUserID');

    var _token = "{{ csrf_token() }}";



    if (selval == 0)
    {
      $("#ratedcomp_"+id+"_"+surveyfor).html('&nbsp;&nbsp; <i class="fa fa-exclamation-circle text-yellow"></i>');
      console.log('rated '+ id);
      $.notify("Please choose an option. \nFilling out the form will help us gather needed data to improve our training materials",{className:"error",globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );
      return false;
      
    }else{
      $("#ratedcomp_"+id+"_"+surveyfor).html('&nbsp;&nbsp; <i class="fa fa-check text-success"></i>');

      // SAVE ITEM
      $.ajax({
                url: "{{action('SurveyController@saveItem')}}",
                type:'POST',
                data:{ 
                  'questionid': id,
                  'survey_optionsid': survey_optionsid,
                  'optiontype': optiontype,
                  'surveytype':"360",
                  'survey_id':"{{$id}}",
                  "survey_userid" : survey_userid,
                  "surveyfor": surveyfor,
                  'comment': '',
                  '_token':_token
                },
                success: function(response){
                  console.log(response);

                  
                }
              });



    }  

    
  });

 

   


      
    


   });

   

  
</script>
<!-- end Page script -->


@stop