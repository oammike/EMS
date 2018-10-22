<div class="modal fade" id="myModal_leader{{$modelID}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
          <h4 class="modal-title" id="myModalLabel"> {{$modalTitle}} {{$modelName}}</h4>
        
      </div>
      <div class="modal-body">
        {{ $modalMessage }}
      </div>
      <div class="modal-footer no-border">
        {{ Form::open(['route' => [$modelRoute, $modelID], 'method'=>$modalTitle,'class'=>'btn-outline pull-right', 'id'=> $formID ]) }}     
          <button type="submit" class="btn btn-default {{$icon}} glyphicon ">Yes</button>
        
        <button type="button" class="btn btn-primary" data-dismiss="modal">Cancel</button>{{ Form::close() }}
      </div>
    </div>
  </div>
</div>