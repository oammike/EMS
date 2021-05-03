<?php

namespace OAMPI_Eval\Http\Controllers;

use Illuminate\Http\Request;

use Carbon\Carbon;
use \DB;
use \Hash;
use Excel;
use \Mail;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use OAMPI_Eval\Http\Requests;
use OAMPI_Eval\User;
use OAMPI_Eval\Campaign;
use OAMPI_Eval\UserType;
use OAMPI_Eval\Team;
use OAMPI_Eval\ImmediateHead;
use OAMPI_Eval\Position;
use OAMPI_Eval\Status;
use OAMPI_Eval\ImmediateHead_Campaign;

class AnnouncementController extends Controller
{
    //
}
