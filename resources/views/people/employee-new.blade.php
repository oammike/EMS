@extends('layouts.main')

@section('metatags')
<title>New Employee | OAMPI Evaluation System</title>

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
        <i class="fa fa-user-plus"></i> Add New Employee
       
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="{{action('UserController@index')}}"> All Employees</a></li>
        <li class="active">Add New Employee</li>
      </ol>
    </section>

     <section class="content">
      

          <div class="row" style="margin-top:50px">
           
            

            <div class="col-lg-12"> <!-- ******************* LEFT COLUMN ***************** -->

              <div class="box box-default" style="background: rgba(256, 256, 256, 0.3)">

              <!-- TABLE: LEFT -->
                <div class="box-header">

                 
                  <h2 class="text-center"> <img class="text-center" src="{{asset('public/img/oam_favicon1-55027f4ev1_site_icon-256x256.png')}}" width="90" /></h2>
                  <!-- <h3 class="text-center"> New OAMPI Employee <br/></h3> -->
                  

                </div>
                <div class="box-body">
                  
                  {{ Form::open(['route' => 'user.store', 'class'=>'col-lg-12', 'id'=> 'addEmployee','name'=>'addEmployee' ]) }}
                  <table class="table" style="width:85%; margin: 5px auto">
                    <tr>
                      <td>
                        <label>Employee Name: </label>
                        <input tabindex="1" type="text" name="firstname" id="firstname" required placeholder="FIRST NAME" class="form-control required" />
                        <div id="alert-firstname" style="margin-top:10px"></div>

                         <input tabindex="3" type="text" name="middlename" id="middlename" placeholder="MIDDLE NAME" class="form-control required" />
                         <div id="alert-middlename" style="margin-top:10px"></div>

                        <input tabindex="5" type="text" name="lastname" id="lastname" placeholder="LAST NAME" required class="form-control required" />
                        <div id="alert-lastname" style="margin-top:10px"></div>


                        <input tabindex="6" type="text" name="nickname" id="nickname" placeholder="NICKNAME / ALIAS" required class="form-control required" />
                        <div id="alert-nickname" style="margin-top:10px"></div>

                        <div class="clearfix" style="margin-top:20px"></div>
                        <label> <input tabindex="8" type="radio" name="gender" required value="M" checked="checked" /> Male </label>&nbsp;&nbsp;&nbsp;&nbsp;
                        <label> <input tabindex="9" type="radio" name="gender" required value="F" /> Female </label>

                        

                          <br/><br/><br/>
                          <label>Position: </label>
                          <select tabindex="10" class="form-control" name="position_id" id="position_id">
                            <option value="0">- Select job title - </option>

                            @foreach ($positions as $pos)
                            <option value="{{$pos->id}}">{{$pos->name}} </option>

                            @endforeach
                            <option value="-1">** <em>add new position</em> ** </option>
                          </select>

                          <div id="newpos"></div>
                          <div id="alert-position" style="margin-top:10px"></div>  

                          <br/><br/>
                          

                          <label>Department / Program: </label>
                          <select tabindex="12" class="form-control" name="campaign_id" id="campaign_id">
                            <option value="0">- Select one - </option>

                            @foreach ($campaigns as $campaign)
                            <option value="{{$campaign->id}}">{{$campaign->name}} </option>

                            @endforeach

                          </select><div id="alert-campaign" style="margin-top:10px"></div>  

                          <div id="newTeam"></div>             
                      </td>
                      <td>
                        <label>Employee Number: </label> <input tabindex="2" type="text" class="form-control required" name="employeeNumber" required id="employeeNumber" placeholder="xxxx-xxxx" /> 
                         <div id="alert-employeeNumber" style="margin-top:10px"></div>

                         <label>Biometrics Access Code: </label> <input tabindex="4" type="text" class="form-control required" name="accesscode" required id="accesscode" placeholder="2XXXXXXX" /> 
                         <div id="alert-accesscode" style="margin-top:10px"></div>

                         <label class="pull-left">OAMPI E-mail handle: &nbsp;&nbsp; </label> <input tabindex="7" type="text" style="width:200px;" class="form-control required pull-left" name="oampi" required id="oampi" placeholder="johnDoe" /> <span class="pull-left" style="padding-top:6px">@openaccessbpo.net</span>
                         <div id="alert-email" style="margin-top:10px"></div>

                         <div class="clearfix" style="margin-top: 65px">&nbsp;</div>


                         <label class="pull-left">Date of Birth:  </label> <input tabindex="14" type="text" class="form-control datepicker pull-left" style="margin-left: 15px; width:50%" name="birthday" id="birthday" placeholder="MM/DD/YYYY" />
                       <div id="alert-birthday" style="margin-top:10px"></div>


                      



                         <div class="clearfix" style="margin-top:80px"></div>

                                      @if( empty($personnel->leadOverride) )
                                     <label for="leadOverride"><input tabindex="11" type="checkbox" name="leadOverride" id="leadOverride" value="1"></input> Override Leader settings</label><br/><em><small>This will enable a leader to be evaluated using agent-level competencies</small></em>

                                     @else
                                     <label for="leadOverride"><input tabindex="11" type="checkbox" name="leadOverride" id="leadOverride" value="1" checked="checked"></input> Override Leader settings</label><br/><em><small>This will enable a leader to be evaluated using agent-level competencies</small></em>
                                     @endif


                         <div class="clearfix" style="margin-top:45px"></div>
                         <label>Floor location: </label>
                          <select tabindex="13" class="form-control" name="floor_id" id="floor_id" style="width:50%" required>
                            <option value="0">- Select one - </option>

                            @foreach ($floors as $floor)
                              @if ($floor->id !== 6)<option value="{{$floor->id}}">{{$floor->name}} </option>@endif

                            @endforeach

                          </select><div id="alert-floor" style="margin-top:10px"></div>  



                       </td>

                    </tr>

                    <tr>
                      <td>

                       <label>Training Start Date: </label> <input tabindex="14" type="text" class="form-control datepicker" style="width:50%" name="startTraining" id="startTraining" placeholder="MM/DD/YYYY" />

                       <label>Training End Date: </label> <input tabindex="15" type="text" class="form-control datepicker" style="width:50%" name="endTraining" id="endTraining" placeholder="MM/DD/YYYY" />
                       

                     </td>
                      <td>

                       

                      <label>Date Hired: </label> <input tabindex="16" required type="text" class="form-control datepicker" style="width:50%" name="dateHired" id="dateHired" placeholder="MM/DD/YYYY" />
                       <div id="alert-dateHired" style="margin-top:10px"></div>


                        <label>Date Regularized: </label> <input tabindex="17" type="text" class="form-control datepicker" style="width:50%" name="dateRegularized" id="dateRegularized" placeholder="MM/DD/YYYY" /> 
                      <div id="alert-dateRegularized" style="margin-top:10px"></div></td>

                    </tr>

                    <tr>
                      <td><label>System User Type: </label>
                        <div id="alert-userType" style="margin-top:10px"></div>
                        @foreach ($userTypes as $type)

                        <label> <input tabindex="18" type="radio" name="userType_id" required value="{{$type->id}}" /> {{$type->name}} </label><br/>

                        @endforeach
                        
                       
                      </td>

                      <td style="padding-top:0px">
                        
                        <label>Employment Status: </label>
                        <div id="alert-status" style="margin-top:10px"></div>
                        @foreach ($statuses as $status)

                        <label> <input tabindex="19" type="radio" name="status_id" required value="{{$status->id}}" /> {{$status->name}} </label><br/>

                        @endforeach
                      

                         
                      </td>

                    </tr>

                    

                   

                  </table><p>&nbsp;</p>

               
                  <p class="text-center"> 
                    <input name="name" id="name" type="hidden" />
                    <input name="email" id="email" type="hidden"/>
                    <input name="password" id="password" type="hidden"/>
                    <input tabindex="20" type="submit" class="btn btn-lg btn-default" name='submit' value="Save" />
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
   $("select[name='position_id']").on('change', function(){
     var pos =  $(this).find(':selected').val();

     if (pos == "-1"){ //add new position
      var htmcode = "<br/><input required type='text' class='form-control' name='newPosition' id='newPosition' placeholder='Enter new job position' value='' />";

        $('#newpos').html(htmcode);
        console.log(htmcode);

     }
     
   });

   
    $("select[name='campaign_id']").on('change', function(){
                  var camp = $(this).find(':selected').val();

                  if (camp !== 0){
                    var _token = "{{ csrf_token() }}";

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
                        var htmlcode2 = "<label>Immediate Supervisor: </label><select name='immediateHead_id' id='immediateHead_id' class='form-control' style='text-transform:uppercase'>";
                        htmlcode2 += "<option value='0'> -- Select Leader -- </option>";
                        $.each(response, function(index) {

                          htmlcode2 +="<option value='"+response[index].id+"'>"+response[index].lastname+", "+response[index].firstname+"</option>";
                        
                        }); //end each

                        htmlcode2 +="</select><br/><div id='alert-immediateHead'></div>";

                        $('#newTeam').html(htmlcode2);



                      }//end success

                      }); //end ajax
                    }//end if

                  }); //end select on change


  $('input[name="dateHired"]').on('focusout',function(){ 
    var dateHired = $('input[name="dateHired"]').val();
    var dateReg = moment(dateHired,"MM/DD/YYYY");
    $('input[name="dateRegularized"]').val(dateReg.add(6,'months').format('MM/DD/Y'));

  });


   $('#addEmployee').on('submit', function(e) {

      var _token = "{{ csrf_token() }}";
      var userType_id = $('input[name="userType_id" ]:checked').val();
      var gender = $('input[name="gender" ]:checked').val();
      var birthday = $('#birthday').val();
      var firstname = $('#firstname').val();
      var middlename = $('#middlename').val();
      var lastname = $('#lastname').val();
      var status_id = $('input[name="status_id" ]:checked').val();
      var username = $('#oampi').val();
      var employeeNumber = $('#employeeNumber').val();
      var accesscode = $('#accesscode').val();
      var nickname = $('#nickname').val();
      var campaign_id = $('select[name="campaign_id"]').find(':selected').val();
      var floor_id = $('select[name="floor_id"]').find(':selected').val();

      var immediateHead_id = $('select[name="immediateHead_id"]').find(':selected').val();
      var immediateHead_Campaigns_id = $('select[name="immediateHead_id"]').find(':selected').val();
      var position_id = $('select[name="position_id"]').find(':selected').val();
      var dateHired = $('#dateHired').val();
      var dateRegularized = $('#dateRegularized').val();

      var startTraining = $('#startTraining').val();
      var endTraining  = $('#endTraining').val();

      var alertCampaign = $('#alert-campaign');
      var alertImmediateHead = $('#alert-immediateHead');
      var alertPosition = $('#alert-position');
      var alertDateHired = $('#alert-dateHired');
      var alertStatus = $('#alert-status');
      var alertUserType = $('#alert-userType');
      var alertFloor = $('#alert-floor');
      var alertNickname = $('#alert-nickname');
      var alertAccesscode = $('#alert-accesscode');

      $('input[name="name"]').attr('value', username);
      $('input[name="email"]').attr('value', username+"@openaccessbpo.net");
      $('input[name="password"]').attr('value', username);


       if ($('input[name="leadOverride"]').is(':checked')){
        var leadOverride = 1;
      } else var leadOverride = 0;



      var uname = $('#name').val();
      var passwordie = $('#password').val();

      if (!validateRequired(campaign_id,alertCampaign,"0")) { 
        console.log('not valid Program '+campaign_id); e.preventDefault(); e.stopPropagation();
        return false;
      } 
      if (!validateRequired(floor_id,alertFloor,"0")){ 
        console.log('not valid Floor'); e.preventDefault(); e.stopPropagation(); return false; 

        } 
         if (!validateRequired(immediateHead_id,alertImmediateHead,"0")){ 
        console.log('not valid Head'); e.preventDefault(); e.stopPropagation(); return false; 

        } 

        if (!validateRequired(position_id,alertPosition,"0")){ 
          console.log('not valid Position '+position_id); e.preventDefault(); e.stopPropagation();return false;

        } else if (position_id == "-1"){

          var newPos = $('input[name="newPosition"]').val();
          if (!validateRequired(newPos,alertPosition,"Enter new job position")){
            console.log('not valid PosValue: '+newPos); e.preventDefault(); e.stopPropagation(); return false; 
          } else {
            //save first the new position
            console.log("save pos first");
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
                                  console.log("Saved position");

                                  //check the rest of the form
                                  if( !validateRequired(dateHired,alertDateHired,"") || !validateRequired(status_id,alertStatus,"") || !validateRequired(userType_id,alertUserType,"")){
                                    e.preventDefault(); e.stopPropagation(); return false; 

                                  } else {

                                    //save employee
                                    console.log("Save employee then");
                                    var emailaddie = uname+"@openaccessbpo.net";
                                    saveEmployee(uname,firstname,middlename,lastname,nickname,gender,birthday,employeeNumber,accesscode,emailaddie,dateHired,dateRegularized, startTraining, endTraining, userType_id,status_id,posID,campaign_id,floor_id,immediateHead_Campaigns_id, _token);

                                  }



                                }//end success

                    }); //end ajax

          
          }//end else valid new position

        } else {

           if( !validateRequired(dateHired,alertDateHired,"") || !validateRequired(status_id,alertStatus,"") || !validateRequired(userType_id,alertUserType,"")){
             e.preventDefault(); e.stopPropagation(); return false; 
           } else {
            //save employee
            var emailaddie = username+"@openaccessbpo.net";
            
            console.log("pumasok else -1");
            console.log("uname: "+ uname);
            console.log("email: "+ emailaddie);
            // console.log("immediateHead_id: "+ immediateHead_id);
            // console.log("campaign_id: "+ campaign_id);
            console.log("position_id: "+ position_id);
            console.log("userType_id: "+ userType_id);
            console.log("status_id: "+ status_id);
            
            saveEmployee(uname,firstname,middlename,lastname,nickname,gender,birthday,employeeNumber,accesscode,emailaddie,dateHired,dateRegularized, startTraining, endTraining, userType_id,status_id,position_id,campaign_id,floor_id, immediateHead_Campaigns_id, _token );

          }

        } //end else if -1
      
      e.preventDefault(); e.stopPropagation();
      return false;
    }); //end addEmployee





   $( ".datepicker" ).datepicker();

       

   });

function saveEmployee(uname,firstname,middlename,lastname,nickname,gender,birthday,employeeNumber,accesscode,email,dateHired,dateRegularized, startTraining, endTraining, userType_id,status_id,position_id,campaign_id,floor_id,immediateHead_Campaigns_id, _token){

   //save movement

   $.ajax({
            url:"{{action('UserController@store')}}",
            type:'POST',
            data:{
              'name': uname,
              'firstname': firstname,
              'middlename': middlename,
              'lastname': lastname,
              'nickname':nickname,
              'gender':gender,
              'birthday':birthday,
              'employeeNumber': employeeNumber,
              'accesscode': accesscode,
              'email': email,
              'password': uname,
              'updatedPass': false,
              'dateHired': dateHired,
              'dateRegularized': dateRegularized,
              'startTraining': startTraining,
              'endTraining': endTraining,
              'userType_id': userType_id,
              'status_id': status_id,
              'position_id': position_id,
              'immediateHead_Campaigns_id':immediateHead_Campaigns_id,
              'campaign_id': campaign_id,
              'floor_id': floor_id,
              //'immediateHead_id': immediateHead_id,
              '_token':_token},

            error: function(response2)
            { console.log("Error saving employee: ");
            console.log(response2); return false;
            },
            success: function(response2)
            {
              console.log(dateRegularized);
              console.log(response2);
               $('input[name="submit"').fadeOut();
                      var htmcode = "<span class=\"success\"> <i class=\"fa fa-save\"></i> Employee  data saved. <br /><br/>";
                     
                      htmcode += "<a href=\"{{action('UserController@index')}}\" class='btn btn-sm btn-default text-black pull-right'><i class='fa fa-reply'></i> Back to All Employees </a> <br/><br/>";
                      
                      $('#alert-submit').addClass('alert alert-success').fadeIn().html(htmcode); 

                      window.location.href = "../editUser/"+response2.user_id+"?page=2";

            }
          });


   

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

<!-- <script type="text/javascript" src="{{asset('public/js/jquery.validate.js')}}"></script> -->

@stop