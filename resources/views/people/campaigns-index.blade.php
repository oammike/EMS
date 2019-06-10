@extends('layouts.main')

@section('metatags')
<title>Departments/Programs | OAMPI Evaluation System</title>
@endsection

@section('bodyClasses')

@endsection

@section('content')


  <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        OAMPI Programs / Departments &nbsp;&nbsp; 
        <a href="{{action('CampaignController@index',['sort'=>'A'])}}" class="btn @if($sort=='1') bg-black @else btn-default  @endif btn-md"><i class="fa fa-sort-alpha-asc"></i> </a>
        <a href="{{action('CampaignController@index',['sort'=>'Z'])}}" class="btn @if($sort=='2') bg-black @else btn-default  @endif btn-md"><i class="fa fa-sort-alpha-desc"></i> </a>
        
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Programs</li>
      </ol>
    </section>

     <section class="content">
      <!-- ******** THE DATATABLE ********** -->
          <div class="row">
             <div class="col-lg-12">
              <div class="box box-primary" style="background: rgba(256, 256, 256, 0.6)">
                      <div class="box-header ">
                      </div><!--end box-header-->
                      
                      <div class="box-body">

                        @foreach ($allCamps as $campaign)

                        <a href="{{action('CampaignController@show',$campaign->id)}}" class="pull-left text-center" style="width: 25%; height:150px">

                          @if(is_null($campaign->filename))
                          <!--pridelogo.png  -->
                          <img src="./public/img/oam_favicon1-55027f4ev1_site_icon-256x256.png" width="50px" />
                          <h4> {{$campaign->name}}</h4>

                          @elseif ($campaign->name=='IMO')
                          <img src="./public/img/{{$campaign->filename}}" height="55"  />

                          @else
                          <img src="./public/img/{{$campaign->filename}}" width="150px" style="margin-top: 20px" />
                          @endif
                          
                        </a>

                        
                       

                        @endforeach
                      </div><!--end box-body-->
              </div><!--end box-primary-->

          </div><!--end main row-->
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