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
                  <h3 class="box-title pull-left"><img class="pull-left" src="{{$logo}}" width="150" /> </h3>


                  <div class="box-tools pull-right">

                    <div class="btn-group">
                      <a id="upload"  data-toggle="modal" data-target="#myModal_uploadCSV" class="btn btn-box-tool dropdown-toggle">
                        <i class="fa fa-upload"></i></a>
                      <button type="button" class="btn btn-box-tool dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-refresh"></i></button>
                      
                    </div>
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                    
                    
                  </div>
                  <div class="clearfix"></div>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                  <div class="row">
                    <div class="col-md-8">

                      <div class="text-right" id="realtime" data-toggle="btn-toggle">
                        <p style="margin-right: 230px">Select Report Date Range: </p>
                        {{Form::open(['action'=>['FormSubmissionsController@downloadCSV',$form->id ]]) }}
                        <input type="hidden" id="from" name="from" />
                        <input type="hidden" id="to" name="to" />
                         <button type="submit" @if($canAdminister==false)disabled="disabled" @endif id="download" data-from="" data-to="" style="margin:3px 5px" class="pull-right btn-success btn-sm btn"><i class="fa fa-download"></i> Download Spreadsheet</button> 

                         {{Form::close()}}
                        
                        <button type="button" class="btn btn-default pull-right" id="daterange-btn1">
                          <span>
                            <i class="fa fa-calendar"></i> Date range
                          </span>
                          <i class="fa fa-caret-down"></i>
                        </button>

                      </div>


                      <p class="text-center">
                        <span class="text-primary" style="font-size:2em;">Rankings</span>
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
                        <strong id="dateescal"></strong><br/><span class="text-primary" style="font-size:2em;">Confirmation</span>
                      </p>
                     

                      <div class="chart">
                        <!-- Sales Chart Canvas -->
                        <div class="chart" id="sales-chart" style="position: relative; height: 300px;"></div>
                      </div>

                      <!-- ********** bars *********** -->

                     
                     
                      <div id="statusprogress">
                        
                        
                      </div>
                      
                      <!-- /.progress-group -->
                     
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
                <h2 class="pull-right">Raw Data</h2>
                <h3 class="text-danger" id="alldata"></h3>
                <table class="table no-margin table-bordered table-striped" id="forms" style="background: rgba(256, 256, 256, 0.3)" ></table>

                        <br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>
                            
              </div> 
              <div class="col-lg-1 col-sm-4  col-xs-9">
              </div>
              <div class="holder"></div>


              <div class="clearfix"></div>
          </div>






       
     </section>

      @include('layouts.modals-upload', [
                                'modelRoute'=>'biometrics.uploadFinanceCSV',
                                'modelID' => '_uploadCSV', 
                                'modelName'=>"Postmate CSV file ", 
                                'modalTitle'=>'Upload', 
                                'modalMessage'=>'Select CSV file to upload (*.csv):', 
                                'formID'=>'uploadBio2',
                                'icon'=>'glyphicon-up' ])
          



@endsection

@section('footer-scripts')

<link href="{{ asset( 'public/js/bootstrap-daterangepicker/daterangepicker.css' ) }}" rel="stylesheet">
<script type="text/javascript" src="{{ asset( 'public/js/chartjs/Chart.min.js' ) }}"></script>
<script type="text/javascript" src="{{ asset( 'public/js/bootstrap-daterangepicker/daterangepicker.js' ) }}"></script>


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


   /*----------------- Report generation -------------*/
    window.start = moment().subtract(6, 'days');
    window.end = moment();
    $('#from').val(window.start.format('YYYY-MM-DD'));
    $('#to').val(window.end.format('YYYY-MM-DD'));

    
   

    var dtforms = $("#forms").DataTable({
    
            "ajax": "../formSubmissions/fetchFrom/{{$form->id}}?from="+window.start.format('YYYY-MM-DD')+"&to="+window.end.format('YYYY-MM-DD'),
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

    var dtranks = $("#ranking").DataTable({

            
            "ajax": "../formSubmissions/fetchRanking/{{$form->id}}?from="+window.start.format('YYYY-MM-DD')+"&to="+window.end.format('YYYY-MM-DD'),
            "deferRender": true,
            "order": [ 3, "DESC" ],
            "processing":true,
            "stateSave": false,
            "lengthMenu": [10, 50, 100],//[5, 20, 50, -1],
            "columns": [
                 
                  { title: "Agent", defaultContent: "<i>0</i>" , data:'id',render:function(data,type,full,meta){return '<small>'+full.firstname+' '+full.lastname+ '</small>';}}, // width:'180'}, 
                   
                  { title: "Merchant Cash Only",width:'50', defaultContent: "<i>0</i>" , data:'id',render:function(data,type,full,meta){
                    for(var x=0; x< full.submissions.length; x++){ 
                      if (full.submissions[x]['item'].toLowerCase() == "merchant_cash_only") return full.submissions[x]['count'];
                      
                    }
                  }}, // width:'180'},

                  { title: "Merchant Closed",width:'50', defaultContent: "<i>0</i>" , data:'id',render:function(data,type,full,meta){
                    for(var x=0; x< full.submissions.length; x++){ 
                      if (full.submissions[x]['item'].toLowerCase() == "merchant_closed") return full.submissions[x]['count'];
                      
                    }
                  }},
                  { title: "Merchant Payment Problem",width:'50', defaultContent: "<i>0</i>" , data:'id',render:function(data,type,full,meta){
                    for(var x=0; x< full.submissions.length; x++){ 
                      if (full.submissions[x]['item'].toLowerCase() == "merchant_payment_problem") return full.submissions[x]['count'];
                      
                    }
                  }},

                  { title: "Merchant Refused",width:'50', defaultContent: "<i>0</i>" , data:'id',render:function(data,type,full,meta){
                    for(var x=0; x< full.submissions.length; x++){ 
                      if (full.submissions[x]['item'].toLowerCase() == "merchant_refused") return full.submissions[x]['count'];
                      
                    }
                  }},

                  { title: "Wrong Address",width:'30', defaultContent: "<i>0</i>" , data:'id',render:function(data,type,full,meta){
                    for(var x=0; x< full.submissions.length; x++){ 
                      if (full.submissions[x]['item'].toLowerCase() == "wrong_address") return full.submissions[x]['count'];
                      
                    }
                  }},

                  { title: "Total",width:'30', defaultContent: "<i>0</i>" , data:'id',render:function(data,type,full,meta){
                   // for(var x=0; x< full.submissions.length; x++){ 
                      return "<strong style=\"font-size:larger\">"+full.claimed+"</strong>";
                      
                   // }
                  }},


                  

              ],
             
          
                      
    });
   
    window.chartColors = {
      red: 'rgba(255, 99, 132, 0.8)',
      orange: 'rgba(255, 159, 64, 1.0)',
      yellow: 'rgba(255, 205, 86, 1.0)',
      green: 'rgba(171, 235, 198, 1.0)',
      blue: 'rgba(54, 162, 235, 1.0)',
      purple: 'rgba(153, 102, 255, 1.0)',
      grey: 'rgba(201, 203, 207, 1.0)',
      darkblue: 'rgba(33, 47, 61, 1.0)',
      lightblue: 'rgba(169, 204, 227, 1.0)',
      black: 'rgba(1, 1, 1, 1.0)'
      
    };
    
    window.colorKeys = [
      "red", "orange", "yellow", "green", "blue", "purple", "grey", "darkblue", "lightblue", "black"
    ];
    
    

    
    function pad(num) {
      return ("0"+num).slice(-2);
    }
    function hhmmss(seconds) {
      return (Math.floor(seconds / 3600)) + ":" + ("0" + Math.floor(seconds / 60) % 60).slice(-2) + ":" + ("0" + seconds % 60).slice(-2)
    
      
    }

    function fetchRankings(start,end) {

      var newsource = "../formSubmissions/fetchRanking/{{$form->id}}?from="+start.format('YYYY-MM-DD')+"&to="+end.format('YYYY-MM-DD');
      dtranks.ajax.url(newsource).load();


       var _token = "{{ csrf_token() }}";
        

        //--- update progressbar
        $.ajax({
            url: "{{action('FormSubmissionsController@getOrderStatus',$form->id)}}",
            type:'POST',
            data:{
             'from': window.start.format('YYYY-MM-DD'),
             'to':window.end.format('YYYY-MM-DD'),
              '_token':_token
            },

            success: function(res){
                      //console.log(res);
                      var c = res['data'].length;
                      var tot = 0;
                      var code = "";
                      
                      for (var i=0; i<c; i++){
                        
                        tot+=res['data'][i]['count'];

                        code += '<div class="progress-group">';
                        code +='<span class="progress-text">'+res['data'][i]['label']+'</span>';
                        code +='<span class="progress-number"><b>'+res['data'][i]['count']+'</b>/ '+res['total']+'</span>';
                        code +='<div class="progress sm">';
                        var s = ( parseFloat(res['data'][i]['count']) / parseFloat(res['total']) )*100;
                       
                        code +='<div class="progress-bar progress-bar-aqua" style="width:'+s+'%"></div>';
                        code +='</div></div>';
                      }
                      $('#statusprogress').html(code);
                     
                      //console.log(tot);
                    
                    
                    
            },
          });

        // DONUT CHART
        $.ajax({
          url:"../formSubmissions/getEscalations/{{$form->id}}?from="+start.format('YYYY-MM-DD')+"&to="+end.format('YYYY-MM-DD'),
          type:"GET",
          data:{
            'from': start.format('YYYY-MM-DD'),
             'to':end.format('YYYY-MM-DD'),
              '_token':_token
          },
          success: function(res){
            console.log("donut");
            //console.log(res);
            // Donut Chart
            var c = res.length;
            var vals = [];
            for (var i=0; i<c; i++){
                  vals[i] = {label:res[i]['label'], value:res[i]['count']};
                }


            console.log(vals);

            var donut = new Morris.Donut({
              element  : 'sales-chart',
              resize   : true,
              colors   : ['#3c8dbc', '#f36b19', '#8ccb2c', 'rgba(255, 205, 86, 1.0)','rgba(171, 235, 198, 1.0)','rgba(153, 102, 255, 1.0)'],
              data     : vals,
              hideHover: 'auto'
            });
          }
        });

    
    }
    
   function fetchAllData(start, end) {

     var newsource = "../formSubmissions/fetchFrom/{{$form->id}}?from="+start.format('YYYY-MM-DD')+"&to="+end.format('YYYY-MM-DD');
            
     dtforms.ajax.url(newsource).load();
     

    } 
    
    
    
    function loadRankings(start,end) {
        $('#daterange-btn1 span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        $('#dateescal').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        $('#alldata').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));

        fetchRankings(start,end);
        fetchAllData(start,end);

     
    }


    $('#daterange-btn1').daterangepicker(
      {
        ranges   : {
          'Last 7 Days' : [moment().subtract(6, 'days'), moment()],
          'Last 30 Days': [moment().subtract(29, 'days'), moment()],
          'This Month'  : [moment().startOf('month'), moment().endOf('month')],
          'Last Month'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        startDate: window.start,
        endDate  : window.end
      },function(start,end){
        
       
        window.start = start;
        window.end = end;
        $('#from').val(start.format('YYYY-MM-DD'));
        $('#to').val(end.format('YYYY-MM-DD'));
        
        loadRankings(start,end);

      } 
    );


    

    loadRankings(window.start, window.end);

    
   
         

        
      
      
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



