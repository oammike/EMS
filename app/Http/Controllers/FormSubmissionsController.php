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
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
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
use OAMPI_Eval\FormSubmissionsReviewer;

class FormSubmissionsController extends Controller
{
    protected $user;
    protected $formBuilder;
    use Traits\FormBuilderTraits;
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

    public function deleteDupes(Request $request)
    {
        $coll = new Collection;
        foreach($request->items as $d)
        {
            $del = FormSubmissionsUser::find($d);
            $coll->push($del);
            $del->delete();
            
        }
        
        //return back();
        return response()->json(['success'=>1,'items'=>$coll]);
    }

    public function deleteThis($id)
    {
        $del = FormSubmissionsUser::find($id);
        $correct = Carbon::now('+GMT8');

        if($this->user->id !== 564 ) {
             
            $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
              fwrite($file, "-------------------\n FormSubmisison [".$del->id."] DEL  by [". $this->user->id."] ".$this->user->lastname." on ". $correct->format('M d h:i A').  "\n");
              fclose($file);
          } 


        $del->delete();
        return back();
        //return response()->json(['success'=>1]);
    }



    public function downloadCSV($id, Request $request)
    {

        DB::connection()->disableQueryLog();
        $correct = Carbon::now('GMT+8');
        
        $from = Carbon::parse($request->from, 'Asia/Manila')->format('Y-m-d H:i:s');
        $to =  Carbon::parse($request->to, 'Asia/Manila')->format('Y-m-d H:i:s');

        switch($id)
        {
            case '1': 
            {
                $sheetTitle = FormBuilder::find($id)->title;
                $form = DB::table('form_submissions_users')->where('form_submissions_users.formBuilder_id',$id)->
                        where('form_submissions_users.created_at','>=',$from)->
                        where('form_submissions_users.created_at','<=',$to)->
                        join('form_submissions','form_submissions.submission_user','=','form_submissions_users.id')->
                        join('formBuilder_items','form_submissions.formBuilder_itemID','=','formBuilder_items.id')->
                        leftJoin('users','form_submissions_users.user_id','=','users.id')->
                        select('form_submissions_users.id as submissionID','users.firstname','users.lastname','formBuilder_items.label','form_submissions.value','form_submissions_users.created_at')->get();
                $submissions = collect($form)->groupBy('submissionID');
                $headers = array('Agent','Customer Name ', 'Merchant Name', 'Merchant Phone Number','Escalation/level 1','Escalation/level 2', 'Order Status','Notes','Date Submitted','PST hour');
                $description = "agent submission for Postmates' ". $sheetTitle;
            }break;

            case '2': 
            {
                $sheetTitle = FormBuilder::find($id)->title;
               
                $form = DB::table('form_submissions_users')->where('form_submissions_users.formBuilder_id',$id)->
                    where('form_submissions_users.created_at','>=',$from)->
                    where('form_submissions_users.created_at','<=',$to)->
                    join('form_submissions','form_submissions.submission_user','=','form_submissions_users.id')->
                    join('formBuilder_items','form_submissions.formBuilder_itemID','=','formBuilder_items.id')->
                    leftJoin('users','form_submissions_users.user_id','=','users.id')->
                    select('form_submissions_users.id as submissionID','users.firstname','users.lastname','formBuilder_items.label','form_submissions.value','form_submissions_users.created_at')->get();
                    $submissions = collect($form)->groupBy('submissionID');

                    //return response()->json($submissions);
                    $headers = array('Agent','Merchant Name','Merchant Location', 'Merchant Phone Number','Confirmation/level 1','Confirmation/level 2', 'Confirmation/level 3','Confirmation/level 4', 'Notes','Date Submitted','PST hour');

                    $description = "agent submission for Postmates' ". $sheetTitle;
                    
                 

                
            }break;
        
        }

        if($this->user->id !== 564 ) {
          $user = User::find(Input::get('id'));
         
          
          $file = fopen('public/build/postmates.txt', 'a') or die("Unable to open logs");
            fwrite($file, "-------------------\n Downloaded CSV [".$id."] [".$from." - ".$to."] " . $correct->format('M d h:i A'). " by [". $this->user->id."] ".$this->user->lastname."\n");
            fclose($file);
        } 


        Excel::create($sheetTitle,function($excel) use($id,$submissions, $sheetTitle, $headers,$description) 
           {
                  $excel->setTitle($sheetTitle.' Summary Report');

                  // Chain the setters
                  $excel->setCreator('Programming Team')
                        ->setCompany('OpenAccess');

                  // Call them separately
                  $excel->setDescription($description);
                  $excel->sheet("Sheet 1", function($sheet) use ($id,$submissions, $headers)
                  {
                    $sheet->appendRow($headers);
                    switch ($id) {
                        case '1':
                        {
                            foreach($submissions as $item)
                            {
                                
                                $k = $item->pluck('value');
                                $arr = array($item->first()->firstname." ". $item->first()->lastname,
                                            $k[0],$k[1],$k[2],$k[3],$k[4],$k[5], $k[6],
                                            Carbon::parse($item->first()->created_at,"Asia/Manila")->format('M d,Y H:i:s'),
                                            Carbon::parse($item->first()->created_at)->setTimeZone('PST')->format('H:i')
                                        );
                                $sheet->appendRow($arr);

                            }

                        }break;

                        case '2':
                        {
                            foreach($submissions as $item)
                            {
                                $ctI = count($item);

                                if ($ctI==5){
                                     $arr = array($item->first()->firstname." ". $item->first()->lastname,
                                            $item[0]->value,
                                            $item[1]->value,
                                            $item[2]->value,
                                            $item[3]->value,
                                            
                                        
                                            '-','-','-',
                                            $item[4]->value,
                                            Carbon::parse($item->first()->created_at,"Asia/Manila")->format('M d,Y H:i:s'),
                                            Carbon::parse($item->first()->created_at)->setTimeZone('PST')->format('H:i')
                                        );

                                 }
                                if ($ctI == 6)
                                {
                                    
                                    //Agent','Customer Name ', 'Merchant Name', 'Merchant Phone Number','Confirmation/level 1','Confirmation/level 2', 'Confirmation/level 3','Confirmation/level 4', 'Notes','Date Submitted','PST hour'
                                    if(!empty($item[5]))
                                     $arr = array($item->first()->firstname." ". $item->first()->lastname,
                                            $item[0]->value,
                                            $item[1]->value,
                                            $item[2]->value,
                                            $item[3]->value,
                                            $item[4]->value,
                                            '-','-',
                                            $item[5]->value,
                                            Carbon::parse($item->first()->created_at,"Asia/Manila")->format('M d,Y H:i:s'),
                                            Carbon::parse($item->first()->created_at)->setTimeZone('PST')->format('H:i')
                                        );
                                    else
                                        $arr = array($item->first()->firstname." ". $item->first()->lastname,
                                            $item[0]->value,
                                            $item[1]->value,
                                            $item[2]->value,
                                            $item[3]->value,
                                            $item[4]->value,
                                            
                                            '-', 
                                            '-',
                                            '-',
                                            Carbon::parse($item->first()->created_at,"Asia/Manila")->format('M d,Y H:i:s'),
                                            Carbon::parse($item->first()->created_at)->setTimeZone('PST')->format('H:i')
                                        );
                                }
                                
                                elseif ($ctI == 7)
                                {
                                    if(!empty($item[6]))
                                        $arr = array($item->first()->firstname." ". $item->first()->lastname,
                                            $item[0]->value,
                                            $item[1]->value,
                                            $item[2]->value,
                                            $item[3]->value,
                                            $item[4]->value,
                                            $item[5]->value,
                                            '-',
                                            $item[6]->value,
                                            Carbon::parse($item->first()->created_at,"Asia/Manila")->format('M d,Y H:i:s'),
                                            Carbon::parse($item->first()->created_at)->setTimeZone('PST')->format('H:i')
                                        );
                                    else
                                        $arr = array($item->first()->firstname." ". $item->first()->lastname,
                                            $item[0]->value,
                                            $item[1]->value,
                                            $item[2]->value,
                                            $item[3]->value,
                                            $item[4]->value,
                                            
                                            '-','-',
                                            $item[5]->value,
                                            Carbon::parse($item->first()->created_at,"Asia/Manila")->format('M d,Y H:i:s'),
                                            Carbon::parse($item->first()->created_at)->setTimeZone('PST')->format('H:i')
                                        );

                                }
                                
                                 elseif ($ctI == 8)
                                 {
                                    if(!empty($item[7]))
                                        $arr = array($item->first()->firstname." ". $item->first()->lastname,
                                            $item[0]->value,
                                            $item[1]->value,
                                            $item[2]->value,
                                            $item[3]->value,
                                            $item[4]->value,
                                            $item[5]->value,
                                            $item[6]->value,
                                            
                                            $item[7]->value,
                                            Carbon::parse($item->first()->created_at,"Asia/Manila")->format('M d,Y H:i:s'),
                                            Carbon::parse($item->first()->created_at)->setTimeZone('PST')->format('H:i')
                                        );
                                    else
                                        $arr = array($item->first()->firstname." ". $item->first()->lastname,
                                            $item[0]->value,
                                            $item[1]->value,
                                            $item[2]->value,
                                            $item[3]->value,
                                            $item[4]->value,
                                            $item[5]->value,
                                            
                                            '-','-',
                                            $item[6]->value,
                                            Carbon::parse($item->first()->created_at,"Asia/Manila")->format('M d,Y H:i:s'),
                                            Carbon::parse($item->first()->created_at)->setTimeZone('PST')->format('H:i')
                                        );

                                 }
                                 elseif ($ctI == 9)
                                 {
                                    if(!empty($item[8]))
                                        $arr = array($item->first()->firstname." ". $item->first()->lastname,
                                            $item[0]->value,
                                            $item[1]->value,
                                            $item[2]->value,
                                            $item[3]->value,
                                            $item[4]->value,
                                            $item[5]->value,
                                            $item[6]->value,
                                            $item[7]->value,
                                            $item[8]->value,
                                            Carbon::parse($item->first()->created_at,"Asia/Manila")->format('M d,Y H:i:s'),
                                            Carbon::parse($item->first()->created_at)->setTimeZone('PST')->format('H:i')
                                        );
                                    else
                                    $arr = array($item->first()->firstname." ". $item->first()->lastname,
                                            $item[0]->value,
                                            $item[1]->value,
                                            $item[2]->value,
                                            $item[3]->value,
                                            $item[4]->value,
                                            $item[5]->value,
                                            $item[6]->value,
                                            $item[7]->value,
                                            '-',
                                            Carbon::parse($item->first()->created_at,"Asia/Manila")->format('M d,Y H:i:s'),
                                            Carbon::parse($item->first()->created_at)->setTimeZone('PST')->format('H:i')
                                        );
                                 }
                                 else{

                                     $arr = array($item->first()->firstname." ". $item->first()->lastname,
                                            $item[0]->value,
                                            $item[1]->value,
                                            $item[2]->value,
                                            $item[3]->value,
                                            '-','-', '-','-',
                                            Carbon::parse($item->first()->created_at,"Asia/Manila")->format('M d,Y H:i:s'),
                                            Carbon::parse($item->first()->created_at)->setTimeZone('PST')->format('H:i')
                                        );

                                 
                                 }
                                 
                                 

                               
                                $sheet->appendRow($arr);

                            }


                        }break;
                        
                       
                    }

                    
                 });//end sheet1



          })->export('xls');

          return "Download";

    

    }

    public function fetchFrom($id)
    {
        DB::connection()->disableQueryLog();
        //switch (Input::get('by')) {
        $from = Input::get('from');
        $to = Input::get('to');


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
                if (!is_null($from) && !is_null($to)){
                    if ($from == $to){
                        $f = Carbon::parse($from,'Asia/Manila')->startOfDay();
                        $t = Carbon::parse($to,'Asia/Manila')->endOfDay();

                    }else{
                        $f = Carbon::parse($from,'Asia/Manila');
                        $t = Carbon::parse($to,'Asia/Manila');

                    }

                    

                    $actualSubmissions = DB::table('form_submissions_users')->where('form_submissions_users.formBuilder_id',$id)->where('form_submissions_users.created_at','>=',$f->format('Y-m-d H:i:s'))->where('form_submissions_users.created_at','<=',$t->format('Y-m-d H:i:s'))->
                        // 
                        leftJoin('users','form_submissions_users.user_id','=','users.id')->
                        // join('form_submissions','form_submissions.submission_user','=','form_submissions_users.id')->
                        // join('formBuilder_items','form_submissions.formBuilder_itemID','=','formBuilder_items.id')->
                        
                        
                     // where([
                     //         ['formBuilder_items.label',"Escalation"],
                        //     ['formBuilder_items.label','==',"Notes"],

                        // ])->
                        select('form_submissions_users.id as submissionID', 'form_submissions_users.user_id as user', 'users.firstname','users.lastname')->
                        orderBy('submissionID','DESC')->paginate(500);
                        
                        // chunk(100, function($items) use ($form)
                        //                 {
                        //                     foreach ($items as $item) {
                        //                         $form->push(["Agent"=>$item->lastname, "Submitted"=>$item->submissionID]);
                        //                     }
                            
                        //                 });
                        //$submissions = collect($form)->groupBy('submissionID')
                        $forms = $actualSubmissions->groupBy('submissionID');
                        
                        $rawData = new Collection;
                        foreach ($forms as $f) {
                            $d = DB::table('form_submissions_users')->where('form_submissions_users.id',$f->first()->submissionID)->
                                // leftJoin('users','form_submissions_users.user_id','=','users.id')->
                                join('form_submissions','form_submissions.submission_user','=','form_submissions_users.id')->
                                join('formBuilder_items','form_submissions.formBuilder_itemID','=','formBuilder_items.id')->
                                select('formBuilder_items.label as item','form_submissions.value','form_submissions_users.created_at')->get();

                                $agent = User::find($f->first()->user);
                                //$rawData->push(['lastname'=>$agent->lastname, 'firstname'=>$agent->firstname, 'submissions'=>collect($d)]);
                                $rawData->push(['id'=>$f->first()->submissionID, 'merchant'=>$d[1]->value,'orderStatus'=>$d[5]->value,'protocol'=>$d[3]->value,'agent'=>$agent->lastname.", ".$agent->firstname,'submitted'=>Carbon::parse($d[0]->created_at,"Asia/Manila")->format('M d,Y H:i:s'), 'hour'=> Carbon::parse($d[0]->created_at)->setTimeZone('PST')->format('H:i')]);
                               
                            
                            //$rawData->push(FormSubmissions::where('submission_user',$f->first()->submissionID)->get());

                        }
                        return response()->json(['data'=>$rawData]);
                    
            
                }else{

                    $form = DB::table('form_submissions_users')->where('form_submissions_users.formBuilder_id',$id)->
                    join('form_submissions','form_submissions.submission_user','=','form_submissions_users.id')->
                    join('formBuilder_items','form_submissions.formBuilder_itemID','=','formBuilder_items.id')->
                    leftJoin('users','form_submissions_users.user_id','=','users.id')->
                    select('form_submissions_users.id as submissionID','users.firstname','users.lastname','formBuilder_items.label','form_submissions.value','form_submissions_users.created_at')->get();
                    

                }
                $submissions = collect($form)->groupBy('submissionID');
                return $submissions;
                    $headers = collect($form)->groupBy('label');
                    $coll = new Collection;

                    
                    foreach($submissions as $item) {

                        $c = new Collection;
                        $k = $item->pluck('value');
                        
                        $coll->push(['id'=>$item->first()->submissionID, 'merchant'=>$k[1],'orderStatus'=>$k[5],'protocol'=>$k[3],'agent'=>$item->first()->lastname.", ".$item->first()->firstname,'submitted'=>Carbon::parse($item->first()->created_at,"Asia/Manila")->format('M d,Y H:i:s'), 'hour'=> Carbon::parse($item->first()->created_at)->setTimeZone('PST')->format('H:i')]);
                       
                        
                    }
                

                
            }break;

            case '2': 
            {
                if (!is_null($from) && !is_null($to)){
                    if ($from == $to){
                        $f = Carbon::parse($from,'Asia/Manila')->startOfDay();
                        $t = Carbon::parse($to,'Asia/Manila')->endOfDay();

                    }else{
                        $f = Carbon::parse($from,'Asia/Manila');
                        $t = Carbon::parse($to,'Asia/Manila');

                    }
                    $form = DB::table('form_submissions_users')->where('form_submissions_users.formBuilder_id',$id)->
                         where('form_submissions_users.created_at','>=',$f->format('Y-m-d H:i:s'))->where('form_submissions_users.created_at','<=',$t->format('Y-m-d H:i:s'))->
                        join('form_submissions','form_submissions.submission_user','=','form_submissions_users.id')->
                        join('formBuilder_items','form_submissions.formBuilder_itemID','=','formBuilder_items.id')->
                        leftJoin('users','form_submissions_users.user_id','=','users.id')->
                        select('form_submissions_users.id as submissionID','users.firstname','users.lastname','formBuilder_items.label','form_submissions.value','form_submissions_users.created_at')->get();
                }else
                {
                    $form = DB::table('form_submissions_users')->where('form_submissions_users.formBuilder_id',$id)->
                        join('form_submissions','form_submissions.submission_user','=','form_submissions_users.id')->
                        join('formBuilder_items','form_submissions.formBuilder_itemID','=','formBuilder_items.id')->
                        leftJoin('users','form_submissions_users.user_id','=','users.id')->
                        select('form_submissions_users.id as submissionID','users.firstname','users.lastname','formBuilder_items.label','form_submissions.value','form_submissions_users.created_at')->get();
                        
                }

                $submissions = collect($form)->groupBy('submissionID');
                        $headers = collect($form)->groupBy('label');
                        $coll = new Collection;

                        
                        foreach($submissions as $item) {

                            $c = new Collection;
                            $ctI = count($item);

                            //$k = $item->pluck('value');
                            
                            //$agent = $item->first()->firstname." ". $item->first()->lastname;
                            if ($ctI == 6)
                            $coll->push(['agent'=>$item->first()->lastname.", ".$item->first()->firstname,
                                        $item[0]->label=>$item[0]->value,
                                        $item[1]->label=>$item[1]->value,
                                        $item[2]->label=>$item[2]->value, 
                                        $item[3]->label=>$item[3]->value,
                                        $item[4]->label=>$item[4]->value,
                                        $item[5]->label=>$item[5]->value,
                                        'submitted'=>Carbon::parse($item->first()->created_at,"Asia/Manila")->format('M d,Y H:i:s'), 
                                        'hour'=> Carbon::parse($item->first()->created_at)->setTimeZone('PST')->format('H:i')]);
                            elseif ($ctI == 7)
                            $coll->push(['agent'=>$item->first()->lastname.", ".$item->first()->firstname,
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
                            $coll->push(['agent'=>$item->first()->lastname.", ".$item->first()->firstname,
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

                    

                
            }break;
            
            default: 
            {
                if (!is_null($from) && !is_null($to)){
                    if ($from == $to){
                        $f = Carbon::parse($from,'Asia/Manila')->startOfDay();
                        $t = Carbon::parse($to,'Asia/Manila')->endOfDay();

                    }else{
                        $f = Carbon::parse($from,'Asia/Manila');
                        $t = Carbon::parse($to,'Asia/Manila');

                    }

                     $form = DB::table('form_submissions_users')->where('form_submissions_users.formBuilder_id',$id)->
                      where('form_submissions_users.created_at','>=',$f->format('Y-m-d H:i:s'))->where('form_submissions_users.created_at','<=',$t->format('Y-m-d H:i:s'))->
                      join('form_submissions','form_submissions.submission_user','=','form_submissions_users.id')->
                      join('formBuilder_items','form_submissions.formBuilder_itemID','=','formBuilder_items.id')->
                      leftJoin('users','form_submissions_users.user_id','=','users.id')->
                      select('form_submissions_users.id as submissionID','users.firstname','users.lastname','formBuilder_items.label','form_submissions.value','form_submissions_users.created_at')->get();
                } else

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

    public function fetchRanking($type)
    {
        $from = Input::get('from');
        $to = Input::get('to');

        

        switch ($type) {

            case '1': 
            { 
                DB::connection()->disableQueryLog();
                $form = FormBuilder::find($type);
                $rankCategory = FormBuilder_Items::where('label','Order Status')->first(); 

                if (!is_null($from) && !is_null($to)){
                    if ($from == $to){
                        $f = Carbon::parse($from,'Asia/Manila')->startOfDay();
                        $t = Carbon::parse($to,'Asia/Manila')->endOfDay();

                    }else{
                        $f = Carbon::parse($from,'Asia/Manila');
                        $t = Carbon::parse($to,'Asia/Manila');

                    }

                    $rankings = DB::table('form_submissions_users')->where('formBuilder_id',$form->id)->
                        where('form_submissions_users.created_at','>=',$f->format('Y-m-d H:i:s'))->where('form_submissions_users.created_at','<=',$t->format('Y-m-d H:i:s'))->
                        join('form_submissions','form_submissions.submission_user','=','form_submissions_users.id')->
                        where('form_submissions.formBuilder_itemID','=',$rankCategory->id)->
                        leftJoin('users','users.id','=','form_submissions_users.user_id')->
                        select('users.firstname','users.nickname','users.lastname','users.id as userID','form_submissions.value','form_submissions_users.created_at')->get();
                }
                
                else
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
                                'submissions'=>$k,'claimed'=>count($t),'created'=>$t->first()->created_at]);
                   
                        
                }

                //$kol = $ranks->sortByDesc('claimed');
                return response()->json(['data'=>$ranks]);//->values()->all()] 

            } break;

            case '2': 
            { 
                DB::connection()->disableQueryLog();

                $form = FormBuilder::find($type); 
                $rankCategory = FormBuilder_Items::where('label','Confirmation')->where('formBuilder_id',$type)->first(); 
                if (!is_null($from) && !is_null($to)){
                    if ($from == $to){
                        $f = Carbon::parse($from,'Asia/Manila')->startOfDay();
                        $t = Carbon::parse($to,'Asia/Manila')->endOfDay();

                    }else{
                        $f = Carbon::parse($from,'Asia/Manila');
                        $t = Carbon::parse($to,'Asia/Manila');

                    }

                    $rankings = DB::table('form_submissions_users')->where('formBuilder_id',$form->id)->
                        where('form_submissions_users.created_at','>=',$f->format('Y-m-d H:i:s'))->where('form_submissions_users.created_at','<=',$t->format('Y-m-d H:i:s'))->
                        join('form_submissions','form_submissions.submission_user','=','form_submissions_users.id')->
                        where('form_submissions.formBuilder_itemID','=',$rankCategory->id)->
                        leftJoin('users','users.id','=','form_submissions_users.user_id')->
                        select('users.firstname','users.nickname','users.lastname','users.id as userID','form_submissions.value')->get();
                }else

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
                $form = FormBuilder::find($type);

                if (!is_null($from) && !is_null($to)){
                    if ($from == $to){
                        $f = Carbon::parse($from,'Asia/Manila')->startOfDay();
                        $t = Carbon::parse($to,'Asia/Manila')->endOfDay();

                    }else{
                        $f = Carbon::parse($from,'Asia/Manila');
                        $t = Carbon::parse($to,'Asia/Manila');

                    }

                    $rankings = DB::table('form_submissions_users')->where('formBuilder_id',$form->id)->
                        where('form_submissions_users.created_at','>=',$f->format('Y-m-d H:i:s'))->where('form_submissions_users.created_at','<=',$t->format('Y-m-d H:i:s'))->
                        join('form_submissions','form_submissions.submission_user','=','form_submissions_users.id')->
                        where('form_submissions.formBuilder_itemID','=',$rankCategory->id)->
                        leftJoin('users','users.id','=','form_submissions_users.user_id')->
                        select('users.firstname','users.nickname','users.lastname','users.id as userID','form_submissions.value','form_submissions_users.created_at')->get();
                }else
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
                        
                        $coll->push(['merchant'=>$k[1],'orderStatus'=>$k[5],'protocol'=>$k[3],'agent'=>$item->first()->lastname.", ".$item->first()->firstname,'submitted'=>Carbon::parse($item->first()->created_at,"Asia/Manila")->format('M d,Y H:i:s'), 'hour'=> Carbon::parse($item->first()->created_at)->setTimeZone('PST')->format('H:i')]);
                       
                        
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
                        $coll->push(['agent'=>$item->first()->lastname.", ".$item->first()->firstname,
                                    $item[0]->label=>$item[0]->value,
                                    $item[1]->label=>$item[1]->value,
                                    $item[2]->label=>$item[2]->value, 
                                    $item[3]->label=>$item[3]->value,
                                    $item[4]->label=>$item[4]->value,
                                    $item[5]->label=>$item[5]->value,
                                    'submitted'=>Carbon::parse($item->first()->created_at,"Asia/Manila")->format('M d,Y H:i:s'), 
                                    'hour'=> Carbon::parse($item->first()->created_at)->setTimeZone('PST')->format('H:i')]);
                        elseif ($ctI == 7)
                        $coll->push(['agent'=>$item->first()->lastname.", ".$item->first()->firstname,
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
                        $coll->push(['agent'=>$item->first()->lastname.", ".$item->first()->firstname,
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

    public function getOrderStatus($id)
    {
        DB::connection()->disableQueryLog();
        $from = Input::get('from');
        $to = Input::get('to');


        $orders = new Collection;

        $form = FormBuilder::find($id);
        $rankCategory = FormBuilder_Items::where('label','Order Status')->first(); 

        if (!is_null($from) && !is_null($to)){
            if ($from == $to){
                        $f = Carbon::parse($from,'Asia/Manila')->startOfDay();
                        $t = Carbon::parse($to,'Asia/Manila')->endOfDay();

                    }else{
                        $f = Carbon::parse($from,'Asia/Manila');
                        $t = Carbon::parse($to,'Asia/Manila');

                    }
            $rankings = DB::table('form_submissions_users')->where('formBuilder_id',$form->id)->
                where('form_submissions_users.created_at','>=',$f->format('Y-m-d H:i:s'))->where('form_submissions_users.created_at','<=',$t->format('Y-m-d H:i:s'))->
                join('form_submissions','form_submissions.submission_user','=','form_submissions_users.id')->
                where('form_submissions.formBuilder_itemID','=',$rankCategory->id)->
                leftJoin('users','users.id','=','form_submissions_users.user_id')->
                select('users.firstname','users.nickname','users.lastname','users.id as userID','form_submissions.value','form_submissions_users.created_at')->get();
        }
        
        else
            $rankings = DB::table('form_submissions_users')->where('formBuilder_id',$form->id)->
                join('form_submissions','form_submissions.submission_user','=','form_submissions_users.id')->
                where('form_submissions.formBuilder_itemID','=',$rankCategory->id)->
                leftJoin('users','users.id','=','form_submissions_users.user_id')->
                select('users.firstname','users.nickname','users.lastname','users.id as userID','form_submissions.value')->get();


        $data = collect($rankings)->groupBy('value');

        foreach ($data as $d) {
           $orders->push(["label"=>$d->first()->value, "count"=>count($d)]);
        }
        
        return response()->json(['data'=>$orders,'total'=>count($rankings),'from'=>$from,'to'=>$to]);
       


       
    }

    

    public function getEscalations($id)
    {
        DB::connection()->disableQueryLog();
        //switch (Input::get('by')) {
        $from = Input::get('from');
        $to = Input::get('to');
        $data = new Collection;


        switch($id){ 

            case '1': 
            {
                if (!is_null($from) && !is_null($to)){
                    if ($from == $to){
                        $f = Carbon::parse($from,'Asia/Manila')->startOfDay();
                        $t = Carbon::parse($to,'Asia/Manila')->endOfDay();

                    }else{
                        $f = Carbon::parse($from,'Asia/Manila');
                        $t = Carbon::parse($to,'Asia/Manila');

                    }
                    $form = DB::table('form_submissions_users')->where('form_submissions_users.formBuilder_id',$id)->
                    where('form_submissions_users.created_at','>=',$f->format('Y-m-d H:i:s'))->where('form_submissions_users.created_at','<=',$t->format('Y-m-d H:i:s'))->
                    join('form_submissions','form_submissions.submission_user','=','form_submissions_users.id')->
                    join('formBuilder_items','form_submissions.formBuilder_itemID','=','formBuilder_items.id')->

                    leftJoin('users','form_submissions_users.user_id','=','users.id')->
                    select('form_submissions_users.id as submissionID','users.firstname','users.lastname','formBuilder_items.label','form_submissions.value','form_submissions_users.created_at')->where('formBuilder_items.id',6)->get();
                    
            
                }else{

                    $form = DB::table('form_submissions_users')->where('form_submissions_users.formBuilder_id',$id)->
                    join('form_submissions','form_submissions.submission_user','=','form_submissions_users.id')->
                    join('formBuilder_items','form_submissions.formBuilder_itemID','=','formBuilder_items.id')->
                    leftJoin('users','form_submissions_users.user_id','=','users.id')->
                    select('form_submissions_users.id as submissionID','users.firstname','users.lastname','formBuilder_items.label','form_submissions.value','form_submissions_users.created_at')->get();
                    

                }

                
                $submissions = collect($form)->groupBy('value');
                foreach($submissions as $item) {

                    $data->push(["label"=>$item->first()->value, "count"=>count($item)]);
                }

                return $data;
                

                
            }break;

            case '2': 
            {
                if (!is_null($from) && !is_null($to)){
                    if ($from == $to){
                        $f = Carbon::parse($from,'Asia/Manila')->startOfDay();
                        $t = Carbon::parse($to,'Asia/Manila')->endOfDay();

                    }else{
                        $f = Carbon::parse($from,'Asia/Manila');
                        $t = Carbon::parse($to,'Asia/Manila');

                    }
                    $form = DB::table('form_submissions_users')->where('form_submissions_users.formBuilder_id',$id)->
                         where('form_submissions_users.created_at','>=',$f->format('Y-m-d H:i:s'))->where('form_submissions_users.created_at','<=',$t->format('Y-m-d H:i:s'))->
                        join('form_submissions','form_submissions.submission_user','=','form_submissions_users.id')->
                        join('formBuilder_items','form_submissions.formBuilder_itemID','=','formBuilder_items.id')->
                        leftJoin('users','form_submissions_users.user_id','=','users.id')->
                        select('form_submissions_users.id as submissionID','users.firstname','users.lastname','formBuilder_items.label','form_submissions.value','form_submissions_users.created_at')->where('formBuilder_items.id',18)->get();
                }else
                {
                    $form = DB::table('form_submissions_users')->where('form_submissions_users.formBuilder_id',$id)->
                        join('form_submissions','form_submissions.submission_user','=','form_submissions_users.id')->
                        join('formBuilder_items','form_submissions.formBuilder_itemID','=','formBuilder_items.id')->
                        leftJoin('users','form_submissions_users.user_id','=','users.id')->
                        select('form_submissions_users.id as submissionID','users.firstname','users.lastname','formBuilder_items.label','form_submissions.value','form_submissions_users.created_at')->get();
                        
                }

                $submissions = collect($form)->groupBy('value');
                foreach($submissions as $item) {

                    $data->push(["label"=>$item->first()->value, "count"=>count($item)]);
                }

                return $data;
                
                        

                
            }break;
            
            
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

        $correct = Carbon::now('GMT+8');

        if ($formItems->contains("- select one -") || $formItems->contains("") )
            return response()->json(['status'=>0,'error'=>"Fill out all required fields before submitting."]);
        else {

            /* get from first item muna */
            $s = explode('_', $keys[0]);


            $formItem = FormBuilder_Items::find($s[1]);

            $userSubmission = new FormSubmissionsUser;
            $userSubmission->user_id = $user->id;
            $userSubmission->formBuilder_id = $formItem->formBuilder_id;
            $userSubmission->created_at = $correct->format('Y-m-d H:i:s');
            $userSubmission->updated_at = $correct->format('Y-m-d H:i:s');
            $userSubmission->save();

            $ctr=0;
            foreach($keys as $k){
                $s = explode('_', $k);
                $coll->push(['s'=>$s]);
                
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
                        $submission->created_at = $correct->format('Y-m-d H:i:s');
                        $submission->updated_at = $correct->format('Y-m-d H:i:s');
                        $submission->save();
                        $coll->push(['submittedx'=>$submission, 's1'=>$s[1]]);

                    }else  $coll->push(['submittedy'=>$formItems[$k], 'lookfor'=>$name] );
                } else if($s[1] == 'from'){ //do nothing

                } else if ($formItems[$k] == 'xx') { //add new "formBuilderElem_values" || from Guideline submisison

                    $e = new FormBuilderElem_Values;
                    $e->label = $request->newPayroll;
                    $e->value = str_replace(" ", "_", $request->newPayroll);
                    $e->selected = '0';
                    $e->formBuilder_itemID = $s[1];
                    $e->save();

                    $submission = new FormSubmissions;
                    $submission->submission_user = $userSubmission->id;
                    $submission->formBuilder_itemID = $s[1];
                    $submission->value = $e->value;
                    $submission->created_at = $correct->format('Y-m-d H:i:s');
                    $submission->updated_at = $correct->format('Y-m-d H:i:s');
                    $submission->save();
                    $coll->push(['submitted'=>$submission, 's1'=>$s[1],'e'=>$e ]);



                }
                else 
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
                        $submission->created_at = $correct->format('Y-m-d H:i:s');
                        $submission->updated_at = $correct->format('Y-m-d H:i:s');
                        $submission->save();
                        $coll->push(['submitted'=>$submission, 's1'=>$s[1] ]);
                    } 

                $ctr++;
            }

            // $correct = Carbon::now('GMT+8'); //->timezoneName();
            // if($this->user->id !== 564 ) {
            //   $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
            //     fwrite($file, "-------------------\n Submitted ReportForm id[".$userSubmission->id."] --" . $correct->format('M d h:i A'). " by [". $this->user->id."] ".$this->user->lastname."\n");
            //     fclose($file);
            // }

            return $coll;
            //return response()->json(['status'=>"ok", 'items'=>$coll,'formid'=>$formItem->formBuilder_id,'usersubmit'=>$userSubmission->id]);

        }
                

        
        


    }

   

    public function rawData($id)
     {
        DB::connection()->disableQueryLog();
        $correct = Carbon::now('GMT+8');
        $form = FormBuilder::find($id);
        $canAdminister = ( count(UserType::find($this->user->userType_id)->roles->where('label','QUERY_REPORTS'))>0 ) ? true : false;

        $data = new Collection;
        $data2 = new Collection;
        $campaign = Campaign::where('name',"Postmates")->first();
        $camp = $campaign->logo;
        $logo = "../public/img/".$camp->filename;


        $from = Input::get('from');
        $to = Input::get('to');
        $download = Input::get('dl');

        $rawData = new Collection;



        switch($id){ 

            

            case '1': 
            {
                if (!is_null($from) && !is_null($to)){

                    if ($from == $to){
                        $f = Carbon::parse($from,'Asia/Manila')->startOfDay();
                        $t = Carbon::parse($to,'Asia/Manila')->endOfDay();

                    }else{
                        $f = Carbon::parse($from,'Asia/Manila');
                        $t = Carbon::parse($to,'Asia/Manila');

                    }

                   
                    
                    $start = Carbon::parse($from,'Asia/Manila')->format('Y-m-d');$end =  Carbon::parse($to,'Asia/Manila')->format('Y-m-d');

                    

                    $actualSubmissions = DB::table('form_submissions_users')->where('form_submissions_users.formBuilder_id',$id)->where('form_submissions_users.created_at','>=',$f->format('Y-m-d H:i:s'))->where('form_submissions_users.created_at','<=',$t->format('Y-m-d H:i:s'))->
                        // 
                        leftJoin('users','form_submissions_users.user_id','=','users.id')->
                        
                        select('form_submissions_users.id as submissionID', 'form_submissions_users.user_id as user', 'users.firstname','users.lastname')->
                        orderBy('submissionID','DESC')->paginate(500);


                        $forms = $actualSubmissions->groupBy('submissionID');
                        //return $actualSubmissions;
                         
                        //return $rawData;
                       
                        foreach ($forms as $f) {
                            //$rawData->push(['from'=>$f, 'to'=>$t,'f'=>$f]);
                            $d = DB::table('form_submissions_users')->where('form_submissions_users.id',$f->first()->submissionID)->
                                
                                join('form_submissions','form_submissions.submission_user','=','form_submissions_users.id')->
                                join('formBuilder_items','form_submissions.formBuilder_itemID','=','formBuilder_items.id')->
                                select('formBuilder_items.label as item','form_submissions.value','form_submissions_users.created_at')->get();

                                $agent = User::find($f->first()->user);
                                //$rawData->push($d);

                               
                                 if (count($d)>=8)
                                
                                $rawData->push(['id'=>$f->first()->submissionID,'customer'=>$d[0]->value, 'merchant'=>$d[1]->value,'phone'=>$d[2]->value,'location'=>$d[3]->value, 'orderStatus'=>$d[6]->value,'protocol'=>$d[4]->value,'agent'=>$agent->lastname.", ".$agent->firstname,'submitted'=>Carbon::parse($d[0]->created_at,"Asia/Manila")->format('M d,Y H:i:s'), 'hour'=> Carbon::parse($d[0]->created_at)->setTimeZone('PST')->format('H:i'),'notes'=>$d[7]->value,'escalation'=>$d[5]->value ]);
                                else if (empty($d))
                                    $rawData->push(['id'=>$f->first()->submissionID,'customer'=>null, 'merchant'=>null,'phone'=>null,'location'=>null, 'orderStatus'=>null,'protocol'=>null,'agent'=>$agent->lastname.", ".$agent->firstname,'submitted'=>null, 'hour'=> null,'notes'=>null ,'escalation'=>null]);
                                else
                                    $rawData->push(['id'=>$f->first()->submissionID,'customer'=>$d[0]->value, 'merchant'=>$d[1]->value,'phone'=>$d[2]->value,'location'=>$d[3]->value, 'orderStatus'=>$d[5]->value,'protocol'=>$d[4]->value,'agent'=>$agent->lastname.", ".$agent->firstname,'submitted'=>Carbon::parse($d[0]->created_at,"Asia/Manila")->format('M d,Y H:i:s'), 'hour'=> Carbon::parse($d[0]->created_at)->setTimeZone('PST')->format('H:i'),'notes'=>$d[6]->value,'escalation'=>$d[5]->value]);

                               

                        }

                        $coll = new Collection;

                        if($download==1)
                        {
                            $sheetTitle = FormBuilder::find($id)->title;
                            $submissions = $rawData;
                            $chenes = new Collection;
                            //return $submissions;
                            $headers = array('Agent','Customer Name ', 'Merchant Name','Merchant Phone Number','Merchant Location',  'Escalation/level 1','Escalation/level 2', 'Order Status','Notes','Date Submitted','PST hour');
                            $description = "agent submission for Postmates' ". $sheetTitle;

                            if($this->user->id !== 564 ) {
                              $user = User::find(Input::get('id'));
                             
                              
                              $file = fopen('public/build/postmates.txt', 'a') or die("Unable to open logs");
                                fwrite($file, "-------------------\n Downloaded CSV [".$id."]-pp[".$actualSubmissions->currentPage()."] [".$start." - ".$end."] " . $correct->format('M d h:i A'). " by [". $this->user->id."] ".$this->user->lastname."\n");
                                fclose($file);
                            } 

                            //$coll->push(['headers'=>$headers, 'submissions'=>$submissions]);return $coll;
                           
                            Excel::create($sheetTitle,function($excel) use($id,$submissions, $sheetTitle, $headers,$description,$chenes) 
                               {
                                      $excel->setTitle($sheetTitle.' Summary Report');

                                      // Chain the setters
                                      $excel->setCreator('Programming Team')
                                            ->setCompany('OpenAccess');

                                      // Call them separately
                                      $excel->setDescription($description);
                                      $excel->sheet("Sheet 1", function($sheet) use ($id,$submissions, $headers,$chenes)
                                      {
                                        $sheet->appendRow($headers);
                                        foreach($submissions as $item)
                                        {
                                            $arr = array($item['agent'],
                                                         $item['customer'],
                                                         $item['merchant'],
                                                         $item['location'],
                                                         $item['phone'],
                                                         
                                                         $item['protocol'],
                                                         $item['escalation'],
                                                         $item['orderStatus'],
                                                         $item['notes'],
                                                         $item['submitted'],
                                                         $item['hour']
                                                    );
                                            $sheet->appendRow($arr);
                                           

                                        }

                                     

                                        
                                     });//end sheet1

                                    



                              })->export('xls');

                            
                             
                              return "Download";

                        }//end download
                        else{

                            if($this->user->id !== 564 ) {
                              $user = User::find(Input::get('id'));
                             
                              
                              $file = fopen('public/build/postmates.txt', 'a') or die("Unable to open logs");
                                fwrite($file, "-------------------\n Viewed Raw data [".$id."]: " . $correct->format('M d h:i A'). " by [". $this->user->id."] ".$this->user->lastname."\n");
                                fclose($file);
                            } 
                            return view('forms.formSubmissions-rawData',compact('form','actualSubmissions','rawData', 'logo','canAdminister','start','end'));
                        }

                    
            
                }else{

                    return view('empty-page',['message'=>"<br/><br/><br/><br/><p class='text-center'>Sorry, there's something wrong with your request. <br/>Please check parameters and try again</p>.",'title'=>"No Raw Data Found"]);

                }
                
                

                
            }break;

            case '2': 
            {
                if (!is_null($from) && !is_null($to)){
                    $f = Carbon::parse($from,'Asia/Manila');
                    $t = Carbon::parse($to,'Asia/Manila');
                    $form = DB::table('form_submissions_users')->where('form_submissions_users.formBuilder_id',$id)->
                         where('form_submissions_users.created_at','>=',$f->format('Y-m-d H:i:s'))->where('form_submissions_users.created_at','<=',$t->format('Y-m-d H:i:s'))->
                        join('form_submissions','form_submissions.submission_user','=','form_submissions_users.id')->
                        join('formBuilder_items','form_submissions.formBuilder_itemID','=','formBuilder_items.id')->
                        leftJoin('users','form_submissions_users.user_id','=','users.id')->
                        select('form_submissions_users.id as submissionID','users.firstname','users.lastname','formBuilder_items.label','form_submissions.value','form_submissions_users.created_at')->get();
                }else
                {
                    $form = DB::table('form_submissions_users')->where('form_submissions_users.formBuilder_id',$id)->
                        join('form_submissions','form_submissions.submission_user','=','form_submissions_users.id')->
                        join('formBuilder_items','form_submissions.formBuilder_itemID','=','formBuilder_items.id')->
                        leftJoin('users','form_submissions_users.user_id','=','users.id')->
                        select('form_submissions_users.id as submissionID','users.firstname','users.lastname','formBuilder_items.label','form_submissions.value','form_submissions_users.created_at')->get();
                        
                }

                $submissions = collect($form)->groupBy('submissionID');
                        $headers = collect($form)->groupBy('label');
                        $coll = new Collection;

                        
                        foreach($submissions as $item) {

                            $c = new Collection;
                            $ctI = count($item);

                            //$k = $item->pluck('value');
                            
                            //$agent = $item->first()->firstname." ". $item->first()->lastname;
                            if ($ctI == 6)
                            $coll->push(['agent'=>$item->first()->lastname.", ".$item->first()->firstname,
                                        $item[0]->label=>$item[0]->value,
                                        $item[1]->label=>$item[1]->value,
                                        $item[2]->label=>$item[2]->value, 
                                        $item[3]->label=>$item[3]->value,
                                        $item[4]->label=>$item[4]->value,
                                        $item[5]->label=>$item[5]->value,
                                        'submitted'=>Carbon::parse($item->first()->created_at,"Asia/Manila")->format('M d,Y H:i:s'), 
                                        'hour'=> Carbon::parse($item->first()->created_at)->setTimeZone('PST')->format('H:i')]);
                            elseif ($ctI == 7)
                            $coll->push(['agent'=>$item->first()->lastname.", ".$item->first()->firstname,
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
                            $coll->push(['agent'=>$item->first()->lastname.", ".$item->first()->firstname,
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

                            elseif ($ctI == 9)
                            $coll->push(['agent'=>$item->first()->lastname.", ".$item->first()->firstname,
                                        $item[0]->label=>$item[0]->value,
                                        $item[1]->label=>$item[1]->value,
                                        $item[2]->label=>$item[2]->value, 
                                        $item[3]->label=>$item[3]->value,
                                        $item[4]->label=>$item[4]->value,
                                        $item[5]->label=>$item[5]->value,
                                        $item[6]->label=>$item[6]->value,
                                        $item[7]->label=>$item[7]->value,
                                        $item[8]->label=>$item[8]->value,
                                        'submitted'=>Carbon::parse($item->first()->created_at,"Asia/Manila")->format('M d,Y H:i:s'), 
                                        'hour'=> Carbon::parse($item->first()->created_at)->setTimeZone('PST')->format('H:i')]); 
                                        
                           
                            
                        }

                    

                
            }break;

            case '3':
            {
                if (!is_null($from) && !is_null($to)){

                    if ($from == $to){
                        $f = Carbon::parse($from,'Asia/Manila')->startOfDay();
                        $t = Carbon::parse($to,'Asia/Manila')->endOfDay();

                    }else{
                        $f = Carbon::parse($from,'Asia/Manila');
                        $t = Carbon::parse($to,'Asia/Manila');

                    }
                }
                    //$start = Carbon::parse($from,'Asia/Manila')->format('Y-m-d');$end =  Carbon::parse($to,'Asia/Manila')->format('Y-m-d');
                    $form = DB::table('form_submissions_users')->where('form_submissions_users.formBuilder_id',$id)->
                    //return (['form'=>$form, 'id'=>$id,'f'=>$f->format('Y-m-d H:i:s'), 't'=>$t->format('Y-m-d H:i:s') ]);
                    
                      where('form_submissions_users.created_at','>=',$f->format('Y-m-d H:i:s'))->where('form_submissions_users.created_at','<=',$t->format('Y-m-d H:i:s'))->
                      join('form_submissions','form_submissions.submission_user','=','form_submissions_users.id')->
                      join('formBuilder_items','form_submissions.formBuilder_itemID','=','formBuilder_items.id')->
                      leftJoin('users','form_submissions_users.user_id','=','users.id')->
                      select('form_submissions_users.id as submissionID','users.firstname','users.lastname','formBuilder_items.label','form_submissions.value','form_submissions_users.created_at')->get();
                      //return (['the form'=>$form, 'id'=>$id,'f'=>$f->format('Y-m-d H:i:s'), 't'=>$t->format('Y-m-d H:i:s') ]);

                      /*$submissions = collect($form)->groupBy('submissionID');
                        $headers = collect($form)->groupBy('label');
                        $coll = new Collection;
                        $coll->push(['headers'=>$headers, 'submissions'=>$submissions]);*/


                    $submissions = collect($form)->groupBy('submissionID');
                    $headers1 = collect($form)->groupBy('label')->keys();
                    $headers = array("Date", "Agent Name");

                    foreach ($headers1 as $key => $value) {
                        array_push($headers, $value);
                    }
                    //array_push($headers, "Status");
                    //array_unshift($headers, "Agent Name");
                    $coll = new Collection;


                        
                        
                    if($download==1)
                        {
                            $sheetTitle = FormBuilder::find($id)->title;
                            $description = "agent submission for Guideline' ". $sheetTitle;

                            if($this->user->id !== 564 ) {
                              $user = User::find(Input::get('id'));
                             
                              
                              $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
                                fwrite($file, "-------------------\n Downloaded CSV [".$id."][".$f->format('Y-m-d')." - ".$t->format('Y-m-d')."] " . $correct->format('M d h:i A'). " by [". $this->user->id."] ".$this->user->lastname."\n");
                                fclose($file);
                            } 

                    //$coll->push(['headers'=>$headers, 'submissions'=>$submissions]);return $coll;

                            
                           
                            Excel::create($sheetTitle,function($excel) use($id,$submissions, $sheetTitle, $headers,$description) 
                               {
                                      $excel->setTitle($sheetTitle.' Summary Report');

                                      // Chain the setters
                                      $excel->setCreator('Programming Team')
                                            ->setCompany('OpenAccess');

                                      // Call them separately
                                      $excel->setDescription($description);
                                      $excel->sheet("Sheet 1", function($sheet) use ($id,$submissions, $headers)
                                      {
                                        $sheet->appendRow($headers);
                                        foreach($submissions as $item)
                                        {
                                            $arr = array($item[0]->created_at, 
                                                         $item[0]->lastname.", ".$item[0]->firstname,
                                                         $item[0]->value, //ID
                                                         $item[1]->value, //plan number
                                                         $item[2]->value, //sponsor name
                                                         $item[3]->value, //user
                                                         
                                                         $item[4]->value, //payroll provider
                                                         $item[5]->value, //action
                                                         
                                                    
                                                         
                                                    );
                                            $sheet->appendRow($arr);
                                           

                                        }

                                     

                                        
                                     });//end sheet1

                                    



                              })->export('xls');

                            
                             
                              return "Download";

                        }//end download

                    

                
            


            }break;
            
            default: 
            {
                if (!is_null($from) && !is_null($to)){
                    $f = Carbon::parse($from,'Asia/Manila');
                    $t = Carbon::parse($to,'Asia/Manila');

                     $form = DB::table('form_submissions_users')->where('form_submissions_users.formBuilder_id',$id)->
                      where('form_submissions_users.created_at','>=',$f->format('Y-m-d H:i:s'))->where('form_submissions_users.created_at','<=',$t->format('Y-m-d H:i:s'))->
                      join('form_submissions','form_submissions.submission_user','=','form_submissions_users.id')->
                      join('formBuilder_items','form_submissions.formBuilder_itemID','=','formBuilder_items.id')->
                      leftJoin('users','form_submissions_users.user_id','=','users.id')->
                      select('form_submissions_users.id as submissionID','users.firstname','users.lastname','formBuilder_items.label','form_submissions.value','form_submissions_users.created_at')->get();
                } else

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


        $canAdminister = ( count(UserType::find($this->user->userType_id)->roles->where('label','QUERY_REPORTS'))>0 ) ? true : false;

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
                        return view('forms.formSubmissions-show2',compact('form','data','data2','total','logo','canAdminister'));
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

                       

                    }break;
        }
        //return $data2;
        //return $topAgents;
        //return response()->json(['canAdminister'=>$canAdminister]);
        


        return view('forms.formSubmissions-show',compact('form','logo','canAdminister'));
    }


    public function uploadCSV(Request $request)
     {
        $today = date('Y-m-d');
        
        $bioFile = $request->file('biometricsData');
        if (!empty($bioFile))
        {
              //$destinationPath = 'uploads'; // upload path
              $destinationPath = storage_path() . '/uploads/';
              $extension = Input::file('biometricsData')->getClientOriginalExtension(); // getting image extension
              $fileName = $today.'-postmates.'.$extension; // renameing image
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
                // 14 = Order Placing

                // #   | Agent Email | Customer Name |  Merchant Name | Merchant Phone Number | Escalation/level 1  
                //     Escalation/level 2 |  Order Status | Notes | Date Created | IP Address | Status | Referer | URL | Comments

                // #   Agent Email | Merchant Name | Merchant Phone Number | Confirmation/level 1 | Confirmation/level 2    
                //     Confirmation/level 3 | Confirmation/level 4 |  Notes | Date Created | IP Address | Status | Referer | URL | Comments

                while (($result = fgetcsv($file)) !== false)
                    {
                        //$coll->push(['result'=>$result]);
                        if ($flag) $flag=false;
                        else{

                            $employee = User::where('email',$result[1])->get();
                            if (count($employee) > 0){
                                $created_at = Carbon::parse($result[9],'Asia/Manila')->format('Y-m-d H:i:s');
                                $user = $employee->first();
                                $formSubmit = new FormSubmissionsUser;
                                $formSubmit->formBuilder_id = '1';
                                $formSubmit->user_id = $user->id;
                                $formSubmit->created_at = $created_at;
                                $formSubmit->save();

                                //we now save form items and values submitted
                                $this->saveFormSubmission($headers[2],$result[2],$created_at,$formSubmit); /* Customer/Merchant name*/
                                $this->saveFormSubmission($headers[3],$result[3],$created_at,$formSubmit); /* Merchant Name/Phone/*/
                                $this->saveFormSubmission($headers[4],$result[4],$created_at,$formSubmit); /* Merchant Phone/Conf 1/*/
                                
                            }

                            

                        }//end else
                        

                        
                        $ctr++;
                        
                    }//end while

                fclose($file);fclose($file2);

               return response()->json($coll);


                   // return redirect()->action('TempUploadController@index');

              
        }
        else return response()->json(['success'=>false]);
        

    }

    public function updateStatus(Request $request)
    {
        $correct = Carbon::now('GMT+8');
        $submission = FormSubmissionsUser::find($request->id);
        $update = new FormSubmissionsReviewer;

        $update->user_id = $this->user->id;
        $update->submission_id = $submission->id;
        $update->oldStatus = $request->oldStatus;
        $update->newStatus = $request->newStatus;
        $update->created_at = $correct->format('Y-m-d H:i:s');
        $update->updated_at = $correct->format('Y-m-d H:i:s');

        $update->save();

        //we now update the status itself on the form submission
        $action = FormBuilder_Items::where('formBuilder_id',$submission->formBuilder_id)->where('label',"Action")->get();
        $toUpdate = FormSubmissions::where('submission_user',$submission->id)->where('formBuilder_itemID',$action->first()->id)->first();
        $toUpdate->value = $request->newStatus;
        $toUpdate->push();
        return back();
    }


    public function widgets()
    {
        $prg = Input::get('program');
        $tab = Input::get('tab');

        $program = Campaign::find($prg);
        $form = FormBuilder::find(Input::get('form'));

        if (empty($form) || empty($program)) return view('empty');

        $l = DB::table('campaign_logos')->where('campaign_id',$program->id)->get();
        (count($l) > 0) ? $logo = $l : $logo=null;$correct = Carbon::now('GMT+8');

        
        $submissions = DB::table('form_submissions_users')->where('form_submissions_users.formBuilder_id',$form->id)->
                //where('form_submissions_users.created_at','>=',$f->format('Y-m-d H:i:s'))->where('form_submissions_users.created_at','<=',$t->format('Y-m-d H:i:s'))->
                join('form_submissions','form_submissions.submission_user','=','form_submissions_users.id')->
                join('formBuilder_items','form_submissions.formBuilder_itemID','=','formBuilder_items.id')->
                //where('form_submissions.formBuilder_itemID','=',$rankCategory->id)->
                leftJoin('users','users.id','=','form_submissions_users.user_id')->
                select('form_submissions_users.id as submissionID', 'users.firstname','users.nickname','users.lastname','users.id as userID','formBuilder_items.label', 'form_submissions.value','form_submissions_users.created_at')->get();

        $start = $correct;
        $end = $correct;
        $actualSubmissions = DB::table('form_submissions_users')->where('form_submissions_users.formBuilder_id',$form->id)->
                //where('form_submissions_users.created_at','>=',$f->format('Y-m-d H:i:s'))->where('form_submissions_users.created_at','<=',$t->format('Y-m-d H:i:s'))->
                join('form_submissions','form_submissions.submission_user','=','form_submissions_users.id')->
                join('formBuilder_items','form_submissions.formBuilder_itemID','=','formBuilder_items.id')->
                //where('form_submissions.formBuilder_itemID','=',$rankCategory->id)->
                leftJoin('users','users.id','=','form_submissions_users.user_id')->
                select('form_submissions_users.id as submissionID', 'users.firstname','users.nickname','users.lastname','users.id as userID','formBuilder_items.label', 'form_submissions.value','form_submissions_users.created_at')->paginate(500);

                //orderBy('form_submissions_users.created_at','DESC')->get();

        /*$actualSubmissions = DB::table('form_submissions_users')->where('form_submissions_users.formBuilder_id',$id)->
                            where('form_submissions_users.created_at','>=',$f->format('Y-m-d H:i:s'))->
                            where('form_submissions_users.created_at','<=',$t->format('Y-m-d H:i:s'))->
                        // 
                        leftJoin('users','form_submissions_users.user_id','=','users.id')->
                        
                        select('form_submissions_users.id as submissionID', 'form_submissions_users.user_id as user', 'users.firstname','users.lastname')->
                        orderBy('submissionID','DESC')->paginate(500);*/


        $groupedSubmissions = collect($submissions)->groupBy('submissionID');
        //return $groupedSubmissions;

        $reviewers = DB::table('formBuilder')->where('formBuilder.id', $form->id)->
                    join('form_submissions_users','form_submissions_users.formBuilder_id','=','formBuilder.id')->
                    join('form_submissions_reviewer','form_submissions_reviewer.submission_id','=','form_submissions_users.id')->
                    join('users','form_submissions_reviewer.user_id','=','users.id')->
                    select('users.id as userID','users.firstname as reviewerFname','users.lastname as reviewerLname','form_submissions_users.id as submissionID','form_submissions_reviewer.newStatus', 'form_submissions_reviewer.created_at')->get(); //return $reviewers;

        
        if($this->user->id !== 564 ) {
             
            $file = fopen('public/build/changes.txt', 'a') or die("Unable to open logs");
              fwrite($file, "-------------------\n Widget [".$form->title."]  by [". $this->user->id."] ".$this->user->lastname." on ". $correct->format('M d h:i A').  "\n");
              fclose($file);
          } 


        //return $groupedSubmissions;
        return view('forms.widgets-guideline',compact('program','logo','tab','groupedSubmissions','form','reviewers','actualSubmissions','start','end'));
    }
}
