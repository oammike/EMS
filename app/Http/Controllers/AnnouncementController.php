<?php

namespace OAMPI_Eval\Http\Controllers;

use Illuminate\Http\Request;

use Carbon\Carbon;
use \DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use OAMPI_Eval\Http\Requests;
use OAMPI_Eval\User;
use OAMPI_Eval\Campaign;
use OAMPI_Eval\UserType;
use OAMPI_Eval\Team;
use OAMPI_Eval\ImmediateHead;
use OAMPI_Eval\Position;
use OAMPI_Eval\Status;
use OAMPI_Eval\ImmediateHead_Campaign;
use OAMPI_Eval\Announcement;
use Illuminate\Support\Facades\Storage;

class AnnouncementController extends Controller
{
  protected $slider_items = 50;

  public function __construct()
  {
    $this->middleware('auth');
    $this->user =  User::find(Auth::user()->id);
    $this->slider_items = 20;
  }

  public function create()
  {
    $campaign = ImmediateHead_Campaign::find(Team::where('user_id',$this->user->id)->first()->immediateHead_Campaigns_id);
    $campaign_id = $campaign->campaign_id;
    //nurses = clinical services = 71, marketing - 16
    //mpamero, mbambico, jmillares
    $allowed_users = [564, 83, 491];

    if ( !in_array($this->user->id, $allowed_users, true) && $campaign_id!=71 && $campaign_id!=16  ) return view('access-denied');

    $can_view_all = false;
    if(in_array($this->user->id, $allowed_users, true)){
      $can_view_all = true;
    }

    $slider_now = Carbon::now('GMT+8');
    $include_memo_scripts = TRUE;
    $include_jqueryform = TRUE;
    $include_ckeditor = TRUE;
    $announcements = Announcement::all();
    //return view('announcements.memo-create', compact('announcements','query','slider_now','include_memo_scripts','include_jqueryform','include_ckeditor'));
    return view('announcements.memo-create', compact('announcements','slider_now','include_memo_scripts','include_jqueryform','include_ckeditor'));
  }

  public function index()
  {
    $campaign = ImmediateHead_Campaign::find(Team::where('user_id',$this->user->id)->first()->immediateHead_Campaigns_id);
    $campaign_id = $campaign->campaign_id;
    $allowed_users = [564, 83, 491];
    $announcements = Announcement::all();

    if ( !in_array($this->user->id, $allowed_users, true) && $campaign_id!=71 && $campaign_id!=16  ) return view('access-denied');

    $can_view_all = false;
    if(in_array($this->user->id, $allowed_users, true)){
      $can_view_all = true;
    }

    return view('announcements.memo-index', compact('announcements'));
  }

  public function list(Request $request){
    $campaign = ImmediateHead_Campaign::find(Team::where('user_id',$this->user->id)->first()->immediateHead_Campaigns_id);
    $campaign_id = $campaign->campaign_id;
    $allowed_users = [564, 83, 491];

    if ( !in_array($this->user->id, $allowed_users, true) && $campaign_id!=71 && $campaign_id!=16  ) return view('access-denied');

    $can_view_all = false;
    if(in_array($this->user->id, $allowed_users, true)){
      $can_view_all = true;
    }

    \DB::enableQueryLog();

    $announcements = DB::table('announcement')
      ->select('announcement.id','announcement.user_id','announcement.template','announcement.title','announcement.decorative_title','announcement.isDraft','announcement.publishDate','announcement.publishExpire','announcement.showAlways','announcement.hidden', DB::raw("CONCAT(`users`.`firstname`, ' ', `users`.`lastname`) AS `author`"));

    if(!$can_view_all)
    {
      //show only posts made by this user
      $announcements
        ->where('user_id',$this->user->id)
        ->orWhere('author_campaign_id',$campaign_id);
    }

    $search = trim($request->input('search.value', ''));
    if( $search != '' ){

      $announcements
      ->where(function($query) use ($search){
        $query->where('title', 'like', "%".$search."%");
        $query->orWhere('decorative_title', 'like', "%".$search."%");
      });
    }


    $columns = array('title','template','publishDate','expiryDate','author');
    $skip = $request->input('start');
    $take = $request->input('length', 25);

    $fetch_all = $request->input('fetch_all', FALSE);
    $data = new \stdClass();
    $order = $request->input('order.0.column',0);

    $announcements->leftJoin('users', 'announcement.user_id', '=', 'users.id');

    if( $order != 0 ){
      $announcements->orderBy($columns[$order], $request->input('order.0.dir'));
    } else {
      $announcements->orderBy('publishDate', 'desc');
    }





    $announcements->skip($skip)->take($take);
    $data->data = $announcements->get();
    $data->iTotalRecords =  Announcement::count();
    $data->recordsFiltered = count($data->data);
    $data->query = DB::getQueryLog();

    return response()->json( $data );
  }


  public function store(Request $request)
    {
      $this->validate($request, [
        'mTitle'       => 'required',
        'mType'       => 'required|in:memo,post',
        'mBody' => 'required',
        'mPublishDate' => 'required|date',
        'mExpiryDate' => 'date',
        'isDraft' => 'boolean'
      ]);

      $campaign = ImmediateHead_Campaign::find(Team::where('user_id',$this->user->id)->first()->immediateHead_Campaigns_id);
      $campaign_id = $campaign->campaign_id;
      //nurses = clinical services = 71, marketing - 16
      //mpamero, mbambico, jmillares
      $allowed_users = [564, 83, 491];
      $formatted_publish_date = Carbon::createFromFormat('m/d/Y', Input::get('mPublishDate'));
      $formatted_expiry_date = (empty(Input::get('mExpiryDate')) || trim(Input::get('mExpiryDate'))=='') ? NULL : Carbon::createFromFormat('m/d/Y', Input::get('mPublishDate'));

      if ( !in_array($this->user->id, $allowed_users, true) && $campaign_id!=71 && $campaign_id!=16  ) return view('access-denied');
      if(Input::get('draftId')!=0){
        //find
        $memo = Announcement::where('author_campaign_id',$campaign_id)->where('id',Input::get('draftId'))->firstOrFail();
      }else{
        // store
        $memo = new Announcement;

      }

      $memo->user_id       = $this->user->id;
      $memo->author_campaign_id      = $campaign_id;
      $memo->title = Input::get('mTitle');
      $memo->decorative_title = Input::get('mDecor');
      $memo->template = Input::get('mType');
      $memo->message_body = Input::get('mBody');
      $memo->isDraft = Input::get('isDraft',0);
      $memo->publishDate = $formatted_publish_date ;
      $memo->publishExpire = $formatted_expiry_date;
      $memo->external_link = Input::get('mExternalLink');




      if($memo->save()) {
        if(Input::file('mFeatureImage')){
          try{
            $image = $request->file('mFeatureImage');
            $new_name = $memo->id."_feature" .'.' . $image->getClientOriginalExtension();
            $storagePath = public_path().'/storage/uploads/';

            $image->move($storagePath, $new_name);

            $memo->feature_image = url('/')."/public/storage/uploads/".$new_name;
            $memo->save();
          }catch(Exception $e){
            $memo->delete();
            return response()->json([
              'success' => false,
              'message' => array('Could not write to disk. Please try again later.'),
              'error' => $e->getMessage()
            ], 422);
          }
        }

        return response()->json([
          'success' => true,
          'message' => 'announcement entry added',
          'draftId' => $memo->id
        ], 200);
      }else{
        return response()->json([
          'success' => false,
          'message' => array('Could not write to database. Please try again later.'),
          'error' => 'memo could not be saved on db'
        ], 422);
      }
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
        'mTitle'       => 'required',
        'mType'       => 'required|in:memo,post',
        'mBody' => 'required',
        'mPublishDate' => 'required|date',
        'mExpiryDate' => 'date',
        'isDraft' => 'boolean',
        'hidden' => 'boolean',
        'showAlways' => 'boolean'

      ]);

      $campaign = ImmediateHead_Campaign::find(Team::where('user_id',$this->user->id)->first()->immediateHead_Campaigns_id);
      $campaign_id = $campaign->campaign_id;
      //nurses = clinical services = 71, marketing - 16
      //mpamero, mbambico, jmillares
      $allowed_users = [564, 83, 491];
      $formatted_publish_date = Carbon::parse(Input::get('mPublishDate'),'Asia/Manila'); //Carbon::createFromFormat('m/d/Y', Input::get('mPublishDate'));
      $formatted_expiry_date = (empty(Input::get('mExpiryDate')) || trim(Input::get('mExpiryDate'))=='') ? NULL : Carbon::parse(Input::get('mExpiryDate'),'Asia/Manila');

      if ( !in_array($this->user->id, $allowed_users, true) && $campaign_id!=71 && $campaign_id!=16  ) return view('access-denied');

      $memo = Announcement::where('author_campaign_id',$campaign_id)->where('id',$id)->firstOrFail();
      $memo->user_id       = $this->user->id;
      $memo->author_campaign_id      = $campaign_id;
      $memo->title = Input::get('mTitle');
      $memo->decorative_title = Input::get('mDecor');
      $memo->template = Input::get('mType');
      $memo->message_body = Input::get('mBody');
      $memo->isDraft = Input::get('isDraft',0);
      $memo->hidden = Input::get('hidden',0);
      $memo->showAlways = Input::get('showAlways',0);
      $memo->publishDate = $formatted_publish_date ;
      $memo->publishExpire = $formatted_expiry_date;
      $memo->external_link = Input::get('mExternalLink');
      if($memo->save()) {
        if(Input::file('mFeatureImage')){
          try{
            $image = $request->file('mFeatureImage');
            $new_name = $memo->id."_feature" .'.' . $image->getClientOriginalExtension();
            $storagePath = public_path().'/storage/uploads/';

            $image->move($storagePath, $new_name);

            $memo->feature_image = url('/')."/public/storage/uploads/".$new_name;
            $memo->save();
          }catch(Exception $e){
            $memo->delete();
            return response()->json([
              'success' => false,
              'message' => array('Could not write to database. Please try again later.'),
              'error' => $e->getMessage()
            ], 422);
          }
        }

        return response()->json([
          'success' => true,
          'message' => 'announcement entry added',
          'draftId' => $memo->id
        ], 200);
      }else{
        return response()->json([
          'success' => false,
          'message' => array('Could not write to database. Please try again later.'),
          'error' => 'memo could not be saved on db'
        ], 422);
      }
    }

    public function edit($id){
      $campaign = ImmediateHead_Campaign::find(Team::where('user_id',$this->user->id)->first()->immediateHead_Campaigns_id);
      $campaign_id = $campaign->campaign_id;

      $allowed_users = [564, 83, 491];
      if ( in_array($this->user->id, $allowed_users, true))
      {
        $announcement = Announcement::where('id',$id)->firstOrFail();
      }
      else
      {
        $announcement = Announcement::where('id',$id)->where('author_campaign_id',$campaign_id)->firstOrFail();
      }



      $slider_now = Carbon::now('GMT+8');
      $include_memo_scripts = TRUE;
      $include_jqueryform = TRUE;
      $include_ckeditor = TRUE;
      $publishDate = Carbon::createFromFormat('Y-m-d', $announcement->publishDate);
      $publishExpire = (empty($announcement->publishExpire)) ? NULL : Carbon::createFromFormat('Y-m-d', $announcement->publishExpire);
      return view('announcements.memo-edit', compact('announcement','slider_now','publishDate','publishExpire','include_memo_scripts','include_jqueryform','include_ckeditor'));
    }

    public function attach(Request $request){
      $campaign = ImmediateHead_Campaign::find(Team::where('user_id',$this->user->id)->first()->immediateHead_Campaigns_id);
      $campaign_id = $campaign->campaign_id;
      $allowed_users = [564, 83, 491];

      if ( !in_array($this->user->id, $allowed_users, true) && $campaign_id!=71 && $campaign_id!=16  ) {
        return response()->json([
            'success' => false,
            'message' => 'unauthorized'
          ], 422);
      }

        if(Input::file('upload')){
          try{
            $image = $request->file('upload');
            $new_name = "memo_attachment_"  .time() . '.' . $image->getClientOriginalExtension();
            $storagePath = public_path().'/storage/uploads/';

            $image->move($storagePath, $new_name);

            $url = url('/')."/public/storage/uploads/".$new_name;
            return response()->json([
              'success' => true,
              'message' => 'attachment uploaded succesfully',
              'url' => $url
            ], 200);
          }catch(Exception $e){
            return response()->json([
              'success' => false,
              'message' => array('Could not write to disk. Please try again later.'),
              'error' => $e->getMessage()
            ], 422);
          }
        }else{

          return response()->json([
            'success' => false,
            'message' => 'no file received'
          ], 422);
        }


    }
}
