<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class Reward_MonthlyAlloc extends Model
{
    protected $table = 'reward_monthlyAlloc';
    protected $fillable = [
        'points','alloc_id'
        
    ];
}
