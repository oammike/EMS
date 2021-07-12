<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class Reward_Alloc extends Model
{
    protected $table = 'reward_alloc';
    protected $fillable = [
        'user_creditor','campaign_id','points'
        
    ];
}
