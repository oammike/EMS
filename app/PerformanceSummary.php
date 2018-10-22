<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class PerformanceSummary extends Model
{
    protected $table= 'performanceSummary';

    protected $fillable = [
        'value','evalForm_id','summary_id'
    ];
}
