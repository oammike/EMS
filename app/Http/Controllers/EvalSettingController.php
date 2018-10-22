<?php

namespace OAMPI_Eval\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use OAMPI_Eval\Http\Requests;
use OAMPI_Eval\User;
use OAMPI_Eval\ImmediateHead;
use OAMPI_Eval\EvalType;
use OAMPI_Eval\RatingScale;
use OAMPI_Eval\EvalSetting;
use OAMPI_Eval\EvalForm;


class EvalSettingController extends Controller
{
    protected $user;

     public function __construct()
    {
        $this->middleware('auth');
        $this->user = User::find(Auth::user()->id);
    }

    
    public function index()
    {
        $forms = EvalSetting::all();


        return view('evaluation.forms-index', compact('forms'));
    }

    public function create()
    {

    }
    public function show($id)
    {

    }
    public function edit($id)
    {

    }

    public function destroy($id)
    {

    }

    public function update($id)
    {

    }

}
