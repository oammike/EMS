@extends('layouts.main')

@section('metatags')
<title>Performance Appraisal | EMS</title>

<meta name="viewport" content="width=device-width">
<link rel="stylesheet" href="../public/css/bootstrap.min.css">
<link rel="stylesheet" href="../public/js/jquery-ui.css">
<!-- Include SmartWizard CSS -->
<link href="../public/css/smart_wizard.css" rel="stylesheet" type="text/css" />

<!-- Optional SmartWizard theme -->
<link href="../public/css/smart_wizard_theme_circles.css" rel="stylesheet" type="text/css" />
<link href="../public/css/smart_wizard_theme_arrows.css" rel="stylesheet" type="text/css" />
<link href="../public/css/smart_wizard_theme_dots.css" rel="stylesheet" type="text/css" />


<style type="text/css">
 #weight1,#weight2,#weight3,#weight4,#weight5 {
    width: 3em;
    height: 1.6em;
    top: 50%;
    margin-top: -.8em;
    text-align: center;
    line-height: 1.6em;

  }

#slider1 .ui-slider-range,
#slider2 .ui-slider-range,
#slider3 .ui-slider-range,
#slider4 .ui-slider-range,
#slider5 .ui-slider-range { background: #729fcf; }
</style>
@endsection

@section('content')


  <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>&nbsp;</h1>
      <ol class="breadcrumb">
        <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="{{action('NewPA_Form_Controller@index')}}"> Performance Appraisal</a></li>
        <li class="active">New</li>
      </ol>
    </section>

     <section class="content">
      <!-- ******** THE DATATABLE ********** -->
          <div class="row">
             <div class="col-lg-12">

              <div class="box box-primary"  style="background: rgba(256, 256, 256, 0.4);padding:30px">
                <h2>Setup New Annual Performance Appraisal</h2>
                <p>This will guide you step-by-step on setting up performance appraisal form for your program/team.</p><br/><br/><br/>

                <form action="#" id="myForm" role="form" data-toggle="validator" method="post" accept-charset="utf-8">

        <!-- SmartWizard html -->
        <div id="smartwizard">
            <ul>
                <li><a href="#step-1"><span style="font-size: x-large;">Step 1:</span><br /><small>Type of Role</small></a></li>
                <li><a href="#step-2"><span style="font-size: x-large;">Step 2:</span><br /><small>Create Goals</small></a></li>
                <li><a href="#step-3"><span style="font-size: x-large;">Step 3:</span><br /><small>Set Competencies</small></a></li>
                <li><a href="#step-4"><span style="font-size: x-large;">Step 4:</span><br /><small>Assign Form</small></a></li>
            </ul>

            <div class="row">
                
                <!-- ****** STEP 1 ********** -->
                <div id="step-1" class="col-lg-12" style="padding:20px;">
                    <h4>Identify employee role for this evaluation form:</h4><br/><br/>
                    <div id="form-step-0" role="form" data-toggle="validator">
                        <div class="form-group">
                            
                            @foreach($roles as $role)

                              @if (strpos($role->description,"without"))
                              <label><input required="required" type="radio" name="type" value="{{$role->id}}" data-roletype="{{$role->name}}"> {{$role->name}} <br/>
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <small style="font-weight: normal;"> {{$role->description}}</small> </label><div class="clearfix"></div>
                              @endif

                            @endforeach
                           
                            <div class="help-block with-errors" style=" padding:30px; "></div>
                        </div>
                    </div>

                </div>

                <!-- ****** STEP 2 ********** -->
                <div id="step-2" class="col-lg-12" style="padding:20px;">
                    <h3>Establish Goals based on our business objectives</h3>
                    <p>Choose a business objective to formulate your goal statement. You need to select at least three (3) business objectives in order to setup goals for your program/department.</p><br/>

                    <p><i class="fa fa-exclamation-circle"></i> Note: Goals and Objectives for <strong id="roletype" style="font-size: large;"></strong> make up <strong id="percentage" class="text-danger" style="font-size: large;"></strong> of the overall appraisal rating.</p>


                    <h4 class="text-primary text-center"><br/><br/>Our Business Objectives:</h4>
                     <!-- Info boxes -->
                    <div class="row">
                      <?php $c=1; ?>
                      @foreach($objectives as $o)

                      <div style="width: 32%; padding:5px; min-height: 150px; border: dotted 1px #000; float: left; margin:5px">
                        
                        <h1 class="bg-aqua text-center" style="padding:5px 30px 10px 10px;width: 15%; margin:10px; float: left">{{$c}}</h1>
                        <p style="margin-top: 20px;margin-right: 30px" class="text-center">
                          {{$o->name}}
                        </p>
                      </div>
                      <?php $c++; ?>
                      @endforeach

                     
                     
                      
                      
                    </div>
                    <!-- /.row -->




                    <div id="form-step-1" role="form" data-toggle="validator">
                        <div class="form-group">

                            <div class="row">
                                <div class="col-lg-4">
                                  <br/><br/>
                                  <label for="name">Establish GOAL 1:</label>
                                  <select class="goals form-control" id="goal1" data-goalnum=1 required>
                                    <option value="0">* Select a business objective *</option>
                                    <?php $c=1; ?>
                                    @foreach($objectives as $o)

                                    <option value="{{$o->id}}">Objective {{$c}} </option>

                                    <?php $c++; ?>
                                    @endforeach
                                  </select>

                                  <br/><br/>
                                  <label for="goalstmt1">Goal statement 1</label>
                                  
                                  <textarea class="form-control" name="goalstmt1" id="goalstmt1" rows="3" placeholder="type in goal statement based on your chosen objective" required></textarea>

                                  <br/><br/>
                                  <label for="goalstmt1">Activities/Actions 1</label>
                                  
                                  <textarea class="form-control" name="action1" id="action1" rows="3" placeholder="List down actions/activities in order to achieve Goal 1" required></textarea>

                                  <br/>
                                  <label for="weight1">Goal 1 Weight: </label>
                                  <div id="slider1">
                                    <div id="weight1" class="ui-slider-handle"></div>
                                  </div>

                                </div>

                                <div class="col-lg-4">
                                  <BR/><BR/>
                                  <label for="name">Establish GOAL 2:</label>
                                  <select class="goals form-control" id="goal2" required disabled="disabled" data-goalnum=2>
                                    <option value="0">* Select a business objective *</option>
                                    <?php $c=1; ?>
                                    @foreach($objectives as $o)

                                    <option value="{{$o->id}}">Objective {{$c}} </option>

                                    <?php $c++; ?>
                                    @endforeach
                                  </select>

                                  <br/><br/>
                                  <label for="goalstmt2">Goal statement 2</label>
                                  
                                  <textarea class="form-control" name="goalstmt2" id="goalstmt2" rows="3" placeholder="type in goal statement based on your chosen objective" required></textarea>

                                   <br/><br/>
                                  <label for="action2">Activities/Actions 2</label>
                                  
                                  <textarea class="form-control" name="action2" id="action2" rows="3" placeholder="List down actions/activities in order to achieve Goal 2" required></textarea>

                                  <br/>
                                  <label for="weight1">Goal 2 Weight: </label>
                                  <div id="slider2">
                                    <div id="weight2" class="ui-slider-handle"></div>
                                  </div>
                                </div>

                                <div class="col-lg-4">
                                  <BR/><BR/>
                                  <label for="name">Establish GOAL 3:</label>
                                  <select class="goals form-control" id="goal3" required disabled="disabled" data-goalnum=3>
                                    <option value="0">* Select a business objective *</option>
                                    <?php $c=1; ?>
                                    @foreach($objectives as $o)

                                    <option value="{{$o->id}}">Objective {{$c}} </option>

                                    <?php $c++; ?>
                                    @endforeach
                                  </select>

                                  <br/><br/>
                                  <label for="goalstmt3">Goal statement 3</label>
                                  
                                  <textarea class="form-control" name="goalstmt3" id="goalstmt3" rows="3" placeholder="type in goal statement based on your chosen objective" required></textarea>

                                   <br/><br/>
                                  <label for="action3">Activities/Actions 3</label>
                                  
                                  <textarea class="form-control" name="action3" id="action3" rows="3" placeholder="List down actions/activities in order to achieve Goal 3" required></textarea>

                                  <br/>
                                  <label for="weight3">Goal 3 Weight: <input type="text" name=""></label>
                                  <div id="slider3">
                                    <div id="weight3" class="ui-slider-handle"></div>
                                  </div>

                                </div>

                                <div id="goalholder">
                                </div>
                                <div id="hiddengoals">
                                  <input type="hidden" name="hg1" id="hg1" />
                                  <input type="hidden" name="hg2" id="hg2" />
                                  <input type="hidden" name="hg3" id="hg3" />
                                  <input type="hidden" name="hg4" id="hg4" value="0" />
                                  <input type="hidden" name="hg5" id="hg5" value="0" />
                                </div>
                            </div>


                            <div id="total" ><h3>Total Goal weight: <span id="totalweight" class="text-success" style="font-weight: bolder;">100 %</span></h3></div>
                            <a id="addmore" style="margin-top: 20px" class="pull-right btn btn-lg btn-primary" data-count='3'><i class="fa fa-plus"></i> Add more goals </a> 

                            <!-- <div class="row">
                              <div class="col-lg-12 text-center"><br/><br/></div>
                            </div> -->
                            


                            


                            
                            
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                </div>



                <div id="step-3">
                    <h2>Your Address</h2>
                    <div id="form-step-2" role="form" data-toggle="validator">
                        <div class="form-group">
                            <label for="address">Address</label>
                            <textarea class="form-control" name="address" id="address" rows="3" placeholder="Write your address..." required></textarea>
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                </div>
                <div id="step-4" class="">
                    <h2>Terms and Conditions</h2>
                    <p>
                        Terms and conditions: Keep your smile :)
                    </p>
                    <div id="form-step-3" role="form" data-toggle="validator">
                        <div class="form-group">
                            <label for="terms">I agree with the T&C</label>
                            <input type="checkbox" id="terms" data-error="Please accept the Terms and Conditions" required>
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>


                </div>
            </div>
        </div>

        </form>


             



              </div><!--end box-primary-->


             

             

          </div><!--end main row-->
      </section>

      

      
 

    



@endsection


@section('footer-scripts')

<script>
  $.widget.bridge('uibutton', $.ui.button);
</script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="../public/js/modernizr-2.6.2.min.js"></script>
<!-- <script src="../public/js/jquery-1.9.1.min.js"></script> -->
<script src="../public/js/jquery.cookie-1.3.1.js"></script> 
<script src="../public/js/jquery.steps.js"></script>

<!-- smartwizard-->
<!-- <script
      src="https://code.jquery.com/jquery-3.3.1.min.js"
      integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
      crossorigin="anonymous"></script> -->

    <!-- <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script> -->
    <script src="../public/js/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <!-- <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script> -->

    <!-- Include SmartWizard JavaScript source -->
    <script type="text/javascript" src="../public/js/jquery.smartWizard.min.js"></script>
    <script type="text/javascript" src="../public/js/validator.min.js"></script>



<!-- Page script -->
<script>
 
  $(function () {
   'use strict';

   // var handle = $( "#weight1" );
   //  $( "#slider1" ).slider({
   //    
   //    
   //  });

   $('#addmore').on('click',function(){

      var ct = $(this).attr('data-count');
      console.log('data-count');
      console.log(ct);



      if(ct < 5)
        {
          ct++;
          $(this).attr('data-count',ct);
          var code = $('#goalholder').html();
          code += '<div class="col-lg-4">';
          code += '<br/><br/><br/><a class="cancel btn btn-xs btn-default pull-right"><i class="fa fa-times"></i> Cancel</a>';
          code += '<br/><br/><label for="name">Establish GOAL '+ct + '</label>';
          code += '<select class="goals form-control" id="goal'+ct+'"  required><option value="0">* Select a business objective *</option>';
                    <?php $c=1; ?>
                    @foreach($objectives as $o)
                    code += '<option value="{{$o->id}}">Objective {{$c}} </option>';
                    <?php $c++; ?>
                    @endforeach
          code += '        </select><br/><br/><label for="goalstmt1">Goal statement '+ct+'</label>';
          code += ' <textarea class="form-control" name="goalstmt'+ct+'" id="goalstmt'+ct+'" rows="3" placeholder="type in goal statement based on your chosen objective" required></textarea><br/><br/><label for="goalstmt'+ct+'">Activities/Actions '+ct+'</label>';
          code += '<textarea class="form-control" name="action'+ct+'" id="action'+ct+'" rows="3" placeholder="List down actions/activities in order to achieve Goal '+ct+'" required></textarea><br/><label for="weight'+ct+'">Goal '+ct+' Weight: </label><div id="slider'+ct+'"><div id="weight'+ct+'" class="ui-slider-handle"></div></div></div>';
          $('#goalholder').html(code);

          var i = ct-1;
          var ps = $("#weight"+i).html();
          //var prevslide =  ps.split(" ");
          //var newvalue = prevslide[0]/2;
          //console.log("prev slide: ");
          //console.log(prevslide[0]);
          console.log('for i value:');
          console.log(ct);
          makeSlider("#slider"+ct,ct, ct,0);

          if (ct == 5) {console.log($(this)); $(this).attr('disabled',true);}
        } else 
        {
          $.notify("You can only add up to 5 goals.",{className:"error", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} ); 

        }


        //console.log($('#goal'+(ct+1)));

        

      

   });

   $(document).on('click','#form-step-1 a.cancel',function(){

          $(this).parent().fadeOut();
          var more = $('#addmore');
          var ct = more.attr('data-count');


          var count = ct-1

          more.attr('data-count',ct-1);
          more.attr('disabled',false);

          $('#goalholder').html();

          if (count > 3)
          {
            var code = '<div class="col-lg-4">';
            code += '<br/><br/><br/><a class="cancel btn btn-xs btn-default pull-right"><i class="fa fa-times"></i> Cancel</a>';
            code += '<br/><br/><label for="name">Establish GOAL '+count+ '</label>';
            code += '<select class="goals form-control" id="goal'+count+'"  required><option value="0">* Select a business objective *</option>';
                      <?php $c=1; ?>
                      @foreach($objectives as $o)
                      code += '<option value="{{$o->id}}">Objective {{$c}} </option>';
                      <?php $c++; ?>
                      @endforeach
            code += '        </select><br/><br/><label for="goalstmt1">Goal statement '+count+'</label>';
            code += ' <textarea class="form-control" name="goalstmt'+count+'" id="goalstmt'+count+'" rows="3" placeholder="type in goal statement based on your chosen objective" required></textarea><br/><br/><label for="goalstmt'+count+'">Activities/Actions '+count+'</label>';
            code += '<textarea class="form-control" name="action'+count+'" id="action'+count+'" rows="3" placeholder="List down actions/activities in order to achieve Goal '+count+'" required></textarea><br/><label for="weight'+count+'">Goal '+count+' Weight: </label><div id="slider'+count+'"><div id="weight'+ct+'" class="ui-slider-handle"></div></div></div>';
            $('#goalholder').html(code);

          }

         });

   

   $(document).on('change','.goals.form-control',function(){
   //$('.goals.form-control').on("change",function(){

      var selval = $(this).find(":selected").val();
      var goaln = $(this).attr('data-goalnum');
      var goalnum = parseInt(goaln);

      var g1 = $('#goal1');
      var g2 = $('#goal2');
      var g3 = $('#goal3');
      var g4 = $('#goal4');
      var g5 = $('#goal5');



      var selectedGoals = [];// 

      if (goalnum == 1 && selval !== '0'){
        g2.attr('disabled',false);
      }

      if (goalnum == 2 && selval !== '0'){
        g3.attr('disabled',false);
      }

      if (goalnum != 1) selectedGoals.push(g1.find(":selected").val());
      if (goalnum != 2) selectedGoals.push(g2.find(":selected").val());
      if (goalnum != 3) selectedGoals.push(g3.find(":selected").val());
      if (goalnum != 4) selectedGoals.push(g4.find(":selected").val());
      if (goalnum != 5) selectedGoals.push(g5.find(":selected").val());

      var ingoal = selectedGoals.includes(selval);

      if (ingoal) 
        {
          alert("You've already selected that business objective. Please choose a different one."); 
          $('#goal'+goaln+' option[value="'+selval+'"]').attr("selected",false);
          $('#goal'+goaln+' option[value="0"]').attr("selected",true);
          return false; 
        }

    });

   var handle1 = $( "#weight1" );
   var handle2 = $( "#weight2" );
   var handle3 = $( "#weight3" );
   var handle4 = $( "#weight4" );
   var handle5 = $( "#weight5" );


   function recalculate(s,h,v,w1)
   {

        //var slider = $(s).slider("value");
        var w = $(w1).slider("value");
        var balance = 100 - (v + w);

        console.log("balance: "+ balance);
        console.log("w: "+w);

        
        if(s === "#slider3")
        {
          if (balance < 1  )//|| v > (100-w)
          {
            var adjust = $("#slider2");
            var hndle =  $( "#weight2" );
            //var oldval = $(hndle).slider("value");
            //var newval = oldval - balance
            
            //$(adjust).slider({max:0,value:0 });
            $(s).slider({max:0,value:0 });
            h.text("0 %");

            var newval = 100-v;
            $(w1).slider({value:newval});
            handle1.text(newval+ " %");

            // $(adjust).slider({max:v,value:v });
            // hndle.text(v+" %");
            

          }else
              {
                $(s).slider({value: balance});
                h.text(balance+" %");


              }
        } 
        else if(s === "#slider2")
        {
          if (balance < 1  )//|| v > (100-w)
          {
            var adjust = $("#slider3");
            var hndle =  $( "#weight3" );
            
            if(w > 1) //paghatian nila ni 2 & 3 yung remaining balance
            {
              var half = w/2;

              $(s).slider({value:half });
              h.text(half+" %");

              
              $(w1).slider({value:half});
              handle3.text(half+ " %");

            }else
            {
              $(s).slider({max:0,value:0 });
              h.text("0 %");

              var newval = 100-v;
              $(w1).slider({value:newval});
              handle3.text(newval+ " %");

            }

            

          }else
              {
                $(s).slider({value: balance});
                h.text(balance+" %");


              }
        
        } 
        else if(s === "#slider1")
        {
          if (balance < 1  )//|| v > (100-w)
          {
            var adjust = $("#slider3");
            var hndle =  $( "#weight3" );
            //var oldval = $(hndle).slider("value");
            //var newval = oldval - balance
            
            //$(adjust).slider({max:0,value:0 });
            $(s).slider({max:0,value:0 });
            h.text("0 %");

            var newval = 100-v;
            $(w1).slider({value:newval});
            handle3.text(newval+ " %");

            // $(adjust).slider({max:v,value:v });
            // hndle.text(v+" %");
            

          }else
              {
                $(s).slider({value: balance});
                h.text(balance+" %");


              }
        }else
        {
          $(s).slider({value: balance});
          h.text(balance+" %");
        }

        


   }

   function reslide(s,h,v)
   {
       
        //console.log("balance: "+ balance);

        $(s).slider({value: v});
        h.text(v+" %");
   }

   function showPercentage(s1,s2,s3,s4,s5) 
   {
      /*if (s1 instanceof jQuery) s1=0;
      if (s2 instanceof jQuery) s2=0;
      if (s3 instanceof jQuery) s3=0;
      if (s4 instanceof jQuery) s4=0;
      if (s5 instanceof jQuery) s5=0;*/

      var totalP = parseInt(s1)+parseInt(s2)+parseInt(s3)+parseInt(s4)+parseInt(s5);
      

      console.log("totalP: "+ totalP);

      if(totalP >= 101)
      {
        $('#totalweight').removeClass('text-success').addClass('text-danger').html(totalP+" % ");
        $('#totalweight').append("<i class='fa fa-exclamation-circle'></i><br/><em style='font-size:smaller'>Please recalibrate your goal weights and make sure it does not exceeed 100%</em>");

      }else if(totalP == 100)
      {
        $('#totalweight').removeClass('text-danger').removeClass('text-warning').addClass('text-success').html(totalP+" % ");
        $('#totalweight').append("<i class='fa fa-thumbs-up'></i>")

      } else{
        $('#totalweight').removeClass('text-danger').addClass('text-warning').html(totalP+" % ");
        $('#totalweight').append("<i class='fa fa-exclamation-circle'></i><br/><em style='font-size:small'>Please make sure the total goal weight equals 100%</em>");

      } 

   }


   function makeSlider(s,c,i,p)
   {
      var handle = $("#weight"+c);
      var adjHandle = $("#weight"+i);
      console.log('from makeSlider s value');
      console.log(s);

      $(s).slider({
        orientation: "horizontal",
        range: "min",
        max: 100,
        value: 0,
        create: function() {
          //handle.text( $(s).slider( "value" ) +" %" );
          adjHandle.text( 0 + " %" );
          $('#hg'+i).val(0);
        },
        slide: function( event, ui ) {
            var svalue = $(s).slider( "value" );
            console.log('svalue');
            console.log(svalue);
            $('#hg'+i).val(svalue);
            var s1 = $('#hg1').val();var s2 = $('#hg2').val();var s3 = $('#hg3').val();var s4 = $('#hg4').val();var s5 = $('#hg5').val();
        
            showPercentage(s1,s2,s3,s4,s5);
            if(i == '4') {  adjHandle.text( s4 + " %"); }
            if(i == '5') { adjHandle.text( s5 + " %"); }

        
      }
        
      });



   }

   $( "#slider1" ).slider({
      orientation: "horizontal",
      range: "min",
      max: 100,
      value: 50,
      //step:5,
      create: function() {
        handle1.text( $( this ).slider( "value" ) +" %" );
        $('#hg1').val($( this ).slider( "value" ));
      },
      slide: function( event, ui ) {
        handle1.text( ui.value + " %");
        $('#hg1').val(ui.value);
        console.log("slide from 1");
        console.log(ui.value);
        var s1 = $('#hg1').val();var s2 = $('#hg2').val();var s3 = $('#hg3').val();var s4 = $('#hg4').val();var s5 = $('#hg5').val();
        /*var s2 = $('#slider2').slider("value");
        var s3 = $('#slider3').slider("value");
        var s4 = $('#goalholder #slider4').slider("value");
        var s5 = $('#goalholder #slider5').slider("value");*/
        //recalculate("#slider2",handle2,ui.value,"#slider3");
        //showPercentage("#slider2",handle2,"#slider3");
        showPercentage(ui.value,s2,s3,s4,s5);
        console.log('s1 s4');
        console.log(s4);
      }
      
    });

   $( "#slider2" ).slider({
      orientation: "horizontal",
      range: "min",
      max: 100,
      value: 30,
      //step:5,
      create: function() {
        handle2.text( $( this ).slider( "value" ) +" %" );
        $('#hg2').val($( this ).slider( "value" ));
      },
      slide: function( event, ui ) {
        handle2.text( ui.value + " %");
        $('#hg2').val(ui.value);
        /*var s1 = $('#slider1').slider("value");
        //var s2 = $('#slider2').slider("value");
        var s3 = $('#slider3').slider("value");
        var s4 = $('#slider4').slider("value");
        var s5 = $('#slider5').slider("value");
        //showPercentage("#slider3",handle3,ui.value,"#slider1");*/
        var s1 = $('#hg1').val();var s2 = $('#hg2').val();var s3 = $('#hg3').val();var s4 = $('#hg4').val();var s5 = $('#hg5').val();
        
        showPercentage(s1,ui.value,s3,s4,s5);
      }
      
    });

   $( "#slider3" ).slider({
      orientation: "horizontal",
      range: "min",
      max: 100,
      value: 20,
      //step:5,
      create: function() {
        handle3.text( $( this ).slider( "value" ) +" %" );
        $('#hg3').val($( this ).slider( "value" ));
      },
      slide: function( event, ui ) {
        handle3.text( ui.value + " %");
        $('#hg3').val(ui.value);
        /*var s1 = $('#slider1').slider("value");
        var s2 = $('#slider2').slider("value");
        var s4 = $('#slider4').slider("value");
        var s5 = $('#slider5').slider("value");*/

        var s1 = $('#hg1').val();var s2 = $('#hg2').val();var s3 = $('#hg3').val();var s4 = $('#hg4').val();var s5 = $('#hg5').val();
        
        showPercentage(s1,s2,ui.value,s4,s5);
      }
      
    });

    // ********* Step show event *******************

    $("#smartwizard").on("showStep", function(e, anchorObject, stepNumber, stepDirection, stepPosition) {
       //alert("You are on step "+stepNumber+" now");
       if(stepPosition === 'first'){
           $("#prev-btn").addClass('disabled');
       }else if(stepPosition === 'final'){
           $("#next-btn").addClass('disabled');
       }else{
           $("#prev-btn").removeClass('disabled');
           $("#next-btn").removeClass('disabled');
       }
    });



    // Toolbar extra buttons
    var btnFinish = $('<button></button>').text('Finish')
                                     .addClass('btn btn-success')
                                     .on('click', function(){
                                            if( !$(this).hasClass('disabled')){
                                                var elmForm = $("#myForm");
                                                if(elmForm){
                                                    elmForm.validator('validate');
                                                    var elmErr = elmForm.find('.has-error');
                                                    if(elmErr && elmErr.length > 0){
                                                        alert('Kindly fill out all required fields to complete form.');
                                                        return false;
                                                    }else{
                                                        alert('Great! we are ready to submit form');
                                                        elmForm.submit();
                                                        return false;
                                                    }
                                                }
                                            }
                                        });
    var btnCancel = $('<button></button>').text('Cancel')
                                     .addClass('btn btn-danger')
                                     .on('click', function(){
                                            $('#smartwizard').smartWizard("reset");
                                            $('#myForm').find("input, textarea").val("");
                                        });


    // Smart Wizard
    $('#smartwizard').smartWizard({
            selected: 0,
            theme: 'arrows',
            transitionEffect:'fade',
            showStepURLhash: true,
            toolbarSettings: {toolbarPosition: 'bottom',
                              toolbarExtraButtons: [btnFinish]
                            },
            anchorSettings: {
                        markDoneStep: true, // add done css
                        markAllPreviousStepsAsDone: true, // When a step selected by url hash, all previous steps are marked done
                        removeDoneStepOnNavigateBack: true, // While navigate back done step after active step will be cleared
                        enableAnchorOnDoneStep: true // Enable/Disable the done steps navigation
                    }
    });

    $("#smartwizard").on("leaveStep", function(e, anchorObject, stepNumber, stepDirection) {
        var elmForm = $("#form-step-" + stepNumber);
        // stepDirection === 'forward' :- this condition allows to do the form validation
        // only on forward navigation, that makes easy navigation on backwards still do the validation when going next
        if(stepDirection === 'forward' && elmForm){
            elmForm.validator('validate');
            var elmErr = elmForm.children('.has-error');
            if(elmErr && elmErr.length > 0){
                // Form validation failed
                return false;
            }
        }

        if (stepNumber == 0)
        {
          var role = $('input[name="type"]:checked');
          var roletype = role.attr('data-roletype');
          

          switch(role.val())
          {
            case '2': { var p = "70%";}break;
            case '4':  { var p = "60%";}break;
            case '6':  { var p = "50%";}break;
          }

          $('#roletype').html(roletype);
          $('#percentage').html(p);

          console.log("roletype: "+ roletype);
        }

        //console.log("step is: "+ stepNumber);
        return true;
    });

    $("#smartwizard").on("showStep", function(e, anchorObject, stepNumber, stepDirection) {
        // Enable finish button only on last step
        if(stepNumber == 3){
            $('.btn-finish').removeClass('disabled');
        }else{
            $('.btn-finish').addClass('disabled');
        }
    });

    // ********* Step show event **********************

        
      
      
   });

   

</script>
<!-- end Page script -->



@stop