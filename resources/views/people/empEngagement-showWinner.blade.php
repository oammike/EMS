@extends('layouts.main')

@section('metatags')
<title>Employee Engagement Activities | EMS</title>
@endsection

@section('content')


  <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>&nbsp;</h1>
      <ol class="breadcrumb">
        <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="{{action('UserController@index')}}"> All Employees</a></li>
        <li class="active">My Team</li>
      </ol>
    </section>

     <section class="content">
      <!-- ******** THE DATATABLE ********** -->
          <div class="row">
             <div class="col-lg-12">

              <div class="box box-primary"  style="background: rgba(256, 256, 256, 0.4);padding:30px">
                
                <h1>Congratulations to all the Winners!!!</h1>
                <p>{{$engagement[0]->activity}} : {{$engagement[0]->description}}</p>
                <div class="item text-center" style="background-size:98%;background-position: top center; background-repeat: no-repeat; background-image: url('../storage/uploads/frightful2020.jpg'); background-color: #000" >
                                   
                     <p style="padding: 5px 30px; margin-bottom: 0px; color:#fff"><div style="margin-top: 95%"></div> 
                     
                     
                          <h2 class="text-center text-danger" style="padding: 30px;">Top 3 Frightful Tales:<br/><br/></h2>
                      <div class="row" style="padding: 0px 70px;color:#fff">
                        <?php $ord=1; ?>
                        @foreach($finalTally->sortByDesc('grandTotal') as $entry)
                        <?php switch ($ord) {
                          case '1':{$order="1st";}break;case '2':{$order="2nd";}break;case '3':{$order="3rd";}break;
                          
                          
                        } ?>

                        @if ($ord <=3)

                        <div class="col-md-4">
                          <h2 style="line-height: 0.5em;"><strong>{{$order}}</strong> <br/><span style="font-size: 0.5em;  font-weight: normal;"> "{{$entry['title'] }}"</span></h2>
                          <a class="text-primary" href="{{action('UserController@show',$entry['user_id'])}}" target="_blank"><img src="../public/img/employees/{{$entry['user_id']}}.jpg" width="60%" class="img-circle" /><br/>
                            <h4 class="text-primary" style="text-transform: uppercase;"> {{$entry['firstname']}} {{$entry['lastname']}} <br/>
                                                          <span style="font-size: x-small;">{{$entry['jobTitle']}} </span><br/>
                                                         <strong style="font-size: smaller;">{{$entry['program']}}</strong>
                                </h4></a>
                          <h5> ({{$entry['grandTotal'] }}%)</h5>

                         
                        </div>
                        @endif
                        <?php $ord++;?>
                        @endforeach
                        


                      </div><br/><br/>

                  </div>

                </div> 
                





                @if( $engagement[0]->withVoting === 1 )
                <hr/>
                <div class="row">
                 
                  
                  <div id="entry" class="col-sm-12 text-center" style="background: rgba(256, 256, 256, 0.7);padding:30px" >
                    
                     <a class="btn btn-lg btn-success" data-toggle="modal" data-target="#warning"><i class="fa fa-file-text"></i> Read all Entries</a>&nbsp;&nbsp;
                     <a  class="btn btn-lg btn-primary" href="{{action('EngagementController@tallyVotes',$id)}}"><i class="fa fa-bar-chart"></i> See Voting Results</a>
                       
                   
                    

                    <!-- @if ($hasEntry)

                    @else
                    <a id="submit" class="btn btn-lg btn-success pull-right" style="margin-top: 20px"> Submit</a>

                    @endif -->
                    
                  </div>
                </div>

                @endif




              </div><!--end box-primary-->


             

             

          </div><!--end main row-->
      </section>

      <div class="modal fade" id="warning" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title text-danger" id="myModalLabel"><i class="fa fa-exclamation-triangle"></i> Disclaimer</h4>
              
            </div>
            <div class="modal-body">
              <p>Understand that some of these stories may contain sensitive topics and themes that some people may find triggering.</p>
              <p>Proceeding means that you understand the potential risks. Each story includes a trigger warning to give you caution before you start reading.</p>
            </div>
            <div class="modal-footer no-border">
              {{ Form::open(['route' => ['employeeEngagement.voteNow',$id], 'method'=>'GET','class'=>'btn-outline pull-right', 'id'=> "show" ]) }} 

                <button type="submit" class="btn btn-primary"><i class="fa fa-check"></i> Proceed </button>
              
              <button type="button" class="btn btn-default" data-dismiss="modal">No, thank you.</button>{{ Form::close() }}
            </div>
          </div>
        </div>
      </div>
      

      
 

    



@endsection


@section('footer-scripts')

<script>
  $.widget.bridge('uibutton', $.ui.button);
</script>



<!-- Page script -->
<script>
 
  $(function () {
   'use strict';

   @if($hasEntry)

    $('#saveTrigger').fadeOut();

    $('#saveTrigger').on('click',function()
    {
      var _token = "{{ csrf_token() }}";

      var triggers = $('input[name="triggers[]"]:checkbox:checked').map(function() {
                  return this.value;
              }).get();

       $.ajax({

                  url:"{{action('EngagementController@saveTriggers')}}",
                  type:'POST',
                  data:{

                    'engagement_id': "{{$id}}",
                    'triggers':triggers,
                    _token: _token

                  },
                  error: function(response)
                  { console.log("Error saving entry: ");
                    console.log(response);
                    $.notify("Error processing request. Please check all submitted fields and try again.",{className:"error", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} ); 
                    return false;
                  },
                  success: function(response)
                  {
                    console.log(response);
                    $.notify("Entry updated. \nThank you for participating.",{className:"success", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );
                    $('#saveTrigger').fadeOut();$('#editTrigger').fadeIn();
                    window.location.reload(true);


                  }

            });
      

    });

    $('#editTrigger').on('click',function(){
      $('#triggers').html();
      $('#editTrigger').fadeOut(); $('#saveTrigger').fadeIn();

      var htmlcode = "<br/>";
      
      @foreach($triggers as $trigger)

        @if( in_array($trigger->id,$myTriggerArray) )

          htmlcode += "<label style=\"font-size: x-small; margin-left: 10px\"><input checked=\"checked\" type=\"checkbox\" name=\"triggers[]\" value=\"{{$trigger->id}}\" />&nbsp; {{$trigger->name}} </label>";

        @else 

          htmlcode += "<label style=\"font-size: x-small; margin-left: 10px\"><input type=\"checkbox\" name=\"triggers[]\" value=\"{{$trigger->id}}\" />&nbsp; {{$trigger->name}} </label>";

        @endif

        
      @endforeach

      $('#triggers').html(htmlcode);


    });



    $('h4.edit a').on('click',function(){

      var itemID = $(this).attr('data-itemID');
      var itemType = $(this).attr('data-itemType');
      var items = $(this).siblings();
      var contents = items[0].innerText;
      var btn = $(this);
      var _token = "{{ csrf_token() }}";

      items.html("");
      btn.fadeOut();

      if (itemType === "TXT")
        var htmlcode = "<input type='text' class='form-control'  id='"+itemID+"' value='"+contents+"' /> ";
      else if(itemType === "PAR")
        var htmlcode = "<textarea id='"+itemID+"'  class='form-control' rows='20' >"+contents+"</textarea>";

      items.append(htmlcode);


      items.focusout(function(){

          var newcontents = items[0];
          var newitems = $('#'+itemID).val();
          console.log(newitems);
          $.ajax({

                  url:"{{action('EngagementController@updateEntry')}}",
                  type:'POST',
                  data:{

                    'itemID': itemID,
                    'value': newitems,
                    _token: _token

                  },
                  error: function(response)
                  { console.log("Error saving entry: ");
                    console.log(response);
                    $.notify("Error saving entry.",{className:"error", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} ); 
                    return false;
                  },
                  success: function(response)
                  {
                    console.log(response);
                    $.notify("Entry updated. \nThank you for participating.",{className:"success", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );
                    btn.fadeIn();window.location.reload(true); 
                   

                  }

            });


          
          //
      });
      

      //console.log($(this).attr('data-itemID'));

    });



   @else

      $('#submit').on('click',function(){

      var items = $('#entry .form-control').map(function( i, e ) {
                    return $( e ).val();
                  }).get();
      var itemIDs = $('#entry .form-control').map(function( i, e ) {
                    return $( e ).attr('data-itemID');
                  }).get();

      var triggers = $('input[name="triggers[]"]:checkbox:checked').map(function() {
                  return this.value;
              }).get();


      var _token = "{{ csrf_token() }}";

      
      //console.log(triggers);

      if (items[0] === "" || items[1] === "")
        $.notify("All fields are required.",{className:"error", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );
      else
      {
        $.ajax({

                  url:"{{action('EngagementController@saveEntry')}}",
                  type:'POST',
                  data:{

                    'engagement_id': "{{$id}}",
                    'items': items,
                    'itemIDs': itemIDs,
                    'triggers':triggers,
                    
                    _token: _token

                  },
                  error: function(response)
                  { console.log("Error saving entry: ");
                    console.log(response);
                    $.notify("Error processing request. Please check all submitted fields and try again.",{className:"error", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} ); 
                    return false;
                  },
                  success: function(response)
                  {
                    console.log(response);
                    $.notify("Entry submitted. \nThank you for participating.",{className:"success", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );
                    $('#submit').fadeOut();

                  }

            });

      }
      

      /**/
      });

   @endif

   

  

  
       
        
      
      
   });

   

</script>
<!-- end Page script -->



@stop