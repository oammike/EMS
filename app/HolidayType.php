<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class HolidayType extends Model
{
   protected $table='holiday_types';

    protected $fillable = [
        'name',
    ];

}
