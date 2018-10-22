
             
             <div class="col-lg-7 col-sm-6 col-xs-12">

                  <div class="box box-danger"style="background: rgba(256, 256, 256, 0.5)">
                    <div class="box-header with-border">
                      <h3 class="box-title"><img src="./public/img/logo_postmates.png" width="150" /></h3>
                      

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

                                <{{$f->subType}} style="font-size:1.5em; margin-bottom:30px" class="pull-left text-danger"> {{$f->label}}  <a href="{{action('FormSubmissionsController@show',$f->formID)}}" class=" btn btn-xs btn-default"><i class="fa fa-pie-chart"></i> View Form Stats </a> </{{$f->subType}}>


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
                                    @foreach ($groupedSelects[$f->selectGroup] as $option) <!-- ->sortByDesc('formOrder') @if($option->selected==1)selected="selected" @endif-->
                                    <option value="{{strtolower($option->value)}}"  >{{$option->optionLabel}}</option>
                                    <?php $idx = $f->selectGroup; ?>
                                    @endforeach
                                    </select> 

                                  </label>
                                  @endif

                                @endif

                                @if ($f->type == "textarea")
                                <div class="clearfix"></div>
                                <label>{{$f->label}} </label>
                                <textarea id="{{$f->itemID}}" name="{{$f->itemName}}" class="formItem {{$f->className}}" placeholder="{{$f->placeholder}}">&nbsp;</textarea>

                                @endif


                                @if ($f->type == "button")
                                  @if ($f->subType == "submit")
                                  <button type="{{$f->subType}}" name="{{$f->subType}}" class="submit btn btn-lg btn-danger pull-right" style="margin-top: 20px;" ><i class="fa fa-bicycle"></i> Submit</button>
                                  <!-- <a href="#" class="submit btn btn-md btn-primary pull-right" style="margin-top: 20px;" id="submit_{{$f->widgetTitle}}"><i class="fa fa-bicycle"></i> Submit </a> -->
                                  @endif
                                @endif


                                <?php $ctr2++;?>
                              
                              @endforeach
                               
                           
                            
                            <?php $ctr++;?></form>
                          </div><!--end pane1 -->
                        
                          @endforeach
                          <!-- /.tab-pane -->

                     

                        </div>
                        <!-- /.tab-content -->
                      </div>
                      <!-- nav-tabs-custom -->
                      
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer" style="background: rgba(221, 75, 57, 0.7)">
                       <!--  <a href="javascript:void(0)" class="btn btn-sm btn-primary btn-flat pull-right">View All Requests</a>
                    --> </div>
                    <!-- /.box-footer -->
                  </div>


                 
            



             </div>


             
