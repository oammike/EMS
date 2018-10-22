<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class User_DTR extends Model
{
    protected $table = "user_dtr";
    protected $fillable = [
        'productionDate','workshift', 'timeIN','timeOUT','hoursWorked','OT_billable','OT_approved','UT', 'user_id'
        //'immediateHead_id','campaign_id','immediateHead_Campaigns_id',
    ];
}
