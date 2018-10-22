@extends('layouts.main')


@section('metatags')
  <title>All OAMPI Leaders</title>
    <meta name="description" content="all teams">

@stop


@section('content')




<section class="content-header">

      <h1>
       OAMPI Leaders
        <small>executives, managers, team leaders, OICs...</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li><a href="{{action('CampaignController@index')}}">All Campaigns</a></li>
        <li class="active">Leaders</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-heading"></div>
            <div class="box-body">

              

                <table id="heads" class="table table-bordered table-striped">
                      <thead>
                      <tr class="text-success">
                        
                        <th class="col-xs-2">Last name</th>
                        <th class="col-xs-2">First name</th>
                        <th class="col-xs-2">Position</th>
                        
                       
                        <th class="col-xs-3">Program / Department</th>
                         
                        <th class="col-xs-2 text-center">Actions</th>

                         
                      </tr>
                      </thead>
                      <tbody>

                      @foreach ($heads->groupBy('userID') as $member)

                         <tr>
                            
                            <td style="text-transform: uppercase;">{{$member[0]->lastname}}</td>
                            @if (is_null($member[0]->nickname))
                            <td>{{$member[0]->firstname}}</td>
                            @else
                            <td>{{$member[0]->nickname}}</td>
                            @endif
                            <td>{{$member[0]->jobTitle}}</td>
                            
                            
                            <td>@foreach($member as $camp)
                                &nbsp;| &nbsp;<a style="font-weight: bold" target="_blank" href="{{action('CampaignController@show',$camp->programID)}}"> {{$camp->program}} </a>
                                @endforeach
                            </td>
                            
                            <td class="text-center">
                              <div id="buttons{{$member[0]->ihID}}">
                               <?php if ( OAMPI_Eval\UserType::find(Auth::user()->userType_id)->roles->pluck('label')->contains('REMOVE_LEADER') ){ ?> 
                          

                              <a class="assign btn btn-xs btn-default" data-toggle="modal" data-target="#myModal2{{$member[0]->ihID}}" href="#" id="assign{{$member[0]->ihID}}" data-id="{{$member[0]->ihID}}" firstname="{{$member[0]->firstname}}" lastname="{{$member[0]->lastname}}" ><i class="fa fa-plus"></i> Assign New Program</a>
                              

                             
                              <?php } else { ?>

                              <span class="text-success"><small><em>* Access Denied *</em></small></span>

                              <?php } ?>
                            </div>

                              <div class="assignTo" id="assign{{$member[0]->ihID}}"><br/>
                                
                              </div>

                            </td>
                            
                            
                            
                         </tr>


                         <div class="modal fade" id="myModal2{{$member[0]->ihID}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                              <div class="modal-content">
                                <div class="modal-header">
                                  
                                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                    <h4 class="modal-title" id="myModalLabel"> Assign New Department/Program</h4>
                                  
                                </div>
                                <div class="modal-body">
                                  <label>Choose a new Program for {{$member[0]->firstname}} to handle: </label>
                                  <select name="newCampaign" id="newCampaign{{$member[0]->ihID}}" class="form-control">
                                    <option value="0">-- Select Department --</option>
                                    @foreach ($campaigns as $camp)
                                      @if ($camp->name !== $member[0]->program)
                                      <option value="{{$camp->id}}">{{$camp->name}} </option>
                                      @endif
                                    @endforeach
                                  </select><br/>
                                 



                                </div>
                                <div class="modal-footer no-border">
                                 
                                    <button id="save{{$member[0]->ihID}}" data-id="{{$member[0]->ihID}}" type="submit" class="btn btn-success glyphicon glyphicon-floppy-disk  "> Save</button>
                                  
                                  <button type="button" class="btn btn-primary" data-dismiss="modal">Cancel</button>
                                </div>
                              </div>
                            </div>
                          </div>

                        

                         

                  
                      @endforeach

                      
                      </tbody>
                      
                </table>
                  

  

            </div>

          </div>

        </div>

      </div>

     

    </section>

@stop

@section('footer-scripts')
<script>
  $(function () {
    $("#heads").DataTable({
      "responsive":true,
      "scrollX":true,
      "stateSave": true,
       "processing":true,
       "dom": '<"col-xs-1"f><"col-xs-11 text-right"l><"clearfix">rt<"bottom"ip><"clear">',
      "order": [[ 0, "asc" ]],
     
      "lengthChange": true,
      "oLanguage": {
         "sSearch": "<small>To refine search, simply type-in</small><br> any values you want to look for:",
         "class": "pull-left"
       }
    });

    $('.assignTo').hide();

    $('#heads tbody').on( 'click', 'a.assign', function (e) {
      e.preventDefault();
      var empID = $(this).attr('data-id');

      $('.assignTo#assign'+empID).fadeIn();
      //$('#buttons'+empID).fadeOut();
      console.log(empID);
      
      

       var employeeNumber = $(this).attr('data-id');
       
       var campaign_id = $(this).attr('campaign_id');
       
       var _token = "{{ csrf_token() }}";

       // console.log("employeeNum:" +employeeNumber);
       // console.log("firstname:" +firstname);
       // console.log("lastname:" +lastname);
       // console.log("campaign_id:" +campaign_id);

       $('#save'+empID).on('click', function(e){
         e.preventDefault();
        
         var employeeNumber = $(this).attr('data-id');
         var campaign_id = $('#newCampaign'+employeeNumber).find(':selected').val();
         
         var _token = "{{ csrf_token() }}";

         if (campaign_id == '0')
         {
          alert("Please specify a Department.");
         } else {

          $.ajax({
                      url:"{{action('ImmediateHeadCampaignController@store')}}",
                      type:'POST',
                      data:{employeeNumber:employeeNumber, campaign_id:campaign_id, _token:_token},
                      error: function(response)
                      {
                          
                        
                        console.log("Error saving leader ");
                        console.log(response);

                          
                          return false;
                      },
                      success: function(response)
                      { 

                        //$('#assign'+employeeNumber).html('<td colspan="6"><p class="text-center text-success">Promoting employee as a New Leader...</p></td>').delay(2000).fadeOut("slow");
                        location.reload();
                        $('#assign'+employeeNumber).fadeOut();
                        $('#assign'+employeeNumber).fadeIn().html('<p class="text-center text-success">Saving Leader...</p></td>').delay(2000).fadeOut("slow");
                        
                        console.log("Leader saved.");
                        console.log(response);

                          return true;
                      }
                  });

         }
         
         
          

                

       });

    });


                        
                       

      




    

  });


    
</script>
@stop