@extends('layouts.main')

@section('metatags')
<title>Gallery | Open Access EMS</title>
<link href="{{URL::asset('public/css/jquery.galereya.css')}}" rel="stylesheet"/>
    <!--[if lt IE 9]>
    <link rel="stylesheet" href="{{URL::asset('public/css/jquery.galereya.ie.css')}}">
    <![endif]-->

    
@endsection

@section('content')


  <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1><i class="fa fa-picture-o"></i> Open Access Gallery </h1>

      <ol class="breadcrumb">
        <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Gallery</li>
      </ol>
    </section>

     <section class="content" id='holder'>
      
       <div class="row">
        <div class="col-lg-12"style="padding-bottom: 16000px; display: block;">
          <h5 class="text-center"><i>"Life is like a camera <i class="fa fa-camera"></i>, <br/>
           you <strong>focus </strong> on the things that matter and <strong>capture</strong> the moments, <br/>
           <strong>develop</strong> from the <strong>negatives</strong>, and if things don't work out -- <br/>
           you just take another <strong>shot</strong>".</i></h5>

          <div id="gallery"></div>
     
          
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
                      $.getJSON("{{action('HomeController@getImages')}}", function(data) {
                          next(data);
                      });
                      
                  }
              })
    gallery.changeCategory("CS Week 2018");
    
    
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