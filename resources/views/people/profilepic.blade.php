@extends('layouts.main')

@section('metatags')
<title>Profile Pic | EMS </title>
<link href="{{URL::asset('public/css/cropper.css')}}" rel="stylesheet"/>
@endsection

@section('content')


  <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Edit Profile Pic
       
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">User Profile</li>
      </ol>
    </section>

     <section class="content">       
       
              <div class="row">
                <div class="col-md-9">
                  <!-- <h3>Demo:</h3> -->
                  <div class="img-container">
                    <img id="image" src="{{$imgfile}}" alt="Picture" width="90%" />
                  </div>
                </div>
                <div class="col-md-3">
                  <!-- <h3>Preview:</h3> -->
                  <div class="docs-preview clearfix">
                    <div class="img-preview preview-lg"></div>
                    <div class="img-preview preview-md"></div>
                    <div class="img-preview preview-sm"></div>
                    <div class="img-preview preview-xs"></div>
                  </div>

                  <!-- <h3>Data:</h3> -->
                  <div class="docs-data">
                    <div class="input-group input-group-sm">
                      <span class="input-group-prepend">
                        <label class="input-group-text" for="dataX">X</label>
                      </span>
                      <input type="text" class="form-control" id="dataX" placeholder="x">
                      <span class="input-group-append">
                        <span class="input-group-text">px</span>
                      </span>
                    </div>
                    <div class="input-group input-group-sm">
                      <span class="input-group-prepend">
                        <label class="input-group-text" for="dataY">Y</label>
                      </span>
                      <input type="text" class="form-control" id="dataY" placeholder="y">
                      <span class="input-group-append">
                        <span class="input-group-text">px</span>
                      </span>
                    </div>
                    <div class="input-group input-group-sm">
                      <span class="input-group-prepend">
                        <label class="input-group-text" for="dataWidth">Width</label>
                      </span>
                      <input type="text" class="form-control" id="dataWidth" placeholder="width">
                      <span class="input-group-append">
                        <span class="input-group-text">px</span>
                      </span>
                    </div>
                    <div class="input-group input-group-sm">
                      <span class="input-group-prepend">
                        <label class="input-group-text" for="dataHeight">Height</label>
                      </span>
                      <input type="text" class="form-control" id="dataHeight" placeholder="height">
                      <span class="input-group-append">
                        <span class="input-group-text">px</span>
                      </span>
                    </div>
                    <div class="input-group input-group-sm">
                      <span class="input-group-prepend">
                        <label class="input-group-text" for="dataRotate">Rotate</label>
                      </span>
                      <input type="text" class="form-control" id="dataRotate" placeholder="rotate">
                      <span class="input-group-append">
                        <span class="input-group-text">deg</span>
                      </span>
                    </div>
                    <div class="input-group input-group-sm">
                      <span class="input-group-prepend">
                        <label class="input-group-text" for="dataScaleX">ScaleX</label>
                      </span>
                      <input type="text" class="form-control" id="dataScaleX" placeholder="scaleX">
                    </div>
                    <div class="input-group input-group-sm">
                      <span class="input-group-prepend">
                        <label class="input-group-text" for="dataScaleY">ScaleY</label>
                      </span>
                      <input type="text" class="form-control" id="dataScaleY" placeholder="scaleY">
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-9 docs-buttons">
                  <!-- <h3>Toolbar:</h3> -->
                  <div class="btn-group">
                    <button type="button" class="btn btn-primary" data-method="setDragMode" data-option="move" title="Move">
                      <span class="docs-tooltip" data-toggle="tooltip" data-animation="false" title="$().cropper(&quot;setDragMode&quot;, &quot;move&quot;)">
                        <span class="fa fa-arrows"></span>
                      </span>
                    </button>
                    <button type="button" class="btn btn-primary" data-method="setDragMode" data-option="crop" title="Crop">
                      <span class="docs-tooltip" data-toggle="tooltip" data-animation="false" title="$().cropper(&quot;setDragMode&quot;, &quot;crop&quot;)">
                        <span class="fa fa-crop"></span>
                      </span>
                    </button>
                  </div>

                  <div class="btn-group">
                    <button type="button" class="btn btn-primary" data-method="zoom" data-option="0.1" title="Zoom In">
                      <span class="docs-tooltip" data-toggle="tooltip" data-animation="false" title="$().cropper(&quot;zoom&quot;, 0.1)">
                        <span class="fa fa-search-plus"></span>
                      </span>
                    </button>
                    <button type="button" class="btn btn-primary" data-method="zoom" data-option="-0.1" title="Zoom Out">
                      <span class="docs-tooltip" data-toggle="tooltip" data-animation="false" title="$().cropper(&quot;zoom&quot;, -0.1)">
                        <span class="fa fa-search-minus"></span>
                      </span>
                    </button>
                  </div>

                  <div class="btn-group">
                    <button type="button" class="btn btn-primary" data-method="move" data-option="-10" data-second-option="0" title="Move Left">
                      <span class="docs-tooltip" data-toggle="tooltip" data-animation="false" title="$().cropper(&quot;move&quot;, -10, 0)">
                        <span class="fa fa-arrow-left"></span>
                      </span>
                    </button>
                    <button type="button" class="btn btn-primary" data-method="move" data-option="10" data-second-option="0" title="Move Right">
                      <span class="docs-tooltip" data-toggle="tooltip" data-animation="false" title="$().cropper(&quot;move&quot;, 10, 0)">
                        <span class="fa fa-arrow-right"></span>
                      </span>
                    </button>
                    <button type="button" class="btn btn-primary" data-method="move" data-option="0" data-second-option="-10" title="Move Up">
                      <span class="docs-tooltip" data-toggle="tooltip" data-animation="false" title="$().cropper(&quot;move&quot;, 0, -10)">
                        <span class="fa fa-arrow-up"></span>
                      </span>
                    </button>
                    <button type="button" class="btn btn-primary" data-method="move" data-option="0" data-second-option="10" title="Move Down">
                      <span class="docs-tooltip" data-toggle="tooltip" data-animation="false" title="$().cropper(&quot;move&quot;, 0, 10)">
                        <span class="fa fa-arrow-down"></span>
                      </span>
                    </button>
                  </div>

                  <div class="btn-group">
                    <button type="button" class="btn btn-primary" data-method="rotate" data-option="-45" title="Rotate Left">
                      <span class="docs-tooltip" data-toggle="tooltip" data-animation="false" title="$().cropper(&quot;rotate&quot;, -45)">
                        <span class="fa fa-rotate-left"></span>
                      </span>
                    </button>
                    <button type="button" class="btn btn-primary" data-method="rotate" data-option="45" title="Rotate Right">
                      <span class="docs-tooltip" data-toggle="tooltip" data-animation="false" title="$().cropper(&quot;rotate&quot;, 45)">
                        <span class="fa fa-rotate-right"></span>
                      </span>
                    </button>
                  </div>

                  <div class="btn-group">
                    <button type="button" class="btn btn-primary" data-method="scaleX" data-option="-1" title="Flip Horizontal">
                      <span class="docs-tooltip" data-toggle="tooltip" data-animation="false" title="$().cropper(&quot;scaleX&quot;, -1)">
                        <span class="fa fa-arrows-h"></span>
                      </span>
                    </button>
                    <button type="button" class="btn btn-primary" data-method="scaleY" data-option="-1" title="Flip Vertical">
                      <span class="docs-tooltip" data-toggle="tooltip" data-animation="false" title="$().cropper(&quot;scaleY&quot;, -1)">
                        <span class="fa fa-arrows-v"></span>
                      </span>
                    </button>
                  </div>

                  <div class="btn-group">
                    <button type="button" class="btn btn-primary" data-method="crop" title="Crop">
                      <span class="docs-tooltip" data-toggle="tooltip" data-animation="false" title="$().cropper(&quot;crop&quot;)">
                        <span class="fa fa-check"></span>
                      </span>
                    </button>
                    <button type="button" class="btn btn-primary" data-method="clear" title="Clear">
                      <span class="docs-tooltip" data-toggle="tooltip" data-animation="false" title="$().cropper(&quot;clear&quot;)">
                        <span class="fa fa-remove"></span>
                      </span>
                    </button>
                  </div>

                  <div class="btn-group">
                    <button type="button" class="btn btn-primary" data-method="disable" title="Disable">
                      <span class="docs-tooltip" data-toggle="tooltip" data-animation="false" title="$().cropper(&quot;disable&quot;)">
                        <span class="fa fa-lock"></span>
                      </span>
                    </button>
                    <button type="button" class="btn btn-primary" data-method="enable" title="Enable">
                      <span class="docs-tooltip" data-toggle="tooltip" data-animation="false" title="$().cropper(&quot;enable&quot;)">
                        <span class="fa fa-unlock"></span>
                      </span>
                    </button>
                  </div>

                  <div class="btn-group">
                    <button type="button" class="btn btn-primary" data-method="reset" title="Reset">
                      <span class="docs-tooltip" data-toggle="tooltip" data-animation="false" title="$().cropper(&quot;reset&quot;)">
                        <span class="fa fa-refresh"></span>
                      </span>
                    </button>
                    <label class="btn btn-primary btn-upload" for="inputImage" title="Upload image file">
                      <input type="file" class="sr-only" id="inputImage" name="file" accept=".jpg,.jpeg,.png,.gif,.bmp,.tiff">
                      <span class="docs-tooltip" data-toggle="tooltip" data-animation="false" title="Import image with Blob URLs">
                        <span class="fa fa-upload"></span>
                      </span>
                    </label>
                    <button type="button" class="btn btn-primary" data-method="destroy" title="Destroy">
                      <span class="docs-tooltip" data-toggle="tooltip" data-animation="false" title="$().cropper(&quot;destroy&quot;)">
                        <span class="fa fa-power-off"></span>
                      </span>
                    </button>
                  </div>

                  <div class="btn-group btn-group-crop"><br/>
                    <button type="button" class="btn btn-lg btn-success" data-method="getCroppedCanvas" data-option="{ &quot;maxWidth&quot;: 700, &quot;maxHeight&quot;: 700,&quot;imageSmoothingEnabled&quot;: &quot;true&quot;,&quot;imageSmoothingQuality&quot;: &quot;high&quot; }" data-toggle="modal" data-target="#getCroppedCanvasModal"><i class="fa fa-save"></i>
                      <span class="docs-tooltip" data-toggle="tooltip" data-animation="false" title="$().cropper(&quot;getCroppedCanvas&quot;, { maxWidth: 500, maxHeight: 500 })">
                       Save Profile Pic
                      </span>
                    </button>
                    <a href="{{action('UserController@show',$id)}}" class="btn btn-default btn-lg pull-right" style="margin-left: 5px">View Profile</a>
                    
                  </div>


                  <!-- Show the cropped image in modal -->
                  <div class="modal fade docs-cropped" id="getCroppedCanvasModal" aria-hidden="true" aria-labelledby="getCroppedCanvasTitle" role="dialog" tabindex="-1">
                    <div class="modal-dialog" style="width: 750px">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title" id="getCroppedCanvasTitle">New Profile Pic</h5>
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                          </button>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                          <input type="hidden" value="{{$id}}.jpg" id="idpic" />
                          <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
                          <a class="btn btn-primary" id="download" href="javascript:void(0);" download="{{$id}}.jpg">Save</a>
                        </div>
                      </div>
                    </div>
                  </div><!-- /.modal -->

                  <!-- <button type="button" class="btn btn-secondary" data-method="getData" data-option data-target="#putData">
                    <span class="docs-tooltip" data-toggle="tooltip" data-animation="false" title="$().cropper(&quot;getData&quot;)">
                      Get Data
                    </span>
                  </button>
                  <button type="button" class="btn btn-secondary" data-method="setData" data-target="#putData">
                    <span class="docs-tooltip" data-toggle="tooltip" data-animation="false" title="$().cropper(&quot;setData&quot;, data)">
                      Set Data
                    </span>
                  </button>
                  <button type="button" class="btn btn-secondary" data-method="getContainerData" data-option data-target="#putData">
                    <span class="docs-tooltip" data-toggle="tooltip" data-animation="false" title="$().cropper(&quot;getContainerData&quot;)">
                      Get Container Data
                    </span>
                  </button>
                  <button type="button" class="btn btn-secondary" data-method="getImageData" data-option data-target="#putData">
                    <span class="docs-tooltip" data-toggle="tooltip" data-animation="false" title="$().cropper(&quot;getImageData&quot;)">
                      Get Image Data
                    </span>
                  </button>
                  <button type="button" class="btn btn-secondary" data-method="getCanvasData" data-option data-target="#putData">
                    <span class="docs-tooltip" data-toggle="tooltip" data-animation="false" title="$().cropper(&quot;getCanvasData&quot;)">
                      Get Canvas Data
                    </span>
                  </button>
                  <button type="button" class="btn btn-secondary" data-method="setCanvasData" data-target="#putData">
                    <span class="docs-tooltip" data-toggle="tooltip" data-animation="false" title="$().cropper(&quot;setCanvasData&quot;, data)">
                      Set Canvas Data
                    </span>
                  </button>
                  <button type="button" class="btn btn-secondary" data-method="getCropBoxData" data-option data-target="#putData">
                    <span class="docs-tooltip" data-toggle="tooltip" data-animation="false" title="$().cropper(&quot;getCropBoxData&quot;)">
                      Get Crop Box Data
                    </span>
                  </button>
                  <button type="button" class="btn btn-secondary" data-method="setCropBoxData" data-target="#putData">
                    <span class="docs-tooltip" data-toggle="tooltip" data-animation="false" title="$().cropper(&quot;setCropBoxData&quot;, data)">
                      Set Crop Box Data
                    </span>
                  </button> -->
                  
                 <!--  <textarea type="text" class="form-control" id="putData" rows="1" placeholder="Get data to here or set data with this value"></textarea> -->
                </div><!-- /.docs-buttons -->

                <div class="col-md-3 docs-toggles">
                  <!-- <h3>Toggles:</h3> -->
                  <div class="btn-group d-flex flex-nowrap" data-toggle="buttons">
                    
                    <label class="btn btn-primary">
                      <input type="radio" class="sr-only" id="aspectRatio2" name="aspectRatio" value="1">
                      <span class="docs-tooltip" data-toggle="tooltip" data-animation="false" title="aspectRatio: 1 / 1">
                        1:1
                      </span>
                    </label>
                   
                  </div>

                 
                  

                 

                </div><!-- /.docs-toggles -->
              </div>
            
               
               

     


                




       
     </section>
          



@endsection


@section('footer-scripts')
 <script src="{{asset('public/js/jquery-3.3.1.slim.min.js')}}"></script>
 <script src="{{asset('public/js/bootstrap.bundle.min.js')}}"></script>
 <script src="{{asset('public/js/cropper.js')}}"></script>
<!--  <script src="{{asset('public/js/jquery-cropper.common.js')}}"></script> -->
 <script src="{{asset('public/js/jquery-cropper.js')}}"></script>
 <script src="{{asset('public/js/cropper-main.js')}}"></script>


<!-- Page script -->
<script>
  $(function () 
  {
    'use strict';

    // var $image = $('#image');

    // $image.cropper({
    //   aspectRatio: 1 / 1,
    //   crop: function(event) {
    //     console.log(event.detail.x);
    //     console.log(event.detail.y);
    //     console.log(event.detail.width);
    //     console.log(event.detail.height);
    //     console.log(event.detail.rotate);
    //     console.log(event.detail.scaleX);
    //     console.log(event.detail.scaleY);
    //   }
    // });

    // var cropper = $image.data('cropper');

    cropper.getCroppedCanvas();

    cropper.getCroppedCanvas({
      width: 160,
      height: 90,
      minWidth: 256,
      minHeight: 256,
      maxWidth: 4096,
      maxHeight: 4096,
      fillColor: '#fff',
      imageSmoothingEnabled: false,
      imageSmoothingQuality: 'high',
    });

    // Upload cropped image to server if the browser supports `HTMLCanvasElement.toBlob`
    cropper.getCroppedCanvas().toBlob((blob) => {
      const formData = new FormData();

      formData.append('croppedImage', blob);

      // Use `jQuery.ajax` method
      // $.ajax('/path/to/upload', {
      //   method: "POST",
      //   data: formData,
      //   processData: false,
      //   contentType: false,
      //   success() {
      //     console.log('Upload success');
      //   },
      //   error() {
      //     console.log('Upload error');
      //   },
      // });

      console.log(formData);


    });


});

 

  /*
   * Custom Label formatter
   * ----------------------
   */
  function labelFormatter(label, series) {
    return '<div style="font-size:13px; text-align:center; padding:2px; color: #fff; font-weight: 600;">'
        + label
        + "<br>"
        + Math.round(series.percent) + "%</div>";
  }
</script>
<!-- end Page script -->


@stop