<?php

namespace OAMPI_Eval\Http\Controllers;

use Hash;
use Carbon\Carbon;
use Excel;
use \PDF;
use \DB;
use \App;
use \Response;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Yajra\Datatables\Facades\Datatables;

//use OAMPI_Eval\Http\Requests;
use OAMPI_Eval\User;
use OAMPI_Eval\Cutoff;
use OAMPI_Eval\User_Leader;
use OAMPI_Eval\User_CWS;
use OAMPI_Eval\User_DTRP;
use OAMPI_Eval\User_VL;
use OAMPI_Eval\User_SL;
use OAMPI_Eval\User_LWOP;
use OAMPI_Eval\User_OT;
use OAMPI_Eval\User_OBT;
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
use OAMPI_Eval\FixedSchedules;
use OAMPI_Eval\MonthlySchedules;
use OAMPI_Eval\Restday;
use OAMPI_Eval\Biometrics;
use OAMPI_Eval\User_VLcredits;
use OAMPI_Eval\User_SLcredits;
use OAMPI_Eval\FormBuilder;
use OAMPI_Eval\FormBuilder_Items;
use OAMPI_Eval\FormBuilderElem_Values;
use OAMPI_Eval\FormBuilderElements;
use OAMPI_Eval\FormBuilderSubtypes;
use OAMPI_Eval\FormSubmissions;
use OAMPI_Eval\FormSubmissionsUser;

class FormSubmissionsController extends Controller
{
    protected $user;
    protected $formBuilder;
    //use Traits\UserTraits;
    //use Traits\TimekeepingTraits;

     public function __construct(FormSubmissions $formSubmissions)
    {
        $this->middleware('auth');
        $this->user =  User::find(Auth::user()->id);
        $this->formSubmissions = $formSubmissions;
    }


    public function index()
    {
    	return view('forms.formBuilder-index');
    }

    public function getAll($id)
    {
        DB::connection()->disableQueryLog();
        //switch (Input::get('by')) {
        switch($id){ 

            //protocol
            case 'p': 
            {
                $protocol = FormBuilder_Items::where('label','Escalation')->first();
                $merchant = FormBuilder_Items::where('label','Merchant Name')->first();
                $form1 = DB::table('form_submissions_users')->where('form_submissions_users.formBuilder_id',$id)->
                    rightJoin('form_submissions','form_submissions.submission_user','=','form_submissions_users.id')->
                    where('form_submissions.formBuilder_itemID','=',$protocol->id);

                 $form2 = DB::table('form_submissions_users')->where('form_submissions_users.formBuilder_id',$id)->
                    rightJoin('form_submissions','form_submissions.submission_user','=','form_submissions_users.id')->
                    where('form_submissions.formBuilder_itemID','=',$merchant->id)->
                    union($form1)->get();

                    return $form2;

                    //join('users','form_submissions_users.user_id','=','users.id')->
                    //select('users.email', 'form_submissions.value as orderProtocol','form_submissions.created_at as dateSubmitted')->get();

                 
                //$merchant = FormBuilder_Items::where('label','Merchant Name')->first();
                    //['form_submissions.formBuilder_itemID','=',$merchant->id],
                // $form = DB::table('form_submissions_users')->where('form_submissions_users.formBuilder_id',$id)->
                //     rightJoin('form_submissions','form_submissions.submission_user','=','form_submissions_users.id')->
                //     where('form_submissions.formBuilder_itemID','=',$protocol->id)->
                //     join('users','form_submissions_users.user_id','=','users.id')->
                //     select('users.email', 'form_submissions.value as orderProtocol','form_submissions.created_at as dateSubmitted')->get();
                   

            }break;

            case '1': 
            {
                $form = DB::table('form_submissions_users')->where('form_submissions_users.formBuilder_id',$id)->
                    join('form_submissions','form_submissions.submission_user','=','form_submissions_users.id')->
                    join('formBuilder_items','form_submissions.formBuilder_itemID','=','formBuilder_items.id')->
                    leftJoin('users','form_submissions_users.user_id','=','users.id')->
                    select('form_submissions_users.id as submissionID','users.firstname','users.lastname','formBuilder_items.label','form_submissions.value','form_submissions_users.created_at')->get();
                    $submissions = collect($form)->groupBy('submissionID');
                    $headers = collect($form)->groupBy('label');
                    $coll = new Collection;

                    
                    foreach($submissions as $item) {

                        $c = new Collection;
                        $k = $item->pluck('value');
                        
                        $coll->push(['merchant'=>$k[1],'orderStatus'=>$k[5],'protocol'=>$k[3],'agent'=>$item->first()->firstname." ". $item->first()->lastname,'submitted'=>Carbon::parse($item->first()->created_at,"Asia/Manila")->format('M d,Y H:i:s'), 'hour'=> Carbon::parse($item->first()->created_at)->setTimeZone('PST')->format('H:i')]);
                       
                        
                    }

                
            }break;

            case '2': 
            {
                $form = DB::table('form_submissions_users')->where('form_submissions_users.formBuilder_id',$id)->
                    join('form_submissions','form_submissions.submission_user','=','form_submissions_users.id')->
                    join('formBuilder_items','form_submissions.formBuilder_itemID','=','formBuilder_items.id')->
                    leftJoin('users','form_submissions_users.user_id','=','users.id')->
                    select('form_submissions_users.id as submissionID','users.firstname','users.lastname','formBuilder_items.label','form_submissions.value','form_submissions_users.created_at')->get();
                    $submissions = collect($form)->groupBy('submissionID');
                    $headers = collect($form)->groupBy('label');
                    $coll = new Collection;

                    
                    foreach($submissions as $item) {

                        $c = new Collection;
                        $ctI = count($item);

                        //$k = $item->pluck('value');
                        
                        //$agent = $item->first()->firstname." ". $item->first()->lastname;
                        if ($ctI == 6)
                        $coll->push(['agent'=>$item->first()->firstname." ". $item->first()->lastname,
                                    $item[0]->label=>$item[0]->value,
                                    $item[1]->label=>$item[1]->value,
                                    $item[2]->label=>$item[2]->value, 
                                    $item[3]->label=>$item[3]->value,
                                    $item[4]->label=>$item[4]->value,
                                    $item[5]->label=>$item[5]->value,
                                    'submitted'=>Carbon::parse($item->first()->created_at,"Asia/Manila")->format('M d,Y H:i:s'), 
                                    'hour'=> Carbon::parse($item->first()->created_at)->setTimeZone('PST')->format('H:i')]);
                        elseif ($ctI == 7)
                        $coll->push(['agent'=>$item->first()->firstname." ". $item->first()->lastname,
                                    $item[0]->label=>$item[0]->value,
                                    $item[1]->label=>$item[1]->value,
                                    $item[2]->label=>$item[2]->value, 
                                    $item[3]->label=>$item[3]->value,
                                    $item[4]->label=>$item[4]->value,
                                    $item[5]->label=>$item[5]->value,
                                    $item[6]->label=>$item[6]->value,
                                    'submitted'=>Carbon::parse($item->first()->created_at,"Asia/Manila")->format('M d,Y H:i:s'), 
                                    'hour'=> Carbon::parse($item->first()->created_at)->setTimeZone('PST')->format('H:i')]);
                         elseif ($ctI == 8)
                        $coll->push(['agent'=>$item->first()->firstname." ". $item->first()->lastname,
                                    $item[0]->label=>$item[0]->value,
                                    $item[1]->label=>$item[1]->value,
                                    $item[2]->label=>$item[2]->value, 
                                    $item[3]->label=>$item[3]->value,
                                    $item[4]->label=>$item[4]->value,
                                    $item[5]->label=>$item[5]->value,
                                    $item[6]->label=>$item[6]->value,
                                    $item[7]->label=>$item[7]->value,
                                    'submitted'=>Carbon::parse($item->first()->created_at,"Asia/Manila")->format('M d,Y H:i:s'), 
                                    'hour'=> Carbon::parse($item->first()->created_at)->setTimeZone('PST')->format('H:i')]); 
                                    
                       
                        
                    }
                   // return $coll;

                
            }break;
            
            default: 
            {
                $form = DB::table('form_submissions_users')->where('form_submissions_users.formBuilder_id',$id)->
                    join('form_submissions','form_submissions.submission_user','=','form_submissions_users.id')->
                    join('formBuilder_items','form_submissions.formBuilder_itemID','=','formBuilder_items.id')->
                    leftJoin('users','form_submissions_users.user_id','=','users.id')->
                    select('form_submissions_users.id as submissionID','users.firstname','users.lastname','formBuilder_items.label','form_submissions.value','form_submissions_users.created_at')->get();
                    $submissions = collect($form)->groupBy('submissionID');
                    $headers = collect($form)->groupBy('label');
                    $coll = new Collection;

                    return $submissions;
                    foreach($submissions as $item) {

                        $c = new Collection;
                        $k = $item->pluck('value');
                        
                        $coll->push(['merchant'=>$k[1],'orderStatus'=>$k[5],'protocol'=>$k[3],'agent'=>$item->first()->firstname." ". $item->first()->lastname,'submitted'=>Carbon::parse($item->first()->created_at,"Asia/Manila")->format('M d,Y H:i:s'), 'hour'=> Carbon::parse($item->first()->created_at)->setTimeZone('PST')->format('H:i')]);
                       
                        
                    }

                
            }break;
        }
        

        //return $coll;
        switch (Input::get('type')) {
            case 'dt': return response()->json(['data'=>$coll]); break;
            case 'label': return response()->json($headers); break;
            
            default: return response()->json(['data'=>$coll]); break;
        }
        
        

    }

    public function getRanking($type)
    {
        switch ($type) {
            case '1': 
            { 
                DB::connection()->disableQueryLog();
                $form = FormBuilder::find($type);
                $rankCategory = FormBuilder_Items::where('label','Order Status')->first(); 
                $rankings = DB::table('form_submissions_users')->where('formBuilder_id',$form->id)->
                        join('form_submissions','form_submissions.submission_user','=','form_submissions_users.id')->
                        where('form_submissions.formBuilder_itemID','=',$rankCategory->id)->
                        leftJoin('users','users.id','=','form_submissions_users.user_id')->
                        select('users.firstname','users.nickname','users.lastname','users.id as userID','form_submissions.value')->get();

                        
                $topPips = collect($rankings)->groupBy('userID');
                $ranks = new Collection;
                foreach ($topPips as $t) {
                    $type = collect($t)->groupBy('value');
                    $ct = count($type);
                    $k = new Collection;

                   
                    foreach($type as $ty){ $k->push(['count'=>count($ty), 'item'=>$ty->first()->value]); }

                    $ranks->push(['id'=>$t->first()->userID, 'firstname'=>$t->first()->firstname, 
                                'lastname'=>$t->first()->lastname,
                                'submissions'=>$k,'claimed'=>count($t)]);
                   
                        
                }

                //$kol = $ranks->sortByDesc('claimed');
                return response()->json(['data'=>$ranks]);//->values()->all()] 

            } break;

            case '2': 
            { 
                DB::connection()->disableQueryLog();

                $form = FormBuilder::find($type); 
                $rankCategory = FormBuilder_Items::where('label','Confirmation')->where('formBuilder_id',$type)->first(); 

                $rankings = DB::table('form_submissions_users')->where('formBuilder_id',$form->id)->
                        join('form_submissions','form_submissions.submission_user','=','form_submissions_users.id')->
                        where('form_submissions.formBuilder_itemID','=',$rankCategory->id)->
                        leftJoin('users','users.id','=','form_submissions_users.user_id')->
                        select('users.firstname','users.nickname','users.lastname','users.id as userID','form_submissions.value')->get();

                   // return $rankings;    
                $topPips = collect($rankings)->groupBy('userID');

                //return $topPips;
                $ranks = new Collection;
                foreach ($topPips as $t) {
                    $type = collect($t)->groupBy('value');
                    $ct = count($type);
                    $k = new Collection;

                   
                    foreach($type as $ty){ $k->push(['count'=>count($ty), 'item'=>$ty->first()->value]); }

                    $ranks->push(['id'=>$t->first()->userID, 'firstname'=>$t->first()->firstname, 
                                'lastname'=>$t->first()->lastname,
                                'submissions'=>$k,'claimed'=>count($t)]);
                   
                        
                }

                //$kol = $ranks->sortByDesc('claimed');
                return response()->json(['data'=>$ranks]);//->values()->all()] 

            } break;
            
            default: 
            {
                $rankCategory = FormBuilder_Items::where('label','Order Status')->first(); 
                $rankings = DB::table('form_submissions_users')->where('formBuilder_id',$form->id)->
                        join('form_submissions','form_submissions.submission_user','=','form_submissions_users.id')->
                        where('form_submissions.formBuilder_itemID','=',$rankCategory->id)->
                        leftJoin('users','users.id','=','form_submissions_users.user_id')->
                        select('users.firstname','users.nickname','users.lastname','users.id as userID','form_submissions.value')->get();

                        
                $topPips = collect($rankings)->groupBy('userID');
                $ranks = new Collection;
                foreach ($topPips as $t) {
                    $type = collect($t)->groupBy('value');
                    $ct = count($type);
                    $k = new Collection;

                    foreach($type as $ty){ $k->push(['count'=>count($ty), 'item'=>$ty->first()->value]); }

                            $ranks->push(['id'=>$t->first()->userID, 'firstname'=>$t->first()->firstname, 
                                        'lastname'=>$t->first()->lastname,
                                        'submissions'=>$k]);
                      
                        
                }

                return response()->json(['data'=>$ranks]); 

            }break; 
        }

          

    }


    public function process(Request $request)
    {
        $formItems = collect($request->formItems);
        $user = User::find($request->user_id);
        $keys = $formItems->keys();
        $coll = new Collection;

        if ($formItems->contains("- select one -") || $formItems->contains("") )
            return response()->json(['status'=>0,'error'=>"Fill out all required fields before submitting."]);
        else {

            /* get from first item muna */
            $s = explode('_', $keys[0]);


            $formItem = FormBuilder_Items::find($s[1]);

            $userSubmission = new FormSubmissionsUser;
            $userSubmission->user_id = $user->id;
            $userSubmission->formBuilder_id = $formItem->formBuilder_id;
            $userSubmission->save();

            foreach($keys as $k){
                $s = explode('_', $k);
                
                /* ---quick hack for signal verification kakatamad i-restructure na DB */

                if ($s[1] == 'x'){
                    $name = "confirmed_".strtolower($formItems['x_from']);
                    $lookFor = FormBuilder_Items::where('name',$name)->get();
                    if (count($lookFor) > 0){
                        $pushItem = $lookFor->first()->id;

                        
                        $coll->push(['formBuilder_items.id'=>$pushItem, 'value'=>$formItems[$k]]);
                        $submission = new FormSubmissions;
                        $formItem = FormBuilder_Items::find($pushItem);

                        $submission->submission_user = $userSubmission->id;
                        $submission->formBuilder_itemID = $formItem->id;
                        //$submission->formBuilder_id = $formItem->formBuilder_id;
                        $submission->value = $formItems[$k];
                        $submission->save();
                        $coll->push(['submittedx'=>$submission, 's1'=>$s[1]]);

                    }else  $coll->push(['submittedy'=>$formItems[$k], 'lookfor'=>$name] );
                } else if($s[1] == 'from'){ //do nothing

                } else 
                    {
                        //$pushItem = $s[1];
                        $coll->push(['formBuilder_items.id'=>$s[1], 'value'=>$formItems[$k]]);
                        //$coll->push(['formBuilder_items.id'=>$pushItem, 'value'=>$formItems[$k]]);
                        $submission = new FormSubmissions;
                        $formItem = FormBuilder_Items::find($s[1]);

                        $submission->submission_user = $userSubmission->id;
                        $submission->formBuilder_itemID = $formItem->id;
                        //$submission->formBuilder_id = $formItem->formBuilder_id;
                        $submission->value = $formItems[$k];
                        $submission->save();
                        $coll->push(['submitted'=>$submission, 's1'=>$s[1] ]);
                    } 

                
            }

            // $correct = Carbon::now('GMT+8'); //->timezoneName();
            // if($this->user->id !== 564 ) {
            //   $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
            //     fwrite($file, "-------------------\n Submitted ReportForm id[".$userSubmission->id."] --" . $correct->format('M d h:i A'). " by [". $this->user->id."] ".$this->user->lastname."\n");
            //     fclose($file);
            // }


            return response()->json(['status'=>"ok", 'items'=>$coll]);

        }
                

        
        


    }


    public function show($id)
    {
        DB::connection()->disableQueryLog();
        $form = FormBuilder::find($id);

        $correct = Carbon::now('GMT+8'); //->timezoneName();
        if($this->user->id !== 564 ) {
          $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
            fwrite($file, "-------------------\n Viewed Form Submissions id[".$id."] --" . $correct->format('M d h:i A'). " by [". $this->user->id."] ".$this->user->lastname."\n");
            fclose($file);
        }

        if(empty($form)) return view('empty');

        switch ($form->id) {
            
            // POSTMATE ORDERING FORM
            case '1':{
                        $data = new Collection;
                        $data2 = new Collection;
                        $campaign = Campaign::where('name',"Postmates")->first();
                        $camp = $campaign->logo;
                        $logo = "../public/img/".$camp->filename;

                        $pips = DB::table('team')->where('campaign_id',$campaign->id)->
                                        join('users','users.id','=','team.user_id')->
                                        where([
                                                ['status_id','!=',7],
                                                ['status_id','!=',8],
                                                ['status_id','!=',9],

                                        ])->
                                        //rightJoin('form_submissions_users','form_submissions_users.user_id','=','users.id')->
                                        select('users.firstname','users.nickname','users.lastname','users.id as userID')->get();

                        $agents = new Collection;
                        foreach($pips as $p)
                        {
                            if( count(ImmediateHead::where('employeeNumber',User::find($p->userID)->employeeNumber)->get())< 1 )
                                $agents->push($p);
                        }

                        $escalations = DB::table('formBuilder_items')->where('formBuilder_id',1)->
                                            where('formBuilder_items.label','=','Escalation')->
                                            leftJoin('formBuilderElem_values','formBuilderElem_values.formBuilder_itemID','=','formBuilder_items.id')->
                                            select('formBuilderElem_values.label','formBuilderElem_values.value','formBuilder_items.id')->get();

                        $orderStatus = DB::table('formBuilder_items')->where('formBuilder_id',1)->
                                            where('formBuilder_items.label','=','Order Status')->
                                            leftJoin('formBuilderElem_values','formBuilderElem_values.formBuilder_itemID','=','formBuilder_items.id')->
                                            select('formBuilderElem_values.label','formBuilderElem_values.value','formBuilder_items.id')->get();                    
                        foreach($escalations as $e){
                            $fs = FormSubmissions::where('formBuilder_itemID',$e->id)->where('value',$e->value)->get();
                            $data->push(["label"=>$e->label, "count"=>count($fs)]);
                        }


                        $total = 0;
                        foreach($orderStatus as $e){
                            $fs = FormSubmissions::where('formBuilder_itemID',$e->id)->where('value',$e->value)->get();
                            $data2->push(["label"=>$e->label, "count"=>count($fs)]);
                            $total += count($fs);
                        }

                    }break;


            case '2':{
                        $data = new Collection;
                        $data2 = new Collection;
                        $campaign = Campaign::where('name',"Postmates")->first();
                        $camp = $campaign->logo;
                        $logo = "../public/img/".$camp->filename;

                        $pips = DB::table('team')->where('campaign_id',$campaign->id)->
                                        join('users','users.id','=','team.user_id')->
                                        where([
                                                ['status_id','!=',7],
                                                ['status_id','!=',8],
                                                ['status_id','!=',9],

                                        ])->
                                        //rightJoin('form_submissions_users','form_submissions_users.user_id','=','users.id')->
                                        select('users.firstname','users.nickname','users.lastname','users.id as userID')->get();

                        $agents = new Collection;
                        foreach($pips as $p)
                        {
                            if( count(ImmediateHead::where('employeeNumber',User::find($p->userID)->employeeNumber)->get())< 1 )
                                $agents->push($p);
                        }

                        $escalations = DB::table('formBuilder_items')->where('formBuilder_id',$form->id)->
                                            where('formBuilder_items.label','=','Confirmation')->
                                            leftJoin('formBuilderElem_values','formBuilderElem_values.formBuilder_itemID','=','formBuilder_items.id')->
                                           select('formBuilderElem_values.label','formBuilderElem_values.value','formBuilder_items.id')->get();

                        /*$orderStatus = DB::table('formBuilder_items')->where('formBuilder_id',$form->id)->
                                            where('formBuilder_items.label','=','Order Status')->
                                            leftJoin('formBuilderElem_values','formBuilderElem_values.formBuilder_itemID','=','formBuilder_items.id')->
                                            select('formBuilderElem_values.label','formBuilderElem_values.value','formBuilder_items.id')->get();                    */
                        foreach($escalations as $e){
                            $fs = FormSubmissions::where('formBuilder_itemID',$e->id)->where('value',$e->value)->get();
                            if (!empty($e->value))
                            $data->push(["label"=>$e->label, "count"=>count($fs)]);
                        }


                        $total = 0;
                        /*foreach($orderStatus as $e){
                            $fs = FormSubmissions::where('formBuilder_itemID',$e->id)->where('value',$e->value)->get();
                            $data2->push(["label"=>$e->label, "count"=>count($fs)]);
                            $total += count($fs);
                        }*/
                        $data2 = [];

                        //return $data;
                        return view('forms.formSubmissions-show2',compact('form','data','data2','total','logo'));
                    }break;
            
            default:{
                        $data = new Collection;
                        $data2 = new Collection;
                        $campaign = Campaign::where('name',"Postmates")->first();
                        $camp = $campaign->logo;
                        $logo = "../public/img/".$camp->filename;

                        $pips = DB::table('team')->where('campaign_id',$campaign->id)->
                                        join('users','users.id','=','team.user_id')->
                                        where([
                                                ['status_id','!=',7],
                                                ['status_id','!=',8],
                                                ['status_id','!=',9],

                                        ])->
                                        //rightJoin('form_submissions_users','form_submissions_users.user_id','=','users.id')->
                                        select('users.firstname','users.nickname','users.lastname','users.id as userID')->get();

                        $agents = new Collection;
                        foreach($pips as $p)
                        {
                            if( count(ImmediateHead::where('employeeNumber',User::find($p->userID)->employeeNumber)->get())< 1 )
                                $agents->push($p);
                        }

                        $escalations = DB::table('formBuilder_items')->where('formBuilder_id',1)->
                                            where('formBuilder_items.label','=','Escalation')->
                                            leftJoin('formBuilderElem_values','formBuilderElem_values.formBuilder_itemID','=','formBuilder_items.id')->
                                            select('formBuilderElem_values.label','formBuilderElem_values.value','formBuilder_items.id')->get();

                        $orderStatus = DB::table('formBuilder_items')->where('formBuilder_id',1)->
                                            where('formBuilder_items.label','=','Order Status')->
                                            leftJoin('formBuilderElem_values','formBuilderElem_values.formBuilder_itemID','=','formBuilder_items.id')->
                                            select('formBuilderElem_values.label','formBuilderElem_values.value','formBuilder_items.id')->get();                    
                        foreach($escalations as $e){
                            $fs = FormSubmissions::where('formBuilder_itemID',$e->id)->where('value',$e->value)->get();
                            $data->push(["label"=>$e->label, "count"=>count($fs)]);
                        }


                        $total = 0;
                        foreach($orderStatus as $e){
                            $fs = FormSubmissions::where('formBuilder_itemID',$e->id)->where('value',$e->value)->get();
                            $data2->push(["label"=>$e->label, "count"=>count($fs)]);
                            $total += count($fs);
                        }

                    }break;
        }
        //return $data2;
        //return $topAgents;

        


        return view('forms.formSubmissions-show',compact('form','data','data2','total','logo'));
    }
}
