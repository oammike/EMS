@extends('layouts.barista')
@section('content')
  
  <video autoplay muted playsinline id="preview" width="213" height="120"></video>
  
  <div >
  
  <script type="text/javascript" src="{{ asset( 'public/js/jquery.js' ) }}"></script>
  <script type="text/javascript" src="{{ asset( 'public/js/jquery.loading.min.js' ) }}"></script>
  <script type="text/javascript" src="{{ asset( 'public/js/instascan.min.js' ) }}"></script>
  <script>
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });
    let opts = {
      continuous: true,
      video: document.getElementById('preview'),
      mirror: true,
      refractoryPeriod: 10000,
      scanPeriod: 2
    };
    
    
    let scanner = new Instascan.Scanner(opts);
    
    scanner.addListener('scan', function (content) {
      console.log("posting: "+content);
      $('#content').LoadingOverlay("show");
      var micro = (Date.now() % 1000) / 1000;
      
      $.ajax({
        type: "GET",
        url : "{{ url('/print-order') }}/"+content+"?m="+micro,
        success : function(data){
          console.log(data);
          /*
          var address = 'http://192.168.4.180/cgi-bin/epos/service.cgi?devid=local_printer&timeout=60000';
          
          var builder = new epson.ePOSBuilder();
          builder.addTextAlign(builder.ALIGN_CENTER);
          builder.addText('OAM Rewards\n');
          builder.addText('Mark Lester Bambico (5051714)');
          builder.addFeed();
          builder.addTextAlign(builder.ALIGN_LEFT);
          builder.addText('Item: Death Wish Coffee (150ml)');
          builder.addTextAlign(builder.ALIGN_RIGHT);
          builder.addTextPosition(475);
          builder.addText('34');
          builder.addTextPosition(0);
          builder.addFeed();
          builder.addText('Remaining Credits:');
          builder.addTextPosition(475);
          builder.addTextAlign(builder.ALIGN_RIGHT);
          builder.addText('34');
          
          var epos = new epson.ePOSPrint(address);
          epos.onreceive = function (res) { alert(res.success); };
          epos.onerror = function (err) { alert(err.status); };
          epos.oncoveropen = function () { alert('coveropen'); };
          epos.send(builder.toString());
          */
          $('#content').LoadingOverlay("hide");
          if(data.success==false || data.success=="false"){
            alert(data.message);
          }
        },
        error: function(data){
          if(data.success==false || data.success=="false"){
            alert(data.message);
          }
          console.log(data);
        }
      });
    });
    Instascan.Camera.getCameras().then(function (cameras) {
      if (cameras.length > 0) {
        scanner.start(cameras[0]);
      } else {
        console.error('No cameras found.');
      }
    }).catch(function (e) {
      console.error(e);
    });
    
    
  </script>

@endsection