<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class RewardExclusiveClaim extends Model
{
    protected $table = 'rewards_exclusives_claims';

    public function reward()
    {
        return $this->hasOne(RewardExclusive::class,'id','exclusive_id');
    }

    public function approver()
    {
        return $this->hasOne(User::class,'id','approver_id');
    }

    public function user()
    {
        return $this->hasOne(User::class,'id','user_id');
    }
}
