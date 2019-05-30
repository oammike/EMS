@extends('layouts.main')

@section('metatags')
<title>DTR Sheet | Open Access EMS</title>
<link rel="stylesheet" type="text/css" href="{{URL::asset('public/js/formBuilder/demo.css')}}">
@endsection

@section('content')


  <!-- Content Header (Page header) -->
    <section class="content-header">
      <h4><i class="fa fa-calendar-o"></i> DTR Sheet  </h4>
      <ol class="breadcrumb">
        <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li>DTR</li>
        <li class="active">  </li>
      </ol>
    </section>

     <section class="content">


          <div class="row">
            <form id="dtrform" action="{{action('DTRController@downloadDTRsheet')}}" method="POST">
            <div class="col-lg-1"></div>
            <div class="col-lg-10" style="background: rgba(255, 255, 255, 0.4);"><BR/><BR/>
              <h3 class="text-primary">1. <span style="font-size: smaller;"> Select cutoff period :</span></h3>
              <select class="form-control" name="cutoff">
                <option value="0">select cutoff</option>
                @foreach($paycutoffs as $p)
                <option value="{{$p->fromDate}}_{{$p->toDate}}">{{date('Y d M',strtotime($p->fromDate))}} to {{date('Y d M',strtotime($p->toDate))}} </option>
                @endforeach
              </select>
              <h3 class="text-primary">2. <span style="font-size: smaller;"> Select department/program :</span></h3>
              <select class="form-control" name="program" disabled="disabled">
                <option value="0">select program</option>
                @foreach($allProgram as $p)
                <option value="{{$p->id}}"> {{$p->name}} </option>
                @endforeach
              </select>
              <br/><br/>

              
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="cutoffstart" />
                <input type="hidden" name="cutoffend" />
                
                <!-- <input type="hidden" name="dtr" /> -->


              <div class="notes">
                <h4>There are <strong><span class="text-danger" id="submitted"></span> out of <span class="text-primary" id="total"> team members</span> </strong> under <span id="programName" style="font-style: italic;"></span>   who have validated DTR sheets for that cutoff period.<br/> 

               <!--  </h4>

                  <p>Please remind the following employees to have their DTR sheets locked and verified for this cutoff period:</p> -->

                <span style="font-size: smaller;"> Proceed with the DTR download?</span></h4>

               
                  
                

                <p class="text-center"> <button type="submit" class="btn btn-md btn-success" id="dl"><i class="fa fa-save"></i> Download DTR sheets</button> </p>
              </div>
              
             
                
               

              </form>

             
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


  $('.notes').fadeOut();

  $('#zendesk').on('click',function(){
    var _token = "{{ csrf_token() }}";

    $.ajax({
                    url: "{{action('DTRController@zendesk')}}",
                    type:'POST',
                    data:{ 
                      'url': "https://circlesasiasupport.zendesk.com/api/v2/groups.json",
                      'method': "GET",
                      '_token':_token
                    },
                    success: function(response){
                      console.log(response);
                      
                      
                    }
                  });

  });

  

  $('select[name="cutoff"]').change(function(){
    var selval = $(this).find(':selected').val();
    var seldept = $('select[name="program"] :selected').val();

    if (selval == 0){

      $.notify("Please select cutoff period to download DTR sheet.",{className:"error",globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );
      return false;

    }else {

      if (seldept == 0){
        $('select[name="program"]').prop('disabled',false);
        $('select[name="program"]').val("0");//  option[value="0"]').attr('selected','selected').change(); 
        $('.notes').fadeOut();   
      }else{


         // get validated dtr
          var _token = "{{ csrf_token() }}";
          var cutoff = $('select[name="cutoff"]').find(':selected').val();


          $.ajax({
                    url: "{{action('DTRController@getValidatedDTRs')}}",
                    type:'POST',
                    data:{ 
                      'cutoff': cutoff,
                      'program': seldept,
                      '_token':_token
                    },
                    success: function(response){
                      //console.log(response);
                      $('.notes').fadeIn();
                      $('#submitted').html('('+response.submitted+')');
                      $('#total').html('('+response.total+') team members ');
                      $('#programName').html(response.program);

                      var rdata = [];

                      $('.notes').fadeIn();
                      $('#submitted').html('('+response.submitted+')');
                      $('#total').html('('+response.total+') team members ');
                      $('#programName').html(response.program);

                      var rdata = response.DTRs;
                      var cutoffstart = response.cutoffstart;
                      var cutoffend = response.cutoffend;
                      var program = response.program;
                      //console.log("array data:");
                      console.log(rdata);

                      //$('input[name="dtr"]').val(jQuery.param(rdata));
                      $('input[name="cutoffstart"]').val(cutoffstart);
                      $('input[name="cutoffend"]').val(cutoffend);
                      
                    }
                  });


      }

        

    }

  }); 


  $('select[name="program"]').change(function(){
    var selval = $(this).find(':selected').val();

    if (selval == 0){
      $('.notes').fadeOut();

      $.notify("Please select department/program to download DTR sheet.",{className:"error",globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );
      return false;

    }else {

       // get validated dtr
      var _token = "{{ csrf_token() }}";
      var cutoff = $('select[name="cutoff"]').find(':selected').val();


      $.ajax({
                url: "{{action('DTRController@getValidatedDTRs')}}",
                type:'POST',
                data:{ 
                  'cutoff': cutoff,
                  'program': selval,
                  '_token':_token
                },
                success: function(response){
                  console.log(response);

                  $('.notes').fadeIn();
                  $('#submitted').html('('+response.submitted+')');
                  $('#total').html('('+response.total+') team members ');
                  $('#programName').html(response.program);

                  var rdata = [];

                      $('.notes').fadeIn();
                      $('#submitted').html('('+response.submitted+')');
                      $('#total').html('('+response.total+') team members ');
                      $('#programName').html(response.program);

                      var rdata = response.DTRs;
                      var cutoffstart = response.cutoffstart;
                      var cutoffend = response.cutoffend;
                      var program = response.program;
                      //console.log("array data:");
                      console.log(rdata);

                      //$('input[name="dtr"]').val(jQuery.param(rdata));
                      $('input[name="cutoffstart"]').val(cutoffstart);
                      $('input[name="cutoffend"]').val(cutoffend);
                      //$('input[name="program"]').val(program);
                    

                       // $('#dl').on('click',function(){
                       //  console.log("clicked");

                       //  $.ajax({
                       //    url: "{{action('DTRController@downloadDTRsheet')}}",
                       //    type:'POST',
                       //    data:{ 
                       //      'dtr': rdata,
                       //      'cutoffstart': cutoffstart,
                       //      'cutoffend': cutoffend,
                       //      'program': program,
                       //      '_token':_token
                       //    },
                       //    success: function(response){
                       //      //console.log(response);

                       //      $('#dl').fadeOut();
                            

                            
                       //    }

                       //  });

                       // });

                  
                }
              });



     

    }

  }); 


      
    


});

   

  
</script>
<!-- end Page script -->


@stop