@extends('layouts.main')

@section('metatags')
<title>Employees | OAMPI Evaluation System</title>

<style type="text/css">
/* Sortable items */

.sortable-list {
  background: none; /* #fcedc6;*/
  list-style: none;
  margin: 0;
  min-height: 60px;
  padding: 10px;
}
.sortable-item {
  background-color: #fcedc6;
  
  cursor: move;
  
  font-weight: bold;
  margin: 2px;
  padding: 10px 0;
  text-align: center;
}

/* Containment area */

#containment {
  background-color: #FFA;
  height: 230px;
}


/* Item placeholder (visual helper) */

.placeholder {
  background-color: #ccc;
  border: 3px dashed #fcedc0;
  min-height: 150px;
  width: 180px;
  float: left;
  margin-bottom: 5px;
  padding: 45px;
}
</style>
@endsection

@section('content')


  <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Employee Movement
        <small>Manage all employee movement </small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="{{action('MovementController@index')}}"> All Employee Movements</a></li>
       
      </ol>
    </section>

     <section class="content">
     

          <div class="row" style="margin-top:50px">
           
            

            <div class="col-lg-12"> <!-- ******************* LEFT COLUMN ***************** -->

              <!-- TABLE: LEFT -->
              <div class="box-body">

                <div class="nav-tabs-custom">
                          <ul class="nav nav-tabs">
                           
                            <li @if($typeID=='1') class="active" @endif><a href="{{action('MovementController@index',['typeID'=>1])}}"><strong class="text-primary"><i class="fa fa-exchange"></i> PROGRAM TRANSFERS </strong></a></li>
                            <li  @if($typeID=='2') class="active" @endif><a href="{{action('MovementController@index',['typeID'=>2])}}"><strong class="text-primary"><i class="fa fa-trophy"></i> CHANGE IN POSITION</strong></a></li>
                            <li  @if($typeID=='3') class="active" @endif><a href="{{action('MovementController@index',['typeID'=>3])}}"><strong class="text-primary"><i class="fa fa-id-badge"></i> CHANGE IN EMPLOYMENT STATUS</strong></a></li>
                           
                           
                            
                          </ul>
                          <div class="tab-content">
                            <div class="tab-pane active" id="tab_2">
                              <div class="row" > 
                                <div class="clearfix" style="padding-top:30px;">&nbsp;</div>
                               
                                <div class="col-xs-12">
                                 
                                  
                                  @if ($typeID=='2')
                                  <h4 style="margin-top:10px" id="ws"><i class="fa fa-trophy fa-2x"></i>  &nbsp;&nbsp; Change In Position / Title</h4> 
                                   <p>The following table shows all employee promotions and/or changes in job title made within the last 6 months:</p><br/><br/><br/>
                                  @elseif ($typeID=='3')
                                  <h4 style="margin-top:10px" id="ws"><i class="fa fa-id-badge fa-2x"></i> &nbsp;&nbsp;  Change in Employment Status</h4> 
                                   <p>The following table shows all employment status made within the last 6 months:</p><br/><br/><br/>
                                  @else
                                  <h4 style="margin-top:10px" id="ws"><i class="fa fa-exchange fa-2x"></i> &nbsp;&nbsp; Program / Department Transfers</h4>
                                   <p>The following table shows all employee program/department movements made within the last 6 months:</p><br/><br/><br/>
                                   @endif 

                                 


                                  
                                  
                                  <table class="table" id="teams" >
                                    
                                  </table>
                               
                                   
                                </div>


                              </div>
                              <!-- /.row -->
                              

                            </div><!--end pane1 -->
                            <!-- /.tab-pane -->



                         


                          </div>
                          <!-- /.tab-content -->
                </div>
                <!-- nav-tabs-custom -->











                  
                </div>
                <!-- /.box-body -->
                <div class="box-footer clearfix" style="background:none">
                 
                  
                </div>
                <!-- /.box-footer -->
              <!-- /.box -->
            </div><!--end left -->

 @foreach ($movements as $movement)
            @include('layouts.modals', [
                          'modelRoute'=>'movement.destroy',
                          'modelID' => $movement['id'], 
                          'modelName'=>$movement['movementType'], 
                          'modalTitle'=>'Delete', 
                          'modalMessage'=>'Are you sure you want to delete this?', 
                          'formID'=>'deleteMovement',
                          'icon'=>'glyphicon-trash' ])

                           @endforeach


           


            
           

          
          </div><!-- end row -->

       
     </section>
          



@endsection


@section('footer-scripts')

<script>
  $.widget.bridge('uibutton', $.ui.button);
</script>



<!-- Page script -->
<script>
  //<a style="margin-top:5px" class="btn btn-xs btn-default" data-toggle="modal" data-target="#myModal'+full.id+'"><i class="fa fa-trash"></i> Delete </a>
  $(function () {
   'use strict';
   $("#teams").DataTable({
                  "ajax": "{{ action('MovementController@getAllMovements', ['typeID'=>$typeID]) }}",
                  "processing":false,

                    "columns": [
                        { title: " ", data:'profilePic', width:'90', class:'text-center', sorting:false, render: function ( data, type, full, meta ) {return '<a target="_blank" href="user/'+full.user_id+'"><img src="'+full.profilePic+'" class="img-circle" alt="User Image" width="60" /></a> ';}},
                        { title: "Employee", defaultContent: "<i>none</i>" , data:'lastname', width:'220', render: function(data,type,full,meta){
                          return '<a target="_blank" href="user/'+full.user_id+'">'+full.lastname+', '+full.firstname;
                        } },  
                        // { title: "First name", defaultContent: " ", data:'firstname',width:'120'}, // 1
                        
                         { title: "Program / Campaign " ,defaultContent: "<i>empty</i>", data:'campaign',width:'180' }, // 2
                        // { title: "Movement Type", defaultContent: " ", data:'type',width:'220'}, // 1
                        { title: "Effectivity", defaultContent: " ", data:'effectivity',width:'180'}, // 1
                        { title: "Actions", data:'id', class:'text-center', sorting:false, 
                        render: 
                        function ( data, type, full, meta ) {
                          

                          return '<a target="_blank" href="movement/'+full.id+'" id="teamMovement'+data+'" memberID="'
                          +full.user_id+'" class="teamMovement btn btn-sm btn-flat btn-default" style="margin-top:5px"><i class="fa fa-eye"></i> View Details </a> <a target="_blank" href="movement/'
                          +data+'/edit" id="teamMovement'+data+'" memberID="'
                          +full.user_id+'" class="teamMovement btn btn-sm btn-default" style="margin-top:5px"><i class="fa fa-pencil"></i> Edit </a> '
                        }}
                        

                    ],
                   

                  "responsive":false,
                  "scrollX":true,
                  "dom": '<"col-xs-1"f><"col-xs-11 text-right"l><"clearfix">rt<"bottom"ip><"clear">',
                  "order": [[ 0, "ASC" ]],
                  "lengthChange": true,
                  "oLanguage": {
                     "sSearch": "<strong>Refine Results</strong> <br/>To re-order entries, click on the sort icon to the right of column headers. <br/>To filter out results, just type in the search box anything you want to look for:",
                     "class": "pull-left"
                   },

            
    });









<?php /*

   $('#teams').DataTable({
    "scrollX": false,
    "iDisplayLength": 25,
    "responsive": false,
    "columnDefs": [
            
            { "width": "60",  "targets": [ 0 ], "sorting":false },
            // { "width": "150",  "targets": [ 1 ] },
            // { "width": "150",  "targets": [ 2 ] },
            // { "width": "180",  "targets": [ 3 ] },
            // { "width": "150",  "targets": [ 4 ] },
            {"sorting": false, "targets": [5], "width":"200"}
        ],

    // "columns": [
    //             { "searchable": false },
    //             { "searchable": true },
    //             { "searchable": true },
    //             { "searchable": true },
    //             { "searchable": true },
                
    //           ]

   }); */ ?>

   $('.teamOption, .saveBtn').hide();


   $('.teamMovement').on('click', function(e) {
      e.preventDefault();
      var memberID = $(this).attr('memberID');
      var holder = "#teamOption";
      $(this).fadeOut();
      $(holder+memberID).fadeIn();
   });

   $('select[name="team"]').change(function(){    

    var memberID = $(this).find(':selected').attr('memberID'); // $(this).val();
    var newTeam = $(this).find(':selected').val();
    var saveBtn = $('#save'+memberID).fadeIn();


    
  });

   $(".saveBtn").on("click", function(){
    var memberID = $(this).attr('memberID');
    var newTeam = $("#teamOption"+memberID+" select[name=team]").find(':selected').val(); // $(this).val();
     var _token = "{{ csrf_token() }}";

    $.ajax({
                      url:"{{action('UserController@moveToTeam')}} ",
                      type:'POST',
                      data:{memberID:memberID, newTeam:newTeam, _token:_token},
                      error: function(response)
                      {
                          $("#teamOption"+memberID).fadeOut();
                        $("#teamMovement"+memberID).fadeIn();
                        
                        console.log("Error moving: "+newTeam);

                          
                          return false;
                      },
                      success: function(response)
                      {

                        $("#teamOption"+memberID).fadeOut();
                        $("#teamMovement"+memberID).fadeIn();
                        $("#row"+memberID).delay(1000).fadeOut('slow');
                        console.log("Moved to: "+newTeam);
                        console.log(response);

                          return true;
                      }
                  });




    

   });

   


      
      
   });

   

 
</script>
<!-- end Page script -->

<!-- <script type="text/javascript" src="{{asset('public/js/dashboard.js')}}"></script> -->

@stop