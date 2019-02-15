<?php

namespace OAMPI_Eval\Http\Controllers;


use OAMPI_Eval\Http\Requests;
use OAMPI_Eval\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\UrlGenerator;

class IDController extends Controller
{
    public function __construct(UrlGenerator $url)
    {
        $this->middleware('auth');
        $this->user =  User::find(Auth::user()->id);
        $this->url = $url;
    }
    
    public function index()
    {
        return View::make('camera.index', ['user' => User::findOrFail($id), 'url'=> $this->url->to('/') ]);
    }
    
    public function trainee()
    {
        return View::make('camera.trainee', ['user' => User::findOrFail($id), 'url'=> $this->url->to('/') ]);
    }
    
    public function load_single($id)
    {
        return View::make('camera.index', ['user' => User::findOrFail($id), 'url'=> $this->url->to('/') ]);
    }
    
    public function load_campaign($id)
    {
        return View::make('camera.index', ['campaign' => User::findOrFail($id), 'url'=> $this->url->to('/') ]);
    }
    
    public function export_id()
    {
        $image_parts = explode(";base64,", $_POST['base64data']);
        $image_base64 = base64_decode($image_parts[1]);
        
        if (preg_match('/^data:image\/(\w+);base64,/', $_POST['base64data'], $image_parts[0])) {
            
            if (!in_array($image_parts[0][1], [ 'png' ])) {
                throw new \Exception('invalid image type: '.$image_parts[0][1]);
            }
            
            if ($image_base64 === false) {
                throw new \Exception('base64_decode failed');
            }
        } else {
            throw new \Exception('did not match data URI with image data');
        }
        $dir = "/var/www/html/evaluation/storage/uploads/id/";
        if (!file_exists($dir)) mkdir($dir, 0755, true);
            
        $filename = microtime(true);
        file_put_contents($dir.$filename.".png", $image_base64);
        echo "storage/uploads/id/".$filename.".png";
        exit;
    }
    
    public function save_signature()
    {
        $image_parts = explode(";base64,", $_POST['base64data']);
        $image_base64 = base64_decode($image_parts[1]);
        
        if (preg_match('/^data:image\/(\w+);base64,/', $_POST['base64data'], $image_parts[0])) {
            
            if (!in_array($image_parts[0][1], [ 'png' ])) {
                throw new \Exception('invalid image type: '.$image_parts[0][1]);
            }
            
            if ($image_base64 === false) {
                throw new \Exception('base64_decode failed');
            }
        } else {
            throw new \Exception('did not match data URI with image data');
        }
        
        if(is_numeric($_POST['id'])){
            $dir = "/var/www/html/evaluation/storage/uploads/id/";
            if (!file_exists($dir)) mkdir($dir, 0755, true);
            
            $filename = microtime(true); 
            file_put_contents($dir."sign_".$_POST['id']."_".$filename.".png", $image_base64);
            echo "storage/uploads/id/sign_".$_POST['id']."_".$filename.".png";
        }else{
            throw new \Exception('Invalid employee ID');
        }
        exit;
    }
}