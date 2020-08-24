@extends('layouts.main')

@section('metatags')
  <title>Donation Intents</title>
@stop

@section('content')
<section class="content-header">
  <h1><i class="fa fa-gift"></i> OAM Rewards: Donation Intents</h1>

  <ol class="breadcrumb">
    <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Home</a></li>
    <li><a href="{{ action('RewardsHomeController@rewards_catalog') }}">Rewards</a></li>
    <li class="active">Donation Intents</li>
  </ol>
</section>

<section class="content">
  <div class="container">
    <div class="row">
      <div class="col-xs-10 col-md-12">
        <div class="box">
          <div class="box-header">
            <div class="box-tools">
              <!--  <button class="btn btn-sm btn-default" id="bt_filter_toggle">Show Redeemed</button> -->
              <a href="{{ url('/export-donation-intents') }}" target="_blank"><button class="btn btn-sm btn-default" id="bt_export"><i class="fa fa-download"></i> Export</button></a>
            </div>
            <br/>
          </div>
            
            <div class="box-body ">
              <table id="rewardlist" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>Donor</th>
                    <th>Donation Category</th>
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

  
    <div class="modal" id="confirmModal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Confirm Voucher Denial</h4>
          </div>
          <div class="modal-body">
            <p>Are you sure you want to deny <span id="deny_name"></span>'s intent to donate to <span id="deny_vname"></span>.<br/> Note: <span id="deny_points"></span> will be refunded.</p>

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

    <div class="modal" id="messengerModal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="messenger_title"></h4>
          </div>
          <div class="modal-body">
            <p id="messenger_body">Confirmation Message</p>

          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary" id="deny_confirm" data-dismiss="modal">Ok</button>
          </div>
        </div>
        <!-- /.modal-content -->
      </div>
      <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->

  
  
  
@stop

@section('footer-scripts')
<!-- page script -->
<script>
  window.active_button = null;
  window.loader = $('<i class="fa fa-refresh fa-spin"></i>');
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
        data: {"intent_id":window.selected_reward_id},
        type: "POST",
        url: "{{ url('/deny-donation-intent') }}",
        success : function(data){
          window.selected_group_row.remove();
          console.log();
          $('#confirmModal').modal('hide');
        },
        error: function(data){
          $('#messenger_title').text("Error");
          $('#messenger_body').text(data.message);
          $('#messengerModal').modal('show');
        }
      });

    });

    $('#rewardlist tbody').on( 'click', '.bt_denier', function () {

      var data = window.table.row( $(this).parents('tr') ).data();      
      window.selected_group_row = $(this).parents('tr');
      window.selected_reward_id = data.id;

      $('#deny_error').removeClass('text-red');
      $('#deny_error').text("");
      //$('#deny_loader').hide();

      $('#deny_name').text(data.user.firstname + " " + data.user.lastname);
      $('#deny_vname').text(data.donation.name);
      $('#deny_points').text(data.donated_points);

      $('#confirmModal').modal('show');

    });

    $('#rewardlist tbody').on( 'click', '.bt_approver', function () {
      console.log($(this));

      window.active_button = $(this);
      var micro = (Date.now() % 1000) / 1000;
      var loader = window.loader;
      loader.insertBefore($(this));
      window.active_button.hide();

      var data = window.table.row( $(this).parents('tr') ).data();      
      window.selected_group_row = $(this).parents('tr');
      window.selected_reward_id = data.id;
      
      $.ajax({
        data: {"intent_id":window.selected_reward_id},
        type: "POST",
        url : "{{ url('/confirm-donation-intent') }}",
        success : function(data){          
          window.selected_group_row.remove();
        },
        error: function(data){
          $('#messenger_title').text("Error");
          $('#messenger_body').text(data.message);
          $('#messengerModal').modal('show');
          loader.remove();
          window.active_button.show();
        }
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
      "ajax": "{{ url('/manage-donation-intents/list') }}",
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
            return  data.donation.name;
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
            return "<a class='btn btn-small btn-primary bt_approver'><span class='fa fa-edit' aria-hidden='true'></span> Approve</a>&nbsp;&nbsp;<a class='btn btn-small btn-danger bt_denier'><span class='fa fa-thumbs-down' aria-hidden='true'></span> Deny</a>";
            
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
