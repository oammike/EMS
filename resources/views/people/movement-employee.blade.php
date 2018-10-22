@extends('layouts.main')


@section('metatags')
  <title>Employee Movements of {{$personnel->firstname}} {{$personnel->lastname}} </title>
    <meta name="description" content="all teams">

@stop


@section('content')




<section class="content-header"  style="margin-bottom:50px">

      <h1>
       <a href="{{action('UserController@show',$personnel->id)}} ">{{$personnel->firstname}} {{$personnel->lastname}}</a>
        <small>Movements</small>
        <a class="btn btn-xs btn-danger" href="{{action('MovementController@createNew',$personnel->id)}} "><i class="fa fa-plus"></i> Add New Movement</a>
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li><a href="{{action('MovementController@index')}}">Employee Movement</a></li>
        <li class="active">{{$personnel->firstname}} {{$personnel->lastname}}</li>
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
                        
                        <th class="col-xs-2">Movement Type</th>
                        <th class="col-xs-3">From</th>
                        <th class="col-xs-3">To</th>
                        
                        <th class="col-xs-1">Effectivity Date</th>
                       
                        <th class="col-xs-2">Actions</th>

                         
                      </tr>
                      </thead>
                      <tbody>

                         @foreach ($movements as $movement)

                         <tr>
                            
                            <td>{{$movement['movementType']}}</td>
                            <td>{{$movement['oldValue']}}</td>
                            <td>{{$movement['newValue']}}</td>
                            
                            <td>{{$movement['effectivity']}}</td>
                            
                            <td>
                              <a class="btn btn-xs btn-primary" href="{{action('MovementController@show',$movement['id'] )}} "><i class="fa fa-file-o"></i> Show Form </a>

                              @if ($canEditMvt)
                              <a class="btn btn-xs btn-primary" href="{{action('MovementController@edit',$movement['id'] )}} "><i class="fa fa-pencil"></i> Edit </a>@endif

                              @if ($canDeleteMvt)
                              <a class="btn btn-xs btn-primary" data-toggle="modal" data-target="#myModal{{$movement['id']}}"><i class="fa fa-trash"></i> Delete </a>
                              @endif
                             

                            </td>
                            
                            
                         </tr>
                         @include('layouts.modals', [
                          'modelRoute'=>'movement.destroy',
                          'modelID' => $movement['id'], 
                          'modelName'=>$movement['movementType'], 
                          'modalTitle'=>'Delete', 
                          'modalMessage'=>'Are you sure you want to delete this?', 
                          'formID'=>'deleteMovement',
                          'icon'=>'glyphicon-trash' ])
                  
                      @endforeach

                      
                      </tbody>
                      <tfoot>
                      <tr>
                       
                       <th class="col-xs-2"></th>
                        <th class="col-xs-3"></th>
                        <th class="col-xs-3"></th>
                        
                        <th class="col-xs-1"> </th>
                       
                        <th class="col-xs-2"></th>
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