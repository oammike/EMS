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


class EngagementPublicController extends Controller
{
    protected $user;
    protected $user_dtr;
    use Traits\TimekeepingTraits;
    use Traits\UserTraits;

    public function __construct(Engagement $engagement)
    {
        $this->engagement = $engagement;
    }

    


    public function getAllPosts($id)
    {
       

        // $post = DB::select( DB::raw("SELECT engagement.id as engagementID, engagement_elements.id as elementID, engagement_elements.label as elementLabel, engagement_entryItems.id as itemID, engagement_entryItems.label as entryLabel, engagement_entryItems.element_id as elementID, engagement_entryItems.ordering, engagement_entry.id as postID, engagement_entry.user_id as postedBy, engagement_entry.anonymous, engagement_entryDetails.value FROM engagement  JOIN engagement_entry ON engagement_entry.id = engagement.id  JOIN engagement_entryItems ON engagement_entryItems.engagement_id = engagement.id  JOIN engagement_entryDetails ON engagement_entryDetails.engagement_entryID = engagement_entry.id  JOIN engagement_elements ON engagement_elements.id = engagement_entryItems.element_id   WHERE engagement.id = :id"), array(
        //              'id' => $id
        //            ));

        $post = DB::table('engagement')->where('engagement.id',$id)->join('engagement_entry','engagement_entry.engagement_id','=','engagement.id')->
                    //join('engagement_entryItems','engagement_entryItems.engagement_id','=','engagement.id')->
                    join('engagement_entryDetails','engagement_entryDetails.engagement_entryID','=','engagement_entry.id')->
                    leftJoin('users','users.id','=','engagement_entry.user_id')->
                    select('engagement_entry.id as entryID','engagement_entry.anonymous', 'engagement_entry.user_id as senderID','users.firstname','users.lastname','users.nickname','engagement_entryDetails.entry_itemID', 'engagement_entryDetails.value','engagement_entry.disqualified','engagement_entry.created_at')->
                    where('engagement_entry.disqualified','!=','1')->get();//->orderBy('engagement_entry.created_at','DESC')
                    //'engagement_entryItems.label','engagement_entryItems.ordering',

        $allPosts = collect($post)->groupBy('entryID');
        $posts = new Collection;
        foreach ($allPosts as $p) {

            (count($p) > 1) ? $img=url('/')."/storage/uploads/".$p[1]->value : $img=null;
            
            if ($p[0]->anonymous){
                $posts->push(['id'=>$p[0]->entryID,'from'=>"anonymous",'img'=>$img,'message'=>$p[0]->value]);

            }else
            {
                ($p[0]->nickname) ? $from = $p[0]->nickname." ".$p[0]->lastname : $p[0]->firstname." ".$p[0]->lastname;

                $posts->push(['id'=>$p[0]->entryID,'from'=>$from,'img'=>$img,'message'=>$p[0]->value]);

            }
        }
   
        return response()->json(["posts"=>$posts]);
    }

   
}
