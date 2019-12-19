@extends('layouts.main')

@section('metatags')
<title>All Evaluations | OAMPI Evaluation System</title>

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
        Evaluations
        <small>Manage all Evaluations </small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        
        <li class="active">Evaluations</li>
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
                           
                            <li @if($type==5) class="active" @endif><a href="{{action('EvalFormController@index',['type'=>5])}}">
                              <strong  @if($type==5) class="text-primary" @else class="text-default" @endif>2019 JAN - DEC<br/> <small>Annual</small></strong></a> </li>

                              <li @if($type==1) class="active" @endif><a href="{{action('EvalFormController@index')}}">
                              <strong @if($type==1) class="text-primary" @else class="text-default" @endif>January-June <br/> <small>Semi-Annual</small> <span style="font-size:smaller" class="text-danger"></span></strong></a></li>
                            <li @if($type==2) class="active" @endif><a href="{{action('EvalFormController@index',['type'=>2])}}">
                              <strong  @if($type==2) class="text-primary" @else class="text-default" @endif>July-December<br/> <small>Semi-Annual</small></strong></a> </li>
                            
                            <li @if($type==3) class="active" @endif><a href="{{action('EvalFormController@index',['type'=>3])}}">
                              <strong  @if($type==3) class="text-primary" @else class="text-default" @endif>Regularization <br/><small>Evaluation</small></strong></a></li>
                            <li @if($type==4) class="active" @endif><a href="{{action('EvalFormController@index',['type'=>4])}}">
                              <strong  @if($type==4) class="text-primary" @else class="text-default" @endif>Contract Extension<br/><small>Evaluation</small></strong></a></li>
                            
                           
                            
                            
                          </ul>
                          <div class="tab-content">
                            <div class="tab-pane active" id="tab_1">
                              <div class="table" style="margin-top: 30px">
                                  <table class="table no-margin" id="teams" ></table>
                              </div><!--end pane1 -->
                          </div>
                          <!-- /.tab-content -->
                        </div>
                        <!-- nav-tabs-custom -->



                    

                    
                  </div>
                  <!-- /.table-responsive -->
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



<!-- Page script -->
<script>
  
  $(function () {
   'use strict';
   $("#teams").DataTable({
                   "ajax": "{{ action('EvalFormController@getAllEval',['type'=>$type])}}",

                    "columns": [
                        { title: " ", data:'user_id', width:'70', class:'text-center', sorting:false, render: function ( data, type, full, meta ) {
                          var l = data+".jpg";
                          var profilepic =  "{{ url('/') }}/public/img/employees/"+l;
                          return '<img src="'+profilepic+'" class="img-circle" alt="No image" width="60" /> ';

                          // $.ajax({
                          //       url: "{{ url('/') }}/public/img/employees/"+l,
                          //       type:'HEAD',
                          //       error: function()
                          //       {
                          //           var profilepic =  "{{ url('/') }}/public/img/useravatar.png";
                          //           return '<img src="'+profilepic+'" class="img-circle" alt="User Image" width="60" /> ';
                          //       },
                          //       success: function()
                          //       {
                          //           var profilepic =  "{{ url('/') }}/public/img/employees/"+l;
                          //           return '<img src="'+profilepic+'" class="img-circle" alt="User Image" width="60" /> ';
                          //       }
                          //   });
                          }},

                          
                        

                        { title: "Last name", defaultContent: "<i>none</i>" , data:'lastname', width:'100'},  
                        { title: "First name", defaultContent: " ", data:'firstname',width:'100'}, // 1
                        { title: "Evaluator", defaultContent: " ", data:'headFname',width:'170',render:function(data,type,full,meta){
                          return data+' '+full.headLname+'<br/><small><strong>'+full.camp+'</strong></small>';
                        } }, 
                        
                        { title: "Eval Type " ,defaultContent: "<i>empty</i>", data:'type',width:'180', render:function(data,type,full,meta){
                          var y = moment(full.year).format('YYYY');
                          return y+' '+data;
                        } }, // 2
                        // { title: "Date Evaluated " ,defaultContent: "<i>empty</i>", data:'dateEvaluated',width:'100'}, // 2
                        { title: "Date Evaluated " ,defaultContent: "<i>empty</i>", data:'created_at',width:'100'}, // 2
                        { title: "Score " ,defaultContent: "<i>empty</i>", data:'overAllScore' }, // 2
                        { title: "Actions", data:'id', class:'text-center', sorting:false, render: function ( data, type, full, meta ) {



                          var deleteLink = "./evalForm/deleteThisEval/"+full.id;
                          var _token = "{{ csrf_token() }}";
                          var modalcode = '<div class="modal fade" id="myModal'+full.id+'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">';

                          modalcode += '<div class="modal-dialog">';
                          modalcode += '  <div class="modal-content">';
                          modalcode += '    <div class="modal-header">';
                                
                          modalcode += '        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>';
                          modalcode += '       <h4 class="modal-title" id="myModalLabel"> Delete Evaluation of '+full.firstname+' '+full.lastname+'</h4>';
                                
                          modalcode += '    </div>';
                          modalcode += '    <div class="modal-body">Are you sure you want to delete this?';
                               
                          modalcode += '    </div>';
                          modalcode += '    <div class="modal-footer no-border">';
                          modalcode += '      <form action="'+deleteLink+'" method="POST" class="btn-outline pull-right" id="deleteReq"><button type="submit" class="btn btn-primary "><i class="fa fa-check"></i> Yes</button><button type="button" class="btn btn-default" data-dismiss="modal">Close</button><input type="hidden" name="_token" value="'+_token+'" /> </form>';
                         
                          modalcode += '   </div>';
                          modalcode += ' </div>';
                         modalcode += ' </div>';
                        modalcode += '</div>';





                          return '<a target="_blank" href="./evalForm/'+full.id+'" class="btn btn-xs btn-default"  style="margin-top:5px" ><i class="fa fa-search"></i> View</a><a href="#"  style="margin-top:5px" class="btn btn-xs btn btn-default" data-toggle="modal" data-target="#myModal'+full.id+'"><i class="fa fa-trash"></i> Delete</a><a href="./evalForm/print/'+full.id+'" target="_blank" class="btn btn-xs btn-default" style="margin-top:5px"><i class="fa fa-print"></i> Print PDF </a><div class="clearfix"></div>'+modalcode;}}
                        

                    ],
                   

                  "responsive":true,
                  "stateSave": false,
                  "deferRender": true,
                  
                  "scrollX":true,
                  "dom": '<"col-xs-1"f><"col-xs-11 text-right"l><"clearfix">rt<"bottom"ip><"clear">',
                  "order": [[ 4, "ASC" ]],
                  "lengthChange": true,
                  //"processing": true,
                  //"serverSide": true,
                  "oLanguage": {
                     "sSearch": "<strong>Refine Results</strong> <br/>To re-order entries, click on the sort icon to the right of column headers. <br/>To filter out results, just type in the search box anything you want to look for:",
                     "class": "pull-left"
                   },

            
    });






   


      
      
   });

   

 
</script>
<!-- end Page script -->

<!-- <script type="text/javascript" src="{{asset('public/js/dashboard.js')}}"></script> -->

@stop