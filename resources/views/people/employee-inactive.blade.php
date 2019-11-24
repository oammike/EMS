@extends('layouts.main')

@section('metatags')
<title>All Inactive Employees | OAMPI Evaluation System</title>

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

@section('bodyClasses')
<!--sidebar-collapse-->
@endsection

@section('content')


  <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Employees
        <small>Manage all OAMPI employees </small>
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

                                                  <li><a href="{{action('UserController@index')}}"><strong class="text-primary">ACTIVE EMPLOYEES</strong></a> </li>
                                                  
                                                  <li class="active"><a href="#tab_2" data-toggle="tab"><strong class="text-primary">INACTIVE EMPLOYEES </strong></a></li>

                                                   <li><a href="{{action('UserController@index_floating')}}" ><strong class="text-primary">FLOATING <span id="floating"></span></strong></a></li>
                                                  
                                                   @if ($hasUserAccess) 
                                                    <a href="{{action('UserController@create')}} " class="btn btn-sm btn-primary  pull-right"><i class="fa fa-plus"></i> Add New Employee</a>
                                                   
                                                    @endif


                                                </ul>
                                                

                                                <div class="tab-content">
                                                  
                                                  <!-- /.tab-pane -->



                                                  <div class="tab-pane active" id="tab_2">

                                                    <div class="row" style="margin-top:50px">

                                                       <div class="table">
                                                          <table class="table no-margin table-bordered table-striped" id="inactive" >
                                                          </table>

                                                          

                                                          
                                                        </div>
                                                        <!-- /.table-responsive -->

                                                    </div>
                                                      <!-- /.row -->
                                                    
                                                    

                                                    
                                                   

                                                  </div>
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
          



@endsection


@section('footer-scripts')

<script>
  $.widget.bridge('uibutton', $.ui.button);
</script>



<!-- Page script <small> '+full.employeeNumber+'</small> -->

 <?php if($hasUserAccess) { ?> 
 <script type="text/javascript">

 $(function () {
   'use strict';
  


   



    $("#inactive").DataTable({
                      "ajax": "{{ action('UserController@getAllInactiveUsers') }}",
                      "deferRender": true,
                      "processing":true,
                      "stateSave": false,
                      "order": [ 1, "asc" ],
                      "lengthMenu": [20, 100, 500],

                        "columns": [
                             { title: " ", data:'id', width:'90', class:'text-center', sorting:false, search:true, render: function ( data, type, full, meta ) {
                               var l = data+".jpg";
                              var profilepic =  "{{ url('/') }}/public/img/employees/"+l;
                              return '<a target="_blank" href="user/'+data+'"><img src="'+profilepic+'" class="img-circle" alt="No image" width="60" /></a><br/><small> '+full.employeeNumber+'</small>';} },

                              //return '<a href="user/'+full.id+'"><img src="'+data+'" class="img-circle" alt="User Image" width="60" height="60" />

                            { title: "Name", defaultContent: "<i>none</i>" , data:'lastname', width:'200', render:function(data,type,full,meta){
                               if (full.nickname == null)
                              return '<a style="font-weight:bolder" href="user/'+full.id+'" target="_blank">'+full.lastname+', '+full.firstname+' </a>';
                              else
                                return '<a style="font-weight:bolder" href="user/'+full.id+'" target="_blank">'+full.lastname+', '+full.firstname+'<br/><small><em>( '+full.nickname+' )</em></small></a>';}}, 
                            
                            { title: "Position : Program", defaultContent: " ", data:'jobTitle',width:'200', render:function(data, type, full, meta ){
                              return'<small>'+data+'</small><br/><strong>'+full.program+'</strong>';
                            } }, // 1


                             { title: "Immediate Head", defaultContent: " ", data:'leaderFname',width:'90', render:function(data,type,full,meta){
                               return '<small>'+data+" "+full.leaderLname+'</small>';
                            }}, // 1
                            
                             { title: "Status " ,defaultContent: "<i>empty</i>", data:'employeeStatus',width:'80', render:function(data,type,full,meta){

                              
                              return data;
                             } }, // 2
                            
                           
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

                              return '<a target="_blank" href="editUser/'+data+'"   style="margin:3px" class="btn btn-xs btn-default"><i class="fa fa-pencil"></i> Edit Profile</a><a target="_blank" href="movement/changePersonnel/'+data+'" id="teamMovement'+data+'" memberID="'+data+'" class="teamMovement btn btn-xs btn-default" style="margin:3px"><i class="fa fa-exchange"></i> Movement</a>  <a target="_blank" href="user_dtr/'+data+'"   style="margin:3px" class="btn btn-xs btn-primary"><i class="fa fa-calendar"></i> View DTR</a>'+modalcode;}}
                            

                        ],
                       

                    
                      "dom": '<"col-xs-1"f><"col-xs-11 text-right"l><"clearfix">rt<"bottom"ip><"clear">',
                      
                      "oLanguage": {
                         "sSearch": "<strong>Refine Results</strong> <br/>To re-order entries, click on the sort icon to the right of column headers. <br/>To filter out results, just type in the search box anything you want to look for:",
                         "class": "pull-left"
                       },

                
        });
});

 </script>

<?php } else { ?>

  <script type="text/javascript">

    $(function () {
   'use strict';

   



    $("#inactive").DataTable({
                      "ajax": "{{ action('UserController@getAllInactiveUsers') }}",
                     "deferRender": true,
                      "processing":true,
                      "stateSave": false,
                      "order": [ 1, "asc" ],
                      "lengthMenu": [20, 100, 500], 

                        "columns": [
                           { title: " ", data:'id', width:'90', class:'text-center', sorting:false, search:true, render: function ( data, type, full, meta ) {
                               var l = data+".jpg";
                              var profilepic =  "{{ url('/') }}/public/img/employees/"+l;
                              return '<img src="'+profilepic+'" class="img-circle" alt="No image" width="60" /><br/><small> '+full.employeeNumber+'</small>';} },

                              //return '<a href="user/'+full.id+'"><img src="'+data+'" class="img-circle" alt="User Image" width="60" height="60" />

                            { title: "Name", defaultContent: "<i>none</i>" , data:'lastname', width:'200', render:function(data,type,full,meta){
                               if (full.nickname == null)
                              return full.lastname+', '+full.firstname;
                              else
                                return full.lastname+', '+full.firstname+'<br/><small><em>( '+full.nickname+' )</em></small>';}}, 
                            
                            { title: "Position : Program", defaultContent: " ", data:'jobTitle',width:'200', render:function(data, type, full, meta ){
                              return'<small>'+data+'</small><br/><strong>'+full.program+'</strong>';
                            } }, // 1

                            { title: "Immediate Head", defaultContent: " ", data:'leaderFname',width:'90', render:function(data,type,full,meta){
                               return '<small>'+data+" "+full.leaderLname+'</small>';
                            }}, // 1
                            
                             { title: "Status " ,defaultContent: "<i>empty</i>", data:'employeeStatus',width:'80', render:function(data,type,full,meta){

                              
                              return "* Access Denied *"; //data;
                             } }, // 2
                            
                            
                            
                           
                        

                        ],
                       

                     
                      "dom": '<"col-xs-1"f><"col-xs-11 text-right"l><"clearfix">rt<"bottom"ip><"clear">',
                      
                      "oLanguage": {
                         "sSearch": "<strong>Refine Results</strong> <br/>To re-order entries, click on the sort icon to the right of column headers. <br/>To filter out results, just type in the search box anything you want to look for:",
                         "class": "pull-left"
                       },

                
        });





});

 </script>

<?php } ?>
<script>
  
 

      
      
   });

   

 
</script>
<!-- end Page script -->


@stop