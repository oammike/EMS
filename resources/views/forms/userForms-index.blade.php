@extends('layouts.main')

@section('metatags')
<title>All Digital Forms | OAMPI Evaluation System</title>

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
        All Digital Forms
        
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
                                                  <li class="active"><a href="#tab_1" data-toggle="tab"><strong class="text-primary ">Signed BIR 2316 <span id="actives"></span> </strong></a></li>
                                                  <li><a href="{{action('UserFormController@userTriggered')}}" ><strong class="text-primary">User-specified Disqualifications <span id="inactives"></span></strong></a></li>
                                                 


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
                      "ajax": "{{ action('UserFormController@getAllForms') }}",
                      "deferRender": true,
                      "processing":true,
                      "stateSave": true,
                      "order": [ 0, "asc" ],
                      "lengthMenu": [20, 100, 500],
                      
                      "columns": [
                           

                            { title: "Name", defaultContent: "<i>none</i>" , data:'lastname', width:'250', render:function(data,type,full,meta){
                              
                                return '<a style="font-weight:bolder" href="user/'+full.id+'" target="_blank">'+full.lastname.toUpperCase()+', '+full.firstname+'<br/><small><em>( '+full.nickname+' )</em></small></a>';}}, 
                            
                            { title: "Program", defaultContent: " ", data:'program',width:'180', render:function(data, type, full, meta ){
                              return'<strong>'+data+' &nbsp;<a target="_blank" class="text-black" href="campaign/'+full.campID+'"><i class="fa fa-external-link"></i></a></strong>';
                            } }, // 1

                             { title: "Date Signed " ,defaultContent: "<i>empty</i>", data:'created_at',width:'130', render:function(data,type,full,meta){

                              var m = moment(data).format('YYYY-MM-DD HH:mm:ss');
                              if (m == "1970-01-01")
                                return "N/A";
                              else 
                                return m;
                              
                             } }, // 2

                             { title: "Qualified For Subsituted Filing?", defaultContent: " ", data:'disqForFiling', render:function(data,type,full,meta){
                              if(data == '1')
                                return "Disqualified";
                              else return "Yes";
                            }}, // 1
                             
                            { title: "Actions", data:'id', class:'text-center',width:'55', sorting:false, render: function ( data, type, full, meta ) {
                              //console.log(type);
                              var deleteLink = "./user/deleteThisUser/"+full.id;
                              var _token = "{{ csrf_token() }}";
                              

                           

                            if(full.hasSigned2316=='1') {
                              var bir = '<a target="_blank" href="viewUserForm?s=1&f=BIR2316&u='+data+'" style="margin:3px" class="btn btn-xs btn-danger"><i class="fa fa-download"></i> Download Signed 2316</a> <br/><br/>';
                            }
                            
                            return bir;
                              
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
  
  

    



   
});

 </script>



<!-- end Page script -->


@stop