<div class="modal fade" id="myModal{{$modelID}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
          <h4 class="modal-title text-success" id="myModalLabel"> {{$modalTitle}} {{$modelName}}</h4>
        
      </div>
      <div class="modal-body-upload">
       

        {{ Form::open(['route' => 'resource.store','class'=>'col-lg-12', 'id'=>'uploadResource', 'name'=>'uploadResource', 'files'=>'true' ]) }} <br/><br/>

         <h5>{{ $modalMessage }}</h5>

        <div id="alert-upload" style="margin-top:10px"></div>
        <input type="file" name="resourceFile" id="resourceFile" class="form-control" required="required" />   <br/>
        <label for="filename">Display Name / File Label:</label>
        <input type="text" name="filename" id="filename" placeholder="Enter Display Name/File label" class="form-control" required="required" /><br/>
        <label for="category">File Category:</label>
         <select class="form-control" name="category" id="category" required="required">
          <option value="1">Select file category: </option>
          @foreach ($categories as $cat)
          <option value="{{$cat->id}}">{{$cat->name}}</option>

          @endforeach
         
          
        </select><br/>
        <label for="description">File Description:</label>
        <textarea class="form-control" name="description" id="description" placeholder="Enter file description"></textarea><br/>
       <br/><br/>
        
        
        
        <button type="button" class="btn btn-default btn-md pull-right " data-dismiss="modal"> <i class="fa fa-times"></i> Cancel</button>
        <button type="submit" id="upload" class="btn btn-success btn-md pull-right" style="margin-right:5px" > <i class="fa fa-upload" ></i> Upload </button>

{{ Form::close() }}
      </div>

      <div class="modal-body-generate"></div>
      <div class="modal-footer no-border">
        
      </div>
    </div>
  </div>
</div>