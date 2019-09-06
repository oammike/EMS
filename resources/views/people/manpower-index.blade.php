@extends('layouts.main')


@section('metatags')
  <title>All Manpower Requests | EMS </title>
    <meta name="description" content="all teams">

@stop


@section('content')




<section class="content-header"  style="margin-bottom:50px">

      <h1>
       <a href="{{action('UserController@show',$personnel->id)}} ">Manpower Request</a>
        <small>Tracker</small>
        <a class="btn btn-xs btn-danger" href="{{action('ManpowerController@create')}} "><i class="fa fa-plus"></i> Request New</a>
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
            <div class="box-body">

              <p class="text-center">Legend: 
                <i class="fa fa-2x fa-info-circle text-primary"></i> - <span class="text-primary">Pending</span> &nbsp;&nbsp;&nbsp;
                <i class="fa fa-2x fa-info-circle text-success"></i> - <span class="text-success">On Going</span> &nbsp;&nbsp;&nbsp;
                <i class="fa fa-2x fa-info-circle text-danger"></i> - <span class="text-danger">On Hold</span> &nbsp;&nbsp;&nbsp;
                <i class="fa fa-2x fa-info-circle text-gray"></i> - <span>Completed</span> &nbsp;&nbsp;&nbsp;
              </p>
                <table id="heads" class="table table-bordered table-striped">
                      <thead>
                      <tr>
                        <th class="col-xs-1">Updated</th>
                        <th class="col-xs-1">Program/Dept</th>
                        <th class="col-xs-3">Position Title</th>
                        <th class="col-xs-1 text-center">Total Needed</th>
                        <th class="col-xs-1 text-center">Current count</th>
                        <th class="col-xs-2">Reason</th>
                        
                        <th class="col-xs-1">Hiring Source <small>(Internal | External)</small></th>
                        
                        <th class="col-xs-1">Start Date </th>
                        <th class="col-xs-1">Progress </th>
                        
                       
                       

                         
                      </tr>
                      </thead>
                      <tbody>
                        @foreach($allRequests as $a)

                        <tr @if($a->progressID == 4) style="text-decoration: line-through;" @endif>
                          <td>{{ date('Y-m-d H:i:s',strtotime($a->created_at))}} </td>
                          
                          <td>
                            <h4>{{$a->program}}<br/>
                              @if(is_null($a->nickname))
                              <span style="font-size: x-small;"> [ from: {{$a->firstname}} {{$a->lastname}} ]</span>
                              @else
                              <span style="font-size: x-small;"> [ from: {{$a->nickname}} {{$a->lastname}} ]</span>
                              @endif
                            </h4>
                          </td>
                          <td>
                            @if($a->progressID == 1)
                            <strong class="text-primary"> {{$a->jobTitle}} </strong> <br /> 
                            @elseif($a->progressID == 2)
                            <strong class="text-success"> {{$a->jobTitle}} </strong> <br /> 
                            @elseif($a->progressID == 3)
                            <strong class="text-danger"> {{$a->jobTitle}} </strong> <br /> 
                            @else
                            <strong class="text-gray"> {{$a->jobTitle}} </strong> <br /> 
                            @endif
                            <span style="font-size: smaller;">{{$a->lob}}</span> <br/>
                            <ul style="font-size: smaller;"> 

                              <?php $s=collect($allStatus)->where('id',$a->status); $f = collect($foreignStatus)->where('id',$a->foreignStatus); ?> 
                              <li>
                              @if(count($s) > 0) {{$s->first()['name']}} @endif | 
                              @if(count($f) > 0) {{$f->first()->name}} @endif 
                              </li>

                              <li>{{$a->type}}</li>
                            </ul>
                          </td> 
                          <td align="center"><h4>{{$a->howMany}} </h4></td>

                          <td align="center" class="count">

                            @if($a->progressID == 1)
                            <h5 class="text-primary">
                            @elseif($a->progressID == 2)
                            <h5 class="text-success">
                            @elseif($a->progressID == 3)
                            <h5 class="text-danger">
                            @else
                            <h5 class="text-gray">

                            @endif
                            
                              <strong>{{$a->currentCount}} </strong>&nbsp;&nbsp;

                              @if($a->progressID != '4' && $canDelete) <!-- meaning not yet complete na-->
                              <a class="btn btn-xs btn-default" data-id="{{$a->id}}" ><i class="fa fa-pencil"></i></a> 
                              
                              @endif
                            </h5> 
                            <input class="form-control newct" type="text" id="newct_{{$a->id}}" data-id="{{$a->id}}" data-current="{{$a->currentCount}}" data-needed="{{$a->howMany}}" style="width: 60%" />
                          </td>


                          <td class="note">
                            {{$a->reason}} <br/>

                            @if($personnel->id == $a->userID || $canDelete)
                            <a class="btn btn-xs btn-default pull-right" data-id="{{$a->id}}" id="n_{{$a->id}}"><i class="fa fa-pencil"></i></a>
                            @endif
                            <blockquote class="{{$a->id}}" style="font-size: smaller;">{!! $a->notes !!} </blockquote>
                            <textarea class="form-control" id="text_{{$a->id}}" data-id="{{$a->id}}">{!! $a->notes !!}</textarea>
                          </td>

                         
                          <td>
                            {{$a->source}} <br/>

                            @if($a->mktgBoost)
                            <span class="text-primary" style="font-weight: bold; font-size: x-small;"><i class="fa fa-check text-primary"></i> with FB ad</span>
                            @endif
                          </td>
                          
                          <td style="font-size: smaller; font-weight: bold;">{{date("  M d 'y - D",strtotime($a->trainingStart))}} </td>
                          

                          <td align="center">

                            @if($canDelete)

                              @if($a->currentCount >= $a->howMany && $a->progressID != 4)
                              <a title="This should be marked as COMPLETE based on current headcount"><i class="fa fa-exclamation-triangle text-yellow"></i></a>
                              @endif
                            <select name="progress" class="form-control" data-id="{{$a->id}}"> 
                            @else
                            <select name="progress" class="form-control" disabled="disabled" data-id="{{$a->id}}"> 

                            @endif
                            
                              @foreach($progress as $p)
                              <option value="{{$p->id}}" @if($a->progressID == $p->id) selected="selected" @endif > {{$p->name}} </option>
                              @endforeach
                            </select>

                            @if($personnel->id == $a->userID)
                            <a data-toggle="modal" data-target="#myModal_delete{{$a->id}}" class="btn btn-xs btn-primary" style="margin-top: 10px"><i class="fa fa-times"></i> Cancel Request </a><br/>
                            @endif

                            

                            @if($a->progressID == 4 && $canDelete)
                            <a data-toggle="modal" data-target="#myModal_delete{{$a->id}}"  class="btn btn-xs btn-primary" style="margin-top: 10px"><i class="fa fa-trash"></i> Delete </a>
                            @endif
                          </td>
                         
                        </tr>

                        <div class="modal fade" id="myModal_delete{{$a->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                              <div class="modal-content">
                                <div class="modal-header">
                                  
                                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                    <h4 class="modal-title" id="myModalLabel">Delete Request </h4>
                                  
                                </div>
                                <div class="modal-body">
                                  Are you sure you want to delete this request?
                                  <blockquote>
                                    
                                    <strong class="text-primary">{{$a->jobTitle}} ({{$a->howMany}})</strong> for <strong>{{$a->program}}</strong> <br /> 
                                    <span style="font-size: smaller;">{{$a->lob}}</span> <br/>
                                    <ul style="font-size: smaller;"> 

                                      <?php $s=collect($allStatus)->where('id',$a->status); $f = collect($foreignStatus)->where('id',$a->foreignStatus); ?> 
                                      <li>
                                      @if(count($s) > 0) {{$s->first()['name']}} @endif | 
                                      @if(count($f) > 0) {{$f->first()->name}} @endif 
                                      </li>

                                      <li>{{$a->type}}</li>
                                    </ul>
                                  </blockquote>

                                </div>
                                <div class="modal-footer no-border">
                                  <a class="del btn btn-md btn-danger" id="delete_{{$a->id}}" data-id="{{$a->id}}" data-dismiss="modal"><i class="fa fa-trash"></i> Yes </a>
                                  <a class="btn btn-md btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Cancel </a>
                                  
                                 
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
    var _token = "{{ csrf_token() }}";

    $('a').tooltip().css({"cursor":"pointer"});

    $('a.del').on('click',function(){
      var val = $(this).attr('data-id');
      $.ajax({

                url:"{{action('ManpowerController@deleteRequest')}}",
                type:'POST',
                data:{
                  'id': val,
                  _token: _token

                },
                error: function(response)
                { console.log("Error saving request: ");
                  console.log(response);
                  $.notify("Error processing request.",{className:"error", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} ); 
                  return false;
                },
                success: function(response)
                {
                  console.log(response);
                  $.notify("Manpower request deleted.",{className:"success", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );
                  location.reload(true);

                }

          });

    });

    $('.count input').fadeOut();
    $('.count a').on('click', function(){
      var cid = $(this).attr('data-id');
      $('#newct_'+cid).fadeIn();
      console.log(cid);

    });



    $('.count input').focusout(function(){
      var id = $(this).attr('data-id');
      var needed = $(this).attr('data-needed');
      var current = $(this).attr('data-current');
      var val = $('#newct_'+id).val();

        if (isNaN(val) )
        {
           $.notify("Please enter a valid number \nand make sure there are no commas.",{className:"error", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );
          return false;
          

        }else{

          if(val === '' || val == null) val=current;
         
          console.log(val);
          $.ajax({

                url:"{{action('ManpowerController@updateCount')}}",
                type:'POST',
                data:{
                  'id': id,
                  'currentCount': val,
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
                  $.notify("Manpower count updated.",{className:"success", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );
                  $('#newct_'+id).fadeOut();
                  location.reload(true);

                }

          });
          
          
          

        }
    });

    $('.count input').on('keyup',function(e){
      if(e.keyCode == 13)
      {
        var id = $(this).attr('data-id');
        var val = $('#newct_'+id).val();
        var current = $(this).attr('data-current');
        var needed  = $(this).attr('data-needed');

        if (isNaN(val) )
        {
           $.notify("Please enter a valid number \nand make sure there are no commas.",{className:"error", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );
          return false;
          

        }
        else{

          if(val === '' || val == null) val=current;
         
          console.log(val);
          $.ajax({

                url:"{{action('ManpowerController@updateCount')}}",
                type:'POST',
                data:{
                  'id': id,
                  'currentCount': val,
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
                  if(val > needed )
                    $.notify("Manpower count updated. \nNote that you now exceeded number of hires from total number needed.",{className:"success", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );
                  else
                    $.notify("Manpower count updated.",{className:"success", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );
                  $('#newct_'+id).fadeOut();
                  location.reload(true);

                }

          });
          
          
          

        }
        
                  

      }
      

    });


    $('.note textarea').fadeOut();
    $('.note a').on('click',function(){
      var n = $(this).attr('data-id');
      $('textarea#text_'+n).fadeIn();
      $('blockquote.'+n).fadeOut();
      $(this).fadeOut();

    });

    $('.note textarea').focusout(function(){
      var n = $(this).attr('data-id');
      var val = $(this).val();
      var txt = $(this);

       $.ajax({

                url:"{{action('ManpowerController@updateNotes')}}",
                type:'POST',
                async:false,
                data:{
                  'id': n,
                  'notes': val,
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
                  $.notify("Manpower count updated.",{className:"success", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );
                  txt.fadeOut();
                  $('blockquote.'+n).fadeIn();
                  $('#n_'+n).fadeIn();
                  location.reload(true);

                }

          });


      

    });
      
      



    $('select[name="progress"]').on('change',function(){

      var p = $(this).attr('data-id'); //attr('data-id');
      var selval = $(this).find(':selected').val();
      
      $.ajax({

                url:"{{action('ManpowerController@updateRequest')}}",
                type:'POST',
                data:{
                  'id': p,
                  'progress': selval,
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
                  $.notify("Manpower request updated.",{className:"success", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );
                  $('#newReq').fadeIn();
                  location.reload(true);

                }

          });
      console.log(selval);

    });

    $("#heads").DataTable({
      "responsive":true,
      "scrollX":true,
       "dom": '<"col-xs-1"f><"col-xs-11 text-right"l><"clearfix">rt<"bottom"ip><"clear">',
      "order": [[ 0, "desc" ]],
      "lengthChange": true,
      //"stateSave": true,
      "oLanguage": {
         "sSearch": "<small>To REFINE or FILTER search, simply type-in</small><br> any values you want to look for:",
         "class": "pull-left"
       }
    });
    
  });
</script>
@stop