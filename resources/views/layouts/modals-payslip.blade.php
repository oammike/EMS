<div class="modal fade" id="myModal{{$modelID}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
     <div class="modal-content">
      <div class="modal-header">
        
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
          <h4 class="modal-title" id="myModalLabel"> {{$modalTitle}} {{$modelName}}</h4>
        
      </div>
      <div class="modal-body">{{ Form::open(['route' => [$modelRoute, $modelID], 'method'=>$modalTitle,'class'=>'btn-outline', 'id'=> $formID ]) }} 
        <input type="hidden" name="user_id" value=" " />
   
        <p style="color: #000"></p>

        
        

        <label class="text-primary pull-right"> Daily Rate: PHP<input class="form-control" type="text" name="rate" id="rate" placeholder="Php {{$rate}}" value="{{$rate}}" /></label>
        
        <label class="pull-left text-primary">Payroll Cutoff</label>
        <select name="cutoff" id="cutoff" class="form-control pull-left" style="width: 50%;margin:10px">
                <option value="0">Select cutoff period</option>
                @foreach($paycutoffs as $p)
                <option value="{{$p}}">{{date('M d, Y', strtotime($p->fromDate))}} -  {{date('M d, Y', strtotime($p->toDate))}}</option>
                @endforeach
                </select>

        
        
        <div class="pull-right" style="padding-top: 20px">
          <button type="submit" class="btn btn-primary">Save</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          

        </div>
        
        {{ Form::close() }}
        
      </div>
      <div class="modal-footer no-border">
            
          
      </div>
    </div>
  </div>
</div>