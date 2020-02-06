<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class Reward_Creditor extends Model
{
    protected $table = 'reward_creditor';
    protected $fillable = [
        'waysto_id', 'user_id'
        
    ];
}
