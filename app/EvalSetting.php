<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class EvalSetting extends Model
{
    protected $table= 'evalSetting';

    protected $fillable = [
        'name','description', 'enableSelfEval','enableCoaching','startMonth','startDate','endMonth','endDate','evalType_id'
    ];

    public function competencyAttributes()
    {
    	return $this->belongsToMany(Competency__Attribute::class,'competency__Att__evalSetting','evalSetting_id','competency__Attribute_id');
    }
}
