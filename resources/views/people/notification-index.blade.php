@extends('layouts.main')

@section('metatags')
<title>Notifications | OAMPI Evaluation System</title>
<style type="text/css">
  .bg-warning{ background: rgba(252, 248, 277, 0.1)  }
</style>
@endsection

@section('content')


  <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Your Notifications
        
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Notifications</li>
      </ol>
    </section>

     <section class="content">
      <!-- ******** THE DATATABLE ********** -->
          <div class="row">
             <div class="col-lg-12">
              <div class="box box-primary" style="background: rgba(256, 256, 256, 0.1)">
                      <div class="box-header ">
                        
                      </div><!--end box-header-->
                      
                      <div class="box-body">

                          <div class="tab-pane" id="timeline">
                            <!-- The timeline -->
                            <ul class="timeline timeline-inverse">

                              @foreach( $allNotifs as $notif )

                              <?php $ctr = 0; $total= count($notif) ?>
                             
                                    <!-- timeline time label -->
                                    <li class="time-label" style="font-size: 13px">
                                          <span class="bg-default">
                                           {{date('M d, Y', strtotime($notif[0]['created_at'])) }}
                                          </span>
                                    </li>
                                    <!-- /.timeline-label -->


                                    <!-- timeline item -->
                                    @while ($ctr < $total)
                                    <li id="notif-{{$notif[$ctr]['id']}}">
                                      <i class="{{ $notif[$ctr]['icon']}} @if(!$notif[$ctr]['seen']) bg-blue @endif" ></i>

                                      <div class="timeline-item" style="background: rgba(256, 256, 256, 0.4)">
                                        <span class="time"><i class="fa fa-clock-o"></i> {{$notif[$ctr]['ago']}} ago</span>

                                        @if(!$notif[$ctr]['seen'])
                                        <p class="timeline-header bg-warning" style="background: rgba(255, 211, 30, 0.16)" style="font-size: 13px">
                                        @else
                                         <p class="timeline-header" style="font-size: 13px">
                                        @endif

                                          <a href="{{$notif[$ctr]['actionlink']}}" @if(!$notif[$ctr]['seen']) class="text-success" @endif>
                                          {{ $notif[$ctr]['title']}} </a> 
                                          </p>

                                         @if(!$notif[$ctr]['seen'])
                                          <div class="timeline-body" style="background: rgba(255, 211, 30, 0.16); font-size: 12px">

                                          @else
                                           <div class="timeline-body" style="font-size: 12px">
                                          @endif

                                          <a href="{{action('UserController@show',$notif[$ctr]['fromDataID'])}}">{!! $notif[$ctr]['fromImage'] !!}</a> <strong> {{$notif[$ctr]['from']}}</strong> 
                                          @if (!is_null($notif[$ctr]['position']))
                                          <small> ({{$notif[$ctr]['position']}})</small> 
                                          @endif

                                          <em> {!! $notif[$ctr]['message'] !!}</em> 
                                          <a class="delNotif btn btn-flat btn-xs btn-default pull-right" data-id="{{$notif[$ctr]['id']}}"><i class="fa fa-trash"></i> Delete</a>
                                        </div>
                                       
                                      </div>
                                    </li>
                                    <?php $ctr++; ?>
                                    @endwhile
                                    <!-- END timeline item -->

                              @endforeach

                              
                             
                              <li>
                                <i class="fa fa-clock-o bg-gray"></i>
                                @if (count($allNotifs)< 1)
                                <p align="center">You have no new notification(s). </p>

                                @endif
                              </li>
                            </ul>
                          </div>
                          <!-- /.tab-pane -->


                      </div><!--end box-body-->
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

       $('.delNotif').on('click', function(){
          var notif = $(this).attr('data-id');
          var _token = "{{ csrf_token() }}";

          console.log(notif);
          $.ajax({
                    url:"{{action('UserNotificationController@deleteNotif')}}",
                    type:'POST',
                    data:{
                      'id': notif,
                      _token:_token},
                      success: function(response)
                      {
                        console.log(response);
                        $('#notif-'+notif).fadeOut();
                        $.notify("Notification successfully deleted.",{className:"success", globalPosition:'right middle',autoHideDelay:3000, clickToHide:true} );
                        location.reload(true).delay(3000);
                        return true;
                      }
                 });

          
       });
      
      
   });

   

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