
             
            

                  <div class="box box-danger"style="background: rgba(256, 256, 256, 0.5)">
                    <div class="box-header with-border">
                      <h3 class="box-title">Reports</h3>
                      

                      <div class="box-tools pull-right">
                       
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                        <!-- <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button> -->
                      </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">

                      <div class="nav-tabs-custom" style="background: rgba(256, 256, 256, 0.1)">
                        <ul class="nav nav-tabs pull-right">
                          
                         
                          <li class="active"><a href="#tab_1" data-toggle="tab">
                            <strong class="text-primary "><i class="fa fa-files-o"></i> <img src="./public/img/logo_postmates.png" width="100" /><span id="actives"></span> </strong></a></li>
                         

                            <li><a href="#tab_2" data-toggle="tab">
                            <strong class="text-primary "><i class="fa fa-files-o"></i> <img src="./public/img/logo_guideline.png" width="100" /><span id="actives"></span> </strong></a></li>
                         


                        </ul>


                        @if($fromGuideline && $reportsTeam)

                            <div class="tab-content" style="background: rgba(256, 256, 256, 0.7)">

                              <?php $c=1; ?>
                             
                               @foreach($groupedForm as $form)
                              
                              <div class="tab-pane @if($c==1) active @endif" id="tab_{{$c}}"> 


                                  

                                    <?php /*@if($f->first()->program == "Postmates")

                                    <a target="_blank" href="{{action('FormSubmissionsController@show',$f->first()->formID)}}" class="btn btn-md btn-default pull-left" style="margin-left: 5px"><h4 class="text-danger"><i class="fa fa-files-o 3x"></i> <br/> {{$f->first()->widgetTitle}}</h4><small>View Form Stats <i class="fa fa-pie-chart"></i></small></a>

                                    @else

                                    <a target="_blank" href="{{action('FormSubmissionsController@widgets',['program'=>$prg2,'tab'=>'issues','form'=>$f->first()->formID])}}" class="btn btn-md btn-default pull-left" style="margin-left: 5px"><h4 class="text-danger"><i class="fa fa-files-o 3x"></i> <br/> {{$f->first()->widgetTitle}}</h4><small>View Submissions <i class="fa fa-files-o"></i></small></a>

                                    

                                    @endif 
                                    */ ?>

                                     <a target="_blank" href="{{action('FormSubmissionsController@widgets',['program'=>$prg2,'tab'=>'review','form'=>$form[0]->formID])}}" class="btn btn-md btn-default pull-left" style="margin-left: 5px"><h4 class="text-danger"><i class="fa fa-files-o 3x"></i> <br/> {{$form[0]->widgetTitle}}</h4><small>View Submissions <i class="fa fa-files-o"></i></small></a>
                                  
                                 

                                 
                                   
                               
                                
                              
                              </div><!--end pane1 -->
                              @endforeach
                             

                              <div class="clearfix"></div>

                            </div>
                            <!-- /.tab-content -->
                        

                        @else

                            <div class="tab-content" style="background: rgba(256, 256, 256, 0.7)">

                              <?php $c=1; ?>
                             
                              @foreach($groupedForm as $form)
                              
                              <div class="tab-pane @if($c==1) active @endif" id="tab_{{$c}}"> 


                                  @foreach($form as $f)

                                    @if($f->first()->program == "Postmates")

                                    <a target="_blank" href="{{action('FormSubmissionsController@show',$f->first()->formID)}}" class="btn btn-md btn-default pull-left" style="margin-left: 5px"><h4 class="text-danger"><i class="fa fa-files-o 3x"></i> <br/> {{$f->first()->widgetTitle}}</h4><small>View Form Stats <i class="fa fa-pie-chart"></i></small></a>

                                    @else

                                    <a target="_blank" href="{{action('FormSubmissionsController@widgets',['program'=>$prg2,'tab'=>'review','form'=>$f->first()->formID])}}" class="btn btn-md btn-default pull-left" style="margin-left: 5px"><h4 class="text-danger"><i class="fa fa-files-o 3x"></i> <br/> {{$f->first()->widgetTitle}}</h4><small>View Submissions <i class="fa fa-files-o"></i></small></a>

                                    

                                    @endif 
                                    

                                    <?php /* <a target="_blank" href="{{action('FormSubmissionsController@widgets',['program'=>$prg2,'tab'=>'issues','form'=>$f->formID])}}" class="btn btn-md btn-default pull-left" style="margin-left: 5px"><h4 class="text-danger"><i class="fa fa-files-o 3x"></i> <br/> {{$f->widgetTitle}}</h4><small>View Submissions <i class="fa fa-files-o"></i></small></a> */ ?>
                                  
                                  @endforeach

                                 
                                   
                               
                                
                              
                              </div><!--end pane1 -->
                              <?php $c++;?>
                            
                              @endforeach
                              <!-- /.tab-pane -->

                              <div class="clearfix"></div>

                            </div>
                            <!-- /.tab-content -->


                        @endif
                        

                        
                      </div>
                      <!-- nav-tabs-custom -->
                      
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer" style="background: rgba(221, 75, 57, 0.7)">
                       <!--  <a href="javascript:void(0)" class="btn btn-sm btn-primary btn-flat pull-right">View All Requests</a>
                    --> </div>
                    <!-- /.box-footer -->
                  </div>



             
