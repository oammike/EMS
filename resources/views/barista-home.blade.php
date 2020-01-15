<!doctype html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, viewport-fit=cover">
  <meta name="apple-mobile-web-app-capable" content="yes" />
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <!-- Page Title -->
  <title>OAM Rewards - Barista</title>

  <!-- Compressed Styles -->
  <link href="{{ asset( 'public/css/barista/slides.min.css' ) }}" rel="stylesheet" type="text/css">

  <!-- Sweet Alert -->
  <link href="{{ asset( 'public/css/sweetalert2.css' ) }}" rel="stylesheet" type="text/css">

  <!-- Fonts and Material Icons -->
  <link rel="stylesheet" as="font" href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,600,700|Material+Icons"/>

  <style>
    .swal2-popup {
     font-size: 2.0rem !important;
    }
    .swal2-container {
      zoom: 1.5;
    }
    .swal2-icon {
      width: 5em !important;
      height: 5em !important;
      border-width: .25em !important;
    }
    .menu_slider_left{
      transform: rotate(90deg);
    }
    .menu_slider_right{
      transform: rotate(270deg);
    }
  </style>
 
</head>
<body class="slides horizontal simplifiedMobile animated">
    
<!-- SVG Library -->
<svg xmlns="http://www.w3.org/2000/svg" style="display:none">
  
  
  
  <symbol id="close" viewBox="0 0 30 30"><path d="M15 0c-8.3 0-15 6.7-15 15s6.7 15 15 15 15-6.7 15-15-6.7-15-15-15zm5.7 19.3c.4.4.4 1 0 1.4-.2.2-.4.3-.7.3s-.5-.1-.7-.3l-4.3-4.3-4.3 4.3c-.2.2-.4.3-.7.3s-.5-.1-.7-.3c-.4-.4-.4-1 0-1.4l4.3-4.3-4.3-4.3c-.4-.4-.4-1 0-1.4s1-.4 1.4 0l4.3 4.3 4.3-4.3c.4-.4 1-.4 1.4 0s.4 1 0 1.4l-4.3 4.3 4.3 4.3z"/></symbol>
  
  <symbol id="close-small" viewBox="0 0 11 11"><path d="M6.914 5.5l3.793-3.793c.391-.391.391-1.023 0-1.414s-1.023-.391-1.414 0l-3.793 3.793-3.793-3.793c-.391-.391-1.023-.391-1.414 0s-.391 1.023 0 1.414l3.793 3.793-3.793 3.793c-.391.391-.391 1.023 0 1.414.195.195.451.293.707.293s.512-.098.707-.293l3.793-3.793 3.793 3.793c.195.195.451.293.707.293s.512-.098.707-.293c.391-.391.391-1.023 0-1.414l-3.793-3.793z"/></symbol>

  <symbol id="arrow-left" viewBox="0 0 29 56"><path d="M28.7.3c.4.4.4 1 0 1.4l-26.3 26.3 26.3 26.3c.4.4.4 1 0 1.4-.4.4-1 .4-1.4 0l-27-27c-.4-.4-.4-1 0-1.4l27-27c.3-.3 1-.4 1.4 0z"/></symbol>
  
  <symbol id="arrow-right" viewBox="0 0 29 56"><path d="M.3 55.7c-.4-.4-.4-1 0-1.4l26.3-26.3-26.3-26.3c-.4-.4-.4-1 0-1.4.4-.4 1-.4 1.4 0l27 27c.4.4.4 1 0 1.4l-27 27c-.3.3-1 .4-1.4 0z"/></symbol>

  <symbol id="back" viewBox="0 0 20 20"><path d="M2.3 10.7l5 5c.4.4 1 .4 1.4 0s.4-1 0-1.4l-3.3-3.3h11.6c.6 0 1-.4 1-1s-.4-1-1-1h-11.6l3.3-3.3c.4-.4.4-1 0-1.4-.2-.2-.4-.3-.7-.3s-.5.1-.7.3l-5 5c-.2.2-.3.5-.3.7 0 .2.1.5.3.7z"/></symbol>
  
  <symbol id="menu" viewBox="0 0 18 18"><path d="M16 5h-14c-.6 0-1-.4-1-1 0-.5.4-1 1-1h14c.5 0 1 .4 1 1s-.4 1-1 1zm-14 5h14c.5 0 1-.4 1-1 0-.5-.4-1-1-1h-14c-.6 0-1 .4-1 1s.4 1 1 1zm14 3h-14c-.5 0-1 .4-1 1 0 .5.4 1 1 1h14c.5 0 1-.4 1-1s-.4-1-1-1z"/></symbol>
  
  <symbol id="share" viewBox="0 0 18 18"><path d="M16 8c-.6 0-1 .4-1 1v6h-12v-6c0-.6-.4-1-1-1s-1 .4-1 1v6c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2v-6c0-.6-.4-1-1-1zm-2.3-2.3c.4-.4.4-1 0-1.4l-4-4c-.4-.4-1-.4-1.4 0l-4 4c-.4.4-.4 1 0 1.4s1 .4 1.4 0l2.3-2.3v7.6c0 .6.4 1 1 1s1-.4 1-1v-7.6l2.3 2.3c.4.4 1 .4 1.4 0z"/></symbol>

  <symbol id="arrow-down" viewBox="0 0 24 24"><path d="M12 18c-.2 0-.5-.1-.7-.3l-11-10c-.4-.4-.4-1-.1-1.4.4-.4 1-.4 1.4-.1l10.4 9.4 10.3-9.4c.4-.4 1-.3 1.4.1.4.4.3 1-.1 1.4l-11 10c-.1.2-.4.3-.6.3z"/></symbol>
  
  <symbol id="arrow-up" viewBox="0 0 24 24"><path d="M11.9 5.9c.2 0 .5.1.7.3l11 10c.4.4.4 1 .1 1.4-.4.4-1 .4-1.4.1l-10.4-9.4-10.3 9.4c-.4.4-1 .3-1.4-.1-.4-.4-.3-1 .1-1.4l11-10c.1-.2.4-.3.6-.3z"/></symbol>
  
  <symbol id="arrow-top" viewBox="0 0 18 18"><path d="M15.7 7.3l-6-6c-.4-.4-1-.4-1.4 0l-6 6c-.4.4-.4 1 0 1.4.4.4 1 .4 1.4 0l4.3-4.3v11.6c0 .6.4 1 1 1s1-.4 1-1v-11.6l4.3 4.3c.2.2.4.3.7.3s.5-.1.7-.3c.4-.4.4-1 0-1.4z"/></symbol>
  
  <symbol id="play" viewBox="0 0 30 30"><path d="M7 30v-30l22 15z"/></symbol>
  
  <symbol id="chat" viewBox="0 0 18 18"><path d="M5,17c-0.2,0-0.3,0-0.4-0.1C4.2,16.7,4,16.4,4,16v-2H2c-1.1,0-2-0.9-2-2V3c0-1.1,0.9-2,2-2h14c1.1,0,2,0.9,2,2v9 c0,1.1-0.9,2-2,2H9.3l-3.7,2.8C5.4,16.9,5.2,17,5,17z M2,12h3.5C5.8,12,6,12.2,6,12.5V14l2.4-1.8C8.6,12.1,8.8,12,9,12h7V3H2V12z M13,7H5C4.4,7,4,6.6,4,6s0.4-1,1-1h8c0.6,0,1,0.4,1,1S13.6,7,13,7z M13,10H5c-0.6,0-1-0.4-1-1s0.4-1,1-1h8c0.6,0,1,0.4,1,1 S13.6,10,13,10z"/></symbol>

  <symbol id="mail" viewBox="0 0 18 18"><path d="M16 2h-14c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2v-10c0-1.1-.9-2-2-2zm0 2v.5l-7 4.3-7-4.4v-.4h14zm-14 10v-7.2l6.5 4c.1.1.3.2.5.2s.4-.1.5-.2l6.5-4v7.2h-14z"/></symbol>

</svg>

<!-- Panel Top #05 -->
<nav class="panel top">
  <div class="sections desktop">
    
  </div>
  <div class="sections compact hidden">
    <div class="left"><a href="#" title="OAM Rewards">OAM Rewards</a></div>
    <div class="right"><span class="button actionButton sidebarTrigger" data-sidebar-id="1"><svg><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#menu"></use></svg></span</div>
  </div>
</nav>


<!-- Slide 1 (#34) -->
<section class="slide fade-6 kenBurns" data-name="home">
  <div class="content">
    <div class="container">
      <div class="wrap">
      
        <div class="fix-12-12">
          <ul class="flex fixedSpaces verticalCenter reverse">
            <li class="col-6-12 left middle">
              <h1 class="ae-1 fromLeft">Welcome to OAM Rewards</h1>
              <p class="ae-2 fromLeft"><span class="opacity-8">Take a break from your strenous activities and take a sip of our expertly handcrafted coffee.</span></p>
              <a class="nextSlide button blue gradient ae-3 fromCenter cropLeft">Get Started</a>
            </li>
            <li class="col-6-12">
              <!-- <img class="ae-4" width="605" src="assets/img/iphones-34.png" alt="iPhones Thumbnail" data-action="zoom"/> -->
              <!-- propably would be nice to  insert a nice coffee cup graphic here 






              -->
            </li>
          </ul>
        </div>
        
      </div>
    </div>
  </div>
  <div class="background" style="background-image:url({{ asset( 'public/img/barista/beans.jpg' ) }})"></div>
</section>

<!-- Slide 2 (#26) -->
<section class="slide fade-6 kenBurns" data-name="menu">
  <div class="content">
    <div class="container">
      <div class="wrap">
        <div class="fix-12-12">

          <ul class="flex fixedSpaces verticalCenter reverse">
          <li class="col-1-12">
            <span href="#" id="bt_menu_left" data-slider-action="prev" data-slider-id="coffee_menu">
              <svg class="menu_slider_left" width="24" height="24">
                <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#arrow-down"></use>
              </svg>
            </span>
          </li>
        
          <li class="col-10-12">
            <div class="fix-12-12">
              
              <ul class="flex reverse verticalCenter margin-bottom-3">
                <li class="col-7-12 left cell-26">
                  <h1 class="ae-2 fromLeft" id="coffee_menu_title">{{ $rewards->first()->name }}</h1>
                  <div class="ae-3 fromLeft">
                    <p class="opacity-8" id="coffee_menu_desc">{{ $rewards->first()->description }}</p>
                  </div>
                  <div class="relative">
                    <div class="left">
                      <div class="ae-5 fromLeft">
                        <a class="nextSlide button blue gradient ae-3 fromCenter cropLeft">Claim</a>
                        <p class="tiny opacity-6">Cost: <span id="coffe_menu_cost">{{ $rewards->first()->category->tiers->average('cost') }}</span> Point</p>
                      </div>
                    </div>
                  </div>
                  
                </li>
                <li class="col-5-12">

                  <div class="fix-4-12 center">
                    <ul class="slider clickable ae-4 fromAbove" data-slider-id="coffee_menu">
                      @forelse($rewards as $key=>$reward)

                        <li class="@if ($key == 0) selected @endif" data-title="{{ $reward->name }}" data-desc="{{ $reward->description }}" data-id="{{ $reward->id }}" data-cost="{{ $reward->category->tiers->average('cost') }}" class="selected">
                          <img src="{{ url('/') }}/public/{{ $reward->attachment_image }}" alt="{{ $reward->name }} Thumbnail"/>
                        </li>

                      @empty
            
                        <li data-title="Still Brewing" data-desc="Sorry, our barista has not yet configured our menu." data-id="0" data-cost="free" class="selected"><img src="{{ asset( 'public/img/barista/empty.jpg' ) }}" alt="Empty Coffee Cup"/></li>
                    
                      @endforelse
                    </ul>
                    <ul class="controller dots ae-3 fromCenter margin-top-3" data-slider-id="coffee_menu">
                      @forelse($rewards as $key=>$reward)

                        <li class="dot @if ($key == 0) selected @endif"></li>

                      @empty
            
                        <li class="dot selected"></li>
                    
                      @endforelse
                    </ul>
                  </div>
                </li>
              </ul>

              <div class="fix-7-12">
                <p class="ae-1 fromBelow"><span class="opacity-5">Select Your Coffee</span></p>
              </div>

            </div>
          </li>
          

          <li class="col-1-12">
            <span id="bt_menu_right" data-slider-action="next" data-slider-id="coffee_menu">
              <svg class="menu_slider_right" width="24" height="24">
                <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#arrow-down"></use>
              </svg>
            </span>
          </li>
        </ul>

        </div>

      </div>
    </div>
  </div>
  <div class="background" style="background-image:url({{ asset( 'public/img/barista/menu.jpg' ) }})"></div>
</section>

<!-- Slide 5 (#91) -->
<section class="slide fade-6 kenBurns scanner" data-name="scanner">
  <div class="content">
    <div class="container">
      <div class="wrap">
      
        <div class="fix-12-12 toCenter">
          <div class="fix-7-12">
            <p class="margin-bottom-2 ae-1"><span class="opacity-6">Verification</p>
            <h1 class="ae-2 fromAbove margin-bottom-2">Tap your QR code to continue</h1>
            <div class="qrscanner" id="preview"></div>
            
            <p class="margin-top-2 ae-1">
              <a class="button white ae-4 fromCenter" id="bt_cancel">Cancel</a>
            </p>
          </div>
        </div>
                
      </div>
    </div>
  </div>
  <div class="background" style="background-image:url({{ asset( 'public/img/barista/secure.jpg' ) }})"></div>
</section>

<!-- Slide 7 (#95) -->
<section class="slide fade-6 kenBurns" data-name="download">
  <div class="content">
    <div class="container">
      <div class="wrap">
      
        <div class="fix-6-12">
          <h1 class="huge ae-1 margin-bottom-2">Thank You</h1>
          <p class="hero ae-2 margin-bottom-3"><span class="opacity-8">Your order has been received by our barista.</span></p>
          <p class="opacity-8 ae-3">Your name will be called once your coffee is ready.</p>
          <a class="button white ae-4 fromCenter" id="bt_cancel">OK</a>
        </div>
                
      </div>
    </div>
  </div>
  <div class="background" style="background-image:url({{ asset( 'public/img/barista/thanks.jpg' ) }})"></div>
</section>

<!-- Panel Bottom #01 -->
<nav class="panel bottom forceMobileView">
  <div class="sections desktop">
  </div>
  <div class="sections compact hidden">
    
  </div>
</nav>

<!-- Loading Progress Bar -->
<div class="progress-bar blue"></div>

<!-- jQuery 3.3.1 -->
  <script src="{{ asset( 'public/js/jquery.js' ) }}"></script>

  <!-- Compressed Scripts -->
  <script src="{{ asset( 'public/js/barista/plugins.js' ) }}" type="text/javascript"></script>
  <script src="{{ asset( 'public/js/barista/slides.js' ) }}" type="text/javascript"></script>

  <!-- Sweet Alert -->
  <script src="{{ asset( 'public/js/sweetalert2.js' ) }}"></script>

  <!-- Sweet Alert -->
  <script src="{{ asset( 'public/js/jsqr/jsqrscanner.nocache.js' ) }}"></script>
<!-- custom scripts -->
  <script>
    window.order_id = 0;
    window.attached = false;
    window.slideOn = false;
    window.qrscanner = null;

    $.ajaxSetup({
      headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    $('#bt_cancel').click(function(){
      Swal.fire({
        title: 'Confirm Cancellation',
        text: "Are you sure you want to cancel your order and return to the home page?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes'
      }).then((result) => {
        if (result.value) {
          window.order = 0;
          window.changeSlide(1);
        }
      })
    });

    window.addEventListener('cameraSlideOn', function (e) { 
      window.slideOn = true;
      if(window.attached == false){
        var scannerParentElement = document.getElementById("preview");
        window.qrscanner.appendTo(scannerParentElement);
        window.attached = true;
        console.log('scanner attached');
      }else{
        console.log('resuming qrscanner');
        window.qrscanner.resumeScanning();
      }
    }, false);

    window.addEventListener('cameraSlideOff', function (e) { 

      window.slideOn = false;
      if(window.attached == true && (window.qrscanner.isScanning() || window.qrscanner.isActive()) ) {
        console.log('stopping scanner');
        window.qrscanner.stopScanning();
      }
    }, false);

    function onQRCodeScanned(scannedText)
    {
      var micro = (Date.now() % 1000) / 1000;
      
      $.ajax({
        type: "POST",
        data: {
          order_id : window.order_id,
          code: scannedText
        },
        url : "{{ url('/create-order') }}",
        success : function(data){
          $('#content').LoadingOverlay("hide");
          if(data.success==false || data.success=="false"){
            Swal.fire(data.message);
          }
          if(data.success==true || data.success=="true"){
            window.changeSlide('increase');
          }
        },
        error: function(data){
          if(data.success==false || data.success=="false"){

            window.changeSlide(1);
            Swal.fire(data.message);
            
          }

        }
      });
    }
    
    //funtion returning a promise with a video stream
    function provideVideoQQ()
    {
        return navigator.mediaDevices.enumerateDevices()
        .then(function(devices) {
            var exCameras = [];
            devices.forEach(function(device) {
            if (device.kind === 'videoinput') {
              exCameras.push(device.deviceId)
            }
         });
            
            return Promise.resolve(exCameras);
        }).then(function(ids){
            if(ids.length === 0)
            {
              return Promise.reject('Could not find a webcam');
            }
            
            return navigator.mediaDevices.getUserMedia({
                video: {
                  'optional': [{
                    'sourceId': ids.length === 1 ? ids[0] : ids[1]//this way QQ browser opens the rear camera
                    }]
                }
            });        
        });                
    }  
  
    //this function will be called when Jsqrscanner is ready to use
    function JsQRScannerReady()
    {
        window.qrscanner = new JsQRScanner(onQRCodeScanned, provideVideoQQ);
        window.qrscanner.setSnapImageMaxSize(300);
        console.log('qrscanner initialized succesfully');
    }

    
  </script>

</body>
</html>
