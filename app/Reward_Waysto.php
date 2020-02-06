<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class Reward_Waysto extends Model
{
    protected $table = 'reward_waysto';
    protected $fillable = [
        'name','description','allowed_points', 'automatic'
        
    ];
}
