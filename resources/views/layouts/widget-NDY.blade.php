

                  <div class="box "><!-- style="background: rgba(7,24,156, 0.5)" -->
                    <div class="box-header with-border">
                      <h3 class="box-title"><img src="./public/img/logo_ndy.png" width="150" /></h3>
                      <h5 class="pull-right" style="margin-top: 50px">{{$trackerNDY[0]->tracker}} </h5>
                      

                      <div class="box-tools pull-right">
                       
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                        <!-- <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button> -->
                      </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">

                      <div class="nav-tabs-custom" style="background: rgba(256, 256, 256, 0.1)">
                        
                        

                        <div class="tab-content" style="background: rgba(256, 256, 256, 0.7)">

                          <?php $ctr=1; $idx=0; $done = array(); ?>
                          
                          
                          <div class="tab-pane active" id="tab1"> 

                            <h2 class="text-center text-primary"><span style="font-size: smaller;"><?php echo date('l M d, Y'); ?></span> <br/> <span id="clock" style="font-weight: bolder;"></span> <br/><span class="text-gray" style="font-size:0.8em;">(Asia/Manila)</span> </h2>
                            <form name="form1" method="POST">

                             

                              <p class="text-center"><br/><br/><i class="fa fa-exclamation-circle"></i> Don't forget to specify the task before ending session</p> 

                              <div class="row">
                                <div class="col-lg-1"></div>
                                <div class="col-lg-10">
                                  <select name="grouptype" id="grouptype" class="form-control">
                                    <option value="0">* select one *</option>
                                     @foreach($groupedTasks as $key)
                                     <option value="{{$key->first()->groupID}}"> {{$key->first()->taskgroup}} </option>
                                     @endforeach
                                    
                                  </select>
                                
                                  @foreach($groupedTasks as $task)
                                  <select name="task{{$task[0]->groupID}}" id="task{{$task[0]->groupID}}" class="tasks form-control" style="margin-top: 5px;">
                                    <option value="0">* select task *</option>

                                     
                                      @foreach(collect($task)->sortBy('task') as $t)
                                       <option value="{{$t->id}}"> {{$t->task}} </option>
                                       @endforeach
                                     
                                    
                                  </select>@endforeach
                                </div>
                                <div class="col-lg-1"></div>
                              </div> 

                               <div class="row">
                                <div class="col-lg-12 text-center">

                                  @if($hasPendingTask=='1')
                                  <input type="hidden" name="taskID" id="taskID" value="{{$pendingTask->id}}"  />
                                  @else
                                  <input type="hidden" name="taskID" id="taskID"/>
                                  @endif

                                  @if($hasPendingTaskBreak=='1')
                                  <input type="hidden" name="breakID" id="breakID" value="{{$pendingTaskBreak->id}}" />
                                  @else
                                  <input type="hidden" name="breakID" id="breakID" />

                                  @endif

                                  <a  data-formid=" " name="start" id="start" class="track btn btn-lg btn-success" style="margin-top: 20px;" ><i class="fa fa-play"></i> START</a>
                                  <a id="btn_breakin" data-timetype="4" class="track btn btn-lg btn-default"  style="margin-top: 20px;"><i class="fa fa-pause"></i> Take a Break </a> 
                                  <a type="button" id="btn_breakout" data-timetype="3" class="track btn btn-lg btn-default"  style="margin-top: 20px;"><i class="fa fa-play-circle-o"></i> Continue Task </a>
                                  <a id="btn_stop" data-timetype="2" class="track btn btn-lg btn-danger"  style="margin-top: 20px;"><i class="fa fa-stop"></i> STOP </a>
                                  
                                </div>
                              </div>

                                  



                             

                            


                          </form>
                          </div><!--end pane1 -->
                        
                         
                          <!-- /.tab-pane -->


                         

                     

                        </div>
                        <!-- /.tab-content -->
                      </div>
                      <!-- nav-tabs-custom -->
                      
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer" style="background: rgba(0,0,0, 0.7)">
                       <a href="javascript:void(0)" class="btn btn-xs btn-default btn-flat pull-right" style="margin-left: 5px"><i class="fa fa-file-o"></i>  My Tasks</a>
                       <a href="{{action('TaskController@allTasks',['program'=>54])}}" class="btn btn-xs btn-default btn-flat pull-right"><i class="fa fa-file"></i> All Tasks</a>
                     </div>
                    <!-- /.box-footer -->
                  </div>



             
