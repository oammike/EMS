@extends('layouts.main')

@section('metatags')
  <title>Rewards | Open Access BPO</title>
  <style type="text/css">
    
  .ui-autocomplete-loading {
    background: white url("images/ui-anim_basic_16x16.gif") right center no-repeat;
  }

  </style>

  <link href="./public/css/easy-autocomplete.min.css" rel="stylesheet" type="text/css">
  <link href="./public/css/bootstrap-grid.min.css" rel="stylesheet" type="text/css">
  <link href="./public/css/bootstrap-reboot.min.css" rel="stylesheet" type="text/css">
  <link href="./public/css/bootstrap.css" rel="stylesheet" type="text/css">
@stop


@section('content')


  <section class="content-header">
    <h1><i class="fa fa-gift"></i> Open Access BPO Rewards <small id="points_counter">Remaining Points: {{ $remaining_points }}</small></h1>

    <ol class="breadcrumb">
      <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Rewards</li>
    </ol>
  </section>

  <section class="content" id='holder'>
    <div class="row">
      <div class="col-xs-12">
        <div class="box"  style="background: rgba(256, 256, 256, 0.4); min-height: 1200px;padding:50px">
          <div class="box-heading"></div>
          <div class="box-body">
            <h1 class="text-center">EARN POINTS, GET COOL REWARDS</h1>
            <img src="../storage/uploads/rewards.png" class="pull-left" width="400" />
            <p>
              Open Access BPO wants to <strong>celebrate YOU!</strong><br/><br/>
              To show our appreciation for all the awesome work you do, we've launched OPEN ACCESS BPO REWARDS.</p><br/><br/>

            

            <p style="width: 60%" class="pull-right"><strong>EARN POINTS</strong><br/><br/>

            - Through awesome performance and stellar team / individual metrics  
            (attendance, retention, quality, productivity, etc. <em>Program heads to announce details soon!</em>)<br/>
            - By participating and registering in company events<br/>
            - Through point transfers from teammates and friends<br/><br/>

            Your points will be stored in and are redeemable through your very own OA Rewards card. 
            Visit your EMS account to check and transfer your accumulated points and to redeem rewards.<br/><br/><br/></p>

            <div class="clearfix"></div>


            <div style="background:url('../storage/uploads/Coffee_making_rewards.jpg')top left no-repeat #000; color:#fff; width: 100%; padding:50px 50px 200px 50px; margin-bottom: 30px;font-size: larger;text-shadow: 2px 2px #333"> 
              <p style="width: 50%; left:45%;position: relative;background: rgba(0, 0, 0, 0.4);padding: 20px"><br/><strong>REDEEM FUN REWARDS</strong><br/><br/>
                Our online catalog showcases the goodies you can redeem through your earned points.  Visit our EMS portal to find out what hot items are up for grabs. Learn more about our Rewards Program Mechanics and soon-to-grow merch catalog!<br/><br/>

                The more points you earn, the more exciting rewards you can redeem!<br/><br/>
              </p>
              <div class="clearfix"></div>
            </div>

            
            <div class="clearfix"></div>

            <div style="background: url('../storage/uploads/REWARDS_COFFEE.jpg')top left no-repeat; width:60%; padding:40px 40px 60px 40px; color:#fff;font-size: larger;text-shadow: 2px 2px #333" class="pull-left">
                <p><strong>PERK UP WITH FREE COFFEE</strong><br/>
                    For starters, you can use your points to redeem any of our top-of-the-line brews freshly prepared for you by our very own barista: <br/><br/>

                <ul>
                  <li>Café Americano</li>
                  <li>Café Mocha</li>
                  <li>Cappuccino</li>
                  <li>Flat White</li>
                  <li>Café Latte [Hazelnut / Vanilla / Caramel]</li>
                </ul>
                <br/><br/>
                These special handcrafted beverages are available for pick up Mondays to Fridays at the G2 Cafeteria, from 9:00 am to 5:30 pm (a later barista shift will be added and announced soon for our evening folks).</p>
            </div>

            <img src="../storage/uploads/Coffee_making_grp.jpg" class="pull-right" width="37%" />
            <img src="../storage/uploads/Coffee_making_stock.jpg" class="pull-right" width="37%" style="margin-top: 30px" />

            <div class="clearfix"></div>

            <h2>ORDERING AND CLAIMING</h2>
            <p>There are two ways to place your orders:<br/>
                1. On-site (G2 Cafeteria) by presenting your reward card to our barista or <br/>
                2. Online at OPEN ACCESS REWARDS EMS Catalog</p>
 
            <h4>ONLINE ORDERING</h4>
            <p>Our coffee menu is now available and may be viewed via the <a href="{{action('RewardsHomeController@rewards_catalog')}}">Open Access BPO Rewards Catalog </a>page.  Select your
            coffee by clicking the Claim button and indicate pickup time (Glorietta site cafeteria).Online orders have a 15-minute
            waiting period from the set pickup time and automatically credit the points.</p>
             
            <h4>FOR OPEN ACCESS JAKA TEAMS</h4>
            <p>For your convenience, you may redeem your coffee by presenting your card when you swing by G2 or ordering online
            ahead of time and selecting your preferred pickup time there.</p>
             
            <h4>DESIGNATED REWARDS CARDS</h4>
            <p>The cards you will receive are your permanently assigned cards that contain your name and corresponding unique QR
            Code. These are nontransferable.</p>
            
            <h4><br/>ACCOUNTABILITY FORMS</h4>
            <p>Claim yours at HR where you will be asked to sign and confirm receipt on accountability forms.</p>

            <h4><br/>LOST OR DAMAGED CARDS</h4>
            <p>Please take care of your reward card.  Replacement for lost or damaged cards cost <strong>P150.00 each</strong>.</p>

            <h4><br/>TRANSFER PONTS</h4>
            <p>Sharing is caring! You may share your earned points to your team mates and friends online via Rewards Central!</p>

            <p>We&#39;re working on expanding our rewards catalogue, so stay tuned for more cool stuff to pop up at our
            [Open Access BPO Rewards Central]! For now, enjoy your hot caffeine fix while we put together more treats for your hard work.</p>

            <br/><br/>
            <h1 class="text-primary">We want to hear from you!</h1>
            <label>What other items do you wish to be included in the catalog for redemption?</label>
            <textarea class="form-control" id="feedback" rows="7"></textarea>
            <a id="send" class="btn btn-lg btn-success pull-right" style="margin-top: 20px">Send Feedback</a>



            

           
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

      

      $('#send').on('click',function(){

        var _token = "{{ csrf_token() }}";
        var feedback = $('#feedback').val();

        if (feedback == ""){
           $.notify("Let us know what items you'd want to be included into our Rewards Catalog!",{ className:"error", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );
          return false;

        }
        else{

          $.ajax({
            type:"POST",
            url : "{{ url('/rewardsFeedback') }}",
            data : {
                      'feedback': feedback,
                      '_token' : _token

            },
            success : function(data){
                                      console.log(data);

                                      if (data.success == '1')
                                      {
                                        

                                        $.notify("Thank you for sending your suggestions.",{className:"success", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );

                                        $('#send').fadeOut();

                                      }
                                      

                                      
            },
            error: function(data){
              
                                      $.notify("An error occured. Please try again.",{ className:"error", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );
              
            }
          });

        }


        

        

      });

  

  


			
		});
	</script>
@stop
