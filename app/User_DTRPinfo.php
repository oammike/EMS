<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class User_DTRPinfo extends Model
{
    protected $table = "user_dtrpInfo";
    protected $fillable = [
         'dtrp_id', 'reasonID', 'attachments','isCleared','clearedBy'
    ];
}
