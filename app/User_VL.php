<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class User_VL extends Model
{
    
    protected $table = 'user_vl';
    protected $fillable = [
        'leaveStart','leaveEnd', 'notes','isApproved','approver'
    ];


    
}
