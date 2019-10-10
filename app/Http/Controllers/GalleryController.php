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


    public function upload(Request $request)
    {
    	$gallery = Gallery::find($request->galleryID);
    	$image_code = '';
    	$images = $request->file('file');
    	$filen = "gallery_".$gallery->id."_";
	    foreach($images as $image)
	     {

	      $new_name = $filen.rand() . '.' . $image->getClientOriginalExtension();
	      $image->move(public_path('images'), $new_name);
	      $image_code .= '<div class="col-md-3" style="margin-bottom:24px;"><img src="/storage/uploads/'.$new_name.'" class="img-thumbnail" /></div>';
	     }

     	$output = array(
      		'success'  => 'Images uploaded successfully',
      		'image'   => $image_code
     	);

     return response()->json($output);
    }
}
