@extends('layouts.barista')
@section('content')
  
    
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
              <a class="waves-effect waves-light btn-large" href="javascript:verify();"><i class="material-icons left">print</i>Scan</a>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
  
  
  
  
  <script type="text/javascript" src="{{ asset( 'public/js/jquery.js' ) }}"></script>
  <script type="text/javascript" src="{{ asset( 'public/js/veu-dev.js' ) }}"></script>
  
  <script type="text/javascript" src="{{ asset( 'public/js/materialize.min.js' ) }}"></script>
    

  
  <script>
  

  
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
    
    
  </script>

@endsection