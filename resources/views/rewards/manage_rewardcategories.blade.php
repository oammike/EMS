@extends('layouts.main')

@section('metatags')
  <title>Rewards Category Manager</title>
@stop

@section('content')
	<div class="container spark-screen">
		<div class="row">
			<div class="col-xs-10 col-md-12">
				<div class="box">
						<div class="box-header">
							<div class="box-tools pull-right">
								<div class="has-feedback">
									<a id="bt_adder" class="btn btn-primary btn-xs" data-skin="skin-blue" href="#"><i class="fa fa-plus"></i> Add Category</a>
								</div>
							</div><!-- /.box-tools -->
						</div><!-- /.box-header -->
						<div class="box-body">
							<table id="categorylist" class="table table-bordered table-striped">
								<thead>
									<tr>
										<th>Name</th>
										<th>Currently Assigned</th>
										<th>Created At</th>
										<th>Option</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td colspan="3">Loading...</td>
									</tr>
								</tbody>
							</table>
					</div><!-- /.box-body -->
				</div><!-- /.box -->
			</div><!-- /.col -->
		</div><!-- /.row -->
	</div>

	<div class="modal" id="categoryManagerModal">
		<form class="form-horizontal" id="categoryManagerForm" action="{{ url('/manage-categories') }}">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="modalTitle">New Category</h4>
					</div>
					<div class="box modal-body">
							<div class="box-body">
								<div class="form-group" id="frm_grp_name">
									<label for="name" class="col-sm-2 control-label">Name</label>
									<div class="col-sm-10">
										<input type="text" class="form-control" id="cm_name" name="name" placeholder="Deathwish Espresso 150ml">
										<input type="hidden" class="form-control" id="cm_id" name="id" value="0">
										<span id="frm_grp_hint_name" class="help-block"></span>
									</div>
								</div>

								<div class="form-group" id="frm_grp_name">
									<label class="col-sm-2 control-label">Variants:</label>
									<div class="col-sm-10 " >
										<div class="row additional_variants">
											<label for="tier1" class="col-sm-2 control-label">Tier 1</label>
											<div class="col-sm-5">
												<input type="text" class="form-control" id="tier1" name="tier[]" placeholder="Small Variant (100ml)">
											</div>
											<label for="cost1" class="col-sm-2 control-label">Price</label>
											<div class="col-sm-3">
												<input type="text" class="form-control" id="tier1" name="cost[]" placeholder="10">
											</div>
										</div>

										<div id="bt_variants_adder_wrapper" class="row">
											<div class="col-xs-6 col-xs-offset-6">
												<br/>
												<button type="button" id="bt_variants_adder" class="btn btn-sm btn-default pull-right">Add Another Variant</button>
											</div>
										</div>
									</div>

								</div>



							</div><!-- /.box-body -->
							<div class="overlay loader">
								<i class="fa fa-refresh fa-spin"></i>
							</div>
					</div>
					<div class="modal-footer">
          
						<button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
						<button type="submit" class="btn btn-primary">Save</button>
					</div>
				</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</form>
	</div><!-- /.modal -->


@stop

@section('footer-scripts')
<!-- page script -->
<script>
	window.current_tier = 0;
	window.selected_editing_id = 0;
	window.submit_type = "PUT";

	window.tier_list = null;

	window.variant_input_html = function(){
		window.current_tier = window.current_tier + 1;
		return '<div class="row additional_variants"><label for="tier'+window.current_tier+'" class="col-sm-2 control-label">Tier '+window.current_tier+'</label><div class="col-sm-5"><input type="text" class="form-control" id="tier'+window.current_tier+'" name="tier[]" placeholder="Small Variant (100ml)"></div><label for="cost'+window.current_tier+'" class="col-sm-2 control-label">Price</label><div class="col-sm-3"><input type="text" class="form-control" id="cost'+window.current_tier+'" name="cost[]" placeholder="10"></div></div>';
	}

	window.variant_edit_html = function(label, cost){
		window.current_tier = window.current_tier + 1;
		return '<div class="row additional_variants"><label for="tier'+window.current_tier+'" class="col-sm-2 control-label">Tier '+window.current_tier+'</label><div class="col-sm-5"><input type="text" class="form-control" id="tier'+window.current_tier+'" name="tier[]" placeholder="Small Variant (100ml)" value="'+label+'"></div><label for="cost'+window.current_tier+'" class="col-sm-2 control-label">Price</label><div class="col-sm-3"><input type="text" class="form-control" id="cost'+window.current_tier+'" name="cost[]" placeholder="10" value="'+cost+'"></div></div>';
	}

	$(function () {
		
		function clear_group_form()
		{
			$('#categoryManagerForm')[0].reset();
			reset_errors();
		}

		function reset_errors(){
			$('.loader').hide();
			$('#frm_grp_hint_name').text('');
			$('#frm_grp_name').removeClass('has-error');
		}

		$('#bt_adder').click(function(){
			clear_group_form();
			$('#modalTitle').text('Add New Category');
			$('#categoryManagerModal').modal('show');
			$('.additional_variants').remove();
			window.submit_type = "POST";
			window.current_tier = 0;
			$('#bt_variants_adder').click();
		});

		$('#categorylist tbody').on( 'click', '.bt_editor', function () {
			$('.loader').show();
			window.tier_list = null;
			window.current_tier = 0;
			window.submit_type = "PUT";
			var data = table.row( $(this).parents('tr') ).data();
			window.selected_group_row = table.row($(this).parents('tr'));
			reset_errors();

			$('.additional_variants').remove();
			$('#categoryManagerModal').modal('show');
			$.ajax({
				type:"GET",
				url : "{{ url('/manage-categories/fetch_tiers/') }}"+"/"+data.id,
				success : function(data){
					if(data.length<=0){
						$('#bt_variants_adder').click();
					}
					window.current_tier = 0;
						$.each(data, function(k, v) {
							var tier = v;
							console.log(tier);
							parsed = $.parseHTML(window.variant_edit_html(tier.description,tier.cost))
							$(parsed).insertBefore($('#bt_variants_adder_wrapper'));
						});

					$('.loader').hide();
				},
				error: function(data){
					alert("Could not fetch this category's tiers. Please try again later.");
					$('#categoryManagerModal').modal('hide');
				}
			});

			$('#cm_id').val(data.id);
			$('#cm_name').val(data.name);
			$('#modalTitle').text("Edit Category");
			window.selected_editing_id = data.id;
		});

		$('#categorylist tbody').on( 'click', '.bt_deleter', function () {
			var data = table.row( $(this).parents('tr') ).data();
			window.selected_group_row = table.row($(this).parents('tr'));
			if(confirm("Are you sure you want to delete the category: "+data.name+"?")){
				$.ajax({
					type: "DELETE",
					url : "{{ url('/manage-categories') }}"+"/"+data.id,
					success : function(data){
						table.ajax.reload();
					},
					error: function(data){
						data = data.responseJSON;
						alert(data.message);
					}
				});
			}
		});

		$('#categoryManagerForm').submit(function( event ) {
			event.preventDefault();
			$('.loader').show();
			$.ajax({
				type: window.submit_type,
				url : (window.submit_type==="PUT") ? "{{ url('/manage-categories') }}"+"/"+window.selected_editing_id : "{{ url('/manage-categories') }}",
				success : function(data){
					table.ajax.reload();
					$('#categoryManagerModal').modal('hide');
				},
				data: $('#categoryManagerForm').serialize(),
				error: function(data){
					reset_errors();
					data = data.responseJSON;
					if(data.name){
						$('#frm_grp_name').addClass('has-error');
						$('#frm_grp_hint_name').text(data.name[0]);
					}
					console.log('failed');
				}
			});
		});

		var table = $('#categorylist').DataTable({
			"pageLength": {{ $items_per_page }},
			"paging": true,
			"pagingType": "simple_numbers",
			"lengthChange": false,
			"searching": true,
			"ordering": true,
			"info": true,
			"autoWidth": true,
			"processing": true,
			"serverSide": true,
			"ajax": "{{ url('/manage-categories/list') }}",
			"columns": [
				{ "data" : "name" },
				{
					"data":"reward_count.0.total",
					"defaultContent": 0
				},
				{ "data":"created_at" },
				{
					"data" : null,
					"defaultContent" : "<a class='btn btn-small btn-primary bt_editor'><span class='fa fa-edit' aria-hidden='true'></span></a>&nbsp;<a class='btn btn-small btn-danger bt_deleter'><span class='fa fa-trash' aria-hidden='true'></span></a>"
				}
			]
		});


		$('#bt_variants_adder').click(function(){
			parsed = $.parseHTML(window.variant_input_html())
			$(parsed).insertBefore($('#bt_variants_adder_wrapper'));
		});



	});
</script>
@stop
