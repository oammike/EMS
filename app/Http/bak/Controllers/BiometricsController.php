<?php

namespace OAMPI_Eval\Http\Controllers;

use Carbon\Carbon;
use Excel;
use \PDF;
use \App;
use \DB;
use \Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;

use Illuminate\Http\Request;

use Yajra\Datatables\Facades\Datatables;

use OAMPI_Eval\Http\Requests;
use OAMPI_Eval\User;
use OAMPI_Eval\Campaign;
use OAMPI_Eval\Status;
use OAMPI_Eval\Team;
use OAMPI_Eval\Floor;
use OAMPI_Eval\UserType;
use OAMPI_Eval\Position;
use OAMPI_Eval\ImmediateHead;
use OAMPI_Eval\ImmediateHead_Campaign;
use OAMPI_Eval\EvalForm;
use OAMPI_Eval\EvalSetting;
use OAMPI_Eval\Schedule;
use OAMPI_Eval\Restday;
use OAMPI_Eval\Cutoff;
use OAMPI_Eval\Biometrics;
use OAMPI_Eval\Biometrics_Uploader;
use OAMPI_Eval\Logs;
use OAMPI_Eval\LogType;
use OAMPI_Eval\TempUpload;
class BiometricsController extends Controller
{
   protected $user;
   protected $biometrics;

     public function __construct(Biometrics $biometrics)
    {
        $this->middleware('auth');
        $this->biometrics = $biometrics;
        $this->user =  User::find(Auth::user()->id);
    }

    public function index()
    {
    	$today = date('Y-m-d');
    	$url = Storage::url('robots.txt');
    	return $url;
    	//return $this->cutoff->first()->startingPeriod(). " - " . $paycutoff->endingPeriod();
    }

    public function setupBiometricUserLogs()
    {
    	DB::connection()->disableQueryLog();

  		// DB::table('temp_uploads')->orderBy('id')->chunk(100, function ($bios) {
		//     foreach ($bios as $bio) {
		        
		//         $prod = date('Y-m-d', strtotime($bio->logs));
		//         $existingBio = Biometrics::where('productionDate',$prod)->get();
		//         if (count($existingBio) > 0){
		// 					// it means may uploaded na for that date, no need to save one
		// 					// kunin mo na lang ung id nya for saving to LOGS
		// 					$biometrics_id = $existingBio->first()->id;
							
		// 					$emp = User::where('employeeNumber',$bio->employeeNumber)->get();
		// 					if(count($emp)>0) {

		// 						$log = new Logs;

		// 						$logType = LogType::where('code',$bio->logType)->get();
		// 						if (count($logType) < 1) $log->logType = 1;
		// 						else $log->logType = $logType->first()->id;
								
		// 						$log->user_id = $emp->first()->id;
		// 						$log->logTime = date('h:m:s', strtotime($bio->logs));
		// 						$log->biometrics_id = $biometrics_id;

		// 					}//end if existing employee

							

		// 				} //end if existing bio 
		// 				else
		// 				{	
		// 					$newbio = new Biometrics;
		// 			    	$newbio->productionDate = $prod;
		// 			    	$newbio->save();

		// 			    	$emp = User::where('employeeNumber',$bio->employeeNumber)->get();
		// 					if(count($emp)>0) {

		// 						$log = new Logs;

		// 						$logType = LogType::where('code',$bio->logType)->get();
		// 						if (count($logType) < 1) $log->logType = 1;
		// 						else $log->logType = $logType->first()->id;
								
		// 						$log->user_id = $emp->first()->id;
		// 						$log->logTime = date('h:m:s', strtotime($bio->logs));
		// 						$log->biometrics_id = $newbio->id;
		// 					}//end if existing employee


							
		// 				}//end new biometrics entry


		//     }
		// });

		return $logs;

    }

    public function upload(Request $request)
    {
    	$today = date('Y-m-d');
    	
    	$bioFile = $request->file('biometricsData');
    	
	    //if (Input::file('biometricsData')->isValid()) 
	    if (!empty($bioFile))
	    {
		      //$destinationPath = 'uploads'; // upload path
		      $destinationPath = storage_path() . '/uploads/';
		      $extension = Input::file('biometricsData')->getClientOriginalExtension(); // getting image extension
		      $fileName = $today.'-biometrics.'.$extension; // renameing image
		      $bioFile->move($destinationPath, $fileName); // uploading file to given path
		      



				$file = fopen($destinationPath.$fileName, 'r');
				


				$coll = new Collection;
				$ctr=0;
				DB::connection()->disableQueryLog();
				while (($result = fgetcsv($file)) !== false)
					{
					    

						
				    	$productionDate = date('Y-m-d', strtotime($result[1]));
						$productionTime = date('H:i:s', strtotime($result[1]));


					    DB::table('temp_uploads')->insert(
    						['employeeNumber' => $result[0],'productionDate'=>$productionDate,  'logTime' => $productionTime, 'logType'=>$result[2] ]
						);

					   

					    $ctr++;
					    
					}//end while

			    fclose($file);


			 
				    /* -------------- log updates made --------------------- */
	         	// $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
	          //   fwrite($file, "\n-------------------\n Biometrics uploaded : ". $ctr .", updated ". date('M d h:i:s'). " by ". $this->user->firstname.", ".$this->user->lastname."\n");
	          //   fclose($file);
				
				//return response()->json(['upload'=>'success', 'totalRecords'=>$ctr, 'biometrics_data'=>$coll]);
				    //return $coll;
				    //$tempUploads = TempUpload::all();

				    //
				    return redirect()->action('TempUploadController@index');

		      
	    }
    	

    }



    public function store(Request $request)
    {
    	$today = date('Y-m-d');
    	
    	$bioFile = $request->file('biometricsData');
    	
	    //if (Input::file('biometricsData')->isValid()) 
	    if (!empty($bioFile))
	    {
		      //$destinationPath = 'uploads'; // upload path
		      $destinationPath = storage_path() . '/uploads/';
		      $extension = Input::file('biometricsData')->getClientOriginalExtension(); // getting image extension
		      $fileName = $today.'-biometrics.'.$extension; // renameing image
		      $bioFile->move($destinationPath, $fileName); // uploading file to given path
		      // sending back with message
		      //return response()->json(['Filename' => $bioFile->getClientOriginalName()]);

		      //generate DTR and biometrics entry

		      /*
		      Excel::load($destinationPath.$fileName, function($reader) {

		      	$entries = $reader->take(100);

		      	$reader->dd();
		      	//dd($entries);

		      	//$entries = $reader->limitColumns(10)->limitRows(100)->toObject();
		      	//$entries = $reader->groupBy('deptprogram')->get();//
		      	//$reader->dd();
		      	//return response()->json($entries);

			    // reader methods



			  });
			  */


				/*
				$handle = fopen($destinationPath.$fileName, "r");
				$header = true;

				while ($csvLine = fgetcsv($handle, 1000, ",")) {

				    if ($header) {
				        $header = false;
				    } else {
				        Character::create([
				            'name' => $csvLine[0] . ' ' . $csvLine[1],
				            'job' => $csvLine[2],
				        ]);
				    }
				}
				*/

				//$contents = Storage::get($destinationPath.$fileName);




				/*
				$data = Excel::load($destinationPath.$fileName, function($reader) {})->get();

				return response()->json(['filename'=>$bioFile->getClientOriginalName(),'entries'=>$data->count()]);*/



				$file = fopen($destinationPath.$fileName, 'r');
				/* $coll = new Collection;
				while (($line = fgetcsv($file)) !== FALSE) {
				  //$line is an array of the csv elements
					$coll->push($line);
				  //print_r($line);
				}
				fclose($file);


				return $coll; */


				$coll = new Collection;
				$ctr=0;
				while (($result = fgetcsv($file)) !== false)
					{
					    //$csv[] = $result;
					    //$arr = explode(',', $result);
					    $coll->push(['ct'=>count($result), 'items'=>$result]);
					    $ctr++;
					    //list($id[], $timestamp[], $inout[]) = explode(',', $result);
					}
			    fclose($file);


			  // $coll->push(['id'=>$id, 'timestamp'=>$timestamp, 'inout'=>$inout]);

			   /*$result = fgetcsv($file);
			    $items = count($result);
			    $ctr = 0;

			    foreach ($result as $key) {
			    	// $coll->push(['id'=> $key[$ctr], 'timestamp'=>$key[$ctr++], 'inout'=>$key[$ctr++]]);
			    	// $ctr++;
			    	$coll->push($key);
			    }*/

			    /* -------------- log updates made --------------------- */
         $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
            fwrite($file, "\n-------------------\n Biometrics uploaded : ". $ctr .", updated ". date('M d h:i:s'). " by ". $this->user->firstname.", ".$this->user->lastname."\n");
            fclose($file);
			    return response()->json(['ct'=>$ctr, 'data'=>$coll]);











		      
	    }
    	
    	


    }
}
