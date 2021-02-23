<div class="modal fade" id="myModal_dtrpDetail{{$modelID}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
          <h4 class="modal-title text-primary" id="myModalLabel"><i class="fa fa-calendar"></i> {{$modalTitle}}</h4>
        
      </div> 
      <div class="modal-body-upload" style="padding:20px;">
       
       @if($extra)
       <p class="text-danger" style="font-style: italic; font-size: small;"><i class="fa fa-exclamation-triangle"></i> {{$extra}} </p>
       @endif

        <br/><br/>
         <div class="row">

             
              <div class="col-sm-12">

                <div class="row">
                  
                  <div class="col-sm-4"><h5 class="text-primary">Production Date</h5></div>

                  <?php $dtrp =  OAMPI_Eval\User_DTRP::find($modelID); ?>
                  

                  @if ($dtrp->logType_id == 1)
                  <!-- DTRP IN request -->
                  <div class="col-sm-4"><h5 class="text-primary">Log IN Time</h5></div>
                  <div class="col-sm-4"><h5 class="text-primary">Notes</h5></div>

                  @elseif ($dtrp->logType_id  == 2)
                  <!-- DTRP OUT request -->
                  <div class="col-sm-4"><h5 class="text-primary">Log OUT Time</h5></div>
                  <div class="col-sm-4"><h5 class="text-primary">Notes</h5></div>
                  @endif
                  
                </div>

                 <div class="row">
                  
                  <div class="col-sm-4">{{$data['productionDate']}}<br/> [<?php echo date('D', strtotime($data['productionDate'])) ?>]<br/>
                    <small><em>Filed: {{$dtrp->created_at->format('m/d/y')}} </em></small></div>
                  
                  

                  
                  <!-- IF DTRP LOGIN -->
                   @if ($dtrp->logType_id == 1)
                 <div class="col-sm-4">
                    <p><strong>{{date('h:i:s A', strtotime($dtrp->logTime))  }} </strong></p>
                  </div>
                  <div class="col-sm-4">
                   <p> {{$dtrp->notes}} </p>
                 </div>

                  

                  <!-- if DTRP LOGOUT -->
                  @elseif ($dtrp->logType_id  == 2)
                  <div class="col-sm-4">
                    <p><strong>{{date('h:i:s A', strtotime($dtrp->logTime))  }} <!-- $data['logOUT'] --> </strong></p>
                  </div>
                  <div class="col-sm-4">
                   <p>{{$dtrp->notes}} </p>
                  </div>

                  @endif
                  
                  <div class="col-sm-3">
                   

                  </div>
                </div>

               
              </div>

               
               
         </div>
        
        <a style="margin-left:5px; margin-top:50px" class="btn btn-sm btn-primary" href="{{action('UserController@myRequests',$user->id)}}"><i class="fa fa-clipboard"></i> View All Requests</a>
       <button type="button" class="btn btn-default btn-sm pull-right" data-dismiss="modal"style="margin-right:5px; margin-top:50px" > <i class="fa fa-times" ></i> Close </button>

      
        @if ($anApprover && is_null($dtrp->isApproved))
        <a href="#" class="process btn btn-danger btn-sm pull-right" data-notifType="{{$data_notifType}}" data-action="0" data-notifID="{{$data_notifID}}" data-id="{{$data_id}}" data-dismiss="modal"style="margin-right:5px; margin-top:50px" > <i class="fa fa-thumbs-down" ></i> Deny </a>
        <a href="#" class="process btn btn-success btn-sm pull-right" data-notifType="{{$data_notifType}}" data-action="1" data-notifID="{{$data_notifID}}" data-id="{{$data_id}}" data-dismiss="modal"style="margin-right:5px; margin-top:50px" > <i class="fa fa-thumbs-up" ></i> Approve </a>

        @endif


     
      </div> 
      <div class="modal-footer no-border">
        
      </div>
    </div>
  </div>
</div>