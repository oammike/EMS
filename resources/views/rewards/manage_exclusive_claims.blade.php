@extends('layouts.main')

@section('metatags')
  <title>Exclusive Claims</title>
@stop

@section('content')
<section class="content-header">
  <h1><i class="fa fa-gift"></i> OAM Rewards: Exclusive Claims</h1>

  <ol class="breadcrumb">
    <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Home</a></li>
    <li><a href="{{ action('RewardsHomeController@rewards_catalog') }}">Rewards</a></li>
    <li class="active">Exclusive Claims</li>
  </ol>
</section>

<section class="content">
  <div class="container">
    <div class="row">
      <div class="col-xs-10 col-md-12">
        <div class="box">
          <div class="box-header">
            <div class="box-tools">
              <!--  <button class="btn btn-sm btn-default" id="bt_filter_toggle">Show Redeemed</button>
              <a href="{{ url('/export-exclusive-claims') }}" target="_blank"><button class="btn btn-sm btn-default" id="bt_export"><i class="fa fa-download"></i> Export</button></a> -->
            </div>
            <br/>
          </div>

            <div class="box-body ">
              <table id="rewardlist" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>Name</th>
                    <th>Reward</th>
                    <th>Campaign</th>
                    <th>Date</th>
                    <th>Options</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td colspan="5">Loading...</td>
                  </tr>
                </tbody>
              </table>
            </div><!-- /.box-body -->
        </div><!-- /.box -->
      </div><!-- /.col -->
    </div><!-- /.row -->
  </div>
</section>


    <div class="modal" id="confirmModal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Confirm Denial</h4>
          </div>
          <div class="modal-body">
            <p>Are you sure you want to deny <span id="deny_name"></span>'s claim: <span id="deny_vname"></span>.<br/> Note: <span id="deny_points"></span> will be refunded.</p>

          </div>
          <div class="modal-footer">

            <p><span id="deny_error" class="help-block"></span></p>
            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">No</button>
            <button type="button" class="btn btn-primary" id="deny_confirm">Yes</button>
          </div>
        </div>
        <!-- /.modal-content -->
      </div>
      <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->


  <!-- Redemption Modal -->
  <div class="modal fade" id="redeemerModal" tabindex="-1" role="dialog" aria-labelledby="modalConfirmExclusiveLabel">
    <form class="form-horizontal" id="claimExclusiveForm" action="{{ url('/approve-exclusive-claim/') }}">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="modalConfirmExclusiveLabel">Approve Redemption</h4>
          </div>
          <div class="box modal-body">
            <div id="exclusive_form_elements">
              <div class="form-group" id="frm_grp_instructions">

                <div class="col-sm-12">
                  <p>Redemption Instructions:</p>
                  <textarea class="form-control" rows="3" placeholder="Visit the HR Office, then use the following redemption code: WAKANDA FOREVER." name="instructions" id="r_instructions"></textarea>
                  <span id="frm_grp_hint_instructions" class="help-block"></span>

                </div>
              </div>

              <!--
              <div class="form-group" id="frm_grp_photo">

                <div class="col-sm-12">
                  <p>If the exclusive requires any images, you can attach them here. (e.g. QR or barcode)
                  <input type="file" id="r_photo" name="attachment">
                  <span id="frm_grp_hint_photo" class="help-block"></span>
                </div>
              </div>
            -->

              <p><span id="exclusive_error" class="help-block"></span></p>





            </div>
            <!-- <input type="hidden" name="debug" value="true" /> -->
            <div id="exclusive_message_wrapper">
              <p>The exclusive reward claim has been approved.</p>
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
            <button id="modalConfirmExclusiveClose" type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            <button id="modalConfirmExclusiveYes" type="submit" class="btn btn-primary">Mark as Claimed</button>
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
  window.show_redeemed = false;
  $(function () {
    var progressbar = $('#uploader_progress');
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    /*
    $('#bt_filter_toggle').on('click',function(){
      window.show_redeemed = !window.show_redeemed;

      window.table.ajax.data(function(d){d.show_redeemed=window.show_redeemed}).reload();
      if(window.show_redeemed){
        $('#bt_filter_toggle').text("Show Un-processed Claims");
      }else{
        $('#bt_filter_toggle').text("Show Processed Claims");
      }

    });
    */


    $('#deny_confirm').on( 'click', function () {
      //$('#deny_loader').show();
      var micro = (Date.now() % 1000) / 1000;
      $.ajax({
        type: "POST",
        url: "{{ url('/deny-exclusive-claim') }}/"+window.selected_reward_id+"?m="+micro,
        success : function(data){
          window.table.ajax.reload();
          window.selected_group_row.remove();
          $('#confirmModal').modal('hide');
          //$('#deny_loader').hide();
        },
        error: function(data){
          //$('#deny_loader').hide();
          $('#deny_error').addClass('text-red');
          $('#deny_error').text(data.responseJSON.message);

        }
      });

    });

    $('#rewardlist tbody').on( 'click', '.bt_denier', function () {

      var data = window.table.row( $(this).parents('tr') ).data();
      window.selected_group_row = window.table.row($(this).parents('tr'));
      window.selected_reward_id = data.id;

      $('#deny_error').removeClass('text-red');
      $('#deny_error').text("");
      //$('#deny_loader').hide();

      $('#deny_name').text(data.user.firstname + " " + data.user.lastname);
      $('#deny_vname').text(data.reward.name);
      $('#deny_points').text(data.reward.cost);

      $('#confirmModal').modal('show');

    });

		$('#rewardlist tbody').on( 'click', '.bt_redeemer', function () {

			var data = window.table.row( $(this).parents('tr') ).data();
      console.log(data);
			window.selected_group_row = window.table.row($(this).parents('tr'));

      progressbar.attr('aria-valuenow', 0).css('width','0%');
      $('#frm_grp_instructions').removeClass('has-error');
      $('#exclusive_error').hide();
      $('#exclusive_message_wrapper').hide();
			$('#exclusive_form_elements').show();
      $('#redeemer_loader').hide();
			$('#redeemerModal').modal('show');
			$('#exclusive_claimant').text(data.user.firstname + " " +data.user.lastname);

			$('#exclusive_name').text(data.reward.name);
      $('#claimExclusiveForm').trigger("reset");

      $('#modalConfirmExclusiveClose').text("Cancel");
			window.selected_reward_id = data.id;
      var micro = (Date.now() % 1000) / 1000;
      $('#claimExclusiveForm').ajaxForm({
        url: "{{ url('/approve-exclusive-claim') }}/"+window.selected_reward_id+"?m="+micro,
        type: "POST",
        dataType: 'json',
        error: function(data, status, xhr, $form){
            data = data.responseJSON;
            if(data.error.instructions){
              $('#frm_grp_instructions').addClass('has-error');
              $('#frm_grp_hint_instructions').text(data.error.instructions);
            }
            if(data.message){
              $('#exclusive_error').show();
              $('#exclusive_error').text(data.message);
            }
            console.log('failed');

        },
        success: function(responseText, statusText, xhr, $form){
          window.table.ajax.reload();
          window.selected_group_row.remove();
          $('#modalConfirmExclusiveYes').hide();
          $('#modalConfirmExclusiveClose').text("OK");
          $('#redeemer_loader').hide();
          $('#exclusive_error').text();
          $('#exclusive_form_elements').hide();
          window.selected_reward_id = 0;
          $('#exclusive_message_wrapper').show();
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

		window.table = $('#rewardlist').DataTable({
			"pageLength": {{ $items_per_page }},
			"paging": true,
			"pagingType": "simple_numbers",
			"lengthChange": false,
			"searching": false,
			"ordering": true,
			"info": true,
			"autoWidth": true,
			"processing": true,
			"serverSide": true,
			"ajax": "{{ url('/manage-exclusive-claims/list') }}" + "/"+window.show_redeemed,
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
            return  data.reward.name;
          }
        },
        {
          "data": null,
          "render": function ( data, type, full, meta ) {
            return  data.reward.campaign.name;
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

              return "<a class='btn btn-small btn-primary bt_redeemer'><span class='fa fa-edit' aria-hidden='true'></span></a>&nbsp;&nbsp;<a class='btn btn-small btn-danger bt_denier'><span class='fa fa-thumbs-down' aria-hidden='true'></span></a>"

          }
				}
			],
      "buttons": [
          {
              "text": 'Show Redeemed',
              "action": function ( e, dt, node, config ) {
                  alert( 'Button activated' );
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