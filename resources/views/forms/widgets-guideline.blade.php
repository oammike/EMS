@extends('layouts.main')

@section('metatags')
<title>{{$program->name}} Widgets | EMS</title>
@endsection

@section('content')


  <!-- Content Header (Page header) -->
    <section class="content-header">
       <h1 class="pull-left">
        @if(is_null($logo))
          
          {{$program->name}}

        @elseif ($program->name=='IMO')
        <img src="../public/img/{{$logo[0]->filename}}" height="55" class="pull-left"  />


        @else
        <img src="./public/img/{{$logo[0]->filename}}" width="150px" class="pull-left" style="margin-top: 20px" />
        @endif
        
      </h1>
      


      <ol class="breadcrumb">
        <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="{{action('CampaignController@index')}}"> All Programs</a></li>
        <li class="active">{{$program->name}} Widgets</li>
      </ol>
    </section>

     <section class="content">
      
          <div class="row">
            <div class="col-sm-12">

                <div style="width: 10%" class="pull-left">
                  <label class="pull-left">From: </label><input type="text" id="from" class="datepicker form-control pull-left" placeholder="{{$start->format('m/d/Y')}}" value="{{$start->format('m/d/Y')}}" />
                  
                </div>
                <div style="width: 10%" class="pull-left">
                  
                  <label class="pull-left">To: </label><input type="text" id="to" class="datepicker form-control pull-left" value="{{$end->format('m/d/Y')}}" placeholder="{{$end->format('m/d/Y')}}" />
                </div>
                  

                  <a id="download" class="btn btn-success btn-sm pull-left" style="margin-top: 27px; margin-left: 5px">  
                  <?php //href="{{route('formSubmissions.rawData',['id'=>$form->id, 'from'=>$start,'to'=>$end,'page'=>$actualSubmissions->currentPage(),'dl'=>1])}}">
                  //href="formSubmissions/rawData/{{$form->id}}?from={{$start}}&to={{$end}}&page={{$actualSubmissions->currentPage()}}&dl=1"> ?>
                  <i class="fa fa-download"></i> Download Spreadsheet</a>
                </div>
          </div>

          <!-- ******** THE DATATABLE ********** -->
          <div class="row">
             <div class="col-lg-12">



                
                <div class="nav-tabs-custom" style="background: rgba(256, 256, 256, 0.1)">
                          <ul class="nav nav-tabs pull-right">  

                             <li @if($tab =='verified')class="active" @endif><a href="{{action('FormSubmissionsController@widgets',['program'=>$program->id,'tab'=>'verified','form'=>$form->id])}}">
                              <strong class="text-primary "><i class="fa fa-check"></i> All Verified  </strong></a></li>


                              <li @if($tab =='issues')class="active" @endif><a href="{{action('FormSubmissionsController@widgets',['program'=>$program->id,'tab'=>'issues','form'=>$form->id])}}">
                              <strong class="text-primary "><i class="fa fa-files-o"></i> Issues  </strong></a></li>

                              <li  @if($tab =='review')class="active" @endif><a href="{{action('FormSubmissionsController@widgets',['program'=>$program->id,'tab'=>'review','form'=>$form->id])}}">
                              <strong class="text-primary "><i class="fa fa-files-o"></i> Review  </strong></a></li>

                          </ul>
                          

                          <div class="tab-content" style="background: rgba(256, 256, 256, 0.7)">

                            <!-- **** ALL VERIFIED -->
                            <div class="tab-pane @if($tab =='verified')active @endif" id="tab_verified"> 

                              <table class="table table-hover" id="table_verified">
                                <thead>
                                  <th>Date</th>
                                  <th>Agent</th>
                                  <th>ID</th>
                                  <th>Plan Number</th>
                                  <th>Sponsor Name</th>
                                  <th>User</th>
                                  <th>Payroll Provider</th>
                                  <th>Status</th>
                                  
                                  <th>Verifier</th>
                                  <th></th>
                                        
                                 
                                </thead>
                                <tbody>
                                  @foreach($groupedSubmissions as $s)
                                  <?php $a = count($s)-1;?>
                                  @if ($s[$a]->value == "VERIFIED")
                                  <tr style="font-size: smaller;">
                                    <td style="font-size: x-small;">{{$s->first()->created_at}}</td>
                                    <td>{{$s->first()->lastname}}, {{$s->first()->firstname}}</td>
                                    <?php $c=0; ?>
                                    @foreach($s as $item)
                                      @if($c == 4)
                                      <td style="text-transform: uppercase;">{{$item->value}} </td>
                                      @else
                                      <td>{{$item->value}} </td>

                                      @endif
                                    <?php $c++; ?>
                                    @endforeach

                                     <?php $r = collect($reviewers)->where('submissionID',$s[0]->submissionID); 
                                          if (count($r) > 1) //if may stages ng review, get only verified
                                          {
                                            $v = collect($r)->where('newStatus',"VERIFIED");

                                          }else $v=null;

                                     ?>

                                      @if(count($r)==0)

                                      <td> <em>* same agent*</em> </td>

                                      @else

                                        @if( is_null($v) )
                                        <td>{{$r->first()->reviewerFname}} {{$r->first()->reviewerLname}} </td>

                                        @else
                                        <td>{{$v->first()->reviewerFname}} {{$v->first()->reviewerLname}} </td>

                                        @endif

                                      @endif
                                      <td><a class="btn btn-xs btn-default" data-toggle="modal"  data-target="#myModal_del{{$s[0]->submissionID}}" ><i class="fa fa-trash"></i>  </a> </td>

                                      

                                      @include('layouts.modals-del',[
                                        'modelType'=>"_del",
                                        'modelID'=>$s[0]->submissionID ,
                                        'modalTitle'=>"Delete",
                                        'modelName'=>"Contribution Sync Submission",
                                        'modalMessage'=> "Are you sure you want to delete this entry?",
                                        'modelRoute'=> 'formSubmissions.deleteThis', 
                                        'modelID'=> $s[0]->submissionID,
                                        'formID'=>"del".$s[0]->submissionID,
                                        'icon'=>'glyphicon-trash'
                                      ])

                                     

                                     <!--  -->
                                   
                                   
                                    
                                  </tr>
                                  @endif
                                
                               

                                  @endforeach
                                  
                                </tbody>
                              </table>
                             
                            </div>

                            <!-- **** END ALL VERIFIED -->

                            <div class="tab-pane @if($tab =='issues')active @endif" id="tab_issues"> 

                                <table class="table table-hover" id="table_issues">
                                  <thead>
                                    <th>Date</th>
                                    <th>Agent</th>
                                    <th>ID</th>
                                    <th>Plan Number</th>
                                    <th>Sponsor Name</th>
                                    <th>User</th>
                                    <th>Payroll Provider</th>
                                    <th>Status</th>
                                    <th>Reviewer</th>
                                    <th>Action</th>
                                  </thead>
                                  <tbody>
                                    @foreach($groupedSubmissions as $s)
                                     <?php $b = count($s)-1;?>
                                    @if ($s[$b]->value == "WITH ISSUE")

                                    <tr style="font-size: smaller;">
                                      <td style="font-size: x-small;">{{$s->first()->created_at}}</td>
                                      <td>{{$s->first()->lastname}}, {{$s->first()->firstname}}</td>
                                      <?php $c=0; ?>
                                      @foreach($s as $item)
                                        @if($c == 4)
                                        <td style="text-transform: uppercase;">{{$item->value}} </td>
                                        @else
                                        <td>{{$item->value}} </td>

                                        @endif
                                      <?php $c++; ?>
                                      @endforeach

                                      <?php $r = collect($reviewers)->where('submissionID',$s[0]->submissionID); ?>

                                      @if(count($r)==0)

                                      <td> <em>* same agent*</em> </td>

                                      @else

                                      <td>{{$r->first()->reviewerFname}} {{$r->first()->reviewerLname}} </td>

                                      @endif
                                      
                                     
                                      <td>
                                         <a id="btnReview" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#verify{{$s->first()->submissionID}}"><i class="fa fa-thumbs-up"></i></a> 

                                         <a class="btn btn-xs btn-default" data-toggle="modal"  data-target="#myModal_issue{{$s[0]->submissionID}}" ><i class="fa fa-trash"></i>  </a> 
                                       
                                        
                                      </td>
                                      @include('layouts.modals-del',[
                                        'modelType'=>"_issue",
                                        'modelID'=>$s[0]->submissionID ,
                                        'modalTitle'=>"Delete",
                                        'modelName'=>"Contribution Sync Submission",
                                        'modalMessage'=> "Are you sure you want to delete this entry with issue?",
                                        'modelRoute'=> 'formSubmissions.deleteThis', 
                                        'modelID'=> $s[0]->submissionID,
                                        'formID'=>"issue".$s[0]->submissionID,
                                        'icon'=>'glyphicon-trash'
                                      ])

                                      
                                    </tr>
                                    @endif

                                    <!-- ********** modal update COMMENT -->
                                     <div class="modal fade" id="verify{{$s->first()->submissionID}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                          <div class="modal-content">
                                            <div class="modal-header">
                                              
                                                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                <h4 class="modal-title" id="myModalLabel">Update Status </h4>
                                              
                                            </div>
                                            <div class="modal-body">
                                              Mark this reviewed entry (ID: {{$s->first()->value}} ) as complete and <strong class="text-success">VERIFIED</strong></span> ?
                                            </div>
                                            <div class="modal-footer no-border">
                                              {{ Form::open(['route' => ['formSubmissions.updateStatus',$s->first()->submissionID], 'method'=>'POST','class'=>'btn-outline pull-right', 'id'=> $s->first()->submissionID]) }} 
                                                <input type="hidden" name="oldStatus" value="{{$s[5]->value}}" />
                                                <input type="hidden" name="newStatus" value="VERIFIED" />
                                                <button type="submit" class="btn btn-success"><i class="fa fa-check"></i> Yes </button>
                                              
                                              <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>{{ Form::close() }}
                                            </div>
                                          </div>
                                        </div>
                                      </div>
                                      <!-- ********** modal update COMMENT -->

                                      


                                    @endforeach
                                    
                                  </tbody>
                                </table>
                             
                            </div><!--end ISSUES -->

                            <div class="tab-pane @if($tab =='review')active @endif" id="tab_review"> 

                              <table class="table table-hover" id="table_review">
                                <thead>
                                  <th>Date</th>
                                  <th>Agent</th>
                                  <th>ID</th>
                                  <th>Plan Number</th>
                                  <th>Sponsor Name</th>
                                  <th>User</th>
                                  <th>Payroll Provider</th>
                                  
                                  <th>Action</th>
                                </thead>
                                <tbody>
                                  @foreach($groupedSubmissions as $s)
                                   <?php $c2 = count($s)-1;?>
                                  @if ($s[$c2]->value == "FOR REVIEW")
                                  <tr>
                                    <td style="font-size: x-small;">{{$s->first()->created_at}}</td>
                                    <td> {{$s->first()->lastname}}, {{$s->first()->firstname}}</td>
                                    <?php $c=0; ?>
                                    @foreach($s as $item)
                                      @if($c == 4)
                                      <td style="text-transform: uppercase;">{{$item->value}} </td>
                                      @elseif($c==5)
                                      @else
                                      <td>{{$item->value}} </td>

                                      @endif
                                    <?php $c++; ?>
                                    @endforeach
                                    <td>
                                      <a id="btnReview" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#update1{{$s->first()->submissionID}}"><i class="fa fa-thumbs-up"></i></a> 
                                      <a id="btnReview" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#update2{{$s->first()->submissionID}}"><i class="fa fa-thumbs-down"></i></a> 
                                      <a class="btn btn-xs btn-default" data-toggle="modal"  data-target="#myModal_rev{{$s[0]->submissionID}}" ><i class="fa fa-trash"></i>  </a> 
                                       
                                        
                                      </td>
                                      @include('layouts.modals-del',[
                                        'modelType'=>"_rev",
                                        'modelID'=>$s[0]->submissionID ,
                                        'modalTitle'=>"Delete",
                                        'modelName'=>"Contribution Sync Submission",
                                        'modalMessage'=> "Are you sure you want to delete this entry that's up for review?",
                                        'modelRoute'=> 'formSubmissions.deleteThis', 
                                        'modelID'=> $s[0]->submissionID,
                                        'formID'=>"issue".$s[0]->submissionID,
                                        'icon'=>'glyphicon-trash'
                                      ])

                                    
                                  </tr>
                                  @endif


                                    <!-- ********** modal update COMMENT -->
                                   <div class="modal fade" id="update1{{$s->first()->submissionID}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                      <div class="modal-dialog">
                                        <div class="modal-content">
                                          <div class="modal-header">
                                            
                                              <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                              <h4 class="modal-title" id="myModalLabel">Update Status </h4>
                                            
                                          </div>
                                          <div class="modal-body">
                                            Mark this entry (ID: {{$s->first()->value}} ) as <strong class="text-success">VERIFIED</strong></span> ?
                                          </div>
                                          <div class="modal-footer no-border">
                                            {{ Form::open(['route' => ['formSubmissions.updateStatus',$s->first()->submissionID], 'method'=>'POST','class'=>'btn-outline pull-right', 'id'=> $s->first()->submissionID]) }} 
                                              <input type="hidden" name="oldStatus" value="{{$s[5]->value}}" />
                                              <input type="hidden" name="newStatus" value="VERIFIED" />
                                              <button type="submit" class="btn btn-success"><i class="fa fa-check"></i> Yes </button>
                                            
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>{{ Form::close() }}
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                    <!-- ********** modal update COMMENT -->

                                     <!-- ********** modal update COMMENT -->
                                   <div class="modal fade" id="update2{{$s->first()->submissionID}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                      <div class="modal-dialog">
                                        <div class="modal-content">
                                          <div class="modal-header">
                                            
                                              <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                              <h4 class="modal-title" id="myModalLabel">Update Status </h4>
                                            
                                          </div>
                                          <div class="modal-body">
                                            Mark this entry (ID: {{$s->first()->value}} ) as <strong class="text-danger">WITH ISSUE</strong>?
                                          </div>
                                          <div class="modal-footer no-border">
                                            {{ Form::open(['route' => ['formSubmissions.updateStatus','id'=>$s->first()->submissionID], 'method'=>'POST','class'=>'btn-outline pull-right', 'id'=> $s->first()->submissionID]) }} 
                                              <input type="hidden" name="oldStatus" value="{{$s[5]->value}}" />
                                              <input type="hidden" name="newStatus" value="WITH ISSUE" />
                                              <button type="submit" class="btn btn-danger"><i class="fa fa-check"></i> Yes </button>
                                            
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>{{ Form::close() }}
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                    <!-- ********** modal update COMMENT -->


                                  @endforeach
                                  
                                </tbody>
                              </table>
                             
                            </div><!--end REVIEW -->

                          </div>
                          <!-- /.tab-content -->
                </div>
                <!-- nav-tabs-custom -->

             </div><!--end col -->
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

    $( ".datepicker" ).datepicker();

    $('#download').on('click',function(){

      var from = $('#from').val();
      var to = $('#to').val();

      if (moment(from) <= moment(to))
        window.location = 'formSubmissions/rawData/{{$form->id}}?from='+from+'&to='+to+'&page={{$actualSubmissions->currentPage()}}&dl=1';
      else{
        $.notify("Invalid dates. Please check selected dates and try again.",{className:"error",globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} ); return false;
      }


    });

  
   
   $('#table_review,#table_issues,#table_verified').DataTable({
      "scrollX": true,
      
      "paging":true,
      "order": [[ 0, "desc" ]]
      });

  
      
   });

   

</script>
<!-- end Page script -->



@stop