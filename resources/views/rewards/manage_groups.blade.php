@extends('layouts.app_admin')

@section('contentheader_title')
	User Manager
@endsection

@section('contentheader_description')
	Manage Groups
@endsection


@section('main-content')
	<div class="container spark-screen">
		<div class="row">
			<div class="col-xs-10 col-md-12">
				<div class="box">
						<div class="box-header">
							<div class="box-tools pull-right">
								<div class="has-feedback">
									<a id="bt_adder" class="btn btn-primary btn-xs" data-skin="skin-blue" href="#"><i class="fa fa-plus"></i> Create New Group</a>
								</div>
							</div><!-- /.box-tools -->
						</div><!-- /.box-header -->
						<div class="box-body">
							<table id="grouplist" class="table table-bordered table-striped">
								<thead>
									<tr>
										<th>Name</th>
										<th>Description</th>
										<th>Member Count</th>
										<th>Options</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>Sample</td>
										<td>This is just an example</td>
										<td>0</td>
										<td><button>EDIT</button></td>
									</tr>
								</tbody>
							</table>
					</div><!-- /.box-body -->
				</div><!-- /.box -->
			</div><!-- /.col -->
		</div><!-- /.row -->
	</div>
	
	<div class="modal" id="groupManagerModal">
		<form class="form-horizontal" id="groupManagerForm" action="#">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="modalTitle">Edit Group</h4>
					</div>
					<div class="modal-body">
							<div class="box-body">
								<div class="form-group" id="frm_grp_name">
									<label for="name" class="col-sm-2 control-label">Group Name</label>
									<div class="col-sm-10">
										<input type="text" class="form-control" id="group_name" name="name" placeholder="Avengers Assemble">
										<span id="frm_grp_hint_name" class="help-block"></span>
									</div>
								</div>
								<div class="form-group" id="frm_grp_description">
									<label for="description" class="col-sm-2 control-label">Description</label>
									<div class="col-sm-10">
										<input type="text" class="form-control" id="group_description" name="description" placeholder="When the team assembles, no villain stands a chance, as the Avengers work together to counter evildoers.">
										<span id="frm_grp_hint_description" class="help-block"></span>
									</div>
								</div>
								<input type="hidden" id="group_id" name="id" />
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
	
	
	<div class="modal" id="addPointsModal">
		<form class="form-horizontal" id="addPointsForm" action="#">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="modalTitle">Add Points</h4>
					</div>
					<div class="modal-body">
							<div class="box-body">
								<div class="row">
									<div class="col-md-12">
									
										<div class="form-group">
											<label class="col-md-4 control-label" for="name">How Much?</label>
											<div class="col-md-8">
												<input id="add_points_value" name="points" type="text" placeholder="300" class="form-control input-md">
												<span id="add_message"></span>
												<span id="add_error" class="modal_error"></span>
											</div>
										</div>
									</div>
							
									<input type="hidden" id="add_group_id" name="group_id" />
								</div><!-- /.row -->
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

@endsection

@section('custom-scripts')
<!-- page script -->
<script>
	window.undo_button_html = "<a class='btn btn-small btn-danger bt_undo'><span class='fa fa-rotate-left' aria-hidden='true'></span></a>";
	window.undo_button = null;
	window.recent_action = null;
	window.selected_group_row = null;
	window.submit_type = "PUT";
	window.options_buttons =
		"<a class='btn btn-small btn-primary bt_adder'><span class='fa fa-thumbs-o-up' aria-hidden='true'></span></a>&nbsp;" +
		"<a class='btn btn-small btn-primary bt_editor'><span class='fa fa-edit' aria-hidden='true'></span></a>&nbsp;" +
		"<a class='btn btn-small btn-danger bt_destroyer'><span class='fa fa-trash' aria-hidden='true'></span></a>"
	
	$(function () {
	
		function clear_group_form()
		{
			$('#groupManagerForm')[0].reset();
			$('#frm_grp_hint_name').text('');
			$('#frm_grp_hint_description').text('');
			$('#frm_grp_name').removeClass('has-error');								
			$('#frm_grp_description').removeClass('has-error');
		}
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});
	
	
		var table = $('#grouplist').DataTable({
		
		// o[
		
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
			"ajax": "{{ url('/manage-groups/list') }}",
			"columns": [
				{ "data":"name" },
				{ "data":"description" },
				{
					"data":"member_count.0.total",
					"defaultContent": 0
				},
				{
					"data" : null,
					"defaultContent" : window.options_buttons,
					"iDataSort": 0
				}
			]
		});
		
		$('#bt_adder').click(function(){
			clear_group_form();
			window.submit_type = "POST";
			$('#modalTitle').text('Create New Group');
			$('#groupManagerModal').modal('show');
		});
		
		$('#grouplist tbody').on( 'click', '.bt_adder', function () {
			
			if(window.undo_button!=null){
				window.undo_button.remove();
				window.recent_action.show();
			}
			
			window.recent_action = $(this);
			
			window.submit_type = "POST";
			var data = table.row( $(this).parents('tr') ).data();
			window.selected_group_row = table.row($(this).parents('tr'));
			$('#modalTitle').text('Edit Group');
			$('#addPointsModal').modal('show');
			$('#add_message').text('Please specify how much points you want to add to: '+data.name);
			$('#add_group_id').val(data.id);
			$('#add_error').text('');
		});
		
		$('#grouplist tbody').on( 'click', '.bt_editor', function () {
			window.submit_type = "PUT";
			clear_group_form();
			var data = table.row( $(this).parents('tr') ).data();
			window.selected_group_row = table.row($(this).parents('tr'));
			$('#modalTitle').text('Edit Group');
			$('#groupManagerModal').modal('show');
			$('#group_name').val(data.name);
			$('#group_description').val(data.description);
			$('#group_id').val(data.id);
				
		});
		$('#grouplist tbody').on( 'click', '.bt_destroyer', function () {
				window.submit_type = "DELETE";
				var data = table.row( $(this).parents('tr') ).data();
				bootbox.confirm("Do you really want to delete the group '"+data.name+"'?", function(result) {
					if(result){
						$.ajax({
							type: "DELETE",
							url : "{{ url('/manage-groups/') }}" +"/"+ data.id,
							success : function(data){
								console.log(data);									
								table.row( $(this).parents('tr') ).remove().draw();
							},
							error: function(data){
								//bootbox.prompt();
							}
						});
					}
				});
		});
		
		$('#groupManagerForm').submit(function( event ) {
			event.preventDefault();
			$.ajax({
				type: window.submit_type,
				url : window.submit_type=="PUT" ? "{{ url('/manage-groups/') }}" +"/"+ window.selected_group_row.data().id : "{{ url('/manage-groups/') }}",
				success : function(data){
					$('#groupManagerModal').modal('hide');
					if(window.submit_type=="PUT")
					{
						var d = window.selected_group_row.data();
						d.name = $('#group_name').val();
						d.description = $('#group_description').val();
						window.selected_group_row
							.data( d )
							.draw();
					}
					else
					{
						table.ajax.reload();
						table.order( [ 3, 'desc' ] ).draw();
						
					}
				},
				error: function(data){
					data = data.responseJSON;
					if(data.description){
						$('#frm_grp_description').addClass('has-error');
						$('#frm_grp_hint_description').text(data.description[0]);
					}
					if(data.tname){
						$('#frm_grp_name').addClass('has-error');								
						$('#frm_grp_hint_name').text(data.name[0]);
					}
					if(data.message){
						
					}
				},
				data: $('#groupManagerForm').serialize()
			});
			
			return false;
		});
		
		
		$('#addPointsForm').submit(function( event ) {
			event.preventDefault();
			var points = $('#add_points_value').val();
			if ($.isNumeric( points )) {
				$.ajax({
					type: "POST",
					url : "{{ url('/manage-groups/add_points') }}",
					success : function(data){
						console.log(data);
						console.log('transaction successful');
						$('#addPointsModal').modal('hide');
						console
						window.undo_button = $(window.undo_button_html);
						window.undo_button.append("&nbsp;");
						window.undo_button.insertAfter(window.recent_action);
						window.recent_action.hide();
						console.log('adding undo button after recent action');
						$('#grouplist tbody').on( 'click', '.bt_undo', function () {
							$.ajax({
								type: "POST",
								url : "{{ url('/manage-groups/undo') }}",
								success : function(data){
									window.undo_button.remove();
									window.recent_action.show();
								},
								data: $('#addPointsForm').serialize(),
								error: function(data){
									console.log(data.responseJSON.message[0]);
									$('#add_error').addClass('text-red');
									$('#add_error').text(data.responseJSON.message[0]);
								}
							});
						});
					},
					data: $('#addPointsForm').serialize(),
					error: function(data){
						console.log(data.responseJSON.message[0]);
						$('#add_error').addClass('text-red');
						$('#add_error').text(data.responseJSON.message[0]);
						
					}
				});
			}	else {
				$('#add_error').addClass('text-red');
				$('#add_error').text('Please enter only numeric values like 10, 200, 3000...');
				
			}
			
		});
		
	});
</script>
@endsection
