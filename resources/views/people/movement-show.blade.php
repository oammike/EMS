@extends('layouts.main')

@section('metatags')
<title>Movement | OAMPI Evaluation System</title>

<style type="text/css">
/* Sortable items */

.sortable-list {
  background: none; /* #fcedc6;*/
  list-style: none;
  margin: 0;
  min-height: 60px;
  padding: 10px;
}
.sortable-item {
  background-color: #fcedc6;
  
  cursor: move;
  
  font-weight: bold;
  margin: 2px;
  padding: 10px 0;
  text-align: center;
}

/* Containment area */

#containment {
  background-color: #FFA;
  height: 230px;
}


/* Item placeholder (visual helper) */

.placeholder {
  background-color: #ccc;
  border: 3px dashed #fcedc0;
  min-height: 150px;
  width: 180px;
  float: left;
  margin-bottom: 5px;
  padding: 45px;
}
</style>
@endsection

@section('content')


  <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Personnel Change Notice |
        <a href="{{action('UserController@show',$personnel->id)}} "><small>{{$personnel->firstname}} {{$personnel->lastname}} <i class="fa fa-file"></i></small>  </a>
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="{{action('MovementController@index')}}"> All Movements</a></li>
        <li class="active">{{$personnel->firstname}} {{$personnel->lastname}}</li>
      </ol>
    </section>

     <section class="content">
     

          <div class="row" style="margin-top:50px">
           
            

            <div class="col-lg-12"> <!-- ******************* LEFT COLUMN ***************** -->

              <div class="box box-default">

              <!-- TABLE: LEFT -->
                <div class="box-header">

                  <a target="_blank" href="{{action('MovementController@printPDF', $movement->id)}}" class="btn btn-md btn-primary pull-right"><i class="fa fa-print"></i> Print PDF</a>

                 

                 
                  <h2 class="text-center"> <img class="text-center" src="{{asset('public/img/logo-transparent.png')}}" width="90" /></h2>
                  <h3 class="text-center"> Personnel Change Notice <br/><br/><br/></h3>
                  

                </div>
                <div class="box-body">
                  <form name="changeNotice" id="changeNotice" method="POST"> 
                  <table class="table" style="width:85%; margin: 5px auto">
                    <tr>
                      <td>Employee Name: <h4>{{$personnel->firstname}} {{$personnel->lastname}}</h4> </td>
                      <td>Employee Number:<h4>{{$personnel->employeeNumber}}  </h4>  </td>

                    </tr>

                    <tr>
                      <td><h4>Date Requested: </h4> <input required type="text" class="form-control datepicker" style="width:50%" name="requestDate" id="requestDate" value="{{date('m/d/Y', strtotime($movement->dateRequested))}} " />
                       <div id="alert-requestDate" style="margin-top:10px"></div></td>
                      <td><h4>Effectivity Date: </h4> <input required type="text" class="form-control datepicker" style="width:50%" name="effectivityDate" id="effectivityDate" value="{{date('m/d/Y', strtotime($movement->effectivity))}}  " /> 
                      <div id="alert-effectivityDate" style="margin-top:10px"></div></td>

                    </tr>

                    <tr>
                      <td>
                        <h4><br/><br/>Reason for Change: <br/><br/></h4>
                        
                       
                      </td>

                      <td style="padding-top:50px">
                        <div id="alert-reason" style="margin-top:10px"></div>
                       
                        <label> <span class="text-success"> {{$movement->type->name}} </span></label><br/>
                        

                       
                        
                        
                         
                      </td>

                    </tr>

                     <tr>
                      <td>
                        <h4><br/><br/>Details: <br/><br/></h4>
                      </td>
                      <td>
                        <table class="table"> 
                          <tr>
                            <th class="text-center col-sm-6" >From</th>
                            <th class="text-center col-sm-6" width="350" >To</th>

                          </tr>
                          

                          <tr id="details">
                            @if ($movement->personnelChange_id == "1")
                            <td>Program: <strong>{{$oldCampaign->name}} </strong><br/>
                              <br/>Immediate Supervisor
                              <br/><strong>{{ OAMPI_Eval\ImmediateHead::find(OAMPI_Eval\ImmediateHead_Campaign::find($movementdetails->imHeadCampID_old)->immediateHead_id)->firstname }}
                              {{ OAMPI_Eval\ImmediateHead::find(OAMPI_Eval\ImmediateHead_Campaign::find($movementdetails->imHeadCampID_old)->immediateHead_id)->lastname }} </strong>

                            </td>
                            <td>
                                  Program: <strong>{{$newCampaign->name}} </strong><br/>
                                  <br/>Immediate Supervisor
                                  <br/><strong>{{$hisNewIDvalue->firstname}} {{$hisNewIDvalue->lastname}}</strong> </option>

                                   

                                  <br/><br/><strong>Floor: </strong>{{$hisFloor->name}}

                             

                              

                            </td>

                            @endif

                            @if ($movement->personnelChange_id == "2")
                            <td>Position: <strong>{{$hisOld->name}} </strong></td>
                            <td>Position: <strong>{{ $hisNew->name}}</strong></td>

                            @endif



                            @if ($movement->personnelChange_id == "3")
                            <td class="text-center">Status: <strong>{{$hisOld->name}} </strong></td>
                            <td class="text-center">Status: <strong>{{ $hisNew->name }}</strong>
                              
                              </td>

                            @endif
                            
                          </tr>

                        </table>
                        

                      </td>

                    </tr>

                    <tr>
                      <td colspan="2"><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p>
                        <p>Please indicate your conformity by signing in the space provided below. 
                          Your signature attests to the fact that you have read and understood the foregoing and that OAMPI Inc. is free from any liability, claim, or legal action for which you are involved or may involved.</p>
                          <p>&nbsp;</p><p>&nbsp;</p>

                      </td>
                    </tr>
                    <tr>
                      @if (($movement->personnelChange_id == "3") && ($hisNew->id == '7'))
                      <td class="text-center" ><strong>Immediate Supervisor:</strong> <br /><br/> <br /><p>&nbsp;</p>
                        @else
                        <td class="text-center" ><strong>Requested By:</strong> <br /><br/> <br /><p>&nbsp;</p>
                          @endif

                        
                       
                        
                         @if ( $isTheRequestor && ($signatureRequestedBy == null) )
                         <p class="text-center"> <a href="#" class="notedBtn btn btn-md btn-success" data-type="isNotedFrom"><i class="fa fa-check"></i> Acknowledge </a></p> 

                         @else
                         <!-- <img class="signature" src="$signatureRequestedBy" width="200" /> -->
                         <br/>


                         @endif
                       


                        <strong>{{$requestedBy->firstname}} {{$requestedBy->lastname}} </strong>
                        <br><em id="requestorPosition"><?php echo OAMPI_Eval\Position::find($requestedBy->userData->position_id)->name; ?> </em></td>
                      <td  class="text-center"><strong>Approved by: <br /><br/><br/> <p>&nbsp;</p>
                       
                       @if($signatureHR !== null)
                        <!-- <img class="signature" src="{{$signatureOpsMgr}}" width="200" /> -->
                        <br/>
                         @else
                        <p>&nbsp;</p><p>&nbsp;</p>
                        @endif

                        {{$theApprover->firstname}} {{$theApprover->lastname}}</strong><br>
                        <em>{{$theApproverTitle->name}} </em></td>

                    </tr>

                    <tr>
                      <td class="text-center"style="padding-top:40px;"><br /><br/><br/>

                        @if ( $noteTo && ($signatureRequestedTo == null) )
                         <p class="text-center"> <a href="#" class="notedBtn btn btn-md btn-success" data-type="isNotedTo"><i class="fa fa-check"></i> Acknowledge <!-- and Sign Digitally --></a></p> 

                         @else
                         <img class="signature" src="{{$signatureRequestedTo}}" width="200" /><br/>


                         @endif


                        <strong>{{$personnel->firstname}} {{$personnel->lastname}}</strong><br/>
                       Employee Signature / Date</td><p>&nbsp;</p><p>&nbsp;</p>
                      <td class="text-center"style="padding-top:40px;"><strong>Noted by:</strong> <br /><br/><p>&nbsp;</p>
                       
                        @if($signatureHR !== null)
                        <!-- <img class="signature" src="{{$signatureHR}}" width="200" /> --><br/>
                         @else
                        <p>&nbsp;</p><p>&nbsp;</p>
                        @endif

                        <strong>{{$hrPersonnel->firstname}} {{$hrPersonnel->lastname}} </strong><br>
                        <em id="personnelPosition"><?php echo OAMPI_Eval\Position::find($hrPersonnel->position_id)->name; ?></em></td>

                    </tr>



                  </table><p>&nbsp;</p>

                  @if ( $movement->isApproved==false && ($movement->personnelChange_id == 3 || $movement->personnelChange_id == 4) && (!empty($canAttachSignatures) && !$transferredToMe )  )
                  <p class="text-center"> <a href="#" class="btn btn-md btn-flat btn-success" id="approve" data-interCampaign="false"><i class="fa fa-check"></i> Approve Movement</a></p> 
                  @elseif ( ((!empty($canAttachSignatures) && !$transferredToMe ) && $movement->isApproved==false) && $interCampaign == true)
                   <p class="text-center"> <a href="#" class="notedBtn btn btn-md btn-flat btn-success" data-type="interCampaign"><i class="fa fa-check"></i> Acknowledge </a></p> 
                   @elseif ( ((!empty($canAttachSignatures) && !$transferredToMe ) && $movement->isApproved==false) && $interCampaign == false)
                   <p class="text-center"> <a href="#" class="btn btn-md btn-flat btn-success" id="approve" data-interCampaign="false"><i class="fa fa-check"></i> Approve Movement</a></p> 
                   @endif



                   <div id="alert-submit" style="margin-top:20px"></div>
                 


                  
                 

                  
                 </form>
                </div>
                <!-- /.box-body -->
                <div class="box-footer clearfix" style="background:none">
                 
                  
                </div>
                <!-- /.box-footer -->
              </div><!-- /.box -->
            </div><!--end left -->


           


            
           

          
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

   $('img.signature').bind('contextmenu', function(e) {
     alert("I know what you're thinking! Sorry, you're not allowed to do that...");
     return false;
    }); 

  $('.notedBtn').on('click',function(e){

    e.preventDefault(); e.stopPropagation();
    var _token = "{{ csrf_token() }}";
    var type = $(this).attr('data-type');
    console.log(type);

    $.ajax({
            url:"{{action('MovementController@noted', $movement->id)}}",
            type:'POST',
            data:{

              'id' : "{{$movement->id}}",
              'type': type,
              '_token':_token
            },

            error: function(response)
            {
              console.log(response);
               $('#save').fadeOut();
                      var htmcode = "<span class=\"success\"> <i class=\"fa fa-save\"></i>"+response.message+" <br /><br/>";
                      
                      $('#alert-submit').addClass('alert alert-success').fadeIn().html(htmcode); 

            },

           
            success: function(response2)
            {
              console.log(response2);
               $('.notedBtn').fadeOut();
                      var htmcode = "<span class=\"success\"> <i class=\"fa fa-save\"></i> Employee movement updated. <br /><br/>";
                      
                      $('#alert-submit').addClass('alert alert-success').fadeIn().html(htmcode); 
                       $('#alert-submit').delay(1000).fadeOut(function() {
                  window.location = "{{action('MovementController@show', $movement->id)}}";
                });

            }
         });
    return false;


  }); 






  $('#approve').on('click',function(e){

    e.preventDefault(); e.stopPropagation();
    var _token = "{{ csrf_token() }}";
    var interCampaign = $(this).attr('data-interCampaign');
    $.ajax({
            url:"{{action('MovementController@approve', $movement->id)}}",
            type:'POST',
            data:{

              'id' : "{{$movement->id}}",
              'interCampaign': interCampaign,
              '_token':_token
            },

            error: function(response)
            {
              console.log(response);
               //$('#approve').fadeOut();
                      var htmcode = "<span class=\"success\"> <i class=\"fa fa-save\"></i>"+response.message+" <br /><br/>";
                      
                      $('#alert-submit').addClass('alert alert-success').fadeIn().html(htmcode); 

            },

           
            success: function(response2)
            {
              console.log(response2);
               $('#approve').fadeOut();
                      var htmcode = "<span class=\"success\"> <i class=\"fa fa-save\"></i> Employee movement updated. <br /><br/>";
                      
                      $('#alert-submit').addClass('alert alert-success').fadeIn().html(htmcode); 
                       $('#alert-submit').delay(1000).fadeOut(function() {
                  window.location = "{{action('MovementController@show', $movement->id)}}";
                });

            }
          });
    return false;


  });

   $( ".datepicker" ).datepicker();



   

    

   });




function validateRequired(param, availability, defaultval) {

        
        if (param == null){

          availability.addClass('alert alert-danger').fadeIn();
            availability.html('<span class="success"> <i class="fa fa-warning"></i> This field is required. </span>');  
             return false;
        }

        else if(param.length <= 0 || param === defaultval) { 
            
            availability.addClass('alert alert-danger').fadeIn();
            availability.html('<span class="success"> <i class="fa fa-warning"></i> This field is required. </span>');   
             return false;         
            

        } else{
            availability.removeClass();
            availability.html('');
            return true;
                      
        }
       

}

   


   

 
</script>
<!-- end Page script -->

<!-- <script type="text/javascript" src="{{asset('public/js/jquery.validate.js')}}"></script> -->

@stop