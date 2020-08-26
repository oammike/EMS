<div class="modal fade" id="myModal_addVLearning{{$modelID}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
     <div class="modal-content">
      <div class="modal-header">
        
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
          <h4 class="modal-title text-primary" id="myModalLabel"> {{$modalTitle}} {{$modelName}}</h4>
        
      </div>
      <div class="modal-body">{{ Form::open(['route' => [$modelRoute, $modelID], 'method'=>$modalTitle, 'id'=> $formID ]) }} 
        <input type="hidden" name="user_id" value="{{$personnel->id}}" />
        <div class="row">
          <div class="col-lg-2"></div>
          <div class="col-lg-4 text-default"><p>Period: </p>
          </div>

          <div class="col-lg-4">
            <?php $today = date('Y'); ?>
            <select class="form-control" name="periodVLearn" id="periodVLearn">
              <option value="0">* select date *</option>
              @foreach($vlUpdates_periods as $p)
              
              
              <option value="{{$p}}">{{date('Y M d', strtotime($p))}}</option>
             

            @endforeach
            </select>
          </div>
          <div class="col-lg-2">&nbsp;</div>
        </div>

         <div class="row" style="padding-top: 20px">
          <div class="col-lg-2 text-center"><i class="fa fa-plane fa-4x"></i></div>
          <div class="col-lg-5 text-default">Choose Leave Earnings:</div> 
          <div class="col-lg-3" id="periods">
            @foreach($vlUpdates as $u)

            <label class="p_{{$u->period}}" style="display: none"><input type="radio" value="{{$u->id}}" name="vlupdate_id" /> &nbsp;{{$u->credits}}&nbsp;&nbsp; </label>

            @endforeach
           
           </div>
          <div class="col-lg-2">&nbsp;</div>
        </div>

       



       <!--  <div class="row">
          <div class="col-lg-6">Total Paid:</div> 
          <div class="col-lg-6"><input class="form-control" type="text" name="paid" placeholder="0.00" /></div>
        </div> -->

        <input type="hidden" name="user_id" value="{{$personnel->id}}" />
        <div class="pull-right" style="padding-top: 20px">
          <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          

        </div>
        
        {{ Form::close() }}
        
      </div>
      <div class="modal-footer no-border">
            
          
      </div>
    </div>
  </div>
</div>