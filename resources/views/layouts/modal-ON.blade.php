<div class="modal fade" id="myModal-ON" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-warning">
    <div class="modal-content">
      <div class="modal-header">
        
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
          <h4 class="modal-title" id="myModalLabel"><i class="fa fa-info-circle"></i> Dev Mode ON</h4>
        
      </div>
      <div class="modal-body">
        <p>Switching Dev Mode ON will make use of the dev copies of Google Spreadsheets instead of the live Lyft copies.</p>
        <p>Are you sure you want to turn ON Dev Mode?</p>
      </div>
      <div class="modal-footer no-border">
         {{ Form::open(array('action' => array('HomeController@devMode', "ON"), 'method'=>'post', 'class'=>'pull-right', 'id'=> 'devModeOFF')) }}      
          <button type="submit" class="btn btn-default" id="switchON"><i class="fa fa-check"></i> Yes</button>
        
        <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>{{ Form::close() }}
      </div>
    </div>
  </div>
</div>