@extends('layouts.main')

@section('metatags')
<title>Form Submissions | Open Access EMS</title>
<link rel="stylesheet" type="text/css" href="{{URL::asset('public/js/formBuilder/demo.css')}}">
@endsection

@section('content')


  <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1><i class="fa fa-file"></i> {{$form->title}}  </h1>
      <ol class="breadcrumb">
        <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active"> </li>
      </ol>
    </section>

     <section class="content">
        <div class="row">
          <div class="col-md-12">
              <div class="box">
                <div class="box-header with-border">
                  <h3 class="box-title pull-left"><img src="{{$logo}}" width="150" /> </h3>

                 <!--  <label class="pull-left text-primary" style="padding:10px ">Select Report Date: </label><input required type="text" class="pull-left form-control datepicker" style="width:130px; margin-right: 5px" name="reportDate" id="reportDate" placeholder="MM/DD/YYYY" value="{{date('m/d/Y')}}" /><a class="btn btn-default pull-left"> Go <i class="fa fa-arrow-right"></i> </a> -->

                  <div class="box-tools pull-right">
                    <div class="btn-group">
                      <button type="button" class="btn btn-box-tool dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-refresh"></i></button>
                      
                    </div>
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                    
                    
                  </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                  <div class="row">
                    <div class="col-md-9">
                      <p class="text-center">
                        <strong>{{date('M d, Y - l')}} </strong><br/><span class="text-orange" style="font-size:2em;">Rankings</span>
                      </p>

                      <div class="chart" style="max-height: 400px; overflow: scroll;">
                        <table class="table no-margin table-bordered table-striped" id="ranking" style="background: rgba(256, 256, 256, 0.3)" ></table>

                       
                      </div>
                      <!-- /.chart-responsive -->
                    </div>
                    <!-- /.col -->
                    <div class="col-md-3">

                      <!-- ********** DONUT *********** -->

                       <p class="text-center">
                        <strong>{{date('M d, Y - l')}} </strong><br/><span class="text-primary" style="font-size:2em;">Confirmation</span>
                      </p>
                     

                      <div class="chart">
                        <!-- Sales Chart Canvas -->
                        <div class="chart" id="sales-chart" style="position: relative; height: 300px;"></div>
                      </div>

                      <!-- ********** bars *********** -->

                      <p class="text-center">
                        <strong></strong>
                      </p>

                      @foreach($data2 as $d)
                      <div class="progress-group">
                        <span class="progress-text">{{$d['label']}} </span>
                        <span class="progress-number"><b>{{$d['count']}} </b>/ {{$total}}</span>

                        <div class="progress sm">
                          <div class="progress-bar progress-bar-aqua" style="width:{{ ($d['count']/$total)*100 }}%"></div>
                        </div>
                      </div>
                      <!-- /.progress-group -->
                      @endforeach
                      <!-- ********** bars *********** -->


                     
                    </div>
                    <!-- /.col -->
                  </div>
                  <!-- /.row -->
                </div>
                <!-- ./box-body -->

               
                <!-- /.box-footer -->
              </div>
              <!-- /.box -->
          </div>
            <!-- /.col -->
        </div>
      



             <!-- ******** THE DATATABLE ********** -->
          <div class="row">
             <div class="col-lg-1 col-sm-4  col-xs-9">
              </div>
              <div class="col-lg-10 col-sm-4 col-xs-12" >
                <table class="table no-margin table-bordered table-striped" id="forms" style="background: rgba(256, 256, 256, 0.3)" ></table>

                        <br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>
                            
              </div> 
              <div class="col-lg-1 col-sm-4  col-xs-9">
              </div>
              <div class="holder"></div>


              <div class="clearfix"></div>
          </div>






       
     </section>
          



@endsection


@section('footer-scripts')

<!-- Morris.js charts -->
<script src="{{URL::asset('public/js/raphael.min.js')}}"></script>
<script src="{{URL::asset('public/js/morris.min.js')}}"></script>

<script>
  $.widget.bridge('uibutton', $.ui.button);
</script>




<!-- Page script -->
<script>



  $(function () {
   'use strict';

    $( ".datepicker" ).datepicker();

   // Donut Chart
  var donut = new Morris.Donut({
    element  : 'sales-chart',
    resize   : true,
    colors   : ['#3c8dbc', '#f36b19', '#8ccb2c','#ffdc28','#e21f30'],
    data     : [

      @foreach($data as $e)
      { label:"{{$e['label']}}" , value: "{{$e['count']}}" },
      @endforeach

      
    ],
    hideHover: 'auto'
  });

   $("#ranking").DataTable({

            "ajax": "{{ action('FormSubmissionsController@getRanking',2)}}",
            "deferRender": true,
            "order": [ 3, "DESC" ],
            "processing":true,
            "stateSave": false,
            "lengthMenu": [10, 50, 100],//[5, 20, 50, -1],
            "columns": [
                 
                  { title: "Agent", defaultContent: "<i>0</i>" , data:'id',render:function(data,type,full,meta){return '<small>'+full.firstname+' '+full.lastname+ '</small>';}}, // width:'180'}, 
                   
                  { title: "Merchant Cash Only", defaultContent: "<i>0</i>" , data:'id',render:function(data,type,full,meta){
                    for(var x=0; x< full.submissions.length; x++){ 
                      if (full.submissions[x]['item'].toLowerCase() == "merchant_cash_only") return full.submissions[x]['count'];
                      
                    }
                  }}, // width:'180'},

                  { title: "Merchant Closed", defaultContent: "<i>0</i>" , data:'id',render:function(data,type,full,meta){
                    for(var x=0; x< full.submissions.length; x++){ 
                      if (full.submissions[x]['item'].toLowerCase() == "merchant_closed") return full.submissions[x]['count'];
                      
                    }
                  }},
                  { title: "Merchant Payment Problem", defaultContent: "<i>0</i>" , data:'id',render:function(data,type,full,meta){
                    for(var x=0; x< full.submissions.length; x++){ 
                      if (full.submissions[x]['item'].toLowerCase() == "merchant_payment_problem") return full.submissions[x]['count'];
                      
                    }
                  }},

                  { title: "Merchant Refused", defaultContent: "<i>0</i>" , data:'id',render:function(data,type,full,meta){
                    for(var x=0; x< full.submissions.length; x++){ 
                      if (full.submissions[x]['item'].toLowerCase() == "merchant_refused") return full.submissions[x]['count'];
                      
                    }
                  }},

                  { title: "Wrong Address", defaultContent: "<i>0</i>" , data:'id',render:function(data,type,full,meta){
                    for(var x=0; x< full.submissions.length; x++){ 
                      if (full.submissions[x]['item'].toLowerCase() == "wrong_address") return full.submissions[x]['count'];
                      
                    }
                  }},

                  { title: "Total", defaultContent: "<i>0</i>" , data:'id',render:function(data,type,full,meta){
                   // for(var x=0; x< full.submissions.length; x++){ 
                      return "<strong style=\"font-size:larger\">"+full.claimed+"</strong>";
                      
                   // }
                  }},


                  

              ],
             

            //"scrollX":false,
            //"dom": '<"col-xs-1"f><"col-xs-11 text-right"l><"clearfix">rt<"bottom"ip><"clear">',
          
                      
    });



   $("#forms").DataTable({

            "ajax": "{{ action('FormSubmissionsController@getAll',$form->id)}}",
            "deferRender": true,
            "order": [ 4, "desc" ],
            "processing":true,
            "stateSave": false,
            "lengthMenu": [10, 50, 100],//[5, 20, 50, -1],
            "columns": [
                 
                  { title: "Agent", defaultContent: "<i>none</i>" , data:'agent',render:function(data,type,full,meta){return '<small>'+data+'</small>';}}, // width:'180'}, 
                  { title: "Merchant", defaultContent: "<i>none</i>" , data:'Merchant Name'}, // width:'180'}, 
                  { title: "Phone", defaultContent: "<i>none</i>" , data:'Merchant Phone Number'}, // width:'180'}, 
                  { title: "Confirmation", defaultContent: "<i>none</i>" , data:'Confirmation'}, // width:'180'},  
                  { title: "Date", defaultContent: "<i>none</i>" , data:'submitted'}, // width:'180'},
                  { title: "Hour (PST)", defaultContent: "<i>none</i>" , data:'hour',render:function(data,type,full,meta){
                    var m = moment(full.hour,"HH:mm").format('hh:mm A'); return m;} },        

              ],

            "dom": '<"col-xs-1"f><"col-xs-11 text-right"l><"clearfix">rt<"bottom"ip><"clear">',
          
            "oLanguage": {
               "sSearch": "<strong>All Submitted Data</strong> <br/><br/>To re-order entries, click the sort icon on the right of column headers. <br/>To filter out results, just type in the search box anything you want to look for:",
               "class": "pull-left"
             },

                      
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


@stop