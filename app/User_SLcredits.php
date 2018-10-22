<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class User_SLcredits extends Model
{
    protected $table = 'user_slcredits';
    protected $fillable = [
        'beginBalance','used','paid', 'creditYear', 'user_id',
    ];
}
