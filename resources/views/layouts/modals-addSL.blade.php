<div class="modal fade" id="myModal_addSL{{$modelID}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
     <div class="modal-content">
      <div class="modal-header">
        
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
          <h4 class="modal-title text-primary" id="myModalLabel"> {{$modalTitle}} {{$modelName}}</h4>
        
      </div>
      <div class="modal-body">{{ Form::open(['route' => [$modelRoute, $modelID], 'method'=>$modalTitle, 'id'=> $formID ]) }} 

        <div class="row">
          <div class="col-lg-2"></div>
          <div class="col-lg-5 text-default"><p>Year: </p>
          </div>

          <div class="col-lg-3">
            <?php $today = date('Y'); ?>
            <select class="form-control" name="creditYear" id="creditYear">
              <!-- <option value="0">* select year *</option> -->
              @while ($today >= $fromYr)
              <?php  $existingYears = $personnel->slCredits->pluck('creditYear')->all(); ?>

              @if (!in_array($today,$existingYears))
              <option value="{{$today}}">{{$today}}</option>
              @endif
              
              <?php $today--; ?>

            @endwhile
            </select>
          </div>
          <div class="col-lg-2">&nbsp;</div>
        </div>

         <div class="row">
          <div class="col-lg-2 text-center"><i class="fa fa-stethoscope fa-4x"></i></div>
          <div class="col-lg-5 text-default">Updated as of:</div> 
          <div class="col-lg-3"><input required type="text" class="form-control datepicker" name="lastUpdated" id="lastUpdated" placeholder="MM/DD/YYYY" /></div>
          <div class="col-lg-2">&nbsp;</div>
        </div>

        <div class="row">
          <div class="col-lg-2 text-center"></div>
          <div class="col-lg-5 text-default">Beginning balance:</div> 
          <div class="col-lg-3"><input style="margin-top: 5px" class="form-control" required="required" type="text" name="beginBalance" placeholder="0.00" /></div>
          <div class="col-lg-2">&nbsp;</div>
        </div>

        <div class="row">
          <div class="col-lg-2">&nbsp;</div>
          <div class="col-lg-5 text-default">Total Used:</div> 
          <div class="col-lg-3"><input style="margin-top: 5px"  class="form-control" required="required" type="text" name="used" placeholder="0.00" /></div>
          <div class="col-lg-2">&nbsp;</div>
        </div>

        <div class="row">
          <div class="col-lg-2">&nbsp;</div>
          <div class="col-lg-5 text-default">Total Paid <br/>(SL Conversion):</div> 
          <div class="col-lg-3"><input style="margin-top: 5px"  class="form-control"  type="text" name="paid" placeholder="0.00" /></div>
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