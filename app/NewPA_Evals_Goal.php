<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class NewPA_Evals_Goal extends Model
{
    protected $table= 'newPA_evals_goal';
    protected $fillable = [
        'eval_id','rating','notes'
    ];
}
