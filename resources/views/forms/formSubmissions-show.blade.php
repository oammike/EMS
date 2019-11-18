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
              <div class="box" style="height: 700px;background: none">
                <div class="box-header with-border">
                  <h3 class="box-title pull-left"><img class="pull-left" src="{{$logo}}" width="150" /> </h3>


                  <div class="box-tools pull-right">

                    <div class="btn-group">
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
                        <!-- {{Form::open(['action'=>['FormSubmissionsController@downloadCSV',$form->id ]]) }} -->
                       

                        <input type="hidden" id="from" name="from" />
                        <input type="hidden" id="to" name="to" />

                        <!--  <button @if(Auth::user()->id != 564 )disabled="disabled" @endif type="submit" id="download" data-from="" data-to="" style="margin:3px 5px" class="pull-right btn-success btn-sm btn"><i class="fa fa-download"></i> Download Spreadsheet</button>  -->
                        
                        <a @if(!$canAdminister) disabled="disabled" @endif id="rawdata" data-from="" data-to="" style="margin:0px 5px" class="pull-right btn-primary btn-md btn"><i class="fa fa-list-ol"></i> View Raw Data</a>

                        

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

                      <div class="chart" style="max-height: 900px; overflow: scroll;">
                        <table class="table no-margin table-bordered table-striped" id="ranking" style="background: rgba(256, 256, 256, 0.3)" ></table>

                       
                      </div>
                      <!-- /.chart-responsive -->
                    </div>
                    <!-- /.col -->
                    <div class="col-md-4">

                      <!-- ********** DONUT *********** -->

                       <p class="text-center">
                        <strong id="dateescal"></strong><br/><span class="text-primary" style="font-size:2em;">Escalations</span>
                      </p>
                     

                      <div class="chart">
                        <!-- Sales Chart Canvas -->
                        <div class="chart" id="sales-chart" style="position: relative; height: 300px;"></div>
                      </div>

                      <!-- ********** bars *********** -->

                      <p class="text-center">
                        <strong>Order Status</strong>
                      </p>

                     
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
      









       
     </section>
          



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

   @if($canAdminister)
   $('#deldupes').on('click',function(){
    var _token = "{{ csrf_token() }}";
    var checkeditems = $('.dupes:checkbox:checked').map(function() {
                          return this.value;
                      }).get();

    $.ajax({
                        url: "{{action('FormSubmissionsController@deleteDupes')}}",
                        type:'POST',
                        data:{ 
                          'items': checkeditems,
                          '_token':_token
                        },

                       
                        success: function(res)
                        {
                          
                          if (res.status == '0')
                            $.notify("An error occured. Please try again later.",{className:"error",globalPosition:'right center',autoHideDelay:7000, clickToHide:true} );
                          else {
                            //$('button[name="submit"]').fadeOut();
                            $.notify("Duplicate items successfully deleted.",{className:"success",globalPosition:'right center',autoHideDelay:7000, clickToHide:true} );
                            console.log(res);
                            var allforms = $("#forms").DataTable();
                            allforms.ajax.reload();
                            
                          }

                           
                        }, error: function(res){
                          console.log("ERROR");
                          $.notify("An error occured. Please try re-submitting later.",{className:"error",globalPosition:'right center',autoHideDelay:7000, clickToHide:true} );
                            
                        }


              });
    

   });
   //end duplicate delete
   @endif

    window.start = moment().startOf('day');//subtract(1, 'days').
    window.end = moment().endOf('day');
    console.log("End: ");
    console.log(end);
    $('#from').val(window.start.format('YYYY-MM-DD'));
    $('#to').val(window.end.format('YYYY-MM-DD'));
    $('#rawdata').attr('href','rawData/1?from='+window.start.format('YYYY-MM-DD')+'&to='+window.end.format('YYYY-MM-DD'));

    var dtforms = $("#forms").DataTable({

            "ajax": "../formSubmissions/fetchFrom/{{$form->id}}?from="+window.start.format('YYYY-MM-DD')+"&to="+window.end.format('YYYY-MM-DD'),
            "deferRender": true,
            "order": [ 4, "desc" ],
            "processing":true,
            "stateSave": true,
            "lengthMenu": [10, 50, 100],//[5, 20, 50, -1],
            "columns": [
                 
                 @if($canAdminister)
                  { title: "Agent", defaultContent: "<i>none</i>" ,width:'180', data:'agent',render:function(data,type,full,meta)
                            {
                              var _token = "{{ csrf_token() }}";
                              // var delModal ='<div class="modal fade" id="dupe'+full.id+'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button><h4 class="modal-title" id="myModalLabel"> Delete '+full.type+'</h4></div><div class="modal-body">Are you sure you want to delete this duplicate entry?</div><div class="modal-footer no-border"><form action="../formSubmissions/deleteThis/'+full.id+'" method="POST" class="btn-outline pull-right" id="deleteReq"><button type="submit" class="btn btn-primary glyphicon-trash glyphicon ">Yes</button><button type="button" class="btn btn-default" data-dismiss="modal">Close</button><input type="hidden" name="_token" value="'+_token+'" /> </div></div></div></div>';

                              return '<input class="dupes form-check-input" type="checkbox" name="dupes" value="'+full.id+'" /> <small>'+data+' </small>';

                            }
                  }, // width:'180'}, 
                  { title: "Status", defaultContent: "<i>none</i>" , data:'orderStatus', width:'120'}, // width:'180'}, 
                  { title: "Order Protocol", defaultContent: "<i>none</i>" , data:'protocol', width:'120'}, // width:'180'}, 
                  { title: "Merchant", defaultContent: "<i>none</i>" , data:'merchant'}, // width:'180'},  
                  { title: "Date", defaultContent: "<i>none</i>" , data:'submitted',width:'100'}, // width:'180'},
                  { title: "Hour (PST)", defaultContent: "<i>none</i>" , data:'hour',width:'70'},        

              ],

              @else

              { title: "Agent", defaultContent: "<i>none</i>" ,width:'180', data:'agent',render:function(data,type,full,meta)
                            {

                              return '<small>'+data+' </small>';

                            }
                  }, // width:'180'}, 
                  { title: "Status", defaultContent: "<i>none</i>" , data:'orderStatus', width:'120'}, // width:'180'}, 
                  { title: "Order Protocol", defaultContent: "<i>none</i>" , data:'protocol', width:'120'}, // width:'180'}, 
                  { title: "Merchant", defaultContent: "<i>none</i>" , data:'merchant'}, // width:'180'},  
                  { title: "Date", defaultContent: "<i>none</i>" , data:'submitted',width:'100'}, // width:'180'},
                  { title: "Hour (PST)", defaultContent: "<i>none</i>" , data:'hour',width:'70'},        

              ],


              @endif
            //"dom": 'Bfrtip',
            "dom": '<"col-xs-1"fb><"col-xs-11 text-right"l><"clearfix">rt<"bottom"ip><"clear">',
            // "buttons": [
            //               {
            //                   text: 'My button',
            //                   action: function ( e, dt, node, config ) {
            //                       alert( 'Button activated' );
            //                   }
            //               }
            //           ],
          
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
                     
                      { title: "Agent", defaultContent: "<i>0</i>" , data:'id',render:function(data,type,full,meta){return '<small>'+full.lastname.toUpperCase()+', '+ full.firstname+'</small>';}}, // width:'180'}, 
                       
                      { title: "Placed", width:'70',defaultContent: "<i>none</i>" , data:'id',render:function(data,type,full,meta){
                        for(var x=0; x< full.submissions.length; x++){ 
                          if (full.submissions[x]['item'] == "placed") return full.submissions[x]['count'];
                          
                        }
                      }}, // width:'180'},

                      { title: "Escalated", width:'70',defaultContent: "<i>0</i>" , data:'id',render:function(data,type,full,meta){
                        for(var x=0; x< full.submissions.length; x++){ 
                          if (full.submissions[x]['item'] == "escalated") return full.submissions[x]['count'];
                          
                        }
                      }},
                      { title: "Claimed", width:'70',defaultContent: "<i>none</i>" , data:'id',render:function(data,type,full,meta){
                        return '<span style="font-size:larger; font-weight:bolder">'+full.claimed+'</span>';
                        //foreach(full.submissions as f){
                          //if ( f['item'] == "placed") return full.submissions[0]['count'];
                       // }
                        

                      }}, // width:'180'}, 
                         

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
        

        //--- update status bars
        $.ajax({
            url: "../formSubmissions/getOrderStatus/{{$form->id}}?from="+start.format('YYYY-MM-DD')+"&to="+end.format('YYYY-MM-DD'),
            type:'GET',
            data:{
             'from': start.format('YYYY-MM-DD'),
             'to':end.format('YYYY-MM-DD'),
              '_token':_token
            },

            success: function(res){
                      console.log(res);
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
          
          success: function(res){
            console.log("donut");
            console.log(res);
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
              colors   : ['#3c8dbc', '#f36b19', '#8ccb2c'],
              data     : vals,
              hideHover: 'auto'
            });
          }
        });

    
    }
    
   function fetchAllData(start,end) {
      var newsource = "../formSubmissions/fetchFrom/{{$form->id}}?from="+start.format('YYYY-MM-DD')+"&to="+end.format('YYYY-MM-DD');
            
      dtforms.ajax.url(newsource).load();
    } 
    
    
    
    function loadRankings(start, end) {
        $('#daterange-btn1 span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        $('#dateescal').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        $('#alldata').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        fetchRankings(start,end);
        fetchAllData(start,end);
    }


    $('#daterange-btn1').daterangepicker(
      {
        ranges   : {
          'Today' : [moment().startOf('day'), moment().endOf('day')],
          'Yesterday': [moment().subtract(1, 'days').startOf('day'),moment().subtract(1, 'days').endOf('day')],
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
        $('#rawdata').attr('href','rawData/1?from='+window.start.format('YYYY-MM-DD')+'&to='+window.end.format('YYYY-MM-DD'));

        
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