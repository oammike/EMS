<?php

namespace OAMPI_Eval\Http\Controllers;


use OAMPI_Eval\Http\Requests;
use OAMPI_Eval\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\UrlGenerator;
use \DB;

class IDController extends Controller
{
    public function __construct(UrlGenerator $url)
    {
        $this->middleware('auth');
        $this->user =  User::find(Auth::user()->id);
        $this->url = $url;
        $this->campaign_mode = false;
    }
    
    public function index()
    {
           
        return view('camera.index',['user' => $this->user, 'url'=> $this->url->to('/'), 'campaign_mode' => $this->campaign_mode ]);
    }
    
    public function trainee()
    {
        return view('camera.trainee', ['url'=> $this->url->to('/') ]);
    }
    
    public function camera_back()
    {
        return view('camera.back', ['url'=> $this->url->to('/') ]);
    }
    
    public function load_single($id)
    {
        return view('camera.index', ['user' => User::find($id), 'url'=> $this->url->to('/'), 'campaign_mode' => $this->campaign_mode ]);
    }
    
    public function load_campaign($id)
    {    
        $users = DB::table('team')->where('team.campaign_id',$id)->
                        leftJoin('users','users.id','=','team.user_id')->
                         where([
                            ['status_id','!=',7],
                            ['status_id','!=',8],
                            ['status_id','!=',9],
                        ])->
                        leftJoin('positions','users.position_id','=','positions.id')->
                        select('users.employeeNumber','users.firstname','users.nickname','users.middlename','users.lastname','users.id','positions.name as jobTitle')->get();
        $users = json_encode($users);
        
        
        $this->campaign_mode = true;
        return view('camera.index', ['user' => $this->user, 'url'=> $this->url->to('/'), 'campaign_mode' => $this->campaign_mode, 'users' => $users ]);
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
        
        $idealW = 1322;
        $idealH = 2071;
        $optimalW = 525;
        $optimalH = 822;
        $outputW = 0;
        $outputH = 0;
        
        $dir = "/var/www/html/evaluation/storage/uploads/id/";
        if (!file_exists($dir)) mkdir($dir, 0755, true);
        $filename = microtime(true);
        $transparency = imagecolorallocatealpha($output, 255, 255, 255, 255);
        
        $image = imagecreatefromstring($image_base64);
        $width = ImageSX($image);
        $height = ImageSY($image);
        
        if($width >= $idealW){
            $outputW = $idealW;
            $outputH = $idealH;
        }else{
            $outputW = $optimalW;
            $outputH = $optimalH;    
        }
        
        $output = imagecreatetruecolor($outputW,$outputH);
        imagefilledrectangle($output, 0, 0, $outputW, $outputH, $transparency);
        imagecopyresampled($output, $image, 0, 0, 0, 0, $outputW, $outputH, $width, $height);
        imagepng($output, $dir.$filename.".png", 9);

        echo "storage/uploads/id/".$filename.".png";
        
        exit;
    }
    
    public function archive()
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
        
        
            $dir = "/var/www/html/evaluation/storage/uploads/id/backlogs";
            if (!file_exists($dir)) mkdir($dir, 0755, true);
            $filename = microtime(true);
            file_put_contents($dir.$filename.".png", $image_base64);
            echo "storage/uploads/id/backlogs/".$filename.".png";
        
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