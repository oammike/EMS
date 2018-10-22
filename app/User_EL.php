<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class User_EL extends Model
{
    protected $table = 'user_el';
    protected $fillable = [
        'leaveStart','leaveEnd', 'notes','deductFrom'
    ];
}
