<div class="modal fade" id="myModal{{$modelID}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
          <h4 class="modal-title text-primary" id="myModalLabel"><i class="fa fa-hourglass"></i> {{$modalTitle}}</h4>
        
      </div> 
      <div class="modal-body-upload" style="padding:20px;">
       

        <br/><br/>
         <div class="row">

             
              <div class="col-sm-12">

                <div class="row">
                  
                  <div class="col-sm-3"><h5 class="text-primary">Production Date</h5></div>
                  <div class="col-sm-6"><h5 class="text-primary">OT Details</h5></div>
                  <div class="col-sm-3"><h5 class="text-primary">Status</h5></div>
                </div>

                 <div class="row">
                  
                  <div class="col-sm-3"><?php echo date('M d, Y - l', strtotime($data['productionDate'])) ?></div>
                  <div class="col-sm-6">
                   <strong>Total OT hrs: </strong> {{$data['userOT']->first()['filed_hours']}}<br/>
                   <strong>Hours worked: </strong> {{date ('h:i A', strtotime($data['userOT']->first()['timeStart'])) }} - {{ date('h:i A', strtotime($data['userOT']->first()['timeEnd'])) }}
                   <strong>Reason: </strong><br/><em style="font-size:0.8em"> {{$data['userOT']->first()['reason']}}</em>
                    </div>
                  <div class="col-sm-3">
                    @if (is_null($data['userOT']->first()['isApproved'])) 
                    <h4 class="text-gray"><em>For Approval</em></h4>
                    @else
                        @if ($data['userOT']->first()['isApproved']) 
                        <h4 class="text-success">Approved</h4>
                        @else 
                        <h4 class="text-danger">Denied</h4>@endif
                    @endif


                  </div>
                </div>

               
              </div>

               
               
         </div>

         

        
        
        
        <a href="{{action('UserController@myRequests',$user->id)}} " style="margin-left:5px; margin-top:50px" class="btn btn-primary btn-sm pull-left"><i class="fa fa-clipboard"></i> View My Requests</a>
        <button type="button" class="btn btn-default btn-sm pull-right" data-dismiss="modal"style="margin-right:5px; margin-top:50px" > 
          <i class="fa fa-check" ></i> Okay </button>

     
      </div> 
      <div class="modal-footer no-border">
        
      </div>
    </div>
  </div>
</div>