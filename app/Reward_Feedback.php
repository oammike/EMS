<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class Reward_Feedback extends Model
{
    protected $table = 'reward_feedback';
    protected $fillable = [
        'notes', 'user_id'
        
    ];
}
