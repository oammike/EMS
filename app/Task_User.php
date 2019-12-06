<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class Task_User extends Model
{
    protected $table='task_user';

    protected $fillable = [
        'id','user_id','task_id','timeStart','timeEnd'
    ];
}
