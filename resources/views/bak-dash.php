
      @if (count($mySubordinates) < 8)

      <!-- ******** LESS THAN 8 ********** -->
          <div class="row">

            <div class="col-lg-12" style="background:#fff; margin-bottom:20px;">
              <h4 class="text-center"><small>Employees who are up for</small> <br/> {{$evalSetting->name}}<br /> 
              <small class="text-black"><em>( {{$currentPeriod->format('M d')}} to {{$endPeriod->format('M d, Y')}} )</em></small><br /></h4>

            </div>
            

            @if (count($mySubordinates) == 0)

            <h3 class="text-center text-success"><br /><br />No entries found.</h3>

            @else

            @foreach ($mySubordinates as $employee)

            @if($employee->id !== Auth::user()->id && $doneEval[$employee->id] !== null)
            <div class="col-lg-3 col-sm-6 col-xs-12">
               <!-- Widget: user widget style 1 -->
                          <div class="box box-widget widget-user-2">
                            <!-- Add the bg color to the header using any of the bg-* classes 
                            done & agent = gray
                            done & leader = darkgreen black
                            !done & leader = black
                            !done & agent = green

                          -->
                            <div class="widget-user-header  @if ($doneEval[$employee->id]['evaluated'] && ($employee->userType_id == '4') ) bg-gray @elseif ($doneEval[$employee->id]['evaluated'] && ($employee->userType_id !== '4') ) bg-black @elseif (!$doneEval[$employee->id]['evaluated'] && ($employee->userType_id !== '4') )bg-darkgreen  @else bg-green @endif">

                              
                                    <div class="widget-user-image">
                                        <a href="{{action('UserController@show',$employee->id)}}" class="text-primary"> 
                                         @if ( file_exists('public/img/employees/'.$employee->id.'.jpg') )
                                       <img src="{{asset('public/img/employees/'.$employee->id.'.jpg')}}" class="img-circle pull-left" alt="User Image" width="70" style="margin-top:-10px" >
                                        @else
                                          <img src="{{asset('public/img/useravatar.png')}}" class="img-circle pull-left" alt="Employee Image"  width="70" style="margin-top:-10px">

                                          @endif
                                       
                                        </a>
                                    </div>
                                  <!-- /.widget-user-image -->
                                  <h3 class="widget-user-username"> {{$employee->lastname }}, {{$employee->firstname}} </h3>
                                  <h5 class="widget-user-desc"> {{$employee->position->name}}</h5>
                                  <h5 class="widget-user-desc" style="color:#fff">

                                      @if(count($employee->campaign) > 1)
                                        @foreach($employee->campaign as $e)

                                          {{$e->name}}

                                        @endforeach

                                      </h5>

                                      @else
                                        {{$employee->campaign->first()->name}} 

                                      @endif
                                  </h5>                              
                             
                            </div>

                            <div class="box-footer no-padding">
                              
                              <ul class="nav nav-stacked">
                                <li><a href="#">Date Hired <span class="pull-right badge bg-gray">{{ date_format(date_create($employee->dateHired), "M d, Y")  }} </span></a></li>
                                <li><a href="#">Absences <span class="pull-right badge bg-red">N/A</span></a></li>
                                <li><a href="#">Tardiness <span class="pull-right badge bg-red">N/A</span></a></li>
                                <li><a href="#">Received Disciplinary Actions <span class="pull-right badge bg-red">N/A</span></a></li>

                                <!-- @if ($employee->userType_id == 3)
                                <li><h5 class="text-left text-success" style="margin-left:18px"><br/><strong>Productivity & Efficiency Summary</strong></h5></li>
                                <li><a href="#">Accomplished Tasks <span class="pull-right badge">N/A</span></a></li>
                                <li><a href="#">Pending Tasks <span class="pull-right badge ">N/A</span></a></li>
                                <li><a href="#">Delays <span class="pull-right badge ">N/A</span></a></li>

                                @endif -->

                                @if ($doneEval[$employee->id]['evaluated']&& $doneEval[$employee->id]['score'] != 0)
                                <li><a href="#">
                                  <span class="pull-left"><strong>Current Rating</strong> <br/>
                                    <small>
                                   
                                      {{date('M d, Y',strtotime($doneEval[$employee->id]['startPeriod']))  }} to  {{date('M d, Y',strtotime($doneEval[$employee->id]['endPeriod']))  }}</small></span><h3 class="pull-right text-danger">{{$doneEval[$employee->id]['score']}} %</h3></a></li>
                              

                                @else

                               <!--  <li><a href="#">
                                  <span class="pull-left">Previous Evaluation <br/>
                                    <small>
                                   
                                     N/A</small></span><h3 class="pull-right text-danger">N/A</h3></a></li> -->
                                     <li><a href="#">
                                  <span class="pull-left">{{$evalSetting->name}} <br/>
                                    <small>
                                   
                                     Period covered: </small></span><h5 class="pull-right text-danger text-center"> {{date('M d, Y',strtotime($doneEval[$employee->id]['startPeriod']))  }} <br/>to<br/> {{date('M d, Y',strtotime($doneEval[$employee->id]['endPeriod']))  }} </h5></a></li>
                              
                              

                                @endif

                              </ul>
                                
                              @if ($doneEval[$employee->id]['evaluated'] && $doneEval[$employee->id]['score'] != 0 )
                               <p class="text-center"><a class="btn btn-md btn-default" href="{{action('EvalFormController@show',$doneEval[$employee->id]['evalForm_id']) }} "><i class="fa fa-check"></i> See Evaluation</a><br/>
                               <a href="#"  style="margin-top:5px" class="btn btn-xs btn-flat btn-default" data-toggle="modal" data-target="#myModal{{$doneEval[$employee->id]['evalForm_id']}}"><i class="fa fa-trash"></i> Delete</a><div class="clearfix"></div>

                               @include('layouts.modals', [
                          'modelRoute'=>'evalForm.destroy',
                          'modelID' => $doneEval[$employee->id]['evalForm_id'], 
                          'modelName'=>"Evaluation of: ". $employee->firstname." ". $employee->lastname, 
                          'modalTitle'=>'Delete', 
                          'modalMessage'=>'Are you sure you want to delete this?', 
                          'formID'=>'deleteEmployee',
                          'icon'=>'glyphicon-trash' ])


                              </p>
                              @else <p class="text-center"><a class="btn btn-md btn-success" href="{{action('EvalFormController@newEvaluation', ['user_id'=>$employee->id, 'evalType_id'=>'2', 'currentPeriod'=> $doneEval[$employee['id']]['startPeriod'], 'endPeriod'=>$doneEval[$employee['id']]['endPeriod']]) }} "><i class="fa fa-check-square-o"></i> Evaluate Now</a></p> 
                              @endif
                            <div class="clearfix"></div>


                            </div>
                          </div>
                          <!-- /.widget-user -->

            </div><!--end employee card-->
            @endif
            @endforeach

            @endif <!--end else subordinates not 0 -->

          </div><!-- end row -->
<!-- ******** END < 8 ********** -->

      @else

       <div class="row">

            <h4 class="text-center">Semi-Annual Evaluation<br /> <small>July - December, 2016</small></h4>

            <div class="col-lg-12">
              <table class="table" id="myTeam" >
                <thead >
                      <tr>
                        <th> </th>
                        
                        <th>Last Name</th>
                        <th>First Name</th>
                        <th>Position</th>
                        <th>Status</th>
                        <th>Evaluation Period</th>
                        <th class="text-center">Rating</th>
                        <th class="text-center">Action</th>
                        
                      </tr>
                      </thead>
                      <tbody>
                        @foreach ($mySubordinates as $employee)

                        @if ($employee->id !== Auth::user()->id && $doneEval[$employee->id] !== null)
                      <tr id="row{{$employee->id}}">
                        <td class="text-center ">
                          <a href="{{action('UserController@show',$employee->id)}}">
                         @if ( file_exists('public/img/employees/'.$employee->id.'.jpg') )
                         <img src="{{asset('public/img/employees/'.$employee->id.'.jpg')}}" width='60' height='60' class="img-circle" alt="User Image"/> 
                         @else
                         <img src="{{asset('public/img/useravatar.png')}}" class="img-circle" width='60' height='60' alt="User Image"> 
                         @endif
                        </a>
                      </td>
                        
                        <td>{{$employee->lastname}}</td>
                        <td>{{$employee->firstname}}</td>
                        <td>{{$employee->position->name}}</td>
                        <td>{{$employee->status->name}}</td>
                        <td>{{$doneEval[$employee->id]['startPeriod']}} to {{$doneEval[$employee->id]['endPeriod']}} </td>
                        <td class="text-danger">
                          @if ($doneEval[$employee->id]['evaluated' ]&& $doneEval[$employee->id]['score'] != 50)
                          <h4 class="text-center">{{$doneEval[$employee->id]['score']}} %</h4>
                          @endif

                        </td>
                        <td>
                          @if ($doneEval[$employee->id]['evaluated'] && $doneEval[$employee->id]['score'] != 50 )
                               <p><a class="btn btn-sm btn-primary" href="{{action('EvalFormController@show',$doneEval[$employee->id]['evalForm_id']) }} " style="margin-bottom:5px"><i class="fa fa-check"></i> See Evaluation</a>
                                <a href="#"  style="margin-top:5px" class="btn btn-xs btn-flat btn-default" data-toggle="modal" data-target="#myModal{{$doneEval[$employee->id]['evalForm_id']}}"><i class="fa fa-trash"></i> Delete</a><div class="clearfix"></div>
                              </p>

                              @include('layouts.modals', [
                          'modelRoute'=>'evalForm.destroy',
                          'modelID' => $doneEval[$employee->id]['evalForm_id'], 
                          'modelName'=>"Evaluation of: ". $employee->firstname." ". $employee->lastname, 
                          'modalTitle'=>'Delete', 
                          'modalMessage'=>'Are you sure you want to delete this?', 
                          'formID'=>'deleteEmployee',
                          'icon'=>'glyphicon-trash' ])


                              @else <p><a class="btn btn-sm btn-success" href="{{action('EvalFormController@newEvaluation', ['user_id'=>$employee->id, 'evalType_id'=>'2',  'currentPeriod'=> $doneEval[$employee['id']]['startPeriod'], 'endPeriod'=>$doneEval[$employee['id']]['endPeriod'] ]) }} "><i class="fa fa-check-square-o"></i> Evaluate Now</a>
                             
                           </p> 
                              @endif
                          
                           
                           <!--  <a href="#" class="btn btn-sm btn-flat btn-default"style="margin-bottom:5px"><i class="fa fa-pencil"></i> Edit</a> <div class="clearfix"></div>
                            -->

                        

                        </td>
                        
                      </tr>
                       @endif <!--end if self check -->



                      @endforeach
                      </tbody>

              </table>

            </div>



       </div>

      @endif