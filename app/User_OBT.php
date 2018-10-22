<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class User_OBT extends Model
{
   protected $table = 'user_obt';
   protected $fillable = [
        'leaveStart','leaveEnd', 'notes',
    ];
}
