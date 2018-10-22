<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class User_Resource extends Model
{
    protected $table='user_resource';

    protected $fillable = [
        'user_id','resource_id','agreed'
    ];


    
}
