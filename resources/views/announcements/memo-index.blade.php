@extends('layouts.main')


@section('metatags')
  <title>Announcements - Index | EMS </title>
    <meta name="description" content="Announcements and Memo Manager">
@stop


@section('content')
  <section class="content-header"  style="margin-bottom:50px">

    <h1>
     <a href="{{action('AnnouncementController@index')}} ">Announcements </a>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
      <li class="active">Announcements Manager</li>
    </ol>
  </section>

    <!-- Main content -->
  <section class="content">
    <div class="container">
      <div class="row">
        <div class="col-xs-10 col-md-12">
          <div class="box">
            <div class="box-header">
              <div class="box-tools">
                <!--  <button class="btn btn-sm btn-default" id="bt_filter_toggle">Show Redeemed</button> -->
                <a class="btn btn-danger pull-right" href="{{action('AnnouncementController@create')}}" target="_self"><i class="fa fa-plus"></i> Create New Announcement</a>
              </div>
              <br/>
            </div>

              <div class="box-body ">
                <table id="memolist" class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th>Title</th>
                      <th>Type</th>
                      <th>Publish Date</th>
                      <th>Expiry Date</th>
                      <th>Author</th>
                      <th>Draft</th>
                      <th>Hidden</th>
                      <th>Pinned</th>
                      <th>Options</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td colspan="8">Loading...</td>
                    </tr>
                  </tbody>
                </table>
              </div><!-- /.box-body -->
          </div><!-- /.box -->
        </div><!-- /.col -->
      </div><!-- /.row -->
    </div>
  </section>

@stop

@section('footer-scripts')
<script>

window.table = $('#memolist').DataTable({
  "pageLength": 25,
  "paging": true,
  "pagingType": "simple_numbers",
  "lengthChange": false,
  "searching": true,
  "ordering": true,
  "autoWidth": true,
  "processing": true,
  "serverSide": true,
  "ajax": "{{ url('/announcement/list') }}",
  "columns": [
        { "data" : null,
          "render" : function ( row, type, val, meta ) {
            var p = $('<p>' + val.title + val.decorative_title + '</p>');
            return p.text();
          }
        },
        { "data" : "template" },
        { "data" : "publishDate" },
        { "data" : "publishExpire" },
        { "data" : "author" },
        {
          "orderable" : false,
          "data" : null,
          "render" : function ( row, type, val, meta ) {
            var color = '<i aria-hidden="true" class="fa fa-check text-success mr-2"></i>';
            if(val.isDraft!='1'){
              color = '<i aria-hidden="true" class="fa fa-times text-secondary mr-2"></i>';
            }
            return  color ;
          }
        },
        {
          "orderable" : false,
          "data" : null,
          "render" : function ( row, type, val, meta ) {
            var color = '<i aria-hidden="true" class="fa fa-check text-success mr-2"></i>';
            if(val.hidden!='1'){
              color = '<i aria-hidden="true" class="fa fa-times text-secondary mr-2"></i>';
            }
            return  color ;
          }
        },
        {
          "orderable" : false,
          "data" : null,
          "render" : function ( row, type, val, meta ) {
            var color = '<i aria-hidden="true" class="fa fa-check text-success mr-2"></i>';
            if(val.showAlways!='1'){
              color = '<i aria-hidden="true" class="fa fa-times text-secondary mr-2"></i>';
            }
            return  color ;
          }
        },
        {
          "orderable" : false,
          "data" : null,
          "render" : function ( row, type, val, meta ) {
            return "<a href='{{action('AnnouncementController@index')}}/" + val.id + "/edit' class='btn btn-small btn-primary'><span class='fa fa-edit' aria-hidden='true'></span> Edit</a>";
          }
        }
      ]
});

</script>
@stop