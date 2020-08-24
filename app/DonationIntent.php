<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class DonationIntent extends Model
{
    protected $table = 'donation_intents';    
    
    public function donation()
    {
        return $this->hasOne(Donation::class,'id','donation_id');
    }

    public function user()
    {
        return $this->hasOne(User::class,'id','user_id');
    }
}
