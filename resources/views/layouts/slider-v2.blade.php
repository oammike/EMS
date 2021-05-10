            <!-- ***** begin announcements slider ***** -->
              @if(count($announcements) >= 1)
                @foreach($announcements as $key => $memo)
                  @if($memo->template=='memo')
                    <div style="background: url('storage/uploads/memobg.png')top left repeat-y; background-size: 50%;background-color: #fff;padding:20px" class="item @if($key==0) active @endif" >
                  @else
                    <div class="item @if($key==0) active @endif" >
                  @endif

                    @if(!is_null($memo->feature_image))
                      <img src="{{ $memo->feature_image }}" width="100%" />
                    @endif

                      <h4 class="text-orange text-center" style="line-height: 1.5em" >
                        {{ $memo->title }} @if(!is_null($memo->decorative_title)) <span class="text-primary"> {!! $memo->decorative_title !!} @endif<br/>
                          <small>{{ $memo->publishDate }}</small><br/>
                          <img src="storage/uploads/divider.png" />
                      </h4>

                      {!! $memo->message_body !!}

                    @if(!is_null($memo->external_link))
                      <input style="font-weight: bold" class="form-control" type="text" id="bundylink" value="{{ $memo->external_link }}" />
                      <button class="cp btn btn-xs btn-primary" data-link="bundylink">Copy Link <i class="fa fa-external-link"></i></button>
                    @endif

                    </div>
                @endforeach
              @endif
            <!-- ***** end announcements slider ***** -->


            <!-- ***** begin anniv celebrators ***** -->
              @if(count($firstYears) >= 1)
                            <!-- ******** FIRST YEAR ANNIV ******* -->
                            <div class="item  text-center">
                              <div class="box box-widget widget-user">
                                <!-- Add the bg color to the header using any of the bg-* classes -->
                                <br/><br/>
                                <h4 class="text-primary">
                                  <img src="storage/uploads/banner_anniv.jpg" width="100%" /><br/>
                                  <i class="fa fa-smile-o fa-2x"></i> <br/><br/>Happy  <span style="color:#f59c0f">1st Year Anniversary</span> <br/><span style="color:#9c9fa0">to the following employees:</span>
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
                                <!-- Add the bg color to the header using any of the bg-* classes --><br/>
                                <h4 class="text-default">Happy 1st Year<span class="text-primary"> @ Open Access!</span><br/></h4>
                                <?php $cover = URL::to('/') . "/storage/uploads/cover-".$n->id."_".$n->hascoverphoto.".png"; ?>

                                @if (is_null($n->hascoverphoto) )
                                 <div class="widget-user-header bg-black" style="background: url('{{ asset('public/img/makati.jpg')}}') center center;">

                                @else
                                <div class="widget-user-header bg-black" style="background: url('{{$cover}}') center center;">
                               @endif


                                </div>



                                  @if ( file_exists('public/img/employees/'.$n->id.'.jpg') )
                                  <div class="widget-user-image" style="left: 40%">
                                  <img class="img-circle" src="{{ asset('public/img/employees/'.$n->id.'.jpg')}}" width="100" style="width: 170px" alt="User Avatar">
                                  @else
                                  <div class="widget-user-image">
                                  <img class="img-circle" src="{{asset('public/img/useravatar.png')}}" width="80" alt="User Avatar">
                                  @endif

                                </div>

                                <div class="box-footer">
                                  @if ( file_exists('public/img/employees/'.$n->id.'.jpg') )<br/><br/><br/>
                                  @endif
                                  @if (empty($n->nickname) || $n->nickname==" ")
                                     <h3 class="widget-user-username"><a href="{{action('UserController@show',$n->id)}}"><small>{{$n->firstname}} {{$n->lastname}} </small></a></h3><small><em>Work Anniversary: {{date('M d, Y', strtotime($n->dateHired))}} </em></small>
                                 @else
                                     <h3 class="widget-user-username"><a href="{{action('UserController@show',$n->id)}}"><small>{{$n->nickname}} {{$n->lastname}} </small></a></h3><small><em>Work Anniversary: {{date('M d, Y', strtotime($n->dateHired))}} </em></small>
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
            <!-- ***** end anniv celebrators ***** -->


            <div class="item text-center" >
                  <h4 class="text-orange" style="line-height: 1.5em" > Feature Article: <br/> <span class="text-primary"><i class="fa fa-newspaper"></i>   The Wall Street Journal (May 15, 2020) <br/><br/>
                  <img src="storage/uploads/wsj.png" width="30%" />
                  <img src="storage/uploads/rooster1.jpg" width="100%" /><br/><br/>
                    <img src="storage/uploads/divider.png" />
                    </h4>
                    <h4><a href="https://www.wsj.com/articles/customer-support-call-animal-noises-rooster-coronavirus-11589465454" target="_blank"> Is That a Rooster on My Customer-Support Call? Yes, Blame Coronavirus.</a></h4>
                    <p style="padding: 30px;" class="text-left">


                      <strong><em>When overseas call-center employees have to work from home, neighborhood animals chime in; ‘the crowing was so close’</em></strong><br/><br/>

                      When Robin Frost called Verizon Communications Inc.’s customer support last month, she was connected with a rooster. At least that’s what it sounded like.<br/><br/>

                      The Pennsylvania resident wanted to ask about a problem with the telecom company’s app, but the agent on the line said she couldn’t hear. Punctuating her words was “the sound of a very authentic, real-sounding rooster,” Ms. Frost recalled.<br/><br/>

                      Thousands of call-center employees in the Philippines and India are working from home, often on the outskirts of urban areas or outside them, during their countries’ coronavirus lockdowns. That has given cows and pigs—but mainly roosters—a chance to chime in.<br/><br/>

                      “It was funny but not funny, and also maddening, as I couldn’t accomplish my task,” Ms. Frost said.<br/><br/>

                      Stefaan Smith, 36, who lives outside Phoenix, had a similar encounter when he called a Sprint helpline in March. He wanted to defer billing on his account after a pizza-parlor job fell through and left him with no money in the bank.<br/><br/>

                      “The crowing was so close,” he said of what sounded like an angry rooster, though that wasn’t the only animal present. “It was like right outside her window. You could hear her pigs grumbling and groaning.”<br/><br/>

                      After follow-up calls, he received a $50 credit from the provider, he said.<br/><br/>

                      Verizon didn’t respond to requests for comment. T-Mobile US, Inc., which owns Sprint Corp., declined to comment.<br/><br/>

                      Call-center executives say roosters are tough to silence. Americans often make customer-service calls in the late afternoon and evening, just when the sun is rising over countries like the Philippines and the birds are at their loudest. That’s when Junela Dumaya’s job gets tricky.<br/><br/>

                      <img src="storage/uploads/rooster2.jpg" width="98%" />

                      The 20-year-old works for Open Access BPO, a call center, and lives in a mountainous area a few hours’ drive from the Philippine capital, Manila. Her neighbor’s cow makes a racket in the mornings, as do her brother-in-law’s chickens.<br/><br/>

                      “I urge my family members to close the door and windows and to catch the cows and take the animals far from me,” said Ms. Dumaya, who begins her shift at midnight. Her family has been fairly effective, she said, though the animals still pose a distraction sometimes.<br/><br/>

                      Her boss, Ben Davidowitz, said he is doing his best to limit the animal soundtrack, adding: “I don’t want roosters honking in the background.” Sometimes employees are put up in housing close to work so they can go into the office, where small crews are permitted—and no roosters are near.<br/><br/>

                      <img src="storage/uploads/rooster3.jpg" style="padding:10px" width="30%" class="pull-left" />Bryce Maddock, the chief executive of TaskUs Inc., a customer-support outsourcing company, has dispatched noise-cancelling headsets. “I don’t think it’s rooster-proof, but that technology is at least one way to reduce the background noise,” said Mr. Maddock, whose firm has operations in the Philippines and caters to clients such as food-delivery services.<br/><br/>

                      Mr. Maddock has a history with roosters. He ordered a hit on one about a decade ago, when he was setting up his business in the Philippines. The neighbor of a staffer who was working from home had a noisy rooster that had become an operational hurdle.<br/><br/>

                      “We asked her how much we would have to pay to get the rooster killed,” he recalled. “She got a price from her neighbor and we paid the price and that was that. There was no more rooster in the background.”<br/><br/>

                      Mr. Maddock said he feels bad about it, particularly because he is now a vegetarian.<br/><br/>


                      Some say the rooster run-ins are providing comic relief. “At first, I was getting tired of hearing all the callers ask about the roosters, but nowadays we just laugh at it,” said Kathryn Ronquillo, a supervisor at a Manila call center that takes calls for a video-streaming company.<br/><br/>

                      A clip of a 2018 avian interruption, posted on Facebook by customer-service agent Jennifer Jasme, is getting fresh online traction in the Philippines. A man can be heard asking Ms. Jasme if she has chickens in her office, with cawing sounds audible in the background. Ms. Jasme responds that it’s actually a ringtone.<br/><br/>

                      “That was really funny,” Ms. Ronquillo said of the recording. “But we don’t want them to lie on their calls.”<br/><br/>

                      For agents like 36-year-old Ms. Jasme, it can be an awkward experience.<br/><br/>

                      “When I’m on a call and the roosters start to do their thing, like as if they are choir singing at the top of their lungs, I’ll start to pray and hope the customer won’t ask me my location,” Ms. Jasme said. Over time, she thinks it will become normal.<br/><br/>

                      “The world is changing, right?," she said. “I think we’ll get used to it.”<br/><br/>

                      Call centers aren’t the only businesses with rooster-induced headaches. Jimmy Roa runs an offshore recruit-process company in Manila whose agents conduct pre-qualifying interviews for job candidates for technology companies in America. When his employees dial out, their phone numbers show U.S. area codes, leaving the impression they are U.S.-based.<br/><br/>

                      “If a dog is barking, you can always apologize and say, ‘I’m sorry, my dog is right beside me,’ ” said Mr. Roa, chief executive of Sysgen RPO. “A rooster isn’t a normal pet in the U.S.”<br/><br/>

                      Because some customers feel being connected to someone halfway around the world means the company isn’t take their problem seriously enough, routing calls to Asia can be a touchy subject. Ms. Ronquillo, the supervisor, said a cock-a-doodle-doo signals to customers that they aren’t speaking to an American. While most callers are pleasant, she said, some ask to be transferred to a U.S. representative.<br/><br/>

                       <img src="storage/uploads/rooster4.jpg" style="padding:10px" width="30%" class="pull-left" />Others are uncomfortable broaching the rooster topic. Danielle Elizabeth, a 25-year-old retail manager in Pittsburgh, was charged twice for a home-delivered order of Chipotle’s veggie burrito bowl. When she called to resolve the matter, she heard what sounded like a rooster squawking.<br/><br/>

                      “I couldn’t get up the courage to ask, like, ‘Is there a rooster?’ ” she said.<br/><br/>

                      Write to Jon Emont at jonathan.emont@wsj.com <a href="https://www.wsj.com/articles/customer-support-call-animal-noises-rooster-coronavirus-11589465454" target="_blank">[Article link]</a></p>






            </div>

            <div class="item text-center" >
                  <h4 class="text-orange" style="line-height: 1.5em" > Feature Article: <br/> <span class="text-primary"><i class="fa fa-newspaper"></i>   The Wall Street Journal <br/>Apr.23, 2020 <br/><br/>
                  <img src="storage/uploads/wsj.png" width="30%" />
                  <img src="storage/uploads/ben_article.jpg" width="100%" /><br/><br/>
                    <img src="storage/uploads/divider.png" />
                    </h4>
                    <h5><a href="https://www.wsj.com/articles/call-center-operator-keeps-phone-lines-open-during-pandemic-11587634205" target="_blank"> Call Center Operator Keeps Phone Lines Open During Pandemic</a></h5>
                    <p style="padding: 30px;" class="text-left">


                      Ben Davidowitz’s company in the Philippines fields customer calls from employees’ homes; ‘we can’t have roosters in the background’<br/><br/>

                      Ben Davidowitz moved to the Philippines in 2010 to open call centers that allowed global companies to move their customer support services offshore. The coronavirus pandemic has forced the American entrepreneur to find a way for his workers to serve global customers from their crowded metro-Manila apartments.<br/><br/>

                      Mr. Davidowitz runs Open Access BPO, a company that counts a U.S. food-delivery service and a major hospital system among its customers. As clients manage the crisis, they rely on his Filipino staffers to field questions about insurance and delivery orders gone wrong.<br/><br/>

                      Two of his company’s largest call centers are based in the Philippines’ capital, Manila, which has been under lockdown since mid-March. While a skeleton crew works in the office, most of his 1,200 employees had to set up makeshift call centers at home.<br/><br/>

                      Open Access BPO’s revenue is down 12% since the start of the coronavirus pandemic, as some clients scaled back and terminated their contracts. And the new working procedures for employees come with additional costs.<br/><br/>

                      Many members of Mr. Davidowitz’s young workforce live in small apartments without desks or even internet connections. He dispatched technicians—carrying special passes from the government—to each of the homes where employees needed support. Over many days, the teams set up internet services, hauled work stations and tested the voice quality of calls.<br/><br/>

                      Answering customer queries, however, requires more than quality equipment. A quiet environment is also necessary.<br/><br/>

                      This is where Mr. Davidowitz faces a problem. Roosters are abundant in the areas a few hours’ drive from central Manila where many call-center workers live.<br/><br/>

                      “We can’t have roosters in the background,” Mr. Davidowitz said. “It may not be a rooster right at their house, but it could be a rooster next door. That immediately sends a red flag to a customer.”<br/><br/>

                      Around 200 of his employees continue to work from the offices because there isn’t enough space in their houses, internet connectivity is poor or the surroundings are too noisy from roosters or neighbors. They are seated far apart, and the company rented housing units and hotel rooms near the call centers for them to stay in. Food is provided for those working from the office.<br/><br/>

                      Once every two weeks, he drops in to his Manila call centers and makes sure employees are following social-distancing procedures.<br/><br/>

                      The complexity the 57-year-old faces in keeping the company’s phone lines open is compounded by increased responsibilities at home. He is now taking care of his 25-year old son, Harry Davidowitz, who has autism, full time because school is closed. Harry’s usual caretaker is staying home with family during the lockdown.<br/><br/>

                      These days, Harry wakes his father up at 6:30 a.m., hungry for breakfast or eager to listen to music. “He’ll just be standing there next to my bed like a statue, waiting for me,” Mr. Davidowitz said.<br/><br/>

                      Mr. Davidowitz said his son was “100% driven by routine” and that it was important to establish a new schedule to replace the old one. Harry, who is mostly nonverbal, still makes requests for his favorite foods and regular activities, such as going to the nearby swimming pool.<br/><br/>

                      “In a kid’s mind like his, the questions start flowing,” he said.<br/><br/>

                      With the pool closed, Mr. Davidowitz took an old bathtub he had in storage and hauled it up to his roof. He fills it with a hose so his son can splash around. “It’s turned into another activity we can incorporate into the day,” Mr. Davidowitz said.<br/><br/>

                      He also gives his son plenty of time to listen to his favorite electronic dance music and watch animation such as “Lilo & Stitch.”<br/><br/>

                      Mr. Davidowitz’s extended family has also been affected. Two of his brothers in Pennsylvania tested positive for Covid-19. One got better quickly, while the other battled the illness for weeks, with a fever, chills and night sweats, though has recently recovered.<br/><br/>

                      Mr. Davidowitz thought about flying home, but most commercial flights to and from the Philippines have been canceled. He also looked into chartering a private flight to see his brothers.<br/><br/>

                      He worried, though, that if he got sick, there would be no one to take care of Harry.<br/><br/>

                      Write to Jon Emont at jonathan.emont@wsj.com <a href="https://www.wsj.com/articles/call-center-operator-keeps-phone-lines-open-during-pandemic-11587634205" target="_blank">[Article link]</a></p>






            </div>

            <div style="background: url('storage/uploads/memobg.png')top left repeat-y; background-size: 50%;background-color: #fff;padding:20px" class="item text-center" >

                  <div style="position: relative;width:100%">
                    <img src="storage/uploads/bdavidowitz.jpg" width="95%"  />
                     <h4 class="text-orange" style="line-height: 1.4em; width:50%; position: absolute; right: 0px; top: 0px" > Message From Our CEO: <br/> <span class="text-primary"> Ben Davidowitz</i><br/><br/><img src="storage/uploads/divider.png" />
                  </div>


                    </h4>


                    <p style="padding: 30px;" class="text-left">
                     Dear Open Access Family:<br/><br/>



                      I hope everyone is keeping safe and healthy. Although many of you are in isolation alone many of you are more fortunate to cherish the extra time we can spend with our loved ones.<br/><br/>



                      We are living through unprecedented times, a period in which 50 years from now people will look back as an important moment in history. It is hard to predict what will happen over the next few weeks and even few months. And while life is now testing each and every one of us, I am 100% confident we will all come through this stronger, if we stick together. Certainly many of us will have a new appreciation for life and the ones we love. <br/><br/>



                      Open Access has been through many ups and downs since 2006 - we started this business at the beginning of what was to become the Global Financial Crisis, an economic event that had not been seen since the Great Depression in the 1930s. This time is different as that period was just about money and jobs, something that can be replaced. COVID-19 is about something even more important - our health and our lives, which can't be replaced. <br/><br/>



                      As this situation is evolving, we are prepared to continue to do everything we can to ensure the safety of everyone at Open Access, as well as making sure everyone gets paid. While many companies in the U.S. are having to reduce their workforces, we are very fortunate the bulk of our portfolio of clients are stable and operational.<br/><br/>



                      In the last two weeks, we have shifted from an in-office BPO to a largely work-from-home operation only made possible by enormous efforts from our stellar IT team, Joy, and the rest of leadership. They have all worked tirelessly making this happen so quickly, which enabled us to continue to operate while Manila is locked down.<strong> Thank you!!!!!!!</strong><br/><br/>



                      Currently, we have approximately:<br/><br/>



                      - 800 people working from home <br/><br/>



                      - 170 people working out of all our Makati and Davao locations <br/><br/>



                      - 40 people working out of our Taiwan and China locations which are both fully operational <br/><br/>



                      We've been able to adapt to the situation that is a testament not only to our leadership but to all of you for making this possible. <br/><br/>



                      Your health and safety is of the utmost importance to me because the virus has also hit close to home. I now have two brothers who have tested positive for COVID-19. One has just recovered and one is still fighting at home, in quarantine, 8500 miles away in the U.S. It has been a sleepless couple weeks as I think about your health and safety as well as that of my brothers.   <br/><br/>



                      With Easter and Holy Week coming, many of you may think of compromising the current integrity of social isolation. You may want to see friends and family that you haven't seen for weeks now. I want you to all be vigilant in respecting our messaging to keep social isolation and have the least amount of contact with other people outside your household until it is absolute in the sense of your safety and others. The next two weeks are critical as we are expected to hit the peak of transmission in the Philippines.<br/><br/>



                      There is a ton of information floating around on the internet and I would like to emphasize one point which I hope puts things into perspective with the rest of this email:<br/><br/>



                      - The Philippines has tested 10,000 people with a population of 100MM<br/><br/>



                      - Germany is testing 500K people a week and has a population of 83MM<br/><br/>



                      I have advised Joy and our leadership there is no way the government can make an informed decision on the timing of the lockdown without the ability to test. Therefore, should the lockdown be lifted anytime this month we will continue with our current strategy of working skeleton crews in the office with most working from their home environments. <br/><br/>



                      I am very concerned the Philippines may make a premature decision heavily weighted by the economic climate. If the lockdown is lifted this month we will sit on the sidelines and observe if there are any new outbreaks and see if the cases are trending downward. A key deciding factor for me is when hospitals return to doing elective surgery - it means their capacity is no longer strained. Today, we are far from that.<br/><br/>



                      I also understand this situation is evolving daily so we will update you and communicate as much as we can via email and on the EMS. As stated above, your safety and making sure you are employed is our highest priority. <br/><br/>




                      One person I follow, who in my opinion knows more than any government about what is going on, is Bill Gates. I listen carefully to what he says. <br/><br/>



                      Below is a recent interview with Bill Gates. I encourage you to watch at your leisure…<br/>



                      <a href="https://youtu.be/4X-KkQeMMSQ" target="_blank">https://youtu.be/4X-KkQeMMSQ</a><br/><br/>



                      Be Safe. Be Healthy. <br/><br/>



                      Best,<br/>




                      <strong>Ben</strong>




                    </p>

            </div>



              <!-- ***** begin New Hires ***** -->
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
                                 <h3 class="widget-user-username"><a href="{{action('UserController@show',$n->id)}}" target="_blank"><small>{{$n->firstname}} {{$n->lastname}} </small></a></h3>
                             @else
                                 <h3 class="widget-user-username"><a href="{{action('UserController@show',$n->id)}}" target="_blank"><small>{{$n->nickname}} {{$n->lastname}} </small></a></h3>
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
              <!-- ***** end New Hires ***** -->


            <div class="item text-center" >
                  <img src="storage/uploads/newRewards.jpg" width="100%" />
                  <h4 class="text-orange" style="line-height: 1.5em" >
                    Open Access BPO Rewards:  <br/><span class="text-primary"><i class="fa fa-gift"></i> New Items for 2021 <i class="fa fa-gift"></i><br/><small>Feb.11, 2021</small><br/>
                    <img src="storage/uploads/divider.png" />
                  </h4>

                  <p style="padding: 30px;" class="text-center">
                      We couldn't thank you enough for continuing the AWESOME work do!<br/><br/>

                      <a class="btn btn-primary btn-md" href="{{action('RewardsHomeController@rewards_catalog') }}"><i class="fa fa-gift"></i> View Rewards Catalog</a>
                  </p>
            </div>


            <div class="item text-center" style="background-color: #fff" >
              <img src="./storage/uploads/OALife.jpg" style="z-index: 2" />
              <p class="text-center" style="padding: 30px;"><br/><br/>
                <strong class="text-primary">Open Access BPO Life</strong> is now up on Facebook!  <br/>
                <a class="btn btn-md btn-primary" target="_blank" href="https://www.facebook.com/openaccessbpolife/"><i class="fa fa-facebook-square"></i> facebook.com/openaccessbpolife</a><br/><br/>

                  Like and follow this page to stay updated with the latest company news, featured stories, parties, and career opportunities - all in one place. While you're at it, engage in the comment section of posts you're interested in to find like-minded people who you can share your interests/hobbies with! <br/><br/>

                  Your ideas matter. So, if you have any content suggestions/recommendations, drop a private message via our Events email <strong>events@openaccessbpo.net</strong> on Zimbra or Messenger.<br/><br/>

                Oh, and you can follow us on other social media sites too!</p>

                <a class="btn btn-md btn-primary" target="_blank" href="https://twitter.com/OpenAccessBPO"><i class="fa fa-2x fa-twitter-square"></i>  </a>

                <a class="btn btn-md btn-primary" target="_blank" href="https://www.instagram.com/openaccessbpo/"><i class="fa fa-2x fa-camera-retro"></i>  </a>

                <a class="btn btn-md btn-primary" target="_blank" href="https://www.linkedin.com/company/open-access-bpo/"><i class="fa fa-2x fa-linkedin-square"></i>  </a>

                <a class="btn btn-md btn-primary" target="_blank" href="https://www.youtube.com/OpenAccessBPO/"><i class="fa fa-2x fa-youtube-square"></i>  </a>
                 <br/><br/><br/>

            </div>


            <div class="item text-center" >
                  <img src="storage/uploads/donationposter.jpg" width="100%" />
                  <h4 class="text-orange" style="line-height: 1.5em" >Share Your Points <br/><span class="text-primary"> <small>A little compassion goes a long way</small><br/>

                  <img src="storage/uploads/divider.png" />
                  </h4>
                  <p style="padding: 30px;" class="text-center">
                     For a minimum of <strong>50 points,</strong>you can help our healthcare providers and frontliners who have been called to serve in the fight against the spread of COVID-19
                   </p>
                   <a class="btn btn-success btn-md" href="{{action('RewardsHomeController@rewards_catalog')}}#donatenow"> Donate Now</a>
            </div>


