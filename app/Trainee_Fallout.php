<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class Trainee_Fallout extends Model
{
    protected $table = "trainee_fallout";
    protected $fillable = [
        'user_id', 'movement_id','reason'
        
    ];
}
