<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class Reward_Award extends Model
{
    protected $table = 'reward_award';
    protected $fillable = [
        'suer_id','waysto_id','beginningBal', 'points','notes', 'awardedBy'
        
    ];
}
