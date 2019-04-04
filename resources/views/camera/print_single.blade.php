@extends('layouts.id-trainee')
@section('content')
  <div id="id_wrapper">
    <div id="id_container">
      
      <img src="{{ asset( 'storage/uploads/id/'.$user->id.'.png' ) }}" id="foreground" style="display: block; opacity: 1"/>
      
    </div>
  </div>
  <div id="controls">
    <div class="section right">
      <h6>Options</h6>
      <div class="row">
        <form class="col s8">  
          
          <div class="input-field col s5 right">
            <div class="row">
              <a class="waves-effect waves-light btn-large" href="javascript:printme();"><i class="material-icons left">print</i>Print</a>
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
  window.filepath = [];
  
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
    
    function pad(num, size){ return ('0000' + num).substr(-size); } 
    
  
    function printme(){
      
        var body = "";
        
          var div = '<div><img src="{{ asset( 'storage/uploads/id/'.$user->id.'.png' ) }}"></div>';
          body = body + div;
        
        
        var filepath = window.location + window.filepath;
        var popupWin = window.open('', '_blank', 'width=638,height=1013');
        //var onload = window.print();
        popupWin.document.open();
        popupWin.document.write('<html><head><link rel="stylesheet" href="{{ $url }}/public/css/printstyle.css" type=></head><body onload="window.print();">' + body + '</html>');
        popupWin.document.close();
      
      
    }
  
  </script>

@endsection