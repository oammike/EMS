@extends('layouts.main')


@section('metatags')
  <title>Generate Employee DTR</title>
    <meta name="description" content="all teams">

@stop


@section('content')




<section class="content-header">

      <h1>
       
        <small></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li><a href="{{action('CampaignController@index')}}">Timekeeping</a></li>
        <li class="active">Generate DTR</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">

          <div class="box">
            <div class="box-heading"></div>
            <div class="box-body">

             
              <div class="row">
                <div class="col-lg-2"></div>
                <div class="col-lg-8">
                   <h3 class="text-center"><strong class="text-success">{{$totalUploads}}</strong> Uploaded Biometrics data<br/><small>available for DTR processing</small><br/><br/>  <a class="btn btn-default btn-md" data-toggle="modal" data-target="#purge"><i class="fa fa-trash"></i> Purge All Temp Data</a></h3>

                  <table id="biometrics" class="table table-bordered table-striped">
                      <thead>
                      <tr class="text-success">
                        
                        <th>Production Date</th>
                       
                        
                       
                       
                         
                        <th class="text-center">Action</th>

                         
                      </tr>
                      </thead>
                      <tbody>
                         <?php $icon = asset('public/img/logo-transparent.png'); $loader = asset('public/img/ajax-loader.gif');?>

                         @foreach ($currentBios as $data)

                         <tr id="row{{$data['id']}}">
                            
                            <td>{{ date('M d, Y [l]', strtotime($data['productionDates']) ) }}</td>
                           
                           
                            
                            <td class="text-center">

                              <a data-biometricsID="{{$data['id']}}" data-productionDate="{{$data['productionDates']}}" data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#generate{{$data['id']}}" href="#" class="generate btn btn-sm btn-success" ><i class="fa fa-calendar"></i> [{{$data['id']}} ] Generate All Employee DTR </a>
                              

                            </td>
                            
                            
                         </tr>

                         <div class="modal fade" id="generate{{$data['id']}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                              <div class="modal-content">
                                <div class="modal-header">
                                  
                                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                    <h4 class="modal-title text-success" id="myModalLabel"> <small>{{ date('M d, Y (l)', strtotime($data['productionDates']) ) }}</small> </h4>
                                  
                                </div>
                                <div class="modal-body-upload">
                                  <h4 class="text-center">
                                    <br/><br/>Generating employee DTRs for date:<br/><span class="text-success">{{ date('M d, Y [l]', strtotime($data['productionDates']) ) }}</span><br/><br/><span class="logo-mini"><img src="{{$icon}}" width="50" style="margin: 0 auto;" /></span>
                                    <br/><img src="{{$loader}}" style="margin:0 auto;" /><br/><br/><br/>This could take a while. <br/>Have a break, have a Kitkat... ;) </h4>
                                    <br/><br/>
                         
                                </div>

                                <div class="modal-body-generate"></div>
                                <div class="modal-footer no-border">
                                  
                                </div>
                              </div>
                            </div>
                          </div>

                  
                      @endforeach

                      
                      </tbody>
                      
                </table>

                </div>
                <div class="col-lg-2"></div>

              </div>
               

                 
                  

  

            </div>

          </div>

        </div>

      </div>

      <div class="modal fade" id="purge" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
          <h4 class="modal-title" id="myModalLabel"> Clear Temp Uploads</h4>
        
      </div>
      <div class="modal-body">
        <p> This will clear up temporary database from the uploads to free up some space</p>
      </div>
      <div class="modal-footer no-border">
        {{ Form::open(['route' => 'tempUpload.purge', 'method'=>'post','class'=>'btn-outline pull-right', 'id'=> 'purgeAll' ]) }}     
          <button type="submit" class="btn btn-default glyphicon-trash glyphicon ">Yes</button>
        
        <button type="button" class="btn btn-primary" data-dismiss="modal">Cancel</button>{{ Form::close() }}
      </div>
    </div>
  </div>
</div>



      

     

    </section>

@stop

@section('footer-scripts')
<script>
  $(function () {


$('.generate').on('click', function(e){
     
      var biometrics_id = $(this).attr('data-biometricsID');
      var productionDate = $(this).attr('data-productionDate');

       

      $.ajax({

        //async: true,
        type: "POST",
        url: "{{action('LogsController@saveDailyUserLogs')}}",
        data: {
          'productionDate': productionDate ,
          'biometrics_id': biometrics_id

        }, // high importance!
        
        success: function(response)
          {
            console.log("DTR generation success");
            console.log(response);
            $('.modal-body-upload').fadeOut();
            $('.modal-body-generate').delay(5000).fadeIn().html('<h4 class="text-center"><br/><span class="logo-mini"><img src="{{$icon}}" width="50" style="margin: 0 auto;" /></span> <br/><br/><strong class="text-danger">'+response.records+' </strong> Employee DTR successfully generated.<br/> </h4>');
     
             
          },
        error: function(jqXHR, textStatus, errorThrown)
          {
              // Handle errors here
              console.log('ERRORS from main ajax: ' + textStatus);
              $('.modal-body-upload').fadeOut('fast',function(){

                $('.modal-body-generate').fadeIn().html('<h4 class="text-center text-danger"><br/><br/><span class="logo-mini"><img src="{{$icon}}" width="50" style="margin: 0 auto;" /></span><br/><br/><br/> Error generating DTR.</h4><h5 class="text-center">Please try again.</h5> ');
         
              });
              
              // STOP LOADING SPINNER
          },
          complete: function(response)
          {
            console.log("complete");
            console.log(response);
            $("#row"+biometrics_id).fadeOut();

             $.ajax({

              //async: true,
              type: "POST",
              url: "{{action('TempUploadController@purgeThis')}}",
              data: { 'productionDate': productionDate}, // high importance!
              
              success: function(response)
                {
                  console.log(response);
                },
                complete: function(response){
                  console.log(response);
                  $('.modal-body-generate').append('<h5 class="text-center">Temporary biometrics data successfully purged to free up DB storage space.</h5>').delay(2000);
                  location.reload();


                }
              });

          }


        
    }); //end ajax

               
});//end .generate
    


                        
                       

      




    

  });


    
</script>
@stop