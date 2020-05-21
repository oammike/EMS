<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class User_VTO extends Model
{
    protected $table = 'user_vto';
    protected $fillable = [
        'productionDate','user_id', 'startTime','endTime', 'totalHours','deductFrom', 'notes','isApproved','approver'
    ];
}
