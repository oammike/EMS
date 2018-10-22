<div class="modal fade" id="myModal-OFF" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-warning">
    <div class="modal-content">
      <div class="modal-header">
        
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
          <h4 class="modal-title" id="myModalLabel"><i class="fa fa-info-circle"></i> Dev Mode OFF</h4>
        
      </div>
      <div class="modal-body">
        <p>Switching Dev Mode OFF will make use of the <span class="text-black"><strong>LIVE copies of Lyft Google Spreadsheets</strong></span>.</p>
        <p>Are you sure you want to turn OFF Dev Mode?</p>
      </div>
      <div class="modal-footer no-border">
         
        {{ Form::open(array('action' => array('HomeController@devMode', "OFF"), 'method'=>'post', 'class'=>'pull-right', 'id'=> 'devModeON')) }}  
          <button type="submit" class="btn btn-default" id="switchOFF"><i class="fa fa-check"></i> Yes</button>
        
        <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>{{ Form::close() }}
      </div>
    </div>
  </div>
</div>