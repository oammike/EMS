<?php

namespace OAMPI_Eval\Http\Controllers\Traits;

use Carbon\Carbon;
use Excel;
use \PDF;
use \App;
use \DB;
use \Response;
use \Log;
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


use OAMPI_Eval\EvalType;
use OAMPI_Eval\Movement;
use OAMPI_Eval\RatingScale;
use OAMPI_Eval\EvalDetail;
use OAMPI_Eval\Competency;
use OAMPI_Eval\Competency__Attribute;
use OAMPI_Eval\Attribute;
use OAMPI_Eval\Summary;
use OAMPI_Eval\PerformanceSummary;
use OAMPI_Eval\Movement_ImmediateHead;
use OAMPI_Eval\Movement_Positions;
use OAMPI_Eval\Notification;
use OAMPI_Eval\User_Notification;


trait EvaluationTraits
{

	public function getPastMemberEvals($mc, $evalSetting, $cur, $endP, $evalType)
	{
		/* ---------------------------------------------------------------- 

            GET PAST MEMBERS moved to you

        /* ---------------------------------------------------------------- */
        $coll2 = new Collection;
       
        $me = $mc->first();


        /* ----- fix for Regularization --------*/

        if (empty($cur) && empty($endP)) //meaning REGULARIZATION EVAL sya
        {

            /*** OLD --- foreach ($me->myCampaigns as $m) { */
            $changedImmediateHeads = new Collection;
            $doneMovedEvals = new Collection;
            $ctr = 0;



            foreach($mc as $m)
            { 

                $moved = Movement_ImmediateHead::where('imHeadCampID_old',$m->id)->get();

               

                $changedHeads = new Collection;
                $chIH = new Collection;

                foreach ($moved as $m) {
                    $changedHeads->push($m->info);
                }

                foreach ($changedHeads as $mvt) {
                  
                  $effective = Carbon::createFromFormat('Y-m-d H:i:s', $mvt->effectivity, 'Asia/Manila');

                  // if movement is within the range of eval period
                  if ($mvt->isDone )
                  {
                    //$chIH->push($changedHeads->first());
                    $chIH->push($mvt);
                    
                  }  
                                                        
                }




                foreach ($chIH as $emp) 
                {
                    $employ = User::find($emp->user_id);

                    /*------------- NEW UPDATE ---------------/
                       we need to check if emp was PROMOTED from agent or not

                    -----------------------------------------*/

                     $currentPeriod = Carbon::createFromFormat('Y-m-d H:i:s',$employ->dateHired,'Asia/Manila');
                     $cPeriod = Carbon::createFromFormat('Y-m-d H:i:s',$employ->dateHired,'Asia/Manila');
                     
                     if ($evalType==3){
                        $endPeriod = $cPeriod->addMonths(6);
                    }else if($evalType==4 || $evalType==5){
                        $endPeriod = $cPeriod->addMonths(3);
                    }


                     // GET ALL his POSITION movements from latest to oldest
                    $checkMovements = Movement::where('user_id',$emp->user_id)->where('personnelChange_id','2')->where('isDone','1')->where('effectivity','>=',$currentPeriod->toDateString())->where('effectivity','<=',$endPeriod->toDateString())->orderBy('effectivity','DESC')->get(); 

                    
                    if (count($checkMovements) > 0)
                    {

                         //***** check old campaign
                        $checkOldCamp = Movement::where('user_id',$emp->user_id)->where('personnelChange_id','1')->where('isDone','1')->where('effectivity','>=',$currentPeriod->toDateString())->where('effectivity','<=',$endPeriod->toDateString())->orderBy('effectivity','ASC')->get(); 

                        if (count($checkOldCamp) > 0) { 
                            
                            if (!$checkOldCamp->first()->withinProgram){
                                $oldCamp = Movement_ImmediateHead::where('movement_id',$checkOldCamp->first()->id)->first();
                                $formerCamp = Campaign::find(ImmediateHead_Campaign::find($oldCamp->imHeadCampID_old)->campaign_id)->name;
                            } else $formerCamp = "";
                             
                        } else $formerCamp = "";
                        

                        //determine if his old position is as a LEADER or not
                        $movedPos = $checkMovements->first();
                        $oldPos = Position::find(Movement_Positions::where('movement_id',$movedPos->id)->first()->position_id_old); 

                        if (empty($oldPos->leadershipRole)){
                            $isLead=false; 
                            $mustPosition = "(Former ". $oldPos->name . " - ". $formerCamp.")";
                        } else {$isLead = $oldPos->leadershipRole; $mustPosition=$employ->position->name; }

                    }else {
                        //verify nga kung leader ba talaga or hindi
                        $leadershipcheck = ImmediateHead::where('employeeNumber', $employ->employeeNumber)->get();
                        (count($leadershipcheck) > 0) ? $isLead=true : $isLead=false;
                        $mustPosition = $employ->position->name;
                    }

                    //$coll2->push(['movedPos'=>$movedPos, 'oldPos'=>$oldPos, 'newPos'=>$newPos]);


                    //disregard resigned or terminated employees
                    if ($employ->status_id != 7 &&  $employ->status_id != 8 && $employ->status_id != 9)
                    {
                        $hisTeam = $employ->team;
                        $hisTL = ImmediateHead::find(Team::find($hisTeam->id)->leader->immediateHead_id);
                        

                        /* -------- we need to check first if YOU are the CURRENT TL. if yes,no need to be added to changedImmediateHeads  ----- */

                        // if ($employ->supervisor->first()->immediateHead_id !== $me->id)
                        // {
                                $changedImmediateHeads->push([
                                                        'movement_id'=> $emp->id,
                                                        'id'=>$employ->id, 
                                                        'index'=> $ctr,
                                                        'user_id'=>$employ->id, 
                                                        'userType_id'=>$employ->userType_id, 
                                                        'dateHired'=>$employ->dateHired, 
                                                        'firstname'=> $employ->firstname, 
                                                        'lastname'=>$employ->lastname, 
                                                        'position'=>$mustPosition, 
                                                        'isLead'=>$isLead,
                                                        'status'=>$employ->status->name]);

                              
                                
                                
                                $effective = Carbon::createFromFormat('Y-m-d H:i:s', $emp->effectivity, 'Asia/Manila');

                                // **** GET THE EVAL RANGES, pag fromPeriod eh wayy past currentPeriod -- get current
                                if($emp->fromPeriod < $currentPeriod->startOfDay()->format('Y-m-d H:i:s')) {

                                    $fr = $currentPeriod->startOfDay(); // Carbon::createFromFormat('Y-m-d H:i:s', $tillWhen->first()->fromPeriod, 'Asia/Manila'); 

                                } elseif ($emp->fromPeriod > $currentPeriod->format('Y-m-d H:i:s')) { //pag in the future pa, kunin mo currentperiod
                                    $fr = $currentPeriod->startOfDay();
                                } else $fr = Carbon::createFromFormat('Y-m-d H:i:s', $emp->fromPeriod, 'Asia/Manila'); 

                                //------- update Sep 21
                                 // **** fix for movements na di pa complete yung previous eval:
                                if ($emp->effectivity >= $endPeriod->format('Y-m-d'))
                                {
                                    $to = Carbon::createFromFormat('Y-m-d H:i:s', $emp->effectivity, 'Asia/Manila'); //$endPeriod;

                                } elseif ($emp->effectivity < $fr) { //pag super tagal na prior to start of eval period, 
                                    $to = Carbon::createFromFormat('Y-m-d H:i:s', $emp->effectivity, 'Asia/Manila'); //$endPeriod;
                                } else $to = Carbon::createFromFormat('Y-m-d H:i:s', $emp->effectivity, 'Asia/Manila'); //

                               
                                $evalBy = $me->id;  
                                //$coll->push(['from: '=>$fr, 'to: '=>$to->startOfDay()]);

                                $evaluated = EvalForm::where('user_id', $emp->user_id)->where('evaluatedBy', $evalBy)->where('evalSetting_id',$evalSetting->id)->where('startPeriod','>=',$fr)->where('endPeriod','<=', $to)->get(); //->where('endPeriod','<=', $to->startOfDay())->get(); //->get(); //where('startPeriod','>=',$currentPeriod)->where('endPeriod','<=',$endPeriod)->orderBy('id','DESC')->get();
                                //$coll2->push(['evaluated'=>$evaluated]);
                            
                             

                                if ( count($evaluated) == 0)
                                {
                                    $doneMovedEvals[$ctr] = ['user_id'=>$emp->user_id,'evaluated'=>0,'isDraft'=>0, 'coachingDone'=>false, 'evalForm_id'=> null, 'score'=>null,'startPeriod'=>$fr->format('M d, Y'), 'endPeriod'=>$to->format('M d, Y')];

                                    
                                } else {

                                    $theeval = EvalForm::find( $evaluated->sortByDesc('id')->first()->id);
                                    $truegrade = $theeval->overAllScore;

                                    if ($theeval->isDraft) 
                                      $doneMovedEvals[$ctr] = ['evaluated'=>1, 'isDraft'=>1, 'evalForm_id'=> $evaluated->first()->id, 'score'=>$truegrade, 'startPeriod'=>$theeval->startPeriod, 'endPeriod'=>$theeval->endPeriod];
                                    else
                                    //$doneEval[$emp->id] = ['evaluated'=>1, 'evalForm_id'=> $existing->first()->id, 'score'=>$truegrade, 'startPeriod'=>$currentPeriod->format('M d, Y'), 'endPeriod'=>$endPeriod->format('M d, Y')];
                                    $doneMovedEvals[$ctr] = ['evaluated'=>1, 'isDraft'=>0, 'evalForm_id'=> $theeval->id, 'score'=>$truegrade, 'startPeriod'=>date('M d, Y', strtotime($theeval->startPeriod)), 'endPeriod'=>date('M d,Y',strtotime($theeval->endPeriod))];



                                    
                                }




                        // }// end if you're not the current immediateHead

                        
                          $ctr++;


                    }//end if not resigned, terminated or end of contract
                    
                   
                }//end foreach chIH


        


            }//end foreach campaign

            $coll = new Collection;
            $coll->push(['doneMovedEvals'=>$doneMovedEvals, 'changedImmediateHeads'=>$changedImmediateHeads, 'changedHeads'=>$changedHeads, 'chIH'=>$chIH, 'endPeriod'=>null, 'startPeriod'=>null]);


        } else //regular evals
        { 

            $currentPeriod = $cur;
            $endPeriod = $endP;


                /*** OLD --- foreach ($me->myCampaigns as $m) { */
            $changedImmediateHeads = new Collection;
            $doneMovedEvals = new Collection;
            $ctr = 0;

            //pang double check ng year
            // if (date('m') >= 7 && date('m')<= 12)
            //               {
            //                 $currentPeriod = Carbon::create((date("Y")), $evalSetting->startMonth, $evalSetting->startDate,0,0,0, 'Asia/Manila');
            //                 $endPeriod = Carbon::create((date("Y")), $evalSetting->endMonth, $evalSetting->endDate,0,0,0, 'Asia/Manila');

            //               } else
            //               {
            //                 $currentPeriod = Carbon::create((date("Y")-1), $evalSetting->startMonth, $evalSetting->startDate,0,0,0, 'Asia/Manila');
            //                 $endPeriod = Carbon::create((date("Y")-1), $evalSetting->endMonth, $evalSetting->endDate,0,0,0, 'Asia/Manila');

            //               }


            foreach($mc as $m)
            { 

            

                //$moved = Movement_ImmediateHead::where('imHeadCampID_old',$m->id)->get();
                $moved = Movement_ImmediateHead::where('imHeadCampID_old',$m->id)->get();
                //$moved = Movement_ImmediateHead::where('imHeadCampID_old',$m->immediateHead_id)->get();
                //$coll->push($moved);
                //$moved = Movement_ImmediateHead::where('imHeadCampID_old',$ihCamp->id)->get();
               

                $changedHeads = new Collection;
                $chIH = new Collection;

                foreach ($moved as $m) {
                    $changedHeads->push($m->info);
                }
                
                



               
                foreach ($changedHeads as $mvt) {
                  
                  $effective = Carbon::createFromFormat('Y-m-d H:i:s', $mvt->effectivity, 'Asia/Manila');

                  // if movement is within the range of eval period
                  if ($mvt->isDone && ( $effective->format('Y-m-d H:i:s') != $currentPeriod->format('Y-m-d H:i:s') ) && ($effective->format('Y-m-d H:i:s') >= $currentPeriod->format('Y-m-d H:i:s') && $effective <= $endPeriod->format('Y-m-d H:i:s')) )
                  {
                    //$chIH->push($changedHeads->first());
                    $chIH->push($mvt);
                    //$coll2->push(['effective'=>$effective->format('Y-m-d H:i:s'), 'currentPeriod'=>$currentPeriod->format('Y-m-d H:i:s'), 'endPeriod'=>$endPeriod->format('Y-m-d H:i:s')]);
                    
                  }  

                  /*//------ update Sep 21
                  else { // let's check if he already has an eval for that period

                    $existingEval = EvalForm::where('evaluatedBy',$me->id)->where('user_id', $mvt->user_id)->where('startPeriod','>=',$currentPeriod->format('Y-m-d'))->where('endPeriod','<=',$endPeriod->format('Y-m-d'))->get();
                    
                    if ($existingEval->isEmpty())
                    {
                        //$chIH->push($mvt); 
                        //$chIH->push(count($existingEval));

                    } //else $chIH->push($mvt);

                  }
                  //------ end update
                  */
                                                        
                }




                foreach ($chIH as $emp) 
                {
                    $employ = User::find($emp->user_id);

                    /*------------- NEW UPDATE ---------------/
                       we need to check if emp was PROMOTED from agent or not

                    -----------------------------------------*/

                     // GET ALL his POSITION movements from latest to oldest
                    $checkMovements = Movement::where('user_id',$emp->user_id)->where('personnelChange_id','2')->where('isDone','1')->where('effectivity','>=',$currentPeriod->toDateString())->where('effectivity','<=',$endPeriod->toDateString())->orderBy('effectivity','DESC')->get(); 

                    
                    if (count($checkMovements) > 0)
                    {

                        //***** check old campaign
                        $checkOldCamp = Movement::where('user_id',$emp->user_id)->where('personnelChange_id','1')->where('isDone','1')->where('effectivity','>=',$currentPeriod->toDateString())->where('effectivity','<=',$endPeriod->toDateString())->orderBy('effectivity','ASC')->get(); 

                        if (count($checkOldCamp) > 0) { 
                            
                            if (!$checkOldCamp->first()->withinProgram){
                                $oldCamp = Movement_ImmediateHead::where('movement_id',$checkOldCamp->first()->id)->first();
                                $formerCamp = Campaign::find(ImmediateHead_Campaign::find($oldCamp->imHeadCampID_old)->campaign_id)->name;
                            } else $formerCamp = "";
                             
                        } else $formerCamp = "";


                        //$coll2->push($checkMovements->first());

                        //determine if his old position is as a LEADER or not
                        $movedPos = $checkMovements->first();
                        $oldPos = Position::find(Movement_Positions::where('movement_id',$movedPos->id)->first()->position_id_old); //
                        //$newPos = Position::find(Movement_Positions::where('movement_id',$movedPos->id)->first()->position_id_new)->leadershipRole;

                        // if ( $oldPos != $newPos ){
                        //     ( ($oldPos!= true) && $newPos) ? $promoted=true : $promoted=false;

                        // }else $promoted=false;


                        if (empty($oldPos->leadershipRole)){
                            $isLead=false; 
                            $mustPosition = "(Former ". $oldPos->name . " -  ". $formerCamp . " )";
                        } else { $isLead = $oldPos->leadershipRole; $mustPosition = "(Former ". $oldPos->name . " -  ". $formerCamp . " )";}

                        

                    }else {
                        //verify nga kung leader ba talaga or hindi
                        $leadershipcheck = ImmediateHead::where('employeeNumber', $employ->employeeNumber)->get();
                        (count($leadershipcheck) > 0) ? $isLead=true : $isLead=false;
                        $mustPosition = $employ->position->name;
                    }

                    //$coll2->push(['movedPos'=>$movedPos, 'oldPos'=>$oldPos, 'newPos'=>$newPos]);


                    //disregard resigned or terminated employees
                    if ($employ->status_id != 7 &&  $employ->status_id != 8 && $employ->status_id != 9)
                    {
                        $hisTeam = $employ->team;
                        $hisTL = ImmediateHead::find(Team::find($hisTeam->id)->leader->immediateHead_id);
                        

                        /* -------- we need to check first if YOU are the CURRENT TL. if yes,no need to be added to changedImmediateHeads  ----- */

                        // if ($employ->supervisor->first()->immediateHead_id !== $me->id)
                        // {
                                $changedImmediateHeads->push([
                                                        'movement_id'=> $emp->id,
                                                        'id'=>$employ->id, 
                                                        'index'=> $ctr,
                                                        'user_id'=>$employ->id, 
                                                        'userType_id'=>$employ->userType_id, 
                                                        'dateHired'=>$employ->dateHired, 
                                                        'firstname'=> $employ->firstname, 
                                                        'lastname'=>$employ->lastname, 
                                                        'position'=>$mustPosition, 
                                                        'isLead'=>$isLead,
                                                        'status'=>$employ->status->name]);

                              
                                
                                
                                $effective = Carbon::createFromFormat('Y-m-d H:i:s', $emp->effectivity, 'Asia/Manila');

                                // **** GET THE EVAL RANGES, pag fromPeriod eh wayy past currentPeriod -- get current
                                //-------- 06.22.2018 fix : if movement from eh hired date, get effectivity

                                if ($emp->fromPeriod < $currentPeriod->startOfDay()->format('Y-m-d H:i:s') &&  $emp->fromPeriod == $employ->dateHired)
                                {
                                    $fr = $effective;
                                }
                                else if($emp->fromPeriod < $currentPeriod->startOfDay()->format('Y-m-d H:i:s') && $emp->fromPeriod !== $employ->dateHired) {

                                    $fr = $currentPeriod->startOfDay(); // Carbon::createFromFormat('Y-m-d H:i:s', $tillWhen->first()->fromPeriod, 'Asia/Manila'); 

                                /*} elseif ($emp->fromPeriod > $currentPeriod->format('Y-m-d H:i:s')) { //pag in the future pa, kunin mo currentperiod
                                    $fr = $currentPeriod->startOfDay(); */
                                } else $fr = Carbon::createFromFormat('Y-m-d H:i:s', $emp->fromPeriod, 'Asia/Manila'); 

                                //------- update Sep 21
                                 // **** fix for movements na di pa complete yung previous eval:
                                if ($emp->effectivity >= $endPeriod->format('Y-m-d'))
                                {
                                    $to = Carbon::createFromFormat('Y-m-d H:i:s', $emp->effectivity, 'Asia/Manila'); //$endPeriod;

                                } elseif ($emp->effectivity < $fr) { //pag super tagal na prior to start of eval period, 
                                    $to = Carbon::createFromFormat('Y-m-d H:i:s', $emp->effectivity, 'Asia/Manila'); //$endPeriod;
                                } else $to = Carbon::createFromFormat('Y-m-d H:i:s', $emp->effectivity, 'Asia/Manila'); //

                                //$coll2->push(['emp'=>$employ->lastname, 'effective'=>$emp->effectivity, 'emp-fromPeriod'=>$emp->fromPeriod, 'currentPeriod'=>$currentPeriod->startOfDay()->format('Y-m-d H:i:s'), 'fr'=>$fr->startOfDay()->format('Y-m-d H:i:s'), 'endPeriod'=>$endPeriod->format('Y-m-d'), 'to'=>$to]);


                                


                                /* ## OLD: $evaluated = EvalForm::where('user_id', $emp->user_id)->where('evaluatedBy', $me->id)->where('startPeriod',$fr)->where('endPeriod',$to)->orderBy('id','DESC')->get(); //where('startPeriod','>=',$currentPeriod)->where('endPeriod','<=',$endPeriod)->orderBy('id','DESC')->get();
                                
                                */
                                $evalBy = $me->id;  
                                //$coll->push(['from: '=>$fr, 'to: '=>$to->startOfDay()]);

                                $evaluated = EvalForm::where('user_id', $emp->user_id)->where('evaluatedBy', $evalBy)->where('evalSetting_id',$evalSetting->id)->where('startPeriod','>=',$fr)->where('endPeriod','<=', $to)->get(); //->where('endPeriod','<=', $to->startOfDay())->get(); //->get(); //where('startPeriod','>=',$currentPeriod)->where('endPeriod','<=',$endPeriod)->orderBy('id','DESC')->get();
                                //$coll2->push(['evaluated'=>$evaluated]);
                            
                             

                                if ( count($evaluated) == 0)
                                {
                                    $doneMovedEvals[$ctr] = ['user_id'=>$emp->user_id,'evaluated'=>0,'isDraft'=>0, 'coachingDone'=>false, 'evalForm_id'=> null, 'score'=>null,'startPeriod'=>$fr->format('M d, Y'), 'endPeriod'=>$to->format('M d, Y')];

                                    
                                } else {

                                    $theeval = EvalForm::find( $evaluated->sortByDesc('id')->first()->id);
                                    $truegrade = $theeval->overAllScore;

                                    if ($theeval->isDraft) 
                                      $doneMovedEvals[$ctr] = ['evaluated'=>1, 'isDraft'=>1, 'evalForm_id'=> $evaluated->first()->id, 'score'=>$truegrade, 'startPeriod'=>$theeval->startPeriod, 'endPeriod'=>$theeval->endPeriod];
                                    else
                                    //$doneEval[$emp->id] = ['evaluated'=>1, 'evalForm_id'=> $existing->first()->id, 'score'=>$truegrade, 'startPeriod'=>$currentPeriod->format('M d, Y'), 'endPeriod'=>$endPeriod->format('M d, Y')];
                                    $doneMovedEvals[$ctr] = ['evaluated'=>1, 'isDraft'=>0, 'evalForm_id'=> $theeval->id, 'score'=>$truegrade, 'startPeriod'=>date('M d, Y', strtotime($theeval->startPeriod)), 'endPeriod'=>date('M d,Y',strtotime($theeval->endPeriod))];



                                    
                                }




                        // }// end if you're not the current immediateHead

                        
                          $ctr++;


                    }//end if not resigned, terminated or end of contract
                    
                   
                }//end foreach chIH


        


            }//end foreach campaign

            $coll = new Collection;
            $coll->push(['doneMovedEvals'=>$doneMovedEvals, 'changedImmediateHeads'=>$changedImmediateHeads, 'changedHeads'=>$changedHeads, 'chIH'=>$chIH, 'endPeriod'=>$endPeriod->format('Y-m-d'), 'startPeriod'=>$currentPeriod->format('Y-m-d')]);
        } //end else regular eval process

        /* ----- end fix for Regularization --------*/


                            
                          
                          
      

        return $coll;
        //return $coll2;
        //return $chIH;
		
	}

    public function getCorrectPosition($emp,$currentPeriod,$endPeriod)
    {
         // GET ALL his POSITION movements from latest to oldest
                $checkMovements = Movement::where('user_id',$emp->id)->where('personnelChange_id','2')->where('isDone','1')->where('effectivity','>=',$currentPeriod->toDateString())->where('effectivity','<=',$endPeriod->toDateString())->orderBy('effectivity','DESC')->get(); 

                if (count($checkMovements) > 0)
                {
                    //$coll2->push($checkMovements->first());
                     //***** check old campaign
                        $checkOldCamp = Movement::where('user_id',$emp->id)->where('personnelChange_id','1')->where('isDone','1')->where('effectivity','>=',$currentPeriod->toDateString())->where('effectivity','<=',$endPeriod->toDateString())->orderBy('effectivity','ASC')->get(); 

                        if (count($checkOldCamp) > 0) { 
                            
                            if (!$checkOldCamp->first()->withinProgram){
                                $oldCamp = Movement_ImmediateHead::where('movement_id',$checkOldCamp->first()->id)->first();
                                $formerCamp = Campaign::find(ImmediateHead_Campaign::find($oldCamp->imHeadCampID_old)->campaign_id)->name;
                            } else $formerCamp = "";
                             
                        } else $formerCamp = "";



                    //determine if his old position is as a LEADER or not
                    $movedPos = $checkMovements->first();
                    $oldPos = Position::find(Movement_Positions::where('movement_id',$movedPos->id)->first()->position_id_old); 

                    if (empty($oldPos->leadershipRole)){
                        $isLead=false; 
                        $mustPosition = "(Former ". $oldPos->name . " - ". $formerCamp.")";
                    } else {$isLead = $oldPos->leadershipRole; $mustPosition=$emp->position->name;}

                }else {
                    //verify nga kung leader ba talaga or hindi
                    $leadershipcheck = ImmediateHead::where('employeeNumber', $emp->employeeNumber)->get();
                    (count($leadershipcheck) > 0) ? $isLead=true : $isLead=false;
                    $mustPosition = $emp->position->name;
                }

        return $mustPosition;
        //return $checkMovements;
    }
}

?>