<?php

namespace OAMPI_Eval\Http\Controllers;


use OAMPI_Eval\Http\Requests;
use OAMPI_Eval\User;
use OAMPI_Eval\UserType_Roles;
use OAMPI_Eval\UserType;
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
        $canDoThis = UserType::find($this->user->userType_id)->roles->where('label','EDIT_EMPLOYEE');
        (count($canDoThis)> 0 ) ? $this->has_id_permissions=1 : $this->has_id_permissions=0;
        
        /*
        $this->beforeFilter(function() {
            if(Auth::user()->role->name != 'client') return redirect::to('/'); // home
        });
        */
        
    }
    
    public function index()
    {
        if(!$this->has_id_permissions){
           return view("access-denied");
        }
        return view('camera.index',['user' => $this->user, 'url'=> $this->url->to('/'), 'campaign_mode' => $this->campaign_mode ]);
    }
    
    public function trainee()
    {
        if(!$this->has_id_permissions){
           return view("access-denied");
        }
        return view('camera.trainee', ['url'=> $this->url->to('/') ]);
    }
    
    public function camera_back()
    {
        if(!$this->has_id_permissions){
           return view("access-denied");
        }
        return view('camera.back', ['url'=> $this->url->to('/') ]);
    }
    
    public function load_single($id)
    {
        if(!$this->has_id_permissions){
           return view("access-denied");
        }
        return view('camera.index', ['user' => User::find($id), 'url'=> $this->url->to('/'), 'campaign_mode' => $this->campaign_mode ]);
    }
    
    public function load_campaign($id)
    {
        if(!$this->has_id_permissions){
           return view("access-denied");
        }
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
        if(!$this->has_id_permissions){
           return view("access-denied");
        }
        
        
        if(isset($_POST['idData'])){
            $image_parts = explode(";base64,", $_POST['idData']);
            $image_base64 = base64_decode($image_parts[1]);
            
            if (preg_match('/^data:image\/(\w+);base64,/', $_POST['idData'], $image_parts[0])) {
                
                if (!in_array($image_parts[0][1], [ 'png' ])) {
                    throw new \Exception('invalid image type: '.$image_parts[0][1]);
                }
                
                if ($image_base64 === false) {
                    throw new \Exception('base64_decode failed');
                }
            } else {
                throw new \Exception('did not match data URI with image data');
            }
        }
        
        if(isset($_POST['portraitData'])){
            $portrait_parts = explode(";base64,", $_POST['portraitData']);
            $portrait_base64 = base64_decode($portrait_parts[1]);
            
            if (preg_match('/^data:image\/(\w+);base64,/', $_POST['portraitData'], $portrait_parts[0])) {
                if (!in_array($image_parts[0][1], [ 'png' ])) {
                    throw new \Exception('invalid image type: '.$portrait_parts[0][1]);
                }
                
                if ($portrait_base64 === false) {
                    throw new \Exception('base64_decode failed');
                }
                
                $dir = "/var/www/html/evaluation/storage/uploads/id/";
                if (!file_exists($dir)) mkdir($dir, 0755, true);
                
                $filename = microtime(true); 
                file_put_contents($dir.$_POST['id']."_portrait.png", $portrait_base64);
                
            } else {
                throw new \Exception('did not match data URI with image data');
            }
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
        if( isset($_POST['id']) && is_numeric($_POST['id'])){
            $filename = $_POST['id'];
            $deleteme = "/var/www/html/evaluation/storage/uploads/id/sign_".$_POST['id'].".png";
            unlink($deleteme);
        }else{
            throw new \Exception('Invalid employee ID');
        }
        
        
        
        
        
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
        $transparency = imagecolorallocatealpha($output, 255, 255, 255, 255);
        imagefilledrectangle($output, 0, 0, $outputW, $outputH, $transparency);
        imagecopyresampled($output, $image, 0, 0, 0, 0, $outputW, $outputH, $width, $height);
        imagepng($output, $dir.$filename.".png", 9);

        echo "storage/uploads/id/".$filename.".png";
        
        exit;
    }
    
    function rename_id(){
        /*
        $id_number = Input::get('id_number');
        $filename = Input::get('filename');
        $is_portrait = Input::get('is_portrait');
        if (file_exists($filename)) {
            if($is_portrait){
                rename($filename, "/storage/uploads/id/".$id_number."_portrait.png");
            } else {
                rename($filename, "/storage/uploads/id/".$id_number.".png");
            }
            echo "success";
        } else {
            echo "file does not exist";
        }
        */
    }
    
    function save_portrait(){
        if(!$this->has_id_permissions){
           return view("access-denied");
        }
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
        
        
            $dir = "/var/www/html/evaluation/storage/uploads/id";
            if (!file_exists($dir)) mkdir($dir, 0755, true);
            $filename = microtime(true);
            file_put_contents($dir.$filename.".png", $image_base64);
            echo "storage/uploads/id/backlogs/".$filename.".png";
        
        exit;
    }

    public function archive()
    {
        if(!$this->has_id_permissions){
           return view("access-denied");
        }
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
        
        
        /*
        $dir = "/var/www/html/evaluation/storage/uploads/id/backlogs";
        if (!file_exists($dir)) mkdir($dir, 0755, true);
        $filename = microtime(true);
        file_put_contents($dir.$filename.".png", $image_base64);
        echo "storage/uploads/id/backlogs/".$filename.".png";
        
        exit;
        */
        
        $idealW = 1322;
        $idealH = 2071;
        $optimalW = 525;
        $optimalH = 822;
        $outputW = 0;
        $outputH = 0;
        
        $dir = "/var/www/html/evaluation/storage/uploads/id/backlogs";
        if (!file_exists($dir)) mkdir($dir, 0755, true);
        $filename = microtime(true);
        
        
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
        $transparency = imagecolorallocatealpha($output, 255, 255, 255, 255);
        imagefilledrectangle($output, 0, 0, $outputW, $outputH, $transparency);
        imagecopyresampled($output, $image, 0, 0, 0, 0, $outputW, $outputH, $width, $height);
        imagepng($output, $dir.$filename.".png", 9);

        echo "storage/uploads/id/backlogs/".$filename.".png";
        
        exit;
        
        
    }
    
    public function save_signature()
    {
        if(!$this->has_id_permissions){
           return view("access-denied");
        }
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
            
            //$filename = microtime(true); 
            file_put_contents($dir."sign_".$_POST['id'].".png", $image_base64);
            echo "storage/uploads/id/sign_".$_POST['id'].".png";
        }else{
            throw new \Exception('Invalid employee ID');
        }
        exit;
    }
    
    public function upload_signature()
    {
        if(!$this->has_id_permissions){
           return view("access-denied");
        }
        
        if(is_numeric($_POST['employeeId'])){
            $dir = "/var/www/html/evaluation/storage/uploads/id/";
            if (!file_exists($dir)) mkdir($dir, 0755, true);
            
            //$filename = microtime(true); 
            //file_put_contents($dir."sign_".$_POST['employeeId'].".png", $image_base64);
            if(move_uploaded_file($_FILES['file']['tmp_name'],$dir."sign_".$_POST['employeeId'].".png")){
                echo "storage/uploads/id/sign_".$_POST['employeeId'].".png";
            }else{
                throw new \Exception('Uploaded file could not be read');
            }
        }else{
            throw new \Exception('Invalid employee ID');
        }
        exit;
    }
}