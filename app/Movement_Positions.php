<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class Movement_Positions extends Model
{
    protected $table= 'movement_positions';

    protected $fillable = [
        'movement_id', 'position_id_old', 'position_id_new',
    ]; //'oldHead_id','newHead_id',
}
