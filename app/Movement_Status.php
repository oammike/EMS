<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class Movement_Status extends Model
{
    protected $table= 'movement_statuses';

    protected $fillable = [
        'movement_id', 'status_id_old', 'status_id_new',
    ]; //'oldHead_id','newHead_id',
}
