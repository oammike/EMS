<div class="modal fade" id="myModal{{$modelID}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
     <div class="modal-content">
      <div class="modal-header">
        
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
          <h4 class="modal-title" id="myModalLabel"> {{$modalTitle}} {{$modelName}}</h4>
        
      </div>
      <div class="modal-body">{{ Form::open(['route' => [$modelRoute, $modelID], 'method'=>$modalTitle,'class'=>'btn-outline', 'id'=> $formID ]) }} 
        <input type="hidden" name="user_id" value="{{$personnel->id}}" />
   
        <p style="color: #000">Choose one or more leaders:</p>
        <?php $campLeads = $leaders->where('campaign',$currentTLcamp[0]->name);?>
        <table class="no-border">
          <tr>
            <?php if (count($teamMates) < 1){ ?>

            <td valign="top" class="text-left">
              @foreach($campLeads as $lead)
          <label class="text-primary"><input type="checkbox" value="{{$lead['id']}}" name="leader[]" <?php if( in_array($lead['id'], $approvers->pluck('id')->toArray())) { ?>checked="checked" <?php } ?> /> {{ strtoupper($lead['lastname'])}}, {{ $lead['firstname']}}</label> <br/>
        @endforeach
              

            </td>

            <?php } 
            else { ?>
            <td width="52%" valign="top">
              @foreach($campLeads as $lead)
                <label class="text-primary"><input type="checkbox" value="{{$lead['id']}}" name="leader[]" <?php if( in_array($lead['id'], $approvers->pluck('id')->toArray())) { ?>checked="checked" <?php } ?> /> {{ strtoupper($lead['lastname'])}}, {{ $lead['firstname']}}</label> <br/>
              @endforeach
              

            </td>
            <td valign="top" style="border-left: dotted 1px #333; padding-left: 15px" width="48%">
              
              <h5 align="center" class="text-danger"><label><input value="yes" type="checkbox" name="applySame"> -- Apply same approver(s) to the following employee(s)</label> <br/><br/><br/></h5>
              @foreach($teamMates as $team)
                <input type="hidden" name="teammates[]" value="{{$team['id']}}" />
                 <label style="margin-left:20px; width:90%; font-size:0.7em;border-bottom:1px dotted #777; color: #000 "><a href="{{action('UserController@show',$team['id'])}}" target="_blank"><img class="img-circle pull-left" width="50" src="{{$team['pic']}}" /></a>&nbsp;&nbsp; {{$team['lastname']}}, {{$team['firstname']}}<br/><small style="font-size:0.75em;">{{$team['position']}}</small> </label>

              @endforeach
             
            </td>
            <?php } ?>
          </tr>
        </table>
        
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