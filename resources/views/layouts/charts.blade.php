@if(count($performance)>0)
             
             <div class="col-lg-7 col-sm-6 col-xs-12">

              @if (count($performance) <= 1)
                  <div class="box box-info"style="background: rgba(256, 256, 256, 0.9)">
                    <div class="box-header with-border">
                      <h3 class="box-title">My Performance</h3>
                      <p style="font-size: smaller">This graph shows your performance ratings based on your semi-annual evaluations. <a href="{{action('UserController@myEvals')}}"><i class="fa fa-file"></i> View All Evals</a></p>

                      <div class="box-tools pull-right">
                       
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                        <!-- <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button> -->
                      </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                      <div class="chart" id="bar-chart" style="height: 300px;"></div>
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer" style="background: rgba(35, 165, 220, 0.5)">
                       <!--  <a href="javascript:void(0)" class="btn btn-sm btn-primary btn-flat pull-right">View All Requests</a>
                    --> </div>
                    <!-- /.box-footer -->
                  </div>


                  @else

                  <!-- LINE CHART -->
                  <div class="box box-info"style="background: rgba(256, 256, 256, 0.5)">
                    <div class="box-header with-border">
                       <h3 class="box-title">My Performance</h3>
                              <p style="font-size: smaller">This graph shows your performance ratings based on your semi-annual evaluations. <a href="{{action('UserController@myEvals')}}"><i class="fa fa-file"></i> View All Evals</a></p>

                      <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                        
                      </div>
                    </div>
                    <div class="box-body">
                      <div class="chart">
                        <canvas id="lineChart" style="height:250px"></canvas>
                      </div>
                    </div>
                    <!-- /.box-body -->
                  </div>
                @endif



             </div>


             

             @endif