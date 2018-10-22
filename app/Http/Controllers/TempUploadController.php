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

class TempUploadController extends Controller
{
    protected $user;
   	protected $tempUpload;

     public function __construct(TempUpload $tempUpload)
    {
        $this->middleware('auth');
        $this->tempUpload = $tempUpload;
        $this->user =  User::find(Auth::user()->id);
    }

    public function index()
    {
    	
    	DB::connection()->disableQueryLog();
    	$totalUploads = DB::table('temp_uploads')->count();

    	if ($totalUploads == 0) return view('empty');
    	else
    	{
    		$perdate = TempUpload::orderBy('productionDate')->pluck('productionDate');
			$productionDates = $perdate->unique();

			$currentBios = new Collection;

			//save biometrics table
			foreach ($productionDates as $prod) {
				$existingBio = Biometrics::where('productionDate',$prod)->get();
				if (count($existingBio) < 1)
				{
					$biometrics = new Biometrics;
					$biometrics->productionDate = $prod;
					$biometrics->save();

					$currentBios->push(['id'=>$biometrics->id, 'productionDates'=> $prod]);

				} else
				{
					$currentBios->push(['id'=>$existingBio->first()->id, 'productionDates'=> $prod]);

				}
			}
			

	    	return view('timekeeping.dtr-generate', compact('totalUploads','currentBios'));

    	}
    	
		


    }

    public function show()
    {

    }

    public function purge()
    {
    	TempUpload::truncate();
    	return back();
    }

    public function purgeThis(Request $request)
    {
    	$productionDate = date('Y-m-d', strtotime($request->productionDate));
    	DB::connection()->disableQueryLog();
    	TempUpload::where('productionDate',$productionDate)->delete();
    	return response()->json(['purge'=>"success"]);
    	//return back();
    }

    public function destroy(){
    	TempUpload::truncate();
    }
}
