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

use OAMPI_Eval\Cutoff;
use OAMPI_Eval\Gallery;
use OAMPI_Eval\Gallery_User;
use OAMPI_Eval\User_Leader;
use OAMPI_Eval\Campaign;
use OAMPI_Eval\Status;
use OAMPI_Eval\Team;
use OAMPI_Eval\Floor;
use OAMPI_Eval\UserType;
use OAMPI_Eval\Position;
use OAMPI_Eval\ImmediateHead;
use OAMPI_Eval\ImmediateHead_Campaign;

class GalleryController extends Controller
{
    protected $user;
   	protected $user_dtr;
    
    use Traits\UserTraits;

    public function __construct(Gallery $gallery)
    {
        $this->middleware('auth');
        $this->gallery = $gallery;
        $this->user =  User::find(Auth::user()->id);
    }

    public function getUploads()
    {
      $album = Input::get('album');
      $allImg = DB::table('gallery_user')->where('gallery_user.gallery_id',$album)->
                        join('users','gallery_user.user_id','=','users.id')->
                        join('gallery','gallery_user.gallery_id','=','gallery.id')->
                        select('gallery.name as gallery','gallery.description', 'gallery.id','gallery_user.id as imgID','gallery_user.link','users.id as userID','users.firstname','users.nickname', 'users.lastname')->orderBy('gallery_user.id','DESC')->get();

      $allImages = new Collection;

      foreach ($allImg as $key) {

        if (empty($key->nickname))
          $allImages->push(['lowsrc'=>"../storage/uploads/".$key->link,
                                  'fullsrc'=>"../storage/uploads/".$key->link,
                                  'description'=> $key->description." [ Photo credits: ".$key->firstname." ".$key->lastname." ]",
                                  'category'=>$key->gallery]);
        else
          $allImages->push(['lowsrc'=>"../storage/uploads/".$key->link,
                                'fullsrc'=>"../storage/uploads/".$key->link,
                                'description'=> $key->description." [ Photo credits: ".$key->nickname." ".$key->lastname." ]",
                                'category'=>$key->gallery]);
      }
      return $allImages;

      return response()->json($allImages);
    }

    public function show($id)
    {
      $gallery = Gallery::find($id); 
      $allImg = DB::table('gallery_user')->where('gallery_user.gallery_id',$gallery->id)->get(); 


      return view('gallery_user',compact('gallery','id','allImg'));
    }

    public function contribute($id)
    {
      $gallery = Gallery::find($id);
      return view('people.gallery-upload',compact('gallery'));
    }


    public function upload(Request $request)
    {
    	$gallery = Gallery::find($request->galleryID);
    	$image_code = '';
    	$images = $request->file('file');
    	$filen = "gallery_".$gallery->id."_";
	    foreach($images as $image)
	     {

	      $new_name = $filen.$this->user->id."_".rand() .'.' . $image->getClientOriginalExtension();
        $destinationPath = storage_path() . '/uploads/';
	      $image->move($destinationPath, $new_name);

        $upload = new Gallery_User;
        $upload->gallery_id = $gallery->id;
        $upload->user_id = $this->user->id;
        $upload->link = $new_name;
        $upload->save();


	      $image_code .= '<div class="col-md-3" style="margin-bottom:24px;"><img src="../../storage/uploads/'.$new_name.'" class="img-thumbnail" /></div>';
	     }

     	$output = array(
      		'success'  => 'Images uploaded successfully',
      		'image'   => $image_code
     	);

     //return redirect()->action('GalleryController@show',$gallery->id);
     return response()->json($output);
    }
}
