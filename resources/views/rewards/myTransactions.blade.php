@extends('layouts.main')

@section('metatags')
  <title>My Reward Transactions</title>
  <style type="text/css">
    
  .ui-autocomplete-loading {
    background: white url("images/ui-anim_basic_16x16.gif") right center no-repeat;
  }

  </style>

  <link href="./public/css/easy-autocomplete.min.css" rel="stylesheet" type="text/css">
 <!--  <link href="./public/css/bootstrap-grid.min.css" rel="stylesheet" type="text/css">
  <link href="./public/css/bootstrap-reboot.min.css" rel="stylesheet" type="text/css">
  <link href="./public/css/bootstrap.css" rel="stylesheet" type="text/css"> -->
@stop


@section('content')
<!-- Confirm Modal -->
<div class="modal fade" id="mytransfer" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
          <h4 class="modal-title" id="myModalLabel"><img src="public/img/oam_favicon1-55027f4ev1_site_icon-256x256.png" width="30" /> Transfer Reward Points</h4>
        
      </div>
      <div class="modal-body">
        <img id="pic" src="" class="pull-left" width="280" />
        <p>You are about to transfer <strong id="amt" class="text-danger" style="font-size: large;"></strong> reward point(s) to <br/><strong id="receiver"></strong> of <em><strong id="campaign"></strong></em>.</p>
        <p class="text-right" style="margin-top: 120px">Please type-in your EMS password to proceed.</p>
        <label class="pull-right">EMS Password: <input type="password" name="pw" id="pw" class="form-control" autocomplete="off" /></label>
        <div class="clearfix"></div>
      </div>
      <div class="modal-footer no-border">

          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <button id="proceed" class="btn btn-success" data-dismiss="modal"><i class="fa fa-exchange"></i> Transfer Now </button>
        
        
        
      </div>
    </div>
  </div>
</div>

  <section class="content-header">
    <h1><i class="fa fa-cart-arrow-down"></i> My Reward Transactions 
      <small id="points_counter">Remaining Points: 
        @if ($remaining_points > 10000)
        <em style="font-weight: bolder;">UNLIMITED</em>
        @else
        {{ $remaining_points }}
        @endif
      
      </small></h1>

    <ol class="breadcrumb">
      <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Rewards</li>
    </ol>
  </section>

  <section class="content" id='holder'>
    <div class="row">
      <div class="col-xs-12">
        <div class="box"  style="background: rgba(256, 256, 256, 0.4);">
          <div class="box-heading"></div>
          <div class="box-body">
            <!-- Small boxes (Stat box) -->
            <div class="row" style="padding-top: 20px">
              <div class="col-lg-3 col-xs-6"style="padding-top: 20px">
                <!-- small box -->
                <div class="small-box bg-aqua">
                  <div class="inner">
                    <h3>{{$remaining_points}} </h3>

                    <p>Remaining <br/>Reward Points</p>
                  </div>
                  <div class="icon">
                    <i class="fa fa-credit-card"></i>
                  </div>
                  <a href="#" class="small-box-footer"></a>
                </div>
              </div>
              <!-- ./col -->
              <div class="col-lg-3 col-xs-6"style="padding-top: 20px">
                <!-- small box -->
                <div class="small-box bg-red">
                  <div class="inner">
                    <h3>{{$allTransfers}} <sup style="font-size: 20px"></sup></h3>

                    <p>Total Points <br/>Transferred</p>
                  </div>
                  <div class="icon">
                    <i class="fa fa-exchange"></i>
                  </div>
                  <a href="#" class="small-box-footer"></a>
                </div>
              </div>
              <!-- ./col -->
              <div class="col-lg-3 col-xs-6"style="padding-top: 20px">
                <!-- small box -->
                <div class="small-box bg-green">
                  <div class="inner">
                    <h3>{{$totalEarnings}} </h3>

                    <p>Total Points <br/>Earned</p>
                  </div>
                  <div class="icon">
                    <i class="fa fa-cart-plus"></i>
                  </div>
                  <a href="#" class="small-box-footer"></a>
                </div>
              </div>
              <!-- ./col -->
              <div class="col-lg-3 col-xs-6">
                <img src="storage/uploads/rewards.png" class="pull-right" width="100%" />
              </div>
              <!-- ./col -->
            </div>

            <div class="row" style="margin-top: 25px">
              <div class="col-lg-12">


                 <!-- ******** collapsible box ********** -->
                  <div class="box box-default collapsed-box">
                    <div class="box-header with-border">
                      <h3 class="box-title text-primary">My Redeemed Items <span style="font-weight: bold;font-size: small;" class="text-orange">({{count($myTransactions)}}) </span></h3>
                      <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                        </button>
                      </div>
                      <!-- /.box-tools -->
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                      <table class="table table-bordered">
                        <thead>
                          <tr>
                            <th>Date Redeemed</th>
                            <th>Item</th>
                            <th>Points</th>
                            
                          </tr>
                        </thead>

                        <tbody>
                          @foreach($myTransactions as $myOrder)
                          <tr>
                            <td>{{date('Y-m-d h:i A', strtotime($myOrder->created_at))}} </td>
                            <td>{{$myOrder->name}} </td>
                            <td>{{$myOrder->cost}} </td>
                            
                          </tr>
                          @endforeach
                        </tbody>
                      </table>
                      <div class="clearfix"></div>
                    


                      
                    </div>
                    <!-- /.box-body -->
                  </div>
                 <!-- ******** end collapsible box ********** -->

                 <!-- ******** collapsible box ********** -->
                  <div class="box box-default collapsed-box">
                    <div class="box-header with-border">
                      <h3 class="box-title text-primary">My Earned Points </h3>
                      <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                        </button>
                      </div>
                      <!-- /.box-tools -->
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                      <table class="table table-bordered">
                        <thead>
                          <tr>
                            <th>Date Transferred</th>
                            <th>From</th>
                            <th>Points</th>
                            <th>Notes / Comments</th>
                            
                          </tr>
                        </thead>

                        <tbody>
                          @foreach($pointsReceived as $p)
                          <tr>
                             <td>{{date('Y-m-d h:i A', strtotime($p->created_at))}} </td>
                             <td style="text-transform: uppercase;">{{$p->from_lname}}, {{$p->from_fname}} <em> "{{$p->from_nname}}"</em> </td>
                            <td>{{$p->transferedPoints}} </td>
                            <td><strong>Co-worker Transfer</strong><br/><em style="font-size: small;">{!! $p->notes !!}</em> </td>
                           
                          </tr>
                          @endforeach

                          @foreach($awardsReceived as $a)
                          <tr>
                            <td>{{date('Y-m-d h:i A', strtotime($a->created_at))}} </td>
                            @if( strpos($a->reason,"Birthday") !== false)
                            <td style="text-transform: uppercase;">Open Access BPO </td>
                            @else
                            <td style="text-transform: uppercase;">{{$a->from_lname}}, {{$a->from_fname}} <em> "{{$a->from_nname}}"</em> </td>

                            @endif
                            
                            <td>{{$a->points}} </td>
                            <td><strong>{{$a->reason}}</strong><br/><em style="font-size: small;"> {!! $a->notes !!} </em></td>
                            
                          </tr>

                          @endforeach
                        </tbody>
                      </table>
                      <div class="clearfix"></div>
                    
                    


                      
                    </div>
                    <!-- /.box-body -->
                  </div>
                 <!-- ******** end collapsible box ********** -->

                 <!-- ******** collapsible box ********** -->
                  <div class="box box-default collapsed-box">
                    <div class="box-header with-border">
                      <h3 class="box-title text-primary">My Transfers </h3>
                      <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                        </button>
                      </div>
                      <!-- /.box-tools -->
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                      <table class="table table-bordered">
                        <thead>
                          <tr>
                            <th>Date Transferred</th>
                            <th>To</th>
                            <th>Points</th>
                            <th>Notes / Comments</th>
                            
                          </tr>
                        </thead>

                        <tbody>
                          @foreach($transfersMade as $p)
                          <tr>
                            <td>{{date('Y-m-d h:i A', strtotime($p->created_at))}} </td>
                            <td style="text-transform: uppercase;">{{$p->to_lname}}, {{$p->to_fname}} <em> "{{$p->to_nname}}"</em> </td>
                            <td>{{$p->transferedPoints}} </td>
                            <td>{!! $p->notes !!} </td>
                            
                          </tr>
                          @endforeach
                        </tbody>
                      </table>
                      <div class="clearfix"></div>
                    


                      
                    </div>
                    <!-- /.box-body -->
                  </div>
                 <!-- ******** end collapsible box ********** -->








                
                <br/><br/>
              </div>
              
             
             
            </div>
          </div>
        </div>
      </div>    
    </div>

    </div>
  </section>    
  

  
@stop

@section('footer-scripts')


  <script>
    window.selected_reward_id = 0;
    $(function() {

      $(".table").DataTable({"order": [[ 0, "ASC" ]]}); 


     

  

  


      
    });
  </script>
@stop
