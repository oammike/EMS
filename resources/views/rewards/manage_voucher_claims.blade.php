@extends('layouts.main')

@section('metatags')
  <title>Voucher Claims</title>
@stop

@section('content')
<section class="content-header">
  <h1><i class="fa fa-gift"></i> OAM Rewards: Voucher Claims</h1>

  <ol class="breadcrumb">
    <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Home</a></li>
    <li><a href="{{ action('RewardsHomeController@rewards_catalog') }}">Rewards</a></li>
    <li class="active">Voucher Claims</li>
  </ol>
</section>

<section class="content">
  <div class="container">
    <div class="row">
      <div class="col-xs-10 col-md-12">
        <div class="box">
            
            <div class="box-body">
              <table id="rewardlist" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>Name</th>
                    <th>Voucher</th>
                    <th>Date</th>
                    <th>Options</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td colspan="4">Loading...</td>
                  </tr>
                </tbody>
              </table>
          </div><!-- /.box-body -->
        </div><!-- /.box -->
      </div><!-- /.col -->
    </div><!-- /.row -->
  </div>
</section>

  <!-- Redemption Modal -->
  <div class="modal fade" id="redeemerModal" tabindex="-1" role="dialog" aria-labelledby="modalConfirmVoucherLabel">
    <form class="form-horizontal" id="claimVoucherForm" action="{{ url('/confirm-voucher-claim/') }}">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="modalConfirmVoucherLabel">Confirm Voucher Redemption</h4>
          </div>
          <div class="box modal-body">
            <div id="voucher_form_elements">
              <div class="form-group" id="frm_grp_instructions">
                
                <div class="col-sm-12">
                  <p>Please enter any special instructions to be sent to <span id="voucher_claimant"></span>'s email, on how to redeem their <span id="voucher_name"></span> voucher.                
                  <textarea class="form-control" rows="3" placeholder="Use the following code: 3XCVB98UIT..." name="instructions" id="r_instructions"></textarea>
                  <span id="frm_grp_hint_instructions" class="help-block"></span>
                </div>
              </div>
              <div class="form-group" id="frm_grp_photo">
                
                <div class="col-sm-12">
                  <p>If the voucher requires any images, you can attach them here. (e.g. QR or barcode)
                  <input type="file" id="r_photo" name="attachment">
                  <span id="frm_grp_hint_photo" class="help-block"></span>
                </div>
              </div>

              <p><span id="voucher_error" class="help-block"></span></p>



              
              
            </div>
            <!-- <input type="hidden" name="debug" value="true" /> -->
            <div id="voucher_message_wrapper">
              <p>The voucher instructions has been sent succesfully.</p>
            </div>

            <div class="overlay" id="redeemer_loader"> 
              <i class="fa fa-refresh fa-spin"></i>
            </div>
            <div class="progress progress-xxs">
              <div id="uploader_progress" style="width: 20%" aria-valuemax="100" aria-valuemin="0" aria-valuenow="20" role="progressbar" class="progress-bar progress-bar-success progress-bar-striped">
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button id="modalConfirmVoucherClose" type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            <button id="modalConfirmVoucherYes" type="submit" class="btn btn-primary">Confirm</button>
          </div>
        </div>
      </div>
    </form>
  </div>
	
	
	
@stop

@section('footer-scripts')
<!-- page script -->
<script>
  window.selected_reward_id = 0;
  $(function () {		
    var progressbar = $('#uploader_progress');
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

		$('#rewardlist tbody').on( 'click', '.bt_redeemer', function () {
			var data = table.row( $(this).parents('tr') ).data();
			window.selected_group_row = table.row($(this).parents('tr'));
			
      progressbar.attr('aria-valuenow', 0).css('width','0%');
      $('#frm_grp_photo').removeClass('has-error');
      $('#frm_grp_instructions').removeClass('has-error');
      $('#voucher_error').hide();
      $('#voucher_message_wrapper').hide();
			$('#voucher_form_elements').show();
      $('#redeemer_loader').hide();
			$('#redeemerModal').modal('show');
			$('#voucher_claimant').text(data.user.firstname + " " +data.user.lastname);
			$('#voucher_name').text(data.voucher.name);
      $('#claimVoucherForm').trigger("reset");

      $('#modalConfirmVoucherClose').text("Cancel");
			window.selected_reward_id = data.id;
      var micro = (Date.now() % 1000) / 1000;
      $('#claimVoucherForm').ajaxForm({
        url: "{{ url('/confirm-voucher-claim') }}/"+window.selected_reward_id+"?m="+micro,
        type: "POST",
        dataType: 'json',
        error: function(data, status, xhr, $form){
            data = data.responseJSON;
            if(data.error.photo){
              $('#frm_grp_photo').addClass('has-error');
              $('#frm_grp_hint_photo').text(data.error.photo);
            }
            if(data.error.instructions){
              $('#frm_grp_instructions').addClass('has-error');               
              $('#frm_grp_hint_instructions').text(data.error.instructions);
            }
            if(data.message){
              $('#voucher_error').show();
              $('#voucher_error').text(data.message);
            }
            console.log('failed');
          
        },
        success: function(responseText, statusText, xhr, $form){
          table.ajax.reload();
          $('#modalConfirmVoucherYes').hide();
          $('#modalConfirmVoucherClose').text("OK");
          $('#redeemer_loader').hide();
          $('#voucher_error').text();
          $('#voucher_form_elements').hide();
          window.selected_reward_id = 0;
          $('#voucher_message_wrapper').show();
        },
        beforeSend: function() {
          console.log('setting submit type to POST');          
          window.submit_type = "POST";
          progressbar.attr('aria-valuenow', 0).css('width','0%');
        },
        uploadProgress: function(event, position, total, percentComplete) {
          var percentVal = percentComplete + '%';
          progressbar.attr('aria-valuenow', percentComplete).css('width',percentVal);
        },
      });
		});

		var table = $('#rewardlist').DataTable({
			"pageLength": {{ $items_per_page }},
			"paging": true,
			"pagingType": "simple_numbers",
			"lengthChange": true,
			"searching": true,
			"ordering": true,
			"info": true,
			"autoWidth": true,
			"processing": true,
			"serverSide": true,
			"ajax": "{{ url('/manage-voucher-claims/list') }}",
			"columns": [
				{
					"data" : null,
          "render": function ( data, type, full, meta ) {          
            return  data.user.firstname + " " + data.user.lastname;
          }
				},
				{
          "data": null,
          "render": function ( data, type, full, meta ) {          
            return  data.voucher.name;
          }
        },
				{
          "data": null,
          "render": function ( data, type, full, meta ) {          
            return  data.created_at;
          }
        },
				{
					"data" : null,
          "render": function ( data, type, full, meta ) {
            if(!data.redeemed){
              return "<a class='btn btn-small btn-primary bt_redeemer'><span class='fa fa-edit' aria-hidden='true'></span></a>"
            }else{
              return "INSTRUCTIONS SENT";
            }            
          }					
				}
			]
		});	
	
	/*
		$('#rewardlist').DataTable({
			"paging": true,
			"lengthChange": false,
			"searching": false,
			"ordering": true,
			"info": true,
			"autoWidth": false
		});
	*/	
		
	});
</script>
@stop