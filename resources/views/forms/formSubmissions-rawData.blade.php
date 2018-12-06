@extends('layouts.main')

@section('metatags')
<title>Form Submissions | Open Access EMS</title>
<link rel="stylesheet" type="text/css" href="{{URL::asset('public/js/formBuilder/demo.css')}}">
@endsection

@section('content')


  <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1><i class="fa fa-file"></i> {{$form->title}} | <span class="text-success" style="font-size: smaller;"> Raw Data Submissions</span></h1>
      <h5 class="text-danger text-left" id="alldata"></h5>
      <ol class="breadcrumb">
        <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active"> </li>
      </ol>
    </section>

     <section class="content">

             <!-- ******** THE DATATABLE ********** -->
          <div class="row">
            
              <div class="col-lg-12 col-sm-4 col-xs-12" >
                <div class="pull-right">  
                  <p>Showing {{ number_format($actualSubmissions->firstItem()) }}-{{ number_format($actualSubmissions->lastItem()) }} of <strong>{{number_format($actualSubmissions->total())}}</strong> entries</p>
                  <div style="margin-top: -20px"> 

                    {{$actualSubmissions->appends(['from'=>$start,'to'=>$end])->links()}}
                    <a href="{{route('formSubmissions.rawData',['id'=>$form->id, 'from'=>$start,'to'=>$end,'page'=>$actualSubmissions->currentPage(),'dl'=>1])}}" class="btn btn-md btn-success pull-right" style="margin:18px 5px"><i class="fa fa-download"></i> Download Spreadsheet</a>
                  </div>
                </div>
                <a class="pull-left btn btn-xs btn-default" href="{{route('formSubmissions.show',$form->id)}}" ><i class="fa fa-arrow-left"></i> Back to Summary</a>
               
                <br/><br/>
                
                <table class="table no-margin table-bordered table-striped" id="forms" style="background: rgba(256, 256, 256, 0.3)" >
                  <tr>
                    <th>Agent</th>
                    <th>Merchant</th>
                    <th>Protocol</th>
                    <th width="10%">Order Status</th>
                    <th width="20%">Notes</th>
                    <th>Submitted</th>
                    <th>PST hour</th>
                  </tr>
                   @if( count($rawData) < 1 )
                  <tr><td colspan="7"><h2 class="text-danger text-center">No submissions made so far.</h2></td></tr>

                  @else
                  
                  @foreach($rawData as $data)

                 
                  <tr>
                    <td>{{ strtoupper($data['agent'])}} </td>
                    <td>{{$data['merchant']}} </td>
                    <td>{{$data['protocol']}} </td>
                    <td>{{$data['orderStatus']}} </td>
                    <td><span style="font-size: small;">{{$data['notes']}}</span></td>
                    <td>{{$data['submitted']}} </td>
                    <td>{{$data['hour']}} </td>
                  </tr>
                  

                  @endforeach
                  @endif

                </table>
                <!-- @if($canAdminister)
                <button id="deldupes"><i class="fa fa-trash"></i> Remove Duplicates</button>
                @endif  -->

                        <br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>
                            
              </div> 
             
              <div class="holder"></div>


              <div class="clearfix"></div>
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

   

    window.start = moment("{{$start}} ");
    window.end = moment("{{$end}}");
    
    $('#from').val(window.start.format('YYYY-MM-DD'));
    $('#to').val(window.end.format('YYYY-MM-DD'));
    loadRankings(window.start, window.end);
 
    
    function loadRankings(start, end) {
        $('#daterange-btn1 span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        $('#dateescal').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        $('#alldata').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        
    }


    $('#daterange-btn1').daterangepicker(
      {
        ranges   : {
          'Last 2 Days' : [moment().subtract(2, 'days'), moment()],
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