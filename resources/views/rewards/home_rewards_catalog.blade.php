@extends('layouts.main')

@section('metatags')
  <title>Rewards Catalog</title>
@stop


@section('content')
  <section class="content-header">
    <h1><i class="fa fa-picture-o"></i> Open Access Rewards </h1>

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
              @forelse($rewards as $key=>$reward)
              
                
                <div class="col-sm-5 col-md-3 product">
                  <span class="product-title">{{ $reward->name }}</span>
                  <span class="product-excerpt">{{ $reward->description }}</span>
                  <div class="product-image-container" style="background-image: url('{{ url('/') }}/public/{{ $reward->attachment_image }}');"></div>
                  
                  <div class="row claim">
                    <div class="col-sm-6 col-xs-6">
                      <span class="product-points">
                        <img src="{{ asset('/public/img/points-icon.png') }}" alt=""/>
                        {{ $reward->category->tiers->average('cost') }}
                      </span>
                    </div>
                    <div class="col-sm-6 col-xs-6 bt_claimer" data-name="{{ $reward->name }}" data-reward-id="{{ $reward->id }}" data-category-id="{{ $reward->category->id }}">
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
              <p><span id="claimer_error" class="help-block"></span></p>
            
            <div id="variants">
              <input type="radio" name="tier" value="small" checked> Small<br/>
              <input type="radio" name="tier" value="medium"> Medium<br/>
              <input type="radio" name="tier" value="large"> Large
            </div>
          </div>
          <div id="qr_code_wrapper">
            <p>Here's your order slip. Show this to the barista to claim your order.</p>
            <div id="qr-code-container"></div>
          </div>
          
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
			$('.bt_claimer').click(function(){
      
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
				$('#claimer_loader').hide();
				$('#modalConfirmRewardName').text(name);
				$('#modalConfirm').modal('show');
        $('#modalConfirmClose').text("Cancel");
				
				$.ajax({
					type:"GET",
					url : "{{ url('/manage-categories/fetch_tiers/') }}"+"/"+category_id,
					success : function(data){
						$('#variants').empty();
							$.each(data, function(k, v) {
								var tier = v;
								console.log(tier);
								
								$('#variants').append($('<input class="state iradio_square-green" type="radio" name="tier" value="'+tier.id+'"><label> '+tier.description+' - '+tier.cost+' points </label><br/>'));
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
			
			$('#modalConfirmYes').click(function(event){
				event.preventDefault();
				$('#claimer_loader').show();
				$.ajax({
					type: "POST",
					url : "{{ url('/claim-reward') }}/"+window.selected_reward_id,
					success : function(data){
            $('#modalConfirmYes').hide();
            $('#modalConfirmClose').text("OK");
						console.log(data);
						$('#claimer_loader').hide();
						$('#claimer_error').text();
            $('#form_elements').hide();
            
            $('#qr-code-container').attr('style','background-image:url("{{ url('/') }}'+data.file+'");');
            $('#qr-code-container').show();
            $('#qr_code_wrapper').show();
						//$('#modalConfirm').modal('hide');
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
