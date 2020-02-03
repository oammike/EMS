@extends('layouts.main')

@section('metatags')
  <title>Barista | Open Access BPO Rewards</title>
  <style type="text/css">
    
  .ui-autocomplete-loading {
    background: white url("images/ui-anim_basic_16x16.gif") right center no-repeat;
  }

  </style>

  <link href="./public/css/easy-autocomplete.min.css" rel="stylesheet" type="text/css">
  <link href="./public/css/bootstrap-grid.min.css" rel="stylesheet" type="text/css">
  <link href="./public/css/bootstrap-reboot.min.css" rel="stylesheet" type="text/css">
  <link href="./public/css/bootstrap.css" rel="stylesheet" type="text/css">
@stop


@section('content')


  <section class="content-header">
    <h1><i class="fa fa-gift"></i> Open Access BPO Rewards <small id="points_counter">Remaining Points: {{ $remaining_points }}</small></h1>

    <ol class="breadcrumb">
      <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Rewards</li>
    </ol>
  </section>

  <section class="content" id='holder'>
    <div class="row">
      <div class="col-xs-12">
        <div class="box"  style="background:url('../storage/uploads/Coffee_making_grp.jpg')center center no-repeat rgba(256, 256, 256, 0.4);background-size: cover; min-height: 1200px;padding:50px">
          <div class="box-heading"><h3 class="text-yellow"> Total Orders for: <strong>{{$today}} </strong></h3></div>
          <div class="box-body">

            <div class="row">
              <div class="col-lg-6">
                <table class="table table-bordered" style="color:#fff">
                  <tr>
                    <th>Item</th>
                    <th>Orders</th>
                  </tr>

                  @foreach($allItems as $i)
                  <tr>
                    <th>{{$i->name}}</th>
                    <?php $c = collect($allOrders)->where('reward_id',$i->id); ?>
                    <th>{{count($c)}} </th>
                  </tr>
                  @endforeach

                  <tr class="text-yellow">
                    <td style="font-size: x-large;">TOTAL</td>
                    <td style="font-size: x-large;"><strong>{{count($allOrders)}} </strong></td>
                  </tr>
                  
                </table>
              </div>

              <div class="col-lg-6">
                <h1 id="status" class="text-center" style="color:#fff; font-size: 5em;">{{$shop->status}}</h1>
                <br><br/>
                <div class="text-center">
                  <label class="text-yellow"><input type="radio" name="status" value="OPEN" @if($shop->status == "OPEN") checked="checked" @endif /> OPEN</label>&nbsp;&nbsp;
                  <label class="text-yellow"><input type="radio" name="status" value="ON_BREAK" @if($shop->status == "ON_BREAK") checked="checked" @endif /> ON BREAK</label>&nbsp;&nbsp;
                  <label class="text-yellow"><input type="radio" name="status" value="CLOSED" @if($shop->status == "CLOSED") checked="checked" @endif /> CLOSED</label>
                </div>
              </div>
            </div>
            
            
           
          </div>
        </div>
      </div>    
    </div>

		</div>
  </section>    
	

	
@stop

@section('footer-scripts')



	<script>
		window.selected_reward_id = 0;
		$(function() {
  

      $('input[type="radio"]').on('click',function(){

        var stat = $(this).val();
        var _token = "{{ csrf_token() }}";

        
        console.log(stat);



        $.ajax({
            type:"POST",
            url : "{{ action('UserController@rewards_coffeeshop') }}",
            data : {
                      
                      'status' : stat,
                      '_token' : _token

            },
            success : function(data){
                                      console.log(data);

                                      if (data.success == '1')
                                      {
                                        $('#status').html("");
                                        $("#status").html(stat);

                                        $.notify("Coffee shop is now: "+stat,{className:"success", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );
                                        

                                      }else {

                                        $.notify("An error occured. Please try again.",{className:"error", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );

                                      }
                                      

                                      
            },
            error: function(data){
              
                                      $.notify("An error occured. Please try again.",{ className:"error", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );
              
            }
          });


      });



			
		});
	</script>
@stop
