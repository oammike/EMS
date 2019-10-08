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

    public function show($id)
    {
    	DB::connection()->disableQueryLog(); 
    	$engagement = DB::table('engagement')->where('engagement.id',$id)->
    						join('engagement_entryItems','engagement.id','=','engagement_entryItems.engagement_id')->
    						join('engagement_elements','engagement_entryItems.element_id','=','engagement_elements.id')->
    						select('engagement.id','engagement.name as activity','engagement.startDate','engagement.endDate','engagement.body as content','engagement.withVoting','engagement.fairVoting','engagement_entryItems.label','engagement_elements.label as dataType','engagement_entryItems.ordering','engagement_entryItems.id as itemID')->
    						get();

    	$existingEntry = DB::table('engagement_entry')->where('engagement_entry.engagement_id',$id)->
    							where('user_id',$this->user->id)->
                                join('engagement_entryDetails','engagement_entryDetails.engagement_entryID','=','engagement_entry.id')->
                                join('engagement_entryItems','engagement_entryDetails.entry_itemID','=','engagement_entryItems.id')->get();
    							//select('id')->get();
    	(count($existingEntry) > 0) ? $hasEntry=true : $hasEntry=false;
    	//return $existingEntry;

    	return view('people.empEngagement-show',compact('engagement','id','hasEntry','existingEntry'));
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

    	return response()->json(['success'=>1, 'entry'=>$entry]);
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
                                join('engagement_entryItems','engagement_entryDetails.entry_itemID','=','engagement_entryItems.id')->get();

        return $allEntries;
        return view('people.empEngagement-vote',compact('engagement'));
    }
}
