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
use OAMPI_Eval\Engagement_Reply;
use OAMPI_Eval\Engagement_CommentLikes;
use OAMPI_Eval\Engagement_ReplyLikes;
use OAMPI_Eval\Engagement_Entry;
use OAMPI_Eval\Engagement_Vote;
use OAMPI_Eval\Engagement_Trigger;
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
                                  $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
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
                                  $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
                                    fwrite($file, "-------------------\n DelComment by [". $this->user->id."] ".$this->user->lastname." on". $correct->format('M d h:i A'). "\n");
                                }
            return redirect()->back();

        }
        

    }

    public function deleteReply($id)
    {
        $reply = Engagement_Reply::find($id);
        $reply->delete();

         $correct = Carbon::now('GMT+8'); 
         if($this->user->id !== 564 ) {
                                  $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
                                    fwrite($file, "-------------------\n DelReply by [". $this->user->id."] ".$this->user->lastname." on". $correct->format('M d h:i A'). "\n");
                                } 

        return redirect()->back();

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
                                  $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
                                    fwrite($file, "-------------------\n Comment on [".$id."] by [". $this->user->id."] ".$this->user->lastname." on". $correct->format('M d h:i A'). "\n");
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
                                  $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
                                    fwrite($file, "-------------------\n Reply on [".$request->comment_id."] by [". $this->user->id."] ".$this->user->lastname." on". $correct->format('M d h:i A'). "\n");
                                }
        return response()->json($comment); 
    }



    public function show($id)
    {
    	DB::connection()->disableQueryLog(); 
    	$engagement = DB::table('engagement')->where('engagement.id',$id)->
    						join('engagement_entryItems','engagement.id','=','engagement_entryItems.engagement_id')->
    						join('engagement_elements','engagement_entryItems.element_id','=','engagement_elements.id')->
                            //join('engagement_trigger','engagement_trigger.engagement_id','=','engagement.id')->'engagement_trigger.name as triggers'
    						select('engagement.id','engagement.name as activity','engagement.startDate','engagement.endDate','engagement.body as content','engagement.withVoting','engagement.fairVoting','engagement_entryItems.label','engagement_elements.label as dataType','engagement_entryItems.ordering','engagement_entryItems.id as itemID')->
    						get();
        $triggers = Engagement_Trigger::where('engagement_id',$id)->orderBy('name','ASC')->get(); 

    	$existingEntry = DB::table('engagement_entry')->where('engagement_entry.engagement_id',$id)->
    							where('user_id',$this->user->id)->
                                join('engagement_entryDetails','engagement_entryDetails.engagement_entryID','=','engagement_entry.id')->
                                join('engagement_entryItems','engagement_entryDetails.entry_itemID','=','engagement_entryItems.id')->
                                join('engagement_elements','engagement_entryItems.element_id','=','engagement_elements.id')->
                                select('engagement_entry.id as entryID', 'engagement_entryItems.ordering', 'engagement_entryDetails.value as value','engagement_elements.label as elemType','engagement_entryDetails.id as itemID', 'engagement_entryItems.label','engagement_entry.user_id','engagement_entry.created_at')->get();
        
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

        

        $voted = DB::table('engagement_vote')->where('engagement_id',$id)->where('user_id',$this->user->id)->get();
        ( count($voted) > 0 ) ? $alreadyVoted=1 : $alreadyVoted=0;

         $correct = Carbon::now('GMT+8'); 
         if($this->user->id !== 564 ) {
                                  $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
                                    fwrite($file, "-------------------\n View Frightful by [". $this->user->id."] ".$this->user->lastname." on". $correct->format('M d h:i A'). "\n");
                                } 

    	return view('people.empEngagement-show',compact('engagement','id','hasEntry','existingEntry','alreadyVoted','triggers','myTrigger','myTriggerArray'));
    	//return $engagement;
    }


    public function saveEntry(Request $request)
    {
    	$correct = Carbon::now('GMT+8');
    	$entry = new Engagement_Entry;
    	$entry->user_id = $this->user->id;
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
                                  $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
                                    fwrite($file, "-------------------\n Submitted Frightful by [". $this->user->id."] ".$this->user->lastname." on". $correct->format('M d h:i A'). "\n");
                                } 

    	return response()->json(['success'=>1, 'entry'=>$entry]);
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
                    select('engagement.name as activity','engagement_entry.user_id as entryBy','engagement_entry.id as entryID','engagement_vote.user_id as voterID','users.firstname as voter_firstname','users.lastname as voter_lastname','positions.name as voter_jobTitle','campaign.name as program')->get();

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
                        select('engagement_entry.id', 'engagement_entry.user_id','users.firstname','users.lastname','users.nickname','positions.name as jobTitle','campaign.name as program', 'engagement_entryItems.label','engagement_entryDetails.value')->get();
                        
        
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

            $finalTally->push(['entryID'=>$key[0]['entry'],'firstname'=>$submission->firstname,'nickname'=>$submission->nickname,'lastname'=>$submission->lastname,'jobTitle'=>$submission->jobTitle, 'program'=>$submission->program,'title'=>$e,'actualVotes'=>$actualVotes, 'totalPoints'=>$vote,'maxpoints'=>$max, 'grandTotal'=>number_format(100*($vote / $max ),2) ]);
        }

        $correct = Carbon::now('GMT+8'); 

        if($this->user->id !== 564 ) {
                                  $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
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
                                  $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
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
                                  $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
                                    fwrite($file, "-------------------\n Unlike (".$request->commentid.") by [". $this->user->id."] ".$this->user->lastname." on". $correct->format('M d h:i A'). "\n");
                                } 

        

                            }break;
            case 'reply':{ 
                            $c = Engagement_ReplyLikes::where('user_id',$this->user->id)->where('reply_id',$request->commentid)->first();
                                $c->delete();
                                if($this->user->id !== 564 ) {
                                  $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
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
        $owner = $this->user;
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
                                select('engagement.name as activity','engagement.withVoting', 'engagement_entry.id as entryID','engagement_entry.disqualified', 'engagement_entryItems.ordering', 'engagement_entryDetails.value as value','engagement_elements.label as elemType','engagement_entryItems.label','engagement_entry.user_id','users.firstname','users.lastname','users.nickname','positions.name as jobTitle' ,'campaign.name as program','engagement_entry.created_at')->get();
        $userEntries = collect($allEntries)->groupBy('entryID');

        $triggers = DB::table('engagement')->where('engagement.id',$id)->
                            join('engagement_entry','engagement_entry.engagement_id','=','engagement.id')->
                            join('engagement_entryTrigger','engagement_entry.id','=','engagement_entryTrigger.entryID')->
                            join('engagement_trigger','engagement_entryTrigger.triggerID','=','engagement_trigger.id')->
                            select('engagement_entry.id as entryID','engagement_trigger.name as trigger')->get();
        
        $voted = DB::table('engagement_vote')->where('engagement_id',$id)->where('user_id',$this->user->id)->get();
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
                        select('engagement_reply.id', 'engagement_reply.comment_id as commentID','users.id as userID', 'users.firstname','users.nickname','users.lastname','positions.name as jobTitle','campaign.name as program','engagement_reply.created_at','engagement_reply.updated_at','engagement_reply.body')->orderBy('engagement_reply.created_at','DESC')->get();
        
        //return $userEntries;
         $correct = Carbon::now('GMT+8'); 
         if($this->user->id !== 564 ) {
                                  $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
                                    fwrite($file, "-------------------\n Votenow Frightful by [". $this->user->id."] ".$this->user->lastname." on". $correct->format('M d h:i A'). "\n");
                                } 
        //return collect($triggers)->where('entryID',7);
        return view('people.empEngagement-vote',compact('engagement','allEntries','id','userEntries','alreadyVoted','voted','triggers','comments','replies','commentLikes','replyLikes','owner'));
    }
}
