@extends('layouts.main')


@section('metatags')
  <title>Upload Work Schedule</title>
    <meta name="description" content="profile page">

@stop


@section('content')




<section class="content-header">

      <h1>Upload Work Schedule<small></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ action('HomeController@index') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        
        <li class="active">Upload Work Schedule</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12" style="background: rgba(256, 256, 256, 0.5)">
           {{Form::open(['route' => 'biometrics.uploadSched','class'=>'col-lg-12', 'id'=>'uploadBio5', 'name'=>'uploadBio5', 'files'=>'true' ])}}<br/><br/>

                   <h5>Select CSV file to upload (*.csv):</h5>

                  <div id="alert-upload" style="margin-top:10px"></div>
                  <input type="file" name="biometricsData" id="biometricsData" class="form-control" />   <br/><br/> 
                  <div id="wait" style="display: none;" ><h3 class="text-primary"><i class="fa fa-calendar"></i> Uploading and processing work schedules. <br/><span style="font-size: 0.7em;"><br/>This could take a while depending on the number of employee schedules.<br/>Please wait...<img src="./public/css/img/loadingspin.gif" /></span></h3> </div>
                  <button id="uploadws" type="submit" class="btn btn-success btn-md pull-right" style="margin-right:5px" > <i class="fa fa-upload" ></i> Upload </button><br/><br/><br/>

          {{Form::close()}} </p><br/><br/><br/>
        </div>
       

      </div>

     

    </section>

@stop

@section('footer-scripts')
 <script src="{{ asset('/public/js/jquery-3.3.1.min') }}"></script>
<script>
$(function () {


  $('#uploadws').on('click', function(e)
    {
      //e.preventDefault(); e.stopPropagation();
      $('#wait').fadeIn();

     /* // Get form
        var form = $('#uploadBio5')[0];
        console.log(form);

    // Create an FormData object 

        var data = new FormData(form);
        console.log(data);
        var _token = "{{ csrf_token() }}";

    // If you want to add an extra field for the FormData
        data.append("_token", _token);

    // disabled the submit button
       

        $.ajax({
            type: "POST",
            enctype: 'multipart/form-data',
            url: "{{action('BiometricsController@uploadSched')}}",
            data: data,
            processData: false,
            contentType: false,
            cache: false,
            timeout: 600000,
            success: function (response) {

                console.log(response);
                  $('#wait').fadeOut();
                  $.notify("Work Schedules uploaded successfully.",{className:"success", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );
                  

            }
          });


   */

    });


 


    


  });
</script>
@stop