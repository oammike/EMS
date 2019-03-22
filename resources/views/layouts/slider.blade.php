
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

                    <div class="item active text-center" >
                        <img src="./storage/uploads/women1.jpg" style="z-index: 2" width="100%" /><br/><br/>
                        <p style="padding:10px 50px">Get to know @openaccessbpo's amazing female leaders and teammates and learn from their stories in this year's #WomenOfOA project! ✨<br/>
                          <strong class="text-primary">#WeSpeakYourLanguage #OAonWomensHistoryMonth #WomenOfOA #IWD2019 #BalanceForBetter</strong> </p>

                          <div style="padding:10px; position: absolute;bottom: 0px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                    <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAonWomensHistoryMonth #WomenOfOA #IWD2019 #BalanceForBetter</small></a></div> <br/><br/><br/><br/>

                    </div>

                    <div class="item  text-center" >
                        <img src="./storage/uploads/women2.jpg" style="z-index: 2" width="100%" /><br/><br/>
                        <p style="padding:10px 50px">Discrimination is a major problem across the globe. For anyone who's been through it or is going through it, you should know that you're more than what they tell you and that this shouldn't limit you from what you can do and who you can be. <br/><br/>
                        Our first feature for this year's <strong>#WomenOfOA</strong> is our French CSR, <strong><a href="{{action('UserController@show',2625)}}" target="_blank" > Anais Andriatsivalaka</a></strong> . Anais is a native of Madagascar who has left home to pursue her career. She has moved to different countries in Africa, Europe, and now, Asia. In doing so, she has experienced the difficulties of being out of her comfort zone and being subjected to discrimination, sexism, and violence. With this experience, she shares a life lesson that may help people who's going through the same. "It is not easy just to leave home and be on your own especially when you are a woman moreover black: it may happen that you encounter discrimination, sexism and even violence. It is important not to take it personally and to remember that though it may be pure hatred, most of the time it is just linked to cultural gaps, and also a lack of knowledge and education." Along with her bubbly personality, Anais possesses the quality of a strong woman, and her wisdom attests to that. "Today, I feel grateful that I am still standing, able to discover other cultures, and bring all this experience to support my relatives and hopefully, as an asset to the company I join. [So,] should you decide to risk it (YES YOU SHOULD), do not to forget that wherever you go, no matter what people tell you or how much they try to put you down, you are here as a person and you have to fight— not against people but to try to be the best version of yourself because this is how you will stand out.” #WeSpeakYourLanguage #OAonWomensHistoryMonth<br/>
                          <strong class="text-primary">#WeSpeakYourLanguage #OAonWomensHistoryMonth #WomenOfOA #IWD2019 #BalanceForBetter </strong></p>

                          <div style="padding:10px; position: absolute;bottom: 0px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                    <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAonWomensHistoryMonth #WomenOfOA #IWD2019 #BalanceForBetter</small></a></div> <br/><br/><br/><br/>

                    </div>


                      <div class="item text-center" >
                        <img src="./storage/uploads/happiness1.jpg" style="z-index: 2" width="100%" /><br/><br/>
                        <p style="padding:10px 50px">To live mindfully helps you understand yourself better and what makes you happy. Choose to stress less and create and influence happiness in your workplace, community, and at home. <br/>
                          <strong class="text-orange"> Happy #InternationalDayOfHappiness! </strong>😊<br/><br/>
                        #WeSpeakYourLanguage #OAonHappinessDay #WorldHappinessDay #Happiness</p>

                          <div style="padding:10px; position: absolute;bottom: 0px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                    <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAonHappinessDay #WorldHappinessDay #Happiness</small></a></div> <br/><br/><br/><br/>

                      </div>

                      <div class="item text-center" >
                        <img src="./storage/uploads/happiness2.jpg" style="z-index: 2" width="100%" /><br/><br/>
                        <p style="padding:10px 50px">To live mindfully helps you understand yourself better and what makes you happy. Choose to stress less and create and influence happiness in your workplace, community, and at home. <br/>
                          <strong class="text-orange"> Happy #InternationalDayOfHappiness! </strong>😊<br/><br/>
                        #WeSpeakYourLanguage #OAonHappinessDay #WorldHappinessDay #Happiness</p>

                          <div style="padding:10px; position: absolute;bottom: 0px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                    <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAonHappinessDay #WorldHappinessDay #Happiness</small></a></div> <br/><br/><br/><br/>

                      </div>

                      <div class="item text-center" >
                        
                         <p style="padding: 5px 30px; margin-bottom: 0px"><h3 class="text-orange"><span style="font-size: smaller;" class="text-primary">What: </span>Open Access 1st Blood Donation Drive 2019</h3><br/><br/>
                          <img src="./storage/uploads/wellness10.jpg" style="z-index: 2" width="100%" /><br/><br/>
                          <p style="padding: 5px 30px; margin-bottom: 0px">
                          <h4>
                          <strong>When:</strong> <span class="text-danger"> March 27, 2019 ( G2); <br/>March 28, 2019 ( Jaka )</span><br/>
                          Time: <span class="text-danger"> 8AM-5PM</span><br/>
                          <strong>Where:</strong> 11th Flr ( G2 ), 5th Flr. ( Jaka )</h4><br/><br/></p>

                          <p style="padding: 5px 30px; margin-bottom: 0px;" class="text-left">
                          <span style="font-size: x-large;"> Basic Requirements:</span><br/><br/>
                          Blood donation helps save lives. Find out if you're eligible to donate blood and what to expect if you decide to donate.<br/><br/>
                          You can donate blood if you…<br/>
                          - Are in good health<br/>
                          - Are between 16 to 65 years old<br/>
                          - Weigh at least 110 pounds (approximately 50kg)<br/>
                          - Have a blood pressure between Systolic: 90-140mmHg,Diastolic: 60-100mmHg; and<br/>
                          - Pass the physical and health history assessments.</p>

                          <h5 class="text-primary"> Every volunteer donor will be given a BLOOD DONOR CARD during the event. This card may be used as a record of donation. However, this card does not exempt the holder from paying the processing fee. This is intended to cover the cost of the reagents an operating expenses used to collect and screen all donated blood for infectious disease</h5><br/><br/><br/><br/>
                      </div>

                      <div class="item text-center" >
                        <img src="./storage/uploads/WOMENSDAY.jpg" style="z-index: 2" width="100%" /><br/><br/>
                        <p style="padding:10px 50px">Women can and women will. On #WomensDay, we celebrate girls and women who challenge gender stereotypes and inspire change to make a gender-equal world. <br/><br/><strong>Happy International Women’s Day!</strong>
                          <br/><br/>#WeSpeakYourLanguage #OAonWomensDay #WomensDay #IWD2019 #IWD</p>

                          <div style="padding:10px; position: absolute;bottom: 0px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                    <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAonWomensDay #WomensDay #IWD2019 #IWD</small></a></div> <br/><br/><br/><br/>

                      </div>


                      <div class="item text-center" >
                        
                         <p style="padding: 5px 30px; margin-bottom: 0px"><h3 class="text-orange"><span style="font-size: smaller;" class="text-primary"> Let's all get our bods</span> Summer-ready!</h3>And the best part is:<br/>it's <span style="font-size: x-large;"> FUN and FREE!!!</span><br/><br/>
                          <img src="./storage/uploads/kick4.jpg" style="z-index: 2" /><br/><br/>

                          Here's the schedule for this week:<br/><br/>

                          <strong style="font-size:larger"><span class="text-primary"> ZUMBA:</span> 
                            <br/><span class="text-danger">March 20, 2019 (Wed) – 7:00 PM</span><br/></strong><br/>

                          <strong style="font-size:larger"><span class="text-primary"> AERO KICKBOXING:</span> 
                            <BR/><span class="text-danger">March 21, 2019 (Thu) – 6:00 PM</span><br/></strong><br/> 

                           <strong style="font-size:larger"><span class="text-primary"> YOGA:</span> 
                            <br/><span class="text-danger">March 22, 2019 (Fri) – 7:00 PM</span><br/></strong>

                          <br/><br/>
                           <strong>Venue:</strong><br/>

                            OPEN ACCESS BPO G2 OFFICE<br/>

                            11F Glorietta 2 Corporate Center,<br/>

                            West. St., Ayala Center, Makati City<br/><br/>

                            <img src="./storage/uploads/oam-location.jpg" style="z-index: 2" width="100%" />
                            <br/><br/>



                              Sign up here: <a href="http://172.17.0.2/coffeebreak/event/5494/" target="_blank">http://172.17.0.2/coffeebreak/event/5494/</a><br/><br/>
                              View our <a class="text-danger" href="./gallery?a=5"><i class="fa fa-picture-o"></i> Gallery</a> for more<br/> #WeSpeakYourLanguage #LetsGetPhysical #OAforWellness

                           
                            <div style="padding:10px; position: absolute;bottom: 0px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                  <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAforWellness #LetsGetPhysical #Zumba</small></a></div> <br/><br/><br/><br/>
                      </div>


                      

                      <div class="item text-center" >
                        
                         <img src="./storage/uploads/teams1.jpg" style="z-index: 2" width="100%" /><br/><br/>
                         

                          <p style="padding: 5px 30px; margin-bottom: 0px;" class="text-left">
                          Get to know more about your <a href="{{action('CampaignController@index')}}"><strong>Open Access family!</strong></a><br/><br/> Check out our <a class="text-orange" href="{{action('CampaignController@index')}}">programs</a> and learn more about the driving force behind our campaigns.</p>

                          <p style="padding: 5px 30px; margin-bottom: 0px; font-size: x-small;" class="text-left"><br/><br/>
                            <i class="fa fa-info-circle text-primary"></i> Tip: Make sure the page you're viewing is updated. Press <strong>CTRL + Shift + R </strong> to do a hard refresh and clear your browser's cache.
                          </p>

                          
                      </div>



                      

                      <div class="item text-center" >
                        
                         <img src="./storage/uploads/teams2.jpg" style="z-index: 2" width="100%" /><br/><br/>
                         

                          <p style="padding: 5px 30px; margin-bottom: 0px;" class="text-left">
                          Get to know more about your <a href="{{action('CampaignController@index')}}"><strong>Open Access family!</strong></a><br/><br/> Check out our <a class="text-orange" href="{{action('CampaignController@index')}}">programs</a> and learn more about the driving force behind our campaigns.</p>

                           <p style="padding: 5px 30px; margin-bottom: 0px; font-size: x-small;" class="text-left"><br/><br/>
                            <i class="fa fa-info-circle text-primary"></i> Tip: Make sure the page you're viewing is updated. Press <strong>CTRL + Shift + R </strong> to do a hard refresh and clear your browser's cache.
                          </p>

                          
                      </div>

                      <div class="item text-center" >
                        <h5>Please be guided of the following schedule for picture taking for our new company ID</h5>
                        <img src="./storage/uploads/bts-1.jpg" style="z-index: 2" width="100%" />
                        <h4>Venue: HR Office, 9th Floor<br/>
                          Attire: <strong>STRICTLY PLAIN BLACK TOP</strong> <br/>(No prints, No design, No sleeveless)</h4><br/>
                          <h5>For those who don't have plain black shirts, there are polo shirts provided by HR that you may use.</h5>
                          <img src="./storage/uploads/bts-2.jpg" style="z-index: 2" width="100%" />

                          <h5>* For Open Access G2 Employees, details to follow for schedule of the ID picture taking.</h5>

                        <table class="table">
                          <tr>
                            <th>Program/Department</th>
                            <th>Date</th>
                            <th>Morning</th>
                            <th>Night</th>
                          </tr>
                          <tr>
                            <td>Finance</td>
                            <td>March 12-13</td>
                            <td>10:00am-7:00pm</td>
                            <td>6:00pm-6:00am</td>
                          </tr>
                          <tr>
                            <td>Business Development/ Marketing/Lebua</td>
                            <td>March 12-13</td>
                            <td>10:00am-7:00pm</td>
                            <td>6:00pm-6:00am</td>
                          </tr>
                          <tr>
                            <td>Engineering/ Facilities</td>
                            <td>March 12-13</td>
                            <td>10:00am-7:00pm</td>
                            <td>6:00pm-6:00am</td>
                          </tr>
                          <tr>
                            <td>Workforce Team</td>
                            <td>March 12-13</td>
                            <td>10:00am-7:00pm</td>
                            <td>6:00pm-6:00am</td>
                          </tr>
                          <tr>
                            <td>QA & Performance</td>
                            <td>March 12-13</td>
                            <td>10:00am-7:00pm</td>
                            <td>6:00pm-6:00am</td>
                          </tr>
                          <tr>
                            <td>IT</td>
                            <td>March 12-13</td>
                            <td>10:00am-7:00pm</td>
                            <td>6:00pm-6:00am</td>
                          </tr>
                           <tr>
                            <td>Training Department</td>
                            <td>March 12-13</td>
                            <td>10:00am-7:00pm</td>
                            <td>6:00pm-6:00am</td>
                          </tr>
                           <tr>
                            <td>Ops Support</td>
                            <td>March 12-13</td>
                            <td>10:00am-7:00pm</td>
                            <td>6:00pm-6:00am</td>
                          </tr>
                          <tr>
                            <td>Exec</td>
                            <td>March 12-13</td>
                            <td>10:00am-7:00pm</td>
                            <td>6:00pm-6:00am</td>
                          </tr>
                          <tr>
                            <td>Adore Me</td>
                            <td>March 14-15</td>
                            <td>6:00am-6:00pm</td>
                            <td>6:00pm-6:00am</td>
                          </tr>
                          <tr>
                            <td>Advance Wellness</td>
                            <td>March 14-15</td>
                            <td>6:00am-6:00pm</td>
                            <td>6:00pm-6:00am</td>
                          </tr>
                          <tr>
                            <td>An Other</td>
                            <td>March 14-15</td>
                            <td>6:00am-6:00pm</td>
                            <td>6:00pm-6:00am</td>
                          </tr>
                           <tr>
                            <td>Boostability</td>
                            <td>March 14-15</td>
                            <td>6:00am-6:00pm</td>
                            <td>6:00pm-6:00am</td>
                          </tr>
                          <tr>
                            <td>Circles.Life</td>
                            <td>March 14-15</td>
                            <td>6:00am-6:00pm</td>
                            <td>6:00pm-6:00am</td>
                          </tr>
                          <tr>
                            <td>DMOPC</td>
                            <td>March 14-15</td>
                            <td>6:00am-6:00pm</td>
                            <td>6:00pm-6:00am</td>
                          </tr>
                         
                          <tr>
                            <td>EDTraining</td>
                            <td>March 14-15</td>
                            <td>6:00am-6:00pm</td>
                            <td>6:00pm-6:00am</td>
                          </tr>
                          <tr>
                            <td>Mous</td>
                            <td>March 14-15</td>
                            <td>6:00am-6:00pm</td>
                            <td>6:00pm-6:00am</td>
                          </tr>
                          <tr>
                            <td>SheerID</td>
                            <td>March 14-15</td>
                            <td>6:00am-6:00pm</td>
                            <td>6:00pm-6:00am</td>
                          </tr>
                          <tr>
                            <td>SKUVantage</td>
                            <td>March 14-15</td>
                            <td>6:00am-6:00pm</td>
                            <td>6:00pm-6:00am</td>
                          </tr>
                          <tr>
                            <td>TurnTo</td>
                            <td>March 14-15</td>
                            <td>6:00am-6:00pm</td>
                            <td>6:00pm-6:00am</td>
                          </tr>
                          <tr>
                            <td>Zenefits and Digicast</td>
                            <td>March 14-15</td>
                            <td>6:00am-6:00pm</td>
                            <td>6:00pm-6:00am</td>
                          </tr>
                          <tr>
                            <td>Bird</td>
                            <td>March 18-22</td>
                            <td>6:00am-6:00pm</td>
                            <td>6:00pm-6:00am</td>
                          </tr>
                          <tr>
                            <td>Postmates</td>
                            <td>March 18-22</td>
                            <td>6:00am-6:00pm</td>
                            <td>6:00pm-6:00am</td>
                          </tr>

      



                        </table>

                      </div>

                      



                      <div class="item  text-center" >
                        
                         <img src="./storage/uploads/teams3.jpg" style="z-index: 2" width="100%" /><br/><br/>
                         

                          <p style="padding: 5px 30px; margin-bottom: 0px;" class="text-left">
                          Get to know more about your <a href="{{action('CampaignController@index')}}"><strong>Open Access family!</strong></a><br/><br/> Check out our <a class="text-orange" href="{{action('CampaignController@index')}}">programs</a> and learn more about the driving force behind our campaigns.</p>

                           <p style="padding: 5px 30px; margin-bottom: 0px; font-size: x-small;" class="text-left"><br/><br/>
                            <i class="fa fa-info-circle text-primary"></i> Tip: Make sure the page you're viewing is updated. Press <strong>CTRL + Shift + R </strong> to do a hard refresh and clear your browser's cache.
                          </p>

                          
                      </div>

                      <div class="item text-center" >
                        
                         <p style="padding: 5px 30px; margin-bottom: 0px"><h3 class="text-orange"><span style="font-size: smaller;" class="text-primary"> Let's all get our bods</span> Summer-ready!</h3>And the best part is:<br/>it's <span style="font-size: x-large;"> FUN and FREE!!!</span><br/><br/>
                          <img src="./storage/uploads/kick1.jpg" style="z-index: 2" /><br/><br/>

                          Here's the schedule for this week:<br/><br/>

                          <strong style="font-size:larger"><span class="text-primary"> ZUMBA:</span> 
                            <br/><span class="text-danger">March 20, 2019 (Wed) – 7:00 PM</span><br/></strong><br/>

                          <strong style="font-size:larger"><span class="text-primary"> AERO KICKBOXING:</span> 
                            <BR/><span class="text-danger">March 21, 2019 (Thu) – 6:00 PM</span><br/></strong><br/> 

                           <strong style="font-size:larger"><span class="text-primary"> YOGA:</span> 
                            <br/><span class="text-danger">March 22, 2019 (Fri) – 7:00 PM</span><br/></strong>




                          



                          <br/><br/>
                           <strong>Venue:</strong><br/>

                            OPEN ACCESS BPO G2 OFFICE<br/>

                            11F Glorietta 2 Corporate Center,<br/>

                            West. St., Ayala Center, Makati City<br/><br/>

                            <img src="./storage/uploads/oam-location.jpg" style="z-index: 2" width="100%" />
                            <br/><br/>



                              Sign up here: <a href="http://172.17.0.2/coffeebreak/event/5494/" target="_blank">http://172.17.0.2/coffeebreak/event/5494/</a><br/><br/>
                              View our <a class="text-danger" href="./gallery?a=5"><i class="fa fa-picture-o"></i> Gallery</a> for more<br/> #WeSpeakYourLanguage #LetsGetPhysical #OAforWellness

                           
                            <div style="padding:10px; position: absolute;bottom: 0px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                  <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAforWellness #LetsGetPhysical #Zumba</small></a></div> <br/><br/><br/><br/>
                      </div>


                

                      

                     <div class="item text-center" >
                        
                         <p style="padding: 5px 30px; margin-bottom: 0px"><h3 class="text-orange"><span style="font-size: smaller;" class="text-primary"> Let's all get our bods</span> Summer-ready!</h3>And the best part is:<br/>it's <span style="font-size: x-large;"> FUN and FREE!!!</span><br/><br/>
                          <img src="./storage/uploads/letsgetphysical-11.jpg" style="z-index: 2" /><br/><br/>

                          Here's the schedule for this week:<br/><br/>

                          <strong style="font-size:larger"><span class="text-primary"> ZUMBA:</span> 
                            <br/><span class="text-danger">March 20, 2019 (Wed) – 7:00 PM</span><br/></strong><br/>

                          <strong style="font-size:larger"><span class="text-primary"> AERO KICKBOXING:</span> 
                            <BR/><span class="text-danger">March 21, 2019 (Thu) – 6:00 PM</span><br/></strong><br/> 

                           <strong style="font-size:larger"><span class="text-primary"> YOGA:</span> 
                            <br/><span class="text-danger">March 22, 2019 (Fri) – 7:00 PM</span><br/></strong>




                          



                          <br/><br/>
                           <strong>Venue:</strong><br/>

                            OPEN ACCESS BPO G2 OFFICE<br/>

                            11F Glorietta 2 Corporate Center,<br/>

                            West. St., Ayala Center, Makati City<br/><br/>

                            <img src="./storage/uploads/oam-location.jpg" style="z-index: 2" width="100%" />
                            <br/><br/>



                              Sign up here: <a href="http://172.17.0.2/coffeebreak/event/5494/" target="_blank">http://172.17.0.2/coffeebreak/event/5494/</a><br/><br/>
                              View our <a class="text-danger" href="./gallery?a=5"><i class="fa fa-picture-o"></i> Gallery</a> for more<br/> #WeSpeakYourLanguage #LetsGetPhysical #OAforWellness

                           
                            <div style="padding:10px; position: absolute;bottom: 0px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                  <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAforWellness #LetsGetPhysical #Zumba</small></a></div> <br/><br/><br/><br/>
                      </div>

                      
                      <!-- CATRIONA -->
                      <div class="item text-center" >
                        <img src="./storage/uploads/cat-31.jpg" style="z-index: 2" />
                        <p style="padding: 30px; margin-bottom: 0px"><strong> Welcome home, <span class="text-primary"> MissUniverse 2018 Catriona Gray!</span></strong><br/>Open Access BPO employees cheered along with other supporters as the beauty queen's float passed through Ayala Avenue during her grand homecoming motorcade last Feb 21.<br/><br/>

                          More in our <a class="text-danger" href="./gallery?a=6"><i class="fa fa-picture-o"></i> Gallery page</a> <br/><br/>#WeSpeakYourLanguage #OAonMUHomecoming #CatrionaHomecoming<br/><br/>



                   


                           
                            <div style="padding:10px; position: absolute;bottom: 0px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                  <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #WeSpeakYourLanguage #OAonMUHomecoming #CatrionaHomecoming</small></a></div> <br/><br/><br/><br/>

                      </div>

                      <div class="item text-center" >
                        <img src="./storage/uploads/cat-5.jpg" style="z-index: 2" />
                        <p style="padding: 30px; margin-bottom: 0px"><strong> Welcome home, <span class="text-primary"> MissUniverse 2018 Catriona Gray!</span></strong><br/>Open Access BPO employees cheered along with other supporters as the beauty queen's float passed through Ayala Avenue during her grand homecoming motorcade last Feb 21.<br/><br/>

                          More in our <a class="text-danger" href="./gallery?a=6"><i class="fa fa-picture-o"></i> Gallery page</a> <br/><br/>#WeSpeakYourLanguage #OAonMUHomecoming #CatrionaHomecoming<br/><br/>



                   


                           
                            <div style="padding:10px; position: absolute;bottom: 0px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                  <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #WeSpeakYourLanguage #OAonMUHomecoming #CatrionaHomecoming</small></a></div> <br/><br/><br/><br/>

                      </div>
                      <div class="item text-center" >
                        <img src="./storage/uploads/cat-44.jpg" style="z-index: 2" />
                        <p style="padding: 30px; margin-bottom: 0px"><strong> Welcome home, <span class="text-primary"> MissUniverse 2018 Catriona Gray!</span></strong><br/>Open Access BPO employees cheered along with other supporters as the beauty queen's float passed through Ayala Avenue during her grand homecoming motorcade last Feb 21.<br/><br/>

                          More in our <a class="text-danger" href="./gallery?a=6"><i class="fa fa-picture-o"></i> Gallery page</a> <br/><br/>#WeSpeakYourLanguage #OAonMUHomecoming #CatrionaHomecoming<br/><br/>



                   


                           
                            <div style="padding:10px; position: absolute;bottom: 0px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                  <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #WeSpeakYourLanguage #OAonMUHomecoming #CatrionaHomecoming</small></a></div> <br/><br/><br/><br/>

                      </div>
                      <!-- CATRIONA -->



                      <div class="item  text-center" >
                        
                         <p style="padding: 5px 30px; margin-bottom: 0px"><h3 class="text-orange"><span style="font-size: smaller;" class="text-primary"> Let's all get our bods</span> Summer-ready!</h3>And the best part is:<br/>it's <span style="font-size: x-large;"> FUN and FREE!!!</span><br/><br/>
                          <img src="./storage/uploads/letsgetphysical-31.jpg" style="z-index: 2" /><br/><br/>

                           Here's the schedule for this week:<br/><br/>

                          <strong style="font-size:larger"><span class="text-primary"> ZUMBA:</span> 
                            <br/><span class="text-danger">March 20, 2019 (Wed) – 7:00 PM</span><br/></strong><br/>

                          <strong style="font-size:larger"><span class="text-primary"> AERO KICKBOXING:</span> 
                            <BR/><span class="text-danger">March 21, 2019 (Thu) – 6:00 PM</span><br/></strong><br/> 

                           <strong style="font-size:larger"><span class="text-primary"> YOGA:</span> 
                            <br/><span class="text-danger">March 22, 2019 (Fri) – 7:00 PM</span><br/></strong>

                          <br/><br/>
                           <strong>Venue:</strong><br/>

                            OPEN ACCESS BPO G2 OFFICE<br/>

                            11F Glorietta 2 Corporate Center,<br/>

                            West. St., Ayala Center, Makati City<br/><br/>

                            <img src="./storage/uploads/oam-location.jpg" style="z-index: 2" width="100%" />
                            <br/><br/>



                              Sign up here: <a href="http://172.17.0.2/coffeebreak/event/5494/" target="_blank">http://172.17.0.2/coffeebreak/event/5494/</a><br/><br/>
                              View our <a class="text-danger" href="./gallery?a=5"><i class="fa fa-picture-o"></i> Gallery</a> for more<br/> #WeSpeakYourLanguage #LetsGetPhysical #OAforWellness

                           
                            <div style="padding:10px; position: absolute;bottom: 0px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                  <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAforWellness #LetsGetPhysical #Zumba</small></a></div> <br/><br/><br/><br/>
                      </div>

                     

                      <div class="item text-center" ><h2 class="text-orange">Hellos &amp; Grubs</h2> <br/>
                        <img src="./storage/uploads/grubs2_1.jpg" style="z-index: 2" />
                            
                            <p style="padding: 30px; margin-bottom: 0px">Dinner with our lovely group of Open Access BPO employees and executives was well spent! Indeed, sharing meals draws people together. We couldn't wait to meet our next batch on our next Hellos & Grubs!<br/><br/>
                              Wishing our HR Director, Ms. Emelda Perez, a happy birthday too! It's a pleasure having you at Open Access BPO. 🎂 <br/><br/>#WeSpeakYourLanguage #HellosAndGrubs

                           
                            <div style="padding:10px; position: absolute;bottom: 0px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                  <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #HellosAndGrubs</small></a></div> <br/><br/><br/><br/>
                      </div> 

                      <div class="item text-center" ><h2 class="text-orange">Hellos &amp; Grubs</h2><br/>
                        <img src="./storage/uploads/grubs2_2.jpg" style="z-index: 2" /> 
                            
                            <p style="padding: 30px; margin-bottom: 0px">Dinner with our lovely group of Open Access BPO employees and executives was well spent! Indeed, sharing meals draws people together. We couldn't wait to meet our next batch on our next Hellos & Grubs!<br/><br/>
                              Wishing our HR Director, Ms. Emelda Perez, a happy birthday too! It's a pleasure having you at Open Access BPO. 🎂 <br/><br/>#WeSpeakYourLanguage #HellosAndGrubs

                           
                            <div style="padding:10px; position: absolute;bottom: 0px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                  <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #HellosAndGrubs</small></a></div> <br/><br/><br/><br/>
                      </div> 

                      <div class="item text-center" ><h2 class="text-orange">Hellos &amp; Grubs</h2> <br/>
                        <img src="./storage/uploads/grubs2_3.jpg" style="z-index: 2" />
                            
                            <p style="padding: 30px; margin-bottom: 0px">Dinner with our lovely group of Open Access BPO employees and executives was well spent! Indeed, sharing meals draws people together. We couldn't wait to meet our next batch on our next Hellos & Grubs!<br/><br/>
                              Wishing our HR Director, Ms. Emelda Perez, a happy birthday too! It's a pleasure having you at Open Access BPO. 🎂 <br/><br/>#WeSpeakYourLanguage #HellosAndGrubs

                           
                            <div style="padding:10px; position: absolute;bottom: 0px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                  <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #HellosAndGrubs</small></a></div> <br/><br/><br/><br/>
                      </div> 

                      <div class="item text-center" >
                        <img src="./storage/uploads/zumba2_3.jpg" style="z-index: 2" />
                        <p style="padding: 30px; margin-bottom: 0px">The ultimate fitness squad goal: doing #Zumba together! Here's the schedule for next week:<br/><br/>

                          <strong style="font-size:larger"><span class="text-primary"> ZUMBA:</span> 
                            <br/><span class="text-danger">March 13, 2019 (Wed) – 7:00 PM</span><br/></strong><br/>

                          <!--  <strong style="font-size:larger"><span class="text-primary"> AERO KICKBOXING:</span> 
                            <BR/><span class="text-danger">March 8, 2019 (Friday) – 5:30 PM</span><br/></strong><br/> -->

                           <strong style="font-size:larger"><span class="text-primary"> YOGA:</span> 
                            <br/><span class="text-danger">March 15, 2019 (Fri) – 7:00 PM</span><br/></strong>


                          <br/><br/>
                           <strong>Venue:</strong><br/>

                            OPEN ACCESS BPO G2 OFFICE<br/>

                            11F Glorietta 2 Corporate Center,<br/>

                            West. St., Ayala Center, Makati City<br/><br/>

                            <img src="./storage/uploads/oam-location.jpg" style="z-index: 2" width="100%" />





                            Nobody has to be a pro for this, we’re all in this together! Kindly choose the class that you prefer. The registration is now open and will end on March 6 (Wed) at exactly 1:00 PM. Final list of participants will be notified via email and SMS the same day.<br/><br/>



                            You may come before/after your shift, during your break times, or on your rest day for the free classes. However, should this coincide with your work schedule, kindly approach any of the Workforce team ASAP to check if any work schedule changes may be accommodated. Shower rooms are also available in our G2 site so you can freshen up after the class!For those who will sign up for the Yogalates, yoga mats will be provided.<br/><br/>



                              Sign up here: <a href="http://172.17.0.2/coffeebreak/event/5494/" target="_blank">http://172.17.0.2/coffeebreak/event/5494/</a><br/><br/>
                              View our <a class="text-danger" href="./gallery?a=5"><i class="fa fa-picture-o"></i> Gallery</a> for more<br/> #WeSpeakYourLanguage #LetsGetPhysical #OAforWellness

                            <div style="padding:10px; position: absolute;bottom: 0px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                  <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAforWellness #LetsGetPhysical #Zumba</small></a></div> <br/><br/><br/><br/>
                      </div>




                      


                      <div class="item text-center" >
                              
                              <img src="./storage/uploads/hello_grubs1.jpg" style="z-index: 2" />
                              <h2 class="text-orange">Hellos &amp; Grubs</h2> <br/>
                              <p style="padding:20px">Our 2019 kicked off with a splendid breakfast filled with fun conversations shared by the Open Access BPO executives and employees -- what a great way to start the year! 
                                Thanks to everyone who joined us earlier on our first <strong class="text-orange">Hellos &amp; Grubs</strong> session!<br/><br/>We can't wait to meet the next batch in a few days!</p>
                                <span class="text-danger">If you won’t get picked for this month, no need to feel bad! We intend to meet <span style="font-size:large">everyone</span> this year as we aim for this to be a monthly activity.</span>
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


                           

                            

                          

                            <?php /* 



                            <div class="item text-center">

                        

                          <img src="./storage/uploads/hobbyist_1.jpg" style="z-index: 2" />
                          
                          <p style="padding: 30px; margin-bottom: 0px"><strong>#FeaturedHobbyist:</strong> <a href="./user/531" target="_blank">Patrick Ocampo</a> is a Team Leader from one of our amazing campaigns. He loves to spend his time off work with activities that stimulate the body and brain.<br/><br/>

                          <strong class="text-primary">@idrewwithacamera: </strong>
                          January is Hobby Month! Activities that stimulate the body and brain: improv theater, badminton, console gaming, puzzles, reading! </p>
                          <div style="padding:10px; position: absolute;bottom: 0px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAonHobbyMonth #HobbyMonth</small></a></div> <br/><br/><br/><br/>
                       </div>

                       <div class="item text-center">
                          <img src="./storage/uploads/hobbyist_2.jpg" style="z-index: 2" />
                            
                            <p style="padding: 30px; margin-bottom: 0px"><strong>#FeaturedHobbyist:</strong> <a href="./user/531" target="_blank">Patrick Ocampo</a> is a Team Leader from one of our amazing campaigns. He loves to spend his time off work with activities that stimulate the body and brain.<br/><br/>

                            <strong class="text-primary">@idrewwithacamera: </strong>
                            January is Hobby Month! Activities that stimulate the body and brain: improv theater, badminton, console gaming, puzzles, reading! </p>
                            <div style="padding:10px; position: absolute;bottom: 0px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                  <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAonHobbyMonth #HobbyMonth</small></a></div> <br/><br/><br/><br/>
                       </div>

                      <div class="item text-center">
                          <img src="./storage/uploads/hobbyist_3.jpg" style="z-index: 2" />
                            
                            <p style="padding: 30px; margin-bottom: 0px"><strong>#FeaturedHobbyist:</strong> <a href="./user/531" target="_blank">Patrick Ocampo</a> is a Team Leader from one of our amazing campaigns. He loves to spend his time off work with activities that stimulate the body and brain.<br/><br/>

                            <strong class="text-primary">@idrewwithacamera: </strong>
                            January is Hobby Month! Activities that stimulate the body and brain: improv theater, badminton, console gaming, puzzles, reading! </p>
                            <div style="padding:10px; position: absolute;bottom: 0px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                  <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAonHobbyMonth #HobbyMonth</small></a></div> <br/><br/><br/><br/>
                      </div>

                      <div class="item text-center">
                          <img src="./storage/uploads/hobbyist_4.jpg" style="z-index: 2" />
                            
                            <p style="padding: 30px; margin-bottom: 0px"><strong>#FeaturedHobbyist:</strong> <a href="./user/1958" target="_blank">Riech Heherson de Vera</a>  is a Customer Care Associate from one of our new campaigns. Off work, he spends his time on theatre acting or by creating astounding makeup looks.<br/><br/>

                            <strong class="text-primary"> @itsmesondevera:</strong>
                           Creating beauty as a makeup artist and portraying different characters as an actor gives pleasure to my mind, body and soul. @lsdvglamteam @itsmesondevera </p>
                            <div style="padding:10px; position: absolute;bottom: 0px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                  <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAonHobbyMonth #HobbyMonth</small></a></div> <br/><br/><br/><br/>
                      </div>

                      <div class="item text-center">
                          <img src="./storage/uploads/hobbyist_5.jpg" style="z-index: 2" />
                            
                            <p style="padding: 30px; margin-bottom: 0px"><strong>#FeaturedHobbyist:</strong> <a href="./user/1958" target="_blank">Riech Heherson de Vera</a>  is a Customer Care Associate from one of our new campaigns. Off work, he spends his time on theatre acting or by creating astounding makeup looks.<br/><br/>

                            <strong class="text-primary"> @itsmesondevera:</strong>
                           Creating beauty as a makeup artist and portraying different characters as an actor gives pleasure to my mind, body and soul. @lsdvglamteam @itsmesondevera </p>
                            <div style="padding:10px; position: absolute;bottom: 0px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                  <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAonHobbyMonth #HobbyMonth</small></a></div> <br/><br/><br/><br/>
                      </div>

                      <div class="item text-center">
                          <img src="./storage/uploads/hobbyist_6.jpg" style="z-index: 2" />
                            
                            <p style="padding: 30px; margin-bottom: 0px"><strong>#FeaturedHobbyist:</strong> <a href="./user/1958" target="_blank">Riech Heherson de Vera</a>  is a Customer Care Associate from one of our new campaigns. Off work, he spends his time on theatre acting or by creating astounding makeup looks.<br/><br/>

                            <strong class="text-primary"> @itsmesondevera:</strong>
                           Creating beauty as a makeup artist and portraying different characters as an actor gives pleasure to my mind, body and soul. @lsdvglamteam @itsmesondevera </p>
                            <div style="padding:10px; position: absolute;bottom: 0px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                  <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAonHobbyMonth #HobbyMonth</small></a></div> <br/><br/><br/><br/>
                      </div>

                      <div class="item text-center">
                          <img src="./storage/uploads/hobbyist_7.jpg" style="z-index: 2" />
                            
                            <p style="padding: 30px; margin-bottom: 0px"><strong>#FeaturedHobbyist:</strong> <a href="./user/62" target="_blank">Jeffrey Aspacio</a> is @openaccessbpo's Senior SEO Strategist. Aside from having an active lifestyle, he also loves to spend his time off work with his family and by playing musical instruments. <br/><br/>

                            <strong class="text-primary">@jaspacio: </strong>
                           Top 5 activities that I enjoy, when I'm not wearing my hat 🎩 as Sr SEO Strategist for @openaccessbpo:<br/>

                            1. Spending time with #family 👪 <br/>
                            2. Running 🏃 (📸 @icanyoucanofficial)<br/>
                            3. Playing the #drums <br/>
                            4. Playing the 🎸 #guitar <br/>
                            5. Playing #football <br/>

                            I ❤ these because they make me feel alive.  </p>
                            <div style="padding:10px; position: absolute;bottom: 0px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                  <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAonHobbyMonth #HobbyMonth</small></a></div> <br/><br/><br/><br/>
                      </div>

                      <div class="item text-center">
                          <img src="./storage/uploads/hobbyist_8.jpg" style="z-index: 2" />
                            
                            <p style="padding: 30px; margin-bottom: 0px"><strong>#FeaturedHobbyist:</strong> <a href="./user/62" target="_blank">Jeffrey Aspacio</a> is @openaccessbpo's Senior SEO Strategist. Aside from having an active lifestyle, he also loves to spend his time off work with his family and by playing musical instruments. <br/><br/>

                            <strong class="text-primary">@jaspacio: </strong>
                           Top 5 activities that I enjoy, when I'm not wearing my hat 🎩 as Sr SEO Strategist for @openaccessbpo:<br/>

                            1. Spending time with #family 👪 <br/>
                            2. Running 🏃 (📸 @icanyoucanofficial)<br/>
                            3. Playing the #drums <br/>
                            4. Playing the 🎸 #guitar <br/>
                            5. Playing #football <br/>

                            I ❤ these because they make me feel alive.  </p>
                            <div style="padding:10px; position: absolute;bottom: 0px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                  <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAonHobbyMonth #HobbyMonth</small></a></div> <br/><br/><br/><br/>
                      </div>

                      <div class="item text-center">
                          <img src="./storage/uploads/hobbyist_9.jpg" style="z-index: 2" />
                            
                            <p style="padding: 30px; margin-bottom: 0px"><strong>#FeaturedHobbyist:</strong> <a href="./user/62" target="_blank">Jeffrey Aspacio</a> is @openaccessbpo's Senior SEO Strategist. Aside from having an active lifestyle, he also loves to spend his time off work with his family and by playing musical instruments. <br/><br/>

                            <strong class="text-primary">@jaspacio: </strong>
                           Top 5 activities that I enjoy, when I'm not wearing my hat 🎩 as Sr SEO Strategist for @openaccessbpo:<br/>

                            1. Spending time with #family 👪 <br/>
                            2. Running 🏃 (📸 @icanyoucanofficial)<br/>
                            3. Playing the #drums <br/>
                            4. Playing the 🎸 #guitar <br/>
                            5. Playing #football <br/>

                            I ❤ these because they make me feel alive.  </p>
                            <div style="padding:10px; position: absolute;bottom: 0px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                  <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAonHobbyMonth #HobbyMonth</small></a></div> <br/><br/><br/><br/>
                      </div>

                      <div class="item text-center">
                          <img src="./storage/uploads/hobbyist_10.jpg" style="z-index: 2" />
                            
                            <p style="padding: 30px; margin-bottom: 0px"><strong>#FeaturedHobbyist:</strong> <a href="./user/534" target="_blank">Luis Anthony Oliveros</a> is @openaccessbpo's lead Digital Content Specialist. During his spare time, he likes to do things he enjoys most such as drawing and listening to podcasts. <br/><br/>

                            <strong class="text-primary">@greyweed: </strong>
                           
                              Sharing my hobbies because January is apparently #HobbyMonth [tagged by @JAspacio]<br/>

                              1. #DRAWING. Been doing #pen and #ink #drawings since grade school and added some digital coloring during college. The ones I've managed to post here are tagged #DesksideDoodles.<br/>

                              2. #IMPROV. I've wanted to get into improv since high school, but only took the plunge last year.<br/>

                              3. GEOLOCATION GAMES. Playing location-based AR games like @PokemonGoApp and @Ingress Prime.<br/>

                              4. #PODCASTS. I listen to nearly 30 comedy, horror, geek-themed, and pop culture podcasts.<br/>

                              5. READING. Love sci-fi- and mystery-themed books and Filipino graphic novels. Sadly, I've been busy recently with my other hobbies, but I plan on denting my unread book pile this year.<br/>

                              6. SCULPTING. Another hobby that's been in the backburner for months now: molding things out of polymer clay. </p>
                            <div style="padding:10px; position: absolute;bottom: 0px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                  <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAonHobbyMonth #HobbyMonth</small></a></div> <br/><br/><br/><br/>
                      </div>

                      <div class="item text-center">
                          <img src="./storage/uploads/hobbyist_11.jpg" style="z-index: 2" />
                            
                            <p style="padding: 30px; margin-bottom: 0px"><strong>#FeaturedHobbyist:</strong> <a href="./user/534" target="_blank">Luis Anthony Oliveros</a>  is @openaccessbpo's lead Digital Content Specialist. During his spare time, he likes to do things he enjoys most such as drawing and listening to podcasts. <br/><br/>

                            <strong class="text-primary">@greyweed: </strong>
                           
                              Sharing my hobbies because January is apparently #HobbyMonth [tagged by @JAspacio]<br/>

                              1. #DRAWING. Been doing #pen and #ink #drawings since grade school and added some digital coloring during college. The ones I've managed to post here are tagged #DesksideDoodles.<br/>

                              2. #IMPROV. I've wanted to get into improv since high school, but only took the plunge last year.<br/>

                              3. GEOLOCATION GAMES. Playing location-based AR games like @PokemonGoApp and @Ingress Prime.<br/>

                              4. #PODCASTS. I listen to nearly 30 comedy, horror, geek-themed, and pop culture podcasts.<br/>

                              5. READING. Love sci-fi- and mystery-themed books and Filipino graphic novels. Sadly, I've been busy recently with my other hobbies, but I plan on denting my unread book pile this year.<br/>

                              6. SCULPTING. Another hobby that's been in the backburner for months now: molding things out of polymer clay. </p>
                            <div style="padding:10px; position: absolute;bottom: 0px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                  <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAonHobbyMonth #HobbyMonth</small></a></div> <br/><br/><br/><br/>
                      </div>

                      <div class="item text-center">
                          <img src="./storage/uploads/hobbyist_12.jpg" style="z-index: 2" />
                            
                            <p style="padding: 30px; margin-bottom: 0px"><strong>#FeaturedHobbyist:</strong> <a href="./user/534" target="_blank">Luis Anthony Oliveros</a>  is @openaccessbpo's lead Digital Content Specialist. During his spare time, he likes to do things he enjoys most such as drawing and listening to podcasts. <br/><br/>

                            <strong class="text-primary">@greyweed: </strong>
                           
                              Sharing my hobbies because January is apparently #HobbyMonth [tagged by @JAspacio]<br/>

                              1. #DRAWING. Been doing #pen and #ink #drawings since grade school and added some digital coloring during college. The ones I've managed to post here are tagged #DesksideDoodles.<br/>

                              2. #IMPROV. I've wanted to get into improv since high school, but only took the plunge last year.<br/>

                              3. GEOLOCATION GAMES. Playing location-based AR games like @PokemonGoApp and @Ingress Prime.<br/>

                              4. #PODCASTS. I listen to nearly 30 comedy, horror, geek-themed, and pop culture podcasts.<br/>

                              5. READING. Love sci-fi- and mystery-themed books and Filipino graphic novels. Sadly, I've been busy recently with my other hobbies, but I plan on denting my unread book pile this year.<br/>

                              6. SCULPTING. Another hobby that's been in the backburner for months now: molding things out of polymer clay. </p>
                            <div style="padding:10px; position: absolute;bottom: 0px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                  <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAonHobbyMonth #HobbyMonth</small></a></div> <br/><br/><br/><br/>
                      </div>

                      <div class="item text-center">
                          <img src="./storage/uploads/hobbyist_13.jpg" style="z-index: 2" />
                            
                            <p style="padding: 30px; margin-bottom: 0px"><strong>#FeaturedHobbyist:</strong> <a href="./user/1717" target="_blank">Wendy Pilar </a>  is @openaccessbpo's Digital Content Specialist. She loves to take photographs, paint, and watch drama series on her leisure time.  <br/><br/>

                            <strong class="text-primary"> @wndunne:  </strong><br/>
                              Happy Hobby Month! 
                              I have three hobbies I usually spend my time off work on: photography, watercolor painting, and watching Chinese/Korean/Thai drama series. 
                              Among the three, photography brings me joy the most. It's where I also feel most at peace. </p>
                            <div style="padding:10px; position: absolute;bottom: 0px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                  <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAonHobbyMonth #HobbyMonth</small></a></div> <br/><br/><br/><br/>
                      </div>

                       <div class="item text-center">
                          <img src="./storage/uploads/hobbyist_14.jpg" style="z-index: 2" />
                            
                            <p style="padding: 30px; margin-bottom: 0px"><strong>#FeaturedHobbyist:</strong> <a href="./user/1717" target="_blank">Wendy Pilar </a>  is @openaccessbpo's Digital Content Specialist. She loves to take photographs, paint, and watch drama series on her leisure time.  <br/><br/>

                            <strong class="text-primary"> @wndunne:  </strong><br/>
                              Happy Hobby Month! 
                              I have three hobbies I usually spend my time off work on: photography, watercolor painting, and watching Chinese/Korean/Thai drama series. 
                              Among the three, photography brings me joy the most. It's where I also feel most at peace. </p>
                            <div style="padding:10px; position: absolute;bottom: 0px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                  <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAonHobbyMonth #HobbyMonth</small></a></div> <br/><br/><br/><br/>
                      </div>

                       <div class="item text-center">
                          <img src="./storage/uploads/hobbyist_15.jpg" style="z-index: 2" />
                            
                            <p style="padding: 30px; margin-bottom: 0px"><strong>#FeaturedHobbyist:</strong> <a href="./user/1717" target="_blank">Wendy Pilar </a>  is @openaccessbpo's Digital Content Specialist. She loves to take photographs, paint, and watch drama series on her leisure time.  <br/><br/>

                            <strong class="text-primary"> @wndunne:  </strong><br/>
                              Happy Hobby Month! 
                              I have three hobbies I usually spend my time off work on: photography, watercolor painting, and watching Chinese/Korean/Thai drama series. 
                              Among the three, photography brings me joy the most. It's where I also feel most at peace. </p>
                            <div style="padding:10px; position: absolute;bottom: 0px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                  <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAonHobbyMonth #HobbyMonth</small></a></div> <br/><br/><br/><br/>
                      </div>

                            <div class="item text-center" >
                        <img src="./storage/uploads/lovemonth-1.jpg" style="z-index: 2" />
                        <p style="padding: 30px; margin-bottom: 0px">This <strong>#LoveMonth</strong>, we are celebrating #SelfLove through music. The <em>"Love, Me" </em> playlist is a letter to the people out there who have struggled—or continue to struggle—loving their true selves.<br/><br/>

We understand your doubts and this is our message: Embrace who you are, with all your strengths and weaknesses. Everyone is unique and beautiful and there's no reason for you to change just to fit in. <br/>
Be empowered by <strong> @colbiecaillat, @natashabedingfield, @ladygaga, @florenceandthemachine, @pink,</strong> and many more in our "Love, Me" playlist on Spotify: <a style="font-size: larger" href="http://tinyurl.com/y33ctdx5" target="_blank">tinyurl.com/y33ctdx5</a> 💞<br/><br/>

#WeSpeakYourLanguage #OAonSpotify #LoveMonth2019 #ValentinesDay #LoveYourself #LifeLesson #SelfWorth

                           
                            <div style="padding:10px; position: absolute;bottom: 0px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                  <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAonSpotify #LoveMonth2019 #ValentinesDay #LoveYourself #LifeLesson #SelfWorth</small></a></div> <br/><br/><br/><br/>
                      </div>
                      
                      <div class="item text-center" >
                        <img src="./storage/uploads/lovemonth-2.jpg" style="z-index: 2" />
                        <p style="padding: 30px; margin-bottom: 0px">This <strong>#LoveMonth</strong>, we are celebrating #SelfLove through music. The <em>"Love, Me" </em> playlist is a letter to the people out there who have struggled—or continue to struggle—loving their true selves.<br/><br/>

                        We understand your doubts and this is our message: Embrace who you are, with all your strengths and weaknesses. Everyone is unique and beautiful and there's no reason for you to change just to fit in. <br/>
                        Be empowered by <strong> @colbiecaillat, @natashabedingfield, @ladygaga, @florenceandthemachine, @pink,</strong> and many more in our "Love, Me" playlist on Spotify: <a style="font-size: larger" href="http://tinyurl.com/y33ctdx5" target="_blank">tinyurl.com/y33ctdx5</a> 💞<br/><br/>

                        #WeSpeakYourLanguage #OAonSpotify #LoveMonth2019 #ValentinesDay #LoveYourself #LifeLesson #SelfWorth

                           
                            <div style="padding:10px; position: absolute;bottom: 0px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                  <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAonSpotify #LoveMonth2019 #ValentinesDay #LoveYourself #LifeLesson #SelfWorth</small></a></div> <br/><br/><br/><br/>
                      </div>

                      <div class="item text-center" >
                        <img src="./storage/uploads/lovemonth-3.jpg" style="z-index: 2" />
                        <p style="padding: 30px; margin-bottom: 0px">This <strong>#LoveMonth</strong>, we are celebrating #SelfLove through music. The <em>"Love, Me" </em> playlist is a letter to the people out there who have struggled—or continue to struggle—loving their true selves.<br/><br/>

                          We understand your doubts and this is our message: Embrace who you are, with all your strengths and weaknesses. Everyone is unique and beautiful and there's no reason for you to change just to fit in. <br/>
                          Be empowered by <strong> @colbiecaillat, @natashabedingfield, @ladygaga, @florenceandthemachine, @pink,</strong> and many more in our "Love, Me" playlist on Spotify: <a  style="font-size: larger" href="http://tinyurl.com/y33ctdx5" target="_blank">tinyurl.com/y33ctdx5</a> 💞<br/><br/>

                          #WeSpeakYourLanguage #OAonSpotify #LoveMonth2019 #ValentinesDay #LoveYourself #LifeLesson #SelfWorth

                           
                            <div style="padding:10px; position: absolute;bottom: 0px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                  <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAonSpotify #LoveMonth2019 #ValentinesDay #LoveYourself #LifeLesson #SelfWorth</small></a></div> <br/><br/><br/><br/>
                      </div>

                      <div class="item text-center" >
                        <img src="./storage/uploads/lovemonth-4.jpg" style="z-index: 2" />
                        <p style="padding: 30px; margin-bottom: 0px">This <strong>#LoveMonth</strong>, we are celebrating #SelfLove through music. The <em>"Love, Me" </em> playlist is a letter to the people out there who have struggled—or continue to struggle—loving their true selves.<br/><br/>

We understand your doubts and this is our message: Embrace who you are, with all your strengths and weaknesses. Everyone is unique and beautiful and there's no reason for you to change just to fit in. <br/>
Be empowered by <strong> @colbiecaillat, @natashabedingfield, @ladygaga, @florenceandthemachine, @pink,</strong> and many more in our "Love, Me" playlist on Spotify: <a  style="font-size: larger"href="http://tinyurl.com/y33ctdx5" target="_blank">tinyurl.com/y33ctdx5</a> 💞<br/><br/>

#WeSpeakYourLanguage #OAonSpotify #LoveMonth2019 #ValentinesDay #LoveYourself #LifeLesson #SelfWorth

                           
                            <div style="padding:10px; position: absolute;bottom: 0px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                  <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAonSpotify #LoveMonth2019 #ValentinesDay #LoveYourself #LifeLesson #SelfWorth</small></a></div> <br/><br/><br/><br/>
                      </div>

                            <div class="item active text-center" >
                        <img src="./storage/uploads/motherlang_1.jpg" style="z-index: 2" />
                        <p style="padding: 30px; margin-bottom: 0px">Today, we're celebrating the beauty and importance of mother languages and we want to hear from you! <br/><br/>
                          Join the Speak Your Language contest and share your favorite word/phrase in your native tongue. Top 3 winners will win cash prizes! How to join:
                          <br/><br/>
                          1. Take a video of yourself stating your favorite word/phrase in your mother language <br/>
                          2. Translate the word/phrase in English and explain why it's your favorite in under 30 seconds <br/><br/>
                          3. Follow @openaccessbpo and use the following hashtags: <strong class="text-primary">#OAonIMLD #WeSpeakYourLanguage #OpenAccessBPO</strong>

                                                     
                                                      <div style="padding:10px; position: absolute;bottom: 0px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                                            <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> 
                          #WeSpeakYourLanguage #OAonIMLD #Multilingualism #Diversity</small></a></div> <br/><br/><br/><br/>
                      </div>

                            <div class="item text-center" >
                              
                            <img src="./storage/uploads/cny2019.jpg" style="z-index: 2" />
                            <h1 class="text-danger text-center">新年快乐!</h1>
                            <p style="padding: 30px; margin-bottom: 0px">To all our Chinese colleagues, families, and compatriots, wishing you happiness, good health, and prosperity in the Year of the Pig! <br/><br/>#WeSpeakYourLanguage #OAonChineseNewYear #CNY2019

                           
                            <div style="padding:10px; position: absolute;bottom: 0px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                  <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAonChineseNewYear #CNY2019</small></a></div> <br/><br/><br/><br/>
                       </div> 


                       <div class="item text-center" >
                              
                              <img src="./storage/uploads/letsgetphysical.jpg" style="z-index: 2" />
                              <p style="padding:20px">We are going to have free Zumba & Yogalates classes this week at the G2 office. The sign-up form has been sent out through Zimba already, and we are asking for your support to remind your respective teams to check out the email on Zimbra! For quick reference, here's the link: <br/><br/><a href="http://172.17.0.2/coffeebreak/event/5494/" target="_blank">http://172.17.0.2/coffeebreak/event/5494/</a><br/><br/>

                              The classes are free, and the employees may also use the shower rooms at the G2 office after each session. For those who will take the Yogalates class, the yoga mats will be provided.<br/><br/>

                              Employees may come before/after their shifts, during their break times, or rest days. However, should this coincide with their work schedule, they may approach any of the Workforce team ASAP to check if any work schedule changes may be accommodated.<br/><br/>

                              This week will be a test run and may be offered weekly, depending on the response this week.</p>
                        </div>

                        <div class="item text-center" >
                          <img src="./storage/uploads/zumba-2.jpg" style="z-index: 2" />
                          Thanks to everyone who joined us, we couldn't be more excited about our next sessions! For those who missed out, join us and let's all strive for a healthier lifestyle!
                              Here are the schedules:<br/><br/>
                              <strong style="font-size: larger">ZUMBA : </strong> Feb 13 <strong class="text-danger">(Wed) 7PM </strong><br/>
                              <strong  style="font-size: larger">YOGALATES : </strong> Feb 15 <strong class="text-danger">(Fri) 7PM</strong> <br/><br/>

                              Sign up here: <a href="http://172.17.0.2/coffeebreak/event/5494/" target="_blank">http://172.17.0.2/coffeebreak/event/5494/</a><br/><br/>
                          
                        </div>
                        <div class="item text-center" >
                          <img src="./storage/uploads/zumba.jpg" style="z-index: 2" />
                          Thanks to everyone who joined us, we couldn't be more excited about our next sessions! For those who missed out, join us and let's all strive for a healthier lifestyle!
                              Here are the schedules:<br/><br/>
                              <strong style="font-size: larger">ZUMBA : </strong> Feb 13 <strong class="text-danger">(Wed) 7PM </strong><br/>
                              <strong  style="font-size: larger">YOGALATES : </strong> Feb 15 <strong class="text-danger">(Fri) 7PM</strong> <br/><br/>

                              Sign up here: <a href="http://172.17.0.2/coffeebreak/event/5494/" target="_blank">http://172.17.0.2/coffeebreak/event/5494/</a><br/><br/>
                          
                        </div>


                            //-------cedula------
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
                            

                            <div class="item  text-center">
                             

                               <h4 class="text-primary">What: OAM Clinic Wellness Program</h4>
                                <img src="storage/uploads/wellness3.jpg" />

  
                              <p><strong>Where:</strong> <span class="text-danger" style="font-size: larger"> 5th Floor</span><br/>
                              <strong>When:</strong> <strong class="text-danger">February 13, 2019, Wednesday, 10AM- 7PM</strong><br/>
                              
                              <small>Should you have questions or concerns, please feel free to drop by the clinic or email our nurses at <strong>nurse@openaccessbpo.net / nurse@openaccessmarketing.com.</strong></small>
                              
                      </div>

                            <div class="item active text-center">
                        <img src="storage/uploads/myhealthally.png" />

                      </div>


                            <!-- RUNNERS -->
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

                            <!--HELLO GRUBS -->
                            <div class="item active text-center" >

                              <img src="./public/img/hellos_grubs.jpg" style="z-index: 2" />
                              <p style="padding:20px">Thank you for all your hard work and for being with the Open Access BPO family. This year is full of exciting things ahead as a lot of activities await you while we continue to grow.<br/><br/>



Before the month ends, <br/><strong class="text-orange">we invite you to a breakfast/ dinner</strong> <br/>to sit down and catch up with you, and just get to know you better. This is also a great opportunity to meet other employees from different programs/departments and our executive leaders as well!<br/><br/>



Your breakfast / dinner is on us! <br/>Here’s the schedule:<br/>

<strong>January 30, 2019 (Wednesday) – 7:00am-10:00am<br/>
               OR<br/>

January 31, 2019 (Thursday) – 7:00pm-10:00pm<br/></strong>


Venue: TBA (but will be walking distance from the Jaka Building office)<br/><br/>



Kindly choose the date that works best for you. <!-- The registration is now open and will end on January 28, 2019 (Monday) at exactly 11:00am. --> Final list of participants will be picked randomly via lottery and will be notified on January 29, 2019 (Tuesday) via email.<br/><br/>



If your preferred schedule is still in conflict with your shift, no need to worry as we will do our best to adjust your work schedule.<br/><br/>



<span class="text-danger">If you won’t get picked for this month, no need to feel bad! We intend to meet <span style="font-size:large">everyone</span> this year as we aim for this to be a monthly activity.</span>


<!--
<h4><a href="http://172.17.0.2/coffeebreak/event/open-access-bpo-hellos-and-grubs/" target="_blank">Click on this link to sign up now!</a></h4> --></p>
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



                            <div class="item text-center">
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




