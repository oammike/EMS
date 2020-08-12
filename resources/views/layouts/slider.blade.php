


            

            <div class="item active  text-center" >
                  <h4 class="text-orange" style="line-height: 1.5em" >ECQonnection Wall: <br/><span class="text-primary"><i class="fa fa-globe"></i> World Youth Day <br/><small>Wednesday, Aug. 12</small><br/>
                  
                    <img src="storage/uploads/divider.png" />
                    </h4>
                    <p  style="padding: 30px;" class="text-center">This week is <strong class="text-primary"></strong><i class="fa fa-children"></i> World Youth Day!<br/><br/>

                    It’s World Youth Day on August 12!
                    Let’s take a walk down memory lane and answer this question:<br/><br/>
                    <strong class="text-primary"> What’s your best memory as a child / teenager?</strong><br/><br/>
                    Share a photo of yourself from your childhood or teenage days.
                    All participants will get 20 points. </p>

                    <img src="storage/uploads/wall_week14.jpg" width="100%" /><br/>
                    <a class="btn btn-success btn-md" href="{{action('EngagementController@show',20)}}"> Check out our ECQonnection Wall</a>
                    
                    
            </div>

            


            <div style="background: url('storage/uploads/memobg.png')top left repeat-y; background-size: 50%;background-color: #fff;padding:20px" class="item text-center" >
                  <h4 class="text-orange" style="line-height: 1.5em" > Message from Finance: <br/> <span class="text-primary"> Your Payroll Help Desk is Here! <br/>
                  
                    <img src="storage/uploads/divider.png" />
                    </h4><img src="storage/uploads/payrollHelpdesk.jpg" width="98%" />
                    <p style="padding: 30px;" class="text-left">
                     
                    Hello everyone,<br/><br/>

                    We are officially introducing Payroll Help Desk today. Getting assistance on your payroll-related inquiries is now more convenient and systematic.<br/><br/>

                    Please access the user guide here to find out how to use it:
                    <input style="font-weight: bold" class="form-control" type="text" id="bundylink" value=" https://rise.articulate.com/share/R7DUt2qkI--qHon-LOtel335dgZHDs26" />
                    <button class="cp btn btn-xs btn-primary" data-link="bundylink">Copy Link <i class="fa fa-external-link"></i></button>
                     <br/><br/>
                     
                    

                   

                   

                    </p> 


 
                    
            </div>

            <div class="item text-center" >
                  <h4 class="text-orange" style="line-height: 1.5em" ><br/>
                    <span class="text-primary"><i class="fa fa-book"></i> National Book Lovers Day Raffle Winners! <br/><small>Aug 10, 2020</small><br/>
                    <img src="storage/uploads/divider.png" />
                    <img src="storage/uploads/booklovers.jpg" width="100%" /><br/>
                    
                    </h4>
                   

                   
                    <video id="teaser1" src="storage/uploads/winners_raffle.mov" width="100%" loop controls></video>

                    <br/>

                    <a class="btn btn-success btn-md" href="{{action('EngagementController@wall',19)}}"> Check out the Wall posts</a>
                    
                    
            </div>


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

                 

                
                  



                 
                
               


            

            


           

            



           

            

             <div style="background: url('storage/uploads/memobg.png')top left repeat-y; background-size: 50%;background-color: #fff;padding:20px" class="item text-center" >
                  <h4 class="text-orange" style="line-height: 1.5em" >Annual <span class="text-primary">  <i class="fa fa-lock"></i> InfoSec Training<br/>
                  
                    <img src="storage/uploads/divider.png" />
                    </h4>
                    <p style="padding: 30px;" class="text-left">
                      <img src="storage/uploads/infosec_training.png" width="98%" /><br/><br/>
                    Hello everyone,<br/><br/>

                    Our Information Security Awareness module is now live on our <a href="https://open-access.training-online.eu/" target="_blank"> LMS</a>! This is foundational to Open Access BPO strengthening system security and standardizing Information Security practices.  This is a <strong><a href="https://open-access.training-online.eu/" target="_blank"> required training</a></strong> and needs to be <strong>completed by August 27, 2020.</strong>
                    <br/><br/>

                    This is fielded once a year to ensure we’re all up to speed with the latest policies and protocols. This year, we are running it from July 27, 2020 to August 27, 2020. The knowledge check is short (15 questions) and would take only about 30 minutes or less to complete, and may be taken at any time which suits you during your shift. The number of days allocated for module completion is more than adequate, please finish it before the 30-day period expires.<br/><br/>

                    Click here for the <strong><a href="https://drive.google.com/file/d/1-xTeTWZ6VvB7zINVoX3JaVZM857dftuh/view" target="_blank"> Module Guidelines and Instructions</a></strong><br/><br/>
                    Click here for the <strong><a href="{{action('ResourceController@index')}}" target="_blank">
                    Information Security Policy</a></strong><br/><br/>
                     
                    We all have a responsibility to safeguard assets entrusted to us and ensure compliance with global regulations. This mandatory training program will fortify us against security risks and threats. Let’s adopt these behaviours together because they protect our business, our customers, our community, and each of us here at Open Access.<br/><br/>

                    If you have any questions, feel free to email us at  corporate_compliance@openaccessbpo.com.<br/><br/>

                    Thank you!  
                    <br/><br/> 
                   

                  

                    </p> 


 
                    
            </div>

            <div style="background: url('storage/uploads/memobg.png')top left repeat-y; background-size: 50%;background-color: #fff;padding:20px" class="item text-center" >
                  <h4 class="text-orange" style="line-height: 1.5em" > eLearning for Leaders: <i class="fa fa-users"></i><br/> <span class="text-primary"> ALERT Part 2<br/>
                  
                    <img src="storage/uploads/divider.png" />
                    </h4>
                    <p style="padding: 30px;" class="text-left">
                     
                    Hello Open Access BPO Leaders,<br/><br/>

                    Part 2 of ALERT, the eLearning course aimed to help you lead your remote teams, is now live on our LMS 
                    <input style="font-weight: bold" class="form-control" type="text" id="bundylink" value="https://open-access.training-online.eu/" />
                    <button class="cp btn btn-xs btn-primary" data-link="bundylink">Copy Link <i class="fa fa-external-link"></i></button>
                     <br/><br/>
                      You may self-enroll (walkthrough) now and start learning. <br/><br/> 
                    <img src="storage/uploads/zeke.png" width="98%" />

                    The focus of this release is communication. Here's a rundown of the lessons: <br/><br/> 
                    - Creating a Communications Plan <br/><br/> 
                    - Remote Team Communications <br/><br/> 
                    - Email for Remote Teams <br/><br/> 
                    - Connecting Through Imagery and Metaphor <br/><br/> 
                    - Brainstorming Across Borders <br/><br/> 
                    - Real-time Conversations Crucial for Collaboration <br/><br/><br/> 
                    There are two expected outputs from you - a communications plan and a stakeholder analysis. Templates are included in the module. This is <strong>due on July 27 </strong>- add a card and attach your docs in Trello. This ensures that you get to apply the concepts you learned and you get to collaborate with your peers. There's also a Wishlist section where you can surface requests, suggestions, and ideas.  <br/><br/> 

                    Managers you are encouraged to work with your team leads - you can add this as part of their regular development plan. One less task to think about when you sit down for their annual performance review. <br/><br/> 

                    If you  missed Part 1 - we are making it available in the catalog for a limited time as well. You may take this chance to catch up. Let us know if you have questions.<br/>
                    
                    <br/><br/> 

                   

                    </p> 


 
                    
            </div>

            <div class="item text-center" >
               
                <h4 class="text-orange" style="line-height: 1.5em" > New <span class="text-primary">Reward Items!!!<br/> 
                  <img src="storage/uploads/divider.png" /> 
                  <img src="storage/uploads/newVouchers.jpg" width="100%" /><br/>
                  
                   
                    </h4>
                    <p style="padding: 30px;" class="text-center">As we continue to celebrate YOU and the awesome work you do
                      especially during the community quarantine, here are the new items available on our Rewards Catalog!<br/><br/>

                      You may redeem <strong class="text-danger">1 voucher per day</strong>. <br/>What are you waiting for?<br/>
                    Check out our Rewards page to select the voucher you want to redeem!</p>


                   
                    
            </div>

           

         

             

              <div class="item text-center">
                
                  <h4 class="text-orange" style="line-height: 1.5em" >Reminders from<br/><span class="text-primary">Clinical Services</span>
                  <br/>
                    <img src="storage/uploads/divider.png" />
                    </h4>

                    <img src="storage/uploads/newnormal_home.jpg" width="100%" /><br/>
                    <img src="storage/uploads/newnormal_office.jpg" width="100%" /><br/>

                    
                    
 
 
                    
              </div>

           

              <div class="item text-center">
                <img src="storage/uploads/oneforhealth_11.jpg" width="100%" />
                  <h4 class="text-orange" style="line-height: 1.5em" >We are #OneForHealth<br/><span class="text-primary">on staying safe</span>
                  <br/>
                    <img src="storage/uploads/divider.png" />
                    </h4>
                    
                    <p style="padding: 30px;" class="text-left">
                     
                   <em>"With GCQ implemented and its loosened restrictions, my biggest fear is that all our efforts in protecting the health, safety, and livelihood of our employees in the last few months will all be for naught. I ask that each member of our Open Access BPO family restrict movements only for absolute essentials. Be responsible. Stay healthy and remain overly cautious. We will see this through, together.<br/><br/>
                    Being a single mom, with 3 kids and 3 dogs, facing the changes since ECQ was quite the predicament I had to quickly accept. I needed to deal with these while leading our team to enable our employees to work from home, develop and deploy a shuttle service system, set up office-dwelling, house a subset of our employees in temporary accommodations while ensuring we get our clients to agree with flexible work arrangements and caring for the health, safety, and livelihood of our employees.<br/><br/>
                    
                    In between meetings and sometimes during one-on-ones with my team, I find myself multitasking to shop for groceries online or start preparing meals. Four months in, I have managed to and have actually enjoyed integrating work-life balance.”</em><br/><br/>

                    Open Access BPO's Vice President Joy talks about her experience as a mother and a leader during quarantine and how she copes with the changes in her daily life brought by the pandemic.<br/><br/>
                    *******************************<br/><br/>
                    We are #OneForHealth is a series from Open Access BPO that tackles how our employees keep themselves and those around them healthy and safe during the pandemic. We hope this can serve as a reminder for everyone to take an active role as we combat COVID-19.<br/><br/>

                    Understand more about health and safety guidelines, visit World Health Organization (WHO)’s website: https://tinyurl.com/whohealthmeasures.
                  </p>    
 
 
                    
              </div>
             
              <div class="item text-center">
                <img src="storage/uploads/oneforhealth_10.jpg" width="100%" />
                  <h4 class="text-orange" style="line-height: 1.5em" >We are #OneForHealth<br/><span class="text-primary">on staying safe</span>
                  <br/>
                    <img src="storage/uploads/divider.png" />
                    </h4>
                    
                    <p style="padding: 30px;" class="text-left">
                     
                   <em>"Learning a painful lesson from the 2002 SARS outbreak, my family and I responded immediately to COVID-19. We regularly wear masks even when there are no new cases in our community. The company also provides employees with face masks and disinfectants to make sure we’re safe at work.<br/><br/>

                    Before the pandemic, my teammates and I would spend our lunch break together, eating and chatting. But now, we eat separately and limit face-to-face meetings to practice social distancing. We mostly use Skype to communicate with each other and our TL.<br/><br/>

                    Working in a tourism campaign is especially challenging during the pandemic. Once, there was a customer whose family was stranded in the Philippines asking for the earliest available flight. I couldn’t help them because all our flights were canceled. I researched for an alternative solution and found a charter flight for them.””</em><br/><br/>

                    Taipei-based Customer Service Agent Julia talks about the immediate precautions she’s taken in order to protect herself and her family and her experience being in the customer service industry during a pandemic.<br/><br/>
                    *******************************<br/><br/>
                    We are #OneForHealth is a series from Open Access BPO that tackles how our employees keep themselves and those around them healthy and safe during the pandemic. We hope this can serve as a reminder for everyone to take an active role as we combat COVID-19.<br/><br/>

                    Understand more about health and safety guidelines, visit World Health Organization (WHO)’s website: https://tinyurl.com/whohealthmeasures.
                  </p>    
 
 
                    
              </div>

              <div class="item text-center">
                <img src="storage/uploads/oneforhealth_9.jpg" width="100%" />
                  <h4 class="text-orange" style="line-height: 1.5em" >We are #OneForHealth<br/><span class="text-primary">on staying safe</span>
                  <br/>
                    <img src="storage/uploads/divider.png" />
                    </h4>
                    
                    <p style="padding: 30px;" class="text-left">
                     
                   <em>"I practiced self-isolation for 14 days to protect myself and others from COVID-19. I felt lonely because I couldn't be with my family and friends, but I did overcome it.<br/><br/>

                    We practice contactless shopping in our community. There are volunteers who help buy our daily necessities and leave the items on the front door for pick up.<br/><br/>

                    For two weeks, I worked at home and doubled my productivity to help as many customers as I can. This was also my way of adjusting to the changes of having to work from home.”</em><br/><br/>

                    Xiamen-based Customer Service Agent Summer shares how she strictly self-isolated and provided quality customer support in the wake of a pandemic.<br/><br/>
                    *******************************<br/><br/>
                    We are #OneForHealth is a series from Open Access BPO that tackles how our employees keep themselves and those around them healthy and safe during the pandemic. We hope this can serve as a reminder for everyone to take an active role as we combat COVID-19.<br/><br/>

                    Understand more about health and safety guidelines, visit World Health Organization (WHO)’s website: https://tinyurl.com/whohealthmeasures.
                  </p>    
 
 
                    
              </div>


             
              

              <div class="item text-center">
                <img src="storage/uploads/oneforhealth_8.jpg" width="100%" />
                  <h4 class="text-orange" style="line-height: 1.5em" >We are #OneForHealth<br/><span class="text-primary">on staying safe</span>
                  <br/>
                    <img src="storage/uploads/divider.png" />
                    </h4>
                    
                    <p style="padding: 30px;" class="text-left">
                     
                   <em>"There’s a constant fear of losing someone to this health crisis. That's why we need to stay healthy and safe for ourselves and our families. We must take our roles seriously to help slow the spread of infections in our communities.<br/><br/>

                    I also try to understand how I can cope with stress. What keeps me going during this period is staying in touch with my family and friends.<br/><br/>

                    The same goes as I take charge of my role in the company. My teammates and I provide our co-workers with necessary supplies and remind them to constantly observe the company's safety guidelines to help them stay safe and healthy as they report to work.”</em><br/><br/>

                    Davao Operations Administrative and Accounting Assistant Joash shares about her perspective on the importance of taking preventive measures and coping with stress in the wake of a pandemic.<br/><br/>
                    *******************************<br/><br/>
                    We are #OneForHealth is a series from Open Access BPO that tackles how our employees keep themselves and those around them healthy and safe during the pandemic. We hope this can serve as a reminder for everyone to take an active role as we combat COVID-19.<br/><br/>

                    Understand more about health and safety guidelines, visit World Health Organization (WHO)’s website: https://tinyurl.com/whohealthmeasures.
                  </p>    
 
 
                    
              </div>
              

              <div class="item  text-center">
                <img src="storage/uploads/oneforhealth_7.jpg" width="100%" />
                  <h4 class="text-orange" style="line-height: 1.5em" >We are #OneForHealth<br/><span class="text-primary">on staying safe</span>
                  <br/>
                    <img src="storage/uploads/divider.png" />
                    </h4>
                    
                    <p style="padding: 30px;" class="text-left">
                     
                   <em>"Protecting yourself is also protecting others. All the policies and reminders won't be effective if we will not be responsible of our own health. Be honest when you know you are not feeling well. Whether you're working in the office or at home, reach out to us. The nurses' priority is your health and we will do our best to help you.<br/><br/>

                    This may sound cliché but prevention is better than cure. Open Access BPO's efforts focuses on preventive measures with great regard to preparedness response. Our team worked on arranging our schedules so we can gradually become available 24/7 for employees reporting on-site. We are also continuously collaborating with other departments to keep our Open Access BPO family safe and healthy during this pandemic.<br/><br/>

                    Personally, the abrupt change brought by this pandemic is a challenge. The shift from reporting on-site to working remotely is new to me. I felt scared at first but I was able to pull myself together. Two of my sisters are also nurses and both of them are on the front lines. Our profession makes our parents worried most of the time, but we do our best to let them know we practice all the precautionary measures needed to stay safe."</em><br/><br/>

                    Nurse Manager Loraine shares her experience as a nurse in the wake of a pandemic and explains how protecting oneself can make a difference to ensure the safety of many.<br/><br/>
                    *******************************<br/><br/>
                    We are #OneForHealth is a series from Open Access BPO that tackles how our employees keep themselves and those around them healthy and safe during the pandemic. We hope this can serve as a reminder for everyone to take an active role as we combat COVID-19.<br/><br/>

                    Understand more about health and safety guidelines, visit World Health Organization (WHO)’s website: https://tinyurl.com/whohealthmeasures.
                  </p>    
 
 
                    
              </div>
              <div class="item text-center">
                <img src="storage/uploads/oneforhealth_6.jpg" width="100%" />
                  <h4 class="text-orange" style="line-height: 1.5em" >We are #OneForHealth<br/><span class="text-primary">on staying safe</span>
                  <br/>
                    <img src="storage/uploads/divider.png" />
                    </h4>
                    
                    <p style="padding: 30px;" class="text-left">
                     
                   <em>“During this pandemic, I realized how important my job as an HR Officer is. To ensure the safety of our co-workers here in Davao, we regularly distribute disinfectant supplies and face masks. We also make sure that they wash their hands properly before entering the office and constantly practice physical distancing. We’re also provided with shuttle services and hotel accommodations. I’m thankful that I have my job and that through it, I help protect others.<br/><br/>

                    My family’s in the province. I’m worried for them so I always call to make sure they’re safe and to remind them to stay home. I encourage everyone to do the same and follow their respective local government’s guidelines to protect themselves and their loved ones.”</em><br/><br/>

                    HR Officer Mary Lord shares how she protects her family and her co-workers through her job during the pandemic.<br/><br/>
                    *******************************<br/><br/>
                    We are #OneForHealth is a series from Open Access BPO that tackles how our employees keep themselves and those around them healthy and safe during the pandemic. We hope this can serve as a reminder for everyone to take an active role as we combat COVID-19.<br/><br/>

                    Understand more about health and safety guidelines, visit World Health Organization (WHO)’s website: https://tinyurl.com/whohealthmeasures.
                  </p>    
 
 
                    
              </div>


               <div class="item text-center">
                
                  <h4 class="text-orange" style="line-height: 1.5em" >Maxicare Member Guidelines:<br/><span class="text-primary">Physician Professional Fees, Screening Tests and Other Charges</span>
                  <br/>
                    <img src="storage/uploads/divider.png" />
                    </h4>
                    <img src="storage/uploads/maxicareGuidelines.jpg" width="100%" />
                    
 
 
                    
              </div>

              

              <div class="item text-center">
                <img src="storage/uploads/oneforhealth_5.jpg" width="100%" />
                  <h4 class="text-orange" style="line-height: 1.5em" >We are #OneForHealth<br/><span class="text-primary">on staying safe</span>
                  <br/>
                    <img src="storage/uploads/divider.png" />
                    </h4>
                    
                    <p style="padding: 30px;" class="text-left">
                     
                   <em>“Stay home if there’s nothing important to do outside. By doing so, we're saving lives. My team delivered PCs and routers to many at-home workers as part of Open Access BPO's response to the ECQ. The company provided assistance so we can stay safe when working at home or in the office. It's our responsibility to take care of ourselves and follow safety measures.<br/><br/>

                    I've been working from home since the quarantine started, and this changed my daily routine. I'm always a bit hesitant to go out for groceries because there are a lot of reported COVID-19 cases in our area. I have an 8-month old baby, and I don't want to risk infection.<br/><br/>

                    To avoid this, I make sure to always being alcohol, wear a face mask, and observe physical distancing.”</em><br/><br/>

                    IT Manager Arvie shares his strict precautions to protect himself and his family against COVID-19.<br/><br/>
                    *******************************<br/><br/>
                    We are #OneForHealth is a series from Open Access BPO that tackles how our employees keep themselves and those around them healthy and safe during the pandemic. We hope this can serve as a reminder for everyone to take an active role as we combat COVID-19.<br/><br/>

                    Understand more about health and safety guidelines, visit World Health Organization (WHO)’s website: https://tinyurl.com/whohealthmeasures.
                  </p>    
 
 
                    
              </div>


              

              <div class="item text-center">
                
                  <h4 class="text-orange" style="line-height: 1.5em" >Reminders from<br/><span class="text-primary">Clinical Services</span>
                  <br/>
                    <img src="storage/uploads/divider.png" />
                    </h4>

                    <img src="storage/uploads/who_1.png" width="100%" /><br/>
                    <img src="storage/uploads/who_2.jpg" width="100%" /><br/>

                    
                    
 
 
                    
              </div>

              <div class="item text-center">
                
                  <h4 class="text-orange" style="line-height: 1.5em" >Reminders from<br/><span class="text-primary">Clinical Services</span>
                  <br/>
                    <img src="storage/uploads/divider.png" />
                    </h4>

                    <img src="storage/uploads/who_3.jpg" width="100%" /><br/>
                    <img src="storage/uploads/who_4.jpg" width="100%" /><br/>
                   
                    
                    
 
 
                    
              </div>

              <div class="item text-center">
                
                  <h4 class="text-orange" style="line-height: 1.5em" >Reminders from<br/><span class="text-primary">Clinical Services</span>
                  <br/>
                    <img src="storage/uploads/divider.png" />
                    </h4>

                    <img src="storage/uploads/who_5.png" width="100%" /><br/>
                    <img src="storage/uploads/who_6.png" width="100%" /><br/>
                   
                    
                    
 
 
                    
              </div>

              <div class="item text-center">
                <img src="storage/uploads/oneforhealth_4.jpg" width="100%" />
                  <h4 class="text-orange" style="line-height: 1.5em" >We are #OneForHealth<br/><span class="text-primary">on staying safe</span>
                  <br/>
                    <img src="storage/uploads/divider.png" />
                    </h4>
                    
                    <p style="padding: 30px;" class="text-left">
                     
                   <em>“As someone who helps ensure that our workplace is safe, I’m happy that I can help others feel secure when they go to work. I hope everyone can find their work-life balance in these uncertain times and stay safe. Eating healthy food, washing up daily, wearing face masks when heading out, and limiting our time outside of home or the office can help minimize any risk of infection.<br/><br/>

                    There are times when I work from home and when I need to supervise my team on-site. Together, we make sure that our workplace safety protocols such as installing table partitions to support physical distancing are being properly implemented. We’ve also significantly reduced overtime work to maintain work-life balance during quarantine.<br/><br/>

                    While it’s difficult for many of us not to see our loved ones, having access to today’s technology makes it possible to spend time with them despite the distance.”</em><br/><br/>

                    Facilities Manager Ronaldo, fondly called as “Engineer Ronald” by his workmates, talks about his role in ensuring safety within Open Access BPO’s office premises and how he protects himself against COVID-19 in his everyday life.<br/><br/>
                    *******************************<br/><br/>
                    We are #OneForHealth is a series from Open Access BPO that tackles how our employees keep themselves and those around them healthy and safe during the pandemic. We hope this can serve as a reminder for everyone to take an active role as we combat COVID-19.<br/><br/>

                    Understand more about health and safety guidelines, visit World Health Organization (WHO)’s website: https://tinyurl.com/whohealthmeasures.
                  </p>    
 
 
                    
              </div>

              <div class="item text-center" >
                
                  <h4 class="text-orange" style="line-height: 1.5em" >Infographic:<br/><span class="text-primary">Enabling Continuous Operations <br/>During Covid-19</span>
                  <br/>
                    <img src="storage/uploads/divider.png" />
                    </h4>
                    <img src="storage/uploads/infographic_covid.jpg" width="100%" />
                    <br/> <a class="btn btn-primary" href="https://www.openaccessbpo.com/blog/infographic-how-open-access-bpo-is-ensuring-continuous-ops-during-covid-19/" target="_blank"> <i class="fa fa-external-link"></i> Read Blog Post</a>
              </div>

             

              
              <div style="background: url('storage/uploads/memobg.png')top left repeat-y; background-size: 50%;background-color: #fff;padding:20px" class="item  text-center" >
                  <h4 class="text-orange" ><i class="fa fa-exclamation-triangle"> </i> MANDATORY <br/>DAILY HEALTH DECLARATION FORM <span class="text-primary"> <br/>No Health Declaration, Strictly No Entry<br/>
                 <br/>
                <img src="storage/uploads/safetyfirst.png" width="40%" /><br/>
                    <img src="storage/uploads/divider.png" /><br/>
                    
                    <!-- <h3 class="text-danger"><i class="fa fa-medkit"></i> Health Alert <i class="fa fa-medkit"></i> </h3> -->
                    <p style="padding: 50px;font-size: smaller;line-height: 1.2em" class="text-left">
                    <strong class="text-primary"> Why</strong><br/>
                    To ensure real-time and daily employee accountability, transparency, and safety - its completion is a must before entering the company shuttle and office building. This is part of the safety measures we are implementing to attest that workers entering our premises are clear health-wise and are not at risk of any infection. This is to prevent further outbreaks, and this is to protect you, your family, your workmates, our whole company, and the communities we live in.  For your strict compliance, every day.<br/><br/>

                    <strong class="text-primary"> Who</strong><br/>
                    This is to be filled out by <strong class="text-danger">ALL ONSITE WORKERS</strong> each and every day before entering the shuttle and the office building – shuttlers and non-shuttlers alike.<br/><br/>

                    <strong class="text-primary"> Where to fill out</strong><br/>
                    On your browser before you leave home<br/><br/>
                    <strong>For shuttlers:</strong> <br/>Click the Health Declaration link via the Daily Trip Notification email or paste this on your browser right before leaving home.<br/>
                    <input class="form-control" type="text" id="bundylink" value="https://www.emailmeform.com/builder/form/e0drGbexiXbTw391N" />
                    <button class="cp btn btn-xs btn-primary" data-link="bundylink">Copy Link <i class="fa fa-external-link"></i></button>
                     <br/><br/>

                    <strong>For non-shuttlers:</strong> <br/>
                    Online Health Declaration <br/>
                    <input class="form-control" type="text" id="videolink" value="https://www.emailmeform.com/builder/form/e0drGbexiXbTw391N" />
                    <button class="cp btn btn-xs btn-primary" data-link="videolink">Copy Link <i class="fa fa-external-link"></i></button><br/><br/>
                     
                    <strong>Last resort:</strong> Before entering Open Access, via EMS on the allocated device in the security area before the entrance.<br/><br/>

                    <strong class="text-primary"> When to fill out</strong><br/>
                    <strong>Required completion date </strong><br/>
                    On the ACTUAL DAY of your shift
                    Advance and late completion are not allowed.<br/><br/>

                    <strong> Required completion time</strong><br/>
                    For shuttlers – BEFORE hopping on the service vehicle<br/>
                    For non-shuttlers – BEFORE entering the office building

                    
                    

                    
              </div>

             

              <div class="item text-center" >
                <img src="storage/uploads/oneforhealth_3.jpg" width="100%" />
                  <h4 class="text-orange" style="line-height: 1.5em" >We are #OneForHealth<br/><span class="text-primary">on staying safe</span>
                  <br/>
                    <img src="storage/uploads/divider.png" />
                    </h4>
                    
                    <p style="padding: 30px;" class="text-left">
                     
                   <em>“I almost lost my job amidst the pandemic. Our client's operations were hit by the crisis, so our company immediately endorsed me to a new program. I started training and began working with my new team—all while I was in the comfort of my home.<br/><br/>

                    More than anything, the pandemic made me miss my mom so much. I usually visit her on my rest days, but we weren’t even able to celebrate her birthday last March.<br/><br/>

                    For those who are away from their loved ones, I understand how much you long for them. But it’s really important for us to practice physical distancing to protect ourselves and those close to our hearts.” </em><br/><br/>

                    Email Support agent Ann shares about how the pandemic affected her job and how she’s trying to cope despite the distance between her and her loved ones.<br/><br/>
                    *******************************<br/><br/>
                    We are #OneForHealth is a series from Open Access BPO that tackles how our employees keep themselves and those around them healthy and safe during the pandemic. We hope this can serve as a reminder for everyone to take an active role as we combat COVID-19.<br/><br/>

                    Understand more about health and safety guidelines, visit World Health Organization (WHO)’s website: https://tinyurl.com/whohealthmeasures.
                  </p>    
 
 
                    
              </div>

              <div class="item text-center" >
                  
                  
                  <img src="storage/uploads/oneforhealth_2.jpg" width="100%" />
                  <h4 class="text-orange" style="line-height: 1.5em" >We are #OneForHealth<br/><span class="text-primary">on staying safe</span>
                  <br/>
                    <img src="storage/uploads/divider.png" />
                    </h4>
                    
                    <p style="padding: 30px;" class="text-left">
                     
                   <em>“Whenever I pass through the hallways, I carefully keep my distance from others. There are asymptomatic cases of COVID-19 and the risk of droplet transmission only increases if we do not properly observe physical distancing.<br/><br/>

                    Notices around the office also help remind me to constantly practice physical distancing and to sanitize my hands.”<br/><br/></em>

                    Japanese Content Moderation Analyst Nozawa shares his experience of working in the office during quarantine and how he personally observes protective measures against COVID-19.<br/><br/>
                    *******************************<br/><br/>
                    We are #OneForHealth is a series from Open Access BPO that tackles how our employees keep themselves and those around them healthy and safe during the pandemic. We hope this can serve as a reminder for everyone to take an active role as we combat COVID-19.<br/><br/>

                    Understand more about health and safety guidelines, visit World Health Organization (WHO)’s website: https://tinyurl.com/whohealthmeasures.
                  </p>    
 
 
                    
              </div>

              <div class="item text-center" >
                  
                  
                  <img src="storage/uploads/oneforhealth_1.jpg" width="100%" />
                  <h4 class="text-orange" style="line-height: 1.5em" >We are #OneForHealth<br/><span class="text-primary">on staying safe</span>
                  <br/>
                    <img src="storage/uploads/divider.png" />
                    </h4>
                    
                    <p style="padding: 30px;" class="text-left">
                     
                   Events Manager Jackie talks about the importance of following basic guidelines such as proper wearing of face masks/PPEs, hand sanitation, and respiratory hygiene in protecting ourselves and those around us against COVID-19:<br/><br/>

                  <em>“There’s still very little that we know about this virus so I find it essential to protect myself and those around me at all times. These are the simplest things we can do and contribute to not spread the virus and get sick. That’s why I'm following these measures and always encouraging those around me to do the same, especially when going outside.

                  We had a death of a loved one during this pandemic. Losing someone in a time like this is devastating. We didn’t even get to say our goodbyes. I don't want anyone to experience the same thing so let’s still be cautious even if we’re now on GCQ. We should strictly observe all safety guidelines in and out of our homes."</em><br/><br/>
                    *******************************<br/><br/>
                    We are #OneForHealth is a series from Open Access BPO that tackles how our employees keep themselves and those around them healthy and safe during the pandemic. We hope this can serve as a reminder for everyone to take an active role as we combat COVID-19.<br/><br/>

                    Understand more about health and safety guidelines, visit World Health Organization (WHO)’s website: https://tinyurl.com/whohealthmeasures.
                  </p>    
 
 
                    
              </div>

              <div style="background: url('storage/uploads/memobg.png')top left repeat-y; background-size: 50%;background-color: #fff;padding:20px" class="item  text-center" >
                  <h4 class="text-orange" style="line-height: 1.5em" ><i class="fa fa-info-circle"></i> GCQ: <br/><span class="text-primary">Transitioning to the New Normal <br/>
                   
                  
                    <img src="storage/uploads/divider.png" />
                    </h4>
                    <p style="padding: 30px;" class="text-left">
                     
                    Dear Open Access Family,<br/><br/>

 

                      Since GCQ has been declared (May 16 for Davao and June 1 for Manila), know that we are taking a very prudent stance in transitioning back to our workplace. While the business dictates what programs go back in phases, we will be as careful as we need to be. Employee health and safety are paramount, hence the primary focus of our efforts and basis of our decisions will be to balance these with business needs.
                     <br/><br/>
                       

                      We take a guarded approach to continuing operations in Davao and G2 and reopening Jaka/6780.  While we will maintain part of our labor force working remotely and do so as long as we can, we are starting to proceed with the incremental increase of workers returning onsite.  Check with your program and department heads if and when your teams will be transitioning. We want everyone to be absolutely responsible and heedful while doing so.<br/><br/>

                       

                       

                      <strong class="text-primary"> Recap ECQ</strong><br/>

                      <strong>We have done all we can to ensure you were safe and productive during ECQ </strong><br/>

                      While many U.S. companies have needed to reduce their workforces or completely shut down, we are very fortunate many of our clients have remained stable and operational. This put us in a position to be able to support everyone and remaining operational, while on community quarantine. From deploying multiple company assets to enable you to work sheltered from your homes and allowing flexibility with work hours and leaves where needed, to providing free near-site and onsite accommodation, including provisions for free food, free roundtrip door-to-door transportation, as well as quarantine incentives; to adopting new corporate policies and procedures for health and safety, to communication blitzes for education on disease prevention.<br/><br/>

                       

                      <strong class="text-primary"> On to the New Normal: GCQ Onward</strong><br/>

                      <strong>Navigate through less restrictions and increased movement in GCQ with even more vigilance</strong><br/>

                      Let us not waste all the efforts in keeping our team safe and healthy over the last three months, by carelessly exposing ourselves to undue risk. Stay home if you’re working from there. Limit movement strictly to essentials as if your life and work depended on doing so.
                      <br/><br/>
                       

                      Let’s be grateful for all the company has done by committing to support our drive for personal accountability to protect our lives and our livelihood.  We need you to take responsibility for your own health and safety, be overly cautious and disciplined at all times, in and out of the office.<br/><br/>

                       

                      Please take time to read through our <a class="text-success" href="oampi-resources/item/67" target="_blank"><strong> transition strategy </strong></a> uploaded resource document. Thank you for your continued commitment.<br/><br/>

                       

                      In the meantime, be safe, team.  Be responsible. Be smart. Stay healthy<br/><br/>
                      All the best,<br/>
                      <strong>Joy</strong>
 
                    
              </div>
              


              <div style="background: url('storage/uploads/memobg.png')top left repeat-y; background-size: 50%;background-color: #fff;padding:20px" class="item  text-center" >
                  <h4 class="text-orange" style="line-height: 1.5em" ><i class="fa fa-exclamation-triangle"></i> Updated Workplace <br/>Health and Safety Policy  <br/> <span class="text-primary">   for the prevention and <br/>control of COVID-19 <br/>
                    <img src="storage/uploads/safetyfirst.png" width="40%" /><br/>
                  
                    <img src="storage/uploads/divider.png" />
                    </h4>
                    <p style="padding: 30px;" class="text-left">
                     
                    ATTENTION TO ALL:    <br/><br/>

                    We are reminding everyone to comply with our health and safety protocols such as the requirement to: <br/><br/>
                    Accomplish the Health Declaration Form prior to going to work.<strong class="text-primary">STAY HOME IF YOU ARE SICK. </strong> <br/><br/>
                    Wear a face mask and have your temperature taken prior to entering the office premises. <br/><br/>
                    Practice physical distancing and respiratory etiquette. <br/><br/>
                    The maximum penalty for non-compliance with the health and safety protocols is termination. <br/><br/> <br/><br/>

                    For guidance, we have outlined scenarios on how our health protocols will generally be implemented: <br/><br/>

                    <strong class="text-primary"> 1. Building Lobby</strong><br/>
                    If temperature is above 37.5C, you will no longer allowed by the building admin to proceed to the office. If necessary, the nurse will go to the lobby and confirm your temperature/assess before you sending home. <br/><br/>

                    <strong class="text-primary"> 2.  At the office reception prior to entering the production area</strong><br/>
                    Our guards will check your temperature. If above 37.5C or if you exhibit other symptoms, you will not be allowed to enter the office. The nurse will go to the reception area and assess before sending you home. <br/><br/>

                    <strong class="text-primary"> 3. During work</strong> <br/>
                    If you feel unwell during your shift, you are required to proceed to the  nearest designated area (isolation room or clinic) from your workstation. The nurse will  assess before sending you home.  <br/><br/>

                    <strong class="text-primary"> 4. Emergency Room Transport, if necessary after Nurse assessment.</strong><br/>
                    The nurse will explain and set expectations on the need for immediate ER transfer (nearest hospital is Makati Medical Center). You may notify a family member about the situation. No one can join in the elevator except for the nurse. Upon arrival in the ER, the nurse will properly endorse the patient to the hospital ER personnel. <br/><br/>
                    Thank you.
 
                    
              </div>

              <div style="background: url('storage/uploads/memobg.png')top left repeat-y; background-size: 50%;background-color: #fff;padding:20px" class="item text-center" >
                  <h4 class="text-orange" style="line-height: 1.5em" > Message from Finance: <br/> <span class="text-primary"><i class="fa fa-lock"></i>   DTR Locking in EMS <br/>
                  
                    <img src="storage/uploads/divider.png" />
                    </h4>
                    <p style="padding: 30px;" class="text-left">
                     
                    Dear All,<br/><br/>
                    
                    Please be informed that we will strictly implement the 24-hour rule in locking your DTR in EMS. This is for the Timekeeping team to regularly upload your approved change work schedule, DTRPs, leaves, and overtime from EMS to Jeonsoft for payroll computation purposes.<br/><br/>
                    <strong class="text-danger"> DTRs should be locked within 24 hours after your shift</strong>, except on the last working day of the cut-off which should be locked the following day on or before 12PM. The change work schedule, DTRPs, leaves, and overtime should be approved by the immediate head within the same date such filings were applied and filed. Once all the filings are approved and reflected, you may now lock your DTR.  We will then assume that your DTR is correct and reviewed by yourself.<br/><br/>

                    If you have questions, please feel free to email salaryinquiry@openaccessbpo.com via Gmail or salaryinquiry@openaccessbpo.net via Zimbra.<br/><br/>

                    Please be guided accordingly. Thank you.
 
                    
              </div>

              

             
              

             

             
            

              

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

              

            

             

             

            
              

              <div style="background: url('storage/uploads/memobg.png')top left repeat-y; background-size: 50%;background-color: #fff;padding:20px" class="item  text-center" >
                  <h4 class="text-orange" style="line-height: 1.5em" > Attention: <br/> <span class="text-primary"><i class="fa fa-users"></i> All Open Access BPO Leaders<br/>
                  
                    <img src="storage/uploads/divider.png" />
                    </h4>
                    <p style="padding: 30px;" class="text-left">
                     
                    Hello Open Access BPO Leaders,<br/><br/>

                    Managing co-located teams is in itself a challenging task, so much more now that our teams have transitioned to a remote setup. We developed <strong class="text-primary"> ALERT,</strong> an eLearning course to help you lead your remote teams. It stands for <strong class="text-primary"> Adeptly Leading Effective Remote Teams</strong>. Part 1 will be released tomorrow. We hope that you'll find this learning resource helpful. Here's a peek at the topics covered:<br/><br/>
                    - unique challenges of remote teams<br/>
                    - remote team leader competencies<br/>
                    - establishing team principles<br/>
                    - establishing trust and accountability<br/><br/>
                    Expect another email from <strong>system@open-access.training-online.eu</strong> tomorrow. This will be triggered once your Learning Management System (LMS) user accounts are created tomorrow. There will be a redirect link in the message. Once you are redirected to the LMS page, click on the login button to set your new password, as shown below.<br/>
                    <img src="storage/uploads/trainingimg.png" width="95%" />
                    <br/><br/> 

                   

                    </p> 


 
                    
              </div>

              <div style="background: url('storage/uploads/memobg.png')top left repeat-y; background-size: 50%;background-color: #fff;padding:20px" class="item text-center" >
                  <h4 class="text-orange" style="line-height: 1.5em" > Memo: <br/> <span class="text-primary"><i class="fa fa-file"></i> ASSET DEPLOYMENT PROCESS <br/>FOR AT-HOME-WORKERS<br/><br/>
                  
                    <img src="storage/uploads/divider.png" />
                    </h4>
                    <p style="padding: 30px;" class="text-left">
                     
                    <strong>Enabling WFH for Open Access Employees Procedures and User Guide</strong><br/><br/>
                    This document outlines the policies and procedures for setting up employees to work from home utilizing company-issued equipment during the ECQ period.<br/><br/> 

                    <a href="oampi-resources#resource_8" class="btn btn-sm btn-primary" target="_blank"><i class="fa fa-book"></i> Read Memo &amp; Guidelines </a>

                    </p> 


 
                    
              </div>

              <div class="item text-center" >
                  <h4 class="text-orange" style="line-height: 1.5em" > Reminders: <br/> <span class="text-primary"><i class="fa fa-newspaper"></i>   Tips and Tricks on How to Stay Productive
                  <br/>
                  <img src="storage/uploads/remotework.jpg" width="100%" /><br/><br/>
                    <img src="storage/uploads/divider.png" />
                    </h4>
                    
                    <p style="padding: 30px;" class="text-left">
                     
                   
                      Reminding everyone that the deadline for completing <a href="https://rise.articulate.com/share/OZZ5OikfamvlE5SqiPqAnRwJYJl0cf4P#/" target="_blank"> Remote Work: Tips and Tricks on How to Stay Productive</a> is this coming Sunday, April 27, 2020.<br/><br/>

                      Take note that you need to respond to the chatbot LEIA before ending the module so we can capture your completion information. Refer to the image below.<br/><br/>

                      Kudos to the following teams for leading the completion ranking per group:<br/><br/>
                      <strong><a target="_blank" href="{{action('CampaignController@show',19)}}"> Data Management</a> </strong>- 90% <br/>
                      <strong><a target="_blank" href="{{action('CampaignController@show',15)}}"> Workforce</a></strong> - 77.78% <br/>
                      <strong><a target="_blank" href="{{action('CampaignController@show',16)}}"> Marketing</a></strong> - 75% <br/><br/>
                      Happy weekend everyone. Stay safe!</p> 



                    
 
                    
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

              

              


              <div style="background: url('storage/uploads/memobg.png')top left repeat-y; background-size: 50%;background-color: #fff;padding:20px" class="item  text-center" >
                  <h4 class="text-orange" style="line-height: 1.5em" > To all Onsite Employees: <br/> <span class="text-primary"><i class="fa fa-building"></i>   ECQ G2 Office Guidelines <br/><br/>
                   <img src="storage/uploads/warning.png" width="98%" /><br/><br/>
                    <img src="storage/uploads/divider.png" />
                    </h4>
                    <p style="padding: 30px;" class="text-left">
                     
                    To reiterate our previous email reminders, it is imperative to observe the following:<br/><br/>


                    1. <strong class="text-danger"> NO LOITERING </strong>outside the office premises and building.<br/><br/>
                    2. <strong class="text-danger">NO SMOKING</strong> in non-designated areas.  <br/><br/>
                    3. <strong class="text-danger">AVOID going in and out</strong> of the office building.<br/><br/>
                     <i class="fa fa-exclamation-triangle text-orange"></i> Stay inside the lobby while waiting for the shuttle service.<br/><br/>
                    
                    <i class="fa fa-exclamation-triangle text-orange"></i> ALWAYS wear a mask inside the office premises, going to/from office, or while outside or in public places.<br/><br/>
                     <i class="fa fa-exclamation-triangle text-orange"></i> ALWAYS follow the physical distancing inside the office premises, outside the office, at shuttle service and public places.<br/><br/>
                     

                    In as much as BPO is one of the essential services that are open, it does not mean that we are at liberty of movement. During this ECQ period, movement of BPO employees are allowed only for the purpose of going to/from the office. .In order to address the Makati City Police PCP5 concerns on the volume of personnel loitering around Ayala Center, the G2 Building Admin will be issuing a limited number of passes to all BPO tenants.  For onsite employees, there will be an assigned Housekeeping Personnel with passes who can accommodate requests for emergency/urgent essential purchases within the office vicinity.  <br/><br/>

                     

                    The Ayala Center Estate Association (ACEA) will report all those who are found loitering, smoking or doing unnecessary activities within Ayala Center and the G2 Building Admin may prevent violators from returning to the G2 building until after the ECQ is lifted. Likewise, the City of Makati prohibits among others loitering in public places within the ECQ period.<br/><br/>

                    Further, attached is the Makati City Ordinance No. 2020-089, requiring the mandatory wearing of face masks and other similar protective equipment penalizing its violation thereof.<br/><br/>

                    Please be guided accordingly.


                    

                    </p>  
                    
              </div>

            
             

              <div style="padding:0px" class="item text-center" >
                 <!--  <h4 class="text-orange" style="line-height: 1.6em" > Happy<br/> <span class="text-primary">Easter!<br/> -->
                   <h4 class="text-orange" style="line-height: 1.6em" > <img src="storage/uploads/doh.jpg" width="100%"><br/>
                    <img src="storage/uploads/divider.png" />
                    </h4>
                    <p style="padding: 30px;" class="text-left">
                     
                     DOH launched the DOH COVID-19 emergency hotlines 02-894-COVID (02-894-26843) and 1555 in partnership with the National Emergency Hotline of the DILG, and PLDT and its wireless subsidiary Smart Communications Inc.<br/><br/>

                     Through the hotline, callers can ask questions if they suspect they are infected with COVID-19, or request assistance if they have symptoms and/or known exposure to confirmed cases of patients under investigation.<br/><br/>

                    For more information, visit:  https://www.doh.gov.ph/doh-press-release/doh-launches-covid-19-hotline
                    

                    </p>  
                    
              </div>

              <div style="padding:0px" class="item  text-center" >
                  <h4 class="text-orange" style="line-height: 1.6em" > Message from <br/> <span class="text-primary"> Leadership Development</span><br/>
                   <h4 class="text-orange" style="line-height: 1.6em" > 
                    <img src="storage/uploads/elearning.png" width="100%"><br/>
                    <img src="storage/uploads/divider.png" />
                    </h4>
                    <p style="padding: 30px;" class="text-left">
                     
                     Please take a few minutes to go through this e-learning module: <strong><a href="https://rise.articulate.com/share/OZZ5OikfamvlE5SqiPqAnRwJYJl0cf4P" target="_blank"> Remote Work: Tips and Tricks on How to Stay Productive</a></strong><br/><br/>

                      It has been designed to guide at-home workers to effectively accomplish our jobs and manage our productivity in our new current workspace - our own homes. Many of these are useful also for those working as a skeletal workforce in the office. A host of practical tips and tools were consolidated here to help make self-management and working from home efficient, and even inspirational and fun while keeping safe and healthy.<br/><br/>

                      <a target="_blank" href="https://rise.articulate.com/share/OZZ5OikfamvlE5SqiPqAnRwJYJl0cf4P" class="btn btn-md btn-success"><i class="fa fa-book"></i> Read Now</a>.<br/><br/>
                      Happy Learning!
                    

                    </p>  
                    
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

             

      

                

                <div style="background: url('storage/uploads/memobg.png')top left repeat-y; background-size: 50%;background-color: #fff;padding:20px" class="item text-center" >
                  <h4 class="text-orange" >Attention: <br/> <span class="text-primary"> <i class="fa fa-info-circle-o"></i>All Shuttle/Carpool Riders<br/> <br/>
                   
                   
                  
                    <img src="storage/uploads/facemask.png" width="80%" /><br/> 
                    <img src="storage/uploads/divider.png" /><br/>
                    </h4>
                    
                    <p style="padding: 50px;" class="text-left">
                      <strong class="text-danger"><i class="fa fa-exclamation-triangle"></i> FACIAL MASKS ARE REQUIRED TO GET INTO THE VEHICLE</strong> --- LGU'S have made it mandatory for citizens to wear masks when in public so this will be inspected in checkpoints.  Hence our company is enforcing this in the office as well as in our company shuttles on the way to our workplace.<br/><br/>

                      For strict compliance.  Please comply and come prepared - so as not to endanger others or delay your shared trip.<br/><br/>

                      Washable masks are available at our clinic. Request for one, if needed.



                    </p>

                   
                    
                </div>

               

                <div style="background: url('storage/uploads/memobg.png')top left repeat-y; background-size: 50%;background-color: #fff;padding:20px" class="item text-center" >
                  <h4 class="text-orange" >Reminders: <br/> <span class="text-primary"> <i class="fa fa-info-circle-o"></i>MANDATORY <br/>BASIC PROTECTIVE MEASURES FOR THE WORKPLACE <br/>
                    <span style="font-size: small;">World Health Organization COVID19 Advice for the Public</span>

                  <br/>
                 <br/>
                    <img src="storage/uploads/divider.png" /><br/>
                    <img src="storage/uploads/who.png" width="60%" />
                    </h4>
                    
                    <!-- <h3 class="text-danger"><i class="fa fa-medkit"></i> Health Alert <i class="fa fa-medkit"></i> </h3> -->
                    <p style="padding: 50px;" class="text-left"><br/>
                     For workforce health and safety, it is critical that basic protective measures be taken at all times by our skeletal workforce, office dwellers, and temporary accommodation stayers.<br/></br/>


                      <strong class="text-primary"> Wash your hands frequently</strong></br/>
                      Regularly and thoroughly clean your hands with an alcohol-based hand rub or wash them with soap and water.
                      Why? Washing your hands with soap and water or using alcohol-based hand rub kills viruses that may be on your hands.<br/></br/>

                      <strong class="text-primary"> Maintain social distancing</strong></br/>
                      Maintain at least 1 meter (3 feet) distance between yourself and anyone who is coughing or sneezing.
                      Why? When someone coughs or sneezes, they spray small liquid droplets from their nose or mouth which may contain virus. If you are too close, you can breathe in the droplets, including the COVID-19 virus if the person coughing has the disease.<br/></br/>

                      <strong class="text-primary"> Avoid touching eyes, nose and mouth</strong></br/>
                      Why? Hands touch many surfaces and can pick up viruses. Once contaminated, hands can transfer the virus to your eyes, nose or mouth. From there, the virus can enter your body and can make you sick.<br/></br/>

                      <strong class="text-primary"> Practice respiratory hygiene</strong></br/>
                      Make sure you, and the people around you, follow good respiratory hygiene. This means covering your mouth and nose with your bent elbow or tissue when you cough or sneeze. Then dispose of the used tissue immediately.
                      Why? Droplets spread virus. By following good respiratory hygiene, you protect the people around you from viruses such as cold, flu and COVID-19.<br/></br/>

                      <strong class="text-primary"> If you have fever, cough and difficulty breathing, seek medical care early</strong></br/>
                      Stay home if you feel unwell. If you have a fever, cough and difficulty breathing, seek medical attention and call in advance. Follow the directions of your local health authority.
                      Why? National and local authorities will have the most up to date information on the situation in your area. Calling in advance will allow your health care provider to quickly direct you to the right health facility. This will also protect you and help prevent spread of viruses and other infections.




                    </p>

                   
                    
                </div>

               

                <div style="background: url('storage/uploads/memobg.png')top left repeat-y; background-size: 50%;background-color: #fff;padding:20px" class="item text-center" >
                  <h4 class="text-orange" >Note:<span class="text-primary"> <i class="fa fa-info-circle-o"></i><br/>Updated Payroll Calendar 2020<br/>
                 <br/>
                    <img src="storage/uploads/payday.png" width="60%"> <br/><br/>
                    <img src="storage/uploads/divider.png" /><br/><br/>

                    
                    
                    <!-- <h3 class="text-danger"><i class="fa fa-medkit"></i> Health Alert <i class="fa fa-medkit"></i> </h3> -->
                    <table class="table table-bordered text-center" style="font-size: smaller; width: 93%" align="center">
                      <thead>
                        <tr>
                          <th>Month</th>
                          <th class="bg-orange">New Cutoff</th>
                          <th class="bg-green">New Payday</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td>April</td>
                          <td>Apr 03 (Fri)</td>
                          <td>Apr 08 (Wed)</td>
                        </tr>

                        <tr>
                          <td>May</td>
                          <td>May 03 (Sun)</td>
                          <td>May 08 (Fri)</td>
                        </tr>

                        <tr>
                          <td>October</td>
                          <td>Oct 18 (Sun)</td>
                          <td>Oct 23 (Fri)</td>
                        </tr>

                        <tr>
                          <td>December</td>
                          <td>Dec 18 (Fri)</td>
                          <td>Dec 23 (Wed)</td>
                        </tr>

                        <tr>
                          <td>January 2021</td>
                          <td>Jan 03, 2021 (Sun)</td>
                          <td>Jan 08, 2021 (Fri)</td>
                        </tr>


                      </tbody>
                    </table>

                    <p style="padding: 50px;font-size: x-small;line-height: 1.2em" class="text-left">

                      Please take note of the following changes in our cutoff and payout dates for the following months:<br/><br/>
                      <em> * note that our regular cutoff period is every 5th and 20th day of the month, and every 10th &amp; 25th for our payout dates)</em> 
                      <br/><br/><br/>
                    </p>


                    
                </div>
                


                <div style="background: url('storage/uploads/memobg.png')top left repeat-y; background-size: 50%;background-color: #fff;padding:20px" class="item text-center" >
                  <h4 class="text-orange" >Workplace Health and Safety Policy<span class="text-primary"> <i class="fa fa-info-circle-o"></i><br/>for: Onsite Employees &amp; Office Dwellers<br/>
                 <br/>
                    <img src="storage/uploads/companynurse.jpg" width="80%" /> <br/><br/>
                    <img src="storage/uploads/divider.png" /><br/>
                    
                    <!-- <h3 class="text-danger"><i class="fa fa-medkit"></i> Health Alert <i class="fa fa-medkit"></i> </h3> -->
                    <p style="padding: 50px;font-size: smaller;line-height: 1.2em" class="text-left">

                      All employees should practice social responsibility by monitoring their health condition. If an employee is sick or has flu-like
                      symptoms such as fever, cough, shortness of breath, fatigue, sore throat, headache, chills, nausea or nasal congestion, or has
                      had history of exposure, compliance with the procedures is mandatory:</p>

                    <ol type="1"style="padding-left: 50px;font-size: smaller;line-height: 1.2em" class="text-left">
                      <li>The employee must promptly notify the Nurse on duty. If no nurse is onsite, the employee is directed
                           immediately call the Nurses (contact information found in Section V)<br/><br/></li>
                      <li>The Nurse will assess the employee and, if necessary, transfer the employee to the designated holding
                           area or isolation room. The Immediate Head has the authority to transfer the employee to the designated
                           isolation room pending the assessment of the Nurse.<br/><br/></li>
                      <li>If symptoms are mild, the employee will be advised to seek medical consult via Maxicare TeleConsult.
                           If there are notable symptoms like fever, difficulty of breathing or prolonged cough, the employee will
                           be advised to immediately seek hospital consult. In either case, the employee will be required to go
                           home or stay at the designated temporary housing.<br/><br/></li>
                      <li> If self-quarantine has been advised, the employee must isolate for 14 days. If hospitalization is
                           recommended, employee is required to be confined.
                            The employee must provide daily updates to the Nurse (oamnurse@openaccessbpo.com or call/SMS
                           09178960634).<br/><br/></li>
                      <li>After 14 days of quarantine or discharge from hospitalization, the employee must seek post
                           quarantine/post-admission medical consult and submit a medical certificate/fit to work certificate. Post
                           consultation may be via Maxicare Teleconsult/Clinic consult. Report or documentation should be sent to
                           the Nurses. <br/>(note: Teleconsultations provide email reports and may be requested by the patient before
                           ending the call.)<br/><br/></li>
                      <li>After medical certificate/fit to work verification, the Nurse will notify the employee’s Immediate Head
                          and will send the appropriate report.</li>


                    </ol>

                    <a href="resource#resource_6" class="btn btn-success btn-md"><i class="fa fa-book"></i> Read More</a>


                    
                </div>

                <div style="background: url('storage/uploads/memobg.png')top left repeat-y; background-size: 50%;background-color: #fff;padding:20px" class="item text-center" >
                  <h4 class="text-orange" >Office Dwellers:<span class="text-primary"> <i class="fa fa-info-circle-o"></i><br/>Shower Room Etiquette<br/>
                 <br/>
                   <img src="storage/uploads/shower.png" width="50%" /><br/><br/>
                    <img src="storage/uploads/divider.png" /><br/>
                    
                    <!-- <h3 class="text-danger"><i class="fa fa-medkit"></i> Health Alert <i class="fa fa-medkit"></i> </h3> -->
                    <p style="padding: 50px;font-size: smaller;line-height: 1.2em" class="text-left">

                      Since the Men's Shower Room may no longer be used, office dwellers may take turns using the Ladies Shower Room. To be able to accommodate everyone, the allocated shower time is <strong class="text-danger">strictly 20 minutes per user</strong><br/><br/>
                      As a courtesy to all the other users, please stick to the time, return the key to the reception guard on time and CAYG.<br/><br/>
                      Let's take good care of our office facilities as this provides a safe dwelling for us during this ECC period. Thank you.
                      <br/><br/><br/>
                    Open Access BPO Management</p>


                    
                </div>

                

                <div style="background: url('storage/uploads/memobg.png')top left repeat-y; background-size: 50%;background-color: #fff;padding:20px" class="item text-center" >
                  <h4 class="text-orange" >Workplace Health and Safety Policy<span class="text-primary"> <i class="fa fa-info-circle-o"></i><br/>for: Employees Using CARPOOL/ SHUTTLE SERVICE FOR WORK<br/>
                 <br/>
                    <img src="storage/uploads/carpool.png" width="80%" /> <br/><br/>
                    <img src="storage/uploads/divider.png" /><br/>
                    
                    <!-- <h3 class="text-danger"><i class="fa fa-medkit"></i> Health Alert <i class="fa fa-medkit"></i> </h3> -->
                    <p style="padding: 50px;font-size: smaller;line-height: 1.2em" class="text-left">

                      All employees should practice social responsibility by monitoring their health condition. If an employee is sick or has flu-like
                      symptoms such as fever, cough, shortness of breath, fatigue, sore throat, headache, chills, nausea or nasal congestion, or has
                      had history of exposure, compliance with the procedures is mandatory:</p>

                    <ol type="1"style="padding-left: 50px;font-size: smaller;line-height: 1.2em" class="text-left">
                      <li>  The employee should stay at home and immediately notify the Nurse two hours prior to the scheduled
                        shift. (contact information found in Section V)<br/><br/></li>
                      <li>After assessment, the employee will be required to seek medical consult.<br/><br/></li>
                      <li>If self-quarantine has been advised, the employee should stay home for the 14-days.<br/><br/></li>
                      <li>If hospitalization is recommended, employee is required to be confined.<br/><br/></li>
                      <li>The employee must provide daily updates to the Nurse (oamnurse@openaccessbpo.com or call/SMS
                         09178960634).<br/><br/></li>
                      <li>After 14 days of quarantine or discharge from hospital, the employee must seek post-quarantine/post-
                         admission medical consult and submit a medical certificate/fit to work certificate. Consultation may be
                         via Maxicare Teleconsult/Clinic consult. Report or documentation should be sent to the Nurses. (note:
                         Teleconsultations provide email reports and may be requested by the patient before ending the call.)<br/><br/></li>
                      <li>After medical certificate/fit to work verification, the Nurse will notify the employee’s Immediate Head
                         and will send the appropriate report.  </li>


                    </ol>

                    <a href="resource#resource_6" class="btn btn-success btn-md"><i class="fa fa-book"></i> Read More</a>


                    
                </div>

               

                <div style="background: url('storage/uploads/memobg.png')top left repeat-y; background-size: 50%;background-color: #fff;padding:20px" class="item text-center" >
                  <h4 class="text-orange" > Supporting our Employees <span class="text-primary"> <i class="fa fa-info-circle-o"></i><br/> through Enhanced Community <br/>Quarantine Period<br/>
                 <br/>
                    <img src="storage/uploads/divider.png" /><br/>
                    
                    <!-- <h3 class="text-danger"><i class="fa fa-medkit"></i> Health Alert <i class="fa fa-medkit"></i> </h3> -->
                    <p style="padding: 50px;font-size: smaller;" class="text-left">Team,<br/><br/>

                        Thank you for the flexibility in adapting to the ever-changing government response and restrictions to help keep our teams and families safe, while enabling us to continue with business operations.<br/><br/>
                        We are permitted to operate a skeletal workforce within our office premises so we can continue service delivery by providing Program Hours of Operations & critical lines of business coverage as deemed necessary by our clients.<br/><br/>
                        <strong class="text-primary"> Check out the following posts about our efforts to help support our employees: <i class="fa fa-arrow-right"></i></strong>
                    </p>

                    
                </div>

                <div style="background: url('storage/uploads/memobg.png')top left repeat-y; background-size: 50%;background-color: #fff;padding:20px" class="item text-center" >
                  <h4 class="text-orange" > Office Dwelling<span class="text-primary"> <i class="fa fa-info-circle-o"></i><br/> <span style="font-size: smaller;"> Employee Registration</span><br/>
                 <br/>
                    <img src="storage/uploads/divider.png" /><br/>
                    
                    <!-- <h3 class="text-danger"><i class="fa fa-medkit"></i> Health Alert <i class="fa fa-medkit"></i> </h3> -->
                    <img src="storage/uploads/officedwell.jpg" width="80%" />
                    <p style="padding: 50px;font-size: smaller;" class="text-left">Team,<br/><br/>

                        To support those who will comprise our skeletal workforce during ECQ, we are offering employees to <strong>temporarily live in our G2 Makati site. </strong><br/><br/>
                        To register, click the button below:<BR/><BR/>


                        <a class="btn btn-lg btn-primary text-center" href="https://docs.google.com/forms/d/e/1FAIpQLSd3PxXcHzD2mFl6BOaMAtmA0GGEdupjBoc23Wvpsvp5ilbWig/viewform?usp=sf_link" target="_blank"><i class="fa fa-home"></i> Register Here</a>
                        <br/><br/>Please note this is an alternative living accommodation option and is not mandatory. 
                    </p>

                    
                </div>

               

               

                


                <div style="background: url('storage/uploads/memobg.png')top left repeat-y; background-size: 50%;background-color: #fff;padding:20px" class="item text-center" >
                  <h4 class="text-orange" > Reminder to Practice: <span class="text-primary"> <i class="fa fa-info-circle-o"></i><br/>SOCIAL DISTANCING <br/>
                    <img src="storage/uploads/divider.png" /><br/>
                    <img src="storage/uploads/distancing.png" width="100%" /></h4>
                    <!-- <h3 class="text-danger"><i class="fa fa-medkit"></i> Health Alert <i class="fa fa-medkit"></i> </h3> -->
                    <p style="padding: 50px" class="text-left">Dear All,<br/><br/>

                        We are implementing strict SOCIAL DISTANCING at our G2 and 6780 sites.  Everyone should maintain a distance of <strong class="text-danger">at least one (1) meter radius apart</strong> during essential work-related meetings and activities.<br/><br/>

                         

                        In general, we can all practice social distancing by:<br/><br/>

                         

                        - Practicing good hand and sneeze/cough hygiene.<br/><br/>
                        - Wearing a facemask if sick.<br/><br/>
                        - Regularly using hand sanitizers or alcohol.<br/><br/>
                        - Holding meetings via video or phone call.<br/><br/>
                        - Reconsidering non-essential travel.<br/><br/>
                         

                        Stay healthy and be safe.<br/><br/><br/><br/>



                        <strong>From HR Department</strong></p>
                </div>

                <div style="background: url('storage/uploads/memobg.png')top left repeat-y; background-size: 50%;background-color: #fff;padding:20px" class="item  text-center" >
                  <h4 class="text-orange" > Attention: <span class="text-primary"> <i class="fa fa-info-circle-o"></i><br/>EMPLOYEES USING COMPANY’S COMPUTERS AND PERSONAL/ISSUED LAPTOPS FOR WORK FROM HOME USE AS PERMITTED <br/><img src="storage/uploads/divider.png" /></h4>
                    <!-- <h3 class="text-danger"><i class="fa fa-medkit"></i> Health Alert <i class="fa fa-medkit"></i> </h3> -->
                    <p style="padding: 50px" class="text-left">For your information and guidance.<br /><br/>

                    Please be informed that there is an urgent need to set your computer time to PH TIME effective immediately for timekeeping purposes.<br /><br/>

                    Check out the IT resource document on <strong>how to set the date and time on your computer and laptop</strong> for your reference. Should there be concerns, please email IT Team at itgroup@openaccessbpo.com.</p>
                    <a href="oampi-resources#resource_7" target="_blank" class="btn btn-success btn-md"><i class="fa fa-book"></i> See How to Change PC clock Settings</a>
                </div>

              

                <div class="item  text-center" >
                  <h4 class="text-orange" >Take the COVID-19 <span class="text-primary"> PREVENTION TRAINING </span></h4>
                    <!-- <h3 class="text-danger"><i class="fa fa-medkit"></i> Health Alert <i class="fa fa-medkit"></i> </h3> -->
                    <img src="./storage/uploads/covid19.jpg" style="z-index: 2" />
                    <br/><br/>
                    <a target="_blank" class="btn btn-md btn-primary" href="https://rise.articulate.com/share/HL1HpxI4bcOo6j0gE16afeNey4gx-ySk"><i class="fa fa-info-circle"></i> LAUNCH TRAINING  </a><br /><br/>
                </div>


             

              

                


                  
                

                 

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

                  

      





               
                

                
              

               

               
              


                    <div class="item text-center" style="background-color: #fff" >
                      
                              <!-- <h4 class="text-orange" >Monday <span class="text-primary"> Motivation </span></h4> -->
                              <img src="./storage/uploads/OALife.jpg" style="z-index: 2" />
                               <p class="text-center" style="padding: 30px;"><br/><br/><strong class="text-primary">Open Access BPO Life</strong> is now up on Facebook!  <br/>
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

          



                      <div class="item  text-center" >
                      <h4 class="text-orange">Timekeeping &amp; Government Concerns<br/><small>Point of Contact</small></h4>
                              
                               <p class="text-left" style="padding-left: 50px;"><br/><br/>
                                <a target="_blank" href="user/262">
                                  <img src="./public/img/employees/262.jpg" class="img-circle pull-left" width="150" style="margin-left: 5px" />
                                  <h4 style="padding-top: 30px">Jomar Domingo <br/><small>(domingo@openaccessbpo.com / jdomingo@openaccessbpo.net)</small></h4></a><br/><br/><br/><br/>
                                  

                                  <ul class="text-left" style="margin-left: 60px">
                                    <li>Admin (Facilities,Finance,HR,Recruitment)</li>
                                    <li>Operation (Jomar's team and Ms. Joy's team)</li>
                                    <li>EDTrainingCenter</li>
                                    <li>IT</li>
                                    <li>Lebua</li>
                                    <li>Marketing</li>
                                    <li>QA & Performance (Reports team)</li>
                                    <li>SKUVantage</li>
                                    <li>Training Department</li>
                                    <li>TurnTo Networks</li>
                                    <li>Leave credits by campaigns</li>
                                  </ul></p><br/><br/><br/><br/>          

                      </div>

                      <div class="item  text-center" >
                      <h4 class="text-orange">Timekeeping &amp; Government Concerns<br/><small>Point of Contact</small></h4>
                              
                               <p class="text-left" style="padding-left: 50px;"><br/><br/>
                                <a target="_blank" href="user/716">
                                  <img src="./public/img/employees/716.jpg" class="img-circle pull-left" width="150" style="margin-left: 5px" />
                                  <h4 style="padding-top: 30px">Lealyn Tiraña  <br/><small>(ltirana@openaccessbpo.com / ltirana@openaccessbpo.net)</small></h4></a><br/><br/><br/><br/>
                                  

                                  <ul class="text-left" style="margin-left: 60px">
                                    <li>Circles.Life</li>
                                    <li>Datascan</li>
                                    <li>Digicast</li>
                                    <li>NDY</li>
                                    <li>SheerID</li>
                                    <li>Workforce</li>
                                    <li>WorldVentures</li>
                                    <li>Zenefits <br/><br/></li>
                                    <li><strong class="text-danger">SSS</strong></li>
                                    <li><strong class="text-danger">Pag-ibig</strong></li>
                                    <li><strong class="text-danger">PhilHealth</strong></li>
                                  </ul></p><br/><br/><br/><br/>          

                      </div>

                      <div class="item  text-center" >
                      <h4 class="text-orange">Timekeeping &amp; Government Concerns<br/><small>Point of Contact</small></h4>
                              
                               <p class="text-left" style="padding-left: 50px;"><br/><br/>
                                <a target="_blank" href="user/1540">
                                  <img src="./public/img/employees/1540.jpg" class="img-circle pull-left" width="150" style="margin-left: 5px" />
                                  <h4 style="padding-top: 30px">Ronalyn Ambrocio <br/><small>(roambrocio@openaccessbpo.com / roambrocio@openaccessbpo.net)</small></h4></a><br/><br/><br/><br/><br/><br/>
                                  

                                  <ul class="text-left" style="margin-left: 60px">
                                    <li>Adoreme</li>
                                    <li>Advance Wellness</li>
                                    <li>Avawomen</li>
                                    <li>Bird</li>
                                    <li>Mayo Clinic</li>
                                    <li>Mous</li>
                                    <li>Postmates</li>
                                    <li>Quora</li>
                                    <li>UiPath</li>
                                    
                                  </ul></p><br/><br/><br/><br/>          

                      </div>

                      <div class="item  text-center" >
                      <h4 class="text-orange">Timekeeping &amp; Government Concerns<br/><small>Point of Contact</small></h4>
                              
                               <p class="text-left" style="padding-left: 50px;"><br/><br/>
                                <a target="_blank" href="user/3044">
                                  <img src="./public/img/employees/3044.jpg" class="img-circle pull-left" width="150" style="margin-left: 5px" />
                                  <h4 style="padding-top: 30px">Marjorie Arias<br/><small>(marias@openaccessbpo.com / marias@openaccessbpo.net)</small></h4></a><br/><br/><br/><br/>
                                  

                                  <ul class="text-left" style="margin-left: 60px">
                                    <li>Glassdoor</li>
                                    <li>IMO</li>
                                    <li>Patch</li>
                                    <li>TaskRabbit</li>
                                    <li>HR (Davao)</li>
                                    <li>IT (Davao)</li>
                                    <li>Facilities (Davao)</li>
                                    
                                    
                                  </ul></p><br/><br/><br/><br/>          

                      </div>

                      <div style="padding:0px" class="item  text-center" >
                        <h4 class="text-orange" style="line-height: 1.6em" > Reminders: <br/> <span class="text-primary">Be #OneForHealth<br/>
                         <img src="storage/uploads/safehome_1.jpg" width="100%"><br/>
                          <img src="storage/uploads/divider.png" />
                          </h4>
                          <p style="padding: 30px;" class="text-left">
                           
                           Let's be #OneForHealth to stop the spread of the COVID-19 virus. Follow these tips to protect yourself and your loved ones!<br/><br/><strong>#WeSpeakYourLanguage</strong>
                          

                          </p>  
                          
                    </div>

                    <div style="padding:0px" class="item  text-center" >
                        <h4 class="text-orange" style="line-height: 1.6em" > Reminders: <br/> <span class="text-primary">Be #OneForHealth<br/>
                         <img src="storage/uploads/safehome_2.jpg" width="100%"><br/>
                          <img src="storage/uploads/divider.png" />
                          </h4>
                           <strong class="text-primary"> DISINFECTING PRACTICES</strong><br/>
                            - Remove and disinfect your footwear before entering your home<br/><br/>
                            - Segregate your used faced masks and gloves from the rest of your trash<br/><br/>
                            - Put the items you used outside (such as bags, phone, glasses, spare change, and keys) in a separate container and disinfect each)<br/><br/>
                          

                          </p>  
                          
                    </div>

                    <div style="padding:0px" class="item  text-center" >
                        <h4 class="text-orange" style="line-height: 1.6em" > Reminders: <br/> <span class="text-primary">Be #OneForHealth<br/>
                         <img src="storage/uploads/safehome_3.jpg" width="100%"><br/>
                          <img src="storage/uploads/divider.png" />
                          </h4>
                           <strong class="text-primary">HYGIENE & LAUNDRY ROUTINE</strong><br/>
                            - Avoid touching surfaces and sitting on chairs or beds if you haven’t washed up<br/><br/>
                            - Soak your used clothes in soapy water or wash them with bleach or disinfectant<br/><br/>
                            - Shower and change your clothes immediately<br/><br/>
                          

                          </p>  
                          
                    </div>


                    <div style="padding:0px" class="item  text-center" >
                        <h4 class="text-orange" style="line-height: 1.6em" > Reminders: <br/> <span class="text-primary">Be #OneForHealth<br/>
                         <img src="storage/uploads/safehome_4.jpg" width="100%"><br/>
                          <img src="storage/uploads/divider.png" />
                          </h4>
                           <strong class="text-primary">CLEAN UP DRILLS</strong><br/>
                            - Use bleach solution or disinfectant to mop floors, clean frequently touched spots, and wipe toilet areas<br/><br/>
                            - Open windows to ventilate your home during clean-ups and whenever possible<br/><br/>
                            - Continue practiciing frequent hand washing<br/><br/>
                          

                          </p>  
                          
                    </div>

                    

                      <div style="padding:0px" class="item  text-center" >
                        <h4 class="text-orange" style="line-height: 1.6em" > Reminders: <br/> <span class="text-primary">Be #OneForHealth<br/>
                         <img src="storage/uploads/grocery_1.png" width="100%"><br/>
                          <img src="storage/uploads/divider.png" />
                          </h4>
                          <p style="padding: 30px;" class="text-left">
                           
                           Let's be #OneForHealth to stop the spread of the COVID-19 virus. Follow these tips to protect yourself and your loved ones!<br/><br/><strong>#WeSpeakYourLanguage</strong>
                          

                          </p>  
                          
                    </div>

                    <div style="padding:0px" class="item  text-center" >
                        <h4 class="text-orange" style="line-height: 1.6em" > Reminders: <br/> <span class="text-primary">Be #OneForHealth<br/>
                         <img src="storage/uploads/grocery_2.png" width="100%"><br/>
                          <img src="storage/uploads/divider.png" />
                          </h4>
                          <p style="padding: 30px;" class="text-left">
                           
                          <strong class="text-primary"> BEFORE SHOPPING</strong><br/>
                            - Take stock of what you already have and plan your daily meals around that<br/><br/>
                            - Make a grocery list of what you’ll need in the next two or three weeks so you don’t have to go out frequently<br/><br/>
                            - Avoid going out if you have respiratory or flu-like symptoms<br/><br/>
                          

                          </p>  
                          
                    </div>

                    <div style="padding:0px" class="item  text-center" >
                        <h4 class="text-orange" style="line-height: 1.6em" > Reminders: <br/> <span class="text-primary">Be #OneForHealth<br/>
                         <img src="storage/uploads/grocery_3.png" width="100%"><br/>
                          <img src="storage/uploads/divider.png" />
                          </h4>
                          <p style="padding: 30px;" class="text-left">
                           
                          <strong class="text-primary"> WHILE SHOPPING</strong><br/>
                            - Wear a face mask and gloves<br/><br/>
                            - Always observe social distancing<br/><br/>
                            - Wipe down your shopping cart handle<br/><br/>
                            - Avoid putting back items you’ve already touched<br/><br/>
                          

                          </p>  
                          
                    </div>


                    <div style="padding:0px" class="item  text-center" >
                        <h4 class="text-orange" style="line-height: 1.6em" > Reminders: <br/> <span class="text-primary">Be #OneForHealth<br/>
                         <img src="storage/uploads/grocery_4.png" width="100%"><br/>
                          <img src="storage/uploads/divider.png" />
                          </h4>
                          <p style="padding: 30px;" class="text-left">
                           
                          <strong class="text-primary"> AFTER SHOPPING</strong><br/>
                            - Dispose of your used face mask and gloves before entering your home<br/><br/>
                            - Disinfect all surfaces that may have come in contact with your groceries<br/><br/>
                            - Remove unnecessary external packaging and move the items to storage containers<br/><br/>
                            - Wash all fruits and vegetables before storing them
                            - Heat food before eating whenever possible<br/><br/>
                          

                          </p>  
                          
                    </div>


                     
                      
                   

                    
                  
                     


                      


                      

                     



<?php /*
 <div style="background: url('storage/uploads/memobg.png')top left repeat-y; background-size: 50%;background-color: #fff;padding:20px" class="item text-center" >
                  <h4 class="text-orange" style="line-height: 1.5em" > Message from Finance: <br/> <span class="text-primary"><i class="fa fa-calculator"></i>   July 31, 2020 Holiday |<br/> Eid'l Adha Pay <br/>
                  
                    <img src="storage/uploads/divider.png" />
                    </h4>
                    <p style="padding: 30px;" class="text-left">
                     
                    <strong>June 31, 2020</strong> is a regular holiday as per Proclamation No. 985 by the President of the Philippines.<br/><br/>

                    Please see attached Proclamation for your reference.  Below is a guide on how the regular holiday pay is computed.</p>


                    <p class="text-center"><strong class="text-primary" style="font-size: large;">Unworked </strong>,<br/> provided the employee was present or <br/>on leave with pay on the workday <br/>prior to the start of June 31, 2020</p>
                    <pre>(Basic Pay + Allowance) x 100%</pre>
                    <strong class="text-success">WORKED</strong>
                    <pre>(Basic Pay + Allowance) x 200%</pre>
                    <strong class="text-success">Additional pay for work done in excess of 8 hours</strong>
                    <pre> [(Basic Pay + Allowance) ÷ 22 days ÷ 8 hours] x [number of hours worked] x  200% x 150%</pre>
                    <strong class="text-success">Worked, and falls on the rest day of the employee</strong>
                    <pre> [(Basic Pay + Allowance) ÷ 22 days ÷ 8 hours] x [number of hours worked] x  200% x 150%</pre>
                    <strong class="text-success">Additional pay for work done in excess of 8 hours, and falls on the employee's rest day</strong>
                    <pre> [(Basic Pay + Allowance) ÷ 22 days ÷ 8 hours] x [number of hours worked] x  200% x 150%</pre>
 
                    
              </div>

              

 <div class="item text-center">
                
                  <h4 class="text-orange" style="line-height: 1.5em" >Survey Form:<br/><span class="text-primary">Maxicare's Customer Satisfaction</span>
                  <br/>
                    <img src="storage/uploads/divider.png" />
                    </h4>
                    <img src="storage/uploads/maxiwebinar.jpg" width="100%" />
                    <p style="padding: 30px;" class="text-left"> 


                      The Open Access BPO Clinic organized our first #OneForHealth Mental Health Awareness Program. The recent event was held to address the stressors arising in employees' daily lives from this pandemic.<br/><br/>

                      Maxicare Healthcare Corporation Primary Care Physician, Dr. Anna Margarita Cruz, led the webinar with Head Company Nurse, Ms. Loraine Lopez, and Maxicare Assistant Manager, Mr. Anthony Joseph Perez, facilitating the event.<br/><br/>

                      Our Clinic and nurses are always available to help you cope with the changes during these trying times. We are one for health.<br/><br/>

                      In addition to this,  participants are requested to fill out <strong>Maxicare's Customer Satisfaction Survey Form</strong>. <br/>Please click/copy paste the link below:
                      <input class="form-control" type="text" id="bundylink" value="https://docs.google.com/forms/d/e/1FAIpQLScJkSEqR275IAuKGV7OoyoyXKIsgfYsVKoo2JSOFqDaGY4N1g/viewform" />
                      <button class="cp btn btn-xs btn-primary" data-link="bundylink">Copy Link <i class="fa fa-external-link"></i></button> <br/><br/>

                      Thank you!<br/>
                      
                     

                  </p>    
 
 
                    
              </div>



              <div class="item text-center" >
                  <h4 class="text-orange" style="line-height: 1.5em" >July 31 Friday: <br/><span class="text-primary"><i class="fa fa-calendar"></i> Eid'l Adha <br/><small>Feast of Sacrifice</small><br/>
                  
                    <img src="storage/uploads/divider.png" />
                    </h4>
                    

                    <img src="storage/uploads/eidladha2020.jpg" width="100%" /><br/>
                    
                    
                    
            </div>
 <div style="background: url('storage/uploads/memobg.png')top left repeat-y; background-size: 50%;background-color: #fff;padding:20px" class="item active text-center" >
                  <h4 class="text-orange" style="line-height: 1.5em" > Reminders from Finance: <br/> <span class="text-primary"><i class="fa fa-lock"></i>   DEADLINE FOR APPROVAL <br/>AND LOCKING OF DTR FOR <br/><strong class="text-success"> AUGUST 10, 2020</strong> PAYOUT <br/>
                  
                    <img src="storage/uploads/divider.png" />
                    </h4>
                    <p style="padding: 30px;" class="text-left">
                     
                    Dear All,<br/><br/>
                    
                    Please be informed that our cut-off for August 10 payout is August 05, 2020. Employee's DTR should be approved and locked in EMS <strong class="text-danger"> on or before THURSDAY 12:00 noon of August 06, 2020</strong>.<br/><br/>

                    Whether the DTR is locked or not, the Finance Department will assume the current data reflecting as final for salary computation and crediting.<br/><br/>

                    Please be guided accordingly.<br/><br/>

                    Thank you.<br/>

 
                    
              </div>
   
              <div style="background: url('storage/uploads/memobg.png')top left repeat-y; background-size: 50%;background-color: #fff;padding:20px" class="item text-center" >
                  <h4 class="text-orange" > OA OFFICE CAB SERVICE <span class="text-primary"> <i class="fa fa-info-circle-o"></i><br/>The Additional Solution for Your GCQ Office Transport Needs<br/>
                 <br/>
                  <img src="storage/uploads/carpool.png" width="100%" />
                    <img src="storage/uploads/divider.png" /><br/>
                    
                    <!-- <h3 class="text-danger"><i class="fa fa-medkit"></i> Health Alert <i class="fa fa-medkit"></i> </h3> -->
                    <p style="padding: 50px;font-size: smaller;line-height: 1.2em" class="text-left">

                      <strong>For onsite employees looking for a faster and equally safe everyday-transport option to the office:</strong><br/><br/>

                        If we were to launch an OA-accredited exclusive cab service, would you sign up if the rates were cheaper than Grab and the recurring weekly trips were scheduled for you?<br/><br/>



                        While our current shuttle service is free and has some degree of flexibility, ridesharing with longer lead times may not be for everyone. Before we can operationalize it, we need to find out if there was enough of you who’d want to use this exclusive cab service fleet for your daily office transport for a minimal fixed fare. If we get at least 20 positive responses, we will be able to set this up for you in about 2 weeks.<br/><br/>



                        Kindly answer the <a style="font-weight: bold;" href="https://www.emailmeform.com/builder/form/Z6bk4fu9HbL1novj6hfbIG" target="_blank"> survey here </a>. It will just take a few minutes.<br/><br/>

                        We will check the results up to <strong> July 12, 2020.</strong> <br/><br/><br/><br/>


                         



                        <strong class="text-success"> Here's how it will work:</strong><br/><br/>

                        To ensure health and safety when traveling to the office, we are planning to set up our very own OA Office Cab Service, a paid transport service from our private driver fleet providing daily prescheduled rides exclusive to OA employees who prefer to travel to work alone (or up to 1-2 carpool mates) and are willing to pay, making daily office trips faster and safer.<br/><br/>

                         

                        This is for those looking for an alternative to our current transport options: company shuttle service, private vehicle, or private carpool. Here then is another safe way for you to get to the office. Our Office Cab Service just might be for you. <br/><br/>





                        <a href="https://app.luminpdf.com/viewer/5efee51a20b8fd0013a36741" target="_blank"> Click here</a> for more details. See our affordable rates here: <a href="https://drive.google.com/file/d/1w9FMip-7e0IGPaIbUk2FCQIe30sYPL0m/view" target="_blank"> Fare Table</a>.

                    </p>
                   
                   

                    
              </div>
 

              <div class="item   text-center" >
               
                <h4 class="text-orange" style="line-height: 1.5em" >Don't forget to  <span class="text-primary">complete the survey!<br/> 
                  <img src="storage/uploads/divider.png" /> 
                  <img src="storage/uploads/pulse2020_live.jpg" width="100%" /><br/>
                  
                   
                    </h4>
                    <p style="padding: 30px;" class="text-center"><strong class="text-primary">Employee Pulse Check 2020</strong> is designed to help us understand our specific strengths and identify our areas for improvement during these last two quarters.<br/>We'd love to hear from you!<br/><br/>
                      Your feedback will show us where our opportunities are for improving employee experience.<br/><br/>
                      This survey is conducted twice a year so we can best track our progress and measure our results.<br/><br/>
                      Please answer each question honestly. It will not take too much of your time.<br/>
                      Your responses will be kept confidential and will not affect your standing at work.<br/><br/>
                      Let's all continue to work together to make life at Open Access better!<br/><br/>

                      <a class="btn btn-lg btn-success" href="{{action('SurveyController@intro',6)}}">Take the Survey <i class="fa fa-arrow-right"></i> </a>
                      <br/><br/>

                      <strong class="text-danger">Completion deadline: July 30, 2020 Thursday</strong>

                   
                    
              </div>

            
              


  <div class="item text-center" >
               
                <h4 class="text-orange" style="line-height: 1.5em" > Free <span class="text-primary">Coffee!!!<br/> 
                  <img src="storage/uploads/divider.png" /> 
                  <img src="storage/uploads/freecoffee.jpg" width="100%" /><br/>
                  
                   
                    </h4>
                    <p style="padding: 30px;" class="text-center"><strong class="text-primary">1 cup/day</strong>

                      For all onsite workers<br/><br/>

                      Swing by the cafeteria for your daily free brew.<br/><br/>

                      Until supplies last.<br/><br/>

                      *BYOT is a good idea too.<br/><br/>

                      <br/>
                      <em>*Bring your own tumbler or mug. Greener is better. Plus, it keeps your coffee warmer longer.  Enjoy!</em>

                   
                    
              </div>  

             
              
              
              

             
  <div class="item  text-center" >
                  <h4 class="text-orange" style="line-height: 1.5em" >Congratulations!
                  <br/>
                    <img src="storage/uploads/divider.png" />
                    </h4>
                    
                    <p style="padding: 30px;" class="text-left">
                      Thank you to all those who participated in our ECQonnection Wall Challenge last week - Cheer Up The Lonely Day.<br/><br/>
                      The jokes you've posted definitely made someone smile, and we definitely need that these days!<br/><br/>

                      Congratulations to our  <strong>Top 3 Entries:</strong><br/><br/>

                      <strong class="text-primary">Crissy Tuazon-Angeles</strong><br/>
                      <img src="storage/uploads/joke_crizzy.jpg" width="52%" class="pull-right" style="padding-left: 10px" /><br/>
                      <em>Knock, knock.<br/><br/>
                      Who's there?<br/><br/>
                      "Hatch". <br/><br/>
                      "Hatch" who?
                      <br/><br/>Bless you!</em><br/>
                      
                      <br/><br/>

                      <strong class="text-primary">Lothar Mckenzie</strong><br/>
                      <img src="storage/uploads/joke_lothar.jpg" width="52%" class="pull-right" style="padding-left: 10px"/><br/>
                      <em>Student: Teacher, what is the abbreviation (short spelling) for "FOLLOW"?<br/><br/>

                          Teacher: "ff"<br/><br/>
                          Student: HOORAY!!! (Immediately shouted right after the teacher answered)</em>
                     <br/>
                     <br/><br/>

                      <strong class="text-primary">Camilo Villanueva</strong><br/>
                      <img src="storage/uploads/joke_camilo.jpg" width="52%" class="pull-right" style="padding-left: 10px" /><br/>
                      <em>A guy tells his doctor, "Doc, help me. I'm addicted to Tiktok, Instagram, and Facebook."<br/><br/>
                      The doctor replies, "Sorry, I don't follow you."</em>
                      <br/>
                      
                       <br/><br/>
                       Until our next challenge!
                       <br/><br/>
                       <a class="btn btn-primary btn-md" href="{{action('EngagementController@wall',16)}}"><i class="fa fa-th-large"></i> View Wall</a>

                    </p>    
 
                    
              </div>

<div class="item text-center" >
                  <h4 class="text-orange" style="line-height: 1.5em" > 18<sup>th</sup> of July:<br/><span class="text-primary">Mandela Day<br/>
                  
                    <img src="storage/uploads/divider.png" />
                    </h4>
                    <img src="storage/uploads/mandela2020.jpg" width="100%" /><br/>
                    <p  style="padding: 30px;" class="text-left">
                      <strong>#MandelaDay </strong>is celebrated every July 18 to honor South Africa’s first black President and civil rights leader, Nelson Mandela. A powerful voice against the apartheid, he remains a symbol of the power that one individual can make a difference.<br/><br/>

                    We also want to take this time to honor his youngest daughter Zindzi Mandela, an activist and South Africa’s ambassador to Denmark as a voice against apartheid, who recently passed away.<br/><br/>

                    May the world continue to reflect on their legacy— take action and strive to make a difference.<br/><br/>

                    #WeSpeakYourLanguage #OACelebratesHeroes</p>
                    
                    
              </div>
               <div class="item text-center" >
                  <h4 class="text-orange" style="line-height: 1.5em" >Congratulations:<br/> <span class="text-primary">Decode the Emoji Winners!<br/><img src="storage/uploads/divider.png" /> </h4>
                    <img src="storage/uploads/emojiwinners.jpg" width="100%" /><br/>
                    <p style="padding: 30px;" class="text-center">
                  </p>

                   
                    
              </div>
 <div style="background: url('storage/uploads/memobg.png')top left repeat-y; background-size: 50%;background-color: #fff;padding:20px" class="item text-center" >
                  <h4 class="text-orange" style="line-height: 1.5em" > Reminders from Finance: <br/> <span class="text-primary"><i class="fa fa-lock"></i>   DEADLINE FOR APPROVAL <br/>AND LOCKING OF DTR FOR <br/><strong class="text-success"> JULY 24, 2020</strong> PAYOUT <br/>
                  
                    <img src="storage/uploads/divider.png" />
                    </h4>
                    <p style="padding: 30px;" class="text-left">
                     
                    Dear All,<br/><br/>
                    
                    Please be informed that our cut-off for July 24th payout is July 20, 2020. Employee's DTR should be approved and locked in EMS <strong class="text-danger"> on or before 12:00 noon of July 21, 2020</strong>.<br/><br/>

                    Whether the DTR is locked or not, the Finance Department will assume the current data reflecting as final for salary computation and crediting.<br/><br/>

                    Please be guided accordingly.<br/><br/>

                    Thank you.<br/>

 
                    
              </div>

 <div class="item text-center" >
                  <h4 class="text-orange" style="line-height: 1.5em" >Congratulations!
                  <br/>
                    <img src="storage/uploads/divider.png" />
                    </h4>
                    
                    <p style="padding: 30px;" class="text-left">
                      Thank you to all the employees who shared their favorite songs, movies and series!<br/><br/>

                      Three stood out and won <strong>P500 worth of Grabfood delivery voucher.</strong><br/><br/>

                      <strong class="text-primary">Dom Guzman of NDY</strong><br/>
                      <em>Myth - Beachhouse covered by Brandon Boyd (Incubus) "What comes after this, Momentary bliss... ...Can't keep hanging on To all that's dead and gone... " ~the nostalgia of what we can never get back... consuming the moment of what we cant let go... the last ecstatic feeling that we will have is the shot that cannot be erased honestly this video just made me miss the beach and its brandon boyd of incubus so :P</em><br/>
                      <img src="storage/uploads/wall[5]2020_2953_1446147328.JPG" width="95%"><br/><br/>

                      <strong class="text-primary">Lorraine Gel Gallego of AdoreMe</strong><br/>
                      <em>"I Am Not Alone" by Kari Jobe The song says ," Lord, You fight my every battle. Oh, and I will not fear. I am not alone. I am not alone. You will go before me , You will never leave me. " This Covid-19 Pandemic and ECQ put fear in my head and I was stuck here alone at home with no family. I cannot go somewhere safer. I played my guitar with this song over and over again, I cried and I felt safe and secured. I feel like God is with me all the time as I worship Him in Spirit and truth</em>
                      <img src="storage/uploads/wall[5]2020_2392_1083735229.jpg" width="95%"><br/><br/><br/>

                      <strong class="text-primary">Umit Ozay of SheerID</strong><br/>
                      <em>the "san junipero" episode of black mirror on netflix made me think another way about life . and especially see this trailer with this unique cover.. lovely! https://vimeo.com/208471851 </em>
                       <img src="storage/uploads/wall[5]2020_3540_1587672673.jpg" width="95%"><br/><br/>
                       <br/><br/>
                       Until our next challenge!
                       <br/><br/>
                       <a class="btn btn-primary btn-md" href="{{action('EngagementController@wall',12)}}"><i class="fa fa-th-large"></i> View Wall</a>

                    </p>    
 
                    
              </div>
<div style="background: url('storage/uploads/memobg.png')top left repeat-y; background-size: 50%;background-color: #fff;padding:20px" class="item text-center" >
                  <h4 class="text-orange" >Company Shuttle Service for GCQ <span class="text-primary"> <i class="fa fa-info-circle-o"></i><br/>Application Form for onsite workers availing shuttle<br/>
                 <br/>
                <img src="storage/uploads/shuttle.png" width="80%" /><br/>
                    <img src="storage/uploads/divider.png" /><br/>
                    
                    <!-- <h3 class="text-danger"><i class="fa fa-medkit"></i> Health Alert <i class="fa fa-medkit"></i> </h3> -->
                    <p style="padding: 50px;font-size: smaller;line-height: 1.2em" class="text-left">Dear Open Access Family,<br/><br/>
                      For those working onsite during GCQ, we will continue to provide a shuttle service. Now that there is more allowable movement, our goal is to get you here easily and more importantly, covid-free.  If you need to work in the office and need a daily ride to work, please complete this<a href="https://www.emailmeform.com/builder/form/628acMekO89" target="_blank"> <strong>application form</strong></a>. This form is for both current and new shuttlers. <br/><br/>

                       

                      We are aware that although some forms of public transportation will now be available, it will be at reduced capacity and it won’t all be back 100%. We expect traffic and public transport to be grueling, problematic, and risky. We want to enable safe access for you so we will continue to provide free transportation during the general community quarantine period.<br/><br/>

                       

                      Please read and understand our terms and conditions of service in the OA Transport Policy, Procedures, and Guidelines before submitting the<a href="https://www.emailmeform.com/builder/form/628acMekO89" target="_blank"> <strong>GCQ Application Form</strong></a>. Make sure you send your request once you have official confirmation of your team returning to the office. Requests are subject to review and approval, and take at least 2 days to process. Once you receive a trip confirmation email, you will be reminded to submit the online Health Declaration Form (link attached in the notification email to be sent).<br/><br/>

                       

                      All shuttle riders will be required to have their COE and company IDs with them for checkpoints and follow our standard health measures and social distancing rules in our vehicles and in our offices. As the company has been doing all to keep us safe and operational, not sparing any expense, our marching orders are to be vigilant and responsible for our own safety at all times. <br/><br/>



                      Let’s be smart and super careful. Stay healthy!
                    
                    

                    
              </div>
           <div class="item text-center" >
                  
                  
                  <h4 class="text-orange" style="line-height: 1.5em" >Congratulations!
                  <br/>
                    <img src="storage/uploads/divider.png" />
                    </h4>
                    
                    <p style="padding: 30px;" class="text-left">
                      Thank you to all the employees who posted their beautiful photos! <br/><br/>

                      Three stood out and won <strong>P500 worth of Grabfood delivery voucher.</strong><br/><br/>

                      <strong class="text-primary"> Cherry Fuentes of Glassdoor</strong><br/>
                      <img src="storage/uploads/wall[3]2020_1802_1113508890.jpg" width="95%"><br/><br/>

                      <strong class="text-primary">Rio Dormitorio of NDY</strong>
                      <img src="storage/uploads/wall[3]2020_3345_2028493235.jpg" width="95%"><br/><br/>

                      <strong class="text-primary">Audrey Reyes of Marketing</strong>
                       <img src="storage/uploads/wall[3]2020_879_1541706714.jpg" width="95%"><br/><br/>
                       <br/><br/>
                       Until our next challenge!

                    </p>    
 
                    
              </div>


               

               <div style="background: url('storage/uploads/memobg.png')top left repeat-y; background-size: 50%;background-color: #fff;padding:20px" class="item text-center" >
                  <h4 class="text-orange" >Message from our Execs: <br/> <span class="text-primary"> <i class="fa fa-thumbs-up"></i> Amazing job, IT Team!</i><br/> <br/>
                   
                    <img src="storage/uploads/divider.png" />
                    </h4>
                    <p style="padding: 30px;" class="text-left"><img src="storage/uploads/teamIT.jpg" width="110%" /><br/><br/>Dear Open Access BPO Family,<br/><br/>
                     Amazing job by our IT team and everyone else involved. In record time we have transferred over 700 people working out of their homes with 50 to 100 more people in progress which is almost 80% of our workforce. <strong>A huge THANK YOU!!!</strong><br/><br/>
                     Outstanding effort. We appreciate you and everything you have sacrificed to get this done. <br/><br/>

                     <strong>Ben &amp; Henry</strong>
                    

                    </p>

                    
                    



                   
                    
                </div>
<div style="background: url('storage/uploads/memobg.png')top left repeat-y; background-size: 50%;background-color: #fff;padding:20px" class="item text-center" >
                  <h4 class="text-orange" >Attention: <br/> <span class="text-primary"> <i class="fa fa-info-circle-o"></i>Davao Employees<br/> <br/>
                   
                   
                    <img src="storage/uploads/divider.png" /><br/>
                    </h4>

                    <p class="text-center"><a href="oampi-resources#resource_2" class="btn btn-sm btn-primary" target="_blank"><i class="fa fa-book"></i> ECQ Health &amp; Safety Protocols Davao</a></p>

                    <h5>Davao HR Hotline <i class="fa fa-phone"></i> : <br/><span style="font-size: large;" class="text-danger"> 0917-859-2539 </span><br/></h5>

                    <table class="table" width="80%">
                      <tr>
                        <th class="text-center text-primary">Point Persons:</th>
                      </tr>
                      <tr>
                        <td>
                          <a target="_blank" href="user/3086"><img src="./public/img/employees/3086.jpg" class="img-circle" width="80" style="margin-left: 5px" /><br/><h4 style="font-size: small;">Joash Elisha Arado <br/><small>(jarado@openaccessbpo.net)</small></h4></a>
                        </td>
                        
                      </tr>
                      <tr>
                        <td>
                          <a target="_blank" href="user/2096"><img src="./public/img/employees/2096.jpg" class="img-circle" width="100" style="margin-left: 5px" /><br/><h4 style="font-size: small;">Mary Lord Maulingan<br/><small>(mmaulingan@openaccessbpo.net)</small></h4></a>
                        </td>
                      </tr>
                      
                    </table>

                     <h5>Davao Nurse Hotline <i class="fa fa-phone"></i> : <br/><span style="font-size: large;" class="text-danger"> 0917-818-7146 </span><br/></h5>

                    <table class="table">
                      <tr>
                        <thclass="text-center text-primary">Point Person:</th>
                      </tr>
                      <tr>
                        <td>
                          <a target="_blank" href="user/3737"><img src="./public/img/oam_favicon1-55027f4ev1_site_icon-256x256.png" width="50" class="img-circle" style="margin-left: 5px" /><br/><h4 style="font-size: small;">Dianna Mariss Ba <br/><small>(dba@openaccessbpo.net)</small></h4></a>
                        </td>
                        
                      </tr>
                      
                    </table>
                    



                   
                    
                </div>

                



                <div style="background: url('storage/uploads/memobg.png')top left repeat-y; background-size: 50%;background-color: #fff;padding:20px" class="item text-center" >
                  <h4 class="text-orange" >  Company Carpool Shuttle Scheme <span class="text-primary"> <i class="fa fa-info-circle-o"></i><br/> Policy and Guidelines<br/><br/>
                  <span style="font-size: smaller;"> Reimbursements and Incentives <br/>for Business Continuity for ECQ</span>
 <br/>
                    <img src="storage/uploads/divider.png" /><br/>
                    <img src="storage/uploads/carpool.png" width="100%" /></h4>
                    <!-- <h3 class="text-danger"><i class="fa fa-medkit"></i> Health Alert <i class="fa fa-medkit"></i> </h3> -->
                    <p style="padding: 50px" class="text-left">Dear All,<br/><br/>

                        This policy outlines the rules and procedures for drivers carpooling to work as part of our Company Shuttle Scheme for our skeletal workforce onsite during ECQ. This includes submission and payout procedures for reimbursement and incentive claims for office parking, toll fees, and carpooling. This is in response to the industry exemptions from suspended public transportation operations due to the Enhanced Community Quarantine for Luzon and in support of our employees who can drive to work during this period.
</p>
                    <a class="btn-success btn btn-md" href="oampi-resources#resource_2" target="_blank"><i class="fa fa-car"></i> Read the Guidelines</a>
                </div>

               <div class="item text-center">
                <img src="storage/uploads/july42020.jpg" width="100%" />
                  <h4 class="text-orange" style="line-height: 1.5em" >Happy<br/><span class="text-primary">4th of July</span>
                  <br/>
                    <img src="storage/uploads/divider.png" />
                    </h4>
                    
                    <p style="padding: 30px;" class="text-left">
                     
                   The true spirit of independence is independence for all. May we never forget the sacrifices of those who fought for equality and liberty so that everyone can celebrate the right to life and happiness.<br/><br/>

                    We hope you have a safe and meaningful #FourthofJuly!<br/><br/>

                    #WeSpeakYourLanguage #OAHolidays
                  </p>    
 
 
                    
              </div>
  <div style="background: url('storage/uploads/memobg.png')top left repeat-y; background-size: 50%;background-color: #fff;padding:20px" class="item text-center" >
                  <h4 class="text-orange" style="line-height: 1.5em" > To all Shuttlers: <br/> <span class="text-primary"><i class="fa fa-car"></i>   Adjusted Ride Waiting Time <br/>
                   <img src="storage/uploads/shuttle.png" width="80%" /><br/><br/>
                    <img src="storage/uploads/divider.png" />
                    </h4>
                    <p style="padding: 30px;" class="text-left">
                     
                    Due to the elevated ECQ restrictions, high volume of ride requests, and driver head count constraints, we have needed to streamline our trip planning to utilize our vehicle pool more efficiently and effectively. Please allow a ride-wait-time of <strong class="text-danger"> no less than 1 to 1.5 hours (even possibly 2-3 hours for farther vicinities)</strong> while you and your co-passengers get picked up.<br/><br/>
                    <strong>We request for your patience and adaptability for this time. We need to be able to accommodate everyone as mush as is within government ruling as well as our logistical capacity.</strong><br/><br/>
                    To be able to accommodate more shuttle riders, we have needed to schedule your shared rides in clusters of 1-3 hours ahead of your actual shift start time (possibly longer for those living in farther vicinities) and up to 1 hour from your shift end time. <br/><br/>
                    This was implemented April 3, 2020.  To reiterate, necessary adjustments in your pickup time have been made and communicated.  Please allot a longer time allowance. Kindly take note of your new ride schedules from the email notification that will be sent and prepare accordingly. 
                    Let’s all help each other get to and from work on time and safely.
                    Thank you for your understanding and cooperation.



                    

                    </p>  
                    
                </div>

<div class="item text-center" >
                  
                  
                  <img src="storage/uploads/mentalhealth.jpg" width="100%" />
                  <h4 class="text-orange" style="line-height: 1.5em" >Mental Health Awareness Month
                  <br/>
                    <img src="storage/uploads/divider.png" />
                    </h4>
                    
                    <p style="padding: 30px;" class="text-left">
                     
                   
                     As Mental Health Awareness Month comes to an end, we’ll leave you with helpful tips to help you look after your mental health during these trying times. Remember: mental health is just as important as physical health! 💛<br/><br/>
                      - Try out grounding exercises or meditation to reduce anxiety<br/><br/>
                      - Take breaks when you're feeling unfocused<br/><br/>
                      - Avoid comparing your feelings and coping mechanisms with those of others<br/><br/>
                      - Don't let productivity define you<br/><br/>
                      - Understand there's no correct way to feel during a pandemic<br/><br/>
                      - Log off social media when your feed becomes too overwhelming<br/><br/>
                      - Hydrate<br/><br/>
                      - Check up on each other and stay connected<br/><br/>
                     <strong>#WeSpeakYourLanguage #OneForHealth #MentalHealthMonth</strong></p>    
 
 
                    
              </div>
<div style="background: url('storage/uploads/memobg.png')top left repeat-y; background-size: 50%;background-color: #fff;padding:20px" class="item  text-center" >
                  <h4 class="text-orange" style="line-height: 1.6em" ><i class="fa fa-exclamation-triangle"></i> Reminders: <br/> <span class="text-primary">  Log IN/OUT and DTRP </i><br/> <br/>
                   <img src="storage/uploads/reminder.jpg" width="80%" /><br/><br/>
                    <img src="storage/uploads/divider.png" />
                    </h4>
                    <p style="padding: 30px;" class="text-left">
                     This is a reminder to those working in the office and working from home to LOG IN and LOG OUT using EMS.  As previously announced, EMS will now be the primary system for daily timekeeping.<br/><br/>

                     <strong class="text-primary">Any DTRP concerns will be subject to validation and review. </strong> Non-compliance with the requirement to  LOG IN and LOG OUT in EMS will be dealt with in accordance with our Code of Conduct.<br/><br/>

                      Please be guided accordingly.<br/><br/>

                      Thank you.
                    

                    </p>  
                    
                </div>
<div style="background: url('storage/uploads/memobg.png')top left repeat-y; background-size: 50%;background-color: #fff;padding:20px" class="item text-center" >
                  <h4 class="text-orange" >Workplace Health and Safety Policy<span class="text-primary"> <i class="fa fa-info-circle-o"></i><br/>for: Employees Staying in Temporary Housing<br/>
                 <br/>
                    <img src="storage/uploads/wfh.jpg" width="80%" /> <br/><br/>
                    <img src="storage/uploads/divider.png" /><br/>
                    
                    <!-- <h3 class="text-danger"><i class="fa fa-medkit"></i> Health Alert <i class="fa fa-medkit"></i> </h3> -->
                    <p style="padding: 50px;font-size: smaller;line-height: 1.2em" class="text-left">

                      All employees should practice social responsibility by monitoring their health condition. If an employee is sick or has flu-like
                      symptoms such as fever, cough, shortness of breath, fatigue, sore throat, headache, chills, nausea or nasal congestion, or has
                      had history of exposure, compliance with the procedures is mandatory:</p>

                    <ol type="1"style="padding-left: 50px;font-size: smaller;line-height: 1.2em" class="text-left">
                      <li> The Employee should remain in the temporary housing and immediately notify the Nurse
                      two hours prior to the scheduled shift. (Nurses’ contact information is found in Section V)<br/><br/></li>
                      <li>The Nurse will assess the employee’s health via phone call. Depending on the assessment, the Nurse
                       may require Maxicare Teleconsult or Hospital Consult.<br/><br/></li><br/><br/></li>
                      <li>If 14-day self-quarantine has been advised, the employee should transfer to and/or maintain single
                      occupancy of the room. If single occupancy is not possible, the employee will be required to selfquarantine at home.<br/><br/></li>
                      <li>If hospitalization is recommended, employee is required to be confined.<br/><br/></li>
                      <li>The employee must provide daily updates to the Nurse
                       (oamnurse@openaccessbpo.com or call/SMS -09178960634).<br/><br/></li>
                      <li>After 14 days of quarantine or discharge from hospital, the employee must seek post
                      quarantine/post-admission medical consult and submit a medical certificate/fit to work certificate.
                      Consultation may be via Maxicare Teleconsult/Clinic consult. Report or documentation should be sent
                      to the Nurses. <br/>(note: Teleconsultations provide email reports and may be requested by the patient
                      before ending the call.)<br/><br/></li>
                      <li>After medical certificate/fit to work verification, the Nurse will notify the employee’s Immediate Head
                       and will send the appropriate report. </li>


                    </ol>

                    <a href="resource#resource_6" class="btn btn-success btn-md"><i class="fa fa-book"></i> Read More</a>


                    
                </div>
<div style="background: url('storage/uploads/memobg.png')top left repeat-y; background-size: 50%;background-color: #fff;padding:20px" class="item text-center" >
                  <h4 class="text-orange" >Pag-IBIG  <span class="text-primary"> <i class="fa fa-info-circle-o"></i><br/>MULTI-PURPOSE LOAN (MPL) AND <br/> CALAMITY LOAN APPLICATION <br/>
                 <br/>
                    <img src="storage/uploads/divider.png" /><br/>
                    <img src="storage/uploads/calamityloan.jpg" width="90%" /></h4>
                    
                    <!-- <h3 class="text-danger"><i class="fa fa-medkit"></i> Health Alert <i class="fa fa-medkit"></i> </h3> -->
                    <p style="padding: 50px;" class="text-left"><br/>
                      Below is the pertinent information necessary for securing a PAG-IBIG calamity loan and a multi-purpose loan. We hope this will answer crucial questions.<br/><br/>

                      The Pag-IBIG Fund Calamity Loan Program seeks to provide immediate financial aid to affected members in calamity-stricken areas.<br/><br/>



                      <strong>Home Development Fund (Pag-IBIG)–Calamity Loan Qualifications</strong><br/><br/>

                      Any Pag-IBIG member who -<br/>

                      Has made at least 24 monthly savings<br/>
                      Has made at least 5 monthly savings in the last 6 months<br/>
                      Resides in an area under the State of Calamity<br/><br/>

                       <strong class="text-primary">Process and Requirements:</strong><br/><br/>

                      The borrower must submit the following to any Pag-IBIG office:<br/><br/>

                      Calamity Loan Application Form*<br/>
                      Photocopy of at least 2 valid IDs<br/>
                      Proof of Income<br/>
                      Declaration of Being Affected by Calamity* (for formally employed members)<br/>
                                    <em>[*Documents are available at www.pagibigfund.gov.ph and Pag-IBIG offices]</em><br/><br/>

                      
                      <strong>Filing:</strong> Loans may be filed within 90 days from the declaration of State of Calamity.<br/><br/>

                      <strong class="text-primary"> Loan Amount</strong><br/><br/>

                      Members can borrow up to 80% of their Total Accumulated Value (TAV) subject to the terms and conditions of the program. Calamity Loan Interest rate is 5.95% per annum. The loan is amortized over 24 months, with a grace period of 3 months. Paying period begins on the 4th month following their check date.<br/><br/>

                      For inquiries and details, see below:<br/><br/>

                      Website:         https://www.pagibigfund.gov.ph/#<br/>

                      Email:              contactus@pagibigfund.gov.ph<br/>

                      Facebook:       https://www.facebook.com/PagIBIGFundOfficialPage/<br/>

                      Pag-IBIG FUND HOTLINE: 8-724-4244 (8-Pag-IBIG) - 24/7 Call Center operation<br/>

                      Virtual Pag-IBIG:  by visiting www.pagibigfund.gov.ph and clicking on the Virtual Pag-IBIG button.<br/><br/>

                       

                      List of valid IDs:<br/><br/>

                      1.       Passport, issued by the Philippine or Foreign Government<br/>

                      2.       Social Security System (SSS) Card<br/>

                      3.       Government Office and GOCC ID (e.g. AFP ID, Pag-IBIG Loyalty Card)<br/>

                      4.       Overseas Workers Welfare Administration (OWWA) ID<br/>

                      5.       Company ID<br/>

                      6.       Senior Citizen Card<br/>

                      7.       Voter’s ID<br/>

                      8.       Professional Regulation Commission (PRC) ID<br/>

                      9.       Integrated Bar of the Philippines (IBP) ID<br/>

                      10.   Driver’s License<br/>

                      11.   Postal ID<br/><br/><br/>

                  

                      For further inquiries, please email Grazelene Anne Hermo, our PAGIBIG FUND Coordinator at ghermo@openaccessbpo.com, employee_relations@openaccessbpo.com and copy salaryinquiry@openaccessbpo.com.<br/><br/>

                      For your information and guidance.<br/><br/>



                      Sincerely,<br/>

                      HR DEPARTMENT
                    </p>

                   
                    
                </div>
<div style="background: url('storage/uploads/memobg.png')top left repeat-y; background-size: 50%;background-color: #fff;padding:20px" class="item text-center" >
                  <h4 class="text-orange" style="line-height: 1.5em" > Message from Finance: <br/> <span class="text-primary"><i class="fa fa-calculator"></i>   June 12, 2020 Holiday |<br/> Independence Pay <br/>
                  
                    <img src="storage/uploads/divider.png" />
                    </h4>
                    <p style="padding: 30px;" class="text-left">
                     
                    June 12, 2020 is a regular holiday as per Proclamation No. 845 by the President of the Philippines.<br/><br/>

                    In relation to this, please file your holiday worked hours as overtime in EMS to align with our payroll system requirement.  Otherwise, you will not get paid for the holiday premium.<br/><br/>

                    Below is a guide on how the regular holiday pay is computed.
                    <br/><br/></p>


                    <p class="text-center"><strong class="text-primary" style="font-size: large;">Unworked </strong>,<br/> provided the employee was present or on leave with pay on the workday prior to the start of June 12, 2020</p>
                    <pre>(Basic Pay + Allowance) x 100%</pre>
                    <strong class="text-success">WORKED</strong>
                    <pre>(Basic Pay + Allowance) x 200%</pre>
                    <strong class="text-success">Additional pay for work done in excess of 8 hours</strong>
                    <pre> [(Basic Pay + Allowance) ÷ 22 days ÷ 8 hours] x [number of hours worked] x  200% x 150%</pre>
                    <strong class="text-success">Worked, and falls on the rest day of the employee</strong>
                    <pre> [(Basic Pay + Allowance) ÷ 22 days ÷ 8 hours] x [number of hours worked] x  200% x 150%</pre>
                    <strong class="text-success">Additional pay for work done in excess of 8 hours, and falls on the employee's rest day</strong>
                    <pre> [(Basic Pay + Allowance) ÷ 22 days ÷ 8 hours] x [number of hours worked] x  200% x 150%</pre>
 
                    
              </div>

 <div class="item text-center">
                <img src="storage/uploads/togetherwithpride.jpg" width="100%" />
                  <h4 class="text-orange" style="line-height: 1.5em" >Together<br/><span class="text-primary">with Pride</span>
                  <br/>
                    <img src="storage/uploads/divider.png" />
                    </h4>
                    
                    <p style="padding: 30px;" class="text-left">
                     
                   <em>#Pride </em>is bigger than a celebration. It is a protest. Our friends and colleagues from the #LGBTQI+ community continue to be subjected to hate crimes and brutality even in the midst of the COVID-19 crisis. Open Access BPO commits to stand together with Pride for equality and visibility.<br/><br/>

                    Let's #PrideMarchFromHome to support the #LGBTQI+ communities affected by the pandemic. Make your march count, visit https://hubs.ly/H0rZgw90 now!<br/><br/>

                    <strong>#WeSpeakYourLanguage #OASpeaksWithPride</strong>.
                  </p>    
 
 
                    
              </div>
              <div class="item active text-center">
                <img src="storage/uploads/fathersday2020.jpg" width="100%" />
                  <h4 class="text-orange" style="line-height: 1.5em" >Happy<br/><span class="text-primary"> Father's Day!</span>
                  <br/>
                    <img src="storage/uploads/divider.png" />
                    </h4>
                    
                    <p style="padding: 30px;" class="text-left">
                     
                   It's Father's Day!<br/><br/>

                    Sending love to every father and father figure out there. Have a happy and safe weekend to everyone celebrating with their loved ones on this special day.<br/><br/>
                    #WeSpeakYourLanguage #OAHolidays<br/><br/>
                  </p>    
 
 
                    
              </div>
<div class="item active text-center">
                
                  <h4 class="text-orange" style="line-height: 1.5em" >Celebrating 122nd<br/><span class="text-primary">Philippine Independence Day</span>
                  <br/>
                    <img src="storage/uploads/divider.png" />
                    </h4>
                    <img src="storage/uploads/independence.jpg" width="100%" />
                    <p style="padding: 30px;" class="text-left">
                     
                     Today, we commemorate the 122nd Philippine Independence Day. Let us remember the sacrifices of the determined Filipinos who fought for freedom and independence.<br/><br/>

                      #WeSpeakYourLanguage #OAHolidays
                     

                  </p>    
 
 
                    
              </div>

 <div style="background: url('storage/uploads/memobg.png')top left repeat-y; background-size: 50%;background-color: #fff;padding:20px" class="item text-center" >
                  <h4 class="text-orange" style="line-height: 1.5em" > Message from Finance: <br/> <span class="text-primary"><i class="fa fa-calculator"></i>   Holiday Pay Treatment <br/>for May 25 Eid'l Fitr <br/>
                  
                    <img src="storage/uploads/divider.png" />
                    </h4>
                    <p style="padding: 30px;" class="text-left">
                     
                    Dear All,<br/><br/>
                    
                    Kindly see below details on the holiday pay treatment for May 25.<br/><br/></p>


                    <p class="text-center"><strong class="text-primary" style="font-size: large;">Unworked </strong>,<br/> provided the employee was present or on leave with pay on the workday prior to the start of May 25, 2020</p>
                    <pre>(Basic Pay + Allowance) x 100%</pre>
                    <strong class="text-success">WORKED</strong>
                    <pre>(Basic Pay + Allowance) x 200%</pre>
                    <strong class="text-success">Additional pay for work done in excess of 8 hours</strong>
                    <pre> [(Basic Pay + Allowance) ÷ 22 days ÷ 8 hours] x [number of hours worked] x  200% x 150%</pre>
                    <strong class="text-success">Worked, and falls on the rest day of the employee</strong>
                    <pre> [(Basic Pay + Allowance) ÷ 22 days ÷ 8 hours] x [number of hours worked] x  200% x 150%</pre>
                    <strong class="text-success">Additional pay for work done in excess of 8 hours, and falls on the employee's rest day</strong>
                    <pre> [(Basic Pay + Allowance) ÷ 22 days ÷ 8 hours] x [number of hours worked] x  200% x 150%</pre>
 
                    
              </div>

<div style="background: url('storage/uploads/memobg.png')top left repeat-y; background-size: 50%;background-color: #fff;padding:20px" class="item  text-center" >
                  <h4 class="text-orange" style="line-height: 1.5em" > Reminder from Finance: <br/> <span class="text-primary"><i class="fa fa-lock"></i> DEADLINE FOR APPROVAL <br/>AND LOCKING OF DTR FOR <br/>JUNE 10, 2020 PAYOUT & ECQ INCENTIVES<br/>
                  
                    <img src="storage/uploads/divider.png" />
                    </h4>
                    <p style="padding: 30px;" class="text-left">
                     
                    Dear All,<br/><br/>
                    
                    Please be informed that our cut-off for June 10th payout is June 5, 2020. Employee's DTR should be approved and locked in EMS <strong class="text-primary"> on or before 12:00 noon of June 6, 2020.</strong><br/><br/>

                    Whether the DTR is locked or not, the Finance Department will assume the current data reflecting as final for salary computation and crediting.<br/><br/>

                    Please also be reminded that effective June 1, 2020, <strong>employees are no longer entitled to ECQ incentives</strong> except for At-Home-Worker Allowance which will still be included in employee’s pay.<br/><br/>

                    Please be guided accordingly.<br/><br/>

                    Thank you<br/><br/>

                   
 
                    
              </div>

  <div style="background: url('storage/uploads/memobg.png')top left repeat-y; background-size: 50%;background-color: #fff;padding:20px" class="item text-center" >
                  <h4 class="text-orange" style="line-height: 1.5em" >All you need to know <br/> to get started with Bundy.ph<br/><img src="storage/uploads/divider.png" /><br/>

                   <br/> <span class="text-primary"> <i class="fa fa-book"></i> Bundy.ph Quick Reference Guide,<br/> FAQs, and Question Page</span>
                   <input class="form-control" type="text" id="guidelink" value="https://rise.articulate.com/share/EhUIcrxCbSRNuoP42zxTUf4RrC8ZyFrD" />
                   <button class="cp btn btn-xs btn-primary" data-link="guidelink">Copy Link <i class="fa fa-external-link"></i></button>
                    <br/><br/>
                   <img src="storage/uploads/bundyguide.png" width="98%" />
                    
                    </h4>
                    <p style="padding: 30px;" class="text-left">
                     
                    <strong> How to pull up the Bundy.ph Quick Reference Guide, FAQs, and Question Page:</strong><br/><br/>
                   


                    If you are using the VPN link to access EMS and Zimbra, copy and paste the  link above on a browser  instead of directly clicking on it from the email or EMS announcement.<br/><br/>

                     

                     

                    <strong class="text-primary"> Other important links:</strong><br/><br/>

                    <strong class="text-success"> Bundy.ph Time Tool</strong><br/>
                    <input class="form-control" type="text" id="bundylink" value="https://bundy.payroll.ph/web/oampi" />
                    <button class="cp btn btn-xs btn-primary" data-link="bundylink">Copy Link <i class="fa fa-external-link"></i></button>

                     <br><br/>

                  

                     <strong class="text-success">Web Bundy Video Guide</strong><br> 
                     <input class="form-control" type="text" id="videolink" value="https://rise.articulate.com/share/wI1ScLEfBkoCJKPJj5b7qaR34W8XPr2b#/" />
                     <button class="cp btn btn-xs btn-primary" data-link="videolink">Copy Link <i class="fa fa-external-link"></i></button>

                    <br><br/>






                  </p>
 
                    
              </div>
 <div style="background: url('storage/uploads/memobg.png')top left repeat-y; background-size: 50%;background-color: #fff;padding:20px" class="item text-center" >
                  <h4 class="text-orange" >DOLE's Php 5,000<span class="text-primary"> <i class="fa fa-info-circle-o"></i><br/>Financial Support<br/>
                 <br/>
                    <img src="storage/uploads/DOLE-logo.jpg" width="80%" /> <br/>
                    <img src="storage/uploads/divider.png" /><br/>
                    
                    <!-- <h3 class="text-danger"><i class="fa fa-medkit"></i> Health Alert <i class="fa fa-medkit"></i> </h3> -->
                    <p style="padding: 50px;font-size: smaller;line-height: 1.2em" class="text-left">

                     In Department Order No. 209 dated March 17, 2020, DOLE instituted the COVID-19 Adjustment Measures Program (“CAMP”) aimed at providing Financial Support to all affected workers. The DOLE likewise issued four (4) advisories which sought to clarify and supplement the CAMP guidelines and procedures.<br/><br/>

                      While awaiting DOLE’s FAQs on CAMP implementation, listed below are Q&As that might be of help:<br/><br/>

                      <strong class="text-orange">Q: How much is the DOLE’s financial assistance? </strong>

                      <span class="text-primary"> A: Php5,000. The amount is a one-time financial assistance to affected workers paid lump sum, non-conditional, regardless of employment status.<br/><br/></span>

                      <strong class="text-orange">Q: Who are entitled to the Php 5,000 financial assistance?</strong>

                      <span class="text-primary">A:  Only affected workers. Under DOLE D.O. 209 as supplemented by Labor Advisory 12-20, “affected workers” is defined as those whose employment suffer interruption due to the COVID-19 pandemic such as:<br/><br/></span><br/>

                      i)  Those whose regular wage is reduced due to implementation of flexible work arrangements; and<br/>

                      ii) Those whose employment is temporarily suspended by reason of suspension or closure of business operations.<br/><br/>

                      <strong class="text-orange">Q: Are those who work from home eligible to receive DOLE financial assistance?</strong>

                      <span class="text-primary">A: No. Under DOLE Memorandum COVID No. 01-2020, workers who still receive full wages (e.g. under telecommuting or work from home arrangement) are not entitled to the financial assistance.<br/><br/></span>

                      <strong class="text-orange">Q:  Can leave credits be utilized during the quarantine period?</strong>

                      <span class="text-primary">A: Yes. Under Labor Advisory No. 11-20, the leaves of absence during community quarantine period shall be charged against the workers’ existing leave credits, if any. Remaining unpaid leaves during said period may be covered and be subject to the conditions provided in the DOLE’s CAMP financial support program. Moreover, under DOLE D.O. No. 209, the financial assistance may be used to cover remaining unpaid leaves of affected workers.<br/><br/></span>

                      <strong class="text-orange">Q: Do I have the option not to use any of my leave credits?</strong>

                      <span class="text-primary">A: Apparently, yes. The DOLE Secretary announced in a press conference that employees have the discretion on whether to utilize his/her leave credits.<br/><br/></span>

                      <strong class="text-orange">Q: What is the process in availing the DOLE financial assistance?</strong>

                      <span class="text-primary">A: The Departments involved will initiate the process and apply with DOLE submitting the documentary requirements. The application for financial assistance is subject to approval of the Department of Labor and Employment.<br/><br/></span>

                      <strong class="text-orange">Q: When will DOLE release the Php5,000 financial assistance?</strong>

                      <span class="text-primary">A:  Once approved, the Php 5,000 will be paid by DOLE directly to your bank accounts at the soonest possible time.<br/><br/></span>

                      If there are any further questions, you may send your queries to HR Employee Relations.

                    </p>
                   

                    
                </div>

                
 <div style="background: url('storage/uploads/memobg.png')top left repeat-y; background-size: 50%;background-color: #fff;padding:20px" class="item  text-center" >
                  <h4 class="text-orange" style="line-height: 1.6em" > Attention: <br/> <span class="text-primary">    DSWD's Social Amelioration Program  <br/>
                   <img src="storage/uploads/dswd.png" width="50%"><br/>
                    <img src="storage/uploads/divider.png" />
                    </h4>
                    <p style="padding: 30px;" class="text-left">
                     
                      Below are important information provided by the  DSWD and the IATF regarding the Government's Social Amelioration Program ("SAP"):<br/><br/>

                      1. The SAP will be spearheaded by the DSWD in coordination with the Local Government Units and Barangays;<br/><br/>
                      2. The DSWD's priority will be the low income families and the informal sector;<br/><br/>
                      3. The  cash aid of Php 5,000 to Php 8,000 will be per Family/Household; and<br/><br/>
                      4. The DSWD Social Amelioration Cards will be distributed at the barangay level.<br/><br/>

                      For more information, you may visit the DSWD website at <a href="http://www.dswd.gov.ph" target="_blank">www.dswd.gov.ph</a> and DSWD's FB page.<br/><br/>

                      Thank you.<br/><br/><strong>HR Department</strong>
                    

                    </p>  
                    
              </div>


<div style="background: url('storage/uploads/memobg.png')top left repeat-y; background-size: 50%;background-color: #fff;padding:20px" class="item  text-center" >
                  <h4 class="text-orange" style="line-height: 1.5em">EARLY CUT-OFF DTR: 
                    <br/> <span class="text-primary"> <i class="fa fa-money"></i> &nbsp;&nbsp; MAY 22, 2020 PAYOUT &nbsp;&nbsp; <i class="fa fa-money"></i> <br/>
                   <!-- <img src="storage/uploads/dswd.png" width="50%"><br/> -->
                    <img src="storage/uploads/divider.png" />
                    </h4>
                    <p style="padding: 30px;" class="text-left">
                     
                      As per Malacañang Proclamation No. 944, <strong>May 25, 2020</strong> has been declared as a <strong class="text-orange"> Regular Holiday.</strong> The payout date will be moved <strong class="text-primary"> from May 25, 2020 to May 22, 2020.</strong><br/><br/>

 

                      In view of this, we will move the payroll cut-off to May 18, 2020.  Attendance from May 19 to 20, 2020 will be paid in full.  Any absence, tardiness, and overtime incurred during that two-day period will be reflected on June 10, 2020 payroll.<br/><br/>

                       

                      Your daily time record should be approved and locked in EMS on or before 12:00 noon of May 20, 2020.<br/><br/>

                       

                      <strong class="text-primary"> ECQ Incentives - </strong> to make sure that the DATA MANAGEMENT TEAM will capture the correct and updated data, kindly update the link that they have shared on or before 12 noon of May 20, 2020.<br/><br/>


                      Please be guided accordingly. <br/><br/>

                       

                      Kindly email <strong>salaryinquiries@openaccessbpo.com</strong> for any questions or concerns you may have.<br/><br/>

                      Thank you.<br/><br/><strong>Finance Team</strong>
                    

                    </p>  
                    
              </div>
                
  <div class="item text-center" >
                  <h4 class="text-orange" style="line-height: 1.5em" >Announcement:<br/><span class="text-primary">Let's Get Physical<br/>
                  
                    <img src="storage/uploads/divider.png" />
                    </h4>

                    <img src="storage/uploads/physicalinvite.jpg" width="100%" /><br/><img src="storage/uploads/ecq_zumba.jpg" width="100%" /><br/>
                    
                   
                    
                    
              </div>

 <div class="item text-center" >
                  
                  <img src="storage/uploads/fitr.jpg" width="100%" />
                  <h4 class="text-orange" style="line-height: 1.5em" >Eid Al Fitr
                  <br/>
                    <img src="storage/uploads/divider.png" />
                    </h4>
                    
                    <p style="padding: 30px;" class="text-left">
                     
                   
                     Today marks the end of Ramadan. We wish our Muslim brothers and sisters who are celebrating on #EidAlFitr, a blessed and safe Eid!<br/><br/>
                    #WeSpeakYourLanguage #OAHolidays</p>    
 
                    
              </div>

              <div class="item text-center" >
                  
                  <img src="storage/uploads/worldday.jpg" width="100%" /><br/><br/>
                  <h4 class="text-orange" style="line-height: 1.5em" >World Day for Cultural Diversity <br/> <span class="text-primary">For Dialogue &amp; Development
                  <br/>
                    <img src="storage/uploads/divider.png" />
                    </h4>
                    
                    <p style="padding: 30px;" class="text-left">
                     
                   
                      As a multilingual company, many within our Open Access BPO family are kept away from their loved ones due to the COVID-19 pandemic. This World Day for Cultural Diversity for Dialogue & Development, send a virtual hug to those yearning for their loved ones and homes.<br/><br/>#WeSpeakYourLanguage #OAonDiversityDay</p>    
 
                    
              </div>
<div style="background: url('storage/uploads/memobg.png')top left repeat-y; background-size: 50%;background-color: #fff;padding:20px" class="item text-center" >
                  <h4 class="text-orange" style="line-height: 1.5em" > All Shuttle Passengers: <br/> <span class="text-primary"><i class="fa fa-car"></i>   UPDATED POLICY AND PROCEDURES <br/> FOR CHANGE REQUESTS: <br/>PICKUP TIME and ADDRESSES </i><br/> <br/>
                   <img src="storage/uploads/shuttle.png" width="80%" /><br/><br/>
                    <img src="storage/uploads/divider.png" />
                    </h4>
                    <p style="padding: 30px;" class="text-left">
                     TO ALL SHUTTLERS<br/><br/>


                    <strong class="text-primary">Deadline</strong> <br/>

                      Requests to change pick-up time should be submitted at least 24 hours before the schedule. <br/>

                      Same-day adjustment requests will no longer be accommodated. <br/> <br/>
 

                       <strong class="text-primary">Request Limit</strong> <br/>

                      To comply with the trip scheduling process and to encourage everyone to plan ahead, each shuttler will be allowed no more than 3* change-requests for the whole duration of ECQ. This will prevent disrupting preset driver and passenger trips. Once the limit has been reached no further requests will be processed.<br/> <br/>

                      * Reasons for pickup time changes due to CWS will require WFM validation.<br/> <br/>

                       

                       

                       <strong class="text-primary">Request Submission Procedure</strong><br/>

                      To avoid confusing multiple entries and unnecessary changes that disrupt trip scheduling and inconvenience other riders, if you are still within the request cap, please follow the steps enumerated below.<br/> <br/>


                      1.        Submit a Shuttle Application request at least 1-2 days before the set ride.<br/><br/>
                      <a class="btn btn-md btn-success" href="https://docs.google.com/forms/d/e/1FAIpQLScN8f9jalq2yUQJbOmw7cY2TYDV5TO5GFMEW9S3opZ7JD9wGQ/viewform?vc=0&c=0&w=1" target="_blank"><i class="fa fa-file-o"></i> Shuttle Application Form </a><br/><br/>

                      2.        Send an email to the Transport Team to itemize the schedule change details (follow the format below), including the link to the application form.<br/>

<pre>
Home-Pickup date change from:______To: _______

Home-Pickup time change from:______To: _______

Pickup address change from: ______To: _______

Office-Pickup date change from:______To: _______

Office-Pickup time change from:______To: _______
</pre><br/><br/>
                      3. Wait for a confirmation if the request is accommodated.<br/><br/>


                      <strong class="text-primary">Subject to Dispatch Review and Authorization</strong><br/>

                      Validated urgent same-day adjustment requests are SUBJECT TO REVIEW. <br/><br/>

                      If it is a WFM-approved CWS, attach its proof to your email request and complete the steps above.<br/><br/>

                       

                      As master trip lists for each day get set 2 days prior, any changes may potentially mess up the plotted queue of the driver routes and schedules.  Thus, they may not be accommodated or at the very least, take time to confirm and plug into the existing sequence. Please be prepared for the request to possibly either take time to be accommodated or be declined during high volume during peak hours.<br/><br/>


                      For your compliance and guidance.


                    

                    </p>  
                    
                </div>

<div style="padding:0px" class="item  text-center" >
                  <h4 class="text-orange" style="line-height: 1.6em" > April 7,2020: <br/> <span class="text-primary">WORLD HEALTH DAY <br/>
                   <img src="storage/uploads/frontliners.jpg" width="100%"><br/>
                    <img src="storage/uploads/divider.png" />
                    </h4>
                    <p style="padding: 30px;" class="text-left">
                     
                      This <strong>#WorldHealthDay,</strong> we thank those who braved the frontlines to minimize the risk of COVID-19 infections across our communities. Stay safe and healthy!<br/><br/>#WeSpeakYourLanguage #WorldHealthDay
                    

                    </p>  
                    
              </div>



 <div style="background: url('storage/uploads/memobg.png')top left repeat-y; background-size: 50%;background-color: #fff;padding:20px" class="item  text-center" >
                  <h4 class="text-orange" >Reminders: <br/> <span class="text-primary"> <i class="fa fa-info-circle-o"></i>EMS as primary system for timekeeping <br/> <strong> effective IMMEDIATELY</strong><br/> <br/>
                    <img src="storage/uploads/divider.png" /><br/>
                    <img src="storage/uploads/emstimekeeping.png" width="60%" />
                    </h4>
                    
                    <!-- <h3 class="text-danger"><i class="fa fa-medkit"></i> Health Alert <i class="fa fa-medkit"></i> </h3> -->
                    <p style="padding: 50px;" class="text-left"><br/>
                     In compliance to WHO’s mandatory protective measures to maximize social distancing and minimize risk and contact with high-touch surfaces, we are suspending the use of our biometrics devices for clocking in and out:  <strong class="text-primary"> EMS will now be our primary source of daily timekeeping.</strong><br/><br/>
                     Log in to EMS to access  the Timekeeping Manager on the Landing Page and click:<br/></br/>
                      <strong class="text-success"> System CHECK IN </strong>to clock in <br/>
                      <strong>Breaktime START </strong>to go on break <br/>
                      <strong>Breaktime END</strong> to go back to work after a break<br/>
                      <strong class="text-danger">System CHECK OUT</strong> to clock out<br/>



                    </p>

                   
                    
                </div>

 <div style="background: url('storage/uploads/memobg.png')top left repeat-y; background-size: 50%;background-color: #fff;padding:20px" class="item  text-center" >
                  <h4 class="text-orange" style="line-height: 1.5em" >Message from Finance team: <br/> <span class="text-primary">Advance 13th Month Pay <br/>and SL Credits During ECQ <br/>
                   <!-- <img src="storage/uploads/dswd.png" width="50%"><br/> -->
                    <img src="storage/uploads/divider.png" />
                    </h4>
                    <p style="padding: 30px;" class="text-left">
                     
                      During ECQ, we have made several inclusions to our payout.  Among those are advance 13th month pay and advance leaves 5 days SL credits.  Please see information below for your guidance.<br/><br/>

 

                       <strong class="text-primary">· The advanced 13th month pay was released on April 8, 2020</strong> for employees who did not opt out to receive their advanced 13 month pay.  For those who opted out, they will receive their 13th month pay in full in December 2020.  We will individually send you an email as confirmation.<br/><br/>

                       <strong class="text-primary">·  Sick leave credits have been advanced since March 25th payout until 5 SL credits have been fully used up.</strong>   For probationary employees, advanced SL credits will be deducted upon regularization.  For contractual employees with a 6-month contract or above, advanced SL credits will be deducted after 6 months. For contractual employees with a contract below 6 months, advanced SL credits will be deducted from their last pay if their contract is not renewed. For regular employees, advanced SL will be deducted from their earned SL every cut-off.  Please make sure to regularly file your leave and monitor your leave balances in EMS.<br/><br/>

                      Please be guided accordingly.<br/><br/>

                       

                      Kindly email <strong>salaryinquiries@openaccessbpo.com</strong> for any questions or concerns you may have.<br/><br/>

                      Thank you.<br/><br/><strong>Finance Team</strong>
                    

                    </p>  
                    
              </div>
<div class="item text-center" >
                  <h4 class="text-orange" style="line-height: 1.5em" >Greetings to all moms out there<br/><span class="text-primary"> <i class="fa fa-heart"></i> HAPPY MOTHER'S DAY! <i class="fa fa-heart"></i> </span><br/><img src="storage/uploads/divider.png" /><br/>
                   <img src="storage/uploads/moms2020.jpg" width="98%" />
                    
                    </h4>
                    <p style="padding: 30px;" class="text-left">
                     
                    Today and always, send her love no matter the distance.<br/><br/>

                    Happy Mother's Day to all moms and mother figures out there! Thank you for your unconditional love.<br/><br/>

                    <strong>#WeSpeakYourLanguage #OAonMothersDay.</strong><br/><br/>


                  </p>
 
                    
              </div>
 <div style="background: url('storage/uploads/memobg.png')top left repeat-y; background-size: 50%;background-color: #fff;padding:20px" class="item  text-center" >
                  <h4 class="text-orange" style="line-height: 1.5em">Reminders from Finance team: <br/> <span class="text-primary"> <i class="fa fa-money"></i> &nbsp;&nbsp; April 24,2020 Payout &nbsp;&nbsp; <i class="fa fa-money"></i> <br/>
                   <!-- <img src="storage/uploads/dswd.png" width="50%"><br/> -->
                    <img src="storage/uploads/divider.png" />
                    </h4>
                    <p style="padding: 30px;" class="text-left">
                     
                      To make sure all worked hours and ECQ benefits are applied accurately for this payout, please lock all DTR's and Employee Status Trackers <strong class="text-danger"> on or before 12:00 noon of April 21, 2020</strong>. <br/><br/>

                      The payroll period  will  start on April 4th since we had our early cut off last payroll .<br/><br/>

 

                       <strong class="text-primary">All employees:</strong>

                       Please ensure your entire DTR's for the period April 4 to 30 are locked on EMS, on or before 12:00 noon of April 21, 2020. <br/><br/>

                       

                      <strong class="text-primary">TL's and Managers:</strong>

                      Please make sure all relevant updates are entered on the Employee Status Tracker on or before the deadline too. You can generate EMS to check  those employees who have not yet completed their logs.  <br/><br/>

                       

                      <strong class="text-primary">After 12:00 noon of April 21, 2020:</strong>

                      Locked/updated or otherwise, the Finance Team will assume the current data reflecting as final for salary computation and crediting. Any update or changes after the said deadline will be included on the next payout. <br/><br/>

                       

                      Kindly email <strong>salaryinquiries@openaccessbpo.com</strong> for any questions or concerns you may have.<br/><br/>

                      Thank you.<br/><br/><strong>Finance Team</strong>
                    

                    </p>  
                    
              </div>
<div style="background: url('storage/uploads/memobg.png')top left repeat-y; background-size: 50%;background-color: #fff;padding:20px" class="item text-center" >
                  <h4 class="text-orange" style="line-height: 1.5em" > Message from Finance: <br/> <span class="text-primary"><i class="fa fa-calculator"></i>   Holiday Pay Treatment <br/>for April 9, 10 and 11 <br/><br/>
                  
                    <img src="storage/uploads/divider.png" />
                    </h4>
                    <p style="padding: 30px;" class="text-left">
                     
                    Dear All,<br/><br/>
                    As stated in Malacañang Proclamation No. 845, the following have been declared as holidays for the month of April 2020.<br/><br/>
                    Kindly see below details on the holiday pay treatment for April 9, 10 and 11.<br/><br/>


                    <strong class="text-primary" style="font-size: large;"> Worked: </strong></p>
                    <strong class="text-success">April 9 - Araw ng Kagitingan &amp;<br/>Holy Thursday (2 Regular Holidays)</strong>
                    <pre>(Basic Pay + Allowance) x 300%</pre>
                    <strong class="text-success">April 10 – Good Friday (Regular Holiday)</strong>
                    <pre>(Basic Pay + Allowance) x 200%</pre>
                    <strong class="text-success">April 11 – Black Saturday (Special non-working holiday)</strong>
                    <pre>(Basic Pay + Allowance) x 150%</pre>
 
                    
              </div>

              <div style="background: url('storage/uploads/memobg.png')top left repeat-y; background-size: 50%;background-color: #fff;padding:20px" class="item text-center" >
                  <h4 class="text-orange" style="line-height: 1.5em" > Message from Finance: <br/> <span class="text-primary"><i class="fa fa-calculator"></i>   Holiday Pay Treatment <br/>for April 9, 10 and 11 <br/><br/>
                  
                    <img src="storage/uploads/divider.png" />
                    </h4>
                    <p style="padding: 30px;" class="text-left">
                     
                    Dear All,<br/><br/>
                    
                    Kindly see below details on the holiday pay treatment for April 9, 10 and 11.<br/><br/>


                    <strong class="text-primary" style="font-size: large;">Additional pay for work done <span class="text-danger">in excess of 8 hours</span> </strong></p>
                    <strong class="text-success">April 9 - Araw ng Kagitingan &amp;<br/>Holy Thursday (2 Regular Holidays)</strong>
                    <pre>[(Basic Pay + Allowance) ÷ 22 days ÷ 8 hours] x [number of hours worked] x  300% x 150%</pre>
                    <strong class="text-success">April 10 – Good Friday (Regular Holiday)</strong>
                    <pre>[(Basic Pay + Allowance) ÷ 22 days ÷ 8 hours] x [number of hours worked] x  200% x 150%</pre>
                    <strong class="text-success">April 11 – Black Saturday (Special non-working holiday)</strong>
                    <pre>[(Basic Pay + Allowance) ÷ 22 days ÷ 8 hours] x [number of hours worked] x  150% x 150%</pre>
 
                    
              </div>

              <div style="background: url('storage/uploads/memobg.png')top left repeat-y; background-size: 50%;background-color: #fff;padding:20px" class="item text-center" >
                  <h4 class="text-orange" style="line-height: 1.5em" > Message from Finance: <br/> <span class="text-primary"><i class="fa fa-calculator"></i>   Holiday Pay Treatment <br/>for April 9, 10 and 11 <br/><br/>
                  
                    <img src="storage/uploads/divider.png" />
                    </h4>
                    <p style="padding: 30px;" class="text-left">
                     
                    Dear All,<br/><br/>
                    
                    Kindly see below details on the holiday pay treatment for April 9, 10 and 11.<br/><br/>


                    <strong class="text-primary" style="font-size: large;">Worked, and falls on the <span class="text-orange">REST DAY </span>of the employee </strong></p>
                    <strong class="text-success">April 9 - Araw ng Kagitingan &amp;<br/>Holy Thursday (2 Regular Holidays)</strong>
                    <pre>[(Basic Pay + Allowance) ÷ 22 days ÷ 8 hours] x [number of hours worked] x  300% x 150%</pre>
                    <strong class="text-success">April 10 – Good Friday (Regular Holiday)</strong>
                    <pre>[(Basic Pay + Allowance) ÷ 22 days ÷ 8 hours] x [number of hours worked] x  200% x 150%</pre>
                    <strong class="text-success">April 11 – Black Saturday (Special non-working holiday)</strong>
                    <pre>[(Basic Pay + Allowance) ÷ 22 days ÷ 8 hours] x [number of hours worked] x  150% x 150%</pre>
 
                    
              </div>

              <div style="background: url('storage/uploads/memobg.png')top left repeat-y; background-size: 50%;background-color: #fff;padding:20px" class="item text-center" >
                  <h4 class="text-orange" style="line-height: 1.5em" > Message from Finance: <br/> <span class="text-primary"><i class="fa fa-calculator"></i>   Holiday Pay Treatment <br/>for April 9, 10 and 11 <br/><br/>
                  
                    <img src="storage/uploads/divider.png" />
                    </h4>
                    <p style="padding: 30px;" class="text-left">
                     
                    Dear All,<br/><br/>
                    As stated in Malacañang Proclamation No. 845, the following have been declared as holidays for the month of April 2020.<br/><br/>
                    Kindly see below details on the holiday pay treatment for April 9, 10 and 11.<br/><br/>


                    <strong class="text-primary" style="font-size: large;">Additional pay for work done <span class="text-danger">IN EXCESSS OF 8 hours</span>, and falls on the employee's <span class="text-orange">REST DAY</span> </strong></p>
                    <strong class="text-success">April 9 - Araw ng Kagitingan &amp;<br/>Holy Thursday (2 Regular Holidays)</strong>
                    <pre>[(Basic Pay + Allowance) ÷ 22 days ÷ 8 hours] x [number of hours worked] x  300% x 150%</pre>
                    <strong class="text-success">April 10 – Good Friday (Regular Holiday)</strong>
                    <pre>[(Basic Pay + Allowance) ÷ 22 days ÷ 8 hours] x [number of hours worked] x  200% x 150%</pre>
                    <strong class="text-success">April 11 – Black Saturday (Special non-working holiday)</strong>
                    <pre>[(Basic Pay + Allowance) ÷ 22 days ÷ 8 hours] x [number of hours worked] x  150% x 150%</pre>

                    For employees who were hired on March 17, 2020 to April 8, 2020, they will be entitled to regular holiday if they are present or with paid leave on workday immediately preceding the holiday. <br/><br/>Please be guided accordingly.
 
                    
              </div>

 <div style="background: url('storage/uploads/memobg.png')top left repeat-y; background-size: 50%;background-color: #fff;padding:20px" class="item text-center" >
                  <h4 class="text-orange" style="line-height: 1.5em" >Calling all <strong>agents, specialists,<br/> TL’s, and non-manager support personnel!</strong><br/>
                  If this is you…<br/>

                   <br/> <span class="text-primary"> THERE IS A NEW <br/><i class="fa fa-clock-o"></i>  TIMECLOCK TOOL FOR YOU!<br/>
                  EFFECTIVE <strong class="text-danger">May 5, 2020 at 11:59pm</strong><br/><br/>
                  
                    <img src="storage/uploads/divider.png" />
                    </h4>
                    <p style="padding: 30px;" class="text-left">
                     
                    <strong> BUNDY.PH</strong>
                    Will be your new place to clock in and out at the start and end of your workday<br/><br/>
                    <img src="storage/uploads/bundy.png" width="100%" /><br/><br/>



                    <strong class="text-primary"> New Tool Use for Who</strong><br/>
                    For agents, frontline team members, team leaders, non-manager support personnel
                    Eg: Specialists, Officers, IT desktop support, QA Analysts, Trainers<br/><br/>

                    <strong class="text-primary"> New Tool Use for What</strong><br/>
                    Solely for clocking in and clocking out<br/>
                    This replaces the biometric login terminal and EMS clock<br/><br/>

                    <strong class="text-primary"> New Tool Use Starting When</strong><br/>
                    May 3, 2020 Sunday 11:59 pm!<br/><br/>

                    <strong class="text-primary"> Accessing the New Timeclock</strong><br/>
                    Access Codes and Passwords will be generated and assigned by the Timekeeping Team <br/><br/>
                    Please expect your credentials to be emailed to you directly by the Finance May 1, 2020 Friday. <br/><br/>
                    No reset is required. <br/><br/>
                    Be sure to remember them and keep them safe.<br/><br/>
                    Test them ASAP: <br/><br/><br/>
                    As soon as you receive your credentials via email, please click the Bundy.ph url and test your assigned access code and password.<br/><br/>
                    For login issues, reach out to your supervisor for help and ask them to email timekeeping@openaccessbpo.com immediately.<br/><br/>

                    <strong class="text-primary"> Camera Feature Will Not Be Used</strong><br/></strong><br/>
                    Not all of our deployed computers have webcams. (The Bundy,ph camera feature is optional.)<br/><br/>
                    Only your Access Code and Password credentials will be required. <br/><br/>

                    <strong class="text-primary"> EMS Continued Use for</strong><br/>
                     - Validating Daily Time Records<br/><br/>
                     - Filing Daily Time Record Problems<br/><br/>
                     - Filing leaves<br/><br/>
                     - Filing overtime<br/><br/>

                    <strong class="text-primary"> Timekeeping Policies</strong><br/>
                    While the tool has changed, the existing company policies for timekeeping remain the same.<br/><br/>
                    Everyone is required to strictly adhere to specific timekeeping rules and regulations in our Employee Manual.<br/><br/>
                    Timekeeping violations will be penalized. <br/><br/>
                    Please refer to the Open Access Employee Manual for the sanctions and *discipline schedule.<br/><br/>
                    *Includes but not limited to: Rule IX Timekeeping & Productivity, Rule III Offense Against Company Interest<br/><br/>


                    To prevent inaccurate or inexistent logs negatively impacting your pay, please ensure consistency and accuracy in managing your timekeeping.<br/><br/>

                    <em class="text-success"><strong>Ready to practice? Login to our LMS at <br/>
                      <span style="color:#333"> https://open-access.training-online.eu </span>and enroll in the course Bundy.PH. <br/><br/>
                    To get to the tool and video guide, copy-paste the links below on a browser.</strong> </em><br/><br/>

                     <img src="storage/uploads/divider.png" />

                    <strong class="text-primary"> HOW TO GET THERE</strong>  <img src="storage/uploads/divider.png" /><br><br/>

 

                    <i class="fa fa-info-circle"></i> If you are using the VPN link to access EMS and Zimbra, copy and paste the tool and video links below on a browser  instead of directly clicking on it from the email or EMS announcement.<br><br/>

                     

                    <i class="fa fa-info-circle"></i> Directly clicking the URL or right-clicking then selecting Copy Link Address from there will not work because extraneous elements are added to them, thus failing to load the web page.<br><br/>

                     

                    If you manually highlight the link and copy, you should be able to get to the new web Bundy tool and the video guide.<br><br/>

 

 

                    <strong class="text-primary">LINKS</strong><br><br/>

                     

                     

                    <strong class="text-success"> Bundy.ph Time Tool</strong><br/>
                    <input class="form-control" type="text" id="bundylink" value="https://bundy.payroll.ph/web/oampi" />
                    <button class="cp btn btn-xs btn-primary" data-link="bundylink">Copy Link <i class="fa fa-external-link"></i></button>

                     <br><br/>

                  

                     <strong class="text-success">Web Bundy Video Guide</strong><br> 
                     <input class="form-control" type="text" id="videolink" value="https://rise.articulate.com/share/wI1ScLEfBkoCJKPJj5b7qaR34W8XPr2b#/" />
                     <button class="cp btn btn-xs btn-primary" data-link="videolink">Copy Link <i class="fa fa-external-link"></i></button>

                    <br><br/>



                    <strong>Definitions:</strong><br/>
                    <em style="font-size: x-small;">*Non-exempt employees are those eligible for overtime, night differential, and holiday premiums.<br/> 
                     Use Bundy.ph for clock in and clock out. <br/>
                     Eg: Agents, Frontline Team Members, Team Leaders, Non-Manager Support Personnel, Specialists, Officers, IT Desktop Support, QA   
                     Analysts, Trainers<br/><br/>

                     Exempt employees are those not eligible for those premiums and enjoy flexible start-times. <br/>
                     Use EMS for all timekeeping activities.<br/>
                     Eg: GTL’s, Managers</em>




                  </p>
 
                    
              </div>


<div style="background: url('storage/uploads/memobg.png')top left repeat-y; background-size: 50%;background-color: #fff;padding:20px" class="item  text-center" >
                  <h4 class="text-orange" style="line-height: 1.5em" > Message from Finance: <br/> <span class="text-primary"><i class="fa fa-calculator"></i>   Holiday Pay Treatment <br/>for May 1 (Labor Day) <br/><br/>
                  
                    <img src="storage/uploads/divider.png" />
                    </h4>
                    <p style="padding: 30px;" class="text-left">
                     
                    Dear All,<br/><br/>
                    
                    Kindly see below details on the holiday pay treatment for May 1, 2020<br/><br/>


                    <strong class="text-primary" style="font-size: large;"> Unworked:</strong> <br/><em> provided the employee was present or on leave with pay on the workday prior to the start of May 1, 2020</em><pre> (Basic Pay + Allowance) x 100%</pre><br/><br/>

                    <strong class="text-primary" style="font-size: large;"> Worked:</strong> <br/>
                    <pre> (Basic Pay + Allowance) x 200%</pre><br/><br/>

                    <strong class="text-primary" style="font-size: large;"> Additional pay for work done <span class="text-danger">in excess of 8 hours</span></strong> <br/>
                    <pre>   [(Basic Pay + Allowance) ÷ 22 days ÷ 8 hours] x [number of hours worked] x  200% x 150%</pre><br/><br/>

                    <strong class="text-primary" style="font-size: large;"> Worked, and falls on the <span class="text-danger"> rest day</span> of the employee</strong> <br/>
                    <pre>   [(Basic Pay + Allowance) ÷ 22 days ÷ 8 hours] x [number of hours worked] x  200% x 150%</pre><br/><br/>


                    <strong class="text-primary" style="font-size: large;"> Additional pay for work done <span class="text-danger">in excess of 8 hours</span>, and falls on the employee's <span class="text-danger">rest day</span></strong> <br/>
                    <pre>   [(Basic Pay + Allowance) ÷ 22 days ÷ 8 hours] x [number of hours worked] x  200% x 150%</pre><br/><br/>


                    Please be guided accordingly.  Thank you.

                  </p>
 
                    
              </div>

 <div style="padding:0px" class="item text-center" >
                 <!--  <h4 class="text-orange" style="line-height: 1.6em" > Happy<br/> <span class="text-primary">Easter!<br/> -->
                   <h4 class="text-orange" style="line-height: 1.6em" > <img src="storage/uploads/easter2020.jpg" width="100%"><br/>
                    <img src="storage/uploads/divider.png" />
                    </h4>
                    <p style="padding: 30px;" class="text-left">
                     
                     May the spirit of #Easter bring light and hope into your home. Happy Easter!<br/><br/><strong>#WeSpeakYourLanguage #OAHolidays</strong>
                    

                    </p>  
                    
              </div>
 <div style="background: url('storage/uploads/memobg.png')top left repeat-y; background-size: 50%;background-color: #fff;padding:20px" class="item text-center" >
                  <h4 class="text-orange" style="line-height: 1.6em" ><i class="fa fa-exclamation-triangle"></i> Reminders: <br/> <span class="text-primary"> <i class="fa fa-info-circle-o"></i><strong class="text-danger"> Opting Out</strong> for Advance 13th Month Pay<br/>
                 <br/>
                    
                    <img src="storage/uploads/13thmonth.jpg" width="90%" /><br/><br/><img src="storage/uploads/divider.png" /></h4>
                    
                    <!-- <h3 class="text-danger"><i class="fa fa-medkit"></i> Health Alert <i class="fa fa-medkit"></i> </h3> -->
                    <p style="padding: 50px;" class="text-left"><br/>
                      Please be reminded that you still have until <strong>April 5</strong> to opt out your advance 13th-month pay.<br/> <span class="text-danger">For those opting out</span>, you will be receiving your <strong> <span class="text-danger"> 13th month pay in full </span>on December 2020</strong>. <br/><br/>

                      For those <strong>who did not send an email</strong> to opt out on salaryinquiry@openaccessbpo.com until April 5, 2020, you will receive 6/12 of your 13th-month pay on April 8, 2020.  <br/><br/>

                       

                      Below are common FAQs:<br/><br/>

                       

                      <strong class="text-primary"> Who is covered for the advance release of 13th-month pay?</strong><br/>

                      Regular, contractual, and project-based employees as of April 5, 2020.<br/><br/>

                       

                      <strong class="text-primary"> Who is not covered for the advance release of 13th-month pay?</strong><br/>

                      Employees who have tendered their resignation.

                      Trainees and Probationary employees.<br/><br/>

                       

                     <strong class="text-primary">  How is advance 13th-month pay computed?</strong><br/><br/><br/>
                     
                     <strong>For Regular Employees </strong>: 
                     <pre> Monthly basic and allowance x 6 months<br/>-----------------------------------<br/>12 months</pre> 

                                                                  

                       

                      <br/><br/><strong>For Contractuals & Project Based Employees:</strong><br/>

                      <pre>Monthly basic and allowance <br/>X  number of months indicated <br/>in the contract  during CY2020) <br/>X 50%<br/>--------------------------------------------<br/>12 Months</pre> <br/><br/>

                       

                     <strong class="text-primary">  When is the release of advance 13th-month pay?</strong><br/>

                      It will be paid on April 8, 2020.<br/><br/>

                       

                      <strong class="text-primary"> What will happen if the employee did not fully render the advanced 13th-month pay?</strong><br/>

                      It will be deducted from employees' last pay.

                    </p>

                   
                    
                </div>
 <div style="background: url('storage/uploads/memobg.png')top left repeat-y; background-size: 50%;background-color: #fff;padding:20px" class="item text-center" >
                  <h4 class="text-orange" ><sup>1/2</sup> 13th-Month  <span class="text-primary"> <i class="fa fa-info-circle-o"></i><br/>Advance Payout<br/>
                 <br/>
                    <img src="storage/uploads/divider.png" /><br/>
                    <img src="storage/uploads/13thmonth.jpg" width="90%" /></h4>
                    
                    <!-- <h3 class="text-danger"><i class="fa fa-medkit"></i> Health Alert <i class="fa fa-medkit"></i> </h3> -->
                    <p style="padding: 50px;" class="text-left"><br/>
                      - Payout Schedule:<strong class="text-danger"> April 10</strong><br/><br/>
                      - Computation will be based on <strong>6/12</strong> of monthly base salary+non-taxable allowance<br/><br/>
                      - Example: (((10,000 + 2,800))/12 months) x 6 months = Php 6,400<br/><br/>
                      - <strong class="text-primary">Employee may opt out (to get the 13th month in full during December payout). </strong><br/>
                     Send an email to:  <strong>salaryinquiry@openaccessbpo.com</strong>

                    </p>

                   
                    
                </div>
  <div style="background: url('storage/uploads/memobg.png')top left repeat-y; background-size: 50%;background-color: #fff;padding:20px" class="item text-center" >
                  <h4 class="text-orange" > <span class="text-primary"> <i class="fa fa-clock-o"></i> Timekeeping Policy </span><br/><img src="storage/uploads/divider.png" /></h4>
                    <!-- <h3 class="text-danger"><i class="fa fa-medkit"></i> Health Alert <i class="fa fa-medkit"></i> </h3> -->
                    <p style="padding: 50px" class="text-left">Dear ALL,<br /><br/>

                      Here is the timekeeping policy which we will apply for this cut-off. <br /><br/>

                      <strong>TIMEKEEPING POLICY</strong><br/><br/>
                      - Work-from-home employees need to update their logs via EMS.<br/><br/>
                      - EMS approvers or team leaders should approve DTRs on or before the cut-off date.<br/><br/>
                      - Monitoring of actual attendance from March 6 to 15, 2020 still applies.<br/><br/>
                      - For employees with zero logs from March 16 to 20, 2020, we will apply SL first then VL if former was fully consumed.  If they don't have leave credits anymore, we will advance the SL/ VL for them.<br /><br/>
                      Thank you and stay safe!</p> <br /><br/>
                </div>

                <div style="background: url('storage/uploads/memobg.png')top left repeat-y; background-size: 50%;background-color: #fff;padding:20px" class="item text-center" >
                  <h4 class="text-orange" > <span class="text-primary"> SKELETAL WORKFORCE  </span><br/><img src="storage/uploads/divider.png" /></h4>
                    <!-- <h3 class="text-danger"><i class="fa fa-medkit"></i> Health Alert <i class="fa fa-medkit"></i> </h3> -->
                    <p style="padding: 50px" class="text-left">As you may know, Business Process Outsourcing companies are permitted to remain operational despite the imposed Enhanced Community Quarantine. Thus, for those who are willing to report to the office this week, especially those with private vehicles or residing nearby, please be advised that Open Access BPO's G2 and 6780 Makati sites may accommodate a skeletal workforce. <br/><br/>
                      If you wish to voluntarily report to work, kindly inform your Immediate Head and wait for your Immediate Head's confirmation before proceeding to the office.  Kindly take note that only those who are required and permitted by the Program Manager and/or Client  to render services may be allowed to report to the office. Lastly, be reminded to strictly practice social distancing.<br/><br/>Thank you and stay safe.</p> <br /><br/>
                </div>



    <div class="item text-center" >
                  <h4 class="text-orange" >Find the Hidden <span class="text-primary"> Logos </span></h4>
                    <!-- <h3 class="text-danger"><i class="fa fa-medkit"></i> Health Alert <i class="fa fa-medkit"></i> </h3> -->
                    <img src="./storage/uploads/hiddenOA.jpg" style="z-index: 2" />
                    <br/><br/>
                    <a class="btn btn-md btn-danger" href="{{action('EngagementController@show',4)}}#resource_4"><i class="fa fa-info-circle"></i> Learn More </a><br /><br/>
                </div>



                

              
                <!-- <div class="item text-center" >
                   <h4 class="text-orange" >International <span class="text-primary"> Women's Day </span></h4>
                              <img src="./storage/uploads/womansday2020.jpg" style="z-index: 2" /><br /><br/>
                              <p class="text-left" style="padding-left: 50px;">It's #InternationalWomensDay. We recognize and celebrate the incredible work accomplished by Open Access BPO’s women employees in strengthening a diverse and inclusive workplace culture. We recognize and celebrate their contributions that enable the service industry to thrive.<br/><br/>

Happy International Womens Day!
                </div>

                <div class="item text-center" >
                   <h4 class="text-orange" >Information <span class="text-primary"> Security </span></h4>
                              <img src="./storage/uploads/compliance1.jpg" style="z-index: 2" /><br /><br/>
                </div>

                <div class="item text-center" >
                   <h4 class="text-orange" >Daily <span class="text-primary"> Motivation </span></h4>
                              <img src="./storage/uploads/champions.jpg" style="z-index: 2" />
                               <p class="text-left" style="padding-left: 50px;"><br/><br/>Never let your failures be the reason why you can't succeed. Just keep on trying and believe that soon, you'll finally get to where you've always wanted to be. After all, creating a masterpiece takes time.<br/><strong>#WeSpeakYourLanguage #MondayMotivation </strong></p>

                    <br /><br/>
                </div> -->



  <div class="item text-center" >
                  <h4 class="text-orange" >Health &amp;<span class="text-primary"> Awareness </span></h4>
                    <!-- <h3 class="text-danger"><i class="fa fa-medkit"></i> Health Alert <i class="fa fa-medkit"></i> </h3> -->
                    
                    <img src="./storage/uploads/ncov2019-2.jpg" style="z-index: 2" /><br/><br/>
                    <a class="btn btn-md btn-danger" href="{{action('ResourceController@index')}}#resource_6"><i class="fa fa-info-circle"></i> Learn More </a><br /><br/>
                </div>

                <div class="item  text-center" >
                  <h4 class="text-orange" >Open Access BPO <span class="text-primary"> Painting Contest </span></h4>
                    <!-- <h3 class="text-danger"><i class="fa fa-medkit"></i> Health Alert <i class="fa fa-medkit"></i> </h3> -->
                    <img src="./storage/uploads/allEntries.jpg" style="z-index: 2" />
                    <br/><br/>
                    <a class="btn btn-md btn-danger" href="{{action('EngagementController@show',3)}}"><i class="fa fa-paint-brush"></i> View All Entries </a><br /><br/>
                </div>

                <div class="item text-center" >
                  <h4 class="text-orange" >Health &amp;<span class="text-primary"> Awareness </span></h4>
                    <!-- <h3 class="text-danger"><i class="fa fa-medkit"></i> Health Alert <i class="fa fa-medkit"></i> </h3> -->
                    <img src="./storage/uploads/ncov2019.jpg" style="z-index: 2" />
                    <br/><br/>
                    <a class="btn btn-md btn-danger" href="{{action('ResourceController@index')}}#resource_6"><i class="fa fa-info-circle"></i> Learn More </a><br /><br/>
                </div>
                <div class="item text-center" style="background: url('./storage/uploads/coffeebg.jpg')bottom center no-repeat; padding:50px" >
                   <h4 class="text-orange" >NEW BARISTA <span class="text-primary"> SCHEDULE </span></h4>
                              <br /><br/>
                              <p class="text-left" style="padding-left: 30px;color:#fff;text-shadow: 2px 2px #333"> 
                                To accommodate more of our Open Access teams, we have rescheduled our Barista Shifts to match our peak hours and staffing:  <br/><br/>
                                Mondays through Fridays<br/>
                                <strong class="text-yellow">6:00AM - 3:00 PM<br/>
                                2:00 PM - 11:00PM</strong><br/>

                                <br/><br/>Here to serve and prep with happiness and care!</p>

                </div>

 <div style="background: url('storage/uploads/memobg.png')top left repeat-y; background-size: 50%;background-color: #fff;padding:20px" class="item  text-center" >
                  <h4 class="text-orange" >Reminders: <br/> <span class="text-primary"> <i class="fa fa-info-circle-o"></i><sup>1/2</sup> 13th-Month Advance Payout<br/>
                 <br/>
                    <img src="storage/uploads/divider.png" /><br/>
                    <img src="storage/uploads/13thmonth.jpg" width="90%" /></h4>
                    
                    <!-- <h3 class="text-danger"><i class="fa fa-medkit"></i> Health Alert <i class="fa fa-medkit"></i> </h3> -->
                    <p style="padding: 50px;" class="text-left"><br/>
                      For those who opted to receive <sup>1/2</sup> 13th month pay, this will be included in the <strong>April 8th payout.</strong><br/><br/>

                      Employees may opt out <em>(meaning they will get the 13th month in full during December payout)</em> by sending an email to <a href="mailto:salaryinquiry@openaccessbpo.com">salaryinquiry@openaccessbpo.com</a> <strong class="text-orange">no later than April 5, 2020</strong><br/><br/> -------------------------- <br/><br/><strong><i class="fa fa-envelope"></i> subject line:</strong> <br/>
                      <strong class="text-success"> OPT OUT - Early Release of 13th Month Pay. </strong><br/>
                      <strong><br/> -------------------------- <br/></strong>

                    </p>

                   
                    
                </div>

                
 <div style="background: url('storage/uploads/memobg.png')top left repeat-y; background-size: 50%;background-color: #fff;padding:20px" class="item  text-center" >
                  <h4 class="text-orange" style="line-height: 1.6em" > Message From: <br/> <span class="text-primary"><i class="fa fa-car"></i>   OpenAccessBPO Transport </i><br/> <br/>
                   <img src="storage/uploads/shuttle.png" width="80%" /><br/><br/>
                    <img src="storage/uploads/divider.png" />
                    </h4>
                    <p style="padding: 30px;" class="text-left">
                     TO ALL SHUTTLERS<br/><br/>


                    Due to the high volume of ride requests, we need to streamline our trip planning to utilize our driver pool more efficiently and effectively.<br/><br/>

                    To be able to accommodate more shuttle riders, we would need to schedule shared rides in clusters of 2-3 hours ahead of your actual shift start time (possibly longer for those living in farther vicinities) and up to 1 hour from your shift end time. <br/><br/>

                    Effective immediately, please expect adjustments in your pickup time and allot a longer time allowance. Please take note of your new ride schedules from the email notification that will be sent, and prepare accordingly. <br/><br/>

                    Let’s all help each other get to and from work on time and safely.<br/><br/>

                    Thank you for your understanding.
                    

                    </p>  
                    
                </div>

<div style="background: url('storage/uploads/memobg.png')top left repeat-y; background-size: 50%;background-color: #fff;padding:20px" class="item text-center" >
                  <h4 class="text-orange" >Memo:<span class="text-primary"> <i class="fa fa-info-circle-o"></i><br/>Early Cutoff and Payroll<br/>
                 <br/>
                    <i class="fa fa-calendar fa-5x"></i> <br/><br/>
                    <img src="storage/uploads/divider.png" /><br/>
                    
                    <!-- <h3 class="text-danger"><i class="fa fa-medkit"></i> Health Alert <i class="fa fa-medkit"></i> </h3> -->
                    <p style="padding: 50px;font-size: smaller;line-height: 1.2em" class="text-left">

                      In view of the upcoming Holy Week , we will move the  payroll cut-off to <br/><strong class="text-danger text-center" style="font-size: larger">Mar 21-Apr 3, 2020.</strong> <br/><br/>Attendance from April 4 to 5, 2020 will be paid in full.  Any absence, tardiness, or overtime rendered during that two-day period will be accounted on April 25, 2020 payroll. 
                       <br/><br/>
                      Our new payout would be <strong>Apr 8th instead of Apr 10th.</strong> <br/><br/>
                      Attendance should be approved and locked in EMS on or before April 4, 2020 12:00pm. <br/><br/>

                      Please be guided accordingly.</p>


                    
                </div>

 <div style="background: url('storage/uploads/memobg.png')top left repeat-y; background-size: 50%;background-color: #fff;padding:20px" class="item  text-center" >
                  <h4 class="text-orange" style="line-height: 1.6em" > Message From Finance: <br/> <span class="text-primary">    Extended deadline 4PM today <span style="font-size: small;">(Apr.06)</span> <br/>to  lock EMS DTR and update <br/>employee status tracker </i><br/> <br/>
                   <i class="fa fa-3x fa-lock"></i> <img src="storage/uploads/payday.png" width="35%"><br/><br/>
                    <img src="storage/uploads/divider.png" />
                    </h4>
                    <p style="padding: 30px;" class="text-left">
                     
                      To those who haven't already, please ensure your entire DTR's are locked on EMS, on or before 4PM today<span style="font-size: small;"> (Apr.06)</span>.<br/><br/>

                       

                      <strong class="text-primary"> TL's and Managers:</strong><br/>

                      Please make sure all relevant updates are entered on the Employee Status Tracker on or before the deadline too.<br/><br/>

                       

                      <strong class="text-primary"> After 4:00 pm today:</strong>

                      Locked/updated or otherwise, the Finance Team will assume the current data reflecting as final for salary computation and crediting.
                    

                    </p>  
                    
              </div>
<div style="background: url('storage/uploads/memobg.png')top left repeat-y; background-size: 50%;background-color: #fff;padding:20px" class="item text-center" >
                  <h4 class="text-orange" > Message from <span class="text-primary"> <i class="fa fa-info-circle-o"></i><br/> OpenAccessBPO Transport<br/>
                 <br/>
                    <img src="storage/uploads/divider.png" /><br/>
                    
                    <!-- <h3 class="text-danger"><i class="fa fa-medkit"></i> Health Alert <i class="fa fa-medkit"></i> </h3> -->
                    <p style="padding: 50px;font-size: smaller;line-height: 1.2em" class="text-left">Dear Open Access Family,<br/><br/>

                      Thank you for your patience while we sorted out our skeletal workforce's daily round-the-clock shuttle rides. 

                      We have created a process for dispatching and coordinating rides more smoothly which provides passenger and driver support more efficiently. We launched <strong>OA Transport Support</strong>, the hub for ride status updates.

                      For shuttle inquiries, email <a href="mailto:oatransport@openaccessbpo.com">oatransport@openaccessbpo.com</a> and send feedback to
                      Shuttle Feedback.

                    </p>
                    <a class="btn btn-primary btn-sm">Shuttle Feedback</a>
                    <img src="storage/uploads/carpool.png" width="100%" />

                    
                </div>
 <div style="background: url('storage/uploads/memobg.png')top left repeat-y; background-size: 50%;background-color: #fff;padding:20px" class="item text-center" >
                  <h4 class="text-orange" >Payout Schedule: <span class="text-primary"> <i class="fa fa-info-circle-o"></i><br/>March 25<br/>
                 <br/>
                    <img src="storage/uploads/divider.png" /><br/>
                    <img src="storage/uploads/bdoatm.jpg" width="60%" />
                    
                    <!-- <h3 class="text-danger"><i class="fa fa-medkit"></i> Health Alert <i class="fa fa-medkit"></i> </h3> -->
                    <p style="padding: 50px;font-size: smaller;" class="text-left"><br/>
                      - Eligibility: <strong class="text-danger">all employees hired before March 16 </strong><br/><br/>
                      - Unless otherwise expressed by the employee, available leave credits will be used for days not worked (SL then VL)<br/><br/>
                      - <strong>For employees with no or not enough leave credits,</strong> the company will advance up to 5 SL credits to be offset as they earn it (use of SL credits during the ECQ period will not be grounds for disqualification from SL conversion<br/><br/>
                      - All employees that reported for work on March 16 <strong class="text-danger">will be paid in full <em style="font-size: smaller;"> (including those that were sent home after the announcement and did not finish their shift)</em></strong><br/><br/>
                      - All agents and TLs that continued to report for work from March 17-20 will get <strong class="text-danger">+50% base pay on hours worked</strong>
                    </p>

                   
                    
                </div>

<div style="background: url('storage/uploads/memobg.png')top left repeat-y; background-size: 50%;background-color: #fff;padding:20px" class="item text-center" >
                  <h4 class="text-orange" >  DOLE <em>(Department of Labor &amp; Employment)</em> <span class="text-primary"> <i class="fa fa-info-circle-o"></i><br/> Financial Support<br/><br/>
                 <br/>
                    <img src="storage/uploads/divider.png" /><br/>
                    <img src="storage/uploads/bills.jpg" width="90%" /></h4>
                    <!-- <h3 class="text-danger"><i class="fa fa-medkit"></i> Health Alert <i class="fa fa-medkit"></i> </h3> -->
                    <p style="padding: 50px" class="text-left">Dear All,<br/><br/>

                        Open Access BPO empathizes with the plight of each employee in the midst of these trying times. As you all know, the Department of Labor and Employment (DOLE) has released a series of advisories regarding the financial support in the amount of <strong style="font-size: large;"> Php5,000.00</strong> to affected workers of COVID-19. Please be informed that the company is closely coordinating with the Department of Labor on this matter. Rest assured that we will be issuing updates early next week.<br/>Thank you.<br/><br/>Sincerely,<br/>Management
                    </p>
                    
                </div>
<div style="background: url('storage/uploads/memobg.png')top left repeat-y; background-size: 50%;background-color: #fff;padding:20px"  class="item text-center" >
                  <h4 class="text-orange" > <span class="text-primary">Temporary Suspension of Office Operations  </span><br/> (Makati Site Only)<br/><img src="storage/uploads/divider.png" /></h4>
                    <!-- <h3 class="text-danger"><i class="fa fa-medkit"></i> Health Alert <i class="fa fa-medkit"></i> </h3> -->
                    <p style="padding: 50px" class="text-left">Dear All,<br/><br/>

                      In view of the imposition of an "Enhanced Community Quarantine" over Luzon which mandates strict home quarantine at 12:00am of March 17, 2020, please be advised we will temporarily suspend business operations at our Makati sites (G2 and 6780) starting today, March 17, 2020 (Tuesday) up to March 22, 2020 (Sunday).<br/><br/>

                      For employees who are allowed to work from home ("WFH"), you are advised to continue working as scheduled.  To facilitate this WHF, we have assembled a deployment team who will help us enable work from home arrangements for programs whose clients have permitted us to do so. In this regard, kindly coordinate with your respective Immediate Heads and Managers.<br/><br/>

                      For the rest, please stay at home. Rest assured everyone will be fully paid for this week as Finance will automatically apply all accrued sick and vacation leaves. For those not entitled to any leaves yet or those who have no more leaves available, Finance will advance your leave credits which will be offset as you earn them.  <br/><br/>

                      To emphasize, the suspension of operations in merely temporary as we are aiming to work through the requirements in resuming operations onsite by next week to, at the very least, set up a skeletal workforce, for those that can and want to report back to work.  <br/><br/>

                      We encourage everyone to comply with the Government's directives in the hope that this community-quarantine is lifted sooner for us to be able to return to normal operations.
                    </p> <br /><br/>
                </div>
<div class="item text-center" >
                  <h4 class="text-orange" >Mobile Legends<span class="text-primary"> Tournament </span></h4>
                    <!-- <h3 class="text-danger"><i class="fa fa-medkit"></i> Health Alert <i class="fa fa-medkit"></i> </h3> -->
                    
                    <img src="./storage/uploads/mltournament.jpg" style="z-index: 2" /><br/><br/>
                    <h4 class="text-primary">MECHANICS</h4>
                    <ul class="text-left" style="padding-left: 50px;">
                      <li>Open Access BPO Makati &amp; Davao employees are invited to join in the tournament</li>
                      <li>Teams should consist of <strong>5 members and 1 optional reserve player</strong></li>
                      <li>All ML ranks are qualified to join (Elite to Mythic).</li>
                      <li>Each game will be played on custom 5V5 battle mode.</li>
                      <li>Teams are required to bring their own phone chargers and earphones during all matches.</li>
                      <li>Trash talking, bashing, and or any forms of violent reactions or profuse language towards other players is not allowed.</li>
                      <li>Any forms of cheating (use of VPN and cheats) is strictly prohibited.</li>
                      <li>Best of 3 on eliminations and best of 5 on the finals round.</li>
                      <li>In case of any dispute, the decision of the game facilitator shall be deemed final and irrevocable.</li>
                    </ul><br/><br/>
                    <h4 class="text-danger">PRIZES</h4>
                    <H5>1st Place: Php 15,000  |  2nd Place: Php 10,000. | 3rd Place: Php 5,000</H5>
                    <p>Non-winning participants - Reward Points</p>

                    <h4>Registration Ends: March 15, 2020</h4>

                    <a class="btn btn-md btn-danger" href="https://docs.google.com/forms/d/e/1FAIpQLSdaHWBP0pmIYfhCCetW9uW_QzSDqyPd3C_M7EE9m51HzOGU5g/viewform" target="_blank"><i class="fa fa-info-circle"></i> Register Now </a><br /><br/>
                </div>

          <div class="item text-center" >
                    <h3 class="text-primary">Congratulations to all the Winners!</h3>
                    <img src="./storage/uploads/zumba_winners.jpg" style="z-index: 2" />

                    <p class="text-left" style="padding-left: 50px;"><br/><br/>Open Access BPO's Let's Get Physical Program raffle draw winners were announced at our Glorietta 2 Makati site. Participants of the fitness program each earned a raffle entry for every class attended including Yoga, Yogalates, Zumba, and Aero Kickboxing in 2019. <br/><br/>
                    Congratulations to all the winners!</p>

                    <table class="table" cellpadding="20" style="font-size: smaller;">
                      <tr>
                        <td style="font-weight: bolder;">Trip to Boracay</td>
                        <td>Jayne Lacson, Recruitment</td>
                      </tr>
                      <tr>
                        <td style="font-weight: bolder;">JBL Flip bluetooth speaker</td>
                        <td>Jessica Francia, Recruitment</td>
                      </tr>
                      <tr>
                        <td style="font-weight: bolder;">urBeats3 earphones</td>
                        <td>Mike Pamero, Marketing</td>
                      </tr>
                      <tr>
                        <td style="font-weight: bolder;">Php 2,000 SM Gift Certificate</td>
                        <td>Maritess Ferrer, Postmates <br/>
                          Mikaela Bordon, Recruitment<br/>
                        Myla Alcantara, QA</td>
                      </tr>
                      <tr>
                        <td style="font-weight: bolder;">Php 1,500 SM Gift Certificate</td>
                        <td>Khristine Bachini, Lebua</td>
                      </tr>
                      <tr>
                        <td style="font-weight: bolder;">Php 1,000 SM Gift Certificate</td>
                        <td>Eunice Cantos, Data Management</td>
                      </tr>
                      <tr>
                        <td style="font-weight: bolder;">Promate Powerbank</td>
                        <td>Wendy Pilar, Marketing</td>
                      </tr>
                    </table>


                               <br/><br/>   
                  </div>


            <div class="item text-center" style="background-color: #000" >
                    
                      <img src="./storage/uploads/idol-winners.jpg" width="100%" style="z-index: 2" /><br/><br/>
                      <a href="{{ action('HomeController@gallery',['a'=>21]) }}" class="btn btn-lg btn-default"><i class="fa fa-image"></i> See all party pics</a><br/><br/>
                  </div>

                 

                 

                  
                   
                    @foreach($top3 as $idol)

                    
                    <div class="item text-center" >

                   


                    
                      <h2 style="color:#666">Congratulations <br/><span  class="text-primary" style="font-size: 0.8em">{!! $titles[$ct1] !!}</span></h2>
                      <img src="./storage/uploads/{{$pics[$ct1]}}" width="100%" style="z-index: 2" />
                      <h4 class="text-warning" style="text-transform: uppercase;"> {{$idol->firstname}} {{$idol->lastname}} <br/>
                                                            <span style="font-size: x-small;">{{$idol->jobTitle}} </span><br/>
                                                            @if (empty($idol->filename))
                                                               <span style="font-size: 0.5em; font-weight: bolder">{{$idol->program}}</span> 
                                                            @else
                                                             <img style="background-color: #fff;" src="{{ asset('public/img/'.$idol->filename) }}" height="30" />

                                                            @endif
                      </h4>
                      <h5 class="text-center"><em><i class="fa fa-music"></i> "{{$songs[$ct1]}}" <i class="fa fa-music"></i> </em></h5><a href="{{ action('HomeController@gallery',['a'=>21]) }}" class="btn btn-lg btn-danger"><i class="fa fa-image"></i> See all party pics</a><br/><br/>

                    </div>


                     
                  
                   
                  <?php $ct1++; ?>
                  @endforeach
                  
  

                   


                    <div class="item text-center" >
                      
                              <h4 class="text-orange" >Daily <span class="text-primary"> Motivation </span></h4>
                              <img src="./storage/uploads/confucius.jpg" style="z-index: 2" />
                               <p class="text-left" style="padding-left: 50px;"><br/><br/>Build your success at your own phase and never quit until you are done. <strong>#WeSpeakYourLanguage #MondayMotivation </strong></p>


                               <div style="padding:10px; position: absolute;bottom: 0px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                    <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/>#WeSpeakYourLanguage #MondayMotivation</small></a></div> <br/><br/><br/><br/>          

                    </div>

<div class="item text-center" >
                    <!-- <h3 class="text-danger"><i class="fa fa-medkit"></i> Health Alert <i class="fa fa-medkit"></i> </h3> -->
                    <img src="./storage/uploads/thankyou_card.jpg" style="z-index: 2" />
                    <p class="text-left" style="padding-left: 50px;"><br/><br/>Congratulations to us! We raised a total of P45,000 in cash donations. And because of the company's price match, we were able to donate a grand total of P90,000 worth of goods and more to the Lipa Archdiocesan Social Action Commission's Malasakit Para sa Batangas initiative.<br/><br/>Thank you for your generosity in helping our brothers and sisters who were impacted by the Taal volcano eruption.</p>

                    

                    

                </div>

                

                <div class="item  text-center" >
                    <!-- <h3 class="text-danger"><i class="fa fa-medkit"></i> Health Alert <i class="fa fa-medkit"></i> </h3> -->
                    <img src="./storage/uploads/rewards_poster.jpg" style="z-index: 2" />

                    <a class="btn btn-lg btn-success" href="{{action('UserController@rewards_about')}}" style="margin-top: 20px">Learn More!</a>

                    

                </div>

                  

                  <div class="item text-center" >
                    <h3 class="text-danger"><i class="fa fa-medkit"></i> Health Alert <i class="fa fa-medkit"></i> </h3>
                    <img src="./storage/uploads/corona.jpg" style="z-index: 2" />

                    <p class="text-left" style="padding-left: 50px;"><br/><br/>Stay safe everyone and keep this simple but useful tips for the prevention of coronavirus. <br/><br/>Also, if anyone from you have cough and colds with fever,we encourage you to drop by the clinic for assessment.</p>

                    

                  </div>


  <div class="item text-center">

                              <h3 class="text-danger"><strong><i class="fa fa-money"></i> <span style="font-size:smaller">Referral Bonus: </span><i class="fa fa-money"></i> <br/><span style="font-size: larger;"> Php 50,000.00</span> </strong> <br/>
                                <span style="font-size: x-small; line-height: 0.5em">No restrictions - TLs and Managers can also send their referrals!<br/></span>
                                <br/><span class="text-orange" style="font-size:0.8em">For those referrals who can start this January</span></br/><br/>
                                <span class="text-primary">JAPANESE CONTENT SUPPORT AGENT (up to 120K/mo) <br/> <br/>
                                <img src="public/img/logo_eden.png" width="40%" /><br/>
                              </h3><BR/><BR/>

                              

                              <ul class="text-left" style="margin-left: 30px">As a Customer Support agent, applicant will be tasked to:
                                <li>Respond and resolve customer service and technical support concerns via LIVE CHAT and EMAIL ina a timely, accurate and professional manner</li>
                                <li>Connect and build rapport with representatives and member by actively listening, asking the right questions and offering solutions, while demonstrating a deep understanding of their concern.</li>
                                <li>Offer quality customer service on every call to surpass expectations</li>
                                <li>Efficiently escalate complex problems to appropriate internal resources</li>
                                <li>Document customer interactions to help maintain the client's internal and external knowledge bases</li>
                                <li>Collaborate with co-workers to provide insightul feedback that will improve processes and products</li>
                                <li>Perform other tasks as required by the campaign</li>
                              </ul>

                              <h4>Qualifications</h4>
                              <ul class="text-left" style="margin-left: 30px">
                                <li>Must be proficient in both written and spoken English and Japanese;</li>
                                <li>With at least college-level education</li>
                                <li>Preferably with at least one year of experience working in a BPO company (preferably in a customer service program)</li>
                                <li>Diligent and keen on exceeding expectations and continuously seeking improvement opportunities</li>
                                <li>Able to multitask and embrace change in a fast-paced, performance-driven team</li>
                                <li>Punctual and committed to work schedules</li>
                                <li>With an upbeat and inquisitive personality;</li>
                                <li>Equipped with basic technical skills - able to navigate through computer and web applications with ease; and</li>
                                <li>Willing to work immediately in Makati</li>
                              </ul>

                              <h5>Please have your referrals come in from MON-FRI 8AM-3PM at G2 office</h5>


                  </div>

                  <div class="item text-center">

                              <h3 class="text-danger"><strong><i class="fa fa-money"></i> <span style="font-size:smaller">Referral Bonus: </span><i class="fa fa-money"></i> <br/>Php 15,000.00 </strong> <br/>
                                <span style="font-size: x-small; line-height: 0.5em">Php 7,500 upon passing the training<br/>
                                Php 7,500 upon Regularization</span>
                                <br/><span class="text-orange" style="font-size:0.8em">For those referrals who can start ASAP</span></br/><br/>
                                <span class="text-primary">CHAT SUPPORT (22K) <br/> <br/>
                                <img src="public/img/logo_circleslife.png" width="40%" /><br/>
                              </h3><BR/><BR/>

                              

                              <ul class="text-left" style="margin-left: 30px">
                                <li>telco campaign</li>
                                <li>1 day process</li>
                                <li>at least college level</li>
                                <li>at least 6 months BPO service experience</li>
                              </ul>

                              <h4>Qualifications</h4>
                              <ul class="text-left" style="margin-left: 30px">
                                <li>Excellent written and spoken English communication skills</li>
                                <li>Minimum of 1 to 2 years' experience</li>
                                <li>Able to multitask and embrace change in a fast-paced, performance driven team</li>
                                <li>mastery of CRM and ticketing tools (Zendesk, Service Cloud, Zopim, etc.)</li>
                                <li>Basic technical skills (able to navigate through smartphone and computer applications)</li>
                                <li>Flexible and willing to work in a fast-paced and quickly growing environment</li>
                                <li>Positive and friendly with an upbeat personality</li>
                              </ul>


                  </div>

                  <div class="item text-center">

                              <h3 class="text-danger"><strong><i class="fa fa-money"></i> <span style="font-size:smaller">Referral Bonus: </span><i class="fa fa-money"></i> <br/>Php 15,000.00 </strong> <br/>
                                <br/><span class="text-orange" style="font-size:0.8em">For those referrals who can start ASAP</span></br/><br/>
                                <span class="text-primary">CUSTOMER SERVICE REPRESENTATIVE (25K) <br/> <br/>
                                <img src="public/img/logo_lucky.png" width="40%" /><br/>
                              </h3><BR/><BR/>

                              

                              <ul class="text-left" style="margin-left: 30px">
                                <li>health account</li>
                                <li>pioneer batch, mass hiring</li>
                                <li>customer service. No sales. No collections</li>
                                <li>at least 1 year BPO service experience</li>
                              </ul>

                              <h4>Qualifications</h4>
                              <ul class="text-left" style="margin-left: 30px">
                                <li>Excellent written and spoken English communication skills</li>
                                <li>Minimum of 1 to 2 years' experience</li>
                                <li>Able to multitask and embrace change in a fast-paced, performance driven team</li>
                                <li>mastery of CRM and ticketing tools (Zendesk, Service Cloud, Zopim, etc.)</li>
                                <li>Basic technical skills (able to navigate through smartphone and computer applications)</li>
                                <li>Flexible and willing to work in a fast-paced and quickly growing environment</li>
                                <li>Positive and friendly with an upbeat personality</li>
                              </ul>


                  </div>

                  <div class="item text-center">

                              <h3 class="text-danger"><strong><i class="fa fa-money"></i> <span style="font-size:smaller">Referral Bonus: </span><i class="fa fa-money"></i> <br/>Php 15,000.00 </strong> <br/>
                                <br/><span class="text-orange" style="font-size:0.8em">For those referrals who can start ASAP</span></br/><br/>
                                <span class="text-primary">CUSTOMER SERVICE REPRESENTATIVE (25K) <br/> <br/>
                                <img src="public/img/logo_eden.png" width="40%" /><br/>
                              </h3><BR/><BR/>

                              

                              <ul class="text-left" style="margin-left: 30px">
                                <li>VOICE AND NON VOICE</li>
                                <li>office supplies and services company</li>
                                <li>strong customer service background</li>
                                <li>exceptional English communication skills</li>
                                <li>at least college level</li>
                                <li>at least 1 year BPO service experience</li>
                              </ul>

                              <h4>Qualifications</h4>
                              <ul class="text-left" style="margin-left: 30px">
                                <li>Excellent written and spoken English communication skills</li>
                                <li>Minimum of 1 to 2 years' experience</li>
                                <li>Able to multitask and embrace change in a fast-paced, performance driven team</li>
                                <li>mastery of CRM and ticketing tools (Zendesk, Service Cloud, Zopim, etc.)</li>
                                <li>Basic technical skills (able to navigate through smartphone and computer applications)</li>
                                <li>Flexible and willing to work in a fast-paced and quickly growing environment</li>
                                <li>Positive and friendly with an upbeat personality</li>
                              </ul>


                  </div>


                  <div class="item text-center">

                              <h3 class="text-danger"><strong><i class="fa fa-money"></i> <span style="font-size:smaller">Referral Bonus: </span><i class="fa fa-money"></i> <br/>Php 5,000.00 </strong> <br/>
                                <br/><span class="text-orange" style="font-size:0.8em">For those referrals who can start ASAP</span></br/><br/>
                                <span class="text-primary">CUSTOMER SERVICE REPRESENTATIVE (25K) <br/> <br/>
                                <img src="public/img/logo_mymusic.png" width="40%" /><br/>
                              </h3><BR/><BR/>

                              

                              <ul class="text-left" style="margin-left: 30px">
                                <li>VOICE AND NON VOICE</li>
                                <li>generate events awareness on social media</li>
                                <li>K-pop lovers / social media butterflies are welcome to apply</li>
                                <li>exceptional English communication skills</li>
                                <li>at least college level</li>
                                <li>at least 6 months BPO service experience</li>
                              </ul>

                              <h4>Qualifications</h4>
                              <ul class="text-left" style="margin-left: 30px">
                                <li>Excellent written and spoken English communication skills</li>
                                <li>Minimum of 1 to 2 years' experience</li>
                                <li>Able to multitask and embrace change in a fast-paced, performance driven team</li>
                                <li>mastery of CRM and ticketing tools (Zendesk, Service Cloud, Zopim, etc.)</li>
                                <li>Basic technical skills (able to navigate through smartphone and computer applications)</li>
                                <li>Flexible and willing to work in a fast-paced and quickly growing environment</li>
                                <li>Positive and friendly with an upbeat personality</li>
                              </ul>


                  </div>

  <div class="item text-center" >
                    <!-- <h3 class="text-danger"><i class="fa fa-medkit"></i> Health Alert <i class="fa fa-medkit"></i> </h3> -->
                    <img src="./storage/uploads/valentine2020.jpg" style="z-index: 2" />
                    <br/><br/>
                    <a class="btn btn-md btn-danger" href="{{action('EngagementController@show',2)}}"><i class="fa fa-heart"></i> Post your Valentine messages </a>
                    <a class="btn btn-md btn-primary" href="{{action('EngagementController@wall',2)}}"><i class="fa fa-th-large"></i> Check our Valentine Wall! </a><br /><br/>

                    

                    

                </div>
<!-- BLOOD DONATION-->

                <div class="item  text-center" >
                    
                     <p style="padding: 5px 30px; margin-bottom: 0px"><h3 class="text-orange"><span style="font-size: smaller;" class="text-primary">What: </span>OAMPI Clinic 10<sup>th</sup> Blood Donation Drive</h3><br/><br/>
                      <img src="./storage/uploads/blooddonation.jpg" style="z-index: 2" width="100%" /><br/><br/>
                      <p style="padding: 5px 30px; margin-bottom: 0px">
                      <h4>
                      <strong>When:</strong> <span class="text-danger">Feb. 13, 2020 Thu </span><br/>
                      Time: <span class="text-danger">9AM to 5PM</span><br/>
                      <strong>Where:</strong> 5F Jaka Bldg. </h4><br/><br/></p>

                      <p style="padding: 5px 30px; margin-bottom: 0px;" class="text-left">
                      <span style="font-size: x-large;"> Basic Requirements:</span><br/><br/>
                      Blood donation helps save lives. Find out if you're eligible to donate blood and what to expect if you decide to donate.<br/><br/>
                      You can donate blood if you…<br/>
                      - Are in good health<br/>
                      - Are between 16 to 65 years old<br/>
                      - Weigh at least 110 pounds (approximately 50kg)<br/>
                      - Have a blood pressure between Systolic: 90-140mmHg,Diastolic: 60-100mmHg; and<br/>
                      - Pass the physical and health history assessments.</p>


                      <p style="padding: 5px 30px; margin-bottom: 0px;" class="text-left"><br/><br/>
                      <strong>Screening:</strong> <br/><br/><br/>

 

                      PRC Blood Services Facilities carefully screen potential donors. The screening guidelines are necessary to ensure that blood donation is safe for you and that it is safe for the person who will be receiving your blood.<br/><br/>



                      In the screening process, you have to fill out a blood donation questionnaire form that includes direct questions about behaviors known to carry a higher risk of blood-borne infections—infections that are transmitted through the blood. A trained physician will be asking you about your medical/ health history, and a physical examination will be conducted—which includes checking your blood pressure, pulse and temperature. All of the information from this evaluation is kept strictly confidential.<br/><br/>



                      During your blood donation screening procedure, a small sample of blood taken from a finger prick is used to check your hemoglobin level, the oxygen-carrying component of your blood. If your hemoglobin concentration is normal, and you've met all the other screening requirements, you can donate blood.<br/><br/>



                      Q: Can a person who has tattoo,piercing, currently taking medication and with travel history still donate blood?<br/>

                      A: Medication taken, travel history, tattoo and piercing to be assessed if qualified to donate.<br/>

                      <h5 class="text-primary"> Every volunteer donor will be given a BLOOD DONOR CARD during the event. This card may be used as a record of donation. However, this card does not exempt the holder from paying the processing fee. This is intended to cover the cost of the reagents an operating expenses used to collect and screen all donated blood for infectious disease</h5><br/><br/><br/><br/>
                </div>
 <div class="item  text-center" >
                    <h2 class="text-primary">Congratulations <br/><span  class="text-primary" style="font-size: 0.8em">to our <span style="color:#666">Monochrome Party </span><br/><strong>Star of The Night </strong> winners!</span></h2>
                      <img src="./storage/uploads/monochrome-516.jpg" width="100%" style="z-index: 2" />
                      <h4 class="text-warning" style="text-transform: uppercase;"> Janelle De Guzman &amp; Adrian Uro <br/>
                                                            
                      </h4>
                      <h5 class="text-center"></h5><a href="{{ action('HomeController@gallery',['a'=>21]) }}" class="btn btn-lg btn-danger"><i class="fa fa-image"></i> See all party pics</a><br/><br/>
                  </div>           


                  <div class="item text-center">
                    
                      <h4 class="text-center">Monochrome <strong>Photo Booth</strong> pics!</h4>

                      <img src="./storage/uploads/pb1.jpg" width="100%" style="z-index: 2" />
                      
                      <br/><br/>
                      <a href="{{ action('HomeController@gallery',['a'=>22]) }}" class="btn btn-md btn-primary">
                        <i class="fa fa-image"></i> Photobooth 1</a><br/><br/>
                      <a href="{{ action('HomeController@gallery',['a'=>23]) }}" class="btn btn-md btn-primary">
                        <i class="fa fa-image"></i> Photobooth 2</a><br/><br/>
                  </div>
<div class="item text-center" >
                    <h3 class="text-primary"> OAMPI CLINIC Wellness Program  </h3>
                    <img src="./storage/uploads/wellness2019.jpg" style="z-index: 2" />

                    <p class="text-left" style="padding-left: 50px;"><br/><br/>We invite everyone to participate in the following activities of OAM Clinic's Wellness Event on <strong class="text-orange">February 11 and 12, 2020</strong> during your break time:</p>


                    <h2 class="text-primary" style="font-size: small;">
                    WHAT:    ﻿ OAM Clinic Wellness Program<br/>
                    WHERE:  5TH FLR JAKA BLDG<br/>
                    WHEN:    TUESDAY FEB. 11, 2020, 10AM- 7PM<br/><br/>

                    WHAT:     OAM Clinic Wellness Program<br/>
                    WHERE:  11TH FLR ADP BLDG<br/>
                    WHEN:    WEDNESDAY FEB. 12, 2020, 10AM-7PM</h2>


                      <h3>Activities:</h3><p class="text-left" style="padding-left: 50px;"><br/><br/>


                      <strong>LENNY'S MASSAGE (Feb.11-12)</strong><br/>

                                - Free Massage Therapy<br/>

                                - Product Sampling of White Flower Oil<br/>

                                - Product Sampling of Hot/Warm Compress Bag<br/>

                                - Ear Candling and Soft Selling<br/><br/>



                      <strong>BUSINESS INNOVATIVE GATEWAY INC. (Feb.11-12)</strong><br/>

                                 -Product sampling (Health and wellness)<br/><br/>



                      <strong>SLIMMERS WORLD (Feb.11-12)</strong><br/>

                                -Body composition analysis and consultation<br/>

                                -Membership Promos<br/><br/>



                      <strong>FARMER AND BROWN HEALTHCARE DISTRIBUTORS INC.﻿ (Feb.11-12)</strong><br/>

                                -Product sampling ( Morning power anti-hangover/anti-fatigue drinks)<br/><br/>



                      <strong>WISE SPENDER (Feb.11-12)</strong><br/>

                               -Product selling<br/><br/>



                      <strong>BRIGHTMOVE CORP. (Feb.11 ONLY)</strong><br/>

                             -Exercise Demo<br/>

                             -Free Non cleansing and foot detox<br/>

                             -Free body massage<br/>

                             -Free foot reflex<br/><br/>

                       

                      <strong>MANILA NATURES LINK CORP. (Feb.12 ONLY)</strong><br/>

                             -Product sampling (Malungai Life Oil)<br/><br/>



                      <strong>YAKULT PHIL. INC. (Feb.12 ONLY)</strong><br/>

                              -Product sampling and film showing<br/>

                      </p>

                    

                  </div>
 @for($r=1;$r<=12;$r++)

                  <div class="item text-center" >
                    <h3 class="text-danger">Year of the Metal Rat Predictions</h3>
                    <img src="./storage/uploads/cny2020_{{$r}}.jpg" style="z-index: 2" />

                    <p class="text-left" style="padding-left: 50px;"><br/><br/></p>

                    

                  </div>

                  @endfor

  <div class="item text-center" >
                    <h3 class="text-primary">SSS | Pag-Ibig Calamity Loan</h3>
                    <img src="./storage/uploads/taal.jpeg" style="z-index: 2" />

                    <p class="text-left" style="padding-left: 50px;"><br/><br/>To those who were affected by the Taal Volcanic eruption specifically those under the state of calamity (Province of Batangas), you may now avail the SSS Calamity Loan starting January 22, 2019.</p>

                    <a class="btn btn-success btn-sm" href="storage/uploads/SSS_loan.pdf">See SSS Loan Details</a>
                    <a class="btn btn-primary btn-sm" href="storage/uploads/pagibig_calamity_loan.PDF"><i class="fa fa-download"></i> Download Pagibig Form</a>

                   

                  </div>



<div class="item text-center" >
                    <h3 class="text-primary">Attn: all JAKA bldg Employees</h3>
                    <img src="./storage/uploads/jaka_bldg.jpg" style="z-index: 2" />

                    <p class="text-left" style="padding-left: 50px;background-color: #000; color:#fff">
                      From               :           Building Administration Office <br/>
                      Subject            :           Temporary Elevator Scheme<br/>

                      <br/><br/>Please be guided on the following elevator scheme below. Effective 22nd of January 2020, we will temporarily implement an Odd-Even Elevator Service. This scheme is on trial basis only and we hope it will help speed up the conveyance of passengers and reduce waiting time.<br/><br/>

                      PASSENGER ELEVATOR No. 4 WILL ONLY SERVE FLOORS 2, 4, 6, 8, 10 and 12 during these hours: <br/><br/>

                        6:00am – 8:00am<br/>

                      10:00am – 1:00pm<br/>

                        3:00pm – 5:00pm<br/><br/><br/>


                      PASSENGER ELEVATOR No. 1 WILL ONLY SERVE FLOORS 3, 5, 7, 9, 11, PH1 and PH2 during these hours:<br/><br/>

                      6:00am – 8:00am<br/>

                    10:00am – 1:00pm<br/>

                      3:00pm – 5:00pm<br/><br/><br/>
                    Thank you for your understanding and cooperation. <br/><br/></p>

                  

                  </div>


                


 <div class="item text-center">

                              <h3 class="text-danger"><strong><i class="fa fa-money"></i> <span style="font-size:smaller">Referral Bonus: </span><i class="fa fa-money"></i> <br/><span style="font-size: larger;"> Php 50,000.00</span> </strong> <br/>
                                <span style="font-size: x-small; line-height: 0.5em">No restrictions - TLs and Managers can also send their referrals!<br/></span>
                                <br/><span class="text-orange" style="font-size:0.8em">For those referrals who can start this January</span></br/><br/>
                                <span class="text-primary">JAPANESE CONTENT MODERATION ANALYST (up to 120K/mo) <br/> <br/>
                                <img src="public/img/logo_quora.png" width="40%" /><br/>
                              </h3><BR/><BR/>

                              

                              <ul class="text-left" style="margin-left: 30px">
                                <li>As a Content Moderator Analyst, applicant will be working for an online Q&amp;A platform, where questions are asked, answered,edited, and organized by its community of users</li>
                                <li>Applicant will be tasked to moderate content based on given guidelines using both JAPANESE and ENGLISH</li>
                              </ul>

                              <h4>Qualifications</h4>
                              <ul class="text-left" style="margin-left: 30px">
                                <li>Must be proficient in both written and spoken English and Japanese;</li>
                                <li>Preferably with at least six (6) months of experience working in a BPO company (preferably in a customer service program)</li>
                                <li>Able to read fast and demonstrate high attention to detail;</li>
                                <li>A critical thinker with good analytical skills;</li>
                                <li>With an upbeat and inquisitive personality;</li>
                                <li>Equipped with basic technical skills - able to navigate through computer and web applications with ease; and</li>
                                <li>Willing to work in Makati</li>
                              </ul>

                              <h5>Please have your referrals come in from MON-FRI 8AM-3PM at G2 office</h5>


                  </div>

// TAAL
<div class="item active text-center" >
                    <h3 class="text-primary">Donations for Taal eruption victims</h3>
                    <img src="./storage/uploads/taal.jpeg" style="z-index: 2" />

                    <p class="text-left" style="padding-left: 50px;"><br/><br/>The recent volcanic eruption of Taal volcano has affected a lot of families in the Tagaytay, Batangas, and Cavite areas. As part of the community, Open Access BPO will be donating to the victims of this unfortunate event. We are accepting in-kind and cash donations.</p>

                    <h4>In-kind donations:</h4>
                    <ul class="text-left" style="margin-left: 30px">
                                <li>Bottled drinking water</li>
                                <li>Easy open canned goods</li>
                                <li>Cup noodles</li>
                                <li>Biscuits</li>
                                <li>3-in-1 coffee</li>
                                <li>Hygiene products <em>(soap, shampoo, toothpaste, toothbrush, sanitary napkins, wet wipes, alcohol)</em></li>
                                <li>Slippers (for children and/or adults)</li>
                                <li>Blankets</li>
                                <li>Mats</li>
                                <li>Towels</li>
                                <li>Diapers (baby and/or adult)</li>
                                <li>Medicine (for headache, cough, colds, diarrhea, antiseptic solution)</li>
                                <li>Cleaning materials and soaps</li>
                                <li>Pet food</li>
                                <li>N95 face masks</li>
                              </ul>
                    <p class="text-left" style="padding-left: 50px;"><br/>You may drop off your donations on the following areas:<br/><br/>
                      <strong>- Jaka 5F left wing</strong><br/>
                      <strong>- G2 reception </strong></p>

                      <br/><br/>   
                    <h4>Cash donations</h4>
                    <ul class="text-left" style="margin-left: 30px">
                                <li>Feel free to donate any amount you wish to share. This will be deducted from your salaries on the Fe.10 2020 payout. For cash donations, kindly <strong><a href="https://docs.google.com/forms/d/e/1FAIpQLScG05Ms7Z_V_5kgY3Mpdq6jiZ0cI_UVK1OU-_aZ4m1yrbX7Ww/viewform?usp=sf_link" target="_blank">fill out this form.</a></strong> </li>
                                <li>We hope to raise Php 50,000 for this cause. All monetary donations received by Open Access BPO will be used to purchase additional in-kind donations.</li>
                    </ul>

                    <p class="text-left" style="padding-left: 50px;">Kindly drop off or confirm your donations by <strong>Monday, Jan. 20,2020.</strong> Volunteers may also be needed to repack our donations soon. More details will be sent out by next week.<br/><br/>
                    Thank you, Open Access BPO family!</p>

                  </div>
 @if(count($fiveYears) >= 1)

                   @foreach($fiveYears as $n)
                    <div class="item text-center">
                     <div style="padding-top:10px;background:url('storage/uploads/bg_2019-awardees.jpg') bottom center no-repeat;background-color: #0b0b0b; background-size: 100%" >
                        <!-- Add the bg color to the header using any of the bg-* classes -->
                        <h4  style="color: #fbf970"  ><br/>Happy 5th Year<span style="color:#fff"> @ Open Access!</span></h4>
                        
                        
                        <div class="widget-user-image">
                           

                          @if ( file_exists('public/img/employees/'.$n->id.'.jpg') )
                          <img class="img-circle" src="{{ asset('public/img/employees/'.$n->id.'.jpg')}}" width="190" alt="User Avatar">
                          @else
                          <img class="img-circle" src="{{asset('public/img/useravatar.png')}}" width="190" alt="User Avatar">
                          @endif

                        </div>
                        
                        <div style="margin-bottom: 180px">
                            @if (empty($n->nickname) || $n->nickname==" ")
                               <h3 class="widget-user-username" style="line-height: 0.2em"><a style="color: #fff" href="{{action('UserController@show',$n->id)}}"><small  style="color: #fff"  >{{$n->firstname}} {{$n->lastname}} </small></a><br/></h3>
                           @else
                               <h3 class="widget-user-username text-white" style="line-height: 0.2em"><a href="{{action('UserController@show',$n->id)}}"><small style="color: #fff"  >{{$n->nickname}} {{$n->lastname}} </small></a><br/></h3>
                           @endif
                           <h5 style="margin-top: -7px"><small style="color:#666; font-weight: bolder"> {{$n->name}} </small><br/>

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



 <div class="item active text-center" style="background-color: #fff">
                              <img src="storage/uploads/bg_2019-awardees.jpg" />
                              <h1>Loyalty <span style="color: #666"> Awardees</span><br/><br/></h1>
                  </div>

                 @if(count($tenYears) >= 1)

                 @foreach($tenYears as $n)
                  <div class="item text-center">
                    <div style="padding-top:10px;background:url('storage/uploads/bg_2019-awardees.jpg') bottom center no-repeat;background-color: #0b0b0b; background-size: 100%" >
                      <!-- Add the bg color to the header using any of the bg-* classes -->
                      <h4  style="color: #fbf970"  ><br/>Happy 10th Year<span style="color:#fff"> @ Open Access!</span></h4>
                      
                      
                      <div class="widget-user-image">
                         

                        @if ( file_exists('public/img/employees/'.$n->id.'.jpg') )
                        <img class="img-circle" src="{{ asset('public/img/employees/'.$n->id.'.jpg')}}" width="190" alt="User Avatar">
                        @else
                        <img class="img-circle" src="{{asset('public/img/useravatar.png')}}" width="190" alt="User Avatar">
                        @endif

                      </div>
                      
                      <div style="margin-bottom: 180px">
                          @if (empty($n->nickname) || $n->nickname==" ")
                             <h3 class="widget-user-username" style="line-height: 0.2em"><a style="color: #fff" href="{{action('UserController@show',$n->id)}}"><small  style="color: #fff"  >{{$n->firstname}} {{$n->lastname}} </small></a><br/></h3>
                         @else
                             <h3 class="widget-user-username text-white" style="line-height: 0.2em"><a href="{{action('UserController@show',$n->id)}}"><small style="color: #fff"  >{{$n->nickname}} {{$n->lastname}} </small></a><br/></h3>
                         @endif
                         <h5 style="margin-top: -7px"><small style="color:#666; font-weight: bolder"> {{$n->name}} </small><br/>

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

<div class="item active text-center">
                      
                              <!-- <h4 class="text-orange" >Monday <span class="text-primary"> Motivation </span></h4> -->
                              
                              <p class="text-center" style="padding: 50px 50px 10px 50px;"><strong>Hello, Open Access family,</strong>  <br/><br/>
                                 

                                You are all invited to watch the LIVE TELECAST of <br/><span style="font-size: x-large;">Miss Universe 2019!</span><br/><br/>
                                <strong>Where:</strong> 5F Left wing Jaka bldg and G2 lobby<br/>
                                <strong>When:</strong> Monday December 9, 2019 8AM-12noon<br/></br/>See you there!</p>
                             
                              <img src="./storage/uploads/missu-invite.jpg" style="z-index: 2" />
                               


                    </div>

<!-- FRIGHTENING TALES WINNERS -->
 <div class="item  text-center" >
                      <p style="margin-left: 35px; margin-right: 20px">
                        <strong class="text-danger">Congratulations </strong><br/> to our <strong>Frightful Tales contest winners</strong>.<br/>
                        <em><br/>They each received a 24,000 mAh power bank to help power up their dark evenings (and other goodies).</em></p>
                      <img src="./storage/uploads/winner-1.jpg" style="z-index: 2" width="100%" />
                      <h3 class="text-primary">Krystle Salde<br/>
                      <span style="font-size: small;" class="text-danger"><em>"When TL is away, someone/something will play?"</em></span></h3>
                      <a class="btn btn-success" href="{{action('EngagementController@show',1)}}"><i class="fa fa-search"></i> See the winning entries </a>
                      
                      
                </div>


                <div class="item text-center" >
                      <p style="margin-left: 35px; margin-right: 20px">
                        <strong class="text-danger">Congratulations </strong><br/> to our Frightful Tales contest winners.<br/>
                        <em><br/>They each received a 24,000 mAh power bank to help power up their dark evenings (and other goodies).</em></p>
                      <img src="./storage/uploads/winner-2.jpg" style="z-index: 2" width="100%" />
                      <h3 class="text-primary">Dwik Morados<br/>
                      <span style="font-size: small;" class="text-danger"><em>"5th Floor"</em></span></h3>
                      <a class="btn btn-success" href="{{action('EngagementController@show',1)}}"><i class="fa fa-search"></i> See the winning entries </a>
                      
                      
                </div>
                <div class="item text-center" >
                      <p style="margin-left: 35px; margin-right: 20px">
                        <strong class="text-danger">Congratulations </strong><br/> to our Frightful Tales contest winners.<br/>
                        <em><br/>They each received a 24,000 mAh power bank to help power up their dark evenings (and other goodies).</em></p>
                      <img src="./storage/uploads/winner-3.jpg" style="z-index: 2" width="100%" />
                      <h3 class="text-primary">Lester Bambico<br/>
                      <span style="font-size: small;" class="text-danger"><em>"Linda"</em></span></h3>
                      <a class="btn btn-success" href="{{action('EngagementController@show',1)}}"><i class="fa fa-search"></i> See the winning entries </a>
                      
                      
                </div>


<div class="item active text-center" ><!--  style="background-color: #f2232d" -->
                      
                              <!-- <h4 class="text-orange" >Monday <span class="text-primary"> Motivation </span></h4> -->
                              <img src="./storage/uploads/thanksgiving-2019.png" style="z-index: 2" />
                               <p class="text-center" style="padding: 50px;f">
                                 

                                With origins that can be traced back to harvest festivals, #Thanksgiving is a federal holiday celebrated in the United States on the fourth Thursday of November. Traditional celebrations include family gatherings, city parades, and professional football games. How are you celebrating the holiday?<br/><br/>To our friends, family, and colleagues celebrating the holiday, we wish you a happy Thanksgiving!<br/><br/>#WeSpeakYourLanguage #OAonThanksgiving</p>

                              
                               <br/><br/><br/>

                    </div>

                  
                   
                <div class="item active text-center">

                              <h3 class="text-danger"><strong><i class="fa fa-money"></i> <span style="font-size:smaller">Referral Bonus: </span><i class="fa fa-money"></i> <br/>Php 10,000.00 </strong> <br/>
                                <br/><span class="text-orange" style="font-size:0.8em">For those referrals who can start on <strong>Monday, November 11</strong></span></br/><br/>
                                <span class="text-primary">Client Support Representative <br/> <br/>
                                <img src="public/img/logo_advent.png" width="40%" /><br/>
                              </h3><BR/><BR/>

                              <h4>Brief Description: </h4>
                              <p style="padding:30px;">As a Client Support Representative for the Global Client Services (GCS) Group, you will
provide great service to Advent Software’s clients by resolving technical and product
functionality inquiries via phone, web and e-mail to ensure the successful use of our solutions
and a high level of customer satisfaction.</p>
                              
                             <h4><br/>Qualifications:</h4>

                              <ul class="text-left" style="margin-left: 30px">
                                <li>Strong financial and technical background</li>
                                <li>Excellent interpersonal skills and ability to work well within a team environment</li>
                                <li>Superior troubleshooting and analysis / resolution skills</li>
                                <li>Proven aptitude to learn complex technical and theoretical information quickly</li>
                                <li>Ability to prioritize and manage multiple complex issues and adapt to different challenges
and changing priorities</li>
<li>Excellent written and verbal communication skills in English, with a strong attention to detail</li>
<li>Working knowledge of MS Windows, MS Office, system architecture and environments</li>
<li>A degree in MIS, computer science, other technical economics or finance</li>
<li>Proficiency in SQL and basic Networking Systems.</li>
                              </ol>

                              
                              
                              



                </div>

      <!-- ANDALES, ANICETO, AQUINO, DAWIS, DICEN, OCAMPO, PICANA, SIBAL, SIMON, SUAREZ, YLMAZ, ZUNZU -->
                  <?php $cidol=1;?>
                  @foreach($idols as $idol)
                   
                  
                   <div class="item  text-center" style="min-height: 800px; background-size:98%;background-position: top center; background-repeat: no-repeat; background-image: url('./storage/uploads/idolbg.jpg')" >

                  

                      
                               
                               <h2 style="color: #fff"><br/><br/><br/><br/><br/><br/><br/><br/><br/>Idol Contender</h2>

                               <p class="text-center" style="color: #fff">

                                <a class="text-yellow" href="{{action('UserController@show',$idol->id)}}" target="_blank"><img src="./public/img/employees/{{$idol->id}}.jpg" width="200px" class="img-circle" /><br/>
                                <h4 class="text-warning" style="text-transform: uppercase;"> {{$idol->firstname}} {{$idol->lastname}} <br/>
                                                          <span style="font-size: x-small;">{{$idol->jobTitle}} </span><br/>
                                                          @if (empty($idol->filename))
                                                              {{$idol->program}}
                                                          @else
                                                           <img style="background-color: #fff;" src="{{ asset('public/img/'.$idol->filename) }}" height="30" />

                                                          @endif

                                </h4></a></p>
                                <p style="color:#dedede">Thank you to all our contenders who joined in our first ever, <br/>Open Access Idol! You are all rockstars!!! 
                                  <br/><a target="_blank" class="btn btn-sm btn-success" href="{{action('GalleryController@show',1)}}"><i class="fa fa-picture-o"></i> View Gallery</a> </p>


                               

                  </div>

                  <?php $cidol++;?>
                  @endforeach

  <div class="item text-center" style="background-color: #fff" >
                      <img src="./storage/uploads/blacknwhite.jpg" style="z-index: 2" />
                      <br/><br/>
                      <p style="margin-left: 35px;margin-right: 20px">Hello Open Access Family,<br/><br/>

                      October is almost over which means we are nearing the holiday season!
                      And what does that mean for Open Access BPO?
                      It means it's almost time to throw our most awaited event, the<strong> YEAR END PARTY!</strong><br/><br/>

                      Mark your calendars as this year we will party to the theme that won in our survey - <strong style="font-size: larger; color: #000"> BLACK & WHITE!</strong><br/><br/>

                      Date: <strong class="text-danger">December 14, 2019 Saturday</strong> <br/>
                      Venue:<strong class="text-danger"> Rizal Ballroom, Makati Shangri-La</strong><br/><br/>

                      Express yourself in Black & White as we party all night with great food, drinks, raffle prizes, and entertainment!<br/><br/>

                      <a class="btn btn-md btn-primary" href="https://docs.google.com/forms/d/e/1FAIpQLScbaoSSOL5m1mAz0ZV5kWqbDi7iTKLIYUkMKsJoL-PNtWEcRA/viewform" target="_blank"> Register Now </a><br/>to confirm your attendance. <br/>Registration ends October 31, 2019<br/><br/><br/></p>

                </div>  
<div class="item text-center" >
                    
                      <img src="./storage/uploads/welcome_advent.jpg" style="z-index: 2" width="100%" /><br/>
                      
                      
                </div>
     <div class="item text-center" >
                      
                              <!-- <h4 class="text-orange" >Monday <span class="text-primary"> Motivation </span></h4> -->
                              <img src="./storage/uploads/happiness.jpg" style="z-index: 2" />
                               <p class="text-left" style="padding-left: 50px;"><br/><br/>Exercising positive thinking helps us train our minds, letting us achieve better results. <strong>#WeSpeakYourLanguage #MondayMotivation</strong></p>


                               <div style="padding:10px; position: absolute;bottom: 0px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                    <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/>#WeSpeakYourLanguage #MondayMotivation</small></a></div> <br/><br/><br/><br/>          

                    </div>

                    
   



                      <div class="item text-center" >

                              <img src="./storage/uploads/dance.jpg" style="z-index: 2" />

                              
                              <p class="text-left" style="padding-left: 30px;"><br/><br/>Growth: It starts within us, to challenge our better and beat our best.<br/><br/><strong>#WeSpeakYourLanguage #MondayMotivation #InternationalDanceDay #DayDay</strong></p>


                      </div>

                     

                     


                      
                      

                  

                    <div class="item text-center" >
                        <img src="./storage/uploads/motivation.jpg" style="z-index: 2" width="100%" /><br/><br/>
                        <p style="padding:10px 50px"> Don't stop until you get there. You're going to trip sometimes, but the important part is you're going to get up and come back stronger. Every lesson you pick up in those times is a key to a door of opportunities in life, so keep going. ✨<br/><br/>#WeSpeakYourLanguage #MondayMotivation</p>

                          <div style="padding:10px; position: absolute;bottom: 0px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                    <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #MondayMotivation</small></a></div> <br/><br/><br/><br/>

                    </div>


 

                    


  <div class="item text-center" >
                              <img src="./storage/uploads/motivation-mia.jpg" style="z-index: 2" />
                               <div style="padding:10px; position: absolute;bottom: 0px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                    <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage</small></a></div> <br/><br/><br/><br/>          

                    </div>

 <!--zumba sched-->
                <div class="item text-center" >
                    
                     <p style="padding: 5px 30px; margin-bottom: 0px"><h3 class="text-orange"><span style="font-size: smaller;" class="text-primary">Last 2</span> Zumba Classes</h3>
                     <img src="./storage/uploads/letsgetphysical-banner.jpg" style="z-index: 2" /><br/>
                     <br/><br/>

                     <strong style="font-size:larger"><span class="text-primary"> Tuesday:</span> 
                        <br/><span class="text-danger">October 22, 2019 (Tue) – 4:30 PM</span><br/></strong><br/>

                      <strong style="font-size:larger"><span class="text-primary"> Thursday:</span> 
                        <BR/><span class="text-danger">October 24, 2019 (Thu) – 7:00 PM</span><br/></strong><br/> 

                        <a class="btn btn-danger" href="https://docs.google.com/forms/d/e/1FAIpQLSeZsdfWT5UvVOWnWmEGG9uAhmfLLVyK1yHyJ9U-wP7KciTsPQ/viewform" target="_blank">
                          Sign Up Now</a><br/><br/>

                          <p class="text-left" style="padding-left: 30px;"><br/><br/>All attendees of the workout classes will be eligible to win in the raffle! For each class you attend, you'll have a raffle entry. The more classes you attend, the more chances you'll have to win the major prize! <br/><br/>Here are the amazing prizes up for grabs:</p>

                          <h5 class="text-primary">Major Prize: <strong>1 Winner - Trip to Boracay!</strong></h5>
                          
                          <div class="text-left"  style="padding-left: 30px;">
                            <h5>Minor Prizes:</h5>
                            <ul>
                              <li>3 winners: SM Gift certificates worth Php 2,000</li>
                              <li>1 winner: SM Gift certificates worth Php 1,500</li>
                              <li>1 winner: SM Gift certificates worth Php 1,000</li>
                              <li>1 winner: JBL Flip3 Bluetooth speaker</li>
                              <li>1 winner: Promate 10,000mAh Powerbank</li>
                              <li>1 winner: urBeats3 earphones</li>
                            </ul></div>

                         

                      <br/><br/>
                       <strong>Venue:</strong><br/>

                        OPEN ACCESS BPO G2 OFFICE<br/>

                        11F Glorietta 2 Corporate Center,<br/>

                        West. St., Ayala Center, Makati City<br/><br/>

                        <img src="./storage/uploads/oam-location.jpg" style="z-index: 2" width="100%" />
                        <br/><br/>

                          View our <a class="text-danger" href="./gallery?a=5"><i class="fa fa-picture-o"></i> Gallery</a> for more<br/> #WeSpeakYourLanguage #LetsGetPhysical #OAforWellness

                       
                        <div style="padding:10px; position: absolute;bottom: 0px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                              <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAforWellness #LetsGetPhysical #Zumba</small></a></div> <br/><br/><br/><br/>
                </div>  



 <!--zumba sched-->
                <div class="item text-center" >
                    
                     <p style="padding: 5px 30px; margin-bottom: 0px"><h3 class="text-orange"><span style="font-size: smaller;" class="text-primary">Zumba Class</span> Is Back!</h3>
                     <img src="./storage/uploads/letsgetphysical-banner.jpg" style="z-index: 2" /><br/>
                     <br/><br/>

                     <strong style="font-size:larger"><span class="text-primary"> Tuesday:</span> 
                        <br/><span class="text-danger">October 15, 2019 (Tue) – 4:30 PM</span><br/></strong><br/>

                      <strong style="font-size:larger"><span class="text-primary"> Thursday:</span> 
                        <BR/><span class="text-danger">October 17, 2019 (Thu) – 7:00 PM</span><br/></strong><br/> 

                        <a class="btn btn-danger" href="https://docs.google.com/forms/d/e/1FAIpQLSeZsdfWT5UvVOWnWmEGG9uAhmfLLVyK1yHyJ9U-wP7KciTsPQ/viewform" target="_blank">
                          Sign Up Now</a><br/><br/>

                          <p class="text-left" style="padding-left: 30px;"><br/><br/>All attendees of the workout classes will be eligible to win in the raffle! For each class you attend, you'll have a raffle entry. The more classes you attend, the more chances you'll have to win the major prize! <br/><br/>Here are the amazing prizes up for grabs:</p>

                          <h5 class="text-primary">Major Prize: <strong>1 Winner - Trip to Boracay!</strong></h5>
                          
                          <div class="text-left"  style="padding-left: 30px;">
                            <h5>Minor Prizes:</h5>
                            <ul>
                              <li>3 winners: SM Gift certificates worth Php 2,000</li>
                              <li>1 winner: SM Gift certificates worth Php 1,500</li>
                              <li>1 winner: SM Gift certificates worth Php 1,000</li>
                              <li>1 winner: JBL Flip3 Bluetooth speaker</li>
                              <li>1 winner: Promate 10,000mAh Powerbank</li>
                              <li>1 winner: urBeats3 earphones</li>
                            </ul></div>

                         

                      <br/><br/>
                       <strong>Venue:</strong><br/>

                        OPEN ACCESS BPO G2 OFFICE<br/>

                        11F Glorietta 2 Corporate Center,<br/>

                        West. St., Ayala Center, Makati City<br/><br/>

                        <img src="./storage/uploads/oam-location.jpg" style="z-index: 2" width="100%" />
                        <br/><br/>

                          View our <a class="text-danger" href="./gallery?a=5"><i class="fa fa-picture-o"></i> Gallery</a> for more<br/> #WeSpeakYourLanguage #LetsGetPhysical #OAforWellness

                       
                        <div style="padding:10px; position: absolute;bottom: 0px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                              <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAforWellness #LetsGetPhysical #Zumba</small></a></div> <br/><br/><br/><br/>
                </div>  



<!--OKTOBERFEST-->
<div class="item  text-center" style="background-color: #fff" >
                      
                              <!-- <h4 class="text-orange" >Monday <span class="text-primary"> Motivation </span></h4> -->
                              <p class="text-center"><a class="btn btn-lg btn-danger" href="https://docs.google.com/forms/d/e/1FAIpQLSf1QiZugFbb-8_E4xJBMhwVJf9EzSTlET9ERXR9AfzNgYXKrA/viewform" target="_blank">Register NOW </a> </p>
                                <img src="./storage/uploads/oktoberfest.jpg" style="z-index: 2" />

                  </div>

                  

<!--CS WEEK STUFF -->
<div class="item text-center" style="background-color: #fff; background:url('./storage/uploads/ben.png')top right no-repeat #fff;" >
                      
                              <!-- <h4 class="text-orange" >Monday <span class="text-primary"> Motivation </span></h4> -->
                              <p class="text-center"><strong class="text-orange">Happy </strong> <strong class="text-primary">CS Week 2019 !!!</strong></p>
                              <p style="padding-left:30px;padding-right: 250px; font-size: smaller;" class="text-left">
                                <br/><br/><strong>Dear Open Access Family,</strong> <br/><br/>

                                For us in the Exec team, you’re not just our employees.
                                Every single one of you is part of the company’s success, and for that, we want to thank you. We organized the Customer Service Week as a way of extending our heartfelt gratitude for your dedication, loyalty, and hard work. This week will be packed with fun activities, so go ahead and enjoy with the rest of the family. <br/><br/>Happy Customer Service Week!<br/><br/>

                                Ben
                              </p><br/><br/><br/><br/><br/><br/>
                              <h4 class="text-center text-danger"> CS Week 2019 Activities:</h4>

 
                              <table class="table text-left" style="padding:20px;width: 80%">
                                <tr>
                                  <td class="text-primary"><strong>Oct 8 (Tue) Movie Day</strong> <br/>9AM - 12MN</td>
                                  <td>* Jaka - 5F common area <br/>* G2 lobby</td>
                                  
                                </tr>
                                <tr>
                                  <td class="text-primary"><strong>Oct 9 (Wed) Massage Day</strong> <br/>10AM - 10PM</td>
                                  <td>* Jaka - 5F common area<br/>* G2 - recreational area</td>
                                </tr>
                                <tr>
                                  <td class="text-primary"><strong>Oct 10 (Thu) Magical Day with your teams</strong> </td>
                                  <td>(Activities to do with your respective teams)</td>
                                </tr>
                                <tr>
                                  <td class="text-primary"><strong>Oct 11 (Fri) Magical Day as One Team</strong></td>
                                  <td>(Townhall, Open Access Idol & Snacks for all)</td>
                                </tr>
                                
                              </table><br/><br/><br/><br/><br/><br/><br/><br/>
                               


                  </div>

                  <div class="item  text-center" style="background-color: #fff" >
                      
                              <!-- <h4 class="text-orange" >Monday <span class="text-primary"> Motivation </span></h4> -->
                              <p class="text-center"><strong class="text-orange">Happy </strong> <strong class="text-primary">CS Week 2019 !!!</strong></p>
                                <img src="./storage/uploads/csweek2019.jpg" style="z-index: 2" />

                  </div>

                  <div class="item text-center" style="background-color: #fff" >
                      
                              <!-- <h4 class="text-orange" >Monday <span class="text-primary"> Motivation </span></h4> -->
                              <p class="text-center"> <strong class="text-primary"><br/>Here's a quick throwback to our last year's</strong>
                                <br/><strong class="text-orange"> CS Week 2018 </strong></p><a class="btn btn-default btn-sm" href="{{ action('HomeController@gallery',['a'=>18]) }}" target="_blank"><i class="fa fa-picture-o"></i> View Gallery</a><br/><br/>
                                 <img src="./storage/uploads/dressupWinner2.jpg" style="z-index: 2" /><br/>
                                
                               

                  </div>

 <div class="item active  text-center" style="background-color: #fff" >
                      
                              <!-- <h4 class="text-orange" >Monday <span class="text-primary"> Motivation </span></h4> -->
                              <p class="text-center"></p>
                                <img src="./storage/uploads/day5.jpg" style="z-index: 2" />

                  </div>

<!--MOBILE LEGENDS-->
<div class="item  text-center" style="background-color: #fff" >
                      
                              <h4 class="text-primary" >Open Access <span class="text-orange"> Mobile Legends Tournament </span></h4>
                              <p class="text-center">Create a <strong class="text-danger" style="font-size: larger;">team of 5 players</strong> and <br/><a class="btn btn-md btn-danger" href="https://docs.google.com/forms/d/e/1FAIpQLSeqN1SnEJp44dmEmR-rWkPGCd2orZ7lBW7QTFjwFmBZzVHwwA/viewform" target="_blank">Register Now!</a><br/><br/>
                                Registration ends <strong>Oct. 9, 2019 Wed 3PM </strong></p>
                              <p class="text-center"><strong class="text-primary"></strong></p>
                                <img src="./storage/uploads/mobileLegends.jpg" style="z-index: 2" />

                  </div>


 <div class="item text-center" style="background-color: #fff" >
                      
                              <!-- <h4 class="text-orange" >Monday <span class="text-primary"> Motivation </span></h4> -->
                              <img src="./storage/uploads/davao-anniv-1.jpg" style="z-index: 2" />
                               <p class="text-center" style="padding-left: 0px;"><br/><br/><strong class="text-primary">Open Access BPO Davao recently celebrated it's4th year</strong>  <br/>and we couldn't be any happier!<br/><br/>It was indeed one awesome indoor party, celebrated with<strong>CEO Ben Davidowitz, President Henry Chang, and Business Development Manager Alessio Urbani</strong>  (with two furry friends). Cheers to more years together, team Davao!</strong></p>
                                <a class="btn btn-md btn-primary" target="_blank" href="{{ action('HomeController@gallery',['a'=>17]) }}"><i class="fa fa-picture-o"></i> View album</a><br/><br/><br/>

                    </div>


<div class="item text-center" style="background-color: #fff" >
                      
                              <!-- <h4 class="text-orange" >Monday <span class="text-primary"> Motivation </span></h4> -->
                              <p class="text-center"><strong class="text-primary"></strong></p>
                                <img src="./storage/uploads/welcome-mattCate.jpg" style="z-index: 2" />

                  </div>

<!-- shana tova -->
<div class="item  text-center" style="background-color: #fff" >
                      
                              <!-- <h4 class="text-orange" >Monday <span class="text-primary"> Motivation </span></h4> -->
                              <img src="./storage/uploads/rosh.jpg" style="z-index: 2" />
                               <p class="text-center" style="padding: 30px;"><br/><br/><strong class="text-primary">Shanah Tovah</strong>
                                <br/><br/>Shanah Tovah to our Jewish brothers and sisters celebrating #RoshHashanah. It also means “Head of the Year” and serves as the Jewish New Year, marking the first of the High Holidays in their calendar.<br/><br/>#WeSpeakYourLanguage #OAonRoshHashanah</p>

                              <br/><br/>

                  </div>
 


<!-- heart day-->
 <div class="item  text-center" style="background-color: #fff" >
                      
                              <!-- <h4 class="text-orange" >Monday <span class="text-primary"> Motivation </span></h4> -->
                              <img src="./storage/uploads/heartday-1.jpg" style="z-index: 2" />
                               <p class="text-center" style="padding: 30px;"><br/><br/><strong class="text-primary">Sept.29 : Happy World Heart Day!</strong>
                                <br/><br/>Every heartbeat matters. On #WorldHeartDay, make the commitment to take care of your heart and prevent heart diseases and stroke. Inspire your loved ones to do the same!<br/><br/>#WeSpeakYourLanguage #OAonHeartDay</p>

                              <br/><br/>

                  </div>
                  <div class="item  text-center" style="background-color: #fff" >
                      
                              <!-- <h4 class="text-orange" >Monday <span class="text-primary"> Motivation </span></h4> -->
                              <img src="./storage/uploads/heartday-2.jpg" style="z-index: 2" />
                               <p class="text-center" style="padding: 30px;"><br/><br/><strong class="text-primary">Sept.29 : Happy World Heart Day!</strong>
                                <br/><br/>Every heartbeat matters. On #WorldHeartDay, make the commitment to take care of your heart and prevent heart diseases and stroke. Inspire your loved ones to do the same!<br/><br/>#WeSpeakYourLanguage #OAonHeartDay</p>

                              <br/><br/>

                  </div>


<!-- SUICIDE -->
<div class="item text-center" style="background-color: #fff" >
                      <img src="./storage/uploads/suicide-0.png" style="z-index: 2" />
                      <br/><br/><br/>

                    </div>
                    <div class="item text-center" style="background-color: #fff" >
                      <img src="./storage/uploads/suicide-0a.png" style="z-index: 2" />
                      <br/><br/><br/>
                    </div>
                    <div class="item text-center" style="background-color: #fff" >
                      <img src="./storage/uploads/suicide-0b.png" style="z-index: 2" />
                      <br/><br/><br/>
                    </div>
                    <div class="item text-center" style="background-color: #fff" >
                      <img src="./storage/uploads/suicide-2.png" style="z-index: 2" />
                      <br/><br/><br/>
                    </div>

                    <div class="item text-center" style="background-color: #fff" >
                      <img src="./storage/uploads/suicide-3.png" style="z-index: 2" />
                      <br/><br/><br/>
                    </div>

                    <div class="item text-center" style="background-color: #fff" >
                      <img src="./storage/uploads/suicide-4.png" style="z-index: 2" />
                      <br/><br/><br/>
                    </div>

                    <div class="item text-center" style="background-color: #fff" >
                      <img src="./storage/uploads/suicide-5.png" style="z-index: 2" />
                      <br/><br/><br/>
                    </div>

                    <div class="item text-center" style="background-color: #fff" >
                      <img src="./storage/uploads/suicide-6.png" style="z-index: 2" />
                      <br/><br/><br/>
                    </div>

                    <div class="item text-center" style="background-color: #fff" >
                      <img src="./storage/uploads/suicide-7.png" style="z-index: 2" />
                      <br/><br/><br/>
                    </div>

                    <div class="item text-center" style="background-color: #fff" >
                      <img src="./storage/uploads/suicide-8.png" style="z-index: 2" />
                      <br/><br/><br/>
                    </div>

 

 <!--wellness-->
                    <div class="item text-center" >
                              <img src="./storage/uploads/wellness-0911.jpg" style="z-index: 2" />
                               <p class="text-left" style="padding-left: 50px;"><br/><br/>We invite everyone to participate in the Wellness Event
                                <strong>TODAY, September 12, 2019 </strong>during your breaktime: <br/><br/>


                                <strong class="text-danger">WHAT: </strong>   Open Access Clinic Wellness Program <br/>


                                <strong class="text-danger">WHERE:</strong> <strong class="text-primary" style="font-size:1.1em">11TH FLR Glorietta2 Corporate Tower</strong> <br/>

                                <strong class="text-danger">WHEN:</strong> <strong class="text-primary" style="font-size:1.1em">THURSDAY Sept 12, 2019, 10AM-7PM</strong>
                                <br/><br/> <!-- <a href="{{ action('HomeController@gallery',['a'=>11]) }}" class="text-center btn btn-xs btn-default"><i class="fa fa-image"></i> View More in our Gallery</a> -->
                                <strong>Activities:</strong><br/><br/>

                                <strong class="text-primary">♦Leny’s Massage</strong><br/>

                                  - Free Massage Therapy<br/>

                                  - Product Sampling of White Flower Oil<br/>

                                  - Product Sampling of Hot/Warm Compress Bag<br/>

                                  - Ear Candling and Soft Selling<br/>


                                <strong class="text-primary">♦ AGO</strong><br/>

                                           -  Eye check up<br/>

                                           - Sample frames /eyeglasses<br/>
                                <strong class="text-primary">♦BDO Unibank Inc ( Sept 11 ONLY)</strong><br/>

                                        -credit card booth<br/>



                                 <strong class="text-primary">Sante Int'l Inc.</strong><br/>
                                        - Various Sante Barley Products<br/><br/> <strong>#WeSpeakYourLanguage #OAonWellness</strong></p>


                               <div style="padding:10px; position: absolute;bottom: 0px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                    <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAonWellness</small></a></div> <br/><br/><br/><br/>          

                    </div>
 <!--ZUMBA -->
                    <div class="item text-center" >
                        
                         <p style="padding: 5px 30px; margin-bottom: 0px"><h3 class="text-orange"><span style="font-size: smaller;" class="text-primary">Zumba Classes</span> will be back next week!</h3>
                         <br/><br/>

                         <!--  <strong style="font-size:larger"><span class="text-primary"> Tuesday:</span> 
                            <br/><span class="text-danger">September 10, 2019 (Tue) – 4:30 PM</span><br/></strong><br/> -->

                          <strong style="font-size:larger"><span class="text-primary"> Thursday:</span> 
                            <BR/><span class="text-danger">September 12, 2019 (Thu) – 7:00 PM</span><br/></strong><br/> 

                            <a class="btn btn-danger" href="https://docs.google.com/forms/d/e/1FAIpQLSeZsdfWT5UvVOWnWmEGG9uAhmfLLVyK1yHyJ9U-wP7KciTsPQ/viewform" target="_blank">
                              Sign Up Now</a><br/><br/>

                              <p class="text-left" style="padding-left: 30px;"><br/><br/>All attendees of the workout classes will be eligible to win in the raffle! For each class you attend, you'll have a raffle entry. The more classes you attend, the more chances you'll have to win the major prize! <br/><br/>Here are the amazing prizes up for grabs:</p>

                              <h5 class="text-primary">Major Prize: <strong>1 Winner - Trip to Boracay!</strong></h5>
                              
                              <div class="text-left"  style="padding-left: 30px;">
                                <h5>Minor Prizes:</h5>
                                <ul>
                                  <li>3 winners: SM Gift certificates worth Php 2,000</li>
                                  <li>1 winner: SM Gift certificates worth Php 1,000</li>
                                  <li>1 winner: JBL Flip3 Bluetooth speaker</li>
                                  <li>1 winner: Promate 10,000mAh Powerbank</li>
                                  <li>1 winner: urBeats3 earphones</li>
                                </ul></div>

                             <img src="./storage/uploads/letsgetphysical-118.jpg" style="z-index: 2" /><br/>

                          <br/><br/>
                           <strong>Venue:</strong><br/>

                            OPEN ACCESS BPO G2 OFFICE<br/>

                            11F Glorietta 2 Corporate Center,<br/>

                            West. St., Ayala Center, Makati City<br/><br/>

                            <img src="./storage/uploads/oam-location.jpg" style="z-index: 2" width="100%" />
                            <br/><br/>

                              View our <a class="text-danger" href="./gallery?a=5"><i class="fa fa-picture-o"></i> Gallery</a> for more<br/> #WeSpeakYourLanguage #LetsGetPhysical #OAforWellness

                           
                            <div style="padding:10px; position: absolute;bottom: 0px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                  <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAforWellness #LetsGetPhysical #Zumba</small></a></div> <br/><br/><br/><br/>
                    </div>
  <!-- dance your way to BORACAY -->
                    <div class="item text-center" >
                        <h4 class="text-primary">Dance your way <br/><span class="text-orange">to Boracay!</span></h4>

                              <img src="./storage/uploads/letsgetphysical-118.jpg" style="z-index: 2" />

                              
                              <p class="text-left" style="padding-left: 30px;"><br/><br/>All attendees of the workout classes will be eligible to win in the raffle! For each class you attend, you'll have a raffle entry. The more classes you attend, the more chances you'll have to win the major prize! <br/><br/>Here are the amazing prizes up for grabs:</p>

                              <h5 class="text-primary">Major Prize: <strong>1 Winner - Trip to Boracay!</strong></h5>
                              
                              <div class="text-left"  style="padding-left: 30px;">
                                <h5>Minor Prizes:</h5>
                                <ul>
                                  <li>4 winners: SM Gift certificates worth Php 2,000</li>
                                  <li>2 winners: SM Gift certificates worth Php 1,000</li>
                                  <li>1 winner: JBL Bluetooth speaker</li>
                                  <li>1 winner: Cheero 10050 Powerbank</li>
                                  <li>1 winner: Beats&trade; head set</li>
                                </ul></div>

                              <p class="text-center" style="padding-left: 30px;"><a class="btn btn-danger" href="https://docs.google.com/forms/d/e/1FAIpQLSeZsdfWT5UvVOWnWmEGG9uAhmfLLVyK1yHyJ9U-wP7KciTsPQ/viewform?usp=sf_link" target="_blank">Sign Up Now</a></p>



                    </div>

                    




<div class="item active text-center" >
                      
                              <!-- <h4 class="text-orange" >National Heroes <span class="text-primary"> Day </span></h4> --> 
                              <img src="./storage/uploads/heroes.jpg" style="z-index: 2" />
                               <p class="text-left" style="padding-left: 50px;"><br/><br/>August 26 is National Heroes Day. Let's take this moment to remember and honor the contributions of our national and unsung Filipino heroes in achieving the peace and freedom of today. May their legacy continue to live on. <strong>#WeSpeakYourLanguage #OACelebratesHeroes</strong></p>


                               <div style="padding:10px; position: absolute;bottom: 0px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                    <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/>#WeSpeakYourLanguage #OACelebratesHeroes</small></a></div> <br/><br/><br/><br/>          

                    </div>

   <!--RYans run -->
                    <div class="item text-center" >
                      
                              <h4 class="text-orange" >Open Access <span class="text-primary"> Cares </span></h4>
                              <img src="./storage/uploads/ryans-run.jpg" style="z-index: 2" />
                               <p class="text-left" style="padding-left: 50px;"><br/><br/>Ryan's Run and Allied Services have always been dear to the Open Access BPO family. We're happy to lend a hand to support these non-profit organizations as they help people with disabilities, life-changing injuries, and chronic illnesses.<br/><br/>
                               Photo credit: Ryan's Run</strong><br/></p>

                               <p class="text-left" style="padding-left: 50px; font-size: smaller;"><strong>Repost:</strong>
                                Feeling #thankful for the support of our friends and major sponsors @openaccessbpo This Philippines based multi-lingual customer care and support center gives generously to change lives @alliedservicesihs Thank you for your incredible support. <br/><br/>#philanthropy #ryansrun #pinoyspirit #changinglives #charityteam #alliedservices #makeadifference</p>


                               <div style="padding:10px; position: absolute;bottom: 0px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                    <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAcares</small></a></div> <br/><br/><br/><br/>          

                    </div>

  <!--zumba sched-->
                    <div class="item text-center" >
                        
                         <p style="padding: 5px 30px; margin-bottom: 0px"><h3 class="text-orange"><span style="font-size: smaller;" class="text-primary"> Free Zumba Class</span> New Schedule</h3>
                         <br/><br/>

                          <strong style="font-size:larger"><span class="text-primary"> Tuesday:</span> 
                            <br/><span class="text-danger">August 27, 2019 (Tue) – 4:30 PM</span><br/></strong><br/>

                          <strong style="font-size:larger"><span class="text-primary"> Thursday:</span> 
                            <BR/><span class="text-danger">August 29, 2019 (Thu) – 7:00 PM</span><br/></strong><br/> 

                            <a class="btn btn-danger" href="https://docs.google.com/forms/d/e/1FAIpQLSeZsdfWT5UvVOWnWmEGG9uAhmfLLVyK1yHyJ9U-wP7KciTsPQ/viewform" target="_blank">
                              Sign Up Now</a><br/><br/>

                              <p class="text-left" style="padding-left: 30px;"><br/><br/>All attendees of the workout classes will be eligible to win in the raffle! For each class you attend, you'll have a raffle entry. The more classes you attend, the more chances you'll have to win the major prize! <br/><br/>Here are the amazing prizes up for grabs:</p>

                              <h5 class="text-primary">Major Prize: <strong>1 Winner - Trip to Boracay!</strong></h5>
                              
                              <div class="text-left"  style="padding-left: 30px;">
                                <h5>Minor Prizes:</h5>
                                <ul>
                                  <li>4 winners: SM Gift certificates worth Php 2,000</li>
                                  <li>2 winners: SM Gift certificates worth Php 1,000</li>
                                  <li>1 winner: JBL Bluetooth speaker</li>
                                  <li>1 winner: Cheero 10050 Powerbank</li>
                                  <li>1 winner: Beats&trade; head set</li>
                                </ul></div>

                             <img src="./storage/uploads/physical_poster.jpg" style="z-index: 2" /><br/>

                          <br/><br/>
                           <strong>Venue:</strong><br/>

                            OPEN ACCESS BPO G2 OFFICE<br/>

                            11F Glorietta 2 Corporate Center,<br/>

                            West. St., Ayala Center, Makati City<br/><br/>

                            <img src="./storage/uploads/oam-location.jpg" style="z-index: 2" width="100%" />
                            <br/><br/>

                              View our <a class="text-danger" href="./gallery?a=5"><i class="fa fa-picture-o"></i> Gallery</a> for more<br/> #WeSpeakYourLanguage #LetsGetPhysical #OAforWellness

                           
                            <div style="padding:10px; position: absolute;bottom: 0px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                  <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAforWellness #LetsGetPhysical #Zumba</small></a></div> <br/><br/><br/><br/>
                      </div>



                    

                    

                    



<div class="item active text-center" >
                              
                              <h4 class="text-orange" >Open Access <span class="text-primary">Upcoming Events!</span></h4>
                              <img src="./storage/uploads/events2019.jpg" style="z-index: 2" />
                              <p class="text-left" style="padding-left: 50px;"><br/><br/>Hello Open Access Family!<br/><br/>
                                <strong>Keep posted for our exciting upcoming events!</strong><br/><br/>
                                <!-- <strong class="text-danger">JUNE</strong><br/>
                                * Pride March <br/>
                                * Flu Vaccination <br/>
                                * Biggest Loser Challenge <br/>
                                * Zumba Classes <br/><br/> -->

                               

                               <strong class="text-danger">AUGUST</strong> <br/>
                                * Mobile Legends Tournament <br/>
                                * Outreach Activity (Local Community) <br/>
                                * Biggest Loser Challenge <br/>
                                * Zumba Classes <br/><br/>

                              Actual dates and more details will be announced soon.</p>
                               

                              <br/>
                    </div>



                    <div class="item text-center" >
                        <img src="./storage/uploads/motivation-04-10.jpg" style="z-index: 2" width="100%" /><br/><br/>
                        <p style="padding:10px 50px">Start the week fresh and get over your Monday blues! It's going to be a great week— let's claim it! 💪<br/><br/>#WeSpeakYourLanguage #MondayMotivation #QuoteOfTheDay</p>

                          <div style="padding:10px; position: absolute;bottom: 0px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                    <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/>#WeSpeakYourLanguage #MondayMotivation #QuoteOfTheDay</small></a></div> <br/><br/><br/><br/>

                    </div>


 <div class="item  text-center" >
                              
                              <h4 class="text-orange" >Ribbon Cutting <span class="text-primary">Ceremony </span></h4>
                              <img src="./storage/uploads/ribbon-cutting-2.jpg" style="z-index: 2" />
                              <p class="text-left" style="padding-left: 50px;"><br/><br/>Open Access BPO's <a href="./user/1" target="_blank" style="font-weight:bold;">CEO Ben Davidowitz</a>, <a href="./user/1784" target="_blank" style="font-weight:bold;">VP for Global Operations Joy Sebastian,</a> and <a href="./user/184" target="_blank" style="font-weight:bold;">President Henry Chang</a> officiated the launch of the company's new office in Glorietta 2 Corporate Center, Makati City earlier today.<br/><br/>

                              #WeSpeakYourLanguage #OANewDoors<br/><br/>

                              <strong>More Pics:</strong><br/>
                              <a class="btn btn-xs btn-default" href="{{ action('HomeController@gallery',['a'=>13]) }}"><i class="fa fa-picture-o"></i> Ribbon Cutting</a> |  <a class="btn btn-xs btn-default" href="{{ action('HomeController@gallery',['a'=>15]) }}"><i class="fa fa-picture-o"></i> Photo Booth</a> | <a class="btn btn-xs btn-default" href="{{ action('HomeController@gallery',['a'=>14]) }}"><i class="fa fa-picture-o"></i> After Party</a>

                               </p>
                               
                              <br/>
                    </div>  

 <div class="item text-center" >
                      
                              <h4 class="text-orange" >Happy <span class="text-primary"> Eid al-Adha </span></h4>
                              <img src="./storage/uploads/mubarak.jpg" style="z-index: 2" />
                               <p class="text-left" style="padding-left: 50px;"><br/><br/>Sending warm wishes to our colleagues, family, and friends from the Muslim community on this blessed day of #EidalAdha. Happy Eid! </strong></p>


                               <div style="padding:10px; position: absolute;bottom: 0px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                    <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/>#WeSpeakYourLanguage #OAonEidalAdha</small></a></div> <br/><br/><br/><br/>          

                    </div>

 <div class="item active text-center" >

                              <img src="./public/img/ana.jpg" style="z-index: 2" />

                              <div class="text-gray text-right" style="position: relative;width:90%; right:0px;top:-200px; z-index: 999; height: 20px">
                                 <h2><img src="./public/img/white_logo_small.png" width="40" style="margin-right: 30px" /><br/> <strong>Internal<br/>Hiring! </strong></h2>

                              </div>

                               <h4 style="margin-top: -30px" class="text-danger"><strong>Admin Assistant </strong><br/>
                               <small>for</small> 
                               <a target="_blank" href="{{action('CampaignController@show','54')}} "> <img src="./public/img/logo_ndy.png" width="120"></a>
                                </h4>

                              <h5 class="text-danger"><i class="fa fa-exclamation-triangle"></i> Deadline of Submission: <strong class="text-primary"> Aug. 19 2019 Monday </strong></h5>
                              <p class="text-left" style="padding-left: 30px; font-weight: bolder;">Qualifications: </p>
                              <ul class="text-left">
                                
                                <li>No attendance issues / DAs in the last 6 months</li>
                                <li>Has stayed with Open Access for at least 6 months</li>
                              </ul>

                              <p class="text-left" style="padding-left: 30px; font-weight: bolder;"><br/><br/>Responsibilities: </p>
                              <ul class="text-left">

                                <li>Reporting to the Assistant Manager, you will provide administrative support. You will liaise closely with Assistant Manager, to ensure that all deadlines are met for both disciplines in an efficient manner that will also assist you in coordination and prioritizing the workload.</li>
                                <li>You will provide support, guidance, and training to your fellow offshore colleagues, assisting in their development and upskilling.</li>

                              </ul>
                              
                              <p class="text-left" style="padding-left: 30px; font-weight: bolder;"><br/><br/>Skills and Experiences: </p>
                              <ul class="text-left">

                                <li>Abilitiy to develop supervisory skills; with understanding and appreciation for regional differences</li>
                                <li>Uncompromising commitment to client and customer satisfaction</li>
                                <li>Excellent analytical skills with high attention to detail</li>
                                <li>Excellent verbal and written communication skills</li>
                                <li>Ability to maintain confidentiality and to work independently with little supervision</li>
                                <li>Well-developed keyboard and computer skills with an advanced working knowledge of Microsoft Office, internet, and e-mail applications</li>

                              </ul>
                              <br/><br/>

                               
                              <br/><br/>
                              

                              <p><small>Interested applicants may submit their letter of intent and updated resume to: <a href="mailto:recruitment@openaccessbpo.com">recruitment@openaccessbpo.com</a></small></p>


                      </div>

 <div class="item  text-center" >

                              <h4 class="text-orange" >We Speak <span class="text-primary"> With Pride</span></h4>
                              <!-- <img src="./storage/uploads/basket-sched.jpg" style="z-index: 2" /> -->
                              <img src="./storage/uploads/pride2019-1.jpg" style="z-index: 2" />
                               <p class="text-left" style="padding-left: 50px;"><br/><br/>More pics in our <a href="./gallery?a=12"><strong class="text-primary">Gallery page</strong></a><br/><br/>
                                <strong>#WeSpeakYourLanguage #OASpeaksWithPride</strong><br/><br/>



                               </p>


                      </div>


 <div class="item text-center" >
                      
                              <h4 class="text-orange" >Health &amp; Wellness -  <span class="text-primary">Davao </span></h4>
                              <img src="./storage/uploads/davao-1.jpg" style="z-index: 2" />
                               <p class="text-left" style="padding-left: 50px;"><br/><br/>To maintain the health and wellness of our employees in Davao, Open Access BPO has launched its first Health & Wellness Program on July 25, 2019. The event is a joint effort between the Clinic team and our Davao site's HR team and Maxicare who provided us with awesome lifestyle partners.<br/><br/> <a href="{{ action('HomeController@gallery',['a'=>16]) }}" class="text-center btn btn-xs btn-default"><i class="fa fa-image"></i> View More in our Gallery</a><br/><br/> <strong>#WeSpeakYourLanguage #OAonWellness</strong></p>


                               <div style="padding:10px; position: absolute;bottom: 0px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                    <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAonWellness</small></a></div> <br/><br/><br/><br/>          

                    </div>



                   




   <!-- HEALTH AND WELLNESS MONTH -->
                    <div class="item text-center" >
                              <img src="./storage/uploads/health-1.jpg" style="z-index: 2" />
                               <p class="text-left" style="padding-left: 50px;"><br/><br/>Every month is a new Health and Wellness Program experience! Happy to see our employees enjoying their time in each of our partners' booths earlier.<br/><br/>We're also thankful to our partners: Leny's wellness massage services., Slimmers World International, Paterno EyeCare Center, Yakult Phils Inc., and BDO Unibank for supporting us in our aim for a healthier and happier workforce.<br/><br/> <a href="{{ action('HomeController@gallery',['a'=>11]) }}" class="text-center btn btn-xs btn-default"><i class="fa fa-image"></i> View More in our Gallery</a><br/><br/> <strong>#WeSpeakYourLanguage #OAonWellness</strong></p>


                               <div style="padding:10px; position: absolute;bottom: 0px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                    <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAonWellness</small></a></div> <br/><br/><br/><br/>          

                    </div>

                    <div class="item text-center" >
                              <img src="./storage/uploads/dreams2.jpg" style="z-index: 2" />
                               <div style="padding:10px; position: absolute;bottom: 0px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                    <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #MondayMotivation</small></a></div> <br/><br/><br/><br/>          

                    </div>

                      <!-- BTS -->
                    <div class="item text-center" >
                              <h4 class="text-primary">BTS: <span class="text-orange">We Speak Your Language</span></h4>
                              <img src="./storage/uploads/wespeak.jpg" style="z-index: 2" />

                              
                              <p class="text-center" style="padding-left: 30px;"><br/><br/>More behind the scene pics in our <a href="{{ action('HomeController@gallery',['a'=>10]) }}">Gallery</a></strong><br/><br/>

                                </p>


                    </div>

  <div class="item text-center" >
                        
                         <img src="./storage/uploads/teams1.jpg" style="z-index: 2" width="100%" /><br/><br/>
                         

                          <p style="padding: 5px 30px; margin-bottom: 0px;" class="text-left">
                          Get to know more about your <a href="{{action('CampaignController@index')}}"><strong>Open Access family!</strong></a><br/><br/> Check out our <a class="text-orange" href="{{action('CampaignController@index')}}">programs</a> and learn more about the driving force behind our campaigns.</p>

                          <p style="padding: 5px 30px; margin-bottom: 0px; font-size: x-small;" class="text-left"><br/><br/>
                            <i class="fa fa-info-circle text-primary"></i> Tip: Make sure the page you're viewing is updated. Press <strong>CTRL + Shift + R </strong> to do a hard refresh and clear your browser's cache.
                          </p>

                          
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

                      <div class="item text-center" ><h2 class="text-orange">Hellos &amp; Grubs</h2> <br/>
                              <img src="./storage/uploads/hello_grubs1.jpg" style="z-index: 2" />
                              
                              <p style="padding:20px">Our 2019 kicked off with a splendid breakfast filled with fun conversations shared by the Open Access BPO executives and employees -- what a great way to start the year! 
                                Thanks to everyone who joined us earlier on our first <strong class="text-orange">Hellos &amp; Grubs</strong> session!<br/><br/>We can't wait to meet the next batch in a few days!</p>
                                <span class="text-danger">If you won’t get picked for this month, no need to feel bad! We intend to meet <span style="font-size:large">everyone</span> this year as we aim for this to be a monthly activity.</span>
                      </div> 

<div class="item  text-center" >
                              
                              <h4 class="text-orange" >Happy <span class="text-primary">4th of July!</span></h4>
                              <img src="./storage/uploads/july4th.jpg" style="z-index: 2" />
                              <p class="text-left" style="padding-left: 50px;"><br/><br/>Wishing our friends, family, and colleagues a happy #4thOfJuly and Filipino-American Friendship Day! May this day be filled with love, laughter, and peace.<br/><br/>
                                <strong>#WeSpeakYourLanguage #OACelebrates4thofJuly</strong></p>
                               

                              <br/>
                    </div>



  <div class="item  text-center" >
                              
                              <h4 class="text-orange" >Congratulations <span class="text-primary"> Open Access Basketball Team!!!</span></h4>
                              <img src="./storage/uploads/game5.jpg" style="z-index: 2" />
                              <p class="text-center" ><br/><br/> for winning <strong> Game 5</strong> last night, with<br/> <strong class="text-danger">Final score: 86-77</strong><br/><br/>
                                
                                </p>
                               

                              <br/>
                    </div> 


<!-- PRIDE -->
<div class="item text-center" >

                              <h4 class="text-orange" >We Speak <span class="text-primary"> With Pride</span></h4>
                              <!-- <img src="./storage/uploads/basket-sched.jpg" style="z-index: 2" /> -->
                              <img src="./storage/uploads/lgbt-resist-0.jpg" style="z-index: 2" />
                               <p class="text-left" style="padding-left: 50px;"><br/><br/><strong class="text-primary">'We Speak With Pride' </strong> is a special social series aimed at giving our LGBTQIA+ friends and colleagues a platform to speak their truths on what we must all #ResistTogether for and against. In a few days, we will also head to the streets. <br/><br/>

                                In a few days, we will also head to the streets. <br/>Join us on <strong class="text-danger"> June 29 at 2019 Metro Manila Pride March and Festival | #ResistTogether</strong>  for our first Pride march. See you there!<br/><br/>
                                <strong>#WeSpeakYourLanguage #OASpeaksWithPride</strong><br/><br/>



                               </p>


                      </div>

                      <!--Purple-->
                      <div class="item text-center" >

                              <h4 class="text-orange" >#ResistTogether <span class="text-primary"> #OASpeaksWithPride</span></h4>
                              <!-- <img src="./storage/uploads/basket-sched.jpg" style="z-index: 2" /> -->
                              <img src="./storage/uploads/lgbt-resist-6.jpg" style="z-index: 2" />
                               <p class="text-left" style="padding-left: 50px;"><br/><br/><strong class="text-danger">'We Speak With Pride' </strong> series comes to an end, we'd like to leave this message to enlighten people of the community's call to #ResistTogether.<br/><br/>
                                For <a href="./user/22" target="_blank" style="font-weight: bold"> Myla, a QA Team Leader</a>, #Pride is an opportunity to use our voices and enlighten people about the community's stand to #ResistTogether. And as a member, she's taking her call for equal rights and free will and against injustices and discrimination to the streets in this year's Metro Manila Pride March: "We all want a happy ending. We resist for equal rights as heterosexual couples, such as recognizing our partner as our spouse, being able to adopt, being able to buy shared property in our names, and so on. <br/><br/>
                                We want discrimination to stop when we hold hands or kiss, apply for jobs, or enroll for education because of our gender orientation and the way we dress. LGBTQIA+ individuals are people who want to be accepted in society. We aren’t “sick” because our gender preference and identity. We are not different from you—we just have our own colorful individuality." <br/><br/>#WeSpeakYourLanguage #OASpeaksWithPride #WorldPride #PrideMonth2019


                               </p>


                      </div>

                      <!--Blue-->
                      <div class="item text-center" >

                              <h4 class="text-orange" >#ResistTogether <span class="text-primary"> #OASpeaksWithPride</span></h4>
                              <!-- <img src="./storage/uploads/basket-sched.jpg" style="z-index: 2" /> -->
                              <img src="./storage/uploads/lgbt-resist-5.jpg" style="z-index: 2" />
                               <p class="text-left" style="padding-left: 50px;"><br/><br/><strong class="text-danger">'We Speak With Pride' </strong> is a special social series aimed at giving our LGBTQIA+ friends and colleagues a platform to speak their truths on what we must all #ResistTogether for and against.<br/><br/>

                                Being an active member of the LGBTQIA+ community,<a style="font-weight: bold;" href="./user/2070" target="_blank"> Quality Analyst Edwick </a>stresses the value of the people's call to #ResistTogether for HIV/AIDS awareness especially among the younger generation and to continue the fight against discrimination: "As an active HIV/AIDS awareness advocate, I share my knowledge on how to prevent the virus from spreading, so people can responsibly engage in activities that may put them at risk of getting infected. HIV/AIDS is one of the leading infectious diseases in the Philippines and some parts of the world, and I want to address the lack of knowledge among the people, most especially to the youth. <br/><br/>
                                Meanwhile, I am and will always be against the on-going discrimination against LGBTQIA+ individuals. Wherever we go, there will always be hypocrites who’ll trample us along the way. We continue to wait for the SOGIE (Sexual Orientation and Gender Identity and Expression) Equality Bill to be passed into a law, so that we can finally be at ease—hopefully." <br/><br/>
                                <strong>#WeSpeakYourLanguage #OASpeaksWithPride #WorldPride #PrideMonth2019 🏳️‍🌈⁣</strong><br/><br/>


                               </p>


                      </div>



                      <!--Green-->
                      <div class="item text-center" >

                              <h4 class="text-orange" >#ResistTogether <span class="text-primary"> #OASpeaksWithPride</span></h4>
                              <!-- <img src="./storage/uploads/basket-sched.jpg" style="z-index: 2" /> -->
                              <img src="./storage/uploads/lgbt-resist-4.jpg" style="z-index: 2" />
                               <p class="text-left" style="padding-left: 50px;"><br/><br/><strong class="text-danger">'We Speak With Pride' </strong> is a special social series aimed at giving our LGBTQIA+ friends and colleagues a platform to speak their truths on what we must all #ResistTogether for and against.<br/><br/>

                              In light of the community's struggles, <a href="./user/1113" target="_blank">Customer Service Representative Anthony </a>is taking his stand with the community and allies to #ResistTogether for equality and against cruelty and exploitation: "I resist for equality. I believe that LGBTQIA+ rights are human rights, which we’ve been fighting for since the very beginning. We ask for the same freedom and respect, regardless of our gender preference and identity. We are not asking for your sympathy; what we need is understanding. For as long as we breathe the same air and inhabit the same lands, then we are all equal in this life.⁣ <br/><br/>

                              On the other hand, I resist against cruelty and exploitation. Every individual, especially young LGBTQIA+ persons, should be protected from any form of abuse. We all grow up exploring who we want to be, and it gets harder when you're growing up confused. So, they should be given more patience, nurturance, and love. I grew up in a very loving family. I’ve never felt alone because they support me in everything I do. I’d want every single person to have the same support that I got."⁣<br/><br/>
                                <strong>#WeSpeakYourLanguage #OASpeaksWithPride #WorldPride #PrideMonth2019 🏳️‍🌈⁣
⁣
⁣</strong><br/><br/>

                               </p>


                      </div>

                      <!--JAzz-->
                      <div class="item text-center" >

                              <h4 class="text-orange" >#ResistTogether <span class="text-primary"> #OASpeaksWithPride</span></h4>
                              <!-- <img src="./storage/uploads/basket-sched.jpg" style="z-index: 2" /> -->
                              <img src="./storage/uploads/lgbt-resist-3.jpg" style="z-index: 2" />
                               <p class="text-left" style="padding-left: 50px;"><br/><br/><strong class="text-danger">'We Speak With Pride' </strong> is a special social series aimed at giving our LGBTQIA+ friends and colleagues a platform to speak their truths on what we must all #ResistTogether for and against.<br/><br/>

                                <a style="font-weight: bold;" href="./user/2400"> For Prince, a Call Center Associate, </a>it is important that we #ResistTogether to achieve equal rights and acceptance and win against violence and inequality: <br/><br/>
                                "Sexual orientation and gender identity are integral aspects and should never lead to discrimination and abuse. I resist for equal rights to live freely and be accepted—not merely tolerated by the rest of society.<br/><br/>
                                I also stand against violence and inequality. Some think LGBTQIA+ individuals need curing. We face discrimination, bullying, and abuse for being queer from those who think that same sex relationships and being our true selves are sins." <br/><br/>
                                <strong>#WeSpeakYourLanguage #OASpeaksWithPride</strong><br/><br/>

                               </p>


                      </div>

                      <!--JAzz-->
                      <div class="item text-center" >

                              <h4 class="text-orange" >#ResistTogether <span class="text-primary"> #OASpeaksWithPride</span></h4>
                              <!-- <img src="./storage/uploads/basket-sched.jpg" style="z-index: 2" /> -->
                              <img src="./storage/uploads/lgbt-resist-2.jpg" style="z-index: 2" />
                               <p class="text-left" style="padding-left: 50px;"><br/><br/><strong class="text-danger">'We Speak With Pride' </strong> is a special social series aimed at giving our LGBTQIA+ friends and colleagues a platform to speak their truths on what we must all #ResistTogether for and against.<br/><br/>

                                This Pride month, <a class="text-primary" style="font-weight: bold;" target="_blank" href="./user/1374"> Team Leader Jazz </a>speaks his truth to #ResistTogether against bigotry and regression: "I resist against nations and leaders who continue to treat our LGBTQIA+ community as worthless citizens. Despite being progressive nations, their thinking and understanding of us persists to be backward. Instead of helping, protecting, and including their LGBTQIA+ citizens, they exercise their power to make our lives miserable. <br/><br/>

                                We must have leaders who create and implement laws that will benefit everyone to bring the community to furtherance." <br/><br/>
                                <strong>#WeSpeakYourLanguage #OASpeaksWithPride</strong><br/><br/>

                               </p>


                      </div>

                      <div class="item text-center" >

                              <h4 class="text-orange" >#ResistTogether <span class="text-primary"> #OASpeaksWithPride</span></h4>
                              <!-- <img src="./storage/uploads/basket-sched.jpg" style="z-index: 2" /> -->
                              <img src="./storage/uploads/lgbt-resist-1.jpg" style="z-index: 2" />
                               <p class="text-left" style="padding-left: 50px;"><br/><br/><strong class="text-danger">'We Speak With Pride' </strong> is a special social series aimed at giving our LGBTQIA+ friends and colleagues a platform to speak their truths on what we must all #ResistTogether for and against.<br/><br/>

                                For our first feature, <a style="font-weight: bold;" href="./user/320" target="_blank">Lead Generation Analyst, James</a>, shares his stand on HIV awareness: "I advocate for HIV awareness because the LGBTQIA+ community—mostly men—are prone to getting infected with HIV. Having awareness on the virus will lessen the number of infected people and save lives." <br/><br/>
                                <strong>#WeSpeakYourLanguage #OASpeaksWithPride</strong><br/><br/>

                               </p>


                      </div>

<!-- END PRIDE -->

 <div class="item  text-center" >
                              
                              <h4 class="text-orange" >Open Access BPO <span class="text-primary">Ribbon Cutting Ceremony</span></h4>
                              <img src="./storage/uploads/ribbon-cutting.jpg" style="z-index: 2" />
                              <p class="text-left" style="padding-left: 50px;"><br/><br/>Hello Everyone!<br/><br/>
                                We are pleased to inform you that we are going to have our <strong>RIBBON CUTTING CEREMONY </strong>in our G2 office on <strong class="text-danger">July 12, 2019 Friday at 1:30 PM.</strong><br/><br/>
                                All employees who are available at this time are highly encouraged to grace the ceremony. It will be attended by invited bloggers and writers who will cover the event and of course our company executives. It would be great to have you there!<br/><br/></p>

                                <h4 class="text-orange">Celebration/Happy Hour to follow <br/>at Pura Vida, in Poblacion Makati City <br/>from 6:00pm-9:30pm.</h4>

                                <p class="text-left" style="padding-left: 50px;">REMINDERS:<br/><br/>
                                Ribbon Cutting Ceremony<br/>
                                - To all employees attending the ribbon cutting ceremony, please make sure to wear your newly issued blue lanyard and company ID.<br/><br/>

                                Happy Hour<br/>
                                - Kindly <a href="https://docs.google.com/forms/d/e/1FAIpQLSfF9enviYUA3lQEBXQ-4kcLPG4vK9VmVFK3aKRQQ89_EV5T0w/viewform?usp=sf_link" target="_blank" style="font-weight: bold">register here</a> for the party. Deadline for registration is on July 11, 2019 Thursday at 8:00 PM.<br/>
                                - Here's a <a href="https://www.google.com/maps/dir/6780+Ayala+Avenue,+Legazpi+Village,+Makati,+Metro+Manila/Pura+Vida+Manila,+Don+Pedro,+Makati,+Metro+Manila/@14.5600704,121.0224205,15.75z/data=!4m14!4m13!1m5!1m1!1s0x3397c90f2680bdb5:0xd7a0a45a86b37867!2m2!1d121.0193991!2d14.5573936!1m5!1m1!1s0x3397c9aae56ef48f:0x3308361b40ce8f6f!2m2!1d121.0314115!2d14.5639081!3e0" target="_blank">google maps link</a> from Jaka to Pura Vida.<br/>
                              See you all there!</p>
                               
                              <br/>
                    </div> 



<div class="item text-center" >

                              <img src="./public/img/ana.jpg" style="z-index: 2" />

                              <div class="text-gray text-right" style="position: relative;width:90%; right:0px;top:-200px; z-index: 999; height: 20px">
                                 <h2><img src="./public/img/white_logo_small.png" width="40" style="margin-right: 30px" /><br/> <strong>Internal<br/>Hiring! </strong></h2>

                              </div>

                               <h4 style="margin-top: -30px" class="text-danger"><strong>Quality Analyst </strong><br/>
                               <small>for</small> ADVENT
                               <!-- <a target="_blank" href="{{action('CampaignController@show','44')}} "> <img src="./public/img/logo_postmates.png" width="120"></a> -->
                                </h4>

                              <h5 class="text-danger"><i class="fa fa-exclamation-triangle"></i> Deadline of Submission: <strong class="text-primary"> July 19,2019 Friday </strong></h5>
                              <p class="text-left" style="padding-left: 30px; font-weight: bolder;">Requirements: </p>
                              <ul class="text-left">
                                
                                <li>No written warning within the last 3 months</li>
                                <li>Passing QA scores (average) in the last 3 months</li>
                                <li>Recommendation from direct supervisor</li>
                              </ul>

                              <p class="text-left" style="padding-left: 30px; font-weight: bolder;"><br/><br/>Job Description: </p>
                              <ul class="text-left">

                                <li>Randomly audit the required number of tickets per agent per week</li>
                                <li>Analyze markdowns and identify gaps in our communication and process compliance</li>
                                <li>Provide recommendations to improve the overall QA scores</li>
                                <li>Update the QA Score Dashboards</li>
                                <li>Facilitate the QA talk whenever needed (for new waves, and whenever anything changes in the QA form)</li>
                                <li>Function as an SME for incoming waves</li>
                                <li>Facilitate weekly QA Calibration</li>
                                <li>Provide Training Needs Analysis data to the Training Department</li>
                                <li>Perform any other QA ad-hoc tasks whenever needed</li>

                              </ul>
                              <br/><br/>

                               
                              <br/><br/>
                              

                              <p><small>Interested applicants may submit their letter of intent and updated resume to: <a href="mailto:recruitment@openaccessbpo.com">recruitment@openaccessbpo.com</a></small></p>


                      </div>


                      

<div class="item  active  text-center" >
                              
                              <h4 class="text-orange" >Open Access BPO <span class="text-primary">Basketball Team Game 5</span></h4>
                              <img src="./storage/uploads/game3.jpg" style="z-index: 2" />
                              <p class="text-left" style="padding-left: 50px;"><br/><br/>Hi all!
                                <br/><br/>You're all invited to come and watch our Open Access BPO basketball team to Game #5<br/><br/>

                                Date: <strong>July 09, 2019 (Tuesday)</strong><br/>
                                Time: 7:45 PM<br/>
                                Venue: The Zone, Malugay, Makati<br/>
                                Directions from  <a target="_blank" href="https://www.google.com/maps/dir/6780+Ayala+Avenue,+Legazpi+Village,+Makati,+Metro+Manila/The+Zone,+Malugay,+Manila,+Metro+Manila/@14.5586884,121.0173373,16z/data=!3m1!4b1!4m14!4m13!1m5!1m1!1s0x3397c90f2680bdb5:0xd7a0a45a86b37867!2m2!1d121.0193991!2d14.5573936!1m5!1m1!1s0x3397c90833a19f17:0x2cb9a9370a09053e!2m2!1d121.0192647!2d14.5627878!3e0"> 6780 to The Zone</a> <br/><br/>

                                To our Open Access BPO Basketball Team, GOOD LUCK AND MAY YOU BRING HOME THE TROPHY!</p>
                               
                              <br/>
                    </div>

   <div class="item  text-center" >

                              <img src="./storage/uploads/pride2019.jpg" style="z-index: 2" />
                               <p class="text-left" style="padding-left: 50px;"><br/><br/>Open Access BPO’s support for the LGBTQIA+ community goes beyond #PrideMonth and our offices. We stand with the struggles of the community and celebrate your individuality.<br/><br/>

                                To the LGBTQIA+ community, you matter. We salute your truth as we #ResistTogether. Happy Pride!<br/><br/>

                                <strong>#WeSpeakYourLanguage #OASpeaksWithPride</strong></p>


                      </div>


                    


                   
                   
 <!--Stonewall-->
                    <div class="item text-center" >

                              <h4 class="text-orange" > <span class="text-primary"> </span></h4>
                              <!-- <img src="./storage/uploads/basket-sched.jpg" style="z-index: 2" /> -->
                              <img src="./storage/uploads/stonewall.jpg" style="z-index: 2" />
                               <p class="text-left" style="padding-left: 50px;"><br/><br/>In June 1969, the <a href="https://www.openaccessbpo.com/blog/stonewall-50-open-access-bpos-pride-commemoration/" target="_blank" style="font-weight: bold;">Stonewall Riots</a> broke out outside the now iconic Stonewall Inn after patrons stood against police brutality and extortion aimed at the LGBTQIA+ community.<br/><br/>The first #Pride march was held the following year as a commemoration for the brutal riots that further brought light to the plights of the queer and trans community.<br/><br/>
                                Fifty years on, Pride has become more than a celebration, it's a resistance. It's action. It's a stand.<br/><br/>
                                <strong>#OASpeaksWithPride #WeSpeakYourLanguage #Stonewall50 #ResistTogether</strong><br/><br/>



                               </p>


                      </div>

                    <div class="item  text-center" >
                              
                              <h4 class="text-orange" >Congratulations <span class="text-primary"> Open Access Basketball Team!!!</span></h4>
                              <img src="./storage/uploads/game3.jpg" style="z-index: 2" />
                              <p class="text-left" style="padding-left: 50px;"><br/><br/> for winning <strong> Game 3</strong> last night, with Final score: 78-43<br/><br/>
                                <strong class="text-danger">Game 4 schedule: </strong><br/><br/>
                                Date: July 3, 2019 (Wednesday)<br/>
                                Time: 8:45 PM<br/>
                                Venue: The Zone, Malugay, Makati<br/>
                                Directions from 6780: <a target="_blank" href="https://www.google.com/maps/dir/6780+Ayala+Avenue,+Legazpi+Village,+Makati,+Metro+Manila/The+Zone,+Malugay,+Manila,+Metro+Manila/@14.5586884,121.0173373,16z/data=!3m1!4b1!4m14!4m13!1m5!1m1!1s0x3397c90f2680bdb5:0xd7a0a45a86b37867!2m2!1d121.0193991!2d14.5573936!1m5!1m1!1s0x3397c90833a19f17:0x2cb9a9370a09053e!2m2!1d121.0192647!2d14.5627878!3e0"> 6780 to The Zone</a> </p>
                               

                              <br/>
                    </div> 

<div class="item text-center" >

                              <h4 class="text-orange" >Happy <span class="text-primary"> Father's Day!!!</span></h4>
                              <img src="./storage/uploads/fathersday2019.jpg" style="z-index: 2" />
                               <p class="text-left" style="padding-left: 50px;"><br/><br/>To the most amazing man in the world, Happy Father's Day! 👨 May this day be like no other as you are to your family. <br/><br/>

                                <strong>#WeSpeakYourLanguage #OAonDadsDay</strong></p>


                      </div>

                     

                     <!--  <div class="item  text-center" >
                              
                              <h4 class="text-orange" >Congratulations <span class="text-primary"> Open Access Basketball Team!!!</span></h4>
                              <img src="./storage/uploads/basket-congrats.jpg" style="z-index: 2" />
                              <p class="text-left" style="padding-left: 50px;"><br/><br/>for winning their first game, with Final score: 85-83<br/><br/>Details of the next game will be sent soon. Hope to see you there!</p>
                               

                              <br/>
                    </div> -->




                      <div class="item  text-center" >
                              
                              <h4 class="text-orange" >June 12 <span class="text-primary">Araw ng Kalayaan</span></h4>
                              <img src="./storage/uploads/philindependence.jpg" style="z-index: 2" />
                              <p class="text-left" style="padding-left: 50px;"><br/><br/>Today, we join the Filipino community in celebrating the 121st anniversary of the Philippine Independence Day.<br/><br/>#WeSpeakYourLanguage #OAonIndependenceDay </p>
                               

                              <br/>
                    </div>

<div class="item text-center" >

                              <h4 class="text-orange" >Game 3 | <span class="text-primary"> Open Access BPO Basketball Team</span></h4>
                              <!-- <img src="./storage/uploads/basket-sched.jpg" style="z-index: 2" /> -->
                              <img src="./storage/uploads/basket-congrats.jpg" style="z-index: 2" />
                               <p class="text-left" style="padding-left: 50px;"><br/><br/>Everyone is invited to come and watch their second win for the league:<br/><br/>

                                Date: <strong class='text-danger'>June 25, 2019 (Tuesday)</strong><br/>
                                Time: 8:45 PM<br/>
                                Venue: The Zone, Malugay, Makati<br/>
                                Directions from 6780: <a target="_blank" href="https://www.google.com/maps/dir/6780+Ayala+Avenue,+Legazpi+Village,+Makati,+Metro+Manila/The+Zone,+Malugay,+Manila,+Metro+Manila/@14.5586884,121.0173373,16z/data=!3m1!4b1!4m14!4m13!1m5!1m1!1s0x3397c90f2680bdb5:0xd7a0a45a86b37867!2m2!1d121.0193991!2d14.5573936!1m5!1m1!1s0x3397c90833a19f17:0x2cb9a9370a09053e!2m2!1d121.0192647!2d14.5627878!3e0"> 6780 to The Zone</a><br/>

                                To our Open Access BPO Basketball Team, GOOD LUCK AND WIN GAME 2!<br/><br/>

                                Hope to see you there! <br/><br/>

                               </p>


                      </div>

<div class="item active text-center" >

                              <img src="./public/img/ana.jpg" style="z-index: 2" />

                              <div class="text-gray text-right" style="position: relative;width:90%; right:0px;top:-200px; z-index: 999; height: 20px">
                                 <h2><img src="./public/img/white_logo_small.png" width="40" style="margin-right: 30px" /><br/> <strong>Internal<br/>Hiring! </strong></h2>

                              </div>

                               <h2 style="margin-top: -30px" class="text-danger"><strong>Quality Analyst </strong><br/>
                               </h2>

                              <h5 class="text-danger"><i class="fa fa-exclamation-triangle"></i> Deadline of Submission: <strong class="text-primary"> June 15,2019 </strong></h5>
                              <p class="text-left" style="padding-left: 30px; font-weight: bolder;">Qualifications: </p>
                              <ul class="text-left">

                                <li>No attendance issues / DAs in the last 3 months</li>
                                <li>Passing QA scores (average) in the last 3 months</li>
                                <li>Passing CSAT scores (average) in the last 3 months</li>
                                <li>Knowledge in statistics preferred but not required</li>
                                <li>Basic knowledge in Microsoft Office</li>

                              </ul>
                              <br/><br/>

                               <p class="text-left" style="padding-left: 30px; font-weight: bolder;">Responsibilities: </p>
                              <ul class="text-left">

                                <li>Randomly audit tickets per agent per week</li>
                                <li>Analyze markdowns and identify gaps in our communication and process compliance</li>
                                <li>Provide recommendations to improve the overall QA and CSAT scores</li>
                                <li>Coach agents on their QA performance</li>
                                <li>Track each agent's QA score progress</li>
                                <li>Update the QA Socre Dashboards</li>
                                <li>Facilitate the QA Talk whenever needed (for new waves, and whenever anything changes in the QA form)</li>
                                <li>Functions as an SME for incoming waves</li>
                                <li>Facilitate weekly QA Calibration</li>
                                <li>Provide Training Needs Analysis data to the Training Department (by request)</li>
                                <li>Perform any other QA ad hoc tasks whenever needed</li>

                              </ul>
                              <br/><br/>
                              

                              <p><small>Interested applicants may submit their letter of intent and updated resume to: <a href="mailto:recruitment@openaccessmarketing.com">recruitment@openaccessbpo.com</a></small></p>


                      </div>

<!-- PRIDE MONTH -->
<div class="item  text-center" >
                              
                              <h4 class="text-primary">Happy <span class="text-orange">Pride Month!</span></h4>
                              <img src="./storage/uploads/pride.jpg" style="z-index: 2" />
                              <p class="text-left" style="padding-left: 50px;"><br/><br/>Hello Open Access Family!<br/><br/>
                                <strong>June is Pride month!</strong> Last year, Open Access BPO donated to the Metro Manila Pride organization and released a <a href="http://172.17.0.2/coffeebreak/wp-content/uploads/2019/05/pridemonth.mp4" target="_blank">Pride Month video</a> featuring some of our LGBTQI+ co-workers.<br/><br/>
                                As a company that <strong>believes in inclusivity</strong>, we wish to continue our advocacy for the community. We are looking into the possibility of gathering those of you who’d love to be part at this year’s Pride March!<br/><br/>
                                If you are part of the LGBTQI+ community, and would like to join the Pride March with Open Access BPO, click on the sign up button below.This year's Pride march will be held on<br/> <strong class="text-danger">June 29, 2019 at the Marikina Sports Center.</strong><br/><br/>Transportation will be provided. If this will also be in conflict with your work schedule, we will try our best to adjust it accordingly<br/><br/>
                                <a href="https://docs.google.com/forms/d/e/1FAIpQLSf7XraczpwOkB-EdzJhavLbRZynAwiDTD-JwyWbTj1iyLtQNg/viewform" class="btn btn-danger btn-md text-center" target="_blank">Sign Up Here</a><br/><br/>
                                Deadline to sign up is until <strong>Monday, May 20, 2019.</strong>Thank you for spreading the love! We are one with the LGBTQI+ community as we "Resist Together".<br/>

                              <br/>
                    </div>

<!-- happiness -->

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

                      

 <!--WOMEN-->
                    <div class="item  text-center" >
                        <img src="./storage/uploads/women1.jpg" style="z-index: 2" width="100%" /><br/><br/>
                        <p style="padding:10px 50px">Get to know @openaccessbpo's amazing female leaders and teammates and learn from their stories in this year's #WomenOfOA project! ✨<br/>
                          <strong class="text-primary">#WeSpeakYourLanguage #OAonWomensHistoryMonth #WomenOfOA #IWD2019 #BalanceForBetter</strong> </p>

                          <div style="padding:10px; position: absolute;bottom: 0px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                    <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAonWomensHistoryMonth #WomenOfOA #IWD2019 #BalanceForBetter</small></a></div> <br/><br/><br/><br/>

                    </div>

                    <div class="item  text-center" >
                        <img src="./storage/uploads/women-emelda.jpg" style="z-index: 2" width="100%" /><br/><br/>
                        <p style="padding:10px 50px">Be grateful to what you have today and every little thing that will come your way— it’s one way to accomplish what you seek.<br/><br/>

                        Our fifth <strong>#WomenOfOA</strong> feature goes to one of Open Access BPO’s inspiring female leaders, <a href="{{action('UserController@show',1611)}}" target="_blank" > <strong>Emelda Perez</strong>, </a>(or Ms. E as everyone fondly calls her). In this feature, she talks about her belief system that she finds helpful in her role as an HR Director.<br/><br/>

                        "[I believe] what we learned from home can be applied wherever we are, especially at work. First, hard work in everything we do. With my role here at Open Access, I try to always give my best because I believe that’s how my staff learns from me. As they always say, to be a good leader you have to be a good follower also. Second is sincerity in how you treat people. It’s difficult to earn someone’s trust if you’re not sincere."<br/><br/>

                        "Third is flexibility, in terms of how I manage my time at work and at home. Whenever I’m needed at work, I focus and give my 100% attention. [Because] I know that like me, they too, have their own priorities and that their time is important. There are plenty of unexpected meetings, and so you need to learn how to manage your time; distinguish what’s important from urgent and vice versa. Focus and attention, so you can always give your best. The same thing goes for my family, I give them my undivided attention because that’s what they deserve. Time is important, no matter who you give it to."<br/><br/>

                        "Passion. I believe in passion for purpose. Because if you’re passionate in what you do, you can do great things. More so, having defined purpose allows us to understand what we’re capable of giving and whether we're capable of doing more."<br/><br/>

                        "Another thing is gratitude. You see, plenty of things do happen unexpectedly at work that may disappoint us. What do I do? I try to see the good in the bad. Oprah writes the things she’s thankful every day, and that helps her overcome the obstacles in her life. I do that also, and it helps me look ahead with positivity. Bad things happen, but they also end. We need to be positive in order to overcome the struggles we encounter in life. Of course, that includes our faith. Every day, before I go to work, I pray. Because I know I can do all things with Him. At the end of the day, I’m only human and I get tired and exhausted. What helps me keep up with life’s challenges? I surrender everything to God."
                        <br/><br/>
                        "Next, empathy. How we treat people describes who we [really] are. I believe that if you care for people, they’ll care for you as well. But if they don’t, then that’s okay. At least you sow, and you’ll never have to regret your actions. You can’t please everyone.<br/><br/>

                        Also, I admire people who stay grounded no matter what position or status they hold. That’s humility. Because whatever you do, you need to treat people with respect. Open Access is a multinational company. We have different people coming in the HR Department every day, these people have different beliefs, personalities, and preferences, and so we, [our team], make sure we exercise this value most."<br/><br/>

                        As she ends her answer, she adds, "The same goes here in the company, we have our own set of core values that we impart to the employees. That makes us work in harmony and at our best selves."<br/><br/>

                        "Values are things that we can apply anywhere, anytime. We apply them to influence good on others, so we don’t regret on whatever mark we may leave as we go forward. At the end of the day, what matters is that you don’t look back and say, 'Sayang!'"<br/><br/>
                          <strong class="text-primary">#WeSpeakYourLanguage #OAonWomensHistoryMonth #WomenOfOA #IWD2019  </strong></p>

                          <div style="padding:10px; position: absolute;bottom: 0px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                    <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAonWomensHistoryMonth #WomenOfOA #IWD2019 </small></a></div> <br/><br/><br/><br/>

                    </div>

                    <div class="item  text-center" >
                        <img src="./storage/uploads/women-jimella.jpg" style="z-index: 2" width="100%" /><br/><br/>
                        <p style="padding:10px 50px">Jimella is a hands-on mom, a full-time Content Writer, a loving wife, and a great friend. In spite of how overwhelming life can be and everything that comes with it, she manages to give her relationships with undivided attention and still leaves time for herself.<br/><br/>

                        For our fourth <strong>#WomenOfOA</strong> feature <a href="{{action('UserController@show',443)}}" target="_blank" > Jimella,</a> every woman can and here's the secret. "Personally, I just make sure to manage my time well so that I can tend to my family's needs. We don't have a helper so I'm pretty much a hands-on mom to my kids— I am their personal cook, teacher, nurse, hairdresser, and stylist. Every so often, we go on an out-of-town trip to let them explore and get in touch with nature. I think it's always a good idea to spend as much time with them because they grow up so fast! However, as much as I love my children, I also believe that every once in a while, my hubby and I deserve to have a date night [which we religiously follow]. Just a few hours of peace and quiet is enough to keep us sane." Along with being devoted to her family, she reminds women like her to have time for themselves as well. "Another important thing: it's essential to reward yourself a 'Me time.' Pamper yourself, go out with friends, or do things that you always want to try. Have a break from everything so that you can recharge and revitalize. That way, you can always offer a better version of yourself!" These can still be overwhelming for many, especially to those who are new to motherhood, but she believes every woman can bring something to the table to make it all work. "[For me,] every woman is a great multi-tasker. We have an extraordinary skill to juggle a lot of things but still can manage to accomplish greatly. We are loaded with responsibilities yet we always handle it with grace. No matter how challenging life gets, expect a woman to come out stronger!" #WeSpeakYourLanguage #OAonWomensHistoryMonth<br/><br/>
                          <strong class="text-primary">#WeSpeakYourLanguage #OAonWomensHistoryMonth #WomenOfOA #IWD2019  </strong></p>

                          <div style="padding:10px; position: absolute;bottom: 0px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                    <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAonWomensHistoryMonth #WomenOfOA #IWD2019 </small></a></div> <br/><br/><br/><br/>

                    </div>

                    
                   

                    <!--lizelle -->
                    <div class="item  text-center" >
                        <img src="./storage/uploads/women-lizelle.jpg" style="z-index: 2" width="100%" /><br/><br/>
                        <p style="padding:10px 50px">Do what makes you happy and be unstoppable! 
                          In our third feature, <strong>#WomenOfOA project</strong> presents <a href="{{action('UserController@show',90)}}" target="_blank" >  Lizelle Barboza:</a> an inspiring thrill-seeker who has the strength and determination to conquer trails. Her love for trekking through rough and treacherous terrains contributes a lot to who she is today.<br/><br/>

                          But we all know how daunting the road to success can be, and Lizelle has her fair share of similar experiences. "There is an event race that I joined with only few women participants [and] like them, I came prepared. However, I am aware that in an event race where you have to trek 6 mountains in 15 hours, the possibility of men winning is so much higher. So, I was just happy to finish the race." "Halfway the course, I almost felt like quitting because of the heat, jelly legs and exhaustion. I felt very weak, I pushed it a little at a time thinking not to waste my month-long training program for nothing. I took a stand and challenged myself to run pass every single runner as I can. This, eventually, helped me reach a pack of male runners [ahead]. That's when I realized I still have the energy to keep pace with them; that I can still push myself further to the finish line like them." She then recalled what kept her going to advance toward the finish line and complete the race, "Strength, focus, faith in myself and their encouragement— that's what I had at that moment that helped me believe I can make it too like the male runners, despite how tired and exhausted we were. We reached the finish line together and surprisingly, I completed the race as the 4th female runner!" What lesson did she learn from this experience? She happily shared, "Learning not to quit and having faith in my own strength are just some of the lessons I've learned from [trail running]. We all know women can do wonders like men and that nobody can stop us but ourselves. There's no superior nor inferior and no gender [bias] when it comes to keeping fit and leading an active lifestyle. Now, I am more confident of myself."<br/><br/>
                          <strong class="text-primary">#WeSpeakYourLanguage #OAonWomensHistoryMonth #WomenOfOA #IWD2019  </strong></p>

                          <div style="padding:10px; position: absolute;bottom: 0px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                    <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAonWomensHistoryMonth #WomenOfOA #IWD2019 </small></a></div> <br/><br/><br/><br/>

                    </div>

                    <!-- LISA -->
                    <div class="item  text-center" >
                        <img src="./storage/uploads/women-lisa.jpg" style="z-index: 2" width="100%" /><br/><br/>
                        <p style="padding:10px 50px">Maintaining a balanced life between one's career, family, and yourself can be challenging. But with the right mindset, it can be possible.<br/><br/>
                        

                        Today, we're putting the <strong>#WomenOfOA</strong> spotlight to our <a href="{{action('UserController@show',344)}}" target="_blank" >  Chief Financial Officer (CFO), <strong>Lisa Jackson-Machalski</strong></a>. 
                        Juggling her day to day responsibilities as a mother and as a career woman can make things challenging for her. But being the super woman that she is, she makes it a point to always give her best shot in all of the important aspects of her life— family, career, and herself. "My biggest struggle has been maintaining the balance between my personal and professional life. In order to compete and stay on top of the game, you need to be direct and assertive. It is often difficult to switch out of this mode when dealing with your partner and children. My daughter often told me growing up "You treat me like an employee." Fortunately now, she thanks me for that as she has become a very hardworking young woman with a great work ethic."<br/><br/> As she takes on another chapter in life, she shares, "Men are from Mars and women are from Venus does still ring very true when it comes to relationships. As progressive as you might think males can be, there is often that power struggle and competitiveness that you don't necessarily expect as it is often inherently wired in many males. My belief system, morals and ethics are more conservative in my personal life as I believe in a certain level of chivalry and males and females having pre-determined defined responsibilities. It can be very challenging to change out of your business woman persona into a wife/mother persona. [As to] How did I overcome it? Try, try, try again is the only way. After 3 failed marriages I finally feel that with my current husband I have gotten it right and couldn't be happier!" <br/>
                          <strong class="text-primary">#WeSpeakYourLanguage #OAonWomensHistoryMonth #WomenOfOA #IWD2019 #BalanceForBetter </strong></p>

                          <div style="padding:10px; position: absolute;bottom: 0px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                    <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAonWomensHistoryMonth #WomenOfOA #IWD2019 #BalanceForBetter</small></a></div> <br/><br/><br/><br/>

                    </div>

                    <!-- ANAIS -->
                     <div class="item  text-center" >
                        <img src="./storage/uploads/women2.jpg" style="z-index: 2" width="100%" /><br/><br/>
                        <p style="padding:10px 50px">Discrimination is a major problem across the globe. For anyone who's been through it or is going through it, you should know that you're more than what they tell you and that this shouldn't limit you from what you can do and who you can be. <br/><br/>
                        Our first feature for this year's <strong>#WomenOfOA</strong> is our French CSR, <strong><a href="{{action('UserController@show',2625)}}" target="_blank" > Anais Andriatsivalaka</a></strong> . Anais is a native of Madagascar who has left home to pursue her career. She has moved to different countries in Africa, Europe, and now, Asia. In doing so, she has experienced the difficulties of being out of her comfort zone and being subjected to discrimination, sexism, and violence. With this experience, she shares a life lesson that may help people who's going through the same. "It is not easy just to leave home and be on your own especially when you are a woman moreover black: it may happen that you encounter discrimination, sexism and even violence. It is important not to take it personally and to remember that though it may be pure hatred, most of the time it is just linked to cultural gaps, and also a lack of knowledge and education." Along with her bubbly personality, Anais possesses the quality of a strong woman, and her wisdom attests to that. "Today, I feel grateful that I am still standing, able to discover other cultures, and bring all this experience to support my relatives and hopefully, as an asset to the company I join. [So,] should you decide to risk it (YES YOU SHOULD), do not to forget that wherever you go, no matter what people tell you or how much they try to put you down, you are here as a person and you have to fight— not against people but to try to be the best version of yourself because this is how you will stand out.” #WeSpeakYourLanguage #OAonWomensHistoryMonth<br/>
                          <strong class="text-primary">#WeSpeakYourLanguage #OAonWomensHistoryMonth #WomenOfOA #IWD2019 #BalanceForBetter </strong></p>

                          <div style="padding:10px; position: absolute;bottom: 0px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                    <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAonWomensHistoryMonth #WomenOfOA #IWD2019 #BalanceForBetter</small></a></div> <br/><br/><br/><br/>

                    </div>

<div class="item  active  text-center" >
                              
                              <h4 class="text-orange" >Open Access BPO <span class="text-primary">Basketball Team's 1st Game</span></h4>
                              <img src="./storage/uploads/basket.jpg" style="z-index: 2" />
                              <p class="text-left" style="padding-left: 50px;"><br/><br/>Hi all!
                                <br/><br/>Our Open Access BPO Basketball Team has joined the Information Technology Basketball League and they are in need of supporters like you!
                                <br/><br/>Everyone is invited to come and watch their first game for the league:<br/><br/>

                                Date: <strong>June 11, 2019 (Tuesday)</strong><br/>
                                Time: 8:45 PM<br/>
                                Venue: The Zone, Malugay, Makati<br/>
                                Directions from 6780: 6780 to The Zone<br/><br/>

                                To our Open Access BPO Basketball Team, GOOD LUCK AND MAY YOU BRING HOME THE TROPHY!</p>
                               

                              <br/>
                    </div>

 <div class="item text-center" >
                      <h4 class="text-primary">Flu Vaccines</h4>
                              <img src="./storage/uploads/wellness10.jpg" style="z-index: 2" />
                               <p class="text-left" style="padding-left: 50px;"><br/><br/>We are glad that many are interested in getting their flu shots. In line with the program, we are now open for registration for those who would like to <strong class="text-orange">avail the flu vaccines via Salary Deduction.</strong> This is open as well to dependents. <br/><br/>
                                To register, kindly drop by the clinic (8th floor Jaka BLDG & 11th floor ADP G2 BLDG) <strong>not later than June 7, 2019 (Friday) </strong>during your break hours, terms will be discussed and there are forms that need to be filled out and signed.</p>


                               <div style="padding:10px; position: absolute;bottom: 0px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                    <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAonWellness</small></a></div> <br/><br/><br/><br/>          

                      </div>

<!-- MOTHERS DAY CINCO DE MAYO -->
                    <div class="item  text-center" >
                              
                              <img src="./storage/uploads/mothersday.jpg" style="z-index: 2" />
                              <br/><h4 class="text-primary">Happy <span class="text-orange">Mother's Day</span></h4>
                    </div>

                    <div class="item text-center" >
                              <h4 class="text-primary">Happy <span class="text-orange">Cinco de Mayo!</span></h4>
                              <img src="./storage/uploads/cinco-3.jpg" style="z-index: 2" />

                              
                              <p class="text-center" style="padding-left: 30px;"><br/><br/>More party pics in our <a href="{{ action('HomeController@gallery',['a'=>9]) }}">Cinco de Mayo Gallery</a></strong><br/><br/>

                                </p>


                    </div>

      <!--physical schel-->
                    <div class="item text-center" >
                        
                         <p style="padding: 5px 30px; margin-bottom: 0px"><h3 class="text-orange"><span style="font-size: smaller;" class="text-primary"> Zumba Free Class</span> New Schedule</h3>
                         <br/><br/>

                          <strong style="font-size:larger"><span class="text-primary"> Tuesday:</span> 
                            <br/><span class="text-danger">May 28, 2019 (Tue) – 7:30 PM</span><br/></strong><br/>

                          <strong style="font-size:larger"><span class="text-primary"> Thursday:</span> 
                            <BR/><span class="text-danger">May 30, 2019 (Thu) – 7:00 PM</span><br/></strong><br/> 

                            <a class="btn btn-danger" href="https://docs.google.com/forms/d/e/1FAIpQLSeZsdfWT5UvVOWnWmEGG9uAhmfLLVyK1yHyJ9U-wP7KciTsPQ/viewform?usp=sf_link" target="_blank">
                              Sign Up Now</a><br/><br/>

                             <img src="./storage/uploads/physical_poster.jpg" style="z-index: 2" /><br/>

                          <br/><br/>
                           <strong>Venue:</strong><br/>

                            OPEN ACCESS BPO G2 OFFICE<br/>

                            11F Glorietta 2 Corporate Center,<br/>

                            West. St., Ayala Center, Makati City<br/><br/>

                            <img src="./storage/uploads/oam-location.jpg" style="z-index: 2" width="100%" />
                            <br/><br/>

                              View our <a class="text-danger" href="./gallery?a=5"><i class="fa fa-picture-o"></i> Gallery</a> for more<br/> #WeSpeakYourLanguage #LetsGetPhysical #OAforWellness

                           
                            <div style="padding:10px; position: absolute;bottom: 0px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                  <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAforWellness #LetsGetPhysical #Zumba</small></a></div> <br/><br/><br/><br/>
                    </div>


                    

                    <div class="item text-center" > <img src="./storage/uploads/summer-care-1.jpg" style="z-index: 2" /></div>
                    <div class="item text-center" > <img src="./storage/uploads/summer-care-2.jpg" style="z-index: 2" /></div>
                    <div class="item text-center" > <img src="./storage/uploads/summer-care-3.jpg" style="z-index: 2" /></div>


                      

                      
                      

                     



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



                      <div class="item text-center" >
                        
                         <img src="./storage/uploads/teams2.jpg" style="z-index: 2" width="100%" /><br/><br/>
                         

                          <p style="padding: 5px 30px; margin-bottom: 0px;" class="text-left">
                          Get to know more about your <a href="{{action('CampaignController@index')}}"><strong>Open Access family!</strong></a><br/><br/> Check out our <a class="text-orange" href="{{action('CampaignController@index')}}">programs</a> and learn more about the driving force behind our campaigns.</p>

                           <p style="padding: 5px 30px; margin-bottom: 0px; font-size: x-small;" class="text-left"><br/><br/>
                            <i class="fa fa-info-circle text-primary"></i> Tip: Make sure the page you're viewing is updated. Press <strong>CTRL + Shift + R </strong> to do a hard refresh and clear your browser's cache.
                          </p>

                          
                      </div>

                     
                      



                      <div class="item  text-center" >
                        
                         <img src="./storage/uploads/teams3.jpg" style="z-index: 2" width="100%" /><br/><br/>
                         

                          <p style="padding: 5px 30px; margin-bottom: 0px;" class="text-left">
                          Get to know more about your <a href="{{action('CampaignController@index')}}"><strong>Open Access family!</strong></a><br/><br/> Check out our <a class="text-orange" href="{{action('CampaignController@index')}}">programs</a> and learn more about the driving force behind our campaigns.</p>

                           <p style="padding: 5px 30px; margin-bottom: 0px; font-size: x-small;" class="text-left"><br/><br/>
                            <i class="fa fa-info-circle text-primary"></i> Tip: Make sure the page you're viewing is updated. Press <strong>CTRL + Shift + R </strong> to do a hard refresh and clear your browser's cache.
                          </p>

                          
                      </div>

                            <div class="item active text-center" >

                              <img src="./storage/uploads/cinco-de-mayo.jpg" style="z-index: 2" />

                              
                              <p class="text-center" style="padding-left: 30px;"><br/><br/>To kick things off for May, we're celebrating <br/><strong class="text-orange" style="font-size: larger;">Cinco de Mayo</strong> as a family!<br/>
                                <br/>Join us as we welcome Cinco de Mayo on <br/><strong class="text-primary" style="font-size: larger"> Saturday, May 4, 2019 at <br/>The Ruins in Poblacion, Makati</strong>. <br/>There will be live music, snacks and drinks for all to enjoy!<br/><br/>

                                Deadline of registration is on Monday, April 29, 2019 at exactly 1:00 PM. <a href="https://docs.google.com/forms/d/e/1FAIpQLSfkqfhur-XX9uJ4p-a76cqTTexf6KFfLUeCpMFELdziXnRYng/viewform?usp=sf_link" class="text-danger" style="font-weight: bold">Register here </a>now as there are limited slots! See you there!</p>


                      </div>

                             <div class="item text-center" >
                        <img src="./storage/uploads/food1.jpg" style="z-index: 2" width="100%" /><br/>
                        <p style="padding:10px 50px">Delicious, affordable meals now available in our <br/><strong class="text-primary" style="font-size: larger;">5F pantry (right wing)</strong><br/>
                         </p><img src="./storage/uploads/food2.jpg" style="z-index: 2" width="100%" /><br/><br/><br/><br/>

                    </div>

                             <div class="item text-center" >

                              <img src="./public/img/ana.jpg" style="z-index: 2" />

                              <div class="text-gray text-right" style="position: relative;width:90%; right:0px;top:-200px; z-index: 999; height: 20px">
                                 <h2><img src="./public/img/white_logo_small.png" width="40" style="margin-right: 30px" /><br/> <strong>Internal<br/>Hiring! </strong></h2>

                              </div>

                               <h2 style="margin-top: -30px" class="text-danger"><strong>Training Apprentice </strong><br/>
                               <small>for</small> <a target="_blank" href="{{action('CampaignController@show','23')}} "> <img src="./public/img/oam_favicon1-55027f4ev1_site_icon-256x256.png" width="30"><span style="font-size: smaller;">Training Dept.</span></a></h2>

                              <h5 class="text-danger"><i class="fa fa-exclamation-triangle"></i> Deadline of Submission: <strong class="text-primary"> April 26,2019 </strong></h5>
                              <p class="text-left" style="padding-left: 30px; font-weight: bolder;">Requirements: </p>
                              <ul class="text-left">
                                <li>Excellent written and verbal English skills</li>
                                <li>Open to all who have worked at least 6 months in Open Access</li>
                                <li>No attendance issues</li>
                                <li>No written warning</li>
                                <li>Exemplary Performance</li>
                              </ul>
                              <br/><br/>
                              <ul class="text-left"><strong>For the demo:</strong><br/>
                                <li>the candidate needs to prepare a 10-minute presentation in Google Slide</li>
                                <li>the candidate can choose a topic from the list below:<br/>
                                  - A Day in the Life of a Call Center Agent<br/>
                                  - An Effective Customer Service Representative<br/>
                                  - Effective Communication Skills for the Workplace<br/></li><br/>
                                <li>the candidate should send the demo material to <strong>marcriol@openaccessbpo.com</strong> before the presentation schedule</li>
                                <li>The interview will happen after the demo</li>
                              </ul>

                              <p><small>Interested applicants may submit their letter of intent and updated resume to: <a href="mailto:recruitment@openaccessmarketing.com">recruitment@openaccessbpo.com</a></small></p>


                      </div>

                            <div class="item text-center" >
                        <h4 class="text-danger">ALL OPEN ACCESS G2 Employees: </h4><h5>
                        Please be guided of the following schedule for picture taking for our new company ID</h5>
                        <img src="./storage/uploads/g2.jpg" style="z-index: 2" width="100%" />
                        <h4><strong>Venue: </strong>11th Floor (Beside Recruitment Area), <br/>Glorietta 2, Corporate Center,<br/> Ayala Center, Makati City<br/><br/>
                          Attire: <strong>STRICTLY PLAIN BLACK TOP</strong> <br/>(No prints, No design, No sleeveless)</h4><br/>
                          <h5>For those who don't have plain black shirts, there are polo shirts provided by HR that you may use.</h5>

                        <table class="table">
                          <tr>
                            <th>Program/Department</th>
                            <th>Date</th>
                            <th>Morning</th>
                            <th>Night</th>
                          </tr>
                          <tr>
                            <td rowspan="6">AvaWomen, <br/>HR-Recruitment, <br/>Quora, <br/>UiPath, <br/>WorldVentures</td>
                            <td>April 10, 2019</td>
                            <td>08:00am-6:00pm</td>
                            <td>6:00pm-6:00am</td>
                          </tr>
                          <tr>
                            
                            <td>April 11, 2019</td>
                            <td>06:00am-06:00pm</td>
                            <td>6:00pm-6:00am</td>
                          </tr>
                          <tr>
                            
                            <td>April 12, 2019</td>
                            <td>06:00am-06:00pm</td>
                            <td>6:00pm-6:00am</td>
                          </tr>
                          <tr>
                            
                            <td>April 15, 2019</td>
                            <td>08:00am-06:00pm</td>
                            <td>6:00pm-6:00am</td>
                          </tr>
                          <tr>
                            
                            <td>April 16, 2019</td>
                            <td>06:00am-06:00pm</td>
                            <td>6:00pm-6:00am</td>
                          </tr>
                          <tr>
                           
                            <td>April 17, 2019</td>
                            <td>06:00am-06:00pm</td>
                            <td>6:00pm-6:00am</td>
                          </tr>
                          
      



                        </table>

                      </div>


                             <div class="item  text-center" >

                              <img src="./public/img/ana.jpg" style="z-index: 2" />

                              <div class="text-gray text-right" style="position: relative;width:90%; right:0px;top:-200px; z-index: 999; height: 20px">
                                 <h2><img src="./public/img/white_logo_small.png" width="40" style="margin-right: 30px" /><br/> <strong>Internal<br/>Hiring! </strong></h2>

                              </div>

                               <h2 style="margin-top: -30px" class="text-danger"><strong>Real-Time Analyst </strong><br/>
                               </h2>

                              <h5 class="text-danger"><i class="fa fa-exclamation-triangle"></i> Deadline of Submission: <strong class="text-primary"> April 17,2019 </strong></h5>
                              <p class="text-left" style="padding-left: 30px; font-weight: bolder;">Requirements: </p>
                              <ul class="text-left">
                                
                                <li>Excellent written and verbal English skills</li>
                                <li>Worked at least 3months in Open Access</li>
                                <li>No written warning</li>
                                <li>Exemplary Performance</li>
                              </ul>
                              <br/><br/>

                              <p><small>Interested applicants may submit their letter of intent and updated resume to: <a href="mailto:recruitment@openaccessmarketing.com">recruitment@openaccessbpo.com</a></small></p>


                      </div>


                            <div class="item active text-center" >
                        <h4 class="text-primary">Wear your Pajama To Work Day!!! </h4>
                        <img src="./storage/uploads/pajama.jpg" style="z-index: 2" />
                        <p style="padding: 5px 30px; margin-bottom: 0px">Join the fun and wear your favorite sleepwear to work on <strong class="text-danger">Wednesday April 16,2019 </strong>.<br/><br/>
                        You may also bring your favorite pillows, stuffed toy, slippers, or blanket. Take selfies and photos with your team and office friends and share them on social media. <br/><br/>

                        E-mail us your photos: <strong>marketing@openaccessmarketing.com</strong> and we'll post them on our official Facebook and Instagram pages.<br/><br/>



                        Don't forget to tag <strong>@OpenAccessBPO</strong> and use the hashtags <strong>#WeSpeakYourLanguage #JPDayAtOA #WearYourPajamaToWorkDay</strong> 

                         <h5 class="text-primary">Reminder: Revealing sleepwear are not allowed, so make sure that what you're wearing is still within our company's dress code guidelines.</h5>


                      </p>

                      </div>



                    <div class="item  text-center" >

                              <img src="./public/img/ana.jpg" style="z-index: 2" />

                              <div class="text-gray text-right" style="position: relative;width:90%; right:0px;top:-200px; z-index: 999; height: 20px">
                                 <h2><img src="./public/img/white_logo_small.png" width="40" style="margin-right: 30px" /><br/> <strong>Internal<br/>Hiring! </strong></h2>

                              </div>

                               <h2 style="margin-top: -30px" class="text-danger"><strong>Training Apprentice </strong><br/>
                               <small>for</small> <a target="_blank" href="{{action('CampaignController@show','32')}} "> <img src="./public/img/logo_circleslife.png" width="180"></a></h2>

                              <h5 class="text-danger"><i class="fa fa-exclamation-triangle"></i> Deadline of Submission: <strong class="text-primary"> April 12,2019 </strong></h5>
                              <p class="text-left" style="padding-left: 30px; font-weight: bolder;">Requirements: </p>
                              <ul class="text-left">
                                <li>open <strong>ONLY to all Circles.Life reps</strong></li>
                                <li>Excellent written and verbal English skills</li>
                                <li>Worked at least 3months in Open Access</li>
                                <li>No written warning</li>
                                <li>Exemplary Performance</li>
                              </ul>
                              <br/><br/>
                              <ul class="text-left"><strong>For the demo:</strong>
                                <li>the candidate needs to prepare a 10-minute presentation in Google Slide</li>
                                <li>the candidate can choose any CL-related topic</li>
                                <li>the candidate should send the demo material before the presentation schedule</li>
                                <li>The interview will happen after the demo</li>
                              </ul>

                              <p><small>Interested applicants may submit their letter of intent and updated resume to: <a href="mailto:recruitment@openaccessmarketing.com">recruitment@openaccessbpo.com</a></small></p>


                      </div>
                      

                     <div class="item text-center" >
                        
                         <p style="padding: 5px 30px; margin-bottom: 0px"><h3 class="text-orange"><span style="font-size: smaller;" class="text-primary"> Let's all get our bods</span> Summer-ready!</h3>And the best part is:<br/>it's <span style="font-size: x-large;"> FUN and FREE!!!</span><br/><br/>
                          <img src="./storage/uploads/letsgetphysical-11.jpg" style="z-index: 2" /><br/><br/>

                          Here's the schedule for this week:<br/><br/>

                          <strong style="font-size:larger"><span class="text-primary"> ZUMBA:</span> 
                            <br/><span class="text-danger">April 03, 2019 (Wed) – 7:00 PM</span><br/></strong><br/>

                          <strong style="font-size:larger"><span class="text-primary"> AERO KICKBOXING:</span> 
                            <BR/><span class="text-danger">April 04, 2019 (Thu) – 6:00 PM</span><br/></strong><br/> 

                           <strong style="font-size:larger"><span class="text-primary"> YOGA:</span> 
                            <br/><span class="text-danger">April 05, 2019 (Fri) – 7:00 PM</span><br/></strong>




                          



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
                            <br/><span class="text-danger">April 03, 2019 (Wed) – 7:00 PM</span><br/></strong><br/>

                          <strong style="font-size:larger"><span class="text-primary"> AERO KICKBOXING:</span> 
                            <BR/><span class="text-danger">April 04, 2019 (Thu) – 6:00 PM</span><br/></strong><br/> 

                           <strong style="font-size:larger"><span class="text-primary"> YOGA:</span> 
                            <br/><span class="text-danger">April 05, 2019 (Fri) – 7:00 PM</span><br/></strong>

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


                             <!--physcal sched -->
                    <div class="item text-center" >
                        
                         <p style="padding: 5px 30px; margin-bottom: 0px"><h3 class="text-orange"><span style="font-size: smaller;" class="text-primary"> Let's all get our bods</span> Summer-ready!</h3>And the best part is:<br/>it's <span style="font-size: x-large;"> FUN and FREE!!!</span><br/><br/>
                          <img src="./storage/uploads/kick1.jpg" style="z-index: 2" /><br/><br/>

                          Here's the schedule for this week:<br/><br/>

                          <strong style="font-size:larger"><span class="text-primary"> ZUMBA:</span> 
                            <br/><span class="text-danger">April 03, 2019 (Wed) – 7:00 PM</span><br/></strong><br/>

                          <strong style="font-size:larger"><span class="text-primary"> AERO KICKBOXING:</span> 
                            <BR/><span class="text-danger">April 04, 2019 (Thu) – 6:00 PM</span><br/></strong><br/> 

                           <strong style="font-size:larger"><span class="text-primary"> YOGA:</span> 
                            <br/><span class="text-danger">April 05, 2019 (Fri) – 7:00 PM</span><br/></strong>




                          



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

                            <!--decade of woman -->

                              <div class="item text-center" >
                        <img src="./storage/uploads/WOMENSDAY.jpg" style="z-index: 2" width="100%" /><br/><br/>
                        <p style="padding:10px 50px">Women can and women will. On #WomensDay, we celebrate girls and women who challenge gender stereotypes and inspire change to make a gender-equal world. <br/><br/><strong>Happy International Women’s Day!</strong>
                          <br/><br/>#WeSpeakYourLanguage #OAonWomensDay #WomensDay #IWD2019 #IWD</p>

                          <div style="padding:10px; position: absolute;bottom: 0px; right: 0px; background: rgba(0, 0, 0, 0.8)"> <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!">
                                    <small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAonWomensDay #WomensDay #IWD2019 #IWD</small></a></div> <br/><br/><br/><br/>

                      </div>


                    



                    <div class="item text-center" >
                        <img src="./storage/uploads/groundbreak_1.jpg" style="z-index: 2" width="100%" /><br/><br/>
                        <p style="padding:10px 50px">For decades, women have been paving the way for equality and innovation. Get to know these groundbreaking women who’ve changed the course of history. May they become inspirations to continue the fight this #WomensHistoryMonth and beyond.<br/>
                          <strong class="text-primary">#WeSpeakYourLanguage #OAonWomensHistoryMonth #WHM2019</strong> </p>

                          <div style="padding:10px; position: absolute;bottom: 0px; right: 0px; background: rgba(0, 0, 0, 0.8)"> 
                            <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!"><small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAonWomensHistoryMonth #WHM2019</small></a>
                          </div> <br/><br/><br/><br/>

                    </div>

                    <div class="item text-center" >
                        <img src="./storage/uploads/groundbreak_2.jpg" style="z-index: 2" width="100%" /><br/><br/>
                        <p style="padding:10px 50px">For decades, women have been paving the way for equality and innovation. Get to know these groundbreaking women who’ve changed the course of history. May they become inspirations to continue the fight this #WomensHistoryMonth and beyond.<br/>
                          <strong class="text-primary">#WeSpeakYourLanguage #OAonWomensHistoryMonth #WHM2019</strong> </p>

                          <div style="padding:10px; position: absolute;bottom: 0px; right: 0px; background: rgba(0, 0, 0, 0.8)"> 
                            <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!"><small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAonWomensHistoryMonth #WHM2019</small></a>
                          </div> <br/><br/><br/><br/>

                    </div>

                    <div class="item text-center" >
                        <img src="./storage/uploads/groundbreak_3.jpg" style="z-index: 2" width="100%" /><br/><br/>
                        <p style="padding:10px 50px">For decades, women have been paving the way for equality and innovation. Get to know these groundbreaking women who’ve changed the course of history. May they become inspirations to continue the fight this #WomensHistoryMonth and beyond.<br/>
                          <strong class="text-primary">#WeSpeakYourLanguage #OAonWomensHistoryMonth #WHM2019</strong> </p>

                          <div style="padding:10px; position: absolute;bottom: 0px; right: 0px; background: rgba(0, 0, 0, 0.8)"> 
                            <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!"><small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAonWomensHistoryMonth #WHM2019</small></a>
                          </div> <br/><br/><br/><br/>

                    </div>

                    <div class="item text-center" >
                        <img src="./storage/uploads/groundbreak_4.jpg" style="z-index: 2" width="100%" /><br/><br/>
                        <p style="padding:10px 50px">For decades, women have been paving the way for equality and innovation. Get to know these groundbreaking women who’ve changed the course of history. May they become inspirations to continue the fight this #WomensHistoryMonth and beyond.<br/>
                          <strong class="text-primary">#WeSpeakYourLanguage #OAonWomensHistoryMonth #WHM2019</strong> </p>

                          <div style="padding:10px; position: absolute;bottom: 0px; right: 0px; background: rgba(0, 0, 0, 0.8)"> 
                            <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!"><small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAonWomensHistoryMonth #WHM2019</small></a>
                          </div> <br/><br/><br/><br/>

                    </div>

                    <div class="item text-center" >
                        <img src="./storage/uploads/groundbreak_5.jpg" style="z-index: 2" width="100%" /><br/><br/>
                        <p style="padding:10px 50px">For decades, women have been paving the way for equality and innovation. Get to know these groundbreaking women who’ve changed the course of history. May they become inspirations to continue the fight this #WomensHistoryMonth and beyond.<br/>
                          <strong class="text-primary">#WeSpeakYourLanguage #OAonWomensHistoryMonth #WHM2019</strong> </p>

                          <div style="padding:10px; position: absolute;bottom: 0px; right: 0px; background: rgba(0, 0, 0, 0.8)"> 
                            <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!"><small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAonWomensHistoryMonth #WHM2019</small></a>
                          </div> <br/><br/><br/><br/>

                    </div>

                    <div class="item text-center" >
                        <img src="./storage/uploads/groundbreak_6.jpg" style="z-index: 2" width="100%" /><br/><br/>
                        <p style="padding:10px 50px">For decades, women have been paving the way for equality and innovation. Get to know these groundbreaking women who’ve changed the course of history. May they become inspirations to continue the fight this #WomensHistoryMonth and beyond.<br/>
                          <strong class="text-primary">#WeSpeakYourLanguage #OAonWomensHistoryMonth #WHM2019</strong> </p>

                          <div style="padding:10px; position: absolute;bottom: 0px; right: 0px; background: rgba(0, 0, 0, 0.8)"> 
                            <a style="color:#fff" href="https://www.instagram.com/openaccessbpo/" target="_blank" title="Follow us on Instagram!"><small>Follow us on Instagram! <strong>@openaccessbpo</strong> <br/> #WeSpeakYourLanguage #OAonWomensHistoryMonth #WHM2019</small></a>
                          </div> <br/><br/><br/><br/>

                    </div>

                            <!-- BLOOD DONATION-->
                    <div class="item text-center" >
                        
                         <p style="padding: 5px 30px; margin-bottom: 0px"><h3 class="text-orange"><span style="font-size: smaller;" class="text-primary">What: </span>Open Access 1st Blood Donation Drive 2019</h3><br/><br/>
                          <img src="./storage/uploads/wellness10.jpg" style="z-index: 2" width="100%" /><br/><br/>
                          <p style="padding: 5px 30px; margin-bottom: 0px">
                          <h4>
                          <strong>When:</strong> <span class="text-danger"> April 03, 2019 ( G2); <br/>April 04, 2019 ( Jaka )</span><br/>
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




