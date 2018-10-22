@extends('layouts.main')

@section('metatags')
<title>Team Movement | OAMPI Evaluation System</title>

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
        Team Movement
        <small>Manage all team movement within {{$myCampaign->first()->name}} by a simply dragging and dropping members into a new team</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="{{action('ImmediateHeadController@index')}}"> All Team Leaders</a></li>
        <li class="active">Team Movement</li>
      </ol>
    </section>

     <section class="content">
      @if (Auth::user()->userType_id == 1 || Auth::user()->userType_id == 2 ) 
      <a href="{{action('ImmediateHeadController@create')}} " class="btn btn-sm btn-default btn-flat pull-right"><i class="fa fa-users"></i> Add New Leader</a>
     
      @endif

          <div class="row" style="margin-top:50px">
            <div class="dhe-example-section" id="empMovements">
            <div id="movements">

            <div class="col-lg-6"> <!-- ******************* LEFT COLUMN ***************** -->

              <!-- TABLE: LEFT -->
              <div class="box box-orange" style="background:none">
                <div class="box-header with-border">
                  <h3 class="box-title">From Current {{$myCampaign->first()->name}} Team Leader</h3>
                  <p></p>

                  <select class="form-control teams" name="teamMembers" id="teamMembers">
                    <option>Select a team leader</option>
                    @foreach ($TLs as $team)
                    <option value="{{$team->id}}" data-teamID="{{$team->campaign_id}}" data-teamname="{{$team->firstname}} {{$team->lastname}}"> {{$team->lastname}}, {{$team->firstname}} </option>

                    @endforeach
                  </select>

                  
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                  
                  
                    
                    <div class="members" id="fromTeam">
                      <h4 class="text-center" id="fromTeamTitle"> </h4>

                      <!-- BEGIN: XHTML for example 1.3 -->

                      

                        <div class="col-lg-12">

                          <ul class="sortable-list row" id="fromHolder">
                           
                          </ul>

                        </div>

                        

                        

                        

                        <div class="clearer">&nbsp;</div>

                      

                      <!-- END: XHTML for example 1.3 -->

                    </div>
                  

                  <!-- /.table-responsive -->
                </div>
                <!-- /.box-body -->
                <div class="box-footer clearfix" style="background:none">
                  
                  
                </div>
                <!-- /.box-footer -->
              </div>
              <!-- /.box -->
            </div><!--end left -->


            <div class="col-lg-6"> <!-- ******************* RIGHT COLUMN ***************** -->

              <!-- TABLE: right -->
              <div class="box box-orange" style="background:none">
                <div class="box-header with-border">
                  <h3 class="box-title">To New {{$myCampaign->first()->name}} Team Leader</h3>
                  <p></p>

                  

                  <select class="form-control teams" name="teamMembers2" id="teamMembers2">
                    <option>Select a team</option>
                   
                  </select>




                  

                  



                  
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                  <div class="members" id="toTeam" style="padding-top:50px">
                     <h4 class="text-left" id="toTeamTitle"></h4>
                     <a href="#" class="btn btn-sm btn-success pull-left saveBtn" id="saveBtn" style="margin-bottom:15px"><i class="fa fa-save"></i> Save Changes</a>
                     <div class="row" >
                          <div class="col-sm-12 callout callout-success pull-left " id="savedMsg"><em>Team movement saved.</em></div>
                      </div>
                     

                      <!-- BEGIN: XHTML for example 1.3 -->

                      

                        

                        <div class="col-lg-12">

                          <ul class="sortable-list row" id="toHolder">
                            
                          </ul>

                        </div>

                        <div class="clearer">&nbsp;</div>

                      

                      <!-- END: XHTML for example 1.3 -->

                    </div>
                </div>
                <!-- /.box-body -->
                <div class="box-footer clearfix" style="background:none">
                  
                  
                </div>
                <!-- /.box-footer -->
              </div>
              <!-- /.box -->
            </div><!--end left -->


            </div> <!--end #movements -->
            </div> <!--end #empMovements -->

          
          </div><!-- end row -->

       
     </section>
          



@endsection


@section('footer-scripts')

<script>
  $.widget.bridge('uibutton', $.ui.button);
</script>



<!-- Page script -->
<script>
  
  $(document).ready(function(){

    $('.saveBtn, #savedMsg').hide();

  

  // Example 1.3: Sortable and connectable lists with visual helper
  $('#movements .sortable-list').sortable({
    connectWith: '#movements .sortable-list',
    placeholder: 'placeholder',
    grid: [ 20, 10 ],
    tolerance: "pointer",
    receive: function(event,ui){ $('.saveBtn').fadeIn().addClass('btn-success').removeClass('btn-default');}
  });



 

});

  $('.members').hide();
  $('select[name="teamMembers2"]').prop('disabled','true');
  
  $('select[name="teamMembers"]').change(function(){  
    var teamName = $(this).find(':selected').attr('data-teamname');
    var teamID = $(this).find(':selected').val();
    var campaignID = $(this).find(':selected').attr('data-teamID');
    var divHolder = $('#fromTeamTitle');

    $("#fromHolder").html(" ");
    $('.members#fromTeam').fadeIn();

    displayMembers(teamName,divHolder, teamID);

     $.getJSON('immediateHead/'+teamID+'/members?except='+campaignID, function(data) 
     {
               

                $.each(data, function(index) {
                   var imglink = "{{url('/')}}/public/img/employees/"+data[index].id+".jpg";
                    var lname = data[index].lastname;
                    var fname = data[index].firstname;
                    var empid = data[index].id;
                    var ftype = 'jpg';
                    var holder = "#fromHolder";
                  imageExists(ftype, imglink, fname, lname, empid, holder);

                   //$("#fromHolder").append('<li class="sortable-item col-sm-3"><img src="./public/img/employees/'+data[index].id+'.jpg" class="img-circle" width="80" height="80" alt="'+data[index].lastname+', '+data[index].firstname + '" /><br/>'+data[index].lastname+', <small>'+data[index].firstname + ' </small></li>');
                   
                });

               



      });

      $('select[name="teamMembers2"]').prop('disabled',false);

               
                var divHolder2 = $('#toTeamTitle');

                $("#teamMembers2").html(" ");
                $("#teamMembers2").append('<option>Select a team</option>');
                

                 $.getJSON('./getOtherTeams?except='+teamID, function(data) {
                            $.each(data, function(index) {
                              //imageExists(ftype, imglink, fname, lname, empid);

                               $("#teamMembers2").append('<option value="'+data[index].id+'" data-teamname="'+data[index].firstname+' '+ data[index].lastname+'"> '+data[index].lastname+', '+ data[index].firstname+' </option>');
                               
                               
                            });
                  });


      

    
   });




  $('select[name="teamMembers2"]').change(function(){  


    var teamName = $(this).find(':selected').attr('data-teamname');
    var teamID = $(this).find(':selected').val();
    var divHolder = $('#toTeamTitle');

    $("#toHolder").html(" ");
    $('.members#toTeam').fadeIn();
    $(".saveBtn").hide();

    displayMembers(teamName,divHolder, teamID);

     $.getJSON('immediateHead/'+teamID+'/members', function(data) {
                $.each(data, function(index) {

                  var imglink = "{{url('/')}}/public/img/employees/"+data[index].id+".jpg";
                  var lname = data[index].lastname;
                  var fname = data[index].firstname;
                  var empid = data[index].id;
                  var ftype = 'jpg';
                  var holder = "#toHolder";
                  imageExists(ftype, imglink, fname, lname, empid, holder);
                 
                   
                });
            });
    
   });

  $('#saveBtn').on('click', function(){

    $(this).removeClass('btn-success').addClass('btn-default');

        
        var toDashers = $("#toHolder .dasher").map(function(){return $(this).attr("data-empid");}).get();
        var fromDashers = $("#fromHolder .dasher").map(function(){return $(this).attr("data-empid");}).get();
        var teamtoID = $("#toTeamTitle").attr('teamID');
        var teamfromID = $("#fromTeamTitle").attr('teamID');
        console.log(fromDashers);
        console.log(toDashers);
        var _token = "{{ csrf_token() }}";

        $.ajax({
            type: "POST",
            url: "{{action('MovementController@updateMovement')}}", 
            data: {teamtoID:teamtoID, teamfromID:teamfromID, fromDashers:fromDashers, toDashers:toDashers, _token:_token},
            success: function(response) {

              $('#savedMsg').fadeIn().delay(2000).fadeOut();
              $('#saveBtn').fadeOut();
              
              console.log(response);
              

               }
                
                
               

                
                
        });
        return false;
    
  });




  function displayMembers(teamName,divHolder,teamID){

    divHolder.html("Team " + teamName);
    divHolder.attr('teamID', teamID);

  }

  function imageExists(filetype, urlimg, fname, lname,empid, holder){
    $.ajax({
                      url:urlimg,
                      type:'HEAD',
                      error: function()
                      {
                          if (filetype == 'jpg'){
                            imageExists('png',urlimg,fname,lname,empid, holder);

                          } else { 
                            $(holder).append('<li class="sortable-item col-sm-3"><img class="dasher user-image" data-empid="'+empid+'" src="./public/img/oam_favicon1-55027f4ev1_site_icon-256x256.png" width="80" height="80" alt="User Image"><br/>'+lname+',<br> <small> '+fname+ ' </small></li>');

                          }
                          
                          return false;
                      },
                      success: function()
                      {
                          $(holder).append('<li class="sortable-item col-sm-3"><img class="dasher" data-empid="'+empid+'" src="./public/img/employees/'+empid+'.jpg" class="img-circle" width="80" height="80" alt="'+lname+', '+fname + '" /><br/>'+lname+', <br><small> '+fname + ' </small></li>');
                          return true;
                      }
                  });
  }



  /*
   * Custom Label formatter
   * ----------------------
   */
  function labelFormatter(label, series) {
    return '<div style="font-size:13px; text-align:center; padding:2px; color: #fff; font-weight: 600;">'
        + label
        + "<br>"
        + Math.round(series.percent) + "%</div>";
  }
</script>
<!-- end Page script -->

<!-- <script type="text/javascript" src="{{asset('public/js/dashboard.js')}}"></script> -->

@stop