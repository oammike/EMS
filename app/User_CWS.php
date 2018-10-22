<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class User_CWS extends Model
{
	protected $table = "user_cws";
    protected $fillable = [
        'user_id', 'biometrics_id', 'timeStart','timeEnd', 'notes', 'timeStart_old','timeEnd_old', 'isRD','isApproved','approver'
        
    ];
    

}
