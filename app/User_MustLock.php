<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class User_MustLock extends Model
{
    protected $table = 'user_mustLock';
    protected $fillable = [
        'user_id','productionDate'
    ];
}
