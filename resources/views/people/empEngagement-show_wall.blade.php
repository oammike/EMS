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

 

                
                {!! $engagement[0]->content!!}


               
               <p class="text-center"> <a class="btn btn-lg btn-danger" href="{{action('EngagementController@wall',$id)}}"  target="_blank"><i class="fa fa-th-large"></i> View Wall</a></p><!-- "http://192.168.64.2/systems/testing/wall" -->

                <hr/>

                @if($canModerate)
                <div class="row">
                  <div class="col-lg-12">
                    <!-- ******** collapsible box ********** -->
                          <div class="box box-primary collapsed-box" style="margin-top: 20px">
                            <div class="box-header with-border">
                             <h3 class="text-primary">All Posts <strong class="text-orange">({{count($userEntries)}})</strong> :</h3>
                              <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                                </button>
                              </div>
                              <!-- /.box-tools -->
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">
                                <table class="table table-bordered" id="allEntries">
                                  <thead>
                                    <tr>
                                      <th>From</th>
                                      <th>Message Body</th>
                                      <th>Image</th>
                                      <th>Date Posted</th>
                                      <th>Status</th>
                                      
                                      <th class="text-center">Actions</th>
                                    </tr>
                                  </thead>

                                  <tbody>
                                     @foreach($userEntries as $e)

                                      @if(count($e) > 1)

                                        <tr>
                                          <td>{{$e[0]->lastname}},{{$e[0]->firstname}} -- <strong>{{$e[0]->program}} </strong> </td>
                                          
                                          <td>
                                            <div class="editable" style="margin:20px; white-space: pre-wrap; font-size: smaller">{!! $e[0]->value !!}

                                              </div>
                                          </td>
                                          <td><a href="../storage/uploads/{{$e[1]->value}}" target="_blank"><img src="../storage/uploads/{{$e[1]->value}}" width="120" /></td>
                                          <td>{{$e[0]->created_at}} </td>
                                          
                                          @if($e[0]->disqualified)
                                          <td><em>Flagged inappropriate</em></td>
                                          @else
                                          <td>
                                            <em>Posted</em> <br/>
                                            @if($e[0]->anonymous)
                                            <em style="font-size: x-small;">(Anonymous)</em>
                                            @else
                                            
                                            @endif

                                          </td>
                                          @endif

                                          
                                          
                                          <td class="text-center">
                                            
                                            <a class="btn btn-xs btn-default" data-toggle="modal" data-target="#adminModal{{$e[0]->entryID}}"><i class="fa fa-trash"></i> Delete </a> 
                                            <label style="margin-top: 20px"><input type="checkbox" @if($e[0]->disqualified) checked="checked" @endif class="flag" data-entryid="{{$e[0]->entryID}}"> Flag as Inappropriate</label>
                                          </td>
                                          
                                        </tr>
                                  

                                      @else

                                        <tr>
                                          <td>{{$e[0]->lastname}},{{$e[0]->firstname}} -- <strong>{{$e[0]->program}} </strong> </td>
                                          
                                          <td>
                                            <div class="editable" style="margin:20px; white-space: pre-wrap; font-size: smaller">{!! $e[0]->value !!}</div>
                                          </td>
                                          <td>None</td>
                                          <td>{{$e[0]->created_at}} </td>
                                          @if($e[0]->disqualified)
                                          <td><em>Flagged inappropriate</em></td>
                                          @else
                                          <td>
                                            <em>Posted</em> <br/>
                                            @if($e[0]->anonymous)
                                            <em style="font-size: x-small;">(Anonymous)</em>
                                            @else
                                            
                                            @endif

                                          </td>
                                          @endif

                                          <td class="text-center">
                                            
                                            <a class="btn btn-xs btn-default" data-toggle="modal" data-target="#adminModal{{$e[0]->entryID}}"><i class="fa fa-trash"></i> Delete </a> 

                                            <label style="margin-top: 20px"><input type="checkbox" @if($e[0]->disqualified) checked="checked" @endif class="flag" data-entryid="{{$e[0]->entryID}}"> Flag as Inappropriate</label>
                                          </td>
                                          
                                        </tr>



                                      @endif

                                      <div class="modal fade" id="adminModal{{$e[0]->entryID}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                          <div class="modal-dialog">
                                            <div class="modal-content">
                                              <div class="modal-header">
                                                
                                                  <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                  <h4 class="modal-title" id="myModalLabel">Delete this post</h4>
                                                
                                              </div>
                                              <div class="modal-body">
                                                Are you sure you want to delete this post for  <strong>{{$engagement[0]->activity}}</strong>?
                                              </div>
                                              <div class="modal-footer no-border">
                                                {{ Form::open(['route' => ['employeeEngagement.deletePost',$e[0]->entryID], 'method'=>'POST','class'=>'btn-outline pull-right','id'=>$e[0]->entryID ]) }} 

                                                  <button type="submit" class="del btn btn-primary"><i class="fa fa-trash"></i> Yes </button>
                                                
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>{{ Form::close() }}
                                              </div>
                                            </div>
                                          </div>
                                        </div> 

                                       

                                   

                                    @endforeach

                                    
                                  </tbody>
                                </table>
                          
                              <div class="clearfix"></div>
                            


                              
                            </div>
                            <!-- /.box-body -->
                          </div>
                         <!-- ******** end collapsible box ********** -->
                  </div>
                </div>
                @endif
                <div class="row">
                  
                    @if ($hasEntry)
                      <div id="entry" class="col-sm-12" style="background: rgba(256, 256, 256, 0.7);padding:30px" >
                        


                        

                        <!-- ******** collapsible box ********** -->
                          <div class="box box-primary collapsed-box" style="margin-top: 20px">
                            <div class="box-header with-border">
                             <h3 class="text-primary">Your Posted Messages <strong class="text-orange">({{count($allPosts)}})</strong> :</h3>
                              <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                                </button>
                              </div>
                              <!-- /.box-tools -->
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">
                                <table class="table table-bordered">
                                  <thead>
                                    <tr>
                                      <th>Image</th>
                                      <th>Message Body</th>
                                      <th>Date Posted</th>
                                      <th>Status</th>
                                      <th>Anonymous</th>
                                      <th class="text-center">Actions</th>
                                    </tr>
                                  </thead>

                                  <tbody>
                                     @foreach($allPosts as $e)

                                      @if(count($e) > 1)

                                        <tr>
                                          <td><img src="../storage/uploads/{{$e[1]->value}}" width="120" /></td>
                                          <td>
                                            <div class="editable" style="margin:20px; white-space: pre-wrap; font-size: smaller">{!! $e[0]->value !!}

                                              </div>
                                          </td>
                                          <td>{{$e[0]->created_at}} </td>
                                          
                                          @if($e[0]->disqualified)
                                          <td><em>Flagged inappropriate</em></td>
                                          @else
                                          <td><em>Posted</em></td>
                                          @endif

                                          @if($e[0]->anonymous)
                                          <td><em>Yes</em></td>
                                          @else
                                          <td><em>No</em></td>
                                          @endif
                                          
                                          <td class="text-center">
                                            
                                            <a class="btn btn-xs btn-default" data-toggle="modal" data-target="#delModal{{$e[0]->entryID}}"><i class="fa fa-trash"></i> Delete </a> 
                                          </td>
                                          
                                        </tr>
                                  

                                      @else

                                        <tr>
                                          <td>None</td>
                                          <td>
                                            <div class="editable" style="margin:20px; white-space: pre-wrap; font-size: smaller">{!! $e[0]->value !!}</div>
                                          </td>
                                          <td>{{$e[0]->created_at}} </td>
                                          <td>status</td>
                                           @if($e[0]->anonymous)
                                          <td><em>Yes</em></td>
                                          @else
                                          <td><em>No</em></td>
                                          @endif

                                          <td class="actions">
                                            
                                            <a class="btn btn-xs btn-default pull-right" data-toggle="modal" data-target="#delModal{{$e[0]->entryID}}"><i class="fa fa-trash"></i> Delete </a> 
                                          </td>
                                          
                                        </tr>



                                      @endif

                                      <div class="modal fade" id="delModal{{$e[0]->entryID}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                          <div class="modal-dialog">
                                            <div class="modal-content">
                                              <div class="modal-header">
                                                
                                                  <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                  <h4 class="modal-title" id="myModalLabel">Delete this post</h4>
                                                
                                              </div>
                                              <div class="modal-body">
                                                Are you sure you want to delete your post for  <strong>{{$engagement[0]->activity}}</strong>?
                                              </div>
                                              <div class="modal-footer no-border">
                                                {{ Form::open(['route' => ['employeeEngagement.deletePost',$e[0]->entryID], 'method'=>'POST','class'=>'btn-outline pull-right','id'=>$e[0]->entryID ]) }} 

                                                  <button type="submit" class="del btn btn-primary"><i class="fa fa-trash"></i> Yes </button>
                                                
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>{{ Form::close() }}
                                              </div>
                                            </div>
                                          </div>
                                        </div> 

                                       

                                   

                                    @endforeach

                                    
                                  </tbody>
                                </table>
                          
                              <div class="clearfix"></div>
                            


                              
                            </div>
                            <!-- /.box-body -->
                          </div>
                         <!-- ******** end collapsible box ********** -->


                         <!-- ************* POST NEW ************** -->
                         <div style="background: rgba(183, 193, 202, 0.7);padding:30px">
                                <h3 class="text-primary">New <span class="text-danger"> Wall Post <i class="fa fa-image"></i> </span></h3>
                                <?php $ctr=0; ?>

                                      <!-- <form id="form" accept-charset="UTF-8" enctype="multipart/form-data"> -->
                                        {{ Form::open(['route' => 'employeeEngagement.saveEntry2','id'=> 'form','itemids'=>$itemIDs, 'name'=>'form','accept-charset'=>'UTF-8','enctype'=>"multipart/form-data" ]) }}

                                @foreach($engagement as $element)<br/>
                                <label style="padding-top: 20px">{{$element->label}} </label>

                                    @if( $element->dataType == 'TXT' )
                                    <input type="text" name="item_{{$element->itemID}}" data-itemID="{{$element->itemID}}" class="form-control" />

                                    @endif

                                    @if( $element->dataType == 'PAR' )
                                    <textarea name="item_{{$element->itemID}}" data-itemID="{{$element->itemID}}" class="form-control"></textarea>
                                    @endif

                                    @if( $element->dataType == 'IMG' )
                                     <p><span class="text-danger"><i class="fa fa-exclamation-circle"></i> Note: </span>Please limit your image uploads to a <strong>maximum of 1.5MB</strong> per image.<br/></p>
                                    
                                    <input type="file" name="file" id="file" accept="image/*"   class="form-control" data-itemID="{{$element->itemID}}" /><br/><br/>
                                    
                                    @endif



                                @endforeach

                                <label><input type="radio" name="anonymous" value="1"> Post this anonymously</label>&nbsp;&nbsp;&nbsp;&nbsp;
                                <label><input type="radio" name="anonymous" value="0" checked="checked"> Let them know it's me!</label><br/>
                                <input type="submit" name="upload" value="Post It! " class="btn btn-lg btn-primary" style="margin: 20px 0 30px 0;"> </input>

                                <div class="progress">
                                          <div class="progress-bar" aria-valuenow="" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                                              0%
                                          </div>
                                </div>
                                <div id="success" class="row"></div>
                                <input type="hidden" name="itemids" id="itemids" value="{{$itemIDs}}" />
                                <input type="hidden" name="engagement_id" value="{{$id}}" />
                                {{Form::close()}}
                          </div>
                          <!-- ************* POST NEW ************** -->
                        </div>

                        




                    @else
                   <div id="entry" class="col-sm-8" style="background: rgba(183, 193, 202, 0.7);padding:30px" >
                        <h3 class="text-primary">New  <span class="text-danger"> Wall Post <i class="fa fa-image"></i> </span></h3>
                        <?php $ctr=0; ?>

                              <!-- <form id="form" accept-charset="UTF-8" enctype="multipart/form-data"> -->
                                {{ Form::open(['route' => 'employeeEngagement.saveEntry2','id'=> 'form','itemids'=>$itemIDs, 'name'=>'form','accept-charset'=>'UTF-8','enctype'=>"multipart/form-data" ]) }}

                        @foreach($engagement as $element)<br/>
                        <label style="padding-top: 20px">{{$element->label}} </label>

                            @if( $element->dataType == 'TXT' )
                            <input type="text" name="item_{{$element->itemID}}" data-itemID="{{$element->itemID}}" class="form-control" />

                            @endif

                            @if( $element->dataType == 'PAR' )
                            <textarea required="required" name="item_{{$element->itemID}}" data-itemID="{{$element->itemID}}" class="form-control"></textarea>
                            @endif

                            @if( $element->dataType == 'IMG' )
                             <p><span class="text-danger"><i class="fa fa-exclamation-circle"></i> Note: </span>Please limit your image uploads to a <strong>maximum of 1.5MB</strong> per image.<br/></p>
                            
                            <input type="file" name="file" id="file" accept="image/*"   class="form-control" data-itemID="{{$element->itemID}}" /><br/><br/>
                            
                            @endif



                        @endforeach

                        <label><input type="radio" name="anonymous" value="1"> Post this anonymously</label>&nbsp;&nbsp;&nbsp;&nbsp;
                        <label><input type="radio" name="anonymous" value="0" checked="checked"> Let them know it's me!</label><br/>
                        <input type="submit" name="upload" value="Post It! " class="btn btn-lg btn-primary" style="margin: 20px 0 30px 0;"> </input>

                         <div class="progress">
                                  <div class="progress-bar" aria-valuenow="" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                                      0%
                                  </div>
                              </div>

                              <div id="success" class="row"></div>

                         
                          <input type="hidden" name="itemids" id="itemids" value="{{$itemIDs}}" />
                          
                          <input type="hidden" name="engagement_id" value="{{$id}}" />
                         {{Form::close()}}

                       </div>

                       <div class="col-sm-4 text-center">
                        <h3 class="text-default">Check out our Wall!</h3>

                       
                        <a class="btn btn-lg btn-danger" href="{{action('EngagementController@wall',$id)}}">View Wall</a>
                        
                       
                      </div>
                  

                    @endif

                    

                    
                    
                  

                  
                  
                </div>

               




              </div><!--end box-primary-->


             

             

          </div><!--end main row-->
      </section>

   

      
 

    



@endsection


@section('footer-scripts')

<script>
  $.widget.bridge('uibutton', $.ui.button);
</script>

<script type="text/javascript" src="../public/js/jquery.form.min.js"></script>

<!-- Page script -->
<script>

   $(document).ready(function(){


    $('.row').on('click','input.flag',function(){

        var d = $(this);
        var q = null;
        var _token = "{{ csrf_token() }}";

        if (d.is(":checked")) q=1; else q=0;

        console.log(q);
        $.ajax({

                  url:"{{action('EngagementController@disqualify')}}",
                  type:'POST',
                  data:{

                    'entry_id': $(this).attr('data-entryid'),
                    'q': q,
                    
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
                    if(q)
                      $.notify("Post tagged as inappropriate.",{className:"success", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );
                    else
                      $.notify("Post unflagged as inappropriate.",{className:"success", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );
                    

                  }

            });



       
    });

   
    $('#allEntries').DataTable();

    $('.del.btn').on('click',function(){
      setTimeout(function () {
        window.location="{{action('EngagementController@show',$id)}}";
  
    }, 500);

     
    });


    $('form').ajaxForm({
        beforeSend:function(){

            $('#success').empty();
            $('.progress-bar').text('0%');
            $('.progress-bar').css('width', '0%');

        },
        uploadProgress:function(event, position, total, percentComplete){
            $('.progress-bar').text(percentComplete + '0%');
            $('.progress-bar').css('width', percentComplete + '0%');
        },
        success:function(data)
        {
            if(data.success)
            {
                console.log(data);
                $('input[name="upload"]').fadeOut();
                $('#success').html('<div class="text-success text-center"><br/><a href=\"{{action("EngagementController@show",$id)}}\" class="btn btn-lg btn-success"> Create New Wall Post <i class="fa fa-image" ></i></a>&nbsp;&nbsp;<a href=\"{{action("EngagementController@wall",$id)}}\" target="_blank" class="btn btn-lg btn-danger"> View Wall  <i class="fa fa-th-large" ></i></a></div><br /><br />');
                //$('#success').append(data.image);
                $('.progress-bar').text('Message Uploaded!');
                $('.progress-bar').css('width', '100%');
               
            }
            return false;

        }
    });
});



</script>
<!-- end Page script -->



@stop