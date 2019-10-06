@extends('layouts.main')

@section('metatags')
<title>Employee Engagement Activities | EMS</title>
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
                
                {!! $engagement[0]->content!!}


                @if( $engagement[0]->withVoting === 1 )
                <hr/>
                <div class="row">
                  <div class="col-sm-6">
                    <h3 class="text-primary">Vote Now</h3>
                  </div>
                  
                  <div id="entry" class="col-sm-6" style="background: rgba(256, 256, 256, 0.7);padding:30px" >
                    <h3 class="text-primary">Submit Your Entry:</h3>

                    @foreach($engagement as $element)
                    <label style="padding-top: 20px">{{$element->label}} </label>

                        @if( $element->dataType == 'TXT' )
                        <input type="text" name="item_{{$element->itemID}}" data-itemID="{{$element->itemID}}" class="form-control" />

                        @endif


                        @if( $element->dataType == 'PAR' )
                        <textarea name="item_{{$element->itemID}}" data-itemID="{{$element->itemID}}" class="form-control"></textarea>
                        @endif



                    @endforeach
                    <a id="submit" class="btn btn-lg btn-success pull-right" style="margin-top: 20px"> Submit</a>
                  </div>
                </div>

                @endif




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