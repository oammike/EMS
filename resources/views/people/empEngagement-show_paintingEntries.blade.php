@extends('layouts.main')

@section('metatags')
<title>{{$engagement[0]->activity}}  | EMS</title>

<link href="../public/css/bootstrap-grid.min.css" rel="stylesheet" type="text/css">
  <link href="../public/css/bootstrap-reboot.min.css" rel="stylesheet" type="text/css">
  <link href="../public/css/bootstrap.css" rel="stylesheet" type="text/css">


  <script src="../public/js/jquery-3.3.1.min.js"></script>
  <link rel="stylesheet" href="../public/css/jquery.fancybox.min.css" />
  <script src="../public/js/jquery.fancybox.min.js"></script>

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
      <h1>&nbsp;</h1>
      <ol class="breadcrumb">
        <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="{{action('UserController@index')}}"> All Employees</a></li>
        <li class="active">My Team</li>
      </ol>
    </section>

     <section class="content">
      <!-- ******** THE DATATABLE ********** -->
          <div class="row">
             <div class="col-lg-12">

              <div class="box box-primary"  style="background: rgba(256, 256, 256, 0.4);padding:30px">

                 <div class="item" style="background-size:98%;background-position: top center; background-repeat: no-repeat; background-image: url('../storage/uploads/paintingContest3.jpg'); background-color: #f4f4f4; padding-left:50px; padding-top: 70%;padding-bottom: 5%" >
                    <h3 style="color:#fb7470">All Entries:</h3>

                    @for($i=1; $i<=9; $i++)
                    <article class="video">
                      <figure>
                        <a data-fancybox="gallery" data-file="Painting_Contest" href="../storage/uploads/paintingEntry_{{$i}}.jpg">
                        <img class="videoThumb" src="../storage/uploads/paintingEntry_{{$i}}.jpg"></a>
                      </figure>
                      <h5 class="videoTitle text-center">Entry #{{$i}}</h5>
                    </article>
                    @endfor
                    
                    
                   
                   <div style="width: 40%;" class="pull-right">
                      <h3 style="color:#fb7470">Criteria For Judging:</h3>
                      <table class="table table-hover" style="width:100%">
                        <tr>
                          <td>Relevance to the Theme </td>
                          <td>30%</td>
                        </tr>
                        <tr>
                          <td>Creativity &amp; Originality </td>
                          <td>35%</td>
                        </tr>
                        <tr>
                          <td>Visual Impact </td>
                          <td>35% </td>

                        </tr>
                        <tr>
                          <td>TOTAL </td>
                          <td> <strong>100%</strong></td>
                        </tr>
                      </table>
                      

                     <h3 style="color:#fb7470">Prizes:</h3>
                      <p  class="text-left">
                        Top 3 winners - <strong>Php 20,000 each</strong><br/>
                        Non-winning participants - <strong>Rewards Points</strong></p>
                                        
                    </div><br/><br/> <div class="clearfix"></div>


                   </div>

                                  


                  </div> 

                

                
                

               




              </div><!--end box-primary-->


             

             

          </div><!--end main row-->
      </section>

   

      
 

    



@endsection


@section('footer-scripts')

<script>
  $.widget.bridge('uibutton', $.ui.button);
</script>

<script type="text/javascript" src="../public/js/jquery.form.min.js"></script>

<!-- Page script -->
<script>

   $(document).ready(function(){

    
});



</script>
<!-- end Page script -->



@stop