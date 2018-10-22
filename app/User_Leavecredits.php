<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class User_Leavecredits extends Model
{
    protected $table = 'user_leavecredits';
    protected $fillable = [
        'balance','creditYear', 'user_id',
    ];
}
