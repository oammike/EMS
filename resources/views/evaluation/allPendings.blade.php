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
                           
                            <li @if($type==6) class="active" @endif><a href="{{action('EvalFormController@allPendings',['type'=>6])}}">
                              <strong  @if($type==6) class="text-primary" @else class="text-default" @endif><span id="ct1" class="label label-warning" style="font-size: small;"></span> For Approval<br/> </strong></a> </li>

                              <li><a href="{{action('EvalFormController@allApproved',['type'=>6])}}">
                              <strong class="text-default">Approved<br/> <small>evaluations</small></strong></a> </li>

                              <li><a href="{{action('EvalFormController@allDenied',['type'=>6])}}">
                              <strong class="text-default">Rejected <br/> <small>evaluations</small> <span style="font-size:smaller" class="text-danger"></span></strong></a></li>
                           
                            
                           
                            
                            
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
   
   @if($type==6) //2020 annual
      $("#teams").DataTable({
                  'initComplete': function() {
                      var allItems = (this.fnSettings().fnRecordsTotal());
                      console.log('init completed');
                      $('#ct1').html(allItems);
                    },
                   "ajax": "{{ action('EvalFormController@getAllPendingEvals',['type'=>$type])}}",

                    "columns": [
                        { title: " ", data:'user_id', width:'70', class:'text-center', sorting:false, render: function ( data, type, full, meta ) {
                          var l = data+".jpg";
                          var profilepic =  "{{ url('/') }}/public/img/employees/"+l;
                          return '<img src="'+profilepic+'" class="img-circle" alt="No image" width="60" /> ';

                         
                          }},

                          
                        

                        { title: "Last name", defaultContent: "<i>none</i>" , data:'lastname', width:'100'},  
                        { title: "First name", defaultContent: " ", data:'firstname',width:'150'}, // 1
                         { title: "Location", defaultContent: " ", data:'headFname',width:'70',render:function(data,type,full,meta){
                          return '<small><strong>'+full.location+'</strong></small>';
                        } }, 
                        { title: "Evaluator", defaultContent: " ", data:'headFname',width:'150',render:function(data,type,full,meta){
                          return data+' '+full.headLname+'<br/><small><strong>'+full.camp+'</strong></small>';
                        } }, 

                       
                        
                        { title: "Period Covered" ,defaultContent: "<i>empty</i>", data:'type',width:'80', render:function(data,type,full,meta){
                          var y = moment(full.year).format('MM/DD/YYYY');
                          var e = moment(full.endPeriod).format('MM/DD/YYYY');
                          return y+' to '+e;
                        } }, // 2
                        // { title: "Date Evaluated " ,defaultContent: "<i>empty</i>", data:'dateEvaluated',width:'100'}, // 2
                        { title: "Date Evaluated " ,defaultContent: "<i>empty</i>", data:'created_at',width:'100'}, // 2
                        { title: "Total Duration (days) " ,defaultContent: "<i>empty</i>", data:'overAllScore', render:function(data,type,full,meta){
                          var y = moment(full.year);//,"YYYY-MM-DD"); //.format('MM/DD/YYYY');
                          var e = moment(full.endPeriod);//,"YYYY-MM-DD"); //.format('MM/DD/YYYY');
                          var handled = moment.duration(e.diff(y,'days')); //diff(e).asDays();

                          return handled+'';
                        } }, // 2
                        { title: "Score " ,defaultContent: "<i>empty</i>", data:'overAllScore',render:function(data,type,full,meta){
                          if(full.overAllScore <= 69.99)
                            var increase = "N/A";
                          else if (full.overAllScore > 69.99 && full.overAllScore <= 79.99)
                            var increase = "2%";
                          else if (full.overAllScore > 79.99 && full.overAllScore <= 84.49)
                            var increase = "4%";
                          else if (full.overAllScore > 84.40 && full.overAllScore <= 89.49)
                            var increase = "6%";
                          else if (full.overAllScore > 89.40 && full.overAllScore <= 97.49)
                            var increase = "8%";
                          else if (full.overAllScore > 97.49 && full.overAllScore <= 100.0)
                            var increase = "10%"


                            return '<strong>'+data+'</strong><br/><small>Salary matrix: <strong>'+increase+'</strong></small>';
                        } }, // 2
                        { title: "Actions", data:'id', class:'text-center', sorting:false, render: function ( data, type, full, meta ) {



                          var deleteLink = "./evalForm/deleteThisEval/"+full.id;
                          var approveLink = "./evalForm/approveThisEval/"+full.id;
                          var rejectLink = "./evalForm/rejectThisEval/"+full.id;
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

                        //--------- review -----------
                        //--- APPROVE
                        modalcode += '<div class="modal fade" id="myModal2'+full.id+'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">';

                          modalcode += '<div class="modal-dialog">';
                          modalcode += '  <div class="modal-content">';
                          modalcode += '    <div class="modal-header">';
                                
                          modalcode += '        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>';
                          modalcode += '       <h4 class="modal-title" id="myModalLabel"> Approve Evaluation of '+full.firstname+' '+full.lastname+'</h4>';
                                
                          modalcode += '    </div>';
                          modalcode += '    <div class="modal-body">Are you sure you want to mark this evaluation as APPROVED for employee discussion?';
                          modalcode += '<br/><br/><label><input type="checkbox" name="finaleval" class="finaleval" data-evalid="'+full.id+ '"  value="1" /> Use as the Final Eval</label><br/><small><em>* for those with multiple evaluations due to movements</em></small><br/>';
                               
                          modalcode += '    </div>';
                          modalcode += '    <div class="modal-footer no-border">';
                          modalcode += '      <form action="'+approveLink+'" method="POST" class="btn-outline pull-right" id="approveReq"><button type="submit" class="btn btn-primary "><i class="fa fa-check"></i> Yes</button><button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button><input type="hidden" name="_token" value="'+_token+'" /><input type="hidden" name="makeFinal" value="0" /> </form>';
                         
                          modalcode += '   </div>';
                          modalcode += ' </div>';
                         modalcode += ' </div>';
                        modalcode += '</div>';

                        //--- reject
                        modalcode += '<div class="modal fade" id="myModal3'+full.id+'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">';

                          modalcode += '<div class="modal-dialog">';
                          modalcode += '  <div class="modal-content"><form action="'+rejectLink+'" method="POST" id="rejectReq">';
                          modalcode += '    <div class="modal-header">';
                                
                          modalcode += '        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>';
                          modalcode += '       <h4 class="modal-title" id="myModalLabel"> Reject Evaluation of '+full.firstname+' '+full.lastname+'</h4>';
                                
                          modalcode += '    </div>';
                          modalcode += '    <div class="modal-body">Are you sure you want to mark this evaluation as <span class="text-danger">REJECTED</span> for employee discussion?';
                          modalcode += '<br/><br/>';
                               
                          modalcode += '    </div>';
                          modalcode += '    <div class="modal-footer no-border">';
                          modalcode += '      <textarea name="reason" class="form-control" rows="10" style="width:80%; margin:10px auto;" placeholder="Type in notes or message to the TL-evaluator why this evaluation is being rejected."></textarea><br/><button type="submit" class="btn btn-primary "><i class="fa fa-check"></i> Yes</button><button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button><input type="hidden" name="_token" value="'+_token+'" /><input type="hidden" name="makeFinal" value="0" /> ';
                         
                          modalcode += '   </div>';
                          modalcode += ' </form></div>';
                         modalcode += ' </div>';
                        modalcode += '</div>';





                          return '<a target="_blank" href="./evalForm/'+full.id+'" class="btn btn-xs btn-default"  style="margin-top:5px" ><i class="fa fa-search"></i> View</a><a href="#"  style="margin-top:5px" class="btn btn-xs btn btn-default" data-toggle="modal" data-target="#myModal'+full.id+'"><i class="fa fa-trash"></i> Delete</a><a href="./evalForm/print/'+full.id+'" target="_blank" class="btn btn-xs btn-default" style="margin-top:5px"><i class="fa fa-print"></i> Print PDF </a><div class="clearfix"></div><h4> -----</h4><a class="btn btn-sm btn-success" style="margin:2px" data-toggle="modal" data-target="#myModal2'+full.id+'"><i class="fa fa-thumbs-up"></i> Approve</a> <a data-toggle="modal" data-target="#myModal3'+full.id+'" class="btn btn-sm btn-danger"><i class="fa fa-thumbs-down"></i> Reject</a>'+modalcode;}}
                        

                    ],
                   

                  "responsive":true,
                  "stateSave": false,
                  "deferRender": true,
                  
                  "scrollX":true,
                  "dom": '<"col-xs-1"f><"col-xs-11 text-right"l><"clearfix">rt<"bottom"ip><"clear">',
                  //"order": [[ 1,'ASC']],
                  "lengthChange": true,

                  //"processing": true,
                  //"serverSide": true,
                  "oLanguage": {
                     "sSearch": "<strong>Refine Results</strong> <br/>To re-order entries, click on the sort icon to the right of column headers. <br/>To filter out results, just type in the search box anything you want to look for:",
                     "class": "pull-left"
                   },
                  

            
    });//var allEvals = 

      //var totalEvals = allEvals.rows().count();

      // $('#teams').on( 'preDraw.dt', function () {
      //   //var allItems = $("#teams").DataTable();
      //     alert( 'pre Table count:' );//+ allItems.data().count() 
      // } );


      $('table').on('click', '.finaleval',function(){

        if ($(this).prop('checked'))
        {
          $('input[name="makeFinal"]').val(1);
          console.log("checked");//console.log("total: "+totalEvals );
        }else{
          $('input[name="makeFinal"]').val(0);
        } 
        

      });

   @else 

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
                            return data;
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

   @endif






   


      
      
   });

   

 
</script>
<!-- end Page script -->

<!-- <script type="text/javascript" src="{{asset('public/js/dashboard.js')}}"></script> -->

@stop