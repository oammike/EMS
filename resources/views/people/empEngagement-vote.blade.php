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


             

             

          </div><!--end main row-->
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

   
  
       
        
      
      
   });

   

</script>
<!-- end Page script -->



@stop