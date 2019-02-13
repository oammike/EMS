@extends('layouts.main')

@section('metatags')
<title>Personnel Change Notice | OAMPI Evaluation System</title>

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
    <section class="content-header" style="margin-bottom:50px">
      <h1>
        Personnel Change Notice
        <small><a href="{{action('UserController@show', $personnel->id)}} ">{{$personnel->firstname}} {{$personnel->lastname}} </a></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="{{action('ImmediateHeadController@index')}}"> All Team Leaders</a></li>
        <li class="active">Team Movement</li>
      </ol>
    </section>

     <section class="content">
      <!-- @if (Auth::user()->userType_id == 1 || Auth::user()->userType_id == 2 ) 
      <a href="{{action('ImmediateHeadController@create')}} " class="btn btn-sm btn-default btn-flat pull-right"><i class="fa fa-users"></i> Add New Leader</a>
     
      @endif -->

          <div class="row" style="margin-top:50px">
           
            

            <div class="col-lg-12"> <!-- ******************* LEFT COLUMN ***************** -->

              <div class="box box-default">

              <!-- TABLE: LEFT -->
                <div class="box-header">

                 
                  <h2 class="text-center"> <img class="text-center" src="{{asset('public/img/oam_favicon1-55027f4ev1_site_icon-256x256.png')}}" width="90" /></h2>
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
                      <td><h4>Date Requested: </h4> <input required type="text" class="form-control datepicker" style="width:50%" name="requestDate" id="requestDate" />
                       <div id="alert-requestDate" style="margin-top:10px"></div></td>
                      <td><h4>Effectivity Date: </h4> <input required type="text" class="form-control datepicker" style="width:50%" name="effectivityDate" id="effectivityDate" /> 
                      <div id="alert-effectivityDate" style="margin-top:10px"></div></td>

                    </tr>

                    <tr>
                      <td>
                        <h4><br/><br/>Reason for Change: <br/><br/></h4>
                        
                       
                      </td>

                      <td style="padding-top:50px">
                        <div id="alert-reason" style="margin-top:10px"></div>
                        @foreach ($changes as $change)

                        <label> <input type="radio" name="reason" required value="{{$change->id}}" /> {{$change->name}} </label><br/>

                        @endforeach
                        
                      

                         
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
                      <td class="text-center" ><strong id="requestedLabel">Requested by:</strong> <br /><br/> <br /><p>&nbsp;</p><p>&nbsp;</p>

                        <div id="alert-requestedBy" style="margin-top:10px"></div>
                        @if (!empty($requestor))
                        <!-- <img class="signature" src="{{$signatureRequestedBy}}" width="200" /> <br/>-->@endif
                        <select name="requestedBy" id="requestedBy" class="required form-control text-left"style="width:70%; margin:0 auto; text-transform:uppercase">
                          @if (empty($requestor))
                              <option value="0" class="text-center"> -- Select a leader --</option>
                              @foreach ($leaders as $leader)
                                <option class="text-left" style="text-transform:uppercase" value="{{$leader->id}}" data-position="{{$leader->position}}" data-campaign="{{$leader->campaign}}">{{$leader->lastname}}, {{$leader->firstname}} -- {{$leader->campaign}}</option>
                              @endforeach
                              </select>

                          @else

                          <option class="text-left" style="text-transform:uppercase"  value="{{$requestor->id}}" data-position="{{$requestorPosition}}" data-campaign="{{$requestorCampaign->name}} "><?php  echo OAMPI_Eval\ImmediateHead::find($requestor->immediateHead_id)->lastname;?>, <?php  echo OAMPI_Eval\ImmediateHead::find($requestor->immediateHead_id)->firstname;?> </option>

                        </select>
                        <br/>{{$requestorPosition}}

                          @endif
                        
                        <br>
                        <em id="requestorPosition"></em></td>
                      <td  class="text-center"><strong>Approved by: <br /><br/><br/> <p>&nbsp;</p><p>&nbsp;</p>
                        {{$theApprover->firstname}} {{$theApprover->lastname}} </strong><br>
                        <em>{{$theApproverTitle->name}} </em></td>

                    </tr>

                    <tr>
                      <td class="text-center"><br /><br/><br/><br/><br/><br/>
                        <strong>{{$personnel->firstname}} {{$personnel->lastname}}</strong><br/>
                       Employee Signature / Date</td><p>&nbsp;</p><p>&nbsp;</p>
                      <td class="text-center"><strong>Noted by:</strong> <br /><br/><br/><p>&nbsp;</p><p>&nbsp;</p> 
                        <div id="alert-hrPersonnel" style="margin-top:10px"></div>
                        <select name="hrPersonnel" id="hrPersonnel" class="form-control text-left" style="text-transform:uppercase; width:45%; margin:0 auto" required>
                          <option value="0" class="text-left"> -- Select HR personnel --</option>
                          
                          @foreach ($hrPersonnels as $leader)
                            
                            <option class="text-left" value="{{$leader->id}}" data-position="{{$leader->position}}" data-campaign="{{$leader->campaign}}">{{$leader->lastname}}, {{$leader->firstname}} </option>
                          @endforeach
                        </select><br>
                        <em id="personnelPosition"></em></td>

                    </tr>



                  </table><p>&nbsp;</p>

                 <!--  <p class="text-center"> <a href="#" class="btn btn-md btn-flat btn-primary" id="save"><i class="fa fa-save"></i> Save</a></p> -->
                  <p class="text-center"> 
                    <input type="submit" class="btn btn-lg btn-success btn-flat" name='submit' value="Save" />
                    <div id="alert-submit" style="margin-top:20px"></div>
                  </p>


                  
                 

                  
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

   //$('#changeNotice').validate();

   $('#changeNotice').on('submit', function(e) {

      var v1 = $('#requestDate').val();
      var v2 = $('#effectivityDate').val();
      var v3 = $('#requestedBy').val();
      var v4 = $('#hrPersonnel').val();


      var h1 = $('#alert-requestDate');
      var h2 = $('#alert-effectivityDate');
      var h3 = $('#alert-requestedBy');
      var h4 = $('#alert-hrPersonnel');

      var _token = "{{ csrf_token() }}";

      //check sub selects

        var reason = $('input[name="reason" ]:checked').val();

        switch(reason){
          case "1": { 
                      var program = $('select[name="program"]').find(':selected').val();
                      var floor = $('select[name="newFloor"]').find(':selected').val();
                      var head = $('select[name="newHead"]').find(':selected').val();
                      var hp = $('#alert-program');
                      var hh = $('#alert-newHead');
                      var hf = $('#alert-newFloor');
                      if (!validateRequired(program,hp,"0")) { 
                        console.log('not valid Program '+program); e.preventDefault(); e.stopPropagation();
                        return false;
                        } else {
                          if (!validateRequired(head,hh,"0")) { 
                            console.log('not valid Head'); e.preventDefault(); e.stopPropagation(); return false; 
                          } else 
                          if (!validateRequired(floor,hf,"0")) { 
                            console.log('indicate seat floor'); e.preventDefault(); e.stopPropagation(); return false; 
                          } 
                           else { 
                            var newHead_id = head; var withinProgram = false;

                             if (!validateRequired(v3,h3,"0") || !validateRequired(v4,h4,"0") ) { 
                              console.log('not valid, preventDefault'); e.preventDefault(); e.stopPropagation(); 
                              } else {

                                        // get first if there were previous movements
                                      
                                      $.ajax({
                                                              url:"{{action('MovementController@findInstances')}} ",
                                                              type:'POST',
                                                              data:{
                                                                'user_id': '{{$personnel->id}}',
                                                                'movementType': reason,
                                                                'old_id': '{{$immediateHead->id}}' ,
                                                                'new_id': head,
                                                                'newFloor': floor,
                                                                'newCampaign': program,
                                                                _token:_token},

                                                              error: function(response)
                                                              { console.log(response); return false;
                                                              },
                                                              success: function(response)
                                                              {
                                                                console.log("fromPeriod: "+ response[0].fromPeriod);

                                                                var withinProgram = false;

                                                                if ({{$personnel->campaign->first()->id}} == program) withinProgram=true; 
                                                               
                                                                
                                                                saveProgramMovement("{{$personnel->id}}","{{$immediateHead->id}}", head, floor, "{{$personnel->floor[0]->id}}", program, "{{$personnel->team->id}}" , withinProgram, response[0].fromPeriod,v2, false, v3,v1,v4,reason,_token );
                                                               
                                                              }
                                              });

                                      
                                        } //end else level2
                        } //end else level1

                      } //end else main
                      break; 
                    };
          case "2": { 
                      var withinProgram = true; 

                      var program = $('select[name="position"]').find(':selected').val();
                      
                      var hp = $('#alert-position');
                      
                      if (!validateRequired(program,hp,"0")) { 
                        console.log('not valid Position '+program); e.preventDefault(); e.stopPropagation();
                        return false;
                      } else {
                          if (program == "-1") 
                          { 
                            var newPos = $('input[name="newPosition"]').val();
                           
                            if (!validateRequired(newPos,hp,"Enter new job position")){
                              console.log('not valid PosValue: '+newPos); e.preventDefault(); e.stopPropagation(); return false; 
                            } else { 

                                      //check first the rest of the form
                                      if (!validateRequired(v3,h3,"0") || !validateRequired(v4,h4,"0") ) { 
                                        console.log('not valid, preventDefault'); e.preventDefault(); e.stopPropagation();
                                      } else {

                                                var withinProgram = false; 

                                              //save first the new position
                                              $.ajax({
                                                                url:"{{action('PositionController@store')}} ",
                                                                type:'POST',
                                                                data:{
                                                                  'name': newPos,
                                                                  _token:_token},

                                                                error: function(response)
                                                                { console.log("Error saving position: "); return false;
                                                                },
                                                                success: function(response)
                                                                {
                                                                  var posID = response.id;

                                                                  // get first if there were previous movements
                                                                  // if there were no movements AND status is CONTRACTUAL -> from dateHired
                                                                  // if STATUS is Regular, ->from dateRegularized

                                                                  $.ajax({
                                                                                          url:"{{action('MovementController@findInstances')}} ",
                                                                                          type:'POST',
                                                                                          data:{
                                                                                            'user_id': '{{$personnel->id}}',
                                                                                            'movementType': reason,
                                                                                            'old_id': '{{$personnel->status_id}}' ,
                                                                                            'new_id': posID,
                                                                                            _token:_token},

                                                                                          error: function(response)
                                                                                          { console.log(response); return false;
                                                                                          },
                                                                                          success: function(response)
                                                                                          {
                                                                                            console.log(response[0].fromPeriod);
                                                                                            saveMovement("{{$personnel->id}}","{{$personnel->position_id}}", posID, withinProgram, response[0].fromPeriod,v2, false, v3,v1,v4,reason,_token );


                                                                                          }//end success
                                                                    });


                                                                  
                                                                 


                                                                }//end success

                                                    }); //end ajax


                                      } // else all cleared

                                      // --- end check first rest of the form


                                      


                                      
                                    }//end !validate else
                            
                          } else { 
                              
                              var withinProgram = true; 

                              
                              // get first if there were previous movements
                              // if there were no movements AND status is CONTRACTUAL -> from dateHired
                              // if STATUS is Regular, ->from dateRegularized

                              $.ajax({
                                                      url:"{{action('MovementController@findInstances')}} ",
                                                      type:'POST',
                                                      data:{
                                                        'user_id': '{{$personnel->id}}',
                                                        'movementType': reason,
                                                        'old_id': '{{$personnel->status_id}}' ,
                                                        'new_id': program,
                                                        _token:_token},

                                                      error: function(response)
                                                      { console.log(response.effectivity); return false;
                                                      },
                                                      success: function(response)
                                                      {
                                                        console.log(response[0].fromPeriod);
                                                        saveMovement("{{$personnel->id}}","{{$personnel->position_id}}", program, withinProgram, response[0].fromPeriod,v2, false, v3,v1,v4,reason,_token );



                                                      }//end success
                                });



                            } //end else if -1
                        }//end main else

                      break;};
          case "3": { 
                      var program = $('select[name="status"]').find(':selected').val();


                      
                      var hp = $('#alert-status');
                     
                      if (!validateRequired(program,hp,"0")) { 
                        console.log('not valid Status '+program); e.preventDefault(); e.stopPropagation();
                        return false;
                        } else {
                          

                           var withinProgram = false;

                             if (!validateRequired(v3,h3,"0") || !validateRequired(v4,h4,"0") ) { 
                              console.log('not valid status'); e.preventDefault(); e.stopPropagation(); 
                              } else {

                                // get first if there were previous movements
                                // if there were no movements AND status is CONTRACTUAL -> from dateHired
                                // if STATUS is Regular, ->from dateRegularized

                                $.ajax({
                                                        url:"{{action('MovementController@findInstances')}} ",
                                                        type:'POST',
                                                        data:{
                                                          'user_id': '{{$personnel->id}}',
                                                          'movementType': reason,
                                                          'old_id': '{{$personnel->status_id}}' ,
                                                          'new_id': program,
                                                          _token:_token},

                                                        error: function(response)
                                                        { console.log(response); return false;
                                                        },
                                                        success: function(response)
                                                        {
                                                          console.log(response);
                                                          saveMovement("{{$personnel->id}}","{{$personnel->status_id}}", program, withinProgram, response[0].fromPeriod,v2, false, v3,v1,v4,reason,_token );
                                                        }//end success

                                            }); //end ajax





                                      
                                        } //end else level2
                        

                      } //end else main
                      break;
                    };
        }; //end switch


    

      
      e.preventDefault(); e.stopPropagation();
      return false;

   });

   $( ".datepicker" ).datepicker({dateFormat:"yy-mm-dd"});

   
   $(document).on('change', 'select[name="status"]',function(){
      var stat =  $(this).find(':selected').val();

      if (stat == 7 || stat == 8 || stat == 9 )
      {
        $('#requestedLabel').html("");
        $('#requestedLabel').html("Immediate Supervisor:");
        console.log(stat);

      } else {
        $('#requestedLabel').html("");
        $('#requestedLabel').html("Requested By:");
        console.log(stat);
      }

    

   });

   

   $("select[name='requestedBy']").on('change', function(){
     var pos =  $(this).find(':selected').attr('data-position');
     var camp =  $(this).find(':selected').attr('data-campaign');
     $('#requestorPosition').html('');
     $('#requestorPosition').html(pos +', <strong>'+camp+'</strong>');

   });

   $("select[name='hrPersonnel']").on('change', function(){
     var pos =  $(this).find(':selected').attr('data-position');
     var camp =  $(this).find(':selected').attr('data-campaign');
     $('#personnelPosition').html('');
     $('#personnelPosition').html('<strong>'+pos+'</strong>');

   });

   $("input[name='reason']").on('click', function(){

    var reason = $(this).val();
    var holder = $('#details');
   holder.html("");

    switch(reason){
      case "1": {
                
                
                

                var htmlcode = "<td>Program: <br/><strong>{{$personnel->campaign[0]->name}}</strong> - <em>{{OAMPI_Eval\ImmediateHead::find($immediateHead->immediateHead_id)->firstname}} {{OAMPI_Eval\ImmediateHead::find($immediateHead->immediateHead_id)->lastname}}  </em> </td>";
                htmlcode +="<td><select name=\"program\" id=\"program\" class=\"form-control\">";
                htmlcode +="<option value=\"0\">Select New Program / Department</option>";
                htmlcode +="@foreach ($campaigns as $c)<option value=\"{{$c->id}}\">{{$c->name}} </option> @endforeach</select>";
                htmlcode += "<br/><div id='alert-program'></div><div id='newTeam'></div></td>";
                holder.html('');
                holder.html(htmlcode);

                $("select[name='program']").on('change', function(){
                  var camp = $(this).find(':selected').val();
                  console.log("selected camp: "+camp);

                  if (camp !== 0){
                    var _token = "{{ csrf_token() }}";

                    if(camp !== "{{$personnel->campaign[0]->id}}" ){
                      // ******* this means it is not an inter-campaign movement, needs HR intervention

                      $("input[name='submit']").val("Submit to HR");
                    } else  $("input[name='submit']").val("Save Movement");

                     $.ajax({
                      url:"{{url('/')}}/campaign/"+camp+"/leaders",
                      type:'GET',
                      data:{id:camp, _token:_token},
                      error: function(response)
                      {
                         
                        
                        console.log("Error leader: "+response.id);

                          
                          return false;
                      },
                      success: function(response)
                      {
                        var htmlcode2 = "<br/><strong>Immediate Supervisor</strong><select name='newHead' id='newhead' class='form-control'  style=\"text-transform:uppercase\" >";
                        htmlcode2 += "<option value='0'> -- Select Leader -- </option>";
                        console.log(response);
                        $.each(response, function(index) {

                          if (response[index].id !== {{$immediateHead->id}}){
                            htmlcode2 +="<option value='"+response[index].id+"'>"+response[index].lastname+", "+response[index].firstname+"</option>";
                          }
                          
                          console.log(response[index].id);


                         

                        
                        }); //end each

                        htmlcode2 +="</select><br/><div id='alert-newHead'></div>";


                        htmlcode2 +="<br/><br/><strong>Floor</strong><select name='newFloor' id='newFloor' class='form-control'>";
                        htmlcode2 += "<option value='0'> -- Select Floor -- </option>";

                        @foreach ($floors as $floor)

                        htmlcode2 +="<option value='{{$floor->id}}'>{{$floor->name}}</option>";

                        @endforeach
                       

                        htmlcode2 +="</select><br/><div id='alert-newFloor'></div>";

                        $('#newTeam').html(htmlcode2);



                      }//end success

                      }); //end ajax
                    }//end if

                  }); //end select on change

                break;



      };
      case "2": {
                var htmlcode = "<td>Position: <strong>{{$personnel->position->name}} </strong></td>";
                htmlcode +="<td><select name=\"position\" id=\"position\" class=\"form-control\">";
                htmlcode +="<option value=\"0\">Select New Job Position</option>";
                htmlcode +="@foreach ($positions as $c)<option value=\"{{$c->id}}\">{{$c->name}} </option> @endforeach";
                htmlcode +="<option value=\"-1\">** <em>add new position</em> ** </option></select>";
                htmlcode += "<br/><div id='alert-position'></div><div id='newpos'></div></td> ";
                
                holder.html('');
                holder.html(htmlcode);

                $("select[name='position']").on('change', function(){
                 var pos =  $(this).find(':selected').val();

                 if (pos == "-1"){ //add new position
                  var htmcode = "<input required type='text' class='form-control' name='newPosition' id='newPosition' placeholder='Enter new job position' value='' />";

                    $('#newpos').html(htmcode);
                    
                    console.log(htmcode);

                 }
                 
               });$("input[name='submit']").val("Submit to HR");


                break;
      };
      
      case "3": {
                
               
                var htmlcode = "<td>Status: <strong>{{$personnel->status->name}} </strong></td>";
                htmlcode +="<td> <select name=\"status\" id=\"status\" class=\"form-control\">";
                htmlcode +="<option value=\"0\">Select New Status</option>";
                htmlcode +="@foreach ($statuses as $c)<option value=\"{{$c->id}}\">{{$c->name}} </option> @endforeach";
                htmlcode += "</select><br/><div id='alert-status'></div></td>";
                holder.html('');
                $("input[name='submit']").val("Submit to HR");
                holder.html(htmlcode);break;

      };
    }

   });
   

    

   });



function saveProgramMovement(user_id, old_id, new_id, new_floor, old_floor, campaign_id, oldTeam_id, withinProgram, fromPeriod, effectivity, isApproved, requestedBy, dateRequested, notedBy, personnelChange_id,_token ){

   //save movement
      $.ajax({
                    url:"{{action('MovementController@store')}} ",
                    type:'POST',
                    data:{
                      'user_id': user_id ,
                      'old_id': old_id ,
                      'new_id': new_id,
                      'withinProgram': withinProgram,
                      'fromPeriod': fromPeriod,
                      'effectivity': effectivity,
                      'isApproved': isApproved,
                      'requestedBy': requestedBy,
                      'dateRequested': dateRequested,
                      'notedBy': notedBy,
                      'personnelChange_id': personnelChange_id,
                      'newFloor': new_floor,
                      'oldFloor': old_floor,
                      'oldTeam_id' : oldTeam_id,
                      'campaign_id': campaign_id,
                      _token:_token},

                    error: function(response2)
                    { console.log("Error saving movement: "); 
                      console.log(response2); return false;
                    },
                    success: function(response2)
                    {
                      console.log('oldid: '+old_id+'; newid: '+new_id);
                      $('input[name="submit"').fadeOut();
                      var htmcode = "<span class=\"success\"> <i class=\"fa fa-save\"></i> <strong>Employee movement data saved.</strong> <br/><br />";

                      if (response2.withinProgram=="true")
                      {
                        htmcode += "Employee: "+response2.info1;
                       // htmcode += "<br/> withinProgram: "+response2.withinProgram;
                        htmcode += "<a href=\"{{action('MovementController@changePersonnel',$personnel->id)}}\" class='btn btn-sm btn-default text-black pull-right'><i class='fa fa-reply'></i> Back to Employee Movement</a> <br/><br/>";
                        
                        $('#alert-submit').addClass('alert alert-success').fadeIn().html(htmcode); 
                         
                         console.log("withinprog: " + response2.withinProgram);

                      } else {

                        {
                        htmcode += "Employee: "+response2.info1;
                       // htmcode += "<br/> Not withinProgram: "+response2.withinProgram;
                        htmcode += "<a href=\"{{action('MovementController@changePersonnel',$personnel->id)}}\" class='btn btn-sm btn-default text-black pull-right'><i class='fa fa-reply'></i> Back to Employee Movement</a> <br/><br/>";
                        
                        $('#alert-submit').addClass('alert alert-success').fadeIn().html(htmcode); 
                         console.log("not withinprog: " + response2.withinProgram);

                      }
                      }
                      


                    }//end success

        }); //end ajax

}


function saveMovement(user_id, old_id, new_id, withinProgram, fromPeriod, effectivity, isApproved, requestedBy, dateRequested, notedBy, personnelChange_id,_token ){

   //save movement
      $.ajax({
                    url:"{{action('MovementController@store')}} ",
                    type:'POST',
                    data:{
                      'user_id': user_id ,
                      'old_id': old_id ,
                      'new_id': new_id,
                      'withinProgram': withinProgram,
                      'fromPeriod': fromPeriod,
                      'effectivity': effectivity,
                      'isApproved': isApproved,
                      'requestedBy': requestedBy,
                      'dateRequested': dateRequested,
                      'notedBy': notedBy,
                      'personnelChange_id': personnelChange_id,
                      _token:_token},

                    error: function(response2)
                    { console.log("Error saving movement: "); 
                      console.log(response2); return false;
                    },
                    success: function(response2)
                    {
                      $('input[name="submit"').fadeOut();
                      var htmcode = "<span class=\"success\"> <i class=\"fa fa-save\"></i> Employee movement data saved. <br />";
                      
                      htmcode += "<a href=\"{{action('MovementController@changePersonnel',$personnel->id)}}\" class='btn btn-sm btn-default text-black pull-right'><i class='fa fa-reply'></i> Back to Employee Movement</a> <br/><br/>";
                      
                      $('#alert-submit').addClass('alert alert-success').fadeIn().html(htmcode); 


                    }//end success

        }); //end ajax

}

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

@stop