<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class User_OT extends Model
{
    protected $table= 'user_ot';

    protected $fillable = [
        'user_id','biometrics_id', 'billable_hours','filed_hours','timeStart','timeEnd', 'reason', 'isRD', 'isApproved','approver'

    ];

   
}
