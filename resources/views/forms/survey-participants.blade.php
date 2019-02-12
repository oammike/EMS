@extends('layouts.main')

@section('metatags')
<title>Survey Reports | Open Access EMS</title>
<link rel="stylesheet" type="text/css" href="{{URL::asset('public/js/formBuilder/demo.css')}}">
@endsection

@section('content')


  <!-- Content Header (Page header) -->
    <section class="content-header">
      <h4><i class="fa fa-file"></i> Report </h4>
      <ol class="breadcrumb">
        <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="{{action('SurveyController@index')}}"><i class="fa fa-question"></i> Surveys</a></li>
        <li class="active">Survey Participants</li>
      </ol>
    </section>

     <section class="content">
          <div class="row">
            <div class="col-lg-1"></div>
            <div class="col-lg-10">

              <div class="box">
            <div class="box-header with-border">
              <h5 class="box-title">{{$survey->name}}</h5>


              <div class="box-tools pull-right">
                 <a class="btn btn-xs btn-default" style="margin-right: 5px" href="{{action('SurveyController@report','1')}}"><i class="fa fa-arrow-left"></i> Back to Summary</a>
                <a class="btn btn-xs btn-default" style="margin-right: 35px"><i class="fa fa-download"></i> Download Raw Data</a>
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                
                <!-- <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button> -->
              </div>
            </div>
           


            <div class="box-footer">

              @if($type == '1')
              <div class="small-box bg-aqua">
                  <div class="inner">
                    <p>The following employees are <br/><strong><span style="font-size: larger">{!! $activity !!} </strong> </p><br/><br/>
                  </div>
                  <div class="icon">
                    <i class="fa fa-smile-o"></i>
                  </div>
                 
                </div>

                @elseif ($type == '2')

                <div class="small-box bg-red">
                  <div class="inner">
                    <p>The following employees are <br/><strong><span style="font-size: larger">{!! $activity !!} </strong> </p><br/><br/>
                    </div>
                  <div class="icon">
                    <i class="fa fa-comments-o"></i>
                  </div>
                  
                </div>


                @endif

              

              @foreach($participants as $p)

                <!-- DIRECT CHAT PRIMARY -->
                    <div class="box box-primary direct-chat direct-chat-primary">
                      <div class="box-header with-border">
                        <h3 class="box-title"> 
                          <a href="{{action('CampaignController@show',$p[0]['programID'])}}" target="_blank">

                            @if ($p[0]['logo'] !== "white_logo_small.png")
                            <img src="../../public/img/{{$p[0]['logo']}}" width="140px" />
                            @else
                            {{$p[0]['program']}}
                            @endif


                          </a> </h3>

                        <div class="box-tools pull-right">
                          @if (count($p) > 1)
                          <span data-toggle="tooltip" title="3 New Messages" class="badge bg-orange">{{count($p)}} </span> <small>participants</small>

                          @else
                          <span data-toggle="tooltip" title="3 New Messages" class="badge bg-orange">{{count($p)}} </span> <small>participant</small>
                          @endif
                          <button name="minimize" type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                          </button>
                        </div>
                      </div>
                      <!-- /.box-header -->

                      
                      <div class="box-body">
                        <table class="table">
                          <tr>
                            <td>
                            <?php $ctr = 1; $s = count($p)/2; ?>
                            @foreach ($p as $emp)

                              @if ($ctr <= $s)
                              <img src="{{$emp['pic']}}" class="user-image" width="50" /> <strong>{{ $emp['respondent'] }}</strong>
                               - <small>{{$emp['jobTitle']}}</small><br/>



                              @else

                                  @if ($ctr == $s+1)
                                </td>
                                <td>
                                  <img src="{{$emp['pic']}}" class="user-image" width="50" /> <strong>{{ $emp['respondent'] }}</strong>
                               - <small>{{$emp['jobTitle']}}</small><br/>

                                  @else
                                  <img src="{{$emp['pic']}}" class="user-image" width="50" /> <strong>{{ $emp['respondent'] }}</strong>
                               - <small>{{$emp['jobTitle']}}</small><br/>

                                  @endif

                              @endif

                            
                              <?php $ctr++;?>
                            @endforeach
                          </td>
                          </tr>
                        </table>
                       

                      </div>

                      <div class="box-footer"></div>

                    </div>





              <!--old -->

             

              
                
             



              @endforeach

             


            </div>
          </div>
             

              

              
              

            </div>
           <div class="col-lg-1"></div>


            

            

          </div><!-- end row -->





       
     </section>
          



@endsection


@section('footer-scripts')

<script>
  $.widget.bridge('uibutton', $.ui.button);
</script>



<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.3.1/jquery.rateyo.min.js"></script>
 -->




<script>
  $(function () {

    
   'use strict';

   $(document).ready(function() { 
    $('button[name="minimize"]').click();
  });
   

    


   

  
   




   


      
    


   });

   

</script>
<!-- end Page script -->


@stop