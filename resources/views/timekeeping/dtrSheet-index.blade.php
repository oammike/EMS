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
              <div id="loader" class="text-center">
                  <span id="wait" style="font-size: small;" class="text-primary">Fetching DTR data. Please wait...<br/><br/><img src="storage/uploads/loading.gif" width="30" />  </span><br/><br/><br/>
              </div>


              <div class="notes">
                <h4>There are <strong><span class="text-danger" id="submitted"></span> out of <span class="text-primary" id="total"> team members</span> </strong> under <span id="programName" style="font-style: italic;"></span>   who have validated DTR sheets for that cutoff period.<br/> 

               <!--  </h4>

                  <p>Please remind the following employees to have their DTR sheets locked and verified for this cutoff period:</p> -->

                <span style="font-size: smaller;"> Proceed with the DTR download?</span></h4>

                <table class="table table-striped" id="team">
                  
                  
                </table>

               
                  
                

                <p class="text-center"> 
                  <button type="submit" class="btn btn-md btn-success" id="dl"><i class="fa fa-save"></i> Download DTR sheets</button> 
                  <input type="hidden" name="dltype" id="dltype" />
                  <button type="submit" class="btn btn-md btn-primary" id="dl2"><i class="fa fa-save"></i> Download Billables</button>

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


<script type="text/javascript" src="{{asset('public/js/morris.min.js')}}"></script>
<script type="text/javascript" src="{{asset('public/js/raphael.min.js')}}"></script>
<script type="text/javascript" src="{{asset('public/js/intro.js')}}"></script>


<!-- Page script -->
<script>



  $(function () {

    
   'use strict';


  $('.notes').fadeOut();
  $('#loader').fadeOut();

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

          $('#loader').fadeIn();


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
                      $('#loader').fadeOut();

                      $('#submitted').html('('+response.submitted+')');
                      $('#total').html('('+response.total+') team members ');
                      $('#programName').html(response.program);

                      var rdata = response.DTRs;
                      var cutoffstart = response.cutoffstart;
                      var cutoffend = response.cutoffend;
                      var program = response.program;
                      var members = response.users;
                      //console.log("array data:");
                      console.log(rdata);

                      //$('input[name="dtr"]').val(jQuery.param(rdata));
                      $('input[name="cutoffstart"]').val(cutoffstart);
                      $('input[name="cutoffend"]').val(cutoffend);

                      $('#team').html('');

                      var htmltags="<tr><th>Employee</th><th>Immediate Head</th><th class='text-right'>Locked DTR entries</th><th></th></tr>";// "<tr>";

                      if (members.length > 1)
                      {
                        var totalDTR = response.payrollPeriod.length;

                        for(var i = 0; i < members.length; i++)
                        {

                          var userid = members[i]['id'];
                          var count = rdata.filter((obj) => obj.id === userid).length;


                          if (count == totalDTR ){
                            htmltags += "<tr style='font-weight:bold; background: rgba(255, 255, 255, 0.5);' class='text-success'><td>"+(i+1)+". "+ members[i]['lastname']+", "+members[i]['firstname']+"<br/><small style='font-weight:normal' class='text-primary'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"+members[i]['jobTitle']+"</small></td>";
                            htmltags += "<td>"+ members[i]['leaderFname']+" "+ members[i]['leaderLname'] +"</td>";
                            htmltags += "<td class='text-right'>"+ count +" / "+ totalDTR +"</td>";
                            htmltags += "<td class='text-center'><a target='_blank' href='./user_dtr/"+members[i]['id']+"?from="+cutoffstart+"&to="+cutoffend+"'  class='btn btn-xs btn-default'><i class='fa fa-calendar-o'></i> View DTR </a></td></tr>";

                          }
                          
                          else{

                            htmltags += "<tr><td>"+(i+1)+". "+ members[i]['lastname']+", "+members[i]['firstname']+"<br/><small style='font-weight:normal' class='text-primary'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"+members[i]['jobTitle']+"</small></td>";
                            htmltags += "<td>"+ members[i]['leaderFname']+" "+ members[i]['leaderLname'] +"</td>";
                            htmltags += "<td class='text-right'>"+ count +" / "+ totalDTR +"</td>";
                            htmltags += "<td class='text-center'><a target='_blank' href='./user_dtr/"+members[i]['id']+"?from="+cutoffstart+"&to="+cutoffend+"'  class='btn btn-xs btn-default'><i class='fa fa-calendar-o'></i> View DTR </a></td></tr>";
                          }

                          

                        }
                        htmltags += "</table>";

                      }else{
                        htmltags += "<td>"+ members[i]['lastname']+", "+members[i]['firstname']+"</td></tr>";

                      }
                      console.log(members.length);

                      
                      

                      $('#team').html(htmltags)
                      
                    }
                  });


      }

        

    }

  }); 

  $('#dl').on('click',function(){
      $('input[name="dltype"]').val(1);
  });

  $('#dl2').on('click',function(){
      $('input[name="dltype"]').val(2);
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
                      var members = response.users;
                      //console.log("array data:");
                      console.log(rdata);

                      //$('input[name="dtr"]').val(jQuery.param(rdata));
                      $('input[name="cutoffstart"]').val(cutoffstart);
                      $('input[name="cutoffend"]').val(cutoffend);

                      $('#team').html('');

                      var htmltags="<tr><th>Employee</th><th>Immediate Head</th><th class='text-right'>Locked DTR entries</th><th></th></tr>";// "<tr>";

                      if (members.length > 1)
                      {
                        var totalDTR = response.payrollPeriod.length;

                        for(var i = 0; i < members.length; i++)
                        {

                          var userid = members[i]['id'];
                          var count = rdata.filter((obj) => obj.id === userid).length;


                          if (count == totalDTR ){
                             htmltags += "<tr style='font-weight:bold; background: rgba(255, 255, 255, 0.5);' class='text-success'><td>"+(i+1)+". "+ members[i]['lastname']+", "+members[i]['firstname']+"<br/><small style='font-weight:normal' class='text-primary'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"+members[i]['jobTitle']+"</small></td>";
                             htmltags += "<td style='font-weight:bold'>"+ members[i]['leaderFname']+" "+ members[i]['leaderLname'] +"</td>";
                              htmltags += "<td class='text-right' style='font-weight:bold'>"+ count +" / "+ totalDTR +"</td>";
                              htmltags += "<td class='text-center' style='font-weight:bold'><a target='_blank' href='./user_dtr/"+members[i]['id']+"?from="+cutoffstart+"&to="+cutoffend+"'  class='btn btn-xs btn-default'><i class='fa fa-calendar-o'></i> View DTR </a></td></tr>";
                          }
                         

                          else{

                            if (count>0){
                              htmltags += "<tr><td style='font-weight:bold'>"+(i+1)+". "+ members[i]['lastname']+", "+members[i]['firstname']+"<br/><small style='font-weight:normal' class='text-primary'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"+members[i]['jobTitle']+"</small></td>";
                              htmltags += "<td style='font-weight:bold'>"+ members[i]['leaderFname']+" "+ members[i]['leaderLname'] +"</td>";
                              htmltags += "<td class='text-right' style='font-weight:bold'>"+ count +" / "+ totalDTR +"</td>";
                              htmltags += "<td class='text-center' style='font-weight:bold'><a target='_blank' href='./user_dtr/"+members[i]['id']+"?from="+cutoffstart+"&to="+cutoffend+"'  class='btn btn-xs btn-default'><i class='fa fa-calendar-o'></i> View DTR </a></td></tr>";

                            }else{
                              htmltags += "<tr><td>"+(i+1)+". "+ members[i]['lastname']+", "+members[i]['firstname']+"<br/><small style='font-weight:normal' class='text-primary'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"+members[i]['jobTitle']+"</small></td>";
                              htmltags += "<td>"+ members[i]['leaderFname']+" "+ members[i]['leaderLname'] +"</td>";
                              htmltags += "<td class='text-right'>"+ count +" / "+ totalDTR +"</td>";
                              htmltags += "<td class='text-center'><a target='_blank' href='./user_dtr/"+members[i]['id']+"?from="+cutoffstart+"&to="+cutoffend+"'  class='btn btn-xs btn-default'><i class='fa fa-calendar-o'></i> View DTR </a></td></tr>";

                            }

                            

                          }

                          
                        }
                        htmltags += "</table>";

                      }else{
                        htmltags += "<td>"+ members[i]['lastname']+", "+members[i]['firstname']+"</td></tr>";

                      }
                      console.log(members.length);

                      
                      

                      $('#team').html(htmltags)
                      
                    
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