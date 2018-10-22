<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
   protected $table='holidays';

    protected $fillable = [
        'name','holidate','holidayType_id'
    ];

}
