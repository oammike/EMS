@extends('layouts.main')

@section('metatags')
<title>Download Digital Form | Employee Management System</title>

@endsection

@section('content')


  <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
       <i class="fa fa-file-text-o"></i> My Digital Forms <small>
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

                                @if($signed)
                                <div class="info-box bg-red">
                                @else
                                <div class="info-box bg-blue">

                                @endif

                                  <span class="info-box-icon"><i class="fa fa-file-text-o"></i></span>
                                  <div class="info-box-content">
                                    <span class="info-box-text">Download <strong>Digital Form</strong>:</span>
                                   

                                    
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
                                    <!-- <h4>You are <strong>Qualified</strong> for substituted filing if: </h4> -->
                                    <p><i class="fa fa-info-circle"></i><em> Please click the following if it is applicable to you. <br/>If not, then select <strong>none of the above</strong></em></p>

                                    <label><input type="radio" name="disq" value="1" /> Individual deriving other non-business, non-profession-related income in addition to compensation not otherwise subject to final tax.</label><br/><br/>
                                    <label><input type="radio" name="disq" value="2" /> Individual deriving purely compensation income from a single employer, although the income of which has been correctly subjected to withholding tax, but whose spouse is not entitled to substituted filing.</label><br/><br/>
                                    <label><input type="radio" name="disq" value="3" /> Non-resident alien engaged in trade or business in the Philippines deriving purely compensation income or compensation income and other business or profession related income.</label><br/><br/>
                                    <label><input type="radio" name="disq" value="0" checked="checked" /> None of the above</label>

                                    <br/><br/>
                                    <a class="btn btn-md btn-default" id="next">Next &raquo;</a>

                                    

                                    

                                    </p>

                                 </div>

                                 <div class="col-lg-6">

                                  <div id="qualified">
                                    <p><br/><strong>Instructions:</strong><br/><br/>
                                      <em>Download the form and <strong class="text-danger"> sign the # 52</strong>, date signed, CTC/ Valid ID number (note: accepted valid IDs are driver’s license and passport only) and <strong class="text-danger">sign the # 54</strong>.<br/><br/> Save the signed PDF file and click the <strong>Upload Signed PDF</strong> button from your EMS side navigation (My BIR 2316 &raquo; Upload Signed PDF). </em><br/><br/ >

                                      <a class="btn btn-lg btn-success" href="viewUserForm?u={{$user->id}}&f=BIR2316"><i class="fa fa-download"></i> Download Form Here</a>
                                  </div>

                                  <div id="notqualified">
                                    <h4>You are <strong>Not Qualified</strong> for substituted filing </h4>
                                    You are not qualified for substituted filing due to the following reasons:

                                    <ul>
                                      <li>an individual deriving compensation income from two or more employers, concurrently or successively at anytime during the taxable year</li>
                                      <li>an employee deriving compensation income, regardless of amount, whether from a single or several employers during the calendar year, the income tax of which has not been withheld correctly (i.e. tax due is not equal to the tax withheld) resulting to a collectible or refundable return</li>
                                      <li>an employee whose monthly gross compensation income does not exceed Five Thousand Pesos (P5,000) or the statutory minimum wage, whichever is higher, and opted for non-withholding of tax on said income</li>
                                      <li>an Individual deriving other non-business, non-profession-related income in addition to compensation not otherwise subject to final tax</li>
                                      <li>an Individual deriving purely compensation income from a single employer, although the income of which has been correctly subjected to withholding tax, but whose spouse is not entitled to substituted filing</li>
                                      <li>a Non-resident alien engaged in trade or business in the Philippines deriving purely compensation income or compensation income and other business or profession related income</li> 
                                    </ul>

                                    

                                     <p><br/><strong>Instructions:</strong><br/><br/>
                                      <em>1. Download your BIR 2316 Form and  sign the # 52, date signed, CTC/ Valid ID number (note: accepted valid IDs are driver’s license and passport only). <br/>2. Save the signed PDF file and click the <strong>Upload Signed PDF </strong> button from your EMS side navigation (My BIR 2316 &raquo; Upload Signed PDF).</em> <br/><br/ >

                                       <a id="nonq" class="btn btn-lg btn-primary" target="_blank" href="viewUserForm?u={{$user->id}}&f=BIR2316"><i class="fa fa-download"></i> Download Form for Non-Qualified</a>

                                    </p>
                                  </div>

                              </div>

                            </div><!--end options_vl-->
                          
                       
                          
                        </div><!--end pane vl-->

                        <div class="clearfix"></div>
                       
                     
                      

                      
                       <div class="clearfix"></div><br/><br/>

                      </div><!--end box-info -->

                      <h2> <br/><br/></h2>
                     
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

     $('#qualified').fadeOut();
     $('#notqualified').fadeOut();

     $('input[name="disq"]').on('change',function(){
        $('#qualified').fadeOut();
        $('#notqualified').fadeOut();
     })

     $('#next').on('click',function(){

        var disq = $('input[name="disq"]:checked').val();

        if(disq !== '0')
        {
          $('#notqualified').fadeIn();
          $('#qualified').fadeOut();

        }else{
          $('#notqualified').fadeOut();
          $('#qualified').fadeIn();

        }

     });

     $('#nonq').on('click',function(){
        var _token = "{{ csrf_token() }}";
        var reason = $('input[name="disq"]:checked').val();
        var data = new FormData();

        
        data.append('reason',reason);
        data.append('_token',_token);

        $.ajax({
                  url: "{{action('UserFormController@disqualifyForFiling')}}",
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
                      $.notify("Non-Qualified saved in database.",{className:"success",globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );
                    }else {
                        $.notify("Something went wrong. Please try again later.",{className:"success", globalPosition:'right middle',autoHideDelay:3000, clickToHide:true} );
                        
                      }
                    
                    console.log(response);
                    
                    // window.setTimeout(function(){
                    //   window.location.href = "{{action('UserController@userRequests',$user->id)}}";
                    // }, 2000);
                  }
                });

        
                     

     });
    

     
  });

  

  
</script>
<!-- end Page script -->


@stop