@extends('layouts.main')

@section('metatags')
<title>Print | OAMPI Evaluation System</title>

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
        Personnel Change Notice
        <small>{{$personnel->firstname}} {{$personnel->lastname}} </small>
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


                 
                  <h2 class="text-center"> <img class="text-center" src="public/img/oam_favicon1-55027f4ev1_site_icon-256x256.png" width="90" /></h2>
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
                            <td>Program: <strong>{{$personnel->campaign->first()->name}} </strong><br/>
                              <br/><strong>Immediate Supervisor</strong>
                              <br/>{{ OAMPI_Eval\ImmediateHead::find($movementdetails->imHeadCampID_old)->firstname }} {{ OAMPI_Eval\ImmediateHead::find($movementdetails->imHeadCampID_old)->lastname }}

                            </td>
                            <td>

                                  <br/><strong>Immediate Supervisor</strong>
                                  <br/>{{$hisNewIDvalue->firstname}} {{$hisNewIDvalue->lastname}} </option>

                                   

                                  <br/><br/><strong>Floor: </strong>{{$hisFloor->name}}

                             

                              

                            </td>

                            @endif

                            @if ($movement->personnelChange_id == "2")
                            <td>Position: <strong>{{$personnel->position->name}} </strong></td>
                            <td>{{ $hisNew->name}}
                              
                              </td>

                            @endif



                            @if ($movement->personnelChange_id == "3")
                            <td>Status: <strong>{{$personnel->status->name}} </strong></td>
                            <td>{{ $hisNew->name }}
                              
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
                      <td class="text-center" ><strong>Requested by:</strong> <br /><br/> <br /><p>&nbsp;</p><p>&nbsp;</p>

                        <strong>{{$requestedBy->firstname}} {{$requestedBy->lastname}} </strong>
                        <br><em id="requestorPosition"><?php echo OAMPI_Eval\Position::find($requestedBy->userData->position_id)->name; ?> </em></td>
                      <td  class="text-center"><strong>Approved by: <br /><br/><br/> <p>&nbsp;</p><p>&nbsp;</p>
                       <br/>
                        Michael Chang</strong><br>
                        <em>Operations Manager</em></td>

                    </tr>

                    <tr>
                      <td class="text-center"style="padding-top:40px;"><br /><br/><br/><br/><br/>
                        <strong>{{$personnel->firstname}} {{$personnel->lastname}}</strong><br/>
                       Employee Signature / Date</td><p>&nbsp;</p><p>&nbsp;</p>
                      <td class="text-center"style="padding-top:40px;"><strong>Noted by:</strong> <br /><br/><p>&nbsp;</p><p>&nbsp;</p> 
                        <br/>
                        <strong>{{$hrPersonnel->firstname}} {{$hrPersonnel->lastname}} </strong><br>
                        <em id="personnelPosition"><?php echo OAMPI_Eval\Position::find($hrPersonnel->position_id)->name; ?></em></td>

                    </tr>



                  </table><p>&nbsp;</p>

                  @if (!empty($canAttachSignatures))
                   <p class="text-center"> <a href="#" class="btn btn-md btn-flat btn-success" id="save"><i class="fa fa-check"></i> Acknowledge and Attach Digital Signatures</a></p> 
                   @endif
                 


                  
                 

                  
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

  $('#save').on('click',function(e){

    e.preventDefault(); e.stopPropagation();

    $(this).fadeOut();

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