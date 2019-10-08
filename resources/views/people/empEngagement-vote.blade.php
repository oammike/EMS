@extends('layouts.main')

@section('metatags')
<title>All Entries: Employee Engagement Activities | EMS</title>
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
                





              </div><!--end box-primary-->


             

             

          </div><!--end main row-->
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

   $('#submit').on('click',function(){

    var items = $('#entry .form-control').map(function( i, e ) {
                  return $( e ).val();
                }).get();
    var itemIDs = $('#entry .form-control').map(function( i, e ) {
                  return $( e ).attr('data-itemID');
                }).get();
    var _token = "{{ csrf_token() }}";

    $.ajax({

                url:"{{action('EngagementController@saveEntry')}}",
                type:'POST',
                data:{

                  'engagement_id': "{{$id}}",
                  'items': items,
                  'itemIDs': itemIDs,
                  
                  _token: _token

                },
                error: function(response)
                { console.log("Error saving entry: ");
                  console.log(response);
                  $.notify("Error processing request. Please check all submitted fields and try again.",{className:"error", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} ); 
                  return false;
                },
                success: function(response)
                {
                  console.log(response);
                  $.notify("Entry submitted. \nThank you for participating.",{className:"success", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );
                  $('#submit').fadeOut();

                }

          });

   });

  
       
        
      
      
   });

   

</script>
<!-- end Page script -->



@stop