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
            <div class="col-lg-10" style="background: rgba(255, 255, 255, 0.4);">
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
                <div class="box box-default collapsed-box" id="emp_{{$c->userID}}">
                  <div class="box-header with-border">
                    <img src="{{asset('public/img/employees/'.$c->userID.'.jpg')}}" class="user-image" alt="User Image" width="50">
                    <h3 class="box-title text-primary">
                      @if(is_null($c->nickname)) 
                      {{$c->lastname}}, {{$c->firstname}}<br/>
                      <span style="font-size: small;">{{$c->jobTitle}} | {{$c->program}}</span>
                      <small id="evaluated-{{$c->userID}}"><i class="fa fa-exclamation-circle text-orange"></i></small> 
                      @else
                      {{$c->lastname}}, {{$c->nickname}}<br/>
                       <span style="font-size: small;">{{$c->jobTitle}} | {{$c->program}}</span>
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
                          @foreach($q as $qs)
                          <tr>
                            <td style="padding-left: 20px">{!! $qs->question !!}</td>
                            <td>
                              <select name="importance_{{$qs->id}}" id="importance_{{$qs->id}}" class="form-control imp pull-left" data-entryID="{{$qs->id}}" data-for="{{$c->userID}}" style="width: 80%">
                                <option value="0">select importance</option>
                                @foreach($importance as $imp)
                                <option value="{{$imp->value}}">{{$imp->label}} </option>
                                @endforeach
                                
                              </select>
                              <div id="rated_{{$qs->id}}_{{$c->userID}}">&nbsp;&nbsp; <i class="fa fa-exclamation-circle text-yellow"></i></div>
                              
                            </td>
                            <td>
                              <select name="competence{{$qs->id}}" id="competence{{$qs->id}}" class="form-control comp pull-left" data-entryID="{{$qs->id}}" data-for="{{$c->userID}}" style="width: 80%">
                                <option value="0">select competence</option>
                                @foreach($competence as $imp)
                                <option value="{{$imp->value}}">{{$imp->label}} </option>
                                @endforeach
                                
                              </select>
                              <div id="ratedcomp_{{$qs->id}}_{{$c->userID}}">&nbsp;&nbsp; <i class="fa fa-exclamation-circle text-yellow"></i></div>
                              

                            </td>
                          </tr>
                          @endforeach


                        @endforeach
                        <tr><td colspan="3"><label>Notes / Comments</label></td></tr>
                        <tr>
                          <td colspan="3" class="text-right">
                            <textarea name="comments" class="form-control" style="margin:10px"></textarea>
                            <a class="submit btn btn-success btn-lg" data-for="{{$c->userID}}" data-employee="{{$c->firstname}} {{$c->lastname}} ">Submit</a> </td>
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


 

      // hide submit button if done with survey
      @if($userSurvey->isDone)
        $('#submit').hide();
      @endif








  $('.submit.btn.btn-success.btn-lg').on('click',function(){

    var item = $(this).attr('data-item');
    var datafor = $(this).attr('data-for');
    var empname = $(this).attr('data-employee');
    var missedItems = $('#emp_'+datafor+' table .fa.fa-exclamation-circle.text-yellow').length;
    console.log(missedItems);
    console.log('for: '+datafor);

    var notYetRated = $('.fa.fa-exclamation-circle.text-yellow').length;


    if (missedItems > 0) {
     $.notify("You missed "+missedItems+ " item(s) for "+empname+". \nFilling out the form will help us gather needed data to improve our training course.",{className:"error",globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );
    }
    else{

      $('#evaluated-'+datafor).html('<i class="fa fa-check text-success"></i>');
      //$('div#emp_'+datafor).addClass("box box-default collapsed-box");
      $('div#emp_'+datafor+' button.btn.btn-box-tool').trigger("click");
      $('div#emp_'+datafor+' h3').removeClass('text-primary').addClass('text-gray');
      $('div#emp_'+datafor+' a.submit.btn.btn-success.btn-lg').fadeOut();
      $('div#emp_'+datafor+' select').prop('disabled','disabled');

      $.notify("Data for "+empname + " saved successfully! \nThank you for participating in this survey.",{className:"success",globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );
        return false;

/*
      // we now save his answer
      var _token = "{{ csrf_token() }}";
      var questionid = $(this).attr('data-questionid');
      var survey_optionsid = $('input[name="answer'+questionid+'"]:checked').val();
      console.log("questionid: " + questionid);
      



      //check first required values
      if ( ($('input[name="answer'+questionid+'"]:checked').length > 0) )
      {
        
        //get if may existing comment
        var comment = $('#notes_q'+questionid).val();
      
      } else{

        $.notify("Please choose an option. \nFilling out the form will help us gather needed data to improve our training course.",{className:"error",globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );
        return false;

      }*/


    }


    

    
      

  });

  $('select[class="form-control imp pull-left"]').change(function(){   

    var selval = $(this).find(':selected').val(); // $(this).val();
    var id = $(this).attr('data-entryID');
    var datafor = $(this).attr('data-for');

    if (selval == 0)
    {
      $("#rated_"+id+"_"+datafor).html('&nbsp;&nbsp; <i class="fa fa-exclamation-circle text-yellow"></i>');
      console.log('rated '+ id);
      $.notify("Please choose an option. \nFilling out the form will help us gather needed data to improve our training materials",{className:"error",globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );
      return false;
      
    }else{
      $("#rated_"+id+"_"+datafor).html('&nbsp;&nbsp; <i class="fa fa-check text-success"></i>');

    }
  });

  $('select[class="form-control comp pull-left"]').change(function(){   

    var selval = $(this).find(':selected').val(); // $(this).val();
    var id = $(this).attr('data-entryID');
    var datafor = $(this).attr('data-for');

    if (selval == 0)
    {
      $("#ratedcomp_"+id+"_"+datafor).html('&nbsp;&nbsp; <i class="fa fa-exclamation-circle text-yellow"></i>');
      console.log('rated '+ id);
      $.notify("Please choose an option. \nFilling out the form will help us gather needed data to improve our training materials",{className:"error",globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );
      return false;
      
    }else{
      $("#ratedcomp_"+id+"_"+datafor).html('&nbsp;&nbsp; <i class="fa fa-check text-success"></i>');

    }
  });

 

   


      
    


   });

   

  
</script>
<!-- end Page script -->


@stop