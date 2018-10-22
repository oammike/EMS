<div class="modal fade" id="myModal{{$modelID}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
          <h4 class="modal-title text-success" id="myModalLabel"><i class="fa fa-calendar"></i> {{$modalTitle}}</h4>
        
      </div> 
      <div class="modal-body-upload" style="padding:20px;">
       

        <br/><br/>
         <div class="row">

             
              <div class="col-sm-12">

                <div class="row">
                  
                  <div class="col-sm-4"><h4 class="text-primary">Production Date</h4></div>
                  @if ($notifs['typeID'] == 6)
                  <!-- CWS request -->
                  <div class="col-sm-4"><h4 class="text-primary">Old Schedule:</h4></div>
                  <div class="col-sm-4"><h4 class="text-primary">New Schedule</h4></div>
                  
                  @elseif ($notifs['typeID'] == 7) 
                  <!-- OT request -->
                  <div class="col-sm-4"><h4 class="text-primary">OT Details:</h4></div>
                  <div class="col-sm-4"><h4 class="text-primary">Reason:</h4></div>

                  @elseif ($notifs['typeID'] == 8)
                  <!-- DTRP IN request -->
                  <div class="col-sm-4"><h4 class="text-primary">Log IN Time</h4></div>
                  <div class="col-sm-4"><h4 class="text-primary">Notes</h4></div>

                  @elseif ($notifs['typeID'] == 9)
                  <!-- DTRP OUT request -->
                  <div class="col-sm-4"><h4 class="text-primary">Log OUT Time</h4></div>
                  <div class="col-sm-4"><h4 class="text-primary">Notes</h4></div>
                  @endif
                  
                </div>

                 <div class="row">
                  
                  <div class="col-sm-4" style="font-size: 12px">{{$notifs['productionDate']}}<br/> [<?php echo date('D', strtotime($notifs['productionDate'])) ?>]</div>
                  
                  <!-- IF CWS -->
                  @if ($notifs['typeID'] == 6)
                  <div class="col-sm-4" style="font-size: 12px">

                    @if ($notifs['deets']['timeStart_old'] === "00:00:00" && $notifs['deets']['timeEnd_old'] === "00:00:00")

                    <p>Shift: <br/><strong>Rest Day </strong></p>

                    @else


                    <p>Shift: <br/><strong><?php echo date('h:i A',strtotime($notifs['deets']['timeStart_old'])) ?> -  <?php echo date('h:i A',strtotime($notifs['deets']['timeEnd_old'])) ?> </strong></p>

                    @endif
                    
                  </div>
                  <div class="col-sm-4" style="font-size: 12px">

                    @if ($notifs['deets']['timeStart'] === "00:00:00" && $notifs['deets']['timeEnd'] === "00:00:00")

                    <p>Shift: <br/><strong>Rest Day </strong></p>

                    @else
                   <p>Shift: <br/><strong><?php echo date('h:i A',strtotime($notifs['deets']['timeStart'])) ?> -  <?php echo date('h:i A',strtotime($notifs['deets']['timeEnd'])) ?> </strong></p>

                   @endif
                  </div>

                 
                  <!-- IF OT -->
                  @elseif ($notifs['typeID'] == 7)
                 <div class="col-sm-4">
                    <p><strong>OT Start: </strong><?php echo date('h:i A',strtotime($notifs['deets']['timeStart'])) ?> <br/>
                       <strong>OT End : </strong><?php echo date('h:i A',strtotime($notifs['deets']['timeEnd'])) ?> <br/>
                       <strong>Billable Hours: </strong>{{ $notifs['deets']['billable_hours'] }} <br/>
                       <strong>Filed Hours worked: </strong>{{ $notifs['deets']['filed_hours'] }} </p>
                  </div>
                  <div class="col-sm-4" style="font-size: 12px">
                   <p>{{$notifs['deets']['reason']}}</p>
                  </div>

                  
                  <!-- IF DTRP LOGIN -->
                  @elseif ($notifs['typeID'] == 8)
                 <div class="col-sm-4">
                    <p><strong>{{$notifs['deets']['logTime']}} </strong></p>
                  </div>
                  <div class="col-sm-4">
                   <p>{{$notifs['deets']['notes']}} </p>
                  </div>

                  

                  <!-- if DTRP LOGOUT -->
                  @elseif ($notifs['typeID'] == 9)
                  <div class="col-sm-4">
                    <p><strong>{{$notifs['deets']['timeEnd']}} </strong></p>
                  </div>
                  <div class="col-sm-4">
                   <p>{{$notifs['deets']['notes']}} </p>
                  </div>

                  @endif
                  
                  <div class="col-sm-3">
                   

                  </div>
                </div>

               
              </div>

               
               
         </div>
 
        
        <button type="button" class="btn btn-default btn-md pull-right" data-dismiss="modal"style="margin-right:5px; margin-top:50px" > <i class="fa fa-times" ></i> Cancel </button>
       
        <a href="#" class="process btn btn-danger btn-md pull-right" data-notifType="{{$notifs['typeID']}}" data-action="0" data-notifID="{{$notifs['id']}}" data-id="{{$notifs['deets']['id']}}" data-dismiss="modal"style="margin-right:5px; margin-top:50px" > <i class="fa fa-thumbs-down" ></i> Deny </a>
        <a href="#" class="process btn btn-success btn-md pull-right" data-notifType="{{$notifs['typeID']}}" data-action="1" data-notifID="{{$notifs['id']}}" data-id="{{$notifs['deets']['id']}}" data-dismiss="modal"style="margin-right:5px; margin-top:50px" > <i class="fa fa-thumbs-up" ></i> Approve </a>

     
      </div> 
      <div class="modal-footer no-border">
        
      </div>
    </div>
  </div>
</div>