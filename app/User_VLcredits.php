<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class User_VLcredits extends Model
{
   protected $table = 'user_vlcredits';
    protected $fillable = [
        'beginBalance','used','paid', 'creditYear', 'user_id',
    ];
}
