@extends('layouts.id-trainee')
@section('content')
  <div id="id_wrapper">
    <div id="id_container">
      
      <img src="{{ asset( 'storage/uploads/id/2191.png' ) }}" id="foreground" style="display: block; opacity: 1"/>
      
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
      
      $('#copies').disabled = true;
      $('#start').disabled = true;
      printme();
    }
  
    function printme(){
      
        var body = "";
        for(var i = 0; i<window.copies; i++){
          var div = '<div><img src="{{ asset( 'storage/uploads/id/2191.png' ) }}"></div>';
          body = body + div;
        }
        
        var filepath = window.location + window.filepath;
        var popupWin = window.open('', '_blank', 'width=638,height=1013');
        //var onload = window.print();
        popupWin.document.open();
        popupWin.document.write('<html><head><link rel="stylesheet" href="{{ $url }}/public/css/printstyle.css" type=></head><body onload="window.print();">' + body + '</html>');
        popupWin.document.close();
      
      
    }
  
  </script>

@endsection