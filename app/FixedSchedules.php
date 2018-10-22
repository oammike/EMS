<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class FixedSchedules extends Model
{
    protected $table= 'fixed_schedules';

    protected $fillable = [
        'user_id', 'workday','timeStart','timeEnd','isFlexitime', 'isRD'
    ];


}
