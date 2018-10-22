<div class="modal fade" id="memo{{$modelID}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
          <h4 class="modal-title" id="myModalLabel"><img src="{{asset('public/img/oam_favicon1-55027f4ev1_site_icon-256x256.png')}}" width="30px" align="center" /> {{$modelName}} </h4>
        
      </div>
      <div class="modal-body" style="padding:80px;">
        
        {!! $modalMessage !!}

       
       
      </div>
      <div class="modal-footer no-border">
        
        <form name="{{$formID}}" id="{{$formID}}">
         <div id="memo"></div>  
         <input type='hidden' name='id' id='id' value='{{$modelID}}' />   
          <!-- <button type="submit" id="yes" class="btn btn-primary {{$icon}} glyphicon "> YES, I agree </button> -->
          <button data-dismiss="modal" type="button" id="yesMemo" class="yes btn btn-primary btn-md" data-resID="{{$modelID}}"><i class="fa fa-check-square-o"></i> Okay, got it. Don't show this message again.</button>
          <!-- <a href="#" id="no" class="no btn btn-success btn-md "> Proceed anyway </a> -->
          <!-- <button title="You may view and agree later on" type="button" id="no" class="no btn btn-success btn-md" data-resID="{{$modelID}}"><i class="fa fa-square-o"></i> View now, I'll agree later... </button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button> -->
        </form>


      </div>
    </div>
  </div>
</div>