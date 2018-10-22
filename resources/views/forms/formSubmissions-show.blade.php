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
                  <h3 class="box-title"><img src="{{$logo}}" width="150" /> </h3>

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
                    <div class="col-md-8">
                      <p class="text-center">
                        <strong>{{date('M d, Y - l')}} </strong><br/><span class="text-primary" style="font-size:2em;">Rankings</span>
                      </p>

                      <div class="chart" style="max-height: 400px; overflow: scroll;">
                        <table class="table no-margin table-bordered table-striped" id="ranking" style="background: rgba(256, 256, 256, 0.3)" ></table>

                       
                      </div>
                      <!-- /.chart-responsive -->
                    </div>
                    <!-- /.col -->
                    <div class="col-md-4">

                      <!-- ********** DONUT *********** -->

                       <p class="text-center">
                        <strong>{{date('M d, Y - l')}} </strong><br/><span class="text-primary" style="font-size:2em;">Escalations</span>
                      </p>
                     

                      <div class="chart">
                        <!-- Sales Chart Canvas -->
                        <div class="chart" id="sales-chart" style="position: relative; height: 300px;"></div>
                      </div>

                      <!-- ********** bars *********** -->

                      <p class="text-center">
                        <strong>Order Status</strong>
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

                <!-- <div class="box-footer">
                  <div class="row">
                    <div class="col-sm-3 col-xs-6">
                      <div class="description-block border-right">
                        <span class="description-percentage text-green"><i class="fa fa-caret-up"></i> 17%</span>
                        <h5 class="description-header">$35,210.43</h5>
                        <span class="description-text">TOTAL REVENUE</span>
                      </div>
                     
                    </div>
                    
                    <div class="col-sm-3 col-xs-6">
                      <div class="description-block border-right">
                        <span class="description-percentage text-yellow"><i class="fa fa-caret-left"></i> 0%</span>
                        <h5 class="description-header">$10,390.90</h5>
                        <span class="description-text">TOTAL COST</span>
                      </div>
                     
                    </div>
                    
                    <div class="col-sm-3 col-xs-6">
                      <div class="description-block border-right">
                        <span class="description-percentage text-green"><i class="fa fa-caret-up"></i> 20%</span>
                        <h5 class="description-header">$24,813.53</h5>
                        <span class="description-text">TOTAL PROFIT</span>
                      </div>
                     
                    </div>
                   
                    <div class="col-sm-3 col-xs-6">
                      <div class="description-block">
                        <span class="description-percentage text-red"><i class="fa fa-caret-down"></i> 18%</span>
                        <h5 class="description-header">1200</h5>
                        <span class="description-text">GOAL COMPLETIONS</span>
                      </div>
                    
                    </div>
                  </div>
                 
                </div> -->
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

   // Donut Chart
  var donut = new Morris.Donut({
    element  : 'sales-chart',
    resize   : true,
    colors   : ['#3c8dbc', '#f36b19', '#8ccb2c'],
    data     : [

      @foreach($data as $e)
      { label:"{{$e['label']}}" , value: "{{$e['count']}}" },
      @endforeach

      
    ],
    hideHover: 'auto'
  });

   $("#ranking").DataTable({

            "ajax": "{{ action('FormSubmissionsController@getRanking',1)}}",
            "deferRender": true,
            "order": [ 3, "DESC" ],
            "processing":true,
            "stateSave": false,
            "lengthMenu": [10, 50, 100],//[5, 20, 50, -1],
            "columns": [
                 
                  { title: "Agent", defaultContent: "<i>0</i>" , data:'id',render:function(data,type,full,meta){return '<small>'+full.firstname+' '+full.lastname+ '</small>';}}, // width:'180'}, 
                   
                  { title: "Placed", defaultContent: "<i>none</i>" , data:'id',render:function(data,type,full,meta){
                    for(var x=0; x< full.submissions.length; x++){ 
                      if (full.submissions[x]['item'] == "placed") return full.submissions[x]['count'];
                      
                    }
                  }}, // width:'180'},

                  { title: "Escalated", defaultContent: "<i>0</i>" , data:'id',render:function(data,type,full,meta){
                    for(var x=0; x< full.submissions.length; x++){ 
                      if (full.submissions[x]['item'] == "escalated") return full.submissions[x]['count'];
                      
                    }
                  }},
                  { title: "Claimed", defaultContent: "<i>none</i>" , data:'id',render:function(data,type,full,meta){
                    return '<span style="font-size:larger; font-weight:bolder">'+full.claimed+'</span>';
                    //foreach(full.submissions as f){
                      //if ( f['item'] == "placed") return full.submissions[0]['count'];
                   // }
                    

                  }}, // width:'180'}, 
                     

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
                  { title: "Status", defaultContent: "<i>none</i>" , data:'orderStatus'}, // width:'180'}, 
                  { title: "Order Protocol", defaultContent: "<i>none</i>" , data:'protocol'}, // width:'180'}, 
                  { title: "Merchant", defaultContent: "<i>none</i>" , data:'merchant'}, // width:'180'},  
                  { title: "Date", defaultContent: "<i>none</i>" , data:'submitted'}, // width:'180'},
                  { title: "Hour (PST)", defaultContent: "<i>none</i>" , data:'hour'},        

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