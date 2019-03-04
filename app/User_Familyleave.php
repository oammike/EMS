<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class User_Familyleave extends Model
{
    protected $table = 'user_familyleaves';
    protected $fillable = [
        'leaveType', 'leaveStart','leaveEnd', 'notes','attachments'
    ];
}
