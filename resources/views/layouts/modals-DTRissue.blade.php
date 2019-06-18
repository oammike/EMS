<div class="modal fade text-left" id="myModal{{$modelID}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
          <h4 class="modal-title text-primary" id="myModalLabel"><i class="fa fa-bullhorn"></i> Report A DTR Problem</h4><p>Production Date:<strong> {{ $Dday }} {{ $DproductionDate }}</strong></p>

          <p><br/>Inform {!! $DTRapprovers !!} that: </p>
        
      </div> 
      <!-- ******* manage() function on: DTRController@manage *********** -->
      {{ Form::open(['route' => 'user_dtr.manage','class'=>'col-lg-12', 'id'=>'reportIssue', 'name'=>'reportIssue' ]) }}
      <input type="hidden" name="biometrics_id" value="{{$biometrics_id}}" />
      <input type="hidden" name="DproductionDate" value="{{$DproductionDate}}" />
      <input type="hidden" name="user_id" value="{{$user->id}}" />
      <input type="hidden" name="isRD" value="{{$isRD}}" />
      <input type="hidden" name="approver" value="{{$approver}}" />
      <input type="hidden" name="TLapprover" value="{{$TLapprover}}" />
      <input type="hidden" name="timeStart_old" value="{{$timeStart_old}}" />
      <input type="hidden" name="timeEnd_old" value="{{$timeEnd_old}}" />
      <div class="modal-body-upload" style="padding: 0px 20px;">

        @if (!is_null($data['shiftStart']) || !is_null($data['shiftEnd']) )

       <label class="form-control"> <input type="checkbox" name="issue[]" id="workshift"  value="1" /> The indicated <span class="text-danger">work shift</span> is incorrect. </label>

       <div class="container" id="workshiftOptions" style="width: 90%; padding-bottom: 30px">

            <div class="row">
              <div class="col-sm-12">
                <i class="fa fa-2x fa-clock-o"></i><h5 class='text-center'><strong class="text-primary">{{ $Dday }} {{ $DproductionDate }} {{ $data['shiftStart']}} - {!! $data['shiftEnd'] !!} </strong><br/>work schedule should be: </h5>
              </div>
            </div>
          

             <div class="row">

                  <div class="col-sm-6">
                    <div style="margin-top:30px">
                      <label><input type="radio" name="shifttype" value="full" required="required" /> Full Time </label> &nbsp;&nbsp;&nbsp;
                      
                    </div>  

                    <select id="fulltimes" name="timeEnd1" class="end form-control" style="margin-top:20px"><option value="">* Select shift *</option>';
                        @if ($data['shiftStart']  !== "* RD *")
                        <option value="RD"> REST DAY </option>';
                        @endif

                        @foreach ($shifts as $shift)
                           <option value="{{$shift}}">{{$shift}} </option>

                       @endforeach
                      </select><p></p>

                  </div>

                  <div class="col-sm-6">
                    <div style="margin-top:30px">
                      <label><input type="radio" name="shifttype" value="part" required="required" /> Part Time </label> &nbsp;&nbsp;&nbsp;
                      
                    </div>

                      <select id="parttimes" name="timeEnd2" class="end form-control" style="margin-top:20px"><option value="">* Select shift *</option>';
                        @if ($data['shiftStart']  !== "* RD *")
                        <option value="RD"> REST DAY </option>';
                        @endif

                        @foreach ($partTimes as $shift)
                           <option value="{{$shift}}">{{$shift}} </option>

                       @endforeach
                      </select><p></p>

                      <input type="hidden" name="timeEnd" value="0" />

                      

                     
                  </div>
                 
                   
             </div>
              <div class="row">
                        <div class="col-sm-12 text-left"><label>Reason: </label>
                          <textarea name="cwsnote" class="form-control"></textarea></div>
                        
                      </div>
       </div>
       @endif

       <label class="form-control"><input type="checkbox" name="issue[]" id="login" value="2" /> There's a problem with my <span class="text-danger">LOG IN </span>time.</label>

       <div class="container" id="login" style="width: 90%; padding-bottom: 30px">
          

             <div class="row">

                  <div class="col-sm-6">  <i class="fa fa-2x fa-sign-in"></i><h5 class='text-center'><strong class="text-primary">LOG IN time for</strong><br/>{{ $Dday }} {{ $DproductionDate }} should be: </h5></div>
                  <div class="col-sm-6">
                    
                  <input class="timepick form-control" type="text" name="login" placeholder="HH:mm" style="margin-top: 50px" />
                  <!--  <div class="row" style="padding-top: 30px">
                    <div class="col-sm-4">
                      <select name="hour" class="form-control">
                        <option value="0">HH</option>
                         @for($h=1; $h<=12; $h++)
                         <option value="{{$h}}">{{$h}}</option>
                         @endfor

                       </select>
                      
                    </div>

                    <div class="col-sm-4">
                      <select name="min" class="form-control">
                        <option value="0">MM</option>
                         @for($h=1; $h<=60; $h++)
                         <option value="{{$h}}">{{$h}}</option>
                         @endfor

                       </select>
                      
                    </div>
                    <div class="col-sm-4">
                      <label><input type="radio" name="am" value="AM" /> AM</label>
                      <label><input type="radio" name="am" value="PM" /> PM</label>
                    </div>
                   </div> -->
                   
                    <p></p><p></p>
                  </div>
            </div>
            <div class="row">
              <div class="col-sm-6 text-right"><label>Reason: </label></div>
              <div class="col-sm-6"><textarea name="loginReason" class="form-control"></textarea></div>
            </div>
       </div>


       <label class="form-control"> <input type="checkbox" name="issue[]" id="logout"  value="3" /> There's a problem with my <span class="text-danger">LOG OUT </span>time.</label>

       <div class="container" id="logout" style="width: 90%; padding-bottom: 30px">
          

             <div class="row">

                  <div class="col-sm-6">  <i class="fa fa-2x fa-sign-out"></i> <h5 class='text-center'><strong class="text-primary">LOG OUT time for </strong><br/>{{ $Dday }} {{ $DproductionDate }} should be: </h5></div>
                  <div class="col-sm-6">
                    <input class="timepick form-control" type="text" name="logout" placeholder="HH:mm" style="margin-top: 50px"/><p></p><p></p>
                  </div>
             </div>

             <div class="row">
              <div class="col-sm-6 text-right"><label>Reason: </label></div>
              <div class="col-sm-6"><textarea name="logoutReason" class="form-control"></textarea></div>
            </div>


       </div>


       <label class="form-control"> <input type="checkbox" name="issue[]" id="leave"  value="4" /> This should be an <span class="text-danger">SL | VL | LWOP | OBT </span>.</label>

       <div class="container" id="leave" style="width: 90%; padding-bottom: 30px">
           <div id="leaveheader"><h5 class='text-center'>Select type of leave for <br/> {{ $Dday }} {{ $DproductionDate }}  </h5></div>

             <div class="row">

                  
                  <div class="col-sm-12">
                    <p class="text-left"><br/><br/>
                      <strong>

                        @if($entitledForLeaves)

                            @if($anApprover && Auth::user()->id != $user->id)
                          <a href="{{action('UserSLController@create',['from'=>$DproductionDate, 'for'=>$user->id])}}" style="margin-bottom: 5px">
                            @else
                            <a href="{{action('UserSLController@create',['from'=>$DproductionDate])}}" style="margin-bottom: 5px">
                              @endif

                          <i class="fa fa-2x fa-stethoscope"></i>&nbsp;&nbsp;&nbsp; Sick Leave <strong>(SL)</strong></a><br/><br/>



                            @if($anApprover && Auth::user()->id != $user->id)
                            <a style="margin-bottom: 5px" href="{{action('UserVLController@create',['from'=>$DproductionDate, 'for'=>$user->id])}}"><i class="fa fa-2x fa-plane"></i> &nbsp;&nbsp;&nbsp;Vacation Leave <strong>(VL)</strong></a><br/><br/>

                            @else
                            <a style="margin-bottom: 5px" href="{{action('UserVLController@create',['from'=>$DproductionDate])}}"><i class="fa fa-2x fa-plane"></i> &nbsp;&nbsp;&nbsp;Vacation Leave <strong>(VL)</strong></a><br/><br/>

                            @endif

                        @endif


                        @if($anApprover && Auth::user()->id != $user->id)
                        <a href="{{action('UserLWOPController@create',['from'=>$DproductionDate, 'for'=>$user->id])}}" style="margin-bottom: 5px"><i class="fa fa-meh-o fa-2x"></i>&nbsp;&nbsp;&nbsp; Leave Without Pay  <strong>(LWOP)</strong></a><br/><br/>

                        @else
                        <a href="{{action('UserLWOPController@create',['from'=>$DproductionDate])}}" style="margin-bottom: 5px"><i class="fa fa-meh-o fa-2x"></i>&nbsp;&nbsp;&nbsp; Leave Without Pay  <strong>(LWOP)</strong></a><br/><br/>
                        @endif



                        @if($anApprover && Auth::user()->id != $user->id)
                        <a href="{{action('UserOBTController@create',['from'=>$DproductionDate, 'for'=>$user->id])}}"  style="margin-bottom: 5px"><i class="fa fa-2x fa-briefcase"></i>&nbsp;&nbsp; Official Business Trip  <strong>(OBT)</strong></a></strong>

                        @else
                         <a href="{{action('UserOBTController@create',['from'=>$DproductionDate])}}"  style="margin-bottom: 5px"><i class="fa fa-2x fa-briefcase"></i>&nbsp;&nbsp; Official Business Trip  <strong>(OBT)</strong></a></strong><br/><br/>


                        @endif


                        @if($anApprover && Auth::user()->id != $user->id)
                        <a href="{{action('UserFamilyleaveController@create',['from'=>$DproductionDate, 'for'=>$user->id,'type'=>'ML'])}}"  style="margin-bottom: 5px"><i class="fa fa-2x fa-female"></i>&nbsp;&nbsp; Maternity Leave  <strong>(ML)</strong></a></strong><br/><br/>

                        @else
                         <a href="{{action('UserFamilyleaveController@create',['from'=>$DproductionDate, 'type'=>'ML'])}}"  style="margin-bottom: 5px"><i class="fa fa-2x fa-female"></i>&nbsp;&nbsp; Maternity Leave  <strong>(ML)</strong></a></strong><br/><br/>


                        @endif


                        @if($anApprover && Auth::user()->id != $user->id)
                        <a href="{{action('UserFamilyleaveController@create',['from'=>$DproductionDate, 'for'=>$user->id,'type'=>'PL'])}}"  style="margin-bottom: 5px"><i class="fa fa-2x fa-male"></i>&nbsp;&nbsp; Paternity Leave  <strong>(PL)</strong></a></strong><br/><br/>

                        @else
                         <a href="{{action('UserFamilyleaveController@create',['from'=>$DproductionDate,'type'=>'PL'])}}"  style="margin-bottom: 5px"><i class="fa fa-2x fa-male"></i>&nbsp;&nbsp; Paternity Leave  <strong>(PL)</strong></a></strong><br/><br/>


                        @endif


                        @if($anApprover && Auth::user()->id != $user->id)
                        <a href="{{action('UserFamilyleaveController@create',['from'=>$DproductionDate, 'for'=>$user->id,'type'=>'SPL'])}}"  style="margin-bottom: 5px"><i class="fa fa-2x fa-street-view"></i>&nbsp;&nbsp; Single-Parent Leave  <strong>(SPL)</strong></a></strong><br/><br/>

                        @else
                         <a href="{{action('UserFamilyleaveController@create',['from'=>$DproductionDate,'type'=>'SPL'])}}"  style="margin-bottom: 5px"><i class="fa fa-2x fa-street-view"></i>&nbsp;&nbsp; Single-Parent Leave  <strong>(SPL)</strong></a></strong><br/><br/>


                        @endif


                    </p>
                  <!-- <label> <input type="radio" name="leave" data-productionDate="{{ $DproductionDate }}" value="vl" id="vl"/>&nbsp;&nbsp; <i class="fa fa-plane"></i> Vacation Leave </label><BR/>
                     <label> <input type="radio" name="leave"  data-productionDate="{{ $DproductionDate }}" value="sl" id="sl"/>&nbsp;&nbsp; <i class="fa fa-stethoscope"></i> Sick Leave </label><br/>
                     <label> <input type="radio" name="leave" data-productionDate="{{ $DproductionDate }}" value="lwop" id="lwop"/>&nbsp;&nbsp;  <i class="fa fa-meh-o"></i> Leave without Pay </label><br/>
                     <label> <input type="radio" name="leave" data-productionDate="{{ $DproductionDate }}" value="obt" id="ob"/>&nbsp;&nbsp; <i class="fa fa-suitcase"></i> Official Business Trip </label><p></p><p></p> -->
                     

                  </div>
                  
                  
                   
             </div>
       </div>
       
       

        <br/><br/>

         
        <div id="alert-upload" style="margin-top:10px"></div>
        
       <!--  <input type="hidden" name="leaveStart" />
        <input type="hidden" name="leaveEnd" />
        <input type="hidden" name="totalCredits" />
        <input type="hidden" name="halfdayFrom"/>
        <input type="hidden" name="halfdayTo" /> -->
        <button type="button" class="btn btn-default btn-md pull-right " data-dismiss="modal"> <i class="fa fa-times"></i> Cancel</button>
        <button disabled="disabled" type="submit" id="upload" class="submit btn btn-primary btn-md pull-right" style="margin-right:5px" > <i class="fa fa-paper-plane" ></i> Submit DTRP </button>

     
      </div> {{ Form::close() }}

      <div class="modal-body-generate"></div>
      <div class="modal-footer no-border">
        
      </div>
    </div>
  </div>
</div>

