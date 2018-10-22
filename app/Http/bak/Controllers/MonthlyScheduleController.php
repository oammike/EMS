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
use OAMPI_Eval\User_DTR;
use OAMPI_Eval\MonthlySchedules;
use OAMPI_Eval\FixedSchedules;

class MonthlyScheduleController extends Controller
{
    protected $user;
   	protected $monthlySchedule;

     public function __construct(MonthlySchedules $monthlySchedule)
    {
        $this->middleware('auth');
        $this->monthlySchedule = $monthlySchedule;
        $this->user =  User::find(Auth::user()->id);
    }


    public function store(Request $request)
    {
        $coll = new Collection;
    	$ctr = 1;
        $effectivityFrom =  date("Y-m-d", strtotime($request->effectivityFrom)); 
        $effectivityTo =  date("Y-m-d", strtotime($request->effectivityTo)); 

        $schedPeriod = [];
        $daysOfWeek = array('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday');

         $schedStart = new Carbon($effectivityFrom);
         $schedEnd = new Carbon($effectivityTo);
         
         

         for($date = $schedStart; $date->lte($schedEnd); $date->addDay()) 
         {
            $schedPeriod[] = $date->format('Y-m-d');
         }

         $selectedWorkDays = $request->selectedWorkDays; // [Monday, Tue.., Fri]

         $applySchedToOthers = $request->applySchedTo; // array of user-ids
 
    	foreach ($schedPeriod as $wd)
    	{
            //get day of the week for RD
            $day = date('l', strtotime($wd));

            $theday = (string)$day;
            $numDay = array_search($theday, $daysOfWeek);
            $selectedDay = array_search($theday, $selectedWorkDays);


            $sched = new MonthlySchedules;

            //look in the array of RD if current day is RD
            if (in_array((string)$numDay, $request->restdays)) $sched->isRD = true; else $sched->isRD = false;

    		
    		$sched->user_id = $request->user_id;
    		$sched->productionDate = $wd;

            $shift = $request->timeEnd[$selectedDay];
            $timeshift = explode('-', $shift);
    		$sched->timeStart = date('H:i A', strtotime($timeshift[0]));  //date('H:i A',strtotime($request->timeStart[$selectedDay])); 
    		$sched->timeEnd =  date('H:i A', strtotime($timeshift[1]));  //date('H:i A',strtotime($request->timeEnd[$selectedDay]));
    		//$sched->isFlexitime = $request->isFlexitime;
            $sched->isFlexitime = ($request->isFlexitime == "YES") ? true: false ;

            /*-- before we save it, check for duplicates for that same production date then delete it --*/
            $dupes = MonthlySchedules::where('user_id',$request->user_id)->where('productionDate',$wd)->delete();
            
    		$sched->save();
           


            if (count($applySchedToOthers) > 0)
            {
                foreach($applySchedToOthers as $user2)
                {
                    $sched2 = new MonthlySchedules;

                    //look in the array of RD if current day is RD
                    if (in_array((string)$numDay, $request->restdays)) $sched2->isRD = true; else $sched2->isRD = false;

                    
                    $sched2->user_id = $user2;
                    $sched2->productionDate = $wd;
                    $sched2->timeStart =  date('H:i A', strtotime($timeshift[0]));   // date('H:i A',strtotime($request->timeStart[$selectedDay])); 
                    $sched2->timeEnd = date('H:i A', strtotime($timeshift[1]));// date('H:i A',strtotime($request->timeEnd[$selectedDay]));
                    $sched2->isFlexitime = $request->isFlexitime;
                    $sched2->save();
                    

                }
                
            } $ctr++;
    		
    	}

    	
    	
        return redirect(action('UserController@show', $request->user_id));
    	//return response()->json(['scheds'=>$ctr]);

    }

}

