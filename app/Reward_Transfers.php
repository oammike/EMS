<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class Reward_Transfers extends Model
{
    protected $table = 'reward_transfers';
    protected $fillable = [
        'from_user', 'to_user','beginningBal','transferedPoints','notes'
        
    ];
    
}
