@extends('layouts.main')

@section('metatags')
<title>Finance JPS Downloads | Open Access EMS</title>
<link rel="stylesheet" type="text/css" href="{{URL::asset('public/js/formBuilder/demo.css')}}">
@endsection

@section('content')


  <!-- Content Header (Page header) -->
    <section class="content-header">
      <h4><i class="fa fa-calendar"></i> Download JPS Template  </h4>
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
              <h3 class="text-primary">2. <span style="font-size: smaller;"> Select JPS Template :</span></h3>
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
                <input type="hidden" name="reportType" id="reportType" value="dailyLogs" />
                
                <!-- <input type="hidden" name="dtr" /> -->

                <div id="loader" class="text-center">
                  <span id="wait" style="font-size: small;" class="text-primary">Fetching EMS data. Please wait...<br/><br/><img src="storage/uploads/loading.gif" width="30" />  </span><br/><br/><br/>
                </div>

              <div class="notes">
                <h4>There are <strong><span class="text-danger" id="submitted"></span> <span class="text-danger" id="programName"></span>  </strong> submissions for this cutoff.<br/> 

               <!--  </h4>

                  <p>Please remind the following employees to have their DTR sheets locked and verified for this cutoff period:</p> -->

                <span style="font-size: smaller;"> Proceed with the template download?</span></h4>

                <table class="table table-striped" id="team">
                  
                  
                </table>

               
                  
                

                <p class="text-center"> 
                  <button type="submit" class="btn btn-md btn-success" id="dl"><i class="fa fa-save"></i> Download Spreadsheet</button> 
                  <input type="hidden" name="template" id="dltype" />
                  

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
                  

                  /*$('#dl').on('click',function(){
                      $('input[name="dltype"]').val(1);

                      $.notify("Processing spreadsheet for download.\nPlease wait...",{className:"success", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );
                  
                      console.log(jpsData);
                      $.ajax({
                              url: "{{action('DTRController@finance_dlJPS')}}",
                              type:'POST',
                              data:{ 
                                'cutoffstart': cutoffstart,
                                'cutoffend': cutoffend,
                                'jpsData': jpsData,
                                'template': selval,
                                '_token':_token
                              },
                              success: function(res){ 
                                console.log('result');console.log(res);
                              }
                            });

                  });*/

                  /*var rdata = [];

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

                      $('input[name="cutoffstart"]').val(cutoffstart);
                      $('input[name="cutoffend"]').val(cutoffend);

                      $('#team').html('');

                      var htmltags="<tr><th>Employee</th><th>Immediate Head</th><th class='text-right'>Locked DTR entries</th>th class='text-center'>Time IN</th>th class='text-center'>Time OUT</th><th></th></tr>";// "<tr>";

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
                      console.log(members.rdata);

                      
                      

                      $('#team').html(htmltags)
                      
                    */

                  
                }
              });



     

    }

  }); 


      
    


});

   

  
</script>
<!-- end Page script -->


@stop