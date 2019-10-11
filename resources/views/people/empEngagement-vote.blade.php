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
                  <div class="box box-default collapsed-box">
                    <div class="box-header with-border">
                      <h3 class="box-title text-primary">{{$entry[0]->value}}  <small id=" "><i class="fa fa-exclamation-circle text-yellow"></i></small> </h3>



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
                          <h4>{{$e->label}} : </h4>   <div style="margin:20px; white-space: pre-wrap;">{!! $e->value !!}</div>
                        @else
                           <h4>{{$e->label}} :  <span style="font-weight: 100"> {!! $e->value !!}</span></h4> 

                        @endif

                        

                      @endforeach
                      <div class="clearfix"></div>
                      <p class="pull-right" style="width: 50%">
                          
                          <a href="{{action('UserController@show',$e->user_id)}}" target="_blank"><img class="user-image pull-right" width="80" src="../../public/img/employees/{{$e->user_id}}.jpg">
                          @if(empty($e->nickname))
                            <strong>{{$e->firstname}} {{$e->lastname}}<br/></strong>
                          
                          @else
                            <strong>{{$e->nickname}} {{$e->lastname}}<br/></strong>
                          @endif
                        </a>
                          
                          <small>{{$e->jobTitle}}</small> - {{$e->program}}
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