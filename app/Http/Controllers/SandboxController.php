<?php

namespace OAMPI_Eval\Http\Controllers;

use Illuminate\Http\Request;
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

use Illuminate\Support\Facades\Auth;
use \DB;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class SandboxController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
    $this->user =  User::find(Auth::user()->id);
    $this->allowed_users = [564, 83];
  }

  public function index()
  {

    if ( !in_array($this->user->id, $this->allowed_users, true)) {
      return view('access-denied');
    }
    $id = 51;

        $post = DB::table('engagement')->where('engagement.id',$id)->join('engagement_entry','engagement_entry.engagement_id','=','engagement.id')->
                    //join('engagement_entryItems','engagement_entryItems.engagement_id','=','engagement.id')->
                    join('engagement_entryDetails','engagement_entryDetails.engagement_entryID','=','engagement_entry.id')->
                    leftJoin('users','users.id','=','engagement_entry.user_id')->
                    select('engagement_entry.id as entryID','engagement_entry.anonymous', 'engagement_entry.user_id as senderID','users.firstname','users.lastname','users.nickname','engagement_entryDetails.entry_itemID', 'engagement_entryDetails.value','engagement_entry.disqualified','engagement_entry.created_at','engagement.bg','engagement.bgcolor')->where('engagement_entry.disqualified','!=','1')->orderBy('engagement_entry.created_at','ASC')->get();


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

            if($id >=41) //valentines 2021 and beyond, new location na
            {
                (count($p) > 1) ? $img="https://internal.openaccess.bpo/EMS/public/storage/uploads/".$p[1]->value : $img=null;

            }else{
                (count($p) > 1) ? $img=url('/')."/storage/uploads/".$p[1]->value : $img=null;

            }


            if ($p[0]->anonymous){
                $posts->push(['id'=>$p[0]->entryID,'disqualified'=>$p[0]->disqualified,
                                'from'=>"anonymous",'img'=>$img,'message'=>$p[0]->value,
                                'datePosted'=>$p[0]->created_at]);

            }else
            {
                ($p[0]->nickname) ? $from = $p[0]->nickname." ".$p[0]->lastname : $from = $p[0]->firstname." ".$p[0]->lastname;

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

        // if($id == 41)
        //     return view('people.wallV',compact('id','user_id', 'idArray','allLikes','allComments', 'posts','allpostCount','firstPost','lastPost'));
        $background = $post[0]->bg;
        $bgcolor = $post[0]->bgcolor;

        if  ($background !== null)
            return view('sandbox.wallcustom',compact('id','user_id','background','bgcolor', 'idArray','allLikes','allComments', 'posts','allpostCount','firstPost','lastPost'));

        else
            return view('sandbox.wall2',compact('id','user_id', 'idArray','allLikes','allComments', 'posts','allpostCount','firstPost','lastPost'));
  }
}
