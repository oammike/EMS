<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class UserForm_DisqFiling extends Model
{
   	protected $table = 'user_formDisqFiling';
    protected $fillable = [
        'user_id','reasonID'
    ];
}
