@extends('layouts.id')
@section('metatags')
<title>ID Printing | EMS </title>
@endsection
@section('content')
  <div id="id_wrapper">
    <div id="id_container">
      <img src="{{ asset( 'public/img/blank_canvas.png' ) }}" id="foreground" style="display: block; opacity: 1"/>
      <canvas id="seriousCanvas" width="720" height="720"></canvas>
      <div id="id_signature_wrapper">
        <img id="id_signature" src="{{ asset( 'public/img/blank_signature.png' ) }}" />
      </div>
    </div>
    <div id="employee_details1">
      <p id="employee_nick" class="medium">{{$user->nickname}}</p>
      <p id="employee_name" class="light">{{$user->firstname}} {{$user->lastname}}</p>
      <p id="employee_position" class="light">{{ $user->position->name }}</p>
    </div>
    <div id="id_number_wrapper">
      <p id="employee_number" class="light">{{$user->employeeNumber}}</p>
    </div>
  </div>
  <div id="controls">
    <div id="inputs">
        <table>
          <tr>
            <td>Nickname:</td>
            <td><input type="text" value="{{ $user->nickname }}" name="emp_name" id="emp_nick"></td>
          </tr>
          <tr>
            <td>Full Name:</td>
            <td><input type="text" value="{{ $user->firstname }} {{ $user->lastname }}" name="emp_name" id="emp_name"></td>
          </tr>
          <tr>
            <td>Position:</td>
            <td><input type="text" value="{{ $user->position->name }}" name="emp_pos" id="emp_pos"></td>
          </tr>
          <tr>
            <td>ID Number:</td>
            <td><input type="text" name="emp_num" id="emp_num" value="{{ $user->employeeNumber }}" ></td>
          </tr>
        </table>
    </div>
    <input type="button" value="Start Camera" onClick="camerapause()" id="bt_controller" class="buttons" />
    <input type="button" value="Save" onClick="save()" class="buttons" />
    <input type="button" value="Signature" onClick="signature()" class="buttons" >
    <input type="button" value="Print" onClick="printme()" class="buttons" >
    <!-- <input type="button" value="Load Image" onClick="loadimage()" class="buttons" > -->
    
    <div id="options">
      <table>
        <!--
        <tr>
          <td><label for="input-chroma-screen">Screen</label></td>
          <td><input name="input-chroma-screen" type="text" class="param_effects" value="[66 / 255, 195 / 255, 31 / 255, 1]" /></td>
        </tr>
        -->
        <tr>
          <td><label for="input-chroma-balance">Balance</label></td>
          <td><input type="range" min="0" max="1" step="0.01" value="1" id="input-chroma-balance"></td>
        </tr>
        <tr>
          <td><label for="input-chroma-clipBlack">Clip Black</label></td>
          <td><input type="range" min="0" max="1" step="0.01" value="0.9" id="input-chroma-clipBlack"></td>
        </tr>
        <tr>
          <td><label for="input-chroma-clipWhite">Clip White</label></td>
          <td><input type="range" min="0" max="1" step="0.01" value="0.9" id="input-chroma-clipWhite"></td>
        </tr>
      </table>
      <video autoplay="true" id="videoElement"></video>
      <img id="imageElement" />
    </div>    
  </div>
  <div id="dimmer">
  <div id="signature_wrapper">
    <canvas id="signature_canvas" width="768" height="432"></canvas>
    <div id="signature_controls">
      <input type="button" value="Clear" onClick="clearSignature()" id="bt_clear_sig"  />
      <input type="button" value="Save" onClick="saveSignature()" id="bt_save_sig"  />
    </div>
      
  </div>
  </div>
  
  <script type="text/javascript" src="{{ asset( 'public/js/jquery.js' ) }}"></script>
  <script type="text/javascript" src="{{ asset( 'public/js/html2canvas.min.js' ) }}"></script>
  <script type="text/javascript" src="{{ asset( 'public/js/seriously.js' ) }}"></script>
  <script type="text/javascript" src="{{ asset( 'public/js/seriously.chroma.js' ) }}"></script>
  <script type="text/javascript" src="{{ asset( 'public/js/signature_pad.min.js' ) }}"></script>
  <script type="text/javascript" src="{{ asset( 'public/js/pace.js' ) }}"></script>
  
  
  <script>
  window.employee_id = 9972;
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
  window.paused = false;
  window.mustFlip = true;
  window.video = document.querySelector("#videoElement");
  window.vStream;
  window.height = 0;
  window.width = 0;
  window.filepath = "";
  window.sign_filepath = "";
  window.signaturePad = null;
  //initializeCamera();
  //loadimage();
  
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
  
  
  function initializeCamera(){
    //$('#videoElement').show();
    //$('#imageElement').hide();
    window.paused = false;
    window.hasCapturedPhoto - false;
    if (navigator.mediaDevices.getUserMedia) {
      $('#bt_controller').val("Capture");
      var constraints = {
        audio: false,
        video: {
          width: { min: 640, ideal: 720, max: 2048 },
          height: { min: 360, ideal: 720, max: 2048 },
        }
      };
      navigator.mediaDevices.getUserMedia(constraints)
      .then(function(stream) {
        //var v=document.getElementById("videoElement");
        document.getElementById('videoElement').onloadedmetadata = function() {
          window.height = this.videoHeight;
          window.width = this.videoWidth;
          console.log('video loaded');
          goserious();  
        }
        window.video.srcObject = stream;
        window.mode = "camera";
      })
      .catch(function(error) {
        console.log("Something went wrong!");
        console.log(error);
      });
    }
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
    window.filepath = "";
    if (window.hasCapturedPhoto) {
      html2canvas(document.querySelector('#id_wrapper')).then(function(canvas) {
        var imgData = canvas.toDataURL('image/png');
        console.log(imgData);
        $.ajax({
          url: "{{ url('/export_id') }}",
          type: "POST",
          dataType: "text",
          data: {
            base64data : imgData
          },
          success: function(data,status,xhr){
            window.filepath = data;
          },
          error: function(xhr,status,msg){
            alert(msg);
          }
        })
        .done(function(e){
          alert("saved");
          $('#bt_controller').val("Start Camera");
        });
      });
    }else{
      alert("Please capture a photo first");
    }
  }
  
  function printme(){
      var filepath = window.location + window.filepath;
      var popupWin = window.open('', '_blank', 'width=638,height=1013');
      //var onload = window.print();
      popupWin.document.open();
      popupWin.document.write('<html><head><link rel="stylesheet" href="{{ $url }}/public/css/printstyle.css" type=></head><body onload="">' + '<div><img src="{{ $url }}/' + window.filepath + '"></div>' + '</html>');
      popupWin.document.close();
  } 
  
  
  function camerapause() {
    
    if($('#bt_controller').val()==="Retry" || $('#bt_controller').val()==="Start Camera" || window.mode=="image"){
      console.log("initializing camera");
      initializeCamera();
      $('#bt_controller').val("Capture");
      return;
    }

    console.log("stopping camera");
    window.video.pause();
    window.paused = true;
    window.video.srcObject.getTracks()[0].stop();
    if($('#bt_controller').val()!=="Retry"){
      $('#bt_controller').val("Retry");
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
    reformat.width = 720;
    reformat.height = 720;
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
      },
      error: function(xhr,status,msg){
        alert(msg);
      }
    });
  }
  
  $('#emp_nick').keyup( function() { $('#employee_nick').text($('#emp_nick').val()); });
  $('#emp_name').keyup( function() { $('#employee_name').text($('#emp_name').val()); });
  $('#emp_pos').keyup( function() { $('#employee_position').text($('#emp_pos').val()); });
  $('#emp_num').keyup( function() { $('#employee_number').text($('#emp_num').val()); });
  
  
  
    
  
  </script>
@endsection