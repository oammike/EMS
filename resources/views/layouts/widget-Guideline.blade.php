

                  <div class="box "><!-- style="background: rgba(7,24,156, 0.5)" -->
                    <div class="box-header with-border">
                      <h3 class="box-title"><img src="./public/img/logo_guideline.png" width="150" /></h3>
                      

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

                           <li><a href="{{action('FormSubmissionsController@widgets',['program'=>$prg2,'tab'=>'issues','form'=>'3'])}}" target="_blank">
                            <strong class="text-primary "><i class="fa fa-files-o"></i> Issues  </strong></a></li>

                             <li><a href="{{action('FormSubmissionsController@widgets',['program'=>$prg2,'tab'=>'review','form'=>'3'])}}" target="_blank">
                            <strong class="text-primary "><i class="fa fa-files-o"></i> Review  </strong></a></li>
                          
                          <?php $ctr=0;?>
                          @foreach($groupedForm->keys() as $form)
                          <li @if($ctr==1)class="active"@endif><a href="#tab_{{$ctr+1}}" data-toggle="tab">
                            <strong class="text-primary "><i class="fa fa-files-o"></i> {{$form}}<span id="actives"></span> </strong></a></li>
                          <?php $ctr++;?>  
                          @endforeach


                        </ul>
                        

                        <div class="tab-content" style="background: rgba(256, 256, 256, 0.7)">

                          <?php $ctr=0; $idx=0; $done = array(); ?>
                          @foreach($groupedForm as $form)
                          
                          <div class="tab-pane @if($ctr == 1)active @endif" id="tab_{{$ctr+1}}"> 
                            <form name="form{{$ctr+1}}" method="POST">
                            <?php $ctr2=0; ?>

                              @foreach($form->sortBy('formOrder') as $f)

                                @if($f->type == "header")

                                <{{$f->subType}} style="font-size:1.5em; margin-bottom:30px" class="pull-left text-danger"> {{$f->label}}
                              
                                  </{{$f->subType}}>
                                  <!-- <a style="margin-top: 30px" href="{{action('FormSubmissionsController@show',$f->formID)}}" target="_blank" class="btn btn-xs btn-default pull-right"><i class="fa fa-pie-chart"></i> View Form Stats </a>  -->


                                @endif

                                @if ($f->type == "text")

                                  @if($f->label == "Agent")
                                  <label style="width:90%; padding-bottom: 20px" ><strong>{{$f->label}} </strong><input name="agent" id="{{$f->itemID}}" style="border: none" disabled="disabled" type="{{$f->subType}}" value="{{Auth::user()->firstname}} {{Auth::user()->lastname}}" class="formItem {{$f->className}}" tabindex="{{$f->formOrder}}" /></label><br/>

                                  @else
                                  <label><strong>{{$f->label}}</strong> <input name="{{$f->itemID}}" id="{{$f->itemID}}" type="{{$f->subType}}" class="formItem {{$f->className}}" tabindex="{{$f->formOrder}}" @if($f->required==1)required="required" @endif /></label>

                                  @endif

                                
                                @endif <!--end text -->

                                @if ($f->type == "select")

                                  @if ($idx != $f->selectGroup && !in_array($f->selectGroup,$done) )
                                  <label style="font-weight: bolder; padding-top: 20px" name="{{strtolower($f->itemName)}}" class="label_{{$f->formOrder}}_{{$f->formID}} {{strtolower($f->itemName)}}" data-formID="{{$f->formID}}">{{$f->label}} <br/><br/>
                                    <?php $done[$ctr2] = $f->selectGroup;  ?>
                                    <select id="{{$f->itemID}}" name="{{strtolower($f->itemName)}}" class="select_{{$f->formOrder}}_{{$f->formID}} formItem {{$f->className}} {{strtolower($f->itemName)}}"  data-formID="{{$f->formID}}" tabindex="{{$f->formOrder}}" @if($f->required==1)required="required" @endif >

                                    <option>- select one -</option>
                                    <?php $arr = $groupedSelects[$f->selectGroup]; 
                                          $arr1 = collect($arr);
                                          $coll = array();

                                           foreach($arr as $o)
                                           {
                                             $coll[$o->value] = $o->optionLabel;
                                           }
                                           asort($coll);

                                    ?>

                                    @foreach ($coll as $key => $value)
                                    <option value="{{$key}}">{{$value}} </option>
                                             
                                    @endforeach

                                   
                                    <?php $idx = $f->selectGroup; ?>

                                    <option value="xx"> * add new Payroll *</option>


                                    
                                    </select> 

                                  </label>


                                 

                                  @endif

                                @endif

                                @if ($f->type == "textarea")
                                <div class="clearfix"></div>
                                <label>{{$f->label}} </label>
                                <textarea id="{{$f->itemID}}" name="{{$f->itemName}}" class="formItem {{$f->className}}" placeholder="{{$f->placeholder}}">&nbsp;</textarea>

                                @endif

                                
                                @if($f->type == "radio-group")

                                  <div class="pull-right" style="width: 30%">
                                      <h4><br/>Action:</h4>

                                      @if ($f->label == "Review Action")
                                      <label><input id="{{$f->itemID}}" class="radio-group" type="radio" name="actiontaken" value="VERIFIED" required="required" > <i class="fa fa-3x fa-thumbs-up"></i></input> </label>&nbsp;&nbsp; 
                                      <label><input id="{{$f->itemID}}" class="radio-group" type="radio" name="actiontaken" value="WITH ISSUE" required="required" > <i class="fa fa-3x fa-thumbs-down"></i></input></label>

                                      @else
                                      <label><input id="{{$f->itemID}}" class="radio-group" type="radio" name="actiontaken" value="FOR REVIEW" required="required" > <i class="fa fa-3x fa-thumbs-up"></i></input> </label>&nbsp;&nbsp; 
                                      <label><input id="{{$f->itemID}}" class="radio-group" type="radio" name="actiontaken" value="WITH ISSUE" required="required" > <i class="fa fa-3x fa-thumbs-down"></i></input></label>

                                      @endif
                                      
                                  </div>
                                  <div class="clearfix"></div> 
                                  <div id="addPayroll_{{$f->formID}}"></div> 
                                @endif


                                @if ($f->type == "button")
                                  @if ($f->subType == "submit")
                                  

                                  <button type="submit" data-formid="{{$f->formID}}" name="submit" class="submit btn btn-lg btn-primary" style="margin-top: 20px;" ><i class="fa fa-save"></i> Submit</button>
                                  

                                  @endif
                                @endif


                                <?php $ctr2++;?>


                              
                              @endforeach
                               
                           
                            
                            <?php $ctr++;?>

                            


                          </form>
                          </div><!--end pane1 -->
                        
                          @endforeach
                          <!-- /.tab-pane -->


                         

                     

                        </div>
                        <!-- /.tab-content -->
                      </div>
                      <!-- nav-tabs-custom -->
                      
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer" style="background: rgba(7,24,156, 0.7)">
                       <!--  <a href="javascript:void(0)" class="btn btn-sm btn-primary btn-flat pull-right">View All Requests</a>
                    --> </div>
                    <!-- /.box-footer -->
                  </div>



             
