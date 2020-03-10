@extends('layouts.main')


@section('metatags')
  <title>Resources</title>
    <meta name="description" content="all teams">

@stop


@section('content')




<section class="content-header">

      <h1>
       Resources
        <small>all OAMPI documents</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        
        <li class="active">OAMPI Resources</li>
      </ol><br/><br/>

      @if($isAdmin)
      <a href="#" data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#myModal_uploadResource" class="btn btn-md btn-success"><i class="fa fa-upload"></i> Upload New Document</a>
      @endif
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">

          @foreach ($allResource as $resource)
          <div class="box">
            
            <div class="box-body">

              <h4 id="resource_{{$resource['id']}}"><i class="fa fa-file"></i> {{$resource['name']}} </h4>

                <table id="forms" class="table table-bordered table-striped">
                      <thead>
                      <tr class="text-success">
                        
                        <th class="col-xs-4">File</th>
                        <th class="col-xs-4">Description</th>
                        <th class="col-xs-4">Actions</th>
                        

                         
                      </tr>
                      </thead>
                      <tbody>

                       
                        @foreach($resource['item'] as $res)
                         <tr>
                            
                            <td><strong> {{$res->name}}</strong> </td>
                            <td> {{$res->description}} </td>
                            
                            
                            <td>
                              <?php $coll = $employee->viewedResources->where('resource_id', $res->id)->where('agreed',1);
                              if ( count($coll) > 0 ) { ?>

                                 <a target="_blank" href="{{action('ResourceController@viewFile',$res->id)}} " class="btn btn-xs btn-primary"  style="margin-top:5px" ><i class="fa fa-search"></i> View {{$res->agreed}}</a>

                              <?php } else { ?>
                              <a target="_blank" href="#" data-toggle="modal" data-target="#myModalAcknowledge{{$res->id}}" class="btn btn-xs btn-danger"  style="margin-top:5px" ><i class="fa fa-search"></i> View</a>

                              <?php } ?>

                              @if($isAdmin)
                              <a href="{{action('ResourceController@track',$res->id) }}" class="btn btn-xs btn-primary"  style="margin-top:5px"><i class="fa fa-list"></i> Track</a>
                              <a href="#"  style="margin-top:5px" class="btn btn-xs btn btn-default" data-toggle="modal" data-target="#myModal{{$res->id}}" ><i class="fa fa-trash"></i> Delete</a>
                              @endif
                             <div class="clearfix"></div>
                             

                            </td>
                            
                            
                         </tr>

                                 @include('layouts.modals-acknowledge', [
                                  'modelRoute'=>'resource.viewItem',
                                  'modelID' => $res->id, 
                                  'modelName'=>$res->name, 
                                  'modalTitle'=>'View', 
                                  'modalMessage'=> $res->name, 
                                  'formID'=>'viewResource',
                                  'icon'=>'glyphicon-check' ])

                                   @include('layouts.modals', [
                          'modelRoute'=>'resource.destroy',
                          'modelID' => $res->id, 
                          'modelName'=>$res->name, 
                          'modalTitle'=>'Delete', 
                          'modalMessage'=>'Are you sure you want to delete this?', 
                          'formID'=>'deleteEval',
                          'icon'=>'glyphicon-trash' ])
                     
                      


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

                @include('layouts.modals-uploadResource', [
                                'modelRoute'=>'resource.store',
                                'modelID' => '_uploadResource', 
                                'modelName'=>"New OAMPI Resource file ", 
                                'modalTitle'=>'Upload', 
                                'modalMessage'=>'Select file to upload:', 
                                'formID'=>'uploadResource',
                                'icon'=>'glyphicon-up' ])
                  

  

            </div><!--end box-body-->

          </div><!--end box-->

          @endforeach

        </div><!--end col xs 12-->

      </div><!--end row-->

     

    </section>

@stop

@section('footer-scripts')
<script>
  $(function () {
    $("#forms").DataTable({
      "responsive":true,
      "scrollX":true,
       "dom": '<"col-xs-1"f><"col-xs-11 text-right"l><"clearfix">rt<"bottom"ip><"clear">',
      "order": [[ 0, "asc" ]],
      "lengthChange": true,
      "oLanguage": {
         "sSearch": "<small>To refine search, simply type-in</small><br> any values you want to look for:",
         "class": "pull-left"
       }
    });

    $('.yes').on('click', function(e) {

      
      var _token = "{{ csrf_token() }}";
     
      var id = $(this).attr('data-resID');
      console.log("clicked yes id:" + id);
     

      $.ajax({
                      url:"{{action('ResourceController@viewItem')}} ",
                      type:'POST',
                      data:{
                        'user_id': '{{ Auth::user()->id }}',
                        'id': id,
                        'agreed': 1 ,
                        _token:_token},

                      error: function(response)
                      { console.log("error"); return false;
                      },
                      success: function(response)
                      {
                        window.location.href="./oampi-resources/item/"+response;
                        
                      }//end success

          }); //end ajax


    });

    $('.no').on('click', function(e) {

     var _token = "{{ csrf_token() }}";
     
      var id = $(this).attr('data-resID');
      console.log("clicked no id:" + id);
     

      $.ajax({
                      url:"{{action('ResourceController@viewItem')}} ",
                      type:'POST',
                      data:{
                        'user_id': '{{ Auth::user()->id }}',
                        'id': id,
                        'agreed': 0 ,
                        _token:_token},

                      error: function(response)
                      { console.log("error"); return false;
                      },
                      success: function(response)
                      {
                        window.location.href = "./oampi-resources/item/"+response;
                        
                      }//end success

          }); //end ajax


    });

    


    
  });
</script>
@stop