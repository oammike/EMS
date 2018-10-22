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

class FixedScheduleController extends Controller
{
    protected $user;
   	protected $fixedSchedule;

     public function __construct(FixedSchedules $fixedSchedule)
    {
        $this->middleware('auth');
        $this->fixedSchedule = $fixedSchedule;
        $this->user =  User::find(Auth::user()->id);
    }

    public function store(Request $request)
    {

    	$ctr = 0;
    	$coll = new Collection;

        $applySchedToOthers = $request->applySchedTo; // array of user-ids

    	foreach($request->workday as $wd)
    	{ 
    			$sched = new FixedSchedules;
	    		$sched->user_id = $request->user_id;
	    		$sched->workday = $wd;
                
                $shift = $request->timeEnd[$ctr];
                $timeshift = explode('-', $shift);

	    		$sched->timeStart = date('H:i A',strtotime($timeshift[0]));  // date('H:i A',strtotime($request->timeStart[$ctr])); 
	    		$sched->timeEnd = date('H:i A',strtotime($timeshift[1]));  //date('H:i A',strtotime($request->timeEnd[$ctr]));
	    		$sched->isFlexitime = ($request->isFlexitime == "YES") ? true: false ;
	    		//$coll->push(['workday'=>$wd, 'timeStart'=>date('H:i A',strtotime($request->timeStart[$ctr])), 'timeEnd'=> date('H:i A',strtotime($request->timeEnd[$ctr])), 'isFlexitime'=>$request->isFlexitime]);
	    		$sched->isRD = 0;
	    		$sched->save();
	    		

                if (count($applySchedToOthers) > 0)
                {
                    foreach($applySchedToOthers as $user2)
                    {
                        $sched2 = new FixedSchedules;
                        $sched2->user_id = $user2;
                        $sched2->workday = $wd;
                        $sched2->timeStart = date('H:i A',strtotime($timeshift[0]));  // date('H:i A',strtotime($request->timeStart[$ctr])); 
                        $sched2->timeEnd = date('H:i A',strtotime($timeshift[1]));  //date('H:i A',strtotime($request->timeEnd[$ctr]));

                        $sched2->isFlexitime = ($request->isFlexitime == "YES") ? true: false ;
                        //$coll->push(['workday'=>$wd, 'timeStart'=>date('H:i A',strtotime($request->timeStart[$ctr])), 'timeEnd'=> date('H:i A',strtotime($request->timeEnd[$ctr])), 'isFlexitime'=>$request->isFlexitime]);
                        $sched2->isRD = 0;
                        $sched2->save();
                        
                    }
                

                }
                $ctr++;
                

    		
    	}

    	// foreach ($request->workday as $wd)
    	// {
    		
    	// }

    	$ctr2 = 0;

    	foreach ($request->restdays as $rd)
    	{
    		$sched = new FixedSchedules;
    		$sched->user_id = $request->user_id;
    		$sched->workday = $rd;
    		$sched->timeStart = null; //date('H:i A',strtotime($request->timeStart[$ctr]));
    		$sched->timeEnd = null; //date('H:i A',strtotime($request->timeEnd[$ctr]));
    		$sched->isFlexitime = ($request->isFlexitime == "YES") ? true: false ;
    		$sched->isRD = 1;
    		$sched->save();
    		

            if (count($applySchedToOthers) > 0)
            {
                foreach($applySchedToOthers as $user2)
                {
                    $sched2 = new FixedSchedules;
                    $sched2->user_id = $user2;
                    $sched2->workday = $rd;
                    $sched2->timeStart = null; //date('H:i A',strtotime($request->timeStart[$ctr]));
                    $sched2->timeEnd = null; //date('H:i A',strtotime($request->timeEnd[$ctr]));
                    $sched->isFlexitime = ($request->isFlexitime == "YES") ? true: false ;
                    $sched2->isRD = 1;
                    $sched2->save();
                   
                }
            }

            $ctr++;
            $ctr2++;
    	}

    	//return response()->json(['saved schedules'=>$ctr]);
    	return redirect(action('UserController@show', $request->user_id));

    }
}
