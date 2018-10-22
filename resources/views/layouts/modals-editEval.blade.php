<div class="modal fade" id="myModal{{$modelID}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
          <h4 class="modal-title text-success" id="myModalLabel"><i class="fa fa-clock-o"></i> Modify Evaluation Period</h4>
        
      </div> {{ Form::open(['route' => 'evalForm.updatePeriod','class'=>'col-lg-12', 'id'=>'reportIssue', 'name'=>'reportIssue' ]) }}

      <input type="hidden" name="id" value="{{$evalForm->id}}" />
      <div class="modal-body-upload" style="padding:20px;">
       

         <div class="row">

              <div class="col-sm-6">
                <label for="newStart">From:  </label> {{$startPeriod->format('M d, Y')}}
                <input required type="text" class="form-control datepicker" style="width:80%" name="newStart" id="newStart" placeholder="Enter new date (mm/dd/yyyy)" value="{{$startPeriod->format('m/d/Y')}}" /> 
                <div id="alert-newStart" style="margin-top:10px"></div>
              </div>
              <div class="col-sm-6">
                <label for="newEnd">To: </label> {{$endPeriod->format('M d, Y')}}
                <input required type="text" class="form-control datepicker" style="width:80%" name="newEnd" id="newEnd" placeholder="Enter new date (mm/dd/yyyy)" value="{{$endPeriod->format('m/d/Y')}}" /> 
                <div id="alert-newEnd" style="margin-top:10px"></div>

              </div>
              
               
         </div>

        <div id="alert-upload" style="margin-top:10px"></div>
         <br/><br/>
        
        
        
        <button type="button" class="btn btn-default btn-md pull-right " data-dismiss="modal"> <i class="fa fa-times"></i> Cancel</button>
        <button type="submit" id="upload" class="btn btn-success btn-md pull-right" style="margin-right:5px" > <i class="fa fa-save" ></i> Save </button>

     
      </div> {{ Form::close() }}

      <div class="modal-body-generate"></div>
      <div class="modal-footer no-border">
        
      </div>
    </div>
  </div>
</div>