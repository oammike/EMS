<?php

namespace OAMPI_Eval\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use OAMPI_Eval\Http\Requests;
use OAMPI_Eval\User;
use OAMPI_Eval\ImmediateHead;
use OAMPI_Eval\Campaign;
use OAMPI_Eval\Status;
use OAMPI_Eval\UserType;
use OAMPI_Eval\Position;
use OAMPI_Eval\ImmediateHead_Campaign;


use Illuminate\Support\Facades\Input;

class ImmediateHeadCampaignController extends Controller
{
    protected $user;
    protected $immediateHeadCampaign;

    public function __construct(ImmediateHead_Campaign $immediateHeadCampaign)
    {
        $this->middleware('auth');
        $this->user =  User::find(Auth::user()->id);
        $this->immediateHeadCampaign = $immediateHeadCampaign;
    }

    public function store(Request $request)
    {
        

        $leaderCampaign = new ImmediateHead_Campaign;
        $leaderCampaign->immediateHead_id = $request->employeeNumber;
        $leaderCampaign->campaign_id = $request->campaign_id;
        $leaderCampaign->save();

        

        return response()->json($leaderCampaign);

    }

    public function disable($id)
    {
        $ih = ImmediateHead_Campaign::find($id);
        $ih->disabled = true;
        $ih->push();
        
       return back();

    }
}
