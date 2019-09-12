@extends('layouts.main')


@section('metatags')
  <title>Manpower Requests | EMS </title>
    <meta name="description" content="all teams">

@stop


@section('content')




<section class="content-header"  style="margin-bottom:50px">

      <h1>
       <a href="{{action('UserController@show',$personnel->id)}} ">Manpower </a>
        <small>Requests</small>
        <a class="btn btn-xs btn-danger" href="{{action('ManpowerController@create')}} "><i class="fa fa-plus"></i> New Request </a>
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li><a href="{{action('MovementController@index')}}">My Requests</a></li>
        <li class="active">Manpower</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      


      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-heading"></div>
            <div class="box-body" style="min-height: 1100px; background: url('../storage/uploads/solutions_wall.jpg')bottom center no-repeat; background-size: 100%">
              <p class="text-left"><i class="fa fa-info-circle"></i> Fill out the form below with complete details about your request for manpower</p>
              <div class="row">
                <div class="col-lg-1"></div>
                <div class="col-lg-10">
                  <h1 class="text-center text-primary">Manpower <span class="text-orange">Request Form</span></h1>
                  <form id="manpower" action="POST" class="form-">
                    <div class="row">
                      <div class="col-lg-4">
                        <br/><br/>
                        <h4 class="text-primary">Reason for request:</h4>
                        @foreach($reasons as $r)

                        &nbsp;<label><input required="required" type="radio" name="manpower_reason_id" id="manpower_reason_id" value="{{$r->id}}" /> &nbsp;{{$r->name}} </label>

                        @endforeach

                        <br/><br/><h4 class="text-primary">Manpower Type:</h4>
                        @foreach($types as $r)

                        &nbsp;<label><input type="radio" required="required" name="manpower_type_id" value="{{$r->id}}" /> &nbsp;{{$r->name}} </label>

                        @endforeach


                        <br/><br/><h4 class="text-primary">Hiring Source:</h4>
                        @foreach($sources as $r)

                        &nbsp;<label><input required="required" type="radio" name="manpower_source_id" value="{{$r->id}}" /> &nbsp;{{$r->name}} </label>

                        @endforeach

                        <br/><br/>
                        <label style="font-weight: normal; font-size: 1.1em;"><input type="checkbox" name="mktgBoost" id="mktgBoost" value="1" /> &nbsp;Request Facebook ad boost from MARKETING</label>



                        <br/><h4 class="text-primary">Employment Status:</h4>
                        @foreach($statuses as $r)

                          @if($r->id == 1)
                          &nbsp;<label><input type="radio" name="manpower_status_id" value="{{$r->id}}" /> &nbsp;{{$r->name}} (1-3 months) </label><br/>
                          @else
                          &nbsp;<label><input type="radio" name="manpower_status_id" value="{{$r->id}}" /> &nbsp;{{$r->name}} </label><br/>
                          @endif

                        @endforeach
                      </div>

                      <div class="col-lg-4">
                         <br/><br/>
                        <h4 class="text-primary">Total number:</h4>
                        <input type="text" required="required"  name="howMany" id="howMany" class="form-control" style="width: 40%" />
                        
                        <br/><br/>
                        <h4 class="text-primary">Position / Title:</h4>
                        <select name="position_id" id="position_id" class="form-control">
                          <option value="0">- Select job title -</option>
                          @foreach ($positions as $p)
                          <option value="{{$p->id}}">{{$p->name}} </option>
                          @endforeach
                          <option value="-1">* * * create NEW * * *</option>


                          
                        </select>

                        <div id="newpos"><br/><label>New position: <input class="form-control" type="text" id="newposition" /></label> </div>

                        <hr/>
                        &nbsp;<label><input required="required" type="radio" name="lob" value="Voice" /> &nbsp;Voice </label>
                        &nbsp;<label><input required="required" type="radio" name="lob" value="Non-voice" /> &nbsp;Non-Voice </label>
                        &nbsp;<label><input required="required" type="radio" name="lob" value="Voice & Non-Voice" /> &nbsp;Both </label>

                         <br/><br/><h4 class="text-primary">Employment Status [Foreigners]:</h4>
                        @foreach($foreign as $r)

                        &nbsp;<label><input type="radio" name="manpower_foreignStatus_id" value="{{$r->id}}" /> &nbsp;{{$r->name}} </label><br/>

                        @endforeach

                        
                       

                      </div>

                     <div class="col-lg-4">
                      <br/><br/>
                      <h4 class="text-primary">Program / Department:</h4>
                      <select name="campaign_id" id="campaign_id" class="form-control">
                          <option value="0">* Select program *</option>
                          @foreach($programs as $r)

                          <option value="{{$r->id}}">{{$r->name}} </option>
                          @endforeach
                           <option value="-1">* * * NEW * * *</option>
                      </select>
                      <div id="newcamp"><br/><label>New Program: <input class="form-control" type="text" id="newcampaign" /></label> </div>
                        

                      <br/><br/>
                      <h4 class="text-primary">Start Date:</h4>
                      <input tabindex="14" type="text" required="required" class="form-control datepicker" style="width:50%" name="trainingStart" id="trainingStart" placeholder="MM/DD/YYYY" /><br/><br/>


                       <h4 class="text-primary">Notes / Details:</h4>
                       <textarea name="notes" id="notes" class="form-control"></textarea>

                       <br/><br/><button type="submit" id="submit" class="btn btn-lg btn-success pull-right"><i class="fa fa-file-o"></i> Submit Request</button>

                       <a style="margin-left: 10px" id="allreq" href="{{action('ManpowerController@index')}}" class="btn btn-xs btn-default pull-right"><i class="fa fa-file-o"></i> View All Request</a>
                       <a id="newReq" href="{{action('ManpowerController@create')}}" class="btn btn-lg btn-danger pull-right"><i class="fa fa-plus"></i> New Request</a> </div>
                     </div>
                    </div>
                    
                  </form>
                </div>
                <div class="col-lg-1"></div>
              </div>
             
              <!-- <br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/> -->

              


  

            </div>

          </div>

        </div>

      </div>

     

    </section>

@stop

@section('footer-scripts')
<script>
  $(function () {
    $( ".datepicker" ).datepicker();
    $('#newpos,#newcamp,#newReq,#allreq').fadeOut();

    $('#position_id').on('change',function(){
      var v = $(this).find(':selected').val();
      if (v == '-1')
      {
        $('#newpos').fadeIn();

      }else $('#newpos').fadeOut();

    });

    $('#campaign_id').on('change',function(){
      var v = $(this).find(':selected').val();
      if (v == '-1')
      {
        $('#newcamp').fadeIn();

      }else $('#newcamp').fadeOut();

    });

    $('#manpower').on('submit', function(e){

        e.preventDefault(); e.stopPropagation();
        var _token = "{{ csrf_token() }}";
        var campaign_id = $('#campaign_id').find(':selected').val();
        var manpower_reason_id = $('input[name="manpower_reason_id" ]:checked').val();
        var manpower_type_id = $('input[name="manpower_type_id" ]:checked').val();
        var howMany = $('#howMany').val();
        var manpower_source_id = $('input[name="manpower_source_id"]:checked').val();
        var position_id = $('select[name="position_id"]').find(':selected').val();
        var lob = $('input[name="lob"]:checked').val();
        var manpower_status_id = $('input[name="manpower_status_id"]:checked').val();
        var manpower_foreignStatus_id = $('input[name="manpower_foreignStatus_id"]:checked').val();
        var trainingStart = $('#trainingStart').val();
        var notes = $('#notes').val();
        var mktgBoost = null;

        if ($('input#mktgBoost').is(':checked')) mktgBoost=1;

        if ((manpower_status_id === "" || manpower_status_id == null)&&(manpower_foreignStatus_id === "" || manpower_foreignStatus_id == null))
        {
          $.notify("Kindly specify employment status",{className:"error", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );
        }else if(position_id == 0){
          $.notify("Kindly specify job title for this request",{className:"error", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );
        

        }else if (campaign_id == 0){
          $.notify("Kindly specify which program/department this request is for.",{className:"error", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );

        }
        else
        {

          //**** check first kung may new positions and dept
          if(position_id == '-1')
          {
              var newPos = $('#newposition').val();
              $.ajax({
                        url:"{{action('PositionController@store')}} ",
                        async:false,
                        type:'POST',
                        data:{
                          'name': newPos,
                          _token:_token},

                        error: function(response)
                        { console.log("Error saving position: "); return false;
                        },
                        success: function(response)
                        {
                          position_id = response.id;
                        }//end success

              }); //end ajax
          }

          if(campaign_id == '-1')
          {
            var newPos = $('#newcampaign').val();
              $.ajax({
                        url:"{{action('CampaignController@store')}} ",
                        async:false,
                        type:'POST',
                        data:{
                          'name': newPos,
                          _token:_token},

                        error: function(response)
                        { console.log("Error saving campaign: "); return false;
                        },
                        success: function(response)
                        {
                          campaign_id = response.id;
                        }//end success

              }); //end ajax
          }

          console.log('new values:');
          console.log(position_id);
          console.log(campaign_id);

          $('#submit').fadeOut();

          $.ajax({

                url:"{{action('ManpowerController@saveRequest')}}",
                type:'POST',
                data:{
                  'campaign_id': campaign_id,
                  'manpower_reason_id': manpower_reason_id,
                  'manpower_type_id': manpower_type_id,
                  'howMany': howMany,
                  'manpower_source_id': manpower_source_id,
                  'position_id': position_id,
                  'lob': lob,
                  'manpower_status_id': manpower_status_id,
                  'manpower_foreignStatus_id': manpower_foreignStatus_id,
                  'trainingStart': trainingStart,
                  'notes': notes,
                  'mktgBoost': mktgBoost,
                  _token: _token

                },
                error: function(response)
                { console.log("Error saving request: ");
                  console.log(response);
                  $.notify("Error processing request. Please check all submitted fields and try again.",{className:"error", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} ); 
                  return false;
                },
                success: function(response)
                {
                  console.log(response);
                  $.notify("Manpower request sent to \nRecruitment team for processing.",{className:"success", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );
                  $('#newReq').fadeIn();
                  $('#allreq').fadeIn();

                }

          });

          


          

        }

    });
    
  });
</script>
@stop