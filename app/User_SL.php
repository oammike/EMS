<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class User_SL extends Model
{
    protected $table = 'user_sl';
    protected $fillable = [
        'leaveStart','leaveEnd', 'notes','attachments'
    ];
}
