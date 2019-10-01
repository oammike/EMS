@extends('layouts.main')

@section('metatags')
<title>Video Gallery | Open Access EMS</title>

    <!--[if lt IE 9]>
    <link rel="stylesheet" href="{{URL::asset('public/css/jquery.galereya.ie.css')}}">
    <![endif]-->
<script src="//code.jquery.com/jquery-3.3.1.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css" />
<script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"></script>


<style type="text/css">
  /* First make sure the video thumbnail images are responsive. */

  img {
    max-width: 100%;
    height: auto;
  }
  
  /* 
  This is the starting grid for each video with thumbnails 4 across for the largest screen size.
  It's important to use percentages or there may be gaps on the right side of the page. 
  */

  .video {
    background: #fff;
    padding-bottom: 20px;
    box-shadow: 0 1px 1px rgba(0, 0, 0, 0.15);
    width: 23%; /* Thumbnails 4 across */
    margin: 1%;
    float: left;
  }

   /* 
   These keep the height of each video thumbnail consistent between YouTube and Vimeo.
   Each can have thumbnail sizes that vary by 1px and are likely break your layout. 
   */

  .video figure {
    height: 0;
    padding-bottom: 56.25%;
    overflow: hidden;

    .video figure a {
      display: block;
      margin: 0;
      padding: 0;
      border: none;
      line-height: 0;
    }
  }

  /* Media Queries - This is the responsive grid. */

  @media (max-width: 1024px) {
    .video {
      width: 31.333%; /* Thumbnails 3 across */
    }
  }

  @media (max-width: 600px) {
    .video {
      width: 48%; /* Thumbnails 2 across */
    }
  }

  @media (max-width: 360px) {
    .video {
      display: block;
      width: 96%; /* Single column view. */
      margin: 2%; /* The smaller the screen, the smaller the percentage actually is. */
      float: none;
    }
  }

  /* These are my preferred rollover styles. */

  .video img {
    width: 100%;
    opacity: 1;
  }

  .video img:hover, .video img:active, .video img:focus {
    opacity: 0.75;
  }

</style>    
@endsection

@section('content')


  <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1><i class="fa fa-video-camera"></i> Open Access Videos </h1>

      <ol class="breadcrumb">
        <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Gallery</li>
      </ol>
    </section>

     <section class="content" id='holder'>
      
       <div class="row">
        <div class="col-lg-12">
          <br/><br/>
          
          
          
          <article class="video">
            <figure>
              <a data-fancybox="gallery" data-file="intlTranslationDay" href="http://172.17.0.2/evaluation/storage/uploads/intlTranslationDay.mp4">
              <img class="videoThumb" src="http://172.17.0.2/evaluation/storage/uploads/sshot-intlTranslationDay.jpg"></a>
            </figure>
            <h5 class="videoTitle text-center">International Translation Day [09.30]</h5>
          </article>

          <article class="video">
            <figure>
              <a data-fancybox="gallery" data-file="worldgratitude" href="http://172.17.0.2/evaluation/storage/uploads/worldgratitude.mp4">
              <img class="videoThumb" src="http://172.17.0.2/evaluation/storage/uploads/sshot-worldgratitude.jpg"></a>
            </figure>
            <h5 class="videoTitle text-center">World Gratitude Day [09.21]</h5>
          </article>

          <article class="video">
            <figure>
              <a data-fancybox="gallery" data-file="ribboncutting" href="http://172.17.0.2/evaluation/storage/uploads/ribboncutting.mp4">
              <img class="videoThumb" src="http://172.17.0.2/evaluation/storage/uploads/sshot-ribboncutting.jpg"></a>
            </figure>
            <h5 class="videoTitle text-center">G2 Office Ribbon Cutting [07.15]</h5>
          </article>

          <article class="video">
            <figure>
              <a data-fancybox="gallery" data-file="zumba-07-09" href="http://172.17.0.2/evaluation/storage/uploads/zumba-07-09.mp4">
              <img class="videoThumb" src="http://172.17.0.2/evaluation/storage/uploads/sshot-zumba-07-09.jpg"></a>
            </figure>
            <h5 class="videoTitle text-center">Zumba sessions [07.09]</h5>
          </article>

          <article class="video">
            <figure>
              <a data-fancybox="gallery" data-file="henry2019" href="http://172.17.0.2/evaluation/storage/uploads/henry2019.mp4">
              <img class="videoThumb" src="http://172.17.0.2/evaluation/storage/uploads/sshot-henry.jpg"></a>
            </figure>
            <h5 class="videoTitle text-center">Message from Henry [05.24]</h5>
          </article>

          <article class="video">
            <figure>
              <a data-fancybox="gallery" data-file="wespeak" href="http://172.17.0.2/evaluation/storage/uploads/wespeak.mp4">
              <img class="videoThumb" src="http://172.17.0.2/evaluation/storage/uploads/sshot-wespeak.jpg"></a>
            </figure>
            <h5 class="videoTitle text-center">We Speak Your Language [05.24]</h5>
          </article>

          <article class="video">
            <figure>
              <a data-fancybox="gallery" data-file="cinco" href="http://172.17.0.2/evaluation/storage/uploads/cinco.mp4">
              <img class="videoThumb" src="http://172.17.0.2/evaluation/storage/uploads/sshot-cinco.jpg"></a>
            </figure>
            <h5 class="videoTitle text-center">Cinco de Mayo [05.05]</h5>
          </article>

          <article class="video">
            <figure>
              <a data-fancybox="gallery" data-file="happyhour" href="http://172.17.0.2/evaluation/storage/uploads/OA-happyhour.webm">
              <img class="videoThumb" src="http://172.17.0.2/evaluation/storage/uploads/sshot-OA-happyhour.jpg"></a>
            </figure>
            <h5 class="videoTitle text-center">Happy Hour [04.20]</h5>
          </article>

          <article class="video">
            <figure>
              <a data-fancybox="gallery" data-file="zumba_04-10" href="http://172.17.0.2/evaluation/storage/uploads/zumba_04-10_upload.webm">
              <img class="videoThumb" src="http://172.17.0.2/evaluation/storage/uploads/sshot-zumba_04-10_upload.jpg"></a>
            </figure>
            <h5 class="videoTitle text-center">Zumba sessions [04.10]</h5>
          </article>

          <article class="video">
            <figure>
              <a data-fancybox="gallery" data-file="zumba_04-03" href="http://172.17.0.2/evaluation/storage/uploads/zumba_04-03.webm">
              <img class="videoThumb" src="http://172.17.0.2/evaluation/storage/uploads/sshot-zumba_04-03.jpg"></a>
            </figure>
            <h5 class="videoTitle text-center">Zumba sessions [04.03]</h5>
          </article>

          <article class="video">
            <figure>
              <a data-fancybox="gallery" data-file="getphysical" href="http://172.17.0.2/evaluation/storage/uploads/getphysical.webm">
              <img class="videoThumb" src="http://172.17.0.2/evaluation/storage/uploads/sshot-getphysical.jpg"></a>
            </figure>
            <h5 class="videoTitle text-center">Let's Get Physical [03.26]</h5>
          </article>

          <article class="video">
            <figure>
              <a data-fancybox="gallery" data-file="zumba2" href="http://172.17.0.2/evaluation/storage/uploads/zumba2.webm">
              <img class="videoThumb" src="http://172.17.0.2/evaluation/storage/uploads/sshot-zumba2.jpg"></a>
            </figure>
            <h5 class="videoTitle text-center">Zumba sessions [03.08]</h5>
          </article>

          <article class="video">
            <figure>
              <a data-fancybox="gallery" data-file="hobbymonth" href="http://172.17.0.2/evaluation/storage/uploads/hobbymonth.webm">
              <img class="videoThumb" src="http://172.17.0.2/evaluation/storage/uploads/sshot-hobbyists.png"></a>
            </figure>
            <h5 class="videoTitle text-center">Hobby Month </h5>
          </article>

           <article class="video">
            <figure>
              <a data-fancybox="gallery" data-file="sde" href="http://172.17.0.2/evaluation/storage/uploads/sde-back_to_the_90s.webm">
              <img class="videoThumb" src="http://172.17.0.2/evaluation/storage/uploads/thumb-backto90s-143.jpg"></a>
            </figure>
            <h5 class="videoTitle text-center">Back to 90s SDE</h5>
          </article>

          <article class="video">
            <figure>
              <a data-fancybox="gallery" data-file="teaser90s" href="http://172.17.0.2/evaluation/storage/uploads/teaser90s.webm">
              <img class="videoThumb" src="http://172.17.0.2/evaluation/storage/uploads/thumb-backto90s-95.jpg"></a>
            </figure>
            <h5 class="videoTitle text-center">Back to 90s teaser</h5>
          </article>

     
          
        </div>
           


       </div>
 




       
     </section>
          



@endsection


@section('footer-scripts')

<script>
  $.widget.bridge('uibutton', $.ui.button);
</script>



    

<!-- Page script -->
<script>


  $(function () {
   'use strict';

     $('article a').on('click',function(){
      var vg = $(this).attr('data-file');
      $.ajax({
                url: "{{action('HomeController@logAction',['action'=>'VG'])}}",
                type: "GET",
                data: {'action': 'VG','vg':vg},
                success: function(response){
                          console.log(response);

              }

        });

     })

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