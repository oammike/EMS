<?php

namespace OAMPI_Eval\Http\Controllers;

use Hash;
use Carbon\Carbon;
use Excel;
use \DB;
use \PDF;
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
use OAMPI_Eval\UserType;
use OAMPI_Eval\UserType_Roles;
use OAMPI_Eval\ImmediateHead;

use OAMPI_Eval\Campaign;
use OAMPI_Eval\Position;
use OAMPI_Eval\Status;
use OAMPI_Eval\Role;
use OAMPI_Eval\RoleType;
use OAMPI_Eval\Team;

use OAMPI_Eval\Resource;
use OAMPI_Eval\Category;
use OAMPI_Eval\Resource_Category;
use OAMPI_Eval\User_Resource;


class ResourceController extends Controller
{
    protected $user;
    protected $resource;

    public function __construct(Resource $resource)
    {
        $this->middleware('auth');
        $this->user =  User::find(Auth::user()->id);
        $this->resource = $resource;
    }

    public function download($id)
    {
        
        $res = Resource::find($id);
        $resource = DB::table('user_resource')->where('resource_id',$id)->
                        leftJoin('users','users.id','=','user_resource.user_id')->
                        join('positions','users.position_id','=','positions.id')->
                        join('team','team.user_id','=','user_resource.user_id')->
                        join('campaign','team.campaign_id','=','campaign.id')->
                        select('users.firstname','users.lastname','positions.name as jobTitle','campaign.name as program','user_resource.agreed','user_resource.created_at as accessed')->orderBy('user_resource.created_at','DESC')->get();

        $correct = Carbon::now('GMT+8'); 
        if($this->user->id !== 564 ) {
              $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
                fwrite($file, "-------------------\n DL Resource: ". $res->name." -- by [". $this->user->id."] ".$this->user->lastname."\n");
                fclose($file);
        } 


        Excel::create($res->name, function($excel) use($resource,$res) {

        

          // Set the title
          $excel->setTitle($res->name);

          // Chain the setters
          $excel->setCreator('Mike Pamero')
                ->setCompany('OAMPI');

          // Call them separately
          $excel->setDescription('Contains all employee data');

          $excel->sheet('Access log', function($sheet) use($resource) {

           
            

            $sheet->appendRow(array('Lastname', 'Firstname','Position','Program/Dept.','Agreed to Terms','Date Accessed'));
            $sheet->cells('A1:Z1', function($cells) {

                              // call cell manipulation methods
                              $cells->setBackground('##1a8fcb');
                              $cells->setFontColor('#ffffff');
                              $cells->setFontSize(18);
                              $cells->setFontWeight('bold');

                          });

            foreach($resource as $emp){
              
              ($emp->agreed) ? $answer="Yes" : $answer="No";
              $accessed = date('Y d M - H:i:s',strtotime($emp->accessed));

              $arr = array($emp->lastname,
                    $emp->firstname,
                    $emp->jobTitle,
                    $emp->program,
                    $answer,
                    $accessed);
              
              $sheet->appendRow($arr);

            }

              

          });



      })->export('xls');

      return "Download";
       
    }

     public function index()
    {
        $canDoThis = UserType::find($this->user->userType_id)->roles->where('label','UPLOAD_NEW_RESOURCE');

        if (!$canDoThis->isEmpty())
        {
            
            $isAdmin = true;

        } else $isAdmin = false;

        $employee = $this->user;

        ( $employee->team->floor_id == 10 || $employee->team->floor_id == 11) ? $nonRP=1 : $nonRP=0;
            
            $resources = Resource::where('hidden',null)->orderBy('created_at','DESC')->get();
            $categories = Category::all()->sortBy('name');
            $allResource = new Collection;
            foreach ($categories as $cat) {

               $allResource->push(["name"=>$cat->name, "item" => $cat->resources,'id'=>$cat->id]);
            }

            if ( file_exists('public/img/employees/'.$employee->id.'-sign.png') )
                 {
                    $signature = asset('public/img/employees/'.$employee->id.'-sign.png');
                 } else {
                    $signature = null;
                 }
                 //return $allResource;
                 //return $employee->viewedResources->where('resource_id',11)->where('agreed',1)->first();
                // return $employee->viewedResources->where('resource_id', 14)->where('agreed',1);
        return view('resources.index', compact('isAdmin','resources', 'categories','allResource','employee','signature','nonRP'));
        
    }


    public function viewItem(Request $request)
    {
        $item = Resource::find($request->id);
         //save user access

        //---- first look for existing views by that person
        //----------------------------
        $existing = User_Resource::where('user_id',$this->user->id)->where('resource_id',$item->id)->get();
        if (count($existing) > 0){
            $agreed = $existing->where('agreed',1)->sortByDesc('created_at');
            $notAgree = $existing->where('agreed',0)->sortByDesc('created_at');

            if (count($agreed)==0){
                if (count($notAgree)==0){ //never pa sya nag access

                    $access = new User_Resource;
                    $access->user_id = $this->user->id;
                    $access->resource_id = $item->id;
                    $access->agreed = $request->agreed;
                    $access->save();
                    
                } else { //update mo yung date accessed

                    $access = $notAgree->first();
                    $access->created_at = date('Y-m-d H:i:s');
                    $access->agreed = $request->agreed;
                    $access->push();
                    

                }

            } else { //he already agreed sa doc. Do nothing

                //return "already agreed";

            }

            
        } else { //never pa sya nag access ng file

            $access = new User_Resource;
            $access->user_id = $this->user->id;
            $access->resource_id = $item->id;
            $access->agreed = $request->agreed;
            $access->save();
        }
        

        /* $access = new User_Resource;
        $access->user_id = $this->user->id;
        $access->resource_id = $item->id;
        $access->agreed = $request->agreed;
        $access->save(); */

        //return redirect(asset('storage/resources/'.$item->link)); //response()->json($item);
        //return response()->download(storage_path(asset('storage/resources/'.$item->link)));
        //return response()->file(storage_path('/uploads/'.$item->link));
        return response()->json($item->id);

    }


    public function waiver()
    {
       
        $id = Input::get('id');
        $correct = Carbon::now('GMT+8');

        $resource = Resource::find($id);

        if (empty($resource)) return view('empty');

        $alreadySigned = User_Resource::where('user_id',$this->user->id)->where('resource_id',$resource->id)->get();
        //return $alreadySigned;

        $employee = $this->user;
        $today = Carbon::now('GMT+8');
            
           

            if ( file_exists('public/img/employees/'.$employee->id.'-sign.png') )
                 {
                    $signature = asset('public/img/employees/'.$employee->id.'-sign.png');
                 } else {
                    $signature = null;
                 }
         
          if($this->user->id !== 564)
        {
            $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
            //fwrite($file, "-------------------\n Email sent to ". $newTL->userData->email."\n");
            fwrite($file, "\n WAIVER by: [". $this->user->id."] ". $this->user->lastname." on ". $correct->format('Y-m-d H:i:s')."\n");
            fclose($file);
        }       

        return view('resources.waiver', compact('employee','signature','today','id','alreadySigned'));
        
    }


    public function item($id)
    {
        $item = Resource::find($id);
        return response()->file(storage_path('/uploads/'.$item->link));

    }
    public function viewFile($id)
    {
        $item = Resource::find($id);
        //return redirect(asset('storage/resources/'.$item->link)); //response()->json($item);
        //return response()->download(storage_path('/resources/'.$item->link));
        return response()->file(storage_path('/uploads/'.$item->link));

    }

    public function track($id)
    {
        $resource = Resource::find($id);
        $viewers = $resource->viewers->groupBy('id');
        $track = new Collection;
        $program = "";

       
        
        foreach ($viewers as $v) {
            $accessed = User_Resource::where('resource_id',$id)->where('user_id',$v->first()->id)->get();
            $agreed = $accessed->where('agreed',1)->sortByDesc('created_at');
            $notAgree =  $accessed->where('agreed',0)->sortByDesc('created_at');

            //--------- campaign

                $teamInfo = Team::where('user_id',$v->first()->id)->first();
                   $leadershipcheck = ImmediateHead::where('employeeNumber', $v->first()->employeeNumber)->first();
                   $camps = "";
                   if (empty($leadershipcheck)) 
                    {
                       $program = Campaign::find($teamInfo->campaign_id)->name;

                    } else
                    {
                        $ct = 1;

                        foreach ($leadershipcheck->myCampaigns as $c) {
                            if ($ct == count($leadershipcheck->myCampaigns)) $program = Campaign::find($c->campaign_id)->name;
                            else $program .= Campaign::find($c->campaign_id)->name . ", ";
                            
                            $ct++;
                        }
                             
                    }

                  //--------end campaign

            if (count($agreed)==0){

                $track->push(['user_id'=>$v->first()->id, 'lastname'=>$v->first()->lastname, 'firstname'=>$v->first()->firstname, 'position'=>Position::find($v->first()->position_id)->name,'program'=> $program, 'accessed'=>date_format($notAgree->first()->created_at, 'M d, Y H:i:s'),'agreed'=>"No"]);

            }else {
                $track->push(['user_id'=>$v->first()->id, 'lastname'=>$v->first()->lastname, 'firstname'=>$v->first()->firstname, 'position'=>Position::find($v->first()->position_id)->name,'program'=> $program, 'accessed'=>date_format($agreed->first()->created_at, 'M d, Y H:i:s'),'agreed'=>"Yes"]);
            }

            
        }

        $canDoThis = UserType::find($this->user->userType_id)->roles->where('label','UPLOAD_NEW_RESOURCE');

        if (!$canDoThis->isEmpty())
        {
            
            $isAdmin = true;

        } else $isAdmin = false;

        return view('resources.trackVisits', compact('track','resource', 'isAdmin','id'));
        //return $track;

    }


    public function store(Request $request)
    {
        $today = date('Y-m-d_H:i:s');
        
        $bioFile = $request->file('resourceFile');
        $categoryFile = $request->file('category');
        
        //if (Input::file('biometricsData')->isValid()) 
        if (!empty($bioFile))
        {
              //$destinationPath = 'uploads'; // upload path
              $destinationPath = storage_path() . '/uploads/';
              $extension = Input::file('resourceFile')->getClientOriginalExtension(); // getting image extension
              $fileName = $today.'-file.'.$extension; // renameing image
              $bioFile->move($destinationPath, $fileName); // uploading file to given path

              $resource = new Resource;
              $resource->name = $request->filename;
              $resource->description = $request->description;
              $resource->link = $fileName;
              $resource->user_id = $this->user->id;
              $resource->save();

              $resourceCategory = new Resource_Category;
              $resourceCategory->category_id = $request->category;
              $resourceCategory->resource_id = $resource->id;
              $resourceCategory->save();
            
                /* -------------- log updates made --------------------- */
         $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
            fwrite($file, "\n-------------------\n New Resource uploaded : ". $fileName ." ". date('M d h:i:s'). " by ". $this->user->firstname.", ".$this->user->lastname."\n");
            fclose($file);
            return redirect()->action('ResourceController@index');
            //return response()->json(['filename'=>$fileName]);

              
        }
    }

    public function destroy($id)
    {
        $thefile = Resource::find($id);
        $destinationPath = storage_path() . '/uploads/';

      if (file_exists($destinationPath.$thefile->link)) unlink($destinationPath.$thefile->link);
      $this->resource->destroy($id);
      return back();

    }
}
