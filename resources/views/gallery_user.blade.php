@extends('layouts.main')

@section('metatags')
<title> {{$gallery->name}} | Open Access EMS</title>
<link href="{{URL::asset('public/css/jquery.galereya.css')}}" rel="stylesheet"/>
    <!--[if lt IE 9]>
    <link rel="stylesheet" href="{{URL::asset('public/css/jquery.galereya.ie.css')}}">
    <![endif]-->

    
@endsection

@section('content')


  <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1><i class="fa fa-picture-o"></i> {{$gallery->name}}  <a href="{{action('GalleryController@contribute',$id)}}" class="btn btn-primary btn-xs"><i class="fa fa-upload"></i> Upload Images</a></h1>

      <ol class="breadcrumb">
        <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Gallery</li>
      </ol>
    </section>

     <section class="content" id='holder'>
      
       <div class="row">
        <div class="col-lg-12"style="padding-bottom: 28000px; display: block;">
          <h5 class="text-center"><i>"Life is like a camera <i class="fa fa-camera"></i>, <br/>
           you <strong>focus </strong> on the things that matter and <strong>capture</strong> the moments, <br/>
           <strong>develop</strong> from the <strong>negatives</strong>, and if things don't work out -- <br/>
           you just take another <strong>shot</strong>".</i></h5>

          <div id="gallery">
            @if (count($allImg) > 0)
            <h2 id="loader" class="text-center" style="position: absolute; top: 0; left:auto; width: 100%"><br/><br/><br/><br/>
              Loading all <span class="text-danger"><strong>{{count($allImg)}}</strong> </span> images. Please wait...<i class="fa fa-refresh"></i> <br/>
             </h2>

             @else

              <h2 class="text-center" style="position: absolute; top: 0; left:auto; width: 100%"><br/><br/><br/><br/>
              <i class="fa fa-info-circle"></i> No image found for this album.  <br/><br/>
              <a href="{{action('GalleryController@contribute',$id)}}" class="btn btn-primary btn-md"><i class="fa fa-upload"></i> Upload Images</a>
             </h2>

             @endif
          </div>
     
          
        </div>
           


       </div>
 




       
     </section>
          



@endsection


@section('footer-scripts')

<script>
  $.widget.bridge('uibutton', $.ui.button);
</script>

<script src="{{URL::asset('public/js/jquery.galereya.min.js')}}"></script>

    

<!-- Page script -->
<script>


  $(function () {
   'use strict';

     var gallery = $('#gallery').galereya({
                  load: function(next) {

                    

                     
                      $.getJSON("{{action('GalleryController@getUploads',['album'=>$id])}}", function(data) {
                          next(data);
                      });
                     

                   
                      
                  }
              }); 
    
    
    
    $('#gallery').on('click','li.galereya-cats-item', function(){
      var h = $('#gallery').height();
      console.log("height:");
      console.log(h);
      //$('#holder').css({'min-height':})
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