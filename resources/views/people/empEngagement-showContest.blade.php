@extends('layouts.main')

@section('metatags')
<title> Contest: {{$engagement[0]->activity}} | EMS</title>
@endsection

@section('content')


  <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>&nbsp;</h1>
      <ol class="breadcrumb">
        <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> HOMEEE</a></li>
        <li><a href="{{action('UserController@index')}}"> All Employees</a></li>
        <li class="active">My Team</li>
      </ol>
    </section>

     <section class="content">
      <!-- ******** THE DATATABLE ********** -->
          <div class="row">
             <div class="col-lg-12">

              <div class="box box-primary"  style="background: rgba(256, 256, 256, 0.4);padding:30px">


                 {!! $engagement[0]->content!!}








                @if( $engagement[0]->withVoting === 1 )
                <hr/>
                <div class="row">
                  <div class="col-sm-6">
                    <h3 class="text-default">View All submitted entries</h3>

                    @if ($alreadyVoted)
                    <a class="btn btn-lg btn-success" data-toggle="modal" data-target="#warning">View all entries</a>
                    @else
                    <a class="btn btn-lg btn-success" data-toggle="modal" data-target="#warning">Vote for your favorite entry</a>

                    @endif
                  </div>

                  <div id="entry" class="col-sm-6" style="background: rgba(256, 256, 256, 0.7);padding:30px" >
                    @if ($hasEntry)

                      @if( $deadline )

                        <h3 class="text-primary">Submission and editing of entries is now CLOSED.</h3>
                        <p>Thank you all for participating. Go and check out all the entries and don't forget to vote for your favorite story!</p>

                      @else

                        <a class="btn btn-xs btn-default pull-right" data-toggle="modal" data-target="#delModal{{$existingEntry[0]->entryID}}"><i class="fa fa-trash"></i> Delete </a>
                        <h3 class="text-primary" style="padding-bottom: 30px">Your Submitted Entry:</h3>

                         <h4>Triggers:</h4>

                         @if( count($myTrigger) > 0 )
                         <a id="editTrigger" class="btn btn-xs btn-default pull-right"><i class="fa fa-pencil"></i> Edit </a>
                         @else
                         <a id="editTrigger" class="btn btn-xs btn-default pull-right"><i class="fa fa-plus"></i> Add Triggers </a>
                         @endif

                         <a id="saveTrigger" class="btn btn-xs btn-success pull-right"><i class="fa fa-save"></i> Save </a>
                         <div id="triggers" style="font-size: smaller;">

                             @if (count($myTrigger)>0)
                             @foreach($myTrigger as $t)

                             {{$t->trigger}},</em>&nbsp;

                             @endforeach
                             @endif

                             <div class="clearfix"></div>

                         </div>

                        @foreach($existingEntry as $e)


                          @if($e->elemType === 'PAR')
                              <h4 style="padding-top: 20px" class="edit">{{$e->label}} :<a class="btn btn-xs btn-default pull-right" data-itemID="{{$e->itemID}}" data-itemType="{{$e->elemType}}" style="margin-right: 5px"><i class="fa fa-pencil"></i> Edit </a><div class="editable" style="margin:20px; white-space: pre-wrap; font-size: smaller">{!! $e->value !!}</div></h4>
                          @else
                               <h4 style="padding-top: 20px" class="edit">{{$e->label}} : <a class="btn btn-xs btn-default pull-right" data-itemID="{{$e->itemID}}" data-itemType="{{$e->elemType}}"   style="margin-right: 5px"><i class="fa fa-pencil"></i> Edit </a> <span style="font-weight: 100" class="editable"> {!! $e->value !!}</span></h4>

                          @endif

                        @endforeach

                        <div class="modal fade" id="delModal{{$existingEntry[0]->entryID}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                          <div class="modal-dialog">
                            <div class="modal-content">
                              <div class="modal-header">

                                  <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                  <h4 class="modal-title" id="myModalLabel">Delete my Entry</h4>

                              </div>
                              <div class="modal-body">
                                Are you sure you want to cancel your entry for  <strong>{{$engagement[0]->activity}}</strong>?
                              </div>
                              <div class="modal-footer no-border">
                                {{ Form::open(['route' => ['employeeEngagement.cancelEntry',$existingEntry[0]->entryID], 'method'=>'POST','class'=>'btn-outline pull-right', 'id'=> $existingEntry[0]->entryID ]) }}

                                  <button type="submit" class="btn btn-primary"><i class="fa fa-trash"></i> Yes </button>

                                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>{{ Form::close() }}
                              </div>
                            </div>
                          </div>
                        </div>

                      @endif





                    @else

                      @if( $deadline )

                        <h3 class="text-primary">Submission of entries is now CLOSED.</h3>
                        <p>Thank you all for participating. Go and check out all the entries and don't forget to vote for your favorite story!</p>

                      @else

                        <h3 class="text-primary">Submit Your Entry:</h3>
                        <?php $ctr=0; ?>
                        @foreach($engagement as $element)<br/>
                        <label style="padding-top: 20px">{{$element->label}} </label>

                            @if( $element->dataType == 'TXT' )
                            <input type="text" name="item_{{$element->itemID}}" data-itemID="{{$element->itemID}}" class="form-control" />

                            @endif

                            @if($ctr < 1)

                              <label><br/>Triggers:</label><br/>
                              @foreach($triggers as $trigger)
                              <label style="font-size: x-small; margin-left: 10px"><input type="checkbox" name="triggers[]" value="{{$trigger->id}}" />&nbsp; {{$trigger->name}} </label>

                              @endforeach

                              <?php $ctr++; ?>

                            @endif


                            @if( $element->dataType == 'PAR' )
                            <textarea name="item_{{$element->itemID}}" data-itemID="{{$element->itemID}}" class="form-control"></textarea>
                            @endif
                        @endforeach

                      @endif

                    @endif



                    @if ($hasEntry)

                    @else
                    @if( !$deadline )
                    <a id="submit" class="btn btn-lg btn-success pull-right" style="margin-top: 20px"> Submit</a>
                    @endif
                    @endif

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