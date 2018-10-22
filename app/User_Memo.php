<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class User_Memo extends Model
{
    protected $table = 'user_memos';
    protected $fillable = [
        'user_id','memo_id'
    ];
}
