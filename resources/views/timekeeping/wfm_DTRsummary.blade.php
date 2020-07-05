@extends('layouts.main')

@section('metatags')
<title>WFM DTR Summary Downloads | Open Access EMS</title>
<link rel="stylesheet" type="text/css" href="{{URL::asset('public/js/formBuilder/demo.css')}}">
@endsection

@section('content')


  <!-- Content Header (Page header) -->
    <section class="content-header">
      <h4><i class="fa fa-calendar"></i> Download DTR Summary Template  </h4>
      <ol class="breadcrumb">
        <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li>DTR</li>
        <li class="active">  </li>
      </ol>
    </section>

     <section class="content">


          <div class="row">
            <form id="dtrform" action="{{action('DTRController@finance_dlJPS')}}" method="POST">

            <div class="col-lg-1"></div>
            <div class="col-lg-10" style="background: rgba(255, 255, 255, 0.4);"><BR/><BR/>
              <h3 class="text-primary">1. <span style="font-size: smaller;"> Select cutoff period :</span></h3>
              <select class="form-control" name="cutoff">
                <option value="0">select cutoff</option>
                @foreach($paycutoffs as $p)
                <option value="{{$p->fromDate}}_{{$p->toDate}}">{{date('Y d M',strtotime($p->fromDate))}} to {{date('Y d M',strtotime($p->toDate))}} </option>
                @endforeach
              </select>
              <h3 class="text-primary">2. <span style="font-size: smaller;"> Select DTR Summary :</span></h3>
              <select class="form-control" name="program" disabled="disabled">
                <option value="0">select a template</option>
                @foreach($templates as $p)
                <option value="{{$p['id']}}" data-name="{{$p['name']}}"> {{$p['name']}} </option>
                @endforeach
              </select>
              <br/><br/>

              
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="cutoffstart" />
                <input type="hidden" name="cutoffend" />
                <input type="hidden" name="jpsData" />
                <input type="hidden" name="DTRsummary" value="1" />
                <input type="hidden" name="reportType" id="reportType" value="dailyLogs" />
                <input type="hidden" name="template" id="dltype" />
                
                <!-- <input type="hidden" name="dtr" /> -->

                <div id="loader" class="text-center">
                  <span id="wait" style="font-size: small;" class="text-primary">Fetching EMS data. Please wait...<br/><br/><img src="storage/uploads/loading.gif" width="30" />  </span><br/><br/><br/>
                </div>

              <div class="notes">
                <h4>There are <strong><span class="text-danger" id="submitted"></span> <span class="text-danger" id="programName"></span>  </strong> submissions for this cutoff.<br/> 

               <!--  </h4>

                  <p>Please remind the following employees to have their DTR sheets locked and verified for this cutoff period:</p> -->

                <span style="font-size: smaller;"> Proceed with the DTR summary download?</span></h4>

                <table class="table table-striped" id="team">
                  
                  
                </table>

               
                  
                

                <p class="text-center"> 
                  <button type="submit" class="btn btn-md btn-success" id="dl"><i class="fa fa-save"></i> Download Spreadsheet</button> 
                 
                  

                </p>
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





<!-- Page script -->
<script>



  $(function () {

    
   'use strict';


  $('.notes').fadeOut();
  $('#loader').fadeOut();

  

  

  $('select[name="cutoff"]').change(function(){
    var selval = $(this).find(':selected').val();
    var seldept = $('select[name="program"] :selected').val();
    var selname = $('select[name="program"] :selected').attr('data-name');
    //var selname = $(this).find(':selected').attr('data-name');

    if (selval == 0){

      $.notify("Please select cutoff period to download DTR sheet.",{className:"error",globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );
      return false;

    }else {

      if (seldept == 0){
        $('select[name="program"]').prop('disabled',false);
        $('select[name="program"]').val("0");//  option[value="0"]').attr('selected','selected').change(); 
        $('.notes').fadeOut(); 
        $('#loader').fadeOut();  
      }else{


         // get validated dtr
          var _token = "{{ csrf_token() }}";
          var cutoff = $('select[name="cutoff"]').find(':selected').val();
          $('#loader').fadeIn();

          $.ajax({
                url: "{{action('DTRController@finance_getJPS')}}",
                type:'POST',
                data:{ 
                  'cutoff': cutoff,
                  'template': seldept,
                  'name': selname,
                  'DTRsummary':1,
                  '_token':_token
                },
                success: function(response){
                  console.log(response);

                  $('.notes').fadeIn();
                  $('#loader').fadeOut();
                  $('#submitted').html('('+response.total+')');
                  //$('#total').html('('+response.total+') team members ');
                  $('#programName').html(response.name);
                  $('#jpsData').val(response.OTs);

                  var jpsData = response.OTs;
                  var cutoffstart = response.cutoffstart;
                  var cutoffend = response.cutoffend;

                  $('input[name="template"]').val(seldept);
                  $('input[name="cutoffstart"]').val(cutoffstart);
                  $('input[name="cutoffend"]').val(cutoffend);
                   $('#loader').fadeOut();  
                  

                  

                  
                }
              });


         


      }

        

    }

  }); 

  

  $('#dl').on('click',function(){
      //$('input[name="dltype"]').val(2);
       $.notify("Processing spreadsheet for download.\nPlease wait...",{className:"success", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );
                  
  });

  $('select[name="program"]').change(function(){
    var selval = $(this).find(':selected').val();
    var selname = $(this).find(':selected').attr('data-name');

    if (selval == 0){
      $('.notes').fadeOut();
      $('#loader').fadeOut();

      $.notify("Please select a JPS template to download.",{className:"error",globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );
      return false;

    }else {

       // get validated dtr
      var _token = "{{ csrf_token() }}";
      var cutoff = $('select[name="cutoff"]').find(':selected').val();
      $('#loader').fadeIn();

      $.ajax({
                url: "{{action('DTRController@finance_getJPS')}}",
                type:'POST',
                data:{ 
                  'cutoff': cutoff,
                  'template': selval,
                  'name': selname,
                  'DTRsummary':1,
                  '_token':_token
                },
                success: function(response){
                  console.log(response);

                  $('.notes').fadeIn();
                  $('#loader').fadeOut();
                  $('#submitted').html('('+response.total+')');
                  //$('#total').html('('+response.total+') team members ');
                  $('#programName').html(response.name);
                  $('#jpsData').val(response.OTs);

                  var jpsData = response.OTs;
                  var cutoffstart = response.cutoffstart;
                  var cutoffend = response.cutoffend;

                  $('input[name="template"]').val(selval);
                  $('input[name="cutoffstart"]').val(cutoffstart);
                  $('input[name="cutoffend"]').val(cutoffend);
                  

                  

                  
                }
              });



     

    }

  }); 


      
    


});

   

  
</script>
<!-- end Page script -->


@stop