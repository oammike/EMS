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


    public function updateEntry(Request $request)
    {
        $entry = Engagement_EntryDetails::find($request->itemID);
        $entry->value = $request->value;
        $entry->save();
        return $entry;
    }



    public function voteNow($id)
    {
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
                                select('engagement.name as activity','engagement.withVoting', 'engagement_entry.id as entryID', 'engagement_entryItems.ordering', 'engagement_entryDetails.value as value','engagement_elements.label as elemType','engagement_entryItems.label','engagement_entry.user_id','users.firstname','users.lastname','users.nickname','positions.name as jobTitle' ,'campaign.name as program','engagement_entry.created_at')->get();
        $userEntries = collect($allEntries)->groupBy('entryID');

        $triggers = DB::table('engagement')->where('engagement.id',$id)->
                            join('engagement_entry','engagement_entry.engagement_id','=','engagement.id')->
                            join('engagement_entryTrigger','engagement_entry.id','=','engagement_entryTrigger.entryID')->
                            join('engagement_trigger','engagement_entryTrigger.triggerID','=','engagement_trigger.id')->
                            select('engagement_entry.id as entryID','engagement_trigger.name as trigger')->get();
        
        $voted = DB::table('engagement_vote')->where('engagement_id',$id)->where('user_id',$this->user->id)->get();
        ( count($voted) > 0 ) ? $alreadyVoted=1 : $alreadyVoted=0;
        
        //return $userEntries;
         $correct = Carbon::now('GMT+8'); 
         if($this->user->id !== 564 ) {
                                  $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
                                    fwrite($file, "-------------------\n Votenow Frightful by [". $this->user->id."] ".$this->user->lastname." on". $correct->format('M d h:i A'). "\n");
                                } 
        //return collect($triggers)->where('entryID',7);
        return view('people.empEngagement-vote',compact('engagement','allEntries','id','userEntries','alreadyVoted','voted','triggers'));
    }
}
