<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class User_DTRP extends Model
{
    protected $table = "user_dtrp";
    protected $fillable = [
         'user_id', 'biometrics_id', 'logTime','logType_id','notes', 'isApproved','approvedBy'
    ];
}
