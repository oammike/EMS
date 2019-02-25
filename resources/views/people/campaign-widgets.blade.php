@extends('layouts.main')

@section('metatags')
<title> {{$groupedForm[$wID][0]->widgetTitle}} | {{$camp->name}}  Widget | OpenAccess EMS</title>
<style type="text/css">
.box.box-widget.widget-user-2{min-height: 455px;}
    input[type="text"]{ background: none; border: none; border-bottom: solid 2px #666 }
    /* Change Autocomplete styles in Chrome*/
input:-webkit-autofill,
input:-webkit-autofill:hover, 
input:-webkit-autofill:focus
textarea:-webkit-autofill,
textarea:-webkit-autofill:hover
textarea:-webkit-autofill:focus,
select:-webkit-autofill,
select:-webkit-autofill:hover,
select:-webkit-autofill:focus {
  border:none;
  -webkit-text-fill-color: #333;
  -webkit-box-shadow: 0 0 0px 1000px #f2fcff inset;
  transition: background-color #f2fcff ease-in-out 0s;
}
.tab-content label {font-weight: normal}

</style>

@endsection

@section('bodyClasses')

@endsection

@section('content')


  <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1 class="pull-left">
        @if(is_null($camp->logo))
          
          {{$camp->name}}

        @elseif ($camp->name=='IMO')
        <img src="../../public/img/{{$camp->logo->filename}}" height="55" class="pull-left"  />


        @else
        <img src="../../public/img/{{$camp->logo->filename}}" width="150px" class="pull-left" style="margin-top: 20px" /> 
        <h1 class="pull-left" style="margin: 25px">Productivity Widget</h1>
        @endif
        
      </h1>
      

      @if(is_null($camp->logo->filename))
      <a href="{{action('CampaignController@index')}}" class="btn btn-xs btn-default" style="margin-left: 60px"><i class="fa fa-arrow-left">
        @else
         <a href="{{action('CampaignController@index')}}" class="btn btn-xs btn-default" style="margin-top:30px; margin-left: 60px"><i class="fa fa-arrow-left">
          @endif
       



      </i> Back to all Programs </a>
      
      @if($camp->has_vicidial)
        
        
      <a style="margin-top:30px; margin-left: 10px" class="btn btn-xs btn-default" href="{{ url('campaignStats/'.$camp->id) }}">View Campaign Stats</a>
      <a style="margin-top:30px; margin-left: 10px" class="btn btn-xs btn-default" href="{{ url('agentStats/'.$camp->id) }}">View Agent Stats</a>
      
      @endif
      
      <div class="clearfix"></div>
      <ol class="breadcrumb">
        <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Programs</li>
      </ol>
    </section>

     <section class="content">
      <div class="row">
      <?php $ctr=0; $idx=0; $done = array(); ?>
      @foreach($groupedForm as $form)
      @if($wID == $form->first()->formID) 
        <div class="col-lg-12" >
                  <div class="box box-danger"style="background: rgba(256, 256, 256, 0.5)">
                    <div class="box-header with-border">
                      <h3 class="box-title"></h3>
                      

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

                         
                          
                          <div class="tab-pane @if($form->first()->formID == $wID)active @endif" id="tab_{{$ctr+1}}"> 
                            <form name="form{{$ctr+1}}" method="POST">
                            <?php $ctr2=0; ?>

                              @foreach($form->sortBy('formOrder') as $f)

                                @if($f->type == "header")

                                <{{$f->subType}} style="font-size:2.5em; margin-bottom:30px" class="pull-left text-danger"> {{$f->label}}
                               
                                 </{{$f->subType}}>
                                  <a href="{{action('FormSubmissionsController@show',$f->formID)}}" target="_blank" class=" btn btn-xs bg-black pull-right"><i class="fa fa-pie-chart"></i> View Form Stats </a> 


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
        </div><!--end col-->
        @endif
      @endforeach
                          



             

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


   /*---------- POSTMATES WIDGET ----------- */
   @if(count($groupedForm)>0)

   $('.escalation_online_order, .escalation_phone, .escalation_tablet').hide();
   //.merchant_closed_confirmation, .Open_confirmation, .confirmed_options 
   //
   $('select[name="escalation"]').on('change', function(){ 

      var escalation = $('select[name="escalation"] :selected').val();

      switch (escalation)
      {
        case "online_order": { $('.escalation_online_order').fadeIn();$('.escalation_phone, .escalation_tablet, .req').hide();} break;
        case "phone":  { $('.escalation_phone').fadeIn();$('.escalation_online_order, .escalation_tablet').hide();} break;
        case "tablet": { $('.escalation_tablet').fadeIn();$('.escalation_online_order, .escalation_phone').hide();} break;
        default:  $('.escalation_online_order, .escalation_phone, .escalation_tablet').hide();
      }

   

   });

  


   /********** signal verification *************/

   $('.select_6, .select_7, .select_8, .label_6_2, .label_7_2, .label_8_2').hide();

  $('select.formItem').on('change',function(){


    var itemName = $(this)[0]['name'];
    var formID = $(this).attr('data-formID');
    var selectedItem = $('select[name='+itemName+'] :selected').val();
    var itemOrder = $(this)[0]['tabIndex'];
    var s = "."+selectedItem.toLowerCase()+"_"+itemName;
    //var elemID = $(this)[0]['id'];

    if (itemOrder=='5' && formID=='2'){
      $('.label_6_2, .label_7_2, .label_8_2').fadeOut();
    } else if (itemOrder=='6' && formID=='2'){
      $('.label_7_2, .label_8_2, .label_9_2').fadeOut();
    } else if (itemOrder=='7' && formID=='2'){
      $('.label_8_2, .label_9_2, .label_10_2').fadeOut();
    }




    if (selectedItem.toLowerCase() == "yes" || selectedItem.toLowerCase() == "no"){
      $('select_'+(itemOrder+1), 'select_'+itemOrder).hide();
      var s = ".confirmed_"+itemName;
      
      var x = $(s);
      //var y = x[0]['children'][2];

      //y.

      // console.log("className:");
      // console.log(x[1]['className']);
      if (x.length == 0 
            || x[1]['className'] == "select_7_2 formItem form-control confirmed_merchant_refused_confirmation"
            || (x.length == 2 && x.selector ==".confirmed_merchant_cash_only_confirmation") )
      {
        var newItem = $(this).parent();
        console.log("parent: ");
        $('.added').fadeOut();
        console.log(newItem);
        var htmlcode ='<label class="added pull-left" style="font-weight: bolder; padding-top: 20px; display: inline-block;"><strong>Confirmed</strong>';
        htmlcode += '     <select id="x" data-from="'+itemName+'" class="form-control formItem">';
        htmlcode += '         <option value="Confirmed_By_Phone" >Confirmed By Phone</option>';
        htmlcode += '         <option value="Confirmed_By_Voicemail">Confirmed By Voicemail</option>';
        htmlcode += '         <option value="Confirmed_Online">Confirmed Online</option>';
        htmlcode == '     </select></label>';

        newItem.append(htmlcode);
      } else $(s).fadeIn();

      //$(y).html('<option value="Confirmed_By_Phone">Confirmed By Phone</option><option value="Confirmed_By_Voicemail">Confirmed By Voicemail</option><option value="Confirmed_Online">Confirmed Online</option>');
      

    }
    else {

      $('.added').fadeOut();

      if(formID == '2'){
        //$('label[name="'+itemName+'"]').fadeOut();//'select.select_'+(itemOrder+1), 'select.select_'+itemOrder, 
        $('.label_'+itemOrder+'_'+formID,'.label_'+(itemOrder+1)+'_'+formID).fadeOut();
        $(s).fadeIn();
      }
      

      console.log("OPEN : "+ s );
    console.log(s);
    console.log("order: "+itemOrder+" | selectedItem: "+selectedItem+" | index: "+itemOrder)
    }
   

    console.log("FormID: "+formID);

    

   });





   $('.submit').on('click',function(e){
      e.preventDefault();

      $('input,textarea,select').filter('[required]:visible').each(
            function(){
              var checkCt=0;
              var v = $(this).val();
              if (v == ""){
                $(this).css('border',"solid 3px #e24527");
                return false;
              } 
              
                $(this).css('border',"none");
                if (v == "- select one -") 
                  return false;
                     
              
            }
        ).promise().done(function(){
          var _token = "{{ csrf_token() }}";
          var formItems_select = $('select.formItem').filter(':visible');
          var formItems_input = $('input.formItem').filter(':visible');
          var formItems_textarea = $('textarea.formItem').filter(':visible');
          
          var formItems ={}; //, inputs: formItems_input, textareas: formItems_textarea }
          var ctr=0;

          formItems_input.each(function(){
             var n = $(this);
             if (n[0]['name'] !== "agent") {
              formItems[ ctr+'_'+n[0]['id'] ] = $(this).val();
             }
             
             ctr++;
          });

          formItems_select.each(function(){
             var n = $(this);
             formItems[ ctr+'_'+n[0]['id'] ] = $(this).val();
             if (n[0]['id']=='x'){
              formItems[ n[0]['id']+'_from' ] = $(this).attr('data-from');
             }
             ctr++;
          });

          formItems_textarea.each(function(){
             var n = $(this);
             formItems[ ctr+'_'+n[0]['id'] ] = $(this).val();
             ctr++;
          });
          console.log(formItems);
            $.ajax({
                        url: "{{action('FormSubmissionsController@process')}}",
                        type:'POST',
                        data:{ 
                          'formItems': formItems,
                          'user_id':"{{Auth::user()->id}}",
                          '_token':_token
                        },

                       
                        success: function(res)
                        {
                          console.log(res);

                          $.ajax({
                                      url: "{{action('HomeController@logAction','4')}}",
                                      type: "GET",
                                      data: {'action': '4', 'formid': res.formid, 'usersubmit':res.usersubmit},
                                      success: function(response){
                                                console.log(response);

                                    }

                          });

                          if (res.status == '0')
                            $.notify(res.error,{className:"error",globalPosition:'right center',autoHideDelay:7000, clickToHide:true} );
                          else {
                            $('button[name="submit"]').fadeOut();
                            $.notify("Form successfully submitted.",{className:"success",globalPosition:'right center',autoHideDelay:7000, clickToHide:true} );
                            window.setTimeout(function(){
                                          //window.location.href = "{{action('HomeController@index')}}";
                                          location.reload();
                                        }, 2000);
                          }

                           
                        }, error: function(res){
                          console.log("ERROR");
                          $.notify("An error occured. Please try re-submitting later.",{className:"error",globalPosition:'right center',autoHideDelay:7000, clickToHide:true} );
                            
                        }


              });
        });
   
  
    
   });


   

   @endif
  /*---------- END POSTMATES WIDGET ----------- */

   

   


      
      
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