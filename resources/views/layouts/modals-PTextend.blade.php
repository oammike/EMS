<div class="modal fade" id="PTextend{{$modelID}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
          <h4 class="modal-title text-primary" id="myModalLabel"><i class="fa fa-clock-o"></i> Extend Part timer Work Schedule</h4>
        
      </div> {{ Form::open(['route' => 'user_dtr.extendPT','class'=>'col-lg-12' ]) }}
      <input type="hidden" name="biometrics_id" value="{{$biometrics_id}}" />
      <input type="hidden" name="productionDate" value="{{$DproductionDate}}" />
      <input type="hidden" name="user_id" value="{{$user->id}}" />
      <input type="hidden" name="isRD" value="{{$data['isRD']}}" />
      <input type="hidden" name="paystart" value="{{$paystart}}" />
      <input type="hidden" name="payend" value="{{$payend}}" />
      <input type="hidden" name="sStart" value="{{$sStart}}" />




      
     
      <input type="hidden" name="ptStart" value="{{ $DproductionDate }} {{$data['shiftStart']}}" />
      
      <div class="modal-body-upload" style="padding:20px;">
       

        <br/><br/>

      

         <div class="row">

              <div class="col-sm-3"></div>
              <div class="col-sm-6">
                <h5>Part timers by default are expected to render <strong class="text-danger"> 4 HOURS </strong>of work.</h5>
                <p>Current PT schedule: <br/><strong class="text-primary" >{{$DproductionDate}} <span class="text-orange"> {!! $sStart !!} - {!! $sEnd !!} </span></strong></p><br/>

                <label>Extend work schedule by :<input type="text" name="hrextend" placeholder="0.00" class="form-control" /> hour(s) more</label><br/><br/>
                <p><i class="fa fa-exclamation-circle"></i> Note: <em>For PT employees who'll be working on a full-time work schedule (8hrs), kindly coordinate first with HR and Finance to override current part time status and allow proper timekeeping computation. Thank you.</em> </p>
                        
                    
              </div>
              <div class="col-sm-3"></div>
               
         </div>

        <div id="alert-upload" style="margin-top:10px"></div>
         <br/><br/><br/><br/>
        
        
        
        <button type="button" class="btn btn-default btn-md pull-right " data-dismiss="modal"> <i class="fa fa-times"></i> Cancel</button>
        <button type="submit" class="btn btn-success btn-md pull-right" style="margin-right:5px" > <i class="fa fa-save" ></i> Save Changes </button>

     
      </div> {{ Form::close() }}

      <div class="modal-body-generate"></div>
      <div class="modal-footer no-border">
        
      </div>
    </div>
  </div>
</div>