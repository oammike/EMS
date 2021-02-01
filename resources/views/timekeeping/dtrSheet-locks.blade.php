@extends('layouts.main')

@section('metatags')
<title>Lock Report: DTR Sheet | Open Access EMS</title>
<link rel="stylesheet" type="text/css" href="{{URL::asset('public/js/formBuilder/demo.css')}}">
@endsection

@section('content')


  <!-- Content Header (Page header) -->
    <section class="content-header">
      <h4><i class="fa fa-calendar-o"></i> DTR Sheet Lock Report  </h4>
      <ol class="breadcrumb">
        <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li>DTR</li>
        <li class="active">  </li>
      </ol>
    </section>

     <section class="content">


          <div class="row">
            <form id="dtrform" action="{{action('DTRController@downloadDTRLockReport')}}" method="POST">
            <div class="col-lg-1"></div>
            <div class="col-lg-10" style="background: rgba(255, 255, 255, 0.4);"><BR/><BR/>
              <h3 class="text-primary">1. <span style="font-size: smaller;"> Select cutoff period :</span></h3>
              <select class="form-control" name="cutoff">
                <option value="0">select cutoff</option>
                @foreach($paycutoffs as $p)
                <option value="{{$p->fromDate}}_{{$p->toDate}}">{{date('Y d M',strtotime($p->fromDate))}} to {{date('Y d M',strtotime($p->toDate))}} </option>
                @endforeach
              </select>
              
              <br/><br/>

              
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="cutoffstart" />
                <input type="hidden" name="cutoffend" />
                
                <!-- <input type="hidden" name="dtr" /> -->
              <div id="loader" class="text-center">
                  <span id="wait" style="font-size: small;" class="text-primary">Fetching DTR data. Please wait...<br/><br/><img src="storage/uploads/loading.gif" width="30" />  </span><br/><br/><br/>
              </div>


              <div class="notes">
               

              <p class="text-center"> 
                 <button type="submit" class="btn btn-md btn-success" id="dl"><i class="fa fa-save"></i> Download DTR Lock Report</button>
                  

                </p>

                <table class="table table-hover" id="team">
                  
                  
                </table>

               
                  
                

                
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
  $('#loader').fadeOut();

  

  

  $('select[name="cutoff"]').change(function(){
    var selval = $(this).find(':selected').val();
    
    if (selval == 0){

      $.notify("Please select cutoff period to download DTR sheet.",{className:"error",globalPosition:'top right',autoHideDelay:7000, clickToHide:true} );
      return false;

    }else {

      


         // get validated dtr
          var _token = "{{ csrf_token() }}";
          var cutoff = $('select[name="cutoff"]').find(':selected').val();

          $('#loader').fadeIn();


          $.ajax({
                    url: "{{action('DTRController@getAllNotLocked')}}",
                    type:'POST',
                    data:{ 
                      'c': cutoff,
                      
                      '_token':_token
                    },
                    success: function(response){
                      //console.log(response);
                      $('.notes').fadeIn();
                      

                      var rdata = [];

                      $('.notes').fadeIn();
                      $('#loader').fadeOut();

                     

                      //var rdata = response.deets;
                      console.log(response);
                      var cutoffstart = response[0]['cutoffstart'];
                      var cutoffend = response[0]['cutoffend'];
                      var totaldays = response[0]['totaldays'];
                      // var program = response.program;
                      // var members = response.users;
                      //console.log("array data:");
                      console.log(rdata);

                      //$('input[name="dtr"]').val(jQuery.param(rdata));
                      $('input[name="cutoffstart"]').val(cutoffstart);
                      $('input[name="cutoffend"]').val(cutoffend);

                      $('#team').html('');

                      var htmltags="<thead><th>Employee</th><th>Program</th><th class='text-right'>Locked DTR entries</th><th></th></thead>";// "<tr>";

                      if (response.length > 1)
                      {
                        //var totalDTR = response.payrollPeriod.length;

                        for(var i = 0; i < response.length; i++)
                        {

                          var deets = response[i]['deets'];
                          var count = response[i]['count']; //rdata.filter((obj) => obj.id === userid).length;


                          

                            htmltags += "<tr><td>"+(i+1)+". "+ deets['lastname']+", "+deets['firstname']+"</td>";
                            htmltags += "<td>"+ deets['program']+"</td>";
                            htmltags += "<td class='text-right'>"+ count +" / " + totaldays+"</td>";
                            htmltags += "<td class='text-center'><a target='_blank' href='./user_dtr/"+deets['user_id']+"?from="+cutoffstart+"&to="+cutoffend+"'  class='btn btn-xs btn-primary'><i class='fa fa-calendar-o'></i> View DTR </a></td></tr>";
                          

                          

                        }
                        htmltags += "</table>";

                      }

                      
                      

                      $('#team').html(htmltags)
                      
                    }
                  });


      

        

    }

  }); 

  $('#dl').on('click',function(){
      $('input[name="dltype"]').val(1);
  });

 


      
    


});

   

  
</script>
<!-- end Page script -->


@stop