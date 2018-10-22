<div class="modal fade" id="myModalAcknowledge{{$modelID}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
          <h4 class="modal-title" id="myModalLabel"><img src="{{asset('public/img/oabpo_logo.jpg')}}" width="90px" align="center" />{{$modelName}}</h4>
        
      </div>
      <div class="modal-body">
        
        <p>Clicking "YES" signifies that I agree and shall faithfully  comply  with  this <strong>{{ $modalMessage }}</strong> and its 
        accompanying  policies, rules,  and regulations.</p>
        <p> It also signifies that I have  read  and will  devote  to  familiarize myself  with  its contents  and follow  its provisions  to  the best  of  my  ability.</p>

        <br/><br/>
        @if(!empty($signature))<img class="signature" src="{{$signature}}" width="140" style="margin-left: 100px" /><br/>@endif
        <strong>Employee Name:</strong> {{$employee->firstname}} {{$employee->lastname}} [{{$employee->employeeNumber}} ]<br/>
        <strong>Position: </strong> {{$employee->position->name}}<br/>
        <strong>Date: </strong> <?php echo date('M d, Y H:i:s')?>
      </div>
      <div class="modal-footer no-border">
        
        <form name="{{$formID}}" id="{{$formID}}">
         <div id="agreement"></div>  
         <input type='hidden' name='id' id='id' value='{{$modelID}}' />   
          <!-- <button type="submit" id="yes" class="btn btn-primary {{$icon}} glyphicon "> YES, I agree </button> -->
          <button type="button" id="yes" class="yes btn btn-primary btn-md" data-resID="{{$modelID}}"><i class="fa fa-check-square-o"></i> YES, I agree </button>
          <!-- <a href="#" id="no" class="no btn btn-success btn-md "> Proceed anyway </a> -->
          <button title="You may view and agree later on" type="button" id="no" class="no btn btn-success btn-md" data-resID="{{$modelID}}"><i class="fa fa-square-o"></i> View now, I'll agree later... </button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        </form>


      </div>
    </div>
  </div>
</div>