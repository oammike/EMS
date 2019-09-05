@extends('layouts.main')


@section('metatags')
  <title>All Manpower Requests | EMS </title>
    <meta name="description" content="all teams">

@stop


@section('content')




<section class="content-header"  style="margin-bottom:50px">

      <h1>
       <a href="{{action('UserController@show',$personnel->id)}} ">Manpower </a>
        <small>Requests</small>
        <a class="btn btn-xs btn-danger" href="{{action('ManpowerController@create')}} "><i class="fa fa-plus"></i> Request New</a>
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li><a href="{{action('MovementController@index')}}">My Requests</a></li>
        <li class="active">Manpower</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      


      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-heading"></div>
            <div class="box-body">

              

                <table id="heads" class="table table-bordered table-striped">
                      <thead>
                      <tr class="text-success">
                        
                        <th class="col-xs-2">Program/Dept</th>
                        <th class="col-xs-2">Position Title</th>
                        <th class="col-xs-1">Reason</th>
                        <th class="col-xs-1">Type</th>
                        <th class="col-xs-1">Hiring Source</th>
                        
                        <th class="col-xs-1">Start of Training </th>
                        <th class="col-xs-1">Requested By </th>
                       
                        <th class="col-xs-2">Actions</th>

                         
                      </tr>
                      </thead>
                      <tbody>

                        

                      
                      </tbody>
                      
                </table>
                  

  

            </div>

          </div>

        </div>

      </div>

     

    </section>

@stop

@section('footer-scripts')
<script>
  $(function () {
    $("#heads").DataTable({
      "responsive":true,
      "scrollX":true,
       "dom": '<"col-xs-1"f><"col-xs-11 text-right"l><"clearfix">rt<"bottom"ip><"clear">',
      "order": [[ 4, "asc" ]],
      "lengthChange": true,
      "oLanguage": {
         "sSearch": "<small>To refine search, simply type-in</small><br> any values you want to look for:",
         "class": "pull-left"
       }
    });
    
  });
</script>
@stop