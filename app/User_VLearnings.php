<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class User_VLearnings extends Model
{
    protected $table = 'user_vlearnings';
    protected $fillable = [
        'user_id','vlupdate_id'
    ];
}
