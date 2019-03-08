@extends('layouts.id')
@section('metatags')
<title>ID Printing | EMS </title>
@endsection
@section('content')
  <div id="id_wrapper">
    <div id="id_container">
      <img src="{{ asset( 'public/img/blank_canvas.png' ) }}" id="foreground" style="display: block; opacity: 1"/>
      <canvas id="seriousCanvas" width="1560" height="1560"></canvas>
      <div id="id_signature_wrapper">
        <img id="id_signature" src="{{ asset( 'public/img/blank_signature.png' ) }}" />
      </div>
    </div>
    <div id="employee_details1">
      <p id="employee_nick" class="medium">{{ ucwords(strtolower($user->nickname)) }}</p>
      <p id="employee_name" class="light">{{ ucwords(strtolower($user->firstname)) }} {{ ucwords(strtolower($user->lastname)) }}</p>
      <p id="employee_position" class="light">{{ $user->position->name }}</p>
    </div>
    <div id="id_number_wrapper">
      <p id="employee_number" class="medium">{{$user->employeeNumber}}</p>
      <!--
      <p class="light">If found please call: <span id="employee_tin"></span></p>
      <p class="light">+63 403 4082<span id="employee_sss"></span></p>
      -->
    </div>
  </div>
  <div id="crosshair_wrapper">
    <img src="{{ asset( 'public/img/crosshair.png' ) }}" />
  </div>
  <div id="controls" class="flow-text">
  
    @if ($campaign_mode === true)
    
    <div id="featdiscov" class="tap-target teal" data-target="employee_loader_next">
      <div class="tap-target-content">
        <h5>Campaign Mode</h5>
        <p>
          Employees under the Campaign / Department: <b>Marketing</b> has been preloaded.<br/>
          Click on these buttons to cycle between the employee's data.<br/>
          <a href="javascript:dismissFeatureDiscov()" class="waves-effect waves-blue-grey btn right blue-grey darken-2">OK</a>
        </p>
      </div>
    </div>
      
    @endif
      
    <div class="section">
      <h6>Employee Details</h6>
      <div class="row">
        <form class="col s12">  
          <div class="input-field col s6">
            <input placeholder="Ben" id="emp_nick" name="emp_nick" type="text" class="validate" value="{{ ucwords(strtolower($user->nickname)) }}">
            <label for="emp_nick">Nickname</label>
          </div>
          <div class="input-field col s6">
            <input placeholder="Benjamin Davidowitz" id="emp_name" name="emp_name" type="text" class="validate" value="{{ ucwords(strtolower($user->firstname)) }} {{ ucwords(strtolower($user->lastname)) }}" >
            
            <label for="emp_name">Full Name</label>
          </div>
          <div class="input-field col s6">
            <input placeholder="Chief Executive Officer" id="emp_pos" name="emp_pos" type="text" class="validate" value="{{ $user->position->name }}">
            <label for="emp_pos">Position</label>
          </div>
          <div class="input-field col s6">
            <input placeholder="0000000001" id="emp_num" name="emp_num" type="number" class="validate" value="{{ $user->employeeNumber }}">
            <label for="emp_num">ID Number</label>
          </div>
            
          <!--  
          <div class="input-field col s6">
            <input id="emp_sss" name="emp_sss" type="text" class="validate" value="" placeholder="04-2047199-9">
            <label for="emp_sss">SSS</label>
          </div>
          <div class="input-field col s6">
            <input placeholder="255-173-099" id="emp_tin" name="emp_tin" type="text" class="validate" value="">
            <label for="emp_tin">TIN</label>
          </div>
          -->
            
          @if ($campaign_mode === true)  
          <div class="input-field col s6">
            <a class="waves-effect waves-light btn" href="javascript:loadPreviousEmployee();"><i class="material-icons">chevron_left</i></a>
          </div>
          <div class="input-field col s6">
            <a class="waves-effect waves-light btn right" href="javascript:loadNextEmployee();" id="employee_loader_next"><i class="material-icons">chevron_right</i></a>
          </div>
          @endif
        </form>
      </div>
    </div>  
    
    <div class="section">
      <h6>Camera Controls</h6>
      <div class="row">
        <form class="col s12">
          <div class="input-field col s12">
            <select id="cameraSelector"></select>
            <label>Choose Camera</label>
          </div>
          <div class="input-field col s6">
              <a class="waves-effect waves-light btn-large col s12" href="javascript:camerapause();"><i class="material-icons left">camera_enhance</i><span id="bt_controller">Start Camera</span></a>
          </div>
          <div class="input-field col s6">            
              <a class="waves-effect waves-light btn-large col s12" href="javascript:signature();"><i class="material-icons left">edit</i>Sign</a>
          </div>
          <div class="input-field col s6">            
              <a class="waves-effect waves-light btn-large col s12 right" href="javascript:save();"><i class="material-icons left">save</i>Save for Print</a>
          </div>
          <div class="input-field col s6">            
              <a class="waves-effect waves-light btn-large col s12 right" href="javascript:saveToArchive();"><i class="material-icons left">archive</i>Archive</a>
          </div>
          <div class="input-field col s6">
              <a class="waves-effect waves-light btn-large col s12 right" href="javascript:printme();"><i class="material-icons left">print</i>Print</a>            
          </div>
          <div class="input-field col s6">
              <a class="waves-effect waves-light btn-large col s12 right" href="javascript:reset();"><i class="material-icons left">settings_backup_restore</i>Reset</a>            
          </div>
        </form>  
      </div>
    </div>
    
    <div class="section">
      <h6>Chroma Key Options</h6>
      <div class="row">
        <form class="col s12">
          <div class="input-field col s3">
            <div class="row">
            <label for="input-chroma-balance">Balance</label>
            <p class="range-field">
            <input type="range" min="0" max="1" step="0.01" value="1" id="input-chroma-balance" >
            </p>
            </div>
          </div>
          <div class="input-field col s3 offset-s1">
            <div class="row">
            <label for="input-chroma-clipBlack">Clip Black</label>
            <p class="range-field">
            <input type="range" min="0" max="1" step="0.01" value="0.9" id="input-chroma-clipBlack">
            </p>
            </div>
          </div>
          <div class="input-field col s3 offset-s1">
            <div class="row">
            <label for="input-chroma-clipWhite">Clip White</label>
            <p class="range-field">
            <input type="range" min="0" max="1" step="0.01" value="0.9" id="input-chroma-clipWhite">
            </p>
            </div>
          </div>
          <!-- <input type="button" value="Load Image" onClick="loadimage()" class="buttons" > -->
        </form>
      </div>
    </div>
    
    
    <div id="options">
      <video autoplay="true" id="videoElement"></video>
      <img id="imageElement" />
    </div>    
  </div>
  <div id="dimmer">
  <div id="signature_wrapper">
    <canvas id="signature_canvas" width="768" height="432"></canvas>
    <div class="row">
      <div class="col s12">
        <a class="waves-effect waves-light btn" href="javascript:clearSignature();"><i class="material-icons left">clear</i>Clear</a>&nbsp;
        <a class="waves-effect waves-light btn" href="javascript:close_signature_capture();"><i class="material-icons left">cancel</i>Cancel</a>
        <a class="waves-effect waves-light btn right" href="javascript:saveSignature();"><i class="material-icons left">check</i>Save</a>
      </div>
      
  </div>
  </div>
  
  <script type="text/javascript" src="{{ asset( 'public/js/jquery.js' ) }}"></script>
  <script type="text/javascript" src="{{ asset( 'public/js/adapter-latest.js' ) }}"></script>
  <script type="text/javascript" src="{{ asset( 'public/js/html2canvas.min.js' ) }}"></script>
  <script type="text/javascript" src="{{ asset( 'public/js/seriously.js' ) }}"></script>
  <script type="text/javascript" src="{{ asset( 'public/js/seriously.chroma.js' ) }}"></script>
  <script type="text/javascript" src="{{ asset( 'public/js/signature_pad.min.js' ) }}"></script>
  <script type="text/javascript" src="{{ asset( 'public/js/materialize.min.js' ) }}"></script>
  <script type="text/javascript" src="{{ asset( 'public/js/pace.js' ) }}"></script>
  
  
  <script>
  window.employee_id = {{ $user->id }};
  window.mode = null;
  window.image_index = 0;
  window.images = [
    "{{ asset( 'public/img/2.jpg' ) }}",
    "{{ asset( 'public/img/3.jpg' ) }}",
    "{{ asset( 'public/img/4.jpg' ) }}"
  ];
  
  $('#employee_details2').hide();
  window.context = null;
  window.seriously = null;
  window.hasCapturedPhoto = false;
  window.hasSignature = false;
  window.paused = false;
  window.mustFlip = true;
  window.video = document.querySelector("#videoElement");
  window.videoSelect = document.querySelector('#cameraSelector');
  window.vStream;
  window.height = 0;
  window.width = 0;
  window.filepath = "";
  window.sign_filepath = "";
  window.signaturePad = null;
  window.readytoprint = false;
  window.archive;
  
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
  
  function handleError(error) {
    M.toast({html: 'Could not load the camera information. Please contact the Marketing Dept.'});
    console.log('navigator.getUserMedia error: ', error);
    
  }
  
  function gotDevices(deviceInfos) {
    const selectors = [window.videoSelect];
    const values = selectors.map(select => select.value);
    selectors.forEach(select => {
      while (select.firstChild) {
        select.removeChild(select.firstChild);
      }
    });
    for (let i = 0; i !== deviceInfos.length; ++i) {
      const deviceInfo = deviceInfos[i];
      const option = document.createElement('option');
      option.value = deviceInfo.deviceId;
      if (deviceInfo.kind === 'videoinput') {
        console.log(deviceInfo);
        option.text = deviceInfo.label || `camera ${window.videoSelect.length + 1}`;
        window.videoSelect.appendChild(option);
        $('select').formSelect();
        
        window.vStream.getTracks()[0].stop();
      }
    }
    selectors.forEach((select, selectorIndex) => {
      if (Array.prototype.slice.call(select.childNodes).some(n => n.value === values[selectorIndex])) {
        select.value = values[selectorIndex];
      }
    });
  }
  
  function initializeCamera(){
    window.video = document.querySelector("#videoElement");
    window.paused = false;
    window.hasCapturedPhoto = false;
    const videoSource = videoSelect.value;
    $('#bt_controller').text("Capture");
    var constraints = {
      audio: false,
      video: {
        deviceId: videoSource ? {exact: videoSource} : undefined,
        width: { exact: window.resolutions[window.resolutionIndex].width },
        height: { exact: window.resolutions[window.resolutionIndex].height }
      }
    };
    navigator.mediaDevices.getUserMedia(constraints)
    .then(function(stream) {
      console.log('constraint satisfied');
      //var v=document.getElementById("videoElement");
      document.getElementById('videoElement').onloadedmetadata = function() {
        window.height = this.videoHeight;
        window.width = this.videoWidth;
        console.log('video loaded');
        goserious();
      }
      window.video.srcObject = stream;
      window.mode = "camera";
      window.cameraMode = "started";
    })
    .catch(function(error) {
      if (error.name=="OverconstrainedError") {
        console.log('tried: '+window.resolutions[window.resolutionIndex].width+'x'+window.resolutions[window.resolutionIndex].height+" - FAILED");
        window.resolutionIndex = window.resolutionIndex + 1;
        if (window.resolutionIndex > window.resolutions.length) {
          M.toast({html: 'Camera resolution is too low. Please consider getting a better web cam.'});
          return;
        }else{
          initializeCamera();
        }
      } else {
        handleError(error) ;
      }
    });
  }
  
  function arrayToHex(color) {
    var i, val, s = '#';
    for (i = 0; i < 4; i++) {
      val = Math.min(255, Math.round(color[i] * 255 || 0));
      val = val.toString(16);
      if (val.length === 1) {
        val = '0' + val;
      }
      s += val;
    }
    return s;
  }
  
  function save() {
    Pace.start;
    window.readytoprint = false;
    /*
    if ($('#emp_sss').val()=="") {
      M.toast({html: 'Please fill out the SSS number field!'})
      window.archive = false;
      return;
    }
    if ($('#emp_tin').val()=="") {
      M.toast({html: 'Please fill out the TIN number field!'})
      window.archive = false;
      return;
    }
    */
    if(window.hasCapturedPhoto == false && window.hasSignature == false){
      M.toast({html: 'Please take a photo first then sign the ID'})
      window.archive = false;
      Pace.stop;
      return;
    }
    if(window.hasCapturedPhoto == true && window.hasSignature == false){
      M.toast({html: 'Please sign the ID first'})
      window.archive = false;
      Pace.stop;
      return;
    }
    if(window.hasCapturedPhoto == false && window.hasSignature == true){
      M.toast({html: 'Please take a photo first'})
      window.archive = false;
      Pace.stop;
      return;
    }
  
    window.filepath = "";
    if (window.hasCapturedPhoto) {
      html2canvas(document.querySelector('#id_wrapper')).then(function(canvas) {
        var portrait = document.getElementById('seriousCanvas').toDataURL('image/png');
        var imgData = canvas.toDataURL('image/png');
        //console.log(imgData);
        $.ajax({
          url: window.archive ? "{{ url('/archive') }}" : "{{ url('/export_id') }}",
          type: "POST",
          dataType: "text",
          data: {
            base64data : imgData,
            portraitData : portrait,
            id: window.employee_id
          },
          success: function(data,status,xhr){
            if(window.archive == true){
              M.toast({html: 'ID successfully added to archive!'})
            } else {
              window.filepath = data;
              window.readytoprint = true;
              M.toast({html: 'ID layout saved successfully!'})
            }
            Pace.stop;
          },
          error: function(xhr,status,msg){
            M.toast({html: msg})
            Pace.stop;
          }
        })
      });
    }else{
      M.toast({html: 'Please capture a photo first'})
    }
  }
  
  function saveToArchive(){
    window.archive = true;
    save();
  }
  
  function printme(){
    if(window.readytoprint == true){
      var filepath = window.location + window.filepath + "?timestamp=" + Date.now();
      var popupWin = window.open('', '_blank', 'width=638,height=1013');

      popupWin.document.open();
      popupWin.document.write('<html><head><link rel="stylesheet" href="{{ $url }}/public/css/printstyle.css" type=></head><body onload="window.print();">' + '<div><img src="{{ $url }}/' + window.filepath + '"></div>' + '</body></html>');
      popupWin.document.close();
      
      
      reset();
    }else{
      console.log(window.readytoprint);
      M.toast({html: 'Please take a photo first then sign the ID'})
    }
  }
  
  function reset(){
    
    if (window.seriously!==null) {
      window.seriously.stop();
      window.seriously.destroy();
    }
    
    if(window.video!=null && window.video!=undefined){
      window.video.pause();
      window.paused = true;
    }
    
    try{
      window.video.srcObject.getTracks()[0].stop();
    }catch(error){
      //ignore
    }
    
    try{
      document.getElementById('seriousCanvas').parentNode.removeChild(document.getElementById('seriousCanvas'));
      $('<canvas id="seriousCanvas" width="1560" height="1560"></canvas>').insertAfter($('#foreground'));
    }catch(error){
      //ignore
    }
    
    window.hasCapturedPhoto = false;
    window.hasSignature = false;
    window.archive = false;
    $('#bt_controller').text("Start Camera");
    $("#id_signature").attr("src", '{{ asset( 'public/img/blank_signature.png' ) }}');
    $('#crosshair_wrapper').hide();
    $('#employee_name').css('font-size',"55px");
  }
  
  
  function camerapause() {
    
    if($('#bt_controller').text()==="Retry" || $('#bt_controller').text()==="Start Camera" || window.mode=="image"){
      console.log("initializing camera");
      initializeCamera();
      $('#bt_controller').text("Capture");
      $('#crosshair_wrapper').show();
      return;
    }

    console.log("stopping camera");
    $('#crosshair_wrapper').hide();
    window.video.pause();
    window.paused = true;
    window.video.srcObject.getTracks()[0].stop();
    if($('#bt_controller').text()!=="Retry"){
      $('#bt_controller').text("Retry");
    }
    window.hasCapturedPhoto = true;
  }
  
  
  function goserious() {
    if (window.seriously!==null) {
      console.log('stopping serious');
        window.seriously.stop();
        window.seriously.destroy();
    }else{
      console.log('seriously is null');
    }

    var subject,
        vignette,
        target,
        scale,
        height,
        width;
    
    window.seriously = new Seriously();
    
    if (window.mode=="camera") {
      console.log('stating seriously in camera mode');
      flip = window.seriously.transform('flip');
      flip.direction = 'horizontal';
      flip.source = '#videoElement';
      subject = flip;
      target = window.seriously.target('#seriousCanvas');
    }
    if (window.mode=="image") {
      console.log('stating seriously in image mode');
      subject = window.seriously.source('#imageElement');
      target = window.seriously.target('#seriousCanvas');
    }
    
    console.log("dimensions = width: "+window.width+", height: "+window.height);
    chroma = window.seriously.effect('chroma');
    
    
    
    
    chroma.source = subject;
    //chroma.screen = arrayToHex($('#input-chroma-screen').val());
    chroma.balance = '#input-chroma-balance';
    chroma.clipBlack = '#input-chroma-clipBlack';
    chroma.clipWhite = '#input-chroma-clipWhite';
    
    //target.source = chroma;
    
    
    reformat = seriously.transform('reformat');
    reformat.source = chroma;
    reformat.mode = "cover";
    reformat.width = 1560;
    reformat.height = 1560;
    target.source = reformat;
    window.seriously.go();
  }
  
  function loadimage() {
    $("#imageElement").remove();
    var src = window.images[window.image_index];
    $("<img/>").on('load',function(){
      window.width = this.width; 
      window.height = this.height;
      
      window.image_index++;
      
      if (window.image_index>(window.images.length -1)) {
        window.image_index=0;
      }
      
      if (window.mode!="image") {
        if (window.video.srcObject!==null) {
          window.video.srcObject.getTracks()[0].stop();
        }
        
        window.mode="image";
        //$('#videoElement').hide();
        //$('#imageElement').show();
      }
      
      goserious();
 
    })
    .attr("src",src);

    $("<img src='"+src+"' id='imageElement'>").appendTo($('#options'));
    
  }
  
  function signature() {
    window.readytoprint = false;
    $('#dimmer').on('click',function(e){
        if (e.target !== this) return;
        confirm_cancel_signature();
    });
    $('body').css({height: $(window).height()});
    $('body').addClass('stop-scrolling');
    
    var canvas = document.querySelector("#signature_canvas");
    
    window.signaturePad = new SignaturePad(canvas);
    $('#dimmer').show();
  }
  
  function confirm_cancel_signature() {
    var yes = confirm("Are you sure you want to cancel the Signature Capture?");
    if (yes) {
      close_signature_capture();
    }
  }
  
  function clearSignature() {
    window.signaturePad.clear();
  }
  
  function close_signature_capture() {
    $('body').removeClass('stop-scrolling');
    $('#dimmer').hide();
    window.signaturePad.off();
  }
  
  function saveSignature() {
    
    var imgData = window.signaturePad.toDataURL();
    $.ajax({
      url: "{{ url('/save_signature') }}",
      type: "POST",
      dataType: "text",
      data: {
        base64data : imgData,
        id: window.employee_id
      },
      success: function(data,status,xhr){
        window.sign_filepath = "{{ $url }}/" + data;
        $("#id_signature").attr("src", window.sign_filepath);
        close_signature_capture();
        window.hasSignature = true;
      },
      error: function(xhr,status,msg){
        M.toast({html: msg});
      }
    });
  }
  
  function camelize(str) {
  
    return str.replace(/\b\w/g, function(l){ return l.toUpperCase() });
  }
  
  function resizeEmployeeName() {
    var length = $('#emp_name').val().length;
    var font_size = 55;
    if (length<=21) {
        font_size = 55;
    } else {
      while (length > 21) {
        font_size = font_size - 2;
        length = length - 1;
      }
    }
    $('#employee_name').css('font-size',font_size + "px");
  }
  
  $('#emp_nick').keyup( function() { $('#employee_nick').text($('#emp_nick').val()); });
  $('#emp_pos').keyup( function() { $('#employee_position').text($('#emp_pos').val()); });
  $('#emp_num').keyup( function() { $('#employee_number').text($('#emp_num').val()); });
  $('#emp_name').keyup( function() {
    $('#employee_name').text($('#emp_name').val());
    resizeEmployeeName();
  });
  $('#emp_sss').keyup( function() { $('#employee_sss').text($('#emp_sss').val()); });
  $('#emp_tin').keyup( function() { $('#employee_tin').text($('#emp_tin').val()); });
  window.resolutionIndex = 0;
  window.resolutions = [
    {
        width: 3840,
        height: 2160
    },
    {
        width: 1920,
        height: 1080
    },
    {
        width: 1600,
        height: 1200
    },
    {
        width: 1280,
        height: 720
    },
    {
        width: 800,
        height: 600
    },
    {
        width: 640,
        height: 480
    },
    {
        width: 640,
        height: 360
    },
    {
        width: 352,
        height: 288
    },
    {
        width: 320,
        height: 240
    }
  ];
  
  $(document).ready(function(){
    $('#crosshair_wrapper').hide();
    
    if (!navigator.getUserMedia) {
        M.toast({html: 'Browser does not support web camera.'})
        return;
    }

    //Call gUM early to force user gesture and allow device enumeration
    navigator.mediaDevices.getUserMedia({audio: false, video: true})
        .then((mediaStream) => {

            window.vStream = mediaStream; // make globally available
            window.video = mediaStream;

            //Now enumerate devices
            navigator.mediaDevices.enumerateDevices()
                .then(gotDevices)
                .catch(handleError);
        })
        .catch((error) => {
            console.error('getUserMedia error!', error);
        });

    //Localhost unsecure http connections are allowed
    if (document.location.hostname !== "localhost") {
        //check if the user is using http vs. https & redirect to https if needed
        if (document.location.protocol !== "https:") {
            $(document).html("redirecting to https...");
            document.location.href = "https:" + document.location.href.substring(document.location.protocol.length);
        }
    }
    
    
    @if ($campaign_mode === true)
      window.currentEmployeeIndex = 0;
      $('.tap-target').tapTarget({
        onClose: function () {
            localStorage.setItem("discovered", "yes");
            console.log('setting discovered');
        }
      });
      if(localStorage.discovered !== "yes"){
        $('.tap-target').tapTarget('open');
      }
    @endif
  });
  
  
  @if ($campaign_mode === true)
  window.employees = JSON.parse(' {!! $users !!} ');
  window.currentEmployeeIndex =  0;
  function dismissFeatureDiscov(){
    $('.tap-target').tapTarget('close');
  }
  
  function loadNextEmployee(){
    window.currentEmployeeIndex =  window.currentEmployeeIndex + 1;
    loadData();
  }
  
  function loadPreviousEmployee(){
    window.currentEmployeeIndex =  window.currentEmployeeIndex - 1;
    loadData();
  }
  
  function loadData(){
    var employee = window.employees[window.currentEmployeeIndex];
    if(employee===undefined){
       M.toast({html: 'You have reached the end of the list'})
       return;
    }
    //console.log(employee);
    var fullname =
      employee.firstname.toLowerCase().charAt(0).toUpperCase() + employee.firstname.toLowerCase().slice(1) + " " +
      //employee.middlename.toLowerCase().charAt(0).toUpperCase() + ". " + 
      employee.lastname.toLowerCase().charAt(0).toUpperCase() + employee.lastname.toLowerCase().slice(1)
      ;
      
    fullname = camelize(fullname);
    
    $('#employee_nick').text(employee.nickname.toLowerCase().charAt(0).toUpperCase() + employee.nickname.toLowerCase().slice(1));
    $('#employee_name').text(fullname);
    $('#employee_position').text(employee.jobTitle);
    $('#employee_number').text(employee.employeeNumber);
    
    $('#emp_nick').val(employee.nickname);
    $('#emp_name').val(fullname);
    $('#emp_pos').val(employee.jobTitle);
    $('#emp_num').val(employee.employeeNumber);
    resizeEmployeeName();
    window.employee_id = employee.id;
  }
  
  loadData();
  @endif
  
  
  
  
  </script>
@endsection