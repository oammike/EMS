                              
<?php /*


                             <div class="item active text-center">
                              <img src="storage/uploads/bg_awardees.png" />
                             </div>

                             @if(count($tenYears) >= 1)

                             @foreach($tenYears as $n)
                              <div class="item text-center">
                                <div style="background:url('storage/uploads/bg_mario.jpg') top center no-repeat; background-size: 100%" >
                                  <!-- Add the bg color to the header using any of the bg-* classes -->
                                  <h4  style="color: #fbf970"  ><br/>Happy 10th Year<span style="color:#fff"> @ Open Access!</span></h4>
                                  
                                  
                                  <div class="widget-user-image">
                                     

                                    @if ( file_exists('public/img/employees/'.$n->id.'.jpg') )
                                    <img class="img-circle" src="{{ asset('public/img/employees/'.$n->id.'.jpg')}}" width="80" alt="User Avatar">
                                    @else
                                    <img class="img-circle" src="{{asset('public/img/useravatar.png')}}" width="80" alt="User Avatar">
                                    @endif

                                  </div>
                                  
                                  <div>
                                      @if (empty($n->nickname) || $n->nickname==" ")
                                         <h3 class="widget-user-username" style="line-height: 0.2em"><a style="color: #fff" href="{{action('UserController@show',$n->id)}}"><small  style="color: #fff"  >{{$n->firstname}} {{$n->lastname}} </small></a><br/></h3>
                                     @else
                                         <h3 class="widget-user-username text-white" style="line-height: 0.2em"><a href="{{action('UserController@show',$n->id)}}"><small style="color: #fff"  >{{$n->nickname}} {{$n->lastname}} </small></a><br/></h3>
                                     @endif
                                     <h5 style="margin-top: -7px"><small style="color:#9cff36; font-weight: bolder"> {{$n->name}} </small><br/>

                                    @if ($n->filename == null) 
                                     <span class="text-white"> {{ OAMPI_Eval\Campaign::find($n->campaign_id)->name}} </span> </h5><br/><br/><br/><br/><br/>
                                    @else
                                   <img src="{{ asset('public/img/'.$n->filename) }}" height="30" style="margin-top: 40px" /> </h5>
                                    
                                    @endif
                                    
                                  </div><br/><br/>
                                </div>
                              </div>

                              @endforeach

                             @endif


                             @if(count($fiveYears) >= 1)

                             @foreach($fiveYears as $n)
                              <div class="item text-center">
                                <div style="background:url('storage/uploads/bg_mario.jpg') top center no-repeat; background-size: 100%" >
                                  <!-- Add the bg color to the header using any of the bg-* classes -->
                                  <h4  style="color: #fbf970"  ><br/>Happy 5th Year<span style="color:#fff"> @ Open Access!</span></h4>
                                  
                                  
                                  <div class="widget-user-image">
                                     

                                    @if ( file_exists('public/img/employees/'.$n->id.'.jpg') )
                                    <img class="img-circle" src="{{ asset('public/img/employees/'.$n->id.'.jpg')}}" width="80" alt="User Avatar">
                                    @else
                                    <img class="img-circle" src="{{asset('public/img/useravatar.png')}}" width="80" alt="User Avatar">
                                    @endif

                                  </div>
                                  
                                  <div>
                                      @if (empty($n->nickname) || $n->nickname==" ")
                                         <h3 class="widget-user-username" style="line-height: 0.2em"><a style="color: #fff" href="{{action('UserController@show',$n->id)}}"><small  style="color: #fff"  >{{$n->firstname}} {{$n->lastname}} </small></a><br/></h3>
                                     @else
                                         <h3 class="widget-user-username text-white" style="line-height: 0.2em"><a href="{{action('UserController@show',$n->id)}}"><small style="color: #fff"  >{{$n->nickname}} {{$n->lastname}} </small></a><br/></h3>
                                     @endif
                                     <h5 style="margin-top: -7px"><small style="color:#9cff36; font-weight: bolder"> {{$n->name}} </small><br/>

                                    @if ($n->filename == null) 
                                     <span class="text-white"> {{ OAMPI_Eval\Campaign::find($n->campaign_id)->name}} </span> </h5><br/><br/><br/><br/><br/>
                                    @else
                                   <img src="{{ asset('public/img/'.$n->filename) }}" height="30" style="margin-top: 40px" /> </h5>
                                    
                                    @endif
                                    
                                  </div><br/><br/>
                                </div>
                              </div>

                              @endforeach

                             @endif


  <?php */ ?>              
                          <div class="item active text-center">

                              <h3 class="text-danger"><strong><i class="fa fa-money"></i> <span style="font-size:smaller">Referral Bonus: </span><i class="fa fa-money"></i> <br/>Php 10,000.00 </strong> <br/>
                                <br/><span class="text-orange" style="font-size:0.8em">For those referrals who can start training this <strong>Monday, January 28</strong></span></br/><br/>
                                <span class="text-primary">Customer Happiness Expert <br/>(Email & Chat support)</span> <br/>
                                <img src="public/img/logo_circleslife.png" width="40%" /><br/>
                              </h3><BR/><BR/>
                              
                             <h5><br/>Qualifications:</h5>

                              <ul class="text-left" style="margin-left: 30px">
                                <li>Prior experience in customer service across different lines of businesses (BPO, E-Commerce and Retail, Telecom) and have assisted customers either via phone, email, chat, and social media with their billing, technical, and some other sudden spur-of-the-moment unexpected questions</li>
                                <li>Flawless communication skills be it verbal, and on written communication</li>
                                <li>Basic, and we mean really basic MS Office skills, although advance is BIG plus</li>
                                <li>Experience falling of the cracks and getting hands dirty (whereby instances) gained in a start-up environment</li>
                                <li>Can start training this Monday, January 28</li>
                              </ol>

                              
                              
                             


                              <p><br/><br/>Please have your referrals come this FRIDAY and SATURDAY<br/>
                              Recruitment is open from 8 AM - 7 PM</p>

                              <p>Thank you.</p>
                              



                            </div>

                            <div class="item text-center" >

                              <img src="./public/img/ana.jpg" style="z-index: 2" />

                              <div class="text-gray text-right" style="position: relative;width:90%; right:0px;top:-200px; z-index: 999; height: 20px">
                                 <h2><img src="./public/img/white_logo_small.png" width="40" style="margin-right: 30px" /><br/> <strong>Internal<br/>Hiring! </strong></h2>

                              </div>

                               <h2 style="margin-top: -30px" class="text-danger"><strong>Officer in Charge (OIC) </strong><br/>
                               <small>for</small> <a target="_blank" href="{{action('CampaignController@show','26')}} "> <img src="./public/img/logo_wv.png" width="180"></a></h2>

                              <h5 class="text-danger"><i class="fa fa-exclamation-triangle"></i> Deadline of Submission: <strong class="text-primary"> January 31,2019 </strong></h5>
                              <p class="text-left" style="padding-left: 30px; font-weight: bolder;">Requirements: </p>
                              <ul class="text-left">
                                <li>open <strong>ONLY to all WorldVentures reps</strong></li>
                                <li>Excellent written and verbal English skills</li>
                                <li>At least 2 yr. in Open Access</li>
                                <li>No written warning within the last six months</li>
                                <li>Exemplary Performance</li>
                              </ul>

                              <p><small>Interested applicants may submit their letter of intent and updated resume to: <a href="mailto:recruitment@openaccessmarketing.com">recruitment@openaccessmarketing.com</a></small></p>


                            </div>  


                          <div class="item text-center">

                              <h3 class="text-danger"><strong><i class="fa fa-exclamation-triangle"></i> Attention: <i class="fa fa-exclamation-triangle"></i> <br/>NO CEDULA, NO TAX REFUND </strong> <br/>
                                <img src="storage/uploads/cedula.png" width="80%" />
                              </h3><BR/><BR/>
                              
                              <h5>Please be informed that you need to submit a <br/><strong>2018 or 2019 CEDULA (Community Tax Certificate)</strong><br/> to Finance Department at the 7th floor HRC Center Bldg. <br/>on or before <strong>JANUARY 21, 2019</strong>, for the following purposes.</h5>

                              <ol class="text-left" style="margin-left: 50px">
                                <li>BIR 2316 for the year 2018</li>
                                <li>Tax Refund (if entitled)</li>
                              </ol>

                              <h5><br/>For 2018 Hires, you need to submit the following:</h5>
                              
                              <ol class="text-left" style="margin-left: 50px">
                                <li>BIR 2316 from previous employer</li>
                                <li>2018/2019 Cedula</li>
                              </ol>

                              <p><br/><br/>For further inquiries, send an e-mail to 
                                <br/>Ronel Ambrocio (rambrocio@openaccessbpo.net; 
                                <br/>rambrocio@openaccessmarketing.com) & 
                                <br/>Salary Inquiry (salaryinquiry@openaccessbpo.net; salaryinquiry@openaccessmarketing.com)</p>

                              <p>Thank you.</p>
                              



                            </div>             




                             @if(count($firstYears) >= 1)
                            <!-- ******** FIRST YEAR ANNIV ******* -->
                            <div class="item  text-center">
                              <div class="box box-widget widget-user">
                                <!-- Add the bg color to the header using any of the bg-* classes -->
                                <br/><br/>
                                <h4 class="text-primary"> <i class="fa fa-smile-o fa-2x"></i> <br/>Happy  <span style="color:#f59c0f">1st Year Anniversary</span> <br/><span style="color:#9c9fa0">to the following employees:</span>
                                  <br/><br/><span style="font-size:smaller">Cheers!</span></h4>
                                
                                <div class="widget-user-image">
                                   

                                 

                                </div>
                                <div class="box-footer">
                                </div>
                              </div>
                            </div>
                            @foreach($firstYears as $n)
                            <div class="item text-center">
                              <div class="box box-widget widget-user">
                                <!-- Add the bg color to the header using any of the bg-* classes -->
                                <h4 class="text-default">Happy 1st Year<span class="text-primary"> @ Open Access!</span></h4>
                                <?php $cover = URL::to('/') . "/storage/uploads/cover-".$n->id."_".$n->hascoverphoto.".png"; ?>

                                @if (is_null($n->hascoverphoto) )  
                                 <div class="widget-user-header bg-black" style="background: url('{{ asset('public/img/makati.jpg')}}') center center;">
                                
                                @else
                                <div class="widget-user-header bg-black" style="background: url('{{$cover}}') center center;">
                               @endif
                                  
                                  
                                </div>
                                <div class="widget-user-image">
                                   

                                  @if ( file_exists('public/img/employees/'.$n->id.'.jpg') )
                                  <img class="img-circle" src="{{ asset('public/img/employees/'.$n->id.'.jpg')}}" width="80" alt="User Avatar">
                                  @else
                                  <img class="img-circle" src="{{asset('public/img/useravatar.png')}}" width="80" alt="User Avatar">
                                  @endif

                                </div>
                                
                                <div class="box-footer">
                                  @if (empty($n->nickname) || $n->nickname==" ")
                                     <h3 class="widget-user-username"><a href="{{action('UserController@show',$n->id)}}"><small>{{$n->firstname}} {{$n->lastname}} </small></a></h3><small><em>Date hired: {{date('M d, Y', strtotime($n->dateHired))}} </em></small>
                                 @else
                                     <h3 class="widget-user-username"><a href="{{action('UserController@show',$n->id)}}"><small>{{$n->nickname}} {{$n->lastname}} </small></a></h3><small><em>Date hired: {{date('M d, Y', strtotime($n->dateHired))}} </em></small>
                                 @endif

                                 <h5 class="widget-user-desc"><small> {{$n->name}} </small><br/>

                                  @if ($n->filename == null) 
                                   <span class="text-primary"> {{ OAMPI_Eval\Campaign::find($n->campaign_id)->name}} </span> </h5>
                                  @else
                                 <img src="{{ asset('public/img/'.$n->filename) }}" height="30" /> </h5>
                                  
                                  @endif
                                  <br/>
                                </div>
                              </div>
                            </div>

                            @endforeach

                            @endif



                           
                            


                            @if (count($newHires) >= 1)
                            <!-- **** NEW HIRES ******************** -->
                            <div class="item text-center">
                              <div class="box box-widget widget-user">
                                <!-- Add the bg color to the header using any of the bg-* classes -->
                                <br/><br/>
                                <h4 class="text-primary">A warm <span style="color:#f59c0f"> welcome</span> <br/><span style="color:#9c9fa0">to the newest members</span>
                                  <br/><span style="font-size:smaller">of our growing family...</span></h4>
                                
                                <div class="widget-user-image">
                                   

                                 

                                </div>
                                <div class="box-footer">
                                </div>
                              </div>
                            </div>

                            @foreach($newHires as $n)
                            <div class="item text-center">
                              <div class="box box-widget widget-user">
                                <!-- Add the bg color to the header using any of the bg-* classes -->
                                <h4 class="text-default">Welcome to<span class="text-primary"> Open Access!</span></h4>
                                <?php $cover = URL::to('/') . "/storage/uploads/cover-".$n->id."_".$n->hascoverphoto.".png"; ?>

                                @if (is_null($n->hascoverphoto) )  
                                 <div class="widget-user-header bg-black" style="background: url('{{ asset('public/img/makati.jpg')}}') center center;">
                                
                                @else
                                <div class="widget-user-header bg-black" style="background: url('{{$cover}}') center center;">
                               @endif
                                  
                                  
                                </div>
                                <div class="widget-user-image">
                                   

                                  @if ( file_exists('public/img/employees/'.$n->id.'.jpg') )
                                  <img class="img-circle" src="{{ asset('public/img/employees/'.$n->id.'.jpg')}}" width="80" alt="User Avatar">
                                  @else
                                  <img class="img-circle" src="{{asset('public/img/useravatar.png')}}" width="80" alt="User Avatar">
                                  @endif

                                </div>
                                
                                <div class="box-footer">
                                  @if (empty($n->nickname))
                                     <h3 class="widget-user-username"><a href="{{action('UserController@show',$n->id)}}"><small>{{$n->firstname}} {{$n->lastname}} </small></a></h3>
                                 @else
                                     <h3 class="widget-user-username"><a href="{{action('UserController@show',$n->id)}}"><small>{{$n->nickname}} {{$n->lastname}} </small></a></h3>
                                 @endif

                                 <h5 class="widget-user-desc"><small> {{$n->name}} </small><br/>

                                  @if ($n->filename == null) 
                                   <span class="text-primary"> {{ OAMPI_Eval\Campaign::find($n->campaign_id)->name}} </span> </h5>
                                  @else
                                 <img src="{{ asset('public/img/'.$n->filename) }}" height="30" /> </h5>
                                  
                                  @endif
                                  <br/>
                                </div>
                              </div>
                            </div>

                            @endforeach

                            @endif


                             <!-- ************* ANNOUNCEMENTS ************-->
                            
                           <!--  <div class="item text-center">
                              <img src="storage/uploads/yearend1.jpg" />
                            </div>

                            <div class="item text-center">
                              <img src="storage/uploads/yearend2.jpg" />
                            </div>

                            <div class="item text-center">
                              <img src="storage/uploads/yearend3.jpg" />
                            </div>

                            <div class="item text-center">
                              <img src="storage/uploads/yearend4.jpg" />
                            </div>

                            <div class="item text-center">
                              <img src="storage/uploads/yearend5.jpg" />
                            </div> -->


                           

                            

                          <div class="item text-center">

                              <img src="storage/uploads/runner-eunice.png" />
                              <div style="padding:10px; position: absolute;bottom: -20px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAonSports #TCSNYCMarathon</small></a></div> 
                              <p style="padding:50px">For <a href="user/150" target="_blank">Euniz</a>, what started as a remedy for her broken heart became one of her biggest passions. Her second consecutive spot for Ryan's Run at the<strong>TCS New York City Marathon</strong> will be a new test to break her personal while running for a good cause. When not out competing or training, Euniz helps ensure high quality customer experiences as part of Open Access BPO's <a href="campaign/19" target="_blank">QA and Performance </a>team.</p>
                             
                           </div> 


                           <div class="item text-center">

                              <img src="storage/uploads/runner-clint.jpg" />
                              <div style="padding:10px; position: absolute;bottom: -20px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAonSports #TCSNYCMarathon</small></a></div> 
                              <p style="padding:50px">Once a running friend, Clint worked his way out of a running intolerance and even managed to secure a spot in 2016's <strong>TCS New York City Marathon.</strong> He has since learned that the best way to defeat distance is through endurance and not speed. This year, he returns to New York to once again compete in the prestigious marathon. In the office, Clint is a Senior Program Manager for one fo our biggest campaigns.</p>
                             
                           </div> 



                            <div class="item text-center">

                              <img src="storage/uploads/nyc-jeff.jpg" />
                              <div style="padding:10px; position: absolute;bottom: -20px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAonSports #TCSNYCMarathon</small></a></div> 
                              <p style="padding:50px">When he's not beefing up his marathon routine,
<a href="user/62" target="_blank">Jeff</a> weaves through digital marketing waters as our SEO strategist.
This November, he will take on a different challenge as he blazes
through New York's five boroughs as a first-time Open Access BPO representative to the 
<strong>TCS New York City Marathon.</strong></p>
                             
                           </div> 

                            <?php /* <div class="item text-center">
                              <div class="box box-widget widget-user">
                                <!-- Add the bg color to the header using any of the bg-* classes -->
                               
                                <h4 class="text-primary"><br/><br/>Come in any<span style="color:#f59c0f"> 90s inspired</span> <br/><span style="color:#9c9fa0">fashion...</span><br/>
                                 </h4><img src="storage/uploads/back90-1.png" width="100%" /><br/><br/><br/>
                                
                                <div class="box-footer"><h5>RSVP Link: <a href="http://172.17.0.2/coffeebreak/2018/10/5458/" target="_blank" >http://172.17.0.2/coffeebreak/2018/10/5458/</a></h5><br/>
                                </div>
                              </div>
                            </div>

                            <div class="item text-center">
                              <div class="box box-widget widget-user">
                                <!-- Add the bg color to the header using any of the bg-* classes -->
                               
                                <h4 class="text-primary"><br/>get ready to <span style="color:#f59c0f"> sing along</span> <br/><span style="color:#9c9fa0">to some 90s tunes...</span><br/>
                                 </h4><img src="storage/uploads/back90-5.JPG" width="100%" /><br/>
                                
                                <div class="box-footer"><h5>RSVP Link: <a href="http://172.17.0.2/coffeebreak/2018/10/5458/" target="_blank" >http://172.17.0.2/coffeebreak/2018/10/5458/</a></h5>
                                </div>
                              </div>
                            </div>

                            <div class="item text-center">
                              <div class="box box-widget widget-user">
                                <!-- Add the bg color to the header using any of the bg-* classes -->
                               
                                <h4 class="text-primary"><br/><br/>and brush up on those <span style="color:#f59c0f">90s moves</span> <br/><span style="color:#9c9fa0"> as we party all night long!</span><br/>
                                 </h4><img src="storage/uploads/back90-3.jpg" width="100%" /><br/>
                                
                                <div class="box-footer"><h5>RSVP Link: <a href="http://172.17.0.2/coffeebreak/2018/10/5458/" target="_blank" >http://172.17.0.2/coffeebreak/2018/10/5458/</a></h5>
                                </div>
                              </div>
                            </div>


                            <div class="item  text-center">
                              <div class="box box-widget widget-user">
                                <!-- Add the bg color to the header using any of the bg-* classes -->
                               
                                <h4 class="text-primary">RSVP not later than<span style="color:#f59c0f"> Nov.09 2018</span> <br/><span style="color:#9c9fa0"></span>
                                 </h4><img src="storage/uploads/back90-4.png" width="100%" /><br/>
                                <h5>RSVP Link: <a href="http://172.17.0.2/coffeebreak/2018/10/5458/" target="_blank" >http://172.17.0.2/coffeebreak/2018/10/5458/</a></h5><br/>
                                <div class="box-footer">
                                </div>
                              </div>
                            </div>



                            //********************** SPOOKY JAR ******************
                             <div class="item text-center">

                              <img src="storage/uploads/stanlee.jpg" />
                              <div style="padding:10px; position: absolute;bottom: -20px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OACelebratesHeroes #RIPStanLee #OAonKindness #WorldKindnessDay</small></a></div> 
                              <p style="padding:50px">@TheRealStanLee will always be remembered as the man behind a universe of the greatest superheroes and one of the world's best storytellers. <br/><br/>May his works continue to inspire people across the globe towards greater kindness. <br/><br/> </p>
                             
                           </div> 


                            <div class="item text-center">

                              <img src="storage/uploads/spooky-jar-winner.jpg" />
                              <h4>Answer: <strong class="text-orange">6,972</strong> jelly beans</h4>
                              <h2 class="text-danger">Congratulations to: </h2>
                              <p><a href="user/525" target="_blank"><img src="public/img/employees/525.jpg" class="img-circle" width="50">&nbsp;&nbsp;Adamson Oca - Circles.Life</a> <strong>[6,960]</strong></p>
                              <p><a href="user/522" target="_blank"><img src="public/img/employees/522.jpg" class="img-circle" width="50">&nbsp;&nbsp;Lesle Novion - Zenefits</a> <strong>[6,920] &nbsp;&nbsp;&nbsp;&nbsp;</strong></p>
                              <p><a href="user/534" target="_blank"><img src="public/img/employees/534.jpg" class="img-circle" width="50">&nbsp;&nbsp;Luis Oliveros - Marketing</a> <strong>[6,882]&nbsp;&nbsp;</strong></p>
                              <p><a href="user/1722" target="_blank"><img src="public/img/employees/1722.jpg" class="img-circle" width="50">&nbsp;&nbsp;Kathleen Manago - SheerID</a> <strong>[7,077]</strong></p>
                              <p><a href="user/1745" target="_blank"><img src="public/img/employees/1745.jpg" class="img-circle" width="50">&nbsp;&nbsp;Armando Cabuga - Zenefits</a> <strong>[6,800]</strong></p>

                              <p style="padding:30px">You may claim your prizes from the <strong>Marketing Department</strong> located at the 5F from Nov.5-9, 2018.

You may also start getting candies from the jar at the 5F Marketing area! Small cups are provided beside it.<br/><br/>Thank you for participating in our activities for Spooky Halloween 2018!</p>
                             
                           </div> 
                            <div class="item text-center">
                              <img src="storage/uploads/spooky-team-winner.jpg" />
                              
                              <p style="padding:50px"><em>Note: Judges for the SPOOKY TEAM are: <br/>
                              Ben Davidowitz (CEO), Henry Chang (President), Joy Sebastian (VP for Operations)</em><br/><br/>
                            <strong>Criteria:</strong><br/>
                            Spookiness - 30%<br/>
                            Creativity - 25%<br/>
                            Relevance to the Theme  - 25%<br/>
                            Execution - 20%</p>
                            <h2 class="text-danger">Congratulations to...<br/><br/></h2>
                             
                           </div>

                           <div class="item text-center"><h2 class="text-danger">3rd Place</h2>
                              
                             <img src="storage/uploads/thumb-spooky-entries4.jpg" />
                              <p style="padding:10px">
                                <h3 class="text-orange">Just because you're dead, doesn't mean you can't have fun.</h3>
                                <h4> Team Jam  <a href="campaign/27" target="_blank">Zenefits</a> </h4> </p>
                             
                           </div> 

                           <div class="item text-center"><h2 class="text-danger">2nd Place</h2>
                              <img src="storage/uploads/thumb-spooky-entries6.jpg" />
                              <p style="padding:10px"><h3 class="text-orange">A dose of your nightmare </h3>
                                <h4> Team Liezl <a href="campaign/48" target="_blank"> AnOther </a> </h4> </p>
                             
                           </div> 

                           <div class="item text-center"><h2 class="text-danger">1st Place</h2>
                              <img src="storage/uploads/thumb-spooky-entries12.jpg" />
                              <p style="padding:10px">

                                <h3 class="text-orange">The Doctor wants to see you now...</h3>
                                <h4> Team Carla <a href="campaign/33" target="_blank">Boostability</a> </h4> </p>
                             
                           </div> 

                           <div class="item  text-center">
                            <img src="storage/uploads/spooky-thanks.jpg" />  
                            <p><br/><br/>View All Entries in our <a href="{{action('HomeController@gallery')}}">Gallery Page <i class="fa fa-picture-o"></i></a></p>
                              
                             
                           </div> 



                           <!-- ********************************* CS WEEK contest ******************************** -->
                            <div class="item text-center">
                              <img src="storage/uploads/dressup1.jpg" />
                              <h4 class="text-primary">Team Prim <br/> <span class="text-orange" style="font-size: smaller">Lebua</span></h4>
                              <p>Thank you all for participating in this year's <a href="https://www.instagram.com/explore/tags/csweek/" target="_blank">#CSWeek </a></p>
                              <p>More in our <a href="{{action('HomeController@gallery')}}">Gallery Page <i class="fa fa-picture-o"></i></a></p>
                              
                             
                           </div> 
                            <div class="item text-center">
                              <img src="storage/uploads/dressupWinner1.jpg" />
                              <h4 class="text-primary">Team Prim <br/> <span class="text-orange" style="font-size: smaller">Lebua</span></h4>
                              <p>Thank you all for participating in this year's <a href="https://www.instagram.com/explore/tags/csweek/" target="_blank">#CSWeek </a></p>
                              <p>More in our <a href="{{action('HomeController@gallery')}}">Gallery Page <i class="fa fa-picture-o"></i></a></p>
                              
                             
                           </div> 
                           <div class="item text-center">
                              <img src="storage/uploads/dressupWinner2.jpg" />
                              <h4 class="text-primary">Team Prim <br/> <span class="text-orange" style="font-size: smaller">Lebua</span></h4>
                              <p>Thank you all for participating in this year's <a href="https://www.instagram.com/explore/tags/csweek/" target="_blank">#CSWeek </a></p>
                              <p>More in our <a href="{{action('HomeController@gallery')}}">Gallery Page <i class="fa fa-picture-o"></i></a></p>
                              
                             
                           </div> 
                           <div class="item text-center">
                             <img src="storage/uploads/dressup2.jpg" />
                              <h4 class="text-primary">Team Catherine de Alzon <br/> <span class="text-orange" style="font-size: smaller">Zenefits</span></h4>
                              <p>Thank you all for participating in this year's <a href="https://www.instagram.com/explore/tags/csweek/" target="_blank">#CSWeek </a></p>
                              <p>More in our <a href="{{action('HomeController@gallery')}}">Gallery Page <i class="fa fa-picture-o"></i></a></p>
                              
                             
                           </div> 
                           <div class="item text-center">
                             <img src="storage/uploads/dressupWinner3.jpg" />
                              <h4 class="text-primary">Team Catherine de Alzon <br/> <span class="text-orange" style="font-size: smaller">Zenefits</span></h4>
                              <p>Thank you all for participating in this year's <a href="https://www.instagram.com/explore/tags/csweek/" target="_blank">#CSWeek </a></p>
                              <p>More in our <a href="{{action('HomeController@gallery')}}">Gallery Page <i class="fa fa-picture-o"></i></a></p>
                              
                             
                           </div> 
                           <div class="item text-center">
                             <img src="storage/uploads/dressupWinner4.jpg" />
                              <h4 class="text-primary">Team Catherine de Alzon <br/> <span class="text-orange" style="font-size: smaller">Zenefits</span></h4>
                              <p>Thank you all for participating in this year's <a href="https://www.instagram.com/explore/tags/csweek/" target="_blank">#CSWeek </a></p>
                              <p>More in our <a href="{{action('HomeController@gallery')}}">Gallery Page <i class="fa fa-picture-o"></i></a></p>
                              
                             
                           </div> 

                           <div class="item text-center">
                             <img src="storage/uploads/dressup3.jpg" />
                              <h4 class="text-primary">Team Geoff Catabay <br/> <span class="text-orange" style="font-size: smaller">Zenefits</span></h4>
                              <p>Thank you all for participating in this year's <a href="https://www.instagram.com/explore/tags/csweek/" target="_blank">#CSWeek </a></p>
                              <p>More in our <a href="{{action('HomeController@gallery')}}">Gallery Page <i class="fa fa-picture-o"></i></a></p>
                              
                             
                           </div> 
                           <div class="item text-center">
                             <img src="storage/uploads/dressupWinner5.jpg" />
                              <h4 class="text-primary">Team Geoff Catabay <br/> <span class="text-orange" style="font-size: smaller">Zenefits</span></h4>
                              <p>Thank you all for participating in this year's <a href="https://www.instagram.com/explore/tags/csweek/" target="_blank">#CSWeek </a></p>
                              <p>More in our <a href="{{action('HomeController@gallery')}}">Gallery Page <i class="fa fa-picture-o"></i></a></p>
                              
                             
                           </div> 
                           <div class="item text-center">
                             <img src="storage/uploads/dressupWinner6.jpg" />
                              <h4 class="text-primary">Team Geoff Catabay <br/> <span class="text-orange" style="font-size: smaller">Zenefits</span></h4>
                              <p>Thank you all for participating in this year's <a href="https://www.instagram.com/explore/tags/csweek/" target="_blank">#CSWeek </a></p>
                              <p>More in our <a href="{{action('HomeController@gallery')}}">Gallery Page <i class="fa fa-picture-o"></i></a></p>
                              
                             
                           </div> 



                              


                             <div class="item text-center">
                             <br/>
                              <img src="storage/uploads/2018-09-24_ig.jpg" /><br/></a>
                            </div>


                            */ ?>


                            

                           

                           




                             

                            

                             
                            

                            <!-- <div class="item text-center">

                              <p style="padding:20px">In line with the clinic's <strong>HIV/AIDS Awareness Program,</strong>we are happy to invite everyone to participate in our activity below on Nov 8, 2018.  <br/><br/>

<strong class="text-danger">WHAT:</strong> HIV 101 PROGRAM with onsite FREE HIV TESTING and one on one counseling ( this process will be treated with confidentiality)<br/>

<strong class="text-danger">WHEN: </strong>Nov 8, 2018 (7pm - 3am)<br/>

<strong class="text-danger">WHERE: </strong> 8TH FLOOR PANTRY SIDE<br/>

<strong class="text-danger">WHO:</strong> Makati Social Hygiene Clinic Team from Makati City Hall<br/><br/>

HIV/AIDS cases in the country are rapidly increasing its rate, from 2 cases/day back in 2009 and presently, 31 cases/day (HARP, 2018). The only way to stop this, is through keeping ourselves informed about how it is being transmitted, its prevention and get tested. <br/><br/>

This will be the third time to have an HIV/AIDS Prevention Program in the company.<br/>

For interested participants and for any questions, please email us at our gmail address: oamnurse@openaccessmarketing.com or nurse@openaccessbpo.net in zimbra.</p>



                            </div>  -->

                           
                          



                          <!-- 
                           <div class="item text-center">

                              <img src="storage/uploads/spooky-2.jpg" />
                              <h4 class="text-orange">Spooky Jar</h4>
                              <p style="padding:20px; text-align: left">* Guess how many candies there are in the <strong class="text-danger">SPOOKY JAR </strong>located at the <strong>5F Marketing area</strong><br/>
     * Employees can submit their guesses through <strong><a href="http://172.17.0.2/coffeebreak/2018/10/oam-spooky-halloween-activity-2018/" target="_blank">this link </a></strong><br/>
     * First 10 employees to guess the correct number will win. In the event that nobody gets it right, the 5 bets closest to the right answer will win<br/>
     * Deadline to submit your bets is until November 1, 11:59PM.<br/>
     * Winners will be announced on November 2, 2018.</p>
                             
                           </div> 
 <div class="item text-center">

                              <img src="storage/uploads/spooky-3.jpg" />
                              <h4 class="text-orange">Spooky Jar</h4>
                               <p style="padding:20px; text-align: left">* Guess how many candies there are in the <strong class="text-danger">SPOOKY JAR </strong>located at the <strong>5F Marketing area</strong><br/>
     * Employees can submit their guesses through <strong><a href="http://172.17.0.2/coffeebreak/2018/10/oam-spooky-halloween-activity-2018/" target="_blank">this link </a></strong><br/>
     * First 10 employees to guess the correct number will win. In the event that nobody gets it right, the 5 bets closest to the right answer will win<br/>
     * Deadline to submit your bets is until November 1, 11:59PM.<br/>
     * Winners will be announced on November 2, 2018.</p>
                             
                           </div> 

                            <div class="item text-center">

                              <img src="storage/uploads/spooky-4.jpg" />
                             
                              
                             
                           </div>  -->

                            

                            <!-- ********************************* MENTAL HEALTH ******************************** -->
                            <!--
                           <div class="item  text-center">
                              <h4 class="text-primary">WORLD MENTAL HEALTH <span class="text-orange" style="font-size: smaller">DAY</span></h4>
                              <img src="storage/uploads/mental1.jpg" />
                              <p style="padding:20px; font-size: smaller">Mental and behavioral disorders commonly begin before the age of 14. About 20% of the world’s youth are living with mental illnesses or problems.<br/><br/>It’s time to do away from discrimination and invalidation. We must shatter the stigma to make room for awareness, early detection, and treatment. Living with mental illness does not define a person.<br/><br/>Practicing self-care for people living with mental illness and those surrounding them is a must. <a href="https://www.instagram.com/explore/tags/WorldMentalHealthDay2018/" target="_blank">#WorldMentalHealthDay2018 </a></p>

                           </div> 

                           <div class="item text-center">
                              <h4 class="text-primary">WORLD MENTAL HEALTH <span class="text-orange" style="font-size: smaller">DAY</span></h4>
                              <img src="storage/uploads/mental2.jpg" />
                              <p style="padding:20px; font-size: smaller">Mental and behavioral disorders commonly begin before the age of 14. About 20% of the world’s youth are living with mental illnesses or problems.<br/><br/>It’s time to do away from discrimination and invalidation. We must shatter the stigma to make room for awareness, early detection, and treatment. Living with mental illness does not define a person.<br/><br/>Practicing self-care for people living with mental illness and those surrounding them is a must. <a href="https://www.instagram.com/explore/tags/WorldMentalHealthDay2018/" target="_blank">#WorldMentalHealthDay2018 </a></p>
                              
                            
                           </div> 

                            <div class="item text-center">
                              <h4 class="text-primary">WORLD MENTAL HEALTH <span class="text-orange" style="font-size: smaller">DAY</span></h4>
                              <img src="storage/uploads/mental3.jpg" />
                              <p style="padding:20px; font-size: smaller">Mental and behavioral disorders commonly begin before the age of 14. About 20% of the world’s youth are living with mental illnesses or problems.<br/><br/>It’s time to do away from discrimination and invalidation. We must shatter the stigma to make room for awareness, early detection, and treatment. Living with mental illness does not define a person.<br/><br/>Practicing self-care for people living with mental illness and those surrounding them is a must. <a href="https://www.instagram.com/explore/tags/WorldMentalHealthDay2018/" target="_blank">#WorldMentalHealthDay2018 </a></p>

                           </div> 

                            <div class="item text-center">
                              <h4 class="text-primary">WORLD MENTAL HEALTH <span class="text-orange" style="font-size: smaller">DAY</span></h4>
                              <img src="storage/uploads/mental4.jpg" />
                              <p style="padding:20px; font-size: smaller">Mental and behavioral disorders commonly begin before the age of 14. About 20% of the world’s youth are living with mental illnesses or problems.<br/><br/>It’s time to do away from discrimination and invalidation. We must shatter the stigma to make room for awareness, early detection, and treatment. Living with mental illness does not define a person.<br/><br/>Practicing self-care for people living with mental illness and those surrounding them is a must. <a href="https://www.instagram.com/explore/tags/WorldMentalHealthDay2018/" target="_blank">#WorldMentalHealthDay2018 </a></p>

                           </div> 

                           <div class="item text-center">
                              <h4 class="text-primary">WORLD MENTAL HEALTH <span class="text-orange" style="font-size: smaller">DAY</span></h4>
                              <img src="storage/uploads/mental5.jpg" />
                              <p style="padding:20px; font-size: smaller">Mental and behavioral disorders commonly begin before the age of 14. About 20% of the world’s youth are living with mental illnesses or problems.<br/><br/>It’s time to do away from discrimination and invalidation. We must shatter the stigma to make room for awareness, early detection, and treatment. Living with mental illness does not define a person.<br/><br/>Practicing self-care for people living with mental illness and those surrounding them is a must. <a href="https://www.instagram.com/explore/tags/WorldMentalHealthDay2018/" target="_blank">#WorldMentalHealthDay2018 </a></p>
                              
                           </div> 
                           <div class="item text-center">
                              <h4 class="text-primary">WORLD MENTAL HEALTH <span class="text-orange" style="font-size: smaller">DAY</span></h4>
                              <img src="storage/uploads/mental6.jpg" />
                              <p style="padding:20px; font-size: smaller">Mental and behavioral disorders commonly begin before the age of 14. About 20% of the world’s youth are living with mental illnesses or problems.<br/><br/>It’s time to do away from discrimination and invalidation. We must shatter the stigma to make room for awareness, early detection, and treatment. Living with mental illness does not define a person.<br/><br/>Practicing self-care for people living with mental illness and those surrounding them is a must. <a href="https://www.instagram.com/explore/tags/WorldMentalHealthDay2018/" target="_blank">#WorldMentalHealthDay2018 </a></p>
                              
                           </div> 
                           <div class="item text-center">
                              <h4 class="text-primary">WORLD MENTAL HEALTH <span class="text-orange" style="font-size: smaller">DAY</span></h4>
                              <img src="storage/uploads/mental7.jpg" />
                              <p style="padding:20px; font-size: smaller">Mental and behavioral disorders commonly begin before the age of 14. About 20% of the world’s youth are living with mental illnesses or problems.<br/><br/>It’s time to do away from discrimination and invalidation. We must shatter the stigma to make room for awareness, early detection, and treatment. Living with mental illness does not define a person.<br/><br/>Practicing self-care for people living with mental illness and those surrounding them is a must. <a href="https://www.instagram.com/explore/tags/WorldMentalHealthDay2018/" target="_blank">#WorldMentalHealthDay2018 </a></p>
                              
                           </div> 
                           <div class="item text-center">
                              <h4 class="text-primary">WORLD MENTAL HEALTH <span class="text-orange" style="font-size: smaller">DAY</span></h4>
                              <img src="storage/uploads/mental8.jpg" />
                              <p style="padding:20px; font-size: smaller">Mental and behavioral disorders commonly begin before the age of 14. About 20% of the world’s youth are living with mental illnesses or problems.<br/><br/>It’s time to do away from discrimination and invalidation. We must shatter the stigma to make room for awareness, early detection, and treatment. Living with mental illness does not define a person.<br/><br/>Practicing self-care for people living with mental illness and those surrounding them is a must. <a href="https://www.instagram.com/explore/tags/WorldMentalHealthDay2018/" target="_blank">#WorldMentalHealthDay2018 </a></p>
                              
                           </div> 
                           <div class="item text-center">
                              <h4 class="text-primary">WORLD MENTAL HEALTH <span class="text-orange" style="font-size: smaller">DAY</span></h4>
                              <img src="storage/uploads/mental9.jpg" />
                              <p style="padding:20px; font-size: smaller">Mental and behavioral disorders commonly begin before the age of 14. About 20% of the world’s youth are living with mental illnesses or problems.<br/><br/>It’s time to do away from discrimination and invalidation. We must shatter the stigma to make room for awareness, early detection, and treatment. Living with mental illness does not define a person.<br/><br/>Practicing self-care for people living with mental illness and those surrounding them is a must. <a href="https://www.instagram.com/explore/tags/WorldMentalHealthDay2018/" target="_blank">#WorldMentalHealthDay2018 </a></p>
                              
                           </div> 

                           <div class="item text-center">
                              <h4 class="text-primary">WORLD MENTAL HEALTH <span class="text-orange" style="font-size: smaller">DAY</span></h4>
                              <img src="storage/uploads/mental10.jpg" />
                              <p style="padding:20px; font-size: smaller">Mental and behavioral disorders commonly begin before the age of 14. About 20% of the world’s youth are living with mental illnesses or problems.<br/><br/>It’s time to do away from discrimination and invalidation. We must shatter the stigma to make room for awareness, early detection, and treatment. Living with mental illness does not define a person.<br/><br/>Practicing self-care for people living with mental illness and those surrounding them is a must. <a href="https://www.instagram.com/explore/tags/WorldMentalHealthDay2018/" target="_blank">#WorldMentalHealthDay2018 </a></p>
                              
                           </div> 
 


                             <div class="item text-center">
                              <h4 class="text-primary">Open Access CS Week <span class="text-orange" style="font-size: smaller">2018</span></h4>
                              <p>Thank you all for participating in this year's <a href="https://www.instagram.com/explore/tags/csweek/" target="_blank">#CSWeek </a></p>
                              <p>More in our <a href="{{action('HomeController@gallery')}}">Gallery Page <i class="fa fa-picture-o"></i></a></p>
                              <img src="storage/uploads/cs_2.jpg" />
                              <div style="padding:10px; position: absolute;bottom: 10px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAonCSWeek2018</small></a></div>
                           </div> 
                         -->

                            
                            

                           <!-- ********************************* CS WEEK FINALE ******************************** -->
                           <!--  <div class="item text-center">
                              <h4 class="text-primary">Open Access CS Week <span class="text-orange" style="font-size: smaller">2018</span></h4>
                              <p>Thank you all for participating in this year's <a href="https://www.instagram.com/explore/tags/csweek/" target="_blank">#CSWeek </a></p>
                              <p>More in our <a href="{{action('HomeController@gallery')}}">Gallery Page <i class="fa fa-picture-o"></i></a></p>
                              <img src="storage/uploads/cs_1.jpg" />
                              <div style="padding:10px; position: absolute;bottom: 10px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAonCSWeek2018</small></a></div>
                           </div> 
                           <div class="item text-center">
                              <h4 class="text-primary">Open Access CS Week <span class="text-orange" style="font-size: smaller">2018</span></h4>
                              <p>Thank you all for participating in this year's <a href="https://www.instagram.com/explore/tags/csweek/" target="_blank">#CSWeek </a></p>
                              <p>More in our <a href="{{action('HomeController@gallery')}}">Gallery Page <i class="fa fa-picture-o"></i></a></p>
                              <img src="storage/uploads/cs_3.jpg" />
                              <div style="padding:10px; position: absolute;bottom: 10px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAonCSWeek2018</small></a></div>
                           </div> 
                           <div class="item text-center">
                              <h4 class="text-primary">Open Access CS Week <span class="text-orange" style="font-size: smaller">2018</span></h4>
                              <p>Thank you all for participating in this year's <a href="https://www.instagram.com/explore/tags/csweek/" target="_blank">#CSWeek </a></p>
                              <p>More in our <a href="{{action('HomeController@gallery')}}">Gallery Page <i class="fa fa-picture-o"></i></a></p>
                              <img src="storage/uploads/cs_4.jpg" />
                              <div style="padding:10px; position: absolute;bottom: 10px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAonCSWeek2018</small></a></div>
                           </div> 
                           <div class="item text-center">
                              <h4 class="text-primary">Open Access CS Week <span class="text-orange" style="font-size: smaller">2018</span></h4>
                              <p>Thank you all for participating in this year's <a href="https://www.instagram.com/explore/tags/csweek/" target="_blank">#CSWeek </a></p>
                              <p>More in our <a href="{{action('HomeController@gallery')}}">Gallery Page <i class="fa fa-picture-o"></i></a></p>
                              <img src="storage/uploads/cs_5.jpg" />
                              <div style="padding:10px; position: absolute;bottom: 10px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAonCSWeek2018</small></a></div>
                           </div> 
                           <div class="item text-center">
                              <h4 class="text-primary">Open Access CS Week <span class="text-orange" style="font-size: smaller">2018</span></h4>
                              <p>Thank you all for participating in this year's <a href="https://www.instagram.com/explore/tags/csweek/" target="_blank">#CSWeek </a></p>
                              <p>More in our <a href="{{action('HomeController@gallery')}}">Gallery Page <i class="fa fa-picture-o"></i></a></p>
                              <img src="storage/uploads/cs_6.jpg" />
                              <div style="padding:10px; position: absolute;bottom: 10px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAonCSWeek2018</small></a></div>
                           </div> 
                           <div class="item text-center">
                              <h4 class="text-primary">Open Access CS Week <span class="text-orange" style="font-size: smaller">2018</span></h4>
                              <p>Thank you all for participating in this year's <a href="https://www.instagram.com/explore/tags/csweek/" target="_blank">#CSWeek </a></p>
                              <p>More in our <a href="{{action('HomeController@gallery')}}">Gallery Page <i class="fa fa-picture-o"></i></a></p>
                              <img src="storage/uploads/cs_7.jpg" />
                              <div style="padding:10px; position: absolute;bottom: 10px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAonCSWeek2018</small></a></div>
                           </div> 
                           <div class="item text-center">
                              <h4 class="text-primary">Open Access CS Week <span class="text-orange" style="font-size: smaller">2018</span></h4>
                              <p>Thank you all for participating in this year's <a href="https://www.instagram.com/explore/tags/csweek/" target="_blank">#CSWeek </a></p>
                              <p>More in our <a href="{{action('HomeController@gallery')}}">Gallery Page <i class="fa fa-picture-o"></i></a></p>
                              <img src="storage/uploads/cs_8.jpg" />
                              <div style="padding:10px; position: absolute;bottom: 10px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAonCSWeek2018</small></a></div>
                           </div> 
                           <div class="item text-center">
                              <h4 class="text-primary">Open Access CS Week <span class="text-orange" style="font-size: smaller">2018</span></h4>
                              <p>Thank you all for participating in this year's <a href="https://www.instagram.com/explore/tags/csweek/" target="_blank">#CSWeek </a></p>
                              <p>More in our <a href="{{action('HomeController@gallery')}}">Gallery Page <i class="fa fa-picture-o"></i></a></p>
                              <img src="storage/uploads/cs_9.jpg" />
                              <div style="padding:10px; position: absolute;bottom: 10px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAonCSWeek2018</small></a></div>
                           </div> 
                           <div class="item text-center">
                              <h4 class="text-primary">Open Access CS Week <span class="text-orange" style="font-size: smaller">2018</span></h4>
                              <p>Thank you all for participating in this year's <a href="https://www.instagram.com/explore/tags/csweek/" target="_blank">#CSWeek </a></p>
                              <p>More in our <a href="{{action('HomeController@gallery')}}">Gallery Page <i class="fa fa-picture-o"></i></a></p>
                              <img src="storage/uploads/cs_10.jpg" />
                              <div style="padding:10px; position: absolute;bottom: 10px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAonCSWeek2018</small></a></div>
                           </div> 
                           <div class="item text-center">
                              <h4 class="text-primary">Open Access CS Week <span class="text-orange" style="font-size: smaller">2018</span></h4>
                              <p>Thank you all for participating in this year's <a href="https://www.instagram.com/explore/tags/csweek/" target="_blank">#CSWeek </a></p>
                              <p>More in our <a href="{{action('HomeController@gallery')}}">Gallery Page <i class="fa fa-picture-o"></i></a></p>
                              <img src="storage/uploads/cs_11.jpg" />
                              <div style="padding:10px; position: absolute;bottom: 10px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAonCSWeek2018</small></a></div>
                           </div> 
                           <div class="item text-center">
                              <h4 class="text-primary">Open Access CS Week <span class="text-orange" style="font-size: smaller">2018</span></h4>
                              <p>Thank you all for participating in this year's <a href="https://www.instagram.com/explore/tags/csweek/" target="_blank">#CSWeek </a></p>
                              <p>More in our <a href="{{action('HomeController@gallery')}}">Gallery Page <i class="fa fa-picture-o"></i></a></p>
                              <img src="storage/uploads/cs_12.jpg" />
                              <div style="padding:10px; position: absolute;bottom: 10px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAonCSWeek2018</small></a></div>
                           </div> 
                           <div class="item text-center">
                              <h4 class="text-primary">Open Access CS Week <span class="text-orange" style="font-size: smaller">2018</span></h4>
                              <p>Thank you all for participating in this year's <a href="https://www.instagram.com/explore/tags/csweek/" target="_blank">#CSWeek </a></p>
                              <p>More in our <a href="{{action('HomeController@gallery')}}">Gallery Page <i class="fa fa-picture-o"></i></a></p>
                              <img src="storage/uploads/cs_13.jpg" />
                              <div style="padding:10px; position: absolute;bottom: 10px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAonCSWeek2018</small></a></div>
                           </div> 
                           <div class="item text-center">
                              <h4 class="text-primary">Open Access CS Week <span class="text-orange" style="font-size: smaller">2018</span></h4>
                              <p>Thank you all for participating in this year's <a href="https://www.instagram.com/explore/tags/csweek/" target="_blank">#CSWeek </a></p>
                              <p>More in our <a href="{{action('HomeController@gallery')}}">Gallery Page <i class="fa fa-picture-o"></i></a></p>
                              <img src="storage/uploads/cs_14.jpg" />
                              <div style="padding:10px; position: absolute;bottom: 10px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAonCSWeek2018</small></a></div>
                           </div>  -->
                            <!-- ********************************* CS WEEK FINALE ******************************** -->


                            <!-- ********************************* CS WEEK GRATITUDE ******************************** -->

                          <!--   <div class="item text-center">
                              <h4 class="text-primary">Open Access CS Week <span class="text-orange" style="font-size: smaller">2018</span></h4>
                              <p>"Gratitude can transform common days into Thanksgiving, turn routine jobs into joy and change opportunities into blessings." - William Arthur Ward <br/><br/>Teams, we hope you're enjoying<a href="https://www.instagram.com/explore/tags/csweek/" target="_blank">#CSWeek </a> so far. Don't forget to use <a href="https://www.instagram.com/explore/tags/csOAonCSWeek2018/" target="_blank"> #OAonCSWeek2018</a> or tag us so we can see your photos, too!</p>
                              <img src="storage/uploads/appreciate1.jpg" />
                              <div style="padding:10px; position: absolute;bottom: 10px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAonCSWeek2018</small></a></div>
                           </div> 

                            <div class="item text-center">
                              <h4 class="text-primary">Open Access CS Week <span class="text-orange" style="font-size: smaller">2018</span></h4>
                              <p>"Gratitude can transform common days into Thanksgiving, turn routine jobs into joy and change opportunities into blessings." - William Arthur Ward <br/><br/>Teams, we hope you're enjoying<a href="https://www.instagram.com/explore/tags/csweek/" target="_blank">#CSWeek </a> so far. Don't forget to use <a href="https://www.instagram.com/explore/tags/csOAonCSWeek2018/" target="_blank"> #OAonCSWeek2018</a> or tag us so we can see your photos, too!</p>
                              <img src="storage/uploads/appreciate2.jpg" />
                              <div style="padding:10px; position: absolute;bottom: 10px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAonCSWeek2018</small></a></div>
                           </div> 

                            <div class="item text-center">
                              <h4 class="text-primary">Open Access CS Week <span class="text-orange" style="font-size: smaller">2018</span></h4>
                              <p>"Gratitude can transform common days into Thanksgiving, turn routine jobs into joy and change opportunities into blessings." - William Arthur Ward <br/><br/>Teams, we hope you're enjoying<a href="https://www.instagram.com/explore/tags/csweek/" target="_blank">#CSWeek </a> so far. Don't forget to use <a href="https://www.instagram.com/explore/tags/csOAonCSWeek2018/" target="_blank"> #OAonCSWeek2018</a> or tag us so we can see your photos, too!</p>
                              <img src="storage/uploads/appreciate3.jpg" />
                              <div style="padding:10px; position: absolute;bottom: 10px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAonCSWeek2018</small></a></div>
                           </div> 

                            <div class="item text-center">
                              <h4 class="text-primary">Open Access CS Week <span class="text-orange" style="font-size: smaller">2018</span></h4>
                              <p>"Gratitude can transform common days into Thanksgiving, turn routine jobs into joy and change opportunities into blessings." - William Arthur Ward <br/><br/>Teams, we hope you're enjoying<a href="https://www.instagram.com/explore/tags/csweek/" target="_blank">#CSWeek </a> so far. Don't forget to use <a href="https://www.instagram.com/explore/tags/csOAonCSWeek2018/" target="_blank"> #OAonCSWeek2018</a> or tag us so we can see your photos, too!</p>
                              <img src="storage/uploads/appreciate4.jpg" />
                              <div style="padding:10px; position: absolute;bottom: 10px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAonCSWeek2018</small></a></div>
                           </div> 

                            <div class="item text-center">
                              <h4 class="text-primary">Open Access CS Week <span class="text-orange" style="font-size: smaller">2018</span></h4>
                              <p>"Gratitude can transform common days into Thanksgiving, turn routine jobs into joy and change opportunities into blessings." - William Arthur Ward <br/><br/>Teams, we hope you're enjoying<a href="https://www.instagram.com/explore/tags/csweek/" target="_blank">#CSWeek </a> so far. Don't forget to use <a href="https://www.instagram.com/explore/tags/csOAonCSWeek2018/" target="_blank"> #OAonCSWeek2018</a> or tag us so we can see your photos, too!</p>
                              <img src="storage/uploads/appreciate5.jpg" />
                              <div style="padding:10px; position: absolute;bottom: 10px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAonCSWeek2018</small></a></div>
                           </div> 

                            <div class="item text-center">
                              <h4 class="text-primary">Open Access CS Week <span class="text-orange" style="font-size: smaller">2018</span></h4>
                              <p>"Gratitude can transform common days into Thanksgiving, turn routine jobs into joy and change opportunities into blessings." - William Arthur Ward <br/><br/>Teams, we hope you're enjoying<a href="https://www.instagram.com/explore/tags/csweek/" target="_blank">#CSWeek </a> so far. Don't forget to use <a href="https://www.instagram.com/explore/tags/csOAonCSWeek2018/" target="_blank"> #OAonCSWeek2018</a> or tag us so we can see your photos, too!</p>
                              <img src="storage/uploads/appreciate6.jpg" />
                              <div style="padding:10px; position: absolute;bottom: 10px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAonCSWeek2018</small></a></div>
                           </div> 

                            <div class="item text-center">
                              <h4 class="text-primary">Open Access CS Week <span class="text-orange" style="font-size: smaller">2018</span></h4>
                              <p>"Gratitude can transform common days into Thanksgiving, turn routine jobs into joy and change opportunities into blessings." - William Arthur Ward <br/><br/>Teams, we hope you're enjoying<a href="https://www.instagram.com/explore/tags/csweek/" target="_blank">#CSWeek </a> so far. Don't forget to use <a href="https://www.instagram.com/explore/tags/csOAonCSWeek2018/" target="_blank"> #OAonCSWeek2018</a> or tag us so we can see your photos, too!</p>
                              <img src="storage/uploads/appreciate7.jpg" />
                              <div style="padding:10px; position: absolute;bottom: 10px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAonCSWeek2018</small></a></div>
                           </div> 

                            <div class="item text-center">
                              <h4 class="text-primary">Open Access CS Week <span class="text-orange" style="font-size: smaller">2018</span></h4>
                              <p>"Gratitude can transform common days into Thanksgiving, turn routine jobs into joy and change opportunities into blessings." - William Arthur Ward <br/><br/>Teams, we hope you're enjoying<a href="https://www.instagram.com/explore/tags/csweek/" target="_blank">#CSWeek </a> so far. Don't forget to use <a href="https://www.instagram.com/explore/tags/csOAonCSWeek2018/" target="_blank"> #OAonCSWeek2018</a> or tag us so we can see your photos, too!</p>
                              <img src="storage/uploads/appreciate8.jpg" />
                              <div style="padding:10px; position: absolute;bottom: 10px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAonCSWeek2018</small></a></div>
                           </div> 

                            <div class="item text-center">
                              <h4 class="text-primary">Open Access CS Week <span class="text-orange" style="font-size: smaller">2018</span></h4>
                              <p>"Gratitude can transform common days into Thanksgiving, turn routine jobs into joy and change opportunities into blessings." - William Arthur Ward <br/><br/>Teams, we hope you're enjoying<a href="https://www.instagram.com/explore/tags/csweek/" target="_blank">#CSWeek </a> so far. Don't forget to use <a href="https://www.instagram.com/explore/tags/csOAonCSWeek2018/" target="_blank"> #OAonCSWeek2018</a> or tag us so we can see your photos, too!</p>
                              <img src="storage/uploads/appreciate9.jpg" />
                              <div style="padding:10px; position: absolute;bottom: 10px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAonCSWeek2018</small></a></div>
                           </div> 

                            <div class="item text-center">
                              <h4 class="text-primary">Open Access CS Week <span class="text-orange" style="font-size: smaller">2018</span></h4>
                              <p>"Gratitude can transform common days into Thanksgiving, turn routine jobs into joy and change opportunities into blessings." - William Arthur Ward <br/><br/>Teams, we hope you're enjoying<a href="https://www.instagram.com/explore/tags/csweek/" target="_blank">#CSWeek </a> so far. Don't forget to use <a href="https://www.instagram.com/explore/tags/csOAonCSWeek2018/" target="_blank"> #OAonCSWeek2018</a> or tag us so we can see your photos, too!</p>
                              <img src="storage/uploads/appreciate9b.jpg" />
                              <div style="padding:10px; position: absolute;bottom: 10px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAonCSWeek2018</small></a></div>
                           </div>  -->

                            <!-- ********************************* CS WEEK GRATITUDE ******************************** -->



                           <!--  <div class="item text-left">
                               <img src="storage/uploads/banner.jpg" /><br/>
                               <h4 class="text-primary">DAY 5 Open Access CS Week <br/><span class="text-orange" style="font-size: smaller"> Schedule of Activities:</span></h4>

                               <p><strong>PHOTO BOOTH:</strong> 8th Floor from 2:00 pm-6:00 PM<br/>
                                <strong>Townhall with Ben: </strong> 3PM, 8thFloor pantry area<br/>
                                <strong>Dress-Your-TL Portrait announcement of winners: </strong> 3PM, 8th Floor <br/>
                                <strong>Food service : </strong> 8AM | 12NN | 7PM
                              </p>
                              <h4 class="text-danger"><i>See you there!</i></h4>
                             </div>


                            <div class="item text-center">
                             
                              <h4 class="text-primary">DAY 4 Open Access CS Week <br/><span class="text-orange" style="font-size: smaller"> Dress Up Your Leader Day!</span></h4>

                             <p> To make <strong>Celebrating the Best Version of Your Leader!</strong> even more exciting, you can take your TL's photos, group pics, and selfies in front of the <a href="https://www.instagram.com/explore/tags/csweek/" target="_blank">#CSWeek</a> Photo Backdrop on the 8th Floor. <br/>It'll be set up near the entrance, all day for all the shifts <strong>starting 8:00 AM today (Oct 4 Thu)</strong><br/><br/>
                              <strong>Don't forget:</strong> <strong class="text-danger">The best-dressed TL Portrait wins a <br/><span style="font-size: 1.8em" class="text-primary"> team dinner or Php 15,000 cash equivalent </span><br/> and backstage passes to the Year-End Party!</strong>   And P5,000.00 and P3,000.00 to the runner-up teams.<br/><br/>
                              So have a blast dressing up your TL's and posing for your group shots!  (And selfies.)  Have fun!</p>
                            </div> -->


                            <!-- ****** DONUTS ********** !-->
                            <!-- <div class="item text-center">
                              <h4 class="text-primary">Open Access CS Week <span class="text-orange" style="font-size: smaller">2018</span></h4>
                              <p>Dunkin' is in the house! Get your free donuts and coffee at the 8th floor pantry today. Happy <a href="https://www.instagram.com/explore/tags/csweek/" target="_blank">#CSWeek </a>everyone!</p>
                              <img src="storage/uploads/donuts1.jpg" />
                              <div style="padding:10px; position: absolute;bottom: 10px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAonCSWeek2018</small></a></div>
                           </div> 

                           <div class="item text-center">
                              <h4 class="text-primary">Open Access CS Week <span class="text-orange" style="font-size: smaller">2018</span></h4>
                              <p>Dunkin' is in the house! Get your free donuts and coffee at the 8th floor pantry today. Happy <a href="https://www.instagram.com/explore/tags/csweek/" target="_blank">#CSWeek </a>everyone!</p>
                              <img src="storage/uploads/donuts2.jpg" />
                              <div style="padding:10px; position: absolute;bottom: 10px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAonCSWeek2018</small></a></div>
                           </div> 

                           <div class="item text-center">
                              <h4 class="text-primary">Open Access CS Week <span class="text-orange" style="font-size: smaller">2018</span></h4>
                              <p>Dunkin' is in the house! Get your free donuts and coffee at the 8th floor pantry today. Happy <a href="https://www.instagram.com/explore/tags/csweek/" target="_blank">#CSWeek </a>everyone!</p>
                              <img src="storage/uploads/donuts3.jpg" />
                              <div style="padding:10px; position: absolute;bottom: 10px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAonCSWeek2018</small></a></div>
                           </div> 

                           <div class="item text-center">
                              <h4 class="text-primary">Open Access CS Week <span class="text-orange" style="font-size: smaller">2018</span></h4>
                              <p>Dunkin' is in the house! Get your free donuts and coffee at the 8th floor pantry today. Happy <a href="https://www.instagram.com/explore/tags/csweek/" target="_blank">#CSWeek </a>everyone!</p>
                              <img src="storage/uploads/donuts4.jpg" />
                              <div style="padding:10px; position: absolute;bottom: 10px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAonCSWeek2018</small></a></div>
                           </div> 

                           <div class="item text-center">
                              <h4 class="text-primary">Open Access CS Week <span class="text-orange" style="font-size: smaller">2018</span></h4>
                              <p>Dunkin' is in the house! Get your free donuts and coffee at the 8th floor pantry today. Happy <a href="https://www.instagram.com/explore/tags/csweek/" target="_blank">#CSWeek </a>everyone!</p>
                              <img src="storage/uploads/donuts5.jpg" />
                              <div style="padding:10px; position: absolute;bottom: 10px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAonCSWeek2018</small></a></div>
                           </div> 

                           <div class="item text-center">
                              <h4 class="text-primary">Open Access CS Week <span class="text-orange" style="font-size: smaller">2018</span></h4>
                              <p>Dunkin' is in the house! Get your free donuts and coffee at the 8th floor pantry today. Happy <a href="https://www.instagram.com/explore/tags/csweek/" target="_blank">#CSWeek </a>everyone!</p>
                              <img src="storage/uploads/donuts6.jpg" />
                              <div style="padding:10px; position: absolute;bottom: 10px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAonCSWeek2018</small></a></div>
                           </div> 

                           <div class="item text-center">
                              <h4 class="text-primary">Open Access CS Week <span class="text-orange" style="font-size: smaller">2018</span></h4>
                              <p>Dunkin' is in the house! Get your free donuts and coffee at the 8th floor pantry today. Happy <a href="https://www.instagram.com/explore/tags/csweek/" target="_blank">#CSWeek </a>everyone!</p>
                              <img src="storage/uploads/donuts7.jpg" />
                              <div style="padding:10px; position: absolute;bottom: 10px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAonCSWeek2018</small></a></div>
                           </div> 

                           <div class="item text-center">
                              <h4 class="text-primary">Open Access CS Week <span class="text-orange" style="font-size: smaller">2018</span></h4>
                              <p>Dunkin' is in the house! Get your free donuts and coffee at the 8th floor pantry today. Happy <a href="https://www.instagram.com/explore/tags/csweek/" target="_blank">#CSWeek </a>everyone!</p>
                              <img src="storage/uploads/donuts8.jpg" />
                              <div style="padding:10px; position: absolute;bottom: 10px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAonCSWeek2018</small></a></div>
                           </div> 

                           <div class="item text-center">
                              <h4 class="text-primary">Open Access CS Week <span class="text-orange" style="font-size: smaller">2018</span></h4>
                              <p>Dunkin' is in the house! Get your free donuts and coffee at the 8th floor pantry today. Happy <a href="https://www.instagram.com/explore/tags/csweek/" target="_blank">#CSWeek </a>everyone!</p>
                              <img src="storage/uploads/donuts9.jpg" />
                              <div style="padding:10px; position: absolute;bottom: 10px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAonCSWeek2018</small></a></div>
                           </div>  -->
                           <!-- ****** DONUTS ********** !-->


                           
                             <!-- ********************************* CS WEEK LETTER ******************************** -->
                           <!--  <div class="item text-left">
                               <img src="storage/uploads/banner.jpg" />
                              <p style="padding-left: 20px; font-size: smaller"><br/><strong>Team,</strong> <br/><br/>We celebrate Customer Service Week starting today. It is an international celebration of the importance of customer service and of the people who serve and support customers on a daily basis. It is commemorated annually during the first week of October. </p>

                                <p style="padding-left: 20px; font-size: smaller">This week, we are taking the time to Celebrate People. YOU. Our business is people. It's run by people. It's built for people. Its purpose is people. That's you and us behind our company, serving our clients and customers. All of us together making Open Access an awesome place to work in and be a part of.</p>

                                <p style="padding-left: 20px; font-size: smaller">As a company, all we do is help people. We help people have happy interactions. We deal with people who call, who chat, who message, who have back-office needs, and we try to resolve their issues and meet their business demands in the best way possible. That takes not just mental and physical energy, but also emotional energy, time, patience, and creativity. And it's a hard thing to try to make people happy all the time. So we're taking the time to recognize all your hard work and commitment this year.</p>

                                <p style="padding-left: 20px; font-size: smaller">Today is the day, this week is the week we celebrate you guys for working so hard at making our customers happy. We celebrate you. We celebrate us. </p>

                                <p style="padding-left: 20px; font-size: smaller">Watch out for the treats and surprises we have in store for all of you throughout the week.
                                To appreciate. To acknowledge. To highlight. To thank. To celebrate. Our most important asset: YOU. OUR PEOPLE.</p>


                                <p class="pull-right" style="text-align: left">All the very best,<br/> <a target="_blank" href="user/1784"><img src="./public/img/employees/1784.jpg" class="img-circle" width="80" style="margin-left: 5px" /><br/><strong>Joy Sebastian</strong></a><br/><small class="text-black">VP for Operations </small>
                                </p>
                            </div> -->
                             <!-- ********************************* CS WEEK LETTER ******************************** -->

                           <!--  <div class="item text-center" >

                              <img src="./public/img/ana.jpg" style="z-index: 2" />

                              <div class="text-gray text-right" style="position: relative;width:90%; right:0px;top:-200px; z-index: 999; height: 20px">
                                 <h2><img src="./public/img/white_logo_small.png" width="40" style="margin-right: 30px" /><br/> <strong>Internal<br/>Hiring! </strong></h2>

                              </div>

                               <h2 style="margin-top: -30px" class="text-danger"><strong>OIC </strong><br/>
                               <small>for</small> <a target="_blank" href="{{action('CampaignController@show','44')}} "> <img src="./public/img/logo_postmates.png" width="120"></a></h2>

                              <h5 class="text-danger"><i class="fa fa-exclamation-triangle"></i> Deadline of Submission: <strong class="text-primary"> Oct. 5, 2018 Friday </strong></h5>
                              <p class="text-left" style="padding-left: 30px; font-weight: bolder;">Requirements: </p>
                              <ul class="text-left">
                                <li>Excellent written and verbal English skills</li>
                                <li>At least 1 yr. in Open Access</li>
                                <li>No written warning within the last six months</li>
                                <li>Exemplary Performance</li>
                              </ul>

                              <p><small>Interested applicants may submit their letter of intent and updated resume to: <a href="mailto:recruitment@openaccessmarketing.com">recruitment@openaccessmarketing.com</a></small></p>

                             


                            </div>     -->

                           <!--  <div class="item text-center">
                              <h4 class="text-primary">OAM Clinic Health &amp; Wellness Program <span class="text-orange" style="font-size: smaller">[09.26.2018]</span></h4>
                              <p>Thanks to all employees who visited the booths at the 8th floor and to our partners for helping us keep our employees' health in check.</p>
                              <img src="storage/uploads/wellness1a.jpg" />
                              <div style="padding:10px; position: absolute;bottom: 10px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage</small></a></div>

                               <h4 class="text-primary">What: OAM Clinic Wellness Program</h4>

                              <img src="storage/uploads/yakult.jpg" /><br/>
                            
                              <p><strong>Where:</strong> <span class="text-danger" style="font-size: larger"> 8th Floor</span><br/>
                              <strong>When:</strong> <strong class="text-danger">September 26, 2018, Wednesday 10AM - 7PM</strong><br/>
                              
                              <small>Should you have questions or concerns, please feel free to drop by the clinic or email our nurses at <strong>nurse@openaccessbpo.net / nurse@openaccessmarketing.com.</strong></small>
                               <h5>Today, Japan celebrates Respect for the Aged Day.</h5>
                              <img src="storage/uploads/ig1.jpg" />
                              <small>To express gratitude and respect to the elders, communities organize special performances and distribute free lunches and commemorative gifts. School children are encouraged to make handmade gifts for their grandparents. The holiday is spent all over Japan and is given great attention by the citizens.<br/><br/>To our Japanese friends, colleagues, and compatriots, may you find joy and peace as you celebrate this day with your loved ones.</small> 
                            </div> -->

                           <!--  <div class="item text-center">
                              <h4 class="text-primary">OAM Clinic Health &amp; Wellness Program <span class="text-orange" style="font-size: smaller">[09.26.2018]</span></h4>
                              <p>Thanks to all employees who visited the booths at the 8th floor and to our partners for helping us keep our employees' health in check.</p>
                              <img src="storage/uploads/wellness1b.jpg" />
                              <div style="padding:10px; position: absolute;bottom: 10px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage</small></a></div>
                           </div> 

                            <div class="item text-center">
                              <h4 class="text-primary">OAM Clinic Health &amp; Wellness Program <span class="text-orange" style="font-size: smaller">[09.26.2018]</span></h4>
                              <p>Thanks to all employees who visited the booths at the 8th floor and to our partners for helping us keep our employees' health in check.</p>
                              <img src="storage/uploads/wellness1c.jpg" />
                              <div style="padding:10px; position: absolute;bottom: 10px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage</small></a></div>
                            </div>  

                            <div class="item text-center">
                              <h4 class="text-primary">OAM Clinic Health &amp; Wellness Program <span class="text-orange" style="font-size: smaller">[09.26.2018]</span></h4>
                              <p>Thanks to all employees who visited the booths at the 8th floor and to our partners for helping us keep our employees' health in check.</p>
                              <img src="storage/uploads/wellness1d.jpg" />
                              <div style="padding:10px; position: absolute;bottom: 10px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage</small></a></div>
                            </div> 

                            <div class="item text-center">
                              <h4 class="text-primary">OAM Clinic Health &amp; Wellness Program <span class="text-orange" style="font-size: smaller">[09.26.2018]</span></h4>
                              <p>Thanks to all employees who visited the booths at the 8th floor and to our partners for helping us keep our employees' health in check.</p>
                              <img src="storage/uploads/wellness1e.jpg" />
                              <div style="padding:10px; position: absolute;bottom: 10px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage</small></a></div>
                            </div> 
                            
                            <div class="item text-center">
                              <h4 class="text-primary">OAM Clinic Health &amp; Wellness Program <span class="text-orange" style="font-size: smaller">[09.26.2018]</span></h4>
                              <p>Thanks to all employees who visited the booths at the 8th floor and to our partners for helping us keep our employees' health in check.</p>
                              <img src="storage/uploads/wellness1f.jpg" />
                              <div style="padding:10px; position: absolute;bottom: 10px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage</small></a></div>
                            </div> 

                            <div class="item text-center">
                              <h4 class="text-primary">OAM Clinic Health &amp; Wellness Program <span class="text-orange" style="font-size: smaller">[09.26.2018]</span></h4>
                              <p>Thanks to all employees who visited the booths at the 8th floor and to our partners for helping us keep our employees' health in check.</p>
                              <img src="storage/uploads/wellness1g.jpg" />
                              <div style="padding:10px; position: absolute;bottom: 10px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage</small></a></div>
                            </div> 

                            <div class="item text-center">
                              <h4 class="text-primary">OAM Clinic Health &amp; Wellness Program <span class="text-orange" style="font-size: smaller">[09.26.2018]</span></h4>
                              <p>Thanks to all employees who visited the booths at the 8th floor and to our partners for helping us keep our employees' health in check.</p>
                              <img src="storage/uploads/wellness1h.jpg" />
                              <div style="padding:10px; position: absolute;bottom: 10px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage</small></a></div>
                            </div> 

                            <div class="item text-center">
                              <h4 class="text-primary">OAM Clinic Health &amp; Wellness Program <span class="text-orange" style="font-size: smaller">[09.26.2018]</span></h4>
                              <p>Thanks to all employees who visited the booths at the 8th floor and to our partners for helping us keep our employees' health in check.</p>
                              <img src="storage/uploads/wellness1i.jpg" />
                             <div style="padding:10px; position: absolute;bottom: 10px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage</small></a></div>
                            </div> 

                            <div class="item text-center">
                              <h4 class="text-primary">OAM Clinic Health &amp; Wellness Program <span class="text-orange" style="font-size: smaller">[09.26.2018]</span></h4>
                              <p>Thanks to all employees who visited the booths at the 8th floor and to our partners for helping us keep our employees' health in check.</p>
                              <img src="storage/uploads/wellness1j.jpg" />
                              <div style="padding:10px; position: absolute;bottom: 10px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage</small></a></div>
                            </div> 

                            <div class="item text-center">
                              <h4 class="text-primary">OAM Clinic Health &amp; Wellness Program <span class="text-orange" style="font-size: smaller">[09.26.2018]</span></h4>
                              <p>Thanks to all employees who visited the booths at the 8th floor and to our partners for helping us keep our employees' health in check.</p>
                              <img src="storage/uploads/wellness1k.jpg" />
                              <div style="padding:10px; position: absolute;bottom: 10px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage</small></a></div>
                            </div>  



                            <div class="item text-center">

                              <h3 class="text-danger"><small>Urgent Hiring </small><i class="fa fa-exclamation-triangle"></i> <br/><strong>E-mail Support </strong> Agents (Php 23k)<br/>
                               <small>for</small> <a target="_blank" href="{{action('CampaignController@show','42')}} "> <img src="./public/img/logo_bird.png" width="120"></a></h3>
                              <h5>Referral Bonus: <strong class="text-primary"> Php 5,000.00 <i class="fa fa-gift"></i></strong><br/>
                              Training starts next week Monday (October 22 @ 3 PM - 12 MN)</h5>
                              <p>Requirements: </p>
                              <ul class="text-left">
                                <li>Previous experience in live chat/email support/customer service/call center environment</li>
                                <li>Strong customer service background</li>
                                <li>High level of written English proficiency + a good sense of US culture/vibe</li>
                                <li>Technical savvy (frequently uses smart phones applications, can navigate basic computer functions like copy /paste, opening multiple tabs, email etc pretty smoothly)</li>
                                <li>CRM experience is an added plus</li>
                                <li>Personality preferred: positive, kind empathetic and upbeat</li>
                                <li>With background/experience in English Writing, Communications and Journalism</li>
                              </ul>

                              <p><small>Send your referrals to <a href="mailto:recruitment@openaccessmarketing.com">recruitment@openaccessmarketing.com</a></small></p>



                            </div>   

                              <div class="item text-center">

                              <h4 class="text-primary"><small>We're hiring!</small><br/><strong>Mandarin</strong> Customer Support Agent (Php 80k - morning shift)</h4>
                              <h5>Referral Bonus: <strong class="text-danger">Php 20,000.00</strong></h5>
                              <p><br/><br/>
                              The agent will be working as a <br/><strong>non-voice customer support</strong> agent <br/>for an international company that develops <br/>products exclusively for women. </p>

                              <p><small>Send your referrals to <a href="mailto:recruitment@openaccessmarketing.com">recruitment@openaccessmarketing.com</a></small></p>



                            </div>      

                            //POSTMATES INTERNAL HIRING
                            <div class="item text-center" >

                              <img src="./public/img/ana.jpg" style="z-index: 2" />

                              <div class="text-gray text-right" style="position: relative;width:90%; right:0px;top:-200px; z-index: 999; height: 20px">
                                 <h2><img src="./public/img/white_logo_small.png" width="40" style="margin-right: 30px" /><br/> <strong>Internal<br/>Hiring! </strong></h2>

                              </div>

                               <h2 style="margin-top: -30px" class="text-danger"><strong>QA Apprentice </strong><br/>
                               <small>for</small> <a target="_blank" href="{{action('CampaignController@show','44')}} "> <img src="./public/img/logo_postmates.png" width="120"></a></h2>

                              <h5 class="text-danger"><i class="fa fa-exclamation-triangle"></i> Deadline of Submission: <strong class="text-primary"> Oct. 24, 2018 Wednesday </strong></h5>
                              <p class="text-left" style="padding-left: 30px; font-weight: bolder;">Requirements: </p>
                              <ul class="text-left">
                                <li>Excellent written and verbal English skills</li>
                                <li>At least 1 yr. in Open Access</li>
                                <li>No written warning within the last six months</li>
                                <li>Exemplary Performance</li>
                              </ul>

                              <p><small>Interested applicants may submit their letter of intent and updated resume to: <a href="mailto:recruitment@openaccessmarketing.com">recruitment@openaccessmarketing.com</a></small></p>


                            </div>  


                          -->




