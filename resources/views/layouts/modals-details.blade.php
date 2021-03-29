<div class="modal fade" id="myModal{{$modelID}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
          <h4 class="modal-title text-primary" id="myModalLabel"><i class="fa fa-calendar"></i> {{$modalTitle}}</h4>
        
      </div> 
      <div class="modal-body-upload" style="padding:20px;">
       

        <br/><br/>
         <div class="row">

             
              <div class="col-sm-12">
                <!-- <table>
                  <tr>
                    <th>Production Date</th>
                    <th>Work Shift</th>
                    <th>Status</th>
                  </tr>
                  <tr>
                    <td></td>
                  </tr>

                </table> -->

                <div class="row">
                  
                  <div class="col-sm-3"><h5 class="text-primary">Production Date</h5></div>
                  <div class="col-sm-6"><h5 class="text-primary">Work Shift</h5></div>
                  <div class="col-sm-3"><h5 class="text-primary">Status</h5></div>
                </div>
                <?php $ctr = 1; ?>

                @foreach($data['usercws'] as $datas)

                 <div class="row" style="padding: 20px 0px; border-bottom: dotted 1px #333; @if($ctr != 1) opacity:0.3 @endif">
                  
                  <div class="col-sm-3"><?php echo date('M d, Y - l', strtotime($data['productionDate'])) ?><br/>
                  </div>
                  <div class="col-sm-6">
                    @if ($datas['timeStart_old'] == "00:00:00" && $datas['timeEnd_old'] == "00:00:00" )

                    <strong>Old:  &nbsp;</strong> REST DAY<br/>

                    @else

                    <strong>Old:  &nbsp;</strong> {{date('h:i A', strtotime($datas['timeStart_old'])) }} - {{ date('h:i A', strtotime($datas['timeEnd_old'])) }}<br/>

                    @endif

                    @if ($datas['timeStart'] == "00:00:00" && $datas['timeEnd'] == "00:00:00" )
                     <strong>New:  &nbsp;</strong> REST DAY<br/> <small><em><br/>Filed: <?php echo date('m/d/y h:i A',strtotime($datas['created_at']))?> </em></small>
                     @else
                    
                    <strong>New: &nbsp; </strong>  {{date('h:i A', strtotime($datas['timeStart'])) }} - {{ date('h:i A', strtotime($datas['timeEnd'])) }}<br/>

                    @if (is_null($datas['notes']))
                    <strong><br/>Reason: </strong><em> *none*</em>

                    @else
                    <strong><br/>Reason: </strong><em> {{$datas['notes'] }}</em>

                    @endif

                     <small><em><br/><br/>Filed: <?php echo date('m/d/y h:i A',strtotime($datas['created_at']))?> </em></small>
                   

                    @endif

                  </div>
                  <div class="col-sm-3">
                    @if (is_null($datas['isApproved']))
                    <h4 class="text-gray"><em>For Approval</em></h4>
                    @else
                        @if ($datas['isApproved']) 
                        <h4 class="text-success">Approved @if($anApprover)<a class="delBtn btn btn-xs btn-flat" title="Delete" data-id="{{$datas['id']}}"><i class="fa fa-trash"></i></a>@endif</h4>

                        @else 
                        <h4 class="text-danger">Denied @if($anApprover)<a class="delBtn btn btn-xs btn-flat" title="Delete" data-id="{{$datas['id']}}"><i class="fa fa-trash"></i></a>@endif</h4>@endif
                    @endif


                  </div>
                </div>
                <?php $ctr++; ?>
                @endforeach

               
              </div>

               
               
         </div>

         

        
        
         @if(!$isBackoffice)<p style="font-size: small;" class="text-success"><br/>* CWS requests from all Operations personnel will be processed by WFM team.</p> @endif

         <button type="button" class="btn btn-default btn-sm pull-right" data-dismiss="modal"style="margin-right:5px; margin-top:50px" > <i class="fa fa-times" ></i> Close </button>



        @if ( (($anApprover && $isBackoffice) || ($isWorkforce && !$isBackoffice) ) && is_null($data['usercws'][0]['isApproved']))


        <a href="#" class="process btn btn-danger btn-sm pull-right" data-notifType="{{$data_notifType}}" data-action="0" data-notifID="{{$data_notifID}}" data-id="{{$data_id}}" data-dismiss="modal"style="margin-right:5px; margin-top:50px" > <i class="fa fa-thumbs-down" ></i> Deny </a>
        <a href="#" class="process btn btn-success btn-sm pull-right" data-notifType="{{$data_notifType}}" data-action="1" data-notifID="{{$data_notifID}}" data-id="{{$data_id}}" data-dismiss="modal"style="margin-right:5px; margin-top:50px" > <i class="fa fa-thumbs-up" ></i> Approve </a>

        @endif

     
      </div> 
      <div class="modal-footer no-border">
        
      </div>
    </div>
  </div>
</div>