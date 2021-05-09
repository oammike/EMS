@extends('layouts.main')

@section('metatags')
<title>All {{$status}} Trainees | OAMPI EMS</title>

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
         Trainee Management
        
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        
        <li class="active">Employees</li>
      </ol>
    </section>

     <section class="content">
     

          <div class="row" style="margin-top:50px">
           
            

            <div class="col-lg-12"> <!-- ******************* LEFT COLUMN ***************** -->
                                                 

              @if($isFinance || $superAdmin)
              
               <a data-toggle="modal" data-target="#myModal_payslip"  class="btn btn-sm btn-success  pull-right" style="margin-right: 2px;"><i class="fa fa-calculator"></i> Issue Payslip</a>
               
               <a href="{{action('DTRController@financeReports',['type'=>'t','stat'=>$stat])}} " class="btn btn-sm btn-default  pull-right" style="margin-right: 2px;"><i class="fa fa-download"></i> Download Finance Report</a>
               
               @if ($hasUserAccess) 
                <a href="{{action('UserController@create')}} " class="btn btn-sm btn-danger  pull-right"style="margin-right: 2px;"><i class="fa fa-plus"></i> Add New Employee</a>
               
                @endif

               <div class="clearfix"></div>
               


              @include('layouts.modals-payslip', [
                    'modelRoute'=>'resource.index',
                    'modelID' => '_payslip', 
                    'modalMessage'=> " ",
                    'modelName'=>"Trainee Payslip ", 
                    'modalTitle'=>'Generate ', 
                    'formID'=>'submit',
                    'icon'=>'glyphicon-up' ])

              @endif


              <!-- TABLE: LEFT -->
              <div class="box-body">
                 


                <!-- Custom Tabs -->
                                              <div class="nav-tabs-custom">
                                                <ul class="nav nav-tabs">
                                                  <li @if(is_null($stat)) class="active" @endif><a href="{{action('UserController@trainees')}}"><strong class="text-primary ">Ongoing Trainees <span id="actives"></span> </strong></a></li>
                                                  <li @if($stat=="p") class="active" @endif><a href="{{action('UserController@trainees',['stat'=>'p'])}}" ><strong class="text-primary">Passed <span id="inactives"></span></strong></a></li>
                                                  <li @if($stat=="f") class="active" @endif><a href="{{action('UserController@trainees',['stat'=>'f'])}}"" ><strong class="text-primary">Fallout <span id="floating"></span></strong></a></li>
                                                  
                                                  


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

 <?php if($hasUserAccess || $isWorkforce || $canBIR) { ?> 
 <script type="text/javascript">

 $(function () {
   'use strict';
  
  

    $("#active").DataTable({
                      "ajax": "{{ action('UserController@getAllTrainees',['status'=>$stat]) }}",
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
                              return '<a target="_blank" href="user/'+data+'"><img src="'+profilepic+'" class="img-circle" alt="No image" width="90" /></a><br/><small> '+full.employeeNumber+'<br/>[ '+data+' ]</small>';} },

                              //return '<a href="user/'+full.id+'"><img src="'+data+'" class="img-circle" alt="User Image" width="60" height="60" />

                            { title: "Name", defaultContent: "<i>none</i>" , data:'lastname', width:'200', render:function(data,type,full,meta){
                               if (full.nickname == null)
                              return '<a style="font-weight:bolder" href="user/'+full.id+'" target="_blank">'+full.lastname.toUpperCase()+', '+full.firstname+' </a>';
                              else
                                return '<a style="font-weight:bolder" href="user/'+full.id+'" target="_blank">'+full.lastname.toUpperCase()+', '+full.firstname+'<br/><small><em>( '+full.nickname+' )</em></small></a>';}}, 
                            
                            { title: "Position : Program", defaultContent: " ", data:'jobTitle',width:'200', render:function(data, type, full, meta ){
                              return'<small>'+data+'</small><br/><strong>'+full.program+' &nbsp;<a target="_blank" class="text-black" href="./campaign/'+full.campID+'"><i class="fa fa-external-link"></i></a></strong>';
                            } }, // 1

                             { title: "Start Training" ,defaultContent: "<i>empty</i>", data:'startTraining',width:'80', render:function(data,type,full,meta){

                              var m = moment(data).format('YYYY-MM-DD');
                              if (m == "1970-01-01")
                                return "N/A";
                              else 
                                return m;
                              
                             } }, // 2

                             { title: "End Training" ,defaultContent: "<i>empty</i>", data:'endTraining',width:'80', render:function(data,type,full,meta){

                              var m = moment(data).format('YYYY-MM-DD');
                              if (m == "1970-01-01")
                                return "N/A";
                              else 
                                return m;
                              
                             } }, // 2


                           
                            
                            { title: "Trainer / Ops Support", defaultContent: " ", data:'leaderFname',width:'90', render:function(data,type,full,meta){
                               return '<small>'+data+" "+full.leaderLname+'</small>';
                            }}, // 1
                             { title: "Location", defaultContent: " ", data:'location',width:'50', render:function(data,type,full,meta){
                              return data;
                            }}, // 1
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

                            /*we process BIR forms*/
                           

                            if(full.isWFH)
                            {
                              var AHW = '<label><input type="checkbox" class="wfh" checked="checked" data-cardid="'+data+'" data-name="'+full.firstname+' '+full.lastname+'"> WFH <i class="fa fa-home"></i> </label><br/><br/>';
                            }else {
                              var AHW = '<label><input type="checkbox" class="wfh" data-cardid="'+data+'" data-name="'+full.firstname+' '+full.lastname+'"> WFH <i class="fa fa-home"></i> </label><br/><br/>';

                            }

                            

                           



                           

                              return '<a target="_blank" href="user_dtr/'+data+'"   style="margin:3px" class="btn btn-xs btn-primary"><i class="fa fa-calendar"></i> View DTR</a><br/><a target="_blank" href="movement/changePersonnel/'+data+'" id="teamMovement'+data+'" memberID="'+data+'" class="teamMovement btn btn-xs btn-default" style="margin:3px"><i class="fa fa-exchange"></i> Trainee Movement</a>'+modalcode;

                               
                                 
                                

                            

                            

                              
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
       



   
});

 </script>

<?php } else { ?>

  <script type="text/javascript">

    $(function () {
   'use strict';

  


    




});

 </script>

<?php } ?>
<script>
  
  $(function () {
   'use strict';
  

   


   

  

   


      
      
   });

   

 
</script>
<!-- end Page script -->


@stop