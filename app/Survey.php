<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    protected $table='surveys';

    protected $fillable = [
        'user_id','name', 'description', 'type','startDate','endDate'
    ];
}
