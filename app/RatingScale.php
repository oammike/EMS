<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class RatingScale extends Model
{
    protected $table= 'ratingScale';

    protected $fillable = [
        'label','status','description', 'percentage','increase','icon', 'maxRange', 'letterGrade'
    ];
}
