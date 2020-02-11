@extends('layouts.main')

@section('metatags')
  <title>Rewards Catalog</title>
@stop


@section('content')
  <section class="content-header">
    <h1><i class="fa fa-gift"></i> Rewards Catalog <small id="points_counter">Remaining Points: 
        @if ($remaining_points > 10000)
        <em style="font-weight: bolder;">UNLIMITED</em>
        @else
        {{ $remaining_points }}
        @endif
    </small></h1>

    <ol class="breadcrumb">
      <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Rewards</li>
    </ol>
  </section>

  <section class="content" id='holder'>
    <div class="row">
      <div class="col-xs-12">
        <div class="box"  style="background: rgba(256, 256, 256, 0.4)">
          <div class="box-heading"></div>
          <div class="box-body">
            <div class="row no_margin catalog">

              

              @if($shop->status !== "OPEN")
              <h3 class="text-right" style="padding: 10px;background-color: #666; color:#fff"><i class="fa fa-coffee"></i> Coffee Drinks &nbsp;</h3> 
              <div class="box"  style="background:url('<?php echo url("/"); ?>/storage/uploads/COFFEE_prm.jpg')top center no-repeat rgba(256, 256, 256, 0.4);background-size: cover; min-height: 500px;padding:50px">
                <div class="box-heading"></div>
                <div class="box-body">
                  
                  <h4 class="text-right pull-right" style="background: rgba(256, 256, 256, 0.4);width:40%;padding: 30px;margin-top: 15%">{!! $msg !!} </h4>
                 
                </div>
              </div>

              @else
              <h3 class="text-right" style="padding: 10px;background-color: #666; color:#fff"><i class="fa fa-coffee"></i> Coffee Drinks &nbsp;</h3> 
               <div class="box"  style="background:url('<?php echo url("/"); ?>/storage/uploads/Coffee_making_grp.jpg')center center no-repeat rgba(256, 256, 256, 0.4);background-size: cover;  min-height: 50%;padding:50px">
                  
                  @forelse($rewards as $key=>$reward)
                  
                    
                    <div class="col-sm-5 col-md-3 product" style="min-height: 370px;">
                      <span class="product-title"><span style="font-size: larger;"> [{{$key + 1}}]</span> {{ $reward->name }} </span>
                      <span class="product-excerpt">{{ $reward->description }}</span>
                      <div class="product-image-container" style="background-image: url('{{ url('/') }}/public/{{ $reward->attachment_image }}');"></div>
                      
                      <div class="row claim">
                        <div class="col-sm-6 col-xs-6">
                          <span class="product-points">
                            <img src="{{ asset('/public/img/points-icon.png') }}" alt=""/>
                            <!-- {{ $reward->category->tiers->average('cost') }} -->
                            {{ $reward->cost }} 
                          </span>
                        </div>
                        <div class="col-sm-6 col-xs-6 bt_claimer" data-name="{{ $reward->name }}" data-reward-id="{{ $reward->id }}" data-category-id="{{ $reward->category->id }}">
                          <span class="product-claim"><i class="fa fa-check"></i> Claim</span>
                        </div>
                      </div>
                    </div>

                    
                    
                   <!--  @if ($key % 3 == 0 && $key!=0)
                      </div><div class="row no_margin catalog">
                    @endif -->
                    
            
                  
                  @empty
                
                    <div class="col-xs-12">
                      <p>The rewards catalog is currently unavailable</p>
                    </div>
                
                  @endforelse
                  <div class="clearfix"></div>
              </div>

              @endif
              <div class="clearfix"></div> 

              <h3 class="text-right" style="padding: 10px;background-color: #333; color:#fff"><i class="fa fa-trophy"></i> Souvenir Items &nbsp; </h3> 
              <p></p><p></p>
              <div class="box"  style="background:url('<?php echo url("/"); ?>/public/img/Merch_0.jpg')bottom right no-repeat rgba(256, 256, 256, 0.4);background-size: cover; min-height: 50%;padding:50px">

              @for($i=1;$i<=11;$i++)

              @if( $i != 3 && $i != 8)
              <div class="col-sm-5 col-md-3 product" style="min-height: 370px;">
                  <span class="product-title"><span style="font-size: larger;"> Merchandise [ {{$i}} ]</span> </span>
                  <span class="product-excerpt">Open Access merchandise {{$i}}</span>
                  <div class="product-image-container" style="background-image: url('{{ url('/') }}/public/img/Merch_{{$i}}.jpg');"></div>
                  
                  <div class="row claim">
                    <div class="col-sm-3 col-xs-6">
                      <span class="product-points">
                        <img src="{{ asset('/public/img/points-icon.png') }}" alt=""/>
                        0.00
                      </span>
                    </div>
                    <div class="col-sm-7 col-xs-6" data-name="Merch_{{$i}}" data-reward-id="merch_{{ $i }}" data-category-id="merch">
                      <span class="btn-default btn btn-sm">Available Soon! <i class="fa fa-exclamation-circle"></i> </span>
                    </div>
                  </div>
                </div>
                @endif
              @endfor
              <div class="clearfix"></div>


             
            </div>
          </div>
        </div>
      </div>    
    </div>

		</div>
  </section>    
	
<!-- Confirm Modal -->
<div class="modal fade" id="modalConfirm" tabindex="-1" role="dialog" aria-labelledby="modalConfirmLabel">
  <form class="form-horizontal" id="claimRewardForm" action="{{ url('/claim-reward/') }}">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="modalConfirmLabel">Confirm Reward Redemption</h4>
        </div>
        <div class="box modal-body">
          <div id="form_elements">
              <p>Please select a variant for your <span id="modalConfirmRewardName"></span>:</p>
              
            
            <div id="variants">
              <input type="radio" name="tier" value="small" checked> Small<br/>
              <input type="radio" name="tier" value="medium"> Medium<br/>
              <input type="radio" name="tier" value="large"> Large

            </div>

            <div id="pickuptime">
              <p><br/>Set Pickup Time</p>
              <div class="input-group">
                <input type="text" class="form-control timepicker" name="time">

                <div class="input-group-addon">
                  <i class="fa fa-clock-o"></i>
                </div>
              </div>
            </div>

            <div id="owncup_wrapper">
              <label><input type="checkbox" name="owncup" id="owncup" value="owncup"/> I'll bring my own cup</label>
              <p>Save 5 Points if you bring your own cup.<br/>Note that the barista is not required to fill your cup to the brim and will only serve 12oz.</p>

            </div>

            <p><span id="claimer_error" class="help-block"></span></p>

            
            
          </div>
          <div id="qr_code_wrapper">
            <p>Your order has been queued.</p>
            <p>Claim your reward with the barista @G2.</p>
            <p id="owncup_message">Please bring your own cup as stated in your order. Failure to do so will void this transaction and your points will NOT be refunded.</p>
            <div id="qr-code-container"></div>
          </div>
          
          <!-- <input type="hidden" name="debug" value="true" /> -->

          <div class="overlay" id="claimer_loader"> 
            <i class="fa fa-refresh fa-spin"></i>
          </div>
        </div>
        <div class="modal-footer">
          <button id="modalConfirmClose" type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <button id="modalConfirmYes" type="button" class="btn btn-primary">Claim</button>
        </div>
      </div>
    </div>
  </form>
</div>
	
@stop

@section('footer-scripts')
	<script>

		window.selected_reward_id = 0;
		$(function() {

      //Timepicker
      $('.timepicker').timepicker({
        showInputs: false,
        snapToStep: true,
        showMeridian: true,
        defaultTime: '{{ $time }}'
      })

			$('.bt_claimer').click(function(){
        $('#owncup').prop('checked',false);
      
        $('#qr_code_wrapper').hide();
        $('#qr-code-container').hide();
        $('#claimer_error').removeClass('text-red');
				$('#claimer_error').text("");
        
				$('#claimer_loader').show();
        $('#modalConfirmYes').show();
        $('#form_elements').show();
        
				var id = $(this).data('reward-id');
				var category_id = $(this).data('category-id');
				var name = $(this).data('name');
				window.selected_reward_id = id;

				$('#modalConfirmRewardName').text(name);
				$('#modalConfirm').modal('show');
        $('#modalConfirmClose').text("Cancel");
				
				$.ajax({
					type:"GET",
					url : "{{ url('/manage-categories/fetch_tiers/') }}"+"/"+category_id,
					success : function(data){
						$('#variants').empty();
              var variant_count = 0;
							$.each(data, function(k, v) {
								var tier = v;
								console.log(tier);
								
                if(~tier.description.indexOf("NOT YET AVAILABLE")){
                  $('#variants').append($('<label><input disabled="disabled" class="state iradio_square-green" type="radio" name="tier" value="'+tier.id+'">&nbsp; '+tier.description+' - '+tier.cost+' points </label><br/>'));
                } else{
                  var checked = "";
                  if(variant_count===0){
                    checked = "checked";
                  }

                  $('#variants').append($('<label><input '+checked+' class="state iradio_square-green" type="radio" name="tier" value="'+tier.id+'">&nbsp; '+tier.description+' - '+tier.cost+' points </label><br/>'));  

                  
                }

                variant_count = variant_count + 1;
							});
						
						$('#claimer_loader').hide();
					},
					error: function(data){
						alert("Could not fetch this reward's variants. Please try again later.");
						$('#modalConfirm').modal('hide');
						$('#claimer_loader').hide();
					}
				});
				
			});
      
      $(document).on('click','.order-canceller',function(){
        var id = $(this).data('order_id');
        
        var micro = (Date.now() % 1000) / 1000;
        $.ajax({
					type: "POST",
					url : "{{ url('/cancel-order') }}/"+id+"?m="+micro,
          success : function(data){
            $('#points_counter').text("Remaining Points: "+data.refund);
            $('#order_'+id).remove();
          }
        });
      });
			
			$('#modalConfirmYes').click(function(event){
				event.preventDefault();
        var micro = (Date.now() % 1000) / 1000;
				$('#claimer_loader').show();
        var data = $('#claimRewardForm').serialize();
        console.log("passing: "+data);
				$.ajax({
					type: "POST",
					url : "{{ url('/claim-reward') }}/"+window.selected_reward_id+"?m="+micro,
					success : function(data){

            if(data.owncup==="owncup"){
              $('#owncup_message').show();
            }else{
              $('#owncup_message').hide();
            }
            
            $('#modalConfirmYes').hide();
            $('#modalConfirmClose').text("OK");
						console.log(data);
						$('#claimer_loader').hide();
						$('#claimer_error').text();
            $('#form_elements').hide();
            
            //$('#qr-code-container').attr('style','background-image:url("{{ url('/') }}'+data.file+'?micro='+micro+'");');
            //$('#qr-code-container').show();
            $('#qr_code_wrapper').show();
						//$('#modalConfirm').modal('hide');
						window.selected_reward_id = 0;
            
            var appendme = $('<tr id="order_'+data.order_id+'"><td>'+data.order_id+'</td><td>'+data.label+'</td><td><button type="button" class="btn btn-block btn-danger btn-xs order-canceller" data-order_id="'+data.order_id+'">Cancel</button></td></tr>');
            $('#pending_table').append(appendme);
            
            $('#points_counter').text("Remaining Points: "+data.points);
						
					},
					data: $('#claimRewardForm').serialize(),
					error: function(data){
						$('#claimer_loader').hide();
						console.log(data.responseJSON.message);
						$('#claimer_error').addClass('text-red');
						$('#claimer_error').text(data.responseJSON.message);
						
					}
				});
			});
		});
	</script>
@stop
