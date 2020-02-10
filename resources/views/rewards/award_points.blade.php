@extends('layouts.main')

@section('metatags')
  <title>Award Reward Points</title>
  <style type="text/css">
    
  .ui-autocomplete-loading {
    background: white url("images/ui-anim_basic_16x16.gif") right center no-repeat;
  }
  span.email-ids {
    float: left;
    /* padding: 4px; */
    border: 1px solid #ccc;
    margin-right: 5px;
    margin-bottom: 5px;
    background: #fffd90;
    padding: 5px 0px 3px 5px;*/
    border-radius: 5px;
    font-size: x-small;
    font-weight: bold;
}
span.cancel-email {
    border: 1px solid #ccc;
    width: 18px;
    display: block;
    float: right;
    text-align: center;
    margin-left: 20px;
    border-radius: 49%;
    height: 18px;
    line-height: 15px;
    margin-top: 1px;    cursor: pointer;
}
.email-id-row {
    border: 1px solid #ccc;
}
.email-id-row input {
    border: 0; outline:0;
}
span.to-input {
    display: block;
    float: left;
    padding-right: 11px;
}
.email-id-row {
    padding-top: 6px;
    padding-bottom: 3px;
    /*margin-top: 23px;*/
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
          <h4 class="modal-title" id="myModalLabel"><img src="public/img/oam_favicon1-55027f4ev1_site_icon-256x256.png" width="30" /> Send Reward Points</h4>
        
      </div>
      <div class="modal-body">
        
        <p id="msg"></p><ul id="receiver"></ul> 
        <p class="text-right" style="margin-top: 120px">Please type-in your EMS password to proceed.</p>
        <label class="pull-right">EMS Password: <input type="password" name="pw" id="pw" class="form-control" autocomplete="off" /></label>
        <div class="clearfix"></div>
      </div>
      <div class="modal-footer no-border">

          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <button id="proceed" class="btn btn-success" data-dismiss="modal"><i class="fa fa-exchange"></i> Send Reward Points Now </button>
        
        
        
      </div>
    </div>
  </div>
</div>

  <section class="content-header">
    <h1><i class="fa fa-gift"></i> Open Access BPO Rewards</h1>

    <ol class="breadcrumb">
      <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Rewards</li>
    </ol>
  </section>

  <section class="content" id='holder'>
    <div class="row">
      <div class="col-xs-12">
        <div class="box"  style="background: rgba(256, 256, 256, 0.4);background-size:cover;   min-height: 2200px">
          <div class="box-heading"></div>
          <div class="box-body">
            <div class="row">
              <div class="col-lg-12">
                

               
                <div class="row">

                  <div class="col-lg-1"></div>
                  <div class="col-lg-6" style="padding:20px"><h2 class="text-left"><i class="fa fa-trophy"></i> Award Points <br><br></h2>

                    <label class="text-primary"><input type="radio" class="awardees" name="awardees" id="specific" value="SPECIFIC"> Search for specific employee(s) by name or program<br/><small><em>You may add in multiple employees</em></small></label>

                    
                   
                        
                        <input type="text" style="width: 95%;display:none;margin-top:10px" name="transferto" required="required" id="transferto" class="form-control pull-left" placeholder="search for FIRSTNAME, LASTNAME, NICKNAME, or PROGRAM name" /><i style="display: none;" id="transfertoi" class="fa fa-angle-double-right fa-3x pull-right"></i>
                        <input id="transfer_id" disabled="disabled" type="hidden" />
                        <input id="transfer_name" disabled="disabled" type="hidden" />
                        <input id="transfer_prog" disabled="disabled" type="hidden" />
                        <input id="recipients" type="hidden" name="recipients" />
                        <input type="hidden" id="essai" placeholder="Email" />
                        <h3 id="maxpoints" style="display: none;padding-top: 50px"></h3>
                      

                        <label id="reasonl" style="display: none;margin-top: 20px">Reason: </label>
                        <select class="form-control" id="reason" style="display: none;">
                          <option value="0">* select a reason *</option>
                          @foreach($creditor as $w)
                          @if( (strpos($w->name,'Birth') !== 0) && !(strpos($w->name,'Anniversary')) )
                          <option value="{{$w->waysto_id}}" data-points="{{$w->allowed_points}}">{{$w->name}} </option>
                          @endif
                          @endforeach
                        </select><br/>
                        
                     

                    @if($canAwardBday)
                    <label class="text-primary" style="margin-top: 0px"><input type="radio" class="awardees" name="awardees" id="bday" value="BDAY"> Search for Birthday Celebrators</label>
                    <div id="bdays" style="margin: 20px 35px; display: none;">
                      <p>Award <strong style="font-size: large;" class="text-danger" id="bdaymaxpt"> {{ $bdayPoints }} </strong> points to all employees celebrating their birthdays </p>
                      <label class="pull-left">From: </label> 
                        <select id="from_mos" class="form-control pull-left" style="width: 30%">
                          <option value="0">* select month *</option>
                          @foreach($months as $m=>$k)
                            <option value="{{$m+1}}">{{$k}} </option>
                          @endforeach
                        </select>
                        <select id="from_day"  class="form-control pull-left" style="width: 30%">
                          <option value="0">* select day *</option>
                          @for($i=1; $i<=31; $i++)
                          <option value="{{$i}}">{{$i}}</option>
                          @endfor
                        </select>
                      <div class="clearfix" style="margin:10px"></div>
                      <label class="pull-left">To:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </label>
                      <select id="to_mos" disabled="disabled" class="form-control pull-left" style="width: 30%">
                          <option value="0">* select month *</option>
                          @foreach($months as $m=>$k)
                            <option value="{{$m+1}}">{{$k}} </option>
                          @endforeach
                        </select>
                        <select id="to_day" class="form-control pull-left" style="width: 30%">
                          <option value="0">* select day *</option>
                          @for($i=1; $i<=31; $i++)
                          <option value="{{$i}}">{{$i}}</option>
                          @endfor
                        </select>
                        <div class="clearfix"></div>
                        
                        <!-- ******** collapsible box ********** -->
                        <div class="box box-default collapsed-box" style="margin-top: 20px">
                          <div class="box-header with-border">
                            <h3 class="box-title text-primary"><strong class="text-orange" id="ct"></strong> Birthday Celebrator(s):
                             </h3>
                            <div class="box-tools pull-right">
                              <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                              </button>
                            </div>
                            <!-- /.box-tools -->
                          </div>
                          <!-- /.box-header -->
                          <div class="box-body">
                            <table id="celebs" class="table table-bordered">
                              <thead>
                                <tr>
                                  <th>Celebrator</th>
                                  <th>Program</th>
                                  <th>Birthday</th>
                                </tr>
                              </thead>
                              <tbody>
                                
                              </tbody>
                              
                            </table>
                            <div class="clearfix"></div>
                          


                            
                          </div>
                          <!-- /.box-body -->
                        </div>
                       <!-- ******** end collapsible box ********** -->
                        
                    </div>
                    
                    @else
                    <label title="Sorry, you don't have enough access to do this." style="margin-top: 0px"><input disabled="disabled" type="radio" class="awardees" name="awardees" id="bday" value="BDAY"> Search for Birthday Celebrators</label>
                    

                    @endif

                    @if($canAwardAnniv)
                    <div class="clearfix"></div>
                   <!--  <label class="text-primary" style="margin-top: 20px"><input type="radio" class="awardees" name="awardees" id="anniv" value="ANNIV"> Search by Work Anniversary</label>
 -->
                    @else
                    <div class="clearfix"></div>
                    <label title="Sorry, you don't have enough access to do this." style="margin-top: 20px"><input disabled="disabled" type="radio" class="awardees" name="awardees" id="anniv" value="ANNIV"> Search by Work Anniversary</label>

                    @endif



                    


                    


                    <div class="clearfix"></div>
                    
                    <label><br/>Notes / Comments: </label>
                    <textarea id="notes" class="form-control"></textarea>

                    <a id="sendpts" class="btn btn-lg btn-primary pull-right" style="margin-top: 10px"><i class="fa fa-exchange"></i> Send Reward Points </a>
                    <a id="makenew" class="btn btn-lg btn-primary pull-right" style="display: none;margin-top: 10px" href="{{action('UserController@rewards_award')}}"><i class="fa fa-exchange"></i> Make New Transfer</a>

                    <div id="pad" style="display: none;" class="btn-group-vertical ml-3 mt-3" role="group">
                              <div class="row">
                                <label>Number of Points to Award: <input required="required" class="text-center form-control mb-2" id="code" autocomplete="off"> </label>
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
                              
                        </div>
    

                   
                  </div>
                  <div class="col-lg-5">
                    <div id="awardees" style="padding-top: 150px"></div>
                    <img src="storage/uploads/rewards.png" width="70%" style="margin-top: 0px" class="pull-right" />
                    
                    
                  </div>
                  
                  
                </div>
                <div class="row">

                  <div class="col-lg-1"></div>
                  <div class="col-lg-6" style="padding:20px;>

                    <div class="row">
                      <div class="col-lg-6">
                        
                      </div>
                      <div class="col-lg-6">
                       
                      </div>
                    </div>
                  </div>

                    
                   

                   
                   
                     

                    



                  

                   
                  
                    


                     
                  </div>
                 

                 
                  
                </div>

                
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

  <script src="{{URL::asset('public/js/moment.min.js')}}" ></script>
  <script src="public/js/jquery.easy-autocomplete.min.js" type="text/javascript"></script>

  <script>
    window.selected_reward_id = 0;
    $(function() {
      $('#specified').hide();

      var all_celebrator = [];

      var options = {
        url: "{{action('UserController@listAllActive')}}",
        requestDelay: 200,

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
                onChooseEvent: function(){
                                var id = $("#transfer_id").val();
                                var n = $("#transfer_name").val();
                                var c = $("#transfer_prog").val();

                               
                                
                                var allexist = $('#awardees span.email-ids'); //.attr('data-userid');
                                var existings = [];
                                $.each(allexist,function(k,v){
                                    var o = $(v);
                                    existings.push(o.attr('data-userid'));
                                   
                                    
                                });

                                if (!existings.includes(id)){
                                  $('#awardees').append('<span class="email-ids" data-userid="'+id+'" data-emp="'+n+' of '+c+' ">' + n+' -- '+c + '<span class="cancel-email">x</span></span>');

                                }else alert(n + " is already on the list.");
                                

                            },
                showAnimation: {
                                type: "fade", //normal|slide|fade
                                time: 400,
                                
                                  
                                  
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
            
          }
        }

      };

      $('#transferto').easyAutocomplete(options); 


      $('.awardees').on('click',function(){
        var v = $(this).val();


        switch(v){
          case 'SPECIFIC': {
                              $('#transferto,#transfertoi,#reason,#reasonl,#awardees').fadeIn();
                              $('#reason').val('0');
                              $('#bdays').fadeOut();

                           }break;
          case 'BDAY': {
                          $('#transferto,#transfertoi,#reason,#reasonl,#maxpoints,#awardees,#pad').fadeOut();
                          $('#bdays,#sendpts').fadeIn();
                          $('#bdays select').on('change',function(){
                            var m_from = $('#from_mos').find(':selected').val();
                            var d_f = $('#from_day').find(':selected').val();
                            var m_t = $('#to_mos').val(m_from);
                            var d_t = $('#to_day').find(':selected').val();

                            
                            var d_from = (d_f==0) ? '1':d_f;
                            var m_to = (m_t==0) ? m_from : m_t;
                            var d_to = (d_t==0) ? '1' : d_t;

                            

                            console.log("m_from="+m_from+"&d_from="+d_from+"&m_to="+m_to+"&d_to="+d_to);
                            // dt.ajax.url("{{ url('/birthdayCelebrators') }}?m_from="+m_from+"&d_from="+d_from+"&m_to="+m_to+"&d_to="+d_to).load();
                            // dt.ajax.reload();
                            var tbl = $('#holder tbody');
                             $.ajax({
                                      type:"GET",
                                      url : "{{ url('/birthdayCelebrators') }}?m_from="+m_from+"&d_from="+d_from+"&m_to="+m_to+"&d_to="+d_to,
                                      
                                      success : function(response){
                                                                //console.log(data);
                                                                tbl.empty();
                                                                $('#ct').html("");
                                                                all_celebrator=[];
                                                                $('#ct').html(response['data'].length);
                                                                $.each(response['data'], function(k,v){
                                                                  //console.log(v);
                                                                  all_celebrator.push(v.id);

                                                                  tbl.append('<tr> <td>'+(k+1)+') '+v.lastname+', '+v.firstname+'</td><td>'+v.program+'</td><td>'+ moment(v.birthday, 'MM-DD-YYYY').format("MMM D")+'</td></tr>');
                                                                  

                                                                });
                                                                console.log(all_celebrator);


                                                              

                                                                
                                      },
                                      error: function(data){
                                        
                                                                $.notify("An error occured. Please try again later.",{ className:"error", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );
                                        
                                      }
                                    });

                          });

                       }break;
          case 'ANNIV': {

                          $('#transferto,#transfertoi,#reason,#reasonl').fadeOut();

                        }break;
        }


      });

      $('#reason').on('change', function(){
        var r = $(this).find(':selected').val();
        var p = $(this).find(':selected').attr('data-points');

        if (p) {
          $('#maxpoints').fadeIn().html('Points to Award: <strong class="text-danger">'+p+'</strong>');
          $('#ptsto').html(p+' points ');
          $('#pad').fadeOut();
          $('#sendpts').fadeIn();
        }
          
        else {
          $('#pad').fadeIn();
          $('#ptsto').html(p+ ' points ');
          $('#maxpoints').fadeOut();
          $('#sendpts').fadeOut();
        }
        console.log(r);

      });

      $('#go').on('click',function(){
        console.log("go");
        var amt = $('#code').val();
        var receiver = $('#transfer_name').val();
        var rid = $('#transfer_id').val();
        var p = $('#transfer_prog').val();
        var a = $('#allTransfers').attr('data-val');
        var ap =  parseInt(a) + parseInt(amt);

        $('#amt').html(amt);$('#campaign').html(p);

        var allexist = $('#awardees span.email-ids'); //.attr('data-userid');
        var allReceiver =  getAllRecipients(allexist);
                               

        if(allReceiver.length == 0)
          $.notify("Please specify the receiver(s) of reward points.",{ className:"error", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );

        else if(amt == "")
          $.notify("Please enter number of points you want to transfer.",{ className:"error", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );
        

        else if(isNaN(amt) || !(amt.indexOf(".") == -1)) {
          $.notify("Sorry, you\'ve entered an Invalid Amount.\nPlease try again.",{ className:"error", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );

        }

        else {

           
           $('#code').val($('#maxpoints strong').text());
           $('#receiver').html("");
           $.each(allexist,function(k,v){
                var o = $(v); $('#receiver').append('<li>'+o.attr('data-emp')+'</li>');
                
            });
          $('#msg').html('You are about to award <strong id="amt" class="text-danger" style="font-size: large;"></strong> reward point(s) to the following employee(s):<br/>');
          $('#amt').html(amt);
          $('#mytransfer').modal('show');
        }

      });

      $('#proceed').on('click',function(){

        
        
        var allexist = $('#awardees span.email-ids'); //.attr('data-userid');
        

       
        var a = $('#allTransfers').attr('data-val');
        var pw = $('#pw').val();
        var awardtype = $('.awardees:checked').val();
       

        var _token = "{{ csrf_token() }}";

        


           switch(awardtype)
           {

            case 'SPECIFIC': { 

                                var points = $('#code').val();
                                if(isNaN(points)) {

                                  $.notify("Sorry, you\'ve entered an Invalid Value.\nPlease try again.",{ className:"error", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );
                                  return false;

                                }else 
                                {
                                  var recipients =  getAllRecipients(allexist);
                                  $.ajax({
                                          type:"POST",
                                          url : "{{ url('/grantRewardPoints') }}",
                                          data : {
                                                    'points' : points,
                                                    'awardtype': awardtype,
                                                    'recipients': recipients,
                                                    'waysto': $('#reason').find(':selected').val(),
                                                    'notes': $('#notes').val(),
                                                    'pw' : pw,
                                                    '_token' : _token

                                          },
                                          success : function(data){
                                                                    console.log(data);

                                                                    if (data.success == '1')
                                                                    {
                                                                      
                                                                      $.notify(points+ " points successfully transferred to "+data.total+" employee(s)",{className:"success", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );
                                                                      $('#go').attr('disabled',true);

                                                                      $('#sendpts').fadeOut(); $('#makenew').fadeIn();

                                                                    }else {

                                                                      $.notify("Unable to award points. Please try again later.",{className:"error", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );

                                                                    }
                                                                    

                                                                    
                                          },
                                          error: function(data){
                                            
                                                                    $.notify("An error occured. Please try again later.",{ className:"error", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );
                                            
                                          }
                                        });

                                }
                                
                              }break;

            case 'BDAY': {
                            var points = $('#amt').attr('data-amt');
                            console.log(points);
                            if(isNaN(points)) {

                              $.notify("Sorry, you\'ve entered an Invalid Value.\nPlease try again.",{ className:"error", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );
                              return false;

                            }else 
                            {
                              var recipients =  all_celebrator;
                              $.ajax({
                                          type:"POST",
                                          url : "{{ url('/grantRewardPoints') }}",
                                          data : {
                                                    'points' : points,
                                                    'awardtype': awardtype,
                                                    'recipients': recipients,
                                                    
                                                    'notes': $('#notes').val(),
                                                    'pw' : pw,
                                                    '_token' : _token

                                          },
                                          success : function(data){
                                                                    console.log(data);

                                                                    if (data.success == '1')
                                                                    {
                                                                      if(data.already)
                                                                        $.notify(points+ " birthday reward points successfully transferred to "+data.total+" employee(s).\nThe other ("+data.already.length+") employees already had existing Birthday Reward points.",{className:"success", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );
                                                                      else
                                                                        $.notify(points+ " birthday reward points successfully transferred to "+data.total+" employee(s)",{className:"success", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );

                                                                      $('#go').attr('disabled',true);

                                                                      $('#sendpts').fadeOut(); $('#makenew').fadeIn();

                                                                    }else {

                                                                      $.notify("Unable to send birthday reward points. Please try again later.",{className:"error", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );

                                                                    }
                                                                    

                                                                    
                                          },
                                          error: function(data){
                                            
                                                                    $.notify("An error occured. Please try again later.",{ className:"error", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );
                                            
                                          }
                                        });
                              
                              $('#sendpts').fadeOut(); $('#makenew').fadeIn();
                            
                            }

                            


                        }break;
           }

  

        


        

        

      });

      $('#sendpts').on('click',function(){

        // *** check mo muna anong type of award
        var awardType = $('.awardees:checked').val();
        switch(awardType){
          case 'BDAY': { 
                          var m_f = $('#from_mos').find(':selected').val();
                          var d_f = $('#from_day').find(':selected').val();
                          var m_t = $('#to_mos').find(':selected').val();
                          var d_t = $('#to_day').find(':selected').val();

                          console.log('array:');
                          console.log(all_celebrator);
                          if(m_f==0 || d_f==0) {
                            $.notify("Please indicate awardee's date of birth.",{ className:"error", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );return false;
                          }else if(all_celebrator.length == 0){
                            $.notify("No employees based on that query. Please try again.",{ className:"error", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );return false;

                          }else {

                                $('#code').val($('#bdaymaxpt').text());
                                $('#receiver').html("");
                                $('#msg').html('You are about to award <strong id="amt" data-amt="'+$('#bdaymaxpt').text()+'" class="text-danger" style="font-size: large;"></strong> reward point(s) to <strong class="text-success">'+all_celebrator.length+' birthday celebrator(s)</strong><br/>');
                                $('#amt').html($('#bdaymaxpt').text()); $('#mytransfer').modal('show');

                          }

                       }break;
          case 'SPECIFIC': { 

                              var allexist = $('#awardees span.email-ids'); //.attr('data-userid');
                              var allReceiver =  getAllRecipients(allexist);
                              var reason = $('#reason').find(':selected').val();

                              if(allReceiver.length == 0) {
                                $.notify("Please specify the receiver(s) of reward points.",{ className:"error", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );
                                return false;
                              }
                              else if(reason==0) 
                              {
                                $.notify("Please indicate the reason for awarding reward points.",{ className:"error", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );
                                return false;

                              }
                              else {

                                $('#code').val($('#maxpoints strong').text());
                                $('#receiver').html("");
                                $.each(allexist,function(k,v){
                                      var o = $(v); $('#receiver').append('<li>'+o.attr('data-emp')+'</li>');
                                      
                                  });
                                 $('#msg').html("");
                                 $('#msg').html('You are about to award <strong id="amt" class="text-danger" style="font-size: large;"></strong> reward point(s) to the following employee(s):<br/>');
                                 $('#amt').html($('#maxpoints strong').text()); $('#mytransfer').modal('show');
                              }

                           }break;
        }

        


      });

      




      let data = [
                
            ]
            $("#essai").email_multiple({
                data: data
                // reset: true
            });



      function getAllRecipients(allexist){
        
        var allReceiver = [];

        $.each(allexist,function(k,v){
            var o = $(v);
            allReceiver.push(o.attr('data-userid'));
            
        });
        return allReceiver;
      }

  

  


      
    });



/**
 * Created by Malal91 and Haziel
 * Select multiple email by jquery.email_multiple
 * **/

(function($){

    $.fn.email_multiple = function(options) {

        let defaults = {
            reset: false,
            fill: false,
            data: null
        };

        let settings = $.extend(defaults, options);
        let email = "";

        return this.each(function()
        {
            $(this).after("");
            let $orig = $(this);
            let $element = $('.enter-mail-id');
            $element.keydown(function (e) {
                $element.css('border', '');
                if (e.keyCode === 13 || e.keyCode === 32) {
                    let getValue = $element.val();
                    if (/^[a-z0-9._-]+@[a-z0-9._-]+\.[a-z]{2,6}$/.test(getValue)){
                        $('#awardees').append('<span class="email-ids">' + getValue + '<span class="cancel-email">x</span></span>');
                        $element.val('');

                        email += getValue + ';'
                    } else {
                        $element.css('border', '1px solid red')
                    }
                }

                $orig.val(email.slice(0, -1))
            });

            $(document).on('click','.cancel-email',function(){
                $(this).parent().remove();
            });

            if(settings.data){
                $.each(settings.data, function (x, y) {
                    if (/^[a-z0-9._-]+@[a-z0-9._-]+\.[a-z]{2,6}$/.test(y)){
                        $('.all-mail').append('<span class="email-ids">' + y + '<span class="cancel-email">x</span></span>');
                        $element.val('');

                        email += y + ';'
                    } else {
                        $element.css('border', '1px solid red')
                    }
                })

                $orig.val(email.slice(0, -1))
            }

            if(settings.reset){
                $('.email-ids').remove()
            }

            return $orig.hide()
        });
    };

})(jQuery);

  </script>
@stop
