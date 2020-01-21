<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class NewPA_Evals_Competencies extends Model
{
    protected $table= 'newPA_evals_competencies';
    protected $fillable = [
        'eval_id','compentecy_id', 'rating','strengths','afi', 'notes'
    ];
}
