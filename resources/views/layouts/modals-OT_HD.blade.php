<div class="modal fade" id="myModal{{$modelID}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
          <h4 class="modal-title text-primary" id="myModalLabel"><i class="fa fa-clock-o"></i> File an HD Overtime</h4>
        
      </div> {{ Form::open(['route' => 'user_ot.store','class'=>'col-lg-12', 'id'=>'reportIssue', 'name'=>'reportIssue' ]) }}
      <input type="hidden" name="biometrics_id" value="{{$biometrics_id}}" />
      <input type="hidden" name="DproductionDate" value="{{$DproductionDate}}" />
      <input type="hidden" name="user_id" value="{{$user->id}}" />
      <input type="hidden" name="isRD" value="{{$data['isRD']}}" />
      <input type="hidden" name="approver" value="{{$approver}}" />
      <input type="hidden" name="billableHours" value="{{$data['billableForOT']}}" />
      @if($data['shiftEnd'] == "* RD *")
      <input type="hidden" name="fromRD" value="1" />
      <input type="hidden" name="OTstart" value="{{$data['logIN']}}" />
      @else
      <input type="hidden" name="OTstart" value="{{$data['logIN']}}" />
      @endif
      <input type="hidden" name="OTend" value="{{$data['logOUT']}}" />
      <div id="otmodal" class="modal-body-upload" style="padding:20px;">
       

        <br/><br/>

         <h5 class='text-center'>File a Holiday OT for <br/><strong class="text-danger">
          {{$data['logIN']}} <?php if ($data['shiftEnd'] == "* RD *") { ?><br/> <br/>{{$data['logIN']}} <?php } ?> - {{$data['logOUT']}}</strong><br/><br/><i class="fa fa-clock-o"></i> Total Hours worked: </h5>

         <div class="row">

              <div class="col-sm-3"></div>
              <div class="col-sm-6">
                      

                        <select name="filedHours" id="filedHours" class="othrs form-control">
                          <option value="0" selected="selected">Indicate total hours you worked</option>
                          <option value="{{$data['billableForOT']}}" data-timeend="{{$data['logOUT']}}" data-timestart="@if($data['shiftEnd'] == '* RD *'){{$data['logIN']}}@else {{$data['logIN']}} @endif" >{{$data['billableForOT']}} hr. OT</option>

                          <!-- gawin mo lang if within OT limits -->

                         
                          <?php 

                          //check mo muna kung divisible by 5, otherwise round it off first
                          $distOT = (float)$data['billableForOT']/0.25; //hatiin mo sya in 15/60 mins
                          $preventDupes = [];
                          $fractions = [];

                            for( $d=$distOT; $d >0; $d=$d-0.25)
                            {
                              $num = $d;// round($d,1,PHP_ROUND_HALF_DOWN);

                              $whole = floor($num);
                              $fraction = $num - $whole;
                              array_push($fractions, $fraction);

                              if($fraction <= 0.9 && $fraction >= 0.7)
                                $num = $whole + 0.75;
                              else if($fraction < 0.7 && $fraction >= 0.5)
                                $num = $whole + 0.50;
                              else if($fraction <= 0.4 && $fraction > 0.2)
                                $num = $whole + 0.25;
                              else
                                $num = number_format($whole,1); // - $fraction;

                              if (in_array($num, $preventDupes)){ }
                              else
                              {
                                array_push($preventDupes, $num);
                                if ( strpos($data['shiftEnd'], "RD") )
                                {
                                  $start = \Carbon\Carbon::parse($data['logIN'],'Asia/Manila'); 
                                  $t1 = \Carbon\Carbon::parse($data['logIN'],'Asia/Manila'); 
                                  $endOT = \Carbon\Carbon::parse($start->format('H:i'),'Asia/Manila')->addMinutes($num*60); 
                                } 
                                else 
                                {
                                  $start= \Carbon\Carbon::parse($data['logIN'],'Asia/Manila'); 
                                  $t1 = \Carbon\Carbon::parse($data['logIN'],'Asia/Manila'); 
                                  $endOT = \Carbon\Carbon::parse($data['logIN'],'Asia/Manila')->addMinutes($num*60); 
                                } 
                                ?>

                                @if ( $num < (float)$data['billableForOT'] && $num != '0')  
                                <option data-proddate="{{ $DproductionDate }}" data-timestart="{{$start->format('M d,Y h:i A')}}" data-timeend="{{$endOT->format('M d,Y h:i A')}}"  value="{{$num}}"> &nbsp;&nbsp;{{$num}} hr. OT [ {{$endOT->format('h:i A')}} ] </option>
                                @endif
                        <?php }      

                            } //end for ?>

                             

                         
                          
                        </select>

                        <br/><br/>
                         <label ><input type="radio" name="billedtype" id="shift_whole" value="1" checked="checked" />&nbsp; &nbsp;<i class="fa fa-hourglass"></i> Billed to Client</label>
                       
                        <br/>
                        <label id="shift_first" ><input type="radio" name="billedtype" id="shift_first" value="2" />&nbsp; &nbsp;<i class="fa fa-hourglass-start"></i> Non-Billed  <em style="font-size: x-small; font-weight: normal;">(billed to Open Access BPO)</em><span id="shiftFrom_1"></span> </label>
                         <br/>
                        <label ><input type="radio" name="billedtype" id="shift_second" value="3" />&nbsp; &nbsp;<i class="fa fa-hourglass-end"></i> Patch <span id="shiftFrom_2"></span> </label><br/>



                        <p style="line-height:0.9em"><br/><br/><i class="fa fa-question-circle"></i> Reason for OT: <br/></p>
                        <textarea class="form-control" name="reason"></textarea>
                        <p style="line-height:0.9em"><br/><small ><i class="fa fa-info"></i> <em> Please give a brief description of the task that you worked on for this OT.</em> </small></p>
                        
                    
                </div>
               <div class="col-sm-3"></div>
               
         </div>

        <div id="alert-upload" style="margin-top:10px"></div>
         <br/><br/><br/><br/>
        
        
        
        <button type="button" class="btn btn-default btn-md pull-right " data-dismiss="modal"> <i class="fa fa-times"></i> Cancel</button>
        <button type="submit" id="uploadOT" class="btn btn-success btn-md pull-right" style="margin-right:5px" > <i class="fa fa-paper-plane" ></i> Submit for Approval </button>

     
      </div> {{ Form::close() }}

      <div class="modal-body-generate"></div>
      <div class="modal-footer no-border">
        
      </div>
    </div>
  </div>
</div>