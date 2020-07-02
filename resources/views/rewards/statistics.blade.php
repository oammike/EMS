@extends('layouts.main')

@section('metatags')
  <title>Coffee Shop Stats | Open Access BPO Rewards</title>
  <style type="text/css">
    
  .ui-autocomplete-loading {
    background: white url("images/ui-anim_basic_16x16.gif") right center no-repeat;
  }

  </style>

  <link href="./public/css/bootstrap-grid.min.css" rel="stylesheet" type="text/css">
  <link href="./public/css/bootstrap-reboot.min.css" rel="stylesheet" type="text/css">
  <link href="./public/css/bootstrap.css" rel="stylesheet" type="text/css">
@stop


@section('content')


  <section class="content-header">
    <h1><i class="fa fa-gift"></i> Coffee Shop Stats <small id="points_counter">Orders Today: </small></h1>

    <ol class="breadcrumb">
      <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Rewards</li>
    </ol>
  </section>

  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-xs-8">
          <div class="box">
              <div class="box-header">
                <h3 class="box-title" id="table_title">Orders Today</h3>

                <div class="box-tools">
                  <div class="input-group input-group-sm hidden-xs" style="width: 150px;">
                    

                    <div class="input-group-addon">
                      <i class="fa fa-calendar"></i>
                    </div>
                    <input type="text" class="form-control pull-right" id="reservation">


                  </div>
                </div>
              </div>
              <!-- /.box-header -->
              <div class="box-body table-responsive no-padding">
                <div class="overlay" id="table_loader">
                  <i class="fa fa-refresh fa-spin"></i>
                </div>
                <table id="table" class="table table-hover">
                  <thead>
                    <tr>
                      <th>Campaign</th>
                      <th>Employee</th>
                      <th>Reward Name</th>
                      <th>Time</th>
                    </tr>
                  </thead>
                  <tbody id="table_contents">
                  @php
                    $previous_index = 0;
                    $displayed = 0;
                  @endphp
                    @forelse($todays_orders as $key=>$order)
                      @if ($previous_index != $order->id)
                        @php
                          $previous_index = $order->id;
                          $displayed = $displayed + 1;
                        @endphp  
                        <tr>
                          <td>{{ $order->campaign_name }}</td>
                          <td>{{ $order->first }} {{ $order->last }}</td>
                          <td>{{ $order->reward_name }}</td>
                          <td>{{ \Carbon\Carbon::parse($order->order_date)->format('h:i:s A') }}</td>
                        </tr>
                      @endif
                    @empty                
                      <tr>
                        <td colspan="4">No orders yet.</td>
                      </tr>
                    @endforelse
                  </tbody>
                </table>
              </div><!-- /.box-body -->
            </div><!-- /.box -->
        </div><!-- /.col -->

        <div class="col-xs-4">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title" id="count_title">Order Tally</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body table-responsive no-padding">
              <table id="count_table" class="table table-hover">
                  <thead>
                    <tr>
                      <th>Coffee</th>
                      <th>Order Count</th>
                    </tr>
                  </thead>
                  <tbody id="count_contents">
                    @forelse($count as $key=>$order)
                      <tr>
                        <td>{{ $key }}</td>
                        <td>{{ $order }}</td>
                      </tr>
                    @empty                
                      <tr>
                        <td colspan="2">No orders yet.</td>
                      </tr>
                    @endforelse
                  </tbody>
              </table>
            </div>
          </div>

        </div><!-- /.box -->

      </div><!-- /.row -->
    </div>
  </section>
	

	
@stop

@section('footer-scripts')
	<script>		
    window.displayed = {{ $displayed }};
		$(function() {
      $('#points_counter').text("Orders Today: "+ window.displayed );

      $('#table_loader').hide();
      $('#reservation').daterangepicker(
        {
          endDate: '{{ \Carbon\Carbon::now()->format('m/d/Y') }}',
          maxDate: '{{ \Carbon\Carbon::now()->format('m/d/Y') }}',
          ranges: {
           'Today': [moment(), moment()],
           'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
           'Last 7 Days': [moment().subtract(6, 'days'), moment()],
           'Last 30 Days': [moment().subtract(29, 'days'), moment()],
           'This Month': [moment().startOf('month'), moment().endOf('month')],
           'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
          }
        },
        function(start, end, label) {
          $('#table').hide();
          $('#table_loader').show();

          $.ajax({
            type: "GET",
            url : "{{ url('/coffeeshop-stats') }}/"+start.format('X')+"/"+end.format('X'),
            success : function(data){
              $('#table_contents').empty();
              var displayed = 0;
              
              var current_index = 0;
              data.orders.forEach(function(order,index){
                
                if(current_index!=order.id){
                  var time = moment(order.order_date);
                  $("<tr><td>"+order.campaign_name+"</td><td>"+order.first+" "+order.last+"</td><td>"+order.reward_name+"</td><td>"+time.format('MM/DD h:mm:ss A')+"</td></tr>").appendTo('#table_contents');
                  current_index = order.id;
                  displayed = displayed + 1;
                }
              });
              $('#table').show();
              $('#table_loader').hide();
              $('#points_counter').text("Orders for this period: "+displayed);
              $('#table_title').text("Orders for " + start.format('MMM DD') + " to " + end.format('MMM DD') );


              $('#count_contents').empty();
              for (const [key, value] of Object.entries(data.count)) {
                $("<tr><td>"+key+"</td><td>"+value+"</td></tr>").appendTo('#count_contents');                                
              };

            }
          });
        }
      );
			
		});
	</script>
@stop
