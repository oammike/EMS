@extends('layouts.main')


@section('metatags')
  <title>All Evaluation Forms</title>
    <meta name="description" content="all teams">

@stop


@section('content')




<section class="content-header">

      <h1>
       Evaluation Forms
        <small>all types of forms available for evaluation purposes</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        
        <li class="active">All Evaluation Forms</li>
      </ol><br/><br/>

      <a class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> Create New Form</a>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-heading"></div>
            <div class="box-body">

              

                <table id="forms" class="table table-bordered table-striped">
                      <thead>
                      <tr class="text-success">
                        
                        <th class="col-xs-4">Form Name</th>
                        <th class="col-xs-4">Description</th>
                        <th class="col-xs-4">Actions</th>
                        

                         
                      </tr>
                      </thead>
                      <tbody>

                         @foreach ($forms as $form)

                         <tr>
                            
                            <td>{{$form->name}}</td>
                            <td>{{$form->description}}</td>
                            
                            
                            <td>
                              <a class="btn btn-xs btn-default" href="{{action('EvalSettingController@edit',$form->id )}} "><i class="fa fa-pencil"></i> Edit </a>
                              <a class="btn btn-xs btn-default" href="{{action('EvalSettingController@destroy',$form->id)}} "><i class="fa fa-trash"></i> Delete </a>

                            </td>
                            
                            
                         </tr>
                  
                      @endforeach

                      
                      </tbody>
                      <tfoot>
                      <tr>
                       
                        <th class="col-xs-4"></th>
                        <th class="col-xs-4"></th>
                        <th class="col-xs-4"></th>
                      </tr>
                      </tfoot>
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
    $("#forms").DataTable({
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