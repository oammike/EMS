<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class UserForm_Access extends Model
{
     protected $table = 'user_formAccess';
    protected $fillable = [
        'accessedBy','user_formID'
    ];
}
