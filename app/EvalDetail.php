<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class EvalDetail extends Model
{
    protected $table= 'evalDetail';

    protected $fillable = [
        'evalForm_id','competency__Attribute_id','ratingScale_id','attributeValue'
    ];

     public function ratings()
    {
        return $this->belongsTo(RatingScale::class,'ratingScale_id');
    }
}
