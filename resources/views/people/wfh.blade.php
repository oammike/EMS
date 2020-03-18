@extends('layouts.main')

@section('metatags')
@if ($user->id == Auth::user()->id) 
         <title>Work From Home Tracker| Employee Management System</title>
       @else 
          @if(empty($user->nickname))<title>{{$user->firstname}}'s Requests | Employee Management System</title>  
          @else <title>{{$user->nickname}}'s Requests | Employee Management System</title> 
          @endif
      @endif


@endsection

@section('content')


  <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
       <i class="fa fa-clipboard"></i> 
       Work From Home Tracker
      </h1>
     
      <ol class="breadcrumb">
        <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">WFH Tracker</li>
      </ol>
    </section>

     <section class="content">




                 <!-- ******** THE DATATABLE ********** -->
          <div class="row">
             <div class="col-lg-1 col-sm-4  col-xs-9">
              </div>
              <div class="col-lg-10 col-sm-4 col-xs-12" ><!--style="background-color:#fff"-->
                <label class="pull-left">Production Date: </label><input type="text" id="date" name="date" class="datepicker form-control pull-left" placeholder="{{$start->format('m/d/Y')}}" value="{{$start->format('m/d/Y')}}"  style="width:20%;margin: 0 10px" />
                     <a class="btn btn-primary btn-sm" id="refresh"><i class="fa fa-refresh"></i> Update Table</a>
                     <a class="btn btn-success btn-md pull-right" id="dl"><i class="fa fa-download"></i> Download CSV </a>
                 <br/><br/><br/><br/>
                
                <table class="table no-margin table-bordered table-striped" id="requests" style="background: rgba(256, 256, 256, 0.3)" style="margin-top: 30px" ></table>

                        <br/><br/><br/><br/><br/>
                            
              </div> 
              <div class="col-lg-1 col-sm-4  col-xs-9">
              </div>
              <div class="holder"></div>


              <div class="clearfix"></div>
          </div>


         
               

     
         




       
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

        $( ".datepicker" ).datepicker();
   
        $("#requests").DataTable({

           "ajax": 'getWFH?date={{$start->format("Y-m-d")}}',
         

            "processing":true,
            "stateSave": false,
            "lengthMenu": [10, 50, 100],//[5, 20, 50, -1],
            "columns": [
                 
                  { title: "Bio AccessCode", defaultContent: "<i>none</i>" , data:'accesscode', width:'150', render:function( data, type, full, meta ){return data;}}, // width:'180'},  
                  { title: "Lastname", defaultContent: "<i>none</i>" , data:'lastname', width:'180', render:function( data, type, full, meta ){return '<span style="text-transform:uppercase">'+data+'</span>';}}, // width:'180'},  
                  { title: "Firstname", defaultContent: " ", width:'180',data:'firstname',render:function(data){
                        //return moment(data,"M d, Y").format('MMM DD, YYYY - ddd')
                        return '<span style="text-transform:uppercase; font-size:x-small">'+data+'</span>';
                    } 
                  }, //,width:'180'}, // 1YYYY-MM-DD hh:mm:ss
                  { title: "Program", defaultContent: " ", data:'program', width:'180', render:function(data,type,full,meta){
                    return data;
                  }}, 
                  { title: "Log Time", defaultContent: " ", data:'logTime',width:'150',render:function(data){return moment(data,"hh:mm:ss").format('HH:mm:ss A')} }, 
                  { title: "Log Type", defaultContent: " ", data:'logType_id',width:'150',render:function(data){
                      if (data == '1')
                      return  "<strong>TIME_IN</strong>";
                      else if(data == '2')
                      return  "<strong>TIME_OUT</strong>";
                      else if(data == '3')
                      return  "<strong>BREAK_end</strong>";
                      else if(data == '4')
                      return  "<strong>BREAK_start</strong>"
                      } }, //,width:'180'}, // 1
                  { title: "DTR sheet", defaultContent: " ", data:'userID',width:'150',render:function(data){
                       
                      return  '<a target="_blank" href="user_dtr/'+data+'"><i class="fa fa-calendar"></i> View DTR </strong></a>'} }, //,width:'180'}, // 1
                  
                  
               

              ],
             

            "responsive":true,
            //"scrollX":false,
            "dom": '<"col-xs-1"f><"col-xs-11 text-right"l><"clearfix">rt<"bottom"ip><"clear">',
            "order": [[ 4, 'DESC' ]],
            "lengthChange": true,
            "oLanguage": {
               "sSearch": "<strong>Recorded Logs</strong> <br/><br/>To re-order entries, click the sort icon on the right of column headers. <br/>To filter out results, just type in the search box anything you want to look for:",
               "class": "pull-left"
             },

                      
              });



        $('#refresh').on('click', function(e, datatable){
          var d = $('#date').val();
          window.location= 'wfh?date='+d;

           /*$.getJSON("getWFH?date="+d, function (response,datatable) 
            {
              //console.log(response);
              console.log("---------");
              console.log(d);
              var dt = $("#requests").DataTable();
              dt.ajax.reload();
              
            });*/
        });

        $('#dl').on('click', function(){
          var d = $('#date').val();
          console.log(d);
          window.location= 'wfh_download?from='+d;

        });



       

        
      
      
   });



   

  /*
   * Custom Label formatter
   * ----------------------
   */
  function labelFormatter(label, series) {
    return '<div style="font-size:13px; text-align:center; padding:2px; color: #fff; font-weight: 600;">'
        + label
        + "<br>"
        + Math.round(series.percent) + "%</div>";
  }
</script>
<!-- end Page script -->
<!-- 
<script type="text/javascript" src="{{asset('public/js/dashboard.js')}}"></script> -->

@stop