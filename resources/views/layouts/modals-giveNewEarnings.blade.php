<div class="modal fade" id="myModal_giveNewEearning{{$modelID}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
     <div class="modal-content">
      <div class="modal-header">
        
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
          <h4 class="modal-title text-primary" id="myModalLabel"> <i class="fa fa-line-chart"></i> {{$modalTitle}} {{$modelName}}</h4>
        
      </div>
      <div class="modal-body">{{ Form::open(['route' => [$modelRoute, $modelID], 'method'=>$modalTitle, 'id'=> $formID ]) }} 
        <input type="hidden" name="user_id" value="1" />
        <div class="row">
          <div class="col-lg-2"></div>
          <div class="col-lg-4 text-default"><p>Period: </p>
          </div>

          <div class="col-lg-4">
            <?php $today = date('Y'); ?>
            <select class="form-control" name="periodEarning" id="periodEarning">
              <option value="0">* select date *</option>
              @foreach($update_periods as $up)
               <option value="{{$up}}">{{date('Y M d', strtotime($up))}}</option>

              @endforeach</select>
           
          </div>
          <div class="col-lg-2">&nbsp;</div>
        </div>
        <div class="row">
          <div class="col-lg-2"></div>
          <div class="col-lg-4 text-default"><p>Credits to Earn: </p>
          </div>

          <div class="col-lg-4">
            <?php $today = date('Y'); ?>
            <input type="text" name="newearning" class="form-control" placeholder="no. of credits" />
          </div>
          <div class="col-lg-2">&nbsp;</div>
        </div>

         <div class="row" style="padding-top: 20px">
         
          <div class="col-lg-2 text-default">Current VL Earnings:</div> 
          <div class="col-lg-10" id="periods">
            <table class="table">
              <thead>
                <th>Period</th>
                <th>Credits</th>
              </thead>

              <tbody>
                @foreach($update_credits as $u)

                <tr>
                  <td>{{date('Y-M-d', strtotime($u[0]->period))}}</td>
                  <td>@foreach($u as $p)
                  <strong style="font-size: smaller;">{{$p->credits}}</strong> | 
                  @endforeach</td>
                </tr>
                @endforeach
              </tbody>
            </table>
           </div>
          
        </div>

        <input type="hidden" name="user_id" value="1" />
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