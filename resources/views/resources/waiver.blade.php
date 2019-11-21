@extends('layouts.main')


@section('metatags')
  <title>Resources</title>
    <meta name="description" content="all teams">

@stop


@section('content')




<section class="content-header">

      <h1>
       Waiver Form
        <small>Year End Party 2019</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        
        <li class="active">OAMPI Resources</li>
      </ol><br/><br/>

      
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-1"></div>
        <div class="col-xs-10" style="background: rgba(256, 256, 256, 0.4);padding:50px">

          <h2 class="text-center" style="padding:20px;"><img src="public/img/oam_favicon1-55027f4ev1_site_icon-256x256.png" width="90" style="margin-bottom: 30px;" /><br/>
            WAIVER OF LIABILITY AND HOLD HARMLESS AGREEMENT <br/><br/></h2>

          <p>1. In consideration for receiving permission to participate in the <strong>Monochrome Year End Party</strong> (“Event”) on <strong>December 14, 2019</strong> at the <strong>Makati Shangri-La</strong> I hereby release, waive, discharge and covenant not to sue <strong>OAMPI, Inc.</strong>, its directors, officers, stockholders, representatives, agents and employees (hereinafter referred to as “releasees”) from any and all liability, claims, demands, actions and causes of action whatsoever arising out of or relating to any loss, expense, damage or injury, including death, that may be sustained by me, or to any property belonging to me, whether caused by any act or omission or the negligence of the releasees, or otherwise, during the entire course of or while participating in the event, or while in, on or upon the premises where the event is being conducted, while in transit to or from the premises, or in any place or places connected with the Event.</p>

          <p>2. I am fully aware of risks and hazards connected with being on the premises and participating in the Event, and I am fully aware that there may be risks and hazards unknown to me connected with being on the premises and participating in the Event, and I hereby elect to voluntarily participate in the Event, to enter upon the premises and engage in activities knowing that conditions may be hazardous, or may become hazardous or dangerous to me and my property. I voluntarily assume full responsibility for any risks of loss, property damage or personal injury, including death, expenses, liabilities that may be sustained by me, or any loss or damage or property owned by me, as a result of my being a participant in the Event, whether caused by the negligence of releasees or otherwise.</p>

          <p>3. I further hereby agree to indemnify and save and hold harmless the releasees and each of them, from any loss, liability, lawsuits, damage or costs, including attorney’s fees, they may incur due to my participation in the Event, whether caused by the negligence of any all of the releasees, or otherwise, and to reimburse the releases for any such expenses incurred.</p>

          <p>4. It is my express intent that this Release shall bind the members of my family and spouse, heirs, assigns, executors, administrators and personal representatives, and shall be deemed as a Release, Waiver, Discharge and Covenant Not to Sue the above named releasees.</p>

          <p>5. I hereby agree to follow the company rules for out of the office activities.</p>

          <p><br/><br/>By clicking on the checkboxes below, I acknowledge and represent that:<br/><br/></p>

          <ul style="list-style: none;">

            @if(count($alreadySigned)==0)
            <li>
              <label><input type="checkbox" name="cb1" class="chk" value="1" /> I have read the foregoing rules and release, understood it, and signed it voluntarily as my own free act and deed;</label></li>
            <li>
              <label><input type="checkbox" name="cb2" class="chk"  value="2" /> No oral representation, statements or inducements, apart from the foregoing written agreement, have been made;</label></li>
            <li>
              <label><input type="checkbox" name="cb3" class="chk"  value="3" /> I am at least eighteen (18) years of age and fully competent. I am in good physical condition and I sought doctor’s clearance and advice on the medical and health risks about this Event. In case of injury or illness during the Event, it is my responsibility to arrange for and pay for medical treatment.</label></li>
            <li>
              <label><input type="checkbox" name="cb4" class="chk"  value="4" /> I execute this Release for full, adequate and complete consideration fully intending to be bound by same.</label></li>

            @else

            <li>
              <label><input type="checkbox" name="cb1" class="chk" value="1" checked="checked" disabled="disabled" /> I have read the foregoing rules and release, understood it, and signed it voluntarily as my own free act and deed;</label></li>
            <li>
              <label><input type="checkbox" name="cb2" class="chk"  value="2"  checked="checked"  disabled="disabled"/> No oral representation, statements or inducements, apart from the foregoing written agreement, have been made;</label></li>
            <li>
              <label><input type="checkbox" name="cb3" class="chk"  value="3"  checked="checked" disabled="disabled" disabled="disabled"/> I am at least eighteen (18) years of age and fully competent. I am in good physical condition and I sought doctor’s clearance and advice on the medical and health risks about this Event. In case of injury or illness during the Event, it is my responsibility to arrange for and pay for medical treatment.</label></li>
            <li>
              <label><input type="checkbox" name="cb4" class="chk"  value="4" checked="checked"  disabled="disabled"/> I execute this Release for full, adequate and complete consideration fully intending to be bound by same.</label></li>

            @endif

          </ul>

          <p><br/><br/>I have hereunto set my hand and seal this {{ date('jS',strtotime($today->format('Y-m-d')) ) }} day of {{$today->format('F')}},2019.<br/><br/></p>

          @if(count($alreadySigned)==0)
          <a id="submit" class="btn btn-success btn-lg pull-right"><i class="fa fa-check"></i> Submit </a>
          <p>Participant: <strong>{{$employee->firstname}} {{$employee->lastname}} </strong></p>
          <p>Date: {{$today->format('F d, Y H:i:s')}} </p>

          @else
          <p>Participant: <strong>{{$employee->firstname}} {{$employee->lastname}} </strong></p>
          <p>Date: {{date('F d, Y H:i:s', strtotime($alreadySigned->first()->created_at))}} </p>
          @endif

          














          

        </div><!--end col xs 12-->
        <div class="col-xs-1"></div>

      </div><!--end row-->

     

    </section>

@stop

@section('footer-scripts')
<script>
  $(function () {
    
    @if(count($alreadySigned)==0)
    $('#submit').on('click', function(e) {

      if ($('.chk:checked').length == $('.chk').length)
      {
        var _token = "{{ csrf_token() }}";
     
       
       

        $.ajax({
                        url:"{{action('ResourceController@viewItem')}} ",
                        type:'POST',
                        data:{
                          'user_id': '{{ Auth::user()->id }}',
                          'id': '{{$id}}',
                          'agreed': 1 ,
                          _token:_token},

                        error: function(response)
                        { console.log("error"); return false;
                        },
                        success: function(response)
                        {
                          //window.location.href="./oampi-resources/item/"+response;
                          $('#submit').fadeOut();
                          $.notify("Thank you for signing up. See you at the party.",{className:"success", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} ); return false;
                          
                        }//end success

            }); //end ajax

      }else{
        $.notify("Kindly select all checkboxes to acknowledge the terms and conditions.",{className:"error", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} ); 
                  return false;
      }
      
      


    });
    @endif

    

    


    
  });
</script>
@stop