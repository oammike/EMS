<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class NewPA_Evals_Professionalism extends Model
{
    protected $table= 'newPA_evals_professionalism';
    protected $fillable = [
        'eval_id','rating','notes'
    ];
}
