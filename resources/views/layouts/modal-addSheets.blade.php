<div class="modal fade" id="addSheets" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-success">
    <div class="modal-content">
      
      <div class="modal-header">
        
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
          <h4 class="modal-title" id="myModalLabel"> Add Sheets to the Dashboard</h4>
        
      </div>
      <div class="modal-body">{{ Form::open(['action' => 'User_TrackerSheetController@store', 'method'=>'post','class'=>'pull-left', 'id'=> 'addSheets' ]) }} 
        <p>Select which tracking sheets you'd like to add into the Dashboard: <br/></p>
        <div style="padding-left:25px;">
          @foreach ($trackingSheets as $trackingSheet)
            @if ($preferredSheets->contains('id',$trackingSheet->id))
            <label for="sheet-{{$trackingSheet->id}}" class="trackingSheets"> {{Form::checkbox('trackingSheets[]',$trackingSheet->id,true,['class'=>"trackingSheets",'id'=>'sheet-'.$trackingSheet->id])}} {{$trackingSheet->name}} </label>
            <div class="clearfix"></div>

            @else
            <label for="sheet-{{$trackingSheet->id}}" class="trackingSheets"> {{Form::checkbox('trackingSheets[]',$trackingSheet->id,null,['class'=>"trackingSheets",'id'=>'sheet-'.$trackingSheet->id])}} {{$trackingSheet->name}} </label>
            <div class="clearfix"></div>

            @endif
          
          

          @endforeach
        </div>
          
        
        <button type="button" class="btn btn-default pull-right" data-dismiss="modal" ><i class="fa fa-times"></i> Cancel</button>
        <button type="submit" class="btn btn-default pull-right" style="margin-right:10px"><i class="fa fa-save"></i> Save</button>
        
        {{ Form::close() }}
        <div class="clearfix"></div>
      </div>
      <div class="modal-footer no-border"></div>
    </div>
  </div>
</div>