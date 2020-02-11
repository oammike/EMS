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
        // DB::select( DB::raw("SELECT users.id, users.status_id, users.firstname,users.lastname,users.nickname,DATE_FORMAT(users.birthday, '%m-%d-%Y')as birthday,users.dateHired,positions.name as jobTitle, campaign.id as campID, campaign.name as program, users.employeeNumber FROM users INNER JOIN team ON team.user_id = users.id INNER JOIN campaign ON team.campaign_id = campaign.id INNER JOIN positions ON users.position_id = positions.id WHERE MONTH(users.birthday) = :m AND DAY(users.birthday) >= :d AND DAY(users.birthday) <= :dt AND  users.status_id != 6 AND users.status_id != 7 AND users.status_id != 8 AND users.status_id != 9  ORDER BY birthday ASC"), array(
        //              'm' => $m_from,
        //              'd' => $d_from,
        //              'dt' =>$d_to
        //            ));

        // $post = DB::select( DB::raw("SELECT engagement.id as engagementID, engagement_elements.id as elementID, engagement_elements.label as elementLabel, engagement_entryItems.id as itemID, engagement_entryItems.label as entryLabel, engagement_entryItems.element_id as elementID, engagement_entryItems.ordering, engagement_entry.id as postID, engagement_entry.user_id as postedBy, engagement_entry.anonymous, engagement_entryDetails.value FROM engagement  JOIN engagement_entry ON engagement_entry.id = engagement.id  JOIN engagement_entryItems ON engagement_entryItems.engagement_id = engagement.id  JOIN engagement_entryDetails ON engagement_entryDetails.engagement_entryID = engagement_entry.id  JOIN engagement_elements ON engagement_elements.id = engagement_entryItems.element_id   WHERE engagement.id = :id"), array(
        //              'id' => $id
        //            ));

        $post = DB::table('engagement')->where('engagement.id',$id)->join('engagement_entry','engagement_entry.engagement_id','=','engagement.id')->
        			//join('engagement_entryItems','engagement_entryItems.engagement_id','=','engagement.id')->
        			join('engagement_entryDetails','engagement_entryDetails.engagement_entryID','=','engagement_entry.id')->
        			leftJoin('users','users.id','=','engagement_entry.user_id')->
        			select('engagement_entry.id as entryID','engagement_entry.anonymous', 'engagement_entry.user_id as senderID','users.firstname','users.lastname','users.nickname','engagement_entryDetails.entry_itemID', 'engagement_entryDetails.value')->get();
        			//'engagement_entryItems.label','engagement_entryItems.ordering',

        $allPosts = collect($post)->groupBy('entryID');
        $posts = new Collection;
        foreach ($allPosts as $p) {
        	
        	if ($p[0]->anonymous){

        		if(count($p) > 1) //meaning may pic
        		{
        			$posts->push(['id'=>$p[0]->entryID,'from'=>"anonymous",'img'=>$p[1]->value,'message'=>$p[0]->value]);
        			
        		}else
        		{
        		    $posts->push(['id'=>$p[0]->entryID,'from'=>"anonymous",'img'=>null,'message'=>$p[0]->value]);

        		}

        		

        	}else
        	{
        		if(count($p) > 1) //meaning may pic
        		{
        			$posts->push(['id'=>$p[0]->entryID,'from'=>$p[0]->firstname." ".$p[0]->lastname,'img'=>$p[1]->value,'message'=>$p[0]->value]);
        			
        		}else
        		{
        		    $posts->push(['id'=>$p[0]->entryID,'from'=>$p[0]->firstname." ".$p[0]->lastname,'img'=>null,'message'=>$p[0]->value]);

        		}


        	}
        }
       
        // collect(DB::table('engagement')->where('engagement.id',$id)->join('engagement_entry','engagement_entry.engagement_id','=','engagement.id')->
        // 			leftJoin('engagement_entryItems','engagement_entryItems.engagement_id','=','engagement.id')->
        // 			leftJoin('engagement_entryDetails','engagement_entryDetails.engagement_entryID','=','engagement_entry.id')->
        // 			leftJoin('users','users.id','=','engagement_entry.user_id')->
        // 			select('users.firstname','users.lastname','engagement_entry.id as entryID','engagement_entryDetails.value')->get())->groupBy('entryID');
        
        return $posts;
    }

   
}
