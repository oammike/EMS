<div class="modal fade" id="myModal{{$modelID}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
          <h4 class="modal-title text-primary" id="myModalLabel"><i class="fa fa-clock-o"></i> File an Overtime</h4>
        
      </div> {{ Form::open(['route' => 'user_ot.store','class'=>'col-lg-12', 'id'=>'reportIssue', 'name'=>'reportIssue' ]) }}
      <input type="hidden" name="biometrics_id" value="{{$biometrics_id}}" />
      <input type="hidden" name="DproductionDate" value="{{$DproductionDate}}" />
      <input type="hidden" name="user_id" value="{{$user->id}}" />
      <input type="hidden" name="isRD" value="{{$data['isRD']}}" />
      <input type="hidden" name="approver" value="{{$approver}}" />
      <input type="hidden" name="billableHours" value="{{$data['billableForOT']}}" />
      @if($data['shiftEnd'] == "* RD *")
      <input type="hidden" name="OTstart" value="{{$data['logIN']}}" />
      @else
      <input type="hidden" name="OTstart" value="{{$data['shiftEnd']}}" />
      @endif
      <input type="hidden" name="OTend" value="{{$data['logOUT']}}" />
      <div class="modal-body-upload" style="padding:20px;">
       

        <br/><br/>

         <h5 class='text-center'>File an OT for <br/><strong class="text-danger">
          {{ $Dday }} {{ $DproductionDate }} {{$data['shiftEnd']}} <?php if ($data['shiftEnd'] == "* RD *") { ?><br/> <br/>{{$data['logIN']}} <?php } ?> - {{$data['logOUT']}}</strong><br/><br/><i class="fa fa-clock-o"></i> Total Hours worked: </h5>

         <div class="row">

              <div class="col-sm-3"></div>
              <div class="col-sm-6">
                      

                        <select name="filedHours" class="othrs form-control">
                          <option value="{{$data['billableForOT']}}" data-timeend="{{$data['logOUT']}}" data-timestart="@if($data['shiftEnd'] == '* RD *'){{$data['logIN']}}@else{{$data['shiftEnd']}}@endif" selected="selected">{{$data['billableForOT']}}</option>
                          <?php 
                          

                          for ($i=$data['billableForOT']; $i >= 0.1; $i=$i-0.5 )
                          { 
                              $num = (round($i/5,1, PHP_ROUND_HALF_DOWN))*5; 
                               if ( strpos($data['shiftEnd'], "RD") ){ $start = Carbon\Carbon::parse($data['logIN']); $t1 = Carbon\Carbon::parse($data['logIN']); } 
                           else {$start= Carbon\Carbon::parse($data['shiftEnd']); $t1 = Carbon\Carbon::parse($data['shiftEnd']); }
                           
                              ?>

                          @if ($num < $data['billableForOT'])    
                          <option data-timestart="{{$start->format('h:i A')}}" data-timeend="{{$data['logOUT']}}"" value="{{$num}}"> &nbsp;&nbsp;{{$num}} hr. OT</option><!--  data-timeend="{{$t1->addMinutes($num*60)->format('h:i A')}}" --><!-- [{{$start->format('h:i A')}} - {{$t1->format('h:i A')}}] -->
                          @endif

                          <?php } ?> 
                        </select>
                        <p style="line-height:0.9em"><br/><br/><i class="fa fa-question-circle"></i> Reason for OT: <br/></p>
                        <textarea class="form-control" name="reason"></textarea>
                        <p style="line-height:0.9em"><br/><small ><i class="fa fa-info"></i> <em> Please give a brief description of the task that you worked on for this OT.</em> </small></p>
                        
                    
                </div>
               <div class="col-sm-3"></div>
               
         </div>

        <div id="alert-upload" style="margin-top:10px"></div>
         <br/><br/><br/><br/>
        
        
        
        <button type="button" class="btn btn-default btn-md pull-right " data-dismiss="modal"> <i class="fa fa-times"></i> Cancel</button>
        <button type="submit" id="upload" class="btn btn-success btn-md pull-right" style="margin-right:5px" > <i class="fa fa-paper-plane" ></i> Submit for Approval </button>

     
      </div> {{ Form::close() }}

      <div class="modal-body-generate"></div>
      <div class="modal-footer no-border">
        
      </div>
    </div>
  </div>
</div>