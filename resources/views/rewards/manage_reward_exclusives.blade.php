@extends('layouts.main')

@section('metatags')
  <title>Rewards Exclusives Manager</title>
@stop

@section('content')
<section class="content-header">
    <h1><i class="fa fa-gift"></i> Manage Rewards: Exclusives </h1>

    <ol class="breadcrumb">
      <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="{{action('UserController@rewards_about')}}">Rewards</a></li>
      <li class="active">Manage Rewards Exclusives</li>
    </ol>
  </section>

	<div class="container spark-screen">
		<div class="row">
			<div class="col-xs-10 col-md-12">
				<div class="box">
						<div class="box-header">
							<div class="box-tools pull-right">
								<div class="has-feedback">
									<a id="bt_adder" class="btn btn-primary btn-xs" data-skin="skin-blue" href="#"><i class="fa fa-plus"></i> Add Exclusive Reward</a>
								</div>
							</div><!-- /.box-tools -->
						</div><!-- /.box-header -->
						<div class="box-body">
							<table id="rewardlist" class="table table-bordered table-striped">
								<thead>
									<tr>
										<th>Photo</th>
										<th>Name</th>
										<th>Description</th>
										<th>Cost</th>
                    <th>Target Campaign</th>
										<th>Option</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td colspan="6">Loading...</td>
									</tr>
								</tbody>
							</table>
					</div><!-- /.box-body -->
				</div><!-- /.box -->
			</div><!-- /.col -->
		</div><!-- /.row -->
	</div>

	<div class="modal" id="rewardManagerModal">
		<form class="form-horizontal" id="rewardManagerForm" action="{{ url('/add-rewards-exclusive') }}">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="modalTitle">New Exclusive Reward</h4>
					</div>
					<div class="modal-body">
							<div class="box-body">
								<div class="form-group" id="frm_grp_name">
									<label for="name" class="col-sm-3 control-label">Name</label>
									<div class="col-sm-9">
										<input type="text" class="form-control" id="rew_name" name="name" placeholder="Playstation 5">
										<span id="frm_grp_hint_name" class="help-block"></span>
									</div>
								</div>
								<div class="form-group" id="frm_grp_description">
									<label for="description" class="col-sm-3 control-label">Description</label>
									<div class="col-sm-9">
										<input type="text" class="form-control" id="rew_description" name="description" placeholder="Experience lightning fast loading with SSD, deeper immersion with support for haptic...">
										<span id="frm_grp_hint_description" class="help-block"></span>
									</div>
								</div>
								<div class="form-group" id="frm_grp_cost">
									<label for="cost" class="col-sm-3 control-label">Cost<br/>(required points)</label>
									<div class="col-sm-9">
										<input type="text" class="form-control" id="rew_cost" name="cost" placeholder="3000">
										<span id="frm_grp_hint_cost" class="help-block"></span>
									</div>
								</div>

								<div class="form-group" id="frm_grp_campaign">
									<label for="description" class="col-sm-3 control-label">Target Campaign</label>
									<div class="col-sm-9">
										<select class="form-control" id="rew_campaign" name="campaign_id">
											<option value="0">Select Campaign</option>
											@foreach($my_campaigns as $mCampaign)
												<option value="{{$mCampaign->id}}">{{$mCampaign->name}}</option>
											@endforeach
										</select>
										<span id="frm_grp_hint_campaign" class="help-block"></span>
									</div>
								</div>
								<div class="form-group" id="frm_grp_photo">
									<label for="description" class="col-sm-3 control-label">Picture</label>
									<div class="col-sm-9">
										<input type="file" id="rew_photo" name="photo">
										<span id="frm_grp_hint_photo" class="help-block"></span>
									</div>
								</div>

									<div class="progress progress-xxs">
										<div id="uploader_progress" style="width: 20%" aria-valuemax="100" aria-valuemin="0" aria-valuenow="20" role="progressbar" class="progress-bar progress-bar-success progress-bar-striped">
										</div>
									</div>

							</div><!-- /.box-body -->
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
						<button type="submit" class="btn btn-primary">Save</button>
					</div>
				</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</form>
	</div><!-- /.modal -->

	<div class="modal" id="rewardEditorModal">
		<form class="form-horizontal" id="rewardEditorForm" action="#">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="modalTitle">Edit Reward</h4>
					</div>
					<div class="box modal-body">
							<div class="box-body">
								<div class="form-group">
									<div class="col-sm-10">
										<span id="reward_editor_name" class="help-block"></span>
									</div>
								</div>
								<div class="form-group" id="frm_grp_description">
									<label for="reward_editor_description" class="col-sm-3 control-label">Description</label>
									<div class="col-sm-9">
										<input type="text" class="form-control" id="reward_editor_description" name="description" placeholder="Fair trade, organic, highly caffeinated, dark roast coffee.">
										<span id="frm_grp_hint_description" class="help-block"></span>
									</div>
								</div>
								<div class="form-group" id="frm_grp_cost">
									<label for="reward_editor_cost" class="col-sm-3 control-label">Cost<br/>(points required)</label>
									<div class="col-sm-9">
										<input type="text" class="form-control" id="reward_editor_cost" name="cost" placeholder="1">
										<span id="frm_grp_hint_cost" class="help-block"></span>
									</div>
								</div>
								<div class="form-group" id="frm_grp_campaign">
                  <label for="description" class="col-sm-3 control-label">Target Campaign</label>
                  <div class="col-sm-9">
                    <select class="form-control" id="reward_editor_campaign" name="campaign_id">
                      <option value="0">Select Campaign</option>
                      @foreach($my_campaigns as $mCampaign)
                        <option value="{{$mCampaign->id}}">{{$mCampaign->name}}</option>
                      @endforeach
                    </select>
                    <span id="frm_grp_hint_campaign" class="help-block"></span>
                  </div>
                </div>


							</div><!-- /.box-body -->
							<div class="overlay" id="editor_loader">
								<i class="fa fa-refresh fa-spin"></i>
							</div>
					</div>
					<div class="modal-footer">
						<input type="hidden" id="reward_editor_id" name="reward_editor_id">
						<button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
						<button type="submit" class="btn btn-primary">Save</button>
					</div>
				</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</form>
	</div><!-- /.modal -->


	<div class="modal" id="photoZoomModal">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="photoZoomTitle">Zoom View</h4>
					</div>
					<div class="modal-body">
							<div class="box-body">
                  <img id="photoZoomSrc" src="{{ url('/public/img/photo1.png') }}" class="img-responsive">

								<span id="photoZoomCaption" class="help-block"></span>
							</div><!-- /.box-body -->
					</div>
				</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->



@stop

@section('footer-scripts')
<!-- page script -->
<script>
	window.selected_editing_id = 0;
	window.submit_type = "PUT";
	window.media_directory = "{{ url('/public') }}";
	window.generate_image_column = function(data){
		console.log(data);
		return	"<img class='test' src='"+window.media_directory+"/"+data.media[0].id+"/"+data.media[0].file_name+"' width='25' height='25'>";
	}



	$(function () {
		var progressbar = $('#uploader_progress');
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});
		function clear_group_form()
		{
			$('#rewardManagerForm')[0].reset();
			progressbar.attr('aria-valuenow', 0).css('width','0%');
			reset_errors();
		}
		function reset_errors(){
			$('#frm_grp_hint_name').text('');
			$('#frm_grp_hint_description').text('');
			$('#frm_grp_hint_cost').text('');
			$('#frm_grp_hint_photo').text('');
			$('#frm_grp_name').removeClass('has-error');
			$('#frm_grp_description').removeClass('has-error');
			$('#frm_grp_cost').removeClass('has-error');
			$('#frm_grp_photo').removeClass('has-error');
		}



		$('#rewardEditorForm').submit(function( event ) {
			event.preventDefault();
			$('#editor_loader').show();
			if ($.isNumeric( $('#reward_editor_cost').val() )) {
				$.ajax({
					type: "POST",
					url : "{{ url('/edit-rewards-exclusive') }}",
					success : function(data){
						$('#editor_loader').hide();
						$('#rewardEditorModal').modal('hide');
            window.table.ajax.reload();

					},
					data: $('#rewardEditorForm').serialize(),
					error: function(data){
						$('#editor_loader').hide();
						console.log(data.responseJSON.message[0]);
						data = data.responseJSON;
						if(data.description){
							$('#frm_grp_description').addClass('has-error');
							$('#frm_grp_hint_description').text(data.description[0]);
						}
						if(data.cost){
							$('#frm_grp_cost').addClass('has-error');
							$('#frm_grp_hint_cost').text(data.cost[0]);
						}
            if(data.campaign){
              $('#frm_grp_campaign').addClass('has-error');
              $('#frm_grp_hint_campaign').text(data.campaign);
            }

					}
				});
			}	else {
				$('#editor_loader').hide();
				$('#add_error').addClass('text-red');
				$('#add_error').text('Please enter only numeric values like 10, 200, 3000...');

			}

		});

		$('#bt_adder').click(function(){
			clear_group_form();

			$('#rewardManagerForm').ajaxForm({
				type: "POST",
				dataType: 'json',
				error: function(data, status, xhr, $form){
						data = data.responseJSON;
						if(data.description){
							$('#frm_grp_description').addClass('has-error');
							$('#frm_grp_hint_description').text(data.description[0]);
						}
						if(data.name){
							$('#frm_grp_name').addClass('has-error');
							$('#frm_grp_hint_name').text(data.name[0]);
						}
						if(data.photo){
							$('#frm_grp_photo').addClass('has-error');
							$('#frm_grp_hint_photo').text(data.photo[0]);
						}
						if(data.cost){
							$('#frm_grp_cost').addClass('has-error');
							$('#frm_grp_hint_cost').text(data.cost[0]);
						}
            if(data.campaign){
							$('#frm_grp_campaign').addClass('has-error');
							$('#frm_grp_hint_campaign').text(data.campaign);
						}

						console.log('failed');

				},
				success: function(responseText, statusText, xhr, $form){
					window.table.ajax.reload();
					$('#rewardManagerModal').modal('hide');
				},
				beforeSend: function() {
					console.log('setting submit type to POST');
					reset_errors();
					window.submit_type = "POST";
					progressbar.attr('aria-valuenow', 0).css('width','0%');
				},
				uploadProgress: function(event, position, total, percentComplete) {
					var percentVal = percentComplete + '%';
					progressbar.attr('aria-valuenow', percentComplete).css('width',percentVal);
				},
			});

			$('#modalTitle').text('Add New Reward');
			$('#rewardManagerModal').modal('show');
		});

		$('#rewardlist tbody').on( 'click', '.bt_editor', function () {
			var data = window.table.row( $(this).parents('tr') ).data();
			window.selected_group_row = window.table.row($(this).parents('tr'));
			console.log(data);
			$('#editor_loader').hide();
			$('#rewardEditorModal').modal('show');
			$('#reward_editor_description').val(data.description);
			$('#reward_editor_cost').val(data.cost);
			$('#reward_editor_id').val(data.id);
			$('#reward_editor_name').text("Edit details for: "+data.name);
			$('#reward_editor_cost').text(data.cost);
			$('#reward_editor_campaign').val(data.campaign.id);

			window.selected_editing_id = data.id;
		});

		$('#rewardlist tbody').on( 'click', '.bt_deleter', function () {
			var data = window.table.row( $(this).parents('tr') ).data();
			window.selected_group_row = window.table.row($(this).parents('tr'));
			if(confirm("Are you sure you want to delete the reward: "+data.name+"?")){
				$.ajax({
					type: "DELETE",
					url : "{{ url('/manage-rewards') }}"+"/"+data.id,
					success : function(data){
						window.table.ajax.reload();
					},
					error: function(data){
						data = data.responseJSON;
						alert(data.message);
					}
				});
			}
		});

		$('#rewardlist tbody').on( 'click', '.img_zoomer', function () {
			var data = window.table.row( $(this).parents('tr') ).data();
			$('#photoZoomTitle').text('Zoom');
			$('#photoZoomCaption').text(data.name + " (" + data.cost+ " credits) "+ " - "+ data.description);
			$('#photoZoomSrc').attr('src',window.media_directory+data.attachment_image);
			$('#photoZoomModal').modal('show');
		});

		window.table = $('#rewardlist').DataTable({
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
			"ajax": "{{ url('/manage-rewards-exclusives/list') }}",
			"columns": [
				{
					"data" : null,
					//"render" : window.generate_image_column(row,type,full,meta)
					"render": function ( data, type, full, meta ) {

						return	"<img class='img_zoomer' src='"+window.media_directory+data.attachment_image+"' width='25' height='25'>";
						//generate_image_column (data)
					}
				},
				{ "data":"name" },
				{ "data":"description" },
				{ "data":"cost" },
				{
					"data":"campaign.name",
					"defaultContent":"(uncategorized)"
				},
				{
					"data" : null,
					"defaultContent" : "<a class='btn btn-small btn-primary bt_editor'><span class='fa fa-edit' aria-hidden='true'></span></a>&nbsp;<a class='btn btn-small btn-danger bt_deleter'><span class='fa fa-trash' aria-hidden='true'></span></a>"
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
