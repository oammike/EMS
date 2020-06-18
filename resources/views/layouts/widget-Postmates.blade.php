

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
                          <!-- <li id="playbook"><a href="#tab_{{count($groupedForm->keys())+1}}" data-toggle="tab">
                            <strong class="text-primary "><i class="fa fa-files-o"></i> Playbook<span id="actives"></span> </strong></a></li> -->

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
                                <a class="btn btn-default btn-xs" href="{{action('CampaignController@widgets',['id'=>$prg, 'wID'=>$f->formID])}}" target="_blank"><i class="fa fa-external-link"></i> New Window </a>
                                  <a href="{{action('FormSubmissionsController@show',$f->formID)}}" target="_blank" class=" btn btn-xs btn-default"><i class="fa fa-pie-chart"></i> View Form Stats </a> </{{$f->subType}}>


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


                          <?php /*<div class="tab-pane" id="tab_{{count($groupedForm->keys())+1}}">
                            
                                  
                                  <div style="position: relative; border: solid 1px #333; width: 100%">
                                    <a  target="_blank" alt="Oder Placement Agent Call Flow" title="Oder Placement Agent Call Flow" href="https://docs.google.com/document/d/1ZCWAty_qOk3C94Sgt0i6WIu77zwPqAKT7-oAQufRlS8/edit" style="position: absolute; left: 21.72%; top: 22%; width: 23.71%; height: 10.15%; z-index: 2;"></a>
                                    <a target="_blank"  alt="Fleet Signal Verification Task" title="Fleet Signal Verification Task" href="https://docs.google.com/presentation/d/1E--CNSFjODjOMN51K7Wl-Y6PsiXPXwki-__RbWUOQkg/edit#slide=id.p" style="position: absolute; left: 21.72%; top: 33.54%; width: 23.71%; height: 10.15%; z-index: 2;"></a>

                                    <a  target="_blank" alt="Online Ordering Handbook" title="Online Ordering Handbook" href="https://docs.google.com/document/d/1GhkvHjxMNGalbD6SLdTFX4lysaCgubPBQSuHIVcagfY/edit" style="position: absolute; left: 21.72%; top: 45.54%; width: 23.71%; height: 10.15%; z-index: 2;"></a>

                                    <a  target="_blank" alt="Payments Over the Phone" title="Payments Over the Phone" href="https://docs.google.com/presentation/d/1352m1XKGtOxJjPLyiE1A0egk7YOA4epNBx0fCLcRW9A/edit#slide=id.p" style="position: absolute; left: 21.72%; top: 56.92%; width: 23.71%; height: 10.15%; z-index: 2;"></a>

                                    <a  target="_blank" alt="Raise Issue Escalation" title="Raise Issue Escalation" href="https://docs.google.com/spreadsheets/d/1MEIJtRPhskS1NX-at_AvC_CQNy40O51ujUJVIhxx4OY/edit#gid=0" style="position: absolute; left: 21.72%; top: 68.46%; width: 23.71%; height: 10.15%; z-index: 2;"></a>

                                    <!-- 2nd col -->

                                    <a  target="_blank" alt="Workbench Flow" title="Workbench Flow" href="https://docs.google.com/presentation/d/1vVeCtZ_2m_cc_6HFpfYhD-PN2oQ9_ACOwPzQyfXR9vY/edit#slide=id.p" style="position: absolute; left: 59.17%; top: 22%; width: 23.71%; height: 10.15%; z-index: 2;"></a>

                                    <a  target="_blank" alt="Agent Work flow" title="Agent Work flow" href="https://docs.google.com/document/d/1O53dLIDhFLh2lwB88iwO3S7eQTw0fkXKoMvhpH0uwG8/edit" style="position: absolute; left: 59.17%; top: 33.54%; width: 23.71%; height: 10.15%; z-index: 2;"></a>

                                    <a  target="_blank" alt="Updates Common Questions" title="Updates Common Questions" href="https://docs.google.com/document/d/1U0YSilZ11R0UbXdAVzDl8nbTpxjdJvavmgZYGkeounU/edit" style="position: absolute; left: 59.17%; top: 45.54%; width: 23.71%; height: 10.15%; z-index: 2;"></a>

                                    <a  target="_blank" alt="Food and Customer Names Pronunciation" title="Food and Customer Names Pronunciation" href="https://docs.google.com/document/d/1bRperepsneaL5lLl5072BCxRTOHspQtsekgZB6gHFwY/edit" style="position: absolute; left: 59.17%; top: 56.92%; width: 23.71%; height: 10.15%; z-index: 2;"></a>

                                    <a  target="_blank" alt="QA Updates" title="QA Updates" href="https://docs.google.com/document/d/1y2X6bpMECV01hlSb3KqpyESwx-O2_QkClweh1mmzspI/edit" style="position: absolute; left: 59.17%; top: 68.46%; width: 23.71%; height: 10.15%; z-index: 2;"></a>

                                    <!-- bottom row-->

                                    <a  target="_blank" alt="IVR Quick Codes" title="IVR Quick Codes" href="https://docs.google.com/spreadsheets/d/1yLtZk4EBGGz755E39-j2rGqyaVzK9fpigfpaBs99LwQ/edit#gid=0" style="position: absolute; left: 12.34%; top: 84%; width: 21.63%; height: 10.15%; z-index: 2;"></a>

                                    <a target="_blank" alt="OO Items" title="OO Items" href="https://docs.google.com/spreadsheets/d/1fenUdq6ML-YNm9pVOovc0nHp7E_tkjsa6ezDxrHlUKM/edit#gid=222910634" style="position: absolute; left: 37.34%; top: 84%; width: 21.63%; height: 10.15%; z-index: 2;"></a>

                                    <a target="_blank" alt="Tools Links" title="Tools Links" href="https://docs.google.com/document/d/14VOr_xruOIpC6AK0Gz3l2AN6jXDUAQXt7-NWXKvP6oQ/edit" style="position: absolute; left: 62.73%; top: 84%; width: 21.63%; height: 10.15%; z-index: 2;"></a>


                                    <img src="./storage/uploads/playbook.png" usemap="#image-map" width="100%">
                                  </div>
                                  

                                  
                          </div>  */ ?>

                     

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



             
