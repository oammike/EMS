@extends('layouts.main')

@section('metatags')
  <title>Donation Categories Manager</title>
@stop

@section('content')
<section class="content-header">
    <h1><i class="fa fa-gift"></i> OAM Rewards: Donation Categories Manager</h1>

    <ol class="breadcrumb">
      <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="{{ action('RewardsHomeController@rewards_catalog') }}">Rewards</a></li>
      <li class="active">Donation Categories Manager</li>
    </ol>
  </section>

  <section class="content">
    <div class="container">
      <div class="row">
        <div class="col-xs-10 col-md-12">
          <div class="box">
              <div class="box-header">
                <div class="box-tools pull-right">
                  <div class="has-feedback">
                    <a id="bt_adder" class="btn btn-primary btn-xs" data-skin="skin-blue" href="#"><i class="fa fa-plus"></i> Add Donation Category</a>
                  </div>
                </div><!-- /.box-tools -->
              </div><!-- /.box-header -->
              <div class="box-body">
                <table id="donatelist" class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th>Photo</th>
                      <th>Name</th>
                      <!-- <th>Point Value</th> -->
                      <th>Minimum</th>
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


  <div class="modal" id="donateManagerModal">
    <form class="form-horizontal" id="donateManagerForm" action="{{ url('/manage-donations') }}">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="modalTitle">New Donation Category</h4>
          </div>
          <div class="modal-body">
              <div class="box-body">
                <div class="form-group" id="frm_grp_name">
                  <label for="new_name" class="col-sm-3 control-label">Name</label>
                  <div class="col-sm-9">
                    <input type="text" class="form-control" id="new_name" name="name" placeholder="Red Cross">
                    <span id="frm_grp_hint_name" class="help-block"></span>
                  </div>
                </div>
                <div class="form-group" id="frm_grp_description">
                  <label for="new_description" class="col-sm-3 control-label">Description</label>
                  <div class="col-sm-9">
                    <input type="text" class="form-control" id="new_description" name="description" placeholder="A premier humanitarian organization in the country that is committed on various social welfare services and promoting volunteerism.">
                    <span id="frm_grp_hint_name" class="help-block"></span>
                  </div>
                </div>
                <!--
                <div class="form-group" id="frm_grp_value">
                  <label for="new_value" class="col-sm-3 control-label">Point Value<br/>(how much points = ₱1.00)</label>
                  <div class="col-sm-9">
                    <input type="text" class="form-control" id="new_value" name="point_value" placeholder="50">
                    <span id="frm_grp_hint_value" class="help-block"></span>
                  </div>
                </div>
                -->
                <div class="form-group" id="frm_grp_minimum">
                  <label for="new_minimum" class="col-sm-3 control-label">Minimum</label>
                  <div class="col-sm-9">
                    <input type="text" class="form-control" id="new_minimum" name="minimum" placeholder="50">
                    <span id="frm_grp_hint_minimum" class="help-block"></span>
                  </div>
                </div>
                <div class="form-group" id="frm_grp_photo">
                  <label for="new_photo" class="col-sm-3 control-label">Banner Photo</label>
                  <div class="col-sm-9">
                    <input type="file" id="new_photo" name="attachment_image">
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

  <div class="modal" id="donateEditorModal">
    <form class="form-horizontal" id="donateEditorForm" action="#">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="modalTitle">Edit Donation</h4>
          </div>
          <div class="box modal-body">
              <div class="box-body">
                <div class="form-group">
                  <div class="col-sm-10">
                    <span id="donate_editor_name" class="help-block"></span>
                  </div>
                </div>
                <div class="form-group" id="frm_grp_edescription">
                  <label for="edit_description" class="col-sm-3 control-label">Description</label>
                  <div class="col-sm-9">
                    <input type="text" class="form-control" id="edit_description" name="description" placeholder="A premier humanitarian organization in the country that is committed on various social welfare services and promoting volunteerism.">
                    <span id="frm_grp_hint_edescription" class="help-block"></span>
                  </div>
                </div>
                <!--
                <div class="form-group" id="frm_grp_evalue">
                  <label for="edit_value" class="col-sm-3 control-label">Point Value<br/>(how much points = ₱1.00)</label>
                  <div class="col-sm-9">
                    <input type="text" class="form-control" id="edit_value" name="point_value" placeholder="50">
                    <span id="frm_grp_hint_evalue" class="help-block"></span>
                  </div>
                </div>
                -->
                <div class="form-group" id="frm_grp_eminimum">
                  <label for="edit_minimum" class="col-sm-3 control-label">Minimum</label>
                  <div class="col-sm-9">
                    <input type="text" class="form-control" id="edit_minimum" name="minimum" placeholder="50">
                    <span id="frm_grp_hint_eminimum" class="help-block"></span>
                  </div>
                </div>


              </div><!-- /.box-body -->
              <div class="overlay" id="editor_loader">
                <i class="fa fa-refresh fa-spin"></i>
              </div>
          </div>
          <div class="modal-footer">
            <input type="hidden" id="donate_editor_id" name="id">
            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Save Changes</button>
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
    return  "<img class='test' src='"+window.media_directory+"/"+data.media[0].id+"/"+data.media[0].file_name+"' width='25' height='25'>";
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
      $('#donateManagerForm')[0].reset();
      progressbar.attr('aria-valuenow', 0).css('width','0%');
      reset_errors();
    }
    function reset_errors(){
      $('#frm_grp_hint_name').text('');
      $('#frm_grp_hint_description').text('');
      //$('#frm_grp_hint_value').text('');
      $('#frm_grp_hint_minimum').text('');
      $('#frm_grp_hint_photo').text('');
      $('#frm_grp_hint_edescription').text('');
      //$('#frm_grp_hint_evalue').text('');
      $('#frm_grp_hint_eminimum').text('');
      $('#frm_grp_name').removeClass('has-error');
      $('#frm_grp_description').removeClass('has-error');
      //$('#frm_grp_value').removeClass('has-error');
      $('#frm_grp_minimum').removeClass('has-error');
      $('#frm_grp_photo').removeClass('has-error');
      $('#frm_grp_edescription').removeClass('has-error');
      //$('#frm_grp_evalue').removeClass('has-error');
      $('#frm_grp_eminimum').removeClass('has-error');
    }



    $('#donateEditorForm').submit(function( event ) {
      event.preventDefault();
      $('#editor_loader').show();
      if ($.isNumeric( $('#edit_minimum').val() )) {
        $.ajax({
          type: "PUT",
          url : "{{ url('/manage-donations') }}"+"/"+window.selected_editing_id,
          success : function(data){
            $('#editor_loader').hide();
            $('#donateEditorModal').modal('hide')
            reset_errors();
            window.table.ajax.reload();
          },
          data: $('#donateEditorForm').serialize(),
          error: function(data){
            $('#editor_loader').hide();
            console.log(data.responseJSON.message[0]);
            data = data.responseJSON;
            if(data.quantity){
              $('#frm_grp_quantity').addClass('has-error');
              $('#frm_grp_hint_quantity').text(data.quantity[0]);
            }
            if(data.cost){
              $('#frm_grp_cost').addClass('has-error');
              $('#frm_grp_hint_cost').text(data.cost[0]);
            }

          }
        });
      } else {
        $('#editor_loader').hide();
        $('#frm_grp_hint_eminimum').addClass('text-red');
        $('#frm_grp_hint_eminimum').text('Please enter only numeric values like 10, 200, 3000...');

      }

    });

    $('#bt_adder').click(function(){
      clear_group_form();

      $('#donateManagerForm').ajaxForm({
        type: "POST",
        dataType: 'json',
        error: function(data, status, xhr, $form){
            data = data.responseJSON;
            if(data.quantity){
              $('#frm_grp_description').addClass('has-error');
              $('#frm_grp_hint_description').text(data.quantity[0]);
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

            console.log('failed');

        },
        success: function(responseText, statusText, xhr, $form){
          window.table.ajax.reload();
          $('#donateManagerModal').modal('hide');
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

      $('#modalTitle').text('Add New Voucher');
      $('#donateManagerModal').modal('show');
    });

    $('#donatelist tbody').on( 'click', '.bt_editor', function () {
      var data = window.table.row( $(this).parents('tr') ).data();
      window.selected_group_row = window.table.row($(this).parents('tr'));
      console.log(data);
      $('#editor_loader').hide();
      $('#donateEditorModal').modal('show');
      $('#edit_description').val(data.description);
      //$('#edit_value').val(data.point_value);
      $('#edit_minimum').val(data.minimum);
      $('#donate_editor_name').text("Edit details for: "+data.name);

      window.selected_editing_id = data.id;
    });

    $('#donatelist tbody').on( 'click', '.bt_deleter', function () {
      var data = table.row( $(this).parents('tr') ).data();
      window.selected_group_row = table.row($(this).parents('tr'));
      if(confirm("Are you sure you want to delete the donation category: "+data.name+"?")){
        $.ajax({
          type: "DELETE",
          url : "{{ url('/manage-donations') }}"+"/"+data.id,
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

    $('#donatelist tbody').on( 'click', '.img_zoomer', function () {
      var data = window.table.row( $(this).parents('tr') ).data();
      $('#photoZoomTitle').text('Zoom');
      $('#photoZoomCaption').text(data.name + " (" + data.cost+ " credits) "+ " - "+ data.description);
      $('#photoZoomSrc').attr('src',window.media_directory+data.attachment_image);
      $('#photoZoomModal').modal('show');
    });

    window.table = $('#donatelist').DataTable({
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
      "ajax": "{{ url('/manage-donations/list') }}",
      "columns": [
        {
          "data" : null,
          //"render" : window.generate_image_column(row,type,full,meta)
          "render": function ( data, type, full, meta ) {

            return  "<img class='img_zoomer' src='"+window.media_directory+data.attachment_image+"' width='25' height='25'>";
            //generate_image_column (data)
          }
        },
        { "data":"name" },
        //{ "data":"point_value" },
        { "data":"minimum" },
        {
          "data" : null,
          "defaultContent" : "<a class='btn btn-small btn-primary bt_editor'><span class='fa fa-edit' aria-hidden='true'></span></a>&nbsp;<a class='btn btn-small btn-danger bt_deleter'><span class='fa fa-trash' aria-hidden='true'></span></a>"
        }
      ]
    });

  /*
    $('#donatelist').DataTable({
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
