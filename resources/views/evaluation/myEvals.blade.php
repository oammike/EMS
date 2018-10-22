@extends('layouts.main')


@section('metatags')
  <title>My Evaluations </title>
    <meta name="description" content="all evaluations">

@stop


@section('content')




<section class="content-header">

      <h1><i class="fa fa-file-o"></i> My Evals 
      
        <small>{{$personnel->firstname}} {{$personnel->lastname}}</small>
        
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        
        <li class="active">My Evals</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      


      <div class="row">
        <div class="col-xs-12">
          <div class="box"  style="background: rgba(256, 256, 256, 0.4)">
            <div class="box-heading"></div>
            <div class="box-body">

              

                <table id="heads" class="table table-bordered table-striped">
                      <thead>
                      <tr class="text-success">
                        
                        <th class="col-xs-3">Eval Type</th>
                        <th class="col-xs-1">Overall Score</th>
                        <th class="col-xs-1">Salary Increase</th>
                        
                        <th class="col-xs-2">Eval Period</th>
                        <th class="col-xs-3">Evaluated By</th>
                       
                        <th class="col-xs-2">Actions</th>

                         
                      </tr>
                      </thead>
                      <tbody>

                         @foreach ($evaluations as $eval)

                         <tr>
                            
                            <td>{{$eval['evalType']}}</td>
                            <td>{{$eval['overallScore']}}</td>
                            
                            @if ($eval['overallScore'] == "DRAFT")
                            <td>{{$eval['salaryIncrease']}}</td>

                            @else
                            <td>{{$eval['salaryIncrease']}} %</td>

                            @endif
                            
                            <td>{{$eval['evalPeriod']}}</td>
                            <td>{{$eval['evaluatedBy']}}</td>
                            
                            <td>

                              @if ($eval['overallScore'] == "DRAFT")
                              <h5 class="text-danger"><em>Access Denied</em></h5>

                                 
                             @else

                                @if ($eval['coachingDone'])
                                  <a class="btn btn-xs btn-success" href="{{action('EvalFormController@show',$eval['id'] )}} "><i class="fa fa-eye"></i> View </a>
                                  @else
                                  <a class="btn btn-xs btn-danger" href="{{action('EvalFormController@review',$eval['id'] )}} "><i class="fa fa-search"></i> Review </a>

                                  @endif
                                  <a class="btn btn-xs btn-primary" href="{{action('EvalFormController@printEval',$eval['id'] )}} " target="_blank"><i class="fa fa-print"></i> Print PDF </a>

                             @endif

                             

                            </td>
                            
                            
                         </tr>
                         
                  
                      @endforeach

                      
                      </tbody>
                      <tfoot>
                      <tr>
                       
                      <!--  <th class="col-xs-3">Eval Type</th>
                        <th class="col-xs-1">Overall Score</th>
                        <th class="col-xs-1">Salary Increase</th>
                        
                        <th class="col-xs-2">Eval Period</th>
                        <th class="col-xs-3">Evaluated By</th>
                       
                        <th class="col-xs-2">Actions</th> -->
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
      //"order": [[ 4, "asc" ]],
      "lengthChange": true,
      "oLanguage": {
         "sSearch": "<small>To refine search, simply type-in</small><br> any values you want to look for:",
         "class": "pull-left"
       }
    });
    
  });
</script>
@stop