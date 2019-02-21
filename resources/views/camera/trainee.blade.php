@extends('layouts.id-trainee')
@section('content')
  <div id="id_wrapper">
    <div id="id_container">
      
      <img src="{{ asset( 'public/img/blank_canvas.png' ) }}" id="foreground" style="display: block; opacity: 1"/>
      
    </div>
    <div id="employee_details1">
      <p id="employee_nick" class="medium">TRAINEE</p>
    </div>
    
    <div id="id_details">
      <p id="employee_number" class="light">ID No.: <span id="id_number">2019-01-0001</span></p>
    </div>
  </div>
  <div id="controls">
    <div class="section right">
      <h6>Options</h6>
      <div class="row">
        <form class="col s8">  
          <div class="input-field col s12">
            <input placeholder="10" id="copies" name="copies" type="number" class="validate" value="10">
            <label for="copies">How Many Copies?</label>
          </div>
          <div class="input-field col s12">
            <input placeholder="1" id="start" name="start" type="number" class="validate" value="1">
            <label for="start">Starting Index:</label>
          </div>
          <div class="input-field col s5 right">
            <div class="row">
              <a class="waves-effect waves-light btn-large" href="javascript:verify();"><i class="material-icons left">print</i>Print</a>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
  
  
  
  
  <script type="text/javascript" src="{{ asset( 'public/js/jquery.js' ) }}"></script>
  <script type="text/javascript" src="{{ asset( 'public/js/html2canvas.min.js' ) }}"></script>
  <script type="text/javascript" src="{{ asset( 'public/js/seriously.js' ) }}"></script>
  <script type="text/javascript" src="{{ asset( 'public/js/seriously.chroma.js' ) }}"></script>
  <script type="text/javascript" src="{{ asset( 'public/js/materialize.min.js' ) }}"></script>

  
  <script>
  window.startAt = 1;
  window.copy_counter = 0;
  window.copies = 1;
  window.filepath = [];
  
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
    
    function pad(num, size){ return ('0000' + num).substr(-size); } 
    
    function verify() {
      window.startAt = 1;
      window.copy_counter = 0;
      window.copies = 1;
      window.filepath = [];
      if (isNaN($('#copies').val())) {
        alert("Please specify how many ID's to print")
        return;
      }else{
        window.copies = $('#copies').val();
      }
      if (isNaN($('#start').val())) {
        alert("Please specify the starting ID number")
        return;
      }else{
        window.startAt = parseInt($('#start').val());
      }
      $('#copies').disabled = true;
      $('#start').disabled = true;
      renderme();
    }
  
    function renderme() {
      
      var d = new Date();
      var month = ('0' + (d.getMonth()+2)).slice(-2);
      var year = d.getFullYear();
      var id_number = year + "-" + month + "-" + pad(window.startAt,4);
      $('#id_number').text(id_number);
      
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
            window.filepath.push(data);
            window.copy_counter = window.copy_counter + 1;
            window.startAt = window.startAt + 1;
            if (window.copy_counter==copies) {
              printme();
            } else {
              renderme();
            }
          },
          error: function(xhr,status,msg){
            alert(msg);
          }
        });
      });
    }
    function printme(){
      if (window.filepath.length>0) {
        var body = "";
        for(var i = 0; i<window.filepath.length; i++){
          var div = '<div><img src="{{ $url }}/' + window.filepath[i] + '"></div>';
          body = body + div;
        }
        
        var filepath = window.location + window.filepath;
        var popupWin = window.open('', '_blank', 'width=638,height=1013');
        //var onload = window.print();
        popupWin.document.open();
        popupWin.document.write('<html><head><link rel="stylesheet" href="{{ $url }}/public/css/printstyle.css" type=></head><body onload="window.print();">' + body + '</html>');
        popupWin.document.close();
      }
      
    }
  
  </script>

@endsection