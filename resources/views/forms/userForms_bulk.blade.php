@extends('layouts.main')

@section('metatags')
<title>Bulk Upload New Digital Form | Employee Management System</title>

@endsection

@section('content')


  <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
       <i class="fa fa-file-text-o"></i> New Digital Form Bulk Upload <small>
        : {{$user->firstname}} {{$user->lastname}} </small>
      </h1>
     
      <ol class="breadcrumb">
        <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">My Forms</li>
      </ol>
    </section>

     <section class="content">

      <div class="row">
        @if(Auth::user()->id == $user->id)
        <div class="col-lg-6"><a href="{{action('UserController@userRequests',$user->id)}} "><i class="fa fa-arrow-left"></i> Back to My Requests</a></div>
        @else
        <div class="col-lg-6"><a href="{{action('UserController@userRequests',$user->id)}} "><i class="fa fa-arrow-left"></i> Back to 
          @if(is_null($user->nickname)) {{$user->firstname}}'s  Requests
          @else {{$user->nickname}}'s  Requests
          @endif

        </a></div>

        @endif
       
      </div>

      <!-- ******** THE PANE ********** -->
          <div class="row">
             <div class="col-lg-1 col-sm-4  col-xs-9"></div>
             <div class="col-lg-10 col-sm-4 col-xs-12" ><!--style="background-color:#fff"--><br/><br/>
                <form class="col-lg-12" id="plotSched" name="plotSched" accept-charset="UTF-8" enctype="multipart/form-data">
              <br/><br/>

                     
                      <div class="box-info" style="background: rgba(256, 256, 256, 0.5)">
                     

                        <div id="pane_VL" class="modal-body-upload" style="padding:20px;">
                            <div class="options_vl">
                              <div style="width:100%; " class="pull-left">

                                @if($isSigned)
                                <div class="info-box bg-red">
                                @else
                                <div class="info-box bg-blue">

                                @endif

                                  <span class="info-box-icon"><i class="fa fa-file-text-o"></i></span>
                                  <div class="info-box-content">
                                    <span class="info-box-text">New <strong>Digital Form Bulk Upload</strong>:</span>
                                   

                                    
                                    <div class="progress"><div id="percentage" class="progress-bar" style="width:100%"></div>
                                    </div>
                                    <span class="progress-description">
                                      <h4 class="pull-right" > {{$formType}} </span>
                                    </span>
                                  </div>
                                  <!-- /.info-box-content -->
                                </div>
                                <!-- /.info-box -->

                              </div><br/>

                              <div class="row">
                                  <div class="col-lg-6">
                                    <h4>Type of Form: </h4>

                                    @if($isSigned)
                                      <label ><input type="radio" name="formType" value="BIR2316" checked="checked" /> &nbsp; &nbsp;<i class="fa fa-hourglass"></i> Signed BIR Form 2316 (for Qualified)</label>
                                      <label ><input type="radio" name="formType" value="BIR2316_NQ" /> &nbsp; &nbsp;<i class="fa fa-hourglass"></i> Signed BIR Form 2316 (for Non-Qualified)</label>
                                    @else
                                      <label ><input type="radio" name="formType" value="BIR2316" checked="checked" /> &nbsp; &nbsp;<i class="fa fa-hourglass"></i> BIR Form 2316 (for Qualified) ZIP file</label>
                                      <label ><input type="radio" name="formType" value="BIR2316_NQ" /> &nbsp; &nbsp;<i class="fa fa-hourglass"></i> BIR Form 2316 (for Non-Qualified) ZIP file</label>
                                    @endif

                                 </div>

                                 <div class="col-lg-6">
                                    <div id="vl_more">
                                      @if($isSigned)
                                      <p style="font-size: smaller">Make sure you upload the digitally signed copy of the PDF file that you downloaded from EMS.</p>
                                      @endif
                                      <input style="margin-top: 20px" type="file" name="attachments" id="attachments" required="required" />
                                    </div>
                                  </div>

                              </div>

                            </div><!--end options_vl-->
                          
                       
                          
                        </div><!--end pane vl-->

                        <div class="clearfix"></div>
                         <div id="loader" class="text-center">
                          <span id="wait" style="font-size: small;" class="text-primary">Uploading all files and assigning it to corresponding employees.<br/>This could take a while. Please wait...<br/><br/><img src="storage/uploads/loading.gif" width="30" />  </span><br/><br/><br/>
                        </div>

                        <a onclick="javascript:window.history.back();" class="back btn btn-default btn-lg pull-right" style="font-weight:bold;margin-top:0px; z-index:999"><i class="fa fa-angle-double-left"></i> &nbsp;Back</a>
                     
                      

                      
                        <a id="save" class="btn btn-primary btn-lg pull-right" style="margin-right:25px" > <i class="fa fa-upload" ></i> Upload File </a>
                      
                       <div class="clearfix"></div><br/><br/>

                      </div><!--end box-info -->

                      <h2> <br/><br/></h2>
                      <input type="hidden" name="userid" id="userid" value="{{$user->id}}" />
                      <input type="hidden" name="isSigned" id="isSigned" value="{{$isSigned}}" />
                      

                </form>
              </div> <!-- end col-lg-10 -->

          

                        
                            
             
              <div class="col-lg-1 col-sm-4  col-xs-9"></div>
              <div class="holder"></div>


              <div class="clearfix"></div>
          </div><!--end row-->

       
     </section>
          



@endsection


@section('footer-scripts')

<script src="{{URL::asset('public/js/moment.min.js')}}" ></script>
<script>
  $.widget.bridge('uibutton', $.ui.button);
</script>



<!-- Page script -->
<script>

  $(function () {
     'use strict';
     var _token = "{{ csrf_token() }}";
     $('#loader').fadeOut();

     $('#save').on('click',function(e)
     {
          e.preventDefault(); e.stopPropagation();
          var _token = "{{ csrf_token() }}";
          var user_id = $('#userid').val(); //$(this).attr('data-userid');
          var isSigned = $('#isSigned').val();
          var formType = $('input[name="formType"]:checked').val();
          

          var attachments = $('#attachments')[0].files[0];

          var data = new FormData();

          data.append('attachments',attachments);
          data.append('userid',user_id);
          data.append('formType',formType);
          data.append('isSigned',isSigned);
          
          data.append('_token',_token);
          console.log(data);

           if (attachments == null) {

              $.notify("Please select PDF file to upload.",{className:"error",globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );

           }else{

              $('#loader').fadeIn();
              $.ajax({
                  url: "{{action('UserFormController@bulkUploadFile')}}",
                  type:'POST',
                  //contentType: 'multipart/form-data', 
                  contentType: false,       // The content type used when sending data to the server.
                  cache: false,             // To unable request pages to be cached
                  processData:false,
                  data:data,
                  dataType: 'json',
                  
                  success: function(response){
                    $('#save').fadeOut();
                    if (response.success == '1'){
                      $.notify("Form uploaded successfully.",{className:"success",globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );
                      $('#loader').fadeOut();$('a#save').fadeOut();

                    }else {
                        $.notify("Something went wrong. Please try again later.",{className:"success", globalPosition:'right middle',autoHideDelay:3000, clickToHide:true} );
                        $('#loader').fadeOut();$('a#save').fadeOut();
                      }
                    
                    console.log(response);
                    
                    // window.setTimeout(function(){
                    //   window.location.href = "{{action('UserController@userRequests',$user->id)}}";
                    // }, 2000);
                  }
                });

           }
      });
  });

  

  
</script>
<!-- end Page script -->


@stop