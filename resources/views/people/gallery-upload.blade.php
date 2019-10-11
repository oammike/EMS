@extends('layouts.main')

@section('metatags')
<title>Image Upload | EMS</title><!-- 
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" /> -->
  
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
                      <div class="panel panel-default">
                          <div class="panel-heading">
                              <h3 class="panel-title">Add Pictures to: <spa class="text-primary"> {{$gallery->name}}</spa></h3>
                          </div>
                          <div class="panel-body">
                              <br />
                              <p><span class="text-danger"><i class="fa fa-exclamation-circle"></i> Note: </span>Please limit your image uploads to a <strong>maximum of 1.5MB</strong> per image.<br/><br/></p>
                              <!-- <form id="form" accept-charset="UTF-8" enctype="multipart/form-data"> -->
                                {{ Form::open(['route' => 'usergallery.upload','id'=> 'form','name'=>'form','accept-charset'=>'UTF-8','enctype'=>"multipart/form-data" ]) }}
                               
                                  <div class="row">
                                    <div class="col-md-3" align="right"><h4>Select Images</h4></div>
                                    <div class="col-md-6">
                                          <input type="hidden" name="galleryID" value="{{$gallery->id}}" />
                                          <input type="file" name="file[]" id="file" accept="image/*" multiple /><br/><br/>
                                          <input type="submit" id="submit" name="upload" value="Upload" class="btn btn-lg btn-success" />
                                    </div>
                                    <div class="col-md-3"></div>
                                  </div>
                             
                              <br />
                              <div class="progress">
                                  <div class="progress-bar" aria-valuenow="" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                                      0%
                                  </div>
                              </div>
                              <br />
                              <div id="success" class="row"></div>
                              <br />
                              {{Form::close()}}
                          </div>
                      </div>

                     <br/><br/><br/><br/>
                </div><!--end box-primary-->
                <br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>
            </div>



             

             

          </div><!--end main row-->
      </section>
          



@endsection


@section('footer-scripts')

<script>
  $.widget.bridge('uibutton', $.ui.button);
</script> 

<script type="text/javascript" src="../../public/js/jquery.form.min.js"></script>


<!-- Page script -->
<script>
 
  $(document).ready(function(){

    $('form').ajaxForm({
        beforeSend:function(){
            $('#success').empty();
            $('.progress-bar').text('0%');
            $('.progress-bar').css('width', '0%');
        },
        uploadProgress:function(event, position, total, percentComplete){
            $('.progress-bar').text(percentComplete + '0%');
            $('.progress-bar').css('width', percentComplete + '0%');
        },
        success:function(data)
        {
            if(data.success)
            {
                $('#success').html('<div class="text-success text-center"><b>'+data.success+'</b><br/><br/><a href=\"{{action("GalleryController@show",$gallery->id)}}\" class="btn btn-md btn-primary"><i class="fa fa-picture-o" ></i> View Album</a></div><br /><br />');
                $('#success').append(data.image);
                $('.progress-bar').text('Uploaded');
                $('.progress-bar').css('width', '100%');
               
            }

        }
    });
});

   

</script>
<!-- end Page script -->






@stop