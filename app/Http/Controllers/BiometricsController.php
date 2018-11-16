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
use OAMPI_Eval\MonthlySchedules;
use OAMPI_Eval\User_VL;
use OAMPI_Eval\User_SL;
use OAMPI_Eval\User_OT;
use OAMPI_Eval\User_OBT;
use OAMPI_Eval\User_LWOP;

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

    public function uploadFinanceCSV(Request $request)
    {
    	$today = date('Y-m-d');
    	
    	$bioFile = $request->file('biometricsData');
    	
	    //if (Input::file('biometricsData')->isValid()) 
	    if (!empty($bioFile))
	    {
		      //$destinationPath = 'uploads'; // upload path
		      $destinationPath = storage_path() . '/uploads/';
		      $extension = Input::file('biometricsData')->getClientOriginalExtension(); // getting image extension
		      $fileName = $today.'-biometrics-finance.'.$extension; // renameing image
		      $bioFile->move($destinationPath, $fileName); // uploading file to given path
		      



				$file = fopen($destinationPath.$fileName, 'r');
				$file2 = fopen($destinationPath.$fileName, 'r');
				


				$coll = new Collection;
				$ctr=0;
				$headers = [];
				$flag = true;
				DB::connection()->disableQueryLog();
				$csvCol = fgets($file2); 

				$headers = explode(',', $csvCol);
				$totalCols = count($headers);
				
				//return response()->json(['totalCols'=>$totalCols, 'headers'=>$headers]);

			    

				
				// rows
				// NAME | EMP ID NO.| ACCESSCODE | SCHEDULE | APPROVER | 21-Oct-2018 | TIME IN | TIME OUT | LATE | ND | OT | TOTAL ND | TOTAL OT
				while (($result = fgetcsv($file)) !== false)
					{
						//$coll->push(['result'=>$result]);
						if ($flag) $flag=false;
						else{

							for ($i=5; $i < $totalCols ; $i++) {
								//$coll->push(['item'=>$headers[$i], 'keme'=>strpos($headers[$i],"TOTAL") ]);

								if (strpos($headers[$i],"OT") !== false || strpos($headers[$i],"ND") !== false || strpos($headers[$i],"TIME") !== false || strpos($headers[$i],"LATE") !== false || strpos($headers[$i],"Approver") !== false || empty($result[3]) || empty($result[$i]) ) 
								{
									// do this things kung may laman lang kung waley dont do it

								}
								else if (Carbon::createFromFormat('d-M-Y',$headers[$i]) !== false) {
									$paydate = Carbon::createFromFormat('d-M-Y',$headers[$i],'Asia/Manila');
									$paydate2 = Carbon::createFromFormat('d-M-Y',$headers[$i],'Asia/Manila');
									$bio = Biometrics::where('productionDate',$paydate->format('Y-m-d'))->get();
									if (count($bio)>0) $b = $bio->first(); 
									else {
										// create new Biometrics
										$b = new Biometrics;
										$b->productionDate = $paydate->format('Y-m-d');
										$b->save();

									} 

									$u = User::where('accesscode',$result[2])->get();
									
									if (count($u)>0 ){
										$emp = $u->first();

										
											$user = $emp->firstname.' '.$emp->lastname;

											$existingSched = MonthlySchedules::where('user_id', $emp->id)->where('productionDate',$paydate->format('Y-m-d'))->orderBy('created_at','DESC')->get();

											if (count($existingSched) > 0)
											{
												//$worksched = $existingSched->first();
												foreach ($existingSched as $key) {
												 	# code...
												 	$key->delete();
												 } 
												//$coll->push(['user'=>$emp->lastname, 'sched'=>$worksched]);

											} 

												if(strpos(strtoupper($result[3]), "FLEXI")  == false && strtoupper($result[3])!=="FLEXIBLE" && strtoupper($result[3])!=="FLEXI" ) 
												{
													// setup worksched. Kung may DTRP submitted and approved yun ang gamitin
													// else save it as user-monthlysched
													//save it as MOnthlysched

													$sched = explode("-", $result[3]);
													//$coll->push(['user'=>$emp->lastname, 'sched'=>$sched]);

													$startSched = date('H:i:s',strtotime($sched[0]));
													$endSched = date('H:i:s',strtotime($sched[1]));

													$worksched = new MonthlySchedules;
													$worksched->user_id = $emp->id;
													$worksched->productionDate = $paydate->format('Y-m-d');
													$worksched->timeStart = $startSched;
													$worksched->timeEnd = $endSched;

													($result[$i] == "RD") ? $worksched->isRD = true : $worksched->isRD = false;
													(strtoupper($result[3]) == "FLEXI") ? $worksched->isFlexitime = true : $worksched->isFlexitime = false;

													$worksched->save();
													$coll->push(['created sched'=>'yes']);

												} else {
													$worksched = new MonthlySchedules;
													$worksched->timeStart = "flexi";
													$worksched->timeEnd = "flexi";
													$worksched->isRD = false;
													$coll->push(['created sched'=>'no']);
												}



											

											

											// WE NOW SAVE THE LOGS
											// BUT first, check if may existing logs na
											// if meron, deadma nalang
											$hasExistingLogs = Logs::where('user_id',$emp->id)->where('biometrics_id',$b->id)->get();

											if (count($hasExistingLogs) <= 1 )
											{
												// ------if workday OR Restday OT, save the log
												if (($result[$i] != "RD" && $result[$i+1] != '0') || ($result[$i] == "RD" && $result[$i+5] != '0'))
												{
													$log = new Logs;
													$log->user_id = $emp->id;
													$log->biometrics_id = $b->id;
													$log->logTime = date('H:i:s',strtotime($result[$i+1]));
													$log->logType_id = 1; //LogIN
													$log->save();

													// check if Next-day logout; ie complicated sched
													// This is assuming na hindi UNDERTIME si employee
													// ** there must be some form of indicator kung undertime si employee

													if ($worksched->timeStart >= date('H:i:s',strtotime("3PM"))) {
														$pd = $paydate2->addDays(1)->format('Y-m-d');
														$bioNext = Biometrics::where('productionDate',$pd)->get();

														if (count($bioNext)>0) $bNext = $bioNext->first(); 
														else {
															// create new Biometrics
															$bNext = new Biometrics;
															$bNext->productionDate = $pd;
															$bNext->save();

														} 

														$log = new Logs;
														$log->user_id = $emp->id;
														$log->biometrics_id = $bNext->id;
														$log->logTime = date('H:i:s',strtotime($result[$i+2]));
														$log->logType_id = 2; //LogOUT
														$log->save();

													} else {
															$log = new Logs;
															$log->user_id = $emp->id;
															$log->biometrics_id = $b->id;
															$log->logTime = date('H:i:s',strtotime($result[$i+2]));
															$log->logType_id = 2; //LogOUT
															$log->save();
													}

													

												} else if($result[$i] == 'VL' || $result[$i] == 'ML' ){

													$vl = new User_VL;
													$vl->user_id = $emp->id;
													$vl->leaveStart = $paydate->format('Y-m-d')." ".$worksched->timeStart;
													$vl->leaveEnd = $paydate->format('Y-m-d')." ".$worksched->timeEnd;
													$vl->totalCredits = 1.00;
													$vl->halfdayFrom = 1;
													$vl->halfdayTo = 1;

													if ($result[$i] == 'ML')
													$vl->notes = "Maternity Leave filed via CSV upload from Google sheet Timekeeping tracker. Check with immediate head and/or Finance for complete details.";
													else
													$vl->notes = "VL filed via CSV upload from Google sheet Timekeeping tracker. Check with immediate head and/or Finance for complete details.";

													$vl->isApproved = true;

													// get approver
													$vl->approver = $emp->supervisor->immediateHead_Campaigns_id;
													$vl->save();



												}
												else if($result[$i] == 'SL' || $result[$i] == 'MC'){

													$vl = new User_SL;
													$vl->user_id = $emp->id;
													$vl->leaveStart = $paydate->format('Y-m-d')." ".$worksched->timeStart;
													$vl->leaveEnd = $paydate->format('Y-m-d')." ".$worksched->timeEnd;
													$vl->totalCredits = 1.00;
													$vl->halfdayFrom = 1;
													$vl->halfdayTo = 1;
													if ($result[$i] == 'MC')
													$vl->notes = "MC filed via CSV upload from Google sheet Timekeeping tracker. Check with immediate head and/or Finance for complete details.";
													else
														$vl->notes = "SL filed via CSV upload from Google sheet Timekeeping tracker. Check with immediate head and/or Finance for complete details.";

													$vl->isApproved = true;
													$vl->attachments = null;

													// get approver
													$vl->approver = $emp->supervisor->immediateHead_Campaigns_id;
													$vl->save();



												}
												else if($result[$i] == 'LWOP'){

													$vl = new User_LWOP;
													$vl->user_id = $emp->id;
													$vl->leaveStart = $paydate->format('Y-m-d')." ".$worksched->timeStart;
													$vl->leaveEnd = $paydate->format('Y-m-d')." ".$worksched->timeEnd;
													$vl->totalCredits = 1.00;
													$vl->halfdayFrom = 1;
													$vl->halfdayTo = 1;
													$vl->notes = "LWOP filed via CSV upload from Google sheet Timekeeping tracker. Check with immediate head and/or Finance for complete details.";
													$vl->isApproved = true;
													
													// get approver
													$vl->approver = $emp->supervisor->immediateHead_Campaigns_id;
													$vl->save();

												}
												else if($result[$i] == 'OBT'){

													$vl = new User_OBT;
													$vl->user_id = $emp->id;
													$vl->leaveStart = $paydate->format('Y-m-d')." ".$worksched->timeStart;
													$vl->leaveEnd = $paydate->format('Y-m-d')." ".$worksched->timeEnd;
													$vl->totalCredits = 1.00;
													$vl->halfdayFrom = 1;
													$vl->halfdayTo = 1;
													$vl->notes = "OBT filed via CSV upload from Google sheet Timekeeping tracker. Check with immediate head and/or Finance for complete details.";
													$vl->isApproved = true;
													

													// get approver
													$vl->approver = $emp->supervisor->immediateHead_Campaigns_id;
													$vl->save();

												}

											}//end hasExistingLogs

										
										


										





									}  else $user = null;




									if (!is_null($user)){
										if ($worksched->isRD)
										$coll->push(['user'=>$user]);
									else
										$coll->push(['user'=>$user]); 


									} 
									
								} else{ 
									
									// do nothing
									//( strtoupper($csvCol[$i]) == 'TIME IN' )
									# code...
								} 
					    	}//end for

						}//end else
						

						
					    $ctr++;
					    
					}//end while

			    fclose($file);fclose($file2);

			   return response()->json($coll);


				   // return redirect()->action('TempUploadController@index');

		      
	    }
	    else return response()->json(['success'=>false]);
    	

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
