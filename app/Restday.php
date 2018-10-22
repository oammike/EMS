<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class Restday extends Model
{
    protected $table = "restdays";
    protected $fillable = [
        'user_id', 'RD'
        
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    
    
}
