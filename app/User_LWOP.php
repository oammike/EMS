<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class User_LWOP extends Model
{
    protected $table = 'user_lwop';
    protected $fillable = [
        'leaveStart','leaveEnd', 'notes',
    ];
}
