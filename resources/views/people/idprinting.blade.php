@extends('layouts.main')

@section('metatags')
<title>ID Printing | Employee Management System</title>

<style type="text/css">
    /* Sortable items */

    #qr-code-container{
      margin: 0 auto;
      width: 480px;
        height: 480px;
        background-size: cover;
        background-repeat: no-repeat;
        background-position: 50% 50%;
    }

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

@section('bodyClasses')
<!--sidebar-collapse-->
@endsection

@section('content')


  <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Employee ID Printing
        
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        
        <li class="active">Employees</li>
      </ol>
    </section>

     <section class="content">
     

          <div class="row" style="margin-top:50px">
           
            

            <div class="col-lg-12"> <!-- ******************* LEFT COLUMN ***************** -->

              <!-- TABLE: LEFT -->
              <div class="box-body">

                <!-- Custom Tabs -->
                                              <div class="nav-tabs-custom">
                                                <ul class="nav nav-tabs">
                                                  <li class="active"><a href="#tab_1" data-toggle="tab"><strong class="text-primary ">For Printing <span id="actives"></span> </strong></a></li>
                                                 

                                                </ul>
                                                

                                                <div class="tab-content">
                                                  <div class="tab-pane active" id="tab_1"> <!-- ACTIVE EMPLOYEES -->
                                                    <div class="row" style="margin-top:50px">

                                                       
                                                          <table class="table no-margin table-bordered table-striped" id="active" width="95%" style="margin:0 auto;" >
                                                            
                                                          </table>

                                                         

                                                          
                                                       
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


           


            
           

          
          </div><!-- end row -->

       
     </section>
          
  
<!-- Confirm Modal -->
<div class="modal fade" id="qrModal" tabindex="-1" role="dialog" aria-labelledby="qrModalLabel">
  
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="qrModalLabel">Employee QR Tag</h4>
        </div>
        <div class="box modal-body">
          <div id="qr_code_wrapper">
            <p>QR Code for <span id="qrModalName"></span>:</p>
              <p><span id="claimer_error" class="help-block"></span></p>
            <div id="qr-code-container"></div>
          </div>
          
          <div class="overlay" id="qr_loader"> 
            <i class="fa fa-refresh fa-spin"></i>
          </div>
        </div>
        <div class="modal-footer">
          <button id="modalConfirmClose" type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button id="modalConfirmYes" type="button" class="btn btn-primary">Print</button>
        </div>
      </div>
    </div>
  
</div>


@endsection


@section('footer-scripts')

<script>
  $.widget.bridge('uibutton', $.ui.button);
</script>



<!-- Page script <small> '+full.employeeNumber+'</small> -->


 <script type="text/javascript">

 $(function () {
   'use strict';
  
  

    $("#active").DataTable({
                      "ajax": "{{ action('UserController@getAllActiveUsers',['print'=>1]) }}",
                      "deferRender": true,
                      "processing":true,
                      "stateSave": true,
                      "order": [ 1, "asc" ],
                      "lengthMenu": [20, 100, 500],
                      "initComplete": function () {
                           
                       },
                      "columns": [
                            { title: " ", data:'id', width:'90', class:'text-center', sorting:false, search:true, render: function ( data, type, full, meta ) {
                               var l = data+".jpg";
                              var profilepic =  "{{ url('/') }}/public/img/employees/"+l;
                              return '<img src="'+profilepic+'" class="img-circle" alt="No image" width="90" /><br/><small> '+full.employeeNumber+'<br/>[ '+data+' ]</small>';} },

                              //return '<a href="user/'+full.id+'"><img src="'+data+'" class="img-circle" alt="User Image" width="60" height="60" />

                            { title: "Name", defaultContent: "<i>none</i>" , data:'lastname', width:'200', render:function(data,type,full,meta){
                               if (full.nickname == null)
                              return full.lastname.toUpperCase()+', '+full.firstname;
                              else
                                return full.lastname.toUpperCase()+', '+full.firstname+'<br/><small><em>( '+full.nickname+' )</em></small>';}}, 
                            
                            { title: "Position", defaultContent: " ", data:'jobTitle',width:'200', render:function(data, type, full, meta ){
                              return'<small>'+data+'</small>';
                            } }, // 1

                            
                            { title: "Actions", data:'id', class:'text-center', sorting:false, render: function ( data, type, full, meta ) {
                              //console.log(type);
                              var deleteLink = "./user/deleteThisUser/"+full.id;
                              var _token = "{{ csrf_token() }}";
                              var modalcode = '<div class="modal fade" id="myModal'+full.id+'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">';

                              modalcode += '<div class="modal-dialog">';
                              modalcode += '  <div class="modal-content">';
                              modalcode += '    <div class="modal-header">';
                                    
                              modalcode += '        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>';
                              modalcode += '       <h4 class="modal-title" id="myModalLabel"> Delete '+full.firstname+' '+full.lastname+'</h4>';
                                    
                              modalcode += '    </div>';
                              modalcode += '    <div class="modal-body">Are you sure you want to delete employee '+full.firstname+' '+full.lastname+'?';
                                   
                              modalcode += '    </div>';
                              modalcode += '    <div class="modal-footer no-border">';
                              modalcode += '      <form action="'+deleteLink+'" method="POST" class="btn-outline pull-right" id="deleteReq"><button type="submit" class="btn btn-primary "><i class="fa fa-check"></i> Yes</button><button type="button" class="btn btn-default" data-dismiss="modal">Close</button><input type="hidden" name="_token" value="'+_token+'" /> </form>';
                             
                              modalcode += '   </div>';
                              modalcode += ' </div>';
                             modalcode += ' </div>';
                            modalcode += '</div>';

                          


                            if(full.claimedCard){
                              var cC = '<label><input type="checkbox" class="claimedcard" checked="checked" disabled="disabled"> Claimed Rewards Card</label>';

                            }else {
                              var cC = '<label><input type="checkbox" class="claimedcard" data-cardid="'+data+'" data-name="'+full.firstname+' '+full.lastname+'"> Claimed Rewards Card</label>';

                            }



                            @if ($hasUserAccess || $isHR)

                              return ' <a target="_blank" href="printID/'+data+'" class="btn btn-md btn-primary" style="margin:3px"><i class="fa fa-id-badge"></i> Print ID Card</a> ';

                               
                                 
                                

                            

                            @endif

                              
                            }}
                            

                        ],
                       

                      //"responsive":true,
                      //"scrollX":false,
                      "dom": '<"col-xs-1"f><"col-xs-11 text-right"l><"clearfix">rt<"bottom"ip><"clear">',
                      
                      //"lengthChange": true,
                      "oLanguage": {
                         "sSearch": "<strong>Refine Results</strong> <br/>To re-order entries, click on the sort icon to the right of column headers. <br/>To filter out results, just type in the search box anything you want to look for:",
                         "class": "pull-left"
                       },

                
        });

        
        var emps = $("#active").DataTable();
        var table_length = emps.page.info().recordsTotal;

          console.log("count");
          console.log(table_length);
       



   

  

   $('table').on('click','.claimedcard',function(){
      var cardid = $(this).attr('data-cardid');
      var empname = $(this).attr('data-name');
      var chck = $(this);
      alert("Claim reward card for employee: "+empname);
      var _token = "{{ csrf_token() }}";

      $.ajax({
                      url:"{{action('UserController@claimedcard')}} ",
                      type:'POST',
                      data:{id:cardid, _token:_token},
                      error: function(response)
                      {
                          
                        console.log("Error saving data: ");

                          
                          return false;
                      },
                      success: function(response)
                      {

                        chck.attr('disabled',true);
                        console.log(response);

                          return true;
                      }
                  });


   });

   $('table').on('click','.wfh',function(){
      var id = $(this).attr('data-cardid');
      var empname = $(this).attr('data-name');
      var chck = $(this).prop('checked');
      if (chck == true) {
        var enableWFH = 1;
        alert("Enable Work From Home for employee: "+empname);
      }

      else {
        var enableWFH = 0;
        alert("Disable Work From Home for employee: "+empname);
      }


      
      var _token = "{{ csrf_token() }}";

      $.ajax({
                      url:"{{action('UserController@wfh')}} ",
                      type:'POST',
                      data:{id:id, enableWFH:enableWFH,  _token:_token},
                      error: function(response)
                      {
                          
                        console.log("Error saving data: ");

                          
                          return false;
                      },
                      success: function(response)
                      {

                        //chck.attr('disabled',true);
                        console.log(response);

                          return true;
                      }
                  });


   });

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


@stop