<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class VoucherClaims extends Model
{
    protected $table = 'voucher_claims';    
    
    public function voucher()
    {
        return $this->hasOne(Voucher::class,'id','voucher_id');
    }

    public function user()
    {
        return $this->hasOne(User::class,'id','user_id');
    }
}
