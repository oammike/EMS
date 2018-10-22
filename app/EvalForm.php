<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class EvalForm extends Model
{
    protected $table= 'evalForm';

    protected $fillable = [
        'coachingDone','coachingTImestamp','overAllScore','salaryIncrease', 'startPeriod','endPeriod', 'evalSetting_id','users_id', 'evaluatedBy','isDraft'
    ];

    public function summaries()
    {
    	return $this->belongsToMany(Summary::class,'performanceSummary','evalForm_id', 'summary_id');
    }

    public function details()
    {
    	return $this->hasMany(EvalDetail::class,'evalForm_id');
    }

    public function setting()
    {
        return $this->belongsTo(EvalSetting::class,'evalSetting_id');
    }

    public function owner()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    


}
