@extends('layouts.app_admin')

@section('htmlheader_title')
	Orders
@endsection

@section('main-content')
		<div class="row no_margin catalog">
			@forelse($rewards as $key=>$reward)
			
				
				<div class="col-sm-5 col-md-3 product">
					<span class="product-title">{{ $reward->name }} </span>
					<span class="product-excerpt">{{ $reward->description }}</span>
					<img src="{{ url('/') }}{{ $reward->attachment_image }}" alt="" class="img-responsive center-block product-image"/>
					<div class="row claim">
						<div class="col-sm-6 col-xs-6"><span class="product-points"><img src="{{ asset('/img/points-icon.png') }}" alt=""/>{{ $reward->cost }}</span></div>
						<div class="col-sm-6 col-xs-6 bt_claimer" data-name="{{ $reward->name }}" data-reward-id="{{ $reward->id }}">
							<span class="product-claim"><i class="fa fa-check"></i> Claim</span>
						</div>
					</div>
				</div>
				
				@if ($key % 3 == 0 && $key!=0)
					</div><div class="row no_margin catalog">
				@endif
				

			
			@empty
		
				<div class="col-xs-12">
					<p>The rewards catalog is currently unavailable</p>
				</div>
		
			@endforelse
		</div>
	
<!-- Confirm Modal -->
<div class="modal fade" id="modalConfirm" tabindex="-1" role="dialog" aria-labelledby="modalConfirmLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="modalConfirmLabel">Confirm Reward Redemption</h4>
      </div>
      <div class="box modal-body">
				
						<p>Are you sure you want to claim a <span id="modalConfirmRewardName"></span>?</p>
						<p><span id="claimer_error" class="help-block"></span></p>
				
				<div class="overlay" id="claimer_loader">
					<i class="fa fa-refresh fa-spin"></i>
				</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
        <button id="modalConfirmYes" type="button" class="btn btn-primary">Yes</button>
      </div>
    </div>
  </div>
</div>
	
@endsection

@section('custom-scripts')
	<script>
		window.selected_reward_id = 0;
		$(function() {
			$('.bt_claimer').click(function(){
				var id = $(this).data('reward-id');
				var name = $(this).data('name');
				window.selected_reward_id = id;
				$('#claimer_loader').hide();
				$('#modalConfirmRewardName').text(name);
				$('#modalConfirm').modal('show');
			});
			
			$('#modalConfirmYes').click(function(event){
				event.preventDefault();
				$('#claimer_loader').show();
				$.ajax({
					type: "GET",
					url : "{{ url('/claim-reward') }}/"+window.selected_reward_id,
					success : function(data){
						console.log(data);
						$('#claimer_loader').hide();
						$('#claimer_error').text();
						$('#modalConfirm').modal('hide');
						window.selected_reward_id = 0;
						/*
						var address = 'http://192.168.4.180/cgi-bin/epos/service.cgi?devid=local_printer&timeout=60000';
						
						var builder = new epson.ePOSBuilder();
						builder.addTextAlign(builder.ALIGN_CENTER);
						builder.addText('OAM Rewards\n');
						builder.addText('Mark Lester Bambico (5051714)');
						builder.addFeed();
						builder.addTextAlign(builder.ALIGN_LEFT);
						builder.addText('Item: Death Wish Coffee (150ml)');
						builder.addTextAlign(builder.ALIGN_RIGHT);
						builder.addTextPosition(475);
						builder.addText('34');
						builder.addTextPosition(0);
						builder.addFeed();
						builder.addText('Remaining Credits:');
						builder.addTextPosition(475);
						builder.addTextAlign(builder.ALIGN_RIGHT);
						builder.addText('34');
						
						var epos = new epson.ePOSPrint(address);
						epos.onreceive = function (res) { alert(res.success); };
						epos.onerror = function (err) { alert(err.status); };
						epos.oncoveropen = function () { alert('coveropen'); };
						epos.send(builder.toString());
						*/
					},
					data: $('#assignGroupsForm').serialize(),
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
@endsection