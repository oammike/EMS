@extends('layouts.main')


@section('metatags')
  <title>Resources</title>
    <meta name="description" content="all teams">

@stop


@section('content')




<section class="content-header">

      <h1>
       Resource Access
        <small>all OAMPI employees </small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        
        <li class="active">OAMPI Resources</li>
      </ol><br/><br/>

      
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">

         
          <div class="box">
            
            <div class="box-body">

              <h4><i class="fa fa-file"></i> All Employees who accessed file: <span class="text-success">{{$resource->name}}</span> </h4>

                <table id="forms" class="table table-bordered table-striped">
                      <thead>
                      <tr class="text-success">
                        <th class="col-xs-2">Date Accessed</th>
                        <th class="col-xs-1">Agreed To Terms</th>
                        <th class="col-xs-2">Last name</th>
                        <th class="col-xs-2">First name</th>
                        <th class="col-xs-3">Position</th>
                        <th class="col-xs-2">Program</th>
                        
                       
                        

                         
                      </tr>
                      </thead>
                      <tbody>

                       
                        @foreach($track as $visitor)
                         <tr>
                            <td> {{$visitor['accessed']}} </td>
                            
                            <td>{{$visitor['agreed']}} </td>
                           
                            
                            <td><a href="{{action('UserController@show',$visitor['user_id'])}} "> {{$visitor['lastname']}} </a></td>
                            <td><a href="{{action('UserController@show',$visitor['user_id'])}} ">  {{$visitor['firstname']}} </a></td>
                            <td> {{$visitor['position']}}</td>
                            <td> {{$visitor['program']}} </td>
                            
                                
                            
                         </tr>



                       @endforeach
                  
                     
                      </tbody>
                      <tfoot>
                      <tr>
                       
                        <th class="col-xs-3"></th>
                        <th class="col-xs-2"></th>
                        <th class="col-xs-2"></th>
                        <th class="col-xs-3"></th>
                        <th class="col-xs-2"></th>
                      </tr>
                      </tfoot>
                </table>

                <p></p><p></p>
                <a href="{{action('ResourceController@index')}}" class="btn btn-md btn-success"><i class="fa fa-arrow-left"></i> Back to all Resources</a>
                  

  

            </div><!--end box-body-->

          </div><!--end box-->

         

        </div><!--end col xs 12-->

      </div><!--end row-->

     

    </section>

@stop

@section('footer-scripts')
<script>
  $(function () {
    $("#forms").DataTable({
      "responsive":false,
      "scrollX":true,
       "dom": '<"col-xs-1"f><"col-xs-11 text-right"l><"clearfix">rt<"bottom"ip><"clear">',
      "order": [[ 0, "desc" ]],
      "lengthChange": true,
      "oLanguage": {
         "sSearch": "<small>To refine search, simply type-in</small><br> any values you want to look for:",
         "class": "pull-left"
       }
    });
    
  });
</script>
@stop