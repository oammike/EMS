<div class="modal fade" id="myModal{{$modelID}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
          <h4 class="modal-title text-success" id="myModalLabel"><i class="fa fa-clock-o"></i> Report A DTR Issue</h4>
        
      </div> {{ Form::open(['route' => 'user_cws.store','class'=>'col-lg-12', 'id'=>'reportIssue', 'name'=>'reportIssue' ]) }}
      <input type="hidden" name="biometrics_id" value="{{$biometrics_id}}" />
      <input type="hidden" name="DproductionDate" value="{{$DproductionDate}}" />
      <input type="hidden" name="user_id" value="{{$user->id}}" />
      <input type="hidden" name="isRD" value="{{$isRD}}" />
      <input type="hidden" name="approver" value="{{$approver}}" />
      <input type="hidden" name="timeStart_old" value="{{$timeStart_old}}" />
      <input type="hidden" name="timeEnd_old" value="{{$timeEnd_old}}" />
      <div class="modal-body-upload" style="padding:20px;">
       

        <br/><br/>

         <h5 class='text-center'>The indicated work shift is incorrect.<br/><br/> <br/><strong class="text-danger">{{ $Dday }} {{ $DproductionDate }} {{ $data['shiftStart']}} - {!! $data['shiftEnd'] !!} </strong><br/>work schedule should be: </h5>

         <div class="row">

              <div class="col-sm-3"></div>
              <div class="col-sm-6">
              
                  <select name="timeEnd" class="end form-control" style="margin-bottom:5px"><option value="0">* Select shift *</option>';
                    @foreach ($shifts as $shift)
                       <option value="{{$shift}}">{{$shift}} </option>

                   @endforeach
                  </select>

              </div>
               <div class="col-sm-3"></div>
               
         </div>

        <div id="alert-upload" style="margin-top:10px"></div>
         <br/><br/><br/><br/><br/><br/> 
        
        
        
        <button type="button" class="btn btn-default btn-md pull-right " data-dismiss="modal"> <i class="fa fa-times"></i> Cancel</button>
        <button type="submit" id="upload" class="btn btn-success btn-md pull-right" style="margin-right:5px" > <i class="fa fa-paper-plane" ></i> Submit for Approval </button>

     
      </div> {{ Form::close() }}

      <div class="modal-body-generate"></div>
      <div class="modal-footer no-border">
        
      </div>
    </div>
  </div>
</div>