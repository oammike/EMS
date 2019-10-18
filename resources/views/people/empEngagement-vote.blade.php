@extends('layouts.main')

@section('metatags')
<title>All Entries: {{$allEntries[0]->activity}}| EMS</title>
@endsection

@section('content')


  <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>&nbsp;</h1>
      <ol class="breadcrumb">
        <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="{{action('EngagementController@show',$id)}}"> Employee Engagement Activities</a></li>
        <li class="active">{{$allEntries[0]->activity}}</li>
      </ol>
    </section>

     <section class="content">
      <!-- ******** THE DATATABLE ********** -->
          <div class="row">
             <div class="col-lg-12">

              <div class="box box-primary"  style="background: rgba(256, 256, 256, 0.4);padding:30px">

                <h3 class="text-default"><span style="font-weight: 100"> All Entries:</span> <a href="{{action('EngagementController@show',$id)}}"> {{$allEntries[0]->activity}}</a><br/><br/></h3> 

                <?php $ctr=1;?>

                @foreach($userEntries as $entry)

               

                <div style="width: 45%;margin-right:20px" class="pull-left">
                  <!-- ******** collapsible box ********** -->
                  <div class="box box-default collapsed-box" style="background-size:98%;background-position: bottom center; background-repeat: no-repeat; background-image: url('../../storage/uploads/frightful2.jpg'); background-color: #000" >
                    <div class="box-header with-border">

                      
                      <h3 class="box-title" style="color:#dedede">{{$entry[0]->value}} </h3>

                      <?php $trigger = collect($triggers)->where('entryID',$entry[0]->entryID)->all(); ?>

                      @if(count($trigger) > 0) 
                        <div class="text-left" style="border-top: 1px dotted #666;margin-top: 5px">
                          <i class="fa fa-exclamation-triangle text-yellow"></i>
                          <?php foreach($trigger as $t) { ?>

                          <strong style="font-size: smaller;" class="text-danger">{{$t->trigger}}, </strong>

                         <?php } ?> 
                        </div>
                     @endif


                      @if ( $alreadyVoted && $voted[0]->engagement_entryID == $entry[0]->entryID )
                      &nbsp;&nbsp;<i class="fa fa-check text-success"></i>
                      @endif



                      <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                        </button>
                      </div>
                      <!-- /.box-tools -->
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                      @foreach($entry as $e)

                      

                        @if($e->elemType === 'PAR')
                             <div style="margin:20px; white-space: pre-wrap; color:#fff">{!! $e->value !!}</div>
                        @else
                           <h4 style="color:#fff"> <span style="font-weight: 100"></span></h4> 

                        @endif

                        

                      @endforeach
                      <div class="clearfix"></div>

                      @if( $alreadyVoted == 0 && $entry[0]->withVoting && ($entry[0]->disqualified !== 1) )
                      <a class="vote btn btn-lg btn-primary" data-toggle="modal" data-target="#myModal{{$entry[0]->entryID}}"><i class="fa fa-check"></i> Vote for this entry</a>
                      @endif

                      @if ( $alreadyVoted && $voted[0]->engagement_entryID == $entry[0]->entryID && $entry[0]->withVoting )

                      <h4 style="width: 40%" class="pull-left text-success text-center"><i class="fa fa-check fa-2x"></i> You Voted for this<br/>
                        <br/><a class="btn btn-xs btn-default" data-toggle="modal" data-target="#delModal{{$entry[0]->entryID}}"><i class="fa fa-times"></i> Cancel Vote</a></h4>


                        <div class="modal fade" id="delModal{{$entry[0]->entryID}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                          <div class="modal-dialog">
                            <div class="modal-content">
                              <div class="modal-header">
                                
                                  <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                  <h4 class="modal-title" id="myModalLabel">Cancel Vote </h4>
                                
                              </div>
                              <div class="modal-body">
                                Are you sure you want to cancel your vote for this entry by: <strong>{{$entry[0]->firstname}} {{$entry[0]->lastname}}</strong>?
                              </div>
                              <div class="modal-footer no-border">
                                {{ Form::open(['route' => ['employeeEngagement.uncastvote',$entry[0]->entryID], 'method'=>'POST','class'=>'btn-outline pull-right', 'id'=> $entry[0]->entryID ]) }} 

                                  <button type="submit" class="btn btn-primary"><i class="fa fa-trash"></i> Yes </button>
                                
                                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>{{ Form::close() }}
                              </div>
                            </div>
                          </div>
                        </div>


                      @endif


                      
                      <p class="pull-right" style="width: 50%; color:#dedede">
                          
                          <a href="{{action('UserController@show',$e->user_id)}}" target="_blank"><img class="user-image pull-right" width="80" src="../../public/img/employees/{{$e->user_id}}.jpg">
                          @if(empty($e->nickname))
                            <strong>{{$e->firstname}} {{$e->lastname}}<br/></strong>
                          
                          @else
                            <strong>{{$e->nickname}} {{$e->lastname}}<br/></strong>
                          @endif
                        </a>
                          
                          <small style="color:#dedede">{{$e->jobTitle}}</small> - {{$e->program}}
                      </p>

                      
                    </div>
                    <!-- /.box-body -->
                  </div>
                  <!-- ******** end collapsible box ********** -->
                </div>
                @if ( $ctr % 2  == 0 )
                <div class="clearfix">&nbsp;</div>
                @endif
                <?php $ctr++; ?>


                @if($alreadyVoted == 0 && $entry[0]->withVoting)
                <div class="modal fade" id="myModal{{$entry[0]->entryID}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                      <div class="modal-content">
                        <div class="modal-header">
                          
                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                            <h4 class="modal-title" id="myModalLabel">Vote for this entry</h4>
                          
                        </div>
                        <div class="modal-body">
                          Are you sure you want to vote for this entry by: <strong>{{$entry[0]->firstname}} {{$entry[0]->lastname}}</strong>?
                        </div>
                        <div class="modal-footer no-border">
                          {{ Form::open(['route' => ['employeeEngagement.castvote',$entry[0]->entryID], 'method'=>'POST','class'=>'btn-outline pull-right', 'id'=> $entry[0]->entryID ]) }} 

                            <button type="submit" class="btn btn-primary">Yes</button>
                          
                          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>{{ Form::close() }}
                        </div>
                      </div>
                    </div>
                  </div>
                  @endif



                @endforeach

                <div class="clearfix">&nbsp;</div>
                





              </div><!--end box-primary-->



              
              <!--COMMENTS -->
              <div class="box box-primary"  style="background: rgba(256, 256, 256, 0.6);padding:30px; max-height: 500px; overflow-y: scroll;">
                <h2>Join the Discussion</h2>
                <p>Share your thoughts and post your comments</p><br/><br/>

                <input type="text" class="form-control input-md pull-left" name="comment" id="comment" placeholder="Post your comment" style="width: 83%" />
                <a id="submitComment"  style="margin-left: 5px" class="btn btn-sm btn-primary pull-left"> Post</a>

                <div id="new">
                </div>

                    @foreach($comments as $comment)
                        <!-- Message -->
                        <div class="direct-chat-msg">

                          <div class="direct-chat-info clearfix"></div>

                          <a href="{{action('UserController@show',$comment->userID)}}" target="_blank"><img class="direct-chat-img" style="width: 60px; height: 60px; margin-left: -10px"  src="../../public/img/employees/{{$comment->userID}}.jpg" alt="message user image"></a>
                          
                          <div class="direct-chat-text" style="padding:20px;background: rgba(256, 256, 256, 0.9); width: 78%" >
                            
                            {!! $comment->body !!}

                              <div class="buttons" style="position: absolute; top:0px; right:-130px; width: 15%; margin-left: 50px">

                                <!-- ******* DELETE COMMENT ********-->
                                @if($owner->id == $comment->userID)
                                <a data-toggle="modal" data-target="#delComment{{$comment->id}}" data-commentID="{{$comment->id}}" class="deleteComment pull-left btn btn-xs btn-default" style="margin-left: 5px;margin-bottom: 5px;">
                                  <i class="fa fa-trash"></i> Delete  </a><div class="clearfix"></div>
                                @endif


                                <!-- ******* LIKE COMMENT ********-->
                                <?php $likes = collect($commentLikes)->where('commentID',$comment->id)->all(); ?>
                                <?php $mylike = collect($likes)->pluck('userID')->toArray(); ?>

                                @if (in_array($owner->id,$mylike))

                                    <a data-commentID="{{$comment->id}}" data-type="comment" class="unlike pull-left btn btn-xs" style="background-color: #fff; margin-left: 5px;margin-bottom: 5px;">
                                      <i class="fa fa-thumbs-o-up"></i> Liked 
                                      <strong style="font-size: x-small;">
                                        
                                        @if(count($likes) > 0)
                                            ( {{count($likes)}} )
                                        @endif
                                     </strong>
                                   </a>

                                @else

                                  <a  data-commentID="{{$comment->id}}" data-type="comment" class="like pull-left btn btn-xs btn-default" style="margin-left: 5px;margin-bottom: 5px;">
                                      <i class="fa fa-thumbs-o-up"></i> Like 
                                      <strong style="font-size: x-small;">
                                        
                                        @if(count($likes) > 0)
                                            ( {{count($likes)}} )
                                        @endif
                                     </strong>
                                   </a>


                                @endif

                                 <div class="clearfix"></div>

                                <!-- ******* REPLY BTN ********-->
                                <a class="reply pull-left btn btn-xs btn-default" data-commentID="{{$comment->id}}" style="margin-left: 5px;margin-bottom: 5px;"><i class="fa fa-reply"></i> Reply</a><div class="clearfix"></div>
                                
                                

                              </div>

                              <span class="pull-right" style="font-size: x-small;background-color: #dedede; padding:3px;">
                                <?php //echo Carbon\Carbon::now('GMT+8')->diffForHumans($comment->created_at,true) ?>
                                {{ date('M d, Y h:m A',strtotime($comment->created_at) ) }} </span>
                          </div>


                          <!-- ********** modal DELETE COMMENT -->
                           <div class="modal fade" id="delComment{{$comment->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                              <div class="modal-dialog">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    
                                      <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                      <h4 class="modal-title" id="myModalLabel">Delete Comment </h4>
                                    
                                  </div>
                                  <div class="modal-body">
                                    Are you sure you want to delete your comment?
                                  </div>
                                  <div class="modal-footer no-border">
                                    {{ Form::open(['route' => ['employeeEngagement.deleteComment',$comment->id], 'method'=>'POST','class'=>'btn-outline pull-right', 'id'=> $comment->id ]) }} 

                                      <button type="submit" class="btn btn-primary"><i class="fa fa-trash"></i> Yes </button>
                                    
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>{{ Form::close() }}
                                  </div>
                                </div>
                              </div>
                            </div>
                            <!-- ********** modal DELETE COMMENT -->

                          <a href="{{action('UserController@show',$comment->userID)}}" target="_blank">
                            <span class="direct-chat-name pull-left"  style="line-height: 0.8em;padding-top: 5px">
                            @if (is_null($comment->nickname))
                              {{$comment->firstname}} {{$comment->lastname}}<br/>
                            @else
                              {{$comment->nickname}} {{$comment->lastname}}<br/>
                            @endif
                            <em style="font-size: smaller"><span style="font-weight: normal;">{{$comment->jobTitle}}</span> - {{$comment->program}} </em>
                          </span></a>
                          

                          <div class="replydiv" id="replyto_{{$comment->id}}">
                             <div class="clearfix"></div>
                             <input id="reply_{{$comment->id}}" type="text" class="form-control input-sm pull-left" name="reply" placeholder="Post your reply" style="margin-left:48px; margin-top:10px;width: 73%" />
                             <a id="submitReply_{{$comment->id}}" data-commentID="{{$comment->id}}"  style="margin-left: 5px; margin-top:10px;" class="submitReply btn btn-sm btn-default pull-left"> Post</a>
                          </div>


                            <!-- **** REPLIES *** -->
                            <?php $reply = collect($replies)->where('commentID',$comment->id)->all();?>
                            
                            @if( count($reply) > 0)
                              @foreach($reply as $r)

                                <div class="direct-chat-msg" style="margin-left: 100px">
                                  <div class="direct-chat-info clearfix"></div>
                                  

                                  <a href="{{action('UserController@show',$r->userID)}}" target="_blank">
                                    <img class="direct-chat-img" style="width: 60px; height: 60px; margin-left: -10px"  src="../../public/img/employees/{{$r->userID}}.jpg" alt="message user image"></a>
                                  
                                  <div class="direct-chat-text" style="padding:20px;background: rgba(256, 256, 256, 0.9); width: 75%" >
                                    <span class="pull-right" style="font-size: x-small;background-color: #dedede; padding:3px;"> 
                                      <?php //echo Carbon\Carbon::now('GMT+8')->diffForHumans($r->created_at,true) ?>
                                      {{ date('M d, Y h:m A',strtotime($r->created_at) ) }} </span>
                                    
                                    {!! $r->body !!}

                                      <div class="buttons" style="position: absolute; top:20px; right:-80px; width: 10%; margin-left: 20px">
                                        <!-- ******* DELETE REPLY ********-->
                                        @if($owner->id == $r->userID)
                                        <a  data-toggle="modal" data-target="#delReply{{$r->id}}" class="pull-left btn btn-xs btn-default" style="margin-left: 5px;margin-bottom: 5px;">
                                          <i class="fa fa-trash"></i> Delete  </a><div class="clearfix"></div>


                                        <!-- ********** modal DELETE REPLY -->
                                         <div class="modal fade" id="delReply{{$r->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                              <div class="modal-content">
                                                <div class="modal-header">
                                                  
                                                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                    <h4 class="modal-title" id="myModalLabel">Delete Reply </h4>
                                                  
                                                </div>
                                                <div class="modal-body">
                                                  Are you sure you want to delete your reply?
                                                </div>
                                                <div class="modal-footer no-border">
                                                  {{ Form::open(['route' => ['employeeEngagement.deleteReply',$r->id], 'method'=>'POST','class'=>'btn-outline pull-right', 'id'=> $r->id ]) }} 

                                                    <button type="submit" class="btn btn-primary"><i class="fa fa-trash"></i> Yes </button>
                                                  
                                                  <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>{{ Form::close() }}
                                                </div>
                                              </div>
                                            </div>
                                          </div>
                                          <!-- ********** modal DELETE COMMENT -->


                                        @endif

                                        <?php $likes = collect($replyLikes)->where('replyID',$r->id)->all(); ?>
                                       
                                        @if(count($likes) > 0)
                                              <?php $mylike = collect($likes)->pluck('userID')->toArray(); ?>
                                              
                                              @if (in_array($owner->id,$mylike))
                                              <a data-commentID="{{$r->id}}" data-type="reply" class="unlike pull-left btn btn-xs" style="background-color:#fff; margin-left: 5px;margin-bottom: 5px;">
                                                <i class="fa fa-thumbs-o-up"></i> Liked 
                                                <strong style="font-size: x-small;">( {{count($likes)}} )</strong>
                                              @else

                                              <a data-commentID="{{$r->id}}" data-type="reply" class="like pull-left btn btn-xs btn-default" style="margin-left: 5px;margin-bottom: 5px;">
                                                <i class="fa fa-thumbs-o-up"></i> Like 
                                                <strong style="font-size: x-small;">( {{count($likes)}} )</strong>
                                              @endif
                                            </a>
                                        @else

                                        <a data-commentID="{{$r->id}}" data-type="reply" class="like pull-left btn btn-xs btn-default" style="margin-left: 5px;margin-bottom: 5px;">
                                                <i class="fa fa-thumbs-o-up"></i> Like 
                                                <strong style="font-size: x-small;"></strong><div class="clearfix"></div>
                                        </a>



                                        @endif



                                        
                                      </div>


                                  </div>

                                  <a href="{{action('UserController@show',$r->userID)}}" target="_blank">
                                    <span class="direct-chat-name pull-left" style="line-height: 0.8em;padding-top: 5px">
                                    @if (is_null($r->nickname))
                                     {{$r->firstname}} {{$r->lastname}} <br/>
                                    @else
                                    {{$r->nickname}} {{$r->lastname}} <br/>
                                    @endif

                                    <em style="font-size: smaller"><span style="font-weight: normal;"> {{$r->jobTitle}}</span> - {{$r->program}} </em></span>
                                  </a>
                                  

                                 
                                </div>
                                <!-- /msg-->
                                @endforeach

                            @endif
                            

                         
                        </div>
                        <!-- /msg-->
                    @endforeach

                   
                   

                    

                    

              </div>
              <!--end comments-->
              


              
              


             

             

          </div><!--end main row-->
      </section>


          



@endsection


@section('footer-scripts')

<script>
  $.widget.bridge('uibutton', $.ui.button);
</script>



<!-- Page script -->
<script>
 
  $(function () 
  {
     'use strict';

     $('.replydiv').fadeOut();

     $('.like.pull-left.btn.btn-xs.btn-default').on('click',function()
     {
        var type = $(this).attr('data-type');
        var commentid = $(this).attr('data-commentID');
        var _token = "{{ csrf_token() }}";

        console.log(type);
        console.log(commentid);

        $.ajax({

                      url:"{{action('EngagementController@like')}}",
                      type:'POST',
                      data:{
                        'type':type,
                        'commentid': commentid,
                         _token: _token

                      },
                      error: function(response)
                      { console.log("Error saving entry: ");
                        console.log(response);
                        $.notify("Error sending like.\nThat comment must have been deleted already by the user.",{className:"error", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} ); 
                        return false;
                      },
                      success: function(response)
                      {
                        console.log(response);
                        $.notify("Comment liked. ",{className:"success", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );
                        
                        window.location.reload(true);


                      }

                });


     });


     $('.unlike.pull-left.btn.btn-xs').on('click',function()
     {
        var type = $(this).attr('data-type');
        var commentid = $(this).attr('data-commentID');
        var _token = "{{ csrf_token() }}";

        console.log(type);
        console.log(commentid);

        $.ajax({

                      url:"{{action('EngagementController@unlike')}}",
                      type:'POST',
                      data:{
                        'type':type,
                        'commentid': commentid,
                         _token: _token

                      },
                      error: function(response)
                      { console.log("Error saving entry: ");
                        console.log(response);
                        $.notify("Error unliking. That comment must have been deleted already by the user.",{className:"error", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} ); 
                        return false;
                      },
                      success: function(response)
                      {
                        console.log(response);
                        $.notify("Comment unliked. ",{className:"success", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );
                        
                        window.location.reload(true);


                      }

                });


     });

     $('.reply.pull-left.btn.btn-xs.btn-default').on('click',function()
     {

        var commentid = $(this).attr('data-commentID');
        $('#replyto_'+commentid).fadeIn();
        console.log("comment reply: "+commentid);

     });



     $('.submitReply.btn.btn-sm.btn-default.pull-left').on('click',function()
     {
        var commentid = $(this).attr('data-commentID');
        

        var comment = $('#reply_'+commentid).val();

        console.log('submit reply: '+ comment);

        if(comment.length == 0)
        {
          $.notify("Reply field is required.",{className:"error", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );
          return false;

        }else {

          var _token = "{{ csrf_token() }}";

          $.ajax({

                      url:"{{action('EngagementController@postReply',$id)}}",
                      type:'POST',
                      data:{

                        'comment': comment,
                        'comment_id':commentid,
                         _token: _token

                      },
                      error: function(response)
                      { console.log("Error saving entry: ");
                        console.log(response);
                        $.notify("Error posting reply. That comment must have been deleted already by the user.",{className:"error", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} ); 
                        return false;
                      },
                      success: function(response)
                      {
                        console.log(response);
                        $('#replyto_'+commentid).fadeOut();
                        $.notify("Reply posted. \nThank you for sharing your thoughts.",{className:"success", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );
                        
                        window.location.reload(true);


                      }

                });

          

        }

     });

     $('#submitComment').on('click',function()
     {

        var comment = $('#comment').val();

        if(comment.length == 0)
        {
          $.notify("Comment field is required.",{className:"error", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );
          return false;

        }else {

          var _token = "{{ csrf_token() }}";

          $.ajax({

                      url:"{{action('EngagementController@postComment',$id)}}",
                      type:'POST',
                      data:{

                        'comment': comment,
                         _token: _token

                      },
                      error: function(response)
                      { console.log("Error saving entry: ");
                        console.log(response);
                        $.notify("Error posting comment. Please check all submitted fields and try again.",{className:"error", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} ); 
                        return false;
                      },
                      success: function(response)
                      {
                        console.log(response);
                        $.notify("Comment posted. \nThank you for sharing your thoughts.",{className:"success", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );
                        
                        window.location.reload(true);


                      }

                });

          

        }

     });
  
      
      
   });

   

</script>
<!-- end Page script -->



@stop