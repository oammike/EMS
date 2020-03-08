@extends('layouts.main')

@section('metatags')
  <title>Rewards | Open Access BPO</title>
  <style type="text/css">
    
  .ui-autocomplete-loading {
    background: white url("images/ui-anim_basic_16x16.gif") right center no-repeat;
  }

  </style>


  <link href="../public/css/bootstrap-grid.min.css" rel="stylesheet" type="text/css">
  <link href="../public/css/bootstrap-reboot.min.css" rel="stylesheet" type="text/css">
  <link href="../public/css/bootstrap.css" rel="stylesheet" type="text/css">


  <script src="../public/js/jquery-3.3.1.min.js"></script>
  <link rel="stylesheet" href="../public/css/jquery.fancybox.min.css" />
  <script src="../public/js/jquery.fancybox.min.js"></script>


<style type="text/css">
  /* First make sure the video thumbnail images are responsive. */

  img {
    max-width: 100%;
    height: auto;
  }
  
  /* 
  This is the starting grid for each video with thumbnails 4 across for the largest screen size.
  It's important to use percentages or there may be gaps on the right side of the page. 
  */

  .video {
    background: #fff;
    padding-bottom: 20px;
    box-shadow: 0 1px 1px rgba(0, 0, 0, 0.15);
    width: 23%; /* Thumbnails 4 across */
    margin: 1%;
    float: left;
  }

   /* 
   These keep the height of each video thumbnail consistent between YouTube and Vimeo.
   Each can have thumbnail sizes that vary by 1px and are likely break your layout. 
   */

  .video figure {
    height: 0;
    padding-bottom: 56.25%;
    overflow: hidden;

    .video figure a {
      display: block;
      margin: 0;
      padding: 0;
      border: none;
      line-height: 0;
    }
  }

  /* Media Queries - This is the responsive grid. */

  @media (max-width: 1024px) {
    .video {
      width: 31.333%; /* Thumbnails 3 across */
    }
  }

  @media (max-width: 600px) {
    .video {
      width: 48%; /* Thumbnails 2 across */
    }
  }

  @media (max-width: 360px) {
    .video {
      display: block;
      width: 96%; /* Single column view. */
      margin: 2%; /* The smaller the screen, the smaller the percentage actually is. */
      float: none;
    }
  }

  /* These are my preferred rollover styles. */

  .video img {
    width: 100%;
    opacity: 1;
  }

  .video img:hover, .video img:active, .video img:focus {
    opacity: 0.75;
  }

</style> 
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
            <img src="../storage/uploads/rewards_poster.jpg" width="100%"/>
            <h1 class="text-center"><br/><br/>EARN <strong class="text-primary"> POINTS</strong>, GET COOL <strong class="text-orange">REWARDS</strong> </h1><br/><br/>
            <!-- <img src="../storage/uploads/rewards_poster.jpg" class="pull-left" width="400" /> -->
            <p>
              Open Access BPO wants to <strong>celebrate YOU!</strong><br/><br/>
              To show our appreciation for all the awesome work you do, we've launched <strong class="text-primary">OPEN ACCESS BPO REWARDS</strong>.</p><br/><br/>


             <article class="video">
                <figure>
                  <a data-fancybox="gallery" data-file="Idol_WINNER" href="../storage/uploads/reward_tenure.jpg">
                  <img class="videoThumb" src="../storage/uploads/reward_tenure.jpg"></a>
                </figure>
                <h5 class="videoTitle text-center">TENURE REWARD</h5>
              </article>

              <article class="video">
                <figure>
                  <a data-fancybox="gallery" data-file="Idol_WINNER" href="../storage/uploads/reward_bday.png">
                  <img class="videoThumb" src="../storage/uploads/reward_bday.png"></a>
                </figure>
                <h5 class="videoTitle text-center">BDAY REWARD</h5>
              </article>

              <article class="video">
                <figure>
                  <a data-fancybox="gallery" data-file="Idol_WINNER" href="../storage/uploads/reward_anniv.jpg">
                  <img class="videoThumb" src="../storage/uploads/reward_anniv.jpg"></a>
                </figure>
                <h5 class="videoTitle text-center">ANNIV REWARD</h5>
              </article>

              <article class="video">
                <figure>
                  <a data-fancybox="gallery" data-file="Idol_WINNER" href="../storage/uploads/reward_training.jpg">
                  <img class="videoThumb" src="../storage/uploads/reward_training.jpg"></a>
                </figure>
                <h5 class="videoTitle text-center">TRAINING REWARD</h5>
              </article>
              <div class="clearfix"></div><br/><br/>

              
              <div class="row" style="background: url('../storage/uploads/Coffee_making_grp.jpg')top left no-repeat;background-size: cover;margin-bottom: 30px">
                <div class="col-lg-12">
                  <table class="table text-orange" >
                    <thead>
                      <tr>
                        <th><h1 style="color: #f0bd65">PERFORMANCE AND PARTICIPATION REWARDS</h1></th>
                        <th>POINTS</th>
                      </tr>
                    </thead>

                    <tbody>
                      <tr>
                        <td>
                          <h3 style="text-shadow: 2px 2px #333">Perfect Monthly Attendance</h3>
                          <p style=" color:#fff;font-size: larger;text-shadow: 2px 2px #333">Automatic 50 points by month-end for <strong>zero lates and absences</strong></p>
                        </td>
                        <td style="color:#dbeeff; font-size: xx-large; text-shadow: 2px 2px #333">50</td>
                      </tr>
                      
                      <tr>
                        <td>
                          <h3 style="text-shadow: 2px 2px #333">Outstanding Monthly Performance</h3>
                          <p style=" color:#fff;font-size: larger;text-shadow: 2px 2px #333">Program and Department perforrmance achievers will be recognized and awarded points by your program heads each month. Stay tuned for best-in-performance, outstanding quality and productivity, and other metric-exceeding criteria your leaders will be announcing.</p>
                        </td>
                        <td style="color:#dbeeff; font-size: large; text-shadow: 2px 2px #333">* varies * </td>
                      </tr>

                      <tr>
                        <td>
                          <h3 style="text-shadow: 2px 2px #333">Best in Quarterly Retention</h3>
                          <p style=" color:#fff;font-size: larger;text-shadow: 2px 2px #333">Teams or clusters who sustain the most members (i.e. lose zero or lose the least employees) get points too! Rewarded points depend on headcount. Check with your Group Team Leaders for details.</p>
                        </td>
                        <td style="color:#dbeeff; font-size: large; text-shadow: 2px 2px #333">by GTL cluster</td>
                      </tr>

                      <tr>
                        <td>
                          <h3 style="text-shadow: 2px 2px #333">Annual Top Performers</h3>
                          <p style=" color:#fff;font-size: larger;text-shadow: 2px 2px #333">2020 Perfect Scorer's Reward: all employees who score a perfect 5.0 Overall Rating in their 2020 Annual Performance Appraisal earn 150 points!</p>
                        </td>
                        <td style="color:#dbeeff; font-size: xx-large; text-shadow: 2px 2px #333">50</td>
                      </tr>

                      <tr>
                        <td>
                          <h3 style="text-shadow: 2px 2px #333">Employee Referral Participation</h3>
                          <p style=" color:#fff;font-size: larger;text-shadow: 2px 2px #333">You get awarded 50 points when the candidate you refer comes and completes their interviews! The more you refer, the more you earn!</p>
                        </td>
                        <td style="color:#dbeeff; font-size: xx-large; text-shadow: 2px 2px #333">50</td>
                      </tr>

                      <tr>
                        <td>
                          <h3 style="text-shadow: 2px 2px #333">Employee Engagement Participation</h3>
                          <p style=" color:#fff;font-size: larger;text-shadow: 2px 2px #333">Participate in Employee Engagement activities and follow event instructions, and you earn points! Specific criteria will be set pet event, so be sure to follow. See you! </p>
                        </td>
                        <td style="color:#dbeeff; font-size:large; text-shadow: 2px 2px #333">* varies *</td>
                      </tr>
                      
                      
                    </tbody>
                    
                    
                  </table>
                  
                </div>
              </div>

              

            
         

            <div class="clearfix"></div>


            <div style="background:url('../storage/uploads/Coffee_making_rewards.jpg')top left no-repeat #000; color:#fff; width: 100%; padding:50px 50px 200px 50px; margin-bottom: 30px;font-size: larger;text-shadow: 2px 2px #333"> 
              <p style="width: 50%; left:45%;position: relative;background: rgba(0, 0, 0, 0.4);padding: 20px"><br/><strong>REDEEM FUN REWARDS</strong><br/><br/>
                Use your points to redeem rewards online or with your Open Access BPO Rewards card. <br/><br/>Just visit the Open Access BPO Rewards Central to find out what hot items are up for grabs. Learn more about our Rewards Program Mechanics and merch catalog!
<br/><br/>

              </p>
              <div class="clearfix"></div>
            </div>

            
            <div class="clearfix"></div>

            <div style="background: url('../storage/uploads/REWARDS_COFFEE.jpg')top left no-repeat; width:60%; padding:40px 40px 10px 40px; color:#fff;font-size: larger;text-shadow: 2px 2px #333;max-width: 900px" class="row pull-left">
                <p><strong>PERK UP WITH FREE DRINKS</strong><br/>
                    For starters, you can use your points to redeem any of our top-of-the-line brews freshly prepared for you by our very own barista: <br/><br/>

                <ul>
                  <li>Café Americano</li>
                  <li>Café Mocha</li>
                  <li>Cappuccino</li>
                  <li>Flat White</li>
                  <li>Café Latte [Hazelnut / Vanilla / Caramel]</li>
                  <li>Hot Choco</li>
                </ul>
                <br/>
                These special handcrafted beverages are available for pick up at the G2 Cafeteria from Mondays to Fridays, 9:00 AM to 5:30 PM. We’re working on adding a later barista shift soon for our evening folks.<br/><br/>
                Each employee is entitled to order up to two (2) beverages per day.<br/>
                Limited cups are available for daily redemption, so order as early as you can!<br/><br/>
                </strong>
</p>
            </div>

            <img src="../storage/uploads/Coffee_making_grp.jpg" class="pull-right" width="37%" />
            <img src="../storage/uploads/Coffee_making_stock.jpg" class="pull-right" width="37%" style="margin-top: 30px" />

            <div class="clearfix"></div>
            <h3>Get a <strong class="text-orange">five-point discount</strong> when you <strong>bring your own non-spill tumbler!</h3>

            <div class="clearfix"></div>

            <h2 style="margin-top: 30px">ORDERING AND CLAIMING</h2>
            <p>There are two ways to place your orders:<br/>
                1. On-site (G2 Cafeteria) by presenting your reward card to our barista or <br/>
                2. Online at OPEN ACCESS REWARDS EMS Catalog</p>
 
            <h4>ONLINE ORDERING</h4>
            <p>Visit the <a href="{{action('RewardsHomeController@rewards_catalog')}}">Open Access BPO Rewards Central</a> to check out our  beverage menu. Select your drink by clicking the Claim button and indicating pickup time at our G2 cafeteria. <br/><br/>    
            Online orders will automatically be deducted from your accumulated points. You will have 15 minutes to claim your drink from your set pickup time. You may lose your beverage and points when you miss your claiming window.</p>
             
            <h4>FOR OPEN ACCESS JAKA TEAMS</h4>
            <p><a href="{{action('RewardsHomeController@rewards_catalog')}}">Order online</a> in advance to save time and have your beverage ready for pick up when you visit our G2 cafeteria. You may also redeem your beverage of choice by presenting your card when you swing by G2. For Jaka employees – your cards will be available soon! In the meantime, you can place your orders online and still enjoy your chosen beverage.
</p>
             
            <h4>DESIGNATED REWARDS CARDS</h4>
            <p>The cards you will receive are your permanently assigned cards that contain your name and corresponding unique QR
            Code. These are nontransferable.</p>
            
            <h4><br/>ACCOUNTABILITY FORMS</h4>
            <p>Claim yours starting on <strong class="text-orange">Monday Feb.10, 2020</strong> at HR where you will be asked to sign and confirm receipt on accountability forms.</p>

            <h4><br/>LOST OR DAMAGED CARDS</h4>
            <p>Please take care of your reward card.  Replacement for lost or damaged cards cost <strong>P150.00 each</strong>.</p>

            <h4><br/>TRANSFER POINTS</h4>
            <p>Sharing is caring! You may share your earned points to your team mates and friends online via Rewards Central!</p>

            <p>We&#39;re working on expanding our rewards catalogue, so stay tuned for more cool stuff to pop up at our
            Open Access BPO Rewards page! For now, enjoy your hot caffeine fix while we put together more treats for your hard work.</p>

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


 

	<script>
		window.selected_reward_id = 0;
		$(function() {

     
      

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
