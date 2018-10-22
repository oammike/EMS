<?php

namespace OAMPI_Eval\Http\Controllers;

use Hash;
use Carbon\Carbon;
use Excel;
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

class ScheduleController extends Controller
{

	protected $schedule;

     public function __construct(Schedule $schedule)
    {
        $this->middleware('auth');
        $this->schedule = $schedule;
        //$this->user =  User::find(Auth::user()->id);
    }
   

    public function destroy($id)
    {
        $this->schedule->destroy($id);
        return back();
    }

    public function store(Request $request)
    {
       if ($request->isFlexi == 1)
       {

                        $schedule = new Schedule;
                        $schedule->user_id = $request->user_id;
                        $schedule->workday = null;
                        $schedule->isFlexi = true;
                        $schedule->timeStart = null; 
                        $schedule->timeEnd =  null;
                        
                        $schedule->save();

       } else 
       {

            if (count($request->addDay) > 1){
                $ctr=0;
                foreach ($request->addDay as $d) {
                    $schedule = new Schedule;
                    $schedule->user_id = $request->user_id;
                    $schedule->workday = $d;
                    $schedule->timeStart = date('H:i A',strtotime($request->addStart[$ctr])); 
                    $schedule->timeEnd =  date('H:i A',strtotime($request->addEnd[$ctr]));
                    $schedule->isFlexi = $request->isFlexi;
                    $schedule->save();
                    $ctr++;
                    
                }

                //now update every sched for flexi settings
                $userScheds = User::find($request->user_id)->schedules;
                foreach ($userScheds as $key) {
                    $sched = Schedule::find($key->id);
                    $sched->isFlexi = $request->isFlexi;
                    $sched->push();
                }

            } else {

                if (empty($request->addDay)) return "empty";
                else if (is_array($request->addDay) && count($request->addDay) == 1){
                     $schedule = new Schedule;
                        $schedule->user_id = $request->user_id;
                        $schedule->workday = $request->addDay[0];
                        $schedule->isFlexi = $request->isFlexi;
                        $schedule->timeStart = date('H:i A',strtotime($request->addStart[0])); 
                        $schedule->timeEnd =  date('H:i A',strtotime($request->addEnd[0]));
                        
                        $schedule->save();

                        //now update every sched for flexi settings
                        $userScheds = User::find($request->user_id)->schedules;
                        foreach ($userScheds as $key) {
                            $sched = Schedule::find($key->id);
                            $sched->isFlexi = $request->isFlexi;
                            $sched->push();
                        }


                }
                else {
                    $schedule = new Schedule;
                    $schedule->user_id = $request->user_id;
                    $schedule->workday = $request->addDay;
                    $schedule->timeStart =  date('H:i A',strtotime($request->addStart)); 
                    $schedule->timeEnd =  date('H:i A',strtotime($request->addEnd));
                    $schedule->isFlexi = $request->isFlexi;
                    $schedule->save();

                }

                //now update every sched for flexi settings
                $userScheds = User::find($request->user_id)->schedules;
                foreach ($userScheds as $key) {
                    $sched = Schedule::find($key->id);
                    $sched->isFlexi = $request->isFlexi;
                    $sched->push();
                }
                

            }

       }

        

    	

    	return response()->json(['updatedIsFlexi'=>$request->isFlexi]);
    }
}
