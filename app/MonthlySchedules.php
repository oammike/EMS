<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class MonthlySchedules extends Model
{
    protected $table= 'monthly_schedules';

    protected $fillable = [
        'user_id', 'productionDate','timeStart','timeEnd','isFlexitime','isRD'
    ];
}
