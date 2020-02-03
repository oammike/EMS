@extends('layouts.main')

@section('metatags')
  <title>Transfer Reward Points</title>
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
    <h1><i class="fa fa-gift"></i> Open Access BPO Rewards 
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
        <div class="box"  style="background: rgba(256, 256, 256, 0.4); min-height: 2200px">
          <div class="box-heading"></div>
          <div class="box-body">
            <div class="row">
              <div class="col-lg-12">
                <h4 class="text-center">Transfer YOUR Reward Points to Anyone <br><br><br></h4>

                @if($remaining_points <= 0 && $remaining_points !== null)

                <h1 class="text-center text-danger"><img src="storage/uploads/rewards.png" class="pull-right" width="40%" />
                  <br/><br/><br/>Oops! <br/><span style="font-size: small;"> You don't have enough reward points to transfer</span>.</h1>



                @else

                <div class="row">

                  <div class="col-lg-3"></div>
                  <div class="col-lg-6" style="background-color: #dedede;padding:20px">
                    <label>Transfer to: </label>
                    <input type="text" name="transferto" required="required" id="transferto" class="form-control" placeholder="search for FIRSTNAME, LASTNAME, NICKNAME, or PROGRAM name" /> 
                    <input id="transfer_id" disabled="disabled" type="hidden" />
                    <input id="transfer_name" disabled="disabled" type="hidden" />
                    <input id="transfer_prog" disabled="disabled" type="hidden" />
                    
                    <div id="cardholder"></div>
                    <label><br/>Notes / Comments: </label>
                    <textarea id="notes" class="form-control"></textarea>
                    


                   

                   
                  </div>
                  <div class="col-lg-3"></div>
                  
                </div>
                <div class="row">

                  <div class="col-lg-3"></div>
                  <div class="col-lg-6" style="padding:20px; background-color: #dedede">
                    <div class="row">
                      <div class="col-lg-6">

                        @if( (float)$allTransfers < 50 )
                        <div class="btn-group-vertical ml-3 mt-3" role="group">
                          <div class="row">
                         
                              <label>Number of Points: <input required="required" class="text-center form-control mb-2" id="code" autocomplete="off"> </label>
                            </div>

                         
                          <div class="row">
                              <button type="button" class="btn btn-outline-secondary py-3" style="padding:24px" onclick="document.getElementById('code').value=document.getElementById('code').value + '1';">1</button>
                              <button type="button" class="btn btn-outline-secondary py-3"style="padding:24px" onclick="document.getElementById('code').value=document.getElementById('code').value + '2';">2</button>
                              <button type="button" class="btn btn-outline-secondary py-3"style="padding:24px" onclick="document.getElementById('code').value=document.getElementById('code').value + '3';">3</button>
                          </div>
                          <div class="row">
                              <button type="button" class="btn btn-outline-secondary py-3" style="padding:24px" onclick="document.getElementById('code').value=document.getElementById('code').value + '4';">4</button>
                              <button type="button" class="btn btn-outline-secondary py-3" style="padding:24px" onclick="document.getElementById('code').value=document.getElementById('code').value + '5';">5</button>
                              <button type="button" class="btn btn-outline-secondary py-3" style="padding:24px" onclick="document.getElementById('code').value=document.getElementById('code').value + '6';">6</button>
                          </div>
                          <div class="row">
                              <button type="button" class="btn btn-outline-secondary py-3" style="padding:24px" onclick="document.getElementById('code').value=document.getElementById('code').value + '7';">7</button>
                              <button type="button" class="btn btn-outline-secondary py-3" style="padding:24px" onclick="document.getElementById('code').value=document.getElementById('code').value + '8';">8</button>
                              <button type="button" class="btn btn-outline-secondary py-3" style="padding:24px" onclick="document.getElementById('code').value=document.getElementById('code').value + '9';">9</button>
                          </div>
                          <div class="row">
                              <button type="button" class="btn btn-outline-secondary py-3" style="padding:24px" onclick="document.getElementById('code').value=document.getElementById('code').value.slice(0, -1);">&lt;</button>
                              <button type="button" class="btn btn-outline-secondary py-3" style="padding:24px" onclick="document.getElementById('code').value=document.getElementById('code').value + '0';">0</button>
                              <button type="button"  class="btn btn-primary py-3" id="go" style="padding:24px" >Go</button>
                              <!-- data-toggle="modal" data-target="#mytransfer" -->
                          </div>
                           <h5 class="text-danger text-left">* Maximum allowable daily transfer:<br/> <strong>50 points</strong></h5>
                        </div>

                        @else

                        <h4 class="text-danger">Sorry, you've already reached <strong>maximum daily transfer limit</strong> of <span style="font-size: larger; font-weight: bolder;">50 points.</span></h4>
                        <p>Please try again after 24 hours.</p>

                        @endif
                      </div>

                      <div class="col-lg-6">
                        <img src="storage/uploads/rewards.png" class="pull-left" width="100%" /><br/>
                        <table class="table table-bordered text-center">
                          <tr>
                            
                              <td style="font-size: smaller;">Total Remaining Points</td>
                              <td style="font-size: smaller;">Total Transfers for Today</td>
                              <td style="font-size: smaller;">Total Received for Today</td>
                            
                          </tr>

                          <tr>
                            <td style="font-size: large;" class="text-primary">
                              <strong id="remainingpts" data-val="{{$remaining_points}}"> 
                                @if ($remaining_points > 10000)
                                <em style="font-size: smaller;">UNLIMITED</em>
                                @else
                                {{ $remaining_points }}
                                @endif
                              </strong></td>
                            <td class="text-danger"><strong id="allTransfers" data-val="{{$allTransfers}}">{{$allTransfers}}</strong>  </td>
                            <td class="text-success"><strong id="allReceived">0</strong> </td>
                          </tr>
                        </table>
                        

                        
                      </div>


                    </div>

                    
                   

                   
                   
                     

                    



                  <a id="makenew" class="btn btn-lg btn-success pull-right" style="display: none" href="{{action('UserController@rewards')}}"><i class="fa fa-exchange"></i> Make New Transfer</a>

                   
                  
                    


                     
                  </div>
                  <div class="col-lg-3"></div>
                  
                </div>

                @endif
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


  <script src="public/js/jquery.easy-autocomplete.min.js" type="text/javascript"></script>

	<script>
		window.selected_reward_id = 0;
		$(function() {

      var options = {
        url: "{{action('UserController@listAllActive')}}",

        getValue: function(item) {
          return item.lastname+ ', '+item.fullname+' ( "'+item.nickname+'" )  -- '+ item.program;
        },

        list: {
                match: {
                  enabled: true
                },
                sort: {
                  enabled: true
                },
                maxNumberOfElements: 100,
                onSelectItemEvent: function() {
                                  var value = $("#transferto").getSelectedItemData().id;
                                  var valname = $("#transferto").getSelectedItemData().lastname; 
                                  var valfname = $("#transferto").getSelectedItemData().fullname;
                                  var prog = $("#transferto").getSelectedItemData().program;

                                  $("#transfer_id").val(value).trigger("change");
                                  $("#transfer_name").val(valname+', '+valfname).trigger("change");
                                  $("#transfer_prog").val(prog).trigger("change");
                                },
                showAnimation: {
                                type: "fade", //normal|slide|fade
                                time: 400,
                                callback: function() {}
                              },

                hideAnimation: {
                  type: "slide", //normal|slide|fade
                  time: 400,
                  callback: function() {}
                }
              },


       

        template: {
          type: 'custom',
          method: function (value, item) {

            if( {{$userID}} !== item.id ){
                  if (item.nickname){
                  
                    return  '<span></span><img src="public/img/employees/'+item.id+'.jpg" width=70 />&nbsp;&nbsp;'  + value.toUpperCase();
                  }
                  else{
                    
                    return  '<i style="margin:10px" class="fa fa-user fa-5x"></i> '+ value.toUpperCase();
                  }

            }
            
            //console.log(value);
            //console.log(item);
          }
        }
      };

      $('#transferto').easyAutocomplete(options);

      $('#go').on('click',function(){
        console.log("go");
        var amt = $('#code').val();
        var receiver = $('#transfer_name').val();
        var rid = $('#transfer_id').val();
        var p = $('#transfer_prog').val();
        var a = $('#allTransfers').attr('data-val');
        var ap =  parseInt(a) + parseInt(amt);

        $('#amt').html(amt);
        $('#receiver').html(receiver);
        $('#campaign').html(p);
        $('#pic').attr('src','public/img/employees/'+rid+'.jpg');

        if(receiver == "")
          $.notify("Please specify the receiver of reward points.",{ className:"error", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );

        else if(amt == "")
          $.notify("Please enter number of points you want to transfer.",{ className:"error", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );
        
        else if (amt > 50 || ap > 50){
           $.notify("Sorry, you've reached maximum amount of transferrable points.\nPlease try a smaller amount.",{ className:"error", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );
          return false;

        }

        else if(isNaN(amt) || !(amt.indexOf(".") == -1)) {
          $.notify("Sorry, you\'ve entered an Invalid Amount.\nPlease try again.",{ className:"error", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );

        }

        else
          $('#mytransfer').modal('show');

      });

      $('#proceed').on('click',function(){

        var points = $('#code').val();
        var to = $('#transfer_name').val();
        var id_to =  $('#transfer_id').val();
        var a = $('#allTransfers').attr('data-val');
        var r = $('#remainingpts').attr('data-val');
        var ap =  parseInt(a) + parseInt(points);
        var rp =   parseInt(r)- parseInt(points);
        var pw = $('#pw').val();

        var _token = "{{ csrf_token() }}";

        if (points > 50 || ap > 50){
           $.notify("Sorry, you've reached maximum amount of transferrable points.\nPlease try a smaller amount.",{ className:"error", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );
          return false;

        }
         
        else if(isNaN(points)) {

          $.notify("Sorry, you\'ve entered an Invalid Value.\nPlease try again.",{ className:"error", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );
          return false;

        }

    

        else{

          $.ajax({
            type:"POST",
            url : "{{ url('/rewardsTransfer') }}",
            data : {
                      'points' : points,
                      'to' : to,
                      'id_to' : id_to,
                      'notes': $('#notes').val(),
                      'pw' : pw,
                      '_token' : _token

            },
            success : function(data){
                                      console.log(data);

                                      if (data.success == '1')
                                      {
                                        $('#allTransfers').html("");
                                        $('#allTransfers').html(ap);
                                        $('#remainingpts').html("");
                                        $('#remainingpts').html(rp);

                                        $.notify(points+ data.message + '\n'+data.user,{className:"success", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );
                                        $('#go').attr('disabled',true);

                                        $('#makenew').fadeIn();

                                      }else {

                                        $.notify(data.message + '\n'+data.user,{className:"error", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );

                                      }
                                      

                                      
            },
            error: function(data){
              
                                      $.notify(data.message+"\nPlease try again.",{ className:"error", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );
              
            }
          });

        }


        

        

      });

  

  


			
		});
	</script>
@stop
