<?php

namespace OAMPI_Eval\Http\Controllers;


use Hash;
use Carbon\Carbon;
use Excel;
use \PDF;
use \DB;
use \App;
use \Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Yajra\Datatables\Facades\Datatables;

use OAMPI_Eval\Http\Requests;
use OAMPI_Eval\User;
use OAMPI_Eval\Engagement;
use OAMPI_Eval\Engagement_Comment;
use OAMPI_Eval\Engagement_EntryComments;
use OAMPI_Eval\Engagement_EntryLikes;
use OAMPI_Eval\Engagement_Reply;
use OAMPI_Eval\Engagement_CommentLikes;
use OAMPI_Eval\Engagement_ReplyLikes;
use OAMPI_Eval\Engagement_Entry;
use OAMPI_Eval\Engagement_Vote;
use OAMPI_Eval\Engagement_Trigger;
use OAMPI_Eval\Engagement_Flags;
use OAMPI_Eval\Engagement_EntryTrigger;
use OAMPI_Eval\Engagement_EntryDetails;
use OAMPI_Eval\Cutoff;
use OAMPI_Eval\User_Leader;
use OAMPI_Eval\Campaign;
use OAMPI_Eval\Status;
use OAMPI_Eval\Team;
use OAMPI_Eval\Floor;
use OAMPI_Eval\UserType;
use OAMPI_Eval\Position;
use OAMPI_Eval\ImmediateHead;
use OAMPI_Eval\ImmediateHead_Campaign;


class EngagementController extends Controller
{
    protected $user;
    protected $user_dtr;
    use Traits\TimekeepingTraits;
    use Traits\UserTraits;

    public function __construct(Engagement $engagement)
    {
        $this->middleware('auth');
        $this->engagement = $engagement;
        $this->user =  User::find(Auth::user()->id);
    }

    public function castvote(Request $request, $id)
    {

        $userEntry = Engagement_Entry::find($id);
        $vote = new Engagement_Vote;
        $vote->engagement_id = $userEntry->engagement_id;
        $vote->user_id = $this->user->id;
        $vote->engagement_entryID = $id;
        $vote->save();

         $correct = Carbon::now('GMT+8');
         if($this->user->id !== 564 ) {
                                  $file = fopen('storage/uploads/log.txt', 'a') or die("Unable to open logs");
                                    fwrite($file, "-------------------\n Castvote Frightful by [". $this->user->id."] ".$this->user->lastname." on". $correct->format('M d h:i A'). "\n");
                                }
        return redirect()->back();

    }

    public function cancelEntry($id)
    {
        $vote = Engagement_Entry::find($id);
        $vote->delete();
        return redirect()->back();
        //return $vote;

    }

    public function deleteComment($id)
    {
        $comment = Engagement_Comment::find($id);

        //check mo muna kung may replies na. Pag meron, update content na lang
        if (count($comment->replies) > 0)
        {
            $comment->body = "<em>*** user already removed this comment *** </em> ";
            $comment->save();
            return redirect()->back();

        }else
        {
            $comment->delete();
            $correct = Carbon::now('GMT+8');
            if($this->user->id !== 564 ) {
                                  $file = fopen('storage/uploads/log.txt', 'a') or die("Unable to open logs");
                                    fwrite($file, "-------------------\n DelComment by [". $this->user->id."] ".$this->user->lastname." on". $correct->format('M d h:i A'). "\n");
                                }
            return redirect()->back();

        }


    }
     public function deleteEntryComment(Request $request)
    {
        $comment = Engagement_EntryComments::find($request->commentID);


            $comment->delete();
            $correct = Carbon::now('GMT+8');
            if($this->user->id !== 564 ) {
                                  $file = fopen('storage/uploads/log.txt', 'a') or die("Unable to open logs");
                                    fwrite($file, "-------------------\n DelComment by [". $this->user->id."] ".$this->user->lastname." on". $correct->format('M d h:i A'). "\n");
                                }
            return redirect()->back();




    }

    public function deletePost($id)
    {
        $vote = Engagement_Entry::find($id);
        if( \Auth::user()->id !== 564 ) {
                $file = fopen('storage/uploads/log.txt', 'a') or die("Unable to open logs");
                  fwrite($file, "-------------------\n Delete Entry on [".$vote->engagement_id."] ".Carbon::now('GMT+8')->format('Y-m-d H:i')." by [". \Auth::user()->id."] ".\Auth::user()->lastname."\n");
                  fclose($file);
              }
        $vote->delete();

        return redirect()->action('EngagementController@show',2);
        //return $vote;

    }

    public function deleteReply($id)
    {
        $reply = Engagement_Reply::find($id);
        $reply->delete();

         $correct = Carbon::now('GMT+8');
         if($this->user->id !== 564 ) {
                                  $file = fopen('storage/uploads/log.txt', 'a') or die("Unable to open logs");
                                    fwrite($file, "-------------------\n DelReply by [". $this->user->id."] ".$this->user->lastname." on". $correct->format('M d h:i A'). "\n");
                                }

        return redirect()->back();

    }

    public function disqualify(Request $request)
    {
        $d = Engagement_Entry::find($request->entry_id);
        $d->disqualified = $request->q;
        $d->push();
         if( \Auth::user()->id !== 564 ) {
                $file = fopen('public/build/rewards.txt', 'a') or die("Unable to open logs");
                  fwrite($file, "-------------------\n FlaggedNote on ".Carbon::now('GMT+8')->format('Y-m-d H:i')." by [". \Auth::user()->id."] ".\Auth::user()->lastname."\n");
                  fclose($file);
              }
        return response()->json(['success'=>1, 'entry'=>$d]);

    }


    public function getAllPosts($id)
    {
        // DB::select( DB::raw("SELECT users.id, users.status_id, users.firstname,users.lastname,users.nickname,DATE_FORMAT(users.birthday, '%m-%d-%Y')as birthday,users.dateHired,positions.name as jobTitle, campaign.id as campID, campaign.name as program, users.employeeNumber FROM users INNER JOIN team ON team.user_id = users.id INNER JOIN campaign ON team.campaign_id = campaign.id INNER JOIN positions ON users.position_id = positions.id WHERE MONTH(users.birthday) = :m AND DAY(users.birthday) >= :d AND DAY(users.birthday) <= :dt AND  users.status_id != 6 AND users.status_id != 7 AND users.status_id != 8 AND users.status_id != 9  ORDER BY birthday ASC"), array(
        //              'm' => $m_from,
        //              'd' => $d_from,
        //              'dt' =>$d_to
        //            ));

        $post = DB::select( DB::raw("SELECT engagement.id as engagementID, engagement_elements.id as elementID, engagement_elements.label as elementLabel, engagement_entryItems.id as itemID, engagement_entryItems.label as entryLabel, engagement_entryItems.element_id as elementID, engagement_entryItems.ordering, engagement_entry.id as postID, engagement_entry.user_id as postedBy, engagement_entry.anonymous, engagement_entryDetails.value FROM engagement INNER JOIN engagement_entry ON engagement_entry.id = engagement.id INNER JOIN engagement_entryItems ON engagement_entryItems.engagement_id = engagement.id INNER JOIN engagement_entryDetails ON engagement_entryDetails.engagement_entryID = engagement_entry.id WHERE engagement.id = :id"), array(
                     'id' => $id
                   ));
        return $post;
    }

    public function like(Request $request)
    {

        switch ($request->type)
        {
            case 'comment':
                            {
                                $c = new Engagement_CommentLikes;
                                $c->user_id = $this->user->id;
                                $c->comment_id = $request->commentid;
                                $c->save();

                            }break;

            case 'post':
                            {
                                $c = new Engagement_EntryLikes;
                                $c->user_id = $this->user->id;
                                $c->entryID = $request->entryID;
                                $c->created_at = Carbon::now('GMT+8')->format('Y-m-d H:i:s');
                                $c->save();

                            }break;

            case 'reply':
                            {
                                $c = new Engagement_ReplyLikes;
                                $c->user_id = $this->user->id;
                                $c->reply_id = $request->commentid;
                                $c->save();

                            }break;



        }

        return response()->json($c);
    }



    public function postComment(Request $request,$id)
    {
        $correct = Carbon::now('GMT+8');
        $comment = new Engagement_Comment;
        $comment->user_id = $this->user->id;
        $comment->engagement_id = $id;
        $comment->body = $request->comment;
        $comment->created_at = $correct->format('Y-m-d H:i:s');
        $comment->updated_at = $correct->format('Y-m-d H:i:s');
        $comment->save();

        if($this->user->id !== 564 ) {
                                  $file = fopen('storage/uploads/log.txt', 'a') or die("Unable to open logs");
                                    fwrite($file, "-------------------\n Comment on [".$id."] by [". $this->user->id."] ".$this->user->lastname." on". $correct->format('M d h:i A'). "\n");
                                }
        return response()->json($comment);
    }

    public function postEntryComment(Request $request)
    {
        $correct = Carbon::now('GMT+8');
        $comment = new Engagement_EntryComments;
        $comment->user_id = $this->user->id;
        $comment->entryID = $request->entryID;
        $comment->body = $request->body;
        $comment->anonymous = $request->anonymously;
        $comment->created_at = $correct->format('Y-m-d H:i:s');
        $comment->updated_at = $correct->format('Y-m-d H:i:s');
        $comment->save();

        if($this->user->id !== 564 ) {
                                  $file = fopen('public/build/rewards.txt', 'a') or die("Unable to open logs");
                                    fwrite($file, "-------------------\n EntryComment on [".$request->entryID."] by [". $this->user->id."] ".$this->user->lastname." on". $correct->format('M d h:i A'). "\n");
                                }
        return response()->json($comment);
    }


    public function postReply(Request $request,$id)
    {
        $correct = Carbon::now('GMT+8');
        $comment = new Engagement_Reply;
        $comment->user_id = $this->user->id;
        $comment->engagement_id = $id;
        $comment->comment_id = $request->comment_id;
        $comment->body = $request->comment;
        $comment->created_at = $correct->format('Y-m-d H:i:s');
        $comment->updated_at = $correct->format('Y-m-d H:i:s');
        $comment->save();

        if($this->user->id !== 564 ) {
                                  $file = fopen('storage/uploads/log.txt', 'a') or die("Unable to open logs");
                                    fwrite($file, "-------------------\n Reply on [".$request->comment_id."] by [". $this->user->id."] ".$this->user->lastname." on". $correct->format('M d h:i A'). "\n");
                                }
        return response()->json($comment);
    }

    public function reportEntry(Request $request)
    {
        $report = new Engagement_Flags;
        $report->user_id = $this->user->id;
        $report->engagement_entryID = $request->entry_id;
        $report->reason = $request->reason;
        $report->created_at = Carbon::now('GMT+8')->format('Y-m-d H:i:s');
        $report->save();
        $correct = Carbon::now('GMT+8');

        if($this->user->id !== 564 )  //VALENTINES
        {
            $file = fopen('public/build/rewards.txt', 'a') or die("Unable to open logs");
                fwrite($file, "-------------------\n Reported_ValNote by [". $this->user->id."] ".$this->user->lastname." on". $correct->format('M d h:i A'). "\n");

        }

        return response()->json(['success'=>1, 'report'=>$report]);

    }



    public function show($id)
    {

        DB::connection()->disableQueryLog();
        $correct = Carbon::now('GMT+8');

        if(is_null(Engagement::find($id))) return view('empty');

        if (Engagement::find($id)->active != '1'){
            if($this->user->id !== 564 ) {
              $file = fopen('storage/uploads/log.txt', 'a') or die("Unable to open logs");
                fwrite($file, "-------------------\n Votenow Frightful by [". $this->user->id."] ".$this->user->lastname." on". $correct->format('M d h:i A'). "\n");}
            return view('access-denied');
        }

        $mods = [564,534,879,1717,1611,1398,491];
        $awarder = [564,491];

        in_array($this->user->id, $mods) ? $canModerate=1 : $canModerate=0;
        in_array($this->user->id, $awarder) ? $canAward=1 : $canAward=0;




        switch ($id) {
            case 6: {
                        $engagement = DB::table('engagement')->where('engagement.id',$id)->

                                select('engagement.id','engagement.entriesDeadline','engagement.name as activity','engagement.startDate','engagement.endDate','engagement.body as content','engagement.withVoting','engagement.fairVoting','engagement.multipleEntry')->get();
                        if($this->user->id !== 564 )
                             {
                                $file = fopen('public/build/rewards.txt', 'a') or die("Unable to open logs");
                                fwrite($file, "-------------------\n Check_Zumba [". $this->user->id."] ".$this->user->lastname." on". $correct->format('M d h:i A'). "\n");
                             }
                        return view('people.empEngagement-show_plain',compact('engagement','id'));

            } break;

            case 7: {
                        $engagement = DB::table('engagement')->where('engagement.id',$id)->

                                select('engagement.id','engagement.entriesDeadline','engagement.name as activity','engagement.startDate','engagement.endDate','engagement.body as content','engagement.withVoting','engagement.fairVoting','engagement.multipleEntry')->get();
                        if($this->user->id !== 564 )
                             {
                                $file = fopen('public/build/rewards.txt', 'a') or die("Unable to open logs");
                                fwrite($file, "-------------------\n Check_Aero [". $this->user->id."] ".$this->user->lastname." on". $correct->format('M d h:i A'). "\n");
                             }
                        return view('people.empEngagement-show_plain',compact('engagement','id'));

            } break;

            case 8: {
                        $engagement = DB::table('engagement')->where('engagement.id',$id)->

                                select('engagement.id','engagement.entriesDeadline','engagement.name as activity','engagement.startDate','engagement.endDate','engagement.body as content','engagement.withVoting','engagement.fairVoting','engagement.multipleEntry')->get();
                        if($this->user->id !== 564 )
                             {
                                $file = fopen('public/build/rewards.txt', 'a') or die("Unable to open logs");
                                fwrite($file, "-------------------\n Check_Yoga [". $this->user->id."] ".$this->user->lastname." on". $correct->format('M d h:i A'). "\n");
                             }
                        return view('people.empEngagement-show_plain',compact('engagement','id'));

            } break;

            default:{
                        $engagement = DB::table('engagement')->where('engagement.id',$id)->
                                join('engagement_entryItems','engagement.id','=','engagement_entryItems.engagement_id')->
                                join('engagement_elements','engagement_entryItems.element_id','=','engagement_elements.id')->
                                //join('engagement_trigger','engagement_trigger.engagement_id','=','engagement.id')->'engagement_trigger.name as triggers'
                                select('engagement.id','engagement.entriesDeadline','engagement.isContest', 'engagement.name as activity','engagement.startDate','engagement.endDate','engagement.body as content','engagement.description', 'engagement.withVoting','engagement.fairVoting','engagement_entryItems.label','engagement_elements.label as dataType','engagement_entryItems.ordering','engagement_entryItems.id as itemID','engagement.multipleEntry')->

                                get();
                    }break;
        }



        //return $engagement[0]->isContest;
        $triggers = Engagement_Trigger::where('engagement_id',$id)->orderBy('name','ASC')->get();
        $itemIDs1 = collect($engagement)->pluck('itemID')->flatten();
        $itemType = collect($engagement)->pluck('dataType')->flatten();
        $itemIDs ="";
        foreach ($itemIDs1 as $i) {
           $itemIDs .= $i.",";
        }
        $itemTypes ="";
        foreach ($itemType as $i) {
           $itemTypes .= $i.",";
        }


        $existingEntry = DB::table('engagement_entry')->where('engagement_entry.engagement_id',$id)->
                                where('user_id',$this->user->id)->
                                join('engagement_entryDetails','engagement_entryDetails.engagement_entryID','=','engagement_entry.id')->
                                join('engagement_entryItems','engagement_entryDetails.entry_itemID','=','engagement_entryItems.id')->
                                join('engagement_elements','engagement_entryItems.element_id','=','engagement_elements.id')->
                                select('engagement_entry.id as entryID', 'engagement_entryItems.ordering', 'engagement_entryDetails.value as value','engagement_elements.label as elemType','engagement_entryDetails.id as itemID', 'engagement_entryItems.label','engagement_entry.user_id','engagement_entry.created_at','engagement_entry.disqualified','engagement_entry.anonymous')->get();

                                //select('id')->get();
        if (count($existingEntry) > 0)
        {
            $hasEntry=true;
            $myTriggers = DB::table('engagement')->where('engagement.id',$id)->
                            join('engagement_entry','engagement_entry.engagement_id','=','engagement.id')->
                            join('engagement_entryTrigger','engagement_entry.id','=','engagement_entryTrigger.entryID')->
                            join('engagement_trigger','engagement_entryTrigger.triggerID','=','engagement_trigger.id')->
                            select('engagement_entry.id as entryID','engagement_trigger.id as triggerID', 'engagement_trigger.name as trigger')->get();
            $myTrigger = collect($myTriggers)->where('entryID',$existingEntry[0]->entryID)->all();
            $myTriggerArray = collect($myTriggers)->where('entryID',$existingEntry[0]->entryID)->pluck('triggerID')->toArray();


        } else { $hasEntry=false; $myTrigger=null; $myTriggerArray=null; }

        $voted = DB::table('engagement_vote')->where('engagement_vote.engagement_id',$id)->
                        join('engagement_entry','engagement_vote.engagement_entryID','=','engagement_entry.id')->
                        where('engagement_vote.user_id',$this->user->id)->
                        where('engagement_entry.disqualified',0)->get();
        ( count($voted) > 0 ) ? $alreadyVoted=1 : $alreadyVoted=0;



        //if tapos na ung contest/activity
        if ( $correct->format('Y-m-d H:i:s') >$engagement[0]->endDate)// && !$canModerate
        {
            if ($id != 1 && !$engagement[0]->isContest){
                return Redirect::action('EngagementController@wall',$id);
            }
            else
            {
                if($id == 3) //painting
                {
                    if( \Auth::user()->id !== 564 ) {
                    $file = fopen('public/build/rewards.txt', 'a') or die("Unable to open logs");
                      fwrite($file, "-------------------\n PaintingEntries on ".Carbon::now('GMT+8')->format('Y-m-d H:i')." by [". \Auth::user()->id."] ".\Auth::user()->lastname."\n");
                      fclose($file);
                    }
                    return view('people.empEngagement-show_paintingEntries',compact( 'engagement'));


                }
                if($id == 2) //VALENTINES
                {
                    if($this->user->id !== 564 )
                     {
                        $file = fopen('public/build/rewards.txt', 'a') or die("Unable to open logs");
                        fwrite($file, "-------------------\n Reminisce Valentine [". $this->user->id."] ".$this->user->lastname." on". $correct->format('M d h:i A'). "\n");
                     }

                     return Redirect::to('http://172.17.0.2/project/freedomwall/wall/index.php');

                }


                $votes = DB::table('engagement')->where('engagement.id',$id)->
                        join('engagement_entry','engagement_entry.engagement_id','=','engagement.id')->
                        join('engagement_vote','engagement_vote.engagement_entryID','=','engagement_entry.id')->
                        //join('engagement_entryDetails','engagement_entryDetails.engagement_entryID','=','engagement_entry.id')->
                        join('users','engagement_vote.user_id','=','users.id')->
                        join('positions','users.position_id','=','positions.id')->
                        join('team','team.user_id','=','engagement_vote.user_id')->
                        join('campaign','campaign.id','=','team.campaign_id')->
                        select('engagement.name as activity','engagement_entry.user_id as entryBy','engagement_entry.id as entryID','engagement_vote.user_id as voterID','users.firstname as voter_firstname','users.lastname as voter_lastname','positions.name as voter_jobTitle','campaign.name as program')->
                        where('engagement_entry.disqualified',0)->get();

                $ranking = new Collection;
                $rankByProgram = new Collection;
                $votesByCampaign = collect($votes)->groupBy('program');

                $allEntries = DB::table('engagement')->where('engagement.id',$id)->
                                join('engagement_entry','engagement_entry.engagement_id','=','engagement.id')->

                                join('engagement_entryDetails','engagement_entryDetails.engagement_entryID','=','engagement_entry.id')->
                                join('engagement_entryItems','engagement_entryItems.id','=','engagement_entryDetails.entry_itemID')->
                                join('users','engagement_entry.user_id','=','users.id')->
                                join('positions','users.position_id','=','positions.id')->
                                join('team','team.user_id','=','engagement_entry.user_id')->
                                join('campaign','campaign.id','=','team.campaign_id')->
                                select('engagement.name as activity', 'engagement_entry.id', 'engagement_entry.user_id','users.firstname','users.lastname','users.nickname','positions.name as jobTitle','campaign.name as program', 'engagement_entryItems.label','engagement_entryDetails.value','engagement_entry.disqualified')->get();
                                //where('engagement_entry.disqualified',NULL)->get();


                foreach ($votesByCampaign as $camp) {

                    $entries = collect($camp)->groupBy('entryID');

                    foreach ($entries as $key) {
                        $voters = collect($votes)->where('program',$camp[0]->program);
                        $percentage = (count($key)/count($voters));
                        $pointsEarned = number_format( (count(collect($allEntries)->groupBy('user_id')) * $percentage),2);
                        $rankByProgram->push(['entry'=>collect($key)->pluck('entryID')->first(), 'votes'=>count($key),'totalVoters'=>count($voters), 'percentage'=>$percentage, 'points'=>$pointsEarned, 'entries'=>count(collect($allEntries)->groupBy('user_id')), 'camp'=>$camp[0]->program]);
                    }
                } //return $rankByProgram;


                //$submissions = collect($allEntries)->groupBy('id');return $submissions;
                $tallyProg = collect($rankByProgram)->sortByDesc('votes')->groupBy('camp'); //return $tallyProg;
                $tallyEntry = collect($rankByProgram)->sortByDesc('entry')->groupBy('entry');
                $finalTally = new Collection;



                foreach ($tallyEntry->reverse() as $key) {

                    $vote=0; $actualVotes=0;
                    foreach ($key as $v) {
                        $vote += (float)$v['points'];
                        $actualVotes += (float)$v['votes'];
                    }

                    $theEntry = collect($allEntries)->where('id',$key[0]['entry']);
                    $e = $theEntry->where('label',"Title")->first()->value;
                    $max = count(collect($allEntries)->groupBy('user_id')) * count($tallyProg);

                    $submission = collect($allEntries)->where('id',$key[0]['entry'])->first();

                    $finalTally->push(['activity'=> $votes[0]->activity,'entryID'=>$key[0]['entry'],'user_id'=>$submission->user_id, 'firstname'=>$submission->firstname,'nickname'=>$submission->nickname,'lastname'=>$submission->lastname,'jobTitle'=>$submission->jobTitle, 'program'=>$submission->program,'title'=>$e,'actualVotes'=>$actualVotes, 'totalPoints'=>$vote,'maxpoints'=>$max, 'grandTotal'=>number_format(100*($vote / $max ),2) ]);

                }

               //return $finalTally;



               if($this->user->id !== 564 )
                 {
                    $file = fopen('storage/uploads/log.txt', 'a') or die("Unable to open logs");
                    fwrite($file, "-------------------\n View Frightful Winners by [". $this->user->id."] ".$this->user->lastname." on". $correct->format('M d h:i A'). "\n");
                 }

                $deadline = $engagement[0]->entriesDeadline!=NULL && $correct->format('Y-m-d H:i:s') > $engagement[0]->entriesDeadline;

                return view('people.empEngagement-showContest',compact('engagement','id','hasEntry','existingEntry','alreadyVoted','triggers','myTrigger','myTriggerArray', 'deadline'));

                //return view('people.empEngagement-showWinner',compact('tallyEntry','finalTally', 'engagement','id','hasEntry','existingEntry','alreadyVoted','triggers','myTrigger','myTriggerArray'));

            }


        }else
        {

            if($id == 2) //VALENTINES
            {
                $allPosts = collect($existingEntry)->groupBy('entryID');
                $allEntries = DB::table('engagement_entry')->where('engagement_entry.engagement_id',$id)->
                                join('engagement','engagement_entry.engagement_id','=','engagement.id')->
                                join('engagement_entryDetails','engagement_entryDetails.engagement_entryID','=','engagement_entry.id')->
                                join('engagement_entryItems','engagement_entryDetails.entry_itemID','=','engagement_entryItems.id')->
                                join('engagement_elements','engagement_entryItems.element_id','=','engagement_elements.id')->
                                join('users','engagement_entry.user_id','=','users.id')->
                                join('team','team.user_id','=','users.id')->
                                join('campaign','team.campaign_id','=','campaign.id')->
                                join('positions','users.position_id','=','positions.id')->
                                select('engagement.name as activity','engagement.withVoting', 'engagement_entry.id as entryID','engagement_entry.disqualified', 'engagement_entryItems.ordering', 'engagement_entryDetails.value as value','engagement_elements.label as elemType','engagement_entryItems.label','engagement_entry.user_id','users.firstname','users.lastname','users.nickname','positions.name as jobTitle' ,'campaign.name as program','engagement_entry.created_at','engagement_entry.anonymous','engagement_entry.created_at')->get();
                                //where('engagement_entry.disqualified',NULL)->get();
                $userEntries = collect($allEntries)->groupBy('entryID');

                if( \Auth::user()->id !== 564 ) {
                $file = fopen('public/build/rewards.txt', 'a') or die("Unable to open logs");
                  fwrite($file, "-------------------\n Check Vday2020 on ".Carbon::now('GMT+8')->format('Y-m-d H:i')." by [". \Auth::user()->id."] ".\Auth::user()->lastname."\n");
                  fclose($file);
                }

                return view('people.empEngagement-show_valentines',compact('engagement','id','hasEntry','allPosts','alreadyVoted','triggers','myTrigger','myTriggerArray','itemIDs','existingEntry','canModerate','userEntries','itemTypes'));

            }else if($id == 3) //PAINTING
            {
                if( \Auth::user()->id !== 564 ) {
                $file = fopen('public/build/rewards.txt', 'a') or die("Unable to open logs");
                  fwrite($file, "-------------------\n PaintingEntries on ".Carbon::now('GMT+8')->format('Y-m-d H:i')." by [". \Auth::user()->id."] ".\Auth::user()->lastname."\n");
                  fclose($file);
                }
                return view('people.empEngagement-show_paintingEntries',compact( 'engagement'));

                $allPosts = collect($existingEntry)->groupBy('entryID');
                $allEntries = DB::table('engagement_entry')->where('engagement_entry.engagement_id',$id)->
                                join('engagement','engagement_entry.engagement_id','=','engagement.id')->
                                join('engagement_entryDetails','engagement_entryDetails.engagement_entryID','=','engagement_entry.id')->
                                join('engagement_entryItems','engagement_entryDetails.entry_itemID','=','engagement_entryItems.id')->
                                join('engagement_elements','engagement_entryItems.element_id','=','engagement_elements.id')->
                                join('users','engagement_entry.user_id','=','users.id')->
                                join('team','team.user_id','=','users.id')->
                                join('campaign','team.campaign_id','=','campaign.id')->
                                join('positions','users.position_id','=','positions.id')->
                                select('engagement.name as activity','engagement.withVoting', 'engagement_entry.id as entryID','engagement_entry.disqualified', 'engagement_entryItems.ordering', 'engagement_entryDetails.value as value','engagement_elements.label as elemType','engagement_entryItems.label','engagement_entry.user_id','users.firstname','users.lastname','users.nickname','positions.name as jobTitle' ,'campaign.name as program','engagement_entry.created_at','engagement_entry.anonymous','engagement_entry.created_at')->get();
                                //where('engagement_entry.disqualified',NULL)->get();
                $userEntries = collect($allEntries)->groupBy('entryID');

                if( \Auth::user()->id !== 564 ) {
                $file = fopen('public/build/rewards.txt', 'a') or die("Unable to open logs");
                  fwrite($file, "-------------------\n Check Painting2020 on ".Carbon::now('GMT+8')->format('Y-m-d H:i')." by [". \Auth::user()->id."] ".\Auth::user()->lastname."\n");
                  fclose($file);
                }
                //return $allPosts;
                return view('people.empEngagement-show_painting',compact('engagement','id','hasEntry','allPosts','alreadyVoted','triggers','myTrigger','myTriggerArray','itemIDs','existingEntry','canModerate','userEntries','itemTypes'));

            }else if($id == 4) //hidden OA
            {
                if( \Auth::user()->id !== 564 ) {
                $file = fopen('public/build/rewards.txt', 'a') or die("Unable to open logs");
                  fwrite($file, "-------------------\n HiddenEntries on ".Carbon::now('GMT+8')->format('Y-m-d H:i')." by [". \Auth::user()->id."] ".\Auth::user()->lastname."\n");
                  fclose($file);
                }
                //return view('people.empEngagement-show_paintingEntries',compact( 'engagement'));

                $allPosts = collect($existingEntry)->groupBy('entryID');
                $allEntries = DB::table('engagement_entry')->where('engagement_entry.engagement_id',$id)->
                                join('engagement','engagement_entry.engagement_id','=','engagement.id')->
                                join('engagement_entryDetails','engagement_entryDetails.engagement_entryID','=','engagement_entry.id')->
                                join('engagement_entryItems','engagement_entryDetails.entry_itemID','=','engagement_entryItems.id')->
                                join('engagement_elements','engagement_entryItems.element_id','=','engagement_elements.id')->
                                join('users','engagement_entry.user_id','=','users.id')->
                                join('team','team.user_id','=','users.id')->
                                join('campaign','team.campaign_id','=','campaign.id')->
                                join('positions','users.position_id','=','positions.id')->
                                select('engagement.name as activity','engagement.withVoting', 'engagement_entry.id as entryID','engagement_entry.disqualified', 'engagement_entryItems.ordering', 'engagement_entryDetails.value as value','engagement_elements.label as elemType','engagement_entryItems.label','engagement_entry.user_id','users.firstname','users.lastname','users.nickname','positions.name as jobTitle' ,'campaign.name as program','engagement_entry.created_at','engagement_entry.anonymous','engagement_entry.created_at','engagement.multipleEntry')->get();
                                //where('engagement_entry.disqualified',NULL)->get();
                $userEntries = collect($allEntries)->groupBy('entryID');


                //return $engagement;
                return view('people.empEngagement-show_hiddenLogo',compact('engagement','id','hasEntry','allPosts','alreadyVoted','triggers','myTrigger','myTriggerArray','itemIDs','existingEntry','canModerate','userEntries','itemTypes'));

            }
            else if($id >= 5 && !$engagement[0]->isContest) // == 5 || $id == 9 || $id == 10 || $id== 11|| $id== 12|| $id== 13 || $id== 14 || $id== 15 || $id ==16 || $id ==17 ) //OPEN WALL
            {
                $waysto = 11; // rewards_waysto ID for EE
                $allPosts = collect($existingEntry)->groupBy('entryID');
                $allEntries = DB::table('engagement_entry')->where('engagement_entry.engagement_id',$id)->
                                join('engagement','engagement_entry.engagement_id','=','engagement.id')->
                                join('engagement_entryDetails','engagement_entryDetails.engagement_entryID','=','engagement_entry.id')->
                                join('engagement_entryItems','engagement_entryDetails.entry_itemID','=','engagement_entryItems.id')->
                                join('engagement_elements','engagement_entryItems.element_id','=','engagement_elements.id')->
                                join('users','engagement_entry.user_id','=','users.id')->
                                join('team','team.user_id','=','users.id')->
                                join('campaign','team.campaign_id','=','campaign.id')->
                                join('positions','users.position_id','=','positions.id')->
                                select('engagement.name as activity','engagement.withVoting', 'engagement_entry.id as entryID','engagement_entry.disqualified', 'engagement_entryItems.ordering', 'engagement_entryDetails.value as value','engagement_elements.label as elemType','engagement_entryItems.label','engagement_entry.user_id','users.firstname','users.lastname','users.nickname','positions.name as jobTitle' ,'campaign.name as program','engagement_entry.created_at','engagement_entry.anonymous','engagement_entry.created_at')->get();
                                //where('engagement_entry.disqualified',NULL)->get();
                $userEntries = collect($allEntries)->groupBy('entryID');
                $uniqueUsers = collect($allEntries)->sortBy('lastname')->groupBy('user_id')->unique();

                if( \Auth::user()->id !== 564 ) {
                $file = fopen('storage/uploads/log.txt', 'a') or die("Unable to open logs");
                ($id == 5) ? fwrite($file, "-------------------\n Check openWall on ".Carbon::now('GMT+8')->format('Y-m-d H:i')." by [". \Auth::user()->id."] ".\Auth::user()->lastname."\n") : fwrite($file, "-------------------\n Check openWall[".$id."] on ".Carbon::now('GMT+8')->format('Y-m-d H:i')." by [". \Auth::user()->id."] ".\Auth::user()->lastname."\n");
                  fclose($file);
                }

                return view('people.empEngagement-show_wall',compact('engagement','id','hasEntry','allPosts','alreadyVoted','triggers','myTrigger','myTriggerArray','itemIDs','existingEntry','canModerate','canAward', 'userEntries','itemTypes','uniqueUsers','waysto'));

            }
            else
            {
                if($this->user->id !== 564 )
                 {
                    $file = fopen('storage/uploads/log.txt', 'a') or die("Unable to open logs");
                    fwrite($file, "-------------------\n View Frightful2020 by [". $this->user->id."] ".$this->user->lastname." on". $correct->format('M d h:i A'). "\n");
                 }

                 //return $engagement;
                 $deadline = $engagement[0]->entriesDeadline!=NULL && $correct->format('Y-m-d H:i:s') > $engagement[0]->entriesDeadline;

                 if ($engagement[0]->isContest)
                    return view('people.empEngagement-showContest',compact('engagement','id','hasEntry','existingEntry','alreadyVoted','triggers','myTrigger','myTriggerArray', 'deadline'));

                 else return view('people.empEngagement-show',compact('engagement','id','hasEntry','existingEntry','alreadyVoted','triggers','myTrigger','myTriggerArray', 'deadline'));

            }





        }




        //return $engagement;
    }


    public function saveEntry(Request $request)
    {
        $correct = Carbon::now('GMT+8');
        $entry = new Engagement_Entry;
        $entry->user_id = $this->user->id;
        $entry->disqualified = 0;
        $entry->engagement_id = $request->engagement_id;
        $entry->created_at = $correct->format('Y-m-d H:i:s');
        $entry->updated_at = $correct->format('Y-m-d H:i:s');
        $entry->save();

        $ctr = 0;
        foreach ($request->itemIDs as $k) {
            $userEntry = new Engagement_EntryDetails;
            $userEntry->engagement_entryID = $entry->id;
            $userEntry->entry_itemID = $k;
            $userEntry->value = $request->items[$ctr];
            $userEntry->created_at = $correct->format('Y-m-d H:i:s');
            $userEntry->updated_at = $correct->format('Y-m-d H:i:s');
            $userEntry->save();

            $ctr++;
        }

        if( !is_null($request->triggers) )
        {
            foreach ($request->triggers as $key) {
            $trigger = new Engagement_EntryTrigger;
            $trigger->entryID = $entry->id;
            $trigger->triggerID = $key;
            $trigger->save();
            }

        }


         $correct = Carbon::now('GMT+8');
         if($this->user->id !== 564 ) {
                                  $file = fopen('storage/uploads/log.txt', 'a') or die("Unable to open logs");
                                    fwrite($file, "-------------------\n Submitted EE entry by [". $this->user->id."] ".$this->user->lastname." on". $correct->format('M d h:i A'). "\n");
                                }

        return response()->json(['success'=>1, 'entry'=>$entry]);
    }

    public function saveEntry2(Request $request)
    {
        $correct = Carbon::now('GMT+8');


        $entry = new Engagement_Entry;
        $entry->user_id = $this->user->id;
        $entry->engagement_id = $request->engagement_id;
        $entry->anonymous = $request->anonymous;
        $entry->disqualified = 0;
        $entry->created_at = $correct->format('Y-m-d H:i:s');
        $entry->updated_at = $correct->format('Y-m-d H:i:s');
        $entry->save();

        $ctr = 0;

        $allitems = explode(',', $request->itemids);
        $allitemTypes = explode(',', $request->itemtypes);
        //return response()->json(['success'=>1, 'allitemTypes'=>$allitemTypes, 'allitems'=>$allitems]);
        foreach ($allitems as $k) {

            //**** with image attachment
            if($k !== "")
            {
                (($k-13) % 2 == 0) ? $anImg=true : $anImg=false;

                if(($k == '4' || $k == '13'|| $anImg || $allitemTypes[$ctr]=='IMG') && $request->file('file') == null ){

                }
                else
                {
                    if(($k == '4' || $k == '13'|| $anImg || $allitemTypes[$ctr]=='IMG') && $request->file('file') )
                    {
                        $image_code = '';
                        $image = $request->file('file');
                        switch ($request->engagement_id) {
                            case '2':$filen = "valentines2020_"; break;
                            case '3':$filen = "painting2020_"; break;
                            case '4':$filen = "guess2020_"; break;
                            case '5':$filen = "wall2020_"; break;
                            case '9':$filen = "wall[2]2020_"; break;
                            case '10':$filen = "wall[3]2020_"; break;
                            case '11':$filen = "wall[4]2020_"; break;
                            case '12':$filen = "wall[5]2020_"; break;
                            case '13':$filen = "wall[6]2020_"; break;
                            case '14':$filen = "wall[7]2020_"; break;
                            case '15':$filen = "wall[8]2020_"; break;
                            case '19':$filen = "wall[13]2020_"; break;
                            default: $filen="wall_";break;

                        }

                        $new_name = $filen.$this->user->id."_".rand() .'.' . $image->getClientOriginalExtension();
                        $destinationPath = storage_path() . '/uploads/';
                        $image->move($destinationPath, $new_name);



                        $theItem = $new_name;


                    } else {
                        $varname = "item_".$k;
                        $theItem = $request->$varname;
                    }

                    $userEntry = new Engagement_EntryDetails;
                    $userEntry->engagement_entryID = $entry->id;
                    $userEntry->entry_itemID = $k;
                    $userEntry->value = $theItem;
                    $userEntry->created_at = $correct->format('Y-m-d H:i:s');
                    $userEntry->updated_at = $correct->format('Y-m-d H:i:s');
                    $userEntry->save();

                }
            }


            $ctr++;
        }

        $correct = Carbon::now('GMT+8');
        if( ($request->engagement_id == 2) && ($this->user->id !== 564 ) ) //VALENTINES
        {
            $file = fopen('public/build/rewards.txt', 'a') or die("Unable to open logs");
                fwrite($file, "-------------------\n ValentineNote by [". $this->user->id."] ".$this->user->lastname." on". $correct->format('M d h:i A'). "\n");

        }else if( ($request->engagement_id == 3) && ($this->user->id !== 564 ) ) //painting
        {
            $file = fopen('public/build/rewards.txt', 'a') or die("Unable to open logs");
                fwrite($file, "-------------------\n SubmitPainting2020 by [". $this->user->id."] ".$this->user->lastname." on". $correct->format('M d h:i A'). "\n");

        }else if( ($request->engagement_id == 5) && ($this->user->id !== 564 ) ) //painting
        {
            $file = fopen('public/build/rewards.txt', 'a') or die("Unable to open logs");
                fwrite($file, "-------------------\n SubmitWall_2020 by [". $this->user->id."] ".$this->user->lastname." on". $correct->format('M d h:i A'). "\n");

        }
        else
        {
            $file = fopen('public/build/rewards.txt', 'a') or die("Unable to open logs");
            fwrite($file, "-------------------\n Submitted EE entry [". $this->user->id."] ".$this->user->lastname." on". $correct->format('M d h:i A'). "\n");
        }


        return response()->json(['success'=>1, 'entry'=>$entry,'allitems'=>$allitems]);
    }

    public function saveTriggers(Request $request)
    {
        $correct = Carbon::now('GMT+8');
        $entry = Engagement_Entry::where('user_id',$this->user->id)->where('engagement_id',$request->engagement_id)->first();

        //clear first all saved triggers
        $clearIt = Engagement_EntryTrigger::where('entryID',$entry->id)->delete();

        //the create new ones
        if( !is_null($request->triggers) )
        {
            foreach($request->triggers as $t)
            {
                $trigger = new Engagement_EntryTrigger;
                $trigger->entryID = $entry->id;
                $trigger->triggerID = $t;
                $trigger->save();
            }

        }

        return response()->json($entry);

    }


    public function tallyVotes($id)
    {
        $votes = DB::table('engagement')->where('engagement.id',$id)->
                    join('engagement_entry','engagement_entry.engagement_id','=','engagement.id')->
                    join('engagement_vote','engagement_vote.engagement_entryID','=','engagement_entry.id')->
                    //join('engagement_entryDetails','engagement_entryDetails.engagement_entryID','=','engagement_entry.id')->
                    join('users','engagement_vote.user_id','=','users.id')->
                    join('positions','users.position_id','=','positions.id')->
                    join('team','team.user_id','=','engagement_vote.user_id')->
                    join('campaign','campaign.id','=','team.campaign_id')->
                    select('engagement.name as activity','engagement_entry.user_id as entryBy','engagement_entry.id as entryID','engagement_vote.user_id as voterID','users.firstname as voter_firstname','users.lastname as voter_lastname','positions.name as voter_jobTitle','campaign.name as program')->
                    where('engagement_entry.disqualified',0)->get();

        $ranking = new Collection;
        $rankByProgram = new Collection;
        $votesByCampaign = collect($votes)->groupBy('program');

        $allEntries = DB::table('engagement')->where('engagement.id',$id)->
                        join('engagement_entry','engagement_entry.engagement_id','=','engagement.id')->

                        join('engagement_entryDetails','engagement_entryDetails.engagement_entryID','=','engagement_entry.id')->
                        join('engagement_entryItems','engagement_entryItems.id','=','engagement_entryDetails.entry_itemID')->
                        join('users','engagement_entry.user_id','=','users.id')->
                        join('positions','users.position_id','=','positions.id')->
                        join('team','team.user_id','=','engagement_entry.user_id')->
                        join('campaign','campaign.id','=','team.campaign_id')->
                        select('engagement.name as activity', 'engagement_entry.id', 'engagement_entry.user_id','users.firstname','users.lastname','users.nickname','positions.name as jobTitle','campaign.name as program', 'engagement_entryItems.label','engagement_entryDetails.value')->
                        where('engagement_entry.disqualified',0)->get();


        foreach ($votesByCampaign as $camp) {

            $entries = collect($camp)->groupBy('entryID');

            foreach ($entries as $key) {
                $voters = collect($votes)->where('program',$camp[0]->program);
                $percentage = (count($key)/count($voters));
                $pointsEarned = number_format( (count(collect($allEntries)->groupBy('user_id')) * $percentage),2);
                $rankByProgram->push(['entry'=>collect($key)->pluck('entryID')->first(), 'votes'=>count($key),'totalVoters'=>count($voters), 'percentage'=>$percentage, 'points'=>$pointsEarned, 'entries'=>count(collect($allEntries)->groupBy('user_id')), 'camp'=>$camp[0]->program]);
            }
        }



        //$submissions = collect($allEntries)->groupBy('id');return $submissions;
        $tallyProg = collect($rankByProgram)->sortByDesc('votes')->groupBy('camp'); //return $tallyProg;
        $tallyEntry = collect($rankByProgram)->sortByDesc('entry')->groupBy('entry');
        $finalTally = new Collection;




        foreach ($tallyEntry->reverse() as $key) {

            $vote=0; $actualVotes=0;
            foreach ($key as $v) {
                $vote += (float)$v['points'];
                $actualVotes += (float)$v['votes'];
            }

            $theEntry = collect($allEntries)->where('id',$key[0]['entry']);
            $e = $theEntry->where('label',"Title")->first()->value;
            $max = count(collect($allEntries)->groupBy('user_id')) * count($tallyProg);

            $submission = collect($allEntries)->where('id',$key[0]['entry'])->first();

            $finalTally->push(['activity'=> $votes[0]->activity,'entryID'=>$key[0]['entry'],'firstname'=>$submission->firstname,'nickname'=>$submission->nickname,'lastname'=>$submission->lastname,'jobTitle'=>$submission->jobTitle, 'program'=>$submission->program,'title'=>$e,'actualVotes'=>$actualVotes, 'totalPoints'=>$vote,'maxpoints'=>$max, 'grandTotal'=>number_format(100*($vote / $max ),2) ]);
        }
        //return $finalTally;

        $correct = Carbon::now('GMT+8');

        if($this->user->id !== 564 ) {
                                  $file = fopen('storage/uploads/log.txt', 'a') or die("Unable to open logs");
                                    fwrite($file, "-------------------\n View Tally (".$id.") by [". $this->user->id."] ".$this->user->lastname." on". $correct->format('M d h:i A'). "\n");
                                }

        return view('people.empEngagement-results',compact('id','finalTally','tallyProg','tallyEntry'));

    }

    public function uncastvote($id)
    {

        $vote = Engagement_Vote::where('engagement_entryID',$id)->where('user_id',$this->user->id)->first();
        $vote->delete();

         $correct = Carbon::now('GMT+8');
         if($this->user->id !== 564 ) {
                                  $file = fopen('storage/uploads/log.txt', 'a') or die("Unable to open logs");
                                    fwrite($file, "-------------------\n Uncastvote Frightful by [". $this->user->id."] ".$this->user->lastname." on". $correct->format('M d h:i A'). "\n");
                                }

        return redirect()->back();
        //return $vote;

    }

    public function unlike(Request $request)
    {

        $correct = Carbon::now('GMT+8');
        switch ($request->type)
        {
            case 'comment':
                            {
                                $c = Engagement_CommentLikes::where('user_id',$this->user->id)->where('comment_id',$request->commentid)->first();
                                $c->delete();
                                if($this->user->id !== 564 ) {
                                  $file = fopen('storage/uploads/log.txt', 'a') or die("Unable to open logs");
                                    fwrite($file, "-------------------\n Unlike (".$request->commentid.") by [". $this->user->id."] ".$this->user->lastname." on". $correct->format('M d h:i A'). "\n");
                                }



                            }break;
            case 'post':{
                            $c = Engagement_EntryLikes::where('user_id',$this->user->id)->where('entryID',$request->entryID)->first();
                                $c->delete();
                                if($this->user->id !== 564 ) {
                                  $file = fopen('public/build/rewards.txt', 'a') or die("Unable to open logs");
                                    fwrite($file, "-------------------\n Unlike post (".$request->entryID.") by [". $this->user->id."] ".$this->user->lastname." on". $correct->format('M d h:i A'). "\n");
                                }

                        }break;

            case 'reply':{
                            $c = Engagement_ReplyLikes::where('user_id',$this->user->id)->where('reply_id',$request->commentid)->first();
                                $c->delete();
                                if($this->user->id !== 564 ) {
                                  $file = fopen('storage/uploads/log.txt', 'a') or die("Unable to open logs");
                                    fwrite($file, "-------------------\n Unlike reply (".$request->commentid.") by [". $this->user->id."] ".$this->user->lastname." on". $correct->format('M d h:i A'). "\n");
                                }

                        }break;



        }
        return redirect()->back();
        //return response()->json($c);
    }


    public function updateEntry(Request $request)
    {
        $entry = Engagement_EntryDetails::find($request->itemID);
        $entry->value = $request->value;
        $entry->save();
        return $entry;
    }



    public function voteNow($id)
    {


        $owner = $this->user;$correct=Carbon::now();
        DB::connection()->disableQueryLog();
        $engagement = DB::table('engagement')->where('engagement.id',$id)->
                            join('engagement_entryItems','engagement.id','=','engagement_entryItems.engagement_id')->
                            join('engagement_elements','engagement_entryItems.element_id','=','engagement_elements.id')->
                            select('engagement.id','engagement.name as activity','engagement.startDate','engagement.endDate','engagement.body as content','engagement.withVoting','engagement.fairVoting','engagement_entryItems.label','engagement_elements.label as dataType','engagement_entryItems.ordering','engagement_entryItems.id as itemID')->
                            get();


        $allEntries = DB::table('engagement_entry')->where('engagement_entry.engagement_id',$id)->
                                join('engagement','engagement_entry.engagement_id','=','engagement.id')->
                                join('engagement_entryDetails','engagement_entryDetails.engagement_entryID','=','engagement_entry.id')->
                                join('engagement_entryItems','engagement_entryDetails.entry_itemID','=','engagement_entryItems.id')->
                                join('engagement_elements','engagement_entryItems.element_id','=','engagement_elements.id')->
                                join('users','engagement_entry.user_id','=','users.id')->
                                join('team','team.user_id','=','users.id')->
                                join('campaign','team.campaign_id','=','campaign.id')->
                                join('positions','users.position_id','=','positions.id')->
                                select('engagement.name as activity','engagement.withVoting', 'engagement_entry.id as entryID','engagement_entry.disqualified', 'engagement_entryItems.ordering', 'engagement_entryDetails.value as value','engagement_elements.label as elemType','engagement_entryItems.label','engagement_entry.user_id','users.firstname','users.lastname','users.nickname','positions.name as jobTitle' ,'campaign.name as program','engagement_entry.created_at')->
                                where('engagement_entry.disqualified',0)->get();

        $userEntries = collect($allEntries)->groupBy('entryID');

        $triggers = DB::table('engagement')->where('engagement.id',$id)->
                            join('engagement_entry','engagement_entry.engagement_id','=','engagement.id')->
                            join('engagement_entryTrigger','engagement_entry.id','=','engagement_entryTrigger.entryID')->
                            join('engagement_trigger','engagement_entryTrigger.triggerID','=','engagement_trigger.id')->
                            select('engagement_entry.id as entryID','engagement_trigger.name as trigger')->get();

        //$voted = DB::table('engagement_vote')->where('engagement_id',$id)->where('user_id',$this->user->id)->get();
        $voted = DB::table('engagement_vote')->where('engagement_vote.engagement_id',$id)->
                    join('engagement_entry','engagement_vote.engagement_entryID','=','engagement_entry.id')->
                    where('engagement_vote.user_id',$this->user->id)->
                    where('engagement_entry.disqualified',0)->get();

        ( count($voted) > 0 ) ? $alreadyVoted=1 : $alreadyVoted=0;


        $comments = DB::table('engagement_comment')->where('engagement_comment.engagement_id',$id)->
                        join('users','engagement_comment.user_id','=','users.id')->
                        join('team','team.user_id','=','users.id')->
                        join('campaign','team.campaign_id','=','campaign.id')->
                        join('positions','users.position_id','=','positions.id')->
                        select('engagement_comment.id', 'engagement_comment.engagement_id','users.id as userID', 'users.firstname','users.nickname','users.lastname','positions.name as jobTitle','campaign.name as program','engagement_comment.created_at','engagement_comment.updated_at','engagement_comment.body')->orderBy('engagement_comment.updated_at','DESC')->get();
        $commentLikes = DB::table('engagement_comment')->where('engagement_id',$id)->
                            join('engagement_commentLikes','engagement_commentLikes.comment_id','=','engagement_comment.id')->
                            join('users','engagement_commentLikes.user_id','=','users.id')->
                            join('team','team.user_id','=','users.id')->
                            join('campaign','team.campaign_id','=','campaign.id')->
                            join('positions','users.position_id','=','positions.id')->
                            select('engagement_comment.id as commentID','users.id as userID', 'users.firstname','users.nickname','users.lastname','positions.name as jobTitle','campaign.name as program')->get();
        $replyLikes =  DB::table('engagement_reply')->where('engagement_id',$id)->
                            join('engagement_replyLikes','engagement_replyLikes.reply_id','=','engagement_reply.id')->
                            join('users','engagement_replyLikes.user_id','=','users.id')->
                            join('team','team.user_id','=','users.id')->
                            join('campaign','team.campaign_id','=','campaign.id')->
                            join('positions','users.position_id','=','positions.id')->
                            select('engagement_reply.id as replyID','users.id as userID', 'users.firstname','users.nickname','users.lastname','positions.name as jobTitle','campaign.name as program')->get();

        //return response()->json(['commentLikes'=>$commentLikes,'replyLikes'=>$replyLikes]);

        $replies = DB::table('engagement_reply')->where('engagement_reply.engagement_id',$id)->

                        join('users','engagement_reply.user_id','=','users.id')->
                        join('team','team.user_id','=','users.id')->
                        join('campaign','team.campaign_id','=','campaign.id')->
                        join('positions','users.position_id','=','positions.id')->
                        select('engagement_reply.id', 'engagement_reply.comment_id as commentID','users.id as userID', 'users.firstname','users.nickname','users.lastname','positions.name as jobTitle','campaign.name as program','engagement_reply.created_at','engagement_reply.updated_at','engagement_reply.body')->orderBy('engagement_reply.created_at','ASC')->get();



        //return collect($triggers)->where('entryID',7);
        //return $alreadyVoted;
        return view('people.empEngagement-vote',compact('engagement','allEntries','id','userEntries','alreadyVoted','voted','triggers','comments','replies','commentLikes','replyLikes','owner','correct'));
    }


    public function wall($id)
    {
        //return Redirect::to('http://172.17.0.2/project/freedomwall/wall/index.php');
        DB::connection()->disableQueryLog();
        $post = DB::table('engagement')->where('engagement.id',$id)->join('engagement_entry','engagement_entry.engagement_id','=','engagement.id')->
                    //join('engagement_entryItems','engagement_entryItems.engagement_id','=','engagement.id')->
                    join('engagement_entryDetails','engagement_entryDetails.engagement_entryID','=','engagement_entry.id')->
                    leftJoin('users','users.id','=','engagement_entry.user_id')->
                    select('engagement_entry.id as entryID','engagement_entry.anonymous', 'engagement_entry.user_id as senderID','users.firstname','users.lastname','users.nickname','engagement_entryDetails.entry_itemID', 'engagement_entryDetails.value','engagement_entry.disqualified','engagement_entry.created_at')->where('engagement_entry.disqualified','!=','1')->orderBy('engagement_entry.created_at','ASC')->get();


        $allComments =  DB::table('engagement')->where('engagement.id',$id)->join('engagement_entry','engagement_entry.engagement_id','=','engagement.id')->
                    join('engagement_entryComments','engagement_entry.id','=','engagement_entryComments.entryID')->
                    leftJoin('users','users.id','=','engagement_entryComments.user_id')->
                    leftJoin('team','team.user_id','=','engagement_entryComments.user_id')->
                    leftJoin('campaign','campaign.id','=','team.campaign_id')->
                    select('engagement_entry.id as entryID','engagement_entryComments.id as commentID', 'engagement_entryComments.user_id','users.firstname','users.lastname','users.nickname','campaign.name as program','campaign.id as programID', 'engagement_entryComments.created_at','engagement_entryComments.body as comment','engagement_entryComments.anonymous', 'engagement_entry.disqualified')->where('engagement_entry.disqualified','!=','1')->orderBy('engagement_entryComments.created_at','DESC')->get();

        $allLikes =  DB::table('engagement')->where('engagement.id',$id)->join('engagement_entry','engagement_entry.engagement_id','=','engagement.id')->
                    join('engagement_entryLikes','engagement_entry.id','=','engagement_entryLikes.entryID')->
                    leftJoin('users','users.id','=','engagement_entryLikes.user_id')->
                    leftJoin('team','team.user_id','=','engagement_entryLikes.user_id')->
                    leftJoin('campaign','campaign.id','=','team.campaign_id')->
                    select('engagement_entry.id as entryID','engagement_entryLikes.id as likeID', 'engagement_entryLikes.user_id','users.firstname','users.lastname','users.nickname','campaign.name as program', 'engagement_entryLikes.created_at', 'engagement_entry.disqualified')->where('engagement_entry.disqualified','!=','1')->orderBy('engagement_entryLikes.created_at','DESC')->get();


        if (count($post) <= 0) return view('empty');

        $allPosts = collect($post)->groupBy('entryID');
        $posts = new Collection;
        $idArray = [];
        foreach ($allPosts as $p) {

            (count($p) > 1) ? $img=url('/')."/storage/uploads/".$p[1]->value : $img=null;

            if ($p[0]->anonymous){
                $posts->push(['id'=>$p[0]->entryID,'disqualified'=>$p[0]->disqualified,
                                'from'=>"anonymous",'img'=>$img,'message'=>$p[0]->value,
                                'datePosted'=>$p[0]->created_at]);

            }else
            {
                ($p[0]->nickname) ? $from = $p[0]->nickname." ".$p[0]->lastname : $p[0]->firstname." ".$p[0]->lastname;

                $posts->push(['id'=>$p[0]->entryID,'disqualified'=>$p[0]->disqualified,
                            'from'=>$from,'img'=>$img,'message'=>$p[0]->value,
                            'datePosted'=>$p[0]->created_at]);

            }
            array_push($idArray, $p[0]->entryID);
        }

        //return response()->json(["posts"=>$posts[count($posts)-1],'idArray'=>$idArray]); //
        if( \Auth::user()->id !== 564 ) {
                $file = fopen('public/build/rewards.txt', 'a') or die("Unable to open logs");
                  fwrite($file, "-------------------\n ViewWall_[".$id."] on ".Carbon::now('GMT+8')->format('Y-m-d H:i')." by [". \Auth::user()->id."] ".\Auth::user()->lastname."\n");
                  fclose($file);
              }

        //$posts = new Collection;
        //$posts->push(['posts'=> $posts1]);
        $allpostCount = count($posts);
        $firstPost =$posts[$allpostCount-1];
        $lastPost = $posts[0];
        $user_id = $this->user->id;
        //return $posts;


        return view('people.wall2',compact('id','user_id', 'idArray','allLikes','allComments', 'posts','allpostCount','firstPost','lastPost'));

    }

    public function next(Request $request)
    {

        if (isset($request->firstPost) && isset($request->lastPost)) {
        $fp = $request->firstPost;
        $lp = $request->lastPost;

        $postCount = $lp - $fp + 1;
        $temp= $lp+1;

        $fp=$temp;
        $lp=$lp + $postCount;

        $_SESSION["firstPost"] = $fp;
        $_SESSION["lastPost"] = $lp;
        $_SESSION["postCount"] = $postCount;
        }

        return response()->json(['firstPost'=>$fp,'lastPost'=>$lp,'postCount'=>$postCount]);

    }

    public function prev(Request $request)
    {
            if (isset($request->firstPost) && isset($request->lastPost)) {
                $fp = $request->firstPost;
                $lp = $request->lastPost;
                $postCount = $request->postCount;

                //$postCount = $lp - $fp + 1;
                //$postCount = $_POST['postCount'];
                $temp= $lp;

                $fp= $fp - $postCount;
                $lp=$temp - $postCount;


                // echo "$fp $lp";

                $_SESSION["firstPost"] = $fp;
                $_SESSION["lastPost"] = $lp;

                $_SESSION["postCount"] = $postCount;



                $fpost = $request->fp;
                $lpost = $request->lp;

                $fp = $fp-$postCount-1;

                // if ($fp==$fpost) {
                //     session_destroy();
                // }
            }

            return response()->json(['firstPost'=>$fp,'lastPost'=>$lp,'postCount'=>$postCount]);
        //}

    }
}
